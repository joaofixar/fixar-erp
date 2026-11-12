<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tenant\ResolvedTenantContext;

final class LegacyExecutionContextAdapter
{
    private LegacyActorResolverInterface $actorResolver;
    private string $rootPath;

    public function __construct(LegacyActorResolverInterface $actorResolver, string $rootPath)
    {
        $rootPath = rtrim($rootPath, '/');
        if ($rootPath === '') {
            throw new LegacyContextResolutionException();
        }
        $this->actorResolver = $actorResolver;
        $this->rootPath = $rootPath;
    }

    /** @param array<string,mixed> $trustedRequestMetadata */
    public function adapt(ExecutionContext $executionContext, ResolvedTenantContext $tenant, array $trustedRequestMetadata): LegacyExecutionContext
    {
        $userId = $executionContext->getResponsibleUserId();
        $remoteIp = $trustedRequestMetadata['remote_ip'] ?? null;
        if ($userId === null || !is_string($remoteIp) || filter_var($remoteIp, FILTER_VALIDATE_IP) === false) {
            throw new LegacyContextResolutionException();
        }

        $companyId = $executionContext->getCompanyId();
        $actor = $this->actorResolver->resolve($companyId, $userId);
        if ($actor === null || $actor->getCompanyId() !== $companyId || $actor->getUserId() !== $userId) {
            throw new LegacyContextResolutionException();
        }

        $database = $tenant->getDatabase();
        if (preg_match('/\A[a-zA-Z0-9_-]+\z/', $database) !== 1) {
            throw new LegacyContextResolutionException();
        }

        return new LegacyExecutionContext(
            $tenant,
            $this->rootPath . '/',
            $this->rootPath . '/dados_usuarios/' . $database . '/',
            $companyId,
            $userId,
            $actor->getProfileId(),
            $actor->getApprovalLevel(),
            $remoteIp,
            $actor->getTimeZone()
        );
    }
}
