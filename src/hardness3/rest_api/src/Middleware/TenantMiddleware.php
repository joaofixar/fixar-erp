<?php

declare(strict_types=1);

namespace RestApi\Middleware;

use RestApi\Core\Request;
use RestApi\Tenant\TenantResolver;

class TenantMiddleware
{
    private TenantResolver $tenantResolver;

    /** @var array<int, string> */
    private array $publicRoutes;

    /**
     * @param array<int, string> $publicRoutes
     */
    public function __construct(TenantResolver $tenantResolver, array $publicRoutes = [])
    {
        $this->tenantResolver = $tenantResolver;
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

        $tenantContext = $this->tenantResolver->resolve($request->getHost());
        $request->setAttribute('tenant', $tenantContext);

        return $next($request);
    }
}
