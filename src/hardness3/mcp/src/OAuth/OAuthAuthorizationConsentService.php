<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use HardnessMcp\Eligibility\McpConnectionEligibilityResolver;
use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Scopes\ScopeCatalog;
use PDO;
use PDOException;
use RuntimeException;

/** Completes one authenticated MCP018 transaction. All browser choices are revalidated here. */
final class OAuthAuthorizationConsentService
{
    private PDO $pdo;
    private array $config;
    private ScopeCatalog $catalog;
    private OAuthAuthorizationConnectionBinding $binding;

    public function __construct(PDO $pdo, array $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
        $this->catalog = new ScopeCatalog();
        $this->binding = new OAuthAuthorizationConnectionBinding();
    }

    /** @return array<string,mixed> */
    public function authenticate(string $cookie, int $userId): array
    {
        $transaction = $this->verifiedTransaction($cookie, $userId, true);
        if ($transaction['MCP018_Status'] === 'pending') {
            $update = $this->pdo->prepare("UPDATE MCP018 SET MCP018_Status='authenticated',MCP018_C007_Id=:user,MCP018_Data_Autenticacao=UTC_TIMESTAMP() WHERE MCP018_Id=:id AND MCP018_Status='pending' AND MCP018_C007_Id IS NULL");
            $update->execute(['user' => $userId, 'id' => $transaction['MCP018_Id']]);
            if ($update->rowCount() !== 1) throw new RuntimeException('Invalid OAuth authorization continuation.');
            $transaction['MCP018_Status'] = 'authenticated';
            $transaction['MCP018_C007_Id'] = $userId;
            $this->audit('oauth_authorization_authenticated', null, (int) $transaction['MCP018_MCP004_Id'], null, $userId);
        }
        if ($transaction['MCP018_Status'] !== 'authenticated') throw new RuntimeException('Invalid OAuth authorization continuation.');
        return $transaction;
    }

    /** @return array<int,array<string,mixed>> */
    public function eligibleConnections(string $cookie, int $userId): array
    {
        $transaction = $this->authenticate($cookie, $userId);
        return $this->binding->eligibleConnections($this->boundConnectionId($transaction), (new McpConnectionEligibilityResolver($this->pdo))->listEligibleConnections($userId, (int) ($transaction['company_id'] ?? 0)));
    }

    /** @return array<string,mixed> */
    public function transaction(string $cookie, int $userId): array { return $this->authenticate($cookie, $userId); }

    /** @return array{requested:string[],effective:string[],connection:array<string,mixed>,user_name:string} */
    public function preview(string $cookie, int $userId, int $connectionId): array
    {
        $transaction = $this->authenticate($cookie, $userId);
        $connectionId = $this->binding->confirm($this->boundConnectionId($transaction), $connectionId);
        if (!(new McpConnectionEligibilityResolver($this->pdo))->canAuthorizeConnection($userId, $connectionId)) throw new RuntimeException('Connection is not eligible.');
        $connection = $this->connection($connectionId);
        $requested = $this->requestedScopesFromCookie($cookie, $userId);
        $name=$this->pdo->prepare('SELECT C007_Nome FROM C007 WHERE C007_Id=:id LIMIT 1');$name->execute(['id'=>$userId]);
        
        return ['requested'=>$requested,'effective'=>(new PermissionResolver())->resolve($this->connectionScopes($connection),$requested),'connection'=>$connection,'user_name'=>(string)$name->fetchColumn(),'client_name'=>(string)$transaction['client_name']];
    }

    /** @return array{code:string,redirect_uri:string,state:?string} */
    public function consent(string $cookie, int $userId, int $connectionId): array
    {
        $this->pdo->beginTransaction();
        try {
            $request = $this->verifiedTransaction($cookie, $userId, true, true);
            if ($request['MCP018_Status'] !== 'authenticated' || $connectionId < 1) throw new RuntimeException('Invalid OAuth authorization continuation.');
            $connectionId = $this->binding->confirm($this->boundConnectionId($request), $connectionId);
            $eligibility = new McpConnectionEligibilityResolver($this->pdo);
            if (!$eligibility->canAuthorizeConnection($userId, $connectionId)) throw new RuntimeException('Connection is not eligible.');
            $connection = $this->connection($connectionId);
            $requested = $this->requestedScopes((int) $request['MCP018_Id']);
            $effective = (new PermissionResolver())->resolve($this->connectionScopes($connection), $requested);
            $grant = $this->grant((int) $connection['MCP001_Id'], (int) $request['MCP018_MCP004_Id'], $userId, $effective);
            // Legacy data can contain more than one active grant for this same
            // user/application/connection. A fresh consent becomes the sole
            // authorization, so old tokens cannot remain usable.
            $this->revokeOtherActiveGrants((int) $connection['MCP001_Id'], (int) $request['MCP018_MCP004_Id'], $userId, $grant, (string) $request['MCP009_Redirect_URI']);
            // A reused grant may be broader, but a code is always narrowed to this consent.
            $code = $this->createCode($grant, (int) $request['MCP018_MCP004_Id'], (int) $request['MCP018_MCP009_Id'], (string) $request['MCP018_PKCE_Challenge'], (string) $request['MCP018_Resource'], $effective);
            $update = $this->pdo->prepare("UPDATE MCP018 SET MCP018_Status='consumed',MCP018_MCP001_Id=:connection,MCP018_Data_Consentimento=UTC_TIMESTAMP(),MCP018_Data_Consumo=UTC_TIMESTAMP() WHERE MCP018_Id=:id AND MCP018_Status='authenticated' AND (MCP018_MCP001_Id IS NULL OR MCP018_MCP001_Id=:bound_connection)");
            $update->execute(['connection' => $connectionId, 'bound_connection' => $connectionId, 'id' => $request['MCP018_Id']]);
            if ($update->rowCount() !== 1) throw new RuntimeException('OAuth request was already used.');
            $this->audit('oauth_code_created', $connectionId, (int) $request['MCP018_MCP004_Id'], $grant, $userId);
            $this->pdo->commit();
            return ['code' => $code, 'redirect_uri' => (string) $request['MCP009_Redirect_URI'], 'state' => $request['MCP018_State'] === null ? null : (string) $request['MCP018_State']];
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    /** @return array{redirect_uri:string,state:?string} */
    public function cancel(string $cookie, int $userId): array
    {
        $this->pdo->beginTransaction();
        try {
            $request = $this->verifiedTransaction($cookie, $userId, true, true);
            if (!in_array($request['MCP018_Status'], ['pending', 'authenticated'], true)) throw new RuntimeException('OAuth request was already used.');
            $update = $this->pdo->prepare("UPDATE MCP018 SET MCP018_Status='cancelled',MCP018_Data_Cancelamento=UTC_TIMESTAMP() WHERE MCP018_Id=:id AND MCP018_Status IN ('pending','authenticated')");
            $update->execute(['id' => $request['MCP018_Id']]);
            if ($update->rowCount() !== 1) throw new RuntimeException('OAuth request was already used.');
            $this->audit('oauth_authorization_cancelled', null, (int) $request['MCP018_MCP004_Id'], null, $userId);
            $this->pdo->commit();
            return ['redirect_uri' => (string) $request['MCP009_Redirect_URI'], 'state' => $request['MCP018_State'] === null ? null : (string) $request['MCP018_State']];
        } catch (\Throwable $exception) { if ($this->pdo->inTransaction()) $this->pdo->rollBack(); throw $exception; }
    }

    /** @return array<string,mixed> */
    private function verifiedTransaction(string $cookie, int $userId, bool $mustBeActive, bool $forUpdate = false): array
    {
        [$selector, $secret] = array_pad(explode('.', $cookie, 2), 2, '');
        if ($userId < 1 || preg_match('/\A[A-Za-z0-9_-]{16,48}\z/', $selector) !== 1 || preg_match('/\A[A-Za-z0-9_-]{32,64}\z/', $secret) !== 1) throw new RuntimeException('Invalid OAuth authorization continuation.');
        // Once authenticated, a browser transaction is permanently bound to that ERP user.
        // The NULL branch is required only for the first post-login authentication.
        $sql = "SELECT r.*,c.MCP004_Nome AS client_name,ru.MCP009_Redirect_URI,c.MCP004_Id AS client_id FROM MCP018 r INNER JOIN MCP004 c ON c.MCP004_Id=r.MCP018_MCP004_Id AND c.MCP004_Status='active' INNER JOIN MCP009 ru ON ru.MCP009_Id=r.MCP018_MCP009_Id AND ru.MCP009_MCP004_Id=c.MCP004_Id AND ru.MCP009_Status='active' INNER JOIN C007 u ON u.C007_Ativo='S' WHERE u.C007_Id=:active_user AND r.MCP018_Selector=:selector AND (r.MCP018_C007_Id IS NULL OR r.MCP018_C007_Id=:bound_user)";
        if ($mustBeActive) $sql .= ' AND r.MCP018_Data_Expiracao>UTC_TIMESTAMP()';
        if ($forUpdate) $sql .= ' FOR UPDATE';
        $statement = $this->pdo->prepare($sql); $statement->execute(['active_user' => $userId, 'selector' => $selector, 'bound_user' => $userId]); $row = $statement->fetch();
        if (!is_array($row)) throw new RuntimeException('Invalid OAuth authorization continuation.');
        $pepper = (string) (($this->config['oauth']['authorization_request_peppers'] ?? [])[$row['MCP018_Pepper_Version']] ?? '');
        if ($pepper === '' || !hash_equals((string) $row['MCP018_Secret_Hash'], hash_hmac('sha256', $secret, $pepper, true))) throw new RuntimeException('Invalid OAuth authorization continuation.');
        return $row;
    }

    /** @return array<string,mixed> */
    private function boundConnectionId(array $transaction): ?int
    {
        $id = $transaction["MCP018_MCP001_Id"] ?? null;
        return $id === null ? null : (int) $id;
    }

    private function connection(int $id): array { $s=$this->pdo->prepare("SELECT c.MCP001_Id,c.MCP001_Nome,c.MCP001_Scopes,e.C004_Nome_Empresa,e.C004_Nome_Abreviado FROM MCP001 c INNER JOIN C004 e ON e.C004_Id=c.MCP001_C004_Id WHERE c.MCP001_Id=:id AND c.MCP001_Status='active' AND (c.MCP001_Data_Expiracao IS NULL OR c.MCP001_Data_Expiracao>UTC_TIMESTAMP()) LIMIT 1 FOR UPDATE");$s->execute(['id'=>$id]);$r=$s->fetch();if(!is_array($r))throw new RuntimeException('Connection is not available.');return $r; }
    /** @return string[] */
    private function requestedScopes(int $id): array { $s=$this->pdo->prepare('SELECT MCP019_Scope FROM MCP019 WHERE MCP019_MCP018_Id=:id ORDER BY MCP019_Scope');$s->execute(['id'=>$id]);return $this->catalog->normalize(array_column($s->fetchAll(), 'MCP019_Scope')); }
    /** @return string[] */
    private function requestedScopesFromCookie(string $cookie,int $userId): array { $r=$this->verifiedTransaction($cookie,$userId,true);return $this->requestedScopes((int)$r['MCP018_Id']); }
    /** @return string[] */
    private function connectionScopes(array $connection): array { $scopes=json_decode((string)$connection['MCP001_Scopes'],true);return $this->catalog->normalize(is_array($scopes)?$scopes:[]); }
    private function grant(int $connectionId,int $clientId,int $userId,array $scopes): int
    {
        $key=OAuthGrantRepository::activeKey($connectionId,$clientId,$userId);
        try { return $this->createGrant($connectionId,$clientId,$userId,$key,$scopes); }
        catch (PDOException $exception) {
            if (($exception->errorInfo[0] ?? '') !== '23000') throw $exception;
            // Older revoked grants may predate clearing the unique active key.
            // Lock that historical row too, release its key and continue safely.
            $s=$this->pdo->prepare("SELECT MCP005_Id,MCP005_Status FROM MCP005 WHERE MCP005_Chave_Ativa=:key FOR UPDATE");$s->execute(['key'=>$key]);$row=$s->fetch();if(!is_array($row))throw $exception;
            if ($row['MCP005_Status'] !== 'active') {
                $release=$this->pdo->prepare('UPDATE MCP005 SET MCP005_Chave_Ativa=NULL WHERE MCP005_Id=:grant AND MCP005_Chave_Ativa=:key AND MCP005_Status<>\'active\'');$release->execute(['grant'=>$row['MCP005_Id'],'key'=>$key]);
                if($release->rowCount()!==1)throw new RuntimeException('OAuth grant was changed concurrently.');
                return $this->createGrant($connectionId,$clientId,$userId,$key,$scopes);
            }
            $oldGrant=(int)$row['MCP005_Id'];
            if (!(new OAuthGrantConsentPolicy())->mustReplace($this->grantScopes($oldGrant),$scopes)) return $oldGrant;
            $this->revokeSupersededGrant($oldGrant);
            $newGrant=$this->createGrant($connectionId,$clientId,$userId,$key,$scopes);
            $this->audit('oauth_grant_replaced_after_consent',$connectionId,$clientId,$newGrant,$userId);
            return $newGrant;
        }
    }
    /** @param string[] $scopes */
    private function createGrant(int $connectionId,int $clientId,int $userId,string $key,array $scopes): int
    { $s=$this->pdo->prepare("INSERT INTO MCP005 (MCP005_MCP001_Id,MCP005_MCP004_Id,MCP005_C007_Id,MCP005_Chave_Ativa,MCP005_Status) VALUES (:connection,:client,:user,:key,'active')");$s->execute(['connection'=>$connectionId,'client'=>$clientId,'user'=>$userId,'key'=>$key]);$id=(int)$this->pdo->lastInsertId();$scope=$this->pdo->prepare('INSERT INTO MCP010 (MCP010_MCP005_Id,MCP010_Scope) VALUES (:grant,:scope)');foreach($scopes as $value)$scope->execute(['grant'=>$id,'scope'=>$value]);return $id; }
    /** @return string[] */
    private function grantScopes(int $grant): array
    { $s=$this->pdo->prepare('SELECT MCP010_Scope FROM MCP010 WHERE MCP010_MCP005_Id=:grant ORDER BY MCP010_Scope');$s->execute(['grant'=>$grant]);return $this->catalog->normalize(array_column($s->fetchAll(),'MCP010_Scope')); }
    private function revokeSupersededGrant(int $grant): void
    {
        // NULL releases U_MCP005_Chave_Ativa for the new grant while preserving
        // the revoked grant and its immutable MCP010 consent snapshot for audit.
        $reason='superseded_by_new_consent';
        $s=$this->pdo->prepare("UPDATE MCP005 SET MCP005_Status='revoked',MCP005_Chave_Ativa=NULL,MCP005_Data_Revogacao=UTC_TIMESTAMP(),MCP005_Motivo_Revogacao=:reason WHERE MCP005_Id=:grant AND MCP005_Status='active'");$s->execute(['grant'=>$grant,'reason'=>$reason]);if($s->rowCount()!==1)throw new RuntimeException('OAuth grant was changed concurrently.');
        $this->pdo->prepare("UPDATE MCP006 SET MCP006_Status='revoked' WHERE MCP006_MCP005_Id=:grant AND MCP006_Status='active'")->execute(['grant'=>$grant]);
        $this->pdo->prepare("UPDATE MCP007 SET MCP007_Status='revoked',MCP007_Data_Revogacao=UTC_TIMESTAMP(),MCP007_Motivo_Revogacao=:reason WHERE MCP007_MCP005_Id=:grant AND MCP007_Status='active'")->execute(['grant'=>$grant,'reason'=>$reason]);
        $this->pdo->prepare("UPDATE MCP008 SET MCP008_Status='revoked',MCP008_Data_Revogacao=UTC_TIMESTAMP(),MCP008_Motivo_Revogacao=:reason WHERE MCP008_MCP005_Id=:grant AND MCP008_Status IN ('active','used')")->execute(['grant'=>$grant,'reason'=>$reason]);
    }
    private function revokeOtherActiveGrants(int $connectionId,int $clientId,int $userId,int $currentGrant,string $redirectUri): void
    {
        $client=$this->pdo->prepare('SELECT MCP004_Origem,MCP004_Tipo FROM MCP004 WHERE MCP004_Id=:client FOR UPDATE');$client->execute(['client'=>$clientId]);$clientRow=$client->fetch();
        if (!is_array($clientRow)) throw new RuntimeException('OAuth client was not found.');
        if (($clientRow['MCP004_Origem']??'')==='dcr' && ($clientRow['MCP004_Tipo']??'')==='public') {
            // DCR public clients such as Claude can register a new client_id at
            // every connection. Their exact registered callback is the stable
            // application identity for this cleanup operation.
            $s=$this->pdo->prepare("SELECT DISTINCT g.MCP005_Id FROM MCP005 g INNER JOIN MCP004 c ON c.MCP004_Id=g.MCP005_MCP004_Id AND c.MCP004_Origem='dcr' AND c.MCP004_Tipo='public' INNER JOIN MCP009 r ON r.MCP009_MCP004_Id=c.MCP004_Id AND r.MCP009_Status='active' AND r.MCP009_Redirect_URI=:redirect_uri WHERE g.MCP005_MCP001_Id=:connection AND g.MCP005_C007_Id=:user AND g.MCP005_Status='active' AND g.MCP005_Id<>:current FOR UPDATE");
            $s->execute(['connection'=>$connectionId,'user'=>$userId,'current'=>$currentGrant,'redirect_uri'=>$redirectUri]);
        } else {
            $s=$this->pdo->prepare("SELECT MCP005_Id FROM MCP005 WHERE MCP005_MCP001_Id=:connection AND MCP005_MCP004_Id=:client AND MCP005_C007_Id=:user AND MCP005_Status='active' AND MCP005_Id<>:current FOR UPDATE");
            $s->execute(['connection'=>$connectionId,'client'=>$clientId,'user'=>$userId,'current'=>$currentGrant]);
        }
        foreach ($s->fetchAll() as $row) {
            $grant=(int)$row['MCP005_Id'];
            $this->revokeSupersededGrant($grant);
            $this->audit('oauth_grant_superseded_after_reconsent',$connectionId,$clientId,$grant,$userId);
        }
    }
    /** @param string[] $scopes */
    private function createCode(int $grant,int $client,int $redirect,string $challenge,string $resource,array $scopes): string
    {
        $oauth=$this->config['oauth']??[];$version=(string)($oauth['authorization_code_pepper_current_version']??'');$pepper=(string)(($oauth['authorization_code_peppers']??[])[$version]??'');if($version===''||$pepper==='')throw new RuntimeException('OAuth code configuration is unavailable.');
        $selector=rtrim(strtr(base64_encode(random_bytes(12)),'+/','-_'),'=');$secret=rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'),'=');
        $s=$this->pdo->prepare("INSERT INTO MCP006 (MCP006_MCP005_Id,MCP006_MCP004_Id,MCP006_MCP009_Id,MCP006_Selector,MCP006_Secret_Hash,MCP006_Pepper_Version,MCP006_PKCE_Challenge,MCP006_PKCE_Method,MCP006_Resource,MCP006_Status,MCP006_Data_Expiracao) VALUES (:grant,:client,:redirect,:selector,:hash,:version,:challenge,'S256',:resource,'active',DATE_ADD(UTC_TIMESTAMP(),INTERVAL 5 MINUTE))");$s->execute(['grant'=>$grant,'client'=>$client,'redirect'=>$redirect,'selector'=>$selector,'hash'=>hash_hmac('sha256',$secret,$pepper,true),'version'=>$version,'challenge'=>$challenge,'resource'=>$resource]);$id=(int)$this->pdo->lastInsertId();$scope=$this->pdo->prepare('INSERT INTO MCP011 (MCP011_MCP006_Id,MCP011_Scope) VALUES (:code,:scope)');foreach($scopes as $value)$scope->execute(['code'=>$id,'scope'=>$value]);return $selector.'.'.$secret;
    }
    private function audit(string $event,?int $connection,?int $client,?int $grant,int $user): void
    { (new McpAuditService($this->pdo))->recordOAuthAdministrative($event,$connection,null,$client,$grant,null,'oauth-authorization','', $user); }
}
