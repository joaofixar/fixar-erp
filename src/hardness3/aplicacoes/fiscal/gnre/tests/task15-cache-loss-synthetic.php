<?php
declare(strict_types=1);
function t15cache(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/task15-consultation.php');
$presentation=(string)file_get_contents($root.'/public/task10-presentation.php');
$index=(string)file_get_contents($root.'/public/index.php');
t15cache(strpos($flow,'!$guide')!==false&&strpos($flow,'$activeRules')!==false,'consulta sem cache não é protegida.');
t15cache(strpos($flow,'$docs')!==false&&strpos($flow,'$items')!==false&&strpos($flow,'GNRE006')!==false,'dados persistidos não são exibidos sem cache.');
t15cache(strpos($flow,'Configuração da UF não está carregada para editar campos dependentes.')!==false,'mensagem de consulta ausente.');
t15cache(strpos($flow,'reload_guide_config')!==false,'recarregamento manual ausente.');
t15cache(strpos($presentation,'task15-consultation.php')!==false,'consulta não foi integrada à página.');
t15cache(strpos($presentation,'if(!$activeRules)')!==false&&strpos($presentation,'Gerar e validar XML')!==false,'XML não é bloqueado visualmente sem regras.');
t15cache(strpos($index,'reload_guide_config')!==false&&strpos($index,'GnreConfigUfClient())->consult')!==false,'recarregamento manual não usa cliente oficial.');
echo "task15 cache-loss synthetic: OK\n";
