<?php
namespace hardness;

include('sessao.php');

if (isset($_POST['idUsuario']))
	$codigo = $_POST['idUsuario'];
	$sql = ("SELECT C007_Id FROM C007 WHERE C007_Id = '$codigo' ");  
	$resultado = mysql_query($sql);
	$contagem = mysql_num_rows($resultado);
if ( $contagem == 1 ) {
	$_SESSION['idUsuario']=$codigo;
	header('location:busca.php');
} else {
	require("index.php");
	echo '<p align="center">Login ou senha inválidos</p>'; 
}