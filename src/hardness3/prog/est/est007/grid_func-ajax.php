<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {

	case 'atualizaT048Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T048_Id = isset($_REQUEST['T048_Id']) ? $_REQUEST['T048_Id'] : false;
		$D004_Id = getD004($T066_Id);
		mysql_query("UPDATE T048 SET T048_T066_Id = '{$T066_Id}' WHERE T048_Id = '{$T048_Id}'");
		
		$resposta['data'] = true;
	break;

    case 'atualizaT071Local':
		$T066_Id = isset($_REQUEST['T066_Id']) ? $_REQUEST['T066_Id'] : false;
		$T071_Id = isset($_REQUEST['T071_Id']) ? $_REQUEST['T071_Id'] : false;
		$D004_Id = getD004($T066_Id);
		mysql_query("UPDATE T071 SET T071_T066_Id = '{$T066_Id}' WHERE T071_Id = '{$T071_Id}'");
		
		$resposta['data'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
