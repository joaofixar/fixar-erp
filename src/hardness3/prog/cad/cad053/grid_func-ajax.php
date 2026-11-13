<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD052.php');
$CAD052 = new CAD052();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluirItem':
		$D074_Id = isset($_REQUEST['D074_Id']) ? $_REQUEST['D074_Id'] : false;

		$result = $CAD052->excluirItem($D074_Id);

		if ($result === true) {
			$resposta['code'] = true;
		} else{
			$resposta['code'] = false;
			$resposta['data'] = $result;			
		}
		
	break;	

	case 'excluirProcesso':
		$D075_Id = isset($_REQUEST['D075_Id']) ? $_REQUEST['D075_Id'] : false;
		$result = $CAD052->excluirProcesso($D075_Id);

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