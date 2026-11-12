<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
			case 'atualizarCamposFornecedor':
			$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
			
			$sql = "SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='{$D024_Id}'";
			$result = mysql_query($sql);
			$linha = mysql_fetch_array($result);
			
			if (!empty($result)) {
				$resposta['code'] = true;
				$resposta['data'] = $linha['D024_Nome_Empresa'];
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
		break;
			case 'adicionarFornecedor':
			$sqlLista = "SELECT * FROM T240 WHERE T240_C007_Id = {$g['usuarioAtual']} AND T240_Status = 'A';";
			$res = mysql_query($sqlLista);
			while($row = mysql_fetch_array($res)){
				if($row['T240_D024_Id'] == $_REQUEST['D024_Id']){
					$sql = "UPDATE T240 SET T240_Status = 'F' WHERE T240_D024_Id = {$_REQUEST['D024_Id']};";
					mysql_query($sql);
					$check = 1;
				}
			}
			$res = mysql_query($sqlLista);

			$sqlOrdemLista = mysql_query("SELECT T240_Ordem FROM T240 WHERE T240_C007_Id = {$g['usuarioAtual']} AND T240_Status = 'A' ORDER BY T240_Ordem DESC LIMIT 1");
			$resOrdem = mysql_fetch_array($sqlOrdemLista);
			if(mysql_num_rows($sqlOrdemLista) <= 0){
				$resOrdem['T240_Ordem'] = 0;
			} else {
				$resOrdem['T240_Ordem']++;
			}
			if(mysql_num_rows($res) < 7){
				$sql = "INSERT INTO T240 (T240_D024_Id, T240_C007_Id, T240_Data, T240_Status, T240_Ordem) VALUES ({$_REQUEST['D024_Id']}, {$g['usuarioAtual']}, CURDATE(), 'A', '{$resOrdem['T240_Ordem']}');";			
				mysql_query($sql);	
				$_SESSION['alteraTab'] = 1;	
				$resposta['code'] = true;	
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Limite de tabs. Feche uma tab para abrir outra.";
			}
		break;
			case 'removerFornecedor':
			$sql = "UPDATE T240 SET T240_Status = 'F' WHERE T240_D024_Id = {$_REQUEST['D024_Id']};";
			mysql_query($sql);	
		break;
		
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>