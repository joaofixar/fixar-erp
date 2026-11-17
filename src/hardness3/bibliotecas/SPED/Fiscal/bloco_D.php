<?php

namespace hardness;

global $g;
/*
# BLOCO C # INFORMAÇÕES PRINCIPAIS #
REG        OBRIG		DESCRIÇÃO
D001        O           Abertura do Bloco C
D100        OC          Documentos Fiscais (CÓDIGO 07, 08, 8B, 09, 10, 11, 26, 27, 57 , 67 e 63)
D190        O(D100)     REGISTRO ANALÍTICO DOS DOCUMENTOS (CÓDIGO 07, 08, 8B, 09, 10, 11, 26, 27, 57 e 67)
D990        O           Encerramento do Bloco
*/

$contador_bloco_D = 1;

$sqlT084 = mysql_query("SELECT Id
                            FROM (
                                SELECT T084_Id AS Id
                                  FROM T084
                              WHERE IF(T084_Flag_Entrada_Saida = 'E',T084_Data_Lancamento, T084_Data_Emissao) BETWEEN '{$dataInicial}' AND '{$dataFinal}'
                                   AND T084_C004_Id='{$g['empresaAtual']}'    AND T084_Flag_Cancelado != 'S'
                                   AND T084_Numero_Protocolo_Cte > 0
                                   AND T084_Modelo in('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')
                             UNION
                                SELECT T013_Id as Id
                                  FROM T013                                                                
                                 WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
                                   AND {$dataNFRecebida} <= '{$dataFinalHora}'
                                   AND T013_C004_Id = '{$g['empresaAtual']}' 
                                   AND T013_Flag_Cancelada != 'S'
                                   AND T013_Flag_Estoque_Finalizado = 'S' 
                                   AND T013_Tipo IN ('7','8')) as conhecimentos");

$IND_MOV = (mysql_num_rows($sqlT084) > 0) ? '0' : '1';
# REGISTRO D001
$D001 = array(                                                                            #TAM								
    $REG = 'D001',                                                                        #004* 
    $IND_MOV                                                                            #001*	Indicador de movimento (0 Bloco com dados informados/1 Bloco sem dados informados)
);
$D001 = implode("|", $D001);
$layout .= '|' . $D001 . '|' . _SALTO_LINHA;

if ($IND_MOV == '0') {

    // tradando a data para os conhecimentos
    // $dataInicialHora = $dataInicial . ' 00:00:00';
    // $dataFinalHora   = $dataFinal . ' 23:59:59';
    //-----------------------------------------

    //Buscando os conhecimentos de transporte 
    $sqlT084 = mysql_query("SELECT Transportadora.D024_Id as Transportadora,
                                   IF(T084_Flag_Entrada_Saida = 'E', Transportadora.D024_Cnpj,T084_Emitente_CNPJ) as Transportadora_Cnpj,                              
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
                                   IF(T084_Flag_Entrada_Saida = 'E',T084_Data_Lancamento, T084_Data_Emissao) as T084_Data_Lancamento,
                                   T084_Valor_Conhecimento,
                                   T084_Valor_Base_ICMS,
                                   T084_Aliquota_ICMS,
                                   T084_Valor_ICMS,
                                   contaCFOP.D014_Codigo_Plano_Conta_Fiscal as D006_D014_Codigo_Plano_Conta_Fiscal, 
                                   contaCab.D014_Codigo_Plano_Conta_Fiscal as T084A_D014_Codigo_Plano_Conta_Fiscal,
                                   T084A_Chave_Acesso_Cte_Original,
                                   Origem.D020_Codigo_IBGE as CodIBGE_Origem,
                                   Destino.D020_Codigo_IBGE as CodIBGE_Destino,
                                   ClienteFornecedorIBGE.D020_Codigo_IBGE as CodIBGE_ClienteFornecedor,
                                   EmpresaIBGE.D020_Codigo_IBGE as CodIBGE_Empresa,
                                   T084_Tipo_Nota,
                                   D006_Codigo_CFOP,
                                   D059_Codigo_ST,
                                   D006_Aliquota_ICMS,
                                   D006_Flag_Deb_Crd_ICMS,                            
                                   T084_D024_Tomador_Id,
                                   T084_D024_Remetente_Id,
                                   T084_Flag_Entrada_Saida,
                                   T084_Emitente_CNPJ,
                                   T084_Tomador_CNPJ,    
                                   T084_D024_Destinatario_Id
                              FROM T084
                         LEFT JOIN D022 ON D022_Id = T084_D022_Id
                         LEFT JOIN D024 as Transportadora ON Transportadora.D024_Cnpj = D022_Cnpj
                         LEFT JOIN C004 ON C004_Id = T084_C004_Id                                  
                         LEFT JOIN D020 as Origem ON Origem.D020_Id = T084_D020_Id_Origem
                         LEFT JOIN D020 as Destino ON Destino.D020_Id = T084_D020_Id_Destino
                         LEFT JOIN D020 as EmpresaIBGE ON EmpresaIBGE.D020_Id = C004_D020_Id
                         LEFT JOIN D024 as ClienteFornecedor ON ClienteFornecedor.D024_Id = T084_D024_Id
                         LEFT JOIN D020 as ClienteFornecedorIBGE ON ClienteFornecedorIBGE.D020_Id = ClienteFornecedor.D024_D020_Id
                         LEFT JOIN D006 ON D006_Id = T084_D006_Id
                         LEFT JOIN D014 as contaCFOP ON contaCFOP.D014_ID = D006_D014_Id
                         LEFT JOIN T084A ON T084A_T084_Id = T084_Id
                         LEFT JOIN D014 as contaCab ON contaCab.D014_ID = T084A_D014_Id
                         LEFT JOIN D059 ON D059_Id = D006_D059_Id
                             WHERE IF(T084_Flag_Entrada_Saida = 'E',T084_Data_Lancamento, T084_Data_Emissao) BETWEEN '{$dataInicial}' AND '{$dataFinal}'                                
                               AND T084_C004_Id='{$g['empresaAtual']}'  AND T084_Flag_Cancelado != 'S'                                
                               AND T084_Numero_Protocolo_Cte > 0
                               AND T084_Modelo IN('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')");

    while ($T084 = mysql_fetch_array($sqlT084)) {
       
        $IND_OPER       = (removeChars($T084['Transportadora_Cnpj'])  == removeChars($T084['C004_Cnpj'])) ? '1' : '0';
        $IND_EMIT       = ($IND_OPER == '1') ? '0' : '1';

        $COD_PART       = $T084['Transportadora'];       
        
        $IND_FRT = $T084['T084_Indicador_Tipo_Frete'];
        $COD_SIT = !empty($T084['T084_Situacao']) ? $T084['T084_Situacao'] : '00'; //Documento regular        

          // Empresa atual é o remetente
        if($T084['T084_Tipo_Nota'] == 'EMITIDA') {
            $COD_MUN_ORIG = $T084['CodIBGE_Empresa'];
            $COD_MUN_DEST = $T084['CodIBGE_ClienteFornecedor'];
        } else {
            $COD_MUN_ORIG = $T084['CodIBGE_ClienteFornecedor'];
            $COD_MUN_DEST = $T084['CodIBGE_Empresa'];
        }
                 
        $VL_BC_ICMS     = $T084['T084_Valor_Base_ICMS'];
        $ALIQ_ICMS      = $T084['T084_Aliquota_ICMS'];    
        $VL_ICMS        = $T084['T084_Valor_ICMS'];
        
        //CT-e Emitidos
        if ($IND_EMIT == '0') {
            
            $COD_PART = $T084['T084_D024_Tomador_Id'];

            // Hoje o sistema não cobre (9 - Sem cobrança de frete)
            if (in_array($T084['T084_D024_Tomador_Id'], [$T084['T084_D024_Remetente_Id'],$T084['T084_D024_Destinatario_Id']])) {
                $IND_FRT = 1;
            } elseif (removeChars($T084['T084_Emitente_CNPJ']) == removeChars($T084['T084_Tomador_CNPJ'])) {
                $IND_FRT = 0;
            } else {
                $IND_FRT = 2;
            }
        
            $COD_MUN_ORIG = $T084['CodIBGE_Origem'];
            $COD_MUN_DEST = $T084['CodIBGE_Destino'];
        }
        
        //Para documentos de entrada, os campos de valor de imposto/contribuição, base de cálculo e alíquota só devem ser informados 
        //se o adquirente tiver direito à apropriação do crédito (enfoque do declarante).
        if($IND_OPER == '0' && $T084['D006_Flag_Deb_Crd_ICMS'] != 'S') {
            $VL_BC_ICMS = '0,00';
            $VL_ICMS = '0,00';
        }

        $D014_Codigo_Plano_Conta_Fiscal = $T084['D006_D014_Codigo_Plano_Conta_Fiscal'];
        if(!empty($T084['T084A_D014_Codigo_Plano_Conta_Fiscal'])){
            $D014_Codigo_Plano_Conta_Fiscal = $T084['T084A_D014_Codigo_Plano_Conta_Fiscal'];
        }

        # REGISTRO D100
        $D100 = array(                                                                          #TAM
            $REG = 'D100',                                                                      #004	
            $IND_OPER,                                                                          #001*	Indicador do tipo de operação (0 Aquisição/1 Prestação)				   
            $IND_EMIT,                                                                          #001*   Indicador do emitente do documento fiscal (0 Emissão própria/1 Terceiros)
            $COD_PART,                                                                          #060    Código do participante (campo 02 do Registro 0150)
            $COD_MOD = $T084['T084_Modelo'],                                                    #002*   Código do modelo do documento fiscal
            $COD_SIT,                                                                           #002*   Código da situação do documento fiscal
            $SER = $T084['T084_Serie'],                                                         #004    Série do documento fiscal                                                        
            $SUB = $T084['T084_Subserie'],                                                      #003    Subsérie do documento fiscal
            $NUM_DOC = $T084['T084_Numero_Conhecimento'],                                       #009    Número do documento fiscal    
            $CHV_CTE = $T084['T084_Chave_Acesso_Cte'],                                          #044*   Chave do Conhecimento de Transporte Eletrônico ou do Bilhete de Passagem Eletrônico      
            $DT_DOC = formataData(substr($T084['T084_Data_Emissao'],0,10)),                     #008*   Data da emissão do documento fiscal
            $DT_A_P = formataData(substr($T084['T084_Data_Lancamento'],0,10)),                  #008*   Data da aquisição ou da prestação do serviço
            $TP_CTe = $T084['T084_Tipo_Conhecimento'],                                          #001*   Tipo de Conhecimento de Transporte Eletrônico conforme definido no Manual de Integração do CT-e ou do Bilhete de Passagem Eletrônico conforme definido no Manual de Integração do BP-e 
            $CHV_CTE_REF = $T084['T084A_Chave_Acesso_Cte_Original'],                            #044*   Chave do CT-e ou Bilhete de Passagem Eletrônico substituído
            $VL_DOC = formataDecimal($T084['T084_Valor_Conhecimento']),                         #       Valor total do documento fiscal
            $VL_DESC_CTe = '',                                                                  #       Valor total do desconto  -  Recebendo vazio pois no sistema não é possivel dar desconto ao CT-e
            $IND_FRT,                                                                           #001*   Indicador do tipo do frete
            $VL_SERV = formataDecimal($T084['T084_Valor_Conhecimento']),                        #       Valor total da prestação de serviço
            formataDecimal($VL_BC_ICMS),                                                        #       Valor da base de cálculo do ICMS
            formataDecimal($VL_ICMS),                                                           #       Valor do ICMS
            $VL_NT,                                                                             #       Valor não-tributado
            $COD_INF,                                                                           #       Código da informação complementar do documento
            $COD_CTA = $D014_Codigo_Plano_Conta_Fiscal,                                         #       Código da conta analítica contábil debitada/creditada
            $COD_MUN_ORIG,                                                                      #007*   Código do município de origem do serviço, conforme a tabela IBGE (Preencher com 9999999, se Exterior)
            $COD_MUN_DEST,                                                                      #007*   Código do município de destino, conforme a tabela IBGE (Preencher com 9999999, se Exterior)            
        );
        $D100 = implode("|", $D100);
        $layout .= '|' . $D100 . '|' . _SALTO_LINHA;
        $contador_bloco_D++;
        
         
		$CST_ICMS       =  str_pad(removeChars($T084['D059_Codigo_ST']),3,0,STR_PAD_LEFT); 

        if($IND_OPER == '0' && $T084['D006_Flag_Deb_Crd_ICMS'] != 'S') {
            $VL_BC_ICMS = '0,00';
            $VL_ICMS = '0,00';
            $ALIQ_ICMS = '0,00';
        }
        
        
        # REGISTRO D190
        $D190 = array(                                                                     #TAM													
            $REG = 'D190',                                                                 #004     Texto fixo contendo "D190"
            $CST_ICMS = $CST_ICMS,                                                         #003*    Código da Situação Tributária, conforme a tabelaindicada no item 4.3.1                                                               
            $CFOP = removeChars($T084['D006_Codigo_CFOP']),                                #004*    Código Fiscal de Operação e Prestação
            formataDecimal($ALIQ_ICMS) ,                                                   #        Alíquota do ICMS
            $VL_OPR = formataDecimal($T084['T084_Valor_Conhecimento']),                    #        Valor da operação correspondente à combinação de CST_ICMS, CFOP, e alíquota do ICMS
            formataDecimal($VL_BC_ICMS),                                                   #        Parcela correspondente ao "Valor da base de cálculo do ICMS" referente à combinação CST_ICMS, CFOP, e alíquota do ICMS
            formataDecimal($VL_ICMS),                                                      #        Parcela correspondente ao "Valor do ICMS" referente à combinação CST_ICMS, CFOP e alíquota do ICMS
            $VL_RED_BC = 0,                                                                #        Valor não tributado em função da redução da base de cálculo do ICMS, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
            $COD_OBS = ''                                                                  #        Código da observação do lançamento fiscal (campo 02 do Registro 0460)
        );
        $D190 = implode("|", $D190);
        $layout .= '|' . $D190 . '|' . _SALTO_LINHA;
        $contador_bloco_D++;                 
    }

    # REGISTRO D500

    $sqlT013 = mysql_query("SELECT  
                                T013_Id, 
                                T013_Tipo,
                                T013_D024_Id,  
                                T013_Numero_Nota_Fiscal,  
                                T013_Data_Emissao,
                                DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
                                T013_Valor_Desconto,
                                T013_Valor_Total,
                                T013_Valor_Total_Produtos,
                                T013_Valor_Despesas_Acessorias,
                                T013_Valor_Total_Base_ICMS,
                                T013_Valor_Total_ICMS,                                
                                T013_Valor_Total_PIS,
                                T013_Valor_Total_COFINS,
                                D014_Codigo_Plano_Conta_Fiscal,
                                D006_Flag_Deb_Crd_ICMS,  
                                D006_Flag_Deb_Crd_PIS,
                                D006_Flag_Deb_Crd_COFINS,
                                T013_Tipo_Assinante,
                                sum(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T013_Valor_Base_ICMS_Sem_Reducao        								
                           FROM T013
                      LEFT JOIN D006 ON D006_Id = T013_D006_Id
                      LEFT JOIN D014 ON D014_Id = D006_D014_Id
                      LEFT JOIN T014 ON T014_T013_Id = T013_Id
                          WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
                            AND {$dataNFRecebida} <= '{$dataFinalHora}'
                            AND T013_C004_Id = '{$g['empresaAtual']}'
                            AND T013_Flag_Cancelada != 'S'
                            AND T013_Flag_Estoque_Finalizado = 'S'
                            AND T013_Tipo IN ('7','8')
                       GROUP BY T013_Id");

	while($T013 = mysql_fetch_array($sqlT013)) {						
        $IND_OPER = 0;
        
        $COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');
        
        $VL_BC_ICMS = formataDecimal($T013['T013_Valor_Total_Base_ICMS']);
        $VL_ICMS = formataDecimal($T013['T013_Valor_Total_ICMS']);
        $VL_PIS = formataDecimal($T013['T013_Valor_Total_PIS']);
        $VL_COFINS = formataDecimal($T013['T013_Valor_Total_COFINS']);
        $VL_SERV_NT = formataDecimal($T013['T013_Valor_Base_ICMS_Sem_Reducao'] - $T013['T013_Valor_Total_Base_ICMS']);

        /*IMPORTANTE: para documentos de entrada, os campos de valor de imposto, base de cálculo e alíquota só devem
        ser informados se o adquirente tiver direito à apropriação do crédito (enfoque do declarante*/

        if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_ICMS'] != 'S'){
			$VL_BC_ICMS = '0,00';
			$VL_ICMS = '0,00';			
		}

		if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_PIS'] != 'S') {
			$VL_PIS = '0,00';
		}

		if($IND_OPER == 0 && $T013['D006_Flag_Deb_Crd_COFINS'] != 'S') {
			$VL_COFINS = '0,00';
		}
        
		

		# Registro D500
		$D500 = array(																#TAM			
            $REG = 'D500',                                                          #Texto fixo contendo "D500" C 004 - O O
            $IND_OPER = '0',                                                        #Indicador do tipo de operação: 0- Aquisição; 1- Prestação
            $IND_EMIT = '1',                                                        #Indicador do emitente do documento fiscal: 0- Emissão própria; 1- Terceiros
            $COD_PART = $T013['T013_D024_Id'],                                      #Código do participante (campo 02 do Registro 0150): - do prestador do serviço, no caso de aquisição; - do tomador do serviço, no caso de prestação.
            $COD_MOD,                                                               #Código do modelo do documento fiscal, conforme a Tabela 4.1.1
            $COD_SIT = '00',                                                        #Código da situação do documento fiscal, conforme a Tabela 4.1.2
            $SER = $T013['T013_Serie'],                                             #Série do documento fiscal
            $SUB,                                                                   #Subsérie do documento fiscal
            $NUM_DOC = $T013['T013_Numero_Nota_Fiscal'],                            #Número do documento fiscal            
            $DT_DOC = formataData(substr($T013['T013_Data_Emissao'],0,10)),         #Data da emissão do documento fiscal 
            $DT_A_P = formataData(substr($T013[$dataNFRecebidaLancarSPED],0,10)),         #Data da entrada (aquisição) ou da saída (prestação do serviço)
            $VL_DOC = formataDecimal($T013['T013_Valor_Total']),                    #Valor total do documento fiscal 
            $VL_DESC = formataDecimal($T013['T013_Valor_Desconto']),                #Valor total do desconto 
            $VL_SERV = formataDecimal($T013['T013_Valor_Total_Produtos']),          #Valor da prestação de serviços 
            $VL_SERV_NT,                                                            #Valor total dos serviços não-tributados pelo ICMS 
            $VL_TERC = '0,00',                                                      #Valores cobrados em nome de terceiros
            $VL_DA = formataDecimal($T013['T013_Valor_Despesas_Acessorias']),       #Valor de outras despesas indicadas no documento fiscal
            $VL_BC_ICMS,                                                            #Valor da base de cálculo do ICMS 
            $VL_ICMS,                                                               #Valor do ICMS 
            $COD_INF = '',                                                          #Código da informação complementar (campo 02 do Registro 0450)
            $VL_PIS,                                                                #Valor do PIS 
            $VL_COFINS,                                                             #Valor da COFINS
            $COD_CTA = $T013['D014_Codigo_Plano_Conta_Fiscal'],                     #Código da conta analítica contábil debitada/creditada
            $TP_ASSINANTE = $T013['T013_Tipo_Assinante'],                           #Código do Tipo de Assinante: 1 - Comercial/Industrial 2 - Poder Público 3 - Residencial/Pessoa física 4 - Público 5 - Semi-Público 6 - Outros
		);

		$D500 = str_replace('|', '', $D500);
		$D500 = implode("|", $D500);
		$layout .= '|' . $D500 . '|' . _SALTO_LINHA;
        $contador_bloco_D++; 

        $sqlT014 = mysql_query("SELECT
									T014_Flag_Situacao_Tributaria,
									D006_Codigo_CFOP, 
									sum(T014_Valor_Base_ICMS) as T014_Valor_Base_ICMS,
									T014_Aliquota_ICMS,
									sum(T014_Valor_ICMS) as T014_Valor_ICMS,									
									sum(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T014_Valor_Base_ICMS_Sem_Reducao,					
									(	sum(T014_Valor_Total_Custo) + 
										sum(T014_Valor_Despesas_Acessorias) -
                                        sum(T014_Valor_Total_Desconto)
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
            $VL_RED_BC = '0,00';
            $VL_OPR = $T014['VL_OPR'];			

            //o valor de VL_RED_BC só pode ser preenchido se os dois últimos dígitos do campo CST_ICMS forem iguais a 20, 70 ou 90.
            $CST_ICMS = substr($T014['T014_Flag_Situacao_Tributaria'],-2);


            if($CST_ICMS == '20' || $CST_ICMS == '70' || $CST_ICMS == '90') {                
                $VL_RED_BC = formataDecimal($T014['T014_Valor_Base_ICMS_Sem_Reducao'] - $T014['T014_Valor_Base_ICMS']);			                
            }

			$D590 = array(
				$REG = 'D590',                                                      #Texto fixo contendo "D590"
                $CST_ICMS = $T014['T014_Flag_Situacao_Tributaria'],                 #Código da Situação Tributária, conforme a tabela indicada no item 4.3.1
                $CFOP = $T014['D006_Codigo_CFOP'],                                  #Código Fiscal de Operação e Prestação, conforme a tabela indicada no item 4.2.2
                $ALIQ_ICMS = formataDecimal($T014['T014_Aliquota_ICMS']),           #Alíquota do ICMS
                formataDecimal($VL_OPR),                                            #Valor da operação correspondente à combinação de CST_ICMS, CFOP, e alíquota do ICMS, incluídas as despesas acessórias e acréscimos
                $VL_BC_ICMS = formataDecimal($T014['T014_Valor_Base_ICMS']),        #Parcela correspondente ao "Valor da base de cálculo do ICMS" referente à combinação CST_ICMS, CFOP, e alíquota do ICMS
                $VL_ICMS = formataDecimal($T014['T014_Valor_ICMS']),                #Parcela correspondente ao "Valor do ICMS" referente à combinação CST_ICMS, CFOP, e alíquota do ICMS
                $VL_BC_ICMS_UF = '0,00',                                            #Parcela correspondente ao valor da base de cálculo do ICMS de outras UFs, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
                $VL_ICMS_UF = '0,00',                                               #Parcela correspondente ao valor do ICMS de outras UFs, referente à combinação de CST_ICMS, CFOP, e alíquota do ICMS.
                $VL_RED_BC,                                                         #Valor não tributado em função da redução da base de cálculo do ICMS, referente à combinação de CST_ICMS, CFOP e alíquota do ICMS.
                $COD_OBS = $T014['D188_Codigo_Observacao'],                         #Código da observação (campo 02 do Registro 0460)
			);

			// //popula registro 0460
			if(!empty($T014['D188_Codigo_Observacao'])) {
				insertRegistro0460($T014['D188_Codigo_Observacao'], $T014['D188_Descricao_Observacao']);
			}

			$D590 = implode("|", $D590);
			$layout .= '|' . $D590 . '|' . _SALTO_LINHA;			
            $contador_bloco_D++; 
		}
	}

    # REGISTRO D700  (NFCom - modelo 62)
    $sql = "SELECT
                T013_Id,
                SUM(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) as T013_Valor_Base_ICMS_Sem_Reducao,
                T013_Valor_Total_Base_ICMS,
                T013_Valor_Total_Base_ICMS,
                T013_Valor_Total_ICMS,
                T013_Valor_Total_PIS,
                T013_Valor_Total_COFINS,
                D006_Flag_Deb_Crd_ICMS,
                D006_Flag_Deb_Crd_PIS,
                D006_Flag_Deb_Crd_COFINS,
                T013_D024_Id,
                T013_Serie,
                T013_Numero_Nota_Fiscal,
                T013_Data_Emissao,
                DATE({$dataNFRecebidaLancarSPED}) as {$dataNFRecebidaLancarSPED},
                T013_Valor_Total,
                T013_Valor_Desconto,
                T013_Valor_Total_Produtos,
                T013_Valor_Despesas_Acessorias,
                T013_Chave_Acesso_Nfe,
                T013_Flag_Finalidade_NFe,
                D020_Codigo_IBGE
            FROM T013
                LEFT JOIN D024 ON D024_Id = T013_D024_Id
                LEFT JOIN D020 ON D020_Id = D024_D020_Id 
                LEFT JOIN D006 ON D006_Id = T013_D006_Id
                LEFT JOIN T014 ON T014_T013_Id = T013_Id
            WHERE
                {$dataNFRecebida} >= '{$dataInicialHora}'
                AND {$dataNFRecebida} <= '{$dataFinalHora}'
                AND T013_C004_Id = '{$g['empresaAtual']}'
                AND T013_Flag_Cancelada != 'S'
                AND T013_Flag_Estoque_Finalizado = 'S'
                AND T013_Tipo IN ('12')
            GROUP BY
                T013_Id";

    $sqlT013 = mysql_query($sql);
    while ($T013 = mysql_fetch_array($sqlT013)) {

        $VL_SERV_NT = formataDecimal($T013['T013_Valor_Base_ICMS_Sem_Reducao'] - $T013['T013_Valor_Total_Base_ICMS']);
        $VL_BC_ICMS = ($T013['D006_Flag_Deb_Crd_ICMS'] == 'S') ? formataDecimal($T013['T013_Valor_Total_Base_ICMS']) : '0,00';
        $VL_ICMS = ($T013['D006_Flag_Deb_Crd_ICMS'] == 'S') ? formataDecimal($T013['T013_Valor_Total_ICMS']) : '0,00';
        $VL_PIS = ($T013['D006_Flag_Deb_Crd_PIS'] == 'S') ? formataDecimal($T013['T013_Valor_Total_PIS']) : '0,00';
        $VL_COFINS = ($T013['D006_Flag_Deb_Crd_COFINS'] == 'S') ? formataDecimal($T013['T013_Valor_Total_COFINS']) : '0,00';

        $finalidade = [1 => '0', 3 => '4']; // 0 - Normal, 4 - Ajuste

        $D700 = array(
            $REG = 'D700',                                                              # D700
            $IND_OPER = '0',                                                            # 0 - Entrada / 1 - Saida
            $IND_EMIT = '1',                                                            # 0 - Emissão Propria / 1 - Terceiros
            $COD_PART = $T013['T013_D024_Id'],                                          # Cod participante (campo 2 do D0150) do prestador no caso de entradas
            $COD_MOD = '62',                                                            # Cod modelo doc fiscal (Tabela 4.1.1)
            $COD_SIT = '00',                                                            # Cod modelo doc fiscal (Tabela 4.1.2)
            $SER = $T013['T013_Serie'],                                                 # Serie doc fiscal
            $NUM_DOC = $T013['T013_Numero_Nota_Fiscal'],                                # Num doc fiscal
            $DT_DOC = formataData(substr($T013['T013_Data_Emissao'],0,10)),             # Data emissão doc fiscal
            $DT_E_S = formataData(substr($T013[$dataNFRecebidaLancarSPED],0,10)),             # Data entrada ou saida
            $VL_DOC = formataDecimal($T013['T013_Valor_Total']),                        # Valor do doc fiscal
            $VL_DESC = formataDecimal($T013['T013_Valor_Desconto']),                    # Valor do desconto
            $VL_SERV = formataDecimal($T013['T013_Valor_Total_Produtos']),              # Valor dos serviços  tributados pelo ICMS
            $VL_SERV_NT,                                                                # Valores cobrados  em nome do prestador sem destaque  de ICMS
            $VL_TERC = '0,00',                                                          # Valores cobrados em nome  de terceiros
            $VL_DA = formataDecimal($T013['T013_Valor_Despesas_Acessorias']),           # Valor de despesas acessórias indicadas no doc fiscal
            $VL_BC_ICMS,                                                                # Valor da Base de Cálculo (BC) do ICMS
            $VL_ICMS,                                                                   # Valor do ICMS
            $COD_INF = '',                                                              # Cod da info complementar doc fiscal (campo 02 do Registro 0450)
            $VL_PIS,                                                                    # Valor do PIS/Pasep
            $VL_COFINS,                                                                 # Valor do Cofins
            $CHV_DOCe = $T013['T013_Chave_Acesso_Nfe'],                                 # Chave NFCom
            $FIN_DOCe = $finalidade[$T013['T013_Flag_Finalidade_NFe']],                 # 0 - NFCom Normal / 3 - NFCom de Substituição / 4 - NFCom de Ajuste
            $TIP_FAT = '0',                                                             # 0 - Faturamento Normal / 1 - Faturamento centralizado / 2 - Cofaturamento
            $COD_MOD_DOC_REF = '',                                                      # Cod modelo doc fiscal referenciado (Tabela 4.1.1)
            $CHV_DOCe_REF = '',                                                         # Chave da NF referenciada
            $HASH_DOC_REF = '',                                                         # Cod autenticação digital do registro (campo 36 do registro do Arquivo tipo mestre de documento fiscal, conforme definido no Convênio 115/2003)
            $SER_DOC_REF = '',                                                          # Serie doc fiscal referenciado
            $NUM_DOC_REF = '',                                                          # Num doc fiscal referenciado
            $MES_DOC_REF = '',                                                          # Mes e ano da emissão do doc fiscal referenciado
            $COD_MUN_DEST = $T013['D020_Codigo_IBGE'],                                  # Cod do municipio do destinatario conforme a tabela do IBGE
            $DED = '',                                                                  # Deduções
        );

        $D700 = str_replace('|', '', $D700);
		$D700 = implode("|", $D700);
		$layout .= '|' . $D700 . '|' . _SALTO_LINHA;
        $contador_bloco_D++;

        # REGISTRO D730
        $sql = "SELECT
                    T014_Flag_Situacao_Tributaria,
                    D006_Codigo_CFOP,
                    SUM(T014_Valor_Base_ICMS) AS T014_Valor_Base_ICMS,
                    T014_Aliquota_ICMS,
                    SUM(T014_Valor_ICMS) AS T014_Valor_ICMS,
                    SUM(T014_Valor_Base_ICMS / ((100 - T014_Percentual_Reducao_ICMS) / 100)) AS T014_Valor_Base_ICMS_Sem_Reducao,
                    (SUM(T014_Valor_Total_Custo) + SUM(T014_Valor_Despesas_Acessorias) - SUM(T014_Valor_Total_Desconto)) AS VL_OPR,
                    D188_Codigo_Observacao,
                    D188_Descricao_Observacao
                FROM T014
                    LEFT JOIN T013 ON T013_Id = T014_T013_Id
                    LEFT JOIN D006 ON D006_Id = T014_D006_Id
                    LEFT JOIN (
                        SELECT
                            T297_T013_Id,
                            D188_Codigo_Observacao,
                            D188_Descricao_Observacao
                        FROM T297
                            LEFT JOIN D188 ON D188_Id = T297_D188_Id
                        WHERE 
                            T297_T013_Id = '{$T013['T013_Id']}' 
                        LIMIT 1
                    ) AS observacoes ON T297_T013_Id = T013_Id
                WHERE
                    T013_Id = '{$T013['T013_Id']}'
                GROUP BY
                    T014_Flag_Situacao_Tributaria, D006_Codigo_CFOP, T014_Aliquota_ICMS";
        
        $sqlT014 = mysql_query($sql);
        while ($T014 = mysql_fetch_array($sqlT014)) {

            //o valor de VL_RED_BC só pode ser preenchido se os dois últimos dígitos do campo CST_ICMS forem iguais a 20, 70 ou 90.
            $st = substr($T014['T014_Flag_Situacao_Tributaria'],-2);
            $VL_RED_BC = ($st == '20' || $st == '70' || $st == '90') ? formataDecimal($T014['T014_Valor_Base_ICMS_Sem_Reducao'] - $T014['T014_Valor_Base_ICMS']) : '0,00';
            
            $D730 = array(
                $REG = 'D730',
                $CST_ICMS = $T014['T014_Flag_Situacao_Tributaria'],
                $CFOP = $T014['D006_Codigo_CFOP'],
                $ALIQ_ICMS = formataDecimal($T014['T014_Aliquota_ICMS']),
                $VL_OPR = formataDecimal($T014['VL_OPR']),
                $VL_BC_ICMS = formataDecimal($T014['T014_Valor_Base_ICMS']),
                $VL_ICMS = formataDecimal($T014['T014_Valor_ICMS']),
                $VL_RED_BC,
                $COD_OBS = $T014['D188_Codigo_Observacao'],
            );

            if(!empty($T014['D188_Codigo_Observacao'])) {
                insertRegistro0460($T014['D188_Codigo_Observacao'], $T014['D188_Descricao_Observacao']);
            }

            $D730 = implode("|", $D730);
            $layout .= '|' . $D730 . '|' . _SALTO_LINHA;			
            $contador_bloco_D++; 
        }
    }
}

# REGISTRO D990
$D990 = array(
    $REG = 'D990',
    $QTD_LIN_C = $contador_bloco_D + 1
);
$D990 = implode("|", $D990);
$layout .= '|' . $D990 . '|' . _SALTO_LINHA;

$linhas_bloco_C = $QTD_LIN_C;