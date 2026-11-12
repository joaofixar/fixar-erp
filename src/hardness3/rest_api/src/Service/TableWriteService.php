<?php

declare(strict_types=1);

namespace RestApi\Service;

use PDO;
use PDOException;
use RestApi\Core\ApiException;
use RestApi\Database\QueryBuilder;
use RestApi\Database\SchemaRepository;
use RestApi\Security\AccessPolicyService;
use RestApi\Security\Blocklist;

class TableWriteService
{
    private SchemaRepository $schemaRepository;
    private QueryBuilder $queryBuilder;
    private Blocklist $blocklist;
    private AccessPolicyService $accessPolicyService;
    private PayloadValidator $payloadValidator;

    public function __construct(
        SchemaRepository $schemaRepository,
        QueryBuilder $queryBuilder,
        Blocklist $blocklist,
        AccessPolicyService $accessPolicyService,
        PayloadValidator $payloadValidator
    ) {
        $this->schemaRepository = $schemaRepository;
        $this->queryBuilder = $queryBuilder;
        $this->blocklist = $blocklist;
        $this->accessPolicyService = $accessPolicyService;
        $this->payloadValidator = $payloadValidator;
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    public function validatePayload($connection, array $tenantContext, string $table, ?array $payload, bool $isUpdate = false): array
    {
        $this->blocklist->assertTableOperationAllowed($table, $isUpdate ? 'update' : 'insert');
        $accessPolicy = $this->accessPolicyService->forTenant($tenantContext);
        if (!$accessPolicy->isTableAllowed($table)) {
            throw new ApiException('TABLE_NOT_ALLOWED', 'Table is not allowed', 403, [
                'table' => $table,
            ]);
        }

        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $table);
        $allowedFields = $accessPolicy->getAllowedFields($table, (array) ($schema['fields'] ?? []));

        $validatedPayload = $isUpdate
            ? $this->payloadValidator->validateForUpdate($payload, $schema, $allowedFields)
            : $this->payloadValidator->validateForInsert($payload, $schema, $allowedFields);

        return [
            'table' => $schema['table'],
            'primary_key' => $schema['primary_key'],
            'validated_payload' => $validatedPayload,
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    public function insert($connection, array $tenantContext, string $table, ?array $payload): array
    {
        $validated = $this->validatePayload($connection, $tenantContext, $table, $payload, false);
        $query = $this->queryBuilder->buildInsert($validated['table'], $validated['validated_payload']);

        try {
            $this->beginTransactionIfSupported($connection);
            $statement = $connection->prepare($query['sql']);
            $this->bindParams($statement, $query['params']);
            $statement->execute();
            $this->commitIfSupported($connection);
        } catch (PDOException $exception) {
            $this->rollbackIfSupported($connection);
            throw new ApiException('WRITE_ERROR', 'Não foi possível inserir o registro.', 500);
        }

        $primaryKeyValue = null;
        if (method_exists($connection, 'lastInsertId')) {
            $lastInsertId = $connection->lastInsertId();
            if ($lastInsertId !== false && $lastInsertId !== '0' && $lastInsertId !== 0 && $validated['primary_key'] !== null) {
                $primaryKeyValue = $lastInsertId;
            }
        }

        return [
            'primary_key' => $primaryKeyValue !== null ? [
                (string) $validated['primary_key'] => $primaryKeyValue,
            ] : new \stdClass(),
            'affected_rows' => method_exists($statement, 'rowCount') ? (int) $statement->rowCount() : 1,
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    public function update($connection, array $tenantContext, string $table, string $recordId, ?array $payload): array
    {
        $validated = $this->validatePayload($connection, $tenantContext, $table, $payload, true);
        $primaryKey = $this->assertPrimaryKeyDetected($validated['primary_key'] ?? null);
        $validatedRecordId = $this->assertPrimaryKeyValue($recordId);

        $query = $this->queryBuilder->buildUpdateByPrimaryKey(
            $validated['table'],
            $validated['validated_payload'],
            $primaryKey,
            $validatedRecordId
        );

        try {
            $this->beginTransactionIfSupported($connection);
            $statement = $connection->prepare($query['sql']);
            $this->bindParams($statement, $query['params']);
            $statement->execute();
            $affectedRows = method_exists($statement, 'rowCount') ? (int) $statement->rowCount() : 0;

            if ($affectedRows > 1) {
                $this->rollbackIfSupported($connection);
                throw new ApiException(
                    'UPDATE_AFFECTED_MULTIPLE_ROWS',
                    'Update affected more than one row and was aborted',
                    500,
                    [
                        'table' => $table,
                        'primary_key' => $primaryKey,
                        'id' => $validatedRecordId,
                        'affected_rows' => $affectedRows,
                    ]
                );
            }

            if ($affectedRows === 0 && !$this->recordExists($connection, $validated['table'], $primaryKey, $validatedRecordId)) {
                $this->rollbackIfSupported($connection);
                throw new ApiException('RECORD_NOT_FOUND', 'Registro não encontrado.', 404, [
                    'table' => $table,
                    'id' => $validatedRecordId,
                ]);
            }

            $this->commitIfSupported($connection);
        } catch (PDOException $exception) {
            $this->rollbackIfSupported($connection);
            throw new ApiException('WRITE_ERROR', 'Não foi possível atualizar o registro.', 500);
        }

        return [
            'primary_key' => [
                $primaryKey => $validatedRecordId,
            ],
            'affected_rows' => $affectedRows,
        ];
    }

    /**
     * @param PDO|object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function delete($connection, array $tenantContext, string $table, string $recordId): array
    {
        $this->blocklist->assertTableOperationAllowed($table, 'delete');
        $schema = $this->schemaRepository->getTableSchema($connection, $tenantContext, $table);
        $primaryKey = $this->assertPrimaryKeyDetected($schema['primary_key'] ?? null);
        $validatedRecordId = $this->assertPrimaryKeyValue($recordId);

        $query = $this->queryBuilder->buildDeleteByPrimaryKey($schema['table'], $primaryKey, $validatedRecordId);

        try {
            $this->beginTransactionIfSupported($connection);
            $statement = $connection->prepare($query['sql']);
            $this->bindParams($statement, $query['params']);
            $statement->execute();
            $affectedRows = method_exists($statement, 'rowCount') ? (int) $statement->rowCount() : 0;

            if ($affectedRows === 0) {
                $this->rollbackIfSupported($connection);
                throw new ApiException('RECORD_NOT_FOUND', 'Registro não encontrado.', 404, [
                    'table' => $table,
                    'id' => $validatedRecordId,
                ]);
            }

            $this->commitIfSupported($connection);
        } catch (PDOException $exception) {
            $this->rollbackIfSupported($connection);
            throw new ApiException('WRITE_ERROR', 'Não foi possível excluir o registro.', 500);
        }

        return [
            'primary_key' => [
                $primaryKey => $validatedRecordId,
            ],
            'affected_rows' => $affectedRows,
        ];
    }

    /**
     * @param mixed $primaryKey
     */
    private function assertPrimaryKeyDetected($primaryKey): string
    {
        $normalizedPrimaryKey = is_string($primaryKey) ? trim($primaryKey) : '';
        if ($normalizedPrimaryKey === '') {
            throw new ApiException(
                'PRIMARY_KEY_NOT_FOUND',
                'Table does not have a detectable primary key',
                400
            );
        }

        return $normalizedPrimaryKey;
    }

    private function assertPrimaryKeyValue(string $recordId): string
    {
        $normalizedRecordId = trim($recordId);
        if ($normalizedRecordId === '') {
            throw new ApiException(
                'INVALID_PRIMARY_KEY',
                'A valid primary key value is required for update',
                400
            );
        }

        return $normalizedRecordId;
    }

    /**
     * @param object $statement
     * @param array<string, mixed> $params
     */
    private function bindParams($statement, array $params): void
    {
        foreach ($params as $name => $value) {
            if ($value === null) {
                $statement->bindValue(':' . $name, null, PDO::PARAM_NULL);
                continue;
            }

            if (is_bool($value)) {
                $statement->bindValue(':' . $name, $value, PDO::PARAM_BOOL);
                continue;
            }

            $parameterType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $statement->bindValue(':' . $name, $value, $parameterType);
        }
    }

    /**
     * @param PDO|object $connection
     */
    private function recordExists($connection, string $table, string $primaryKey, string $recordId): bool
    {
        $query = $this->queryBuilder->buildExistsByPrimaryKey($table, $primaryKey, $recordId);
        $statement = $connection->prepare($query['sql']);
        $this->bindParams($statement, $query['params']);
        $statement->execute();
        $row = $statement->fetch();

        if (is_array($row)) {
            return $row !== [];
        }

        return $row !== false && $row !== null;
    }

    /**
     * @param PDO|object $connection
     */
    private function beginTransactionIfSupported($connection): void
    {
        if (method_exists($connection, 'beginTransaction')) {
            $connection->beginTransaction();
        }
    }

    /**
     * @param PDO|object $connection
     */
    private function commitIfSupported($connection): void
    {
        if (method_exists($connection, 'commit')) {
            $connection->commit();
        }
    }

    /**
     * @param PDO|object $connection
     */
    private function rollbackIfSupported($connection): void
    {
        if (method_exists($connection, 'rollBack')) {
            $connection->rollBack();
        }
    }
}
