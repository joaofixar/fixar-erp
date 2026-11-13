<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

/** Temporary, secret-free request trace for diagnosing MCP interoperability. */
final class HttpDiagnostic
{
    public static function record(bool $enabled, Request $request, Response $response, float $startedAt, string $outcome, ?string $safeCode = null): void
    {
        if (!$enabled) return;
        $responseHeaders = $response->getHeaders();
        $event = [
            'event' => 'hardness_mcp_http_diagnostic', 'timestamp' => gmdate('c'), 'correlation_id' => OperationalTrace::correlationId(),
            'host' => self::headerValue($request->getHost()), 'method' => $request->getMethod(), 'route' => $request->getPath(),
            'http_status' => $response->getStatus(), 'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            'content_type' => self::headerValue($request->getHeader('content-type')), 'accept' => self::headerValue($request->getHeader('accept')),
            'authorization_present' => $request->getHeader('authorization') !== null,
            'mcp_protocol_version' => self::headerValue($request->getHeader('mcp-protocol-version')), 'outcome' => $outcome,
            'response_content_type' => self::headerValue($responseHeaders['Content-Type'] ?? null),
            'response_mcp_protocol_version' => self::headerValue($responseHeaders['MCP-Protocol-Version'] ?? null),
            'www_authenticate' => self::headerValue($responseHeaders['WWW-Authenticate'] ?? null),
            'response_has_body' => $response->getPayload() !== null,
        ];
        if ($safeCode !== null) $event['safe_code'] = $safeCode;
        if ($request->getPath() === '/mcp') $event = array_merge($event, self::jsonRpcSummary($request->getBody()));
        error_log((string) json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /** @return array<string,mixed> */
    private static function jsonRpcSummary(string $body): array
    {
        if (strlen($body) > 1048576) return ['jsonrpc' => 'body_too_large'];
        try { $payload = json_decode($body, true, 32, JSON_THROW_ON_ERROR); } catch (\JsonException $exception) { return ['jsonrpc' => 'invalid_json']; }
        if (!is_array($payload) || array_keys($payload) === range(0, count($payload) - 1)) return ['jsonrpc' => 'invalid_request'];
        $summary = [];
        if (isset($payload['method']) && is_string($payload['method'])) {
            $summary['jsonrpc_method'] = preg_match('/\A[A-Za-z0-9._\/-]{1,100}\z/', $payload['method']) === 1 ? $payload['method'] : 'method_sha256:' . substr(hash('sha256', $payload['method']), 0, 16);
        }
        if (array_key_exists('id', $payload)) {
            $summary['jsonrpc_id'] = is_int($payload['id']) || is_float($payload['id']) ? $payload['id'] : (is_string($payload['id']) ? 'id_sha256:' . substr(hash('sha256', $payload['id']), 0, 16) : gettype($payload['id']));
        }
        return $summary;
    }

    private static function headerValue(?string $value): ?string
    {
        if ($value === null) return null;
        if (strlen($value) > 256 || preg_match('/\A[\x20-\x7E]*\z/', $value) !== 1) return '[invalid-or-truncated]';
        return $value;
    }
}
