<?php
namespace hardness;
/**
 * CAD - cad065 - Cadastro de Lacres
 */

function validaCampoTipo($array){
	if ($array['todosCampos']['D171_Tipo'] == 1) {
		return "TRANSPORTE";
	} else if ($array['todosCampos']['D171_Tipo'] == 2) {
		return "CARGA";
	} else if ($array['todosCampos']['D171_Tipo'] == 3) {
		return "MDF-e";
	} else {
		return "RODOVIÁRIO";
	}
}

function validaCampoStatus($array){
	if($array['todosCampos']['D171_Flag_Ativo'] == 'S'){
		return "ATIVO";
	}else{
		return "INATIVO";
	}
}

?>
