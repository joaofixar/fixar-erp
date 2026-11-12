<?php
// Busca o último Pedido do vendedor
$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : 'last';
$acao = isset($_REQUEST['acao']) ? "&acao=caixa" : "";
// Dados do Pedido
$sql = "SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id = '{$T005_Id}';";
$res = mysql_query($sql);
$T005 = mysql_fetch_assoc($res); 

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


$sql = <<<EOT
	SELECT T005_Id  FROM T005 WHERE T005_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' AND T005_Flag_Status_Orcamento = 'P' ORDER BY T005_Id DESC LIMIT 0,100
EOT;
$res = mysql_query($sql);
if ($T005_Id == 'last') {
	$row = @mysql_fetch_assoc($res);
	$T005_Id = $row['T005_Id'];
	@mysql_data_seek($res, 0);
}

// Status do pedido com Analise
if($T005['T005_Flag_Status_Liberacao'] == "1"){
    $statuspedido = "Em Análise";
}else if($T005['T005_Flag_Status_Liberacao'] == "2"){
    $statuspedido = "Aprovado";
}else if($T005['T005_Flag_Status_Liberacao'] == "3"){
    $statuspedido = "Negado";
}

$geradoFormaPagamento = false;
if(!empty($T005['T005_Pgto_C007_Id'])){
	$geradoFormaPagamento = true;
}