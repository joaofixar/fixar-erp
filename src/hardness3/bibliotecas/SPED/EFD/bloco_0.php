<?php
namespace hardness;
global $g;
/*
# BLOCO 0 (ZERO) # INFORMAÇÕES PRINCIPAIS #
REG	   OCOR    OBRIG    DESCRIÇÃO																	
0000	1		O		Abertura do Arquivo Digital e Identificação da Pessoa Jurídica
0001	1		O		Abertura do Bloco 0
0005	1		OC 		Dados complementares da entidade
0015	V 		OC 	 	Dados do contribuinte substituto
0100	V		OC		Dados do Contabilista
0110	1		O		Regimes de Apuração da Contribuição Social e de Apropriação de Crédito
0111	1:1		O 		Tabela de Receita Bruta Mensal para Fins de Rateio de Créditos Comuns
0120	V		OC 		Identificação de Períodos Dispensados da Escrituração Digital
0140	V		O		Tabela de Cadastro de Estabelecimento
0145	1:1		OC		Regime de Apuração da Contribuição Previdenciária sobre a Receita Bruta
0150	1:N		OC		Tabela de Cadastro do Participante
0175    1:N     OC 		Alterações na Tabela de Cadastro do Participante  
0190	1:N		OC		Identificação das Unidades de Medida
0200	1:N		OC		Tabela de Identificação do Item (Produtos e Serviços)
0205	1:N		OC		Alteração do Item
0220   	1:N     OC 		Fatores de Conversão de unidades
0400	1:N		OC		Tabela de Natureza da Operação/ Prestação
0450	1:N		OC		Tabela de Informação Complementar do Documento Fiscal
0460	1:N 	OC 		Tabela de observações do lançamento fiscal
0500	V		OC 		Plano de Contas Contábeis – Contas Informadas
0600	V		OC		Centro de Custos
0990	1		O   	Encerramento do Bloco 0
*/
//Verifica se existe alguma atividade comercial no periodo
// $T007 = mysql_query("SELECT * FROM T007 WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' AND T007_C004_Id = '{$g['empresaAtual']}' AND T007_Flag_Cancelada != 'S'");

$layoutBloco0 = '';

$movimentacoes = mysql_query("SELECT 
							C004_Id,
							COUNT(*) AS Movimentacoes
							FROM 
							(SELECT
									T007_Id AS Id,
									T007_C004_Id AS C004_Id
							   FROM T007 
						  LEFT JOIN T007A ON T007A_T007_Id = T007_Id
						  LEFT JOIN D006 ON D006_Id = T007_D006_Id
							  WHERE T007_Data_Emissao >= '{$dataInicial}' 
								AND T007_Data_Emissao <= '{$dataFinal}' 
								AND T007_C004_Id IN ($matrizEFiliais) 
								AND T007_Flag_Cancelada != 'S' 
								AND T007_Numero_Protocolo_Nfe > 0
								AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								AND ((T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0) OR ((D006_Situacao_Tributaria_PIS = 06 OR D006_Situacao_Tributaria_PIS = 08 OR D006_Situacao_Tributaria_PIS = 09) OR (D006_Situacao_Tributaria_COFINS = 06 OR D006_Situacao_Tributaria_COFINS = 08 OR D006_Situacao_Tributaria_COFINS = 09)))
								GROUP BY T007_Id
							UNION 
								SELECT 
										T013_Id AS Id,
										T013_C004_Id AS C004_Id
								FROM T013 
								LEFT JOIN T013A ON T013A_T013_Id = T013_Id
								WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
								AND {$dataNFRecebida} <= '{$dataFinalHora}' 
								AND T013_C004_Id IN ($matrizEFiliais) AND T013_Flag_Cancelada != 'S' 
								AND T013_Numero_Protocolo_Nfe > 0
								AND T013_Flag_Estoque_Finalizado = 'S'
								AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
								GROUP BY T013_Id
							) as notas
						GROUP BY C004_Id");

$C004 = mysql_query("SELECT * FROM C004 
	LEFT JOIN D018 ON D018_Id = C004_D018_Id 
	LEFT JOIN D020 ON D020_Id = C004_D020_Id
	WHERE C004_Id = '{$matriz}'");
$C004 = mysql_fetch_array($C004);

$SUFRAMA = (strlen($C004['C004_Numero_Suframa']) == 9) ? $C004['C004_Numero_Suframa'] : ''; 

# REGISTRO 0000
$R0000 = array(																#TAM
	$REG = '0000', 															#004* 	
	$COD_VER = '006',														#003* 	Codigo do Layout
	$tipoEFD,																#001* 	Tipo de Escrituração (0 Original / 1 Retificadora)
	$IND_SIT_ESP,															#001* 	Indicador de situação especial (0 Abertura/1 Cisão/2 Fusão/3 Incorporação/4 Encerramento)
	$NUM_REC_ANTERIOR,														#041* 	Número do Recibo da Escrituração anterior a ser retificada
	formataData($dataInicial),												#008* 	Data inicial das informações
	formataData($dataFinal),												#008* 	Data final das informações
	$NOME = $C004['C004_Nome_Empresa'],										#100 	Nome empresarial da pessoa jurídica
	$CNPJ = removerCharEspecial($C004['C004_Cnpj']),								#014* 	CNPJ do estabelecimento
	$UF = $C004['D018_UF'],													#002* 	UF Pessoa Juridica
	$COD_MUN = $C004['D020_Codigo_IBGE'],									#007* 	Código do município do domicílio fiscal da pessoa jurídica
	$SUFRAMA,																#009* 	Inscrição da pessoa jurídica na Suframa
	$IND_NAT_PJ = $C004['C004_Flag_Natureza_Pessoa_Juridica'],				#002* 	Natureza da pessoa jurídica (00 Pessoa jurídica em geral/01 Sociedade cooperativa/02 Entidade sujeita ao PIS/Pasep exclusivamente com base na Folha de Salários)
	$IND_ATIV = $C004['C004_Flag_Tipo_Atividade']							#001 	Indicador de tipo de atividade preponderante (0 Industrial ou equiparado a industrial/1 Prestador de serviços/2 Atividade de comércio/3 Atividade financeira/4 Atividade imobiliária/9 Outros)
);
$R0000 = str_replace('|', '', $R0000);
$R0000 = implode("|", $R0000);
$layoutBloco0 .= '|' . $R0000 . '|' . _SALTO_LINHA;

$IND_MOV = (mysql_num_rows($movimentacoes) > 0)? '0' : '1';
# REGISTRO 0001
$R0001 = array(																#TAM
	$REG = '0001',															#004* 	
	$IND_MOV																#001 	Indicador de movimento (0 Com dados / 1 Sem dados)
);
$R0001 = str_replace('|', '', $R0001);
$R0001 = implode("|", $R0001);
$layoutBloco0 .= '|' . $R0001 . '|' . _SALTO_LINHA;

if($IND_MOV == '0'){
	# REGISTRO 0100
	$C007 = mysql_fetch_array(mysql_query("SELECT * FROM C007 LEFT JOIN D020 ON C007_D020_Id = D020_Id WHERE C007_Id = '{$C007_Id_Contabilista}'"));
	
	$R0100 = array(																#TAM
		$REG = '0100',															#004*	
		$NOME =	$C007['C007_Nome'],												#100 	Nome do contabilista.
		$CPF = removerChar($C007['C007_Cpf']),									#011*	Número de inscrição do contabilista no CPF.
		$CRC =  trim(removerCharEspecial($C007['C007_Crc'])),							#015 	Número de inscrição do contabilista no Conselho Regional de Contabilidade.
		$CNPJ = removerChar($C007['C007_Cnpj']),								#014*	Número de inscrição do escritório de contabilidade no CNPJ, se houver.
		$CEP = removerChar($C007['C007_Cep']),									#008*	Código de Endereçamento Postal.
		$END = $C007['C007_Endereco'],											#060 	Logradouro e endereço do imóvel.
		$NUM = $C007['C007_Numero'],											#	 	Número do imóvel.
		$COMPL = $C007['C007_Complemento'],										#060 	Dados complementares do endereço.
		$BAIRRO = $C007['C007_Bairro'],											#060 	Bairro em que o imóvel está situado.
		$FONE = removerChar($C007['C007_DDD_Telefone'].$C007['C007_Telefone']),	#10* 	Número do telefone.
		$FAX = '',																#10* 	Número do fax.
		$EMAIL = $C007['C007_Email'],											#	 	Endereço do correio eletrônico.
		$COD_MUN = $C007['D020_Codigo_IBGE']									#007*	Código do município, conforme tabela IBGE.
	);	
	$R0100 = implode("|", $R0100);
	$layoutBloco0 .= '|' . $R0100 . '|' . _SALTO_LINHA;

	// Setando essas informações la no inicio (arquivo fiscal) porque serão utilizados em outros blocos e o 0 e o ultimo a ser chamado
	//$COD_INC_TRIB_0110 = $C004['C004_Flag_Incidencia_Tributaria'];
	//$IND_APRO_CRED = ($C004['C004_Flag_Metodo_Apropriacao_Credito'] > 0) ?  $C004['C004_Flag_Metodo_Apropriacao_Credito'] : '';
	//$IND_REG_CUM = ($COD_INC_TRIB_0110 == '2') ? '9' : '';
	# REGISTRO 0110
	$R0110 = array(																#TAM
		$REG = '0110',															#004*
		$COD_INC_TRIB_0110,														#001* 	Indicador da incidência tributária (1 Ñ Cumulativo/2 Cumulativo/3 Ambos)
		$IND_APRO_CRED,															#001* 	Indicador de método de apropriação de créditos CASO Ñ CUMULATIVO (1 Método de Apropriação Direta/2 Método de Rateio Proporcional (Receita Bruta))
		$COD_TIPO_CONT = $C004['C004_Flag_Tipo_Contribuicao'],					#001* 	Indicador do Tipo de Contribuição Apurada (1 – Contribuição Exclusivamente a Alíquota Básica/2 Contribuição a Alíquotas Específicas (Diferenciadas e/ou por Unidade de Medida de Produto))
		$IND_REG_CUM															#001* 	Indicador do critério de escrituração e apuração adotado, no caso de incidência exclusivamente no regime cumulativo (1 Regime de Caixa – Escrituração consolidada/2 Regime de Competência - Escrituração consolidada/9 Regime de Competência - Escrituração detalhada)
	);
	$R0110 = str_replace('|', '', $R0110);
	$R0110 = implode("|", $R0110);
	$layoutBloco0 .= '|' . $R0110 . '|' . _SALTO_LINHA;

	if(($IND_APRO_CRED == '2' && ($COD_INC_TRIB_0110 == '1' || $COD_INC_TRIB_0110 == '3')) && ($COD_INC_TRIB_0110 != '2' && $IND_APRO_CRED != '1')){
		# REGISTRO 0111
		$R0111 = array(															#TAM		
			$REG = '0111', 														#004*
			$REC_BRU_NCUM_TRIB_MI = '0000',										#		Receita Bruta Não-Cumulativa - Tributada no Mercado Interno
			$REC_BRU_NCUM_NT_MI = '0000',										#		Receita Bruta Não-Cumulativa – Não Tributada no Mercado Interno (Vendas com suspensão, alíquota zero, isenção e sem incidência das contribuições)
			$REC_BRU_NCUM_EXP = '0000',											#		Receita Bruta Não-Cumulativa – Exportação
			$REC_BRU_CUM = '0000',												#		Receita Bruta Cumulativa
			$REC_BRU_TOTAL = '0000',											#		Receita Bruta Total
		);
		$R0111 = str_replace('|', '', $R0111);
		$R0111 = implode("|", $R0111);
		$layoutBloco0 .= '|' . $R0111 . '|' . _SALTO_LINHA;
	}
	if($mes == '12' || isset($MES_DISPENSA)){
		# REGISTRO 0120
		$R0120 = array(																#TAM
			$REG = '0120',															#004*
			$MES_DISPENSA,						 									#006* 	Mês de referência do  ano-calendário da escrituração, dispensada da entrega.
			$INF_COMP = ''				 	   										#090 	Informação complementar do registro.
		);
		$R0120 = str_replace('|', '', $R0120);
		$R0120 = implode("|", $R0120);
		$layoutBloco0 .= '|' . $R0120 . '|' . _SALTO_LINHA;
	}

	while ($mMovimentacoes = mysql_fetch_array($movimentacoes)) {
		
		$empresa = $mMovimentacoes['C004_Id'];

		$C004 = mysql_query("SELECT * FROM C004 
									LEFT JOIN D018 ON D018_Id = C004_D018_Id 
									LEFT JOIN D020 ON D020_Id = C004_D020_Id
									WHERE C004_Id = '{$empresa}'");
		$C004 = mysql_fetch_array($C004);
	
	# REGISTRO 0140
	$R0140 = array(																#TAM
		$REG = '0140',															#004*	
		$COD_EST = $C004['C004_Id'],											#060	Código de identificação do estabelecimento	
		$NOME = $C004['C004_Nome_Empresa'],										#100	Nome empresarial do estabelecimento
		$CNPJ = removerCharEspecial($C004['C004_Cnpj']),								#014*	Número de inscrição do estabelecimento no CNPJ.	
		$UF = $C004['D018_UF'],													#002*	Sigla da unidade da federação do estabelecimento.
		$IE = '',																#014 	Inscrição Estadual do estabelecimento, se contribuinte de ICMS.
		$COD_MUN = $C004['D020_Codigo_IBGE'],									#007*	Código do município do domicílio fiscal do estabelecimento, conforme a tabela IBGE		
		$IM = '',																#		Inscrição Municipal do estabelecimento, se contribuinte do ISS.
		$SUFRAMA = ''															#009* 	Inscrição do estabelecimento na Suframa		
	);
	$R0140 = str_replace('|', '', $R0140);
	$R0140 = implode("|", $R0140);
	$layoutBloco0 .= '|' . $R0140 . '|' . _SALTO_LINHA;

	$receitaBrutaTotal = mysql_query("SELECT SUM(T007_Valor_Total_Produtos_Sem_Desconto) as receitaTotal FROM T007 
			LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' AND T007_C004_Id = '{$empresa}' AND T007_Flag_Cancelada != 'S' AND D006_Flag_Entrada_Saida = 'S'");
	$receitaBrutaTotal = mysql_fetch_array($receitaBrutaTotal);

	$receitaBruta = mysql_query("SELECT SUM(T007_Valor_Total_Produtos_Sem_Desconto) as receita FROM T007 
		LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' 
		AND D006_Flag_Isento_ICMS = 'N' AND D006_Flag_Isento_IPI = 'N'
		AND D006_Flag_Isento_PIS = 'N' AND D006_Flag_Isento_COFINS = 'N'
		AND D006_Flag_Entrada_Saida = 'S' 
			AND T007_C004_Id = '{$empresa}' 
		AND T007_Flag_Cancelada != 'S'");
	$receitaBruta = mysql_fetch_array($receitaBruta);

	# REGISTRO 0145
	$R0145 = array(																#TAM
		$REG = '0145',															#004* 		
		$COD_INC_TRIB = '1',													#001*	Indicador da incidência tributária 	(1 Contribuição Previdenciária apurada no período, exclusivamente com base na Receita Bruta/ 2 Contribuição Previdenciária apurada no período, com base na Receita Bruta e com base nas Remunerações pagas)		
		$VL_REC_TOT = formataDecimal($receitaBrutaTotal['receitaTotal']),		#-		Valor da Receita Bruta Total da Pessoa Jurídica no Período			
		$VL_REC_ATIV = formataDecimal($receitaBruta['receita']),				#-		Valor da Receita Bruta da(s) Atividade(s) Sujeita(s) à Contribuição Previdenciária sobre a Receita Bruta			
		$VL_REC_DEMAIS_ATIV = '',												#-		Valor da Receita Bruta da(s) Atividade(s) Sujeita(s) à Contribuição Previdenciária sobre a Remuneração
		$INFO_COMPL = ''														#-		Informação complementar		
	);
	$R0145 = str_replace('|', '', $R0145);
	$R0145 = implode("|", $R0145);
	//$layoutBloco0 .= '|' . $R0145 . '|' . _SALTO_LINHA;

	// $sqlT007_D024 = mysql_query("SELECT D024_Id, D024_Nome_Empresa, D030_Codigo_Pais, D020_Codigo_IBGE, D024_Endereco, D024_Numero, D024_Complemento, D024_Bairro, D024_Flag_Pessoa_Fisica_Juridica, D024_Cnpj,D024_Cpf,D024_Inscricao_Estadual FROM T007 LEFT JOIN D024 ON D024_Id = T007_D024_Id LEFT JOIN D020 ON D020_Id = D024_D020_Id LEFT JOIN D030 ON D030_Id = D024_D030_Id WHERE T007_Data_Emissao >= '{$dataInicial}' AND T007_Data_Emissao <= '{$dataFinal}' AND T007_C004_Id = '{$g['empresaAtual']}'  GROUP BY D024_Id");	
	$sqlD024 = mysql_query("SELECT
							D024_Id,
							D024_Nome_Empresa,
							D030_Codigo_Pais,
							D020_Codigo_IBGE,
							D024_Endereco,
							D024_Numero,
							D024_Complemento,
							D024_Bairro,
							D024_Flag_Pessoa_Fisica_Juridica,
							D024_Cnpj,
							D024_Cpf,
							D024_Inscricao_Estadual
						FROM (	
								SELECT
									D024_Id,
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual
								FROM
									T007
									LEFT JOIN D024 ON D024_Id = T007_D024_Id
									LEFT JOIN D020 ON D020_Id = D024_D020_Id
									LEFT JOIN D030 ON D030_Id = D024_D030_Id
									LEFT JOIN T007A ON T007_Id = T007A_T007_Id
								WHERE
									T007_Data_Emissao >= '{$dataInicial}'
									AND T007_Data_Emissao <= '{$dataFinal}'
										AND T007_C004_Id = '{$empresa}'
									AND T007_Flag_Cancelada != 'S' 
									AND ((T007_Numero_Protocolo_Nfe > 0 AND T007_Flag_ACP IN('6','8')) OR (T007_Flag_ACP = '7' AND T007_Flag_Travar_Nota = 'S'))
									AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'									
									AND (T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0)
								GROUP BY
									D024_Id
							UNION
								SELECT
									D024_Id,
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual
									FROM 
										T013 
										LEFT JOIN D024 ON D024_Id = T013_D024_Id 
										LEFT JOIN D020 ON D020_Id = D024_D020_Id 
										LEFT JOIN D030 ON D030_Id = D024_D030_Id 
										LEFT JOIN T013A ON T013_Id = T013A_T013_Id
									WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
										AND {$dataNFRecebida} <= '{$dataFinalHora}' 
										AND T013_C004_Id = '{$empresa}' 
										AND T013_Flag_Cancelada != 'S' 																				
										AND T013_Flag_Estoque_Finalizado = 'S'
										AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo IN ('5','6','7','8','10','11'))     
										AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
										AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
									GROUP BY
										D024_Id
							UNION
								SELECT 
									D024_Id,
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual
								FROM T084
									LEFT JOIN D022 ON D022_Id = T084_D022_Id
									LEFT JOIN D024 ON D024_Cnpj = D022_Cnpj
									LEFT JOIN D020 ON D020_Id = D024_D020_Id
									LEFT JOIN D030 ON D030_Id = D024_D030_Id
								WHERE T084_Data_Lancamento >= '{$dataInicial}'
									AND T084_Data_Lancamento <= '{$dataFinal}'
										AND T084_C004_Id = '{$empresa}'  
									AND T084_Chave_Acesso_Cte > 0    
								GROUP BY D024_Id
						) as participantes");

	$mD024_0150 = array();

	while($T007_D024 = mysql_fetch_array($sqlD024)) {
		if($T007_D024['D024_Id'] <= 0 || empty($T007_D024['D024_Id'])){
			continue;
		}

		if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
			$T007_D024['D024_Cnpj'] = '';
			$T007_D024['D024_Cpf'] = '';
			$T007_D024['D024_Inscricao_Estadual'] = '';
		} else if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
			$T007_D024['D024_Cnpj'] = '';	
		} else if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
			$T007_D024['D024_Cpf'] = '';
		}
		$T007_D024['D024_Inscricao_Estadual'] = (trim($T007_D024['D024_Inscricao_Estadual']) == 'ISENTO') ? '' : $T007_D024['D024_Inscricao_Estadual'];		
		$complemento = removerEspacosDuplicados(substr(trim(adicionarEspape($T007_D024['D024_Complemento'])),0,60)); 

		#LOOP REGISTRO 0150
		$R0150 = array(																#TAM
			$REG = '0150',															#004*			
			$COD_PART = $T007_D024['D024_Id'],										#060	Código de identificação do participante no arquivo.		
			$NOME = trim($T007_D024['D024_Nome_Empresa']),							#100	Nome pessoal ou empresarial do participante.	
			$COD_PAIS = $T007_D024['D030_Codigo_Pais'],								#005	Código do país do participante, conforme a tabela indicada no item 3.2.1.		
			$CNPJ = removerCharEspecial($T007_D024['D024_Cnpj']),					#014*	CNPJ do participante.	
			$CPF = removerCharEspecial($T007_D024['D024_Cpf']),						#011*	CPF do participante.	
			$IE	= removerCharEspecial($T007_D024['D024_Inscricao_Estadual']),		#014	Inscrição Estadual do participante.
			$COD_MUN = $T007_D024['D020_Codigo_IBGE'],								#007*	Código do município, conforme a tabela IBGE		
			$SUFRAMA = '',															#009*	Número de inscrição do participante na Suframa		
			$END = trim($T007_D024['D024_Endereco']),								#060	Logradouro e endereço do imóvel	
			$NUM = $T007_D024['D024_Numero'],										#-		Número do imóvel
			$COMPL = $complemento,													#060	Dados complementares do endereço	
			$BAIRRO	= trim($T007_D024['D024_Bairro'])								#060	Bairro em que o imóvel está situado	
		);
		$R0150 = str_replace('|', '', $R0150);
		$R0150 = implode("|", $R0150);
		$layoutBloco0 .= '|' . $R0150 . '|' . _SALTO_LINHA;
		$mD024_0150[] = $T007_D024['D024_Id'];
	}

	// Segurança para ter lançando todos os registros no 0150
	// Popula o array no bloco A,C,D 
	for ($i = 0; $i < sizeof($array0150[$empresa]); $i++) {

		if (array_search($array0150[$empresa][$i], $mD024_0150) !== false) {
            continue;
        } elseif ($array0150[$empresa][$i] <= 0 || empty($array0150[$empresa][$i])) {
            continue;
        }

		$D024 = mysql_query("SELECT D024_Id,
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual
							   FROM D024 
			   			  LEFT JOIN D020 ON D020_Id = D024_D020_Id 
			   			  LEFT JOIN D030 ON D030_Id = D024_D030_Id 
				              WHERE D024_Id = '{$array0150[$empresa][$i]}'
							    AND D024_Id > 0
					       GROUP BY D024_Id"); 	

		$mD024 = mysql_fetch_array($D024);
		
		if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
			$mD024['D024_Cnpj'] = '';
			$mD024['D024_Cpf'] = '';
			$mD024['D024_Inscricao_Estadual'] = '';
		} else if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
			$mD024['D024_Cnpj'] = '';	
		} else if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
			$mD024['D024_Cpf'] = '';
		}
		$mD024['D024_Inscricao_Estadual'] = (trim($mD024['D024_Inscricao_Estadual']) == 'ISENTO') ? '' : $mD024['D024_Inscricao_Estadual'];		
		$complemento = removerEspacosDuplicados(substr(trim(adicionarEspape($mD024['D024_Complemento'])),0,60)); 

		#LOOP REGISTRO 0150
		$R0150 = array(															#TAM
			$REG = '0150',														#004*			
			$COD_PART = $mD024['D024_Id'],										#060	Código de identificação do participante no arquivo.		
			$NOME = trim($mD024['D024_Nome_Empresa']),							#100	Nome pessoal ou empresarial do participante.	
			$COD_PAIS = $mD024['D030_Codigo_Pais'],								#005	Código do país do participante, conforme a tabela indicada no item 3.2.1.		
			$CNPJ = removerCharEspecial($mD024['D024_Cnpj']),					#014*	CNPJ do participante.	
			$CPF = removerCharEspecial($mD024['D024_Cpf']),						#011*	CPF do participante.	
			$IE	= removerCharEspecial($mD024['D024_Inscricao_Estadual']),		#014	Inscrição Estadual do participante.
			$COD_MUN = $mD024['D020_Codigo_IBGE'],								#007*	Código do município, conforme a tabela IBGE		
			$SUFRAMA = '',														#009*	Número de inscrição do participante na Suframa		
			$END = trim($mD024['D024_Endereco']),								#060	Logradouro e endereço do imóvel	
			$NUM = $mD024['D024_Numero'],										#-		Número do imóvel
			$COMPL = $complemento,												#060	Dados complementares do endereço	
			$BAIRRO	= trim($mD024['D024_Bairro'])								#060	Bairro em que o imóvel está situado	
		);
		$R0150 = str_replace('|', '', $R0150);
		$R0150 = implode("|", $R0150);
		$layoutBloco0 .= '|' . $R0150 . '|' . _SALTO_LINHA;
		$mD024_0150[] = $mD024['D024_Id'];
	}
	
	$sqlD001 = mysql_query("SELECT
								D037_Unidade,
								D001_Codigo_Produto,
								D001_Descricao_Produto,
								D005_Classificacao_Fiscal,
								D001_Flag_Tipo_Item,
								D001_Id,
								D037_Id
							FROM
								(	SELECT
										D037_Unidade,
										D001_Codigo_Produto,
										D001_Descricao_Produto,
										D005_Classificacao_Fiscal,
										D001_Flag_Tipo_Item,
										D001_Id,
										D037_Id
									FROM
										T008
										LEFT JOIN T007 ON T007_Id = T008_T007_Id
										LEFT JOIN D009 ON D009_Id = T008_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D001 ON D001_Id = D049_D001_Id
										LEFT JOIN D037 ON D037_Id = D001_D037_Id
										LEFT JOIN D005 ON D005_Id = D001_D005_Id
										LEFT JOIN T007A ON T007_Id = T007A_T007_Id      
									WHERE
										T007_Data_Emissao >= '{$dataInicial}'
										AND T007_Data_Emissao <= '{$dataFinal}'
											AND T007_C004_Id = '{$empresa}'
										AND T007_Flag_Cancelada != 'S'										
										AND ((T007_Numero_Protocolo_Nfe > 0 AND T007_Flag_ACP IN('6','8')) OR (T007_Flag_ACP = '7' AND T007_Flag_Travar_Nota = 'S'))
										AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
										AND (T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0)									
									GROUP BY
										D001_Id
								UNION
									SELECT      
										D037_Unidade,
										D001_Codigo_Produto,
										D001_Descricao_Produto,
										D005_Classificacao_Fiscal,
										D001_Flag_Tipo_Item,
										D001_Id,
										D037_Id
									FROM
										T014
										LEFT JOIN T013 ON T013_Id = T014_T013_Id
										LEFT JOIN D009 ON D009_Id = T014_D009_Id
										LEFT JOIN D049 ON D049_Id = D009_D049_Id
										LEFT JOIN D001 ON D001_Id = D049_D001_Id
										LEFT JOIN D037 ON D037_Id = D001_D037_Id
										LEFT JOIN D005 ON D005_Id = D001_D005_Id 
										LEFT JOIN T013A ON T013_Id = T013A_T013_Id      
									WHERE
										{$dataNFRecebida} >= '{$dataInicialHora}'
										AND {$dataNFRecebida} <= '{$dataFinalHora}'
										AND T013_C004_Id = '{$empresa}'
										AND T013_Flag_Cancelada != 'S'										
										AND T013_Flag_Estoque_Finalizado = 'S'										
										AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
										AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo IN ('5','10'))
										AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'     
									GROUP BY
									D001_Id 
								) AS produtos ORDER BY D001_Codigo_Produto");
	$registro0200 = '';
	$mD001_0200 = array();
	while($T007_D001 = mysql_fetch_array($sqlD001)){

		# REGISTRO 0200
		$R0200 = array(																#TAM
			$REG = '0200',															#004				
			$COD_ITEM = $T007_D001['D001_Codigo_Produto'],							#060	Código do item			
			$DESCR_ITEM = $T007_D001['D001_Descricao_Produto'],						#-		Descrição do item	
			$COD_BARRA = ''					,										#-		Representação alfanumérico do código de barra do produto, se houver.	
			$COD_ANT_ITEM = '',														#060	Código anterior do item com relação à última informação apresentada.			
			$UNID_INV = $T007_D001['D037_Unidade'],									#006	Unidade de medida utilizada na quantificação de estoques.  		
			$TIPO_ITEM = $T007_D001['D001_Flag_Tipo_Item'],							#002*	Tipo do item (00 Mercadoria para Revenda/01 Matéria-Prima/02 Embalagem/03 Produto em Processo/04 Produto Acabado/05 Subproduto/06 Produto Intermediário/07 Material de Uso e Consumo/08 Ativo Imobilizado/09 Serviços/10 Outros insumos/99 Outras)		
			$COD_NCM = $T007_D001['D005_Classificacao_Fiscal'],						#008	Código da Nomenclatura Comum do Mercosul		
			$EX_IPI = '',															#003	Código EX, conforme a TIPI	
			$COD_GEN = substr($T007_D001['D005_Classificacao_Fiscal'],0,2),															#002*	Código do gênero do item, conforme a Tabela 4.2.1.		
			$COD_LST = '',															#004	Código do serviço conforme lista do Anexo I da Lei Complementar Federal nº 116/03.		
			$ALIQ_ICMS	= ''														#006	Alíquota de ICMS aplicável ao item nas operações internas												
		);
		$R0200 = str_replace('|', '', $R0200);
		$R0200 = implode("|", $R0200);
		$registro0200 .= '|' . $R0200 . '|' . _SALTO_LINHA;
		$mD001_0200[] = $T007_D001['D001_Id'];
		verificaRegistro0190($T007_D001['D037_Id'], $empresa);

	}

	for ($i = 0; $i < sizeof($array0200[$empresa]); $i++) {
	
		if (array_search($array0200[$empresa][$i], $mD001_0200) !== false) {
			continue;
		} elseif ($array0200[$empresa][$i] <= 0 || empty($array0200[$empresa][$i])) {
			continue;
		}

		$D001 = mysql_query("SELECT D037_Unidade,
									D001_Codigo_Produto,
									D001_Descricao_Produto,
									D005_Classificacao_Fiscal,
									D001_Flag_Tipo_Item,
									D001_Id,
									D037_Id
							   FROM	
							   		D001
						  LEFT JOIN D005 ON D005_Id = D001_D005_Id  
  						  LEFT JOIN D037 ON D037_Id = D001_D037_Id    
						  	  WHERE	
							  		D001_Id = '{$array0200[$empresa][$i]}'
							    AND D001_Id > 0
						   GROUP BY D001_Id");

		$mD001 = mysql_fetch_array($D001);

		# REGISTRO 0200
		$R0200 = array(																#TAM
			$REG = '0200',															#004				
			$COD_ITEM     = $mD001['D001_Codigo_Produto'],						#060	Código do item			
			$DESCR_ITEM   = $mD001['D001_Descricao_Produto'],					#-		Descrição do item	
			$COD_BARRA    = '',  	  												#-		Representação alfanumérico do código de barra do produto, se houver.	
			$COD_ANT_ITEM = '',														#060	Código anterior do item com relação à última informação apresentada.			
			$UNID_INV 	  = $mD001['D037_Unidade'],								#006	Unidade de medida utilizada na quantificação de estoques.  		
			$TIPO_ITEM 	  = $mD001['D001_Flag_Tipo_Item'],						#002*	Tipo do item (00 Mercadoria para Revenda/01 Matéria-Prima/02 Embalagem/03 Produto em Processo/04 Produto Acabado/05 Subproduto/06 Produto Intermediário/07 Material de Uso e Consumo/08 Ativo Imobilizado/09 Serviços/10 Outros insumos/99 Outras)		
			$COD_NCM 	  = $mD001['D005_Classificacao_Fiscal'],				#008	Código da Nomenclatura Comum do Mercosul		
			$EX_IPI 	  = '',														#003	Código EX, conforme a TIPI	
			$COD_GEN      = substr($mD001['D005_Classificacao_Fiscal'],0,2),	#002*	Código do gênero do item, conforme a Tabela 4.2.1.		
			$COD_LST 	  = '',														#004	Código do serviço conforme lista do Anexo I da Lei Complementar Federal nº 116/03.		
			$ALIQ_ICMS	  = ''														#006	Alíquota de ICMS aplicável ao item nas operações internas												
		);
		$R0200 = str_replace('|', '', $R0200);
		$R0200 = implode("|", $R0200);
		$registro0200 .= '|' . $R0200 . '|' . _SALTO_LINHA;
		$mD001_0200[] = $mD001['D001_Id'];	
		verificaRegistro0190($mD001['D037_Id'], $empresa);
	}

	$sqlT007_D037 = mysql_query("SELECT D037_Unidade, 
										D037_Descricao_Unidade
								   	  FROM (
											SELECT 	D037_Unidade, 
													D037_Descricao_Unidade
											   FROM T008
		  								  LEFT JOIN T007 ON T007_Id = T008_T007_Id
		  								  LEFT JOIN D009 ON D009_Id = T008_D009_Id
		  								  LEFT JOIN D049 ON D049_Id = D009_D049_Id
		  								  LEFT JOIN D001 ON D001_Id = D049_D001_Id
		  								  LEFT JOIN D037 ON D037_Id = D001_D037_Id 
										  LEFT JOIN T007A ON T007_Id = T007A_T007_Id     
		                                      WHERE T007_Data_Emissao >= '{$dataInicial}'
		                                        AND T007_Data_Emissao <= '{$dataFinal}'
												AND T007_C004_Id = '{$empresa}'
		                                        AND T007_Flag_Cancelada != 'S'
		                                        AND ((T007_Numero_Protocolo_Nfe > 0 AND T007_Flag_ACP IN('1', '6')) OR (T007_Flag_ACP = '7' AND T007_Flag_Travar_Nota = 'S'))
												AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
												AND (T007_Valor_Total_PIS > 0 or T007_Valor_Total_COFINS > 0)
										UNION
											SELECT D037_Unidade, 
											       D037_Descricao_Unidade
											  FROM T014
		  								 LEFT JOIN T013 ON T013_Id = T014_T013_Id
		  								 LEFT JOIN D009 ON D009_Id = T014_D009_Id
		  								 LEFT JOIN D049 ON D049_Id = D009_D049_Id
		  								 LEFT JOIN D001 ON D001_Id = D049_D001_Id
		  								 LEFT JOIN D037 ON D037_Id = D001_D037_Id
										 LEFT JOIN T013A ON T013_Id = T013A_T013_Id      
										  	 WHERE {$dataNFRecebida} >= '{$dataInicialHora}'
		  									   AND {$dataNFRecebida} <= '{$dataFinalHora}'
											   AND T013_C004_Id = '{$empresa}'
		                                       AND T013_Flag_Cancelada != 'S'		  
		                                       AND T013_Flag_Estoque_Finalizado = 'S'
		                                       AND (T013_Valor_Total_PIS > 0 or T013_Valor_Total_COFINS > 0)
		                                       AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo IN ('5','10'))     
											   AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
										) AS unidades
										GROUP BY D037_Unidade
										ORDER BY D037_Unidade");

	$mD037_D190 = array();
	$registro0190 = '';
	while($T007_D037_Id = mysql_fetch_array($sqlT007_D037)){

		if($T007_D037_Id['D037_Id'] <= 0 || empty($T007_D037_Id['D037_Id'])){
			continue;
		}
		#LOOP REGISTRO 0190
		$R0190 = array(																#TAM
			$REG = '0190',															#004*	
			$UNID = $T007_D037_Id['D037_Unidade'],									#006	Código da unidade de medida
			$DESCR = $T007_D037_Id['D037_Descricao_Unidade']						#-		Descrição da unidade de medida
		);
		$R0190 = str_replace('|', '', $R0190);
		$R0190 = implode("|", $R0190);
			
		$registro0190 .= '|' . $R0190 . '|' . _SALTO_LINHA;
		$mD037_D190[] = $T007_D037_Id['D037_Id'];	
	}

	for($i=0;$i<sizeof($array0190[$empresa]);$i++){

		if (array_search($array0190[$empresa][$i], $mD037_D190) !== false) {
			continue;
		} elseif ($array0190[$empresa][$i] <= 0 || empty($array0190[$empresa][$i])) {
			continue;
		}

		$D037 = mysql_query("SELECT D037_Unidade, 
									D037_Descricao_Unidade,
									D037_Id 
							   FROM D037
							  WHERE D037_Id = '{$array0190[$empresa][$i]}'");
		
		$mD037 = mysql_fetch_array($D037);

			#LOOP REGISTRO 0190
			$R0190 = array(																#TAM
				$REG = '0190',															#004*	
				$UNID = $mD037['D037_Unidade'],									#006	Código da unidade de medida
				$DESCR = $mD037['D037_Descricao_Unidade']						#-		Descrição da unidade de medida
			);

			$R0190 = str_replace('|', '', $R0190);
			$R0190 = implode("|", $R0190);
				
			$registro0190 .= '|' . $R0190 . '|' . _SALTO_LINHA;
			$mD037_D190[] = $T007_D037_Id['D037_Id'];	

	}

	$layoutBloco0 = $layoutBloco0.$registro0190.$registro0200;


	$mD006_0400 = array();
	for($i=0;$i<sizeof($array0400[$empresa]);$i++){
		$D006 = mysql_query("SELECT D006_Codigo_CFOP, 
									D006_Descricao 
							   FROM D006 
							  WHERE D006_Id='{$array0400[$empresa][$i]}'");

		$mD006 = mysql_fetch_array($D006);
		$mD006['D006_Codigo_CFOP'] = removerCharEspecial($mD006['D006_Codigo_CFOP']);

		if (array_search($mD006['D006_Codigo_CFOP'], $mD006_0400) !== false) {
			continue;
		} 

		#LOOP REGISTRO 0400
		$R0400 = array(									#TAM
			$REG = '0400',								#004*	
			$COD_NAT = $mD006['D006_Codigo_CFOP'],		#010	Código da natureza da operação/prestação
			$DESCR_NAT = $mD006['D006_Descricao']		#-		Descrição da natureza da operação/prestação
		);
		$R0400 = implode("|", $R0400);
		$layoutBloco0 .= '|' . $R0400 . '|' . _SALTO_LINHA;	
		$mD006_0400[] = $mD006['D006_Codigo_CFOP'];
	}

	# REGISTRO 0205
	/*$R0205 = array(															#TAM
		$REG = '0205',															#004*	
		$DESCR_ANT_ITEM	= 'RODA MOMO',											#-		Descrição anterior do item	
		$DT_INI	= '10112012',													#008*	Data inicial de utilização da descrição do item
		$DT_FIM = '10102012',													#008*	Data final de utilização da descrição do item
		$COD_ANT_ITEM = '00'													#060	Código anterior do item com relação à última informação apresentada.
	);
	$R0205 = str_replace('|', '', $R0205);
	$R0205 = implode("|", $R0205);
	//$layoutBloco0 .= '|' . $R0205 . '|' . _SALTO_LINHA;

	# REGISTRO 0450
	$R0450 = array(																#TAM
		$REG = '0450',															#004*
		$COD_INF = '',															#006	Código da informação complementar do documento fiscal. 	
		$TXT = ''																#-		Texto livre da informação complementar existente no documento fiscal.
	);
	$R0450 = str_replace('|', '', $R0450);
	$R0450 = implode("|", $R0450);
	//$layoutBloco0 .= '|' . $R0450 . '|' . _SALTO_LINHA;*/
	}
	// Aqui acaba o loop do registro 0140 por empresa

	//Loop das Contas e dos Grupos de Contas
	// $sqlD014 = mysql_query("SELECT * FROM D014 WHERE D014_Flag_Ativo = 'S'");
	$sqlD014 = mysql_query("SELECT D014_Id,
								   D014_Data_Inclusao,
								   D014_Codigo_Plano_Conta_Fiscal,
								   D014_SubConta,
								   D014_Mascara_Plano_Conta_Fiscal,	
								   D014_Codigo_Natureza_Conta_Corrente,
								   D014_Codigo_Indicador_Conta_Corrente,
								   SUM(Empresas) AS QtdeEmpresas,
								   C004_Cnpj							
							FROM (
									SELECT D014_Id,
										   D014_Data_Inclusao,
										   D014_Codigo_Plano_Conta_Fiscal,
										   D014_SubConta,
										   D014_Mascara_Plano_Conta_Fiscal,
										   D014_Codigo_Natureza_Conta_Corrente,
										   D014_Codigo_Indicador_Conta_Corrente,
										   COUNT(DISTINCT C004_Id) AS Empresas,
										   C004_Cnpj
									  FROM T008
								 LEFT JOIN T007 ON T007_Id = T008_T007_Id
								 LEFT JOIN D006 ON D006_Id = T008_D006_Id 
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id    
								 LEFT JOIN C004 ON C004_Id = T007_C004_Id
								 LEFT JOIN T007A ON T007_Id = T007A_T007_Id										
									 WHERE T007_Data_Emissao >= '{$dataInicial}'
									   AND T007_Data_Emissao <= '{$dataFinal}'
									   AND T007_C004_Id IN ($matrizEFiliais) 
									   AND T007_Flag_Cancelada != 'S'
									   AND ((T007_Numero_Protocolo_Nfe > 0 AND T007_Flag_ACP IN('6','8')) OR (T007_Flag_ACP = '7' AND T007_Flag_Travar_Nota = 'S'))									
									   AND (T007_Valor_Total_PIS > 0 OR T007_Valor_Total_COFINS > 0)
									   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
									   AND D014_Flag_Ativo = 'S'
								  GROUP BY D014_Id
							UNION
									SELECT D014_Id,
										   D014_Data_Inclusao,
										   D014_Codigo_Plano_Conta_Fiscal,
										   D014_SubConta,
										   D014_Mascara_Plano_Conta_Fiscal,
										   D014_Codigo_Natureza_Conta_Corrente,
										   D014_Codigo_Indicador_Conta_Corrente,
										   COUNT(DISTINCT C004_Id) AS Empresas,
										   C004_Cnpj										
									  FROM T014
								 LEFT JOIN T013 ON T013_Id = T014_T013_Id 
								 LEFT JOIN D006 ON D006_Id = T014_D006_Id 
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id 
								 LEFT JOIN C004 ON C004_Id = T013_C004_Id
								 LEFT JOIN T013A ON T013_Id = T013A_T013_Id											
								  	 WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
									   AND {$dataNFRecebida} <= '{$dataFinalHora}' 
									   AND T013_C004_Id  IN ($matrizEFiliais) 
									   AND T013_Flag_Cancelada != 'S'
									   AND T013_Numero_Protocolo_Nfe > 0
									   AND T013_Tipo = 1		
									   AND T013_Flag_Estoque_Finalizado = 'S'	
									   AND (T013_Valor_Total_PIS > 0 OR T013_Valor_Total_COFINS > 0)	 
									   AND D014_Flag_Ativo = 'S'
									   AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								  GROUP BY D014_Id
							UNION	
									SELECT D014_Id,
										   D014_Data_Inclusao,
										   D014_Codigo_Plano_Conta_Fiscal,
										   D014_SubConta,
										   D014_Mascara_Plano_Conta_Fiscal,
										   D014_Codigo_Natureza_Conta_Corrente,
										   D014_Codigo_Indicador_Conta_Corrente,	
										   COUNT(DISTINCT C004_Id) AS Empresas,
										   C004_Cnpj										
									  FROM T084
								 LEFT JOIN D022 ON D022_Id = T084_D022_Id
								 LEFT JOIN D024 AS Transportadora ON Transportadora.D024_Cnpj = D022_Cnpj	
								 LEFT JOIN D006 ON D006_Id = T084_D006_Id
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id
								 LEFT JOIN C004 ON C004_Id = T084_C004_Id											
								  	 WHERE T084_Data_Lancamento >= '{$dataInicial}' 
									   AND T084_Data_Lancamento <= '{$dataFinal}'
									   AND T084_C004_Id IN ($matrizEFiliais)   
									   AND T084_Flag_Cancelado != 'S'
									   AND T084_Flag_Entrada_Saida = 'E'
									   AND T084_Numero_Protocolo_Cte > 0
									   AND (D006_Flag_Isento_PIS != 'S'  OR D006_Flag_Isento_COFINS != 'S')
									   AND T084_Modelo IN('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')	
									   AND D014_Flag_Ativo = 'S'
								  GROUP BY D014_Id
							) AS Contas
							GROUP BY D014_Id");
	
	$mD014_0500 = array();
	while($D014 = mysql_fetch_array($sqlD014)){

		# REGISTRO 0500
		$R0500 = array(																#TAM
			$REG = '0500',															#004* 	
			$DT_ALT = formataData($D014['D014_Data_Inclusao']),						#008*	Data da inclusão/alteração		
			$COD_NAT_CC = $D014['D014_Codigo_Natureza_Conta_Corrente'],				#002* 	Código da natureza da conta/grupo de contas (01 Contas de ativo/02 Contas de passivo/03 Patrimônio líquido/04 Contas de resultado/05 Contas de compensação/09 Outras)
			$IND_CTA = $D014['D014_Codigo_Indicador_Conta_Corrente'],					#001* 	Indicador do tipo de conta (S Sintética (grupo de contas)/A Analítica (conta))
			$NÍVEL = '1',															#005 	Nível da conta analítica/grupo de contas. 	
			$COD_CTA  = $D014['D014_Codigo_Plano_Conta_Fiscal'],					#060 	Código da conta analítica/grupo de contas. 		
			$NOME_CTA  = $D014['D014_SubConta'],									#060 	Nome da conta analítica/grupo de contas. 		
			$COD_CTA_REF = $D014['D014_Mascara_Plano_Conta_Fiscal'],				#060	Código da conta correlacionada no Plano de Contas Referenciado, publicado pela RFB.		
			$CNPJ_EST = $D014['QtdeEmpresas'] == 1 ? removerCharEspecial($D014['C004_Cnpj']) : ''															#014*	CNPJ do estabelecimento, no caso da conta informada no campo COD_CTA ser específica de um estabelecimento.
		);
		$R0500 = str_replace('|', '', $R0500);
		$R0500 = implode("|", $R0500);
		$layoutBloco0 .= '|' . $R0500 . '|' . _SALTO_LINHA;
		$mD014_0500[] = $D014['D014_Id'];
	}	

 	for($i=0;$i<sizeof($array0500);$i++){
		if (array_search($array0500[$i], $mD014_0500) !== false) {
			continue;
		} elseif($array0500[$i] <= 0 || empty($array0500[$i])){
			continue;
		}	
		

		$D014 = mysql_query("SELECT D014_Id,
									D014_Data_Inclusao,
									D014_Codigo_Plano_Conta_Fiscal,
									D014_SubConta,
									D014_Mascara_Plano_Conta_Fiscal,
									D014_Codigo_Natureza_Conta_Corrente,
									D014_Codigo_Indicador_Conta_Corrente
							   FROM D014
						 	  WHERE D014_Id = '{$array0500[$i]}'
								AND D014_Flag_Ativo = 'S'");

		$mD014 = mysql_fetch_array($D014);

		# REGISTRO 0500
		$R0500 = array(																#TAM
			$REG = '0500',															#004* 	
			$DT_ALT = formataData($mD014['D014_Data_Inclusao']),						#008*	Data da inclusão/alteração		
			$COD_NAT_CC = $mD014['D014_Codigo_Natureza_Conta_Corrente'],				#002* 	Código da natureza da conta/grupo de contas (01 Contas de ativo/02 Contas de passivo/03 Patrimônio líquido/04 Contas de resultado/05 Contas de compensação/09 Outras)
			$IND_CTA = $mD014['D014_Codigo_Indicador_Conta_Corrente'],					#001* 	Indicador do tipo de conta (S Sintética (grupo de contas)/A Analítica (conta))
			$NÍVEL = '1',															#005 	Nível da conta analítica/grupo de contas. 	
			$COD_CTA  = $mD014['D014_Codigo_Plano_Conta_Fiscal'],					#060 	Código da conta analítica/grupo de contas. 		
			$NOME_CTA  = $mD014['D014_SubConta'],									#060 	Nome da conta analítica/grupo de contas. 		
			$COD_CTA_REF = $mD014['D014_Mascara_Plano_Conta_Fiscal'],				#060	Código da conta correlacionada no Plano de Contas Referenciado, publicado pela RFB.		
			$CNPJ_EST 														        #014*	CNPJ do estabelecimento, no caso da conta informada no campo COD_CTA ser específica de um estabelecimento.
		);
		$R0500 = str_replace('|', '', $R0500);
		$R0500 = implode("|", $R0500);
		$layoutBloco0 .= '|' . $R0500 . '|' . _SALTO_LINHA;
		$mD014_0500[] = $mD014['D014_Id'];
	} 


	$sqlD073 = mysql_query("SELECT D073_Id,
								   D073_Nome							
							 FROM (
									SELECT D073_Id,
										   D073_Nome
									  FROM T008
								 LEFT JOIN T007 ON T007_Id = T008_T007_Id
								 LEFT JOIN D006 ON D006_Id = T008_D006_Id 
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id    
								 LEFT JOIN C004 ON C004_Id = T007_C004_Id
								 LEFT JOIN D073 ON D073_Id = D014_D073_Id
								 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
								 	 WHERE T007_Data_Emissao >= '{$dataInicial}'
									   AND T007_Data_Emissao <= '{$dataFinal}'
									   AND D073_Id IS NOT NULL
									   AND T007_C004_Id IN ($matrizEFiliais) 
									   AND T007_Flag_Cancelada != 'S'
									   AND ((T007_Numero_Protocolo_Nfe > 0 AND T007_Flag_ACP IN('6','8')) OR (T007_Flag_ACP = '7' AND T007_Flag_Travar_Nota = 'S'))									
									   AND (T007_Valor_Total_PIS > 0 OR T007_Valor_Total_COFINS > 0)
								       AND D014_Flag_Ativo = 'S'
									   AND IFNULL(T007A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								  GROUP BY D073_Id
							UNION
								 	SELECT D073_Id,
										   D073_Nome										
									  FROM T014
								 LEFT JOIN T013 ON T013_Id = T014_T013_Id 
								 LEFT JOIN D006 ON D006_Id = T014_D006_Id 
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id 
								 LEFT JOIN C004 ON C004_Id = T013_C004_Id
								 LEFT JOIN D073 ON D073_Id = D014_D073_Id
								 LEFT JOIN T013A ON T013_Id = T013A_T013_Id
								  	 WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
									   AND {$dataNFRecebida} <= '{$dataFinalHora}' 
									   AND D073_Id IS NOT NULL
									   AND T013_C004_Id  IN ($matrizEFiliais) 
									   AND T013_Flag_Cancelada != 'S'
									   AND T013_Numero_Protocolo_Nfe > 0
									   AND T013_Tipo = 1		
									   AND T013_Flag_Estoque_Finalizado = 'S'	
									   AND (T013_Valor_Total_PIS > 0 OR T013_Valor_Total_COFINS > 0)	 
									   AND D014_Flag_Ativo = 'S'
									   AND IFNULL(T013A_Flag_Escritura_Sped_Contribuicoes, 'S') = 'S'
								  GROUP BY D073_Id
							UNION	
									SELECT D073_Id,
										   D073_Nome										
									  FROM T084
								 LEFT JOIN D022 ON D022_Id = T084_D022_Id
								 LEFT JOIN D024 AS Transportadora ON Transportadora.D024_Cnpj = D022_Cnpj	
								 LEFT JOIN D006 ON D006_Id = T084_D006_Id
								 LEFT JOIN D014 ON D014_Id = D006_D014_Id
								 LEFT JOIN C004 ON C004_Id = T084_C004_Id
								 LEFT JOIN D073 ON D073_Id = D014_D073_Id	
									 WHERE T084_Data_Lancamento >= '{$dataInicial}' 
									   AND T084_Data_Lancamento <= '{$dataFinal}'
									   AND D073_Id IS NOT NULL
									   AND T084_C004_Id IN ($matrizEFiliais)   
									   AND T084_Flag_Cancelado != 'S'
									   AND T084_Flag_Entrada_Saida = 'E'
									   AND T084_Numero_Protocolo_Cte > 0
									   AND (D006_Flag_Isento_PIS != 'S'  OR D006_Flag_Isento_COFINS != 'S')
									   AND T084_Modelo IN('07', '08', '8B', '09', '10', '11', '26', '27', '57' , '67' , '63')	
									   AND D014_Flag_Ativo = 'S'
							   	  GROUP BY D073_Id
							) AS Contas
							GROUP BY D073_Id");
	$mD073_0600 = array();							
	while($D073 = mysql_fetch_array($sqlD073)){
		# REGISTRO 0600
		$R0600 = array(																#TAM
			$REG = '0600',															#004* 
			$DT_ALT = '10102012',													#008* 	Data da inclusão/alteração. 		
			$COD_CCUS = $D073['D073_Id'],											#060	Código do centro de custos. 	
			$CCUS = $D073['D073_Nome']												#060 	Nome do centro de custos. 		

		);
		$R0600 = str_replace('|', '', $R0600);
		$R0600 = implode("|", $R0600);
		$layoutBloco0 .= '|' . $R0600 . '|' . _SALTO_LINHA;

		$mD073_0600[] = $D073['D073_Id'];
	}

	for($i=0;$i<sizeof($array0600);$i++){
		if (array_search($array0600[$i], $mD073_0600) !== false) {
			continue;
		} elseif($array0600[$i] <= 0 || empty($array0600[$i])){
			continue;
		}	
		

		$D073 = mysql_query("SELECT D073_Id,
							 	    D073_Nome										
							   FROM D073
							  WHERE D073_Id = '{$array0600[$i]}' 
						   GROUP BY D073_Id");

		$mD073 = mysql_fetch_array($D073);

		# REGISTRO 0600
		$R0600 = array(																#TAM
			$REG = '0600',															#004* 
			$DT_ALT = '10102012',													#008* 	Data da inclusão/alteração. 		
			$COD_CCUS = $mD073['D073_Id'],											#060	Código do centro de custos. 	
			$CCUS = $mD073['D073_Nome']												#060 	Nome do centro de custos. 	

		);
		$R0600 = str_replace('|', '', $R0600);
		$R0600 = implode("|", $R0600);
		$layoutBloco0 .= '|' . $R0600 . '|' . _SALTO_LINHA;

		$mD073_0600[] = $mD073['D073_Id'];
	} 

}
$linhas = substr_count($layoutBloco0, CHR(13).CHR(10));
# REGISTRO 0990
$R0990 = array(																#TAM
	$REG = '0990',															#004	
	$QTD_LIN_0 = $linhas+1													#-		Quantidade total de linhas do Bloco 0
);
$R0990 = str_replace('|', '', $R0990);
$R0990 = implode("|", $R0990);
$layoutBloco0 .= '|' . $R0990 . '|' . _SALTO_LINHA;


$linhas_bloco_0 = $QTD_LIN_0;

