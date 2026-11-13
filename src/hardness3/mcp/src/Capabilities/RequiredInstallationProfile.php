<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

use InvalidArgumentException;

/** Requires one explicit, server-owned installation profile. */
final class RequiredInstallationProfile implements CapabilityRequirementInterface
{
    private string $profile;

    public function __construct(string $profile)
    {
        if (preg_match('/\A[a-z][a-z0-9]*(?:-[a-z0-9]+)*\z/', $profile) !== 1) {
            throw new InvalidArgumentException('Invalid installation profile requirement.');
        }
        $this->profile = $profile;
    }

    public function isSatisfied(CapabilityProbeInterface $probe): bool
    {
        return $probe->hasInstallationProfile($this->profile);
    }
}
