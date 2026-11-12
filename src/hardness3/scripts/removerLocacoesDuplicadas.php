<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'e229_novo',
    'dbHost' => '127.0.0.1',
    'dbUser' => 'e229_novo',
    'dbPass' => '',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($g['conexaoBanco'],$con,'SET NAMES utf8;');
$g['conexaoBanco'] = $con;

$rel = gPegaRelacionamento2('T066');

$sql = <<<EOT
	SELECT T066_D004_Id, T066_D009_Id, COUNT(*) FROM T066 GROUP BY CONCAT(T066_D004_Id,T066_D009_Id) HAVING COUNT(*) > 1
EOT;
$res = mysqli_query($g['conexaoBanco'],$sql);
while ($row = mysqli_fetch_assoc($res)) {
	echo 'D004_Id: ' . $row['T066_D004_Id'] . ' - D009_Id: ' . $row['T066_D009_Id'] . "\n";
	$sql = <<<EOT
		SELECT
			T066_Id
		FROM
			T066
		WHERE
			T066_D004_Id = '{$row['T066_D004_Id']}'
			AND T066_D009_Id = '{$row['T066_D009_Id']}'
EOT;
	$T066_Id = false;
	$resLoop = mysqli_query($g['conexaoBanco'],$sql);
	while ($rowLoop = mysqli_fetch_assoc($resLoop)) {
		if (!$T066_Id) {
			$T066_Id = $rowLoop['T066_Id'];
			//echo "\tPrimeiro T066_Id: {$T066_Id}\n";
		} else {
			//echo "\t+ T066_Id: {$rowLoop['T066_Id']}\n";
			mudar($T066_Id, $rowLoop['T066_Id']);
		}
	}
}

function gPegaRelacionamento2($tabela) {
	global $g;
	if (isset($g['cacheRelacionamento']) && isset($g['cacheRelacionamento'][$tabela])) {
		return $g['cacheRelacionamento'][$tabela];
	}
	$relacionamentos = array();
	$sql = "SHOW TABLES;";
	$res = mysqli_query($g['conexaoBanco'],$sql);
	while ($table = mysql_fetch_row($res)) {
		$table = $table[0];
		$sql = "DESC {$table}";
		$resDesc = mysqli_query($g['conexaoBanco'],$sql);
		while ($column = mysql_fetch_row($resDesc)) {
			$column = $column[0];
			if (preg_match('/^.*' . preg_quote($tabela) . '\_Id$/i', $column)) {
				if ($column != ($tabela . '_Id')) {
					$relacionamentos[] = $column;
				}
			}
		}
	}
	$g['cacheRelacionamento'][$tabela] = $relacionamentos;
	return $relacionamentos;
}

function mudar($novo, $antigo) {
	global $rel,$g;
	foreach ($rel as $id) {
		$tabela = explode('_', $id);
		$tabela = $tabela[0];
		$sql = "UPDATE {$tabela} SET {$id} = '{$novo}' WHERE {$id} = '{$antigo}'";
		//echo "\t\t$sql\n";
		mysqli_query($g['conexaoBanco'],$sql);
	}
	$sql = "DELETE FROM T066 WHERE T066_Id = '{$antigo}'";
	//echo "\t\t$sql\n";
	mysqli_query($g['conexaoBanco'],$sql);
}
