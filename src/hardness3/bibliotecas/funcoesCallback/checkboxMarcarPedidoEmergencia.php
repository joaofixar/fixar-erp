<?php
namespace hardness;
function checkboxMarcarPedidoEmergencia($array){
	global $g;
	$T006_Flag_Gerar_Cotacao = $array['todosCampos']['T006_Flag_Gerar_Cotacao'];
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T006_C007_Id_Comprador = $array['todosCampos']['T006_C007_Id_Comprador'];
	$C007_Primeiro_Nome = substr($array['todosCampos']['marcou.C007_Primeiro_Nome'], 0,8);
	
	$retorno = "<input onclick=\"checkboxMontarPedidoEmergencia(this, '{$T006_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Gerar_Cotacao' title='Cotar'";
	if ($T006_Flag_Gerar_Cotacao == 'S'){
		if($T006_C007_Id_Comprador != $g['usuarioAtual'] && $T006_C007_Id_Comprador > 0 && $T006_C007_Id_Comprador != null){
			return $retorno .="checked='checked' disabled='disabled' /><br /><font color='gray' style='font-size:8px;'>{$C007_Primeiro_Nome}</font> ";
		}
		return $retorno .="checked='checked' /> ";
	} else {
		return $retorno .= " />";
	}
}