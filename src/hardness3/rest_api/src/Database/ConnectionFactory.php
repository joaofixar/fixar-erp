<?php

declare(strict_types=1);

namespace RestApi\Database;

use PDO;
use PDOException;
use RestApi\Core\ApiException;

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
     * @param array<string, string> $tenantContext
     */
    public function create(array $tenantContext): PDO
    {
        $dbConfig = $this->appConfig['database'] ?? [];
        $charset = (string) ($dbConfig['charset'] ?? 'utf8mb4');
        $defaultPort = (int) ($dbConfig['default_port'] ?? 3306);
        $pdoOptions = (array) ($dbConfig['pdo_options'] ?? []);

        [$host, $port] = $this->parseHostAndPort($tenantContext['dbHost'], $defaultPort);
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $tenantContext['dbDatabase'],
            $charset
        );

        try {
            return new PDO($dsn, $tenantContext['dbUser'], $tenantContext['dbPass'], $pdoOptions);
        } catch (PDOException $exception) {
            throw new ApiException(
                'DB_CONNECTION_ERROR',
                'Não foi possível conectar ao banco do tenant.',
                500,
                ['host' => $tenantContext['host'], 'database' => $tenantContext['dbDatabase']]
            );
        }
    }

    /**
     * @return array{0:string,1:int}
     */
    private function parseHostAndPort(string $dbHost, int $defaultPort): array
    {
        $host = $dbHost;
        $port = $defaultPort;

        if (strpos($dbHost, ':') !== false && substr_count($dbHost, ':') === 1) {
            [$hostPart, $portPart] = explode(':', $dbHost, 2);
            if ($hostPart !== '') {
                $host = $hostPart;
            }

            if (ctype_digit($portPart)) {
                $port = (int) $portPart;
            }
        }

        return [$host, $port];
    }
}
