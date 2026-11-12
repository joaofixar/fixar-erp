<?php
/** Run in hardness-php7 with PRICE_TEST_DSN=mysql:host=127.0.0.1;dbname=e998.
 * Only temporary tables receive test data. MCP020/021 must already be migrated.
 */
declare(strict_types=1);
require dirname(__DIR__, 2).'/autoload.php';

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\Tools\{ToolExecutor, ToolRegistry};
use HardnessMcp\Tools\Product\Prices\{ApplyReversalTool, PrepareReversalTool, PriceHistoryRepository, PreparePricesTool, PreparePricesRepository};

function verify($condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
    echo 'PASS '.$message."\n";
}
$dsn = getenv('PRICE_TEST_DSN');
if (!$dsn) throw new RuntimeException('Explicit PRICE_TEST_DSN required.');
$pdo = new PDO($dsn, getenv('PRICE_TEST_USER'), getenv('PRICE_TEST_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES=>false]);
$pdo->exec("SET time_zone='+00:00'");
$migration = file_get_contents(dirname(__DIR__,3).'/scripts/atualizacaoBase.php');
foreach (['MCP020','MCP021'] as $table) {
    preg_match('/CREATE TABLE IF NOT EXISTS '.$table.' \(.*?ENGINE=InnoDB;/s', $migration, $match);
    $pdo->exec(str_replace('CREATE TABLE IF NOT EXISTS','CREATE TEMPORARY TABLE', $match[0]));
}
$pdo->exec("CREATE TEMPORARY TABLE D001 (D001_Id BIGINT PRIMARY KEY,D001_Codigo_Produto VARCHAR(255),D001_Flag_Ativo CHAR(1)) ENGINE=InnoDB");
$pdo->exec("CREATE TEMPORARY TABLE D049 (D049_Id BIGINT PRIMARY KEY,D049_D001_Id BIGINT) ENGINE=InnoDB");
$columns=[];
for ($n=1;$n<=4;$n++) { $columns[]='D009_Preco_'.$n.' DECIMAL(20,10)'; $columns[]='D009_IPT_'.$n.' DECIMAL(20,10) DEFAULT 0'; }
$pdo->exec('CREATE TEMPORARY TABLE D009 (D009_Id BIGINT PRIMARY KEY,D009_D049_Id BIGINT,D009_C004_Id BIGINT,'.implode(',',$columns).') ENGINE=InnoDB');
$pdo->exec('CREATE TEMPORARY TABLE D009A (D009A_Id BIGINT PRIMARY KEY,D009A_D009_Id BIGINT,D009A_Preco_5 DECIMAL(20,10),D009A_IPT_5 DECIMAL(20,10) DEFAULT 0) ENGINE=InnoDB');
foreach ([1=>'00123',2=>'AUTO',3=>'MISSING5',4=>'OTHER',5=>'AMB',6=>'AMB',7=>'INACTIVE',8=>'OTHERONLY'] as $id=>$code) {
    $pdo->exec("INSERT INTO D001 VALUES ($id,'$code','".($id===7?'N':'S')."')");
    $pdo->exec("INSERT INTO D049 VALUES ($id,$id)");
    $company=in_array($id,[4,8],true)?2:1;
    $pdo->exec("INSERT INTO D009 (D009_Id,D009_D049_Id,D009_C004_Id,D009_Preco_1,D009_Preco_2,D009_Preco_3,D009_Preco_4) VALUES ($id,$id,$company,90,NULL,30,40)");
    if ($id!==3) $pdo->exec("INSERT INTO D009A VALUES ($id,$id,50,0)");
}
$pdo->exec("UPDATE D009 SET D009_IPT_1=2 WHERE D009_Id=2");
$pdo->exec("UPDATE D009A SET D009A_IPT_5=2 WHERE D009A_D009_Id=2");
$sourceBefore = [$pdo->query('SELECT * FROM D009 ORDER BY D009_Id')->fetchAll(PDO::FETCH_ASSOC), $pdo->query('SELECT * FROM D009A ORDER BY D009A_Id')->fetchAll(PDO::FETCH_ASSOC)];
$pdo->exec('ALTER TABLE D009 ADD D009_Data_Alteracao_Preco DATETIME NULL');
$tool = new PreparePricesTool(new PreparePricesRepository($pdo));
$server = new JsonRpcServer([], new ToolExecutor(new ToolRegistry([$tool])));
$actor = new ExecutionContext(1,1,1,1,['product:prices:prepare'],'api_key',null,null,null,null,true);
function callPreview(JsonRpcServer $server, ExecutionContext $actor, array $input): array {
    return $server->handle(json_encode(['jsonrpc'=>'2.0','id'=>1,'method'=>'tools/call','params'=>['name'=>'hardness_product_prices_prepare','arguments'=>$input]]), '2025-11-25', $actor)->getPayload();
}
function requestData(string $key, string $code='00123', array $prices=['price_1'=>'100']): array {
    return ['request_key'=>$key,'origin'=>'spreadsheet','items'=>[['line'=>1,'code'=>$code,'prices'=>$prices ?: (object)[]]]];
}
function content(array $response): array { return $response['result']['structuredContent'] ?? []; }

use HardnessMcp\Tools\Product\Prices\{ApplyPricesRepository, ApplyPricesTool};
$applyTool=new ApplyPricesTool(new ApplyPricesRepository($pdo));
$applyServer=new JsonRpcServer([],new ToolExecutor(new ToolRegistry([$applyTool])));
$applyActor=new ExecutionContext(1,1,1,1,['product:prices:apply'],'api_key',null,null,null,null,true);
function approve(array $preview,bool $partial=false,bool $zero=false): array {
    return ['batch_id'=>$preview['batch_id'],'preview_hash'=>$preview['preview_hash'],'confirmed'=>true,'allow_partial'=>$partial,'allow_zero'=>$zero];
}
function applyCall(JsonRpcServer $s,ExecutionContext $c,array $a): array {
    return $s->handle(json_encode(['jsonrpc'=>'2.0','id'=>2,'method'=>'tools/call','params'=>['name'=>'hardness_product_prices_apply','arguments'=>$a]]),'2025-11-25',$c)->getPayload();
}
function preview(JsonRpcServer $s,ExecutionContext $c,string $key,array $prices=['price_1'=>'100','price_5'=>'60']): array {
    return content(callPreview($s,$c,requestData($key,'00123',$prices)));
}
function scalar(PDO $pdo,string $sql) { return $pdo->query($sql)->fetchColumn(); }
$p=preview($server,$actor,'apply-one'); $a=approve($p);
$wrong=$a;$wrong['confirmed']=false;
verify(isset(applyCall($applyServer,$applyActor,$wrong)['error']),'unconfirmed request rejected by schema');
verify(content(applyCall($applyServer,$actor,$a))['error']['code']==='scope_denied','prepare permission cannot apply');
$other=new ExecutionContext(1,1,2,1,['product:prices:apply'],'api_key',null,null,null,null,true);
verify(content(applyCall($applyServer,$other,$a))['error']['code']==='batch_unavailable','another company cannot apply batch');
$wrong=$a;$wrong['preview_hash']=str_repeat('0',64);
verify(content(applyCall($applyServer,$applyActor,$wrong))['error']['code']==='preview_changed','wrong preview hash rejected');
$r=content(applyCall($applyServer,$applyActor,$a));
verify(($r['summary']['applied']??0)===1 && $r['status']==='completed','selected prices applied');
verify(scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1')==='100.0000000000' && scalar($pdo,'SELECT D009A_Preco_5 FROM D009A WHERE D009A_Id=1')==='60.0000000000','both price tables updated');
verify(scalar($pdo,'SELECT D009_Preco_3 FROM D009 WHERE D009_Id=1')==='30.0000000000','unselected price untouched');
verify(scalar($pdo,"SELECT MCP021_Preco_1_Antes FROM MCP021 WHERE MCP021_MCP020_Id=1")==='90.0000000000','original value retained in applied history');
$attempts=scalar($pdo,'SELECT SUM(MCP021_Tentativas) FROM MCP021');
$pdo->exec('UPDATE D009 SET D009_Preco_1=110 WHERE D009_Id=1');
$r=content(applyCall($applyServer,$applyActor,$a));
verify($r['applied_this_call']===0 && scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1')==='110.0000000000' && scalar($pdo,'SELECT SUM(MCP021_Tentativas) FROM MCP021')===$attempts,'retry never overwrites a later edit or increments completed attempts');
$p=preview($server,$actor,'conflict');$a=approve($p);
$pdo->exec('UPDATE D009 SET D009_Preco_1=120 WHERE D009_Id=1');
$r=content(applyCall($applyServer,$applyActor,$a));
verify($r['summary']['conflict']===1 && scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1')==='120.0000000000','price changed after preview becomes conflict');
$p=preview($server,$actor,'auto-changed');$a=approve($p);
$pdo->exec('UPDATE D009A SET D009A_IPT_5=2 WHERE D009A_Id=1');
$r=content(applyCall($applyServer,$applyActor,$a));
verify($r['items'][0]['error']==='automatic_price','automatic pricing rechecked before any write');
$pdo->exec('UPDATE D009A SET D009A_IPT_5=0 WHERE D009A_Id=1');
$p=preview($server,$actor,'zero',['price_1'=>'0']);
verify(content(applyCall($applyServer,$applyActor,approve($p)))['error']['code']==='zero_approval_required','zero needs explicit approval');
verify(content(applyCall($applyServer,$applyActor,approve($p,false,true)))['summary']['applied']===1,'explicit zero approval applies');
$p=preview($server,$actor,'expires');
$pdo->exec("UPDATE MCP020 SET MCP020_Data_Expiracao='2000-01-01' WHERE MCP020_Chave_Idempotencia='expires'");
verify(content(applyCall($applyServer,$applyActor,approve($p)))['error']['code']==='preview_expired','expired preview cannot start');
$input=requestData('partial');$input['items'][]=['line'=>2,'code'=>'MISSING','prices'=>['price_1'=>'80']];
$p=content(callPreview($server,$actor,$input));
verify(content(applyCall($applyServer,$applyActor,approve($p)))['error']['code']==='partial_approval_required','blocked rows need explicit partial approval');
$r=content(applyCall($applyServer,$applyActor,approve($p,true)));
verify($r['summary']['applied']===1 && $r['summary']['blocked']===1 && $r['status']==='partial','partial approval applies valid rows and preserves blocked rows');
verify(content(applyCall($applyServer,$applyActor,approve($p,false)))['error']['code']==='approval_mismatch','approval choices cannot change on retry');
$p=preview($server,$actor,'tampered');
$pdo->exec("UPDATE MCP021 i JOIN MCP020 b ON b.MCP020_Id=i.MCP021_MCP020_Id SET i.MCP021_Preco_1_Depois=888 WHERE b.MCP020_Chave_Idempotencia='tampered'");
verify(content(applyCall($applyServer,$applyActor,approve($p)))['error']['code']==='preview_changed','stored snapshot tampering rejected');
// A history write error after both price updates must undo both prices.
$p=preview($server,$actor,'rollback',['price_1'=>'200','price_5'=>'300']);$a=approve($p);
$before=[scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1'),scalar($pdo,'SELECT D009A_Preco_5 FROM D009A WHERE D009A_Id=1')];
$pdo->exec('ALTER TABLE MCP021 DROP COLUMN MCP021_Data_Aplicacao');
$r=content(applyCall($applyServer,$applyActor,$a));
verify($r['summary']['failed']===1 && $r['can_resume']===true,'failed item recorded as resumable');
verify($before===[scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1'),scalar($pdo,'SELECT D009A_Preco_5 FROM D009A WHERE D009A_Id=1')],'history failure rolls back both price tables');
$pdo->exec('ALTER TABLE MCP021 ADD MCP021_Data_Aplicacao DATETIME NULL');
$r=content(applyCall($applyServer,$applyActor,$a));
verify($r['summary']['applied']===1 && $r['summary']['failed']===0,'retry resumes failed item after storage recovery');
verify(scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1')==='200.0000000000' && scalar($pdo,'SELECT D009A_Preco_5 FROM D009A WHERE D009A_Id=1')==='300.0000000000','resumed update persisted correctly');

// A reversal creates a separate auditable batch and restores only the source fields.
$source=preview($server,$actor,'reversal-source',['price_1'=>'350','price_5'=>'450']);
verify(content(applyCall($applyServer,$applyActor,approve($source)))['summary']['applied']===1,'source batch for reversal applied');
$history=new PriceHistoryRepository($pdo);
$reversePreview=$history->prepareReversal(['source_batch_id'=>$source['batch_id'],'request_key'=>'reversal-one'],$applyActor);
verify($reversePreview['summary']['ready']===1 && $reversePreview['source_batch_id']===$source['batch_id'],'reversal preview links to its source batch');
$reverseTool=new ApplyReversalTool(new ApplyPricesRepository($pdo));
$reverseServer=new JsonRpcServer([],new ToolExecutor(new ToolRegistry([$reverseTool])));
$reverseActor=new ExecutionContext(1,1,1,1,['product:prices:reverse'],'api_key',null,null,null,null,true);
$reverseArguments=['batch_id'=>$reversePreview['batch_id'],'preview_hash'=>$reversePreview['preview_hash'],'confirmed'=>true,'allow_partial'=>false];
$reverseResponse=$reverseServer->handle(json_encode(['jsonrpc'=>'2.0','id'=>3,'method'=>'tools/call','params'=>['name'=>'hardness_product_prices_reversal_apply','arguments'=>$reverseArguments]]),'2025-11-25',$reverseActor)->getPayload();
verify(content($reverseResponse)['summary']['applied']===1,'approved reversal applied');
verify(scalar($pdo,'SELECT D009_Preco_1 FROM D009 WHERE D009_Id=1')==='200.0000000000' && scalar($pdo,'SELECT D009A_Preco_5 FROM D009A WHERE D009A_Id=1')==='300.0000000000','reversal restored the original selected prices');
verify(scalar($pdo,"SELECT MCP020_Tipo FROM MCP020 WHERE MCP020_Public_Id='".$reversePreview['batch_id']."'")==='reversal','reversal history is stored as a separate batch');

$source=preview($server,$actor,'reversal-conflict',['price_1'=>'500']);
verify(content(applyCall($applyServer,$applyActor,approve($source)))['summary']['applied']===1,'second source for reversal conflict applied');
$pdo->exec('UPDATE D009 SET D009_Preco_1=501 WHERE D009_Id=1');
$reversePreview=$history->prepareReversal(['source_batch_id'=>$source['batch_id'],'request_key'=>'reversal-conflict-undo'],$applyActor);
verify($reversePreview['summary']['blocked']===1 && $reversePreview['items'][0]['error']==='price_changed_since_original','reversal blocks a price changed after the original batch');
$historyResult=$history->history(['limit'=>100],$applyActor);
verify(count($historyResult['batches'])>=3 && $historyResult['batches'][0]['type']==='reversal','history lists updates and reversals');
$historyDetail=$history->history(['batch_id'=>$source['batch_id'],'limit'=>1],$applyActor);
verify(count($historyDetail['items'])===1 && $historyDetail['items'][0]['changes'][0]['before']==='200.0000000000','history detail returns selected before and after prices');
echo "Price application integration passed (temporary tables only).\n";
