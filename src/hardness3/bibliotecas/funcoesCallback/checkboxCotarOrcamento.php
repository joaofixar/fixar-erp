<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: checkboxCotarOrcamento()
*/

function checkboxCotarOrcamento ($array){
	include('prog/ven/ven001/grid_func-js.php');
	global $g;
	$T004_Flag_Comprar = $array['todosCampos']['T004_Flag_Comprar'];
	$T219_T218_Id = $array['todosCampos']['T219_T218_Id'];
	$T004_Id = $array['todosCampos']['T004_Id'];
	$D049_Flag_Tipo = $array['todosCampos']['D049_Flag_Tipo'];
	$disabled = ($D049_Flag_Tipo != 'CP' && $D049_Flag_Tipo != 'PD' && $D049_Flag_Tipo != '') ? ' disabled=disabled ': '';
	$disabled = ($T219_T218_Id>0) ? ' disabled=disabled ': '';
	$retorno = "<input onclick=\"ven001CheckboxCotar(this, '{$T004_Id}', '{$g['divId']}');\" type='checkbox' id='T004_Flag_Comprar' title='Cotar' {$disabled}";
	if ($T004_Flag_Comprar == 'S' or $T219_T218_Id>0){
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
}
