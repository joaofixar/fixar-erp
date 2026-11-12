<?php
namespace hardness;
// D009_Quantidade_Estoque
function estoqueCorD009_Quantidade_Estoque($array){
	if($array['todosCampos']['D009_Quantidade_Estoque']<0){
		$array['todosCampos']['D009_Quantidade_Estoque'] = gCorrigeNumero($array['todosCampos']['D009_Quantidade_Estoque']);
		return "<font style='color:red;'>{$array['todosCampos']['D009_Quantidade_Estoque']}</font>";
	} else {
		return gCorrigeNumero($array['todosCampos']['D009_Quantidade_Estoque']);
	}
}