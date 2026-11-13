<?php
namespace hardness;

include('sessao.php');

if (!isset($_POST['enviar'])){
	$sql = null;
	$codigo = $_POST['codigo'];
	$sql .= " SELECT D009_C004_Id, T066_Id, ";
	$sql .= " D001_Codigo_Produto, D001_Descricao_Produto, ";
	$sql .= " D009_Quantidade_Estoque, D009_Quantidade_Estoque_Liquido, ";
	$sql .= " GROUP_CONCAT(IFNULL(D004_Local, '')  SEPARATOR '') AS D004_Local FROM D001 ";
	$sql .= " LEFT JOIN D049 ON D049_D001_Id=D001_Id ";
	$sql .= " LEFT JOIN D009 ON D009_D049_Id=D049_Id ";
	$sql .= " LEFT JOIN T066 ON T066_D009_Id=D009_Id ";
	$sql .= " LEFT JOIN D004 ON D004_Id=T066_D004_Id ";
	$sql .= " WHERE D001_Codigo_Produto = '$codigo' OR D004_Local = '$codigo' AND D009_C004_Id = 1 ";
	$sql .= " GROUP BY D001_Codigo_Produto ";


	
	$resultado = mysql_query($sql) or die (mysql_error()); 
	
	require("imprime.php");

	if (mysql_num_rows($resultado)==0) {
		
		header('location:busca.php'); 
		echo '<p align="center">Produto não encontrado</p>';
		
	}

}
$_SESSION['codigo']=$codigo;
mysql_close($con);