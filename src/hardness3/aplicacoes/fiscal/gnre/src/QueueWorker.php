<?php
declare(strict_types=1);
require_once __DIR__ . '/EnvironmentResolver.php';
require_once __DIR__ . '/GuideControlProjection.php';
require_once __DIR__ . '/PendingResultPolicy.php';
require_once __DIR__ . '/ResultInterpreter.php';

/** Fila GNRE: claim transacional, lease e serialização da consulta ao Portal. */
final class GnreQueueWorker
{
    private const GAP=5000000, LOCK_WAIT=5000000;
    /** @var callable|null */ private $consult;
    /** @var callable|null */ private $snapshotEnvironment;
    /** @var callable|null */ private $finishOverride;
    /** @var string */ private $lockPath;
    public function __construct(?callable $consult=null, ?callable $snapshotEnvironment=null, ?callable $finishOverride=null, ?string $lockPath=null) { $this->consult=$consult;$this->snapshotEnvironment=$snapshotEnvironment;$this->finishOverride=$finishOverride;$this->lockPath=$lockPath?:sys_get_temp_dir().'/gnre-resultado-lote.lock'; }
    public function run(int $limit): array { $n=0;$c=['pending'=>0,'final'=>0,'retry'=>0,'locked'=>0];while($n<max(1,min(100,$limit))&&($j=$this->claim(null,null))){$x=$this->process($j);$c[$x['kind']]++;$n++;}return ['processed'=>$n,'counts'=>$c]; }
    public function consultGuide(int $companyId,int $guideId): array { $claim=$this->claimGuide($companyId,$guideId);if(isset($claim['outcome']))return $claim;$x=$this->process($claim);return ['outcome'=>$x['kind'],'result'=>$x]; }
    private function process(array $job): array { $result=$this->call($job);$this->finish($job,$result);return $result; }
    /** The queue never falls back to Homologação: the guide's immutable snapshot is required. */
    private function callForSnapshotEnvironment(array $job): array {
        $environment=$this->snapshotEnvironment($job);
        $environment=GnreEnvironmentResolver::assertOperationalAllowed($environment);
        if ($this->consult) return call_user_func($this->consult,(int)$job['company_id'],(string)$job['GNRE005_Recibo'],$environment);
        return (new GnreResultClient())->consult((int)$job['company_id'],(string)$job['GNRE005_Recibo'],$environment);
    }
    private function snapshotEnvironment(array $job): string {
        if($this->snapshotEnvironment) return (string)call_user_func($this->snapshotEnvironment,$job);
        $db=gnre_connect(); if(!$db) throw new RuntimeException('Base ERP indisponível.');
        try {
            $sql='SELECT s.GNRE007_Ambiente FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id AND t.GNRE004_Id=? AND t.GNRE004_GNRE001_Id=? JOIN GNRE001 g ON g.GNRE001_Id=t.GNRE004_GNRE001_Id AND g.GNRE001_C004_Id=? JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id AND s.GNRE007_C004_Id=g.GNRE001_C004_Id AND s.GNRE007_UF=g.GNRE001_UF_Favorecida WHERE q.GNRE005_Id=? AND q.GNRE005_Recibo=? LIMIT 1';
            $s=mysqli_prepare($db,$sql); if(!$s) throw new RuntimeException('Não foi possível validar o snapshot da guia para consulta.');
            $guide=(int)$job['GNRE004_GNRE001_Id'];$company=(int)$job['company_id'];$queue=(int)$job['GNRE005_Id'];$receipt=(string)$job['GNRE005_Recibo'];$attempt=(int)$job['GNRE005_GNRE004_Id'];
            mysqli_stmt_bind_param($s,'iiiis',$attempt,$guide,$company,$queue,$receipt);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
        } finally { mysqli_close($db); }
        $environment=(string)($row['GNRE007_Ambiente']??'');
        if(!in_array($environment,['1','2'],true)) throw new RuntimeException('A guia não possui snapshot oficial compatível para consulta de resultado.');
        return $environment;
    }
    private function call(array $job): array {
        $f=@fopen($this->lockPath,'c'); if(!$f)return $this->retry('Lock global GNRE indisponível.');
        $end=microtime(true)+self::LOCK_WAIT/1000000;
        do {
            if(flock($f,LOCK_EX|LOCK_NB)) {
                $at=microtime(true);
                try { return $this->callForSnapshotEnvironment($job); }
                catch(Throwable $e) { return $this->retry($e->getMessage()); }
                finally { $wait=self::GAP-(int)((microtime(true)-$at)*1000000);if($wait>0)usleep($wait);flock($f,LOCK_UN);fclose($f); }
            }
            usleep(20000);
        } while(microtime(true)<$end);
        fclose($f);return $this->retry('Lock global GNRE ocupado; consulta reagendada.');
    }
    private function retry(string $error): array { return ['kind'=>'retry','http'=>0,'code'=>'','description'=>'','soap'=>'','error'=>$error,'retry_after'=>5]; }
    private function claimGuide(int $companyId,int $guideId): array
    {
        $d=gnre_connect();if(!$d)throw new RuntimeException('Base ERP indisponível.');
        try { mysqli_begin_transaction($d);
            $s=mysqli_prepare($d,'SELECT GNRE001_Status FROM GNRE001 WHERE GNRE001_Id=? AND GNRE001_C004_Id=? FOR UPDATE');mysqli_stmt_bind_param($s,'ii',$guideId,$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$guide=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
            if(!$guide){mysqli_commit($d);return ['outcome'=>'not_found'];}
            if($guide['GNRE001_Status']==='F'){mysqli_commit($d);return ['outcome'=>'final'];}
            if($guide['GNRE001_Status']!=='P'){mysqli_commit($d);return ['outcome'=>'blocked','state'=>$guide['GNRE001_Status']];}
            /* A ação manual precisa consultar a mesma fila mais recente exibida na guia. */
            $s=mysqli_prepare($d,"SELECT q.GNRE005_Situacao,q.GNRE005_Proxima_Tentativa,q.GNRE005_Lock_Ate FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id=? AND t.GNRE004_Estado='RECEBIDA' AND t.GNRE004_Recibo=q.GNRE005_Recibo ORDER BY q.GNRE005_Id DESC LIMIT 1 FOR UPDATE");mysqli_stmt_bind_param($s,'i',$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$queue=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
            if(!$queue){mysqli_commit($d);return ['outcome'=>'no_receipt'];}
            if($queue['GNRE005_Situacao']==='FINALIZADA'){mysqli_commit($d);return ['outcome'=>'final'];}
            if($queue['GNRE005_Situacao']==='BLOQUEADA'){mysqli_commit($d);return ['outcome'=>'blocked'];}
            if($queue['GNRE005_Situacao']==='PROCESSANDO'&&strtotime((string)$queue['GNRE005_Lock_Ate'])>time()){mysqli_commit($d);return ['outcome'=>'processing'];}
            if($queue['GNRE005_Situacao']==='PENDENTE'&&strtotime((string)$queue['GNRE005_Proxima_Tentativa'])>time()){mysqli_commit($d);return ['outcome'=>'rescheduled','next'=>$queue['GNRE005_Proxima_Tentativa']];}
            mysqli_rollback($d);
        } catch(Throwable $e) { mysqli_rollback($d);throw $e; } finally { mysqli_close($d); }
        return ($job=$this->claim($companyId,$guideId))?$job:['outcome'=>'processing'];
    }
    private function claim(?int $companyId,?int $guideId): ?array
    {
        $d=gnre_connect();if(!$d)throw new RuntimeException('Base ERP indisponível.');$token=bin2hex(random_bytes(32));
        try { mysqli_begin_transaction($d);$where="(q.GNRE005_Situacao='PENDENTE' OR(q.GNRE005_Situacao='PROCESSANDO' AND q.GNRE005_Lock_Ate<NOW())) AND q.GNRE005_Proxima_Tentativa<=NOW() AND g.GNRE001_Status='P' AND t.GNRE004_Estado='RECEBIDA' AND t.GNRE004_Recibo=q.GNRE005_Recibo";if($companyId!==null)$where.=' AND g.GNRE001_C004_Id='.(int)$companyId.' AND g.GNRE001_Id='.(int)$guideId;$r=mysqli_query($d,"SELECT q.GNRE005_Id,q.GNRE005_GNRE004_Id,q.GNRE005_Recibo,t.GNRE004_GNRE001_Id,g.GNRE001_C004_Id company_id FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id JOIN GNRE001 g ON g.GNRE001_Id=t.GNRE004_GNRE001_Id WHERE $where ORDER BY q.GNRE005_Id DESC LIMIT 1 FOR UPDATE");$job=$r?mysqli_fetch_assoc($r):null;if(!$job){mysqli_commit($d);return null;}$s=mysqli_prepare($d,"UPDATE GNRE005 SET GNRE005_Situacao='PROCESSANDO',GNRE005_Lock_Token=?,GNRE005_Lock_Ate=DATE_ADD(NOW(),INTERVAL 300 SECOND),GNRE005_Tentativas=GNRE005_Tentativas+1 WHERE GNRE005_Id=?");mysqli_stmt_bind_param($s,'si',$token,$job['GNRE005_Id']);mysqli_stmt_execute($s);mysqli_stmt_close($s);mysqli_commit($d);$job['token']=$token;return $job;
        } catch(Throwable $e) {mysqli_rollback($d);throw $e;} finally {mysqli_close($d);}
    }
    /** Defesa em profundidade: nenhum callback/cliente pode finalizar sem uma guia conclusiva única. */
    private static function hasConclusiveIndividualResult(string $soap): bool
    {
        $parsed=GnreResultInterpreter::interpret($soap);$guides=(array)($parsed['guides']??[]);
        return !empty($parsed['valid'])&&count($guides)===1&&in_array((string)($guides[0]['code']??''),['0','1','2'],true);
    }
    private function finish(array $job,array $result): void
    {
        if($this->finishOverride){call_user_func($this->finishOverride,$job,$result);return;}
        $d=gnre_connect();if(!$d)throw new RuntimeException('Base ERP indisponível.');try{if(!mysqli_begin_transaction($d))throw new RuntimeException('Não foi possível iniciar a finalização da consulta GNRE.');$s=mysqli_prepare($d,"SELECT q.GNRE005_Tentativas,t.GNRE004_Tempo_Estimado_Processamento_MS FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE q.GNRE005_Id=? AND q.GNRE005_Situacao='PROCESSANDO' AND q.GNRE005_Lock_Token=? FOR UPDATE");mysqli_stmt_bind_param($s,'is',$job['GNRE005_Id'],$job['token']);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$owned=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);if(!$owned){mysqli_commit($d);return;}$final=$result['kind']==='final'&&self::hasConclusiveIndividualResult((string)($result['soap']??''));$state=$final?'FINALIZADA':'PENDENTE';$pending=($result['kind']??'')==='pending'||($result['kind']??'')==='final';$seconds=$pending?GnrePendingResultPolicy::nextDelaySeconds($owned['GNRE004_Tempo_Estimado_Processamento_MS']??null,(int)$owned['GNRE005_Tentativas']):($result['retry_after']?:60);$error=$pending?null:$result['error'];$s=mysqli_prepare($d,"UPDATE GNRE005 SET GNRE005_Situacao=?,GNRE005_Proxima_Tentativa=DATE_ADD(NOW(),INTERVAL ? SECOND),GNRE005_Lock_Token=NULL,GNRE005_Lock_Ate=NULL,GNRE005_Data_Hora_Processamento=NOW(),GNRE005_Erro_Tecnico=?,GNRE005_Ultima_Consulta_HTTP=NULLIF(?,0),GNRE005_Ultima_Consulta_Codigo=NULLIF(?,''),GNRE005_Ultima_Consulta_Descricao=NULLIF(?,''),GNRE005_Ultima_Consulta_SOAP=NULLIF(?,''),GNRE005_Data_Hora_Ultima_Consulta=IF(?=0,GNRE005_Data_Hora_Ultima_Consulta,NOW()) WHERE GNRE005_Id=? AND GNRE005_Situacao='PROCESSANDO' AND GNRE005_Lock_Token=?");mysqli_stmt_bind_param($s,'sisisssiis',$state,$seconds,$error,$result['http'],$result['code'],$result['description'],$result['soap'],$result['http'],$job['GNRE005_Id'],$job['token']);$ok=mysqli_stmt_execute($s);$changed=mysqli_stmt_affected_rows($s);mysqli_stmt_close($s);if(!$ok||$changed!==1)throw new RuntimeException('Não foi possível gravar o resultado da consulta GNRE.');if($final){$control=GnreGuideControlProjection::fromOfficialSoap((string)$result['soap']);$hasControlColumn=GnreGuideControlProjection::available($d);$sql=$control!==null&&$hasControlColumn?"UPDATE GNRE001 SET GNRE001_Status='F',GNRE001_Numero_Controle=COALESCE(NULLIF(GNRE001_Numero_Controle,''),?) WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status='P'":"UPDATE GNRE001 SET GNRE001_Status='F' WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status='P'";$s=mysqli_prepare($d,$sql);if($control!==null&&$hasControlColumn)mysqli_stmt_bind_param($s,'sii',$control,$job['GNRE004_GNRE001_Id'],$job['company_id']);else mysqli_stmt_bind_param($s,'ii',$job['GNRE004_GNRE001_Id'],$job['company_id']);$ok=mysqli_stmt_execute($s);$changed=mysqli_stmt_affected_rows($s);mysqli_stmt_close($s);if(!$ok||$changed!==1)throw new RuntimeException('Resultado final recebido, mas não foi possível marcar a guia como F.');}mysqli_commit($d);
        }catch(Throwable $e){mysqli_rollback($d);throw $e;}finally{mysqli_close($d);}
    }
}
