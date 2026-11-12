<?php

declare(strict_types=1);

namespace RestApi\Core;

use RestApi\Auth\TokenService;
use RestApi\Controller\AuthController;
use RestApi\Controller\DocsController;
use RestApi\Controller\HealthController;
use RestApi\Controller\TableController;
use RestApi\Controller\TenantController;
use RestApi\Database\ConnectionFactory;
use RestApi\Database\QueryBuilder;
use RestApi\Database\SchemaRepository;
use RestApi\Middleware\AuditMiddleware;
use RestApi\Middleware\AuthMiddleware;
use RestApi\Middleware\TenantMiddleware;
use RestApi\Security\AccessPolicyRepository;
use RestApi\Security\AccessPolicyService;
use RestApi\Security\Blocklist;
use RestApi\Security\FieldPolicy;
use RestApi\Service\AuditService;
use RestApi\Service\JoinPlanner;
use RestApi\Service\PayloadValidator;
use RestApi\Service\QueryValidator;
use RestApi\Service\TableReadService;
use RestApi\Service\TableWriteService;
use RestApi\Tenant\TenantResolver;
use Throwable;

class Bootstrap
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/');
    }

    public function run(): void
    {
        $request = Request::capture();
        $request->setAttribute('request_id', $this->generateRequestId());
        try {
            $config = new Config($this->rootPath . '/config');
            $appConfig = $config->load('app');
            $blocklistConfig = $config->load('blocklist');
            $this->enforcePayloadLimit($request, $appConfig);
            $router = new Router();
            $connectionFactory = new ConnectionFactory($appConfig);
            $tokenService = new TokenService($appConfig);
            $schemaRepository = new SchemaRepository($appConfig);
            $accessPolicyRepository = new AccessPolicyRepository($appConfig);
            $accessPolicyService = new AccessPolicyService($accessPolicyRepository);
            $blocklist = new Blocklist($blocklistConfig);
            $fieldPolicy = new FieldPolicy($blocklistConfig);
            $queryBuilder = new QueryBuilder();
            $queryValidator = new QueryValidator($appConfig, $blocklistConfig, $fieldPolicy);
            $joinPlanner = new JoinPlanner($schemaRepository, $accessPolicyService, $blocklist);
            $payloadValidator = new PayloadValidator($fieldPolicy);
            $auditService = new AuditService($appConfig, $blocklistConfig);
            $tableReadService = new TableReadService($schemaRepository, $queryBuilder, $blocklist, $accessPolicyService, $fieldPolicy, $queryValidator, $joinPlanner);
            $tableWriteService = new TableWriteService($schemaRepository, $queryBuilder, $blocklist, $accessPolicyService, $payloadValidator);

            $healthController = new HealthController($appConfig);
            $docsController = new DocsController($this->rootPath, $appConfig);
            $tenantController = new TenantController($appConfig, $connectionFactory);
            $authController = new AuthController();
            $tableController = new TableController($tableReadService, $tableWriteService);

            $basePath = rtrim((string) ($appConfig['base_path'] ?? '/api/v1'), '/');
            $router->add('GET', $basePath . '/health', [$healthController, 'health']);
            $router->add('GET', $basePath . '/version', [$healthController, 'version']);
            if ((bool) (($appConfig['debug']['enable_tenant_check'] ?? false))) {
                $router->add('GET', $basePath . '/tenant/check', [$tenantController, 'check']);
            }
            $router->add('GET', $basePath . '/auth/me', [$authController, 'me']);
            $router->add('GET', $basePath . '/tables', [$tableController, 'listTables']);
            $router->add('GET', $basePath . '/tables/{table}/schema', [$tableController, 'schema']);
            $router->add('POST', $basePath . '/tables/{table}/validate', [$tableController, 'validatePayload']);
            $router->add('GET', $basePath . '/tables/{table}/{id}', [$tableController, 'recordById']);
            $router->add('PUT', $basePath . '/tables/{table}/{id}', [$tableController, 'putNotAllowed']);
            $router->add('PATCH', $basePath . '/tables/{table}/{id}', [$tableController, 'updateRecord']);
            $router->add('DELETE', $basePath . '/tables/{table}/{id}', [$tableController, 'deleteNotAllowed']);
            $router->add('GET', $basePath . '/tables/{table}', [$tableController, 'records']);
            $router->add('POST', $basePath . '/tables/{table}', [$tableController, 'createRecord']);
            $router->add('GET', '/api/docs', [$docsController, 'swaggerUi']);
            $router->add('GET', '/api/docs/openapi.yaml', [$docsController, 'openapi']);
            $router->add('GET', '/api/docs/postman_collection.json', [$docsController, 'postman']);

            $publicRoutes = [
                'GET ' . $basePath . '/health',
                'GET ' . $basePath . '/version',
                'GET /api/docs',
                'GET /api/docs/openapi.yaml',
                'GET /api/docs/postman_collection.json',
            ];

            $middlewares = [
                new TenantMiddleware(
                    new TenantResolver($appConfig),
                    $publicRoutes
                ),
                new AuthMiddleware($connectionFactory, $tokenService, $publicRoutes),
                new AuditMiddleware($auditService, $publicRoutes),
            ];

            $runner = $this->buildMiddlewareRunner(
                $middlewares,
                static function (Request $nextRequest) use ($router): array {
                    return $router->dispatch($nextRequest);
                }
            );

            $result = $runner($request);
        } catch (ApiException $exception) {
            $result = Response::error(
                $exception->getErrorCode(),
                $exception->getMessage(),
                $exception->getDetails(),
                ['request_id' => (string) $request->getAttribute('request_id', '')],
                $exception->getCode()
            );
        } catch (Throwable $exception) {
            $status = $exception->getCode();
            if (!is_int($status) || $status < 400 || $status > 599) {
                $status = 500;
            }

            $errorCode = $status === 404 ? 'ROUTE_NOT_FOUND' : 'INTERNAL_ERROR';
            $message = $status === 404 ? 'Route not found.' : 'Internal server error.';
            $result = Response::error(
                $errorCode,
                $message,
                [],
                ['request_id' => (string) $request->getAttribute('request_id', '')],
                $status
            );
        }

        Response::send($result['payload'], $result['status'], $result['headers'] ?? []);
    }

    /**
     * @param array<int, object> $middlewares
     * @param callable(Request): array{status:int,payload:array<string,mixed>} $lastHandler
     * @return callable(Request): array{status:int,payload:array<string,mixed>}
     */
    private function buildMiddlewareRunner(array $middlewares, callable $lastHandler): callable
    {
        $runner = $lastHandler;

        foreach (array_reverse($middlewares) as $middleware) {
            $runner = static function (Request $request) use ($middleware, $runner): array {
                return $middleware->handle($request, $runner);
            };
        }

        return $runner;
    }

    private function generateRequestId(): string
    {
        return 'req_' . bin2hex(random_bytes(8));
    }

    /**
     * @param array<string, mixed> $appConfig
     */
    private function enforcePayloadLimit(Request $request, array $appConfig): void
    {
        $rawBody = $request->getRawBody();
        if ($rawBody === null || $rawBody === '') {
            return;
        }

        $maxPayloadKb = (int) (($appConfig['limits']['max_payload_kb'] ?? 0));
        if ($maxPayloadKb <= 0) {
            return;
        }

        if (strlen($rawBody) > ($maxPayloadKb * 1024)) {
            throw new ApiException('PAYLOAD_TOO_LARGE', 'Payload excede o limite permitido.', 413);
        }
    }
}
