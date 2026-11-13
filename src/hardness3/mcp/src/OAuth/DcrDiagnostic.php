<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

/**
 * Opt-in, secret-free diagnostics for Dynamic Client Registration.
 *
 * This class must never receive or emit a raw HTTP body. It records only the
 * client metadata needed to investigate interoperability failures.
 */
final class DcrDiagnostic
{
    /** @return array<string,mixed> */
    public static function summarize($payload): array
    {
        if (!is_array($payload)) {
            return ['payload_type' => $payload === null ? 'unavailable' : gettype($payload)];
        }

        $summary = [
            'payload_type' => 'object',
            'fields' => self::fieldNames($payload),
        ];
        if (array_key_exists('grant_types', $payload)) $summary['grant_types'] = self::safeValues($payload['grant_types'], ['authorization_code', 'refresh_token', 'client_credentials', 'urn:ietf:params:oauth:grant-type:device_code']);
        if (array_key_exists('response_types', $payload)) $summary['response_types'] = self::safeValues($payload['response_types'], ['code', 'token', 'id_token']);
        if (array_key_exists('token_endpoint_auth_method', $payload)) $summary['token_endpoint_auth_method'] = self::safeValue($payload['token_endpoint_auth_method'], ['none', 'client_secret_basic', 'client_secret_post', 'private_key_jwt', 'tls_client_auth', 'self_signed_tls_client_auth']);
        if (array_key_exists('scope', $payload)) $summary['scopes'] = self::safeScopes($payload['scope']);
        if (array_key_exists('redirect_uris', $payload)) $summary['redirect_uris'] = self::redirectUris($payload['redirect_uris']);
        return $summary;
    }

    /**
     * Summarize the successful DCR response object immediately before it is
     * serialized. Values that could identify a client or redirect URI are
     * deliberately never included in this diagnostic.
     *
     * @return array<string,mixed>
     */
    public static function summarizeSuccessfulResponse($payload): array
    {
        $expectedFields = [
            'client_id',
            'client_id_issued_at',
            'client_name',
            'redirect_uris',
            'response_types',
            'grant_types',
            'token_endpoint_auth_method',
        ];
        $response = is_array($payload) ? $payload : [];
        $fieldNames = array_map('strval', array_keys($response));
        $unexpectedFieldCount = count(array_diff($fieldNames, $expectedFields));
        $hasExactFieldSet = count($fieldNames) === count($expectedFields)
            && $unexpectedFieldCount === 0
            && count(array_diff($expectedFields, $fieldNames)) === 0;

        $jsonSerializationOk = false;
        if (is_array($payload)) {
            try {
                $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                $jsonSerializationOk = true;
            } catch (\JsonException $exception) {
                $jsonSerializationOk = false;
            }
        }

        $responseTypes = $response['response_types'] ?? null;
        $grantTypes = $response['grant_types'] ?? null;
        $tokenEndpointAuthMethod = $response['token_endpoint_auth_method'] ?? null;
        $contractValid = is_array($payload)
            && $hasExactFieldSet
            && self::fieldType($response, 'client_id') === 'string'
            && self::fieldType($response, 'client_id_issued_at') === 'integer'
            && self::fieldType($response, 'client_name') === 'string'
            && self::isStringArray($response['redirect_uris'] ?? null)
            && $responseTypes === ['code']
            && $grantTypes === ['authorization_code', 'refresh_token']
            && $tokenEndpointAuthMethod === 'none'
            && !array_key_exists('scope', $response)
            && $jsonSerializationOk;

        return [
            'response_contract' => 'dcr_public_code_refresh_without_scope_v1',
            'contract_valid' => $contractValid,
            'field_count' => count($fieldNames),
            'json_serialization_ok' => $jsonSerializationOk,
            'client_id_type' => self::fieldType($response, 'client_id'),
            'client_id_issued_at_type' => self::fieldType($response, 'client_id_issued_at'),
            'redacted_registered_metadata_count' => array_key_exists('client_name', $response) ? 1 : 0,
            'redirect_uris' => self::redirectUris($response['redirect_uris'] ?? null),
            'grant_types' => self::safeValues($grantTypes, ['authorization_code', 'refresh_token']),
            'response_types' => self::safeValues($responseTypes, ['code']),
            'token_endpoint_auth_method' => self::safeValue($tokenEndpointAuthMethod, ['none']),
            'scope_present' => array_key_exists('scope', $response),
            'unexpected_field_count' => $unexpectedFieldCount,
        ];
    }

    /** @param array<string,mixed> $event */
    public static function record(bool $enabled, array $event): void
    {
        if (!$enabled) return;
        $event['event'] = 'hardness_mcp_oauth_dcr_diagnostic';
        error_log((string) json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /** @param array<string,mixed> $payload @return string[] */
    private static function fieldNames(array $payload): array
    {
        $names = [];
        foreach (array_keys($payload) as $field) {
            $field = (string) $field;
            $names[] = preg_match('/\A[A-Za-z][A-Za-z0-9_#-]{0,100}\z/', $field) === 1
                ? $field
                : 'field_sha256:' . substr(hash('sha256', $field), 0, 16);
        }
        sort($names, SORT_STRING);
        return $names;
    }

    /** @param array<string,mixed> $payload */
    private static function fieldType(array $payload, string $field): string
    {
        return array_key_exists($field, $payload) ? gettype($payload[$field]) : 'missing';
    }

    private static function isStringArray($value): bool
    {
        if (!is_array($value)) return false;
        foreach ($value as $item) if (!is_string($item)) return false;
        return true;
    }

    /** @return array<string,mixed> */
    private static function safeValues($value, array $allowed): array
    {
        if (!is_array($value)) return ['type' => gettype($value)];
        $values = [];
        foreach ($value as $item) $values[] = self::safeValue($item, $allowed);
        return ['type' => 'array', 'count' => count($value), 'values' => array_slice($values, 0, 20)];
    }

    /** @return array<string,string> */
    private static function safeValue($value, array $allowed): array
    {
        if (!is_string($value)) return ['type' => gettype($value)];
        if (in_array($value, $allowed, true)) return ['type' => 'string', 'value' => $value];
        return ['type' => 'string', 'value_sha256' => substr(hash('sha256', $value), 0, 16)];
    }

    /** @return array<string,mixed> */
    private static function safeScopes($value): array
    {
        if (!is_string($value)) return ['type' => gettype($value)];
        $parts = trim($value) === '' ? [] : preg_split('/ +/', trim($value));
        $values = [];
        foreach (is_array($parts) ? $parts : [] as $scope) $values[] = self::safeValue($scope, ['company:read', 'partner:search', 'partner:lookup', 'product:read', 'product:partner-reference:lookup', 'quote:read', 'quote:report:read', 'quote:write', 'quote:item:update', 'quote:item:delete', 'ide_smt:quote:order:generate', 'sales:read', 'order:report:read', 'finance:receivables:read', 'finance:payables:read']);
        return ['type' => 'string', 'count' => count($values), 'values' => array_slice($values, 0, 20)];
    }

    /** @return array<string,mixed> */
    private static function redirectUris($value): array
    {
        if (!is_array($value)) return ['type' => gettype($value)];
        $origins = [];
        $itemTypes = [];
        foreach ($value as $uri) {
            $type = gettype($uri);
            $itemTypes[$type] = ($itemTypes[$type] ?? 0) + 1;
            if (!is_string($uri) || count($origins) >= 10) continue;
            $parts = parse_url($uri);
            if (!is_array($parts) || !isset($parts['scheme'], $parts['host']) || isset($parts['user'], $parts['pass'])) {
                $origins[] = '[invalid-uri]';
                continue;
            }
            $origin = strtolower((string) $parts['scheme']) . '://' . strtolower((string) $parts['host']);
            if (isset($parts['port'])) $origin .= ':' . (int) $parts['port'];
            $origins[] = $origin;
        }
        return ['type' => 'array', 'count' => count($value), 'item_types' => $itemTypes, 'origins' => $origins];
    }
}
