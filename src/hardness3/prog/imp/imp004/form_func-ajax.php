<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * VEN - VEN001 - Cadastro de Orçamentos
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
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;

		$T293 =	mysql_query("SELECT T293_D024_Id 
							   FROM T293
							  WHERE T293_Id = '{$T293_Id}'");
		$mT293 = mysql_fetch_assoc($T293);

		$sql = <<<EOT
			SELECT *
	   			from D001
	   			left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=D001_D037_Id
				left join C004 on C004_Id=D009_C004_Id
				left join D005 on D005_Id=D001_D005_Id or D005_Classificacao_Fiscal=D009_Classificacao_Fiscal(D009_Id)
				left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
				left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT293['T293_D024_Id']}'
	   			WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
		$aliquotas = $IMP003->retornaAliquotas($D009_Id, $T293_Id, false, false);
		
		//$row['C004_Percentual_COFINS']   						  = number_format($row['C004_Percentual_COFINS'],2,',','.');
		//$row['C004_Percentual_PIS']     						  = number_format($row['C004_Percentual_PIS'],2,',','.');
		$row['D001_Peso_Unitario_Kg']     						  = number_format($row['D001_Peso_Unitario_Kg'],5,',','.');
		$row['D149_Importacao_PIS']      					      = number_format($aliquotas['Aliquota_PIS'],2,',','.');
		$row['D149_Importacao_COFINS']    					 	  = number_format($aliquotas['Aliquota_COFINS'],2,',','.');
		$row['D149_Imposto_Importacao']       					  = number_format($aliquotas['Imposto_Importacao'],2,',','.');
		$row['D149_Importacao_IPI']       					      = number_format($aliquotas['Aliquota_IPI'],2,',','.');
		$row['D053_Aliquota_ICMS_Importacao']      				  = number_format($aliquotas['Aliquota_ICMS_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_Presumido_Importacao']           = number_format($aliquotas['Aliquota_ICMS_Presumido_Importacao'],2,',','.');
		$row['D053_Percentual_Reducao_ICMS_Importacao']       	  = number_format($aliquotas['Percentual_Reducao_ICMS_Importacao'],2,',','.');
		$row['D053_Percentual_Reducao_ICMS_Presumido_Importacao'] = number_format($aliquotas['Percentual_Reducao_ICMS_Presumido_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_PIS_COFINS_Importacao']          = number_format($aliquotas['Aliquota_ICMS_PIS_COFINS_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_Credito_Presumido_Importacao']   = number_format($aliquotas['Aliquota_ICMS_Credito_Presumido_Importacao'],2,',','.');
		$row['D005_Id']                   						  = $aliquotas['D005_Id'];
		$row['D005_Classificacao_Fiscal']						  = $aliquotas['D005_Classificacao_Fiscal'];
			
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
		
	case 'gerarNFe':
		require_once("bibliotecas/classes/IMP004.php");
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		
		$T294_T066_Id = mysql_query("SELECT T294_T066_Id FROM T294 WHERE T294_T293_Id='{$T293_Id}' and ifnull(T294_T066_Id,0)<=0");        
        if(mysql_num_rows($T294_T066_Id)>0){
          	$resposta['data'] = "Existem produtos sem locação, favor preencher antes de gerar a nota.";
          	$resposta['code'] = 'ERRO';
        }else{
        	$IMP004 = new IMP004();		
			$T005_Id = 0;
			$retorno = $IMP004->gerarNotaFiscal($T293_Id);
				
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Nota gerada com sucesso";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
        }
		
	break;
			
	case 'Calcular':
		require_once("bibliotecas/classes/IMP004.php");
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		
		$IMP004 = new IMP004();
		
		$T005_Id = 0;
		$IMP004->calcularDespesas($T293_Id);
		$IMP004->calculaCustoProduto($T293_Id);
		$retorno = $IMP004->Calcular($T293_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Calculo concluido";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
		
	case 'GerarXMLLI':
		require_once("bibliotecas/classes/IMP004.php");
		$IMP004 = new IMP004();

		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		
		$retorno = $IMP004->GerarXMLLI($T293_Id);
			
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
		/*echo $retorno;	
		die();
*/
	break;

	case 'validarArmazem':
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "and D146_Id={$D146_Id}";
		} else {
			$filtro = " GROUP BY T294_Id";
		}

		$sql = <<<EOT
			SELECT T294_Id
				FROM T294 
                LEFT JOIN T293 ON T293_Id=T294_T293_Id
   				WHERE T294_T293_Id={$T293_Id} AND T294_Id NOT IN
	 		(SELECT T294_Id
				FROM T294 
                LEFT JOIN T293 ON T293_Id=T294_T293_Id
   				LEFT JOIN D009 ON D009_Id=T294_D009_Id
   				LEFT JOIN T066 ON D009_Id=T066_D009_Id
  				LEFT JOIN D004 ON D004_Id=T066_D004_Id
  				LEFT JOIN D146 ON D146_Id=D004_D146_Id
   				WHERE T294_T293_Id={$T293_Id} 
   					  $filtro)
EOT;
		$T294 = mysql_query($sql);
		if(mysql_num_rows($T294)>0){
			$resposta['code'] = false;
			$resposta['data'] = "Existem produtos sem locação neste armazém, deseja continuar com a atualização?";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = 'Verdadeiro';
		}
   			
	break;

	case 'atualizarArmazemEmProdutos':
	
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "and D146_Id={$D146_Id}";
		} else {
			$filtro = " GROUP BY T294_Id";
		}
		
		$sql = <<<EOT
	 			SELECT T066_Id,T294_Id FROM T294 
			    LEFT JOIN T293 ON T293_Id=T294_T293_Id
			    LEFT JOIN D009 ON D009_Id=T294_D009_Id
			    LEFT JOIN T066 ON D009_Id=T066_D009_Id
			    LEFT JOIN D004 ON D004_Id=T066_D004_Id
			    LEFT JOIN D146 ON D146_Id=D004_D146_Id
			    WHERE T294_T293_Id={$T293_Id} 
			          $filtro
EOT;
		$T294 = mysql_query($sql);
		$erro = '';
		$atualizado = 0;

		while($mT294=mysql_fetch_array($T294)){
			$sql = <<<EOT
					UPDATE T294 
					LEFT JOIN T293 ON T293_Id=T294_T293_Id
			    LEFT JOIN D009 ON D009_Id=T294_D009_Id
			    LEFT JOIN T066 ON D009_Id=T066_D009_Id
			    LEFT JOIN D004 ON D004_Id=T066_D004_Id
			    LEFT JOIN D146 ON D146_Id=D004_D146_Id 
			    set T294_T066_Id='{$mT294['T066_Id']}'
			    where T294_Id='{$mT294['T294_Id']}'
EOT;
			$update = mysql_query($sql);
			$erro.=mysql_error();
			if(empty($erro)){
				$atualizado++;
			}
		}

		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = "Armazém dos produtos atualizado com sucesso!<br/>Quantidade de produtos atualizados:".$atualizado;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = "Erro na hora de atualizar, contacte o suporte técnico e informe o seguinte erro:".$erro;
		}
			
	break;


	case 'downloadXMLLI':		
		require_once("bibliotecas/classes/IMP004.php");
		$IMP004 = new IMP004();

		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		$retorno = $IMP004->geraDownloadXMLLI($T293_Id);		
		echo $retorno;	
		die();
	break;	


	case 'imp007Finalizar':
		require_once("bibliotecas/classes/IMP004.php");
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		$IMP004 = new IMP004();
		
		$T005_Id = 0;
		$retorno = $IMP004->imp007Finalizar($T293_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Operação realizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'imp004AtualizarCusto':
		require_once("bibliotecas/classes/IMP004.php");
		$IMP004 = new IMP004();
		
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;		
		$retorno = $IMP004->imp004AtualizarCusto($T293_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Operação realizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'carregarLocacoes':
		$D009_Id = mysql_real_escape_string($r_D009_Id);
		$sql = "SELECT D009_T066_Id FROM D009 WHERE D009_Id = '{$D009_Id}'";
		$resD009 = mysql_query($sql);
		$rowD009 = mysql_fetch_assoc($resD009);
		$extraD004 = "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY IF(T066_Id='{$rowD009['D009_T066_Id']}',1,0) DESC, T066_Id ASC";
		$resposta['data']['T294_T066_Id'] = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,T066_Id', $extraD004, false, false, "%1 (%2)"));
		$resposta['data']['T294_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local', $extraD004, false, false, "%1 (%2)")));
		$resposta['data']['T294_D009_Id'] = $rowT294['T294_D009_Id'];
	break;

	case 'excluirItensMarcados':
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;

		$T294 = mysql_query("SELECT T294_Id 
							   FROM T294 
							  WHERE T294_T293_Id = '{$T293_Id}' 
							    AND T294_Flag_Excluir = 'S'");

		while($mT294 = mysql_fetch_Array($T294)){
			mysql_query("DELETE FROM T294 WHERE T294_Id = '{$mT294['T294_Id']}' and T294_Flag_Excluir = 'S'");
		}

		if(mysql_num_rows($T294) > 0){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum item selecionado!";
		}
	break;
	
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>