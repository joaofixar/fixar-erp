<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/QueueWorker.php';

function q40(bool $ok,string $message):void { if(!$ok) throw new RuntimeException('Task 40 queue: '.$message); }
function q40job():array { return ['GNRE005_Id'=>71,'GNRE005_GNRE004_Id'=>41,'GNRE005_Recibo'=>'1234567890','GNRE004_GNRE001_Id'=>31,'company_id'=>21,'token'=>'lease-token']; }
function q40run(callable $snapshot, callable $consult, array &$stored,string $lockPath):array {
    $stored=['state'=>'PROCESSANDO','token'=>'lease-token','lease'=>'2099-01-01 00:00:00','attempts'=>0,'gnre004_created'=>0,'guide_status'=>'P'];
    $finish=function(array $job,array $result)use(&$stored):void {$stored['state']='PENDENTE';$stored['token']=null;$stored['lease']=null;$stored['attempts']++;$stored['error']=$result['error'];$stored['next']=$result['retry_after'];};
    $worker=new GnreQueueWorker($consult,$snapshot,$finish,$lockPath);
    $method=new ReflectionMethod(GnreQueueWorker::class,'process');$method->setAccessible(true);
    return $method->invoke($worker,q40job());
}

$lockPath=sys_get_temp_dir().'/gnre-task40-queue-'.getmypid().'.lock';
$called=0;$stored=[];$result=q40run(static function():string{return '2';},static function(int $company,string $receipt,string $environment)use(&$called):array {$called++;q40($company===21&&$receipt==='1234567890'&&$environment==='2','callback recebeu o ambiente imutável do snapshot.');return ['kind'=>'pending','http'=>0,'code'=>'','description'=>'','soap'=>'','error'=>'','retry_after'=>5];},$stored,$lockPath);
q40($called===1&&$result['kind']==='pending','Homologação deve chegar ao callback sob lock.');
q40($stored['state']==='PENDENTE'&&$stored['token']===null&&$stored['lease']===null&&$stored['gnre004_created']===0&&$stored['guide_status']==='P','finalização controlada limpa lease/token sem retransmitir.');

/* Produção é encaminhada tal como Homologação; ambiente ausente segue bloqueado sem I/O. */
$called=0;$stored=[];$result=q40run(static function():string{return '1';},static function(int $company,string $receipt,string $environment)use(&$called):array {$called++;q40($environment==='1','Produção deve chegar ao cliente pelo snapshot.');return ['kind'=>'pending','http'=>0,'code'=>'','description'=>'','soap'=>'','error'=>'','retry_after'=>5];},$stored,$lockPath);
q40($called===1&&$result['kind']==='pending','Produção não pode ser bloqueada pela fila.');

foreach ([static function():string{return '';},static function():string{throw new RuntimeException('snapshot incompatível');}] as $snapshot) {
    $called=0;$stored=[];$result=q40run($snapshot,static function()use(&$called):array {$called++;return ['kind'=>'final','http'=>0,'code'=>'','description'=>'','soap'=>'','error'=>'','retry_after'=>0];},$stored,$lockPath);
    q40($called===0&&$result['kind']==='retry','falha de snapshot não pode chamar externo nem finalizar emissão.');
    q40($stored['state']==='PENDENTE'&&$stored['token']===null&&$stored['lease']===null&&$stored['attempts']===1&&$stored['gnre004_created']===0&&$stored['guide_status']==='P','retry deve limpar lease/token e preservar tentativa/guia.');
}

/* The first failing process must release the global lock for another controlled process. */
$lock=@fopen($lockPath,'c');q40($lock!==false,'lock de teste disponível.');q40(flock($lock,LOCK_EX|LOCK_NB),'finally liberou o lock global após falha.');flock($lock,LOCK_UN);fclose($lock);@unlink($lockPath);
echo "Task 40 queue behavioral synthetic: OK\n";
