<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');

$sql = "SHOW TABLES;";
$res = mysql_query($sql);
$count = 0;
$tabelas = array();
while ($table = mysql_fetch_row($res)) {
	$table = $table[0];
	$tabelas[] = $table;
}
shuffle($tabelas);
foreach ($tabelas as $table) {
	//echo $table . "\n";
	$resRows = mysql_query("SELECT * FROM {$table} ORDER BY {$table}_Id DESC;");
	while ($row = @mysql_fetch_assoc($resRows)) {
		// faz nada
	}
}