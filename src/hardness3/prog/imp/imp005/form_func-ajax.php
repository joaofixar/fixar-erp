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
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;

		$T295 =	mysql_query("SELECT T295_D024_Id 
							   FROM T295
							  WHERE T295_Id = '{$T295_Id}'");
		$mT295 = mysql_fetch_assoc($T295);

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
				left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT295['T295_D024_Id']}'
	   			WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
		$aliquotas = $IMP003->retornaAliquotas($D009_Id, false, $T295_Id, false);

		$row['C004_Percentual_COFINS']                            = number_format($row['C004_Percentual_COFINS'],2,',','.');
		$row['C004_Percentual_PIS']                               = number_format($row['C004_Percentual_PIS'],2,',','.');
		$row['D001_Peso_Unitario_Kg']                             = number_format($row['D001_Peso_Unitario_Kg'],5,',','.');
		$row['D149_Importacao_PIS']                               = number_format($aliquotas['Aliquota_PIS'],2,',','.');
		$row['D149_Importacao_COFINS']                            = number_format($aliquotas['Aliquota_COFINS'],2,',','.');
		$row['D149_Imposto_Importacao']                           = number_format($aliquotas['Imposto_Importacao'],2,',','.');
		$row['D149_Importacao_IPI']                               = number_format($aliquotas['Aliquota_IPI'],2,',','.');
		$row['D053_Aliquota_ICMS_Importacao']      				  = number_format($aliquotas['Aliquota_ICMS_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_Presumido_Importacao']           = number_format($aliquotas['Aliquota_ICMS_Presumido_Importacao'],2,',','.');
		$row['D053_Percentual_Reducao_ICMS_Importacao']       	  = number_format($aliquotas['Percentual_Reducao_ICMS_Importacao'],2,',','.');
		$row['D053_Percentual_Reducao_ICMS_Presumido_Importacao'] = number_format($aliquotas['Percentual_Reducao_ICMS_Presumido_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_PIS_COFINS_Importacao']          = number_format($aliquotas['Aliquota_ICMS_PIS_COFINS_Importacao'],2,',','.');
		$row['D053_Aliquota_ICMS_Credito_Presumido_Importacao']   = number_format($aliquotas['Aliquota_ICMS_Credito_Presumido_Importacao'],2,',','.');
		$row['D005_Id']                                           = $aliquotas['D005_Id'];
		$row['D005_Classificacao_Fiscal']                         = $aliquotas['D005_Classificacao_Fiscal'];
			
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
		require_once("bibliotecas/classes/IMP005.php");
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		
		$T296_T066_Id = mysql_query("SELECT T296_T066_Id FROM T296 WHERE T296_T295_Id='{$T295_Id}' and ifnull(T296_T066_Id,0)<=0");        
        if(mysql_num_rows($T296_T066_Id)>0){
          	$resposta['data'] = "Existem produtos sem locação, favor preencher antes de gerar a nota.";
          	$resposta['code'] = 'ERRO';
        }else{
        	$IMP005 = new IMP005();		
			$T005_Id = 0;
			$retorno = $IMP005->gerarNotaFiscal($T295_Id);
				
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
		require_once("bibliotecas/classes/IMP005.php");
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		
		$IMP005 = new IMP005();
		
		$IMP005->calcularDespesas($T295_Id);
		$IMP005->calculaCustoProduto($T295_Id);	
		$retorno = $IMP005->Calcular($T295_Id);
			
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
		require_once("bibliotecas/classes/IMP005.php");
		$IMP005 = new IMP005();

		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		
		$retorno = $IMP005->GerarXMLLI($T295_Id);
			
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
		/*echo $retorno;	
		die();
*/
	break;

	case 'validarArmazem':
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "and D146_Id={$D146_Id}";
		} else {
			$filtro = " GROUP BY T296_Id";
		}

		$sql = <<<EOT
			SELECT T296_Id
				FROM T296 
                LEFT JOIN T295 ON T295_Id=T296_T295_Id
   				WHERE T296_T295_Id={$T295_Id} AND T296_Id NOT IN
	 		(SELECT T296_Id
				FROM T296 
                LEFT JOIN T295 ON T295_Id=T296_T295_Id
   				LEFT JOIN D009 ON D009_Id=T296_D009_Id
   				LEFT JOIN T066 ON D009_Id=T066_D009_Id
  				LEFT JOIN D004 ON D004_Id=T066_D004_Id
  				LEFT JOIN D146 ON D146_Id=D004_D146_Id
   				WHERE T296_T295_Id={$T295_Id} 
   					  $filtro)
EOT;
		$T296 = mysql_query($sql);
		if(mysql_num_rows($T296)>0){
			$resposta['code'] = false;
			$resposta['data'] = "Existem produtos sem locação neste armazém, deseja continuar com a atualização?";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = 'Verdadeiro';
		}
   			
	break;

	case 'atualizarArmazemEmProdutos':
	
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "and D146_Id={$D146_Id}";
		} else {
			$filtro = " GROUP BY T296_Id";
		}
		
		$sql = <<<EOT
	 			SELECT T066_Id,T296_Id FROM T296 
			    LEFT JOIN T295 ON T295_Id=T296_T295_Id
			    LEFT JOIN D009 ON D009_Id=T296_D009_Id
			    LEFT JOIN T066 ON D009_Id=T066_D009_Id
			    LEFT JOIN D004 ON D004_Id=T066_D004_Id
			    LEFT JOIN D146 ON D146_Id=D004_D146_Id
			    WHERE T296_T295_Id={$T295_Id} 
			          $filtro
EOT;
		$T296 = mysql_query($sql);
		$erro = '';
		$atualizado = 0;

		while($mT296=mysql_fetch_array($T296)){
			$sql = <<<EOT
					UPDATE T296 
					LEFT JOIN T295 ON T295_Id=T296_T295_Id
			    LEFT JOIN D009 ON D009_Id=T296_D009_Id
			    LEFT JOIN T066 ON D009_Id=T066_D009_Id
			    LEFT JOIN D004 ON D004_Id=T066_D004_Id
			    LEFT JOIN D146 ON D146_Id=D004_D146_Id 
			    set T296_T066_Id='{$mT296['T066_Id']}'
			    where T296_Id='{$mT296['T296_Id']}'
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
		require_once("bibliotecas/classes/IMP005.php");
		$IMP005 = new IMP005();

		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		$retorno = $IMP005->geraDownloadXMLLI($T295_Id);		
		echo $retorno;	
		die();
	break;	


	case 'imp007Finalizar':
		require_once("bibliotecas/classes/IMP005.php");
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;
		$IMP005 = new IMP005();
		
		$T005_Id = 0;
		$retorno = $IMP005->imp007Finalizar($T295_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Operação realizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'imp005AtualizarCusto':
		require_once("bibliotecas/classes/IMP005.php");
		$IMP005 = new IMP005();
		
		$T295_Id = isset($_REQUEST['T295_Id']) ? $_REQUEST['T295_Id'] : false;		
		$retorno = $IMP005->imp005AtualizarCusto($T295_Id);
			
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
		$resposta['data']['T296_T066_Id'] = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,T066_Id', $extraD004, false, false, "%1 (%2)"));
		$resposta['data']['T296_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local', $extraD004, false, false, "%1 (%2)")));
		$resposta['data']['T296_D009_Id'] = $rowT296['T296_D009_Id'];
	break;

	case 'imp005enviarParaAnalise':
		require_once("bibliotecas/classes/IMP005.php");
		$IMP005 = new IMP005();		

		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;		
		$retorno = $IMP005->imp005enviarParaAnalise($T295_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "PO enviada para análise";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'imp005verificaAlcada':
		global $g;
		require_once("bibliotecas/classes/IMP005.php");
		$IMP005 = new IMP005();	
		
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;	
		$retorno = $IMP005->imp005VerificaAlcada($T295_Id);

		$T295 = mysql_query("SELECT T295_Flag_Status_Liberacao, 
									T295_Flag_Status,
									T295_Flag_Alcada							
							   FROM T295 
							  WHERE T295_Id='{$T295_Id}'");

		$mT295=mysql_fetch_array($T295);

		if($mT295['T295_Flag_Status_Liberacao'] == 4){

			if($g['C007']['C007_Flag_Vendedor'] == $mT295['T295_Flag_Alcada'] || $g['C007']['C007_Flag_Vendedor'] == 'D' || ($g['C007']['C007_Flag_Vendedor'] == 'S' && ($mT295['T295_Flag_Alcada'] == 'C' || $mT295['T295_Flag_Alcada'] == 'S'))){
				$resposta['code'] = 'analise';
				break;
			} else {
				$msg = "A liberação desta PO exige maior alçada, pedir liberação?";
			}
		}
		
		if ($mT295['T295_Flag_Status_Liberacao'] != 4) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $msg;
		}
	break;

	case 'excluirItensMarcados':
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;

		$T296 = mysql_query("SELECT T296_Id,
									T294_T293_Id
							   FROM T296 
						  LEFT JOIN T294 ON T294_Id = T296_T294_Id
							  WHERE T296_T295_Id = '{$T295_Id}' 
							    AND T296_Flag_Excluir = 'S'");

		$arrayT293 = array();
		while($mT296 = mysql_fetch_Array($T296)){
			$arrayT293[] = $mT296['T294_T293_Id'];
			mysql_query("DELETE FROM T296 WHERE T296_Id = '{$mT296['T296_Id']}' and T296_Flag_Excluir = 'S'");
		}
		
		require_once('bibliotecas/classes/IMP004.php');
		$IMP004 = new IMP004();
        $atualizaStatus = $IMP004->alteraStatusQuotation($arrayT293);

		if(mysql_num_rows($T296) > 0){
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