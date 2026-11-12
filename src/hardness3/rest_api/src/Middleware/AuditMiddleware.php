<?php

declare(strict_types=1);

namespace RestApi\Middleware;

use RestApi\Core\Request;
use RestApi\Service\AuditService;
use Throwable;

class AuditMiddleware
{
    private AuditService $auditService;

    /** @var array<int, string> */
    private array $publicRoutes;

    /**
     * @param array<int, string> $publicRoutes
     */
    public function __construct(AuditService $auditService, array $publicRoutes = [])
    {
        $this->auditService = $auditService;
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

        $startedAt = microtime(true);

        try {
            $result = $next($request);
            $this->auditService->record(
                $request->getAttribute('connection'),
                $request,
                $result,
                null,
                $this->calculateDurationMs($startedAt)
            );

            return $result;
        } catch (Throwable $throwable) {
            $this->auditService->record(
                $request->getAttribute('connection'),
                $request,
                null,
                $throwable,
                $this->calculateDurationMs($startedAt)
            );

            throw $throwable;
        }
    }

    private function calculateDurationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
