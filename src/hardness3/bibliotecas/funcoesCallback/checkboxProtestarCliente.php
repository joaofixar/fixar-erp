<?php
namespace hardness;
// D024_Id
function checkboxProtestarCliente($array){
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	$retorno = "<input type='checkbox' onChange=\"checkboxProtestarCliente(this, '{$D024_Id}', '{$g['divId']}'); return false;\">";
	return $retorno;
}