<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002;

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'cad002atualizarCamposProdutos':
		$D049_Id_Itens = isset($r_D049_Id_Itens) ? $r_D049_Id_Itens : false;
		$D049_Id = isset($r_D049_Id) ? $r_D049_Id : false;
		
		$retorno = $CAD002->cad002gravarProduto($D049_Id_Itens, $D049_Id);
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'cad002atualizarCamposJuntarProdutos':
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
			
			$sql = <<<EOT
			SELECT  *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   				WHERE D009_Id = '{$D009_Id}'
EOT;

			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);
			
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = $row;
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}
   			
		break;
		
	case 'flagMultimarcas':
		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$Flag = isset($r_Flag) ? $r_Flag : false;
		mysql_query("UPDATE D001 SET D001_Flag_Multimarcas = '{$Flag}' WHERE D001_Id = '{$D001_Id}'");
		$resposta['code'] = true;
	break;

	case 'cad002RetornarPrecosPromocao':
		global $g;

		$extraRetornoPreco = isset($r_extraRetornoPreco) ? $r_extraRetornoPreco : false;
		
		$retorno = $CAD002->cad002RetornarPrecosPromocao($extraRetornoPreco);
		
		if ($retorno !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Operacao realizada com sucesso";
		}
	break;

	case 'juntarProcuraProdutos':
		$sql = "SELECT * FROM D001 WHERE D001_Id = '{$r_D001_Id}'";
		$res = mysql_query($sql);
		$resposta['code'] = true;
		$resposta['data'] = mysql_fetch_assoc($res);
	break;

	case 'atualizarCamposProdutosAssociar':
		$D009_Id= isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$D009_Id= mysql_real_escape_string($D009_Id);

		
		$sql = <<<EOT
			SELECT concat(D001_Codigo_Produto, ' - ', D001_Descricao_Produto) as D001_Descricao_Produto,
				   D001_Codigo_Produto,
			       D009_Id
			     FROM D001
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D041 on D041_D049_Id=D049_Id
			WHERE D009_Id = '{$D009_Id}'
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
			
	break;

	case 'atualizarAliquotas':
		global $g;
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		
        if ($r_inicio==1){
	    	$sql = base64_decode($r_extra);
	        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "", $sql);
			$sqlCache = mysqli_query("SELECT D069_D009_Id,D069_Id,D069_Valor_Custo_Unitario,D069_Codigo_Substituto, D070_D024_Id  FROM D069 ".$sql);
			log("SELECT D069_D009_Id,D069_Id,D069_Valor_Custo_Unitario,D069_Codigo_Substituto, D070_D024_Id  FROM D069 ".$sql);
        	$count=0;
        	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['D069_D009_Id'] = $rowCache['D069_D009_Id'];	
			    $cache[$count]['D069_Id'] = $rowCache['D069_Id'];	
			    $cache[$count]['D069_Valor_Custo_Unitario'] = $rowCache['D069_Valor_Custo_Unitario'];	
			    $cache[$count]['D069_Codigo_Substituto'] = $rowCache['D069_Codigo_Substituto'];
			    $cache[$count]['D070_D024_Id'] = $rowCache['D070_D024_Id'];			    
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 20);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheD069-{$g['usuarioAtual']}");
			apc_delete("cacheD069-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheD069-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }
	    } else {
			$cache = unserialize(apc_fetch("cacheD069-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheD069-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache resposta';
	        }else{
	        	$resposta['code'] = true;
	        }
	    }
		
		$resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

    	if($resposta['etapa'] >= $resposta['etapas']){
    		$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
    	} else {
    		$resposta['limit2'] += $resposta['registrosEtapa'] ;
    	}

        $resposta['percentual'] = round((100/$resposta['etapas']) * $resposta['etapa']);

       
        $r_opcoes = json_decode($r_opcoes);
  
        if(!is_array($r_opcoes)) { 
            $opcoes = get_object_vars($r_opcoes);
        }

        $ok = 0;
    	$erro = 0;
    	$erros ="";

        for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 

        	$D069_Id = $cache[$i]['D069_Id'];

						$retorno = $CAD002->atualizarAliquotasProdutos($D069_Id, $opcoes);

   	        if($retorno !== true){
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
           	}

			// Função personalizável conforme a necessidade
			$retorno = $CAD002->Cad002PersonalizacaoTriggerAtualizarTabelaGeralFornecedor($cache[$i]);
			if($retorno !== true){
				$erro++;
				$erros .= $erro ." - ". $retorno."<br />";
			}

        	mysqli_query("call D069_Gravar_Totalizacao('{$D069_Id}', '{$g['usuarioAtual']}')");

        	if($cache[$i]['D069_Codigo_Substituto']!= ''){
				$CAD002->definirCodigoFornecedor($cache[$i]['D069_D009_Id'], $cache[$i]['D070_D024_Id'], $cache[$i]['D069_Codigo_Substituto'], '', false, true);
			}

			$retorno = $CAD002->atualizarCustoProduto($cache[$i]['D069_D009_Id']);

   	        if($retorno !== true){
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
           	}else{
           		$ok++;
           	}

		}

		$resposta['data'] = '';        
        $resposta['ok'] += $ok;
        $resposta['erro'] += $erro;
        $resposta['erros'] .= $erros;

		if($resposta['etapa'] > $resposta['etapas']){
			apc_delete("cacheD069-{$g['usuarioAtual']}");
			apc_delete("cacheD069-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheD069-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheD069-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}

	break;

	case 'atualizarCamposProdutosAssociarInverso':
		$D069_Id= isset($_REQUEST['D069_Id']) ? $_REQUEST['D069_Id'] : false;
		$D069_Id= mysql_real_escape_string($D069_Id);

		
		$sql = <<<EOT
			SELECT D069_Descricao_Produto FROM D069
			WHERE D069_Id = '{$D069_Id}'
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'mudarC008':
		$resposta['data']['D001_D060_Id'] = gProcessaSelect(gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', " WHERE D060_C008_Id = '{$r_C008_Id}' OR D060_C008_Id IS NULL OR D060_C008_Id <= 0 order by D060_Descricao_Grupo ", true));
		$resposta['data']['D001_D060_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D060','D060_Id','D060_Id, D060_Descricao_Grupo'," WHERE D060_C008_Id = '{$r_C008_Id}' OR D060_C008_Id IS NULL OR D060_C008_Id <= 0 order by D060_Id ", true)));
	break;

	case 'infoCustoVendaProduto':
		require_once('bibliotecas/funcoesCallback/infoCustoVendaProduto.php');
		$resposta['data'] = infoCustoVendaProduto(false, array($r_D009_Data_Alteracao_Custo, $r_D009_Data_Ultima_Venda, $r_D009_Flag_Custo_Utilizado), 'S');
	break;

	case 'atualizarCamposIncluirProdutoFornecedor':
		$D009_Id= isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;

		$sql = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id = '{$D009_Id}'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);

		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'obterCodigoAtualizado':
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		$codigoProduto = $CAD002->obterNovoCodigoProduto($r_acaoId, false, true);
		if($codigoProduto !== false){
			$resposta['code'] = true;
			$resposta['data'] = $codigoProduto;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao obter código do produto";
		}
	break;

	case 'produtoFornecedor':
		$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false; 
		$sql = "SELECT D001_Id,D001_Descricao_Produto FROM D001 
					LEFT JOIN D049 ON D049_D001_Id = D001_Id
					LEFT JOIN D009 ON D009_D049_Id = D049_Id					
					WHERE D001_Id = '{$D001_Id}'";
		$res = mysql_query($sql);
		if(mysql_num_rows($res) > 0){
			$result = mysql_fetch_array($res);
			$resposta['data'] = $result;
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}		
	break;

	case 'enviarProdutosTray':	

		set_time_limit(0);

		if ($r_inicio == 1) {

			if($g['C031']['sincronizaGrupoESubGrupoTray'] == 'S'){
				require_once('bibliotecas/webservices/tray/envioCategoriasTray.php');
				$envioCategorias = new EnvioCategoriasTray();
				$envioCategorias->executar();
			}

			require_once('bibliotecas/webservices/tray/envioProdutosTray.php');
			$envioProdutos = new EnvioProdutosTray();
			$envioProdutos->executar();

			if($g['C031']['sincronizaImagensProdutosTray'] == 'S'){
				require_once('bibliotecas/webservices/tray/envioProdutosImagensTray.php');
				$EnvioProdutosImagensTray = new EnvioProdutosImagensTray();
				$EnvioProdutosImagensTray->executar();
			}
		}

		$resposta = array('finalizado' => true);
	break;

	case 'sincronizarIDsIntegracaoD186Tray':	

		set_time_limit(0);
		$sincD185 = false; // desativado, usar somente em casos BEM específicos

		if ($r_inicio == 1) {

			require_once('bibliotecas/webservices/tray/conexaoTray.php');
			$conexaoTray = new ConexaoTray();

			require_once('bibliotecas/webservices/tray/envioProdutosTray.php');
			$envioProdutos = new EnvioProdutosTray();

			if ($conexaoTray->isTudoOk()) {

				if ($sincD185) {
					$sql = "select D185_Id, D185_Codigo 
							from D185 
							where coalesce(D185_Integracao_ID,0) = 0 
							and exists (select D186_Id from D186 where D186_D185_Id = D185_Id) ";

					$query = mysql_query($sql);
					while ($row = mysql_fetch_assoc($query)) {
						$D185_Codigo = $row['D185_Codigo'];
						$D185_Id = $row['D185_Id'];

						$deTray = $conexaoTray->execGet("products", array('reference' => $D185_Codigo), true);

						if (!empty($deTray['Products'])) {
							foreach ($deTray['Products'] as $produto) {
								$D185_Integracao_ID = $produto['Product']['id'];
								$sql = "update D185 set D185_Integracao_ID = {$D185_Integracao_ID} where D185_Id = {$D185_Id}";
								mysql_query($sql);
								break;
							}
						} else {
							$D185_Erro_Ult_Envio = mysql_real_escape_string(\json_encode($deTray));
							$sql = "update D185 set D185_Erro_Ult_Envio = '{$D185_Erro_Ult_Envio}' where D185_Id = {$D185_Id}";
							mysql_query($sql);
						}
					}
				}

				$sql = "SELECT D001_Codigo_Produto, D186_Id, D185_Id, D185_Integracao_ID
						FROM D186 
						JOIN D185 ON D186_D185_Id = D185_Id
						JOIN D001 ON D186_D001_Id = D001_Id
						where coalesce(D186_Integracao_ID,0) = 0
						and   coalesce(D185_Integracao_ID,0) > 0
						and   D186_Integrado_Tray = 'Z' ";

				$query = mysql_query($sql);
				while ($row = mysql_fetch_assoc($query)) {
					$product_id = $row['D185_Integracao_ID'];
					$reference = $row['D001_Codigo_Produto'];

					$D186_Id = $row['D186_Id'];
					$D186_Integracao_ID = $envioProdutos->getIdVariacaoTray($conexaoTray, $product_id, $reference);

					if ($D186_Integracao_ID > 0) {
						$sql = "update D186 set D186_Integracao_ID = {$D186_Integracao_ID}, D186_Integrado_Tray = 'X' where D186_Id = {$D186_Id}";
						mysql_query($sql);
					} else {
						$sql = "update D186 set D186_Integrado_Tray = 'E' where D186_Id = {$D186_Id}";
						log("Produto de código {$row['D001_Codigo_Produto']} não teve seu ID encontrado na Tray! D186_Id: {$D186_Id}");
					}
				}
			}

		}

		$resposta = array('finalizado' => true);
	break;
	
	/* Faz uma importação generica para todas as tabelas do fornecedor em todas as empresas*/
	case 'tabelaGeral':
		$configCampos = $CAD002->retornaArrayTabelasGeral();

		$D069CodProd = $configCampos[0];
		if($D069CodProd["nomeCampo"] != "D069_Codigo_Produto" || $D069CodProd["obrigatorio"] != true) {
			$resposta = array("data" => "O campo 'D069_Codigo_Produto' é obrigatório e deve estar na primeira coluna. Favor contatar o suporte.");
			break;
		}

		$tabelas_fornecedores = $r_tabelas_fornecedor;
		if(count($tabelas_fornecedores) <= 0){
			$resposta = array("data" => "Nenhuma tabela fornecedor foi selecionada. Por favor selecionar alguma tabela antes de enviar");
			break;
		}
		$implodeSqlInTabelasFornecedor = implode(",", $tabelas_fornecedores);
		
		// Tratamento arquivo ---------------------------------------
		$nomeEnvioCsvTabelaGeral = $_FILES['envioCsvTabelaGeral']['tmp_name'];
		$tipoNomeEnvioCsvTabelaGeral     = substr($nomeEnvioCsvTabelaGeral, strrpos($nomeEnvioCsvTabelaGeral, ".") + 1, 3);
		$tipoNomeEnvioCsvTabelaGeral     = StrToLower($tipoNomeEnvioCsvTabelaGeral);

		if (empty($nomeEnvioCsvTabelaGeral)) { // Importante pq ele pode tentar enviar sem selecionar um arquivo
			$resposta = array("data" => "Arquivo está vazio");
			break;
		}

		$ponteiro = fopen($nomeEnvioCsvTabelaGeral, "r"); // Abre o arquivo
		if (!$ponteiro){
			$resposta = array("code" => true, "data" => "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})");		
			break;
		}

		$tipoArquivo='UTF-8';
		while ($array_linha = fgets($ponteiro)) {
			// Limpa characteres especiais do arquivo texto
			$array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

			if (mb_detect_encoding($array_linha)!='UTF-8'){
				$tipoArquivo=mb_detect_encoding($array_linha);
				break;
			}
		}

		$ponteiro = fopen($nomeEnvioCsvTabelaGeral, "r"); // Abre o arquivo
		if (!$ponteiro){
			$resposta = array("code" => true, "data" => "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})");		
			break;
		}

		// Importação do arquivo ----------------------------------------------------------------------
		$errosImportacao = array();
		$linha = 0; 
		while ($array_linha = fgets($ponteiro)){
			$linha +=1;
			$colunas = explode(";", $array_linha); // pelo meu arquivo ser separado por (;)

			if($linha == 1){
				if(count($colunas) !== count($configCampos)){
					$errosImportacao[] = "O número de colunas existentes no arquivo CSV está diferente da configuração!";
					break;
				}
				continue;
			}

			// Se D069_Codigo_Produto estiver vazio
			if(empty($colunas[0])){
				$errosImportacao[] = "Linha $linha - A chave primária D069_Codigo_Produto está vazia na linha. A linha foi pulada";
				continue;
			}

			// Array que vai formar as colunas que serão atualizadas no update
			$camposUpdate = array();
			$camposUpdateJson = array();
			// Salva os erros das colunas
			$errosColunas = array(); 
			foreach($configCampos as $key => $value){
				$nomeCampo = $value["nomeCampo"];
				$valorCampo = $colunas[$key];

				if($value['obrigatorio'] && (empty($valorCampo) || trim($valorCampo) == '[NAO-ALTERAR]')){
					$errosColunas[] = "Linha $linha - A coluna $nomeCampo é obrigatória.";
				}
				
				if($nomeCampo == 'D069_Codigo_Produto' OR trim($valorCampo) == '[NAO-ALTERAR]'){
					continue;
				}

				$valorCampo = addslashes($valorCampo);
				// Se for um campo JSON é preciso fazer outro tratamento para atualizar o campo
				if(strpos(strtoupper($nomeCampo), "JSON") !== false){
					$separaNomeCampoJson = explode("->", $nomeCampo);
					$nomeCampoJson = trim($separaNomeCampoJson[0]);
					$nomeCampoDentroJson = trim($separaNomeCampoJson[1]);

					$camposUpdateJson[$nomeCampoJson][] = "'$.{$nomeCampoDentroJson}', '{$valorCampo}'";
				} else {
					$camposUpdate[] = "$nomeCampo = '$valorCampo'";
				}
			}

			if(count($errosColunas) > 0){
				$errosImportacao = array_merge($errosImportacao, $errosColunas);
				continue;
			}

			// Junta numa string os campos JSON(tem uma tratativa diferente para update)
			foreach($camposUpdateJson as $key => &$value){
				$value = "$key = JSON_SET(COALESCE(D069A_JSON, '{}'), ".implode(",", $value).")";
			}

			$camposUpdate = array_merge($camposUpdate, $camposUpdateJson);

			// Junta numa string os campos normais
			$camposUpdate = implode(", ", $camposUpdate);
			$qD069 = "UPDATE D069
				   LEFT JOIN D069A ON D069A_D069_Id = D069_Id
						 SET $camposUpdate
					   WHERE D069_Codigo_Produto = '{$colunas[0]}'
					     AND D069_D070_Id IN ($implodeSqlInTabelasFornecedor)";
			log("qD069 -> $qD069");
			mysql_query($qD069);
			if($erro = mysql_error()){
				$erro = "Erro de importação na linha $linha: $erro";
				$errosImportacao[] = $erro;
				continue;
			}
		}

		$breakChar = "&#"."13".";&#"."10".";";

		$errosImportacao[] = "Processo finalizado.";
		$errosImportacao = implode("$breakChar", $errosImportacao);
	
		$resposta = array("data" => $errosImportacao);
	break;	

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
