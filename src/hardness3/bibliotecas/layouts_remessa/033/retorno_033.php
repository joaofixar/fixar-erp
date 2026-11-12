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
		De    Até   Picture   Conteúdo
		001   001   9(001)    Código do registro = 0
		002   002   9(001)    Código da remessa = 2 
		003   009   X(007)    Literal de transmissão = RETORNO
		010   011   9(002)    Código do serviço = 01
		012   026   X(015)    Literal de serviço = COBRANCA
		027   030   9(004)    Código da agência cedente (nota 2)
		031   038   9(008)    Conta movimento cedente (nota 2)
		039   046   9(008)    Conta cobrança cedente (nota 2)
		047   076   X(030)    Nome do cedente
		077   079   9(003)    Código do Banco = 033
		080   094   X(015)    Nome do Banco = SANTANDER
		095   100   9(006)    Data do movimento
		101   110   9(010)    Zeros
		111   117   X(007)    Código do cedente
		118   391   X(274)    Brancos
		392   394   9(003)    Número da versão
		395   400   9(006)    Número seqüencial do registro no arquivo
  
		
		//#### BODY ####
		    De    Até Picture Conteúdo
		1 - 001   001 9(001) Código do registro = 1
		2 - 002   003 9(002) Tipo de inscrição do cedente
							01 = CPF
							02 = CGC
		3 - 004   017 9(014) CGC ou CPF do cedente
		4 - 018   021 9(004) Código da agência cedente (nota 2)
		5 - 022   029 9(008) Conta movimento cedente (nota 2)
		6 - 030   037 9(008) Conta cobrança cedente (nota 2)
		7 - 038   062 X(025) Número de controle do participante, para controle por parte do cedente
		8 - 063   070 9(008) Nosso número
		9 - 071   107 X(037) Brancos
		10- 108   108 9(001) Código da carteira:
			  			2 = ELETRÔNICA COM REGISTRO
			  			3 = CAUCIONADA ELETRÔNICA
			  			4 = COBRANÇA SEM REGISTRO
			  			5 = RÁPIDA COM REGISTRO (BLOQUETE EMITIDO PELO CLIENTE)
			  			7 = DESCONTADA ELETRÔNICA
		11- 109   110 9(002) Código de ocorrência:
			  			01 = título não existe
			  			02 = entrada tít. confirmada
			  			03 = entrada tít. rejeitada
			  			06 = liquidação
			  			07 = liquidação por conta
			  			08 = liquidação por saldo
			  			09 = baixa automática
			  			10 = tít. baix. conf. instrução ou por título protestado
			  			11 = em ser
			  			12 = abatimento concedido
			  			13 = abatimento cancelado
			  			14 = prorrogação de vencimento
			  			15 = Enviado para Cartório
			  			16 = tít. já baixado/liquidado
			  			17 = liquidado em cartório
			  			21 = Entrada em Cartório
			  			22 = Retirado de cartório
			  			24 = Custas de Cartório
			  			25 = Protestar Título
			  			26 = Sustar Protesto
		12- 111   116 9(006) Data da ocorrência
		13- 117   126 X(010) Seu número
		14- 127   134 9(008) Nosso número
		15- 135   136 9(002) Código Original da Remessa Obs.: Esse campo terá conteúdo diferente de 0(zeros) caso ocorra erro no processamento da remessa - Nota 5
		16- 137   139 X(003) Código do Erro (1ª ocorrência), será preenchido com brancos quando não ocorrer erro Nota 5
		17- 140   142 X(003) Código do Erro (2ª ocorrência), será preenchido com brancos quando não ocorrer erro Nota 5
		18- 143   145 X(003) Código do Erro (3ª ocorrência), será preenchido com brancos quando não ocorrer erro Nota 5
		19- 146   146 X(001) Brancos
		20- 147   152 9(006) Data de vencimento do título COBRANÇA – GRUPO SANTANDER BRASIL 10/2009 12
		21- 153   165 9(013)v99 Valor do título em moeda corrente
		22- 166   168 9(003) Número do Banco cobrador
		23- 169   173 9(005) Código da agência recebedora do título
		24- 174   175 9(002) Espécie de documento:
				  			01 = DUPLICATA
				  			02 = NOTA PROMISSÓRIA
				  			03 = APÓLICE OU NOTA DE SEGURO
				  			05 = RECIBO
				  			06 = DUPLICATA DE SERVIÇO
				  			07 = LETRA DE CÂMBIO
		25- 176   188 9(013)v99 Valor da tarifa cobrada
		26- 189   201 9(013)v99 Valor de outras despesas
		27- 202   214 9(013)v99 Valor dos juros de atraso
		28- 215   227 9(013)v99 Valor do IOF devido
		29- 228   240 9(013)v99 Valor do abatimento concedido
		30- 241   253 9(013)v99 Valor do desconto concedido
		31- 254   266 9(013)v99 Valor total recebido
		32- 267   279 9(013)v99 Valor dos juros de mora
		33- 280   292 9(013)v99 Valor de outros créditos
		34- 293   293 X(001) Branco
		35- 294   294 X(001) Código de aceite = N
		36- 295   295 X(001) Branco
		37- 296   301 9(006) Data do crédito
		38- 302   337 X(036) Nome do sacado
		39- 338   338 X(001) Identificador do Complemento (nota 2)
		40- 339   340 9(002) Unidade de valor moeda corrente = 00
		41- 341   353 9(8)v9(5) Valor do título em outra unidade de valor
		42- 354   366 9(8)v9(5) Valor do IOC em outra unidade de valor
		43- 367   379 9(013)v99 Valor do débito ou crédito
		44- 380   380 X(001) D = DÉBITO
			  		   C = CRÉDITO
		45- 381   391 X(011) Brancos
		46- 392   394 9(003) Número da versão
		47- 395   400 9(006) Número sequencial do registro no arquivo
	

		//#### TRAILLER ####
		De    Até    Picture    Conteúdo
		001   001    9(001) 	Código do registro = 9
		002   002    9(001) 	Código da remessa = 2
		003   004    9(002) 	Código do serviço = 01
		005   007    9(003) 	Código do Banco = 033
		008   017    X(010) 	Brancos
		018   025    9(008) 	Quantidade de registros em cobrança simples referente ao cedente
		026   039    9(014)v99  Valor dos títulos em cobrança simples referente ao cedente
		040   047    9(008) 	Número do aviso da cobrança simples
		048   097    X(050) 	Brancos
		098   105    9(008) 	Quantidade de registros em cobrança caucionada referente ao cedente
		106   119    9(012)v99  Valor dos títulos em cobrança caucionada referente ao cedente
		120   127    9(008) 	Número do aviso da cobrança caucionada
		128   137    X(010) 	Brancos
		138   145    9(008) 	Quantidade de registros em cobrança descontada referente ao cedente
		146   159    9(012)v99  Valor dos títulos em cobrança descontada referente ao cedente
		160   167    9(008) 	Número do aviso da cobrança descontada
		168   391    X(224) 	Brancos
		392   394    9(003) 	Número da versão
		395   400    9(006) 	Número seqüencial do registro no arquivo
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{4})(.{8})(.{8})(.{30})(.{3})(.{15})(.{6})(.{10})(.{7})(.{274})(.{3})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{10})(.{8})(.{14})(.{8})(.{50})(.{8})(.{12})(.{8})(.{10})(.{8})(.{12})(.{8})(.{224})(.{3})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{4})(.{8})(.{8})(.{25})(.{8})(.{37})(.{1})(.{2})(.{6})(.{10})(.{8})(.{2})(.{3})(.{3})(.{3})(.{1})(.{6})(.{13})(.{3})(.{5})(.{2})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{1})(.{1})(.{1})(.{6})(.{36})(.{1})(.{2})(.{8})(.{8})(.{13})(.{1})(.{11})(.{3})(.{6})/", $linha, $posicoes);
			}
			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno   = $this->converteData($linha[12]);
				$codigoBanco   = $linha[10];
				$linhaHeader   = $linha[0];

				$codigoEmpresa = $linha[14];
			}
			if($key != 0 && $key != (count($linhas)-1)){
				$conteudo .= $linha[0] . '\n';
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
		$conteudoArquivo = $linhaHeader . '\n' . $conteudo . $linhaTrailler;

		$codigoEmpresa = ltrim($codigoEmpresa, '0');
		$sqlD007 = mysql_query("SELECT D007_Id FROM D007 WHERE (D007_Numero_Convenio = '{$codigoEmpresa}' OR D007_Codigo_Cedente = '{$codigoEmpresa}') and D007_Flag_Ativo='S';");
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
				$total += $linha[31];
				$nossoNumero = $linha[8];
				$digitoNossoNumero = '';
				$carteira = $linha[10];
				$dataRecebimento = $this->converteData($linha[12]);
				$dataVencimento = $this->converteData($linha[20]);
				$dataCredito = $this->converteData($linha[37]);
				$valorRecebido = $this->converteNumero($linha[31]);
				$valorTitulo = $this->converteNumero($linha[21]);
				$valorAbatimento = $this->converteNumero($linha[29]);
				$valorDesconto = $this->converteNumero($linha[30]);
				$valorJurosMora = $this->converteNumero($linha[32]);
				$valorIOF = $this->converteNumero($linha[28]);
				$valorOutrosCreditos = $this->converteNumero($linha[33]);
				$Numero_Documento = $this->converteNumero($linha[13]);
				$Codigo_Natureza_Recebimento = ($linha[11]);
				$Prefixo_Agencia_Recebedora = ($linha[11]);
				$Carteira = ($linha[13]);
				$Codigo_Motivo = '';
				$Codigo_Banco_Recebedor = ($linha[22]);
				$conta = $linha[5];
				$descricaoOcorr = '';
				if($linha[14] == '06'){
					$descricaoOcorr = 'Liquidação normal';
				} else if($linha[14] == '15'){
					$descricaoOcorr = 'Liquidação em Cartorio';
				}

				$codigoNossoNumero = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = $nossoNumero . '-' . $digitoNossoNumero;
				$sql = mysql_query("SELECT * FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id LEFT JOIN D007 ON D007_Id=T117_D007_Id WHERE (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero like '%{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}') AND D007_Numero_Conta='{$conta}'");
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
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$Numero_Documento}', '{$nossoNumero}',
										'{$digitoNossoNumero}', '{$Codigo_Natureza_Recebimento}', '{$descricaoOcorr}', '{$Carteira}', '{$dataRecebimento}', '{$Codigo_Motivo}',
										'{$dataVencimento}', '{$valorTitulo}', '{$Codigo_Banco_Recebedor}', '{$Prefixo_Agencia_Recebedora}', '0',
										'0', '{$dataCredito}', '{$valorOutrosCreditos}', '{$valorIOF}', '{$valorAbatimento}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[47]}', '{$linha[0]}', '{$T038_Flag_Autorizado}'
									);";
				mysql_query($sql);
				$titulos++;
			}
		}
		mysql_query("UPDATE T037 SET T037_Numero_Titulos='{$titulos}' WHERE T037_Id='{$T037_Id}'");

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
