<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$presentation=(string)file_get_contents($root.'/public/task10-presentation.php');
$layout=(string)file_get_contents($root.'/public/task10-layout.css');
function t35header(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
t35header(substr_count($presentation,'guide-operational-header')===1,'A guia deve ter um único cabeçalho operacional.');
t35header(strpos($presentation,'</section><nav class="guide-tabs" aria-label="Seções da guia">')!==false,'As abas devem suceder imediatamente o cabeçalho operacional.');
t35header(strpos($layout,'.gnre-content>.guide-operational-header+.guide-tabs{margin-top:0}')!==false&&strpos($layout,'.gnre-content>.guide-tabs+*{margin-top:0!important}')!==false,'A âncora entre cabeçalho, abas e conteúdo deve ser contínua.');
t35header(strpos($layout,'.guide-tabs{position:sticky')===false&&strpos($layout,'.guide-operational-header{position:sticky')===false,'Abas e cabeçalho não podem ter sticky independente que desloque cada rota.');
t35header(strpos($layout,'.gnre-workspace{margin:0 auto}')!==false,'A área da guia não pode centralizar verticalmente conforme a altura da aba.');
t35header(strpos($layout,'@media(max-width:900px)')!==false&&strpos($layout,'@media(max-width:680px)')!==false&&strpos($layout,'@media(max-width:560px)')!==false,'Breakpoints desktop, tablet e mobile devem ser preservados.');
echo "Task 35 guide header static: OK\n";
