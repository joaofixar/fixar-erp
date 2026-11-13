<?php
namespace hardness;

class RETORNO_REMESSA {
	public function importarRetorno($arquivo) {
		global $g;

		// FOI CRIADO O RETORNO DO CNAB240 PORÉM NÃO TIVEMOS UM ARQUIVO PARA TESTAR, PODE SER QUE PRECISE DE AJUSTES QUANDO FOR UTILIZADO
		// FELIPE CARRANO - 10/07/2023 (OBS: FOI REALIZADO O DEV PARA UNITYERP, CLIENTE FIXWELL)
		// APÓS AJUSTAR, RETIRAR OS COMENTÁRIOS ACIMA.
        
		$fileName = $arquivo;
		$mArquivo = $arquivo;
		$cArquivo = $mArquivo["name"];

		if (empty($arquivo['name'])){
			return 'Selecione o arquivo';
		}

		$arquivo = file($mArquivo['tmp_name']);
		$linhas  = array();

		/*
		NOMES DOS CAMPOS E AS POSIÇÕES DO ARRAY

		############# HEADER DO ARQUIVO #############    
		1-CÓDIGO DO BANCO 			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO  		    (008 008)  01
		4-BRANCO             		    (009 017)  09
		5-TIPO DE INSCRIÇÃO DA EMPRESA  (018 018)  01
		6-Nº DE INSCRIÇÃO NA EMPRESA    (019 032)  14
		7-Nº CONVÊNIO				    (033 041)  09
		8-CÓDIGO 					    (042 045)  04
		9-USO DO BANCO				    (046 050)  05
		10-ARQUIVO TESTE			    (051 052)  02
		11-AGENCIA		                (053 057)  05
		12-DV AGENCIA   			    (058 058)  01
		13-NUMERO CONTA 			    (059 070)  12
		14-DV CONTA		                (071 071)  01
		15-DV CONTA/AG				    (072 072)  01
		16-NOME EMPRESA                 (073 102)  30
		17-NOME BANCO                   (103 132)  30
		18-USO FENABRAN		            (133 142)  10
		19-CODIGO REMESSA/RETORNO 	    (143 143)  01
		20-DATA GERACAO ARQUIVO   	    (144 151)  08
		22-HORA GERACAO ARQUIVO		    (152 157)  06
		23-VERSAO LAYOUT ARQUIVO        (164 166)  03
		24-DENSIDADE GRAVAO ARQUIVO     (167 171)  05
		25-USO RESERVADO BANCO          (172 191)  19
        26-USO RESERVADO EMPRESA        (192 211)  20
        27-USO RESERVADO FENABRAN       (212 222)  11
        28-COBRANCA SEM PAPEL           (223 225)  03
        29-USO RESERVADO VANS           (226 228)  03
        30-TIPO SERVICO                 (229 230)  02
        31-CODIGO OCORRENCIAS           (231 240)  10

        ############## HEADER DO LOTE ###############    
		1-CÓDIGO DO BANCO 			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO   		    (008 008)  01
		4-TIPO DE OPERAÇÃO    		    (009 009)  01
		5-TIPO DE SERVIÇO      		    (010 011)  02
		6-BRANCOS                       (012 013)  02
		7-Nº VERSÃO LAYOUT              (014 016)  03
		8-BRANCOS 					    (017 017)  01
		9-TIPO DE INSCRIÇÃO DA EMPRESA  (018 018)  01
		10-Nº INSCRIÇÃO EMPRESA		    (019 033)  15
		11-CONVÊNIO                     (034 053)  20
		12-AGÊNCIA               	    (054 058)  05
		13-DV AGÊNCIA    			    (059 059)  01
		14-CONTA CORRENTE 			    (060 071)  12
		15-DV CONTA CORRENTE     	    (072 072)  01
		16-DV AG/CC          		    (073 073)  01
		17-NOME DA EMPRESA       	    (074 103)  30
		18-MENSAGEM 1    			    (104 143)  40
		19-MENSAGEM 2 				    (144 183)  40
		20-NÚMERO RETORNO        	    (184 191)  08
		21-DATA GRAVAÇÃO RETORNO        (192 199)  08
		22-DATA CRÉDITO      		    (200 207)  08
		23-BRANCOS           		    (208 240)  30
		    
		################### BODY #################### 
		################# SEGMENTO T ###############       
		1-CÓDIGO DO BANCO 			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO  		    (008 008)  01
		4-Nº SEQ DO REGISTRO NO LOTE    (009 013)  05
		5-COD SEGMENTO DO REGISTRO	    (014 014)  01
		6-BRANCOS 					    (015 015)  01
		7-COD MOVIMENTO				    (016 017)  02
		8-AGÊNCIA       			    (018 022)  05
		9-DV AGÊNCIA    			    (023 023)  01
		10-CONTA CORRENTE			    (024 035)  12
		11-DV CONTA CORRENTE     	    (036 036)  01
		12-DV AG/CC                     (037 037)  01
		13-NOSSO NÚMERO                 (038 057)  20
		14-CARTEIRA             	    (058 058)  01
		15-NÚMERO DO DOCUMENTO          (059 073)  15
		16-VENCIMENTO            	    (074 081)  08
		17-VALOR DO TITULO              (082 096)  13 2
		18-NÚMERO DO BANCO COBRADOR     (097 099)  03
		19-AGENCIA COBRADORA            (100 104)  05
		20-DV AG COBRADORA   		    (105 105)  01
		21-USO DA EMPRESA T117_ID       (106 130)  25
		22-MOEDA                        (131 132)  02
		23-TIPO DE INSCRIÇÃO    	    (133 133)  01
		24-CNPJ/CPF EMPRESA     	    (134 148)  15
		25-NOME DA EMPRESA 			    (149 188)  40
		26-CONTRATO          		    (189 198)  10
		27-VALOR TARIFA/CUSTAS 		    (199 213)  13 2
		28-MOTIVO OCORRENCIA  		    (214 223)  10
		29-CNAB BRANCOS      		    (224 240)  17

		################# SEGMENTO U ################       
		1-CÓDIGO DO BANCO 			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO  		    (008 008)  01
		4-Nº SEQ DO REGISTRO NO LOTE    (009 013)  05
		5-COD SEGMENTO DO REGISTRO	    (014 014)  01
		6-BRANCOS 					    (015 015)  01
		7-COD MOVIMENTO				    (016 017)  02
		8-JUROS/MULTA/ENCARGOS		    (018 032)  13 2
		9-VALOR DO DESCONTO CONDEDIDO   (033 047)  13 2
		10-VALOR DO ABATIMENTO		    (048 062)  13 2
		11-VALOR IOF              	    (063 077)  13 2
		12-VALOR PAGO PELO SACADO       (078 092)  13 2
		13-VALOR LIQUIDO A SER CRED.    (093 107)  13 2
		14-VALOR OUTRAS DESPESAS   	    (108 122)  13 2
		15-VALOR DE OUTROS CRÉDITOS     (123 137)  13 2
		16-DATA OCORRENCIA         	    (138 145)  08
		17-DATA EFET. CREDITO           (146 153)  08
		18-CODIGO DA OCORRENCIA         (154 157)  04
		19-DATA OCORRENCIA              (158 165)  08
		20-VALOR DA OCORRENCIA          (166 180)  13 2
		21-COMPLEM. OCORRENCIA          (181 210)  30
		22-COD BANCO                    (211 213)  03
		23-NOSSO NUMERO                 (214 233)  20
		24-BRANCOS                      (234 240)  07
    
		############## TRAILLER DO LOTE #############
		1-COD DO BANCO  			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO  		    (008 008)  01
		4-BRANCOS       			    (009 017)  09
		5-QTDE. DE REGISTROS NO LOTE    (018 023)  06
		6-VALOR TOTAL DOS TIT.   	    (024 041)  15 2
        7-QUANTIDADE MOEADAS   	        (042 059)  13 5
        8-NÚMERO AVISO DÉBITO  	        (060 065)  6
        9-CNAB               	        (066 230)  165
        10-OCORRENCIAS         	        (231 240)  10

		############ TRAILLER DO ARQUIVO #############
		1-COD DO BANCO  			    (001 003)  03
		2-LOTE DE SERVIÇO    		    (004 007)  04
		3-TIPO DE REGISTRO  		    (008 008)  01
		4-BRANCOS       			    (009 017)  01
		5-QTDE. DE LOTES NO ARQUIVO     (018 023)  09
		6-QTDE. DE REGISTROS NO ARQUIVO (024 029)  06
		7-QTDE. DE CONTAS PACA CONC.    (030 035)  06
		8-BRANCOS       			    (036 240)  205
		*/

		//as expressões regulares abaixo tiram os espaços em branco
		foreach ($arquivo as $nr => $linha) {
			if($nr == 0) { //HEADER DO ARQUIVO
				//preg_match("/(.{3})(.{4})(.{1})(.{9})(.{1})(.{14})(.{20})(.{5})(.{1})(.{12})(.{1})(.{1})(.{30})(.{30})(.{10})(.{1})(.{8})(.{6})(.{6})(.{3})(.{5})(.{20})(.{20})(.{29})/", $linha, $posicoes);
                preg_match("/(.{3})(.{4})(.{1})(.{9})(.{1})(.{14})(.{9})(.{4})(.{5})(.{2})(.{5})(.{1})(.{12})(.{1})(.{1})(.{30})(.{30})(.{10})(.{1})(.{8})(.{6})(.{3})(.{5})(.{19})(.{20})(.{11})(.{3})(.{3})(.{2})(.{10})/", $linha, $posicoes);
			} else if($nr == 1){ // HEADER DO LOTE
				preg_match("/(.{3})(.{4})(.{1})(.{1})(.{2})(.{2})(.{3})(.{1})(.{1})(.{15})(.{20})(.{5})(.{1})(.{12})(.{1})(.{1})(.{30})(.{40})(.{40})(.{8})(.{8})(.{8})(.{33})/", $linha, $posicoes);           
            } else if($nr == (count($arquivo)-1)) { //TRAILLER DO ARQUIVO
				preg_match("/(.{3})(.{4})(.{1})(.{1})(.{9})(.{6})(.{6})(.{205})/", $linha, $posicoes);
			} else if($nr == (count($arquivo)-2)){//TRAILER DO LOTE
				//preg_match("/(.{3})(.{4})(.{1})(.{9})(.{6})(.{6})(.{17})(.{6})(.{17})(.{6})(.{17})(.{6})(.{17})(.{8})(.{117})/", $linha, $posicoes);
				preg_match("/(.{3})(.{4})(.{1})(.{9})(.{6})(.{17})(.{18})(.{6})(.{165})(.{10})/", $linha, $posicoes);
			} else if(($nr != 0) && ($nr != 1) && ($nr != (count($arquivo)-2)) && ($nr != (count($arquivo)-1)) && ($nr % 2 == 0)) { //CONTEUDO segmento T (linha pares e diferentes dos headers e trailers)
				preg_match("/(.{3})(.{4})(.{1})(.{5})(.{1})(.{1})(.{2})(.{5})(.{1})(.{12})(.{1})(.{1})(.{20})(.{1})(.{15})(.{8})(.{15})(.{3})(.{5})(.{1})(.{25})(.{2})(.{1})(.{15})(.{40})(.{10})(.{15})(.{10})(.{17})/", $linha, $posicoes);
			} else{ //CONTEÚDO segmento U (linhas pares e diferentes dos headers e trailers)
				preg_match("/(.{3})(.{4})(.{1})(.{5})(.{1})(.{1})(.{2})(.{15})(.{15})(.{15})(.{15})(.{15})(.{15})(.{15})(.{15})(.{8})(.{8})(.{4})(.{8})(.{15})(.{30})(.{3})(.{20})(.{7})/", $linha, $posicoes);
			}																									     
			$linhas[] = $posicoes;
		}

		$conteudo = '';
		//FOREACH do Header e do Trailler
		foreach ($linhas as $key => $linha) {
			//DADOS DO HEADER DO ARQUIVO
			if($key == 0){ 
				$linhaHeaderArquivo = $linha[0];
				$agencia 	        = $linha[11];
				$agencia 			= ltrim(strval($agencia), 0);
				$contacorrente      = $linha[13];
				$contacorrente 		= ltrim(strval($contacorrente), 0);
				$convenio           = $linha[7];
				$convenio 			= ltrim(strval($convenio), 0);
				$codigoBanco        = $linha[1];
			}
			//DADOS DO HEADER DO LOTE
			if($key == 1){
				$dataRetorno 	 = $this->converteData($linha[21]);
				$linhaHeaderLote = $linha[0];
			}
			//DADOS DO CONTEÚDO (SEGMENTO T)
			if($key != 0  && $key != 1 && $key != (count($linhas)-1) && $key != (count($linhas)-2) && ($key % 2 == 0)){
				//$conteudo .= $linha[0] . '\n';
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
			}
			//DADOS DO CONTEÚDO (SEGMENTO U)
			if($key != 0  && $key != 1 && $key != (count($linhas)-1) && $key != (count($linhas)-2) && ($key % 2 == 1)){
				$conteudo .= $linha[0];
				$conteudo .= PHP_EOL;
			}
			//DADOS DO TRAILER DO LOTE
			if($key == (count($linhas)-2)){ 
				$linhaTraillerLote = $linha[0];
				$titulos 		   = ($linha[7]/2-1);
			}

			//DADOS DO TRAILER DO ARQUIVO
			if($key == (count($linhas)-1)){ 
				$linhaTraillerArquivo = $linha[0];
				//$titulos = $linha[6];
			}

		}

		$linhaTrailler = $linhaTraillerLote.$linhaTraillerArquivo;

		//Verifica se o arquivo ja foi enviado
		$sql = mysql_query("SELECT T137_Id 
                              FROM T037 
                             WHERE T037_Linha_Header = '{$linhaHeaderArquivo}'");

		if(mysql_num_rows($sql) > 0){
			return 'Este arquivo ja foi enviado';
		}
		$conteudoArquivo = $linhaHeaderArquivo;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $linhaHeaderLote;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $conteudo;
		$conteudoArquivo .= $linhaTraillerLote;
		$conteudoArquivo .= PHP_EOL;
		$conteudoArquivo .= $linhaTraillerArquivo;
		
		$sqlD007 = mysql_query("SELECT D007_Id 
                                  FROM D007 
                                 WHERE D007_Numero_Agencia  = '{$agencia}' 
                                   AND D007_Numero_Conta    = '{$contacorrente}'
                                   AND D007_Numero_Convenio = '{$convenio}'");
		$rows = mysql_num_rows($sqlD007);
		if($rows == 0){
			return "Não foi possível localizar nenhum banco com a agência {$agencia},conta {$contacorrente} e convenio {$convenio}, por favor verifique.";
		} else if($rows > 1){
			return "Foi identificado mais de um banco com a agência {$agencia},conta {$contacorrente} e convenio {$convenio}, por favor verifique.";
		}
		$rowD007 = mysql_fetch_array($sqlD007);
		
		//Registro na tabela de histórico de remessas e retornos com upload
		$conteudoArquivo = mysql_real_escape_string($conteudoArquivo);
		$T037 = "INSERT INTO T037 (T037_C004_Id, 
							       T037_Nome_Arquivo, 
							       T037_Data_Processamento, 
							       T037_Numero_Titulos, 
							       T037_Linha_Header, 
							       T037_Linha_Trailler, 
							       T037_Codigo_Banco, 
							       T037_Conteudo_Arquivo, 
							       T037_D007_Id
					     ) VALUES (
								   '{$g['empresaAtual']}', 
								   '{$fileName['name']}', 
								   '{$dataRetorno}', 
								   '{$titulos}', 
								   '{$linhaHeaderArquivo}', 
								   '{$linhaTrailler}', 
								   '{$codigoBanco}', 
								   '{$conteudoArquivo}', 
								   '{$rowD007['D007_Id']}')";
							
		mysql_query($T037);
		$T037_Id = $g['mysqlLastId'];

		foreach ($linhas as $key => $linha) {
			if($key != 0  && $key != 1 && $key != (count($linhas)-1) && $key != (count($linhas)-2)){ //DADOS DO SEGMENTO T e U
				/*
				/ ALTERAR O NUMERO DA LINHA DE ACORDO COM O LAYOUT DO BANCO
				*/
				if($key % 2 == 0){//SEGMENTO T
					$usoEmpresa 		  = $linha[21]; //T117_Id
					$nossoNumero          = $linha[13];
					$dataVencimento 	  = $this->converteData($linha[16]);
					$valorTitulo 		  = $this->converteNumero($linha[17]);
					$agenciaRecebedora 	  = $linha[19];
					$dvAgenciaRecebedora  = $linha[20];
					$numeroDocumento      = $linha[15];
					$carteira 			  = $linha[14];
					$codigoBancoRecebedor = $linha[18];
					$motivoOcorrencia     = $linha[28];
					$valorOutrasDespesas = $this->converteNumero($linha[27]);
				}

				if($key % 2 == 1){//SEGMENTO U
					$dataRecebimento 	 = $this->converteData($linha[16]);
					$dataCredito 		 = $this->converteData($linha[17]);
					$valorRecebido 		 = $this->converteNumero($linha[13]); // Acrescenta o valor da taxa do banco no recebido
					$valorAbatimento 	 = $this->converteNumero($linha[10]);
					$valorDesconto 		 = $this->converteNumero($linha[9]);
					$valorJurosMora 	 = $this->converteNumero($linha[8]);
					$valorIOF 			 = $this->converteNumero($linha[11]);
					$valorOutrosCreditos = $this->converteNumero($linha[15]);
					$codigoMovimento     = $linha[7];					
					$codigoMotivo        = $linha[18];

					//Verifica os boletos da empresa atual, caso não encontre verifica em todas as empresas
					$sql = mysql_query("SELECT T002_Id 
										  FROM T117 
									 LEFT JOIN T002 ON T002_T117_Id=T117_Id 
									     WHERE T117_C004_Id = '{$g['empresaAtual']}' 
										   AND (T117_Nosso_Numero = '{$nossoNumero}' OR T117_Id = '{$usoEmpresa}')");

					if(mysql_num_rows($sql) > 0){
						$res     = mysql_fetch_array($sql);
						$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;

					} else {

						$sql = mysql_query("SELECT T002_Id 
										      FROM T117 
									     LEFT JOIN T002 ON T002_T117_Id=T117_Id 
										     WHERE T117_Nosso_Numero = '{$nossoNumero}' 
											    OR T117_Id = '{$usoEmpresa}'");

						if(mysql_num_rows($sql) > 0){
							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;

						} else {
							$sql = mysql_query("SELECT T002_Id 
                                                  FROM T117 
                                             LEFT JOIN T002 ON T002_T117_Id=T117_Id 
                                                 WHERE T117_Id = '{$usoEmpresa}'");

							$res = mysql_fetch_array($sql);
							$T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
						}
					}

					$T038_Flag_Autorizado = '';
					if(!empty($T002_Id) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0){
						$T038_Flag_Autorizado = 'N';
					}

					$sql = "INSERT INTO T038 (T038_T037_Id, 
								              T038_T002_Id, 
								              T038_C004_Id, 
								              T038_Numero_Documento, 
								              T038_Nosso_Numero,
								              T038_Codigo_Natureza_Recebimento, 
								              T038_Carteira, 
								              T038_Data_Recebimento, 
								              T038_Descricao_Motivo,
								              T038_Data_Vencimento, 
								              T038_Valor_Titulo, 
								              T038_Codigo_Banco_Recebedor, 
								              T038_Prefixo_Agencia_Recebedora, 
								              T038_Digito_Verificador_Agencia_Recebedora, 
								              T038_Data_Credito, 
								              T038_Valor_Outros_Recebimentos, 
								              T038_Valor_Taxa_IOF, 
								              T038_Valor_Abatimento, 
								              T038_Valor_Outras_Despesas,
								              T038_Valor_Desconto, 
								              T038_Valor_Recebido, 
								              T038_Valor_Juros_Mora, 
								              T038_String, 
								              T038_Flag_Autorizado,
								              T038_Codigo_Motivo
						            ) VALUES ('{$T037_Id}', 
								              '{$T002_Id}', 
								              '{$g['empresaAtual']}', 
								              '{$numeroDocumento}', 
								              '{$nossoNumero}',
								              '{$codigoMovimento}', 
								              '{$carteira}', 
								              '{$dataRecebimento}', 
								              '{$motivoOcorrencia}',
								              '{$dataVencimento}', 
								              '{$valorTitulo}', 
								              '{$codigoBancoRecebedor}', 
								              '{$agenciaRecebedora}', 
								              '{$dvAgenciaRecebedora}',
								              '{$dataCredito}', 
								              '{$valorOutrosCreditos}', 
								              '{$valorIOF}', 
								              '{$valorAbatimento}', 
								              '{$valorOutrasDespesas}',
								              '{$valorDesconto}', 
								              '{$valorRecebido}', 
								              '{$valorJurosMora}', 
								              '{$linha[0]}',
								              '{$T038_Flag_Autorizado}',
								              '{$motivoOcorrencia}');";
					mysql_query($sql);
				}					
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
			$codigoBanco     = $res['T037_Codigo_Banco'];
			$dataBaixa       = $res['T038_Data_Credito'];
			$dataRecebimento = $res['T038_Data_Recebimento'];
			if(!empty($res['T002_Id'])){
				$T002_Id        = $res['T002_Id'];
				$valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'] - $res['T038_Valor_Outras_Despesas'];
				
				// Implementação realizada pois ao processar o retorno dos títulos não estava processando pois deposi de gerar o título o cliente atualizava vencimento do boleto, enviava par ao banco e ao processar retorno os valores do título não correspondia com o valor do retorno.
				$outrosJuros = false;
				if($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)){ // Outros juros que vem direamente no valor recebido
					$outrosJuros    = true;
					$valorCalculado = $res['T038_Valor_Recebido'];
					$valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
				}
				
				$valorCalculado = number_format($valorCalculado, 2, '.', '');
				if(($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
					if($res['T038_Codigo_Natureza_Recebimento'] == '05' || $res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '15'){ //Titulos a serem baixados
						
                        if($res['T038_Valor_Juros_Mora'] > 0){ //Juros
							mysql_query("INSERT INTO T020 (T020_T002_Id, 
                                                           T020_C004_Id, 
                                                           T020_Historico, 
                                                           T020_Data_Lancamento, 
                                                           T020_Valor, 
                                                           T020_Flag_Deb_Cred
												 ) VALUES ('{$res['T002_Id']}', 
                                                           '{$g['empresaAtual']}', 
                                                           '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(),
                                                           '{$res['T038_Valor_Juros_Mora']}',
                                                           'C')");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'JM');
						}

						if($res['T038_Valor_Taxa_IOF'] > 0){ //IOF
							mysql_query("INSERT INTO T020 (T020_T002_Id, 
                                                           T020_C004_Id, 
                                                           T020_Historico, 
                                                           T020_Data_Lancamento, 
                                                           T020_Valor, 
                                                           T020_Flag_Deb_Cred
												 ) VALUES ('{$res['T002_Id']}',
                                                           '{$g['empresaAtual']}',
                                                           '{$res['T037_Nome_Arquivo']} - IOF', 
                                                            CURDATE(), 
                                                           '{$res['T038_Valor_Taxa_IOF']}', 
                                                           'C')");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'I');
						}

						if($res['T038_Valor_Outros_Recebimentos'] > 0){ //Outros Créditos
							mysql_query("INSERT INTO T020 (T020_T002_Id, 
                                                           T020_C004_Id, 
                                                           T020_Historico, 
                                                           T020_Data_Lancamento, 
                                                           T020_Valor, 
                                                           T020_Flag_Deb_Cred
												 ) VALUES ('{$res['T002_Id']}', 
                                                           '{$g['empresaAtual']}', 
                                                           '{$res['T037_Nome_Arquivo']} - Outros créditos', 
                                                           CURDATE(), 
                                                           '{$res['T038_Valor_Outros_Recebimentos']}', 
                                                           'C')");

							$T020_Id = $g['mysqlLastId'];
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OR');
						}

						// Outros juros que vem diretamente no valor recebido
						if($outrosJuros === true && $res['T038_Valor_Juros_Mora'] <=0 && $res['D007_Baixar_Automatico_Valor_Excedido_Retorno'] == 'S'){ 
							mysql_query("INSERT INTO T020 (T020_T002_Id, 
                                                           T020_C004_Id, 
                                                           T020_Historico, 
                                                           T020_Data_Lancamento, 
                                                           T020_Valor, 
                                                           T020_Flag_Deb_Cred
												 ) VALUES ('{$res['T002_Id']}', 
                                                           '{$g['empresaAtual']}', 
                                                           '{$res['T037_Nome_Arquivo']} - JUROS', 
                                                           CURDATE(), 
                                                           '{$valorDiferenca}', 
                                                           'C')");

							$T020_Id = $g['mysqlLastId'];
							$res['outrosJuros'] = $valorDiferenca;
							$FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OJ');

						}

						mysql_query("UPDATE T038 SET T038_C007_Id_Processado = '{$g['usuarioAtual']}', 
                                                     T038_Data_Hora_Processado = CURRENT_TIMESTAMP() 
                                               WHERE T038_Id = '{$res['T038_Id']}'");

						mysqli_query("CALL T002_Gravar_Totalizacao('{$res['T002_Id']}')");
						$extra = base64_encode("WHERE (T002_Id = '{$T002_Id}')");
						$FIN001->baixarRetorno($res['T037_D007_Id'], $dataBaixa, $extra, $dataRecebimento);

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
			$ano = substr($data, 4, 4);
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
