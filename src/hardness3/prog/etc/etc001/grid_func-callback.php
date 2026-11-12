<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Funções PHP para callbacks
*/
function possuiCache($array) {
	global $g;
	if (empty($array['campoAtual'])) {
		return '';
	} else {
		return '<a href="#" onclick="abrirVersaoCache(\''.$array['todosCampos']['T144_Id'].'\', \''.$g['divIdRoot'].'\', \''.$g['divId'].'\', event);"> ver offline </a>';
	}
}

function tipoDocumento($array) {
	global $g;
	if ($array['campoAtual'] == 'F') {
		return 'FOTO SISTEMA';
	} else {
		return 'LINK ORÇAMENTO/PEDIDO';
	}
}

