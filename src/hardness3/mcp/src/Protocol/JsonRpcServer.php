<?php

declare(strict_types=1);

namespace HardnessMcp\Protocol;

use HardnessMcp\Core\McpException;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ToolExecutor;

class JsonRpcServer
{
    /** @var array<string, mixed> */
    private array $appConfig;
    private ToolExecutor $toolExecutor;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig, ToolExecutor $toolExecutor)
    {
        $this->appConfig = $appConfig;
        $this->toolExecutor = $toolExecutor;
    }

    /**
     * @return JsonRpcResult
     */
    public function handle(string $body, ?string $protocolVersionHeader, ExecutionContext $executionContext): JsonRpcResult
    {
        try {
            $request = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $rawRequest = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return JsonRpcResult::response($this->error(null, -32700, 'Parse error.'));
        }

        if (!is_array($request) || $this->isList($request)) {
            return JsonRpcResult::response($this->error(null, -32600, 'Invalid Request.'));
        }

        $isNotification = !array_key_exists('id', $request);
        $id = $request['id'] ?? null;
        if (($request['jsonrpc'] ?? null) !== '2.0' || !isset($request['method']) || !is_string($request['method'])) {
            return $isNotification
                ? JsonRpcResult::rejectedNotification()
                : JsonRpcResult::response($this->error(null, -32600, 'Invalid Request.'));
        }

        if ($id !== null && !is_int($id) && !is_string($id)) {
            return JsonRpcResult::response($this->error(null, -32600, 'Invalid Request.'));
        }

        if ($request['method'] !== 'initialize' && !$this->hasSupportedProtocolVersion($protocolVersionHeader)) {
            return $isNotification
                ? JsonRpcResult::rejectedNotification()
                : JsonRpcResult::response($this->error($id, -32600, 'Invalid protocol version.'), 400);
        }

        if ($request['method'] === 'notifications/initialized') {
            if ($isNotification && !isset($request['params'])) {
                return JsonRpcResult::acceptedNotification();
            }

            return $isNotification
                ? JsonRpcResult::rejectedNotification()
                : JsonRpcResult::response($this->error($id, -32602, 'Invalid notification parameters.'), 400);
        }

        if ($request['method'] === 'tools/list') {
            if ($isNotification) { return JsonRpcResult::rejectedNotification(); }
            if (isset($request['params']) && (!is_array($request['params']) || $this->isList($request['params']))) {
                return JsonRpcResult::response($this->error($id, -32602, 'Invalid tool list parameters.'), 200, 'tools_list_invalid_params');
            }
            return JsonRpcResult::response([
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => ['tools' => $this->toolExecutor->list($executionContext)],
            ], 200, 'tools_list');
        }

        if ($request['method'] === 'tools/call') {
            if ($isNotification) { return JsonRpcResult::rejectedNotification(); }
            try {
                $argumentsAreObject = $rawRequest instanceof \stdClass
                    && isset($rawRequest->params)
                    && $rawRequest->params instanceof \stdClass
                    && property_exists($rawRequest->params, 'arguments')
                    && $rawRequest->params->arguments instanceof \stdClass;
                $rawArguments = $argumentsAreObject ? $rawRequest->params->arguments : null;
                $execution = $this->toolExecutor->call($request['params'] ?? null, $executionContext, $argumentsAreObject, $rawArguments);
                $safeCode = $execution->getAuditSafeCode();
                if ($safeCode === null && is_array($request['params'] ?? null) && is_string($request['params']['name'] ?? null)) {
                    $safeCode = $request['params']['name'];
                }
                return JsonRpcResult::response(['jsonrpc' => '2.0', 'id' => $id, 'result' => $execution->getResult()], 200, $execution->getAuditEvent(), $safeCode);
            } catch (McpException $exception) {
                $event = $exception->getJsonRpcCode() === -32601 ? 'tools_call_not_found' : 'tools_call_invalid_params';
                return JsonRpcResult::response($this->error($id, $exception->getJsonRpcCode(), $exception->getMessage()), 200, $event);
            }
        }

        if ($request['method'] !== 'initialize') {
            return $isNotification
                ? JsonRpcResult::acceptedNotification()
                : JsonRpcResult::response($this->error($id, -32601, 'Method not found.'));
        }

        if ($isNotification) {
            return JsonRpcResult::rejectedNotification();
        }

        try {
            return JsonRpcResult::response([
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $this->initialize($request['params'] ?? null),
            ]);
        } catch (McpException $exception) {
            return JsonRpcResult::response($this->error($id, $exception->getJsonRpcCode(), $exception->getMessage(), $exception->getErrorData()));
        }
    }

    /**
     * @param mixed $params
     * @return array<string, mixed>
     */
    private function initialize($params): array
    {
        if (
            !is_array($params)
            || $this->isList($params)
            || !isset($params['protocolVersion'], $params['capabilities'], $params['clientInfo'])
            || !is_string($params['protocolVersion'])
            || preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $params['protocolVersion']) !== 1
            || !is_array($params['capabilities'])
            || !is_array($params['clientInfo'])
            || !isset($params['clientInfo']['name'], $params['clientInfo']['version'])
            || !is_string($params['clientInfo']['name'])
            || !is_string($params['clientInfo']['version'])
        ) {
            throw new McpException('Invalid initialize parameters.', -32602);
        }

        $server = (array) ($this->appConfig['server'] ?? []);

        return [
            'protocolVersion' => (string) ($server['protocol_version'] ?? '2025-11-25'),
            'capabilities' => ['tools' => new \stdClass()],
            'serverInfo' => [
                'name' => (string) ($server['name'] ?? 'Hardness MCP'),
                'version' => (string) ($server['version'] ?? '0.1.0'),
            ],
        ];
    }

    /**
     * @param int|string|null $id
     * @param array<string, mixed>|null $data
     * @return array<string, mixed>
     */
    private function error($id, int $code, string $message, ?array $data = null): array
    {
        $error = ['code' => $code, 'message' => $message];
        if ($data !== null) {
            $error['data'] = $data;
        }

        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => $error];
    }

    private function hasSupportedProtocolVersion(?string $protocolVersionHeader): bool
    {
        $server = (array) ($this->appConfig['server'] ?? []);

        return $protocolVersionHeader === (string) ($server['protocol_version'] ?? '2025-11-25');
    }

    /**
     * @param array<mixed> $value
     */
    private function isList(array $value): bool
    {
        return array_keys($value) === range(0, count($value) - 1);
    }
}
