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

		/*
		NOMES DOS CAMPOS E AS POSIÇÕES DO ARRAY
		//#### HEADER ####
		1 - Identificação do Registro (001 001) 1
		2 - Identificação do Arquivo de Retorno (002 002) 1
		3 - Literal RETORNO (003 009) 7
		4 - Código do Serviço (010 011) 2
		5 - Literal Serviço (012 026) 15
		6 - Código da Empresa (027 046) 20
		7 - Nome da Empresa por extenso (047 076) 30
		8 - Nº do Bradesco na Câmara Compensação (077 079) 3
		9 - Nome do Banco por extenso (080 094) 15
		10 - Data da gravação do arquivo (095 100) 6
		11 - Densidade de Gravação (101 108) 8
		12 - Nº Aviso Bancario (109 113) 5
		13 - Branco (114 379) 266
		14 - Data do Crédito (380 385) 6
		15 - Branco (386 394) 9
		16 - Nº Sequencial de Registro (395 400) 6

		//#### BODY ####
		1 - Identificação do Registro (001 001) 1
		2 - Tipo de Inscrição Empresa (002 003) 2
		3 - Nº Inscrição da Empresa (004 017) 14
		4 - Zeros (018 020) 3
		5 - Identificação da Empresa Cedente no Banco (021 037) 17
		6 - N° Controle do Participante (038 062) 25
		7 - Zeros (063 070) 8
		*8 - Identificação do Titulo no Banco (071 082) 12 #NOSSO NUMERO
		9 - Uso do Banco (083 092) 10
		10 - Uso do Banco (093 104) 12
		11 - Indicador de Rateio Crédito (105 105) 1
		12 - Zeros (106 107) 2
		13 - Carteira (108 108) 1
		*14 - Identificação de Ocorrência (109 110) 2 @PAG 46
		*15 - Data Ocorrencia no Banco (111 116) 6
		*16 - Numero do Documento (117 126) 10
		*17 - Identificação do Titulo no Banco (127 146) 20 #NOSSO NUMERO
		*18 - Data Vencimento do Titulo (147 152) 6
		*19 - Valor do Titulo (153 165) 13
		20 - Banco Cobrador (166 168) 3
		21 - Agencia Cobradora (169 173) 5
		22 - Espécie do Titulo (174 175) 2
		*23 - Despesas de cobrança para os Códigos de Oc. (176 188) 13
		*24 - Outras Despesas(Custas de Protesto) (189 201) 13
		*25 - Juros de Operacao em Atraso (202 214) 13
		*26 - IOF Devido (215 227) 13
		*27 - Abatimento Concedido sobre titulo (228 240) 13
		*28 - Desconto Concedido (241 253) 13
		*29 - Valor Pago (254 266) 13
		*30 - Juros de Mora (267 279) 13
		*31 - Outros Creditos (280 292) 13
		32 - Brancos (293 294) 2
		*33 - Motivo do Codigo de Oc. 25 (295 295) 1
		*34 - Data do Crédito (296 301) 6
		35 - Origem Pagamento (302 304) 3
		36 - Brancos (305 314) 10
		37 - Quando cheque Bradesco informe 0237 (315 318) 4
		*38 - Motivo das Rejeicoes para os Códigos de Oc da posição 109 a 110 (319 329) 10
		39 - Brancos (329 368) 40
		*40 - Numero do Cartorio (369 370) 2
		*41 - Numero do Protocolo (371 380) 10
		42 - Brancos (381 394) 14
		43 - Nº Sequencial de Registro (395 400) 6

		//#### TRAILLER ####
		1 - Identificação do Registro (001 001) 1
		2 - Identificação do Retorno (001 001) 1
		3 - Identificação Tipo de Registro (003 004) 2
		4 - Código do Banco (005 007) 3
		5 - Brancos (008 017) 10
		6 - Quantidade de Titulos em Cobrança (018 025) 8
		7 - Valor Total em Cobrança (026 039) 14
		8 - N° do Aviso Bancario (040 047) 8
		9 - Brancos (048 057) 10
		10 - Quantidade de Registros 02 (058 062) 5
		11 - Valor dos Registros 02 (063 074) 12
		12 - Valor dos Registros 06 - Liquidação (075 086) 12
		13 - Quantidade dos Registros 06 - Liquidação (087 091) 5
		14 - Valor dos Registros 06 (092 103) 12
		15 - Quantidade dos Registros 09/10 (104 108) 5
		16 - Valor dos Registros 09/10 (109 120) 12
		17 - Quantidade dos Registros 13 (121 125) 5
		18 - Valor dos Registros 13 (126 137) 12
		19 - Quantidade dos Registros 14 (138 142) 5
		20 - Valor dos Registros 14 (143 154) 12
		21 - Quantidade dos Registros 12 (155 159) 5
		22 - Valor dos Registros 12 (160 171) 12
		23 - Quantidade dos Registros 19 (172 176) 5
		24 - Valor dos Registros 19 (177 188) 12
		25 - Brancos (189 362) 174
		26 - Valor Total dos Rateios Efetuados (363 377) 15
		27 - Quantidade Total dos Rateios Efetuados (378 385) 8
		28 - Brancos (386 394) 9
		29 - Numero Sequencial do Registro (395 400) 6
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{20})(.{30})(.{3})(.{15})(.{6})(.{8})(.{5})(.{266})(.{6})(.{9})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{10})(.{8})(.{14})(.{8})(.{10})(.{5})(.{12})(.{12})(.{5})(.{12})(.{5})(.{12})(.{5})(.{12})(.{5})(.{12})(.{5})(.{12})(.{5})(.{12})(.{174})(.{15})(.{8})(.{9})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{3})(.{17})(.{25})(.{8})(.{12})(.{10})(.{12})(.{1})(.{2})(.{1})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{5})(.{2})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{2})(.{1})(.{6})(.{3})(.{10})(.{4})(.{10})(.{40})(.{2})(.{10})(.{14})(.{6})/", $linha, $posicoes);
			}
			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno   = $this->converteData($linha[10]);
				$codigoBanco   = $linha[8];
				$linhaHeader   = $linha[0];

				$codigoEmpresa = $linha[6];
			}
			if($key != 0 && $key != (count($linhas)-1)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
			}
			if($key == (count($linhas)-1)){ //DADOS DO TRAILLER
				$linhaTrailler = $linha[0];
				$titulos = ($linha[29]-2);
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

		$codigoEmpresa = ltrim($codigoEmpresa, '0');
		$sqlD007 = mysql_query("SELECT D007_Id FROM D007 WHERE D007_Numero_Convenio = '{$codigoEmpresa}' OR D007_Codigo_Cedente = '{$codigoEmpresa}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com o código de cedente ou número do convenio igual a {$codigoEmpresa}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com o código de cedente ou número do convenio igual a {$codigoEmpresa}, por favor verifique.";
		}
		$rowD007 = mysql_fetch_array($sqlD007);

		$conteudoArquivo = mysql_real_escape_string($conteudoArquivo);
		//Registro na tabela de histórico de remessas e retornos com upload
		$T037 = "INSERT INTO T037 (
								T037_C004_Id, T037_Nome_Arquivo, T037_Data_Processamento, T037_Numero_Titulos, T037_Linha_Header, T037_Linha_Trailler, T037_Codigo_Banco, T037_Conteudo_Arquivo, T037_D007_Id
							) VALUES (
								'{$g['empresaAtual']}', '{$fileName['name']}', '{$dataRetorno}', '{$titulos}', '{$linhaHeader}', '{$linhaTrailler}', '{$codigoBanco}', '{$conteudoArquivo}', '{$rowD007['D007_Id']}'
							)";
		mysql_query($T037);
		$T037_Id = $g['mysqlLastId'];

		foreach ($linhas as $key => $linha) {
			if($key != 0 && $key != (count($linhas)-1)){ //DADOS DO BODY
				$total += $linha[19];
				$nossoNumero = substr($linha[17], 8, -1);
				$digitoNossoNumero = substr($linha[17], -1, 1);
				$carteira = $linha[13];
				$dataRecebimento = $this->converteData($linha[15]);
				$dataVencimento = $this->converteData($linha[18]);
				$dataCredito = $this->converteData($linha[34]);
				$valorRecebido = $this->converteNumero($linha[29]);
				$valorTitulo = $this->converteNumero($linha[19]);
				$valorAbatimento = $this->converteNumero($linha[27]);
				$valorDesconto = $this->converteNumero($linha[28]);
				$valorJurosMora = $this->converteNumero($linha[30]);
				$valorIOF = $this->converteNumero($linha[26]);
				$valorOutrosCreditos = $this->converteNumero($linha[31]);
				$contaComZeroEsquerda = substr($linha[5], 9,-1);
				$contaSemZeroEsquerda = ltrim($contaComZeroEsquerda,'0');
				$descricaoOcorr = '';
				if($linha[14] == '06'){
					$descricaoOcorr = 'Liquidação normal';
				} else if($linha[14] == '15'){
					$descricaoOcorr = 'Liquidação em Cartorio';
				}

				$codigoNossoNumero = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = $nossoNumero . '-' . $digitoNossoNumero;
				$sql = mysql_query("SELECT * 
								 	FROM T002 
									LEFT JOIN T117 ON T117_Id = T002_T117_Id 
									LEFT JOIN D007 ON D007_Id=T117_D007_Id 
									WHERE (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}') 
									AND (D007_Numero_Conta='{$contaComZeroEsquerda}' OR D007_Numero_Conta = '{$contaSemZeroEsquerda}')");
				$res = mysql_fetch_array($sql);
				$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;

				$T038_Flag_Autorizado = '';
				if(!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
					$T038_Flag_Autorizado = 'N';
				}

				$sql = "INSERT INTO T038 (
										T038_T037_Id, T038_T002_Id, T038_C004_Id, T038_Numero_Documento, T038_Nosso_Numero,
										T038_Digito_Verificador_Nosso_Numero, T038_Codigo_Natureza_Recebimento, T038_Descricao_Natureza_Recebimento, T038_Carteira, T038_Data_Recebimento, T038_Codigo_Motivo,
										T038_Data_Vencimento, T038_Valor_Titulo, T038_Codigo_Banco_Recebedor, T038_Prefixo_Agencia_Recebedora, T038_Digito_Verificador_Agencia_Recebedora,
										T038_Especie_Titulo, T038_Data_Credito, T038_Valor_Outros_Recebimentos, T038_Valor_Taxa_IOF, T038_Valor_Abatimento,
										T038_Valor_Desconto, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String, T038_Flag_Autorizado
									) VALUES (
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$linha[16]}', '{$nossoNumero}',
										'{$digitoNossoNumero}', '{$linha[14]}', '{$descricaoOcorr}', '{$linha[13]}', '{$dataRecebimento}', '{$linha[38]}',
										'{$dataVencimento}', '{$valorTitulo}', '{$linha[20]}', '{$linha[21]}', '0',
										'0', '{$dataCredito}', '{$valorOutrosCreditos}', '{$valorIOF}', '{$valorAbatimento}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[43]}', '{$linha[0]}', '{$T038_Flag_Autorizado}'
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
		$mensagemErro='';
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
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					if($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15' || ($res['T038_Codigo_Natureza_Recebimento'] == '17' && $res['T038_Data_Credito'] != '0000-00-00')){ //Titulos a serem baixados
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
						//Busca o CNPJ da empresa atual
						$C004 = mysql_query("SELECT C004_Cnpj FROM C004 where C004_Id = '{$g['empresaAtual']}'");
						$mC004 = mysql_fetch_array($C004);
						//Busca o codigo do banco
						//$sql = mysql_query("SELECT D007_Id FROM D007 LEFT JOIN D062 ON D062_Id = D007_D062_Id WHERE D062_Numero_Banco = '{$codigoBanco}' AND Substring(D007_Cnpj,1,10) = Substring('{$mC004['C004_Cnpj']}',1,10)");
						//$res = mysql_fetch_array($sql);
						//Baixa os Titulos
						//$data = $dataCredito;
						$extra = base64_encode("WHERE (T002_Id = '{$T002_Id}')");
						$retorno = $FIN001->baixarRetorno($res['T037_D007_Id'], $dataBaixa, $extra, $dataRecebimento);
						if ($retorno !== true){
							$mensagemErro.=$retorno.'<br />';
						}
					}
				}
			}
		}
		$detalhe = array(
			'ultimoId' => $ultimoIdLido,
			'lidos' => $lidos,
		);
		if (!empty($mensagemErro)){
			return $mensagemErro;
		} else {
			return true;
		}
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
