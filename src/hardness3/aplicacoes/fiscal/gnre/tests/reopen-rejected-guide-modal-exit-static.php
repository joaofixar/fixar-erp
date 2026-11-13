<?php
declare(strict_types=1);
$index=(string)file_get_contents(__DIR__.'/../public/index.php');
$start=strpos($index,"if (\$_SERVER['REQUEST_METHOD'] === 'POST' && (string)(\$_POST['action'] ?? '') === 'reopen_rejected_guide')");
$end=strpos($index,"if (\$_SERVER['REQUEST_METHOD'] === 'POST' && (string)(\$_POST['action']??'')==='delete_draft')",$start);
if($start===false||$end===false)throw new RuntimeException('Handler de correção não encontrado.');
$handler=substr($index,$start,$end-$start);
if(strpos($handler,"gnre_url('?edit='.\$sourceId.'&tab=dados')")===false||strpos($handler,'header(\'Location: \'.gnre_url')===false)throw new RuntimeException('Correção precisa sair do modal para Dados e itens.');
if(strpos($handler,"gnre_back(\$sourceId,'&tab=dados')")!==false)throw new RuntimeException('Correção não pode reabrir gnre34 após sucesso.');
if(strpos($handler,'GnreCorrectionReopen::reopen')===false||strpos($handler,'gnre_invalidate_local_xml')===false)throw new RuntimeException('Correção deve preservar a reabertura e invalidação local existentes.');
echo "Rejected-guide correction exits the historical modal check passed\n";
