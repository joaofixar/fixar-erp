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
		01-Identificação do Registro					(001 001) 001
		02-Tipo de Operação: “2”						(002 002) 001
		03-Identificação Tipo de Operação				(003 009) 007
		04-Identificação do Tipo de Serv.				(010 011) 002
		05-Identificação por Extenso					(012 019) 008
		06-Complemento do Registro						(020 026) 007
		07-Prefixo da Agência							(027 030) 004
		08-Dígito Verificador							(031 031) 001
		09-Número da Conta Corrente						(032 039) 008
		10-Dígito Verificador							(040 040) 001
		11-Zeros										(041 046) 006
		12-Nome do Cedente								(047 076) 030
		13-001BANCODOBRASIL								(077 094) 018
		14-Data da Gravação								(095 100) 006
		15-Seqüencial do Retorno 						(101 107) 007
		16-Complemento do Registro						(108 149) 042
		17-Número de convênio							(150 156) 007
		18-Complemento do Registro						(157 394) 238
		19-Nr. Seqüencial 								(395 400) 006

		//#### BODY ####
		01-Identificação do Registro Detalhe: 7			(001 001) 001
		02-Zeros										(002 003) 002
		03-Zeros										(004 017) 014
		04-Prefixo da Agência							(018 021) 004
		05-Dígito Verificador							(022 022) 001
		06-Número da Conta Corrente do Cedente			(023 030) 008
		07-Dígito Verificador							(031 031) 001
		08-Número do Convênio 							(032 038) 007
		09-Número de Controle							(039 063) 025
		10-Nosso-Número									(064 080) 017
		11-Tipo de cobrança 02							(081 081) 001
		12-Tipo de cobrança para comando 72				(082 082) 001
		13-Dias para cálculo 04							(083 086) 004
		14-Natureza do recebimento 05					(087 088) 002
		15-Prefixo do Título 06							(089 091) 003
		16-Variação da Carteira							(092 094) 003
		17-Conta Caução 06								(095 095) 001
		18-Taxa para desconto							(096 100) 005
		19-Taxa IOF										(101 105) 004
		20-Branco										(106 106) 001
		21-Carteira										(107 108) 002
		22-Comando										(109 110) 002
		23-Data de liquidação							(111 116) 006
		24-Número do título dado pelo cedente			(117 126) 010
		25-Brancos										(127 146) 020
		26-Data de vencimento							(147 152) 006
		27-v99 Valor do título							(153 165) 011
		28-Código do banco recebedor 08					(166 168) 003
		29-Prefixo da agência recebedora 08				(169 172) 004
		30-DV prefixo recebedora						(173 173) 001
		31-Espécie do título 09							(174 175) 002
		32-Data do crédito (DDMMAA) 10					(176 181) 006
		33-v99 Valor da tarifa 06						(182 188) 005
		34-v99 Outras despesas							(189 201) 011
		35-v99 Juros do desconto						(202 214) 011
		36-v99 IOF do desconto							(215 227) 011
		37-v99 Valor do abatimento						(228 240) 011
		38-v99 Desconto concedido						(241 253) 011
		39-v99 Valor recebido							(254 266) 011
		40-v99 Juros de mora							(267 279) 011
		41-v99 Outros recebimentos						(280 292) 011
		42-v99 Abatimento não aproveitado				(293 305) 011
		43-v99 Valor do lançamento						(306 318) 011
		44-Indicativo de débito/crédito 11				(319 319) 001
		45-Indicador de valor 12						(320 320) 001
		46-v99 Valor do ajuste 13						(321 332) 010
		47-Brancos										(333 333) 001
		48-Brancos										(334 342) 009
		49-Zeros										(343 349) 007
		50-Zeros										(350 358) 009
		51-Zeros										(359 365) 007
		52-Zeros										(366 374) 009
		53-Zeros										(375 381) 007
		54-Zeros										(382 390) 009
		55-Brancos										(391 392) 002
		58-Canal de pagamento							(393 394) 002
		59-Seqüencial do registro						(395 400) 006

		//#### TRAILLER ####
		01-Identificação do Registro Trailer: “9”		(001 001) 001
		02-“2”											(002 002) 001
		03-“01”											(003 004) 002
		04-“001”										(005 007) 003
		05-Brancos										(008 017) 010
		06-Cobrança Simples - quantidade de títulos		(018 025) 008
		07-v99 Cobrança Simples - valor total			(026 039) 013
		08-Cobrança Simples - Número do aviso			(040 047) 008
		09-Cobrança Simples - Brancos					(048 057) 010
		10-Cobrança Vinculada - quantidade de títulos	(058 065) 008
		11-v99 Cobrança Vinculada - valor total			(066 079) 013
		12-Cobrança Vinculada - Número do aviso			(080 087) 008
		13-Cobrança Vinculada - Brancos					(088 097) 010
		14-Cobrança Caucionada - quantidade de títulos	(098 105) 008
		15-v99 Cobrança Caucionada - valor total		(106 119) 013
		16-Cobrança Caucionada - Número do aviso		(120 127) 008
		17-Cobrança Caucionada - Brancos				(128 137) 010
		18-Cobrança Descontada - quantidade de títulos	(138 145) 008
		19-v99 Cobrança Descontada - valor total		(146 159) 013
		20-Cobrança Descontada - Número do aviso		(160 167) 008
		21-Cobrança Descontada - Brancos				(168 217) 050
		22-Cobrança Vendor - quantidade de títulos		(218 225) 008
		23-v99 Cobrança Vendor - valor total			(226 239) 013
		24-Cobrança Vendor - Número do aviso			(240 247) 008
		25-Cobrança Vendor – Brancos					(248 394) 147
		26-Seqüencial do registro						(395 400) 006
		*/

		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER
				preg_match("/(.{1})(.{1})(.{7})(.{2})(.{8})(.{7})(.{4})(.{1})(.{8})(.{1})(.{6})(.{30})(.{18})(.{6})(.{7})(.{42})(.{7})(.{238})(.{6})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-1)) { //TRAILLER
				preg_match("/(.{1})(.{1})(.{2})(.{3})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{10})(.{8})(.{14})(.{8})(.{50})(.{8})(.{14})(.{8})(.{147})(.{6})/", $linha, $posicoes);
			}else { //CONTEUDO
				preg_match("/(.{1})(.{2})(.{14})(.{4})(.{1})(.{8})(.{1})(.{7})(.{25})(.{17})(.{1})(.{1})(.{4})(.{2})(.{3})(.{3})(.{1})(.{5})(.{4})(.{2})(.{2})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{4})(.{1})(.{2})(.{6})(.{7})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{1})(.{1})(.{12})(.{1})(.{9})(.{7})(.{9})(.{7})(.{9})(.{7})(.{9})(.{2})(.{2})(.{6})/", $linha, $posicoes);				
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
				$codigoBanco = str_pad($codigoBanco, 3, "0", STR_PAD_LEFT);			
				$linhaHeader = $linha[0];

				$agencia 	 = $linha[7];
				$conta 		 = trim($linha[9],'0');
				$dvConta 	 = $linha[10];
			}
			//CONTEUDO DO BODY
			if($key != 0 && $key != (count($linhas)-1)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
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
		//$conteudoArquivo = $linhaHeader . '\n' . $conteudo . $linhaTrailler;
		$conteudoArquivo = $linhaHeader;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $conteudo . $linhaTrailler;

		$sqlD007 = mysql_query("SELECT D007_Id FROM D007 WHERE D007_Numero_Agencia = '{$agencia}' AND D007_Numero_Conta = '{$conta}' AND D007_Digito_Verificador_Conta = '{$dvConta}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com a agência {$agencia} e conta {$conta}-{$dvConta}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com a agência {$agencia} e conta {$conta}-{$dvConta}, por favor verifique.";
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
				/*
				/ ALTERAR O NUMERO DA LINHA DE ACORDO COM O LAYOUT DO BANCO
				*/
				$nossoNumero 		 = $linha[10];
				$digitoNossoNumero   = substr($linha[10],-1,1);

				$dataRecebimento 	 = $this->converteData($linha[23]);
				$dataVencimento 	 = $this->converteData($linha[26]);
				$dataCredito 		 = $this->converteData($linha[32]);
				$valorRecebido 		 = $this->converteNumero($linha[39]);
				$valorTitulo 		 = $this->converteNumero($linha[27]);
				$valorAbatimento 	 = $this->converteNumero($linha[37]);
				$valorDesconto 		 = $this->converteNumero($linha[38]);
				$valorJurosMora 	 = $this->converteNumero($linha[40]);
				$valorIOF 			 = $this->converteNumero($linha[19]);
				$valorOutrosCreditos = $this->converteNumero($linha[41]);

				$especieTitulo 		 = $linha[31];
				$codigoNaturezaRec 	 = $linha[22];
				$codigoBancoRecebedor= $linha[28];
				$agenciaRecebedora 	 = $linha[29];
				$dvAgenciaRecebedora = $linha[30];
				$numeroDocumento     = $linha[9];
				$carteira 			 = $linha[16];

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
			if(!empty($res['T002_Id'])){
				$T002_Id = $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'];
				
				// Implementação realizada pois ao processar o retorno dos títulos não estava processando pois deposi de gerar o título o cliente atualizava vencimento do boleto, enviava par ao banco e ao processar retorno os valores do título não correspondia com o valor do retorno.
				$outrosJuros = false;
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ // Outros juros que vem direamente no valor recebido
					$outrosJuros = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}
				
				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					if($res['T038_Codigo_Natureza_Recebimento'] == '05' || $res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15'){ //Titulos a serem baixados
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

						// Outros juros que vem diretamente no valor recebido
						if($outrosJuros === true && $res['T038_Valor_Juros_Mora'] <=0 && $res['D007_Baixar_Automatico_Valor_Excedido_Retorno'] == 'S'){ 
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
