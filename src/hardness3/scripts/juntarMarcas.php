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
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysqli_error($con));
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');
$g['conexaoBanco'] = $con;

//$g = array();
include('../bibliotecas/funcoesGerais.php');

$sql = <<<EOT
SELECT
	T014_Id, D083_Codigo_Barras, T014_Codigo_Barras
FROM T014
left join T013 on T013_Id=T014_T013_Id
left join T012 on T012_Id=T014_T012_Id
left join T225 on T225_Id=T014_T225_Id
left join D009 on D009_Id=T014_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
left join D083 on D001_Id=D083_D001_Id
EOT;
$res = mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }
while ($row = mysqli_fetch_assoc($res)) {
	if (empty($row['D083_Codigo_Barras'])) { continue; }
	if ($row['D083_Codigo_Barras'] == $row['T014_Codigo_Barras']) { continue; }
	$sql = "UPDATE T014 SET T014_Codigo_Barras = '{$row['D083_Codigo_Barras']}' WHERE T014_Id = '{$row['T014_Id']}'";
	mysqli_query($con,$sql);
	if (mysqli_error($con)) { die($sql); }
	echo $sql . "\n";
}

$sql = <<<EOT
SELECT
	*
FROM D001
EOT;
$resD001 = mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }
while($rowD001 = mysqli_fetch_assoc($resD001)) {
	echo "D001_Id: {$rowD001['D001_Id']} ...\n";
	$sql = <<<EOT
		SELECT
			D049_Id, D049_D001_Id, D049_D082_Id
		FROM D049
		left join D009 on D009_D049_Id=D049_Id
		WHERE D049_D001_Id='{$rowD001['D001_Id']}' and (D009_C004_Id='1')
		ORDER BY D049_Modelo ASC
EOT;
	$resD049 = mysqli_query($con,$sql);
	if (mysqli_error($con)) { die($sql); }
	if (mysqli_num_rows($resD049) > 1) {
		$sqlD001 = 'INSERT INTO D001 (';
		foreach ($rowD001 as $key => $value) {
			if ($key == 'D001_Id') { continue; }
			$sqlD001 .= "{$key},";
		}
		$sqlD001 = trim($sqlD001, ',') . ') VALUE (';
		foreach ($rowD001 as $key => $value) {
			if ($key == 'D001_Id') { continue; }
			$sqlD001 .= "'" . mysqli_real_escape_string($con,$value) . "',";
		}
		$sqlD001 = trim($sqlD001, ',') . ')';
		echo "\tRegistos: " . mysqli_num_rows($resD049) . "\n";
		$count = 0;
		while ($rowD049 = mysqli_fetch_assoc($resD049)) {
			$count++;
			if ($count == 1) { continue; }
			mysqli_query($con,$sqlD001);
			if (mysqli_error($con)) { die($sql); }
			$D001_Id = mysql_insert_id();
			$sql = "UPDATE D049 SET D049_D001_Id = '{$D001_Id}' WHERE D049_Id = '{$rowD049['D049_Id']}'";
			mysqli_query($con,$sql);
			if (mysqli_error($con)) { die($sql); }
		}
	}
}

$sql = "UPDATE D001 SET D001_Flag_Multimarcas = 'N'";
mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }

$sql = "TRUNCATE D083";
mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }

$sql = <<<EOT
SELECT 
	D001_Id, D049_Id, T013_D024_Id, T014_Codigo_Produto, T014_Codigo_Barras
FROM T014
left join T013 on T013_Id=T014_T013_Id
left join D009 on D009_Id=T014_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
EOT;
$res = mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }
while ($row = mysqli_fetch_assoc($res)) {
	if (empty($row['D001_Id'])) { continue; }
	if (!empty($row['T014_Codigo_Produto']) || !empty($row['T014_Codigo_Barras'])) {
		$row['T014_Codigo_Produto'] = mysqli_real_escape_string($con,$row['T014_Codigo_Produto']);
		$row['T014_Codigo_Barras'] = mysqli_real_escape_string($con,$row['T014_Codigo_Barras']);
		$sql = "SELECT D083_Id FROM D083 WHERE D083_D024_Id = '{$row['T013_D024_Id']}' AND D083_D001_Id = '{$row['D001_Id']}'";
		$resD083 = mysqli_query($con,$sql);
		if (mysqli_error($con)) { die($sql); }
		if (mysqli_num_rows($resD083) > 0) {
			$rowD083 = mysqli_fetch_assoc($resD083);
			$sql = <<<EOT
				UPDATE D083 SET D083_Codigo_Barras = '{$row['T014_Codigo_Barras']}', D083_Codigo_Produto_Fornecedor = '{$row['T014_Codigo_Produto']}' WHERE D083_Id = '{$rowD083['D083_Id']}'
EOT;
			mysqli_query($con,$sql);
			if (mysqli_error($con)) { die($sql); }
			echo $sql . "\n";
		} else {
			$sql = <<<EOT
				INSERT INTO D083
					(D083_D024_Id, D083_D001_Id, D083_Codigo_Barras, D083_Codigo_Produto_Fornecedor, D083_Data_Cadastro, D083_D049_Id)
				VALUES
					('{$row['T013_D024_Id']}', '{$row['D001_Id']}', '{$row['T014_Codigo_Barras']}', '{$row['T014_Codigo_Produto']}', CURDATE(), '{$row['D049_Id']}')
EOT;
			mysqli_query($con,$sql);
			if (mysqli_error($con)) { die($sql); }
			echo $sql . "\n";
		}
	}
}

$sql = <<<EOT
SELECT 
	D001_Id, D049_Id, T003_D024_Id, T004_Codigo_Substituto
FROM T004
left join T003 on T003_Id=T004_T003_Id
left join D009 on D009_Id=T004_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
EOT;
$res = mysqli_query($con,$sql);
if (mysqli_error($con)) { die($sql); }
while ($row = mysqli_fetch_assoc($res)) {
	if (empty($row['D001_Id'])) { continue; }
	if (!empty($row['T004_Codigo_Substituto'])) {
		$row['T004_Codigo_Substituto'] = mysqli_real_escape_string($con,$row['T004_Codigo_Substituto']);
		$sql = "SELECT D083_Id FROM D083 WHERE D083_D024_Id = '{$row['T003_D024_Id']}' AND D083_D001_Id = '{$row['D001_Id']}'";
		$resD083 = mysqli_query($con,$sql);
		if (mysqli_error($con)) { die($sql); }
		if (mysqli_num_rows($resD083) > 0) {
			$rowD083 = mysqli_fetch_assoc($resD083);
			$sql = <<<EOT
				UPDATE D083 SET D083_Codigo_Produto_Fornecedor = '{$row['T004_Codigo_Substituto']}' WHERE D083_Id = '{$rowD083['D083_Id']}'
EOT;
			//mysqli_query($con,$sql);
			//if (mysqli_error($con)) { die($sql); }
			//echo $sql . "\n";
		} else {
			$sql = <<<EOT
				INSERT INTO D083
					(D083_D024_Id, D083_D001_Id, D083_Codigo_Produto_Fornecedor, D083_Data_Cadastro, D083_D049_Id)
				VALUES
					('{$row['T003_D024_Id']}', '{$row['D001_Id']}', '{$row['T004_Codigo_Substituto']}', CURDATE(), '{$row['D049_Id']}')
EOT;
			mysqli_query($con,$sql);
			if (mysqli_error($con)) { die($sql); }
			echo $sql . "\n";
		}
	}
}
