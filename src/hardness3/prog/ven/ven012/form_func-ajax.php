<?php
namespace hardness;
  /**
 * Weiberlan
 * 25/11/2011
 * VEN - VEN001 - Cadastro de Orçamentos
 *
 * Processamento de ações AJAX
 */
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'dividirPedido':
		require_once("bibliotecas/classes/VEN012.php");
		log("entrou no ajax dividirPedido");
		$T005_Id_Antigo = isset($r_AntigoT005_Id) ? $r_AntigoT005_Id : false;
		$C004_Id_Novo = isset($r_C004_Id) ? $r_C004_Id : false;
		$r_sql = base64_decode($r_extra1);
		log("C004_Id_Novo= ".$C004_Id_Novo);
		log("T005_Id_Antigo no ajax dividir pedido: ".$T005_Id_Antigo);
		
		$VEN012 = new VEN012();
		
		$retorno = $VEN012->est012DividirPedidoEmpresa($T005_Id_Antigo, $C004_Id_Novo, $r_sql);
		if (is_array($retorno)) {
			log("entra aqui");
			$resposta['code'] = true;
			$resposta['novoT005_Id'] = $retorno['T005_Id'];
			log("novo T005_Id no ajax: ".$retorno['T005_Id']);
		} else {
			log("entra aqui1");
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'reprocessaHistoricoDividirPedido':
		log("entrou no ajax reprocessaHistorico");
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $T005_Id_Novo = isset($r_T005_Id_Novo) ? $r_T005_Id_Novo : false;
        $resposta['novoT005_Id'] = $T005_Id_Novo;
        $T005_Id_Antigo = isset($r_AntigoT005_Id) ? $r_AntigoT005_Id : false;
        log("T005_Id antigo no reprocessa historico= ".$T005_Id_Antigo);
		log("T005_Id_Novo no reprocessa historico: ".$T005_Id_Novo);
		
		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT006 = "{$Dir}cacheT006-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		$resposta['code'] = true;
		if ($r_inicio==1){
	        $CAD002 = new CAD002();
	        log("T005_Id_antigo no ajax de reprocessa= ".$T005_Id_Antigo);

	        $sqlT005 = "SELECT T006_D009_Id, 
	                           D009_Id, 
	                           D001_Id, 
	                           T006_Id
						  FROM T005 
					 LEFT JOIN T006 ON T006_T005_Id= T005_Id 
					 LEFT JOIN D009 ON T006_D009_Id= D009_Id 
					 LEFT JOIN D049 ON D009_D049_Id = D049_Id
					 LEFT JOIN D001 ON D001_Id = D049_D001_Id
					     WHERE T005_Id='{$T005_Id_Antigo}'"; 

			$resultT005 = mysql_query($sqlT005);					

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($resultT005)){
	        	$cache[$count]['D001_Id']= $rowCache['D001_Id'];
	        	$cache[$count]['T006_Id']= $rowCache['T006_Id'];
	        	log("%%D001_Id no ajax reprocessa: ".$rowCache['D001_Id']);
	        	log("%%T006_Id no ajax reprocessa: ".$rowCache['T006_Id']);
				$count++;
				log("count= ".$count);
			}

			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
			$resposta['concluido'] = 0;

			apc_delete("cacheT006-{$g['usuarioAtual']}");
			apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT006-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT006, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $cache = file_exists($cacheT006);
			//--------------------------------------------------------------------------
			$cache = unserialize(apc_fetch("cacheT006-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT006-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar variável no cache resposta';
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

	   	$ok = 0;
		$erro = 0;
		$erros ="";

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	log("T006_Id no reprocessaHistorico= ".$cache[$i]['T006_Id']);
	    	log("D001_Id no reprocessaHistorico= ".$cache[$i]['D001_Id']);
	    	$retorno = $CAD002->D001_reprocessa_historico($cache[$i]['D001_Id']);
	    	mysql_query("call T006_Gravar_Totalizacao_4('{$linhaT006['T006_Id']}')");          
           	if($retorno !== true){
           		$resposta['code'] = false;
       	     	$erro++;
           	 	$erros .= $retorno."<br />";
       		}else{
       			$ok++;
       		}
        }
   	
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			//Se for usar o arquivo do disco, usr o unlink ao invés do apc_delete na variável cache T006
			// unlink("{$Dir}cacheT006-{$g['usuarioAtual']}.txt");
			//----------------------------------------------------------------------------------------------
			apc_delete("cacheT006-{$g['usuarioAtual']}");
			//----------------------------------------------------------------------------------------------
			apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT006-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

    case 'TotalizarDividirPedido':
		require_once("bibliotecas/classes/VEN012.php");
		log("entrou no ajax TotalizarDividirPedido");
		$T005_Id_Antigo = isset($r_AntigoT005_Id) ? $r_AntigoT005_Id : false;
		$T005_Id_Novo = isset($r_T005_Id_Novo) ? $r_T005_Id_Novo : false;
		log("T005_Id_Antigo no ajax totalizardividirpedido: ".$T005_Id_Antigo);
		log("T005_Id_Novo no ajax totalizardividirpedido: ".$T005_Id_Novo);
		mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id_Novo}')");
		$erroSQL    = mysql_error();
        if (!empty($erroSQL)) {
        	$resposta['code'] = false;
			$resposta['data'] =  'Erro1: ' . $erroSQL;
        }
        mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id_Antigo}')");
        $erroSQL    = mysql_error();
        if (!empty($erroSQL)) {
        	$resposta['code'] = false;
		 	$resposta['data'] =  'Erro2: ' . $erroSQL;
        }
        $resposta['code'] = true;		
	break;


	case 'gerarNotaFiscal':
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();
		$form = $r_ven012FormPedido;	
		
		$retorno = $VEN012->gerarNotaFiscal($form);
		log("retorno: ".$retorno);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else if(is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$resposta['retornarPedidoSeparacao'] = true;
		}else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'gerarNotaFiscalBoxLeitor':
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();
		$T005_Id = $r_T005_Id;

		$retorno = $VEN012->gerarNotaFiscalBoxLeitor($T005_Id);

		if ($retorno['sucesso']) {
			$resposta['code'] = true;
			$resposta['T007_Id'] = $retorno['T007_Id'];

			$sqlT007 = "select T007_Numero_Nota_Fiscal from T007 where T007_Id = {$retorno['T007_Id']}";
			$qryT007 = mysql_query($sqlT007);
			$rowT007 = mysql_fetch_array($qryT007);

			$resposta['T007_Numero_Nota_Fiscal'] = $rowT007['T007_Numero_Nota_Fiscal'];
		}else {
			log("Falha na geração da NF: " . $retorno['mensagem']);
			$resposta['code'] = false;
			$resposta['data'] = $retorno['mensagem'];
			// $resposta['retornarPedidoSeparacao'] = true;
		}
	break;

	case 'gerarNFValidacaoFormulario':
		log("%% entra no AJAX gerarNFValidacaoFormulario");
		require_once("bibliotecas/classes/VEN012.php");
		$form=$r_ven012FormPedido;	
		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";
		$VEN012 = new VEN012();
		$retorno = $VEN012->gerarNFValidacaoFormulario($form);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
				log("resposta do roolback na validação: ".$resposta['erroRoolback']);
			}
		}
	break;

	case 'gerarNFValidacaoFormularioBoxLeitor':
		log("%% entra no AJAX gerarNFValidacaoFormularioBoxLeitor T005_Id: {$r_T005_Id}");

		$form = array('T005_Id' => $r_T005_Id);

		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";

		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();
		$retorno = $VEN012->gerarNFValidacaoFormulario($form);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
				log("resposta do roolback na validação: ".$resposta['erroRoolback']);
			}
		}
	break;

	case 'transmitirNFBoxLeitor':

		global $g;

		$T005_Id = $r_T005_Id;
		$T007_Id = $r_T007_Id;
		$C004_Id = $g['empresaAtual'];

		$sql = " select T005_Id_Pedido_Externo from T005 where T005_Id = {$T005_Id}";
		$query = mysql_query($sql);
		$row = mysql_fetch_array($query);

		$T005_Id_Pedido_Externo = $row['T005_Id_Pedido_Externo'];

		require_once('bibliotecas/classes/SMTNFE.php');
		$SMTNFE = new SMTNFE();
	
		$nota = array(
		  'T007_Id'                => $T007_Id,
		  'C004_Id'                => $C004_Id,
		  'T005_Id'                => $T005_Id,
		  'T005_Id_Pedido_Externo' => $T005_Id_Pedido_Externo
		);

		$retTransmissao = $SMTNFE->emitirNFe($nota);

		$resposta['sucesso']        = $retTransmissao['nfAprovada'];
		$resposta['retornoTray']    = $retTransmissao['retornoTray'];
		$resposta['xmlTransp']      = $retTransmissao['xmlTransp'];
		$resposta['logTransmissao'] = $retTransmissao['mensagem'];
	break;

	case 'gerarNFTotalizarPedido':
		global $g;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();

		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT005 = "{$Dir}cacheT005-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		/*inicio calculo etapas*/
        if ($r_inicio==1){
        	//mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}')");
			$sqlCache = mysql_query("SELECT T006_Id FROM T006 WHERE T006_T005_Id='{$T005_Id}'");
        	$count = 0;
        	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['T006_Id'] = $rowCache['T006_Id'];	
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
			$resposta['concluido'] = 0;
			//apc_delete("cacheT005-{$g['usuarioAtual']}");
			//apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT005, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT005);
			//--------------------------------------------------------------------------
			//Se for usar o cache, descomenta a linha abaixo e comenta a parte de cima--
			//$retorno = apc_store("cacheT005-{$g['usuarioAtual']}", serialize($cache));
			$retorno = file_put_contents("/tmp/cacheT005-{$g['usuarioAtual']}", serialize($cache));

			//--------------------------------------------------------------------------
	        if(!$retorno){
	        	$resposta['concluido'] = 1;
	        	$resposta['code'] = false;
				$resposta['data'] = "Erro ao criar variável no cache";
				//apc_delete("cacheT005-{$g['usuarioAtual']}");
				//apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
				@unlink("/tmp/cacheT005-{$g['usuarioAtual']}");
        		@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");
	        	$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
	        }
	    } else {
			// Recuperando dados do disco------------------------------------------------------
			// $cache = unserialize(file_get_contents($cacheT005));
			// if(!is_array($cache)){
	        // 	$resposta['code'] = false;
	        // 	$resposta['data'] = 'Erro ao recuperar arquivo com o cache T005.';
	        // 	$resposta['concluido'] = 1;
	        // 	break;
			// }
			//---------------------------------------------------------------------------------
			//$cache = unserialize(apc_fetch("cacheT005-{$g['usuarioAtual']}"));
			$cache = unserialize(file_get_contents("/tmp/cacheT005-{$g['usuarioAtual']}"));			
			// --------------------------------------------------------------------------------
			//$resposta = unserialize(apc_fetch("cacheT005-resposta-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheT005-resposta-{$g['usuarioAtual']}"));

	        if(!$resposta){
	        	$resposta['concluido'] = 1;
	        	$resposta['code'] = false;
				$resposta['data'] = "Erro ao recuperar variável cacheT005-resposta-{$g['usuarioAtual']}";
				apc_delete("cacheT005-{$g['usuarioAtual']}");
				apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
	       		$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
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

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$T006_Id = $cache[$i]['T006_Id'];
	    	//mysql_query("call T006_Gravar_Totalizacao_4({$T006_Id})");
	    	$cErro = mysql_error();
	        if (!empty($cErro)) {
	        	$resposta['concluido'] = 1;
	            $retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
	            if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
				$resposta['code'] = false;
				$resposta['data'] = "ERRO01:". $cErro;
	        }
            
        }

		if($resposta['etapa'] >= $resposta['etapas']){
			//mysql_query("call T005_Gravar_Totalizacao_4('{$T005_Id}')");
			// Se for usar o arquivo de disco, usar o unlink, senão usar o apc_delete --------
			// unlink("{$Dir}cacheT005-{$g['usuarioAtual']}.txt");
			//apc_delete("cacheT005-{$g['usuarioAtual']}");
			// -------------------------------------------------------------------------------
			//apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");

			$resposta['concluido'] = 1;
		} else {
	        //apc_delete("cacheT005-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT005-resposta-{$g['usuarioAtual']}");

	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        //apc_store("cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));
			file_put_contents("/tmp/cacheT005-resposta-{$g['usuarioAtual']}", serialize($resposta));

		}	 
	break;

	case 'infoCache':
		echo print_r(apc_cache_info());
	break;

	case 'gerarNFMontarCabecalho':
		log("%%entra no ajax gerarNFMontarCabecalho");
		global $g;
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();
		$form = $r_ven012FormPedido;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;	
		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";

		$retorno = $VEN012->gerarNFMontarCabecalho($form);

		if ($retorno === true) {
			$mT007 = mysql_fetch_array(mysql_query("SELECT T007_Id FROM T007 WHERE T007_T005_Id='{$T005_Id}'"));
			$resposta['T007_Id'] = $mT007['T007_Id'];
			$resposta['code'] = true;
		} else if(is_array($retorno)){ //se for um erro de separação
			$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
			}
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$resposta['retornarPedidoSeparacao'] = true;
		}else { 
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
			}
		}
	break;

	case 'gerarNFIncluirItens':
		log("%%entra no ajax gerarNFIncluirItens");
		global $g;
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";

		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT008 = "{$Dir}cacheT008-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		/*inicio calculo etapas*/
        if ($r_inicio==1){
        	log("entrou na 1 etapa");
	        
	        $sql = "SELECT D001_Codigo_Barras,
	                       D001_Codigo_Barras_Tributavel,
	                       D001_D003_Id,
	                       D001_D037_Id,
	                       D006_Codigo_Beneficio_Fiscal,
	                       D006_Codigo_CFOP,
	                       D009_Valor_Custo_Compra,
	                       D009_Valor_Total_Preco,
	                       D037_Unidade,
	                       D049_Codigo_Barras,
	                       D049_Flag_Nacional_Importado,
	                       T006_Aliquota_COFINS,
	                       T006_Aliquota_CSLL,
	                       T006_Aliquota_ICMS,
	                       T006_Aliquota_ICMS_Credito,
	                       T006_Aliquota_ICMS_Oculto,
	                       T006_Aliquota_IPI,
	                       T006_Aliquota_IRPJ,
	                       T006_Aliquota_PIS,
	                       T006_C004_Id,
	                       T006_Classificacao_Fiscal,
	                       T006_Codigo_Fornecedor,
	                       T006_Codigo_Produto,
	                       T006_Codigo_Substituto,
	                       T006_D005_Id,
	                       T006_D005_Id_Info,
	                       T006_D006_Id,
	                       T006_D006_Id_Info,
	                       T006_D009_Id,
	                       T006_D037_Id,
	                       T006_D040_Id,
	                       T006_D059_Id,
	                       T006_Descricao_Produto,
	                       T006_Flag_Destino_Produto,
	                       T006_Flag_Isento_COFINS,
	                       T006_Flag_Isento_CSLL,
	                       T006_Flag_Isento_IRPJ,
	                       T006_Flag_Isento_PIS,
	                       T006_Flag_Preco_Especial,
	                       T006_Flag_Situacao_Tributaria,
	                       T006_Flag_ST,
	                       T006_ICMS_Inter_Estadual,
	                       T006_Id,
	                       T006_Item_Pedido_Compra,
	                       T006_Modalidade_ICMS,
	                       T006_Modalidade_ICMS_Substituicao,
	                       T006_Motivo_Desoneracao_ICMS,
	                       T006_Numero_Pedido_Compra,
	                       T006_Percentual_Base_Operacao_Propria,
	                       T006_Percentual_Comissao,
	                       T006_Percentual_Desconto,
	                       T006_Percentual_Reducao_ICMS,
	                       T006_Percentual_Reducao_ICMS_Oculto,
	                       T006_Percentual_Reducao_ICMS_ST,
	                       T006_Percentual_Trib_Estadual,
	                       T006_Percentual_Trib_Federal,
	                       T006_Percentual_Trib_Municipal,
	                       T006_Percentual_Trib_Transparencia,
	                       T006_Peso_Bruto,
	                       T006_Peso_Unitario,
	                       T006_Quantidade,
	                       T006_Situacao_Tributaria_COFINS,
	                       T006_Situacao_Tributaria_IPI,
	                       T006_Situacao_Tributaria_PIS,
	                       T006_ST_ICMS_Interno,
	                       T006_ST_VA,
	                       T006_T008_Id,
	                       T006_T014_Id,
	                       T006_UF_ICMS_Substituicao_Devido,
	                       T006_Valor_Base_COFINS,
	                       T006_Valor_Base_CSLL,
	                       T006_Valor_Base_ICMS,
	                       T006_Valor_Base_ICMS_Oculto,
	                       T006_Valor_Base_ICMS_Substituicao_Destino,
	                       T006_Valor_Base_ICMS_Substituicao_Retido,
	                       T006_Valor_Base_IPI,
	                       T006_Valor_Base_IRPJ,
	                       T006_Valor_Base_PIS,
	                       T006_Valor_COFINS,
	                       T006_Valor_Comissao,
	                       T006_Valor_CSLL,
	                       T006_Valor_Custo_Unitario,
	                       T006_Valor_Desconto_Unitario,
	                       T006_Valor_Despesas_Acessorias_Unitario,
	                       T006_Valor_Frete_Unitario,
	                       T006_Valor_ICMS,
	                       T006_Valor_ICMS_Credito,
	                       T006_Valor_ICMS_Desonerado,
	                       T006_Valor_ICMS_Oculto,
	                       T006_Valor_ICMS_Substituicao_Destino,
	                       T006_Valor_ICMS_Substituicao_Retencao,
	                       T006_Valor_ICMS_Substituicao_Retido,
	                       T006_Valor_IPI,
	                       T006_Valor_IRPJ,
	                       T006_Valor_PIS,
	                       T006_Valor_Preco_Original,
	                       T006_Valor_Preco_Sem_Desconto_Unitario,
	                       T006_Valor_Preco_Unitario,
	                       T006_Valor_Trib_Transparencia,
	                       D009_Custo_Unitario(D009_Id) Custo_Unitario,
	                       (D009_Custo_Unitario(D009_Id)*T006_Quantidade) Custo_Total,
	                       D009_Local(D009_Id,1) Local_1,
						   T006_Preco_Pauta_ST
	                  FROM T006
	             LEFT JOIN D037 ON D037_Id=T006_D037_Id
	             LEFT JOIN D009 ON D009_Id=T006_D009_Id
	             LEFT JOIN D049 ON D049_Id=D009_D049_Id
	             LEFT JOIN D001 ON D001_Id=D049_D001_Id
	             LEFT JOIN D006 ON D006_Id=T006_D006_Id
	                 WHERE T006_T005_Id='{$T005_Id}'
	                   AND T006_Quantidade>0
	                   AND T006_Flag_Status!=3
	              ORDER BY T006_Item";
	        $sqlCache = mysql_query($sql);
	        log("SQL do T006: ".$sql);

        	$count = 0;
        	while($rowCache = mysql_fetch_array($sqlCache)){
        		$cache[$count]['D001_Codigo_Barras'] = $rowCache['D001_Codigo_Barras'];	
			    $cache[$count]['D001_Codigo_Barras_Tributavel'] = $rowCache['D001_Codigo_Barras_Tributavel'];	
			    $cache[$count]['D001_D003_Id'] = $rowCache['D001_D003_Id'];		
			    $cache[$count]['D001_D037_Id'] = $rowCache['D001_D037_Id'];		
			    $cache[$count]['D006_Codigo_Beneficio_Fiscal'] = $rowCache['D006_Codigo_Beneficio_Fiscal'];		
			    $cache[$count]['D006_Codigo_CFOP'] = $rowCache['D006_Codigo_CFOP'];		
			    $cache[$count]['D009_Valor_Custo_Compra'] = $rowCache['D009_Valor_Custo_Compra'];		
			    $cache[$count]['D009_Valor_Total_Preco'] = $rowCache['D009_Valor_Total_Preco'];		
			    $cache[$count]['D037_Unidade'] = $rowCache['D037_Unidade'];		
			    $cache[$count]['D049_Codigo_Barras'] = $rowCache['D049_Codigo_Barras'];		
			    $cache[$count]['D049_Flag_Nacional_Importado'] = $rowCache['D049_Flag_Nacional_Importado'];		
			    $cache[$count]['T006_Aliquota_COFINS'] = $rowCache['T006_Aliquota_COFINS'];		
			    $cache[$count]['T006_Aliquota_CSLL'] = $rowCache['T006_Aliquota_CSLL'];		
			    $cache[$count]['T006_Aliquota_ICMS'] = $rowCache['T006_Aliquota_ICMS'];		
			    $cache[$count]['T006_Aliquota_ICMS_Credito'] = $rowCache['T006_Aliquota_ICMS_Credito'];		
			    $cache[$count]['T006_Aliquota_ICMS_Oculto'] = $rowCache['T006_Aliquota_ICMS_Oculto'];		
			    $cache[$count]['T006_Aliquota_IPI'] = $rowCache['T006_Aliquota_IPI'];		
			    $cache[$count]['T006_Aliquota_IRPJ'] = $rowCache['T006_Aliquota_IRPJ'];		
			    $cache[$count]['T006_Aliquota_PIS'] = $rowCache['T006_Aliquota_PIS'];		
			    $cache[$count]['T006_C004_Id'] = $rowCache['T006_C004_Id'];		
			    $cache[$count]['T006_Classificacao_Fiscal'] = $rowCache['T006_Classificacao_Fiscal'];		
			    $cache[$count]['T006_Codigo_Fornecedor'] = $rowCache['T006_Codigo_Fornecedor'];		
			    $cache[$count]['T006_Codigo_Produto'] = $rowCache['T006_Codigo_Produto'];		
			    $cache[$count]['T006_Codigo_Substituto'] = $rowCache['T006_Codigo_Substituto'];		
			    $cache[$count]['T006_D005_Id'] = $rowCache['T006_D005_Id'];		
			    $cache[$count]['T006_D005_Id_Info'] = $rowCache['T006_D005_Id_Info'];		
			    $cache[$count]['T006_D006_Id'] = $rowCache['T006_D006_Id'];		
			    $cache[$count]['T006_D006_Id_Info'] = $rowCache['T006_D006_Id_Info'];		
			    $cache[$count]['T006_D009_Id'] = $rowCache['T006_D009_Id'];		
			    $cache[$count]['T006_D037_Id'] = $rowCache['T006_D037_Id'];		
			    $cache[$count]['T006_D040_Id'] = $rowCache['T006_D040_Id'];		
			    $cache[$count]['T006_D059_Id'] = $rowCache['T006_D059_Id'];		
			    $cache[$count]['T006_Descricao_Produto'] = $rowCache['T006_Descricao_Produto'];		
			    $cache[$count]['T006_Flag_Destino_Produto'] = $rowCache['T006_Flag_Destino_Produto'];		
			    $cache[$count]['T006_Flag_Isento_COFINS'] = $rowCache['T006_Flag_Isento_COFINS'];		
			    $cache[$count]['T006_Flag_Isento_CSLL'] = $rowCache['T006_Flag_Isento_CSLL'];		
			    $cache[$count]['T006_Flag_Isento_IRPJ'] = $rowCache['T006_Flag_Isento_IRPJ'];		
			    $cache[$count]['T006_Flag_Isento_PIS'] = $rowCache['T006_Flag_Isento_PIS'];		
			    $cache[$count]['T006_Flag_Preco_Especial'] = $rowCache['T006_Flag_Preco_Especial'];		
			    $cache[$count]['T006_Flag_Situacao_Tributaria'] = $rowCache['T006_Flag_Situacao_Tributaria'];		
			    $cache[$count]['T006_Flag_ST'] = $rowCache['T006_Flag_ST'];		
			    $cache[$count]['T006_ICMS_Inter_Estadual'] = $rowCache['T006_ICMS_Inter_Estadual'];		
			    $cache[$count]['T006_Id'] = $rowCache['T006_Id'];		
			    $cache[$count]['T006_Item_Pedido_Compra'] = $rowCache['T006_Item_Pedido_Compra'];		
			    $cache[$count]['T006_Modalidade_ICMS'] = $rowCache['T006_Modalidade_ICMS'];		
			    $cache[$count]['T006_Modalidade_ICMS_Substituicao'] = $rowCache['T006_Modalidade_ICMS_Substituicao'];		
			    $cache[$count]['T006_Motivo_Desoneracao_ICMS'] = $rowCache['T006_Motivo_Desoneracao_ICMS'];		
			    $cache[$count]['T006_Numero_Pedido_Compra'] = $rowCache['T006_Numero_Pedido_Compra'];		
			    $cache[$count]['T006_Percentual_Base_Operacao_Propria'] = $rowCache['T006_Percentual_Base_Operacao_Propria'];
			    $cache[$count]['T006_Percentual_Comissao'] = $rowCache['T006_Percentual_Comissao'];		
			    $cache[$count]['T006_Percentual_Desconto'] = $rowCache['T006_Percentual_Desconto'];		
			    $cache[$count]['T006_Percentual_Reducao_ICMS'] = $rowCache['T006_Percentual_Reducao_ICMS'];		
			    $cache[$count]['T006_Percentual_Reducao_ICMS_Oculto'] = $rowCache['T006_Percentual_Reducao_ICMS_Oculto'];		
			    $cache[$count]['T006_Percentual_Reducao_ICMS_ST'] = $rowCache['T006_Percentual_Reducao_ICMS_ST'];		
			    $cache[$count]['T006_Percentual_Trib_Estadual'] = $rowCache['T006_Percentual_Trib_Estadual'];		
			    $cache[$count]['T006_Percentual_Trib_Federal'] = $rowCache['T006_Percentual_Trib_Federal'];		
			    $cache[$count]['T006_Percentual_Trib_Municipal'] = $rowCache['T006_Percentual_Trib_Municipal'];	
			    $cache[$count]['T006_Percentual_Trib_Transparencia'] = $rowCache['T006_Percentual_Trib_Transparencia'];		
			    $cache[$count]['T006_Peso_Bruto'] = $rowCache['T006_Peso_Bruto'];		
			    $cache[$count]['T006_Peso_Unitario'] = $rowCache['T006_Peso_Unitario'];		
			    $cache[$count]['T006_Quantidade'] = $rowCache['T006_Quantidade'];		
			    $cache[$count]['T006_Situacao_Tributaria_COFINS'] = $rowCache['T006_Situacao_Tributaria_COFINS'];		
			    $cache[$count]['T006_Situacao_Tributaria_IPI'] = $rowCache['T006_Situacao_Tributaria_IPI'];		
			    $cache[$count]['T006_Situacao_Tributaria_PIS'] = $rowCache['T006_Situacao_Tributaria_PIS'];		
			    $cache[$count]['T006_ST_ICMS_Interno'] = $rowCache['T006_ST_ICMS_Interno'];		
			    $cache[$count]['T006_ST_VA'] = $rowCache['T006_ST_VA'];		
			    $cache[$count]['T006_T008_Id'] = $rowCache['T006_T008_Id'];	
			    $cache[$count]['T006_T014_Id'] = $rowCache['T006_T014_Id'];		
			    $cache[$count]['T006_UF_ICMS_Substituicao_Devido'] = $rowCache['T006_UF_ICMS_Substituicao_Devido'];		
			    $cache[$count]['T006_Valor_Base_COFINS'] = $rowCache['T006_Valor_Base_COFINS'];		
			    $cache[$count]['T006_Valor_Base_CSLL'] = $rowCache['T006_Valor_Base_CSLL'];	
			    $cache[$count]['T006_Valor_Base_ICMS'] = $rowCache['T006_Valor_Base_ICMS'];		
			    $cache[$count]['T006_Valor_Base_ICMS_Oculto'] = $rowCache['T006_Valor_Base_ICMS_Oculto'];		
			    $cache[$count]['T006_Valor_Base_ICMS_Substituicao_Destino'] = $rowCache['T006_Valor_Base_ICMS_Substituicao_Destino'];		
			    $cache[$count]['T006_Valor_Base_ICMS_Substituicao_Retido'] = $rowCache['T006_Valor_Base_ICMS_Substituicao_Retido'];		
			    $cache[$count]['T006_Valor_Base_IPI'] = $rowCache['T006_Valor_Base_IPI'];		
			    $cache[$count]['T006_Valor_Base_IRPJ'] = $rowCache['T006_Valor_Base_IRPJ'];		
			    $cache[$count]['T006_Valor_Base_PIS'] = $rowCache['T006_Valor_Base_PIS'];		
			    $cache[$count]['T006_Valor_COFINS'] = $rowCache['T006_Valor_COFINS'];	
			    $cache[$count]['T006_Valor_Comissao'] = $rowCache['T006_Valor_Comissao'];		
			    $cache[$count]['T006_Valor_CSLL'] = $rowCache['T006_Valor_CSLL'];		
			    $cache[$count]['T006_Valor_Custo_Unitario'] = $rowCache['T006_Valor_Custo_Unitario'];		
			    $cache[$count]['T006_Valor_Desconto_Unitario'] = $rowCache['T006_Valor_Desconto_Unitario'];		
			    $cache[$count]['T006_Valor_Despesas_Acessorias_Unitario'] = $rowCache['T006_Valor_Despesas_Acessorias_Unitario'];		
			    $cache[$count]['T006_Valor_Frete_Unitario'] = $rowCache['T006_Valor_Frete_Unitario'];		
			    $cache[$count]['T006_Valor_ICMS'] = $rowCache['T006_Valor_ICMS'];		
			    $cache[$count]['T006_Valor_ICMS_Credito'] = $rowCache['T006_Valor_ICMS_Credito'];		
			    $cache[$count]['T006_Valor_ICMS_Desonerado'] = $rowCache['T006_Valor_ICMS_Desonerado'];		
			    $cache[$count]['T006_Valor_ICMS_Oculto'] = $rowCache['T006_Valor_ICMS_Oculto'];		
			    $cache[$count]['T006_Valor_ICMS_Substituicao_Destino'] = $rowCache['T006_Valor_ICMS_Substituicao_Destino'];		
			    $cache[$count]['T006_Valor_ICMS_Substituicao_Retencao'] = $rowCache['T006_Valor_ICMS_Substituicao_Retencao'];		
			    $cache[$count]['T006_Valor_ICMS_Substituicao_Retido'] = $rowCache['T006_Valor_ICMS_Substituicao_Retido'];		
			    $cache[$count]['T006_Valor_IPI'] = $rowCache['T006_Valor_IPI'];		
			    $cache[$count]['T006_Valor_IRPJ'] = $rowCache['T006_Valor_IRPJ'];		
			    $cache[$count]['T006_Valor_PIS'] = $rowCache['T006_Valor_PIS'];		
			    $cache[$count]['T006_Valor_Preco_Original'] = $rowCache['T006_Valor_Preco_Original'];		
			    $cache[$count]['T006_Valor_Preco_Sem_Desconto_Unitario'] = $rowCache['T006_Valor_Preco_Sem_Desconto_Unitario'];		
			    $cache[$count]['T006_Valor_Preco_Unitario'] = $rowCache['T006_Valor_Preco_Unitario'];		
			    $cache[$count]['T006_Valor_Trib_Transparencia'] = $rowCache['T006_Valor_Trib_Transparencia'];	
			    $cache[$count]['Custo_Unitario'] = $rowCache['Custo_Unitario'];		
			    $cache[$count]['Custo_Total'] = $rowCache['Custo_Total'];	
				$cache[$count]['Local_1'] = $rowCache['Local_1'];
				$cache[$count]['T006_Preco_Pauta_ST'] = $rowCache['T006_Preco_Pauta_ST'];				
				$count++;
			}
			log("Contador do cache: ".count($cache));
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheT008-{$g['usuarioAtual']}");
			apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT008, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT008);
			//--------------------------------------------------------------------------
	        $retorno = apc_store("cacheT008-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
				if(!$retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        	$resposta['concluido'] = 1;
	        	break;
	        }
	    } else {
			// Recuperando dados do disco------------------------------------------------------
			// $cache = unserialize(file_get_contents($cacheT008));
			// if(!is_array($cache)){
	        // 	$resposta['code'] = false;
	        // 	$resposta['data'] = 'Erro ao recuperar arquivo com o cache cacheT008.';
	        // 	$resposta['concluido'] = 1;
	        // 	break;
			// }
			//---------------------------------------------------------------------------------
			$cache = unserialize(apc_fetch("cacheT008-{$g['usuarioAtual']}"));
			//---------------------------------------------------------------------------------
			$resposta = unserialize(apc_fetch("cacheT008-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
				if(!$retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao recuperar variável no cache resposta";
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

	    $ok = 0;
		$erro = 0;
		$erros ="";
		$retorno = "";

		log("limit1 T006= ".$resposta['limit1']);
		log("limit2 T006= ".$resposta['limit2']);
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$retorno = $VEN012->gerarNFincluirItens($T007_Id, $T005_Id, $cache[$i]);
           	if($retorno !== true){
				$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, null,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}
				$resposta['code'] = false;
				$resposta['data'] = "Erro no retorno do método gerarNFIncluirItens. Avise o suporte técnico.";
				$resposta['concluido'] = 1;
				log("Erro método gerarNFIncluirItens ". $retorno);
				break;
       		}else{
       			$ok++;
       		}
        }
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;
	

		if($resposta['etapa'] >= $resposta['etapas']){
			log("ultima etapa");
			// Se usar o arquivo do disco, usa o unlink, senão usa o apc_delete
			// unlink("{$Dir}cacheT008-{$g['usuarioAtual']}.txt");
			//------------------------------------------------------------------
			apc_delete("cacheT008-{$g['usuarioAtual']}");
			//-----------------------------------------------------------------
			apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT008-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'gerarNFContas':
		log("%%entra no ajax gerarNFContas");
		global $g;
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();
		$form = $r_ven012FormPedido;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;	
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;	
		$EtapaRoolback = $r_EtapaRoolback;
		$resposta['erroRoolback'] = "";
		log("t005_Id no gerarNFContas= ".$T005_Id);
		log("t007_Id no gerarNFContas= ".$T007_Id);

		//salva algumas variáveis no cache caso precise chamar o roolback
		$mT005 = mysql_fetch_array(mysql_query("SELECT T005_D024_Id, T005_C004_Id FROM T005 WHERE T005_Id='{$T005_Id}'"));

		$D007 = mysql_query("SELECT D007_Id,
				                    D007_Nosso_Numero
							   FROM D007 
						  LEFT JOIN D027 ON D027_D007_Id=D007_Id
						  LEFT JOIN T090 ON T090_D027_Id=D027_Id 
						      WHERE T090_T005_Id='{$T005_Id}'");
		$mD007 = mysql_fetch_array($D007);
		$cache['D007_Nosso_Numero'] = $mD007['D007_Nosso_Numero'];


		$D024 = mysql_query("SELECT D024_Data_Primeira_Compra,
									D024_Data_Ultima_Compra
							   FROM D024
	 		                  WHERE D024_Id='{$mT005['T005_D024_Id']}'");
		$mD024 = mysql_fetch_array($D024);
		$cache['D024_Data_Primeira_Compra'] = $mD024['D024_Data_Primeira_Compra'];
		$cache['D024_Data_Ultima_Compra'] = $mD024['D024_Data_Ultima_Compra'];

		$C004 = mysql_query("SELECT C004_Ultimo_Numero_Nota_Fiscal,
									C004_Ultimo_Numero_Nota_Fiscal_Eletronica,
									C004_Ultimo_Numero_Cupom_Fiscal,
									C004_Ultimo_Numero_ACP
							   FROM C004
							  WHERE C004_Id ='{$mT005['T005_C004_Id']}'");

		$mC004 = mysql_fetch_array($C004);
		$cache['C004_Ultimo_Numero_Nota_Fiscal'] = $mC004['C004_Ultimo_Numero_Nota_Fiscal'];
		$cache['C004_Ultimo_Numero_Nota_Fiscal_Eletronica'] = $mC004['C004_Ultimo_Numero_Nota_Fiscal_Eletronica'];
		$cache['C004_Ultimo_Numero_Cupom_Fiscal'] = $mC004['C004_Ultimo_Numero_Cupom_Fiscal'];
		$cache['C004_Ultimo_Numero_ACP'] = $mC004['C004_Ultimo_Numero_ACP'];


		apc_delete("cacheRoolback-{$g['usuarioAtual']}");
		$criouCache = apc_store("cacheRoolback-{$g['usuarioAtual']}", serialize($cache));
        if(!$criouCache){
        	$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, $cacheRoolback,$EtapaRoolback);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
			}
        	$resposta['code'] = false;
        	$resposta['concluido'] = 1;
        	$resposta['data'] = "Erro ao criar variável no cache";
        	apc_delete("cacheRoolback-{$g['usuarioAtual']}");
        	break;
        }
        //----------------------------------------------------------------


        $retorno = $VEN012->gerarNFContas($T007_Id, $form);
		if ($retorno === true) {
			$resposta['code'] = true;
		}else { 
			$cacheRoolback = unserialize(apc_fetch("cacheRoolback-{$g['usuarioAtual']}"));	
			$retornoRoolback = $VEN012->roolbackNF($T005_Id,$T007_Id, $cacheRoolback,$EtapaRoolback);
			if($retornoRoolback !== true){
				$resposta['erroRoolback'] = $retornoRoolback;
			}
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			apc_delete("cacheRoolback-{$g['usuarioAtual']}");
			break;
		}
	break;

	case 'gerarNFTotalizar':
		global $g;
		require_once("bibliotecas/classes/CAD002.php");	
		$CAD002 = new CAD002();
		require_once("bibliotecas/classes/VEN012.php");	
		$VEN012 = new VEN012();

		$EtapaRoolback = $r_EtapaRoolback;

		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT007 = "{$Dir}cacheT007-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		/*inicio calculo etapas*/
        if ($r_inicio==1){

			$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
			$resposta['validacaoST'] = false;
			$cacheRoolback = unserialize(apc_fetch("cacheRoolback-{$g['usuarioAtual']}"));
	
			$T007 = mysql_query("SELECT T007_Id,
										T007_Observacao_Nota_Fiscal_1, 
										T007_Numero_Nota_Fiscal 
								   FROM T007 
								  WHERE T007_T005_Id = '{$T005_Id}' 
									AND T007_Flag_Cancelada != 'S'");
			$mT007 = mysql_fetch_assoc($T007);
	
			$resposta['T007_Id']= $mT007['T007_Id'];

			//----------------------------------------------------------
        	//mysql_query("call T007_Gravar_Totalizacao_4('{$resposta['T007_Id']}')");
			//Para chamar a trigger T007, o T007_Valor_Total é calculado no T007_Gravar_Totalizacao_4
			mysql_query("update T007 set T007_Valor_Total = '0' where T007_Id= '{$resposta['T007_Id']}' ");
        	//----------------------------------------------------------
	        $cSql_T005 = mysql_query("SELECT T008_Id, 
										     T006_D009_Id,
											 D006_Observacao_Produto, 
											 T008_Codigo_Produto,
											 D006_Flag_Estoque
	        							FROM T007
								   LEFT JOIN T008 ON T008_T007_Id=T007_Id
								   LEFT JOIN T006 ON T006_Id=T008_T006_Id
								   LEFT JOIN D006 ON D006_Id=T008_D006_Id
	        					       WHERE T007_Id = '{$resposta['T007_Id']}'
								    GROUP BY T008_Id"); 
	        $count = 0;
        	while($rowCache = mysql_fetch_assoc($cSql_T005)) {
        		$cache[$count]['T008_Id'] = $rowCache['T008_Id'];
			    $cache[$count]['T006_D009_Id'] = $rowCache['T006_D009_Id'];
				$cache[$count]['D006_Observacao_Produto'] = trim($rowCache['D006_Observacao_Produto']);
				$cache[$count]['T008_Codigo_Produto'] = trim($rowCache['T008_Codigo_Produto']);
				$cache[$count]['D006_Flag_Estoque'] = trim($rowCache['D006_Flag_Estoque']);
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
			$resposta['concluido'] = 0;
			apc_delete("cacheT007-{$g['usuarioAtual']}");
			apc_delete("cacheT007-resposta-{$g['usuarioAtual']}");
			
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT007, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT007);
			//--------------------------------------------------------------------------
	        $retorno = apc_store("cacheT007-{$g['usuarioAtual']}", serialize($cache));
			//--------------------------------------------------------------------------
	        if(!$retorno){
	        	$resposta['concluido'] = 1;
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        	/*$retornoRoolback = $VEN012->roolbackNF($T005_Id,$resposta['T007_Id'], $cacheRoolback,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}*/
				apc_delete("cacheRoolback-{$g['usuarioAtual']}");
	        	break;
	        }
	    } else {
			// Recuperando dados do disco------------------------------------------------------
			// $cache = unserialize(file_get_contents($cacheT007));
			// if(!is_array($cache)){
	        // 	$resposta['code'] = false;
	        // 	$resposta['data'] = 'Erro ao recuperar arquivo com o cache T005.';
	        // 	$resposta['concluido'] = 1;
	        // 	break;
			// }
			//---------------------------------------------------------------------------------
			$cache = unserialize(apc_fetch("cacheT007-{$g['usuarioAtual']}"));
			// --------------------------------------------------------------------------------
			$resposta = unserialize(apc_fetch("cacheT007-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao recuperar variável no cache resposta";
	        	/*$retornoRoolback = $VEN012->roolbackNF($T005_Id,$resposta['T007_Id'], $cacheRoolback,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}*/
				apc_delete("cacheRoolback-{$g['usuarioAtual']}");
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

		if ($resposta['percentual']>100) {
			$resposta['percentual'] = 100;
		}	
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$erros ="";
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$T008_Id = $cache[$i]['T008_Id'];
	    	$D009_Id = $cache[$i]['T006_D009_Id'];
			$D006_Observacao_Produto = $cache[$i]['D006_Observacao_Produto'];
			$D006_Flag_Estoque = $cache[$i]['D006_Flag_Estoque'];

			// Chama a trigger para popular informações, esse valor é recalculado no T008_Gravar_Totalizacao
			mysql_query("UPDATE T008 SET T008_Valor_Total_Custo=0 WHERE T008_Id={$T008_Id}");

			if ($D006_Flag_Estoque == 'C') {
				$CAD002->D001_reprocessa_historico(0, $D009_Id, 10);
			} else {
				$CAD002->D001_reprocessa_historico(0, $D009_Id, 5, true);
			}

	        //mysql_query("call T008_Gravar_Totalizacao_4('{$T008_Id}')");

	        // Busca Observação Produto: da CFOP
    		if (!empty($D006_Observacao_Produto)) {

				$Observacao_Fiscal_Produto = $D006_Observacao_Produto . ';';

	        	// Seleciona os campos (valores) a serem substituidos na observação do produto
	        	$sqlT008 = mysql_query("SELECT *,
											   T290_Valor_Base_ICMS_Substituicao AS T008_BC_ICMS_ST_Compra, 
				                               T290_Valor_ICMS_Substituicao AS T008_Valor_ICMS_ST_Compra 
				                          FROM T008 
 	 							     LEFT JOIN D005 on D005_Id=T008_D005_Id 
									 LEFT JOIN T290 on T008_Id=T290_T008_Id										  
										 WHERE T008_Id = '{$T008_Id}'");
	       		$mT008   = mysql_fetch_assoc($sqlT008);

	            // Váriaveis dentro da Observação Fiscal
	            foreach ($mT008 as $key => $value) {
	                $key = preg_replace('#^[^_]+_#', '', $key);
	                $key = strtoupper($key);
	                if (preg_match('#^([0-9])+\.([0-9])+$#', $value)) {
	                    $value = gCorrigeNumero($value);
	                }
	                $Observacao_Fiscal_Produto = str_replace('[' . strtoupper($key) . ']', $value, $Observacao_Fiscal_Produto);
	            }
	            // Novo formato: [FORMULA: VALOR_TOTAL_BASE_ICMS * 0.075]
	            if (preg_match_all('/\[FORMULA:\s*([^\]]+)\]/i', $Observacao_Fiscal_Produto, $formulas, PREG_SET_ORDER)) {
	            	foreach ($formulas as $formula) {
	            		$expressaoOriginal = $formula[1];
	            		$expressaoCalc = strtoupper($expressaoOriginal);
	            		foreach ($mT008 as $keyExpr => $valueExpr) {
	            			if (!is_string($keyExpr) || ctype_digit($keyExpr)) {
	            				continue;
	            			}
	            			$keyExpr = strtoupper(preg_replace('#^[^_]+_#', '', $keyExpr));
	            			$valorExpr = is_numeric($valueExpr) ? str_replace(',', '.', (string) $valueExpr) : 0;
	            			$expressaoCalc = preg_replace('/\b' . preg_quote($keyExpr, '/') . '\b/', $valorExpr, $expressaoCalc);
	            		}
	            		$expressaoCalc = preg_replace('/(?<=\d),(?=\d)/', '.', $expressaoCalc);
	            		if (preg_match('/^[0-9\.\+\-\*\/\(\)\s]+$/', $expressaoCalc)) {
	            			$resultadoFormula = 0;
	            			eval('$resultadoFormula = ' . $expressaoCalc . ';');
	            			$Observacao_Fiscal_Produto = str_replace($formula[0], gCorrigeNumero($resultadoFormula), $Observacao_Fiscal_Produto);
	            		}
	            	}
	            }
	           
	            mysqli_query("UPDATE T008 SET T008_Informacoes_Adicionais='{$Observacao_Fiscal_Produto}' WHERE T008_Id={$T008_Id}");
	            
	        }

	        $retornoTotalizacao = mysql_error();     	 
           	if(!empty($retornoTotalizacao)){
           		$resposta['concluido'] = 1;
				/*$retornoRoolback = $VEN012->roolbackNF($T005_Id,$resposta['T007_Id'], $cacheRoolback,$EtapaRoolback);
				if($retornoRoolback !== true){
					$resposta['erroRoolback'] = $retornoRoolback;
				}*/
				apc_delete("cacheRoolback-{$g['usuarioAtual']}");
           	 	$resposta['code'] = false;
           	 	$resposta['data'] = $retorno;
           	 	break;
       		}else{
       			$ok++;
       		}
        }
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			//última etapa-----------------------------------------
			$sql = mysql_query("SELECT T005_Valor_ICMS_Substituicao_Retencao
			                      FROM T005 
			                     WHERE T005_Id = '{$T005_Id}'");
			$result = mysql_fetch_assoc($sql);
			$valorST = $result['T005_Valor_ICMS_Substituicao_Retencao'];

			if($g['C031']['gerarStContasPagar'] == 'S' && $valorST > 0){
				$resposta['validacaoST'] = true;
			}

			//mysql_query("call T007_Gravar_Totalizacao_4('{$resposta['T007_Id']}')");
			// ************************************************************************************************************************************************
        	// CRIANDO A OBSERVAÇÃO DA NOTA
        	// ************************************************************************************************************************************************
	        $sqlT007 = mysql_query("SELECT * 
									  FROM T007 
								 LEFT JOIN D006 ON D006_Id = T007_D006_Id 
								 LEFT JOIN D024 ON D024_Id = T007_D024_Id 
								 LEFT JOIN D018 ON D018_Id = D024_D018_Id 
								 LEFT JOIN D024A ON D024_Id = D024A_D024_Id
								     WHERE T007_Id = '{$resposta['T007_Id']}'");
	        $T007 = mysql_fetch_assoc($sqlT007);
	        
	        $sqlT008 = mysql_query("SELECT D006_Observacao 
									  FROM T007 
								 LEFT JOIN T008 ON T008_T007_Id = T007_Id 
								 LEFT JOIN D006 ON T008_D006_Id = D006_Id 
								     WHERE T007_Id = '{$resposta['T007_Id']}' 
								  GROUP BY D006_Observacao");

	        $Observacao_Fiscal = $T007['T007_Observacao_Nota_Fiscal_1'];
	        
            while ($T008 = mysql_fetch_array($sqlT008)) {
            	if(!empty($T008['D006_Observacao'])){
                 	$Observacao_Fiscal .= $T008['D006_Observacao'] . ';';
            	}
	        }

			if (!empty($Observacao_Fiscal)) {
				// CALCULANDO O DIFERIMENTO DO ICMS PARA PODER EXIBIR NA OBS DA CFOP
				// $valor_Diferido = $mT008[T008_Valor_ICMS_Oculto] - $mT008[T008_Valor_ICMS];
				$T008=mysql_query("SELECT (SUM(T008_Valor_ICMS_Oculto) - SUM(T008_Valor_ICMS)) AS T007_Valor_Total_Diferimento  FROM T008 WHERE T008_T007_Id='{$resposta['T007_Id']}'");
				$T008_Dif=mysql_fetch_assoc($T008);
				$T007=array_merge($T007,$T008_Dif);

				$sqlT290 = mysql_query("SELECT SUM(T290_Valor_Base_ICMS_Substituicao) AS T007_BC_ICMS_ST_Compra, SUM(T290_Valor_ICMS_Substituicao) AS T007_Valor_ICMS_ST_Compra, T290_T008_Id FROM T290 LEFT JOIN T008 ON T008_Id=T290_T008_Id WHERE T008_T007_Id='{$resposta['T007_Id']}'");
				$T290=mysql_fetch_assoc($sqlT290);
				$T007=array_merge($T007,$T290);

				// Váriaveis dentro da Observação Fiscal
				foreach ($T007 as $key => $value) {
					$key = preg_replace('#^[^_]+_#', '', $key);
					$key = strtoupper($key);
					if (preg_match('#^([0-9])+\.([0-9])+$#', $value)) {
						$value = gCorrigeNumero($value);
					}
					$Observacao_Fiscal = str_replace('[' . strtoupper($key) . ']', $value, $Observacao_Fiscal);
				}
				// Novo formato: [FORMULA: VALOR_TOTAL_BASE_ICMS * 0.075]
				if (preg_match_all('/\[FORMULA:\s*([^\]]+)\]/i', $Observacao_Fiscal, $formulas, PREG_SET_ORDER)) {
					foreach ($formulas as $formula) {
						$expressaoOriginal = $formula[1];
						$expressaoCalc = strtoupper($expressaoOriginal);
						foreach ($T007 as $keyExpr => $valueExpr) {
							if (!is_string($keyExpr) || ctype_digit($keyExpr)) {
								continue;
							}
							$keyExpr = strtoupper(preg_replace('#^[^_]+_#', '', $keyExpr));
							$valorExpr = is_numeric($valueExpr) ? str_replace(',', '.', (string) $valueExpr) : 0;
							$expressaoCalc = preg_replace('/\b' . preg_quote($keyExpr, '/') . '\b/', $valorExpr, $expressaoCalc);
						}
						$expressaoCalc = preg_replace('/(?<=\d),(?=\d)/', '.', $expressaoCalc);
						if (preg_match('/^[0-9\.\+\-\*\/\(\)\s]+$/', $expressaoCalc)) {
							$resultadoFormula = 0;
							eval('$resultadoFormula = ' . $expressaoCalc . ';');
							$Observacao_Fiscal = str_replace($formula[0], gCorrigeNumero($resultadoFormula), $Observacao_Fiscal);
						}
					}
				}

 				//Trava a nota para alteração
				/*$upT007Obs1 = "UPDATE T007 SET T007_Observacao_Nota_Fiscal_1='{$Observacao_Fiscal}', T007_Flag_Travar_Nota = 'S' WHERE T007_Id={$resposta['T007_Id']};";
				log($upT007Obs1);

				if($Observacao_Fiscal != ''){
					mysql_query($upT007Obs1);
				} */
			}
			
			$updateT007  = "UPDATE T007 SET";
			if($Observacao_Fiscal != ''){
				$Observacao_Fiscal = mysql_real_escape_string($Observacao_Fiscal);
				$updateT007 .= " T007_Observacao_Nota_Fiscal_1 = '{$Observacao_Fiscal}' ,";
			}
			$updateT007 .= " T007_Flag_Travar_Nota = 'S'";
			$updateT007 .= " WHERE T007_Id='{$resposta['T007_Id']}'";

			mysql_query($updateT007);

	        // ************************************************************************************************************************************************
        	// FIM OBSERVAÇÃO NOTA
        	// ************************************************************************************************************************************************
			
			// ----------------------------------------------------------------------------
			// unlink("{$Dir}cacheT007-{$g['usuarioAtual']}.txt");
			// Se for usar o arquivo em disco, usar o unlink, senão usa o apc_delete-------
			apc_delete("cacheT007-{$g['usuarioAtual']}");
			// -------------------------------------------------------------------------------
			apc_delete("cacheT007-resposta-{$g['usuarioAtual']}");
			apc_delete("cacheRoolback-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT007-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT007-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'atualizarAliquotasPedido':
		log("entrou no ajax atualizarAliquotasPedido");
		require_once("bibliotecas/classes/VEN002.php");	
		$VEN002 = new VEN002();

		global $g;
		$resposta['code'] = true;	
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$C004_Id = isset($r_C004_Id) ? $r_C004_Id : false;
		log("T005_Id no atualizarAliquota".$T005_Id);


		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT006 = "{$Dir}cacheT006-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		if ($r_inicio==1){
			if (empty($T005_Id)) {
				$resposta['code'] = false;
	            $resposta['data'] = "A variável T005_Id nao foi passada para o programa: atualizar_aliquotas.php3";
	        }
	        $cSql_T005 = mysql_query("SELECT T005_Flag_Devolucao FROM T005 LEFT JOIN D006 ON D006_Id=T005_D006_Id LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
	        $mT005     = mysql_fetch_array($cSql_T005);

	        if ($mT005['T005_Flag_Devolucao'] == 'S') {
	        	$resposta['code'] = false;
	            $resposta['data'] = "Este é um pedido para emissão de nota de devolução, não é possivel executar a atualização de aliquotas";
	        }

	        //=====================================================================
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        //-- RODA A REGRA CFOP
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        
	        $cSql_T006 = mysql_query("SELECT
                                        T006_Id,
                                        D009_Id,
                                        D001_Codigo_Produto,
                                        D001_Peso_Unitario_Kg,
                                        T005_Flag_Lista_Preco,
                                        T005_D024_Id,
                                        T005_Flag_Acp,
                                        T006_D006_Id,
                                        ifnull(T006_T219_Id,0) as T006_T219_Id,
                                        ifnull(T006_T225_Id,0) as T006_T225_Id,
                                        IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria
                                        FROM T005
                                        LEFT JOIN T006 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D024 ON D024_Id=T006_D024_Id
                                        LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                        WHERE T005_Id='{$T005_Id}'");
	       	$erroMysql = mysql_error();
	        if (!empty($erroMysql)) 
	        {
	        	$resposta['code'] = false;
	            $resposta['data'] = "Erro consultando itens do pedido: $erroMysql $cClausula";
	        }

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cSql_T006)){
	        	$cache[$count]['T006_Id']= $rowCache['T006_Id'];
	        	log("T006_Id no atualizar aliquotas: ".$rowCache['T006_Id']);
	            $cache[$count]['D009_Id'] = $rowCache['D009_Id'];   
	            $cache[$count]['D009_Origem_Mercadoria']= $rowCache['D009_Origem_Mercadoria'];
	            $cache[$count]['T005_Flag_Lista_Preco']= $rowCache['T005_Flag_Lista_Preco'];
	            $cache[$count]['T005_D024_Id']= $rowCache['T005_D024_Id'];
	            $cache[$count]['T006_D006_Id']= $rowCache['T006_D006_Id'];
            	$cache[$count]['T006_T219_Id']= $rowCache['T006_T219_Id'];
	            $cache[$count]['T006_T225_Id']= $rowCache['T006_T225_Id'];
	            $cache[$count]['D001_Peso_Unitario_Kg']= $rowCache['D001_Peso_Unitario_Kg'];	
				$count++;
				log("count do atualizar aliquotas= ".$count);
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheT006-{$g['usuarioAtual']}");
			apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT006, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT006);
			//--------------------------------------------------------------------------
			$retorno = apc_store("cacheT006-{$g['usuarioAtual']}", serialize($cache));
			//--------------------------------------------------------------------------
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			// Recuperando dados do disco------------------------------------------------------
			// $cache = unserialize(file_get_contents($cacheT006));
			// if(!is_array($cache)){
	        // 	$resposta['code'] = false;
	        // 	$resposta['data'] = 'Erro ao recuperar arquivo com o cache cacheT006.';
	        // 	$resposta['concluido'] = 1;
	        // 	break;
			// }
			//---------------------------------------------------------------------------------
			$cache = unserialize(apc_fetch("cacheT006-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT006-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao recuperar variável no cache resposta';
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

	   	$ok = 0;
		$erro = 0;
		$erros ="";

		log("limit1 no atualizarAliquota: ".$resposta['limit1']);
		log("limit2 no atualizaraliquota: ".$resposta['limit2']);
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$retorno = $VEN002-> atualizarAliquotas($T005_Id,$cache[$i]);           
           	if($retorno !== true){
           		log("entra aqui");
           		$resposta['code'] = false;
       	     	$erro++;
           	 	$erros .= $retorno."<br />";
       		}else{
       			$ok++;
       		}
        }
   	
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){ 
			// Se for usar o arquivo em disco, usa o unlink senão usa o apc_delete--------
			// unlink("{$Dir}cacheT006-{$g['usuarioAtual']}.txt");
			// ---------------------------------------------------------------------------
			apc_delete("cacheT006-{$g['usuarioAtual']}");
			apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT006-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT006-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;
	
	                         			
	case 'atualizarAliquota':
		require_once("bibliotecas/classes/VEN002.php");
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$VEN002 = new VEN002();
		
		$retorno = $VEN002->atualizarAliquotasPedidos($T005_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'inserirParcela':
		require_once("bibliotecas/classes/VEN002.php");
		
		$form=$r_ven012FormPagamento;		
		$VEN002 = new VEN002();		
		$retorno = $VEN002->inserirParcela($form);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;
	
	case 'calcularParcelaSimulacao':
		require_once("bibliotecas/classes/VEN012.php");
		
		$form=$r_ven012FormPagamento;		
		$VEN012 = new VEN012();		
		$retorno = $VEN012->calcularParcelaSimulacao($form);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'salvarObservacao':
		require_once("bibliotecas/classes/VEN012.php");
		
		$form=$r_ven012FormPagamento;		
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		$VEN012 = new VEN012();		
		$retorno = $VEN012->gravarObservacao($form, $T005_Id);
		
		if ($retorno === false) {			
			$resposta['code'] = false;
			$resposta['data'] = 'Falha ao salvar...';			
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'ven012ImprimirChecklist':
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$retorno = $VEN012->montarChecklistPdf($T005_Id);
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
	break;
	
	case 'ven012ImprimirOrdemServico':
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();
		
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		
		$retorno = $VEN012->montarOrdemServicoPdf($T005_Id);
		$resposta['code'] = true;
		$resposta['data'] = $retorno;
	break;
	
	case 'atualizarPrecoProduto':
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$T006_Valor_Preco = isset($r_T006_Valor_Preco) ? gCorrigeNumeroInverte($r_T006_Valor_Preco) : false;
		if($T006_Id){
			mysql_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario='{$T006_Valor_Preco}',
		                                T006_Percentual_Desconto=if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100,0),
		                                T006_Valor_Desconto_Unitario=if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*({$T006_Valor_Preco}/100)),0)
		                      WHERE T006_Id='{$T006_Id}'");
			//mysql_query("UPDATE T006 SET T006_Valor_Preco_Sem_Desconto_Unitario = '{$T006_Valor_Preco}' WHERE T006_Id = '{$T006_Id}'");			
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'gerarNotaFiscalCupom':
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();

		$r_T007_Id = mysql_real_escape_string($r_T007_Id);
		$retorno = $VEN012->gerarNotaFiscalCupom($r_T007_Id);
		if ($retorno === true) {
			$resposta['code'] = 1;
		} else if (strpos($retorno, 'Deseja continuar?') !== false) {
			$resposta['code'] = 2;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'retornarPedidoSeparacao':
		global $g;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$cErro = "";
		if($g['C031']['desabilitarSeparacao']=='N'){
			mysql_query("UPDATE T005 SET T005_Flag_Status = '2' WHERE T005_Id = '{$T005_Id}'");
			$cErro   = mysql_error();
			$mensagem = "Pedido retornado para separação com sucesso!";
		}else{
			$cErro = "Pedido não retornado para a separação, a opção 'Separaçao' está desabilitada em configurações.";
		}

        if (!empty($cErro)) {
            $resposta['code'] = false;
			$resposta['data'] = $cErro;
        } else {
        	$resposta['code'] = true;
        	$resposta['data'] = $mensagem;
        }
	break;

	case 'enviarEmailNotas':
		$resposta['code'] = false;
		$resposta['data'] = 'A base local não envia e-mail.';
		break;
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();
		$erros = '';
		$enviarDanfe = (!empty($r_enviarDanfe)) ? true : false;

		if($r_Opcao=='marcadas'){
			$extra=" and T007_Flag_Enviar='S'";
		} else if($r_Opcao=='naoEnviadas'){
			$extra=" and T007_Data_Envio_XML='0000-00-00 00:00:00'";
		}else{
			$extra='';
		}
		$sqlT007 = mysql_query("SELECT T007_Id,
									   T007_Numero_Recibo_Nfe,
									   T007_Numero_Nota_Fiscal
								  FROM T007 
							 LEFT JOIN D024 ON D024_Id = T007_D024_Id 
								 WHERE (T007_Flag_ACP=6 OR T007_Flag_ACP=8)
								   AND T007_C004_Id={$g['empresaAtual']} 
								   AND T007_Flag_Cancelada='N' 
								   AND T007_TX_Flag_Finalizada != 1 
								       {$extra} 
							 ORDER BY T007_Data_Emissao DESC");
	
		while($T007 = mysql_fetch_array($sqlT007)){
			$retorno = $VEN012->enviarMultiNotasFiscais($T007['T007_Id'], $enviarDanfe);
			if($retorno !== true){
				$erros .= $T007['T007_Numero_Nota_Fiscal'].', ';
			}
		}
		if(mysql_num_rows($sqlT007)<=0){
			$resposta['data'] = "Não foi encontrado nenhuma nota para ser enviada, por favor verifique.";
			$resposta['code'] = false;
		} else if($erros != '') { 
			$erros = substr($erros, 0, -2);
			$resposta['data'] = "As notas: ".$erros." não foram enviadas, verifique as informações do cliente";
			$resposta['code'] = false;
		} else {
			$resposta['data'] = "E-mails enviados com sucesso!";
			$resposta['code'] = true;
		} 
	break;

	case 'validarCamposNFe':

		require_once("bibliotecas/classes/VEN002.php");

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		$VEN002 = new VEN002();

		$mensagem = '';
		$retorno = $VEN002->validarCamposNFe(null, $T005_Id, null);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = $mensagem;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'separarPedido':
		global $g;
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();

		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		$cErro = "";
		
		$retorno = $VEN012->separarPedido($T005_Id);

        if ($retorno === true) {
            $resposta['code'] = true;
			$resposta['data'] = "Produtos Separados";
        } else {
        	$resposta['code'] = false;
        	$resposta['data'] = $retorno;
        }
	break;

	case 'getEtiquetaTransporteBoxLeitor':

		$resposta = array();
		$T007_Id = $r_T007_Id;
		$T005_Id = $r_T005_Id;

		$sql = " select T007_D022_Id, T007_Quantidade_Volumes from T007 where T007_Id = {$T007_Id} ";
		$query = mysql_query($sql);
		$row = mysql_fetch_array($query);

		$D022_Id = $row['T007_D022_Id'];

		$resposta['tpEtiqueta'] = "";

		if (empty($resposta['T007_Quantidade_Volumes'])) {
			$resposta['T007_Quantidade_Volumes'] = 1;
		}

		$sql = " select D022_Nome_Empresa from D022 where D022_Id = {$D022_Id} ";
		$query = mysql_query($sql);
		$D022 = mysql_fetch_array($query);

		if(!empty($D022['D022_Nome_Empresa'])){
			$transportadora = $D022['D022_Nome_Empresa'];

			if($transportadora == 'MERCADO LIVRE'){

				$sql = "select T005_Id_Pedido_Externo from T005 where T005_Id = {$T005_Id}";
				$query = mysql_query($sql);
				$row = mysql_fetch_array($query);

				require_once("prog/outros/integracaoSMT/integracaoTray/etiquetasML.php");
				$etiquetasML = new etiquetasML();
				$etiqueta = $etiquetasML->getEtiqueta($row['T005_Id_Pedido_Externo']);

				$resposta['semEtiqueta'] = $etiqueta['semEtiqueta'];
				$resposta['urlEtiquetaML'] = $etiqueta['etiqueta'];
				$resposta['tpEtiqueta'] = "ML";

			}elseif($transportadora == 'CORREIOS PAC' || $transportadora == 'CORREIOS SEDEX'){

				$resposta['semCodRastreio'] = false;

				$sql = "SELECT T268.T268_Codigo_Rastreio, 
							   T005.T005_Id_Pedido_Externo
						from t005 
						left join T007 on T005.T005_T007_Id = T007.T007_Id
						left join T268 on T268.T268_T007_Id = T268.T268_Id
						where T005_Id = {$T005_Id}";

				$query = mysql_query($sql);
				$row = mysql_fetch_array($query);

				require_once("prog/outros/integracaoSMT/integracaoPortalPostal/pegaEtiquetaCorreios.php");
				require_once("prog/outros/integracaoSMT/integracaoTray/pegaCodigoCorreiosTray.php");

				$pegaCodigoCorreiosTray = new pegaCodigoCorreiosTray();
				$codigoRastreio = $pegaCodigoCorreiosTray->getCodRastreio($row['T005_Id_Pedido_Externo']);

				log($row);

				if($codigoRastreio == null){
					if($row['T268_Codigo_Rastreio'] == null || $row['T268_Codigo_Rastreio'] == ''){
						$resposta['semCodRastreio'] = true;
					}else{
						$pegaEtiquetaCorreios = new pegaEtiquetaCorreios();
						$filePathName = $pegaEtiquetaCorreios->getEtiquetaCorreio($row['T268_Codigo_Rastreio'], null);
						$resposta['urlEtiquetaCR'] = $filePathName;
					}
				}else{
					$pegaEtiquetaCorreios = new pegaEtiquetaCorreios();
					$filePathName = $pegaEtiquetaCorreios->getEtiquetaCorreio(null, $codigoRastreio);
					$resposta['urlEtiquetaCR'] = $filePathName;
				}

				$resposta['tpEtiqueta'] = "CR";

			}else{

				$resposta['T007_Quantidade_Volumes'] = $row['T007_Quantidade_Volumes'];
				$resposta['tpEtiqueta'] = "PD";

			}
		}

	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
