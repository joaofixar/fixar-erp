<?php
namespace hardness;

$confUsuario = array(
	'dbDatabase' => 'dev_e234',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');
$g['conexaoBanco'] = $con;

include('../bibliotecas/funcoesGerais.php');

$sqlT166 = mysql_query("SELECT * FROM T166");
while($row = mysql_fetch_Array($sqlT166)){
	echo 'Reparcelamento ('.$row['T166_Tipo'].') ' . $row['T166_Id'] . ' Geradores (';
	$arrayGeradores = explode("\n", $row['T166_Titulos_Geradores']);
	foreach($arrayGeradores as $linha){
		if(empty($linha)) { continue; }
		$linha = explode(";", $linha);
		echo $linha[0] . ' ';
		mysqli_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$row['T166_Id']}')");
	}
	echo ') - Gerados (';
	$arrayGerados = explode("\n", $row['T166_Titulos_Gerados']);
	foreach($arrayGerados as $linha){
		if(empty($linha)) { continue; }
		$linha = explode(";", $linha);
		echo $linha[0] . ' ';
		mysqli_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$row['T166_Id']}')");
	}
	echo ')<br>';
}
