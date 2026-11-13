<?php
declare(strict_types=1);
namespace HardnessMcp\OAuth;

use HardnessMcp\Eligibility\McpConnectionEligibilityResolver;
use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Scopes\ScopeCatalog;
use PDO;
use RuntimeException;

final class OAuthTokenEndpointException extends RuntimeException { public string $oauthError; public function __construct(string $oauthError, int $status=400){$this->oauthError=$oauthError;parent::__construct($oauthError,$status);} }

/** OAuth token exchange only. It intentionally has no MCP transport authentication behavior. */
final class OAuthTokenEndpointService
{
    private PDO $pdo; private array $config; private ScopeCatalog $catalog; private string $legacyResource;
    public function __construct(PDO $pdo,array $config,string $legacyResource=''){$this->pdo=$pdo;$this->config=$config;$this->catalog=new ScopeCatalog();$this->legacyResource=$legacyResource;}
    /** @param array<string,string> $input @return array<string,mixed> */
    public function exchange(array $input): array
    {
        $type=$input['grant_type']??''; if($type==='authorization_code')return $this->code($input);if($type==='refresh_token')return $this->refresh($input);throw new OAuthTokenEndpointException('unsupported_grant_type');
    }
    /** @param array<string,string> $in @return array<string,mixed> */
    private function code(array $in): array
    {
        foreach(['code','client_id','redirect_uri','code_verifier','resource'] as $k) $this->required($in,$k);
        [$selector,$secret]=$this->opaque($in['code']); $this->pdo->beginTransaction();
        try {
            $q=$this->pdo->prepare("SELECT z.*,g.MCP005_MCP001_Id,g.MCP005_C007_Id,g.MCP005_Status AS grant_status,g.MCP005_Data_Expiracao,c.MCP001_Scopes,c.MCP001_Status AS connection_status,c.MCP001_Data_Expiracao,u.C007_Ativo,cl.MCP004_Client_Id,cl.MCP004_Status AS client_status,ru.MCP009_Redirect_URI FROM MCP006 z INNER JOIN MCP005 g ON g.MCP005_Id=z.MCP006_MCP005_Id INNER JOIN MCP001 c ON c.MCP001_Id=g.MCP005_MCP001_Id INNER JOIN C007 u ON u.C007_Id=g.MCP005_C007_Id INNER JOIN MCP004 cl ON cl.MCP004_Id=z.MCP006_MCP004_Id INNER JOIN MCP009 ru ON ru.MCP009_Id=z.MCP006_MCP009_Id WHERE z.MCP006_Selector=:selector FOR UPDATE");$q->execute(['selector'=>$selector]);$r=$q->fetch();
            if(!is_array($r)||!$this->validSecret($secret,(string)$r['MCP006_Secret_Hash'],'authorization_code',$r['MCP006_Pepper_Version']))$this->fail('invalid_grant');
            if($r['MCP006_Status']!=='active'||strtotime((string)$r['MCP006_Data_Expiracao'])<=time()||$r['client_status']!=='active'){
                if(strtotime((string)$r['MCP006_Data_Expiracao'])<=time()){$this->audit('oauth_code_expired',(int)$r['MCP005_MCP001_Id'],(int)$r['MCP006_MCP004_Id'],(int)$r['MCP006_MCP005_Id'],null);$this->pdo->commit();throw new OAuthTokenEndpointException('invalid_grant');}
                $this->fail('invalid_grant');
            }
            if(!hash_equals((string)$r['MCP004_Client_Id'],$in['client_id']))$this->fail('invalid_client',401);
            if(!hash_equals((string)$r['MCP009_Redirect_URI'],$in['redirect_uri'])||!hash_equals((string)$r['MCP006_Resource'],$in['resource']))$this->fail('invalid_grant');
            $challenge=rtrim(strtr(base64_encode(hash('sha256',$in['code_verifier'],true)),'+/','-_'),'=');if(!hash_equals((string)$r['MCP006_PKCE_Challenge'],$challenge))$this->fail('invalid_grant');
            $scopes=$this->eligibleScopes($r,(int)$r['MCP006_Id']);
            $tokens=$this->issue((int)$r['MCP006_MCP005_Id'],(int)$r['MCP005_MCP001_Id'],(int)$r['MCP005_C007_Id'],(int)$r['MCP006_MCP004_Id'],(string)$r['MCP006_Resource'],$scopes,null);
            unset($tokens['_refresh_id']);
            $u=$this->pdo->prepare("UPDATE MCP006 SET MCP006_Status='consumed',MCP006_Data_Consumido=UTC_TIMESTAMP() WHERE MCP006_Id=:id AND MCP006_Status='active'");$u->execute(['id'=>$r['MCP006_Id']]);if($u->rowCount()!==1)$this->fail('invalid_grant');
            $this->audit('oauth_code_exchanged',(int)$r['MCP005_MCP001_Id'],(int)$r['MCP006_MCP004_Id'],(int)$r['MCP006_MCP005_Id'],null);
            $this->pdo->commit();return $tokens;
        }catch(\Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();if($e instanceof OAuthTokenEndpointException)$this->auditInvalidAttempt();throw $e;}
    }
    /** @param array<string,string> $in @return array<string,mixed> */
    private function refresh(array $in): array
    {
        foreach(['refresh_token','client_id','resource'] as $k)$this->required($in,$k);[$selector,$secret]=$this->opaque($in['refresh_token']);$this->pdo->beginTransaction();
        try {
            $q=$this->pdo->prepare("SELECT z.*,g.MCP005_MCP001_Id,g.MCP005_C007_Id,g.MCP005_Status AS grant_status,g.MCP005_Data_Expiracao,c.MCP001_Scopes,c.MCP001_Status AS connection_status,c.MCP001_Data_Expiracao,u.C007_Ativo,cl.MCP004_Client_Id,cl.MCP004_Status AS client_status,cl.MCP004_Id AS client_id FROM MCP008 z INNER JOIN MCP005 g ON g.MCP005_Id=z.MCP008_MCP005_Id INNER JOIN MCP001 c ON c.MCP001_Id=g.MCP005_MCP001_Id INNER JOIN C007 u ON u.C007_Id=g.MCP005_C007_Id INNER JOIN MCP004 cl ON cl.MCP004_Id=g.MCP005_MCP004_Id WHERE z.MCP008_Selector=:selector FOR UPDATE");$q->execute(['selector'=>$selector]);$r=$q->fetch();
            if(!is_array($r)||!$this->validSecret($secret,(string)$r['MCP008_Secret_Hash'],'refresh_token',$r['MCP008_Pepper_Version']))$this->fail('invalid_grant');
            if(!hash_equals((string)$r['MCP004_Client_Id'],$in['client_id']))$this->fail('invalid_client',401);if($r['client_status']!=='active')$this->fail('invalid_grant');
            if($r['MCP008_Status']!=='active'){if($r['MCP008_Status']==='used'){$this->revokeFamily((string)$r['MCP008_Familia_Id'],'refresh_reuse_detected');$this->audit('oauth_refresh_reuse_detected',(int)$r['MCP005_MCP001_Id'],(int)$r['client_id'],(int)$r['MCP008_MCP005_Id'],null);$this->pdo->commit();throw new OAuthTokenEndpointException('invalid_grant');}$this->fail('invalid_grant');}
            if(strtotime((string)$r['MCP008_Data_Expiracao'])<=time()){$this->audit('oauth_refresh_expired',(int)$r['MCP005_MCP001_Id'],(int)$r['client_id'],(int)$r['MCP008_MCP005_Id'],null);$this->pdo->commit();throw new OAuthTokenEndpointException('invalid_grant');}
            $resource=(string)($r['MCP008_Resource'] ?? '');
            // Refresh tokens issued before connection-specific resources existed have
            // no stored resource. They remain usable only at the legacy /mcp resource.
            if ($resource === '') $resource=$this->legacyResource;
            if ($resource === '' || !hash_equals($resource,$in['resource'])) $this->fail('invalid_grant');
            $r['MCP006_Resource']=$resource;$scopes=$this->eligibleScopes($r,null);
            $tokens=$this->issue((int)$r['MCP008_MCP005_Id'],(int)$r['MCP005_MCP001_Id'],(int)$r['MCP005_C007_Id'],(int)$r['client_id'],$resource,$scopes,(string)$r['MCP008_Familia_Id']);
            $newId=$tokens['_refresh_id'];unset($tokens['_refresh_id']);$u=$this->pdo->prepare("UPDATE MCP008 SET MCP008_Status='used',MCP008_Substituido_Por_MCP008_Id=:new WHERE MCP008_Id=:old AND MCP008_Status='active'");$u->execute(['new'=>$newId,'old'=>$r['MCP008_Id']]);if($u->rowCount()!==1)$this->fail('invalid_grant');$this->audit('oauth_refresh_rotated',(int)$r['MCP005_MCP001_Id'],(int)$r['client_id'],(int)$r['MCP008_MCP005_Id'],null);$this->pdo->commit();return $tokens;
        }catch(\Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();if($e instanceof OAuthTokenEndpointException)$this->auditInvalidAttempt();throw $e;}
    }
    /** @param array<string,mixed> $r @return string[] */
    private function eligibleScopes(array $r,?int $codeId): array
    {
        if($r['grant_status']!=='active'||$r['connection_status']!=='active'||$r['C007_Ativo']!=='S'||($r['MCP005_Data_Expiracao']!==null&&strtotime((string)$r['MCP005_Data_Expiracao'])<=time())||($r['MCP001_Data_Expiracao']!==null&&strtotime((string)$r['MCP001_Data_Expiracao'])<=time())||!(new McpConnectionEligibilityResolver($this->pdo))->canAuthorizeConnection((int)$r['MCP005_C007_Id'],(int)$r['MCP005_MCP001_Id']))$this->fail('invalid_grant');
        $grantId=(int)($r['MCP006_MCP005_Id'] ?? $r['MCP008_MCP005_Id']);$connection=$this->decodeScopes((string)$r['MCP001_Scopes']);$grant=$this->scopes('MCP010','MCP010_MCP005_Id',$grantId);$snapshot=$codeId===null?$grant:$this->scopes('MCP011','MCP011_MCP006_Id',$codeId);$allowed=(new PermissionResolver())->resolve($connection,$grant,[$snapshot]);if(count($allowed)!==count($snapshot))$this->fail('invalid_scope');return $allowed;
    }
    /** @param string[] $scopes @return array<string,mixed> */
    private function issue(int $grant,int $connection,int $user,int $client,string $resource,array $scopes,?string $family): array
    {
        $access=$this->secret('access_token');$a=$this->pdo->prepare("INSERT INTO MCP007 (MCP007_MCP005_Id,MCP007_Selector,MCP007_Secret_Hash,MCP007_Pepper_Version,MCP007_Resource,MCP007_Status,MCP007_Data_Expiracao) VALUES (:grant,:selector,:hash,:version,:resource,'active',DATE_ADD(UTC_TIMESTAMP(),INTERVAL :ttl SECOND))");$a->bindValue(':grant',$grant,PDO::PARAM_INT);$a->bindValue(':selector',$access['selector']);$a->bindValue(':hash',$access['hash'],PDO::PARAM_LOB);$a->bindValue(':version',$access['version']);$a->bindValue(':resource',$resource);$a->bindValue(':ttl',$this->ttl('access_token'),PDO::PARAM_INT);$a->execute();$accessId=(int)$this->pdo->lastInsertId();$ss=$this->pdo->prepare('INSERT INTO MCP012 (MCP012_MCP007_Id,MCP012_Scope) VALUES (:id,:scope)');foreach($scopes as $scope)$ss->execute(['id'=>$accessId,'scope'=>$scope]);
        $refresh=$this->secret('refresh_token');$family=$family??bin2hex(random_bytes(16));$rt=$this->pdo->prepare("INSERT INTO MCP008 (MCP008_MCP005_Id,MCP008_Familia_Id,MCP008_Selector,MCP008_Secret_Hash,MCP008_Pepper_Version,MCP008_Resource,MCP008_Status,MCP008_Data_Expiracao) VALUES (:grant,:family,:selector,:hash,:version,:resource,'active',DATE_ADD(UTC_TIMESTAMP(),INTERVAL :ttl SECOND))");$rt->bindValue(':grant',$grant,PDO::PARAM_INT);$rt->bindValue(':family',$family);$rt->bindValue(':selector',$refresh['selector']);$rt->bindValue(':hash',$refresh['hash'],PDO::PARAM_LOB);$rt->bindValue(':version',$refresh['version']);$rt->bindValue(':resource',$resource);$rt->bindValue(':ttl',$this->ttl('refresh_token'),PDO::PARAM_INT);$rt->execute();$refreshId=(int)$this->pdo->lastInsertId();
        return ['access_token'=>$access['selector'].'.'.$access['secret'],'token_type'=>'Bearer','expires_in'=>$this->ttl('access_token'),'refresh_token'=>$refresh['selector'].'.'.$refresh['secret'],'scope'=>implode(' ',$scopes),'_refresh_id'=>$refreshId];
    }
    /** @return array{selector:string,secret:string,hash:string,version:string} */ private function secret(string $kind): array {$version=(string)($this->config['oauth'][$kind.'_pepper_current_version']??'');$pepper=(string)(($this->config['oauth'][$kind.'_peppers']??[])[$version]??'');if($version===''||$pepper==='')throw new OAuthTokenEndpointException('temporarily_unavailable',503);$selector=rtrim(strtr(base64_encode(random_bytes(12)),'+/','-_'),'=');$secret=rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'),'=');return ['selector'=>$selector,'secret'=>$secret,'hash'=>hash_hmac('sha256',$secret,$pepper,true),'version'=>$version];}
    private function validSecret(string $secret,string $hash,string $kind,string $version): bool {$p=(string)(($this->config['oauth'][$kind.'_peppers']??[])[$version]??'');return $p!==''&&hash_equals($hash,hash_hmac('sha256',$secret,$p,true));}
    /** @return array{0:string,1:string} */ private function opaque(string $value): array {$p=explode('.',$value,2);if(count($p)!==2||preg_match('/\A[A-Za-z0-9_-]{16}\z/',$p[0])!==1||preg_match('/\A[A-Za-z0-9_-]{32,64}\z/',$p[1])!==1)$this->fail('invalid_grant');return $p;}
    private function required(array $in,string $key): void {if(!isset($in[$key])||!is_string($in[$key])||$in[$key]===''||strlen($in[$key])>4096)$this->fail('invalid_request');}
    /** @return string[] */ private function scopes(string $table,string $column,int $id): array {$name=$table==='MCP010'?'MCP010_Scope':'MCP011_Scope';$s=$this->pdo->prepare("SELECT $name FROM $table WHERE $column=:id ORDER BY $name");$s->execute(['id'=>$id]);return $this->catalog->normalize(array_column($s->fetchAll(),$name));}
    /** @return string[] */ private function decodeScopes(string $json): array {$v=json_decode($json,true);return $this->catalog->normalize(is_array($v)?$v:[]);}
    private function ttl(string $kind): int {$n=(int)($this->config['oauth'][$kind.'_ttl_seconds']??0);if($n<60||$n>2592000)throw new OAuthTokenEndpointException('temporarily_unavailable',503);return $n;}
    private function revokeFamily(string $family,string $reason): void {$s=$this->pdo->prepare("UPDATE MCP008 SET MCP008_Status='revoked',MCP008_Data_Revogacao=UTC_TIMESTAMP(),MCP008_Motivo_Revogacao=:reason WHERE MCP008_Familia_Id=:family AND MCP008_Status IN ('active','used')");$s->execute(['reason'=>$reason,'family'=>$family]);}
    private function audit(string $event,int $connection,int $client,int $grant,?int $token): void {(new McpAuditService($this->pdo))->recordOAuthAdministrative($event,$connection,null,$client,$grant,$token,'oauth-token','',null);}
    private function auditInvalidAttempt(): void { try { (new McpAuditService($this->pdo))->recordOAuthAdministrative('oauth_token_invalid',null,null,null,null,null,'oauth-token','',null); } catch (\Throwable $ignored) {} }
    private function fail(string $error,int $status=400): void {throw new OAuthTokenEndpointException($error,$status);}
}
