<?php
declare(strict_types=1);
require_once __DIR__.'/../src/TransmissionOutcomePresentation.php';
function t75(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
$rejected=GnreTransmissionOutcomePresentation::fromAttempt(['GNRE004_Estado'=>'REJEITADA','GNRE004_Codigo_Retorno'=>'215','GNRE004_Descricao_Retorno'=>'Dados inválidos'],'R');
t75($rejected['state']==='reception_rejected'&&strpos($rejected['message'],'215 — Dados inválidos')!==false,'rejeição de recepção deve manter código e descrição públicos');
foreach ([['GNRE004_Estado'=>'INCONCLUSIVA'],['GNRE004_Estado'=>'CRIADA','GNRE004_Recibo'=>'']] as $attempt) {$outcome=GnreTransmissionOutcomePresentation::fromAttempt($attempt,'I');t75($outcome['state']==='technical_error','tentativa sem recibo deve ser técnica e terminal');t75(stripos($outcome['message'],'soap')===false&&stripos($outcome['message'],'curl')===false&&stripos($outcome['message'],'http')===false,'mensagem técnica não pode expor transporte');}
$index=(string)file_get_contents(__DIR__.'/../public/index.php');$modal=(string)file_get_contents(__DIR__.'/../public/task34-modal-state.php');$batch=(string)file_get_contents(__DIR__.'/../public/task63-batch.js');
t75(strpos($index,'catch (Throwable $e)')!==false,'exception da recepção deve ser convertida em desfecho persistível');t75(strpos($index,"gnre_transmission_outcome")!==false,'falha antes de finalizar tentativa deve sobreviver a PRG por empresa e guia');t75(strpos($modal,'TransmissionOutcomePresentation')!==false&&strpos($modal,"'reception_outcome'=>\$receptionOutcome")!==false,'modal deve ler classificação pública comum');t75(strpos($batch,"sent.state==='rejected'?'rejected'")!==false,'fila não pode mover rejeição para bloqueadas');t75(strpos($batch,"sent.state==='inconclusive'?'inconclusive':'technical'")!==false,'fila deve separar inconclusão e falha técnica');
echo "OK Task 75 transmission outcome presentation checks\n";
