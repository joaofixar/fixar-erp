<?php
namespace hardness;

$sql = "SELECT * FROM C007 WHERE C007_Login != '' AND C007_Login IS NOT NULL";
$resC007 = mysql_query($sql);
$array = array();
while($rowC007 = mysql_fetch_assoc($resC007)) {
	$array[$rowC007['C007_Id']] = array(
		'login' => $rowC007['C007_Login'],
		'hits' => apc_fetch("{$confUsuario['dbDatabase']}-{$rowC007['C007_Id']}-hits"),
		'size' => apc_fetch("{$confUsuario['dbDatabase']}-{$rowC007['C007_Id']}-size"),
	);
	apc_store("{$confUsuario['dbDatabase']}-{$rowC007['C007_Id']}-hits", 0);
	apc_store("{$confUsuario['dbDatabase']}-{$rowC007['C007_Id']}-size", 0);
}
echo serialize($array);
die();