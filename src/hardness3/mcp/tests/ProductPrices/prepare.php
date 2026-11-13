<?php
/** Run in hardness-php7 with PRICE_TEST_DSN=mysql:host=127.0.0.1;dbname=e998.
 * Only temporary tables receive test data. MCP020/021 must already be migrated.
 */
declare(strict_types=1);
require dirname(__DIR__, 2).'/autoload.php';

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\Tools\{ToolExecutor, ToolRegistry};
use HardnessMcp\Tools\Product\Prices\{PreparePricesTool, PreparePricesRepository};

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
$input=requestData('one','00123',['price_1'=>'100.1234567890','price_5'=>'55']);
$r=callPreview($server,$actor,$input); $preview=content($r);
verify(($preview['summary']['ready']??0)===1 && $preview['prices_updated']===false,'valid preview is ready without changing prices');
verify($preview['items'][0]['changes'][0]['proposed']==='100.1234567890' && $preview['items'][0]['changes'][1]['current']==='50.0000000000','exact decimal and price 5 retained');
verify(strpos($r['result']['content'][0]['text'],'Preço 5')!==false,'human readable preview includes selected prices');
verify(content(callPreview($server,$actor,$input))===$preview,'identical retry returns original preview');
$input['items'][0]['prices']['price_1']='101';
verify(content(callPreview($server,$actor,$input))['error']['code']==='idempotency_conflict','same key with changed data rejected');
$changedActor = new ExecutionContext(1,1,1,2,['product:prices:prepare'],'api_key',null,null,null,null,true);
verify(content(callPreview($server,$changedActor,requestData('one')))['error']['code']==='idempotency_conflict','another actor cannot reuse preview');
foreach (['123'=>'product_not_found','0012%'=>'product_not_found','AMB'=>'ambiguous_product','INACTIVE'=>'product_not_found','OTHERONLY'=>'product_not_found','AUTO'=>'automatic_price'] as $code=>$error) {
    $x=content(callPreview($server,$actor,requestData('case-'.bin2hex(random_bytes(3)),(string)$code)));
    verify(($x['items'][0]['error']??null)===$error,'resolution '.$code.' -> '.$error);
}
$x=content(callPreview($server,$actor,requestData('missing5','MISSING5',['price_5'=>'80'])));
verify($x['items'][0]['error']==='price_5_record_missing','missing D009A blocks fifth price');
$x=content(callPreview($server,$actor,requestData('missing5-ok','MISSING5')));
verify($x['summary']['ready']===1,'missing D009A does not block other prices');
$x=content(callPreview($server,$actor,requestData('auto5','AUTO',['price_5'=>'80'])));
verify($x['items'][0]['error']==='automatic_price','automatic fifth price blocked');
foreach (['-1','1.250,50','1e3','10000000000','0.00000000001'] as $value) {
    $x=content(callPreview($server,$actor,requestData('bad-'.bin2hex(random_bytes(3)),'00123',['price_5'=>$value])));
    verify($x['items'][0]['error']==='invalid_price','invalid amount rejected: '.$value);
}
$x=content(callPreview($server,$actor,requestData('zero','00123',['price_2'=>'0'])));
verify($x['items'][0]['changes'][0]['current']===null && count($x['items'][0]['warnings'])===1,'NULL preserved and zero requires explicit later approval');
$x=content(callPreview($server,$actor,requestData('unchanged','00123',['price_1'=>'90','price_5'=>'50'])));
verify($x['summary']['unchanged']===1,'unchanged prices recognized');
$x=content(callPreview($server,$actor,requestData('empty','00123',[])));
verify($x['items'][0]['error']==='price_selection_required','price selection mandatory');
$dup=requestData('duplicate'); $dup['items'][]=['line'=>2,'code'=>'00123','prices'=>['price_1'=>'200']];
$x=content(callPreview($server,$actor,$dup));
verify($x['summary']['blocked']===2,'both duplicate codes blocked, no last-row wins');
$dup['request_key']='same-line'; $dup['items'][1]['line']=1;
verify(content(callPreview($server,$actor,$dup))['error']['code']==='duplicate_line','duplicate line identifiers rejected');
$pdo->exec("UPDATE MCP020 SET MCP020_Data_Expiracao='2000-01-01' WHERE MCP020_Chave_Idempotencia='one'");
verify(content(callPreview($server,$actor,requestData('one','00123',['price_1'=>'100.1234567890','price_5'=>'55'])))['error']['code']==='preview_expired','expired preview cannot be reused');
$withoutScope=new ExecutionContext(1,1,1,1,['product:read'],'api_key',null,null,null,null,true);
verify((content(callPreview($server,$withoutScope,requestData('denied')))['error']['code']??null)==='scope_denied','product read alone cannot disclose prices');
$withoutActor=new ExecutionContext(1,1,1,null,['product:prices:prepare']);
verify((content(callPreview($server,$withoutActor,requestData('no-actor')))['error']['code']??null)==='erp_actor_required','actor required');
$bad=requestData('company'); $bad['company_id']=2;
verify(isset(callPreview($server,$actor,$bad)['error']),'company argument rejected');
$bad=requestData('unknown-price','00123',['price_6'=>'1']);
verify(isset(callPreview($server,$actor,$bad)['error']),'price outside allowlist rejected');
$bad=requestData('over-limit'); $bad['items']=array_fill(0,1001,$bad['items'][0]);
verify(isset(callPreview($server,$actor,$bad)['error']),'1001 rows rejected');
// Exercise valid five-price rows, not just missing product lookups.
for ($n=100;$n<1100;$n++) {
    $pdo->exec("INSERT INTO D001 VALUES ($n,'BULK-$n','S')");
    $pdo->exec("INSERT INTO D049 VALUES ($n,$n)");
    $pdo->exec("INSERT INTO D009 (D009_Id,D009_D049_Id,D009_C004_Id,D009_Preco_1) VALUES ($n,$n,1,10)");
    $pdo->exec("INSERT INTO D009A VALUES ($n,$n,50,0)");
}
$sourceBefore = [$pdo->query('SELECT * FROM D009 ORDER BY D009_Id')->fetchAll(PDO::FETCH_ASSOC), $pdo->query('SELECT * FROM D009A ORDER BY D009A_Id')->fetchAll(PDO::FETCH_ASSOC)];
foreach ([500,1000] as $size) {
    $bulk=requestData('bulk-'.$size); $bulk['items']=[];
    for($n=100;$n<100+$size;$n++)$bulk['items'][]=['line'=>$n,'code'=>'BULK-'.$n,'prices'=>['price_1'=>'11','price_2'=>'22','price_3'=>'33','price_4'=>'44','price_5'=>'55']];
    $start=microtime(true);
    $x=content(callPreview($server,$actor,$bulk));
    verify(count($x['items']??[])===$size && $x['summary']['ready']===$size,"$size valid five-price rows supported in one batch");
    echo 'Elapsed '.$size.': '.round(microtime(true)-$start,3)."s\n";
}
$sourceAfter = [$pdo->query('SELECT * FROM D009 ORDER BY D009_Id')->fetchAll(PDO::FETCH_ASSOC), $pdo->query('SELECT * FROM D009A ORDER BY D009A_Id')->fetchAll(PDO::FETCH_ASSOC)];
verify($sourceBefore===$sourceAfter,'all preparations leave D009 and D009A untouched');
$count=$pdo->query('SELECT COUNT(*) FROM MCP020')->fetchColumn();
$pdo->exec('ALTER TABLE MCP021 DROP COLUMN MCP021_Preco_5_Depois');
$x=content(callPreview($server,$actor,requestData('storage-fails')));
verify($x['error']['code']==='preview_unavailable' && $count===$pdo->query('SELECT COUNT(*) FROM MCP020')->fetchColumn(),'item storage failure rolls back entire batch without leaking SQL');
echo "Product price preparation integration passed (temporary data only).\n";
