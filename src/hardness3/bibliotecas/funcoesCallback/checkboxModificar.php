<?php
namespace hardness;
function checkboxModificar($array){
	require_once('prog/ven/ven002/grid_func-js.php');
	global $g;
	$T006_Flag_Modificar = $array['todosCampos']['T006_Flag_Modificar'];
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T189_Id = $array['todosCampos']['T189_Id'];
	$disabled='';
	if($array['todosCampos']['D049_Flag_Tipo']!='MD'){
		$disabled = "disabled='disabled";
	}
	if($array['todosCampos']['T189_Flag_Status'] != 'D'){
		$retorno = "<input onclick=\"ven002CheckboxModificar(this, '{$T006_Id}', '{$T189_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Modificar' {$disabled} title='Modificar'";
		if ($T006_Flag_Modificar == 'S'){
			return $retorno .="checked='checked' /> ";
		} else {
			return $retorno .= " />";
		}
	}
}