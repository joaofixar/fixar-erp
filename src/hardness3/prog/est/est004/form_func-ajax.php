<?php
namespace hardness;
/**
* VEN - VEN001 - Cadastro de Orçamentos
*
* Processamento de ações AJAX
*/
$resposta = array('code' => true, 'data' => array());
require_once("bibliotecas/classes/EST004.php");
/**
* Ações
*/
switch($g['r'][3]) {
	
	case 'atualizarCamposProdutosNota':
		global $g;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		
		// $EST004 = new EST004();		
		// $retorno = $EST004->incluirProdutoNota($D009_Id, $T013_Id);				
		$cClausula = <<<EOT
			SELECT 			
				D009_Id, 
				D001_D037_Id, 
				D001_Codigo_Produto,   
				D049_Codigo_Produto_Fornecedor, 
				D001_Descricao_Produto, 
				D037_Unidade, 
				D037_Id, 
				D005_Classificacao_Fiscal, 				
				D005_Id,
                D049_Origem_Mercadoria				
			from D009 
			left join D049 on D049_Id=D009_D049_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D037 on D037_Id=D001_D037_Id 
			left join D005 on D009_D005_Id(D009_Id)=D005_Id 
            left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			where
				D009_Id = '{$D009_Id}' 
EOT;
        $row  = mysql_query($cClausula);
	
		if (mysql_num_rows($row) > 0) {

			$retorno = mysql_fetch_assoc($row);

			$T013 = mysql_query("SELECT T013_D006_Id
								   FROM T013
								  WHERE T013_Id = '{$T013_Id}'");
			
			$mT013 = mysql_fetch_array($T013);

			if($mT013['T013_D006_Id'] > 0 ){
				$retorno['D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Venda_Compra_Outros != "V" ORDER BY D006_Codigo_CFOP',true, $mT013['T013_D006_Id']));
			} else {
				$retorno['D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Venda_Compra_Outros != "V" ORDER BY D006_Codigo_CFOP',true));
			}

			$retorno['T014_T066_Id'] = gProcessaSelect(gGeraSelect('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY D004_Local", true));			
			$retorno['T014_T066_Id_refreshBtn'] = base64_encode(serialize(array('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$D009_Id}' ORDER BY D004_Local",true)));						

			$resposta['code'] = true;
			$resposta['data'] = $retorno;

		} else {			
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao consultar o produto: $cClausula";
		}
		
	break;
	
	case 'atualizarCamposProdutosAssociar':
		$T008_Id= isset($_REQUEST['T008_Id']) ? $_REQUEST['T008_Id'] : false;
		$T008_Id= mysql_real_escape_string($T008_Id);
		
		/*
		T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
		T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
		*/
		
		$sql = "SELECT * FROM T008 WHERE T008_Id = '{$T008_Id}'";

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

	case 'campoProcuraProdutoAssociarItem':
		$D009_Id= isset($_REQUEST['D009_Id']) ? $_REQUEST['D009_Id'] : false;

		$sql = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto, D009_Id 
							FROM D009 
							LEFT JOIN D049 ON D049_Id=D009_D049_Id 
							LEFT JOIN D001 ON D001_Id=D049_D001_Id
							WHERE D009_Id = '{$D009_Id}'");
		$erro = mysql_error();
		$row = mysql_fetch_array($sql);

		if(empty($erro)){
			$resposta['code'] = true;
			$resposta['data'] = $row;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Erro: $erro";
		}
	break;
	
	case 'finalizarNotaEntrada':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
			
		$EST004 = new EST004();
		
		$retorno = $EST004->finalizarNotaCompras($T013_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'totalizarNotaEntrada':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		$resposta['T013_Id'] = $T013_Id;
		log("T013_Id= ".$T013_Id);
			
		$EST004 = new EST004();
	
		if ($r_inicio==1){
			log("vai chamar o call T013_Gravar_Totalizacao com o T013_Id= ".$T013_Id);
			mysql_query("call T013_Gravar_Totalizacao('{$T013_Id}')");
			$sqlCache = mysql_query("SELECT T014_Id FROM T014 where T014_T013_Id = '{$T013_Id}'");
			$count = 0;
		    while ($rowCache = mysql_fetch_array($sqlCache)) {
		    	$cache[$count]['T014_Id'] = $rowCache['T014_Id'];	
				$count++;
				log("T014_Id= ".$rowCache['T014_Id']);
				log("contador de produtos= ".$count);
		    }
		    $resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheT013-{$g['usuarioAtual']}");
			apc_delete("cacheT013-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT013-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        }
	    } else {
			$cache = unserialize(apc_fetch("cacheT013-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT013-resposta-{$g['usuarioAtual']}"));
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

		log("limit1 T014= ".$resposta['limit1']);
		log("limit2 T014= ".$resposta['limit2']);
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$T014_Id = $cache[$i]['T014_Id'];
	    	log("T014_Id do for= ".$T014_Id);
	    	log("vai chamar o T014_Gravar_Totalizacao - T014_Id= ".$T014_Id);
	    	mysql_query("CALL T014_Gravar_Totalizacao('{$T014_Id}')");
	    	$cErro = mysql_error();
	        if (!empty($cErro)) {
	           	$erro++;
           	 	$erros .= $erro ." - ". $cErro."<br />";
	        }else{
	        	$ok++;
	        }
        }
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){
			apc_delete("cacheT013-{$g['usuarioAtual']}");
			apc_delete("cacheT013-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT013-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT013-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;
	
	case 'preencheCamposFrete':
		
		global $g;
		
		$T084_Numero_Conhecimento = isset($r_T084_Numero_Conhecimento) ? $r_T084_Numero_Conhecimento : false;
		$sql = "SELECT * FROM T084 LEFT JOIN D022 ON D022_Id=T084_D022_Id left join D036 on D036_Id = D022_D036_Id left join D024 on D024_Id = T084_D024_Id WHERE T084_Numero_Conhecimento={$T084_Numero_Conhecimento}";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		
		$resposta['code'] = true;
		$resposta['data'] = $row;
		$resposta['data']['T084_Valor_Conhecimento'] = gCorrigeNumero($row['T084_Valor_Conhecimento']);
		
		
		/*
		$array3 = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$D006_Id.' ORDER BY D006_Codigo_CFOP');
		
		if(!empty($array3)){
			$resposta['data']['T003_D006_Id'] = gProcessaSelect($array3);
			
		}else if($aD024['D024_D018_Id'] == $aC004['C004_D018_Id']){
			$arrayDentroEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Dentro_Estado']);
			$resposta['data']['T003_D006_Id'] = gProcessaSelect($arrayDentroEstado);
			
		}else if($aD024['D024_D018_Id'] != $aC004['C004_D018_Id']){
			$arrayForaEstado = gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Id = '.$aC004['C004_CFOP_Venda_Fora_Estado']);
			$resposta['data']['T003_D006_Id'] = gProcessaSelect($arrayForaEstado);
		}else{
			$resposta['data']['T003_D006_Id'] = gProcessaSelect(gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' ORDER BY D006_Codigo_CFOP',true));
		}
		$resposta['data']['T003_D013_Id'] = gProcessaSelect(gGeraSelect('D013','D013_Id','D013_Nome_Contato',"WHERE D013_D024_Id='{$T003_D024_Id}'",true));	
		*/
		
	break;
	
	case 'Calcular':
		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($_REQUEST['T013_Id']) ? $_REQUEST['T013_Id'] : false;
		
		$EST004 = new EST004();
		
		//$T005_Id = 0;
		$retorno = $EST004->Calcular($T013_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Cálculo concluído";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'est004AtualizarEstoqueProdutosAjax':

		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($_REQUEST['T013_Id']) ? $_REQUEST['T013_Id'] : false;
		
		$EST004 = new EST004();
		
		//$T005_Id = 0;
		$retorno = $EST004->EST004AtualizarEstoqueProdutos($T013_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Estoque atualizado com sucesso.";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;
	
	case 'finalizarNotaEntradaEstoque':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
					
		$EST004 = new EST004();
		
		$retorno = $EST004->finalizarNotaEstoque($T013_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Estoque: Nota finalizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'verificaReprocessaHistorico':

		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;

		$T014 = mysql_query("SELECT T014_D009_Id,
								    D009_Data_Reprocessa_Historico,
									T001_Data_Hora_Insercao,
									T001_Quantidade_Saldo,
									IFNULL(D009A_Id,0) as D009A_Id
							   FROM T014
						  LEFT JOIN T001 ON T014_Id = T001_T014_Id
						  LEFT JOIN D009 ON D009_Id = T014_D009_Id
						  LEFT JOIN D009A ON D009_Id = D009A_D009_Id
						  LEFT JOIN D006 ON D006_Id = T014_D006_Id
						      WHERE T014_T013_Id = '{$T013_Id}'
							    AND D006_Flag_Estoque = 'C'");
		
		while($mT014 = mysql_fetch_array($T014)){
			if($mT014['D009_Data_Reprocessa_Historico'] >= $mT014['T001_Data_Hora_Insercao'] && $mT014['T001_Quantidade_Saldo'] > 0){
				continue;
			} else {
				//Flag utilizada no processo automático para atualizar estoque
				if($mT014['D009A_Id'] > 0){
					mysqli_query("UPDATE D009A SET D009A_Flag_Atualizar_Estoque = 'S'
										     WHERE D009A_Id = '{$mT014['D009A_Id']}'");
				} else {
					mysqli_query("INSERT INTO D009A (D009A_D009_Id, 
											 		 D009A_Flag_Atualizar_Estoque
											)VALUES('{$mT014['T014_D009_Id']}',
													'S')");
				}
			}
		}	

		$resposta['code'] = true;
	
	break;

	case 'reprocessaHistoricoNotaEntradaEstoque':
		global $g;
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;	

		// Classe produtos
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

		if ($r_inicio == 1){
			$sqlCache = mysql_query("SELECT T014_D009_Id,
									D009_D049_Id	
                                  FROM T014
                             LEFT JOIN D009 ON D009_Id=T014_D009_Id
                             LEFT JOIN D049 ON D049_Id=D009_D049_Id
                             LEFT JOIN D001 ON D001_Id=D049_D001_Id
                             LEFT JOIN D006 ON D006_Id=T014_D006_Id
                             LEFT JOIN T001 ON T001_T014_Id=T014_Id
                                 WHERE T014_T013_Id='{$T013_Id}'");
			$erro = mysql_error();
			if(!empty($erro)){
				$resposta['code'] = false;
				$resposta['data'] = "Erro: " . $erro;
			}
			$count = 0;

		    while ($rowCache = mysql_fetch_array($sqlCache)) {
		    	$cache[$count]['D009_Id'] = $rowCache['T014_D009_Id'];	
				$count++;
		    	//select dos produtos da filial se a global estiver setado como SIM para reprocessar histórico de todas as empresas
				if($g['C031']['reprocessarHistoricoTodasEmpresas'] == 'S'){
					$Item_Filial = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$rowCache['D009_D049_Id']}' and D009_C004_Id!={$g['empresaAtual']}");
					while ($mItemFilial = mysql_fetch_array($Item_Filial)) {
				    	$cache[$count]['D009_Id'] = $mItemFilial['D009_Id'];	
						$count++;
					}
			    }		
		    }
		    $resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheT014-{$g['usuarioAtual']}");
			apc_delete("cacheT014-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT014-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        }
	    } else {
			$cache = unserialize(apc_fetch("cacheT014-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT014-resposta-{$g['usuarioAtual']}"));
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
	    	$D009_Id = $cache[$i]['D009_Id'];
			$retorno = $CAD002->D001_reprocessa_historico(0, $D009_Id, 20);			
	        if (!($retorno)) {
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
			apc_delete("cacheT014-{$g['usuarioAtual']}");
			apc_delete("cacheT014-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT014-resposta-{$g['usuarioAtual']}");
	       	$resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT014-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        
	break;

	case 'finalizarNotaEntradaFinanceiro':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;

		$cSql_T014 = mysql_query("SELECT T014_Id, 
									     T014_Codigo_Produto, 
										 T014_Descricao_Produto,
										 D006_Codigo_CFOP 
								    FROM T014
                               LEFT JOIN D006 ON D006_Id = T014_D006_Id
                                   WHERE T014_T013_Id = '{$T013_Id}' 
								     AND D006_Flag_Gera_Contas='N'");
		if(mysql_num_rows($cSql_T014) > 0){
			$resposta['code'] = false;
			$resposta['data'] = "A CFOP dos produtos abaixo está configurada para não gerar financeiro, por favor verifique <br><br>";
			while($row = mysql_fetch_assoc($cSql_T014)){
				$resposta['data'] .= $row['T014_Codigo_Produto'] . ' - ' . $row['T014_Descricao_Produto'] . ' - CFOP: '. $row['D006_Codigo_CFOP'] . '<br>';
			}
		} else{
			$EST004 = new EST004();		
			$retorno = $EST004->finalizarNotaFinanceiro($T013_Id);
			
			if ($retorno === true) {
				$resposta['code'] = true;
				$resposta['data'] = "Financeiro: Nota finalizada com sucesso!";
			} else {
				// excluir
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}			

	break;

	case 'est004DesvincularProduto':
		$T014_Id = isset($r_T014_Id) ? $r_T014_Id : false;

		mysql_query("UPDATE T014 SET T014_D009_Id = 0, T014_T008_Id = 0 WHERE T014_Id = '{$T014_Id}'");
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro: " . $erro;
		}
		$resposta['code'] = true;
	break;

	case 'verificaFinalizarNotaEntradaEstoque':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;

		$cSql_T014 = mysql_query("SELECT T014_Id, T014_Codigo_Produto, T014_Descricao_Produto FROM T014
                                 LEFT JOIN D009 ON D009_Id = T014_D009_Id
                                 LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                 LEFT JOIN D006 ON D006_Id = T014_D006_Id
                                 WHERE T014_T013_Id = '{$T013_Id}' AND D006_Flag_Estoque='N'");
		$erro = mysql_error();
		if(!empty($erro)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro: " . $erro;
			break;
		}

		if(mysql_num_rows($cSql_T014) > 0){
			$resposta['code'] = false;
			$resposta['data'] = "A CFOP dos produtos abaixo está configurada para não movimentar estoque: <br>";
			while($row = mysql_fetch_assoc($cSql_T014)){
				$resposta['data'] .= $row['T014_Codigo_Produto'] . ' - ' . $row['T014_Descricao_Produto'] . '<br>';
			}
			$resposta['data'] .= "<br>Deseja finalizar estoque?";
		} else {
			$resposta['code'] = true;
		}

	break;

	case 'verificaFinalizarNotaValidar':
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;

		$EST004 = new EST004();
		
		$retorno = $EST004->finalizarNotaEstoqueValidar($T013_Id);
		
		if ($retorno === true) {
			$resposta['code'] = true;
			//$resposta['data'] = "Estoque: Nota finalizada com sucesso!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}

	break;

	case 'preencherLocacao':
		require_once("bibliotecas/classes/EST004.php");
		$T013_Id = isset($_REQUEST['T013_Id']) ? $_REQUEST['T013_Id'] : false;
		
		$EST004 = new EST004();
		
		$retorno = $EST004->PreencherLocacao($T013_Id);
			
		if ($retorno === true) {
			$resposta['code'] = true;
			$resposta['data'] = "Foram preenchidas as locações de todos os produtos!";
		} else {
			// excluir
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'enviarParaWMS':
		require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");

		$T013_Id = isset($_REQUEST['T013_Id']) ? $_REQUEST['T013_Id'] : false;

		if(empty($T013_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = "Nota não encontrada!";
		}

		$WMS = new WMS();
		$WMS->integrarRecebimento('INSERT', $T013_Id);

		if(!empty($WMS->getErrorMessage())) {
			$resposta['code'] = false;
			$resposta['data'] = $WMS->getErrorMessage();
		} else {
			$resposta['code'] = true;
			$resposta['data'] = "Nota enviada para WMS com sucesso!";
		}
	break;

	case 'cancelarWMS':
		require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");

		$T013_Id = isset($_REQUEST['T013_Id']) ? $_REQUEST['T013_Id'] : false;

		$WMS = new WMS();
		$WMS->cancelarRecebimento($T013_Id);
		
		if(!empty($WMS->getErrorMessage())) {
			$resposta['code'] = false;
			$resposta['data'] = $WMS->getErrorMessage();
		} else {
			$resposta['code'] = true;
			$resposta['data'] = "Nota cancelada no WMS com sucesso!";
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
