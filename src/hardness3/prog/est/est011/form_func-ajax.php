<?php
namespace hardness;
/**
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'atualizarCamposProdutos':
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;		
		
		$sql = <<<EOT
			SELECT *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   				WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);				
			
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
	

	case 'carregaContatoGarantiaCliente':

		$T182_D024_Id_Cliente = isset($r_T182_D024_Id_Cliente) ? $r_T182_D024_Id_Cliente : false;

		if($T182_D024_Id_Cliente != false){
			$resposta['data']['T182_D013_Id_Cliente'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$T182_D024_Id_Cliente}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true));
			$resposta['data']['T182_D013_Id_Cliente_refreshBtn'] = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$T182_D024_Id_Cliente}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true)));
			$resposta['code'] = true;
		}
		else{
			$resposta['code'] = false;	
		}
			
	break;

	case 'carregaContatoGarantiaFornecedor':

		$T182_D024_Id_Fornecedor = isset($r_T182_D024_Id_Fornecedor) ? $r_T182_D024_Id_Fornecedor : false;
		if($T182_D024_Id_Fornecedor != false){
			$resposta['data']['T182_D013_Id_Fornecedor'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$T182_D024_Id_Fornecedor}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true));
			$resposta['data']['T182_D013_Id_Fornecedor_refreshBtn'] = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='{$T182_D024_Id_Fornecedor}' AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true)));
			$resposta['code'] = true;
		}
		else{
			$resposta['code'] = false;	
		}			
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
