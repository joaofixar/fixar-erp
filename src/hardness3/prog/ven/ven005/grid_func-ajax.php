<?php
namespace hardness;
/**
 * VEN - VEN005 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN005.php');
$VEN005 = new VEN005();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'ven005AprovarOrcamento':
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
		$verifica = $VEN005->ven005AprovarOrcamento($T003_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>