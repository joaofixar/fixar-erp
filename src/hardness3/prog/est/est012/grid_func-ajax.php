<?php
namespace hardness;
/**
 * VEN - VEN013 - Cadastro de Pedidos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/EST012.php');
$EST012 = new EST012();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'est012AprovarCotacao':
		$T173_Id = isset($_REQUEST['T173_Id']) ? $_REQUEST['T173_Id'] : false;
		$verifica = $EST012->est012AprovarCotacao($T173_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		}else{
			$resposta['code'] = true;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>