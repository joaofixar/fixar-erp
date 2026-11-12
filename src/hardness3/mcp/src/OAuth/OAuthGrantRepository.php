<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use PDO;

final class OAuthGrantRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<string,mixed>|null */
    public function findConnection(int $connectionId): ?array
    {
        $statement = $this->pdo->prepare('SELECT MCP001_Id,MCP001_C004_Id,MCP001_Scopes,MCP001_Status,MCP001_Data_Expiracao FROM MCP001 WHERE MCP001_Id=:id LIMIT 1');
        $statement->execute(['id' => $connectionId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    public function hasActiveClient(int $clientId): bool
    {
        $statement = $this->pdo->prepare("SELECT MCP004_Id FROM MCP004 WHERE MCP004_Id=:id AND MCP004_Status='active' LIMIT 1");
        $statement->execute(['id' => $clientId]);
        return is_array($statement->fetch());
    }

    public function hasActiveUserForCompany(int $userId, int $companyId): bool
    {
        $statement = $this->pdo->prepare("SELECT C007_Id FROM C007 WHERE C007_Id=:user_id AND C007_Ativo<>'N' LIMIT 1");
        $statement->execute(['user_id' => $userId]);
        return is_array($statement->fetch());
    }

    /** @param string[] $scopes */
    public function create(int $connectionId, int $clientId, int $userId, array $scopes): int
    {
        $statement = $this->pdo->prepare('INSERT INTO MCP005 (MCP005_MCP001_Id,MCP005_MCP004_Id,MCP005_C007_Id,MCP005_Chave_Ativa,MCP005_Status) VALUES (:connection_id,:client_id,:user_id,:active_key,\'active\')');
        $statement->execute(['connection_id' => $connectionId, 'client_id' => $clientId, 'user_id' => $userId, 'active_key' => self::activeKey($connectionId,$clientId,$userId)]);
        $grantId = (int) $this->pdo->lastInsertId();
        $scopeStatement = $this->pdo->prepare('INSERT INTO MCP010 (MCP010_MCP005_Id,MCP010_Scope) VALUES (:grant_id,:scope)');
        foreach ($scopes as $scope) { $scopeStatement->execute(['grant_id' => $grantId, 'scope' => $scope]); }
        return $grantId;
    }

    /** @return array<string,mixed>|null */
    public function findActiveForUpdate(int $connectionId,int $clientId,int $userId): ?array
    { $s=$this->pdo->prepare('SELECT MCP005_Id FROM MCP005 WHERE MCP005_Chave_Ativa=:key AND MCP005_Status=\'active\' FOR UPDATE');$s->execute(['key'=>self::activeKey($connectionId,$clientId,$userId)]);$r=$s->fetch();return is_array($r)?$r:null; }
    public static function activeKey(int $connectionId,int $clientId,int $userId): string { return $connectionId . ':' . $clientId . ':' . $userId; }

    /** @return string[] */
    public function scopes(int $grantId): array
    {
        $statement = $this->pdo->prepare('SELECT MCP010_Scope FROM MCP010 WHERE MCP010_MCP005_Id=:grant_id ORDER BY MCP010_Scope');
        $statement->execute(['grant_id' => $grantId]);
        $scopes = [];
        while (($row = $statement->fetch()) !== false) { if (is_array($row)) { $scopes[] = (string) $row['MCP010_Scope']; } }
        return $scopes;
    }
}
