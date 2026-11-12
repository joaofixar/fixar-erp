<?php

declare(strict_types=1);

namespace RestApi\Security;

class AccessPolicyRepository
{
    private string $basePath;
    private string $tenantDataBasePath;
    private string $tenantDataRelativePath;

    /** @var array<string, array<string, array<string, mixed>>> */
    private array $cache = [];

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $policyConfig = $appConfig['policy'] ?? [];
        $configuredBasePath = (string) ($policyConfig['base_path'] ?? '');
        $this->basePath = rtrim($configuredBasePath, '/');
        $this->tenantDataBasePath = rtrim((string) ($policyConfig['tenant_data_base_path'] ?? ''), '/');
        $this->tenantDataRelativePath = trim((string) ($policyConfig['tenant_data_relative_path'] ?? 'rest_api/access_policy.php'), '/');
    }

    /**
     * @param array<string, string> $tenantContext
     * @return array<string, array<string, mixed>>
     */
    public function load(array $tenantContext): array
    {
        $inspection = $this->inspect($tenantContext);
        return $inspection['status'] === 'valid' ? $inspection['policy'] : [];
    }

    /**
     * @param array<string, string> $tenantContext
     */
    public function getPolicyFilePath(array $tenantContext): ?string
    {
        $identifier = $this->resolvePolicyIdentifier($tenantContext);
        return $identifier === null ? null : $this->getPrimaryPolicyFile($tenantContext, $identifier['file_name']);
    }

    /**
     * @param array<string, string> $tenantContext
     * @return array{status:string,path:?string,last_modified:?int,policy:array<string, array<string, mixed>>,error:?string}
     */
    public function inspect(array $tenantContext): array
    {
        $identifier = $this->resolvePolicyIdentifier($tenantContext);
        $path = $identifier === null ? null : $this->getPrimaryPolicyFile($tenantContext, $identifier['file_name']);

        if ($identifier === null) {
            return [
                'status' => 'missing',
                'path' => $path,
                'last_modified' => null,
                'policy' => [],
                'error' => null,
            ];
        }

        $cacheKey = $identifier['cache_key'];
        if (isset($this->cache[$cacheKey])) {
            return [
                'status' => 'valid',
                'path' => $this->resolveExistingPolicyFile($tenantContext, $identifier['file_name']) ?? $path,
                'last_modified' => null,
                'policy' => $this->cache[$cacheKey],
                'error' => null,
            ];
        }

        $file = $this->resolveExistingPolicyFile($tenantContext, $identifier['file_name']);
        if ($file === null || !is_file($file) || !is_readable($file)) {
            return [
                'status' => 'missing',
                'path' => $path,
                'last_modified' => null,
                'policy' => [],
                'error' => null,
            ];
        }

        try {
            $policy = $this->loadPolicyFile($file);
        } catch (\Throwable $throwable) {
            return [
                'status' => 'invalid',
                'path' => $file,
                'last_modified' => @filemtime($file) ?: null,
                'policy' => [],
                'error' => $throwable->getMessage(),
            ];
        }

        if (!is_array($policy)) {
            return [
                'status' => 'invalid',
                'path' => $file,
                'last_modified' => @filemtime($file) ?: null,
                'policy' => [],
                'error' => 'O arquivo não retornou um array válido.',
            ];
        }

        $normalized = $this->normalizePolicy($policy);
        if ($normalized === null) {
            return [
                'status' => 'invalid',
                'path' => $file,
                'last_modified' => @filemtime($file) ?: null,
                'policy' => [],
                'error' => 'A estrutura da policy é inválida.',
            ];
        }

        $this->cache[$cacheKey] = $normalized;

        return [
            'status' => 'valid',
            'path' => $file,
            'last_modified' => @filemtime($file) ?: null,
            'policy' => $normalized,
            'error' => null,
        ];
    }

    private function resolveDatabase(array $tenantContext): string
    {
        $database = (string) ($tenantContext['dbDatabase'] ?? '');
        return trim($database);
    }

    private function resolveHost(array $tenantContext): string
    {
        $host = (string) ($tenantContext['host'] ?? '');
        return trim($host);
    }

    /**
     * @param array<string, string> $tenantContext
     * @return array{cache_key:string, file_name:string}|null
     */
    private function resolvePolicyIdentifier(array $tenantContext): ?array
    {
        $database = $this->resolveDatabase($tenantContext);
        $host = $this->normalizeHostForFileName($this->resolveHost($tenantContext));

        if ($database === '' || $host === '') {
            return null;
        }

        $fileName = $host . '__' . $database;

        return [
            'cache_key' => $fileName,
            'file_name' => $fileName,
        ];
    }

    private function normalizeHostForFileName(string $host): string
    {
        $host = strtolower(trim($host));
        if ($host === '') {
            return '';
        }

        $host = preg_replace('/:\d+$/', '', $host) ?? '';
        $host = str_replace('.', '-', $host);
        $host = preg_replace('/[^a-z0-9-]+/', '-', $host) ?? '';
        $host = preg_replace('/-+/', '-', $host) ?? '';

        return trim($host, '-');
    }

    private function getPolicyFile(string $fileName): ?string
    {
        if ($this->basePath === '') {
            return null;
        }

        return $this->basePath . '/' . $fileName . '.php';
    }

    private function getTenantDataPolicyFile(string $database): ?string
    {
        if ($this->tenantDataBasePath === '' || $this->tenantDataRelativePath === '' || trim($database) === '') {
            return null;
        }

        return $this->tenantDataBasePath . '/' . trim($database) . '/' . $this->tenantDataRelativePath;
    }

    /**
     * @param array<string, string> $tenantContext
     */
    private function getPrimaryPolicyFile(array $tenantContext, string $fileName): ?string
    {
        $database = $this->resolveDatabase($tenantContext);
        $tenantDataFile = $this->getTenantDataPolicyFile($database);
        if ($tenantDataFile !== null) {
            return $tenantDataFile;
        }

        return $this->getPolicyFile($fileName);
    }

    /**
     * @param array<string, string> $tenantContext
     */
    private function resolveExistingPolicyFile(array $tenantContext, string $fileName): ?string
    {
        $primaryFile = $this->getPrimaryPolicyFile($tenantContext, $fileName);
        if ($primaryFile !== null && is_file($primaryFile) && is_readable($primaryFile)) {
            return $primaryFile;
        }

        $fallbackFile = $this->getPolicyFile($fileName);
        if ($fallbackFile !== null && $fallbackFile !== $primaryFile && is_file($fallbackFile) && is_readable($fallbackFile)) {
            return $fallbackFile;
        }

        return $primaryFile;
    }

    /**
     * @return mixed
     */
    private function loadPolicyFile(string $file)
    {
        clearstatcache(true, $file);
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($file, true);
        }

        return (static function (string $policyFile) {
            return require $policyFile;
        })($file);
    }

    /**
     * @param array<string, mixed> $policy
     * @return array<string, array<string, mixed>>|null
     */
    private function normalizePolicy(array $policy): ?array
    {
        $normalized = [];

        foreach ($policy as $table => $rule) {
            if (!is_string($table)) {
                return null;
            }

            $normalizedTable = strtoupper(trim($table));
            if ($normalizedTable === '') {
                return null;
            }

            if ($rule === true) {
                $normalized[$normalizedTable] = [
                    'mode' => 'all',
                ];
                continue;
            }

            if (!is_array($rule) || $rule === []) {
                return null;
            }

            $hasAllow = array_key_exists('allow', $rule);
            $hasBlock = array_key_exists('block', $rule);

            if (($hasAllow && $hasBlock) || (!$hasAllow && !$hasBlock)) {
                return null;
            }

            $mode = $hasAllow ? 'allow' : 'block';
            $values = $rule[$mode];

            if (!is_array($values) || $values === []) {
                return null;
            }

            $normalizedFields = [];
            foreach ($values as $field) {
                if (!is_string($field)) {
                    return null;
                }

                $normalizedField = trim($field);
                if ($normalizedField === '') {
                    return null;
                }

                $normalizedFields[] = $normalizedField;
            }

            $normalized[$normalizedTable] = [
                'mode' => $mode,
                'fields' => array_values(array_unique($normalizedFields)),
            ];
        }

        return $normalized;
    }
}
