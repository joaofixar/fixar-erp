<?php
declare(strict_types=1);
require_once __DIR__.'/../src/PendingResultPolicy.php';
require_once __DIR__.'/../src/QueueResultPresentation.php';
require_once __DIR__.'/../src/ResultClient.php';
require_once __DIR__.'/../src/PendingResultRegressionRepair.php';

function t74(bool $ok,string $message):void { if(!$ok) throw new RuntimeException('Task 74: '.$message); }
function t74soap(string $process,string $guides=''):string { return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><situacaoProcess><codigo>'.$process.'</codigo><descricao>O lote possui pendência de tempo de processamento.</descricao></situacaoProcess>'.$guides.'</TResultLote_GNRE></soap:Body></soap:Envelope>'; }

t74(GnrePendingResultPolicy::nextDelaySeconds(null,1)===3,'primeira pendência sem estimativa deve aguardar três segundos');
t74(GnrePendingResultPolicy::nextDelaySeconds(null,2)===3&&GnrePendingResultPolicy::nextDelaySeconds(null,99)===3,'sem estimativa, a retomada rápida deve permanecer em três segundos');
t74(GnrePendingResultPolicy::nextDelaySeconds(1200000,1)===1200,'estimativa oficial válida deve orientar a próxima consulta');
t74(GnrePendingResultPolicy::nextDelaySeconds(1,1)===3&&GnrePendingResultPolicy::nextDelaySeconds(999999999,1)===3,'estimativa inválida deve cair na retomada rápida');

$client=new GnreResultClient();$parse=new ReflectionMethod(GnreResultClient::class,'parse');$parse->setAccessible(true);
$pending=$parse->invoke($client,t74soap('403','<resultado><guia><situacaoGuia>4</situacaoGuia></guia></resultado>'),200);
t74($pending['kind']==='pending'&&$pending['code']==='403','403 com situacaoGuia=4 deve permanecer pendente');
foreach(['400','401','403'] as $code){$withoutGuide=$parse->invoke($client,t74soap($code),200);t74($withoutGuide['kind']==='pending',$code.' sem resultado individual deve permanecer pendente oficial');}
$unknown=$parse->invoke($client,t74soap('999'),200);t74($unknown['kind']==='pending','código desconhecido sem resultado individual deve permanecer inconclusivo pendente, nunca final');
foreach(['0','1','2'] as $guideCode){$final=$parse->invoke($client,t74soap('402','<resultado><guia><situacaoGuia>'.$guideCode.'</situacaoGuia></guia></resultado>'),200);t74($final['kind']==='final','402 com situacaoGuia='.$guideCode.' deve finalizar somente pela evidência individual');}
$multiple=$parse->invoke($client,t74soap('402','<resultado><guia><situacaoGuia>0</situacaoGuia></guia><guia><situacaoGuia>0</situacaoGuia></guia></resultado>'),200);t74($multiple['kind']==='pending','múltiplos resultados não podem finalizar');
$invalid=$parse->invoke($client,'<soap>',200);t74($invalid['kind']==='retry','SOAP inválido deve manter retry técnico distinto de pendência');
$processing=new ReflectionMethod(GnreResultClient::class,'processing');$processing->setAccessible(true);
foreach([400,401] as $http){$response=$processing->invoke($client,$http,'<html>processando</html>');t74($response['kind']==='pending'&&$response['http']===$http,'HTTP '.$http.' após recibo deve manter acompanhamento pendente');}

$public=GnreQueueResultPresentation::pending('O Portal informou prazo médio de 20 minutos.');
t74($public['state']==='pending'&&$public['message']===GnrePendingResultPolicy::publicMessage(),'pendência deve ter orientação pública única');
t74($public['description']==='O Portal informou prazo médio de 20 minutos.','descrição pública persistida deve ser separada do SOAP');
$worker=(string)file_get_contents(__DIR__.'/../src/QueueWorker.php');
t74(strpos($worker,'GNRE004_Tempo_Estimado_Processamento_MS')!==false&&strpos($worker,'GNRE005_Situacao=\'PENDENTE\'')!==false,'worker deve persistir a mesma fila pendente usando a estimativa da recepção');
t74(strpos($worker,'hasConclusiveIndividualResult')!==false&&strpos($worker,'$result[\'kind\']===\'final\'&&self::hasConclusiveIndividualResult')!==false,'worker deve defender contra finalização sem evidência individual conclusiva');
t74(strpos($worker,'INSERT INTO GNRE004')===false&&strpos($worker,'transmit')===false,'pendência não pode criar tentativa nem retransmitir');
$js=(string)file_get_contents(__DIR__.'/../public/task63-batch.js');
t74(strpos($js,"var fresh=items.filter(function(x){return x.status==='new';});if(fresh.length)return fresh[0];")!==false,'Fila deve priorizar a próxima guia ainda não iniciada');
t74(strpos($js,'waitForPersistedTime')!==false&&strpos($js,'state.queue&&state.queue.next')!==false,'Fila deve retomar somente no horário persistido');
$affected=['GNRE004_Estado'=>'RECEBIDA','GNRE001_Status'=>'F','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>t74soap('401')];
t74(GnrePendingResultRegressionRepair::affected($affected),'reparo deve identificar 401 sem guia finalizado indevidamente');
t74(!GnrePendingResultRegressionRepair::affected(array_merge($affected,['GNRE005_Ultima_Consulta_SOAP'=>t74soap('401','<resultado><guia><situacaoGuia>0</situacaoGuia></guia></resultado>')] )),'reparo não pode alcançar autorização individual');
t74(GnrePendingResultRegressionRepair::restoredState()===['GNRE001_Status'=>'P','GNRE005_Situacao'=>'PENDENTE'],'reparo sintético deve restaurar somente P/PENDENTE');
$repairSql=(string)file_get_contents(__DIR__.'/../scripts/reparar-pendencias-task74.sql.example');
t74(strpos($repairSql,'NÃO EXECUTAR AUTOMATICAMENTE')!==false&&strpos($repairSql,'<IDS_CONFIRMADOS>')!==false&&strpos($repairSql,'START TRANSACTION')!==false,'reparo manual deve exigir inspeção e transação explícitas');
echo "OK Task 74 conservative pending result checks\n";
