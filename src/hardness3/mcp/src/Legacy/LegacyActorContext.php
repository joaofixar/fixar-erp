<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

/**
 * Authorization facts resolved internally for one legacy ERP actor.
 */
final class LegacyActorContext
{
    private int $companyId;
    private int $userId;
    private int $profileId;
    private int $approvalLevel;
    private string $timeZone;

    public function __construct(int $companyId, int $userId, int $profileId, int $approvalLevel, string $timeZone)
    {
        if ($companyId <= 0 || $userId <= 0 || $profileId <= 0 || $approvalLevel < 1 || trim($timeZone) === '') {
            throw new LegacyContextResolutionException();
        }

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->profileId = $profileId;
        $this->approvalLevel = $approvalLevel;
        $this->timeZone = $timeZone;
    }

    public function getCompanyId(): int { return $this->companyId; }
    public function getUserId(): int { return $this->userId; }
    public function getProfileId(): int { return $this->profileId; }
    public function getApprovalLevel(): int { return $this->approvalLevel; }
    public function getTimeZone(): string { return $this->timeZone; }
}
