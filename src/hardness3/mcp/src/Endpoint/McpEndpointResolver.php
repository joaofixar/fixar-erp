<?php

declare(strict_types=1);

namespace HardnessMcp\Endpoint;

use HardnessMcp\Core\HttpException;
use PDO;

/** Resolves the legacy endpoint or an active connection-specific MCP endpoint. */
final class McpEndpointResolver
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Returns null for the legacy /mcp endpoint, otherwise the resolved MCP001 id. */
    public function resolve(string $path): ?int
    {
        if ($path === '/mcp') {
            return null;
        }
        if (preg_match('#\A/mcp/c/([A-Za-z0-9_-]{24})\z#', $path, $matches) !== 1) {
            throw new HttpException(404);
        }

        $statement = $this->pdo->prepare("SELECT MCP001_Id FROM MCP001 WHERE MCP001_Public_Id=:public_id AND MCP001_Status='active' AND (MCP001_Data_Expiracao IS NULL OR MCP001_Data_Expiracao>UTC_TIMESTAMP()) LIMIT 1");
        $statement->execute(['public_id' => $matches[1]]);
        $row = $statement->fetch();
        if (!is_array($row)) {
            throw new HttpException(404);
        }
        return (int) $row['MCP001_Id'];
    }
}
