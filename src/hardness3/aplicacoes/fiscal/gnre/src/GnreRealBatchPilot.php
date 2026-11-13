<?php
declare(strict_types=1);
require_once __DIR__.'/GnreBatchCorrelationFingerprint.php';
require_once __DIR__.'/GnreRealBatchCohort.php';
require_once __DIR__.'/ReceptionClient.php';
require_once __DIR__.'/ResultClient.php';
require_once __DIR__.'/ResultInterpreter.php';

/**
 * Protocol for the experimental physical GNRE batch.  It intentionally never
 * creates GNRE004, GNRE005 or GNRE008: those records belong to the MVP queue.
 */
final class GnreRealBatchPilot
{
    public const HOMOLOGATION='2';
    private const ACTIVE=['PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','INCONCLUSIVO'];

    /** Schema gate: no automatic migration is ever attempted by the pilot. */
    public static function available(mysqli $db): bool
    {
        foreach(['GNRE010_Chave_Idempotencia','GNRE010_XML_Fisico','GNRE010_Recibo','GNRE010_Consulta_Lock_Token','GNRE010_Proxima_Tentativa_Consulta'] as $column)if(!self::column($db,'GNRE010',$column))return false;
        foreach(['GNRE011_Situacao','GNRE011_Situacao_Guia','GNRE011_Resultado_Individual'] as $column)if(!self::column($db,'GNRE011',$column))return false;
        return true;
    }

    /** @return array{cohort_id:int,status:string,reused:bool} */
    public function send(mysqli $db,int $companyId,int $userId,int $cohortId): array
    {
        mysqli_begin_transaction($db);
        try {
            if(!self::available($db))throw new RuntimeException('O schema mínimo da Task 74 ainda não está disponível para o piloto.');
            $lot=$this->lockLot($db,$companyId,$cohortId);
            if(!$lot) throw new RuntimeException('Lote piloto não encontrado para a empresa ativa.');
            if((string)$lot['GNRE010_Ambiente']!==self::HOMOLOGATION) throw new RuntimeException('O piloto de lote real está disponível somente em Homologação.');
            $status=(string)$lot['GNRE010_Situacao'];
            if($status!=='PREPARADO') { mysqli_commit($db); return ['cohort_id'=>$cohortId,'status'=>$status,'reused'=>true]; }
            $participants=$this->lockParticipants($db,$cohortId);
            if(!$participants) throw new RuntimeException('Lote piloto sem guias participantes.');
            $xml=$this->buildXml($participants);
            $key=self::physicalAttemptKey($companyId,$cohortId,array_column($participants,'GNRE011_GNRE001_Id'),$xml);
            $this->assertNoIndividualEvidence($db,array_column($participants,'GNRE011_GNRE001_Id'));
            $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Chave_Idempotencia=?,GNRE010_XML_SHA256=?,GNRE010_XML_Fisico=?,GNRE010_Data_Hora_Envio=NOW(),GNRE010_C007_Id_Solicitante_Envio=NULLIF(?,0),GNRE010_Situacao='ENVIANDO' WHERE GNRE010_Id=? AND GNRE010_C004_Id=? AND GNRE010_Situacao='PREPARADO'");
            if(!$s)throw new RuntimeException('Não foi possível preparar o envio do lote piloto.');$hash=hash('sha256',$xml);mysqli_stmt_bind_param($s,'sssiii',$key,$hash,$xml,$userId,$cohortId,$companyId);$ok=mysqli_stmt_execute($s);$changed=mysqli_stmt_affected_rows($s);$statementError=$ok?'':mysqli_stmt_errno($s).' '.mysqli_stmt_error($s);mysqli_stmt_close($s);
            /* An SQL/storage error leaves PREPARADO intact and must not be
               presented as a concurrency condition. No reception is attempted
               until this transition has been committed. */
            if(!$ok){error_log('GNRE batch local persistence failed lot='.(int)$cohortId.' mysql='.trim($statementError));throw new RuntimeException('Não foi possível registrar o XML físico do lote preparado.');}
            if($changed!==1)throw new RuntimeException('O lote piloto já está sendo processado.');
            mysqli_commit($db);
        } catch(Throwable $e){mysqli_rollback($db);throw $e;}
        try {$reply=(new GnreReceptionClient())->process(['company_id'=>$companyId],$xml,self::HOMOLOGATION);}
        catch(Throwable $e){$reply=['kind'=>'inconclusive','http'=>0,'code'=>'','description'=>'','receipt'=>'','receipt_at'=>'','estimated_ms'=>'','soap'=>'','error'=>'Falha sem confirmação de recibo.'];}
        mysqli_begin_transaction($db);
        try {
            $kind=(string)$reply['kind'];$state=$kind==='received'?'RECEBIDO':($kind==='rejected'?'REJEITADO':'INCONCLUSIVO');
            /* Rejection is a confirmed public response, not a transport error:
               retain its code and description with the lot. */
            $message=$kind==='rejected'?trim((string)($reply['code']??'').' '.(string)($reply['description']??'')):(string)($reply['error']??'');
            $next=$kind==='received'?$this->firstConsultationAt((string)($reply['receipt_at']??'')):'';
            $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_HTTP_Recepcao_Status=NULLIF(?,0),GNRE010_SOAP_Recepcao=NULLIF(?,''),GNRE010_Mensagem_Tecnica=NULLIF(?,''),GNRE010_Recibo=NULLIF(?,''),GNRE010_Data_Hora_Recibo=NULLIF(?,''),GNRE010_Proxima_Tentativa_Consulta=NULLIF(?,''),GNRE010_Situacao=? WHERE GNRE010_Id=? AND GNRE010_C004_Id=? AND GNRE010_Situacao='ENVIANDO'");
            mysqli_stmt_bind_param($s,'issssssii',$reply['http'],$reply['soap'],$message,$reply['receipt'],$reply['receipt_at'],$next,$state,$cohortId,$companyId);$ok=mysqli_stmt_execute($s);mysqli_stmt_close($s);if(!$ok)throw new RuntimeException('Não foi possível registrar a recepção do lote piloto.');
            if($kind==='received') mysqli_query($db,"UPDATE GNRE011 SET GNRE011_Situacao='PENDENTE' WHERE GNRE011_GNRE010_Id=".(int)$cohortId);
            if($kind==='rejected') mysqli_query($db,"UPDATE GNRE011 SET GNRE011_Situacao='REJEITADO',GNRE011_Data_Hora_Decisao=NOW() WHERE GNRE011_GNRE010_Id=".(int)$cohortId);
            mysqli_commit($db);return ['cohort_id'=>$cohortId,'status'=>$state,'reused'=>false];
        } catch(Throwable $e){
            mysqli_rollback($db);
            /* The reception request may already have been attempted.  Never
               leave an unprovable ENVIANDO state: without a persisted receipt,
               HTTP response or SOAP, make the block explicit and require
               operator review rather than risking an automatic retransmission. */
            $this->markInconclusiveWithoutEvidence($db,$companyId,$cohortId);
            return ['cohort_id'=>$cohortId,'status'=>'INCONCLUSIVO','reused'=>false];
        }
    }

    /** Repairs only an interrupted local persistence with no reception proof. */
    public function recoverUnconfirmedSending(mysqli $db,int $companyId,int $cohortId): array
    {
        mysqli_begin_transaction($db);
        try {
            $lot=$this->lockLot($db,$companyId,$cohortId);
            if(!$lot)throw new RuntimeException('Lote piloto não encontrado para a empresa ativa.');
            if((string)$lot['GNRE010_Situacao']==='ENVIANDO'&&trim((string)($lot['GNRE010_Recibo']??''))===''&&trim((string)($lot['GNRE010_SOAP_Recepcao']??''))==='')$this->markInconclusiveWithoutEvidence($db,$companyId,$cohortId);
            mysqli_commit($db);
            return ['cohort_id'=>$cohortId,'status'=>$this->status($db,$companyId,$cohortId)];
        } catch(Throwable $e){mysqli_rollback($db);throw $e;}
    }

    /** Repairs the legacy integer binding only when receipt and consultation are already persisted. */
    public function recoverEmptyConsultationStatus(mysqli $db,int $companyId,int $cohortId): array
    {
        mysqli_begin_transaction($db);
        try {
            $lot=$this->lockLot($db,$companyId,$cohortId);
            if(!$lot)throw new RuntimeException('Lote piloto não encontrado para a empresa ativa.');
            if(trim((string)($lot['GNRE010_Situacao']??''))===''&&trim((string)($lot['GNRE010_Recibo']??''))!==''&&trim((string)($lot['GNRE010_Ultima_Consulta_SOAP']??''))!==''){
                $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Situacao='PENDENTE' WHERE GNRE010_Id=? AND GNRE010_C004_Id=? AND (GNRE010_Situacao IS NULL OR GNRE010_Situacao='')");
                if(!$s)throw new RuntimeException('Não foi possível reparar o estado do lote.');mysqli_stmt_bind_param($s,'ii',$cohortId,$companyId);mysqli_stmt_execute($s);mysqli_stmt_close($s);
            }
            mysqli_commit($db);
            return ['cohort_id'=>$cohortId,'status'=>$this->status($db,$companyId,$cohortId)];
        } catch(Throwable $e){mysqli_rollback($db);throw $e;}
    }

    /** The UI invokes this automatically only after the persisted Portal due time. */
    public function consult(mysqli $db,int $companyId,int $cohortId,bool $resumeInconclusive=false): array
    {
        mysqli_begin_transaction($db);try {if(!self::available($db))throw new RuntimeException('O schema mínimo da Task 74 ainda não está disponível para o piloto.');$lot=$this->lockLot($db,$companyId,$cohortId);$states=['RECEBIDO','PENDENTE'];if($resumeInconclusive)$states[]='INCONCLUSIVO';if(!$lot||!in_array((string)$lot['GNRE010_Situacao'],$states,true)||(string)$lot['GNRE010_Recibo']==='')throw new RuntimeException('Este lote não possui recibo disponível para consulta.');$due=(string)($lot['GNRE010_Proxima_Tentativa_Consulta']??'');if(!$resumeInconclusive&&$due!==''&&strtotime($due)>time()){mysqli_commit($db);return ['cohort_id'=>$cohortId,'status'=>(string)$lot['GNRE010_Situacao'],'deferred'=>true,'next_at'=>$due];}$receipt=(string)$lot['GNRE010_Recibo'];mysqli_query($db,"UPDATE GNRE010 SET GNRE010_Consulta_Lock_Token=SHA2(CONCAT(UUID(),RAND()),256),GNRE010_Consulta_Lock_Ate=DATE_ADD(NOW(),INTERVAL 5 MINUTE) WHERE GNRE010_Id=".(int)$cohortId);mysqli_commit($db);}catch(Throwable $e){mysqli_rollback($db);throw $e;}
        try {$reply=(new GnreResultClient())->consultPhysicalBatch($companyId,$receipt,self::HOMOLOGATION);}catch(Throwable $e){$reply=['kind'=>'retry','http'=>0,'soap'=>'','error'=>'Falha sem resultado confirmado.'];}
        mysqli_begin_transaction($db);try {$this->persistConsultation($db,$companyId,$cohortId,$reply);mysqli_commit($db);return ['cohort_id'=>$cohortId,'status'=>$this->status($db,$companyId,$cohortId)];}catch(Throwable $e){mysqli_rollback($db);throw $e;}
    }

    /** Pure result decision: position, NF-e, receipt, control number and PDF are never inputs. */
    public static function decide(array $participants,array $returned): array
    {
        $out=[];foreach($participants as $p)$out[(int)$p['id']]=['decision'=>'INCONCLUSIVO','situacao'=>null,'reasons'=>[]];
        foreach($returned as $result){$correlation=self::correlateReturned($participants,is_array($result['tdados']??null)?$result['tdados']:null);if($correlation['decision']!=='MATCHED')continue;$id=(int)$correlation['participant_ids'][0];$code=(string)($result['situacaoGuia']??'');if($code==='0')$out[$id]=['decision'=>'FINALIZADO','situacao'=>$code,'reasons'=>[]];elseif(in_array($code,['1','2'],true))$out[$id]=['decision'=>'REJEITADO','situacao'=>$code,'reasons'=>(array)($result['motivos']??[])];elseif($code==='4')$out[$id]=['decision'=>'PENDENTE','situacao'=>$code,'reasons'=>[]];else $out[$id]=['decision'=>'INCONCLUSIVO','situacao'=>$code?:null,'reasons'=>[]];}return $out;
    }

    /**
     * A returned identificadorGuia is a direct correlation only when it is a
     * valid, unique participant id in this very lot.  A missing identifier
     * keeps the conservative business-evidence path; a malformed, unknown or
     * colliding one is never allowed to authorize a participant by fallback.
     *
     * @param list<array{id:int,tdados:array<string,mixed>}> $participants
     * @param array<string,mixed>|null $tdados
     * @return array{decision:string,participant_ids:list<string>}
     */
    public static function correlateReturned(array $participants,?array $tdados):array
    {
        $identifier=trim((string)($tdados['identificadorGuia']??''));
        if($identifier==='')return GnreBatchCorrelationFingerprint::correlate($participants,$tdados);
        if(!preg_match('/^[1-9][0-9]{0,9}$/',$identifier))return ['decision'=>'UNMATCHED','participant_ids'=>[]];
        $matches=[];foreach($participants as $participant)if((string)($participant['id']??'')===$identifier)$matches[]=$identifier;
        if(count($matches)===1)return ['decision'=>'MATCHED','participant_ids'=>$matches];
        return ['decision'=>count($matches)>1?'AMBIGUOUS':'UNMATCHED','participant_ids'=>[]];
    }

    /**
     * The database uniqueness key identifies one physical attempt, not merely
     * its payload. A reception-rejected lot may therefore be followed by a
     * new permitted lot with the same immutable participants and XML, while a
     * second caller of the same lot remains protected by its state transition.
     *
     * @param list<int|string> $participantIds
     */
    public static function physicalAttemptKey(int $companyId,int $lotId,array $participantIds,string $xml):string
    {
        if($companyId<1||$lotId<1)throw new InvalidArgumentException('Tentativa física inválida.');
        $ids=[];foreach($participantIds as $participantId){$id=(int)$participantId;if($id<1)throw new InvalidArgumentException('Participante inválida para a tentativa física.');$ids[]=$id;}
        if(!$ids)throw new InvalidArgumentException('Tentativa física sem participantes.');
        sort($ids,SORT_NUMERIC);
        return hash('sha256',"GNRE-PHYSICAL-ATTEMPT-v1\ncompany=".$companyId."\nlot=".$lotId."\nparticipants=".implode(',',$ids)."\nxml_sha256=".hash('sha256',$xml));
    }

    private function persistConsultation(mysqli $db,int $company,int $id,array $reply):void
    {
        $soap=(string)($reply['soap']??'');$kind=(string)($reply['kind']??'');
        /* A normal pending answer is evidence that the receipt is still being
           processed. It must remain consultable and must never unlock a resend. */
        $state=$kind==='final'?'PENDENTE':($kind==='pending'?'PENDENTE':'INCONCLUSIVO');
        $next=$kind==='pending'?$this->nextConsultationAt():'';
        $message=$kind==='retry'?'Não foi possível confirmar o resultado do lote; ele permanece bloqueado para novo envio.':'';
        $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Ultima_Consulta_SOAP=NULLIF(?,''),GNRE010_Data_Hora_Ultima_Consulta=NOW(),GNRE010_Consulta_Lock_Token=NULL,GNRE010_Consulta_Lock_Ate=NULL,GNRE010_Proxima_Tentativa_Consulta=NULLIF(?,''),GNRE010_Mensagem_Tecnica=NULLIF(?,''),GNRE010_Situacao=? WHERE GNRE010_Id=? AND GNRE010_C004_Id=?");
        mysqli_stmt_bind_param($s,'ssssii',$soap,$next,$message,$state,$id,$company);mysqli_stmt_execute($s);mysqli_stmt_close($s);
        if($kind!=='final')return;
        /* ResultInterpreter intentionally does not expose a matching key. Until
           TDadosGNRE is positively correlated, every participant stays blocked. */
        $parsed=GnreResultInterpreter::interpret($soap);if(!$parsed['valid'])return;
        $participants=$this->participantsForDecision($db,$id);$returned=$this->returnedWithTdados($soap,$parsed['guides']);$decisions=self::decide($participants,$returned);$allFinal=true;
        $hasPending=false;foreach($decisions as $participantId=>$decision){$state=$decision['decision']==='FINALIZADO'?'FINALIZADO':($decision['decision']==='REJEITADO'?'REJEITADO':($decision['decision']==='PENDENTE'?'PENDENTE':'INCONCLUSIVO'));if($state==='PENDENTE')$hasPending=true;if(!in_array($state,['FINALIZADO','REJEITADO'],true))$allFinal=false;$json=json_encode($decision['reasons'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$situacao=$decision['situacao'];$s=mysqli_prepare($db,'UPDATE GNRE011 SET GNRE011_Situacao=?,GNRE011_Situacao_Guia=?,GNRE011_Motivos_Rejeicao=?,GNRE011_Decisao_Correlacao=?,GNRE011_Data_Hora_Decisao=NOW() WHERE GNRE011_GNRE010_Id=? AND GNRE011_GNRE001_Id=?');$correlation=$state==='INCONCLUSIVO'?'INCONCLUSIVO':'MATCHED';mysqli_stmt_bind_param($s,'ssssii',$state,$situacao,$json,$correlation,$id,$participantId);mysqli_stmt_execute($s);mysqli_stmt_close($s);}
        $lotState=$allFinal?'FINALIZADO':($hasPending?'PENDENTE':'INCONCLUSIVO');$next=$hasPending?$this->nextConsultationAt():'';$s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Situacao=?,GNRE010_Proxima_Tentativa_Consulta=NULLIF(?, '') WHERE GNRE010_Id=? AND GNRE010_C004_Id=?");mysqli_stmt_bind_param($s,'ssii',$lotState,$next,$id,$company);mysqli_stmt_execute($s);mysqli_stmt_close($s);
    }
    private function returnedWithTdados(string $soap,array $basic):array
    {
        if(!class_exists('DOMDocument'))return array_map(static fn(array $r):array=>['situacaoGuia'=>$r['code']??'','motivos'=>$r['rejections']??[],'tdados'=>null],$basic);
        $dom=new DOMDocument();if(!@$dom->loadXML($soap,LIBXML_NONET))return [];$xp=new DOMXPath($dom);$nodes=$xp->query('//*[local-name()="TResultLote_GNRE"]/*[local-name()="resultado"]/*[local-name()="guia"]');$out=[];
        foreach($nodes?:[] as $node){if(!$node instanceof DOMElement)continue;$text=static function(string $query)use($xp,$node):string{return trim((string)$xp->evaluate('string('.$query.')',$node));};
            /* The official response puts TDadosGNRE-equivalent fields directly
               in resultado/guia (not inside a TDadosGNRE child).  Keep every
               business field for the immutable fingerprint and discard only
               outcome/presentation values produced after processing. */
            $tdados=self::xmlChildrenToArray($node);foreach(['situacaoGuia','motivosRejeicao','representacaoNumerica','codigoBarras','linhaDigitavel','qrcodePayload','nossoNumero','numeroControle','numeroControleFecp'] as $technical)unset($tdados[$technical]);
            $reasons=[];foreach($xp->query('./*[local-name()="motivosRejeicao"]/*[local-name()="motivo"]',$node)?:[] as $reason)$reasons[]=['code'=>trim((string)$xp->evaluate('string(./*[local-name()="codigo"])',$reason)),'description'=>trim((string)$xp->evaluate('string(./*[local-name()="descricao"])',$reason)),'field'=>trim((string)$xp->evaluate('string(./*[local-name()="campo"])',$reason))];$out[]=['situacaoGuia'=>$text('./*[local-name()="situacaoGuia"]'),'motivos'=>$reasons,'tdados'=>$tdados];}
        return $out;
    }
    /** Converts official TDadosGNRE XML without losing repeated tags or tipo attributes. */
    private static function xmlChildrenToArray(DOMElement $node):array {$out=[];foreach($node->childNodes as $child){if(!$child instanceof DOMElement)continue;$name=$child->localName;$value=self::hasElementChildren($child)?self::xmlChildrenToArray($child):trim((string)$child->textContent);if($child->hasAttributes()){if(!is_array($value))$value=['valor'=>$value];foreach($child->attributes as $attribute)if($attribute instanceof DOMAttr)$value[$attribute->localName]=$attribute->value;}if(isset($out[$name])){$out[$name]=is_array($out[$name])&&array_keys($out[$name])===range(0,count($out[$name])-1)?$out[$name]:[$out[$name]];$out[$name][]=$value;}else $out[$name]=$value;}return $out;}
    /** DOMElement::childElementCount is unavailable on the supported PHP 7 runtime. */
    private static function hasElementChildren(DOMElement $node):bool {foreach($node->childNodes as $child)if($child instanceof DOMElement)return true;return false;}
    private function participantsForDecision(mysqli $db,int $lot):array {$r=mysqli_query($db,'SELECT GNRE011_GNRE001_Id,GNRE011_Fragmento_Canonico FROM GNRE011 WHERE GNRE011_GNRE010_Id='.(int)$lot);$out=[];while($r&&($x=mysqli_fetch_assoc($r))){$frozen=(string)$x['GNRE011_Fragmento_Canonico'];$tdados=json_decode($frozen,true);if(!is_array($tdados)&&class_exists('DOMDocument')){$dom=new DOMDocument();if(@$dom->loadXML($frozen,LIBXML_NONET)){$node=(new DOMXPath($dom))->query('//*[local-name()="TDadosGNRE"]')->item(0);if($node instanceof DOMElement)$tdados=self::xmlChildrenToArray($node);}}$out[]=['id'=>(int)$x['GNRE011_GNRE001_Id'],'tdados'=>$tdados];}if($r)mysqli_free_result($r);return $out;}
    private function status(mysqli $db,int $company,int $id):string {$r=mysqli_query($db,'SELECT GNRE010_Situacao FROM GNRE010 WHERE GNRE010_Id='.(int)$id.' AND GNRE010_C004_Id='.(int)$company);$x=$r?mysqli_fetch_assoc($r):null;if($r)mysqli_free_result($r);return (string)($x['GNRE010_Situacao']??'INCONCLUSIVO');}
    /** Records only the absence of proof; it never replaces a receipt or SOAP. */
    private function markInconclusiveWithoutEvidence(mysqli $db,int $company,int $id):void
    {
        $message='Não foi possível confirmar a recepção do lote. Nenhuma retransmissão automática será realizada.';
        $s=mysqli_prepare($db,"UPDATE GNRE010 SET GNRE010_Situacao='INCONCLUSIVO',GNRE010_Mensagem_Tecnica=COALESCE(NULLIF(GNRE010_Mensagem_Tecnica,''),?) WHERE GNRE010_Id=? AND GNRE010_C004_Id=? AND GNRE010_Situacao='ENVIANDO' AND (GNRE010_Recibo IS NULL OR GNRE010_Recibo='') AND (GNRE010_SOAP_Recepcao IS NULL OR GNRE010_SOAP_Recepcao='')");
        if(!$s)return;mysqli_stmt_bind_param($s,'sii',$message,$id,$company);mysqli_stmt_execute($s);mysqli_stmt_close($s);
    }
    private function lockLot(mysqli $db,int $company,int $id):?array {$s=mysqli_prepare($db,'SELECT * FROM GNRE010 WHERE GNRE010_Id=? AND GNRE010_C004_Id=? FOR UPDATE');mysqli_stmt_bind_param($s,'ii',$id,$company);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$x=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return $x?:null;}
    private function lockParticipants(mysqli $db,int $lot):array {$r=mysqli_query($db,'SELECT p.*,g.* ,s.GNRE007_Regras_JSON FROM GNRE011 p JOIN GNRE001 g ON g.GNRE001_Id=p.GNRE011_GNRE001_Id JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id WHERE p.GNRE011_GNRE010_Id='.(int)$lot.' ORDER BY p.GNRE011_GNRE001_Id FOR UPDATE');$out=[];while($r&&($x=mysqli_fetch_assoc($r)))$out[]=$x;if($r)mysqli_free_result($r);return $out;}
    private function assertNoIndividualEvidence(mysqli $db,array $ids):void {$in=implode(',',array_map('intval',$ids));$r=mysqli_query($db,"SELECT 1 FROM GNRE008 WHERE GNRE008_GNRE001_Id IN ($in) LIMIT 1");$pdf=$r&&mysqli_fetch_row($r);if($r)mysqli_free_result($r);if($pdf)throw new RuntimeException('Uma guia possui PDF individual e não pode entrar no lote real.');/* GNRE004 is admitted only after the conservative preparation gate; a proven 1/2 must not be rejected merely because its immutable history exists. */}
    private function buildXml(array $participants):string {
        if(!class_exists('DOMDocument'))throw new RuntimeException('A extensão DOM é necessária para o lote piloto.');
        if(count($participants)>GnreRealBatchCohort::MAX_GUIDES_PER_PHYSICAL_LOT)throw new RuntimeException('O lote físico excede o limite operacional de 30 guias.');
        $dom=new DOMDocument('1.0','UTF-8');$dom->formatOutput=false;$root=$dom->createElementNS('http://www.gnre.pe.gov.br','TLote_GNRE');$root->setAttribute('versao','2.00');$dom->appendChild($root);$guides=$dom->createElementNS('http://www.gnre.pe.gov.br','guias');$root->appendChild($guides);
        foreach($participants as $p){$tdados=$this->frozenTdados((string)($p['GNRE011_Fragmento_Canonico']??''));$guides->appendChild($dom->importNode($tdados,true));}
        $xml=(string)$dom->saveXML();if(strlen($xml)>600*1024)throw new RuntimeException('O XML físico excede o limite de 600 KB.');
        $xsd=dirname(__DIR__).'/docs/referencias/gnre-v2.17/lote_gnre_v2.00.xsd';$old=libxml_use_internal_errors(true);$valid=@$dom->schemaValidate($xsd);$errors=libxml_get_errors();libxml_clear_errors();libxml_use_internal_errors($old);if(!$valid){$detail='';if($errors){$first=$errors[0];$detail=' '.trim((string)$first->message);}throw new RuntimeException('O XML físico não é válido no XSD oficial de lote.'.$detail);}
        return $xml;
    }
    private function firstConsultationAt(string $receiptAt):string {$timestamp=strtotime($receiptAt);return date('Y-m-d H:i:s',($timestamp===false?time():$timestamp)+30);}
    private function nextConsultationAt():string {return date('Y-m-d H:i:s',time()+30);}
    /** Accepts old frozen fragments that inherited their namespace from the individual lot root. */
    private function frozenTdados(string $frozen):DOMElement {$part=new DOMDocument();if(!@$part->loadXML($frozen,LIBXML_NONET))throw new RuntimeException('O conteúdo congelado de uma participante não está disponível.');$tdados=(new DOMXPath($part))->query('//*[local-name()="TDadosGNRE"]')->item(0);if(!$tdados instanceof DOMElement)throw new RuntimeException('O conteúdo congelado de uma participante não contém TDadosGNRE.');if($tdados->namespaceURI==='http://www.gnre.pe.gov.br')return $tdados;$wrapped=preg_replace('/^\s*<\?xml[^>]*\?>\s*/','',$frozen)??$frozen;$namespaced=new DOMDocument();if(!@$namespaced->loadXML('<wrapper xmlns="http://www.gnre.pe.gov.br">'.$wrapped.'</wrapper>',LIBXML_NONET))throw new RuntimeException('O conteúdo congelado de uma participante não está disponível.');$tdados=(new DOMXPath($namespaced))->query('//*[local-name()="TDadosGNRE"]')->item(0);if(!$tdados instanceof DOMElement||$tdados->namespaceURI!=='http://www.gnre.pe.gov.br')throw new RuntimeException('O conteúdo congelado de uma participante não contém TDadosGNRE.');return $tdados;}
    private static function column(mysqli $db,string $table,string $column):bool {$s=mysqli_prepare($db,'SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1');if(!$s)return false;mysqli_stmt_bind_param($s,'ss',$table,$column);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$found=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);return(bool)$found;}
}
