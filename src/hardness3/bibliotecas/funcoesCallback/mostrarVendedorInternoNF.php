<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: mostrarVendedorInternoNF()
*/

function mostrarVendedorInternoNF ($array) {
	global $g;
	return $array['todosCampos']['vendedor.C007_Primeiro_Nome'];
}
