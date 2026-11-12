<?php
namespace hardness;

set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

verificarConexaoClickhouse();

require_once("scripts/crontab/scripts-datawareHouse-funcoes.php");
/**
* Dataware house
* 
* Este script limpa e popula o dataware house
*/

echo "Inicio " . date("H:i:s") . "\n<br />";

// Tables
$tables = array(
	'FAC001',	// fatos
	'DIM001',	// Data 1
	'DIM002',	// D024
	'DIM003',	// C007 (Vendedor Interno)
	'DIM004',	// C007 (Vendedor Externo)
	'DIM005',	// Data 2
	'DIM006',	// Data 3
	'DIM007',	// D014
	'DIM008',	// D006 (Filtros)
	'DIM009',	// D009
	'DIM010',	// D006
	'DIM011',	// D021
	'DIM012',	// C004
	'DIM013'	// D007
);
$cache = array(
    'insert_id' => 0
);
// Apaga e recria as tabelas
carregaTabelas();

$sql = "UPDATE D182 SET D182_Grid_Salvo = NULL";
mysqli_query($sql);

/*
Historico dos Produtos
---------------------------
*** Dimensões ***
Data (1): 				T001_Data_Lancamento
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		T001_D024_Id / T001_D024_Id_Cliente
Local:					T001_D024_Id / T001_D024_Id_Cliente
Usuários (Vendedor):	-
Usuários (Superior):	-
Produto:				T001_D009_Id
Conta:					-
*** Fatos ***
T001_Quantidade
T001_Valor_Preco_Unitario
*/

/*
echo "Historico dos Produtos...\n<br />";
$sql = <<<EOT
		 SELECT T001_Id,
		        T001_Flag_Operacao, 
			    T001_D024_Id,
				T001_D024_Id_Cliente,
				T001_D009_Id,
				T001_D006_Id,
				D009_C004_Id,
				T001_T066_Id,
				T001_Quantidade,
				T001_Valor_Preco_Unitario,
				T001_Valor_Custo_Unitario,
				T001_Quantidade_Saldo,
				T001_Data_Lancamento
       	   FROM T001 
	  LEFT JOIN D009 ON D009_Id=T001_D009_Id 
	      WHERE T001_Data_Lancamento >= "{$dataInicialDados}"
	   ORDER BY T001_Id ASC
EOT;
$result = mysqli_query($sql);
echo mysqli_error();
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	$D024_Id = ($row['T001_Flag_Operacao'] == 'E') ? $row['T001_D024_Id'] : $row['T001_D024_Id_Cliente'];
	$FAC001_DIM001_Id = addData($row['T001_Data_Lancamento'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($D024_Id);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($D024_Id);
	$FAC001_DIM004_Id_Vendedor = false;
	$FAC001_DIM004_Id_Supervisor = false;
	$FAC001_DIM005_Id = false;
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = false;
	$FAC001_DIM008_Id = false;
	$FAC001_DIM009_Id = addProduto($row['T001_D009_Id']);
	$FAC001_DIM010_Id = addCFOP($row['T001_D006_Id']);
	$FAC001_DIM011_Id = false;
	$FAC001_DIM012_Id = addEmpresa($row['D009_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = $row['T001_Flag_Operacao'] == 'C' ? 'S' : 'N';
	
	$valorSaldoInicial = $row['T001_Quantidade_Saldo'];
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade', $row['T001_Quantidade']);
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Valor_Preco', $row['T001_Valor_Preco_Unitario']);
	
	if($row['T001_Flag_Operacao'] == 'E' && isset($row['T001_T066_Id']) && $row['T001_T066_Id'] > 0){
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade_Entrada', $row['T001_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Valor_Entrada', $row['T001_Quantidade']*$row['T001_Valor_Custo_Unitario']);
		$valorSaldoInicial -= $row['T001_Quantidade'];
	}
	if($row['T001_Flag_Operacao'] == 'S' && isset($row['T001_T066_Id']) && $row['T001_T066_Id'] > 0){
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade_Saida', $row['T001_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Valor_Saida', $row['T001_Quantidade']*$row['T001_Valor_Custo_Unitario']);
		$valorSaldoInicial += $row['T001_Quantidade'];
	}
	incluirFato($FAC001_DIM001_Id, false, false, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, false, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade_Saldo_Inicial', $valorSaldoInicial, 'S', null);
	incluirFato($FAC001_DIM001_Id, false, false, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, false, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade_Saldo_Inicial', $valorSaldoInicial*$row['T001_Valor_Custo_Unitario'], 'S', null);
	incluirFato($FAC001_DIM001_Id, false, false, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, false, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Quantidade_Saldo', $row['T001_Quantidade_Saldo'], 'S', false);
	incluirFato($FAC001_DIM001_Id, false, false, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, false, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_HIST_Valor_Saldo', $row['T001_Quantidade_Saldo']*$row['T001_Valor_Custo_Unitario'], 'S', false);
	$count++;
	if (($count % 1000) == 0) {
		echo "($count) ";
	}
}
echo "\n<br />";

///////////////////////////////////////////////////////////////////
// Loop para popular o saldo dos produtos sem movimentação no mês
///////////////////////////////////////////////////////////////////
echo "Corrigindo Saldo dos Produtos...\n<br />";
$count = 0;

$arrayDIM012 = array();
$sql = mysqli_query("SELECT DIM012_Id FROM DIM012");
while($row = mysqli_fetch_assoc($sql)){
	$arrayDIM012[] = $row['DIM012_Id'];
}

foreach ($arrayDIM012 as $DIM012_Id) {
	$arrayHist = array();
	$sql = mysqli_query("SELECT * FROM (SELECT DIM001_Data, CONCAT(DIM001_Ano,'-',LPAD(DIM001_Mes, 2, '0')) AS Mes, FAC001_HIST_Quantidade_Saldo, FAC001_HIST_Valor_Saldo, FAC001_DIM009_Id, FAC001_DIM012_Id FROM FAC001 LEFT JOIN DIM001 ON DIM001_Id = FAC001_DIM001_Id WHERE FAC001_FLAG_SALDO = 'S' AND FAC001_DIM012_Id = '{$DIM012_Id}' ORDER BY DIM001_Data DESC, FAC001_Id DESC) AS T2 GROUP BY Mes, FAC001_DIM009_Id");
	while($row = mysqli_fetch_assoc($sql)){
		$arrayHist[$row['FAC001_DIM009_Id']][][$row['Mes']] = array($row['FAC001_HIST_Quantidade_Saldo'], $row['FAC001_HIST_Valor_Saldo']);
	}
	foreach ($arrayHist as $DIM009_Id => $arrayKeys) {
		$arrayKeys = array_reverse($arrayKeys);
		$arrayDatas = array_reduce($arrayKeys, array_merge, array());
		$index = 0;
		$mes = date("m");
		$ano = date("Y");
		$dataB = $ano.'-'.$mes;
		$dataFinal = key(array_slice($arrayDatas, -1, 1, true)); 
		while($dataB != $dataFinal){
			if(!isset($arrayDatas[$dataB])) {
				$dataB = date("{$dataB}-t", strtotime($dataB.'-1'));
				incluirFato(addData($dataB, 'DIM001'), false, false, false, false, false, false, false, false, $DIM009_Id, false, false, $DIM012_Id, false, false, 'FAC001_HIST_Quantidade_Saldo', $arrayKeys[$index][key($arrayKeys[$index])][0], 'S', false);
				incluirFato(addData($dataB, 'DIM001'), false, false, false, false, false, false, false, false, $DIM009_Id, false, false, $DIM012_Id, false, false, 'FAC001_HIST_Quantidade_Saldo_Inicial', $arrayKeys[$index][key($arrayKeys[$index])][0], 'S', false);
				incluirFato(addData($dataB, 'DIM001'), false, false, false, false, false, false, false, false, $DIM009_Id, false, false, $DIM012_Id, false, false, 'FAC001_HIST_Valor_Saldo', $arrayKeys[$index][key($arrayKeys[$index])][1], 'S', false);
				incluirFato(addData($dataB, 'DIM001'), false, false, false, false, false, false, false, false, $DIM009_Id, false, false, $DIM012_Id, false, false, 'FAC001_HIST_Valor_Saldo_Inicial', $arrayKeys[$index][key($arrayKeys[$index])][1], 'S', false);
				$count++;
				if (($count % 1000) == 0) {
					echo "($count)";
				}
			} else {
				$index++;
			}
			$mes--;			
			if($mes == 0){ $mes = 12; $ano--; }
			$mes = str_pad($mes, 2, '0',STR_PAD_LEFT);
			$dataB = $ano.'-'.$mes;
			if($ano <= 1971){
				echo "Terminou";
				exit($ano);
			}
		}
	}
}

echo "\n<br />";
*/
/*
Ordem de Compras
---------------------------
*** Dimensões ***
Data (1): 				T224_Data_Emissao
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		T224_D024_Id
Local:					T224_D024_Id
Usuários (Vendedor):	T224_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				T225_D009_Id
Conta:					-
CFOP:					T224_D006_Id
Representante:			T224_D021_Id
Flag Cancelado:			T224_Flag_Status = 8 .. T224_T224_Id_Agrupado
*/
/*
echo "Ordens de Compra...\n<br />";
$sql = <<<EOT
SELECT 
	T224_Id, T224_Data_Emissao, T224_D024_Id, T224_C007_Id_Vendedor_Interno, C007_C007_Id, T224_D006_Id, T224_D021_Id, T224_Flag_Status, T224_C004_Id
FROM T224
left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
WHERE T224_Data_Emissao >= "{$dataInicialDados}"
  AND (T224_T224_Id_Agrupado<=0 OR T224_T224_Id_Agrupado is null)
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	// A nível de produto na NF
	$sql2 = "SELECT * FROM T225 WHERE T225_T224_Id = '{$row['T224_Id']}';";
	$result2 = mysqli_query($sql2);
	if (!$result2) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql2); }
	while ($row2 = mysqli_fetch_assoc($result2)) {
		// Dimensões
		$FAC001_DIM001_Id = addData($row['T224_Data_Emissao'], 'DIM001');
		$FAC001_DIM002_Id = addClienteFornecedor($row['T224_D024_Id']);
		$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T224_D024_Id']);
		$FAC001_DIM004_Id_Vendedor = addUsuario($row['T224_C007_Id_Vendedor_Interno']);
		$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
		$FAC001_DIM005_Id = false;
		$FAC001_DIM006_Id = false;
		$FAC001_DIM007_Id = false;
		$FAC001_DIM008_Id = addFiltros($row2['T225_D006_Id']);
		$FAC001_DIM009_Id = addProduto($row2['T225_D009_Id']);
		$FAC001_DIM010_Id = addCFOP($row2['T225_D006_Id']);
		$FAC001_DIM011_Id = addRepresentante($row['T224_D021_Id']);
		$FAC001_DIM012_Id = addEmpresa($row['T224_C004_Id']);
		$FAC001_DIM013_Id = false;
		$FAC001_FLAG_CANCELADO = $row['T224_Flag_Status'] == 8 ? 'S' : 'N';
		
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_ORDCOMP_Valor_Total_Preco', $row2['T225_Valor_Total_Preco']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_ORDCOMP_Produtos_Quantidade_Itens', 1);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_ORDCOMP_Produtos_Quantidade', $row2['T225_Quantidade']);
		$count++;
		if (($count % 1000) == 0) {
			echo "($count) ";
		}
	}
	
	// Dimensões
	$FAC001_DIM001_Id = addData($row['T224_Data_Emissao'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T224_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T224_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = addUsuario($row['T224_C007_Id_Vendedor_Interno']);
	$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
	$FAC001_DIM005_Id = false;
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = false;
	$FAC001_DIM008_Id = addFiltros($row['T224_D006_Id']);
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = addCFOP($row['T224_D006_Id']);
	$FAC001_DIM011_Id = addRepresentante($row['T224_D021_Id']);
	$FAC001_DIM012_Id = addEmpresa($row['T224_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = $row['T224_Flag_Status'] == 11 ? 'S' : 'N';
		
	// FAC001_NF_Quantidade = 1
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_ORDCOMP_Quantidade', 1);
}
echo "\n<br />";
*/
/*
Cotações
---------------------------
*** Dimensões ***
Data (1): 				T218_Data_Emissao
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		T218_D024_Id
Local:					T218_D024_Id
Usuários (Vendedor):	T218_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				T219_D009_Id
Conta:					-
CFOP:					T218_D006_Id 
Representante:			T218_D021_Id
Flag Cancelado:			T218_Flag_Perdido = 'S'
*/
/*
echo "Cotacoes...\n<br />";
$sql = <<<EOT
SELECT 
	T218_Id, T218_Data_Emissao, T218_D024_Id, T218_C007_Id_Vendedor_Interno, C007_C007_Id, T218_D006_Id, T218_D021_Id, T218_Flag_Perdido, T218_C004_Id
FROM T218
left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno
WHERE T218_Data_Emissao >= "{$dataInicialDados}"
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	// A nível de produto na NF
	$sql2 = "SELECT * FROM T219 WHERE T219_T218_Id = '{$row['T218_Id']}';";
	$result2 = mysqli_query($sql2);
	if (!$result2) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql2); }
	while ($row2 = mysqli_fetch_assoc($result2)) {
		// Dimensões
		$FAC001_DIM001_Id = addData($row['T218_Data_Emissao'], 'DIM001');
		$FAC001_DIM002_Id = addClienteFornecedor($row['T218_D024_Id']);
		$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T218_D024_Id']);
		$FAC001_DIM004_Id_Vendedor = addUsuario($row['T218_C007_Id_Vendedor_Interno']);
		$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
		$FAC001_DIM005_Id = false;
		$FAC001_DIM006_Id = false;
		$FAC001_DIM007_Id = false;
		$FAC001_DIM008_Id = addFiltros($row2['T219_D006_Id']);
		$FAC001_DIM009_Id = addProduto($row2['T219_D009_Id']);
		$FAC001_DIM010_Id = addCFOP($row2['T219_D006_Id']);
		$FAC001_DIM011_Id = addRepresentante($row['T218_D021_Id']);
		$FAC001_DIM012_Id = addEmpresa($row['T218_C004_Id']);
		$FAC001_DIM013_Id = false;
		$FAC001_FLAG_CANCELADO = $row['T218_Flag_Perdido'] == 'S' ? 'S' : 'N';
		
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_COT_Valor_Total_Preco', $row2['T219_Valor_Total_Preco']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_COT_Produtos_Quantidade_Itens', 1);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_COT_Produtos_Quantidade', $row2['T219_Quantidade']);
		$count++;
		if (($count % 1000) == 0) {
			echo "($count) ";
		}
	}
	
	// Dimensões
	$FAC001_DIM001_Id = addData($row['T218_Data_Emissao'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T218_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T218_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = addUsuario($row['T218_C007_Id_Vendedor_Interno']);
	$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
	$FAC001_DIM005_Id = false;
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = false;
	$FAC001_DIM008_Id = addFiltros($row['T218_D006_Id']);
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = addCFOP($row['T218_D006_Id']);
	$FAC001_DIM011_Id = addRepresentante($row['T218_D021_Id']);
	$FAC001_DIM012_Id = addEmpresa($row['T218_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = $row['T218_Flag_Perdido'] == 'S' ? 'S' : 'N';
		
	// FAC001_NF_Quantidade = 1
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_COT_Quantidade', 1);
}
echo "\n<br />";
*/
/*
Notas Fiscais Recebidas
---------------------------
*** Dimensões ***
Data (1): 				T013_Data_Emissao
Data (2):				T013_Data_Entrada
Data (3):				-
Cliente/Fornecedor:		T013_D024_Id
Local:					T013_D024_Id
Usuários (Vendedor):	T224_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				T014_D009_Id
Conta:					-
CFOP:					T013_D006_Id
Representante:			T013_D021_Id
Flag Cancelado:			T013_Flag_Cancelada = 'S'
*/
/*
echo "Notas Fiscais Recebidas...\n<br />";
$sql = <<<EOT
SELECT 
	T013_Id, T013_Data_Emissao, T013_Data_Entrada, T013_D024_Id, T224_C007_Id_Vendedor_Interno, C007_C007_Id, T013_D006_Id, T013_D021_Id, T013_Flag_Cancelada, T013_C004_Id
FROM T013
left join T014 on T014_T013_Id=T013_Id
left join T012 on T012_Id=T014_T012_Id
left join T225 on T225_Id=T014_T225_Id
left join T224 on T224_Id=T225_T224_Id
left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
WHERE T013_Data_Emissao >= "{$dataInicialDados}"
GROUP BY T013_Id
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	// A nível de produto na NF
	$sql2 = "SELECT * FROM T014 WHERE T014_T013_Id = '{$row['T013_Id']}';";
	$result2 = mysqli_query($sql2);
	if (!$result2) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql2); }
	while ($row2 = mysqli_fetch_assoc($result2)) {
		// Dimensões
		$FAC001_DIM001_Id = addData($row['T013_Data_Emissao'], 'DIM001');
		$FAC001_DIM002_Id = addClienteFornecedor($row['T013_D024_Id']);
		$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T013_D024_Id']);
		$FAC001_DIM004_Id_Vendedor = addUsuario($row['T224_C007_Id_Vendedor_Interno']);
		$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
		$FAC001_DIM005_Id = addData($row['T013_Data_Entrada'], 'DIM005');
		$FAC001_DIM006_Id = false;
		$FAC001_DIM007_Id = false;
		$FAC001_DIM008_Id = addFiltros($row2['T014_D006_Id']);
		$FAC001_DIM009_Id = addProduto($row2['T014_D009_Id']);
		$FAC001_DIM010_Id = addCFOP($row2['T014_D006_Id']);
		$FAC001_DIM011_Id = addRepresentante($row['T013_D021_Id']);
		$FAC001_DIM012_Id = addEmpresa($row['T013_C004_Id']);
		$FAC001_DIM013_Id = false;
		$FAC001_FLAG_CANCELADO = $row['T013_Flag_Cancelada'] == 'S' ? 'S' : 'N';

		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_Valor_Total_Custo', $row2['T014_Valor_Total_Custo']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_Produtos_Quantidade_Itens', 1);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_Produtos_Quantidade', $row2['T014_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_ICMS', $row2['T014_Valor_ICMS']/$row2['T014_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_ICMS_ST', $row2['T014_Valor_ICMS_Substituicao_Unitario']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_ICMS_IPI', $row2['T014_Valor_IPI']/$row2['T014_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_ICMS_PIS', $row2['T014_Valor_PIS']/$row2['T014_Quantidade']);
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_ICMS_COFINS', $row2['T014_Valor_COFINS']/$row2['T014_Quantidade']);
		$count++;
		if (($count % 1000) == 0) {
			echo "($count) ";
		}
	}
	
	// Dimensões
	$FAC001_DIM001_Id = addData($row['T013_Data_Emissao'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T013_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T013_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = addUsuario($row['T224_C007_Id_Vendedor_Interno']);
	$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
	$FAC001_DIM005_Id = addData($row['T013_Data_Entrada'], 'DIM005');
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = false;
	$FAC001_DIM008_Id = addFiltros($row['T013_D006_Id']);
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = addCFOP($row['T013_D006_Id']);
	$FAC001_DIM011_Id = addRepresentante($row['T013_D021_Id']);
	$FAC001_DIM012_Id = addEmpresa($row['T013_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = $row['T013_Flag_Cancelada'] == 'S' ? 'S' : 'N';
		
	// FAC001_NF_Quantidade = 1
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NFR_Quantidade', 1);
}
echo "\n<br />";
*/
/*
Notas Fiscais
---------------------------
*** Dimensões ***
Data (1): 				T007_Data_Emissao
Data (2):				T007_Data_Saida
Data (3):				T007_Data_Vencimento
Cliente/Fornecedor:		T007_D024_Id
Local:					T007_D024_Id
Usuários (Vendedor):	T007_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				T008_D009_Id
Conta:					-
CFOP:					T007_D006_Id
Flag Cancelado:			T007_Flag_Cancelada = 'S'
*/


/* ----------- NOTA DE VENDAS --------------- */
$anos = 4;
if(isset($anosProcessarBI)){
	// essa variavel pode ser definida no confUsuario
	$anos = $anosProcessarBI;
}

$anoPesquisa = date('Y');
$mesPesquisa = date('m');
echo "***** Notas Fiscais de Venda...";
$T007_Ids = array();
while ($anos > 0) {
    while ($mesPesquisa > 0) {
        // Corrigir: MySQL server has gone away
        $con = mysqli_init();
        $con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3600);
        $con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
        $g['conexaoBanco'] = $con;

        $ultimoDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT) . '-' . date("t", mktime(0,0,0,$mesPesquisa,'01',$anoPesquisa));
        $primeiroDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT)  . '-' . '01';

        echo "\n<br/>Pesquisando {$primeiroDiaMes} - {$ultimoDiaMes}\n<br />";
		$sql = <<<EOT
			SELECT /*+max_execution_time(210000)*/
				T007_Id, 
				T007_Valor_Total, 
				T007_Data_Emissao, 
				T007_Data_Saida, 
				T007_Data_Vencimento, 
				T007_D024_Id, 
				T007_C007_Id_Vendedor_Interno, 
				C007_C007_Id, 
				T007_D006_Id, 
				T007_D021_Id, 
				T007_Flag_Cancelada, 
				T007_C004_Id,
				T008_D006_Id, 
				T008_D009_Id,
				T008_Valor_Total_Preco,
				T008_Quantidade,
				T008_Valor_ICMS,
				T008_Valor_ICMS_Substituicao_Retencao,
				T008_Valor_IPI,
				T008_Valor_PIS,
				T008_Valor_COFINS
			FROM T007
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T008 ON T008_T007_Id=T007_Id
			LEFT JOIN D006 ON D006_Id=T008_D006_Id
			WHERE T007_Data_Emissao >= '{$primeiroDiaMes}' and T007_Data_Emissao <= '{$ultimoDiaMes}'
			AND T007_Flag_Cancelada = 'N'
			AND T007_Flag_Entrada_Saida = 'S'
			AND D006_Flag_Venda_Compra_Outros='V'
			AND D006_Flag_Devolucao != 'S'
EOT;
		$result = mysqli_query($sql);
		$mesPesquisa--;
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			// Por produto (T008)
			$FAC001_DIM001_Id = addData($row['T007_Data_Emissao'], 'DIM001');
			$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
			$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
			$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
			$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
			$FAC001_DIM005_Id = addData($row['T007_Data_Saida'], 'DIM005');
			$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
			$FAC001_DIM007_Id = false;
			$FAC001_DIM008_Id = addFiltros($row['T008_D006_Id']);
			$FAC001_DIM009_Id = addProduto($row['T008_D009_Id']);
			$FAC001_DIM010_Id = addCFOP($row['T008_D006_Id']);
			$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
			$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
			$FAC001_DIM013_Id = false;
			$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Valor_Total_Preco', $row['T008_Valor_Total_Preco']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade_Itens', 1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade', $row['T008_Quantidade']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS', $row['T008_Valor_ICMS']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS_ST', $row['T008_Valor_ICMS_Substituicao_Retencao']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_IPI', $row['T008_Valor_IPI']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_PIS', $row['T008_Valor_PIS']);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_COFINS', $row['T008_Valor_COFINS']);

			$count++;
			if (($count % 1000) == 0) {
				echo "($count) ";
			}

			if (!isset($T007_Ids[$row['T007_Id']])) {
				$T007_Ids[$row['T007_Id']] = true;

				// Por nota (T007)
				$FAC001_DIM001_Id = addData($row['T007_Data_Emissao'], 'DIM001');
				$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
				$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
				$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
				$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
				$FAC001_DIM005_Id = addData($row['T007_Data_Saida'], 'DIM005');
				$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
				$FAC001_DIM007_Id = false;
				$FAC001_DIM008_Id = addFiltros($row['T007_D006_Id']);
				$FAC001_DIM009_Id = false;
				$FAC001_DIM010_Id = addCFOP($row['T007_D006_Id']);
				$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
				$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
				$FAC001_DIM013_Id = false;
				$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

				incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Quantidade', 1);
			}

		}
	}
	$anoPesquisa--;
	$anos--;
	$mesPesquisa=12;
}
unset($T007_Ids);
echo "\n<br />";
/* ---------------------------------------------------------------------- */


/* ----------- NOTA DE DEVOLUÇÃO EMITIDAS --------------- */
$anos = 4;
$anoPesquisa = date('Y');
$mesPesquisa = date('m');
echo "***** Notas Fiscais de Devolução Emitidas...";
$T007_Ids = array();
while ($anos > 0) {
	while ($mesPesquisa > 0) {
		// Corrigir: MySQL server has gone away
		$con = mysqli_init();
		$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3600);
		$con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
		$g['conexaoBanco'] = $con;

		$ultimoDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT) . '-' . date("t", mktime(0,0,0,$mesPesquisa,'01',$anoPesquisa));
		$primeiroDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT)  . '-' . '01';

		echo "\n<br/>Pesquisando {$primeiroDiaMes} - {$ultimoDiaMes}\n<br />";
		$sql = <<<EOT
			SELECT /*+max_execution_time(210000)*/
				T007_Id, 
				T007_Valor_Total, 
				T007_Data_Emissao, 
				T007_Data_Saida, 
				T007_Data_Vencimento, 
				T007_D024_Id, 
				T007_C007_Id_Vendedor_Interno, 
				C007_C007_Id, 
				T007_D006_Id, 
				T007_D021_Id, 
				T007_Flag_Cancelada, 
				T007_C004_Id,
				T008_D006_Id, 
				T008_D009_Id,
				T008_Valor_Total_Preco,
				T008_Quantidade,
				T008_Valor_ICMS,
				T008_Valor_ICMS_Substituicao_Retencao,
				T008_Valor_IPI,
				T008_Valor_PIS,
				T008_Valor_COFINS
			FROM T007
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T008 ON T008_T007_Id=T007_Id
			LEFT JOIN D006 ON D006_Id=T008_D006_Id
			WHERE T007_Data_Emissao >= '{$primeiroDiaMes}' and T007_Data_Emissao <= '{$ultimoDiaMes}'
			AND T007_Flag_Cancelada = 'N'
			AND T007_Flag_Entrada_Saida = 'E'
			AND D006_Flag_Venda_Compra_Outros='DV'
			AND D006_Flag_Devolucao = 'S'
EOT;
		$result = mysqli_query($sql);
		$mesPesquisa--;
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			// Por produto (T008)
			$FAC001_DIM001_Id = addData($row['T007_Data_Emissao'], 'DIM001');
			$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
			$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
			$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
			$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
			$FAC001_DIM005_Id = addData($row['T007_Data_Saida'], 'DIM005');
			$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
			$FAC001_DIM007_Id = false;
			$FAC001_DIM008_Id = addFiltros($row['T008_D006_Id']);
			$FAC001_DIM009_Id = addProduto($row['T008_D009_Id']);
			$FAC001_DIM010_Id = addCFOP($row['T008_D006_Id']);
			$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
			$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
			$FAC001_DIM013_Id = false;
			$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Valor_Total_Preco', $row['T008_Valor_Total_Preco'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade_Itens', 0);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade', $row['T008_Quantidade'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS', $row['T008_Valor_ICMS'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS_ST', $row['T008_Valor_ICMS_Substituicao_Retencao'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_IPI', $row['T008_Valor_IPI'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_PIS', $row['T008_Valor_PIS'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_COFINS', $row['T008_Valor_COFINS'] * -1);

			$count++;
			if (($count % 1000) == 0) {
				echo "($count) ";
			}

			if (!isset($T007_Ids[$row['T007_Id']])) {
				$T007_Ids[$row['T007_Id']] = true;

				// Por nota (T007)
				$FAC001_DIM001_Id = addData($row['T007_Data_Emissao'], 'DIM001');
				$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
				$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
				$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
				$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
				$FAC001_DIM005_Id = addData($row['T007_Data_Saida'], 'DIM005');
				$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
				$FAC001_DIM007_Id = false;
				$FAC001_DIM008_Id = addFiltros($row['T007_D006_Id']);
				$FAC001_DIM009_Id = false;
				$FAC001_DIM010_Id = addCFOP($row['T007_D006_Id']);
				$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
				$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
				$FAC001_DIM013_Id = false;
				$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

				incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Quantidade', 1);
			}

		}
	}
	$anoPesquisa--;
	$anos--;
	$mesPesquisa=12;
}
unset($T007_Ids);
echo "\n<br />";
/* ---------------------------------------------------------------------- */

/* ----------- NOTA DE DEVOLUÇÃO RECEBIDAS --------------- */
$anos = 4;
$anoPesquisa = date('Y');
$mesPesquisa = date('m');
echo "***** Notas Fiscais de Devolução Recebidas...";
$T007_Ids = array();
while ($anos > 0) {
	while ($mesPesquisa > 0) {
		// Corrigir: MySQL server has gone away
		$con = mysqli_init();
		$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3600);
		$con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
		$g['conexaoBanco'] = $con;

		$ultimoDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT) . '-' . date("t", mktime(0,0,0,$mesPesquisa,'01',$anoPesquisa));
		$primeiroDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT)  . '-' . '01';

		echo "\n<br/>Pesquisando {$primeiroDiaMes} - {$ultimoDiaMes}\n<br />";
		$sql = <<<EOT
			SELECT /*+max_execution_time(210000)*/
				T013_Id AS 'T007_Id', 
				T013_Valor_Total AS 'T007_Valor_Total', 
				T013_Data_Emissao AS 'T007_Data_Emissao', 
				T013_Data_Emissao AS 'T007_Data_Saida', 
				'0000-00-00' AS 'T007_Data_Vencimento', 
				T013_D024_Id AS 'T007_D024_Id', 
				T013_C007_Id_Vendedor_Retorno AS 'T007_C007_Id_Vendedor_Interno', 
				C007_C007_Id, 
				T013_D006_Id AS 'T007_D006_Id', 
				T013_D021_Id AS 'T007_D021_Id', 
				T013_Flag_Cancelada AS 'T007_Flag_Cancelada', 
				T013_C004_Id AS 'T007_C004_Id',
				T014_D006_Id AS 'T008_D006_Id', 
				T014_D009_Id AS 'T008_D009_Id',
				T014_Valor_Total_Preco_Recebida AS 'T008_Valor_Total_Preco',
				T014_Quantidade AS 'T008_Quantidade',
				T014_Valor_ICMS AS 'T008_Valor_ICMS',
				T014_Valor_ICMS_Substituicao_Retencao_Recebida AS 'T008_Valor_ICMS_Substituicao_Retencao',
				T014_Valor_IPI AS 'T008_Valor_IPI',
				T014_Valor_PIS AS 'T008_Valor_PIS',
				T014_Valor_COFINS AS 'T008_Valor_COFINS'
			FROM T013
			LEFT JOIN C007 ON C007_Id=T013_C007_Id_Vendedor_Retorno
			LEFT JOIN T014 ON T014_T013_Id=T013_Id
			LEFT JOIN D006 ON D006_Id=T014_D006_Id
			WHERE T013_Data_Emissao >= '{$primeiroDiaMes}' and T013_Data_Emissao <= '{$ultimoDiaMes}'
			AND T013_Flag_Cancelada = 'N'
			AND D006_Flag_Venda_Compra_Outros='DV'
			AND D006_Flag_Devolucao = 'S'
EOT;
		$result = mysqli_query($sql);
		$mesPesquisa--;
		$count = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			// Por produto (T008)
			$FAC001_DIM001_Id = addData(substr($row['T007_Data_Emissao'],0,10), 'DIM001');
			$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
			$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
			$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
			$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
			$FAC001_DIM005_Id = addData(substr($row['T007_Data_Saida'],0,10), 'DIM005');
			$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
			$FAC001_DIM007_Id = false;
			$FAC001_DIM008_Id = addFiltros($row['T008_D006_Id']);
			$FAC001_DIM009_Id = addProduto($row['T008_D009_Id']);
			$FAC001_DIM010_Id = addCFOP($row['T008_D006_Id']);
			$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
			$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
			$FAC001_DIM013_Id = false;
			$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Valor_Total_Preco', $row['T008_Valor_Total_Preco'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade_Itens', 0);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Produtos_Quantidade', $row['T008_Quantidade'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS', $row['T008_Valor_ICMS'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_ICMS_ST', $row['T008_Valor_ICMS_Substituicao_Retencao'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_IPI', $row['T008_Valor_IPI'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_PIS', $row['T008_Valor_PIS'] * -1);
			incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_COFINS', $row['T008_Valor_COFINS'] * -1);

			$count++;
			if (($count % 1000) == 0) {
				echo "($count) ";
			}

			if (!isset($T007_Ids[$row['T007_Id']])) {
				$T007_Ids[$row['T007_Id']] = true;

				// Por nota (T007)
				$FAC001_DIM001_Id = addData(substr($row['T007_Data_Emissao'],0,10), 'DIM001');
				$FAC001_DIM002_Id = addClienteFornecedor($row['T007_D024_Id']);
				$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T007_D024_Id']);
				$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
				$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
				$FAC001_DIM005_Id = addData(substr($row['T007_Data_Saida'],0,10), 'DIM005');
				$FAC001_DIM006_Id = addData($row['T007_Data_Vencimento'], 'DIM006');
				$FAC001_DIM007_Id = false;
				$FAC001_DIM008_Id = addFiltros($row['T007_D006_Id']);
				$FAC001_DIM009_Id = false;
				$FAC001_DIM010_Id = addCFOP($row['T007_D006_Id']);
				$FAC001_DIM011_Id = addRepresentante($row['T007_D021_Id']);
				$FAC001_DIM012_Id = addEmpresa($row['T007_C004_Id']);
				$FAC001_DIM013_Id = false;
				$FAC001_FLAG_CANCELADO = $row['T007_Flag_Cancelada'] == 'S' ? 'S' : 'N';

				incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_NF_Quantidade', 1);
			}

		}
	}
	$anoPesquisa--;
	$anos--;
	$mesPesquisa=12;
}
unset($T007_Ids);
echo "\n<br />";
/* ---------------------------------------------------------------------- */

/*
Orçamento
---------------------------
*** Dimensões ***
Data (1): 				T003_Data_Emissao
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		T003_D024_Id
Local:					T003_D024_Id
Usuários (Vendedor):	T003_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				T004_D009_Id
Conta:					-
*** Fatos ***
T004_Valor_Total_Preco (não mais: T003_Valor_Total)
Flag Cancelado:			T003_Flag_Perdido = 'S'
*/
/*
echo "Orcamentos...\n<br />";
$sql = <<<EOT
SELECT
	T003_Id, T003_D024_Id, T003_Data_Emissao, T003_Valor_Total, T003_C007_Id_Vendedor_Interno, T003_D006_Id, C007_C007_Id, T003_D021_Id, T003_Flag_Perdido, T003_C004_Id
FROM T003
LEFT JOIN C007 ON C007_Id = T003_C007_Id_Vendedor_Interno
WHERE T003_Data_Emissao >= "{$dataInicialDados}"
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	// A nível de produto
	$sql = "SELECT T004_D009_Id, T004_Valor_Total_Preco, T004_D006_Id FROM T004 WHERE T004_T003_Id = '{$row['T003_Id']}';";
	$result2 = mysqli_query($sql);
	if (!$result2) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
	while ($row2 = mysqli_fetch_assoc($result2)) {
		$FAC001_DIM001_Id = addData($row['T003_Data_Emissao'], 'DIM001');
		$FAC001_DIM002_Id = addClienteFornecedor($row['T003_D024_Id']);
		$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T003_D024_Id']);
		$FAC001_DIM004_Id_Vendedor = addUsuario($row['T003_C007_Id_Vendedor_Interno']);
		$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
		$FAC001_DIM005_Id = false;
		$FAC001_DIM006_Id = false;
		$FAC001_DIM007_Id = false;
		$FAC001_DIM008_Id = false;
		$FAC001_DIM009_Id = addProduto($row2['T004_D009_Id']);
		$FAC001_DIM010_Id = addCFOP($row2['T004_D006_Id']);
		$FAC001_DIM011_Id = addRepresentante($row['T003_D021_Id']);
		$FAC001_DIM012_Id = addEmpresa($row['T003_C004_Id']);
		$FAC001_DIM013_Id = false;
		$FAC001_FLAG_CANCELADO = $row['T003_Flag_Perdido'] == 'S' ? 'S' : 'N';
		
		// Fatos:
		// T004_Valor_Total_Preco
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_Orcamento_Valor_Total_Preco', $row2['T004_Valor_Total_Preco']);
		$count++;
		if (($count % 1000) == 0) {
			echo "($count) ";
		}
	}
}
echo "\n<br />";
*/
/*
Contas a Receber
---------------------------
*** Dimensões ***
Data (1): 				T002_Data_Emissao
Data (2):				T002_Data_Vencimento
Data (3):				T002_Data_Recebimento
Cliente/Fornecedor:		T002_D024_Id
Local:					T002_D024_Id
Usuários (Vendedor):	-
Usuários (Superior):	-
Produto:				-
Conta:					T002_D014_Id
*** Fatos ***
T002_Valor_Duplicata
Flag Cancelado:			-
*/
/*
echo "Contas a Receber...\n<br />";
$sql = <<<EOT
	SELECT T002_Id, T002_Valor_Duplicata, T002_Valor_Total, T002_Data_Emissao, T002_Data_Vencimento, T002_Data_Recebimento, T002_D024_Id, T002_D014_Id, T002_D021_Id, T007_C007_Id_Vendedor_Interno, C007_C007_Id FROM T002
	LEFT JOIN T007 ON T007_Id=T002_T007_Id
	LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
	WHERE T002_Data_Emissao >= "{$dataInicialDados}"
	  AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
	
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	$FAC001_DIM001_Id = addData($row['T002_Data_Emissao'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T002_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T002_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
	$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
	$FAC001_DIM005_Id = addData($row['T002_Data_Vencimento'], 'DIM005');
	$FAC001_DIM006_Id = addData($row['T002_Data_Recebimento'], 'DIM006');
	$FAC001_DIM007_Id = addConta($row['T002_D014_Id']);
	$FAC001_DIM008_Id = false;
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = false;
	$FAC001_DIM011_Id = addRepresentante($row['T002_D021_Id']);
	$FAC001_DIM012_Id = false;
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = 'N';
	
	// Fatos:
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_CR_Valor_Duplicata', $row['T002_Valor_Total']);
	$count++;
	if (($count % 1000) == 0) {
		echo "($count) ";
	}
}
echo "\n<br />";
*/
/*
Contas a Pagar
---------------------------
*** Dimensões ***
Data (1): 				T015_Data_Emissao
Data (2):				T015_Data_Vencimento
Data (3):				T015_Data_Pagamento
Cliente/Fornecedor:		T015_D024_Id
Local:					T015_D024_Id
Usuários (Vendedor):	-
Usuários (Superior):	-
Produto:				-
Conta:					T015_D014_Id
*** Fatos ***
T015_Valor_Duplicata
Flag Cancelado:			-
*/
/*
echo "Contas a Pagar...\n<br />";
$sql = <<<EOT
SELECT
	T015_Id, T015_Valor_Duplicata, T015_Data_Emissao, T015_Data_Vencimento, T015_Data_Pagamento, T015_D014_Id, T015_D024_Id, T015_D021_Id, T007_C007_Id_Vendedor_Interno, C007_C007_Id, T015_C004_Id
FROM T015
LEFT JOIN T007 ON T007_Id=T015_T007_Id
LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
WHERE T015_Data_Emissao >= "{$dataInicialDados}"
  AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	$FAC001_DIM001_Id = addData($row['T015_Data_Emissao'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T015_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T015_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = addUsuario($row['T007_C007_Id_Vendedor_Interno']);
	$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
	$FAC001_DIM005_Id = addData($row['T015_Data_Vencimento'], 'DIM005');
	$FAC001_DIM006_Id = addData($row['T015_Data_Pagamento'], 'DIM006');
	$FAC001_DIM007_Id = addConta($row['T015_D014_Id']);
	$FAC001_DIM008_Id = false;
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = false;
	$FAC001_DIM011_Id = addRepresentante($row['T015_D021_Id']);
	$FAC001_DIM012_Id = addEmpresa($row['T015_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = 'N';
	
	// Fatos:
	// T015_Valor_Duplicata
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_CP_Valor_Duplicata', $row['T015_Valor_Duplicata']);
	$count++;
	if (($count % 1000) == 0) {
		echo "($count) ";
	}
}
echo "\n<br />";
*/
/*
Lançamentos Bancários / Caixa
---------------------------
*** Dimensões ***
Data (1): 				T019_Data_Lancamento
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		T019_D024_Id
Local:					T019_D024_Id
Usuários (Vendedor):	-
Usuários (Superior):	-
Produto:				-
Conta:					T019_D014_Id
*** Fatos ***
T019_Valor_Lancamento
Flag Cancelado:			-
*/
/*
echo "Lançamentos Bancários...\n<br />";
$sql = "SELECT * FROM T019 LEFT JOIN D007 ON D007_Id = T019_D007_Id WHERE T019_Data_Lancamento >= '$dataInicialDados' GROUP BY T019_Id ORDER BY T019_Id ASC";
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	$FAC001_DIM001_Id = addData($row['T019_Data_Lancamento'], 'DIM001');
	$FAC001_DIM002_Id = addClienteFornecedor($row['T019_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T019_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = false;
	$FAC001_DIM004_Id_Supervisor = false;
	$FAC001_DIM005_Id = false;
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = addConta($row['T019_D014_Id'], $row['T019_Flag_Deb_Crd']);
	$FAC001_DIM008_Id = false;
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = false;
	$FAC001_DIM011_Id = false;
	$FAC001_DIM012_Id = addEmpresa($row['D007_C004_Id']);
	$FAC001_DIM013_Id = addCaixaBanco($row['T019_D007_Id']);
	$FAC001_FLAG_CANCELADO = 'N';
	
	// Fatos:
	// T019_Valor_Lancamento
	$saldoInicial = $row['T019_Saldo'];
	$row['T019_Valor_Lancamento'] = ($row['T019_Valor_Lancamento'] < 0) ? $row['T019_Valor_Lancamento'] * -1 : $row['T019_Valor_Lancamento'];
	if($row['T019_Flag_Deb_Crd'] == 0){
		$saldoInicial += $row['T019_Valor_Lancamento'];
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_LB_Valor_Lancamento_Pago', $row['T019_Valor_Lancamento']);
	} else {
		$saldoInicial -= $row['T019_Valor_Lancamento'];
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_LB_Valor_Lancamento_Recebido', $row['T019_Valor_Lancamento']);
	}
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_LB_Valor_Saldo_Inicial', $saldoInicial, 'S', null);
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_LB_Valor_Saldo', $row['T019_Saldo'], 'S', false);
	$count++;
	if (($count % 1000) == 0) {
		echo "($count) ";
	}
}
echo "\n<br />";
*/
/*
Projeção Orçamentaria
---------------------------
*** Dimensões ***
Data (1): 				T018_Ano-T018_Mes-D014_Dia_Vencimento
Data (2):				-
Data (3):				-
Cliente/Fornecedor:		D014_D024_Id
Local:					D014_D024_Id
Usuários (Vendedor):	-
Usuários (Superior):	-
Produto:				-
Conta:					D014_Id
*** Fatos ***
T018_Valor
T018_Valor_Saldo
Flag Cancelado:			-
*/
/*
echo "Projeção Orçamentaria...\n<br />";
$sql = "SELECT T018_Id, CONCAT(T018_Ano,'-',T018_Mes,'-',D014_Dia_Vencimento) AS Data_Vencimento, D014_D024_Id, D014_Id, T018_Valor, T018_Valor_Saldo, T018_C004_Id FROM T018 LEFT JOIN D014 ON D014_Id=T018_D014_Id";
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	$FAC001_DIM001_Id = false;
	$FAC001_DIM002_Id = addClienteFornecedor($row['D014_D024_Id']);
	$FAC001_DIM003_Id = addClienteFornecedorLocal($row['D014_D024_Id']);
	$FAC001_DIM004_Id_Vendedor = false;
	$FAC001_DIM004_Id_Supervisor = false;
	$FAC001_DIM005_Id = preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $row['Data_Vencimento']) ? addData($row['Data_Vencimento'], 'DIM005') : false;
	$FAC001_DIM006_Id = false;
	$FAC001_DIM007_Id = addConta($row['D014_Id']);
	$FAC001_DIM008_Id = false;
	$FAC001_DIM009_Id = false;
	$FAC001_DIM010_Id = false;
	$FAC001_DIM011_Id = false;
	$FAC001_DIM012_Id = addEmpresa($row['T018_C004_Id']);
	$FAC001_DIM013_Id = false;
	$FAC001_FLAG_CANCELADO = 'N';
	
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_PROJ_Valor_Projetado', $row['T018_Valor']);
	incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_PROJ_Valor_Saldo', $row['T018_Valor_Saldo']);
	$count++;
	if (($count % 1000) == 0) {
		echo "($count) ";
	}
}
echo "\n<br />";
*/
/*
Pedidos
---------------------------
*** Dimensões ***
Data (1): 				T005_Data_Emissao
Data (2):				T005_Data_Saida
Data (3):				T005_Data_Entrega
Cliente/Fornecedor:		T005_D024_Id
Local:					T005_D024_Id
Usuários (Vendedor):	T005_C007_Id_Vendedor_Interno
Usuários (Superior):	C007_C007_Id
Produto:				-
Conta:					-
*** Fatos ***
T006_Valor_Total_Preco (não mais T005_Valor_Total)
Flag Cancelado:			T005_Flag_Status = '8'
*/
/*
echo "Pedidos...\n<br />";
$sql = <<<EOT
SELECT
	T005_Id, T005_Data_Emissao, T005_Data_Saida, T005_Data_Entrega, T005_D024_Id, T005_C007_Id_Vendedor_Interno, C007_C007_Id, T005_Valor_Total, T005_D021_Id, T005_C004_Id
FROM T005
LEFT JOIN C007 ON C007_Id = T005_C007_Id_Vendedor_Interno
WHERE T005_Data_Emissao >= "{$dataInicialDados}"
  AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
EOT;
$result = mysqli_query($sql);
if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	// A nível de produto
	$sql = "SELECT T006_D009_Id, T006_Valor_Total_Preco, T006_D006_Id FROM T006 WHERE T006_T005_Id = '{$row['T005_Id']}';";
	$result2 = mysqli_query($sql);
	if (!$result2) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
	while ($row2 = mysqli_fetch_assoc($result2)) {
		$FAC001_DIM001_Id = addData($row['T005_Data_Emissao'], 'DIM001');
		$FAC001_DIM002_Id = addClienteFornecedor($row['T005_D024_Id']);
		$FAC001_DIM003_Id = addClienteFornecedorLocal($row['T005_D024_Id']);
		$FAC001_DIM004_Id_Vendedor = addUsuario($row['T005_C007_Id_Vendedor_Interno']);
		$FAC001_DIM004_Id_Supervisor = addUsuario($row['C007_C007_Id']);
		$FAC001_DIM005_Id = addData($row['T005_Data_Saida'], 'DIM005');
		$FAC001_DIM006_Id = addData($row['T005_Data_Entrega'], 'DIM006');
		$FAC001_DIM007_Id = false;
		$FAC001_DIM008_Id = false;
		$FAC001_DIM009_Id = addProduto($row2['T006_D009_Id']);
		$FAC001_DIM010_Id = addCFOP($row2['T006_D006_Id']);
		$FAC001_DIM011_Id = addRepresentante($row['T005_D021_Id']);
		$FAC001_DIM012_Id = addEmpresa($row['T005_C004_Id']);
		$FAC001_DIM013_Id = false;
		$FAC001_FLAG_CANCELADO = $row['T005_Flag_Status'] == 8 ? 'S' : 'N';
		
		// Fatos:
		// T006_Valor_Total_Preco
		incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, 'FAC001_Pedidos_Valor_Total_Preco', $row2['T006_Valor_Total_Preco']);
		$count++;
		if (($count % 1000) == 0) {
			echo "($count) ";
		}
	}
}
echo "\n<br />";
*/

processarIncluirFatosEnd();

echo "The End! " . date("H:i:s"). "\n<br />";
unset($cache);
