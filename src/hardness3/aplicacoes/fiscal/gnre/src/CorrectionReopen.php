<?php
declare(strict_types=1);

/** Reopens the same guide only after a persisted final individual rejection. */
final class GnreCorrectionReopen
{
    /** The newest attempt must itself have one unequivocal final rejection. */
    public static function decisionFromHistory(array $guide, array $attempts, array $queues): array
    {
        if (($guide['GNRE001_Status'] ?? '') === 'R') return ['outcome'=>'already_open'];
        if (($guide['GNRE001_Status'] ?? '') !== 'F' || !$attempts) return ['outcome'=>'blocked'];
        usort($attempts, static function(array $a, array $b): int {
            return [(int)($a['GNRE004_Sequencia'] ?? 0),(int)($a['GNRE004_Id'] ?? 0)] <=> [(int)($b['GNRE004_Sequencia'] ?? 0),(int)($b['GNRE004_Id'] ?? 0)];
        });
        $latest=end($attempts); $attemptId=(int)($latest['GNRE004_Id'] ?? 0); $receipt=trim((string)($latest['GNRE004_Recibo'] ?? ''));
        if (!$attemptId || (string)($latest['GNRE004_Estado'] ?? '') !== 'RECEBIDA' || $receipt==='') return ['outcome'=>'blocked'];
        $matching=[];
        foreach ($queues as $queue) if ((int)($queue['GNRE005_GNRE004_Id'] ?? 0)===$attemptId && trim((string)($queue['GNRE005_Recibo'] ?? ''))===$receipt) $matching[]=$queue;
        /* Pending, processing, invalid and duplicate rows are ambiguous. */
        if (count($matching)!==1 || (string)($matching[0]['GNRE005_Situacao'] ?? '')!=='FINALIZADA') return ['outcome'=>'blocked'];
        return self::eligible($guide, GnreResultInterpreter::interpret((string)($matching[0]['GNRE005_Ultima_Consulta_SOAP'] ?? '')))
            ? ['outcome'=>'reopened'] : ['outcome'=>'blocked'];
    }

    public static function eligible(array $guide, ?array $result): bool
    {
        if (($guide['GNRE001_Status'] ?? '') !== 'F' || !is_array($result) || empty($result['valid'])) return false;
        $individuals=(array)($result['guides'] ?? []);
        if(count($individuals)!==1) return false;
        return in_array((string)($individuals[0]['code'] ?? ''),['1','2'],true);
    }

    /** @return array{outcome:'reopened'|'already_open'|'blocked'} */
    public static function decision(array $guide, ?array $result): array
    {
        if (($guide['GNRE001_Status'] ?? '') === 'R') return ['outcome'=>'already_open'];
        return self::eligible($guide,$result) ? ['outcome'=>'reopened'] : ['outcome'=>'blocked'];
    }

    /** @return array{outcome:string} */
    public static function reopen(mysqli $db, int $guideId, array $context): array
    {
        if (!mysqli_begin_transaction($db)) throw new RuntimeException('Não foi possível iniciar a reabertura da guia.');
        try {
            $q=mysqli_prepare($db,'SELECT * FROM GNRE001 WHERE GNRE001_Id=? AND GNRE001_C004_Id=? FOR UPDATE');
            mysqli_stmt_bind_param($q,'ii',$guideId,$context['company_id']); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $guide=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($q);
            if(!$guide) throw new RuntimeException('Guia não encontrada para a empresa GNRE selecionada.');
            $q=mysqli_prepare($db,'SELECT GNRE004_Id,GNRE004_Sequencia,GNRE004_Estado,GNRE004_Recibo FROM GNRE004 WHERE GNRE004_GNRE001_Id=? ORDER BY GNRE004_Sequencia ASC,GNRE004_Id ASC FOR UPDATE');
            mysqli_stmt_bind_param($q,'i',$guideId); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $attempts=[]; while($r&&($row=mysqli_fetch_assoc($r)))$attempts[]=$row; mysqli_stmt_close($q);
            $q=mysqli_prepare($db,'SELECT q.* FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id=? ORDER BY q.GNRE005_Id ASC FOR UPDATE');
            mysqli_stmt_bind_param($q,'i',$guideId); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $queues=[]; while($r&&($row=mysqli_fetch_assoc($r)))$queues[]=$row; mysqli_stmt_close($q);
            $decision=self::decisionFromHistory($guide,$attempts,$queues);
            if($decision['outcome']==='already_open'){mysqli_commit($db);return $decision;}
            if($decision['outcome']!=='reopened') throw new RuntimeException('Esta guia não possui rejeição individual final elegível para correção.');
            $q=mysqli_prepare($db,"UPDATE GNRE001 SET GNRE001_Status='R',GNRE001_Data_Hora_Alteracao=NOW(),GNRE001_C007_Id_Alteracao=? WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status='F'");
            mysqli_stmt_bind_param($q,'iii',$context['user_id'],$guideId,$context['company_id']); $ok=mysqli_stmt_execute($q); $changed=mysqli_stmt_affected_rows($q); mysqli_stmt_close($q);
            if(!$ok||$changed!==1) throw new RuntimeException('Não foi possível reabrir a guia rejeitada.');
            mysqli_commit($db); return $decision;
        } catch(Throwable $e) { mysqli_rollback($db); throw $e; }
    }
}
