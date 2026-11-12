<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/autoload.php';

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\Tools\Catalog\QuoteToolsModule;
use HardnessMcp\Tools\Catalog\ToolCatalogFactory;
use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\Quote\Item\QuoteItemGatewayInterface;
use HardnessMcp\Tools\ToolExecutor;
use HardnessMcp\Auth\OAuthAccessTokenAuthenticator;
use HardnessMcp\Core\Bootstrap;
use HardnessMcp\Core\Request;
use HardnessMcp\RateLimit\RateLimitDecision;
use HardnessMcp\RateLimit\RateLimiterInterface;
use HardnessMcp\RateLimit\RateLimitPolicy;

final class QuoteItemOAuthStatement extends PDOStatement
{
    private QuoteItemOAuthPdo $pdo; private string $sql; private array $rows=[];
    public function __construct(QuoteItemOAuthPdo $pdo,string $sql){$this->pdo=$pdo;$this->sql=$sql;}
    public function execute($parameters=null){$this->rows=$this->pdo->rows($this->sql);return true;}
    public function fetch($fetchStyle=null,$cursorOrientation=null,$cursorOffset=null){return $this->rows[0]??false;}
    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array{return $this->rows;}
}
final class QuoteItemOAuthPdo extends PDO
{
    private bool $actorValid;
    public function __construct(bool $actorValid){$this->actorValid=$actorValid;}
    public function prepare($statement,$options=[]){return new QuoteItemOAuthStatement($this,$statement);}
    public function rows(string $sql): array
    {
        if(strpos($sql,'SELECT t.MCP007_Id')===0)return [[
            'MCP007_Id'=>6,'MCP007_Secret_Hash'=>hash_hmac('sha256',str_repeat('a',32),'oauth-test-pepper',true),'MCP007_Pepper_Version'=>'p1','MCP007_Resource'=>'https://mcp.test/mcp','MCP007_Status'=>'active','MCP007_Data_Expiracao'=>null,
            'MCP005_Id'=>5,'MCP005_Status'=>'active','MCP005_Data_Expiracao'=>null,'MCP005_MCP001_Id'=>1,'MCP005_C007_Id'=>9,'MCP005_MCP004_Id'=>4,'MCP001_C004_Id'=>3,'MCP001_Scopes'=>'["quote:item:update","quote:item:delete"]','MCP001_Status'=>'active','MCP001_Data_Expiracao'=>null,'C007_Ativo'=>'S','MCP004_Status'=>'active','MCP005_OAuth_Actor_Valid'=>$this->actorValid?1:0
        ]];
        if(strpos($sql,'SELECT MCP010_Scope')===0)return [['MCP010_Scope'=>'quote:item:update'],['MCP010_Scope'=>'quote:item:delete']];
        if(strpos($sql,'SELECT MCP012_Scope')===0)return [['MCP012_Scope'=>'quote:item:update'],['MCP012_Scope'=>'quote:item:delete']];
        if(strpos($sql,'SELECT c.MCP001_Id')===0)return $this->actorValid?[['MCP001_Id'=>1]]:[];
        return [];
    }
}

function qiAssert(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }

final class QuoteItemGatewayFixture implements QuoteItemGatewayInterface
{
    public ?array $updateArguments = null; public ?int $deleteId = null; public bool $fail = false; public ?string $updateFailure = null; public ?string $deleteFailure = null;
    public ?array $addArguments = null; public ?array $addResult = null; public ?string $addFailure = null;
    public function add(array $arguments, ExecutionContext $context): array { if ($this->addFailure !== null) throw new RuntimeException($this->addFailure); $this->addArguments=$arguments; return $this->addResult ?? ['quote_id'=>(int)$arguments['quote_id'],'status'=>'completed','items'=>array_map(static function(array $item,int $offset):array{return ['index'=>$offset+1,'product_id'=>(int)$item['product_id'],'requested_quantity'=>$item['quantity'],'status'=>'included','t004_id'=>900+$offset,'item_order'=>4+$offset];},$arguments['items'],array_keys($arguments['items']))]; }
    public function update(array $arguments, ExecutionContext $context): array { if ($this->updateFailure !== null) throw new RuntimeException($this->updateFailure); if ($this->fail) throw new RuntimeException('quote_item_unavailable'); $this->updateArguments=$arguments; $value=($arguments['field']??'')==='location_code'?strtoupper(trim((string)($arguments['location_code']??''))):(string)($arguments['value']??''); return ['t004_id'=>(int)$arguments['t004_id'],'quote_id'=>7,'field'=>(string)$arguments['field'],'value'=>$value]; }
    public function delete(int $t004Id, ExecutionContext $context): array { if ($this->deleteFailure !== null) throw new RuntimeException($this->deleteFailure); if ($this->fail) throw new RuntimeException('quote_item_unavailable'); $this->deleteId=$t004Id; return ['t004_id'=>$t004Id,'quote_id'=>7,'deleted'=>true]; }
}
final class QuoteItemRepositoryFixture implements QuoteRepositoryInterface
{
    public function search(int $companyId,string $from,string $to,?int $customerId,?string $origin,int $limit): array { return []; }
    public function findById(int $companyId,int $quoteId): ?array {
        return ['header'=>['T003_Id'=>$quoteId,'T003_Data_Emissao'=>'2026-08-27','status_label'=>'Pendente','T003_Valor_Total'=>'10.00','D024_Id'=>1,'D024_Nome_Fantasia'=>'Cliente'], 'items'=>[
            ['T004_Id'=>81,'T004_Item'=>1,'T004_Descricao_Produto'=>'Produto','T004_Codigo_Local'=>'COZINHA','T004_Quantidade'=>'1','T004_Valor_Preco_Unitario'=>'10','T004_Valor_Total_Preco'=>'10','D001_Id'=>2,'D001_Codigo_Produto'=>'P1','D037_Unidade'=>'UN']
        ]];
    }
}

/** Captures the actual prepared MCP003 INSERT emitted by McpAuditService. */
final class QuoteItemAuditStatement extends PDOStatement
{
    private QuoteItemAuditPdo $pdo; private string $sql;
    public function __construct(QuoteItemAuditPdo $pdo, string $sql) { $this->pdo=$pdo; $this->sql=$sql; }
    public function execute($parameters=null): bool { $this->pdo->record($this->sql, is_array($parameters) ? $parameters : []); return true; }
}
final class QuoteItemAuditPdo extends PDO
{
    /** @var array<int,array{sql:string,params:array<string,mixed>}> */ public array $executed=[];
    public function __construct() {}
    public function prepare($statement, $options=[]) { return new QuoteItemAuditStatement($this, (string)$statement); }
    /** @param array<string,mixed> $params */ public function record(string $sql, array $params): void { $this->executed[]=['sql'=>$sql,'params'=>$params]; }
    /** @return array{sql:string,params:array<string,mixed>} */ public function latestAudit(): array { foreach (array_reverse($this->executed) as $entry) if (strpos($entry['sql'], 'INSERT INTO MCP003') === 0) return $entry; throw new RuntimeException('MCP003 insert was not executed.'); }
}
final class QuoteItemAuditRateLimiter implements RateLimiterInterface
{
    public function consume(string $key, int $limit, int $windowSeconds): RateLimitDecision { return new RateLimitDecision(true, 0); }
}
final class QuoteItemAuditBootstrap extends Bootstrap
{
    private QuoteItemAuditPdo $pdo; private ExecutionContext $context; private QuoteItemGatewayFixture $gateway;
    public function __construct(QuoteItemAuditPdo $pdo, ExecutionContext $context, QuoteItemGatewayFixture $gateway) { parent::__construct(dirname(__DIR__, 2)); $this->pdo=$pdo; $this->context=$context; $this->gateway=$gateway; }
    protected function resolveTenant(array $config, string $host): array { return ['host'=>'mcp.test','conf_file'=>'/tmp/mcp-test.php','dbDatabase'=>'tenant','dbHost'=>'db','dbUser'=>'user','dbPass'=>'pass','urlRaiz'=>'https://mcp.test']; }
    protected function connectTenant(array $config, array $tenant): PDO { return $this->pdo; }
    protected function authenticate(array $config, PDO $pdo, Request $request): ?ExecutionContext { return $this->context; }
    protected function createRateLimitPolicy(array $config): RateLimitPolicy { return new RateLimitPolicy(new QuoteItemAuditRateLimiter(), $config); }
    protected function createJsonRpcServer(array $config, PDO $pdo): JsonRpcServer { return new JsonRpcServer($config, new ToolExecutor((new ToolCatalogFactory([new QuoteToolsModule(new QuoteItemRepositoryFixture(), null, null, $this->gateway)]))->createRegistry())); }
}

$gateway=new QuoteItemGatewayFixture();
$registry=(new ToolCatalogFactory([new QuoteToolsModule(new QuoteItemRepositoryFixture(),null,null,$gateway)]))->createRegistry();
$server=new JsonRpcServer([],new ToolExecutor($registry));
$apiUpdate=new ExecutionContext(1,2,3,9,['quote:item:update'],'api_key',null,null,null,'203.0.113.10',true);
$apiDelete=new ExecutionContext(1,2,3,9,['quote:item:delete'],'api_key',null,null,null,'203.0.113.10',true);
$oauthUpdate=new ExecutionContext(1,0,3,9,['quote:item:update'],'oauth',4,5,6,'203.0.113.10');
$oauthDelete=new ExecutionContext(1,0,3,9,['quote:item:delete'],'oauth',4,5,6,'203.0.113.10');
$oauthNoActor=new ExecutionContext(1,0,3,null,['quote:item:update','quote:item:delete'],'oauth',4,5,6,'203.0.113.10');
$noActor=new ExecutionContext(1,2,3,null,['quote:item:update'],'api_key',null,null,null,'203.0.113.10',false);
$noActorDelete=new ExecutionContext(1,2,3,null,['quote:item:delete'],'api_key',null,null,null,'203.0.113.10',false);

$oauthConfig=['oauth'=>['access_token_peppers'=>['p1'=>'oauth-test-pepper'],'access_token_pepper_current_version'=>'p1']];
$oauthCredential='abcdefghijklmnop.'.str_repeat('a',32);
$oauthActor=(new OAuthAccessTokenAuthenticator($oauthConfig))->authenticate(new QuoteItemOAuthPdo(true),$oauthCredential,'https://mcp.test/mcp','203.0.113.10');
qiAssert($oauthActor!==null && $oauthActor->getResponsibleUserId()===9 && $oauthActor->hasErpActor(),'OAuth with a valid ERP actor must produce an executable actor context');
$oauthWrongResource=(new OAuthAccessTokenAuthenticator($oauthConfig))->authenticate(new QuoteItemOAuthPdo(true),$oauthCredential,"https://mcp.test/mcp/c/AbCdEfGhIjKlMnOpQrStUvWx","203.0.113.10");
qiAssert($oauthWrongResource===null,"OAuth access token for connection A must be rejected at connection B resource.");
$oauthNoProfileActor=(new OAuthAccessTokenAuthenticator($oauthConfig))->authenticate(new QuoteItemOAuthPdo(false),$oauthCredential,'https://mcp.test/mcp','203.0.113.10');
qiAssert($oauthNoProfileActor!==null && $oauthNoProfileActor->getResponsibleUserId()===null && !$oauthNoProfileActor->hasErpActor(),'OAuth without an ERP profile must produce a non-executable actor context');
$oauthInactiveCompanyActor=(new OAuthAccessTokenAuthenticator($oauthConfig))->authenticate(new QuoteItemOAuthPdo(false),$oauthCredential,'https://mcp.test/mcp','203.0.113.10');
qiAssert($oauthInactiveCompanyActor!==null && !$oauthInactiveCompanyActor->hasErpActor(),'OAuth with an inactive ERP company must produce a non-executable actor context');
$eligibilitySource=(string)file_get_contents(dirname(__DIR__,2).'/src/Eligibility/McpConnectionEligibilityRepository.php');
qiAssert(strpos($eligibilitySource,"company.C004_Flag_Ativo='S'")!==false && strpos($eligibilitySource,'membership.C019_C021_Id>0')!==false,'OAuth eligibility must require an active company and ERP profile before legacy execution');
$updateCatalog=$server->handle('{"jsonrpc":"2.0","id":79,"method":"tools/list"}','2025-11-25',$apiUpdate)->getPayload();
$updateDefinition=null; foreach (($updateCatalog['result']['tools']??[]) as $definition) if (($definition['name']??null)==='hardness_quote_item_update') {$updateDefinition=$definition; break;}
qiAssert(is_array($updateDefinition),'quote item update must remain published in the MCP catalog');
$updateDescription=(string)($updateDefinition['description']??'');
qiAssert(strpos($updateDescription,'collective update within exactly one explicitly chosen quote')!==false && strpos($updateDescription,'one target quote, the field, the value or rule')!==false && strpos($updateDescription,'one unitary call per item, only for items of that same quote')!==false && strpos($updateDescription,'A collective update intent may cover at most one T003')!==false && strpos($updateDescription,'do not execute any update from that intent, do not process quotes sequentially')!==false && strpos($updateDescription,'require the user to choose one quote and then make a new request limited to that one quote')!==false && strpos($updateDescription,'Never choose quotes or items arbitrarily, invent a value, choose a field')!==false && strpos($updateDescription,'already authorizes the required unitary calls; do not require item-by-item confirmation')!==false,'published update Tool description must permit only one-T003 collective updates and prohibit carrying a multi-quote intent into sequential processing');
$updateSchema=$updateDefinition['inputSchema']??[];
qiAssert(($updateSchema['required']??null)===['t004_id','field'] && ($updateSchema['additionalProperties']??null)===false && in_array('location_code',$updateSchema['properties']['field']['enum']??[],true) && ($updateSchema['properties']['location_code']['maxLength']??null)===60,'update catalog schema must support the optional ERP Ambiente while remaining unitary');
$deleteCatalog=$server->handle('{"jsonrpc":"2.0","id":80,"method":"tools/list"}','2025-11-25',$apiDelete)->getPayload();
$deleteDefinition=null; foreach (($deleteCatalog['result']['tools']??[]) as $definition) if (($definition['name']??null)==='hardness_quote_item_delete') {$deleteDefinition=$definition; break;}
qiAssert(is_array($deleteDefinition),'quote item delete must remain published in the MCP catalog');
$deleteDescription=(string)($deleteDefinition['description']??'');
qiAssert(strpos($deleteDescription,'PROHIBITED: do not use this Tool to delete more than one item as part of the same user request or intent')!==false && strpos($deleteDescription,'do not call this Tool even once')!==false && strpos($deleteDescription,'Do not decompose, search for, identify, select, prepare, propose, clarify')!==false && strpos($deleteDescription,'A user confirmation never authorizes a collective deletion')!==false && strpos($deleteDescription,'operation is not permitted and require a new request for one specific item')!==false && strpos($deleteDescription,'execute exactly one delete, and end the destructive operation')!==false && strpos($deleteDescription,'never continue automatically from an earlier collective intent')!==false,'published delete Tool description must categorically prohibit every call or preparation for a collective request');
$deleteSchema=$deleteDefinition['inputSchema']??[];
qiAssert(($deleteSchema['properties']??null)===['t004_id'=>['type'=>'integer','minimum'=>1]] && ($deleteSchema['required']??null)===['t004_id'] && ($deleteSchema['additionalProperties']??null)===false,'delete catalog schema must continue to accept exclusively one t004_id');

foreach (['quantity'=>2,'discount_percent'=>5,'unit_price'=>12.5,'item_order'=>3] as $field=>$value) {
    $result=$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>1,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_update','arguments'=>['t004_id'=>81,'field'=>$field,'value'=>$value]]]),'2025-11-25',$apiUpdate)->getPayload();
    qiAssert(($result['result']['structuredContent']['field']??null)===$field && $gateway->updateArguments['t004_id']===81,"$field update must reach the fixed gateway");
}
$location=$server->handle('{"jsonrpc":"2.0","id":15,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"location_code","location_code":"Cozinha"}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($location['result']['structuredContent']['value']??null)==='COZINHA'&&($gateway->updateArguments['location_code']??null)==='Cozinha','location_code update must reach the fixed gateway and return the normalized ERP Ambiente');
$clearLocation=$server->handle('{"jsonrpc":"2.0","id":16,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"location_code","location_code":""}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($clearLocation['result']['structuredContent']['value']??null)==='','an explicit empty location_code must be accepted to remove the ERP Ambiente');
$missingLocation=$server->handle('{"jsonrpc":"2.0","id":17,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"location_code"}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($missingLocation['result']['structuredContent']['error']['code']??null)==='quote_item_invalid_value','location_code updates must require their string property');
$mixedLocation=$server->handle('{"jsonrpc":"2.0","id":18,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"location_code","location_code":"Sala","value":1}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($mixedLocation['result']['structuredContent']['error']['code']??null)==='quote_item_invalid_value','location_code updates must reject ambiguous numeric and text values');
$longLocation=$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>19,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_update','arguments'=>['t004_id'=>81,'field'=>'location_code','location_code'=>str_repeat('A',61)]]]),'2025-11-25',$apiUpdate)->getPayload();
qiAssert(($longLocation['error']['code']??null)===-32602,'location_code updates must enforce the ERP 60-character limit');
$discountZero=$server->handle('{"jsonrpc":"2.0","id":11,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"discount_percent","value":0}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($discountZero['result']['structuredContent']['value']??null)==='0','zero discount must reach the fixed gateway to remove a discount');
$discountNegative=$server->handle('{"jsonrpc":"2.0","id":12,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"discount_percent","value":-5}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($discountNegative['result']['structuredContent']['value']??null)==='-5','negative discount must reach the fixed gateway when the legacy allows an acréscimo');
$invalidOrder=$server->handle('{"jsonrpc":"2.0","id":13,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"item_order","value":0}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($invalidOrder['result']['structuredContent']['error']['code']??null)==='quote_item_invalid_value','zero item order must remain rejected by the MCP contract');
$negativeOrder=$server->handle('{"jsonrpc":"2.0","id":14,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"item_order","value":-1}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($negativeOrder['result']['structuredContent']['error']['code']??null)==='quote_item_invalid_value','negative item order must remain rejected by the MCP contract');
$bad=$server->handle('{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"T004_D009_Id","value":2}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($bad['error']['code']??null)===-32602,'physical or non-allow-listed fields must be schema-rejected');
$badValue=$server->handle('{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"quantity","value":0}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($badValue['result']['structuredContent']['error']['code']??null)==='quote_item_invalid_value','non-discount values must remain positive');
$gateway->updateFailure='quote_item_price_unavailable';
$priceUnavailable=$server->handle('{"jsonrpc":"2.0","id":31,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"unit_price","value":12.5}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($priceUnavailable['result']['structuredContent']['error']['code']??null)==='quote_item_price_unavailable','items without an effective legacy price must return a specific functional error');
$gateway->updateFailure=null;
$gateway->updateFailure='quote_item_retryable';
$retryable=$server->handle('{"jsonrpc":"2.0","id":32,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"quantity","value":2}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($retryable['result']['structuredContent']['error']['code']??null)==='quote_item_retryable','deadlock and lock-timeout failures must be retryable without exposing database details');
$gateway->updateFailure=null;
$denied=$server->handle('{"jsonrpc":"2.0","id":4,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$apiUpdate)->getPayload();
qiAssert(($denied['result']['structuredContent']['error']['code']??null)==='scope_denied','update scope must not grant delete');
$actor=$server->handle('{"jsonrpc":"2.0","id":5,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"quantity","value":2}}}','2025-11-25',$noActor)->getPayload();
qiAssert(($actor['result']['structuredContent']['error']['code']??null)==='erp_actor_required','API key updates require a valid ERP actor');
$deleteActor=$server->handle('{"jsonrpc":"2.0","id":51,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$noActorDelete)->getPayload();
qiAssert(($deleteActor['result']['structuredContent']['error']['code']??null)==='erp_actor_required','API key deletes require a valid ERP actor');
$oauth=$server->handle('{"jsonrpc":"2.0","id":6,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"quantity","value":2}}}','2025-11-25',$oauthUpdate)->getPayload();
qiAssert(($oauth['result']['structuredContent']['field']??null)==='quantity','OAuth actor and update scope must be accepted');
$delete=$server->handle('{"jsonrpc":"2.0","id":7,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$apiDelete)->getPayload();
qiAssert(($delete['result']['structuredContent']['deleted']??false)===true && $gateway->deleteId===81,'delete must reach the fixed gateway once');
$oauthDeleteResult=$server->handle('{"jsonrpc":"2.0","id":70,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$oauthDelete)->getPayload();
qiAssert(($oauthDeleteResult['result']['structuredContent']['deleted']??false)===true && $gateway->deleteId===81,'OAuth delete with a valid ERP actor must reach the fixed gateway');
$oauthNoProfile=$server->handle('{"jsonrpc":"2.0","id":701,"method":"tools/call","params":{"name":"hardness_quote_item_update","arguments":{"t004_id":81,"field":"quantity","value":2}}}','2025-11-25',$oauthNoActor)->getPayload();
qiAssert(($oauthNoProfile['result']['structuredContent']['error']['code']??null)==='erp_actor_required','OAuth without an ERP profile must be rejected before the gateway');
$oauthInactiveCompany=$server->handle('{"jsonrpc":"2.0","id":702,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$oauthNoActor)->getPayload();
qiAssert(($oauthInactiveCompany['result']['structuredContent']['error']['code']??null)==='erp_actor_required','OAuth without an active ERP company must be rejected before the gateway');
$gateway->deleteFailure='quote_not_pending';
$deleteStatus=$server->handle('{"jsonrpc":"2.0","id":71,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$apiDelete)->getPayload();
qiAssert(($deleteStatus['result']['structuredContent']['error']['code']??null)==='quote_not_pending','delete must preserve the pending-quote functional error');
$gateway->deleteFailure='quote_item_locked';
$deleteLocked=$server->handle('{"jsonrpc":"2.0","id":72,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$apiDelete)->getPayload();
qiAssert(($deleteLocked['result']['structuredContent']['error']['code']??null)==='quote_item_locked','delete must preserve the T006 functional error');
$gateway->deleteFailure='quote_item_retryable';
$deleteRetryable=$server->handle('{"jsonrpc":"2.0","id":73,"method":"tools/call","params":{"name":"hardness_quote_item_delete","arguments":{"t004_id":81}}}','2025-11-25',$apiDelete)->getPayload();
qiAssert(($deleteRetryable['result']['structuredContent']['error']['code']??null)==='quote_item_retryable','delete must preserve retryable locking errors');
$gateway->deleteFailure=null;
$read=$server->handle('{"jsonrpc":"2.0","id":8,"method":"tools/call","params":{"name":"hardness_quote_get_by_id","arguments":{"id":7}}}','2025-11-25',new ExecutionContext(1,2,3,9,['quote:read'],'api_key',null,null,null,'203.0.113.10',true))->getPayload();
qiAssert(($read['result']['structuredContent']['items'][0]['id']??null)===81,'quote get by id must expose the additive T004 id');
qiAssert(($read['result']['structuredContent']['items'][0]['location_code']??null)==='COZINHA','quote get by id must expose the ERP Ambiente stored on the quote item');

// Integration coverage: Bootstrap -> McpAuditService -> actual MCP003 INSERT.
$auditConfig=require dirname(__DIR__,2).'/config/app.php';
$auditConfig['rate_limit']['key_pepper']='quote-item-audit-test-pepper';
$auditConfig['rate_limit']['driver']='apcu';
$auditConfig['oauth']['http_diagnostics_enabled']=false;
$auditHeaders=['Accept'=>'application/json, text/event-stream','Content-Type'=>'application/json','MCP-Protocol-Version'=>'2025-11-25','X-Client-IP'=>'203.0.113.10'];
$auditGateway=new QuoteItemGatewayFixture();
$auditCall=static function (ExecutionContext $context, string $tool, array $arguments) use ($auditConfig,$auditHeaders,$auditGateway): array {
    $pdo=new QuoteItemAuditPdo(); $body=json_encode(['jsonrpc'=>'2.0','id'=>91,'method'=>'tools/call','params'=>['name'=>$tool,'arguments'=>$arguments]]);
    $response=(new QuoteItemAuditBootstrap($pdo,$context,$auditGateway))->handle(new Request('POST','/mcp','mcp.test',(string)$body,$auditHeaders),$auditConfig);
    return ['response'=>$response,'audit'=>$pdo->latestAudit()];
};
$assertAudit=static function (array $result, string $event, string $safeCode, string $authMethod, ?int $actorId): void {
    $row=$result['audit']; $params=$row['params'];
    qiAssert($result['response']->getStatus()===200 && ($params['status']??null)===200,'functional Tool outcomes must be persisted with HTTP 200');
    qiAssert(($params['connection_id']??null)===1 && ($params['company_id']??null)===3 && ($params['user_id']??null)===$actorId,'MCP003 must persist connection, company and eligible ERP actor');
    qiAssert(($params['event']??null)===$event && ($params['code']??null)===$safeCode,'MCP003 must persist the Tool event and safe code');
    qiAssert(($params['duration']??-1)>=0 && preg_match('/^[a-f0-9]{32}$/',(string)($params['request_id']??''))===1,'MCP003 must persist duration and an opaque request ID');
    qiAssert(strpos($row['sql'],'MCP003_Request_Id')!==false && !array_key_exists('arguments',$params) && !array_key_exists('payload',$params) && !array_key_exists('token',$params),'MCP003 must not persist Tool arguments, payloads or credentials');
    if ($authMethod==='api_key') qiAssert(strpos($row['sql'],'MCP003_MCP002_Id')!==false && ($params['credential_id']??null)===2,'API Key audit must persist the credential context');
    else qiAssert(strpos($row['sql'],'MCP003_MCP004_Id')!==false && ($params['client_id']??null)===4 && ($params['grant_id']??null)===5 && ($params['token_id']??null)===6,'OAuth audit must persist client, grant and access-token context');
};
$auditApiUpdate=new ExecutionContext(1,2,3,9,['quote:item:update'],'api_key',null,null,null,'203.0.113.10',true);
$auditOAuthDelete=new ExecutionContext(1,0,3,9,['quote:item:delete'],'oauth',4,5,6,'203.0.113.10');
$auditOAuthNoActor=new ExecutionContext(1,0,3,null,['quote:item:update'],'oauth',4,5,6,'203.0.113.10');
$auditGateway->updateFailure=null;
$assertAudit($auditCall($auditApiUpdate,'hardness_quote_item_update',['t004_id'=>81,'field'=>'quantity','value'=>2]),'tools_call_quote_item_update','hardness_quote_item_update','api_key',9);
$assertAudit($auditCall($auditApiUpdate,'hardness_quote_item_update',['t004_id'=>81,'field'=>'location_code','location_code'=>'COZINHA']),'tools_call_quote_item_update','hardness_quote_item_update','api_key',9);
$auditGateway->deleteFailure=null;
$assertAudit($auditCall($auditOAuthDelete,'hardness_quote_item_delete',['t004_id'=>81]),'tools_call_quote_item_delete','hardness_quote_item_delete','oauth',9);
foreach ([
    ['updateFailure'=>'quote_not_pending','tool'=>'hardness_quote_item_update','context'=>$auditApiUpdate,'arguments'=>['t004_id'=>81,'field'=>'quantity','value'=>2]],
    ['deleteFailure'=>'quote_item_unavailable','tool'=>'hardness_quote_item_delete','context'=>$auditOAuthDelete,'arguments'=>['t004_id'=>81]],
    ['updateFailure'=>'quote_item_locked','tool'=>'hardness_quote_item_update','context'=>$auditApiUpdate,'arguments'=>['t004_id'=>81,'field'=>'quantity','value'=>2]],
    ['deleteFailure'=>'quote_item_retryable','tool'=>'hardness_quote_item_delete','context'=>$auditOAuthDelete,'arguments'=>['t004_id'=>81]],
] as $case) {
    $auditGateway->updateFailure=$case['updateFailure']??null; $auditGateway->deleteFailure=$case['deleteFailure']??null;
    $code=$case['updateFailure']??$case['deleteFailure'];
    $event=strpos($case['tool'],'update')!==false?'tools_call_quote_item_update_failed':'tools_call_quote_item_delete_failed';
    $assertAudit($auditCall($case['context'],$case['tool'],$case['arguments']),$event,$code,$case['context']->getAuthMethod(),9);
}
$auditGateway->updateFailure=null; $auditGateway->deleteFailure=null;
$assertAudit($auditCall($auditOAuthNoActor,'hardness_quote_item_update',['t004_id'=>81,'field'=>'quantity','value'=>2]),'tools_call_erp_actor_required','hardness_quote_item_update','oauth',null);
echo "Quote item isolated tests passed\n";
