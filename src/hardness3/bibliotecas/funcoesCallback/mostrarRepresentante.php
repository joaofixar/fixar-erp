<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: mostrarVendedorInternoNF()
*/

function mostrarRepresentante ($array) {
	global $g;
	return $array['todosCampos']['D021_Nome_Empresa'];
}
