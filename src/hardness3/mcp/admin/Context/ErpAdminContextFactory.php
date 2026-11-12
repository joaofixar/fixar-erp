<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Context;

use HardnessMcp\AdminUi\Authorization\ErpAdminAuthorization;
use PDO;
use RuntimeException;

final class ErpAdminContextFactory
{
    private ErpAdminAuthorization $authorization;

    public function __construct(?ErpAdminAuthorization $authorization = null)
    {
        $this->authorization = $authorization ?? new ErpAdminAuthorization();
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public function createTenantContext(array $frameworkContext, array $connectionConfiguration): TenantAdminContext
    {
        $this->authorization->assertAuthorized($frameworkContext);
        $databaseName = trim((string) ($connectionConfiguration['dbDatabase'] ?? ''));
        if ($databaseName === '') {
            throw new RuntimeException('MCP administrative context is unavailable.');
        }

        return new TenantAdminContext(
            $this->normalizedHost((string) ($_SERVER['HTTP_HOST'] ?? '')),
            $databaseName,
            (int) $frameworkContext['usuarioAtual'],
            (int) $frameworkContext['nivelAlcada'],
            substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45)
        );
    }

    /** @param array<string,mixed> $connectionConfiguration */
    public function createPdo(array $connectionConfiguration): PDO
    {
        $host = trim((string) ($connectionConfiguration['dbHost'] ?? ''));
        $databaseName = trim((string) ($connectionConfiguration['dbDatabase'] ?? ''));
        $user = (string) ($connectionConfiguration['dbUser'] ?? '');
        $password = (string) ($connectionConfiguration['dbPass'] ?? '');
        if ($host === '' || $databaseName === '' || $user === '') {
            throw new RuntimeException('MCP administrative connection is unavailable.');
        }

        $dsn = 'mysql:host=' . $host . ';dbname=' . $databaseName . ';charset=utf8mb4';
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 3,
        ]);
    }

    private function normalizedHost(string $host): string
    {
        $host = strtolower(trim($host));
        if (preg_match('/^(.+):[0-9]{1,5}$/', $host, $matches) === 1) {
            $host = $matches[1];
        }
        return substr($host, 0, 253);
    }
}
