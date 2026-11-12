<?php
namespace hardness;
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts', '', dirname($home));
require_once($home . 'bibliotecas/confUsuario.php');
require_once($home . 'bibliotecas/conexaoBanco.php');

$sql = <<<EOT
	SELECT 
		D001_Codigo_Produto, D009_Id, D049_Id, D049_D024_Id, D009_Valor_Custo_Ultima_Compra
	FROM 
		D009
	LEFT JOIN D049 ON D049_Id=D009_D049_Id
	LEFT JOIN D001 ON D001_Id=D049_D001_Id
	LEFT JOIN D024 AS UltimoForn ON UltimoForn.D024_Id=D049_D024_Id
	WHERE
	D049_D024_Id IS NULL OR D049_D024_Id = 0 OR D049_D024_Id = ''
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$sql = "SELECT T001_D024_Id, T001_Valor_Custo_Unitario FROM T001 WHERE T001_Flag_Atualiza_Custo = 'S' AND T001_D009_Id={$row['D009_Id']} AND T001_Descricao_Operacao LIKE 'NF-%' AND (T001_D024_Id is not NULL AND T001_D024_Id != '' AND T001_D024_Id != 0) ORDER BY T001_Data_Lancamento DESC LIMIT 0,1";
	$res2 = mysql_query($sql);
	if (mysql_num_rows($res2) > 0) {
		$row2 = mysql_fetch_assoc($res2);
		if (!empty($row2['T001_D024_Id'])) {
			echo "{$row['D001_Codigo_Produto']} - {$row['D009_Id']} - {$row2['T001_D024_Id']} - {$row2['T001_Valor_Custo_Unitario']}\n";
			$result = mysql_query("UPDATE D009 SET D009_Valor_Custo_Ultima_Compra = '{$row2['T001_Valor_Custo_Unitario']}' WHERE D009_Id = '{$row['D009_Id']}';");
			if (!$result) { die('Erro: ' . mysql_error()); }
			$result = mysql_query("UPDATE D049 SET D049_D024_Id = '{$row2['T001_D024_Id']}' WHERE D049_Id = '{$row['D049_Id']}';");
			if (!$result) { die('Erro: ' . mysql_error()); }
			//echo $sqlD009 . "\n";
			//echo $sqlD049 . "\n";
		}
	}
}
