<?php
namespace hardness;
// D069_Id
function checkboxD069FlagTabelaPrincipal($array, $atualizarGrid = false){
	global $g,$r_acaoId;

	$retorno = "<input onclick=\"checkboxD069FlagTabelaPrincipal(this, '{$array['todosCampos']['D069_Id']}', '{$g['divId']}', '{$atualizarGrid}')\" type='checkbox' id='D069_Flag_Tabela_Principal' title='Selecieonar a tabela principal como custo do produto'";

	if ($array['todosCampos']['D069_Flag_Tabela_Principal']=='S'){
	    return $retorno .="checked='checked'";
	}
	return $retorno .= " />";
}