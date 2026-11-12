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
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
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
				left join D001A on D001_Id=D001A_D001_Id
	   			WHERE D009_Id = '{$D009_Id}'
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
		$aliquotas = $IMP003->retornaAliquotas($D009_Id, false, false, $T075_Id);
		
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
		$row['D001A_Largura']                                     = $row['D001A_Largura'];
		$row['D001A_Altura']                                      = $row['D001A_Altura'];
		$row['D001A_Comprimento']                                 = $row['D001A_Comprimento'];

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
		require_once("bibliotecas/classes/IMP003.php");
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		
		$IMP003 = new IMP003();		
		$T005_Id = 0;
		$retorno = $IMP003->gerarNotaFiscal($T075_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Nota gerada com sucesso";
			$resposta['T007_Id'] = $T007_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$resposta['T007_Id_aux'] = $T007_Id_aux;
		}
			
	break;
			
	case 'Calcular':
		require_once("bibliotecas/classes/IMP003.php");
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		
		$IMP003 = new IMP003();
		
		$T005_Id = 0;
		$IMP003->calcularDespesas($T075_Id);
		$retorno = $IMP003->Calcular($T075_Id);
			
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
		require_once("bibliotecas/classes/IMP003.php");
		$IMP003 = new IMP003();

		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		
		$retorno = $IMP003->GerarXMLLI($T075_Id);
			
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
		/*echo $retorno;	
		die();
*/
	break;

	case 'validarArmazem':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "and D146_Id={$D146_Id}";
		} else {
			$filtro = " GROUP BY T055_Id";
		}

		$sql = <<<EOT
			SELECT T055_Id
				FROM T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id
   				WHERE T055_T075_Id={$T075_Id} AND T055_Id NOT IN
	 		(SELECT T055_Id
				FROM T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id
   				LEFT JOIN D009 ON D009_Id=T055_D009_Id
   				LEFT JOIN T066 ON D009_Id=T066_D009_Id
  				LEFT JOIN D004 ON D004_Id=T066_D004_Id
  				LEFT JOIN D146 ON D146_Id=D004_D146_Id
   				WHERE T055_T075_Id={$T075_Id} 
   					  $filtro)
EOT;
		$T055 = mysql_query($sql);
		if(mysql_num_rows($T055)>0){
			$resposta['code'] = false;
			$resposta['data'] = "Existem produtos sem locação neste armazém, deseja continuar com a atualização?";
		}else{
			log('não existem produtos sem armazem');
			$resposta['code'] = true;
			$resposta['data'] = 'Verdadeiro';
		}
   			
	break;

	case 'atualizarArmazemEmProdutos':
	
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$D146_Id = isset($_REQUEST['D146_Id']) ? $_REQUEST['D146_Id'] : false;

		if ($D146_Id>0){
			$filtro = "AND D146_Id={$D146_Id} GROUP BY T055_Id";
		} else {
			$filtro = " GROUP BY T055_Id";
		}
		
		$sql = <<<EOT
	 			SELECT T066_Id,T055_Id FROM T055 
			    LEFT JOIN T075 ON T075_Id=T055_T075_Id
			    LEFT JOIN D009 ON D009_Id=T055_D009_Id
			    LEFT JOIN T066 ON D009_Id=T066_D009_Id
			    LEFT JOIN D004 ON D004_Id=T066_D004_Id
			    LEFT JOIN D146 ON D146_Id=D004_D146_Id
			    WHERE T055_T075_Id={$T075_Id} 
			          $filtro
EOT;
		
		$T055 		= mysqli_query($sql);
		$erro 	    = '';
		$atualizado = 0;

		while($mT055=mysql_fetch_array($T055)){
			
			$sql = <<<EOT
					UPDATE T055 
			    	   SET T055_T066_Id = '{$mT055['T066_Id']}'
			   	     WHERE T055_Id = '{$mT055['T055_Id']}'
EOT;
			$update = mysql_query($sql);
			$erro  .= mysql_error();

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
		require_once("bibliotecas/classes/IMP003.php");
		$IMP003 = new IMP003();

		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$retorno = $IMP003->geraDownloadXMLLI($T075_Id);		
		echo $retorno;	
		die();
	break;	


	case 'imp007Finalizar':
		require_once("bibliotecas/classes/IMP003.php");
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$IMP003 = new IMP003();
		
		$T005_Id = 0;
		$retorno = $IMP003->imp007Finalizar($T075_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Operação realizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'imp003AtualizarCusto':
		require_once("bibliotecas/classes/IMP003.php");
		$IMP003 = new IMP003();
		
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;		
		$retorno = $IMP003->imp003AtualizarCusto($T075_Id);
			
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
		$resposta['data']['T055_T066_Id'] = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,T066_Id', $extraD004, false, false, "%1 (%2)"));
		$resposta['data']['T055_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local', $extraD004, false, false, "%1 (%2)")));
		$resposta['data']['T055_D009_Id'] = $rowT055['T055_D009_Id'];
	break;

	case 'excluirItensMarcados':
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		
		$T055 = mysql_query("SELECT T055_Id,
									T296_T295_Id 
							   FROM T055 
						  LEFT JOIN T296 ON T296_Id = T055_T296_Id
							  WHERE T055_T075_Id = '{$T075_Id}' 
							    AND T055_Flag_Excluir = 'S'");
		$arrayT295 = array();
		while($mT055 = mysql_fetch_Array($T055)){
			$arrayT295[] = $mT055['T296_T295_Id'];
			mysql_query("DELETE FROM T055 WHERE T055_Id = '{$mT055['T055_Id']}' AND T055_Flag_Excluir = 'S'");
			mysql_query("DELETE FROM T055A WHERE T055A_T055_Id = '{$mT055['T055_Id']}'");
		}
		require_once('bibliotecas/classes/IMP005.php');
		$IMP005 = new IMP005();
        $atualizaStatus = $IMP005->alteraStatusPO($arrayT295);

		if(mysql_num_rows($T055) > 0){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum item selecionado!";
		}
	break;

	case 'validaAtualizarPesoProdutos':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;

		$T055 = mysql_query("SELECT T055_Id
							   FROM T055 
	 					      WHERE T055_T075_Id ='{$T075_Id}' 
							    AND T055_Peso_Kg_Liquido_Unitario <= 0");

		if(mysql_num_rows($T055)>0){
			$resposta['code'] = false;
			$resposta['data'] = "Existem produtos sem peso, deseja continuar com a atualização?";
		}else{
			$resposta['code'] = true;
		}   			
	break;
	
	case 'atualizarPesoProdutos':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		
		require_once('bibliotecas/classes/IMP003.php');
        $IMP003  = new IMP003();
		$retorno = $IMP003->imp003AtualizarPesoProdutos($T075_Id);

		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}else{
			$resposta['code'] = true;
			$resposta['data'] = 'Peso dos produtos atualizados com sucesso';
		}   			
	break;	

	case 'validaDescricaoProdutos':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;

		$T055 = mysql_query("SELECT T055_Id
							   FROM T055 
	 					      WHERE T055_T075_Id ='{$T075_Id}' 
							    AND T055_Descricao_Produto_Ingles = ''");

		if(mysql_num_rows($T055)>0){
			$resposta['code'] = false;
			$resposta['data'] = "Existem produtos sem descrição em inglês preenchida, deseja continuar com a atualização?";
		}else{
			$resposta['code'] = true;
		}   			
	break;
	
	case 'atualizarDescricaoProdutos':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		log("entrou no ajax e pegou o T075 ".$T075_Id);
		require_once('bibliotecas/classes/IMP003.php');
        $IMP003  = new IMP003();
		$retorno = $IMP003->imp003AtualizarDescricaoProdutos($T075_Id);

		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}else{
			$resposta['code'] = true;
			$resposta['data'] = 'Descrição inglês dos produtos atualizados com sucesso';
		}   			
	break;	

	case 'atualizarCustoMEProdutos':
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		require_once('bibliotecas/classes/IMP003.php');
        $IMP003  = new IMP003();
		$retorno = $IMP003->imp003AtualizarCustoME($T075_Id);

		if($retorno !== true){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}else{
			$resposta['code'] = true;
			$resposta['data'] = 'Custo ME atualizado com sucesso';
		}   			
	break;	

	case 'campoProcuraProdutoAssociarItem':
		$D009_Id= isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;

		$sql = mysql_query("SELECT D001_Codigo_Produto, 
								   D001_Descricao_Produto, 
								   D009_Id 
						  	  FROM D009 
					     LEFT JOIN D049 ON D049_Id=D009_D049_Id 
					     LEFT JOIN D001 ON D001_Id=D049_D001_Id
						     WHERE D009_Id = '{$D009_Id}'");
		$erro = mysql_error();
		$row = mysql_fetch_array($sql);

		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro: $erro";
		}
	break;

	case 'consultarDuimpPreparar':
		require_once("bibliotecas/classes/IMP006.php");
		$T075_Id = isset($_REQUEST['T075_Id']) ? $_REQUEST['T075_Id'] : false;
		$IMP006 = new IMP006();
		$retorno = $IMP006->prepararCabecalhoConsulta($T075_Id);
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Cabeçalho da DUIMP consultado com sucesso.';
			$resposta['processoId'] = $retorno['processoId'];
			$resposta['numero'] = $retorno['numero'];
			$resposta['versao'] = $retorno['versao'];
			$resposta['totalItensLista'] = $retorno['totalItensLista'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpCarregarItens':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$IMP006 = new IMP006();
		$retorno = $IMP006->carregarItensConsulta($processoId);
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Itens completos da DUIMP carregados com sucesso.';
			$resposta['totalItens'] = $retorno['totalItens'];
			$resposta['limiteLote'] = $retorno['limiteLote'];
			$resposta['totalLotes'] = $retorno['totalLotes'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpCarregarCatalogo':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$IMP006 = new IMP006();
		$retorno = $IMP006->carregarCatalogoConsulta($processoId);
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Catálogo de produtos consultado com sucesso.';
			$resposta['processoId'] = $retorno['processoId'];
			$resposta['totalItens'] = $retorno['totalItens'];
			$resposta['totalProdutos'] = $retorno['totalProdutos'];
			$resposta['limiteLote'] = $retorno['limiteLote'];
			$resposta['totalLotes'] = $retorno['totalLotes'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpGravarCabecalho':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$IMP006 = new IMP006();
		$retorno = $IMP006->gravarCabecalho($processoId);
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = 'Cabeçalho da DUIMP atualizado com sucesso.';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpProcessarItens':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$limite = isset($_REQUEST['limite']) ? $_REQUEST['limite'] : 5;
		$IMP006 = new IMP006();
		$retorno = $IMP006->processarItensLote($processoId, $offset, $limite);
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Lote de itens processado com sucesso.';
			$resposta['totalItens'] = $retorno['totalItens'];
			$resposta['processados'] = $retorno['processados'];
			$resposta['restantes'] = $retorno['restantes'];
			$resposta['finalizado'] = $retorno['finalizado'];
			$resposta['totalProdutos'] = $retorno['totalProdutos'];
			$resposta['ultimoNumeroItem'] = $retorno['ultimoNumeroItem'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpGravarItens':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$limite = isset($_REQUEST['limite']) ? $_REQUEST['limite'] : 10;
		$IMP006 = new IMP006();
		$retorno = $IMP006->gravarItensLote($processoId, $offset, $limite);
		if (is_array($retorno)) {
			$resposta['code'] = true;
			$resposta['data'] = 'Lote de itens gravado com sucesso.';
			$resposta['totalItens'] = $retorno['totalItens'];
			$resposta['processados'] = $retorno['processados'];
			$resposta['restantes'] = $retorno['restantes'];
			$resposta['finalizado'] = $retorno['finalizado'];
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpFinalizar':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$IMP006 = new IMP006();
		$retorno = $IMP006->finalizarImportacao($processoId);
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = 'Importação DUIMP concluída com sucesso.';
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'consultarDuimpLimpar':
		require_once("bibliotecas/classes/IMP006.php");
		$processoId = isset($_REQUEST['processoId']) ? $_REQUEST['processoId'] : '';
		$IMP006 = new IMP006();
		$IMP006->limparImportacao($processoId);
		$resposta['code'] = true;
		$resposta['data'] = 'Snapshot temporário removido.';
	break;

	case 'imp003verificaPacoteDespesas':
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;

		$T075 = mysql_query("SELECT T168_Id
						  	   FROM T168 
						      WHERE T168_T075_Id = '{$T075_Id}'");

		if(mysql_num_rows($T075) > 0){
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
