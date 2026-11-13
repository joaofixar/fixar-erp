<?php
namespace hardness;
function checkboxCotarPedidoEmergencia($array){
	global $g;
	$T006_Flag_Gerar_Cotacao = $array['todosCampos']['T006_Flag_Gerar_Cotacao'];
	$T006_Id = $array['todosCampos']['T006_Id'];
	$T006_C007_Id_Comprador = $array['todosCampos']['T006_C007_Id_Comprador'];
	$C007_Primeiro_Nome = substr($array['todosCampos']['marcou.C007_Primeiro_Nome'], 0,8);
	if($array['todosCampos']['T219_T218_Id'] > 0 || $array['todosCampos']['OT.T224_Id'] > 0){
		if(!empty($array['todosCampos']['T224_Id'])){
			$retorno = "<input onclick=\"est015CheckboxComprar(this, '{$array['todosCampos']['T219_Id']}', '{$g['divId']}')\" type='checkbox' id='T219_Flag_Comprar' title='Comprar'";
			if ($array['todosCampos']['T219_Flag_Comprar']=='S'){
				$retorno .="checked='checked'";
			}
			$retorno .= " /><br>";
			$arrayT224 = explode(",", $array['todosCampos']['T224_Id']);
			foreach ($arrayT224 as $T224_Id) {
				$retorno .= "<span style=\"background-color: yellow\"><a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$T224_Id}'), [990,605]); return false;\">{$T224_Id}</a></span>, ";
			}
			return substr($retorno, 0, -2);
		} else {
			$retorno = "<input onclick=\"est015CheckboxComprar(this, '{$array['todosCampos']['T219_Id']}', '{$g['divId']}')\" type='checkbox' id='T219_Flag_Comprar' title='Comprar'";
			if($array['todosCampos']['T006_T219_Id'] <= 0){
				return $retorno .="disabled='disabled'";
			}
			if ($array['todosCampos']['T219_Flag_Comprar']=='S'){
				return $retorno .="checked='checked'";
			}
			return $retorno .= " />";
		}
	} else {
		$retorno = "<input onclick=\"checkboxCotarPedidoEmergencia(this, '{$T006_Id}', '{$g['divId']}');\" type='checkbox' id='T006_Flag_Gerar_Cotacao' title='Cotar'";
		if ($T006_Flag_Gerar_Cotacao == 'S'){
			if($T006_C007_Id_Comprador != $g['usuarioAtual'] && $T006_C007_Id_Comprador > 0 && $T006_C007_Id_Comprador != null){
				return $retorno .="checked='checked' disabled='disabled' /><br /><font color='gray' style='font-size:8px;'>{$C007_Primeiro_Nome}</font> ";
			}
			return $retorno .="checked='checked' /> ";
		} else {
			return $retorno .= " />";
		}
	}
}