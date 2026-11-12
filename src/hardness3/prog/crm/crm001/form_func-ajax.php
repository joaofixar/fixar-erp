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
        $D024 = mysql_query("select D024_C007_Vendedor_Interno, C007_C007_Id, D024_Flag_Ativo from D024 left join C007 on C007_Id=D024_C007_Vendedor_Interno where D024_Id='{$_REQUEST['D024_Id']}'");
        $mD024 = mysql_fetch_array($D024);
        if ($mD024['D024_Flag_Ativo']!='S'){
			$resposta['code'] = false;
			$resposta['data'] = "Este cliente está inativo!";
		}
        if ($mD024['D024_C007_Vendedor_Interno']>0 and $g['nivelAlcada']<4){
        	if ($g['nivelAlcada']==1){
	        	if ($g['usuarioAtual']!=$mD024['D024_C007_Vendedor_Interno']){
					$resposta['code'] = false;
					$resposta['data'] = "Este cliente não pertence a sua carteira.";
				}
			}
        	if ($g['nivelAlcada']==2){
	        	if ($g['usuarioAtual']!=$mD024['D024_C007_Vendedor_Interno'] and $g['usuarioAtual']!=$mD024['C007_C007_Id']){
					$resposta['code'] = false;
					$resposta['data'] = "Este cliente não pertence a sua carteira e de seus vendedores.";
				}
			}
		}
		if ($resposta['code']){
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
			if(mysql_num_rows($res) < 7){
				$sql = "INSERT INTO T194 (T194_D024_Id, T194_C007_Id, T194_Data, T194_Status) VALUES ({$_REQUEST['D024_Id']}, {$g['usuarioAtual']}, CURDATE(), 'A');";			
				mysql_query($sql);	
				$_SESSION['alteraTab'] = 1;	
				$resposta['code'] = true;	
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Limite de tabs. Feche uma tab para abrir outra.";
			}
		}
	break;

	case 'removerCliente':
		$sql = "UPDATE T194 SET T194_Status = 'F' WHERE T194_D024_Id = {$_REQUEST['D024_Id']};";
		mysql_query($sql);	
	break;

	case 'ativarClienteVendedor':
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		/* Caso o usuário tiver nivelAlcada > 2 tem poder de selecionar qual vendedor
		vai tratar do cliente, form envia através do JS o id do vendedor selecionado.
		Se caso não for enviado um id é utilizado o usuarioAtual para ser selecionado
		como vendedor */
		$D024_C007_Vendedor_Interno = isset($r_D024_C007_Vendedor_Interno) ? $r_D024_C007_Vendedor_Interno : $g['usuarioAtual'];
		
		$sql = "SELECT D024_C007_Vendedor_Interno,
					   C007_Usuario_Acessos
				  FROM D024
			 LEFT JOIN C007 ON C007_Id =  D024_C007_Vendedor_Interno
				 WHERE D024_Id='{$D024_Id}'";

		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);

		$msg = ($linha['D024_C007_Vendedor_Interno'] != $D024_C007_Vendedor_Interno) ? ". Vendedor de: {$linha['D024_C007_Vendedor_Interno']}. Para: {$D024_C007_Vendedor_Interno}." : "";

		if($linha['D024_C007_Vendedor_Interno'] == $vendedorId && $linha['C007_Usuario_Acessos'] == 'N'){
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível ativar o cliente, no momento seu usuário é o vendedor deste cliente.";
		} else {
			mysql_query("UPDATE D024 SET D024_Flag_Comercial = 'A', D024_C007_Vendedor_Interno = '{$D024_C007_Vendedor_Interno}', D024_Data_Ativo = curdate() WHERE D024_Id = '{$D024_Id}'");
			$mensagem = "CLIENTE ATIVADO POR {$g['C007']['C007_Primeiro_Nome']}";
			mysql_query("INSERT INTO D085 (D085_D024_Id, D085_Data, D085_Mensagem) VALUES ('{$D024_Id}', CURDATE(), '{$mensagem}{$msg}')");
			
			$resposta['code'] = true;
			$resposta['data'] = "Cliente ativado com sucesso";
		}
	break;

	case 'carregarContatosClienteAgenda':
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

		$resposta['data']['D085_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$D024_Id}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true));
		$resposta['data']['D085_D013_Id_refreshBtn'] = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$D024_Id}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true)));
		$resposta['code'] = true;
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
