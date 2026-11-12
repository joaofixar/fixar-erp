<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
* Ações
*/
switch($g['r'][3]) {
	case 'verificarNome':
		$r_nome = md5($r_nome);
		$sql = "SELECT D140_Id FROM D140 WHERE D140_Nome_Md5 = '{$r_nome}';";
		$res = mysql_query($sql);
		if (mysql_numrows($res) > 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Já existe um grid com este nome!";
		}
	break;
	
	case 'salvarGrid':
		$r_nome = strtoupper($r_nome);
		$nome = mysql_real_escape_string($r_nome);
		$nomeMd5 = md5($r_nome);
		$sql = mysql_real_escape_string($r_sql);
		$sql = "INSERT INTO D140 (`D140_Nome`, `D140_Nome_Md5`, `D140_SQL`, `D140_Data_Criacao`) VALUES ('{$nome}','{$nomeMd5}','{$sql}',CURDATE());";
		$res = mysql_query($sql);
		$resposta['data'] = $g['mysqlLastId'];
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";