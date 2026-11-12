<?php
namespace hardness;

function validaCampoProduzir($array){
	global $g;
	$disabled="";
	$C007_Nome = "";
	$retorno = "<input onchange=\"est014CheckboxProduzir(this, '{$array['todosCampos']['T189_Id']}', '{$g['divId']}')\" type='checkbox' id='D085_Flag_Retornado' title='Retornado?'";
	if ($array['todosCampos']['D085_Flag_Retornado']=='S'){
		$retorno .="checked='checked'";
	}

	return $retorno .= " {$disabled}/>";
}


?>