<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */

function erp004_Dia_Semana($array) {
	global $g;
	$data=$array['todosCampos']['R001_Data'];
	$data = gPegaDiaSemana($data);
	if($data == "Sábado"){
		$data = substr($data, 0, 4);
	} else {
		$data = substr($data, 0, 3);
	}
	return $data;
}	

function retornaAno($array) {
	$ano = $array['todosCampos']['R001_Data'];
	$ano = substr($ano, 0, 4);
	return $ano;
}

function retornaMes($array) {
	$ano = $array['todosCampos']['R001_Data'];
	$ano = gCorrigeData($ano);
	$ano = substr($ano, 3, 11);
	return $ano;
}
?>