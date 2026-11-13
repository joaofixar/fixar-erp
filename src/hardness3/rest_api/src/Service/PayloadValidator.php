<?php

declare(strict_types=1);

namespace RestApi\Service;

use RestApi\Core\ApiException;
use RestApi\Security\FieldPolicy;

class PayloadValidator
{
    private FieldPolicy $fieldPolicy;

    public function __construct(FieldPolicy $fieldPolicy)
    {
        $this->fieldPolicy = $fieldPolicy;
    }

    /**
     * @param array<string, mixed>|null $payload
     * @param array<string, mixed> $schema
     * @param array<int, string> $allowedFields
     * @return array<string, mixed>
     */
    public function validateForInsert(?array $payload, array $schema, array $allowedFields = []): array
    {
        $normalizedPayload = $this->normalizePayload($payload);
        $fieldMap = $this->buildFieldMap($schema);
        $allowedFieldSet = $this->buildAllowedFieldSet($allowedFields, $fieldMap);

        foreach ($normalizedPayload as $fieldName => $value) {
            $field = $this->assertFieldWritable($fieldName, $fieldMap, $allowedFieldSet);
            if (($field['is_primary_key'] ?? false) && ($field['is_auto_increment'] ?? false)) {
                throw new ApiException('FIELD_BLOCKED', 'Campo bloqueado.', 400, ['field' => $fieldName]);
            }
            $normalizedPayload[$fieldName] = $this->normalizeValueForField($field, $value);
        }

        return $normalizedPayload;
    }

    /**
     * @param array<string, mixed>|null $payload
     * @param array<string, mixed> $schema
     * @param array<int, string> $allowedFields
     * @return array<string, mixed>
     */
    public function validateForUpdate(?array $payload, array $schema, array $allowedFields = []): array
    {
        $normalizedPayload = $this->normalizePayload($payload);
        $fieldMap = $this->buildFieldMap($schema);
        $allowedFieldSet = $this->buildAllowedFieldSet($allowedFields, $fieldMap);
        $primaryKey = (string) ($schema['primary_key'] ?? '');

        foreach ($normalizedPayload as $fieldName => $value) {
            $field = $this->assertFieldWritable($fieldName, $fieldMap, $allowedFieldSet);
            if ($fieldName === $primaryKey || ($field['is_primary_key'] ?? false)) {
                throw new ApiException('PRIMARY_KEY_CHANGE_NOT_ALLOWED', 'Chave primária não pode ser alterada.', 400, [
                    'field' => $fieldName,
                ]);
            }
            $normalizedPayload[$fieldName] = $this->normalizeValueForField($field, $value);
        }

        return $normalizedPayload;
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(?array $payload): array
    {
        if ($payload === null || $payload === []) {
            throw new ApiException('INVALID_PAYLOAD', 'Payload vazio.', 400);
        }

        if (array_keys($payload) === range(0, count($payload) - 1)) {
            throw new ApiException('INVALID_PAYLOAD', 'Payload inválido.', 400);
        }

        return $payload;
    }

    /**
     * @param array<string, array<string, mixed>> $fieldMap
     * @param array<string, bool> $allowedFieldSet
     * @return array<string, mixed>
     */
    private function assertFieldWritable(string $fieldName, array $fieldMap, array $allowedFieldSet): array
    {
        if (!isset($fieldMap[$fieldName])) {
            throw new ApiException('FIELD_NOT_FOUND', 'Campo não encontrado.', 400, ['field' => $fieldName]);
        }

        if ($allowedFieldSet !== [] && !isset($allowedFieldSet[$fieldName])) {
            throw new ApiException('FIELD_NOT_ALLOWED', 'Campo não permitido.', 403, ['field' => $fieldName]);
        }

        if (!$this->fieldPolicy->isFieldWritable($fieldName)) {
            throw new ApiException('FIELD_BLOCKED', 'Campo bloqueado.', 400, ['field' => $fieldName]);
        }

        return $fieldMap[$fieldName];
    }

    /**
     * @param array<string, mixed> $field
     * @param mixed $value
     */
    private function normalizeValueForField(array $field, $value)
    {
        $fieldName = (string) ($field['name'] ?? '');
        $dataType = strtolower((string) ($field['data_type'] ?? ''));
        $nullable = (bool) ($field['nullable'] ?? false);
        $maxLength = $field['max_length'] ?? null;

        if ($value === null) {
            if (!$nullable) {
                throw new ApiException('INVALID_PAYLOAD', 'Campo obrigatório não pode ser nulo.', 400, [
                    'field' => $fieldName,
                ]);
            }
            return null;
        }

        if (is_array($value) || is_object($value)) {
            if ($dataType === 'json') {
                $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($encoded === false) {
                    throw new ApiException('INVALID_PAYLOAD', 'JSON inválido para o campo.', 400, [
                        'field' => $fieldName,
                    ]);
                }
                return $encoded;
            }
            throw new ApiException('INVALID_PAYLOAD', 'Tipo de campo inválido.', 400, [
                'field' => $fieldName,
            ]);
        }

        if ($this->isIntegerType($dataType) && filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ApiException('INVALID_PAYLOAD', 'Tipo de campo inválido.', 400, ['field' => $fieldName]);
        }

        if ($this->isNumericType($dataType) && !is_numeric($value)) {
            throw new ApiException('INVALID_PAYLOAD', 'Tipo de campo inválido.', 400, ['field' => $fieldName]);
        }

        if ($this->isStringType($dataType)) {
            $stringValue = (string) $value;
            if ($maxLength !== null && mb_strlen($stringValue) > (int) $maxLength) {
                throw new ApiException('INVALID_PAYLOAD', 'Campo excede o tamanho máximo.', 400, [
                    'field' => $fieldName,
                ]);
            }
        }

        if (in_array($dataType, ['date', 'datetime', 'timestamp', 'time'], true) && !is_string($value)) {
            throw new ApiException('INVALID_PAYLOAD', 'Tipo de campo inválido.', 400, ['field' => $fieldName]);
        }

        return $value;
    }

    private function isIntegerType(string $dataType): bool
    {
        return in_array($dataType, ['int', 'integer', 'bigint', 'smallint', 'mediumint', 'tinyint'], true);
    }

    private function isNumericType(string $dataType): bool
    {
        return $this->isIntegerType($dataType) || in_array($dataType, ['decimal', 'numeric', 'float', 'double', 'real'], true);
    }

    private function isStringType(string $dataType): bool
    {
        return in_array($dataType, ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext'], true);
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
        if ($allowedFields === []) {
            return [];
        }

        $allowedFieldSet = [];
        foreach ($allowedFields as $fieldName) {
            if (!is_string($fieldName) || !isset($fieldMap[$fieldName])) {
                continue;
            }

            $allowedFieldSet[$fieldName] = true;
        }

        return $allowedFieldSet;
    }
}
