<?php
namespace hardness;

global $g;
/*
# BLOCO C # INFORMAÇÕES PRINCIPAIS #
REG   OBRIG		DESCRIÇÃO
M001	O		Abertura do Bloco M
M100	OC		Crédito de PIS/PASEP Relativo ao Período
M105	OC		Detalhamento da Base de Cálculo do Crédito Apurado no Período – PIS/PASEP
M110	OC		Ajustes do Crédito de PIS/PASEP Apurado
M200	O		Consolidação da Contribuição para o PIS/PASEP do Período
M205	O		CONTRIBUIÇÃO PARA PIS/PASEP A RECOLHER - DETALHAMENTO POR CÓDIGO DE RECEITA
M210	O 		Detalhamento da Contribuição para o PIS/PASEP do Período ((Blocos A, C, D ou F CST “01”,“02”, “03” ou “05”))
M211	O 		Sociedades Cooperativas – Composição da Base de Cálculo – PIS/PASEP ((IND_NAT_PJ/0000 = 01))
M220	OC		Ajustes da Contribuição para o PIS/PASEP Apurada
M230	OC		Informações Adicionais de Diferimento
M300	OC		Contribuição de PIS/PASEP Diferida em Períodos Anteriores - Valores a Pagar no Período
M350	OC		PIS/PASEP - Folha de Salários
M400	OC		Receitas Isentas, Não Alcançadas pela Incidência da Contribuição, Sujeitas à Alíquota Zero ou de Vendas com Suspensão – PIS/PA
M410	O(M400)	Detalhamento das Receitas Isentas, Não Alcançadas pela Incidência da Contribuição, Sujeitas à Alíquota Zero ou de Vendas com Suspensão – PIS/PA
M500	OC		Crédito de COFINS Relativo ao Período
M505	OC		Detalhamento da Base de Cálculo do Crédito Apurado no Período – COFINS
M510	OC		Ajustes do Crédito de COFINS Apurado
M600	O		Consolidação da Contribuição para a Seguridade Social - COFINS do Período
M610	O 		Detal. da Contr. para a Seguridade Soc. - COFINS do Período IND_NAT_PJ/0000 = 01) Soc. Coop. – Comp. da Base de Cálculo – COFINS ((Blocos A, C, D ou F CST = 01, 02, 03, 05))
M605	O   	COFINS
M620	OC		Ajustes da COFINS Apurada
M630	OC		Informações Adicionais de Diferimento
M700	OC		COFINS Diferida em Períodos Anteriores – Valores a Pagar no Período
M800	OC		Receitas Isentas, Não Alcançadas pela Incidência da Contribuição, Sujeitas à Alíquota Zero ou de Vendas com Suspensão – COFINS
M810	O(M800)	Detalhamento das Receitas Isentas, Não Alcançadas pela Incidência da Contribuição, Sujeitas à Alíquota Zero ou de Vendas com Suspensão – COFINS
M990	O		Encerramento do Bloco M
*/																				

$linhas_bloco_D = substr_count($layout, CHR(13).CHR(10));

$Movimentacoes = mysql_query("SELECT 
								COUNT(*) AS Movimentacoes
							FROM T007
								LEFT JOIN D006 ON D006_Id = T007_D006_Id
								LEFT JOIN T005 ON T005_Id = T007_T005_Id
								LEFT JOIN T006 ON T005_Id = T006_T005_Id
								LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							WHERE
								T007_Data_Emissao >= '{$dataInicial}'
								AND T007_Data_Emissao <= '{$dataFinal}'
								AND T007_C004_Id IN ($matrizEFiliais)
								AND T007_Flag_Cancelada != 'S'
								AND T007_Numero_Protocolo_Nfe > 0
								AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");

$mMovimentacoes = mysql_fetch_array($Movimentacoes);

$IND_MOV = ($mMovimentacoes['Movimentacoes'] > 0) ? '0' : '1';

# REGISTRO M001
$M001 = array(
	$REG = 'M001',
	$IND_MOV																			
);
$M001 = str_replace('|', '', $M001);
$M001 = implode("|", $M001);
$layout .= '|' . $M001 . '|' . _SALTO_LINHA;

$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$matriz}'");
$C004 = mysql_fetch_array($C004);

if($IND_MOV == '0'){
	if($C004['C004_Flag_Incidencia_Tributaria'] != '2'){
		# REGISTRO M100
		$T008 = mysql_query("SELECT 
								D006_Flag_Estado,
								T008_Situacao_Tributaria_PIS,
								T007_D006_Id,
								SUM(T008_Quantidade) as Quantidade,
								SUM(T008_Valor_Base_PIS) as BasePIS,
								T008_Aliquota_PIS, 
								SUM(T008_Valor_Total_Preco_Sem_Desconto) as TotalBruto 
							FROM T008 							
								LEFT JOIN T007 ON T007_Id = T008_T007_Id 
								LEFT JOIN D006 ON D006_Id = T007_D006_Id
								LEFT JOIN T007A ON T007_Id = T007A_T007_Id 
							WHERE T007_Flag_Entrada_Saida = 'E'								
								AND T007_Data_Emissao >= '{$dataInicial}' 
								AND T007_Data_Emissao <= '{$dataFinal}' 
								AND T007_C004_Id IN ($matrizEFiliais)		
								AND T007_Flag_Cancelada != 'S' 
								AND T008_Situacao_Tributaria_PIS IN('01', '02', '03', '04', '05', '06', '08', '09', '99')
								AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							GROUP BY D006_Flag_Estado, T008_Situacao_Tributaria_PIS");

		while($entradaT008 = mysql_fetch_array($T008)){
			$ESTADO = $entradaT008['D006_Flag_Estado'];
			$COD_CRED = geraCodigoContribuicaoEntrada($ESTADO, $entradaT008['T008_Situacao_Tributaria_PIS']);
			$VL_BC_PIS = $entradaT008['BasePIS'];
			$ALIQ_PIS = $entradaT008['T008_Aliquota_PIS'];		

			if($COD_CRED == '103' || $COD_CRED == '203' || $COD_CRED == '303'){
				$QUANT_BC_PIS = $entradaT008['Quantidade'];
			}

			$ALIQ_PIS_QUANT = ($entradaT008['T008_Situacao_Tributaria_PIS'] == '03') ? $ALIQ_PIS*$entradaT008['TotalBruto']/100 : '0';
			$VL_CRED = ($entradaT008['T008_Situacao_Tributaria_PIS'] != '03') ? $VL_BC_PIS*$ALIQ_PIS/100 : $QUANT_BC_PIS*$ALIQ_PIS_QUANT;
			$VL_CRED_DESC = $VL_CRED + $VL_AJUS_ACRES - $VL_AJUS_REDUC - $VL_CRED_DIF;
			$VL_CRED_DISP = $VL_CRED;
			$M100_VL_DESC = '0'; //$VL_CRED_DESC;			
			
			$M100 = array(													#TAM
				$REG = 'M100',											
				$COD_CRED,													#003*	Código de Tipo de Crédito apurado no período, conforme Tabela 4.3.6.
				$IND_CRED_ORI = '0',										#001*	Indicador de Crédito Oriundo de (0  Operações próprias/1 Evento de incorporação, cisão ou fusão)	
				formataDecimal($VL_BC_PIS),									#-		Valor da Base de Cálculo do Crédito
				formataDecimal($ALIQ_PIS),									#008	Alíquota do PIS/PASEP (em percentual)
				formataDecimal((float) $QUANT_BC_PIS),						#-		Quantidade – Base de cálculo PIS
				formataDecimal($ALIQ_PIS_QUANT),							#-		Alíquota do PIS (em reais)				
				formataDecimal($VL_CRED),									#-		Valor total do crédito apurado no período
				$VL_AJUS_ACRES = '0',										#-		Valor total dos ajustes de acréscimo
				$VL_AJUS_REDUC = '0',										#-		Valor total dos ajustes de redução
				$VL_CRED_DIF = '0',											#-		Valor total do crédito diferido no período
				formataDecimal($VL_CRED_DISP),								#-		Valor Total do Crédito Disponível relativo ao Período (08 + 09 – 10 – 11)
				$IND_DESC_CRED = '0',										#001*	Indicador de opção de utilização do crédito disponível no período: (0 Util. do valor total para desc. da contr. apurada/ 1 Utilização de valor parcial para desconto da contribuição apurada no período)
				formataDecimal($VL_CRED_DESC),								#		Valor do Crédito disponível, descontado  da contribuição apurada no próprio período. Se IND_DESC_CRED=0, informar o valor total do Campo 12; Se IND_DESC_CRED=1, informar o valor parcial do Campo 12.
				$SLD_CRED = '0'												#		Saldo de créditos a utilizar em períodos futuros (12 – 14)
			); 
			$M100 = str_replace('|', '', $M100);
			$M100 = implode("|", $M100);
			$layout .= '|' . $M100 . '|' . _SALTO_LINHA;

			# REGISTRO M105	
			$sqlT008 = mysql_query("SELECT 
										T008_Flag_Base_Calculo,
										D006_Flag_Estado,
										T008_Situacao_Tributaria_PIS,
										T007_D006_Id,
										SUM(T008_Quantidade) as Quantidade,
										SUM(T008_Valor_Base_PIS) as BasePIS,
										T008_Aliquota_PIS 
									FROM T008 
										LEFT JOIN T007 ON T007_Id = T008_T007_Id 
										LEFT JOIN D006 ON D006_Id = T007_D006_Id 
										LEFT JOIN T007A ON T007_Id = T007A_T007_Id
									WHERE T007_Flag_Entrada_Saida = 'E'
										AND T007_Data_Emissao >= '{$dataInicial}' 
										AND T007_Data_Emissao <= '{$dataFinal}'  
										AND T007_C004_Id IN ($matrizEFiliais)
										AND T007_Flag_Cancelada != 'S' AND T008_Situacao_Tributaria_PIS IN('01', '02', '03', '04', '05', '06', '08', '09', '99')
										AND D006_Flag_Estado = '{$ESTADO}'
										AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									GROUP BY T008_Flag_Base_Calculo");
									
			while($resT008 = mysql_fetch_array($sqlT008)){				
				$VL_BC_PIS_TOT = $resT008['BasePIS'];
				$VL_BC_PIS_CUM = ($COD_INC_TRIB_0110 == '3') ? formataDecimal(0) : '';
				$VL_BC_PIS_NC = $VL_BC_PIS_TOT;
				$VL_BC_PIS = $VL_BC_PIS_TOT;
				$M105 = array(												#TAM								
					$REG = 'M105',
					$NAT_BC_CRED = $resT008['T008_Flag_Base_Calculo'],		#Código da Base de Cálculo do Crédito apurado no período, conforme a Tabela 4.3.7.	
					$CST_PI = $res['T008_Situacao_Tributaria_PIS'],			#Código da Situação Tributária referente ao crédito de PIS/Pasep (Tabela 4.3.3) vinculado ao tipo de crédito escriturado em M100.
					formataDecimal($VL_BC_PIS_TOT),							#Valor Total da Base de Cálculo escriturada nos documentos e operações (Blocos “A”, “C”, “D” e “F”), referente ao CST_PIS informado no Campo 03.	
					$VL_BC_PIS_CUM,											#Parcela do Valor Total da Base de Cálculo informada no Campo 04, vinculada a receitas com incidência cumulativa.
					formataDecimal($VL_BC_PIS_NC),							#Valor Total da Base de Cálculo do Crédito, vinculada a receitas com incidência não-cumulativa (Campo 04 – Campo 05).				
					formataDecimal($VL_BC_PIS),								#Valor da Base de Cálculo do Crédito, vinculada ao tipo de Crédito escriturado em M100.
					$QUANT_BC_PIS_TOT,										#Quantidade Total da Base de Cálculo do Crédito apurado em Unidade de Medida de Produto				
					$QUANT_BC_PIS = '',										#Parcela da base de cálculo do crédito em quantidade (campo 08) vinculada ao tipo de crédito escriturado em M100.
					$DESC_CRED = ''											#Descrição do crédito
				); 
				$M105 = str_replace('|', '', $M105);
				$M105 = implode("|", $M105);
				$layout .= '|' . $M105 . '|' . _SALTO_LINHA;
			}
		}

		/*
		 * CST 51 - Operacao com direito a credito vinculada exclusivamente
		 * a receita nao tributada no mercado interno.
		 *
		 * Mantido em uma apuracao separada para nao alterar o tratamento
		 * historico dos demais CSTs. Conforme a tabela 4.3.6, o credito a
		 * aliquota basica deve ser escriturado com COD_CRED 201.
		 */
		$creditoPisCst51 = mysql_query("SELECT
									SUM(T008_Valor_Base_PIS) AS BasePIS,
									T008_Aliquota_PIS
								FROM T008
							LEFT JOIN T007 ON T007_Id = T008_T007_Id
							LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							   WHERE T007_Flag_Entrada_Saida = 'E'
								 AND T007_Data_Emissao >= '{$dataInicial}'
								 AND T007_Data_Emissao <= '{$dataFinal}'
								 AND T007_C004_Id IN ($matrizEFiliais)
								 AND T007_Flag_Cancelada != 'S'
								 AND T008_Situacao_Tributaria_PIS = '51'
								 AND T008_Aliquota_PIS = 1.65
								 AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							GROUP BY T008_Aliquota_PIS");

		while($pisCst51 = mysql_fetch_array($creditoPisCst51)){
			$COD_CRED = '201';
			$VL_BC_PIS = $pisCst51['BasePIS'];
			$ALIQ_PIS = $pisCst51['T008_Aliquota_PIS'];
			$VL_CRED = round($VL_BC_PIS * $ALIQ_PIS / 100, 2);
			$VL_CRED_DISP = $VL_CRED;
			$VL_CRED_DESC = 0;
			$SLD_CRED = $VL_CRED_DISP;

			$M100 = array(
				$REG = 'M100',
				$COD_CRED,
				$IND_CRED_ORI = '0',
				formataDecimal($VL_BC_PIS),
				formataDecimal($ALIQ_PIS),
				'',
				'',
				formataDecimal($VL_CRED),
				$VL_AJUS_ACRES = '0',
				$VL_AJUS_REDUC = '0',
				$VL_CRED_DIF = '0',
				formataDecimal($VL_CRED_DISP),
				$IND_DESC_CRED = '1',
				formataDecimal($VL_CRED_DESC),
				formataDecimal($SLD_CRED)
			);
			$M100 = str_replace('|', '', $M100);
			$M100 = implode('|', $M100);
			$layout .= '|' . $M100 . '|' . _SALTO_LINHA;

			$detalhePisCst51 = mysql_query("SELECT
										D006_Base_Calculo_Credito AS NAT_BC_CRED,
										T008_Situacao_Tributaria_PIS,
										SUM(T008_Valor_Base_PIS) AS BasePIS
									FROM T008
								 LEFT JOIN T007 ON T007_Id = T008_T007_Id
								 LEFT JOIN D006 ON D006_Id = T008_D006_Id
								 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
									WHERE T007_Flag_Entrada_Saida = 'E'
									  AND T007_Data_Emissao >= '{$dataInicial}'
									  AND T007_Data_Emissao <= '{$dataFinal}'
									  AND T007_C004_Id IN ($matrizEFiliais)
									  AND T007_Flag_Cancelada != 'S'
									  AND T008_Situacao_Tributaria_PIS = '51'
									  AND T008_Aliquota_PIS = 1.65
									  AND T008_Aliquota_PIS = '{$ALIQ_PIS}'
									  AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								 GROUP BY D006_Base_Calculo_Credito, T008_Situacao_Tributaria_PIS");

			while($pisCst51Detalhe = mysql_fetch_array($detalhePisCst51)){
				$VL_BC_PIS_TOT = $pisCst51Detalhe['BasePIS'];
				$VL_BC_PIS_CUM = ($COD_INC_TRIB_0110 == '3') ? formataDecimal(0) : '';
				$M105 = array(
					$REG = 'M105',
					$NAT_BC_CRED = $pisCst51Detalhe['NAT_BC_CRED'],
					$CST_PIS = $pisCst51Detalhe['T008_Situacao_Tributaria_PIS'],
					formataDecimal($VL_BC_PIS_TOT),
					$VL_BC_PIS_CUM,
					formataDecimal($VL_BC_PIS_TOT),
					formataDecimal($VL_BC_PIS_TOT),
					'',
					'',
					''
				);
				$M105 = str_replace('|', '', $M105);
				$M105 = implode('|', $M105);
				$layout .= '|' . $M105 . '|' . _SALTO_LINHA;
			}
		}
	}

	# REGISTRO M210
	$layoutM210 = '';
	$sqlT008 = mysql_query("SELECT SUM(T008_Valor_Total_Preco_Sem_Desconto) AS TotalBruto, 
								   T008_Situacao_Tributaria_PIS, 
								   SUM(T008_Valor_PIS) AS ValorPIS,
								   SUM(T008_Valor_Base_PIS) as BasePIS,
								   T008_Aliquota_PIS, 
								   SUM(T008_Quantidade) as Quantidade 
							  FROM T007 
						 LEFT JOIN T008 ON T007_Id = T008_T007_Id 
						 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							 WHERE T007_Data_Emissao >= '{$dataInicial}' 
							   AND T007_Data_Emissao <= '{$dataFinal}' 
							   AND T007_C004_Id IN ($matrizEFiliais)	
							   AND T007_Flag_Entrada_Saida = 'S' 
							   AND T008_Situacao_Tributaria_PIS IN('01','02','03','05') 
							   AND T007_Flag_Cancelada != 'S'
							   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							GROUP BY T008_Situacao_Tributaria_PIS, T008_Aliquota_PIS");
							
	while($T008 = mysql_fetch_array($sqlT008)){
		$COD_CONT = geraCodigoContribuicaoPIS($T008['T008_Situacao_Tributaria_PIS'], $COD_INC_TRIB_0110, $matriz);		
		$VL_REC_BRT = $T008['TotalBruto'];
		$VL_BC_CONT = $T008['BasePIS'];
		$QUANT_BC_PIS = quantidadeBaseCalculoPIS($COD_CONT, $T008['BasePIS'], $T008['Quantidade']);

		if($COD_CONT != '03' && $COD_CONT != '53') {
			$ALIQ_PIS = formataDecimal($T008['T008_Aliquota_PIS']);
			$ALIQ_PIS_QUANT = '';
			$VL_CONT_APUR = $T008['ValorPIS'];			
		} else {
			$VL_ALIQ = mysql_query("SELECT 
										SUM(T008_Valor_Total_Custo_Final)*T008_Aliquota_PIS/100 AS TotalPIS 
									FROM T007 
										LEFT JOIN T008 ON T007_Id = T008_T007_Id
										LEFT JOIN T007A ON T007_Id = T007A_T007_Id 
									WHERE T007_Data_Emissao >= '{$dataInicial}' 
										AND T007_Data_Emissao <= '{$dataFinal}' 
										AND T007_C004_Id IN ($matrizEFiliais)			
										AND T007_Flag_Entrada_Saida = 'S' 
										AND T008_Situacao_Tributaria_PIS = '{$T008['T008_Situacao_Tributaria_PIS']}' 
										AND T007_Flag_Cancelada != 'S'
										AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");
			$VL_ALIQ = mysql_fetch_array($VL_ALIQ);
			$ALIQ_PIS = '';
			$ALIQ_PIS_QUANT = $VL_ALIQ['TotalPIS'];
			$VL_CONT_APUR = $QUANT_BC_PIS*$ALIQ_PIS_QUANT;
		}		

		$VL_AJUS_ACRES_BC_PIS = 0;
		$VL_AJUS_REDUC_BC_PIS = 0;
		$VL_BC_CONT_AJUS = $VL_BC_CONT + $VL_AJUS_ACRES_BC_PIS - $VL_AJUS_REDUC_BC_PIS;
		if($COD_CONT != '03' && $COD_CONT != '53') {
			$VL_CONT_APUR = round($VL_BC_CONT_AJUS * $T008['T008_Aliquota_PIS'] / 100, 2);
		}
		$VL_AJUS_ACRES = 0;
		$VL_AJUS_REDUC = 0;
		$VL_CONT_DIFER = 0;
		$VL_CONT_DIFER_ANT = 0;
		$VL_CONT_PER = $VL_CONT_APUR + $VL_AJUS_ACRES - $VL_AJUS_REDUC;

		$M210 = array(													#TAM					
			$REG = 'M210',
			$COD_CONT,													#002* 	Código da contribuição social apurada no período, conforme a Tabela 4.3.5.
			formataDecimal($VL_REC_BRT),								#-		Valor da Receita Bruta
			formataDecimal($VL_BC_CONT),								#-		Valor da Base de Cálculo da Contribuição
			formataDecimal($VL_AJUS_ACRES_BC_PIS),
			formataDecimal($VL_AJUS_REDUC_BC_PIS),
			formataDecimal($VL_BC_CONT_AJUS),
			$ALIQ_PIS,													#008 	Alíquota do PIS/PASEP (em percentual)
			$QUANT_BC_PIS,												# 		Quantidade – Base de cálculo PIS						
			formataDecimal($ALIQ_PIS_QUANT),							#		Alíquota do PIS (em reais)							
			formataDecimal($VL_CONT_APUR),								#		Valor total da contribuição social apurada			
			formataDecimal($VL_AJUS_ACRES = '0'),						#		Valor total dos ajustes de acréscimo			
			formataDecimal($VL_AJUS_REDUC = '0'),						# 		Valor total dos ajustes de redução			
			formataDecimal($VL_CONT_DIFER = '0'),						#		Valor da contribuição a diferir no período			
			formataDecimal($VL_CONT_DIFER_ANT = '0'),					#		Valor da contribuição diferida em períodos anteriores				
			formataDecimal($VL_CONT_PER)								#		Valor Total da Contribuição do Período (08 + 09 – 10 – 11+12)															
		); 														
		$M210 = str_replace('|', '', $M210);
		$M210 = implode("|", $M210);
		$layoutM210 .= '|' . $M210 . '|' . _SALTO_LINHA;

		//Dados do registro M200
		$arrayCOD_CONT = array('01', '02', '03', '04', '32', '71');
		if(in_array($COD_CONT, $arrayCOD_CONT)){
			$VL_TOT_CONT_NC_PER += $VL_CONT_PER;
		}
	}
	
	# REGISTRO M200
	//If para verificar a natureza da empresa
	$T008 = mysql_query("SELECT SUM(T008_Valor_PIS) as ValorPIS, 
						    	SUM(T008_Valor_Total_Preco_Sem_Desconto) AS TotalBruto 
						   FROM T007 
					  LEFT JOIN T008 ON T007_Id = T008_T007_Id
					  LEFT JOIN T007A ON T007_Id = T007A_T007_Id 
						  WHERE T007_Data_Emissao >= '{$dataInicial}' 
							AND T007_Data_Emissao <= '{$dataFinal}' 
							AND T007_C004_Id IN ($matrizEFiliais)
							AND T008_Situacao_Tributaria_PIS IN('01','02','03','05')
							AND T007_Flag_Entrada_Saida = 'S' 
							AND T007_Flag_Cancelada != 'S'
							AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");

	$T008 = mysql_fetch_array($T008);
	$VL_TOT_CONT_CUM_PER = ($COD_INC_TRIB_0110 != '02') ? '0' : $T008['ValorPIS'];
	$VL_TOT_CRED_DESC = '0';
	$VL_RET_NC = '0';
	$VL_OUT_DED_NC = '0';
	$VL_TOT_CONT_NC_DEV = $VL_TOT_CONT_NC_PER-$VL_TOT_CRED_DESC;
	$VL_CONT_NC_REC = ($COD_INC_TRIB_0110 != '02') ? $VL_TOT_CONT_NC_DEV - $VL_RET_NC - $VL_OUT_DED_NC : '0';
	$VL_CONT_CUM_REC = $VL_TOT_CONT_CUM_PER - $VL_RET_CUM - $VL_OUT_DED_CUM;
	$VL_TOT_CONT_REC = $VL_CONT_NC_REC + $VL_CONT_CUM_REC;

	$M200 = array(															#TAM
		$REG = 'M200',														#1
		formataDecimal($VL_TOT_CONT_NC_PER),								#2 Valor Total da Contribuição Não Cumulativa do Período
		formataDecimal($VL_TOT_CRED_DESC),									#3 Valor do Crédito Descontado, Apurado no Próprio Período da Escrituração	
		formataDecimal($VL_TOT_CRED_DESC_ANT),								#4 Valor do Crédito Descontado, Apurado em Período de Apuração Anterior			
		formataDecimal($VL_TOT_CONT_NC_DEV),								#5 Valor Total da Contribuição Não Cumulativa Devida	
		formataDecimal($VL_RET_NC),											#6 Valor Retido na Fonte Deduzido no Período	
		formataDecimal($VL_OUT_DED_NC),										#7 Outras Deduções no Período		
		formataDecimal($VL_CONT_NC_REC),									#8 Valor da Contribuição Não Cumulativa a Recolher/Pagar (05 – 06 - 07)		
		formataDecimal($VL_TOT_CONT_CUM_PER),								#9 Valor Total da Contribuição Cumulativa do Período
		formataDecimal($VL_RET_CUM),										#10 Valor Retido na Fonte Deduzido no Período		
		formataDecimal($VL_OUT_DED_CUM),									#11 Outras Deduções no Período			
		formataDecimal($VL_CONT_CUM_REC),									#12 Valor da Contribuição Cumulativa a Recolher/Pagar (09 - 10 – 11)			
		formataDecimal($VL_TOT_CONT_REC)									#13 Valor Total da Contribuição a Recolher/Pagar no Período (08 + 12)
	); 
	$M200 = str_replace('|', '', $M200);
	$M200 = implode("|", $M200);
	$layout .= '|' . $M200 . '|' . _SALTO_LINHA;


	# Registro M205 - CONTRIBUIÇÃO PARA PIS/PASEP A RECOLHER - DETALHADAMENTO POR CÓDIGO DE RECEITA
	if($VL_CONT_NC_REC > 0 || $VL_CONT_CUM_REC > 0){
		if($VL_CONT_NC_REC > 0){
			$NUM_CAMPO = '08';	
			$VL_DEBITO = formataDecimal($VL_CONT_NC_REC);
			$COD_REC='691201';
		}else{
			$NUM_CAMPO = '12';
			$VL_DEBITO = formataDecimal($VL_CONT_CUM_REC);
			$COD_REC='691201';
		}

		$NUM_CAMPO = ($VL_CONT_NC_REC > 0) ? '08' : '12';
		$M205 = array(
			$REG = 'M205',
			$NUM_CAMPO,
			$COD_REC,
			$VL_DEBITO
		);
		$M205 = str_replace('|', '', $M205);
		$M205 = implode("|", $M205);
		$layout .= '|' . $M205 . '|' . _SALTO_LINHA;
	}

	//Insere os registros M210 após o M205
	$layout .= $layoutM210;

	unset($VL_TOT_CONT_NC_PER);
	unset($VL_TOT_CRED_DESC);
	unset($VL_TOT_CRED_DESC_ANT);
	unset($VL_TOT_CONT_NC_DEV);
	unset($VL_RET_NC);
	unset($VL_OUT_DED_NC);
	unset($VL_CONT_NC_REC);
	unset($VL_TOT_CONT_CUM_PER);
	unset($VL_RET_CUM);
	unset($VL_OUT_DED_CUM);
	unset($VL_CONT_CUM_REC);
	unset($VL_TOT_CONT_REC);


	if($C004['C004_Flag_Incidencia_Tributaria'] != '2'){

		$isentasT008 = mysql_query("SELECT T008_Situacao_Tributaria_PIS,
										   SUM(T008_Valor_Total_Preco_Sem_Desconto) as ValorTotal,
										   D014_Codigo_Plano_Conta_Fiscal,
										   D014_Id,
										   T008A_D014_Id 
									  FROM T008 
								 LEFT JOIN T007 ON T007_Id = T008_T007_Id 
								 LEFT JOIN D006 ON D006_Id = T007_D006_Id
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id
								 LEFT JOIN T008A ON T008_Id = T008A_T008_Id
								 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
									 WHERE T007_Data_Emissao >= '{$dataInicial}' 
									   AND T007_Data_Emissao <= '{$dataFinal}' 
									   AND T007_C004_Id IN ($matrizEFiliais)
									   AND T007_Flag_Entrada_Saida = 'S' 
									   AND (T008_Situacao_Tributaria_PIS IN('04','06','07','08','09') OR (T008_Situacao_Tributaria_PIS = '05' AND T008_Aliquota_PIS = 0)) 
									   AND T007_Flag_Cancelada != 'S'
									   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								  GROUP BY T008_Situacao_Tributaria_PIS");

		while($T008 = mysql_fetch_array($isentasT008)){

			if($T008['T008A_D014_Id'] > 0){
				$D014 = mysql_query("SELECT D014_Id,
											D014_Codigo_Plano_Conta_Fiscal 
									   FROM D014
									  WHERE D014_Id = '{$T008['T008A_D014_Id']}'");
				$mD014 = mysql_fetch_array($D014);

				$T008['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
				$T008['D014_Id']					    = $mD014['D014_Id'];
			}

			# REGISTRO M400
			$M400 = array(													#TAM								
				$REG = 'M400',
				$CST_PIS = $T008['T008_Situacao_Tributaria_PIS'],			#Código de Situação Tributária – CST das demais receitas auferidas no período, sem incidência da contribuição, ou sem contribuição apurada a pagar, conforme a Tabela 4.3.3.
				$VL_TOT_REC = formataDecimal($T008['ValorTotal']),			#Valor total da receita bruta no período.
				$COD_CTA = $T008['D014_Codigo_Plano_Conta_Fiscal'],			#Código da conta analítica contábil debitada/creditada.
				$DESC_COMPL = ''											#Descrição Complementar da Natureza da Receita.																
			); 
			$M400 = str_replace('|', '', $M400);
			$M400 = implode("|", $M400);
			$layout .= '|' . $M400 . '|' . _SALTO_LINHA;
			verificaRegistro0500($T008['D014_Id']);	

			$NAT_REC = ($CST_PIS != '04') ? '999' : '';
			$VL_REC = $T008['ValorTotal'];
			# REGISTRO M410
			$M410 = array(																			
				$REG = 'M410',		
				$NAT_REC,												#Natureza da Receita, conforme relação constante nas Tabelas de Detalhamento da Natureza da Receita por Situação Tributária 
				formataDecimal($VL_REC),								#Valor da receita bruta no período, relativo a natureza da receita 
				$COD_CTA = $T008['D014_Codigo_Plano_Conta_Fiscal'],		#Código da conta analítica contábil debitada/creditada
				$DESC_COMPL = ''										#Descrição Complementar da Natureza da Receita.
			); 
			$M410 = str_replace('|', '', $M410);
			$M410 = implode("|", $M410);
			$layout .= '|' . $M410 . '|' . _SALTO_LINHA;
		}	
	}

	if($C004['C004_Flag_Incidencia_Tributaria'] != '2'){
		# REGISTRO M500
		$T008 = mysql_query("SELECT 
								D006_Flag_Estado,
						 		T008_Situacao_Tributaria_COFINS,
						 		T007_D006_Id,
						 		SUM(T008_Quantidade) as Quantidade,
						 		SUM(T008_Valor_Base_COFINS) as BaseCOFINS,
						 		T008_Aliquota_COFINS 
							FROM T008 
								LEFT JOIN T007 ON T007_Id = T008_T007_Id 
								LEFT JOIN D006 ON D006_Id = T007_D006_Id 
								LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							WHERE T007_Flag_Entrada_Saida = 'E'		
								AND T007_Data_Emissao >= '{$dataInicial}' 
								AND T007_Data_Emissao <= '{$dataFinal}' 
								AND T007_C004_Id IN ($matrizEFiliais)
								AND T007_Flag_Cancelada != 'S' 
								AND T008_Situacao_Tributaria_COFINS IN('01', '02', '03', '04', '05', '06', '08', '09', '99')
								AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							GROUP BY D006_Flag_Estado, T008_Situacao_Tributaria_COFINS");

		while($entradaT008 = mysql_fetch_array($T008)){
			$ESTADO = $entradaT008['D006_Flag_Estado'];
			$COD_CRED = geraCodigoContribuicaoEntrada($entradaT008['D006_Flag_Estado'], $entradaT008['T008_Situacao_Tributaria_COFINS']);
			$VL_BC_COFINS = ($entradaT008['T008_Situacao_Tributaria_PIS'] != '03') ? $entradaT008['BaseCOFINS'] : '0';
			$ALIQ_COFINS = $entradaT008['T008_Aliquota_COFINS'];
			$QUANT_BC_COFINS = ($entradaT008['T008_Situacao_Tributaria_PIS'] == '03') ? $entradaT008['Quantidade'] : '0';
			$ALIQ_COFINS_QUANT = ($entradaT008['T008_Situacao_Tributaria_PIS'] == '03') ? $ALIQ_PIS*$entradaT008['T008_Valor_Preco_Sem_Desconto_Unitario'] : '0';
			$VL_CRED = ($entradaT008['T008_Situacao_Tributaria_PIS'] != '03') ? $VL_BC_COFINS*$ALIQ_COFINS/100 : $QUANT_BC_COFINS*$ALIQ_COFINS_QUANT;
			$VL_CRED_DESC = $VL_CRED + $VL_AJUS_ACRES - $VL_AJUS_REDUC - $VL_CRED_DIF;		 
			$M500_VL_DESC = $VL_CRED_DESC;
			$VL_CRED_DISP = $VL_CRED;					
			$M500 = array(												#TAM								
				$REG = 'M500',
				$COD_CRED,												#003*	Código de Tipo de Crédito apurado no período, conforme a Tabela 4.3.6.
				$IND_CRED_ORI = '0',									#001*	Indicador de Crédito Oriundo de (0 Operações próprias/1 Evento de incorporação, cisão ou fusão)	
				formataDecimal($VL_BC_COFINS),							#-		Valor da Base de Cálculo do Crédito
				formataDecimal($ALIQ_COFINS),							#008	Alíquota da COFINS (em percentual)	
				formataDecimal($QUANT_BC_COFINS),						# -		Quantidade – Base de cálculo COFINS	
				formataDecimal($ALIQ_COFINS_QUANT),						# -		Alíquota da COFINS (em reais)	
				formataDecimal($VL_CRED),								#-		Valor total do crédito apurado no período
				formataDecimal($VL_AJUS_ACRES = '0'),					#-		Valor total dos ajustes de acréscimo
				formataDecimal($VL_AJUS_REDUC = '0'),					#-		Valor total dos ajustes de redução
				formataDecimal($VL_CRED_DIFER = '0'),					#-		Valor total do crédito diferido no período
				formataDecimal($VL_CRED_DISP),							#-		Valor Total do Crédito Disponível relativo ao Período (08 + 09 – 10 – 11)
				$IND_DESC_CRED = '0',									#001*	Indicador de utilização do crédito disponível no período (0 Utilização do valor total para desconto da contribuição apurada no período, no Registro M600/1 Utilização de valor parcial para desconto da contribuição apurada no período, no Registro M600)	
				formataDecimal($VL_CRED_DESC),							#		Valor do Crédito disponível, descontado da contribuição apurada no próprio período (IND_DESC_CRED=0, informar o valor total do Campo 12 / IND_DESC_CRED=1, informar o valor parcial do Campo 12)
				formataDecimal($SLD_CRED = '0')							#		Saldo de créditos a utilizar em períodos futuros (12 – 14)																			
			); 
			$M500 = str_replace('|', '', $M500);
			$M500 = implode("|", $M500);
			$layout .= '|' . $M500 . '|' . _SALTO_LINHA;
			$sqlT008 = mysql_query("SELECT 
										T008_Flag_Base_Calculo,
										D006_Flag_Estado,
										T008_Situacao_Tributaria_COFINS,
										T007_D006_Id,
										SUM(T008_Quantidade) as Quantidade,
										SUM(T008_Valor_Base_COFINS) as BaseCOFINS,
										T008_Aliquota_COFINS 
									FROM T008 
										LEFT JOIN T007 ON T007_Id = T008_T007_Id 
										LEFT JOIN D006 ON D006_Id = T007_D006_Id 
										LEFT JOIN T007A ON T007_Id = T007A_T007_Id
									WHERE T007_Flag_Entrada_Saida = 'E'
										AND T007_Data_Emissao >= '{$dataInicial}' 
										AND T007_Data_Emissao <= '{$dataFinal}' 
										AND T007_C004_Id IN ($matrizEFiliais)
										AND T007_Flag_Cancelada != 'S' 
										AND T008_Situacao_Tributaria_COFINS IN('01', '02', '03', '04', '05', '06', '08', '09', '99')
										AND D006_Flag_Estado = '{$ESTADO}'
										AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									GROUP BY T008_Flag_Base_Calculo");

			while($T008 = mysql_fetch_array($sqlT008)){
				$VL_BC_COFINS_TOT = $T008['BaseCOFINS'];
				$VL_BC_COFINS_CUM = ($COD_INC_TRIB_0110 == '3') ? formataDecimal(0) : '';
				$VL_BC_COFINS_NC = $VL_BC_PIS_TOT;
				$VL_BC_COFINS = $VL_BC_PIS_TOT;

				# REGISTRO M505
				$M505 = array(														#TAM								
					$REG = 'M505',
					$NAT_BC_CRED = $T008['T008_Flag_Base_Calculo'],					#	Código da Base de Cálculo do Crédito apurado no período, conforme a Tabela 4.3.7.
					$CST_COFINS = $T008['T008_Situacao_Tributaria_COFINS'],			#	Código da Situação Tributária referente ao crédito de COFINS (Tabela 4.3.4) vinculado ao tipo de crédito escriturado em M500.
					formataDecimal($VL_BC_COFINS_TOT),								#	Valor Total da Base de Cálculo escriturada nos documentos e operações (Blocos “A”, “C”, “D” e “F”), referente ao CST_COFINS informado no Campo 03.	
					formataDecimal($VL_BC_COFINS_CUM),								#	Parcela do Valor Total da Base de Cálculo informada no Campo 04, vinculada a receitas com incidência cumulativa.					
					formataDecimal($VL_BC_COFINS_NC),								#	Valor Total da Base de Cálculo do Crédito, vinculada a receitas com incidência não-cumulativa (Campo 04 – Campo 05).	
					formataDecimal($VL_BC_COFINS),									#	Valor da Base de Cálculo do Crédito, vinculada ao tipo de Crédito escriturado em M500.
					$QUANT_BC_COFINS_TOT = '',										#	Quantidade Total da Base de Cálculo do Crédito apurado em Unidade de Medida de Produto				
					$QUANT_BC_COFINS = '',											#	Parcela da base de cálculo do crédito em quantidade (campo 08) vinculada ao tipo de crédito escriturado em M500.	
					$DESC_CRED = ''													# 	Descrição do crédito
				); 
				$M505 = str_replace('|', '', $M505);
				$M505 = implode("|", $M505);
				$layout .= '|' . $M505 . '|' . _SALTO_LINHA;
			}
		}

		/* Tratamento dedicado ao CST 51 para o credito de COFINS. */
		$creditoCofinsCst51 = mysql_query("SELECT
									   SUM(T008_Valor_Base_COFINS) AS BaseCOFINS,
									   T008_Aliquota_COFINS
								  FROM T008
							   LEFT JOIN T007 ON T007_Id = T008_T007_Id
							   LEFT JOIN T007A ON T007_Id = T007A_T007_Id
								 WHERE T007_Flag_Entrada_Saida = 'E'
								   AND T007_Data_Emissao >= '{$dataInicial}'
								   AND T007_Data_Emissao <= '{$dataFinal}'
								   AND T007_C004_Id IN ($matrizEFiliais)
								   AND T007_Flag_Cancelada != 'S'
								   AND T008_Situacao_Tributaria_COFINS = '51'
								   AND T008_Aliquota_COFINS = 7.60
								   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							  GROUP BY T008_Aliquota_COFINS");

		while($cofinsCst51 = mysql_fetch_array($creditoCofinsCst51)){
			$COD_CRED = '201';
			$VL_BC_COFINS = $cofinsCst51['BaseCOFINS'];
			$ALIQ_COFINS = $cofinsCst51['T008_Aliquota_COFINS'];
			$VL_CRED = round($VL_BC_COFINS * $ALIQ_COFINS / 100, 2);
			$VL_CRED_DISP = $VL_CRED;
			$VL_CRED_DESC = 0;
			$SLD_CRED = $VL_CRED_DISP;

			$M500 = array(
				$REG = 'M500',
				$COD_CRED,
				$IND_CRED_ORI = '0',
				formataDecimal($VL_BC_COFINS),
				formataDecimal($ALIQ_COFINS),
				'',
				'',
				formataDecimal($VL_CRED),
				formataDecimal($VL_AJUS_ACRES = '0'),
				formataDecimal($VL_AJUS_REDUC = '0'),
				formataDecimal($VL_CRED_DIFER = '0'),
				formataDecimal($VL_CRED_DISP),
				$IND_DESC_CRED = '1',
				formataDecimal($VL_CRED_DESC),
				formataDecimal($SLD_CRED)
			);
			$M500 = str_replace('|', '', $M500);
			$M500 = implode('|', $M500);
			$layout .= '|' . $M500 . '|' . _SALTO_LINHA;

			$detalheCofinsCst51 = mysql_query("SELECT
										   D006_Base_Calculo_Credito AS NAT_BC_CRED,
										   T008_Situacao_Tributaria_COFINS,
										   SUM(T008_Valor_Base_COFINS) AS BaseCOFINS
									  FROM T008
								   LEFT JOIN T007 ON T007_Id = T008_T007_Id
								   LEFT JOIN D006 ON D006_Id = T008_D006_Id
								   LEFT JOIN T007A ON T007_Id = T007A_T007_Id
									 WHERE T007_Flag_Entrada_Saida = 'E'
									   AND T007_Data_Emissao >= '{$dataInicial}'
									   AND T007_Data_Emissao <= '{$dataFinal}'
									   AND T007_C004_Id IN ($matrizEFiliais)
									   AND T007_Flag_Cancelada != 'S'
									   AND T008_Situacao_Tributaria_COFINS = '51'
									   AND T008_Aliquota_COFINS = 7.60
									   AND T008_Aliquota_COFINS = '{$ALIQ_COFINS}'
									   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									GROUP BY D006_Base_Calculo_Credito, T008_Situacao_Tributaria_COFINS");

			while($cofinsCst51Detalhe = mysql_fetch_array($detalheCofinsCst51)){
				$VL_BC_COFINS_TOT = $cofinsCst51Detalhe['BaseCOFINS'];
				$VL_BC_COFINS_CUM = ($COD_INC_TRIB_0110 == '3') ? formataDecimal(0) : '';
				$M505 = array(
					$REG = 'M505',
					$NAT_BC_CRED = $cofinsCst51Detalhe['NAT_BC_CRED'],
					$CST_COFINS = $cofinsCst51Detalhe['T008_Situacao_Tributaria_COFINS'],
					formataDecimal($VL_BC_COFINS_TOT),
					$VL_BC_COFINS_CUM,
					formataDecimal($VL_BC_COFINS_TOT),
					formataDecimal($VL_BC_COFINS_TOT),
					'',
					'',
					''
				);
				$M505 = str_replace('|', '', $M505);
				$M505 = implode('|', $M505);
				$layout .= '|' . $M505 . '|' . _SALTO_LINHA;
			}
		}
	}

	# REGISTRO M610
	$layoutM610 = '';
	$saidaT008 = mysql_query("SELECT SUM(T008_Valor_COFINS) as ValorCOFINS,
									 T008_Situacao_Tributaria_COFINS,
									 SUM(T008_Valor_Total_Preco_Sem_Desconto) as TotalBruto,								
									 SUM(T008_Valor_Base_COFINS) as BaseCOFINS,
									 T008_Aliquota_COFINS,
									 SUM(T008_Quantidade) as Quantidade
								FROM T007 
						   LEFT JOIN T008 ON T007_Id = T008_T007_Id
						   LEFT JOIN T007A ON T007_Id = T007A_T007_Id 
							   WHERE T007_Data_Emissao >= '{$dataInicial}' 
								 AND T007_Data_Emissao <= '{$dataFinal}' 
								 AND T007_C004_Id IN ($matrizEFiliais)
								 AND T008_Situacao_Tributaria_COFINS IN('01','02','03','04','05') 
								 AND T007_Flag_Cancelada != 'S'
								 AND T007_Flag_Entrada_Saida = 'S' 
								 AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
   					        GROUP BY T008_Situacao_Tributaria_COFINS, T008_Aliquota_COFINS");
		
		while($T008 = mysql_fetch_array($saidaT008)){

			$COD_CONT = geraCodigoContribuicaoCOFINS($T008['T008_Situacao_Tributaria_COFINS'], $COD_INC_TRIB_0110, $matriz);		
			$VL_REC_BRT = $T008['TotalBruto'];
			$VL_BC_CONT = $T008['BaseCOFINS'];
			$QUANT_BC_COFINS = quantidadeBaseCalculoCOFINS($COD_CONT, $T008['BaseCOFINS'], $T008['Quantidade']);		

			if($COD_CONT != '03' && $COD_CONT != '53'){
				$ALIQ_COFINS = formataDecimal($T008['T008_Aliquota_COFINS']);
				$ALIQ_COFINS_QUANT = '';
				$VL_CONT_APUR = $T008['ValorCOFINS'];
			} else {
				$VL_ALIQ = mysql_query("SELECT SUM(T008_Valor_Total_Custo_Final)*T008_Aliquota_COFINS/100 AS TotalCOFINS 
										  FROM T007 
									 LEFT JOIN T008 ON T007_Id = T008_T007_Id 
									 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
										 WHERE T007_Data_Emissao >= '{$dataInicial}' 
										   AND T007_Data_Emissao <= '{$dataFinal}' 
										   AND T007_C004_Id IN ($matrizEFiliais)
										   AND T007_Flag_Entrada_Saida = 'S' 
										   AND T008_Situacao_Tributaria_COFINS = '{$T008['T008_Situacao_Tributaria_COFINS']}' 
										   AND T007_Flag_Cancelada != 'S'
										   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");

				$VL_ALIQ = mysql_fetch_array($VL_ALIQ);
				$ALIQ_COFINS = '';
				$ALIQ_COFINS_QUANT = $VL_ALIQ['TotalCOFINS'];
				$VL_CONT_APUR = $QUANT_BC_COFINS*$ALIQ_COFINS_QUANT;
			}		

			$VL_AJUS_ACRES_BC_COFINS = 0;
			$VL_AJUS_REDUC_BC_COFINS = 0;
			$VL_BC_CONT_AJUS = $VL_BC_CONT + $VL_AJUS_ACRES_BC_COFINS - $VL_AJUS_REDUC_BC_COFINS;
			if($COD_CONT != '03' && $COD_CONT != '53'){
				$VL_CONT_APUR = round($VL_BC_CONT_AJUS * $T008['T008_Aliquota_COFINS'] / 100, 2);
			}
			$VL_AJUS_ACRES = 0;
			$VL_AJUS_REDUC = 0;
			$VL_CONT_DIFER = 0;
			$VL_CONT_DIFER_ANT = 0;
			$VL_CONT_PER = $VL_CONT_APUR + $VL_AJUS_ACRES - $VL_AJUS_REDUC - $VL_CONT_DIFER + $VL_CONT_DIFER_ANT;

			$M610 = array(																			
				$REG = 'M610',
				$COD_CONT,												#Código da contribuição social apurada no período, conforme a Tabela 4.3.5.
				formataDecimal($VL_REC_BRT),							#Valor da Receita Bruta	
				formataDecimal($VL_BC_CONT),							#Valor da Base de Cálculo da Contribuição	
				formataDecimal($VL_AJUS_ACRES_BC_COFINS),
				formataDecimal($VL_AJUS_REDUC_BC_COFINS),
				formataDecimal($VL_BC_CONT_AJUS),
				$ALIQ_COFINS,											#Alíquota do COFINS (em percentual)	
				$QUANT_BC_COFINS,										#Quantidade – Base de cálculo COFINS		
				$ALIQ_COFINS_QUANT,										#Alíquota do COFINS (em reais)		
				formataDecimal($VL_CONT_APUR),							#Valor total da contribuição social apurada	
				formataDecimal($VL_AJUS_ACRES),							#Valor total dos ajustes de acréscimo	
				formataDecimal($VL_AJUS_REDUC),							#Valor total dos ajustes de redução	
				formataDecimal($VL_CONT_DIFER),							#Valor da contribuição a diferir no período	
				formataDecimal($VL_CONT_DIFER_ANT),						#Valor da contribuição diferida em períodos anteriores		
				formataDecimal($VL_CONT_PER)							#Valor Total da Contribuição do Período (08 + 09 – 10 – 11 +12)	
			); 
			$M610 = str_replace('|', '', $M610);
			$M610 = implode("|", $M610);
			$layoutM610 .= '|' . $M610 . '|' . _SALTO_LINHA;

			//Dados do registro M200
			$arrayCOD_CONT = array('01', '02', '03', '04', '32', '71');
			if(in_array($COD_CONT, $arrayCOD_CONT)){
				$VL_TOT_CONT_NC_PER += $VL_CONT_PER;
			}
		}
	

	# REGISTRO M600
	$T008 = mysql_query("SELECT 
							SUM(T008_Valor_COFINS) as ValorCOFINS 
						FROM T007 
							LEFT JOIN T008 ON T007_Id = T008_T007_Id 
							LEFT JOIN T007A ON T007_Id = T007A_T007_Id
						WHERE T007_Data_Emissao >= '{$dataInicial}' 
							AND T007_Data_Emissao <= '{$dataFinal}' 
							AND T007_C004_Id IN ($matrizEFiliais)
							AND T008_Situacao_Tributaria_COFINS IN('01','02','03','04','05') 	
							AND T007_Flag_Entrada_Saida = 'S' 
							AND T007_Flag_Cancelada != 'S'
							AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'");

	$T008 = mysql_fetch_array($T008);
	$VL_TOT_CONT_CUM_PER = ($COD_INC_TRIB_0110 != '02') ? '0' : $T008['ValorCOFINS'];
	$VL_TOT_CRED_DESC = '0';
	$VL_RET_NC = '0';
	$VL_OUT_DED_NC = '0';
	$VL_TOT_CONT_NC_DEV = $VL_TOT_CONT_NC_PER-$VL_TOT_CRED_DESC;
	$VL_CONT_NC_REC = ($COD_INC_TRIB_0110 != '02') ? $VL_TOT_CONT_NC_DEV - $VL_RET_NC - $VL_OUT_DED_NC : '0';
	$VL_CONT_CUM_REC = $VL_TOT_CONT_CUM_PER - $VL_RET_CUM - $VL_OUT_DED_CUM;
	$VL_TOT_CONT_REC = $VL_CONT_NC_REC + $VL_CONT_CUM_REC;
	$M600 = array(																			
		$REG = 'M600',												#1
		formataDecimal($VL_TOT_CONT_NC_PER),						#2	Valor Total da Contribuição Não Cumulativa do Período (recuperado do campo 13 do Registro M610, quando o campo “COD_CONT” = 01, 02, 03, 04, 32 e 71)				
		formataDecimal($VL_TOT_CRED_DESC),							#3	Valor do Crédito Descontado, Apurado no Próprio Período da Escrituração (recuperado do campo 14 do Registro M500)			
		formataDecimal($VL_TOT_CRED_DESC_ANT),						#4	Valor do Crédito Descontado, Apurado em Período de Apuração Anterior (recuperado do campo 13 do Registro 1500)				
		formataDecimal($VL_TOT_CONT_NC_DEV),						#5	Valor Total da Contribuição Não Cumulativa Devida (02 - 03 - 04)				
		formataDecimal($VL_RET_NC),									#6	Valor Retido na Fonte Deduzido no Período	
		formataDecimal($VL_OUT_DED_NC),								#7	Outras Deduções no Período		
		formataDecimal($VL_CONT_NC_REC),							#8	Valor da Contribuição Não Cumulativa a Recolher/Pagar (05 - 06 - 07)			
		formataDecimal($VL_TOT_CONT_CUM_PER),						#9	Valor Total da Contribuição Cumulativa do Período (recuperado do campo 13 do Registro M610, quando o campo “COD_CONT” = 31, 32, 51, 52, 53, 54 e 72)				
		formataDecimal($VL_RET_CUM),								#10	Valor Retido na Fonte Deduzido no Período		
		formataDecimal($VL_OUT_DED_CUM),							#11	Outras Deduções no Período			
		formataDecimal($VL_CONT_CUM_REC),							#12	Valor da Contribuição Cumulativa a Recolher/Pagar (09 - 10 - 11)			
		formataDecimal($VL_TOT_CONT_REC)							#13	Valor Total da Contribuição a Recolher/Pagar no Período (08 + 12)			
	); 
	$M600 = str_replace('|', '', $M600);
	$M600 = implode("|", $M600);
	$layout .= '|' . $M600 . '|' . _SALTO_LINHA;

	# Registro M605 - CONTRIBUIÇÃO PARA PIS/PASEP A RECOLHER - DETALHADAMENTO POR CÓDIGO DE RECEITA
	if($VL_CONT_NC_REC > 0 || $VL_CONT_CUM_REC > 0){
		if($VL_CONT_NC_REC > 0){
			$NUM_CAMPO = '08';	
			$VL_DEBITO = formataDecimal($VL_CONT_NC_REC);
			$COD_REC='585601';
		}else{
			$NUM_CAMPO = '12';
			$VL_DEBITO = formataDecimal($VL_CONT_CUM_REC);
			$COD_REC='585601';
		}

		$NUM_CAMPO = ($VL_CONT_NC_REC > 0) ? '08' : '12';
		$M605 = array(
			$REG = 'M605',
			$NUM_CAMPO,
			$COD_REC,
			$VL_DEBITO
		);
		$M605 = str_replace('|', '', $M605);
		$M605 = implode("|", $M605);
		$layout .= '|' . $M605 . '|' . _SALTO_LINHA;
	}

	//Insere os registros M610 na sequencia
	$layout .= $layoutM610;

	if($C004['C004_Flag_Incidencia_Tributaria'] != '2'){
		# REGISTRO M800
		$isentosT008 = mysql_query("SELECT T008_Situacao_Tributaria_COFINS, SUM(T008_Valor_Total_Preco_Sem_Desconto) as TotalBruto, 
										   D014_Codigo_Plano_Conta_Fiscal,
										   D014_Id,
										   T008A_D014_Id
								      FROM T008 
								 LEFT JOIN T007 ON T007_Id = T008_T007_Id
								 LEFT JOIN D006 ON D006_Id = T008_D006_Id 
								 LEFT JOIN D014 ON D014_ID = D006_D014_Id
								 LEFT JOIN T008A ON T008_ID = T008A_T008_Id
								 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							  	     WHERE T007_Flag_Cancelada != 'S' 
									   AND T007_Data_Emissao >= '{$dataInicial}' 
									   AND T007_Data_Emissao <= '{$dataFinal}' 
									   AND T007_C004_Id IN ($matrizEFiliais)
									   AND (T008_Situacao_Tributaria_COFINS IN('04','06','07','08','09') OR (T008_Situacao_Tributaria_COFINS = '05' AND T008_Aliquota_COFINS = 0))
									   AND T007_Flag_Entrada_Saida = 'S' 
									   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								  GROUP BY T008_Situacao_Tributaria_COFINS");

		while($T008 = mysql_fetch_array($isentosT008)){

			if($T008['T008A_D014_Id'] > 0){
				$D014 = mysql_query("SELECT D014_Id,
											D014_Codigo_Plano_Conta_Fiscal 
									   FROM D014
									  WHERE D014_Id = '{$T008['T008A_D014_Id']}'");
				$mD014 = mysql_fetch_array($D014);

				$T008['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
				$T008['D014_Id']					    = $mD014['D014_Id'];
			}


			$CST_COFINS = $T008['T008_Situacao_Tributaria_COFINS'];
			$VL_TOT_REC = $T008['TotalBruto'];
			$M800 = array(
				$REG = 'M800',
				$CST_COFINS,											#Código de Situação Tributária – CST das demais receitas auferidas no período, sem incidência da contribuição, ou sem contribuição apurada a pagar, conforme a Tabela 4.3.4.	
				formataDecimal($VL_TOT_REC),							#Valor total da receita bruta no período.	
				$COD_CTA = $T008['D014_Codigo_Plano_Conta_Fiscal'],		#Código da conta analítica contábil debitada/creditada.
				$DESC_COMPL												#Descrição Complementar da Natureza da Receita.																		
			); 
			$M800 = str_replace('|', '', $M800);
			$M800 = implode("|", $M800);
			$layout .= '|' . $M800 . '|' . _SALTO_LINHA;
			verificaRegistro0500($T008['D014_Id']);			
			
			$NAT_REC = ($CST_COFINS != '04') ? '999' : '';
			$VL_REC = $T008['TotalBruto'];
			# REGISTRO M810
			$M810 = array(																			
				$REG = 'M810',		
				$NAT_REC,												#Natureza da Receita, conforme relação constante nas Tabelas de Detalhamento da Natureza da Receita por Situação Tributária 
				formataDecimal($VL_REC),								#Valor da receita bruta no período, relativo a natureza da receita 
				$COD_CTA = $T008['D014_Codigo_Plano_Conta_Fiscal'],		#Código da conta analítica contábil debitada/creditada
				$DESC_COMPL = ''										#Descrição Complementar da Natureza da Receita.
			); 
			$M810 = str_replace('|', '', $M810);
			$M810 = implode("|", $M810);
			$layout .= '|' . $M810 . '|' . _SALTO_LINHA;
		}
	}	
}

$LINE_NUM = substr_count($layout, CHR(13).CHR(10));
$LINE_NUM = $LINE_NUM - $linhas_bloco_D + 1;

# REGISTRO M990
$M990 = array(											#TAM							
	$REG = 'M990',
	$LINE_NUM																		
); 
$M990 = str_replace('|', '', $M990);
$M990 = implode("|", $M990);
$layout .= '|' . $M990 . '|' . _SALTO_LINHA;

$layout .= '|P001|1|' . _SALTO_LINHA;
$layout .= '|P990|2|' . _SALTO_LINHA;
$layout .= '|1001|1|' . _SALTO_LINHA;
$layout .= '|1990|2|' . _SALTO_LINHA;
$layout .= '|9001|0|' . _SALTO_LINHA;

function geraCodigoContribuicaoEntrada($ESTADO, $CST){	
	if($ESTADO == 'I'){
		return '3' . $CST;
	} else if($ESTADO == 'D'){
		return '1' . $CST;
	} else if($ESTADO == 'F'){ 
		return '2' . $CST;
	}
}

function geraCodigoContribuicaoPIS($CST_PIS, $CODIGO_INC, $empresa){
	$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$empresa}'");
	$C004 = mysql_fetch_array($C004);
	$PER_PIS = $C004['C004_Percentual_PIS'];
	if($CST_PIS == '01'){
		if($CODIGO_INC == '1'){
			$COD_CONT = '01';
		} else if($CODIGO_INC == '3' && $PER_PIS == '0.65'){
			$COD_CONT = '51';
		} else if($CODIGO_INC == '3' && $PER_PIS == '1.65'){
			$COD_CONT = '01';
		} else {
			$COD_CONT = '51';
		}
	} else if($CST_PIS == '02'){
		if($CODIGO_INC == '1' || $CODIGO_INC == '3'){
			$COD_CONT = '02';
		} else {
			$COD_CONT = '52';
		}
	} else if($CST_PIS == '03'){
		if($CODIGO_INC == '1' || $CODIGO_INC == '3'){
			$COD_CONT = '03';
		} else if($CODIGO_INC == '2'){
			$COD_CONT = '53';
		}
	} else if($CST_PIS == '05'){
		if($PER_PIS == '0.65'){
			$COD_CONT = '31';
		} else {
			$COD_CONT = '32';
		}
	}
	return $COD_CONT;
}

function quantidadeBaseCalculoPIS($COD, $BASE, $QUANTIDADE){
	if($COD == '03' || $COD == '53' || $COD == '32'){
		return formataDecimal($BASE / $QUANTIDADE);
	} else if($COD == '01' || $COD == '51' || $COD == '02' || $COD == '52' || $COD == '31'){
		return '';
	}
}

function quantidadeBaseCalculoCOFINS($COD, $BASE, $QUANTIDADE){
	if($COD == '03' || $COD == '53' || $COD == '32'){
		return formataDecimal($BASE / $QUANTIDADE);
	} else if($COD == '01' || $COD == '51' || $COD == '02' || $COD == '52' || $COD == '31'){
		return '';
	}
}

function quantidadeBaseCalculoEntrada($COD, $BASE, $QUANTIDADE){
	$COD_CRED = array('103', '203', '303', '105', '205', '305', '108', '208', '308');
	if(in_array($COD, $COD_CRED)){
		return $BASE / $QUANTIDADE;
	} else {
		return '';
	}
}

function geraCodigoContribuicaoCOFINS($CST_COFINS, $CODIGO_INC, $empresa){
	$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$empresa}'");
	$C004 = mysql_fetch_array($C004);
	$PER_COFINS = $C004['C004_Percentual_COFINS'];
	if($CST_COFINS == '01'){
		if($CODIGO_INC == '1'){
			$COD_CONT = '01';
		} else if($CODIGO_INC == '3' && $PER_COFINS == '7.6'){
			$COD_CONT = '01';
		} else if($CODIGO_INC == '3' && $PER_COFINS == '3'){
			$COD_CONT = '51';
		} else {
			$COD_CONT = '51';
		}
	} else if($CST_COFINS == '02'){
		if($CODIGO_INC == '1' || $CODIGO_INC == '3'){
			$COD_CONT = '02';
		} else {
			$COD_CONT = '52';
		}
	} else if($CST_COFINS == '03'){
		if($CODIGO_INC == '1' || $CODIGO_INC == '3'){
			$COD_CONT = '03';
		} else if($CODIGO_INC == '2'){
			$COD_CONT = '53';
		}
	} else if($CST_COFINS == '05'){
		if($PER_COFINS == '3'){
			$COD_CONT = '31';
		} else {
			$COD_CONT = '32';
		}
	}
	return $COD_CONT;
}
