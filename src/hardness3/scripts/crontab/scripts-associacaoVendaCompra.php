<?php
namespace hardness;

$sql = <<<EOT
SELECT 
	T008_Id,
	T001_Id,
	D001_Codigo_Produto,
	T001_D009_Id
FROM T008
LEFT JOIN T001 ON T001_T008_Id=T008_Id
LEFT JOIN T007 ON T007_Id=T008_T007_Id
left join D009 on D009_Id=T001_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
WHERE
	T001_Id IS NOT NULL
	AND (T008_Compra_T001_Id IS NULL OR T008_Compra_T001_Id = 0)
GROUP BY T001_Id
EOT;
// AND T007_Flag_ACP = 6
$resT008 = mysql_query($sql);
while ($rowT008 = mysql_fetch_assoc($resT008)) {
	// echo "D001_Codigo_Produto: {$rowT008['D001_Codigo_Produto']} T008_Id: {$rowT008['T008_Id']} T001_Id: {$rowT008['T001_Id']} ...\n";
	$sql = <<<EOT
SELECT
	T001_Id,
	T001_T014_Id
FROM T001
WHERE
	T001_Id < {$rowT008['T001_Id']}
	AND T001_Flag_Operacao = 'E'
	AND IFNULL(T001_T014_Id,0) > 0
	AND T001_D009_Id = '{$rowT008['T001_D009_Id']}'
ORDER BY T001_Id DESC
LIMIT 1
EOT;
	$resT001 = mysql_query($sql);
	if (mysql_num_rows($resT001) > 0) {
		$rowT001 = mysql_fetch_assoc($resT001);
		echo "\tT001_Id: {$rowT001['T001_Id']}\n";
		$sql = "UPDATE T008 SET T008_Compra_T001_Id = '{$rowT001['T001_Id']}' WHERE T008_Id = '{$rowT008['T008_Id']}'";
		mysqli_query($sql);
	} else {
		// echo "\tCompra não encontrada!\n";
	}
}
