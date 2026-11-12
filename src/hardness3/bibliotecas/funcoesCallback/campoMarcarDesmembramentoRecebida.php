<?php
namespace hardness;
function campoMarcarDesmembramentoRecebida($array) {
	global $g;
	if($array['todosCampos']['T216_Id'] > 0){
		if($array['todosCampos']['T002_Data_Recebimento'] != '0000-00-00'){
			return $array['todosCampos']['T216_T215_Id'];
		} else {
			return "<a href=\"#\" onclick=\"removerVinculoTransacao('{$array['todosCampos']['T216_Id']}', '{$g['divId']}'); return false;\">{$array['todosCampos']['T216_T215_Id']}</a>";
		}
	} else if($array['todosCampos']['T002_Data_Recebimento'] != '0000-00-00'){
		return '';
	}
	$T002_Id = $array['todosCampos']['T002_Id'];
	$T002_Flag_Marcar_Lancamento = $array['todosCampos']['T002_Flag_Marcar_Lancamento'];
	$retorno = "<span><input onclick=\"campoMarcarDesmembramentoRecebida(this, '{$T002_Id}', '{$g['divId']}')\" type='checkbox' id='T002_Flag_Marcar_Lancamento' title='Marcar Título a Receber'";
	if ($array['todosCampos']['T002_Flag_Marcar_Lancamento'] == 'S'){
	    $retorno .="checked='checked'";
	}
	return $retorno .= " /></span>";
}