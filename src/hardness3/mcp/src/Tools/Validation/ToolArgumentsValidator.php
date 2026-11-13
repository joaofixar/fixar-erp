<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Validation;

/**
 * Deliberately small, fail-closed JSON Schema validator for MCP tool arguments.
 * It validates the decoded value together with the raw JSON value so empty JSON
 * objects and arrays remain distinguishable.
 */
final class ToolArgumentsValidator
{
    private const MAX_DEPTH = 8;
    private const MAX_OBJECT_PROPERTIES = 100;
    private const MAX_STRING_BYTES = 8192;

    private int $maxArrayItems;
    private int $maxArgumentBytes;

    public function __construct(int $maxArrayItems = 100, int $maxArgumentBytes = 65536)
    {
        if ($maxArrayItems < 1 || $maxArgumentBytes < 1) throw new \InvalidArgumentException('Invalid tool argument limits.');
        $this->maxArrayItems = $maxArrayItems;
        $this->maxArgumentBytes = $maxArgumentBytes;
    }

    /** @param array<string,mixed> $arguments @param mixed $rawArguments @param array<string,mixed> $schema */
    public function isValid(array $arguments, $rawArguments, array $schema): bool
    {
        $encoded = json_encode($arguments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($encoded) || strlen($encoded) > $this->maxArgumentBytes) {
            return false;
        }

        return $this->isWithinStructuralLimits($rawArguments, 1)
            && $this->validateValue($arguments, $rawArguments, $schema, 1);
    }

    /** @param mixed $value @param mixed $rawValue @param array<string,mixed> $schema */
    private function validateValue($value, $rawValue, array $schema, int $depth): bool
    {
        if ($depth > self::MAX_DEPTH || !isset($schema['type']) || !is_string($schema['type'])) {
            return false;
        }

        if (!$this->hasOnlySupportedKeywords($schema)) { return false; }

        switch ($schema['type']) {
            case 'object': return $this->validateObject($value, $rawValue, $schema, $depth);
            case 'array': return $this->validateArray($value, $rawValue, $schema, $depth);
            case 'string': return $this->validateString($value, $schema);
            case 'integer': return $this->validateNumber($value, $schema, true);
            case 'number': return $this->validateNumber($value, $schema, false);
            case 'boolean': return is_bool($value) && $this->matchesEnum($value, $schema);
            case 'null': return $value === null && $this->matchesEnum($value, $schema);
            default: return false;
        }
    }

    /** @param mixed $value @param mixed $rawValue @param array<string,mixed> $schema */
    private function validateObject($value, $rawValue, array $schema, int $depth): bool
    {
        if (!$rawValue instanceof \stdClass || !is_array($value) || count(get_object_vars($rawValue)) > self::MAX_OBJECT_PROPERTIES) {
            return false;
        }
        $properties = $this->properties($schema['properties'] ?? []);
        if ($properties === null || !$this->requiredFieldsArePresent($rawValue, $schema['required'] ?? [], $properties)) {
            return false;
        }
        if (isset($schema['additionalProperties']) && !is_bool($schema['additionalProperties'])) {
            return false;
        }

        foreach (get_object_vars($rawValue) as $name => $rawChild) {
            if (!array_key_exists($name, $properties)) {
                if (($schema['additionalProperties'] ?? true) === false) { return false; }
                continue;
            }
            if (!array_key_exists($name, $value) || !$this->validateValue($value[$name], $rawChild, $properties[$name], $depth + 1)) {
                return false;
            }
        }
        return true;
    }

    /** @param mixed $value @param mixed $rawValue @param array<string,mixed> $schema */
    private function validateArray($value, $rawValue, array $schema, int $depth): bool
    {
        if (!is_array($rawValue) || !is_array($value) || !$this->isList($rawValue) || count($rawValue) > $this->maxArrayItems) {
            return false;
        }
        if (!$this->integerKeywordIsValid($schema, 'minItems', 0) || !$this->integerKeywordIsValid($schema, 'maxItems', 0)) {
            return false;
        }
        $count = count($rawValue);
        if ((isset($schema['minItems']) && $count < $schema['minItems']) || (isset($schema['maxItems']) && $count > $schema['maxItems'])) {
            return false;
        }
        if (!isset($schema['items']) || !is_array($schema['items'])) { return false; }
        foreach ($rawValue as $index => $rawChild) {
            if (!$this->validateValue($value[$index], $rawChild, $schema['items'], $depth + 1)) { return false; }
        }
        return true;
    }

    /** @param mixed $value @param array<string,mixed> $schema */
    private function validateString($value, array $schema): bool
    {
        if (!is_string($value) || strlen($value) > self::MAX_STRING_BYTES || !$this->integerKeywordIsValid($schema, 'minLength', 0) || !$this->integerKeywordIsValid($schema, 'maxLength', 0)) {
            return false;
        }
        $length = $this->stringLength($value);
        if ((isset($schema['minLength']) && $length < $schema['minLength']) || (isset($schema['maxLength']) && $length > $schema['maxLength'])) {
            return false;
        }
        return $this->matchesEnum($value, $schema);
    }

    /** @param mixed $value @param array<string,mixed> $schema */
    private function validateNumber($value, array $schema, bool $integer): bool
    {
        if ((!$integer && !is_int($value) && !is_float($value)) || ($integer && !is_int($value)) || !is_finite((float) $value)) {
            return false;
        }
        foreach (['minimum', 'maximum', 'exclusiveMinimum', 'exclusiveMaximum'] as $keyword) {
            if (isset($schema[$keyword]) && !is_int($schema[$keyword]) && !is_float($schema[$keyword])) { return false; }
        }
        if ((isset($schema['minimum']) && $value < $schema['minimum']) || (isset($schema['maximum']) && $value > $schema['maximum']) || (isset($schema['exclusiveMinimum']) && $value <= $schema['exclusiveMinimum']) || (isset($schema['exclusiveMaximum']) && $value >= $schema['exclusiveMaximum'])) {
            return false;
        }
        return $this->matchesEnum($value, $schema);
    }

    /** @param array<string,mixed> $schema */
    private function matchesEnum($value, array $schema): bool
    {
        if (!array_key_exists('enum', $schema)) { return true; }
        return is_array($schema['enum']) && $schema['enum'] !== [] && in_array($value, $schema['enum'], true);
    }

    /** @param mixed $properties @return array<string,array<string,mixed>>|null */
    private function properties($properties): ?array
    {
        if ($properties instanceof \stdClass) { $properties = get_object_vars($properties); }
        if (!is_array($properties) || ($properties !== [] && $this->isList($properties))) { return null; }
        foreach ($properties as $name => $schema) {
            if (!is_string($name) || $name === '' || !is_array($schema)) { return null; }
        }
        return $properties;
    }

    /** @param mixed $required @param array<string,array<string,mixed>> $properties */
    private function requiredFieldsArePresent(\stdClass $rawValue, $required, array $properties): bool
    {
        if (!is_array($required) || !$this->isList($required)) { return false; }
        foreach ($required as $name) {
            if (!is_string($name) || $name === '' || !array_key_exists($name, $properties) || !property_exists($rawValue, $name)) { return false; }
        }
        return true;
    }

    /** @param array<string,mixed> $schema */
    private function integerKeywordIsValid(array $schema, string $keyword, int $minimum): bool
    {
        return !isset($schema[$keyword]) || (is_int($schema[$keyword]) && $schema[$keyword] >= $minimum);
    }

    /** @param array<string,mixed> $schema */
    private function hasOnlySupportedKeywords(array $schema): bool
    {
        $allowed = [
            'object' => ['type', 'properties', 'required', 'additionalProperties'],
            'array' => ['type', 'items', 'minItems', 'maxItems'],
            'string' => ['type', 'minLength', 'maxLength', 'enum'],
            'integer' => ['type', 'minimum', 'maximum', 'exclusiveMinimum', 'exclusiveMaximum', 'enum'],
            'number' => ['type', 'minimum', 'maximum', 'exclusiveMinimum', 'exclusiveMaximum', 'enum'],
            'boolean' => ['type', 'enum'],
            'null' => ['type', 'enum'],
        ];
        if (!isset($allowed[$schema['type']])) { return false; }
        foreach (array_keys($schema) as $keyword) {
            if (!in_array($keyword, $allowed[$schema['type']], true)) { return false; }
        }
        return true;
    }

    /** @param mixed $rawValue */
    private function isWithinStructuralLimits($rawValue, int $depth): bool
    {
        if ($depth > self::MAX_DEPTH) { return false; }
        if (is_string($rawValue)) { return strlen($rawValue) <= self::MAX_STRING_BYTES; }
        if ($rawValue instanceof \stdClass) {
            $properties = get_object_vars($rawValue);
            if (count($properties) > self::MAX_OBJECT_PROPERTIES) { return false; }
            foreach ($properties as $child) {
                if (!$this->isWithinStructuralLimits($child, $depth + 1)) { return false; }
            }
            return true;
        }
        if (is_array($rawValue)) {
            if (!$this->isList($rawValue) || count($rawValue) > $this->maxArrayItems) { return false; }
            foreach ($rawValue as $child) {
                if (!$this->isWithinStructuralLimits($child, $depth + 1)) { return false; }
            }
        }
        return true;
    }

    /** @param array<mixed> $value */
    private function isList(array $value): bool { return $value === [] || array_keys($value) === range(0, count($value) - 1); }

    private function stringLength(string $value): int { return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value); }
}
