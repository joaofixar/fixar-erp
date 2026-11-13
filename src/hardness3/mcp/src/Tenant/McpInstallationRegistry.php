<?php

declare(strict_types=1);

namespace HardnessMcp\Tenant;

use InvalidArgumentException;

/** MCP-owned, fail-closed inventory for customer-specific capabilities. */
final class McpInstallationRegistry
{
    /** @var array<string,array{database:string,profile:string}> */
    private array $byHost = [];

    /** @param array<string,mixed> $installations */
    public function __construct(array $installations)
    {
        foreach ($installations as $installationId => $entry) {
            if (!is_string($installationId) || preg_match('/\A[a-z][a-z0-9-]*\z/', $installationId) !== 1 || !is_array($entry)) throw new InvalidArgumentException('Invalid MCP installation registry.');
            $database = $entry['expected_database'] ?? null;
            $profile = $entry['profile'] ?? null;
            $hosts = $entry['hosts'] ?? null;
            if (!is_string($database) || $database === '' || !is_string($profile) || preg_match('/\A[a-z][a-z0-9]*(?:-[a-z0-9]+)*\z/', $profile) !== 1 || !is_array($hosts) || $hosts === []) throw new InvalidArgumentException('Invalid MCP installation registry.');
            foreach ($hosts as $host) {
                if (!is_string($host) || preg_match('/\A(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9-]+\z/', $host) !== 1 || isset($this->byHost[$host])) throw new InvalidArgumentException('Invalid MCP installation registry.');
                $this->byHost[$host] = ['database' => $database, 'profile' => $profile];
            }
        }
    }

    /** @param array<string,string> $tenant @return array<string,string> */
    public function apply(array $tenant): array
    {
        unset($tenant['mcpInstallationProfile']);
        $entry = $this->byHost[$tenant['host'] ?? ''] ?? null;
        if ($entry !== null && hash_equals($entry['database'], $tenant['dbDatabase'] ?? '')) $tenant['mcpInstallationProfile'] = $entry['profile'];
        return $tenant;
    }

    /** @param array<string,mixed> $configuration */
    public function profileForConfiguration(array $configuration): ?string
    {
        $url = $configuration['urlRaiz'] ?? null;
        $database = $configuration['dbDatabase'] ?? null;
        $host = is_string($url) ? parse_url($url, PHP_URL_HOST) : null;
        if (!is_string($host) || !is_string($database)) return null;
        $entry = $this->byHost[strtolower($host)] ?? null;
        return $entry !== null && hash_equals($entry['database'], $database) ? $entry['profile'] : null;
    }
}
