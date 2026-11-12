<?php

declare(strict_types=1);

namespace RestApi\Controller;

use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Core\Response;
use RestApi\Database\ConnectionFactory;

class TenantController
{
    /** @var array<string, mixed> */
    private array $appConfig;

    private ConnectionFactory $connectionFactory;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig, ConnectionFactory $connectionFactory)
    {
        $this->appConfig = $appConfig;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Temporary technical route for phase validation.
     *
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function check(Request $request): array
    {
        $request->setAttribute('audit_operation', 'tenant_check');

        $tenantContext = $request->getAttribute('tenant');
        if (!is_array($tenantContext)) {
            throw new ApiException('INVALID_TENANT', 'Não foi possível resolver o tenant.', 500);
        }

        $this->connectionFactory->create($tenantContext);

        return Response::success([
            'tenant' => [
                'host' => $tenantContext['host'],
                'dbDatabase' => $tenantContext['dbDatabase'],
                'dbHost' => $tenantContext['dbHost'],
                'pathRaiz' => $tenantContext['pathRaiz'],
                'urlRaiz' => $tenantContext['urlRaiz'],
            ],
            'connection' => [
                'status' => 'ok',
            ],
            'temporary' => true,
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }
}
