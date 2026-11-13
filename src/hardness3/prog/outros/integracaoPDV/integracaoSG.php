<?php
namespace hardness;
set_time_limit(60000);

// $g['empresaAtual'] = 2;

switch ($g['r'][3]) {
	/*
	case 'contentExportar':
		// Como serÃ¡ dividio o conteÃºdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que serÃ¡ carregado em cada conteÃºdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/outros/integracaoPDV/integracaoSG/formExportar/', '')
			);
		echo gProcessaAcoes($actions);
	break;

	case 'formExportar':
		echo <<<EOT
			<div style="margin: 10px 10px">
				<button id="produtosBtn"><a href="/outros/integracaoPDV/integracaoSG/exportarProdutos/?ajax=true">Produtos</a></button><br />
				<button id="clienteBtn"><a href="/outros/integracaoPDV/integracaoSG/exportarClientes/?ajax=true">Clientes</a></button>
			</div>
EOT;
	break;
	*/

	case 'exportarProdutos':

		//die("chegou");
		/*
		------------------      produtos.txt     ------------------
		Produtos
		Nº Campo      Denominação do Campo                      Tam  Dec Tipo
		01 IDENTIF    CAMPO FIXO 01 PRODUTOS                    002    0    C
		02 CODFIL01   FILIAL                                    002    0    N
		03 CODPRO01   CODIGO DO PRODUTO                         006    0    N
		04 DESCABR01  DESCRICAO ABREVIADA DO PRODUTO            026    0    C
		05 PREVEND01  PRECO DE VENDA                            012    3    N
		06 PRODESS01  TIPO DO PRODUTO - PERM. COMBINACOES A PA  003    0    C  -----MELHORAR A EXPLICAÇÃO
		07 ALIKICM01  ALIQUOTA DE ICMS P/VENDA NO ESTADO        005    2    N
		08 SUBTRI01   PRODUTO DE SUB. TRIB. [S]IM [ ]NAO        001    0    C
		09 BALANCA01  PRODUTO DE BALANCA                        001    0    C
		10 PREVEND201 PRECO DE VENDA 2 DO PRODUTO               012    3    N
		11 MAXDESC01  PERCENTUAL MAXIMO DE DESCONTO NO ITEM NA  005    2    N
		12 UNIDPRO01  UNIDADE DE PRODUTO                        002    0    C
		13 ESTATU01   ESTOQUE ATUAL                             011    3    N
		14 DESCPRO01  DESCRICAO DO PRODUTO                      035    0    C
		15 CUSREAL01  CUSTO REAL                                011    3    N
		16 PRECUST01  PRECO DE CUSTO                            011    3    N
		17 CUSTENC01  CUSTO COM ENCARGOS                        011    3    N
		18 CUSMEDIO01 CUSTO MEDIO DO CUSREAL01.                 011    3    N
		19 CUSTFIS01  CUSTO FISCAL                              011    3    N
		20 PISCOFIN01 'S'->PIS/COFINS JA RECOLHIDO NA FONTE,    001    0    C
		21 PCPRESUM01 PERCENTUAL PRESUMIDO DO PIS E COFINS      008    4    N
		22 CODNCM01   CODIGO DA NOMENCLATURA COMUM DO MERCOSUL  010    0    C
		              EX: 0000.00.00, 0709.90.00
		23 NATPISCO01 NATUREZA DA OPERACAO DE PIS/COFINS        003    0    N
		24 PBCREDUZ01 PERCENTUAL DE REDUCAO DA ULTIMA ENTRADA   006    3    N
		25 REDPISCO01 PERCENTUAL DE REDUCAO DE PIS/COFINS       005    2    N
		26 NOVOCODIGO PERC TRIBUTOS LEI TRANSPARENCIA
		27 ORIGCST01  ORIGEM DA MERCADORIA						001	   0    N
		28 CODCEST01  CODIGO CEST DO PRODUTOS                 C 009    0    N
		29 ALQFECOP01 ALIQUOTA FUNDO DE COMBATE A POBREZA     N 005    2    N

		Codigo de barras dos produtos
		01 IDENTIF    CAMPO FIXO 02 CODIGO DE BARRAS            002    0    C
		02 CODPRO     CODIGO DO PRODUTO                         006    0    N
		03 CODBARRA   CODIGO DE BARRA DO PRODUTO                014    0    N
		04 QTDEEMBAL  QUANTIDADE POR EMBALAGEM                  007    2    N
		*/

		$sqlD053 = mysql_query("SELECT D053_Aliquota_ICMS_1 FROM D053 WHERE D053_D018_Id = '{$g['C004']['C004_D018_Id']}' AND D053_C004_Id = '{$g['empresaAtual']}'");
		if(mysql_error()){
			die('2 Erro SQL: '.mysql_error());
		}
		
		$rowD053 = mysql_fetch_assoc($sqlD053);

		//Verifica o flag_st da CFOP COM ST setada nas configurações globais
		$D006comST = mysql_query("SELECT D006_Flag_Substituicao_Tributaria FROM D006 WHERE D006_Id='{$g['C031']['cfopCupomFiscalComST']}'");
		$D006comST = mysql_fetch_assoc($D006comST);

		//Verifica o flag_st da CFOP SEM ST setada nas configurações globais
		$D006semST = mysql_query("SELECT D006_Flag_Substituicao_Tributaria FROM D006 WHERE D006_Id='{$g['C031']['cfopCupomFiscalSemST']}'");
		$D006semST = mysql_fetch_assoc($D006semST);

		$caracteresRemovidos = array('"', '\'', '-', '+', '(', ')', '%', '=', '_', 'Ø', 'Ã', 'Ç', ':', 'º', '°', '²', 'Ê', );
		$conteudo = array();
		$conteudoProduto = array();


		$limit = 0;
		$codigosBarraUtilizados = array();
		while (true) {

			$sql = mysql_query("SELECT D001_Id,
									D149_Flag_ST,
									SUBSTRING(D001_Descricao_Produto, 1, 35) AS D001_Descricao_Produto,
									D005_Classificacao_Fiscal,
									D009_Preco_Loja,
									D037_Unidade,
									D009_Quantidade_Estoque_Loja,
									D009_Valor_Custo_Unitario,
									D009_Valor_Custo_Ultima_Compra,
									D009_Valor_Custo_Medio_Unitario,
									D149_Percentual_Trib_Transparencia,
									D001_Codigo_Barras,
									D001_Qte_Embalagem,
									D001_Codigo_Barra1,
									D049_Origem_Mercadoria,
									D149_Codigo_CEST
								FROM D001 
								LEFT JOIN D049 ON D049_D001_Id=D001_Id 
								LEFT JOIN D009 ON D009_D049_Id=D049_Id 
								LEFT JOIN D005 ON D001_D005_Id=D005_Id
								LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id=D009_C004_Id
								LEFT JOIN D037 ON D001_D037_Id=D037_Id 
								WHERE D001_Flag_Pre_Cadastro = 'N' 
								AND D001_Flag_Ativo = 'S'
								AND D009_C004_Id = '{$g['empresaAtual']}' 
								GROUP BY D001_Id 
								LIMIT $limit,3000 ");
			
			$limit += 3000;
			if(mysql_num_rows($sql) <= 0){
				break;
			}

			if(mysql_error()){
				die('1 Erro SQL: '.mysql_error());
			}

			while($row = mysql_fetch_assoc($sql)){
				//Compara ST da CFOP e NCM
				$D006 = ($row['D149_Flag_ST'] == 'S') ? $D006comST['D006_Flag_Substituicao_Tributaria']: $D006semST['D006_Flag_Substituicao_Tributaria'];
				if ($D006 == 'S' && $row['D149_Flag_ST'] == 'S') {
					$Flag_ST = 'S';
				} else {
					$Flag_ST = '';
				}
				// echo $Flag_ST. "\n";
				$descricaoAbreviada = gLimpaAcentos(str_replace($caracteresRemovidos, '', strtoupper($row['D001_Descricao_Produto'])));
				$row['D005_Classificacao_Fiscal'] = substr($row['D005_Classificacao_Fiscal'], 0, 4) . '.' . substr($row['D005_Classificacao_Fiscal'], 4, 2) . '.' . substr($row['D005_Classificacao_Fiscal'], 6, 2);
				$tipoProduto = ($row['D009_Preco_Loja'] > 0) ? '' : 'V';
				$row['D009_Preco_Loja'] = ($row['D009_Preco_Loja'] <= 0) ? '0.01' : $row['D009_Preco_Loja'];
				$linha = array(
					prepararCampo('01', 'C'),
					//prepararCampo($g['empresaAtual'], 'N'),
					prepararCampo(1, 'N'),
					prepararCampo($row['D001_Id'], 'N'),
					prepararCampo($descricaoAbreviada, 'C', '26'),
					prepararCampo($row['D009_Preco_Loja'], 'N', '3'),
					prepararCampo($tipoProduto, 'C'),
					prepararCampo($rowD053['D053_Aliquota_ICMS_1'], 'N', '2'),
					prepararCampo($Flag_ST, 'C'),
					// prepararCampo($row['D149_Flag_ST'], 'C'),
					prepararCampo('', 'C'),
					prepararCampo($row['D009_Preco_Loja'], 'N', '3'),
					prepararCampo('3.50', 'N', '2'),
					prepararCampo($row['D037_Unidade'], 'C', '2'),
					prepararCampo($row['D009_Quantidade_Estoque_Loja'], 'N', '3'),
					prepararCampo($row['D001_Descricao_Produto'], 'C', '35'),
					prepararCampo($row['D009_Valor_Custo_Unitario'], 'N', '3'),
					prepararCampo($row['D009_Valor_Custo_Ultima_Compra'], 'N', '3'),
					prepararCampo('', 'N'),
					prepararCampo($row['D009_Valor_Custo_Medio_Unitario'], 'N', '3'),
					prepararCampo('', 'N'),
					prepararCampo('', 'C'),
					prepararCampo('', 'N'),
					prepararCampo($row['D005_Classificacao_Fiscal'], 'C', '10'),
					prepararCampo('', 'N'),
					prepararCampo('', 'N'),
					prepararCampo('', 'N'),
					prepararCampo($row['D149_Percentual_Trib_Transparencia'], 'N', '2'),
					prepararCampo($row['D049_Origem_Mercadoria'], 'N'),
					prepararCampo($row['D149_Codigo_CEST'], 'C'),
					prepararCampo('0', 'C'),
				);
				$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
			}
		
			mysql_data_seek($sql, 0);
			while($row = mysql_fetch_assoc($sql)){
				if(empty($row['D001_Codigo_Barras']) || $row['D001_Codigo_Barras'] == '000000') { continue; }
				if(isset($codigosBarraUtilizados[$row['D001_Codigo_Barras']])) { continue; }
				if(!preg_match('#^[0-9]+$#', $row['D001_Codigo_Barras'])) { continue; }
				$linha = array(
					prepararCampo('02', 'C'),
					prepararCampo($row['D001_Id'], 'N'),
					prepararCampo($row['D001_Codigo_Barras'], 'C', '14'),
					//prepararCampo($row['D001_Qte_Embalagem'], 'N', '2'),
					prepararCampo(0, 'N', '2'),
				);
				$conteudoProduto[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
				$codigosBarraUtilizados[$row['D001_Codigo_Barras']] = true;

				if(empty($row['D001_Codigo_Barra1']) || $row['D001_Codigo_Barra1'] == '000000') { continue; }
				if(isset($codigosBarraUtilizados[$row['D001_Codigo_Barra1']])) { continue; }
				if(!preg_match('#^[0-9]+$#', $row['D001_Codigo_Barra1'])) { continue; }
				$linha = array(
					prepararCampo('02', 'C'),
					prepararCampo($row['D001_Id'], 'N'),
					prepararCampo($row['D001_Codigo_Barra1'], 'C', '14'),
					//prepararCampo($row['D001_Qte_Embalagem'], 'N', '2'),
					prepararCampo(0, 'N', '2'),
				);
				$conteudoProduto[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
				$codigosBarraUtilizados[$row['D001_Codigo_Barra1']] = true;
			}

		}

		$sql=mysql_query("SELECT D001_Id,D083_Codigo_Barras,D001_Qte_Embalagem FROM D001 
							LEFT JOIN D049 ON D049_D001_Id=D001_Id 
							LEFT JOIN D009 ON D009_D049_Id=D049_Id 
							LEFT JOIN D083 ON D083_D049_Id=D049_Id
							WHERE D001_Flag_Pre_Cadastro = 'N' 
							AND D001_Flag_Ativo = 'S'
							AND D009_C004_Id = '{$g['empresaAtual']}' 
							AND IFNULL(D083_Codigo_Barras, '') != '' 
							GROUP BY D083_Id");
		if(mysql_error()){
			//die('Erro SQL: '.mysql_error());
		}
		/*
		$teste = mysql_fetch_array($sql);
		print_r($teste);
		die('alaor');
		*/
		while($row = mysql_fetch_assoc($sql)){
			
			/*if(empty($row['D083_Codigo_Barras']) || $row['D083_Codigo_Barras'] == '000000' || strlen($row['D083_Codigo_Barras']) < 9) {continue; }
			if(isset($codigosBarraUtilizados[$row['D083_Codigo_Barras']])) { continue; }
			if(!preg_match('#^[0-9]+$#', $row['D083_Codigo_Barras'])) { continue; }*/
			if(empty($row['D083_Codigo_Barras']) || $row['D083_Codigo_Barras'] == '000000' || strlen(trim($row['D083_Codigo_Barras'])) < 9) { continue; }
			if(isset($codigosBarraUtilizados[$row['D083_Codigo_Barras']])) { continue; }
			if(!preg_match('#^[0-9]+$#', $row['D083_Codigo_Barras'])) { continue; }
			
			$linha = array(
				prepararCampo('02', 'C'),
				prepararCampo($row['D001_Id'], 'N'),
				prepararCampo($row['D083_Codigo_Barras'], 'C', '14'),
				//prepararCampo($row['D001_Qte_Embalagem'], 'N', '2'),
				prepararCampo(0, 'N', '2'),
			);
			$conteudoProduto[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
			$codigosBarraUtilizados[$row['D083_Codigo_Barras']] = true;

		}
		
		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = 'produtos.txt';
		file_put_contents($dir . $arquivo, $conteudo);
		file_put_contents($dir . $arquivo, $conteudoProduto, FILE_APPEND);
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.$arquivo);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();
	break;

	case 'exportarClientes':
		/*
		------------------     clientes.txt     -------------------
		Nº Campo      Denominação do Campo            Tam  Dec Tipo Obrigatoriedade
		01 CODCLI10   CODIGO DO CLIENTE               006    0    N
		02 CODPDV210  CODIGO DO CLIENTE               013    0    C
		03 CGCCPF10   CNPJ/CPF DO CLIENTE             018    0    C
		04 RAZSOC10   RAZAO SOCIAL DO CLIENTE         060    0    C
		05 ENDCLI10   ENDERECO DO CLIENTE             050    0    C
		06 BAIRRO10   BAIRRO DO CLIENTE               040    0    C
		07 CEPCLI10   CEP DO CLIENTE                  008    0    N
		08 FONECLI10  FONE DO CLIENTE                 012    0    C
		09 INSCCLI10  INSCRICAO ESTADUAL DO CLIENTE   015    0    C
		10 DATACAD10  DATA CADASTRO                   008    0    D
		11 DATANASC10 DATA NASCIMENTO                 008    0    D
		12 ULTCOMP10  ULTIMA COMPRA                   008    0    D
		13 VENCCART10 VENCIMENTO CARTAO               008    0    D
		14 TIPOPAG10  TIPO PAGAMENTO                  002    0    N
		15 CODFIL10   CODIGO FILIAL                   002    0    N
		16 LIMCOMPR10 VALOR DO LIMITE DE COMPRA       012    2    N
		17 COBRAJUR10 COBRA JUROS DO CLIENTE(S/N)     001    0    C
		18 VALCREDI10 VALOR DO CREDITO DO CLIENTE     011    2    N
		19 DTRENOV10  DATA RENOVACAO                  008    0    D
		20 PRAZOPGT10 PRAZO PGTO                      002    0    N
		21 PERCJURO10 PERC JURO                       005    2    N
		22 CARENCJR10 CARENCIA JURO                   002    0    N
		23 SITUACAO10 SITUACAO                        001    0    N
		24 DIALIMIT10 DIA LIMITE                      002    0    N
		25 JURIDFIS10 JURIDICA/FISICA(F/J)            001    0    C
		26 CODIBGE    CODIGO DO MUNICIPIO NO IBGE     007    0    N
		*/
		$conteudo = array();
		$sqlC004 = mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
		$rowC004 = mysql_fetch_array($sqlC004);
		if (isset($r_hoje)) {
			$sql = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id WHERE D024_Flag_Pessoa_Fisica_Juridica != 'I' AND D024_Flag_Ativo != 'N' AND D024_Data_Cadastro = CURDATE()");
		} else {
			$sql = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id WHERE D024_Flag_Pessoa_Fisica_Juridica != 'I' AND D024_Flag_Ativo != 'N'");
		}
		while($row = mysql_fetch_array($sql)){
			$cpf_cnpj = ($row['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') ? $row['D024_Cnpj'] : $row['D024_Cpf'];
			$cpf_cnpj = str_replace(array('.','/','-', ' '), '', $cpf_cnpj);
			$cpf_cnpj = (trim($row['D024_Nome_Empresa']) == 'CONSUMIDOR FINAL') ? '' : $cpf_cnpj;
			$row['D024_Flag_Pessoa_Fisica_Juridica'] = !empty($row['D024_Flag_Pessoa_Fisica_Juridica']) ? $row['D024_Flag_Pessoa_Fisica_Juridica'] : 'J';
			$codigoIBGE = !empty($row['D020_Codigo_IBGE']) ? $row['D020_Codigo_IBGE'] : $rowC004['D020_Codigo_IBGE'];
			$row['D024_Cep'] = str_replace('.', '', substr($row['D024_Cep'], 0, 8));
			$row['D024_Nome_Empresa'] = str_replace(array('"', '\''), '', $row['D024_Nome_Empresa']);
			$linha = array(
				prepararCampo($row['D024_Id'], 'N'),
				prepararCampo($row['D024_Id'], 'C'),
				prepararCampo($cpf_cnpj, 'C'),
				prepararCampo($row['D024_Nome_Empresa'], 'C', '60'),
				prepararCampo($row['D024_Endereco'], 'C', '50'),
				prepararCampo($row['D024_Bairro'], 'C', '40'),
				prepararCampo($row['D024_Cep'], 'N'),
				prepararCampo($row['D024_DDD_Telefone_1'].$row['D024_Telefone_1'], 'C', '12'),
				prepararCampo($row['D024_Inscricao_Estadual'], 'C', '15'),
				prepararCampo($row['D024_Data_Cadastro'], 'D'),
				prepararCampo($row['D024_Data_Fundacao'], 'D'),
				prepararCampo($row['D024_Data_Ultima_Compra'], 'D'),
				prepararCampo('', 'D'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo('', 'C', '1'),
				prepararCampo('', 'N'),
				prepararCampo('', 'D'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo('', 'N'),
				prepararCampo($row['D024_Flag_Pessoa_Fisica_Juridica'], 'C', '1'),
				prepararCampo($codigoIBGE, 'N'),
			);
			$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
		}

		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = 'clientes.txt';
		file_put_contents($dir . $arquivo, $conteudo);
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.$arquivo);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();
	break;

	case 'exportarOperadores':
		/*
		-------------------------------------    OPERADORES.TXT    ---------------------------------------------------
		Nº Campo      Denominação do Campo                              Tipo Tam Dec Obrigatório Permite(0) Permite(-)
		01 CODIGO     CODIGO DO OPERADOR                                   N 003   0     Sim        Não        Não
		02 USUARIO    USUARIO DO OPERADOR                                  C 015   0 	 Não
		03 SENHA      SENHA DO OPERADOR                                    N 006   0     Não
		04 NIVEL      NIVEL DO OPERADOR (1 - 3)                            N 001   0     Não
		05 FILIAL     FILIAL DO OPERADOR                                   N 002   0     Não
		*/
		$conteudo = array();
		$sqlC007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome, C007_Senha, C007_Flag_Vendedor FROM C007 WHERE C007_Ativo = 'S'");
		while($row = mysql_fetch_array($sqlC007)){
			$senha = base64_decode($row['C007_Senha']);
			if (strlen($senha) > 6 || !preg_match('#^[0-9]+$#', $senha)) {
				$senha = $row['C007_Id'];
			}
			$nivel = 1;
			switch ($row['C007_Flag_Vendedor']) {
				case 'S':
					$nivel = 2;
					break;
				case 'D':
					$nivel = 3;
					break;
			}
			$linha = array(
				prepararCampo($row['C007_Id'], 'N'),
				prepararCampo($row['C007_Primeiro_Nome'], 'C', '15'),
				prepararCampo($senha, 'N'),
				prepararCampo($nivel, 'N'),
				//prepararCampo($g['empresaAtual'], 'N'),
				prepararCampo(1, 'N'),
			);
			$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
		}

		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = 'operadores.txt';
		file_put_contents($dir . $arquivo, $conteudo);
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.$arquivo);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();
	break;

	case 'exportarPedidos':
		/*
		------------------      pedidos.txt      ------------------
		Cabecario do pedido

		Nº Campo      Denominação do Campo                              Tam  Dec Tipo
		01 IDENTIF    CAMPO FIXO 01 CABECARIO DO PEDIDO                 002    0    C
		02 CODFIL     CODIGO DA FILIAL                                  002    0    N
		03 NUMPED     NUMERO DO PEDIDO                                  006    0    N
		04 CODCLI     CODIGO DO CLIENTE                                 006    0    N
		05 CODVEND    CODIGO DO VENDEDOR                                003    0    N
		06 DATAPED    DATA DO PEDIDO                                    008    0    D
		07 FATURADO   DATA DO FATURAMENTO DO PEDIDO                     008    0    D
		08 VALOR      VALOR TOTAL DO PEDIDO                             011    2    N
		09 DESCFATURA DESCONTO DE FATURAMENTO (DESCONTO GERAL)          011    2    N
		10 VLRCOMDESC VALOR DO PEDIDO COM OS DESCONTOS.                 011    2    N
		11 VENCIMENTO DATA DO VENCIMENTO DO PEDIDO                      008    0    D
		12 PRAZO      PRAZO PARA VENCIMENTO DO PEDIDO                   003    0    N
		13 CANCELADO  PEDIDO CANCELADO(S/N)                             001    0    C
		14 ACREDES    PERCENTUAL DE ACRESCIMO/DESCONTO                  006    2    N
		15 OBSERVACAO OBSERVACAO DO PEDIDO                              060    0    C
		16 CONSFINAL  CONSUMIDOR FINAL(S/N)                             001    0    C
		17 PEDEMITIDO O PEDIDO JA FOI EMITIDO(IMPRESSO)(S/N)            001    0    C
		18 STATUS2    [ ]NORMAL, [P]PENDENTE, [A]ALTERADO, [C]CANCELADO 003    0    C
		19 TIPOPED    [D]DAV, [P]PRÉ-VENDA                              001    0    C

		Produtos do pedido
		Nº Campo      Denominação do Campo                              Tam  Dec Tipo
		01 IDENTIF    CAMPO FIXO 02 PRODUTOS DO PEDIDO                  002    0    C
		02 CODFIL     CODIGO DA FILIAL                                  002    0    N
		03 NUMPED     NUMERO DO PEDIDO                                  006    0    N
		04 CODPRO     CODIGO DO PRODUTO                                 006    0    N
		05 ORDEM      ORDEM DO PRODUTO NO PEDIDO                        003    0    N
		06 COMPOORDEM ORDEM DO PRODUTO PRINCIPAL NA COMPOSICAO          003    0    N
		07 CODBARRAS  CODIGO DE BARRAS DO PRODUTO                       014    0    N
		08 DESCPRO    DESCRICAO DO PRODUTO                              035    0    C
		09 QTDEPED    QUANTIDADE PEDIDA                                 010    3    N
		10 QTDEEMB    QUANTIDADE POR EMBALAGEM                          003    0    N
		11 PRECO      PRECO DE VENDA                                    012    3    N
		12 UNIDADE    UNIDADE. EX: CX,KG...                             002    0    C
		13 ACRESCIMO  PERCENTUAL DE ACRESCIMO                           006    2    N
		14 DESCONTO1  1º PERCENTUAL DE DESCONTOS                        006    2    N
		15 DESCONTO2  2º PERCENTUAL DE DESCONTOS                        006    2    N
		16 PERCCOM    PERCENTUAL DE COMISSAO DO PRODUTO                 005    2    N
		17 ALIQFRENTE ALIQUOTAS DE ICMS                                 005    0    C
		              EX: 07.00, 12.00, 27.00 .....
		              PARA ISENTO: IS.00, SUBSTITUICAO: ST.00,
		              NAO TRIBUTADO: NT.00
		18 IPIVENDA   IPI DE VENDA DO PRODUTO                           002    0    N
		19 STATUS2    STATUS DO PRODUTO [C]CANCELADO [ ]NORMAL          001    0    C
		20 PRECONORM  PRECO DE VENDA NORMAL                             012    3    N
		21 CUSSICM    CUSTO SEM ICMS(1)                                 011    3    N
		22 CUSREAL    CUSTO REAL(2)                                     011    3    N
		23 CUSTENC    CUSTO COM ENCARGOS(3)                             011    3    N
		24 CUSMEDIO   CUSTO MEDIO(4)                                    011    3    N
		25 CUSTFIS    CUSTO MEDIO FISCAL(5)                             011    3    N
		26 VLIPIVEN   VALOR UNITARIO DO IPI                             008    2    N
		*/
		$sqlD053 = mysql_query("SELECT D053_Aliquota_ICMS_1 FROM D053 WHERE D053_D018_Id = '{$g['C004']['C004_D018_Id']}' AND D053_C004_Id = '{$g['empresaAtual']}'");
		$rowD053 = mysql_fetch_array($sqlD053);

		$conteudo = array();
		//$sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_C004_Id = '{$g['empresaAtual']}' AND T005_Flag_ACP = 4 AND T005_Flag_Expedicao = 2 AND T005_Data_Emissao = CURDATE() AND T005_Flag_Status > 0 AND T005_Flag_Pdv_Importado = 'N'");
		$sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_C004_Id = '{$g['empresaAtual']}' AND T005_Flag_ACP = 4 AND T005_Flag_Expedicao = 2 AND T005_Flag_Status > 0 AND T005_Flag_Status != 5 AND T005_Flag_Status != 8");
		while($rowT005 = mysql_fetch_array($sqlT005)){
			$linha = array(
				prepararCampo('01', 'C'),
				//prepararCampo($g['empresaAtual'], 'N'),
				prepararCampo(1, 'N'),
				prepararCampo($rowT005['T005_Id'], 'N'),
				prepararCampo($rowT005['T005_D024_Id'], 'N'),
				prepararCampo('', 'N'),
				prepararCampo($rowT005['T005_Data_Emissao'], 'D'),
				prepararCampo('', 'D'),
				prepararCampo($rowT005['T005_Valor_Total'], 'N', 2),
				prepararCampo('', 'N', 2),
				prepararCampo($rowT005['T005_Valor_Total'], 'N', 2),
				prepararCampo('', 'D'),
				prepararCampo('', 'N'),
				prepararCampo('N', 'C'),
				prepararCampo('', 'N'),
				prepararCampo($rowT005['T005_Observacao'], 'C', 60),
				prepararCampo('S', 'C'),
				prepararCampo('S', 'C'),
				prepararCampo(' ', 'C'),
				prepararCampo('D', 'C'),
			);
			$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);

			$sql = <<<EOT
				SELECT 
					T006.*, D001_Id, D001_Codigo_Barras, D037_Unidade, D006_Flag_Substituicao_Tributaria, D006_Flag_Isento_ICMS, D149_Flag_ST
				FROM
					T006
				LEFT JOIN D009 ON D009_Id=T006_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D049_D001_Id=D001_Id
				LEFT JOIN D037 ON D037_Id = D001_D037_Id
				LEFT JOIN D006 ON D006_Id=T006_D006_Id
				LEFT JOIN D005 ON D005_Id=D001_D005_Id
				LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id=D009_C004_Id
				WHERE
					T006_T005_Id = '{$rowT005['T005_Id']}'
EOT;
			$sqlT006 = mysql_query($sql);
			while($rowT006 = mysql_fetch_array($sqlT006)){
				$linha = array(
					prepararCampo('02', 'C'),
					//prepararCampo($g['empresaAtual'], 'N'),
					prepararCampo(1, 'N'),
					prepararCampo($rowT005['T005_Id'], 'N'),
					prepararCampo($rowT006['D001_Id'], 'N'),
					prepararCampo($rowT006['T006_Item'], 'N'),
					prepararCampo($rowT006['T006_Item'], 'N'),
					prepararCampo($rowT006['D001_Codigo_Barras'], 'N'),
					prepararCampo($rowT006['T006_Descricao_Produto'], 'C', 35),
					prepararCampo($rowT006['T006_Quantidade'], 'N', 3),
					prepararCampo('', 'N'),
					prepararCampo($rowT006['T006_Valor_Preco_Sem_Desconto_Unitario'], 'N', 3),
					prepararCampo($rowT006['D037_Unidade'], 'C', 2),
					prepararCampo('', 'N'),
					prepararCampo('', 'N'),
					prepararCampo('', 'N'),
					prepararCampo('', 'N'),
				);
				// ----------------------------------------------------------------------------------------
				// REGRA ANTERIOR, ENVIANDO ST PARA PRODUTOS COM ST
				/*
				if ($rowT006['D149_Flag_ST'] == 'S') {
					$linha[] = prepararCampo('ST.00', 'C');
				} else {
					$linha[] = prepararCampo($rowD053['D053_Aliquota_ICMS_1'], 'C');
				}
				*/
                // NOVA REGRA, NÃO ENVIAR NENHUM ITEM COM ST A PEDIDO DO CASSIANO NO CHAMADO 39183
				$linha[] = prepararCampo($rowD053['D053_Aliquota_ICMS_1'], 'C');
				//-----------------------------------------------------------------------------------------

				$linha[] = prepararCampo('', 'N', 0); //prepararCampo($rowT006['T006_Aliquota_IPI'], 'N', 0),
				$linha[] = prepararCampo(' ', 'C');
				$linha[] = prepararCampo($rowT006['T006_Valor_Preco_Sem_Desconto_Unitario'], 'N', 3);
				$linha[] = prepararCampo('', 'N'); //$linha[] = prepararCampo($rowT006['T006_Valor_Custo_Unitario'] - $rowT006['T006_Valor_ICMS'], 'N', 3);
				$linha[] = prepararCampo($rowT006['T006_Valor_Custo_Unitario'], 'N', 3);
				$linha[] = prepararCampo('', 'N');
				$linha[] = prepararCampo('', 'N');
				$linha[] = prepararCampo('', 'N');
				$linha[] = prepararCampo('', 'N');
				$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
			}
			//
			//mysql_query("UPDATE T005 SET T005_Flag_Pdv_Importado = 'S' WHERE T005_Id = '{$rowT005['T005_Id']}'");
		}

		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = 'pedidos.txt';
		file_put_contents($dir . $arquivo, $conteudo);
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.$arquivo);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();
	break;

	case 'importarCupomFiscal':
//file_put_contents('/tmp/TESTECUPOM', '\n\n\n\n\n chegou aqui');
//die("stick");
		require_once('bibliotecas/classes/VEN003.php');
		$VEN003 = new VEN003();
		require_once("bibliotecas/classes/CAD002.php");
	       $CAD002 = new CAD002();



		/*$r_filename = 'C:\\sgloja\\exportacao\\22022018\\012102201800023409.txt';
		$r_content = <<<EOT
|01|1|10300570|C|1|1|ECF|35166|20180221|20190715|17:47|74.40|0.00|ECF|A|N|0|35166|||59|
|02|N|3|1.000|15.900000|0.00000|15.900000|1.000|1|27|99.99|PC|7899605514592|S
|02|N|5|1.000|15.900000|0.00000|15.900000|1.000|2|27|99.99|PC|7899605514592|S
|02|N|5899888|1.000|15.900000|0.00000|15.900000|1.000|3|27|99.99|PC|7899605514592|S
|02|N|1|1.000|8.900000|0.00000|8.900000|1.000|4|27|99.99|PC|7898617511650|S
|02|N|102|1.000|8.900000|0.00000|8.900000|1.000|5|27|99.99|PC|7898617511650|S
|02|N|14611|1.000|8.900000|0.00000|8.900000|1.000|6|27|99.99|PC|7898617511650|S
|03|R|11|1|74.40
EOT;*/




		//C:\sgloja\exportacao\21082014\012108201400000045.txt
		$diretorioArquivo = explode("\\", $r_filename);
		$dataCupom = $diretorioArquivo[3];
		//Copia o arquivo para a pasta temp do usuario
//print_r($diretorioArquivo);
//die();
		$dir = $g['pathDados']."/PDV/".$dataCupom;
		is_dir($dir) or mkdir($dir, 0777, true);
		$dir = $dir."/".$diretorioArquivo[4];

		file_put_contents($dir, $r_content);
		file_put_contents($dir.'_log', "Gravou conteudo do arquivo" . "\n\n", FILE_APPEND);
		$logImportacao .= "Gravou conteudo do arquivo"."<br>";

		if(strpos($diretorioArquivo[4], 'sansup') !== false || substr($diretorioArquivo[4], 0, 1) == 'r'){
			echo "true1";
		//	file_put_contents('/tmp/poiqwe', 'chegou aqui');
			die();
		}

		$arrayDados = explode("\n", $r_content);
		foreach ($arrayDados as $key => $linha) {
			$arrayDados[$key] = explode("|", trim($linha, '|'));
		}
		file_put_contents('/tmp/poiqwe', serialize($arrayDados));
		file_put_contents('/tmp/alaor', serialize($arrayDados));

		mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        file_put_contents($dir.'_log', "Transaction iniciada" . "\n\n", FILE_APPEND);
        $logImportacao .= "Transaction iniciada"."<br>";
	
		/************** CABEÇALHO DO CUPOM FISCAL
		--------------------     regsai       ---------------------
		Nº Campo      Denominação do Campo             Tam Dec Tipo
		01 01         TIPO DO REGISTRO                 002 000    N
		02 CODFIL83   FILIAL                           002 000    N
		03 LANSAI83   CHAVE DO SITEMA                  009 000    N
		04 CLIFOR83   CLIENTE OU FORNECEDOR            001 000    C
		05 FORNCLI83  CODIGO CLIENTE OU FORNECEDOR     006 000    N
		06 NUMPDV83   NUMERO DO PDV                    003 000    N
		07 SERNOTA83  SERIE DA NOTA/CUPOM              003 000    C
		08 NUMNOTA83  NUMERO DA NOTA/CUPOM             008 000    N
		09 DATEMIS83  DATA EMISSAO                     008 000    D
		10 DATASAI83  DATA SAIDA                       008 000    D
		11 HORARIO83  HORA EMISSAO                     005 000    C
		12 VALCON83   VALOR TOTAL COM DESCONTO         013 002    N
		13 TOTDESC83  VALOR DESCONTO SUBTOTAL          013 002    N
		14 USUARIO83  USUARIO                          010 000    C
		15 AVISPRAZ83 (A)VISTA (P)RAZO                 001 000    C
		16 SITNOTA83  SITUACAO (N)ORMAL (C)ANCELADA    001 000    C
		17 PEDIDO     NUMERO DO PEDIDO                 006 000    N
		18 COO        COO                              006 000    N
		19 CPFCLIENTE CPF DO CLIENTE                   014 000    C
		20 TIPOPED    TIPO DO PEDIDO (D)AV (P)RE-VENDA 001 000    C
		21 OPERADOR83 OPERADOR                         003 000    N
		***********************************************************/

		$cab = $arrayDados[0];
		$flagCancelada = ($cab[15] == 'C') ? 'S' : 'N';
		$cab[8] = corrigeData($cab[8]);
		$dataEmissaoCupom = $cab[8];
		$cab[9] = corrigeData($cab[9]);
		$D024_Id = $cab[4];
		//$C004_Id = $cab[1];
		//$C004_Id = '2'; //Fixo empresa 2 pois no programa PDV só existe uma filial
		$C004_Id = $g['empresaAtual'];
		if ($C004_Id==2) {
			$flagACP = 4;
		} else {
			$flagACP = 8;
		}
		$C007_Nome = $cab[13];
		$C007=mysql_query("SELECT C007_Id FROM C007 WHERE C007_Nome LIKE '%{$C007_Nome}%';");
		$mC007 = mysql_fetch_array($C007);
		$C007_Id = $mC007['C007_Id'];
		$numeroNotaFiscal = $cab[7];
		$T005_Id = ($cab[19] == 'D' && !empty($cab[16])) ? $cab[16] : 0;
		$sqlC020 = mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$C004_Id}'");
		$rowC020 = mysql_fetch_array($sqlC020);
		$D006_Id = $rowC020['C020_Cupom_Fiscal_CFOP'];

		//Verifica se o cupom foi importado anteriormente e agora foi cancelado
		if($flagCancelada == 'S'){
			file_put_contents($dir.'_log', "Cupom importado está cancelado" . "\n", FILE_APPEND);
			$logImportacao .= "Cupom importado está cancelado"."<br>";
			$sqlT007 = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Numero_Nota_Fiscal = '{$numeroNotaFiscal}' AND T007_C004_Id = '{$C004_Id}' AND T007_Flag_ACP = '{$flagACP}' AND T007_Data_Emissao = '{$cab[8]}' AND T007_Flag_Cancelada != 'S'");
			if(mysql_num_rows($sqlT007) > 0){
				file_put_contents($dir.'_log', "Gravando cancelamento do cupom" . "\n", FILE_APPEND);
				$logImportacao .= "Gravando cancelamento do cupom"."<br>";
				$rowT007 = mysql_fetch_array($sqlT007);
				mysql_query("DELETE FROM T002 WHERE T002_T007_Id = '{$rowT007['T007_Id']}'");
				mysql_query("DELETE FROM T001 WHERE T001_T007_Id = '{$rowT007['T007_Id']}'");
				mysql_query("DELETE FROM T019 WHERE T019_T007_Id = '{$rowT007['T007_Id']}'");
				mysql_query("UPDATE T007 SET T007_Flag_Cancelada = 'S' WHERE T007_Id = '{$rowT007['T007_Id']}'");
				mysql_query("UPDATE T006 SET T006_Flag_Pdv_Importado='N' WHERE T006_T005_Id='{$T005_Id}'"); 
				mysql_query("COMMIT;");
				//die("Cupom {$numeroNotaFiscal} cancelado.");
				mysql_query("SET AUTOCOMMIT=1");
				registrarLog(array("T007", $rowT007['T007_Id'], $C004_Id, "CF {$numeroNotaFiscal}", "Cancelamento de cupom fiscal importado", $logImportacao, "N", "1"));
				echo "true2";
				die();
			}
		} 
		
		$sqlT007 = mysql_query("SELECT T007_Id FROM T007 WHERE T007_Numero_Nota_Fiscal = '{$numeroNotaFiscal}' AND T007_C004_Id = '{$C004_Id}' AND T007_Flag_ACP = '{$flagACP}' AND T007_Data_Emissao = '{$cab[8]}'");
		if(mysql_num_rows($sqlT007) > 0){
			file_put_contents($dir.'_log', "Cupom importado já existe" . "\n", FILE_APPEND);
			$logImportacao .= "Cupom importado já existe"."<br>";
			mysql_query("SET AUTOCOMMIT=1");
			registrarLog(array("T007", "", $C004_Id, "CF {$numeroNotaFiscal}", "Cupom já importado anteriormente", $logImportacao, "N", "1"));
			echo "true3";
			die();
		}
		


		$r_filename = mysql_real_escape_string($r_filename);
		$r_content = mysql_real_escape_string($r_content);
		$valorProdutosSemDesconto = $cab[11] + $cab[12];
		$sql = <<<EOT
			INSERT INTO T007 (
				T007_C004_Id,
				T007_D024_Id,
				T007_D006_Id,
				T007_Flag_ACP,
				T007_Flag_Entrada_Saida,
				T007_Numero_Nota_Fiscal,
				T007_Data_Emissao,
				T007_Data_Saida,
				T007_Hora_Saida,
				T007_Valor_Total,
				T007_Valor_Total_Produtos,
				T007_Valor_Total_Produtos_Sem_Desconto,
				T007_Valor_Desconto,
				T007_C007_Id_Vendedor_Interno,
				T007_Flag_Cancelada,
				T007_Nome_Arquivo_PDV,
				T007_Conteudo_Arquivo_PDV,
				T007_T005_Id,
				T007_Casas_Decimais_Preco,
				T007_Flag_Calcular
			) VALUES (
				'{$C004_Id}',
				'{$D024_Id}',
				'{$D006_Id}',
				'{$flagACP}',
				'S',
				'{$numeroNotaFiscal}',
				'{$cab[8]}',
				'{$cab[9]}',
				'{$cab[10]}',
				'{$cab[11]}',
				'{$cab[11]}',
				'{$valorProdutosSemDesconto}',
				'{$cab[12]}',
				'{$C007_Id}',
				'{$flagCancelada}',
				'{$r_filename}',
				'{$r_content}',
				'{$T005_Id}',
				'5',
				'N'
			);
EOT;
		mysql_query($sql);
		file_put_contents($dir.'_log', "Gravou o cabeçalho do cupom" . "\n", FILE_APPEND);
		$logImportacao .= "Gravou o cabeçalho do cupom"."<br>";
		$erro = mysql_error();
        if (!empty($erro)) {
        	file_put_contents($dir.'_log', "Erro ao gravar cabeçalho do cupom. Erro: " . $erro . "\n", FILE_APPEND);
        	$logImportacao .= "Erro ao gravar cabeçalho do cupom. Erro: " . $erro."<br>";
            mysql_query("ROLLBACK");
			die("Erro: " . $erro);
		}
		
		$T007_Id = $g['mysqlLastId'];
		/***************** ITENS DO CUPOM FISCAL
		---------------------     movpro      ---------------------
		Nº Campo      Denominação do Campo             Tam Dec Tipo
		01 02         TIPO DO REGISTRO                 002 000    N
		02 N/C        SITUACAO PRODUTO (N)ORMAL (C)ANC.001 000    C
		03 CODPRO05   CODIGO PRODUTO                   006 000    N
		04 QTDEMOV05  QUANTIDADE PRODUTO               011 003    N
		05 PRECO05    VALOR DO PRODUTO                 013 006    N
		06 DESCNOTA05 DESCONTO NO PROTUDO              009 005    N
		07 PRECONOT05 VALOR DO PRODUTO COM DESCONTO    009 005    N
		08 QTDEEMB05  QUANTIDADE EMBALAGEM             009 003    N
		09 ORDPROD05  ORDEM PRODUTO                    003 000    N
		10 CODVEND05  VENDEDOR                         003 000    N
		11 ALIQICM05  ALIQUOTA ICMS                    005 002    N
		12 UNIDADE05  UNIDADE PRODUTO                  002 000    C
		13 CODBARRAS  CODIGO DE BARRAS DO PRODUTO      014 000    N
		**********************************************************/

		file_put_contents($dir.'_log', "Iniciando importação dos itens do cupom" . "\n\n", FILE_APPEND);
		$logImportacao .= "Iniciando importação dos itens do cupom"."<br>";
		foreach ($arrayDados as $key => $linha) {
			if($linha[0] != '02') { continue; }
			if($flagCancelada == 'N' && $linha[1] == 'C') { continue; }
			file_put_contents($dir.'_log', "Importando item do cupom" . "\n", FILE_APPEND);
			$logImportacao .= "Importando item do cupom"."<br>";
			// Se tiver pedido, encontra o item no pedido
			$T006_Id = 0;
			if ($T005_Id > 0) {
				file_put_contents($dir.'_log', "Item do cupom está em um pedido" . "\n", FILE_APPEND);
				$logImportacao .= "Item do cupom está em um pedido"."<br>";
				$sql = <<<EOT
					SELECT
						T006_Id, T006_Valor_Preco_Original
					FROM T006
					LEFT JOIN D009 ON D009_Id=T006_D009_Id
					LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D049_D001_Id=D001_Id
					LEFT JOIN D037 ON D037_Id = D001_D037_Id
					LEFT JOIN D006 ON D006_Id=T006_D006_Id
					WHERE
						T006_T005_Id = '{$T005_Id}'
						AND D001_Id = '{$linha[2]}'
						AND T006_Quantidade = '{$linha[3]}'
						AND T006_Flag_Pdv_Importado = 'N'
EOT;
				file_put_contents('/tmp/pdv', $sql . "\n\n", FILE_APPEND);
				$resT006 = mysql_query($sql);
				if (mysql_num_rows($resT006) > 0) {
					$rowT006 = mysql_fetch_assoc($resT006);
					$T006_Id = $rowT006['T006_Id'];
					//Caso o preço tabela do produto no pedido esteja preenchido, utilizamos ele para popular o campo T008_Valor_Preco_Original
					//Esta alteração é feita para que a comissao seja calculada corretamente
					$linha[4] = ($rowT006['T006_Valor_Preco_Original'] > 0) ? $rowT006['T006_Valor_Preco_Original'] : $linha[4];
					mysql_query("UPDATE T006 SET T006_Flag_Pdv_Importado = 'S' WHERE T006_Id = '{$T006_Id}'");
				} else {
					// Se não tiver T006, é um item acrescentado da LOJA
				}
			}

			$sqlD037 = mysql_query("SELECT D037_Id FROM D037 WHERE D037_Unidade = '{$linha[11]}'");
			$rowD037 = mysql_fetch_array($sqlD037);
			$D037_Id = $rowD037['D037_Id'];
			file_put_contents($dir.'_log', "Iniciando busca do produto do cupom" . "\n", FILE_APPEND);
			$logImportacao .= "Iniciando busca do produto do cupom"."<br>";
			$sqlD009 = mysql_query("SELECT D009_Id, D001_Descricao_Produto, D001_Codigo_Produto, D001_D037_Id, D001_D005_Id, D009_Valor_Custo_Unitario, if(D009_Preco_Loja>0,D009_Preco_Loja,D009_Preco_Loja_Real) D009_Preco_Loja FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE (D001_Id = '{$linha[2]}' OR D001_Id_Antigo = '{$linha[2]}') AND D009_C004_Id = '{$C004_Id}'");
			//die("SELECT D009_Id, D001_Descricao_Produto, D001_Codigo_Produto, D001_D037_Id, D001_D005_Id, D009_Valor_Custo_Unitario, if(D009_Preco_Loja>0,D009_Preco_Loja,D009_Preco_Loja_Real) D009_Preco_Loja FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$linha[2]}' AND D009_C004_Id = '{$C004_Id}'");
			$rowD009 = mysql_fetch_array($sqlD009);
			if(mysql_num_rows($sqlD009) == 1){
				file_put_contents($dir.'_log', "Produto do cupom encontrado" . "\n", FILE_APPEND);
				$logImportacao .= "Produto do cupom encontrado"."<br>";
				if(empty($D037_Id)){
					$D037_Id = $rowD009['D001_D037_Id'];
				}
			} else {
				file_put_contents($dir.'_log', "Produto do cupom não encontrado" . "\n", FILE_APPEND);
				$logImportacao .= "Produto do cupom não encontrado: ".$linha[2]."<br>";
				$rowD009['D001_Descricao_Produto'] = "Produto não encontrado. Código antigo: {$linha[2]}";
				registrarLog(array("T007", "", $C004_Id, "CF {$numeroNotaFiscal}", "Produto não encontrado. codigo nao encontrado: ".$linha[2], $logImportacao, "N", "1"));
//				mysql_query("ROLLBACK");
//		            	die("Erro: Produto não encontrado. codigo nao encontrado: ".$linha[2]);
				
			}
			echo $logImportacao."<br/>";
			$linha[10] = ($linha[10] == 99.99) ? 0 : $linha[10];
			$desc = (100-$linha[5]);
			$C007_Id = $linha[9];
			$rowD009['D001_Descricao_Produto'] = mysql_real_escape_string($rowD009['D001_Descricao_Produto']);
			$rowD009['D001_Codigo_Produto'] = mysql_real_escape_string($rowD009['D001_Codigo_Produto']);
			//$percentualDesconto = (($linha[6]-$linha[8])/$linha[6])*100;
			$sql = <<<EOT
				INSERT INTO T008 (
					T008_T007_Id,
					T008_D009_Id,
					T008_D006_Id,
					T008_D005_Id,
					T008_Codigo_Produto,
					T008_Descricao_Produto,
					T008_Quantidade,
					T008_Valor_Preco_Sem_Desconto_Unitario,
					T008_Valor_Desconto_Unitario,
					T008_Valor_Preco_Unitario,
					T008_Aliquota_ICMS,
					T008_D037_Id,
					T008_T006_Id,
					T008_Valor_Custo_Unitario,
					T008_Valor_Preco_Original,
					T008_Percentual_Desconto,
					T008_Flag_Calculo_Manual
				) VALUES (
					'{$T007_Id}',
					'{$rowD009['D009_Id']}',
					'{$D006_Id}',
					'{$rowD009['D001_D005_Id']}',
					'{$rowD009['D001_Codigo_Produto']}',
					'{$rowD009['D001_Descricao_Produto']}',
					'{$linha[3]}',
					'{$linha[6]}',
					'{$desc}',
					'{$linha[6]}',
					'{$linha[10]}',
					'{$D037_Id}',
					'{$T006_Id}',
					'{$rowD009['D009_Valor_Custo_Unitario']}',
					'{$linha[4]}',
					'{$linha[5]}',
					'S'
				);
EOT;
			
			mysql_query($sql);
			file_put_contents($dir.'_log', "Gravou um item do cupom" . "\n", FILE_APPEND);
			$logImportacao .= "Gravou um item do cupom"."<br>";
			$erro = mysql_error();
			
	        if (!empty($erro)) {
	        	file_put_contents($dir.'_log', "Erro ao gravar um item do cupom. Erro: " . $erro . "\n", FILE_APPEND);
	        	$logImportacao .= "Erro ao gravar um item do cupom. Erro: " . $erro."<br>";
				mysql_query("ROLLBACK");
				die("Erro: " . $erro);
	        }
	        $T008_Id = $g['mysqlLastId'];
	        mysql_query("call T008_Gravar_Totalizacao_4('{$T008_Id}');");

			//Verifica a quantidade de locações para esse D009
			$T066 = mysql_query("SELECT T066_Id, 
										D004_Id 
									  FROM T066 
								 LEFT JOIN D004 ON D004_Id=T066_D004_Id 
									 WHERE T066_D009_Id = '{$rowD009['D009_Id']}'
									   AND IFNULL(T066_D004_Id, 0) > 0"); 
			//Se tiver apenas uma locação, usa essa locação. Senão usa a que possui o D004_Local=LOJA'
			if(mysql_num_rows($T066) == 1){
				$rowT066 = mysql_fetch_array($T066);
			}else{
				$sqlT066 = mysql_query("SELECT T066_Id, 
				                               D004_Id 
		                                  FROM T066 
	                                 LEFT JOIN D004 ON D004_Id=T066_D004_Id 
		                                 WHERE T066_D009_Id = '{$rowD009['D009_Id']}' 
			                               AND D004_Local = 'LOJA'");
				$rowT066 = mysql_fetch_array($sqlT066);
			}
	        if (!$T006_Id && $flagCancelada == 'N') {
	        	file_put_contents($dir.'_log', "Item do cupom não está vinculado a um pedido, o histórico do produto será inserido" . "\n", FILE_APPEND);
	        	$logImportacao .= "Item do cupom não está vinculado a um pedido, o histórico do produto será inserido"."<br>";
			if($rowD009['D009_Id'] > 0){
				$sql = <<<EOT
					INSERT INTO T001 (
						T001_D024_Id_Cliente, 
						T001_T008_Id, 
						T001_T007_Id, 
						T001_D009_Id, 
						T001_C004_Id, 
						T001_T066_Id, 
						T001_Codigo_Produto, 
						T001_Data_Lancamento, 
						T001_Descricao_Operacao, 
						T001_Quantidade, 
						T001_Flag_Operacao, 
						T001_Valor_Preco_Unitario, 
						T001_Valor_Custo_Unitario,
						T001_Flag_MMF, 
						T001_Flag_Atualiza_Custo,  
						T001_D004_Id 
					) VALUES (
						'{$D024_Id}',
						'{$T008_Id}',
						'{$T007_Id}',
						'{$rowD009['D009_Id']}',
						'{$C004_Id}',
						'{$rowT066['T066_Id']}',
						'{$rowD009['D001_Codigo_Produto']}',
						'{$dataEmissaoCupom}',
						'CF-{$cab[7]}',
						'{$linha[3]}',
						'S',
						'{$linha[6]}',
						'',
						'S',
						'N',
						'{$rowT066['D004_Id']}'
					)
EOT;
				mysql_query($sql);
				file_put_contents($dir.'_log', "Gravou o histórico do item do cupom" . "\n\n", FILE_APPEND);
				$logImportacao .= "Gravou o histórico do item do cupom"."<br>";
				$erro = mysql_error();
				
				if (!empty($erro)) {
					file_put_contents($dir.'_log', "Erro ao gravar histórico do item do cupom. Erro: " . $erro . "\n", FILE_APPEND);
					$logImportacao .= "Erro ao gravar histórico do item do cupom. Erro: " . $erro."<br>";
					mysql_query("ROLLBACK");
					die("Erro: " . $erro);
				}				
			}
				
		    }
		}
		mysql_query("CALL T007_Gravar_Totalizacao_4('{$T007_Id}');");
		

		/**********************************************************************************************
		* REALIZA CALCULOS DO T007/T008 GRAVAR TOTALIZACAO SEM MODIFICAR O VALOR DOS PRODUTOS DO CUPOM
		***********************************************************************************************/

		$sqlDadosT007 = mysql_query("SELECT IFNULL((T007_Valor_Frete+T007_Valor_Despesas_Acessorias+T007_Valor_Seguro) / sum(T008_Valor_Total_Preco),0),
             						   IFNULL(T007_Valor_Despesas_Acessorias / sum(T008_Valor_Total_Preco),0),
             						   IFNULL(T007_Valor_Seguro / sum(T008_Valor_Total_Preco),0),
             						   IFNULL(T007_Valor_Frete / sum(T008_Valor_Total_Preco),0)
    							  FROM T007 LEFT JOIN T008 ON T008_T007_Id=T007_Id WHERE T007_Id = '{$T007_Id}' GROUP BY T007_Id");
		$rowDadosT007 = mysql_fetch_array($sqlDadosT007);
    	$xMultiplicador_Despesa 			= $rowDadosT007[0];
        $xMultiplicador_Despesas_Acessorias = $rowDadosT007[1];
        $xMultiplicador_Seguro 				= $rowDadosT007[2];
        $xMultiplicador_Frete 				= $rowDadosT007[3];

    	$sqlT008 = mysql_query("SELECT T008_Id FROM T008 WHERE T008_T007_Id = '{$T007_Id}'");
    	while ($rowT008 = mysql_fetch_array($sqlT008)) {

    		$sqlDadosT008 = mysql_query("SELECT D006_Flag_Somar_IPI_Base_ICMS, 
    											D006_Flag_Somar_Despesas_Base_IPI, 
    											D006_Flag_Forma_Calculo_COFINS, 
    											D006_Flag_Forma_Calculo_PIS, 
    											IFNULL(T134_Reducao_Base_ICMS, 0) T134_Reducao_Base_ICMS, 
    											IFNULL(T134_Id,0) T134_Id, 
    											D006_Flag_Isento_ICMS
										   FROM T008
								      LEFT JOIN T007 ON T007_Id=T008_T007_Id
 								      LEFT JOIN D006 ON D006_Id=T008_D006_Id
 								      LEFT JOIN T134 ON T134_Id=T007_T134_Id
      									  WHERE T008_Id = '{$rowT008['T008_Id']}'
      								   GROUP BY T008_Id");

    		$rowDadosT008 = mysql_fetch_array($sqlDadosT008);
            
            $xD006_Flag_Somar_IPI_Base_ICMS 	= $rowDadosT008['D006_Flag_Somar_IPI_Base_ICMS'];
            $xD006_Flag_Somar_Despesas_Base_IPI = $rowDadosT008['D006_Flag_Somar_Despesas_Base_IPI'];
            $xD006_Flag_Forma_Calculo_COFINS 	= $rowDadosT008['D006_Flag_Forma_Calculo_COFINS'];
            $xD006_Flag_Forma_Calculo_PIS 		= $rowDadosT008['D006_Flag_Forma_Calculo_PIS'];
            $xT134_Reducao_Base_ICMS 			= $rowDadosT008['T134_Reducao_Base_ICMS'];
            $xT134_Id 							= $rowDadosT008['T134_Id'];
            $xD006_Flag_Isento_ICMS 			= $rowDadosT008['D006_Flag_Isento_ICMS'];
	        
	        mysql_query("UPDATE T008 SET T008_Percentual_Desconto=if(T008_Valor_Preco_Original>0,(1-(T008_Valor_Preco_Sem_Desconto_Unitario/T008_Valor_Preco_Original))*100,0),
	        					T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade,
                                T008_Valor_Total_Despesas=T008_Valor_Total_Preco*$xMultiplicador_Despesa,
                                T008_Valor_Total_Despesas_Acessorias=if((T008_Valor_Total_Preco*$xMultiplicador_Despesas_Acessorias)<0.01 and $xMultiplicador_Despesas_Acessorias>0,0.01,T008_Valor_Total_Preco*$xMultiplicador_Despesas_Acessorias),
                                T008_Valor_Total_Frete=if($xMultiplicador_Frete>0 and (T008_Valor_Total_Preco*$xMultiplicador_Frete)<=0.01,0.01,T008_Valor_Total_Preco*$xMultiplicador_Frete),
                                T008_Valor_Total_Seguro=T008_Valor_Total_Preco*$xMultiplicador_Seguro,
                                T008_Valor_Base_IPI=If(T008_Aliquota_IPI>0,(T008_Valor_Total_Preco+T008_Valor_Total_Imposto_Importacao+if('$xD006_Flag_Somar_Despesas_Base_IPI'='S',T008_Valor_Total_Frete+T008_Valor_Total_Seguro+T008_Valor_Total_Despesas_Acessorias,0))*If(T008_Percentual_Reducao_IPI>0,(T008_Percentual_Reducao_IPI/100),1),0),
                                T008_Valor_IPI=round(T008_Valor_Base_IPI*(T008_Aliquota_IPI/100),2),
                                T008_Valor_Despesas_Unitario=T008_Valor_Total_Despesas/T008_Quantidade,
                                T008_Valor_Despesas_Acessorias_Unitario=T008_Valor_Total_Despesas_Acessorias/T008_Quantidade,
                                T008_Valor_Frete_Unitario=T008_Valor_Total_Frete/T008_Quantidade,
                                T008_Valor_Seguro_Unitario=T008_Valor_Total_Seguro/T008_Quantidade,
                                T008_Peso_Total=T008_Quantidade*T008_Peso_Unitario,
                                T008_Peso_Bruto_Total=T008_Quantidade*T008_Peso_Bruto,
                                T008_Valor_Total_Custo_Final=(T008_Valor_Total_Preco+T008_Valor_Total_Despesas+if('$xD006_Flag_Somar_IPI_Base_ICMS'='S',T008_Valor_IPI,0)),
                                T008_Valor_Custo_Final_Unitario=T008_Valor_Total_Custo_Final/T008_Quantidade,
                                T008_Valor_Base_ICMS_Sem_Reducao=If(T008_Aliquota_ICMS>0,T008_Valor_Total_Custo_Final+if('$xD006_Flag_Somar_IPI_Base_ICMS'='S',T008_Valor_IPI,0),0),
                                T008_Valor_Base_ICMS=If(T008_Aliquota_ICMS>0,T008_Valor_Base_ICMS_Sem_Reducao*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1),0),
                                T008_Valor_Base_ICMS_Diferido=T008_Valor_Base_ICMS_Sem_Reducao-T008_Valor_Base_ICMS,
                                T008_Valor_Base_ICMS_Desonerado=IF(T008_Aliquota_ICMS_Oculto>0 AND '$xD006_Flag_Isento_ICMS' = 'D',T008_Valor_Total_Custo_Final+if('$xD006_Flag_Somar_IPI_Base_ICMS'='S',T008_Valor_IPI,0)-IF(T008_Percentual_Reducao_ICMS>0,T008_Valor_Base_ICMS,0),0),
                                T008_Valor_ICMS=T008_Valor_Base_ICMS*(T008_Aliquota_ICMS/100),
                                T008_Valor_ICMS_Desonerado=T008_Valor_Base_ICMS_Desonerado*(T008_Aliquota_ICMS_Oculto/100),
                                T008_Valor_Lucro=round((T008_Valor_Total_Custo*T008_IPV)-(T008_Valor_Total_Custo*T008_IPV_Sem_Lucro),2),
                                T008_Valor_Base_ICMS_Substituicao=IF(T008_Flag_ST='S',(T008_Valor_Total_Preco+T008_Valor_Total_Despesas+T008_Valor_IPI)*((T008_ST_VA/100)+1),0),
                                T008_Valor_ICMS_Substituicao=(T008_Valor_Base_ICMS_Substituicao*(T008_ST_ICMS_Interno/100)),
                                T008_Valor_ICMS_Substituicao_Retencao=((T008_Valor_Base_ICMS_Substituicao*if(T008_Percentual_Reducao_ICMS_ST>0,((100-T008_Percentual_Reducao_ICMS_ST)/100),1))*(T008_ST_ICMS_Interno/100))-((T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1))*T008_ICMS_Inter_Estadual/100),
                                T008_Valor_ICMS_Substituicao_Retencao=IF(T008_Valor_ICMS_Substituicao_Retencao<0,0,T008_Valor_ICMS_Substituicao_Retencao),
                                T008_Valor_Base_ICMS_Interno=(((T008_Valor_Total_Preco+T008_Valor_Total_Despesas+T008_Valor_IPI)*((T008_ST_VA/100)+1))*if(T008_Percentual_Reducao_ICMS_ST>0,((100-T008_Percentual_Reducao_ICMS_ST)/100),1)),
                                T008_Valor_ICMS_Interno=(T008_Valor_Base_ICMS_Interno*(T008_ST_ICMS_Interno/100)),
                                T008_Valor_Base_ICMS_Inter_Estadual=(T008_Valor_Total_Custo_Final*if(T008_Percentual_Reducao_ICMS>0,((100-T008_Percentual_Reducao_ICMS)/100),1)),
                                T008_Valor_ICMS_Inter_Estadual=((T008_Valor_Base_ICMS_Inter_Estadual*T008_ICMS_Inter_Estadual)/100),
                                T008_Valor_Base_ICMS_Impresso=if(substring(T008_Flag_Situacao_Tributaria,2,2)='51' or substring(T008_Flag_Situacao_Tributaria,2,2)='90',T008_Valor_Base_ICMS_Sem_Reducao,T008_Valor_Base_ICMS),
                                T008_Valor_Base_PIS=if(T008_Flag_Isento_PIS='S',0,if('$xD006_Flag_Forma_Calculo_PIS'='L',if(T008_Valor_Total_Custo < T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),
                                T008_Aliquota_PIS=if(T008_Flag_Isento_PIS='S',0,T008_Aliquota_PIS),
                                T008_Valor_PIS=if(T008_Aliquota_PIS > 0,T008_Valor_Base_PIS*(T008_Aliquota_PIS/100),0),
                                T008_Valor_Base_COFINS=if(T008_Flag_Isento_COFINS='S',0,if('$xD006_Flag_Forma_Calculo_COFINS'='L',if(T008_Valor_Total_Custo < T008_Valor_Total_Preco,T008_Valor_Total_Preco-T008_Valor_Total_Custo,0),T008_Valor_Total_Preco)),
                                T008_Aliquota_COFINS=if(T008_Flag_Isento_COFINS='S',0,T008_Aliquota_COFINS),
                                T008_Valor_COFINS=if(T008_Aliquota_COFINS > 0,T008_Valor_Base_COFINS*(T008_Aliquota_COFINS/100),0),
                                T008_Valor_ICMS_Credito=if('$xT134_Id'<=0 or '$xT134_Id'='' or ifnull('$xT134_Id',0)<=0,T008_Valor_ICMS_Credito,If(T008_Aliquota_ICMS_Credito>0,(T008_Valor_Total_Custo_Final+if('$xD006_Flag_Somar_IPI_Base_ICMS'='S',T008_Valor_IPI,0))-((T008_Valor_Total_Custo_Final+if('$xD006_Flag_Somar_IPI_Base_ICMS'='S',T008_Valor_IPI,0))*if($xT134_Reducao_Base_ICMS>0,($xT134_Reducao_Base_ICMS/100),1)),0)*(T008_Aliquota_ICMS_Credito/100)),
                                T008_Aliquota_IRPJ=if(T008_Flag_Isento_IRPJ='S',0,T008_Aliquota_IRPJ),
                                T008_Aliquota_CSLL=if(T008_Flag_Isento_CSLL='S',0,T008_Aliquota_CSLL),
                                T008_Valor_Base_IRPJ=T008_Valor_Total_Preco,
                                T008_Valor_IRPJ=if(T008_Aliquota_IRPJ>0,T008_Valor_Base_IRPJ*(T008_Aliquota_IRPJ/100),0),
                                T008_Valor_Base_CSLL=T008_Valor_Total_Preco,
                                T008_Valor_CSLL=if(T008_Aliquota_CSLL>0,T008_Valor_Base_CSLL*(T008_Aliquota_CSLL/100),0),
                                T008_Total_Cubagem = IFNULL(T008_Cubagem_Unitaria,0) * IFNULL(T008_Quantidade,0),
                                T008_Valor_Trib_Transparencia=IFNULL(T008_Valor_Total_Preco_Sem_Desconto,0)*(IFNULL(T008_Percentual_Trib_Transparencia,0)/100),
                                T008_IPV=(T008_Valor_Preco_Unitario-
                                        IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))/T008_Valor_Custo_Unitario,
                                T008_Percentual_Margem=round((((T008_Valor_Preco_Unitario-
                                        IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0))-T008_Valor_Custo_Unitario)/
                                      (T008_Valor_Preco_Unitario-
                                        IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T008_Valor_ICMS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T008_Valor_PIS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T008_Valor_COFINS/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T008_Valor_IRPJ/T008_Quantidade,0), 0)-
                                        IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T008_Valor_CSLL/T008_Quantidade,0), 0)))*100,2)
            			  WHERE T008_Id = '{$rowT008['T008_Id']}'");
		}

		$sqlDadosT007 = mysql_query("SELECT IFNULL(SUM(T008_Valor_Total_Custo),0),
								            IFNULL(SUM(round(T008_Valor_Base_ICMS,2)),0),
 								            IFNULL(SUM(T008_Valor_Base_ICMS_Sem_Reducao),0),
 								            IFNULL(SUM(T008_Valor_Base_ICMS_Diferido),0),
 								            IFNULL(SUM(round(T008_Valor_ICMS,2)),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Desonerado),0),
 								            IFNULL(SUM(T008_Valor_Base_IPI),0),
 								            IFNULL(SUM(T008_Valor_IPI),0),
 								            IFNULL(SUM(round(T008_Valor_Base_ICMS_Impresso,2)),0),
 								            IFNULL(SUM(T008_Valor_Base_ICMS_Substituicao),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Substituicao),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Substituicao_Retencao),0),
 								            IFNULL(SUM(T008_Peso_Total),0),
 								            IFNULL(SUM(T008_Peso_Bruto_Total),0),
 								            IFNULL(SUM(T008_Valor_Total_Imposto_Importacao),0),
 								            IFNULL(SUM(T008_Valor_COFINS),0),
 								            IFNULL(SUM(T008_Valor_PIS),0),
 								            IFNULL(SUM(T008_Total_Cubagem),0),
 								            IFNULL(SUM(T008_Valor_Base_ICMS_Interno),0),
 								            IFNULL(SUM(T008_Valor_Base_ICMS_Inter_Estadual),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Interno),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Inter_Estadual),0),
 								            IFNULL(SUM(T008_Valor_Trib_Transparencia),0),
 								            IFNULL(SUM(T008_Valor_IRPJ),0),
 								            IFNULL(SUM(T008_Valor_Base_IRPJ),0),
 								            IFNULL(SUM(T008_Valor_CSLL),0),
 								            IFNULL(SUM(T008_Valor_Base_CSLL),0),
 								            IFNULL(SUM(T008_Valor_ICMS_Credito),0),
 								            T008_Aliquota_ICMS_Credito
 								       FROM T008
 								      WHERE T008_T007_Id= '{$T007_Id}';");

		$rowDadosT007 = mysql_fetch_array($sqlDadosT007);
		$xValor_Total_Custo					= $rowDadosT007[0];
 		$xValor_Base_ICMS					= $rowDadosT007[1];
 		$xValor_Base_ICMS_Sem_Reducao		= $rowDadosT007[2];
 		$xValor_Base_ICMS_Diferido			= $rowDadosT007[3];
 		$xValor_ICMS						= $rowDadosT007[4];
 		$xValor_ICMS_Desonerado				= $rowDadosT007[5];
 		$xValor_Base_IPI					= $rowDadosT007[6];
 		$xValor_IPI							= $rowDadosT007[7];
 		$xValor_Base_ICMS_Impresso			= $rowDadosT007[8];
 		$xValor_Base_ICMS_Substituicao		= $rowDadosT007[9];
 		$xValor_ICMS_Substituicao			= $rowDadosT007[10];
 		$xValor_ICMS_Substituicao_Retencao	= $rowDadosT007[11];
 		$xPeso_Total						= $rowDadosT007[12];
 		$xPeso_Total_Bruto					= $rowDadosT007[13];
 		$xValor_Total_II					= $rowDadosT007[14];
 		$xValor_COFINS						= $rowDadosT007[15];
 		$xValor_PIS							= $rowDadosT007[16];
 		$xValor_Total_Cubagem				= $rowDadosT007[17];
 		$xValor_Base_ICMS_Interno			= $rowDadosT007[18];
 		$xValor_Base_ICMS_Inter_Estadual	= $rowDadosT007[19];
 		$xValor_ICMS_Interno				= $rowDadosT007[20];
 		$xValor_ICMS_Inter_Estadual			= $rowDadosT007[21];
 		$xValor_Trib_Transparencia			= $rowDadosT007[22];
 		$xValor_IRPJ						= $rowDadosT007[23];
 		$xValor_Base_IRPJ					= $rowDadosT007[24];
 		$xValor_CSLL						= $rowDadosT007[25];
 		$xValor_Base_CSLL					= $rowDadosT007[26];
 		$xValor_ICMS_Credito				= $rowDadosT007[27];
 		$xAliquota_ICMS_Credito				= $rowDadosT007[28];

		mysql_query("UPDATE T007 SET T007_Valor_Total_Custo={$xValor_Total_Custo},
				                     T007_Valor_Total_Base_ICMS_Sem_Reducao={$xValor_Base_ICMS_Sem_Reducao},
				                     T007_Valor_Total_Base_ICMS={$xValor_Base_ICMS},
				                     T007_Valor_Total_Base_ICMS_Diferido={$xValor_Base_ICMS_Diferido},
				                     T007_Valor_Total_Base_ICMS_Impresso={$xValor_Base_ICMS_Impresso},
				                     T007_Valor_Total_ICMS={$xValor_ICMS},
				                     T007_Valor_Total_ICMS_Desonerado=IFNULL({$xValor_ICMS_Desonerado},0),
				                     T007_Valor_Total_Base_IPI={$xValor_Base_IPI},
				                     T007_Valor_Total_IPI={$xValor_IPI},
				                     T007_Valor_Total_Imposto_Importacao={$xValor_Total_II},
				                     T007_Valor_ISS=if(T007_Aliquota_ISS>0,T007_Valor_Mao_Obra*(T007_Aliquota_ISS/100),0),
				                     T007_Total_Peso={$xPeso_Total},
				                     T007_Total_Peso_Bruto={$xPeso_Total_Bruto},
				                     T007_IPV_sem_icms=T007_IPV_Medio(T007_Id,2),
				                     T007_Valor_Base_ICMS_Substituicao={$xValor_Base_ICMS_Substituicao},
				                     T007_Valor_ICMS_Substituicao={$xValor_ICMS_Substituicao},
				                     T007_Valor_ICMS_Substituicao_Retencao=ifnull({$xValor_ICMS_Substituicao_Retencao},0.00),
				                     T007_Valor_Base_ICMS_Interno=ifnull({$xValor_Base_ICMS_Interno},0),
				                     T007_Valor_Base_ICMS_Inter_Estadual=ifnull({$xValor_Base_ICMS_Inter_Estadual},0),
				                     T007_Valor_ICMS_Interno=ifnull({$xValor_ICMS_Interno},0),
				                     T007_Valor_ICMS_Inter_Estadual=ifnull({$xValor_ICMS_Inter_Estadual},0),
				                     T007_Valor_Total_ICMS_Credito={$xValor_ICMS_Credito},
				                     T007_Aliquota_ICMS_Credito={$xAliquota_ICMS_Credito},
				                     T007_Valor_Total_COFINS={$xValor_COFINS},
				                     T007_Valor_Total_PIS={$xValor_PIS},
				                     T007_Valor_Total_Base_IRPJ={$xValor_Base_IRPJ},
				                     T007_Valor_Total_IRPJ={$xValor_IRPJ},
				                     T007_Valor_Total_Base_CSLL={$xValor_Base_CSLL},
				                     T007_Valor_Total_CSLL={$xValor_CSLL},
				                     T007_Valor_Total_Trib_Transparencia={$xValor_Trib_Transparencia},
				                     T007_Total_Cubagem=IFNULL({$xValor_Total_Cubagem},0),
				                     T007_C007_Id_Vendedor_Interno={$C007_Id},
				                     T007_IPV=if(T007_Valor_Total_Custo>0,(T007_Valor_Total_Produtos-
				                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
				                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS,0), 0)-
				                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS,0), 0)-
				                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ,0), 0)-
				                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL,0), 0))/T007_Valor_Total_Custo,0),
				                     T007_Percentual_Margem=round((((T007_Valor_Total_Produtos-
				                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
				                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS,0), 0)-
				                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS,0), 0)-
				                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ,0), 0)-
				                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL,0), 0))-T007_Valor_Total_Custo)/
				                                    (T007_Valor_Total_Produtos-
				                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_ICMS,0), 0)-
				                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(T007_Valor_Total_PIS,0), 0)-
				                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(T007_Valor_Total_COFINS,0), 0)-
				                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_IRPJ,0), 0)-
				                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(T007_Valor_Total_CSLL,0), 0)))*100,2)
				               WHERE T007_Id='{$T007_Id}'");

		$mLetras[1]="A";
		$mLetras[2]="B";
		$mLetras[3]="C";
		$mLetras[4]="D";
		$mLetras[5]="E";
		$mLetras[6]="F";
		$mLetras[7]="G";
		$mLetras[8]="H";
		$mLetras[9]="I";
		$mLetras[10]="J";
		$mLetras[11]="K";
		$mLetras[12]="L";
		$mLetras[13]="M";
		$mLetras[14]="N";
		$mLetras[15]="O";
		$mLetras[16]="P";
		$mLetras[17]="Q";
		$mLetras[18]="R";
		$mLetras[19]="S";
		$mLetras[20]="T";
		$mLetras[21]="U";
		$mLetras[22]="V";
		$mLetras[23]="W";
		$mLetras[24]="X";
		$mLetras[25]="Y";
		$mLetras[26]="Z";
		$mLetras[27]="AA";
		$mLetras[28]="AB";
		$mLetras[29]="AC";
		$mLetras[30]="AD";
		$mLetras[31]="AE";
		$mLetras[32]="AF";
		$mLetras[33]="AG";
		$mLetras[34]="AH";
		$mLetras[35]="AI";
		$mLetras[36]="AJ";
		$mLetras[37]="AK";
		$mLetras[38]="AL";
		$mLetras[39]="AM";
		$mLetras[40]="AN";
		$mLetras[41]="AO";
		$mLetras[42]="AP";
		$mLetras[43]="AQ";
		$mLetras[44]="AR";
		$mLetras[45]="AS";
		$mLetras[46]="AT";
		$mLetras[47]="AU";
		$mLetras[48]="AV";

		
		/************* CONTAS A RECEBER
		|03|R|3|1|200.00
		|03|R|1|1|-55.23
		---------------------     movfin      ----------------------
		Nº Campo      Denominação do Campo             Tam Dec Tipo
		01 03         TIPO DO REGISTRO                 002 000    N
		02 REC_PAG    (R)ECEBIMENTO (P)AGAMENTO        001 000    C
		03 CODTIPPAG  CODIGO DA FINALIZADORA           002 000    N
		04 PARCELA    PARCELA                          003 000    N
		05 VALOR      VALOR                            011 002    N
		***********************************************************/
		if ($flagCancelada == 'N') {
			file_put_contents($dir.'_log', "Iniciando gravação dos dados de pagamento" . "\n", FILE_APPEND);
			$logImportacao .= "Iniciando gravação dos dados de pagamento"."<br>";
			// Mapeamento Finalizadoras SG -> HARDNESS
			$mapFin = array(
				2 => 15, // Dinheiro
				4 => 21, // Cheque
				10 => 16, // Troca/Vale
				8 => 8, // Cartão Offline
				9 => 6, // Cartao Crédito
			);
			$sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}'");
			$rowD024 = mysql_fetch_array($sqlD024);
			$sqlD014 = mysql_query("SELECT * FROM D014 WHERE D014_Flag_Emite_Cupom = 'S'"); 
			$rowD014 = mysql_fetch_array($sqlD014);
			$sqlD007 = mysql_query("SELECT * FROM D007 WHERE D007_Flag_Emite_Cupom = 'S'");
			$rowD007 = mysql_fetch_array($sqlD007);

			$countParcelas = 0;
			file_put_contents($dir.'_log', "Iniciando gravação dos títulos a receber" . "\n", FILE_APPEND);
			$logImportacao .= "Iniciando gravação dos títulos a receber"."<br>";
			foreach ($arrayDados as $key => $linha) {
				if($linha[0] != '03') { continue; }
				$linha[4] = floatval($linha[4]);
				$sqlD027 = mysql_query("SELECT * FROM D027 WHERE D027_Id_Portador_PDV = '{$linha[2]}'");
				if(mysql_num_rows($sqlD027) <= 0){
					mysql_query("ROLLBACK");
					mysql_query("SET AUTOCOMMIT=1");
						file_put_contents($dir.'_log', "Portador do PDV não vinculado aos portadores a receber do sistema" . "\n", FILE_APPEND);
						file_put_contents($dir.'_log', "SELECT * FROM D027 WHERE D027_Id_Portador_PDV = '{$linha[2]}'" . "\n", FILE_APPEND);
						$logImportacao .= "Portador do PDV não vinculado aos portadores a receber do sistema (D027_Id_Portador_PDV = '{$linha[2]}')"."<br>";
						registrarLog(array("T007", "", $C004_Id, "CF {$numeroNotaFiscal}", "Erro ao importar cupom fiscal <b>{$numeroNotaFiscal}</b>, portador do PDV não vinculado aos portadores a receber do sistema.", $logImportacao, "S", "1"));
						echo "portador não encontrado";
						die();
					}
					$rowD027 = mysql_fetch_array($sqlD027);
					$D027_Id = $rowD027['D027_Id'];
					$numeroDocumento = 'CF-'.$numeroNotaFiscal;
					$numeroDuplicata = 'CF-'.$numeroNotaFiscal.$mLetras[$linha[3]];
					$sqlT002 = <<<EOT
						INSERT INTO T002 (
							T002_D024_Id,
							T002_D027_Id,
							T002_T007_Id,
							T002_D014_Id,
					T002_D032_Id,
							T002_C004_Id,
							T002_D062_Id,
							T002_D007_Id,
							T002_Nome_Cliente,
							T002_Numero_Duplicata,
							T002_Numero_Documento,
							T002_Data_Emissao,
							T002_Data_Vencimento,
							T002_Data_Vencimento_Original,
							T002_Data_Recebimento,
							T002_Valor_Duplicata,
							T002_Valor_Juros,
							T002_Valor_Desconto,
							T002_Flag_Enviar_Banco_Protestar,
							T002_Valor_Total,
							T002_Data_Lancamento,
							T002_Hora_Lancamento,
							T002_Numero_Parcela,
					T002_Valor_Recebido_Total
						) values (
							'{$D024_Id}',
							'{$D027_Id}',
							'{$T007_Id}',
							'{$rowD014['D014_Id']}',
					'{$rowD014['D014_D032_Id']}',
							'{$C004_Id}',
							'{$rowD007['D007_D062_Id']}',
							'{$rowD007['D007_Id']}',
							'{$rowD024['D024_Nome_Empresa']}',
							'{$numeroDuplicata}',
							'{$numeroDocumento}',
							'{$dataEmissaoCupom}',
							'{$dataEmissaoCupom}',
							'{$dataEmissaoCupom}',
							'0000-00-00',
							'{$linha[4]}',
							'0',
							'0',
							'{$rowD024['D024_Flag_Enviar_Banco_Protestar']}',
							'{$linha[4]}',
							current_date(),
							current_time(),
							'{$linha[3]}',
							'{$linha[4]}'
						)
EOT;
					if($linha[4] > 0 && $rowD027['D027_Operacao_Importar_PDV'] == '1'){
						mysqli_query($sqlT002);
						file_put_contents($dir.'_log', "Gravou o título a receber" . "\n", FILE_APPEND);
						$logImportacao .= "Gravou o título a receber"."<br>";
						// Ultimo dinheiro
						$T002_Id = mysql_insert_id();
						$ultimaT002_Id = mysql_insert_id();
						
						//Portador de baixa automatica e banco encontrado
						if ($rowD027['D027_Flag_Baixa_Automatica'] == 'S' && $rowD007['D007_Id'] > 0) {
							file_put_contents($dir.'_log', "Iniciando gravação do lançamento bancário" . "\n", FILE_APPEND);
							$logImportacao .= "Iniciando gravação do lançamento bancário"."<br>";
						    $sql = <<<EOT
							INSERT INTO T019 (
									T019_T007_Id,
									T019_D007_Id,
									T019_D027_Id,
									T019_C004_Id,
									T019_D024_Id,
									T019_D014_Id,
									T019_Complemento,
									T019_Data_Lancamento,
									T019_Referente,
									T019_Valor_Lancamento,
									T019_T002_Id
								) VALUES (
									'{$T007_Id}',
									'{$rowD007['D007_Id']}',
									'{$D027_Id}',
									'{$C004_Id}',
									'{$D024_Id}',
									'{$rowD014['D014_Id']}',
									'{$rowD024['D024_Nome_Empresa']}',
									'{$dataEmissaoCupom}',
									'{$numeroDuplicata}',
									'{$linha[4]}',
									'{$T002_Id}'
								)
EOT;
							mysql_query($sql);
							file_put_contents($dir.'_log', "Gravou o lançamento bancário" . "\n", FILE_APPEND);
							$logImportacao .= "Gravou o lançamento bancário"."<br>";
							$erro = mysql_error();
							if (!empty($erro)) {
								file_put_contents($dir.'_log', "Erro ao gravar lançamento bancário. Erro: " . $erro . "\n", FILE_APPEND);
								$logImportacao .= "Erro ao gravar lançamento bancário. Erro: " . $erro."<br>";
							    mysql_query("ROLLBACK");
							    die("Erro: " . $erro);
							}
							if ($linha[2] == 2) {
								$ultimaT019_Id = $g['mysqlLastId'];
							}
								mysql_query("CALL T002_Gravar_Totalizacao('{$T002_Id}')");
						}
					} else {
						if($ultimaT002_Id){
							//Troco
							if($linha[4] < 0){
								//Caso a parcela a seguir seja o valor do troco, remover a diferença do valor da duplicata
								$linha[4] = $linha[4] * -1;
								mysql_query("UPDATE T002 SET T002_Valor_Duplicata = T002_Valor_Duplicata - {$linha[4]}, T002_Valor_Total = T002_Valor_Total - {$linha[4]}, T002_Valor_Troco = {$linha[4]} WHERE T002_Id = '{$ultimaT002_Id}'");
								file_put_contents($dir.'_log', "Ajustando o troco no título a receber" . "\n", FILE_APPEND);
								$logImportacao .= "Ajustando o troco no título a receber"."<br>";
								if($ultimaT019_Id){
									mysql_query("UPDATE T019 SET T019_Valor_Lancamento = T019_Valor_Lancamento - {$linha[4]} WHERE T019_Id = '{$ultimaT019_Id}'");
									file_put_contents($dir.'_log', "Ajustando o troco no lançamento bancário" . "\n", FILE_APPEND);
									$logImportacao .= "Ajustando o troco no lançamento bancário"."<br>";
								}
							//Desconto
							} else if($rowD027['D027_Operacao_Importar_PDV'] == '2') {
								mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Data_Lancamento, T020_Historico, T020_Valor, T020_Flag_Deb_Cred) VALUES ('{$ultimaT002_Id}', '{$g['empresaAtual']}', CURDATE(), 'Desconto {$rowD027['D027_Portador']}', '{$linha[4]}', 'D')");
								file_put_contents($dir.'_log', "Adicionando o desconto de vale/troca" . "\n", FILE_APPEND);
								$logImportacao .= "Adicionando o desconto de vale/troca"."<br>";
								
								mysql_query("UPDATE T002 SET T002_Valor_Duplicata = T002_Valor_Duplicata + {$linha[4]}, T002_Valor_Total = T002_Valor_Total + {$linha[4]} WHERE T002_Id = '{$ultimaT002_Id}'");
								file_put_contents($dir.'_log', "Ajustando o vale/troca no título a receber" . "\n", FILE_APPEND);
								$logImportacao .= "Ajustando o vale/troca no título a receber"."<br>";
								if($ultimaT019_Id){
									mysql_query("UPDATE T019 SET T019_Valor_Lancamento = T019_Valor_Lancamento + {$linha[4]} WHERE T019_Id = '{$ultimaT019_Id}'");
									file_put_contents($dir.'_log', "Ajustando o vale/troca no lançamento bancário" . "\n", FILE_APPEND);
									$logImportacao .= "Ajustando o vale/troca no lançamento bancário"."<br>";
								}
							}
						} else if($rowD027['D027_Operacao_Importar_PDV'] == '2'){
							$sqlT002 = <<<EOT
								INSERT INTO T002 (
									T002_D024_Id,
									T002_D027_Id,
									T002_T007_Id,
									T002_D014_Id,
							T002_D032_Id,
									T002_C004_Id,
									T002_D062_Id,
									T002_D007_Id,
									T002_Nome_Cliente,
									T002_Numero_Duplicata,
									T002_Numero_Documento,
									T002_Data_Emissao,
									T002_Data_Vencimento,
									T002_Data_Vencimento_Original,
									T002_Data_Recebimento,
									T002_Valor_Duplicata,
									T002_Valor_Juros,
									T002_Valor_Desconto,
									T002_Flag_Enviar_Banco_Protestar,
									T002_Valor_Total,
									T002_Data_Lancamento,
									T002_Hora_Lancamento,
									T002_Numero_Parcela,
							T002_Valor_Recebido_Total
								) values (
									'{$D024_Id}',
									'{$D027_Id}',
								'{$T007_Id}',
								'{$rowD014['D014_Id']}',
				               	'{$rowD014['D014_D032_Id']}',
								'{$C004_Id}',
								'{$rowD007['D007_D062_Id']}',
								'{$rowD007['D007_Id']}',
								'{$rowD024['D024_Nome_Empresa']}',
								'{$numeroDuplicata}',
								'{$numeroDocumento}',
								'{$dataEmissaoCupom}',
								'{$dataEmissaoCupom}',
								'{$dataEmissaoCupom}',
								'0000-00-00',
								'{$linha[4]}',
								'0',
								'0',
								'{$rowD024['D024_Flag_Enviar_Banco_Protestar']}',
								'{$linha[4]}',
								current_date(),
								current_time(),
								'{$linha[3]}',
								'{$linha[4]}'
							)
EOT;
						mysqli_query($sqlT002);
					}
				}
				$countParcelas++;
			}
		}
		
		//if($countParcelas == 0){
		//	mysql_query("UPDATE T007 SET T007_Flag_Cancelada = 'S' WHERE T007_Id = '{$T007_Id}'");
		//	mysql_query("DELETE FROM T001 WHERE T001_T007_Id = '{$T007_Id}'");
		//}
		//$VEN003->ven003atualizarCFOP($T007_Id);
		file_put_contents($dir.'_log', "\nFinalizando a gravação de todos os dados" . "\n", FILE_APPEND);
		$logImportacao .= "\nFinalizando a gravação de todos os dados"."<br>";
		mysql_query("COMMIT;");
		mysql_query("SET AUTOCOMMIT=1");
		file_put_contents($dir.'_log', "Transaction finalizada" . "\n", FILE_APPEND);
		$logImportacao .= "Transaction finalizada"."<br>";
		$sqlT001 = mysql_query("SELECT T001_D009_Id FROM T001 WHERE T001_T007_Id = '{$T007_Id}'");
		while($rowT001 = mysql_fetch_array($sqlT001)){
			$CAD002->D001_reprocessa_historico(0, $rowT001['T001_D009_Id']);
		}
		
		registrarLog(array("T007", $T007_Id, $C004_Id, "CF {$numeroNotaFiscal}", "Importação de cupom realizada com sucesso", $logImportacao, "N", "1"));
		mysqli_query("UPDATE T245 SET T245_Flag_Erro = 'R' WHERE T245_Tabela = 'T007' AND T245_Referencia = 'CF {$numeroNotaFiscal}'",$g['conexaoBandoAuditoria']);
		echo "true";
	break;

	case 'exportarVendedores':
		/*
			---------------------------------- vendedores.txt -------------------------------------------------------------------

			Nº Campo      Denominação do Campo                    Tam  Dec Tipo   Obrigatoriedade

			01 CODIGO     CODIGO DO VENDEDOR                      003    0    N			O
			02 NOME       NOME DO VENDEDOR                        030    0    C			O
			03 PERCCOMIS  PERCENTUAL DE COMISSAO A VISTA          006    2    N
			04 PPERCCOMIS PERCENTUAL DE COMISSAO A PRAZO          006    2    N
			05 FUNCAO     ' '->VENDEDOR, S->SUPERVISOR            001    0    C
			06 CODSUPERV  CODIGO DO SUPERVISOR                    003    0    N
			07 SENHA      SENHA DO VENDEDOR/SUPERVISOR            006    0    C
			08 CODFIL     COD. DA FILIAL DO VENDEDOR              002    0    N
			09 OBS        OBSERVACAO SOBRE O VENDEDOR             050    0    C
			10 CBTAXASERV ATENDENTE COBRA TAXA DE SERVICO (S/Û)   001    0    C
			11 PERDESCON  PERC. DE DESCONTO MAX DO VENDEDOR       006    2    N
		*/

		$conteudo = array();
		$sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Ativo = 'S'");
		while($row = mysql_fetch_array($sqlC007)){
			$senha = base64_decode($row['C007_Senha']);
			if (strlen($senha) > 6 || !preg_match('#^[0-9]+$#', $senha)) {
				$senha = $row['C007_Id'];
			}
			$nivel = '';
			switch ($row['C007_Flag_Vendedor']) {
				case 'V':
					$nivel = ' ';
					break;
				case 'S':
					$nivel = 'S';
					break;
			}
			$percentualDescontoMaximo = ($nivel==' ') ? $g['C031']['alcadaVendaSupervisor_descontoMinimoSupervisor'] : 0.00;

			$linha = array(
				prepararCampo($row['C007_Id'], 'N'), 														// CODIGO VENDEDOR
				prepararCampo($row['C007_Primeiro_Nome'], 'C', '30'),										// NOME DO VENDEDOR
				prepararCampo($row['C007_Percentual_Comissao'], 'N', '2'),									// PERC COMISSÃO A VISTA
				prepararCampo($row['C007_Percentual_Comissao'], 'N', '2'),									// PERC COMISSÃO A PRAZO
				prepararCampo($nivel, 'N'),																	// FUNÇÃO 
				prepararCampo(0, 'N'),																		// COD SUPERVISOR
				prepararCampo($senha, 'C'),																	// SENHA
				prepararCampo(0, 'N'),																		// COD DA FILIAL
				prepararCampo(0, 'C'),																		// OBSERVAÇÃO
				prepararCampo(0, 'C'),																		// ATENDENTE COBRA TAXA DE SERVICO (S/Û) 
				prepararCampo($percentualDescontoMaximo, 'N', '2'),											// PERCENTUAL MÁXIMO DE DESCONTO VENDEDOR
			);
			$conteudo[] = '|'.implode("|", $linha).'|'.CHR(13).CHR(10);
		}

		$dir = $g['pathDados'] . '/tmp/';
		$arquivo = 'vendedores.txt';
		file_put_contents($dir . $arquivo, $conteudo);
		if (strpos($arquivo, '..') !== false) { die(); }
		header('Content-type: application/x-msdownload');
		header('Content-Disposition: attachment; filename='.$arquivo);
		header('Pragma: no-cache');
		header('Expires: 0');
		echo file_get_contents($dir . $arquivo);
		die();

			
	break;
}

function corrigeData($data){
	return substr($data,0,4).'-'.substr($data,4,2).'-'.substr($data,6,2);
}

function prepararCampo($valor, $tipo, $tamanho = false){
	//Data
	$valor = trim($valor);
	$valor = str_replace(array('"','\''), '', $valor);
	$valor = str_replace(array("\n", "\t"),  " ", $valor);
	if($valor == '' || ($tipo == 'D' && ($valor == '0000-00-00' || $valor == '0000-00-00 00:00:00'))){
		return '';
	}
	if($tipo == 'D'){
		$valor = explode('-', substr($valor, 0, 10));	
		$dia = @$valor[2];
		$mes = @$valor[1];
		$ano = $valor[0];
		return $dia . $mes . $ano;
	} else if($tipo == 'C'){
		$valor = str_replace('|', '', $valor);
		if($tamanho){
			return substr($valor, 0, $tamanho);
		} else {
			return $valor;
		}
	} else if($tipo == 'N'){
		if($tamanho){
			return number_format($valor, $tamanho, '.', '');
		}
		return preg_replace('/[^0-9.]/','', $valor);
	}
}
