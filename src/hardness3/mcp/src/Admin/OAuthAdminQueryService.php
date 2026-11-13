<?php

declare(strict_types=1);

namespace HardnessMcp\Admin;

use PDO;

/** Read-only OAuth administration queries; token material is never selected. */
final class OAuthAdminQueryService
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    /** @return array<int,array<string,mixed>> */
    public function listClients(): array
    {
        return $this->pdo->query('SELECT MCP004_Id AS id,MCP004_Client_Id AS client_id,MCP004_Nome AS name,MCP004_Origem AS origin,MCP004_Tipo AS type,MCP004_Status AS status,MCP004_Data_Criacao AS created_at FROM MCP004 ORDER BY MCP004_Nome,MCP004_Id')->fetchAll();
    }
    /** @return array<int,array<string,mixed>> */
    public function listGrantsForConnection(int $connectionId): array
    {
        $statement = $this->pdo->prepare('SELECT g.MCP005_Id AS id,g.MCP005_C007_Id AS user_id,g.MCP005_Status AS status,g.MCP005_Data_Consentimento AS consented_at,g.MCP005_Data_Expiracao AS expires_at,g.MCP004_Nome AS client_name FROM MCP005 g INNER JOIN MCP004 c ON c.MCP004_Id=g.MCP005_MCP004_Id WHERE g.MCP005_MCP001_Id=:connection ORDER BY g.MCP005_Id DESC');
        $statement->execute(['connection' => $connectionId]); return $statement->fetchAll();
    }
    /** @return array<int,array<string,mixed>> */
    public function listGrantsForUser(int $userId): array
    {
        $statement = $this->pdo->prepare('SELECT g.MCP005_Id AS id,g.MCP005_MCP001_Id AS connection_id,g.MCP005_Status AS status,g.MCP005_Data_Consentimento AS consented_at,c.MCP004_Nome AS client_name FROM MCP005 g INNER JOIN MCP004 c ON c.MCP004_Id=g.MCP005_MCP004_Id WHERE g.MCP005_C007_Id=:user ORDER BY g.MCP005_Id DESC');
        $statement->execute(['user' => $userId]); return $statement->fetchAll();
    }
    /** @return string[] */
    public function listGrantScopes(int $grantId): array
    {
        $statement = $this->pdo->prepare('SELECT MCP010_Scope FROM MCP010 WHERE MCP010_MCP005_Id=:grant ORDER BY MCP010_Scope'); $statement->execute(['grant' => $grantId]);
        return array_map(static function (array $row): string { return (string) $row['MCP010_Scope']; }, $statement->fetchAll());
    }
}
