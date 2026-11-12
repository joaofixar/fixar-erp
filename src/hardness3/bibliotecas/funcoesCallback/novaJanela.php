<?php
namespace hardness;

function novaJanela($array, $param){
	global $g;

	$titulo = isset($param[0]) ? $param[0] : '';
	$url = isset($param[1]) ? $param[1] : '';
	$acaoId = isset($param[2]) ? $param[2] : '';
	$tamanho = isset($param[3]) ? $param[3] : '750,500';
	$tamanho = explode(',', $tamanho);
	if (preg_match('/^([0-9]|\.)+$/', $array['campoAtual'])) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual']);
	}
	//return "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', '{$titulo}', '{$url}', '&acaoId=' + encodeURIComponent('{$array['todosCampos'][$acaoId]}'), {$tamanho}); return false;\">{$array['campoAtual']}</a>";
	return "<a href=\"#\" onclick=\"nova_janela('{$url}' + '{$array['todosCampos'][$acaoId]}',0,0,{$tamanho[0]},{$tamanho[1]},'yes','no','no','no','yes','no','{$titulo}'); return false;\">{$array['campoAtual']}</a>";
}