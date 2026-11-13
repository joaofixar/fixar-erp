<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) { function observationAssert(bool $condition,string $message):void{if(!$condition){throw new RuntimeException($message);}} }

use Hardness\Observability\Collectors\Mysql\MysqlRecorderCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderDeltaCalculator;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryRepository;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryService;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Collectors\Mysql\RecorderQuerySource;

$task7Source=new class implements RecorderQuerySource {
    public $digestSql='';
    public function rows(string $sql,array $parameters=array()):array {
        if(strpos($sql,'CONNECTION_ID()')!==false){return array(array('connection_id'=>9999));}
        if(strpos($sql,'SHOW FULL PROCESSLIST')!==false){$rows=array();for($i=1;$i<=101;$i++){$rows[]=array('Id'=>$i,'User'=>'app','Host'=>'local','db'=>'e229','Command'=>'Query','Time'=>20,'State'=>'executing','Info'=>'SELECT * FROM T WHERE ID='.$i);}return $rows;}
        if(strpos($sql,'data_lock_waits')!==false){$rows=array();for($i=1;$i<=100;$i++){$rows[]=array('waiting_process_id'=>$i,'blocking_process_id'=>500+$i,'object_schema'=>'e229','object_name'=>'T','waiting_database'=>'e229','blocking_database'=>'e229','waiting_seconds'=>20,'blocking_seconds'=>30);}return $rows;}
        if(strpos($sql,'INNODB_TRX')!==false){$rows=array();for($i=1;$i<=500;$i++){$rows[]=array('transaction_id'=>'trx-'.$i,'transaction_state'=>'RUNNING','age_seconds'=>20,'process_id'=>$i,'database_name'=>'e229','transaction_sql'=>'SELECT ?');}return $rows;}
        if(strpos($sql,'events_statements_summary_by_digest')!==false){$this->digestSql=$sql;$rows=array();for($i=1;$i<=2001;$i++){$rows[]=array('database_name'=>'e229','digest'=>'digest-'.$i,'digest_text'=>'SELECT ?','executions'=>$i,'total_wait_picoseconds'=>$i);}return $rows;}
        if(strpos($sql,'SHOW GLOBAL STATUS')!==false){observationAssert(strpos($sql,'Performance_schema_digest_lost')!==false,'status collection must request Performance_schema_digest_lost');return array(array('Variable_name'=>'Uptime','Value'=>'1000'),array('Variable_name'=>'Performance_schema_digest_lost','Value'=>'7'));}
        if(strpos($sql,'events_errors_summary_global_by_error')!==false){return array();}
        throw new RuntimeException('unexpected Task 7 query');
    }
    public function close():void{}
};

$task7Collector=new MysqlRecorderCollector($task7Source);
$fast=$task7Collector->collectFastServer(array('e229'));
observationAssert($fast['records']['e229']['collection']['coverage_limited']===array('locks'=>true,'long_queries'=>true,'long_transactions'=>true),'fast collection must explicitly flag every reached result limit');
$digestBatch=$task7Collector->collectDigestsServer(array('e229'));
observationAssert($digestBatch['collection']['result_limit_reached']===true&&count($digestBatch['records']['e229']['digests'])===2000,'digest collection must use a sentinel row and keep the declared maximum');
foreach(array('schema_rank','total_time_rank','execution_rank','rows_rank','lock_rank','issue_rank','LIMIT 2001') as $selection){observationAssert(strpos($task7Source->digestSql,$selection)!==false,'digest selection must cover '.$selection);}
$status=$task7Collector->collectStatus();
observationAssert(($status['status']['Performance_schema_digest_lost']??null)===7,'digest loss counter must be collected');
$statusDelta=(new MysqlRecorderDeltaCalculator())->status(array('Uptime'=>1100,'Performance_schema_digest_lost'=>9),array(),array('Uptime'=>1000,'Performance_schema_digest_lost'=>7),array());
observationAssert(($statusDelta['delta']['Performance_schema_digest_lost']??null)===2.0,'digest loss counter must participate in interval deltas');

$task7Root=sys_get_temp_dir().'/observability-recorder-task7-'.getmypid().'-'.bin2hex(random_bytes(4));
putenv('OBSERVABILITY_TEST_MODE=1');putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH='.$task7Root.'/history');$storage=new MysqlRecorderStorage();
for($i=0;$i<3;$i++){$at=sprintf('2026-10-13T10:00:%02d-03:00',$i*10);$storage->append('events',array('schema_version'=>3,'sample_type'=>'events','scope'=>'schema','collected_at'=>$at,'database'=>'e229','locks'=>array(),'long_queries'=>array(array('process_id'=>44,'seconds'=>20+$i*10,'query'=>'SELECT * FROM T WHERE ID=?','fingerprint'=>'same-query')),'long_transactions'=>array(),'collection'=>array('partial'=>false,'failures'=>array(),'coverage_limited'=>array('locks'=>$i===0,'long_queries'=>$i===0,'long_transactions'=>$i===0))),'2026-10-13');}
$storage->append('status',array('schema_version'=>3,'sample_type'=>'status','scope'=>'server','collected_at'=>'2026-10-13T10:00:20-03:00','database'=>'server','collection'=>array('partial'=>false,'failures'=>array()),'counters'=>array('baseline'=>true,'absolute'=>array('Performance_schema_digest_lost'=>7),'delta'=>null,'errors'=>array())),'2026-10-13');
$history=(new MysqlRecorderHistoryService(new MysqlRecorderHistoryRepository($task7Root.'/history')))->history(array('e229','server'),array('view'=>true,'full_sql'=>false,'connection_identity'=>true),strtotime('2026-10-13 09:00:00'),strtotime('2026-10-13 11:00:00'));
observationAssert(count($history['timeline'])===1&&$history['timeline'][0]['sample_count']===3&&$history['timeline'][0]['duration_seconds']===40,'consecutive observations of one query must be consolidated into one incident with the maximum duration');
observationAssert($history['signals']['lock_coverage_limited']===true&&$history['signals']['long_query_coverage_limited']===true&&$history['signals']['transaction_coverage_limited']===true&&$history['signals']['digest_entries_lost']===7&&$history['metadata']['partial']===true,'coverage and Performance Schema loss must remain explicit in the API');

putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 7 fixtures passed\n";
