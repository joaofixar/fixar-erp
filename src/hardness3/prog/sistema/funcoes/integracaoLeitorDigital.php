<?php
namespace hardness;
/**
* Atualiza o arquivo de filtros com base na busca
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'tryIdentify':
		$resposta = array('code' => false, 'data' => array());
		$r_key = mysql_real_escape_string($r_key);
		$sql = "SELECT * FROM T181 WHERE T181_Codigo = '{$r_key}'";
		$res = mysql_query($sql);
		if (mysql_numrows($res) != 0) {
			$row = mysql_fetch_assoc($res);
			$resposta = array('code' => true, 'data' => $row['T181_Status']);
		}
		$sql = "DELETE FROM T181 WHERE T181_Codigo = '{$r_key}';";
		mysql_query($sql);
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;
	
	case 'identify':
		//$r_key
		//$r_id
		$r_key = mysql_real_escape_string($r_key);
		$sql = "DELETE FROM T181 WHERE T181_Codigo = '{$r_key}';";
		mysql_query($sql);
		if ($r_id) {
			$sql = "insert into T181 (T181_Codigo, T181_Status, T181_Registrado) values ('{$r_key}',{$r_id},'".time()."')";
			mysql_query($sql);
		}
	break;
	
	case 'checkMD5':
		//$r_m
		echo "ok";
	break;
	
	case 'upload':
		print_r($_FILES);
	break;
}
?>