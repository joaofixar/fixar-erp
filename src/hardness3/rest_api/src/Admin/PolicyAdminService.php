<?php

declare(strict_types=1);

namespace RestApi\Admin;

use PDO;
use RestApi\Database\ConnectionFactory;
use RestApi\Database\SchemaRepository;
use RestApi\Security\AccessPolicyRepository;

class PolicyAdminService
{
    private AccessPolicyRepository $accessPolicyRepository;
    private SchemaRepository $schemaRepository;
    private ConnectionFactory $connectionFactory;

    /** @var array<string, mixed> */
    private array $tenantContext = [];

    /** @var array<int, string> */
    private array $blockedTables = ['A001', 'A002'];

    public function __construct(
        AccessPolicyRepository $accessPolicyRepository,
        SchemaRepository $schemaRepository,
        ConnectionFactory $connectionFactory
    ) {
        $this->accessPolicyRepository = $accessPolicyRepository;
        $this->schemaRepository = $schemaRepository;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * @param array<string, mixed> $tenantContext
     */
    public function forTenant(array $tenantContext): self
    {
        $clone = clone $this;
        $clone->tenantContext = $tenantContext;

        return $clone;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPolicyStatus(): array
    {
        $record = $this->getPolicyInspection();
        $summary = [];

        if ($record['status'] === 'valid') {
            foreach ($record['policy'] as $table => $rule) {
                $summary[] = $this->buildTableSummary($table, $rule);
            }
        }

        usort($summary, static function (array $left, array $right): int {
            return strcmp((string) $left['table'], (string) $right['table']);
        });

        return [
            'status' => $record['status'],
            'path' => $record['path'],
            'last_modified' => $record['last_modified'],
            'total_tables' => count($summary),
            'tables' => $summary,
            'error' => $record['error'],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function listAvailableTables(): array
    {
        $tables = $this->schemaRepository->listTables($this->getConnection(), $this->getTenantContext());
        $tables = array_values(array_filter($tables, function (string $table): bool {
            return !$this->isStructurallyBlockedTable($table);
        }));
        sort($tables);

        return $tables;
    }

    /**
     * @return array<string, mixed>
     */
    public function loadTable(string $table): array
    {
        $normalizedTable = $this->normalizeTable($table);
        $this->assertTableAllowedForAdmin($normalizedTable);

        $schema = $this->getTableSchema($normalizedTable);
        $record = $this->getPolicyInspection();
        $rule = ($record['status'] === 'valid' && isset($record['policy'][$normalizedTable]))
            ? $record['policy'][$normalizedTable]
            : null;

        $mode = 'all';
        $selectedFields = [];
        if (is_array($rule)) {
            $mode = (string) ($rule['mode'] ?? 'all');
            $selectedFields = array_values((array) ($rule['fields'] ?? []));
        }

        return [
            'table' => $normalizedTable,
            'fields' => $schema['fields'],
            'mode' => $mode,
            'selected_fields' => $selectedFields,
            'has_rule' => is_array($rule),
            'summary' => $this->buildModeSummary($mode, $selectedFields, count($schema['fields'])),
            'policy_status' => $record['status'],
            'policy_error' => $record['error'],
        ];
    }

    /**
     * @param array<int, string> $fields
     */
    public function previewTable(string $table, string $mode, array $fields): string
    {
        $validated = $this->validateTablePayload($table, $mode, $fields);

        return $this->renderTableSnippet($validated['table'], $validated['rule']);
    }

    /**
     * @param array<int, string> $fields
     * @return array<string, mixed>
     */
    public function saveTable(string $table, string $mode, array $fields): array
    {
        $validated = $this->validateTablePayload($table, $mode, $fields);
        $record = $this->getPolicyInspection();

        if ($record['status'] === 'invalid') {
            throw new \RuntimeException('A policy atual está inválida. Corrija ou remova o arquivo antes de salvar.');
        }

        $policy = $record['status'] === 'valid' ? $record['policy'] : [];
        $policy[$validated['table']] = $validated['rule'];
        ksort($policy);

        $this->savePolicy($policy);

        return [
            'table' => $validated['table'],
            'preview' => $this->renderTableSnippet($validated['table'], $validated['rule']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function removeTable(string $table): array
    {
        $normalizedTable = $this->normalizeTable($table);
        if ($normalizedTable === '') {
            throw new \RuntimeException('Informe uma tabela válida para remover.');
        }

        $record = $this->getPolicyInspection();
        if ($record['status'] === 'invalid') {
            throw new \RuntimeException('A policy atual está inválida. Corrija ou remova o arquivo antes de editar.');
        }

        if ($record['status'] !== 'valid' || !isset($record['policy'][$normalizedTable])) {
            throw new \RuntimeException('A tabela informada não está configurada na policy.');
        }

        $policy = $record['policy'];
        unset($policy[$normalizedTable]);
        ksort($policy);

        $this->savePolicy($policy);

        return [
            'table' => $normalizedTable,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPolicyInspection(): array
    {
        return $this->accessPolicyRepository->inspect($this->getTenantContext());
    }

    /**
     * @return array<string, mixed>
     */
    private function getTenantContext(): array
    {
        return $this->tenantContext;
    }

    private function getPolicyPath(): string
    {
        $path = $this->accessPolicyRepository->getPolicyFilePath($this->getTenantContext());
        if (!is_string($path) || trim($path) === '') {
            throw new \RuntimeException('Não foi possível determinar o caminho da policy.');
        }

        return $path;
    }

    private function getHistoryDir(): string
    {
        return dirname($this->getPolicyPath()) . '/history';
    }

    /**
     * @return array<string, mixed>
     */
    private function getTableSchema(string $table): array
    {
        $schema = $this->schemaRepository->getTableSchema($this->getConnection(), $this->getTenantContext(), $table);

        return [
            'table' => $schema['table'],
            'fields' => array_values(array_map(static function (array $field): string {
                return (string) $field['name'];
            }, (array) ($schema['fields'] ?? []))),
        ];
    }

    private function getConnection(): PDO
    {
        static $connections = [];

        $context = $this->getTenantContext();
        $key = md5(json_encode([
            $context['dbHost'] ?? '',
            $context['dbDatabase'] ?? '',
            $context['dbUser'] ?? '',
        ]));

        if (!isset($connections[$key])) {
            $connections[$key] = $this->connectionFactory->create($context);
        }

        return $connections[$key];
    }

    private function normalizeTable(string $table): string
    {
        return strtoupper(trim($table));
    }

    private function isStructurallyBlockedTable(string $table): bool
    {
        return in_array(strtoupper(trim($table)), $this->blockedTables, true);
    }

    private function assertTableAllowedForAdmin(string $table): void
    {
        if ($table === '') {
            throw new \RuntimeException('Informe uma tabela válida.');
        }

        if ($this->isStructurallyBlockedTable($table)) {
            throw new \RuntimeException('A tabela informada não pode ser liberada nesta interface.');
        }
    }

    /**
     * @param array<int, string> $fields
     * @return array<string, mixed>
     */
    private function validateTablePayload(string $table, string $mode, array $fields): array
    {
        $normalizedTable = $this->normalizeTable($table);
        $this->assertTableAllowedForAdmin($normalizedTable);

        $schema = $this->getTableSchema($normalizedTable);
        $schemaFields = $schema['fields'];
        $normalizedMode = $this->normalizeMode($mode);

        if ($normalizedMode === 'all') {
            return [
                'table' => $normalizedTable,
                'rule' => ['mode' => 'all', 'fields' => []],
            ];
        }

        $normalizedFields = [];
        foreach ($fields as $field) {
            if (!is_string($field)) {
                continue;
            }

            $normalizedField = trim($field);
            if ($normalizedField === '') {
                continue;
            }

            if (!in_array($normalizedField, $schemaFields, true)) {
                throw new \RuntimeException('O campo "' . $normalizedField . '" não existe na tabela informada.');
            }

            $normalizedFields[] = $normalizedField;
        }

        $normalizedFields = array_values(array_unique($normalizedFields));
        if ($normalizedFields === []) {
            throw new \RuntimeException('Selecione ao menos um campo para o modo escolhido.');
        }

        return [
            'table' => $normalizedTable,
            'rule' => [
                'mode' => $normalizedMode,
                'fields' => $normalizedFields,
            ],
        ];
    }

    private function normalizeMode(string $mode): string
    {
        $mode = strtolower(trim($mode));
        if ($mode === 'true') {
            $mode = 'all';
        }

        if (!in_array($mode, ['all', 'block', 'allow'], true)) {
            throw new \RuntimeException('Modo de configuração inválido.');
        }

        return $mode;
    }

    /**
     * @param array<string, mixed> $rule
     * @return array<string, mixed>
     */
    private function buildTableSummary(string $table, array $rule): array
    {
        $mode = (string) ($rule['mode'] ?? 'all');
        $count = count((array) ($rule['fields'] ?? []));

        if ($mode === 'block') {
            $label = sprintf('Block (%d bloqueados)', $count);
        } elseif ($mode === 'allow') {
            $label = sprintf('Allow (%d liberados)', $count);
        } else {
            $label = 'Tabela inteira';
        }

        return [
            'table' => $table,
            'mode' => $mode,
            'count' => $count,
            'label' => $label,
        ];
    }

    /**
     * @param array<int, string> $selectedFields
     * @return array<string, int>
     */
    private function buildModeSummary(string $mode, array $selectedFields, int $totalFields): array
    {
        if ($mode === 'block') {
            $blocked = count($selectedFields);
            return [
                'total' => $totalFields,
                'allowed' => max(0, $totalFields - $blocked),
                'blocked' => $blocked,
            ];
        }

        if ($mode === 'allow') {
            $allowed = count($selectedFields);
            return [
                'total' => $totalFields,
                'allowed' => $allowed,
                'blocked' => max(0, $totalFields - $allowed),
            ];
        }

        return [
            'total' => $totalFields,
            'allowed' => $totalFields,
            'blocked' => 0,
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $policy
     */
    private function savePolicy(array $policy): void
    {
        $path = $this->getPolicyPath();
        $directory = dirname($path);
        $historyDirectory = $this->getHistoryDir();

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Não foi possível criar o diretório da policy.');
        }

        if (!is_dir($historyDirectory) && !@mkdir($historyDirectory, 0775, true) && !is_dir($historyDirectory)) {
            throw new \RuntimeException('Não foi possível criar o diretório de histórico da policy.');
        }

        if (is_file($path)) {
            $backupFile = $historyDirectory . '/access_policy_' . date('Ymd_His') . '.php';
            if (!@copy($path, $backupFile)) {
                throw new \RuntimeException('Não foi possível criar o backup da policy atual.');
            }
        }

        $content = $this->renderPolicyFile($policy);
        $tempFile = $path . '.tmp.' . uniqid('', true);

        if (@file_put_contents($tempFile, $content, LOCK_EX) === false) {
            @unlink($tempFile);
            throw new \RuntimeException('Não foi possível gravar o arquivo temporário da policy.');
        }

        if (!$this->isValidGeneratedPhp($tempFile)) {
            @unlink($tempFile);
            throw new \RuntimeException('O PHP gerado para a policy é inválido.');
        }

        @chmod($tempFile, 0664);
        if (!@rename($tempFile, $path)) {
            @unlink($tempFile);
            throw new \RuntimeException('Não foi possível publicar a nova versão da policy.');
        }

        clearstatcache(true, $path);
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($path, true);
        }
    }

    private function isValidGeneratedPhp(string $file): bool
    {
        try {
            $loaded = (static function (string $policyFile) {
                return require $policyFile;
            })($file);
        } catch (\Throwable $throwable) {
            return false;
        }

        return is_array($loaded);
    }

    /**
     * @param array<string, array<string, mixed>> $policy
     */
    private function renderPolicyFile(array $policy): string
    {
        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'return [';
        $lines[] = '';

        foreach ($policy as $table => $rule) {
            $snippet = $this->renderTableSnippet($table, $rule);
            foreach (explode("\n", $snippet) as $line) {
                $lines[] = '    ' . $line;
            }
            $lines[] = '';
        }

        $lines[] = '];';
        $lines[] = '';

        return implode("\n", $lines);
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function renderTableSnippet(string $table, array $rule): string
    {
        $mode = (string) ($rule['mode'] ?? 'all');
        $fields = array_values((array) ($rule['fields'] ?? []));

        if ($mode === 'all') {
            return "'" . $table . "' => true,";
        }

        $configKey = $mode === 'block' ? 'block' : 'allow';
        $lines = [];
        $lines[] = "'" . $table . "' => [";
        $lines[] = "    '" . $configKey . "' => [";
        foreach ($fields as $field) {
            $lines[] = "        '" . $field . "',";
        }
        $lines[] = '    ],';
        $lines[] = '],';

        return implode("\n", $lines);
    }
}
