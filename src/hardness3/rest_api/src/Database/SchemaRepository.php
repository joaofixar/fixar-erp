<?php

declare(strict_types=1);

namespace RestApi\Database;

use PDOException;
use RestApi\Core\ApiException;

class SchemaRepository
{
    /** @var array<string, mixed> */
    private array $appConfig;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<int, string>
     */
    public function listTables($connection, array $tenantContext): array
    {
        try {
            $statement = $connection->prepare(
                'SELECT TABLE_NAME
                   FROM INFORMATION_SCHEMA.TABLES
                  WHERE TABLE_SCHEMA = :database
                    AND TABLE_TYPE = :table_type
               ORDER BY TABLE_NAME ASC'
            );
            $statement->execute([
                'database' => $tenantContext['dbDatabase'],
                'table_type' => 'BASE TABLE',
            ]);
            $rows = $statement->fetchAll();
        } catch (PDOException $exception) {
            throw new ApiException('SCHEMA_READ_ERROR', 'Não foi possível consultar as tabelas do banco.', 500);
        }

        $tables = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !isset($row['TABLE_NAME'])) {
                continue;
            }
            $tables[] = (string) $row['TABLE_NAME'];
        }

        return $tables;
    }

    /**
     * @param object $connection
     * @param array<string, string> $tenantContext
     * @return array<string, mixed>
     */
    public function getTableSchema($connection, array $tenantContext, string $table): array
    {
        $normalizedTable = trim($table);
        if ($normalizedTable === '') {
            throw new ApiException('TABLE_NOT_FOUND', 'Tabela não encontrada.', 404);
        }

        $cacheConfig = $this->appConfig['schema_cache'] ?? [];
        $cacheEnabled = (bool) ($cacheConfig['enabled'] ?? false);
        $cachePath = (string) ($cacheConfig['path'] ?? '');
        $ttl = (int) ($cacheConfig['ttl_seconds'] ?? 0);

        if ($cacheEnabled) {
            $cached = $this->readCache($cachePath, $tenantContext['dbDatabase'], $normalizedTable, $ttl);
            if (is_array($cached)) {
                return $cached;
            }
        }

        try {
            $statement = $connection->prepare(
                'SELECT
                    COLUMN_NAME,
                    DATA_TYPE,
                    COLUMN_TYPE,
                    IS_NULLABLE,
                    COLUMN_DEFAULT,
                    COLUMN_KEY,
                    EXTRA,
                    CHARACTER_MAXIMUM_LENGTH,
                    NUMERIC_PRECISION,
                    NUMERIC_SCALE,
                    DATETIME_PRECISION,
                    ORDINAL_POSITION
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = :database
                   AND TABLE_NAME = :table
                 ORDER BY ORDINAL_POSITION ASC'
            );
            $statement->execute([
                'database' => $tenantContext['dbDatabase'],
                'table' => $normalizedTable,
            ]);
            $rows = $statement->fetchAll();
        } catch (PDOException $exception) {
            throw new ApiException('SCHEMA_READ_ERROR', 'Não foi possível consultar o schema da tabela.', 500);
        }

        if (!is_array($rows) || $rows === []) {
            throw new ApiException('TABLE_NOT_FOUND', 'Tabela não encontrada.', 404, [
                'table' => $normalizedTable,
            ]);
        }

        $fields = [];
        $primaryKey = null;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $isPrimaryKey = (($row['COLUMN_KEY'] ?? '') === 'PRI');
            $isAutoIncrement = stripos((string) ($row['EXTRA'] ?? ''), 'auto_increment') !== false;

            if ($isPrimaryKey && $primaryKey === null) {
                $primaryKey = (string) $row['COLUMN_NAME'];
            }

            $fields[] = [
                'name' => (string) $row['COLUMN_NAME'],
                'data_type' => (string) $row['DATA_TYPE'],
                'column_type' => (string) $row['COLUMN_TYPE'],
                'nullable' => (string) $row['IS_NULLABLE'] === 'YES',
                'default' => $row['COLUMN_DEFAULT'],
                'key' => (string) $row['COLUMN_KEY'],
                'extra' => (string) $row['EXTRA'],
                'max_length' => $row['CHARACTER_MAXIMUM_LENGTH'] !== null ? (int) $row['CHARACTER_MAXIMUM_LENGTH'] : null,
                'numeric_precision' => $row['NUMERIC_PRECISION'] !== null ? (int) $row['NUMERIC_PRECISION'] : null,
                'numeric_scale' => $row['NUMERIC_SCALE'] !== null ? (int) $row['NUMERIC_SCALE'] : null,
                'datetime_precision' => $row['DATETIME_PRECISION'] !== null ? (int) $row['DATETIME_PRECISION'] : null,
                'ordinal_position' => (int) $row['ORDINAL_POSITION'],
                'is_primary_key' => $isPrimaryKey,
                'is_auto_increment' => $isAutoIncrement,
            ];
        }

        $schema = [
            'table' => $normalizedTable,
            'database' => $tenantContext['dbDatabase'],
            'primary_key' => $primaryKey,
            'fields' => $fields,
        ];

        if ($cacheEnabled) {
            $this->writeCache($cachePath, $tenantContext['dbDatabase'], $normalizedTable, $schema);
        }

        return $schema;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readCache(string $cachePath, string $database, string $table, int $ttl): ?array
    {
        $file = $this->getCacheFile($cachePath, $database, $table);
        if (!is_file($file)) {
            return null;
        }

        if ($ttl > 0 && (time() - filemtime($file)) > $ttl) {
            @unlink($file);
            return null;
        }

        $content = file_get_contents($file);
        if (!is_string($content) || $content === '') {
            return null;
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function writeCache(string $cachePath, string $database, string $table, array $schema): void
    {
        if ($cachePath === '') {
            return;
        }

        if (!is_dir($cachePath)) {
            @mkdir($cachePath, 0775, true);
        }

        $file = $this->getCacheFile($cachePath, $database, $table);
        $encoded = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return;
        }

        $tempFile = $file . '.' . uniqid('tmp_', true);
        if (@file_put_contents($tempFile, $encoded, LOCK_EX) === false) {
            @unlink($tempFile);
            return;
        }

        @chmod($tempFile, 0664);
        @rename($tempFile, $file);
    }

    private function getCacheFile(string $cachePath, string $database, string $table): string
    {
        $key = hash('sha256', strtolower($database . '::' . $table));
        return rtrim($cachePath, '/') . '/' . $key . '.json';
    }
}
