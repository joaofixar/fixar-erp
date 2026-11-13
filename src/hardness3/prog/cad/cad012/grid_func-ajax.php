<?php
namespace hardness;
/**
 * CAD - CAD012 - Cadastro de Materiais
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD012.php');
$CAD012 = new CAD012();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$D111_Id = isset($_REQUEST['D111_Id']) ? $_REQUEST['D111_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('D111_Id', $D111_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD012->excluir($D111_Id);
			$resposta['data'] = 'D111_Id-' . $D111_Id;
		}
		break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>