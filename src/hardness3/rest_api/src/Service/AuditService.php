<?php

declare(strict_types=1);

namespace RestApi\Service;

use PDOException;
use RestApi\Core\ApiException;
use RestApi\Core\Request;
use Throwable;

class AuditService
{
    /** @var array<string, mixed> */
    private array $appConfig;

    /** @var array<string, mixed> */
    private array $blocklistConfig;

    /**
     * @param array<string, mixed> $appConfig
     * @param array<string, mixed> $blocklistConfig
     */
    public function __construct(array $appConfig, array $blocklistConfig)
    {
        $this->appConfig = $appConfig;
        $this->blocklistConfig = $blocklistConfig;
    }

    /**
     * @param object|null $connection
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    public function record($connection, Request $request, ?array $result, ?Throwable $exception, int $durationMs): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $authContext = $request->getAttribute('auth');
        if (!is_array($authContext) || !isset($authContext['A001_Id']) || !is_object($connection)) {
            return;
        }

        $tenantContext = $request->getAttribute('tenant', []);
        $row = [
            'A002_A001_Id' => (int) $authContext['A001_Id'],
            'A002_Request_Id' => (string) $request->getAttribute('request_id', ''),
            'A002_Host' => is_array($tenantContext) && isset($tenantContext['host']) ? (string) $tenantContext['host'] : $request->getHost(),
            'A002_IP' => $request->getIp(),
            'A002_Metodo_HTTP' => $request->getMethod(),
            'A002_Path' => $request->getPath(),
            'A002_Tabela' => $this->normalizeNullableString($request->getAttribute('audit_table')),
            'A002_Operacao' => $this->resolveOperation($request),
            'A002_Query_String' => $this->normalizeNullableString($request->getQueryString()),
            'A002_Payload_Mascarado' => $this->encodeMaskedPayload($request),
            'A002_Response_Resumo' => $this->encodeMaskedResponse($result, $exception),
            'A002_Status_Code' => $this->resolveStatusCode($result, $exception),
            'A002_Codigo_Erro' => $this->normalizeNullableString($this->resolveErrorCode($result, $exception)),
            'A002_Mensagem_Erro' => $this->normalizeNullableString($this->resolveErrorMessage($result, $exception)),
            'A002_Linhas_Afetadas' => $this->resolveAffectedRows($request, $result),
            'A002_Duracao_MS' => $durationMs,
        ];

        try {
            $statement = $connection->prepare(
                'INSERT INTO A002 (
                    A002_A001_Id,
                    A002_Request_Id,
                    A002_Host,
                    A002_IP,
                    A002_Metodo_HTTP,
                    A002_Path,
                    A002_Tabela,
                    A002_Operacao,
                    A002_Query_String,
                    A002_Payload_Mascarado,
                    A002_Response_Resumo,
                    A002_Status_Code,
                    A002_Codigo_Erro,
                    A002_Mensagem_Erro,
                    A002_Linhas_Afetadas,
                    A002_Duracao_MS,
                    A002_Data_Criacao
                ) VALUES (
                    :A002_A001_Id,
                    :A002_Request_Id,
                    :A002_Host,
                    :A002_IP,
                    :A002_Metodo_HTTP,
                    :A002_Path,
                    :A002_Tabela,
                    :A002_Operacao,
                    :A002_Query_String,
                    :A002_Payload_Mascarado,
                    :A002_Response_Resumo,
                    :A002_Status_Code,
                    :A002_Codigo_Erro,
                    :A002_Mensagem_Erro,
                    :A002_Linhas_Afetadas,
                    :A002_Duracao_MS,
                    NOW()
                )'
            );

            foreach ($row as $name => $value) {
                $statement->bindValue(':' . $name, $value);
            }

            $statement->execute();
        } catch (PDOException $pdoException) {
            $this->logAuditFailure('Falha ao gravar auditoria no banco.', [
                'exception' => $pdoException->getMessage(),
                'request_id' => $row['A002_Request_Id'],
                'path' => $row['A002_Path'],
            ]);
        } catch (Throwable $throwable) {
            $this->logAuditFailure('Falha inesperada ao gravar auditoria.', [
                'exception' => $throwable->getMessage(),
                'request_id' => $row['A002_Request_Id'],
                'path' => $row['A002_Path'],
            ]);
        }
    }

    private function isEnabled(): bool
    {
        return (bool) (($this->appConfig['audit']['enabled'] ?? false));
    }

    private function resolveOperation(Request $request): string
    {
        $operation = $request->getAttribute('audit_operation');
        if (is_string($operation) && trim($operation) !== '') {
            return trim($operation);
        }

        return strtolower($request->getMethod());
    }

    /**
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    private function resolveStatusCode(?array $result, ?Throwable $exception): int
    {
        if ($result !== null && isset($result['status'])) {
            return (int) $result['status'];
        }

        if ($exception instanceof ApiException) {
            return $exception->getCode();
        }

        $status = $exception !== null ? (int) $exception->getCode() : 500;
        return $status >= 400 && $status <= 599 ? $status : 500;
    }

    /**
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    private function resolveErrorCode(?array $result, ?Throwable $exception): ?string
    {
        if ($exception instanceof ApiException) {
            return $exception->getErrorCode();
        }

        if ($result !== null && isset($result['payload']['error']['code'])) {
            return (string) $result['payload']['error']['code'];
        }

        if ($exception !== null) {
            return 'INTERNAL_ERROR';
        }

        return null;
    }

    /**
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    private function resolveErrorMessage(?array $result, ?Throwable $exception): ?string
    {
        if ($exception instanceof ApiException) {
            return $exception->getMessage();
        }

        if ($result !== null && isset($result['payload']['error']['message'])) {
            return (string) $result['payload']['error']['message'];
        }

        if ($exception !== null) {
            return 'Internal server error.';
        }

        return null;
    }

    /**
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    private function resolveAffectedRows(Request $request, ?array $result): ?int
    {
        $value = $request->getAttribute('audit_affected_rows');
        if (is_int($value)) {
            return $value;
        }

        if ($result === null || !isset($result['payload']['data'])) {
            return null;
        }

        $data = $result['payload']['data'];
        if (is_array($data) && isset($data['affected_rows'])) {
            return (int) $data['affected_rows'];
        }

        if (is_array($data) && array_keys($data) === range(0, count($data) - 1)) {
            return count($data);
        }

        return is_array($data) && $data !== [] ? 1 : null;
    }

    private function encodeMaskedPayload(Request $request): ?string
    {
        $rawBody = $request->getRawBody();
        if ($rawBody === null || trim($rawBody) === '') {
            return null;
        }

        $decoded = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $this->truncateEncoded('"***INVALID_JSON***"', (int) (($this->appConfig['audit']['max_payload_bytes'] ?? 16384)));
        }

        return $this->truncateEncoded(
            $this->encodeJson($this->maskValue($decoded)),
            (int) (($this->appConfig['audit']['max_payload_bytes'] ?? 16384))
        );
    }

    /**
     * @param array{status:int,payload:array<string,mixed>}|null $result
     */
    private function encodeMaskedResponse(?array $result, ?Throwable $exception): ?string
    {
        if ($result !== null) {
            return $this->truncateEncoded(
                $this->encodeJson($this->maskValue($result['payload'])),
                (int) (($this->appConfig['audit']['max_response_bytes'] ?? 16384))
            );
        }

        if ($exception instanceof ApiException) {
            return $this->truncateEncoded(
                $this->encodeJson($this->maskValue([
                    'success' => false,
                    'error' => [
                        'code' => $exception->getErrorCode(),
                        'message' => $exception->getMessage(),
                        'details' => $exception->getDetails(),
                    ],
                ])),
                (int) (($this->appConfig['audit']['max_response_bytes'] ?? 16384))
            );
        }

        if ($exception !== null) {
            return $this->truncateEncoded(
                $this->encodeJson([
                    'success' => false,
                    'error' => [
                        'code' => 'INTERNAL_ERROR',
                        'message' => 'Internal server error.',
                    ],
                ]),
                (int) (($this->appConfig['audit']['max_response_bytes'] ?? 16384))
            );
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function maskValue($value, ?string $fieldName = null)
    {
        if ($fieldName !== null && $this->shouldMaskField($fieldName)) {
            return '***MASKED***';
        }

        if (!is_array($value)) {
            return $value;
        }

        $masked = [];
        foreach ($value as $key => $item) {
            $childFieldName = is_string($key) ? $key : null;
            $masked[$key] = $this->maskValue($item, $childFieldName);
        }

        return $masked;
    }

    private function shouldMaskField(string $fieldName): bool
    {
        foreach ((array) ($this->blocklistConfig['masked_field_patterns'] ?? $this->blocklistConfig['blocked_field_patterns'] ?? []) as $pattern) {
            if (@preg_match((string) $pattern, $fieldName) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     */
    private function encodeJson($value): ?string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $encoded === false ? null : $encoded;
    }

    /**
     * @param mixed $value
     */
    private function normalizeNullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);
        return $normalized === '' ? null : $normalized;
    }

    private function truncateEncoded(?string $value, int $maxBytes): ?string
    {
        if ($value === null || $maxBytes <= 0) {
            return $value;
        }

        if (strlen($value) <= $maxBytes) {
            return $value;
        }

        return substr($value, 0, max(0, $maxBytes - 16)) . '...[TRUNCATED]';
    }

    /**
     * @param array<string, mixed> $context
     */
    private function logAuditFailure(string $message, array $context = []): void
    {
        $logPath = (string) (($this->appConfig['audit']['log_path'] ?? ''));
        if ($logPath === '') {
            return;
        }

        $directory = dirname($logPath);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $line = sprintf(
            "[%s] %s %s\n",
            date('c'),
            $message,
            $this->encodeJson($context) ?? '{}'
        );

        @error_log($line, 3, $logPath);
    }
}
