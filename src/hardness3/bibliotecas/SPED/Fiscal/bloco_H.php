<?php

namespace hardness;

global $g;

/*
BLOCO H: INVENTÁRIO FÍSICO

Este bloco destina-se a informar o inventário físico do estabelecimento, nos casos e prazos previstos na legislação
pertinente.
Para que o Bloco H seja utilizado como Registro de Inventário para efeito de imposto de renda o contribuinte deve:
a) acrescentar os bens cujo inventário não é exigido para fins do IPI/ICMS, mas apenas pela legislação do Imposto
de Renda (bens em almoxarifado);
b) acrescentar o valor unitário dos bens, de acordo com os critérios exigidos pela legislação do Imposto de Renda,
quando discrepante dos critérios previstos na legislação do IPI/ICMS, conduzindo-se ao valor contábil dos estoques. Esse
acréscimo é autorizado pelo Convênio Sinief/1970, art. 63, § 12, como "Outras indicações" e será informado no campo 11 -
VL_ITEM_IR do registro H010 - Inventário.
As pessoas jurídicas do segmento de construção civil dispensadas de apresentar a Escrituração Fiscal Digital (EFD)
pelos estados e obrigadas a escriturar o livro Registro de Inventário devem apresentá-lo na Escrituração Contábil Digital,
como um livro auxiliar, conforme art. 3º, parágrafo 5º, da IN RFB 1420/2013, com a nova redação da IN RFB 1486/2014.

REG	   OCOR    DESCRIÇÃO																	
H001	1	   Este registro deve ser gerado para abertura do bloco H, indicando se há registros de informações no bloco. Obrigatoriamente deverá ser informado “0” no campo IND_MOV no período de referência fevereiro de cada ano.
H005	N	   Este registro deve ser apresentado para discriminar os valores totais dos itens/produtos do inventário realizado em 31 de dezembro de cada exercício, ou nas demais datas estabelecidas pela legislação fiscal ou comercial.
H010	1:N	   Este registro deve ser informado para discriminar os itens existentes no estoque. Este registro não pode ser fornecido se o campo 03 (VL_INV) do registro H005 for igual a “0” (zero). 
H020	1:N	   Este registro deve ser preenchido para complementar as informações do inventário, quando o campo MOT_INV do registro H005 for de “02” a “05”. Não informar se o campo 03 (VL_INV) do registro H005 for igual a “0” (zero).
H030    1:N    Este registro é obrigatório quando o campo MOT_INV do registro H005 for igual “06”, conforme legislação definida pela UF de domicílio do contribuinte. Para os demais motivos, não deve ser informado.
H990	1	   Encerramento do bloco 	
*/

require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();

$contador_bloco_H = 1;
$IND_MOV = $gerarBlocoH == 'S' ? '0' : '1';

# REGISTRO H001 - ABERTURA DO BLOCO H
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "H001" 																										C 		004		- 		O
	02 DT_EST 			Indicador de movimento:                                                                                                         C 		001		- 		O
                        0- Bloco com dados informados
                        1- Bloco sem dados informados 																		                            
*/

#REGISTRO H001 - INICIADO COM AS VARIAVEIS AQUI, POREM MONTADO MAIS PRA BAIXO \/ \/
$H001 = array(
	$REG = 'H001',
	$IND_MOV
);
$H001 = implode("|", $H001);
$layout .= '|' . $H001 . '|' . _SALTO_LINHA;
$contador_bloco_H++;

if($gerarBlocoH == 'S') {
	# REGISTRO H010 - INVENTÁRIO
	/*
		Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
		01 REG 				Texto fixo contendo "H010" 																										C 		004		- 		O
		02 COD_ITEM         Código do item (campo 02 do Registro 0200)                                                                                      C       060     -		O
		03 UNID             Unidade do item                                                                                                                 C       006     -		O
		04 QTD              Quantidade do item                                                                                                              N       -       03		O
		05 VL_UNIT          Valor unitário do item                                                                                                          N       -       06		O
		06 VL_ITEM          Valor do item                                                                                                                   N       -       02		O
		07 IND_PROP         Indicador de propriedade/posse do item:                                                                                         C       001*    -		O
							0- Item de propriedade do informante e em seu poder
							1- Item de propriedade do informante em posse de terceiros
							2- Item de propriedade de terceiros em posse do informante
		08 COD_PART         Código do participante (campo 02 do Registro 0150):                                                                             C       060     -		OC
							- proprietário/possuidor que não seja o informante do arquivo 	
		09 TXT_COMPL        Descrição complementar                                                                                                          C       -       -		OC
		10 COD_CTA          Código da conta analítica contábil debitada/creditada                                                                           C       -       -		OC
		11 VL_ITEM_IR       Valor do item pare efeitos do Imposto de Renda                                                                                  N       -       02		OC
	*/

	//Tipo custo não é informado

	// Não vai mais ser uma subquery e vai ser executado no while, estava muito pesado 
	//$Saldo = "(SELECT T001_Quantidade_Saldo from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento<='$dataInventarioBlocoH' ORDER BY T001_Data_Lancamento DESC,T001_Id DESC LIMIT 1) AS Saldo";
	//$Custo = "(SELECT T001_Valor_Custo_Medio_Unitario from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento<='$dataInventarioBlocoH' ORDER BY T001_Data_Lancamento DESC,T001_Id DESC LIMIT 1) AS Custo";
	//if($tipoCusto == '3'){
	//	$Custo = "D009_Valor_Preco_Tabela as Custo";
	//}

	$retorno = $CAD002->inventarioBloco_H($dataInventarioBlocoH);

	if($retorno === true){
		$sqlD001 = mysql_query("SELECT D001_Codigo_Produto,
								   D001_Flag_Tipo_Item,
								   D037_Unidade,
								   ifnull(D004_Indicador_Propriedade, '0') as D004_Indicador_Propriedade,
								   D187_Codigo_Plano_Conta_Contabil,
								   D009_Id,
								   D001_Id,
								   D009_Valor_Preco_Tabela
							  from D001  
						  left join D049 on D049_D001_Id=D001_Id 
						  left join D009 on D009_D049_Id=D049_Id 
						  left join D037 on D001_D037_Id=D037_Id
						  left join T066 on T066_D009_Id=D009_Id
						  left join D004 on D004_Id=T066_D004_Id
						  left join D001A on D001A_D001_Id=D001_Id
						  left join D187 on D187_Id=D001A_D187_Id
						   	  where D009_C004_Id = '{$g['empresaAtual']}'
							  		{$D001_C004_Id}
							    and ifnull(D004_Indicador_Propriedade, '0') = '0'
								and D001_Flag_Tipo_Item != ''								
						   group by D009_Id");
	}else{
		$sqlD001 = mysql_query($retorno);
	}

	$CustoTotal = 0;
	$SaldoTotal = 0;
	while ($mD009 = mysql_fetch_array($sqlD001)) {
		if ((trim($mD009['D001_Flag_Tipo_Item']) >= 00 && trim($mD009['D001_Flag_Tipo_Item']) <= 06) || trim($mD009['D001_Flag_Tipo_Item']) == 10) {
		
			if($retorno === true){
				$saldoCusto = mysql_query("SELECT T001_Quantidade_Saldo AS Saldo,
												T001_Valor_Custo_Medio_Unitario AS Custo
											FROM T001
											WHERE T001_D009_Id = '{$mD009['D009_Id']}' 
											AND T001_Data_Lancamento <= '{$dataInventarioBlocoH}'
										ORDER BY T001_Data_Lancamento DESC, T001_Id DESC 
											LIMIT 1");

				$mSaldoCusto = mysql_fetch_array($saldoCusto);

				$mD009['Saldo'] = $mSaldoCusto['Saldo'];
				$mD009['Custo'] = $mSaldoCusto['Custo'];
			}else{
				$mD009['Saldo'] = $mD009['SMT114_Saldo'];
				$mD009['Custo'] = $mD009['SMT114_Custo_Medio'];
			}

			if($tipoCusto == '3'){
				$mD009['Custo'] = $mD009['D009_Valor_Preco_Tabela'];
			}

			if (empty($mD009['Saldo']) || $mD009['Saldo'] == "NULL" || $mD009['Saldo'] <= "0") {
				continue;
			} 

			$CustoTotal = $mD009['Saldo'] * $mD009['Custo'];
			$SaldoTotal += $CustoTotal;

			if($retorno === true){
				$H010 = array(
					$REG = 'H010',
					$COD_ITEM = trim($mD009['D001_Codigo_Produto']),
					$UND = $mD009['D037_Unidade'],
					$QTD = number_format($mD009['Saldo'], 3, ',', ''),
					$VL_UNIT = number_format($mD009['Custo'], 6, ',', ''),
					$VL_ITEM = number_format($CustoTotal, 2, ',', ''),
					$IND_PROP = $mD009['D004_Indicador_Propriedade'],
					$COD_PART = '',
					$TXT_COMPL = '',
					$COD_CTA = $mD009['D187_Codigo_Plano_Conta_Contabil'],
					$VL_ITEM_IR = number_format($CustoTotal, 2, ',', '')
				);
			}else{
				$H010 = array(
					$REG = 'H010',
					$COD_ITEM = trim($mD009['D001_Codigo_Produto']),
					$UND = $mD009['D037_Unidade'],
					$QTD = number_format($mD009['Saldo'], 3, ',', ''),
					$VL_UNIT = number_format($mD009['Custo'], 6, ',', ''),
					$VL_ITEM = number_format($CustoTotal, 2, ',', ''),
					$IND_PROP = $mD009['SMT114_Indicador'],
					$COD_PART = $mD009['SMT114_D024_Id'],
					$TXT_COMPL = '',
					$COD_CTA = $mD009['SMT114_Conta_Contabil'],
					$VL_ITEM_IR = number_format($CustoTotal, 2, ',', '')
				);
			}

			$H010 = implode("|", $H010);
			$layoutH10 .= '|' . $H010 . '|' . _SALTO_LINHA;
			$contador_bloco_H++;
			verificaRegistro0200($mD009['D001_Id']);
			if(!empty($COD_PART)){
				verificaRegistro0150($COD_PART);		
			}
		}
	}

	if($retorno === true){
		$T001 = mysql_query("SELECT
								D001_Codigo_Produto,	
								D001_Flag_Tipo_Item,
								if(T001_Flag_Operacao = 'E' and T001_T008_Id is null, Forn.D024_Id, IF(D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id)) as Participante,
								D004_Indicador_Propriedade,    
								(case 
									when D004_Indicador_Propriedade = '2' OR D004_Flag_Fulfillment = 'S'
										then sum(T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1))
										else sum( T001_Quantidade * if(T001_Flag_Operacao = 'S', 1, -1))
								end) as `Saldo`,
								(select T001_Valor_Custo_Medio_Unitario 
									from T001 
									where T001_D009_Id=D009_Id 
										and T001_Data_Lancamento<= '$dataInventarioBlocoH'
									order by T001_Data_Lancamento desc,T001_Id desc 
									limit 1
								) as `Custo`, 
								D037_Unidade,
								D187_Codigo_Plano_Conta_Contabil,
								D001_Id                          
							from T001
							left join T013 on T013_Id = T001_T013_Id
							left join D024 as Forn on Forn.D024_Id = ifnull(T013_D024_Id_Participante, T001_D024_Id)
							left join D024 as Cli on Cli.D024_Id = T001_D024_Id_Cliente
							left join D009 on D009_Id=T001_D009_Id
							left join D049 on D049_Id=D009_D049_Id
							left join D001 on D001_Id=D049_D001_Id  
							left join D001A on D001A_D001_Id=D001_Id
							left join D037 on D037_Id=D001_D037_Id
							left join T066 on T066_Id=T001_T066_Id
							left join D004 on D004_Id=T066_D004_Id
							left join D187 on D187_Id=D001A_D187_Id
							where D009_C004_Id = '{$g['empresaAtual']}'
								{$D001_C004_Id}
							and (D004_Indicador_Propriedade = '1'  or D004_Indicador_Propriedade = '2')
							and (T001_Flag_Operacao = 'S'or T001_Flag_Operacao = 'E')      
							and T001_Data_Lancamento <= '$dataInventarioBlocoH'							    							
							group by D009_Id, if(T001_Flag_Operacao = 'E' and T001_T008_Id is null, Forn.D024_Id, IF(D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id))					
							order by D004_Indicador_Propriedade desc, D001_Codigo_Produto");

		while ($mT001 = mysql_fetch_Array($T001)) {
			if ((trim($mT001['D001_Flag_Tipo_Item']) >= 00 && trim($mT001['D001_Flag_Tipo_Item']) <= 06) || trim($mT001['D001_Flag_Tipo_Item']) == 10) {
				if (empty($mT001['Saldo']) || $mT001['Saldo'] == "NULL" || $mT001['Saldo'] <= "0") {
					continue;
				}

				$CustoTotal = $mT001['Saldo'] * $mT001['Custo'];
				$SaldoTotal += $CustoTotal;
				$H010 = array(
					$REG = 'H010',
					$COD_ITEM = trim($mT001['D001_Codigo_Produto']),
					$UND = $mT001['D037_Unidade'],
					$QTD = number_format($mT001['Saldo'], 3, ',', ''),
					$VL_UNIT = number_format($mT001['Custo'], 6, ',', ''),
					$VL_ITEM = number_format($CustoTotal, 2, ',', ''),
					$IND_PROP = $mT001['D004_Indicador_Propriedade'],
					$COD_PART = $mT001['Participante'],
					$TXT_COMPL = '',
					$COD_CTA = $mT001['D187_Codigo_Plano_Conta_Contabil'],
					$VL_ITEM_IR = number_format($CustoTotal, 2, ',', '')
				);

				verificaRegistro0200($mT001['D001_Id']);
				if(!empty($COD_PART)){
					verificaRegistro0150($COD_PART);		
				}

				$H010 = implode("|", $H010);
				$layoutH10 .= '|' . $H010 . '|' . _SALTO_LINHA;
				$contador_bloco_H++;
			}
			
		}
	}


	#REGISTRO H005 - TOTAIS DO INVENTÁRIO - MONTADO LAYOUT H005 COM BASE NO SALDO TOTAL DO H010

	/*
		Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
		01 REG 				Texto fixo contendo "H005" 																										C 		4 		- 		O
		02 DT_INV 			Data do inventario										 																		N 		8 		- 		O
		03 VL_INV 			valor total do estoque					 																						N 		- 		02 		O
		04 MOT_INV	 		Informe o motivo do Inventário:
							01 - No final no período
							02 - Na mudança de forma de tributação da mercadoria (ICMS)
							03 - Na solicitação da baixa cadastral, paralisação temporária e outras situações
							04 - Na alteração de regime de pagamento - condição do contribuinte
							05 - Por determinação dos fiscos
							06 – Para controle das mercadorias sujeitas ao regime de substituição tributária –
							restituição/ ressarcimento/ complementação 																						c 		2 		-	 	O
		
	*/

	$H005 = array(
		$REG = 'H005',
		$DT_INV = formataData($dataInventarioBlocoH),
		$VL_INV = number_format($SaldoTotal, 2, ',', ''),
		$MOT_INV = '01'
	);

	$H005 = implode("|", $H005);
	$layout .= '|' . $H005 . '|' . _SALTO_LINHA;
	$contador_bloco_H++;

	$layout .= $layoutH10;  // LAYOUT H010
}

# REGISTRO H020 -   INFORMAÇÃO COMPLEMENTAR DO INVENTÁRIO
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "H020" 																										C 		4 		- 		O
	02 CST_ICMS 		Código da Situação Tributária referente ao ICMS, conforme a Tabela indicada no item 4.3.1 										N 		3 		- 		O
	03 BC_ICMS 			Informe a base de cálculo do ICMS																								N 		- 		2 		O
	04 VL_ICMS	 		Informe o valor do ICMS a ser debitado ou creditado 																			N 		 		2	 	O
	
*/


# REGISTRO H030 - OUTRAS MOVIMENTAÇÕES INTERNAS DE ENTRE MERCADORIAS - DEIXAR POR ENQUANTO
/*	
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K220" 																										C 		4 		- 		O
	02 DT_MOV 			Data da movimentação interna 																									N 		8 		- 		O
	03 COD_ITEM_ORI 	Código do item de origem (campo 02 do Registro 0200)																			C 		60 		- 		O
	04 COD_ITEM_DEST 	Código do item de destino (campo 02 do Registro 0200) 																			C 		60 		-	 	O
	05 QTD 				Quantidade movimentada 																											N 		- 		3 		O
*/


# REGISTRO H990 - ENCERRAMENTO DO BLOCO H
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec     Obrig.
	01 REG 				Texto fixo contendo "H990" 																										C 		004		- 	 	O
	02 QTD_LIN_H        Quantidade total de linhas do Bloco H                   																		N       -       -		O																                            
*/

$H990 = array(
		$REG = 'H990',
		$QTD_LIN_K = $contador_bloco_H
);

$H990 = implode("|", $H990);
$layout .= '|' . $H990 . '|' . _SALTO_LINHA;



$linhas_bloco_0 = $contador_bloco_H;
