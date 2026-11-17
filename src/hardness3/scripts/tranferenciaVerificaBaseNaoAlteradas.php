<?php
namespace hardness;
die("Não está mais em uso.");

$confUsuarioERP = array(
	'dbDatabase' => 'global',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => '$$hard@@0982',
	//'dbPass' => 'hardness',
);
$conERP=@mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

$confUsuarioNOTA = array(
	'dbDatabase' => 'global',
	'dbHost' => '186.202.64.122',
	'dbUser' => 'root',
	'dbPass' => '!hard@3871',
);
$conNOTA=mysql_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioNOTA['dbDatabase'], $conNOTA) or die (mysql_error());
mysql_set_charset('utf8', $conNOTA);
mysql_query('SET NAMES utf8;', $conNOTA);
mysql_query("USE e001", $conNOTA);

$sql = "SELECT * FROM C025 WHERE C025_Flag_Transferido = 1 ORDER BY C025_Banco_Dados ASC";
$res = mysql_query($sql, $conNOTA);
while ($row = mysql_fetch_assoc($res)) {
	// 
	$sql = "SELECT COUNT(*) as Linhas FROM `{$row['C025_Banco_Dados']}`.T007";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['Linhas'] != $rowNOTA['Linhas']) { continue; }
	// 
	$sql = "SELECT T007_Id FROM `{$row['C025_Banco_Dados']}`.T007 ORDER BY T007_Id DESC LIMIT 1";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['T007_Id'] != $rowNOTA['T007_Id']) { continue; }
	// 
	$sql = "SELECT COUNT(*) as Linhas FROM `{$row['C025_Banco_Dados']}`.T008";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['Linhas'] != $rowNOTA['Linhas']) { continue; }
	// 
	$sql = "SELECT T008_Id FROM `{$row['C025_Banco_Dados']}`.T008 ORDER BY T008_Id DESC LIMIT 1";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['T008_Id'] != $rowNOTA['T008_Id']) { continue; }
	// 
	$sql = "SELECT COUNT(*) as Linhas FROM `{$row['C025_Banco_Dados']}`.D024";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['Linhas'] != $rowNOTA['Linhas']) { continue; }
	// 
	$sql = "SELECT D024_Id FROM `{$row['C025_Banco_Dados']}`.D024 ORDER BY D024_Id DESC LIMIT 1";
	$resERP = mysql_query($sql, $conERP);
	$rowERP = mysql_fetch_assoc($resERP);
	$resNOTA = mysql_query($sql, $conNOTA);
	$rowNOTA = mysql_fetch_assoc($resNOTA);
	if ($rowERP['D024_Id'] != $rowNOTA['D024_Id']) { continue; }
	// 
	echo "{$row['C025_Banco_Dados']}\n";
	$cmd = "chmod 000 /var/www/sites/{$row['C025_Dominio']}.hardness.com.br-*";
	//echo $cmd . "\n";
	shell_exec($cmd);
	$sql = "UPDATE C025 SET C025_Flag_Transferido = 0 WHERE C025_Id = '{$row['C025_Id']}'";
	//echo $sql . "\n";
	mysql_query($sql, $conNOTA);
}
