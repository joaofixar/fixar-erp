<?php

declare(strict_types=1);

namespace RestApi\Database;

class QueryBuilder
{
    /**
     * @param array<string, mixed> $joinPlan
     * @param array<int, string> $fields
     * @param array<int, array<string, mixed>> $filters
     * @param array<int, array<string, string>> $sorts
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildSelect(array $joinPlan, array $fields, array $filters, array $sorts, int $limit, int $offset): array
    {
        $baseTable = trim((string) ($joinPlan['base_table'] ?? ''));
        $fieldSources = $this->normalizeFieldSources((array) ($joinPlan['field_sources'] ?? []), $baseTable);
        $params = [];
        $joinParamCounter = 0;
        $select = implode(', ', array_map(function (string $fieldName) use ($fieldSources, $baseTable): string {
            $fieldSource = $this->resolveFieldSource($fieldName, $fieldSources, $baseTable);
            return $this->quoteQualifiedIdentifier($fieldSource['table'], $fieldSource['column']);
        }, $fields));
        $sql = 'SELECT ' . $select . ' FROM ' . $this->quoteIdentifier($baseTable);

        foreach ((array) ($joinPlan['joins'] ?? []) as $join) {
            if (!is_array($join)) {
                continue;
            }

            $sql .= $this->buildJoinClause($join, $params, $joinParamCounter);
        }

        $where = $this->buildWhere($filters, $params, $fieldSources, $baseTable);
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        if ($sorts !== []) {
            $parts = [];
            foreach ($sorts as $sort) {
                $fieldSource = $this->resolveFieldSource((string) $sort['field'], $fieldSources, $baseTable);
                $parts[] = $this->quoteQualifiedIdentifier($fieldSource['table'], $fieldSource['column']) . ' ' . strtoupper($sort['direction']);
            }
            $sql .= ' ORDER BY ' . implode(', ', $parts);
        }

        $sql .= ' LIMIT :limit OFFSET :offset';
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return [
            'sql' => $sql,
            'params' => $params,
        ];
    }

    /**
     * @param array<int, string> $fields
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildSelectByPrimaryKey(string $table, array $fields, string $primaryKey, string $recordId): array
    {
        $select = implode(', ', array_map([$this, 'quoteIdentifier'], $fields));
        $sql = 'SELECT ' . $select .
            ' FROM ' . $this->quoteIdentifier($table) .
            ' WHERE ' . $this->quoteIdentifier($primaryKey) . ' = :primary_key_value LIMIT 1';

        return [
            'sql' => $sql,
            'params' => [
                'primary_key_value' => $recordId,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildInsert(string $table, array $payload): array
    {
        $fields = array_keys($payload);
        $columns = implode(', ', array_map([$this, 'quoteIdentifier'], $fields));
        $placeholders = [];
        $params = [];

        foreach ($fields as $field) {
            $paramName = 'insert_' . $field;
            $placeholders[] = ':' . $paramName;
            $params[$paramName] = $payload[$field];
        }

        return [
            'sql' => 'INSERT INTO ' . $this->quoteIdentifier($table) . ' (' . $columns . ') VALUES (' . implode(', ', $placeholders) . ')',
            'params' => $params,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildUpdateByPrimaryKey(string $table, array $payload, string $primaryKey, string $recordId): array
    {
        $setParts = [];
        $params = [];

        foreach ($payload as $field => $value) {
            $paramName = 'update_' . $field;
            $setParts[] = $this->quoteIdentifier($field) . ' = :' . $paramName;
            $params[$paramName] = $value;
        }

        $params['primary_key_value'] = $recordId;

        return [
            'sql' => 'UPDATE ' . $this->quoteIdentifier($table) .
                ' SET ' . implode(', ', $setParts) .
                ' WHERE ' . $this->quoteIdentifier($primaryKey) . ' = :primary_key_value' .
                ' LIMIT 1',
            'params' => $params,
        ];
    }

    /**
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildDeleteByPrimaryKey(string $table, string $primaryKey, string $recordId): array
    {
        return [
            'sql' => 'DELETE FROM ' . $this->quoteIdentifier($table) .
                ' WHERE ' . $this->quoteIdentifier($primaryKey) . ' = :primary_key_value',
            'params' => [
                'primary_key_value' => $recordId,
            ],
        ];
    }

    /**
     * @return array{sql:string, params:array<string, mixed>}
     */
    public function buildExistsByPrimaryKey(string $table, string $primaryKey, string $recordId): array
    {
        return [
            'sql' => 'SELECT 1 FROM ' . $this->quoteIdentifier($table) .
                ' WHERE ' . $this->quoteIdentifier($primaryKey) . ' = :primary_key_value LIMIT 1',
            'params' => [
                'primary_key_value' => $recordId,
            ],
        ];
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function quoteQualifiedIdentifier(string $table, string $column): string
    {
        return $this->quoteIdentifier($table) . '.' . $this->quoteIdentifier($column);
    }

    /**
     * @param array<string, mixed> $join
     * @param array<string, mixed> $params
     */
    private function buildJoinClause(array $join, array &$params, int &$joinParamCounter): string
    {
        if (isset($join['conditions']) && is_array($join['conditions']) && $join['conditions'] !== []) {
            return $this->buildStructuredJoinClause($join, $params, $joinParamCounter);
        }

        $joinTable = trim((string) ($join['to_table'] ?? ''));
        $sourceTable = trim((string) ($join['source_table'] ?? ''));
        $sourceColumn = trim((string) ($join['source_column'] ?? ''));
        $targetTable = trim((string) ($join['target_table'] ?? ''));
        $targetColumn = trim((string) ($join['target_column'] ?? ''));

        if ($joinTable === '' || $sourceTable === '' || $sourceColumn === '' || $targetTable === '' || $targetColumn === '') {
            return '';
        }

        return ' LEFT JOIN ' . $this->quoteIdentifier($joinTable) .
            ' ON ' . $this->quoteQualifiedIdentifier($sourceTable, $sourceColumn) .
            ' = ' . $this->quoteQualifiedIdentifier($targetTable, $targetColumn);
    }

    /**
     * @param array<string, mixed> $join
     * @param array<string, mixed> $params
     */
    private function buildStructuredJoinClause(array $join, array &$params, int &$joinParamCounter): string
    {
        $joinTable = trim((string) ($join['to_table'] ?? $join['table'] ?? ''));
        if ($joinTable === '') {
            return '';
        }

        $joinType = strtoupper(trim((string) ($join['join_type'] ?? 'LEFT')));
        if ($joinType === '') {
            $joinType = 'LEFT';
        }

        $conditions = [];
        foreach ((array) ($join['conditions'] ?? []) as $condition) {
            if (!is_array($condition)) {
                continue;
            }

            $leftTable = trim((string) (($condition['left']['table'] ?? $condition['left_table'] ?? '')));
            $leftColumn = trim((string) (($condition['left']['column'] ?? $condition['left_column'] ?? '')));
            $operator = $this->normalizeJoinOperator((string) ($condition['operator'] ?? ''));
            if ($leftTable === '' || $leftColumn === '' || $operator === '') {
                continue;
            }

            if ((string) ($condition['kind'] ?? '') === 'field') {
                $rightTable = trim((string) (($condition['right']['table'] ?? $condition['right_table'] ?? '')));
                $rightColumn = trim((string) (($condition['right']['column'] ?? $condition['right_column'] ?? '')));
                if ($rightTable === '' || $rightColumn === '') {
                    continue;
                }

                $conditions[] = $this->quoteQualifiedIdentifier($leftTable, $leftColumn) .
                    ' ' . $operator . ' ' .
                    $this->quoteQualifiedIdentifier($rightTable, $rightColumn);
                continue;
            }

            if ((string) ($condition['kind'] ?? '') === 'value') {
                $joinParamCounter++;
                $paramName = 'join_' . $joinParamCounter;
                $conditions[] = $this->quoteQualifiedIdentifier($leftTable, $leftColumn) .
                    ' ' . $operator . ' :' . $paramName;
                $params[$paramName] = $condition['value'] ?? null;
            }
        }

        if ($conditions === []) {
            return '';
        }

        return ' ' . $joinType . ' JOIN ' . $this->quoteIdentifier($joinTable) .
            ' ON (' . implode(' AND ', $conditions) . ')';
    }

    private function normalizeJoinOperator(string $operator): string
    {
        $normalized = trim($operator);
        if ($normalized === '!=') {
            return '<>';
        }

        if (in_array($normalized, ['=', '<>', '>', '>=', '<', '<='], true)) {
            return $normalized;
        }

        return '';
    }

    /**
     * @param array<int, array<string, mixed>> $filters
     * @param array<string, mixed> $params
     * @param array<string, array{table:string,column:string}> $fieldSources
     */
    private function buildWhere(array $filters, array &$params, array $fieldSources, string $baseTable): string
    {
        $clauses = [];
        $counter = 0;

        foreach ($filters as $filter) {
            $counter++;
            $fieldSource = $this->resolveFieldSource((string) $filter['field'], $fieldSources, $baseTable);
            $field = $this->quoteQualifiedIdentifier($fieldSource['table'], $fieldSource['column']);
            $operator = $filter['operator'];
            $paramBase = 'filter_' . $counter;

            switch ($operator) {
                case 'eq':
                    $clauses[] = $field . ' = :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'neq':
                    $clauses[] = $field . ' <> :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'gt':
                    $clauses[] = $field . ' > :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'gte':
                    $clauses[] = $field . ' >= :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'lt':
                    $clauses[] = $field . ' < :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'lte':
                    $clauses[] = $field . ' <= :' . $paramBase;
                    $params[$paramBase] = $filter['value'];
                    break;
                case 'like':
                    $clauses[] = $field . ' LIKE :' . $paramBase;
                    $params[$paramBase] = '%' . $filter['value'] . '%';
                    break;
                case 'in':
                    $placeholders = [];
                    foreach ($filter['value'] as $index => $value) {
                        $paramName = $paramBase . '_' . $index;
                        $placeholders[] = ':' . $paramName;
                        $params[$paramName] = $value;
                    }
                    $clauses[] = $field . ' IN (' . implode(', ', $placeholders) . ')';
                    break;
                case 'between':
                    $startParam = $paramBase . '_start';
                    $endParam = $paramBase . '_end';
                    $clauses[] = $field . ' BETWEEN :' . $startParam . ' AND :' . $endParam;
                    $params[$startParam] = $filter['value'][0];
                    $params[$endParam] = $filter['value'][1];
                    break;
                case 'isnull':
                    $clauses[] = $field . ($filter['value'] ? ' IS NULL' : ' IS NOT NULL');
                    break;
            }
        }

        return implode(' AND ', $clauses);
    }

    /**
     * @param array<string, mixed> $fieldSources
     * @return array<string, array{table:string,column:string}>
     */
    private function normalizeFieldSources(array $fieldSources, string $baseTable): array
    {
        $normalized = [];

        foreach ($fieldSources as $fieldName => $fieldSource) {
            if (!is_string($fieldName) || !is_array($fieldSource)) {
                continue;
            }

            $table = trim((string) ($fieldSource['table'] ?? ''));
            $column = trim((string) ($fieldSource['column'] ?? ''));
            if ($table === '' || $column === '') {
                continue;
            }

            $normalized[$fieldName] = [
                'table' => $table,
                'column' => $column,
            ];
        }

        if ($baseTable !== '' && !isset($normalized[$baseTable . '_Id'])) {
            $normalized[$baseTable . '_Id'] = [
                'table' => $baseTable,
                'column' => $baseTable . '_Id',
            ];
        }

        return $normalized;
    }

    /**
     * @param array<string, array{table:string,column:string}> $fieldSources
     * @return array{table:string,column:string}
     */
    private function resolveFieldSource(string $fieldName, array $fieldSources, string $baseTable): array
    {
        if (isset($fieldSources[$fieldName])) {
            return $fieldSources[$fieldName];
        }

        return [
            'table' => $baseTable,
            'column' => $fieldName,
        ];
    }
}
