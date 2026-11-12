<?php
$confUsuarioERP = array(
	'dbDatabase' => 'global',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'master',
	//'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');

$array = array();

$sql = "SHOW FULL TABLES FROM `e237` WHERE table_type = 'BASE TABLE'";
$resTables = mysqli_query($con,$sql);
while ($rowTable = mysqli_fetch_array($resTables)) {
	$sql = "SHOW CREATE TABLE `e237`.`{$rowTable[0]}`;";
	$res = mysqli_query($con,$sql);
	$row = mysqli_fetch_array($res);
	echo $row[1] . ";\n";
	$create = explode("\n", $row[1]);
	foreach ($create as $item) {
		$item = trim(str_replace("\t", '', $item));
		if (preg_match("/^ *`{$rowTable[0]}_/i", $item)) {
			$item = trim($item, ',');
			echo "ALTER TABLE `{$rowTable[0]}` ADD COLUMN {$item};\n";
		} else {
			//echo " ---- " . $item . "\n";
		}
	}
	/*
	$sql = "SHOW FULL FIELDS FROM `e237`.`{$rowTable[0]}`;";
	$resFields = mysql_query($sql);
	while ($rowField = mysql_fetch_array($resFields)) {
		
	}
	*/
}

