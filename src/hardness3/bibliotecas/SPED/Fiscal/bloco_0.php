<?php
namespace hardness;
global $g;
/*
# BLOCO 0 (ZERO) # INFORMAÇÕES PRINCIPAIS #
REG	   OCOR    DESCRIÇÃO																	
0000	1	   Abertura do Arquivo Digital e Identificação da Pessoa Jurídica
0001	1	   Abertura do Bloco 0
0002	1	   Classificação do estabelecimento industrial ou equiparado a industrial
0005	1	   Dados complementares da entidade
0100	V	   Dados do Contabilista
0150	1:N	   Tabela de Cadastro do Participante
0175    1:N    Alterações na Tabela de Cadastro do Participante  
0190	1:N	   Identificação das Unidades de Medida
0200	1:N	   Tabela de Identificação do Item (Produtos e Serviços)
0205	1:N	   Alteração do Item
0460	1:N    Tabela de Observações do Lançamento Fiscal 
0990	1	   Encerramento do Bloco 0
*/
//Verifica se existe alguma atividade comercial no periodo

require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();

$C004 = mysql_query("SELECT C004_Numero_Suframa, 
							C004_Flag_Tipo_Atividade, 
							C004_Nome_Empresa,
							C004_Cnpj,
							D018_UF,
							C004_Inscricao_Estadual,
							D020_Codigo_IBGE,
							C004_Inscricao_Municipal,
							C004_Telefone,
							C004_Fax,
							C004_Nome_Abreviado,
							C004_Cep,
							C004_Logradouro,
							C004_Numero,
							C004_Complemento,
							C004_Bairro,
							C004_DDD_Fax,
							C004_E_Mail,
							C004_DDD_Telefone,
							C004_Classificacao_Contribuinte
					FROM C004 
					LEFT JOIN D018 ON D018_Id = C004_D018_Id 
					LEFT JOIN D020 ON D020_Id = C004_D020_Id
					WHERE C004_Id = '{$g['empresaAtual']}'");
$C004 = mysql_fetch_array($C004);

$layout0200 = "";

$SUFRAMA = (strlen($C004['C004_Numero_Suframa']) == 9) ? $C004['C004_Numero_Suframa'] : ''; 
$tipoAtividade = ($C004['C004_Flag_Tipo_Atividade'] != '0') ? '1' : '0';
$C004['C004_Flag_Perfil_EFD'] = $g["C031"]["PerfilSPEDFiscal"];
# REGISTRO 0000
$R0000 = array(																#TAM
	$REG = '0000', 															#004* 	
	$COD_VER = trim($g['C031']['CodigoLayoutSPED']),								#003* 	Codigo do Layout
	$tipoEFD,																#001* 	Tipo de Escrituração (0 Original / 1 Retificadora)
	formataData($dataInicial),												#008* 	Data inicial das informações
	formataData($dataFinal),												#008* 	Data final das informações
	$NOME = trim(adicionarEspape($C004['C004_Nome_Empresa'])),					#100 	Nome empresarial da pessoa jurídica
	$CNPJ = trim(removeChars($C004['C004_Cnpj'])),								#014* 	CNPJ do estabelecimento
	$CPF = '',																#014* 	CPF do estabelecimento
	$UF = trim($C004['D018_UF']),													#002* 	UF Pessoa Juridica
	$IE = trim(removeChars($C004['C004_Inscricao_Estadual'])),					#014 	Codigo Inscrição Estadual
	$COD_MUN = trim($C004['D020_Codigo_IBGE']),									#007* 	Código do município do domicílio fiscal da pessoa jurídica
	$IM = trim(removeChars($C004['C004_Inscricao_Municipal'])),					#		Codigo da Inscricao Municipal
	$SUFRAMA,																#009* 	Inscrição da pessoa jurídica na Suframa
	$IND_PERFIL =trim($C004['C004_Flag_Perfil_EFD']),							#001* 	Perfil de apresentação do arquivo Fiscal
	$IND_ATIV = trim($tipoAtividade)												#001 	Indicador de tipo de atividade preponderante (0 Industrial ou equiparado a industrial/1  Outros)
);
$R0000 = implode("|", $R0000);
$layout0200 .= '|' . $R0000 . '|' . _SALTO_LINHA;

$IND_MOV = '0';
# REGISTRO 0001
$R0001 = array(																#TAM
	$REG = '0001',															#004* 	
	$IND_MOV																#001 	Indicador de movimento (0 Com dados / 1 Sem dados)
);
$R0001 = implode("|", $R0001);
$layout0200 .= '|' . $R0001 . '|' . _SALTO_LINHA;

# REGISTRO 0002
if($tipoAtividade === '0') {
	$R0002 = array(																			#TAM
		$REG = '0002',																		#004* 	
		$CLAS_ESTAB_IND	 = trim($C004['C004_Classificacao_Contribuinte'])					#002 Classificação do estabelecimento conforme tabela 4.5.5
	);
	$R0002 = implode("|", $R0002);
	$layout0200 .= '|' . $R0002 . '|' . _SALTO_LINHA;
}

# VALIDAÇÕES DE CAMPOS 
$C004['C004_Telefone'] = str_replace("-", "", $C004['C004_Telefone']);
$C004['C004_Fax'] 	   = str_replace("-", "", $C004['C004_Fax']);
if($IND_MOV == '0'){
	# REGISTRO 0005
	$R0005 = array(																			#TAM
		$REG = '0005',																		#004* 	
		$FANTASIA = adicionarEspape($C004['C004_Nome_Abreviado']),							#060 	Nome de fantasia associado ao nome empresarial.
		$CEP = removeChars(trim($C004['C004_Cep'])),										#008*	Código de Endereçamento Postal.
		$END = adicionarEspape($C004['C004_Logradouro']),									#060 	Logradouro e endereço do imóvel.
		$NUM = trim($C004['C004_Numero']),											#010 	Número do imóvel.
		$COMPL = adicionarEspape($C004['C004_Complemento']),								#060 	Dados complementares do endereço.
		$BAIRRO = adicionarEspape($C004['C004_Bairro']),									#060 	Bairro em que o imóvel está situado.
		$FONE = removeChars($C004['C004_DDD_Telefone']).removeChars($C004['C004_Telefone']),		#011 	Número do telefone (DDD+FONE).
		$FAX = removeChars($C004['C004_DDD_Fax']).removeChars($C004['C004_Fax']),											#011 	Número do fax.
		$EMAIL = adicionarEspape($C004['C004_E_Mail'])										#		Endereço do correio eletrônico.
	);

	$R0005 = implode("|", $R0005);
	$layout0200 .= '|' . $R0005 . '|' . _SALTO_LINHA;

	# REGISTRO 0015
	// Array criado no bloco E registro E200
	foreach ($id_UF_ICMS_ST as $id_UF) {
		$sql = "SELECT D018_UF, D053_Inscricao_Estadual_ST FROM D018 LEFT JOIN D053 ON D053_D018_Id = D018_Id WHERE D018_Id = '{$id_UF}' AND D053_C004_Id = '{$g['empresaAtual']}'";
		$D053 = mysql_fetch_assoc(mysql_query($sql));

		if (!empty($D053['D053_Inscricao_Estadual_ST'])) {
			$R0015 = array(																		#TAM
				$REG = '0015',																	#004
				$UF_ST = trim($D053['D018_UF']),												#002* 	Sigla da UF onde o contribuinte possui inscrição como substituto tributário.
				$IE_ST = trim(removeChars($D053['D053_Inscricao_Estadual_ST']))					#014*	IE do contribuinte na UF onde ele estiver inscrito como substituto tributário
			);
	
			$R0015 = implode("|", $R0015);
			$layout0200 .= '|' . $R0015 . '|' . _SALTO_LINHA;
		}
	}

	# REGISTRO 0100
	$C007 = mysql_fetch_array(mysql_query("SELECT C007_Nome, 
												  C007_Cpf, 
												  C007_Crc, 
												  C007_Cnpj, 
												  C007_Cep, 
												  C007_Endereco,
												  C007_Numero, 
												  C007_Complemento,
												  C007_Bairro, 
												  C007_DDD_Telefone, 
												  C007_Telefone,
												  C007_Email, 
												  D020_Codigo_IBGE
										     FROM C007 
										LEFT JOIN D020 ON C007_D020_Id = D020_Id 
										    WHERE C007_Id = '{$C007_Id_Contabilista}'"));
	$R0100 = array(																#TAM
		$REG = '0100',															#004*	
		$NOME =	trim(adicionarEspape($C007['C007_Nome'])),							#100 	Nome do contabilista.
		$CPF = trim(removeChars($C007['C007_Cpf'])),									#011*	Número de inscrição do contabilista no CPF.
		$CRC = trim(removeChars($C007['C007_Crc'])),									#015 	Número de inscrição do contabilista no Conselho Regional de Contabilidade.
		$CNPJ = trim(removeChars($C007['C007_Cnpj'])),								#014*	Número de inscrição do escritório de contabilidade no CNPJ, se houver.
		$CEP = trim(removeChars($C007['C007_Cep'])),									#008*	Código de Endereçamento Postal.
		$END = trim($C007['C007_Endereco']),										#060 	Logradouro e endereço do imóvel.
		$NUM = trim($C007['C007_Numero']),											#	 	Número do imóvel.
		$COMPL = trim($C007['C007_Complemento']),									#060 	Dados complementares do endereço.
		$BAIRRO = trim($C007['C007_Bairro']),										#060 	Bairro em que o imóvel está situado.
		$FONE = removeChars($C007['C007_DDD_Telefone']).removeChars($C007['C007_Telefone']),	#10* 	Número do telefone.
		$FAX = '',																#10* 	Número do fax.
		$EMAIL = trim($C007['C007_Email']),											#	 	Endereço do correio eletrônico.
		$COD_MUN = trim($C007['D020_Codigo_IBGE'])									#007*	Código do município, conforme tabela IBGE.
	);

	//VALIDAÇÕES REGISTRO 0100
	if(empty($R0100[1])){
		$erros .= "Campo obrigatório - O campo 'Nome' do cadastro do contador selecionado deve estar preenchido.". _SALTO_LINHA;
	}

	if(empty($R0100[2])){
		$erros .= "Campo obrigatório - O campo 'CPF' do cadastro do contador selecionado deve estar preenchido.". _SALTO_LINHA;
	}

	if(empty($R0100[3])){
		$erros .= "Campo obrigatório - O campo 'CRC' do cadastro do contador selecionado deve estar preenchido.". _SALTO_LINHA;
	}

	if(empty($R0100[12])){
		$erros .= "Campo obrigatório - O campo 'E-mail' do cadastro do contador selecionado deve estar preenchido.". _SALTO_LINHA;
	}

	if(empty($R0100[13])){
		$erros .= "Campo obrigatório - O campo 'Cidade' do cadastro do contador selecionado deve estar preenchido.". _SALTO_LINHA;
	}
	
	$R0100 = implode("|", $R0100);
	$layout0200 .= '|' . $R0100 . '|' . _SALTO_LINHA;

	// tradando a data para as notas de entrada	
	// $dataInicialHora = $dataInicial.' 00:00:00';
	// $dataFinalHora   = $dataFinal. ' 23:59:59';
	//-----------------------------------------
	
	$sqlT007_D024 = mysql_query("SELECT D024_Flag_Pessoa_Fisica_Juridica,
										D024_Cnpj,
										D024_Cpf,
										D024_Inscricao_Estadual,
										D024_Id,
										D024_Nome_Empresa,
										D030_Codigo_Pais,
										D020_Codigo_IBGE,
										D024_Endereco,
										D024_Numero,
										D024_Complemento,
										D024_Bairro
							      from (
										 SELECT D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro
										   FROM T007 
									  LEFT JOIN D024 ON D024_Id = T007_D024_Id 
									  LEFT JOIN D020 ON D020_Id = D024_D020_Id 
									  LEFT JOIN D030 ON D030_Id = D024_D030_Id 
										  WHERE T007_Data_Emissao >= '{$dataInicial}' 
											AND T007_Data_Emissao <= '{$dataFinal}' 
											AND T007_C004_Id = '{$g['empresaAtual']}' 
											AND T007_Flag_Cancelada != 'S' 
											AND T007_Numero_Protocolo_Nfe > 0 
											AND T007_Flag_ACP IN('6') 
											AND D024_Id > 0
									   GROUP BY D024_Id
										  UNION
										 SELECT D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro
										   FROM T013 
									  LEFT JOIN D024 ON D024_Id = T013_D024_Id 
									  LEFT JOIN D020 ON D020_Id = D024_D020_Id 
									  LEFT JOIN D030 ON D030_Id = D024_D030_Id 
										  WHERE {$dataNFRecebida} >= '{$dataInicialHora}' 
											AND {$dataNFRecebida} <= '{$dataFinalHora}' 
											AND T013_C004_Id = '{$g['empresaAtual']}' 
											AND T013_Flag_Cancelada != 'S' 											
											AND T013_Flag_Estoque_Finalizado = 'S'
											AND ((T013_Tipo = '1' AND T013_Numero_Protocolo_Nfe > 0) OR T013_Tipo IN('5','6','7','8','11','12'))     
											AND D024_Id > 0
									   GROUP BY D024_Id
									   	 UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T084
										LEFT JOIN D022 ON D022_Id = T084_D022_Id
										LEFT JOIN D024 ON D024_Cnpj = D022_Cnpj
										LEFT JOIN D020 ON D020_Id = D024_D020_Id
										LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE T084_Data_Lancamento >= '{$dataInicialHora}'
											AND T084_Data_Lancamento <= '{$dataFinalHora}'
											AND T084_Flag_Entrada_Saida = 'E'
											AND T084_C004_Id = '{$g['empresaAtual']}'  
											AND T084_Chave_Acesso_Cte > 0    
											AND D024_Id > 0
											GROUP BY D024_Id
										UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T084
										LEFT JOIN D022 ON D022_Id = T084_D022_Id
										LEFT JOIN D024 ON D024_Id = T084_D024_Tomador_Id
										LEFT JOIN D020 ON D020_Id = D024_D020_Id
										LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE T084_Data_Emissao >= '{$dataInicialHora}' AND T084_Data_Emissao <= '{$dataFinalHora}'
											AND T084_Flag_Entrada_Saida = 'S'
											AND T084_C004_Id = '{$g['empresaAtual']}'  
											AND T084_Chave_Acesso_Cte > 0    
											AND D024_Id > 0
											GROUP BY D024_Id
										UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T008
										LEFT JOIN T007 ON T007_Id = T008_T007_Id
										LEFT JOIN D027 ON D027_Id = T007_D027_Id  
										LEFT JOIN D024 ON D024_Cnpj = IF(D027_Cnpj_Instituicao_Pagamento = '',0, D027_Cnpj_Instituicao_Pagamento)    
										LEFT JOIN D020 ON D020_Id = D024_D020_Id
										LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE T007_Data_Emissao >= '{$dataInicial}' 
											AND T007_Data_Emissao <= '{$dataFinal}'
											AND T007_C004_Id = '{$g['empresaAtual']}'
											AND T007_Flag_ACP IN('6', '8')
											AND (D027_Tipo_Forma_Pagamento_NFCE IN ('03', '04', '15', '16', '17', '18', '19'))  
											AND T007_Flag_Cancelada != 'S'
											AND T007_Numero_Protocolo_Nfe > 0	
											AND D024_Id > 0											
											GROUP BY D024_Id  
										UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T008
										LEFT JOIN T007 ON T007_Id = T008_T007_Id
										LEFT JOIN D027 ON D027_Id = T007_D027_Id  
										LEFT JOIN D024 ON D024_Cnpj = IF(D027_Cnpj_Intermediador_Transacao = '',0, D027_Cnpj_Intermediador_Transacao)    
										LEFT JOIN D020 ON D020_Id = D024_D020_Id
										LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE T007_Data_Emissao >= '{$dataInicial}' 
											AND T007_Data_Emissao <= '{$dataFinal}'
											AND T007_C004_Id = '{$g['empresaAtual']}'
											AND T007_Flag_ACP IN('6', '8')
											AND (D027_Tipo_Forma_Pagamento_NFCE IN ('03', '04', '15', '16', '17', '18', '19'))  
											AND (D027_Cnpj_Intermediador_Transacao != '' AND D027_Cnpj_Intermediador_Transacao IS NOT NULL)
											AND T007_Flag_Cancelada != 'S'
											AND T007_Numero_Protocolo_Nfe > 0
											AND D024_Id > 0
											GROUP BY D024_Id    	
										UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T132
											INNER JOIN T013 ON T132_Chave_Acesso = T013_Chave_Acesso_Nfe
											LEFT JOIN T007 ON T007_Id = T132_T007_Id
											LEFT JOIN D024 ON D024_Id = T013_D024_Id 
											LEFT JOIN D020 ON D020_Id = D024_D020_Id 
											LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE  T007_Data_Emissao >= '{$dataInicial}'
												AND T007_Data_Emissao <= '{$dataFinal}'
												AND T007_C004_Id = '{$g['empresaAtual']}'
												AND T007_Flag_Cancelada != 'S'
												AND T007_Numero_Protocolo_Nfe > 0
												AND T007_Flag_ACP IN('6')
												AND D024_Id > 0
											GROUP BY D024_Id
										UNION
											SELECT 
												D024_Flag_Pessoa_Fisica_Juridica,
												D024_Cnpj,
												D024_Cpf,
												D024_Inscricao_Estadual,
												D024_Id,
												D024_Nome_Empresa,
												D030_Codigo_Pais,
												D020_Codigo_IBGE,
												D024_Endereco,
												D024_Numero,
												D024_Complemento,
												D024_Bairro 
											FROM T132
											INNER JOIN T007 AS Referenciada ON Referenciada.T007_Chave_Acesso_Nfe = T132_Chave_Acesso
											LEFT JOIN T007 AS Origem ON Origem.T007_Id = T132_T007_Id
											LEFT JOIN D024 ON D024_Id = Referenciada.T007_D024_Id 
											LEFT JOIN D020 ON D020_Id = D024_D020_Id 
											LEFT JOIN D030 ON D030_Id = D024_D030_Id
											WHERE  Origem.T007_Data_Emissao >=  '{$dataInicial}'
												AND Origem.T007_Data_Emissao <= '{$dataFinal}'
												AND Origem.T007_C004_Id = '{$g['empresaAtual']}'
												AND Origem.T007_Flag_Cancelada != 'S'
												AND Origem.T007_Numero_Protocolo_Nfe > 0
												AND Origem.T007_Flag_ACP IN('6')
												AND D024_Id > 0
											GROUP BY D024_Id
									   ) as notaFiscal
										GROUP BY D024_Id");		
											
	$mD024_0150 = array();

	while($T007_D024 = mysql_fetch_array($sqlT007_D024)) {
		if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
			$T007_D024['D024_Cnpj'] = '';
			$T007_D024['D024_Cpf'] = '';
			$T007_D024['D024_Inscricao_Estadual'] = '';
		} else if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
			$T007_D024['D024_Cnpj'] = '';	
		} else if($T007_D024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
			$T007_D024['D024_Cpf'] = '';
		}
		$IE = (trim($T007_D024['D024_Inscricao_Estadual']) == "ISENTO") ? '' : removeChars($T007_D024['D024_Inscricao_Estadual']);
		#LOOP REGISTRO 0150
		$R0150 = array(															#TAM
			$REG = '0150',														#004*			
			$COD_PART = trim($T007_D024['D024_Id']),						    #060	Código de identificação do participante no arquivo.		
			$NOME = trim(adicionarEspape($T007_D024['D024_Nome_Empresa'])),		#100	Nome pessoal ou empresarial do participante.	
			$COD_PAIS = trim($T007_D024['D030_Codigo_Pais']),					#005	Código do país do participante, conforme a tabela indicada no item 3.2.1.		
			$CNPJ = trim(removeChars($T007_D024['D024_Cnpj'])),					#014*	CNPJ do participante.	
			$CPF = trim(removeChars($T007_D024['D024_Cpf'])),					#011*	CPF do participante.	
			$IE,																#014	Inscrição Estadual do participante.
			$COD_MUN = trim($T007_D024['D020_Codigo_IBGE']),					#007*	Código do município, conforme a tabela IBGE		
			$SUFRAMA = '',														#009*	Número de inscrição do participante na Suframa		
			$END = trim(adicionarEspape($T007_D024['D024_Endereco'])),			#060	Logradouro e endereço do imóvel	
			$NUM = trim($T007_D024['D024_Numero']),								#-		Número do imóvel
			$COMPL = trim(adicionarEspape($T007_D024['D024_Complemento'])),		#060	Dados complementares do endereço	
			$BAIRRO	= trim(adicionarEspape($T007_D024['D024_Bairro']))			#060	Bairro em que o imóvel está situado	
		);
		if(strlen($R0150[10])>10){
			$erros .= "Tamanho incorreto - O campo 'Número do endereço' do cadastro do cliente/fornecedor só permite 10 caracteres - Cliente:".$R0150[1]." - " . $R0150[2] . " - Endereço:" . $R0150[9] . ", " . $R0150[10] . _SALTO_LINHA;
		}
		$R0150 = implode("|", $R0150);
		$layout0200 .= '|' . $R0150 . '|' . _SALTO_LINHA;
		$mD024_0150[] = $T007_D024['D024_Id'];
	}

	$retorno = $CAD002->inventarioBloco_0($dataInventario);

	if($retorno === true){
			
		$sqlT001 = mysql_query("SELECT
									D024_Id,
									D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual,								
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro,
									(case 
										when D004_Indicador_Propriedade = '2'
											then sum(T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1))
											else sum( T001_Quantidade * if(T001_Flag_Operacao = 'S', 1, -1))
									end) as `Saldo` 
								FROM
									T001
									left join T013 on T013_Id=T001_T013_Id  
									left join D024 on D024_Id=if(T001_Flag_Operacao = 'E' and T001_T008_Id is null, ifnull(T013_D024_Id_Participante, T001_D024_Id) , T001_D024_Id_Cliente)
									left join D009 on D009_Id=T001_D009_Id
									left join D049 on D049_Id=D009_D049_Id
									left join D001 on D001_Id=D049_D001_Id
									left join T066 on T066_Id=T001_T066_Id
									left join D004 on D004_Id=T066_D004_Id
									left join D030 ON D030_Id=D024_D030_Id
									left join D020 ON D020_Id=D024_D020_Id																												
									left join D037 ON D037_Id=D001_D037_Id
									left join D001A ON D001A_D001_Id=D001_Id
									left join D187 ON D187_Id=D001A_D187_Id  
								WHERE
									D009_C004_Id = '{$g['empresaAtual']}'
									and (D004_Indicador_Propriedade = '1' or D004_Indicador_Propriedade = '2')
									and (T001_Flag_Operacao = 'S' or T001_Flag_Operacao = 'E')
									and ((D001_Flag_Tipo_Item >= '00'and D001_Flag_Tipo_Item <= '06') or D001_Flag_Tipo_Item = '10')
									and T001_Data_Lancamento <= '$dataInventario'	
								group by D009_Id, D024_Id 
								having sum(case
											when D004_Indicador_Propriedade = '2' 
												then T001_Quantidade * if(T001_Flag_Operacao = 'S', -1, 1)
												else T001_Quantidade * if(T001_Flag_Operacao = 'S', 1, -1) 
											end) > 0
								order by D024_Id");	
	}else{
		$sqlT001 = mysql_query($retorno);
	}

	$erro = mysql_error();					
	if (!empty($erro)) $erro .= "Erro 0150: " . $erro;

	while($mT001 = mysql_fetch_Array($sqlT001)) {
		if((float)$mT001['Saldo'] <= 0) {
			continue;
		}
		if (array_search($mT001['D024_Id'], $mD024_0150) !== false) {
			continue;
		}		
		if($mT001['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
			$mT001['D024_Cnpj'] = '';
			$mT001['D024_Cpf'] = '';
			$mT001['D024_Inscricao_Estadual'] = '';
		} else if($mT001['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
			$mT001['D024_Cnpj'] = '';	
		} else if($mT001['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
			$mT001['D024_Cpf'] = '';
		}
		$IE = (trim($mT001['D024_Inscricao_Estadual']) == "ISENTO") ? '' : removeChars($mT001['D024_Inscricao_Estadual']);
		#LOOP REGISTRO 0150
		$R0150 = array(															#TAM
			$REG = '0150',														#004*			
			$COD_PART = trim($mT001['D024_Id']),						    	#060	Código de identificação do participante no arquivo.		
			$NOME = trim(adicionarEspape($mT001['D024_Nome_Empresa'])),			#100	Nome pessoal ou empresarial do participante.	
			$COD_PAIS = trim($mT001['D030_Codigo_Pais']),						#005	Código do país do participante, conforme a tabela indicada no item 3.2.1.		
			$CNPJ = trim(removeChars($mT001['D024_Cnpj'])),						#014*	CNPJ do participante.	
			$CPF = trim(removeChars($mT001['D024_Cpf'])),						#011*	CPF do participante.	
			$IE,																#014	Inscrição Estadual do participante.
			$COD_MUN = trim($mT001['D020_Codigo_IBGE']),						#007*	Código do município, conforme a tabela IBGE		
			$SUFRAMA = '',														#009*	Número de inscrição do participante na Suframa		
			$END = trim(adicionarEspape($mT001['D024_Endereco'])),				#060	Logradouro e endereço do imóvel	
			$NUM = trim($mT001['D024_Numero']),									#-		Número do imóvel
			$COMPL = trim(adicionarEspape($mT001['D024_Complemento'])),			#060	Dados complementares do endereço	
			$BAIRRO	= trim(adicionarEspape($mT001['D024_Bairro']))				#060	Bairro em que o imóvel está situado	
		);
		if(strlen($R0150[10])>10){
			$erros .= "Tamanho incorreto - O campo 'Número do endereço' do cadastro do cliente/fornecedor só permite 10 caracteres - Cliente:".$R0150[1]." - " . $R0150[2] . " - Endereço:" . $R0150[9] . ", " . $R0150[10] . _SALTO_LINHA;
		}
		$R0150 = implode("|", $R0150);
		$layout0200 .= '|' . $R0150 . '|' . _SALTO_LINHA;
		$mD024_0150[] = $mT001['D024_Id'];
	}	
	
	for($i=0;$i<sizeof($array0150);$i++){
		
		$D024 = mysql_query("SELECT D024_Flag_Pessoa_Fisica_Juridica,
									D024_Cnpj,
									D024_Cpf,
									D024_Inscricao_Estadual,
									D024_Id,
									D024_Nome_Empresa,
									D030_Codigo_Pais,
									D020_Codigo_IBGE,
									D024_Endereco,
									D024_Numero,
									D024_Complemento,
									D024_Bairro
							   FROM D024 
			   			  LEFT JOIN D020 ON D020_Id = D024_D020_Id 
			   			  LEFT JOIN D030 ON D030_Id = D024_D030_Id 
				              WHERE D024_Id = '{$array0150[$i]}'
							    AND D024_Id > 0
					       GROUP BY D024_Id"); 	

		$mD024 = mysql_fetch_array($D024);
		
		if (array_search($mD024['D024_Id'], $mD024_0150) !== false) {
			continue;
		}		

		if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I'){
			$mD024['D024_Cnpj'] = '';
			$mD024['D024_Cpf'] = '';
			$mD024['D024_Inscricao_Estadual'] = '';
		} else if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F'){
			$mD024['D024_Cnpj'] = '';	
		} else if($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
			$mD024['D024_Cpf'] = '';
		}
		$IE = (trim($mD024['D024_Inscricao_Estadual']) == "ISENTO") ? '' : removeChars($mD024['D024_Inscricao_Estadual']);
		#LOOP REGISTRO 0150
		$R0150 = array(															#TAM
			$REG 	  = '0150',													#004*			
			$COD_PART = trim($mD024['D024_Id']),						    	#060	Código de identificação do participante no arquivo.		
			$NOME 	  = trim(adicionarEspape($mD024['D024_Nome_Empresa'])),		#100	Nome pessoal ou empresarial do participante.	
			$COD_PAIS = trim($mD024['D030_Codigo_Pais']),						#005	Código do país do participante, conforme a tabela indicada no item 3.2.1.		
			$CNPJ 	  = trim(removeChars($mD024['D024_Cnpj'])),					#014*	CNPJ do participante.	
			$CPF 	  = trim(removeChars($mD024['D024_Cpf'])),					#011*	CPF do participante.	
			$IE,																#014	Inscrição Estadual do participante.
			$COD_MUN  = trim($mD024['D020_Codigo_IBGE']),						#007*	Código do município, conforme a tabela IBGE		
			$SUFRAMA  = '',														#009*	Número de inscrição do participante na Suframa		
			$END 	  = trim(adicionarEspape($mD024['D024_Endereco'])),			#060	Logradouro e endereço do imóvel	
			$NUM 	  = trim($mD024['D024_Numero']),							#-		Número do imóvel
			$COMPL 	  = trim(adicionarEspape($mD024['D024_Complemento'])),		#060	Dados complementares do endereço	
			$BAIRRO	  = trim(adicionarEspape($mD024['D024_Bairro']))			#060	Bairro em que o imóvel está situado	
		);
		if(strlen($R0150[10])>10){
			$erros .= "Tamanho incorreto - O campo 'Número do endereço' do cadastro do cliente/fornecedor só permite 10 caracteres - Cliente:".$R0150[1]." - " . $R0150[2] . " - Endereço:" . $R0150[9] . ", " . $R0150[10] . _SALTO_LINHA;
		}
		$R0150 = implode("|", $R0150);
		$layout0200 .= '|' . $R0150 . '|' . _SALTO_LINHA;
		$mD024_0150[] = $mD024['D024_Id'];
	}
	
	$registro0200 = "";

	for($i=0;$i<sizeof($array0200);$i++){
		$sqlD001 = mysql_query("SELECT 
								D001_Codigo_Produto,
							  	D001_Codigo_Barras,
							 	D049_Codigo_Barras,
							  	D001_Descricao_Produto,
							  	D049_Origem_Mercadoria,
							  	D005_Classificacao_Fiscal,
							  	D037_Unidade,
							  	D037_Id,
							  	D001_Flag_Tipo_Item,
							  	ROUND(D149_Aliquota_IPI),
							  	D053_ST_ICMS_Interno,
							  	D149_Codigo_CEST,
								D053_D018_Id,
								C004_D018_Id  
							FROM
							 	D001 
							LEFT JOIN D049 ON D049_D001_Id = D001_Id 
							LEFT JOIN D009 ON D009_D049_Id = D049_Id 
							LEFT JOIN D037 ON D001_D037_Id = D037_Id 
							LEFT JOIN D005 ON D001_D005_Id = D005_Id 
							LEFT JOIN C004 ON D009_C004_Id = C004_Id 
							LEFT JOIN D053 ON C004_Id = D053_C004_Id
							LEFT JOIN D149 ON D149_D005_Id = D005_Id AND D149_C004_Id = '{$g['empresaAtual']}' 
							WHERE D001_Id='{$array0200[$i]}'
							  AND D053_D018_Id=C004_D018_Id");
		$rD001 = mysql_num_rows($sqlD001);
		$D001 = mysql_fetch_array($sqlD001);
		//Codigo de Barras
		$Codigo_Barras = "";
		if (!empty($D001['D049_Codigo_Barras'])) {
            $Codigo_Barras = $D001['D049_Codigo_Barras'];
        } else {
            $Codigo_Barras = $D001['D001_Codigo_Barras'];
        }
		$EAN = $Codigo_Barras;

        $len = strlen($EAN);
        if ($len != 8 && $len != 12 && $len != 13 && $len != 14) {
            $EAN = "SEM GTIN";
        }

        $NCM = "";
        if(!($D001['D001_Flag_Tipo_Item']=="07") || !($D001['D001_Flag_Tipo_Item']=="08") || !($D001['D001_Flag_Tipo_Item']=="09") || !($D001['D001_Flag_Tipo_Item']=="10") || !($D001['D001_Flag_Tipo_Item']=="99")){
        	$NCM = $D001['D005_Classificacao_Fiscal'];
        }

		# REGISTRO 0200
		$R0200 = array(																#TAM
			$REG = '0200',															#004				
			$COD_ITEM = tirarEspacos($D001['D001_Codigo_Produto']),							#060	Código do item			
			$DESCR_ITEM = tirarEspacos(adicionarEspape($D001['D001_Descricao_Produto'])),		#-		Descrição do item	
			$COD_BARRA = tirarEspacos($EAN),													#-		Representação alfanumérico do código de barra do produto, se houver.	
			$COD_ANT_ITEM = '',													#060	Código anterior do item com relação à última informação apresentada.			
			$UNID_INV = tirarEspacos($D001['D037_Unidade']),									#006	Unidade de medida utilizada na quantificação de estoques.  		
			$TIPO_ITEM = tirarEspacos($D001['D001_Flag_Tipo_Item']),							#002*	Tipo do item (00 Mercadoria para Revenda/01 Matéria-Prima/02 Embalagem/03 Produto em Processo/04 Produto Acabado/05 Subproduto/06 Produto Intermediário/07 Material de Uso e Consumo/08 Ativo Imobilizado/09 Serviços/10 Outros insumos/99 Outras)		
			$COD_NCM = tirarEspacos(substr($NCM, 0,8)),						#008	Código da Nomenclatura Comum do Mercosul		
			$EX_IPI = tirarEspacos($D001['D149_Aliquota_IPI']),								#003	Código EX, conforme a TIPI
			$COD_GEN = tirarEspacos(substr($NCM, 0,2)),														#002*	Código do gênero do item, conforme a Tabela 4.2.1.	 - serviços	
			$COD_LST = '',														#004	Código do serviço conforme lista do Anexo I da Lei Complementar Federal nº 116/03.	- serviços	
			$ALIQ_ICMS	= tirarEspacos(formataDecimal($D001['D053_ST_ICMS_Interno'])),		#006	Alíquota de ICMS aplicável ao item nas operações internas												
			$CEST = tirarEspacos(str_replace(".", "", $D001['D149_Codigo_CEST']))									#007*   Código Especificador da Substituição Tributária
		);
		$R0200 = implode("|", $R0200);
		$registro0200 .= '|' . $R0200 . '|' . _SALTO_LINHA;
		verificaRegistro0190($D001['D037_Id']);
	}
	
	//ADICIONADO O REGISTRO 0190 DEPOIS DO 200 POIS EXISTEM VALIDAÇÕES NECESSÁRIAS
	//A ORDEM CORRETA É 0190 - 0200
	//SERÁ CONCATENADO PARA FICAR NA ORDEM CERTA
	$registro0190 = "";
	log("array 0190:".sizeof($array0190));
	for($i=0;$i<sizeof($array0190);$i++){
		$sqlD037 = mysql_query("SELECT D037_Unidade, D037_Descricao_Unidade FROM D037 WHERE D037_Id='{$array0190[$i]}'");
		while($D037_Id = mysql_fetch_array($sqlD037)){
			$verificaUnidade = "|".$D037_Id['D037_Unidade']."|";
 			if(strpos($registro0190, $verificaUnidade) !== false){
				// Se tiver mais de uma unidade com a mesma sigla, não envia
				// Não pode enviar mais de uma unidade com o mesmo nome, da erro na escrituração
				continue;				
			} 
			#LOOP REGISTRO 0190
			$R0190 = array(																#TAM
				$REG = '0190',															#004*	
				$UNID = tirarEspacos($D037_Id['D037_Unidade']),									#006	Código da unidade de medida
				$DESCR = tirarEspacos(adicionarEspape($D037_Id['D037_Descricao_Unidade']))		#-		Descrição da unidade de medida
			);
			$R0190 = implode("|", $R0190);
			$registro0190 .= '|' . $R0190 . '|' . _SALTO_LINHA;
		}
	}

    $layout0200 = $layout0200.$registro0190.$registro0200;
	
	//REGISTRO 0300: CADASTRO DE BENS OU COMPONENTES DO ATIVO IMOBILIZADO 
	// Este registro tem o objetivo de identificar e caracterizar todos os bens ou componentes arrolados no registro G125 do Bloco G e os bens em construção
	if($gerarBlocoG == 'S'){
		
		$T316 = mysql_query("SELECT D001_Codigo_Produto,
								    D001_Descricao_Produto,
								    D014_Codigo_Plano_Conta_Fiscal,
									T316_Numero_Parcelas_Apropriacao,
									D073_Id,
									T316_Descricao_Utilizacao_Bem,
									T316_Id,
									T316_Codigo_Produto_CIAP								    
						       FROM T316
					      LEFT JOIN T014 ON T014_Id = T316_T014_Id
					      LEFT JOIN D009 ON D009_Id = T014_D009_Id
					      LEFT JOIN D049 ON D049_Id = D009_D049_Id
					      LEFT JOIN D001 ON D001_Id = D049_D001_Id
						  LEFT JOIN D006 ON D006_Id = T014_D006_Id
						  LEFT JOIN D014 ON D014_Id = D006_D014_Id
						  LEFT JOIN T013 ON T013_Id = T014_T013_Id
						  LEFT JOIN D073 ON D073_Id = D006_D073_Id
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
							 SELECT D001_Codigo_Produto,
									D001_Descricao_Produto,
									D014_Codigo_Plano_Conta_Fiscal,
									T316_Numero_Parcelas_Apropriacao,
									D073_Id,
									T316_Descricao_Utilizacao_Bem,
									T316_Id,
									T316_Codigo_Produto_CIAP
							   FROM T316
						  LEFT JOIN T008 ON T008_Id = T316_T008_Id
						  LEFT JOIN D009 ON D009_Id = T008_D009_Id
						  LEFT JOIN D049 ON D049_Id = D009_D049_Id
						  LEFT JOIN D001 ON D001_Id = D049_D001_Id
						  LEFT JOIN D006 ON D006_Id = T008_D006_Id
						  LEFT JOIN D014 ON D014_Id = D006_D014_Id
						  LEFT JOIN T007 ON T007_Id = T008_T007_Id
						  LEFT JOIN D073 ON D073_Id = D006_D073_Id
							  WHERE T316_Flag_Ativo_Baixado = 'N'
								AND T316_Numero_Parcelas_Apropriacao > 0
								AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
								AND T007_C004_Id = '{$g['empresaAtual']}' 
								AND T007_Flag_Cancelada != 'S' 
								AND T007_Numero_Protocolo_Nfe > 0
								AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
								AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')");
		$mysql_num_rows = mysql_num_rows($T316);
		$lancadoAtivosImobilizados = 'N';
		if(mysql_num_rows($T316) > 0){
			$lancadoAtivosImobilizados = 'S';
			while($mT316 = mysql_fetch_array($T316)){
				# REGISTRO 0300
				$codigoProduto = (!empty($mT316['T316_Codigo_Produto_CIAP'])) ? $mT316['T316_Codigo_Produto_CIAP'] : $mT316['D001_Codigo_Produto']; 
				$R0300 = array(																#TAM
					$REG         = '0300',													#004*	Texto fixo contendo "0300" 
					$COD_IND_BEM = trim($codigoProduto),						#060	Código individualizado do bem ou componente adotado no controle patrimonial do estabelecimento informante
					$IDENT_MERC  = '1',														#001	1 = bem; 2 = componente
					$DESCR_ITEM  = $mT316['D001_Descricao_Produto'],                           # -     Descrição do bem ou componente (modelo, marca e	outras características necessárias a sua individualização)
					$COD_PRNC    = '',							                            #060    Código de cadastro do bem principal nos casos em que o bem ou componente (campo 02) esteja vinculado a um bem principal.
					$COD_CTA     = $mT316['D014_Codigo_Plano_Conta_Fiscal'],                #060    Código da conta analítica de contabilização do bem ou componente (campo 06 do Registro 0500)
					$NR_PARC     = $mT316['T316_Numero_Parcelas_Apropriacao']               #003    Número total de parcelas a serem apropriadas, segundo a	legislação de cada unidade federada
				);

				$R0300 = str_replace('|', '', $R0300);
				$R0300 = implode("|", $R0300);
				$layout0200 .= '|' . $R0300 . '|' . _SALTO_LINHA;

				# REGISTRO 0305
				$R0305 = array(																#TAM
					$REG         = '0305',													#004*	Texto fixo contendo "0300" 
					$COD_CCUS    = $mT316['D073_Id'],     						            #060	Código do centro de custo onde o bem está sendo ou será utilizado (campo 03 do Registro 0600)
					$FUNC        = $mT316['T316_Descricao_Utilizacao_Bem'],  				#-   	Descrição sucinta da função do bem na atividade do estabelecimento
					$VIDA_UTIL   = ''                                                       # 003   Vida útil estimada do bem, em número de meses
				);

				$R0305 = str_replace('|', '', $R0305);
				$R0305 = implode("|", $R0305);
				$layout0200 .= '|' . $R0305 . '|' . _SALTO_LINHA;
			}
		}
	}

	# REGISTRO 0205
	/*$R0205 = array(																	#TAM
		$REG = '0205',																#004*	
		$DESCR_ANT_ITEM	= 'RODA MOMO',												#-		Descrição anterior do item	
		$DT_INI	= '10112012',														#008*	Data inicial de utilização da descrição do item
		$DT_FIM = '10102012',														#008*	Data final de utilização da descrição do item
		$COD_ANT_ITEM = '00'														#060	Código anterior do item com relação à última informação apresentada.
	);
	$R0205 = implode("|", $R0205);*/
	//$layout0200 .= '|' . $R0205 . '|' . _SALTO_LINHA;
	
	for($i = 0; $i < count($array0450); $i++) {	
		# REGISTRO 0450				
		$R0450 = array(																	#TAM
			$REG = '0450',	
			$COD_INF = $array0450[$i]['codigoInformacao'],																	#004
			$TXT = tirarEspacos(adicionarEspape($array0450[$i]['descricao']))
		);

		$R0450 =  implode("|", $R0450);		
		$layout0200 .= '|' . $R0450 . '|' . _SALTO_LINHA;		
	}

 	for($i = 0; $i < count($array0460); $i++) {	
		# REGISTRO 0460				
		$R0460 = array(																	#TAM
			$REG = '0460',	
			$COD_OBS = $array0460[$i]['D188_Codigo_Observacao'],																	#004
			$TXT = tirarEspacos(adicionarEspape($array0460[$i]['D188_Descricao_Observacao']))
		);

		$R0460 =  implode("|", $R0460);		
		$layout0200 .= '|' . $R0460 . '|' . _SALTO_LINHA;		
	}

	if($lancadoAtivosImobilizados == 'S'){
		# REGISTRO 0500
		#Este registro tem o objetivo de identificar as contas contábeis utilizadas pelo contribuinte informante em sua
		#Contabilidade Geral, relativas às contas referenciadas no registro 0300. Não podem ser informados dois ou mais registros
		#com a mesma combinação de conteúdo nos campos DT_ALT e COD_CTA.

		$D014 = mysql_query("SELECT D014_Codigo_Plano_Conta_Fiscal,
									D014_Data_Inclusao,
									D014_SubConta						
						       FROM T316
					      LEFT JOIN T014 ON T014_Id = T316_T014_Id
						  LEFT JOIN D006 ON D006_Id = T014_D006_Id
						  LEFT JOIN D014 ON D014_Id = D006_D014_Id
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
						   GROUP BY D014_Id
						UNION
							 SELECT D014_Codigo_Plano_Conta_Fiscal,
									D014_Data_Inclusao,
									D014_SubConta						
							   FROM T316
						  LEFT JOIN T008 ON T008_Id = T316_T008_Id
						  LEFT JOIN D006 ON D006_Id = T008_D006_Id
						  LEFT JOIN D014 ON D014_Id = D006_D014_Id
						  LEFT JOIN T007 ON T007_Id = T008_T007_Id
							  WHERE T316_Flag_Ativo_Baixado = 'N'
								AND T316_Numero_Parcelas_Apropriacao > 0
								AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
								AND T007_C004_Id = '{$g['empresaAtual']}' 
								AND T007_Flag_Cancelada != 'S' 
								AND T007_Numero_Protocolo_Nfe > 0
								AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
								AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
						   GROUP BY D014_Id");
		$mD014 = mysql_fetch_array($D014);
		# REGISTRO 0500
		$R0500 = array(																#TAM
			$REG         = '0500',													#004*	Texto fixo contendo "0500" 
			$DT_ALT      = formataData($mD014['D014_Data_Inclusao']),	            #008	Data da inclusão/alteração
			$COD_NAT_CC  = '01',  											        #002   	Código da natureza da conta/grupo de contas: 01 - Contas de ativo; 02 - Contas de passivo; 03 - Patrimônio líquido; 04 - Contas de resultado; 05 - Contas de compensação ; 09 - Outras.
			$IND_CTA     = 'A',                                                     #001    Indicador do tipo de conta:	S - Sintética (grupo de contas); A - Analítica (conta).
			$NIVEL       = '1',														#005    Nível da conta analítica/grupo de contas. 
			$COD_CTA     = $mD014['D014_Codigo_Plano_Conta_Fiscal'],		    	#060    Código da conta analítica/grupo de contas 
			$NOME_CTA    = $mD014['D014_SubConta'],									#060	Nome da conta analítica/grupo de contas.
		);		
		$R0500 = str_replace('|', '', $R0500);
		$R0500 = implode("|", $R0500);
		$layout0200 .= '|' . $R0500 . '|' . _SALTO_LINHA;

		# REGISTRO 0600
		#Este registro tem o objetivo de identificar os centros de custos referenciados no registro 0305 – Informação sobre utilização do bem.

		$D073 = mysql_query("SELECT D073_Id,
									D073_Nome						
						       FROM T316
					      LEFT JOIN T014 ON T014_Id = T316_T014_Id
						  LEFT JOIN D006 ON D006_Id = T014_D006_Id
						  LEFT JOIN D073 ON D073_Id = D006_D073_Id
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
						   GROUP BY D073_Id
						UNION
							 SELECT D073_Id,
									D073_Nome						
							   FROM T316
						  LEFT JOIN T008 ON T008_Id = T316_T008_Id
						  LEFT JOIN D006 ON D006_Id = T008_D006_Id
						  LEFT JOIN D073 ON D073_Id = D006_D073_Id
						  LEFT JOIN T007 ON T007_Id = T008_T007_Id
							  WHERE T316_Flag_Ativo_Baixado = 'N'
								AND T316_Numero_Parcelas_Apropriacao > 0
								AND T316_Data_Inicio_Apropriacao_Credito > '0000-00-00'
								AND T007_C004_Id = '{$g['empresaAtual']}' 
								AND T007_Flag_Cancelada != 'S' 
								AND T007_Numero_Protocolo_Nfe > 0
								AND PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y%m')) <= T316_Numero_Parcelas_Apropriacao
								AND DATE_FORMAT(T316_Data_Inicio_Apropriacao_Credito, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
						   GROUP BY D073_Id");
		$mD073 = mysql_fetch_array($D073);
		# REGISTRO 0600
		$R0500 = array(									#TAM
			$REG         = '0600',						#004*	Texto fixo contendo "0600" 
			$DT_ALT      = '10102012',                  #008	Data da inclusão/alteração.
			$COD_CCUS    = $mD073['D073_Id'],  			#060   	Código do centro de custos
			$CCUS        = $mD073['D073_Nome']          #060    Nome do centro de custos.
		);		
		$R0500 = str_replace('|', '', $R0500);
		$R0500 = implode("|", $R0500);
		$layout0200 .= '|' . $R0500 . '|' . _SALTO_LINHA;
	}
}
$linhas = substr_count($layout0200, CHR(13).CHR(10));
# REGISTRO 0990
$R0990 = array(																		#TAM
	$REG = '0990',																	#004	
	$QTD_LIN_0 = $linhas+1															#-		Quantidade total de linhas do Bloco 0
);
$R0990 = implode("|", $R0990);
$layout0200 .= '|' . $R0990 . '|' . _SALTO_LINHA;

$linhas_bloco_0 = $QTD_LIN_0;
