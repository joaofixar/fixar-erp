<?php
namespace hardness;

class RETORNO_REMESSA {
	public function importarRetorno($arquivo) {
		global $g;
		$fileName = $arquivo;
		$mArquivo = $arquivo;
		$cArquivo = $mArquivo["name"];

		if (empty($arquivo['name'])){
			return 'Selecione o arquivo';
		}

		$arquivo = file($mArquivo['tmp_name']);
		$linhas = array();
		log("Inicío");
		/*
		NOMES DOS CAMPOS E AS POSIÇÕES DO ARRAY
		//#### HEADER ####
		01-TIPO DE REGISTRO			(001 001)  001
		02-TIPO DE OPERAÇÃO  		(002 002)  001
		03-LITERAL DE RETORNO 		(003 009)  007
		04-CÓDIGO DO SERVIÇO 		(010 011)  002
		05-LITERAL DE SERVIÇO 		(012 019)  008
		06-COMPLEMENTO DO REGISTRO  (020 026)  007
		07-COOPERATIVA				(027 030)  004
		08-DV COOPERATIVA			(031 031)  001
		09-CÓDIGO DO CLIENTE		(032 039)  008
		10-DV CÓDIGO DO CLIENTE		(040 040)  001
		11-NÚMERO DA CONTA CLIENTE	(041 046)  006
		12-NOME DO BENEFICIÁRIO  	(047 076)  030
		13-IDENTIFICAÇÃO DO BANCO	(077 094)  018
		14-DATA DE GRAVAÇÃO			(095 100)  006
		15-SEQUÊNCIAL RETORNO		(101 107)  007
		16-COMPLEMENTO DO REGISTRO	(108 394)  287
		17-Nº SEQ. DO REGISTRO 	 	(395 400)  006
		
---
		//#### BODY ####
		1-TIPO DE REGISTRO 			(001 001)  01
		2-CÓDIGO DE INSCRIÇÃO 		(002 003)  02
		3-NÚMERO DE INSCRIÇÃO 		(004 017)  14
		4-AGÊNCIA 					(018 021)  04
		5-DV AGENCIA				(022 022)  01
		6-CONTA 					(023 030)  08
		7-DAC 						(031 031)  01
		8-BRANCOS 					(032 037)  06
		9-USO DA EMPRESA 			(038 062)  25
		10-NOSSO NÚMERO				(063 073)  11
		11-BRANCOS 					(074 074)  01
		12-CARTEIRA 				(075 076)  02
		13-NOSSO NÚMERO 			(077 080)  04
		14-DAC NOSSO NÚMERO 		(081 082)  02
		15-BRANCOS 					(083 085)  13
		16-CARTEIRA 				(086 108)  01
		17-CÓD. DE OCORRÊNCIA 		(089 110)  02
		18-DATA DE OCORRÊNCIA 		(090 116)  06
		19-Nº DO DOCUMENTO 			(095 126)  10
		20-NOSSO NÚMERO 			(096 134)  08
		21-BRANCOS 					(101 146)  12
		22-VENCIMENTO 				(106 152)  06
		23-VALOR DO TÍTULO 			(107 165)  11
		24-CÓDIGO DO BANCO 			(109 168)  03
		25-AGÊNCIA COBRADORA 		(111 172)  04
		26-DAC AG. COBRADORA 		(117 173)  01
		27-ESPÉCIE 					(127 175)  02
		28-TARIFA DE COBRANÇA 		(147 188)  11
		29-							(153 165)  13	
		30-BRANCOS 					(166 214)  26
		31-VALOR DO IOF 			(169 227)  11
		32-VALOR ABATIMENTO 		(173 240)  11
		33-DESCONTOS 				(174 253)  11
		34-VALOR PRINCIPAL 			(176 266)  11
		35-JUROS DE MORA/MULTA 		(182 279)  11
		36-OUTROS CRÉDITOS 			(189 292)  11
		37-BOLETO DDA 				(202 293)  01
		38-BRANCOS 					(215 295)  02
		39-DATA CRÉDITO 			(228 301)  06
		40-INSTR.CANCELADA 			(241 305)  04
		41-BRANCOS 					(254 311)  06
		42-ZEROS 					(267 324)  13
		43-NOME DO SACADO 			(280 354)  30
		44-BRANCOS 					(293 377)  23
		45-ERROS / MSG INFORMATIVA  (306 385)  08
		46-BRANCOS 					(319 392)  07
		47-CÓD. DE LIQUIDAÇÃO 		(320 394)  02
		48-NÚMERO SEQÜENCIAL 		(321 400)  06
		49-COMPLEMENTO DO REGISTRO	(333 342)  10
		50-CPF/CNPJ				 	(343 357)  14
		51-COMPLEMENTO DO REGISTRO  (358 394)  38
		52-SEQUENCIAL DO REGISTRO 	(395 400)  06
 
		//#### TRAILLER ####
		1-TIPO DE REGISTRO 			(001 001)  01
		2-CÓDIGO DE RETORNO 		(002 002)  01
		3-CÓDIGO DE SERVIÇO 		(003 004)  02
		4-CÓDIGO DO BANCO 			(005 007)  03
		5-BRANCOS 					(008 017)  10
		6-QTDE. DE TÍTULOS 			(018 025)  08
		7-VALOR TOTAL 				(026 039)  12
		8-AVISO BANCÁRIO 			(040 047)  08
		9-BRANCOS 					(048 057)  10
		10-QTDE. DE TÍTULOS 		(058 065)  08
		11-VALOR TOTAL 				(066 079)  12
		12-AVISO BANCÁRIO 			(080 087)  08
		13-BRANCOS 					(088 177)  90
		14-QTDE. DE TÍTULOS 		(178 185)  08
		15-VALOR TOTAL 				(186 199)  12
		16-AVISO BANCÁRIO 			(200 207)  08
		17-CONTROLE DO ARQUIVO 		(208 212)  05
		18-QTDE DE DETALHES 		(213 220)  08
		19-VLR TOTAL INFORMADO 		(221 234)  12
		20-BRANCOS 					(235 394)  160
		21-NÚMERO SEQÜENCIAL 		(395 400)  06
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{8})(.{7})(.{4})(.{1})(.{8})(.{1})(.{6})(.{30})(.{18})(.{6})(.{7})(.{287})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{2})(.{3})(.{4})(.{25})(.{50})(.{30})(.{8})(.{30})(.{2})(.{8})(.{8})(.{11})(.{212})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{4})(.{1})(.{8})(.{1})(.{6})(.{25})(.{11})(.{1})(.{2})(.{4})(.{2})(.{3})(.{3})(.{1})(.{5})(.{1})(.{5})(.{5})(.{1})(.{2})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{4})(.{1})(.{2})(.{6})(.{7})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{1})(.{1})(.{12})(.{10})(.{14})(.{38})(.{6})/", $linha, $posicoes);
			}//                 1     2      3     4     5     6     7     8     9      10     11    12   13    14    15    16     17   18     19    20    21    22    23    24    25    26     27     28    29     30    31    32    33    34    35    36     37     38     39     40     41     42     43     44     45     46    47    48     49     50     51     52                                                                            
   			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $this->converteData($linha[14]);
				$codigoBanco = $linha[13];
				$linhaHeader = $linha[0];

				$agencia 	 = $linha[7];
				$conta 		 = floor($linha[11]);
				$conta 		 = substr($conta,0,-1); // recortando o ultimo dígito, DV da conta conta corrente
				$codCliente  = $linha[9];
				$dvCliente   = $linha[10];
				$cliente = "$codCliente$dvCliente";
//				$dvConta 	 = $linha[9];
				
			}
			if($key != 0 && $key != (count($linhas)-1)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
				$titulos = ($linha[52]-1);
			}
			if($key == (count($linhas)-1)){ //DADOS DO TRAILLER
				$linhaTrailler = $linha[0];
			}
		}

		//Verifica se o arquivo ja foi enviado
		$sql = mysql_query("SELECT * FROM T037 WHERE T037_Linha_Header = '{$linhaHeader}'");
		if(mysql_num_rows($sql) > 0){
			return 'Este arquivo ja foi enviado';
		}
		//$conteudoArquivo = $linhaHeader . '\n' . $conteudo . $linhaTrailler;
		$conteudoArquivo = $linhaHeader;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $conteudo . $linhaTrailler;

		log("Numero da conta:".$conta);
		$sqlD007 = mysql_query("SELECT D007_Id FROM D007 WHERE D007_Numero_Agencia = '{$agencia}' AND D007_Numero_Conta='{$conta}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com a agência {$agencia} e conta {$conta}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com a agência {$agencia} e conta {$conta}, por favor verifique.";
		}
		$rowD007 = mysql_fetch_array($sqlD007);
		//Registro na tabela de histórico de remessas e retornos com upload
		$conteudoArquivo = mysql_real_escape_string($conteudoArquivo);
		$T037 = "INSERT INTO T037 (
				T037_C004_Id, T037_Nome_Arquivo, T037_Data_Processamento, T037_Numero_Titulos, T037_Linha_Header, T037_Linha_Trailler, T037_Codigo_Banco, T037_Conteudo_Arquivo, T037_D007_Id
			) VALUES (
				'{$g['empresaAtual']}', '{$fileName['name']}', '{$dataRetorno}', '{$titulos}', '{$linhaHeader}', '{$linhaTrailler}', '{$codigoBanco}', '{$conteudoArquivo}', '{$rowD007['D007_Id']}'
			)";
		mysql_query($T037);
		$T037_Id = $g['mysqlLastId'];

		foreach ($linhas as $key => $linha) {
			if($key != 0 && $key != (count($linhas)-1)){ //DADOS DO BODY
				/*
				/ ALTERAR O NUMERO DA LINHA DE ACORDO COM O LAYOUT DO BANCO
				*/
				$usoEmpresa 		 = $linha[9];
				$nossoNumero 		 = $linha[10];
				$digitoNossoNumero   = $linha[11];
				$prefixoTitulo       = $linha[15];
				$taxaDesconto        = $this->converteNumero($linha[20]);
				$taxaValorIOF 		 = $this->converteNumero($linha[21]);
				$carteira 			 = $linha[23];
				$codigoNaturezaRec 	 = $linha[24]; //atributo como comando/movimento surgiu dúvida se não seria o campo 14 código de baixa/Recusa
				$dataRecebimento 	 = $this->converteData($linha[25]);
				$numeroDocumento     = $linha[26];
				$dataVencimento 	 = $this->converteData($linha[28]);
				$valorTitulo 		 = $this->converteNumero($linha[29]);
				$codigoBancoRecebedor= $linha[30];
				$agenciaRecebedora 	 = $linha[31];
				$dvAgenciaRecebedora = $linha[32];
				$especieTitulo 		 = $linha[33];
				$dataCredito 		 = $this->converteData($linha[34]);
				$valorTarifa      	 = $this->converteNumero($linha[35]);
				$valorOutrasDespesas = $this->converteNumero($linha[36]);
				$valorJurosDesconto  = $this->converteNumero($linha[37]);
				$valorIOFDesconto    = $this->converteNumero($linha[38]);
				$valorAbatimento 	 = $this->converteNumero($linha[39]);
				$valorDesconto 		 = $this->converteNumero($linha[40]);
				$valorRecebido 		 = $this->converteNumero($linha[41]);
				$valorJurosMora 	 = $this->converteNumero($linha[42]);
				$valorOutrosCreditos = $this->converteNumero($linha[43]);
				$valorAbatimentoNaoAproveitado = $this->converteNumero($linha[44]);
				$valorLancamento 	 = $this->converteNumero($linha[45]);
				$indicativoDC      	 = $linha[46];
				$indicativoValor	 = $linha[47];
				$valorAjuste 		 = $this->converteNumero($linha[48]);
				$valorRecebido       = $this->converteNumero($linha[41] + $linha[36]);
				$codigoMotivo 		 = $linha[14]; //atribuido como código de baixa/recusa, surgiu dúvida se não seria o campo 24, comando/movimento
				$codigoNossoNumero   = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = substr($nossoNumero,-6) . '-' . $digitoNossoNumero;


				//Verifica os boletos da empresa atual, para o banco corrente
				$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_C004_Id = '{$g['empresaAtual']}' AND T117_D007_Id='{$rowD007['D007_Id']}' AND (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}'");
				if(mysql_num_rows($sql) > 0){
					//guarda os dados em índices associativos, usando o nome do campo como chave.
					$res = mysql_fetch_array($sql);
					$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					log("1-T002_Id".$T002_Id);
				} else {
					//verifica os boletos de acordo com o banco 
					$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_D007_Id='{$rowD007['D007_Id']}' AND (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
					if(mysql_num_rows($sql) > 0){
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					} else {
						$sql = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$usoEmpresa}' AND T117_D007_Id='{$rowD007['D007_Id']}'");
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					}
				}

				if ($T002_Id<=0){
					//Verifica os boletos da empresa atual, em qualquer banco caso não encontre para o banco corrente.
					$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_C004_Id = '{$g['empresaAtual']}' AND (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
					if(mysql_num_rows($sql) > 0){
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
						log("2-T002_Id".$T002_Id);
					} else {
						$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
						if(mysql_num_rows($sql) > 0){
							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
							log("22-T002_Id".$T002_Id);
						} else {
							$sql = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$usoEmpresa}'");
							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
							log("222-T002_Id".$T002_Id);
						}
					}
				}

				$T038_Flag_Autorizado = '';
				if(!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
					$T038_Flag_Autorizado = 'N';
				}

				$sql = "INSERT INTO T038 (
										T038_T037_Id, T038_T002_Id, T038_C004_Id, T038_Numero_Documento, T038_Nosso_Numero,
										T038_Digito_Verificador_Nosso_Numero, T038_Codigo_Natureza_Recebimento, T038_Carteira, T038_Data_Recebimento, T038_Codigo_Motivo,
										T038_Data_Vencimento, T038_Valor_Titulo, T038_Codigo_Banco_Recebedor, T038_Prefixo_Agencia_Recebedora, T038_Digito_Verificador_Agencia_Recebedora,
										T038_Especie_Titulo, T038_Data_Credito, T038_Valor_Outros_Recebimentos, T038_Valor_Taxa_IOF, T038_Valor_Abatimento, T038_Valor_Outras_Despesas,
										T038_Valor_Desconto, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String,
										T038_Flag_Autorizado, T038_Valor_Tarifa,
										T038_Valor_Lancamento, T038_Prefixo_Titulo, T038_Valor_Taxa_Desconto, T038_Valor_Abatimento_Nao_Aproveitado, T038_Valor_IOF_Desconto,
										T038_Flag_Debito_Credito, T038_Flag_Indicador_Valor, T038_Valor_Ajuste
									) VALUES (
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$numeroDocumento}', '{$nossoNumero}',
										'{$digitoNossoNumero}', '{$codigoNaturezaRec}', '{$carteira}', '{$dataRecebimento}', '{$codigoMotivo}',
										'{$dataVencimento}', '{$valorTitulo}', '{$codigoBancoRecebedor}', '{$agenciaRecebedora}', '{$dvAgenciaRecebedora}',
										'{$especieTitulo}', '{$dataCredito}', '{$valorOutrosCreditos}', '{taxaValorIOF}', '{$valorAbatimento}', '{$valorOutrasDespesas}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[47]}', '{$linha[0]}',
										'{$T038_Flag_Autorizado}', '{$valorTarifa}',
										'{$valorLancamento}', '{$prefixoTitulo}','{$taxaDesconto}', '{$valorAbatimentoNaoAproveitado}', '{$valorIOFDesconto}',
										'{$indicativoDC}', '{$indicativoValor}', '{$valorAjuste}'
									);";
				mysql_query($sql);
			}
		}
		
		//Copia o arquivo para a pasta temp do usuario
		$dir = $g['pathDadosUsuario']."arq_temp/";
		is_dir($dir) or mkdir($dir, 0777, true);
		$cArquivoTemp=$g['pathDadosUsuario']."arq_temp/".basename($cArquivo);
		if (!@move_uploaded_file($mArquivo["tmp_name"], $cArquivoTemp)) {
			return "Erro ao tentar copiar arquivo temporario: " . $mArquivo['tmp_name'] . " para: ".$cArquivoTemp;
		}
		return true;
	}

	public function processarRetorno($T037_Id, $opcoes = array(), &$detalhe = null){
		require_once('bibliotecas/classes/FIN001.php');
		$FIN001 = new FIN001();
		global $g;
		$limite = isset($opcoes['limite']) ? (int)$opcoes['limite'] : 0;
		$ultimoId = isset($opcoes['ultimoId']) ? (int)$opcoes['ultimoId'] : 0;
		$ultimoIdLido = $ultimoId;
		$lidos = 0;
		$extraWhere = '';
		if ($ultimoId > 0) {
			$extraWhere .= " AND T038_Id > '{$ultimoId}'";
		}
		$extraLimit = '';
		if ($limite > 0) {
			$extraLimit = " LIMIT {$limite}";
		}
		$T038 = mysql_query("SELECT * 
							   FROM T038 
						  LEFT JOIN T037 ON T037_Id = T038_T037_Id 
						  LEFT JOIN T002 ON T002_Id = T038_T002_Id 
						  LEFT JOIN D027 ON D027_Id=T002_D027_Id 
						  LEFT JOIN D007 ON D007_Id=D027_D007_Id 
						      WHERE T038_T037_Id = '{$T037_Id}'{$extraWhere} ORDER BY T038_Id ASC{$extraLimit}");
		while($res = mysql_fetch_array($T038)){
			$lidos++;
			$ultimoIdLido = $res['T038_Id'];
			$codigoBanco = $res['T037_Codigo_Banco'];
			$dataBaixa = $res['T038_Data_Credito'];
			$dataRecebimento = $res['T038_Data_Recebimento'];
			if($res['T038_Valor_Juros_Mora']<=0){$res['T038_Valor_Juros_Mora']='';}
			if(!empty($res['T002_Id'])){
				$T002_Id = $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'];
				
				$outrosJuros = false;
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ // Outros juros que vem direamente no valor recebido
					$outrosJuros = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}
				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N') {
					if($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15'){ //Titulos a serem baixados
						if($res['T038_Valor_Juros_Mora'] > 0){ //Juros
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(), '{$res['T038_Valor_Juros_Mora']}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'JM');
						}

						if($res['T038_Valor_Taxa_IOF'] > 0){ //IOF
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - IOF', CURDATE(), '{$res['T038_Valor_Taxa_IOF']}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'I');
						}

						if($res['T038_Valor_Outros_Recebimentos'] > 0){ //Outros Créditos
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Outros créditos', CURDATE(), '{$res['T038_Valor_Outros_Recebimentos']}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OR');
						}

						if($outrosJuros === true && empty($res['T038_Valor_Juros_Mora']) && $res['D007_Baixar_Automatico_Valor_Excedido_Retorno'] == 'S'){ // Outros juros que vem diretamente no valor recebido
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - JUROS', CURDATE(), '{$valorDiferenca}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$res['outrosJuros'] = $valorDiferenca;
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OJ');
						}

						mysql_query("UPDATE T038 SET T038_C007_Id_Processado = '{$g['usuarioAtual']}', T038_Data_Hora_Processado = CURRENT_TIMESTAMP() WHERE T038_Id = '{$res['T038_Id']}'");
						mysqli_query("CALL T002_Gravar_Totalizacao('{$res['T002_Id']}')");
						//Busca o codigo do banco
						//$sql = mysql_query("SELECT D007_Id FROM D007 LEFT JOIN D062 ON D062_Id = D007_D062_Id WHERE D062_Numero_Banco = '{$codigoBanco}' AND D007_C004_Id = '{$g['empresaAtual']}'");
						//$res = mysql_fetch_array($sql);
						//Baixa os Titulos
						//$data = $dataCredito;
						$extra = base64_encode("WHERE (T002_Id = '{$T002_Id}')");
						$retorno = $FIN001->baixarRetorno($res['T037_D007_Id'], $dataBaixa, $extra, $dataRecebimento);
					}
				} 
			}
		}
		$detalhe = array(
			'ultimoId' => $ultimoIdLido,
			'lidos' => $lidos,
		);
		return true;
	}

	public function converteData($data){ //Recebe uma data DDMMAA e converte para data formato banco
		if($data > 0){
			$dia = substr($data, 0, 2);
			$mes = substr($data, 2, 2);
			$ano = substr($data, 4, 2);
			return date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano)); 
		} else {
			return "0000-00-00";
		}
	}

	public function converteNumero($numero){ //Recebe um numero inteiro e converte os 2 ultimos para decimais
		if(isset($numero)) {
			$decimal = substr($numero, -2);
			$numero = substr($numero, 0, -2);			
			return $numero . '.' . $decimal;
		} else {
			return 0;
		}
	}

	public function formataNossoNumero($numero, $digito, $carteira){ //Formata nosso numero com carteira e digito verificador
		if(isset($numero)){
			$numero = '0' . $carteira . '/'. $numero . '-' . $digito; 
			return $numero;
		} else {
			return 0;
		}
	}
	
}
