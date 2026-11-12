<?php
namespace hardness;
require_once('bibliotecas/funcoesCallback/abrirJanela.php');

function abrirDocumento($array){	
	$params = array('Documento', "{$array['todosCampos']['D194_Link']}", 'T302_Tabela_Id', '1070,540',false);		
	return abrirJanela($array, $params);		
}