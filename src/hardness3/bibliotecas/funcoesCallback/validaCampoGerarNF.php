<?php
namespace hardness;
// Pedido, D049_Id
function validaCampoGerarNF ($array){
	require_once('prog/crm/crm001/grid_func-js.php');
	global $g;
	$Nome_GerarNF 		= $array['todosCampos']['Nome_GerarNF'];
	$T005_C007_Gerar_NF = $array['todosCampos']['T005_C007_Gerar_NF'];
	$T005_Flag_Gerar_NF = $array['todosCampos']['T005_Flag_Gerar_NF'];
	$T005_Id 			= $array['todosCampos']['T005_Id'];
	$retorno = "<input onclick=\"crm001CheckboxGerarNF(this, '{$T005_Id}', '{$g['divId']}');\" type='checkbox' id='T005_Flag_Gerar_NF' title='Gerar NF'";
    if ($T005_C007_Gerar_NF > 0 && $T005_Flag_Gerar_NF == 'S'){
		return $retorno .="checked='checked' /><br /><font color=gray style='font-size:8px;'>".$Nome_GerarNF."</font>";
    } else {
    	return $retorno .= " />";
    }
}