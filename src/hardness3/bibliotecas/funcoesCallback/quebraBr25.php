<?php
namespace hardness;

/**
* Quebra (<br />) de 40 em 40 espaços o texto
* 
* @param mixed $array
*/
function quebraBr25($array){
	global $g;
	$novo = '';
	$count = 0;
	for ($i = 0; $i < strlen($array['campoAtual']); $i++) {
		$count++;
		if ($count > 25 && $array['campoAtual'][$i] == ' ') {
			$count = 0;
			$novo .= '<br />';
		} else {
			$novo .= $array['campoAtual'][$i];
		}
	}
	return $novo;
}
