<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: checkboxCotarOrcamento()
*/

function checkboxPrazoCompra ($array){
	include('prog/ven/ven001/grid_func-js.php');
	global $g;
	$T004A_Flag_Solicitar_Prazo_Compras = $array['todosCampos']['T004A_Flag_Solicitar_Prazo_Compras'];
	$T219_T218_Id                       = $array['todosCampos']['T219_T218_Id'];
	$T004_Id                            = $array['todosCampos']['T004_Id'];
	$D049_Flag_Tipo                     = $array['todosCampos']['D049_Flag_Tipo'];
	$disabled                           = ($D049_Flag_Tipo != 'CP' && $D049_Flag_Tipo != 'PD' && $D049_Flag_Tipo != '') ? ' disabled=disabled ': '';
	$disabled                           = ($T219_T218_Id > 0) ? ' disabled=disabled ': '';
	$retorno = "<input onclick=\"ven001CheckboxPrazoCompra(this, '{$T004_Id}', '{$g['divId']}');\" type='checkbox' id='T004A_Flag_Solicitar_Prazo_Compras' title='Solicitar prazo entrega' {$disabled}";
	if ($T004A_Flag_Solicitar_Prazo_Compras == 'S' or $T219_T218_Id>0){
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
}
