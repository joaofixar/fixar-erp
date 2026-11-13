<?php
declare(strict_types=1);
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
$controller=(string)file_get_contents(__DIR__.'/../public/index.php');
function t18xml(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
t18xml(strpos($page,'XML gerado e validado localmente com sucesso.')!==false,'sucesso XML não é exibido');
t18xml(strpos($page,'A geração local encontrou impedimentos:')!==false,'erros XML não são exibidos');
t18xml(strpos($page,'is_array($xmlResult)')!==false,'bloco XML vazio não é protegido');
t18xml(strpos($page,'data-generated-xml')!==false&&strpos($page,'gnre_h((string)($xmlResult')!==false,'XML não é escapado');
t18xml(strpos($page,'data-copy-generated-xml')!==false,'cópia segura do XML ausente');
t18xml(strpos($controller,"['temporary']['xml_result']")!==false,'resultado local não é guardado na sessão para o GET seguinte');
t18xml(strpos($controller,'gnre_back($guideId);')!==false,'geração local não usa redirect 303 para a guia');
t18xml(strpos($controller,"unset(\$_SESSION[GNRE_SESSION]['temporary']['xml_result']")!==false,'resultado local não é consumido após a exibição');
t18xml(strpos($page,'gnre_transmit_atomic(')===false,'apresentação local não pode criar tentativa');
echo "guide-draft xml presentation: OK\n";
