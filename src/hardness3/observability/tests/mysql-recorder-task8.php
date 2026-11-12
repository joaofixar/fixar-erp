<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) { function observationAssert(bool $condition,string $message):void{if(!$condition){throw new RuntimeException($message);}} }

use Hardness\Observability\Collectors\Mysql\MysqlRecorderCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderServerRunner;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Collectors\Mysql\RecorderQuerySource;

$task8Source=new class implements RecorderQuerySource {
    public $round=0; public $digestSql='';
    public function rows(string $sql,array $parameters=array()):array {
        if(strpos($sql,'CONNECTION_ID()')!==false){return array(array('connection_id'=>999));}
        if(strpos($sql,'SHOW FULL PROCESSLIST')!==false||strpos($sql,'data_lock_waits')!==false||strpos($sql,'INNODB_TRX')!==false){return array();}
        if(strpos($sql,'SHOW GLOBAL STATUS')!==false){return array(array('Variable_name'=>'Uptime','Value'=>(string)(1000+$this->round*300)));}
        if(strpos($sql,'events_errors_summary_global_by_error')!==false){return array();}
        if(strpos($sql,'events_statements_summary_by_digest')!==false){
            $this->digestSql=$sql;$ids=$this->round===0?array('digest-a','digest-b'):array('digest-a','digest-c');$rows=array();
            foreach($ids as $id){$rows[]=array('database_name'=>'e229','digest'=>$id,'digest_text'=>'SELECT ?','executions'=>$this->round===0?10:12,'total_wait_picoseconds'=>$this->round===0?1000000000:1200000000,'total_digest_rows'=>3);}
            return $rows;
        }
        throw new RuntimeException('unexpected Task 8 query');
    }
    public function close():void{}
};

$task8Collector=new MysqlRecorderCollector($task8Source);$preview=$task8Collector->collectDigestsServer(array('e229'));
observationAssert($preview['collection']['coverage_limited']===true&&$preview['collection']['result_limit_reached']===false&&$preview['collection']['total_digest_rows']===3&&$preview['collection']['selected_digest_rows']===2,'collector must distinguish sampled coverage from the hard result limit');
foreach(array('COUNT(*) OVER() AS total_digest_rows','SUM_TIMER_WAIT DESC, DIGEST ASC','COUNT_STAR DESC, DIGEST ASC','digest ASC LIMIT 2001') as $fragment){observationAssert(strpos($task8Source->digestSql,$fragment)!==false,'digest ranking must be deterministic and expose total coverage: '.$fragment);}

$task8Root=sys_get_temp_dir().'/observability-recorder-task8-'.getmypid().'-'.bin2hex(random_bytes(4));
putenv('OBSERVABILITY_TEST_MODE=1');putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH='.$task8Root.'/history');$storage=new MysqlRecorderStorage();$task8Now=3000000.0;
$runner=new MysqlRecorderServerRunner($task8Collector,$storage,null,function()use(&$task8Now):float{return $task8Now;},function(float $seconds)use(&$task8Now):void{$task8Now+=$seconds;});
$runner->run(array('e229'),true);$task8Now+=300;$task8Source->round=1;$runner->run(array('e229'),true);
$lines=file($storage->baseDirectory().'/digests/'.date('Y-m-d').'.jsonl',FILE_IGNORE_NEW_LINES);$last=json_decode($lines[count($lines)-1],true);
observationAssert($last['counters']['coverage_limited']===true&&$last['counters']['disappeared_digests']===array(),'a digest leaving a sampled ranking must not be reported as disappeared');
$ids=array();foreach($last['counters']['items'] as $item){$ids[]=$item['digest'];}
observationAssert(in_array('digest-a',$ids,true)&&in_array('digest-c',$ids,true),'sampled ranking must keep valid deltas and new baselines while suppressing only false disappearance');

putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 8 fixtures passed\n";
