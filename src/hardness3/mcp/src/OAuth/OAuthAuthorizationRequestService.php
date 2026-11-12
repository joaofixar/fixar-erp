<?php
declare(strict_types=1);
namespace HardnessMcp\OAuth;
use HardnessMcp\Scopes\ScopeCatalog;
use PDO;
use RuntimeException;

/** Creates short-lived server-side authorization transactions; it never issues a grant or code. */
final class OAuthAuthorizationRequestService
{
    private PDO $pdo; private OAuthClientRepository $clients; private ScopeCatalog $catalog; private array $config;
    public function __construct(PDO $pdo,array $config){$this->pdo=$pdo;$this->clients=new OAuthClientRepository($pdo);$this->catalog=new ScopeCatalog();$this->config=$config;}
    /** @return array{cookie:string,selector:string} */
    public function begin(array $input,string $issuer,string $resource,?int $connectionId = null): array
    {
        // Temporary interoperability fallback: this authorization server exposes one canonical MCP resource.
        if (($input['resource'] ?? null) === null) $input['resource']=$resource;
        foreach(['response_type','client_id','redirect_uri','code_challenge','code_challenge_method','resource'] as $key){if(!isset($input[$key])||!is_string($input[$key]))throw new RuntimeException('invalid_request');}
        if($input['response_type']!=='code'||$input['code_challenge_method']!=='S256'||$input['resource']!==$resource)throw new RuntimeException('invalid_request');
        if(strlen($input['client_id'])>191||strlen($input['redirect_uri'])>1024||strlen($input['code_challenge'])<43||strlen($input['code_challenge'])>128||preg_match('/^[A-Za-z0-9_-]+$/',$input['code_challenge'])!==1)throw new RuntimeException('invalid_request');
        $state=$input['state']??null;if($state!==null&&(!is_string($state)||strlen($state)>2048))throw new RuntimeException('invalid_request');
        $scopes=$this->catalog->normalize($this->scopeList($input['scope']??'')); $redirect=$this->clients->findActiveRedirectUri($input['client_id'],$input['redirect_uri']);if($redirect===null)throw new RuntimeException('invalid_request');
        $oauth=$this->config['oauth']??[];$version=(string)($oauth['authorization_request_pepper_current_version']??'');$pepper=(string)(($oauth['authorization_request_peppers']??[])[$version]??'');if($version===''||$pepper==='')throw new RuntimeException('server_error');
        $selector=rtrim(strtr(base64_encode(random_bytes(18)),'+/','-_'),'=');$secret=rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'),'=');$hash=hash_hmac('sha256',$secret,$pepper,true);
        $this->pdo->beginTransaction();try{$s=$this->pdo->prepare("INSERT INTO MCP018 (MCP018_MCP004_Id,MCP018_MCP009_Id,MCP018_MCP001_Id,MCP018_Selector,MCP018_Secret_Hash,MCP018_Pepper_Version,MCP018_Issuer,MCP018_Resource,MCP018_State,MCP018_PKCE_Challenge,MCP018_PKCE_Method,MCP018_Data_Expiracao) VALUES (:client,:redirect,:connection,:selector,:hash,:version,:issuer,:resource,:state,:challenge,'S256',DATE_ADD(UTC_TIMESTAMP(),INTERVAL 5 MINUTE))");$s->execute(['client'=>$redirect['MCP004_Id'],'redirect'=>$redirect['MCP009_Id'],'connection'=>$connectionId,'selector'=>$selector,'hash'=>$hash,'version'=>$version,'issuer'=>$issuer,'resource'=>$resource,'state'=>$state,'challenge'=>$input['code_challenge']]);$id=(int)$this->pdo->lastInsertId();$scope=$this->pdo->prepare('INSERT INTO MCP019 (MCP019_MCP018_Id,MCP019_Scope) VALUES (:id,:scope)');foreach($scopes as $value)$scope->execute(['id'=>$id,'scope'=>$value]);$this->pdo->commit();}catch(\Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
        return ['cookie'=>$selector.'.'.$secret,'selector'=>$selector];
    }
    /** @return string[] */ private function scopeList($value): array {if(!is_string($value)||strlen($value)>1000)throw new RuntimeException('invalid_request');$values=$value===''?[]:preg_split('/ +/',trim($value));return is_array($values)?$values:[];}
}
