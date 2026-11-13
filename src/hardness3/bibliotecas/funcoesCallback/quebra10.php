<?php
namespace hardness;

/**
* Quebra (...) 10
* 
* @param mixed $array
*/
function quebra10($array){
	global $g;
	if (strlen($array['campoAtual']) > 10) {
		$okjs1 = str_replace("'", "\\'", $array['campoAtual']);
		$okjs2 = str_replace('"', '\"', $array['campoAtual']);
		return '<a href="#" title="' . $okjs1 . '" onclick="alert(\'' . $okjs2 . '\'); return false;">' . substr($array['campoAtual'], 0, 10) . '...</a>';
	} else {
		return $array['campoAtual'];
	}
}
