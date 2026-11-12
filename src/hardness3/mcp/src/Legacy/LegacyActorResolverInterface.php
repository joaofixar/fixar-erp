<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

interface LegacyActorResolverInterface
{
    /**
     * Returns null when the user is inactive, has no unique authorized profile
     * for the company, or the derived approval level cannot be established.
     */
    public function resolve(int $companyId, int $userId): ?LegacyActorContext;
}
