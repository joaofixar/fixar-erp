<?php
declare(strict_types=1);

namespace Hardness\Observability\Config;

/** Fonte centralizada e controlada dos arquivos legados de tenant. */
final class SiteTenantProvider
{
    private const DIRECTORY = '/var/www/sites';
    private $directory;
    private $localProfilesPath;

    public function __construct(?string $directory = null, ?string $localProfilesPath = null)
    {
        $testDirectory = getenv('OBSERVABILITY_TEST_SITES_PATH');
        $this->directory = $directory ?? (is_string($testDirectory) && $testDirectory !== '' ? $testDirectory : self::DIRECTORY);
        if ($localProfilesPath !== null) {
            $this->localProfilesPath = $localProfilesPath;
        } elseif ($directory === null && (!is_string($testDirectory) || $testDirectory === '')) {
            $this->localProfilesPath = dirname(HARDNESS_OBSERVABILITY_ROOT) . '/configuracoes/local/ambientes.php';
        } else {
            $this->localProfilesPath = null;
        }
    }

    public function resolve(string $host): array
    {
        $host = $this->normalizeRequestHost($host);
        $local = $this->localProfiles();
        if (isset($local[$host])) { return $this->tenant($host, $local[$host]); }
        return $this->load($host, $this->directory . '/' . $host . '-confUsuario.php');
    }

    /** @return array<int,array<string,mixed>> */
    public function all(): array
    {
        $tenants = array(); $databases = array();
        if (is_dir($this->directory) && !is_link($this->directory)) {
            foreach (scandir($this->directory) ?: array() as $entry) {
                if ($entry === '.' || $entry === '..' || substr($entry, -16) !== '-confUsuario.php') { continue; }
                $host = substr($entry, 0, -16);
                try { $this->append($tenants, $databases, $this->load($this->normalizeHost($host), $this->directory . '/' . $entry)); }
                catch (\Throwable $exception) { continue; }
            }
        }
        foreach ($this->localProfiles() as $host => $config) {
            try { $this->append($tenants, $databases, $this->tenant($host, $config)); }
            catch (\Throwable $exception) { continue; }
        }
        return $tenants;
    }

    private function append(array &$tenants, array &$databases, array $tenant): void
    {
        $database = $tenant['database'];
        $key = strtolower($database['host']) . "\0" . $database['port'] . "\0" . $database['database'];
        if (!isset($databases[$key])) { $databases[$key] = true; $tenants[] = $tenant; }
    }

    private function normalizeRequestHost(string $host): string
    {
        $normalized = strtolower(trim(explode(':', $host, 2)[0]));
        if ($this->localProfilesPath !== null && ($normalized === 'localhost' || $normalized === '127.0.0.1' || preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+localhost$/D', $normalized) === 1)) {
            return $normalized;
        }
        return $this->normalizeHost($normalized);
    }

    private function normalizeHost(string $host): string
    {
        $host = strtolower(trim(explode(':', $host, 2)[0]));
        if ($host === '' || strpos($host, '..') !== false || strpos($host, '/') !== false || strpos($host, '\\') !== false || preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/D', $host) !== 1) { throw new \RuntimeException('invalid_host'); }
        return $host;
    }

    private function load(string $host, string $path): array
    {
        $base = realpath($this->directory); $stat = @lstat($path);
        if ($base === false || dirname($path) !== $this->directory || $stat === false || is_link($path) || !is_file($path) || basename($path) !== $host . '-confUsuario.php') { throw new \RuntimeException('invalid_tenant_file'); }
        $config = (static function (string $file) { $confUsuario = null; require $file; return $confUsuario; })($path);
        return $this->tenant($host, $config);
    }

    private function tenant(string $host, $config): array
    {
        if (!is_array($config)) { throw new \RuntimeException('invalid_tenant_config'); }
        foreach (array('dbDatabase', 'dbHost', 'dbUser', 'dbPass', 'pathRaiz', 'urlRaiz') as $field) { if (!array_key_exists($field, $config) || !is_string($config[$field]) || $config[$field] === '') { throw new \RuntimeException('invalid_tenant_config'); } }
        $port = isset($config['dbPort']) ? (int) $config['dbPort'] : 3306;
        if ($port < 1 || $port > 65535) { throw new \RuntimeException('invalid_tenant_config'); }
        return array('tenant_key'=>$host, 'tenant_label'=>$host, 'database'=>array('host'=>$config['dbHost'], 'port'=>$port, 'database'=>$config['dbDatabase'], 'username'=>$config['dbUser'], 'password'=>$config['dbPass']));
    }

    private function localProfiles(): array
    {
        $path = $this->localProfilesPath;
        if (!is_string($path) || $path === '') { return array(); }
        $expected = dirname(HARDNESS_OBSERVABILITY_ROOT) . '/configuracoes/local/ambientes.php';
        $testMode = getenv('OBSERVABILITY_TEST_MODE') === '1';
        if (!$testMode && $path !== $expected) { return array(); }
        $stat = @lstat($path);
        if ($stat === false || is_link($path) || !is_file($path) || realpath($path) !== $path) { return array(); }
        $profiles = (static function (string $file) { return require $file; })($path);
        if (!is_array($profiles)) { return array(); }
        $result = array();
        foreach ($profiles as $host => $config) {
            if (!is_string($host) || !is_array($config)) { continue; }
            try { $host = $this->normalizeRequestHost($host); }
            catch (\Throwable $exception) { continue; }
            $databaseHost = strtolower((string) ($config['dbHost'] ?? ''));
            if (!in_array($databaseHost, array('127.0.0.1', 'localhost', '::1'), true)) { continue; }
            $result[$host] = $config;
        }
        return $result;
    }
}
