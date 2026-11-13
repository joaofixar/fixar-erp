<?php
namespace hardness;
/**
* VEN - VEN009 - logistica aluguel
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/VEN009.php');
$VEN009 = new VEN009();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'excluir':
		$T143_Id = isset($_REQUEST['T143_Id']) ? $_REQUEST['T143_Id'] : false;
		if (empty($T143_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro não existe.';
		} else {
			$retorno = $VEN009->excluir($T143_Id);
			if ($retorno === true) {
				$resposta['data'] = 'T143_Id-' . $T143_Id;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;
	
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>