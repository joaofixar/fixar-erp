<?php
namespace hardness;

$confUsuario = array(
	'dbDatabase' => 'e237',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query('SET NAMES utf8;');
$g['conexaoBanco'] = $con;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$sql = <<<EOT
SELECT D001_Codigo_Produto, D083_D001_Id, D083_D024_Id, D083_D049_Id FROM D083 LEFT JOIN D001 ON D001_Id = D083_D001_Id 
WHERE IFNULL(D001_Id,0) > 0
GROUP BY CONCAT(D083_D001_Id, D083_D024_Id, D083_D049_Id) HAVING COUNT(*) > 1
EOT;
$resD083 = mysqli_query($con,$sql);
while ($rowD083 = mysqli_fetch_assoc($resD083)) {
	$sql = "SELECT * FROM D083 WHERE D083_D001_Id = '{$rowD083['D083_D001_Id']}' AND D083_D024_Id = '{$rowD083['D083_D024_Id']}' AND D083_D049_Id = '{$rowD083['D083_D049_Id']}'";
	echo $sql . "\n";
	$res = mysqli_query($con,$sql);

	$D083_Codigo_Produto_Fornecedor = '';
	$D083_Codigo_Barras = '';
	$D083_Flag_Cliente = '';
	$D083_Flag_Fornecedor = '';
	$id = false;
	while ($row = mysqli_fetch_assoc($res)) {
		if (!$id) {
			$D083_Codigo_Produto_Fornecedor = $row['D083_Codigo_Produto_Fornecedor'];
			$D083_Codigo_Barras = $row['D083_Codigo_Barras'];
			$D083_Flag_Cliente = $row['D083_Flag_Cliente'];
			$D083_Flag_Fornecedor = $row['D083_Flag_Fornecedor'];
			$id = $row['D083_Id'];
			continue;
		}
		if (empty($D083_Codigo_Produto_Fornecedor) && !empty($row['D083_Codigo_Produto_Fornecedor'])) {
			$D083_Codigo_Produto_Fornecedor = $row['D083_Codigo_Produto_Fornecedor'];
			mysqli_query($con,"UPDATE D083 SET D083_Codigo_Produto_Fornecedor = '{$D083_Codigo_Produto_Fornecedor}' WHERE D083_Id = '{$id}'");
		}
		if (empty($D083_Codigo_Barras) && !empty($row['D083_Codigo_Barras'])) {
			$D083_Codigo_Barras = $row['D083_Codigo_Barras'];
			mysqli_query($con,"UPDATE D083 SET D083_Codigo_Barras = '{$D083_Codigo_Barras}' WHERE D083_Id = '{$id}'");
		}
		if (empty($D083_Flag_Cliente) && !empty($row['D083_Flag_Cliente'])) {
			$D083_Flag_Cliente = $row['D083_Flag_Cliente'];
			mysqli_query($con,"UPDATE D083 SET D083_Flag_Cliente = '{$D083_Flag_Cliente}' WHERE D083_Id = '{$id}'");
		}
		if (empty($D083_Flag_Fornecedor) && !empty($row['D083_Flag_Fornecedor'])) {
			$D083_Flag_Fornecedor = $row['D083_Flag_Fornecedor'];
			mysqli_query($con,"UPDATE D083 SET D083_Flag_Fornecedor = '{$D083_Flag_Fornecedor}' WHERE D083_Id = '{$id}'");
		}
		mysqli_query($con,"DELETE FROM D083 WHERE D083_Id = '{$row['D083_Id']}'");
	}
}
