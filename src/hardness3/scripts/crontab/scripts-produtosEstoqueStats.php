<?php
namespace hardness;
/**
* Atualização estatísticas produtos
* 
* Este script atualiza a tabela T177
*/

// Truncate
$sql = "TRUNCATE T177;";
$result = mysql_query($sql);
if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }

// Temos que fazer ano por ano, senão estoura a memória
for ($fAno=2010; $fAno <= 2013; $fAno++) {
	$array = array();
	for ($fMes=1; $fMes <= 12; $fMes++) {
		$sql = "SELECT T001_D024_Id, T001_D009_Id, T001_Data_Lancamento, T001_Quantidade, T001_Flag_Operacao, T001_Valor_Custo_Unitario, T001_Quantidade_Saldo FROM T001 WHERE T001_Data_Lancamento >= '{$fAno}-{$fMes}-01' AND T001_Data_Lancamento <= '{$fAno}-{$fMes}-31' ORDER BY T001_Data_Lancamento ASC;";
		$result = mysql_query($sql);
		if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['T001_Flag_Operacao'] != 'S') { continue; }
			$mes = explode('-', $row['T001_Data_Lancamento']);
			$mes = "{$mes[0]}-{$mes[1]}-01";
			isset($array[$mes][$row['T001_D009_Id']]) or ($array[$mes][$row['T001_D009_Id']] = array('Operacao' => 'S', 'Quantidade' => 0, 'Operacoes' => 0, 'Clientes' => array()));
			$array[$mes][$row['T001_D009_Id']]['Quantidade'] += $row['T001_Quantidade'];
			$array[$mes][$row['T001_D009_Id']]['Operacoes'] += 1;
			in_array($row['T001_D024_Id'], $array[$mes][$row['T001_D009_Id']]['Clientes']) or ($array[$mes][$row['T001_D009_Id']]['Clientes'][] = $row['T001_D024_Id']);
			// Custo
			isset($array[$mes][$row['T001_D009_Id']]['T177_Custo_Media']) or ($array[$mes][$row['T001_D009_Id']]['T177_Custo_Media'] = array(0, 0));
			$array[$mes][$row['T001_D009_Id']]['T177_Custo_Media'][0]++;
			$array[$mes][$row['T001_D009_Id']]['T177_Custo_Media'][1] += $row['T001_Valor_Custo_Unitario'];
			$array[$mes][$row['T001_D009_Id']]['T177_Custo_Fim_Mes'] = $row['T001_Valor_Custo_Unitario'];
			// Estoque
			isset($array[$mes][$row['T001_D009_Id']]['T177_Estoque_Media']) or ($array[$mes][$row['T001_D009_Id']]['T177_Estoque_Media'] = array(0, 0));
			$array[$mes][$row['T001_D009_Id']]['T177_Estoque_Media'][0]++;
			$array[$mes][$row['T001_D009_Id']]['T177_Estoque_Media'][1] += $row['T001_Quantidade_Saldo'];
			$array[$mes][$row['T001_D009_Id']]['T177_Estoque_Fim_Mes'] = $row['T001_Quantidade_Saldo'];
		}
	}
	// Popular a T177
	foreach ($array as $popData => $popProdutos) {
		echo "{$popData}\n";
		foreach ($popProdutos as $popProduto => $popProdArray) {
			// Custo
			$popProdArray['T177_Custo_Media'] = round($popProdArray['T177_Custo_Media'][1] / $popProdArray['T177_Custo_Media'][0], 2);
			// Estoque
			$popProdArray['T177_Estoque_Media'] = round($popProdArray['T177_Estoque_Media'][1] / $popProdArray['T177_Estoque_Media'][0], 2);
			// Clientes
			$T177_Clientes_Total = count($popProdArray['Clientes']);
			$popProdArray['Clientes'] = implode(',', $popProdArray['Clientes']);
			$sql = "INSERT INTO T177 (`T177_D009_Id`,`T177_Flag_Operacao`,`T177_Data`,`T177_Quantidade`,`T177_Operacoes`,`T177_Clientes`,T177_Clientes_Total, T177_Custo_Media, T177_Custo_Fim_Mes, T177_Estoque_Media, T177_Estoque_Fim_Mes) VALUES ('".mysql_real_escape_string($popProduto)."','S','{$popData}','{$popProdArray['Quantidade']}','{$popProdArray['Operacoes']}','".mysql_real_escape_string($popProdArray['Clientes'])."', '{$T177_Clientes_Total}', '{$popProdArray['T177_Custo_Media']}', '{$popProdArray['T177_Custo_Fim_Mes']}', '{$popProdArray['T177_Estoque_Media']}', '{$popProdArray['T177_Estoque_Fim_Mes']}');";
			$resultIns = mysqli_query($sql);
			if (!$resultIns) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
		}
	}
}

// Todos os produtos dos últimos 3,6,12,24 meses
$sql = "TRUNCATE T187;";
mysql_query($sql);
echo "Pré-calculando meses:\n";
//$meses = array(3, 6, 12, 24);
//foreach ($meses as $mes) {
for ($mes = 12; $mes <= 12; $mes++) {
	$date = new \DateTime();
	$date->modify('-'.$mes. ' months');
	echo "\t{$date->format('Y-m-d')}\n";
	// Quantidade
	$sql = "SELECT T177_D009_Id, SUM(T177_Quantidade) AS Total FROM T177 WHERE T177_Data >= '{$date->format('Y-m-d')}' GROUP BY T177_D009_Id";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		// Tem que existir alguma movimentação anterior (o produto já tem que existir)
		$T187_Flag_Prod_Anterior = 1;
		if ($row['T177_D009_Id'] > 0) {
			$sql = "SELECT COUNT(*) as Total FROM T001 WHERE T001_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T001_Data_Lancamento < '{$date->format('Y-m-d')}';";
			$res2 = mysql_query($sql);
			$row2 = mysql_fetch_assoc($res2);
			if ($row2['Total'] == 0) { $T187_Flag_Prod_Anterior = 0; }
		}
		$sql = "INSERT INTO T187 (`T187_D009_Id`,`T187_Nr_Meses`,`T187_Quantidade`,`T187_Quantidade_Media`,`T187_Flag_Prod_Anterior`) VALUES ('".mysql_real_escape_string($row['T177_D009_Id'])."', '".mysql_real_escape_string($mes)."', '".mysql_real_escape_string($row['Total'])."', '".mysql_real_escape_string(round($row['Total']/$mes, 2))."', '{$T187_Flag_Prod_Anterior}');";
		mysqli_query($sql);
	}
	// Garante que todos os produtos tenham um registro
	$sql = "SELECT D009_Id FROM D009;";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$sql = "SELECT * FROM T187 WHERE T187_D009_Id = '".mysql_real_escape_string($row['D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		$res2 = mysql_query($sql);
		if (mysql_num_rows($res2) == 0) {
			// Tem que existir alguma movimentação anterior (o produto já tem que existir)
			$T187_Flag_Prod_Anterior = 1;
			if ($row['D009_Id'] > 0) {
				$sql = "SELECT COUNT(*) as Total FROM T001 WHERE T001_D009_Id = '".mysql_real_escape_string($row['D009_Id'])."' AND T001_Data_Lancamento < '{$date->format('Y-m-d')}';";
				$res2 = mysql_query($sql);
				$row2 = mysql_fetch_assoc($res2);
				if ($row2['Total'] == 0) { $T187_Flag_Prod_Anterior = 0; }
			}
			$sql = "INSERT INTO T187 (`T187_D009_Id`,`T187_Nr_Meses`,`T187_Nr_Meses_Count`,`T187_Quantidade`,`T187_Quantidade_Media`,`T187_Operacoes`,`T187_Operacoes_Media`,`T187_Clientes_Total`,`T187_Custo_Media`,`T187_Estoque_Media`,`T187_Flag_Prod_Anterior`) VALUES ('{$row['D009_Id']}','{$mes}','0','0','0','0','0','0','0','0','{$T187_Flag_Prod_Anterior}');";
			$result = mysqli_query($sql);
			if (!$result) { die('Invalid query: ' . mysql_error()); }
		}
	}
	// Operacoes
	$sql = "SELECT T177_D009_Id, SUM(T177_Operacoes) AS Total FROM T177 WHERE T177_Data >= '{$date->format('Y-m-d')}' GROUP BY T177_D009_Id";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$sql = "UPDATE T187 SET T187_Operacoes = '".mysql_real_escape_string($row['Total'])."', T187_Operacoes_Media = '".mysql_real_escape_string(round($row['Total']/$mes, 2))."' WHERE T187_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		mysql_query($sql);
	}
	// Custo
	$sql = "SELECT T177_D009_Id, SUM(T177_Custo_Media)/count(*) AS Total FROM T177 WHERE T177_Data >= '{$date->format('Y-m-d')}' GROUP BY T177_D009_Id";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$sql = "UPDATE T187 SET T187_Custo_Media = '".mysql_real_escape_string($row['Total'])."' WHERE T187_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		mysql_query($sql);
	}
	// Estoque
	$sql = "SELECT T177_D009_Id, SUM(T177_Estoque_Media)/count(*) AS Total FROM T177 WHERE T177_Data >= '{$date->format('Y-m-d')}' GROUP BY T177_D009_Id";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$sql = "UPDATE T187 SET T187_Estoque_Media = '".mysql_real_escape_string($row['Total'])."' WHERE T187_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		mysql_query($sql);
	}
	// Clientes
	$sql = "SELECT T177_D009_Id, SUM(T177_Clientes_Total) AS Total FROM T177 WHERE T177_Data >= '{$date->format('Y-m-d')}' GROUP BY T177_D009_Id";
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		$sql = "UPDATE T187 SET T187_Clientes_Total = '".mysql_real_escape_string($row['Total'])."' WHERE T187_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		mysql_query($sql);
		// T187_Nr_Meses_Count
		$sql2 = "SELECT T177_Id FROM T177 WHERE T177_D009_Id = {$row['T177_D009_Id']} AND T177_Data >= '{$date->format('Y-m-d')}';";
		$res2 = mysql_query($sql2);
		$sql3 = "UPDATE T187 SET T187_Nr_Meses_Count = '".mysql_real_escape_string(mysql_numrows($res2))."' WHERE T187_D009_Id = '".mysql_real_escape_string($row['T177_D009_Id'])."' AND T187_Nr_Meses = '{$mes}';";
		mysql_query($sql3);
	}
}

// Garante que todos os produtos tenham registros para todos os meses (para poder tirar a media via SQL)
/*$sql = "SELECT T177_D009_Id FROM T177 GROUP BY T177_D009_Id;";
$result = mysql_query($sql);
if (!$result) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
while ($row = mysql_fetch_assoc($result)) {
	echo $row['T177_D009_Id'] . "\n";
	for ($fAno=2011; $fAno <= 2012; $fAno++) {
		for ($fMes=1; $fMes <= 12; $fMes++) {
			$sql = "SELECT * FROM T177 WHERE T177_D009_Id = '{$row['T177_D009_Id']}' AND T177_Data = '{$fAno}-{$fMes}-01';";
			$result2 = mysql_query($sql);
			if (!$result2) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			if (mysql_num_rows($result2) == 0) {
				$sql = "INSERT INTO T177 (`T177_D009_Id`,`T177_Flag_Operacao`,`T177_Data`,`T177_Quantidade`,`T177_Operacoes`,`T177_Clientes`) VALUES ('".mysql_real_escape_string($row['T177_D009_Id'])."','S','{$fAno}-{$fMes}-01','0','0','');";
				$resultIns = mysqli_query($sql);
				if (!$resultIns) { die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); }
			}
		}
	}
}*/

/*
T177_Id
T177_D009_Id
T177_Flag_Operacao
T177_Data
T177_Quantidade
T177_Operacoes
T177_Clientes
*/