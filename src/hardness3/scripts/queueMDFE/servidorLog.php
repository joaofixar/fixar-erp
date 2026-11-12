<?php
namespace hardness;
/**
* Servidor de LOG
*/
global $g;
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

$log = unserialize($_REQUEST['log']);
foreach ($log as &$value) {
	$value = mysql_real_escape_string($value);
}
$sql = <<<EOT
INSERT INTO Q005 (Q005_Servidor_Id, 
				  Q005_Database, 
				  Q005_C004_Id, 
				  Q005_T276_Id, 
				  Q005_Etapa, 
				  Q005_Codigo_Resposta, 
				  Q005_Resposta_Extra, 
				  Q005_Soap_Debug, 
				  Q005_Numero_MDFe, 
				  Q005_Retorno_Sefaz, 
				  Q005_T276_Serie, 
				  Q005_T276_Data_Emissao, 
				  Q005_C004_Nome_Abreviado, 
				  Q005_C004_Telefone, 
				  Q005_Url, 
				  Q005_DataHora, 
				  Q005_T276_Valor_Total)
		  VALUES ('{$log['Servidor_Id']}', 
				  '{$log['Database']}', 
				  '{$log['C004_Id']}', 
				  '{$log['T276_Id']}', 
				  '{$log['Etapa']}', 
				  '{$log['Codigo_Resposta']}', 
				  '{$log['Resposta_Extra']}', 
				  '{$log['Soap_Debug']}', 
				  '{$log['Numero_MDFe']}', 
				  '{$log['Retorno_Sefaz']}', 
				  '{$log['T276_Serie']}', 
				  '{$log['T276_Data_Emissao']}', 
				  '{$log['C004_Nome_Abreviado']}', 
				  '{$log['C004_Telefone']}', 
				  '{$log['Url']}', 
				  NOW(), 
				  '{$log['T276_Valor_Carga']}');
EOT;
mysqli_query($sql);
$Q005_Id = mysql_insert_id();

// Q006
$Q006_Md5 = mysql_real_escape_string(md5($log['Database'] . $log['C004_Id'] . $log['T276_Id']));
$sql = "SELECT * FROM Q006 WHERE Q006_Md5 = '{$Q006_Md5}';";
$res = mysqli_query($sql);
if (mysql_numrows($res) == 0) {
	$xml = !empty($log['XML']) ? $log['XML'] : '';
	$sql = "INSERT INTO Q006 (Q006_Md5, Q006_Q005_Id, Q006_XML) VALUES ('{$Q006_Md5}', '{$Q005_Id}', '{$xml}');";
} else {
	$xml = !empty($log['XML']) ? ", Q006_XML = '{$log['XML']}' " : '';
	$sql = "UPDATE Q006 SET Q006_Q005_Id = '{$Q005_Id}'{$xml} WHERE Q006_Md5 = '{$Q006_Md5}';";
}
mysqli_query($sql);
