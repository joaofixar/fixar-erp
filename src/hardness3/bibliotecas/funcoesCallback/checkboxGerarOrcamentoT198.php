<?php
namespace hardness;
// D009_Id, T198_Id
function checkboxGerarOrcamentoT198($array){
	global $g;
	$D009_Id = $array['todosCampos']['D009_Id'];
	$T198_Id = (isset($array['todosCampos']['T198_Id'])) ? $array['todosCampos']['T198_Id'] : 0;
	$retorno = "<input type='checkbox' onChange=\"checkboxGerarOrcamentoT198(this, '{$D009_Id}', '{$T198_Id}', '{$g['divId']}'); return false;\" ";
	if (!empty($T198_Id)) {
		$retorno .="checked='checked'";
	}
	return $retorno .= " />";
}