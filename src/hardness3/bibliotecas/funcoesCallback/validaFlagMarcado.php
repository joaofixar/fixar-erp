<?php
//---------------------------------------------------------------------------------
/* $array (array) todos os campos da linha
* $param (array)[0] - nome do campo
*				[1] - N|S ou 0|1 - Define os valores booleanos aceitos
* 				
*/
namespace hardness;
function validaFlagMarcado($array){
	require_once('prog/est/est003/grid_func-js.php');
	global $g;
	$T206_Flag_Marcado = $array['todosCampos']['T206_Flag_Marcado'];
	$T206_Id = $array['todosCampos']['T206_Id'];
	$disabled='';
	$retorno = "<input onclick=\"est003CheckboxGerarPedido(this, '{$T206_Id}', '{$g['divId']}');\" type='checkbox' id='T206_Flag_Marcado' title='Gerar Pedido?'";
	if ($T206_Flag_Marcado == 'S'){
		return $retorno .="checked='checked' {$disabled} />";
	} else {
		return $retorno .= " />";
	}
}