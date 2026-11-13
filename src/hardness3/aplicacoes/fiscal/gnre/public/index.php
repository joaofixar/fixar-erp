<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/ConfigUfRules.php';
require_once __DIR__ . '/../src/ConfigUfSnapshot.php';
require_once __DIR__ . '/../src/EnvironmentPreference.php';
require_once __DIR__ . '/../src/FavoredStateCatalog.php';
require_once __DIR__ . '/../src/FormDefinitionBuilder.php';
require_once __DIR__ . '/../src/Municipality.php';
require_once __DIR__ . '/../src/CompanyEmitter.php';
require_once __DIR__ . '/../src/SemanticValidator.php';
require_once __DIR__ . '/../src/ConfigUfClient.php';
require_once __DIR__ . '/../src/XmlGenerator.php';
require_once __DIR__ . '/../src/ReceptionClient.php';
require_once __DIR__ . '/../src/ResultClient.php';
require_once __DIR__ . '/../src/ResultInterpreter.php';
require_once __DIR__ . '/../src/ManualConsultFeedback.php';
require_once __DIR__ . '/../src/OfficialPdfService.php';
require_once __DIR__ . '/../src/AuthorizedGuidesPrint.php';
require_once __DIR__ . '/../src/QueueWorker.php';
require_once __DIR__ . '/../src/CorrectionReopen.php';
require_once __DIR__ . '/../src/HistoryResultProjection.php';
require_once __DIR__ . '/../src/GuideCenter.php';
require_once __DIR__ . '/../src/BatchTransmissionCandidates.php';
/* Real-batch pilot classes deliberately are not loaded here during emergency
   containment. Their source and audit records remain preserved. */
require_once __DIR__ . '/../src/GuideRetention.php';
require_once __DIR__ . '/../src/NfGnreEligibilityReader.php';
require_once __DIR__ . '/../src/NfGnreDraftCreator.php';

$context = gnre_require_access();
$csrf = gnre_csrf_token();
$xmlResult = null;
$configError = '';
$configResult = null;
$formError = '';
$submittedGuide = null;
$itemError = '';
$submittedItem = null;
/* The initial UF selector must not depend on local snapshots or call the Portal. */
$states = GnreFavoredStateCatalog::all();

/* Download é uma ação autenticada e vinculada novamente à empresa GNRE atual. */
if ((string)($_GET['action'] ?? '') === 'download_official_pdf') {
    $guideId=(int)($_GET['guide_id']??0); $documentId=(int)($_GET['document_id']??0);
    try { $document=GnreOfficialPdfService::download((int)$context['company_id'],$guideId,$documentId); if(!$document) gnre_abort('Documento oficial não encontrado para esta empresa e guia.',404); header('Content-Type: application/pdf'); header('Content-Disposition: '.((string)($_GET['print']??'')==='1'?'inline':'attachment').'; filename="gnre-oficial-'.$guideId.'.pdf"'); header('Content-Length: '.strlen((string)$document['GNRE008_Conteudo'])); header('X-Content-Type-Options: nosniff'); header('Cache-Control: private, no-store, max-age=0'); header('Pragma: no-cache'); echo $document['GNRE008_Conteudo']; exit; }
    catch(RuntimeException $e) { gnre_abort($e->getMessage(),503); }
}
if ((string)($_GET['action'] ?? '') === 'print_authorized_guides') {
    $guideIds=is_array($_GET['guide_id']??null)?$_GET['guide_id']:[];$documentIds=is_array($_GET['document_id']??null)?$_GET['document_id']:[];
    try {
        if(!$guideIds||count($guideIds)!==count($documentIds)||count($guideIds)>100) throw new RuntimeException('Não foi possível preparar a impressão das guias autorizadas.');
        $documents=[];foreach($guideIds as $position=>$guideId)$documents[]=['guide_id'=>(int)$guideId,'document_id'=>(int)($documentIds[$position]??0)];
        $content=(new GnreAuthorizedGuidesPrint())->render((int)$context['company_id'],$documents);
        header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="impressao-guias-autorizadas.pdf"');header('Content-Length: '.strlen($content));header('X-Content-Type-Options: nosniff');header('Cache-Control: private, no-store, max-age=0');header('Pragma: no-cache');echo $content;exit;
    } catch(Throwable $e) { gnre_abort($e->getMessage(),503); }
}

/* Troca somente o contexto desta aplicação; Empresa_Atual do ERP não é tocada. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'select_gnre_company') {
    gnre_require_csrf();
    gnre_select_company((int)($_POST['gnre_company_id'] ?? 0));
    header('Location: ' . gnre_url('?view=list'), true, 303);
    exit;
}

/* Emergency containment: pilot records stay intact for audit, but no pilot
   endpoint may prepare, send, consult, resume, cancel or mutate a cohort. */
$gnrePilotActions=['prepare_real_batch_pilot','send_real_batch_pilot','consult_real_batch_pilot','prepare_real_batch_pilot_queue','send_real_batch_pilot_groups','consult_real_batch_pilot_queue','resume_real_batch_pilot_queue','prepare_real_batch_cohorts','cancel_real_batch_cohort'];
if (in_array((string)($_GET['view'] ?? ''), ['real_batch_pilot','real_batch_cohorts'], true) || in_array((string)($_POST['action'] ?? ''), $gnrePilotActions, true)) {
    header('Location: '.gnre_url('?view=batch_transmit'), true, 303);
    exit;
}

/* Task 38: somente persiste uma preferência da empresa GNRE já autorizada. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'save_environment_preference') {
    gnre_require_csrf();
    try {
        $db = gnre_connect();
        if (!$db) throw new RuntimeException('Base indisponível.');
        (new GnreEnvironmentPreference())->save($db, (int) $context['company_id'], (string) ($_POST['environment'] ?? ''));
        mysqli_close($db);
        $_SESSION[GNRE_SESSION]['environment_preference_flash'] = ['message' => 'Preferência de ambiente GNRE salva.', 'error' => false];
    } catch (Throwable $e) {
        if (isset($db) && $db instanceof mysqli) @mysqli_close($db);
        $_SESSION[GNRE_SESSION]['environment_preference_flash'] = ['message' => $e->getMessage(), 'error' => true];
    }
    header('Location: ' . gnre_url('?view=config'), true, 303);
    exit;
}

function gnre_db(): mysqli { $db = gnre_connect(); if (!$db) gnre_abort('Base indisponível.', 503); return $db; }
function gnre34_origin_tab(string $tab): string { return in_array($tab,['dados','processar'],true)?$tab:'dados'; }
function gnre_back(int $guide, string $extra = ''): void {
    if((string)($_POST['gnre34']??'')==='1'){
        $company=(string)($GLOBALS['context']['company_id']??0);$origin=gnre34_origin_tab((string)($_POST['gnre34_origin_tab']??''));
        $_SESSION[GNRE_SESSION]['temporary']['gnre34_cycle'][$company][$guide]=['guide_id'=>$guide,'origin_tab'=>$origin,'active'=>true];
        if(isset($_SESSION[GNRE_SESSION]['temporary']['xml_result']))$_SESSION[GNRE_SESSION]['temporary']['gnre34_xml_result']=$_SESSION[GNRE_SESSION]['temporary']['xml_result'];
        header('Location: '.gnre_url('?edit='.$guide.'&tab='.$origin.'&gnre34=1'),true,303);exit;
    }
    header('Location: ' . gnre_url('?edit=' . $guide . $extra), true, 303); exit;
}
function gnre_guide_url(int $guide, string $tab = 'resumo', string $extra = ''): string { $tab=['validacao'=>'processar','transmissao'=>'processar','documento'=>'resumo'][$tab]??$tab;return gnre_url('?edit='.$guide.'&tab='.$tab.$extra); }
function gnre_field(string $name, int $length): ?string { $value = trim((string) ($_POST[$name] ?? '')); return mb_strlen($value, 'UTF-8') <= $length ? $value : null; }
function gnre_money(string $value): ?string { $value = str_replace(',', '.', trim($value)); return preg_match('/^\d{1,13}(\.\d{1,2})?$/', $value) && (float) $value > 0 ? number_format((float) $value, 2, '.', '') : null; }
function gnre_get_guide(mysqli $db, int $id, int $company): ?array { $s=mysqli_prepare($db,'SELECT * FROM GNRE001 WHERE GNRE001_Id=? AND GNRE001_C004_Id=?'); mysqli_stmt_bind_param($s,'ii',$id,$company); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $row=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($s); return $row?:null; }
function gnre_docs(mysqli $db, int $id): array { $s=mysqli_prepare($db,'SELECT d.*,n.T007_Numero_Nota_Fiscal AS NF_Numero,n.T007_Serie AS NF_Serie,n.T007_Data_Emissao AS NF_Data_Emissao,n.T007_Cliente_Estado AS NF_UF_Destinatario,n.T007_Chave_Acesso_Nfe AS NF_Chave_Acesso,n.T007_Valor_ICMS_Substituicao_Retencao AS NF_Valor_ST,n.T007_Valor_FCP_ST AS NF_Valor_FCP_ST,n.T007_Valor_Total_Partilha_ICMS_Destino AS NF_Valor_DIFAL,n.T007_Valor_Total_FCP_Partilha AS NF_Valor_FCP_Partilha FROM GNRE002 d LEFT JOIN T007 n ON n.T007_Id=d.GNRE002_T007_Id WHERE d.GNRE002_GNRE001_Id=? ORDER BY d.GNRE002_Id'); mysqli_stmt_bind_param($s,'i',$id); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $rows=[]; while($r&&($row=mysqli_fetch_assoc($r)))$rows[]=$row; mysqli_stmt_close($s); return $rows; }
function gnre_items(mysqli $db, int $id): array { $s=mysqli_prepare($db,'SELECT i.*,d.GNRE002_Identificacao,d.GNRE002_Descricao FROM GNRE003 i LEFT JOIN GNRE002 d ON d.GNRE002_Id=i.GNRE003_GNRE002_Id WHERE i.GNRE003_GNRE001_Id=? ORDER BY i.GNRE003_Id'); mysqli_stmt_bind_param($s,'i',$id); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $rows=[];$byId=[];while($r&&($row=mysqli_fetch_assoc($r))){$row['GNRE006']=[];$byId[(int)$row['GNRE003_Id']]=count($rows);$rows[]=$row;}mysqli_stmt_close($s);if(!$rows)return $rows;$s=mysqli_prepare($db,'SELECT GNRE006_GNRE003_Id,GNRE006_Tipo_Valor,GNRE006_Valor FROM GNRE006 WHERE GNRE006_GNRE003_Id IN (SELECT GNRE003_Id FROM GNRE003 WHERE GNRE003_GNRE001_Id=?) ORDER BY GNRE006_Id');mysqli_stmt_bind_param($s,'i',$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);while($r&&($value=mysqli_fetch_assoc($r))){$itemId=(int)$value['GNRE006_GNRE003_Id'];if(isset($byId[$itemId]))$rows[$byId[$itemId]]['GNRE006'][]=$value;}mysqli_stmt_close($s);return $rows; }
function gnre_draft(mysqli $db, int $id, array $context): ?array { $guide=gnre_get_guide($db,$id,$context['company_id']); return $guide&&$guide['GNRE001_Status']==='R'?$guide:null; }
function gnre_attempts(mysqli $db, int $id): array { $s=mysqli_prepare($db,'SELECT GNRE004_Id,GNRE004_Sequencia,GNRE004_Data_Hora_Criacao,GNRE004_Estado,GNRE004_HTTP_Status,GNRE004_Codigo_Retorno,GNRE004_Descricao_Retorno,GNRE004_Recibo,GNRE004_Erro_Tecnico FROM GNRE004 WHERE GNRE004_GNRE001_Id=? ORDER BY GNRE004_Sequencia DESC');mysqli_stmt_bind_param($s,'i',$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$out=[];while($r&&($row=mysqli_fetch_assoc($r)))$out[]=$row;mysqli_stmt_close($s);return $out; }
function gnre_result_queue(mysqli $db, int $id): ?array { $s=mysqli_prepare($db,"SELECT q.* FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id=? AND t.GNRE004_Estado='RECEBIDA' AND t.GNRE004_Recibo=q.GNRE005_Recibo ORDER BY q.GNRE005_Id DESC LIMIT 1");mysqli_stmt_bind_param($s,'i',$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return $row?:null; }
function gnre_result_queues(mysqli $db, int $id): array { $s=mysqli_prepare($db,'SELECT q.* FROM GNRE005 q JOIN GNRE004 t ON t.GNRE004_Id=q.GNRE005_GNRE004_Id WHERE t.GNRE004_GNRE001_Id=? ORDER BY q.GNRE005_Id ASC');mysqli_stmt_bind_param($s,'i',$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$out=[];while($r&&($row=mysqli_fetch_assoc($r)))$out[]=$row;mysqli_stmt_close($s);return $out; }
function gnre_consult_message(array $result): string { return GnreManualConsultFeedback::message((string)($result['outcome']??'')); }
function gnre_consult_flash_set(array $context, int $guideId, string $outcome): void { $_SESSION[GNRE_SESSION]['consult_flash'][(string)$context['company_id']][(string)$guideId]=['outcome'=>$outcome]; }
function gnre_consult_flash_take(array $context, int $guideId): ?array { $company=(string)$context['company_id'];$key=(string)$guideId;$flash=$_SESSION[GNRE_SESSION]['consult_flash'][$company][$key]??null;unset($_SESSION[GNRE_SESSION]['consult_flash'][$company][$key]);return is_array($flash)?$flash:null; }
/* A interface preserva as ações Consultar resultado e o aviso Resultado final já registrado. */
/** GNRE007, never the session, is the source of truth for a selected snapshot. */
/** Reads GNRE007 only for the explicit environment selected by the caller. */
function gnre_rules(array $context, ?string $uf, int $snapshotId, string $environment): ?array {
    $environment=GnreEnvironmentResolver::assertOperationalAllowed($environment);
    $db=gnre_db();
    if($snapshotId) $snapshot=GnreConfigUfSnapshot::byId($db,$snapshotId,$context);
    else { $choices=$uf?GnreConfigUfSnapshot::availableForUf($db,$context,(string)$uf,$environment):[]; $snapshot=$choices?GnreConfigUfSnapshot::byId($db,(int)$choices[0]['GNRE007_Id'],$context):null; }
    mysqli_close($db);
    if(!$snapshot || (string)($snapshot['GNRE007_Ambiente']??'')!==$environment || ($uf!==null&&(string)($snapshot['GNRE007_UF']??'')!==$uf)) return null;
    try {$rules=json_decode((string)$snapshot['GNRE007_Regras_JSON'],true,512,JSON_THROW_ON_ERROR);} catch(Throwable $e) { return null; }
    return is_array($rules)?['rules'=>$rules,'snapshot_id'=>(int)$snapshot['GNRE007_Id'],'snapshot'=>$snapshot]:null;
}
function gnre_current_rules(array $context, array $guide): ?array { $db=gnre_db(); $snapshot=GnreConfigUfSnapshot::forGuide($db,$guide,$context); mysqli_close($db); return $snapshot ? $snapshot['_rules'] : null; }
/** Session retains only the user's current selection; GNRE007 is authoritative. */
function gnre_store_rules(array $context, array $result, string $environment): array { $environment=GnreEnvironmentResolver::assertOperationalAllowed($environment);if((string)(($result['rules']??[])['environment']??'')!==$environment)throw new RuntimeException('A configuração oficial retornou ambiente divergente.');$db=gnre_db(); try {$snapshot=GnreConfigUfSnapshot::persist($db,$context,(string)$result['xml'],(array)$result['rules']);} finally {mysqli_close($db);} return $snapshot; }
function gnre_new_guide_state(array $context): array { return $_SESSION[GNRE_SESSION]['temporary']['new_guide'][(string)$context['company_id']]??[]; }
function gnre_set_new_guide_state(array $context, string $uf, string $environment): void { $_SESSION[GNRE_SESSION]['temporary']['new_guide'][(string)$context['company_id']]=['uf'=>$uf,'environment'=>GnreEnvironmentResolver::assertOperationalAllowed($environment)]; }
function gnre_clear_new_guide_state(array $context): void { unset($_SESSION[GNRE_SESSION]['temporary']['new_guide'][(string)$context['company_id']]); }
/** Called only after a committed change that can alter the generated XML. */
function gnre_invalidate_local_xml(array $context, int $guideId): void {
    $company=(string)$context['company_id'];
    unset(
        $_SESSION[GNRE_SESSION]['temporary']['xml_valid'][$company][$guideId],
        $_SESSION[GNRE_SESSION]['temporary']['xml_result'][$company][$guideId],
        $_SESSION[GNRE_SESSION]['temporary']['gnre34_xml_result'][$company][$guideId],
        $_SESSION[GNRE_SESSION]['temporary']['gnre34_cycle'][$company][$guideId],
        $_SESSION[GNRE_SESSION]['temporary']['gnre_transmission_outcome'][$company][$guideId]
    );
}
function gnre_transmit_atomic(mysqli $db, int $guideId, array $context): array {
    $guide=gnre_draft($db,$guideId,$context); if(!$guide) throw new RuntimeException('Somente rascunhos podem ser transmitidos.');
    $snapshot=GnreConfigUfSnapshot::forGuide($db,$guide,$context);
    if(!$snapshot) throw new RuntimeException('Esta guia não possui um snapshot oficial compatível.');
    $environment=GnreEnvironmentResolver::assertOperationalAllowed((string)$snapshot['GNRE007_Ambiente']);
    /* XML/transmissão use the linked immutable version; never call the Portal implicitly. */
    $rules=gnre_current_rules($context,$guide);
    if (!$rules) throw new RuntimeException('Esta guia não possui uma configuração oficial vinculada. Carregue e vincule uma configuração manualmente antes de gerar XML ou transmitir.');
    $items=gnre_items($db,$guideId); $emitter=(new GnreCompanyEmitter())->load((int)$context['company_id'],$rules);
    $xml=(new GnreXmlGenerator())->generate($guide,$items,$rules,$emitter); if($xml['errors']) return ['errors'=>$xml['errors']];
    if(!mysqli_begin_transaction($db)) throw new RuntimeException('Não foi possível iniciar a auditoria da transmissão.');
    try {
        $s=mysqli_prepare($db,'SELECT GNRE001_Status FROM GNRE001 WHERE GNRE001_Id=? AND GNRE001_C004_Id=? FOR UPDATE');
        mysqli_stmt_bind_param($s,'ii',$guideId,$context['company_id']); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $locked=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($s);
        if(!$locked||$locked['GNRE001_Status']!=='R') throw new RuntimeException('A guia não está disponível para transmissão.');
        /* Compatibility invariant: c.GNRE010_Situacao='PREPARADO' remains one
           of the active physical-batch states blocked from individual send. */
        $s=mysqli_prepare($db,"SELECT 1 FROM GNRE011 p JOIN GNRE010 c ON c.GNRE010_Id=p.GNRE011_GNRE010_Id WHERE p.GNRE011_GNRE001_Id=? AND c.GNRE010_Situacao IN ('PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','INCONCLUSIVO') LIMIT 1 FOR UPDATE");
        if(!$s)throw new RuntimeException('Não foi possível verificar a coorte local da guia.');
        mysqli_stmt_bind_param($s,'i',$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$preparedCohort=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);
        if($preparedCohort)throw new RuntimeException('Esta guia pertence a uma coorte preparada e não pode ser transmitida individualmente.');
        $s=mysqli_prepare($db,"SELECT 1 FROM GNRE011 p WHERE p.GNRE011_GNRE001_Id=? AND p.GNRE011_Situacao='FINALIZADO' AND p.GNRE011_Situacao_Guia='0' LIMIT 1 FOR UPDATE");mysqli_stmt_bind_param($s,'i',$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$authorizedBatch=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);if($authorizedBatch)throw new RuntimeException('Esta guia já foi autorizada por lote real e não pode ser transmitida individualmente.');
        $s=mysqli_prepare($db,"SELECT 1 FROM GNRE004 WHERE GNRE004_GNRE001_Id=? AND GNRE004_Estado='CRIADA' LIMIT 1 FOR UPDATE");
        mysqli_stmt_bind_param($s,'i',$guideId); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $created=$r&&mysqli_fetch_row($r); mysqli_stmt_close($s);
        if($created) throw new RuntimeException('Já existe uma tentativa de transmissão em andamento para esta guia.');
        $r=mysqli_query($db,'SELECT COALESCE(MAX(GNRE004_Sequencia),0)+1 AS n FROM GNRE004 WHERE GNRE004_GNRE001_Id='.(int)$guideId);
        $seq=(int)(mysqli_fetch_assoc($r)['n']??1); $hash=hash('sha256',$xml['xml']);
        $s=mysqli_prepare($db,"INSERT INTO GNRE004 (GNRE004_GNRE001_Id,GNRE004_Sequencia,GNRE004_XML_Enviado,GNRE004_XML_SHA256,GNRE004_Data_Hora_Criacao,GNRE004_C007_Id_Solicitante,GNRE004_Estado) VALUES (?,?,?,?,NOW(),?,'CRIADA')");
        mysqli_stmt_bind_param($s,'iissi',$guideId,$seq,$xml['xml'],$hash,$context['user_id']); $ok=mysqli_stmt_execute($s); $attempt=(int)mysqli_insert_id($db); mysqli_stmt_close($s);
        if(!$ok||!$attempt) throw new RuntimeException('Não foi possível registrar a tentativa antes do envio.');
        $s=mysqli_prepare($db,"UPDATE GNRE001 SET GNRE001_Status='I' WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status='R'");
        mysqli_stmt_bind_param($s,'ii',$guideId,$context['company_id']); $ok=mysqli_stmt_execute($s); $changed=mysqli_stmt_affected_rows($s); mysqli_stmt_close($s);
        if(!$ok||$changed!==1) throw new RuntimeException('Não foi possível bloquear a guia antes da transmissão.'); mysqli_commit($db);
    } catch(Throwable $e) { mysqli_rollback($db); throw $e; }
    try {
        $result=(new GnreReceptionClient())->process($context,$xml['xml'],$environment);
    } catch (Throwable $e) {
        /* The attempt already exists.  Persist a conservative public-safe
           inconclusion instead of losing it to the PRG/modal reconstruction. */
        $result=['kind'=>'inconclusive','http'=>0,'code'=>'','description'=>'','receipt'=>'','receipt_at'=>'','estimated_ms'=>'','soap'=>'','error'=>'Falha inesperada antes da confirmação de recibo.'];
    }
    $state=$result['kind']==='received'?'RECEBIDA':($result['kind']==='rejected'?'REJEITADA':'INCONCLUSIVA');
    $status=$result['kind']==='received'?'P':($result['kind']==='inconclusive'?'I':'R');
    if(!mysqli_begin_transaction($db)) throw new RuntimeException('A tentativa foi criada; não foi possível iniciar a gravação do retorno.');
    try {
        $s=mysqli_prepare($db,"UPDATE GNRE004 SET GNRE004_Data_Hora_Envio=NOW(),GNRE004_HTTP_Status=NULLIF(?,0),GNRE004_Codigo_Retorno=NULLIF(?,''),GNRE004_Descricao_Retorno=NULLIF(?,''),GNRE004_Recibo=NULLIF(?,''),GNRE004_Data_Hora_Recibo=NULLIF(?,''),GNRE004_Tempo_Estimado_Processamento_MS=NULLIF(?,0),GNRE004_Resposta_SOAP=NULLIF(?,''),GNRE004_Erro_Tecnico=NULLIF(?,''),GNRE004_Estado=? WHERE GNRE004_Id=?");
        mysqli_stmt_bind_param($s,'issssisssi',$result['http'],$result['code'],$result['description'],$result['receipt'],$result['receipt_at'],$result['estimated_ms'],$result['soap'],$result['error'],$state,$attempt); $ok=mysqli_stmt_execute($s); mysqli_stmt_close($s);
        if(!$ok) throw new RuntimeException('Não foi possível gravar o retorno da transmissão.');
        if ($result['kind']==='received') {
            $s=mysqli_prepare($db,"INSERT IGNORE INTO GNRE005 (GNRE005_GNRE004_Id,GNRE005_Recibo,GNRE005_Situacao,GNRE005_Proxima_Tentativa,GNRE005_Data_Hora_Criacao) VALUES (?,?,'PENDENTE',NOW(),NOW())");
            mysqli_stmt_bind_param($s,'is',$attempt,$result['receipt']); $ok=mysqli_stmt_execute($s); mysqli_stmt_close($s);
            if(!$ok) throw new RuntimeException('Não foi possível enfileirar o recibo GNRE.');
        }
        $s=mysqli_prepare($db,'UPDATE GNRE001 SET GNRE001_Status=? WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status=\'I\'');
        mysqli_stmt_bind_param($s,'sii',$status,$guideId,$context['company_id']); $ok=mysqli_stmt_execute($s); $changed=mysqli_stmt_affected_rows($s); mysqli_stmt_close($s);
        if(!$ok||($status!=='I'&&$changed!==1)) throw new RuntimeException('Não foi possível gravar atomicamente o estado da guia.');
        mysqli_commit($db);
    } catch(Throwable $e) { mysqli_rollback($db); throw $e; }
    return ['result'=>$result,'attempt'=>$attempt];
}
/** Task 63: a batch item is deliberately stricter than an ordinary draft. */
function gnre_batch_candidate(mysqli $db, int $guideId, array $context): array {
    $rows=GnreBatchTransmissionCandidates::load($db,(int)$context['company_id'],GnreGuideRetention::available($db));
    foreach($rows as $row) if((int)$row['GNRE001_Id']===$guideId){
        $snapshot=GnreConfigUfSnapshot::forGuide($db,$row,$context);
        $environment=$snapshot?(string)($snapshot['GNRE007_Ambiente']??''):'';
        $newDraft=empty($row['_attempts']) && ($row['_state']['key']??'')==='RASCUNHO';
        $rejectedReception=!empty($row['_retransmission_allowed']) && ($row['_state']['key']??'')==='RECEPCAO_REJEITADA';
        if((string)$row['GNRE001_Status']==='R' && empty($row['_queue']) && ($newDraft||$rejectedReception) && in_array($environment,['1','2'],true)) return ['guide'=>$row,'environment'=>$environment];
        break;
    }
    throw new RuntimeException('Esta guia não está mais disponível para transmissão em lote.');
}
function gnre_batch_reply(array $value): void { header('Content-Type: application/json; charset=utf-8'); echo json_encode($value,JSON_UNESCAPED_UNICODE); exit; }
/* The browser orchestrates one guide at a time. Every request repeats the
   ownership/state/snapshot check, so a forged or stale selection is harmless. */
function gnre_batch_step(array $context): void {
    $guideId=(int)($_POST['guide_id']??0); $phase=(string)($_POST['phase']??'');
    if($guideId<1 || !in_array($phase,['validate','transmit','consult','pdf'],true)) gnre_batch_reply(['ok'=>false,'state'=>'blocked','message'=>'Solicitação de lote inválida.']);
    $db=gnre_db();
    try {
        $candidate=null; $guide=null;
        if(in_array($phase,['validate','transmit'],true)) { $candidate=gnre_batch_candidate($db,$guideId,$context); $guide=$candidate['guide']; }
        else { $guide=gnre_get_guide($db,$guideId,(int)$context['company_id']); if(!$guide) throw new RuntimeException('Guia não encontrada para a empresa ativa.'); }
        if($phase==='validate') { $rules=gnre_current_rules($context,$guide); $emitter=$rules?(new GnreCompanyEmitter())->load((int)$context['company_id'],$rules):null; $xml=(new GnreXmlGenerator())->generate($guide,gnre_items($db,$guideId),$rules,$emitter); gnre_batch_reply(empty($xml['errors'])?['ok'=>true,'state'=>'validated','environment'=>$candidate['environment']]:['ok'=>false,'state'=>'invalid','errors'=>(array)$xml['errors'],'message'=>'A validação local do XML falhou.']); }
        if($phase==='transmit') { $result=gnre_transmit_atomic($db,$guideId,$context); if(!empty($result['errors'])) gnre_batch_reply(['ok'=>false,'state'=>'invalid','errors'=>$result['errors'],'message'=>'A validação local do XML falhou.']); $kind=(string)($result['result']['kind']??''); gnre_batch_reply(['ok'=>$kind==='received','state'=>$kind==='received'?'waiting':($kind==='rejected'?'rejected':'technical_error'),'message'=>$kind==='received'?'Transmitida e aguardando resultado.':($kind==='rejected'?'A recepção foi rejeitada.':'Não foi possível confirmar a recepção da guia. Não haverá retransmissão automática.')]); }
        mysqli_close($db); $db=null;
        if($phase==='consult') {
            $result=(new GnreQueueWorker())->consultGuide((int)$context['company_id'],$guideId); $out=(string)($result['outcome']??'');
            $read=gnre_db(); try {$rows=GnreGuideCenter::load($read,(int)$context['company_id'],[],GnreGuideRetention::available($read));} finally {mysqli_close($read);}
            $state=$out; foreach($rows as $row) if((int)$row['GNRE001_Id']===$guideId) { $key=(string)($row['_state']['key']??''); if($key==='EMITIDA')$state='authorized'; elseif($key==='REJEITADA'||$key==='RECEPCAO_REJEITADA')$state='rejected'; elseif($key==='INCONCLUSIVA')$state='inconclusive'; break; }
            gnre_batch_reply(['ok'=>true,'state'=>$state,'message'=>GnreManualConsultFeedback::message($out)]);
        }
        if($phase==='pdf') { $result=(new GnreOfficialPdfService())->obtain((int)$context['company_id'],$guideId,(int)$context['user_id']); gnre_batch_reply(['ok'=>true,'state'=>'authorized','message'=>(string)$result['message'],'status'=>(string)($result['status']??''),'document_id'=>(int)($result['document_id']??0)]); }
    } catch(Throwable $e) { gnre_batch_reply(['ok'=>false,'state'=>'blocked','message'=>GnreBatchTransmissionCandidates::unexpectedFailureMessage($e)]); }
    finally { if($db instanceof mysqli) mysqli_close($db); }
}
function gnre_item_values(): ?array {
    $v=['document'=>(int)($_POST['document_id']??0),'receipt'=>gnre_field('receipt',6),'detail'=>gnre_field('detail',6),'type'=>(string)($_POST['value_type']??''),'amount'=>gnre_money((string)($_POST['item_amount']??'')),'document_type'=>gnre_field('document_type',2),'product'=>gnre_field('product',4),'period'=>gnre_field('period',1),'month'=>gnre_field('month',2),'year'=>gnre_field('year',4),'installment'=>gnre_field('installment',3),'recipient_type'=>(string)($_POST['recipient_type']??''),'recipient_id'=>gnre_field('recipient_id',16),'recipient_name'=>gnre_field('recipient_name',60),'recipient_city'=>gnre_field('recipient_city',5),'extras'=>gnre_field('extras',5000)];
    if(!$v['receipt']||!$v['amount']||$v['detail']===null||$v['document_type']===null||$v['product']===null||$v['period']===null||$v['month']===null||$v['year']===null||$v['installment']===null||$v['recipient_id']===null||$v['recipient_name']===null||$v['recipient_city']===null||$v['extras']===null||!in_array($v['type'],['11','12','21','22','31','32','41','42','51','52'],true)||($v['recipient_type']!==''&&!in_array($v['recipient_type'],['CNPJ','CPF','IE'],true))) return null;
    return $v;
}

require_once __DIR__ . '/../src/GuidedDraftController.php';
require_once __DIR__ . '/../src/GuideItemTransaction.php';
require_once __DIR__ . '/../src/GuideItemEditPolicy.php';
require_once __DIR__ . '/../src/GuideDraftController.php';
require_once __DIR__ . '/../src/LegacyTask18Compatibility.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array((string) ($_POST['action'] ?? ''), ['task60_prepare_batch','task60_create_batch','task60_cancel_batch'], true)) {
    gnre_require_csrf(); $company=(string) $context['company_id']; $action=(string) $_POST['action'];
    if ($action==='task60_cancel_batch') { unset($_SESSION[GNRE_SESSION]['task60_batch'][$company]);$_SESSION[GNRE_SESSION]['task60_flash']=['message'=>'Conferência descartada. Nenhum rascunho foi criado.','error'=>false];header('Location: '.gnre_url('?view=source_notes'),true,303);exit; }
    if ($action==='task60_prepare_batch') {
        $ids=is_array($_POST['note_ids']??null)?$_POST['note_ids']:[]; $db=null;
        try {$db=gnre_db();$entries=GnreNfDraftCreator::plan($db,$context,$ids);mysqli_close($db);$token=bin2hex(random_bytes(24));$_SESSION[GNRE_SESSION]['task60_batch'][$company]=['token'=>$token,'entries'=>$entries];$_SESSION[GNRE_SESSION]['task60_flash']=['message'=>$entries?'Confira os rascunhos planejados antes de criar.':'Selecione ao menos uma NF para preparar o lote.','error'=>!$entries];}
        catch(Throwable $e){if($db instanceof mysqli)mysqli_close($db);$_SESSION[GNRE_SESSION]['task60_flash']=['message'=>$e->getMessage(),'error'=>true];}
        header('Location: '.gnre_url('?view=source_notes&batch_plan=1'),true,303);exit;
    }
    $batch=(array)($_SESSION[GNRE_SESSION]['task60_batch'][$company]??[]);
    if(!hash_equals((string)($batch['token']??''),(string)($_POST['batch_token']??''))){$_SESSION[GNRE_SESSION]['task60_flash']=['message'=>'A conferência do lote expirou. Prepare as notas novamente.','error'=>true];header('Location: '.gnre_url('?view=source_notes'),true,303);exit;}
    $types=is_array($_POST['gnre_types']??null)?$_POST['gnre_types']:[];$results=[];$db=gnre_db();
    try {foreach((array)($batch['entries']??[]) as $entry){$key=(int)$entry['note_id'].'_'.(string)$entry['tax'];if(($entry['status']??'')==='ready')$result=GnreNfDraftCreator::createComponent($db,$context,(int)$entry['note_id'],(string)$entry['tax'],(string)($types[$key]??''));elseif(($entry['status']??'')==='existing')$result=['outcome'=>'existing','guide_id'=>(int)($entry['guide_id']??0),'message'=>(string)$entry['message']];else $result=['outcome'=>'blocked','message'=>(string)$entry['message']];$results[]=array_replace($entry,$result);}mysqli_close($db);}catch(Throwable $e){if($db instanceof mysqli)mysqli_close($db);$results[]=['note_id'=>0,'tax'=>'','outcome'=>'failure','message'=>'Falha segura no processamento do lote.'];}
    unset($_SESSION[GNRE_SESSION]['task60_batch'][$company]);$_SESSION[GNRE_SESSION]['task60_batch_results'][$company]=$results;header('Location: '.gnre_url('?view=source_notes&batch_result=1'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'reopen_rejected_guide') {
    gnre_require_csrf(); $sourceId=(int)($_POST['guide_id']??0); $db=null;
    try { $db=gnre_db(); $reopened=GnreCorrectionReopen::reopen($db,$sourceId,$context); mysqli_close($db); if($reopened['outcome']==='reopened')gnre_invalidate_local_xml($context,$sourceId); $_SESSION[GNRE_SESSION]['task23_flash']=['message'=>$reopened['outcome']==='already_open'?'Esta guia já está reaberta como rascunho; o histórico técnico foi preservado.':'Guia #'.$sourceId.' reaberta para correção. Corrija os dados, gere e valide um novo XML e transmita quando estiver pronto.','error'=>false]; /* A correction must leave the historical reception modal instead of reopening it through gnre34. */ header('Location: '.gnre_url('?edit='.$sourceId.'&tab=dados'),true,303);exit; }
    catch(Throwable $e) { if($db instanceof mysqli)@mysqli_close($db); $_SESSION[GNRE_SESSION]['task23_flash']=['message'=>$e->getMessage(),'error'=>true]; gnre_back($sourceId); }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action']??'')==='delete_draft') {
    gnre_require_csrf();$guideId=(int)($_POST['guide_id']??0);$db=null;
    try{$db=gnre_db();if(!GnreGuideRetention::available($db)){GnreGuideRetention::deleteWithoutArchiveColumn($db,$guideId,(int)$context['company_id']);mysqli_close($db);$_SESSION[GNRE_SESSION]['retention_flash']=['message'=>'Rascunho excluído definitivamente.','error'=>false];header('Location: '.gnre_url('?view=list'),true,303);exit;}}catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['retention_flash']=['message'=>$e->getMessage(),'error'=>true];header('Location: '.gnre_guide_url($guideId),true,303);exit;}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array((string)($_POST['action'] ?? ''), ['delete_draft','archive_guide','unarchive_guide'], true)) {
    gnre_require_csrf(); $actions=['delete_draft'=>'delete','archive_guide'=>'archive','unarchive_guide'=>'unarchive'];$guideId=(int)($_POST['guide_id']??0);$db=null;
    try {$db=gnre_db();if($_POST['action']!=='delete_draft'&&!GnreGuideRetention::available($db))throw new RuntimeException('O SQL manual da Task 45 ainda não foi aplicado; arquivamento está indisponível.');GnreGuideRetention::execute($db,$guideId,(int)$context['company_id'],$actions[$_POST['action']]);mysqli_close($db);$messages=['delete_draft'=>'Rascunho excluído definitivamente.','archive_guide'=>'Guia arquivada; o histórico técnico foi preservado.','unarchive_guide'=>'Guia desarquivada.'];$_SESSION[GNRE_SESSION]['retention_flash']=['message'=>$messages[$_POST['action']],'error'=>false];header('Location: '.gnre_url('?view=list'),true,303);exit;}catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['retention_flash']=['message'=>$e->getMessage(),'error'=>true];header('Location: '.gnre_guide_url($guideId),true,303);exit;}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    gnre_legacy_task18_adapt_request();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos((string)($_POST['action'] ?? ''), 'guide_draft_') === 0) {
    gnre_guide_draft_handle($context);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos((string)($_POST['action'] ?? ''), 'task15_') === 0) {
    gnre_task15_handle($context, $csrf);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'task63_batch_step') { gnre_require_csrf(); gnre_batch_step($context); }
function gnre_real_batch_send_public_error(Throwable $error): string {
    $message=trim($error->getMessage());
    foreach(['XML físico','conteúdo congelado','TDadosGNRE','participante','lote piloto','PDF individual','schema mínimo'] as $safe)if(stripos($message,$safe)!==false)return $message;
    return 'O lote não foi enviado. Revise as guias e tente novamente.';
}
/* Task 76: the physical pilot starts from the very same queue selection.  The
   sequential JavaScript flow above is deliberately untouched. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'prepare_real_batch_pilot_queue') {
    gnre_require_csrf();$ids=is_array($_POST['guide_ids']??null)?$_POST['guide_ids']:[];$db=null;
    try {
        if(!$ids)throw new RuntimeException('Selecione ao menos uma guia para o lote real.');
        $db=gnre_db();if(!GnreRealBatchPilot::available($db))throw new RuntimeException('O schema mínimo do piloto ainda não está disponível.');
        /* One action owns preparation and reception. Consultation starts only
           after the official persisted waiting window for each physical lot. */
        $out=(new GnreRealBatchCohort())->prepareAutomatically($db,(int)$context['company_id'],$ids,true);
        $pilot=new GnreRealBatchPilot();$groups=[];
        foreach((array)($out['cohort_ids']??[]) as $cohortId){
            try {
                $sent=$pilot->send($db,(int)$context['company_id'],(int)$context['user_id'],(int)$cohortId);
                $groups[]=['cohort_id'=>(int)$cohortId,'status'=>(string)($sent['status']??'INCONCLUSIVO')];
            } catch(Throwable $groupError) {
                /* Keep the persisted group for audit. Never retry it here and
                   never let its error abort processing of another group. */
                $groups[]=['cohort_id'=>(int)$cohortId,'status'=>'PREPARADO','not_sent'=>true,'message'=>gnre_real_batch_send_public_error($groupError)];
            }
        }
        mysqli_close($db);$db=null;
        $_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['result'=>$out,'groups'=>$groups,'error'=>false];
    }
    catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['message'=>$e->getMessage(),'error'=>true];}
    header('Location: '.gnre_url('?view=batch_transmit&real_batch_pilot=1'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'send_real_batch_pilot_groups') {
    gnre_require_csrf();$ids=is_array($_POST['cohort_ids']??null)?$_POST['cohort_ids']:[];$ids=array_values(array_unique(array_filter(array_map('intval',$ids))));$db=null;
    try {if(!$ids)throw new RuntimeException('Não há lote preparado para transmitir.');$db=gnre_db();$pilot=new GnreRealBatchPilot();$groups=[];foreach($ids as $id){try{$sent=$pilot->send($db,(int)$context['company_id'],(int)$context['user_id'],$id);$groups[]=['cohort_id'=>$id,'status'=>(string)($sent['status']??'INCONCLUSIVO')];}catch(Throwable $e){$groups[]=['cohort_id'=>$id,'status'=>'PREPARADO','not_sent'=>true,'message'=>gnre_real_batch_send_public_error($e)];}}mysqli_close($db);$db=null;$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['sent'=>$groups,'groups'=>$groups,'error'=>false];}
    catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['message'=>'Não foi possível preparar o envio dos grupos. Nenhuma retransmissão automática foi realizada.','error'=>true];}
    header('Location: '.gnre_url('?view=batch_transmit&real_batch_pilot=1'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'consult_real_batch_pilot_queue') {
    gnre_require_csrf();$db=null;try{$db=gnre_db();$out=(new GnreRealBatchPilot())->consult($db,(int)$context['company_id'],(int)($_POST['cohort_id']??0));mysqli_close($db);$db=null;$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['consulted'=>$out,'error'=>false];}catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['message'=>'Não foi possível confirmar o resultado; o grupo permanece bloqueado para retransmissão.','error'=>true];}header('Location: '.gnre_url('?view=batch_transmit&real_batch_pilot=1'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'resume_real_batch_pilot_queue') {
    gnre_require_csrf();$db=null;try{$db=gnre_db();$out=(new GnreRealBatchPilot())->consult($db,(int)$context['company_id'],(int)($_POST['cohort_id']??0),true);mysqli_close($db);$db=null;$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['consulted'=>$out,'error'=>false];}catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_queue_flash']=['message'=>'Não foi possível retomar a consulta; o grupo continua bloqueado para retransmissão.','error'=>true];}header('Location: '.gnre_url('?view=batch_transmit&real_batch_pilot=1'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'prepare_real_batch_cohorts') {
    gnre_require_csrf();$ids=is_array($_POST['guide_ids']??null)?$_POST['guide_ids']:[];$db=null;
    if(!$ids){$_SESSION[GNRE_SESSION]['real_batch_preparation_flash']=['empty'=>true];header('Location: '.gnre_url('?view=batch_transmit'),true,303);exit;}
    try {$db=gnre_db();$result=(new GnreRealBatchCohort())->prepareAutomatically($db,(int)$context['company_id'],$ids);mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_preparation_flash']=['result'=>$result];}
    catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_preparation_flash']=['error'=>true,'message'=>'Não foi possível preparar os lotes locais agora. Nenhuma transmissão foi realizada.'];}
    header('Location: '.gnre_url('?view=batch_transmit'),true,303);exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'cancel_real_batch_cohort') {
    gnre_require_csrf();$db=null;
    try {$db=gnre_db();$result=(new GnreRealBatchCohortCancellation())->cancel($db,(int)$context['company_id'],(int)($_POST['cohort_id']??0));mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_cohort_flash']=['message'=>$result['idempotent']?'A coorte já estava cancelada.':'Lote preparado cancelado localmente. As guias voltaram a ficar disponíveis conforme as regras normais.','error'=>false];}
    catch(Throwable $e){if($db instanceof mysqli)@mysqli_close($db);$_SESSION[GNRE_SESSION]['real_batch_cohort_flash']=['message'=>$e->getMessage(),'error'=>true];}
    header('Location: '.gnre_url('?view=real_batch_cohorts'),true,303);exit;
}
if($_SERVER['REQUEST_METHOD']==='POST') {
    gnre_require_csrf(); $action=(string)($_POST['action']??'');
    if($action==='save_item') { $checkDb=gnre_db(); $checkGuide=gnre_draft($checkDb,(int)($_POST['guide_id']??0),$context); mysqli_close($checkDb); if($checkGuide&&!gnre_current_rules($context,$checkGuide)) { $itemError='Recarregue a configuração oficial da UF antes de alterar itens desta guia.'; $_GET['edit']=(int)$_POST['guide_id']; $action='item_config_missing'; } }
    if($action==='start_new_guide') { try { $uf=strtoupper(trim((string)($_POST['favored_state']??''))); if(!preg_match('/^[A-Z]{2}$/',$uf)) throw new RuntimeException('Selecione a UF favorecida para continuar.'); $preferenceDb=gnre_db();try{$environment=(new GnreEnvironmentPreference())->read($preferenceDb,(int)$context['company_id']);}finally{mysqli_close($preferenceDb);}$environment=GnreEnvironmentResolver::assertOperationalAllowed($environment);$loaded=gnre_rules($context,$uf,0,$environment); if($loaded) $configResult=$loaded; else { $configResult=(new GnreConfigUfClient())->consult($context,$uf,null,true,$environment); gnre_store_rules($context,$configResult,$environment); } gnre_set_new_guide_state($context,$uf,$environment); $_GET['view']='new'; } catch(RuntimeException $e) {$configError='Não foi possível carregar a configuração oficial da UF: '.$e->getMessage(); $_GET['view']='new';} }
    elseif($action==='attach_guide_config') { $guideId=(int)($_POST['guide_id']??0); $_GET['edit']=$guideId; $configError='Esta guia legada não possui snapshot vinculado. Crie uma nova guia pelo fluxo atual para selecionar uma configuração oficial com ambiente explícito.'; }
    elseif($action==='reload_guide_config') { $guideId=(int)($_POST['guide_id']??0); $_GET['edit']=$guideId; try { $db=gnre_db();$reloadGuide=gnre_get_guide($db,$guideId,$context['company_id']);$snapshot=$reloadGuide?GnreConfigUfSnapshot::forGuide($db,$reloadGuide,$context):null;mysqli_close($db);$db=null;if(!$reloadGuide||!$snapshot) throw new RuntimeException('A guia não possui snapshot oficial vinculado; use o fluxo atual para uma nova guia.');$environment=GnreEnvironmentResolver::assertOperationalAllowed((string)$snapshot['GNRE007_Ambiente']);$configResult=(new GnreConfigUfClient())->consult($context,(string)$reloadGuide['GNRE001_UF_Favorecida'],null,true,$environment);gnre_store_rules($context,$configResult,$environment); } catch(RuntimeException $e) { if(isset($db)&&$db instanceof mysqli)mysqli_close($db);$configError='Não foi possível recarregar a configuração oficial da UF: '.$e->getMessage(); } }
    elseif($action==='consult_config_uf') { try { $uf=strtoupper(trim((string)($_POST['configuration_uf']??''))); $r=trim((string)($_POST['configuration_revenue']??''));$preferenceDb=gnre_db();try{$environment=(new GnreEnvironmentPreference())->read($preferenceDb,(int)$context['company_id']);}finally{mysqli_close($preferenceDb);}$environment=GnreEnvironmentResolver::assertOperationalAllowed($environment);$configResult=(new GnreConfigUfClient())->consult($context,$uf,$r===''?null:$r,true,$environment);gnre_store_rules($context,$configResult,$environment); } catch(RuntimeException $e) {$configError=$e->getMessage();} }
    elseif($action==='create') { $formError='O cadastro legado não cria guias sem snapshot. Use o fluxo atual de nova guia.';$_GET['view']='new'; }
    else {
        $db=gnre_db(); $guideId=(int)($_POST['guide_id']??0);
        if($action==='generate_xml') { $guide=gnre_draft($db,$guideId,$context); if(!$guide||empty($guide['GNRE001_GNRE007_Id'])){$xmlResult=['errors'=>['A guia deve estar em rascunho e possuir um snapshot oficial vinculado antes da geração local.']];}else{$rules=gnre_current_rules($context,$guide); $emitter=$rules?(new GnreCompanyEmitter())->load((int)$context['company_id'],$rules):null; $xmlResult=(new GnreXmlGenerator())->generate($guide,gnre_items($db,$guideId),$rules,$emitter);}$_SESSION[GNRE_SESSION]['temporary']['xml_valid'][(string)$context['company_id']][$guideId]=empty($xmlResult['errors']);$_SESSION[GNRE_SESSION]['temporary']['xml_result'][(string)$context['company_id']][$guideId]=['result'=>$xmlResult,'at'=>date('c')];mysqli_close($db);gnre_back($guideId,'&tab=validacao'.((string)($_POST['gnre34']??'')==='1'?'&gnre34=1':((string)($_POST['gnre57_fallback']??'')==='1'?'&gnre57_confirm=1':''))); }
        elseif($action==='transmit_homologation') { unset($_SESSION[GNRE_SESSION]['temporary']['gnre_transmission_outcome'][(string)$context['company_id']][$guideId]); try{$transmitResult=gnre_transmit_atomic($db,$guideId,$context);$_GET['edit']=$guideId;}catch(Throwable $e){/* PRG/reload must retain a safe outcome even when no attempt could be finalized. */$_SESSION[GNRE_SESSION]['temporary']['gnre_transmission_outcome'][(string)$context['company_id']][$guideId]=['at'=>date('c'),'state'=>'technical_error'];$transmitError='Não foi possível confirmar a recepção da guia. Nenhuma retransmissão automática foi realizada.';$_GET['edit']=$guideId;}mysqli_close($db); }
        elseif($action==='consult_result') { try { mysqli_close($db);$db=null;$consultResult=(new GnreQueueWorker())->consultGuide((int)$context['company_id'],$guideId);gnre_consult_flash_set($context,$guideId,(string)($consultResult['outcome']??'')); } catch(Throwable $e) { if($db instanceof mysqli)mysqli_close($db);gnre_consult_flash_set($context,$guideId,'exception'); } /* Compatibilidade Task 07: gnre_back($guideId,'&tab=processar') */ gnre_back($guideId,'&tab=processar'.((string)($_POST['gnre34']??'')==='1'?'&gnre34=1':'')); }
        elseif($action==='obtain_official_pdf') { try { mysqli_close($db); $obtained=(new GnreOfficialPdfService())->obtain((int)$context['company_id'],$guideId,(int)$context['user_id']); $_SESSION[GNRE_SESSION]['task25_flash']=['message'=>$obtained['message'],'error'=>false]; } catch(Throwable $e) { if($db instanceof mysqli) @mysqli_close($db); $_SESSION[GNRE_SESSION]['task25_flash']=['message'=>$e->getMessage(),'error'=>true]; } gnre_back($guideId,'&tab=documento'.((string)($_POST['gnre34']??'')==='1'?'&gnre34=1':'')); }
        elseif($action==='add_document') { $guide=gnre_draft($db,$guideId,$context); $number=gnre_field('document_number',100); $description=gnre_field('document_description',500); if(!$guide||!$number||$description===null)gnre_abort('Documento inválido.',422); $s=mysqli_prepare($db,'INSERT INTO GNRE002 (GNRE002_GNRE001_Id,GNRE002_Identificacao,GNRE002_Descricao,GNRE002_Data_Hora_Criacao) VALUES (?, ?, NULLIF(?,\'\'),NOW())'); mysqli_stmt_bind_param($s,'iss',$guideId,$number,$description); $ok=mysqli_stmt_execute($s); mysqli_stmt_close($s); mysqli_close($db); if(!$ok)gnre_abort('Não foi possível gravar o documento.',409); gnre_invalidate_local_xml($context,$guideId);gnre_back($guideId); }
        elseif($action==='delete_document') { $guide=gnre_draft($db,$guideId,$context); $doc=(int)($_POST['document_id']??0); if(!$guide)gnre_abort('Rascunho inválido.',409); $s=mysqli_prepare($db,'SELECT 1 FROM GNRE003 WHERE GNRE003_GNRE002_Id=? LIMIT 1'); mysqli_stmt_bind_param($s,'i',$doc); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $used=$r&&mysqli_fetch_row($r); mysqli_stmt_close($s); if($used)gnre_abort('Documento já usado por item de recolhimento.',409); $s=mysqli_prepare($db,'DELETE FROM GNRE002 WHERE GNRE002_Id=? AND GNRE002_GNRE001_Id=?'); mysqli_stmt_bind_param($s,'ii',$doc,$guideId); $ok=mysqli_stmt_execute($s); mysqli_stmt_close($s); mysqli_close($db); if(!$ok)gnre_abort('Não foi possível remover o documento.',409); gnre_invalidate_local_xml($context,$guideId);gnre_back($guideId); }
        elseif($action==='item_config_missing') { mysqli_close($db); }
        elseif($action==='save_item') { $guide=gnre_draft($db,$guideId,$context); $values=gnre_item_values(); $itemId=(int)($_POST['item_id']??0); if(!$guide||!$values)gnre_abort('Item inválido.',422); $rules=gnre_current_rules($context,$guide); if(!$rules)gnre_abort('Carregue a configuração oficial desta UF antes de gravar itens.',422); $rr=GnreConfigUfRules::receipt($rules,$values['receipt']); if(!$rr)gnre_abort('Receita não permitida pela configuração oficial carregada.',422); foreach(['detail'=>'details','product'=>'products','period'=>'periods','document_type'=>'document_types'] as $field=>$list)if($values[$field]!==''&&$rr[$list]&&!in_array($values[$field],array_column($rr[$list],'code'),true))gnre_abort('Valor não permitido pela configuração oficial: '.$field.'.',422); if($values['document']){$s=mysqli_prepare($db,'SELECT 1 FROM GNRE002 WHERE GNRE002_Id=? AND GNRE002_GNRE001_Id=?');mysqli_stmt_bind_param($s,'ii',$values['document'],$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$valid=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);if(!$valid)gnre_abort('Documento de origem inválido.',422);}else $values['document']=null;
            $sql='GNRE003_GNRE002_Id=?,GNRE003_Codigo_Receita=?,GNRE003_Detalhamento_Receita=NULLIF(?,\'\'),GNRE003_Tipo_Valor=?,GNRE003_Valor=?,GNRE003_Tipo_Documento_Origem=NULLIF(?,\'\'),GNRE003_Codigo_Produto=NULLIF(?,\'\'),GNRE003_Referencia_Periodo=NULLIF(?,\'\'),GNRE003_Referencia_Mes=NULLIF(?,\'\'),GNRE003_Referencia_Ano=NULLIF(?,\'\'),GNRE003_Referencia_Parcela=NULLIF(?,\'\'),GNRE003_Destinatario_Tipo_Identificacao=NULLIF(?,\'\'),GNRE003_Destinatario_Identificacao=NULLIF(?,\'\'),GNRE003_Destinatario_Razao_Social=NULLIF(?,\'\'),GNRE003_Destinatario_Codigo_Municipio=NULLIF(?,\'\'),GNRE003_Campos_Extras_JSON=NULLIF(?,\'\')';
            if($itemId){$s=mysqli_prepare($db,"UPDATE GNRE003 SET $sql,GNRE003_Data_Hora_Alteracao=NOW(),GNRE003_C007_Id_Alteracao=? WHERE GNRE003_Id=? AND GNRE003_GNRE001_Id=?");mysqli_stmt_bind_param($s,'isssssssssssssssiii',$values['document'],$values['receipt'],$values['detail'],$values['type'],$values['amount'],$values['document_type'],$values['product'],$values['period'],$values['month'],$values['year'],$values['installment'],$values['recipient_type'],$values['recipient_id'],$values['recipient_name'],$values['recipient_city'],$values['extras'],$context['user_id'],$itemId,$guideId);}else{$columns='GNRE003_GNRE001_Id,GNRE003_GNRE002_Id,GNRE003_Codigo_Receita,GNRE003_Detalhamento_Receita,GNRE003_Tipo_Valor,GNRE003_Valor,GNRE003_Tipo_Documento_Origem,GNRE003_Codigo_Produto,GNRE003_Referencia_Periodo,GNRE003_Referencia_Mes,GNRE003_Referencia_Ano,GNRE003_Referencia_Parcela,GNRE003_Destinatario_Tipo_Identificacao,GNRE003_Destinatario_Identificacao,GNRE003_Destinatario_Razao_Social,GNRE003_Destinatario_Codigo_Municipio,GNRE003_Campos_Extras_JSON,GNRE003_Data_Hora_Criacao,GNRE003_C007_Id_Criacao';$s=mysqli_prepare($db,"INSERT INTO GNRE003 ($columns) VALUES (?,?,?,?,NULLIF(?,''),?,?,NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NULLIF(?,''),NOW(),?)");mysqli_stmt_bind_param($s,'iisssssssssssssssi',$guideId,$values['document'],$values['receipt'],$values['detail'],$values['type'],$values['amount'],$values['document_type'],$values['product'],$values['period'],$values['month'],$values['year'],$values['installment'],$values['recipient_type'],$values['recipient_id'],$values['recipient_name'],$values['recipient_city'],$values['extras'],$context['user_id']);} $ok=$s&&mysqli_stmt_execute($s);if($s)mysqli_stmt_close($s);mysqli_close($db);if(!$ok)gnre_abort('Não foi possível gravar o item.',409);gnre_invalidate_local_xml($context,$guideId);gnre_back($guideId); }
        elseif($action==='delete_item') { $guide=gnre_draft($db,$guideId,$context); $item=(int)($_POST['item_id']??0); if(!$guide)gnre_abort('Rascunho inválido.',409); $s=mysqli_prepare($db,'DELETE FROM GNRE003 WHERE GNRE003_Id=? AND GNRE003_GNRE001_Id=?');mysqli_stmt_bind_param($s,'ii',$item,$guideId);$ok=mysqli_stmt_execute($s);mysqli_stmt_close($s);mysqli_close($db);if(!$ok)gnre_abort('Não foi possível remover.',409);gnre_invalidate_local_xml($context,$guideId);gnre_back($guideId); }
        elseif($action==='update') { $mode=(string)($_POST['mode']??'');$uf=strtoupper(gnre_field('favored_state',2)??'');$receipt=gnre_field('revenue_code',20);$detail=gnre_field('revenue_detail',50);$due=gnre_field('due_date',10);$amount=gnre_money((string)($_POST['amount']??''));$reference=gnre_field('reference',50);$notes=gnre_field('notes',1000);$submittedGuide=['GNRE001_Id'=>$guideId,'GNRE001_Modalidade'=>$mode,'GNRE001_UF_Favorecida'=>$uf,'GNRE001_Codigo_Receita'=>(string)$receipt,'GNRE001_Detalhamento_Receita'=>(string)$detail,'GNRE001_Data_Vencimento'=>(string)$due,'GNRE001_Valor'=>(string)($_POST['amount']??''),'GNRE001_Referencia'=>(string)$reference,'GNRE001_Observacao'=>(string)$notes,'GNRE001_Status'=>'R'];if(!in_array($mode,['INDIVIDUAL','APURACAO'],true)||!preg_match('/^[A-Z]{2}$/',$uf)||!$receipt||$detail===null||!preg_match('/^\d{4}-\d\d-\d\d$/',(string)$due)||!$amount||$reference===null||$notes===null)$formError='Revise os dados obrigatórios da guia e tente novamente.';if($formError!==''){mysqli_close($db);$_GET['edit']=$guideId;}else{
            if($action==='create'){$s=mysqli_prepare($db,'INSERT INTO GNRE001 (GNRE001_C004_Id,GNRE001_Modalidade,GNRE001_UF_Favorecida,GNRE001_Codigo_Receita,GNRE001_Detalhamento_Receita,GNRE001_Data_Vencimento,GNRE001_Valor,GNRE001_Referencia,GNRE001_Observacao,GNRE001_Data_Hora_Criacao,GNRE001_C007_Id_Criacao) VALUES (?,?,?,?,NULLIF(?,\'\'),?,?,NULLIF(?,\'\'),NULLIF(?,\'\'),NOW(),?)');mysqli_stmt_bind_param($s,'issssssssi',$context['company_id'],$mode,$uf,$receipt,$detail,$due,$amount,$reference,$notes,$context['user_id']);$ok=mysqli_stmt_execute($s);$guideId=(int)mysqli_insert_id($db);mysqli_stmt_close($s);}else{$guide=gnre_draft($db,$guideId,$context);if(!$guide)$ok=false;else{$s=mysqli_prepare($db,"UPDATE GNRE001 SET GNRE001_Modalidade=?,GNRE001_UF_Favorecida=?,GNRE001_Codigo_Receita=?,GNRE001_Detalhamento_Receita=NULLIF(?,''),GNRE001_Data_Vencimento=?,GNRE001_Valor=?,GNRE001_Referencia=NULLIF(?,''),GNRE001_Observacao=NULLIF(?,''),GNRE001_Data_Hora_Alteracao=NOW(),GNRE001_C007_Id_Alteracao=? WHERE GNRE001_Id=? AND GNRE001_C004_Id=?");mysqli_stmt_bind_param($s,'ssssssssiii',$mode,$uf,$receipt,$detail,$due,$amount,$reference,$notes,$context['user_id'],$guideId,$context['company_id']);$ok=mysqli_stmt_execute($s);mysqli_stmt_close($s);}}mysqli_close($db);if(!$ok){$formError='Não foi possível gravar a guia. Os dados foram mantidos para correção.';$_GET['view']='new';}else{gnre_clear_new_guide_state($context);if($action==='update')gnre_invalidate_local_xml($context,$guideId);gnre_back($guideId);} }}
        else { mysqli_close($db); gnre_abort('Ação inválida.',422); }
    }
}

$db=gnre_db();$retentionAvailable=GnreGuideRetention::available($db);$id=(int)($_GET['edit']??0);$guide=$id?gnre_get_guide($db,$id,$context['company_id']):null;if($id&&!$guide)gnre_abort('Guia não encontrada.',404);$temporaryXml=$_SESSION[GNRE_SESSION]['temporary']['xml_result'][(string)$context['company_id']][$id]??null;if($temporaryXml){$xmlResult=$temporaryXml['result']??null;/* XML local continua disponível para conferência até ser invalidado por alteração da guia. */}$docs=$guide?gnre_docs($db,$id):[];$items=$guide?gnre_items($db,$id):[];$attempts=$guide?gnre_attempts($db,$id):[];$resultQueues=$guide?gnre_result_queues($db,$id):[];$resultQueue=$guide?gnre_result_queue($db,$id):null;$storedResult=$resultQueue?GnreResultInterpreter::interpret((string)($resultQueue['GNRE005_Ultima_Consulta_SOAP']??'')):null;$officialPdf=null;if($guide){$s=mysqli_prepare($db,'SELECT GNRE008_Id,GNRE008_Data_Hora_Obtencao FROM GNRE008 WHERE GNRE008_C004_Id=? AND GNRE008_GNRE001_Id=? AND GNRE008_Tipo=\'PDF_OFICIAL\' AND GNRE008_Origem=\'PORTAL_GNRE\' ORDER BY GNRE008_Id DESC LIMIT 1');if($s){mysqli_stmt_bind_param($s,'ii',$context['company_id'],$id);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$officialPdf=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);}}$editing=null;foreach($items as $row)if((int)$row['GNRE003_Id']===(int)($_GET['item']??0))$editing=$row;mysqli_close($db);
$correctionSource=null;$correctionSourceResult=null;$correctionDraft=null; /* Compatibilidade: vínculo legado permanece no schema, sem uso no fluxo Task 23. */
$task23Flash=$_SESSION[GNRE_SESSION]['task23_flash']??null;unset($_SESSION[GNRE_SESSION]['task23_flash']);
$task25Flash=$_SESSION[GNRE_SESSION]['task25_flash']??null;unset($_SESSION[GNRE_SESSION]['task25_flash']);
$consultFlash=$guide?gnre_consult_flash_take($context,$id):null;
if($consultFlash)$consultFlash['outcome']=GnreManualConsultFeedback::persistedOutcome((string)($consultFlash['outcome']??''),$guide,$resultQueue,time());
$nav=(string)($_GET['view']??($guide?'details':'overview'));
$noteFilters=GnreNfEligibilityReader::filters($_GET);
if (!$guide && $nav === 'source_notes' && !array_key_exists('issued_from', $_GET) && !array_key_exists('issued_to', $_GET)) {
    $today=(new DateTimeImmutable('today', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
    $noteFilters=['issued_from'=>$today, 'issued_to'=>$today, 'note_number'=>'', 'recipient_uf'=>'', 'recipient_name'=>''];
}
$noteSearchRequested=$noteFilters['issued_from']!==''&&$noteFilters['issued_to']!=='';
$noteRows=[];
if(!$guide&&$nav==='source_notes'&&$noteSearchRequested){
    $noteDb=gnre_db();
    try {$noteRows=GnreNfEligibilityReader::load($noteDb,(int)$context['company_id'],$noteFilters['issued_from'],$noteFilters['issued_to'],$noteFilters);}
    finally {mysqli_close($noteDb);}
}
$noteDraftTypes=[];
if (!$guide && $nav==='source_notes' && $noteRows) {
    $typesDb=gnre_db();
    try { foreach ($noteRows as $note) { $uf=strtoupper(trim((string) ($note['T007_Cliente_Estado'] ?? ''))); if (!isset($noteDraftTypes[$uf])) $noteDraftTypes[$uf]=GnreNfDraftCreator::availableTypes($typesDb, $context, $uf); } }
    finally {mysqli_close($typesDb);}
}
$task60Flash=$_SESSION[GNRE_SESSION]['task60_flash']??null;
unset($_SESSION[GNRE_SESSION]['task60_flash']);
$task60Batch=(array)($_SESSION[GNRE_SESSION]['task60_batch'][(string)$context['company_id']]??[]);
$task60BatchResults=(array)($_SESSION[GNRE_SESSION]['task60_batch_results'][(string)$context['company_id']]??[]);
if ((string)($_GET['batch_result']??'')==='1') unset($_SESSION[GNRE_SESSION]['task60_batch_results'][(string)$context['company_id']]);
if (!$guide && ($nav!=='source_notes' || (string)($_GET['batch_plan']??'')!=='1')) unset($_SESSION[GNRE_SESSION]['task60_batch'][(string)$context['company_id']]);
if ((string)($_GET['batch_plan']??'')==='1' && $task60Batch && !empty($task60Batch['shown'])) { unset($_SESSION[GNRE_SESSION]['task60_batch'][(string)$context['company_id']]);$task60Batch=[];$_SESSION[GNRE_SESSION]['task60_flash']=['message'=>'A conferência anterior expirou. Selecione as notas novamente.','error'=>true]; }
elseif ((string)($_GET['batch_plan']??'')==='1' && $task60Batch) $_SESSION[GNRE_SESSION]['task60_batch'][(string)$context['company_id']]['shown']=true;
$environmentPreference = GnreEnvironmentPreference::HOMOLOGATION;
$environmentPreferenceFlash = $_SESSION[GNRE_SESSION]['environment_preference_flash'] ?? null;
unset($_SESSION[GNRE_SESSION]['environment_preference_flash']);
if (!$guide && $nav === 'config') {
    $preferenceDb = gnre_db();
    try { $environmentPreference = (new GnreEnvironmentPreference())->read($preferenceDb, (int) $context['company_id']); }
    finally { mysqli_close($preferenceDb); }
}
$guideFilters=GnreGuideCenter::filters($_GET);
if (!$guide && in_array($nav,['list','batch_transmit'],true) && !array_key_exists('created_date', $_GET)) {
    $today=(new DateTimeImmutable('today', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
    $guideFilters['created_date']=$today;
}
$guides=[];$filtered=[];if(!$guide){$listDb=gnre_db();$guides=GnreGuideCenter::load($listDb,(int)$context['company_id'],[],$retentionAvailable);$filtered=GnreGuideCenter::load($listDb,(int)$context['company_id'],$guideFilters,$retentionAvailable);mysqli_close($listDb);}
$batchFilters=GnreGuideCenter::filters($_GET);
if (!$guide && $nav==='batch_transmit' && !array_key_exists('created_date', $_GET)) $batchFilters['created_date']=(new DateTimeImmutable('today', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
$batchCandidates=[];
if(!$guide && $nav==='batch_transmit') { $batchDb=gnre_db(); try {$batchCandidates=GnreBatchTransmissionCandidates::load($batchDb,(int)$context['company_id'],$retentionAvailable,$batchFilters);} finally {mysqli_close($batchDb);} }
$realBatchQueueLots=[];$realBatchPilotAvailable=false;
/* Pilot audit is intentionally not loaded into the individual queue. */
$realBatchCohorts=[];
$realBatchPreparationFlash=$_SESSION[GNRE_SESSION]['real_batch_preparation_flash']??null;unset($_SESSION[GNRE_SESSION]['real_batch_preparation_flash']);
$realBatchCohortFlash=$_SESSION[GNRE_SESSION]['real_batch_cohort_flash']??null;unset($_SESSION[GNRE_SESSION]['real_batch_cohort_flash']);
$realBatchQueueFlash=$_SESSION[GNRE_SESSION]['real_batch_queue_flash']??null;unset($_SESSION[GNRE_SESSION]['real_batch_queue_flash']);
$counts=['RASCUNHO'=>0,'AGUARDANDO'=>0,'EMITIDA'=>0,'REJEITADA'=>0,'INCONCLUSIVA'=>0];foreach($guides as $g){$key=$g['_state']['key'];$counts[$key==='RECEPCAO_REJEITADA'?'INCONCLUSIVA':$key]++;}
function gnre_business_state(array $guide): string {
    $queue=$guide['_queue']??null;
    $result=$guide['_result']??($queue?GnreResultInterpreter::interpret((string)($queue['GNRE005_Ultima_Consulta_SOAP']??'')):null);
    return GnreGuideCenter::classify($guide,['result'=>$result,'queue'=>$queue,'attempts'=>$guide['_attempts']??[]])['key'];
}
$newGuideState=gnre_new_guide_state($context);$form=$guide?:($submittedGuide?:['GNRE001_Id'=>0,'GNRE001_Modalidade'=>'INDIVIDUAL','GNRE001_UF_Favorecida'=>(string)($newGuideState['uf']??''),'GNRE001_Codigo_Receita'=>'','GNRE001_Detalhamento_Receita'=>'','GNRE001_Data_Vencimento'=>'','GNRE001_Valor'=>'','GNRE001_Referencia'=>'','GNRE001_Observacao'=>'','GNRE001_Status'=>'R']);$item=$editing?:['GNRE003_Id'=>0,'GNRE003_GNRE002_Id'=>'','GNRE003_Codigo_Receita'=>$form['GNRE001_Codigo_Receita'],'GNRE003_Detalhamento_Receita'=>$form['GNRE001_Detalhamento_Receita'],'GNRE003_Tipo_Valor'=>'11','GNRE003_Valor'=>'','GNRE003_Tipo_Documento_Origem'=>'','GNRE003_Codigo_Produto'=>'','GNRE003_Referencia_Periodo'=>'','GNRE003_Referencia_Mes'=>'','GNRE003_Referencia_Ano'=>'','GNRE003_Referencia_Parcela'=>'','GNRE003_Destinatario_Tipo_Identificacao'=>'','GNRE003_Destinatario_Identificacao'=>'','GNRE003_Destinatario_Razao_Social'=>'','GNRE003_Destinatario_Codigo_Municipio'=>'','GNRE003_Campos_Extras_JSON'=>''];$activeConfig=null;$activeRules=$guide?gnre_current_rules($context,$guide):null;$activeReceipt=$activeRules?GnreConfigUfRules::receipt($activeRules,(string)$item['GNRE003_Codigo_Receita']):null;$emitterPreview=$activeRules?GnreCompanyEmitter::preview((new GnreCompanyEmitter())->load((int)$context['company_id'],$activeRules)):null;
    /* Compatibility UI may cache this request's resolved snapshot, but never selects it as truth. */
    if ($guide && $activeRules) {
        $companyKey = (string)$context['company_id'];
        $ufKey = (string)$guide['GNRE001_UF_Favorecida'];
        $_SESSION[GNRE_SESSION]['config_uf'][$companyKey][$ufKey] = ['rules'=>$activeRules,'snapshot_id'=>(int)($guide['GNRE001_GNRE007_Id']??0),'at'=>time()];
    }
    $form=$submittedGuide?:$form;
    require __DIR__ . '/task10-presentation.php';
