<?php
namespace hardness;
/**
 * CAD - CAD008 - Cadastro de Bem
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD008.php');
$CAD008 = new CAD008();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	// -> campoExclusao($array) -> fin002CampoExclusao(T015_Id)
	case 'excluir':
		$D106_Id = isset($_REQUEST['D106_Id']) ? $_REQUEST['D106_Id'] : false;
		$verifica = rVerificaExcluir('D106_Id', $D106_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD008->excluir($D106_Id);
			$resposta['data'] = 'D106_Id-' . $D106_Id;
		}
		break;
		
		case 'gravarCampo':
			$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
			$D120_Id = isset($_REQUEST['D120_Id']) ? $_REQUEST['D120_Id'] : false;
			$D106_Id = isset($_REQUEST['D106_Id']) ? $_REQUEST['D106_Id'] : false;
			$D125_Id = isset($_REQUEST['D125_Id']) ? $_REQUEST['D125_Id'] : false;
			$CAD008->updateT159_Flag_Ok($D120_Id, $D106_Id, $selected, $D125_Id);
		break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
