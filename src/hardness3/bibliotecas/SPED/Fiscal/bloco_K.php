<?php
namespace hardness;
global $g;

/*
# BLOCO K (ZERO) # INFORMAÇÕES PRINCIPAIS #

Este bloco se destina a prestar informações mensais da produção e respectivo consumo de insumos, bem como do
estoque escriturado, relativos aos estabelecimentos industriais ou a eles equiparados pela legislação federal e pelos
atacadistas, podendo, a critério do Fisco, ser exigido de estabelecimento de contribuintes de outros setores (conforme § 4º
do art. 63 do Convênio s/número, de 1970). O bloco K entrará em vigor na EFD a partir de 2016.
Os contribuintes optantes pelo Simples Nacional estão dispensados de apresentarem este bloco, em virtude da
Resolução Comitê Gestor do Simples Nacional nº 94, de 29 de novembro de 2011, e alterações posteriores
(http://www.receita.fazenda.gov.br/Legislacao/Resolucao/2011/CGSN/Resol94.htm), que lista os livros
obrigatórios do Regime Especial Unificado de Arrecadação de Tributos e Contribuições devidos pelas Microempresas e
Empresas de Pequeno Porte - Simples Nacional.

REG	   OCOR    DESCRIÇÃO																	
K001	1	   Este registro deve ser gerado para abertura do bloco K, indicando se há registros de informações no bloco
K100	N	   Período de apuração do ICMS/IPI
K200	1:N	   Estoque escriturado - Informa o estoque final escriturado do período de apuração
K220	1:N	   Outras movimentações internas entre mercadorias. - Este registro tem o objetivo de informar a movimentação interna entre mercadorias de tipos: 00 – Mercadoria para revenda; 01 – Matéria-Prima; 02 – Embalagem; 03 – Produtos emProcesso; 04 – Produto Acabado; 05 – Subproduto e 10 – Outros Insumos
K230    1:N    Informar a produção acabada de produto em processo (tipo 03 – campo TIPO_ITEM do registro 0200) e produto acabado (tipo 04 – campo TIPO_ITEM do registro 0200), inclusive daquele industrializado para terceiro por encomenda. 
K235	1:N	   Informar o consumo de mercadoria no processo produtivo, vinculado ao produto resultante informado no campo COD_ITEM do Registro K230 – Itens Produzidos. 
K250	1:N	   INDUSTRIALIZAÇÃO EFETUADA POR TERCEIROS – ITENS PRODUZIDOS - Este registro tem o objetivo de informar os produtos que foram industrializados por terceiros por encomenda e sua quantidade
K255	1:N	   INDUSTRIALIZAÇÃO EM TERCEIROS – INSUMOS CONSUMIDOS
K990	1	   Encerramento do bloco
*/

require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();

$contador_bloco_K = 1;

$IND_MOV = '0';

# REGISTRO K001
$K001 = array(																#TAM
	$REG = 'K001',															#004* 	
	$IND_MOV																#001 	Indicador de movimento (0 Com dados / 1 Sem dados)
);
$K001 = implode("|", $K001);
$layout .= '|' . $K001 . '|' . _SALTO_LINHA;
$contador_bloco_K++;

#REGISTRO K010 INFORMAÇÃO SOBRE O TIPO DE LEIAUTE (SIMPLIFICADO /COMPLETO)
$K010 = array(
	$REG = 'K010',
	$IND_TP_LEIAUTE = '0'
);

$K010 = implode("|", $K010);
$layout .= '|' . $K010 . '|' . _SALTO_LINHA;
$contador_bloco_K++;

#REGISTRO K100 Período de apuração
$K100 = array(
	$REG = 'K100',
	$DT_INI = formataData($dataInicial),
	$DT_FIN = formataData($dataFinal)
);

$K100 = implode("|", $K100);
$layout .= '|' . $K100 . '|' . _SALTO_LINHA;
$contador_bloco_K++;

$retorno = $CAD002->inventario($dataInventario);

#REGISTRO K200 - Estoque escriturado
if($gerarK200 === 'S') {
	# No bloco H é utilizado sempre o último saldo da movimentação
	if($retorno === true){
		$sqlD001 = mysql_query("SELECT D001_Codigo_Produto,
									D001_Flag_Tipo_Item,  					
									D009_Id,
									D001_Id
								FROM D001
							LEFT JOIN D049 ON D049_D001_Id=D001_Id
							LEFT JOIN D009 ON D009_D049_Id=D049_Id
							LEFT JOIN D037 ON D001_D037_Id=D037_Id
							LEFT JOIN T066 ON T066_D009_Id=D009_Id
							LEFT JOIN D004 ON D004_Id = T066_D004_Id							
								WHERE D009_C004_Id ='{$g['empresaAtual']}' 
									{$D001_C004_Id}
								AND IFNULL(D004_Indicador_Propriedade, '0') = '0'
								AND D001_Flag_Tipo_Item != ''
								AND ((D001_Flag_Tipo_Item >= '00' AND D001_Flag_Tipo_Item <= '06') OR D001_Flag_Tipo_Item = '10')
							GROUP BY D009_Id");
	}else{
		$sqlD001 = mysql_query($retorno);
	}
							
		while($mD009 = mysql_fetch_array($sqlD001)){
		
			if($retorno === true){
				// Quando nao utiliza inventario alternativo, o saldo vem do ultimo movimento.
				$saldo = mysql_query("SELECT T001_Quantidade_Saldo
								FROM T001
							   WHERE T001_D009_Id = '{$mD009['D009_Id']}'	 
								 AND T001_Data_Lancamento <= '{$dataInventario}'  								
							ORDER BY T001_Data_Lancamento DESC, T001_Id DESC LIMIT 1");
				$mSaldo = mysql_fetch_array($saldo);

				$mD009['Saldo'] = $mSaldo['T001_Quantidade_Saldo'];
				$indEst         = '0';
				$codPart        = '';
			}else{
				$mD009['Saldo'] = $mD009['SMT114_Saldo'];
				$indEst         = $mD009['SMT114_Indicador'];
				$codPart        = $mD009['SMT114_D024_Id']; 
			}

		if((float)$mD009['Saldo'] > 0) {
			
			$K200 = array(
				$REG      = 'K200',									#4 	    Texto fixo contendo "K200"
				$DT_EST   = formataData($dataFinal),				#8 		Data estoque final
				$COD_ITEM = trim($mD009['D001_Codigo_Produto']),	#60 	Código do item (campo 02 do Registro 0200)
				$QTD      = formataDecimalBlocoK($mD009['Saldo']),	#N 		Quantidade em estoque		
				$IND_EST  = $indEst,								#1 		Indicador de estoque (0 = Estoque de propriedade de informante e em seu poder. 1 = Estoque de propriedade do informante e em posso de terceiros. 2 = Estoque de prorpiedade de terceiros e em posso do informante)
				$COD_PART = $codPart								#60		Código de identificação do participante no arquivo. - no bloco_0, registro 150 foi informado ao campo T007_D024_Id.
			);	
			
			$K200 = implode("|", $K200);
			$layout .= '|' . $K200 . '|' . _SALTO_LINHA;
			$contador_bloco_K++;
			verificaRegistro0200($mD009['D001_Id']);
		}								
		continue;		
	}			
	
	/* Usa de funções de janela (PARTITION, LAG etc) que foram lançadas no MySQL 8.0 e alguns
	clientes usam versões anteriores. Além disso, o uso dessas funções para corrigir o problema
	do balanço não está funcionando adequadamente segundo Gabriel Feo */

	// $T001 = mysql_query("SELECT	
	// 						D001_Id,
	// 						D001_Codigo_Produto,
	// 						Participante,
	// 						D004_Indicador_Propriedade,    	
	// 						(CASE 
	// 							WHEN D004_Indicador_Propriedade = '2' OR D004_Flag_Fulfillment = 'S'
	// 							THEN SUM(T001_Quantidade * IF(T001_Flag_Operacao = 'S', -1, 1))
	// 							ELSE SUM( T001_Quantidade * IF(T001_Flag_Operacao = 'S', 1, -1))
	// 						END) AS `Saldo`                                 
	// 					FROM  (														
	// 						SELECT
	// 							D001_Id,
	// 							D001_Codigo_Produto,    
	// 							IF( T001_Flag_Operacao = 'E' OR T001_Flag_Operacao = 'B' AND T001_T008_Id IS NULL, Forn.D024_Id, IF (D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id)) AS Participante,
	// 							D004_Indicador_Propriedade,
	// 							D004_Flag_Fulfillment,
	// 							D009_Id,
	// 							CASE 
	// 								WHEN T001_Flag_Operacao = 'B' THEN
	// 								CASE
	// 								WHEN T001_Quantidade > LAG(T001_Quantidade) 
	// 									OVER (PARTITION BY IF( T001_Flag_Operacao = 'E' OR T001_Flag_Operacao = 'B' AND T001_T008_Id IS NULL, Forn.D024_Id, IF (D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id)) ORDER BY T001_Id) 
	// 										THEN 'E'
	// 										ELSE 'S'
	// 								END
	// 								ELSE T001_Flag_Operacao
	// 							END AS T001_Flag_Operacao,
	// 							CASE 
	// 								WHEN T001_Flag_Operacao = 'B'
	// 									THEN ABS(T001_Quantidade - LAG(T001_Quantidade) 
	// 										OVER (PARTITION BY IF( T001_Flag_Operacao = 'E' OR  T001_Flag_Operacao = 'B' AND T001_T008_Id IS NULL, Forn.D024_Id, IF (D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id)) ORDER BY T001_Id))
	// 									ELSE T001_Quantidade
	// 							END AS T001_Quantidade
	// 						FROM T001
	// 							LEFT JOIN T013 ON T013_Id = T001_T013_Id
	// 							LEFT JOIN D024 AS Forn ON Forn.D024_Id = ifnull(T013_D024_Id_Participante, T001_D024_Id)
	// 							LEFT JOIN D024 AS Cli ON Cli.D024_Id = T001_D024_Id_Cliente
	// 							LEFT JOIN D009 ON D009_Id=T001_D009_Id
	// 							LEFT JOIN D049 ON D049_Id=D009_D049_Id
	// 							LEFT JOIN D001 ON D001_Id=D049_D001_Id  
	// 							LEFT JOIN T066 ON T066_Id=T001_T066_Id
	// 							LEFT JOIN D004 ON D004_Id=T066_D004_Id
	// 						WHERE D009_C004_Id = '{$g['empresaAtual']}'
	// 						AND (D004_Indicador_Propriedade = '1'  OR D004_Indicador_Propriedade = '2')
	// 							AND ((D001_Flag_Tipo_Item >= '00' AND D001_Flag_Tipo_Item <= '06') OR D001_Flag_Tipo_Item = '10')
	// 							AND T001_Flag_Operacao IN ('S','E','B')
	// 							AND T001_Data_Lancamento <= '{$dataInventario}'
	// 					) AS Inventario
	// 					GROUP BY D009_Id, Participante
	// 					HAVING SUM(CASE 
	// 								WHEN D004_Indicador_Propriedade = '2' OR D004_Flag_Fulfillment = 'S'
	// 									THEN T001_Quantidade * IF(T001_Flag_Operacao = 'S', -1, 1)
	// 									ELSE T001_Quantidade * IF(T001_Flag_Operacao = 'S', 1, -1)
	// 								END) > 0
	// 					ORDER BY D004_Indicador_Propriedade DESC, D001_Codigo_Produto");
	if($retorno === true){
		$T001 = mysql_query("SELECT
								D001_Id,
								D001_Codigo_Produto,	
								IF( T001_Flag_Operacao = 'E' AND T001_T008_Id IS NULL, Forn.D024_Id, IF (D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id)) as Participante,
								D004_Indicador_Propriedade,    
								(case 
									when D004_Indicador_Propriedade = '2' OR D004_Flag_Fulfillment = 'S'
										then sum(T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1))
										else sum( T001_Quantidade * if(T001_Flag_Operacao = 'S', 1, -1))
									end) as `Saldo`                            
							from T001
								left join T013 on T013_Id = T001_T013_Id
								left join D024 as Forn on Forn.D024_Id = ifnull(T013_D024_Id_Participante, T001_D024_Id)
								left join D024 as Cli on Cli.D024_Id = T001_D024_Id_Cliente
								left join D009 on D009_Id=T001_D009_Id
								left join D049 on D049_Id=D009_D049_Id
								left join D001 on D001_Id=D049_D001_Id  
								left join T066 on T066_Id=T001_T066_Id
								left join D004 on D004_Id=T066_D004_Id
							where   D009_C004_Id = '{$g['empresaAtual']}'
									{$D001_C004_Id}
								and (D004_Indicador_Propriedade = '1'  or D004_Indicador_Propriedade = '2')
								and ((D001_Flag_Tipo_Item >= '00' and D001_Flag_Tipo_Item <= '06') or D001_Flag_Tipo_Item = '10')
								and (T001_Flag_Operacao = 'S'or T001_Flag_Operacao = 'E')      
								and T001_Data_Lancamento <= '$dataInventario'							    							
							group by D009_Id, IF( T001_Flag_Operacao = 'E' AND T001_T008_Id IS NULL, Forn.D024_Id, IF (D004_Indicador_Propriedade = 1 AND D004_Flag_Fulfillment = 'S', Forn.D024_Id, Cli.D024_Id))
							having sum(case 
										when D004_Indicador_Propriedade = '2' OR D004_Flag_Fulfillment = 'S'
											then T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1)
											else T001_Quantidade * if(T001_Flag_Operacao = 'S', 1, -1)
										end) > 0
							order by D004_Indicador_Propriedade desc, D001_Codigo_Produto");

		while($mT001 = mysql_fetch_Array($T001)) {			
				$K200 = array(
					$REG = 'K200',												#4 	    Texto fixo contendo "K200"
					$DT_EST = formataData($dataFinal),							#8 		Data estoque final
					$COD_ITEM = trim($mT001['D001_Codigo_Produto']),			#60 	Código do item (campo 02 do Registro 0200)
					$QTD = formataDecimalBlocoK($mT001['Saldo']),				#N 		Quantidade em estoque		
					$IND_EST = $mT001['D004_Indicador_Propriedade'],			#1 		Indicador de estoque (0 = Estoque de propriedade de informante e em seu poder. 1 = Estoque de propriedade do informante e em posso de terceiros. 2 = Estoque de prorpiedade de terceiros e em posso do informante)
					$COD_PART = $mT001['Participante'] 							#60		Código de identificação do participante no arquivo. - no bloco_0, registro 150 foi informado ao campo T007_D024_Id.
				);					
				
				verificaRegistro0200($mT001['D001_Id']);			
				$K200 = implode("|", $K200);
				$layout .= '|' . $K200 . '|' . _SALTO_LINHA;
				$contador_bloco_K++;			
		}
	}
}

# REGISTRO K280 -  CORREÇÃO DE APONTAMENTO – ESTOQUE ESCRITURADO
/*
	Nº Campo 			Descrição 																														Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K280" 																										C 		4 		- 		O
	02 DT_EST 			Data do estoque final escriturado que está sendo corrigido 																		N 		8 		- 		O
	03 COD_ITEM 		Código do item (campo 02 do Registro 0200) 																						C 		60 		- 		O
	04 QTD_COR_POS 		Quantidade de correção positiva de apontamento ocorrido em período de apuração anterior 										N 		- 		3	 	OC
	05 QTD_COR_NEG 		Quantidade de correção negativa de apontamento ocorrido em período de apuração anterior 										N 		- 		3		OC
	06 IND_EST 			Indicador do tipo de estoque 																									C 		1 		- 		O
	07 COD_PART 		Código do participante (campo 02 do Registro 0150): proprietário/possuidor que não seja o informante do arquivo 				C 		60 		- 		OC
*/


//--------------------------------------------------------------------------------------------------------------
//    Divergencias de estoque
//--------------------------------------------------------------------------------------------------------------	
//---Buscando produtos inseridos em D.E que estão concluidase foram inseridos entre a data de apuração
// $sqlT030 = mysql_query("SELECT T030_Id,
// 							   D001_Id
// 							   D001_Codigo_Produto,
// 							   T030_Quantidade,
// 							   T030_Flag_Entrada_Saida,
// 							   D001_Flag_Tipo_Item
// 							FROM
// 								T030 
// 							LEFT JOIN T029 ON T029_Id = T030_T029_Id 
// 							LEFT JOIN D009 ON D009_Id = T030_D009_Id 
// 							LEFT JOIN D049 ON D049_Id = D009_D049_Id 
// 							LEFT JOIN D001 ON D001_Id = D049_D001_Id 
// 							WHERE T029_Flag_Ok='S'
// 							AND (T030_Data_Inclusao>='{$dataInicial}' AND T030_Data_Inclusao<='{$dataFinal}')
// 							AND T030_Flag_Entrada_Saida!='B'");

// while($mT030 = mysql_fetch_array($sqlT030)){
// 	if(($mT030['D001_Flag_Tipo_Item']>=00 && $mT030['D001_Flag_Tipo_Item']<=06) || $mT030['D001_Flag_Tipo_Item']==10){
// 		$qtdPos = "";
// 		$qtdNeg = "";
// 	//---Validando se é uma entrada ou saída de estoque e adicionando o valor para o campo correspondente (positivo ou negativo)
// 		if($mT030['T030_Flag_Entrada_Saida']=="E"){
// 			$qtdPos = $mT030['T030_Quantidade'];
// 		}else{
// 			$qtdNeg = $mT030['T030_Quantidade'];
// 		}

// 		$dataCorrecao = date('Y-m-d',strtotime("-1 day", strtotime($dataInicial)));
// 		$K280 = array(
// 			$REG = 'K280',												// 	Texto fixo contendo "K280"
// 			$DT_EST = formataData($dataCorrecao),						//  Data do estoque final escriturado que está sendo corrigido 
// 			$COD_ITEM = trim($mT030['D001_Codigo_Produto']),			//  Código do item (campo 02 do Registro 0200) 
// 			$QTD_COR_POS = formataDecimalBlocoK($qtdPos),				//  Quantidade de correção positiva de apontamento ocorrido em período de apuração anterior
// 			$QTD_COR_NEG = formataDecimalBlocoK($qtdNeg),				//  Quantidade de correção negativa de apontamento ocorrido em período de apuração anterior	
// 			$IND_EST = '0',												//  Indicador de estoque (0 = Estoque de propriedade de informante e em seu poder.
// 			$COD_PART = ''												// Código de identificação do participante no arquivo. - no bloco_0, registro 150 foi informado ao campo T007_D024_Id. Não sei se isso está certo.
// 		);
		// if(verificaRegistro0200($mT030['D001_Id'],$array0200)===false){
		// 	// $array0200[] = $mT030['D001_Id'];
		// }
		
// 		$K280 = implode("|", $K280);
// 		$layout .= '|' . $K280 . '|' . _SALTO_LINHA;
// 		$contador_bloco_K++;
// 	}else{
// 		continue;
// 	}
	
// }

//---------------------------------------------------------------------------------------------------
//    Calculo de saldo do balanço
//---------------------------------------------------------------------------------------------------
//---select buscando todas as entradas em historico do produto que sejam balanço e tenham sido inseridas entre as datas de apuração
// $sqlT001 = mysql_query("SELECT 
// 						  T001_Id,
// 						  D001_Codigo_Produto,
// 						  T001_Data_Lancamento,
// 						  T001_Flag_Operacao,
// 						  T001_Quantidade_Saldo,
// 						  D001_Flag_Tipo_Item,
// 						  D009_Id,
//					   	  D001_Id
// 						FROM
// 						  T001
// 					 	LEFT JOIN D009 ON D009_Id = T001_D009_Id
// 					  	LEFT JOIN D049 ON D049_Id = D009_D049_Id 
// 					  	LEFT JOIN D001 ON D001_Id = D049_D001_Id  
// 					  	LEFT JOIN T030 ON T030_Id = T001_T030_Id 
// 					  	LEFT JOIN T029 ON T030_T029_Id = T029_Id 
// 					  	WHERE  (T001_Data_Lancamento>='{$dataInicial}' AND T001_Data_Lancamento<='{$dataFinal}')
// 			  			AND T001_Flag_Operacao='B'");

// while($mT001 = mysql_fetch_array($sqlT001)){
// 	if(($mT001['D001_Flag_Tipo_Item']>=00 && $mT001['D001_Flag_Tipo_Item']<=06) || $mT001['D001_Flag_Tipo_Item']==10){
// 		$qtdPos = "";
// 		$qtdNeg = "";
// 	//---buscando o saldo enterior ao balanço pois o balanço não grava a diferença de saldo e sim entra o saldo que deve
// 		$sqlT001Anterior = mysql_query("SELECT 
// 											T001_Id,
// 											T001_Data_Lancamento,
// 											T001_Flag_Operacao,
// 											T001_Quantidade_Saldo,
// 											D009_Id
// 										FROM
// 											T001
// 										LEFT JOIN D009 ON D009_Id = T001_D009_Id
// 										LEFT JOIN D049 ON D049_Id = D009_D049_Id 
// 										LEFT JOIN D001 ON D001_Id = D049_D001_Id  
// 										LEFT JOIN T030 ON T030_Id = T001_T030_Id 
// 										LEFT JOIN T029 ON T030_T029_Id = T029_Id 
// 										WHERE D009_Id='{$mT001['D009_Id']}' AND T001_Id<'{$mT001['T001_Id']}'
// 										ORDER BY T001_Id DESC
// 										LIMIT 1;");
// 		$mT001Anterior = mysql_fetch_array($sqlT001Anterior);
		
// 		if(empty($mT001Anterior['T001_Quantidade_Saldo'])){
// 			$mT001Anterior['T001_Quantidade_Saldo'] = 0;
// 		}
// 	//---substração do saldo entrado no balanço e o saldo anterior ao balanço 
// 		$diferencaSaldo = $mT001['T001_Quantidade_Saldo'] - $mT001Anterior['T001_Quantidade_Saldo'];
// 	//---tratando o valor do saldo positivo ou negativo
// 	//---Adicionando 3 casas decimais e retirando o simbolo de negativo com a função abs
// 		if($diferencaSaldo<0){
// 			$qtdNeg = formataDecimalBlocoK(abs($diferencaSaldo));
// 		}else{
// 			$qtdPos = formataDecimalBlocoK(abs($diferencaSaldo));
// 		}

// 		$dataCorrecao = date('Y-m-d',strtotime("-1 day", strtotime($dataInicial)));
// 		$K280 = array(
// 			$REG = 'K280',												// 	Texto fixo contendo "K280"
// 			$DT_EST = formataData($dataCorrecao),						    //  Data do estoque final escriturado que está sendo corrigido 
// 			$COD_ITEM = trim($mT001['D001_Codigo_Produto']),					//  Código do item (campo 02 do Registro 0200) 
// 			$QTD_COR_POS = $qtdPos,										//  Quantidade de correção positiva de apontamento ocorrido em período de apuração anterior
// 			$QTD_COR_NEG = $qtdNeg,										//  Quantidade de correção negativa de apontamento ocorrido em período de apuração anterior	
// 			$IND_EST = '0',												//  Indicador de estoque (0 = Estoque de propriedade de informante e em seu poder.
// 			$COD_PART = ''				       							//  Código de identificação do participante no arquivo. - no bloco_0, registro 150 foi informado ao campo T007_D024_Id. Não sei se isso está certo.
// 		);
		// if(verificaRegistro0200($mT001['D001_Id'],$array0200)===false){
		// 	// $array0200[] = $mT001['D001_Id'];
		// }
// 		$K280 = implode("|", $K280);
// 		$layout .= '|' . $K280 . '|' . _SALTO_LINHA;
// 		$contador_bloco_K++;
// 	}else{
// 		continue;
// 	}
// }


# REGISTRO K220 - OUTRAS MOVIMENTAÇÕES INTERNAS DE ENTRE MERCADORIAS - DEIXAR POR ENQUANTO
/*
	Nº Campo 			Descrição 												Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K220" 								C 		4 		- 		O
	02 DT_MOV 			Data da movimentação interna 							N 		8 		- 		O
	03 COD_ITEM_ORI 	Código do item de origem (campo 02 do Registro 0200)	C 		60 		- 		O
	04 COD_ITEM_DEST 	Código do item de destino (campo 02 do Registro 0200) 	C 		60 		-	 	O
	05 QTD 				Quantidade movimentada 									N 		- 		3 		O
*/

# REGISTRO K230 - ITENS PRODUZIDOS - DEIXAR POR ENQUANTO
// Dados do produto final que foi produzido
/*
	Nº Campo 			Descrição 												Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K230" 								C 		4 		- 		O
	02 DT_INI_OP 		Data de início da ordem de produção 					N 		8 		- 		OC
	03 DT_FIN_OP 		Data de conclusão da ordem de produção 					N 		8 		- 		OC
	04 COD_DOC_OP 		Código de identificação da ordem de produção 			C 		30 		- 		OC
	05 COD_ITEM 		Código do item produzido (campo 02 do Registro 0200) 	C 		60 		- 		O
	06 QTD_ENC 			Quantidade de produção acabada 							N 		- 		3 		O
*/

# REGISTRO K235 - INSUMOS CONSUMIDOS - DEIXAR POR ENQUANTO
//Dados dos componentes utilizados para produzir o K230
/*
	Nº Campo 			Descrição 																						Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K235" 																		C 		4 		- 		O
	02 DT_SAÍDA 		Data de saída do estoque para alocação ao produto 												N 		8 		- 		O
	03 COD_ITEM 		Código do item componente/insumo (campo 02 do Registro 0200)									C 		60 		- 		O
	04 QTD 				Quantidade consumida do item 																	N 		- 		3 		O
	05 COD_INS_SUBST 	Código do insumo que foi substituído, caso ocorra a substituição (campo 02 do Registro0210) 	C 		60 		- 		OC

*/

# REGISTRO K250 - INDUSTRIALIZAÇÃO EFETUADA POR TERCEIROS - ITENS PRODUTOS - DEIXAR POR ENQUANTO
/*
	Nº Campo 			Descrição 												Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K250" 								C 		4 		- 		O
	02 DT_PROD 			Data do reconhecimento da produção ocorrida no terceiro N 		8 		- 		O
	03 COD_ITEM 		Código do item produzido (campo 02 do Registro 0200) 	C 		60 		- 		O
	04 QTD 				Quantidade produzida 									N 		- 		3 		O
*/

# REGISTRO K255 - INSDUSTRIALIZAÇÃO EM TERCEIROS - INSUMOS CONSUMIDOS - DEIXAR POR ENQUANTO
/*
	Nº Campo 			Descrição 																									Tipo 	Tam 	Dec 	Obrig.
	01 REG 				Texto fixo contendo "K255" 																					C 		4 		- 		O
	02 DT_CONS 			Data do reconhecimento do consumo do insumo referente ao produto informado no campo 04 do Registro K250 	N 		8 		- 		O
	03 COD_ITEM 		Código do insumo (campo 02 do Registro 0200) 																C 		60 		- 		O
	04 QTD 				Quantidade de consumo do insumo. 																			N 		- 		3 		O
	05 					COD_INS_SUBST Código do insumo que foi substituído, caso ocorra a substituição (campo 02 do Registro 0210)	C 		60 		- 		OC
*/

# REGISTRO K990 - ENCERRAMENTO DO BLOCO K
if($IND_MOV=='0'){										#TAM
	$K990 = array(
		$REG = 'K990',									#4 		TEXTO FIXO CONTENDO "K990"
		$QTD_LIN_K = $contador_bloco_K			#N 		QUANTIDADE TOTAL DE LINHAS DO BLOCK K
	);
}
$K990 = implode("|", $K990);
$layout .= '|' . $K990 . '|' . _SALTO_LINHA;



$linhas_bloco_0 = $contador_bloco_K;
