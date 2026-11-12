<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

use HardnessMcp\Tenant\ResolvedTenantContext;

/**
 * Immutable, server-derived input for the future legacy bridge.
 */
final class LegacyExecutionContext
{
    private ResolvedTenantContext $tenant;
    private string $rootPath;
    private string $dataPath;
    private int $companyId;
    private int $userId;
    private int $profileId;
    private int $approvalLevel;
    private string $remoteIp;
    private string $timeZone;

    public function __construct(ResolvedTenantContext $tenant, string $rootPath, string $dataPath, int $companyId, int $userId, int $profileId, int $approvalLevel, string $remoteIp, string $timeZone)
    {
        if ($rootPath === '' || $dataPath === '' || $companyId <= 0 || $userId <= 0 || $profileId <= 0 || $approvalLevel < 1 || filter_var($remoteIp, FILTER_VALIDATE_IP) === false || trim($timeZone) === '') {
            throw new LegacyContextResolutionException();
        }

        $this->tenant = $tenant;
        $this->rootPath = $rootPath;
        $this->dataPath = $dataPath;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->profileId = $profileId;
        $this->approvalLevel = $approvalLevel;
        $this->remoteIp = $remoteIp;
        $this->timeZone = $timeZone;
    }

    public function getTenant(): ResolvedTenantContext { return $this->tenant; }
    public function getCanonicalHost(): string { return $this->tenant->getHost(); }
    public function getRootPath(): string { return $this->rootPath; }
    public function getDataPath(): string { return $this->dataPath; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getUserId(): int { return $this->userId; }
    public function getProfileId(): int { return $this->profileId; }
    public function getApprovalLevel(): int { return $this->approvalLevel; }
    public function getRemoteIp(): string { return $this->remoteIp; }
    public function getTimeZone(): string { return $this->timeZone; }
    public function isDeveloperMode(): bool { return false; }
    public function isDeveloperAppMode(): bool { return false; }
}
