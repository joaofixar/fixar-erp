<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */

function cad032CheckboxAcessos($array){
 	   global $g,$r_acaoId;
		$retorno = "<input onchange=\"cad032CheckboxAcessos(this, '{$array['todosCampos']['C029_Id']}', '{$r_acaoId}', '{$g['divId']}')\" type='checkbox' id='C030_Acesso_Liberado' title='Conceder Acesso'";
		if ($array['todosCampos']['C030_Acesso_Liberado']=='S'){
			return $retorno .="checked='checked'";
		}
		return $retorno .= " />";
}

function cad032CheckboxAcessosItem($array){
	global $g, $r_acaoId;
	$retorno = "<input onchange=\"cad032CheckboxAcessos(this, '{$r_acaoId}', '{$array['todosCampos']['C021_Id']}', '{$g['divId']}')\" type='checkbox' id='C030_Acesso_Liberado' title='Conceder Acesso'";
	if ($array['todosCampos']['C030_Acesso_Liberado']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}

function cad032CheckboxResumosRelatorios($array){
	
	global $g,$r_acaoId;
	
	$retorno = "<input onchange=\"cad032CheckboxResumosRelatorios(this, '{$array['todosCampos']['C027_Id']}', '{$r_acaoId}', '{$g['divId']}')\" type='checkbox' id='C028_Acesso_Liberado' title='Conceder Acesso'";
	if ($array['todosCampos']['C028_Acesso_Liberado']=='S'){
		return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}
?>