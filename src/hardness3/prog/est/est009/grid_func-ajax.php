<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/

// Classes
require_once('bibliotecas/classes/EST009.php');
$EST009 = new EST009();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {

	case 'est009atualizarCFOP':
		require_once("bibliotecas/classes/EST015.php");
		$grupo = isset($r_grupo) ? $r_grupo : false;
		$EST015 = new EST015(); 
		$resposta['code'] = true;
		$resposta['grupo'] = $grupo;

		if ($r_inicio==1){
			if (empty($grupo)) {
				$resposta['code'] = false;
            	$resposta['data'] = "Não foi passado o Id do Grupo, contate o suporte técnico";
        	}

        	$cmysql_grupo = mysql_query("SELECT T218_Id
                                           FROM T218
                                          WHERE T218_Grupo='{$grupo}'");
	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cmysql_grupo)){
	      		log("count= ".$count);
	        	$cache[$count]['T218_Id']= $rowCache['T218_Id'];
	        	log("T218_Id no atualizar CFOP= ".$rowCache['T218_Id']);
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
			apc_delete("cacheT218grupo-{$g['usuarioAtual']}");
			apc_delete("cacheT218grupo-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT218grupo-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(apc_fetch("cacheT218grupo-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT218grupo-resposta-{$g['usuarioAtual']}"));
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
	    	$retorno = $EST015-> est015atualizarCFOP($cache[$i]['T218_Id']);      
           	if($retorno !== true){
           		$resposta['code'] = false;
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
           	 	log("erros= ".$erros);
       		}else{
       			if ($resposta['T218_Id']!=$cache[$i]['T218_Id']) {
       				$resposta['T218_Id'] = $cache[$i]['T218_Id'];
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
			apc_delete("cacheT218grupo-{$g['usuarioAtual']}");
			apc_delete("cacheT218grupo-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		}else{
			log("entra nesse final");
			apc_delete("cacheT218grupo-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT218grupo-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}      		
	break;

	case 'atualizarAliquota':
		require_once("bibliotecas/classes/EST015.php");
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$grupo = isset($r_grupo) ? $r_grupo : false;
		$C004_Id = $g['empresaAtual'];
		$resposta['grupo'] = $grupo;

		$EST015 = new EST015();
		global $g;
		$resposta['code'] = true;

		if ($r_inicio==1){
			if (empty($grupo)) {
				$resposta['code'] = false;
            	$resposta['data'] = "Não foi passado o Id do Grupo, contate o suporte técnico";
        	}
	        
	       	//========================================================================================
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        //-- VERIFICA SE OS IMPOSTOS ESTAO CORRETOS
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        $cClausula = "SELECT
	        				T218_Id, 
	                        T218_Flag_Calcular_IPI,
	                        T218_Flag_Calcular_ICMS,
	                        T218_Flag_Calcular_ST,
	                        T219_Id,
	                        T219_Item,
	                        T219_Flag_Origem_Produto,
	                        D009_Id,
	                        D001_Codigo_Produto,
	                        D001_D037_Id,
	                        D001_Peso_Unitario_Kg
	                        FROM T218
	                   LEFT JOIN D006 ON D006_Id=T218_D006_Id
	                   LEFT JOIN T219 ON T218_Id=T219_T218_Id
	                   LEFT JOIN T225 ON T219_Id=T225_T219_Id
	                   LEFT JOIN D009 ON D009_Id=T219_D009_Id
	                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                       WHERE T218_Grupo='{$grupo}'
	                        AND T225_Id is NULL";
	       	$erroMysql = mysql_error();
	        if (!empty($erroMysql)) 
	        {
	        	$resposta['code'] = false;
	            $resposta['data'] = "Erro consultando itens do orçamento: $erroMysql $cClausula";
	        }
	                        
	        $cmysql_T218 = mysql_query("$cClausula");

	        $count = 0;
	      	while($rowCache = mysql_fetch_array($cmysql_T218)){
	      		$cache[$count]['T218_Id']= $rowCache['T218_Id'];
	        	$cache[$count]['T218_Flag_Calcular_IPI']= $rowCache['T218_Flag_Calcular_IPI'];
	        	$cache[$count]['T218_Flag_Calcular_ICMS']= $rowCache['T218_Flag_Calcular_ICMS'];
		       	$cache[$count]['T218_Flag_Calcular_ST']= $rowCache['T218_Flag_Calcular_ST'];
		       	$cache[$count]['T219_Id']= $rowCache['T219_Id'];
		   		$cache[$count]['T219_Item'] = $rowCache['T219_Item'];	
		   		$cache[$count]['T219_Flag_Origem_Produto'] = $rowCache['T219_Flag_Origem_Produto'];	
		   		$cache[$count]['D009_Id'] = $rowCache['D009_Id'];	
		   		$cache[$count]['D001_Codigo_Produto'] = $rowCache['D001_Codigo_Produto'];	
		   		$cache[$count]['D001_D037_Id'] = $rowCache['D001_D037_Id'];	
		   		$cache[$count]['D001_Peso_Unitario_Kg'] = $rowCache['D001_Peso_Unitario_Kg'];
				$count++;

				$cmysql = mysql_query("SELECT T225_Id
                                         FROM T219
                                    LEFT JOIN T225 ON T225_T219_Id=T219_Id
                                        WHERE T219_T218_Id='{$rowCache['T218_Id']}'
                                          AND T225_Id>0");
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
			apc_delete("cacheT218-{$g['usuarioAtual']}");
			apc_delete("cacheT218-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheT218-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(apc_fetch("cacheT218-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheT218-resposta-{$g['usuarioAtual']}"));
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
	    	$retorno = $EST015->atualizarAliquotas($cache[$i]['T218_Id'], $C004_Id, $r_usarTabelaFornecedor, $cache[$i]); 	            
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
			apc_delete("cacheT218-{$g['usuarioAtual']}");
			apc_delete("cacheT218-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheT218-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheT218-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	       
	break;

	case 'est009CheckboxCotar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$EST009->updateCheckboxGerarCotar($D009_Id, $selected);
	break;
	
	case 'est009CheckboxComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		$EST009->updateCheckboxGerarComprar($D009_Id, $selected);
	break;

	case 'est009CheckboxNaoComprar':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D049_Id = isset($r_D049_Id) ? $r_D049_Id : false;
		$EST009->updateCheckboxGerarNaoComprar($D049_Id, $selected);
	break;

	case 'est009FlagDesmarcar':
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
	
	case 'est009FlagMarcar':
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
	
	case 'est009flagCotar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Cotar = 'S' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est009FlagDesmarcarCotar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Cotar='N' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est009FLagMarcarComprar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Comprar='S' WHERE {$extra};";
		 mysql_query($sql);
	break;
	case 'est009FLagDesmarcarComprar':
		 $extra = gPegarParteWhere(base64_decode($r_extra));
 		 $sql = "update D009 LEFT JOIN D001 ON D001_Id=D009_D001_Id set D009_Flag_Comprar='N' WHERE {$extra};";
		 mysql_query($sql);
	break;
	
	case 'est009GerarOC':
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
	
	case 'est009GerarCotacao':
		if(!empty($r_extra)){
			$extraOriginal = base64_decode(urldecode($r_extra));
			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id={$g['empresaAtual']}"));

			$sql = "SELECT COUNT(distinct D009_Id) as Total_Cotar FROM D009 
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
			left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
			LEFT JOIN C007 on C007_Id=D009_C007_Id_Comprar
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
			LEFT JOIN D009A ON D009A_D009_Id=D009_Id
			WHERE {$extra}";
		} else {
			$sql = "SELECT COUNT(distinct D009_Id) as Total_Cotar FROM D009 WHERE D009_Flag_Cotar='S' and D009_C004_Id={$g['empresaAtual']}";
		}

		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);

		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
		}
	break;

	case 'est009GerarCotacaoEmergencia':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;

		if($T003_Id){
			log("Veio T003_Id");
			$sql = "SELECT COUNT(distinct T004_Id) as Total_Cotar FROM T004 LEFT JOIN T219 ON T219_T004_Id=T004_Id WHERE T004_T003_Id={$T003_Id} AND T004_Flag_Gerar_Cotacao='S' AND IFNULL(T004_T219_Id,0)<=0 AND IFNULL(T219_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'";
		}else{
			if(!empty($r_extra)){
			$extraOriginal = base64_decode($r_extra);
			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

			$sql = "SELECT COUNT(distinct T004_Id) as Total_Cotar FROM T004 
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
					LEFT JOIN T219 ON T004_Id=T219_T004_Id
					LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
					LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
					LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
					LEFT JOIN T241 ON T241_D001_Id=D001_Id
					LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
					LEFT JOIN T004A ON T004_Id=T004A_T004_Id
					WHERE {$extra}";
			} else {
				$sql = "SELECT COUNT(distinct T004_Id) as Total_Cotar FROM T004 LEFT JOIN T219 ON T219_T004_Id=T004_Id WHERE T004_Flag_Gerar_Cotacao='S' AND IFNULL(T004_T219_Id,0)<=0 AND IFNULL(T219_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'";
			}
		}
		

		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);

		log($sql);
		
		if ($row['Total_Cotar'] <= 0) {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi selecionado nenhum item para cotar.";
		}else{
			$resposta['code'] = true;
			$resposta['data'] = "Deseja iniciar uma cotação dos {$row['Total_Cotar']} itens selecionados?";
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
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;

		$sqlT218 = mysql_query("SELECT * FROM T218 WHERE T218_Id = '{$T218_Id}'");
		if(mysql_num_rows($sqlT218) == 1){
			if($r_tipo == 'cotacaoEmergencia'){
				$retorno = $EST015->insereItemCotacaoEmergencia($T218_Id, $r_extra,$T003_Id);
				if(is_array($retorno)){
					$resposta['code'] = true;
					$resposta['data'] = $retorno['T005_Id']; // o retorno é o T005_Id;
				}else{
					$resposta['code'] = false;
					$resposta['data'] = $retorno; 
				}
			} else if($r_tipo == 'comprasEstoque') {
				$retorno = $EST015->insereItemEstoque($T218_Id, urldecode($r_extra));
				if($retorno == 1){
					$resposta['code'] = true;	
				}else{
					$resposta['code'] = false;
					$resposta['data'] = $retorno; 
				}
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível localizar a cotação informada, verifique o número da cotação!";
		}
	break;

	case 'adicionarProdutosPedidoCotacao':
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$T218_Id = isset($r_T218_Id) ? $r_T218_Id : false;
		$sqlT218 = mysql_query("SELECT * FROM T218 WHERE T218_Id = '{$T218_Id}'");
		if(mysql_num_rows($sqlT218) == 1){
			$T015_Id = $EST015->insereItemCotacaoPedidoEmergencia($T218_Id, $r_extra);

			$resposta['code'] = true;
			$resposta['data'] = $T015_Id;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Não foi possível localizar a cotação informada, verifique o número da cotação!";
		}
	break;

	case 'est009GerarCotacaoPedidoEmergencia':
		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$sql = "SELECT COUNT(distinct T006_Id) as Total_Cotar, SUM(IF(ifnull(D049_Flag_Tipo,'CP') = 'PD' AND D074_Id IS NULL, 1, 0)) as Itens_Sem_Engenharia FROM T006 
				LEFT JOIN T006A ON T006A_T006_Id = T006_Id
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
				LEFT JOIN T219 ON T006_Id=T219_T006_Id
				LEFT JOIN T218 ON T218_Id=T219_T218_Id
				LEFT JOIN T224 ON T218_Id=T224_T218_Id
				LEFT JOIN T225 ON T225_Id=T006_T225_Id
				LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
				LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
				LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
				LEFT JOIN T241 ON T241_D001_Id=D001_Id
				LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
				WHERE {$extra}";

		$retorno = mysql_query($sql);
		$row = mysql_fetch_array($retorno);

		log($sql);

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

	case 'est009GerarOT':
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

	case 'est009GerarOTEstoque':
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

	case 'est009VerificarCotacaoAutomatica':
		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND ifnull(T219_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

		$sql = "SELECT Fornecedor.D024_Id, T004_Id, Fornecedor.D024_Nome_Empresa, COUNT(D001_Id) as count FROM T004 
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
				LEFT JOIN T219 ON T004_Id=T219_T004_Id
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
				if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
					$semFornecedor = $row['count'];
				} else {
					$msg .= "{$row['D024_Nome_Empresa']} {$row['count']} produtos<br><br>";
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

	case 'est009GerarCotacaoEmergenciaAutomatica':
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();
		
		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND ifnull(T219_Id,0)<=0 AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

		$retorno = $EST015->gerarCotacaoAutomatica($extra);
		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est009VerificarCotacaoPedidoAutomatica':
		$semFornecedor = false;
		$semEngenharia = 0;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND ifnull(T219_Id,0)<=0 AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$sql = "SELECT Fornecedor.D024_Id, T004_Id, Fornecedor.D024_Nome_Empresa, COUNT(D001_Id) as count, ifnull(D049_Flag_Tipo,'CP') as D049_Flag_Tipo, IFNULL(D074_Id,0) as D074_Id FROM T006
				LEFT JOIN T006A ON T006A_T006_Id = T006_Id
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
				LEFT JOIN T219 ON T006_Id=T219_T006_Id
				LEFT JOIN T218 ON T218_Id=T219_T218_Id
				LEFT JOIN T224 ON T218_Id=T224_T218_Id
				LEFT JOIN T225 ON T225_Id=T006_T225_Id
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

	case 'est009RecuperarEstoqueLoja':
		
		require_once('bibliotecas/classes/EST009.php');
		$EST015 = new EST009();

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S'"), true);

		$retorno = $EST009->est009RecuperarEstoqueLoja($extra);	 	
		
		$resposta['code'] = $retorno[0];
		$resposta['data'] = $retorno[1];

	break;

	case 'est009GerarCotacaoPedidoEmergenciaAutomatica':
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND ifnull(T219_Id,0)<=0 AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);

		$retorno = $EST015->gerarCotacaoPedidoAutomatica($extra);
		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est009VerificarGerarOrdemCompra':
		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_T219_Id > 0 AND T219_Flag_Comprar = 'S'"));

		$sql = "SELECT D024_Id, D024_Nome_Empresa, COUNT(T006_Id) as count, T218_Id, GROUP_CONCAT(DISTINCT T218_Id SEPARATOR ',') FROM T006 
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
				LEFT JOIN T219 ON T219_T004_Id=T004_Id OR T006_T219_Id=T219_Id
				LEFT JOIN T218 on T218_Id=T219_T218_Id
				LEFT JOIN T225 ON T225_Id=T006_T225_Id
				LEFT JOIN T224 ON T224_Id=T225_T224_Id
				LEFT JOIN D024 ON D024_Id=T218_D024_Id
				LEFT JOIN T005A ON T005_Id=T005A_T005_Id
				WHERE {$extra}";
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY D024_Id";
		$retorno = mysql_query($sql);
		
		$qtde = mysql_num_rows($retorno);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";
		} else {
			if($qtde > 1){
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} ordens de compra para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma ordem de compra para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				$msg .= "{$row['D024_Nome_Empresa']} {$row['count']} produto(s)<br><br>";
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

	case 'est009VerificarQuantidade':

		$validaQuantidade = '';

		if($g['C031']['avisarQtaCompradaMaiorNecessario'] == 'S'){
			$extraOriginal = base64_decode($r_extra);
			$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_T219_Id > 0 AND T219_Flag_Comprar = 'S'"));
	
			$sql = "SELECT T219_Quantidade, 
						   T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as quantidadeFalta,
						   T006_T005_Id,
						   T006_Codigo_Produto,
						   T006_Descricao_Produto
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
				 LEFT JOIN T219 ON T219_T004_Id=T004_Id OR T006_T219_Id=T219_Id
				 LEFT JOIN T218 on T218_Id=T219_T218_Id
				 LEFT JOIN T225 ON T225_Id=T006_T225_Id
				 LEFT JOIN T224 ON T224_Id=T225_T224_Id
				 LEFT JOIN D024 ON D024_Id=T218_D024_Id
				 LEFT JOIN T005A ON T005_Id=T005A_T005_Id
					WHERE {$extra}";
			$sql = explode("LIMIT", $sql);
			$sql = $sql[0];
			$sql .= " ORDER BY T006_T005_Id";
			$T006 = mysql_query($sql);
			$error = mysql_error();
			
			while($mT006 = mysql_fetch_array($T006)){
				if($mT006['T219_Quantidade'] > $mT006['quantidadeFalta']){
					$validaQuantidade .= 'Pedido: '. $mT006['T006_T005_Id'] . ' | Código '.$mT006['T006_Codigo_Produto'] . ' - ' . $mT006['T006_Descricao_Produto'].":\n";
					$validaQuantidade .= '- Falta pedido : ' . gCorrigeNumero($mT006['quantidadeFalta']) . ' / Quantidade cotação : ' . gCorrigeNumero($mT006['T219_Quantidade']) ."\n\n";
				}
			}
		}
		
		if($validaQuantidade != ''){
			$retorno = "Atenção, os itens abaixo estão com a quantidade na cotação MAIOR que a quantidade necessária para o pedido:\n\n".$validaQuantidade;
			$resposta['code'] = false;
			$resposta['estoque'] = $retorno;
		} else {	
			$resposta['code'] = true;
		}
	break;

	case 'est009GerarOrdemCompra':
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_T219_Id > 0 AND T219_Flag_Comprar = 'S'"));

		$sql = "SELECT D024_Id, D024_Nome_Empresa, COUNT(T006_Id) as count, T218_Id, GROUP_CONCAT(DISTINCT T218_Id SEPARATOR ',') as listaT218 FROM T006 
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
				LEFT JOIN T219 ON T219_T004_Id=T004_Id OR T006_T219_Id=T219_Id
				LEFT JOIN T218 on T218_Id=T219_T218_Id
				LEFT JOIN T225 ON T225_Id=T006_T225_Id
				LEFT JOIN T224 ON T224_Id=T225_T224_Id
				LEFT JOIN D024 ON D024_Id=T218_D024_Id
				LEFT JOIN T005A ON T005_Id=T005A_T005_Id 
				WHERE {$extra}";
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY D024_Id";
		$res = mysql_query($sql);
		$qtde = mysql_num_rows($res);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";
		} else {
			$sqlT224 = mysql_query("SELECT T224_Grupo FROM T224 ORDER BY T224_Grupo DESC LIMIT 1");
            $resT224 = mysql_fetch_array($sqlT224);
            $grupo = $resT224['T224_Grupo'] + 1;

			while($row = mysql_fetch_array($res)){
				$retorno = $EST015->gerarPedido($row['T218_Id'], $row['listaT218'], $grupo);
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

	case 'est009VerificarCotacaoEstoqueAutomatica':
		$extraOriginal = base64_decode(urldecode($r_extra));
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id={$g['empresaAtual']}"));

		$semFornecedor = false;
		$msg = '<b style="font-size: 15px; text-align:center;">Atenção</b><br><br>';

		$sql = "SELECT fornecedor.D024_Id, fornecedor.D024_Nome_Empresa, GROUP_CONCAT(DISTINCT D009_Id SEPARATOR ', ') AS D009_Ids, COUNT(DISTINCT D009_Id) AS count FROM D009 
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
            left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
            LEFT JOIN D041 ON D041_D049_Id=D049_Id
            LEFT JOIN D005 ON D005_Id=D001_D005_Id
            LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
            LEFT JOIN T241 ON T241_D001_Id=D001_Id
            LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
			LEFT JOIN D009A ON D009A_D009_Id = D009_Id
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
				$msg .= "<span style='font-size: 14px;'>Serão geradas {$qtde} ordens de compra para os fornecedores:</span><br><br>";
			} else {
				$msg .= "<span style='font-size: 14px;'>Será gerada uma ordem de compra para o fornecedor:</span><br><br>";
			}

			while($row = mysql_fetch_array($retorno)){
				$msg .= "{$row['D024_Nome_Empresa']} {$row['count']} produtos<br><br>";
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

	case 'est009GerarCotacaoAutomatica':
		log("entra no est009GerarCotacaoAutomatica");
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$r_extra = urldecode($r_extra);

		$retorno = $EST015->gerarCotacaoEstoqueAutomatica($r_extra);

		if(!is_array($retorno)){
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		} else {
			$resposta['code'] = true;
			$resposta['data'] = $retorno['grupo'];
		}
	break;

	case 'est009VerificarGerarMontagem':
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

	case 'est009GerarMontagem':
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

	case 'est009VerificarGerarMontagemEstoque':
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

	case 'est009GerarMontagemEstoque':
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

	case 'adicionarProdutosOrdemCompra':
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$T224_Id = $r_T224_Id;
		if(empty($T224_Id)){
			$resposta['code'] = false;
			$resposta['data'] = "O número da ordem de compra deve estar preenchido";
			break;
		}

		$extraOriginal = base64_decode($r_extra);
		$extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_T219_Id > 0 AND T219_Flag_Comprar = 'S' AND T224_Id IS NULL"));

		$sql = "SELECT D024_Id, D024_Nome_Empresa, COUNT(T006_Id) as count, GROUP_CONCAT(DISTINCT T219_Id SEPARATOR ',') as listaT219 FROM T006 
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
				LEFT JOIN T219 ON T219_T004_Id=T004_Id OR T006_T219_Id=T219_Id
				LEFT JOIN T218 on T218_Id=T219_T218_Id
				LEFT JOIN T225 ON T225_Id=T006_T225_Id
				LEFT JOIN T224 ON T224_Id=T225_T224_Id
				LEFT JOIN D024 ON D024_Id=T218_D024_Id
				LEFT JOIN T005A ON T005_Id=T005A_T005_Id
				WHERE {$extra}";
		$sql = explode("LIMIT", $sql);
		$sql = $sql[0];
		$sql .= " GROUP BY D024_Id";
		$res = mysql_query($sql);
		$qtde = mysql_num_rows($res);
		if($qtde <= 0){
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum produto foi selecionado";
		} if($qtde > 1){
			$resposta['code'] = false;
			$resposta['data'] = "Foram selecionados produtos de fornecedores diferentes";
		} else {
			$row = mysql_fetch_array($res);		
			$retorno = $EST015->adicionarProdutosOrdemCompra($T224_Id, $row['listaT219']);
			if($retorno !== true){
				$resposta['code'] = false;
				$resposta['data'] = $retorno;
			} else {
				$resposta['code'] = true;
			}
		}
	break;
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
