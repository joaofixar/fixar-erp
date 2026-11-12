<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

switch($g['r'][3]) {
	case 'verificarPermissao':
		if (!empty($r_C029_Id) && !isset($g['c029Ids'][$r_C029_Id])) {
			$resposta['code'] = false;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";