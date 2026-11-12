<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'bibliotecas/funcoesGerais.php');

$sql = "SHOW TABLES FROM `e237`";
$res = mysqli_query($sql);
$count = 0;
while ($table = mysql_fetch_row($res)) {
	$sqlDesc = "DESC `e237`.{$table[0]}";
	$resDesc = mysqli_query($sqlDesc);
	while ($rowDesc = mysql_fetch_assoc($resDesc)) {
		// Field, Type, Null, Key, Extra
		$sqlColumn = "SHOW COLUMNS FROM {$table[0]} LIKE '{$rowDesc['Field']}'";
		$resColumn = mysqli_query($sqlColumn);
		if (mysql_num_rows($resColumn) == 0) {
			echo "Criar_Campo(\"{$rowDesc['Field']}\",\"{$rowDesc['Type']}\");";
			if ($rowDesc['Null'] == 'NO') {
				echo " --------------------- NO\n";
			} else {
				echo "\n";
			}
		}
	}
}
