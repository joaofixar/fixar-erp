<?php
namespace hardness;

session_start();
if (empty($_SESSION['C007_Id'])) {
	header("Location: index.php");
}

include("../../bibliotecas/confUsuario.php");
include("../../bibliotecas/conexaoBanco.php");