<?php
namespace hardness;

if (isset($_SESSION['authDB']) && $_SESSION['authDB'] && $confUsuario['dbDatabase'] != $sys['dbDatabase']) {
	$confUsuario['dbUser_'] = $confUsuario['dbUser'];
	$confUsuario['dbPass_'] = $confUsuario['dbPass'];
	$confUsuario['dbUser'] = $_SESSION['authDBUser'];
	$confUsuario['dbPass'] = $_SESSION['authDBPass'];
}
if(isset($g['conexaoBanco'])){
	mysqli_close($g['conexaoBanco']);
}
$con=mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);

if (!empty($Error)) {
	// Efetua mais uma tentativa, para verificar se não foi alterado a senha do usuário no banco
	unset($_SESSION['authDB']);
	mysqli_close($con);
	$confUsuario['dbUser'] = $confUsuario['dbUser_'];
	$confUsuario['dbPass'] = $confUsuario['dbPass_'];
	$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
	$Error=mysqli_connect_error($con);
	if (!empty($Error)) {
		echo "Ocorreu um erro ao conectar com banco de dados(2):".$Error;
		die();
	}
}

// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');

if (!isset($_SESSION['authDB']) && isset($_SESSION['Usuario_Atual'])) {
	$_SESSION['authDB'] = false;
	$C007 = mysqli_fetch_assoc(mysqli_query($con,"SELECT C007_Id, C007_Login, C007_Banco_Senha FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
	if (!empty($C007['C007_Banco_Senha'])) {
		$_SESSION['authDB'] = true;
		$_SESSION['authDBUser'] = $C007['C007_Login'] . $C007['C007_Id'];
		$_SESSION['authDBPass'] = $C007['C007_Banco_Senha'];
	}
}

$g['conexaoBanco'] = $con;



