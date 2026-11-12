<?php
namespace hardness;

function mostrarImagemProdutoNormal($array){
	global $g, $confUsuario;

	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();        

	$foto = $CAD002->cad002FotoProduto($array['todosCampos']['D001_Id'],150);

	return $foto;

}
