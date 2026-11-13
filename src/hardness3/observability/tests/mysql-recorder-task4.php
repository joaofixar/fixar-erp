<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) { function observationAssert(bool $condition,string $message):void{if(!$condition){throw new RuntimeException($message);}} }

use Hardness\Observability\Collectors\Mysql\MysqlRecorderCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderSchemaProvider;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderServerRunner;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Collectors\Mysql\RecorderQuerySource;
use Hardness\Observability\Config\SiteTenantProvider;

$task4Root=sys_get_temp_dir().'/observability-recorder-task4-'.getmypid().'-'.bin2hex(random_bytes(4));@mkdir($task4Root,0700,true);
$task4Sites=$task4Root.'/sites';@mkdir($task4Sites,0700,true);
for($i=1;$i<=5;$i++){ $host='client-'.$i.'.test';$config=array('dbDatabase'=>'tenant_'.$i,'dbHost'=>'mysql.internal','dbPort'=>3306,'dbUser'=>'user','dbPass'=>'secret','pathRaiz'=>'/var/www','urlRaiz'=>'https://'.$host.'/');file_put_contents($task4Sites.'/'.$host.'-confUsuario.php','<?php $confUsuario='.var_export($config,true).';'); }
$other=array('dbDatabase'=>'outside','dbHost'=>'other.internal','dbPort'=>3306,'dbUser'=>'user','dbPass'=>'secret','pathRaiz'=>'/var/www','urlRaiz'=>'https://outside.test/');file_put_contents($task4Sites.'/outside.test-confUsuario.php','<?php $confUsuario='.var_export($other,true).';');
$otherPort=array('dbDatabase'=>'outside_port','dbHost'=>'mysql.internal','dbPort'=>3307,'dbUser'=>'user','dbPass'=>'secret','pathRaiz'=>'/var/www','urlRaiz'=>'https://outside-port.test/');file_put_contents($task4Sites.'/outside-port.test-confUsuario.php','<?php $confUsuario='.var_export($otherPort,true).';');
$schemas=(new MysqlRecorderSchemaProvider(new SiteTenantProvider($task4Sites)))->forServer(array('database'=>array('host'=>'mysql.internal','port'=>3306)));
observationAssert($schemas===array('tenant_1','tenant_2','tenant_3','tenant_4','tenant_5'),'server recorder must derive only same-host and same-port schemas from the trusted backend inventory');

$serverSource=new class implements RecorderQuerySource {
    public $calls=array('connection'=>0,'processlist'=>0,'locks'=>0,'transactions'=>0,'status'=>0,'errors'=>0,'digests'=>0); public $round=0;
    public function rows(string $sql,array $parameters=array()):array{
        if(strpos($sql,'CONNECTION_ID()')!==false){$this->calls['connection']++;return array(array('connection_id'=>999));}
        if(strpos($sql,'SHOW FULL PROCESSLIST')!==false){$this->calls['processlist']++;$rows=array();for($i=1;$i<=5;$i++){$rows[]=array('Id'=>$i,'User'=>'app','Host'=>'10.0.0.'.$i,'db'=>'tenant_'.$i,'Command'=>'Query','Time'=>20+$i,'State'=>'executing','Info'=>'SELECT * FROM T'.$i.' WHERE ID='.$i);}return $rows;}
        if(strpos($sql,'data_lock_waits')!==false){$this->calls['locks']++;return array(array('waiting_process_id'=>2,'blocking_process_id'=>1,'object_schema'=>'tenant_2','object_name'=>'T2','waiting_database'=>'tenant_2','blocking_database'=>'tenant_1','waiting_seconds'=>25,'blocking_seconds'=>30));}
        if(strpos($sql,'INNODB_TRX')!==false){$this->calls['transactions']++;observationAssert($parameters===array(),'server transaction query must not run once per schema');return array(array('transaction_id'=>'trx-3','transaction_state'=>'RUNNING','age_seconds'=>40,'process_id'=>3,'database_name'=>'tenant_3','transaction_sql'=>'UPDATE T3 SET A=1'));}
        if(strpos($sql,'SHOW GLOBAL STATUS')!==false){$this->calls['status']++;return array(array('Variable_name'=>'Uptime','Value'=>(string)(1000+$this->round*300)),array('Variable_name'=>'Connections','Value'=>(string)(100+$this->round*10)),array('Variable_name'=>'Threads_connected','Value'=>'5'),array('Variable_name'=>'Threads_running','Value'=>'2'));}
        if(strpos($sql,'events_errors_summary_global_by_error')!==false){$this->calls['errors']++;return array();}
        if(strpos($sql,'events_statements_summary_by_digest')!==false){$this->calls['digests']++;observationAssert($parameters===array(),'server digest query must be global and fixed');$rows=array();for($i=1;$i<=5;$i++){$rows[]=array('database_name'=>'tenant_'.$i,'digest'=>'digest-'.$i,'digest_text'=>'SELECT * FROM T'.$i.' WHERE ID = ?','executions'=>10+$this->round*2,'total_wait_picoseconds'=>1000000000+$this->round*500000000,'lock_time_picoseconds'=>0);}return $rows;}
        throw new RuntimeException('unexpected server recorder query');
    }
    public function close():void{}
};
$serverCollector=new MysqlRecorderCollector($serverSource);
$batch=$serverCollector->collectFastServer($schemas,15,15);
observationAssert(count($batch['records'])===5&&$serverSource->calls['processlist']===1&&$serverSource->calls['locks']===1&&$serverSource->calls['transactions']===1,'five schemas must share one processlist, lock and transaction collection');
observationAssert(count($batch['records']['tenant_2']['locks'])===1&&count($batch['records']['tenant_3']['long_transactions'])===1&&$batch['records']['tenant_5']['long_queries'][0]['process_id']===5,'global evidence must be attributed to the responsible schema');
$digestBatch=$serverCollector->collectDigestsServer($schemas);
observationAssert($serverSource->calls['digests']===1&&count($digestBatch['records'])===5&&$digestBatch['records']['tenant_4']['digests'][0]['digest']==='digest-4','all schema digests must come from one bounded global query');
$manySchemas=array();for($i=1;$i<=35;$i++){$manySchemas[]='tenant_'.$i;}
$manyBatch=$serverCollector->collectFastServer($manySchemas,15,15);$manyDigestBatch=$serverCollector->collectDigestsServer($manySchemas);
observationAssert(count($manyBatch['records'])===35&&$serverSource->calls['connection']===2&&$serverSource->calls['processlist']===2&&$serverSource->calls['locks']===2&&$serverSource->calls['transactions']===2,'thirty-five schemas must still use one global fast collection per source');
observationAssert(count($manyDigestBatch['records'])===35&&$serverSource->calls['digests']===2,'thirty-five schemas must still use one bounded global digest query');
observationAssert(isset($manyBatch['collection']['source_duration_ms']['processlist'])&&isset($manyDigestBatch['collection']['source_duration_ms']['digests']),'global batches must expose per-source duration for the performance pilot');

foreach($serverSource->calls as $key=>$ignored){$serverSource->calls[$key]=0;}
putenv('OBSERVABILITY_TEST_MODE=1');putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH='.$task4Root.'/storage');$serverStorage=new MysqlRecorderStorage();$task4Now=2000000.0;
$serverRunner=new MysqlRecorderServerRunner($serverCollector,$serverStorage,null,function()use(&$task4Now):float{return $task4Now;},function(float $seconds)use(&$task4Now):void{$task4Now+=$seconds;});
$firstRun=$serverRunner->run($schemas,true);
observationAssert($firstRun['active']===true&&$firstRun['schemas']===5&&$serverSource->calls['processlist']===1&&$serverSource->calls['locks']===1&&$serverSource->calls['transactions']===1&&$serverSource->calls['status']===1&&$serverSource->calls['errors']===1&&$serverSource->calls['digests']===1,'one persisted server cycle must execute each global source only once');
$day=date('Y-m-d');$eventLines=file($task4Root.'/storage/events/'.$day.'.jsonl',FILE_IGNORE_NEW_LINES);$statusLines=file($task4Root.'/storage/status/'.$day.'.jsonl',FILE_IGNORE_NEW_LINES);$digestLines=file($task4Root.'/storage/digests/'.$day.'.jsonl',FILE_IGNORE_NEW_LINES);
observationAssert(count($eventLines)===5&&count($statusLines)===1&&count($digestLines)===5,'server cycle must persist schema events/digests separately and global status once');
$globalStatus=json_decode($statusLines[0],true);observationAssert($globalStatus['database']==='server'&&$globalStatus['scope']==='server','global metrics must be explicitly server-scoped');
foreach($digestLines as $digestLine){$persistedDigest=json_decode($digestLine,true);foreach($persistedDigest['counters']['items'] as $item){observationAssert(!array_key_exists('absolute',$item),'digest history must not duplicate absolute counters kept in server state');}}
observationAssert(file_exists($task4Root.'/storage/state/server.json')&&!file_exists($task4Root.'/storage/state/tenant_1.json'),'single recorder must keep one server state and one server lock scope');
$tooMany=array();for($i=0;$i<101;$i++){$tooMany[]='schema_'.$i;}$schemaLimitRejected=false;try{$serverRunner->run($tooMany,true);}catch(InvalidArgumentException $exception){$schemaLimitRejected=true;}observationAssert($schemaLimitRejected,'server recorder must reject more than 100 schemas before collecting');
$task4Now+=300;$serverSource->round=1;$secondRun=$serverRunner->run($schemas,true);$digestLines=file($task4Root.'/storage/digests/'.$day.'.jsonl',FILE_IGNORE_NEW_LINES);$lastDigest=json_decode($digestLines[count($digestLines)-1],true);
observationAssert($secondRun['active']===true&&$serverSource->calls['processlist']===2&&$serverSource->calls['digests']===2&&$lastDigest['counters']['items'][0]['baseline']===false&&(float)$lastDigest['counters']['items'][0]['delta']['executions']===2.0,'subsequent server cycle must preserve per-schema delta baselines without duplicating global queries');
$digestLineCount=count($digestLines);$task4Now+=300;$thirdRun=$serverRunner->run($schemas,true);$unchangedDigestLines=file($task4Root.'/storage/digests/'.$day.'.jsonl',FILE_IGNORE_NEW_LINES);
observationAssert($thirdRun['active']===true&&count($unchangedDigestLines)===$digestLineCount,'unchanged digest counters must update state without growing digest history');

$serverCode=file_get_contents(dirname(__DIR__).'/src/Collectors/Mysql/MysqlRecorderCollector.php').file_get_contents(dirname(__DIR__).'/src/Collectors/Mysql/MysqlRecorderServerRunner.php');
foreach(array('INSERT ','UPDATE ','DELETE ','ALTER ','DROP ','TRUNCATE ','KILL ','FLUSH ','SET GLOBAL') as $mutable){observationAssert(stripos($serverCode,$mutable)===false,'server recorder must remain MySQL read-only: '.$mutable);}
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 4 fixtures passed\n";
