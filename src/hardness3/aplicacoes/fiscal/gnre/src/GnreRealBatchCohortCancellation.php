<?php
declare(strict_types=1);
require_once __DIR__.'/ResultInterpreter.php';
require_once __DIR__.'/GnreBatchCorrelationFingerprint.php';

/** Local lifecycle only. It never creates transport, XML or Portal side effects. */
final class GnreRealBatchCohortCancellation
{
    public const STATUS_PREPARED='PREPARADO';
    public const STATUS_CANCELLED='CANCELADO';
    /* Reserved for the future real-lot protocol; not operational in Task 67. */
    public const FUTURE_STATUS_SENDING='ENVIANDO';
    public const FUTURE_STATUS_RECEIVED='RECEBIDO';
    public const FUTURE_STATUS_COMPLETED='CONCLUIDO';

    /** @return array{cohort_id:int,status:string,cancelled:bool,idempotent:bool} */
    public function cancel(mysqli $db,int $companyId,int $cohortId): array
    {
        if($companyId<1||$cohortId<1)throw new RuntimeException('Coorte inválida.');
        mysqli_begin_transaction($db);
        try {
            self::activeCompany($db,$companyId);
            $cohort=self::lockCohort($db,$companyId,$cohortId);
            if(!$cohort)throw new RuntimeException('Coorte não encontrada para a empresa GNRE ativa.');
            $status=(string)$cohort['GNRE010_Situacao'];
            if($status===self::STATUS_CANCELLED){mysqli_commit($db);return ['cohort_id'=>$cohortId,'status'=>$status,'cancelled'=>false,'idempotent'=>true];}
            if($status!==self::STATUS_PREPARED)throw new RuntimeException('Somente coorte preparada pode ser cancelada.');
            self::lockParticipantsAndRejectEvidence($db,$cohortId);
            $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Situacao='CANCELADO' WHERE GNRE010_Id=? AND GNRE010_C004_Id=? AND GNRE010_Situacao='PREPARADO'");
            if(!$s)throw new RuntimeException('Não foi possível cancelar a coorte.');
            mysqli_stmt_bind_param($s,'ii',$cohortId,$companyId);$ok=mysqli_stmt_execute($s);$changed=mysqli_stmt_affected_rows($s);mysqli_stmt_close($s);
            if(!$ok||$changed!==1)throw new RuntimeException('A coorte não está mais disponível para cancelamento.');
            mysqli_commit($db);return ['cohort_id'=>$cohortId,'status'=>self::STATUS_CANCELLED,'cancelled'=>true,'idempotent'=>false];
        } catch(Throwable $e) { mysqli_rollback($db);throw $e; }
    }

    /** @return list<array<string,mixed>> Safe list projection: no frozen fragments or fingerprints. */
    public static function load(mysqli $db,int $companyId): array
    {
        /* Bases que só receberam as Tasks 66/67 não possuem os campos da
           consolidação 74. A tela de coortes continua somente-leitura nelas,
           em vez de produzir erro 500 ou tentar uma migração automática. */
        $receipt=self::hasColumn($db,'GNRE010','GNRE010_Recibo')?'c.GNRE010_Recibo':"'' AS GNRE010_Recibo";
        $next=self::hasColumn($db,'GNRE010','GNRE010_Proxima_Tentativa_Consulta')?'c.GNRE010_Proxima_Tentativa_Consulta':"NULL AS GNRE010_Proxima_Tentativa_Consulta";
        $consultationSoap=self::hasColumn($db,'GNRE010','GNRE010_Ultima_Consulta_SOAP')?'c.GNRE010_Ultima_Consulta_SOAP':"'' AS GNRE010_Ultima_Consulta_SOAP";
        $frozen=self::hasColumn($db,'GNRE011','GNRE011_Fragmento_Canonico')?'p.GNRE011_Fragmento_Canonico':"'' AS GNRE011_Fragmento_Canonico";
        $participantStatus=self::hasColumn($db,'GNRE011','GNRE011_Situacao')?'p.GNRE011_Situacao':"'' AS GNRE011_Situacao";
        $sql='SELECT c.GNRE010_Id,c.GNRE010_UF_Favorecida,c.GNRE010_Ambiente,c.GNRE010_Situacao,'.$receipt.','.$next.','.$consultationSoap.',c.GNRE010_Data_Hora_Criacao,p.GNRE011_GNRE001_Id,'.$participantStatus.','.$frozen.',g.GNRE001_Status FROM GNRE010 c LEFT JOIN GNRE011 p ON p.GNRE011_GNRE010_Id=c.GNRE010_Id LEFT JOIN GNRE001 g ON g.GNRE001_Id=p.GNRE011_GNRE001_Id AND g.GNRE001_C004_Id=c.GNRE010_C004_Id WHERE c.GNRE010_C004_Id=? ORDER BY c.GNRE010_Data_Hora_Criacao DESC,c.GNRE010_Id DESC,p.GNRE011_GNRE001_Id';
        $s=mysqli_prepare($db,$sql);
        if(!$s)throw new RuntimeException('Não foi possível consultar as coortes locais.');
        mysqli_stmt_bind_param($s,'i',$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$out=[];
        while($r&&($row=mysqli_fetch_assoc($r))){$id=(int)$row['GNRE010_Id'];if(!isset($out[$id]))$out[$id]=['id'=>$id,'uf'=>(string)$row['GNRE010_UF_Favorecida'],'environment'=>(string)$row['GNRE010_Ambiente'],'status'=>(string)$row['GNRE010_Situacao'],'receipt'=>(string)($row['GNRE010_Recibo']??''),'next_consultation_at'=>(string)($row['GNRE010_Proxima_Tentativa_Consulta']??''),'soap'=>(string)($row['GNRE010_Ultima_Consulta_SOAP']??''),'created_at'=>(string)$row['GNRE010_Data_Hora_Criacao'],'guides'=>[],'frozen'=>[]];if((int)($row['GNRE011_GNRE001_Id']??0)>0){$out[$id]['guides'][]=['id'=>(int)$row['GNRE011_GNRE001_Id'],'status'=>(string)($row['GNRE011_Situacao']??$row['GNRE001_Status']??'')];$out[$id]['frozen'][]=['id'=>(int)$row['GNRE011_GNRE001_Id'],'xml'=>(string)($row['GNRE011_Fragmento_Canonico']??'')];}}
        mysqli_stmt_close($s);foreach($out as &$lot){$audit=self::correlationAudit((string)$lot['soap'],(array)$lot['frozen']);$lot['diagnosis']=self::consultationDiagnosis((string)$lot['status'],(string)$lot['soap'],(array)$lot['frozen'],$audit);$lot['resume_allowed']=$audit['complete'];unset($lot['soap'],$lot['frozen']);}unset($lot);return array_values($out);
    }

    private static function hasColumn(mysqli $db,string $table,string $column): bool
    {
        $s=mysqli_prepare($db,'SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1');
        if(!$s)return false;mysqli_stmt_bind_param($s,'ss',$table,$column);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$found=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);return (bool)$found;
    }

    /** Public, non-XML-only reason for a persisted consultation that remains blocked. */
    private static function consultationDiagnosis(string $status,string $soap,array $frozen=[],array $audit=[]): string
    {
        if($status!=='INCONCLUSIVO')return '';
        if(trim($soap)==='')return 'O Portal não devolveu um SOAP de resultado confirmável nesta consulta.';
        $parsed=GnreResultInterpreter::interpret($soap);
        if(!$parsed['valid'])return 'O Portal devolveu uma resposta de consulta que não pôde ser validada.';
        $guides=(array)($parsed['guides']??[]);
        if(!$guides)return 'O Portal ainda não devolveu resultados individuais para o recibo.';
        $returned=self::returnedTdados($soap);$participants=[];foreach($frozen as $entry){$tdados=self::frozenTdados((string)($entry['xml']??''));if($tdados!==null)$participants[]=['id'=>(int)($entry['id']??0),'tdados'=>$tdados];}
        if(!$returned||!$participants)return 'O retorno possui resultado individual, mas não trouxe dados suficientes para correlacioná-lo com segurança às guias enviadas.';
        $decisions=[];foreach($returned as $tdados)$decisions[]=GnreBatchCorrelationFingerprint::correlate($participants,$tdados)['decision'];
        if(in_array('INSUFFICIENT_EVIDENCE',$decisions,true))return 'O retorno individual não contém todos os campos mínimos exigidos para uma correlação segura. Ausentes: '.implode(', ',self::missingCorrelationFields($returned[0])).'.';
        if(in_array('UNMATCHED',$decisions,true)){ $paths=[];foreach($participants as $participant)foreach($returned as $tdados)$paths=array_merge($paths,GnreBatchCorrelationFingerprint::returnedEvidenceDifferences((array)$participant['tdados'],$tdados));$paths=array_values(array_unique($paths));return 'Os dados retornados divergem do conteúdo congelado enviado. Campos divergentes: '.implode(', ',array_slice($paths,0,8)).'.'; }
        if(in_array('AMBIGUOUS',$decisions,true))return 'Os dados retornados correspondem a mais de uma guia; a associação permanece bloqueada por segurança.';
        return !empty($audit['summary'])?'Auditoria local: '.$audit['summary'].'.':'O retorno possui resultado individual, mas não pôde ser concluído com segurança.';
    }

    /** @return array{complete:bool,summary:string} */
    private static function correlationAudit(string $soap,array $frozen): array
    {
        $returned=self::returnedTdados($soap);$participants=[];foreach($frozen as $entry){$tdados=self::frozenTdados((string)($entry['xml']??''));if($tdados!==null)$participants[]=['id'=>(int)($entry['id']??0),'tdados'=>$tdados];}
        if(!$returned||!$participants||count($returned)!==count($participants))return ['complete'=>false,'summary'=>'quantidade de resultados incompatível'];
        $mapped=[];$labels=[];foreach($returned as $tdados){$result=GnreBatchCorrelationFingerprint::correlate($participants,$tdados);if($result['decision']!=='MATCHED'||count($result['participant_ids'])!==1)return ['complete'=>false,'summary'=>'correlação '.strtolower($result['decision'])];$id=(string)$result['participant_ids'][0];if(isset($mapped[$id]))return ['complete'=>false,'summary'=>'correlação AMBIGUOUS'];$mapped[$id]=true;$labels[]='#'.$id.' MATCHED';}
        return ['complete'=>count($mapped)===count($participants),'summary'=>implode(', ',$labels)];
    }

    private static function returnedTdados(string $soap): array
    {
        if(!class_exists('DOMDocument'))return [];$dom=new DOMDocument();if(!@$dom->loadXML($soap,LIBXML_NONET))return [];$xp=new DOMXPath($dom);$out=[];
        foreach($xp->query('//*[local-name()="TResultLote_GNRE"]/*[local-name()="resultado"]/*[local-name()="guia"]')?:[] as $guide)if($guide instanceof DOMElement){$data=self::xmlChildren($guide);unset($data['situacaoGuia'],$data['motivosRejeicao'],$data['linhaDigitavel'],$data['codigoBarras'],$data['qrcodePayload'],$data['numeroControle'],$data['numeroControleFecp']);$out[]=$data;}return $out;
    }
    private static function frozenTdados(string $xml): ?array
    {
        if(!class_exists('DOMDocument')||trim($xml)==='')return null;$dom=new DOMDocument();if(!@$dom->loadXML($xml,LIBXML_NONET))return null;$node=(new DOMXPath($dom))->query('//*[local-name()="TDadosGNRE"]')->item(0);return $node instanceof DOMElement?self::xmlChildren($node):null;
    }
    private static function xmlChildren(DOMElement $node): array
    {
        $out=[];foreach($node->childNodes as $child){if(!$child instanceof DOMElement)continue;$name=$child->localName;$value=self::hasElementChildren($child)?self::xmlChildren($child):trim((string)$child->textContent);if($child->hasAttributes()){if(!is_array($value))$value=['valor'=>$value];foreach($child->attributes as $attribute)if($attribute instanceof DOMAttr)$value[$attribute->localName]=$attribute->value;}if(isset($out[$name])){$out[$name]=is_array($out[$name])&&array_keys($out[$name])===range(0,count($out[$name])-1)?$out[$name]:[$out[$name]];$out[$name][]=$value;}else $out[$name]=$value;}return $out;
    }
    private static function hasElementChildren(DOMElement $node): bool {foreach($node->childNodes as $child)if($child instanceof DOMElement)return true;return false;}
    /** Field names only: no fiscal values or identities leave local storage. */
    private static function missingCorrelationFields(array $data): array
    {
        $missing=[];foreach(['ufFavorecida','tipoGnre'] as $field)if(trim((string)($data[$field]??''))==='')$missing[]=$field;
        $items=$data['itensGNRE']['item']??($data['itensGNRE']??[]);if(!is_array($items)||!$items){$missing[]='itensGNRE.item';return $missing;}
        $list=array_keys($items)===range(0,count($items)-1)?$items:[$items];foreach($list as $index=>$item)if(!is_array($item)||trim((string)($item['receita']??''))==='')$missing[]='itensGNRE.item['.$index.'].receita';
        return $missing?:['campos de negócio normalizados'];
    }

    private static function activeCompany(mysqli $db,int $companyId): void {$s=mysqli_prepare($db,"SELECT C004_Id FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo='S' FOR UPDATE");if(!$s)throw new RuntimeException('Não foi possível bloquear a empresa GNRE.');mysqli_stmt_bind_param($s,'i',$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_row($r):null;mysqli_stmt_close($s);if(!$row)throw new RuntimeException('Empresa GNRE ativa não encontrada.');}
    private static function lockCohort(mysqli $db,int $companyId,int $cohortId): ?array {$s=mysqli_prepare($db,'SELECT GNRE010_Id,GNRE010_Situacao FROM GNRE010 WHERE GNRE010_Id=? AND GNRE010_C004_Id=? FOR UPDATE');if(!$s)throw new RuntimeException('Não foi possível bloquear a coorte.');mysqli_stmt_bind_param($s,'ii',$cohortId,$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return $row?:null;}
    private static function lockParticipantsAndRejectEvidence(mysqli $db,int $cohortId): void
    {
        $s=mysqli_prepare($db,'SELECT GNRE011_GNRE001_Id FROM GNRE011 WHERE GNRE011_GNRE010_Id=? FOR UPDATE');if(!$s)throw new RuntimeException('Não foi possível bloquear as participantes da coorte.');mysqli_stmt_bind_param($s,'i',$cohortId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$ids=[];while($r&&($row=mysqli_fetch_assoc($r)))$ids[]=(int)$row['GNRE011_GNRE001_Id'];mysqli_stmt_close($s);
        if(!$ids)throw new RuntimeException('Coorte sem participantes não pode ser cancelada.');$in=implode(',',array_map('intval',$ids));
        foreach(["SELECT 1 FROM GNRE004 WHERE GNRE004_GNRE001_Id IN ($in) LIMIT 1","SELECT 1 FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id IN ($in) LIMIT 1","SELECT 1 FROM GNRE008 WHERE GNRE008_GNRE001_Id IN ($in) LIMIT 1"] as $sql){$r=mysqli_query($db,$sql);if(!$r)throw new RuntimeException('Não foi possível verificar evidências de transmissão.');$found=mysqli_fetch_row($r);mysqli_free_result($r);if($found)throw new RuntimeException('A coorte possui evidência de transmissão individual e não pode ser cancelada.');}
    }
}
