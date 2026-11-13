<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function validaCampoPreco1($array){
	$array['todosCampos']['D049_Preco_1'] = gCorrigeNumero($array['todosCampos']['D049_Preco_1']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='green'><b>{$array['todosCampos']['D049_Preco_1']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_1'];
	}
}
function validaCampoPreco2($array){
	$array['todosCampos']['D049_Preco_2'] = gCorrigeNumero($array['todosCampos']['D049_Preco_2']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='green'><b>{$array['todosCampos']['D049_Preco_2']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_2'];
	}
}
function validaCampoPreco3($array){
	$array['todosCampos']['D049_Preco_3'] = gCorrigeNumero($array['todosCampos']['D049_Preco_3']);
	if($array['todosCampos']['D049_Flag_Promocao'] == "S"){
		return "<font color='green'><b>{$array['todosCampos']['D049_Preco_3']}</b></font>";
	} else {
		return $array['todosCampos']['D049_Preco_3'];
	}
}

?>