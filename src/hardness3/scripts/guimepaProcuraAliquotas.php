<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'dev_e229',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'].$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysqli_select_db($confUsuario['dbDatabase'], $con) or die (mysqli_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');

set_time_limit(0);
$g = array();
chdir('../');
require_once('sistema/confSistema.php');
$g['conf'] = new confSistema();

require('bibliotecas/classes/VEN003.php');

echo "Empresa;Nr. NF;Codigo Produto;Descricao Produto;ICMS Antes;ICMS Depois;CFOP Antes;CFOP Depois;ICMS Antes;ICMS Depois\n";

$sql = "SELECT T007_Id, T007_C004_Id, T007_Numero_Nota_Fiscal FROM T007 WHERE T007_Numero_Nota_Fiscal > 4274 ORDER BY T007_Numero_Nota_Fiscal";
$resT007 = mysqli_query($con,$sql);
while ($rowT007 = mysqli_fetch_assoc($resT007)) {
	file_put_contents('/tmp/guimepa', $rowT007['T007_Numero_Nota_Fiscal']);
	$g['empresaAtual'] = $rowT007['T007_C004_Id'];
	$g['c020'] = $g['C020'] = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM C020 WHERE C020_C004_Id = '{$rowT007['T007_C004_Id']}';"));
	
	$arrayAntes = array();
	$sql = "SELECT T008_Id, T008_Aliquota_ICMS, T008_C004_Id, T008_Codigo_Produto, T008_Descricao_Produto, D006_Codigo_CFOP, T008_Valor_ICMS FROM T008 LEFT JOIN D006 ON D006_Id=T008_D006_Id WHERE T008_T007_Id = '{$rowT007['T007_Id']}' ORDER BY T008_Id ASC";
	$resT008 = mysqli_query($con,$sql);
	while($rowT008 = mysqli_fetch_assoc($resT008)) {
		$arrayAntes[$rowT008['T008_Id']] = $rowT008;
	}

	$VEN003 = new VEN003();
	$atualizar = $VEN003->ven003atualizarCFOP($rowT007['T007_Id']);
	if ($atualizar !== true) {
		die('Erro: ' . $atualizar);
	}

	$arrayDepois = array();
	$sql = "SELECT T008_Id, T008_Aliquota_ICMS, T008_C004_Id, T008_Codigo_Produto, T008_Descricao_Produto, D006_Codigo_CFOP, T008_Valor_ICMS FROM T008 LEFT JOIN D006 ON D006_Id=T008_D006_Id WHERE T008_T007_Id = '{$rowT007['T007_Id']}' ORDER BY T008_Id ASC";
	$resT008 = mysqli_query($con,$sql);
	while($rowT008 = mysqli_fetch_assoc($resT008)) {
		$arrayDepois[$rowT008['T008_Id']] = $rowT008;
	}

	foreach ($arrayAntes as $key => $value) {
		if ($arrayDepois[$key]['T008_Aliquota_ICMS'] != $value['T008_Aliquota_ICMS'] || $arrayDepois[$key]['D006_Codigo_CFOP'] != $value['D006_Codigo_CFOP']) {
			$value['T008_Valor_ICMS'] = gCorrigeNumero($value['T008_Valor_ICMS']);
			$arrayDepois[$key]['T008_Valor_ICMS'] = gCorrigeNumero($arrayDepois[$key]['T008_Valor_ICMS']);
			echo "{$value['T008_C004_Id']};{$rowT007['T007_Numero_Nota_Fiscal']};{$value['T008_Codigo_Produto']};{$value['T008_Descricao_Produto']};{$value['T008_Aliquota_ICMS']};{$arrayDepois[$key]['T008_Aliquota_ICMS']};{$value['D006_Codigo_CFOP']};{$arrayDepois[$key]['D006_Codigo_CFOP']};{$value['T008_Valor_ICMS']};{$arrayDepois[$key]['T008_Valor_ICMS']}\n";
		}
	}
}

function definirClasse($classe) {
	global $g;
	$dir = "{$g['pathDados']}personalizacaoConf/";
	$arquivoGlobal = $dir . $classe . '.PHP-alterarMetodos.php';
	$arquivoEmpresa = $dir . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
	if (file_exists($arquivoEmpresa)) {
		include $arquivoEmpresa;
		return true;
	}
	if (file_exists($arquivoGlobal)) {
		include $arquivoGlobal;
		return true;
	}
	eval("namespace hardness;\nclass {$classe} extends {$classe}_ {}");
	return false;
}

function log($x) {
	return true;
}

function gCorrigeNumero($valor, $decimal = 2) {
	// para lidar com o callback do grid
	if (is_array($valor)) {
		if (empty($valor['campoAtual'])) { return ''; } 
		$valor = $valor['campoAtual'];
	}
	if (!empty($valor) && is_numeric($valor)) {
		return number_format($valor, $decimal, ',', '.');
	} else {
		return str_pad('0,', $decimal+2, "0", STR_PAD_RIGHT);
	}
}