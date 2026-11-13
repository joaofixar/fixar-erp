<?php

declare(strict_types=1);

namespace RestApi\Service;

use RestApi\Core\ApiException;
use RestApi\Database\SchemaRepository;
use RestApi\Security\AccessPolicyService;
use RestApi\Security\Blocklist;
use SplQueue;

class JoinPlanner
{
    private SchemaRepository $schemaRepository;
    private AccessPolicyService $accessPolicyService;
    private Blocklist $blocklist;

    /** @var array<string, array<string, string>> */
    private array $tableIndexCache = [];

    /** @var array<string, array<string, mixed>> */
    private array $graphCache = [];

    /** @var array<string, array<string, mixed>> */
    private array $schemaCache = [];

    public function __construct(
        SchemaRepository $schemaRepository,
        AccessPolicyService $accessPolicyService,
        Blocklist $blocklist
    )
    {
        $this->schemaRepository = $schemaRepository;
        $this->accessPolicyService = $accessPolicyService;
        $this->blocklist = $blocklist;
    }

    /**
     * Encontra o menor caminho entre a tabela base e a tabela destino.
     *
     * O grafo é montado em memória a partir do schema do banco. Cada relacionamento
     * físico é inferido exclusivamente pela convenção SOURCE_TARGET_Id e pode ser
     * percorrido nos dois sentidos durante a busca.
     *
     * Quando existem múltiplos menores caminhos possíveis, o planner seleciona
     * deterministicamente o primeiro caminho mínimo encontrado. A ordenação das
     * arestas prioriza relacionamentos originados pela tabela atual (forward)
     * antes de relacionamentos percorridos no sentido reverso.
     *
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function findPath(
        string $baseTable,
        string $targetTable,
        $connection,
        array $tenantContext
    ): array
    {
        $normalizedBaseTable = $this->normalizeTableName($baseTable);
        $normalizedTargetTable = $this->normalizeTableName($targetTable);

        if ($normalizedBaseTable === '') {
            throw JoinPlanningException::baseTableRequired();
        }

        if ($normalizedTargetTable === '') {
            throw JoinPlanningException::targetTableRequired();
        }

        $graph = $this->getRelationshipGraph($connection, $tenantContext);

        if (!isset($graph['tables'][$normalizedBaseTable])) {
            throw JoinPlanningException::tableNotFound($normalizedBaseTable);
        }

        if (!isset($graph['tables'][$normalizedTargetTable])) {
            throw JoinPlanningException::tableNotFound($normalizedTargetTable);
        }

        if ($normalizedBaseTable === $normalizedTargetTable) {
            return [
                'base_table' => $normalizedBaseTable,
                'target_table' => $normalizedTargetTable,
                'distance' => 0,
                'tables' => [$normalizedBaseTable],
                'joins' => [],
            ];
        }

        $searchResult = $this->searchShortestPath(
            (array) ($graph['adjacency'] ?? []),
            $normalizedBaseTable,
            $normalizedTargetTable
        );

        if (($searchResult['found'] ?? false) !== true) {
            throw JoinPlanningException::pathNotFound($normalizedBaseTable, $normalizedTargetTable);
        }

        $distance = isset($searchResult['distance']) ? (int) $searchResult['distance'] : 0;

        $joins = $this->reconstructPath(
            (array) ($searchResult['parents'] ?? []),
            $normalizedBaseTable,
            $normalizedTargetTable
        );

        return [
            'base_table' => $normalizedBaseTable,
            'target_table' => $normalizedTargetTable,
            'distance' => $distance,
            'tables' => $this->extractTablesFromJoins($normalizedBaseTable, $joins),
            'joins' => $joins,
        ];
    }

    /**
     * Monta um plano consolidado para múltiplas tabelas destino.
     *
     * Cada destino é resolvido individualmente com BFS, e os joins compartilhados
     * são deduplicados na saída final preservando a ordem da primeira ocorrência.
     *
     * @param array<int, string> $targetTables
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function buildPlan(
        string $baseTable,
        array $targetTables,
        $connection,
        array $tenantContext
    ): array
    {
        $normalizedBaseTable = $this->normalizeTableName($baseTable);

        if ($normalizedBaseTable === '') {
            throw JoinPlanningException::baseTableRequired();
        }

        $normalizedTargetTables = $this->normalizeTargetTables($targetTables);
        if ($normalizedTargetTables === []) {
            throw JoinPlanningException::targetTablesRequired();
        }

        $paths = [];
        $joins = [];
        $joinKeys = [];
        $tableSet = [$normalizedBaseTable => true];

        foreach ($normalizedTargetTables as $targetTable) {
            $path = $this->findPath($normalizedBaseTable, $targetTable, $connection, $tenantContext);
            $paths[$targetTable] = $path;

            foreach ((array) ($path['tables'] ?? []) as $table) {
                if (is_string($table) && $table !== '') {
                    $tableSet[$table] = true;
                }
            }

            foreach ((array) ($path['joins'] ?? []) as $join) {
                if (!is_array($join)) {
                    continue;
                }

                $joinKey = $this->buildJoinKey($join);
                if ($joinKey === '' || isset($joinKeys[$joinKey])) {
                    continue;
                }

                $joinKeys[$joinKey] = true;
                $joins[] = $join;
            }
        }

        return [
            'base_table' => $normalizedBaseTable,
            'target_tables' => $normalizedTargetTables,
            'tables' => array_keys($tableSet),
            'paths' => $paths,
            'joins' => $joins,
        ];
    }

    /**
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    private function getRelationshipGraph($connection, array $tenantContext): array
    {
        $databaseKey = $this->getDatabaseKey($tenantContext);

        if (isset($this->graphCache[$databaseKey])) {
            return $this->graphCache[$databaseKey];
        }

        $tableIndex = $this->getTableIndex($connection, $tenantContext);
        $schemas = [];
        $fieldSets = [];

        foreach ($tableIndex as $normalizedTable => $actualTable) {
            $schema = $this->getSchema($connection, $tenantContext, $normalizedTable, $actualTable);
            $schemas[$normalizedTable] = $schema;
            $fieldSets[$normalizedTable] = $this->buildFieldSet($normalizedTable, $schema);
        }

        $adjacency = [];
        foreach (array_keys($tableIndex) as $table) {
            $adjacency[$table] = [];
        }

        foreach ($schemas as $normalizedTable => $schema) {
            $relationships = $this->inferOutgoingRelationships($normalizedTable, $schema, $tableIndex, $fieldSets);
            foreach ($relationships as $relationship) {
                $this->appendRelationship($adjacency, $relationship);
            }
        }

        foreach ($adjacency as &$edges) {
            usort($edges, static function (array $left, array $right): int {
                $comparison = strcmp((string) ($left['to_table'] ?? ''), (string) ($right['to_table'] ?? ''));
                if ($comparison !== 0) {
                    return $comparison;
                }

                $directionPriority = [
                    'forward' => 0,
                    'reverse' => 1,
                ];
                $leftDirectionPriority = $directionPriority[(string) ($left['direction'] ?? '')] ?? 99;
                $rightDirectionPriority = $directionPriority[(string) ($right['direction'] ?? '')] ?? 99;
                $comparison = $leftDirectionPriority <=> $rightDirectionPriority;
                if ($comparison !== 0) {
                    return $comparison;
                }

                return strcmp((string) ($left['source_column'] ?? ''), (string) ($right['source_column'] ?? ''));
            });
        }
        unset($edges);

        $graph = [
            'database' => $databaseKey,
            'tables' => $tableIndex,
            'adjacency' => $adjacency,
        ];

        $this->graphCache[$databaseKey] = $graph;

        return $graph;
    }

    /**
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, string>
     */
    private function getTableIndex($connection, array $tenantContext): array
    {
        $databaseKey = $this->getDatabaseKey($tenantContext);

        if (isset($this->tableIndexCache[$databaseKey])) {
            return $this->tableIndexCache[$databaseKey];
        }

        $tables = $this->schemaRepository->listTables($connection, $tenantContext);
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        $tableIndex = [];

        foreach ($tables as $table) {
            if (!is_string($table)) {
                continue;
            }

            $normalizedTable = $this->normalizeTableName($table);
            if ($normalizedTable === '') {
                continue;
            }

            try {
                $this->blocklist->assertTableOperationAllowed($normalizedTable, 'read');
            } catch (ApiException $exception) {
                continue;
            }

            if (!$accessPolicy->isTableAllowed($normalizedTable)) {
                continue;
            }

            $tableIndex[$normalizedTable] = $table;
        }

        ksort($tableIndex);
        $this->tableIndexCache[$databaseKey] = $tableIndex;

        return $tableIndex;
    }

    /**
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    private function getSchema(
        $connection,
        array $tenantContext,
        string $normalizedTable,
        string $actualTable
    ): array
    {
        $databaseKey = $this->getDatabaseKey($tenantContext);
        $cacheKey = $databaseKey . '::' . $normalizedTable;

        if (isset($this->schemaCache[$cacheKey])) {
            return $this->schemaCache[$cacheKey];
        }

        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $actualTable);
        $this->schemaCache[$cacheKey] = $schema;

        return $schema;
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, string>
     */
    private function buildFieldSet(string $table, array $schema): array
    {
        if (!isset($schema['fields']) || !is_array($schema['fields'])) {
            throw JoinPlanningException::invalidSchema($table);
        }

        $fieldSet = [];
        foreach ($schema['fields'] as $field) {
            if (!is_array($field) || !isset($field['name'])) {
                throw JoinPlanningException::invalidSchema($table);
            }

            $fieldName = trim((string) $field['name']);
            if ($fieldName === '') {
                throw JoinPlanningException::invalidSchema($table);
            }

            $fieldSet[strtoupper($fieldName)] = $fieldName;
        }

        return $fieldSet;
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<string, string> $tableIndex
     * @param array<string, array<string, string>> $fieldSets
     * @return array<int, array<string, string>>
     */
    private function inferOutgoingRelationships(
        string $sourceTable,
        array $schema,
        array $tableIndex,
        array $fieldSets
    ): array
    {
        if (!isset($schema['fields']) || !is_array($schema['fields'])) {
            throw JoinPlanningException::invalidSchema($sourceTable);
        }

        $relationships = [];
        $prefix = $sourceTable . '_';

        foreach ($schema['fields'] as $field) {
            if (!is_array($field) || !isset($field['name'])) {
                throw JoinPlanningException::invalidSchema($sourceTable);
            }

            $fieldName = trim((string) $field['name']);
            if ($fieldName === '') {
                continue;
            }

            $fieldNameUpper = strtoupper($fieldName);
            if (strpos($fieldNameUpper, $prefix) !== 0) {
                continue;
            }

            $tail = substr($fieldNameUpper, strlen($prefix));
            if (!preg_match('/^([A-Z0-9]+)_ID$/', $tail, $matches)) {
                continue;
            }

            $targetTable = $matches[1];
            if (!isset($tableIndex[$targetTable])) {
                continue;
            }

            $targetColumnKey = $targetTable . '_ID';
            if (!isset($fieldSets[$targetTable][$targetColumnKey])) {
                continue;
            }

            $relationships[] = [
                'source_table' => $sourceTable,
                'source_column' => $fieldName,
                'target_table' => $targetTable,
                'target_column' => $fieldSets[$targetTable][$targetColumnKey],
            ];
        }

        return $relationships;
    }

    /**
     * @param array<string, array<int, array<string, string>>> $adjacency
     * @param array<string, string> $relationship
     */
    private function appendRelationship(array &$adjacency, array $relationship): void
    {
        $sourceTable = $relationship['source_table'];
        $targetTable = $relationship['target_table'];

        if (!isset($adjacency[$sourceTable])) {
            $adjacency[$sourceTable] = [];
        }

        if (!isset($adjacency[$targetTable])) {
            $adjacency[$targetTable] = [];
        }

        $adjacency[$sourceTable][] = [
            'from_table' => $sourceTable,
            'to_table' => $targetTable,
            'direction' => 'forward',
            'source_table' => $sourceTable,
            'source_column' => $relationship['source_column'],
            'target_table' => $targetTable,
            'target_column' => $relationship['target_column'],
        ];

        $adjacency[$targetTable][] = [
            'from_table' => $targetTable,
            'to_table' => $sourceTable,
            'direction' => 'reverse',
            'source_table' => $sourceTable,
            'source_column' => $relationship['source_column'],
            'target_table' => $targetTable,
            'target_column' => $relationship['target_column'],
        ];
    }

    /**
     * @param array<string, array<int, array<string, string>>> $adjacency
     * @return array<string, mixed>
     */
    private function searchShortestPath(array $adjacency, string $baseTable, string $targetTable): array
    {
        $queue = new SplQueue();
        $queue->enqueue($baseTable);

        $distances = [$baseTable => 0];
        $parents = [$baseTable => null];

        while (!$queue->isEmpty()) {
            /** @var string $currentTable */
            $currentTable = $queue->dequeue();
            $currentDistance = $distances[$currentTable];

            foreach ($adjacency[$currentTable] ?? [] as $edge) {
                $nextTable = (string) ($edge['to_table'] ?? '');
                if ($nextTable === '') {
                    continue;
                }

                $nextDistance = $currentDistance + 1;

                if (!array_key_exists($nextTable, $distances)) {
                    $distances[$nextTable] = $nextDistance;
                    $parents[$nextTable] = [
                        'table' => $currentTable,
                        'edge' => $edge,
                    ];
                    $queue->enqueue($nextTable);
                    continue;
                }

                // O primeiro menor caminho encontrado é preservado para manter
                // o planejamento determinístico e privilegiar as arestas já
                // ordenadas com prioridade forward > reverse.
            }
        }

        if (!array_key_exists($targetTable, $distances)) {
            return [
                'found' => false,
                'distance' => null,
                'parents' => $parents,
            ];
        }

        return [
            'found' => true,
            'distance' => $distances[$targetTable],
            'parents' => $parents,
        ];
    }

    /**
     * @param array<string, array<string, mixed>|null> $parents
     * @return array<int, array<string, string>>
     */
    private function reconstructPath(array $parents, string $baseTable, string $targetTable): array
    {
        $joins = [];
        $currentTable = $targetTable;

        while ($currentTable !== $baseTable) {
            if (!array_key_exists($currentTable, $parents) || !is_array($parents[$currentTable])) {
                throw JoinPlanningException::pathNotFound($baseTable, $targetTable);
            }

            $parent = $parents[$currentTable];
            if (!is_array($parent) || !isset($parent['table'], $parent['edge']) || !is_array($parent['edge'])) {
                throw JoinPlanningException::pathNotFound($baseTable, $targetTable);
            }

            $edge = $parent['edge'];
            $joins[] = [
                'from_table' => (string) ($edge['from_table'] ?? ''),
                'to_table' => (string) ($edge['to_table'] ?? ''),
                'join_type' => 'LEFT',
                'direction' => (string) ($edge['direction'] ?? ''),
                'source_table' => (string) ($edge['source_table'] ?? ''),
                'source_column' => (string) ($edge['source_column'] ?? ''),
                'target_table' => (string) ($edge['target_table'] ?? ''),
                'target_column' => (string) ($edge['target_column'] ?? ''),
            ];

            $currentTable = (string) $parent['table'];
        }

        return array_reverse($joins);
    }

    /**
     * @param array<int, array<string, string>> $joins
     * @return array<int, string>
     */
    private function extractTablesFromJoins(string $baseTable, array $joins): array
    {
        $tables = [$baseTable => true];

        foreach ($joins as $join) {
            $fromTable = (string) ($join['from_table'] ?? '');
            $toTable = (string) ($join['to_table'] ?? '');

            if ($fromTable !== '') {
                $tables[$fromTable] = true;
            }

            if ($toTable !== '') {
                $tables[$toTable] = true;
            }
        }

        return array_keys($tables);
    }

    /**
     * @param array<int, string> $targetTables
     * @return array<int, string>
     */
    private function normalizeTargetTables(array $targetTables): array
    {
        $normalizedTargetTables = [];
        $seen = [];

        foreach ($targetTables as $targetTable) {
            if (!is_string($targetTable)) {
                throw JoinPlanningException::invalidTargetTableValue($targetTable);
            }

            $normalizedTargetTable = $this->normalizeTableName($targetTable);
            if ($normalizedTargetTable === '') {
                throw JoinPlanningException::targetTableRequired();
            }

            if (isset($seen[$normalizedTargetTable])) {
                continue;
            }

            $seen[$normalizedTargetTable] = true;
            $normalizedTargetTables[] = $normalizedTargetTable;
        }

        return $normalizedTargetTables;
    }

    /**
     * @param array<string, string> $join
     */
    private function buildJoinKey(array $join): string
    {
        $parts = [
            (string) ($join['from_table'] ?? ''),
            (string) ($join['to_table'] ?? ''),
            (string) ($join['direction'] ?? ''),
            (string) ($join['source_table'] ?? ''),
            (string) ($join['source_column'] ?? ''),
            (string) ($join['target_table'] ?? ''),
            (string) ($join['target_column'] ?? ''),
        ];

        if (implode('', $parts) === '') {
            return '';
        }

        return implode('|', $parts);
    }

    /**
     * @param array<string, string> $tenantContext
     */
    private function getDatabaseKey(array $tenantContext): string
    {
        $database = trim((string) ($tenantContext['dbDatabase'] ?? ''));
        if ($database === '') {
            throw JoinPlanningException::tenantDatabaseRequired();
        }

        $host = trim((string) ($tenantContext['host'] ?? ''));

        return strtolower($host . '::' . $database);
    }

    private function normalizeTableName(string $table): string
    {
        return strtoupper(trim($table));
    }
}
