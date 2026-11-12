<?php
namespace hardness;


// Nome do vendedor
if($T003['T003_Flag_Status_Orcamento'] == "L"){
	$statusOrcamento = "Em Análise";
}elseif($T003['T003_Flag_Status_Orcamento'] == "A"){
	$statusOrcamento = "Aprovado";
}elseif($T003['T003_Flag_Status_Orcamento'] == "P"){
	$statusOrcamento = "Pendente";
}elseif($T003['T003_Flag_Status_Orcamento'] == "N"){
	$statusOrcamento = "Negado";
}
if ($g['usuarioAtual'] > 0) {
	$sql = "SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';";
	$res = mysql_query($sql);
	$C007 = mysql_fetch_assoc($res);
} else {
	$server = $_SERVER['SERVER_NAME'];
	$endereco = $_SERVER ['PHP_SELF'];
	$login = strpos($endereco, 'login.php');
	if($login === false){
		// (TODO) LOGIN/LOGOUT
		header('Location: ../login.php');
		die();
	}
}

// Busca o último orçamento do vendedor
$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : 'last';
$sql = <<<EOT
	SELECT T003_Id  FROM T003 WHERE T003_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T003_Flag_Status_Orcamento = 'P' ORDER BY T003_Id DESC LIMIT 0,100
EOT;
$res = mysql_query($sql);
if ($T003_Id == 'last') {
	$row = mysql_fetch_assoc($res);
	$T003_Id = $row['T003_Id'];
	@mysql_data_seek($res, 0);
}

// Dados do orçamento
$sql = "SELECT * FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id = '{$T003_Id}';";
$res = mysql_query($sql);
$T003 = mysql_fetch_assoc($res); 
