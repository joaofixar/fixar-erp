<?php
$confUsuario = array(
	'dbDatabase' => 'dev_e218',
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


$cache = array();
$sql = <<<EOT
SELECT 
	D009_Id, D009_Valor_Custo_Tabela 
FROM D009
EOT;
$res = mysqli_query($con,$sql);
while ($row = mysqli_fetch_assoc($res)) {
	echo "UPDATE D009 SET D009_Valor_Custo_Tabela = '{$row['D009_Valor_Custo_Tabela']}' WHERE D009_Id = '{$row['D009_Id']}';\n";
}
