<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');
require_once($home . 'bibliotecas/funcoesGerais.php');

$tmp = gPegaRelacionamento2(T007);
print_r($tmp);
die();


$sql = "SHOW TABLES;";
$res = mysql_query($sql);
$count = 0;
while ($table = mysql_fetch_row($res)) {
	$table = $table[0];
	$sql = "SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE table_schema = '{$confUsuario['dbDatabase']}' AND table_name = '{$table}';";
	$res2 = mysql_query($sql);
	while ($keys = mysql_fetch_assoc($res2)) {
		if (!empty($keys['REFERENCED_COLUMN_NAME'])) {
			echo $table . "\n";
			echo "\t{$keys['CONSTRAINT_NAME']}\n";
			$sql = "ALTER TABLE `{$keys['CONSTRAINT_SCHEMA']}`.`{$keys['TABLE_NAME']}` DROP FOREIGN KEY `{$keys['CONSTRAINT_NAME']}`;";
			echo "\t\t{$sql}\n";
			mysql_query($sql);
			$count++;
		}
	}
}
echo "\nTotal: {$count}\n";
