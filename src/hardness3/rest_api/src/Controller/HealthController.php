<?php

declare(strict_types=1);

namespace RestApi\Controller;

use RestApi\Core\Request;
use RestApi\Core\Response;

class HealthController
{
    /**
     * @var array<string, mixed>
     */
    private array $appConfig;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function health(Request $request): array
    {
        return Response::success([
            'status' => 'ok',
            'service' => $this->appConfig['app_name'] ?? 'REST API',
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function version(Request $request): array
    {
        return Response::success([
            'version' => $this->appConfig['api_version'] ?? 'v1',
            'base_path' => $this->appConfig['base_path'] ?? '/api/v1',
            'service' => $this->appConfig['app_name'] ?? 'REST API',
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }
}
