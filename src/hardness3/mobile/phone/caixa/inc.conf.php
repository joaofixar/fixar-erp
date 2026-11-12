<?php
// Nome do vendedor
if($T005['T005_Flag_Status_Orcamento'] == "L"){
	$statusOrcamento = "Em Análise";
}elseif($T005['T005_Flag_Status_Orcamento'] == "A"){
	$statusOrcamento = "Aprovado";
}elseif($T005['T005_Flag_Status_Orcamento'] == "P"){
	$statusOrcamento = "Pendente";
}elseif($T005['T005_Flag_Status_Orcamento'] == "N"){
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

// Busca o último caixa do vendedor
$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : 'last';
$sql = <<<EOT
	SELECT T005_Id  FROM T005 WHERE T005_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T005_Flag_Status_Orcamento = 'P' ORDER BY T005_Id DESC LIMIT 0,100
EOT;
$res = mysql_query($sql);
if ($T005_Id == 'last') {
	$row = @mysql_fetch_assoc($res);
	$T005_Id = $row['T005_Id'];
	@mysql_data_seek($res, 0);
}

// Dados do caixa
$sql = "SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id = '{$T005_Id}';";
$res = mysql_query($sql);
$T005 = mysql_fetch_assoc($res); 
