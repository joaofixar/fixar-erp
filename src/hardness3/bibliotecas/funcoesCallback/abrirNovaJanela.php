<?php
namespace hardness;

function abrirNovaJanela($array, $param){
	global $g;
	$titulo = isset($param[0]) ? $param[0] : '';
	$url = isset($param[1]) ? $param[1] : '';
	$acaoId = isset($param[2]) ? $array['todosCampos'][$param[2]] : '';
	$tamanho = isset($param[3]) ? $param[3] : '';
	$array['campoAtual'] = isset($param[5]) ? $param[5] : $array['campoAtual'];

	if (isset($param[4]) && $param[4]) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual']);
	}
	return "<a href=\"#\" onclick=\"nova_janela('{$url}' + '{$acaoId}', 0,0,750,500,'yes','no','no','no','yes','no','{$titulo}'); return false;\">{$array['campoAtual']}</a>";
}
