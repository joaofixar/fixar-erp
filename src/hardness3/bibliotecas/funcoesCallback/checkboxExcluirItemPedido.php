<?php
namespace hardness;
function checkboxExcluirItemPedido($array) {
	global $g;
	$T006_Id = $array['todosCampos']['T006_Id'];
	
	$retorno = "<input onclick=\"checkboxExcluirItemPedido(this, '{$T006_Id}', '{$g['divId']}')\" type='checkbox' id='T006_Flag_Excluir' title='Excluir Item'";
	if ($array['todosCampos']['T006_Flag_Excluir'] == 1){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}