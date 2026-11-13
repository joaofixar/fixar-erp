<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'refresh':
		$callback = (isset($r_callback)) ? $r_callback : false;
		$resposta = array('code' => true, 'data' => array());
		
		$array = isset($r_array) ? unserialize(base64_decode($r_array)) : false;
		$geraSelect = gGeraSelect($array[0], $array[1], $array[2], isset($array[3]) ? $array[3] : '', isset($array[4]) ? $array[4] : false, isset($array[5]) ? $array[5] : false, isset($array[6]) ? $array[6] : false);
		$resposta['data'] = gProcessaSelect($geraSelect);
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
}
?>