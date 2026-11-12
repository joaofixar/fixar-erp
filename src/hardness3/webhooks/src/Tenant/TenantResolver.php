<?php

declare(strict_types=1);

namespace HardnessWebhooks\Tenant;

final class TenantResolver
{
    /** @var string */ private $directory;
    /** @var array<int,string> */ private $errors = array();

    public function __construct(string $directory = '/var/www/sites') { $this->directory = rtrim($directory, '/'); }

    /** @return array<int,array{label:string,host:string,database:string,user:string,password:string}> */
    public function resolveAll(): array
    {
        $root = realpath($this->directory);
        if ($root === false || !is_dir($root)) { throw new \RuntimeException('Webhook tenant configuration directory is unavailable.'); }
        $files = glob($root . '/*-confUsuario.php'); if ($files === false) { return array(); }
        $tenants = array(); $seen = array();
        foreach ($files as $candidate) {
            $file = realpath($candidate);
            if ($file === false || strpos($file, rtrim($root, '/') . '/') !== 0) { continue; }
            try { $configuration = $this->parseConfiguration($file); $this->validate($configuration); } catch (\RuntimeException $exception) { $this->errors[] = basename($file); continue; }
            $identity = strtolower($configuration['dbHost']) . "\0" . $configuration['dbDatabase'];
            if (isset($seen[$identity])) { continue; }
            $seen[$identity] = true;
            $tenants[] = array('label' => basename($file, '-confUsuario.php'), 'host' => $configuration['dbHost'], 'database' => $configuration['dbDatabase'], 'user' => $configuration['dbUser'], 'password' => $configuration['dbPass']);
        }
        return $tenants;
    }

    /** @return array<int,string> Safe labels only; no paths or configuration values. */
    public function getErrors(): array { return $this->errors; }

    /** @return array<string,string> */
    private function parseConfiguration(string $file): array
    {
        $source = file_get_contents($file); if ($source === false) { throw new \RuntimeException('Unreadable tenant configuration.'); }
        if (preg_match('/\\$confUsuario\\s*=\\s*(?:array\\s*\\(|\\[)(.*?)(?:\\)|\\])\\s*;/s', $source, $match) !== 1) { throw new \RuntimeException('Invalid tenant configuration.'); }
        $result = array();
        if (preg_match_all("/'((?:\\\\.|[^'])*)'\\s*=>\\s*'((?:\\\\.|[^'])*)'/", $match[1], $pairs, PREG_SET_ORDER) !== false) {
            foreach ($pairs as $pair) { $result[str_replace("\\\\'", "'", str_replace('\\\\\\\\', '\\\\', $pair[1]))] = str_replace("\\\\'", "'", str_replace('\\\\\\\\', '\\\\', $pair[2])); }
        }
        return $result;
    }

    /** @param array<string,string> $configuration */
    private function validate(array $configuration): void
    {
        foreach (array('dbDatabase', 'dbHost', 'dbUser', 'dbPass') as $field) { if (!isset($configuration[$field]) || $configuration[$field] === '') { throw new \RuntimeException('Incomplete tenant configuration.'); } }
    }
}
