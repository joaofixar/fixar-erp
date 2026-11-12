<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {

    case 'excluirNotaPDV':
		global $g;
		
		require_once("bibliotecas/classes/VEN003.php");

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		$VEN003 = new VEN003();
		$retorno = $VEN003->ven003excluirNota($T007_Id, true);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCamposProdutosPDV':
		global $g;
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
	
		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
	
		$sql =  "SELECT T007_D024_Id,
						T007_D006_Id,
						T007_Flag_Destino_Produto,
						T007_C004_Id,
						T007_C004_Id_Estoque
				   FROM T007 
				  WHERE T007_Id = '{$T007_Id}'";
	
		$resultado = mysql_query($sql);
		$mT007       = mysql_fetch_assoc($resultado);
		
		$T007_D024_Id              = $mT007['T007_D024_Id'];
		$T007_D006_Id              = $mT007['T007_D006_Id'];
		$T007_Flag_Destino_Produto = $mT007['T007_Flag_Destino_Produto'];
	
	/*     $sql = <<<EOT
		SELECT  *
			  from D001
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T007_D024_Id']}'
			left join D082 on D082_Id=D049_D082_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D005 on D005_Id=D001_D005_Id
			left join D069 on D069_Id=D009_D069_Id
			left join D070 on D070_Id=D069_D070_Id
			WHERE D009_Id = '{$D009_Id}'
	EOT; */
	
		$sql = <<<EOT
			 SELECT D009_Id,
					D009_Origem_Mercadoria,
					D049_Origem_Mercadoria,
					D009_Quantidade_Estoque_Liquido,
					D009_Quantidade_Estoque_Loja,
					D009_Quantidade_OC,
					D009_Quantidade_Estoque_Fora,
					D070_Data_Vencimento,
					D009_Preco_Loja,
					D009_Data_Alteracao_Custo,
					D009_Data_Ultima_Venda,
					D009_Flag_Custo_Utilizado,
					D001_Descricao_Produto,
					D001_Codigo_Produto,
					D001_D037_Id,
					D001_D005_Id,
					D001_Id
			   FROM D001
		  LEFT JOIN D049 on D049_D001_Id=D001_Id
		  LEFT JOIN D009 on D009_D049_Id=D049_Id
		  LEFT JOIN D069 on D069_Id=D009_D069_Id
		  LEFT JOIN D070 on D070_Id=D069_D070_Id
			  WHERE D009_Id = '{$D009_Id}'
EOT;
	
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
	
		if($row['D009_Origem_Mercadoria'] != ''){
			$row['D049_Origem_Mercadoria'] = $row['D009_Origem_Mercadoria'];
		}
	
		$row['estoqueLiquido']  = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['estoqueLiquido2'] = $row['D009_Quantidade_Estoque_Liquido'];
		$row['estoqueLoja']     = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
		$row['estoqueTotal']    = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']+$row['D009_Quantidade_Estoque_Loja']);
		$row['ordemCompra']     = gCorrigeNumero($row['D009_Quantidade_OC']);
		$row['estoqueFora']     = gCorrigeNumero($row['D009_Quantidade_Estoque_Fora']);
	
		$row['validadePrecoTabela'] = 'true';
		if(strtotime($row['D070_Data_Vencimento']) < strtotime(date("Y-m-d"))){
			$row['validadePrecoTabela'] = 'false';
		}
	
	/*  Utilizando a query do inicio
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
						T007_Flag_Destino_Produto";
		$cClausula.=" from T007 ";
		$cClausula.=" left join D024 on D024_Id=T007_D024_Id ";
		$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
		$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T007_C004_Id ";
		$cClausula.=" left join C004 on C004_Id=T007_C004_Id ";
		$cClausula.=" where T007_Id='{$T007_Id}'";
		$T007=mysql_query($cClausula);
		$cErro=mysql_error();
		if (!empty($cErro)) {
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $cErro";
			break;
		}
		$mT007=mysql_fetch_array($T007); */
	
		$row['T007_Flag_Destino_Produto'] = $mT007['T007_Flag_Destino_Produto'];
	
		$cfop = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $row['D009_Id'], $mT007['T007_C004_Id'], $T007_Flag_Destino_Produto, false, false, false, $mT007['T007_C004_Id_Estoque']);
		
		//Melhorar desempenho inclusão item PDV, já chama no formulario após inclusão
		//$aliquotas = $VEN001->retornaAliquotas($D009_Id, false, false, $T007_Id, false, false, false, $cfop);
		// verifica se está setado a flag lista preço do cabeçalho do orçamento, se estiver, manda ela, senão manda o id do cliente
	
		 //$preco = $CAD002->retornaPrecoVenda($row['D009_Id'], $mT007['T007_Flag_Lista_Preco'], $mT007['T007_D024_Id'], $mT007['T007_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], 0, 0, $mT007['T007_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		//$precoTabela = $CAD002->cad002RetornaPrecoTabela($row['D009_Id'], $mT007['T007_Flag_Lista_Preco'], $mT007['T007_D024_Id'], $mT007['T007_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI']);
		
		$preco[0]       = gCorrigeNumero($row['D009_Preco_Loja']);
		$precoTabela[0] = gCorrigeNumero($row['D009_Preco_Loja']);
		
		$row['precoTabela']   = $precoTabela[0];
		$row['Preco_Produto'] = $preco[0];
	
		//$precoCalc			   = gCorrigeNumeroInverte($preco[0]);
		//$percentualPrecoMinimo = $g['C031']['percentualPrecoMinimoVenda']/100;
		//$row['PrecoMinimo']    = gCorrigeNumero($precoCalc - ($precoCalc*$percentualPrecoMinimo), 4);
	
		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		//$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT007['T007_D024_Id'], $row['D009_Id'], $T007_Id);
		//$row['Aliquota_IPI'] 	 = $aliquotas['Aliquota_ICMS'];
		$row['Aliquota_IPI'] 	 = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');
		$row['D006_Id'] 		 = $cfop;
	
		if($row['D006_Id'] <= 0 || $row['D006_Id'] === false){
			$row['D006_Id'] = $T007_D006_Id;
		}
	
		//Melhorar desempenho inclusão item PDV
		//require_once('bibliotecas/funcoesCallback/infoCustoVendaProduto.php');
		//$row['infoCustoVenda'] = infoCustoVendaProduto(false, array($row['D009_Data_Alteracao_Custo'], $row['D009_Data_Ultima_Venda'], $row['D009_Flag_Custo_Utilizado']), 'S');
	
		if($g['C031']['utilizarDescProdutoUltimaVenda'] == 'S'){
			$sqlT007 = mysql_query("SELECT T008_Descricao_Produto FROM T008 LEFT JOIN T007 ON T007_Id = T008_T007_Id WHERE T007_D024_Id ='{$T007_D024_Id}' AND T008_D009_Id = '{$row['D009_Id']}' ORDER BY T008_Id DESC LIMIT 1");
			if(mysql_num_rows($sqlT007) > 0){
				$resT007 = mysql_fetch_array($sqlT007);
				if($row['D001_Descricao_Produto'] != $resT007['T008_Descricao_Produto']){
					$row['D001_Descricao_Produto'] = $resT007['T008_Descricao_Produto'];
				}
			}
		}
	
		$row['campoFocus'] = $g['C031']['editarDescricaoProdutoVenda']=='S' ? 'T008_Descricao_Produto' : 'T008_Quantidade';
		if (!empty($row)) {
			$resposta['code'] = true;

			$params = array(
				"T007_Id" => $T007_Id,
				"D009_Id" => $D009_Id,
			);
			$row = $VEN001->ven001ModPreencheFormProdutosNota($row);
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'Ven015finalizarNotaFiscalPDV':
		global $g;
		
		mysql_query("delete from T118 where T118_T007_Id='{$r_T007_Id}'");

		// funcao personalizada no APP Integracao Pagamento POS
		// Felipe 18/11/2022
		require_once("bibliotecas/classes/VEN015.php");
		$VEN015 = new VEN015();
		$T292 = $VEN015->ven015IncluirParcelasNotaFiscal($r_T007_Id);

		if($T292 == ''){
				$T292 = mysql_query("select T292_D027_Id, 
										    T292_Valor_Recebido, 
										    T292_Quantidade_Parcelas,
										    D027_Tipo_Forma_Pagamento_NFCE,
											D027_Baixa_Automatica_Dias
								       from T292 
							      left join D027 on D027_Id = T292_D027_Id
								      where T292_T007_Id='{$r_T007_Id}'
									    and T292_Valor_Recebido>0");

		} 

		$totalParcelas    = 0;
		$numeroParcela    = 1;
		$portadorDinheiro = 'N';
		
		while ($mT292 = mysql_fetch_assoc($T292)) {
			
			$valorParcela  	   = $mT292['T292_Valor_Recebido'] / $mT292['T292_Quantidade_Parcelas'];
			$vencimentoParcela = date('Y-m-d');
			$prazo   		   = 0;

			if($g['C031']['prazoVencimentoParcelaPdvPortador'] == 'S' && $mT292['D027_Baixa_Automatica_Dias'] > 0){
				$dataVencimento      = date('Y-m-d', strtotime('+'.$mT292['D027_Baixa_Automatica_Dias']. 'days', strtotime($vencimentoParcela)));
				$vencimentoParcela   = $dataVencimento;
				$prazo			     = $mT292['D027_Baixa_Automatica_Dias'];
			}


			if($mT292['D027_Tipo_Forma_Pagamento_NFCE'] == '01'){
				$portadorDinheiro = 'S';
			}
			
			for ($parcela=1; $parcela<=$mT292['T292_Quantidade_Parcelas']; $parcela++) {
				mysql_query("insert into T118 
									(T118_T007_Id,
									T118_D027_Id, 
									T118_Valor_Parcela, 
									T118_Data_Vencimento, 
									T118_Prazos_Dias,
									T118_Numero_Parcela) 
							values ('{$r_T007_Id}',
									'{$mT292['T292_D027_Id']}',
									'$valorParcela',
									'$vencimentoParcela',
									'$prazo',
									'$numeroParcela')");

				$T118_Id           = $g['mysqlLastId'];
				$prazo            += 30;
				$vencimentoParcela = date('Y-m-d', strtotime("+{$prazo} days",strtotime($vencimentoParcela)));
				$totalParcelas    += round($valorParcela,2);
				$numeroParcela++;
				
		        // Funcao personalizada no APP INTEGRACAO PAGAMENTO POS
				$VEN015->vinculaPagamentoNotaPOS($mT292['IPP004_Id'], $T118_Id, $r_T007_Id);		
				
			}
		}

		$T007 = mysql_query("SELECT T007_Valor_Total
							   FROM T007
							  WHERE T007_Id = '{$r_T007_Id}'");
		$mT007 = mysql_fetch_assoc($T007);

		// Verificar se tem diferença na parcela para ajustar o valor da ultima parcela
		if((round($mT007['T007_Valor_Total'],2) != round($totalParcelas,2)) && $portadorDinheiro == 'N'){
			$diferencaParcela  = round($mT007['T007_Valor_Total'] - $totalParcelas,2);
			mysql_query("UPDATE T118 SET T118_Valor_Parcela = T118_Valor_Parcela + '{$diferencaParcela}' WHERE T118_Id = '{$T118_Id}'");
		}

		$resposta['code'] = false;
		$resposta['data'] = 'teste';

	break;	

	case 'ven015aplicarDescontoPDV':
		global $g;

		$T007_Id       = isset($r_T007_Id) ? $r_T007_Id : false;
		$valorDesconto = isset($r_valorDesconto) ? $r_valorDesconto : false;
		$valorDesconto = gCorrigeNumeroInverte($valorDesconto);

		$T007 = mysql_query("SELECT T007_Valor_Total_Produtos_Sem_Desconto,
                                    T007_Valor_Total_Produtos
                               FROM T007
                              WHERE T007_Id='{$T007_Id}'");
        $mT007 = mysql_fetch_array($T007);

        //Verifica qual o percentual com o valor do desconto aplicado
        $percentualDesconto = (($valorDesconto / $mT007['T007_Valor_Total_Produtos']) * 100);
        
		//Verifica se ja existe desconto aplicado nos produtos 
        $percentualDescontoTabela = ((($mT007['T007_Valor_Total_Produtos_Sem_Desconto']- $mT007['T007_Valor_Total_Produtos']) / $mT007['T007_Valor_Total_Produtos_Sem_Desconto']) * 100);

        $percentualDesconto +=$percentualDescontoTabela;
		$percentualDesconto = number_format($percentualDesconto,2);
		$erro = '';
        if ($percentualDesconto > $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista']) {
            require_once('prog/sistema/funcoes/autoriza.php');
            $autorizado = validaAutorizacao("T007", $T007_Id, $percentualDesconto);
            if ($autorizado !== true) {
				$percentual   = gCorrigeNumero($percentualDesconto);
				$valorDesconto = gCorrigeNumero($valorDesconto);
				$percentualPermitido = gCorrigeNumero($g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisorAVista']);
                $erro = "O desconto de R$ {$valorDesconto} ({$percentual}%) e é maior que {$percentualPermitido}% liberado";
            }
        }

		if (empty($erro)) {
			mysql_query("UPDATE T007 SET T007_Valor_Desconto = '{$valorDesconto}' WHERE T007_Id = '{$T007_Id}'");
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			$percentualDesconto = gCorrigeNumero($percentualDesconto);
			$resposta['code'] = false;
			$resposta['data'] = $erro;
			$resposta['percentualDesconto'] = $percentualDesconto;
		}
	break;	

	case 'verificaAbrirGaveta':
		global $g;

			$retorno = true;
			// Supervisor e diretor tem permissão para abrir gaveta
			if($g['nivelAlcada'] < 2){
				$retorno = false;
			}
			
			if($retorno === true){
				$resposta['code'] = true;
				$resposta['C007_Id'] = $g['usuarioAtual'];
			}else{
				$resposta['code'] = false;
				$resposta['data'] = 'Você não possue autorização para abrir gaveta, solicitar liberação?';
			}
	break;	

	case 'ven015VerificaAbrirNotaCompleta':
		global $g;

			$T007_Id = $r_T007_Id;
			$T007 = mysql_query("SELECT T007_D024_Id,
										D024_Nome_Empresa
								   FROM T007
							  LEFT JOIN D024 ON D024_Id = T007_D024_Id
								  WHERE T007_Id = '{$T007_Id}'");
			
			$mT007 = mysql_fetch_array($T007);
			
			$retorno = true;
			if($g['C031']['codigoClienteCupomFiscal'] == $mT007['T007_D024_Id'] && $g['C031']['permiteAbrirNFCompletaComClientePadraoPDV'] == 'N'){
				$retorno = false;
			}
			
			if($retorno === true){
				$resposta['code'] = true;
			}else{
				log("retornou false agora");
				$resposta['code'] = false;
				$resposta['data'] = "Não é permitido abrir nota completa com o cliente {$mT007['D024_Nome_Empresa']}";
			}
	break;	

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
