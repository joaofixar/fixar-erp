<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

/** Read-only database facts used to decide whether an optional feature exists. */
interface CapabilityProbeInterface
{
    /** True only for the explicit server-owned profile resolved for this installation. */
    public function hasInstallationProfile(string $profile): bool;

    /** @param string[] $columns */
    public function hasColumns(string $table, array $columns): bool;

    /** @param scalar|null $value */
    public function hasRecord(string $table, string $column, $value): bool;
}
