<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

use InvalidArgumentException;

/** A code-owned, all-of definition of an optional installation feature. */
final class CapabilityDefinition
{
    private string $id;
    /** @var CapabilityRequirementInterface[] */
    private array $requirements;

    /** @param CapabilityRequirementInterface[] $requirements */
    public function __construct(string $id, array $requirements)
    {
        if (preg_match('/\A[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)*\z/', $id) !== 1 || $requirements === []) {
            throw new InvalidArgumentException('Invalid capability definition.');
        }
        foreach ($requirements as $requirement) {
            if (!$requirement instanceof CapabilityRequirementInterface) {
                throw new InvalidArgumentException('Invalid capability requirement.');
            }
        }
        $this->id = $id;
        $this->requirements = array_values($requirements);
    }

    public function getId(): string { return $this->id; }

    public function isAvailable(CapabilityProbeInterface $probe): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$requirement->isSatisfied($probe)) { return false; }
        }
        return true;
    }
}
