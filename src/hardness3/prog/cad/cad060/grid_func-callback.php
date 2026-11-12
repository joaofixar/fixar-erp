<?php
namespace hardness;

function mostraTipoVariavel($array){
	global $g;
	switch($array['todosCampos']['T249_Tipo']){
		case '1':
			$tipo = "FRETE";
			break;
		case '2':
			$tipo = "COMISSÃO";
			break;
		case '3':
			$tipo = "LUCRO";
			break;
		case '4':
			$tipo = "TRIBUTOS";
			break;
		case '5':
			$tipo = 'TAXA PORTADOR';	
			break;
	}
	return $tipo;
}

function nomeDoNivel($array){
	global $g;
	$nivel = $array['todosCampos']['T249_Nivel_Acesso'];
	
	if($nivel==1){
		return "Vendedor";
	}else if($nivel==2){
		return "Supervisor";
	}else{
		return "Diretor";
	}

}	

function nomeCampoCalculado($array){
	global $g;
	$campo_calculado = $array['todosCampos']['T249_Flag_Campo_Calculado'];

	if($campo_calculado=='N'){
		return "Não";
	}else if($campo_calculado=='S'){
		return "Sim";
	}
}

function nomeCampoAlteraPreco($array){
	global $g;
	$altera_preco = $array['todosCampos']['T249_Flag_Altera_Preco'];

	if($altera_preco=='N'){
		return "Não";
	}else if($altera_preco=='S'){
		return "Sim";
	}
}


