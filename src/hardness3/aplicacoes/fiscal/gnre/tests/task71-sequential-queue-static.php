<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$ui=(string)file_get_contents($root.'/public/task63-batch.php');
$js=(string)file_get_contents($root.'/public/task63-batch.js');
$index=(string)file_get_contents($root.'/public/index.php');
function task71_assert(bool $condition,string $message):void {if(!$condition){fwrite(STDERR,"FAIL: $message\n");exit(1);}}
foreach(['Processando ','Validar XML','Transmitir','Aguardar resultado','Consultar resultado','PDF','Interromper após a guia atual'] as $text)task71_assert(strpos($ui.$js,$text)!==false,'acompanhamento deve conter '.$text);
task71_assert(strpos($js,"var fresh=items.filter(function(x){return x.status==='new';})")!==false,'guias novas devem ter prioridade sobre pendentes');
task71_assert(strpos($js,"x.status==='pending'&&x.next<=Date.now()")!==false,'pendente deve respeitar o horário persistido');
task71_assert(strpos($js,"post(id,'validate')")!==false&&strpos($js,"post(id,'transmit')")!==false&&strpos($js,"post(id,'consult')")!==false,'ciclo deve reutilizar validação, transmissão e consulta individuais');
task71_assert(strpos($js,"post(id,'pdf')")!==false,'PDF deve continuar sendo obtido pela etapa individual idempotente');
task71_assert(strpos($js,'data-task63-summary')!==false&&strpos($ui,'data-task63-summary')!==false,'encerramento deve renderizar resumo visual consolidado');
task71_assert(strpos($js,"x.status==='pending'")!==false&&strpos($js,"x.outcome==='authorized'")!==false,'resumo deve separar pendentes dos resultados finais');
task71_assert(strpos($js,"pdf.ok&&pdf.status==='stored'&&Number(pdf.document_id)>0")!==false&&strpos($index,"document_id'=>(int)(\$result['document_id']??0)")!==false,'impressão só pode usar PDF armazenado e identificado na etapa da própria Fila');
task71_assert(strpos($js,'print_authorized_guides')!==false&&strpos($js,'printUrl(storedPdfs)')!==false,'impressão da Fila deve usar uma única rota consolidada');
task71_assert(strpos($js,'stoppedNow()')!==false&&strpos($js,'Nenhuma nova operação será criada.')!==false,'fechar ou interromper deve parar novas operações');
task71_assert(strpos($js,'task34-modal-state.php?guide_id=')!==false&&strpos($js,'state.queue&&state.queue.next')!==false,'orquestrador deve ler o horário persistido da guia');
task71_assert(strpos($index,'function gnre_batch_next_consultation')===false,'não deve criar uma segunda consulta SQL para a Fila');
task71_assert(stripos($ui.$js,'SOAP')===false&&stripos($ui.$js,'stack trace')===false,'modal não pode exibir termos técnicos');
echo "OK Task 71 sequential queue static checks\n";
