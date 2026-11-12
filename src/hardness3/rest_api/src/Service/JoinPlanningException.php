<?php

declare(strict_types=1);

namespace RestApi\Service;

use RestApi\Core\ApiException;

class JoinPlanningException extends ApiException
{
    public static function tenantDatabaseRequired(): self
    {
        return new self(
            'JOIN_TENANT_DATABASE_REQUIRED',
            'Contexto do tenant sem database configurado.',
            500
        );
    }

    public static function baseTableRequired(): self
    {
        return new self(
            'JOIN_BASE_TABLE_REQUIRED',
            'Tabela base não informada.',
            400
        );
    }

    public static function targetTableRequired(): self
    {
        return new self(
            'JOIN_TARGET_TABLE_REQUIRED',
            'Tabela destino não informada.',
            400
        );
    }

    public static function targetTablesRequired(): self
    {
        return new self(
            'JOIN_TARGET_TABLES_REQUIRED',
            'Lista de tabelas destino não informada.',
            400
        );
    }

    /**
     * @param mixed $value
     */
    public static function invalidTargetTableValue($value): self
    {
        return new self(
            'JOIN_TARGET_TABLE_INVALID',
            'Tabela destino inválida.',
            400,
            [
                'value' => $value,
            ]
        );
    }

    public static function tableNotFound(string $table): self
    {
        return new self(
            'JOIN_TABLE_NOT_FOUND',
            'Tabela não encontrada para planejamento de join.',
            404,
            [
                'table' => $table,
            ]
        );
    }

    public static function pathNotFound(string $baseTable, string $targetTable): self
    {
        return new self(
            'JOIN_PATH_NOT_FOUND',
            'Não existe caminho entre as tabelas informadas.',
            404,
            [
                'base_table' => $baseTable,
                'target_table' => $targetTable,
            ]
        );
    }

    public static function ambiguousPath(string $baseTable, string $targetTable, int $distance): self
    {
        return new self(
            'JOIN_PATH_AMBIGUOUS',
            'Existe mais de um menor caminho possível entre as tabelas informadas.',
            409,
            [
                'base_table' => $baseTable,
                'target_table' => $targetTable,
                'distance' => $distance,
            ]
        );
    }

    public static function invalidSchema(string $table): self
    {
        return new self(
            'JOIN_SCHEMA_INVALID',
            'Schema inválido para planejamento de join.',
            500,
            [
                'table' => $table,
            ]
        );
    }
}
