<?php

declare(strict_types=1);

namespace HardnessMcp\Admin;

use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\OAuth\OAuthClientRepository;
use HardnessMcp\OAuth\OAuthGrantRepository;
use HardnessMcp\OAuth\PermissionResolver;
use HardnessMcp\Eligibility\McpConnectionEligibilityResolver;
use HardnessMcp\Scopes\ScopeCatalog;
use PDO;
use RuntimeException;
use Throwable;

/** Internal administration only. It deliberately exposes no OAuth protocol endpoint. */
final class OAuthAdminService
{
    private PDO $pdo; private OAuthClientRepository $clients; private OAuthGrantRepository $grants; private ScopeCatalog $catalog; private McpAuditService $audit;
    private string $host; private string $ip; private ?int $actor;
    public function __construct(PDO $pdo, string $host, string $ip, ?int $actor = null)
    {
        $this->pdo = $pdo; $this->clients = new OAuthClientRepository($pdo); $this->grants = new OAuthGrantRepository($pdo); $this->catalog = new ScopeCatalog(); $this->audit = new McpAuditService($pdo);
        $this->host = $host; $this->ip = $ip; $this->actor = $actor !== null && $actor > 0 ? $actor : null;
    }
    /** @param string[] $redirectUris @return array{id:int,client_id:string} */
    public function createClient(string $clientId, string $name, array $redirectUris): array
    {
        $clientId = trim($clientId); $name = trim($name);
        if ($clientId === '' || strlen($clientId) > 191 || $name === '' || strlen($name) > 150 || $redirectUris === []) { throw new RuntimeException('Invalid OAuth client.'); }
        if ($this->clients->findByClientId($clientId) !== null) { throw new RuntimeException('OAuth client already exists.'); }
        $this->pdo->beginTransaction();
        try {
            $id = $this->clients->create($clientId, 'pre_registered', $name);
            foreach ($redirectUris as $uri) { $this->clients->addRedirectUri($id, $this->redirectUri($uri)); }
            $this->audit->recordOAuthAdministrative('oauth_client_created', null, null, $id, null, null, $this->host, $this->ip, $this->actor);
            $this->pdo->commit(); return ['id' => $id, 'client_id' => $clientId];
        } catch (Throwable $exception) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } throw $exception; }
    }
    /** @param mixed $rawScopes */
    public function createGrant(int $companyId, int $connectionId, int $clientId, int $userId, $rawScopes): int
    {
        $connection = $this->grants->findConnection($connectionId);
        if ($companyId < 1 || $connection === null || (int) $connection['MCP001_C004_Id'] !== $companyId || ($connection['MCP001_Status'] ?? null) !== 'active' || !$this->grants->hasActiveClient($clientId) || !(new McpConnectionEligibilityResolver($this->pdo))->canAuthorizeConnection($userId, $connectionId)) { throw new RuntimeException('Invalid OAuth grant.'); }
        $connectionScopes = $this->catalog->normalize(json_decode((string) $connection['MCP001_Scopes'], true));
        $scopes = $this->catalog->normalize($rawScopes);
        if ($this->resolver()->resolve($connectionScopes, $scopes) !== $scopes) { throw new RuntimeException('OAuth grant scope exceeds MCP connection.'); }
        $this->pdo->beginTransaction();
        try { $grantId = $this->grants->create($connectionId, $clientId, $userId, $scopes); $this->audit->recordOAuthAdministrative('oauth_grant_created', $connectionId, $companyId, $clientId, $grantId, null, $this->host, $this->ip, $this->actor); $this->pdo->commit(); return $grantId; }
        catch (Throwable $exception) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } throw $exception; }
    }
    public function setClientStatus(int $clientId, string $status): void { $this->setStatus('MCP004', 'MCP004_Id', $clientId, $status, ['active','disabled','revoked'], 'oauth_client_' . $status); }
    public function renameClient(int $clientId, string $name): void
    {
        $name = trim($name);
        if ($clientId < 1 || $name === '' || strlen($name) > 150 || !$this->clients->rename($clientId, $name)) { throw new RuntimeException('Invalid OAuth client.'); }
        $this->audit->recordOAuthAdministrative('oauth_client_updated', null, null, $clientId, null, null, $this->host, $this->ip, $this->actor);
    }
    public function addRedirectUri(int $clientId, string $redirectUri): int
    {
        $client = $this->clients->findById($clientId);
        if ($client === null || $client['MCP004_Status'] === 'revoked') { throw new RuntimeException('Invalid OAuth client.'); }
        $redirectId = $this->clients->addRedirectUri($clientId, $this->redirectUri($redirectUri));
        $this->audit->recordOAuthAdministrative('oauth_redirect_uri_created', null, null, $clientId, null, null, $this->host, $this->ip, $this->actor);
        return $redirectId;
    }
    public function revokeRedirectUri(int $redirectUriId): void
    {
        $redirect = $this->clients->findRedirectUri($redirectUriId);
        if ($redirect === null || !$this->clients->revokeRedirectUri($redirectUriId)) { throw new RuntimeException('OAuth redirect URI not found.'); }
        $this->audit->recordOAuthAdministrative('oauth_redirect_uri_revoked', null, null, (int) $redirect['MCP009_MCP004_Id'], null, null, $this->host, $this->ip, $this->actor);
    }
    public function revokeGrant(int $companyId, int $connectionId, int $grantId, string $reason): void
    {
        $statement = $this->pdo->prepare('SELECT MCP005_MCP004_Id FROM MCP005 WHERE MCP005_Id=:grant AND MCP005_MCP001_Id=:connection FOR UPDATE');
        $this->pdo->beginTransaction();
        try { $statement->execute(['grant' => $grantId, 'connection' => $connectionId]); $row = $statement->fetch(); if (!is_array($row)) { throw new RuntimeException('OAuth grant not found.'); }
            $safeReason = substr($reason, 0, 255);
            $this->pdo->prepare("UPDATE MCP005 SET MCP005_Status='revoked',MCP005_Chave_Ativa=NULL,MCP005_Data_Revogacao=UTC_TIMESTAMP(),MCP005_Motivo_Revogacao=:reason WHERE MCP005_Id=:grant AND MCP005_Status='active'")->execute(['grant' => $grantId, 'reason' => $safeReason]);
            $this->revokeGrantArtifacts($grantId, $safeReason);
            $this->audit->recordOAuthAdministrative('oauth_grant_revoked', $connectionId, $companyId, (int) $row['MCP005_MCP004_Id'], $grantId, null, $this->host, $this->ip, $this->actor); $this->pdo->commit();
        } catch (Throwable $exception) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } throw $exception; }
    }
    /** Revokes only active grants that predate the latest connection scope expansion. */
    public function revokeOutdatedGrants(int $companyId, int $connectionId): int
    {
        if ($companyId < 1 || $connectionId < 1) { throw new RuntimeException('Invalid OAuth grant.'); }
        $this->pdo->beginTransaction();
        try {
            $connection = $this->pdo->prepare('SELECT MCP001_Scopes FROM MCP001 WHERE MCP001_Id=:connection AND MCP001_C004_Id=:company FOR UPDATE');
            $connection->execute(['connection' => $connectionId, 'company' => $companyId]);
            $row = $connection->fetch();
            if (!is_array($row)) { throw new RuntimeException('MCP connection not found.'); }
            $connectionScopes = $this->catalog->normalize(json_decode((string) $row['MCP001_Scopes'], true));
            $scopeUpdate = $this->pdo->prepare("SELECT MAX(MCP003_Data_Criacao) FROM MCP003 WHERE MCP003_MCP001_Id=:connection AND MCP003_Evento='connection_scopes_updated'");
            $scopeUpdate->execute(['connection' => $connectionId]);
            $updatedAt = $scopeUpdate->fetchColumn();
            if (!is_string($updatedAt) || $updatedAt === '') { $this->pdo->commit(); return 0; }

            $grants = $this->pdo->prepare("SELECT MCP005_Id,MCP005_MCP004_Id FROM MCP005 WHERE MCP005_MCP001_Id=:connection AND MCP005_Status='active' AND MCP005_Data_Consentimento<:updated_at FOR UPDATE");
            $grants->execute(['connection' => $connectionId, 'updated_at' => $updatedAt]);
            $grantScopes = $this->pdo->prepare('SELECT MCP010_Scope FROM MCP010 WHERE MCP010_MCP005_Id=:grant');
            $connectionScopeSet = array_fill_keys($connectionScopes, true);
            $revoked = 0;
            foreach ($grants->fetchAll() as $grant) {
                $grantId = (int) $grant['MCP005_Id'];
                $grantScopes->execute(['grant' => $grantId]);
                $grantedScopeSet = [];
                foreach ($grantScopes->fetchAll() as $scope) { $grantedScopeSet[(string) $scope['MCP010_Scope']] = true; }
                if (array_diff_key($connectionScopeSet, $grantedScopeSet) === []) { continue; }
                $this->pdo->prepare("UPDATE MCP005 SET MCP005_Status='revoked',MCP005_Chave_Ativa=NULL,MCP005_Data_Revogacao=UTC_TIMESTAMP(),MCP005_Motivo_Revogacao='permissions_updated' WHERE MCP005_Id=:grant AND MCP005_Status='active'")->execute(['grant' => $grantId]);
                $this->revokeGrantArtifacts($grantId, 'permissions_updated');
                $this->audit->recordOAuthAdministrative('oauth_grant_revoked_permissions_updated', $connectionId, $companyId, (int) $grant['MCP005_MCP004_Id'], $grantId, null, $this->host, $this->ip, $this->actor);
                $revoked++;
            }
            $this->pdo->commit();
            return $revoked;
        } catch (Throwable $exception) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } throw $exception; }
    }
    private function resolver(): PermissionResolver { return new PermissionResolver(); }
    private function redirectUri($uri): string
    {
        if (!is_string($uri) || strlen($uri) > 1024 || strpos($uri, '*') !== false || filter_var($uri, FILTER_VALIDATE_URL) === false) { throw new RuntimeException('Invalid redirect URI.'); }
        $parts = parse_url($uri);
        if (!is_array($parts) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])) { throw new RuntimeException('Invalid redirect URI.'); }
        if (($parts['scheme'] ?? null) === 'https') { return $uri; }
        if (($parts['scheme'] ?? null) !== 'http' || !in_array($parts['host'], ['127.0.0.1', '[::1]'], true) || !isset($parts['port']) || (int) $parts['port'] < 1 || (int) $parts['port'] > 65535) { throw new RuntimeException('Invalid redirect URI.'); }
        return $uri;
    }
    /** @param string[] $allowed */ private function setStatus(string $table, string $idField, int $id, string $status, array $allowed, string $event): void
    { if ($id < 1 || !in_array($status, $allowed, true)) { throw new RuntimeException('Invalid OAuth client status.'); } $this->pdo->beginTransaction(); try { $statement = $this->pdo->prepare("UPDATE $table SET MCP004_Status=:status,MCP004_Data_Alteracao=UTC_TIMESTAMP(),MCP004_Data_Revogacao=CASE WHEN :status='revoked' THEN UTC_TIMESTAMP() ELSE MCP004_Data_Revogacao END WHERE $idField=:id"); $statement->execute(['status' => $status, 'id' => $id]); if ($statement->rowCount() !== 1) { throw new RuntimeException('OAuth client not found.'); } if ($status === 'revoked') { $grants=$this->pdo->prepare('SELECT MCP005_Id FROM MCP005 WHERE MCP005_MCP004_Id=:client FOR UPDATE');$grants->execute(['client'=>$id]);foreach($grants->fetchAll() as $grant){$grantId=(int)$grant['MCP005_Id'];$this->pdo->prepare("UPDATE MCP005 SET MCP005_Status='revoked',MCP005_Chave_Ativa=NULL,MCP005_Data_Revogacao=UTC_TIMESTAMP(),MCP005_Motivo_Revogacao='client_revoked' WHERE MCP005_Id=:grant AND MCP005_Status='active'")->execute(['grant'=>$grantId]);$this->revokeGrantArtifacts($grantId,'client_revoked');} } $this->audit->recordOAuthAdministrative($event, null, null, $id, null, null, $this->host, $this->ip, $this->actor); $this->pdo->commit(); } catch (Throwable $exception) { if($this->pdo->inTransaction())$this->pdo->rollBack(); throw $exception; } }
    private function revokeGrantArtifacts(int $grantId,string $reason): void
    { $reason=substr($reason,0,255);$this->pdo->prepare("UPDATE MCP006 SET MCP006_Status='revoked' WHERE MCP006_MCP005_Id=:grant AND MCP006_Status='active'")->execute(['grant'=>$grantId]);$this->pdo->prepare("UPDATE MCP007 SET MCP007_Status='revoked',MCP007_Data_Revogacao=UTC_TIMESTAMP(),MCP007_Motivo_Revogacao=:reason WHERE MCP007_MCP005_Id=:grant AND MCP007_Status='active'")->execute(['grant'=>$grantId,'reason'=>$reason]);$this->pdo->prepare("UPDATE MCP008 SET MCP008_Status='revoked',MCP008_Data_Revogacao=UTC_TIMESTAMP(),MCP008_Motivo_Revogacao=:reason WHERE MCP008_MCP005_Id=:grant AND MCP008_Status IN ('active','used')")->execute(['grant'=>$grantId,'reason'=>$reason]); }
}
