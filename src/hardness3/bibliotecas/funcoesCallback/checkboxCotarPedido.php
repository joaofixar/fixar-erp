<?php
namespace hardness;
function checkboxCotarPedido($array){
	require_once('prog/ven/ven002/grid_func-js.php');
	global $g;
	$T006_Flag_Cotar = $array['todosCampos']['T006_Flag_Cotar'];
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T189_Id = $array['todosCampos']['T189_Id'];
	if(empty($T189_Id)){
		$retorno = "<input onclick=\"ven002CheckboxCotar(this, '{$T006_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Cotar' title='Cotar'";
		if ($T006_Flag_Cotar == 'S'){
			return $retorno .="checked='checked' /> ";
		} else {
			return $retorno .= " />";
		}
	}
}