<?php
namespace hardness;

function colunaCurvaABC($array, $param = array()){
	switch($array['campoAtual']){
		case 'A':
			$classe = "BGVerde";
		break;
		case 'B':
			$classe = "BGAzul";
		break;
		case 'C':
			$classe = "BGAmarelo";
		break;
		case 'D':
			$classe = "BGVermelho";
		break;
	}
	$array['campoAtual'] = "<span class='$classe'>{$array['campoAtual']}</span>";
	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	return abrirNotificacao($array, $param);
}