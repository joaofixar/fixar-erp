<?php
namespace hardness;

global $g;

/*
# BLOCO 1 # INFORMAÇÕES PRINCIPAIS #
DESCRIÇÃO 												REG.  NIVEL  OC.
Abertura do Bloco 										1001 	1 	  1
Obrigatoriedade de registros do Bloco 1 				1010 	2 	  1
Operações com instrumentos de pagamentos eletrônicos 	1601 	2 	  V
Encerramento do Bloco 1 								1990 	1 	  1
*/
$contador_bloco_1 = 1;


$movimentacao = mysql_query("SELECT COUNT(*) as num_rows
								FROM T007
									LEFT JOIN D027 ON D027_Id = T007_D027_Id  									
								WHERE T007_Data_Emissao >= '{$dataInicial}' 
											AND T007_Data_Emissao <= '{$dataFinal}'
											AND T007_C004_Id = '{$g['empresaAtual']}'
											AND T007_Flag_ACP IN('6', '8')
											AND (D027_Tipo_Forma_Pagamento_NFCE IN ('03', '04', '15', '16', '17', '18', '19'))
											AND T007_Flag_Cancelada != 'S'
											AND T007_Numero_Protocolo_Nfe > 0");

$mMovimentacao = mysql_fetch_array($movimentacao);

$INV_MOV = $mMovimentacao['num_rows'] > 0 ? 0: 1;


$R1001 = array(
	$REG = '1001',
	$IND_MOV
);
$R1001 = implode("|", $R1001);
$layout .= '|' . $R1001 . '|' . _SALTO_LINHA;
$contador_bloco_1++;

if($IND_MOV == 0) {	
	$meiosEletronicos = mysql_query("SELECT	instituicao_pagamento.D024_Id AS instituicao_pagamento,
										    intermediador_transacao.D024_Id AS intermediador_transacao,
										SUM(IFNULL(T008_Valor_Total_Preco,0) +               
											IFNULL(T008_Valor_Total_Despesas_Acessorias,0) +
											IFNULL(T008_Valor_ICMS_Substituicao_Retencao,0) +
											IFNULL(T008_Valor_IPI,0) +
											IFNULL(T008_Valor_IPI_Devolucao,0) +
											IFNULL(T008_Valor_Total_Seguro,0) +
											IFNULL(T008_Valor_FCP_ST,0) +
											IFNULL(T008_Valor_Total_Frete,0) -
											IFNULL(T008_Valor_Total_Desconto,0) -
											IFNULL(IF(D006_Deduz_ICMS_Desonerado_Valor_Total = 'S',T008_Valor_ICMS_Desonerado,0),0)) AS TOT_VS,
											0 AS TOT_OUTROS
									   FROM T008
								  LEFT JOIN T007 ON T007_Id = T008_T007_Id
								  LEFT JOIN D027 ON D027_Id = T007_D027_Id
								  LEFT JOIN D024 AS instituicao_pagamento ON instituicao_pagamento.D024_Cnpj = IF(D027_Cnpj_Instituicao_Pagamento = '',0,D027_Cnpj_Instituicao_Pagamento)
								  LEFT JOIN D024 AS intermediador_transacao ON intermediador_transacao.D024_Cnpj = IF(D027_Cnpj_Intermediador_Transacao = '',0,D027_Cnpj_Intermediador_Transacao)
								  LEFT JOIN D006 ON D006_Id = T008_D006_Id
									  WHERE T007_Data_Emissao >= '{$dataInicial}' 
									    AND T007_Data_Emissao <= '{$dataFinal}'
									    AND T007_C004_Id = '{$g['empresaAtual']}'
									    AND T007_Flag_ACP IN('6', '8')
									    AND (D027_Tipo_Forma_Pagamento_NFCE IN ('03', '04', '15', '16', '17', '18', '19'))
									    AND T007_Flag_Cancelada != 'S'
									    AND T007_Numero_Protocolo_Nfe > 0
										AND instituicao_pagamento.D024_Id IS NOT NULL
										AND intermediador_transacao.D024_Id IS NOT NULL
								  GROUP BY D027_Tipo_Forma_Pagamento_NFCE, instituicao_pagamento, intermediador_transacao");

	$vendasMeiosEletronicos = mysql_num_rows($meiosEletronicos);	
	$IND_CART = ($vendasMeiosEletronicos > 0) ? 'S' : 'N';

	$R1010 = array(								#TAM 	   DESCRIÇÃO
		$REG = '1010',
		$IND_EXP = 'N', 						#001* 	   Reg 1100 - Ocorreu averbação (conclusão) de exportação no período: (S – Sim / N - Não)
		$IND_CCRF = 'N',       					#001*	   Reg 1200 – Existem informações acerca de créditos de ICMS a serem controlados, definidos pela Sefaz: (S – Sim / N - Não)
		$IND_COMB = 'N', 		       			#001*	   Reg 1300 – É comercio varejista de combustíveis com movimentação e/ou estoque no período: (S – Sim / N - Não)
		$IND_USINA = 'N',       				#001*	   Reg 1390 – Usinas de açúcar e/álcool – O estabelecimento é produtor de açúcar e/ou álcool carburante com movimentação e/ou estoque no período: (S – Sim / N - Não)
		$IND_VA = 'N',       					#001*	   Reg 1400 – Sendo o registro obrigatório em sua Unidade de Federação, existem informações a serem prestadas neste registro: (S – Sim; / N - Não)
		$IND_EE = 'N',       					#001*	   Reg 1500 - A empresa é distribuidora de energia e ocorreu fornecimento de energia elétrica para consumidores de outra UF: (S – Sim; / N - Não)
		$IND_CART,		       					#001*	   Reg 1601 - Realizou vendas com instrumentos eletrônicos de pagamento
		$IND_FORM = 'N',       					#001*	   Reg 1700 – Foram emitidos documentos fiscais em papel no período em unidade da federação que exija o controle de utilização de documentos fiscais: (S – Sim / N - Não)
		$IND_AER = 'N',       					#001*	   Reg 1800 – A empresa prestou serviços de transporte aéreo de cargas e de passageiros: (S – Sim / N - Não)
		$IND_GIAF1 = 'N',       				#001*	   Reg 1960 - Possui informações GIAF1?: (S – Sim / N - Não)
		$IND_GIAF1 = 'N',       				#001*	   Reg 1970 - Possui informações GIAF3?: (S – Sim / N - Não)
		$IND_GIAF4 = 'N',      					#001*	   Reg 1980 - Possui informações GIAF4?: (S – Sim / N - Não)
		$IND_REST_RESSARC_COMPL_ICMS = 'N'		#001*	   Reg 1980 - Reg. 1250 – Possui informações consolidadas de saldos de restituição, ressarcimento e complementação do ICMS?: (S – Sim / N - Não)
	);
	$R1010 = implode("|", $R1010);
	$layout .= '|' . $R1010 . '|' . _SALTO_LINHA;
	$contador_bloco_1++;


	if($IND_CART == 'S')	 {

		while($tipoPagamento = mysql_fetch_array($meiosEletronicos)) {

			$R1601 = array(																	#TAM       DESCRIÇÃO
				$REG = '1601',																#004	   #Texto fixo contendo "1601"
				$COD_PART_IP = $tipoPagamento['instituicao_pagamento'],
				$COD_PART_IT = $tipoPagamento['intermediador_transacao'],
				$TOT_VS = formataDecimal($tipoPagamento['TOT_VS']),
				$TOT_ISS = '0,00',
				$TOT_OUTROS = formataDecimal($tipoPagamento['TOT_OUTROS'])
			);

			$R1601 = implode("|", $R1601);
			$layout .= '|' . $R1601 . '|' . _SALTO_LINHA;
			$contador_bloco_1++;
		}
	}

}

# REGISTRO 0990
$R1990 = array(																#TAM
	$REG = '1990',															#004	
	$QTD_LIN_1 = $contador_bloco_1											#-		Quantidade total de linhas do Bloco 0
);
$R1990 = implode("|", $R1990);
$layout .= '|' . $R1990 . '|' . _SALTO_LINHA;

$linhas_bloco_1 = $contador_bloco_1;
