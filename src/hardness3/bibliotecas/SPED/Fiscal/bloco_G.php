<?php

namespace hardness;

global $g;
/*
BLOCO G: CIAP - Creditos de ICMS

Bloco de registros dos dados relativos ao CIAP – Controle de Crédito do Ativo Permanente cujo objetivo é
demonstrar o cálculo da parcela do crédito de ICMS apropriada no mês, decorrente da entrada de mercadorias destinadas ao
ativo imobilizado, conforme previsto no art. 20, § 5º, da Lei Complementar nº 87, de 13 de setembro de 1996.

REG	   OCOR    DESCRIÇÃO																	
G001	1	   Este registro deve ser gerado para abertura do bloco G, indicando se há registros de informações no bloco.
G110	N	   Este registro tem o objetivo de prestar informações sobre o CIAP:
                a) saldo de ICMS do CIAP, composto pelo valor do ICMS de bens ou componentes (somente componentes cujo
                crédito de ICMS já foi apropriado) que entraram anteriormente ao período de apuração. (campo 4);
                b) o somatório das parcelas de ICMS passíveis de apropriação de cada bem ou componente, inclusive aqueles que
                foram escriturados no CIAP em período anterior (campo 5);
                Guia Prático EFD-ICMS/IPI – Versão 3.1.5
                Atualização: 24 de agosto de 2023
                Página 253 de 360
                c) o valor do índice de participação do somatório do valor das saídas tributadas e saídas para exportação no valor
                total das saídas (campo 8) - (o valor é sempre igual ou menor que 1 (um);
                d) o valor de ICMS a ser apropriado como crédito. Esse valor (campo 9) será apropriado diretamente no Registro de
                Apuração do ICMS, como ajuste de apuração, salvo se a legislação obrigar à emissão de documento fiscal;
                e) o valor de outras parcelas de ICMS a ser apropriado. Esse valor (campo 10) será apropriado diretamente no
                Registro de Apuração do ICMS, como ajuste de apuração, salvo se a legislação obrigar à emissão de documento fiscal
G125	1:N	   Este registro tem o objetivo de informar as movimentações de bens ou componentes no CIAP e a apropriação de parcelas de créditos de ICMS do Ativo Imobilizado.
                Inclui-se no conceito de movimentação:
                a) entrada de bem ou componente no CIAP;
                b) saída de bem ou componente do CIAP;
                c) baixa de bem ou componente do CIAP;
                d) entrada no CIAP pela conclusão de bem que estava sendo construído pelo contribuinte (exceto quando o bem ou
                componente gerar créditos a partir do momento de sua entrada).
                Validação do Registro: Não podem ser informados dois ou mais registros com a mesma combinação de conteúdo
                nos campos COD_IND_BEM e TIPO_MOV.
G130	1:N	   Este registro tem o objetivo de identificar o documento fiscal que acobertou a entrada ou a saída do bem ou componente do CIAP.
                Quando o tipo de movimentação – TIPO_MOV do registro G125 – for igual a "MC", "IM", "IA" ou "AT", este
                registro é obrigatório.
                Caso exista previsão legal de emissão de documento fiscal para os demais tipos de movimentação – TIPO_MOV do
                registro G125 – esse registro deverá ser informado.
                No período em que se iniciar a obrigação de escrituração fiscal digital do CIAP ou quando isso ocorrer de forma
                espontânea, este registro é obrigatório nas seguintes situações:
                a) quando o tipo de movimentação – TIPO_MOV do registro G125 – for igual a “SI” e esse “SI” for originado dos
                tipos de movimentação “IM”, “IA” ou “MC”;
                b) quando o tipo de movimentação – TIPO_MOV do registro G125 – for igual a “SI” e esse “SI” for originado do
                tipo de movimentação “CI”, devem ser informados os documentos fiscais relativos ao tipo de movimentação “IA” dos seus
                componentes que entraram antes desse período;
                c) quando o tipo de movimentação – TIPO_MOV do registro G125 – for igual a “CI”, devem ser informados os
                documentos fiscais relativos ao tipo de movimentação “IA” dos seus componentes que entraram antes desse período. 
G140    1:N    Este registro tem o objetivo de identificar o item do documento fiscal informado no registro G130.
                Validação do Registro: Não podem ser informados dois ou mais registros com o mesmo valor no campo
                NUM_ITEM + COD_ITEM.
G990	1	   Encerramento do bloco 	
*/
$contador_bloco_G = 1;

$T316 = mysql_query("SELECT T316_Id 
					   FROM T316
				  LEFT JOIN T014 ON T014_Id = T316_T014_Id
				  LEFT JOIN T013 ON T013_Id = T014_T013_Id
				      WHERE T316_Flag_Ativo_Baixado = 'N'
					    AND T316_Numero_Parcelas_Apropriacao > 0
					    AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
					    AND T013_C004_Id = '{$g['empresaAtual']}' 
					    AND T013_Flag_Cancelada != 'S' 
					    AND T013_Numero_Protocolo_Nfe > 0
					    AND T013_Flag_Estoque_Finalizado = 'S'
					    AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
					    AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
					UNION
					 SELECT T316_Id 
					   FROM T316
				  LEFT JOIN T008 ON T008_Id = T316_T008_Id
				  LEFT JOIN T007 ON T007_Id = T008_T007_Id
					  WHERE T316_Flag_Ativo_Baixado = 'N'
						AND T316_Numero_Parcelas_Apropriacao > 0
						AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
						AND T007_C004_Id = '{$g['empresaAtual']}' 
						AND T007_Flag_Cancelada != 'S' 
						AND T007_Numero_Protocolo_Nfe > 0
						AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
						AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')");
$registros = mysql_num_rows($T316);

$IND_MOV = ($gerarBlocoG == 'S' && $registros > 0) ? '0' : '1';

# REGISTRO G001 - ABERTURA DO BLOCO H
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "G001" 																										C 		004		- 		O
	02 ND_MOV 			Indicador de movimento:                                                                                                         C 		001		- 		O
                        0- Bloco com dados informados
                        1- Bloco sem dados informados 																		                            
*/

#REGISTRO H001 - INICIADO COM AS VARIAVEIS AQUI, POREM MONTADO MAIS PRA BAIXO \/ \/
$G001 = array(
	$REG = 'G001',
	$IND_MOV
);
$G001 = implode("|", $G001);
$layout .= '|' . $G001 . '|' . _SALTO_LINHA;
$contador_bloco_G++;

if($IND_MOV == '0') {

    $T316 = mysql_query("SELECT T014_Valor_ICMS,
	                            T014_Quantidade,
								D001_Codigo_Produto,
								D001_Id,
								T014_Id,
								T316_Data_Inicio_Apropriacao_Credito,
								T316_Numero_Parcelas_Apropriacao,
								T316_Codigo_Produto_CIAP,
								T316_Id,
								T013_Serie,
								T013_Numero_Nota_Fiscal,
								T013_Chave_Acesso_Nfe,	
								T013_Data_Emissao,				
								T013_D024_Id,
								D037_Unidade
						   FROM T316
					  LEFT JOIN T014 ON T014_Id = T316_T014_Id
					  LEFT JOIN D009 ON D009_Id = T014_D009_Id
					  LEFT JOIN D049 ON D049_Id = D009_D049_Id
					  LEFT JOIN D001 ON D001_Id = D049_D001_Id
					  LEFT JOIN D037 ON D037_Id = D001_D037_Id
					  LEFT JOIN T013 ON T013_Id = T014_T013_Id
					      WHERE T316_Flag_Ativo_Baixado = 'N'
						    AND T316_Numero_Parcelas_Apropriacao > 0
							AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
					        AND T013_C004_Id = '{$g['empresaAtual']}' 
						    AND T013_Flag_Cancelada != 'S' 
						    AND T013_Numero_Protocolo_Nfe > 0
						    AND T013_Flag_Estoque_Finalizado = 'S'
					        AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
						    AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
					UNION
						 SELECT T008_Valor_ICMS AS T014_Valor_ICMS,
								T008_Quantidade AS T014_Quantidade,
								D001_Codigo_Produto,
								D001_Id,
								T008_Id AS T014_Id,
								T316_Data_Inicio_Apropriacao_Credito,
								T316_Numero_Parcelas_Apropriacao,
								T316_Codigo_Produto_CIAP,
								T316_Id,
								T007_Serie AS T013_Serie,
								T007_Numero_Nota_Fiscal AS T013_Numero_Nota_Fiscal,
								T007_Chave_Acesso_Nfe AS T013_Chave_Acesso_Nfe,	
								T007_Data_Emissao AS T013_Data_Emissao,				
								T007_D024_Id AS T013_D024_Id,
								D037_Unidade
						   FROM T316
					  LEFT JOIN T008 ON T008_Id = T316_T008_Id
					  LEFT JOIN D009 ON D009_Id = T008_D009_Id
					  LEFT JOIN D049 ON D049_Id = D009_D049_Id
					  LEFT JOIN D001 ON D001_Id = D049_D001_Id
					  LEFT JOIN D037 ON D037_Id = D001_D037_Id
					  LEFT JOIN T007 ON T007_Id = T008_T007_Id
						  WHERE T316_Flag_Ativo_Baixado = 'N'
							AND T316_Numero_Parcelas_Apropriacao > 0
							AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
							AND T007_C004_Id = '{$g['empresaAtual']}' 
							AND T007_Flag_Cancelada != 'S' 
							AND T007_Numero_Protocolo_Nfe > 0
							AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
							AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')");
	
	$saldoIcmsJaApropriado = 0;	// Vai ser utilizado no G110 - SALDO_IN_ICMS
	$arrayT316 = array();
	while ($mT316 = mysql_fetch_array($T316)) {

		# REGISTRO G125: ICMS – ATIVO PERMANENTE – CIAP
		/*
			Nº Campo 			Descrição 																														
			01 REG 				Texto fixo contendo "G125" 																										
			02 COD_IND_BEM      Código individualizado do bem ou componente adotado no controle patrimonial do estabelecimento informante
			03 DT_MOV           Data da movimentação ou do saldo inicial                                                                                                  
			04 TIPO_MOV         Tipo de movimentação do bem ou componente:
								SI = Saldo inicial de bens imobilizados;
								IM = Imobilização de bem individual;
								IA = Imobilização em Andamento - Componente;
								CI = Conclusão de Imobilização em Andamento – Bem Resultante;
								MC = Imobilização oriunda do Ativo Circulante;
								BA = Baixa do bem - Fim do período de apropriação;
								AT = Alienação ou Transferência;
								PE = Perecimento, Extravio ou Deterioração;
								OT = Outras Saídas do Imobilizado
			05 VL_IMOB_ICMS_OP  Valor do ICMS da Operação Própria na entrada do bem ou componente
																										
			06 VL_IMOB_ICMS_ST  Valor do ICMS da Oper. por Sub. Tributária na entrada do bem ou componente                                                                                                                  
			07 VL_IMOB_ICMS_FRT Valor do ICMS sobre Frete do Conhecimento de Transporte na entrada do bem ou componente                                                                                   
			08 VL_IMOB_ICMS_DIF Valor do ICMS - Diferencial de Alíquota, conforme Doc.de Arrecadação, na entrada do bem ou componente
			09 NUM_PARC         Número da parcela do ICMS                                                                                                       
			10 VL_PARC_PASS     Valor da parcela de ICMS passível de apropriação (antes da aplicação da participação percentual do valor das saídas tributadas/exportação sobre as saídas totais)
																		
		*/
		$codigoProduto     = (!empty($mT316['T316_Codigo_Produto_CIAP'])) ? $mT316['T316_Codigo_Produto_CIAP'] : $mT316['D001_Codigo_Produto']; 

		$COD_IND_BEM       = $codigoProduto; 
		$DT_MOV 	       = formataData($dataInicial);
		$verificaMovimento = verificaMovimento($mT316['T316_Id'], $mT316['T316_Data_Inicio_Apropriacao_Credito'],$mT316['T316_Numero_Parcelas_Apropriacao']);
		$TIPO_MOV 		   = $verificaMovimento['tipo'];
		$VL_IMOB_ICMS_OP   = $mT316['T014_Valor_ICMS']; 
		$VL_IMOB_ICMS_ST   = 0;
		$VL_IMOB_ICMS_FRT  = 0;
		$VL_IMOB_ICMS_DIF  = 0;
		$NUM_PARC          = $verificaMovimento['parcela'];
		$VL_PARC_PASS      = $VL_IMOB_ICMS_OP / $mT316['T316_Numero_Parcelas_Apropriacao'];

		$lancarBaixa = 'N';
		if($TIPO_MOV == 'BA'){
			/*a baixa de bem ou componente pelo fim de apropriação de crédito deverá ocorrer no período de apropriação da
			última parcela e, neste caso, deverão ser apresentados dois registros: um registro com tipo de movimentação “SI”,
			com os campos NUM_PARC e VL_PARC_PASS preenchidos, representando a apropriação da última parcela, e o
			segundo registro com o tipo de movimentação “BA”, representando a saída do CIAP. Esse 2º registro não poderá ter
			os campos: VL_IMOB_ICMS_OP, VL_IMOB_ICMS_ST, VL_IMOB_ICMS_FRT, VL_IMOB_ICMS_DIF,
			NUM_PARC e VL_PARC_PASS preenchidos;*/
			$TIPO_MOV    = 'SI';
			$lancarBaixa = 'S';
			$arrayT316[] = $mT316['T316_Id'];
		}

		if($TIPO_MOV == 'SI'){
			// Vai ser utilizado no G110 - SALDO_IN_ICMS
			$saldoIcmsJaApropriado += $mT316['T014_Valor_ICMS'];
		}

		$G125 = array(
			$REG = 'G125',
			trim($COD_IND_BEM),
			$DT_MOV,
			$TIPO_MOV,
			number_format($VL_IMOB_ICMS_OP, 2, ',', ''),
			number_format($VL_IMOB_ICMS_ST, 2, ',', ''),
			number_format($VL_IMOB_ICMS_FRT, 2, ',', ''),
			number_format($VL_IMOB_ICMS_DIF, 2, ',', ''),
			$NUM_PARC,
			number_format($VL_PARC_PASS,2,',','')
		);

		$G125 = implode("|", $G125);
		$layoutG .= '|' . $G125 . '|' . _SALTO_LINHA;
		$contador_bloco_G++;

		# REGISTRO G130: IDENTIFICAÇÃO DO DOCUMENTO FISCAL
		/*
			Nº Campo 			Descrição 																														
			01 REG 				Texto fixo contendo "G130" 																										
			02 IND_EMIT         Indicador do emitente do documento fiscal: 0- Emissão própria; 1- Terceiros
			03 COD_PART         Código do participante: - do emitente do documento ou do remetente das mercadorias, no caso de entradas; - do adquirente, no caso de saídas                                                                                                  
			04 COD_MOD          Código do modelo de documento fiscal, conforme tabela 4.1.1
			05 SERIE            Série do documento fiscal																										
			06 NUM_DOC          Número de documento fiscal                                                                                                            
			07 CHV_NFE_CTE      Valor Chave do documento fiscal eletrônico                                                                                   
			08 DT_DOC           Data da emissão do documento fiscal
			09 NUM_DA           Número do documento de arrecadação estadual, se houver*/

		$G130 = array(
			$REG         = 'G130',
			$IND_EMIT    = '1',
			$COD_PART    = $mT316['T013_D024_Id'],
			$COD_MOD     = '55',
			$SERIE       = $mT316['T013_Serie'],
			$NUM_DOC     = $mT316['T013_Numero_Nota_Fiscal'],
			$CHV_NFE_CTE = $mT316['T013_Chave_Acesso_Nfe'],
			$DT_DOC      = formataData(substr($mT316['T013_Data_Emissao'],0,10)),
			$NUM_DA      = ''
		);

		$G130 = implode("|", $G130);
		$layoutG .= '|' . $G130 . '|' . _SALTO_LINHA;
		$contador_bloco_G++;
		verificaRegistro0150($mT316['T013_D024_Id']);


		# REGISTRO G140: IDENTIFICAÇÃO DO DOCUMENTO FISCAL
		/*
			Nº Campo 			    Descrição 																														
			01 REG 				    Texto fixo contendo "G140" 																										
			02 NUM_ITEM             Número sequencial do item no documento fiscal 
			03 COD_ITEM             Código correspondente do bem no documento fiscal (campo 02 do registro 0200)                                                                                                 
			04 QTDE                 Quantidade, deste item da nota fiscal, que foi aplicada neste bem, expressa na mesma unidade constante no documento fiscal de entrada
			05 UNID                 Unidade do item constante no documento fiscal de entrada																										
			06 VL_ICMS_OP_APLICADO  Valor do ICMS da Operação Própria na entrada do item, proporcional à quantidade aplicada no bem ou componente.                                                                                                            
			07 VL_ICMS_ST_APLICADO  Valor do ICMS ST na entrada do item, proporcional à  quantidade aplicada no bem ou componente.                                                                                
			08 VL_ICMS_FRT_APLICADO Valor do ICMS sobre Frete do Conhecimento de Transporte na entrada do item, proporcional à quantidade aplicada no bem ou componente.
			09 VL_ICMS_DIF_APLICADO Valor do ICMS Diferencial de Alíquota, na entrada do item, proporcional à quantidade aplicada no bem ou componente.*/

			$VL_ICMS_OP_APLICADO  = $mT316['T014_Valor_ICMS']; 
			$VL_ICMS_ST_APLICADO  = 0;
			$VL_ICMS_FRT_APLICADO = 0;
			$VL_ICMS_DIF_APLICADO = 0;

			$G140 = array(
				$REG                  = 'G140',
				$NUM_ITEM             = '1',
				$COD_ITEM             = $mT316['D001_Codigo_Produto'],
				$QTDE                 = number_format($mT316['T014_Quantidade'],5,',',''),
				$UNID                 = $mT316['D037_Unidade'],
				number_format($VL_ICMS_OP_APLICADO,2,',', ''),
				number_format($VL_ICMS_ST_APLICADO,2,',', ''),
				number_format($VL_ICMS_FRT_APLICADO,2,',', ''),
				number_format($VL_ICMS_DIF_APLICADO,2,',', '')
			);
	
			$G140 = implode("|", $G140);
			$layoutG .= '|' . $G140 . '|' . _SALTO_LINHA;
			$contador_bloco_G++;
			verificaRegistro0200($mT316['D001_Id']);

		if($lancarBaixa == 'S'){
			$G125 = array(
				$REG = 'G125',
				trim($COD_IND_BEM),
				$DT_MOV,
				'BA',
				'',
				'',
				'',
				'',
				'',
				''
			);
	
			$G125 = implode("|", $G125);
			$layoutG .= '|' . $G125 . '|' . _SALTO_LINHA;
			$contador_bloco_G++;

			$G130 = array(
				$REG         = 'G130',
				$IND_EMIT    = '1',
				$COD_PART    = $mT316['T013_D024_Id'],
				$COD_MOD     = '55',
				$SERIE       = $mT316['T013_Serie'],
				$NUM_DOC     = $mT316['T013_Numero_Nota_Fiscal'],
				$CHV_NFE_CTE = $mT316['T013_Chave_Acesso_Nfe'],
				$DT_DOC      = formataData(substr($mT316['T013_Data_Emissao'],0,10)),
				$NUM_DA      = ''
			);
	
			$G130 = implode("|", $G130);
			$layoutG .= '|' . $G130 . '|' . _SALTO_LINHA;
			$contador_bloco_G++;

			$G140 = array(
				$REG                  = 'G140',
				$NUM_ITEM             = '1',
				$COD_ITEM             = $mT316['D001_Codigo_Produto'],
				$QTDE                 = number_format($mT316['T014_Quantidade'],5,',',''),
				$UNID                 = $mT316['D037_Unidade'],
				number_format($VL_ICMS_OP_APLICADO,2,',', ''),
				number_format($VL_ICMS_ST_APLICADO,2,',', ''),
				number_format($VL_ICMS_FRT_APLICADO,2,',', ''),
				number_format($VL_ICMS_DIF_APLICADO,2,',', '')
			);
	
			$G140 = implode("|", $G140);
			$layoutG .= '|' . $G140 . '|' . _SALTO_LINHA;
			$contador_bloco_G++;
		}			
	}

		# REGISTRO G110: ICMS – ATIVO PERMANENTE – CIAP
	/*
		Nº Campo 			Descrição 																														
		01 REG 				Texto fixo contendo "H010" 																										
		02 DT_INI           Data inicial a que a apuração se refere                                                                                  
		03 DT_FIN           Data final a que a apuração se refere                                                                                                   
		04 SALDO_IN_ICMS    Saldo inicial de ICMS do CIAP, composto por ICMS de bens que entraram anteriormente ao período de apuração (somatório dos campos 05 a 08 dos registros G125)                                                                                                            
		05 SOM_PARC         Somatório das parcelas de ICMS passível de apropriação de cada bem (campo 10 do G125)                                                                                                          
		06 VL_TRIB_EXP      Valor do somatório das saídas tributadas e saídas para exportação                                                                                                                  
		07 VL_TOTAL         Valor total de saídas                                                                                   
		08 IND_PER_SAI      Índice de participação do valor do somatório das saídas tributadas e saídas para exportação no valor total de saídas (Campo 06 dividido pelo campo 07)                                                                            
		09 ICMS_APROP       Valor de ICMS a ser apropriado na apuração do ICMS, correspondente à multiplicação do campo 05 pelo campo 08.                                                                                                       
		10 SOM_ICMS_OC      Valor de outros créditos a ser apropriado na Apuração do ICMS, correspondente ao somatório do campo 09 do registro G126.                                                                          
	*/


	// Inicio ICMS Apropriação - Pega a quantidade do valor do ICMS que tem para apropriação no periodo
    $icmsAtivo = mysql_query("SELECT SUM(totalICMSAtivo) totalICMSAtivo FROM (
									SELECT SUM(T014_Valor_ICMS / T316_Numero_Parcelas_Apropriacao) AS totalICMSAtivo
										FROM T316
									LEFT JOIN T014 ON T014_Id = T316_T014_Id
									LEFT JOIN T013 ON T013_Id = T014_T013_Id
									WHERE T316_Flag_Ativo_Baixado = 'N'
										AND T316_Numero_Parcelas_Apropriacao > 0
										AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
										AND T013_C004_Id = '{$g['empresaAtual']}' 
										AND T013_Flag_Cancelada != 'S' 
										AND T013_Numero_Protocolo_Nfe > 0
										AND T013_Flag_Estoque_Finalizado = 'S'
										AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
										AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
								UNION ALL
									SELECT SUM(T008_Valor_ICMS / T316_Numero_Parcelas_Apropriacao) AS totalICMSAtivo
										FROM T316
									LEFT JOIN T008 ON T008_Id = T316_T008_Id
									LEFT JOIN T007 ON T007_Id = T008_T007_Id
									WHERE T316_Flag_Ativo_Baixado = 'N'
										AND T316_Numero_Parcelas_Apropriacao > 0
										AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
										AND T007_C004_Id = '{$g['empresaAtual']}' 
										AND T007_Flag_Cancelada != 'S' 
										AND T007_Numero_Protocolo_Nfe > 0
										AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
										AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
								) T008");

	$mIcmsAtivo = mysql_fetch_array($icmsAtivo);
	// Fim ICMS Apropriação

	// Inicio Total Saídas Tributadas 
	$saidasTributadas =  mysql_query("SELECT SUM(T007_Valor_Total) AS T007_valor_Total
										FROM T007 
   									   WHERE T007_Data_Emissao >= '{$dataInicial}' 
	 									 AND T007_Data_Emissao <= '{$dataFinal}' 
	 									 AND T007_C004_Id = '{$g['empresaAtual']}' 
	 									 AND T007_Numero_Protocolo_Nfe > 0                                    
					   					 AND T007_Flag_ACP IN('6', '8') 
										 AND T007_Flag_Entrada_Saida = 'S'
										 AND T007_Valor_Total_ICMS > 0
					   					 AND NOT (T007_Flag_Cancelada = 'S' AND T007_Flag_ACP = '8')");
	$mSaidasTributadas = mysql_fetch_array($saidasTributadas);
	// FIM Total Saídas Tributadas 
	
	// Inicio Total Saídas  
	$totalSaidas =  mysql_query("SELECT SUM(T007_Valor_Total) AS T007_valor_Total
								   FROM T007 
   								  WHERE T007_Data_Emissao >= '{$dataInicial}' 
	 							    AND T007_Data_Emissao <= '{$dataFinal}' 
	 								AND T007_C004_Id = '{$g['empresaAtual']}' 
	 								AND T007_Numero_Protocolo_Nfe > 0                                    
					   				AND T007_Flag_ACP IN('6', '8') 
									AND T007_Flag_Entrada_Saida = 'S'
					   				AND NOT (T007_Flag_Cancelada = 'S' AND T007_Flag_ACP = '8')");
	$mTotalSaidas = mysql_fetch_array($totalSaidas);
	// FIM Total Saídas  

	$IND_PER_SAI = $mSaidasTributadas['T007_valor_Total'] / $mTotalSaidas['T007_valor_Total'];
	$ICMS_APROP = $mIcmsAtivo['totalICMSAtivo'] * $IND_PER_SAI;
	$G110 = array(
		$REG 		   = 'G110',
		$DT_INI 	   = formataData($dataInicial),
		$DT_FIN        = formataData($dataFinal),
		$SALDO_IN_ICMS = number_format($saldoIcmsJaApropriado, 2, ',', ''),
		$SOM_PARC      = number_format($mIcmsAtivo['totalICMSAtivo'], 2, ',', ''),
		$VL_TRIB_EXP   = number_format($mSaidasTributadas['T007_valor_Total'], 2, ',', ''),
		$VL_TOTAL      = number_format($mTotalSaidas['T007_valor_Total'], 2, ',', ''),
		number_format($IND_PER_SAI,8,',',''),
		number_format($ICMS_APROP, 2, ',', ''),
		$SOM_ICMS_OC = '0,00'
	);

	$G110 = implode("|", $G110);
	$layout .= '|' . $G110 . '|' . _SALTO_LINHA;
	$contador_bloco_G++;

	// Juntando com o layout acima G125, G130, G140, tem valores do G125 que precisa constar no G110
	$layout .= $layoutG;
}

# REGISTRO G990 - ENCERRAMENTO DO BLOCO H
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec     Obrig.
	01 REG 				Texto fixo contendo "G990" 																										C 		004		- 	 	O
	02 QTD_LIN_H        Quantidade total de linhas do Bloco H                   																		N       -       -		O																                            
*/

$G990 = array(
		$REG = 'G990',
		$QTD_LIN_G = $contador_bloco_G
);

$G990 = implode("|", $G990);
$layout .= '|' . $G990 . '|' . _SALTO_LINHA;

$linhas_bloco_G = $QTD_LIN_G;

function verificaMovimento($T316_Id, $T316_Data_Inicio_Apropriacao_Credito, $T316_Numero_Parcelas_Apropriacao){

	/* Tipo de movimentação do bem ou componente:
	   SI = Saldo inicial de bens imobilizados;
	   IM = Imobilização de bem individual;
	   IA = Imobilização em Andamento - Componente;
	   CI = Conclusão de Imobilização em Andamento – Bem Resultante;
	   MC = Imobilização oriunda do Ativo Circulante;
	   BA = Baixa do bem - Fim do período de apropriação;
	   AT = Alienação ou Transferência;
	   PE = Perecimento, Extravio ou Deterioração;
	   OT = Outras Saídas do Imobilizado*/

	// Data do banco de dados
	$dataInicio = new \DateTime($T316_Data_Inicio_Apropriacao_Credito);

	// Número de parcelas
	$numeroParcelas = $T316_Numero_Parcelas_Apropriacao;

	// Data atual
	$dataAtual = new \DateTime();

	// Calcular a diferença em meses
	$parcelaAtual = (($dataAtual->format('Y') - $dataInicio->format('Y')) * 12) + $dataAtual->format('m') - $dataInicio->format('m');
	if($parcelaAtual == 1){
		return array('tipo' => 'SI', 'parcela' => $parcelaAtual);
	} else if($parcelaAtual >= $numeroParcelas){
		return array('tipo' => 'BA', 'parcela' => $parcelaAtual);
	} else{
		return array('tipo' => 'IM', 'parcela' => $parcelaAtual);
	}
}




