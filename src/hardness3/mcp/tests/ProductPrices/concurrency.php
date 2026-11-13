<?php
/** Creates and drops a uniquely named, isolated test schema on the configured local server. */
declare(strict_types=1);
require dirname(__DIR__,2).'/autoload.php';
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Product\Prices\{ApplyPricesRepository,PreparePricesInput,PreparePricesRepository};
function connection(string $dsn): PDO { return new PDO($dsn,getenv('PRICE_TEST_USER'),getenv('PRICE_TEST_PASSWORD'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]); }
function actor(): ExecutionContext { return new ExecutionContext(1,1,1,1,['product:prices:prepare','product:prices:apply'],'api_key',null,null,null,null,true); }
if(($argv[1]??'')==='worker') {
    $pdo=connection(getenv('PRICE_TEST_DSN'));
    $result=(new ApplyPricesRepository($pdo))->apply(json_decode($argv[2],true),actor());
    if($result['summary']['applied']!==1)exit(2);
    echo $result['applied_this_call'];
    exit;
}
$dsn=getenv('PRICE_TEST_DSN');
if(!$dsn || strpos($dsn,'dbname=e998')===false)throw new RuntimeException('Use the confirmed local e998 test DSN.');
$admin=connection($dsn);
$schema='mcp_price_test_'.bin2hex(random_bytes(6));
$testDsn=preg_replace('/dbname=[^;]+/','dbname='.$schema,$dsn);
$admin->exec('CREATE DATABASE `'.$schema.'`');
$processes=[];
try {
    $pdo=connection($testDsn);
    $migration=file_get_contents(dirname(__DIR__,3).'/scripts/atualizacaoBase.php');
    foreach(['MCP020','MCP021'] as $table) {
        preg_match('/CREATE TABLE IF NOT EXISTS '.$table.' \(.*?ENGINE=InnoDB;/s',$migration,$m);
        $pdo->exec($m[0]);
    }
    $pdo->exec('CREATE TABLE D001 (D001_Id BIGINT PRIMARY KEY,D001_Codigo_Produto VARCHAR(50),D001_Flag_Ativo CHAR(1)) ENGINE=InnoDB');
    $pdo->exec('CREATE TABLE D049 (D049_Id BIGINT PRIMARY KEY,D049_D001_Id BIGINT) ENGINE=InnoDB');
    $pdo->exec('CREATE TABLE D001A (D001A_Id BIGINT PRIMARY KEY) ENGINE=InnoDB');
    $cols=[];
    for($n=1;$n<=4;$n++) { $cols[]='D009_Preco_'.$n.' DECIMAL(20,10) DEFAULT 10'; $cols[]='D009_IPT_'.$n.' DECIMAL(20,10) DEFAULT 0'; }
    $pdo->exec('CREATE TABLE D009 (D009_Id BIGINT PRIMARY KEY,D009_D049_Id BIGINT,D009_C004_Id BIGINT,D009_Data_Alteracao_Preco DATETIME NULL,'.implode(',',$cols).') ENGINE=InnoDB');
    $pdo->exec('CREATE TABLE D009A (D009A_Id BIGINT PRIMARY KEY,D009A_D009_Id BIGINT UNIQUE,D009A_Preco_5 DECIMAL(20,10),D009A_IPT_5 DECIMAL(20,10) DEFAULT 0) ENGINE=InnoDB');
    $pdo->exec("INSERT INTO D001 VALUES (1,'001','S')");
    $pdo->exec('INSERT INTO D049 VALUES (1,1)');
    $pdo->exec('INSERT INTO D009 (D009_Id,D009_D049_Id,D009_C004_Id) VALUES (1,1,1)');
    $pdo->exec('INSERT INTO D009A VALUES (1,1,50,0)');
    $p=(new PreparePricesRepository($pdo))->prepare(PreparePricesInput::normalize(['request_key'=>'concurrent','origin'=>'manual','items'=>[['line'=>1,'code'=>'001','prices'=>['price_1'=>'20','price_5'=>'60']]]]),actor());
    $a=['batch_id'=>$p['batch_id'],'preview_hash'=>$p['preview_hash'],'confirmed'=>true,'allow_partial'=>false,'allow_zero'=>false];
    // Both independent workers will contend on the same batch lock.
    $pdo->beginTransaction();
    $pdo->query('SELECT MCP020_Id FROM MCP020 WHERE MCP020_Id=1 FOR UPDATE')->fetch();
    putenv('PRICE_TEST_DSN='.$testDsn);
    for($n=0;$n<2;$n++) {
        $pipes=[];
        $process=proc_open([PHP_BINARY,__FILE__,'worker',json_encode($a)],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
        if(!is_resource($process))throw new RuntimeException('Worker creation failed.');
        fclose($pipes[0]);
        $processes[]=[$process,$pipes];
    }
    $pdo->commit();
    $applied=0;
    foreach($processes as [$process,$pipes]) {
        $out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);
        fclose($pipes[1]);fclose($pipes[2]);
        if(proc_close($process)!==0)throw new RuntimeException('Concurrent worker failed: '.$err);
        $applied+=(int)$out;
    }
    $processes=[];
    $attempts=(int)$pdo->query('SELECT MCP021_Tentativas FROM MCP021')->fetchColumn();
    if($applied!==1 || $attempts!==1 || $pdo->query('SELECT D009_Preco_1 FROM D009')->fetchColumn()!=='20.0000000000')throw new RuntimeException('Duplicate application detected.');
    echo "PASS two simultaneous workers apply the product exactly once\n";
    // Real SQL trigger mismatch must roll back both prices and leave a retryable item.
    $p=(new PreparePricesRepository($pdo))->prepare(PreparePricesInput::normalize(['request_key'=>'trigger','origin'=>'manual','items'=>[['line'=>1,'code'=>'001','prices'=>['price_1'=>'30','price_5'=>'70']]]]),actor());
    $pdo->exec('CREATE TRIGGER test_override BEFORE UPDATE ON D009A FOR EACH ROW SET NEW.D009A_Preco_5=999');
    $a['batch_id']=$p['batch_id'];$a['preview_hash']=$p['preview_hash'];
    $result=(new ApplyPricesRepository($pdo))->apply($a,actor());
    if($result['summary']['failed']!==1 || $pdo->query('SELECT D009_Preco_1 FROM D009')->fetchColumn()!=='20.0000000000' || $pdo->query('SELECT D009A_Preco_5 FROM D009A')->fetchColumn()!=='60.0000000000')throw new RuntimeException('Trigger mismatch was not rolled back.');
    echo "PASS SQL trigger price mismatch rolls back both tables\n";
    $pdo->exec('DROP TRIGGER test_override');
    $pdo->exec("INSERT INTO D001 VALUES (2,'002','S')");
    $pdo->exec('INSERT INTO D049 VALUES (2,2)');
    $pdo->exec('INSERT INTO D009 (D009_Id,D009_D049_Id,D009_C004_Id) VALUES (2,2,1)');
    $pdo->exec('INSERT INTO D009A VALUES (2,2,50,0)');
    $p=(new PreparePricesRepository($pdo))->prepare(PreparePricesInput::normalize(['request_key'=>'resume-two','origin'=>'spreadsheet','items'=>[
        ['line'=>1,'code'=>'001','prices'=>['price_1'=>'40','price_5'=>'80']],
        ['line'=>2,'code'=>'002','prices'=>['price_1'=>'40','price_5'=>'80']],
    ]]),actor());
    $pdo->exec('CREATE TRIGGER fail_second BEFORE UPDATE ON D009A FOR EACH ROW SET NEW.D009A_Preco_5=IF(NEW.D009A_Id=2,999,NEW.D009A_Preco_5)');
    $a['batch_id']=$p['batch_id'];$a['preview_hash']=$p['preview_hash'];
    $first=(new ApplyPricesRepository($pdo))->apply($a,actor());
    if($first['summary']['applied']!==1 || $first['summary']['failed']!==1)throw new RuntimeException('Expected committed first item and failed second item.');
    $pdo->exec('DROP TRIGGER fail_second');
    $second=(new ApplyPricesRepository($pdo))->apply($a,actor());
    if($second['summary']['applied']!==2 || $second['applied_this_call']!==1)throw new RuntimeException('Resume repeated completed work.');
    echo "PASS partial batch resumes only its failed item after recovery\n";

} finally {
    foreach($processes as [$process,$pipes])if(is_resource($process)){proc_terminate($process);proc_close($process);}
    if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();
    putenv('PRICE_TEST_DSN='.$dsn);
    $admin->exec('DROP DATABASE `'.$schema.'`');
}
