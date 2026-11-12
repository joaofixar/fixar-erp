<?php
@session_destroy();
$_SESSION = array();	

session_start();

$_SESSION['bancoDados'] = strtolower($_REQUEST['db']);

include("bibliotecas/confUsuario.php");

header('Location: http://nota.hardness.com.br/hardness3/outros/login/');