<?php
/**
* Servidor de LOG
*/
$home = __FILE__;
$home = str_replace('scripts/queueNF', '', dirname($home));
chdir($home);
require 'scripts/queueNF/conf.php';
require 'scripts/queueNF/codigos.php';

// Conexão com o banco
$con = @mysql_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);
$Error = mysql_error();
if (!empty($Error)) { die($Error); }
$dbs = mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
if ($usarUTF8) {
	mysql_set_charset('utf8');
	mysql_query('SET NAMES utf8;');
}

$r_db = mysql_real_escape_string($_REQUEST['db']);
$r_T007_TX_Mensagem = mysql_real_escape_string($_REQUEST['T007_TX_Mensagem']);
$r_T007_Id = mysql_real_escape_string($_REQUEST['T007_Id']);
$sql = "UPDATE `{$r_db}`.T007 SET T007_TX_Mensagem = '{$r_T007_TX_Mensagem}' WHERE T007_Id = '{$r_T007_Id}';";
mysql_query($sql);
