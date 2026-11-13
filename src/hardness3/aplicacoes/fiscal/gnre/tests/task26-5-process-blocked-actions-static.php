<?php declare(strict_types=1);
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
foreach(['gnre-action-blocked','Consultar resultado','consultAction','if($consultAction[\'available\'])'] as $needle)if(strpos($page,$needle)===false)throw new RuntimeException('Ação bloqueada ausente: '.$needle);
if(strpos($page,'<form method="post" action="<?=gnre_h(gnre_guide_url($id,\'processar\'))?>" onsubmit="return confirm')===false)throw new RuntimeException('Ação ativa de transmissão foi removida.');
echo "task26.5 process blocked actions: OK\n";
