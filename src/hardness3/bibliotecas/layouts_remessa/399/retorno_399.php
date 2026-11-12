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
		1-Código do registro 				(001 001)  001
		2-Código de retorno 				(002 002)  001
		3-Literal Retorno 					(003 009)  007
		4-Código do serviço 				(010 011)  002
		5-Literal serviço 					(012 026)  015
		6-Zero								(027 027)  001
		7-Agência cedente 					(028 031)  004
		8-Sub-Conta							(032 033)  002
		9-Conta Corrente 					(034 044)  011
		10-uso do Banco 					(045 046)  002
		11-Nome do cliente 					(047 076)  030
		12-Codigo do banco 					(077 079)  003
		13-Nome do banco 					(080 094)  015
		14-Data da gravação 				(095 100)  006
		15-Densidade						(101 105)  005
		16-Literal densidade 				(106 108)  003
		17-Banco 							(109 119)  011
		18-Data do crédito 					(120 125)  006
		19-Banco							(126 388)  263
		20-Sequencial do arquivo 			(389 393)  005
		21-Banco 							(394 394)  001
		22-Numero sequencial 				(395 400)  006

		//#### BODY ####
		1-Código do registro				(001 001)  001
		2-Código de inscrição				(002 003)  002
		3-Código do cedente					(004 017)  014
		4-Zero								(018 018)  001
		5-Agencia Cedente 					(019 022)  004
		6-Sub conta 						(023 024)  002
		7-Conta corrente 					(025 035)  011
		8-Origem do pagamento	  			(036 036)  001
		9-uso Banco 						(037 037)  001
		10-Controle do participante 		(038 062)  025
		11-Nosso numero e digito 			(063 073)  011
		12-Desconto data 2 					(074 079)  006
		13-Valor do desconto 2 	 			(080 090)  011
		14-Desconto data 3 					(091 096)  006
		15-Valor do desconto 3 				(097 107)  011
		16-Carteira 						(108 108)  001
		17-Codigo da ocorrencia 			(109 110)  002
		18-Data da ocorrencia 				(111 116)  006
		19-Seu numero 						(117 126)  010
		20-Nosso numero 					(127 137)  011
		21-Uso do banco 	 				(138 146)  009
		22-Vencimento 						(147 152)  006
		23-Valor do titulo 					(153 165)  013
		24-Banco cobrador 					(166 168)  003
		25-Agencia cobradora 				(169 173)  005
		26-Especie 							(174 175)  002
		27-Tarifa ou custas 				(176 188)  013
		28-Uso do banco 					(189 227)  039
		29-Valor do abatimento 				(228 240)  013
		30-Valor do desconto 				(241 253)  013
		31-Valor pago 						(254 266)  013
		32-Juros de mora 					(267 279)  013
		33-uso do banco 					(280 301)  022
		34-Complemento da ocorrencia 		(302 303)  002
		35-Indicativo de credito 			(304 304)  001
		36-Uso do banco 					(305 388)  084
		37-Numero do aviso 					(389 393)  005
		38-Tipo de moeda 					(394 394)  001
		39-Numero sequencial 				(395 400)  005

		//#### TRAILLER ####
		1-Código do registro				(001 001)  001
		2-Código do arquivo 				(002 002)  001
		3-Código do serviço					(003 004)  002
		4-Código do banco					(005 007)  003
		5-uso do banco 						(008 017)  010
		6-Quantidade em ser 				(018 025)  008
		7-Valor em ser 						(026 039)  014
		8-Uso do banco 						(040 394)  355
		9-Numero sequencial 				(395 400)  006
		*/ 

		foreach ($arquivo as $nr => $linha) {			
			if($nr == 0) { //HEADER				
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{1})(.{4})(.{2})(.{11})(.{2})(.{30})(.{3})(.{15})(.{6})(.{5})(.{3})(.{11})(.{6})(.{263})(.{5})(.{1})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER				
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{10})(.{8})(.{14})(.{355})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{1})(.{4})(.{2})(.{11})(.{1})(.{1})(.{25})(.{11})(.{6})(.{11})(.{6})(.{11})(.{1})(.{2})(.{6})(.{10})(.{11})(.{9})(.{6})(.{13})(.{3})(.{5})(.{2})(.{13})(.{39})(.{13})(.{13})(.{13})(.{13})(.{22})(.{2})(.{1})(.{84})(.{5})(.{1})(.{6})/", $linha, $posicoes);
			}
			$linhas[] = $posicoes;		
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $this->converteData($linha[14]);
				$codigoBanco = $linha[12];
				$linhaHeader = $linha[0];
				$dataCredito = $linha[18];
				$dtC 		 = $this->converteData($linha[18]);
			}
			if($key != 0 && $key != (count($linhas)-1)){
				$conteudo .= $linha[0] . '\n';
			}
			if($key == (count($linhas)-1)){ //DADOS DO TRAILLER
				$linhaTrailler = $linha[0];
				$titulos = ($linha[9]-2);
			}				
		}

		//Verifica se o arquivo ja foi enviado
		$sql = mysql_query("SELECT * FROM T037 WHERE T037_Linha_Header = '{$linhaHeader}'");
		if(mysql_num_rows($sql) > 0){
			return 'Este arquivo ja foi enviado';
		}		
		$conteudoArquivo = $linhaHeader . '\n' . $conteudo . $linhaTrailler;
		$conteudoArquivo = mysql_real_escape_string($conteudoArquivo);
		//Registro na tabela de histórico de remessas e retornos com upload
		$T037 = "INSERT INTO T037 (
								T037_C004_Id, T037_Nome_Arquivo, T037_Data_Processamento, T037_Numero_Titulos, T037_Linha_Header, T037_Linha_Trailler, T037_Codigo_Banco, T037_Conteudo_Arquivo
							) VALUES (
								'{$g['empresaAtual']}', '{$fileName['name']}', '{$dataRetorno}', '{$titulos}', '{$linhaHeader}', '{$linhaTrailler}', '{$codigoBanco}', '{$conteudoArquivo}'
							)";
		mysql_query($T037);
		$T037_Id = $g['mysqlLastId'];

		foreach ($linhas as $key => $linha) {
			if($key != 0 && $key != (count($linhas)-1)){ //DADOS DO BODY
				/*
				/ ALTERAR O NUMERO DA LINHA DE ACORDO COM O LAYOUT DO BANCO
				*/
				$nossoNumero 		 = substr($linha[11], 0, -1);
				$digitoNossoNumero 	 = substr($linha[11], -1, 1);
				$dataRecebimento 	 = $this->converteData($linha[18]);
				$dataVencimento 	 = $this->converteData($linha[22]);


				$valorRecebido 		 = $this->converteNumero($linha[31]);
				$valorTitulo 		 = $this->converteNumero($linha[23]);
				$valorDesconto 		 = $this->converteNumero($linha[30]);
				$valorJurosMora 	 = $this->converteNumero($linha[32]);
				$valorIOF 			 = $this->converteNumero($linha[28]);
				$valorOutrosCreditos = $this->converteNumero($linha[35]);
				$valorAbatimento 	 = $this->converteNumero($linha[29]);

				$especieTitulo 		 = $linha[26];
				$codigoNaturezaRec 	 = $linha[17];
				$codigoBancoRecebedor= $linha[24];
				$agenciaRecebedora 	 = $linha[25];
				$dvAgenciaRecebedora = $linha[25];
				$numeroDocumento     = $linha[19];
				$codigoMotivo 		 = $linha[34];
				$carteira 			 = $linha[16];

				$ocorrenciasAceitas = array('06','07','15','16','31','32','33','36','38','39');
				if(in_array($codigoNaturezaRec, $ocorrenciasAceitas)){
					$dataCredito = $this->converteData($linha[18]);
				}

				$codigoNossoNumero = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = $nossoNumero . '-' . $digitoNossoNumero;
				$nossoNumeroComDigitoSemTraco = $nossoNumero . $digitoNossoNumero;
				$sql = mysql_query("SELECT * FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id WHERE T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Nosso_Numero = '{$nossoNumeroComDigitoSemTraco}'");
				$res = mysql_fetch_array($sql);
				$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;

				$T038_Flag_Autorizado = '';
				if(!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
					$T038_Flag_Autorizado = 'N';
				}

				$sql = "INSERT INTO T038 (
										T038_T037_Id, T038_T002_Id, T038_C004_Id, T038_Numero_Documento, T038_Nosso_Numero,
										T038_Digito_Verificador_Nosso_Numero, T038_Codigo_Natureza_Recebimento, T038_Carteira, T038_Data_Recebimento, T038_Codigo_Motivo,
										T038_Data_Vencimento, T038_Valor_Titulo, T038_Codigo_Banco_Recebedor, T038_Prefixo_Agencia_Recebedora, T038_Digito_Verificador_Agencia_Recebedora,
										T038_Especie_Titulo, T038_Data_Credito, T038_Valor_Outros_Recebimentos, T038_Valor_Taxa_IOF, T038_Valor_Abatimento,
										T038_Valor_Desconto, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String, T038_Flag_Autorizado
									) VALUES (
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$numeroDocumento}', '{$nossoNumero}',
										'{$digitoNossoNumero}', '{$codigoNaturezaRec}', '{$carteira}', '{$dataRecebimento}', '{$codigoMotivo}',
										'{$dataVencimento}', '{$valorTitulo}', '{$codigoBancoRecebedor}', '{$agenciaRecebedora}', '0',
										'{$especieTitulo}', '{$dtC}', '{$valorOutrosCreditos}', '{$valorIOF}', '{$valorAbatimento}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[39]}', '{$linha[0]}', '{$T038_Flag_Autorizado}'
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
		$ocorrenciasAceitas = array('06','07','15','16','31','32','33','36','38','39');
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
		$T038 = mysql_query("SELECT * FROM T038 LEFT JOIN T037 ON T037_Id = T038_T037_Id LEFT JOIN T002 ON T002_Id = T038_T002_Id LEFT JOIN D027 ON D027_Id=T002_D027_Id LEFT JOIN D007 ON D007_Id=D027_D007_Id WHERE T038_T037_Id = '{$T037_Id}'{$extraWhere} ORDER BY T038_Id ASC{$extraLimit}");
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
					if(in_array($res['T038_Codigo_Natureza_Recebimento'],$ocorrenciasAceitas)){ //Titulos a serem baixados
						if($res['T038_Valor_Juros_Mora'] > 0){ //Juros
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(), '{$res['T038_Valor_Juros_Mora']}', 'C'
														)");
						}

						if($res['T038_Valor_Taxa_IOF'] > 0){ //IOF
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - IOF', CURDATE(), '{$res['T038_Valor_Taxa_IOF']}', 'C'
														)");
						}

						if($res['T038_Valor_Outros_Recebimentos'] > 0){ //Outros Créditos
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Outros créditos', CURDATE(), '{$res['T038_Valor_Outros_Recebimentos']}', 'C'
														)");
						}

						if($outrosJuros === true && empty($res['T038_Valor_Juros_Mora']) && $res['D007_Baixar_Automatico_Valor_Excedido_Retorno'] == 'S'){ // Outros juros que vem diretamente no valor recebido
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - JUROS', CURDATE(), '{$valorDiferenca}', 'C'
														)");
						}

						mysql_query("UPDATE T038 SET T038_C007_Id_Processado = '{$g['usuarioAtual']}', T038_Data_Hora_Processado = CURRENT_TIMESTAMP() WHERE T038_Id = '{$res['T038_Id']}'");
						mysqli_query("CALL T002_Gravar_Totalizacao('{$res['T002_Id']}')");
						//Busca o codigo do banco
						$sql = mysql_query("SELECT D007_Id FROM D007 LEFT JOIN D062 ON D062_Id = D007_D062_Id WHERE D062_Numero_Banco = '{$codigoBanco}' AND D007_C004_Id = '{$g['empresaAtual']}'");
						$res = mysql_fetch_array($sql);
						//Baixa os Titulos
						$extra = base64_encode("WHERE (T002_Id = '{$T002_Id}')");
						$retorno = $FIN001->baixarRetorno($res['D007_Id'], $dataBaixa, $extra, $dataRecebimento);
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
