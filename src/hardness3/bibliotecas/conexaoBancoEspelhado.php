<?php
namespace hardness;

// Este comando já está em vários pontos de entrada (exemplo index-framework.php), colocado aqui também para garantir que será executado em scripts antigos
setlocale(LC_CTYPE, 'pt_BR.UTF-8');

if (isset($_SESSION['authDB']) && $_SESSION['authDB'] && $confUsuarioEspelhado['dbDatabase'] != $sys['dbDatabase']) {
	$confUsuarioEspelhado['dbUser_'] = $confUsuarioEspelhado['dbUser'];
	$confUsuarioEspelhado['dbPass_'] = $confUsuarioEspelhado['dbPass'];
	//$confUsuarioEspelhado['dbPass']  = $_SESSION['authDBPass'];
	//$confUsuarioEspelhado['dbPass']  = $_SESSION['authDBPass'];
    $confUsuarioEspelhado['dbPass']  = $confUsuarioEspelhado['authDBPass'];
    $confUsuarioEspelhado['dbPass']  = $confUsuarioEspelhado['authDBPass'];
}
if(isset($g['conexaoBanco'])){
	mysqli_close($g['conexaoBanco']);
}

$con = mysqli_init();
if (!$con) {
  die("mysqli_init failed");
} 

//echo 'Conexão banco: ' . $confUsuarioEspelhado['dbUser'] .' '. $confUsuarioEspelhado['dbPass']

$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 240);
$con -> real_connect($confUsuarioEspelhado['dbHost'], $confUsuarioEspelhado['dbUser'], $confUsuarioEspelhado['dbPass'],$confUsuarioEspelhado['dbDatabase']);

//$con=mysqli_connect($confUsuarioEspelhado['dbHost'], $confUsuarioEspelhado['dbUser'], $confUsuarioEspelhado['dbPass'],$confUsuarioEspelhado['dbDatabase']);
$Error=mysqli_connect_error($con);

log('conexaoBanco');

if (!empty($Error)) {
	// Efetua mais uma tentativa, para verificar se não foi alterado a senha do usuário no banco
	unset($_SESSION['authDB']);
	mysqli_close($con);
	$confUsuarioEspelhado['dbUser'] = $confUsuarioEspelhado['dbUser_'];
	$confUsuarioEspelhado['dbPass'] = $confUsuarioEspelhado['dbPass_'];
	$con=@mysqli_connect($confUsuarioEspelhado['dbHost'], $confUsuarioEspelhado['dbUser'], $confUsuarioEspelhado['dbPass'],$confUsuarioEspelhado['dbDatabase']);
	$Error1=mysqli_connect_error($con);
	if (!empty($Error1)) {
		echo "Ocorreu um erro ao conectar com banco de dados {$confUsuarioEspelhado['dbDatabase']} (2): <br><br> Erro 1: ".$Error."<br><br> Erro 2: ".$Error1;
		die();
	}
}

// $dbs=mysql_select_db($confUsuarioEspelhado['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
\mysqli_query($con,'SET NAMES utf8;');

if (!isset($_SESSION['authDB']) && isset($_SESSION['Usuario_Atual'])) {
	$_SESSION['authDB'] = false;
	$C007 = mysqli_fetch_assoc(\mysqli_query($con,"SELECT C007_Id, C007_Login, C007_Banco_Senha FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
	if (!empty($C007['C007_Banco_Senha'])) {
		$_SESSION['authDB'] = true;
		$_SESSION['authDBUser'] = $C007['C007_Login'] . $C007['C007_Id'];
		$_SESSION['authDBPass'] = $C007['C007_Banco_Senha'];
	}
}

$g['conexaoBanco'] = $con;

include_once('bibliotecas/conexaoBancoAuditoria.php');

// if(isset($confUsuarioEspelhadoAuditoria)){
// 	//Conexão do banco da auditoria
// 	$conAuditoria=mysqli_connect($confUsuarioEspelhadoAuditoria['dbHost'], $confUsuarioEspelhadoAuditoria['dbUser'], $confUsuarioEspelhadoAuditoria['dbPass'],$confUsuarioEspelhadoAuditoria['dbDatabase']);
// 	$ErrorAuditoria=mysqli_connect_error($conAuditoria);

// 	if(empty($ErrorAuditoria)){
// 		$g['conexaoBancoAuditoria'] = $conAuditoria;
// 	}
// }

// if(!isset($g['conexaoBancoAuditoria'])){
// 	$g['conexaoBancoAuditoria'] = $g['conexaoBanco'];
// }

// mysqli_set_charset($g['conexaoBancoAuditoria'],'utf8');
// mysqli_query($g['conexaoBancoAuditoria'],'SET NAMES utf8;');



