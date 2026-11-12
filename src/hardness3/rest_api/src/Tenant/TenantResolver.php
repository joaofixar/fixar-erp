<?php

declare(strict_types=1);

namespace RestApi\Tenant;

use RestApi\Core\ApiException;

class TenantResolver
{
    private string $confPath;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $tenantConfig = $appConfig['tenant'] ?? [];
        $this->confPath = rtrim((string) ($tenantConfig['conf_path'] ?? '/var/www/sites'), '/');
    }

    /**
     * @return array<string, string>
     */
    public function resolve(string $rawHost): array
    {
        $host = $this->normalizeHost($rawHost);
        $file = sprintf('%s/%s-confUsuario.php', $this->confPath, $host);

        if (!file_exists($file)) {
            throw new ApiException(
                'INVALID_TENANT',
                'Não foi possível resolver o tenant.',
                404,
                ['reason' => 'conf_not_found', 'host' => $host]
            );
        }

        $confUsuario = $this->loadConfUsuario($file);
        $this->validateConfUsuario($confUsuario, $host);

        return [
            'host' => $host,
            'conf_file' => $file,
            'dbDatabase' => (string) $confUsuario['dbDatabase'],
            'dbHost' => (string) $confUsuario['dbHost'],
            'dbUser' => (string) $confUsuario['dbUser'],
            'dbPass' => (string) $confUsuario['dbPass'],
            'pathRaiz' => (string) $confUsuario['pathRaiz'],
            'urlRaiz' => (string) $confUsuario['urlRaiz'],
        ];
    }

    private function normalizeHost(string $rawHost): string
    {
        $host = trim(strtolower($rawHost));
        if ($host === '') {
            throw new ApiException('INVALID_TENANT', 'Não foi possível resolver o tenant.', 400, [
                'reason' => 'empty_host',
            ]);
        }

        $host = preg_replace('/:\d+$/', '', $host) ?? '';
        if ($host === '') {
            throw new ApiException('INVALID_TENANT', 'Não foi possível resolver o tenant.', 400, [
                'reason' => 'invalid_host',
            ]);
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return $host;
        }

        if ($host === 'localhost') {
            return $host;
        }

        if (!preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)*$/', $host)) {
            throw new ApiException('INVALID_TENANT', 'Não foi possível resolver o tenant.', 400, [
                'reason' => 'invalid_host',
                'host' => $host,
            ]);
        }

        return $host;
    }

    /**
     * @return mixed
     */
    private function loadConfUsuario(string $file)
    {
        $confUsuario = (static function (string $confFile) {
            $confUsuario = null;
            require $confFile;
            return $confUsuario;
        })($file);

        if (!is_array($confUsuario)) {
            throw new ApiException(
                'INVALID_TENANT',
                'Não foi possível resolver o tenant.',
                500,
                ['reason' => 'conf_usuario_missing']
            );
        }

        return $confUsuario;
    }

    /**
     * @param mixed $confUsuario
     */
    private function validateConfUsuario($confUsuario, string $host): void
    {
        $requiredFields = ['dbDatabase', 'dbHost', 'dbUser', 'dbPass', 'pathRaiz', 'urlRaiz'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $confUsuario)) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields !== []) {
            throw new ApiException(
                'INVALID_TENANT',
                'Não foi possível resolver o tenant.',
                500,
                [
                    'reason' => 'missing_required_fields',
                    'host' => $host,
                    'fields' => $missingFields,
                ]
            );
        }
    }
}
