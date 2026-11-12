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
$g['conexaoBanco'] = $con;
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');

include('../bibliotecas/funcoesGerais.php');

$D020 = gPegaRelacionamento2('D020');

$cache = array();
$sql = <<<EOT
SELECT A.*, (SELECT B.D020_Id FROM D020 AS B WHERE B.D020_Id != A.D020_Id AND B.D020_Nome_Cidade = A.D020_Nome_Cidade LIMIT 1) AS D020_Id_Errado FROM D020 AS A 
WHERE
(SELECT D020_Id FROM D020 AS B WHERE B.D020_Id != A.D020_Id AND B.D020_Nome_Cidade = A.D020_Nome_Cidade LIMIT 1) != ''
AND
A.D020_Codigo_IBGE != ''
ORDER BY A.D020_Nome_Cidade
EOT;
$res = mysqli_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	foreach ($D020 as $key => $value) {
		$tabela = explode('_', $value);
		$sql = "UPDATE {$tabela[0]} SET {$value} = '{$row['D020_Id']}' WHERE {$value} = '{$row['D020_Id_Errado']}';";
		echo $sql . "\n";
		mysqli_query($sql);
	}
	$sql = "DELETE FROM D020 WHERE D020_Id = '{$row['D020_Id_Errado']}'";
	echo $sql . "\n";
	mysqli_query($sql);
}
