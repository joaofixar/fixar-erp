<?php
namespace hardness;

if (isset($_SESSION['authDB']) && $_SESSION['authDB'] && $confUsuario['dbDatabase'] != 'e001') {
	$confUsuario['dbUser_'] = $confUsuario['dbUser'];
	$confUsuario['dbPass_'] = $confUsuario['dbPass'];
	$confUsuario['dbUser'] = $_SESSION['authDBUser'];
	$confUsuario['dbPass'] = $_SESSION['authDBPass'];
}
mysql_close();
$con=@mysql_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);
$Error=mysql_error();

if (!empty($Error)) {
	// Efetua mais uma tentativa, para verificar se não foi alterado a senha do usuário no banco
	unset($_SESSION['authDB']);
	mysql_close();
	$confUsuario['dbUser'] = $confUsuario['dbUser_'];
	$confUsuario['dbPass'] = $confUsuario['dbPass_'];
	$con=@mysql_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);
	$Error=mysql_error();
	if (!empty($Error)) {
		echo $Error;
		die();
	}
}

$dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

if (!isset($_SESSION['authDB']) && isset($_SESSION['Usuario_Atual'])) {
	$_SESSION['authDB'] = false;
	$C007 = mysql_fetch_assoc(mysql_query("SELECT C007_Id, C007_Login, C007_Banco_Senha FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
	if (!empty($C007['C007_Banco_Senha'])) {
		$_SESSION['authDB'] = true;
		$_SESSION['authDBUser'] = $C007['C007_Login'] . $C007['C007_Id'];
		$_SESSION['authDBPass'] = $C007['C007_Banco_Senha'];
	}
}