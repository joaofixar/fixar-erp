<?php
namespace hardness;
/**
 * CAD - CAD011 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD011.php');
$CAD011 = new CAD011();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$D110_Id = isset($_REQUEST['D110_Id']) ? $_REQUEST['D110_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('D110_Id', $D110_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD011->excluir($D110_Id);
			$resposta['data'] = 'D110_Id-' . $D110_Id;
		}
		break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>