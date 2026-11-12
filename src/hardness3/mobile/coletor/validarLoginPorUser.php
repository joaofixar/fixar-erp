<?php
namespace hardness;

session_start();
include("../../bibliotecas/confUsuario.php");
include("../../bibliotecas/conexaoBanco.php");
require_once('../../bibliotecas/segurancaSenha.php');

$login = mysql_real_escape_string($_POST['login']);
$senha = $_POST['senha'];
$sql = ("SELECT C007_Id, C007_Senha FROM C007 WHERE C007_Login = '$login' AND C007_Ativo='S' LIMIT 1");

$resultado = mysql_query($sql);
$row = $resultado ? mysql_fetch_assoc($resultado) : null;
$contagem = ($row && SegurancaSenha::validarSenhaArmazenada($senha, $row['C007_Senha'])) ? 1 : 0;

if ($contagem == 1) {

  $_SESSION['C007_Id'] = $row['C007_Id'];
  
  header('location:busca.php');


} 
else {


  require("index.php");
  echo '<p align="center">Login ou senha inválidos</p>'; 
}
