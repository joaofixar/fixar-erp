<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
			case 'atualizarCamposClientes':
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
			case 'adicionarCliente':
			$sqlLista = "SELECT * FROM T194 WHERE T194_C007_Id = {$g['usuarioAtual']} AND T194_Status = 'A';";
			$res = mysql_query($sqlLista);
			while($row = mysql_fetch_array($res)){
				if($row['T194_D024_Id'] == $_REQUEST['D024_Id']){
					$sql = "UPDATE T194 SET T194_Status = 'F' WHERE T194_D024_Id = {$_REQUEST['D024_Id']};";
					mysql_query($sql);
					$check = 1;
				}
			}
				
			$res = mysql_query($sqlLista);
			if(mysql_num_rows($res) < 4){
				$sql = "INSERT INTO T194 (T194_D024_Id, T194_C007_Id, T194_Data, T194_Status) VALUES ({$_REQUEST['D024_Id']}, {$g['usuarioAtual']}, CURDATE(), 'A');";			
				mysql_query($sql);	
				$_SESSION['alteraTab'] = 1;	
				$resposta['code'] = true;	
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Limite de tabs. Feche uma tab para abrir outra.";
			}
		break;
			case 'removerCliente':
			$sql = "UPDATE T194 SET T194_Status = 'F' WHERE T194_D024_Id = {$_REQUEST['D024_Id']};";
			mysql_query($sql);	
		break;
		
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>