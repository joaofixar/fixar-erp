<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) { function observationAssert(bool $condition,string $message):void{if(!$condition){throw new RuntimeException($message);}} }

use Hardness\Observability\Collectors\Mysql\MysqlRecorderDeltaCalculator;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryRepository;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;

$task9Root=sys_get_temp_dir().'/observability-recorder-task9-'.getmypid().'-'.bin2hex(random_bytes(4));
putenv('OBSERVABILITY_TEST_MODE=1');putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH='.$task9Root.'/history');$storage=new MysqlRecorderStorage();

$items=array();
for($i=0;$i<250;$i++){$items[]=array('digest'=>hash('sha256','digest-'.$i),'query'=>'SELECT '.str_repeat('x',1800).' WHERE ID=?','first_seen'=>'2026-10-13 10:00:00','last_seen'=>'2026-10-13 10:05:00','maximum_time_ms'=>10,'baseline'=>false,'baseline_reason'=>null,'delta'=>array('executions'=>1,'total_time_ms'=>10,'lock_time_ms'=>0,'rows_examined'=>1,'rows_sent'=>1,'rows_affected'=>0,'errors'=>0,'warnings'=>0,'temporary_tables'=>0,'temporary_disk_tables'=>0,'no_index_used'=>0,'no_good_index_used'=>0));}
$largeDigest=array('schema_version'=>3,'sample_type'=>'digests','scope'=>'schema','collected_at'=>'2026-10-13T10:05:00-03:00','database'=>'e229','collection'=>array('partial'=>false,'failures'=>array()),'counters'=>array('items'=>$items,'disappeared_digests'=>array(),'coverage_limited'=>true));
$append=$storage->append('digests',$largeDigest,'2026-10-13');
$path=$storage->baseDirectory().'/digests/2026-10-13.jsonl';$lines=file($path,FILE_IGNORE_NEW_LINES);
observationAssert($append['chunked']===true&&$append['written']===true&&count($lines)>1,'large digest samples must be split into multiple complete JSONL records');
$restored=0;foreach($lines as $index=>$line){observationAssert(strlen($line)+1<=MysqlRecorderStorage::MAX_RECORD_BYTES,'every persisted chunk must remain readable by the repository');$decoded=json_decode($line,true);observationAssert(($decoded['collection']['digest_chunk']['index']??0)===$index+1&&($decoded['collection']['digest_chunk']['total']??0)===count($lines),'digest chunks must carry stable sequence metadata');$restored+=count($decoded['counters']['items']??array());}
observationAssert($restored===250,'chunking must preserve every digest item exactly once');
$read=(new MysqlRecorderHistoryRepository($storage->baseDirectory()))->read('digests',array('e229'),strtotime('2026-10-13 09:00:00'),strtotime('2026-10-13 11:00:00'));
$readItems=0;foreach($read['records'] as $record){$readItems+=count($record['counters']['items']??array());}
observationAssert($read['available']===true&&$read['invalid_lines']===0&&$readItems===250,'the history reader must accept every chunk produced by storage');

$rawState=array();for($i=0;$i<2000;$i++){$rawState[]=array('digest'=>hash('sha256','state-'.$i),'query'=>str_repeat('Q',2000),'executions'=>$i,'total_time_ms'=>$i,'lock_time_ms'=>0,'rows_examined'=>$i,'rows_sent'=>1,'rows_affected'=>0,'errors'=>0,'warnings'=>0,'temporary_tables'=>0,'temporary_disk_tables'=>0,'no_index_used'=>0,'no_good_index_used'=>0);}
$compact=(new MysqlRecorderDeltaCalculator())->compactDigestsForState($rawState);
observationAssert(count($compact)===2000&&!array_key_exists('query',$compact[0])&&strlen((string)json_encode($compact))<4194304,'state compaction must remove SQL text and remain safely below the former 4 MiB limit');
$storage->saveState('server',array('digests_absolute_by_schema'=>array('e229'=>$compact)));$loaded=$storage->loadState('server');
observationAssert(count($loaded['digests_absolute_by_schema']['e229'])===2000,'maximum digest state must survive an atomic save/load cycle');

$oversizedRejected=false;try{$storage->append('events',array('database'=>'e229','payload'=>str_repeat('z',MysqlRecorderStorage::MAX_RECORD_BYTES)),'2026-10-13');}catch(RuntimeException $exception){$oversizedRejected=$exception->getMessage()==='mysql_recorder_record_too_large';}
observationAssert($oversizedRejected,'non-digest records larger than the reader limit must be rejected before writing');

putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 9 fixtures passed\n";
