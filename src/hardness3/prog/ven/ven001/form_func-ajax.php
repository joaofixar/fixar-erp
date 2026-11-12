<?php
namespace hardness;
	/**
	* VEN - VEN001 - Cadastro de Orçamentos
	*
	* Processamento de ações AJAX
	*/
	$resposta = array('code' => true, 'data' => array());
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	/**
	* Ações
	*/
	switch($g['r'][3]) {
		case 'atualizarCamposProdutos':
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			/*
			T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
			T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
			*/
			
			$sql =  "SELECT * from T003 WHERE T003_Id = '{$T003_Id}'";
			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
			$T003_D024_Id = $row['T003_D024_Id'];
			$T003_D006_Id = $row['T003_D006_Id'];
			$T003_Prazo_Entrega = $row['T003_Prazo_Entrega'];
			$T003_Flag_Destino_Produto = $row['T003_Flag_Destino_Produto'];

			$sqlD024 = mysql_query("select D024_CNPJ_Matriz from D024 where D024_Id='{$row['T003_D024_Id']}'");
			$mD024 = mysql_fetch_array($sqlD024);

			      
			$sql = <<<EOT
			SELECT  *
			      from D001
			    left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T003_D024_Id']}'
			    left join D082 on D082_Id=D049_D082_Id
			    left join D041 on D041_D049_Id=D049_Id
			    left join D005 on D005_Id=D001_D005_Id
			    left join D069 on D069_Id=D009_D069_Id
			    left join D070 on D070_Id=D069_D070_Id
			    left join D002 on D002_Id=D001_D002_Id
			    left join D002A on D002_Id=D002A_D002_Id
			    WHERE D009_Id = '{$D009_Id}'
EOT;
			
			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);

			if (empty($row['D083_Codigo_Produto_Fornecedor']) and !empty($mD024['D024_CNPJ_Matriz'])){

				$sqlD083 = mysql_query("select D083_Codigo_Produto_Fornecedor
										  from D083 
				                         where D083_D001_Id='{$row['D001_Id']}'
					                       and D083_D024_Id IN (select D024_Id FROM D024 WHERE D024_CNPJ_Matriz='{$mD024['D024_CNPJ_Matriz']}')
					                       and D083_Codigo_Produto_Fornecedor!=''
				                         limit 1");
				$mD083 = mysql_fetch_assoc($sqlD083);

				$row['D083_Codigo_Produto_Fornecedor'] = $mD083['D083_Codigo_Produto_Fornecedor'];
			
			}


			$row['estoqueLiquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
			$row['estoqueLiquido2'] = $row['D009_Quantidade_Estoque_Liquido'];
			$row['estoqueLoja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
			$row['estoqueTotal'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']+$row['D009_Quantidade_Estoque_Loja']);
			$row['ordemCompra'] = gCorrigeNumero($row['D009_Quantidade_OC']);
			$row['estoqueFora'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Fora']);

			$row['validadePrecoTabela'] = 'true';
			if(strtotime($row['D070_Data_Vencimento']) < strtotime(date("Y-m-d"))){
				$row['validadePrecoTabela'] = 'false';
			}
			/*
			$D005_Id=mysql_query("SELECT D009_Classificacao_Fiscal(D009_Id) AS Classificacao_Fiscal FROM D009 WHERE D009_Id='{$D009_Id}'");
			$mD005=mysql_fetch_array($D005_Id);

			$var=mysql_query("SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='{$mD005['Classificacao_Fiscal']}'");
			$mVar = mysql_fetch_array($var);
			$row['D001_D005_Id'] = $mVar['D005_Id'];
			*/

			// Aliquota IPI
			//$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
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
			              D024_Flag_Contribuinte,
						  T003_Ordem_Compra,
						  T003_Flag_Destino_Produto";
			$cClausula.=" from T003 ";
			$cClausula.=" left join D024 on D024_Id=T003_D024_Id ";
			$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
			$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T003_C004_Id ";
			$cClausula.=" left join C004 on C004_Id=T003_C004_Id ";
			$cClausula.=" where T003_Id='{$T003_Id}'";
			$T003=mysql_query($cClausula);
			$cErro=mysql_error();
			if (!empty($cErro))
			{
				return "$cErro/p/p$cClausula";
				exit;
			}
			$mT003=mysql_fetch_array($T003);

			$row['T003_Flag_Destino_Produto'] = $mT003['T003_Flag_Destino_Produto'];
			/*$sql = <<<EOT
			SELECT 
			  D009_Id, 
			  D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
			FROM 
			  D009 
			where D009_Id='{$D009_Id}'
EOT; 
      		$result = mysql_query($sql);
      		$row2 = mysql_fetch_assoc($result);
     		*/
			/**
			* DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
			* 
			* $row['D041_Valor_Base'] = number_format($row['D041_Valor_Base'],2,',','.');
			*/
			$cfop      = $VEN001->retornaCFOP($mT003['T003_D024_Id'], $row['D009_Id'], $mT003['T003_C004_Id'], $T003_Flag_Destino_Produto, false, false, false, $mT003['T003_C004_Id_Estoque']);
			$aliquotas = $VEN001->retornaAliquotas($D009_Id, $T003_Id, false, false, false, false, false, $cfop);
			// verifica se está setado a flag lista preço do cabeçalho do orçamento, se estiver, manda ela, senão manda o id do cliente

			//Buscando as aliquotas digitadas na tela de montagem do ipv no orçamento
			$T250 = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasIPV 
								   FROM T250 
								  WHERE T250_T003_Id = '{$T003_Id}' 
								    AND T250_Flag_Altera_Preco='S' 
									AND T250_Grupo='IPV'");
			$mT250 = mysql_fetch_array($T250);
			$TotalPorcentagemIPV = $mT250['AliquotasIPV'];

			//Buscando as aliquotas digitadas na tela de montagem do icf no orçamento
			$T250icf = mysql_query("SELECT SUM(T250_Aliquota-T250_Aliquota_Original) AS AliquotasICF 
									  FROM T250 
									 WHERE T250_T003_Id = '{$T003_Id}' 
									   AND T250_Flag_Altera_Preco='S' 
									   AND T250_Grupo='ICF'");
			$mT250icf = mysql_fetch_array($T250icf);
			$TotalPorcentagemICF = $mT250icf['AliquotasICF'];

			$preco = $CAD002->retornaPrecoVenda($row['D009_Id'], $mT003['T003_Flag_Lista_Preco'], $mT003['T003_D024_Id'], $mT003['T003_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], 0, 0, $mT003['T003_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL'], 0, $TotalPorcentagemIPV, $TotalPorcentagemICF);
			$precoTabela = $CAD002->cad002RetornaPrecoTabela($row['D009_Id'], $mT003['T003_Flag_Lista_Preco'], $mT003['T003_D024_Id'], $mT003['T003_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $TotalPorcentagemIPV, $TotalPorcentagemICF, $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
			$row['precoTabela'] = $precoTabela[0];
			$row['Preco_Produto'] = $preco[0];
			$precoCalc = gCorrigeNumeroInverte($preco[0]);
			$percentualPrecoMinimo = $g['C031']['percentualPrecoMinimoVenda']/100;
			$row['PrecoMinimo'] = gCorrigeNumero($precoCalc - ($precoCalc*$percentualPrecoMinimo), 4);

			global $g;
			// Método que irá retonar o último preço que o produto foi vendido para o cliente
			$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT003['T003_D024_Id'], $row['D009_Id'], $T003_Id);

			$row['Aliquota_IPI'] = $aliquotas['Aliquota_ICMS'];

			$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');

			$row['D006_Id'] = $cfop;

			$row['T004_Numero_Pedido_Compra'] = (isset($mT003['T003_Ordem_Compra']) && $mT003['T003_Ordem_Compra'] != '') ? $mT003['T003_Ordem_Compra'] : '';
			if($row['D006_Id'] <= 0 || $row['D006_Id'] === false){
				$row['D006_Id'] = $T003_D006_Id;
			}
			$row['T003_Prazo_Entrega'] = $T003_Prazo_Entrega;

			require_once('bibliotecas/funcoesCallback/infoCustoVendaProduto.php');
			$row['infoCustoVenda'] = infoCustoVendaProduto(false, array($row['D009_Data_Alteracao_Custo'], $row['D009_Data_Ultima_Venda'], $row['D009_Flag_Custo_Utilizado']), 'S');

			if($g['C031']['utilizarDescProdutoUltimaVenda'] == 'S'){
				$sqlT003 = mysql_query("SELECT T004_Descricao_Produto FROM T004 LEFT JOIN T003 ON T003_Id = T004_T003_Id WHERE T003_D024_Id ='{$T003_D024_Id}' AND T004_D009_Id = '{$row['D009_Id']}' ORDER BY T004_Id DESC LIMIT 1");
				if(mysql_num_rows($sqlT003) > 0){
					$resT003 = mysql_fetch_array($sqlT003);
					if($row['D001_Descricao_Produto'] != $resT003['T004_Descricao_Produto']){
						$row['D001_Descricao_Produto'] = $resT003['T004_Descricao_Produto'];
					}
				}
			}

			$row['utilizaQuantidadeConversao'] = $g['C031']['utilizaQuantidadeConversao'] === 'S';

			$row['campoFocus'] = $VEN001->campoFocusFormCampoProcuraProduto();

			if (!empty($row)) {
				$resposta['code'] = true;

				$params = array(
					"D009_Id" => $D009_Id,
					"T003_Id" => $T003_Id
				);
				$row = $VEN001->ven001ModPreencheFormProdutosOrcamento($row, $params);
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
		break;

		case 'carregarLocacoes':
			$D009_Id = mysql_real_escape_string($r_D009_Id);

			$D001 = mysql_query("SELECT D001A_Id 
								   FROM D001A
							  LEFT JOIN D001 ON D001_Id = D001A_D001_Id
							  LEFT JOIN D049 ON D001_Id = D049_D001_Id
							  LEFT JOIN D009 ON D049_Id = D009_D049_Id
								  WHERE D009_Id = '{$D009_Id}'
								    AND D001A_Flag_Validade ='S'");
									
			if(mysql_num_rows($D001) > 0){
				// Se tiver controle de validade vem selecionado a locacao com a data de validade mais antiga
				$extraD004 = "LEFT JOIN D004  ON T066_D004_Id  = D004_Id 
							  LEFT JOIN T066A ON T066A_T066_Id = T066_Id 
								  WHERE T066_D009_Id = '{$D009_Id}'
									AND T066A_Data_Validade != '0000-00-00'
							   ORDER BY IF(T066A_Data_Validade = '0000-00-00','9999-99-99', T066A_Data_Validade) ASC, T066_Quantidade_Estoque_Liquido DESC, T066_Id ASC";

				$resposta['data']['T004A_T066_Id']            = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,format(T066_Quantidade_Estoque_Liquido,0),if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, true, false, "%1 (QTD:%2) %3"));
				$resposta['data']['T004A_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local,T066_Id,T066A_Data_Validade', $extraD004, true, false, "%1 %3")));
			}  else {
				// Se não tiver controle de validade busca as locações do item mas não vem selecionado no formulario
				$extraD004 = "LEFT JOIN D004  ON T066_D004_Id  = D004_Id 
							  LEFT JOIN T066A ON T066A_T066_Id = T066_Id 
								  WHERE T066_D009_Id = '{$D009_Id}' 
							   ORDER BY T066_Quantidade_Estoque DESC, T066_Id ASC";
							   
				$resposta['data']['T004A_T066_Id']            = gProcessaSelect(gGeraSelect('T066','T066_Id','D004_Local,format(T066_Quantidade_Estoque_Liquido,0),if(T066A_Data_Validade = "0000-00-00","", concat("- VAL: ",DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y")))', $extraD004, true, false, "%1 (QTD:%2)"));
				$resposta['data']['T004A_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066','T066_Id','D004_Local,T066_Id,T066A_Data_Validade', $extraD004, true, false, "%1 %3")));
			}	 	
		break;
			
		case 'atualizarCamposServicos':
			global $g;
			$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
			$D112_Id = mysql_real_escape_string($D112_Id);

			$sql = "SELECT * FROM T212 LEFT JOIN D112 ON D112_T212_Id = T212_Id WHERE D112_Id = '{$D112_Id}'";

			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
			
			$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
			$teste = strtotime($row['D112_Tempo_Estimado']);
			$teste = date("H:i", $teste);
			$row['D112_Tempo_Estimado'] = $teste;

			$row['selectTipoServico'] = gProcessaSelect(gGeraSelect('T212', 'T212_Id', 'T212_Nome', "", true, $row['D112_T212_Id']));
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
		break;
		
		case 'gerarPedido':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			
			$VEN001 = new VEN001();
			
			$T005_Id = 0;
			$retorno = $VEN001->gerarPedido($T003_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Pedido Gerado com sucesso!";
				$resposta['T005_Id'] = $T005_Id;

				$VEN001->processoPedido($T005_Id);

				$sqlT005 = mysql_query("SELECT T005_Flag_Status FROM T005 WHERE T005_Id = '{$T005_Id}'");
				$T005 = mysql_fetch_array($sqlT005);
				if($T005['T005_Flag_Status'] == 6){
					$resposta['notificacao'] = "ATENÇÂO!!!<br />O cliente possui restrições! Pedido foi enviado para análise de crédito.";
				}
			} elseif (is_array($retorno)) {
				$resposta['code'] = true;
				$resposta['alerta'] = $retorno;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'aplicarIPV':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$IPV = isset($r_IPV) ? $r_IPV : false;
			
			$IPV = str_replace('.', '', $r_IPV);
			$IPV = str_replace(',', '.', $IPV);
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->aplicarIpvMinimo($T003_Id, $IPV);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "IPV aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;

		case 'ven001aplicarDesconto':
			global $g;
			$tabelaCabecalho = isset($r_tabelaCabecalho) ? $r_tabelaCabecalho : false;
			$idTabelaCabecalho = isset($r_idTabelaCabecalho) ? $r_idTabelaCabecalho : false;
			$tabelaProduto = isset($r_tabelaProduto) ? $r_tabelaProduto : false;
			$desconto = isset($r_desconto) ? $r_desconto : false;
			$campoAplicardesconto = isset($r_campoAplicardesconto) ? $r_campoAplicardesconto : false;
			$erro = '';

			// Validação desconto PDV
			if ($r_PDV){
/*				$T007 = mysql_query("select T007_Valor_Total_Produtos_Sem_Desconto, 
											T007_Valor_Total_Produtos,
											T007_Valor_Desconto,
											T007_Valor_Total
									   from T007
				   					  where T007_Id='$idTabelaCabecalho'");
				$mT007 = mysql_fetch_array($T007);
				
				$totalDesconto = ($mT007['T007_Valor_Total_Produtos_Sem_Desconto']-$mT007['T007_Valor_Total_Produtos']);
				$percentualDesconto  = ($totalDesconto/$mT007['T007_Valor_Total_Produtos_Sem_Desconto']);
				$percentualDesconto += ($mT007['T007_Valor_Desconto']/$mT007['T007_Valor_Total']);
				$percentualDesconto = $percentualDesconto * 100;*/

				$T007 = mysql_query("select T007_Valor_Desconto,
											T007_Valor_Total
									   from T007
				   					  where T007_Id='$idTabelaCabecalho'");
				$mT007 = mysql_fetch_array($T007);
				
				$percentualDesconto  = gCorrigeNumeroInverte($desconto);
				$percentualDesconto += ( ($mT007['T007_Valor_Desconto']/$mT007['T007_Valor_Total']) * 100 );

				if (number_format($percentualDesconto,2) > number_format($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'],2)) {
					require_once('prog/sistema/funcoes/autoriza.php');
					$autorizado = validaAutorizacao("T007", $idTabelaCabecalho, $percentualDesconto);
					if (!$autorizado) {
						$desconto = gCorrigeNumero($percentualDesconto);
						$percentualPermitido = gCorrigeNumero($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista']);
						$erro = "O desconto {$desconto}% maior que {$percentualPermitido}% liberado.";
					}
				}
						
			}

			if (empty($erro)) {
				$VEN001 = new VEN001();

				if ($g['C031']['aplicarDescontoEmEtapas'] == 'S' && $tabelaCabecalho == "T003") {
					$retorno = $VEN001->validaAplicarDescontoEmEtapas(array(
						'tabelaCabecalho' => $tabelaCabecalho,
						'idTabelaCabecalho' => $idTabelaCabecalho,
						'tabelaProduto' => $tabelaProduto,
						'desconto' => $desconto,
						'campoAplicarDesconto' => $campoAplicardesconto
					));

					if ($retorno === true) {
						$descontoSql = mysql_real_escape_string($desconto);
						$campoAplicarDescontoSql = mysql_real_escape_string($campoAplicardesconto);
						$campoOrdem = ($tabelaProduto == 'T004') ? "{$tabelaProduto}_Item" : "{$tabelaProduto}_Id";
						$sqlEtapas = gComprimeSql("
							SELECT
								{$tabelaProduto}_Id,
								{$tabelaProduto}_{$tabelaCabecalho}_Id AS {$tabelaCabecalho}_Id,
								'{$descontoSql}' AS Desconto_Aplicar,
								'{$campoAplicarDescontoSql}' AS Campo_Aplicar_Desconto,
								IF(
									{$tabelaProduto}_Id = (
										SELECT Ult.{$tabelaProduto}_Id
										  FROM {$tabelaProduto} AS Ult
										 WHERE Ult.{$tabelaProduto}_{$tabelaCabecalho}_Id = '{$idTabelaCabecalho}'
										 ORDER BY Ult.{$campoOrdem} DESC, Ult.{$tabelaProduto}_Id DESC
										 LIMIT 1
									),
									1,
									0
								) AS Ultimo_Item
							FROM {$tabelaProduto}
							WHERE {$tabelaProduto}_{$tabelaCabecalho}_Id = '{$idTabelaCabecalho}'
							ORDER BY {$campoOrdem} ASC, {$tabelaProduto}_Id ASC
						");

						$resposta['code'] = true;
						$resposta['data'] = 'jsonEtapas';
						$resposta['json'] = json_encode(array(
							'titulo' => 'Aplicar Desconto',
							'classe' => 'VEN001',
							'metodo' => 'VEN001aplicarDescontoEmEtapas()',
							'sql' => $sqlEtapas,
							'idBotao' => false,
							'divId' => $g['divIdParent'],
							'autoExec' => true,
						));
						break;
					}
				} else {
					$retorno = $VEN001->VEN001aplicarDesconto($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $desconto, $campoAplicardesconto);
				}

			} else {
				$retorno = $erro;
				$resposta['autorizacao'] = true;
			}
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Desconto aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;

		case 'ven001aplicarPercentualDescontoValorTotal':
			global $g;
			$tabelaCabecalho   	  = isset($r_tabelaCabecalho) ? $r_tabelaCabecalho : false;
			$idTabelaCabecalho    = isset($r_idTabelaCabecalho) ? $r_idTabelaCabecalho : false;
			$percentualDesconto   = isset($r_percentualDesconto) ? $r_percentualDesconto : false;
			$percentualDesconto   = gCorrigeNumeroInverte($percentualDesconto);
			$erro = '';
			$desconto = mysql_query("SELECT {$tabelaCabecalho}_Valor_Total_Produtos as totalProdutos FROM $tabelaCabecalho WHERE {$tabelaCabecalho}_Id = {$idTabelaCabecalho}");

			$mDesconto = mysql_fetch_array($desconto);

			if($tabelaCabecalho == 'T003' || $tabelaCabecalho == 'T005'){
				$campo = $tabelaCabecalho.'_Valor_Desconto_Manual';
			} else {
				$campo = $tabelaCabecalho.'_Valor_Desconto';
			}

			$totalDesconto = $mDesconto['totalProdutos'] * ($percentualDesconto / 100);
			mysql_query("UPDATE $tabelaCabecalho SET $campo = $totalDesconto WHERE {$tabelaCabecalho}_Id = {$idTabelaCabecalho}");
			$error = mysql_error();
			if(!empty($error)){
				$erro = $error;
			}
			
			if (empty($erro)) {
				$resposta['code'] = true;
				$resposta['data'] = "Desconto aplicado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			}
		break;
		
		case 'ImportarOrcamento':
			require_once("bibliotecas/classes/VEN001.php");
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$T003_Id_Origem = isset($r_T003_Id_Origem) ? $r_T003_Id_Origem : false;
			
			$VEN001 = new VEN001();
			
			$retorno = $VEN001->importarOrcamento($T003_Id, $T003_Id_Origem);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Orçamento importado com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'atualizarAliquota':
			global $g;
			$resposta['code'] = true;
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;


	        if (empty($T003_Id)) {
	            $resposta['code']=false;
	            $resposta['data']= "Não foi passado o Id do Orçamento, contate o suporte técnico";
	        }

	        $cmysql_T003 = mysql_query("SELECT T006_Id FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id='{$T003_Id}' AND T006_Id>0");
	        if (mysql_num_rows($cmysql_T003) > 0) {
	            $resposta['code']=false;
	            $resposta['data']= "Este orçamento já foi faturado, não é permitido mudar o CFOP";
	        }

			/*inicio calculo etapas*/
	        if ($r_inicio==1){
	        	log("entrou na 1 etapa");
		    	$sql = base64_decode($r_extra);
		        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "", $sql);
		        $sqlCache = mysql_query("SELECT
											T004_Id,
											D009_Id,
											D001_Codigo_Produto,
											D001_Peso_Unitario_Kg,
											D001_Cubagem_Unitaria,
											T003_Flag_Lista_Preco,
											T003_D024_Id,
											T003_Flag_Acp,
											T004_D006_Id,
											D009_Preco_Pauta_ST,
											ifnull(T004_T219_Id,0) as T004_T219_Id,
											T004_Flag_Origem_Produto
											FROM T003
											LEFT JOIN T004 ON T003_Id=T004_T003_Id
											LEFT JOIN T006 ON T004_Id=T006_T004_Id
											LEFT JOIN D009 ON D009_Id=T004_D009_Id
											LEFT JOIN D049 ON D049_Id=D009_D049_Id
											LEFT JOIN D001 ON D001_Id=D049_D001_Id
												WHERE T003_Id='{$T003_Id}'
												AND T006_Id IS NULL {$sql}");
		        $erro = mysql_error();
		        if (!empty($erro)) {
		            $resposta['code']=false;
	            	$resposta['data']= "Erro consultando itens do orcamento: $erro";
		        }
	        	$count=0;

	        	while($rowCache = mysql_fetch_array($sqlCache)){
				    $cache[$count]['D009_Id'] = $rowCache['D009_Id'];	
				    $cache[$count]['T004_Id'] = $rowCache['T004_Id'];	
				    $cache[$count]['T003_Flag_Lista_Preco'] = $rowCache['T003_Flag_Lista_Preco'];	
				    $cache[$count]['T003_D024_Id'] = $rowCache['T003_D024_Id'];		
				    $cache[$count]['T004_D006_Id'] = $rowCache['T004_D006_Id'];	
				    $cache[$count]['T004_D006_Id'] = $rowCache['T004_D006_Id'];	 
				    $cache[$count]['T004_T219_Id'] = $rowCache['T004_T219_Id']; 
				    $cache[$count]['D001_Peso_Unitario_Kg'] = $rowCache['D001_Peso_Unitario_Kg']; 
					$cache[$count]['D001_Cubagem_Unitaria'] = $rowCache['D001_Cubagem_Unitaria']; 
					$cache[$count]['D009_Preco_Pauta_ST'] = $rowCache['D009_Preco_Pauta_ST'];
					$cache[$count]['T004_Flag_Origem_Produto'] = $rowCache['T004_Flag_Origem_Produto'];  
					$count++;
					log('Preco pauta $cache ' . $cache[$count]['T004_Preco_Pauta_ST'] . ' = ' . $rowCache['D009_Preco_Pauta_ST']);
				}
				$resposta['totalRegistros'] = count($cache);
				$resposta['etapas'] = round($resposta['totalRegistros'] / 2);
		    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
		    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
				$resposta['etapa'] = 1;
		        $resposta['ok'] = 0;
	    	    $resposta['erro'] = 0;
	        	$resposta['concluido'] = 0;
				
				//apc_delete("cacheT003-{$g['usuarioAtual']}");
				//apc_delete("cacheT003-resposta-{$g['usuarioAtual']}");
		        //$retorno = apc_store("cacheT003-{$g['usuarioAtual']}", serialize($cache));
				@unlink("/tmp/cacheT003-{$g['usuarioAtual']}");
				@unlink("/tmp/cacheT003-resposta-{$g['usuarioAtual']}");
				$retorno = file_put_contents("/tmp/cacheT003-{$g['usuarioAtual']}", serialize($cache));
		        if(!$retorno){
		        	$resposta['code'] = false;
		        	$resposta['data'] = "Erro ao criar variável no cache";
		        }
		    } else {
				//$cache = unserialize(apc_fetch("cacheT003-{$g['usuarioAtual']}"));
				//$resposta = unserialize(apc_fetch("cacheT003-resposta-{$g['usuarioAtual']}"));
				$cache = unserialize(file_get_contents("/tmp/cacheT003-{$g['usuarioAtual']}"));
				$resposta = unserialize(file_get_contents("/tmp/cacheT003-resposta-{$g['usuarioAtual']}"));

		        if(!$resposta){
		        	$resposta['code'] = false;
		        	$resposta['data'] = "Erro ao recuperar variável no cache resposta";
		        }
		    }

		    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

			if($resposta['etapa'] >= $resposta['etapas']){
				$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
			} else {
				$resposta['limit2'] += $resposta['registrosEtapa'] ;
			}

		    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);
		   
		    $r_opcoes = json_decode($r_opcoes);

		    if(!is_array($r_opcoes)) { 
		        $opcoes = get_object_vars($r_opcoes);
		    }

		    $ok = 0;
			$erro = 0;
			$erros ="";

		    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
		    	$T004_Id = $cache[$i]['T004_Id'];
		    	$D009_Id = $cache[$i]['D009_Id'];
		    	$T003_Flag_Lista_Preco = $cache[$i]['T003_Flag_Lista_Preco'];
		    	$T003_D024_Id = $cache[$i]['T003_D024_Id'];
		    	$T004_D006_Id = $cache[$i]['T004_D006_Id'];
		    	$T004_T219_Id = $cache[$i]['T004_T219_Id'];
		    	$D001_Peso_Unitario_Kg = $cache[$i]['$D001_Peso_Unitario_Kg'];
				$D001_Cubagem_Unitaria = $cache[$i]['$D001_Cubagem_Unitaria'];
				$T004_Preco_Pauta_ST = $cache[$i]['D009_Preco_Pauta_ST'];
				$T004_Flag_Origem_Produto = $cache[$i]['T004_Flag_Origem_Produto'];

		    	$retorno = $VEN001-> atualizarAliquotas($T003_Id, null, $T004_Id, $cache[$i]);
	            
	           	if($retorno !== true){
	       	     	$erro++;
	           	 	$erros .= $erro ." - ". $retorno."<br />";
           		}else{
           			$ok++;
           		}
	        }
		    $resposta['ok'] += $ok;
		    $resposta['erro'] += $erro;
		    $resposta['erros'] .= $erros;

			if($resposta['etapa'] >= $resposta['etapas']){
				//apc_delete("cacheT003-{$g['usuarioAtual']}");
				//apc_delete("cacheT003-resposta-{$g['usuarioAtual']}");
				@unlink("/tmp/cacheT003-{$g['usuarioAtual']}");
				@unlink("/tmp/cacheT003-resposta-{$g['usuarioAtual']}");
				
				$resposta['concluido'] = 1;
			} else {
		        //apc_delete("cacheT003-resposta-{$g['usuarioAtual']}");
				@unlink("/tmp/cacheT003-resposta-{$g['usuarioAtual']}");

		        $resposta['etapa'] = $resposta['etapa'] + 1;

		        //apc_store("cacheT003-resposta-{$g['usuarioAtual']}", serialize($resposta));
				file_put_contents("/tmp/cacheT003-resposta-{$g['usuarioAtual']}", serialize($resposta));
			}	        
		break;
		
		case 'salvarObservacao':
			require_once("bibliotecas/classes/VEN001.php");
			
			$form=$r_ven001FormPagamento;		
			$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
			$VEN001 = new VEN001();		
			$retorno = $VEN001->gravarObservacao($form, $T003_Id);
			
			if ($retorno === false) {			
				$resposta['code'] = false;
				$resposta['data'] = 'Falha na inclusão da Observação. Por favor, tente novamente';			
			} else {
				$resposta['code'] = true;
				$resposta['data'] = $retorno;
			}
		break;
		
		case 'confirmarTudo':
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			$sql = <<<EOT
 			update T004 set T004_Quantidade_Confirmada=T004_Quantidade where T004_T003_Id = '{$T003_Id}';
EOT;
			$request = mysqli_query($sql);

			$T003A = mysql_query("SELECT T003A_Flag_Conversao_Unidade
								    FROM T003A
								   WHERE T003A_T003_Id = '{$T003_Id}'");
			$mT003A = mysql_fetch_array($T003A);					   

			if($mT003A['T003A_Flag_Conversao_Unidade'] == 'S'){
				mysqli_query("UPDATE T004A 
				           LEFT JOIN T004 ON T004_Id = T004A_T004_Id
								 SET T004A_Quantidade_Comercial_Confirmada = T004A_Quantidade_Comercial
							   WHERE T004_T003_Id = '{$T003_Id}'");
			}

			
			if ($request) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
		break;

		case 'ven001ContentComprasPreencherPrecos':
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			mysql_query("UPDATE T004
                        LEFT JOIN T219 ON T219_Id=T004_T219_Id      
                        SET T004_Valor_Custo_Unitario=T219_Valor_Custo_Unitario,
 			                T004_Valor_Preco_Original=T219_Valor_Preco_Venda, 
 			                T004_Valor_Desconto_Unitario=0,
 			                T004_Percentual_Desconto=0,
 			                T004_Valor_Preco_Unitario=T219_Valor_Preco_Venda
 			          WHERE T004_T003_Id = '{$T003_Id}'
 			            AND T219_Id IS NOT NULL;");

			$T004 = mysql_query("select T004_Id from T004 where T004_T003_Id='{$T003_Id}'");
			while ($mT004=mysql_fetch_array($T004)) {
				   mysqli_query("call T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}')"); 
			}
		    mysqli_query("call T003_Gravar_Totalizacao_4('{$T003_Id}')");

			if ($request) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
		break;
			
		case 'zerarTudo':
			$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;
			
			$sql = <<<EOT
 			update T004 set T004_Quantidade_Confirmada=0 where T004_T003_Id = '{$T003_Id}';
EOT;
			$request = mysqli_query($sql);

			$T003A = mysql_query("SELECT T003A_Flag_Conversao_Unidade
									FROM T003A
		   						   WHERE T003A_T003_Id = '{$T003_Id}'");
			$mT003A = mysql_fetch_array($T003A);					   

			if($mT003A['T003A_Flag_Conversao_Unidade'] == 'S'){
				mysqli_query("UPDATE T004A 
				           LEFT JOIN T004 ON T004_Id = T004A_T004_Id
								 SET T004A_Quantidade_Comercial_Confirmada = 0
							   WHERE T004_T003_Id = '{$T003_Id}'");
			}

			if ($request) {
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}

		break;

		case 'carregaCFOP_Contatos':
			global $g;

			//T003_Flag_Acp
			$T003_D024_Id = isset($r_T003_D024_Id) ? $r_T003_D024_Id : false;
			if (empty($T003_D024_Id)) {
				$resposta['code'] = false;
				$resposta['data']['erro'] = "Nenhum cliente selecionado";
			} else {
				$sql = "SELECT D024_D006_Id, 
				               D024_D018_Id,
                               D024_D021_Id,
							   D024_D022_Id,
				               D024_Flag_Revenda_Consumidor,
				               D024_Flag_Contribuinte,
				               D024_Percentual_Comissao,
				               D024_Percentual_Comissao_Externo,
				               D021_Percentual_Comissao,
				               Externo.C007_Percentual_Comissao as ComissaoExterno,
				               Usuario.C007_Percentual_Comissao as ComissaoUsuario,
				               D024_Nome_Fantasia,
							   D024_C007_Vendedor_Interno,
							   D024_C007_Vendedor_Externo,
							   D024_Flag_Frete,
							   D024_Flag_Destino_Produto,
							   D024_Nome_Empresa,
							   D024_Id,
							   D024_Cpf,
							   D024_Cnpj,
							   D020_Nome_Cidade,
							   D018_UF
				          FROM D024 
				     LEFT JOIN D021 ON D021_Id=D024_D021_Id
				     LEFT JOIN C007 as Externo ON Externo.C007_Id=D024_C007_Vendedor_Externo
				     LEFT JOIN C007 as Usuario ON Usuario.C007_Id='{$g['usuarioAtual']}'
					 LEFT JOIN D020 ON D020_Id=D024_D020_Id 
					 LEFT JOIN D018 ON D018_Id=D024_D018_Id
				         WHERE D024_Id=" . $T003_D024_Id;
				$result = mysql_query($sql);
				$aD024 = mysql_fetch_assoc($result);

				echo mysql_error();
				
				$resposta['data']['setaDestinoVenda'] = false;
				$destinoPadrao = $g['conf']->camposValorPadrao("T003_Flag_Destino_Produto");
				if(isset($destinoPadrao)){
					$resposta['data']['setaDestinoVenda'] = true;
					$resposta['data']['T003_Flag_Destino_Produto'] = $destinoPadrao;
				} 

				if($g['C031']['definirDestinoVendaTipoCliente'] == 'S'){
					$resposta['data']['setaDestinoVenda'] = true;
					
					switch ($aD024['D024_Flag_Contribuinte']) {
						case '5':
							$resposta['data']['T003_Flag_Destino_Produto'] = '1';
							break;
						case '2':
							$resposta['data']['T003_Flag_Destino_Produto'] = '2';
							break;
						case '1':
							$resposta['data']['T003_Flag_Destino_Produto'] = '3';
							break;
					}
				}

				// Caso de erro de validacao pela T003.php no campo T003_D024_Id, perdia o cliente no form, forçando o valor caso de erro
				$resposta['data']['T003_D024_Id_Pesquisa'] = "{$aD024['D024_Nome_Empresa']} - {$aD024['D024_Id']}  ({$aD024['D024_Cnpj']}{$aD024['D024_Cpf']}) - [{$aD024['D020_Nome_Cidade']}/{$aD024['D018_UF']}]";
				
				if(!empty($aD024['D024_Flag_Destino_Produto'])) {
					$resposta['data']['setaDestinoVenda'] = true;
					$resposta['data']['T003_Flag_Destino_Produto'] = $aD024['D024_Flag_Destino_Produto'];
				}

				if(!empty($aD024['D024_Percentual_Comissao'])){
					$resposta['data']['T003_Percentual_Comissao'] = gCorrigeNumero($aD024['D024_Percentual_Comissao']);
				}else{
					$resposta['data']['T003_Percentual_Comissao'] = gCorrigeNumero($aD024['D021_Percentual_Comissao']);
				}
				$resposta['data']['T003_Percentual_Comissao_Representante'] = $aD024['D021_Percentual_Comissao']; 
				if(!empty($aD024['D024_Percentual_Comissao']) && $aD024['D024_Percentual_Comissao'] > 0){
					//Caso a comissao para o representante no cliente esteja preenchida, será utilizado este percentual
					$resposta['data']['T003_Percentual_Comissao_Representante'] = $aD024['D024_Percentual_Comissao']; 
				}

				$resposta['data']['T003_Percentual_Comissao_Externo'] = $aD024['ComissaoExterno'];
				if(!empty($aD024['D024_Percentual_Comissao_Externo']) && $aD024['D024_Percentual_Comissao_Externo'] > 0){
					//Caso a comissao para o vendedor externo no cliente esteja preenchida, será utilizado este percentual
					$resposta['data']['T003_Percentual_Comissao_Externo'] = $aD024['D024_Percentual_Comissao_Externo'];
				}
				$resposta['data']['T003_Percentual_Comissao_Usuario'] = $aD024['ComissaoUsuario']; 

				$C007=mysql_query("SELECT C007_Flag_Expedicao FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
				$mC007=mysql_fetch_array($C007);

				if($mC007['C007_Flag_Expedicao']=="1"){

					$resposta['data']['T003_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Comercial', 'value' => '1', 'selected'=>true),
							array('title' => 'Balcão', 'value' => '2'),
						)
					);
				} else if($mC007['C007_Flag_Expedicao']=="2"){

					$resposta['data']['T003_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Comercial', 'value' => '1'),
							array('title' => 'Balcão', 'value' => '2', 'selected'=>true),
						)
					);
				} else {
					$resposta['data']['T003_Flag_Expedicao'] = gProcessaSelect(
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Comercial', 'value' => '1'),
							array('title' => 'Balcão', 'value' => '2'),
						)
					);
				}

				$resposta['data']['T003_Nome_Cliente'] = $aD024['D024_Nome_Fantasia'];

				$resposta['data']['Altera_Flag_Acp'] = false;

				if ($g['C031']['codigoClienteCupomFiscal'] == $T003_D024_Id) {
					$resposta['data']['Altera_Flag_Acp'] = true;
					$resposta['data']['T003_Flag_Acp']   = 8;
				}				
				//Não pode alterar para NF porque se o cliente abriu o orçamento como PDV e vai colocar o CPF/CNPJ, tem que manter como NFC
				/* else {
					$resposta['data']['Altera_Flag_Acp'] = true;
					$resposta['data']['T003_Flag_Acp']   = 6;
				} */

				/*
				$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
				               C004_CFOP_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Dentro_Estado, 
				               C004_CFOP_CONSUMO_Venda_Fora_Estado, 
				               C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
				               C004_D018_Id 
				          FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
				$result = mysql_query($sql);
				$aC004 = mysql_fetch_assoc($result);

				echo mysql_error();

				$resposta['code'] = true;
				$D006_Id = $aD024['D024_D006_Id'];

                if ($D006_Id<=0)
                {
			        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			        {
			            $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
					}
					else
					{       
			            $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
					}	
				    if ($aD024['D024_Flag_Contribuinte'] == '1')
				    {
				        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				        {
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
						}
						else
						{
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
						}	
					}
				}
				*/
				
				require_once("bibliotecas/classes/VEN001.php");
				$VEN001 = new VEN001();
				$D006_Id=$VEN001->retornaCFOP($T003_D024_Id);

				$resposta['code'] = true;
				// se nenhuma regra satisfizer, usa o critério antigo	
				if ($D006_Id===false){
					$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
					               C004_CFOP_Venda_Fora_Estado, 
					               C004_CFOP_ST_Venda_Fora_Estado, 
					               C004_CFOP_ST_Venda_Dentro_Estado, 
					               C004_CFOP_CONSUMO_Venda_Fora_Estado, 
					               C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
					               C004_D018_Id 
					          FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
					$result = mysql_query($sql);
					$aC004 = mysql_fetch_assoc($result);

					echo mysql_error();

					$D006_Id = $aD024['D024_D006_Id'];

	                if ($D006_Id<=0)
	                {
				        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				        {
				            $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
						}
						else
						{       
				            $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
						}	
					    if ($aD024['D024_Flag_Contribuinte'] == '1')
					    {
					        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
					        {
					            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
							}
							else
							{
					            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
							}	
						}
					}
					if ($D006_Id<=0)
						$D006_Id=false;
				}	

				if(!empty($aD024['D024_D022_Id'])){
					$sqlD022 = mysql_query("SELECT D022_Nome_Empresa,D020_Nome_Cidade,D018_UF FROM D022 LEFT JOIN D020 ON D020_Id=D022_D020_Id LEFT JOIN D018 ON D018_Id=D022_D018_Id WHERE D022_Id='{$aD024['D024_D022_Id']}'");
					$D022 = mysql_fetch_assoc($sqlD022);
					$resposta['data']['T003_D022_Id_Pesquisa'] = $D022['D022_Nome_Empresa']." - ".$D022['D020_Nome_Cidade']." - ".$D022['D018_UF'];
					$resposta['data']['T003_D022_Id'] = $aD024['D024_D022_Id'];
				}

				// se apenas uma regra tiver sido satisfeita
			    if ($D006_Id>0) 
				{
					$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo!="S" ORDER BY D006_Codigo_CFOP',true, $D006_Id);
					$resposta['data']['T003_D006_Id'] = gProcessaSelect($arrayDentroEstado);
				}
				// se nenhuma regra tiver sido satisfeita (critério novo e antigo)
				else if ($D006_Id===false)
				{
					$resposta['data']['T003_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE D006_Flag_Inativo!="S" ORDER BY D006_Codigo_CFOP',true));
				}
				// se mais de uma regra tiver sido satisfeita
				else
				{
					//$resposta['code'] = false;
					//$resposta['data']['erro'] = $D006_Id;	
				}
				//$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id='{$T003_D024_Id}'",true));
				$resposta['data']['T003_Flag_Revenda_Consumidor'] = "$aD024[D024_Flag_Revenda_Consumidor]";


				$resposta['data']['notificarVendedor'] = 0;

				if($aD024['D024_C007_Vendedor_Interno'] != $g['usuarioAtual']){
					$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM C007 WHERE C007_Id = '{$aD024['D024_C007_Vendedor_Interno']}'");
					if(mysql_num_rows($sqlC007) > 0){
						$resC007 = mysql_fetch_array($sqlC007);
						if($resC007['C007_Flag_Ausente'] == 'S'){
							$resposta['data']['T003_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
							$resposta['data']['notificarVendedor'] = 2;
						} else {
							$resposta['data']['T003_C007_Id_Vendedor_Interno'] = $aD024['D024_C007_Vendedor_Interno'];
							$resposta['data']['notificarVendedor'] = 1;
						}
					} else {
						$resposta['data']['T003_C007_Id_Vendedor_Interno'] = $g['usuarioAtual'];
					}
				} else {
					$resposta['data']['T003_C007_Id_Vendedor_Interno'] = $aD024['D024_C007_Vendedor_Interno'];
				}
				$resposta['data']['T003_C007_Id_Vendedor_Externo'] = $aD024['D024_C007_Vendedor_Externo'];

				// carrega os contatos do cliente selecionado
				if($r_D013_Contato_Pre_Selecionado > 0){
					//select para carregar os contatos e deixar o contato pré selecionado na combo
					$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true, $r_D013_Contato_Pre_Selecionado));
					$resposta['data']['T003_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true, $r_D013_Contato_Pre_Selecionado)));
				} else if($g['C031']['orcamentoCarregarPrimeiroContato'] == 'N'){
					//select para carregar os contatos e deixar "..." selecionado na combo
					$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true));
					$resposta['data']['T003_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true)));
				}else{
					//select para carregar os contatos e deixar o primeiro contato selecionado na combo
					$D013_Contato_Selecionado = mysql_query("select D013_Id from D013 WHERE D013_D024_Id=".$T003_D024_Id." AND D013_Flag_Ativo = 'S' ORDER BY D013_Id LIMIT 1");
					$D013_Contato_Selecionado = mysql_fetch_array($D013_Contato_Selecionado);
					
					$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',false, $D013_Contato_Selecionado['D013_Id']));
					$resposta['data']['T003_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T003_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',false, $D013_Contato_Selecionado['D013_Id'])));
				}

				//carrega os veículos do cliente selecionado
				$resposta['data']['T003_D116_Id'] = gProcessaSelect(gGeraSelect('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T003_D024_Id,true));
				$resposta['data']['T003_D116_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor',' JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T003_D024_Id,true)));


				// endereço de entrega
				$D148_Id_Entrega = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T003_D024_Id}' AND D148_Flag_Tipo = 'E'");
				if (mysql_num_rows($D148_Id_Entrega) > 0) {
					$D148_Id_Entrega = mysql_fetch_assoc($D148_Id_Entrega);
					$D148_Id_Entrega = $D148_Id_Entrega['D148_Id'];
				} else {
					$D148_Id_Entrega = false;
				}
				$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Entrega);
				$array[0]['title'] = 'PADRÃO';
				$resposta['data']['T003_D148_Id_Entrega'] = gProcessaSelect($array);
				$resposta['data']['T003_D148_Id_Entrega_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Entrega)));

				// endereço de cobrança
				$D148_Id_Cobranca = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T003_D024_Id}' AND D148_Flag_Tipo = 'C'");
				if (mysql_num_rows($D148_Id_Cobranca) > 0) {
					$D148_Id_Cobranca = mysql_fetch_assoc($D148_Id_Cobranca);
					$D148_Id_Cobranca = $D148_Id_Cobranca['D148_Id'];
				} else {
					$D148_Id_Cobranca = false;
				}
				$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Cobranca);
				$array[0]['title'] = 'PADRÃO';
				$resposta['data']['T003_D148_Id_Cobranca'] = gProcessaSelect($array);
				$resposta['data']['T003_D148_Id_Cobranca_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Cobranca)));

				// endereço de redespacho
				$D148_Id_Redespacho = mysql_query("SELECT D148_Id FROM D148 WHERE D148_D024_Id='{$T003_D024_Id}' AND D148_Flag_Tipo = 'R'");
				if (mysql_num_rows($D148_Id_Redespacho) > 0) {
					$D148_Id_Redespacho = mysql_fetch_assoc($D148_Id_Redespacho);
					$D148_Id_Redespacho = $D148_Id_Redespacho['D148_Id'];
				} else {
					$D148_Id_Redespacho = false;
				}
				$array = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Redespacho);
				$array[0]['title'] = 'PADRÃO';
				$resposta['data']['T003A_D148_Id_Redespacho'] = gProcessaSelect($array);
				$resposta['data']['T003A_D148_Id_Redespacho_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$T003_D024_Id.' ORDER BY D148_Id', true, $D148_Id_Redespacho)));
				
				//carrega o representante
				$resposta['data']['T003_D021_Id'] = gProcessaSelect(gGeraSelect('D021','D021_Id','D021_Nome_Empresa', '',true,$aD024['D024_D021_Id']));

				//carrega tipo frete
				$resposta['data']['T003_Flag_Frete'] = $aD024['D024_Flag_Frete'];
			}
		break;
		
	case 'verificaFlagContribuinte':

		global $g;
		
		$T003_D024_Id = isset($r_T003_D024_Id) ? $r_T003_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Contribuinte, D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$T003_D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		$msg = "";
		$D024['D024_Flag_Lista_Preco'] = trim($D024['D024_Flag_Lista_Preco']);
		if (!empty($D024['D024_Flag_Lista_Preco']))	{
			$retorno = 1;
		}else{
			$retorno = 2;
			$msg .= "Favor preencher o campo LISTA PREÇO no cadastro do cliente<br />";
		}
		
		if ($D024['D024_Flag_Contribuinte']>-1)	{
			$retorno = ($retorno == 2) ? 2 : 1;
		}else{
			$retorno = 2;
			$msg .= "Favor preencher o campo TIPO no cadastro do cliente";
		}
		
		if ($retorno == 1) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $msg;
		}
	break;	
	
	case 'verificaFlagListaPreco':
		global $g;
		
		$T003_D024_Id = isset($r_T003_D024_Id) ? $r_T003_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$T003_D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		switch($D024['D024_Flag_Lista_Preco']){
			case '1':
				$resposta['data']['T003_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1', 'selected' => true),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '2':
				$resposta['data']['T003_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2', 'selected' => true),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '3':
				$resposta['data']['T003_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3', 'selected' => true),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5'),
						));
				$resposta['code'] = true;
				break;
			case '4':
				$resposta['data']['T003_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4', 'selected' => true),   
							array('title' => 'Preço 5', 'value' => '5'),   
						));
				$resposta['code'] = true;
				break;
			case '5':
				$resposta['data']['T003_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5', 'selected' => true), 
						));
				$resposta['code'] = true;
				break;
		}
		
	break;

	case 'inserirParcela':
		require_once("bibliotecas/classes/VEN001.php");
		$form_tabela=(isset($r_form_tabela)) ? $r_form_tabela : false;
		$T003_Id=(isset($r_T003_Id)) ? $r_T003_Id : false;
		$form_campo_pk=(isset($r_form_campo_pk)) ? $r_form_campo_pk : false;
		$dadosTabela = array('tabela' => $form_tabela, 
							 'tabelaId' => 	$T003_Id,
							 'campos' => 'T003_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                 T003_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                 T003_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                 T003_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                 T003_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                 T003_D027_Id as D027_Id,
                                 T003_Pgto_Parcelas as Total_Parcelas,
                                 T003_Valor_Total as valorTotal', 
                             'where' => $form_campo_pk."='{$T003_Id}'", 
                             'tabelaPgto' => 'T089', 
                             'camposPgto' => 'T089_Prazos_Dias as prazoDias,T089_D027_Id as D027Pagamento,T089_Numero_Parcela as numeroParcela, T089_Data_Vencimento as dataVencimento', 
                             'wherePgto' => "T089_T003_Id='{$T003_Id}'");

		$VEN001 = new VEN001();		
		$retorno = $VEN001->inserirParcela($dadosTabela);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;
	
	case 'calcularParcelaSimulacao':
		require_once("bibliotecas/classes/VEN001.php");
		
		$form=$r_ven001FormPagamento;		
		$VEN001 = new VEN001();		
		$retorno = $VEN001->calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'gravarDesconto':
		require_once("bibliotecas/classes/VEN001.php");
		
		$T003_Id=(isset($r_T003_Id)) ? $r_T003_Id : false;
		$T003_Valor_Desconto = (isset($r_T003_Valor_Desconto)) ? $r_T003_Valor_Desconto : false;
		$T003_Valor_Desconto_Confirmado = (isset($r_T003_Valor_Desconto_Confirmado)) ? $r_T003_Valor_Desconto_Confirmado : false;
		$valor = (isset($r_Valor)) ? $r_Valor : false;
		$VEN001 = new VEN001();		
		$retorno = $VEN001->gravarDesconto($T003_Id, $T003_Valor_Desconto, $T003_Valor_Desconto_Confirmado, $valor);
		
		if ($retorno !== true) {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'verificaAlcada':
			require_once("bibliotecas/classes/VEN001.php");
			$VEN001 = new VEN001();
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$retorno = $VEN001->verificaAlcada($T003_Id);
			//die($retorno. "AA");
			
			//Calcula Desconto por alçada
			$descontoOrcamento = $VEN001->calculaPercentualDescontoOrcamento($T003_Id);
			$descontoOrcamento = number_format($descontoOrcamento, 2, '.', ' ');
			$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
			$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
			
			//Calcula prazo médio por alçada
			$prazoMedioAlcadaVendedor = $VEN001->calculaPrazoMedioAlcadaVendedor($T003_Id);
			$prazoMedioAlcadaSupervisor = $VEN001->calculaPrazoMedioAlcadaSupervisor($T003_Id);
			$ret=mysql_query("SELECT AVG(T089_Prazos_Dias) FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
			$T003=mysql_fetch_array($ret);				
			$prazoMedio=$T003['AVG(T089_Prazos_Dias)'];

			// Globais de configuração da alçada por desconto do produto
			$alcadaDescontoVendedorPorProduto = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
			$alcadaDescontoSupervisorPorProduto = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');

			$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($alcadaDescontoSupervisorPorProduto);
			$alcadaDescontoVendedorPorProduto = gCorrigeNumeroInverte($alcadaDescontoVendedorPorProduto);

			$usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');
			$T003MaiorDesconto = $VEN001->retornaMaiorDescontoOrcamento($T003_Id);
			$T003MaiorDesconto = number_format($T003MaiorDesconto, 2, ".", " ");
  			
  			//Verificações de alçada
            $retornoSaldoMinimo = $VEN001->verificaFuturoSaldoProduto($T003_Id);
            
			if(($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo==0)){
				$alcadaUpdate = "V";
			} 
			elseif(($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){
				$alcadaUpdate = "S";
			}else{
				 $alcadaUpdate = "D";
			}

			//die("T003_Maior_Desconto: " . $T003MaiorDesconto . " alçada supervisor: ".$alcadaDescontoSupervisorPorProduto);

			// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
			if($usarAlcadaPorProduto == "S"){

				if($T003MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

					$alcadaUpdate = "V";		

				}else if($T003MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

					$alcadaUpdate = "S";

				} else {

					$alcadaUpdate = "D";
				}
			}
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";
			} elseif($retorno === false) {
				$resposta['code'] = false;
				$resposta['data'] = "Você não tem permissão para gerar Orçamentos!";
			} else{
				$resposta['code'] = $retorno;
				$resposta['data'] = $retorno;
				$resposta['alcadaUpdate'] = $alcadaUpdate;
			}
		break;
		
	case 'enviarParaAnalise':
			require_once("bibliotecas/classes/VEN001.php");
			$VEN001 = new VEN001();
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
			
			$retorno = $VEN001->enviarParaAnalise($T003_Id,$alcadaUpdate);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Orçamento enviado para análise";
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;

		case 'ven001ImprimirOrcamento':
			global $g;

			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
			$moeda = isset($r_moeda) ? $r_moeda : false;

			if (isset($r_gerarHtml)) {
				echo $VEN001->montarEmailPdf($T003_Id, true, $moeda);
				die();
			} else {
				$T089 = mysql_query("SELECT SUM(T089_Valor_Parcela) as TotalParcelas, T003_Valor_Total FROM T089 LEFT JOIN T003 ON T003_Id = T089_T003_Id WHERE T089_T003_Id='{$T003_Id}'");
				$resT089 = mysql_fetch_array($T089);

				$sql="SELECT D006_Flag_Gera_Contas FROM T003 LEFT JOIN D006 on D006_Id=T003_D006_Id WHERE T003_Id='{$T003_Id}'";
				$resultado=mysql_query($sql);
				$row=mysql_fetch_array($resultado);

				if($row['D006_Flag_Gera_Contas'] == "R" && $g['C031']['permitirOrcamentoEmailImpSemPagamento'] == 'N'){
					if($resT089['TotalParcelas'] == 0){
						$erro = "É necessário preencher a forma de pagamento";
					} else {
						if($resT089['T003_Valor_Total'] != $resT089['TotalParcelas']){
							$erro = "Valor das parcelas não confere com o total do pedido.";
						}
					}
				}
				if(isset($erro)){
					$resposta['code'] = false;
					$resposta['data'] = $erro;
				} else {
					$retorno = $VEN001->montarEmailPdf($T003_Id, false, $moeda);
					$resposta['code'] = true;
					$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
				}
			}
		break;

		case 'orcamentoWhats':
			global $g;

			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$phone = $r_phone;

			$T004 = mysql_query("select T004_Codigo_Produto, 
										T004_Descricao_Produto, 
										T004_Quantidade, 
										T004_Valor_Preco_Sem_Desconto_Unitario,
										T004_Valor_Preco_Bruto
								   from T004
								  where T004_T003_Id='{$T003_Id}'");

			$retorno = "Orçamento N. " . $T003_Id . chr(10).chr(13);
			$retorno .= "---------------------------------" . chr(10);
			while ($mT004 = mysql_fetch_array($T004)){
				   $preco = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? $mT004['T004_Valor_Preco_Bruto'] : $mT004['T004_Valor_Preco_Sem_Desconto_Unitario'];
				   $retorno .= 'Código: ' . $mT004['T004_Codigo_Produto'] . chr(10) .
							   'Descrição: ' . $mT004['T004_Descricao_Produto'] . chr(10) .
							   'Quantidade: ' . gCorrigeNumero($mT004['T004_Quantidade']) . chr(10) .
							   'Preço unitário: ' . gCorrigeNumero($preco) . chr(10);
				   $retorno .= "---------------------------------" . chr(10);
			}

			$T003 = mysql_query("select C007_Nome, T003_D013_Id, T003_D024_Id from T003 left join C007 on C007_Id=T003_C007_Id where T003_Id='$T003_Id'");
			$mT003 = mysql_fetch_array($T003);

			$assunto   = 'Orçamento N. ' . $T003_Id;
			$de        = $mT003['C007_Nome'];
			$para      = 'Whatsapp: ' .$phone;

			mysql_query("INSERT INTO T139 ( T139_Pasta, 
											T139_Uid, 
											T139_Titulo, 
											T139_De, 
											T139_Para, 
											T139_Data_Hora, 
											T139_Estrutura, 
											T139_Mensagem_Corpo, 
											T139_Flag_Lido, 
											T139_D024_Id, 
											T139_D013_Id, 
											T139_T003_Id, 
											T139_C007_Id) 
									VALUES ('S', 
											0, 
											'".mysql_real_escape_string($assunto)."', 
											'".mysql_real_escape_string($de)."', 
											'".mysql_real_escape_string($para)."', 
											'".date('Y-m-d H:i:s')."', 
											'" . mysql_real_escape_string($retorno) . "', 
											'" . mysql_real_escape_string($retorno) . "', 
											'1', 
											'".mysql_real_escape_string($mT003['T003_D024_Id'])."', 
											'".mysql_real_escape_string($mT003['T003_D013_Id'])."', 
											'".mysql_real_escape_string($T003_Id)."', 
											'".mysql_real_escape_string($g['usuarioAtual'])."')");

			mysql_query("UPDATE T003A 
					  LEFT JOIN T003 ON T003A_T003_Id = T003_Id 
				 			SET T003A_Data_Envio_Primeiro_Email = '".date('Y-m-d H:i:s')."'
						  WHERE T003A_T003_Id ='{$T003_Id}' 
							AND T003A_Data_Envio_Primeiro_Email = '0000-00-00 00:00:00'");

			$sqlT259 = mysql_query("SELECT T259_Id, 
										   T259_C007_Id 
									  FROM D085 
								 LEFT JOIN T259 ON T259_Id=D085_T259_Id 
								     WHERE D085_Entidade = 'T003' 
									   AND D085_Id_Entidade = '{$T003_Id}'");

			$rowT259 = mysql_fetch_assoc($sqlT259);
			require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
			$arrayAssuntoTarefa = array(
					'campoAtual' => '',
					'todosCampos' => array(
					'T259_Id' => $rowT259['T259_Id'],
					'T259_C007_Id' => $rowT259['T259_C007_Id']
				)
			);
				
			assuntoTarefa($arrayAssuntoTarefa, false);

			$resposta['code'] = true;
			$resposta['data'] = urlencode($retorno);


		break;

		case 'ven001PhoneWhats':
			global $g;

			$T003=mysql_query("select T003_Id,T003_D013_Id, D024_DDD_Celular, D024_Celular, D013_DDD_Celular, D013_Celular from T003 left join D024 on D024_Id=T003_D024_Id left join D013 on D013_Id=T003_D013_Id where T003_Id='{$r_T003_Id}'");
			$mT003 = mysql_fetch_assoc($T003);
	
			$D013_Id=$mT003['T003_D013_Id'];
	
			$phoneWhatsApp = '';
			$notificaSemPhone = '';
			if (!empty($mT003['D013_Celular'])){
				$phoneWhatsApp = '+55'.trim($mT003['D013_DDD_Celular']).trim($mT003['D013_Celular']);		
			} else if (!empty($mT003['D024_Celular'])) {
				$phoneWhatsApp = '+55'.trim($mT003['D024_DDD_Celular']).trim($mT003['D024_Celular']);		
			} else {
				$notificaSemPhone = " ** Contato não possui celular cadastrado, atualize agora! ** ";
			}
			$phoneWhatsApp = str_replace('-','',$phoneWhatsApp);
	
			$resposta['phoneWhatsApp'] = urlencode($phoneWhatsApp);
			$resposta['notificaSemPhone'] = htmlspecialchars($notificaSemPhone);

		break;

		case 'orcamentoWhatsPDF':
			global $g;
			
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
			$moeda = isset($r_moeda) ? $r_moeda : 1;
			$phone = $r_phone;

			$T089 = mysql_query("SELECT SUM(T089_Valor_Parcela) as TotalParcelas, T003_Valor_Total FROM T089 LEFT JOIN T003 ON T003_Id = T089_T003_Id WHERE T089_T003_Id='{$T003_Id}'");
			$resT089 = mysql_fetch_array($T089);

			$sql="SELECT D006_Flag_Gera_Contas FROM T003 LEFT JOIN D006 on D006_Id=T003_D006_Id WHERE T003_Id='{$T003_Id}'";
			$resultado=mysql_query($sql);
			$row=mysql_fetch_array($resultado);

			if($row['D006_Flag_Gera_Contas'] == "R" && $g['C031']['permitirOrcamentoEmailImpSemPagamento'] == 'N'){
				if($resT089['TotalParcelas'] == 0){
					$erro = "É necessário preencher a forma de pagamento";
				} else {
					if($resT089['T003_Valor_Total'] != $resT089['TotalParcelas']){
						$erro = "Valor das parcelas não confere com o total do pedido.";
					}
				}
			}
			if(isset($erro)){
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			} else {

				// Arquivo
				$dir = 'whatsapp/';
				is_dir($dir) OR mkdir($dir, 0777, true);
				
				$filename             = md5('T003'.$T003_Id.uniqid('',true)).".pdf";
				$file                 = $dir . $filename;
				$fileFull             = $g['pathRaiz'] . '/' . $file;
				$urlFile              = $g['urlRaiz'] . 'hardness3/' . $file;
				
				$r_link = '/ven/ven001/form_func-ajax/ven001ImprimirOrcamento/?ajax=true&gerarHtml=1&T003_Id=' . $T003_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
				$r_link = str_replace(' ', '+', $r_link);
				shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --minimum-font-size 13 --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
				
				if(file_exists($file)){
					
					$texto  = "Olá, segue link do nosso orçamento N. " . $T003_Id . chr(10);
					$texto .= $urlFile;

					$T003 = mysql_query("select C007_Nome, T003_D013_Id, T003_D024_Id from T003 left join C007 on C007_Id=T003_C007_Id where T003_Id='$T003_Id'");
					$mT003 = mysql_fetch_array($T003);

					$assunto   = 'Orçamento N. ' . $T003_Id;
					$de        = $mT003['C007_Nome'];
					$para      = 'Whatsapp: ' .$phone;

					mysql_query("INSERT INTO T139 ( T139_Pasta, 
													T139_Uid, 
													T139_Titulo, 
													T139_De, 
													T139_Para, 
													T139_Data_Hora, 
													T139_Estrutura, 
													T139_Mensagem_Corpo, 
													T139_Flag_Lido, 
													T139_D024_Id, 
													T139_D013_Id, 
													T139_T003_Id, 
													T139_C007_Id) 
											VALUES ('S', 
													0, 
													'".mysql_real_escape_string($assunto)."', 
													'".mysql_real_escape_string($de)."', 
													'".mysql_real_escape_string($para)."', 
													'".date('Y-m-d H:i:s')."', 
													'" . mysql_real_escape_string($texto) . "', 
													'" . mysql_real_escape_string($texto) . "', 
													'1', 
													'".mysql_real_escape_string($mT003['T003_D024_Id'])."', 
													'".mysql_real_escape_string($mT003['T003_D013_Id'])."', 
													'".mysql_real_escape_string($T003_Id)."', 
													'".mysql_real_escape_string($g['usuarioAtual'])."')");

					mysql_query("UPDATE T003A 
							  LEFT JOIN T003 ON T003A_T003_Id = T003_Id 
									SET T003A_Data_Envio_Primeiro_Email = '".date('Y-m-d H:i:s')."'
								  WHERE T003A_T003_Id ='{$T003_Id}' 
									AND T003A_Data_Envio_Primeiro_Email = '0000-00-00 00:00:00'");

					$sqlT259 = mysql_query("SELECT T259_Id, 
												   T259_C007_Id 
										      FROM D085 
										 LEFT JOIN T259 ON T259_Id=D085_T259_Id 
											 WHERE D085_Entidade = 'T003' 
											   AND D085_Id_Entidade = '{$T003_Id}'");

					$rowT259 = mysql_fetch_assoc($sqlT259);
					require_once('bibliotecas/funcoesCallback/assuntoTarefa.php');
					$arrayAssuntoTarefa = array(
						'campoAtual'   => '',
						'todosCampos'  => array(
							'T259_Id'      => $rowT259['T259_Id'],
							'T259_C007_Id' => $rowT259['T259_C007_Id']
						)
					);
					
					assuntoTarefa($arrayAssuntoTarefa, false);

					$resposta['code'] = true;
					$resposta['data'] = urlencode($texto);

				}else{
					$resposta['code'] = false;
					$resposta['data'] = "Houve um problema ao gerar o PDF, favor tentar novamente";
				}
			}

		break;

		case 'ven001DeletarPDFOrcamento':
			global $g;
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			$dir = $g['pathDados'] . 'tmp/';
			$filename             = "orcamento-{$T003_Id}.pdf";
			$fileFull             = $g['pathRaiz'] . '/' . $dir . $filename;
			if(file_exists($fileFull)){
				unlink($fileFull);
			}
		break;
		/*
		case 'carregaCFOP':
			
			global $g;
			
			$T003_D024_Id = isset($r_T003_D024_Id) ? $r_T003_D024_Id : false;
			$sql = "SELECT D024_D006_Id, D024_D018_Id FROM D024 WHERE D024_Id=" . $T003_D024_Id;
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			
			$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, C004_CFOP_Venda_Fora_Estado, C004_D018_Id FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
			$result = mysql_query($sql);
			$aC004 = mysql_fetch_assoc($result);
			
			$resposta['code'] = true;
			$D006_Id = $aD024['D024_D006_Id'];
			
			$array3 = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$D006_Id.' ORDER BY D006_Codigo_CFOP');
			
			if(!empty($array3)){
				$resposta['data']['T003_D006_Id'] = gProcessaSelect($array3);
				
			}else if($aD024['D024_D018_Id'] == $aC004['C004_D018_Id']){
				$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Dentro_Estado']);
				$resposta['data']['T003_D006_Id'] = gProcessaSelect($arrayDentroEstado);
				
			}else if($aD024['D024_D018_Id'] != $aC004['C004_D018_Id']){
				$arrayForaEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Fora_Estado']);
				$resposta['data']['T003_D006_Id'] = gProcessaSelect($arrayForaEstado);
			}else{
				$resposta['data']['T003_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' ORDER BY D006_Codigo_CFOP',true));
			}
			$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"WHERE D013_D024_Id='{$T003_D024_Id}'",true));	
			
			
		break;
		*/
		
		case 'atualizarCamposCliente':
			$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
			$sql = "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}';";
			$res = mysql_query($sql);
			$row = mysql_fetch_assoc($res);
			$resposta['code'] = true;
			$resposta['data'] = $row;
		break;
		
		case 'verificarParcelas':
			$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
			
			$T089 = mysql_query("SELECT SUM(T089_Valor_Parcela) as TotalParcelas, T003_Valor_Total FROM T089 LEFT JOIN T003 ON T003_Id = T089_T003_Id WHERE T089_T003_Id='{$T003_Id}'");
			$resT089 = mysql_fetch_array($T089);

			$sql="SELECT D006_Flag_Gera_Contas FROM T003 LEFT JOIN D006 on D006_Id=T003_D006_Id WHERE T003_Id='{$T003_Id}'";
			$resultado=mysql_query($sql);
			$row=mysql_fetch_array($resultado);

			if($row['D006_Flag_Gera_Contas'] == "R" && $g['C031']['permitirOrcamentoEmailImpSemPagamento'] == 'N'){
				if($resT089['TotalParcelas'] == 0){
					$erro = "É necessário preencher a forma de pagamento";
				} else {
					if($resT089['T003_Valor_Total'] != $resT089['TotalParcelas']){
						$erro = "Valor das parcelas não confere com o total do orçamento.";
					}
				}
			}

        	// 22/01/2025 - Felipe Carrano
        	// Validação por segurança porque em alguns casos estava zerando o T004_Percentual_Desconto e ajustando o T004_Valor_Preco_Unitario mantendo o valor final do item errado
       	 	// Ainda não foi encontrado o problema
			$validaDesconto = $VEN001->validaDescontoAplicado($T003_Id);
			if($validaDesconto !== true){
				$erro = $validaDesconto;
			}

			if(isset($erro)){
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			} else {
				$resposta['code'] = true;
			}
		break;

	case 'ven001aplicarDescontoReais':
		require_once("bibliotecas/classes/VEN001.php");
	
		$tabelaCabecalho = isset($r_tabelaCabecalho) ? $r_tabelaCabecalho : false;
		$idTabelaCabecalho = isset($r_idTabelaCabecalho) ? $r_idTabelaCabecalho : false;
		$tabelaProduto = isset($r_tabelaProduto) ? $r_tabelaProduto : false;
		$descontoReais = isset($r_descontoReais) ? $r_descontoReais : false;

		$VEN001 = new VEN001();
		
		$retorno = $VEN001->VEN001aplicarDescontoReais($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $descontoReais);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'observacaoEntrega':
		require_once("bibliotecas/classes/VEN001.php");
		
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T004_Quantidade = isset($r_T004_Quantidade) ? gCorrigeNumeroInverte($r_T004_Quantidade) : false;
		$T004_Codigo_Produto = isset($r_T004_Codigo_Produto) ? $r_T004_Codigo_Produto : false;
		
		$VEN001 = new VEN001();	
		$retorno = $VEN001->observacaoEntrega($D009_Id, $T004_Quantidade, $T004_Codigo_Produto);		
		if ($retorno !== false) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'ven001aplicarDescontoTotal':
		require_once("bibliotecas/classes/VEN001.php");
		
		$tabelaCabecalho = isset($r_tabelaCabecalho) ? $r_tabelaCabecalho : false;
		$idTabelaCabecalho = isset($r_idTabelaCabecalho) ? $r_idTabelaCabecalho : false;
		$tabelaProduto = isset($r_tabelaProduto) ? $r_tabelaProduto : false;
		$descontoValorTotal = isset($r_descontoValorTotal) ? $r_descontoValorTotal : false;

		$VEN001 = new VEN001();
		
		$retorno = $VEN001->VEN001aplicarDescontoTotal($tabelaCabecalho, $idTabelaCabecalho, $tabelaProduto, $descontoValorTotal);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCamposProdutosEditar':

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T003_Id = isset($_REQUEST['T003_Id']) ? $_REQUEST['T003_Id'] : false;

		$Aliquotas = $VEN001->retornaAliquotas($D009_Id, $T003_Id, false);

		$T003=mysql_query("SELECT * FROM T003 
		                  LEFT JOIN D024 on D024_Id=T003_D024_Id
		                  WHERE T003_Id='{$T003_Id}'");
		$mT003=mysql_fetch_assoc($T003);

	    $D001=mysql_query("SELECT * FROM D001 
	                      LEFT JOIN D049 ON D001_Id=D049_D001_Id 
	                      LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                      LEFT JOIN D082 ON D082_Id=D049_D082_Id
	                      left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT003['T003_D024_Id']}'
	                      WHERE D009_Id='{$D009_Id}'");
	    $mD001=mysql_fetch_assoc($D001);
	    $mD001['D009_Valor_Custo_Unitario'] = gCorrigeNumero($mD001['D009_Valor_Custo_Unitario']);

	    $row=array();

	    $row['D006_Id'] 					= $Aliquotas['D006_Id'];
	    $row['D005_Id'] 					= $Aliquotas['D005_Id'];
	    $row['D009_C004_Id'] 				= $mD001['D009_C004_Id'];
	    $row['Aliquota_IPI'] 				= gCorrigeNumero($Aliquotas['Aliquota_IPI']);
	    $row['Situacao_Tributaria_IPI'] 	= $Aliquotas['Situacao_Tributaria_IPI'];
	    $row['D082_Id'] 					= $Aliquotas['D082_Id'];
	    $row['D059_Id'] 					= $Aliquotas['D059_Id'];
	    $row['ST_ICMS_Interno'] 			= gCorrigeNumero($Aliquotas['ST_ICMS_Interno']);
	    $row['ST_ICMS_Interno_MVA'] 		= gCorrigeNumero($Aliquotas['ST_ICMS_Interno_MVA']);
	    $row['ICMS_Inter_Estadual'] 		= gCorrigeNumero($Aliquotas['ICMS_Inter_Estadual']);
	    $row['ICMS_Inter_Estadual_MVA'] 	= gCorrigenumero($Aliquotas['ST_ICMS_Inter_Estadual_MVA']);
	    $row['ST_VA'] 						= gCorrigeNumero($Aliquotas['ST_VA']);
	    $row['Percentual_PIS'] 				= gCorrigeNumero($Aliquotas['Aliquota_PIS']);
	    $row['Flag_Isento_PIS'] 			= $Aliquotas['Flag_Isento_PIS'];
	    $row['Situacao_Tributaria_PIS'] 	= $Aliquotas['Situacao_Tributaria_PIS'];
	    $row['Percentual_COFINS'] 			= $Aliquotas['Aliquota_COFINS'];
	    $row['Flag_Isento_COFINS'] 			= $Aliquotas['Flag_Isento_COFINS'];
	    $row['Situacao_Tributaria_COFINS'] 	= $Aliquotas['Situacao_Tributaria_COFINS'];
	    $row['Aliquota_ICMS_Correto'] 		= gCorrigeNumero($Aliquotas['Aliquota_ICMS']);
	    $row['Percentual_Reducao_ICMS_Correto'] 	= gCorrigeNumero($Aliquotas['Percentual_Reducao_ICMS']);
	    $row['Flag_ST'] 					= $Aliquotas['Flag_ST'];
	    //die("ST->".$row['Flag_ST']);

		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 */
		$cfop = $VEN001->retornaCFOP($mT003['T003_D024_Id'], $D009_Id, $mT003['T003_C004_Id'], $mT003['T003_Flag_Destino_Produto'], false, false, false, $mT003['T003_C004_Id_Estoque']);

		$row['D006_Id'] = $cfop;
		$sql =	"SELECT T003_D024_Id, T003_Flag_Lista_Preco, T003_Flag_Acp from T003 WHERE T003_Id = '{$T003_Id}'";
		$resultado = mysql_query($sql);
		$mT003 = mysql_fetch_assoc($resultado);

		$dadosPrecoVenda = $CAD002->retornaPrecoVenda($D009_Id, $mT003['T003_Flag_Lista_Preco'], $mT003['T003_D024_Id'], false, $cfop, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
		$row['Preco_Produto'] = $dadosPrecoVenda[0];
		$row['demonstrativoPrecoVenda'] = serialize($dadosPrecoVenda[1]);
		$row['Custo_Produto'] = number_format($dadosPrecoVenda[4],2,'.', '');

		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT003['T003_D024_Id'], $D009_Id);
		$row2 = array_merge($row, $mD001);
		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row2;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'verificaAlcadaNovo':
		global $g;

		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		$msg="";
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$gerarPedido = isset($r_gerarPedido) ? $r_gerarPedido : false;

		// Não efetua verificação se já estiver em análise
		$T003=mysql_query("SELECT T003_Flag_Status_Orcamento, T003_Flag_Frete, T003_Valor_Frete, ROUND(SUM(T004_Valor_Frete_Total),2) as FreteProdutos FROM T003 LEFT JOIN T004 ON T004_T003_Id=T003_Id WHERE T003_Id='{$T003_Id}' GROUP BY T003_Id");
		$mT003=mysql_fetch_array($T003);

		//Verifica o frete dos produtos caso obrigatório rateio do frete quando por conta do emitente
		if($mT003['T003_Valor_Frete'] != $mT003['FreteProdutos'] && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT003['T003_Flag_Frete'] == '0'){
			$resposta['code'] = 'erro';
			$resposta['data'] = "A soma do frete dos produtos é diferente do valor do frete no cabeçalho do orçamento. <p>Para corrigir clique no botão \"Atualizar\" em seguida a opção \"Atualizar preço tabela\".";
			break;
		}


		if(!$gerarPedido && $g['C031']['verificaAlcadaOrcamentoEmailImpressao'] == 'N'){
			$resposta['code'] = true;
		} else if ($mT003['T003_Flag_Status_Orcamento'] == 'L') {
			$resposta['code'] = 'analise';
			$resposta['data'] = "Este orçamento está em análise.";
		} else {
			if(!$VEN001->ven001TriggerPersonalizadaPularVerificacaoAlcada($T003_Id, false))
			{
				$retorno = $VEN001->verificaAlcadaNovo($T003_Id, false);
			}

			$T003=mysql_query("SELECT T003_Flag_Status_Liberacao, T003_D024_Id FROM T003 WHERE T003_Id='{$T003_Id}'");
			$mT003=mysql_fetch_array($T003);

			if($mT003['T003_D024_Id'] == $g['C020']['C020_Cupom_Fiscal_Cliente']) {
				$mT003['T003_Flag_Status_Liberacao'] = 1;
			}

			if($mT003['T003_Flag_Status_Liberacao'] == 4){

				$motivo_Liberacao = mysql_query("SELECT T003_Motivo_Pedido_Liberacao FROM T003 WHERE T003_Id='{$T003_Id}'");
				$mMotivo_Liberacao = mysql_fetch_array($motivo_Liberacao);
				if(!empty($mMotivo_Liberacao['T003_Motivo_Pedido_Liberacao'])){
					$msg ="<br />".$mMotivo_Liberacao['T003_Motivo_Pedido_Liberacao'];
				}
				$msg="A liberação deste orçamento exige maior alçada, pedir liberação? {$msg}";
			}

			if($mT003['T003_Flag_Status_Liberacao'] != 4) {
				$resposta['code'] = true;
				$resposta['data'] = "Você tem certeza que deseja gerar o pedido?";

				if(!empty($g['C031']['horarioLimiteGeracaoPedido']) && $g['C031']['horarioLimiteGeracaoPedido'] != '00:00' && strtotime(date("H:i")) > strtotime($g['C031']['horarioLimiteGeracaoPedido'])){
					$resposta['code'] = 'erro';
					$resposta['data'] = "O horário para geração de novos pedidos ultrapassou o horário permitido ({$g['C031']['horarioLimiteGeracaoPedido']}).";
				}
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $msg;
			}
		}
	break;

	case 'excluirItensMarcados':
		
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$VEN001->gerarAtividadeItemExlusao($T003_Id);
		
		$sql = mysql_query("SELECT T004_Id,
								   D006_Flag_Destaca_Desconto_NF 
							  FROM T004 
						 LEFT JOIN D006 ON D006_Id = T004_D006_Id 
							 WHERE T004_T003_Id = '{$T003_Id}'
							   AND T004_Flag_Excluir = 1");
		
		$atualizarDescontoAposExclusao = 'N';
		while($row = mysql_fetch_Array($sql)){

			if($row['D006_Flag_Destaca_Desconto_NF'] == 'S'){
				$atualizarDescontoAposExclusao = 'S';
			}

			mysql_query("DELETE FROM T004A WHERE T004A_T004_Id = '{$row['T004_Id']}'");
			mysql_query("DELETE FROM T004 WHERE T004_Id = '{$row['T004_Id']}' and T004_Flag_Excluir = 1");
		}

		if($atualizarDescontoAposExclusao == 'S'){
			// Se a CFOP aplica desconto no total precisa recalcular o desconto no total
            // Esse desconto é feito pela soma dos produtos                                                            
            $VEN001->aplicaDescontoCFOP('T004', 'T003', 0, $T003_Id, 'S');	
		}

		if(mysql_num_rows($sql) > 0){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum item selecionado!";
		}
	break;

	case 'atualizarCFOP':
		require_once("bibliotecas/classes/VEN001.php");
		$resposta['code'] = true;
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;

		$cmysql_T003 = mysql_query("SELECT T006_Id FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id='{$T003_Id}' AND T006_Id>0");
        if (mysql_num_rows($cmysql_T003) > 0) {
        	$resposta['code'] = false;
            $resposta['data'] = "Este orçamento já foi faturado, não é permitido mudar o CFOP";
        }

        if($resposta['code']){
			$VEN001 = new VEN001();
			$retorno = $VEN001->ven001atualizarCFOP($T003_Id);
			
			if ($retorno === true) {
				$resposta['data'] = "Atualizado!";
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}
	break;

	case 'atualizarDadosServicos':
		global $g;
		$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
		$D112_Id = mysql_real_escape_string($D112_Id);

		$sql = "SELECT * FROM D112 LEFT JOIN D110 ON D112_D110_Id = D110_Id LEFT JOIN T212 ON T212_Id = D112_T212_Id WHERE D112_Id = '{$D112_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
		$teste = strtotime($row['D112_Tempo_Estimado']);
		$teste = date("H:i", $teste);
		$row['D112_Tempo_Estimado'] = $teste;

		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'carregaTipoTransp':
		global $g;
		
		$D022_Id = isset($r_D022_Id) ? $r_D022_Id : false;
		$sql = "SELECT D022_D036_Id FROM D022 WHERE D022_Id=" . $D022_Id;
		$result = mysql_query($sql);
		$aD022 = mysql_fetch_assoc($result);

		$resposta['code'] = true;
		$resposta['data']['T003_D036_Id'] = $aD022['D022_D036_Id'];
	break;

	case 'ven001VerificarMoedaImpressao':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$sql = mysql_query("SELECT D044_Id, D044_Moeda, T003_Cotacao_Moeda FROM T003 LEFT JOIN D044 ON D044_Id=T003_D044_Id WHERE T003_Id = '{$T003_Id}'");
		$row = mysql_fetch_array($sql);

		if($row['D044_Id'] == '1'){
			$resposta['code'] = true;
		} else if($row['T003_Cotacao_Moeda'] <= 0){
			$resposta['code'] = false;
			$resposta['data'] = 'A cotação da moeda estrangeira está em branco, verifique a cotação da moeda na aba "Moeda".';
		} else {
			$resposta['code'] = 'verificarMoeda';
			$resposta['moeda'] = $row['D044_Moeda'];
			$resposta['data'] = $row['D044_Id'];
		}
	break;

	case 'ven001VerificarEstoqueOrcamento':

		if($g['C031']['informarEstoqueInsuficiente'] == 'N'){
			$resposta['code'] = true;
		} else {
			$D009_Id = $r_D009_Id;
			$T066_Id = $r_T066_Id;
			$T004_Quantidade = gCorrigeNumeroInverte($r_T004_Quantidade);
			$sqlD009 = mysql_query("SELECT D009_Quantidade_Estoque_Liquido, 
										   D037_Unidade,
										   T066_Quantidade_Estoque_Liquido,
										   D004_Local 
									  FROM D009 
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
								 LEFT JOIN D001 ON D001_Id = D049_D001_Id 
								 LEFT JOIN D037 ON D037_Id = D001_D037_Id
								 LEFT JOIN T066 ON T066_Id = '{$T066_Id}'
								 LEFT JOIN D004 ON D004_Id = T066_D004_Id
									 WHERE D009_Id = '{$D009_Id}'");
	
			$rowD009 = mysql_fetch_array($sqlD009);
	
			if($T066_Id > 0){
				
				if($rowD009['T066_Quantidade_Estoque_Liquido'] < $T004_Quantidade){
					$rowD009['T066_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009['T066_Quantidade_Estoque_Liquido']);
					$resposta['data'] = "Estoque insuficiente na locação {$rowD009['D004_Local']}, disponível {$rowD009['T066_Quantidade_Estoque_Liquido']} {$rowD009['D037_Unidade']}";
					$resposta['code'] = false;
				} else {
					if($rowD009['D009_Quantidade_Estoque_Liquido'] < $T004_Quantidade){
						$rowD009['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009['D009_Quantidade_Estoque_Liquido']);
						$resposta['data'] = "Estoque insuficiente, disponível {$rowD009['D009_Quantidade_Estoque_Liquido']} {$rowD009['D037_Unidade']}";
						$resposta['code'] = false;
					} else {
						$resposta['code'] = true;
					}
				}
			} else {
	
				if($rowD009['D009_Quantidade_Estoque_Liquido'] < $T004_Quantidade){
					$rowD009['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($rowD009['D009_Quantidade_Estoque_Liquido']);
					$resposta['data'] = "Estoque insuficiente, disponível {$rowD009['D009_Quantidade_Estoque_Liquido']} {$rowD009['D037_Unidade']}";
					$resposta['code'] = false;
				} else {
					$resposta['code'] = true;
				}
			}			
		}
	break;

	case 'ven001VerificarLocacaoOrcamento':

		if($g['C031']['obrigatorioLocacaoItemComValidade'] == 'N'){
			$resposta['code'] = true;
		} else {
			$D009_Id = $r_D009_Id;
			$T066_Id = $r_T066_Id;
			$sqlD009 = mysql_query("SELECT D001A_Flag_Validade
									  FROM D009 
								 LEFT JOIN D049 ON D049_Id  = D009_D049_Id
								 LEFT JOIN D001 ON D001_Id  = D049_D001_Id 
								 LEFT JOIN D001A ON D001_Id = D001A_D001_Id
									 WHERE D009_Id = '{$D009_Id}'");
	
			$rowD009 = mysql_fetch_array($sqlD009);
			
			if($T066_Id <= 0 && $rowD009['D001A_Flag_Validade'] == 'S' && $g['C031']['obrigatorioLocacaoItemComValidade'] == 'S'){
				$resposta['data'] = "Obrigatório selecionar uma locação, item possui controle de validade";
				$resposta['code'] = false;
			} else {
				$resposta['code'] = true;
			}
		}
	break;

	case 'atualizarPrecoTabela':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;

		$resposta = $VEN001->atualizarPrecoTabela($T003_Id);

	break;

	// Conferido
	case 'verificaPermissaoEnviarEmail':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
					//OK
		$resultado = $VEN001->PersonalizacaoTriggerVerificaItemOrcamentoEmail($T003_Id);
		if($resultado === true){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $resultado;
		}
	break;	

	// Conferido
	case 'verificacaoGenericaImpressao':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
					//Ok
		$resultado = $VEN001->PersonalizacaoTriggerVerificaItemOrcamentoImprimir($T003_Id);
		if($resultado === true){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $resultado;
		}
	break;
	
	case 'campoProcuraProdutoAssociarItemOrc':
		$D009_Id= isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;

		$sql = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D009_Id 
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

	case 'gatilhoPrecoOrcamentoPorQtd':
		// Pode ser personalizado conforme necessidade do cliente	
		$resposta['code'] = false;
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
