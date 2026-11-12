<?php
namespace hardness;
/**
 * VEN - VEN010 - Contratos de Locação
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN010.php');
$VEN010 = new VEN010();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T141_Id = isset($_REQUEST['T141_Id']) ? $_REQUEST['T141_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T141_Id', $T141_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $VEN010->excluir($T141_Id);
			$resposta['data'] = 'T141_Id-' . $T141_Id;
		}
		break;
		
	case 'gerarContasReceber':
		$T141_Id = isset($_REQUEST['T141_Id']) ? $_REQUEST['T141_Id'] : false;

			$retorno = $VEN010->gerarContasReceber($T141_Id);
			$resposta['data'] = $retorno;			
			
			if ($retorno===true)
				$resposta['code'] = true;
			else
				$resposta['code'] = false;
			
		break;		
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>