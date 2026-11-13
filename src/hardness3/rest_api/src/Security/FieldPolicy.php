<?php

declare(strict_types=1);

namespace RestApi\Security;

class FieldPolicy
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

    /**
     * @param array<string, mixed> $field
     */
    public function isSchemaVisible(array $field): bool
    {
        return !$this->matchesPatterns((string) ($field['name'] ?? ''), (array) ($this->config['blocked_field_patterns'] ?? []));
    }

    public function isFieldReadable(string $fieldName): bool
    {
        return !$this->matchesPatterns($fieldName, (array) ($this->config['blocked_field_patterns'] ?? []));
    }

    public function isFieldWritable(string $fieldName): bool
    {
        return !$this->matchesPatterns($fieldName, (array) ($this->config['blocked_field_patterns'] ?? []));
    }

    public function isFilterAllowed(string $fieldName): bool
    {
        return !$this->matchesPatterns($fieldName, (array) ($this->config['blocked_filter_field_patterns'] ?? []));
    }

    public function isSortAllowed(string $fieldName): bool
    {
        return !$this->matchesPatterns($fieldName, (array) ($this->config['blocked_sort_field_patterns'] ?? []));
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<int, string> $allowedFields
     * @return array<int, array<string, mixed>>
     */
    public function filterSchemaFields(array $fields, array $allowedFields): array
    {
        if ($allowedFields === []) {
            return [];
        }

        $visibleFields = [];
        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }

            $fieldName = (string) ($field['name'] ?? '');
            if ($fieldName === '') {
                continue;
            }

            if (
                !$this->isSchemaVisible($field)
                || !in_array($fieldName, $allowedFields, true)
            ) {
                continue;
            }

            $visibleFields[] = $field;
        }

        return $visibleFields;
    }

    /**
     * @param array<int, string> $patterns
     */
    private function matchesPatterns(string $value, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (@preg_match((string) $pattern, $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
