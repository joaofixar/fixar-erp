<?php
namespace hardness;
function gPorcentagem($array){
	global $g;
	if (empty($array['campoAtual']) || $array['campoAtual'] == 0) {
		return '-';
	} else {
		return $array['campoAtual'] . '%';
	}
}