<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

final class RequiredColumns implements CapabilityRequirementInterface
{
    private string $table;
    /** @var string[] */
    private array $columns;

    /** @param string[] $columns */
    public function __construct(string $table, array $columns) { $this->table = $table; $this->columns = array_values($columns); }
    public function isSatisfied(CapabilityProbeInterface $probe): bool { return $probe->hasColumns($this->table, $this->columns); }
}
