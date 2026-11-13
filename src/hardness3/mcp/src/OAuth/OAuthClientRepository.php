<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use PDO;

/** Persistence boundary only. OAuth HTTP and DCR flows are intentionally not implemented here. */
final class OAuthClientRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<string,mixed>|null */
    public function findByClientId(string $clientId): ?array
    {
        $statement = $this->pdo->prepare('SELECT MCP004_Id,MCP004_Client_Id,MCP004_Origem,MCP004_Tipo,MCP004_Nome,MCP004_Status FROM MCP004 WHERE MCP004_Client_Id=:client_id LIMIT 1');
        $statement->execute(['client_id' => $clientId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }
    /** @return array<string,mixed>|null */
    public function findActiveRedirectUri(string $clientId,string $uri): ?array
    { $s=$this->pdo->prepare("SELECT r.MCP009_Id,r.MCP009_Redirect_URI,c.MCP004_Id,c.MCP004_Nome FROM MCP004 c INNER JOIN MCP009 r ON r.MCP009_MCP004_Id=c.MCP004_Id WHERE c.MCP004_Client_Id=:client AND c.MCP004_Status='active' AND r.MCP009_Status='active' AND r.MCP009_URI_Hash=:hash AND r.MCP009_Redirect_URI=:uri LIMIT 1");$s->execute(['client'=>$clientId,'hash'=>hash('sha256',$uri,true),'uri'=>$uri]);$row=$s->fetch();return is_array($row)?$row:null; }

    /** @return array<string,mixed>|null */
    public function findById(int $clientId): ?array
    {
        $statement = $this->pdo->prepare('SELECT MCP004_Id,MCP004_Client_Id,MCP004_Nome,MCP004_Status FROM MCP004 WHERE MCP004_Id=:id LIMIT 1');
        $statement->execute(['id' => $clientId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    public function create(string $clientId, string $origin, string $name, string $type = 'public'): int
    {
        $statement = $this->pdo->prepare('INSERT INTO MCP004 (MCP004_Client_Id,MCP004_Origem,MCP004_Tipo,MCP004_Nome,MCP004_Status) VALUES (:client_id,:origin,:type,:name,\'active\')');
        $statement->execute(['client_id' => $clientId, 'origin' => $origin, 'type' => $type, 'name' => $name]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addRedirectUri(int $clientId, string $redirectUri): int
    {
        $statement = $this->pdo->prepare('INSERT INTO MCP009 (MCP009_MCP004_Id,MCP009_Redirect_URI,MCP009_URI_Hash,MCP009_Status) VALUES (:client_id,:uri,:hash,\'active\')');
        $statement->execute(['client_id' => $clientId, 'uri' => $redirectUri, 'hash' => hash('sha256', $redirectUri, true)]);
        return (int) $this->pdo->lastInsertId();
    }

    public function rename(int $clientId, string $name): bool
    {
        $statement = $this->pdo->prepare('UPDATE MCP004 SET MCP004_Nome=:name,MCP004_Data_Alteracao=UTC_TIMESTAMP() WHERE MCP004_Id=:id');
        $statement->execute(['id' => $clientId, 'name' => $name]);
        return $statement->rowCount() === 1;
    }

    /** @return array<string,mixed>|null */
    public function findRedirectUri(int $redirectUriId): ?array
    {
        $statement = $this->pdo->prepare('SELECT MCP009_Id,MCP009_MCP004_Id,MCP009_Status FROM MCP009 WHERE MCP009_Id=:id LIMIT 1');
        $statement->execute(['id' => $redirectUriId]);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    public function revokeRedirectUri(int $redirectUriId): bool
    {
        $statement = $this->pdo->prepare("UPDATE MCP009 SET MCP009_Status='revoked',MCP009_Data_Revogacao=UTC_TIMESTAMP() WHERE MCP009_Id=:id AND MCP009_Status='active'");
        $statement->execute(['id' => $redirectUriId]);
        return $statement->rowCount() === 1;
    }
}
