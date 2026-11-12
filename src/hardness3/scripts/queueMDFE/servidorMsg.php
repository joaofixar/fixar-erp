<?php
namespace hardness;
/**
* Servidor de LOG
*/
$home = __FILE__;
$home = str_replace('scripts/queueMDFE', '', dirname($home));
chdir($home);
require 'scripts/queueMDFE/conf.php';
require 'scripts/queueMDFE/codigos.php';
require 'bibliotecas/funcoesGerais.php';


// Conexão com o banco
$con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error = mysqli_connect_error();
if (!empty($Error)) { die($Error); }
$g['conexaoBanco'] = $con;
// $dbs = mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');

$r_db = mysql_real_escape_string($_REQUEST['db']);
$r_T276_TX_Mensagem = mysql_real_escape_string($_REQUEST['T276_TX_Mensagem']);
$r_T276_Id = mysql_real_escape_string($_REQUEST['T276_Id']);
$sql = "UPDATE `{$r_db}`.T276 SET T276_TX_Mensagem = '{$r_T276_TX_Mensagem}' WHERE T276_Id = '{$r_T276_Id}';";
mysqli_query($sql);
