<?php
namespace hardness;

function downloadXMLPedido($array){
	global $g;

	$T005_Id = $array['todosCampos']['T005_Id'];

	$retorno = "<a href=\"/crm/crm001/grid_func-ajax/abreXMLPedidos/?ajax=true&T005_Id={$T005_Id}\">Abrir</a>";

	$cArquivo = "{$g['pathDados']}/ecommerce/xmlPedido/PEDIDO_{$T005_Id}.xml";
	if (file_exists($cArquivo)){
		return $retorno;
	}
}
