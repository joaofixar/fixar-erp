<?php

declare(strict_types=1);

use HardnessMcp\OAuth\PermissionResolver;
use HardnessMcp\OAuth\OAuthGrantConsentPolicy;
use HardnessMcp\OAuth\OAuthGrantRepository;
use HardnessMcp\OAuth\OAuthAuthorizationConnectionBinding;

$permissionResolver = new PermissionResolver();
mcpAssert(
    $permissionResolver->resolve(['company:read', 'product:read', 'quote:read'], ['product:read', 'quote:read']) === ['product:read', 'quote:read'],
    'OAuth permission resolver must limit grant scopes by MCP001 scopes'
);
mcpAssert(
    $permissionResolver->resolve(['company:read', 'product:read'], ['company:read', 'product:read'], [['product:read']]) === ['product:read'],
    'OAuth permission resolver must accept future restrictive policy layers without changing Tools'
);
mcpAssert(
    $permissionResolver->resolve(['company:read'], ['company:read', 'product:read']) === ['company:read'],
    'a requested scope absent from MCP001 must never be granted'
);

try {
    $permissionResolver->resolve(['company:read'], ['invalid scope']);
    throw new RuntimeException('invalid scope should have failed');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid MCP scope.', 'permission resolver must fail closed for invalid scopes');
}

$connectionBinding = new OAuthAuthorizationConnectionBinding();
$connectionA = ['id' => 101, 'name' => 'Empresa A'];
$connectionB = ['id' => 202, 'name' => 'Empresa B'];
mcpAssert($connectionBinding->eligibleConnections(101, [$connectionA, $connectionB]) === [$connectionA] && $connectionBinding->eligibleConnections(202, [$connectionA, $connectionB]) === [$connectionB], 'the same OAuth user must receive independently bound connection A and B authorization choices.');
mcpAssert($connectionBinding->eligibleConnections(null, [$connectionA, $connectionB]) === [$connectionA, $connectionB], 'legacy OAuth authorization must preserve the full connection selection list.');
mcpAssert($connectionBinding->confirm(101, 101) === 101 && $connectionBinding->confirm(202, 202) === 202, 'a connection-specific authorization must confirm its own connection.');
try { $connectionBinding->confirm(101, 202); throw new RuntimeException('connection A must not authorize connection B'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Connection is not eligible.', 'a connection-specific OAuth transaction must reject a different selected connection.'); }
$grantConsentPolicy = new OAuthGrantConsentPolicy();
mcpAssert(!$grantConsentPolicy->mustReplace(['company:read'], ['company:read']), 'a repeat authorization with the same scopes must reuse its active grant.');
mcpAssert(!$grantConsentPolicy->mustReplace(['company:read', 'product:read'], ['product:read', 'company:read']), 'scope order must not replace an equivalent grant.');
mcpAssert($grantConsentPolicy->mustReplace(['company:read'], ['company:read', 'product:read']), 'an explicit consented scope expansion must replace the grant snapshot.');
mcpAssert($grantConsentPolicy->mustReplace(['company:read', 'product:read'], ['company:read']), 'a scope reduction must replace the grant snapshot instead of retaining broader permission.');
mcpAssert(OAuthGrantRepository::activeKey(10, 20, 30) !== OAuthGrantRepository::activeKey(11, 20, 30) && OAuthGrantRepository::activeKey(10, 20, 30) !== OAuthGrantRepository::activeKey(10, 21, 30) && OAuthGrantRepository::activeKey(10, 20, 30) !== OAuthGrantRepository::activeKey(10, 20, 31), 'active grants must stay isolated by connection, OAuth client and ERP user.');

$migration = file_get_contents(dirname(__DIR__, 3) . '/scripts/atualizacaoBase.php');
mcpAssert($migration !== false, 'OAuth foundation migration must exist');
foreach (array_merge(range(4, 12), range(18, 19)) as $tableNumber) {
    mcpAssert(strpos((string) $migration, 'CREATE TABLE IF NOT EXISTS MCP' . sprintf('%03d', $tableNumber)) !== false, 'OAuth migration must create MCP' . sprintf('%03d', $tableNumber));
}
mcpAssert(strpos((string) $migration, 'JSON') === false || strpos((string) $migration, 'MCP004_Redirect_URIs_JSON') === false, 'OAuth migration must not use OAuth JSON columns');
mcpAssert(strpos((string) $migration, 'ALTER TABLE MCP003') === false, 'OAuth foundation must define MCP003 directly without ALTER TABLE migrations');
mcpAssert($migration !== false && strpos($migration, "MCP008_Resource VARCHAR(255) NULL DEFAULT NULL") !== false && strpos($migration, 'Criar_Campo("MCP008_Resource"') !== false && strpos($migration, "MCP001_Public_Id CHAR(24) CHARACTER SET ascii COLLATE ascii_bin NULL DEFAULT NULL") !== false && strpos($migration, "UNIQUE KEY U_MCP001_Public_Id (MCP001_Public_Id)") !== false && strpos($migration, 'Criar_Campo("MCP001_Public_Id"') !== false && strpos($migration, "UPDATE MCP001 SET MCP001_Public_Id") === false, "connection public identifiers must be unique for new connections while existing records remain unmodified.");

$bootstrapSource = file_get_contents(dirname(__DIR__, 2) . '/src/Core/Bootstrap.php');
$requestStoreSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthAuthorizationRequestService.php');
$consentSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthAuthorizationConsentService.php');
$bindingSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthAuthorizationConnectionBinding.php');
mcpAssert($bootstrapSource !== false && strpos($bootstrapSource, "'/oauth/register'") !== false && strpos($bootstrapSource, "'/oauth/authorize'") !== false && strpos($bootstrapSource, 'setcookie') !== false, 'OAuth 5.1 must start authorization with an opaque resume cookie');
mcpAssert($bootstrapSource !== false && strpos($bootstrapSource, "'/hardness3/outros/login/index.php?mcp_oauth_tx='") !== false, 'OAuth authorization must enter the legacy login directly with the opaque transaction selector');
mcpAssert($requestStoreSource !== false && strpos($requestStoreSource, 'MCP018') !== false && strpos($requestStoreSource, 'MCP019') !== false && strpos($requestStoreSource, 'MCP005') === false && strpos($requestStoreSource, 'MCP006') === false, 'OAuth 5.1 request store must not create grants or authorization codes');
mcpAssert($requestStoreSource !== false && strpos($requestStoreSource, 'MCP018_MCP001_Id') !== false && strpos($requestStoreSource, "'connection'=>\$connectionId") !== false && $consentSource !== false && strpos($consentSource, 'OAuthAuthorizationConnectionBinding') !== false && strpos($consentSource, 'MCP018_MCP001_Id IS NULL OR MCP018_MCP001_Id=:bound_connection') !== false && $bindingSource !== false, 'connection-specific OAuth authorization must bind MCP018 at request creation and enforce that binding during consent.');
mcpAssert($requestStoreSource !== false && strpos($requestStoreSource, "if ((\$input['resource'] ?? null) === null) \$input['resource']=\$resource;") !== false, 'authorization requests without resource must temporarily default only to the single canonical MCP resource');
mcpAssert($bootstrapSource !== false && strpos($bootstrapSource, 'legacyResource') !== false, 'token requests without resource must default to the preserved legacy MCP resource');
$dockerRewrite = file_get_contents(dirname(__DIR__, 3) . '/docker/.htaccess');
$rootRewrite = file_get_contents(dirname(__DIR__, 3) . '/.htaccess');
mcpAssert($dockerRewrite !== false && $rootRewrite !== false && strpos($dockerRewrite, 'oauth/authorize') !== false && strpos($rootRewrite, 'oauth/authorize') !== false && strpos($dockerRewrite, 'oauth/token') !== false && strpos($rootRewrite, 'oauth/token') !== false, 'OAuth authorization and token routes must bypass the ERP router in every supported deployment rewrite file');
$exclusiveMcpRule = 'RewriteRule ^mcp/c/[A-Za-z0-9_-]{24}$ /hardness3/mcp/public/index.php [END]'; $exclusiveMetadataRule = 'RewriteRule ^\.well-known/oauth-protected-resource/mcp/c/[A-Za-z0-9_-]{24}$ /hardness3/mcp/public/index.php [END]'; foreach ([$rootRewrite, $dockerRewrite] as $rewrite) { mcpAssert($rewrite !== false && strpos($rewrite, $exclusiveMcpRule) !== false && strpos($rewrite, $exclusiveMetadataRule) !== false && strpos($rewrite, $exclusiveMcpRule) < strpos($rewrite, 'RewriteCond %{SCRIPT_FILENAME} !-f'), 'connection-specific MCP and metadata rewrites must be strict and precede the ERP router.'); }
$mcpAccessRules = file_get_contents(dirname(__DIR__, 2) . '/.htaccess');
mcpAssert($mcpAccessRules !== false && strpos($mcpAccessRules, 'Options -Indexes') !== false && strpos($mcpAccessRules, '(?:config|docs|src|tests)') !== false && strpos($mcpAccessRules, 'admin/(?!assets/') !== false && strpos($mcpAccessRules, 'Require all denied') !== false, 'the MCP source tree must deny direct access to non-public implementation paths without blocking the existing ERP-admin assets when Apache allows project overrides.');
$legacyLoginSource = file_get_contents(dirname(__DIR__, 3) . '/outros/login/index.php');
mcpAssert($legacyLoginSource !== false && strpos($legacyLoginSource, "McpOAuthAuthorizationResume/?ajax=true&tx=") !== false, "the OAuth login continuation must remain on the current public host instead of using the legacy configured root");

$consentSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthAuthorizationConsentService.php');
$frameworkSource = file_get_contents(dirname(__DIR__, 2) . '/admin/FrameworkEntry.php');
mcpAssert($frameworkSource !== false && strpos($frameworkSource, "C004_Nome_Abreviado") !== false && strpos($frameworkSource, "Empresa conectada") !== false && strpos($frameworkSource, "Conexão:") !== false, "OAuth consent must prominently identify the abbreviated company and selected connection.");
mcpAssert($consentSource !== false && strpos($consentSource, 'McpConnectionEligibilityResolver') !== false && strpos($consentSource, 'canAuthorizeConnection') !== false && strpos($consentSource, 'PermissionResolver') !== false, 'OAuth 5.2 must exclusively revalidate the selected connection through the eligibility resolver and intersect scopes.');
mcpAssert($consentSource !== false && strpos($consentSource, 'MCP005') !== false && strpos($consentSource, 'MCP010') !== false && strpos($consentSource, 'MCP006') !== false && strpos($consentSource, 'MCP011') !== false && strpos($consentSource, "MCP018_Status='consumed'") !== false && strpos($consentSource, "MCP018_Status='cancelled'") !== false, 'OAuth 5.2 must atomically persist grants, code scope snapshots and consume or cancel MCP018.');
mcpAssert($consentSource !== false && strpos($consentSource, 'authorization_code_peppers') !== false && strpos($consentSource, "MCP006_Status,MCP006_Data_Expiracao") !== false && strpos($consentSource, "INTERVAL 5 MINUTE") !== false, 'Authorization codes must be opaque, pepper-hashed and short-lived.');
mcpAssert($frameworkSource !== false && strpos($frameworkSource, 'renderOAuthSelection') !== false && strpos($frameworkSource, 'renderOAuthConsent') !== false && strpos($frameworkSource, 'clearOAuthCookie') !== false && strpos($frameworkSource, "'access_denied'") !== false, 'OAuth 5.2 must render selection and consent, preserve per-transaction cookies and redirect cancellation safely.');

$tokenEndpointSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthTokenEndpointService.php');
$accessAuthenticatorSource = file_get_contents(dirname(__DIR__, 2) . '/src/Auth/OAuthAccessTokenAuthenticator.php');
mcpAssert($bootstrapSource !== false && strpos($bootstrapSource, "'/oauth/token'") !== false && strpos($bootstrapSource, 'application/x-www-form-urlencoded') !== false, 'OAuth 6 token endpoint must be a form-encoded POST route.');
mcpAssert($tokenEndpointSource !== false && strpos($tokenEndpointSource, 'SELECT z.*') !== false && strpos($tokenEndpointSource, 'FOR UPDATE') !== false && strpos($tokenEndpointSource, "MCP006_Status='consumed'") !== false, 'OAuth 6 must lock and consume authorization codes exactly once.');
mcpAssert($tokenEndpointSource !== false && strpos($tokenEndpointSource, 'MCP008_Resource') !== false && strpos($tokenEndpointSource, 'hash_equals') !== false && strpos($tokenEndpointSource, 'MCP007') !== false && strpos($tokenEndpointSource, 'MCP008') !== false && strpos($tokenEndpointSource, 'MCP012') !== false && strpos($tokenEndpointSource, 'refresh_reuse_detected') !== false, 'OAuth refresh tokens must persist and enforce their exact resource before rotating credentials.');
mcpAssert($tokenEndpointSource !== false && strpos($tokenEndpointSource, '$snapshot=$codeId===null?$grant') !== false && strpos($accessAuthenticatorSource, "MCP005_Status'] !== 'active'") !== false && strpos($accessAuthenticatorSource, 'MCP012') !== false, 'old refresh tokens and access tokens must remain bound to their old grant/token snapshots and cannot gain new scopes.');
$bridgeSource = file_get_contents(dirname(__DIR__, 2) . '/src/OAuth/OAuthAuthorizationBridgeService.php');
$adminOAuthSource = file_get_contents(dirname(__DIR__, 2) . '/src/Admin/OAuthAdminService.php');
mcpAssert($consentSource !== false && strpos($consentSource, 'r.MCP018_C007_Id IS NULL OR r.MCP018_C007_Id=:bound_user') !== false && strpos($consentSource, "u.C007_Id=:active_user") !== false && strpos($consentSource, "'active_user' => \$userId, 'selector' => \$selector, 'bound_user' => \$userId") !== false, 'the native PDO MCP018 lookup must bind each repeated user value with a distinct placeholder.');
mcpAssert($bridgeSource !== false && strpos($bridgeSource, 'MCP018_C007_Id IS NULL') !== false && strpos($bridgeSource, 'oauth_authorization_authenticated') !== false, 'the login bridge must bind an unclaimed transaction once and audit authentication without persisting the opaque secret.');
mcpAssert($consentSource !== false && strpos($consentSource, 'oauth_code_created') !== false && strpos($consentSource, 'oauth_authorization_cancelled') !== false && strpos($tokenEndpointSource, 'oauth_code_expired') !== false && strpos($tokenEndpointSource, 'oauth_refresh_expired') !== false && strpos($tokenEndpointSource, 'oauth_token_invalid') !== false, 'authorization lifecycle creation, cancellation, expiration and invalid attempts must be audited without recording raw credentials.');
mcpAssert($consentSource !== false && strpos($consentSource, 'OAuthGrantConsentPolicy') !== false && strpos($consentSource, 'superseded_by_new_consent') !== false && strpos($consentSource, 'MCP005_Chave_Ativa=NULL') !== false && strpos($consentSource, "MCP007_Status='revoked'") !== false && strpos($consentSource, "MCP008_Status='revoked'") !== false, 'a changed consent scope set must replace the grant and revoke its old code, access-token and refresh-token artifacts.');
mcpAssert($consentSource !== false && strpos($consentSource, 'revokeOtherActiveGrants') !== false && strpos($consentSource, 'MCP009_Redirect_URI=:redirect_uri') !== false && strpos($consentSource, 'oauth_grant_superseded_after_reconsent') !== false, 'a fresh consent must retire legacy active grants for the same user, application identity and connection.');
mcpAssert($consentSource !== false && preg_match("~UPDATE MCP005 SET MCP005_Chave_Ativa=NULL WHERE MCP005_Id=:grant AND MCP005_Chave_Ativa=:key AND MCP005_Status<>\\\\?[\x27\x22]active\\\\?[\x27\x22]~", $consentSource) === 1, "legacy revoked grants must release their obsolete active key so a new OAuth authorization can create a fresh grant");
mcpAssert($adminOAuthSource !== false && strpos($adminOAuthSource, 'revokeGrantArtifacts') !== false && strpos($adminOAuthSource, "MCP008_Status='revoked'") !== false && strpos($adminOAuthSource, "MCP006_Status='revoked'") !== false, 'grant and revoked-client administration must immediately revoke outstanding code, access and refresh artifacts.');


final class OAuthTokenIntegrationStatement extends PDOStatement
{
    private OAuthTokenIntegrationPdo $pdo; private string $sql; private array $bound=[]; private array $rows=[]; private int $affected=0;
    public function __construct(OAuthTokenIntegrationPdo $pdo,string $sql){$this->pdo=$pdo;$this->sql=$sql;}
    public function bindValue($parameter,$value,$type=PDO::PARAM_STR){$this->bound[ltrim((string)$parameter,":")]=$value;return true;}
    public function execute($parameters=null){$params=$this->bound;if(is_array($parameters))foreach($parameters as $key=>$value)$params[ltrim((string)$key,":")]=$value;[$this->rows,$this->affected]=$this->pdo->run($this->sql,$params);return true;}
    public function fetch($fetchStyle=null,$cursorOrientation=null,$cursorOffset=null){if(strpos($this->sql,"FROM MCP001 c WHERE")!==false)return ["MCP001_Id"=>1];return $this->rows[0]??false;}
    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array{return $this->rows;}
    public function rowCount(): int{return $this->affected;}
}
final class OAuthTokenIntegrationPdo extends PDO
{
    public array $issued=[]; private array $codes=[]; private bool $transaction=false; private int $lastId=700;
    public function __construct(){ $this->addCode("aaaaaaaaaaaaaaaa",101,100,"https://mcp.test/mcp/c/AbCdEfGhIjKlMnOpQrStUvWx");$this->addCode("bbbbbbbbbbbbbbbb",202,200,"https://mcp.test/mcp/c/ZyXwVuTsRqPoNmLkJiHgFeDc");$this->addCode("cccccccccccccccc",1,300,"https://mcp.test/mcp"); }
    private function addCode(string $selector,int $connection,int $grant,string $resource): void { $secret=str_repeat("a",32);$verifier=str_repeat("v",43);$this->codes[$selector]=["MCP006_Id"=>$grant+1,"MCP006_MCP005_Id"=>$grant,"MCP006_Secret_Hash"=>hash_hmac("sha256",$secret,"code-pepper",true),"MCP006_Pepper_Version"=>"p1","MCP006_Status"=>"active","MCP006_Data_Expiracao"=>"2099-01-01 00:00:00","MCP006_Resource"=>$resource,"MCP006_PKCE_Challenge"=>rtrim(strtr(base64_encode(hash("sha256",$verifier,true)),"+/","-_"),"="),"MCP005_MCP001_Id"=>$connection,"MCP005_C007_Id"=>9,"MCP005_Status"=>"active","grant_status"=>"active","MCP005_Data_Expiracao"=>null,"MCP001_Scopes"=>"[\"company:read\"]","connection_status"=>"active","MCP001_Data_Expiracao"=>null,"C007_Ativo"=>"S","MCP004_Client_Id"=>"client-test","MCP004_Id"=>4,"MCP006_MCP004_Id"=>4,"client_status"=>"active","MCP009_Redirect_URI"=>"https://client.test/callback"]; }
    public function beginTransaction(){ $this->transaction=true;return true; } public function commit(){ $this->transaction=false;return true; } public function rollBack(){ $this->transaction=false;return true; } public function inTransaction(){return $this->transaction;} public function lastInsertId($name=null){return (string)$this->lastId;} public function prepare($statement,$options=[]){return new OAuthTokenIntegrationStatement($this,(string)$statement);}
    public function run(string $sql,array $params): array { if(strpos($sql,"SELECT z.*,")===0)return [isset($this->codes[$params["selector"]??""])?[$this->codes[$params["selector"]]]:[],0];if(strpos($sql,"MCP001_Id=:connection")!==false)return [["MCP001_Id"=>(int)($params["connection"]??0)],0];if(strpos($sql,"SELECT MCP010_Scope")===0)return [["MCP010_Scope"=>"company:read"],0];if(strpos($sql,"SELECT MCP011_Scope")===0)return [["MCP011_Scope"=>"company:read"],0];if(strpos($sql,"INSERT INTO MCP007")===0||strpos($sql,"INSERT INTO MCP008")===0){$this->issued[]=["sql"=>$sql,"params"=>$params];$this->lastId++;return [[],1];}if(strpos($sql,"UPDATE MCP006 SET MCP006_Status")===0)return [[],1];return [[],1]; }
}
$tokenIntegrationConfig=["oauth"=>["authorization_code_peppers"=>["p1"=>"code-pepper"],"access_token_peppers"=>["p1"=>"access-pepper"],"access_token_pepper_current_version"=>"p1","refresh_token_peppers"=>["p1"=>"refresh-pepper"],"refresh_token_pepper_current_version"=>"p1","access_token_ttl_seconds"=>3600,"refresh_token_ttl_seconds"=>7200]];
$tokenIntegrationPdo=new OAuthTokenIntegrationPdo();$tokenIntegration=new \HardnessMcp\OAuth\OAuthTokenEndpointService($tokenIntegrationPdo,$tokenIntegrationConfig,"https://mcp.test/mcp");$exchange=function(string $selector,string $resource)use($tokenIntegration):array{return $tokenIntegration->exchange(["grant_type"=>"authorization_code","code"=>$selector.".".str_repeat("a",32),"client_id"=>"client-test","redirect_uri"=>"https://client.test/callback","code_verifier"=>str_repeat("v",43),"resource"=>$resource]);};
$resourceA="https://mcp.test/mcp/c/AbCdEfGhIjKlMnOpQrStUvWx";$resourceB="https://mcp.test/mcp/c/ZyXwVuTsRqPoNmLkJiHgFeDc";try{$exchange("aaaaaaaaaaaaaaaa",$resourceB);throw new RuntimeException("authorization code A accepted resource B");}catch(\HardnessMcp\OAuth\OAuthTokenEndpointException $exception){mcpAssert($exception->oauthError==="invalid_grant","authorization code A must reject resource B before token issuance");}
$tokensA=$exchange("aaaaaaaaaaaaaaaa",$resourceA);$tokensB=$exchange("bbbbbbbbbbbbbbbb",$resourceB);$legacyTokens=$exchange("cccccccccccccccc","https://mcp.test/mcp");mcpAssert(isset($tokensA["access_token"],$tokensB["access_token"],$legacyTokens["access_token"]),"authorization code exchange must issue tokens for A, B and the legacy resource");
$accessIssues=array_values(array_filter($tokenIntegrationPdo->issued,static function(array $entry):bool{return strpos($entry["sql"],"INSERT INTO MCP007")===0;}));$refreshIssues=array_values(array_filter($tokenIntegrationPdo->issued,static function(array $entry):bool{return strpos($entry["sql"],"INSERT INTO MCP008")===0;}));mcpAssert(($accessIssues[0]["params"]["resource"]??null)===$resourceA&&($accessIssues[0]["params"]["grant"]??null)===100&&($accessIssues[1]["params"]["resource"]??null)===$resourceB&&($accessIssues[1]["params"]["grant"]??null)===200&&($accessIssues[2]["params"]["resource"]??null)==="https://mcp.test/mcp"&&($accessIssues[2]["params"]["grant"]??null)===300,"authorization code exchanges must bind access tokens to the exact resource and grant connection for A, B and legacy /mcp");mcpAssert(($refreshIssues[0]["params"]["resource"]??null)===$resourceA&&($refreshIssues[1]["params"]["resource"]??null)===$resourceB&&($refreshIssues[2]["params"]["resource"]??null)==="https://mcp.test/mcp","authorization code exchanges must persist the exact resource on each refresh token");
