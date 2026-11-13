<?php
declare(strict_types=1);
require_once __DIR__.'/../src/BatchTransmissionCandidates.php';
function task63_candidate_assert(bool $condition, string $message): void { if(!$condition){fwrite(STDERR,"FAIL: $message\n");exit(1);} }
$company=998;
$guide=['GNRE001_Id'=>95,'GNRE001_C004_Id'=>$company,'GNRE001_Status'=>'R','GNRE001_Arquivada'=>'N','GNRE001_GNRE007_Id'=>77,'GNRE001_UF_Favorecida'=>'PR'];
$snapshot=['GNRE007_Id'=>77,'GNRE007_C004_Id'=>$company,'GNRE007_UF'=>'PR','GNRE007_Ambiente'=>'2'];
task63_candidate_assert(GnreBatchTransmissionCandidates::eligible($guide,$snapshot,$company,true),'rascunho elegível deve ser listado mesmo sem XML, itens, receita, vencimento ou valor');
task63_candidate_assert(!GnreBatchTransmissionCandidates::eligible($guide,$snapshot,$company,true,1,0),'rascunho com tentativa deve ser excluído');
task63_candidate_assert(!GnreBatchTransmissionCandidates::eligible($guide,$snapshot,$company,true,0,1),'rascunho com fila deve ser excluído');
$other=$guide;$other['GNRE001_C004_Id']=999;task63_candidate_assert(!GnreBatchTransmissionCandidates::eligible($other,$snapshot,$company,true),'guia de outra empresa deve ser excluída');
$archived=$guide;$archived['GNRE001_Arquivada']='S';task63_candidate_assert(!GnreBatchTransmissionCandidates::eligible($archived,$snapshot,$company,true),'guia arquivada deve ser excluída');
$invalidEnvironment=$snapshot;$invalidEnvironment['GNRE007_Ambiente']='';task63_candidate_assert(!GnreBatchTransmissionCandidates::eligible($guide,$invalidEnvironment,$company,true),'snapshot sem ambiente válido deve ser excluído');
$technical='curl error: TLS certificate failed at https://portal.example.test/ HTTP 500';
$public=GnreBatchTransmissionCandidates::unexpectedFailureMessage(new RuntimeException($technical));
task63_candidate_assert($public==='Não foi possível processar esta guia agora. Consulte o Histórico e tente novamente quando apropriado.','exceção inesperada deve ter mensagem pública neutra');
foreach(['curl','TLS','https://','HTTP 500','certificate'] as $detail) task63_candidate_assert(stripos($public,$detail)===false,'mensagem pública não pode expor detalhe técnico: '.$detail);
task63_candidate_assert(GnreBatchTransmissionCandidates::dueLabel('2026-11-10','2026-11-11')==='10/11/2026','vencimento do cabeçalho deve prevalecer');
task63_candidate_assert(GnreBatchTransmissionCandidates::dueLabel('','2026-11-11')==='11/11/2026','vencimento único de item deve ser exibido');
task63_candidate_assert(GnreBatchTransmissionCandidates::dueLabel('','2026-11-11,2026-11-12')==='Múltiplos vencimentos','vencimentos distintos de itens devem ser resumidos');
task63_candidate_assert(GnreBatchTransmissionCandidates::dueLabel('','')==='Não informado','ausência de vencimento deve ser informada corretamente');
echo "Task 63 candidate behavior synthetic checks OK\n";
