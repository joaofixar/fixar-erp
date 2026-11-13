<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$center=(string)file_get_contents($root.'/src/GuideCenter.php');
$batch=(string)file_get_contents($root.'/src/BatchTransmissionCandidates.php');
$listUi=(string)file_get_contents($root.'/public/task28-guide-center.php');
$batchUi=(string)file_get_contents($root.'/public/task63-batch.php');
$index=(string)file_get_contents($root.'/public/index.php');
foreach([$center,$batch] as $source)foreach(["'created_date'",'DATE(g.GNRE001_Data_Hora_Criacao)=?'] as $needle)if(strpos($source,$needle)===false)throw new RuntimeException('Filtro de criação ausente: '.$needle);
foreach([$listUi,$batchUi] as $source)if(strpos($source,"'created_date'=>['Data de criação','date'")===false)throw new RuntimeException('Campo Data de criação ausente na interface.');
foreach([$listUi,$batchUi] as $source)if(strpos($source,'<th>Data de criação</th>')===false||strpos($source,'GNRE001_Data_Hora_Criacao')===false)throw new RuntimeException('Coluna Data de criação ausente na listagem.');
if(strpos($index,"!array_key_exists('created_date', \$_GET)")===false||strpos($index,"['created_date']=\$today")===false)throw new RuntimeException('Data atual não é o padrão inicial das telas.');
echo "Creation-date filters checks passed\n";
