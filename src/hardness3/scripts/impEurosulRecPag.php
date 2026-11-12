<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');

echo "Recebidos...\n";
$sql = "SELECT * FROM T002";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	if (!empty($row['T002_Data_Recebimento']) && $row['T002_Data_Recebimento'] != '0000-00-00') {
		$sql = "INSERT INTO T019 (T019_D007_Id, T019_D024_Id, T019_D027_Id, T019_D014_Id, T019_T002_Id, T019_C004_Id, T019_Data_Lancamento, T019_Valor_Lancamento) VALUES (0, '{$row['T002_D024_Id']}', '{$row['T002_D027_Id']}', '{$row['T002_D014_Id']}', '{$row['T002_Id']}', '{$row['T002_C004_Id']}', '{$row['T002_Data_Recebimento']}', '{$row['T002_Valor_Recebido']}');";
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br />SQL: {$sql}"); }
	}
}

echo "Pagos...\n";
$sql = "SELECT * FROM T015";
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	if (!empty($row['T015_Data_Pagamento']) && $row['T015_Data_Pagamento'] != '0000-00-00') {
		$row['T015_Valor_Pago'] = -1 * $row['T015_Valor_Pago'];
		$sql = "INSERT INTO T019 (T019_D007_Id, T019_D024_Id, T019_D031_Id, T019_D014_Id, T019_T015_Id, T019_C004_Id, T019_Data_Lancamento, T019_Valor_Lancamento) VALUES (0, '{$row['T015_D024_Id']}', '{$row['T015_D031_Id']}', '{$row['T015_D014_Id']}', '{$row['T015_Id']}', '{$row['T015_C004_Id']}', '{$row['T015_Data_Pagamento']}', '{$row['T015_Valor_Pago']}');";
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br />SQL: {$sql}"); }
	}
}