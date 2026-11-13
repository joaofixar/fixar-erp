<?php
declare(strict_types=1);

/** Read-only query model for the operational Guides center. */
final class GnreGuideCenter
{
    public const STATUSES = ['RASCUNHO','AGUARDANDO','EMITIDA','REJEITADA','INCONCLUSIVA','RECEPCAO_REJEITADA'];

    public static function filters(array $source): array
    {
        $date = static function ($value): string {
            $value = trim((string)$value);
            $parts = explode('-', $value);
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && count($parts) === 3 && checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]) ? $value : '';
        };
        $status = strtoupper(trim((string)($source['status'] ?? '')));
        return [
            'status'=>in_array($status, self::STATUSES, true) ? $status : '',
            'uf'=>preg_match('/^[A-Za-z]{2}$/', (string)($source['uf'] ?? '')) ? strtoupper((string)$source['uf']) : '',
            'revenue'=>preg_match('/^\d{1,6}$/', trim((string)($source['revenue'] ?? ''))) ? trim((string)$source['revenue']) : '',
            'due_from'=>$date($source['due_from'] ?? ''), 'due_to'=>$date($source['due_to'] ?? ''),
            'created_date'=>$date($source['created_date'] ?? ''),
            'guide'=>filter_var($source['guide'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) ?: 0,
            'receipt'=>preg_match('/^\d{1,20}$/', trim((string)($source['receipt'] ?? ''))) ? trim((string)$source['receipt']) : '',
            'environment'=>in_array((string)($source['environment'] ?? ''), ['1','2'], true) ? (string)$source['environment'] : '',
            'include_archived'=>(string)($source['include_archived'] ?? '')==='1',
        ];
    }

    /**
     * Operational list projection only: legacy header wins; otherwise use the
     * first persisted item and disclose additional distinct item revenues.
     *
     * @param array<string,mixed> $guide
     * @param array<int,array<string,mixed>> $items persisted item facts
     */
    public static function projectedRevenue(array $guide, array $items): string
    {
        $header = trim((string)($guide['GNRE001_Codigo_Receita'] ?? ''));
        if ($header !== '') return $header;
        usort($items, static function (array $left, array $right): int {
            return ((int)($left['GNRE003_Id'] ?? 0)) <=> ((int)($right['GNRE003_Id'] ?? 0));
        });
        $revenues = [];
        foreach ($items as $item) {
            $revenue = trim((string)($item['GNRE003_Codigo_Receita'] ?? ''));
            if ($revenue !== '' && !isset($revenues[$revenue])) $revenues[$revenue] = true;
        }
        if (!$revenues) return '—';
        $codes = array_keys($revenues);
        return $codes[0].(count($codes) > 1 ? ' · +'.(count($codes) - 1).' receitas' : '');
    }

    /** @param array<string,mixed> $guide @param array<string,mixed> $facts */
    public static function classify(array $guide, array $facts = []): array
    {
        $codes = [];
        $result = $facts['result'] ?? null;
        if (is_array($result) && !empty($result['valid'])) foreach ((array)($result['guides'] ?? []) as $row) {
            $code = trim((string)($row['code'] ?? '')); if ($code !== '') $codes[] = $code;
        }
        $attempts = (array)($facts['attempts'] ?? []);
        $hasAttempt = static function (string $state) use ($attempts): bool { foreach ($attempts as $a) if ((string)($a['GNRE004_Estado'] ?? '') === $state) return true; return false; };
        $status = (string)($guide['GNRE001_Status'] ?? '');
        if (in_array('0', $codes, true)) return ['key'=>'EMITIDA','label'=>'Emitida','action'=>'Abrir','action_tab'=>'dados'];
        /* A reabertura Task 23 conserva uma única rejeição 1/2 como
           auditoria, mas o rascunho atual não pode herdá-la. Qualquer
           resultado múltiplo permanece conservadoramente bloqueado abaixo. */
        if ($status === 'R' && count($codes)===1 && in_array($codes[0], ['1','2'], true)) return ['key'=>'RASCUNHO','label'=>'Rascunho','action'=>'Abrir','action_tab'=>'dados'];
        if (count($codes)>1 && (bool)array_intersect($codes, ['1','2'])) return ['key'=>'INCONCLUSIVA','label'=>'Inconclusiva','action'=>'Abrir','action_tab'=>'historico'];
        if ((bool)array_intersect($codes, ['1','2'])) return ['key'=>'REJEITADA','label'=>'Rejeitada','action'=>'Ver resultado','action_tab'=>'processar'];
        if (in_array('3', $codes, true) || $status === 'I' || $hasAttempt('INCONCLUSIVA')) return ['key'=>'INCONCLUSIVA','label'=>'Inconclusiva','action'=>'Abrir','action_tab'=>'historico'];
        if ($status === 'P' || $hasAttempt('RECEBIDA')) return ['key'=>'AGUARDANDO','label'=>'Aguardando resultado','action'=>'Acompanhar','action_tab'=>'processar'];
        $hasReceipt = !empty($facts['queue']); foreach ($attempts as $attempt) if (trim((string)($attempt['GNRE004_Recibo'] ?? '')) !== '') { $hasReceipt=true; break; }
        if ($hasAttempt('REJEITADA') && !$hasReceipt) return ['key'=>'RECEPCAO_REJEITADA','label'=>'Recepção rejeitada','action'=>'Abrir','action_tab'=>'historico'];
        if ($status === 'R') return ['key'=>'RASCUNHO','label'=>'Rascunho','action'=>'Abrir','action_tab'=>'dados'];
        return ['key'=>'INCONCLUSIVA','label'=>'Inconclusiva','action'=>'Abrir','action_tab'=>'historico'];
    }

    /** Loads only records belonging to the active GNRE company. */
    public static function load(mysqli $db, int $companyId, array $filters, bool $archiveColumnAvailable): array
    {
        $where = ['g.GNRE001_C004_Id=?']; $types = 'i'; $values = [$companyId];
        if ($archiveColumnAvailable && empty($filters['include_archived'])) $where[]="g.GNRE001_Arquivada='N'";
        foreach ([['uf','g.GNRE001_UF_Favorecida=?','s'],['guide','g.GNRE001_Id=?','i'],['environment','s.GNRE007_Ambiente=?','s']] as $rule) if (!empty($filters[$rule[0]])) { $where[]=$rule[1]; $types.=$rule[2]; $values[]=$filters[$rule[0]]; }
        if ($filters['created_date'] !== '') { $where[]='DATE(g.GNRE001_Data_Hora_Criacao)=?'; $types.='s'; $values[]=$filters['created_date']; }
        if ($filters['due_from'] !== '' || $filters['due_to'] !== '') {
            $headerDue = ["g.GNRE001_Data_Vencimento<>'0000-00-00'"];
            $itemDue = ["di.GNRE003_Data_Vencimento<>'0000-00-00'"];
            $dueValues = [];
            if ($filters['due_from'] !== '') { $headerDue[]='g.GNRE001_Data_Vencimento>=?'; $dueValues[]=$filters['due_from']; }
            if ($filters['due_to'] !== '') { $headerDue[]='g.GNRE001_Data_Vencimento<=?'; $dueValues[]=$filters['due_to']; }
            $itemValues = [];
            if ($filters['due_from'] !== '') { $itemDue[]='di.GNRE003_Data_Vencimento>=?'; $itemValues[]=$filters['due_from']; }
            if ($filters['due_to'] !== '') { $itemDue[]='di.GNRE003_Data_Vencimento<=?'; $itemValues[]=$filters['due_to']; }
            $where[]='(('.implode(' AND ',$headerDue).') OR EXISTS (SELECT 1 FROM GNRE003 di WHERE di.GNRE003_GNRE001_Id=g.GNRE001_Id AND '.implode(' AND ',$itemDue).'))';
            $types.=str_repeat('s',count($dueValues)+count($itemValues));
            foreach (array_merge($dueValues,$itemValues) as $value) $values[]=$value;
        }
        if ($filters['revenue'] !== '') { $where[]='(g.GNRE001_Codigo_Receita=? OR EXISTS (SELECT 1 FROM GNRE003 ri WHERE ri.GNRE003_GNRE001_Id=g.GNRE001_Id AND ri.GNRE003_Codigo_Receita=?))'; $types.='ss'; $values[]=$filters['revenue']; $values[]=$filters['revenue']; }
        if ($filters['receipt'] !== '') { $where[]='EXISTS (SELECT 1 FROM GNRE004 fr WHERE fr.GNRE004_GNRE001_Id=g.GNRE001_Id AND fr.GNRE004_Recibo LIKE CONCAT(?, \'%\'))'; $types.='s'; $values[]=$filters['receipt']; }
        $sql='SELECT g.*,s.GNRE007_Ambiente FROM GNRE001 g LEFT JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id AND s.GNRE007_C004_Id=g.GNRE001_C004_Id WHERE '.implode(' AND ',$where).' ORDER BY COALESCE(g.GNRE001_Data_Hora_Alteracao,g.GNRE001_Data_Hora_Criacao) DESC,g.GNRE001_Id DESC';
        $statement=mysqli_prepare($db,$sql); self::bind($statement,$types,$values); mysqli_stmt_execute($statement); $result=mysqli_stmt_get_result($statement); $guides=[]; while($result&&($row=mysqli_fetch_assoc($result))) $guides[(int)$row['GNRE001_Id']]=$row; mysqli_stmt_close($statement);
        if (!$guides) return [];
        $ids=implode(',',array_map('intval',array_keys($guides)));
        $dueDates=[]; $itemRevenues=[]; $r=mysqli_query($db,"SELECT GNRE003_Id,GNRE003_GNRE001_Id,GNRE003_Codigo_Receita,GNRE003_Data_Vencimento FROM GNRE003 WHERE GNRE003_GNRE001_Id IN ($ids) ORDER BY GNRE003_GNRE001_Id ASC,GNRE003_Id ASC"); while($r&&($row=mysqli_fetch_assoc($r))){$id=(int)$row['GNRE003_GNRE001_Id'];$itemRevenues[$id][]=$row;$due=(string)$row['GNRE003_Data_Vencimento'];if($due!==''&&$due!=='0000-00-00'&&!isset($dueDates[$id][$due]))$dueDates[$id][$due]=true;}
        $attempts=[]; $r=mysqli_query($db,"SELECT * FROM GNRE004 WHERE GNRE004_GNRE001_Id IN ($ids) ORDER BY GNRE004_GNRE001_Id,GNRE004_Sequencia DESC"); while($r&&($row=mysqli_fetch_assoc($r))) $attempts[(int)$row['GNRE004_GNRE001_Id']][]=$row;
        $queues=[]; $r=mysqli_query($db,"SELECT q.*,t.GNRE004_GNRE001_Id FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id IN ($ids) AND t.GNRE004_Estado='RECEBIDA' AND t.GNRE004_Recibo=q.GNRE005_Recibo ORDER BY q.GNRE005_Id DESC"); while($r&&($row=mysqli_fetch_assoc($r))){$id=(int)$row['GNRE004_GNRE001_Id'];if(!isset($queues[$id]))$queues[$id]=$row;}
        $pdf=[]; $r=mysqli_query($db,"SELECT GNRE008_Id,GNRE008_GNRE001_Id FROM GNRE008 WHERE GNRE008_C004_Id=".(int)$companyId." AND GNRE008_GNRE001_Id IN ($ids) AND GNRE008_Tipo='PDF_OFICIAL' AND GNRE008_Origem='PORTAL_GNRE' ORDER BY GNRE008_Id DESC"); while($r&&($row=mysqli_fetch_assoc($r))){$id=(int)$row['GNRE008_GNRE001_Id'];if(!isset($pdf[$id]))$pdf[$id]=(int)$row['GNRE008_Id'];}
        $batch=[];$r=mysqli_query($db,"SELECT p.GNRE011_GNRE001_Id,p.GNRE011_Situacao,p.GNRE011_Situacao_Guia FROM GNRE011 p JOIN GNRE010 c ON c.GNRE010_Id=p.GNRE011_GNRE010_Id WHERE p.GNRE011_GNRE001_Id IN ($ids) ORDER BY p.GNRE011_Id DESC");while($r&&($row=mysqli_fetch_assoc($r))&&!isset($batch[(int)$row['GNRE011_GNRE001_Id']]))$batch[(int)$row['GNRE011_GNRE001_Id']]=$row;
        $out=[]; foreach($guides as $id=>$guide){$queue=$queues[$id]??null;$parsed=$queue?GnreResultInterpreter::interpret((string)($queue['GNRE005_Ultima_Consulta_SOAP']??'')):null;$batchFact=$batch[$id]??[];$state=((string)($batchFact['GNRE011_Situacao']??'')==='FINALIZADO'&&(string)($batchFact['GNRE011_Situacao_Guia']??'')==='0')?['key'=>'EMITIDA','label'=>'Emitida por lote real','action'=>'Abrir','action_tab'=>'dados']:self::classify($guide,['attempts'=>$attempts[$id]??[],'queue'=>$queue,'result'=>$parsed]);if($filters['status']!==''&&$state['key']!==$filters['status'])continue;$guide['_attempts']=$attempts[$id]??[];$guide['_queue']=$queue;$guide['_result']=$parsed;$guide['_batch_participation']=$batchFact;$guide['_state']=$state;$guide['_pdf_id']=$pdf[$id]??0;$guide['_item_due_dates']=array_keys($dueDates[$id]??[]);$guide['_item_revenues']=$itemRevenues[$id]??[];$guide['_projected_revenue']=self::projectedRevenue($guide,$guide['_item_revenues']);$out[]=$guide;} return $out;
    }
    private static function bind(mysqli_stmt $statement,string $types,array $values):void { $args=[$statement,$types]; foreach($values as $key=>$value)$args[]=&$values[$key]; call_user_func_array('mysqli_stmt_bind_param',$args); }
}
