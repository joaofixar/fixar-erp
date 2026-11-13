<?php
namespace hardness;

include('sessao.php');

$quantidade = $_POST['quantidade'];
if(($quantidade == NULL ) || ($quantidade == 0)){
	header('Location: busca.php?incorreto=true');
} else{
	$opcao = 'NULL';
	if(isset($_POST['codProduto']))
		$codigo = $_POST['codProduto'];

	if(isset($_POST['voltar']))
		header("Location: index.php");
	if(isset($_POST['somar'])){
		$opcao = "E";
		$operacao = "ENTRADA";
	}
	if(isset($_POST['subtrair'])){
		$opcao = "S";
		$operacao = "SAIDA";
	}
	if(isset($_POST['balanco'])){
		$opcao = "B";
		$operacao = "BALANCO";
	}

	//seleciona o ultimo ID e acrescenta mais um para fazer o insert na T001 
	/*$sql = mysql_query("select MAX(T029_Id) as T029_Id from T029") or die (mysql_error()); 
	$row = mysql_fetch_array($sql);
	$result = $row['T029_Id'];
	$result = $result + 1;*/

	$sql = NULL; 

	//seleciona o ultimo ID da D009 de acordo com o código do produto
	$sql .= " SELECT D009_Id FROM D001 ";
	$sql .= " LEFT JOIN D049 ON D049_D001_id = D001_Id "; 
	$sql .= " LEFT JOIN D082 ON D082_Id = D049_D082_Id ";
	$sql .= " LEFT JOIN D009 ON D009_D049_Id = D049_Id ";
	$sql .= " WHERE D001_Codigo_Produto = '$codigo' ";
	$sql .= " AND D009_C004_Id = 1 ";
	$sql .= " GROUP BY D001_Codigo_Produto ";


	$resultado = mysql_query($sql) or die (mysql_error());


	$row = mysql_fetch_array($resultado);
	$D009_D001_Id = $row['D009_Id'];

	//echo $D009_D001_Id;
	//echo $codigo;

	//insere na T001 novos registros e o novo ID se equivale àquele acrescentado na T001

	/*mysql_query("INSERT INTO T029(T029_C004_Id, T029_C007_Id, T029_Data, T029_Descricao, T029_Flag_Ok, T029_Flag_Coletor) 
		VALUES ('1','{$_SESSION['C007_Id']}',CURRENT_DATE(),'T001_Descricao_Operacao','$T029_Descricao','S', 'S')");*/


	$sql = <<<EOT
INSERT INTO T001 (
	T001_D009_Id,  T001_Codigo_Produto, T001_Data_Lancamento, T001_Descricao_Operacao, T001_Quantidade,  T001_Flag_Operacao,T001_Flag_Atualiza_Custo,T001_C007_Id
) VALUES (
	'$D009_D001_Id',  '$codigo', CURRENT_DATE(), '$operacao', '$quantidade',  '$opcao','N','{$_SESSION['C007_Id']}'
)
EOT;
	mysql_query($sql);

	/*mysql_query("INSERT INTO T030(T030_D009_Id,T030_T029_Id,T030_C004_Id, T030_C007_Id, T030_Quantidade, T030_Flag_Entrada_Saida, T030_Flag_Alterar_Custo, T030_Codigo_Produto, T030_Flag_Cancelada, T030_Observacao,T030_Data_Inclusao)
	VALUES('$D009_D001_Id','$result', '1', '{$_SESSION['C007_Id']}', '$quantidade', '$opcao', 'N', '$codigo', 'N', '$T029_Descricao', CURRENT_DATE())");*/

	//require("busca.php");
	header('Location: busca.php');
}