<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

interface CapabilityRequirementInterface
{
    public function isSatisfied(CapabilityProbeInterface $probe): bool;
}
