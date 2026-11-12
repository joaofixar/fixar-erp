<?php
namespace hardness;


require_once('bibliotecas/classes/SRV004.php');
$SRV004 = new SRV004();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'marcarInspecionado':
		
		$T138_Id = isset($_REQUEST['T138_Id']) ? $_REQUEST['T138_Id'] : false;						
		$retorno = $SRV004->aprovarTroca($T138_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}					
		
	break;	
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>