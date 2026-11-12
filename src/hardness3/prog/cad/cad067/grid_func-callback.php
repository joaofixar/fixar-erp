<?php
namespace hardness;
/**
 * CAD - cad064 - Cadastro de Veículos
 */

function tipoImpressora($array){
	$tipoImpressora = $array['todosCampos']['D175_Tipo_Impressora'];

	switch ($tipoImpressora) {
		case 6:
			return "Etiqueta";
		case 7:
			return "DANFe";
		case 8:
			return "DANFCe";
		case 9:
			return "Relatório";
		default:
			return "";
	  }
}

?>
