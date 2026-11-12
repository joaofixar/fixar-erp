<?php
namespace hardness;
/**
 * VEN - VEN011 - Manutenção Bens
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/VEN011.php');
$VEN011 = new VEN011();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T142_Id = isset($_REQUEST['T142_Id']) ? $_REQUEST['T142_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('T142_Id', $T142_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $VEN011->excluir($T142_Id);
			$resposta['data'] = 'T142_Id-' . $T142_Id;
		}
		break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>