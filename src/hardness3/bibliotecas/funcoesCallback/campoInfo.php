<?php
namespace hardness;

function campoInfo($array, $param){
	global $g;
	$dadosCampo = isset($param[0]) ? $param[0] : '';
	$titulo = isset($param[1]) ? $param[1] : '';
	$acaoId = isset($param[2]) ? $param[2] : '';
	$local = isset($param[3]) ? $param[3] : 'right';
	if(isset($param[4]) && $param[4]) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual'], $param[4]);
	}
	$width = (isset($param[5]) && $param[5]) ? $param[5] : '236'; //236 é o width default do popover
	$id = 'popover-' . uniqid();
	$array['campoAtual'] = ($array['campoAtual'] == '' || !isset($array['campoAtual'])) ? "????" : $array['campoAtual'];
	return <<<EOT
<a id="{$id}" href="#" onclick="campoInfo('{$id}', '{$dadosCampo}', '{$titulo}', '{$array['todosCampos'][$acaoId]}', '{$local}', '{$width}', '{$g['divId']}', '{$g['divIdRoot']}');">{$array['campoAtual']}</a>
EOT;
}