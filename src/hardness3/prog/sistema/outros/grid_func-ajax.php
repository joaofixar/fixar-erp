<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {

	case 'ajaxEtapas':
		global $g;

        if ($r_inicio==1) {
			$sql = gDescomprimeSql($r_sql);
			
			log(" inicio $sql ");
            $sqlCache = mysql_query($sql);
			if (mysql_num_rows($sqlCache)<=0){
	        	$resposta['code'] = false;
	        	$resposta['erros'] = "Não existem registros para processar";
			}
            $erro = mysql_error();
	        if (!empty($erro)) {
	        	$resposta['code'] = false;
	        	$resposta['erros'] = "erro inicio: {$erro}";
	        }
        	$count=0;

        	while($rowCache = mysql_fetch_array($sqlCache)){
				foreach ($rowCache as $field => $value) {
					$cache[$count][$field]= $value;
				}
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
			@unlink("/tmp/cacheEtapas-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEtapas-resposta-{$g['usuarioAtual']}");
	        $retorno = file_put_contents("/tmp/cacheEtapas-{$g['usuarioAtual']}", serialize($cache));
	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }
        } else {
			$cache = unserialize(file_get_contents("/tmp/cacheEtapas-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheEtapas-resposta-{$g['usuarioAtual']}"));
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

		require_once("bibliotecas/classes/{$r_classe}.php");
		$classeCompleta = __NAMESPACE__ . "\\{$r_classe}";
		if (!class_exists($classeCompleta, false) && class_exists($r_classe, false)) {
			$classeCompleta = $r_classe;
		}
		if (!isset(${$r_classe}) || !is_object(${$r_classe})) {
			${$r_classe} = new $classeCompleta();
		}

		// Não funcinou a chamado da classe assim, para funcionar coloquei o instaniamento da classe no final do .php exemplo ver final VEN002.php
		//eval("\${$r_classe} = new {$r_classe}();");

		if (strpos($r_metodo,'()') > 0){
			$r_metodo = str_replace('(','($cache[$i],$cacheAnterior',$r_metodo);
		} else {
			$r_metodo = str_replace('(','($cache[$i],$cacheAnterior,',$r_metodo);
		}

	    for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 

			$cacheAnterior = false;
			if ($i>0) {
				$ii = $i-1;
				$cacheAnterior = $cache[$ii];
			} 
					
			$metodo = "\$retorno = \${$r_classe}->{$r_metodo};";

			log($metodo);

			eval($metodo);

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
			@unlink("/tmp/cacheEtapas-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEtapas-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        @unlink("/tmp/cacheEtapas-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        file_put_contents("/tmp/cacheEtapas-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}	        

	break;

	case 'ajaxEtapaUnica':
		global $g;

		if (!file_exists("bibliotecas/classes/{$r_classe}.php")){
			return "Classe bibliotecas/classes/{$r_classe}.php não encontrada.";
		}

		require_once("bibliotecas/classes/{$r_classe}.php");
		$classeCompleta = __NAMESPACE__ . "\\{$r_classe}";
		if (!class_exists($classeCompleta, false) && class_exists($r_classe, false)) {
			$classeCompleta = $r_classe;
		}
		if (!isset(${$r_classe}) || !is_object(${$r_classe})) {
			${$r_classe} = new $classeCompleta();
		}

		// Não funcinou a chamado da classe assim, para funcionar coloquei o instanciamento da classe no final do .php exemplo ver final VEN002.php
		//eval("\${$r_classe} = new {$r_classe}();");

		if (!empty($r_sql)) {
			if (strpos($r_metodo,'()') > 0){
				$r_metodo = str_replace('(','($r_sql',$r_metodo);
			} else {
				$r_metodo = str_replace('(','($r_sql,',$r_metodo);
			}
		}
					
		$metodo = "\$retorno = \${$r_classe}->{$r_metodo};";

		log($metodo);

		eval($metodo);

		if($retorno !== true){
      		$resposta['code'] = false;
      	 	$resposta['erros'] = $retorno;
        }
   
	break;

    case 'ajaxEtapasGrid':
        global $g;

        $registro = $r_registro;
        $lote = $r_lote;
        $camposForm = $r_camposForm ? json_decode(base64_decode($r_camposForm), true) : array();
        //$todosCamposCache = $lote == '0' ? array() : unserialize(apc_fetch("grid-cache-dados-{$r_cacheDados}"));
        $todosCamposCache = $lote == '0' ? array() : unserialize(file_get_contents("/tmp/grid-cache-dados-{$r_cacheDados}"));

        // Personalizações
        $botoes = Personalizacoes::getBotoes($r_gridId);
        $botoesPorId = array();
        foreach ($botoes as $key => $botao) {
            $botoesPorId[$botao['id']] = $key;
        }

        // Personalização
        $personalizado = array();


        if ($g['modoDesenvolvedor']) {
            $arquivos = array(
                "ide/personalizacaoBotoes/" . $r_gridId,
            );
        } else if ($g['modoDesenvolvedorApp']) {
            $arquivos = array(
                "ide/personalizacaoBotoes/" . $r_gridId,
                Apps::getDirAtivos() . "personalizacaoBotoes/" . $r_gridId,
                Apps::getDirEditor() . "personalizacaoBotoes/" . $r_gridId,
            );
        } else {
            $arquivos = array(
                "ide/personalizacaoBotoes/" . $r_gridId,
                Apps::getDirAtivos() . "personalizacaoBotoes/" . $r_gridId,
                "{$g['pathDados']}personalizacaoBotoes/" . $r_gridId,
            );
        }
        foreach ($arquivos as $arquivoPersonalizado) {
            if (file_exists($arquivoPersonalizado)) {
                $personalizado = unserialize(file_get_contents($arquivoPersonalizado));
                foreach ($personalizado as $botao) {
                    if (isset($botoesPorId[$botao['id']])) {
                        $botoes[$botoesPorId[$botao['id']]] = $botao;
                    } else {
                        $botoes[] = $botao;
                        end($botoes);
                        $botoesPorId[$botao['id']] = key($botoes);
                    }
                }
            }
        }
        $callback = $botoes[$botoesPorId[$r_id]]['callback'];

        if (file_exists("bibliotecas/funcoesCallback/{$callback}.php")) {
            $callUserFunc = 'hardness\\' . $callback;
            if (!function_exists($callUserFunc)) {
                require_once("bibliotecas/funcoesCallback/{$callback}.php");
            }
        } else {
            $codigo = $callback;
            $codigo = preg_replace('/function[^(]+\(/', '$callbackPersonalizado = function(', $codigo);
            $codigo = str_replace('<?php', '', $codigo);
            $codigo .= ';';
            eval($codigo);
            $callUserFunc = $callbackPersonalizado;
        }

        if ($lote == '0') {
            $retorno = call_user_func($callUserFunc, array_merge($_REQUEST, array('camposForm' => $camposForm)));
        } else if ($lote == 'js') {
            echo call_user_func($callUserFunc, array_merge($_REQUEST, array('camposForm' => $camposForm)));
            die();
        } else {
            for ($i = $registro; $i < ($registro + $lote); $i++) {
                if (isset($todosCamposCache[$i])) {
                    $retorno = call_user_func($callUserFunc, array_merge(
                        $_REQUEST,
                        array(
                            'campoAtual' => '',
                            'todosCampos' => $todosCamposCache[$i],
                            'camposForm' => $camposForm,
                            'registroAtual' => $i + 1,
                            'totalRegistros' => count($todosCamposCache),
                            'uniqId' => $r_uniqId,
                        )
                    ));
                }
            }
            if (!isset($todosCamposCache[$registro + $lote])) {
                @unlink("/tmp/grid-cache-dados-{$r_cacheDados}");
            }
        }

        if (is_array($retorno)) {
            if (array_key_exists('code', $retorno)) {
                $resposta['code'] = $retorno['code'];
            }
            if (array_key_exists('data', $retorno)) {
                $resposta['data'] = $retorno['data'];
            }
            if (array_key_exists('janela', $retorno)) {
                $resposta['janela'] = $retorno['janela'];
            }
            if (array_key_exists('janelaParametros', $retorno)) {
                $resposta['janelaParametros'] = $retorno['janelaParametros'];
            }
        }
        if ($lote == '0' || !isset($todosCamposCache[$registro + $lote])) {
            $resposta['concluido'] = 1;
        }

        break;

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
