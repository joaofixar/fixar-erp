<?php

declare(strict_types=1);

namespace RestApi\Middleware;

use RestApi\Auth\TokenService;
use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Database\ConnectionFactory;

class AuthMiddleware
{
    private ConnectionFactory $connectionFactory;
    private TokenService $tokenService;

    /** @var array<int, string> */
    private array $publicRoutes;

    /**
     * @param array<int, string> $publicRoutes
     */
    public function __construct(ConnectionFactory $connectionFactory, TokenService $tokenService, array $publicRoutes = [])
    {
        $this->connectionFactory = $connectionFactory;
        $this->tokenService = $tokenService;
        $this->publicRoutes = $publicRoutes;
    }

    /**
     * @param callable(Request): array{status:int,payload:array<string,mixed>} $next
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function handle(Request $request, callable $next): array
    {
        $routeKey = $request->getMethod() . ' ' . $request->getPath();
        if (in_array($routeKey, $this->publicRoutes, true)) {
            return $next($request);
        }

        $tenantContext = $request->getAttribute('tenant');
        if (!is_array($tenantContext)) {
            throw new ApiException('INVALID_TENANT', 'Não foi possível resolver o tenant.', 500);
        }

        $token = $this->tokenService->extractToken($request);
        $connection = $this->connectionFactory->create($tenantContext);
        $authContext = $this->tokenService->authenticateWithToken($token, $request, $connection);

        $request->setAttribute('connection', $connection);
        $request->setAttribute('auth', $authContext);

        return $next($request);
    }
}
