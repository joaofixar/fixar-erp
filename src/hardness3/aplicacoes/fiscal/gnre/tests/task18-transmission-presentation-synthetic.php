<?php
declare(strict_types=1);

$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
function t18transmit(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }

t18transmit(strpos($page,'data-gnre-transmission')!==false,'área de transmissão não está identificada');
t18transmit(strpos($page,"GNRE001_Status']==='R'")!==false,'estado R não condiciona a transmissão');
t18transmit(strpos($page,"GNRE001_GNRE007_Id")!==false,'snapshot vinculado não condiciona a transmissão');
t18transmit(strpos($page,'!empty($items)')!==false,'itens não condicionam a transmissão');
t18transmit(strpos($page,'$xmlValidated')!==false,'validação XML na sessão não condiciona a transmissão');
t18transmit(strpos($page,'name="action" value="transmit_homologation"')!==false,'ação existente de transmissão não é acionada');
t18transmit(strpos($page,"confirm('Transmitir esta guia em homologação?')")!==false,'confirmação no navegador ausente');
t18transmit(strpos($page,'Consultar resultado')!==false,'consulta de resultado não é apresentada');
t18transmit(strpos($page,'class="gnre-panel gnre-history"')!==false,'histórico não é apresentado em estrutura própria');
t18transmit(strpos($page,'Resultado individual')!==false,'resultado individual não é apresentado');
echo "guide-draft transmission presentation: OK\n";
