<?php

declare(strict_types=1);

namespace RestApi\Controller;

use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Core\Response;
use RestApi\Service\TableReadService;
use RestApi\Service\TableWriteService;

class TableController
{
    private TableReadService $tableReadService;
    private TableWriteService $tableWriteService;

    public function __construct(TableReadService $tableReadService, TableWriteService $tableWriteService)
    {
        $this->tableReadService = $tableReadService;
        $this->tableWriteService = $tableWriteService;
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function listTables(Request $request): array
    {
        $request->setAttribute('audit_operation', 'list_tables');
        $request->setAttribute('audit_table', null);

        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');

        if (!is_array($tenantContext) || !is_object($connection)) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $tables = $this->tableReadService->listTables($connection, $tenantContext);
        $request->setAttribute('audit_affected_rows', count($tables));

        return Response::success([
            'tables' => $tables,
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
            'count' => count($tables),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function schema(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'get_schema');
        $request->setAttribute('audit_table', $table);

        $schema = $this->tableReadService->getTableSchema($connection, $tenantContext, $table);
        $request->setAttribute('audit_affected_rows', count((array) ($schema['fields'] ?? [])));

        return Response::success($schema, [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function records(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'select');
        $request->setAttribute('audit_table', $table);

        $result = $this->tableReadService->getRecords($connection, $tenantContext, $table, $request);
        $request->setAttribute('audit_affected_rows', (int) ($result['pagination']['returned'] ?? 0));

        return Response::success($result['rows'], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
            'pagination' => $result['pagination'],
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function recordById(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');
        $recordId = $request->getRouteParam('id');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null || $recordId === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'select_by_pk');
        $request->setAttribute('audit_table', $table);

        $row = $this->tableReadService->getRecordByPrimaryKey($connection, $tenantContext, $table, $recordId);
        $request->setAttribute('audit_affected_rows', 1);

        return Response::success($row, [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function validatePayload(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'validate');
        $request->setAttribute('audit_table', $table);

        $validated = $this->tableWriteService->validatePayload(
            $connection,
            $tenantContext,
            $table,
            $request->getJsonBody(),
            $request->getMethod() === 'PATCH'
        );
        $request->setAttribute('audit_affected_rows', 0);

        return Response::success([
            'valid' => true,
            'table' => $validated['table'],
            'primary_key' => $validated['primary_key'],
            'fields' => array_keys($validated['validated_payload']),
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function createRecord(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'insert');
        $request->setAttribute('audit_table', $table);

        $result = $this->tableWriteService->insert($connection, $tenantContext, $table, $request->getJsonBody());
        $request->setAttribute('audit_affected_rows', (int) ($result['affected_rows'] ?? 0));

        return Response::success($result, [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ], 201);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function updateRecord(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');
        $recordId = $request->getRouteParam('id');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        if ($recordId === null || trim($recordId) === '') {
            throw new ApiException('INVALID_PRIMARY_KEY', 'A valid primary key value is required for update', 400);
        }

        $request->setAttribute('audit_operation', 'update');
        $request->setAttribute('audit_table', $table);

        $result = $this->tableWriteService->update($connection, $tenantContext, $table, $recordId, $request->getJsonBody());
        $request->setAttribute('audit_affected_rows', (int) ($result['affected_rows'] ?? 0));

        return Response::success($result, [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function putNotAllowed(Request $request): array
    {
        $table = $request->getRouteParam('table');
        $request->setAttribute('audit_operation', 'put_blocked');
        $request->setAttribute('audit_table', $table);
        $request->setAttribute('audit_affected_rows', 0);

        return Response::error(
            'METHOD_NOT_ALLOWED',
            'PUT is not available in this API. Use PATCH instead.',
            [],
            [
                'request_id' => (string) $request->getAttribute('request_id', ''),
            ],
            405
        );
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function deleteNotAllowed(Request $request): array
    {
        $table = $request->getRouteParam('table');
        $request->setAttribute('audit_operation', 'delete_blocked');
        $request->setAttribute('audit_table', $table);
        $request->setAttribute('audit_affected_rows', 0);

        return Response::error(
            'METHOD_NOT_ALLOWED',
            'DELETE is not available in this API',
            [],
            [
                'request_id' => (string) $request->getAttribute('request_id', ''),
            ],
            405
        );
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function deleteRecord(Request $request): array
    {
        $tenantContext = $request->getAttribute('tenant');
        $connection = $request->getAttribute('connection');
        $table = $request->getRouteParam('table');
        $recordId = $request->getRouteParam('id');

        if (!is_array($tenantContext) || !is_object($connection) || $table === null || $recordId === null) {
            throw new ApiException('CONTEXT_MISSING', 'Contexto da requisição inválido.', 500);
        }

        $request->setAttribute('audit_operation', 'delete');
        $request->setAttribute('audit_table', $table);

        $result = $this->tableWriteService->delete($connection, $tenantContext, $table, $recordId);
        $request->setAttribute('audit_affected_rows', (int) ($result['affected_rows'] ?? 0));

        return Response::success($result, [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }
}
