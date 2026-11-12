<?php
namespace hardness;

function colunaClasseCliente($array, $param){
	switch($array['todosCampos']['D024_Flag_Classe']){
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
		case 'X':
			$classe = "BGVermelho";
		break;
	}
	$array['campoAtual'] = "<span class='$classe'>{$array['campoAtual']}</span>";
	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	return abrirNotificacao($array, $param);
}