<?php

declare(strict_types=1);

namespace HardnessMcp\Tenant;

use HardnessMcp\Core\McpException;

class TenantResolver
{
    private string $configurationDirectory;
    private string $configurationDirectoryRealPath;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $tenant = $appConfig['tenant'] ?? [];
        $this->configurationDirectory = rtrim((string) ($tenant['conf_path'] ?? '/var/www/sites'), '/');
        $realPath = realpath($this->configurationDirectory);

        if ($realPath === false || !is_dir($realPath)) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }

        $this->configurationDirectoryRealPath = rtrim($realPath, '/');
    }

    /**
     * @return array<string, string>
     */
    public function resolve(string $rawHost): array
    {
        $host = $this->normalizeHost($rawHost);
        $candidate = $this->configurationDirectory . '/' . $host . '-confUsuario.php';
        $file = realpath($candidate);

        if ($file === false || !$this->isConfigurationFileInsideDirectory($file)) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }

        $source = file_get_contents($file);
        if ($source === false) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }
        $configuration = $this->loadConfiguration($source, '$confUsuario');
        $this->assertConfiguration($configuration);

        $tenant = [
            'host' => $host,
            'conf_file' => $file,
            'dbDatabase' => (string) $configuration['dbDatabase'],
            'dbHost' => (string) $configuration['dbHost'],
            'dbUser' => (string) $configuration['dbUser'],
            'dbPass' => (string) $configuration['dbPass'],
            'urlRaiz' => (string) ($configuration['urlRaiz'] ?? ''),
            // Some private MCP hosts are intentionally not resolvable from
            // the PHP container.  This optional, server-owned value gives
            // wkhtmltopdf an internal origin without changing public links.
            'mcpPdfRenderUrl' => (string) ($configuration['mcpPdfRenderUrl'] ?? ($configuration['urlRaiz'] ?? '')),
        ];

        // This optional literal is read without executing the legacy
        // configuration file.  An incomplete mirror configuration is ignored
        // so it cannot make the principal MCP connection unavailable.
        $mirror = $this->loadConfiguration($source, '$confUsuarioEspelhado', false);
        // A replica can use another host, but it must represent the same
        // tenant database. Never resolve IDs in one customer database and
        // submit the subsequent write to another.
        if ($mirror !== null && $this->isValidConfiguration($mirror) && (string) $mirror['dbDatabase'] === (string) $configuration['dbDatabase']) {
            $tenant['readDbDatabase'] = (string) $mirror['dbDatabase'];
            $tenant['readDbHost'] = (string) $mirror['dbHost'];
            $tenant['readDbUser'] = (string) $mirror['dbUser'];
            $tenant['readDbPass'] = (string) $mirror['dbPass'];
        }

        return $tenant;
    }

    private function normalizeHost(string $rawHost): string
    {
        $host = strtolower(trim($rawHost));
        if ($host === '') {
            throw new McpException('Unable to resolve tenant.', -32603);
        }

        if (preg_match('/^(.+):(\d{1,5})$/', $host, $matches) === 1) {
            $port = (int) $matches[2];
            if ($port < 1 || $port > 65535) {
                throw new McpException('Unable to resolve tenant.', -32603);
            }
            $host = $matches[1];
        }

        if (
            strlen($host) > 253
            || filter_var($host, FILTER_VALIDATE_IP) !== false
            || $host === 'localhost'
            || preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?))+$/', $host) !== 1
        ) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }

        return $host;
    }

    private function isConfigurationFileInsideDirectory(string $file): bool
    {
        $directoryPrefix = $this->configurationDirectoryRealPath . '/';

        return is_file($file) && strpos($file, $directoryPrefix) === 0;
    }

    /**
     * @return mixed
     */
    private function loadConfiguration(string $source, string $variable, bool $required = true): ?array
    {
        $tokens = token_get_all($source);
        $position = 0;
        while (($token = $this->nextSignificantToken($tokens, $position)) !== null) {
            if (!is_array($token) || $token[0] !== T_VARIABLE || $token[1] !== $variable) {
                continue;
            }

            $equals = $this->nextSignificantToken($tokens, $position);
            if ($equals !== '=') {
                continue;
            }

            $array = $this->parseLiteralArray($tokens, $position);
            if ($array !== null) {
                return $array;
            }
        }

        if ($required) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }
        return null;
    }

    /**
     * Reads only a flat PHP array literal. Tenant configuration must never be
     * executed by the MCP endpoint: legacy files may call session_start().
     *
     * @param array<int, mixed> $tokens
     * @return array<string, string>|null
     */
    private function parseLiteralArray(array $tokens, int &$position): ?array
    {
        $start = $this->nextSignificantToken($tokens, $position);
        if (is_array($start) && $start[0] === T_ARRAY) {
            $open = $this->nextSignificantToken($tokens, $position);
            $close = ')';
        } elseif ($start === '[') {
            $open = '[';
            $close = ']';
        } else {
            return null;
        }

        if ($open !== ($close === ')' ? '(' : '[')) {
            return null;
        }

        $configuration = [];
        while (true) {
            $next = $this->nextSignificantToken($tokens, $position);
            if ($next === $close) {
                return $configuration;
            }

            $key = $this->decodeStringToken($next);
            $arrow = $this->nextSignificantToken($tokens, $position);
            $value = $this->decodeStringToken($this->nextSignificantToken($tokens, $position));
            if ($key === null || $arrow === null || !is_array($arrow) || $arrow[0] !== T_DOUBLE_ARROW || $value === null) {
                return null;
            }
            $configuration[$key] = $value;

            $separator = $this->nextSignificantToken($tokens, $position);
            if ($separator === $close) {
                return $configuration;
            }
            if ($separator !== ',') {
                return null;
            }
        }
    }

    /** @param array<int, mixed> $tokens @return mixed */
    private function nextSignificantToken(array $tokens, int &$position)
    {
        while (isset($tokens[$position])) {
            $token = $tokens[$position++];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            return $token;
        }

        return null;
    }

    /** @param mixed $token */
    private function decodeStringToken($token): ?string
    {
        if (!is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
            return null;
        }

        $literal = $token[1];
        if (strlen($literal) < 2) {
            return null;
        }

        $quote = $literal[0];
        if (($quote !== "'" && $quote !== '"') || substr($literal, -1) !== $quote) {
            return null;
        }

        $value = substr($literal, 1, -1);
        return $quote === "'"
            ? str_replace(["\\\\", "\\'"], ["\\", "'"], $value)
            : stripcslashes($value);
    }

    /**
     * @param mixed $configuration
     */
    private function assertConfiguration($configuration): void
    {
        if (!$this->isValidConfiguration($configuration)) {
            throw new McpException('Unable to resolve tenant.', -32603);
        }
    }

    /** @param mixed $configuration */
    private function isValidConfiguration($configuration): bool
    {
        if (!is_array($configuration)) {
            return false;
        }
        foreach (['dbDatabase', 'dbHost', 'dbUser', 'dbPass'] as $field) {
            if (!array_key_exists($field, $configuration) || !is_string($configuration[$field]) || $configuration[$field] === '') {
                return false;
            }
        }
        return true;
    }
}
