<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD017.php');
$CAD017 = new CAD017();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {

	case 'processarOrdemDespesa':
		$D192_Id = isset($r_D192_Id) ? $r_D192_Id : false;
		$novoValor = isset($r_novoValor) ? $r_novoValor : false;

		$retorno = $CAD017->processarOrdemDespesa($D192_Id, $novoValor);
		if($retorno === true){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['code'] = $retorno;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";