<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: custosPorFornecedor()
*/

function custosPorFornecedor ($array) {
	$retorno = '';
	$itens = explode('-', trim($array['campoAtual'], '- '));
	foreach ($itens as $item) {
		if ($item>0){
			$item = trim($item);
			$item = gCorrigeNumero($item, 5);
			$retorno .= $item . "<br/>";
		}
	}
	if (empty($item))$retorno=' - ';
	return trim($retorno, '<br/>');
}
