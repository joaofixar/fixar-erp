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
		1-CÓDIGO DO REGISTRO		(001 001)  01
		2-CÓDIGO DO RETORNO 		(002 002)  01
		3-LITERAL DO RETORNO	    (003 009)  07
		4-CÓDIGO DO SERVIÇO 		(010 011)  02
		5-LITERAL DO SERVIÇO	    (012 026)  15
		6-CODIGO DA EMPRESA			(027 046)  20
		7-NOME EMPRESA				(047 076)  30
		8-CÓDIGO DO BANCO           (077 079)  03        
		9-NOME DO BANCO				(080 094)  15
        10-DATA GRAVACAO			(095 100)  06
        11-DENSIDADE                (101 105)  05
        11-UNIDADE DE DENSIDADE     (106 108)  03
        10-Nº SEQ.FILA RETORNO      (109 113)  05
		11-BRANCO      	            (114 119)  06
		12-BRANCO				    (120 394)  275
		19-Nº SEQUENCIAL       		(395 400)  06	

		//#### BODY ####
		1-CÓDIGO DE REGISTRO 		(001 001)  01
		2-CÓDIGO DE INSCRIÇÃO 		(002 003)  02
		3-NÚMERO DE INSCRIÇÃO 		(004 017)  14
		4-BRANCOS 					(018 037)  20
		5-USO EMPRESA 				(038 062)  25        
		6-NOSSO NÚMERO E DIGITO		(063 073)  11
		7-BRANCOS                   (074 082)  09
        8-NOSSA CARTEIRA    		(083 085)  03
		9-BRANCOS                   (086 094)  09
        10-BRANCOS                  (095 107)  13
        11-COD CARTEIRA    	    	(108 108)  01
        12-COD OCORRENCIA 			(109 110)  02
		13-DATA OCORRENCIA 			(111 116)  06
		14-SEU NUMERO 		        (117 126)  10 
        15-BRANCOS                  (127 146)  20
        16-VENCIMENTO 				(147 152)  06
		17-VALOR DO TÍTULO 		    (153 165)  13
		18-COD BANCO    		    (166 168)  03
		19-AGÊNCIA COBRADORA 		(169 172)  04
		20-DV AGÊNCIA COBRADORA 	(173 173)  01
		21-ESPECIE 	                (174 175)  02 
		22-TARIFA COBRANÇA 			(176 188)  13
		23-BRANCO 			        (189 214)  26
		24-IOF 			            (215 227)  13
		25-ZEROS 			        (228 240)  13
		26-VALOR DO DESCONTO 		(241 253)  13
        27-VALOR PAGO 		        (254 266)  13
		28-JUROS DE MORA 		    (267 279)  13
        29-COMPLEMENTAÇÃO 		    (280 376)  97
		30-CÓDIGO MOEDA 			(377 377)  01
		31-RETORNO ERRO 			(378 385)  08
		32-DATA GRAVAÇÃO			(386 391)  06
		33-BRACO        			(392 394)  03
		34-Nº SEQUENCIAL 			(395 400)  06
		
		//#### TRAILLER ####
		1-TIPO DE REGISTRO 			(001 001)  01
		2-IDENTIFICA FITA RETORNO   (002 002)  01
		3-CÓDIGO SERVIÇO  	        (003 004)  02
		4-CÓDIGO BANCO   			(005 007)  03
        5-BRANCOS				    (008 105)  98
		6-BRANCOS				    (106 394)  289
		7-Nº SEQUENCIAL 			(395 400)  06

		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{20})(.{30})(.{3})(.{15})(.{6})(.{5})(.{3})(.{5})(.{6})(.{275})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{98})(.{289})(.{06})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{20})(.{25})(.{11})(.{9})(.{3})(.{9})(.{13})(.{1})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{4})(.{1})(.{2})(.{13})(.{26})(.{13})(.{13})(.{13})(.{13})(.{13})(.{97})(.{1})(.{8})(.{6})(.{3})(.{6})/", $linha, $posicoes);
			}   //              1     2      3      4      5      6     7     8     9     10     11    12    13    14     15     16    17     18    19    20    21    22     23     24     25     26     27     28     29     30    31    32    33    34          
			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $this->converteData($linha[10]);
				$codigoBanco = $linha[8];
				$linhaHeader = $linha[0];

                $codigoEmpresa = intval(substr($linha[6],0,9));
			}
			if($key != 0 && $key != (count($linhas)-1)){
			//	$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
				$titulos = ($linha[34]-1);
			}
			if($key == (count($linhas)-1)){ //DADOS DO TRAILLER
				$linhaTrailler = $linha[0];
			}
		}

		//Verifica se o arquivo ja foi enviado
		$sql = mysql_query("SELECT * FROM T037 WHERE T037_Linha_Header = '{$linhaHeader}'");
		if(mysql_num_rows($sql) > 0){
			//return 'Este arquivo ja foi enviado';
		}
		//$conteudoArquivo = $linhaHeader . '\n' . $conteudo . $linhaTrailler;
		$conteudoArquivo = $linhaHeader;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $conteudo . $linhaTrailler;

		log("codigoEmpresa: ".$codigoEmpresa);

        $sqlD007 = mysql_query("SELECT D007_Id FROM D007 WHERE D007_Codigo_Cedente = '{$codigoEmpresa}' OR D007_Numero_Convenio = '{$codigoEmpresa}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com o código de cedente ou número do convenio igual a {$codigoEmpresa}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com o código de cedente ou número do convenio igual a {$codigoEmpresa}, por favor verifique.";
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
				$usoEmpresa 		 = $linha[5];

				$nossoNumero         = substr($linha[6], 0, 10); // USEI DE EXEMPLO O 399
				$digitoNossoNumero   = substr($linha[6], -1, 1); // USEI DE EXEMPLO O 399

				$dataRecebimento 	 = $this->converteData($linha[13]); 
				$dataVencimento 	 = $this->converteData($linha[16]);
				$dataCredito 		 = $this->converteData($linha[13]); // NÃO TEM DATA CREDITO
				$valorTitulo 		 = $this->converteNumero($linha[17]);
				$valorRecebido 		 = $this->converteNumero($linha[27]);
				//$valorAbatimento 	 = $this->converteNumero($linha[28]); // NÃO TEM  ABATIMENTOS
				$valorDesconto 		 = $this->converteNumero($linha[26]);
				$valorJurosMora 	 = $this->converteNumero($linha[28]);
				$valorIOF 			 = $this->converteNumero($linha[24]);
				//$valorOutrosCreditos = $this->converteNumero($linha[32]); // NAO TEM OUTROS CREDITOS
				$valorOutrasDespesas = $this->converteNumero($linha[22]); 
                $valorRecebido 		 = $this->converteNumero($linha[27]);

				$especieTitulo 		  = $linha[21];
				$codigoNaturezaRec 	  = $linha[12];
				$codigoBancoRecebedor = $linha[18];
				$agenciaRecebedora 	  = $linha[19];
				$dvAgenciaRecebedora  = $linha[20];
				$numeroDocumento      = $linha[14];
				$codigoMotivo 		  = $linha[31];
				$carteira 			  = $linha[11];
				$codigoNossoNumero    = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = $nossoNumero . $digitoNossoNumero;


				//Verifica os boletos da empresa atual, para o banco corrente
				$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_C004_Id = '{$g['empresaAtual']}' AND T117_D007_Id='{$rowD007['D007_Id']}' AND (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
				if(mysql_num_rows($sql) > 0){
					$res = mysql_fetch_array($sql);
					$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
				} else {
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
					} else {
						$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
						if(mysql_num_rows($sql) > 0){
							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
						} else {
							$sql = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$usoEmpresa}'");
							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
						}
					}
				}

				$T038_Flag_Autorizado = '';
	
				if ($codigoNaturezaRec=='06' || $codigoNaturezaRec=='08') {
					if (!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
						$T038_Flag_Autorizado = 'N';
					}
				}

				log("entrou");

				$sql = "INSERT INTO T038 (
										T038_T037_Id, 
                                        T038_T002_Id, 
                                        T038_C004_Id, 
                                        T038_Numero_Documento, 
                                        T038_Nosso_Numero,
										T038_Digito_Verificador_Nosso_Numero,
                                        T038_Codigo_Natureza_Recebimento,
                                        T038_Carteira,
                                        T038_Data_Recebimento,
                                        T038_Codigo_Motivo,
										T038_Data_Vencimento,
                                        T038_Valor_Titulo,
                                        T038_Codigo_Banco_Recebedor,
                                        T038_Prefixo_Agencia_Recebedora,
                                        T038_Digito_Verificador_Agencia_Recebedora,
										T038_Especie_Titulo,
                                        T038_Data_Credito,
                                        T038_Valor_Taxa_IOF,
                                        T038_Valor_Outras_Despesas,
										T038_Valor_Desconto,
                                        T038_Valor_Recebido,
                                        T038_Valor_Juros_Mora,
                                        T038_Numero_Linha,
                                        T038_String,
                                        T038_Flag_Autorizado
									) VALUES (
										'{$T037_Id}',
                                        '{$T002_Id}',
                                        '{$g['empresaAtual']}',
                                        '{$numeroDocumento}',
                                        '{$nossoNumero}',
										'{$digitoNossoNumero}',
                                        '{$codigoNaturezaRec}',
                                        '{$carteira}',
                                        '{$dataRecebimento}',
                                        '{$codigoMotivo}',
										'{$dataVencimento}',
                                        '{$valorTitulo}',
                                        '{$codigoBancoRecebedor}',
                                        '{$agenciaRecebedora}',
                                        '{$dvAgenciaRecebedora}',
										'{$especieTitulo}',
                                        '{$dataCredito}',
                                        '{$valorIOF}',
                                        '{$valorOutrasDespesas}',
										'{$valorDesconto}',
                                        '{$valorRecebido}',
                                        '{$valorJurosMora}',
                                        '{$linha[34]}',
                                        '{$linha[0]}',
                                        '{$T038_Flag_Autorizado}'
									);";
				mysql_query($sql);
			}
		}
		
		log("saiu");
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
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ // Outros juros que vem diretamente no valor recebido
					$outrosJuros = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}
				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					if($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '08'){ //Titulos a serem baixados

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
						if($retorno!==true){
							return $retorno;
						}
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
