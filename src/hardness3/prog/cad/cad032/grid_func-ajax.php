<?php
namespace hardness;
/**
 * FIN - FIN001 - Contas a Receber
 *
 * Processamento de ações AJAX
 */

// Classes
require_once('bibliotecas/classes/CAD032.php');
$CAD032 = new CAD032();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	 
	case 'gravarCampo':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$C029_Id = isset($_REQUEST['C029_Id']) ? $_REQUEST['C029_Id'] : false;
		$C021_Id = isset($_REQUEST['C021_Id']) ? $_REQUEST['C021_Id'] : false;
		$CAD032->updateC030_Acesso_Liberado($C029_Id, $C021_Id, $selected);
	break;
	
	case 'gravarCampoResumoselatorios':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$C027_Id = isset($_REQUEST['C027_Id']) ? $_REQUEST['C027_Id'] : false;
		$C021_Id = isset($_REQUEST['C021_Id']) ? $_REQUEST['C021_Id'] : false;
		$CAD032->updateC028_Acesso_Liberado($C027_Id, $C021_Id, $selected);
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>