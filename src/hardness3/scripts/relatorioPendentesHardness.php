<?php
namespace hardness;
die("Não está mais em uso.");


$confUsuarioNOTA = array(
	'dbDatabase' => 'global',
	'dbHost' => '192.168.10.3',
	'dbUser' => 'root',
	'dbPass' => '!hard@3871',
);
$conNOTA=mysql_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioNOTA['dbDatabase'], $conNOTA) or die (mysql_error());
mysql_set_charset('utf8', $conNOTA);
mysql_query('SET NAMES utf8;', $conNOTA);
mysql_query("USE e001", $conNOTA);


// Totais
$total2013_ = strtotime('2013-01-01');
$total2013 = 0;
$total3meses_ = strtotime('-3 month');
$total3meses = 0;
$totalAnterior = 0;
$totalNunca = 0;

//
$array = array();
$sql = <<<EOT
SELECT * FROM C025 ORDER BY C025_Banco_Dados ASC
EOT;
$resBanco = mysql_query($sql);
while ($rowBanco = mysql_fetch_assoc($resBanco)) {
	$D024_Quantidade_Nfe_2013 = 0;
	$D024_Data_Ultima_Nota_Emitida = '0000-00-00';
	$D024_Data_Ultimo_Recebimento_Duplicata = '0000-00-00';
	$D024_Valor_Soma_Duplicatas_2013 = 0;

	// D024_Data_Ultima_Nota_Emitida
	$sql = <<<EOT
SELECT T007_Data_Emissao FROM `{$rowBanco['C025_Banco_Dados']}`.T007 ORDER BY T007_Id DESC LIMIT 1
EOT;
	$resT007 = mysql_query($sql);
	if (@mysql_num_rows($resT007)) {
		$rowT007 = mysql_fetch_assoc($resT007);
		$D024_Data_Ultima_Nota_Emitida = $rowT007['T007_Data_Emissao'];
	}
	// totais
	$emitiu = 'Nao';
	if (!$D024_Data_Ultima_Nota_Emitida) {
		$totalNunca++;
	} else if (strtotime($D024_Data_Ultima_Nota_Emitida) > $total3meses_) {
		$emitiu = 'Sim';
		$total2013++;
		$total3meses++;
	} else if (strtotime($D024_Data_Ultima_Nota_Emitida) > $total2013_) {
		$total2013++;
	} else {
		$totalAnterior++;
	}
	// fim totais
	
	// D024_Quantidade_Nfe_2013
	$sql = <<<EOT
SELECT COUNT(*) as Total FROM `{$rowBanco['C025_Banco_Dados']}`.T007 WHERE T007_Data_Emissao >= '2013-01-01' AND T007_Data_Emissao <= '2013-12-31'
EOT;
	$resT007 = mysql_query($sql);
	if (@mysql_num_rows($resT007)) {
		$rowT007 = mysql_fetch_assoc($resT007);
		$D024_Quantidade_Nfe_2013 = $rowT007['Total'];
	}

	//$D024_Valor_Soma_Duplicatas_2013
	$sql = <<<EOT
SELECT SUM(T002_Valor_Total) as Total FROM T002 WHERE T002_D024_Id = '{$rowBanco['C025_D024_Id']}' AND T002_Data_Recebimento >= '2013-01-01' AND T002_Data_Recebimento <= '2013-12-31'
EOT;
	$resT002 = mysql_query($sql);
	if (@mysql_num_rows($resT002)) {
		$rowT002 = mysql_fetch_assoc($resT002);
		$D024_Valor_Soma_Duplicatas_2013 = $rowT002['Total'] ? $rowT002['Total'] : 0;
	}
	
	// D024_Data_Ultimo_Recebimento_Duplicata
	$sql = <<<EOT
SELECT T002_Data_Recebimento, D024_Nome_Fantasia FROM T002 LEFT JOIN D024 ON D024_Id=T002_D024_Id WHERE T002_D024_Id = '{$rowBanco['C025_D024_Id']}' ORDER BY T002_Data_Recebimento DESC LIMIT 1
EOT;
	$resT002 = mysql_query($sql);
	if (@mysql_num_rows($resT002)) {
		$rowT002 = mysql_fetch_assoc($resT002);
		$D024_Data_Ultimo_Recebimento_Duplicata = $rowT002['T002_Data_Recebimento'];
		// última data de recebimento menor que 12 meses e emitio nota fiscal nos último 13 meses
		if (strtotime("-12 month") > strtotime($rowT002['T002_Data_Recebimento']) && (strtotime($D024_Data_Ultima_Nota_Emitida) > $total2013_)) {
			echo "\n{$rowBanco['C025_Banco_Dados']}\n\tCliente: {$rowT002['D024_Nome_Fantasia']}\n\tNotas Emitidas: {$D024_Quantidade_Nfe_2013}\n\tData ultima nota emitida: {$D024_Data_Ultima_Nota_Emitida}\n\tData ultimo recebimento duplicata: {$D024_Data_Ultimo_Recebimento_Duplicata}\n\tSoma das duplicatas em 2013: {$D024_Valor_Soma_Duplicatas_2013}\n\tEmitiu nota nos ultimos 3 meses: {$emitiu}\n";
		}
	}
}

// Imprimir totais
echo <<<EOT

TOTAL POR EMISSÃO DE NOTAS:
2013: {$total2013}
3 Meses: {$total3meses}
< 2013: {$totalAnterior}
Nunca: {$totalNunca}

EOT;

function gCorrigeData($data, $removerTime = false) {
	// para lidar com o callback do grid
	$valor = $data;
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	$valor = explode(' ', $valor);
	$hora = (isset($valor[1])) ? ' ' . $valor[1] : '';
	$valor = explode('-', $valor[0]);
	if ($valor[0] == '0000') { return ''; }
	if (isset($valor[0]) && isset($valor[1]) && isset($valor[2])) {
		$data = mktime(0, 0, 0, $valor[1], $valor[2], $valor[0]);
		if ($removerTime) {
			return date("d/m/Y", $data);
		} else {
			return date("d/m/Y", $data) . $hora;
		}
	} else {
		return $data;
	}
}
function gCorrigeDataInverte($valor) {
	// para lidar com o callback do grid
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	$valor = explode('/', $valor);
	$valor[2] = (int) $valor[2];
	$data = mktime(0, 0, 0, $valor[1], $valor[0], $valor[2]);
	if(empty($data)){
		return '';
	}
	return date("Y-m-d", $data);
}