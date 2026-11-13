<?php
$confUsuario = array(
	'dbDatabase' => 'dev_e216',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysqli_select_db($confUsuario['dbDatabase'], $con) or die (mysqli_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');


$cache = array();
$sql = <<<EOT
SELECT 
	* 
FROM T005 
LEFT JOIN D024 ON D024_Id = T005_D024_Id 
left join T007 on T005_T007_Id=T007_Id and T007_Flag_Cancelada!='S' 
WHERE D024_Nome_Fantasia like '%SODEXO%' AND T005_Data_Entrega >= '2013-04-27' AND T005_Data_Entrega <= '2013-04-29'  
ORDER BY T005_Data_Emissao DESC
EOT;
$res = mysqli_query($con,$sql);
while ($row = mysqli_fetch_assoc($res)) {
	$t006s = '';
	$res2 = mysqli_query($con,"SELECT T006_D009_Id FROM T006 WHERE T006_T005_Id = '{$row['T005_Id']}' ORDER BY T006_D009_Id");
	while ($row2 = mysqli_fetch_assoc($res2)) {
		$t006s .= $row2['T006_D009_Id'] . '-';
	}
	$id = $row['T005_Data_Emissao'] . ' - ' . $row['T005_Data_Entrega'] . ' - ' . $row['T005_D024_Id'] . ' - ' . $row['T005_D006_Id'] . ' - ' . $row['T005_C004_Id'] . ' - ' . $t006s;
	//$cache[$id][] = $row['T005_Id'] . ' - ' . $row['T007_Numero_Nota_Fiscal'] . ' - ' . $row['T005_Data_Emissao'] . ' - ' . $row['T005_Data_Entrega'];
	$cache[$id][] = array($row['T007_Numero_Nota_Fiscal'], $row['T005_Id']);
}
foreach ($cache as $key => $value) {
	if (count($value) == 1) {
		unset($cache[$key]);
	}
}
//print_r($cache);
//die();
foreach ($cache as $key => $value) {
	foreach ($value as $key2 => $value2) {
		if (count($value) > 1 && empty($value2[0])) {
			//echo "T005_Id: " . $value2[1] . "\n";
			echo "UPDATE T005 SET T005_Flag_Status = 8, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T005_Id = {$value2[1]};\n";
			echo "UPDATE T006 SET T006_Flag_Status = 3, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1) WHERE T006_T005_Id = {$value2[1]};\n";
			unset($value[$key2]);
		}
	}
}

// 1968
// 27/04/2013