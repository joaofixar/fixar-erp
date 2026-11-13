<?php
declare(strict_types=1);

function t49(bool $condition, string $message): void { if (!$condition) throw new RuntimeException('Task 49: '.$message); }

$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/guide-draft-flow.php');
$controller=(string)file_get_contents($root.'/src/GuideDraftController.php');
$client=(string)file_get_contents($root.'/src/ConfigUfClient.php');
$newView=(string)strstr($flow,'<?php endif; if($guide):',true);

foreach(['Criar nova guia','Selecione a UF favorecida para iniciar.','UF favorecida','Selecione a UF','>Continuar<','Configurações para <?=gnre_h($uf)?>','Carregar configurações da UF','Buscar configurações no Portal','Alterar UF','Configurações para <?=gnre_h($uf)?> carregadas','Escolha o tipo de guia que deseja criar.','Tipo de guia','>Criar guia<','Certificado digital indisponível','Acesso ao Portal GNRE não autorizado','Não foi possível consultar as configurações da UF'] as $text) t49(strpos($flow,$text)!==false,'texto ausente: '.$text);
foreach(['1. UF · 2. Configuração oficial','Snapshot local','Tipo GNRE','Trocar UF','Nova guia GNRE','NF-e'] as $text) t49(stripos($flow,$text)===false,'texto público obsoleto presente: '.$text);
t49(stripos($flow,'Snapshot local')===false&&stripos($flow,'snapshot selecionado')===false,'a tela Nova guia não pode expor snapshot ao usuário.');
t49(strpos($flow,'name="action" value="guide_draft_choose_uf"')!==false&&strpos($flow,'name="action" value="guide_draft_consult"')!==false&&strpos($flow,'name="action" value="guide_draft_create"')!==false,'actions existentes devem permanecer.');
t49(substr_count($flow,'name="csrf"')>=5,'formulários existentes devem preservar CSRF.');
t49(strpos($flow,'name="favored_state"')!==false&&strpos($flow,'name="snapshot_id"')!==false&&strpos($flow,'name="gnre_type"')!==false,'campos do fluxo devem permanecer.');
t49(strpos($controller,"if(\$action==='guide_draft_restart'){unset(\$_SESSION[GNRE_SESSION]['temporary']['guide_draft'][\$company])")!==false,'reinício deve apenas limpar o estado transitório da nova guia.');
t49(strpos($controller,'Os dados da nova guia foram descartados. Selecione a UF para começar novamente.')!==false,'mensagem de reinício deve explicar o efeito real.');
t49(strpos($newView,"['guide_draft_choose_uf','guide_draft_consult','guide_draft_choose_snapshot','guide_draft_restart','guide_draft_create']")!==false&&strpos($newView,'guide_draft_save_item')===false,'flash de item não pode aparecer em Nova guia.');
t49(strpos($client,'GnreConfigUfConsultException')!==false&&strpos($client,'CERTIFICATE_UNAVAILABLE')!==false&&strpos($client,'PORTAL_ACCESS_DENIED')!==false&&strpos($client,'isExplicitAccessDenied')!==false,'cliente deve estruturar somente certificado e recusa explícita do Portal.');
require_once $root.'/src/ConfigUfClient.php';
require_once $root.'/src/GuideDraftController.php';
$clientInstance=new GnreConfigUfClient();$nonSuccess=new ReflectionMethod(GnreConfigUfClient::class,'nonSuccessResponseException');$nonSuccess->setAccessible(true);
$faultDenied='<?xml version="1.0"?><soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Body><soap:Fault><soap:Reason><soap:Text>Acesso ao Web Service negado</soap:Text></soap:Reason></soap:Fault></soap:Body></soap:Envelope>';
$faultInconclusive='<?xml version="1.0"?><soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"><soap:Body><soap:Fault><soap:Reason><soap:Text>Falha temporária</soap:Text></soap:Reason></soap:Fault></soap:Body></soap:Envelope>';
foreach([500,403] as $status){$failure=$nonSuccess->invoke($clientInstance,$status,$faultDenied);t49($failure instanceof GnreConfigUfConsultException&&$failure->publicCategory()===GnreConfigUfConsultException::PORTAL_ACCESS_DENIED,'SOAP Fault explícito em HTTP não-2xx deve indicar acesso não autorizado.');}
foreach([$faultInconclusive,'','<html><body>negado</body></html>','<soap:Envelope>incompleto'] as $body){$failure=$nonSuccess->invoke($clientInstance,500,$body);t49($failure instanceof GnreConfigUfConsultException&&$failure->publicCategory()===GnreConfigUfConsultException::INCONCLUSIVE,'corpo não conclusivo em HTTP não-2xx deve permanecer inconclusivo.');}
$certificate=gnre_guide_draft_public_error(new GnreConfigUfConsultException(GnreConfigUfConsultException::CERTIFICATE_UNAVAILABLE,'interno'),0,0,'guide_draft_consult');
$denied=gnre_guide_draft_public_error(new GnreConfigUfConsultException(GnreConfigUfConsultException::PORTAL_ACCESS_DENIED,'interno'),0,0,'guide_draft_consult');
$inconclusive=gnre_guide_draft_public_error(new RuntimeException('SOAP HTTP senha token interno'),0,0,'guide_draft_consult');
t49(strpos($certificate,'certificado digital da empresa não está disponível')!==false&&strpos($certificate,'Nenhuma guia foi criada.')!==false,'falha de certificado deve ter categoria pública própria.');
t49(strpos($denied,'não autorizou esta empresa')!==false&&strpos($denied,'Nenhuma guia foi criada.')!==false,'recusa explícita deve ter categoria pública própria.');
t49(strpos($inconclusive,'Não foi possível acessar o Portal GNRE neste momento.')!==false&&strpos($inconclusive,'SOAP')===false&&strpos($inconclusive,'senha')===false&&strpos($inconclusive,'token')===false,'retorno inconclusivo deve ser neutro e não expor detalhes.');
t49(strpos(strstr($controller,"if(\$action==='guide_draft_consult')",true),'INSERT INTO GNRE001')===false,'falha de consulta não pode criar GNRE001 antes do retorno.');
t49(strpos($controller,'new GnreReceptionClient')===false&&strpos($controller,'new GnreResultClient')===false&&strpos($controller,'GnreOfficialPdfService')===false,'fluxo Nova guia não pode acionar transmissão, resultado ou PDF.');
echo "Task 49 new-guide copy static: OK\n";
