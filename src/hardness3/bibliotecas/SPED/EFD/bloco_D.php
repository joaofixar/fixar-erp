<?php

namespace hardness;

global $g;
/*
# BLOCO C # INFORMAÇÕES PRINCIPAIS #
REG 	 OBRIG		DESCRIÇÃO
D001	 O			Abertura do Bloco D
D100	 OC			Aquisição de Serviços de Transportes (Códigos 07, 08, 8B, 09, 10, 11, 26, 27, 57, 63 e 67)
D101	 OC			Complemento do Documento de Transporte – PIS/PASEP
D105	 OC			Complemento do Documento de Transporte – COFINS 
D990	 O			Encerramento do Bloco D 
*/

$T084Movimento = mysql_query("SELECT 							
								Id,
								C004_Id,
								COUNT(*) AS Movimentacoes
							FROM (
									SELECT 
											T084_Id AS Id,
											T084_C004_Id AS C004_Id
										FROM T084 
									LEFT JOIN D006 ON D006_Id = T084_D006_Id
									WHERE T084_Data_Lancamento >= '{$dataInicial}' AND T084_Data_Lancamento <= '{$dataFinal}'   
										AND T084_C004_Id IN ($matrizEFiliais)  AND T084_Flag_Cancelado != 'S'
										AND T084_Numero_Protocolo_Cte > 0														
										AND (D006_Flag_Isento_PIS != 'S'  or D006_Flag_Isento_COFINS != 'S')
										AND T084_Modelo in('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')
								union
									SELECT 
											T013_Id AS Id,
											T013_C004_Id AS C004_Id
										FROM T013
									LEFT JOIN D006 ON D006_Id = T013_D006_Id
									LEFT JOIN T013A ON T013_Id = T013A_T013_Id
									WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
									    AND {$dataNFRecebida} <= '{$dataFinalHora}'
										AND T013_C004_Id IN ($matrizEFiliais)
										AND T013_Flag_Cancelada != 'S'
										AND T013_Flag_Estoque_Finalizado = 'S'
										AND T013_Tipo IN ('7','8')
										AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
										AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							) AS documentos
							GROUP BY C004_Id");


// Verifica se existem conhecimentos para popular o Indicador de Movimentação
$IND_MOV = (mysql_num_rows($T084Movimento) > 0) ? '0' : '1';

$C004 = mysql_query("SELECT C004_Flag_Incidencia_Tributaria
					   FROM C004 
					  WHERE C004_Id = '{$matriz}'");
$C004 = mysql_fetch_array($C004);

if($C004['C004_Flag_Incidencia_Tributaria'] == '2'){
	//A empresa que apura o imposto na incidência “Cumulativo”, não apropria crédito sobre os conhecimentos. 
	//Por isso, CTEs não são consideradas na apuração do imposto.
	$IND_MOV = '1';
}

# REGISTRO D001
$D001 = array(											#TAM								
	$REG = 'D001',										#004* 
	$IND_MOV											#001	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
);
$D001 = str_replace('|', '', $D001);
$D001 = implode("|", $D001);
$layout .= '|' . $D001 . '|' . _SALTO_LINHA;

if ($IND_MOV == '0') {
	// Caso existam conhecimentos
	while($mT084Movimento = mysql_fetch_array($T084Movimento))
	{	
		$empresa = $mT084Movimento['C004_Id'];

		$C004 = mysql_query("SELECT C004_Cnpj 
							FROM C004 
							WHERE C004_Id = '{$empresa}'");
		$C004 = mysql_fetch_array($C004);

		# REGISTRO D010
		$D010 = array(											#TAM							
			$REG = 'D010',										#004*
			$CNPJ = removerCharEspecial($C004['C004_Cnpj']),	#014* 	Número de inscrição do estabelecimento no CNPJ.		
		);
		$D010 = str_replace('|', '', $D010);
		$D010 = implode("|", $D010);
		$layout .= '|' . $D010 . '|' . _SALTO_LINHA;	

		// Buscar todos os conhecimentos emitidos no período
		$sqlT084 = mysql_query("SELECT
									Transportadora.D024_Id as Transportadora,
									Transportadora.D024_Cnpj as Transportadora_Cnpj,
									C004_Cnpj,
									T084_Modelo,
									T084_Situacao,
									T084_Serie,
									T084_Subserie,
									T084_Tipo_Conhecimento,
									T084_Indicador_Tipo_Frete,
									T084_Numero_Conhecimento,
									T084_Chave_Acesso_Cte,
									T084_Data_Emissao,
									T084_Data_Lancamento,
									T084_Valor_Conhecimento,
									T084_Valor_Base_ICMS,
									T084_Aliquota_ICMS,
									T084_Valor_ICMS,		
									T084_Valor_Base_PIS,
									T084_Aliquota_PIS,
									T084_Valor_PIS,
									T084_Valor_Base_COFINS,
									T084_Aliquota_COFINS,
									T084_Valor_COFINS,
									contaCFOP.D014_Codigo_Plano_Conta_Fiscal AS D006_D014_Codigo_Plano_Conta_Fiscal, 
									contaCab.D014_Codigo_Plano_Conta_Fiscal AS T084A_D014_Codigo_Plano_Conta_Fiscal,
									T084A_Chave_Acesso_Cte_Original,
									T084_Indicador_Natureza_Frete,
									T084_Base_Calculo_Credito,
									D006_Situacao_Tributaria_PIS,
									D006_Situacao_Tributaria_COFINS,
									C004_Percentual_PIS,
									C004_Percentual_COFINS,
									contaCFOP.D014_Id AS D006_D014_Id,
									contaCab.D014_Id AS T084A_D014_Id
								FROM T084
									LEFT JOIN D022 ON D022_Id = T084_D022_Id
									LEFT JOIN D024 as Transportadora ON Transportadora.D024_Cnpj = D022_Cnpj
									LEFT JOIN C004 ON C004_Id = T084_C004_Id  
									LEFT JOIN D024 as ClienteFornecedor ON ClienteFornecedor.D024_Id = T084_D024_Id  
									LEFT JOIN D006 ON D006_Id = T084_D006_Id
                        			LEFT JOIN D014 as contaCFOP ON contaCFOP.D014_ID = D006_D014_Id
									LEFT JOIN D059 ON D059_Id = D006_D059_Id
									LEFT JOIN T084A ON T084A_T084_Id = T084_Id
                         			LEFT JOIN D014 as contaCab ON contaCab.D014_ID = T084A_D014_Id
								WHERE T084_Data_Lancamento >= '{$dataInicial}' AND T084_Data_Lancamento <= '{$dataFinal}'
									AND T084_C004_Id='{$empresa}'  AND T084_Flag_Cancelado != 'S'
									AND T084_Flag_Entrada_Saida = 'E'
									AND T084_Numero_Protocolo_Cte > 0
									AND (D006_Flag_Isento_PIS != 'S'  OR D006_Flag_Isento_COFINS != 'S')
									AND T084_Modelo IN('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')");

		while ($T084 = mysql_fetch_array($sqlT084)) {
			
			$IND_OPER 		= '0';
			$IND_EMIT       = '1';
			$COD_PART       = $T084['Transportadora']; // Como é aquisição será sempre a transportadora

			//usa a aliquota vinda da CFOP e não do XML 
			$VL_BC_ICMS     = $T084['T084_Valor_Base_ICMS'];
			$ALIQ_ICMS      = $T084['T084_Aliquota_ICMS'];
			$VL_ICMS        = $T084['T084_Valor_ICMS'];

			$D014_Codigo_Plano_Conta_Fiscal = $T084['D006_D014_Codigo_Plano_Conta_Fiscal'];
			if(!empty($T084['T084A_D014_Codigo_Plano_Conta_Fiscal'])){
				$D014_Codigo_Plano_Conta_Fiscal = $T084['T084A_D014_Codigo_Plano_Conta_Fiscal'];
			}

			# REGISTRO D100
			$D100 = array(                                                                          #TAM
				$REG = 'D100',                                                                      #004	
				$IND_OPER,                                                                          #001*	Indicador do tipo de operação (0 Aquisição)				   
				$IND_EMIT,                                                                          #001*   Indicador do emitente do documento fiscal (0 Emissão própria/1 Terceiros)
				$COD_PART,                                                                          #060    Código do participante (campo 02 do Registro 0150)
				$COD_MOD = $T084['T084_Modelo'],                                                    #002*   Código do modelo do documento fiscal
				$COD_SIT = $T084['T084_Situacao'],                                                  #002*   Código da situação do documento fiscal
				$SER = $T084['T084_Serie'],                                                         #004    Série do documento fiscal                                                        
				$SUB = $T084['T084_Subserie'],                                                      #003    Subsérie do documento fiscal
				$NUM_DOC = $T084['T084_Numero_Conhecimento'],                                       #009    Número do documento fiscal    
				$CHV_CTE = $T084['T084_Chave_Acesso_Cte'],                                          #044*   Chave do Conhecimento de Transporte Eletrônico    
				$DT_DOC = formataData(substr($T084['T084_Data_Emissao'], 0, 10)),                   #008*   Data de referência/emissão dos documentos fiscais
				$DT_A_P = formataData(substr($T084['T084_Data_Lancamento'], 0, 10)),                #008*   Data da aquisição ou da prestação do serviço
				$TP_CTe = $T084['T084_Tipo_Conhecimento'],                                          #001*   Tipo de Conhecimento de Transporte Eletrônico conforme definido no Manual de Integração do CT-e ou do Bilhete de Passagem Eletrônico conforme definido no Manual de Integração do BP-e 
				$CHV_CTE_REF = $T084['T084A_Chave_Acesso_Cte_Original'],                            #044*   Chave do CT-e ou Bilhete de Passagem Eletrônico substituído
				$VL_DOC = formataDecimal($T084['T084_Valor_Conhecimento']),                         #       Valor total do documento fiscal
				formataDecimal($VL_DESC),															#       Valor total do desconto
				$IND_FRT = $T084['T084_Indicador_Tipo_Frete'],                                      #001*   Indicador do tipo do frete
				$VL_SERV = formataDecimal($T084['T084_Valor_Conhecimento']),                        #       Valor total da prestação de serviço
				formataDecimal($VL_BC_ICMS),                                                        #       Valor da base de cálculo do ICMS
				formataDecimal($VL_ICMS),                                                           #       Valor do ICMS
				$VL_NT,                                                                             #       Valor não-tributado
				$COD_INF,                                                                           #       Código da informação complementar do documento
				$COD_CTA = $D014_Codigo_Plano_Conta_Fiscal,                                         #       Código da conta analítica contábil debitada/creditada			
			);
			$D100 = str_replace('|', '', $D100);
			$D100 = implode("|", $D100);
			$layout .= '|' . $D100 . '|' . _SALTO_LINHA;
			
		
			$D014_Id = $T084['D006_D014_Id'];
			if(!empty($T084['T084A_D014_Id'])){
				$D014_Id = $T084['T084A_D014_Id'];
			}
			verificaRegistro0500($D014_Id);	
			verificaRegistro0150($T084['Transportadora'], $empresa);

			
			
			$VL_BC_PIS     	= $T084['T084_Valor_Base_PIS'];
			$ALIQ_PIS       = $T084['T084_Aliquota_PIS'];    
			$VL_PIS        	= $T084['T084_Valor_PIS'];

			# REGISTRO D101
			$D101 = array(																			#TAM														
				$REG = 'D101',																		#004	
				$IND_NAT_FRT = $T084['T084_Indicador_Natureza_Frete'],								#001* 	Indicador da Natureza do Frete Contratado
				$VL_ITEM = formataDecimal($T084['T084_Valor_Conhecimento']),						#		Valor total dos itens
				$CST_PIS = removerCharEspecial($T084['D006_Situacao_Tributaria_PIS']),				#002*	Código da Situação Tributária referente ao PIS/PASEP 
				$NAT_BC_CRED = $T084['T084_Base_Calculo_Credito'],									#002*	Código da Base de Cálculo do Crédito, conforme a Tabela indicada no item 4.3.7.
				formataDecimal($VL_BC_PIS),															#		Valor da base de cálculo do PIS/PASEP
				formataDecimal($ALIQ_PIS),															#008	Alíquota do PIS/PASEP (em percentual)
				formataDecimal($VL_PIS),															#		Valor do PIS/PASEP
				$COD_CTA = $D014_Codigo_Plano_Conta_Fiscal,									        #255	Código da conta analítica contábil debitada/creditada
			);		
			$D101 = str_replace('|', '', $D101);
			$D101 = implode("|", $D101);
			$layout .= '|' . $D101 . '|' . _SALTO_LINHA;


			$VL_BC_COFINS		= $T084['T084_Valor_Base_COFINS'];
			$ALIQ_COFINS		= $T084['T084_Aliquota_COFINS'];    
			$VL_COFINS			= $T084['T084_Valor_COFINS'];        

			# REGISTRO D105
			$D105 = array(																			#TAM														
				$REG = 'D105',																		#004	
				$IND_NAT_FRT = $T084['T084_Indicador_Natureza_Frete'],								#001* 	Indicador da Natureza do Frete Contratado
				$VL_ITEM = formataDecimal($T084['T084_Valor_Conhecimento']),						#		Valor total dos itens
				$CST_COFINS = removerCharEspecial($T084['D006_Situacao_Tributaria_COFINS']),		#002*	Código da Situação Tributária referente a COFINS
				$NAT_BC_CRED = $T084['T084_Base_Calculo_Credito'],									#002*	Código da Base de Cálculo do Crédito, conforme a Tabela indicada no item 4.3.7.
				formataDecimal($VL_BC_COFINS),														#		Valor da base de cálculo da COFINS
				formataDecimal($ALIQ_COFINS),														#008	Alíquota da COFINS (em percentual)
				formataDecimal($VL_COFINS),															#		Valor da COFINS
				$COD_CTA = $D014_Codigo_Plano_Conta_Fiscal,				       					    #255	Código da conta analítica contábil debitada/creditada
			);		
			$D105 = str_replace('|', '', $D105);
			$D105 = implode("|", $D105);
			$layout .= '|' . $D105 . '|' . _SALTO_LINHA;
		}


		$sqlT013 = mysql_query("SELECT
									T013_Id,
									T013_Tipo,
									T013_D024_Id,
									T013_Numero_Nota_Fiscal,
									T013_Chave_Acesso_Nfe,
									T013_Data_Emissao,
									DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
									T013_Valor_Total, 
									T013_Valor_Total_ICMS,
									T013_Valor_Total_PIS,
									T013_Valor_Total_COFINS,
									T013_Valor_Desconto,
									T013_Valor_Despesas_Acessorias,
									T013_Valor_Total_Base_ICMS,
									T013_Valor_Total_Produtos,
									sum(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T014_Valor_Base_ICMS_Sem_Reducao
								from
									T014
									left join T013 ON T013_Id = T014_T013_Id	
									left join T013A ON T013_Id = T013A_T013_Id							
								where
									{$dataNFRecebida} >= '{$dataInicialHora}'
									and {$dataNFRecebida} <= '{$dataFinalHora}'
									and T013_C004_Id = '{$empresa}'
									and T013_Flag_Cancelada != 'S'
									and T013_Flag_Estoque_Finalizado = 'S'
									and T013_Tipo IN ('7','8')
									and (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
									and IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'								
								group by T013_Id");

		while($T013 = mysql_fetch_array($sqlT013)) {				
					
	        $COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');
			
			$VL_SERV_NT = formataDecimal($T013['T014_Valor_Base_ICMS_Sem_Reducao'] - $T013['T013_Valor_Total_Base_ICMS']);

			# Registro D500
			$D500 = array(																#TAM
				$REG  = "D500",																	
				$IND_OPER = '0',																	
				$IND_EMIT = '1',																	
				$COD_PART = $T013['T013_D024_Id'],																	
				$COD_MOD,																	
				$COD_SIT = '00',																	
				$SER,																	
				$SUB,																	
				$NUM_DOC = $T013['T013_Numero_Nota_Fiscal'],																	
				$DT_DOC	 = formataData(substr($T013['T013_Data_Emissao'],0,10)),																	
				$DT_A_P	 = formataData(substr($T013[$dataNFRecebidaLancarSPED],0,10)),																	
				$VL_DOC	 = formataDecimal($T013['T013_Valor_Total']),																	
				$VL_DESC = formataDecimal($T013['T013_Valor_Desconto']),																	
				$VL_SERV = formataDecimal($T013['T013_Valor_Total_Produtos']),																	
				$VL_SERV_NT,																	
				$VL_TERC = '0,00',																	
				$VL_DA  = formataDecimal($T013['T013_Valor_Despesas_Acessorias']),																	
				$VL_BC_ICMS = formataDecimal($T013['T013_Valor_Total_Base_ICMS']),																	
				$VL_ICMS 	= formataDecimal($T013['T013_Valor_Total_ICMS']),																	
				$COD_INF,																	
				$VL_PIS 	= formataDecimal($T013['T013_Valor_Total_PIS']),																	
				$VL_COFINS	= formataDecimal($T013['T013_Valor_Total_COFINS']),
				$CHV_DOC_E  = $T013['T013_Chave_Acesso_Nfe'],
			);

			$D500 = str_replace('|', '', $D500);
			$D500 = implode("|", $D500);
			$layout .= '|' . $D500 . '|' . _SALTO_LINHA;
			verificaRegistro0150($T013['T013_D024_Id'], $empresa);


			$sqlT014  = mysql_query("SELECT
										T014_Valor_Total_Custo,
										T014_Situacao_Tributaria_PIS, 																											
										T014_Valor_Base_PIS,
										T014_Aliquota_PIS,     
										T014_Valor_PIS,									         
										D014_Codigo_Plano_Conta_Fiscal,
										D006_Base_Calculo_Credito,
										D014_Id,
										T014A_D014_Id
									from
										T014																											
										left join T013 on T013_Id = T014_T013_Id
										left join D006 on D006_Id = T014_D006_Id
										left join D014 on D014_Id = D006_D014_Id
										left join T014A on T014_Id = T014A_T014_Id
									where
										T014_T013_Id = '{$T013['T013_Id']}' 
										and D006_Flag_Deb_Crd_PIS = 'S'");

			while($T014 = mysql_fetch_array($sqlT014)) {

				if($T014['T014A_D014_Id'] > 0){
					$D014 = mysql_query("SELECT D014_Id,
												D014_Codigo_Plano_Conta_Fiscal 
										   FROM D014
										  WHERE D014_Id = '{$T014['T014A_D014_Id']}'");
					$mD014 = mysql_fetch_array($D014);

					$T014['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
					$T014['D014_Id']					    = $mD014['D014_Id'];
				}

				$D501 = array(															#TAM
					$REG = "D501",														#004*	Texto fixo contendo "D501”
					$CST_PIS = $T014['T014_Situacao_Tributaria_PIS'],					#002*	Código da Situação Tributária referente ao PIS/PASEP	
					$VL_ITEM = formataDecimal($T014['T014_Valor_Total_Custo']),			#		Valor total dos itens
					$NAT_BC_CRED = $T014['D006_Base_Calculo_Credito'],					#002*	Código da Base de Cálculo do Crédito
					$VL_BC_PIS = formataDecimal($T014['T014_Valor_Base_PIS']),			#		Valor da base de cálculo do PIS/PASEP
					$ALIQ_PIS = formataDecimal($T014['T014_Aliquota_PIS']),				#008	Alíquota do PIS/PASEP (em percentual)
					$VL_PIS = formataDecimal($T014['T014_Valor_PIS']),					#		Valor do PIS/PASEP
					$COD_CTA = $T014['D014_Codigo_Plano_Conta_Fiscal'],					#255	Código da conta analítica contábil debitada/creditada	
				);
				
				
				$D501 = str_replace('|', '', $D501);
				$D501 = implode("|", $D501);
				$layout .= '|' . $D501 . '|' . _SALTO_LINHA;
				verificaRegistro0500($T014['D014_Id']);
			}

			$sqlT014  = mysql_query("SELECT
										T014_Valor_Total_Custo,									
										T014_Situacao_Tributaria_COFINS,   
										T014_Aliquota_COFINS,
										T014_Valor_COFINS,
										T014_Valor_Base_COFINS,									            
										D014_Codigo_Plano_Conta_Fiscal,
										D006_Base_Calculo_Credito,
										D014_Id,
										T014A_D014_Id
									from
										T014																											
										left join T013 on T013_Id = T014_T013_Id
										left join D006 on D006_Id = T014_D006_Id
										left join D014 on D014_Id = D006_D014_Id
										left join T014A on T014_Id=T014A_T014_Id
									where T014_T013_Id = '{$T013['T013_Id']}' 
										and D006_Flag_Deb_Crd_COFINS = 'S'");

			while($T014 = mysql_fetch_array($sqlT014)) {	
				
				if($T014['T014A_D014_Id'] > 0){
					$D014 = mysql_query("SELECT D014_Id,
												D014_Codigo_Plano_Conta_Fiscal 
										   FROM D014
										  WHERE D014_Id = '{$T014['T014A_D014_Id']}'");
					$mD014 = mysql_fetch_array($D014);

					$T014['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
					$T014['D014_Id']					    = $mD014['D014_Id'];
				}

				$D505 = array(															#TAM
					$REG = "D505",														#004*	Texto fixo contendo "D505”
					$CST_COFINS = $T014['T014_Situacao_Tributaria_COFINS'],				#002*	Código da Situação Tributária referente a COFINS
					$VL_ITEM = formataDecimal($T014['T014_Valor_Total_Custo']),			#		Valor total dos itens
					$NAT_BC_CRED = $T014['D006_Base_Calculo_Credito'],					#002*	Código da Base de Cálculo do Crédito
					$VL_BC_COFINS = formataDecimal($T014['T014_Valor_Base_COFINS']),	#		Valor da base de cálculo da COFINS
					$ALIQ_COFINS = formataDecimal($T014['T014_Aliquota_COFINS']),		#008	Alíquota da COFINS (em percentual)
					$VL_COFINS = formataDecimal($T014['T014_Valor_COFINS']),			#		Valor da COFINS
					$COD_CTA = $T014['D014_Codigo_Plano_Conta_Fiscal'],					#255	Código da conta analítica contábil debitada/creditada 
				);

				
				$D505 = str_replace('|', '', $D505);
				$D505 = implode("|", $D505);
				$layout .= '|' . $D505 . '|' . _SALTO_LINHA;
				verificaRegistro0500($T014['D014_Id']);
			}
		}
	}
}

$linhas = substr_count($layout, CHR(13) . CHR(10));

$linhas -= $linhas_bloco_C;

# REGISTRO D990
$D990 = array(
	$REG = 'D990',
	$QTD_LIN_D = $linhas + 1
);
$D990 = str_replace('|', '', $D990);
$D990 = implode("|", $D990);
$layout .= '|' . $D990 . '|' . _SALTO_LINHA;
#----------------------------------------------

$layout .= '|F001|1|' . _SALTO_LINHA;
$layout .= '|F990|2|' . _SALTO_LINHA;

$linhas_bloco_D = $QTD_LIN_D + $linhas_bloco_C;
