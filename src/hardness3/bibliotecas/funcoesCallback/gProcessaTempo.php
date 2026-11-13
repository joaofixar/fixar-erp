<?php
namespace hardness;

function gProcessaTempo($array){
    global $g;
    $tempo = $array['campoAtual']; //Tempo em minutos

    $d = floor($tempo / 1440);
	$h = floor(($tempo - $d * 1440) / 60);
	$m = $tempo - ($d * 1440) - ($h * 60);

	$retorno = '';
	if($d > 0){
		$retorno = ($d > 1) ? "{$d} dias" : "{$d} dia";
	}
	if($h > 0){
		$retorno .= ($h > 1) ? " {$h} horas" : " {$h} hora";
	}
	if($m > 0 && $d <= 0){
		$retorno .= ($m > 1) ? " {$m} minutos" : " {$m} minuto";
	}
	return trim($retorno);
}