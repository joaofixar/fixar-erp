<?php

declare(strict_types=1);

namespace HardnessMcp\Scopes;

use HardnessMcp\Capabilities\InstallationCapabilityCatalog;
use PDO;

/** Resolves optional installation capabilities once for each database boundary. */
final class ScopeCatalogFactory
{
    public static function forPdo(PDO $pdo, ?string $installationProfile = null): ScopeCatalog
    {
        return new ScopeCatalog(InstallationCapabilityCatalog::resolve($pdo, $installationProfile));
    }
}
