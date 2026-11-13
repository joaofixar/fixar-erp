<?php
namespace hardness;
global $g;
/*
# BLOCO C # INFORMAÇÕES PRINCIPAIS #
REG 	 OBRIG		DESCRIÇÃO
C001	 O			Abertura do Bloco C
C010	 O			Identificação do Estabelecimento
C100	 OC			Documento - Nota Fiscal (código 01), Nota Fiscal Avulsa (código 1B), Nota Fiscal de Produtor (código 04) e NF-e (código 55)
C110	 OC			Complemento de Documento – Informação Complementar da Nota Fiscal (códigos 01, 1B, 04 e 55)
C111	 OC			Processo Referenciado
C112	 			Complemento de Documento - Documento de Arrecadação Referenciado 
C113	 			Complemento de Documento - Documento Fiscal Referenciado
C130	 			Complemento de Documento - ISSQN , IRRF e Previdência Social
C170	 O(C100)	Complemento de Documento – Itens do Documento (códigos 01, 1B, 04 e 55)
C175	 O(C100)	Registro Analítico do Documento (código 65)
C176	 			Complemento de Item -Ressarcimento de ICMS em operações com Substituição Tributária (código 01,55)
C190	 OC			Consolidação de Notas Fiscais Eletrônicas (Código 55) – Operações de Aquisição com Direito a Crédito, e Operações de Devolução de Compras e Vendas.
C191	 O(C190)	Detalhamento da Consolidação – Operações de Aquisição com Direito a Crédito, e Operações de Devolução de Compras e Vendas – PIS/PASEP
C195	 O(C190)	Detalhamento da Consolidação - Operações de Aquisição com Direito a Crédito, e Operações de Devolução de Compras e Vendas – COFINS
C197	 			Outras Obrigações Tributárias , Ajustes e Informações provenientes de Documento Fiscal
C198	 OC			Processo Referenciado
C199	 OC			Complemento de Documento – Operações de Importação (código 55)
C990	 O			Encerramento do Bloco C
*/

$notasMovimentacoes = mysql_query("SELECT 
							Id,
							C004_Id,
							COUNT(*) AS Movimentacoes
					FROM (						
							SELECT 	
								T007_Id AS Id,
								T007_C004_Id AS C004_Id
							FROM T007
								LEFT JOIN D006 ON D006_Id = T007_D006_Id
								LEFT JOIN T005 ON T005_Id = T007_T005_Id
								LEFT JOIN T006 ON T005_Id = T006_T005_Id
								LEFT JOIN T007A ON T007_Id = T007A_T007_Id						
							WHERE T007_Data_Emissao >= '{$dataInicial}' 
							AND T007_Data_Emissao <= '{$dataFinal}' 
							AND T007_C004_Id IN ($matrizEFiliais)
							AND T007_Flag_Cancelada != 'S' 
							AND T007_Numero_Protocolo_Nfe > 0
							AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
						UNION
						SELECT 
								T013_Id AS Id,
								T013_C004_Id AS C004_Id
							FROM T013
							LEFT JOIN T013A ON T013_Id = T013A_T013_Id
						WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
							AND {$dataNFRecebida} <= '{$dataFinalHora}'
							AND T013_C004_Id IN ($matrizEFiliais)
							AND T013_Flag_Cancelada != 'S' 										
							AND T013_Flag_Estoque_Finalizado = 'S'
							AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo = '5')  
							AND (T013_Valor_Total_PIS > 0 OR T013_Valor_Total_COFINS > 0)
							AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
					) AS notasMovimentacoes
				GROUP BY C004_Id");

// Verifica se existem notas fiscais para popular o Indicador de Movimentação
$IND_MOV = (mysql_num_rows($notasMovimentacoes) > 0) ? '0' : '1';

// Variavel TXT do layout de saída no arquivo
// $layout = '';

# REGISTRO C001
$C001 = array(											#TAM								
	$REG = 'C001',										#004* 
	$IND_MOV											#001*	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
); 
$C001 = str_replace('|', '', $C001);
$C001 = implode("|", $C001);
$layout .= '|' . $C001 . '|' . _SALTO_LINHA;
#-------------------------------------------

if($IND_MOV == '0'){
    // Caso existam notas fiscais
	while($mNotasMovimentacoes = mysql_fetch_array($notasMovimentacoes)) 
	{
		$empresa = $mNotasMovimentacoes['C004_Id'];

		$C004 = mysql_query("SELECT * 
							FROM C004 
							WHERE C004_Id = '{$empresa}'");
		$C004 = mysql_fetch_array($C004);

		# REGISTRO C010
		$C010 = array(											#TAM							
			$REG = 'C010',										#004*
			$CNPJ = removerCharEspecial($C004['C004_Cnpj']),	#014* 	Número de inscrição do estabelecimento no CNPJ.
			$IND_ESCRI = '2'									#001*	Indicador da apuração das contribuições e créditos, na escrituração das operações por NF-e e ECF, no período (1 Consolidação das operações por NF-e (C180 e C190) e por ECF (C490)/2 Individualizado de NF-e (C100 e C170) e de ECF (C400))
		);
		$C010 = str_replace('|', '', $C010);
		$C010 = implode("|", $C010);
		$layout .= '|' . $C010 . '|' . _SALTO_LINHA;
		#-------------------------------------------

		// Buscar todas notas emitidas no período
		$sqlT007 = mysql_query("select D006_Codigo_CFOP,
									D006_Flag_Entrada_Saida,
									T007_Flag_Entrada_Saida,
									T007_Flag_ACP,
									T013_Tipo,
									T007_D024_Id,
									T007_T005_Id,								   
									T007_Numero_Nota_Fiscal,
									T007_Chave_Acesso_Nfe,
									T007_Data_Emissao,
									T007_Data_Saida,
									T007_Valor_Total,
									T007_Valor_Total_Produtos,
									T007_Flag_Frete,
									T007_Valor_Frete,
									T007_Valor_Despesas_Acessorias,
									T007_Valor_Total_Base_ICMS,
									T007_Valor_Total_ICMS,
									T007_Valor_Base_ICMS_Substituicao,
									T007_Valor_ICMS_Substituicao_Retencao,
									T007_Valor_Total_IPI,
									T007_Valor_Total_PIS,
									T007_Valor_Total_COFINS,
									T007_Id,
									T013_Id,
									T007_Flag_Finalidade_NFe
									T007_C004_Id,
									T007_Flag_Cancelada,
									T007_Numero_Protocolo_Nfe,
									Emissao_Propria,
									T007_Valor_Desconto,
									T013A_Flag_Destaca_Frete_SPED
								from (
										select D006_Codigo_CFOP,
												D006_Flag_Entrada_Saida,
												T007_Flag_Entrada_Saida,                  
												T007_Flag_ACP,  
												'' as T013_Tipo,
												T007_D024_Id,                   
												T007_T005_Id,                                     
												T007_Numero_Nota_Fiscal,           
												T007_Chave_Acesso_Nfe,             
												T007_Data_Emissao,                 
												T007_Data_Saida,                   
												T007_Valor_Total,                  
												T007_Valor_Total_Produtos,         
												T007_Flag_Frete,                   
												T007_Valor_Frete,                  
												T007_Valor_Despesas_Acessorias,      
												T007_Valor_Total_Base_ICMS,        
												T007_Valor_Total_ICMS,             
												T007_Valor_Base_ICMS_Substituicao, 
												T007_Valor_ICMS_Substituicao_Retencao,      
												T007_Valor_Total_IPI,              
												T007_Valor_Total_PIS,              
												T007_Valor_Total_COFINS,           
												T007_Id,     
												0 as T013_Id,                      
												T007_Flag_Finalidade_NFe,
												T007_C004_Id,
												T007_Flag_Cancelada,
												T007_Numero_Protocolo_Nfe,
												'0' as Emissao_Propria,
												T007_Valor_Desconto,
												'' as T013A_Flag_Destaca_Frete_SPED
										from T007
									left join D006 ON D006_Id = T007_D006_Id 
									left join T005 ON T005_Id = T007_T005_Id
									left join T006 ON T005_Id = T006_T005_Id
									left join T007A on T007_Id = T007A_T007_Id
										where T007_Data_Emissao >= '{$dataInicial}' 
											and T007_Data_Emissao <= '{$dataFinal}' 
											and T007_C004_Id = '{$empresa}' 
											and T007_Flag_Cancelada != 'S' 
											and T007_Numero_Protocolo_Nfe > 0
											and T007_Flag_ACP IN('6','8') 
											and ((T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0) OR ((D006_Situacao_Tributaria_PIS = 06 OR D006_Situacao_Tributaria_PIS = 08 OR D006_Situacao_Tributaria_PIS = 09) OR (D006_Situacao_Tributaria_COFINS = 06 OR D006_Situacao_Tributaria_COFINS = 08 OR D006_Situacao_Tributaria_COFINS = 09)))
											and IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									group by T007_Id 
										union
										select D006_Codigo_CFOP,
												D006_Flag_Entrada_Saida,
												'E' as T007_Flag_Entrada_Saida,
												'' as T007_Flag_ACP,
												T013_Tipo,
												T013_D024_Id as T007_D024_Id,
												0 as T007_T005_Id,
												T013_Numero_Nota_Fiscal as T007_Numero_Nota_Fiscal, 
												T013_Chave_Acesso_Nfe as T007_Chave_Acesso_Nfe,
												T013_Data_Emissao as T007_Data_Emissao,
												DATE({$dataNFRecebidaLancarSPED}) as T007_Data_Saida,
												T013_Valor_Total as T007_Valor_Total,
												T013_Valor_Total_Produtos as T007_Valor_Total_Produtos,
												T013_Flag_Frete as T007_Flag_Frete,
												T013_Valor_Frete as T007_Valor_Frete,
												T013_Valor_Despesas_Acessorias as T007_Valor_Despesas_Acessorias,
												T013_Valor_Total_Base_ICMS as T007_Valor_Total_Base_ICMS,
												T013_Valor_Total_ICMS as T007_Valor_Total_ICMS,
												T013_Valor_Total_Base_ICMS_Substituicao as T007_Valor_Base_ICMS_Substituicao,
												T013_Valor_ICMS_Substituicao as T007_Valor_ICMS_Substituicao_Retencao,
												T013_Valor_Total_IPI as T007_Valor_Total_IPI,
												T013_Valor_Total_PIS as T007_Valor_Total_PIS,
												T013_Valor_Total_COFINS as T007_Valor_Total_COFINS,
												0 as T007_Id,
												T013_Id,
												T013_Flag_Finalidade_NFe as T007_Flag_Finalidade_NFe,
												T013_C004_Id as T007_C004_Id,
												T013_Flag_Cancelada as T007_Flag_Cancelada,
												T013_Numero_Protocolo_Nfe as T007_Numero_Protocolo_Nfe	,
												'1' as Emissao_Propria,
												T013_Valor_Desconto as T007_Valor_Desconto,
												IFNULL(T013A_Flag_Destaca_Frete_SPED, 'S') as T013A_Flag_Destaca_Frete_SPED
										from T013
									left join D006 ON D006_Id = T013_D006_Id
									left join T013A on T013_Id = T013A_T013_Id 
										where {$dataNFRecebida} >= '{$dataInicialHora}' 
											and {$dataNFRecebida} <= '{$dataFinalHora}' 
											and T013_C004_Id = '{$empresa}' 
											and T013_Flag_Cancelada != 'S' 										
											and T013_Flag_Estoque_Finalizado = 'S'
											and ((T013_Tipo = '1' and T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo = '5')  
											and ((T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0) OR ((D006_Situacao_Tributaria_PIS = 08 OR D006_Situacao_Tributaria_PIS = 09) OR (D006_Situacao_Tributaria_COFINS = 08 OR D006_Situacao_Tributaria_COFINS = 09)))
											and IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
  								       group by T013_Id 
								) as notaFiscal
							order by T007_Data_Emissao");

		while($T007 = mysql_fetch_array($sqlT007)){

			if($C004['C004_Flag_Incidencia_Tributaria'] == '2' && $T007['T007_Flag_Entrada_Saida'] == 'E'){
				//A empresa que apura o imposto na incidência “Cumulativo”, não apropria crédito sobre as entradas. 
				//Por isso, as notas fiscais de entrada não são consideradas na apuração do imposto.
				continue;
			}
			
			$NUMERO_CFOP = removerCharEspecial($T007['D006_Codigo_CFOP']);
			$IND_OPER = ($T007['T007_Flag_Entrada_Saida'] == 'S') ? '1' : '0';
			// $IND_EMIT = ($T007['D006_Flag_Entrada_Saida'] == 'E') ? '1' : '0';
			$IND_EMIT = $T007['Emissao_Propria'];

			//verifica o código no sistema e escreve o modelo de acordo		
			if($T007['T013_Tipo'] == '5') {
				$COD_MOD = '01';
			} else if($T007['T007_Flag_ACP'] == '6' || $T007['T013_Tipo'] == '1')  {
				$COD_MOD = '55';
			} else if($T007['T007_Flag_ACP'] == '8') {
				$COD_MOD = '65';
			}	

			// Busca valor desconto no pedido
			//$T006 = mysql_query("SELECT SUM(T006_Valor_Desconto_Unitario_Bruto) as Desconto FROM T006 WHERE T006_T005_Id = '{$T007['T007_T005_Id']}'");
			//$T006 = mysql_fetch_array($T006);
			//$VL_DESC = (formataDecimal($T006['Desconto']) > 0) ? formataDecimal($T006['Desconto']) : '';
			//--------------------------------
			$VL_DESC = (formataDecimal($T007['T007_Valor_Desconto']) > 0) ? formataDecimal($T007['T007_Valor_Desconto']) : '';

			$IND_FRT= $T007['T007_Flag_Frete'];
			$VL_FRT = formataDecimal($T007['T007_Valor_Frete']);
			if($T007['T013_Id'] > 0 && $T007['T013A_Flag_Destaca_Frete_SPED'] != 'S'){
				$IND_FRT = '9';
				$VL_FRT = '0,00';
			}

			# REGISTRO C100
			$C100 = array(				   													#TAM
				$REG = 'C100',			   													#004						
				$IND_OPER, 				   													#001*	Indicador do tipo de operação(0 Entrada/1 Saída)				
				$IND_EMIT,				   													#001*	Indicador do emitente do documento fiscal (0 Emissão própria/1 Terceiros)				
				$COD_PART = $T007['T007_D024_Id'],											#060	Código do participante (campo 02 do Registro 0150)				
				$COD_MOD,				   													#002*	Código do modelo do documento fiscal, conforme a Tabela 4.1.1 				
				$COD_SIT = '00',		   													#002*	Código da situação do documento fiscal, conforme a Tabela 4.1.2				
				$SER = '',					   												#003	Série do documento fiscal			
				$NUM_DOC = $T007['T007_Numero_Nota_Fiscal'],	   							#009	Número do documento fiscal				
				$CHV_NFE = $T007['T007_Chave_Acesso_Nfe'],		   							#044*	Chave da Nota Fiscal Eletrônica				
				$DT_DOC = formataData(substr($T007['T007_Data_Emissao'],0,10)),							#008*	Data da emissão do documento fiscal 				
				$DT_E_S = formataData($T007['T007_Data_Saida']),							#008*	Data da entrada ou da saída				
				$VL_DOC = formataDecimal($T007['T007_Valor_Total']),						#		Valor total do documento fiscal			
				$IND_PGTO = '2',		   													#001*	Indicador do tipo de pagamento (0 À vista/1 A prazo/2 Outros)			
				$VL_DESC,																	#		Valor total do desconto			
				$VL_ABAT_NT = '',		   													#		Abatimento não tributado e não comercial				
				$VL_MERC = formataDecimal($T007['T007_Valor_Total_Produtos']),				#		Valor total das mercadorias e serviços			
				$IND_FRT,																	#		Indicador do tipo do frete (0 Por conta do emitente/1 Por conta do destinatário/remetente/2 Por conta de terceiros/9 Sem cobrança de frete)
				$VL_FRT,																	#001*	Valor do frete indicado no documento fiscal				
				$VL_SEG = '',				   													#		Valor do seguro indicado no documento fiscal			
				$VL_OUT_DA = formataDecimal($T007['T007_Valor_Despesas_Acessorias']),		#		Valor de outras despesas acessórias			
				$VL_BC_ICMS = formataDecimal($T007['T007_Valor_Total_Base_ICMS']),			#		Valor da base de cálculo do ICMS				
				$VL_ICMS = formataDecimal($T007['T007_Valor_Total_ICMS']),					#		Valor do ICMS			
				$VL_BC_ICMS_ST = formataDecimal($T007['T007_Valor_Base_ICMS_Substituicao']),#		Valor da base de cálculo do ICMS substituição tributária				
				$VL_ICMS_ST = formataDecimal($T007['T007_Valor_ICMS_Substituicao_Retencao']),#		Valor do ICMS retido por substituição tributária				
				$VL_IPI = formataDecimal($T007['T007_Valor_Total_IPI']),					#		Valor total do IPI 			
				$VL_PIS = formataDecimal($T007['T007_Valor_Total_PIS']),					#		Valor total do PIS			
				$VL_COFINS = formataDecimal($T007['T007_Valor_Total_COFINS']),				#		Valor total da COFINS			
				$VL_PIS_ST = '',															#		Valor total do PIS retido por substituição tributária			
				$VL_COFINS_ST = ''															#		Valor total da COFINS retido por substituição tributária				
			);
			$C100 = str_replace('|', '', $C100);
			$C100 = implode("|", $C100);
			$layout .= '|' . $C100 . '|' . _SALTO_LINHA;
			verificaRegistro0150($T007['T007_D024_Id'], $empresa);
			#---------------------------------------------------

			// Verifica se é nota fiscal de Importação
			$digitoCFOP = substr($NUMERO_CFOP, 0, 1);
			if($IND_OPER == '0' && $digitoCFOP == '3'){

				$IMP = mysql_query("SELECT T007_T075_Id, 
										SUM(T008_Valor_COFINS) as COFINS, 
										SUM(T008_Valor_PIS) as PIS, 
										T075_Numero_DI
									FROM T008 
								LEFT JOIN T007 ON T008_T007_Id = T007_Id 
								LEFT JOIN T075 ON T007_T075_Id = T075_Id 
									WHERE T008_T007_Id = '{$T007['T007_Id']}'");

				$IMP = mysql_fetch_array($IMP);

				# REGISTRO C120
				$C120 = array(											#TAM														
					$REG = 'C120',										#004	
					$COD_DOC_IMP = '0',									#001*	Documento de importação (0 Declaração de Importação/1 Declaração Simplificada de Importação)
					$NUM_DOC_IMP = removerChar($IMP['T075_Numero_DI']),	#010	Número do documento de Importação.
					$VL_PIS_IMP = formataDecimal($IMP['PIS']),			#-		Valor pago de PIS na importação
					$VL_COFINS_IMP = formataDecimal($IMP['COFINS']),	#-		Valor pago de COFINS na importação
					$NUM_ACDRAW	= ''									#020	Número do Ato Concessório do regime Drawback
				);		
				$C120 = str_replace('|', '', $C120);
				$C120 = implode("|", $C120);
				$layout .= '|' . $C120 . '|' . _SALTO_LINHA;
				#---------------------------------------------------

			}
			if($COD_MOD != '65') {
				// Busca os produtos da nota para montar o registro C170
				$sqlT008 = mysql_query("select T008_Id,
											T014_Id,
											T008_Situacao_Tributaria_PIS,
											T008_Situacao_Tributaria_COFINS,
											T008_Quantidade,
											T006_Valor_Desconto_Unitario_Bruto,
											T008_Valor_Total_Desconto,
											T008_Flag_Situacao_Tributaria,
											T008_Aliquota_COFINS,
											T008_Valor_COFINS,
											T008_Valor_Base_COFINS,
											T008_Valor_Base_PIS,
											T008_Aliquota_PIS,
											D001_Codigo_Produto,
											D001_Descricao_Produto,
											D037_Unidade,
											T008_Valor_Total_Preco_Sem_Desconto,
											T008_Valor_Base_ICMS,
											T008_Aliquota_ICMS,
											T008_Valor_ICMS,
											T008_Valor_Base_ICMS_Substituicao,
											T008_Aliquota_ICMS_ST,
											T008_Valor_ICMS_Substituicao_Retencao,
											T008_Situacao_Tributaria_IPI,
											T008_Valor_Base_IPI,
											T008_Aliquota_IPI,
											T008_Valor_IPI,
											T008_Valor_PIS,
											T008_T007_Id,
											T014_Flag_Origem_Produto,
											D006_Codigo_CFOP,
											D014_Codigo_Plano_Conta_Fiscal,
											D001_Id,
											D014_Id,
											D006_Id,
											T008A_D014_Id,
											D073_Id,
											D037_Id
										from (
												select  T008_Id,
														0 as T014_Id,
														T008_Situacao_Tributaria_PIS,
														T008_Situacao_Tributaria_COFINS,
														T008_Quantidade,
														T006_Valor_Desconto_Unitario_Bruto,
														T008_Valor_Total_Desconto,
														T008_Flag_Situacao_Tributaria,
														T008_Aliquota_COFINS,
														T008_Valor_COFINS,
														T008_Valor_Base_COFINS,
														T008_Valor_Base_PIS,
														T008_Aliquota_PIS,
														D001_Codigo_Produto,
														D001_Descricao_Produto,
														D037_Unidade,
														T008_Valor_Total_Preco_Sem_Desconto,
														T008_Valor_Base_ICMS,
														T008_Aliquota_ICMS,
														T008_Valor_ICMS,
														T008_Valor_Base_ICMS_Substituicao,
														T008_Aliquota_ICMS_ST,
														T008_Valor_ICMS_Substituicao_Retencao,
														T008_Situacao_Tributaria_IPI,
														T008_Valor_Base_IPI,
														T008_Aliquota_IPI,
														T008_Valor_IPI,
														T008_Valor_PIS,
														T008_T007_Id,
														'' as T014_Flag_Origem_Produto,
														D006_Codigo_CFOP,
														D014_Codigo_Plano_Conta_Fiscal,
														D001_Id,
														D014_Id,
														D006_Id,
														T008A_D014_Id,
														D073_Id,
														D037_Id
												from T008 
											left join D009 on D009_Id = T008_D009_Id
											left join D049 on D049_Id = D009_D049_Id	
											left join D001 on D001_Id = D049_D001_Id
											left join D037 on D037_Id = T008_D037_Id
											left join T007 on T007_Id = T008_T007_Id
											left join T005 on T005_Id = T007_T005_Id
											left join T006 on T005_Id = T006_T005_Id
											left join D059 on D059_Id = T008_D059_Id
											left join D006 on D006_Id = T008_D006_Id
											left join D014 on D014_Id = D006_D014_Id
											left join D073 on D073_Id = D014_D073_Id
											left join T008A on T008_Id = T008A_T008_Id 
												where T008_T007_Id = '{$T007['T007_Id']}'
											group by T008_Id                                   
												union
												select  0 as T008_Id,
														T014_Id,
														T014_Situacao_Tributaria_PIS as T008_Situacao_Tributaria_PIS,                                                
														T014_Situacao_Tributaria_COFINS as T008_Situacao_Tributaria_COFINS,
														T014_Quantidade as T008_Quantidade,
														0 as T006_Valor_Desconto_Unitario_Bruto,
														T014_Valor_Total_Desconto as T008_Valor_Total_Desconto,
														T014_Flag_Situacao_Tributaria as T008_Flag_Situacao_Tributaria,
														T014_Aliquota_COFINS as T008_Aliquota_COFINS,
														T014_Valor_COFINS as T008_Valor_COFINS,
														T014_Valor_Base_COFINS as T008_Valor_Base_COFINS,
														T014_Valor_Base_PIS as T008_Valor_Base_PIS,
														T014_Aliquota_PIS as T008_Aliquota_PIS,
														D001_Codigo_Produto,
														D001_Descricao_Produto,
														D037_Unidade,
														T014_Valor_Total_Custo as T008_Valor_Total_Preco_Sem_Desconto,
														T014_Valor_Base_ICMS as T008_Valor_Base_ICMS,
														T014_Aliquota_ICMS as T008_Aliquota_ICMS,
														T014_Valor_ICMS as T008_Valor_ICMS,
														T014_Valor_Base_ICMS_Substituicao as T008_Valor_Base_ICMS_Substituicao,
														0 as T008_Aliquota_ICMS_ST,
														T014_Valor_ICMS_Substituicao as T008_Valor_ICMS_Substituicao_Retencao,
														T014_Situacao_Tributaria_IPI as T008_Situacao_Tributaria_IPI,
														T014_Valor_Base_IPI as T008_Valor_Base_IPI,
														T014_Aliquota_IPI as T008_Aliquota_IPI,
														T014_Valor_IPI as T008_Valor_IPI,
														T014_Valor_PIS as T008_Valor_PIS,
														T014_T013_Id as T008_T007_Id,
														T014_Flag_Origem_Produto,
														D006_Codigo_CFOP,
														D014_Codigo_Plano_Conta_Fiscal,
														D001_Id,
														D014_Id,
														D006_Id,
														T014A_D014_Id as T008A_D014_Id,
														D073_Id,
														D037_Id
												from T014 
											left join D009 on D009_Id = T014_D009_Id
											left join D049 on D049_Id = D009_D049_Id	
											left join D001 on D001_Id = D049_D001_Id
											left join D037 on D037_Id = T014_D037_Id
											left join T013 on T013_Id = T014_T013_Id
											left join D006 on D006_Id = T014_D006_Id
											left join D014 on D014_Id = D006_D014_Id
											left join D073 on D073_Id = D014_D073_Id
											left join T014A on T014_Id = T014A_T014_Id 
												where T014_T013_Id = '{$T007['T013_Id']}'
											group by T014_Id
										) as itemNota");

				$count=1;
				while($T008 = mysql_fetch_array($sqlT008)){			
					// Tratamento PIS e COFINS - Verificar validações abaixo
					$CST_PIS      = $T008['T008_Situacao_Tributaria_PIS'];
					$CST_COFINS   = $T008['T008_Situacao_Tributaria_COFINS'];
					$CST_IPI	  = str_pad($T008['T008_Situacao_Tributaria_IPI'],2,'0', STR_PAD_LEFT);
					$IND_MOV      = ($T007['T007_Flag_Finalidade_NFe'] == '1') ? '0' : '1';
					$QTD          = formataDecimal($T008['T008_Quantidade']);
					$VL_DESC      = (formataDecimal($T008['T008_Valor_Total_Desconto']) > 0) ? formataDecimal($T008['T008_Valor_Total_Desconto']) : '';
					// $CST_ICMS  = $T008['T008_Flag_Situacao_Tributaria'];			
					$CST_ICMS     = substr($T008['T008_Flag_Situacao_Tributaria'],-3);
					$CST_ICMS     = (strlen($CST_ICMS) == 2 ? $T008['T014_Flag_Origem_Produto'].$CST_ICMS : $CST_ICMS);

					// Tratamento aliquota cofins
					$T008['T008_Aliquota_COFINS'] = ($CST_COFINS=='01' && $T008['T008_Aliquota_COFINS'] <= 0) ? '3,00' : $T008['T008_Aliquota_COFINS'];

					// Tratamento PIS e COFINS
					$array_CFOP = explode(".", $T008['D006_Codigo_CFOP']);
					if($array_CFOP[0]=='1' || $array_CFOP[0]=='2' || $array_CFOP[0]=='3' && ((empty($CST_PIS) || empty($CST_COFINS) || ($CST_PIS <= '50' || $CST_COFINS <='50')))){
						$CST_PIS='99';
						$CST_COFINS='99';
					}


					if(($T008['T008_Valor_COFINS']==0 || empty($T008['T008_Valor_COFINS'])) && $T008['T008_Valor_Base_COFINS'] > 0){
						//$T008['T008_Valor_COFINS'] = $T008['T008_Valor_Base_COFINS'] * ($T008['T008_Aliquota_COFINS']/100);
					}

					if($T008['T008_Situacao_Tributaria_PIS'] == '03' && $T008['T008_Situacao_Tributaria_COFINS'] == '03'){
						$VL_BC_PIS = '';
						$ALIQ_PIS = '';
						$QUANT_BC_PIS = formataDecimal('0');														
						$ALIQ_PIS_QUANT = $T008['T008_Quantidade'];

						$VL_BC_COFINS = '';
						$ALIQ_COFINS = '';
						$QUANT_BC_COFINS = formataDecimal('0');
						$ALIQ_COFINS_QUANT = $T008['T008_Quantidade'];
					} else {
						$VL_BC_PIS = formataDecimal($T008['T008_Valor_Base_PIS']);
						$ALIQ_PIS = formataDecimal($T008['T008_Aliquota_PIS']);
						$QUANT_BC_PIS = '';
						$ALIQ_PIS_QUANT = '';

						$VL_BC_COFINS = formataDecimal($T008['T008_Valor_Base_COFINS']);
						$ALIQ_COFINS = formataDecimal($T008['T008_Aliquota_COFINS']);
						$QUANT_BC_COFINS = '';	
						$ALIQ_COFINS_QUANT = '';
					}

					if($T008['T008A_D014_Id'] > 0){
						$D014 = mysql_query("SELECT D014_Id,
													D014_Codigo_Plano_Conta_Fiscal,
													D073_Id 
											   FROM D014
										  LEFT JOIN D073 on D073_Id = D014_D073_Id
											  WHERE D014_Id = '{$T008['T008A_D014_Id']}'");
						$mD014 = mysql_fetch_array($D014);

						$T008['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
						$T008['D014_Id']					    = $mD014['D014_Id'];
						$T008['D073_Id']					    = $mD014['D073_Id'];

					}

					// ---------------------------

					# REGISTRO C170			
					$C170 = array(																	#TAM													
						$REG = 'C170',																#004	
						$NUM_ITEM = $count,															#003	Número seqüencial do item no documento fiscal				
						$COD_ITEM = $T008['D001_Codigo_Produto'],									#060	Código do item (campo 02 do Registro 0200)				
						$DESCR_COMPL = $T008['D001_Descricao_Produto'],								#-		Descrição complementar do item como adotado no documento fiscal				
						$QTD,																		#-		Quantidade do item		
						$UNID = $T008['D037_Unidade'],												#006	Unidade do item (Campo 02 do registro 0190)			
						$VL_ITEM = formataDecimal($T008['T008_Valor_Total_Preco_Sem_Desconto']),	#-		Valor total do item (mercadorias ou serviços)			
						$VL_DESC,																	#-		Valor do desconto comercial			
						$IND_MOV,																	#001	Movimentação física do ITEM/PRODUTO (0 SIM/1 NÃO)				
						
						$CST_ICMS,																	#003*	Código da Situação Tributária referente ao ICMS, conforme a Tabela indicada no item 4.3.1				
						$CFOP = removerCharEspecial($T008['D006_Codigo_CFOP']),						#004*	Código Fiscal de Operação e Prestação			
						$COD_NAT = removerCharEspecial($T008['D006_Codigo_CFOP']),					#010	Código da natureza da operação (campo 02 do Registro 0400)				
						$VL_BC_ICMS = formataDecimal($T008['T008_Valor_Base_ICMS']),				#-		Valor da base de cálculo do ICMS				
						$ALIQ_ICMS = formataDecimal($T008['T008_Aliquota_ICMS']),					#006	Alíquota do ICMS				
						$VL_ICMS = formataDecimal($T008['T008_Valor_ICMS']),						#-		Valor do ICMS creditado/debitado			
						$VL_BC_ICMS_ST = formataDecimal($T008['T008_Valor_Base_ICMS_Substituicao']),#-		Valor da base de cálculo referente à substituição tributária				
						$ALIQ_ST = formataDecimal($T008['T008_Aliquota_ICMS_ST']),					#006	Alíquota do ICMS da substituição tributária na unidade da federação de destino				
						$VL_ICMS_ST = formataDecimal($T008['T008_Valor_ICMS_Substituicao_Retencao']),		#-		Valor do ICMS referente à substituição tributária				
						
						$IND_APUR = '0',															#001*	Indicador de período de apuração do IPI:(0 Mensal/1 Decendial)				
						$CST_IPI,																	#002*	Código da Situação Tributária referente ao IPI				
						$COD_ENQ = '',																#003*	Código de enquadramento legal do IPI				
						$VL_BC_IPI = formataDecimal($T008['T008_Valor_Base_IPI']),					#-		Valor da base de cálculo do IPI			
						$ALIQ_IPI = formataDecimal($T008['T008_Aliquota_IPI']),						#006	Alíquota do IPI				
						$VL_IPI = formataDecimal($T008['T008_Valor_IPI']),							#-		Valor do IPI creditado/debitado			
						
						$CST_PIS,																	#002*	Código da Situação Tributária referente ao PIS.				
						$VL_BC_PIS,																	#-		Valor da base de cálculo do PIS			
						$ALIQ_PIS,																	#008	Alíquota do PIS (em percentual)				
						$QUANT_BC_PIS,																#-		Quantidade – Base de cálculo PIS/PASEP				
						$ALIQ_PIS_QUANT,															#-		Alíquota do PIS (em reais)					
						$VL_PIS = formataDecimal($T008['T008_Valor_PIS']),							#-		Valor do PIS			
						
						$CST_COFINS,																#002*	Código da Situação Tributária referente ao COFINS.					
						$VL_BC_COFINS,																#-		Valor da base de cálculo da COFINS				
						$ALIQ_COFINS,																#008	Alíquota do COFINS (em percentual)					
						$QUANT_BC_COFINS,															#-		Quantidade – Base de cálculo COFINS					
						$ALIQ_COFINS_QUANT,															#-		Alíquota da COFINS (em reais)					
						$VL_COFINS = formataDecimal($T008['T008_Valor_COFINS']),					#-		Valor da COFINS							
						$COD_CTA = 	$T008['D014_Codigo_Plano_Conta_Fiscal']							#060	Código da conta analítica contábil debitada/creditada				
					);
					$count++;
					$C170 = str_replace('|', '', $C170);
					$C170 = implode("|", $C170);			
					$layout .= '|' . $C170 . '|' . _SALTO_LINHA;
					
					verificaRegistro0190($T008['D037_Id'], $empresa);
					verificaRegistro0200($T008['D001_Id'], $empresa);
					verificaRegistro0400($T008['D006_Id'], $empresa);
					verificaRegistro0500($T008['D014_Id']);
					verificaRegistro0600($T008['D073_Id']);

					#---------------------------------------------------------
				}	
			} else {
				//Totalizar por CST PIS, CST Cofins, CFOP, alíquota de PIS e alíquota da Cofins
				$sqlT008 = mysql_query("SELECT
											D006_Codigo_CFOP,
											D006_Id,
											T007_Valor_Desconto,
											T008_Situacao_Tributaria_PIS,
											sum(T008_Valor_Base_PIS) as T008_Valor_Base_PIS,
											T008_Aliquota_PIS,
											sum(T008_Valor_PIS) as T008_Valor_PIS,
											T008_Situacao_Tributaria_COFINS,
											sum(T008_Valor_Base_COFINS) as T008_Valor_Base_COFINS,
											T008_Aliquota_COFINS,
											sum(T008_Valor_COFINS) as T008_Valor_COFINS,
											D014_Codigo_Plano_Conta_Fiscal,
											D014_Id,
											T008A_D014_Id,
											D073_Id
											sum(T008_Valor_Total_Preco) as VL_OPR
										from
											T008
											left join T007 on T007_Id = T008_T007_Id    
											left join D006 on D006_Id = T008_D006_Id
											left join D014 on D014_Id = D006_D014_Id
											LEFT JOIN D073 ON D073_Id = D014_D073_Id
											left join T008A on T008_Id = T008A_T008_Id
										where
											T008_T007_Id = '{$T007['T007_Id']}'
										group by
											T008_Situacao_Tributaria_PIS,
											T008_Situacao_Tributaria_COFINS,
											D006_Codigo_CFOP,
											T008_Aliquota_PIS,
											T008_Aliquota_COFINS");

				while($T008 = mysql_fetch_array($sqlT008)) {
					# REGISTRO C175			

					if($T008['T008A_D014_Id'] > 0){
						$D014 = mysql_query("SELECT D014_Id,
													D014_Codigo_Plano_Conta_Fiscal,
													D073_Id 
											   FROM D014
										  LEFT JOIN D073 ON D073_Id = D014_D073_Id  
											  WHERE D014_Id = '{$T008['T008A_D014_Id']}'");
						$mD014 = mysql_fetch_array($D014);

						$T008['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
						$T008['D014_Id']					    = $mD014['D014_Id'];
						$T008['D073_Id']					    = $mD014['D014_Id'];
					}

					$C175 = array(																		#TAM													
							$REG = 'C175', 																#004 Texto fixo contendo "C175”
							$CFOP = removerCharEspecial($T008['D006_Codigo_CFOP']), 					#004 Código fiscal de operação e prestação
							$VL_OPR = formataDecimal($T008['VL_OPR']), 									#02 Valor da operação na combinação de CFOP, CST e alíquotas, correspondente ao somatório do valor das mercadorias e produtos constantes no documento
							$VL_DESC = '0,00',															#02 Valor do desconto comercial / exclusão da base de cálculo do PIS/PASEP e da COFINS 
							$CST_PIS = $T008['T008_Situacao_Tributaria_PIS'], 							#002 Código da Situação Tributária referente ao PIS/PASEP, conforme a Tabela indicada no item 4.3.3.
							$VL_BC_PIS = formataDecimal($T008['T008_Valor_Base_PIS']), 					#02 Valor da base de cálculo do PIS/PASEP (em valor)
							$ALIQ_PIS = formataDecimal($T008['T008_Aliquota_PIS']), 					#008 Alíquota do PIS/PASEP (em percentual) 
							$QUANT_BC_PIS = '', 														#Base de cálculo PIS/PASEP (em quantidade)
							$ALIQ_PIS_QUANT = '', 														#Alíquota do PIS (em reais) 
							$VL_PIS = formataDecimal($T008['T008_Valor_PIS']),							#Valor do PIS/PASEP 
							$CST_COFINS = $T008['T008_Situacao_Tributaria_COFINS'], 					#002 Código da Situação Tributária referente a Cofins, conforme a Tabela indicada no item 4.3.4.
							$VL_BC_COFINS = formataDecimal($T008['T008_Valor_Base_COFINS']), 			#Valor da base de cálculo da Cofins 
							$ALIQ_COFINS = formataDecimal($T008['T008_Aliquota_COFINS']), 				#008 Alíquota da Cofins (em percentual) 
							$QUANT_BC_COFINS = '', 														#Base de cálculo COFINS (em quantidade) 
							$ALIQ_COFINS_QUANT = '', 													#Alíquota da COFINS (em reais) 
							$VL_COFINS = formataDecimal($T008['T008_Valor_COFINS']),					#Valor da Cofins 
							$COD_CTA = $T008['D014_Codigo_Plano_Conta_Fiscal'],							#255 Código da conta analítica contábil debitada/creditada 
							$INFO_COMPL = '', 															#Informação complementar 
					);			
					$C175 = str_replace('|', '', $C175);
					$C175 = implode("|", $C175);
					$layout .= '|' . $C175 . '|' . _SALTO_LINHA;
					
					verificaRegistro0400($T008['D006_Id'], $empresa);	
					verificaRegistro0500($T008['D014_Id']);	
					verificaRegistro0600($T008['D073_Id']);	

				}
			}
		}

		//Busca os produtos em NFs de compras e devoluções para consilidar os resultados
		// $sqlT008 = mysql_query("select D001_Codigo_Produto,
		// 							D005_Classificacao_Fiscal,
		// 							T007_Data_Emissao,
		// 							SUM(T008_Valor_Total_Preco_Sem_Desconto) as T008_Valor_Total_Preco_Sem_Desconto
		// 						from (
		// 									select D001_Codigo_Produto,
		// 										D005_Classificacao_Fiscal,
		// 										T007_Data_Emissao,
		// 										sum(T008_Valor_Total_Preco_Sem_Desconto) as T008_Valor_Total_Preco_Sem_Desconto
		// 									from T008 
		// 								left join T007 on T007_Id = T008_T007_Id 
		// 								left join D009 on D009_Id = T008_D009_Id
		// 								left join D049 on D049_Id = D009_D049_Id
		// 								left join D001 on D001_Id = D049_D001_Id
		// 								left join D005 on D005_Id = D001_D005_Id
		// 									where T007_Data_Emissao >= '{$dataInicial}' 
		// 									and T007_Data_Emissao <= '{$dataFinal}' 
		// 									and T007_C004_Id = '{$empresa}' 
		// 									and T007_Flag_Cancelada != 'S'
		// 									and T007_Numero_Protocolo_Nfe > 0
		// 									and T007_Flag_ACP = 6
		// 									and T007_Flag_Finalidade_NFe = 4
		// 									and (T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0)
		// 								group by D001_Id 
		// 									union
		// 									select D001_Codigo_Produto,
		// 										D005_Classificacao_Fiscal,
		// 										T013_Data_Entrada as T007_Data_Emissao,
		// 										sum(T014_Valor_Total_Custo) as T008_Valor_Total_Preco_Sem_Desconto
		// 									from T014
		// 								left join T013 on T013_Id = T014_T013_Id 
		// 								left join D009 on D009_Id = T014_D009_Id
		// 								left join D049 on D049_Id = D009_D049_Id
		// 								left join D001 on D001_Id = D049_D001_Id
		// 								left join D005 on D005_Id = D001_D005_Id
		// 									where T013_Data_Entrada >= '{$dataInicial}' 
		// 									and T013_Data_Entrada <= '{$dataFinal}' 
		// 									and T013_C004_Id = '{$empresa}' 
		// 									and T013_Flag_Cancelada != 'S'
		// 									and T013_Numero_Protocolo_Nfe > 0
		// 									and T013_Tipo = 1		
		// 									and T013_Flag_Estoque_Finalizado = 'S'	
		// 									and (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)	 
		// 								group by D001_Id
		// 								) as totalItem
		// 							group by D001_Codigo_Produto
		// 							order by T007_Data_Emissao");
				
		// while($T008 = mysql_fetch_array($sqlT008)){
		// 	# REGISTRO C190
		// 	$C190 = array(																     #TAM
		// 		$REG = 'C190',															     #004 Texto fixo contendo C190			
		// 		$COD_MOD = '55',														     #002 Texto fixo contendo "55" (Código da Nota Fiscal Eletrônica, modelo 55, conforme a Tabela 4.1.1)
		// 		$DT_REF_INI	= formataData(substr($T008['T007_Data_Emissao'],0,10)),			 #008 Data Inicial de Referência da Consolidação
		// 		$DT_REF_FIN = formataData(substr($T008['T007_Data_Emissao'],0,10)),			 #008 Data Final de Referência da Consolidação
		// 		$COD_ITEM = $T008['D001_Codigo_Produto'],                                    #060 Código do item (campo 02 do Registro 0200)
		// 		$COD_NCM = $T008['D005_Classificacao_Fiscal'],							     #008 Código da Nomenclatura Comum do Mercosul
		// 		$EX_IPI = '',															     #003 Código EX, conforme a TIPI
		// 		$VL_TOT_ITEM = formataDecimal($T008['T008_Valor_Total_Preco_Sem_Desconto']), # -  Valor Total do Item
		// 	);
		// 	$C190 = str_replace('|', '', $C190);
		// 	$C190 = implode("|", $C190);
		// 	$layout .= '|' . $C190 . '|' . _SALTO_LINHA;
		// 	#---------------------------------------------------------
		// }

		# REGISTRO C110
		$C110 = array();
		$C110 = str_replace('|', '', $C110);
		$C110 = implode("|", $C110);
		//$layout .= '|' . $C110 . '|' . _SALTO_LINHA;
		#-----------------------------------

		# REGISTRO C111
		$C111 = array();
		$C111 = str_replace('|', '', $C111);
		$C111 = implode("|", $C111);
		//$layout .= '|' . $C111 . '|' . _SALTO_LINHA;
		#-----------------------------------

		
		# Não está em uso esta tabela
		$sqlD141 = mysql_query("SELECT * FROM D141");
		if(mysql_num_rows($sqlD141) > 0){

			while($D141 = mysql_fetch_array($sqlD141)){
				# REGISTRO C400
				$C400 = array(
					$REG = 'C400',
					$COD_MOD = '2D',
					$ECF_MOD = $D141['D141_Modelo'],
					$ECF_FAB = $D141['D141_Numero_Serie'],
					$ECF_CX  = $D141['D141_Numero_Caixa']													
				);
				$C400 = str_replace('|', '', $C400);
				$C400 = implode("|", $C400);
				//$layout .= '|' . $C400 . '|' . _SALTO_LINHA;
				#------------------------------------------------
			}

			$sqlT007 = mysql_query("SELECT * 
									FROM T007 
									WHERE T007_Data_Emissao >= '{$dataInicial}' 
									AND T007_Data_Emissao <= '{$dataFinal}' 
									AND T007_C004_Id = '{$empresa}'
									AND T007_Flag_ACP = '4' 
								GROUP BY T007_Data_Emissao");

			while($T007 = mysql_fetch_array($sqlT007)){
				# REGISTRO C405
				$C405 = array(																
					$REG = 'C405',
					$DT_DOC = $T007['T007_Data_Emissao'],
					$CRO,
					$CRZ,
					$NUM_COO_FIN,
					$GT_FIN,
					$VL_BRT
				);
				$C405 = str_replace('|', '', $C405);
				$C405 = implode("|", $C405);
				//$layout .= '|' . $C405 . '|' . _SALTO_LINHA;
				#---------------------------------------------------
				
				//SQL T008 PIS
				$sqlT008 = mysql_query("SELECT D001_Codigo_Produto, 
											T007_Data_Emissao, 
											T008_Situacao_Tributaria_PIS, 
											SUM(T008_Valor_Preco_Sem_Desconto_Unitario) as TotalBruto, 
											SUM(T008_Valor_Base_PIS) as BasePIS, 
											T008_Aliquota_PIS, 
											SUM(T008_Valor_PIS) as TotalPIS, 
											T008_D009_Id 
										FROM T008 
									LEFT JOIN D009 ON D009_Id = T008_D009_Id 
									LEFT JOIN D049 ON D049_Id = D009_D049_Id 
									LEFT JOIN D001 ON D001_Id = D049_D001_Id
									LEFT JOIN T007 ON T007_Id = T008_T007_Id 
										WHERE T007_Data_Emissao = '{$T007['T007_Data_Emissao']}'
										AND T007_Flag_ACP = '4' 
									GROUP BY D001_Codigo_Produto, T008_Situacao_Tributaria_PIS 
									ORDER BY T008_Situacao_Tributaria_PIS, D001_Codigo_Produto");

				while($T008 = mysql_fetch_array($sqlT008)){
					# REGISTRO C481
					$C481 = array(																
						$REG = 'C481',
						$C481_CST_PIS = $T008['T008_Situacao_Tributaria_PIS'],
						$C481_VL_ITEM = formataDecimal($T008['TotalBruto']),
						$C481_VL_BC_PIS = formataDecimal($T008['BasePIS']),
						$C481_ALIQ_PIS = formataDecimal($T008['T008_Aliquota_PIS']),
						$C481_QUANT_BC_PIS,
						$C481_ALIQ_PIS_QUANT,
						$C481_VL_PIS = formataDecimal($T008['TotalPIS']),
						$C481_COD_ITEM = $T008['D001_Codigo_Produto'],
						$C481_COD_CTA = ''
					);
					$C481 = str_replace('|', '', $C481);
					$C481 = implode("|", $C481);
					//$layout .= '|' . $C481 . '|' . _SALTO_LINHA;
					#----------------------------------------------------
				}
				//SQL T008 COFINS
				$sqlT008 = mysql_query("SELECT D001_Codigo_Produto, 
											T007_Data_Emissao, 
											T008_Situacao_Tributaria_COFINS, 
											SUM(T008_Valor_Preco_Sem_Desconto_Unitario) as TotalBruto, 
											SUM(T008_Valor_Base_COFINS) as BaseCOFINS, 
											T008_Aliquota_COFINS, 
											SUM(T008_Valor_COFINS) as TotalCOFINS, 
											T008_D009_Id 
										FROM T008 
									LEFT JOIN D009 ON D009_Id = T008_D009_Id 
									LEFT JOIN D049 ON D049_Id = D009_D049_Id	
									LEFT JOIN D001 ON D001_Id = D049_D001_Id
									LEFT JOIN T007 ON T007_Id = T008_T007_Id 
										WHERE T007_Data_Emissao = '{$T007['T007_Data_Emissao']}'
										AND T007_Flag_ACP = '4' 
									GROUP BY D001_Codigo_Produto, T008_Situacao_Tributaria_COFINS 
									ORDER BY T008_Situacao_Tributaria_PIS, D001_Codigo_Produto");

				while($T008 = mysql_fetch_array($sqlT008)){
					
					# REGISTRO C485
					$C485 = array(																
						$REG = 'C485',
						$C485_CST_COFINS = $T008['T008_Situacao_Tributaria_COFINS'],
						$C485_VL_ITEM = formataDecimal($T008['TotalBruto']),
						$C485_VL_BC_COFINS = formataDecimal($T008['BaseCOFINS']),
						$C485_ALIQ_COFINS = formataDecimal($T008['T008_Aliquota_COFINS']),
						$C485_QUANT_BC_COFINS,
						$C485_ALIQ_COFINS_QUANT,
						$C485_VL_COFINS =formataDecimal( $T008['TotalCOFINS']),
						$C485_COD_ITEM = $T008['D001_Codigo_Produto'],
						$C485_COD_CTA = ''
					);
					$C485 = str_replace('|', '', $C485);
					$C485 = implode("|", $C485);
					//$layout .= '|' . $C485 . '|' . _SALTO_LINHA;
					#-------------------------------------------------
				}
			}		
		}

		$sqlT013 = mysql_query("SELECT
									T013_Id, 
									T013_Tipo,
									T013_D024_Id,  
									T013_Numero_Nota_Fiscal,  
									T013_Data_Emissao,
									DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
									T013_Valor_Total,
									T013_Valor_Total_Produtos,      
									T013_Valor_Total_ICMS,      
									T013_Valor_Total_PIS,
									T013_Valor_Total_COFINS								
								from
									T013
									left join D006 ON D006_Id = T013_D006_Id
									left join T013A ON T013_Id = T013A_T013_Id
								where
									{$dataNFRecebida} >= '{$dataInicialHora}'
									and {$dataNFRecebida} <= '{$dataFinalHora}'
									and T013_C004_Id = '{$empresa}'
									and T013_Flag_Cancelada != 'S'
									and T013_Flag_Estoque_Finalizado = 'S'
									and (T013_Tipo = '6' OR T013_Tipo = '11')
									and (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
									and IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");

		while($T013 = mysql_fetch_array($sqlT013)) {	

			if($T013['T013_Tipo'] == '6'){
				//Nota Fiscal/Conta de Energia Elétrica (Código 06)
				$modelo = "06";
			} else{
				//Nota Fiscal de Energia Elétrica Eletrônica- NF3e (Código 66)
				$modelo = "66";
			}

			# Registro C500
			$C500 = array(																#TAM
				$REG = "C500",															#004*	Texto fixo contendo C500			
				$COD_PART = $T013['T013_D024_Id'],										#060	Código do participante do fornecedor (campo 02 do Registro 0150). 
				$COD_MOD = $modelo,														#002*	Código do modelo do documento fiscal
				$COD_SIT = "00",														#002*	Código da situação do documento fiscal
				$SER = "",																#004	Série do documento fiscal
				$SUB = "",																#003	Subsérie do documento fiscal
				$NUM_DOC = $T013['T013_Numero_Nota_Fiscal'],							#009	Número do documento fiscal
				$DT_DOC = formataData(substr($T013['T013_Data_Emissao'],0,10)),			#008*	Data da emissão do documento fiscal
				$DT_ENT = formataData(substr($T013[$dataNFRecebidaLancarSPED],0,10)),   #008*	Data da entrada
				$VL_DOC = formataDecimal($T013['T013_Valor_Total']),					#		Valor total do documento fiscal 
				$VL_ICMS = formataDecimal($T013['T013_Valor_Total_ICMS']),				#		Valor acumulado do ICMS
				$COD_INF = '',															#006	Código da informação complementar do documento
				$VL_PIS = formataDecimal($T013['T013_Valor_Total_PIS']),				#		Valor do PIS/PASEP
				$VL_COFINS =formataDecimal( $T013['T013_Valor_Total_COFINS']),			#		Valor da COFINS
				$CHV_DOCe = ''															#044*	Chave do Documento Fiscal Eletrônico
			);

			$C500 = str_replace('|', '', $C500);
			$C500 = implode("|", $C500);
			$layout .= '|' . $C500 . '|' . _SALTO_LINHA;
			verificaRegistro0150($T013['T013_D024_Id'], $empresa);

			$sqlT014  = mysql_query("SELECT
										T014_Valor_Total_Custo,
										T014_Situacao_Tributaria_PIS, 																											
										T014_Valor_Base_PIS,
										T014_Aliquota_PIS,     
										T014_Valor_PIS,									         
										D014_Codigo_Plano_Conta_Fiscal,
										D006_Base_Calculo_Credito
									from
										T014																											
										left join T013 on T013_Id = T014_T013_Id
										left join D006 on D006_Id = T014_D006_Id
										left join D014 on D014_Id = D006_D014_Id
									where
										T014_T013_Id = '{$T013['T013_Id']}' and D006_Flag_Deb_Crd_PIS = 'S'");

			while($T014 = mysql_fetch_array($sqlT014)) {
				$C501 = array(															#TAM
					$REG = "C501",														#004*	Texto fixo contendo "C501”
					$CST_PIS = $T014['T014_Situacao_Tributaria_PIS'],					#002*	Código da Situação Tributária referente ao PIS/PASEP	
					$VL_ITEM = formataDecimal($T014['T014_Valor_Total_Custo']),			#		Valor total dos itens
					$NAT_BC_CRED = $T014['D006_Base_Calculo_Credito'],					#002*	Código da Base de Cálculo do Crédito
					$VL_BC_PIS = formataDecimal($T014['T014_Valor_Base_PIS']),			#		Valor da base de cálculo do PIS/PASEP
					$ALIQ_PIS = formataDecimal($T014['T014_Aliquota_PIS']),				#008	Alíquota do PIS/PASEP (em percentual)
					$VL_PIS = formataDecimal($T014['T014_Valor_PIS']),					#		Valor do PIS/PASEP
					$COD_CTA = $T014['D014_Codigo_Plano_Conta_Fiscal'],					#255	Código da conta analítica contábil debitada/creditada	
				);
				
				
				$C501 = str_replace('|', '', $C501);
				$C501 = implode("|", $C501);
				$layout .= '|' . $C501 . '|' . _SALTO_LINHA;
			}

			$sqlT014  = mysql_query("SELECT
										T014_Valor_Total_Custo,									
										T014_Situacao_Tributaria_COFINS,   
										T014_Aliquota_COFINS,
										T014_Valor_COFINS,
										T014_Valor_Base_COFINS,									            
										D014_Codigo_Plano_Conta_Fiscal,
										D006_Base_Calculo_Credito
									from
										T014																											
										left join T013 on T013_Id = T014_T013_Id
										left join D006 on D006_Id = T014_D006_Id
										left join D014 on D014_Id = D006_D014_Id
									where T014_T013_Id = '{$T013['T013_Id']}' and D006_Flag_Deb_Crd_COFINS = 'S' ");

			while($T014 = mysql_fetch_array($sqlT014)) {			

				$C505 = array(															#TAM
					$REG = "C505",														#004*	Texto fixo contendo "C505”
					$CST_COFINS = $T014['T014_Situacao_Tributaria_COFINS'],				#002*	Código da Situação Tributária referente a COFINS
					$VL_ITEM = formataDecimal($T014['T014_Valor_Total_Custo']),			#		Valor total dos itens
					$NAT_BC_CRED = $T014['D006_Base_Calculo_Credito'],					#002*	Código da Base de Cálculo do Crédito
					$VL_BC_COFINS = formataDecimal($T014['T014_Valor_Base_COFINS']),	#		Valor da base de cálculo da COFINS
					$ALIQ_COFINS = formataDecimal($T014['T014_Aliquota_COFINS']),		#008	Alíquota da COFINS (em percentual)
					$VL_COFINS = formataDecimal($T014['T014_Valor_COFINS']),			#		Valor da COFINS
					$COD_CTA = $T014['D014_Codigo_Plano_Conta_Fiscal'],					#255	Código da conta analítica contábil debitada/creditada 
				);

				
				$C505 = str_replace('|', '', $C505);
				$C505 = implode("|", $C505);
				$layout .= '|' . $C505 . '|' . _SALTO_LINHA;
			}
		}
	}
}
$linhas = substr_count($layout, CHR(13).CHR(10));

$linhas -= $linhas_bloco_A;

# REGISTRO C990
$C990 = array(																
	$REG = 'C990',
	$QTD_LIN_C = $linhas+1
);
$C990 = str_replace('|', '', $C990);
$C990 = implode("|", $C990);
$layout .= '|' . $C990 . '|' . _SALTO_LINHA;
#----------------------------------------------

	
$linhas_bloco_C = $QTD_LIN_C + $linhas_bloco_A;

function geraCodigoSituacaoEntrada($CFOP, $CST, $COD_INC_TRIB_0110){
	$ESTADO = substr($CFOP, 0, 1);
	switch($CST) {
		case '01':
			return ($COD_INC_TRIB_0110 == '2') ? '98' : '54';
		break;
		case '02':
			return ($COD_INC_TRIB_0110 == '2') ? '98' : '54';
		break;
		case '03':
			return ($COD_INC_TRIB_0110 == '2') ? '98' : '54';
		break;
		case '04':
			return '73';
		break;
		case '06':
			return '73';
		break;
		case '07':
			return '71';
		break;
		case '08':
			return '74';
		break;
		case '09':
			return '72';
		break;
		case '99':
			return '98';
		break;
	}
	/* SITUAÇÕES DISPONÍVEIS
	50	Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno
	51	Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não Tributada no Mercado Interno
	52	Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação
	
	53	Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
	54	Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
	55	Operação com Direito a Crédito - Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação
	56	Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação
	
	60	Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno
	61	Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno
	62	Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação
	
	63	Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno
	64	Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação
	65	Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação
	66	Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação
	67	Crédito Presumido - Outras Operações
	
	70	Operação de Aquisição sem Direito a Crédito
	71	Operação de Aquisição com Isenção
	72	Operação de Aquisição com Suspensão
	73	Operação de Aquisição a Alíquota Zero
	74	Operação de Aquisição sem Incidência da Contribuição
	75	Operação de Aquisição por Substituição Tributária
	
	98	Outras Operações de Entrada
	*/
}
