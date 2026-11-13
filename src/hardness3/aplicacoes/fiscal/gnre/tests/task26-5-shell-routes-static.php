<?php
declare(strict_types=1);
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
function t265(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
t265(strpos($page,"if(!\$guide&&\$nav==='new')")!==false,'Nova guia não compartilha o shell.');
t265(strpos($page,"elseif(!\$guide)")!==false,'Listagem/visão geral não compartilham o shell.');
t265(strpos($page,"data-gnre-sidebar")!==false&&strpos($page,"data-gnre-company-form")!==false,'Shell 26.2 ausente.');
t265(strpos($page,"if(\$guideTab==='dados')")!==false&&strpos($page,'guide-tabs')!==false,'Abas não estão limitadas à edição da guia.');
echo "Task 26.5 shell routes static: OK\n";
