<?php
namespace hardness;
function checkboxExcluirItemOrcamento($array) {
	global $g;
	$T004_Id = $array['todosCampos']['T004_Id'];
	
	$restricao = $g['conf']->restricaoTravar('excluir', 'T004_Id', $T004_Id, false, false);
	$disabled = '';
	if($restricao !== true){
		$disabled = 'disabled=disabled';
	}

	$retorno = "<input onclick=\"checkboxExcluirItemOrcamento(this, '{$T004_Id}', '{$g['divId']}')\" type='checkbox' id='T004_Flag_Excluir' title='Excluir Item' ". $disabled;
	if ($array['todosCampos']['T004_Flag_Excluir'] == 1){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}