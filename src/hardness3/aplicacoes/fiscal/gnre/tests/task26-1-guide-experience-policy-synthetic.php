<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GuideExperiencePolicy.php';
function t261(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
function p261(array $guide=[],array $facts=[]):array { return GnreGuideExperiencePolicy::decide(array_merge(['GNRE001_Status'=>'R','GNRE001_GNRE007_Id'=>8,'GNRE001_Tipo_GNRE'=>'0'],$guide),$facts); }
function result261(string $code):array { return ['valid'=>true,'guides'=>[['code'=>$code]]]; }

$x=p261(); t261($x['state']==='draft'&&!$x['can_generate_xml']&&$x['can_edit_data'],'rascunho com snapshot sem item permanece editável e não gera XML.');
$x=p261([],['item_count'=>1]); t261($x['can_generate_xml']&&!$x['can_transmit'],'rascunho com item pode gerar XML, mas não transmitir sem validação local.');
$x=p261([],['item_count'=>1,'xml_valid_local'=>true]); t261($x['can_transmit'],'rascunho com XML local válido pode transmitir.');
$x=p261(['GNRE001_Status'=>'P'],['attempts'=>[['GNRE004_Estado'=>'RECEBIDA']]]); t261($x['state']==='awaiting_result'&&$x['can_consult_result']&&!$x['can_edit_data'],'recepção recebida aguarda resultado e protege contra edição/reenvio.');
$x=p261(['GNRE001_Status'=>'R'],['attempts'=>[['GNRE004_Estado'=>'REJEITADA']]]); t261($x['state']==='reception_rejected'&&!$x['can_consult_result'],'rejeição na recepção sem recibo é distinta de resultado individual.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('0')]); t261($x['state']==='approved'&&$x['can_obtain_official_pdf'],'situação individual 0 persistida aprova e permite obter PDF.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('1')]); t261($x['state']==='rejected'&&$x['can_reopen_rejected_guide']&&!$x['can_start_correction'],'resultado individual 1 isolado rejeita e permite reabrir a própria guia.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('3')]); t261($x['state']==='inconclusive'&&!$x['can_transmit'],'resultado inconclusivo não libera retransmissão.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('2')]); t261($x['primary_action']==='reopen_rejected_guide'&&$x['can_reopen_rejected_guide'],'rejeitada isolada reabre a própria guia.');
$x=p261(['GNRE001_Status'=>'R'],['result'=>result261('2'),'item_count'=>1,'xml_valid_local'=>true]); t261($x['state']==='draft'&&$x['can_edit_data']&&$x['can_transmit'],'rascunho reaberto com rejeição isolada permite novo XML e transmissão explícita.');
$x=p261(['GNRE001_Status'=>'R'],['result'=>result261('0')]); t261($x['state']==='approved'&&!$x['can_edit_data'],'resultado 0 prevalece mesmo se o estado técnico informado for R.');
$x=p261(['GNRE001_Status'=>'R'],['result'=>['valid'=>true,'guides'=>[['code'=>'1'],['code'=>'2']]]]); t261($x['state']==='inconclusive'&&!$x['can_edit_data'],'múltiplos resultados rejeitados não liberam rascunho.');
$x=p261(['GNRE001_GNRE007_Id'=>0],['item_count'=>1,'xml_valid_local'=>true]); t261($x['state']==='legacy_blocked'&&!$x['can_transmit'],'guia sem snapshot compatível não ganha ação indevida.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('0')]); t261($x['can_obtain_official_pdf']&&!$x['can_download_official_pdf'],'PDF ausente pode ser obtido, mas não baixado.');
$x=p261(['GNRE001_Status'=>'F'],['result'=>result261('0'),'official_pdf_id'=>3]); t261(!$x['can_obtain_official_pdf']&&$x['can_download_official_pdf'],'PDF persistido pode ser baixado.');
$x=p261([],['company_authorized'=>false]); t261($x['state']==='inaccessible'&&$x['allowed_actions']===[],'guia de outra empresa não é acessível.');
$x=p261(['GNRE001_Status'=>'F']); t261($x['state']!=='approved','F isoladamente nunca significa aprovação.');
t261(isset(GnreGuideExperiencePolicy::routeContracts()['technical_history']),'contratos conceituais de rota existem.');
echo "OK Task 26.1: política de estados, abas e ações passou.\n";
