<?php
namespace hardness;
/**
* FIN - FIN001 - Contas a Receber
* 
* Processamento de ações AJAX
*/
$resposta = array('code' => true, 'data' => array());

require_once('bibliotecas/classes/CAD002.php');
$CAD002 = new CAD002();
require_once('bibliotecas/webservices/tray/comunicacaoTray.php');
require_once('bibliotecas/webservices/nuvemshop/envioProdutosNuvemshop.php');
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'MarcarTodosProdutos':
		$flag = $r_flagMarcar; 
		if ($r_inicio==1){	
	    	$sql = base64_decode($r_extra);
	        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "", $sql);
	        $sqlCache = mysqli_query("SELECT D069_Id, D069_Flag_Tabela_Principal FROM D069 {$sql}");
	    	$count=0;
	    	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['D069_Id'] = $rowCache['D069_Id'];	
			    $cache[$count]['D069_Flag_Tabela_Principal'] = $rowCache['D069_Flag_Tabela_Principal'];		    
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 5);
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
	    	mysql_query("UPDATE D069 SET D069_Flag_Tabela_Principal='{$flag}' WHERE D069_Id='{$D069_Id}'");
	    	$erro = mysql_error();
			if(!empty($erro)){
				log("Erro mysql:".$erro);
	   	     	$erro++;
	       	 	$erros .= $erro ."<br />";
	       	}else{
	       		$ok++;
	       	}
		}

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){ 
			apc_delete("cacheD069-{$g['usuarioAtual']}");
			apc_delete("cacheD069-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheD069-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheD069-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	 
	break;

	case 'atualizarPreco':
		$D001_Id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		
		if($D001_Id>0){
			$C020=mysql_query("select C020_IPV_Atacado,C020_IPV_Revenda,C020_IPV_Varejo from C020 where C020_C004_Id={$g['empresaAtual']}");
			$mC020=mysql_fetch_array($C020);
			
			mysql_query("update D009
              	LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    set D009_Preco_1   	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
                        D009_Preco_2	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
                        D009_Preco_3	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
                  where D049_D001_Id='$D001_Id'
                    and D009_C004_Id={$g['empresaAtual']}
                    and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
					
					$resposta['data'] = "Atualizado! ID: " . $D001_Id;
		}else{
			mysql_query("update D009
              	LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    set D009_Preco_1   	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
                        D009_Preco_2 	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
                        D009_Preco_3	=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
                  where D009_C004_Id={$g['empresaAtual']}
                    and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
                    $resposta['data'] = "Atualizado!";
		}		
	break;
	
	case 'excluir':
		$T140_Id = isset($_REQUEST['T140_Id']) ? $_REQUEST['T140_Id'] : false;
		$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;

		$verifica = $g['conf']->restricaoExcluir('T140_Id', $T140_Id);

		if ($verifica !== true) {
			$resposta['code'] = false;
			$resposta['data'] = $verifica;
		} else {
			// excluir
			$retorno = $CAD002->excluir($T140_Id, $D001_Id);
			$resposta['data'] = 'T140_Id-' . $T140_Id;
		}
	break;

    case 'cad002CheckboxD069FlagTabelaPrincipal':
        $selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
        $D069_Id = isset($r_D069_Id) ? $r_D069_Id : false;
          $CAD002->updateCheckboxD069FlagTabelaPrincipal($D069_Id, $selected);
    break;
	
	case 'abreCertificado':
		$id = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;		
		$sql = 'select T140_Nome_Arquivo from T140 WHERE T140_Id ='.$id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T140_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/certificados/{$id}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T140_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;

	case 'atualizarEstoque':       
	
		$r_rapido = filter_var($r_rapido, FILTER_VALIDATE_BOOLEAN);
		if($r_rapido){
			$r_rapido = 150;
		}

		$retorno = $CAD002->D001_reprocessa_historico($r_acaoId,false,$r_rapido);

        if ($retorno !== TRUE)
        {
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }else{
			$resposta['code'] = true;
            $resposta['data'] = "Estoque Processado";
		}
	break;
	

    case 'buscarEstoqueTray':
		global $g;
		$resposta['code'] = true;
		$Tray = new ComunicacaoTray();
		
		if($r_pagina==1){
			//apc_delete("cacheEstoqueTray-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEstoqueTray-{$g['usuarioAtual']}");
			$products = array();
		}else{
			//$products = unserialize(apc_fetch("cacheEstoqueTray-{$g['usuarioAtual']}"));
			$products = unserialize(file_get_contents("/tmp/cacheEstoqueTray-{$g['usuarioAtual']}"));
		}
	
    	$retornoLista = $Tray->listarProdutos($r_pagina);
    	
        if ($retornoLista==0) {
        	apc_delete("cacheEstoqueTray-{$g['usuarioAtual']}");
            $resposta['code']=false;
        	$resposta['data']= "Erro ao buscar produtos na Tray, verifique o log de erros.";
        }else if($retornoLista==1){
        	$resposta['concluido'] = 1;
        }else{
        	$paging = (array) $retornoLista['paging'];
	    	$paginas = ceil($paging['total']/$paging['limit']);
	    	$resposta['percentual'] = floor((100/$paginas) * $r_pagina);
            $products = array_merge($products,$retornoLista['Products']);

            //$retorno = apc_store("cacheEstoqueTray-{$g['usuarioAtual']}", serialize($products));
			$retorno = file_put_contents("/tmp/cacheEstoqueTray-{$g['usuarioAtual']}", serialize($products));

            $resposta['concluido'] = 0;

            if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        	$resposta['concluido'] = 1;
        	}
        }
    break;

	case 'atualizarEstoqueNuvemshop':
		global $g;
		$resposta['code'] = true;
	
		$envioProdutos = new EnvioProdutosNuvemshop();
		$envioProdutos->executar();

		$resposta['data']= "0";
    break;

    case 'atualizarEstoqueTray':
		global $g;
		$resposta['code'] = true;
	
		$Tray = new ComunicacaoTray();

		/*inicio calculo etapas*/
        if ($r_inicio==1){
    	
	    	//$retornoLista = unserialize(apc_fetch("cacheEstoqueTray-{$g['usuarioAtual']}"));
			$retornoLista = unserialize(file_get_contents("/tmp/cacheEstoqueTray-{$g['usuarioAtual']}"));

			//apc_delete("cacheEstoqueTray-{$g['usuarioAtual']}");

        	$count = 0;
        	$produtosEncontrados = 0;
        	$produtosNaoEncontrados = 0;
        	foreach ($retornoLista as $key => $product) {
	            $product = (array) $product;
	            $product = (array) $product['Product'];

	            $cache[$count]['id'] = $product['id'];
            	$cache[$count]['nome'] = $product['name'];
            	$cache[$count]['ean'] = $product['ean'];
            	$cache[$count]['estoqueTray'] = $product['stock'];
		    	$cache[$count]['erro'] = "";

	            $D001_Codigo_Barras = $product['ean'];
        		if(!empty($D001_Codigo_Barras)){
        			$sqlD001 = mysql_query("SELECT D009_Id,
                                           D009_Quantidade_Estoque_Liquido,
                                           D009_Quantidade_Estoque_Fora 
                                      FROM D009     
                                 LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                 LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                 WHERE D001_Codigo_Barras = '{$D001_Codigo_Barras}' AND D009_C004_Id = '{$g['empresaAtual']}'");
        			$erro = mysql_error();

        			if(!empty($erro)){
        				log("Erro mysql:".$erro);
        			}
        			
            		if(mysql_num_rows($sqlD001) > 0){
		                $produtosEncontrados++;
		                $rowD001 = mysql_fetch_array($sqlD001);		                
		                $estoqueTotal = $rowD001['D009_Quantidade_Estoque_Liquido']+$rowD001['D009_Quantidade_Estoque_Fora'];		                
					    $cache[$count]['estoqueSistema'] = $estoqueTotal;	
					    $cache[$count]['erro'] = "";					                  
	                }else{
	                	$cache[$count]['estoqueSistema'] = 0;	
	                	$cache[$count]['erro'] = "EAN não cadastrada no sistema.";
	                	$produtosNaoEncontrados++;
	                }
            	}else{
            		$cache[$count]['estoqueSistema'] = 0;	
            		$cache[$count]['erro'] = "EAN em branco na Tray.";
            		$produtosNaoEncontrados++;
            	}           	

			    $count++;
			}

			$resposta['produtosEncontrados'] = $produtosEncontrados;
			$resposta['produtosNaoEncontrados'] = $produtosNaoEncontrados;
			
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 10);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			//apc_delete("cacheTray-{$g['usuarioAtual']}");
			//apc_delete("cacheTray-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheTray-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheTray-resposta-{$g['usuarioAtual']}");

	        //$retorno = apc_store("cacheTray-{$g['usuarioAtual']}", serialize($cache));
			$retorno = file_put_contents("/tmp/cacheTray-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = "Erro ao criar variável no cache";
	        }
	    } else {
			//$cache = unserialize(apc_fetch("cacheTray-{$g['usuarioAtual']}"));
			$cache = unserialize(file_get_contents("/tmp/cacheTray-{$g['usuarioAtual']}"));
			//$resposta = unserialize(apc_fetch("cacheTray-resposta-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheTray-resposta-{$g['usuarioAtual']}"));
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

		if($resposta['limit2'] < $resposta['totalRegistros']){
	    	$resposta['etapa'] = $resposta['etapa'] + 1;	
	    }else{
	    	$resposta['concluido'] = 1;
	    }
	   
	    $r_opcoes = json_decode($r_opcoes);

	    if(!is_array($r_opcoes)) { 
	        $opcoes = get_object_vars($r_opcoes);
	    }

	    $ok = 0;
		$erro = 0;
		$retornoTray = "";
		$retorno = 0;
	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 
	    	$idProduto = $cache[$i]['id'];
	    	$eanProduto = $cache[$i]['ean'];
	    	$estoqueSistema = $cache[$i]['estoqueSistema'];

	    	if(empty($cache[$i]['erro'])){
    			$retorno = $Tray->atualizarProduto($idProduto,$estoqueSistema,$eanProduto);
    			if($retorno === true){
	       	     	$ok++;
	       	     	$retornoTray =  "Atualizado com sucesso.";
	       		}else{
	       			$erro++;
	       			$retornoTray = "Falha de comunicação com Tray.";
	       		}
	       		$cache[$i]['retornoTray'] = $retornoTray;
	    	}else{
	    		$erro++;
	    		$cache[$i]['retornoTray'] = "Comunicação não realizada.";
	    	}       		
        }

        //apc_store("cacheTray-{$g['usuarioAtual']}", serialize($cache));
		$retorno = file_put_contents("/tmp/cacheTray-{$g['usuarioAtual']}", serialize($cache));

	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    
		if($etapa > $etapas){
			//apc_delete("cacheTray-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheTray-resposta-{$g['usuarioAtual']}");
		} else {
	        //apc_delete("cacheTray-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheTray-resposta-{$g['usuarioAtual']}");
	        //apc_store("cacheTray-resposta-{$g['usuarioAtual']}", serialize($resposta));
			file_put_contents("/tmp/cacheTray-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}      
    break;

    case 'atualizarEstoqueGeral':

    	/** 
		* Funcionamento em etapas
    	*/
       	$valorTotal = $r_valorTotal; 
    	$registrosEtapa = floor($valorTotal / $r_etapas);
    	$etapa = $r_etapa;
    	$limit1 = floor(( $registrosEtapa * $etapa ) - $registrosEtapa);
    	if($etapa >= $r_etapas){
    		$limit2 = ceil($registrosEtapa + ($valorTotal - ($limit1 + $registrosEtapa)));
    	} else {
    		$limit2 = $registrosEtapa ;
    	}
    	$concluido = 0;

        $resposta['percentual'] = ceil((100/$r_etapas) * $etapa);
        
    	if($limit2 < $valorTotal){
        	$etapa = $r_etapa + 1;	
        }else{
        	$concluido = 1;
        }
        $resposta['etapa'] = $etapa;
        $resposta['concluido'] = $concluido;
    	// --

     	$sql = base64_decode($r_extraSql);
		$sql = gRetirarOrderBy($sql);
     	$sql = $sql . " LIMIT 0,100";
        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "LIMIT $limit1 , $limit2", $sql);
        $sql = "SELECT D001_Id FROM D001 ".$sql;
        log("SQL= ".$sql);

        $sql = mysql_query($sql);
    	$ok = 0;
    	$erro = 0;
    	$erros ="";

    	while($row = mysql_fetch_array($sql)){
         	set_time_limit(0);
            $retorno = $CAD002->D001_reprocessa_historico($row['D001_Id'],false);
            $retorno = true;
            if($retorno !== true){
             	$erro++;
             	$erros .= $erro ." - ". $retorno."<br />";
            }else{
             	$ok++;
            }
        }

        $mysql_erro=mysql_error();
        if (!empty($mysql_erro)){
        	$erros .= $mysql_erro;
        }
        
    	$resposta['data']="Ocorreu um erro inesperado!";
        $resposta['code'] = true;
        $resposta['ok'] = $ok;
        $resposta['erro'] = $erro;
        $resposta['erros'] = $erros;
	break;
	

	case 'atualizarEstoqueRapido': 
    	/** 
		* Funcionamento em etapas
    	*/
		$valorTotal = $r_valorTotal; 
    	$registrosEtapa = floor($valorTotal / $r_etapas);
    	$etapa = $r_etapa;
    	$limit1 = floor(( $registrosEtapa * $etapa ) - $registrosEtapa);
    	if($etapa >= $r_etapas){
    		$limit2 = ceil($registrosEtapa + ($valorTotal - ($limit1 + $registrosEtapa)));
    	} else {
    		$limit2 = $registrosEtapa ;
    	}
    	$concluido = 0;

        $resposta['percentual'] = ceil((100/$r_etapas) * $etapa);
        
    	if($limit2 < $valorTotal){
        	$etapa = $r_etapa + 1;	
        }else{
        	$concluido = 1;
        }
        $resposta['etapa'] = $etapa;
        $resposta['concluido'] = $concluido;
    	// --

     	$sql = base64_decode($r_extraSql);
  	    $sql = gRetirarOrderBy($sql);
     	$sql = $sql . " LIMIT 0,100";
        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', "LIMIT $limit1 , $limit2", $sql);
        $sql = "SELECT D001_Id, D009_Id FROM D001 ".$sql;
        log("SQL= ".$sql);

        $sql = mysql_query($sql);
    	$ok = 0;
    	$erro = 0;
    	$erros ="";

    	while($row = mysql_fetch_array($sql)){
         	set_time_limit(0);
            $retorno = $CAD002->D001_reprocessa_historico(false,$row['D009_Id'],500);
            $retorno = true;
            if($retorno !== true){
             	$erro++;
             	$erros .= $erro ." - ". $retorno."<br />";
            }else{
             	$ok++;
            }
        }

        $mysql_erro=mysql_error();
        if (!empty($mysql_erro)){
        	$erros .= $mysql_erro;
        }
        
    	$resposta['data']="Ocorreu um erro inesperado!";
        $resposta['code'] = true;
        $resposta['ok'] = $ok;
        $resposta['erro'] = $erro;
        $resposta['erros'] = $erros;

	break;

	
	case 'gravarCampo':
		$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$D001_Id = isset($_REQUEST['D001_Id']) ? $_REQUEST['D001_Id'] : false;
		$CAD002->updateD049_Flag_Padrao($D001_Id, $selected);
	break;
	
	case 'alterarICF1':
		$r_valorDigitado = str_replace('.', '', $r_valorDigitado);
		$r_valorDigitado = str_replace(',', '.', $r_valorDigitado);
		$valorDigitado = isset($r_valorDigitado) ? $r_valorDigitado : false;
		$sql = isset($r_extra) ? base64_decode($r_extra) : '';
		$sql = explode('LIMIT', $sql);
		$sql = stripslashes($sql[0]);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			$retorno = mysql_query("update D049 set D049_ICF='{$valorDigitado}' WHERE D049_Id = '{$row['D049_Id']}';");
		}
		if ($retorno == TRUE)
		{
			$resposta['code'] = false;
			$resposta['data'] = "ICF 1 alterado com sucesso!";
		} 
	break;
	
	case 'alterarICF2':
		$r_valorDigitado = str_replace('.', '', $r_valorDigitado);
		$r_valorDigitado = str_replace(',', '.', $r_valorDigitado);
		$valorDigitado = isset($r_valorDigitado) ? $r_valorDigitado : false;
		$sql = isset($r_extra) ? base64_decode($r_extra) : '';
		$sql = explode('LIMIT', $sql);
		$sql = stripslashes($sql[0]);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			$retorno = mysql_query("update D049 set D049_ICF1='{$valorDigitado}' WHERE D049_Id = '{$row['D049_Id']}';");
		}
		if ($retorno == TRUE)
		{
			$resposta['code'] = false;
			$resposta['data'] = "ICF 2 alterado com sucesso!";
		} 
	break;
	
	case 'alterarICF3':
		$r_valorDigitado = str_replace('.', '', $r_valorDigitado);
		$r_valorDigitado = str_replace(',', '.', $r_valorDigitado);
		$valorDigitado = isset($r_valorDigitado) ? $r_valorDigitado : false;
		$sql = isset($r_extra) ? base64_decode($r_extra) : '';
		$sql = explode('LIMIT', $sql);
		$sql = stripslashes($sql[0]);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			$retorno = mysql_query("update D049 set D049_ICF2='{$valorDigitado}' WHERE D049_Id = '{$row['D049_Id']}';");
		}
		if ($retorno == TRUE)
		{
			$resposta['code'] = false;
			$resposta['data'] = "ICF 3 alterado com sucesso!";
		} 
	break;
	
	case 'alterarIPV':
		$r_valorDigitado = str_replace('.', '', $r_valorDigitado);
		$r_valorDigitado = str_replace(',', '.', $r_valorDigitado);
		$valorDigitado = isset($r_valorDigitado) ? $r_valorDigitado : false;
		$sql = isset($r_extra) ? base64_decode($r_extra) : '';
		$sql = explode('LIMIT', $sql);
		$sql = stripslashes($sql[0]);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res)) {
			$retorno = mysql_query("update D049 set D049_IPV='{$valorDigitado}' WHERE D049_Id = '{$row['D049_Id']}';");
		}
		if ($retorno == TRUE)
		{
			$resposta['code'] = false;
			$resposta['data'] = "IPV alterado com sucesso!";
		} 
	break;
	
	case 'abreFotoProduto':
		$idFoto = (isset($r_idFoto)) ? $r_idFoto : false;
		$idProduto = (isset($r_idProduto)) ? $r_idProduto : false;
		// echo "idFoto: ".$idFoto ."<br />idProd: ".$idProduto;
		// die();
		$sql = 'select T172_Nome_Arquivo from T172 WHERE T172_Id ='.$idFoto;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$pathinfo = pathinfo($row['T172_Nome_Arquivo']);
		$cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
		if (file_exists($cArquivo))
		{
			$finfo = new \finfo();
		    header('Content-type: ' . $finfo->file($cArquivo, FILEINFO_MIME_TYPE));
		    header('Content-Disposition: attachment; filename="' . $row['T172_Nome_Arquivo'] . '"');
		    readfile($cArquivo);
		}
		else
		{
			echo "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
			return false;
		}
		die();
	break;
	
	case 'aplicarValores':
		global $g;
		$ICF = (isset($r_ICF)) ? $r_ICF : false;
		$IPV_1 = (isset($r_IPV1)) ? $r_IPV1 : false;
		$IPV_2 = (isset($r_IPV2)) ? $r_IPV2 : false;
		$IPV_3 = (isset($r_IPV3)) ? $r_IPV3 : false;
		$CustoManual = (isset($r_CustoManual)) ? $r_CustoManual : false;
		$Desconto1 = (isset($r_Desconto1)) ? $r_Desconto1 : false;
		$Desconto2 = (isset($r_Desconto2)) ? $r_Desconto2 : false;
		$Desconto3 = (isset($r_Desconto3)) ? $r_Desconto3 : false;
		$Promocao = (isset($r_Promocao)) ? $r_Promocao : false;
		$Promocao = ($Promocao == 'true') ? 'S' : 'N';
		$sqlParam = (isset($r_sqlParam)) ? $r_sqlParam : false;
		$extraParam = (isset($r_extraParam)) ? $r_extraParam : false;
		
		$ICF = gCorrigeNumeroInverte($ICF);
		$IPV_1 = gCorrigeNumeroInverte($IPV_1);
		$IPV_2 = gCorrigeNumeroInverte($IPV_2);
		$IPV_3 = gCorrigeNumeroInverte($IPV_3);
		$CustoManual = gCorrigeNumeroInverte($CustoManual);
		
		$campos = "";
		
		$campos .= (empty($ICF)) ? "" : "D009_ICF_1='{$ICF}',";
		$campos .= (empty($IPV_1)) ? "" : "D009_IPV_1='{$IPV_1}',";
		$campos .= (empty($IPV_2)) ? "" : "D009_IPV_2='{$IPV_2}',";
		$campos .= (empty($IPV_3)) ? "" : "D009_IPV_3='{$IPV_3}',";
		$campos .= (empty($CustoManual)) ? "" : "D009_Valor_Preco_Tabela='{$CustoManual}',";
		$campos .= (empty($Desconto1)) ? "" : "D009_Preco_1=(D009_Preco_1-((D009_Preco_1/100) * {$Desconto1})),";
		$campos .= (empty($Desconto2)) ? "" : "D009_Preco_2=(D009_Preco_2-((D009_Preco_2/100) * {$Desconto2})),";
		$campos .= (empty($Desconto3)) ? "" : "D009_Preco_3=(D009_Preco_3-((D009_Preco_3/100) * {$Desconto3})),";
		$campos .= ($Promocao == "N") ? "" : "D009_Flag_Promocao='{$Promocao}',";
		
		if(empty($campos)){
			$resposta['code'] = false;
			$resposta['data'] = "Você não informou nenhum valor";
		} else {
			$campos = trim($campos, ",");

			$extraParam = base64_decode($extraParam);

			$extraParam = str_replace("\\", "", $extraParam);

			$where = 'WHERE ' . gPegarParteWhere($extraParam);
			
			$where = gInsertExtraWhere($where, "(D009_Flag_Preco_Tabelado!='S' OR  D009_Flag_Preco_Tabelado IS NULL) AND D009_C004_Id='{$g['empresaAtual']}'");

			$extraParam = preg_split('/where/i', $extraParam);

			$Joins = $extraParam[0];

			$sql = "UPDATE D049 {$Joins} SET {$campos} {$where};";
            $ret = mysql_query($sql);
            if(!$ret){
            	$resposta['code'] = false;
				$resposta['data'] = "Houve alguma erro. SQL: ".$sql;
            }

			$sql = "UPDATE D049 {$Joins} SET D009_Valor_Custo_Unitario=(D009_Valor_Custo_Compra*D009_ICF_1) {$where};";
            $ret = mysql_query($sql);
            if(!$ret){
            	$resposta['code'] = false;
				$resposta['data'] = "Houve alguma erro. SQL: ".$sql;
            }

			$sql = "UPDATE D049 {$Joins} SET 
				                D009_Preco_1=(D009_Valor_Custo_Unitario*D009_IPV_1),
				                D009_Preco_2=(D009_Valor_Custo_Unitario*D009_IPV_2),
				                D009_Preco_3=(D009_Valor_Custo_Unitario*D009_IPV_3)
				                {$where};";
            $ret = mysql_query($sql);
            if(!$ret){
            	$resposta['code'] = false;
				$resposta['data'] = "Houve alguma erro. SQL: ".$sql;
            }else{
            	$resposta['code'] = true;
            }
		}
	break;
	
	case 'CAD002checkboxCampoPromocao':
		$selected = isset($r_selected) ? $r_selected : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		mysql_query("UPDATE D009 SET D009_Flag_Promocao='{$selected}' WHERE D009_Id='{$D009_Id}'");
	break;
	
	case 'CAD002checkboxCampoPrecoTabelado':
		$selected = isset($r_selected) ? $r_selected : false;
		$D009_Id = isset($r_D009_Id) ? $r_D009_Id : false;
		mysql_query("UPDATE D009 SET D009_Flag_Preco_Tabelado='{$selected}' WHERE D009_Id='{$D009_Id}'");	
	break;
	case 'reprocessarValidade':
		$retorno = $CAD002->reprocessarValidade();
		if ($retorno !== TRUE)
		{
			$resposta['code'] = false;
			$resposta['data'] = $retorno;
		}
	break;

	case 'cad002AjaxGerarXmlProdutos':
		$extra = isset($r_extraSql) ? $r_extraSql : false;
		$retorno = $CAD002->cad002GerarXmlProdutos($extra);
	break;

	case 'limparLocacao':
		$T066_Id = mysql_real_escape_string($r_T066_Id);
		$sql = "UPDATE T066 SET T066_D004_Id = NULL WHERE T066_Id = '{$T066_Id}'";
		mysql_query($sql);
	break;

	case 'atualizarPrecosPorPeso':
		$sql = base64_decode($r_extraSql);
        /*$sql = str_replace("LIMIT 0,100", "", $sql);
        $sql = str_replace("LIMIT 0,200", "", $sql);
        $sql = str_replace("LIMIT 0,300", "", $sql);
        $sql = str_replace("LIMIT 0,500", "", $sql);
        $sql = str_replace("LIMIT 0,1000", "", $sql);*/
        $sql = preg_replace('/LIMIT [0-9]+,[0-9]+/', '', $sql);
        $sql = mysql_query($sql);
        $ok=0;
        $erro=0;
        $erros="";
        while($row = mysql_fetch_array($sql)){
        	set_time_limit(0);
            $retorno = $CAD002->atualizarPrecosPorPeso($row['D009_Id']);
            if($retorno !== true){
            	$erro++;
            	$erros .= $erro ." - ". $retorno."<br />";
            }else{
            	$ok++;
            }
        }
        $mysql_erro=mysql_error();
        if (!empty($mysql_erro)){
        	$erros .= $mysql_erro;
        }

        $resposta['code'] = true;
        $resposta['ok'] = $ok;
        $resposta['erro'] = $erro;
        $resposta['erros'] = $erros;
	break;

	case 'atualizarPrecoVendaTransferencia':
		// $dadosPrecoVenda 		  = unserialize(base64_decode($r_retornaPrecoVenda));
		// $precoVendaProduto        = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
  //       $demonstrativoPrecoVenda  = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
  //       $custoProduto             = $dadosPrecoVenda[4];
  //       $origemProduto            = $dadosPrecoVenda[5];

        if(empty($r_tabela) || empty($r_idItemVenda) || empty($r_retornaPrecoVenda) || empty($r_C004_Id_Transferencia)){
			$resposta['code'] = false;
			$resposta['data'] = "Erro ao atualizar preço tabela";
		}
		if($r_tabela=='T004'){
			$sqlT004 = mysql_query("SELECT T004_Valor_Preco_Transferencia,
								T004_Demonstrativo_Preco_Transferencia,
								T004_Valor_Custo_Transferencia,
								T004_Flag_Origem_Produto_Transferencia 
								FROM T004
								WHERE T004_Id = '{$r_idItemVenda}'");
			$T004 = mysql_fetch_array($sqlT004);

			$precoVendaProduto        = $T004['T004_Valor_Preco_Transferencia'];
	        $demonstrativoPrecoVenda  = mysql_real_escape_string(serialize(unserialize($T004['T004_Demonstrativo_Preco_Transferencia'])));
	        $custoProduto             = $T004['T004_Valor_Custo_Transferencia'];
	        $origemProduto            = $T004['T004_Flag_Origem_Produto_Transferencia'];
		}else if($r_tabela=='T006'){
			$sqlT006 = mysql_query("SELECT T006_Valor_Preco_Transferencia,
								T006_Demonstrativo_Preco_Transferencia,
								T006_Valor_Custo_Transferencia,
								T006_Flag_Origem_Produto_Transferencia 
								FROM T006
								WHERE T006_Id = '{$r_idItemVenda}'");
			$T006 = mysql_fetch_array($sqlT006);

			$precoVendaProduto        = $T006['T006_Valor_Preco_Transferencia'];
	        $demonstrativoPrecoVenda  = mysql_real_escape_string(serialize(unserialize($T006['T006_Demonstrativo_Preco_Transferencia'])));
	        $custoProduto             = $T006['T006_Valor_Custo_Transferencia'];
	        $origemProduto            = $T006['T006_Flag_Origem_Produto_Transferencia'];
		}

		$resposta['code'] = true;
		switch ($r_tabela) {
			case 'T004':
				mysql_query("UPDATE T004
	                            SET T004_Valor_Preco_Original        = '{$precoVendaProduto}',
	                                T004_Valor_Preco_Original_Bruto  = '{$precoVendaProduto}',
	                                T004_Valor_Custo_Unitario        = '{$custoProduto}',
	                                T004_Flag_Origem_Produto         = '{$origemProduto}',
	                                T004_Demonstrativo_Preco_Venda   = '{$demonstrativoPrecoVenda}',
	                                T004_C004_Id_Transferencia 		 = '{$r_C004_Id_Transferencia}',
	                                T004_Flag_Transferencia 		 = 'S'
	                          WHERE T004_Id = '{$r_idItemVenda}'");
			break;
			case 'T006':
				 mysql_query("UPDATE T006
				 				 SET T006_Valor_Preco_Original    	  = '{$precoVendaProduto}',
                                     T006_Valor_Preco_Original_Bruto  = '{$precoVendaProduto}',
                                     T006_Valor_Custo_Unitario        = '{$custoProduto}',
                                     T006_Flag_Origem_Produto         = '{$origemProduto}',
                                     T006_Demonstrativo_Preco_Venda   = '{$demonstrativoPrecoVenda}',
                                     T006_C004_Id_Transferencia 	  = '{$r_C004_Id_Transferencia}',
                                     T006_Flag_Transferencia 		  = 'S'
                               WHERE T006_Id = '{$r_idItemVenda}'");
			break;
			default:
				$resposta['code'] = false;
				$resposta['data'] = "Erro ao atualizar preço tabela";
			break;
		}
	break;

	case 'checkboxExcluirD069':
		$D069_Id = isset($r_D069_Id) ? $r_D069_Id : false;
		$selected = isset($r_selected) ? $r_selected : false;
		mysqli_query("UPDATE D069 SET D069_Flag_Excluir = '{$selected}' WHERE D069_Id = '{$D069_Id}'");
		$resposta['code'] = true;
	break;

	case 'excluirD069Marcadosx':
		$D070_Id = isset($r_D070_Id) ? $r_D070_Id : false;
		$sql = mysql_query("SELECT D069_Id FROM D069 WHERE D069_D070_Id = '{$D070_Id}' and D069_Flag_Excluir = 1");

		if(mysql_num_rows($sql) > 0){
			$countD069=0;
			while($row = mysql_fetch_Array($sql)){
				$countD069++;
				mysql_query("DELETE FROM D069 WHERE D069_Id = '{$row['D069_Id']}' and D069_Flag_Excluir = 1");
			}

			$resposta['code'] = true;
			$resposta['data'] = "{$countD069} Itens excluidos com sucesso";
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Nenhum item selecionado!";
		}
	break;

	case 'excluirD069Marcados':

		$D070_Id = isset($r_D070_Id) ? $r_D070_Id : false;
		
		if ($r_inicio==1){	
			$sql = base64_decode($r_extra);
			$sql = gInsertExtraWhere($sql, "D069_Flag_Excluir = 1");
			$sqlCache = mysql_query("SELECT D069_Id, D069_Flag_Tabela_Principal FROM D069 {$sql}");
			$count=0;
	    	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['D069_Id'] = $rowCache['D069_Id'];	
			    $cache[$count]['D069_Flag_Tabela_Principal'] = $rowCache['D069_Flag_Tabela_Principal'];		    
				$count++;
			}
			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 5);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
		    $resposta['erro'] = 0;
	    	$resposta['concluido'] = 0;
			apc_delete("cacheD069ExcluirProduto-{$g['usuarioAtual']}");
			apc_delete("cacheD069ExcluirProduto-resposta-{$g['usuarioAtual']}");
	        $retorno = apc_store("cacheD069ExcluirProduto-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }
	    } else {
			$cache = unserialize(apc_fetch("cacheD069ExcluirProduto-{$g['usuarioAtual']}"));
			$resposta = unserialize(apc_fetch("cacheD069ExcluirProduto-resposta-{$g['usuarioAtual']}"));
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
	    	mysql_query("DELETE FROM D069 WHERE D069_Id = '{$D069_Id}' and D069_Flag_Excluir = 1");
	    	$erro = mysql_error();
			if(!empty($erro)){
				log("Erro mysql:".$erro);
	   	     	$erro++;
	       	 	$erros .= $erro ."<br />";
	       	}else{
	       		$ok++;
	       	}
		}

		$resposta['data'] = '';        
	    $resposta['ok'] += $ok;
	    $resposta['erro'] += $erro;
	    $resposta['erros'] .= $erros;

		if($resposta['etapa'] >= $resposta['etapas']){ 
			apc_delete("cacheD069ExcluirProduto-{$g['usuarioAtual']}");
			apc_delete("cacheD069ExcluirProduto-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        apc_delete("cacheD069ExcluirProduto-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        apc_store("cacheD069ExcluirProduto-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}
	break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
