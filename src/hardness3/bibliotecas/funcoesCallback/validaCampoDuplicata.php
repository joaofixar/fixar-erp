<?php
namespace hardness;
function validaCampoDuplicata($array, $param){
	global $g;
	$tamanho = isset($param[0]) ? $param[0] : 15;
	$explode = isset($param[1]) ? $param[1] : '';
	$campo   = isset($param[2]) ? $param[2] : 'campoAtual';
	if (strlen($array['campoAtual']) > $tamanho) {
		require_once('bibliotecas/funcoesCallback/gMostraMaisNotificacao.php');
		return gMostraMaisNotificacao($array, $param);
	} else if($array['todosCampos'][$campo] != '') {
		require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
		return abrirNotificacao(
			$array,
			array(
				'Todos resultados',
				'/sistema/funcoes/gMostraMaisNotificacao/texto/',
				$campo,
				'right',
				'',
				$explode
			)
		);
	} else {
		return $array['campoAtual'];
	}
}