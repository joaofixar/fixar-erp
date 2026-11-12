<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraEstadoTransportadoras':
		$D022_D018_Id = isset($r_D022_D018_Id) ? $r_D022_D018_Id : false;
		$resposta['code'] = true;
		$resposta['data']['D022_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_D022_D018_Id.' ORDER BY D020_Nome_Cidade'));
	break;	
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>