<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/EST019.php');
$EST019 = new EST019();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {

	case 'est019atualizarCFOP':
		require_once("bibliotecas/classes/EST019.php");
		$grupo  = isset($r_grupo) ? $r_grupo : false;
		$EST019 = new EST019(); 

		$resposta['code']  = true;
		$resposta['grupo'] = $grupo;

		if ($r_inicio==1){
			if (empty($grupo)) {
				$resposta['code'] = false;
            	$resposta['data'] = "Não foi passado o Id do Grupo, contate o suporte técnico";
        	}

        	$cmysql_grupo = mysql_query("SELECT T293_Id
                                           FROM T293
                                          WHERE T293_Grupo='{$grupo}'");
	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cmysql_grupo)){
	        	$cache[$count]['T293_Id']= $rowCache['T293_Id'];	        	
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas']         = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] 		= ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] 			= 1;
	        $resposta['ok'] 			= 0;
    	    $resposta['erro'] 			= 0;
        	$resposta['concluido'] 		= 0;

			apc_delete("cacheT293grupo-{$g['usuarioAtual']}");
			apc_delete("cacheT293grupo-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT293grupo-{$g['usuarioAtual']}", serialize($cache));

	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache    = unserialize(apc_fetch("cacheT293grupo-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT293grupo-resposta-{$g['usuarioAtual']}"));

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
		$ordemDeCompra = 0;

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) {  

	    	$retorno = $EST019-> est019atualizarCFOP($cache[$i]['T293_Id']);   

           	if($retorno !== true){
           		$resposta['code'] = false;
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
           	 	log("erros= ".$erros);
       		}else{
       			if ($resposta['T293_Id']!=$cache[$i]['T293_Id']) {
       				$resposta['T293_Id'] = $cache[$i]['T293_Id'];
       				$ordemDeCompra++;
       			}
       			$ok++;
       		}
        }
        $resposta['ordemDeCompra'] += $ordemDeCompra;       
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;


		if($resposta['etapa'] >= $resposta['etapas']){
			log("finaliza o atualizar CFOP");
			apc_delete("cacheT293grupo-{$g['usuarioAtual']}");
			apc_delete("cacheT293grupo-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		}else{
			log("entra nesse final");
			apc_delete("cacheT293grupo-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT293grupo-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}      		
	break;

	case 'atualizarAliquota':

		require_once("bibliotecas/classes/EST019.php");
		$EST019 = new EST019();
		global $g;

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$grupo   = isset($r_grupo) ? $r_grupo : false;

		$resposta['grupo'] = $grupo;
		$resposta['code']  = true;

		if ($r_inicio==1){
			
			if (empty($grupo)) {
				$resposta['code'] = false;
            	$resposta['data'] = "Não foi passado o Id do Grupo, contate o suporte técnico";
        	}
	        
	        $cClausula = "SELECT T293_Id, 
	                             T294_Id,
	                        	 D009_Id,
	                        	 D001_Codigo_Produto,
	                        	 D001_D037_Id,
	                        	 D001_Peso_Unitario_Kg
	                        FROM T293
	                   LEFT JOIN D006 ON D006_Id=T293_D006_Id
	                   LEFT JOIN T294 ON T293_Id=T294_T293_Id
	                   LEFT JOIN T296 ON T294_Id=T296_T294_Id
	                   LEFT JOIN D009 ON D009_Id=T294_D009_Id
	                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                       WHERE T293_Grupo='{$grupo}'
	                         AND T296_Id IS NULL";
	       	$erroMysql = mysql_error();
	        if (!empty($erroMysql)) 
	        {
	        	$resposta['code'] = false;
	            $resposta['data'] = "Erro consultando itens do orçamento: $erroMysql $cClausula";
	        }
	                        
	        $cmysql_T293 = mysql_query("$cClausula");

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cmysql_T293)){
				
	      		$cache[$count]['T293_Id']				   = $rowCache['T293_Id'];
		       	$cache[$count]['T294_Id']				   = $rowCache['T294_Id'];
		   		$cache[$count]['D009_Id']                  = $rowCache['D009_Id'];	
		   		$cache[$count]['D001_Codigo_Produto']      = $rowCache['D001_Codigo_Produto'];	
		   		$cache[$count]['D001_D037_Id'] 			   = $rowCache['D001_D037_Id'];	
		   		$cache[$count]['D001_Peso_Unitario_Kg']    = $rowCache['D001_Peso_Unitario_Kg'];
				$count++;

				$cmysql = mysql_query("SELECT T296_Id
                                         FROM T294
                                    LEFT JOIN T296 ON T296_T294_Id=T294_Id
                                        WHERE T294_T293_Id = '{$rowCache['T293_Id']}'
                                          AND T296_Id > 0");
		        if (mysql_num_rows($cmysql) > 0) {
		            $resposta['code'] = false;
		            $resposta['data'] = 'Esta Cotação já foi faturada, não é permitido mudar o CFOP';
		        }
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 2);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			apc_delete("cacheT293-{$g['usuarioAtual']}");
			apc_delete("cacheT293-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT293-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(apc_fetch("cacheT293-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT293-resposta-{$g['usuarioAtual']}"));
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
	    	$retorno = $EST019->atualizarAliquotas($cache[$i]); 	            
           	if($retorno !== true){
           		$resposta['code'] = false;
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
			apc_delete("cacheT293-{$g['usuarioAtual']}");
			apc_delete("cacheT293-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT293-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT293-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	       
	break;

	case 'est019CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$EST019->updateCheckboxGerarCotar($D009_Id, $selected);
	break;
	
	case 'est019CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$EST019->updateCheckboxGerarComprar($D009_Id, $selected);
	break;

	case 'est019CheckboxNaoComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D049_Id = isset($r_D049_Id) ? $r_D049_Id : false;
		$EST019->updateCheckboxGerarNaoComprar($D049_Id, $selected);
	break;

	case 'est019FlagDesmarcar':
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='D009_Flag_Cotar';
		} else {
		    $campo='D009_Flag_Comprar';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update D009 set {$campo}='N' where D009_Id='{$linha['D009_Id']}'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est019FlagMarcar':
		 mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		$sql = base64_decode($r_extra);
		$sql = stripslashes($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$result = mysql_query($sql);
		if(mysql_error()){
			mysql_query("ROLLBACK");
			return mysql_error();
		}
		if($r_operacao==1) {
			$campo='D009_Flag_Cotar';
		} else {
		    $campo='D009_Flag_Comprar';
		}
		while($linha = mysql_fetch_array($result)){
			$sql = "update D009 set {$campo}='S' where D009_Id='{$linha['D009_Id']}'";
			mysql_query($sql);
			if(mysql_error()){
				mysql_query("ROLLBACK");
				return mysql_error();
			}
		}
		mysql_query("COMMIT");
	break;
	
	case 'est019flagCotar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Cotar = 'S' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est019FlagDesmarcarCotar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Cotar='N' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est019FLagMarcarComprar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Comprar='S' WHERE {$extra};";
		 mysql_query($sql);
	break;
	case 'est019FLagDesmarcarComprar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Comprar='N' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est019GerarOC':
		$sql="select count(D009_Id) as Total_Comprar from D009 where D009_Flag_Comprar='S' and D009_C004_Id={$g['empresaAtual']} ";
		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		
		if ($row['Total_Comprar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para compra.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma ordem de compra dos {$row['Total_Comprar']} itens selecionados?";
		}
	break;
	
	case 'est019GerarCotacao':
		if(!empty($r_extra)){
			$extraOriginal = base64_decode(urldecode($r_extra));
			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id={$g['empresaAtual']} and D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));

			$sql = "SELECT COUNT(distinct D009_Id) as Total_Cotar 
			         FROM D009 
			    LEFT JOIN T187 on T187_D009_Id=D009_Id 
				LEFT JOIN D049 on D049_Id=D009_D049_Id 
				LEFT JOIN D001 on D001_Id=D049_D001_Id 
				LEFT JOIN D082 on D082_Id=D049_D082_Id 
				LEFT JOIN D083 on D083_D049_Id=D049_Id 
				LEFT JOIN D037 on D037_Id=D001_D037_Id 
				LEFT JOIN C008 on C008_Id=D001_C008_Id 
				LEFT JOIN D003 on D003_Id=D001_D003_Id 
				LEFT JOIN D060 on D001_D060_Id=D060_Id 
				LEFT JOIN D002 on D001_D002_Id=D002_Id 
				LEFT JOIN D015 on D001_D015_Id=D015_Id 
				LEFT JOIN T001 on T001_Id=D009_T001_Id 
			    LEFT JOIN D024 as ultimaCompra ON ultimaCompra.D024_Id = T001_D024_Id
				LEFT JOIN C007 on C007_Id=D009_C007_Id_Comprar
				LEFT JOIN T241 ON T241_D001_Id=D001_Id
				LEFT JOIN D024 as fornecedor ON fornecedor.D024_Id=T241_D024_Id
					WHERE {$extra}";
		} else {
			$sql = "SELECT COUNT(distinct D009_Id) AS Total_Cotar 
					  FROM D009 
				     WHERE D009_Flag_Cotar='S' 
					   AND D009_C004_Id={$g['empresaAtual']}";
		}

		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);

		log($sql);
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma Quotation dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;

	case 'est019GerarCotacaoEmergencia':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		
		if($T003_Id){
			$sql = "SELECT COUNT(distinct T004_Id) AS Total_Cotar 
			          FROM T004 
				 LEFT JOIN T294 ON T294_T004_Id=T004_Id 
				     WHERE T004_T003_Id={$T003_Id} 
					   AND T004_Flag_Gerar_Cotacao='S' 
					   AND IFNULL(T294_Id,0)<=0 
					   AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'";
		}else{
			if(!empty($r_extra)){
				$extraOriginal = base64_decode($r_extra);
				$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

				$sql = "SELECT COUNT(distinct T004_Id) as Total_Cotar 
						  FROM T004 
					 LEFT JOIN D009 ON D009_Id=T004_D009_Id
					 LEFT JOIN D049 ON D049_Id=D009_D049_Id
					 LEFT JOIN D001 ON D001_Id=D049_D001_Id
					 LEFT JOIN D082 ON D082_Id=D049_D082_Id
					 LEFT JOIN D037 ON D037_Id=T004_D037_Id
					 LEFT JOIN D005 ON D005_Id=T004_D005_Id
					 LEFT JOIN D006 ON D006_Id=T004_D006_Id
					 LEFT JOIN T006 ON T006_T004_Id=T004_Id
					 LEFT JOIN T005 ON T005_Id=T006_T005_Id
					 LEFT JOIN T003 ON T003_Id=T004_T003_Id
					 LEFT JOIN D024 AS Cliente ON T003_D024_Id=Cliente.D024_Id
					 LEFT JOIN T294 ON T004_Id=T294_T004_Id
					 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
					 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
					 LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
					 LEFT JOIN T241 ON T241_D001_Id=D001_Id
					 LEFT JOIN D024 AS Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
					 LEFT JOIN T004A ON T004_Id=T004A_T004_Id
					  	 WHERE {$extra}";
			} else {
				$sql = "SELECT COUNT(distinct T004_Id) AS Total_Cotar 
				          FROM T004 
					 LEFT JOIN T294 ON T294_T004_Id=T004_Id 
					     WHERE T004_Flag_Gerar_Cotacao='S' 
						   AND IFNULL(T294_Id,0)<=0 
						   AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'";
			}
		}		

		$retorno = mysql_query($sql);
		$row     = mysql_fetch_array($retorno);
		
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma Quotation dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;

	case 'processaCalcularEstoque':
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		
        set_time_limit(0);

		$extra = gPegarParteWhere(base64_decode(urldecode($r_extra)));
		$sqlD009 = mysql_query("SELECT D009_Id FROM D009 
								LEFT JOIN T187 on D009_Id=T187_D009_Id
								LEFT JOIN D049 ON D049_Id=D009_D049_Id
								LEFT JOIN D001 ON D001_Id=D049_D001_Id
								LEFT JOIN D082 ON D082_Id=D049_D082_Id
								LEFT JOIN D083 ON D049_Id=D083_D049_Id
								LEFT JOIN D024 ON D024_Id=D083_D024_Id
								LEFT JOIN D037 ON D037_Id=D001_D037_Id
								LEFT JOIN C008 ON C008_Id=D001_C008_Id
								LEFT JOIN D003 ON D003_Id=D001_D003_Id
								LEFT JOIN D060 ON D060_Id=D001_D060_Id
								LEFT JOIN D002 ON D002_Id=D001_D002_Id
								LEFT JOIN D015 ON D015_Id=D001_D015_Id
								LEFT JOIN T241 ON T241_D001_Id=D001_Id
								LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
								WHERE {$extra} 
								GROUP BY D009_Id 
								ORDER BY D049_Flag_Curva_ABC ASC;");
		while($mD009 = mysql_fetch_array($sqlD009)){
			$CAD002->D001_reprocessa_historico(false, $mD009['D009_Id'],50);
			$CAD002->atualizarEstatisticasCompra($mD009['D009_Id']);
			$itens++;
		}

		if (mysql_num_rows($sqlD009) > 0) {
			$resposta['code'] = true;
			$resposta['data'] = "Estoque atualizado com sucesso! ($itens itens) ";
		}else{
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível atualizar o estoque, filtre os produtos para atualizar.";
			$resposta['data'] = "ERRO! Não foi encontrado produtos para processar! Informe suporte técnico" ;
		}
	break;

	case 'checkboxCotarEmergencia':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T004_Id = isset($r_T004_Id) ? $r_T004_Id : false;
		$C007_Id = ($selected == 'S') ? $g['usuarioAtual'] : 0;
		mysql_query("UPDATE T004 SET T004_Flag_Gerar_Cotacao = '{$selected}', T004_C007_Id_Comprador = '{$C007_Id}' WHERE T004_Id = '{$T004_Id}'");
	break;

	case 'checkboxCotarPedidoEmergencia':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$T006_Id = isset($r_T006_Id) ? $r_T006_Id : false;
		$C007_Id = ($selected == 'S') ? $g['usuarioAtual'] : 0;
		mysql_query("UPDATE T006 SET T006_Flag_Gerar_Cotacao = '{$selected}', T006_C007_Id_Comprador = '{$C007_Id}' WHERE T006_Id = '{$T006_Id}'");
	break;

	case 'adicionarProdutosCotacao':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;

		$sqlT218 = mysql_query("SELECT T293_Id
								  FROM T293 
								 WHERE T293_Id = '{$T293_Id}'");

		if(mysql_num_rows($sqlT218) == 1){
			if($r_tipo == 'cotacaoEmergencia'){
				$retorno = $EST019->insereItemCotacaoEmergencia($T293_Id, $r_extra, $T003_Id);
				if($retorno === true){
					$resposta['code'] = true;
				}else{
					$resposta['code'] = false;
					$resposta['data'] = $retorno; 
				}
			} else if($r_tipo == 'comprasEstoque') {
				$retorno = $EST019->insereItemEstoque($T293_Id, urldecode($r_extra));
				if($retorno == 1){
					$resposta['code'] = true;	
				}else{
					$resposta['code'] = false;
					$resposta['data'] = $retorno; 
				}
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível localizar a Quotation informada, verifique o número da Quotation!";
		}
	break;

	case 'adicionarProdutosPedidoCotacao':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
		$T293 = mysql_query("SELECT * FROM T293 WHERE T293_Id = '{$T293_Id}'");
		
		if(mysql_num_rows($T293) == 1){
			$retorno = $EST019->insereItemCotacaoPedidoEmergencia($T293_Id, $r_extra);

			if($retorno === true){
				$resposta['code'] = true;
			} else {
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			}
		}else{

			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível localizar a cotação informada, verifique o número da cotação!";
		}

	break;

	case 'est019GerarCotacaoPedidoEmergencia':
		$extraOriginal = base64_decode($r_extra);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$sql = "SELECT COUNT(distinct T006_Id) as Total_Cotar, 
					   SUM(IF(ifnull(D049_Flag_Tipo,'CP') = 'PD' AND D074_Id IS NULL, 1, 0)) as Itens_Sem_Engenharia 
				  FROM T006 
			 LEFT JOIN T261 on T006_Id=T261_T006_Id
			 LEFT JOIN D009 on D009_Id=T006_D009_Id
			 LEFT JOIN D049 on D049_Id=D009_D049_Id
			 LEFT JOIN D001 on D001_Id=D049_D001_Id
			 LEFT JOIN D074 on D001_Id=D074_D001_Id
			 LEFT JOIN D082 on D082_Id=D049_D082_Id
			 LEFT JOIN D037 on D037_Id=T006_D037_Id
			 LEFT JOIN D005 on D005_Id=T006_D005_Id
			 LEFT JOIN D006 on D006_Id=T006_D006_Id
			 LEFT JOIN T005 ON T005_Id=T006_T005_Id
			 LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
			 LEFT JOIN T003 ON T003_Id=T005_T003_Id
			 LEFT JOIN T004 ON T004_Id=T006_T004_Id
			 LEFT JOIN T294 ON T006_Id=T294_T006_Id
			 LEFT JOIN T293 ON T293_Id=T294_T293_Id
			 LEFT JOIN T296 ON T294_Id=T296_T294_Id
			 LEFT JOIN T295 ON T295_Id=T296_T295_Id
			 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			 LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			 LEFT JOIN T241 ON T241_D001_Id=D001_Id
			 LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			  	 WHERE {$extra}";

		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);
		

		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			if($row['Itens_Sem_Engenharia'] > 0){
				$resposta['code'] = false;
				$resposta['data'] = ($row['Itens_Sem_Engenharia'] == 1) ? "Há um item produzido sem engenharia" : "Existem produtos produzidos sem engenharia!";
			} else {
				$resposta['code'] = true;
				$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
			}
		}
	break;

	case 'est019GerarOT':
		$count = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', $r_sql);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if (empty($row['T219_T218_Id']) && $row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['T006_C007_Id_Comprador'] == $g['usuarioAtual']) {
				$count++;
			}
		}
		if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = $count;
		}
	break;

	case 'est019GerarOTEstoque':
		$count = 0;
		$semQtdeComprar = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', $r_sql);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C004_Id'] == $g['empresaAtual'] && $row['D009_C007_Id_Comprar'] == $g['usuarioAtual']) {
				if ($row['D009_Quantidade_Comprar'] <= 0) {
					$semQtdeComprar++;
				} else {
					$count++;
				}
			}
		}
		if ($semQtdeComprar > 0) {
			$resposta['code'] = false;
			$resposta['data'] = $semQtdeComprar > 1 ? "Existem {$semQtdeComprar} itens que não foram preenchidos a 'Quantidade Comprar'." : "Existe {$semQtdeComprar} item que não foi preenchido a 'Quantidade Comprar'.";
		} else if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $count;
		}
	break;

	case 'processaCalcularEstoqueEmergencia':
		require_once('bibliotecas/classes/CAD002.php');
		$CAD002 = new CAD002();
		
		$sql = str_replace('LIMIT 0,100', '', $r_sql);
		$resD009 = mysql_query($sql);
		while($mD009 = mysql_fetch_array($resD009)){
			$CAD002->D001_reprocessa_historico(false, $mD009['D009_Id']);
			$itens++;
		}

		if (mysql_num_rows($resD009) > 0) {
			$resposta['code'] = true;
			$resposta['data'] = "Estoque atualizado com sucesso! ($itens itens)";
		}else{
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível atualizar o estoque, filtre os produtos para atualizar.";
		}
	break;

	case 'est019VerificarCotacaoAutomatica':
		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND ifnull(T294_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

		$sql = "SELECT Fornecedor.D024_Id, 
		               T004_Id, 
					   Fornecedor.D024_Nome_Empresa, COUNT(D001_Id) as count 
				  FROM T004 
			  LEFT JOIN D009 on D009_Id=T004_D009_Id
			  LEFT JOIN D049 on D049_Id=D009_D049_Id
			  LEFT JOIN D001 on D001_Id=D049_D001_Id
			  LEFT JOIN D082 on D082_Id=D049_D082_Id
			  LEFT JOIN D037 on D037_Id=T004_D037_Id
			  LEFT JOIN D005 on D005_Id=T004_D005_Id
			  LEFT JOIN D006 on D006_Id=T004_D006_Id
			  LEFT JOIN T006 on T006_T004_Id=T004_Id
			  LEFT JOIN T005 ON T005_Id=T006_T005_Id
			  LEFT JOIN T003 ON T003_Id=T004_T003_Id
			  LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
			  LEFT JOIN T294 ON T004_Id=T294_T004_Id
			  LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			  LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			  LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
			  LEFT JOIN T241 ON T241_D001_Id=D001_Id
			  LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			  LEFT JOIN T004A ON T004_Id=T004A_T004_Id
			 	  WHERE {$extra}";
		
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY Fornecedor.D024_Id";

		$retorno = mysql_query($sql);
		$qtde    = mysql_num_rows($retorno);

		if($qtde <= 0){

			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";

		} else {

			if($qtde > 1){
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} cotações para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma cotação para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
					$semFornecedor = $row['count'];
				} else {
					$msg .= "{$row['D024_Nome_Empresa']} - {$row['count']} produtos<br><br>";
				}
			}

			if($semFornecedor !== false){
				$resposta['code'] = false;
				if($semFornecedor > 1){
					$resposta['data'] = "Existem {$semFornecedor} produtos sem fornecedor.";
				} else {
					$resposta['data'] = "Existe um produto sem fornecedor.";
				}
			} else {
				$resposta['code'] = true;
				$resposta['data'] = $msg;
			}
		}

	break;

	case 'est019GerarCotacaoEmergenciaAutomatica':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();
		log("entrou aqui para est019GerarCotacaoEmergenciaAutomatica");
		$extraOriginal = base64_decode($r_extra);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND ifnull(T294_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

		$retorno = $EST019->gerarCotacaoAutomatica($extra);
		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est019VerificarCotacaoPedidoAutomatica':
		$semFornecedor = false;
		$semEngenharia = 0;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND ifnull(T294_Id,0)<=0 AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$sql = "SELECT Fornecedor.D024_Id, 
					   T004_Id, Fornecedor.D024_Nome_Empresa, COUNT(D001_Id) as count,
					   ifnull(D049_Flag_Tipo,'CP') as D049_Flag_Tipo, 
					   IFNULL(D074_Id,0) as D074_Id 
				  FROM T006
			  LEFT JOIN T261 on T006_Id=T261_T006_Id
			  LEFT JOIN D009 on D009_Id=T006_D009_Id
			  LEFT JOIN D049 on D049_Id=D009_D049_Id
			  LEFT JOIN D001 on D001_Id=D049_D001_Id
			  LEFT JOIN D074 on D001_Id=D074_D001_Id
			  LEFT JOIN D082 on D082_Id=D049_D082_Id
			  LEFT JOIN D037 on D037_Id=T006_D037_Id
			  LEFT JOIN D005 on D005_Id=T006_D005_Id
			  LEFT JOIN D006 on D006_Id=T006_D006_Id
			  LEFT JOIN T005 ON T005_Id=T006_T005_Id
			  LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
			  LEFT JOIN T003 ON T003_Id=T005_T003_Id
			  LEFT JOIN T004 ON T004_Id=T006_T004_Id
			  LEFT JOIN T294 ON T006_Id=T294_T006_Id
			  LEFT JOIN T293 ON T293_Id=T294_T293_Id
			  LEFT JOIN T296 ON T294_Id=T296_T294_Id
			  LEFT JOIN T295 ON T295_Id=T296_T295_Id
			  LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			  LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			  LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			  LEFT JOIN T241 ON T241_D001_Id=D001_Id
			  LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			  	  WHERE {$extra}";
					
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY Fornecedor.D024_Id";
		$retorno = mysql_query($sql);

		$qtde = mysql_num_rows($retorno);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";
		} else {
			if($qtde > 1){
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} cotações para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma cotação para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				if($row['D049_Flag_Tipo'] == 'PD' && empty($row['D074_Id'])){
					$semEngenharia++;
				} else if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
					$semFornecedor = $row['count'];
				} else {
					$msg .= "{$row['D024_Nome_Empresa']} {$row['count']} produtos<br><br>";
				}
			}

			if($semEngenharia > 0){
				$resposta['code'] = false;
				$resposta['data'] = ($semEngenharia == 1) ? "Há um item produzido sem engenharia" : "Existem produtos produzidos sem engenharia.";
			} else if($semFornecedor !== false){
				$resposta['code'] = false;
				if($semFornecedor > 1){
					$resposta['data'] = "Existem {$semFornecedor} produtos sem fornecedor.";
				} else {
					$resposta['data'] = "Existe um produto sem fornecedor.";
				}
			} else {
				$resposta['code'] = true;
				$resposta['data'] = $msg;
			}
		}
	break;

	case 'est019RecuperarEstoqueLoja':
		
		require_once('bibliotecas/classes/EST019.php');
		$EST015 = new EST019();

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S'"), true);

		$retorno = $EST019->est019RecuperarEstoqueLoja($extra);	 	
		
		$resposta['code'] = $retorno[0];
		$resposta['data'] = $retorno[1];

	break;

	case 'est019GerarCotacaoPedidoEmergenciaAutomatica':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$extraOriginal = base64_decode($r_extra);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND ifnull(T294_Id,0)<=0 AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$retorno = $EST019->gerarCotacaoPedidoAutomatica($extra);
		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est019VerificarCotacaoEstoqueAutomatica':
		$extraOriginal = base64_decode(urldecode($r_extra));
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id={$g['empresaAtual']} and D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));

		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$sql = "SELECT fornecedor.D024_Id, 
					   fornecedor.D024_Nome_Empresa, 
					   GROUP_CONCAT(DISTINCT D009_Id SEPARATOR ', ') AS D009_Ids, 
					   COUNT(DISTINCT D009_Id) AS count 
				  FROM D009 
             LEFT JOIN T187 ON T187_D009_Id=D009_Id 
             LEFT JOIN D049 ON D049_Id=D009_D049_Id 
             LEFT JOIN D001 ON D001_Id=D049_D001_Id 
             LEFT JOIN D082 ON D082_Id=D049_D082_Id 
             LEFT JOIN D083 ON D083_D049_Id=D049_Id 
             LEFT JOIN D037 ON D037_Id=D001_D037_Id 
             LEFT JOIN C008 ON C008_Id=D001_C008_Id 
             LEFT JOIN D003 ON D003_Id=D001_D003_Id 
             LEFT JOIN D060 ON D001_D060_Id=D060_Id 
             LEFT JOIN D002 ON D001_D002_Id=D002_Id 
             LEFT JOIN D015 ON D001_D015_Id=D015_Id 
             LEFT JOIN T001 ON T001_Id=D009_T001_Id 
             LEFT JOIN D024 AS ultimaCompra ON ultimaCompra.D024_Id = T001_D024_Id
             LEFT JOIN D041 ON D041_D049_Id=D049_Id
             LEFT JOIN D005 ON D005_Id=D001_D005_Id
             LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
             LEFT JOIN T241 ON T241_D001_Id=D001_Id
             LEFT JOIN D024 AS fornecedor ON fornecedor.D024_Id=T241_D024_Id
                 WHERE {$extra}";

	    $sql = explode("LIMIT", $sql);
	    $sql = $sql[0];
	    $sql .= " GROUP BY fornecedor.D024_Id";
		$retorno = mysql_query($sql);
		
		$qtde = mysql_num_rows($retorno);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";
		} else {
			if($qtde > 1){
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} Quotations para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma contation para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
					$semFornecedor = $row['count'];
				} else {
					$msg .= "{$row['D024_Nome_Empresa']} - {$row['count']} produtos<br><br>";
				}
			}

			if($semFornecedor !== false){
				$resposta['code'] = false;
				if($semFornecedor > 1){
					$resposta['data'] = "Existem {$semFornecedor} produtos sem fornecedor.";
				} else {
					$resposta['data'] = "Existe um produto sem fornecedor.";
				}
			} else {
				$resposta['code'] = true;
				$resposta['data'] = $msg;
			}

		}
	break;

	case 'est019GerarCotacaoAutomatica':
		
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$r_extra = urldecode($r_extra);

		$retorno = $EST019->gerarCotacaoEstoqueAutomatica($r_extra);

		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est019VerificarGerarMontagem':
		$count = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', base64_decode($r_extra));
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['T006_C007_Id_Comprador'] == $g['usuarioAtual']) {
				$count++;
			}
		}
		if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		}else{
			$resposta['code'] = true;
			if($count == 1){
				$resposta['data'] = "Será gerada uma montagem para {$count} produto selecionado.";
			} else {
				$resposta['data'] = "Será gerada uma montagem para {$count} produtos selecionados.";
			}
		}
	break;

	case 'est019GerarMontagem':
		require_once('bibliotecas/classes/EST007.php');
		$EST007 = new EST007();

		$count = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', base64_decode($r_extra));
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['T006_C007_Id_Comprador'] == $g['usuarioAtual']) {
				$count++;
			}
		}
		if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = $EST007->est007GerarMontagemEmergencia($sql);
		}
	break;

	case 'est019VerificarGerarMontagemEstoque':
		$count = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', base64_decode(urldecode($r_extra)));
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C007_Id_Comprar'] == $g['usuarioAtual']) {
				$count++;
			}
		}
		if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		}else{
			$resposta['code'] = true;
			if($count == 1){
				$resposta['data'] = "Será gerada uma montagem para {$count} produto selecionado.";
			} else {
				$resposta['data'] = "Será gerada uma montagem para {$count} produtos selecionados.";
			}
		}
	break;

	case 'est019GerarMontagemEstoque':
		require_once('bibliotecas/classes/EST007.php');
		$EST007 = new EST007();

		$count = 0;
		$sql = preg_replace('#LIMIT 0,100$#', '', base64_decode(urldecode($r_extra)));
		$res = mysql_query($sql);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C007_Id_Comprar'] == $g['usuarioAtual']) {
				$count++;
			}
		}
		if ($count <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = $EST007->est007GerarMontagemEstoque($sql);
		}
	break;

	case 'est019VerificarGerarOrdemCompra':
		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		//$extraOriginal = str_replace("Fornecedor.D024_Nome_Empresa","D024_Nome_Empresa",$extraOriginal); // retira o alias que está vindo do GRID
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "(T294_T006_Id > 0 OR T294_T004_Id > 0) AND T294_Quantidade_PO > 0"));

		$sql = "SELECT D024_Id, 
					   D024_Nome_Empresa, 
					   COUNT(T006_Id) as count, 
					   T293_Id, 
					   GROUP_CONCAT(DISTINCT T293_Id SEPARATOR ',') 
				  FROM T006 
			 LEFT JOIN T005 ON T005_Id=T006_T005_Id
			 LEFT JOIN T004 ON T004_Id=T006_T004_Id
			 LEFT JOIN D009 on D009_Id=T006_D009_Id
			 LEFT JOIN D049 on D049_Id=D009_D049_Id
			 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			 LEFT JOIN D001 on D001_Id=D049_D001_Id
			 LEFT JOIN D082 on D082_Id=D049_D082_Id
			 LEFT JOIN D037 on D037_Id=T006_D037_Id
			 LEFT JOIN D005 on D005_Id=T006_D005_Id
			 LEFT JOIN D006 on D006_Id=T006_D006_Id
			 LEFT JOIN T294 ON T006_Id=T294_T006_Id OR T004_Id=T294_T004_Id
			 LEFT JOIN T293 ON T293_Id=T294_T293_Id
			 LEFT JOIN T296 ON T294_Id=T296_T294_Id
			 LEFT JOIN T295 ON T295_Id=T296_T295_Id
			 LEFT JOIN D024 AS Fornecedor ON D024_Id=T293_D024_Id
			  	 WHERE {$extra}";

		log("vai fazer a query");
		log($sql);
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY D024_Id";
		$retorno = mysql_query($sql);
		
		$qtde = mysql_num_rows($retorno);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto com a <b>Qtd nova PO</b> preenhida";
		} else {
			if($qtde > 1){
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} ordens de compra para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma ordem de compra para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				$msg .= "{$row['D024_Nome_Empresa']} - {$row['count']} produto(s)<br><br>";
			}

			$resposta['code'] = true;
			$resposta['data'] = $msg;
		}
	break;

	case 'adicionarProdutosOrdemCompra':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$T295_Id = $r_T295_Id;
		if(empty($T295_Id)){
			$resposta['code'] = false;
			$resposta['data'] = "O número da ordem de compra deve estar preenchido";
			break;
		}

		$extraOriginal = base64_decode($r_extra);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "(T294_T006_Id > 0 OR T294_T004_Id > 0)AND T294_Quantidade_PO > 0 AND T296_Id IS NULL"));

		$sql = "SELECT D024_Id,
					   D024_Nome_Empresa, 
					   COUNT(T006_Id) as count, 
					   GROUP_CONCAT(DISTINCT T294_Id SEPARATOR ',') as listaT294 
				  FROM T006 
			 LEFT JOIN T005 ON T005_Id=T006_T005_Id
			 LEFT JOIN T004 ON T004_Id=T006_T004_Id
			 LEFT JOIN D009 on D009_Id=T006_D009_Id
			 LEFT JOIN D049 on D049_Id=D009_D049_Id
			 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			 LEFT JOIN D001 on D001_Id=D049_D001_Id
			 LEFT JOIN D082 on D082_Id=D049_D082_Id
			 LEFT JOIN D037 on D037_Id=T006_D037_Id
			 LEFT JOIN D005 on D005_Id=T006_D005_Id
			 LEFT JOIN D006 on D006_Id=T006_D006_Id
			 LEFT JOIN T294 ON T006_Id=T294_T006_Id OR T004_Id=T294_T004_Id
			 LEFT JOIN T293 ON T293_Id=T294_T293_Id
			 LEFT JOIN T296 ON T294_Id=T296_T294_Id
			 LEFT JOIN T295 ON T295_Id=T296_T295_Id
			 LEFT JOIN D024 AS Fornecedor ON D024_Id=T293_D024_Id
			  	 WHERE {$extra}";

		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY D024_Id";
		$res = mysql_query($sql);
		$qtde = mysql_num_rows($res);
		log("quanto deu a quantidade agora ".$qtde);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto com a <b>Qtd nova PO</b> preenhida";
			break;
		} if($qtde > 1){
			$resposta['code'] = false;
			$resposta['data'] = "Foram selecionados produtos com Quotation de fornecedores diferentes";
		} else {
			$row = mysql_fetch_array($res);		
			$retorno = $EST019->adicionarProdutosOrdemCompra($T295_Id, $row['listaT294']);
			if($retorno !== true){
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				$resposta['code'] = true;
			}
		}
	break;

	case 'est019GerarOrdemCompra':
		require_once('bibliotecas/classes/EST019.php');
		$EST019 = new EST019();

		$extraOriginal = base64_decode($r_extra);
		$extraOriginal = str_replace("Fornecedor.","",$extraOriginal);
		$extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "(T294_T006_Id > 0 OR T294_T004_Id > 0) AND T294_Quantidade_PO > 0"));

		$sql = "SELECT D024_Id,
				       D024_Nome_Empresa, 
					   COUNT(T006_Id) as count, 
					   T293_Id, 
					   GROUP_CONCAT(DISTINCT T293_Id SEPARATOR ',') as listaT293 
				  FROM T006 
			 LEFT JOIN T005 ON T005_Id=T006_T005_Id
			 LEFT JOIN T004 ON T004_Id=T006_T004_Id
			 LEFT JOIN D009 on D009_Id=T006_D009_Id
			 LEFT JOIN D049 on D049_Id=D009_D049_Id
			 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			 LEFT JOIN D001 on D001_Id=D049_D001_Id
			 LEFT JOIN D082 on D082_Id=D049_D082_Id
			 LEFT JOIN D037 on D037_Id=T006_D037_Id
			 LEFT JOIN D005 on D005_Id=T006_D005_Id
			 LEFT JOIN D006 on D006_Id=T006_D006_Id
			 LEFT JOIN T294 ON T006_Id=T294_T006_Id OR T004_Id=T294_T004_Id
			 LEFT JOIN T293 ON T293_Id=T294_T293_Id
			 LEFT JOIN T296 ON T294_Id=T296_T294_Id
			 LEFT JOIN T295 ON T295_Id=T296_T295_Id
			 LEFT JOIN D024 ON D024_Id=T293_D024_Id
				 WHERE {$extra}";

		$sql  = explode("LIMIT", $sql);
		$sql  = $sql[0];
		$sql .= " GROUP BY D024_Id";
		log("qual é a query agora");
		
		$res  = mysql_query($sql);
		$error = mysql_error();
		if(!empty($error)){
			$resposta['code'] = false;
			$resposta['data'] = $error;
			break;
		}
		$qtde = mysql_num_rows($res);

		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto com a <b>Qte nova PO<\b> preenhida";
		
		} else {

			$T295 = mysql_query("SELECT T295_Grupo 
								   FROM T295 
							   ORDER BY T295_Grupo DESC LIMIT 1");

            $mT295 = mysql_fetch_array($T295);
            $grupo = $mT295['T295_Grupo'] + 1;

			while($row = mysql_fetch_array($res)){
				log("qual id está mandando ".$row['T293_Id']);
				$retorno = $EST019->gerarPedido($row['T293_Id'], $row['listaT293'], $grupo);
			}
			
			if($retorno !== true){
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				$resposta['code'] = true;
				$resposta['data'] = $grupo;
			}
		}
	break;

	case 'est019DefinirQuantidadePO';
		global $g;
		$erro = 0;
		mysql_query("START_TRANSACTION");
		$sql  = base64_decode($r_sql);
		$sql  = str_replace('LIMIT 0,100', '', $sql);
		$T294 = mysql_query($sql);
		
		while($mT294 = mysql_fetch_array($T294)){
			$upT294 = mysqli_query("UPDATE T294 SET T294_Quantidade_PO=T294_Quantidade_Saldo WHERE T294_Id='{$mT294['T294_Id']}'");
			$error   = mysql_error();
			if(!empty($erro)){
				mysql_query("ROLLBACK");
				$erro = 1;
			}
		}
		mysql_query("COMMIT");

		if($erro == 0){
			$resposta['code'] = true;
		}else{
			$resposta['code'] = false;
			$resposta['data'] = $error;
		}		
	break;

	case 'est019LimparQuantidadePO';
	global $g;
	$erro = 0;
	mysql_query("START_TRANSACTION");
	$sql  = base64_decode($r_sql);
	$sql  = str_replace('LIMIT 0,100', '', $sql);
	$T294 = mysql_query($sql);
	
	while($mT294 = mysql_fetch_array($T294)){
		$upT294 = mysqli_query("UPDATE T294 SET T294_Quantidade_PO = 0 WHERE T294_Id='{$mT294['T294_Id']}'");
		$error   = mysql_error();
		if(!empty($erro)){
			mysql_query("ROLLBACK");
			$erro = 1;
		}
	}
	mysql_query("COMMIT");
	
	if($erro == 0){
		$resposta['code'] = true;
	}else{
		$resposta['code'] = false;
		$resposta['data'] = $error;
	}		
	break;

	case 'est019validaQuantidadeComprar';
		global $g;
		
    	$D009_Id 	   = isset($r_D009_Id) ? $r_D009_Id : false;
    	$r_novoValor   = gCorrigeNumeroInverte($r_novoValor);
    	$r_valorAntigo = gCorrigeNumeroInverte($r_valorAntigo);
    	$novoValor     = isset($r_novoValor) ? $r_novoValor : false;
    	$valorAntigo   = isset($r_valorAntigo) ? $r_valorAntigo : false;

    	$D049 = mysql_query("SELECT D049_Quantidade_Embalagem_Compra,
								    D049_Quantidade_Minima_Compra 
							   FROM D049 
						  LEFT JOIN D009 ON D049_Id = D009_D049_Id
						      WHERE D009_Id = '{$D009_Id}'");
    	$mD049 = mysql_fetch_array($D049);
		
		// verifica o resto da divisão para ver se a quantidade digitada é multiplo da quantidade da embalagem
		$multiploQtdEmbalagem = 0;
		if($mD049['D049_Quantidade_Embalagem_Compra'] > 0){
			$multiploQtdEmbalagem = ($novoValor % $mD049['D049_Quantidade_Embalagem_Compra']);
		}
		
		if($mD049['D049_Quantidade_Minima_Compra'] > 0 && ($novoValor > 0 && $novoValor < $mD049['D049_Quantidade_Minima_Compra'])){
			// se for menor que a quantidade minina retorna erro
			mysql_query("UPDATE D009 SET D009_Quantidade_Comprar = '{$valorAntigo}' WHERE D009_Id = '{$D009_Id}'");
			$resposta['data'] = "O valor informado é menor que a quantidade <b>mínima de compra</b>";
			$resposta['code'] = false;
		} else {			
			if($multiploQtdEmbalagem != 0){
				// se não for multiplo da embalagem retorna erro
				mysql_query("UPDATE D009 SET D009_Quantidade_Comprar = '{$valorAntigo}' WHERE D009_Id = '{$D009_Id}'");
				$resposta['data'] = "O valor informado não é múltiplo da <b>quantidade embalagem</b>";
				$resposta['code'] = false;
			} else {
				$resposta['code'] = true;
			}
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
