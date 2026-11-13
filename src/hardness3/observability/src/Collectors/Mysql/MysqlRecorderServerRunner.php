<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Um único ciclo por servidor; fontes globais são lidas uma vez e particionadas por schema. */
final class MysqlRecorderServerRunner
{
    public const STATUS_INTERVAL_SECONDS = 60;
    public const DIGEST_INTERVAL_SECONDS = 300;
    private $collector; private $storage; private $deltas; private $clock; private $sleeper;

    public function __construct(MysqlRecorderCollector $collector, MysqlRecorderStorage $storage, ?MysqlRecorderDeltaCalculator $deltas = null, ?callable $clock = null, ?callable $sleeper = null)
    {
        $this->collector=$collector; $this->storage=$storage; $this->deltas=$deltas ?? new MysqlRecorderDeltaCalculator();
        $this->clock=$clock ?? function (): float { return microtime(true); }; $this->sleeper=$sleeper ?? function (float $seconds): void { if ($seconds>0) { usleep((int) round($seconds*1000000)); } };
    }

    public function run(array $databases, bool $once = false, int $durationSeconds = 50, int $intervalSeconds = 10, int $longQuerySeconds = 15, int $longTransactionSeconds = 15, int $retentionDays = 7): array
    {
        $databases=$this->databases($databases); $durationSeconds=max(1,min(300,$durationSeconds)); $intervalSeconds=max(1,min(60,$intervalSeconds)); $retentionDays=max(1,min(90,$retentionDays));
        $lock=new MysqlRecorderRunLock(); if (!$lock->acquire($this->storage->lockPath('server'))) { return array('active'=>false,'reason'=>'another_server_collection_is_active','samples'=>0,'partial'=>false,'failures'=>array()); }
        $started=$this->now(); $deadline=$started+$durationSeconds; $samples=0; $failures=array(); $limits=array(); $budgetOverruns=0;
        try {
            try { $retention=$this->storage->retain($retentionDays,(int)$started); if ($retention['failures']!==array()) { $failures[]=array('section'=>'retention','message'=>'mysql_recorder_retention_partial'); } }
            catch (\Throwable $exception) { $retention=array('removed'=>array(),'failures'=>array('retention_failed'),'retention_days'=>$retentionDays); $failures[]=$this->failure('retention',$exception); }
            do {
                $sampleStarted=$this->now(); $state=$this->storage->loadState('server') ?? array('schema_version'=>2,'scope'=>'server');
                $events=$this->collector->collectFastServer($databases,$longQuerySeconds,$longTransactionSeconds);
                foreach ($events['records'] as $record) { $counts=$record['collection']['counts']; $coverage=array_filter($record['collection']['coverage_limited']??array()); if (array_sum($counts)===0&&empty($record['collection']['partial'])&&$coverage===array()) { continue; } $record['runner']=array('scope'=>'server','started_at_epoch'=>$sampleStarted,'success'=>!$record['collection']['partial']); $this->persist('events',$record,$limits,$failures); }
                $restart=false;
                if ($this->due($state['status_collected_at']??null,$sampleStarted,self::STATUS_INTERVAL_SECONDS)) {
                    $status=$this->collector->collectStatus(); $status['database']='server'; $status['scope']='server';
                    $statusFailed=$this->collectionFailed($status['collection'],'status'); $errorsFailed=$this->collectionFailed($status['collection'],'errors');
                    $statusValues=$statusFailed?($state['status_absolute']??array()):$status['status']; $errorValues=$errorsFailed?array():$status['errors'];
                    $calculated=$this->deltas->status($statusValues,$errorValues,$state['status_absolute']??null,$state['errors_absolute']??null);
                    if ($statusFailed) { $calculated['mysql_restart_detected']=false; $calculated['baseline']=true; $calculated['baseline_reason']='collection_failed'; $calculated['absolute']=null; $calculated['delta']=null; }
                    if ($errorsFailed) { $calculated['errors']=array(); $calculated['errors_unavailable']=true; }
                    $restart=!$statusFailed&&!empty($calculated['mysql_restart_detected']); $status['counters']=$calculated; unset($status['status'],$status['errors']); $this->persist('status',$status,$limits,$failures);
                    $state['status_collected_at']=$sampleStarted; if (!$statusFailed) { $state['status_absolute']=$statusValues; } if (!$errorsFailed) { $state['errors_absolute']=$this->rawErrors($calculated['errors']); } if ($restart) { $state['mysql_restart_at']=$sampleStarted; }
                }
                if ($this->due($state['digests_collected_at']??null,$sampleStarted,self::DIGEST_INTERVAL_SECONDS)) {
                    $batch=$this->collector->collectDigestsServer($databases); $restartSinceLast=$restart||(isset($state['mysql_restart_at'])&&(!isset($state['digests_collected_at'])||(float)$state['mysql_restart_at']>(float)$state['digests_collected_at']));
                    $batchFailed=!empty($batch['collection']['partial']); $digestCoverageLimited=!empty($batch['collection']['coverage_limited']);
                    foreach ($batch['records'] as $database=>$record) {
                        $previous=$state['digests_absolute_by_schema'][$database]??null;
                        $calculated=$batchFailed?array('items'=>array(),'disappeared_digests'=>array(),'unavailable'=>true):$this->deltas->digests($record['digests'],$previous,$restartSinceLast);
                        if($digestCoverageLimited){$calculated['disappeared_digests']=array();}
                        $calculated['coverage_limited']=$digestCoverageLimited;
                        if (!$batchFailed) { $state['digests_absolute_by_schema'][$database]=$this->deltas->compactDigestsForState($record['digests']); }
                        $record['counters']=$this->deltas->compactDigestsForHistory($calculated);
                        $record['collection']['counts']['observed_digests']=$record['collection']['counts']['digests']??0;
                        $record['collection']['counts']['digests']=count($record['counters']['items']);
                        unset($record['digests']); if($this->hasDigestHistory($record['counters'])){$this->persist('digests',$record,$limits,$failures);}
                    }
                    $state['digests_collected_at']=$sampleStarted;
                }
                $state['schema_version']=2; $state['scope']='server'; $state['databases']=$databases; $state['updated_at']=date('c',(int)$sampleStarted);
                try { $this->storage->saveState('server',$state); } catch (\Throwable $exception) { $failures[]=$this->failure('state',$exception); }
                $samples++; $sampleElapsed=$this->now()-$sampleStarted; if ($sampleElapsed>$intervalSeconds) { $budgetOverruns++; }
                if ($once) { break; } $next=min($deadline,$sampleStarted+$intervalSeconds); $remaining=$next-$this->now(); if ($remaining>0) { ($this->sleeper)($remaining); }
            } while ($this->now()<$deadline);
            if ($budgetOverruns>0) { $failures[]=array('section'=>'performance_budget','message'=>'collection_interval_exceeded'); }
            if ($limits!==array()||$failures!==array()) { $this->persistHealth($limits,$failures,$budgetOverruns); }
            return array('active'=>true,'scope'=>'server','schemas'=>count($databases),'samples'=>$samples,'duration_ms'=>round(($this->now()-$started)*1000,2),'partial'=>$failures!==array()||$limits!==array(),'failures'=>$failures,'file_limits'=>array_values(array_unique($limits)),'performance_budget_overruns'=>$budgetOverruns,'retention'=>$retention);
        } finally { $lock->release(); }
    }

    private function databases(array $databases): array { $valid=array(); foreach ($databases as $database) { if (!is_string($database)||preg_match('/^[A-Za-z0-9_]+$/D',$database)!==1) { throw new \InvalidArgumentException('invalid_database_name'); } $valid[$database]=true; } if ($valid===array()||count($valid)>100) { throw new \InvalidArgumentException('invalid_server_database_scope'); } $result=array_keys($valid); sort($result); return $result; }
    private function persist(string $category,array $record,array &$limits,array &$failures):void { try{$result=$this->storage->append($category,$record);if($result['limit_reached']){$limits[]=$category;}}catch(\Throwable $exception){$failures[]=$this->failure($category,$exception);} }
    private function persistHealth(array $limits,array &$failures,int $budgetOverruns):void { $conditions=array();if($limits!==array()){$conditions[]='daily_file_limit_reached';}if($failures!==array()){$conditions[]='partial_failure';}if($budgetOverruns>0){$conditions[]='performance_budget_exceeded';}$health=array('schema_version'=>3,'sample_type'=>'collector_health','scope'=>'server','collected_at'=>date('c'),'database'=>'server','healthy'=>false,'conditions'=>array_values(array_unique($conditions)),'categories'=>array_values(array_unique($limits)),'performance_budget_overruns'=>$budgetOverruns,'failures'=>$failures);try{$this->storage->append('status',$health);}catch(\Throwable $exception){$failures[]=$this->failure('health',$exception);} }
    private function due($last,float $now,int $frequency):bool{return !is_numeric($last)||$now-(float)$last>=$frequency;}
    private function collectionFailed(array $collection,string $section):bool{foreach($collection['failures']??array() as $failure){if(($failure['section']??'')===$section){return true;}}return false;}
    private function hasDigestHistory(array $counters):bool{return ($counters['items']??array())!==array()||($counters['disappeared_digests']??array())!==array()||!empty($counters['unavailable'])||!empty($counters['coverage_limited']);}
    private function now():float{return (float)($this->clock)();}
    private function failure(string $section,\Throwable $exception):array{return array('section'=>$section,'message'=>substr((string)preg_replace('/(?:password|passwd|pwd)\s*[=:]\s*\S+/i','credential=[hidden]',$exception->getMessage()),0,300));}
    private function rawErrors(array $records):array{$result=array();foreach($records as $record){$result[]=array_merge(array('number'=>$record['number'],'name'=>$record['name'],'sql_state'=>$record['sql_state'],'first_seen'=>$record['first_seen'],'last_seen'=>$record['last_seen']),$record['absolute']);}return $result;}
}
