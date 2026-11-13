<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());
require_once('bibliotecas/classes/CAD002.php');
require_once('bibliotecas/segurancaSenha.php');
$CAD002 = new CAD002();

/**
 * Ações
 */
	switch($g['r'][3]) {
    case 'salvarTipoDanfe':
        require_once __DIR__ . '/../../../hardness/class/DanfeModelo.php';
        $retornoDanfe = ($_SERVER['REQUEST_METHOD'] === 'POST')
            ? DanfeModelo::salvarPorNota($_POST['T007_Id'] ?? 0, $g['empresaAtual'], $_POST['tipoDanfe'] ?? null)
            : 'Utilize o botão Salvar tipo de DANFE.';
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('code' => $retornoDanfe === true, 'data' => $retornoDanfe === true ? 'Tipo de DANFE salvo.' : $retornoDanfe));
        exit;

	case 'gerarNFDebitoCreditoAutorizada':
		require_once('bibliotecas/classes/VEN003.php');
		$VEN003 = new VEN003();
		$quantidades = isset($_REQUEST['quantidades']) ? json_decode($_REQUEST['quantidades'], true) : array();
		$reforma = isset($_REQUEST['reforma']) ? json_decode($_REQUEST['reforma'], true) : array();
		$precos = isset($_REQUEST['precos']) ? json_decode($_REQUEST['precos'], true) : array();
		$retorno = $VEN003->gerarNFDebitoCreditoNotaAutorizada(array(
			'T007_Id' => isset($_REQUEST['T007_Id']) ? (int) $_REQUEST['T007_Id'] : 0,
			'tipoOperacao' => isset($_REQUEST['tipoOperacao']) ? $_REQUEST['tipoOperacao'] : '',
			'tipoFiscal' => isset($_REQUEST['tipoFiscal']) ? $_REQUEST['tipoFiscal'] : '',
			'D006_Id' => isset($_REQUEST['D006_Id']) ? (int) $_REQUEST['D006_Id'] : 0,
			'quantidades' => is_array($quantidades) ? $quantidades : array(),
			'precos' => $precos,
			'reforma' => is_array($reforma) ? $reforma : array()
		));
		if (is_array($retorno)) {
			$identificacao = !empty($retorno['numero']) ? $retorno['numero'] : 'Em elaboração #' . $retorno['T007_Id'];
			$localReferencia = $retorno['referencia'] === 'cabecalho'
				? 'A chave da NF-e autorizada foi referenciada automaticamente no cabeçalho.'
				: ($retorno['referencia'] === 'item'
					? 'A chave e, quando exigido, o número do item foram referenciados automaticamente nos produtos.'
					: 'Este tipo não exige referência fiscal automática à NF-e de origem.');
			$resposta['code'] = true;
			$resposta['data'] = "NF de {$retorno['operacao']} tipo {$retorno['tipoFiscal']} gerada em elaboração.<br />NF: <strong>{$identificacao}</strong> &nbsp; Série: {$retorno['serie']}<br />Itens: {$retorno['itens']}<br />{$localReferencia}<br /><br />Revise e transmita a NF pelo fluxo Fiscal normal.";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCamposItemEvento112140':
		$T321_Id = (int)$_REQUEST['T321_Id'];
		$T008_Id = (int)$_REQUEST['T008_Id'];
		$sql = "SELECT T008_Id, T008_Codigo_Produto, T008_Descricao_Produto, T008_Quantidade, T008A_Valor_IBS, T008A_Valor_CBS,
					IFNULL((SELECT SUM(T322_Quantidade_Nao_Fornecida) FROM T322 INNER JOIN T321 AS EventoAutorizado ON EventoAutorizado.T321_Id = T322.T322_T321_Id WHERE T322.T322_T008_Id_Origem = T008.T008_Id AND EventoAutorizado.T321_T007_Id = T321.T321_T007_Id AND EventoAutorizado.T321_Tipo_Evento = '112140' AND (EventoAutorizado.T321_Status = 'AUTORIZADO' OR T322.T322_T321_Id = '{$T321_Id}')), 0) AS Quantidade_Utilizada,
					IFNULL((SELECT SUM(T322_Valor_IBS) FROM T322 INNER JOIN T321 AS EventoAutorizado ON EventoAutorizado.T321_Id = T322.T322_T321_Id WHERE T322.T322_T008_Id_Origem = T008.T008_Id AND EventoAutorizado.T321_T007_Id = T321.T321_T007_Id AND EventoAutorizado.T321_Tipo_Evento = '112140' AND (EventoAutorizado.T321_Status = 'AUTORIZADO' OR T322.T322_T321_Id = '{$T321_Id}')), 0) AS Valor_IBS_Utilizado,
					IFNULL((SELECT SUM(T322_Valor_CBS) FROM T322 INNER JOIN T321 AS EventoAutorizado ON EventoAutorizado.T321_Id = T322.T322_T321_Id WHERE T322.T322_T008_Id_Origem = T008.T008_Id AND EventoAutorizado.T321_T007_Id = T321.T321_T007_Id AND EventoAutorizado.T321_Tipo_Evento = '112140' AND (EventoAutorizado.T321_Status = 'AUTORIZADO' OR T322.T322_T321_Id = '{$T321_Id}')), 0) AS Valor_CBS_Utilizado
				FROM T008
				INNER JOIN T321 ON T321_T007_Id = T008_T007_Id
				LEFT JOIN T008A ON T008A_T008_Id = T008_Id
				WHERE T321_Id = '{$T321_Id}' AND T008_Id = '{$T008_Id}'";
		$linha = mysql_fetch_assoc(mysql_query($sql));
		if (empty($linha)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Produto não pertence à NF-e do evento.';
		} else {
			$linha['Quantidade_Saldo'] = (float)$linha['T008_Quantidade'] - (float)$linha['Quantidade_Utilizada'];
			$linha['Valor_IBS_Saldo'] = (float)$linha['T008A_Valor_IBS'] - (float)$linha['Valor_IBS_Utilizado'];
			$linha['Valor_CBS_Saldo'] = (float)$linha['T008A_Valor_CBS'] - (float)$linha['Valor_CBS_Utilizado'];
			$resposta['data'] = $linha;
		}
	break;

	case 'finalizarNotaFiscal':
		require_once("bibliotecas/classes/VEN003.php");

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$resposta['T007_Id'] = $T007_Id;
		$VEN003 = new VEN003();

		$retorno = $VEN003->finalizarNf($T007_Id);
		log("retorno do finalizar NF= ".$retorno);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'totalizarNotaFiscal':
		$resposta['totalRegistros'] = 0;
		$resposta['etapas'] = 0;
		$resposta['etapas'] = 0;
		$resposta['registrosEtapa'] = 0;
		$resposta['etapa'] = 0;
		$resposta['ok'] = 0;
		$resposta['erro'] = 0;
		$resposta['concluido'] = 1;
		$resposta['code'] = true;
		$resposta['data'] = "";
		$resposta['percentual'] = 100;
	break;

	case 'XXXtotalizarNotaFiscal':
		require_once("bibliotecas/classes/VEN003.php");
		global $g;
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		log("%T007_Id no totalizarNotaFiscal= ".$T007_Id);

		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT005 = "{$Dir}cacheT007-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		/*inicio calculo etapas*/
        if ($r_inicio==1){
        	//totaliza produtos da nota
        	$sqlCache = mysql_query("SELECT T008_Id FROM T008 WHERE T008_T007_Id='{$T007_Id}' ORDER BY T008_Id");
        	$count = 0;
        	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['T008_Id'] = $rowCache['T008_Id'];	
				$count++;
				log("count T008= ".$count);
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT005, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT005);
			//--------------------------------------------------------------------------
			$retorno = apc_store("cacheT007-{$g['usuarioAtual']}", serialize($cache));
			//--------------------------------------------------------------------------
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
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
			$cache = unserialize(apc_fetch("cacheT007-{$g['usuarioAtual']}"));
			//---------------------------------------------------------------------------------
			$resposta = unserialize(apc_fetch("cacheT007-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao recuperar variável no cache resposta";
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

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$T008_Id = $cache[$i]['T008_Id'];
	    	mysql_query("call T008_Gravar_Totalizacao_4('{$T008_Id}')");
	    	$cErro = mysql_error();
	        if (!empty($cErro)) {
	            $retorno = "ERRO: " . $cErro;
	        }            
           	if(!empty($retorno)){
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
       		}else{
       			$ok++;
       		}
        }
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			mysql_query("call T007_Gravar_Totalizacao_4('{$T007_Id}')");
			// Se for usar o arquivo do disco, usa o unlink, senão usa o apc_delete
			// unlink("{$Dir}cacheT005-{$g['usuarioAtual']}.txt");
			//------------------------------------------------------------------------------
			apc_delete("cacheT007-{$g['usuarioAtual']}");
			//------------------------------------------------------------------------------
			apc_delete("cacheT007-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT007-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT007-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'finalizarNotaFiscalContinuarLiberacao':
		require_once("bibliotecas/classes/VEN003.php");

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		$VEN003 = new VEN003();

		log("Entrou no continuarLiberacao");
		$retorno = $VEN003->continuarLiberacao($T007_Id);
		log("Saiu do continuarLiberacao");

		if ($retorno === true) {
			if ($r_C007PDV) {
				// Caso seja uma venda PDV limpar C007_T007_Id_PDV após finalização da nota
				mysql_query("update C007 set C007_T007_Id_PDV = 0 where C007_Id='{$g['usuarioAtual']}' and C007_T007_Id_PDV='{$T007_Id}'");
			}
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'totalizarReprocessaHistorico':
		require_once("bibliotecas/classes/VEN003.php");
		require_once("bibliotecas/classes/CAD002.php");
		global $g;
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$CAD002 = new CAD002();
		
		//cria pasta para salvar o cache----------
		// $Dir = $g['pathDados'] . 'tmp/';
		// is_dir($Dir) or mkdir($Dir, 0777, true);
		// $cacheT008 = "{$Dir}cacheT008-{$g['usuarioAtual']}.txt";
		//-----------------------------------------

		/*inicio calculo etapas*/
        if ($r_inicio==1){
        	$sqlCache   = mysql_query("SELECT T008_D009_Id,
        		                              T008_Id,
											  D006_Flag_Estoque,
											  T008_T006_Id
										 FROM T008
								    LEFT JOIN D006 ON D006_Id=T008_D006_Id
									    WHERE T008_T007_Id='{$T007_Id}'
									 ORDER BY T008_Id");
        	$count = 0;
        	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['T008_D009_Id'] = $rowCache['T008_D009_Id'];	
			    $cache[$count]['T008_Id'] = $rowCache['T008_Id'];	
			    $cache[$count]['D006_Flag_Estoque'] = $rowCache['D006_Flag_Estoque'];	
			    $cache[$count]['T008_T006_Id'] = $rowCache['T008_T006_Id'];	
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
			//apc_delete("cacheT008-{$g['usuarioAtual']}");
			//apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT008-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheT008-resposta-{$g['usuarioAtual']}");
			// salvando cache em disco--------------------------------------------------
			// $h = fopen($cacheT008, "a");
			// $r = fwrite($h, serialize($cache));
			// fclose($h);
			// $retorno = file_exists($cacheT008);
			//--------------------------------------------------------------------------
			//$retorno = apc_store("cacheT008-{$g['usuarioAtual']}", serialize($cache));
			$retorno = file_put_contents("/tmp/cacheT008-{$g['usuarioAtual']}", serialize($cache));
			//--------------------------------------------------------------------------
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        }
	    } else {
			// Recuperando dados do disco------------------------------------------------------
			// $cache = unserialize(file_get_contents($cacheT008));
			// if(!is_array($cache)){
	        // 	$resposta['code'] = false;
	        // 	$resposta['data'] = 'Erro ao recuperar arquivo com o cache T005.';
	        // 	$resposta['concluido'] = 1;
	        // 	break;
			// }
			//---------------------------------------------------------------------------------
			//$cache = unserialize(apc_fetch("cacheT008-{$g['usuarioAtual']}"));
			$cache = unserialize(file_get_contents("/tmp/cacheT008-{$g['usuarioAtual']}"));			
			//---------------------------------------------------------------------------------
			//$resposta = unserialize(apc_fetch("cacheT008-resposta-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheT008-resposta-{$g['usuarioAtual']}"));

	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao recuperar variável no cache resposta";
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

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$T008_D009_Id = $cache[$i]['T008_D009_Id'];
	    	$T008_Id = $cache[$i]['T008_Id'];
			$D006_Flag_Estoque = $cache[$i]['D006_Flag_Estoque'];
			$T008_T006_Id = $cache[$i]['T008_T006_Id'];
		
			if ($D006_Flag_Estoque == 'C') {
				$CAD002->D001_reprocessa_historico(0, $T008_D009_Id, 10);
			} else {
				$CAD002->D001_reprocessa_historico(0, $T008_D009_Id, 5, true);
			}
	    
			$retornoTotalizacao = mysql_error();
            
           	if($retorno !== true && !empty($retornoTotalizacao)){
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
       		}else{
       			$ok++;
       		}
        }
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			// Não chamar totalizacao pois já é executado no processo totalizarNotaFiscal
			$resposta['transmitirDireto'] = $resposta['transmitir'] = false;
			if($g['C031']['PDVTransmitirNFCeAutomaticamente'] == 'S') {
				$row = mysql_fetch_assoc(mysql_query("SELECT T007_Flag_ACP, T007_TX_Flag_Finalizada, T007_Protocolo_Inutilizacao, T007_Flag_Almoxarifado, T007_Numero_Nota_Fiscal, T007_Flag_PDV FROM T007 WHERE T007_Id = '{$T007_Id}'"));
				if(($row['T007_Flag_ACP'] == '6' || $row['T007_Flag_ACP'] == '8') && $row['T007_TX_Flag_Finalizada'] != '1' && empty($row['T007_Protocolo_Inutilizacao']) && $row['T007_Flag_Almoxarifado'] == 'S' && !empty($row['T007_Numero_Nota_Fiscal'])) {
					$resposta['transmitir'] = true;
					if($row['T007_Flag_PDV'] == 'S'){
						$resposta['transmitirDireto'] = true;
					}
				}
			}
			//Se for usar o arquivo de disco usa o unlink, senão usa o apc_delete
			// unlink("{$Dir}cacheT008-{$g['usuarioAtual']}.txt");
			//------------------------------------------------------------------------------
			//apc_delete("cacheT008-{$g['usuarioAtual']}");
			//apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT008-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheT008-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        //apc_delete("cacheT008-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheT008-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        //apc_store("cacheT008-resposta-{$g['usuarioAtual']}", serialize($resposta));
			file_put_contents("/tmp/cacheT008-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'finalizarNotaFiscalAtualizada':
		require_once("bibliotecas/classes/VEN003.php");
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$VEN003 = new VEN003();

		$VEN003->atualizarAliquotasNF($T007_Id);
		$retorno = $VEN003->finalizarNf($T007_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Nota Fiscal Finalizada!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'atualizarCamposProdutos':
			$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
			$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;
			/*
			T008_Ultimo_Preco(D001_Id,'$mT007[T007_D024_Id]','$T007_Id',1) as Preco_Consultado,
			T008_Ultimo_Preco(D001_Id,'$mT007[T007_D024_Id]','$T007_Id',2) as Preco_Confirmado
			*/

			$sql =	"SELECT T007_D024_Id from T007 WHERE T007_Id = '{$T007_Id}'";
			$resultado = mysql_query($sql);
			$rowT007 = mysql_fetch_assoc($resultado);

			$sql = <<<EOT
			SELECT  *
	   				from D001
	   			left join D049 on D049_D001_Id=D001_Id
	   			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$rowT007['T007_D024_Id']}'
	   			left join D009 on D009_D049_Id=D049_Id
	   			left join D082 on D082_Id=D049_D082_Id
	   			left join D041 on D041_D049_Id=D049_Id
	   			left join D005 on D005_Id=D001_D005_Id
	   				WHERE D009_Id = '{$D009_Id}'
EOT;

			$resultado = mysql_query($sql);
			$row = mysql_fetch_assoc($resultado);

			$sql = <<<EOT
			SELECT * from T008 
	   			left join T007 on T007_Id=T008_T007_Id
	   				WHERE T007_Id = '{$T007_Id}'
EOT;

			$resultado = mysql_query($sql);
			$row2 = mysql_fetch_assoc($resultado);

			// Aliquota IPI
			//$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
			$cClausula =" select *, ";
			$cClausula.=" D053_ST_ICMS_Interno,
			              T007_ICMS_Inter_Estadual=ICMS_Inter_Estadual(T007_C004_Id, T007_D024_Id, D018_Id, D024_Flag_Revenda_Consumidor, T007_Flag_ACP, 1) as ICMS_Inter_Estadual, 
			              D053_Flag_ST,
			              D018_Id,
			              T007_Flag_Acp,
			              T007_Flag_Entrada_Saida,
			              C004_Percentual_COFINS,
			              C004_Percentual_PIS,
			              C004_D018_Id,
			              C004_CFOP_ST_Venda_Dentro_Estado,
			              C004_CFOP_ST_Venda_Fora_Estado,
			              C004_CFOP_CONSUMO_Venda_Dentro_Estado,
			              C004_CFOP_CONSUMO_Venda_Fora_Estado,
			              D024_Flag_Revenda_Consumidor,
			              D024_Flag_Contribuinte, 
						  D148_D018_Id ";
			$cClausula.=" from T007 ";
			$cClausula.=" left join D024 on D024_Id=T007_D024_Id ";
			$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
			$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T007_C004_Id ";
			$cClausula.=" left join C004 on C004_Id=T007_C004_Id ";
			$cClausula.=" left join D148 on D148_Id=T007_D148_Id_Entrega ";			
			$cClausula.=" where T007_Id='{$T007_Id}'";
			$T007=mysql_query($cClausula);
			$cErro=mysql_error();
			if (!empty($cErro))
			{
			   return "$cErro/p/p$cClausula";
			   exit;
			}
			$mT007=mysql_fetch_array($T007);
			$sql = <<<EOT
				SELECT
					D009_Id,
					D009_IPI('{$mT007['T007_C004_Id']}',D009_Id,'{$mT007['T007_D024_Id']}','{$mT007['T007_D006_Id']}','1','{$mT007['T007_Flag_Acp']}') as Aliquota_IPI
				FROM
					D009
				where D009_Id='{$D009_Id}'
EOT;
			$result = mysql_query($sql);
			$row3 = mysql_fetch_assoc($result);

			$cClausula =" select * ";
		    $cClausula.=" from D006 ";
		    $cClausula.=" left join D059 on D059_Id=D006_D059_Id ";
		    $cClausula.=" where D006_Id='{$mT007['T007_D006_Id']}'";
		    $D006=mysql_query($cClausula);
		    if (!empty($cErro))
		    {
		       return "$cErro/p/p$cClausula";
		       exit;
		    }
		    $mD006=mysql_fetch_array($D006);
            $D006_Id=$mD006['D006_Id'];
            $D024_Id=$mT007['T007_D024_Id'];

			$cClausula  = " select ";
		    $cClausula .= " D009_Id, ";
		    $cClausula .= " D001_Id, ";
		    $cClausula .= " D049_Id, ";
		    $cClausula .= " D001_Codigo_Produto, ";
		    $cClausula .= " D001_Descricao_Produto, ";
		    $cClausula .= " D001_D037_Id, ";
		    $cClausula .= " D009_Valor_Custo_Unitario, ";
		    $cClausula .= " D009_Flag_Preco_Tabelado, ";
		    $cClausula .= " D041_Valor_Preco_Consumidor, ";
		    $cClausula .= " D024_Horario_Entrega, ";
		    $cClausula .= " D001_Peso_Unitario_Kg, ";
		    $cClausula .= " D001_Peso_Unitario_Kg, ";
		    $cClausula .= " D001_Peso_Total_Kg, ";
		    $cClausula .= " D001_Qte_Embalagem, ";
		    //$cClausula .= " D009_IPI('{$mT007['T007_C004_Id']}',D009_Id,'{$mT007['T007_D024_Id']}','$D006_Id','1','{$mT007['T007_Flag_Acp']}') as Aliquota_IPI, ";
		    $cClausula .= " D009_IPI('{$mT007['T007_C004_Id']}',D009_Id,'{$mT007['T007_D024_Id']}','$D006_Id','2','{$mT007['T007_Flag_Acp']}') as Percentual_Reducao_IPI, ";
		    $cClausula .= " D009_IPV_ICMS('{$mT007['T007_C004_Id']}','{$mT007['T007_D024_Id']}',D009_Id,'$D006_Id','{$mT007['D024_D018_Id']}','{$mT007['D024_Flag_Revenda_Consumidor']}','0','{$mT007['T007_Flag_Acp']}',2) as Aliquota_ICMS, ";
		    $cClausula .= " D009_IPV_ICMS('{$mT007['T007_C004_Id']}','{$mT007['T007_D024_Id']}',D009_Id,'$D006_Id','{$mT007['D024_D018_Id']}','{$mT007['D024_Flag_Revenda_Consumidor']}','0','{$mT007['T007_Flag_Acp']}',3) as Percentual_Reducao_ICMS, ";
		    $cClausula .= " D009_D005_Id(D009_Id) as D005_Id, ";
		    $cClausula .= " D049_Flag_Substituicao_Tributaria, ";
		    $cClausula .= " D049_Flag_Nacional_Importado, ";
		    $cClausula .= " D049_Origem_Mercadoria, ";
		    $cClausula .= " IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria, ";
		    $cClausula .= " D005_Flag_ST, ";
		    $cClausula .= " D005_Classificacao_Fiscal, ";
		    $cClausula .= " D088_Id, ";
		    $cClausula .= " D088_Codigo_ST, ";
		    $cClausula .= " D005_Valor_VA, ";
		    $cClausula .= " D001_Flag_Tipo ";
		    $cClausula .= " from D001 ";
		    $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
		    $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
		    $cClausula .= " left join D041 on D041_D049_Id=D049_Id ";
		    $cClausula .= " left join D024 on D024_Id='$D024_Id' ";
		    $cClausula .= " left join D005 on D005_Id=D001_D005_Id ";
		    $cClausula .= " left join D088 on D088_Id=D005_D088_Id ";
		    $cClausula .= " where D009_Id='$D009_Id'";
		    //die($cClausula);
		    $T008 = mysql_query($cClausula);
		    $mT008 = mysql_fetch_array($T008);
		    //die("Alíquota ICMS: ".$mT008['Aliquota_ICMS']);

		    require_once("bibliotecas/classes/CAD002.php");
		    $CAD002 = new CAD002();

		    require_once("bibliotecas/classes/VEN001.php");
		    $VEN001 = new VEN001();

			$contextoCFOP = [
            	'ufEnderecoEntrega' => $mT007['D148_D018_Id']
			];
		    //$D006_Id = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $D009_Id, $mT007['T007_C004_Id'], false, false, false, false, $mT007['T007_C004_Id_Estoque']);
			$D006_Id = $VEN001->retornaCFOPContexto($mT007['T007_D024_Id'], $D009_Id, $mT007['T007_C004_Id'], false, false, false, false, $mT007['T007_C004_Id_Estoque'], false, $contextoCFOP);
		    $Aliquotas = $VEN001->retornaAliquotas($D009_Id, $T003_Id=false, $T005_Id=false, $T007_Id, false, false, false, $D006_Id);

		    $Aliquota_ICMS = $CAD002->retornaICMS($mT007['T007_C004_Id'], $mT007['T007_D024_Id'], $mT008['D009_Id'], $D006_Id, $mT007['D024_D018_Id'], $mT007['D024_Flag_Revenda_Consumidor'], '0', $mT007['T007_Flag_Acp'], '2');
		    $Percentual_Reducao_ICMS = $CAD002->retornaICMS($mT007['T007_C004_Id'], $mT007['T007_D024_Id'], $mT008['D009_Id'], $D006_Id, $mT007['D024_D018_Id'], $mT007['D024_Flag_Revenda_Consumidor'], '0', $mT007['T007_Flag_Acp'], '3');

		    if($Aliquota_ICMS==$mT008['Aliquota_ICMS']){
		    	$mT008['Aliquota_ICMS'] = $Aliquota_ICMS;
		    }
		    if($Percentual_Reducao_ICMS==$mT008['Percentual_Reducao_ICMS']){
		    	$mT008['Percentual_Reducao_ICMS'] = $Percentual_Reducao_ICMS;
		    }

		    // Verificando a Situação tributária
		    if($mT008['D049_Origem_Mercadoria'] != ''){
		    	$row['Origem_Mercadoria']=$mT008['D049_Origem_Mercadoria'];

		    	if($mT008['D009_Origem_Mercadoria'] != ''){
		    		$row['Origem_Mercadoria'] = $mT008['D009_Origem_Mercadoria'];
		    	}
		    }else if(!empty($mT008['D049_Flag_Nacional_Importado'])){

		    	if($mT008['D049_Flag_Nacional_Importado'] == "N"){
		    		$row['Origem_Mercadoria']='0';
		    	}else{
		    		$row['Origem_Mercadoria']='1';
		    	}
		    }else if(!empty($mT008['D001_Flag_Tipo'])){

		    	if($mT008['D001_Flag_Tipo'] == "PRODUZIDO"){
		    		$row['Origem_Mercadoria']='1';
		    	}else{
		    		$row['Origem_Mercadoria']='0';
		    	}
		    }

            if ($mT007['T007_Flag_Entrada_Saida']=="S"){
			    if ($row3['Aliquota_IPI']>0)
			    {
					$mT008['D088_Codigo_ST']='50';
			    }else{
				    $mT008['D088_Codigo_ST']='53';
				}
			}

			$row['Aliquota_IPI'] = !isset($Aliquotas['Aliquota_IPI']) ? '0,00' : number_format($Aliquotas['Aliquota_IPI'],2,',','.');
			$row['Situacao_Tributaria_IPI'] = $Aliquotas['Situacao_Tributaria_IPI'];
			$row['C004_Percentual_COFINS'] = gCorrigeNumero($Aliquotas['Aliquota_COFINS']);
			$row['C004_Percentual_PIS'] = gCorrigeNumero($Aliquotas['Aliquota_PIS']);
			$row['D006_Situacao_Tributaria_COFINS'] = $Aliquotas['Situacao_Tributaria_COFINS'];
			$row['D006_Situacao_Tributaria_PIS'] = $Aliquotas['Situacao_Tributaria_PIS'];
			$row['D059_Id'] = $Aliquotas['D059_Id'];
			$row['D005_Id'] = $Aliquotas['D005_Id'];
			$row['Aliquota_ICMS'] = gCorrigeNumero($Aliquotas['Aliquota_ICMS']);
			$row['Percentual_Reducao_ICMS'] = $Aliquotas['Percentual_Reducao_ICMS'];
			$row['ST_ICMS_Interno'] = $Aliquotas['ST_ICMS_Interno'];
			$row['T008_ST_ICMS_Inter_Estadual'] = $Aliquotas['ICMS_Inter_Estadual'];
			$row['T008_ST_VA'] = $Aliquotas['ST_VA'];
			$row['T008_Flag_Isento_PIS'] = $Aliquotas['Flag_Isento_PIS'];
			$row['T008_Flag_Isento_COFINS'] = $Aliquotas['Flag_Isento_COFINS'];
			$row['D149_Id'] = $Aliquotas['D149_Id'];
			$row['D001_Codigo_Produto'] = $mT008['D001_Codigo_Produto'];

			$row['D006_Id'] = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $D009_Id, $mT007['T007_C004_Id'], false, false, false, false, $mT007['T007_C004_Id_Estoque']);

			// Buscando o preço do produto
			$dadosPrecoVenda 		= $CAD002->retornaPrecoVenda($row['D009_Id'], '1', $mT007['T007_D024_Id'], false, $row['D006_Id'], $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
			$row['D041_Valor_Base'] = $dadosPrecoVenda[0];

			// Buscando o percentual de tributos (lei transparências)
		    $D149 = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$row['D149_Id']}'");
		    $mD149 = mysql_fetch_array($D149);

		    $D006 = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$row['D006_Id']}'");
		    $mD006 = mysql_fetch_array($D006);

		    if(!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0){
		    	$row2['T008_Percentual_Trib_Transparencia'] = $mD149['D149_Percentual_Trib_Transparencia'];
		    }else if(!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0){
		    	$row2['T008_Percentual_Trib_Transparencia'] = $mD006['D006_Percentual_Trib_Transparencia'];
		    } else {
		    	$row2['T008_Percentual_Trib_Transparencia'] = 0;
		    }
		    $row2['T008_Percentual_Trib_Transparencia'] = gCorrigeNumero($row2['T008_Percentual_Trib_Transparencia']);

			// pode ser vazio
			$row2 = empty($row2) ? array() : $row2;
			if (!empty($row)) {
				$resposta['code'] = true;
				$resposta['data'] = array_merge($row, $row2);
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = "Erro SQL: $sql";
			}

		break;

        case 'atualizarAliquota':
			require_once("bibliotecas/classes/VEN003.php");

			$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

			$VEN003 = new VEN003();
			$retorno = $VEN003->atualizarAliquotasNF($T007_Id);
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Atualizado!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		break;

		case 'carregaCFOP':
			global $g;

			$T007_D024_Id = isset($r_T007_D024_Id) ? $r_T007_D024_Id : false;
			if (empty($T007_D024_Id)) {
				$resposta['code'] = false;
				$resposta['data']['erro'] = "Nenhum cliente selecionado";
			} else {
				$sql = "SELECT D024_D006_Id, 
				               D024_D018_Id,
				               D024_Flag_Revenda_Consumidor,
				               D024_Flag_Contribuinte
				          FROM D024 WHERE D024_Id=" . $T007_D024_Id;
				$result = mysql_query($sql);
				$aD024 = mysql_fetch_assoc($result);
				echo mysql_error();

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
				require_once("bibliotecas/classes/VEN001.php");
				$VEN001 = new VEN001();
				$D006_Id=$VEN001->retornaCFOP($T007_D024_Id);

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
					$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Venda_Compra_Outros="V" AND D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true, $D006_Id);
					$resposta['data']['T007_D006_Id'] = gProcessaSelect($arrayDentroEstado);
				}
				// se nenhuma regra tiver sido satisfeita (critério novo e antigo)
				else if ($D006_Id===false)
				{
					$resposta['data']['T007_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE D006_Flag_Venda_Compra_Outros="V" AND D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true));
				}
				// se mais de uma regra tiver sido satisfeita
				else{
					//$resposta['code'] = false;
					//$resposta['data']['erro'] = $D006_Id;
				}
			}
		break;

	case 'inserirParcela':
		require_once("bibliotecas/classes/VEN001.php");
		$form_tabela = (isset($r_form_tabela)) ? $r_form_tabela : false;
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;
		$form_campo_pk = (isset($r_form_campo_pk)) ? $r_form_campo_pk : false;
		$dadosTabela = array('tabela' => $form_tabela, 
							 'tabelaId' => 	$T007_Id,
							 'campos' => 'T007_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                 T007_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                 T007_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                 T007_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                 T007_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                 T007_D027_Id as D027_Id,
                                 T007_Pgto_Parcelas as Total_Parcelas,
                                 T007_Valor_Total as valorTotal', 
                             'where' => $form_campo_pk."='{$T007_Id}'", 
                             'tabelaPgto' => 'T118', 
                             'camposPgto' => 'T118_Prazos_Dias as prazoDias,T118_D027_Id as D027Pagamento,T118_Numero_Parcela as numeroParcela, T118_Data_Vencimento as dataVencimento', 
                             'wherePgto' => "T118_T007_Id='{$T007_Id}'");

		$VEN001 = new VEN001();		
		$retorno = $VEN001->inserirParcela($dadosTabela);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da parcela...<br>'.$retorno;
		}
	break;

	case 'salvarObservacao':
		require_once("bibliotecas/classes/VEN003.php");

		$form=$r_formVen003FormPagamentos;
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;
		$VEN003 = new VEN003();
		$retorno = $VEN003->gravarObservacao($form, $T007_Id);

		if ($retorno === false) {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha na inclusão da Observação. Por favor, tente novamente';
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;

	case 'calcularParcelaSimulacao':
		require_once("bibliotecas/classes/VEN003.php");

		$form=$r_formVen003FormPagamentos;
		$VEN003 = new VEN003();
		$retorno = $VEN003->calcularParcelaSimulacao($form);

		if ($retorno === false) {
			$resposta['code'] = false;
			$resposta['data'] = 'Falha no calculo da parcela...';
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno;
		}
	break;

	case 'procuraCliente':
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$sql = "SELECT * FROM D024 WHERE D024_Id='{$D024_Id}' AND D024_Flag_Pre_Cadastro != 'S'";
		log("SQL: ".$sql);
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);

		if (mysql_num_rows($result) > 0) {
			$resposta['code'] = true;
			$resposta['data'] = $linha;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'marcarEnviarNota':
		mysql_query("UPDATE T007 SET T007_Flag_Enviar = 'S' WHERE T007_Id = '{$_REQUEST['T007_Id']}'");
		$resposta['code'] = true;
	break;

	case 'alterarModeloNota':		
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;
		
		require_once("bibliotecas/classes/VEN012.php");
		$VEN012 = new VEN012();
		$retorno = $VEN012->retornaProximaNF('6',$g['empresaAtual']);

		list($proximoNumero,$serie) = $retorno;

		if($proximoNumero === false){

			$resposta['code'] = false;
			$resposta['data'] = $serie;

		}else{

			// Update no T007
			mysql_query("UPDATE T007 SET T007_Flag_ACP 			 = '6',
										 T007_Flag_Almoxarifado  = 'N', 
										 T007_Numero_Nota_Fiscal = '{$proximoNumero}', 
										 T007_Serie              = '{$serie}',
										 T007_Versao_NFe         = '4.00' 
								   WHERE T007_Id				 = '{$T007_Id}'");
			
			// Update do novo numero da nota no C004
			mysql_query("UPDATE C004 SET C004_Ultimo_Numero_Nota_Fiscal_Eletronica = '{$proximoNumero}'
		    					   WHERE C004_Id = '{$g['empresaAtual']}'");

			$resposta['code'] = true;
			$resposta['data'] = 'ACP alterada para NFe '.$proximoNumero.' com sucesso';
		}

	break;

	case 'atualizarCFOP':
		require_once("bibliotecas/classes/VEN003.php");
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		$VEN003 = new VEN003();
		$retorno = $VEN003->ven003atualizarCFOP($T007_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Atualizado!";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'excluirNota':
		require_once("bibliotecas/classes/VEN003.php");
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		$VEN003 = new VEN003();
		$retorno = $VEN003->ven003excluirNota($T007_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'verificarDescontoCupom':
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$retorno = $VEN003->percentualDescontoCupom($r_T007_Id);
		$retorno = gCorrigeNumeroInverte(gCorrigeNumero($retorno));
		// 5% de desconto
		if ($retorno >= 2) {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
			$resposta['percentual'] = gCorrigeNumero($retorno);
		}
	break;

	case 'verificarAutorizacaoCupom':
		$r_login = mysql_real_escape_string($r_login);
		$r_pass = (string) $r_pass;
		$sql = <<<EOT
SELECT 
	C019_C021_Id,
	C007_Senha
FROM C007 
LEFT JOIN C019 ON C019_C007_Id = C007_Id
WHERE C007_Login = '{$r_login}' AND C007_Ativo = 'S'
EOT;
		$res = mysql_query($sql);
		$row = $res ? mysql_fetch_assoc($res) : null;
		if (!$row || !SegurancaSenha::validarSenhaArmazenada($r_pass, $row['C007_Senha'])) {
			$resposta['code'] = false;
			$resposta['data'] = 'Usuário ou senha inválidos.';
		} else {
			$res = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$row['C019_C021_Id']}' AND C030_Acesso_Liberado = 'S' AND C030_C029_Id = 177;");
			if (!mysql_num_rows($res)) {
				$resposta['code'] = false;
				$resposta['data'] = 'Usuário sem permissão para autorizar este desconto.';
			}
		}
	break;

	case 'finalizarCupom':
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();

		$r_T007_Id = mysql_real_escape_string($r_T007_Id);

		$sql = <<<EOT
SELECT
	T008_D009_Id
FROM T008
WHERE
	T008_T007_Id = '{$r_T007_Id}'
EOT;
		$res = mysql_query($sql);
		while($row = mysql_fetch_assoc($res)) {
			$CAD002->D001_reprocessa_historico(0,$row['T008_D009_Id']);
		}
	break;

	case 'verificarStatusPedido':
/*
T005_Flag_Status,
T005_T007_Id,
T005_Flag_Acp,
*/
		$sql = <<<EOT
SELECT
	*,
	T005_Status_Pedido(T005_Flag_Status, 1) as Status_Pedido
FROM T005
left join T007 on T007_T005_Id = T005_Id
WHERE
T005_Id = '{$r_codbarra}'
EOT;
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0) {
			$row = mysql_fetch_assoc($res);
			$resposta['data'] = $row;
			if ($row['T005_Flag_Status'] != 8 && $row['T005_Flag_Status'] != 5 && empty($row['T007_Id']) && ($row['T005_Flag_Acp'] = 4 || $row['T007_Flag_Expedicao'] = 2) && $row['T005_Flag_Status'] != 0 && $row['T005_Flag_Status'] != 6) {
				$resposta['data'] = $row['T005_Id'];
			} else {
				$resposta['code'] = false;
				$resposta['data'] = 'Impossível abrir pedido com o status: ' . $row['Status_Pedido'];
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Pedido não encontrado no sistema.';
		}

	break;

	case 'verificaAlcada':
		global $g;

		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$pedirLiberacao = isset($r_pedirLiberacao) ? $r_pedirLiberacao : false;
		$retorno = $VEN003->verificaAlcada($T007_Id, $pedirLiberacao);
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;


	case 'atualizarComissaoRepresentante':
		$D021_Id = isset($r_D021_Id) ? $r_D021_Id : false;

		$sqlD021 = mysql_query("SELECT D021_Percentual_Comissao FROM D021 WHERE D021_Id = '{$D021_Id}'");
		if(mysql_num_rows($sqlD021) > 0){
			$rowD021 = mysql_fetch_array($sqlD021);
			$resposta['code'] = true;
			$resposta['data'] = gCorrigeNumero($rowD021['D021_Percentual_Comissao']);
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'atualizarCamposProdutosConsumidor':
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();

		$D009_Id = isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;
		$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;

		$sql =  "SELECT * from T007 WHERE T007_Id = '{$T007_Id}'";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		$T007_D024_Id = $row['T007_D024_Id'];
		$T007_D006_Id = $row['T007_D006_Id'];
		$T007_Prazo_Entrega = $row['T007_Prazo_Entrega'];
		$T007_Flag_Destino_Produto = $row['T007_Flag_Destino_Produto'];

		$sql = <<<EOT
		SELECT  *
		      from D001
		    left join D049 on D049_D001_Id=D001_Id
		    left join D009 on D009_D049_Id=D049_Id
			left join D083 on D083_D001_Id=D001_Id AND D083_D024_Id = '{$row['T007_D024_Id']}'
		    left join D082 on D082_Id=D049_D082_Id
		    left join D041 on D041_D049_Id=D049_Id
		    left join D005 on D005_Id=D001_D005_Id
		    left join D069 on D069_Id=D009_D069_Id
		    left join D070 on D070_Id=D069_D070_Id
		    WHERE D009_Id = '{$D009_Id}'
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		if($row['D009_Origem_Mercadoria'] != ''){
            $row['D049_Origem_Mercadoria'] = $row['D009_Origem_Mercadoria'];
        }

		$row['estoqueLiquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['estoqueLiquido2'] = $row['D009_Quantidade_Estoque_Liquido'];
		$row['estoqueLoja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);
		$row['estoqueTotal'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']+$row['D009_Quantidade_Estoque_Loja']);
		$row['ordemCompra'] = gCorrigeNumero($row['D009_Quantidade_OC']);
		$row['estoqueFora'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Fora']);

		$row['validadePrecoTabela'] = 'true';
		if(strtotime($row['D070_Data_Vencimento']) < strtotime(date("Y-m-d"))){
			$row['validadePrecoTabela'] = 'false';
		}

		$cClausula =" select *, ";
		$cClausula.=" D053_ST_ICMS_Interno,
		              D053_ST_ICMS_Inter_Estadual,
		              D053_Flag_ST,
		              D018_Id,
		              C004_D018_Id,
		              C004_CFOP_ST_Venda_Dentro_Estado,
		              C004_CFOP_ST_Venda_Fora_Estado,
		              C004_CFOP_CONSUMO_Venda_Dentro_Estado,
		              C004_CFOP_CONSUMO_Venda_Fora_Estado,
		              D024_Flag_Contribuinte,
						T007_Flag_Destino_Produto";
		$cClausula.=" from T007 ";
		$cClausula.=" left join D024 on D024_Id=T007_D024_Id ";
		$cClausula.=" left join D018 on D018_Id=D024_D018_Id ";
		$cClausula.=" left join D053 on D053_D018_Id=D024_D018_Id and D053_C004_Id=T007_C004_Id ";
		$cClausula.=" left join C004 on C004_Id=T007_C004_Id ";
		$cClausula.=" where T007_Id='{$T007_Id}'";
		$T007=mysql_query($cClausula);
		$cErro=mysql_error();
		if (!empty($cErro)) {
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $cErro";
			break;
		}
		$mT007=mysql_fetch_array($T007);

		$row['T007_Flag_Destino_Produto'] = $mT007['T007_Flag_Destino_Produto'];

		$cfop      = $VEN001->retornaCFOP($mT007['T007_D024_Id'], $row['D009_Id'], $mT007['T007_C004_Id'], $T007_Flag_Destino_Produto, false, false, false, $mT007['T007_C004_Id_Estoque']);
		$aliquotas = $VEN001->retornaAliquotas($D009_Id, false, false, $T007_Id, false, false, false, $cfop);
		// verifica se está setado a flag lista preço do cabeçalho do orçamento, se estiver, manda ela, senão manda o id do cliente
		$preco = $CAD002->retornaPrecoVenda($row['D009_Id'], $mT007['T007_Flag_Lista_Preco'], $mT007['T007_D024_Id'], $mT007['T007_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], 0, 0, $mT007['T007_Casas_Decimais_Preco'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], $aliquotas['ST_VA'], $aliquotas['Percentual_Reducao_ICMS_ST'], $aliquotas['ST_ICMS_Interno'], $aliquotas['Percentual_Reducao_ICMS'], $aliquotas['ICMS_Inter_Estadual'], $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		$precoTabela = $CAD002->cad002RetornaPrecoTabela($row['D009_Id'], $mT007['T007_Flag_Lista_Preco'], $mT007['T007_D024_Id'], $mT007['T007_Flag_Acp'], $cfop, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI'], 0, 0, $aliquotas['Aliquota_IRPJ'], $aliquotas['Aliquota_CSLL']);
		$row['precoTabela'] = $precoTabela[0];
		$row['Preco_Produto'] = $preco[0];
		$precoCalc = gCorrigeNumeroInverte($preco[0]);
		$percentualPrecoMinimo = $g['C031']['percentualPrecoMinimoVenda']/100;
		$row['PrecoMinimo'] = gCorrigeNumero($precoCalc - ($precoCalc*$percentualPrecoMinimo), 4);

		global $g;
		// Método que irá retonar o último preço que o produto foi vendido para o cliente
		$row['ultimoPrecoVenda'] = $VEN001->retornaltimoPrecoVenda($mT007['T007_D024_Id'], $row['D009_Id'], $T007_Id);

		$row['Aliquota_IPI'] = $aliquotas['Aliquota_ICMS'];

		$row['Aliquota_IPI'] = !isset($row2['Aliquota_IPI']) ? '0,00' : number_format($row2['Aliquota_IPI'],2,',','.');

		$row['D006_Id'] = $cfop;

		if($row['D006_Id'] <= 0 || $row['D006_Id'] === false){
			$row['D006_Id'] = $T007_D006_Id;
		}
		$row['T007_Prazo_Entrega'] = $T007_Prazo_Entrega;

		require_once('bibliotecas/funcoesCallback/infoCustoVendaProduto.php');
		$row['infoCustoVenda'] = infoCustoVendaProduto(false, array($row['D009_Data_Alteracao_Custo'], $row['D009_Data_Ultima_Venda'], $row['D009_Flag_Custo_Utilizado']), 'S');

		if($g['C031']['utilizarDescProdutoUltimaVenda'] == 'S'){
			$sqlT007 = mysql_query("SELECT T008_Descricao_Produto FROM T008 LEFT JOIN T007 ON T007_Id = T008_T007_Id WHERE T007_D024_Id ='{$T007_D024_Id}' AND T008_D009_Id = '{$row['D009_Id']}' ORDER BY T008_Id DESC LIMIT 1");
			if(mysql_num_rows($sqlT007) > 0){
				$resT007 = mysql_fetch_array($sqlT007);
				if($row['D001_Descricao_Produto'] != $resT007['T008_Descricao_Produto']){
					$row['D001_Descricao_Produto'] = $resT007['T008_Descricao_Produto'];
				}
			}
		}

		$row['campoFocus'] = $g['C031']['editarDescricaoProdutoVenda']=='S' ? 'T008_Descricao_Produto' : 'T008_Quantidade';
		if (!empty($row)) {
			$resposta['code'] = true;

			$params = array(
				"D009_Id" => $D009_Id,
				"T007_Id" => $T007_Id,
			);
			$row = $VEN001->ven001ModPreencheFormProdutosNota($row, $params);
			$resposta['data'] = $row;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = "Erro SQL: $sql";
		}
	break;

	case 'reimportarCupomFiscal':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		require_once("bibliotecas/classes/VEN003.php");
		$VEN003 = new VEN003();
		$retorno = $VEN003->reimportarCupomFiscal($T007_Id);

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'recuperarCupomFiscal':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		
		$retorno = true;

		if($g['nivelAlcada'] >= '4'){
			mysql_query("UPDATE T007 SET T007_Flag_Cancelada='N', T007_Flag_Calcular='S' WHERE T007_Id={$T007_Id}");
			$erro = mysql_error();

			if($erro){
				$retorno = false;
			}

		}else{
			$retorno = false;
			$erro = "É necessário perfil diretor para recuperar o cupom fiscal";
		}

		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = $erro;
		}
	break;

	case 'habilitarEdicaoNota':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$T007=mysql_query("SELECT T007_Chave_Acesso_Nfe from T007 WHERE T007_Id='{$T007_Id}'");
		$mT007=mysql_fetch_array($T007);
		$erro='';
		if(!empty($mT007['T007_Chave_Acesso_Nfe'])){
			$erro = 'A nota fiscal já possui chave de acesso, para habilitar a edição favor acessar a aba "técnico" e apagar a chave de acesso preenchida';
		} else {
			mysql_query("UPDATE T007 SET T007_Flag_Travar_Nota = 'N' WHERE T007_Id = '{$T007_Id}'");
			if(mysql_error()){
				$erro = 'Erro ao habilitar a nota para edição, por favor entre em contato com o suporte técnico. Erro: ' . mysql_error();
			}
		}
		if(!empty($erro)){
			$resposta['data'] = $erro;
			$resposta['code'] = false;
		} else {
			$resposta['code'] = true;
		}
	break;

	// Função criada para realizar o download do XML da nota, quando está não tiver sido autorizado pela SEFAZ.
	case 'downloadXMLNaoAutorizado':
			$T007_Id = isset($_REQUEST['T007_Id']) ? $_REQUEST['T007_Id'] : false;

			$sql = 'select T007_Numero_Nota_Fiscal, T007_Serie, T007_Numero_Protocolo_Nfe, T007_Chave_Acesso_Nfe from T007 WHERE T007_Id ='.$T007_Id;
			$result = mysql_query($sql);
			$array = mysql_fetch_assoc($result);


			$cAno_Mes_NFe=substr($array['T007_Data_Emissao'],0,7);
			$Diretorio_NFe=$g['pathDadosAntigo'] . 'nfe';
			$cArquivoAssinado="$Diretorio_NFe/xml/assinado/";
			$cArquivoPre="$Diretorio_NFe/xml/pre/";

			// Verificação realizada pois ao gerar o XML o nome do arquivo corresponde ao número da nota, ao assinar o XML o nome do arquivo corresponde a chave de acesso
			// A maior relevância é o arquivo assinado por isso é feita a primeira verificação com ele.
			if(file_exists($cArquivoAssinado . "NFe" . $array['T007_Chave_Acesso_Nfe'] . ".xml")){
				$nomeArquivo = $cArquivoAssinado . "NFe" . $array['T007_Chave_Acesso_Nfe'] . ".xml";
				$resposta['code'] = true;
			}else if(file_exists($cArquivoAssinado . $array['T007_Numero_Nota_Fiscal'] . "_" . $array['T007_Serie']  . ".xml")){
				$nomeArquivo = $cArquivoAssinado . $array['T007_Numero_Nota_Fiscal'] . "_" . $array['T007_Serie']  . ".xml";
				$resposta['code'] = true;
			}else if(file_exists($cArquivoPre . $array['T007_Numero_Nota_Fiscal'] . "_" . $array['T007_Serie']  . ".xml")){
				$nomeArquivo = $cArquivoPre . $array['T007_Numero_Nota_Fiscal'] . "_" . $array['T007_Serie']  . ".xml";
				$resposta['code'] = true;
			}else{
				$resposta['code'] = false;
				echo "Não foi gerado o XML dessa nota fiscal. <br/>O arquivo " . $nomeArquivo . " não existe na pasta " .  $cArquivo;;
				die();
			}

			if($resposta['code']){
				header('Content-type: application/x-msdownload');
				header('Content-Disposition: attachment; filename='.basename($nomeArquivo));
				header('Pragma: no-cache');
				header('Expires: 0');
				echo file_get_contents($cArquivo . $nomeArquivo);
				die();
			}
			
	break;

	case 'carregaDestinoProduto':
            
        global $g;
        
        $T007_D024_Id = isset($r_T007_D024_Id) ? $r_T007_D024_Id : false;
		
		if (empty($T007_D024_Id)) {
			$resposta['code'] = false;
			$resposta['data']['erro'] = "Nenhum cliente selecionado";
		} else {
			$sql = "SELECT D024_Flag_Destino_Produto,
						   IFNULL(D024_D021_Id,0) AS D024_D021_Id, 
						   IFNULL(D021_Percentual_Comissao,0) AS D021_Percentual_Comissao
					  FROM D024 
				 LEFT JOIN D021 ON D021_Id = D024_D021_Id
					 WHERE D024_Id=" . $T007_D024_Id;
			$D024 = mysql_query($sql);
			$mD024 = mysql_fetch_assoc($D024);
			
			echo mysql_error();
			
			if(!empty($mD024['D024_Flag_Destino_Produto'])) {
				$resposta['data']['T007_Flag_Destino_Produto'] = $mD024['D024_Flag_Destino_Produto'];
			}			
			
			if($mD024['D024_D021_Id'] > 0){
				$resposta['data']['T007_D021_Id'] = gProcessaSelect(gGeraSelect('D021','D021_Id','D021_Nome_Empresa', 'ORDER BY D021_Nome_Empresa',true , $mD024['D024_D021_Id']));
				$resposta['data']['T007_Percentual_Comissao_Representante'] = gCorrigeNumero($mD024['D021_Percentual_Comissao']);
			}
		}
        
    break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
