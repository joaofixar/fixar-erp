<?php
namespace hardness;
function checkboxComprar($array){
	require_once('prog/ven/ven002/grid_func-js.php');
	global $g;
	$T006_Flag_Comprar = $array['todosCampos']['T006_Flag_Comprar'];
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T189_Id = $array['todosCampos']['T189_Id'];
	if($array['todosCampos']['T189_Flag_Status'] != 'D'){
		$retorno = "<input onclick=\"ven002CheckboxComprar(this, '{$T006_Id}', '{$T189_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Comprar' title='Comprar'";
		if ($T006_Flag_Comprar == 'S'){
			return $retorno .="checked='checked' /> ";
		} else {
			return $retorno .= " />";
		}
	}
}