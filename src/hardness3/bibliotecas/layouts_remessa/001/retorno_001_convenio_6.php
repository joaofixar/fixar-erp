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
		01-Identificação do Registro Header: “0”											(001 001) 001
		02-Tipo de Operação: “2”															(002 002) 001
		03-Identificação Tipo de Operação “RETORNO”											(003 009) 007
		04-Identificação do Tipo de Serviço: “01” 											(010 011) 002
		05-Identificação por Extenso do Tipo de Serviço: “COBRANCA”							(012 019) 008
		06-Complemento do Registro: “Brancos”												(020 026) 007
		07-Prefixo da agência																(027 030) 004
		08-Dígito Verificador - D.V. - do Prefixo da Agência. 								(031 031) 001
		09-Número da Conta Corrente 														(032 039) 008
		10-Dígito Verificador - D.V. - da Conta Corrente do Cedente 						(040 040) 001
		11-Número do convênio líder 														(041 046) 006
		12-Nome do Cedente 																	(047 076) 030
		13-001BANCODOBRASIL 																(077 094) 018
		14-Data da Gravação: Informe no formado “DDMMAA”									(095 100) 006
		15-Seqüencial do Retorno 01 														(101 107) 007
		16-Complemento do Registro: “Brancos”												(108 394) 287
		17-Seqüencial do Registro:”000001”													(395 400) 006
		
		//#### BODY ####		
		01-Identificação do Registro Detalhe: 1 (um)										(001 001) 001
		02-Zeros 																			(002 003) 002
		03-Zeros 																			(004 017) 014
		04-Prefixo da Agência 																(018 021) 004
		05-Dígito Verificador - D.V. - do Prefixo da Agência 								(022 022) 001
		06-Número da Conta Corrente do Cedente 												(023 030) 008
		07-Dígito Verificador - D.V. - do Número da Conta Corrente do Cedente 				(031 031) 001
		08-Número do Convênio de Cobrança do Cedente 										(032 037) 006
		09-Número de Controle do Participante												(038 062) 025
		10-Nosso-Número 																	(063 073) 011
		11-DV do Nosso-Número 																(074 074) 001
		12-Tipo de cobrança 02 																(075 075) 001
		13-Tipo de cobrança específico para comando 72 										(076 076) 001
		14-Dias para cálculo 04																(077 080) 004
		15-Natureza do recebimento 05														(081 082) 002
		16-Uso do Banco 																	(083 085) 003
		17-Variação da Carteira 															(086 088) 003
		18-Conta Caução 06 																	(089 089) 001
		19-Uso do Banco 																	(090 094) 005
		20-Uso do Banco 																	(095 095) 001
		21-v99 Taxa de desconto 															(096 100) 005
		22-Taxa de IOF 																		(101 105) 005
		23-Branco 																			(106 106) 001
		24-Carteira 																		(107 108) 002
		25-Comando 07 																		(109 110) 002
		26-Data da Entrada/Liquidação (DDMMAA) 												(111 116) 006
		27-Número título dado pelo cedente 06 												(117 126) 010
		28-Confirmação das posições 63 a 82 												(127 146) 020
		29-Data de vencimento (DDMMAA) 														(147 152) 006
		30-v99 Valor do título 																(153 165) 011
		31-Código do banco recebedor 08 													(166 168) 003
		32-Prefixo da agência recebedora 08 												(169 172) 004
		33-DV prefixo recebedora 															(173 173) 001
		34-Espécie do título 09 															(174 175) 002
		35-Data do crédito (DDMMAA) 10 														(176 181) 006
		36-v99 Valor da tarifa 06 															(182 188) 005
		37-v99 Outras despesas 																(189 201) 011
		38-v99 Juros do desconto 															(202 214) 011
		39-v99 IOF do desconto 																(215 227) 011
		40-v99 Valor do abatimento 															(228 240) 011
		41-v99 Desconto concedido (diferença entre valor do título e valor recebido)		(241 253) 011
		42-v99 Valor recebido (valor recebido parcial) 										(254 266) 011
		43-v99 Juros de mora 																(267 279) 011
		44-v99 Outros recebimentos 															(280 292) 011
		45-v99 Abatimento não aproveitado pelo sacado 										(293 305) 011
		46-v99 Valor do lançamento 															(306 318) 011
		47-Indicativo de débito/crédito 11 													(319 319) 001
		48-Indicador de valor 12 															(320 320) 001
		49-v99 Valor do ajuste 13 															(321 332) 010
		50-Brancos (vide observação para cobrança compartilhada) 14 						(333 333) 001
		51-Brancos (vide observação para cobrança compartilhada) 14 						(334 342) 009
		52-Zeros 14 																		(343 348) 006
		53-Zeros 14 																		(349 357) 009
		54-Zeros 14 																		(358 363) 006
		55-Zeros 14 																		(364 372) 009
		56-Zeros 14 																		(373 378) 006
		57-Zeros 14 																		(379 387) 009
		58-Brancos 																			(388 392) 005
		59-Canal de pagamento do título utilizado pelo sacado 15 							(393 394) 002
		60-Seqüencial do registro															(395 400) 006

		//#### TRAILLER ####
		01-Identificação do Registro Trailer: “9”											(001 001) 001
		02-“2”																				(002 002) 001
		03-“01”																				(003 004) 002
		04-“001”																			(005 007) 003
		05-Brancos																			(008 017) 010
		06-Cobrança Simples - quantidade de títulos											(018 025) 008
		07-v99 Cobrança Simples - valor total												(026 039) 013
		08-Cobrança Simples - Número do aviso												(040 047) 008
		09-Cobrança Simples - Brancos														(048 057) 010
		10-Cobrança Vinculada - quantidade de títulos										(058 065) 008
		11-v99 Cobrança Vinculada - valor total												(066 079) 013
		12-Cobrança Vinculada - Número do aviso												(080 087) 008
		13-Cobrança Vinculada - Brancos														(088 097) 010
		14-Cobrança Caucionada - quantidade de títulos										(098 105) 008
		15-v99 Cobrança Caucionada - valor total											(106 119) 013
		16-Cobrança Caucionada - Número do aviso											(120 127) 008
		17-Cobrança Caucionada - Brancos													(128 137) 010
		18-Cobrança Descontada - quantidade de títulos										(138 145) 008
		19-v99 Cobrança Descontada - valor total											(146 159) 013
		20-Cobrança Descontada - Número do aviso											(160 167) 008
		21-Cobrança Descontada - Brancos													(168 217) 050
		22-Cobrança Vendor - quantidade de títulos											(218 225) 008
		23-v99 Cobrança Vendor - valor total												(226 239) 013
		24-Cobrança Vendor - Número do aviso												(240 247) 008
		25-Cobrança Vendor – Brancos														(248 394) 147
		26-Seqüencial do registro															(395 400) 006
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER				
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{8})(.{7})(.{4})(.{1})(.{8})(.{1})(.{6})(.{30})(.{18})(.{6})(.{7})(.{287})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER				
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{50})(.{8})(.{14})(.{8})(.{147})(.{6})/", $linha, $posicoes);
			} else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{4})(.{1})(.{8})(.{1})(.{6})(.{25})(.{11})(.{1})(.{1})(.{1})(.{4})(.{2})(.{3})(.{3})(.{1})(.{5})(.{1})(.{5})(.{5})(.{1})(.{2})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{4})(.{1})(.{2})(.{6})(.{7})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{1})(.{1})(.{12})(.{1})(.{9})(.{6})(.{9})(.{6})(.{9})(.{6})(.{9})(.{5})(.{2})(.{6})/", $linha, $posicoes);
			}
			$linhas[] = $posicoes;		
		}
		$total = 0;
		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			if($key == 0){ //DADOS DO HEADER
				$dataRetorno = $this->converteData($linha[14]);
				$codigoBanco = substr($linha[13],0,3);
				$linhaHeader = $linha[0];
			}
			//CONTEUDO DO BODY
			if($key != 0 && $key != (count($linhas)-1)){
				$conteudo .= $linha[0] . '\n';
			}
			if($key == (count($linhas)-1)){ //DADOS DO TRAILLER
				$linhaTrailler = $linha[0];
				$titulos = ($linha[26]-2);
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
				$nossoNumero 		 = $linha[10];
				$digitoNossoNumero   = $linha[11];
								
				$dataRecebimento 	 = $this->converteData($linha[26]);
				$dataVencimento 	 = $this->converteData($linha[29]);
				$dataCredito 		 = $this->converteData($linha[35]);
				$valorRecebido 		 = $this->converteNumero($linha[42]);
				$valorTitulo 		 = $this->converteNumero($linha[30]);
				$valorAbatimento 	 = $this->converteNumero($linha[40]);
				$valorDesconto 		 = $this->converteNumero($linha[41]);
				$valorJurosMora 	 = $this->converteNumero($linha[43]);
				$valorIOF 			 = $this->converteNumero($linha[22]);
				$valorOutrosCreditos = $this->converteNumero($linha[44]);

				$especieTitulo 		 = $linha[34];
				$codigoNaturezaRec 	 = $linha[25];
				$codigoBancoRecebedor= $linha[31];
				$agenciaRecebedora 	 = $linha[32];
				$dvAgenciaRecebedora = $linha[33];
				$numeroDocumento     = $linha[9];
				$carteira 			 = $linha[24];

				$codigoNossoNumero = $this->formataNossoNumero($nossoNumero, $digitoNossoNumero, $carteira);
				$nossoNumeroComDigito = $nossoNumero . '-' . $digitoNossoNumero;
				$sql = mysql_query("SELECT * FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id WHERE T117_Nosso_Numero = '{$codigoNossoNumero}' OR T117_Nosso_Numero = '{$nossoNumero}' OR T117_Nosso_Numero = '{$nossoNumeroComDigito}'");
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
										'{$digitoNossoNumero}', '{$codigoNaturezaRec}', '{$carteira}', '{$dataRecebimento}', '0',
										'{$dataVencimento}', '{$valorTitulo}', '{$codigoBancoRecebedor}', '{$agenciaRecebedora}', '{$dvAgenciaRecebedora}',
										'{$especieTitulo}', '{$dataCredito}', '{$valorOutrosCreditos}', '{$valorIOF}', '{$valorAbatimento}',
										'{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[59]}', '{$linha[0]}', '{$T038_Flag_Autorizado}'
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
		$T038 = mysql_query("SELECT * FROM T038 LEFT JOIN T037 ON T037_Id = T038_T037_Id LEFT JOIN T002 ON T002_Id = T038_T002_Id WHERE T038_T037_Id = '{$T037_Id}'{$extraWhere} ORDER BY T038_Id ASC{$extraLimit}");
		while($res = mysql_fetch_array($T038)){
			$lidos++;
			$ultimoIdLido = $res['T038_Id'];
			$codigoBanco = $res['T037_Codigo_Banco'];
			$dataBaixa = $res['T038_Data_Credito'];
			$dataRecebimento = $res['T038_Data_Recebimento'];
			if(!empty($res['T002_Id'])){
				$T002_Id = $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'];
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N') {
					if($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15'){ //Titulos a serem baixados
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
						mysql_query("UPDATE T038 SET T038_C007_Id_Processado = '{$g['usuarioAtual']}', T038_Data_Hora_Processado = CURRENT_TIMESTAMP() WHERE T038_Id = '{$res['T038_Id']}'");
						mysqli_query("CALL T002_Gravar_Totalizacao('{$res['T002_Id']}')");
						//Busca o codigo do banco
						$sql = mysql_query("SELECT D007_Id FROM D007 LEFT JOIN D062 ON D062_Id = D007_D062_Id WHERE D062_Numero_Banco = '{$codigoBanco}'");
						$res = mysql_fetch_array($sql);
						//Baixa os Titulos
						//$data = $dataCredito;
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
