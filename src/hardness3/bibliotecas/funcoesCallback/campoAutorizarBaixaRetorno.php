<?php
namespace hardness;
function campoAutorizarBaixaRetorno($array){
	global $g;
	$T038_Flag_Autorizado = $array['campoAtual'];
	$T038_Id = $array['todosCampos']['T038_Id'];

	if($T038_Flag_Autorizado == ''){
		return '';
	} else {
		if($array['todosCampos']['T002_Data_Recebimento'] != '0000-00-00' && $array['todosCampos']['T038_T002_Id'] != '0'){
			$disabled = "disabled='disabled'";
		}
		$checked = ($T038_Flag_Autorizado == 'S') ? "checked='checked'" : "";
		return "<input onChange=\"campoAutorizarBaixaRetorno(this, '{$T038_Id}', '{$g['divId']}'); return false;\" type='checkbox' id='T038_Flag_Autorizado' title='Autorizar baixa' {$checked} {$disabled}>";
	}
}