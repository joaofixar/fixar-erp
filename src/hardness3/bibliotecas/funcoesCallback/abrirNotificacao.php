<?php
namespace hardness;

function abrirNotificacao($array = array(), $param = array()){
	global $g;

	$titulo = isset($param[0]) ? $param[0] : '';
	$url = isset($param[1]) ? $param[1] : '';
	$acaoId = isset($param[2]) ? $param[2] : '';
	$local = isset($param[3]) ? $param[3] : 'right';
	if (isset($param[4]) && $param[4]) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual'], $param[4]);
	}
	$extra = isset($param[5]) ? $param[5] : '';
	if (isset($param[6]) && $param[6]) {
		$array['campoAtual'] = $param[6];
	}
	$width = (isset($param[7]) && $param[7]) ? $param[7] : '236'; //236 é o width default do popover
	$center = (isset($param[8]) && $param[8]) ? $param[8] : 'N';

	if($width == '-1'){
		$width = $array['todosCampos']['width'];
	}
	$id = 'popover-' . uniqid();

	return "<a id=\"{$id}\" href=\"#\" style='text-decoration: none;' onclick=\"abrirNotificacao('{$id}', '{$titulo}', '{$url}', '{$array['todosCampos'][$acaoId]}', '{$local}', '{$extra}', '{$width}', '{$center}', '{$g['divId']}', '{$g['divIdParent']}', '{$g['divIdRoot']}');\"><u>{$array['campoAtual']}</u></a>";
}