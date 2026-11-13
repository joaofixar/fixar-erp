<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

final class RequiredRecord implements CapabilityRequirementInterface
{
    private string $table;
    private string $column;
    /** @var scalar|null */
    private $value;

    /** @param scalar|null $value */
    public function __construct(string $table, string $column, $value) { $this->table = $table; $this->column = $column; $this->value = $value; }
    public function isSatisfied(CapabilityProbeInterface $probe): bool { return $probe->hasRecord($this->table, $this->column, $this->value); }
}
