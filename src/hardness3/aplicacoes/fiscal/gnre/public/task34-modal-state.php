<?php
declare(strict_types=1);
/* Read model used by the guided modal.  It deliberately never calls Portal GNRE. */
require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/ResultInterpreter.php';
require_once __DIR__ . '/../src/QueueResultPresentation.php';
require_once __DIR__ . '/../src/TransmissionOutcomePresentation.php';
require_once __DIR__ . '/../src/ManualConsultFeedback.php';
require_once __DIR__ . '/../src/ConfigUfSnapshot.php';
require_once __DIR__ . '/../src/EnvironmentResolver.php';
$context=gnre_require_access();
$guideId=(int)($_GET['guide_id']??0);
header('Content-Type: application/json; charset=utf-8');
if($guideId<1){http_response_code(422);echo json_encode(['error'=>'Guia inválida.']);exit;}
$db=gnre_connect();if(!$db){http_response_code(503);echo json_encode(['error'=>'Base indisponível.']);exit;}
try {
    $s=mysqli_prepare($db,'SELECT GNRE001_Id,GNRE001_GNRE007_Id,GNRE001_Status,GNRE001_UF_Favorecida,GNRE001_Tipo_GNRE,GNRE001_Valor FROM GNRE001 WHERE GNRE001_Id=? AND GNRE001_C004_Id=?');mysqli_stmt_bind_param($s,'ii',$guideId,$context['company_id']);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$guide=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
    if(!$guide){http_response_code(404);echo json_encode(['error'=>'Guia não encontrada.']);exit;}
    $s=mysqli_prepare($db,"SELECT t.GNRE004_Id,t.GNRE004_Estado,t.GNRE004_Recibo,t.GNRE004_Codigo_Retorno,t.GNRE004_Descricao_Retorno,q.GNRE005_Situacao,q.GNRE005_Proxima_Tentativa,(q.GNRE005_Proxima_Tentativa<=NOW()) AS GNRE005_Due,GREATEST(0,TIMESTAMPDIFF(SECOND,NOW(),q.GNRE005_Proxima_Tentativa)) AS GNRE005_Seconds_Until,q.GNRE005_Erro_Tecnico,q.GNRE005_Ultima_Consulta_Descricao,q.GNRE005_Ultima_Consulta_SOAP FROM GNRE004 t LEFT JOIN GNRE005 q ON q.GNRE005_GNRE004_Id=t.GNRE004_Id WHERE t.GNRE004_GNRE001_Id=? ORDER BY t.GNRE004_Sequencia DESC,q.GNRE005_Id DESC LIMIT 1");mysqli_stmt_bind_param($s,'i',$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
    $receivedPendingAttempts=0;if($row&&in_array((string)($row['GNRE005_Situacao']??''),['PENDENTE','PROCESSANDO'],true)&&trim((string)($row['GNRE004_Recibo']??''))!==''){$s=mysqli_prepare($db,"SELECT COUNT(DISTINCT t.GNRE004_Id) AS total FROM GNRE004 t JOIN GNRE005 q ON q.GNRE005_GNRE004_Id=t.GNRE004_Id AND q.GNRE005_Recibo=t.GNRE004_Recibo WHERE t.GNRE004_GNRE001_Id=? AND t.GNRE004_Estado='RECEBIDA' AND t.GNRE004_Recibo=? AND q.GNRE005_Situacao IN ('PENDENTE','PROCESSANDO')");$receipt=(string)$row['GNRE004_Recibo'];mysqli_stmt_bind_param($s,'is',$guideId,$receipt);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$receivedPendingAttempts=(int)(mysqli_fetch_assoc($r)['total']??0);mysqli_stmt_close($s);}
    $s=mysqli_prepare($db,"SELECT GNRE008_Id FROM GNRE008 WHERE GNRE008_C004_Id=? AND GNRE008_GNRE001_Id=? AND GNRE008_Tipo='PDF_OFICIAL' AND GNRE008_Origem='PORTAL_GNRE' ORDER BY GNRE008_Id DESC LIMIT 1");mysqli_stmt_bind_param($s,'ii',$context['company_id'],$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$pdf=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);
    $parsed=$row?GnreResultInterpreter::interpret((string)($row['GNRE005_Ultima_Consulta_SOAP']??'')):null;
    $authorized=is_array($parsed)&&!empty($parsed['valid'])&&count((array)$parsed['guides'])===1&&(string)$parsed['guides'][0]['code']==='0';
    $publicResult=$row&&($row['GNRE005_Situacao']??'')==='PENDENTE'?GnreQueueResultPresentation::pending((string)($row['GNRE005_Ultima_Consulta_Descricao']??'')):GnreQueueResultPresentation::fromPersistedSoap((string)($row['GNRE005_Ultima_Consulta_SOAP']??''),(string)$guide['GNRE001_Status']==='F');
    $receptionOutcome=GnreTransmissionOutcomePresentation::fromAttempt($row,(string)$guide['GNRE001_Status']);
    $transientOutcome=$_SESSION[GNRE_SESSION]['temporary']['gnre_transmission_outcome'][(string)$context['company_id']][$guideId]??null;
    if($receptionOutcome['state']===''&&is_array($transientOutcome))$receptionOutcome=['state'=>'technical_error','message'=>GnreTransmissionOutcomePresentation::technicalMessage(),'code'=>'','description'=>''];
    /* A reception rejection remains visible in History. Once correction has
       explicitly reopened the same guide as R, it must not block a fresh,
       separately confirmed validation/transmission cycle. */
    $terminal=''; if($authorized)$terminal='authorized'; elseif($guide['GNRE001_Status']!=='R'&&$receptionOutcome['state']!=='')$terminal=$receptionOutcome['state']; elseif($guide['GNRE001_Status']!=='R'&&$row&&($row['GNRE005_Situacao']??'')==='BLOQUEADA')$terminal='blocked'; elseif($guide['GNRE001_Status']!=='R'&&$row&&trim((string)($row['GNRE005_Erro_Tecnico']??''))!=='')$terminal='technical_error'; elseif($guide['GNRE001_Status']==='F')$terminal='final_not_authorized';
    $xmlEntry=$_SESSION[GNRE_SESSION]['temporary']['gnre34_xml_result'][(string)$context['company_id']][$guideId]??null;$xml=(string)($xmlEntry['result']['xml']??'');$xmlValid=!empty($_SESSION[GNRE_SESSION]['temporary']['xml_valid'][(string)$context['company_id']][$guideId])&&$xml!=='';
    /* Erros da validação local são públicos para o usuário da guia. O estado
       continua estritamente de leitura: não cria tentativa nem chama o Portal. */
    $validationErrors=[];foreach((array)($xmlEntry['result']['errors']??[]) as $error){$error=trim((string)$error);if($error!=='')$validationErrors[]=$error;}
    $items=[];if($xmlValid){$s=mysqli_prepare($db,'SELECT GNRE003_Codigo_Receita,GNRE003_Detalhamento_Receita,GNRE003_Valor,GNRE003_Data_Vencimento,GNRE003_Referencia_Periodo,GNRE003_Referencia_Mes,GNRE003_Referencia_Ano FROM GNRE003 WHERE GNRE003_GNRE001_Id=? ORDER BY GNRE003_Id');mysqli_stmt_bind_param($s,'i',$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);while($r&&($item=mysqli_fetch_assoc($r))){$items[]=$item;}mysqli_stmt_close($s);}
    $snapshot=GnreConfigUfSnapshot::forGuide($db,$guide,$context);$environmentLabel=$snapshot?GnreEnvironmentResolver::label((string)$snapshot['GNRE007_Ambiente']):'Ambiente indisponível';
    $type=(string)$guide['GNRE001_Tipo_GNRE'];$typeLabels=['0'=>'GNRE Simples','1'=>'Múltiplos documentos de origem','2'=>'Múltiplas receitas'];
    $preview=$xmlValid?['guide_id'=>$guideId,'company'=>(string)$context['company_name'],'environment'=>$environmentLabel,'uf'=>$guide['GNRE001_UF_Favorecida'],'type'=>$type,'type_label'=>isset($typeLabels[$type])?$type.' — '.$typeLabels[$type]:($type!==''?$type:'Não informado'),'total'=>$guide['GNRE001_Valor'],'items'=>$items,'xml'=>$xml]:null;
    $cycle=$_SESSION[GNRE_SESSION]['temporary']['gnre34_cycle'][(string)$context['company_id']][$guideId]??null;
    $cycleActive=is_array($cycle)&&!empty($cycle['active'])&&(int)($cycle['guide_id']??0)===$guideId;
    $originTab=$cycleActive&&in_array((string)($cycle['origin_tab']??''),['dados','processar'],true)?$cycle['origin_tab']:'dados';
    $queueMessage='';if($row&&($row['GNRE005_Situacao']??'')==='PENDENTE'){$next=(string)$row['GNRE005_Proxima_Tentativa'];$when=GnreManualConsultFeedback::brazilianDateTime($next);$queueMessage=$publicResult['message'].' Próxima consulta permitida em '.$when.'.';}
    $resumablePending=(string)$guide['GNRE001_Status']==='P'&&$receivedPendingAttempts===1;
    echo json_encode(['server_now'=>date('Y-m-d H:i:s'),'guide_status'=>$guide['GNRE001_Status'],'resumable_pending'=>$resumablePending,'xml_valid'=>$xmlValid,'validation_errors'=>$validationErrors,'preview'=>$preview,'cycle'=>['active'=>$cycleActive,'origin_tab'=>$originTab],'attempt'=>$row?['state'=>$row['GNRE004_Estado'],'receipt'=>$row['GNRE004_Recibo'],'code'=>$row['GNRE004_Codigo_Retorno'],'description'=>$row['GNRE004_Descricao_Retorno']]:null,'queue'=>$row?['state'=>$row['GNRE005_Situacao'],'next'=>$row['GNRE005_Proxima_Tentativa'],'due'=>(bool)$row['GNRE005_Due'],'seconds_until'=>(int)$row['GNRE005_Seconds_Until'],'message'=>$queueMessage]:null,'result'=>$publicResult,'reception_outcome'=>$receptionOutcome,'authorized'=>$authorized,'pdf_id'=>(int)($pdf['GNRE008_Id']??0),'terminal'=>$terminal],JSON_UNESCAPED_UNICODE);
} finally {mysqli_close($db);}
