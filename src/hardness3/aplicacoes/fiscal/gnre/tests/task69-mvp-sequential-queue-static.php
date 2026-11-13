<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$ui=(string)file_get_contents($root.'/public/task63-batch.php');
$js=(string)file_get_contents($root.'/public/task63-batch.js');
function task69_assert(bool $ok,string $message):void { if(!$ok){fwrite(STDERR,"FAIL: $message\n");exit(1);} }
foreach(['Preparar lote real','Ver coortes locais','coorte','lote real'] as $text) task69_assert(stripos($ui,$text)===false,'HTML da Fila não deve expor: '.$text);
foreach(['data-task63-guide','data-task63-all','Marcar todas as listadas','data-task63-start','Abrir',"'uf'=>","'revenue'=>",'name="environment"','data-task63-confirm','data-task63-progress'] as $needle) task69_assert(strpos($ui,$needle)!==false,'Fila deve preservar: '.$needle);
task69_assert(strpos($ui,'Selecione as guias que deseja processar. Cada guia será validada e transmitida individualmente.')!==false,'texto público sequencial deve estar presente');
task69_assert(strpos($ui,'As guias serão processadas uma por vez. Cada transmissão terá seu próprio recibo e resultado.')!==false,'modal deve explicar resultado individual');
foreach(["post(id,'validate')","post(id,'transmit')","while(!stoppedNow())"] as $needle) task69_assert(strpos($js,$needle)!==false,'processamento sequencial deve preservar: '.$needle);
task69_assert(strpos($ui,'Interromper após a guia atual')!==false,'progresso deve permitir interromper somente itens não iniciados');
echo "OK Task 69 MVP sequential queue static checks\n";
