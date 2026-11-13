<?php

declare(strict_types=1);

namespace RestApi\Security;

use RestApi\Core\ApiException;

class Blocklist
{
    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function assertTableOperationAllowed(string $table, string $operation): void
    {
        $normalizedTable = strtoupper(trim($table));
        $normalizedOperation = strtolower(trim($operation));

        if ($this->isTableBlocked($normalizedTable)) {
            throw new ApiException('TABLE_BLOCKED', 'Tabela bloqueada.', 403, [
                'table' => $normalizedTable,
            ]);
        }

        $blockedOperations = $this->config['blocked_operations'] ?? [];
        foreach ($blockedOperations as $configuredTable => $operations) {
            if (strtoupper((string) $configuredTable) !== $normalizedTable) {
                continue;
            }

            $normalizedOperations = array_map('strtolower', is_array($operations) ? $operations : []);
            if (in_array($normalizedOperation, $normalizedOperations, true)) {
                throw new ApiException('OPERATION_BLOCKED', 'Operação bloqueada para a tabela.', 403, [
                    'table' => $normalizedTable,
                    'operation' => $normalizedOperation,
                ]);
            }
        }
    }

    public function isTableBlocked(string $table): bool
    {
        $normalizedTable = strtoupper(trim($table));

        foreach ((array) ($this->config['blocked_tables'] ?? []) as $blockedTable) {
            if (strtoupper((string) $blockedTable) === $normalizedTable) {
                return true;
            }
        }

        foreach ((array) ($this->config['blocked_table_patterns'] ?? []) as $pattern) {
            if (@preg_match((string) $pattern, $normalizedTable) === 1) {
                return true;
            }
        }

        return false;
    }
}
