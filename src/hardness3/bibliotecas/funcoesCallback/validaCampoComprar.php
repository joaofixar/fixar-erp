<?php
namespace hardness;
function validaCampoComprar($array){
	global $g;
	if($array['todosCampos']['T189_Flag_Status'] != 'D'){
		return "<a href=\"#\" onclick=\"validaCampoComprar('{$g['divId']}', '{$g['divIdRoot']}', '{$array['todosCampos']['T006_T005_Id']}', '{$array['todosCampos']['T006_Id']}', '{$array['todosCampos']['T006_D009_Id']}', '{$array['todosCampos']['T189_Id']}');\"><b>Comprar</b></a>";
	} else {
		return '';
	}
}