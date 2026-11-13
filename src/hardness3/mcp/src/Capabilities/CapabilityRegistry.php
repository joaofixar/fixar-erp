<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

use InvalidArgumentException;

/** Resolves the explicit, versioned catalogue without exposing database details to Tools. */
final class CapabilityRegistry
{
    /** @var CapabilityDefinition[] */
    private array $definitions;

    /** @param CapabilityDefinition[] $definitions */
    public function __construct(array $definitions)
    {
        $ids = [];
        foreach ($definitions as $definition) {
            if (!$definition instanceof CapabilityDefinition || isset($ids[$definition->getId()])) {
                throw new InvalidArgumentException('Invalid capability registry.');
            }
            $ids[$definition->getId()] = true;
        }
        $this->definitions = array_values($definitions);
    }

    public function resolve(CapabilityProbeInterface $probe): CapabilitySet
    {
        $availability = [];
        foreach ($this->definitions as $definition) {
            $availability[$definition->getId()] = $definition->isAvailable($probe);
        }
        return new CapabilitySet($availability);
    }
}
