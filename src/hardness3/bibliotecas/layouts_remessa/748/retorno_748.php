<?php
namespace hardness;
//retorno banco Sicredi

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
		01-IDENTIFICAÇÃO DO REGISTRO HEADER		(001 001)  001
		02-IDENTIFICAÇÃO DO ARQUIVO RETORNO		(002 002)  001
		03-LITERAL DE RETORNO 					(003 009)  007
		04-CÓDIGO DO SERVIÇO DE COBRANÇA	 	(010 011)  002
		05-LITERAL COBRANÇA 					(012 026)  015
		06-CÓDIGO DO BENEFICIÁRIO				(027 031)  005
		07-CIC/CGC DO BENEFICIÁRIO				(032 045)  014
		08-FILLER								(046 076)  031
		09-NÚMERO DO SICREDI					(077 079)  003
		10-LITERAL BANSICREDI					(080 094)  015
		11-DATA DE GRAVAÇÃO DO ARQUIVO AAAAMMDD (095 102)  008
		12-FILLER								(103 110)  008
		13-NÚMERO DO RETORNO					(111 117)  007
		14-FILLER								(118 389)  272
		15-VERSÃO DO SISTEMA					(390 394)  005
		16-Nº SEQ. DO REGISTRO 	 				(395 400)  006
		
---
		//#### BODY ####
		1-IDENTIFICAÇÃO DO REGISTRO DETALHE		(001 001)  01
		2-FILLER 								(002 013)  12
		3-TIPO DE COBRANÇA 						(014 014)  01
		4-CÓD DO PAGADOR NA COOP DO BENEFICIÁRIO(015 019)  05
		5-CÓD DO PAGADOR JUNTO AO ASSOCIADO		(020 024)  05
		6-BOLETO DDA 							(025 025)  01
		7-FILLER 								(026 047)  22
		8-NOSSO NUMERO SICREDI SEM EDIÇÃO		(048 062)  15
		9-FILLER 								(063 108)  46
		10-OCORRÊNCIA							(109 110)  02
		11-DATA DA OCORRÊNCIA 					(111 116)  06
		12-SEU NÚMERO 							(117 126)  10
		13-FILLER 								(127 146)  20
		14-DATA DE VENCIMENTO 					(147 152)  06
		15-VALOR DO TÍTULO 						(153 165)  13
		16-FILLER 								(166 174)  09
		17-ESPÉCIE DE DOCUMENTO 				(175 175)  01
		18-DESPESAS DE COBRANÇA 				(176 188)  13
		19-DESPESAS DE CUSTAS DE PROTESTO		(189 201)  13
		20-FILLER		 						(202 227)  26
		21-ABATIMENTO CONCEDIDO 				(228 240)  13
		22-DESCONTO CONCEDIDO	 				(241 253)  13
		23-VALOR EFETIVAMENTE PAGO 				(254 266)  13
		24-JUROS DE MORA 						(267 279)  13
		25-MULTA 								(280 292)  13
		26-FILLER 								(293 294)  02
		27-SOMENTE PARA OCORRÊNCIA "19"			(295 295)  01
		28-FILLER 								(296 318)  23
		29-MOTIVOS DA OCORRÊNCIA				(319 328)  10	
		30-DATA PREVISTA P/ LANÇAMENTO NA CC	(329 336)  08
		31-FILLER 								(337 394)  58
		32-SEQUENCIAL DO REGISTRO 				(395 400)  06
 
		//#### TRAILLER ####
		1-IDENT. REGISTRO TRAILER	(001 001)  001
		2-IDENT. ARQUIV RETORNO		(002 002)  001
		3-NÚMERO DO SICREDI 		(003 005)  003
		4-CÓDIGO DO BENEFICIÁRIO	(006 010)  005
		5-FILLER 					(011 394)  384
		6-NÚMERO SEQÜENCIAL REG		(395 400)  006
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{5})(.{14})(.{31})(.{3})(.{15})(.{8})(.{8})(.{7})(.{272})(.{5})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{3})(.{5})(.{384})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{12})(.{1})(.{5})(.{5})(.{1})(.{22})(.{15})(.{46})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{9})(.{1})(.{13})(.{13})(.{26})(.{13})(.{13})(.{13})(.{13})(.{13})(.{2})(.{1})(.{23})(.{10})(.{8})(.{58})(.{6})/", $linha, $posicoes);
			}//                 1     2      3     4     5     6     7      8      9      10    11    12     13     14    15     16    17    18     19     20     21     22     23      24    25     26    27    28     29     30    31    32                                                                      
   			$linhas[] = $posicoes;
		}

		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $linha[11];
				$codigoBanco = $linha[9];
				$linhaHeader = $linha[0];
				$codigoEmpresa = $linha[6];

			}
			if($key != 0 && $key != (count($linhas)-1)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
				$titulos = ($linha[32]-1);
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
		//$codigoEmpresa = ltrim($codigoEmpresa, '0');

		$sqlD007 = mysql_query("SELECT D007_Id 
								  FROM D007 
								 WHERE D007_Numero_Convenio = '{$codigoEmpresa}' 
								    OR D007_Codigo_Cedente = '{$codigoEmpresa}' 
									OR D007_Numero_Conta LIKE '%{$codigoEmpresa}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com o código de cedente ou número do convenio igual a {$codigoEmpresa}, por favor verifique.";
		} else if($rows > 1){
			//Sicredi pode ter mais de uma conta o mesmo número, oque muda é o código do cedente
			//Sendo realiza mais uma verificação para não dar problema em quem já usa o Sicredi caso não tenha preenchido o D007_Codigo_Cedente
			$sqlD007 = mysql_query("SELECT D007_Id 
									  FROM D007 
		   							 WHERE D007_Codigo_Cedente = '{$codigoEmpresa}'");
			$rows = mysql_num_rows($sqlD007);
			if($rows > 1){
				return "Foi identificado mais de um banco com o código de cedente igual a {$codigoEmpresa}, por favor verifique.";
			}
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
				$nossoNumeroFormatado	  = $this->formataNossoNumero($linha[8]);
				$DigitoNossoNumero   	  = $this->formataNossoNumero($linha[8], 3);
				$nossoNumero   		 	  = $this->formataNossoNumero($linha[8], 2);
				$nossoNumeroPrimeiraParte = $this->formataNossoNumero($linha[8], 1);
				$codigoNaturezaRec 	 = $linha[10];
				$dataOcorrencia 	 = $this->converteData($linha[11]);
				$numeroDocumento     = $linha[12];
				$dataVencimento 	 = $this->converteData($linha[14]);
				$valorTitulo 		 = $this->converteNumero($linha[15]);
				$especieTitulo 		 = $linha[17];
				$valorAbatimento 	 = $this->converteNumero($linha[21]);
				$valorDesconto 		 = $this->converteNumero($linha[22]);
				$valorRecebido 		 = $this->converteNumero($linha[23]);
				$valorJurosMora 	 = $this->converteNumero($linha[24]);
				$valorMulta 		 = $this->converteNumero($linha[25]);	
				$codigoMotivo		 = $linha[29];
				$dataCredito 		 = $this->converteDataModelo2($linha[30]);
				$valorOutrasDespesas = 0;
				if($codigoNaturezaRec == '28'){
					// No retorno Sicredi, a tarifa também é repetida no campo valor
					// efetivamente pago. O valor da despesa de cobrança é o da tarifa.
					$valorOutrasDespesas = $this->converteNumero($linha[18]);
					$valorRecebido = 0;
				}


				//Verifica os boletos da empresa atual, para o banco corrente
				$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_C004_Id = '{$g['empresaAtual']}' AND T117_D007_Id='{$rowD007['D007_Id']}' AND T117_Nosso_Numero = '{$nossoNumeroFormatado}'");
				if(mysql_num_rows($sql) > 0){
					//guarda os dados em índices associativos, usando o nome do campo como chave.
					$res = mysql_fetch_array($sql);
					$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
					log("1-T002_Id".$T002_Id);
				}

				if ($T002_Id<=0){
					//Verifica os boletos da empresa atual, em qualquer banco caso não encontre para o banco corrente.
					$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE T117_C004_Id = '{$g['empresaAtual']}' AND T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroFormatado}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
					if(mysql_num_rows($sql) > 0){
						$res = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
						log("2-T002_Id".$T002_Id);
					} else {
						$sql = mysql_query("SELECT * FROM T117 LEFT JOIN T002 ON T002_T117_Id=T117_Id WHERE (T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroFormatado}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}' OR T117_Id = '{$usoEmpresa}')");
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
										T038_T037_Id, T038_T002_Id, T038_C004_Id,           T038_Numero_Documento, T038_Nosso_Numero,                              T038_Codigo_Natureza_Recebimento,T038_Codigo_Motivo, T038_Data_Recebimento, T038_Data_Vencimento, T038_Valor_Titulo, T038_Valor_Multa, T038_Especie_Titulo,T038_Data_Credito, T038_Valor_Abatimento,T038_Valor_Desconto, T038_Valor_Outras_Despesas, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String,T038_Flag_Autorizado,T038_Digito_Verificador_Nosso_Numero
									) VALUES (
										'{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$numeroDocumento}',  '{$nossoNumeroPrimeiraParte}/{$nossoNumero}',  '{$codigoNaturezaRec}',          '{$codigoMotivo}',  '{$dataOcorrencia}',   '{$dataVencimento}',  '{$valorTitulo}', '{$valorMulta}', '{$especieTitulo}','{$dataCredito}','{$valorAbatimento}','{$valorDesconto}', '{$valorOutrasDespesas}', '{$valorRecebido}','{$valorJurosMora}', '{$linha[32]}', '{$linha[0]}','{$T038_Flag_Autorizado}',{$DigitoNossoNumero}
									);";
				log($sql);
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
							   LEFT JOIN D027 ON D027_Id = T002_D027_Id 
							   LEFT JOIN D007 ON D007_Id = D027_D007_Id 
							       WHERE T038_T037_Id = '{$T037_Id}'{$extraWhere} ORDER BY T038_Id ASC{$extraLimit}");
		while($res = mysql_fetch_array($T038)){
			$lidos++;
			$ultimoIdLido = $res['T038_Id'];
			$codigoBanco = $res['T037_Codigo_Banco'];
			$dataBaixa = $res['T038_Data_Credito'];
			$dataOcorrencia = $res['T038_Data_Recebimento'];
			if($res['T038_Codigo_Natureza_Recebimento'] == '28'){
				// Tarifa não representa liquidação e não deve gerar baixa ou lançamento.
				continue;
			}
			if($res['T038_Valor_Juros_Mora']<=0){
				$res['T038_Valor_Juros_Mora']='';
			}
			if(!empty($res['T002_Id'])){
				$T002_Id = $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Multa'];
				
				$outrosJuros = false;
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ // Outros juros que vem diretamente no valor recebido
					$outrosJuros = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}
				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					log("TESTE");
					if($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15' || $res['T038_Codigo_Natureza_Recebimento'] == '16' || $res['T038_Data_Credito'] != '0000-00-00'){ //Titulos a serem baixados
						if($res['T038_Valor_Juros_Mora'] > 0){ //Juros
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(), '{$res['T038_Valor_Juros_Mora']}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'JM');
						}

						if($res['T038_Valor_Multa'] > 0){ //Multa
							mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
														) VALUES (
															'{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Multa', CURDATE(), '{$res['T038_Valor_Multa']}', 'C'
														)");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'M');

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
						log("Baixar retorno = 1");
						$retorno = $FIN001->baixarRetorno($res['T037_D007_Id'], $dataBaixa, $extra, $dataOcorrencia);
						log("Baixar retorno = 2");
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

	public function converteDataModelo2($data){ //Recebe uma data AAAAMMDD e converte para data formato banco
		if($data > 0){
			$ano = substr($data, 0, 4);
			$mes = substr($data, 4, 2);
			$dia = substr($data, 6, 2);
			
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

	public function formataNossoNumero($numero, $parte=null){ //Formata nosso numero com carteira e digito verificador
		if(isset($numero)){
			$primeiraParte = substr($numero, 0,2);
			$segundaParte = substr($numero, 2,6);
			$digitoVerificador = substr($numero, 8,1);
			
			if($parte==1){
				return $primeiraParte;	
			} else if($parte==2) {
				return $segundaParte;
			} else if($parte == 3){
				return $digitoVerificador;
			} else {
				return $primeiraParte."/".$segundaParte."-".$digitoVerificador;
			}
		} else {
			return 0;
		}
	}
	
}
