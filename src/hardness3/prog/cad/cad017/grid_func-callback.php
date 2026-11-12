<?php
namespace hardness;
/**
 * CAD - cad009 - Cadastro de Catálogos
 */

function tipoDespesa($array) {
	global $g;
	if($array['todosCampos']['D192_Flag_Tipo'] == 'F'){
		return 'FÓRMULA';
	} elseif($array['todosCampos']['D192_Flag_Tipo'] == 'V'){
		return 'VALOR FIXO';
	} else {
		return '';
	}
}

function tipoRateio($array) {
	global $g;
	if($array['todosCampos']['D192_Flag_Tipo_Rateio'] == 'C'){
		return 'CUSTO';
	} elseif($array['todosCampos']['D192_Flag_Tipo_Rateio'] == 'P'){
		return 'PESO';
	} else {
		return '';
	}
}

function baseRateio($array) {
	global $g;
	if($array['todosCampos']['D192_Flag_Base_Rateio'] == '1'){
		return 'TOTAL DI';
	} elseif($array['todosCampos']['D192_Flag_Base_Rateio'] == '2'){
		return 'TOTAL ADIÇÃO';
	} elseif($array['todosCampos']['D192_Flag_Base_Rateio'] == '3'){
		return 'PESO ADIÇÃO';
	} else {
		return '';
	}
}

function tipoDespesaComex($array) {

	if($array['todosCampos']['D191_Tipo_Despesa'] == '2'){
		return 'THC';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '3'){
		return 'SEGURO';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '4'){
		return 'SISCOMEX';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '5'){
		return 'AFRMM';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '6'){
		return 'FRETE';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '7'){
		return 'DESP.ACESSÓRIA';
	} elseif($array['todosCampos']['D191_Tipo_Despesa'] == '8'){
		return 'DESP.ADUANEIRA';
	} else {
		return 'OUTROS';
	}
}

function tipoComex($array) {

	if($array['todosCampos']['D191_Tipo'] == '1'){
		return 'Outras despesas';
	} elseif($array['todosCampos']['D191_Tipo'] == '2'){
		return 'Despesa Aduaneira';
	} elseif($array['todosCampos']['D191_Tipo'] == '3'){
		return 'Despesa Acessória';
	}
}

?>