<?php
namespace hardness;
if (!function_exists('hardness\campoLinkProdutoPesquisaGrid')) {
	include 'prog/sistema/funcoes/pesquisaGridCallback.php';
}

/**
* Grid de pesquisa para o campo do formulário tipo "pesquisaGrid"
*/
switch($g['r'][3]){

	case 'pesquisa':
		$resposta = array('code' => true, 'data' => array());
		$g['r_'] = $g['r_'] . '?pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&campo15=' . urlencode($r_campo15) . '&campo16=' . urlencode($r_campo16);

		// Monta array com chaves primarias
		$chaves = explode(',', $r_campo2);

		// Defie como campos do select apenas as chaves primarias
		$campos = $chaves;
		if (strpos($r_campo3, '||') !== false) {
			$campos = explode('||', $r_campo3);
		} else {
			$campos = explode(',', $r_campo3);
		}
		$camposDesc = explode(',', $r_campo6);
		$r_pesquisa = mysql_real_escape_string($r_pesquisa);
		$r_campo9 = unserialize($r_campo9);
		$r_campo10 = unserialize($r_campo10);
		
		$campos11 = explode('||', $r_campo11);

		// Parametros extras (CAMPO 9) podem ser necessario nas personalizacoes, por isso vamos transformar eles em váriaveis aqui - exemplo: [[r_extra1]], [[r_extra2]], ...
		foreach ($r_campo9 as $key => $valor) {
			$var = 'r_extra' . ($key + 1);
			$$var = $valor;
		}

		// Parametros extras (CAMPO 9) podem ser necessario nas personalizacoes, por isso vamos transformar eles em váriaveis aqui - exemplo: [[r_extra1]], [[r_extra2]], ...
		foreach ($r_campo9 as $key => $valor) {
			$var = 'r_extra' . ($key + 1);
			$$var = $valor;
		}

		/**
		* Definição do Grid (Tabela)
		*/
		require_once('bibliotecas/classes/FRAMEWORK.php');
		$FRAMEWORK = new FRAMEWORK();
		list($where, $grid, $explode) = $FRAMEWORK->montaPesquisaGrid($r_pesquisa, $chaves);

		foreach ($campos as $id => $campo) {
			$campo = trim($campo);
			$campo = str_replace('[[virgula]]', ',', $campo);
            // campo já existe como chave primária, vamos remover ele e colocar o novo
            if (in_array($campo, $chaves)) {
                $arrayTmp['chavePrimaria'] = true;
            } 
            // campo já existe como chave primária, vamos remover ele e colocar o novo
            if (in_array($campo, $chaves)) {
                foreach ($grid as $key => $value) {
                    if ($value['colunaValor'] == $campo) {
                    	unset($grid[$key]);
                    }
                }
            }
			// colunas com ' as ' atualmente não entram no WHERE
			if (strpos($campo, ' as ') !== false) { continue; }
			if (in_array($campo, $campos11)) { continue; }
			if (!isset($r_pegarExtra)) {
				foreach ($explode as $r_pesquisa_key => $r_pesquisa_item) {
					$logica = ($r_pesquisa_key == 0) ? 'OR (' : 'AND';
					$r_pesquisa_item = str_replace(" ", "%", $r_pesquisa_item);
					if($r_campo15 == '-%'){
						$where .= (empty($where)) ? "WHERE (({$campo} LIKE '{$r_pesquisa_item}%'" : " {$logica} {$campo} LIKE '{$r_pesquisa_item}%'";	
					} else if($r_campo15 == '='){
						$where .= (empty($where)) ? "WHERE (({$campo} = '{$r_pesquisa_item}'" : " {$logica} {$campo} = '{$r_pesquisa_item}'";	
					} else {
						$where .= (empty($where)) ? "WHERE (({$campo} LIKE '%{$r_pesquisa_item}%'" : " {$logica} {$campo} LIKE '%{$r_pesquisa_item}%'";
					}
				}
				$where .= ')';
			}
		}
		$grid = array_values($grid);

		if (!empty($where)) {
			$where .= ')';
		} else {
			$where .= 'WHERE (1=1)';
		}

		// Tabela
		$from = $r_campo1;
		
		$r_pesquisaGridId = $pesquisaGridIdBase."_".md5($g['r'][0]."/".$g['r'][1]."/".$g['r'][2]."/".$g['r'][3]."/");

        $todosCampos = $r_todosCampos ? json_decode($r_todosCampos) : '';
        if ($todosCampos) {
            foreach ($todosCampos as $key => $value) {
                $r_campo4 = str_replace("[[{$key}]]", $value, $r_campo4);
            }
        }

		// Gera o filtro e acrescenta ao $extra
		$gridFiltro = array();
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $r_campo4, $r_pesquisaGridId);

		// Adiciona a pesquisa ao extra (somente se não for personalizado e tiver o [[pesquisa]])
		$extraOriginal = '';
		$arquivos = array(
			"{$g['pathDados']}personalizacaoGrid/" . $r_pesquisaGridId . '-extra',
			"{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . $r_pesquisaGridId . '-extra'
		);

		foreach ($arquivos as $arquivo) {
			if (file_exists($arquivo)) {
				$extraOriginal = file_get_contents($arquivo);
			}
		}

		if ($extraOriginal && strpos($extraOriginal, '[[pesquisa]]') !== false) {
			$extra = str_replace('[[pesquisa]]', $r_pesquisa_item, $extraOriginal);
            $extra = gSubstituirVariaveisParametros($extra);
		} else {
			$extra = preg_replace('/where/i', $where . ' AND ', $extra);
		}

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$extra = str_replace('[[virgula]]', ',', $extra);
		$sql = mysql_query("select {$r_campo2} from $from  $extra");

		if (mysql_num_rows($sql) == 0) {
			$resposta['code'] = false;
			$resposta['erro'] = true;
		} else if (mysql_num_rows($sql) == 1) {
			$row = mysql_fetch_array($sql);
			$resposta['code'] = true;
			$resposta['ids']  = count($chaves);
			$resposta['id1']  = $row[0];
			$resposta['id2']  = $row[1];
			$resposta['id3']  = $row[2];
			$resposta['id4']  = $row[3];
		} else {
			$resposta['code'] = false;
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'content':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/pesquisaGrid/tab/', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&todosCampos=' . urlencode($r_todosCampos))
		);		
		echo gProcessaAcoes($actions);

		$r_T005_Id = !isset($r_T005_Id) ? '' : $r_T005_Id;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est002FormPreencherSeparadosBtn').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est002/grid_func-ajax/est002PreencherSeparados/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
						if (request.code) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		//dialogConfirm('Ok', "Operação realizada com sucesso.", \$buttons);
							divRefresh('{$content1}');
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
					$('#{$g['divIdParent']} .itemForm-{$r_campoPesquisaOrigem}').focus().select();
				});
			} </script></div>
EOT;
		//Habilita botão "incluir produtos" no grid de pesquisa do orçamento e do pedido
		if(($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be' || $r_pesquisaGridId == '37321e60498ae6e8a6c0ce244b6b3389') && $g['C031']['habilitarInclusaoProdutosPesquisaGrid'] == 'S'){
			$pk = unserialize($r_campo9); $pk = $pk[0];
			$tabela = ($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be') ? 'T003' : 'T005';
			$botoesEsq = $botoesMid = '';
			$botoesDir .= '<button id="incluirProdutosPesquisaGrid">Incluir produto em lote</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

			$etapas = ($g['C031']['incluirProdutosPesquisaGridEmEtapas'] == 'S');

			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#incluirProdutosPesquisaGrid').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven001/grid_func-ajax/incluirProdutosPesquisaGrid/?ajax=true&etapas={$etapas}&{$tabela}_Id=' + encodeURIComponent("{$pk}") + '&callback=?', function(request) {
							if (!request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
								$('#{$g['divId']}').hideLoading();
								return;
							}

							console.log('request etapas:', request);
							if (request.data == 'jsonEtapas') {
								var p = typeof request.json === 'string' ? JSON.parse(request.json) : request.json;
								processaEtapasAsync(p.titulo, p.classe, p.metodo, p.sql, 0, p.divId || $('#{$g['divIdParent']}').attr('id') || '')
									.then(function () {
										console.log('then executando depois do processaEtapasAsync');
									})
									.catch(function (err) {
										console.log('erro etapas:', err);
									})
									.finally(function () {
    									console.log('finally etapas');
										divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-last').attr('id'));
										divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-width').attr('id'));
										fecharJanela('{$g['divId']}');
										$('#{$g['divId']}').hideLoading();
									});
								return;
							}

							divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-last').attr('id'));
							divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-width').attr('id'));
							fecharJanela('{$g['divId']}');
							$('#{$g['divId']}').hideLoading();
						});
					});

					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						$.getJSON('/ven/ven001/grid_func-ajax/limparProdutosPesquisaGrid/?ajax=true&{$tabela}_Id=' + encodeURIComponent("{$pk}") + '&callback=?', function(request) {});
					});
				} </script></div>
EOT;
		}
	break;
	
	case 'tab':
		// Definição das Tabs
		$tabs   = array();
		$idGrid = uniqid();
		// campo16 é o paramentro para criar os filtros de pesquisaGrid vindo de produtos (pedido, orçamento, ordem de compra...)
		if($g['C031']['modeloBuscaPesquisaGridProduto'] != '1' && !empty($r_campo16)){
			// Define qual campo sera o preferência do filtro ao digitar um valor
			$campoBuscaFiltro = ($g['C031']['modeloBuscaPesquisaGridProduto'] == '2') ? 'D001_Codigo_Produto' : 'D001_Descricao_Produto';
			// Modelo novo - podendos realizar filtros na busca dos produtos
			$realizaFiltroAutomatico = 'N'; // Essa varivavel vai definir se é filtro obrigatorio ou não
			if (!empty($r_pesquisa)) {
				ob_start();
				$gOriginal = $g;
				$g['r'] = '/sistema/funcoes/gridFiltro/filtrar/';
				$g['r_'] = $g['r'];
				$g['r_'] = preg_replace('/^\//', '', $g['r_']);
				$g['r'] = explode('/', trim($g['r'], '/'));
				$r_filtroUID = uniqid();
				$_REQUEST[$r_filtroUID . '-' . base64_encode($campoBuscaFiltro)] = "{$r_pesquisa}";
				$realizaFiltroAutomatico = 'S';
				$r_grid = $r_pesquisaGridId . "_a6a9d8a5d7b040982435323aa03650e9";
				include('prog/sistema/funcoes/gridFiltro.php');
				$g = $gOriginal;
				ob_end_clean();	 
			}	
			$tabsList[] = array($idGrid, '/sistema/funcoes/pesquisaGrid/grid/', 'Pesquisa', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . '' . '&realizaFiltroAutomatico=' . urlencode($realizaFiltroAutomatico) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&todosCampos=' . urlencode($r_todosCampos), true, false, "corrigeGrid('{$idGrid}');");
		}else{
			// Modelo antigo
			$tabsList[] = array($idGrid, '/sistema/funcoes/pesquisaGrid/grid/', 'Pesquisa', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&todosCampos=' . urlencode($r_todosCampos), true, false, "corrigeGrid('{$idGrid}');");
		}

		//Pesquisa grid inclusão item orçamento ou pedido
		if(($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be' || $r_pesquisaGridId == '37321e60498ae6e8a6c0ce244b6b3389') && $g['C031']['modeloBuscaPesquisaGridProduto'] == '1'){
			$D024_Id = unserialize($r_campo9);
			$D024_Id = $D024_Id[1];

			$sql_tab1=mysql_query("SELECT D001_Id FROM D001 LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$D024_Id}' WHERE D001_Flag_Ativo='S' AND (replace(replace(D001_Codigo_Produto,' ',''),'-','') LIKE replace(replace('$r_pesquisa%',' ',''),'-','') OR replace(replace(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE replace(replace('$r_pesquisa%',' ',''),'-','')) LIMIT 1");
			$sql_tab2=mysql_query("SELECT D001_Id FROM D001 LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$D024_Id}' WHERE D001_Flag_Ativo='S' AND (replace(replace(D001_Codigo_Produto,' ',''),'-','') LIKE replace(replace('%$r_pesquisa%',' ',''),'-','') OR replace(replace(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE replace(replace('%$r_pesquisa%',' ',''),'-','')) LIMIT 1");
			
			// Definição das Tabs
			$tabs = array();
			if (mysql_num_rows($sql_tab1)>0) { 
				$tabsList = array(
					array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid/', 'Pesquisa pelo inicio do código', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas01'), 
					array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid2/', 'Pesquisa por parte do código', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas02'), 
					array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid3/', 'Pesquisa pela descrição', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas03'),
				);		
			} else if (mysql_num_rows($sql_tab2)>0) { 
				$tabsList = array(
					array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid2/', 'Pesquisa por parte do código', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas02'), 
					array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid3/', 'Pesquisa pela descrição', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas03'),
				);		
			} else {

				// Busca exata pela código de barras
				$sql_tab3=mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Barras = '{$r_pesquisa}' LIMIT 1");
				if(mysql_num_rows($sql_tab3)>0){
					$tabsList = array(
						array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid4/', 'Pesquisa pelo EAN', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas04'),
					);
				}else {
					$tabsList = array(
						array(uniqid(), '/sistema/funcoes/pesquisaGrid/grid3/', 'Pesquisa pela descrição', '&pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&where_pesquisa=vendas03'),
					);		
				}
			}
		}

		if($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be' || $r_pesquisaGridId == '37321e60498ae6e8a6c0ce244b6b3389'){
			//Aba tabela fornecedores
			$tabela = ($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be') ? 'T003' : 'T005';
			$tabsList[] = array(uniqid(), '/ven/ven001/grid/ven001PesquisaTabelaFornecedores/', 'Tabelas Fornecedores', '&pesquisaGridId=' . urlencode("e1a81a1501742a3537fb3dab62e078a8") . '&pesquisa=' . urlencode($r_pesquisa) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&tabela=' . urlencode($tabela));
		}

		if(!empty($r_campo13)){
			$idCab = unserialize($r_campo9);
			$idCab = $idCab[0];
			
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, $r_campo13, '&acaoId=' . urlencode($idCab), true),
			);
			echo gProcessaAcoes($actions);
		}

		if(!empty($r_campo14)){
			$tabsList[0][3] = $tabsList[0][3] . '&arrayGridLinha=' . urlencode($r_campo14);
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'grid':
	case 'grid1':
	case 'grid2':
	case 'grid3':
	case 'grid4':
	case 'grid5':
	case 'grid6':
		$pesquisaGridIdBase = $r_pesquisaGridId;
		if ($g['r'][3] != 'grid' && strpos($r_pesquisaGridId, $g['r'][3]) === false) {
			$r_pesquisaGridId .= $g['r'][3];
		}

		// Personalização do GRID: por padrão o ID do grid é a sua propria URL (md5), aqui setamos o 'pesquisaGridId' para utilizar um ID diferente
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('pesquisaGridId', '{$r_pesquisaGridId}');
				//$('#{$g['divId']}').find('tr:eq(1)').addClass('linhaSelecionada');
				if ('{$g['C031']['modeloBuscaPesquisaGridProduto']}' == '1' && isset('{$r_linhaGridId}')) {
					escolherFoco($('#{$g['divId']}').closest('.ui-dialog-content'), true);
				}
			} </script></div>
EOT;
		
		if(!empty($r_arrayGridLinha)){
			$arrayGridLinha = unserialize($r_arrayGridLinha);
			global $arrayGridLinha;
			function gridLinha($array) {
				global $arrayGridLinha;
				foreach ($arrayGridLinha as $gridLinha) {
					if (eval("return ".base64_decode($gridLinha['clausulaIf']).";")) {
					    return $gridLinha['corLinha'];
					}
				}
			}
		}
		/*
		Campos:
		
		pesquisaGridId	// id do pesquisaGrid
		pesquisa	// conteúdo a ser pesquisado (digitado no campo)
		campo1 // tabela
		campo2 // chave primaria
		campo3 // campos
		campo4 // extra
		campo5 // título da janela
		campo6 // descrição dos campos
		campo7 // callback js
		campo8 // divId
		campo9 // (opcional) parametro(s) extra(s) (passado para o callback js)
		campo10 // parametros para cada campo do grid
		campo11 // campos que devem ser incluidos na clausula Where
		campo12
		campo13
		campo14 
		campo15 // Tipo de cláusula where que vai utilizar - aceita '%-%' (padrão), '-%'' e '='   Ex: like '%busca%' - like 'busca%' -  = 'busca'
		campo16 // Parametros para criar os filtros, no momento utilizado apenas para produtos (01/09/2022) (pedido, orçamento, ordem de compra ...)
		todosCampos // campos do formulário
		*/
		// URL para chegar neste grid
		// o único campo que não precisa é o pesquisa ('&pesquisa=' . urlencode($r_pesquisa))
		$g['r_'] = $g['r_'] . '?pesquisaGridId=' . urlencode($r_pesquisaGridId) . '&campo1=' . urlencode($r_campo1) . '&campo2=' . urlencode($r_campo2) . '&campo3=' . urlencode($r_campo3) . '&campo4=' . urlencode($r_campo4) . '&campo5=' . urlencode($r_campo5) . '&campo6=' . urlencode($r_campo6) . '&campo7=' . urlencode($r_campo7) . '&campo8=' . urlencode($r_campo8) . '&campo9=' . urlencode($r_campo9) . '&campo10=' . urlencode($r_campo10) . '&campo11=' . urlencode($r_campo11) . '&campo15=' . urlencode($r_campo15);
		// Processa alguns campos
		$chaves = explode(',', $r_campo2);
		if (strpos($r_campo3, '||') !== false) {
			$campos = explode('||', $r_campo3);
		} else {
			$campos = explode(',', $r_campo3);
		}
		$camposDesc = explode(',', $r_campo6);
		$r_pesquisa = mysql_real_escape_string($r_pesquisa);
		$r_campo9 = unserialize($r_campo9);
		$r_campo10 = unserialize($r_campo10);
		
		$campos11 = explode('||', $r_campo11);
        $todosCampos = $r_todosCampos ? json_decode($r_todosCampos) : '';
        if ($todosCampos) {
            foreach ($todosCampos as $key => $value) {
                $r_campo4 = str_replace("[[{$key}]]", $value, $r_campo4);
            }
        }

		// Parametros extras (CAMPO 9) podem ser necessario nas personalizacoes, por isso vamos transformar eles em váriaveis aqui - exemplo: [[r_extra1]], [[r_extra2]], ...
		foreach ($r_campo9 as $key => $valor) {
			$var = 'r_extra' . ($key + 1);
			$$var = $valor;
		}

		/**
		* Definição do Grid (Tabela)
		*/
		require_once('bibliotecas/classes/FRAMEWORK.php');
		$FRAMEWORK = new FRAMEWORK();
		list($where, $grid, $explode) = $FRAMEWORK->montaPesquisaGrid($r_pesquisa, $chaves);
		foreach ($campos as $id => $campo) {
			$campo = trim($campo);
			$campo = str_replace('[[virgula]]', ',', $campo);
			$arrayTmp = array('colunaValor' => $campo, 'colunaTitulo' => $camposDesc[$id]);
            // campo já existe como chave primária, vamos remover ele e colocar o novo
            if (in_array($campo, $chaves)) {
                $arrayTmp['chavePrimaria'] = true;
            } 
			if (isset($r_campo10[$id])) {
				$arrayTmp = array_merge($arrayTmp, $r_campo10[$id]);
			}
            // campo já existe como chave primária, vamos remover ele e colocar o novo
            if (in_array($campo, $chaves)) {
                foreach ($grid as $key => $value) {
                    if ($value['colunaValor'] == $campo) {
                    	unset($grid[$key]);
                    }
                }
            }
			$grid[] = $arrayTmp;
			// colunas com ' as ' atualmente não entram no WHERE
			if (strpos($campo, ' as ') !== false) { continue; }
			if (in_array($campo, $campos11)) { continue; }
			if (!isset($r_pegarExtra) && ($g['C031']['modeloBuscaPesquisaGridProduto'] == '1' || empty($r_campo16))) {
				foreach ($explode as $r_pesquisa_key => $r_pesquisa_item) {
					$logica = ($r_pesquisa_key == 0) ? 'OR (' : 'AND';
					$r_pesquisa_item = str_replace(" ", "%", $r_pesquisa_item);
					if($r_campo15 == '-%'){
						$where .= (empty($where)) ? "WHERE (({$campo} LIKE '{$r_pesquisa_item}%'" : " {$logica} {$campo} LIKE '{$r_pesquisa_item}%'";	
					} else if($r_campo15 == '='){
						$where .= (empty($where)) ? "WHERE (({$campo} = '{$r_pesquisa_item}'" : " {$logica} {$campo} = '{$r_pesquisa_item}'";	
					} else {
						$where .= (empty($where)) ? "WHERE (({$campo} LIKE '%{$r_pesquisa_item}%'" : " {$logica} {$campo} LIKE '%{$r_pesquisa_item}%'";
					}
				}								
				$where .= ')';
			}
		}
		$grid = array_values($grid);
		if (!empty($where)) {
			$where .= ')';
		} else {
			$where .= 'WHERE (1=1)';
		}

		if(!empty($r_where_pesquisa)){
			// Só entra aqui as pesquisas em orçamento e pedido
			require_once('bibliotecas/classes/VEN001.php');
			$VEN001 = new VEN001();

			$D024_Id = unserialize($r_campo9);
			$D024_Id = $D024_Id[1];

			$case = $VEN001->triggerWherePesquisaGrid($r_pesquisa_item);

			switch ($r_where_pesquisa) {
				case 'vendas01':
					$where = ($case['vendas01'] != '') ? $case['vendas01'] : "WHERE (REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('{$r_pesquisa_item}%',' ',''),'-','') OR REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE REPLACE(REPLACE('{$r_pesquisa_item}%',' ',''),'-',''))";
				break;
				case 'vendas02':
					$where = ($case['vendas02'] != '') ? $case['vendas02'] : "WHERE ((REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('%{$r_pesquisa_item}%',' ',''),'-','') AND REPLACE(REPLACE(D001_Codigo_Produto,' ',''),'-','') NOT LIKE REPLACE(REPLACE('{$r_pesquisa_item}%',' ',''),'-','')) OR (REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') LIKE REPLACE(REPLACE('%{$r_pesquisa_item}%',' ',''),'-','') AND REPLACE(REPLACE(D083_Codigo_Produto_Fornecedor,' ',''),'-','') NOT LIKE REPLACE(REPLACE('{$r_pesquisa_item}%',' ',''),'-','')))";
				break;
				case 'vendas03':
					$where = ($case['vendas03'] != '') ? $case['vendas03'] : "WHERE (REPLACE(REPLACE(D001_Descricao_Produto,' ',''),'-','') LIKE REPLACE(REPLACE('%{$r_pesquisa_item}%',' ','%'),'-','') OR REPLACE(REPLACE(D001_Descricao_Ingles,' ',''),'-','') LIKE REPLACE(REPLACE('%{$r_pesquisa_item}%',' ','%'),'-','') OR REPLACE(REPLACE(D082_Marca,' ',''),'-','') LIKE REPLACE(REPLACE('%{$r_pesquisa_item}%',' ','%'),'-',''))";
				break;
				case 'vendas04':
					$buscaEAN = 'S';
					$where = ($case['vendas04'] != '') ? $case['vendas04'] : "WHERE D001_Codigo_Barras = '{$r_pesquisa_item}'";
				break;
			}
		}
		// Tabela
		$from = $r_campo1;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		if (count($chaves) == 1) {
			$ids = "var id = \$(this).closest('tr').attr('id').replace('{$chaves[0]}-', ''); {$r_campo7}('{$r_campo8}', id";
		} else if (count($chaves) == 2) {
			$ids = <<<EOT
				var id = $(this).closest('tr').attr('id');
				var id = id.split('|');
				var id1 = id[0].replace('{$chaves[0]}-', '');
				var id2 = id[1].replace('{$chaves[1]}-', '');
				{$r_campo7}('{$r_campo8}', id1, id2
EOT;
		} else if (count($chaves) == 3) {
			$ids = <<<EOT
				var id = $(this).closest('tr').attr('id');
				var id = id.split('|');
				var id1 = id[0].replace('{$chaves[0]}-', '');
				var id2 = id[1].replace('{$chaves[1]}-', '');
				var id3 = id[2].replace('{$chaves[2]}-', '');
				{$r_campo7}('{$r_campo8}', id1, id2, id3
EOT;
		} else if (count($chaves) == 4) {
			$ids = <<<EOT
				var id = $(this).closest('tr').attr('id');
				var id = id.split('|');
				var id1 = id[0].replace('{$chaves[0]}-', '');
				var id2 = id[1].replace('{$chaves[1]}-', '');
				var id3 = id[2].replace('{$chaves[2]}-', '');
				var id4 = id[3].replace('{$chaves[3]}-', '');
				{$r_campo7}('{$r_campo8}', id1, id2, id3, id4
EOT;
		} else {
			$ids = "alert('Alterar arquivo pesquisaGrid.php para suportar mais de 4 chaves primárias.');";
		}
		// atualmente só suportado 3 chaves primárias ($ids vira alert, linha acima)
		if (count($chaves) < 5) {
			if (is_array($r_campo9) && count($r_campo9) > 0) {
				foreach($r_campo9 as $valueParam) {
					$ids .= ", '{$valueParam}'";
				}
			}
			$ids .= ");";
		}

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				{$ids}
				fecharJanela('{$g['divId']}');
			});
			// Torna o grid um um grid tipo 'select'
		    //$('#{$g['divId']} .grid').addClass('grid-select');
		    // Atribui a classe de identificação
		    $('#{$g['divId']} .grid').addClass('pesquisaGrid');
		    // Torna tipo 'select'
		    $('#{$g['divId']} .grid').addClass('grid-select');
		} </script></div>
EOT;

		//Editar a quantidade de produtos a serem incluidos no orçamento/pedido
		if(strpos($r_pesquisaGridId, 'a7c1eeb35df881952044a70d6af1c4be') !== false || strpos($r_pesquisaGridId, '37321e60498ae6e8a6c0ce244b6b3389') !== false){
			$D024_Id = $r_campo9[1];
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					function colunaEditarCallbackJS_T198_Quantidade(pks, campoEditado, valorAntigo, novoValor) {
						pks = pks.split('|');
						var D009_Id = pks[0].replace(/D009_Id-/, '');
						var D001_Id = pks[3] ? pks[3].replace(/D001_Id-/, '') : '';
						$.getJSON('/ven/ven001/grid_func-ajax/gravarT198/?ajax=true&D009_Id='    + encodeURIComponent(D009_Id) +
																				  '&D024_Id='    + encodeURIComponent('{$D024_Id}') +
																				  '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				  '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				  '&novoValor='			+ encodeURIComponent(novoValor) +
																				  '&callback=?', function(request) {
							divRefresh('{$g['divId']}', true, false, false, D001_Id, 'D001_Id'); // grid
							if (request.refresh) {
								divRefresh('{$g['divId']}'); // grid
							}
							if (!request.code) {
								dialogConfirm('Erro', request.data);
							}
						});
					}

					function colunaEditarCallbackJS_T266_Quantidade(pks, campoEditado, valorAntigo, novoValor) {
						pks = pks.split('|');
						var D009_Id = pks[0].replace(/D009_Id-/, '');
						var D001_Id = pks[2] ? pks[2].replace(/D001_Id-/, '') : '';
						$.getJSON('/ven/ven002/grid_func-ajax/gravarT266/?ajax=true&D009_Id='    + encodeURIComponent(D009_Id) +
																				  '&D024_Id='    + encodeURIComponent('{$D024_Id}') +
																				  '&campoEditado='		+ encodeURIComponent(campoEditado) +
																				  '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																				  '&novoValor='			+ encodeURIComponent(novoValor) +
																				  '&callback=?', function(request) {
							divRefresh('{$g['divId']}', true, false, false, D001_Id, 'D001_Id'); // grid
							if (request.refresh) {
								divRefresh('{$g['divId']}'); // grid
							}
							if (!request.code) {
								dialogConfirm('Erro', request.data);
							}
						});
					}
				} </script></div>
EOT;
		}

		global $filtroObrigatorio;
		$filtroObrigatorio = false;
		// Filtro obrigatorio para grid produtos (algo específico para a empresa Ceigon - não existe personalização do pesquisaGrid hoje)
		if ($r_pesquisaGridId == 'a7c1eeb35df881952044a70d6af1c4be' && $confUsuario['dbDatabase'] == 'e237' && $_SERVER['SERVER_ADDR'] != '192.168.38.45') {
			//$r_gridFiltrado = true;
			$filtroObrigatorio = true;
		} else if($g['C031']['modeloBuscaPesquisaGridProduto'] != '1' && $r_realizaFiltroAutomatico == 'N'){
			$filtroObrigatorio = true;
		}

		$r_pesquisaGridId = $pesquisaGridIdBase."_".md5($g['r'][0]."/".$g['r'][1]."/".$g['r'][2]."/".$g['r'][3]."/");


		if($g['C031']['modeloBuscaPesquisaGridProduto'] != '1' && !empty($r_campo16)){	
			// Filtros
			$gridFiltro = array();
			$filtros = explode('||',$r_campo16);
			foreach ($filtros as $item) {
				$dadosFiltro = explode(',', $item);
				$gridFiltro[$dadosFiltro[0]] = array('titulo' => $dadosFiltro[1], 'tipo' => $dadosFiltro[2]);
			}

		} else {
			// Gera o filtro e acrescenta ao $extra
			$gridFiltro = array();
		}
		
		$filtroGlobal = false;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $r_campo4, $r_pesquisaGridId, false, false, true, false, false, false, false, $filtroGlobal, true);
		$exibirFiltro = false;
		foreach ($gridFiltro as $item) {
			if (!isset($item['exibir']) || (isset($item['exibir']) && $item['exibir'])) {
				$exibirFiltro = true;
			}
		}
		if ($exibirFiltro) {
			echo $g['ui']->gridFiltroPrint($gridFiltro, $r_pesquisaGridId, ($r_linhaGridId) ? false : true);
		}

		// Adiciona a pesquisa ao extra (somente se não for personalizado e tiver o [[pesquisa]])
		$extraOriginal = '';
		$arquivos = array(
			"{$g['pathDados']}personalizacaoGrid/" . $r_pesquisaGridId . '-extra',
			"{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . $r_pesquisaGridId . '-extra'
		);
		foreach ($arquivos as $arquivo) {
			if (file_exists($arquivo)) {
				$extraOriginal = file_get_contents($arquivo);
			}
		}
		if ($extraOriginal && strpos($extraOriginal, '[[pesquisa]]') !== false) {
			$extra = str_replace('[[pesquisa]]', $r_pesquisa_item, $extraOriginal);
            $extra = gSubstituirVariaveisParametros($extra);
		} else {
			$extra = preg_replace('/where/i', $where . ' AND ', $extra);
		}

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		$extra = str_replace('[[virgula]]', ',', $extra);
    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 300, $r_pesquisaGridId);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados, $r_pesquisaGridId);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', $r_pesquisaGridId);
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId'], $r_pesquisaGridId), JSON_FORCE_OBJECT));
		$g['smarty']->assign('gridTable', $from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		// Pode ter um loading vindo da mudança de tabs automatica no pesquisagrid, garantimos que o loading é tirado depois de carregado o grid
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']}").hideLoading();
			} </script></div>
EOT;
		if(strpos($r_pesquisaGridId, 'd296572027558e68c1eb9879e0825f5c') !== false || strpos($r_pesquisaGridId, 'a6a9d8a5d7b040982435323aa03650e9') !== false || $buscaEAN == 'S'){
			$T007_Flag_PDV = $r_campo9[2];
			if($T007_Flag_PDV == 'S' || $buscaEAN == 'S'){
				//No PDV ou pela busca pelo EAN se houver apenas um registro de produto retornado, selecionamos automaticamente este produto
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						if($('#{$g['divId']} .grid tbody tr').size() == '1'){
							$('#{$g['divId']} .grid tbody tr:first').trigger('click');
						}
					} </script></div>
EOT;
			}
		}

		if($g['C031']['destaqueTermosPesquisaGrid'] == 'S'){
			// Destaca em amerelo o texto pesquisado
			
			$pesquisaItem = str_replace("%", " ", $r_pesquisa_item);
			$termos 	  = array_filter(explode(' ', trim($pesquisaItem))); 
			$termosJSON   = json_encode($termos, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT); 	

			echo <<<EOT
			<script type="text/javascript">
			(function() {	
				let termos = JSON.parse('$termosJSON');	
				if (!Array.isArray(termos) || termos.length === 0) return;
			
				document.querySelectorAll(".pesquisaGrid tbody td").forEach(td => {
					let walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
					let nodes = [];
			
					while (walker.nextNode()) {
						if (walker.currentNode.nodeValue.trim() !== "") {
							nodes.push(walker.currentNode);
						}
					}
			
					nodes.forEach(node => {
						let originalText = node.nodeValue;
						let modified = false;
						let fragment = document.createDocumentFragment();
						let lastIndex = 0;
			
						termos.forEach(termo => {
							if (!termo.trim()) return;
							let regex = new RegExp(termo.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&'), "gi");
							let match;
			
							while ((match = regex.exec(originalText)) !== null) {
								fragment.appendChild(document.createTextNode(originalText.substring(lastIndex, match.index)));
			
								let mark = document.createElement("mark");
								mark.style.background = "yellow";
								mark.style.cursor = "pointer";
								mark.classList.add("highlighted");
								mark.textContent = match[0];
			
								fragment.appendChild(mark);
								lastIndex = regex.lastIndex;
								modified = true;
							}
						});
			
						fragment.appendChild(document.createTextNode(originalText.substring(lastIndex)));
			
						if (modified) {
							node.parentNode.replaceChild(fragment, node);
						}
					});
				});
			
				// Mantém o clique na linha mesmo nos textos destacados
				document.querySelectorAll(".pesquisaGrid tbody tr").forEach(tr => {
					tr.addEventListener("click", function(event) {
						if (event.target.tagName === "MARK") {
							event.target.closest("tr").dispatchEvent(new Event('click', { bubbles: true }));
						}
					});
				});
			})();
			</script>
EOT; 
		}
		
	break;
}
?>
