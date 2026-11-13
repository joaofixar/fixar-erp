<?php

namespace hardness;

global $g;
/*
# BLOCO A # INFORMAÇÕES PRINCIPAIS #
REG 	 OBRIG		DESCRIÇÃO
A001		O 		Abertura do Bloco A 
A010		O		Identificação do Estabelecimento O (se em A001 IND_MOV = 0)
A100		OC		Documento – Nota Fiscal de Serviço
A170		O		Complemento de Documento – Itens do Documento	O (se existir A100)
A990		O		Encerramento do Bloco A
*/

$notasServico = mysql_query("SELECT
								T013_C004_Id,
								COUNT(*) AS Movimentacoes					
						FROM(
								SELECT 	T013_Id, 
										T013_C004_Id
									FROM T013
								LEFT JOIN T013A ON T013_Id = T013A_T013_Id						
								WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
									AND {$dataNFRecebida} <= '{$dataFinalHora}' 
									AND T013_C004_Id  IN ($matrizEFiliais)
									AND T013_Flag_Cancelada != 'S' 										
									AND T013_Flag_Estoque_Finalizado = 'S' 
									AND T013_Tipo = '10'									
									AND (T013_Valor_Total_PIS > 0 OR T013_Valor_Total_COFINS > 0)
									AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
							UNION
								SELECT 	T007_Id AS T013_Id,
										T007_C004_Id AS T013_C004_Id
								FROM T007
								LEFT JOIN T007A ON T007_Id = T007A_T007_Id
								WHERE T007_Data_Emissao >= '{$dataInicial}' 
									AND T007_Data_Emissao <= '{$dataFinal}' 
									AND T007_C004_Id  IN ($matrizEFiliais)
									AND T007_Flag_Cancelada != 'S' 									
									AND T007_Flag_Travar_Nota = 'S'
									AND T007_Flag_ACP = '7'
									AND (T007_Valor_Total_PIS > 0 OR T007_Valor_Total_COFINS > 0)
									AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
						) as notasServico
					GROUP BY T013_C004_Id
");


$IND_MOV = mysql_num_rows($notasServico) > 0 ? '0' : '1';

# REGISTRO A001
$A001 = array(											#TAM								
	$REG = 'A001',										#004* 
	$IND_MOV											#001	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
);
$A001 = str_replace('|', '', $A001);
$A001 = implode("|", $A001);
$layout .= '|' . $A001 . '|' . _SALTO_LINHA;


if($IND_MOV == '0') {	

	while($mNotasServico = mysql_fetch_array($notasServico)) {

		$empresa = $mNotasServico['T013_C004_Id'];
		
		$C004 = mysql_query("SELECT C004_Cnpj 
								FROM C004 
							WHERE C004_Id = '{$empresa}'");
		$mC004 = mysql_fetch_array($C004);

		$mC004['C004_Cnpj'] = removerCharEspecial($mC004['C004_Cnpj']);

		# REGISTRO A010
		$A010 = array(											#TAM								
			$REG = 'A010',										#004* 
			$CNPJ = $mC004['C004_Cnpj']							#014* Número de inscrição do estabelecimento no CNPJ
		);
		$A010 = str_replace('|', '', $A010);
		$A010 = implode("|", $A010);
		$layout .= '|' . $A010 . '|' . _SALTO_LINHA;


		$T013 = mysql_query("SELECT
								T007_Id,
								T013_Id, 
								T013_Numero_Nota_Fiscal,							
								T013_D024_Id,  
								T013_Chave_Acesso_Nfe,
								T013_Data_Emissao,
								DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
								T013_Valor_Total,        
								T013_Valor_Total_PIS,
								T013_Valor_Total_COFINS,             
								T013_Valor_Desconto,
								Base_PIS,
								Base_COFINS,
								NumeroParcelas
							FROM(	
								SELECT  
									0 as T007_Id, 
									T013_Id, 
									T013_Numero_Nota_Fiscal,								
									T013_D024_Id,  
									T013_Chave_Acesso_Nfe,
									T013_Data_Emissao,
									DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
									T013_Valor_Total,        
									T013_Valor_Total_PIS,
									T013_Valor_Total_COFINS,             
									T013_Valor_Desconto,
									SUM(T014_Valor_Base_PIS) as Base_PIS,
									SUM(T014_Valor_Base_COFINS) as Base_COFINS,
									(SELECT COUNT(T154_Id) FROM T154 WHERE T154_T013_Id = T013_Id) as NumeroParcelas
								FROM T013
									LEFT JOIN T014 ON T014_T013_Id = T013_Id
									LEFT JOIN T013A ON T013A_T013_Id = T013_Id
								WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
									AND {$dataNFRecebida} <= '{$dataFinalHora}' 
									AND T013_C004_Id = '{$empresa}' 
									AND T013_Flag_Cancelada != 'S' 										
									AND T013_Flag_Estoque_Finalizado = 'S' 
									AND T013_Tipo = '10'
									AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
									AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								GROUP BY T013_Id
							UNION
								SELECT  
										T007_Id, 
										0 as T013_Id,
										T007_Numero_Nota_Fiscal as T013_Numero_Nota_Fiscal,									
										T007_D024_Id as T013_D024_Id,  
										T007_Chave_Acesso_Nfe as T013_Chave_Acesso_Nfe,
										T007_Data_Emissao as T013_Data_Emissao,
										T007_Data_Saida as {$dataNFRecebidaLancarSPED},
										T007_Valor_Total as T013_Valor_Total,        
										T007_Valor_Total_PIS as T013_Valor_Total_PIS,
										T007_Valor_Total_COFINS as T013_Valor_Total_COFINS,             
										T007_Valor_Desconto as T013_Valor_Desconto,
										SUM(T008_Valor_Base_PIS) as Base_PIS,
										SUM(T008_Valor_Base_COFINS) as Base_COFINS,
										(SELECT COUNT(T118_Id) FROM T118 WHERE T118_T007_Id = T007_Id) as NumeroParcelas
									FROM T007
										LEFT JOIN T008 ON T008_T007_Id = T007_Id
										LEFT JOIN T007A ON T007A_T007_Id = T007_Id
									WHERE T007_Data_Emissao >= '{$dataInicial}' 
										AND T007_Data_Emissao <= '{$dataFinal}' 
										AND T007_C004_Id = '{$empresa}'									
										AND T007_Flag_Cancelada != 'S' 									
										AND T007_Flag_Travar_Nota = 'S'
										AND T007_Flag_ACP = '7'
										AND (T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0)
										AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									GROUP BY T007_Id							
						) as notas");

		
		while($mT013 = mysql_fetch_array($T013)) {

			$IND_OPER 		= $mT013['T013_Id'] == 0 ? 1 : 0;
			$IND_EMIT       = $IND_OPER == 1 ? 0 : 1;
			
			if($mT013['NumeroParcelas'] > 1) {
				$IND_PGTO = 1; //A prazo
			} else if($mT013['NumeroParcelas'] == 1) {
				$IND_PGTO = 0;//À vista
			} else {
				$IND_PGTO = 9;// Sem pagamento
			}		

			# REGISTRO A100
			$A100 = array(				   													#TAM
				$REG = 'A100',			   													#004						
				$IND_OPER, 				   													#001*	Indicador do tipo de operação: 0 - Serviço Contratado pelo Estabelecimento;1 - Serviço Prestado pelo Estabelecimento.			
				$IND_EMIT,				   													#001*	Indicador do emitente do documento fiscal: 0 - Emissão Própria; 1 - Emissão de Terceiros
				$COD_PART = $mT013['T013_D024_Id'],											#060	Código do participante (campo 02 do Registro 0150)		
				$COD_SIT = '00',		   													#002*	Código da situação do documento fiscal: 00 – Documento regular 02 – Documento cancelado
				$SER = '',					   												#003	Série do documento fiscal			
				$SUB = '',					   												#003	Série do documento fiscal			
				$NUM_DOC = $mT013['T013_Numero_Nota_Fiscal'],	   							#009	Número do documento fiscal ou documento internacional equivalente
				$CHV_NFSE = '',		   														#044*	Chave/Código de Verificação da nota fiscal de serviço eletrônica
				$DT_DOC = formataData(substr($mT013['T013_Data_Emissao'],0,10)),			#008*	Data da emissão do documento fiscal 				
				$DT_EXE_SERV = formataData($mT013[$dataNFRecebidaLancarSPED]),			    #008*	Data de Execução / Conclusão do Serviço
				$VL_DOC = formataDecimal($mT013['T013_Valor_Total']),						#		Valor total do documento fiscal			
				$IND_PGTO,		   															#001*	Indicador do tipo de pagamento: 0- À vista; 1- A prazo; 9- Sem pagamento.			
				$VL_DESC = formataDecimal($mT013['T013_Valor_Desconto']),					#		Valor total do desconto			
				$VL_BC_PIS = formataDecimal($mT013['Base_PIS']),							#		Valor da base de cálculo do PIS/PASEP 		
				$VL_PIS = formataDecimal($mT013['T013_Valor_Total_PIS']),					#		Valor total do PIS 
				$VL_BC_COFINS = formataDecimal($mT013['Base_COFINS']),						#		Valor da base de cálculo da COFINS 			
				$VL_COFINS = formataDecimal($mT013['T013_Valor_Total_COFINS']),				#		Valor total da COFINS						
				$VL_PIS_RET = 0,															#		Valor total do PIS retido na fonte
				$VL_COFINS_RET = 0,															#		Valor total da COFINS retido na fonte
				$VL_ISS = 0																	#		Valor do ISS
			);
			$A100 = str_replace('|', '', $A100);
			$A100 = implode("|", $A100);
			$layout .= '|' . $A100 . '|' . _SALTO_LINHA;
			verificaRegistro0150($mT013['T013_D024_Id'],$empresa);

			$T014 = mysql_query("SELECT
									D001_Codigo_Produto,
									D001_Descricao_Produto,
									T014_Valor_Total_Custo,
									T014_Valor_Total_Desconto,								
									T014_Situacao_Tributaria_PIS,
									T014_Valor_Base_PIS,
									T014_Aliquota_PIS,
									T014_Valor_PIS,
									T014_Situacao_Tributaria_COFINS, 
									T014_Valor_Base_COFINS, 
									T014_Aliquota_COFINS, 
									T014_Valor_COFINS,
									D014_Codigo_Plano_Conta_Fiscal,
									D006_Base_Calculo_Credito,
									D014_Id,
									T014A_D014_Id		
								FROM(SELECT
											D001_Codigo_Produto,
											D001_Descricao_Produto,
											T014_Valor_Total_Custo,
											T014_Valor_Total_Desconto,										
											T014_Situacao_Tributaria_PIS,
											T014_Valor_Base_PIS,
											T014_Aliquota_PIS,
											T014_Valor_PIS,
											T014_Situacao_Tributaria_COFINS, 
											T014_Valor_Base_COFINS, 
											T014_Aliquota_COFINS, 
											T014_Valor_COFINS,
											D014_Codigo_Plano_Conta_Fiscal,
											D006_Base_Calculo_Credito,
											D014_Id,
											T014A_D014_Id								
										FROM T014
											LEFT JOIN D009 ON D009_Id = T014_D009_Id
											LEFT JOIN D049 ON D049_Id = D009_D049_Id
											LEFT JOIN D001 ON D001_Id = D049_D001_Id							
											LEFT JOIN T013 ON T013_Id = T014_T013_Id
											LEFT JOIN D006 ON D006_Id = T014_D006_Id
											LEFT JOIN D014 ON D014_Id = D006_D014_Id
											left join T014A on T014_Id=T014A_T014_Id
										WHERE T014_T013_Id = '{$mT013['T013_Id']}'
										GROUP BY T014_Id
									UNION ALL
									SELECT
											D001_Codigo_Produto,
											D001_Descricao_Produto,
											T008_Valor_Total_Preco_Sem_Desconto as T014_Valor_Total_Custo,
											T008_Valor_Total_Desconto as T014_Valor_Total_Desconto,										
											T008_Situacao_Tributaria_PIS as T014_Situacao_Tributaria_PIS,
											T008_Valor_Base_PIS as T014_Valor_Base_PIS,
											T008_Aliquota_PIS as T014_Aliquota_PIS,
											T008_Valor_PIS as T014_Valor_PIS,
											T008_Situacao_Tributaria_COFINS as T014_Situacao_Tributaria_COFINS, 
											T008_Valor_Base_COFINS as T014_Valor_Base_COFINS, 
											T008_Aliquota_COFINS as T014_Aliquota_COFINS, 
											T008_Valor_COFINS as T014_Valor_COFINS,
											D014_Codigo_Plano_Conta_Fiscal,
											D006_Base_Calculo_Credito,
											D014_Id,
											T008A_D014_Id AS T014A_D014_Id								
										FROM T008
											LEFT JOIN D009 ON D009_Id = T008_D009_Id
											LEFT JOIN D049 ON D049_Id = D009_D049_Id
											LEFT JOIN D001 ON D001_Id = D049_D001_Id							
											left join T007 on T007_Id = T008_T007_Id
											left join D006 on D006_Id = T008_D006_Id
											left join D014 on D014_Id = D006_D014_Id
											left join T008A on T008_Id=T008A_T008_Id
										WHERE T008_T007_Id = '{$mT013['T007_Id']}'
										GROUP BY T008_Id) as servicos");

			$count = 1;
			while($mT014 = mysql_fetch_array($T014)) {

				$NUM_ITEM = $count;
				$IND_ORIG_CRED = 0;//Mercado Interno
				$NAT_BC_CRED = $mT014['D006_Base_Calculo_Credito'];

				// O campo somente deve ser preenchido nos casos de serviços contratados pelo estabelecimento
				if($IND_OPER == 1) { // Prestacao de Serviço / Saida
					$NAT_BC_CRED = '';
					$IND_ORIG_CRED = '';
				}

				if($mT014['T014A_D014_Id'] > 0){
					$D014 = mysql_query("SELECT D014_Id,
												D014_Codigo_Plano_Conta_Fiscal 
										   FROM D014
										  WHERE D014_Id = '{$mT014['T014A_D014_Id']}'");
					$mD014 = mysql_fetch_array($D014);

					$mT014['D014_Codigo_Plano_Conta_Fiscal'] = $mD014['D014_Codigo_Plano_Conta_Fiscal'];
					$mT014['D014_Id']					     = $mD014['D014_Id'];
				}

				// # REGISTRO A170	
				$A170 = array(																			#TAM								
					$REG = 'A170',																		#004* 
					$NUM_ITEM,																			#		Número seqüencial do item no documento fiscal
					$COD_ITEM = $mT014['D001_Codigo_Produto'],											#060	Código do item (campo 02 do Registro 0200)
					$DESCR_COMPL = $mT014['D001_Descricao_Produto'],									#		Descrição complementar do item como adotado no documento fiscal	
					$VL_ITEM = formataDecimal($mT014['T014_Valor_Total_Custo']),						#		Valor total do item (mercadorias ou serviços) 					
					$VL_DESC = formataDecimal($mT014['T014_Valor_Total_Desconto']),						#		Valor do desconto comercial / exclusão da base de cálculo do PIS/PASEP e da COFINS						
					$NAT_BC_CRED,																		#		Código da base de cálculo do crédito, conforme a Tabela indicada no item 4.3.7
					$IND_ORIG_CRED,																		#001*	Indicador da origem do crédito:0 – Operação no Mercado Interno; 1 – Operação de Importação
					$CST_PIS = $mT014['T014_Situacao_Tributaria_PIS'],									#002*	Código da Situação Tributária referente ao PIS/PASEP
					$VL_BC_PIS = formataDecimal($mT014['T014_Valor_Base_PIS']),							#		Valor da base de cálculo do PIS/PASEP					
					$ALIQ_PIS =formataDecimal( $mT014['T014_Aliquota_PIS']),							#		Alíquota do PIS/PASEP (em percentual) 					
					$VL_PIS = formataDecimal($mT014['T014_Valor_PIS']),									#		Valor do PIS/PASEP		
					$CST_COFINS = $mT014['T014_Situacao_Tributaria_COFINS'],							#002*	Código da Situação Tributária referente ao COFINS 					
					$VL_BC_COFINS = formataDecimal($mT014['T014_Valor_Base_COFINS']),					#		Valor da base de cálculo da COFINS						
					$ALIQ_COFINS = formataDecimal($mT014['T014_Aliquota_COFINS']),						#006	Alíquota do COFINS (em percentual)					
					$VL_COFINS = formataDecimal($mT014['T014_Valor_COFINS']),							#		Valor da COFINS					
					$COD_CTA = $mT014['D014_Codigo_Plano_Conta_Fiscal'],								#255	Código da conta analítica contábil debitada/creditada				
					$COD_CCUS = ''																		#255	Código do centro de custos
				);
				$count++;
				$A170 = str_replace('|', '', $A170);
				$A170 = implode("|", $A170);
				$layout .= '|' . $A170 . '|' . _SALTO_LINHA;
				verificaRegistro0500($mT014['D014_Id']);	
			}
		}	
	}
}


$linhas = substr_count($layout, CHR(13) . CHR(10));
$linhas -= $linhas_bloco_0;

# REGISTRO A990
$A990 = array(
	$REG = 'A990',
	$QTD_LIN_A = $linhas + 1
);
$A990 = str_replace('|', '', $A990);
$A990 = implode("|", $A990);
$layout .= '|' . $A990 . '|' . _SALTO_LINHA;
#----------------------------------------------


$linhas_bloco_A = $QTD_LIN_A + $linhas_bloco_0;
