<?php

declare(strict_types=1);

namespace RestApi\Service;

use PDO;
use PDOException;
use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Database\QueryBuilder;
use RestApi\Database\SchemaRepository;
use RestApi\Security\AccessPolicyService;
use RestApi\Security\Blocklist;
use RestApi\Security\FieldPolicy;

class TableReadService
{
    private SchemaRepository $schemaRepository;
    private QueryBuilder $queryBuilder;
    private Blocklist $blocklist;
    private AccessPolicyService $accessPolicyService;
    private FieldPolicy $fieldPolicy;
    private QueryValidator $queryValidator;
    private JoinPlanner $joinPlanner;

    public function __construct(
        SchemaRepository $schemaRepository,
        QueryBuilder $queryBuilder,
        Blocklist $blocklist,
        AccessPolicyService $accessPolicyService,
        FieldPolicy $fieldPolicy,
        QueryValidator $queryValidator,
        JoinPlanner $joinPlanner
    )
    {
        $this->schemaRepository = $schemaRepository;
        $this->queryBuilder = $queryBuilder;
        $this->blocklist = $blocklist;
        $this->accessPolicyService = $accessPolicyService;
        $this->fieldPolicy = $fieldPolicy;
        $this->queryValidator = $queryValidator;
        $this->joinPlanner = $joinPlanner;
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<int, array<string, string>>
     */
    public function listTables($connection, array $tenantContext): array
    {
        $tables = $this->schemaRepository->listTables($connection, $tenantContext);
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        $visibleTables = [];

        foreach ($tables as $table) {
            if ($this->blocklist->isTableBlocked($table)) {
                continue;
            }

            if (!$accessPolicy->isTableAllowed($table)) {
                continue;
            }

            $visibleTables[] = [
                'name' => $table,
            ];
        }

        return $visibleTables;
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function getTableSchema($connection, array $tenantContext, string $table): array
    {
        $this->blocklist->assertTableOperationAllowed($table, 'schema');
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        if (!$accessPolicy->isTableAllowed($table)) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $table);
        $allowedFields = $accessPolicy->getReadableFields(
            $table,
            (array) ($schema['fields'] ?? []),
            is_string($schema['primary_key'] ?? null) ? (string) $schema['primary_key'] : null
        );
        $visibleFields = $this->fieldPolicy->filterSchemaFields((array) ($schema['fields'] ?? []), $allowedFields);

        if ($visibleFields === []) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        return [
            'table' => $schema['table'],
            'database' => $schema['database'],
            'primary_key' => $schema['primary_key'],
            'fields' => $visibleFields,
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function getRecords($connection, array $tenantContext, string $table, $request): array
    {
        $this->blocklist->assertTableOperationAllowed($table, 'read');
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        if (!$accessPolicy->isTableAllowed($table)) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        if (!$request instanceof Request) {
            throw new ApiException('INVALID_REQUEST', 'Requisição inválida.', 500);
        }

        $normalizedBaseTable = strtoupper(trim($table));
        $manualJoinOverrides = $this->extractManualJoinOverrides($request, $normalizedBaseTable);
        $availableTables = $this->buildAvailableTableSet($connection, $tenantContext);
        $this->assertExplicitRequestedTableAccess(
            $accessPolicy,
            $normalizedBaseTable,
            $request,
            $manualJoinOverrides,
            $availableTables
        );
        if (!$this->queryRequiresJoin($request, $normalizedBaseTable, $manualJoinOverrides)) {
            $baseReadContext = $this->buildBaseTableReadContext(
                $connection,
                $tenantContext,
                $accessPolicy,
                $normalizedBaseTable
            );
            $validatedQuery = $this->queryValidator->validateListQuery(
                $request,
                $baseReadContext['schema'],
                $baseReadContext['allowed_fields']
            );
            $query = $this->queryBuilder->buildSelect(
                $baseReadContext['join_plan'],
                $validatedQuery['fields'],
                $validatedQuery['filters'],
                $validatedQuery['sorts'],
                $validatedQuery['limit'],
                $validatedQuery['offset']
            );

            return $this->executeReadQuery($connection, $query, $validatedQuery);
        }

        $preliminaryJoinPlan = $this->buildJoinPlanForRead(
            $connection,
            $tenantContext,
            $normalizedBaseTable,
            $request,
            $manualJoinOverrides,
            $availableTables
        );
        $aggregatedReadContext = $this->buildAggregatedReadContext(
            $connection,
            $tenantContext,
            $accessPolicy,
            $normalizedBaseTable,
            $preliminaryJoinPlan
        );
        $validatedManualJoins = $this->queryValidator->validateJoinOverrides(
            (array) ($preliminaryJoinPlan['manual_overrides'] ?? []),
            $aggregatedReadContext['schema'],
            $aggregatedReadContext['allowed_fields']
        );
        $finalJoinPlan = $this->applyManualJoinOverridesToPlan(
            $aggregatedReadContext['join_plan'],
            $validatedManualJoins
        );
        $aggregatedReadContext['join_plan'] = $finalJoinPlan;
        $aggregatedReadContext['schema']['join_plan'] = $finalJoinPlan;

        $validatedQuery = $this->queryValidator->validateListQuery(
            $request,
            $aggregatedReadContext['schema'],
            $aggregatedReadContext['allowed_fields']
        );
        if (!$this->hasExplicitFields($request)) {
            $validatedQuery['fields'] = $aggregatedReadContext['base_visible_field_names'];
        }
        $query = $this->queryBuilder->buildSelect(
            $aggregatedReadContext['join_plan'],
            $validatedQuery['fields'],
            $validatedQuery['filters'],
            $validatedQuery['sorts'],
            $validatedQuery['limit'],
            $validatedQuery['offset']
        );

        return $this->executeReadQuery($connection, $query, $validatedQuery);
    }

    /**
     * @param PDO|object $connection
     * @param array{sql:string, params:array<string, mixed>} $query
     * @param array<string, mixed> $validatedQuery
     * @return array<string, mixed>
     */
    private function executeReadQuery($connection, array $query, array $validatedQuery): array
    {
        try {
            $statement = $connection->prepare($query['sql']);
            foreach ($query['params'] as $name => $value) {
                $parameterType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $statement->bindValue(':' . $name, $value, $parameterType);
            }
            $statement->execute();
            $rows = $statement->fetchAll();
        } catch (PDOException $exception) {
            throw new ApiException('READ_ERROR', 'Não foi possível consultar os registros.', 500);
        }

        return [
            'rows' => is_array($rows) ? $rows : [],
            'pagination' => [
                'limit' => $validatedQuery['limit'],
                'offset' => $validatedQuery['offset'],
                'returned' => is_array($rows) ? count($rows) : 0,
            ],
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    private function buildBaseTableReadContext(
        $connection,
        array $tenantContext,
        AccessPolicyService $accessPolicy,
        string $baseTable
    ): array
    {
        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $baseTable);
        $schemaFields = $this->attachTableToFields((array) ($schema['fields'] ?? []), $baseTable);
        $allowedFields = $accessPolicy->getReadableFields(
            $baseTable,
            $schemaFields,
            is_string($schema['primary_key'] ?? null) ? (string) $schema['primary_key'] : null
        );
        $visibleFields = $this->fieldPolicy->filterSchemaFields($schemaFields, $allowedFields);

        if ($visibleFields === []) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $baseTable,
            ]);
        }

        return [
            'schema' => [
                'table' => $schema['table'],
                'database' => $schema['database'],
                'primary_key' => $schema['primary_key'],
                'fields' => $schemaFields,
            ],
            'allowed_fields' => $allowedFields,
            'join_plan' => [
                'base_table' => $baseTable,
                'tables' => [$baseTable],
                'joins' => [],
                'field_sources' => [],
            ],
            'base_visible_field_names' => array_values(array_filter(array_map(static function ($field): string {
                return is_array($field) && isset($field['name']) ? trim((string) $field['name']) : '';
            }, $visibleFields), static function (string $fieldName): bool {
                return $fieldName !== '';
            })),
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function getRecordByPrimaryKey($connection, array $tenantContext, string $table, string $recordId): array
    {
        $this->blocklist->assertTableOperationAllowed($table, 'read');
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        if (!$accessPolicy->isTableAllowed($table)) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $table);
        $validatedId = $this->queryValidator->validatePrimaryKeyLookup($schema, $recordId);
        $allowedFields = $accessPolicy->getReadableFields(
            $table,
            (array) ($schema['fields'] ?? []),
            is_string($schema['primary_key'] ?? null) ? (string) $schema['primary_key'] : null
        );
        $visibleFields = $this->fieldPolicy->filterSchemaFields((array) ($schema['fields'] ?? []), $allowedFields);

        if ($visibleFields === []) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        $fields = array_column($visibleFields, 'name');

        $query = $this->queryBuilder->buildSelectByPrimaryKey(
            $schema['table'],
            $fields,
            (string) $schema['primary_key'],
            $validatedId
        );

        try {
            $statement = $connection->prepare($query['sql']);
            foreach ($query['params'] as $name => $value) {
                $parameterType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $statement->bindValue(':' . $name, $value, $parameterType);
            }
            $statement->execute();
            $row = $statement->fetch();
        } catch (PDOException $exception) {
            throw new ApiException('READ_ERROR', 'Não foi possível consultar o registro.', 500);
        }

        if (!is_array($row) || $row === []) {
            throw new ApiException('RECORD_NOT_FOUND', 'Registro não encontrado.', 404, [
                'table' => $table,
                'id' => $recordId,
            ]);
        }

        return $row;
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    private function buildJoinPlanForRead(
        $connection,
        array $tenantContext,
        string $baseTable,
        Request $request,
        array $manualJoinOverrides = [],
        array $availableTables = []
    ): array
    {
        $requestedFields = $this->collectRequestedFieldNames($request, $manualJoinOverrides);
        $requestedTables = $this->collectRequestedTables($baseTable, $requestedFields, $availableTables);
        $manualJoinTables = $this->collectManualJoinTables($manualJoinOverrides);
        $automaticRequestedTables = [];

        foreach ($requestedTables as $requestedTable) {
            if ($requestedTable !== $baseTable && isset($manualJoinTables[$requestedTable])) {
                continue;
            }

            $automaticRequestedTables[] = $requestedTable;
        }

        $joinPlan = $this->joinPlanner->buildPlan($baseTable, $automaticRequestedTables, $connection, $tenantContext);
        $plannedTables = [];
        foreach ((array) ($joinPlan['tables'] ?? []) as $plannedTable) {
            if (!is_string($plannedTable) || trim($plannedTable) === '') {
                continue;
            }

            $plannedTables[strtoupper(trim($plannedTable))] = true;
        }

        $plannedTables[$baseTable] = true;
        foreach (array_keys($manualJoinTables) as $manualJoinTable) {
            $plannedTables[$manualJoinTable] = true;
        }

        $joinPlan['tables'] = array_keys($plannedTables);
        $joinPlan['manual_overrides'] = $manualJoinOverrides;

        return $joinPlan;
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @param array<string, mixed> $joinPlan
     * @return array<string, mixed>
     */
    private function buildAggregatedReadContext(
        $connection,
        array $tenantContext,
        AccessPolicyService $accessPolicy,
        string $baseTable,
        array $joinPlan
    ): array
    {
        $plannedTables = [];
        foreach ((array) ($joinPlan['tables'] ?? []) as $plannedTable) {
            if (!is_string($plannedTable)) {
                continue;
            }

            $normalizedTable = strtoupper(trim($plannedTable));
            if ($normalizedTable === '') {
                continue;
            }

            $plannedTables[] = $normalizedTable;
        }

        if ($plannedTables === []) {
            $plannedTables[] = $baseTable;
        }

        if (!in_array($baseTable, $plannedTables, true)) {
            array_unshift($plannedTables, $baseTable);
        }

        $aggregatedFields = [];
        $aggregatedAllowedFields = [];
        $fieldTables = [];
        $fieldSources = [];
        $tablesContext = [];
        $database = (string) ($tenantContext['dbDatabase'] ?? '');
        $basePrimaryKey = null;

        foreach ($plannedTables as $plannedTable) {
            $this->blocklist->assertTableOperationAllowed($plannedTable, 'read');

            if (!$accessPolicy->isTableAllowed($plannedTable)) {
                throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                    'table' => $plannedTable,
                ]);
            }

            $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $plannedTable);
            $schemaFields = $this->attachTableToFields((array) ($schema['fields'] ?? []), $plannedTable);
            $allowedFields = $accessPolicy->getReadableFields(
                $plannedTable,
                $schemaFields,
                is_string($schema['primary_key'] ?? null) ? (string) $schema['primary_key'] : null
            );
            $visibleFields = $this->fieldPolicy->filterSchemaFields($schemaFields, $allowedFields);

            if ($visibleFields === []) {
                throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                    'table' => $plannedTable,
                ]);
            }

            if ($plannedTable === $baseTable) {
                $basePrimaryKey = is_string($schema['primary_key'] ?? null) ? (string) $schema['primary_key'] : null;
                if ($database === '') {
                    $database = (string) ($schema['database'] ?? '');
                }
            }

            $tablesContext[$plannedTable] = [
                'schema' => $schema,
                'schema_fields' => $schemaFields,
                'allowed_fields' => $allowedFields,
                'visible_fields' => $visibleFields,
            ];

            foreach ($schemaFields as $field) {
                if (!is_array($field) || !isset($field['name'])) {
                    continue;
                }

                $fieldName = trim((string) $field['name']);
                if ($fieldName === '') {
                    continue;
                }

                $fieldTables[$fieldName] = $plannedTable;
                $fieldSources[$fieldName] = [
                    'table' => $plannedTable,
                    'column' => $fieldName,
                ];

                if (!isset($aggregatedFields[$fieldName])) {
                    $fieldWithTable = $field;
                    $fieldWithTable['table'] = $plannedTable;
                    $aggregatedFields[$fieldName] = $fieldWithTable;
                }

                $aggregatedAllowedFields[$fieldName] = true;
            }
        }

        $aggregatedJoinPlan = $joinPlan;
        $aggregatedJoinPlan['field_sources'] = $fieldSources;

        return [
            'schema' => [
                'table' => $baseTable,
                'database' => $database,
                'primary_key' => $basePrimaryKey,
                'fields' => array_values($aggregatedFields),
                'join_plan' => $aggregatedJoinPlan,
                'field_tables' => $fieldTables,
                'tables' => array_keys($tablesContext),
            ],
            'allowed_fields' => array_keys($aggregatedAllowedFields),
            'base_visible_field_names' => isset($tablesContext[$baseTable]['visible_fields'])
                ? array_values(array_filter(array_map(static function ($field): string {
                    return is_array($field) && isset($field['name']) ? trim((string) $field['name']) : '';
                }, (array) $tablesContext[$baseTable]['visible_fields']), static function (string $fieldName): bool {
                    return $fieldName !== '';
                }))
                : [],
            'field_tables' => $fieldTables,
            'join_plan' => $aggregatedJoinPlan,
            'tables' => $tablesContext,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function collectRequestedFieldNames(Request $request, array $manualJoinOverrides = []): array
    {
        $queryParams = $request->getQueryParams();
        $fieldNames = [];

        foreach ($this->splitCommaSeparatedValues($queryParams['fields'] ?? null) as $fieldName) {
            $fieldNames[$fieldName] = true;
        }

        foreach ($this->splitCommaSeparatedValues($queryParams['sort'] ?? null) as $sortField) {
            if (strpos($sortField, '-') === 0) {
                $sortField = substr($sortField, 1);
            }

            $sortField = trim($sortField);
            if ($sortField === '') {
                continue;
            }

            $fieldNames[$sortField] = true;
        }

        $filters = $queryParams['filter'] ?? [];
        if (is_array($filters)) {
            foreach ($filters as $fieldName => $operatorMap) {
                if (!is_string($fieldName)) {
                    continue;
                }

                $normalizedFieldName = trim($fieldName);
                if ($normalizedFieldName === '') {
                    continue;
                }

                $fieldNames[$normalizedFieldName] = true;
            }
        }

        foreach ($manualJoinOverrides as $manualJoin) {
            if (!is_array($manualJoin)) {
                continue;
            }

            foreach ((array) ($manualJoin['conditions'] ?? []) as $condition) {
                if (!is_array($condition)) {
                    continue;
                }

                $leftField = trim((string) ($condition['leftField'] ?? ''));
                if ($leftField !== '') {
                    $fieldNames[$leftField] = true;
                }

                $rightField = trim((string) ($condition['rightField'] ?? ''));
                if ($rightField !== '') {
                    $fieldNames[$rightField] = true;
                }
            }
        }

        return array_keys($fieldNames);
    }

    /**
     * @param array<int, string> $requestedFields
     * @param array<string, bool> $availableTables
     * @return array<int, string>
     */
    private function collectRequestedTables(string $baseTable, array $requestedFields, array $availableTables): array
    {
        $requestedTables = [
            $baseTable => true,
        ];

        foreach ($requestedFields as $fieldName) {
            $fieldTable = $this->extractTableFromFieldName($fieldName);
            if ($fieldTable === null || !isset($availableTables[$fieldTable])) {
                continue;
            }

            $requestedTables[$fieldTable] = true;
        }

        return array_keys($requestedTables);
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, bool>
     */
    private function buildAvailableTableSet($connection, array $tenantContext): array
    {
        $availableTables = [];

        foreach ($this->schemaRepository->listTables($connection, $tenantContext) as $table) {
            if (!is_string($table)) {
                continue;
            }

            $normalizedTable = strtoupper(trim($table));
            if ($normalizedTable === '') {
                continue;
            }

            $availableTables[$normalizedTable] = true;
        }

        return $availableTables;
    }

    /**
     * @param array<string, bool> $availableTables
     */
    private function assertExplicitRequestedTableAccess(
        AccessPolicyService $accessPolicy,
        string $baseTable,
        Request $request,
        array $manualJoinOverrides,
        array $availableTables
    ): void
    {
        $requestedTables = $this->collectExplicitRequestedTables(
            $baseTable,
            $this->collectRequestedFieldNames($request, $manualJoinOverrides),
            $manualJoinOverrides,
            $availableTables
        );

        foreach ($requestedTables as $requestedTable) {
            if ($requestedTable === $baseTable || !isset($availableTables[$requestedTable])) {
                continue;
            }

            try {
                $this->blocklist->assertTableOperationAllowed($requestedTable, 'read');
            } catch (ApiException $exception) {
                throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                    'table' => $requestedTable,
                ]);
            }

            if (!$accessPolicy->isTableAllowed($requestedTable)) {
                throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                    'table' => $requestedTable,
                ]);
            }
        }
    }

    /**
     * @param array<int, string> $requestedFields
     * @param array<int, array<string, mixed>> $manualJoinOverrides
     * @param array<string, bool> $availableTables
     * @return array<int, string>
     */
    private function collectExplicitRequestedTables(
        string $baseTable,
        array $requestedFields,
        array $manualJoinOverrides,
        array $availableTables
    ): array
    {
        $requestedTables = [
            $baseTable => true,
        ];

        foreach ($requestedFields as $fieldName) {
            $fieldTable = $this->extractTableFromFieldName($fieldName);
            if ($fieldTable === null || !isset($availableTables[$fieldTable])) {
                continue;
            }

            $requestedTables[$fieldTable] = true;
        }

        foreach (array_keys($this->collectManualJoinTables($manualJoinOverrides)) as $manualJoinTable) {
            if (!isset($availableTables[$manualJoinTable])) {
                continue;
            }

            $requestedTables[$manualJoinTable] = true;
        }

        return array_keys($requestedTables);
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<int, array<string, mixed>>
     */
    private function attachTableToFields(array $fields, string $table): array
    {
        $fieldsWithTable = [];

        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }

            $fieldWithTable = $field;
            $fieldWithTable['table'] = $table;
            $fieldsWithTable[] = $fieldWithTable;
        }

        return $fieldsWithTable;
    }

    /**
     * @return array<int, string>
     */
    private function splitCommaSeparatedValues($values): array
    {
        if ($values === null || trim((string) $values) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $values)), static function (string $value): bool {
            return $value !== '';
        }));
    }

    private function extractTableFromFieldName(string $fieldName): ?string
    {
        $normalizedFieldName = strtoupper(trim($fieldName));
        $separatorPosition = strpos($normalizedFieldName, '_');

        if ($separatorPosition === false || $separatorPosition <= 0) {
            return null;
        }

        $tableName = substr($normalizedFieldName, 0, $separatorPosition);
        if ($tableName === '' || !preg_match('/^[A-Z0-9]+$/', $tableName)) {
            return null;
        }

        return $tableName;
    }

    private function queryRequiresJoin(Request $request, string $baseTable, array $manualJoinOverrides): bool
    {
        foreach ($this->collectRequestedFieldNames($request, $manualJoinOverrides) as $fieldName) {
            $fieldTable = $this->extractTableFromFieldName($fieldName);
            if ($fieldTable !== null && $fieldTable !== $baseTable) {
                return true;
            }
        }

        return false;
    }

    private function hasExplicitFields(Request $request): bool
    {
        $queryParams = $request->getQueryParams();

        return array_key_exists('fields', $queryParams)
            && trim((string) ($queryParams['fields'] ?? '')) !== '';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractManualJoinOverrides(Request $request, string $baseTable): array
    {
        $queryParams = $request->getQueryParams();
        if (!array_key_exists('joins', $queryParams)) {
            return [];
        }

        $joinsParam = $queryParams['joins'];
        if ($joinsParam === null || $joinsParam === '') {
            return [];
        }

        if (is_string($joinsParam)) {
            $decoded = json_decode($joinsParam, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                throw new ApiException('INVALID_JOINS', 'Parâmetro joins inválido.', 400);
            }

            $joins = $decoded;
        } elseif (is_array($joinsParam)) {
            $joins = $joinsParam;
        } else {
            throw new ApiException('INVALID_JOINS', 'Parâmetro joins inválido.', 400);
        }

        $normalizedJoins = [];
        $seenTables = [];

        foreach ($joins as $joinIndex => $join) {
            if (!is_array($join)) {
                throw new ApiException('INVALID_JOINS', 'Parâmetro joins inválido.', 400, [
                    'join_index' => $joinIndex,
                ]);
            }

            foreach (['on', 'extraCondition', 'expression', 'sql', 'rawSql', 'raw_sql'] as $blockedKey) {
                if (array_key_exists($blockedKey, $join)) {
                    throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                        'join_index' => $joinIndex,
                        'blocked_key' => $blockedKey,
                    ]);
                }
            }

            $joinTable = strtoupper(trim((string) ($join['table'] ?? '')));
            if ($joinTable === '') {
                throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                    'join_index' => $joinIndex,
                ]);
            }

            if ($joinTable === $baseTable) {
                throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                    'join_index' => $joinIndex,
                    'table' => $joinTable,
                ]);
            }

            if (isset($seenTables[$joinTable])) {
                throw new ApiException('INVALID_JOINS', 'Join manual duplicado para a tabela.', 400, [
                    'join_index' => $joinIndex,
                    'table' => $joinTable,
                ]);
            }

            $joinType = strtoupper(trim((string) ($join['type'] ?? 'LEFT')));
            if ($joinType !== 'LEFT') {
                throw new ApiException('INVALID_JOINS', 'Tipo de join inválido.', 400, [
                    'join_index' => $joinIndex,
                    'table' => $joinTable,
                    'type' => $joinType,
                ]);
            }

            $conditions = $join['conditions'] ?? null;
            if (!is_array($conditions) || $conditions === []) {
                throw new ApiException('INVALID_JOINS', 'Condições de join inválidas.', 400, [
                    'join_index' => $joinIndex,
                    'table' => $joinTable,
                ]);
            }

            $normalizedConditions = [];
            foreach ($conditions as $conditionIndex => $condition) {
                if (!is_array($condition)) {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $joinIndex,
                        'condition_index' => $conditionIndex,
                        'table' => $joinTable,
                    ]);
                }

                foreach (['on', 'extraCondition', 'expression', 'sql', 'rawSql', 'raw_sql'] as $blockedKey) {
                    if (array_key_exists($blockedKey, $condition)) {
                        throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                            'join_index' => $joinIndex,
                            'condition_index' => $conditionIndex,
                            'table' => $joinTable,
                            'blocked_key' => $blockedKey,
                        ]);
                    }
                }

                $leftField = trim((string) ($condition['leftField'] ?? ''));
                $operator = trim((string) ($condition['operator'] ?? ''));
                $hasRightField = array_key_exists('rightField', $condition);
                $hasValue = array_key_exists('value', $condition);

                if ($leftField === '' || !in_array($operator, ['=', '!=', '>', '>=', '<', '<='], true) || $hasRightField === $hasValue) {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $joinIndex,
                        'condition_index' => $conditionIndex,
                        'table' => $joinTable,
                    ]);
                }

                $normalizedCondition = [
                    'leftField' => $leftField,
                    'operator' => $operator,
                ];

                if ($hasRightField) {
                    $rightField = trim((string) $condition['rightField']);
                    if ($rightField === '') {
                        throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                            'join_index' => $joinIndex,
                            'condition_index' => $conditionIndex,
                            'table' => $joinTable,
                        ]);
                    }

                    $normalizedCondition['rightField'] = $rightField;
                } else {
                    $value = $condition['value'];
                    if (!is_scalar($value)) {
                        throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                            'join_index' => $joinIndex,
                            'condition_index' => $conditionIndex,
                            'table' => $joinTable,
                        ]);
                    }

                    $normalizedCondition['value'] = $value;
                }

                $normalizedConditions[] = $normalizedCondition;
            }

            $seenTables[$joinTable] = true;
            $normalizedJoins[] = [
                'table' => $joinTable,
                'type' => $joinType,
                'conditions' => $normalizedConditions,
            ];
        }

        return $normalizedJoins;
    }

    /**
     * @param array<int, array<string, mixed>> $manualJoinOverrides
     * @return array<string, bool>
     */
    private function collectManualJoinTables(array $manualJoinOverrides): array
    {
        $tables = [];

        foreach ($manualJoinOverrides as $manualJoin) {
            if (!is_array($manualJoin)) {
                continue;
            }

            $table = strtoupper(trim((string) ($manualJoin['table'] ?? '')));
            if ($table === '') {
                continue;
            }

            $tables[$table] = true;
        }

        return $tables;
    }

    /**
     * @param array<int, array<string, mixed>> $validatedManualJoins
     * @return array<string, mixed>
     */
    private function applyManualJoinOverridesToPlan(array $joinPlan, array $validatedManualJoins): array
    {
        if ($validatedManualJoins === []) {
            unset($joinPlan['manual_overrides']);
            return $joinPlan;
        }

        $baseTable = strtoupper(trim((string) ($joinPlan['base_table'] ?? '')));
        $availableTables = [];
        if ($baseTable !== '') {
            $availableTables[$baseTable] = true;
        }

        $manualJoinMap = [];
        foreach ($validatedManualJoins as $manualJoin) {
            if (!is_array($manualJoin)) {
                continue;
            }

            $joinTable = strtoupper(trim((string) ($manualJoin['table'] ?? '')));
            if ($joinTable === '') {
                continue;
            }

            $manualJoinMap[$joinTable] = $manualJoin;
        }

        $appliedManualJoins = [];
        $finalJoins = [];

        foreach ((array) ($joinPlan['joins'] ?? []) as $join) {
            if (!is_array($join)) {
                continue;
            }

            $joinTable = strtoupper(trim((string) ($join['to_table'] ?? '')));
            if ($joinTable !== '' && isset($manualJoinMap[$joinTable])) {
                $finalJoins[] = $this->buildStructuredManualJoin($manualJoinMap[$joinTable], $availableTables);
                $availableTables[$joinTable] = true;
                $appliedManualJoins[$joinTable] = true;
                continue;
            }

            $finalJoins[] = $join;
            if ($joinTable !== '') {
                $availableTables[$joinTable] = true;
            }
        }

        foreach ($validatedManualJoins as $manualJoin) {
            if (!is_array($manualJoin)) {
                continue;
            }

            $joinTable = strtoupper(trim((string) ($manualJoin['table'] ?? '')));
            if ($joinTable === '' || isset($appliedManualJoins[$joinTable])) {
                continue;
            }

            $finalJoins[] = $this->buildStructuredManualJoin($manualJoin, $availableTables);
            $availableTables[$joinTable] = true;
        }

        $joinPlan['joins'] = $finalJoins;
        $joinPlan['tables'] = array_keys($availableTables);
        unset($joinPlan['manual_overrides']);

        return $joinPlan;
    }

    /**
     * @param array<string, bool> $availableTables
     * @return array<string, mixed>
     */
    private function buildStructuredManualJoin(array $manualJoin, array $availableTables): array
    {
        $joinTable = strtoupper(trim((string) ($manualJoin['table'] ?? '')));
        if ($joinTable === '') {
            throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400);
        }

        $structuredConditions = [];
        $hasTableLink = false;

        foreach ((array) ($manualJoin['conditions'] ?? []) as $conditionIndex => $condition) {
            if (!is_array($condition)) {
                throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                    'table' => $joinTable,
                    'condition_index' => $conditionIndex,
                ]);
            }

            $kind = (string) ($condition['kind'] ?? '');
            $leftTable = strtoupper(trim((string) ($condition['left_table'] ?? '')));
            $leftColumn = trim((string) ($condition['left_column'] ?? ''));
            $operator = trim((string) ($condition['operator'] ?? ''));

            if ($leftTable === '' || $leftColumn === '' || $operator === '') {
                throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                    'table' => $joinTable,
                    'condition_index' => $conditionIndex,
                ]);
            }

            if ($kind === 'field') {
                $rightTable = strtoupper(trim((string) ($condition['right_table'] ?? '')));
                $rightColumn = trim((string) ($condition['right_column'] ?? ''));
                if ($rightTable === '' || $rightColumn === '') {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'table' => $joinTable,
                        'condition_index' => $conditionIndex,
                    ]);
                }

                $leftIsJoinTable = $leftTable === $joinTable;
                $rightIsJoinTable = $rightTable === $joinTable;
                if (!$leftIsJoinTable && !$rightIsJoinTable) {
                    throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                        'table' => $joinTable,
                        'condition_index' => $conditionIndex,
                    ]);
                }

                $referenceTable = $leftIsJoinTable ? $rightTable : $leftTable;
                if ($referenceTable !== $joinTable) {
                    if (!isset($availableTables[$referenceTable])) {
                        throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                            'table' => $joinTable,
                            'condition_index' => $conditionIndex,
                            'reference_table' => $referenceTable,
                        ]);
                    }

                    $hasTableLink = true;
                }

                $structuredConditions[] = [
                    'kind' => 'field',
                    'operator' => $operator,
                    'left' => [
                        'table' => $leftTable,
                        'column' => $leftColumn,
                    ],
                    'right' => [
                        'table' => $rightTable,
                        'column' => $rightColumn,
                    ],
                ];
                continue;
            }

            if ($kind === 'value') {
                if ($leftTable !== $joinTable) {
                    throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                        'table' => $joinTable,
                        'condition_index' => $conditionIndex,
                    ]);
                }

                $structuredConditions[] = [
                    'kind' => 'value',
                    'operator' => $operator,
                    'left' => [
                        'table' => $leftTable,
                        'column' => $leftColumn,
                    ],
                    'value' => $condition['value'] ?? null,
                ];
                continue;
            }

            throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                'table' => $joinTable,
                'condition_index' => $conditionIndex,
            ]);
        }

        if (!$hasTableLink) {
            throw new ApiException('INVALID_JOINS', 'Join manual inválido.', 400, [
                'table' => $joinTable,
            ]);
        }

        return [
            'mode' => 'manual',
            'to_table' => $joinTable,
            'join_type' => 'LEFT',
            'conditions' => $structuredConditions,
        ];
    }

}
