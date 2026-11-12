<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin;

/** Opens the current ERP company database only from trusted framework configuration. */
final class ErpDatabaseConnectionFactory
{
    /** @param array<string,mixed> $frameworkContext */
    public function create(array $frameworkContext): \mysqli
    {
        if (isset($frameworkContext['webhooks_mysqli']) && $frameworkContext['webhooks_mysqli'] instanceof \mysqli) return $frameworkContext['webhooks_mysqli'];
        $config = isset($frameworkContext['confUsuario']) && is_array($frameworkContext['confUsuario']) ? $frameworkContext['confUsuario'] : (isset($GLOBALS['confUsuario']) && is_array($GLOBALS['confUsuario']) ? $GLOBALS['confUsuario'] : null);
        if ($config === null) throw new \RuntimeException('ERP database configuration is unavailable.');
        $connection = new \mysqli((string)($config['dbHost']??''),(string)($config['dbUser']??''),(string)($config['dbPass']??''),(string)($config['dbDatabase']??''));
        if ($connection->connect_errno !== 0) throw new \RuntimeException('ERP database connection is unavailable.');
        if (!$connection->set_charset('utf8mb4')) { $connection->close(); throw new \RuntimeException('ERP database charset is unavailable.'); }
        return $connection;
    }
}
