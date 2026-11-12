<?php
namespace hardness;
function gMostraMaisNotificacao($array, $param){
	global $g;
	$tamanho = isset($param[0]) ? $param[0] : 15;
	$explode = isset($param[1]) ? $param[1] : '';
	if (strlen($array['campoAtual']) > $tamanho) {
		$array['todosCampos']['campoOriginal'] = $array['campoAtual'];
		$array['campoAtual'] = substr($array['campoAtual'], 0, $tamanho) . '...';
		require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
		return abrirNotificacao(
			$array,
			array(
				'Todos resultados',
				'/sistema/funcoes/gMostraMaisNotificacao/texto/',
				'campoOriginal',
				'right',
				'',
				$explode
			)
		);
	} else {
		return $array['campoAtual'];
	}
}