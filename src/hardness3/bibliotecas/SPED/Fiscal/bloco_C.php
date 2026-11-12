<?php
namespace hardness;
global $g;
/*
# BLOCO C # INFORMAÇÕES PRINCIPAIS #
REG 	 OBRIG		DESCRIÇÃO
C001	 O			Abertura do Bloco C
C010	 O			Identificação do Estabelecimento
C100	 OC			Documento - Nota Fiscal (código 01), Nota Fiscal Avulsa (código 1B), Nota Fiscal de Produtor (código 04), NF-e (código 55) e NFC-e (código 65).
C101	 OC			INFORMAÇÃO COMPLEMENTAR DOS DOCUMENTOS FISCAIS QUANDO DAS OPERAÇÕES INTERESTADUAIS DESTINADAS A CONSUMIDOR FINAL NÃO CONTRIBUINTE EC 87/15 (CÓDIGO 55)
C110	 OC			Complemento de Documento - Informação Complementar da Nota Fiscal (código 01, 1B, 55)
C113	 OC			Complemento de Documento - Documento Fiscal Referenciado
C120                COMPLEMENTO DE DOCUMENTO - OPERAÇÕES DE IMPORTAÇÃO (CÓDIGOS 01 e 55).
C170	 O(C100)	Complemento de Documento – Itens do Documento (códigos 01, 1B, 04 e 55)
C195	 OC			Complemento do Registro Analítico - Observações do Lançamento Fiscal
C197	 OC			Outras Obrigações Tributárias, Ajustes e Informações provenientes de Documento Fiscal
C400	 OC			Equipamento ECF (código 02 e 2D)  
C405	 OC			Redução Z (código 02 e 2D)  
C410	 OC			PIS e COFINS Totalizados no Dia (código 02 e 2D)  
C420	 OC			Registro dos Totalizadores Parciais da Redução Z (código 02 e 2D)  
C425	 OC			Resumo de itens do movimento diário (código 02 e 2D)  
C460	 OC			Documento Fisca l Emitido por ECF (código 02 e 2D)  
C470	 OC			Itens do Documento Fisca l Emitido por ECF (código 02 e 2D)  
C490	 OC			Registro Analítico do movimento diário (código 02 e 2D)  
C990	 O			Encerramento do Bloco C
*/
$contador_bloco_C = 1;

$sqlT007 = mysql_query("SELECT T007_Id
						FROM 
				  	  (SELECT T007_Id 
						 FROM T007 
						WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' 
					  	  AND T007_C004_Id = '{$g['empresaAtual']}' AND T007_Flag_Cancelada != 'S' 
						  AND T007_Numero_Protocolo_Nfe > 0
				   	 GROUP BY T007_Id
						UNION 
					   SELECT T013_Id 
						 FROM T013 
						WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
						  AND {$dataNFRecebida} <= '{$dataFinalHora}'
						  AND T013_C004_Id = '{$g['empresaAtual']}' AND T013_Flag_Cancelada != 'S' 
						  AND T013_Numero_Protocolo_Nfe > 0
						  AND T013_Flag_Estoque_Finalizado = 'S'
				  	 GROUP BY T013_Id) as notas");
					   					
$IND_MOV = (mysql_num_rows($sqlT007) > 0) ? '0' : '1';
# REGISTRO C001
$C001 = array(																			#TAM								
	$REG = 'C001',																		#004* 
	$IND_MOV																			#001*	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
); 
$C001 = implode("|", $C001);
$layout .= '|' . $C001 . '|' . _SALTO_LINHA;

if($IND_MOV == '0'){

// tradando a data para as notas de entrada	
// $dataInicialHora = $dataInicial.' 00:00:00';
// $dataFinalHora   = $dataFinal. ' 23:59:59';
//-----------------------------------------

//Buscando as NFs de entrada e saída do período
$sqlT007 = mysql_query("select T007_D024_Id,
                               T007_Id,
							   T013_Id,
                               D006_Codigo_CFOP, 
                               T007_Flag_Entrada_Saida,
                               D006_Flag_Entrada_Saida,
                               T007_Flag_ACP,
							   T013_Tipo,
                               T007_Serie,
                               T007_Numero_Nota_Fiscal,
                               T007_Chave_Acesso_Nfe,
                               T007_Data_Emissao,
                               T007_Data_Saida,
                               T007_Valor_Total,
                               T007_Valor_Desconto,
                               T007_Valor_Total_Produtos,
                               T007_Flag_Frete,
                               T007_Valor_Frete,
                               T007_Valor_Despesas_Acessorias,
                               T007_Valor_Total_Base_ICMS,
                               T007_Valor_Total_ICMS,
                               T007_Valor_Base_ICMS_Substituicao,
                               T007_Valor_Total_IPI,
                               T007_Valor_Total_PIS,
                               T007_Valor_Total_COFINS,
                               T007_Flag_Finalidade_NFe,
                               T007_Valor_ICMS_Substituicao_Retencao,
							   D006_Flag_Deb_Crd_ICMS,
							   D006_Flag_Credita_IPI,
							   D006_Flag_Deb_Crd_PIS,
							   D006_Flag_Deb_Crd_COFINS,
							   Emissao_Propria,
							   T007_Flag_Cancelada,
							   T007_Valor_Total_IPI_Devolucao,
							   T007_Valor_Total_FCP_Partilha,
							   T007_Valor_Total_Partilha_ICMS_Destino,
							   T013A_Codigo_Situacao_Documento,
							   T013A_Flag_Destaca_Frete_SPED
                          from (
                                    select T007_D024_Id,
                                           T007_Id,
										   0 as T013_Id,
                                           D006_Codigo_CFOP, 
                                           T007_Flag_Entrada_Saida,
                                           D006_Flag_Entrada_Saida,
                                           T007_Flag_ACP,
										   '' as T013_Tipo,
                                           T007_Serie,
                                           T007_Numero_Nota_Fiscal,
                                           T007_Chave_Acesso_Nfe,
                                           T007_Data_Emissao,
                                           T007_Data_Saida,
                                           T007_Valor_Total,
                                           T007_Valor_Desconto,
                                           T007_Valor_Total_Produtos,
                                           T007_Flag_Frete,
                                           T007_Valor_Frete,
                                           T007_Valor_Despesas_Acessorias,
                                           T007_Valor_Total_Base_ICMS_Impresso as T007_Valor_Total_Base_ICMS,
                                           T007_Valor_Total_ICMS,
                                           T007_Valor_Base_ICMS_Substituicao,
                                           T007_Valor_Total_IPI,
                                           T007_Valor_Total_PIS,
                                           T007_Valor_Total_COFINS,
                                           T007_Flag_Finalidade_NFe,
                                           T007_Valor_ICMS_Substituicao_Retencao,
										   D006_Flag_Deb_Crd_ICMS,
										   D006_Flag_Credita_IPI,
										   D006_Flag_Deb_Crd_PIS,
										   D006_Flag_Deb_Crd_COFINS,
										   '0' as Emissao_Propria,
										   T007_Flag_Cancelada,
										   T007_Valor_Total_IPI_Devolucao,
										   T007_Valor_Total_FCP_Partilha,
										   T007_Valor_Total_Partilha_ICMS_Destino,
										   '' as T013A_Codigo_Situacao_Documento,
										   '' as T013A_Flag_Destaca_Frete_SPED
                                      from T007 
                                 left join D006 on D006_Id = T007_D006_Id 
                                 left join T005 on T005_Id = T007_T005_Id
                                     where T007_Data_Emissao >= '{$dataInicial}' 
                                       and T007_Data_Emissao <= '{$dataFinal}' 
                                       and T007_C004_Id = '{$g['empresaAtual']}' 
                                       and T007_Numero_Protocolo_Nfe > 0                                    
									   and T007_Flag_ACP IN('6', '8') 
									   and not (T007_Flag_Cancelada = 'S' AND T007_Flag_ACP = '8')
                                  group by T007_Id
                                     union
                                     select T013_D024_Id as T007_D024_Id,
                                            0 as T007_Id,
											T013_Id,
                                            D006_Codigo_CFOP, 
                                            'E' as T007_Flag_Entrada_Saida,
                                            D006_Flag_Entrada_Saida,
											'' as T007_Flag_ACP,
											T013_Tipo,
                                            T013_Serie as T007_Serie,
                                            T013_Numero_Nota_Fiscal as T007_Numero_Nota_Fiscal,
                                            T013_Chave_Acesso_Nfe as T007_Chave_Acesso_Nfe,
                                            T013_Data_Emissao as T007_Data_Emissao,
                                            DATE({$dataNFRecebidaLancarSPED}) as T007_Data_Saida,
                                            T013_Valor_Total as T007_Valor_Total,
                                            T013_Valor_Desconto as T007_Valor_Desconto,
                                            T013_Valor_Total_Produtos as T007_Valor_Total_Produtos,
                                            T013_Flag_Frete as T007_Flag_Frete,
                                            T013_Valor_Frete as T007_Valor_Frete,
                                            T013_Valor_Despesas_Acessorias as T007_Valor_Despesas_Acessorias,
											(SELECT SUM(T014_Valor_Base_ICMS) FROM T014 LEFT JOIN D006 as D006_Item ON D006_Item.D006_Id = T014_D006_Id WHERE T014_T013_Id = T013_Id AND D006_Item.D006_Flag_Deb_Crd_ICMS = 'S') as T007_Valor_Total_Base_ICMS,						
											(SELECT SUM(T014_Valor_ICMS) FROM T014 LEFT JOIN D006 as D006_Item ON D006_Item.D006_Id = T014_D006_Id WHERE T014_T013_Id = T013_Id AND D006_Item.D006_Flag_Deb_Crd_ICMS = 'S') as T007_Valor_Total_ICMS,
											T013_Valor_Total_Base_ICMS_Substituicao as T007_Valor_Base_ICMS_Substituicao,
											(SELECT SUM(T014_Valor_IPI) FROM T014 LEFT JOIN D006 as D006_Item ON D006_Item.D006_Id = T014_D006_Id WHERE T014_T013_Id = T013_Id AND D006_Item.D006_Flag_Credita_IPI = 'S') as T007_Valor_Total_IPI,
										    (SELECT SUM(T014_Valor_PIS) FROM T014 LEFT JOIN D006 as D006_Item ON D006_Item.D006_Id = T014_D006_Id WHERE T014_T013_Id = T013_Id AND D006_Item.D006_Flag_Deb_Crd_PIS = 'S') as T007_Valor_Total_PIS,
											(SELECT SUM(T014_Valor_COFINS) FROM T014 LEFT JOIN D006 as D006_Item ON D006_Item.D006_Id = T014_D006_Id WHERE T014_T013_Id = T013_Id AND D006_Item.D006_Flag_Deb_Crd_COFINS = 'S') as T007_Valor_Total_COFINS,
                                            T013_Flag_Finalidade_NFe as T007_Flag_Finalidade_NFe,
                                            T013_Valor_ICMS_Substituicao as T007_Valor_ICMS_Substituicao_Retencao,
											D006_Flag_Deb_Crd_ICMS,
											D006_Flag_Credita_IPI,
											D006_Flag_Deb_Crd_PIS,
											D006_Flag_Deb_Crd_COFINS,
											'1' as Emissao_Propria,
											'' as T007_Flag_Cancelada,
											'0' as T007_Valor_Total_IPI_Devolucao,
											'0' as T007_Valor_Total_FCP_Partilha,
											'0'	as T007_Valor_Total_Partilha_ICMS_Destino,
											T013A_Codigo_Situacao_Documento,
											IFNULL(T013A_Flag_Destaca_Frete_SPED, 'S') as T013A_Flag_Destaca_Frete_SPED
                                       from T013 
                                  left join D006 on D006_Id = T013_D006_Id 
								  left join T013A on T013_Id = T013A_T013_Id
                                      where {$dataNFRecebida} >= '{$dataInicialHora}' 
                                        and {$dataNFRecebida} <= '{$dataFinalHora}' 
                                        and T013_C004_Id = '{$g['empresaAtual']}' 
                                        and T013_Flag_Cancelada != 'S'                                         
										and T013_Flag_Estoque_Finalizado = 'S'
                                        and ((T013_Tipo = '1' and T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo = '5')     
                                   group by T013_Id
                          ) as notaFiscal
                        order by T007_Data_Emissao");

	while($T007 = mysql_fetch_array($sqlT007)){
		$NUMERO_CFOP = removeChars($T007['D006_Codigo_CFOP']);
		$IND_OPER    = ($T007['T007_Flag_Entrada_Saida'] == 'S') ? '1' : '0';
		// $IND_EMIT    = ($T007['D006_Flag_Entrada_Saida'] == 'E') ? '1' : '0';
		$IND_EMIT = $T007['Emissao_Propria'];
		$COD_MOD = '';
		
		//verifica o código no sistema e escreve o modelo de acordo		
		if($T007['T013_Tipo'] == '5') {
			$COD_MOD = '01';
		} else if($T007['T007_Flag_ACP'] == '6' || $T007['T013_Tipo'] == '1')  {
			$COD_MOD = '55';
		} else if($T007['T007_Flag_ACP'] == '8') {
			$COD_MOD = '65';
		}		

		$VL_BC_ICMS = formataDecimal($T007['T007_Valor_Total_Base_ICMS']);
		$VL_ICMS    = formataDecimal($T007['T007_Valor_Total_ICMS']);
		$VL_IPI     = formataDecimal($T007['T007_Valor_Total_IPI'] + $T007['T007_Valor_Total_IPI_Devolucao']);
		$VL_PIS     = formataDecimal($T007['T007_Valor_Total_PIS']);
		$VL_COFINS  = formataDecimal($T007['T007_Valor_Total_COFINS']);

		//Para NFs de entrada os campos de valor de imposto, base de cálculo e alíquota só devem ser informados se tiver direito à crédito
		// Está validando direto na query por produto se credita ou não, desta forma estava errado, pois se tivesse 2 itens na NF, um que credita e ou que não
		// E a CFOP do cabeçalho estivesse para não creditar, não enviada nenhum valor
		/*if($IND_OPER == 0 && $T007['D006_Flag_Deb_Crd_ICMS'] != 'S'){
			$VL_BC_ICMS = '0,00';
			$VL_ICMS    = '0,00';
			$T007['T007_Valor_Total_Produtos'] += ($T007['T007_Valor_ICMS_Substituicao_Retencao'] != 0 ? $T007['T007_Valor_ICMS_Substituicao_Retencao'] : 0);
		}

		if($IND_OPER == 0 && $T007['D006_Flag_Credita_IPI'] != 'S'){
			$VL_IPI = '0,00';
			$T007['T007_Valor_Total_Produtos'] += $T007['T007_Valor_Total_IPI'];
		}

		if($IND_OPER == 0 && $T007['D006_Flag_Deb_Crd_PIS'] != 'S'){
			$VL_PIS = '0,00';
		}

		if($IND_OPER == 0 && $T007['D006_Flag_Deb_Crd_COFINS'] != 'S'){
			$VL_COFINS = '0,00';
		}*/
		//-------------------------------------------

		//$T006 = mysql_query("SELECT SUM(T006_Valor_Desconto_Unitario_Bruto) as Desconto FROM T006 WHERE T006_T005_Id = '{$T007['T007_T005_Id']}'");
		//$T006 = mysql_fetch_array($T006);

		//não devem ser informados os campos COD_PART, VL_BC_ICMS_ST, VL_ICMS_ST, VL_IPI, VL_PIS, VL_COFINS, VL_PIS_ST e VL_COFINS_ST para modelo (65)
		$COD_PART = $T007['T007_D024_Id'];
		$VL_BC_ICMS_ST = formataDecimal($T007['T007_Valor_Base_ICMS_Substituicao']);
		$VL_ICMS_ST = formataDecimal($T007['T007_Valor_ICMS_Substituicao_Retencao']);								

		if($COD_MOD == '65') {
			$COD_PART = '';
			$VL_BC_ICMS_ST  = '';
			$VL_ICMS_ST = '';
			$VL_IPI = '';
			$VL_PIS = '';
			$VL_COFINS = '';
			$VL_PIS_ST  = '';
			$VL_COFINS_ST  = '';
		}

		$DT_DOC = formataData(substr($T007['T007_Data_Emissao'],0,10));
		$DT_E_S = formataData($T007['T007_Data_Saida']);
		$VL_DOC = formataDecimal($T007['T007_Valor_Total']);
		$IND_PGTO = '2';
		$VL_DESC = formataDecimal($T007['T007_Valor_Desconto']);
		$VL_MERC = formataDecimal($T007['T007_Valor_Total_Produtos']);
		$VL_FRT = formataDecimal($T007['T007_Valor_Frete']);
		$IND_FRT= $T007['T007_Flag_Frete'];
		if($T007['T013_Id'] > 0 && $T007['T013A_Flag_Destaca_Frete_SPED'] != 'S'){
			$VL_FRT = '0,00';
			$IND_FRT = '9';
		}
		$VL_OUT_DA = formataDecimal($T007['T007_Valor_Despesas_Acessorias']);
		$COD_SIT = '00';
		// IND_OPER,IND_EMIT, COD_MOD, COD_SIT, SER, NUM_DOC e CHV_NF-e

		if($T007['T007_Flag_Cancelada'] == 'S' && $COD_MOD == '55') {					
			$COD_SIT = '02';
			$COD_PART = '';
			$DT_DOC = '';
			$DT_E_S = '';
			$VL_DOC = '';
			$IND_PGTO = '';
			$VL_DESC = '';
			$VL_MERC = '';
			$VL_FRT = '';
			$IND_FRT = '';
			$VL_OUT_DA = '';
			$VL_BC_ICMS = '';
			$VL_ICMS = '';
			$VL_BC_ICMS_ST = '';
			$VL_ICMS_ST = '';
			$VL_IPI = '';
			$VL_PIS = '';
			$VL_COFINS = '';
		}

		if(!empty($T007['T013A_Codigo_Situacao_Documento'])){
			$COD_SIT = $T007['T013A_Codigo_Situacao_Documento'];
		}

		# REGISTRO C100
		$C100 = array(				   													#TAM
			$REG = 'C100',			   													#004						
			$IND_OPER, 				   													#001*	Indicador do tipo de operação(0 Entrada/1 Saída)				
			$IND_EMIT,				   													#001*	Indicador do emitente do documento fiscal (0 Emissão própria/1 Terceiros)				
			$COD_PART,																	#060	Código do participante (campo 02 do Registro 0150)				
			$COD_MOD,				   													#002*	Código do modelo do documento fiscal, conforme a Tabela 4.1.1 				
			$COD_SIT,		   													#002*	Código da situação do documento fiscal, conforme a Tabela 4.1.2				
			$SER = $T007['T007_Serie'],	   												#003	Série do documento fiscal			
			$NUM_DOC = $T007['T007_Numero_Nota_Fiscal'],	   							#009	Número do documento fiscal				
			$CHV_NFE = $T007['T007_Chave_Acesso_Nfe'],		   							#044*	Chave da Nota Fiscal Eletrônica				
			$DT_DOC,																	#008*	Data da emissão do documento fiscal 				
			$DT_E_S,																	#008*	Data da entrada ou da saída				
			$VL_DOC,																	#		Valor total do documento fiscal			
			$IND_PGTO,				   													#001*	Indicador do tipo de pagamento (0 À vista/1 A prazo/2 Outros)			
			$VL_DESC,				    												#		Valor total do desconto			
			$VL_ABAT_NT = '',		   													#		Abatimento não tributado e não comercial				
			$VL_MERC,																	#		Valor total das mercadorias e serviços			
			$IND_FRT,																	#		Indicador do tipo do frete (0 Por conta do emitente/1 Por conta do destinatário/remetente/2 Por conta de terceiros/9 Sem cobrança de frete)
			$VL_FRT,																	#001*	Valor do frete indicado no documento fiscal				
			$VL_SEG = '',				   												#		Valor do seguro indicado no documento fiscal			
			$VL_OUT_DA,																	#		Valor de outras despesas acessórias
			$VL_BC_ICMS,																#		Valor da base de cálculo do ICMS				
			$VL_ICMS,																	#		Valor do ICMS		
			$VL_BC_ICMS_ST,																#		Valor da base de cálculo do ICMS substituição tributária				
			$VL_ICMS_ST,																#		Valor do ICMS retido por substituição tributária	
			$VL_IPI,																	#		Valor total do IPI 	
			$VL_PIS,																	#		Valor total do PIS			
			$VL_COFINS,																	#		Valor total da COFINS			
			$VL_PIS_ST = '',															#		Valor total do PIS retido por substituição tributária			
			$VL_COFINS_ST = ''															#		Valor total da COFINS retido por substituição tributária				
		);
		$C100 = implode("|", $C100);
		$layout .= '|' . $C100 . '|' . _SALTO_LINHA;
		$contador_bloco_C++;
		//--------------------------------------------------------
		if($T007['T007_Flag_Cancelada'] == 'S' && $COD_MOD == '55') {			
			continue;
		}

		if(($T007['T007_Valor_Total_FCP_Partilha'] > 0 || $T007['T007_Valor_Total_Partilha_ICMS_Destino'] > 0) && $COD_MOD == '55'){

			if($IND_OPER == '1'){
				// Se for saida, é para o destino
				$VL_ICMS_UF_DEST = $T007['T007_Valor_Total_Partilha_ICMS_Destino']; 
				$VL_ICMS_UF_REM = 0;
			} else {
				// Se for entrada, é para a origem
				$VL_ICMS_UF_DEST = 0;
				$VL_ICMS_UF_REM = $T007['T007_Valor_Total_Partilha_ICMS_Destino']; 
			}

			# REGISTRO C101
			$C101 = array(																 	  	    #TAM														
				$REG = 'C101',															 		    #004	
				$VL_FCP_UF_DEST = formataDecimal($T007['T007_Valor_Total_FCP_Partilha']),		    # Valor total relativo ao Fundo de Combate à pobreza (FCP) da UF de destino
				formataDecimal($VL_ICMS_UF_DEST), 													# Valor total do ICMS Interestadual para a UF de origem ;
				formataDecimal($VL_ICMS_UF_REM)											            # Valor total do ICMS Interestadual para a UF do remetente
			);		

			$C101 = implode("|", $C101);
			$layout .= '|' . $C101 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
			//----------------------------------------------------
		}

		if($COD_MOD == '55' && $T007['T007_Id'] > 0) {	
			//So lança se for NFe e T007
			$referenciadas = mysql_query("SELECT COUNT(*) as notasReferenciadas
											FROM T132 
										   WHERE T132_T007_Id = '{$T007['T007_Id']}'
										     AND T132_Tipo = 'NFe'");

			$mReferenciadas = mysql_fetch_array($referenciadas);
		
			if ($mReferenciadas['notasReferenciadas'] > 0) {

				$C110 = array(
					$REG = 'C110',											#Texto fixo contendo "C110"
					$COD_INF = '1.1',										#Código da informação complementar do documento fiscal (campo 02 do Registro 0450)
					$TXT_COMPL = ''											#Descrição complementar do código de referência
				);
	
				$C110 = implode("|", $C110);
				$layout .= '|' . $C110 . '|' . _SALTO_LINHA;
				$contador_bloco_C++;

				//popula registro 0450
				insertRegistro0450('1.1', 'Documento(s) referenciado(s):');

				$T132 = mysql_query("SELECT
										T007_D024_Id,
										T007_Id,
										T013_Id, 
										T007_Numero_Nota_Fiscal,
										T007_Serie,
										T007_Data_Emissao,
										T007_Chave_Acesso_Nfe,
										T007_Flag_Entrada_Saida,
										T013_Tipo,
										T007_Flag_ACP
									FROM (
										SELECT T007_D024_Id,
  											   T007_Id,
  											   0 as T013_Id, 
  											   T007_Numero_Nota_Fiscal,
  											   T007_Serie,
  											   T007_Data_Emissao,
  											   T007_Chave_Acesso_Nfe,
  											   T007_Flag_Entrada_Saida,
  											   '' as T013_Tipo,
  											   T007_Flag_ACP
										  FROM T007
									 LEFT JOIN T132 ON T132_Chave_Acesso = T007_Chave_Acesso_Nfe
									     WHERE T132_T007_Id = '{$T007['T007_Id']}'
										   AND T132_Tipo = 'NFe'
										UNION
										SELECT T013_D024_Id as T007_D024_Id,
   											   0 as T007_Id,
   											   T013_Id,
   											   T013_Numero_Nota_Fiscal as T007_Numero_Nota_Fiscal,
   											   T013_Serie as T007_Serie,
   											   T013_Data_Emissao as T007_Data_Emissao,
   											   T013_Chave_Acesso_Nfe as T007_Chave_Acesso_Nfe,
   											   'E' as T007_Flag_Entrada_Saida,
   											   T013_Tipo,
											   '' as T007_Flag_ACP
										  FROM T013
									 LEFT JOIN T132 ON T132_Chave_Acesso = T013_Chave_Acesso_Nfe
									  	 WHERE T132_T007_Id = '{$T007['T007_Id']}'
										   AND T132_Tipo = 'NFe'
									) as Referenciadas");

				while($mT132 = mysql_fetch_array($T132)) {

					$IND_OPER = ($mT132['T007_Flag_Entrada_Saida'] == 'S') ? '1' : '0';
					$IND_EMIT = ($mT132['T007_Id'] == 0 ? '1' : '0');
					$COD_PART = $mT132['T007_D024_Id'];

					if($mT132['T013_Tipo'] == '5') {
						$COD_MOD_DEV = '01';
					} else if($mT132['T007_Flag_ACP'] == '6' || $mT132['T013_Tipo'] == '1') {
						$COD_MOD_DEV = '55';
					} elseif($mT132['T007_Flag_ACP'] == '8'){
						$COD_MOD_DEV = '65';
					}

					$SER = $mT132['T007_Serie'];
					$SUB = '';
					$DT_DOC = formataData(substr($mT132['T007_Data_Emissao'],0,10));
					$NUM_DOC = $mT132['T007_Numero_Nota_Fiscal'];
					$CHV_DOCe = $mT132['T007_Chave_Acesso_Nfe'];

					$C113 = array(
						$REG = 'C113',						#Texto fixo contendo "C113"
						$IND_OPER,							#Indicador do tipo de operação: 0- Entrada/aquisição;1- Saída/prestação
						$IND_EMIT,							#Indicador do emitente do título:0- Emissão própria;1- Terceiros
						$COD_PART,							#Código do participante emitente (campo 02 do Registro 0150) do documento referenciado. 
						$COD_MOD_DEV,					    #Código do documento fiscal, conforme a Tabela 4.1.1
						$SER,								#Série do documento fiscal
						$SUB,								#Subsérie do documento fiscal
						$NUM_DOC,							#Número do documento fiscal 
						$DT_DOC,							#Data da emissão do documento fiscal.
						$CHV_DOCe							#Chave do Documento Eletrônico
					);
		
					$C113 = implode("|", $C113);
					$layout .= '|' . $C113 . '|' . _SALTO_LINHA;
					$contador_bloco_C++;		
				}
			}
		}

		//Verifica se é nota fiscal de Importação
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
			$C120 = array(																#TAM														
				$REG = 'C120',															#004	
				$COD_DOC_IMP = '0',														#001*	Documento de importação (0 Declaração de Importação/1 Declaração Simplificada de Importação)
				$NUM_DOC_IMP = removeChars($IMP['T075_Numero_DI']),						#010	Número do documento de Importação.
				$VL_PIS_IMP = formataDecimal($IMP['PIS']),								#-		Valor pago de PIS na importação
				$VL_COFINS_IMP = formataDecimal($IMP['COFINS']),						#-		Valor pago de COFINS na importação
				$NUM_ACDRAW	= ''														#020	Número do Ato Concessório do regime Drawback
			);		
			$C120 = implode("|", $C120);
			$layout .= '|' . $C120 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
			//----------------------------------------------------
		}

		// Busca os produtos da nota para montar o registro C170
		$sqlT008 = mysql_query("select T008_Id,
									   T008_Situacao_Tributaria_PIS,
									   T008_Situacao_Tributaria_COFINS,
									   T006_Valor_Desconto_Unitario_Bruto,
									   T008_Valor_Total_Desconto,
									   T008_Flag_Situacao_Tributaria,
									   T008_Situacao_Tributaria_IPI,
									   T008_Quantidade,
									   T008_Valor_Base_PIS,
									   T008_Aliquota_PIS,
									   T008_Valor_Base_COFINS,
									   T008_Aliquota_COFINS,
									   D001_Codigo_Produto,
									   D001_Descricao_Produto,
									   D001_Id,
									   D037_Unidade,
									   T008_Valor_Total_Preco_Sem_Desconto,
									   T008_Valor_Base_ICMS,
									   T008_Aliquota_ICMS,
									   T008_Valor_ICMS,
									   T008_Valor_Base_ICMS_Substituicao,
									   T008_Aliquota_ICMS_ST,
									   T008_Valor_ICMS_Substituicao_Retencao,
									   T008_Valor_Base_IPI,
									   T008_Aliquota_IPI,
									   T008_Valor_IPI,
									   D037_Id,
									   T008_Valor_PIS,
									   T008_Valor_COFINS, 
									   T008_Flag_ST,
									   D006_Codigo_CFOP,
									   T014_Flag_Origem_Produto,
									   D088_Codigo_ST,
									   T014_T013_Id,
									   T008_T007_Id,
									   D006_Flag_Deb_Crd_ICMS,
									   D006_Flag_Credita_IPI,
									   D006_Flag_Deb_Crd_PIS,
									   D006_Flag_Deb_Crd_COFINS
								 from(
										    select T008_Id,
												   T008_Situacao_Tributaria_PIS,
												   T008_Situacao_Tributaria_COFINS,
												   T006_Valor_Desconto_Unitario_Bruto,
												   T008_Valor_Total_Desconto,
												   T008_Flag_Situacao_Tributaria,
												   T008_Situacao_Tributaria_IPI,
												   T008_Quantidade,
												   T008_Valor_Base_PIS,
												   T008_Aliquota_PIS,
												   T008_Valor_Base_COFINS,
												   T008_Aliquota_COFINS,
												   D001_Codigo_Produto,
												   D001_Descricao_Produto,
												   D001_Id,
												   D037_Unidade,
												   T008_Valor_Total_Preco_Sem_Desconto,
												   T008_Valor_Base_ICMS_Impresso as T008_Valor_Base_ICMS,
												   T008_Aliquota_ICMS,
												   T008_Valor_ICMS,
												   T008_Valor_Base_ICMS_Substituicao,
												   T008_Aliquota_ICMS_ST,
												   T008_Valor_ICMS_Substituicao_Retencao,
												   T008_Valor_Base_IPI,
												   T008_Aliquota_IPI,
												   T008_Valor_IPI,
												   D037_Id,
												   T008_Valor_PIS,
												   T008_Valor_COFINS, 
												   T008_Flag_ST,
												   D006_Codigo_CFOP,
												   '' as T014_Flag_Origem_Produto,
												   '' as D088_Codigo_ST,
												   0 as T014_T013_Id,
												   T008_T007_Id,
												   D006_Flag_Deb_Crd_ICMS,
												   D006_Flag_Credita_IPI,
												   D006_Flag_Deb_Crd_PIS,
												   D006_Flag_Deb_Crd_COFINS
											  from T008 
										 left join D009 on D009_Id = T008_D009_Id
										 left join D049 on D049_Id = D009_D049_Id
										 left join D001 on D001_Id = D049_D001_Id
										 left join D037 on D037_Id = D001_D037_Id
										 left join T007 on T007_Id = T008_T007_Id
										 left join T005 on T005_Id = T007_T005_Id
										 left join T006 on T005_Id = T006_T005_Id
										 left join D059 on D059_Id = T008_D059_Id
										 left join D006 on D006_Id = T008_D006_Id
											 where T008_T007_Id = '{$T007['T007_Id']}'
										  group by T008_Id
											 union
											select T014_Id as T008_Id,
												   T014_Situacao_Tributaria_PIS as T008_Situacao_Tributaria_PIS,
												   T014_Situacao_Tributaria_COFINS as T008_Situacao_Tributaria_COFINS,
												   0 as T006_Valor_Desconto_Unitario_Bruto,
												   T014_Valor_Total_Desconto as T008_Valor_Total_Desconto,
												   T014_Flag_Situacao_Tributaria as T008_Flag_Situacao_Tributaria,
												   T014_Situacao_Tributaria_IPI as T008_Situacao_Tributaria_IPI,
												   T014_Quantidade as T008_Quantidade,
												   T014_Valor_Base_PIS as T008_Valor_Base_PIS,
												   T014_Aliquota_PIS as T008_Aliquota_PIS,
												   T014_Valor_Base_COFINS as T008_Valor_Base_COFINS,
												   T014_Aliquota_COFINS as T008_Aliquota_COFINS,
												   D001_Codigo_Produto,
												   D001_Descricao_Produto,
												   D001_Id,
												   D037_Unidade,
												   T014_Valor_Total_Custo as T008_Valor_Total_Preco_Sem_Desconto,
												   T014_Valor_Base_ICMS as T008_Valor_Base_ICMS,
												   T014_Aliquota_ICMS as T008_Aliquota_ICMS,
												   T014_Valor_ICMS as T008_Valor_ICMS,
												   T014_Valor_Base_ICMS_Substituicao as T008_Valor_Base_ICMS_Substituicao,
												   0 as T008_Aliquota_ICMS_ST,
												   T014_Valor_ICMS_Substituicao as T008_Valor_ICMS_Substituicao_Retencao,
												   T014_Valor_Base_IPI as T008_Valor_Base_IPI,
												   T014_Aliquota_IPI as T008_Aliquota_IPI,
												   T014_Valor_IPI as T008_Valor_IPI,
												   D037_Id,
												   T014_Valor_PIS as T008_Valor_PIS,
												   T014_Valor_COFINS as T008_Valor_COFINS, 
												   'N' as T008_Flag_ST,
												   D006_Codigo_CFOP,
												   T014_Flag_Origem_Produto,
												   D088_Codigo_ST,
												   T014_T013_Id,
												   '0' as T008_T007_Id,
												   D006_Flag_Deb_Crd_ICMS,
												   D006_Flag_Credita_IPI,
												   D006_Flag_Deb_Crd_PIS,
												   D006_Flag_Deb_Crd_COFINS
											  from T014 
										 left join D009 on D009_Id = T014_D009_Id
										 left join D049 on D049_Id = D009_D049_Id
										 left join D001 on D001_Id = D049_D001_Id
										 left join D037 on D037_Id = D001_D037_Id
										 left join T013 on T013_Id = T014_T013_Id
										 left join D006 on D006_Id = T014_D006_Id
										 left join D088 on D088_Id = D006_D088_Id
											 where T014_T013_Id = '{$T007['T013_Id']}'
										  group by T014_Id
								 ) as itemNota");             
								
	
		$nSequencial=1;
		while($T008 = mysql_fetch_array($sqlT008)){
			$cont++;
			//ITENS DO DOCUMENTO (CÓDIGO 01, 1B, 04 e 55)
			if($COD_MOD == '65') 
				break;

			$CST_PIS           = $T008['T008_Situacao_Tributaria_PIS'];
			$CST_COFINS        = $T008['T008_Situacao_Tributaria_COFINS'];

			if ($T008['T008_T007_Id'] <= 0 && $IND_EMIT == '1') {
				// Se for nota de entrada emitida por terceiros, precisa ajustar a situação tributaria do IPI
				switch ($T008['T008_Situacao_Tributaria_IPI']) {
					case 50:
						$T008['T008_Situacao_Tributaria_IPI'] = '00';
						break;
					case 51:
						$T008['T008_Situacao_Tributaria_IPI'] = '01';
						break;
					case 52:
						$T008['T008_Situacao_Tributaria_IPI'] = '02';
						break;
					case 53:
						$T008['T008_Situacao_Tributaria_IPI'] = '03';
						break;
					case 54:
						$T008['T008_Situacao_Tributaria_IPI'] = '04';
						break;
					case 55:
						$T008['T008_Situacao_Tributaria_IPI'] = '05';
						break;
					case 99:
						$T008['T008_Situacao_Tributaria_IPI'] = '49';
						break;
					// Não é necessário um bloco "default" para manter o valor original
				}
			}
			
			$CST_IPI 		   = $T008['T008_Situacao_Tributaria_IPI'];
			$IND_MOV           = ($T007['T007_Flag_Finalidade_NFe'] == '1') ? '0' : '1';
			//$QTD             = formataDecimal($T008['T008_Quantidade']);
			//$VL_DESC         = (formataDecimal($T008['T006_Valor_Desconto_Unitario_Bruto']) > 0) ? formataDecimal($T008['T006_Valor_Desconto_Unitario_Bruto']) : '';
			$VL_DESC           = $T008['T008_Valor_Total_Desconto'] > 0 ? formataDecimal($T008['T008_Valor_Total_Desconto']) : '';			
			//$CST_ICMS        = ($T007['D006_Flag_Entrada_Saida'] == 'S') ? $T008['T008_Flag_Situacao_Tributaria'] : '';
			
			// Concatenando a origem e CST das notas de entrada, verifica se é 2 porque as notas de entrada pode vir CST (regime normal) com 2 caracteres e CSOSN (simples) com 3 caracteres			
			$CST_ICMS          = substr($T008['T008_Flag_Situacao_Tributaria'],-3);
			$CST_ICMS          = (strlen($CST_ICMS) == 2 ? $T008['T014_Flag_Origem_Produto'].$CST_ICMS : $CST_ICMS);			
			//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

			$VL_BC_ICMS        = formataDecimal($T008['T008_Valor_Base_ICMS']);		
			$ALIQ_ICMS         = formataDecimal($T008['T008_Aliquota_ICMS']);
			$VL_ICMS           = formataDecimal($T008['T008_Valor_ICMS']);
			$VL_BC_IPI         = formataDecimal($T008['T008_Valor_Base_IPI']);			
			$ALIQ_IPI          = formataDecimal($T008['T008_Aliquota_IPI']);
			$VL_IPI            = formataDecimal($T008['T008_Valor_IPI']);
			$VL_BC_PIS         = formataDecimal($T008['T008_Valor_Base_PIS']);
			$ALIQ_PIS          = formataDecimal($T008['T008_Aliquota_PIS']);
			$VL_PIS            = formataDecimal($T008['T008_Valor_PIS']);
			$VL_BC_COFINS      = formataDecimal($T008['T008_Valor_Base_COFINS']);
			$ALIQ_COFINS       = formataDecimal($T008['T008_Aliquota_COFINS']);
			$VL_COFINS         = formataDecimal($T008['T008_Valor_COFINS']);
			$QUANT_BC_PIS      = '';
			$ALIQ_PIS_QUANT    = '';
			$QUANT_BC_COFINS   = '';	
			$ALIQ_COFINS_QUANT = '';

			if($T008['T008_T007_Id'] > 0){
            	$CST_IPI = (strlen($T008['T008_Situacao_Tributaria_IPI']) == 1 ? '0'.$T008['T008_Situacao_Tributaria_IPI'] : $T008['T008_Situacao_Tributaria_IPI']);				
			}

			// Verifica a situação tributária PIS E COFINS - Operação Tributável com Alíquota por Unidade de Medida de Produto
			if($T008['T008_Situacao_Tributaria_PIS'] == '03' && $T008['T008_Situacao_Tributaria_COFINS'] == '03'){
				$VL_BC_PIS = '';
				$ALIQ_PIS = '';
				$QUANT_BC_PIS = '0';														
				$ALIQ_PIS_QUANT = $T008['T008_Quantidade'];

				$VL_BC_COFINS = '';
				$ALIQ_COFINS = '';
				$QUANT_BC_COFINS = '0';
				$ALIQ_COFINS_QUANT = $T008['T008_Quantidade'];
			} 

			//Para NFs de entrada os campos de valor de imposto, base de cálculo e alíquota só devem ser informados se tiver direito à crédito
			if($IND_OPER == 0 && $T008['D006_Flag_Deb_Crd_ICMS'] != 'S'){
				$VL_BC_ICMS = '0,00';
				$ALIQ_ICMS  = '0,00';
				$VL_ICMS    = '0,00';

				/*$T008['T008_Valor_Total_Preco_Sem_Desconto'] += ($T008['T008_Valor_ICMS_Substituicao_Retencao'] > 0 
																				? $T008['T008_Valor_ICMS_Substituicao_Retencao']
																				: 0); */
			}

			if($IND_OPER == 0 && $T008['D006_Flag_Credita_IPI'] != 'S'){
				$VL_BC_IPI = '0,00';
				$ALIQ_IPI  = '0,00';
				$VL_IPI    = '0,00';

				//$T008['T008_Valor_Total_Preco_Sem_Desconto'] += $T008['T008_Valor_IPI'];
			}

			if($IND_OPER == 0 && $T008['D006_Flag_Deb_Crd_PIS'] != 'S'){
				$VL_BC_PIS = '0,00';
				$ALIQ_PIS  = '0,00';
				$VL_PIS    = '0,00';
			}

			if($IND_OPER == 0 && $T008['D006_Flag_Deb_Crd_COFINS'] != 'S'){
				$VL_BC_COFINS = '0,00';
				$ALIQ_COFINS  = '0,00'; 
				$VL_COFINS    = '0,00';
			}
			//----------------------------------------------

			# REGISTRO C170
			$C170 = array(																	#TAM													
				$REG = 'C170',																#004	
				$NUM_ITEM = $nSequencial,													#003	Número seqüencial do item no documento fiscal				
				$COD_ITEM = $T008['D001_Codigo_Produto'],									#060	Código do item (campo 02 do Registro 0200)				
				$DESCR_COMPL = adicionarEspape($T008['D001_Descricao_Produto']),			#-		Descrição complementar do item como adotado no documento fiscal				
				$QTD = formataDecimal($T008['T008_Quantidade']),							#-		Quantidade do item		
				$UNID = adicionarEspape($T008['D037_Unidade']),								#006	Unidade do item (Campo 02 do registro 0190)			
				$VL_ITEM = formataDecimal($T008['T008_Valor_Total_Preco_Sem_Desconto']),	#-		Valor total do item (mercadorias ou serviços)			
				$VL_DESC,																	#-		Valor do desconto comercial			
				$IND_MOV,																	#001	Movimentação física do ITEM/PRODUTO (0 SIM/1 NÃO)				
				
				$CST_ICMS,																	#003*	Código da Situação Tributária referente ao ICMS, conforme a Tabela indicada no item 4.3.1				
				$CFOP = removeChars($T008['D006_Codigo_CFOP']),								#004*	Código Fiscal de Operação e Prestação			
				$COD_NAT = '',																#010	Código da natureza da operação (campo 02 do Registro 0400)				
				$VL_BC_ICMS,																#-		Valor da base de cálculo do ICMS				
				$ALIQ_ICMS,																	#006	Alíquota do ICMS				
				$VL_ICMS,																	#-		Valor do ICMS creditado/debitado			
				$VL_BC_ICMS_ST = formataDecimal($T008['T008_Valor_Base_ICMS_Substituicao']),#-		Valor da base de cálculo referente à substituição tributária				
				$ALIQ_ST = formataDecimal($T008['T008_Aliquota_ICMS_ST']),					#006	Alíquota do ICMS da substituição tributária na unidade da federação de destino				
				$VL_ICMS_ST = formataDecimal($T008['T008_Valor_ICMS_Substituicao_Retencao']),		#-		Valor do ICMS referente à substituição tributária				
				
				$IND_APUR = '0',															#001*	Indicador de período de apuração do IPI:(0 Mensal/1 Decendial)				
				$CST_IPI,                                        							#002*	Código da Situação Tributária referente ao IPI				
				$COD_ENQ = '',																#003*	Código de enquadramento legal do IPI				
				$VL_BC_IPI,																	#-		Valor da base de cálculo do IPI			
				$ALIQ_IPI,																	#006	Alíquota do IPI				
				$VL_IPI,																	#-		Valor do IPI creditado/debitado			
				
				$CST_PIS,																	#002*	Código da Situação Tributária referente ao PIS.				
				$VL_BC_PIS,																	#-		Valor da base de cálculo do PIS			
				$ALIQ_PIS,																	#008	Alíquota do PIS (em percentual)				
				$QUANT_BC_PIS,																#-		Quantidade – Base de cálculo PIS/PASEP				
				$ALIQ_PIS_QUANT,															#-		Alíquota do PIS (em reais)					
				$VL_PIS,																	#-		Valor do PIS			
				
				$CST_COFINS,																#002*	Código da Situação Tributária referente ao COFINS.					
				$VL_BC_COFINS,																#-		Valor da base de cálculo da COFINS				
				$ALIQ_COFINS,																#008	Alíquota do COFINS (em percentual)					
				$QUANT_BC_COFINS,															#-		Quantidade – Base de cálculo COFINS					
				$ALIQ_COFINS_QUANT,															#-		Alíquota da COFINS (em reais)					
				$VL_COFINS,																	#-		Valor da COFINS			
				
				$COD_CTA = '',																#060	Código da conta analítica contábil debitada/creditada	
				$VL_ABAT_NT = ''														  //#-   Valor do abatimento não tributado e não comercial
			);

			// Só deve ser preenchido o registro C170 caso a emissão não seja própria. 
			$C170 = implode("|", $C170);
			//if($T007['D006_Flag_Entrada_Saida'] == 'E' && $IND_EMIT == '1') {
			// Se for uma NF de entrada emitida por terceiros, precisa lançar o bloco C170
			if($T008['T014_T013_Id'] > 0 && $IND_EMIT == '1') {
				$layout .= '|' . $C170 . '|' . _SALTO_LINHA;
				$contador_bloco_C++;
				verificaRegistro0200($T008['D001_Id']);
				verificaRegistro0190($T008['D037_Id']);
			}
			//-----------------------------------------------------------

			if($T008['T008_Flag_ST'] == 'S'){
				$C176 = array(																	#TAM													
					$REG = 'C176',																
					$COD_MOD_ULT_E = '',
					$NUM_DOC_ULT_E = '',
					$SER_ULT_E = '',
					$DT_ULT_E = '',
					$COD_PART_ULT_E = '',
					$QUANT_ULT_E = '',
					$VL_UNIT_ULT_E = '',
					$VL_UNIT_BC_ST = ''
				);

				$C176 = implode("|", $C176);
				//$layout .= '|' . $C176 . '|' . _SALTO_LINHA;
				// $contador_bloco_C++;
			}
			$nSequencial++;
		}

			/* 		$sqlT008 = mysql_query("select  sum(t008_valor_desconto) as T008_Valor_Desconto,
										T008_Flag_Situacao_Tributaria,
										D006_Codigo_CFOP,
										T008_Aliquota_ICMS,									 
										sum(T008_Valor_Total_Preco) as T008_Valor_Total_Preco, 
										sum(T008_Valor_Base_ICMS) as T008_Valor_Base_ICMS, 
										sum(T008_Valor_ICMS) as T008_Valor_ICMS,
										sum(T008_Valor_Base_ICMS_Substituicao) as T008_Valor_Base_ICMS_Substituicao, 
										sum(T008_Valor_ICMS_Substituicao_Retencao) as T008_Valor_ICMS_Substituicao_Retencao, 
										sum(T008_Valor_ICMS_Reducao) as T008_Valor_ICMS_Reducao,
										sum(T008_Valor_Base_ICMS_Sem_Reducao) as T008_Valor_Base_ICMS_Sem_Reducao,
										sum(T008_Valor_IPI) as T008_Valor_IPI, 
										sum(T008_Valor_Total_Frete) as T008_Valor_Total_Frete,
										sum(VL_OPR) as VL_OPR,
										T014_Flag_Origem_Produto,
										sum(T008_Valor_IPI_Devolucao) as T008_Valor_IPI_Devolucao,
										D006_Flag_Deb_Crd_ICMS,
										D006_Flag_Credita_IPI,
										D006_Flag_Deb_Crd_PIS,
										D006_Flag_Deb_Crd_COFINS
                                 from(
                                            select sum(T008_Valor_Total_Desconto) as T008_Valor_Desconto,
                                                   T008_Flag_Situacao_Tributaria,
                                                   D006_Codigo_CFOP,
                                                   T008_Aliquota_ICMS,                                                       
                                                   sum(T008_Valor_Total_Preco) as T008_Valor_Total_Preco, 
                                                   sum(T008_Valor_Base_ICMS_Impresso) as T008_Valor_Base_ICMS, 
												   sum(T008_Valor_Base_ICMS_Impresso*((T008_Percentual_Reducao_ICMS)/100)) as T008_Valor_ICMS_Reducao, 
                                                   sum(T008_Valor_ICMS) as T008_Valor_ICMS,
                                                   sum(T008_Valor_Base_ICMS_Substituicao) as T008_Valor_Base_ICMS_Substituicao, 
                                                   sum(T008_Valor_ICMS_Substituicao_Retencao) as T008_Valor_ICMS_Substituicao_Retencao, 
                                                   sum(T008_Valor_Base_ICMS_Sem_Reducao) as T008_Valor_Base_ICMS_Sem_Reducao,
                                                   coalesce(sum(T008_Valor_IPI),0) as T008_Valor_IPI, 												   
                                                   sum(T008_Valor_Total_Frete) as T008_Valor_Total_Frete, 
                                                   (sum(T008_Valor_Total_Preco) + 
                                                    coalesce(sum(T008_Valor_IPI),0) + 
                                                    sum(T008_Valor_ICMS_Substituicao_Retencao) + 
                                                    sum(T008_Valor_Total_Frete) + 
                                                    sum(T008_Valor_Total_Despesas_Acessorias) + 
												    sum(T008_Valor_IPI_Devolucao) + 
												    sum(T008_Valor_FCP_ST)) as VL_OPR,
												   '' as T014_Flag_Origem_Produto,
												   sum(T008_Valor_IPI_Devolucao) as T008_Valor_IPI_Devolucao,
												   D006_Flag_Deb_Crd_ICMS,
												   D006_Flag_Credita_IPI,
												   D006_Flag_Deb_Crd_PIS,
												   D006_Flag_Deb_Crd_COFINS
                                              from T008 
                                         left join D009 on D009_Id = T008_D009_Id
                                         left join D049 on D049_Id = D009_D049_Id
                                         left join D001 on D001_Id = D049_D001_Id
                                         left join D037 on D037_Id = D001_D037_Id
                                         left join T007 on T007_Id = T008_T007_Id
                                         left join T005 on T005_Id = T007_T005_Id
                                         left join D059 on D059_Id = T008_D059_Id
                                         left join D006 on D006_Id = T008_D006_Id
                                             where T008_T007_Id = '{$T007['T007_Id']}'
										  group by T008_Flag_Situacao_Tributaria, D006_Codigo_CFOP, T008_Aliquota_ICMS
                                             union
                                            select sum(T014_Valor_Total_Desconto) as T008_Valor_Desconto,
												   if(LENGTH(T014_Flag_Situacao_Tributaria) = 2, CONCAT(T014_Flag_Origem_Produto,T014_Flag_Situacao_Tributaria),T014_Flag_Situacao_Tributaria) as T008_Flag_Situacao_Tributaria,
                                                   D006_Codigo_CFOP,
                                                   T014_Aliquota_ICMS as T008_Aliquota_ICMS,                                                       
                                                   sum(T014_Valor_Total_Custo) as T008_Valor_Total_Preco, 
                                                   sum(T014_Valor_Base_ICMS) as T008_Valor_Base_ICMS,
												   sum(T014_Valor_Base_ICMS*((T014_Percentual_Reducao_ICMS)/100)) as T008_Valor_ICMS_Reducao,  
                                                   sum(T014_Valor_ICMS) as T008_Valor_ICMS,
                                                   sum(T014_Valor_Base_ICMS_Substituicao) as T008_Valor_Base_ICMS_Substituicao, 
                                                   sum(T014_Valor_ICMS_Substituicao) as T008_Valor_ICMS_Substituicao_Retencao, 
                                                   sum(T014_Valor_Base_ICMS) as T008_Valor_Base_ICMS_Sem_Reducao,
                                                   sum(T014_Valor_IPI) as T008_Valor_IPI, 
                                                   sum(T014_Valor_Frete) as T008_Valor_Total_Frete, 
                                                   (sum(T014_Valor_Total_Custo) + 
                                                    coalesce(sum(T014_Valor_IPI),0) + 
													sum(T014_Valor_ICMS_Substituicao) + 
                                                    if(T013_Numero_Conhecimento = 'NAO TEM', sum(T014_Valor_Frete), 0) + 
                                                    sum(T014_Valor_Despesas_Acessorias) + 
												    sum(T014_Valor_FCP_ST)) as VL_OPR,
												   T014_Flag_Origem_Produto,
												   0 as T008_Valor_IPI_Devolucao,
												   D006_Flag_Deb_Crd_ICMS,
												   D006_Flag_Credita_IPI,
												   D006_Flag_Deb_Crd_PIS,
												   D006_Flag_Deb_Crd_COFINS
                                              from T014 
                                         left join D009 on D009_Id = T014_D009_Id
                                         left join D049 on D049_Id = D009_D049_Id
                                         left join D001 on D001_Id = D049_D001_Id
                                         left join D037 on D037_Id = D001_D037_Id
                                         left join T013 on T013_Id = T014_T013_Id
                                         left join D006 on D006_Id = T014_D006_Id
                                             where T014_T013_Id = '{$T007['T013_Id']}'
										  group by T008_Flag_Situacao_Tributaria, D006_Codigo_CFOP, T014_Aliquota_ICMS 
                                 ) as totalNota
								  group by T008_Flag_Situacao_Tributaria, D006_Codigo_CFOP, T008_Aliquota_ICMS"); */

		$sqlT008 = mysql_query(" SELECT T008_Id,
										T008_Valor_Desconto,
										T008_Flag_Situacao_Tributaria,
										D006_Codigo_CFOP,
										T008_Aliquota_ICMS,									 
										T008_Valor_Total_Preco, 
										T008_Valor_Base_ICMS, 
										T008_Valor_ICMS,
										T008_Valor_Base_ICMS_Substituicao, 
										T008_Valor_ICMS_Substituicao_Retencao, 
										T008_Valor_ICMS_Reducao,
										T008_Valor_Base_ICMS_Sem_Reducao,
										T008_Valor_IPI, 
										T008_Valor_Total_Frete,
										VL_OPR,
										T014_Flag_Origem_Produto,
										T008_Valor_IPI_Devolucao,
										D006_Flag_Deb_Crd_ICMS,
										D006_Flag_Credita_IPI,
										D006_Flag_Deb_Crd_PIS,
										D006_Flag_Deb_Crd_COFINS
                                 FROM(
                                        SELECT T008_Id,
											   T008_Valor_Total_Desconto AS T008_Valor_Desconto,
											   T008_Flag_Situacao_Tributaria,
											   D006_Codigo_CFOP,
											   T008_Aliquota_ICMS,                                                       
											   T008_Valor_Total_Preco AS T008_Valor_Total_Preco, 
											   ROUND(T008_Valor_Base_ICMS_Impresso,2) AS T008_Valor_Base_ICMS, 
											   T008_Valor_Base_ICMS_Impresso*((T008_Percentual_Reducao_ICMS)/100) AS T008_Valor_ICMS_Reducao, 
											   T008_Valor_ICMS,
											   T008_Valor_Base_ICMS_Substituicao, 
											   T008_Valor_ICMS_Substituicao_Retencao, 
											   T008_Valor_Base_ICMS_Sem_Reducao,
											   COALESCE(T008_Valor_IPI,0) AS T008_Valor_IPI, 												   
											   T008_Valor_Total_Frete, 
											   ROUND((T008_Valor_Total_Preco + 
												COALESCE(T008_Valor_IPI,0) + 
												T008_Valor_ICMS_Substituicao_Retencao + 
												T008_Valor_Total_Frete + 
												T008_Valor_Total_Despesas_Acessorias + 
												T008_Valor_IPI_Devolucao + 
												T008_Valor_FCP_ST),2) AS VL_OPR,
												'' AS T014_Flag_Origem_Produto,
												T008_Valor_IPI_Devolucao,
												D006_Flag_Deb_Crd_ICMS,
												D006_Flag_Credita_IPI,
												D006_Flag_Deb_Crd_PIS,
												D006_Flag_Deb_Crd_COFINS
										   FROM T008 
                                      LEFT JOIN D009 ON D009_Id = T008_D009_Id
                                      LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                      LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                      LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                      LEFT JOIN T007 ON T007_Id = T008_T007_Id
                                      LEFT JOIN T005 ON T005_Id = T007_T005_Id
                                      LEFT JOIN D059 ON D059_Id = T008_D059_Id
                                      LEFT JOIN D006 ON D006_Id = T008_D006_Id
                                          WHERE T008_T007_Id = '{$T007['T007_Id']}'
                                          UNION
                                         SELECT  T014_Id as T008_Id,
										         T014_Valor_Total_Desconto AS T008_Valor_Desconto,
												 IF(LENGTH(T014_Flag_Situacao_Tributaria) = 2, CONCAT(T014_Flag_Origem_Produto,T014_Flag_Situacao_Tributaria),T014_Flag_Situacao_Tributaria) AS T008_Flag_Situacao_Tributaria,
                                                 D006_Codigo_CFOP,
                                                 T014_Aliquota_ICMS AS T008_Aliquota_ICMS,                                                       
                                                 T014_Valor_Total_Custo AS T008_Valor_Total_Preco, 
                                                 T014_Valor_Base_ICMS AS T008_Valor_Base_ICMS,
												 T014_Valor_Base_ICMS * ((T014_Percentual_Reducao_ICMS)/100) AS T008_Valor_ICMS_Reducao,  
                                                 T014_Valor_ICMS AS T008_Valor_ICMS,
                                                 T014_Valor_Base_ICMS_Substituicao AS T008_Valor_Base_ICMS_Substituicao, 
                                                 T014_Valor_ICMS_Substituicao AS T008_Valor_ICMS_Substituicao_Retencao, 
                                                 T014_Valor_Base_ICMS AS T008_Valor_Base_ICMS_Sem_Reducao,
                                                 T014_Valor_IPI AS T008_Valor_IPI, 
	                                             IF(IFNULL(T013A_Flag_Destaca_Frete_SPED, 'S') = 'S' AND T013_Numero_Conhecimento = 'NAO TEM', T014_Valor_Frete, 0) AS T008_Valor_Total_Frete, 
                                                 (T014_Valor_Total_Custo + 
                                                 COALESCE(T014_Valor_IPI,0) + 
												 T014_Valor_ICMS_Substituicao + 
	                                             IF(IFNULL(T013A_Flag_Destaca_Frete_SPED, 'S') = 'S' AND T013_Numero_Conhecimento = 'NAO TEM', T014_Valor_Frete, 0) + 
                                                 T014_Valor_Despesas_Acessorias + 
												 T014_Valor_FCP_ST) AS VL_OPR,
												 T014_Flag_Origem_Produto,
												 0 AS T008_Valor_IPI_Devolucao,
												 D006_Flag_Deb_Crd_ICMS,
												 D006_Flag_Credita_IPI,
												 D006_Flag_Deb_Crd_PIS,
												 D006_Flag_Deb_Crd_COFINS
                                            FROM T014 
                                       LEFT JOIN D009 ON D009_Id = T014_D009_Id
                                       LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                       LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                       LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                       LEFT JOIN T013 ON T013_Id = T014_T013_Id
                                       LEFT JOIN T013A ON T013A_T013_Id = T013_Id
                                       LEFT JOIN D006 ON D006_Id = T014_D006_Id
                                           WHERE T014_T013_Id = '{$T007['T013_Id']}'
                                 ) AS totalNota");
		
		$registrosC190 = array();
		while($T008 = mysql_fetch_array($sqlT008)){

			$VL_RED        = $T008['T008_Valor_ICMS_Reducao'];
			$VL_RED        = ($VL_RED > 0) ? $VL_RED : 0;
			$VL_OPR        = $T008['VL_OPR'] - $T008['T008_Valor_Desconto'];

			// Concatenando a origem e CST das notas de entrada, verifica se é 2 porque as notas de entrada pode vir CST (regime normal) com 2 caracteres e CSOSN (simples) com 3 caracteres
			$CST_ICMS      = substr($T008['T008_Flag_Situacao_Tributaria'],-3);
			$CST_ICMS      = (strlen($CST_ICMS) == 2 ? $T008['T014_Flag_Origem_Produto'].$CST_ICMS : $CST_ICMS);
			//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

			$VL_BC_ICMS    = $T008['T008_Valor_Base_ICMS'];
			$VL_ICMS       = $T008['T008_Valor_ICMS'];
			$ALIQ_ICMS     = $T008['T008_Aliquota_ICMS'];
			$VL_IPI        = $T008['T008_Valor_IPI'] + $T008['T008_Valor_IPI_Devolucao'];
			$VL_BC_ICMS_ST = $T008['T008_Valor_Base_ICMS_Substituicao'];
			$VL_ICMS_ST    = $T008['T008_Valor_ICMS_Substituicao_Retencao'];
			$CFOP          = removeChars($T008['D006_Codigo_CFOP']);
			
			//Para NFs de entrada os campos de valor de imposto, base de cálculo e alíquota só devem ser informados se tiver direito à crédito
			if($IND_OPER == 0 && $T008['D006_Flag_Deb_Crd_ICMS'] != 'S'){
				$VL_BC_ICMS = 0.00;
				$VL_ICMS    = 0.00;
				$ALIQ_ICMS  = 0.00;
			}

			if($IND_OPER == 0 && $T008['D006_Flag_Credita_IPI'] != 'S'){
				$VL_IPI    = 0.00;
			}
			//----------------------------------------------

			// Chave para agrupar os dados e depois lançar o C190
			// A aliquota ICMS da nota de entrada pode ser zerada por conta da CFOP, então vai ser agrupado os resultados no array
			$chave = $CST_ICMS . '-' . $CFOP . '-' . $ALIQ_ICMS;

			// Se a chave já existe, soma os valores
			if (isset($registrosC190[$chave])) {
				$registrosC190[$chave]['VL_OPR']        += $VL_OPR; 
				$registrosC190[$chave]['VL_BC_ICMS']    += $VL_BC_ICMS; 
				$registrosC190[$chave]['VL_ICMS']       += $VL_ICMS; 
				$registrosC190[$chave]['VL_BC_ICMS_ST'] += $VL_BC_ICMS_ST; 
				$registrosC190[$chave]['VL_ICMS_ST']    += $VL_ICMS_ST; 
				$registrosC190[$chave]['VL_RED']        += $VL_RED; 
				$registrosC190[$chave]['VL_IPI']        += $VL_IPI; 
			} else {
				// Caso contrário, inicializa os valores
				$registrosC190[$chave] = array(
					'CST_ICMS'   	=> $CST_ICMS, 
					'CFOP' 	     	=> $CFOP, 
					'ALIQ_ICMS'  	=> $ALIQ_ICMS, 
					'VL_OPR'     	=> $VL_OPR,  
					'VL_BC_ICMS' 	=> $VL_BC_ICMS, 
					'VL_ICMS'       => $VL_ICMS,
					'VL_BC_ICMS_ST' => $VL_BC_ICMS_ST, 
					'VL_ICMS_ST'    => $VL_ICMS_ST, 
					'VL_RED'        => $VL_RED, 
					'VL_IPI'        => $VL_IPI 
				);
			}
		}

		foreach ($registrosC190 as $chave => $dados) {

			$CST_ICMS      = $dados['CST_ICMS'];
			$CFOP          = $dados['CFOP'];
			$ALIQ_ICMS     = formataDecimal($dados['ALIQ_ICMS']);
			$VL_OPR        = formataDecimal($dados['VL_OPR']);
			$VL_BC_ICMS    = formataDecimal($dados['VL_BC_ICMS']);
			$VL_ICMS       = formataDecimal($dados['VL_ICMS']);
			$VL_BC_ICMS_ST = formataDecimal($dados['VL_BC_ICMS_ST']);
			$VL_ICMS_ST    = formataDecimal($dados['VL_ICMS_ST']);
			$VL_RED        = formataDecimal($dados['VL_RED']);
			$VL_IPI        = formataDecimal($dados['VL_IPI']);


			# REGISTRO C190
			$C190 = array(																	 #TAM													
				$REG = 'C190',																 #004 Texto fixo contendo "C190"
				$CST_ICMS,                                                                   #003 Código da Situação Tributária, conforme a Tabela indicada no item 4.3.1
				$CFOP,											                             #004 Código Fiscal de Operação e Prestação do agrupamento de itens
				$ALIQ_ICMS,                                                                  #006 Alíquota do ICMS
				$VL_OPR,                                                                     # -  Valor da operação na combinação de CST_ICMS, CFOP e alíquota do ICMS, correspondente ao somatório do valor das	mercadorias, despesas acessórias (frete, seguros e outras despesas acessórias), ICMS_ST, FCP_ST e IPI. 
				$VL_BC_ICMS,				 											  	 # -  Parcela correspondente ao "Valor da base de cálculo do ICMS" referente à combinação de	CST_ICMS, CFOP e alíquota do ICMS.
				$VL_ICMS,						                                             # -  Parcela correspondente ao "Valor do ICMS", incluindo o FCP, quando aplicável, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS
				$VL_BC_ICMS_ST,																 # -  Parcela correspondente ao "Valor da base de cálculo do ICMS" da substituição tributária referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
				$VL_ICMS_ST,																 # -  Parcela correspondente ao valor creditado/debitado do ICMS da substituição tributária, incluindo o FCP_ ST, quando aplicável, referente à combinação de CST_ICMS, CFOP, e alíquota do ICMS.
				$VL_RED,			                                                         # -  Valor não tributado em função da redução da base de cálculo do ICMS, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS
				$VL_IPI,							                                         # -  Parcela correspondente ao "Valor do IPI" referente à combinação CST_ICMS, CFOP e alíquota do ICMS.
				$OBS = ''                                                                    # -  Código da observação do lançamento fiscal (campo 02 do Registro 0460)
			);
			$C190 = implode("|", $C190);
			$layout .= '|' . $C190 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
			//--------------------------------------------------			
		}
						
		$T297;
		if(!empty($T007['T013_Id'])) {
			$T297 = mysql_query("SELECT T297_Id, D188_Codigo_Observacao,D188_Descricao_Observacao, T297_Descricao_Complementar_Observacao FROM T297 LEFT JOIN D188 ON D188_Id = T297_D188_Id WHERE T297_T013_Id = '{$T007['T013_Id']}'");
		} else {
			$T297 = mysql_query("SELECT T297_Id, D188_Codigo_Observacao,D188_Descricao_Observacao, T297_Descricao_Complementar_Observacao FROM T297 LEFT JOIN D188 ON D188_Id = T297_D188_Id WHERE T297_T007_Id = '{$T007['T007_Id']}'");
		}		

		while($mT297 = mysql_fetch_array($T297)) {
			$T298 = mysql_query("SELECT D189_Codigo_Ajuste,
							T298_Descricao_Complementar_Ajuste,
							D001_Codigo_Produto,
							T298_Valor_Base_ICMS,
							T298_Aliquota_ICMS,
							T298_Valor_ICMS,
							T298_Outros_Valores
						FROM T298
							LEFT JOIN D189 ON D189_Id = T298_D189_Id
							LEFT JOIN T014 ON T014_Id = T298_T014_Id
							LEFT JOIN T008 ON T008_Id = T298_T008_Id
							LEFT JOIN D009 ON D009_Id = COALESCE(T008_D009_Id, T014_D009_Id)
							LEFT JOIN D049 ON D049_Id = D009_D049_Id 
							LEFT JOIN D001 ON D001_Id = D049_D001_Id
						WHERE T298_T297_Id = '{$mT297['T297_Id']}'");

			$ajustesDocumentoFiscal = array();
			while($mT298 = mysql_fetch_array($T298)) {
				// Codigos da tabela 5.1.1 (UF + 6 digitos) pertencem aos ajustes
				// de apuracao do bloco E e nao podem ser informados no C197,
				// cujo COD_AJ deve ser um codigo de ajuste do documento fiscal.
				$codigoAjuste = trim($mT298['D189_Codigo_Ajuste']);
				if (preg_match('/^[A-Z]{2}[0-9]{6}$/', $codigoAjuste)) {
					continue;
				}
				$ajustesDocumentoFiscal[] = $mT298;
			}

			$C195 = array(																	 	#TAM
				$REG = 'C195',																 	#004 Texto fixo contendo "C190"
				$COD_OBS = $mT297['D188_Codigo_Observacao'],								 	#006 Código da observação do lançamento fiscal (campo 02 do Registro 0460)
				$TXT_COMPL = tirarEspacos($mT297['T297_Descricao_Complementar_Observacao'])		# Descrição complementar do código de observação
			);

			//popula registro 0460
			insertRegistro0460($mT297['D188_Codigo_Observacao'], $mT297['D188_Descricao_Observacao']);

			$C195 = implode("|", $C195);
			$layout .= '|' . $C195 . '|' . _SALTO_LINHA;

			$contador_bloco_C++;
			foreach($ajustesDocumentoFiscal as $mT298) {

				$C197 = array(																			 	#TAM													
					$REG = 'C197',																 			#004 Texto fixo contendo "C190"					
					$COD_AJ = $mT298['D189_Codigo_Ajuste'],													#010* Código do ajustes/benefício/incentivo
					$DESCR_COMPL_AJ = tirarEspacos($mT298['T298_Descricao_Complementar_Ajuste']),			# Descrição complementar do ajuste do documento fiscal
					$COD_ITEM = $mT298['D001_Codigo_Produto'],												#060 Código do item (campo 02 do Registro 0200)
					$VL_BC_ICMS = formataDecimal($mT298['T298_Valor_Base_ICMS']),							# Base de cálculo do ICMS ou do ICMS ST
					$ALIQ_ICMS = formataDecimal($mT298['T298_Aliquota_ICMS']),								#006 Alíquota do ICMS
					$VL_ICMS = formataDecimal($mT298['T298_Valor_ICMS']),									# Valor do ICMS ou do ICMS ST
					$VL_OUTROS = formataDecimal($mT298['T298_Outros_Valores'])								# Outros valores
				);
				$C197 = implode("|", $C197);
				$layout .= '|' . $C197 . '|' . _SALTO_LINHA;
				$contador_bloco_C++;
			}
		}
	}
	
	$sqlD141 = mysql_query("SELECT D141_Modelo,  
								   D141_Numero_Serie,
								   D141_Numero_Caixa
							FROM D141");
	if(mysql_num_rows($sqlD141) > 0){
		while($D141 = mysql_fetch_array($sqlD141)){
			# REGISTRO C400
			$C400 = array(
				$REG = 'C400',
				$COD_MOD = '2D',
				$ECF_MOD = $D141['D141_Modelo'],
				$ECF_FAB = $D141['D141_Numero_Serie'],
				$ECF_CX = $D141['D141_Numero_Caixa']													
			);
			$C400 = implode("|", $C400);
			$layout .= '|' . $C400 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
		}
		$sqlT007 = mysql_query("SELECT T007_Data_Emissao 
								FROM T007 
								WHERE T007_Data_Emissao >= '{$dataInicial}' 
								AND T007_Data_Emissao <= '{$dataFinal}' 
								AND T007_C004_Id = '{$g['empresaAtual']}'
								AND T007_Flag_ACP = '4' 
								GROUP BY T007_Data_Emissao");
		while($T007 = mysql_fetch_array($sqlT007)){
			# REGISTRO C405
			$C405 = array(																
				$REG = 'C405',
				$DT_DOC = formataData($T007['T007_Data_Emissao']),
				$CRO = '',
				$CRZ = '',
				$NUM_COO_FIN = '',
				$GT_FIN = '',
				$VL_BRT = ''
			);
			$C405 = implode("|", $C405);
			$layout .= '|' . $C405 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
			
			$sqlT008 = mysql_query("SELECT SUM(T008_Valor_PIS) AS TOTALPIS, 
										   SUM(T008_Valor_COFINS) AS TOTALCOFINS 
									FROM T008 
									LEFT JOIN T007 ON T007_Id = T008_T007_Id 
									WHERE T007_Data_Emissao = '{$T007['T007_Data_Emissao']}'");
			$T008 = mysql_fetch_array($sqlT008);
			# REGISTRO C410
			$C410 = array(																
				$REG = 'C410',
				$VL_PIS = $T008['TOTALPIS'],
				$VL_COFINS = $T008['TOTALCOFINS']
			);
			$C410 = implode("|", $C410);
			$layout .= '|' . $C410 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
		}

		$sqlT007 = mysql_query("SELECT T007_Id,
									   T007_Flag_Cancelada,
									   T007_Numero_Nota_Fiscal,
									   T007_Valor_Total,
									   T007_Valor_Total_PIS,
									   T007_Valor_Total_COFINS,
									   D024_Flag_Pessoa_Fisica_Juridica,
									   D024_Cnpj,
									   D024_Cpf,
									   D024_Nome_Empresa,
									   T007_Numero_Nota_Fiscal,
									   T007_Flag_Cancelada,
									   D006_Codigo_CFOP
								FROM T007 
								LEFT JOIN D006 ON D006_Id = T007_D006_Id 
								LEFT JOIN T005 ON T005_Id = T007_T005_Id
								LEFT JOIN T006 ON T005_Id = T006_T005_Id
								LEFT JOIN D024 ON D024_Id = T007_D024_Id
								WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' 
								AND T007_C004_Id = '{$g['empresaAtual']}' 
								AND T007_Numero_Protocolo_Nfe > 0
								AND T007_Flag_ACP = '4'
								GROUP BY T007_Id ORDER BY T007_Data_Emissao");
		while($T007 = mysql_fetch_array($sqlT007)){
			# REGISTRO C460	
			if($T007['T007_Flag_Cancelada'] == 'S'){
				$COD_SIT = '06';
				$DT_DOC = '';
				$VL_DOC = '';
				$VL_PIS = '';
				$VL_COFINS = '';
				$CPF_CNPJ = '';
				$NOM_ADQ = '';
			} else {
				$COD_SIT = '00';
				$DT_DOC = formataData($T007['T007_Numero_Nota_Fiscal']);
				$VL_DOC = formataDecimal($T007['T007_Valor_Total']);
				$VL_PIS = formataDecimal($T007['T007_Valor_Total_PIS']);
				$VL_COFINS = formataDecimal($T007['T007_Valor_Total_COFINS']);
				if($T007['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
					$CPF_CNPJ = removeChars($T007['D024_Cnpj']);	
				} else if($T007['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
					$CPF_CNPJ = removeChars($T007['D024_Cpf']);
				}
				$NOM_ADQ = $T007['D024_Nome_Empresa'];
			}			
			$C460 = array(
				$REG = 'C460',
				$COD_MOD = '2D',															#002*	Código do modelo do documento fiscal
				$COD_SIT,																	# 		Código da situação do documento fiscal
				$NUM_DOC = $T007['T007_Numero_Nota_Fiscal'],								#006	Número do documento fiscal (COO)
				$DT_DOC,																	#008*	Data da emissão do documento fiscal
				$VL_DOC,																	#002	Valor total do documento fiscal
				$VL_PIS,																	#002 	Valor do PIS
				$VL_COFINS,																	#002 	Valor da COFINS
				$CPF_CNPJ,																	#014 	CPF ou CNPJ do adquirente
				$NOM_ADQ																	#060 	Nome do adquirente
			);
			$C460 = implode("|", $C460);
			$layout .= '|' . $C460 . '|' . _SALTO_LINHA;	
			$contador_bloco_C++;

			if($T007['T007_Flag_Cancelada'] != 'S'){
				$sqlT008 = mysql_query("SELECT T008_Id,
											   D001_Codigo_Produto,
				 							   T008_Quantidade,
				 							   D037_Unidade,
				 							   T008_Valor_Total_Preco_Sem_Desconto,
				 							   D059_Codigo_ST,
				 							   T008_Aliquota_ICMS,
				 							   T008_Valor_PIS,
				 							   T008_Valor_COFINS,
				 							   D037_Id,
				 							   D001_Id
										FROM T008 
										LEFT JOIN D009 ON D009_Id = T008_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D001 ON D001_Id = D049_D001_Id
										LEFT JOIN D037 ON D037_Id = D001_D037_Id
										LEFT JOIN T007 ON T007_Id = T008_T007_Id
										LEFT JOIN T005 ON T005_Id = T007_T005_Id
										LEFT JOIN T006 ON T005_Id = T006_T005_Id
										LEFT JOIN D059 ON D059_Id = T008_D059_Id
										WHERE T008_T007_Id = '{$T007['T007_Id']}'
										GROUP BY T008_Id");
				while($T008 = mysql_fetch_array($sqlT008)){
					# REGISTRO C470
					$C470 = array(
						$REG = 'C470',
						$COD_ITEM = $T008['D001_Codigo_Produto'],									#060 	Código do item (campo 02 do Registro 0200) 
						$QTD = $T008['T008_Quantidade'],											#003 	Quantidade do item
						$QTD_CANC = '',           													#003 	Quantidade cancelada, no caso de cancelamento parcial de item 
						$UNID = adicionarEspape($T008['D037_Unidade']),								#006 	Unidade do item (Campo 02 do registro 0190) 
						$VL_ITEM = formataDecimal($T008['T008_Valor_Total_Preco_Sem_Desconto']),	#002 	Valor total do item
						$CST_ICMS = $T008['D059_Codigo_ST'],										#003* 	Código da Situação Tributária, conforme a Tabela indicada no item 4.3.1. 
						$CFOP = removeChars($T007['D006_Codigo_CFOP']),								#004*  	Código Fiscal de Operação e Prestação
						$ALIQ_ICMS = formataDecimal($T008['T008_Aliquota_ICMS']),					#004* 	Alíquota do ICMS – Carga tributária efetiva em percentual
						$VL_PIS = formataDecimal($T008['T008_Valor_PIS']),      					#006 	Valor do PIS 
						$VL_COFINS = formataDecimal($T008['T008_Valor_COFINS'])						#002 	Valor da COFINS
					);
					$C470 = implode("|", $C470);
					$layout .= '|' . $C470 . '|' . _SALTO_LINHA;
					$contador_bloco_C++;
					verificaRegistro0200($T008['D001_Id']);
					verificaRegistro0190($T008['D037_Id']);
				}
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
								T013_Valor_Desconto,
								T013_Valor_Total_Produtos,
								T013_Valor_Despesas_Acessorias,
								T013_Valor_Total_Base_ICMS,
								T013_Valor_Total_ICMS,
								T013_Valor_Total_Base_ICMS_Substituicao,
								T013_Valor_ICMS_Substituicao,
								T013_Valor_Total_PIS,
								T013_Valor_Total_COFINS,
								D014_Codigo_Plano_Conta_Fiscal,
								D006_Flag_Deb_Crd_ICMS,  
								D006_Flag_Deb_Crd_PIS,
								D006_Flag_Deb_Crd_COFINS,
								T013_Tipo_Ligacao,
								T013_Grupo_Tensao,
								T013_Classe_Consumo,
								T013_Valor_Mao_Obra,
								T013A_Codigo_Situacao_Documento,
								sum(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T013_Valor_Base_ICMS_Sem_Reducao        								
						   FROM T013
					  LEFT JOIN D006 ON D006_Id = T013_D006_Id
					  LEFT JOIN D014 ON D014_Id = D006_D014_Id
					  LEFT JOIN T014 ON T014_T013_Id = T013_Id
					  LEFT JOIN T013A ON T013_Id = T013A_T013_Id
						  WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
							AND {$dataNFRecebida} <= '{$dataFinalHora}'
							AND T013_C004_Id = '{$g['empresaAtual']}'
							AND T013_Flag_Cancelada != 'S'
							AND T013_Flag_Estoque_Finalizado = 'S'
							AND (T013_Tipo = '6' OR T013_Tipo = '11')
					   GROUP BY T013_Id");

	while($T013 = mysql_fetch_array($sqlT013)) {
		$IND_OPER	= 0;
		$IND_EMIT	= 1;
		$VL_DESC	= $T013['T013_Valor_Desconto'];
		$VL_FORN	= $T013['T013_Valor_Total_Produtos'];
		$VL_DA		= $T013['T013_Valor_Despesas_Acessorias'];
		$VL_BC_ICMS	= $T013['T013_Valor_Total_Base_ICMS'];
		$VL_ICMS	= $T013['T013_Valor_Total_ICMS'];
		$VL_BC_ICMS_ST= $T013['T013_Valor_Total_Base_ICMS_Substituicao'];
		$VL_ICMS_ST	= $T013['T013_Valor_ICMS_Substituicao'];
		$VL_PIS		= $T013['T013_Valor_Total_PIS'];
		$VL_COFINS	= $T013['T013_Valor_Total_COFINS'];
		$VL_TERC	= 0;
		$ENER_INJET = 0;
		$OUTRAS_DED = 0;
		
		//Valores Calculados
		//$VL_SERV_NT = $T013['T013_Valor_Base_ICMS_Sem_Reducao'] - $T013['T013_Valor_Total_Base_ICMS'];
		$VL_SERV_NT = $T013['T013_Valor_Mao_Obra'];
		$VL_DOC = ($VL_FORN  + $VL_DA + $VL_SERV_NT + $VL_TERC) - ($VL_DESC + $ENER_INJET + $OUTRAS_DED);

		/*para documentos de entrada, os campos de valor de imposto, base de cálculo e alíquota só devem
		ser informados se o adquirente tiver direito à apropriação do crédito (enfoque do declarante).
		*/

		if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_ICMS'] != 'S'){
			$VL_BC_ICMS = '0,00';
			$VL_ICMS = '0,00';
			$VL_BC_ICMS_ST = '0,00';
			$VL_ICMS_ST = '0,00';
		}

		if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_PIS'] != 'S') {
			$VL_PIS = '0,00';
		}

		if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_COFINS'] != 'S') {
			$VL_COFINS = '0,00';
		}

		if($T013['T013_Tipo'] == '6'){
			//Nota Fiscal/Conta de Energia Elétrica (Código 06)
			$COD_MOD = '06';
		} else{
			//Nota Fiscal de Energia Elétrica Eletrônica- NF3e (Código 66)
			$COD_MOD = "66";
		}

		$COD_SIT = (!empty($T013['T013A_Codigo_Situacao_Documento'])) ? $T013['T013A_Codigo_Situacao_Documento'] : '00' ;

		//Registro C500	
		$C500 = array(																#TAM
			$REG = "C500",															#004*	Texto fixo contendo C500			
			$IND_OPER,																#Indicador do tipo de operação:0 - Entrada;1 - Saída
			$IND_EMIT,																#Indicador do emitente do documento fiscal:0 - Emissão própria;1 - Terceiros
			$COD_PART = $T013['T013_D024_Id'],										#Código do participante (campo 02 doRegistro 0150):												
			$COD_MOD, 																#COD_MOD Código do modelo do documento fiscal, conforme a Tabela 4.1.1
			$COD_SIT, 														#COD_SIT Código da situação do documento fiscal, conforme a Tabela 4.1.2
			$SER = '', 																#SER Série do documento fiscal
			$SUB = '', 																#SUB Subsérie do documento fiscal
			$COD_CONS = $T013['T013_Classe_Consumo'], 								#COD_CONS - Código de classe de consumo de energia elétrica ou gás
			$NUM_DOC = $T013['T013_Numero_Nota_Fiscal'],							#NUM_DOC Número do documento fiscal
			$DT_DOC = formataData(substr($T013['T013_Data_Emissao'],0,10)), 		#DT_DOC Data da emissão do documento fiscal
			$DT_E_S = formataData(substr($T013[$dataNFRecebidaLancarSPED],0,10)), 		#DT_E_S Data da entrada ou da saída
			formataDecimal($VL_DOC), 												#VL_DOC Valor total do documento fiscal
			formataDecimal($VL_DESC), 												#VL_DESC Valor total do desconto
			formataDecimal($VL_FORN), 												#VL_FORN Valor total fornecido/consumido
			formataDecimal($VL_SERV_NT), 											#VL_SERV_NT Valor total dos serviços não-tributados pelo ICMS
			formataDecimal($VL_TERC), 												#VL_TERC Valor total cobrado em nome de terceiros
			formataDecimal($VL_DA), 												#VL_DA Valor total de despesas acessórias indicadas no documento fiscal
			formataDecimal($VL_BC_ICMS), 											#VL_BC_ICMS Valor acumulado da base de cálculo do ICMS
			formataDecimal($VL_ICMS), 												#VL_ICMS Valor acumulado do ICMS
			formataDecimal($VL_BC_ICMS_ST), 										#VL_BC_ICMS_ST Valor acumulado da base de cálculo do ICMS substituição tributária
			formataDecimal($VL_ICMS_ST), 											#VL_ICMS_ST Valor acumulado do ICMS retido por substituição tributária
			$COD_INF = '', 															#COD_INF Código da informação complementar do documento fiscal (campo 02 do Registro 0450)
			formataDecimal($VL_PIS), 												#VL_PIS Valor do PIS
			formataDecimal($VL_COFINS), 											#VL_COFINS Valor da COFINS
			$TP_LIGACAO = $T013['T013_Tipo_Ligacao'], 								#TP LIGACAO Código de tipo de Ligação
			$COD_GRUPO_TENSAO =  $T013['T013_Grupo_Tensao'], 						#COD_GRUPO_TENSAO Código de grupo de tensão
			$CHV_DOCe = '', 														#CHV_DOCe Chave da Nota Fiscal de Energia Elétrica Eletrônica
			$FIN_DOCe = '', 														#FIN_DOCe Finalidade da emissão do documento eletrônico: 1 – Normal 2 – Substituição 3 – Normal com ajuste
			$CHV_DOCe_REF = '', 													#CHV_DOCe_REF Chave da nota referenciada.
			$IND_DEST = '', 														#IND_DEST Indicador do Destinatário/Acessante: 1 – Contribuinte do ICMS; 2 – Contribuinte Isento de Inscrição no Cadastro de Contribuintes do ICMS; 9 – Não Contribuinte.
			$COD_MUN_DEST = '', 													#COD_MUN_DEST Código do município do destinatário conforme a tabela do IBGE.
			$COD_CTA = $T013['D014_Codigo_Plano_Conta_Fiscal'], 					#COD_CTA Código da conta analítica contábil debitada/creditada
			$COD_MOD_DOC_REF = '', 													#COD_MOD_DOC_REF Código do modelo do documento fiscal referenciado, conforme a Tabela 4.1.1
			$HASH_DOC_REF = '', 													#HASH_DOC_REF Código de autenticação digital do registro (Convênio 115/2003).
			$SER_DOC_REF = '', 														#SER_DOC_REF Série do documento fiscal referenciado.
			$NUM_DOC_REF = '', 														#NUM_DOC_REF Número do documento fiscal referenciado.
			$MES_DOC_REF = '', 														#MES_DOC_REF Mês e ano da emissão do documento fiscal referenciado.
			formataDecimal($ENER_INJET), 											#ENER_INJET Energia injetada
			formataDecimal($OUTRAS_DED), 											#OUTRAS_DED Outras deduções 
		);

		$C500 = implode("|", $C500);
		$layout .= '|' . $C500 . '|' . _SALTO_LINHA;
		$contador_bloco_C++;

		$sqlT014 = mysql_query("SELECT
									T014_Flag_Situacao_Tributaria,
									D006_Codigo_CFOP, 
									sum(T014_Valor_Base_ICMS) as T014_Valor_Base_ICMS,
									T014_Aliquota_ICMS,
									sum(T014_Valor_ICMS) as T014_Valor_ICMS,
									sum(T014_Valor_Base_ICMS_Substituicao) as T014_Valor_Base_ICMS_Substituicao,
									sum(T014_Valor_ICMS_Substituicao) as T014_Valor_ICMS_Substituicao,	
									sum(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T014_Valor_Base_ICMS_Sem_Reducao,					
									(	sum(T014_Valor_Total_Custo) + 
										sum(T014_Valor_Despesas_Acessorias)
									) as VL_OPR,
									D188_Codigo_Observacao,
    								D188_Descricao_Observacao
								from
									T014
										left join T013 on T013_Id = T014_T013_Id
										left join D006 on D006_Id = T014_D006_Id
										left join D014 on D014_Id = D006_D014_Id										   
										left join 
											(SELECT T297_T013_Id, D188_Codigo_Observacao,D188_Descricao_Observacao FROM T297 LEFT JOIN D188 ON D188_Id = T297_D188_Id WHERE T297_T013_Id = '{$T013['T013_Id']}' LIMIT 1)
												as observacoes on T297_T013_Id = T013_Id
									where T013_Id = '{$T013['T013_Id']}'
									group by T014_Flag_Situacao_Tributaria, D006_Codigo_CFOP, T014_Aliquota_ICMS");

		while($T014 = mysql_fetch_array($sqlT014)) {					
			$VL_RED_BC = $T014['T014_Valor_Base_ICMS_Sem_Reducao'] - $T014['T014_Valor_Base_ICMS'];
			$VL_RED_BC = $VL_RED_BC  > 0 ? $VL_RED_BC : 0;
			$VL_OPR = $T014['VL_OPR'];

			$VL_BC_ICMS = formataDecimal($T014['T014_Valor_Base_ICMS']);		
			$VL_ICMS = formataDecimal($T014['T014_Valor_ICMS']);			
			$VL_BC_ICMS_ST = formataDecimal($T014['T014_Valor_Base_ICMS_Substituicao']);
			$VL_ICMS_ST = formataDecimal($T014['T014_Valor_ICMS_Substituicao']);

			$C590 = array(
				$REG = "C590",   													#REG Texto fixo contendo "C590"
				$CST_ICMS = $T014['T014_Flag_Situacao_Tributaria'],  				#CST_ICMS Código da Situação Tributária, conforme a Tabela indicada no item 4.3.1.
				$CFOP = removeChars($T014['D006_Codigo_CFOP']),   					#CFOP Código Fiscal de Operação e Prestação do agrupamento de itens
				$ALIQ_ICMS = formataDecimal($T014['T014_Aliquota_ICMS']),   		#ALIQ_ICMS Alíquota do ICMS
				formataDecimal($VL_OPR),   											#VL_OPR Valor da operação correspondente à combinação de CST_ICMS, CFOP, e alíquota do ICMS.
				$VL_BC_ICMS,   														#VL_BC_ICMS Parcela correspondente ao "Valor da base de cálculo do ICMS" referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
				$VL_ICMS,   														#VL_ICMS Parcela correspondente ao "Valor do ICMS" referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
				$VL_BC_ICMS_ST,   													#VL_BC_ICMS_ST Parcela correspondente ao "Valor da base de cálculo do ICMS" da substituição tributária referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
				$VL_ICMS_ST,   														#VL_ICMS_ST Parcela correspondente ao valor creditado/debitado do ICMS da substituição tributária, referente à combinação de CST_ICMS, CFOP, e alíquota do ICMS.
				formataDecimal($VL_RED_BC),   										#VL_RED_BC Valor não tributado em função da redução da base de cálculo do ICMS, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
				$COD_OBS =  $T014['D188_Codigo_Observacao'],   						#COD_OBS Código da observação do lançamento fiscal (campo 02 do Registro 0460)
			);

			// //popula registro 0460
			if(!empty($T014['D188_Codigo_Observacao'])) {
				insertRegistro0460($T014['D188_Codigo_Observacao'], $T014['D188_Descricao_Observacao']);
			}

			$C590 = implode("|", $C590);
			$layout .= '|' . $C590 . '|' . _SALTO_LINHA;
			$contador_bloco_C++;
		}
	}
}

# REGISTRO C990
$C990 = array(																
	$REG = 'C990',
	$QTD_LIN_C = $contador_bloco_C+1
);
$C990 = implode("|", $C990);
$layout .= '|' . $C990 . '|' . _SALTO_LINHA;

$linhas_bloco_C = $QTD_LIN_C;

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
		break;		case '06':
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
