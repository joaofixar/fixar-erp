<?php

declare(strict_types=1);

namespace RestApi\Service;

use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Security\FieldPolicy;

class QueryValidator
{
    /** @var array<string, mixed> */
    private array $appConfig;

    /** @var array<string, mixed> */
    private array $blocklistConfig;

    private FieldPolicy $fieldPolicy;

    /**
     * @param array<string, mixed> $appConfig
     * @param array<string, mixed> $blocklistConfig
     */
    public function __construct(array $appConfig, array $blocklistConfig, FieldPolicy $fieldPolicy)
    {
        $this->appConfig = $appConfig;
        $this->blocklistConfig = $blocklistConfig;
        $this->fieldPolicy = $fieldPolicy;
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<int, string> $allowedFields
     * @return array<string, mixed>
     */
    public function validateListQuery(Request $request, array $schema, array $allowedFields = []): array
    {
        $query = $request->getQueryParams();
        $fieldMap = $this->buildFieldMap($schema);
        $allowedFieldSet = $this->buildAllowedFieldSet($allowedFields, $fieldMap);

        $selectedFields = $this->validateFields($query['fields'] ?? null, $fieldMap, $allowedFieldSet);
        $sorts = $this->validateSort($query['sort'] ?? null, $fieldMap, $allowedFieldSet);
        $filters = $this->validateFilters($query['filter'] ?? [], $fieldMap, $allowedFieldSet);
        $pagination = $this->validatePagination($query);

        return [
            'fields' => $selectedFields,
            'sorts' => $sorts,
            'filters' => $filters,
            'limit' => $pagination['limit'],
            'offset' => $pagination['offset'],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $joins
     * @param array<string, mixed> $schema
     * @param array<int, string> $allowedFields
     * @return array<int, array<string, mixed>>
     */
    public function validateJoinOverrides(array $joins, array $schema, array $allowedFields = []): array
    {
        if ($joins === []) {
            return [];
        }

        $fieldMap = $this->buildFieldMap($schema);
        $allowedFieldSet = $this->buildAllowedFieldSet($allowedFields, $fieldMap);
        $validated = [];

        foreach ($joins as $index => $join) {
            if (!is_array($join)) {
                throw new ApiException('INVALID_JOINS', 'Parâmetro joins inválido.', 400, [
                    'join_index' => $index,
                ]);
            }

            $table = trim((string) ($join['table'] ?? ''));
            if ($table === '') {
                throw new ApiException('INVALID_JOINS', 'Parâmetro joins inválido.', 400, [
                    'join_index' => $index,
                ]);
            }

            $type = strtoupper(trim((string) ($join['type'] ?? 'LEFT')));
            if ($type !== 'LEFT') {
                throw new ApiException('INVALID_JOINS', 'Tipo de join inválido.', 400, [
                    'join_index' => $index,
                    'table' => $table,
                    'type' => $type,
                ]);
            }

            $conditions = $join['conditions'] ?? null;
            if (!is_array($conditions) || $conditions === []) {
                throw new ApiException('INVALID_JOINS', 'Condições de join inválidas.', 400, [
                    'join_index' => $index,
                    'table' => $table,
                ]);
            }

            $validatedConditions = [];
            foreach ($conditions as $conditionIndex => $condition) {
                if (!is_array($condition)) {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $index,
                        'condition_index' => $conditionIndex,
                        'table' => $table,
                    ]);
                }

                $leftField = trim((string) ($condition['leftField'] ?? ''));
                if ($leftField === '') {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $index,
                        'condition_index' => $conditionIndex,
                        'table' => $table,
                    ]);
                }

                $operator = trim((string) ($condition['operator'] ?? ''));
                if (!in_array($operator, ['=', '!=', '>', '>=', '<', '<='], true)) {
                    throw new ApiException('INVALID_JOINS', 'Operador de join inválido.', 400, [
                        'join_index' => $index,
                        'condition_index' => $conditionIndex,
                        'table' => $table,
                        'operator' => $operator,
                    ]);
                }

                $left = $this->resolveReadableField($leftField, $fieldMap, $allowedFieldSet);
                $hasRightField = array_key_exists('rightField', $condition);
                $hasValue = array_key_exists('value', $condition);
                if ($hasRightField === $hasValue) {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $index,
                        'condition_index' => $conditionIndex,
                        'table' => $table,
                    ]);
                }

                if ($hasRightField) {
                    $rightField = trim((string) $condition['rightField']);
                    if ($rightField === '') {
                        throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                            'join_index' => $index,
                            'condition_index' => $conditionIndex,
                            'table' => $table,
                        ]);
                    }

                    $right = $this->resolveReadableField($rightField, $fieldMap, $allowedFieldSet);
                    $validatedConditions[] = [
                        'kind' => 'field',
                        'operator' => $operator,
                        'left_field' => $leftField,
                        'left_table' => $left['table'],
                        'left_column' => $left['column'],
                        'right_field' => $rightField,
                        'right_table' => $right['table'],
                        'right_column' => $right['column'],
                    ];
                    continue;
                }

                $value = $condition['value'];
                if (!is_scalar($value)) {
                    throw new ApiException('INVALID_JOINS', 'Condição de join inválida.', 400, [
                        'join_index' => $index,
                        'condition_index' => $conditionIndex,
                        'table' => $table,
                    ]);
                }

                $validatedConditions[] = [
                    'kind' => 'value',
                    'operator' => $operator,
                    'left_field' => $leftField,
                    'left_table' => $left['table'],
                    'left_column' => $left['column'],
                    'value' => $value,
                ];
            }

            $validated[] = [
                'table' => $table,
                'type' => $type,
                'conditions' => $validatedConditions,
            ];
        }

        return $validated;
    }

    /**
     * @param array<string, mixed> $schema
     */
    public function validatePrimaryKeyLookup(array $schema, string $recordId): string
    {
        if ($recordId === '') {
            throw new ApiException('PRIMARY_KEY_REQUIRED', 'Chave primária não informada.', 400);
        }

        if (($schema['primary_key'] ?? null) === null) {
            throw new ApiException('PRIMARY_KEY_REQUIRED', 'Tabela sem chave primária suportada.', 400);
        }

        return $recordId;
    }

    /**
     * @param array<string, array<string, mixed>> $fieldMap
     * @param array<string, bool> $allowedFieldSet
     * @return array<int, string>
     */
    private function validateFields($fieldsParam, array $fieldMap, array $allowedFieldSet): array
    {
        if ($fieldsParam === null || trim((string) $fieldsParam) === '') {
            return array_keys($allowedFieldSet);
        }

        $fields = array_filter(array_map('trim', explode(',', (string) $fieldsParam)), static function (string $value): bool {
            return $value !== '';
        });

        if ($fields === []) {
            throw new ApiException('INVALID_FIELDS', 'Parâmetro fields inválido.', 400);
        }

        $validated = [];
        foreach ($fields as $field) {
            if (!isset($fieldMap[$field])) {
                throw new ApiException('FIELD_NOT_FOUND', 'Campo não encontrado.', 400, ['field' => $field]);
            }

            if (!isset($allowedFieldSet[$field])) {
                throw new ApiException('FIELD_NOT_ALLOWED', 'Campo não permitido.', 403, ['field' => $field]);
            }

            $validated[] = $field;
        }

        return array_values(array_unique($validated));
    }

    /**
     * @param array<string, array<string, mixed>> $fieldMap
     * @param array<string, bool> $allowedFieldSet
     * @return array<int, array<string, string>>
     */
    private function validateSort($sortParam, array $fieldMap, array $allowedFieldSet): array
    {
        if ($sortParam === null || trim((string) $sortParam) === '') {
            return [];
        }

        $sorts = array_filter(array_map('trim', explode(',', (string) $sortParam)), static function (string $value): bool {
            return $value !== '';
        });

        $validated = [];
        foreach ($sorts as $sort) {
            $direction = 'ASC';
            $field = $sort;

            if (strpos($sort, '-') === 0) {
                $direction = 'DESC';
                $field = substr($sort, 1);
            }

            if ($field === '') {
                throw new ApiException('INVALID_SORT', 'Campo de ordenação inválido.', 400, ['field' => $field]);
            }

            if (!isset($fieldMap[$field])) {
                throw new ApiException('FIELD_NOT_FOUND', 'Campo de ordenação não encontrado.', 400, ['field' => $field]);
            }

            if (!isset($allowedFieldSet[$field])) {
                throw new ApiException('FIELD_NOT_ALLOWED', 'Campo não permitido.', 403, ['field' => $field]);
            }

            if (!$this->fieldPolicy->isSortAllowed($field)) {
                throw new ApiException('FIELD_BLOCKED', 'Campo bloqueado para ordenação.', 400, ['field' => $field]);
            }

            $validated[] = [
                'field' => $field,
                'direction' => $direction,
            ];
        }

        return $validated;
    }

    /**
     * @param mixed $filterParam
     * @param array<string, array<string, mixed>> $fieldMap
     * @param array<string, bool> $allowedFieldSet
     * @return array<int, array<string, mixed>>
     */
    private function validateFilters($filterParam, array $fieldMap, array $allowedFieldSet): array
    {
        if (!is_array($filterParam) || $filterParam === []) {
            return [];
        }

        $allowedOperators = array_map('strtolower', (array) ($this->blocklistConfig['allowed_filter_operators'] ?? []));
        $maxInValues = (int) (($this->appConfig['limits']['max_in_values'] ?? 100));
        $validated = [];

        foreach ($filterParam as $field => $operatorMap) {
            if (!is_string($field) || !isset($fieldMap[$field])) {
                throw new ApiException('FIELD_NOT_FOUND', 'Campo de filtro não encontrado.', 400, ['field' => $field]);
            }

            if (!isset($allowedFieldSet[$field])) {
                throw new ApiException('FIELD_NOT_ALLOWED', 'Campo não permitido.', 403, ['field' => $field]);
            }

            if (!$this->fieldPolicy->isFilterAllowed($field)) {
                throw new ApiException('FIELD_BLOCKED', 'Campo bloqueado para filtro.', 400, ['field' => $field]);
            }

            if (!is_array($operatorMap) || count($operatorMap) !== 1) {
                throw new ApiException('INVALID_FILTER', 'Filtro inválido.', 400, ['field' => $field]);
            }

            $operator = strtolower((string) array_key_first($operatorMap));
            $rawValue = $operatorMap[array_key_first($operatorMap)];

            if (!in_array($operator, $allowedOperators, true)) {
                throw new ApiException('INVALID_FILTER', 'Operador de filtro inválido.', 400, [
                    'field' => $field,
                    'operator' => $operator,
                ]);
            }

            if ($operator === 'in') {
                $values = array_values(array_filter(array_map('trim', explode(',', (string) $rawValue)), static function (string $value): bool {
                    return $value !== '';
                }));
                if ($values === []) {
                    throw new ApiException('INVALID_FILTER', 'Filtro IN inválido.', 400, ['field' => $field]);
                }
                if (count($values) > $maxInValues) {
                    throw new ApiException('INVALID_FILTER', 'Filtro IN excede o limite permitido.', 400, ['field' => $field]);
                }
                $validated[] = ['field' => $field, 'operator' => $operator, 'value' => $values];
                continue;
            }

            if ($operator === 'between') {
                $values = array_values(array_map('trim', explode(',', (string) $rawValue)));
                if (count($values) !== 2 || $values[0] === '' || $values[1] === '') {
                    throw new ApiException('INVALID_FILTER', 'Filtro BETWEEN inválido.', 400, ['field' => $field]);
                }
                $validated[] = ['field' => $field, 'operator' => $operator, 'value' => $values];
                continue;
            }

            if ($operator === 'isnull') {
                $normalized = filter_var($rawValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($normalized === null) {
                    $normalized = in_array((string) $rawValue, ['1', '0'], true) ? ((string) $rawValue === '1') : null;
                }
                if ($normalized === null) {
                    throw new ApiException('INVALID_FILTER', 'Filtro ISNULL inválido.', 400, ['field' => $field]);
                }
                $validated[] = ['field' => $field, 'operator' => $operator, 'value' => $normalized];
                continue;
            }

            $validated[] = ['field' => $field, 'operator' => $operator, 'value' => (string) $rawValue];
        }

        return $validated;
    }

    /**
     * @param array<string, mixed> $query
     * @return array{limit:int, offset:int}
     */
    private function validatePagination(array $query): array
    {
        $limits = (array) ($this->appConfig['limits'] ?? []);
        $defaultLimit = (int) ($limits['default_limit'] ?? 25);
        $maxLimit = (int) ($limits['max_limit'] ?? 100);
        $maxOffset = (int) ($limits['max_offset'] ?? 10000);

        $limit = isset($query['limit']) ? (int) $query['limit'] : $defaultLimit;
        $offset = isset($query['offset']) ? (int) $query['offset'] : 0;

        if ($limit <= 0 || $limit > $maxLimit) {
            throw new ApiException('INVALID_LIMIT', 'Limit inválido.', 400);
        }

        if ($offset < 0 || $offset > $maxOffset) {
            throw new ApiException('INVALID_OFFSET', 'Offset inválido.', 400);
        }

        return [
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, array<string, mixed>>
     */
    private function buildFieldMap(array $schema): array
    {
        $map = [];
        foreach ((array) ($schema['fields'] ?? []) as $field) {
            if (!is_array($field) || !isset($field['name'])) {
                continue;
            }
            $map[(string) $field['name']] = $field;
        }

        return $map;
    }

    /**
     * @param array<int, string> $allowedFields
     * @param array<string, array<string, mixed>> $fieldMap
     * @return array<string, bool>
     */
    private function buildAllowedFieldSet(array $allowedFields, array $fieldMap): array
    {
        $allowedFieldSet = [];

        if ($allowedFields === []) {
            foreach ($fieldMap as $fieldName => $field) {
                if ($this->fieldPolicy->isFieldReadable($fieldName)) {
                    $allowedFieldSet[$fieldName] = true;
                }
            }

            return $allowedFieldSet;
        }

        foreach ($allowedFields as $fieldName) {
            if (!is_string($fieldName) || !isset($fieldMap[$fieldName])) {
                continue;
            }

            if ($this->fieldPolicy->isFieldReadable($fieldName)) {
                $allowedFieldSet[$fieldName] = true;
            }
        }

        return $allowedFieldSet;
    }

    /**
     * @param array<string, array<string, mixed>> $fieldMap
     * @param array<string, bool> $allowedFieldSet
     * @return array{table:string,column:string}
     */
    private function resolveReadableField(string $fieldName, array $fieldMap, array $allowedFieldSet): array
    {
        if (!isset($fieldMap[$fieldName])) {
            throw new ApiException('FIELD_NOT_FOUND', 'Campo não encontrado.', 400, ['field' => $fieldName]);
        }

        if (!isset($allowedFieldSet[$fieldName])) {
            throw new ApiException('FIELD_NOT_ALLOWED', 'Campo não permitido.', 403, ['field' => $fieldName]);
        }

        $field = $fieldMap[$fieldName];
        $table = trim((string) ($field['table'] ?? ''));
        if ($table === '') {
            $separatorPosition = strpos($fieldName, '_');
            $table = $separatorPosition === false ? '' : substr($fieldName, 0, $separatorPosition);
        }

        return [
            'table' => $table,
            'column' => $fieldName,
        ];
    }
}
