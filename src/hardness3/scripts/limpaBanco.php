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
while ($table = mysql_fetch_row($res)) {
	$table = $table[0];
	if ($table == 'c004' || $table == 'c007') { continue; }
	$sql = "TRUNCATE {$table};";
	echo $sql."\n";
	$res2 = mysql_query($sql);
}
