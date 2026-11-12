<?php
namespace hardness;
/**
 * CAD - CAD009 - Cadastro de Catálogo
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD009.php');
$CAD009 = new CAD009();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	// -> campoExclusao($array) -> fin002CampoExclusao(T015_Id)
	case 'excluir':
		$D107_Id = isset($_REQUEST['D107_Id']) ? $_REQUEST['D107_Id'] : false;
		$verifica = $g['conf']->restricaoExcluir('D107_Id', $D107_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD009->excluir($D107_Id);
			$resposta['data'] = 'D107_Id-' . $D107_Id;
		}
		break;
		
	case 'excluirItem':
		$D108_Id = isset($_REQUEST['D108_Id']) ? $_REQUEST['D108_Id'] : false;
		$D108_D107_Id = isset($_REQUEST['D108_D107_Id']) ? $_REQUEST['D108_D107_Id'] : false;
		
			// excluir
			$retorno = $CAD009->excluirItem($D108_Id,$D108_D107_Id);
			if ($retorno===true){
				$resposta['code'] = true;
			}else{
				$resposta['code'] = false;				
				$resposta['data'] = 'Falha na exclusão do Ítem...';
			}
				
			$resposta['data'] = 'D108_Id-' . $D108_Id;
		
		break;		
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>