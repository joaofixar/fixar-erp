<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ManualConsultFeedback.php';
$guide=['GNRE001_Status'=>'P'];
foreach(['pending','final','retry','processing','rescheduled','no_receipt','blocked','not_found','exception'] as $outcome){$message=GnreManualConsultFeedback::message($outcome);if($message===''||stripos($message,'SOAP')!==false)throw new RuntimeException('Mensagem pública inválida: '.$outcome);}
if(GnreManualConsultFeedback::persistedOutcome('final',['GNRE001_Status'=>'P'],['GNRE005_Situacao'=>'PENDENTE','GNRE005_Proxima_Tentativa'=>'2000-01-01 00:00:00'],time())!=='pending')throw new RuntimeException('Flash final não pode contradizer a pendência persistida.');
if(GnreManualConsultFeedback::persistedOutcome('pending',['GNRE001_Status'=>'F'],['GNRE005_Situacao'=>'FINALIZADA'],time())!=='final')throw new RuntimeException('Final persistido deve prevalecer sobre o flash.');
if(!GnreManualConsultFeedback::action($guide,['GNRE005_Situacao'=>'PENDENTE','GNRE005_Proxima_Tentativa'=>'2000-01-01 00:00:00'],time())['available'])throw new RuntimeException('Pendente elegível deveria habilitar a ação.');
foreach(['PROCESSANDO','BLOQUEADA','FINALIZADA'] as $state)if(GnreManualConsultFeedback::action($guide,['GNRE005_Situacao'=>$state],time())['available'])throw new RuntimeException('Estado '.$state.' não pode habilitar a ação.');
if(GnreManualConsultFeedback::action($guide,['GNRE005_Situacao'=>'PENDENTE','GNRE005_Proxima_Tentativa'=>'2999-01-01 00:00:00'],time())['available'])throw new RuntimeException('Fila reagendada não pode habilitar a ação.');
if(GnreManualConsultFeedback::action(['GNRE001_Status'=>'F'],null,time())['available'])throw new RuntimeException('Resultado final não pode habilitar a ação.');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
foreach(['consult_flash','[(string)$context[\'company_id\']][(string)$guideId]','unset($_SESSION[GNRE_SESSION][\'consult_flash\'][$company][$key])'] as $needle)if(strpos($index,$needle)===false)throw new RuntimeException('Flash não está isolado/consumido: '.$needle);
if(strpos($page,'<button type="button" class="gnre-action-blocked" disabled title="<?=gnre_h($consultAction[\'reason\'])?>"')===false)throw new RuntimeException('Controle bloqueado não é condicional.');
$consultBlock=substr($index,(int)strpos($index,"elseif(\$action==='consult_result')"),(int)(strpos($index,"elseif(\$action==='obtain_official_pdf')")-strpos($index,"elseif(\$action==='consult_result')")));if(strpos($consultBlock,'GnreOfficialPdfService')!==false)throw new RuntimeException('Consulta manual não pode obter PDF automaticamente.');
echo "Task 07 consult feedback synthetic checks OK\n";
