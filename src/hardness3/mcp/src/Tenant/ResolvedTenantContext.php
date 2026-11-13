<?php

declare(strict_types=1);

namespace HardnessMcp\Tenant;

use InvalidArgumentException;

/**
 * Trusted tenant data resolved from the server-side host configuration.
 *
 * This object deliberately has no relation to MCP Tool arguments or the
 * public response contract. It is retained only inside the MCP runtime until
 * a future legacy bridge consumes it.
 */
final class ResolvedTenantContext
{
    private string $host;
    private string $configurationFile;
    private string $database;
    private string $databaseHost;
    private string $databaseUser;
    private string $databasePassword;
    private string $rootUrl;
    private string $pdfRenderUrl;

    /** @param array<string,string> $tenant */
    public static function fromResolvedTenant(array $tenant): self
    {
        foreach (['host', 'conf_file', 'dbDatabase', 'dbHost', 'dbUser', 'dbPass'] as $field) {
            if (!isset($tenant[$field]) || !is_string($tenant[$field])) {
                throw new InvalidArgumentException('Invalid resolved tenant context.');
            }
        }
        if ((isset($tenant['urlRaiz']) && !is_string($tenant['urlRaiz'])) || (isset($tenant['mcpPdfRenderUrl']) && !is_string($tenant['mcpPdfRenderUrl']))) {
            throw new InvalidArgumentException('Invalid resolved tenant context.');
        }

        return new self(
            $tenant['host'],
            $tenant['conf_file'],
            $tenant['dbDatabase'],
            $tenant['dbHost'],
            $tenant['dbUser'],
            $tenant['dbPass'],
            $tenant['urlRaiz'] ?? '',
            $tenant['mcpPdfRenderUrl'] ?? ($tenant['urlRaiz'] ?? '')
        );
    }

    private function __construct(string $host, string $configurationFile, string $database, string $databaseHost, string $databaseUser, string $databasePassword, string $rootUrl, string $pdfRenderUrl)
    {
        $this->host = $host;
        $this->configurationFile = $configurationFile;
        $this->database = $database;
        $this->databaseHost = $databaseHost;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->rootUrl = $rootUrl;
        $this->pdfRenderUrl = $pdfRenderUrl;
    }

    public function getHost(): string { return $this->host; }
    public function getConfigurationFile(): string { return $this->configurationFile; }
    public function getDatabase(): string { return $this->database; }
    public function getDatabaseHost(): string { return $this->databaseHost; }
    public function getDatabaseUser(): string { return $this->databaseUser; }
    public function getDatabasePassword(): string { return $this->databasePassword; }
    public function getRootUrl(): string { return $this->rootUrl; }
    /** Internal origin used only by wkhtmltopdf; public links always use getRootUrl(). */
    public function getPdfRenderUrl(): string { return $this->pdfRenderUrl; }
}
