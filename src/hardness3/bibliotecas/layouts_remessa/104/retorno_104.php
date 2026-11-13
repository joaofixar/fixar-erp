<?php
namespace hardness;

require_once __DIR__ . '/beneficiario_104.php';

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
		1-TIPO DE REGISTRO 			(001 001)  01
		2-CÓDIGO DE RETORNO 		(002 002)  01
		3-LITERAL DE RETORNO 		(003 009)  07
		4-CÓDIGO DO SERVIÇO 		(010 011)  02
		5-LITERAL DE SERVIÇO 		(012 026)  15
		6-AGÊNCIA 					(027 030)  04
		7-ZEROS 					(031 032)  02
		8-CONTA 					(033 037)  05
		9-DAC 						(038 038)  01
		10-BRANCOS 					(039 046)  08
		11-NOME DA EMPRESA 			(047 076)  30
		12-CÓDIGO DO BANCO 			(077 079)  03
		13-NOME DO BANCO 			(080 094)  15
		14-DATA DE GERAÇÃO 			(095 100)  06
		15-DENSIDADE 				(101 105)  05
		16-UNIDADE DE DENSID. 		(106 108)  03
		17-Nº SEQ. ARQUIVO RET. 	(109 113)  05
		18-DATA DE CRÉDITO 			(114 119)  06
		19-BRANCOS 					(120 394)  275
		20-NÚMERO SEQÜENCIAL 		(395 400)  06

		//#### BODY ####
		1-TIPO DE REGISTRO 			(001 001)  01
		2-CÓDIGO DE INSCRIÇÃO 		(002 003)  02
		3-NÚMERO DE INSCRIÇÃO 		(004 017)  14
		4-AGÊNCIA 					(018 021)  04
		5-ZEROS 					(022 023)  02
		6-CONTA 					(024 028)  05
		7-DAC 						(029 029)  01
		8-BRANCOS 					(030 037)  08
		9-USO DA EMPRESA 			(038 062)  25
		10-NOSSO NÚMERO				(063 070)  08
		11-BRANCOS 					(071 082)  12
		12-CARTEIRA 				(083 085)  03
		13-NOSSO NÚMERO 			(086 093)  08
		14-DAC NOSSO NÚMERO 		(094 094)  01
		15-BRANCOS 					(095 107)  13
		16-CARTEIRA 				(108 108)  01
		17-CÓD. DE OCORRÊNCIA 		(109 110)  02
		18-DATA DE OCORRÊNCIA 		(111 116)  06
		19-Nº DO DOCUMENTO 			(117 126)  10
		20-NOSSO NÚMERO 			(127 134)  08
		21-BRANCOS 					(135 146)  12
		22-VENCIMENTO 				(147 152)  06
		23-VALOR DO TÍTULO 			(153 165)  11
		24-CÓDIGO DO BANCO 			(166 168)  03
		25-AGÊNCIA COBRADORA 		(169 172)  04
		26-DAC AG. COBRADORA 		(173 173)  01
		27-ESPÉCIE 					(174 175)  02
		28-TARIFA DE COBRANÇA 		(176 188)  11
		29-BRANCOS 					(189 214)  26
		30-VALOR DO IOF 			(215 227)  11
		31-VALOR ABATIMENTO 		(228 240)  11
		32-DESCONTOS 				(241 253)  11
		33-VALOR PRINCIPAL 			(254 266)  11
		34-JUROS DE MORA/MULTA 		(267 279)  11
		35-OUTROS CRÉDITOS 			(280 292)  11
		36-BOLETO DDA 				(293 293)  01
		37-BRANCOS 					(294 295)  02
		38-DATA CRÉDITO 			(296 301)  06
		39-INSTR.CANCELADA 			(302 305)  04
		40-BRANCOS 					(306 311)  06
		41-ZEROS 					(312 324)  13
		42-NOME DO SACADO 			(325 354)  30
		43-BRANCOS 					(355 377)  23
		44-ERROS / MSG INFORMATIVA  (378 385)  08
		45-BRANCOS 					(386 392)  07
		46-CÓD. DE LIQUIDAÇÃO 		(393 394)  02
		47-NÚMERO SEQÜENCIAL 		(395 400)  06

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
			$linha = rtrim($linha, "\r\n");
			if (strlen($linha) != 400) {
				return 'Arquivo de retorno da CAIXA inválido: cada registro deve possuir 400 posições.';
			}
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{4})(.{7})(.{9})(.{30})(.{3})(.{15})(.{6})(.{58})(.{3})(.{228})(.{5})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{387})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{3})(.{7})(.{1})(.{1})(.{2})(.{25})(.{2})(.{15})(.{6})(.{3})(.{24})(.{2})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{5})(.{2})(.{13})(.{3})(.{1})(.{2})(.{6})(.{14})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{1})(.{6})(.{1})(.{14})(.{80})(.{6})/", $linha, $posicoes);
			}
			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $this->converteData($linha[12]);
				$codigoBanco = $linha[10];
				$linhaHeader = $linha[0];

                $agencia = $linha[6];
                if ($codigoBanco !== '104' || !preg_match('/\A[0-9]{4}\z/', $agencia)) {
                    return 'Banco ou agencia invalidos no retorno CAIXA.';
                }
                try {
                    // Versao nas posicoes 159-161 do header de retorno (NE065).
                    $cedente = b104_beneficiario_retorno($linha[7], $linha[14]);
                } catch (\InvalidArgumentException $e) {
                    return $e->getMessage();
                }
			}
			if($key != 0 && $key != (count($linhas)-1)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
				$titulos = ($linha[42]-1);
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

        // Compara o codigo sem o DV de apresentacao, somente em contas da CAIXA.
        $sqlD007 = mysql_query("SELECT D007_Id FROM D007
            INNER JOIN D062 ON D062_Id = D007_D062_Id
            WHERE D062_Numero_Banco = '104'
              AND D007_Numero_Agencia = '{$agencia}'
              AND TRIM(D007_Codigo_Cedente) REGEXP '^[0-9]{1,7}(-[0-9])?$'
              AND LPAD(SUBSTRING_INDEX(TRIM(D007_Codigo_Cedente), '-', 1), 7, '0') = '{$cedente}'
            LIMIT 2");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com a agência {$agencia} e código cedente {$cedente}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com a agência {$agencia} e código cedente {$cedente}, por favor verifique.";
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

				$usoEmpresa 		  = $linha[9];
				$nossoNumeroSemDigito = $linha[10] . $linha[11];
				$dataRecebimento 	  = $this->converteData($linha[17]);
				$dataVencimento 	  = $this->converteData($linha[20]);
				$carteira 			  = $linha[15];
				$codigoNaturezaRec 	  = $linha[16];
				$numeroDocumento      = $linha[18];
				$codigoBancoRecebedor = $linha[22];
				$agenciaRecebedora 	  = $linha[23];
				$especieTitulo 		  = $linha[24];
				$dvAgenciaRecebedora  = '';

				if($codigoNaturezaRec != '21' && $codigoNaturezaRec != '22' && $codigoNaturezaRec != '23' && $codigoNaturezaRec != '25'){
					$dataRecebimento = '0000-00-00';
				}

				// Valores de recebimento
				$valorTitulo 		  = $this->converteNumero($linha[21]);
				$valorOutrasDespesas  = $this->converteNumero($linha[25]);
				$valorIOF 			  = $this->converteNumero($linha[31]);
				$valorAbatimento 	  = $this->converteNumero($linha[32]);
				$valorDesconto 		  = $this->converteNumero($linha[33]);
				$valorRecebido 		  = $this->converteNumero($linha[34]);
				$valorJurosMora 	  = $this->converteNumero($linha[35]);
				$dataCredito 		  = $this->converteData($linha[38]);
				$codigoMotivo 		  = $linha[13];
				$numeroLinha          = $linha[42];
				$canalLiquidacao      = $linha[26];
				$formaLiquidacao      = $linha[27];
				$floatLiquidacao      = $linha[28];

				//Verifica os boletos da empresa atual, caso não encontre verifica em todas as empresas
				$sql = mysql_query("SELECT * 
									  FROM T117 
								 LEFT JOIN T002 ON T002_T117_Id=T117_Id 
								 	 WHERE T117_C004_Id = '{$g['empresaAtual']}' 
									   AND (T117_Nosso_Numero = '{$nossoNumeroSemDigito}' OR T117_Nosso_Numero LIKE '{$nossoNumeroSemDigito}%' OR T117_Id = '{$usoEmpresa}')");
				if(mysql_num_rows($sql) > 0){
					$res = mysql_fetch_array($sql);
					$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
				} else {
					$sql = mysql_query("SELECT * 
										  FROM T117 
									 LEFT JOIN T002 ON T002_T117_Id=T117_Id 
									     WHERE T117_Nosso_Numero = '{$nossoNumeroSemDigito}' OR T117_Nosso_Numero LIKE '{$nossoNumeroSemDigito}%' OR T117_Id = '{$usoEmpresa}'");
					if(mysql_num_rows($sql) > 0){
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					} else {
						$sql = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$usoEmpresa}'");
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					}
				}

				$T038_Flag_Autorizado = '';
				if(!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
					$T038_Flag_Autorizado = 'N';
				}

				$sql = "INSERT INTO T038 (
										T038_T037_Id, T038_T002_Id, T038_C004_Id, T038_Numero_Documento, T038_Nosso_Numero,
										T038_Codigo_Natureza_Recebimento, T038_Carteira, T038_Data_Recebimento, T038_Codigo_Motivo,
										T038_Data_Vencimento, T038_Valor_Titulo, T038_Codigo_Banco_Recebedor, T038_Prefixo_Agencia_Recebedora, T038_Digito_Verificador_Agencia_Recebedora,
										T038_Especie_Titulo, T038_Data_Credito, T038_Valor_Taxa_IOF, T038_Valor_Abatimento, T038_Valor_Outras_Despesas,
										T038_Valor_Desconto, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String,
										T038_Info_Complementar_1, T038_Info_Complementar_2, T038_Info_Complementar_3, T038_Flag_Autorizado
									) VALUES (
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$numeroDocumento}', '{$nossoNumeroSemDigito}',
										'{$codigoNaturezaRec}', '{$carteira}', '{$dataRecebimento}', '{$codigoMotivo}',
										'{$dataVencimento}', '{$valorTitulo}', '{$codigoBancoRecebedor}', '{$agenciaRecebedora}', '{$dvAgenciaRecebedora}',
										'{$especieTitulo}', '{$dataCredito}', '{$valorIOF}', '{$valorAbatimento}', '{$valorOutrasDespesas}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$numeroLinha}', '{$linha[0]}',
										'{$canalLiquidacao}', '{$formaLiquidacao}', '{$floatLiquidacao}', '{$T038_Flag_Autorizado}'
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
			
			if($res['T038_Valor_Juros_Mora']<=0){
				$res['T038_Valor_Juros_Mora']='';
			}

			if(!empty($res['T002_Id'])){
				
				$T002_Id 		= $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'];

				$outrosJuros = false;
				// Outros juros que vem direamente no valor recebido
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ 
					$outrosJuros = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}

				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					if($res['T038_Codigo_Natureza_Recebimento'] == '21' || $res['T038_Codigo_Natureza_Recebimento'] == '22'|| $res['T038_Codigo_Natureza_Recebimento'] == '23' || $res['T038_Codigo_Natureza_Recebimento'] == '25'){ //Titulos a serem baixados
						if($res['T038_Valor_Juros_Mora'] > 0){ //Juros
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES ('{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(), '{$res['T038_Valor_Juros_Mora']}', 'C')");

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
