<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

/** Immutable result of resolving all capabilities for one tenant database. */
final class CapabilitySet
{
    /** @var array<string,bool> */
    private array $availability;

    /** @param array<string,bool> $availability */
    public function __construct(array $availability)
    {
        $this->availability = $availability;
    }

    public static function empty(): self { return new self([]); }

    public function has(string $id): bool
    {
        return ($this->availability[$id] ?? false) === true;
    }
}
