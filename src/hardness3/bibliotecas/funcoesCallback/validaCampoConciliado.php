<?php
namespace hardness;

function validaCampoConciliado($array) {
	global $g;
	$retorno = "<input type='checkbox' title='Baixar titulo' onChange=\"validaCampoConciliado(this, '{$array['todosCampos']['T019_Id']}', '{$g['divId']}')\" ";
	if ($array['campoAtual']==1){
		$retorno .="checked='checked'";
	}
	return $retorno . " />";
}
