<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
//require_once('bibliotecas/classes/CAD022.php');
//$CAD022 = new CAD022();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'checkboxFlagComercial':
		global $g;
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D027_Id = isset($_REQUEST['D027_Id']) ? $_REQUEST['D027_Id'] : false;
		$sql = "UPDATE D027 SET D027_FLag_Comercial='{$selected}' WHERE D027_Id='{$D027_Id}'";
		mysql_query($sql);
		$respota['code'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>