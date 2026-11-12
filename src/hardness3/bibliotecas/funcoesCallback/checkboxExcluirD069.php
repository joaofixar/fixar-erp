<?php
namespace hardness;
function checkboxExcluirD069($array) {
	global $g;
	$D069_Id = $array['todosCampos']['D069_Id'];
	
	$restricao = $g['conf']->restricaoTravar('excluir', 'D069', $D069_Id, false, false);
	$disabled = '';
	if($restricao !== true){
		$disabled = 'disabled=disabled';
	}

	$retorno = "<input onclick=\"checkboxExcluirD069(this, '{$D069_Id}', '{$g['divId']}')\" type='checkbox' id='D069_Flag_Excluir' title='Excluir Item' ". $disabled;
	if ($array['todosCampos']['D069_Flag_Excluir'] == 1){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";	
}