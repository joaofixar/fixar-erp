<?php

declare(strict_types=1);

namespace RestApi\Security;

class AccessPolicyService
{
    private AccessPolicyRepository $repository;

    /** @var array<string, string> */
    private array $tenantContext = [];

    public function __construct(AccessPolicyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array<string, string> $tenantContext
     */
    public function forTenant(array $tenantContext): self
    {
        $clone = clone $this;
        $clone->tenantContext = $tenantContext;

        return $clone;
    }

    public function isTableAllowed(string $table): bool
    {
        $policy = $this->getPolicy();
        $normalizedTable = strtoupper(trim($table));

        return $normalizedTable !== '' && isset($policy[$normalizedTable]);
    }

    public function isFieldAllowed(string $table, string $field, array $allSchemaFields = []): bool
    {
        $allowedFields = $this->getAllowedFields($table, $allSchemaFields);
        return in_array($field, $allowedFields, true);
    }

    public function isReadableFieldAllowed(string $table, string $field, array $allSchemaFields = [], ?string $primaryKey = null): bool
    {
        $allowedFields = $this->getReadableFields($table, $allSchemaFields, $primaryKey);
        return in_array($field, $allowedFields, true);
    }

    /**
     * @return array<int, string>
     */
    public function getAllowedTables(): array
    {
        return array_keys($this->getPolicy());
    }

    /**
     * @param array<int, mixed> $allSchemaFields
     * @return array<int, string>
     */
    public function getAllowedFields(string $table, array $allSchemaFields): array
    {
        $policy = $this->getPolicy();
        $normalizedTable = strtoupper(trim($table));

        if ($normalizedTable === '' || !isset($policy[$normalizedTable])) {
            return [];
        }

        $schemaFields = $this->normalizeSchemaFields($allSchemaFields);
        if ($schemaFields === []) {
            return [];
        }

        $rule = $policy[$normalizedTable];
        $mode = (string) ($rule['mode'] ?? '');

        if ($mode === 'all') {
            return $schemaFields;
        }

        $configuredFields = array_values(array_unique(array_map('strval', (array) ($rule['fields'] ?? []))));

        if ($mode === 'block') {
            return array_values(array_filter($schemaFields, static function (string $field) use ($configuredFields): bool {
                return !in_array($field, $configuredFields, true);
            }));
        }

        if ($mode === 'allow') {
            return array_values(array_filter($schemaFields, static function (string $field) use ($configuredFields): bool {
                return in_array($field, $configuredFields, true);
            }));
        }

        return [];
    }

    /**
     * @param array<int, mixed> $allSchemaFields
     * @return array<int, string>
     */
    public function getReadableFields(string $table, array $allSchemaFields, ?string $primaryKey = null): array
    {
        $allowedFields = $this->getAllowedFields($table, $allSchemaFields);
        $normalizedPrimaryKey = is_string($primaryKey) ? trim($primaryKey) : '';

        if ($normalizedPrimaryKey === '') {
            return $allowedFields;
        }

        $schemaFields = $this->normalizeSchemaFields($allSchemaFields);
        if (!in_array($normalizedPrimaryKey, $schemaFields, true)) {
            return $allowedFields;
        }

        if (in_array($normalizedPrimaryKey, $allowedFields, true)) {
            return $allowedFields;
        }

        $allowedFields[] = $normalizedPrimaryKey;

        return array_values(array_unique($allowedFields));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getPolicy(): array
    {
        if ($this->tenantContext === []) {
            return [];
        }

        return $this->repository->load($this->tenantContext);
    }

    /**
     * @param array<int, mixed> $allSchemaFields
     * @return array<int, string>
     */
    private function normalizeSchemaFields(array $allSchemaFields): array
    {
        $normalized = [];

        foreach ($allSchemaFields as $field) {
            if (is_string($field)) {
                $fieldName = trim($field);
            } elseif (is_array($field) && isset($field['name']) && is_string($field['name'])) {
                $fieldName = trim($field['name']);
            } else {
                continue;
            }

            if ($fieldName === '') {
                continue;
            }

            $normalized[] = $fieldName;
        }

        return array_values(array_unique($normalized));
    }
}
