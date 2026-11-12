<?php

declare(strict_types=1);

namespace HardnessMcp\Database;

use HardnessMcp\Core\McpException;
use PDO;
use PDOException;

class ConnectionFactory
{
    /** @var array<string, mixed> */
    private array $appConfig;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(array $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @param array<string, string> $tenant
     */
    public function create(array $tenant): PDO
    {
        $database = $this->appConfig['database'] ?? [];
        $charset = (string) ($database['charset'] ?? 'utf8mb4');
        $defaultPort = (int) ($database['default_port'] ?? 3306);
        $options = (array) ($database['pdo_options'] ?? []);
        [$host, $port] = $this->parseHostAndPort($tenant['dbHost'], $defaultPort);

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $tenant['dbDatabase'], $charset),
                $tenant['dbUser'],
                $tenant['dbPass'],
                $options
            );
            // All MCP DATETIME values, CURRENT_TIMESTAMP and UTC_TIMESTAMP share UTC.
            $pdo->exec("SET time_zone = '+00:00'");
            return $pdo;
        } catch (PDOException $exception) {
            throw new McpException('Unable to connect to the tenant database.', -32603, null, 500);
        }
    }

    /**
     * @return array{0:string,1:int}
     */
    private function parseHostAndPort(string $configuredHost, int $defaultPort): array
    {
        if (substr_count($configuredHost, ':') !== 1) {
            return [$configuredHost, $defaultPort];
        }

        [$host, $port] = explode(':', $configuredHost, 2);
        if ($host !== '' && ctype_digit($port) && (int) $port > 0 && (int) $port <= 65535) {
            return [$host, (int) $port];
        }

        return [$configuredHost, $defaultPort];
    }
}
