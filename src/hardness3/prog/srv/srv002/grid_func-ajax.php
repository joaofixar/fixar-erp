<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * srv - srv002 - Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/SRV002.php');
$srv002 = new srv002();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarPrioridade':
			require_once("bibliotecas/classes/VEN002.php");
			
			$T147_Id 		= isset($r_T147_Id) ? $r_T147_Id : false;
			$campoEditado 	= isset($r_campoEditado) ? $r_campoEditado : false;
			$valorAntigo 	= isset($r_valorAntigo) ? $r_valorAntigo : false;
			$novoValor 		= isset($r_novoValor) ? $r_novoValor : false;
			
			
			$VEN002 = new VEN002();
			
			$retorno = $srv002->srv002atualizarPrioridade($T147_Id, $campoEditado, $valorAntigo, $novoValor);
			
			if ($retorno === true) {
				$resposta['code'] = true;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
	break;
	
	case 'marcarConferido':
		$T147_Id = isset($_REQUEST['T147_Id']) ? $_REQUEST['T147_Id'] : false;
		$conferido = $srv002->marcarConferido($T147_Id);

		if ($conferido === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $conferido;			
		}
	break;	
	
	case 'marcarReprovado':
		$T147_Id = isset($_REQUEST['T147_Id']) ? $_REQUEST['T147_Id'] : false;
		$conferido = $srv002->marcarReprovado($T147_Id);

		if ($conferido === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $conferido;			
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>