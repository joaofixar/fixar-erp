<?php
declare(strict_types=1);

namespace Hardness\Observability\Config;

/** Credencial local já usada pelo showprocess para leitura global do MySQL. */
final class ShowprocessConnectionProvider
{
    public function tenant(): ?array
    {
        $path = dirname(HARDNESS_OBSERVABILITY_ROOT) . '/showprocess/confConnect.php';
        $stat = @lstat($path);
        if ($stat === false || is_link($path) || !is_file($path)) { return null; }

        $config = (static function (string $file) { $confConnect = null; require $file; return $confConnect; })($path);
        if (!is_array($config) || empty($config['dbHost']) || empty($config['dbUser']) || !array_key_exists('dbPass', $config)) { return null; }

        return array(
            'tenant_key' => 'mysql-server',
            'tenant_label' => 'Servidor MySQL',
            'central' => true,
            'database' => array(
                'host' => (string) $config['dbHost'],
                'port' => isset($config['dbPort']) ? (int) $config['dbPort'] : 3306,
                'database' => 'mysql',
                'username' => (string) $config['dbUser'],
                'password' => (string) $config['dbPass'],
            ),
        );
    }
}
