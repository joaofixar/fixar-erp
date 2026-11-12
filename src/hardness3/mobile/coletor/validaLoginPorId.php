<?php
namespace hardness;

session_start();
include("../../bibliotecas/confUsuario.php");
include("../../bibliotecas/conexaoBanco.php");

if (isset($_POST['idUsuario']))
	$idUsuario = $_POST['idUsuario'];

$sql = ("SELECT C007_Id FROM C007 WHERE C007_Id = '$idUsuario' ") or die(mysql_error());;  

$resultado = mysql_query($sql);
$contagem = mysql_num_rows($resultado);

if ( $contagem == 1 ) {
	$_SESSION['C007_Id'] = $_POST['idUsuario'];
	header('location:busca.php');
} 

else {
	require("index.php");
	echo '<p align="center">Login ou senha inválidos</p>'; 
}
