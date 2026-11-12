<?php
namespace hardness;

// Este comando já está em vários pontos de entrada (exemplo index-framework.php), colocado aqui também para garantir que será executado em scripts antigos
setlocale(LC_CTYPE, 'pt_BR.UTF-8');

if (isset($_SESSION['authDB']) && $_SESSION['authDB'] && $confUsuario['dbDatabase'] != $sys['dbDatabase']) {
	$confUsuario['dbUser_'] = $confUsuario['dbUser'];
	$confUsuario['dbPass_'] = $confUsuario['dbPass'];
	$confUsuario['dbUser'] = $_SESSION['authDBUser'];
	$confUsuario['dbPass'] = $_SESSION['authDBPass'];
}
if(isset($g['conexaoBanco'])){
	mysqli_close($g['conexaoBanco']);
}

$con = mysqli_init();
if (!$con) {
  die("mysqli_init failed");
} 

//echo 'Conexão banco: ' . $confUsuario['dbUser'] .' '. $confUsuario['dbPass']

$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
$con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);

//$con=mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error();

if (function_exists(__NAMESPACE__ . '\\log')) { log('conexaoBanco'); }

if (!empty($Error)) {
	// Efetua mais uma tentativa, para verificar se não foi alterado a senha do usuário no banco
	if (function_exists(__NAMESPACE__ . '\\log')) { log('ERRO Conexao mysql = ' .$Error); }
	unset($_SESSION['authDB']);
	mysqli_close($con);
	$confUsuario['dbUser'] = $confUsuario['dbUser_'];
	$confUsuario['dbPass'] = $confUsuario['dbPass_'];
	$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
	$Error1=mysqli_connect_error();
	if (!empty($Error1)) {
		echo "Ocorreu um erro ao conectar com banco de dados {$confUsuario['dbDatabase']} (2): <br><br> Erro 1: ".$Error."<br><br> Erro 2: ".$Error1;
		die();
	}
}

// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
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

// if(isset($confUsuarioAuditoria)){
// 	//Conexão do banco da auditoria
// 	$conAuditoria=mysqli_connect($confUsuarioAuditoria['dbHost'], $confUsuarioAuditoria['dbUser'], $confUsuarioAuditoria['dbPass'],$confUsuarioAuditoria['dbDatabase']);
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



