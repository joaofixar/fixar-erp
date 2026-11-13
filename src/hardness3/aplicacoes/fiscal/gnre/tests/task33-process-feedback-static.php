<?php
declare(strict_types=1);
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
$worker=(string)file_get_contents(__DIR__.'/../src/QueueWorker.php');
foreach(['XML gerado e validado localmente com sucesso.','A geração local encontrou impedimentos:','data-generated-xml','Transmissão aceita pelo Portal. Recibo:','retorno inconclusivo','Nenhuma retransmissão automática','confirm(\'Transmitir esta guia em homologação?\')'] as $text)if(strpos($page,$text)===false)throw new RuntimeException('Feedback ausente: '.$text);
foreach(['Motivos de rejeição:','Campo:','resultGuide[\'rejections\']'] as $text)if(strpos($page,$text)===false)throw new RuntimeException('Motivo de rejeição persistido ausente: '.$text);
if(strpos($page,'value="<?=gnre_h($csrf)?>"><input')===false||strpos($page,"Normaliza atributos de CSRF")!==false)throw new RuntimeException('CSRF não é HTML direto.');
if(strpos($worker,'ORDER BY q.GNRE005_Id DESC LIMIT 1 FOR UPDATE')===false)throw new RuntimeException('Consulta manual não seleciona a fila mais recente.');
echo "Task 33 process feedback static checks OK\n";
