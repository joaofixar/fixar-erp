<?php
declare(strict_types=1);

/** Read model for Task 63. It must not depend on locally generated XML. */
final class GnreBatchTransmissionCandidates
{
    public static function unexpectedFailureMessage(Throwable $error): string
    {
        /* Exception details may contain transport, certificate or infrastructure data. */
        return 'Não foi possível processar esta guia agora. Consulte o Histórico e tente novamente quando apropriado.';
    }

    public static function dueLabel(string $headerDueDate, string $itemDueDatesCsv): string
    {
        $headerDueDate=trim($headerDueDate);
        if ($headerDueDate !== '' && $headerDueDate !== '0000-00-00') return self::formatDate($headerDueDate);
        $dates=[]; foreach(explode(',', $itemDueDatesCsv) as $date) { $date=trim($date); if($date!==''&&$date!=='0000-00-00')$dates[$date]=true; }
        if (count($dates) === 1) return self::formatDate((string)array_key_first($dates));
        return count($dates) > 1 ? 'Múltiplos vencimentos' : 'Não informado';
    }

    /** @param array<string,mixed> $guide @param array<string,mixed>|null $snapshot */
    public static function eligible(array $guide, ?array $snapshot, int $companyId, bool $archiveColumnAvailable, int $attemptCount = 0, int $queueCount = 0, int $preparedCohortCount = 0): bool
    {
        if ((int)($guide['GNRE001_C004_Id'] ?? 0) !== $companyId || (string)($guide['GNRE001_Status'] ?? '') !== 'R') return false;
        if ($archiveColumnAvailable && (string)($guide['GNRE001_Arquivada'] ?? 'N') !== 'N') return false;
        if ($attemptCount !== 0 || $queueCount !== 0 || $preparedCohortCount !== 0 || !$snapshot) return false;
        return (int)($snapshot['GNRE007_C004_Id'] ?? 0) === $companyId
            && (int)($snapshot['GNRE007_Id'] ?? 0) === (int)($guide['GNRE001_GNRE007_Id'] ?? 0)
            && (string)($snapshot['GNRE007_UF'] ?? '') === (string)($guide['GNRE001_UF_Favorecida'] ?? '')
            && in_array((string)($snapshot['GNRE007_Ambiente'] ?? ''), ['1','2'], true);
    }

    /** @return array<int,array<string,mixed>> */
    public static function load(mysqli $db, int $companyId, bool $archiveColumnAvailable, array $filters = []): array
    {
        /* A reopened draft with reception rejection and no receipt is visible
           for correction only. It is never a selectable transmission item. */
        /* A resposta persistida pode usar namespace, atributos ou espaços no
           elemento. Aceitar apenas 1/2 mantém a reabertura restrita a uma
           rejeição final já comprovada, sem confundir recibo pendente. */
        $finalRejection="EXISTS (SELECT 1 FROM GNRE005 fq JOIN GNRE004 ft ON ft.GNRE004_Id=fq.GNRE005_GNRE004_Id WHERE ft.GNRE004_GNRE001_Id=g.GNRE001_Id AND fq.GNRE005_Situacao='FINALIZADA' AND fq.GNRE005_Ultima_Consulta_SOAP REGEXP '<([[:alnum:]_.-]+:)?situacaoGuia([[:space:]][^>]*)?>[[:space:]]*[12][[:space:]]*</([[:alnum:]_.-]+:)?situacaoGuia[[:space:]]*>')";
        $reopenedFinalHistory="EXISTS (SELECT 1 FROM GNRE005 rh JOIN GNRE004 rt ON rt.GNRE004_Id=rh.GNRE005_GNRE004_Id WHERE rt.GNRE004_GNRE001_Id=g.GNRE001_Id AND rh.GNRE005_Situacao='FINALIZADA')";
        $rejectedReception="(EXISTS (SELECT 1 FROM GNRE004 r WHERE r.GNRE004_GNRE001_Id=g.GNRE001_Id AND r.GNRE004_Estado='REJEITADA' AND COALESCE(r.GNRE004_Recibo,'')='') AND NOT EXISTS (SELECT 1 FROM GNRE004 o WHERE o.GNRE004_GNRE001_Id=g.GNRE001_Id AND (o.GNRE004_Estado<>'REJEITADA' OR COALESCE(o.GNRE004_Recibo,'')<>'')))";
        $where=["g.GNRE001_C004_Id=?","g.GNRE001_Status IN ('R','F')","s.GNRE007_C004_Id=?","s.GNRE007_UF=g.GNRE001_UF_Favorecida","s.GNRE007_Ambiente IN ('1','2')","((g.GNRE001_Status='R' AND (NOT EXISTS (SELECT 1 FROM GNRE004 t WHERE t.GNRE004_GNRE001_Id=g.GNRE001_Id) OR $rejectedReception OR $reopenedFinalHistory)) OR (g.GNRE001_Status='F' AND $finalRejection))","(g.GNRE001_Status='F' OR $reopenedFinalHistory OR NOT EXISTS (SELECT 1 FROM GNRE005 q JOIN GNRE004 qt ON qt.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE qt.GNRE004_GNRE001_Id=g.GNRE001_Id))","NOT EXISTS (SELECT 1 FROM GNRE011 cp JOIN GNRE010 cc ON cc.GNRE010_Id=cp.GNRE011_GNRE010_Id WHERE cp.GNRE011_GNRE001_Id=g.GNRE001_Id AND cp.GNRE011_Situacao IN ('PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','INCONCLUSIVO'))","NOT EXISTS (SELECT 1 FROM GNRE011 ap JOIN GNRE010 ac ON ac.GNRE010_Id=ap.GNRE011_GNRE010_Id WHERE ap.GNRE011_GNRE001_Id=g.GNRE001_Id AND ap.GNRE011_Situacao='FINALIZADO' AND ap.GNRE011_Situacao_Guia='0')"];
        $types='ii'; $values=[$companyId,$companyId];
        if ($archiveColumnAvailable) $where[]="g.GNRE001_Arquivada='N'";
        foreach ([['uf','g.GNRE001_UF_Favorecida=?','s'],['guide','g.GNRE001_Id=?','i'],['environment','s.GNRE007_Ambiente=?','s']] as $rule) if (!empty($filters[$rule[0]])) { $where[]=$rule[1];$types.=$rule[2];$values[]=$filters[$rule[0]]; }
        if (!empty($filters['created_date'])) { $where[]='DATE(g.GNRE001_Data_Hora_Criacao)=?';$types.='s';$values[]=$filters['created_date']; }
        if (!empty($filters['revenue'])) { $where[]='(g.GNRE001_Codigo_Receita=? OR EXISTS (SELECT 1 FROM GNRE003 ri WHERE ri.GNRE003_GNRE001_Id=g.GNRE001_Id AND ri.GNRE003_Codigo_Receita=?))';$types.='ss';$values[]=$filters['revenue'];$values[]=$filters['revenue']; }
        if (!empty($filters['due_from']) || !empty($filters['due_to'])) { $header=["g.GNRE001_Data_Vencimento<>'0000-00-00'"];$item=["di.GNRE003_Data_Vencimento<>'0000-00-00'"];$dates=[];foreach(['due_from'=>'>=','due_to'=>'<='] as $name=>$operator)if(!empty($filters[$name])){$header[]='g.GNRE001_Data_Vencimento'.$operator.'?';$item[]='di.GNRE003_Data_Vencimento'.$operator.'?';$dates[]=$filters[$name];}$where[]='(('.implode(' AND ',$header).') OR EXISTS (SELECT 1 FROM GNRE003 di WHERE di.GNRE003_GNRE001_Id=g.GNRE001_Id AND '.implode(' AND ',$item).'))';$types.=str_repeat('s',count($dates)*2);foreach($dates as $date)$values[]=$date;foreach($dates as $date)$values[]=$date; }
        /* Keep the main candidate query compatible with the legacy MySQL used by the ERP. */
        $sql="SELECT g.*,s.GNRE007_Ambiente,s.GNRE007_C004_Id AS _snapshot_company,s.GNRE007_UF AS _snapshot_uf,EXISTS (SELECT 1 FROM GNRE004 r WHERE r.GNRE004_GNRE001_Id=g.GNRE001_Id AND r.GNRE004_Estado='REJEITADA' AND COALESCE(r.GNRE004_Recibo,'')='') AS _needs_correction,(g.GNRE001_Status='F' AND $finalRejection) AS _needs_reopen FROM GNRE001 g JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id WHERE ".implode(' AND ',$where).' ORDER BY COALESCE(g.GNRE001_Data_Hora_Alteracao,g.GNRE001_Data_Hora_Criacao) DESC,g.GNRE001_Id DESC';
        $statement=mysqli_prepare($db,$sql); if(!$statement) throw new RuntimeException('Não foi possível consultar os rascunhos disponíveis.'); self::bind($statement,$types,$values); mysqli_stmt_execute($statement); $result=mysqli_stmt_get_result($statement); $out=[];
        while($result&&($row=mysqli_fetch_assoc($result))) { $correction=(bool)$row['_needs_correction'];$reopen=(bool)$row['_needs_reopen']; $row['_retransmission_allowed']=$correction; $row['_attempts']=$correction?[['GNRE004_Estado'=>'REJEITADA']]:[]; $row['_queue']=null; $row['_state']=$reopen?['key'=>'REJEITADA_FINAL','label'=>'Rejeitada']:($correction?['key'=>'RECEPCAO_REJEITADA','label'=>'Recepção rejeitada']:['key'=>'RASCUNHO','label'=>'Rascunho']); $row['_projected_revenue']=trim((string)$row['GNRE001_Codigo_Receita']) ?: '—'; $out[(int)$row['GNRE001_Id']]=$row; }
        mysqli_stmt_close($statement);
        if (!$out) return [];
        /* MySQL legado: fetch item dates in one extra query, rather than a
           GROUP_CONCAT subquery in the main candidate statement. */
        $ids=implode(',',array_map('intval',array_keys($out)));
        $itemStatement=mysqli_prepare($db,'SELECT GNRE003_GNRE001_Id,GNRE003_Data_Vencimento FROM GNRE003 WHERE GNRE003_GNRE001_Id IN ('.$ids.") AND GNRE003_Data_Vencimento IS NOT NULL AND GNRE003_Data_Vencimento<>'0000-00-00' ORDER BY GNRE003_GNRE001_Id,GNRE003_Data_Vencimento");
        if(!$itemStatement) throw new RuntimeException('Não foi possível consultar os vencimentos disponíveis.');
        mysqli_stmt_execute($itemStatement); $itemResult=mysqli_stmt_get_result($itemStatement); $dates=[];
        while($itemResult&&($item=mysqli_fetch_assoc($itemResult))) { $id=(int)$item['GNRE003_GNRE001_Id'];$date=(string)$item['GNRE003_Data_Vencimento'];$dates[$id][$date]=true; }
        mysqli_stmt_close($itemStatement);
        foreach($out as $id=>&$row) $row['_item_due_dates_csv']=implode(',',array_keys($dates[$id]??[]));
        unset($row); return array_values($out);
    }
    private static function bind(mysqli_stmt $statement, string $types, array $values): void { $args=[$statement,$types];foreach($values as $key=>$value)$args[]=&$values[$key];call_user_func_array('mysqli_stmt_bind_param',$args); }
    private static function formatDate(string $value): string { $date=DateTime::createFromFormat('Y-m-d',$value);return $date instanceof DateTime?$date->format('d/m/Y'):$value; }
}
