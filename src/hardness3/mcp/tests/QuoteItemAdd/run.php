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

function qia(bool $condition,string $message):void { if(!$condition) throw new RuntimeException($message); }
final class QiaRepository implements QuoteRepositoryInterface { public function search(int $companyId,string $from,string $to,?int $customerId,?string $origin,int $limit):array{return [];} public function findById(int $companyId,int $quoteId):?array{return null;} }
final class QiaGateway implements QuoteItemGatewayInterface {
 public ?array $received=null; public ?array $result=null; public ?string $failure=null;
 public function add(array $arguments,ExecutionContext $context):array {if($this->failure!==null)throw new RuntimeException($this->failure);$this->received=$arguments;return $this->result??['quote_id'=>(int)$arguments['quote_id'],'status'=>'completed','items'=>[['index'=>1,'product_id'=>(int)$arguments['items'][0]['product_id'],'requested_quantity'=>$arguments['items'][0]['quantity'],'status'=>'included','t004_id'=>91,'item_order'=>3]]];}
 public function update(array $arguments,ExecutionContext $context):array{return [];} public function delete(int $id,ExecutionContext $context):array{return [];}
}
$config=require dirname(__DIR__,2).'/config/app.php'; $gateway=new QiaGateway();
$server=new JsonRpcServer($config,new ToolExecutor((new ToolCatalogFactory([new QuoteToolsModule(new QiaRepository(),null,null,$gateway)]))->createRegistry()));
$context=new ExecutionContext(1,2,3,9,['quote:item:add'],'api_key',null,null,null,'203.0.113.10',true);
$list=$server->handle('{"jsonrpc":"2.0","id":1,"method":"tools/list"}','2025-11-25',$context)->getPayload();$definition=null;foreach(($list['result']['tools']??[]) as $tool)if(($tool['name']??'')==='hardness_quote_item_add')$definition=$tool;
qia(is_array($definition),'add must be listed only with its dedicated scope');qia(($definition['inputSchema']['properties']['items']['maxItems']??null)===100,'add must retain the approved 100 item limit');qia(strpos((string)$definition['description'],'Never choose a quote')!==false&&strpos((string)$definition['description'],'multiple quotes')!==false,'description must forbid autonomous or multi-quote selection');
qia(strpos((string)$definition['description'],'never ask for it merely because it was omitted')!==false&&!in_array('location_code',$definition['inputSchema']['properties']['items']['items']['required']??[],true),'add must publish ERP Ambiente as optional and must prohibit asking for it merely because it is absent');
$body='{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"hardness_quote_item_add","arguments":{"quote_id":129,"items":[{"product_id":354,"quantity":2},{"product_id":500,"quantity":5}]}}}';$payload=$server->handle($body,'2025-11-25',$context)->getPayload();
qia($gateway->received===['quote_id'=>129,'items'=>[['product_id'=>354,'quantity'=>2],['product_id'=>500,'quantity'=>5]]],'add must pass only the approved contract to its fixed gateway');qia(($payload['result']['structuredContent']['status']??null)==='completed','completed add must retain its complete structured result');
$withLocation='{"jsonrpc":"2.0","id":21,"method":"tools/call","params":{"name":"hardness_quote_item_add","arguments":{"quote_id":129,"items":[{"product_id":354,"quantity":2,"location_code":"Sala"}]}}}';$server->handle($withLocation,'2025-11-25',$context);qia(($gateway->received['items'][0]['location_code']??null)==='Sala','add must accept an explicitly supplied optional item location');
$twenty=['quote_id'=>129,'items'=>array_fill(0,20,['product_id'=>354,'quantity'=>1])];$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>20,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_add','arguments'=>$twenty]]),'2025-11-25',$context);qia(count($gateway->received['items']??[])===20,'the approved contract must support 20 items in one quote');
$hundred=['quote_id'=>129,'items'=>array_fill(0,100,['product_id'=>354,'quantity'=>1])];$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>100,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_add','arguments'=>$hundred]]),'2025-11-25',$context);qia(count($gateway->received['items']??[])===100,'the approved contract must support 100 items in one quote');
$gateway->result=['quote_id'=>129,'status'=>'interrupted','items'=>[['index'=>1,'product_id'=>354,'requested_quantity'=>2,'status'=>'included','t004_id'=>91,'item_order'=>3],['index'=>2,'product_id'=>500,'requested_quantity'=>5,'status'=>'indeterminate','t004_id'=>92,'item_order'=>4,'error_code'=>'requires_quote_review'],['index'=>3,'product_id'=>600,'requested_quantity'=>1,'status'=>'not_attempted']],'error_code'=>'requires_quote_review'];$partial=$server->handle($body,'2025-11-25',$context)->getPayload();qia(($partial['result']['structuredContent']['items'][1]['status']??null)==='indeterminate'&&($partial['result']['structuredContent']['items'][2]['status']??null)==='not_attempted','partial add must expose all item outcomes without pretending success');
$denied=$server->handle($body,'2025-11-25',new ExecutionContext(1,2,3,9,['quote:write'],'api_key',null,null,null,'203.0.113.10',true))->getPayload();qia(($denied['result']['structuredContent']['error']['code']??null)==='scope_denied','quote write must not grant add');
$noActor=$server->handle($body,'2025-11-25',new ExecutionContext(1,2,3,null,['quote:item:add'],'api_key',null,null,null,'203.0.113.10',false))->getPayload();qia(($noActor['result']['structuredContent']['error']['code']??null)==='erp_actor_required','add requires an ERP actor');
$invalid=$server->handle('{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"hardness_quote_item_add","arguments":{"quote_id":129,"items":[{"product_id":354,"quantity":2,"unit_price":1}]}}}','2025-11-25',$context)->getPayload();qia(($invalid['error']['code']??null)===-32602,'add must reject manual commercial fields');
$longLocation=['quote_id'=>129,'items'=>[['product_id'=>354,'quantity'=>2,'location_code'=>str_repeat('A',61)]]];$invalidLocation=$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>31,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_add','arguments'=>$longLocation]]),'2025-11-25',$context)->getPayload();qia(($invalidLocation['error']['code']??null)===-32602,'add must reject an item location longer than the ERP 60-character column');
$tooMany=['quote_id'=>129,'items'=>array_fill(0,101,['product_id'=>354,'quantity'=>1])];$over=$server->handle(json_encode(['jsonrpc'=>'2.0','id'=>4,'method'=>'tools/call','params'=>['name'=>'hardness_quote_item_add','arguments'=>$tooMany]]),'2025-11-25',$context)->getPayload();qia(($over['error']['code']??null)===-32602,'add must reject more than 100 items');
echo "Quote item add tests passed\n";
