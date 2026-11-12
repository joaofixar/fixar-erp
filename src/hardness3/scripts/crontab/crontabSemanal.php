<?php
namespace hardness;

// include('configuracoesCrontab.php');

mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Processos de Fechamento Semanal(Reprocessa histórico...)'",$g['conexaoBancoAuditoria']);
$idCrontabSemanal = mysql_insert_id($g['conexaoBancoAuditoria']);

/**
* Reprocessa histórico dos produtos
*/
mysqli_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Crontab', 'Reprocessamento do histórico dos produtos')",$g['conexaoBancoAuditoria']);
$idUltimoScript = mysql_insert_id($g['conexaoBancoAuditoria']);
$time = microtime(true);
echo "Executando Reprocessa historico ...\n";
include($local . 'scripts/crontab/scripts-produtosReprocessaHistorico.php');
echo "Fim scripts-produtosReprocessaHistorico: " . round(microtime(true) - $time) . "\n";
mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'",$g['conexaoBancoAuditoria']);


mysqli_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idCrontabSemanal}'",$g['conexaoBancoAuditoria']);

$h = fopen('/tmp/crontabTempo', 'a');
$time_end = microtime(true);
$time = round($time_end - $time_start);
fwrite($h, "{$dominio}: {$time} segundos\n");
fclose($h);
