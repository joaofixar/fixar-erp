<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoDuplicar($array){
	include('prog/crm/crm001/grid_func-js.php');
	global $g;
	$T005_Id = $array['todosCampos']['T005_Id'];
	$T005_Flag_Marcado = $array['todosCampos']['T005_Flag_Marcado'];
	$T005_Flag_Status = $array['todosCampos']['T005_Flag_Status'];
	if($T005_Flag_Status != '8'){
		$retorno = "<input onclick=\"crm001CheckboxDuplicar(this, '{$T005_Id}', '{$g['divId']}');\" type='checkbox' id='T005_Flag_Marcado'";
		if ($T005_Flag_Marcado=='S'){
			$retorno .="checked='checked'";
		}
	    return $retorno .= " />";
	} else {
		return '';
	}
}