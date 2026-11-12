<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CRM005.php');
$CRM005 = new CRM005();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluirProduto':
		$T099_Id = isset($_REQUEST['T099_Id']) ? $_REQUEST['T099_Id'] : false;
		$result = $CRM005->excluirProduto($T099_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;		
	
	case 'preencherPedido':
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		$dataPedido = isset($_REQUEST['dataPedido']) ? $_REQUEST['dataPedido'] : false;
		$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : false;
		
		$result = $CRM005->incluirProgramacao($data, $dataPedido, $D024_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
   		
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>