<?php
namespace hardness;
	/**
	* SRV - SRV001 - Cadastro de Serviços
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'atualizarCamposProdutos':
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
	
			$sql = <<<EOT
			SELECT  *
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
			
			$cClausula =" select *, ";
			$cClausula.=" D053_ST_ICMS_Interno, 
			              D053_ST_ICMS_Inter_Estadual, 
			              D053_Flag_ST, 
			              D018_Id, 
			              C004_D018_Id, 
			              C004_CFOP_ST_Venda_Dentro_Estado, 
			              C004_CFOP_ST_Venda_Fora_Estado,
			              C004_CFOP_CONSUMO_Venda_Dentro_Estado,
			              C004_CFOP_CONSUMO_Venda_Fora_Estado,
			              D024_Flag_Contribuinte ";
			$cClausula.=" from T003 ";
			$cClausula.=" left join D024 on D024_Id=T003_D024_Id ";
			$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
			$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T003_C004_Id ";
			$cClausula.=" left join C004 on C004_Id=T003_C004_Id ";
			$cClausula.=" where T003_Id='{$T003_Id}' ";
			$T003=mysql_query($cClausula);
			$cErro=mysql_error();
			if (!empty($cErro))
			{
			   return "$cErro/p/p$cClausula";
			   exit;
			}
			$mT003=mysql_fetch_array($T003);
			$sql = <<<EOT
				SELECT 
					D009_Id, 
					D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
				FROM 
					D009 
				where D009_Id='{$D009_Id}'
EOT;
			$result = mysql_query($sql);
			$row2 = mysql_fetch_assoc($result);
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
			break;
	}

	echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>