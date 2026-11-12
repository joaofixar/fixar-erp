<?php
declare(strict_types=1);
namespace HardnessMcp\OAuth;
use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Scopes\ScopeCatalog;
use PDO;
use RuntimeException;
use Throwable;

final class DcrValidationException extends RuntimeException
{
    public string $field;
    public string $internalCode;
    public function __construct(string $field, string $internalCode) { $this->field=$field; $this->internalCode=$internalCode; parent::__construct('Invalid client metadata.'); }
}

/** RFC 7591 subset: public clients only; no secret or client-management API. */
final class DynamicClientRegistrationService
{
    private PDO $pdo; private OAuthClientRepository $clients; private McpAuditService $audit; private string $host; private string $ip;
    public function __construct(PDO $pdo,string $host,string $ip) { $this->pdo=$pdo;$this->clients=new OAuthClientRepository($pdo);$this->audit=new McpAuditService($pdo);$this->host=$host;$this->ip=$ip; }
    /** @param array<string,mixed> $request @return array<string,mixed> */
    public function register(array $request): array
    {
        foreach ($request as $field => $_) { if (!in_array($field,['client_name','redirect_uris','grant_types','response_types','token_endpoint_auth_method','scope','application_type'],true) && !$this->isDiscardedStandardMetadata((string) $field)) throw new DcrValidationException((string) $field,'unsupported_metadata_field'); }
        $applicationType=$this->applicationType($request['application_type']??null); $name=$this->name($request['client_name']??null); $uris=$this->uris($request['redirect_uris']??null,$applicationType);
        $grants=$request['grant_types']??['authorization_code','refresh_token']; $responses=$request['response_types']??['code']; $method=$request['token_endpoint_auth_method']??'none';
        if ($grants !== ['authorization_code','refresh_token']) throw new DcrValidationException('grant_types','unsupported_grant_types');
        if ($responses !== ['code']) throw new DcrValidationException('response_types','unsupported_response_types');
        if ($method !== 'none') throw new DcrValidationException('token_endpoint_auth_method','unsupported_token_endpoint_auth_method');
        $scopes=array_key_exists('scope',$request)?$this->scopes($request['scope']):null;
        $this->pdo->beginTransaction();
        try { do { $clientId='hrd_dcr_'.rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'),'='); } while ($this->clients->findByClientId($clientId)!==null);
            $id=$this->clients->create($clientId,'dcr',$name,'public'); foreach($uris as $uri){$this->clients->addRedirectUri($id,$uri);} $this->audit->recordOAuthAdministrative('oauth_dcr_client_registered',null,null,$id,null,null,$this->host,$this->ip); $this->pdo->commit();
        } catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
        $response=['client_id'=>$clientId,'client_id_issued_at'=>time(),'client_name'=>$name,'redirect_uris'=>$uris,'response_types'=>['code'],'grant_types'=>['authorization_code','refresh_token'],'token_endpoint_auth_method'=>'none'];
        if ($scopes !== null) $response['scope']=implode(' ',$scopes);
        return $response;
    }
    private function name($name): string { if(!is_string($name)){throw new DcrValidationException('client_name','invalid_client_name_type');}$name=trim($name);if($name===''||strlen($name)>150||preg_match('/[\x00-\x1F\x7F]/',$name)||strpbrk($name,'<>')!==false)throw new DcrValidationException('client_name','invalid_client_name');return $name; }
    /** @return string[] */ private function uris($uris,?string $applicationType): array { if(!is_array($uris)||count($uris)<1||count($uris)>10||array_keys($uris)!==range(0,count($uris)-1))throw new DcrValidationException('redirect_uris','invalid_redirect_uris_shape');$seen=[];foreach($uris as $uri){if(!is_string($uri)||strlen($uri)>1024||isset($seen[$uri])||!$this->uri($uri,$applicationType==='web'))throw new DcrValidationException('redirect_uris','invalid_redirect_uri');$seen[$uri]=true;}return array_keys($seen); }
    private function uri(string $uri,bool $httpsOnly=false): bool { if(strpos($uri,'*')!==false)return false;$p=parse_url($uri);if(!is_array($p)||empty($p['scheme'])||empty($p['host'])||isset($p['user'])||isset($p['pass'])||isset($p['fragment']))return false;if($p['scheme']==='https')return true;if($httpsOnly||$p['scheme']!=='http'||!in_array($p['host'],['127.0.0.1','[::1]','::1'],true)||empty($p['port']))return false;return true; }
    private function applicationType($value): ?string { if($value===null)return null;if(!is_string($value)||$value!=='web')throw new DcrValidationException('application_type','unsupported_application_type');return 'web'; }
    /** @return string[] */ private function scopes($value): array { if(!is_string($value)||strlen($value)>1000)throw new DcrValidationException('scope','invalid_scope_type');$values=trim($value)===''?[]:preg_split('/ +/',trim($value));try{return (new ScopeCatalog())->normalize(is_array($values)?$values:[]);}catch(RuntimeException $exception){throw new DcrValidationException('scope','unknown_scope');} }
    private function isDiscardedStandardMetadata(string $field): bool
    {
        if (in_array($field, ['client_uri','logo_uri','tos_uri','policy_uri','contacts','software_id','software_version'], true)) return true;
        return preg_match('/\A(?:client_name|client_uri|logo_uri|tos_uri|policy_uri)#[A-Za-z0-9-]+\z/', $field) === 1;
    }
}
