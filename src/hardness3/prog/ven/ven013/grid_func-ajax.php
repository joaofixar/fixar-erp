<?php
namespace hardness;
/**
 * VEN - VEN013 - Cadastro de Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN013.php');
$VEN013 = new VEN013();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'ven013AprovarPedido':
		$T005_Id = isset($_REQUEST['T005_Id']) ? $_REQUEST['T005_Id'] : false;
		
		$retorno = $VEN013->ven013AprovarPedido($T005_Id);

		$resposta['code'] = false;
		$resposta['data'] = $retorno;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>