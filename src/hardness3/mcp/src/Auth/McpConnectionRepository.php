<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

use PDO;

final class McpConnectionRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<string, mixed>|null */
    public function findActiveByCredentialPublicId(string $publicId): ?array
    {
        $sql = 'SELECT c.MCP001_Id, c.MCP001_C004_Id, c.MCP001_C007_Id, c.MCP001_Scopes, c.MCP001_Status, c.MCP001_Data_Expiracao, '
            . "((SELECT COUNT(*) FROM C007 actor INNER JOIN C019 membership ON membership.C019_C007_Id=actor.C007_Id INNER JOIN C004 actor_company ON actor_company.C004_Id=membership.C019_C004_Id AND actor_company.C004_Flag_Ativo='S' WHERE actor.C007_Id=c.MCP001_C007_Id AND actor.C007_Ativo='S' AND membership.C019_C021_Id>0 AND membership.C019_C004_Id=c.MCP001_C004_Id)=1) AS MCP001_Api_Key_Actor_Valid, "
            . 'k.MCP002_Id, k.MCP002_Secret_Hash, k.MCP002_Pepper_Version, k.MCP002_Status AS MCP002_Status, k.MCP002_Data_Expiracao '
            . 'FROM MCP002 k INNER JOIN MCP001 c ON c.MCP001_Id = k.MCP002_MCP001_Id '
            . 'INNER JOIN C004 company ON company.C004_Id = c.MCP001_C004_Id '
            . 'WHERE k.MCP002_Public_Id = :public_id LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['public_id' => $publicId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    public function registerCredentialUse(int $credentialId, string $ip): void
    {
        $statement = $this->pdo->prepare('UPDATE MCP002 SET MCP002_Data_Ultimo_Uso = CURRENT_TIMESTAMP, MCP002_IP_Ultimo_Uso = :ip WHERE MCP002_Id = :id');
        $statement->execute(['id' => $credentialId, 'ip' => substr($ip, 0, 45)]);
    }
}
