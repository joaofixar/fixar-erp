<?php
namespace hardness;
/**
* est - est019 - Cadastro de Orçamentos
*
* Processamento de ações AJAX
*/
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();
require_once('bibliotecas/classes/EST019.php');
$EST019 = new EST019();

/**
* Ações
*/
switch($g['r'][3]) {

	case 'est019atualizarCFOP':
		require_once("bibliotecas/classes/EST019.php");
		$EST019 = new EST019();

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$retorno = $EST019->est019atualizarCFOP($T293_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est019TotalizarInserirItemEstoque':
		
		$T293_Id 		   = isset($r_T293_Id) ? $r_T293_Id : false;
		$grupo             = isset($r_grupo) ? $r_grupo : false;
		$resposta['grupo'] = $grupo;
        
		
		$retorno = mysql_query("select T294_Id from T294 where T294_T293_Id='{$T293_Id}'");
		$cErro=mysql_error();

        while ($row = mysql_fetch_array($retorno)) {
            $cSql = mysql_query("call T294_Gravar_Totalizacao('{$row['T294_Id']}');");
            $cErro=mysql_error();
        }
		
		if(!empty($cErro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao totalizar produtos da cotação";	
		}else{
			$resposta['code'] = true;
		}

	break;

	case 'est019TotalizarInsereItemCotacaoEmergencia':
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;      
		
		$retorno = mysql_query("select T294_Id from T294 where T294_T293_Id='{$T293_Id}'");
		$cErro   = mysql_error();

        while ($row = mysql_fetch_array($retorno)) {
            $cSql  = mysql_query("call T294_Gravar_Totalizacao('{$row['T294_Id']}');");
            $cErro =mysql_error();
        }
		
		if(!empty($cErro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao totalizar produtos da cotação";	
		}else{
			$resposta['code'] = true;
		}

	break;

	case 'atualizarCamposProdutos':
		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;

		log('D009_Id = '.$D009_Id);

		log('T293_Id = '.$T293_Id);

		/*
		T219_Ultimo_Preco(D001_Id,'$mT218[T218_D024_Id]','$T293_Id',1) as Preco_Consultado,
		T219_Ultimo_Preco(D001_Id,'$mT218[T218_D024_Id]','$T293_Id',2) as Preco_Confirmado
		*/

        ///////////////////////////////////////
        // Busca dados do cabeçalho da Cotação
        ///////////////////////////////////////
		$T218  = mysql_query("SELECT T218_D024_Id,T218_Flag_Destino_Produto from T218 WHERE T293_Id = '{$T293_Id}'");
		$mT218 = mysql_fetch_assoc($T218);
		$row['T218_Flag_Destino_Produto'] = $mT218['T218_Flag_Destino_Produto'];

        ///////////////////////////////////////
        // Busca dados do cadastro do produto
        ///////////////////////////////////////
		$D009 = mysql_query("SELECT D001_Codigo_Produto,
		                            D001_Descricao_Produto,
		                            D082_Marca,
		                            D001_D037_Id,
		                            D001_D005_Id,
		                            D001_Id,
		                            D083_Codigo_Produto_Fornecedor,
		                            D049_Id,
		                            D009_Quantidade_Estoque_Liquido,
		                            D009_Quantidade_Estoque_Loja,
		                            D009_Quantidade_OC,
		                            D049_Origem_Mercadoria,
		                            D009_Quantidade_Estoque_Fora,
									D005_Flag_Ativo
		                       FROM D009
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                          LEFT JOIN D005 ON D005_Id=D001_D005_Id 
                          LEFT JOIN D082 ON D082_Id=D049_D082_Id
                          LEFT JOIN D083 on D083_D001_Id=D001_Id and D083_D024_Id='{$mT218['T218_D024_Id']}'
                              WHERE D009_Id = '{$D009_Id}'");
		$mD009 = mysql_fetch_assoc($D009);

		$row['D001_Codigo_Produto']    = $mD009['D001_Codigo_Produto'];
		$row['D001_Descricao_Produto'] = $mD009['D001_Descricao_Produto'];
		$row['D082_Marca']             = $mD009['D082_Marca'];
		$row['D037_Id']                = $mD009['D001_D037_Id'] ? $mD009['D001_D037_Id'] : $row['D037_Id']; 
		$row['D005_Id']                = $mD009['D001_D005_Id'] ? $mD009['D001_D005_Id'] : $row['D005_Id'];
		$row['Codigo_Produto_Fornecedor'] = $mD009['D083_Codigo_Produto_Fornecedor'];
		$row['Preco_Produto']          = 0;
		$row['D009_Id']                = $D009_Id;
		$row['D001_Id']                = $mD009['D001_Id'];
		$row['D049_Origem_Mercadoria'] = $mD009['D049_Origem_Mercadoria'];
		$row['D005_Flag_Ativo'] = $mD009['D005_Flag_Ativo'];
		$row['Validacao_NCM'] = ($mD009['D005_Flag_Ativo'] == 'N') ? "NCM do cadastro do produto está inativa." : "";

		$row['estoqueLiquido'] = gCorrigeNumero($mD009['D009_Quantidade_Estoque_Liquido']);
		$row['estoqueLoja'] = gCorrigeNumero($mD009['D009_Quantidade_Estoque_Loja']);
		$row['estoqueTotal'] = gCorrigeNumero($mD009['D009_Quantidade_Estoque_Liquido']+$mD009['D009_Quantidade_Estoque_Loja']);
		$row['ordemCompra'] = gCorrigeNumero($mD009['D009_Quantidade_OC']);
		$row['estoqueFora'] = gCorrigeNumero($mD009['D009_Quantidade_Estoque_Fora']);

        ////////////////////////////////////
        // Busca dados da Cotação de Compra
        ////////////////////////////////////
        //ORC-118508 - QNT: 1,00 - R$ 10,00 - 10/07/2014 - ADMINIST (1)

		$T219 = mysql_query("SELECT T219_T293_Id, T219_D037_Id, T219_D005_Id, T219_Valor_Preco_Unitario, T219_Aliquota_ICMS, T219_Aliquota_IPI, T218_C004_Id
									T219_Codigo_Substituto, T219_ST_ICMS_Interno, T219_ICMS_Inter_Estadual, T219_ST_VA, T219_Flag_ST, T218_Data_Emissao, D005_Flag_Ativo
							FROM T219
							LEFT JOIN T218 ON T293_Id=T219_T293_Id
							LEFT JOIN D005 ON D005_Id=T219_D005_Id
							WHERE T219_D009_Id = '{$D009_Id}'
							AND T218_D024_Id = '{$mT218['T218_D024_Id']}'
							ORDER BY T293_Id DESC LIMIT 1");
		if (mysql_num_rows($T219)>0){
			$mT219 = mysql_fetch_assoc($T219);

			$row['D037_Id']       = $mT219['T219_D037_Id'] ? $mT219['T219_D037_Id'] : $row['D037_Id'];
			$row['Validacao_NCM'] = ($mT219['T219_D005_Id'] && $mT219['D005_Flag_Ativo'] == 'N') ? "NCM da cotação de compra está inativa." : "";
			$row['D005_Id']       = ($mT219['T219_D005_Id'] && $mT219['D005_Flag_Ativo'] == 'S') ? $mT219['T219_D005_Id'] : $row['D005_Id'];
			$row['D005_Flag_Ativo']       = ($mT219['T219_D005_Id'] && $mT219['D005_Flag_Ativo'] == 'S') ? $mT219['D005_Flag_Ativo'] : $row['D005_Flag_Ativo'];

			//$row['Preco_Produto'] = number_format($mT219['T219_Valor_Preco_Unitario'],4,',','.');
			//$row['Aliquota_ICM']  = number_format($mT219['T219_Aliquota_ICMS'],2,',','.');
			//$row['Aliquota_IPI']  = number_format($mT219['T219_Aliquota_IPI'],2,',','.');
			//$row['Codigo_Produto_Fornecedor'] = $mT219['T219_Codigo_Substituto'];
			//$row['Aliquota_ICM_Interno']  = number_format($mT219['T219_ST_ICMS_Interno'],2,',','.');
			//$row['Aliquota_ICM_Inter_Estadual']  = number_format($mT219['T219_ICMS_Inter_Estadual'],2,',','.');
			//$row['MVA']           = number_format($mT219['T219_ST_VA'],2,',','.');
			//$row['Flag_ST']       = $mT219['T219_Flag_ST'];
		    $row['ultimoPrecoVenda'] .= "COT-{$mT219['T219_T293_Id']}. - QNT:  R$ ".number_format($mT219['T219_Valor_Preco_Unitario'],2,',','.')." ".gcorrigeData($mT219['T218_Data_Emissao'])." ) ";
		}

        ////////////////////////////////////////
        // Busca dados da tabela do fornecedor
        ////////////////////////////////////////
		$sql = <<<EOT
			SELECT  *
   				from D069
   				LEFT JOIN D009 ON D009_Id = D069_D009_Id
   				LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
   				left join D070 on D070_Id=D069_D070_Id
   				left join D037 on D037_Unidade = D069_Unidade
   				left join D005 on if(D069_NCM != '', D005_Id = D069_D005_Id, D005_Id=D001_D005_Id)
				WHERE D069_D009_Id = '{$D009_Id}'
				AND D070_D024_Id = '{$mT218['T218_D024_Id']}'
   				ORDER BY D069_Id desc 
EOT;

		log($sql);

		$D069  = mysql_query($sql);

		if (mysql_num_rows($D069)>0){
			$mD069 = mysql_fetch_assoc($D069);

			$row['D037_Id']     	= $mD069['D037_Id'] ? $mD069['D037_Id'] : $row['D037_Id'];
			$row['D005_Id']     	= $mD069['D005_Id'] ? $mD069['D005_Id'] : $row['D005_Id'];
			$row['D005_Flag_Ativo'] = $mD069['D005_Flag_Ativo'] ? $mD069['D005_Flag_Ativo'] : $row['D005_Flag_Ativo'];
			$row['Validacao_NCM'] = ($mD069['D005_Flag_Ativo'] == 'N') ? "NCM do cadastro do produto na tabela fornecedor está inativa." : "";

			//$row['Codigo_Produto_Fornecedor'] = $mD069['D069_Codigo_Produto'];
			//$row['Preco_Produto'] = number_format($mD069['D069_Preco_Unitario_Liquido'],4,',','.');
			//$row['Aliquota_ICM']  = number_format($mD069['D069_Aliquota_ICMS'],2,',','.');
			//$row['Aliquota_IPI']  = number_format($mD069['D069_Aliquota_IPI'],2,',','.');
			//$row['Aliquota_IPI']  = number_format($mD069['D069_Aliquota_IPI'],2,',','.');

			log('Inter estadual = '.$mD069['D069_Aliquota_ICM_Inter_Estadual'])	;

			//$row['Aliquota_ICM_Interno']  = number_format($mD069['D069_Aliquota_ICM_Interno'],2,',','.');
			//$row['Aliquota_ICM_Inter_Estadual']  = number_format($mD069['D069_Aliquota_ICM_Inter_Estadual'],2,',','.');
			//$row['MVA']           = number_format($mD069['D069_MVA'],2,',','.');
			//$row['Flag_ST']       = $mD069['D069_Flag_ST'];

		    $row['ultimoPrecoVenda'] .= ' ( Tab Forn. R$ '.number_format($mD069['D069_Preco_Unitario_Liquido'],2,',','.').' ) ';
		}
		$row['UltimoPrecoCompra'] = $EST019->retornaUltimoPrecoCompra($mD009['D049_Id'], $mT218['T218_D024_Id']);
		// Método que irá retonar o último preço que o produto foi estdido para o cliente
		// $row['ultimoPrecoVenda'] = $EST019->retornaltimoPrecoVenda($mT218['T218_D024_Id'], '$D009_Id');
		$row['D006_Id'] = $EST019->retornaCFOP($mT218['T218_D024_Id'], $D009_Id);
		log("Campos retornados:".json_encode($row));

		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'atualizarCamposServicos':
		global $g;
		$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
		$D112_Id = mysql_real_escape_string($D112_Id);

		$sql = "SELECT * FROM T212 LEFT JOIN D112 ON D112_T212_Id = T212_Id WHERE D112_Id = '{$D112_Id}'";

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
		$teste = strtotime($row['D112_Tempo_Estimado']);
		$teste = date("H:i", $teste);
		$row['D112_Tempo_Estimado'] = $teste;

		$row['selectTipoServico'] = gProcessaSelect(gGeraSelect('T212', 'T212_Id', 'T212_Nome', "", true, $row['D112_T212_Id']));
		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;
	
	case 'gerarPedido':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		
		$EST019 = new EST019();
		
		$T224_Id = 0;
		$retorno = $EST019->gerarPedido($T293_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Ordem de Compra gerada com sucesso!";
			$resposta['T224_Id'] = $T224_Id;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'aplicarIPV':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$IPV = isset($r_IPV) ? $r_IPV : false;
		
		$IPV = str_replace('.', '', $r_IPV);
		$IPV = str_replace(',', '.', $IPV);
		
		$EST019 = new EST019();
		
		$retorno = $EST019->aplicarIpvMinimo($T293_Id, $IPV);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "IPV aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est019aplicarDesconto':
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$Desconto = isset($r_Desconto) ? $r_Desconto : false;
		$CampoAplicarDesconto = isset($r_CampoAplicarDesconto) ? $r_CampoAplicarDesconto : false;
		
		//$Desconto = str_replace('.', '', $r_Desconto);
		//$Desconto = str_replace(',', '.', $Desconto);
		
		$EST019 = new EST019();
		
		$retorno = $EST019->est019aplicarDesconto($T293_Id, $Desconto, $CampoAplicarDesconto);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'ImportarOrcamento':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$T293_Id_Origem = isset($r_T293_Id_Origem) ? $r_T293_Id_Origem : false;
		
		$EST019 = new EST019();
		
		$retorno = $EST019->importarOrcamento($T293_Id, $T293_Id_Origem);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Orçamento importado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'atualizarAliquota':
		require_once("bibliotecas/classes/EST019.php");
		$EST019 = new EST019();

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		//$C004_Id = isset($r_C004_Id) ? $r_C004_Id : false;

		$resposta['code'] = true;

		if ($r_inicio==1){
			
			if (empty($T293_Id)) {
				$resposta['concluido'] = 1;
				$resposta['code']      = false;
            	$resposta['data']      = "Não foi passado o Id da Cotação, contate o suporte técnico";
            	break;
        	}

        	$cmysql_T218 = mysql_query("SELECT T296_Id
                                          FROM T294
                                     LEFT JOIN T296 ON T296_T294_Id=T294_Id
                                         WHERE T294_T293_Id='{$T293_Id}'
                                           AND T296_Id > 0");

	        if (mysql_num_rows($cmysql_T218) > 0) {

	        	$resposta['concluido'] = 1;
	            $resposta['code']      = false;
	            $resposta['data'] 	   = 'Esta Cotação já foi faturada, não é permitido mudar a CFOP';
	            break;
	        }
	        
	        $cClausula = "SELECT T294_Id,
								 T293_Id,
								 D009_Id,
	                             D001_Peso_Unitario_Kg
	                        FROM T293
	                   LEFT JOIN T294 ON T293_Id=T294_T293_Id
	                   LEFT JOIN T296 ON T294_Id=T296_T294_Id
	                   LEFT JOIN D009 ON D009_Id=T294_D009_Id
	                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                       WHERE T293_Id='{$T293_Id}'
	                         AND T296_Id IS NULL";
	        log("Clausula= ".$cClausula);
	                        
	        $cmysql_T294 = mysql_query("$cClausula");
	       	$erroMysql = mysql_error();
	        if (!empty($erroMysql)) 
	        {
	        	$resposta['concluido'] = 1;
	        	$resposta['code'] = false;
	            $resposta['data'] = "Erro consultando itens do orçamento: $erroMysql $cClausula";
	            break;
	        }

	        $count = 0;			
	      	while($rowCache = mysql_fetch_array($cmysql_T294)){
				$cache[$count]['T293_Id']				= $rowCache['T293_Id'];
		       	$cache[$count]['T294_Id']               = $rowCache['T294_Id'];
		   		$cache[$count]['D009_Id']			    = $rowCache['D009_Id'];	
		   		$cache[$count]['D001_Peso_Unitario_Kg'] = $rowCache['D001_Peso_Unitario_Kg'];
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas']         = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] 		= ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] 		    = 1;
	        $resposta['ok'] 			= 0;
    	    $resposta['erro'] 			= 0;
        	$resposta['concluido'] 		= 0;
			apc_delete("cacheT296-{$g['usuarioAtual']}");
			apc_delete("cacheT296-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT296-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        	$resposta['concluido'] = 1;
	        	break;
	        }
        } else {
			$cache    = unserialize(apc_fetch("cacheT296-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT296-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code']      = false;
	        	$resposta['data']      = 'Erro ao recuperar variável no cache resposta';
	        	$resposta['concluido'] = 1;
	        	break;
	        }
	    }
	    $resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

		if($resposta['etapa'] >= $resposta['etapas']){
			$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
		} else {
			$resposta['limit2'] += $resposta['registrosEtapa'] ;
		}

	    $resposta['percentual'] = ceil((100/$resposta['etapas']) * $resposta['etapa']);
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	   	$ok    = 0;
		$erro  = 0;
		$erros ="";

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$retorno = $EST019->atualizarAliquotas($cache[$i]);	            
           	if($retorno !== true){
           		log("&&entra aqui erro ".$i);
       	     	$erro++;
           	 	$erros .= $retorno."<br />";
           	 	$resposta['code'] = false;
           	 	log("ERROS: ".$erros);
       		}else{
       			log("&&entra aqui ok: ".$i);
       			$ok++;
       		}
        }
   	
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			apc_delete("cacheT296-{$g['usuarioAtual']}");
			apc_delete("cacheT296-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT296-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT296-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	       
	break;

	case 'atualizarCFOPManual':
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$D006_Id = isset($r_D006_Id) ? $r_D006_Id : false;

		$resposta['code'] = true;
		log("T293_Id no atualizar CFOP Manual= ".$T293_Id);
		log("D006_Id no atualizar CFOP Manual= ".$D006_Id);
		mysql_query("UPDATE T218 SET T218_D006_Id = '{$D006_Id}' WHERE T293_Id = '{$T293_Id}'");
		$erroSQLT218    = mysql_error();
        if (!empty($erroSQLT218)) {
        	$resposta['code'] = false;
            $resposta['data'] = $erroSQLT218;
        }

        mysql_query("UPDATE T219 SET T219_D006_Id = '{$D006_Id}' WHERE T219_T293_Id = '{$T293_Id}'");
        log("UPDATE T219 SET T219_D006_Id = '{$D006_Id}' WHERE T219_T293_Id = '{$T293_Id}'");
        $erroSQLT219    = mysql_error();
        if (!empty($erroSQLT219)) {
        	$resposta['code'] = false;
            $resposta['data'] = $erroSQLT219;
        }
	break;

	
	case 'salvarObservacao':
		require_once("bibliotecas/classes/EST019.php");
		
		$form=$r_est019FormPagamento;		
		$T293_Id = (isset($r_T293_Id)) ? $r_T293_Id : false;
		$EST019 = new EST019();		
		$retorno = $EST019->gravarObservacao($form, $T293_Id);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da Observação. Por favor, tente novamente';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'confirmarTudo':
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		
		$sql = <<<EOT
 		update T219 set T219_Quantidade_Confirmada=T219_Quantidade where T219_T293_Id = '{$T293_Id}';
EOT;
		$request = mysql_query($sql);

		
		if ($request) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
   		
	break;
		
	case 'zerarTudo':
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;
		
		$sql = <<<EOT
 		update T219 set T219_Quantidade_Confirmada=0 where T219_T293_Id = '{$T293_Id}';
EOT;
		$request = mysql_query($sql);

		
		if ($request) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
		
	break;
	
	case 'carregaCFOP_Contatos':		
		global $g;			
		$T218_D024_Id = isset($r_T218_D024_Id) ? $r_T218_D024_Id : false;						
		if (empty($T218_D024_Id)) {
			$resposta['code'] = false;
			$resposta['data']['erro'] = "Nenhum cliente selecionado";
		} else {
			$sql = "SELECT D024_D006_Id, 
			               D024_D018_Id,
                           D024_D021_Id,
			               D024_Flag_Revenda_Consumidor,
			               D024_Flag_Contribuinte,
			               D024_Percentual_Comissao,
			               D021_Percentual_Comissao,
			               D024_Nome_Fantasia
			          FROM D024 
			     LEFT JOIN D021 ON D021_Id=D024_D021_Id
			         WHERE D024_Id=" . $T218_D024_Id;
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			
			echo mysql_error();

			if(!empty($aD024['D024_Percentual_Comissao'])){
				$resposta['data']['T218_Percentual_Comissao'] = gCorrigeNumero($aD024['D024_Percentual_Comissao']);
			}else{
				$resposta['data']['T218_Percentual_Comissao'] = gCorrigeNumero($aD024['D021_Percentual_Comissao']);
			}
			$C007=mysql_query("SELECT C007_Flag_Expedicao FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
			$mC007=mysql_fetch_array($C007);

			if($mC007['C007_Flag_Expedicao']=="1"){

				$resposta['data']['T218_Flag_Expedicao'] = gProcessaSelect(
					array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Comercial', 'value' => '1', 'selected'=>true),
						array('title' => 'Balcão', 'value' => '2'),
					)
				);
			} else if($mC007['C007_Flag_Expedicao']=="2"){

				$resposta['data']['T218_Flag_Expedicao'] = gProcessaSelect(
					array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Comercial', 'value' => '1'),
						array('title' => 'Balcão', 'value' => '2', 'selected'=>true),
					)
				);
			} else {
				$resposta['data']['T218_Flag_Expedicao']="";
			}
			$resposta['data']['T218_Nome_Cliente'] = $aD024['D024_Nome_Fantasia'];
			/*				
			$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
			               C004_CFOP_Venda_Fora_Estado, 
			               C004_CFOP_ST_Venda_Fora_Estado, 
			               C004_CFOP_ST_Venda_Dentro_Estado, 
			               C004_CFOP_CONSUMO_Venda_Fora_Estado, 
			               C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
			               C004_D018_Id 
			          FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
			$result = mysql_query($sql);
			$aC004 = mysql_fetch_assoc($result);
			
			echo mysql_error();
			
			$resposta['code'] = true;
			$D006_Id = $aD024['D024_D006_Id'];
			
            if ($D006_Id<=0)
            {
		        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
		        {
		            $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
				}
				else
				{       
		            $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
				}	
			    if ($aD024['D024_Flag_Contribuinte'] == '1')
			    {
			        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			        {
			            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
					}
					else
					{
			            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
					}	
				}
			}
			*/
			
			require_once("bibliotecas/classes/EST019.php");
			$EST019 = new EST019();
			$D006_Id=$EST019->retornaCFOP($T218_D024_Id);

			$resposta['code'] = true;
			// se nenhuma regra satisfizer, usa o critério antigo	
			if ($D006_Id===false){
				$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, 
				               C004_CFOP_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Fora_Estado, 
				               C004_CFOP_ST_Venda_Dentro_Estado, 
				               C004_CFOP_CONSUMO_Venda_Fora_Estado, 
				               C004_CFOP_CONSUMO_Venda_Dentro_Estado, 
				               C004_D018_Id 
				          FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
				$result = mysql_query($sql);
				$aC004 = mysql_fetch_assoc($result);
				
				echo mysql_error();
				
				$D006_Id = $aD024['D024_D006_Id'];
				
                if ($D006_Id<=0)
                {	                	
			        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
			        {
			            $D006_Id = $aC004['C004_CFOP_Venda_Fora_Estado'];
					}
					else
					{       
			            $D006_Id = $aC004['C004_CFOP_Venda_Dentro_Estado'];
					}	
				    if ($aD024['D024_Flag_Contribuinte'] == '1')
				    {
				        if ($aD024['D024_D018_Id'] != $aC004['C004_D018_Id'])
				        {
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Fora_Estado'];
						}
						else
						{
				            $D006_Id = $aC004['C004_CFOP_CONSUMO_Venda_Dentro_Estado'];
						}	
					}
				}
				if ($D006_Id<=0)
					$D006_Id=false;					
			}	

			// se apenas uma regra tiver sido satisfeita
		    if ($D006_Id>0) 
			{
				$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo!="S" ORDER BY D006_Codigo_CFOP',true, $D006_Id);
				$resposta['data']['T218_D006_Id'] = gProcessaSelect($arrayDentroEstado);
			}
			// se nenhuma regra tiver sido satisfeita (critério novo e antigo)
			else if ($D006_Id===false)
			{
				$resposta['data']['T218_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE D006_Flag_Inativo!="S" ORDER BY D006_Codigo_CFOP',true));
			}
			// se mais de uma regra tiver sido satisfeita
			else
			{
				$resposta['code'] = false;
				$resposta['data']['erro'] = $D006_Id;	
			}
			//$resposta['data']['T218_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id='{$T218_D024_Id}'",true));
			$resposta['data']['T218_Flag_Revenda_Consumidor'] = "$aD024[D024_Flag_Revenda_Consumidor]";
			$resposta['data']['T218_C007_Id_Vendedor_Interno'] = "{$g['usuarioAtual']}";
			
			// carrega os contatos do cliente selecionado
			if($g['C031']['orcamentoCarregarPrimeiroContato'] == 'N'){
				//select para carregar os contatos e deixar "..." selecionado na combo
				$resposta['data']['T218_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T218_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true));		
				$resposta['data']['T218_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T218_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato',true)));
			}else{
				//select para carregar os contatos e deixar o primeiro contato selecionado na combo
				$resposta['data']['T218_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T218_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false));		
				$resposta['data']['T218_D013_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail','WHERE D013_D024_Id='.$T218_D024_Id.' AND D013_Flag_Ativo = "S" ORDER BY D013_Id',false)));
			}
			
			//carrega os veículos do cliente selecionado
			$resposta['data']['T218_D116_Id'] = gProcessaSelect(gGeraSelect('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T218_D024_Id,true));
			$resposta['data']['T218_D116_Id_refreshBtn'] = $selectRefresh = base64_encode(serialize(array('D116','D116_Id','D116_Placa,D133_Modelo,D134_Cor',' JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id='.$T218_D024_Id,true)));

			//carrega o representante
			$resposta['data']['T218_D021_Id'] = gProcessaSelect(gGeraSelect('D021','D021_Id','D021_Nome_Empresa', '',true,$aD024['D024_D021_Id']));
		}
		
	break;
		
	case 'verificaFlagContribuinte':

		global $g;
		
		$T218_D024_Id = isset($r_T218_D024_Id) ? $r_T218_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Contribuinte, D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$T218_D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		$msg = "";
		$D024['D024_Flag_Lista_Preco'] = trim($D024['D024_Flag_Lista_Preco']);
		if (!empty($D024['D024_Flag_Lista_Preco']))	{
			$retorno = 1;
		}else{
			$retorno = 2;
			$msg .= "Favor preencher o campo LISTA PREÇO no cadastro do cliente<br />";
		}
		
		if ($D024['D024_Flag_Contribuinte']>-1)	{
			$retorno = ($retorno == 2) ? 2 : 1;
		}else{
			$retorno = 2;
			$msg .= "Favor preencher o campo TIPO no cadastro do cliente";
		}
		
        // Não vamos utilizar este método
        return true;

		if ($retorno == 1) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $msg;
		}
	break;	
	
	case 'verificaFlagListaPreco':
		global $g;
		
		$T218_D024_Id = isset($r_T218_D024_Id) ? $r_T218_D024_Id : false;

		$result = mysql_query("SELECT D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$T218_D024_Id}'");
		$D024 = mysql_fetch_assoc($result);
		
		switch($D024['D024_Flag_Lista_Preco']){
			case '1':
				$resposta['data']['T218_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1', 'selected' => true),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
						));
				$resposta['code'] = true;
				break;
			case '2':
				$resposta['data']['T218_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2', 'selected' => true),   
							array('title' => 'Preço 3', 'value' => '3'),   
						));
				$resposta['code'] = true;
				break;
			case '3':
				$resposta['data']['T218_Flag_Lista_Preco'] = gProcessaSelect(
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3', 'selected' => true),   
						));
				$resposta['code'] = true;
				break;
		}
		
	break;
		
	case 'inserirParcela':
		require_once("bibliotecas/classes/EST019.php");
		
		$form=$r_est019FormPagamento;		
		$EST019 = new EST019();		
		$retorno = $EST019->inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;
	
	case 'calcularParcelaSimulacao':
		require_once("bibliotecas/classes/EST019.php");
		
		$form=$r_est019FormPagamento;		
		$EST019 = new EST019();		
		$retorno = $EST019->calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'gravarDesconto':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id=(isset($r_T293_Id)) ? $r_T293_Id : false;
		$T218_Valor_Desconto = (isset($r_T218_Valor_Desconto)) ? $r_T218_Valor_Desconto : false;
		$T218_Valor_Desconto_Confirmado = (isset($r_T218_Valor_Desconto_Confirmado)) ? $r_T218_Valor_Desconto_Confirmado : false;
		$valor = (isset($r_Valor)) ? $r_Valor : false;
		$EST019 = new EST019();		
		$retorno = $EST019->gravarDesconto($T293_Id, $T218_Valor_Desconto, $T218_Valor_Desconto_Confirmado, $valor);
		
		if ($retorno !== true) {			
			$resposta['code'] = false;
			$resposta['data'] = $retorno;			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
		
	case 'enviarParaAnalise':
		require_once("bibliotecas/classes/EST019.php");
		$EST019 = new EST019();
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		
		$retorno = $EST019->enviarParaAnalise($T293_Id,$alcadaUpdate);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Cotação de compra enviado para análise";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'est019ImprimirOrcamento':
		global $g;

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;

		// Arquivo
		$dir = $g['pathDados'] . 'tmp/';
		is_dir($dir) or mkdir($dir, 0777, true);
		$filename = "cotacao-{$T293_Id}.pdf";
		$file = $dir . $filename;
		$fileFull = $g['pathRaiz'] . '/' . $file;
		$retorno['Anexos'] = array($file);
		$retorno['AnexosWeb'] = array('/hardness3/' . $file);

		if (isset($r_gerarHtml)) {
			echo $EST019->montarEmailPdf($T293_Id);
			die();
		} else {
			$r_link = '/est/est019/form_func-ajax/est019ImprimirOrcamento/?ajax=true&gerarHtml=1&T293_Id='.$T293_Id.'&alcadaUpdate='.$alcadaUpdate;
			$r_link = str_replace(' ', '+', $r_link);
			//echo "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}\" \"{$fileFull}\"";
			//die();
			$cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
			shell_exec($cmd);
			log($cmd);
			
			$T220 = mysql_query("SELECT SUM(T220_Valor_Parcela) as TotalParcelas, T218_Valor_Total FROM T220 LEFT JOIN T218 ON T293_Id = T220_T293_Id WHERE T220_T293_Id='{$T293_Id}'");
			$resT220 = mysql_fetch_array($T220);

			$sql="SELECT D006_Flag_Gera_Contas FROM T218 LEFT JOIN D006 on D006_Id=T218_D006_Id WHERE T293_Id='{$T293_Id}'";
			$resultado=mysql_query($sql);
			$row=mysql_fetch_array($resultado);

			if($row['D006_Flag_Gera_Contas'] == "R"){
				if($resT220['TotalParcelas'] == 0){
					$erro = "É necessário preencher a forma de pagamento";
				} else {
					if($resT220['T218_Valor_Total'] != $resT220['TotalParcelas']){
						$erro = "Valor das parcelas não confere com o total do pedido.";
					}
				}
			}
			if(isset($erro)){
				$resposta['code'] = false;
				$resposta['data'] = $erro;
			} else {
				$resposta['code'] = true;
				$resposta['data'] = isset($retorno['AnexosWeb'][0]) ? $retorno['AnexosWeb'][0] : '';
			}
		}
	break;

	case 'est019DeletarPDFCotacao':
		global $g;
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$dir = $g['pathDados'] . 'tmp/';
		$filename             = "cotacao-{$T293_Id}.pdf";
		$fileFull             = $g['pathRaiz'] . '/' . $dir . $filename;
		if(file_exists($fileFull)){
			unlink($fileFull);
		}
	break;
	/*
	case 'carregaCFOP':
		
		global $g;
		
		$T218_D024_Id = isset($r_T218_D024_Id) ? $r_T218_D024_Id : false;
		$sql = "SELECT D024_D006_Id, D024_D018_Id FROM D024 WHERE D024_Id=" . $T218_D024_Id;
		$result = mysql_query($sql);
		$aD024 = mysql_fetch_assoc($result);
		
		$sql = "SELECT C004_CFOP_Venda_Dentro_Estado, C004_CFOP_Venda_Fora_Estado, C004_D018_Id FROM C004 WHERE C004_Id=" . $g['c004']['C004_Id'];
		$result = mysql_query($sql);
		$aC004 = mysql_fetch_assoc($result);
		
		$resposta['code'] = true;
		$D006_Id = $aD024['D024_D006_Id'];
		
		$array3 = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$D006_Id.' ORDER BY D006_Codigo_CFOP');
		
		if(!empty($array3)){
			$resposta['data']['T218_D006_Id'] = gProcessaSelect($array3);
			
		}else if($aD024['D024_D018_Id'] == $aC004['C004_D018_Id']){
			$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Dentro_Estado']);
			$resposta['data']['T218_D006_Id'] = gProcessaSelect($arrayDentroEstado);
			
		}else if($aD024['D024_D018_Id'] != $aC004['C004_D018_Id']){
			$arrayForaEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Fora_Estado']);
			$resposta['data']['T218_D006_Id'] = gProcessaSelect($arrayForaEstado);
		}else{
			$resposta['data']['T218_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' ORDER BY D006_Codigo_CFOP',true));
		}
		$resposta['data']['T218_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"WHERE D013_D024_Id='{$T218_D024_Id}'",true));	
		
		
	break;
	*/
	
	case 'atualizarCamposCliente':
		$D024_Id = isset($_REQUEST['D024_Id']) ? $_REQUEST['D024_Id'] : false;
		$sql = "SELECT * FROM D024 WHERE D024_Id = '{$D024_Id}';";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$resposta['code'] = true;
		$resposta['data'] = $row;
	break;
	
	case 'verificarParcelas':
		/*$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		
		$T220 = mysql_query("SELECT SUM(T220_Valor_Parcela) as TotalParcelas, T218_Valor_Total FROM T220 LEFT JOIN T218 ON T293_Id = T220_T293_Id WHERE T220_T293_Id='{$T293_Id}'");
		$resT220 = mysql_fetch_array($T220);
        
        $sql="SELECT D006_Flag_Gera_Contas FROM T218 LEFT JOIN D006 on D006_Id=T218_D006_Id WHERE T293_Id='{$T293_Id}'";
        $resultado=mysql_query($sql);
        $row=mysql_fetch_array($resultado);
    
        if($row['D006_Flag_Gera_Contas'] == "R"){
		    if($resT220['TotalParcelas'] == 0){
			    $erro = "É necessário preencher a forma de pagamento";
		    } else {
			    if($resT220['T218_Valor_Total'] != $resT220['TotalParcelas']){
				    $erro = "Valor das parcelas não confere com o total do orçamento.";
			    }
		    }
        }
		
		if(isset($erro)){
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		} else {
			$resposta['code'] = true;
		}*/	
        $resposta['code'] = true;
	break;

	case 'est019aplicarDescontoReais':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$DescontoReais = isset($r_DescontoReais) ? $r_DescontoReais : false;
		
		$EST019 = new EST019();
		
		$retorno = $EST019->est019aplicarDescontoReais($T293_Id, $DescontoReais);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'observacaoEntrega':
		require_once("bibliotecas/classes/EST019.php");
		
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T219_Quantidade = isset($r_T219_Quantidade) ? gCorrigeNumeroInverte($r_T219_Quantidade) : false;
		$T219_Codigo_Produto = isset($r_T219_Codigo_Produto) ? $r_T219_Codigo_Produto : false;
		
		$EST019 = new EST019();	
		$retorno = $EST019->observacaoEntrega($D009_Id, $T219_Quantidade, $T219_Codigo_Produto);		
		if ($retorno !== false) {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'est019aplicarDescontoTotal':
		require_once("bibliotecas/classes/EST019.php");
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$DescontoTotal = isset($r_DescontoValorTotal) ? $r_DescontoValorTotal : false;
		
		$EST019 = new EST019();
		
		$retorno = $EST019->est019aplicarDescontoTotal($T293_Id, $DescontoTotal);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Desconto aplicado com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCamposProdutosEditar':

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T293_Id = isset($_REQUEST['T293_Id']) ? $_REQUEST['T293_Id'] : false;

		$Aliquotas = $EST019->retornaAliquotas($D009_Id, $T293_Id, false);

		$T218=mysql_query("SELECT * FROM T218 
		                  LEFT JOIN D024 on D024_Id=T218_D024_Id
		                  WHERE T293_Id='{$T293_Id}'");
		$mT218=mysql_fetch_assoc($T218);


	    $D001=mysql_query("SELECT * FROM D001 
	                      LEFT JOIN D049 ON D001_Id=D049_D001_Id 
	                      LEFT JOIN D009 ON D049_Id=D009_D049_Id
	                      LEFT JOIN D082 ON D082_Id=D049_D082_Id
	                      left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$mT218['T218_D024_Id']}'
	                      WHERE D009_Id='{$D009_Id}'");
	    $mD001=mysql_fetch_assoc($D001);
	    $mD001['D009_Valor_Custo_Unitario'] = gCorrigeNumero($mD001['D009_Valor_Custo_Unitario']);

	    $row=array();

	    $row['D006_Id'] 					= $Aliquotas['D006_Id'];
	    $row['D005_Id'] 					= $mD001['D001_D005_Id'];
	    $row['D009_C004_Id'] 				= $mD001['D009_C004_Id'];
	    $row['Aliquota_IPI'] 				= gCorrigeNumero($Aliquotas['Aliquota_IPI']);
	    $row['Situacao_Tributaria_IPI'] 	= $Aliquotas['Situacao_Tributaria_IPI'];
	    $row['D082_Id'] 					= $Aliquotas['D082_Id'];
	    $row['D059_Id'] 					= $Aliquotas['D059_Id'];
	    $row['ST_ICMS_Interno'] 			= gCorrigeNumero($Aliquotas['ST_ICMS_Interno']);
	    $row['ST_ICMS_Interno_MVA'] 		= gCorrigeNumero($Aliquotas['ST_ICMS_Interno_MVA']);
	    $row['ICMS_Inter_Estadual'] 		= gCorrigeNumero($Aliquotas['ICMS_Inter_Estadual']);
	    $row['ICMS_Inter_Estadual_MVA'] 	= gCorrigenumero($Aliquotas['ST_ICMS_Inter_Estadual_MVA']);
	    $row['ST_VA'] 						= gCorrigeNumero($Aliquotas['ST_VA']);
	    $row['Percentual_PIS'] 				= gCorrigeNumero($Aliquotas['Aliquota_PIS']);
	    $row['Flag_Isento_PIS'] 			= $Aliquotas['Flag_Isento_PIS'];
	    $row['Situacao_Tributaria_PIS'] 	= $Aliquotas['Situacao_Tributaria_PIS'];
	    $row['Percentual_COFINS'] 			= $Aliquotas['Aliquota_COFINS'];
	    $row['Flag_Isento_COFINS'] 			= $Aliquotas['Flag_Isento_COFINS'];
	    $row['Situacao_Tributaria_COFINS'] 	= $Aliquotas['Situacao_Tributaria_COFINS'];
	    $row['Aliquota_ICMS_Correto'] 		= gCorrigeNumero($Aliquotas['Aliquota_ICMS']);
	    $row['Percentual_Reducao_ICMS_Correto'] 	= gCorrigeNumero($Aliquotas['Percentual_Reducao_ICMS']);
	    $row['Flag_ST'] 					= $Aliquotas['Flag_ST'];
	    //die("ST->".$row['Flag_ST']);

		/**
		 * DEFININDO QUAL O PREÇO QUE SERÁ RETORNADO
		 * 
		 */
		// verifica se está setado a flag lista preço do cabeçalho do orçamento, se estiver, manda ela, senão manda o id do cliente

		$sql =	"SELECT T218_D024_Id, T218_Flag_Lista_Preco, T218_Flag_Acp from T218 WHERE T293_Id = '{$T293_Id}'";
		$resultado = mysql_query($sql);
		$mT218 = mysql_fetch_assoc($resultado);

		if(!empty($mT218['T218_Flag_Lista_Preco'])){
			$row['Preco_Produto'] = $CAD002->retornaPrecoVenda($D009_Id, $mT218['T218_Flag_Lista_Preco'], $mT218['T218_D024_Id'], $mT218['T218_Flag_Acp']);
		}else{
			$row['Preco_Produto'] = $CAD002->retornaPrecoVenda($D009_Id, null, $mT218['T218_D024_Id'], $mT218['T218_Flag_Acp']);
		}

		// Método que irá retonar o último preço que o produto foi estdido para o cliente
		$row['ultimoPrecoVenda'] = $EST019->retornaltimoPrecoVenda($mT218['T218_D024_Id'], $D009_Id);
		$row2 = array_merge($row, $mD001);
		
		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row2;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'excluirItensMarcados':
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$sql = mysql_query("SELECT * FROM T219 WHERE T219_T293_Id = '{$T293_Id}' and T219_Flag_Excluir = 1");
		mysql_query("DELETE FROM T219 WHERE T219_T293_Id = '{$T293_Id}' and T219_Flag_Excluir = 1");
		if(mysql_num_rows($sql) > 0){
			$resposta['code'] = true;
			$resposta['data'] = "Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum item selecionado!";
		}
	break;

	case 'atualizarDadosServicos':
		global $g;
		$D112_Id = isset($_REQUEST['D112_Id']) ? $_REQUEST['D112_Id'] : false;
		$D112_Id = mysql_real_escape_string($D112_Id);

		$sql = "SELECT * FROM D112 LEFT JOIN D110 ON D112_D110_Id = D110_Id LEFT JOIN T212 ON T212_Id = D112_T212_Id WHERE D112_Id = '{$D112_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
		$teste = strtotime($row['D112_Tempo_Estimado']);
		$teste = date("H:i", $teste);
		$row['D112_Tempo_Estimado'] = $teste;

		if (!empty($row)) {
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'carregaTipoTransp':
        global $g;
        
        $D022_Id = isset($r_D022_Id) ? $r_D022_Id : false;
        $sql = "SELECT D022_D036_Id FROM D022 WHERE D022_Id=" . $D022_Id;
        $result = mysql_query($sql);
        $aD022 = mysql_fetch_assoc($result);
        
        $resposta['code'] = true;
        $resposta['data']['T218_D036_Id'] = $aD022['D022_D036_Id'];
    break;

    case 'atualizarCusto':
    	require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		global $g;
		$T293_Id = $r_T293_Id;
		$sql = mysql_query("SELECT * FROM T219 LEFT JOIN T218 ON T293_Id = T219_T293_Id WHERE T219_T293_Id = '{$T293_Id}' AND T219_Flag_Comprar = 'S'");
		if(mysql_num_rows($sql) > 0){
			while($row = mysql_fetch_array($sql)){
				$sqlT001 = mysql_query("SELECT T001_Id FROM T001 WHERE T001_T219_Id = '{$row['T219_Id']}'");
				$T219_Valor_ST_Unitario = $row['T219_Valor_ST'] / $row['T219_Quantidade'];
				if(mysql_num_rows($sqlT001) > 0){
					$rowT001 = mysql_fetch_array($sqlT001);
					mysql_query("UPDATE T001 SET T001_D024_Id = '{$row['T218_D024_Id']}',
												T001_D006_Id = '{$row['T219_D006_Id']}',
												T001_D009_Id = '{$row['T219_D009_Id']}',
												T001_C004_Id = '{$g['empresaAtual']}',
												T001_Codigo_Produto = '{$row['T219_Codigo_Produto']}',
												T001_Data_Lancamento = current_date(),
												T001_Descricao_Operacao = 'COT-{$T293_Id}',
												T001_Valor_Custo_Unitario = '{$row['T219_Valor_Custo_Unitario']}',
												T001_Valor_Preco_Unitario = '{$row['T219_Valor_Custo_Unitario']}',
												T001_Valor_ST_Unitario = '{$T219_Valor_ST_Unitario}',
												T001_Valor_ICMS = '{$row['T219_Valor_ICMS']}',
												T001_Aliquota_ICMS = '{$row['T219_Aliquota_ICMS']}',
												T001_Valor_IPI = '{$row['T219_Valor_IPI']}',
												T001_Aliquota_IPI = '{$row['T219_Aliquota_IPI']}',
												T001_Valor_ST = '{$row['T219_Valor_ST']}',
												T001_Valor_Frete = '{$row['T219_Valor_Frete_Total']}'
								WHERE T001_Id = '{$rowT001['T001_Id']}'");
				} else {
					mysql_query("INSERT INTO T001 (T001_D024_Id_Cliente,
													T001_D024_Id,
													T001_D006_Id,
													T001_T030_Id,
													T001_T029_Id,
													T001_D009_Id,
													T001_T066_Id,
													T001_C004_Id,
													T001_Codigo_Produto,
													T001_Data_Lancamento,
													T001_Descricao_Operacao,
													T001_Quantidade,
													T001_Flag_Complemento,
													T001_Flag_Operacao,
													T001_Valor_Preco_Unitario,
													T001_Observacao,
													T001_Valor_Custo_Unitario,
													T001_Flag_Atualiza_Custo,
													T001_Quantidade_Saldo,
													T001_Valor_ST_Unitario,
													T001_Valor_ICMS,
													T001_Aliquota_ICMS,
													T001_Valor_IPI,
													T001_Aliquota_IPI,
													T001_Valor_ST,
													T001_Valor_Frete,
													T001_T219_Id
												) VALUES (
								 					'0',
													'{$row['T218_D024_Id']}',
								 					'{$row['T219_D006_Id']}',
								 					'0',
								 					'0',
								 					'{$row['T219_D009_Id']}',
								 					'',
								 					'{$g['empresaAtual']}',
								 					'{$row['T219_Codigo_Produto']}',
								 					current_date(),
								 					'COT-{$T293_Id}',
								 					'0',
								 					'',
								 					'E',
								 					'{$row['T219_Valor_Custo_Unitario']}',
								 					'',
								 					'{$row['T219_Valor_Custo_Unitario']}',
								 					'S',
								 					'0',
								 					'{$T219_Valor_ST_Unitario}',
								 					'{$row['T219_Valor_ICMS']}',
								 					'{$row['T219_Aliquota_ICMS']}',
								 					'{$row['T219_Valor_IPI']}',
								 					'{$row['T219_Aliquota_IPI']}',
								 					'{$row['T219_Valor_ST']}',
								 					'{$row['T219_Valor_Frete_Total']}',
													'{$row['T219_Id']}'
								 				);");
				}
				$CAD002->D001_reprocessa_historico(false, $row['T219_D009_Id']);
			}
			$resposta['code'] = true;
			$resposta['data'] = "Custo(s) atualizado(s) com sucesso!";
		} else {
			$resposta['code'] = false;
			//$resposta['data'] = "ATENÇÃO!!! <BR /><BR />NENHUM PRODUTO FOI SELECIONADO!!!!";
			$resposta['data'] = "Nenhum produto foi selecionado.";
		}
    break;

    case 'atualizarCustoProduto':
    	global $g;
		$T293_Id = $r_T293_Id;

		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();
		//Verifica se há itens da cotação sem CFOP, NCM ou com origem em branco
        $retornoRestricao = $EST019->verificaDadosCotacao("T219_T293_Id = '{$T293_Id}' AND T219_Flag_Comprar = 'S'");
        if($retornoRestricao !== true){
            $resposta['code'] = false;
			$resposta['data'] = $retornoRestricao;
			break;
        }

		$sql = mysql_query("SELECT * FROM T219 LEFT JOIN D009 ON D009_Id=T219_D009_Id LEFT JOIN T218 ON T293_Id = T219_T293_Id WHERE T219_T293_Id = '{$T293_Id}' AND T219_Flag_Comprar = 'S'");
		$erro=false;
		if(mysql_num_rows($sql) > 0){
			while($row = mysql_fetch_array($sql)){
				if ($row['T219_D006_Id']<=0 || $row['T219_D005_Id']<=0 || !isset($row['T219_Flag_Origem_Produto']) || empty($row['T219_Valor_Custo_Unitario'])){
					$erro = true;
					break;
				}
				mysql_query("UPDATE T004 LEFT JOIN T219 ON T219_T004_Id = T004_Id SET T004_T219_Id = '{$row['T219_Id']}', T004_Flag_Comprar='N' WHERE T219_Id = '{$row['T219_Id']}'");

				$T219_Valor_ST      = $row['T219_Valor_ST'] / $row['T219_Quantidade'];
				$T219_Valor_ICMS    = $row['T219_Valor_ICMS'] / $row['T219_Quantidade'];
				$T219_Valor_IPI     = $row['T219_Valor_IPI'] / $row['T219_Quantidade'];
				$T219_Valor_Frete   = ($row['T219_Valor_Frete_Total']+$row['T219_Valor_Frete']) / $row['T219_Quantidade'];
				$T219_Percentual_ST = ($T219_Valor_ST/($row['T219_Valor_Preco_Unitario']+$T219_Valor_IPI))*100;

				$sqlT001 = mysql_query("SELECT T001_Id FROM T001 WHERE T001_T219_Id = '{$row['T219_Id']}'");
				$T066=mysql_query("SELECT T066_Id,D009_C004_Id FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id WHERE T066_D009_Id='{$row['T219_D009_Id']}' ORDER BY T066_Id DESC LIMIT 1;");
				$mT066=mysql_fetch_array($T066);

				if (mysql_num_rows($sqlT001)>0){ 
					$rowT001 = mysql_fetch_array($sqlT001);
					mysql_query("UPDATE T001 SET T001_D024_Id = '{$row['T218_D024_Id']}',
												 T001_D006_Id = '{$row['T219_D006_Id']}',
												 T001_D009_Id = '{$row['T219_D009_Id']}',
												 T001_C004_Id = '{$g['empresaAtual']}',
												 T001_Codigo_Produto = '{$row['T219_Codigo_Produto']}',
												 T001_Data_Lancamento = current_date(),
												 T001_Descricao_Operacao = 'COT-{$T293_Id}',
												 T001_Valor_Custo_Unitario = '{$row['T219_Valor_Custo_Unitario']}',
												 T001_Valor_Preco_Unitario = '{$row['T219_Valor_Preco_Unitario']}',
												 T001_Valor_ST_Unitario = '{$T219_Valor_ST}',
												 T001_Valor_ST = '{$T219_Valor_ST}',
												 T001_Percentual_ST = '{$T219_Percentual_ST}',
												 T001_Valor_ICMS = '{$T219_Valor_ICMS}',
												 T001_Aliquota_ICMS = '{$row['T219_Aliquota_ICMS']}',
												 T001_Valor_IPI = '{$T219_Valor_IPI}',
												 T001_Aliquota_IPI = '{$row['T219_Aliquota_IPI']}',
												 T001_T066_Id = '{$mT066['T066_Id']}',
												 T001_Valor_Frete = '{$T219_Valor_Frete}'
								WHERE T001_Id = '{$rowT001['T001_Id']}'");
				} else {
					mysql_query("INSERT INTO T001 (T001_D024_Id_Cliente,
													T001_D024_Id,
													T001_D006_Id,
													T001_T030_Id,
													T001_T029_Id,
													T001_D009_Id,
													T001_T066_Id,
													T001_C004_Id,
													T001_Codigo_Produto,
													T001_Data_Lancamento,
													T001_Descricao_Operacao,
													T001_Quantidade,
													T001_Flag_Complemento,
													T001_Flag_Operacao,
													T001_Valor_Preco_Unitario,
													T001_Observacao,
													T001_Valor_Custo_Unitario,
													T001_Flag_Atualiza_Custo,
													T001_Quantidade_Saldo,
													T001_Valor_ST_Unitario,
													T001_Valor_ICMS,
													T001_Aliquota_ICMS,
													T001_Valor_IPI,
													T001_Aliquota_IPI,
													T001_Valor_ST,
													T001_Percentual_ST,
													T001_Valor_Frete,
													T001_T219_Id
												) VALUES (
								 					'0',
													'{$row['T218_D024_Id']}',
								 					'{$row['T219_D006_Id']}',
								 					'0',
								 					'0',
								 					'{$row['T219_D009_Id']}',
													'{$mT066['T066_Id']}',
								 					'{$g['empresaAtual']}',
								 					'{$row['T219_Codigo_Produto']}',
								 					current_date(),
								 					'COT-{$T293_Id}',
								 					'0',
								 					'',
								 					'E',
								 					'{$row['T219_Valor_Preco_Unitario']}',
								 					'',
								 					'{$row['T219_Valor_Custo_Unitario']}',
								 					'S',
								 					'0',
								 					'{$T219_Valor_ST}',
								 					'{$T219_Valor_ICMS}',
								 					'{$row['T219_Aliquota_ICMS']}',
								 					'{$T219_Valor_IPI}',
								 					'{$row['T219_Aliquota_IPI']}',
								 					'{$T219_Valor_ST}',
								 					'{$T219_Percentual_ST}',
								 					'{$T219_Valor_Frete}',
													'{$row['T219_Id']}'
								 				);");
				}
				$CAD002->D001_reprocessa_historico(false, $row['T219_D009_Id']);
			}
			if($erro == true){
				$resposta['code'] = false;
				$resposta['data'] = "Existem itens na cotação de compra que estão com informações em branco, favor verificar a CFOP, NCM, origem do produto e o custo de compra! <br /><br />";
			} else {
				$resposta['code'] = true;
				$resposta['data'] = "Custo(s) atualizado(s) com sucesso!";	
			}
		} else {
			$resposta['code'] = false;
			//$resposta['data'] = "ATENÇÃO!!! <BR /><BR />NENHUM PRODUTO FOI SELECIONADO!!!!";
			$resposta['data'] = "Nenhum produto foi selecionado.";
		}
    break;

    case 'atualizarCustoVenda':
    	require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		require_once('bibliotecas/classes/VEN001.php');
		$VEN001 = new VEN001();
		require_once('bibliotecas/classes/VEN002.php');
		$VEN002 = new VEN002();
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		global $g;

		$T293_Id = $r_T293_Id;


		//Verifica se há itens da cotação sem CFOP, NCM ou com origem em branco
        $retornoRestricao = $EST019->verificaDadosCotacao("T219_T293_Id = '{$T293_Id}' AND T219_Flag_Comprar = 'S'");
        if($retornoRestricao !== true){
            $resposta['code'] = false;
			$resposta['data'] = $retornoRestricao;
			break;
        }


		mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
		//Atualizando Custo do produto no Orçamento
		$T003 = mysql_query("SELECT T003_Id, T004_Id
							FROM T004
							LEFT JOIN T006 ON T006_T004_Id=T004_Id
							LEFT JOIN D009 ON D009_Id=T004_D009_Id
							LEFT JOIN T003 ON T003_Id=T004_T003_Id
							LEFT JOIN T001 ON T001_Id=D009_T001_Id
							LEFT JOIN T219 ON T219_T004_Id=T004_Id
							LEFT JOIN T218 ON T293_Id=T219_T293_Id
							WHERE T219_Flag_Comprar = 'S'
							AND T293_Id='{$T293_Id}'
							GROUP BY T003_Id");


		while($mT003 = mysql_fetch_array($T003)){
			$retornoAliquotas = $VEN001->ven001atualizarCFOP($mT003['T003_Id'], $mT003['T004_Id']);
		}

		$T004 = mysql_query("SELECT D009_Valor_Custo_Unitario,
									D009_Valor_Custo_Compra,
									D009_T001_Id,
									D009_Id,
									T003_Flag_Lista_Preco,
									T003_D024_Id,
									T003_Flag_ACP,
									T004_D006_Id,
									T004_D005_Id,
									T004_Flag_Origem_Produto,
									T004_Aliquota_ICMS,
									D009_T001_Id,
									T004_Id,
									D009_Id,
									T219_Id,
									T003_Flag_Lista_Preco,
									T004_Aliquota_ICMS,
									T004_Aliquota_PIS,
									T004_Aliquota_COFINS,
									T004_Aliquota_IPI,
									T219_Valor_Custo_Unitario,
									T219_D006_Id,
									T219_D005_Id,
									T219_Flag_Origem_Produto,
									T004_ST_VA,
                                    T004_Percentual_Reducao_ICMS_ST,
                                    T004_ST_ICMS_Interno,
                                    T004_Percentual_Reducao_ICMS,
                                    T004_ICMS_Inter_Estadual,
                                    T004_Aliquota_IRPJ,
                                    T004_Aliquota_CSLL, 
                                    T004_Codigo_Produto,
									D049_Flag_Tipo
									FROM T004
									LEFT JOIN T006 ON T004_Id=T006_T004_Id
									LEFT JOIN T008 ON T006_Id=T008_T006_Id
									LEFT JOIN D009 ON D009_Id=T004_D009_Id
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
									LEFT JOIN T003 ON T003_Id=T004_T003_Id
									LEFT JOIN D024 ON D024_Id=T003_D024_Id
									LEFT JOIN T001 ON T001_Id=D009_T001_Id
									LEFT JOIN T219 ON T219_T004_Id=T004_Id
									LEFT JOIN T218 ON T293_Id=T219_T293_Id
									WHERE T219_Flag_Comprar = 'S'
									AND T008_Id IS NULL
									AND T293_Id='{$T293_Id}';");

		$count=0;
		$erroOrcamento='';
		$contProdutosProduzidos = 0;
		while($mT004 = mysql_fetch_assoc($T004)){
			if($mT006['D049_Flag_Tipo'] == 'PD'){
				$contProdutosProduzidos++;
				continue;
			}

			$count++;

			$precoCotacao      	  = $CAD002->cad002RetornaPrecoCotacao($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], true, $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $mT004['T219_Id'], false, 4, $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI']);
			$demonstrativoCotacao = $precoCotacao[1];
			$custoMedio 	   	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[2] : 0;
			$quantidadeEstoque 	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[3] : 0;
			$precoCotacao 	   	  = gCorrigeNumeroInverte($precoCotacao[0]);
			$demonstrativoCotacao = mysql_real_escape_string(serialize($demonstrativoCotacao));

			$dadosPrecoVenda    	 = $CAD002->retornaPrecoVenda($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], true, $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $mT004['T219_Id'], false, 4, $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI'], $mT004['T004_ST_VA'], $mT004['T004_Percentual_Reducao_ICMS_ST'], $mT004['T004_ST_ICMS_Interno'], $mT004['T004_Percentual_Reducao_ICMS'], $mT004['T004_ICMS_Inter_Estadual'], $mT004['T004_Aliquota_IRPJ'], $mT004['T004_Aliquota_CSLL']);
			$precoVendaProduto    	 = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
			$demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
			$custoMedio 	   	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[2] : 0;
			$quantidadeEstoque 	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[3] : 0;
			$custoProduto            = $dadosPrecoVenda[4];

			if ($mT004['T004_D006_Id']<=0){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} no orçamento de venda está faltando a CFOP<br />";
			}
			if ($mT004['T004_D005_Id']<=0){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} no orçamento de venda está faltando a NCM<br />";
			}
			if (!isset($mT004['T004_Flag_Origem_Produto'])){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} no orçamento de venda está faltando a Origem do produto<br />";
			}
			if ($mT004['T219_D006_Id']<=0){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} na cotação de compra está faltando a CFOP<br />";
			}
			if ($mT004['T219_D005_Id']<=0){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} na cotação de compra está faltando a NCM<br />";
			}
			if (!isset($mT004['T219_Flag_Origem_Produto'])){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} na cotação de compra está faltando a Origem do produto<br />";
			}
			if (empty($mT004['T219_Valor_Custo_Unitario'])){
				$erroOrcamento .= "O item {$mT004['T004_Codigo_Produto']} na cotação de compra está faltando o Custo do produto<br />";
			}

			mysql_query("UPDATE T004
			          LEFT JOIN D009 ON D009_Id=T004_D009_Id
						SET T004_Valor_Preco_Cotacao 	  = '{$precoCotacao}',
						T004_Valor_Preco_Original 	 	  = '{$precoVendaProduto}',
						T004_Valor_Custo_Unitario 		  = '{$custoProduto}',
						T004_Percentual_Desconto  		  = ((T004_Valor_Preco_Original-T004_Valor_Preco_Unitario)/T004_Valor_Preco_Original)*100,
						T004_Demonstrativo_Preco_Cotacao  = '{$demonstrativoCotacao}',
						T004_T001_Id              		  = '{$mT004['D009_T001_Id']}',
						T004_Flag_Origem_Produto  		  = '{$mT004['T219_Flag_Origem_Produto']}',
						T004_T219_Id 			 		  = '{$mT004['T219_Id']}',
						T004_Demonstrativo_Preco_Venda 	  = '{$demonstrativoPrecoVenda}',
						T004_Flag_Comprar                 = 'N'
						WHERE T004_Id = '{$mT004['T004_Id']}'");

			mysql_query("UPDATE T219
						LEFT JOIN T004 ON T004_T219_Id=T219_Id
						SET T219_Valor_Preco_Venda 		 = T004_Valor_Preco_Original,
						T219_Valor_Preco_Venda_Negociado = T004_Valor_Preco_Unitario,
						T219_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T219_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T219_Valor_Custo_Momento_Compra  = '{$mT004['D009_Valor_Custo_Compra']}'
						WHERE T004_Id = '{$mT004['T004_Id']}'");

			mysql_query("UPDATE T225
						LEFT JOIN T219 ON T219_Id=T225_T219_Id
						LEFT JOIN T004 ON T004_T219_Id=T219_Id
						SET T225_Valor_Preco_Venda 		 = T004_Valor_Preco_Original,
						T225_Valor_Preco_Venda_Negociado = T004_Valor_Preco_Unitario,
						T225_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T225_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T225_Valor_Custo_Momento_Compra  = '{$mT004['D009_Valor_Custo_Compra']}'
						WHERE T004_Id = '{$mT004['T004_Id']}'");
		}

	
/*	 	NÃO ATUALIZAR MAIS AS CFOPS E ALIQUOTAS DOS PEDIDOS NESTE MOMENTO
 
		//Atualizando Custo do produto no Pedido
		$T005 = mysql_query("SELECT T005_Id, T006_Id
							FROM T006
							LEFT JOIN D009 ON D009_Id=T006_D009_Id
							LEFT JOIN T005 ON T005_Id=T006_T005_Id
							LEFT JOIN T001 ON T001_Id=D009_T001_Id
							LEFT JOIN T219 ON T006_Id=T219_T006_Id
							LEFT JOIN T218 ON T293_Id=T219_T293_Id
							WHERE T219_Flag_Comprar = 'S'
							AND T293_Id='{$T293_Id}'
							GROUP BY T005_Id");


		while($mT005 = mysql_fetch_array($T005)){
			$VEN002->ven002atualizarCFOP($mT005['T005_Id'], $mT005['T006_Id']);
			$VEN002->atualizarAliquotasPedidos($mT005['T005_Id'], $mT005['T006_Id']);
		}
*/

		$T006 = mysql_query("SELECT D009_Valor_Custo_Unitario,
									D009_Valor_Custo_Compra,
									D009_T001_Id,
									D009_Id,
									T005_Flag_Lista_Preco,
									T005_D024_Id,
									T005_Flag_ACP,
									T006_D006_Id,
									T006_D005_Id,
									T006_Aliquota_ICMS,
									D009_T001_Id,
									T006_Id,
									D009_Id,
									T219_Id,
									T006_Aliquota_ICMS,
									T006_Aliquota_PIS,
									T006_Aliquota_COFINS,
									T006_Aliquota_IPI,
									T219_Valor_Custo_Unitario,
									T219_D006_Id,
									T219_D005_Id,
									T219_Flag_Origem_Produto,
									T006_Flag_Origem_Produto,
									T006_ST_VA,
                                    T006_Percentual_Reducao_ICMS_ST,
                                    T006_ST_ICMS_Interno,
                                    T006_Percentual_Reducao_ICMS,
                                    T006_ICMS_Inter_Estadual,
                                    T006_Aliquota_IRPJ,
                                    T006_Aliquota_CSLL,
                                    T005_Id,
                                    T006_Codigo_Produto,
									D049_Flag_Tipo,
									T006_Item
									FROM T006
									LEFT JOIN T008 ON T006_Id=T008_T006_Id
									LEFT JOIN D009 ON D009_Id=T006_D009_Id
									LEFT JOIN D049 ON D049_Id = D009_D049_Id
									LEFT JOIN T005 ON T005_Id=T006_T005_Id
									LEFT JOIN D024 ON D024_Id=T005_D024_Id
									LEFT JOIN T001 ON T001_Id=D009_T001_Id
									LEFT JOIN T219 ON T006_Id=T219_T006_Id OR T006_T006_Id_Dividir=T219_T006_Id
									LEFT JOIN T218 ON T293_Id=T219_T293_Id
									WHERE T219_Flag_Comprar = 'S'
									AND T293_Id='{$T293_Id}'
									AND T008_Id IS NULL
									AND IFNULL(T219_T006_Id,0) > 0;");
		$erroPedido='';
		$contProdutosProduzidos = 0;
		while($mT006 = mysql_fetch_array($T006)){			
			// Não atualiza custo de produtos produzidos
			if($mT006['D049_Flag_Tipo'] == 'PD'){
				$contProdutosProduzidos++;
				continue;
			}

			$count++;

			

			$precoCotacao      	  = $CAD002->cad002RetornaPrecoCotacao($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], true, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $mT006['T219_Id'], false, 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI']);
			$demonstrativoCotacao = $precoCotacao[1];
			$custoMedio 	   	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[2] : 0;
			$quantidadeEstoque 	  = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[3] : 0;
			$precoCotacao 	   	  = gCorrigeNumeroInverte($precoCotacao[0]);
			$demonstrativoCotacao = mysql_real_escape_string(serialize($demonstrativoCotacao));

			$dadosPrecoVenda    	 = $CAD002->retornaPrecoVenda($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], true, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $mT006['T219_Id'], false, 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI'], $mT006['T006_ST_VA'], $mT006['T006_Percentual_Reducao_ICMS_ST'], $mT006['T006_ST_ICMS_Interno'], $mT006['T006_Percentual_Reducao_ICMS'], $mT006['T006_ICMS_Inter_Estadual'], $mT006['T006_Aliquota_IRPJ'], $mT006['T006_Aliquota_CSLL']);
			$precoVendaProduto    	 = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
			$demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
			$custoMedio 	   	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[2] : 0;
			$quantidadeEstoque 	     = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[3] : 0;
			$custoProduto            = $dadosPrecoVenda[4];

			if ($mT006['T006_D006_Id']<=0) {
				$erroPedido .= "O item {$mT006['T006_Codigo_Produto']} ({$mT006['T006_Id']}) no pedido de venda {$mT006['T005_Id']} está faltando CFOP! ({$mT006['T006_D006_Id']})<br />";
			}
			if ($mT006['T006_D005_Id']<=0) {
				$erroPedido .= "O item {$mT006['T006_Codigo_Produto']} ({$mT006['T006_Id']}) no pedido de venda {$mT006['T005_Id']} está faltando NCM!<br />";
			}
			if (!isset($mT006['T006_Flag_Origem_Produto'])) {
				$erroPedido .= "O item {$mT006['T006_Codigo_Produto']} ({$mT006['T006_Id']}) no pedido de venda {$mT006['T005_Id']} está faltando origem do produto!<br />";
			}
			if ($mT006['T219_D006_Id']<=0 || $mT006['T219_D005_Id']<=0 || !isset($mT006['T219_Flag_Origem_Produto']) || $mT006['T219_Valor_Custo_Unitario']<=0) {
				//$erroPedido="EXISTEM ITENS NO PEDIDO DE VENDA OU NA COTAÇÃO DE COMPRA SEM CFOP, SEM NCM OU COM ORIGEM EM BRANCO, FAVOR VERIFICAR!!!<br /><br />";
				$erroPedido .= "O item {$mT006['T006_Codigo_Produto']} na cotação de compra {$mT006['T219_Id']} está faltando informações, favor verificar a CFOP, NCM, origem do produto e o custo de compra! <br />";
			}
			mysql_query("UPDATE T006 
						SET T006_Valor_Preco_Cotacao 	  = '{$precoCotacao}',
						T006_Valor_Preco_Original 		  = '{$precoVendaProduto}',
						T006_Valor_Preco_Original_Bruto   = '{$precoVendaProduto}',
						T006_Valor_Custo_Unitario		  = '{$custoProduto}',
						T006_Percentual_Desconto 		  = ((T006_Valor_Preco_Original-T006_Valor_Preco_Unitario)/T006_Valor_Preco_Original)*100,
						T006_Demonstrativo_Preco_Cotacao  = '{$demonstrativoCotacao}',
						T006_T001_Id              		  = '{$mT006['D009_T001_Id']}',
						T006_Flag_Origem_Produto  		  = '{$mT006['T219_Flag_Origem_Produto']}',
						T006_T219_Id 					  = '{$mT006['T219_Id']}',
						T006_Demonstrativo_Preco_Venda 	  = '{$demonstrativoPrecoVenda}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");

			mysql_query("UPDATE T219
						LEFT JOIN T006 ON T006_T219_Id=T219_Id
						SET T219_Valor_Preco_Venda 		 = T006_Valor_Preco_Original,
						T219_Valor_Preco_Venda_Negociado = T006_Valor_Preco_Unitario,
						T219_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T219_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T219_Valor_Custo_Momento_Compra  = '{$mT006['D009_Valor_Custo_Compra']}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");

			mysql_query("UPDATE T225
						LEFT JOIN T219 ON T219_Id=T225_T219_Id
						LEFT JOIN T006 ON T006_T219_Id=T219_Id
						SET T225_Valor_Preco_Venda 		 = T006_Valor_Preco_Original,
						T225_Valor_Preco_Venda_Negociado = T006_Valor_Preco_Unitario,
						T225_Valor_Custo_Medio_Unitario  = '{$custoMedio}',
						T225_Quantidade_Estoque 		 = '{$quantidadeEstoque}',
						T225_Valor_Custo_Momento_Compra  = '{$mT006['D009_Valor_Custo_Compra']}'
						WHERE T006_Id = '{$mT006['T006_Id']}'");
		}


		if ($erroOrcamento != '' || $erroPedido != ''){
			$resposta['code'] = false;
			$resposta['data'] = "ATENÇÃO!!! <BR /><BR />";
			$resposta['data'] .= $erroOrcamento;
			$resposta['data'] .= $erroPedido;
			mysql_query("ROLLBACK");
		} else {
			$erro = mysql_error();
			if(empty($erro) and ($count>0 || $contProdutosProduzidos>0)){
				$resposta['code'] = true;
				$resposta['data'] = $count . " Custo(s) atualizado(s) com sucesso!<br/>" . $contProdutosProduzidos . " Custo(s) não atualizado(s), pois o produto é do Tipo: \"Produzido\".";
				mysql_query("COMMIT");
			} else {
				$resposta['code'] = false;
				$resposta['data'] = "Nenhum produto foi selecionado ou os produtos não estão relacionados a uma venda.";
				//$resposta['data'] = "ATENÇÃO!!! <BR /><BR />NENHUM PRODUTO FOI SELECIONADO OU OS PRODUTOS NÃO ESTÃO RELACIONADOS A UMA VENDA, FAVOR VERIFICAR!!!";
				mysql_query("ROLLBACK");
			}
		}
		mysql_query("SET AUTOCOMMIT=1");
    break;

    case 'atualizarTabelaFornecedor':
    	require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		require_once('bibliotecas/classes/VEN001.php');
		$VEN001 = new VEN001();

		global $g;

		$T293_Id = $r_T293_Id;

		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();
		//Verifica se há itens da cotação sem CFOP, NCM ou com origem em branco
        $retornoRestricao = $EST019->verificaDadosCotacao("T219_T293_Id = '{$T293_Id}' AND T219_Flag_Comprar = 'S'");
        if($retornoRestricao !== true){
            $resposta['code'] = false;
			$resposta['data'] = $retornoRestricao;
			break;
        }
        
		mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
		//Atualizando itens da cotação relacionadas ao orçamento
		$T004 = mysql_query("SELECT * FROM T219
			LEFT JOIN T004 ON T219_T004_Id=T004_Id
			LEFT JOIN T006 ON T006_T004_Id=T004_Id 
			LEFT JOIN D009 ON D009_Id=T219_D009_Id 
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN T001 ON T001_Id=D009_T001_Id
			LEFT JOIN T218 ON T293_Id=T219_T293_Id
			LEFT JOIN D069 ON D069_D009_Id=D009_Id and D069_D070_Id=14
			WHERE T006_Id IS NULL AND T004_Id IS NOT NULL
			AND T219_Flag_Comprar = 'S'
			AND T293_Id='{$T293_Id}';");

		$Count=0;
		$erroOrcamento = '';
		while($mT004 = mysql_fetch_array($T004)){
			$Count++;
			mysql_query("UPDATE D069 SET 
				D069_Valor_Preco_Original       = '{$mT004['T219_Valor_Preco_Unitario']}',
				D069_Aliquota_ICMS              = '{$mT004['T219_Aliquota_ICMS']}',
				D069_Aliquota_IPI               = '{$mT004['T219_Aliquota_IPI']}',
				D069_ICMS_Inter_Estadual        = '{$mT004['T219_ICMS_Inter_Estadual']}',
				D069_ST_ICMS_Interno            = '{$mT004['T219_ST_ICMS_Interno']}',
				D069_ST_VA                      = '{$mT004['T219_ST_VA']}',
				D069_Percentual_Frete           = '{$mT004['T219_Percentual_Frete']}',
				D069_Percentual_Financeiro      = '{$mT004['T219_Percentual_Financeiro']}',
				D069_Flag_ST                    = '{$mT004['T219_Flag_ST']}'
				WHERE D069_Id='{$mT004['D009_D069_Id']}'");
			
			mysql_query("UPDATE T004 LEFT JOIN T219 ON T219_T004_Id = T004_Id SET T004_T219_Id = '{$mT004['T219_Id']}' WHERE T219_Id = '{$mT004['T219_Id']}'");

			$CAD002->D001_reprocessa_historico(false, $mT004['T004_D009_Id']);

			if ($mT004['T004_D006_Id']<=0 || $mT004['T004_D005_Id']<=0 || !isset($mT004['T004_Flag_Origem_Produto']) || $mT004['T219_D006_Id']<=0 || $mT004['T219_D005_Id']<=0 || !isset($mT004['T219_Flag_Origem_Produto']) || empty($mT004['T219_Valor_Custo_Unitario'])){
				//$erroOrcamento = "EXISTEM ITENS NO ORÇAMENTO DE VENDA OU NA COTAÇÃO DE COMPRA SEM CFOP, SEM NCM OU COM ORIGEM EM BRANCO, FAVOR VERIFICAR!!!<br /><br />";
				$erroOrcamento = "Existem itens no orçamento de venda ou na cotação de compra que estão com informações em branco, favor verificar a CFOP, NCM, origem do produto e o custo de compra! <br /><br />";
			}
		}




		//Atualizando itens da cotação relacionados a pedido
		$T006 = mysql_query("SELECT * FROM T219
			LEFT JOIN T006 ON T219_T006_Id=T006_Id OR T219_T006_Id=T006_T006_Id_Dividir
			LEFT JOIN D009 ON D009_Id=T219_D009_Id 
			LEFT JOIN T003 ON T003_Id=T006_T005_Id
			LEFT JOIN T001 ON T001_Id=D009_T001_Id
			LEFT JOIN T218 ON T293_Id=T219_T293_Id
			LEFT JOIN D069 ON D069_D009_Id=D009_Id and D069_D070_Id=14
			WHERE T219_Flag_Comprar = 'S'
			AND T293_Id='{$T293_Id}';");

		$erroPedido = '';
		while($mT006 = mysql_fetch_array($T006)){
			$Count++;
			mysql_query("UPDATE D069 SET 
				D069_Valor_Preco_Original       = '{$mT006['T219_Valor_Preco_Unitario']}',
				D069_Aliquota_ICMS              = '{$mT006['T219_Aliquota_ICMS']}',
				D069_Aliquota_IPI               = '{$mT006['T219_Aliquota_IPI']}',
				D069_ICMS_Inter_Estadual        = '{$mT006['T219_ICMS_Inter_Estadual']}',
				D069_ST_ICMS_Interno            = '{$mT006['T219_ST_ICMS_Interno']}',
				D069_ST_VA                      = '{$mT006['T219_ST_VA']}',
				D069_Percentual_Frete           = '{$mT006['T219_Percentual_Frete']}',
				D069_Percentual_Financeiro      = '{$mT006['T219_Percentual_Financeiro']}',
				D069_Flag_ST                    = '{$mT006['T219_Flag_ST']}'
				WHERE D069_Id='{$mT006['D009_D069_Id']}'");

			mysql_query("UPDATE T006 LEFT JOIN T219 ON T219_T006_Id = T006_Id OR T219_T006_Id = T006_T006_Id_Dividir SET T006_T219_Id = '{$mT006['T219_Id']}' WHERE T219_Id = '{$mT006['T219_Id']}'");

			$CAD002->D001_reprocessa_historico(false, $mT006['T006_D009_Id']);

			if ($mT006['T006_D006_Id']<=0 || $mT006['T006_D005_Id']<=0 || !isset($mT006['T006_Flag_Origem_Produto']) || $mT006['T219_D006_Id']<=0 || $mT006['T219_D005_Id']<=0 || !isset($mT006['T219_Flag_Origem_Produto']) || empty($mT006['T219_Valor_Custo_Unitario'])){
				//$erroPedido="EXISTEM ITENS NO PEDIDO DE VENDA OU NA COTAÇÃO DE COMPRA SEM CFOP, SEM NCM OU COM ORIGEM EM BRANCO, FAVOR VERIFICAR!!!<br /><br />";
				$erroPedido = "Existem itens no pedido de venda ou na cotação de compra que estão com informações em branco, favor verificar a CFOP, NCM, origem do produto e o custo de compra! <br /><br />";
			}
		}


		$erro = mysql_error();
		if(empty($erro) && $Count>0 && $erroOrcamento == '' && $erroPedido == ''){
			$resposta['code'] = true;
			$resposta['data'] = "Custo(s) atualizado(s) com sucesso!";
			mysql_query("COMMIT");
		} else if($Count <= 0) {
			$resposta['code'] = false;
			//$resposta['data'] = "ATENÇÃO!!! <BR /><BR />NENHUM PRODUTO FOI SELECIONADO!!!!";
			$resposta['data'] = "Nenhum produto foi selecionado.";
			mysql_query("ROLLBACK");
		} else if($erroOrcamento != '' || $erroPedido != ''){
			$resposta['code'] = false;
			$resposta['data'] = "ATENÇÃO!!! <BR /><BR />";
			$resposta['data'] .= $erroOrcamento;
			$resposta['data'] .= $erroPedido;
			mysql_query("ROLLBACK");
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao atualizar produtos: " . $erro;
			mysql_query("ROLLBACK");
		}
		mysql_query("SET AUTOCOMMIT=1");
	break;
	
    case 'orcamentoWhats':
		global $g;

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$phone = $r_phone;

		$T219 = mysql_query("select T219_Codigo_Produto, 
									T219_Descricao_Produto, 
									T219_Quantidade, 
									T219_Valor_Preco_Sem_Desconto_Unitario,
									T219_Valor_Preco_Bruto
							   from T219
							  where T219_T293_Id='{$T293_Id}'");

		$retorno = "Cotação N. " . $T293_Id . chr(10).chr(13);
		$retorno .= "---------------------------------" . chr(10);
		while ($mT219 = mysql_fetch_array($T219)){
			   $preco = ($g['C031']['precopadraoVendaProduto'] == 'bruto') ? $mT219['T219_Valor_Preco_Bruto'] : $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'];
			   $retorno .= 'Código: ' . $mT219['T219_Codigo_Produto'] . chr(10) .
						   'Descrição: ' . $mT219['T219_Descricao_Produto'] . chr(10) .
						   'Quantidade: ' . gCorrigeNumero($mT219['T219_Quantidade']) . chr(10);
			   $retorno .= "---------------------------------" . chr(10);
		}

		$T218 = mysql_query("select C007_Nome, T218_D013_Id, T218_D024_Id from T218 left join C007 on C007_Id=T218_C007_Id where T293_Id='$T293_Id'");
		$mT218 = mysql_fetch_array($T218);

		$assunto   = 'Cotação N. ' . $T293_Id;
		$de        = $mT218['C007_Nome'];
		$para      = 'Whatsapp: ' .$phone;

		mysql_query("INSERT INTO T139 ( T139_Pasta, 
										T139_Uid, 
										T139_Titulo, 
										T139_De, 
										T139_Para, 
										T139_Data_Hora, 
										T139_Estrutura, 
										T139_Mensagem_Corpo, 
										T139_Flag_Lido, 
										T139_D024_Id, 
										T139_D013_Id, 
										T139_T293_Id, 
										T139_C007_Id) 
								VALUES ('S', 
										0, 
										'".mysql_real_escape_string($assunto)."', 
										'".mysql_real_escape_string($de)."', 
										'".mysql_real_escape_string($para)."', 
										'".date('Y-m-d H:i:s')."', 
										'" . mysql_real_escape_string($retorno) . "', 
										'" . mysql_real_escape_string($retorno) . "', 
										'1', 
										'".mysql_real_escape_string($mT218['T218_D024_Id'])."', 
										'".mysql_real_escape_string($mT218['T218_D013_Id'])."', 
										'".mysql_real_escape_string($T293_Id)."', 
										'".mysql_real_escape_string($g['usuarioAtual'])."')");


		$resposta['code'] = true;
		$resposta['data'] = urlencode($retorno);


	break;

	case 'est019PhoneWhats':
		global $g;

		$T218=mysql_query("select T293_Id,T218_D013_Id, D024_DDD_Celular, D024_Celular, D013_DDD_Celular, D013_Celular from T218 left join D024 on D024_Id=T218_D024_Id left join D013 on D013_Id=T218_D013_Id where T293_Id='{$r_T293_Id}'");
		$mT218 = mysql_fetch_assoc($T218);

		$D013_Id=$mT218['T218_D013_Id'];

		$phoneWhatsApp = '';
		$notificaSemPhone = '';
		if (!empty($mT218['D013_Celular'])){
			$phoneWhatsApp = '+55'.trim($mT218['D013_DDD_Celular']).trim($mT218['D013_Celular']);		
		} else if (!empty($mT218['D024_Celular'])) {
			$phoneWhatsApp = '+55'.trim($mT218['D024_DDD_Celular']).trim($mT218['D024_Celular']);		
		} else {
			$notificaSemPhone = " ** Contato não possui celular cadastrado, atualize agora! ** ";
		}
		$phoneWhatsApp = str_replace('-','',$phoneWhatsApp);

		$resposta['phoneWhatsApp'] = urlencode($phoneWhatsApp);
		$resposta['notificaSemPhone'] = htmlspecialchars($notificaSemPhone);

	break;

	case 'orcamentoWhatsPDF':
		global $g;

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$alcadaUpdate = isset($r_alcadaUpdate) ? $r_alcadaUpdate : false;
		$moeda = isset($r_moeda) ? $r_moeda : 1;
		$phone = $r_phone;
		
		$T220 = mysql_query("SELECT SUM(T220_Valor_Parcela) as TotalParcelas, T218_Valor_Total FROM T220 LEFT JOIN T218 ON T293_Id = T220_T293_Id WHERE T220_T293_Id='{$T293_Id}'");
		$resT220 = mysql_fetch_array($T220);

		$sql="SELECT D006_Flag_Gera_Contas FROM T218 LEFT JOIN D006 on D006_Id=T218_D006_Id WHERE T293_Id='{$T293_Id}'";
		$resultado=mysql_query($sql);
		$row=mysql_fetch_array($resultado);

		if($row['D006_Flag_Gera_Contas'] == "R"){
			if($resT220['TotalParcelas'] == 0){
				$erro = "É necessário preencher a forma de pagamento";
			} else {
				if($resT220['T218_Valor_Total'] != $resT220['TotalParcelas']){
					$erro = "Valor das parcelas não confere com o total do pedido.";
				}
			}
		}

		if(isset($erro)){
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		} else {

			// Arquivo
			$dir = 'whatsapp/';
			is_dir($dir) OR mkdir($dir, 0777, true);
			
			$filename             = md5('T218'.$T293_Id.uniqid('',true)).".pdf";
			$file                 = $dir . $filename;
			$fileFull             = $g['pathRaiz'] . '/' . $file;
			$urlFile              = $g['urlRaiz'] . 'hardness3/' . $file;
			
			$r_link = '/est/est019/form_func-ajax/est019ImprimirOrcamento/?ajax=true&gerarHtml=1&T293_Id='.$T293_Id.'&alcadaUpdate='.$alcadaUpdate;
			$r_link = str_replace(' ', '+', $r_link);
			$cmd = "xvfb-run --server-num ".mt_rand(0,98)." -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"";
			shell_exec($cmd);
				
			$texto  = "Olá, segue link da nossa Cotação N. " . $T293_Id . chr(10);
			$texto .= $urlFile;

			$T218 = mysql_query("select C007_Nome, T218_D013_Id, T218_D024_Id from T218 left join C007 on C007_Id=T218_C007_Id where T293_Id='$T293_Id'");
			$mT218 = mysql_fetch_array($T218);

			$assunto   = 'Cotação N. ' . $T293_Id;
			$de        = $mT218['C007_Nome'];
			$para      = 'Whatsapp: ' .$phone;

			mysql_query("INSERT INTO T139 ( T139_Pasta, 
											T139_Uid, 
											T139_Titulo, 
											T139_De, 
											T139_Para, 
											T139_Data_Hora, 
											T139_Estrutura, 
											T139_Mensagem_Corpo, 
											T139_Flag_Lido, 
											T139_D024_Id, 
											T139_D013_Id, 
											T139_T293_Id, 
											T139_C007_Id) 
									VALUES ('S', 
											0, 
											'".mysql_real_escape_string($assunto)."', 
											'".mysql_real_escape_string($de)."', 
											'".mysql_real_escape_string($para)."', 
											'".date('Y-m-d H:i:s')."', 
											'" . mysql_real_escape_string($texto) . "', 
											'" . mysql_real_escape_string($texto) . "', 
											'1', 
											'".mysql_real_escape_string($mT218['T218_D024_Id'])."', 
											'".mysql_real_escape_string($mT218['T218_D013_Id'])."', 
											'".mysql_real_escape_string($T293_Id)."', 
											'".mysql_real_escape_string($g['usuarioAtual'])."')");

			$resposta['code'] = true;
			$resposta['data'] = urlencode($texto);
		}
	break;	

}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
