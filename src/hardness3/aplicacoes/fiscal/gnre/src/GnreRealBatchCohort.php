<?php
declare(strict_types=1);
require_once __DIR__.'/GnreBatchCorrelationFingerprint.php';
require_once __DIR__.'/GnreTdadosProjection.php';
require_once __DIR__.'/XmlGenerator.php';
require_once __DIR__.'/GnreRealBatchReentryPolicy.php';
require_once __DIR__.'/ResultInterpreter.php';
require_once __DIR__.'/CompanyEmitter.php';

final class GnreRealBatchCohortRollbackFailure extends RuntimeException {}

/** Transactional preparation only: never creates XML, receipt, queue or PDF. */
final class GnreRealBatchCohort
{
    public const FINGERPRINT_VERSION='GNRE-BATCH-CORRELATION-v1';
    public const LAYOUT_VERSION='2.00';
    public const STATUS_PREPARED='PREPARADO';
    public const STATUS_CANCELLED='CANCELADO';
    public const MAX_GUIDES_PER_PHYSICAL_LOT=30;
    public const MAX_PHYSICAL_XML_BYTES=614400;

    /** @param list<int|string> $guideIds @return array{cohort_id:int,reused:bool} */
    public function prepare(mysqli $db,int $companyId,array $guideIds):array
    {
        $ids=self::ids($guideIds);if($companyId<=0||!$ids)throw new RuntimeException('Selecione ao menos uma guia válida para a coorte.');
        mysqli_begin_transaction($db);
        try {
            self::activeCompany($db,$companyId);$guides=self::lockGuides($db,$ids);
            if(count($guides)!==count($ids))throw new RuntimeException('Uma ou mais guias não foram encontradas.');
            self::assertSelection($guides,$companyId);
            $existing=self::existing($db,$ids);if($existing!==null){mysqli_commit($db);return ['cohort_id'=>$existing,'reused'=>true];}
            self::noActiveParticipant($db,$ids);self::noIndividualHistory($db,$ids);
            $descriptions=[];foreach($guides as $guide){$projection=self::project($db,$guide);$d=self::describeParticipant($projection['tdados']);if(!$d['complete'])throw new RuntimeException('A guia não possui evidência suficiente para a coorte.');$descriptions[(int)$guide['GNRE001_Id']]=$d;}
            $cohort=self::insertCohort($db,$guides[0]);
            foreach($guides as $guide)self::insertParticipant($db,$cohort,(int)$guide['GNRE001_Id'],$descriptions[(int)$guide['GNRE001_Id']]);
            mysqli_commit($db);return ['cohort_id'=>$cohort,'reused'=>false];
        } catch(Throwable $e) { mysqli_rollback($db);throw $e; }
    }

    /**
     * Prepares every admissible selected guide, splitting compatible guides into
     * their immutable local cohorts.  This is deliberately local: it never
     * creates operational XML or any Portal side effect.
     *
     * @param list<int|string> $guideIds
     * @return array{cohorts:int,guides:int,cohort_ids:list<int>,already_prepared:list<int>,blocked:list<array{id:int,reason:string}>}
     */
    public function prepareAutomatically(mysqli $db,int $companyId,array $guideIds,bool $homologationOnly=false):array
    {
        $ids=self::ids($guideIds);
        if($companyId<=0||!$ids)throw new RuntimeException('Selecione ao menos uma guia para preparar o lote real.');
        $result=['cohorts'=>0,'guides'=>0,'cohort_ids'=>[],'already_prepared'=>[],'blocked'=>[]];
        mysqli_begin_transaction($db);
        try {
            self::activeCompany($db,$companyId);
            $rows=[];foreach(self::lockGuides($db,$ids) as $guide)$rows[(int)$guide['GNRE001_Id']]=$guide;
            $groups=[];
            foreach($ids as $id){
                $guide=$rows[$id]??null;
                if(!$guide){$result['blocked'][]=['id'=>$id,'reason'=>'Guia não encontrada para a empresa ativa.'];continue;}
                if((int)$guide['GNRE001_C004_Id']!==$companyId){$result['blocked'][]=['id'=>$id,'reason'=>'Guia não pertence à empresa ativa.'];continue;}
                if($homologationOnly&&(string)($guide['GNRE007_Ambiente']??'')!=='2'){$result['blocked'][]=['id'=>$id,'reason'=>'O piloto de lote real aceita somente Homologação.'];continue;}
                if(self::preparedMembership($db,$id)!==null){$result['already_prepared'][]=$id;continue;}
                try { self::assertSelection([$guide],$companyId); if((string)$guide['GNRE001_Status']!=='R'||!self::hasFreshDraft($db,$id))self::assertSafeReentry($db,$id); }
                catch(Throwable $e){$result['blocked'][]=['id'=>$id,'reason'=>self::publicReason($e)];continue;}
                try {$rules=json_decode((string)$guide['GNRE007_Regras_JSON'],true);if(!is_array($rules))throw new RuntimeException('O snapshot oficial da guia é inválido.');$itemCount=self::itemCount($db,$id);self::assertItemsPerGuide($itemCount,(int)($rules['max']['itensPorGuia']??0));$projection=self::project($db,$guide);$description=self::describeParticipant($projection['tdados']);$description['tdados_xml']=$projection['xml'];$description['physical_bytes']=strlen($projection['xml']);$official=(int)($rules['max']['guiasPorLote']??0);$description['guide_limit']=$official>0?min(self::MAX_GUIDES_PER_PHYSICAL_LOT,$official):self::MAX_GUIDES_PER_PHYSICAL_LOT;$description['item_count']=$itemCount;$officialItems=(int)($rules['max']['itensPorLote']??0);$description['item_limit']=$officialItems>0?$officialItems:PHP_INT_MAX;if((int)$description['physical_bytes']+512>self::MAX_PHYSICAL_XML_BYTES)throw new RuntimeException('O TDadosGNRE da guia excede o limite de 600 KB do lote físico.');/* O nó XML individual validado é a única fonte do lote. */}
                catch(Throwable $e){$result['blocked'][]=['id'=>$id,'reason'=>self::publicReason($e)];continue;}
                $key=implode('|',[(int)$guide['GNRE001_C004_Id'],(string)$guide['GNRE007_Ambiente'],self::LAYOUT_VERSION]);
                $groups[$key][]=['guide'=>$guide,'description'=>$description,'fresh'=>(string)$guide['GNRE001_Status']==='R'&&self::hasFreshDraft($db,$id)];
            }
            $groupNumber=0;foreach($groups as $entries)foreach(self::splitPhysicalLots($entries) as $entries){$groupNumber++;
                $guides=array_column($entries,'guide');$groupIds=array_map(static fn(array $entry):int=>(int)$entry['guide']['GNRE001_Id'],$entries);
                try {
                    $outcome=self::withGroupSavepoint($db,$groupNumber,function()use($db,$companyId,$guides,$groupIds,$entries):array {
                        /* Reuse the same cohort contract and its idempotency guards. */
                        self::assertSelection($guides,$companyId);
                        $existing=self::existing($db,$groupIds);
                        if($existing!==null)return ['existing'=>true,'cohort_id'=>$existing];
                        self::noActiveParticipant($db,$groupIds);
                        foreach($entries as $entry){
                            $guideId=(int)$entry['guide']['GNRE001_Id'];
                            if(!empty($entry['fresh']))self::noIndividualHistory($db,[$guideId]);
                            else { self::noPdf($db,[$guideId]); self::assertSafeReentry($db,$guideId); }
                        }
                        $cohort=self::insertCohort($db,$guides[0]);
                        foreach($entries as $entry)self::insertParticipant($db,$cohort,(int)$entry['guide']['GNRE001_Id'],$entry['description']);
                        return ['existing'=>false,'cohort_id'=>$cohort];
                    });
                    if($outcome['existing']){foreach($groupIds as $id)$result['already_prepared'][]=$id;continue;}
                    $result['cohorts']++;$result['guides']+=count($groupIds);$result['cohort_ids'][]=(int)$outcome['cohort_id'];
                } catch(Throwable $e) { if($e instanceof GnreRealBatchCohortRollbackFailure)throw $e;foreach($groupIds as $id)$result['blocked'][]=['id'=>$id,'reason'=>self::publicReason($e)]; }
            }
            $result['already_prepared']=array_values(array_unique($result['already_prepared']));
            $result['cohort_ids']=array_values(array_unique(array_filter(array_map('intval',$result['cohort_ids']))));
            mysqli_commit($db);return $result;
        } catch(Throwable $e) { mysqli_rollback($db);throw $e; }
    }

    /** Pure admission gate also exercised without a database by the test. */
    public static function assertSelection(array $guides,int $companyId):void
    {
        if(!$guides)throw new RuntimeException('A coorte não pode ficar vazia.');$base=null;
        foreach($guides as $g){
            if((int)($g['GNRE001_C004_Id']??0)!==$companyId)throw new RuntimeException('A coorte não pode misturar empresas.');
            if(!in_array((string)($g['GNRE001_Status']??''),['R','F'],true))throw new RuntimeException('A coorte aceita somente rascunho ou rejeição comprovada.');
            if((string)($g['GNRE001_Arquivada']??'N')!=='N')throw new RuntimeException('Guia arquivada não pode participar da coorte.');
            $snapshot=(int)($g['GNRE001_GNRE007_Id']??0);$uf=strtoupper(trim((string)($g['GNRE001_UF_Favorecida']??'')));$environment=(string)($g['GNRE007_Ambiente']??'');
            if(!$snapshot||(int)($g['GNRE007_Id']??0)!==$snapshot||(int)($g['GNRE007_C004_Id']??0)!==$companyId||(string)($g['GNRE007_UF']??'')!==$uf||!in_array($environment,['1','2'],true)||!self::rules((string)($g['GNRE007_Regras_JSON']??''),$environment))throw new RuntimeException('A guia não possui snapshot oficial compatível.');
            $current=[$environment,self::LAYOUT_VERSION];if($base===null){$base=$current;continue;}
            if($base[0]!==$current[0])throw new RuntimeException('A coorte não pode misturar ambientes.');
            if($base[1]!==$current[1])throw new RuntimeException('A coorte não pode misturar versões XML.');
        }
    }
    /** @return array{fragment:string,fragment_sha256:string,fingerprint:string,complete:bool} */
    public static function describeParticipant(array $tdados):array { return GnreBatchCorrelationFingerprint::describe($tdados); }
    /** @param list<array{guide:array<string,mixed>,description:array<string,mixed>}> $entries @return list<list<array{guide:array<string,mixed>,description:array<string,mixed>}>> */
    public static function splitPhysicalLots(array $entries):array
    {
        $lots=[];$current=[];$bytes=512;$items=0;$limit=self::MAX_GUIDES_PER_PHYSICAL_LOT;$itemLimit=PHP_INT_MAX;
        foreach($entries as $entry){$size=max(1,(int)($entry['description']['physical_bytes']??0));$entryItems=max(0,(int)($entry['description']['item_count']??0));$entryLimit=max(1,min(self::MAX_GUIDES_PER_PHYSICAL_LOT,(int)($entry['description']['guide_limit']??self::MAX_GUIDES_PER_PHYSICAL_LOT)));$entryItemLimit=max(1,(int)($entry['description']['item_limit']??PHP_INT_MAX));if($size+512>self::MAX_PHYSICAL_XML_BYTES)throw new RuntimeException('O TDadosGNRE de uma guia excede o limite de 600 KB do lote físico.');$limit=min($limit,$entryLimit);$itemLimit=min($itemLimit,$entryItemLimit);if($entryItems>$itemLimit)throw new RuntimeException('Os itens da guia excedem o limite oficial do lote físico.');if($current&&(count($current)>=$limit||$items+$entryItems>$itemLimit||$bytes+$size>self::MAX_PHYSICAL_XML_BYTES)){$lots[]=$current;$current=[];$bytes=512;$items=0;$limit=$entryLimit;$itemLimit=$entryItemLimit;}$current[]=$entry;$bytes+=$size;$items+=$entryItems;}
        if($current)$lots[]=$current;return $lots;
    }
    /** Pure guard for the individual traces which make a draft ineligible. */
    public static function assertNoIndividualState(array $state):void { foreach(['attempts'=>'tentativa, recibo ou transmissão','receipts'=>'tentativa, recibo ou transmissão','queue'=>'fila de consulta','pdf'=>'PDF'] as $key=>$label)if(!empty($state[$key]))throw new RuntimeException('Guia com '.$label.' não pode participar da coorte.'); }
    /** The per-guide official limit is distinct from the physical lot limit. */
    public static function assertItemsPerGuide(int $count,int $officialLimit):void {if($officialLimit>0&&$count>$officialLimit)throw new RuntimeException('A guia possui mais itens que o máximo oficial permitido para uma guia.');}
    /** @return list<int> */
    private static function ids(array $values):array {$out=[];foreach($values as $v){$id=(int)$v;if($id>0)$out[$id]=$id;}return array_values($out);}
    private static function activeCompany(mysqli $db,int $company):void {$s=mysqli_prepare($db,"SELECT C004_Id FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo='S' FOR UPDATE");if(!$s)throw new RuntimeException('Não foi possível bloquear a empresa GNRE.');mysqli_stmt_bind_param($s,'i',$company);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$ok=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);if(!$ok)throw new RuntimeException('Empresa GNRE ativa não encontrada.');}
    /** @return list<array<string,mixed>> */
    private static function lockGuides(mysqli $db,array $ids):array {$in=implode(',',array_map('intval',$ids));$r=mysqli_query($db,"SELECT g.*,s.GNRE007_Id,s.GNRE007_C004_Id,s.GNRE007_Ambiente,s.GNRE007_UF,s.GNRE007_Regras_JSON FROM GNRE001 g LEFT JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id WHERE g.GNRE001_Id IN ($in) ORDER BY g.GNRE001_Id FOR UPDATE");if(!$r)throw new RuntimeException('Não foi possível bloquear as guias da coorte.');$out=[];while($row=mysqli_fetch_assoc($r))$out[]=$row;mysqli_free_result($r);return $out;}
    private static function existing(mysqli $db,array $ids):?int {$in=implode(',',array_map('intval',$ids));$count=count($ids);$r=mysqli_query($db,"SELECT c.GNRE010_Id,COUNT(p.GNRE011_Id) total,SUM(p.GNRE011_GNRE001_Id IN ($in)) selected_count FROM GNRE010 c JOIN GNRE011 p ON p.GNRE011_GNRE010_Id=c.GNRE010_Id WHERE c.GNRE010_Situacao='PREPARADO' GROUP BY c.GNRE010_Id HAVING total=$count AND selected_count=$count LIMIT 1");if(!$r)throw new RuntimeException('Não foi possível verificar a idempotência da coorte.');$row=mysqli_fetch_assoc($r);mysqli_free_result($r);return $row?(int)$row['GNRE010_Id']:null;}
    private static function noActiveParticipant(mysqli $db,array $ids):void {$in=implode(',',array_map('intval',$ids));self::none($db,"SELECT 1 FROM GNRE011 p JOIN GNRE010 c ON c.GNRE010_Id=p.GNRE011_GNRE010_Id WHERE c.GNRE010_Situacao NOT IN ('FINALIZADO','REJEITADO','CANCELADO') AND p.GNRE011_GNRE001_Id IN ($in) LIMIT 1",'Uma guia já pertence a outro lote ativo.');}
    private static function activeMembership(mysqli $db,int $id):?int {$s=mysqli_prepare($db,"SELECT c.GNRE010_Id FROM GNRE011 p JOIN GNRE010 c ON c.GNRE010_Id=p.GNRE011_GNRE010_Id WHERE p.GNRE011_GNRE001_Id=? AND c.GNRE010_Situacao NOT IN ('FINALIZADO','REJEITADO','CANCELADO') LIMIT 1 FOR UPDATE");if(!$s)throw new RuntimeException('Não foi possível verificar o lote local da guia.');mysqli_stmt_bind_param($s,'i',$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return $row?(int)$row['GNRE010_Id']:null;}
    /* Compatibilidade do nome da preparação local; a guarda agora cobre todo lote ativo. */
    private static function preparedMembership(mysqli $db,int $id):?int {return self::activeMembership($db,$id);}
    private static function noIndividualHistory(mysqli $db,array $ids):void {$in=implode(',',array_map('intval',$ids));$attempt=self::has($db,"SELECT 1 FROM GNRE004 WHERE GNRE004_GNRE001_Id IN ($in) LIMIT 1");$pdf=self::has($db,"SELECT 1 FROM GNRE008 WHERE GNRE008_GNRE001_Id IN ($in) LIMIT 1");$queue=self::has($db,"SELECT 1 FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id IN ($in) LIMIT 1");self::assertNoIndividualState(['attempts'=>$attempt,'queue'=>$queue,'pdf'=>$pdf]);}
    private static function noPdf(mysqli $db,array $ids):void {$in=implode(',',array_map('intval',$ids));if(self::has($db,"SELECT 1 FROM GNRE008 WHERE GNRE008_GNRE001_Id IN ($in) LIMIT 1"))throw new RuntimeException('Guia com PDF não pode participar da coorte.');}
    private static function hasFreshDraft(mysqli $db,int $id):bool{return !self::has($db,"SELECT 1 FROM GNRE004 WHERE GNRE004_GNRE001_Id=$id LIMIT 1");}
    private static function itemCount(mysqli $db,int $id):int {$r=mysqli_query($db,'SELECT COUNT(*) total FROM GNRE003 WHERE GNRE003_GNRE001_Id='.(int)$id);$row=$r?mysqli_fetch_assoc($r):null;if($r)mysqli_free_result($r);return (int)($row['total']??0);}
    private static function assertSafeReentry(mysqli $db,int $id):void { $r=mysqli_query($db,"SELECT GNRE004_Estado,GNRE004_Recibo,GNRE004_Erro_Tecnico,GNRE004_Resposta_SOAP FROM GNRE004 WHERE GNRE004_GNRE001_Id=$id ORDER BY GNRE004_Id DESC LIMIT 1");$a=$r?mysqli_fetch_assoc($r):null;if($r)mysqli_free_result($r);$soap=(string)($a['GNRE004_Resposta_SOAP']??'');$result=$soap!==''?GnreResultInterpreter::interpret($soap):null;$codes=is_array($result)?array_column((array)$result['guides'],'code'):[];$r=mysqli_query($db,"SELECT GNRE011_Situacao,GNRE011_Situacao_Guia FROM GNRE011 WHERE GNRE011_GNRE001_Id=$id ORDER BY GNRE011_Id DESC LIMIT 1");$p=$r?mysqli_fetch_assoc($r):null;if($r)mysqli_free_result($r);$ok=GnreRealBatchReentryPolicy::allowed(['situation'=>count($codes)===1?$codes[0]:(string)($p['GNRE011_Situacao_Guia']??''),'receipt'=>trim((string)($a['GNRE004_Recibo']??''))!=='','technical'=>trim((string)($a['GNRE004_Erro_Tecnico']??''))!=='','ambiguous'=>is_array($result)&&(!$result['valid']||count($codes)!==1),'reception_rejected'=>(string)($a['GNRE004_Estado']??'')==='REJEITADA'&&trim((string)($a['GNRE004_Recibo']??''))==='','batch_rejected'=>(string)($p['GNRE011_Situacao']??'')==='REJEITADO']);if(!$ok)throw new RuntimeException('A guia não possui rejeição comprovada para novo lote real.');}
    private static function has(mysqli $db,string $sql):bool {$r=mysqli_query($db,$sql);if(!$r)throw new RuntimeException('Não foi possível verificar o histórico individual da guia.');$row=mysqli_fetch_row($r);mysqli_free_result($r);return (bool)$row;}
    private static function none(mysqli $db,string $sql,string $message):void {$r=mysqli_query($db,$sql);if(!$r)throw new RuntimeException('Não foi possível verificar o histórico individual da guia.');$row=mysqli_fetch_row($r);mysqli_free_result($r);if($row)throw new RuntimeException($message);}
    private static function insertCohort(mysqli $db,array $g):int {/* snapshot/UF abaixo são metadados da primeira participante; não definem a composição nacional do lote. */$company=(int)$g['GNRE001_C004_Id'];$snapshot=(int)$g['GNRE001_GNRE007_Id'];$uf=strtoupper(trim((string)$g['GNRE001_UF_Favorecida']));$environment=(string)$g['GNRE007_Ambiente'];$layout=self::LAYOUT_VERSION;$version=self::FINGERPRINT_VERSION;$status=self::STATUS_PREPARED;$s=mysqli_prepare($db,'INSERT INTO GNRE010 (GNRE010_C004_Id,GNRE010_GNRE007_Id,GNRE010_UF_Favorecida,GNRE010_Ambiente,GNRE010_Layout_Versao,GNRE010_Fingerprint_Versao,GNRE010_Situacao,GNRE010_Data_Hora_Criacao) VALUES (?,?,?,?,?,?,?,NOW())');if(!$s)throw new RuntimeException('Não foi possível preparar a coorte.');mysqli_stmt_bind_param($s,'iisssss',$company,$snapshot,$uf,$environment,$layout,$version,$status);$ok=mysqli_stmt_execute($s);$id=(int)mysqli_insert_id($db);mysqli_stmt_close($s);if(!$ok||!$id)throw new RuntimeException('Não foi possível persistir a coorte.');return $id;}
    private static function insertParticipant(mysqli $db,int $cohort,int $guide,array $d):void {$version=self::FINGERPRINT_VERSION;$frozen=(string)($d['tdados_xml']??$d['fragment']);$hash=hash('sha256',$frozen);$s=mysqli_prepare($db,'INSERT INTO GNRE011 (GNRE011_GNRE010_Id,GNRE011_GNRE001_Id,GNRE011_Fragmento_Canonico,GNRE011_Fragmento_SHA256,GNRE011_Fingerprint_Negocio,GNRE011_Fingerprint_Versao,GNRE011_Decisao_Correlacao,GNRE011_Candidatos_Correlacao,GNRE011_Data_Hora_Criacao) VALUES (?,?,?,?,?,?,NULL,NULL,NOW())');if(!$s)throw new RuntimeException('Não foi possível preparar participante da coorte.');mysqli_stmt_bind_param($s,'iissss',$cohort,$guide,$frozen,$hash,$d['fingerprint'],$version);$ok=mysqli_stmt_execute($s);mysqli_stmt_close($s);if(!$ok)throw new RuntimeException('Não foi possível persistir participante da coorte.');}
    /** @return array<string,mixed> */
    /* Replaces the former GnreTdadosProjection::project path. Single source
       of truth: freeze the official TDadosGNRE actually emitted
       by the same generator/validation used by the individual flow. */
    /** @return array{tdados:array<string,mixed>,xml:string} */
    private static function project(mysqli $db,array $guide):array {$items=self::items($db,(int)$guide['GNRE001_Id']);$rules=json_decode((string)$guide['GNRE007_Regras_JSON'],true);if(!is_array($rules))throw new RuntimeException('O snapshot oficial da guia é inválido.');$emitter=(new GnreCompanyEmitter())->load((int)$guide['GNRE001_C004_Id'],$rules);$generated=(new GnreXmlGenerator())->generate($guide,$items,$rules,$emitter);if($generated['errors'])throw new RuntimeException(implode(' ',array_slice((array)$generated['errors'],0,3)));$dom=new DOMDocument();if(!@$dom->loadXML((string)$generated['xml'],LIBXML_NONET))throw new RuntimeException('O XML individual válido não pôde ser lido para o lote.');$node=(new DOMXPath($dom))->query('//*[local-name()="TDadosGNRE"]')->item(0);if(!$node instanceof DOMElement)throw new RuntimeException('O XML individual não contém TDadosGNRE.');self::addGuideIdentifier($node,(int)$guide['GNRE001_Id']);$frozen=new DOMDocument('1.0','UTF-8');$frozen->appendChild($frozen->importNode($node,true));return ['tdados'=>self::tdadosFromXml($node),'xml'=>(string)$frozen->saveXML()];}
    /**
     * Adds the ERP guide id only to a new real-batch fragment.  The individual
     * XML is merely the validated source for this copy and is never persisted
     * or changed by the batch pilot.
     */
    public static function addGuideIdentifier(DOMElement $node,int $guideId):void {
        $identifier=(string)$guideId;
        if(!preg_match('/^[1-9][0-9]{0,9}$/',$identifier))throw new RuntimeException('A guia não possui identificador válido para o lote real.');
        $existing=null;foreach($node->childNodes as $child)if($child instanceof DOMElement&&$child->localName==='identificadorGuia'){$existing=$child;break;}
        if($existing instanceof DOMElement){if(trim((string)$existing->textContent)!==$identifier)throw new RuntimeException('A guia possui identificador incompatível para o lote real.');return;}
        /* dadosGnre_2.00_3 places identificadorGuia after dataPagamento.
           Result-only fields can exist in a re-read fragment; they are after
           this group and must never be moved ahead of the identifier. */
        $document=$node->ownerDocument;if(!$document instanceof DOMDocument)throw new RuntimeException('Não foi possível preparar o identificador da guia.');
        $element=$node->namespaceURI!==null&&$node->namespaceURI!==''?$document->createElementNS($node->namespaceURI,'identificadorGuia',$identifier):$document->createElement('identificadorGuia',$identifier);
        $after=['dataLimitePagamento'=>true,'informacoesComplementares'=>true,'nossoNumero'=>true,'representacaoNumerica'=>true,'codigoBarras'=>true,'linhaDigitavel'=>true,'qrcodePayload'=>true,'motivosRejeicao'=>true];
        $firstAfter=null;foreach($node->childNodes as $child)if($child instanceof DOMElement&&isset($after[$child->localName])){$firstAfter=$child;break;}
        /* insertBefore(null) is deliberately used only when the XSD sequence
           has no subsequent element. It is the compatible position whether
           dataPagamento is present or omitted, never a blind append. */
        $node->insertBefore($element,$firstAfter);
    }
    private static function tdadosFromXml(DOMElement $node):array {$out=[];foreach($node->childNodes as $child){if(!$child instanceof DOMElement)continue;$name=$child->localName;$value=self::hasElementChildren($child)?self::tdadosFromXml($child):trim((string)$child->textContent);if($child->hasAttributes()){if(!is_array($value))$value=['valor'=>$value];foreach($child->attributes as $a)if($a instanceof DOMAttr)$value[$a->localName]=$a->value;}if(isset($out[$name])){$out[$name]=is_array($out[$name])&&array_keys($out[$name])===range(0,count($out[$name])-1)?$out[$name]:[$out[$name]];$out[$name][]=$value;}else $out[$name]=$value;}return $out;}
    /** DOMElement::childElementCount is unavailable on the supported PHP 7 runtime. */
    private static function hasElementChildren(DOMElement $node):bool {foreach($node->childNodes as $child)if($child instanceof DOMElement)return true;return false;}
    /** @return list<array<string,mixed>> */
    private static function items(mysqli $db,int $guide):array {$s=mysqli_prepare($db,'SELECT i.*,d.GNRE002_Identificacao FROM GNRE003 i LEFT JOIN GNRE002 d ON d.GNRE002_Id=i.GNRE003_GNRE002_Id WHERE i.GNRE003_GNRE001_Id=? ORDER BY i.GNRE003_Id');if(!$s)throw new RuntimeException('Não foi possível ler os itens da guia.');mysqli_stmt_bind_param($s,'i',$guide);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$out=[];while($r&&($row=mysqli_fetch_assoc($r))){$row['GNRE006']=self::values($db,(int)$row['GNRE003_Id']);$out[]=$row;}mysqli_stmt_close($s);return $out;}
    /** @return array<string,mixed> */
    private static function emitter(mysqli $db,int $company):array {$s=mysqli_prepare($db,"SELECT C004.C004_Cnpj,C004.C004_Nome_Empresa,C004.C004_Logradouro,C004.C004_Numero,C004.C004_Bairro,C004.C004_Cep,C004.C004_DDD_Telefone,C004.C004_Telefone,D020.D020_Codigo_IBGE,D018.D018_UF FROM C004 LEFT JOIN D020 ON D020.D020_Id=C004.C004_D020_Id LEFT JOIN D018 ON D018.D018_Id=C004.C004_D018_Id WHERE C004.C004_Id=? AND C004.C004_Flag_Ativo='S' LIMIT 1");if(!$s)throw new RuntimeException('Não foi possível ler o emitente da guia.');mysqli_stmt_bind_param($s,'i',$company);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);if(!$row)throw new RuntimeException('Emitente ativo não encontrado.');$digits=static function($value):string{return preg_replace('/\D/','',(string)$value)??'';};$city=$digits($row['D020_Codigo_IBGE']);$out=['cnpj'=>$digits($row['C004_Cnpj']),'name'=>trim((string)$row['C004_Nome_Empresa']),'address'=>trim(trim((string)$row['C004_Logradouro']).', '.trim((string)$row['C004_Numero']).' - '.trim((string)$row['C004_Bairro'])),'city'=>GnreMunicipality::gnreCode($city),'uf'=>trim((string)$row['D018_UF']),'cep'=>$digits($row['C004_Cep']),'phone'=>$digits($row['C004_DDD_Telefone'].$row['C004_Telefone']),'errors'=>[]];foreach(['cnpj'=>'CNPJ','name'=>'razão social'] as $key=>$label)if($out[$key]===''||($key==='cnpj'&&!preg_match('/^\d{14}$/',$out[$key])))$out['errors'][]=$label.' ausente ou inválido no cadastro da empresa.';return $out;}
    private static function values(mysqli $db,int $item):array {$r=mysqli_query($db,'SELECT GNRE006_Tipo_Valor,GNRE006_Valor FROM GNRE006 WHERE GNRE006_GNRE003_Id='.(int)$item.' ORDER BY GNRE006_Id');if(!$r)throw new RuntimeException('Não foi possível ler os valores da guia.');$out=[];while($row=mysqli_fetch_assoc($r))$out[]=['GNRE006_Tipo_Valor'=>(string)$row['GNRE006_Tipo_Valor'],'GNRE006_Valor'=>(string)$row['GNRE006_Valor']];mysqli_free_result($r);return $out;}
    private static function rules(string $json,string $environment):bool {try{$rules=json_decode($json,true,512,JSON_THROW_ON_ERROR);}catch(Throwable $e){return false;}return is_array($rules)&&(!isset($rules['environment'])||(string)$rules['environment']===$environment);}
    private static function publicReason(Throwable $error):string {
        $message=$error->getMessage();
        if(strpos($message,'rascunho')!==false)return 'A guia não está em rascunho.';
        if(strpos($message,'arquivada')!==false)return 'A guia está arquivada.';
        if(strpos($message,'snapshot')!==false)return 'A guia não possui snapshot oficial compatível.';
        if(strpos($message,'tentativa')!==false||strpos($message,'recibo')!==false||strpos($message,'fila')!==false||strpos($message,'PDF')!==false)return 'A guia já possui histórico individual e não pode entrar no lote local.';
        if(strpos($message,'lote ativo')!==false)return 'A guia já pertence a um lote ativo.';
        return $message!==''?$message:'A guia não pode ser preparada localmente agora.';
    }
    /** Runs the group callback as an all-or-nothing unit within the outer transaction. */
    private static function withGroupSavepoint(mysqli $db,int $number,callable $operation):array {
        $name='gnre_cohort_group_'.max(1,$number);
        return self::executeGroupAtomically(
            static function()use($db,$name):void {if(!mysqli_query($db,'SAVEPOINT '.$name))throw new RuntimeException('Não foi possível iniciar a preparação local da coorte.');},
            $operation,
            static function()use($db,$name):void {if(!mysqli_query($db,'ROLLBACK TO SAVEPOINT '.$name))throw new RuntimeException('Não foi possível reverter a coorte incompleta.');},
            static function()use($db,$name):void {if(!mysqli_query($db,'RELEASE SAVEPOINT '.$name))throw new RuntimeException('Não foi possível concluir a preparação local da coorte.');}
        );
    }
    /**
     * Shared transaction primitive. Public only so the behavioral test can use
     * an observable in-memory transaction adapter; production uses mysqli above.
     */
    public static function executeGroupAtomically(callable $savepoint,callable $operation,callable $rollback,callable $release):array {
        $savepoint();
        try {$result=$operation();$release();return $result;}
        catch(Throwable $error){
            try {$rollback();} catch(Throwable $rollbackError){throw new GnreRealBatchCohortRollbackFailure('Não foi possível reverter a coorte incompleta.',0,$rollbackError);}
            try {$release();} catch(Throwable $releaseError){throw new GnreRealBatchCohortRollbackFailure('Não foi possível concluir a reversão da coorte incompleta.',0,$releaseError);}
            throw $error;
        }
    }
}
