<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Context;

/** Immutable administrative identity derived solely from the authenticated ERP. */
final class WebhookAdminContext
{
    private int $userId;
    private int $profileId;
    private int $companyId;

    public function __construct(int $userId, int $profileId, int $companyId)
    {
        $this->userId = $userId;
        $this->profileId = $profileId;
        $this->companyId = $companyId;
    }

    public function getUserId(): int { return $this->userId; }
    public function getProfileId(): int { return $this->profileId; }
    public function getCompanyId(): int { return $this->companyId; }
}
