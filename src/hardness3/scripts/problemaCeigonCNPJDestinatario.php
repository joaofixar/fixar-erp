<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'dev_e237',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');
$g['conexaoBanco'] = $con;

chdir('../');
require_once('sistema/confSistema.php');
$g['conf'] = new confSistema();
require_once('bibliotecas/funcoesGerais.php');
require_once("bibliotecas/classes/VEN003.php");
$VEN003 = new VEN003();

$sql = <<<EOT
SELECT 
	*
FROM
	T013
ORDER BY T013_Id ASC
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	echo $row['T013_Id'] . " ...\n";
	$dom = new \DomDocument;
	$row['T013_Arquivo_XML'] = preg_replace('#^\?#', '', $row['T013_Arquivo_XML']);
	$row['T013_Arquivo_XML'] = stripslashes($row['T013_Arquivo_XML']);
	$xml = $dom->loadXML($row['T013_Arquivo_XML']);
	if (!$xml) { continue; }
	
	$emit = $dom->getElementsByTagName("emit")->item(0);
	$cnpj = @$emit->getElementsByTagName("CNPJ")->item(0)->nodeValue;
	if (empty($cnpj)) {
		$cnpj = @$emit->getElementsByTagName("CPF")->item(0)->nodeValue;
	}
	if (!empty($cnpj)) {
		echo "\t{$cnpj}\n";
		$cnpj = mysql_real_escape_string($cnpj);
		$sql = "UPDATE T013 SET T013_Cnpj_Fornecedor = '{$cnpj}' WHERE T013_Id = '{$row['T013_Id']}'";
		mysqli_query($sql);
	}
}
