<?php
namespace hardness;
function checkboxCotarEmergencia($array){
	global $g;
	$T004_Flag_Gerar_Cotacao = $array['todosCampos']['T004_Flag_Gerar_Cotacao'];
	$T004_Id = $array['todosCampos']['T004_Id'];
	$T004_C007_Id_Comprador = $array['todosCampos']['T004_C007_Id_Comprador'];
	$C007_Primeiro_Nome = substr($array['todosCampos']['marcou.C007_Primeiro_Nome'], 0,8);
	$T219_T218_Id = $array['todosCampos']['T219_T218_Id'];

	$retorno = "<input onclick=\"checkboxCotarEmergencia(this, '{$T004_Id}', '{$g['divId']}');\" type='checkbox' id='T004_Flag_Gerar_Cotacao' title='Cotar'";
	if($T219_T218_Id>0){
		return $retorno .="checked='checked' disabled='disabled' />";
	}
	if ($T004_Flag_Gerar_Cotacao == 'S'){
		if($T004_C007_Id_Comprador != $g['usuarioAtual'] && $T004_C007_Id_Comprador > 0 && $T004_C007_Id_Comprador != null){
			return $retorno .="checked='checked' disabled='disabled' /><br /><font color='gray' style='font-size:8px;'>{$C007_Primeiro_Nome}</font> ";
		}
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
}