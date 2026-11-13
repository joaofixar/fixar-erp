<?php
declare(strict_types=1);

$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
function t55(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }

/* Guia pendente: orientação honesta, sem alegar atualização automática. */
t55(strpos($page,'if($manualStatus===\'P\')')!==false&&strpos($page,'A consulta está em andamento. Aguarde alguns instantes e atualize a página.')!==false,'guia pendente não orienta atualização manual.');
/* Guia finalizada: F sozinho é neutro; sucesso exige resultado individual persistido 0. */
t55(strpos($page,"'F'=>'O Portal concluiu o processamento. Confira o resultado abaixo.'")!==false,'estado final não tem texto neutro.');
t55(strpos($page,'$manualFinalAuthorized=$manualStatus===\'F\'&&$pdfEligible')!==false,'autorização não continua condicionada ao resultado individual persistido.');
t55(strpos($page,'$authorizedProcessPanel')!==false&&strpos($page,'Guia autorizada com sucesso pelo Portal.')!==false&&strpos($page,'O PDF oficial está disponível para download.')!==false,'guia autorizada não recebe apresentação compacta com estado do PDF.');
t55(strpos($page,"if(\$authorizedProcessPanel!=='')return preg_replace")!==false,'guia autorizada ainda exibe controles redundantes de transmissão ou resultado.');
/* Guia rejeitada: a recepção, o retorno inconclusivo e os motivos persistidos continuam visíveis. */
foreach(['O Portal rejeitou a recepção da guia. Código','retorno inconclusivo','Motivos de rejeição:'] as $text)t55(strpos($page,$text)!==false,'feedback de rejeição ausente: '.$text);
/* Rejeição individual final elegível substitui somente a tela redundante por
   um bloco curto, baseado no resultado persistido e na decisão Task 23. */
foreach(['$canReopenRejectedGuide','$rejectedProcessPanel','Guia rejeitada pelo Portal','Esta guia não foi emitida/autorizada.','Motivo da rejeição:','Corrigir e reenviar','As tentativas e o Histórico serão preservados.'] as $text)t55(strpos($page,$text)!==false,'bloco principal de rejeição elegível ausente: '.$text);
t55(strpos($page,"if(\$rejectedProcessPanel!=='')return preg_replace")!==false,'rejeição elegível não substitui os controles redundantes de Processar.');
t55(strpos($page,"\$canReopenRejectedGuide=\$guideTab==='processar'")!==false,'guia autorizada, pendente ou inconclusiva pode receber bloco de rejeição indevidamente.');
/* Tentativa recebida com recibo e histórico sem dados opcionais. */
foreach(['Tentativa <?=gnre_h','Recibo</dt>','Retorno do Portal','Data e hora','Resultado individual</dt>','Motivo da rejeição</dt>','Guia autorizada/emitida pelo Portal.'] as $text)t55(strpos($page,$text)!==false,'estrutura pública do histórico ausente: '.$text);
t55(strpos($page,"<dt>Situação da transmissão</dt><dd>.*?</dd>")!==false,'Histórico ainda expõe situação da transmissão.');
t55(strpos($page,'$historyResultsBySequence')!==false&&strpos($page,'Motivo da rejeição</dt>')!==false,'Histórico não projeta motivos persistidos por tentativa.');
t55(strpos($page,'GnreHistoryResultProjection::byAttempt($resultQueues)')!==false&&strpos($page,'$attemptResultsById[(int)($attempt[\'GNRE004_Id\']??0)]')!==false,'Histórico não vincula cada resultado à GNRE004 correspondente.');
t55(strpos($page,'GNRE004_Resposta_SOAP')===false,'histórico expõe SOAP bruto.');
t55(strpos($page,'Histórico técnico resumido')===false,'nome técnico ainda é exibido na aba Histórico.');
echo "Task 55 process/history presentation static checks OK\n";
