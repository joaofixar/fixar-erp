<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'tabGrid':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs 
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/lista/', 'Produtos'),
			array(uniqid(), '/cad/cad002/content/cad002ContentPreco/', 'Preços'),
			//array(uniqid(), '/cad/cad002/grid/cad002GridCustos/', 'Custos'),
			//array(uniqid(), '/cad/cad002/grid/cad002GridOutros/', 'Outros'),
			array(uniqid(), '/cad/cad002/content/cad002ContentListaFornecedor/', 'Tabelas'),
			array(uniqid(), '/cad/cad007/grid/cad007grid01/', 'Documentos'),
			array(uniqid(), '/cad/cad002/grid/cad002gridInativos/', 'Inativos'),
			//array(uniqid(), '/cad/cad002/grid/cad002gridCodigosDuplicados/', 'Duplicados'),
            array(uniqid(), '/cad/cad002/grid/cad002GridHistoricoGeral/', 'Histórico'),
            array(uniqid(), '/cad/cad002/grid/cad002GridSimilaresGeral/', 'Similares'),
            //array(uniqid(), '/cad/cad002/grid/cad002GridValidade/', 'Validade'),
			array(uniqid(), '/cad/cad002/content/cad002ImportarCSV/','Importar CSV'),
			array(uniqid(), '/cad/cad002/content/cad002ReferenciasProdutos/','Referências'),
			array(uniqid(), '/cad/cad002/content/cad002ContentCadastroFornecedoresProdutos/','Fornecedores'),
			array(uniqid(), '/cad/cad002/grid/cad002GridPertenceEmpresa/', 'Pertence à empresa'),
		);

		if($g['c029Ids'][275]){
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentAtivosImobilizados/','Ativos Imobilizado (CIAP)');
		}

		if ($g["C031"]["plataformaECommerce"] == "tray" || $g["C031"]["plataformaECommerce2"] == "tray") {
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridECommerceTray/','E-Commerce Tray');
		}
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabForm1':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/form1/', 'Novo Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),

		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
	
	case 'tabFormXML':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/formXML/', 'Gerar XML', "&tab=geral&acaoId=" . urlencode($r_acaoId)),

		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;	
	
	case 'tabForm2':
		global $g;
		if(!empty($r_acaoId)){
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		//array(uniqid(), '/cad/cad002/content/detalhes/', 'Marcas', "&acaoId=" . urlencode($r_acaoId)),
		//array(uniqid(), '/cad/cad002/form/form2/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),			
		$sql = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_D001_Id = D001_Id LEFT JOIN D009 ON D009_D049_Id = D049_Id LEFT JOIN D001A ON D001A_D001_Id = D001_Id WHERE D001_Id = '{$r_acaoId}' AND D009_C004_Id = '{$g['empresaAtual']}'");		
		$res = mysql_fetch_array($sql);
		$idTabela = uniqid();
		if($g['C031']['multimarcas'] == 'S'){
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProduto/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));			
			$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
		} else if($g['C031']['multimarcas'] == 'N'){
			$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProdutoComum/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));		
			if(!empty($r_acaoId)){
				$tabsList[] = array($idTabela, '/cad/cad002/content/tabela/', 'Tabelas', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&tabela=T001&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002FIFO/', 'FIFO', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($res['D009_Id']) . "&D001_Id=" . urlencode($res['D001_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/orcamentosProduto/', 'Orçamentos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/pedidosProduto/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridCotacoesNovo/', 'Cotações', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridOrdemCompraNovo/', 'OC', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);	
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridOrdemProducao/', 'OP', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);	
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridFornecedoresProduto/', 'Fornecedores', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true);	
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridSimilares/', 'Similares', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridRegraConversao', 'Conversão de Unidade', "&tab=geral&acaoId=" . urlencode($res['D001_Id']), false, true); // confirmar	
				if ($g["C031"]["plataformaECommerce"] == "nuvemshop" || $g["C031"]["plataformaECommerce2"] == "nuvemshop") {
					$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentNuvemShop/', 'Nuvemshop', "&D001A_Id=" . urlencode($res['D001A_Id']) . "&D001_Id=" . urlencode($res['D001_Id']));
				}
			}
		} else {
			if($res['D001_Flag_Multimarcas'] != 'N') {
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProduto/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));			
				$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
			} else {
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentCabecalhoProdutoComum/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divIdMain=' . urldecode($r_divIdMain));		
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&tabela=T001&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array($idTabela, '/cad/cad002/content/tabela/', 'Tabelas', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($res['D009_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D024_Id=' . urlencode($r_D024_Id));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentUploadFotoProduto/', 'Fotos', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/cad002FormEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId));
				$tabsList[] = array(uniqid(), '/etc/etc001/grid/etc001_grid_urls/', 'Links', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentServicos/', 'Serviços', "&D001_Id=" . urlencode($r_acaoId));
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/empresas/', 'Referências', "&tab=geral&D001_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($res['D009_Id']) . "&D009_Id=" . urlencode($res['D009_Id']) . "&D001_Id=" . urlencode($res['D001_Id']) );
				//$tabsList[] = array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($res['D049_Id']));
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/orcamentosProduto/', 'Orçamentos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/pedidosProduto/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridCotacoesNovo/', 'Cotações', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
				$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridOrdemCompraNovo/', 'OC', "&tab=geral&acaoId=" . urlencode($res['D009_Id']), false, true);
			}
		}
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Quando vier do botão (Incluir Contatos, diretamente no combo)
		if (isset($r_abrirTab) && $r_abrirTab == 'tabela') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$idTabela}');
					}, 100);
				} </script></div>
EOT;
		}
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
		
	case 'tabProdutoFornecedorCompras':
		global $g;
		if(!empty($r_acaoId)){
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		//array(uniqid(), '/cad/cad002/content/detalhes/', 'Marcas', "&acaoId=" . urlencode($r_acaoId)),
		//array(uniqid(), '/cad/cad002/form/form2/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),			
		$sql = mysql_query("SELECT * FROM D001 LEFT JOIN D049 ON D049_D001_Id = D001_Id LEFT JOIN D009 ON D009_D049_Id = D049_Id WHERE D001_Id = '{$r_acaoId}' AND D009_C004_Id = '{$g['empresaAtual']}'");		
		$res = mysql_fetch_array($sql);
		$idTabela = uniqid();
		$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridFornecedoresProduto/', 'Fornecedores', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true);	

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Quando vier do botão (Incluir Contatos, diretamente no combo)
		if (isset($r_abrirTab) && $r_abrirTab == 'tabela') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$idTabela}');
					}, 100);
				} </script></div>
EOT;
		}
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabForm3':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/grid/comercial/', 'Comercial', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4']) .  "&divReload=" . urlencode($r_divReload)),
			array(uniqid(), '/cad/cad002/grid/estoque/', 'Estoque', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4'])),
			array(uniqid(), '/cad/cad002/grid/custos/', 'Custos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&idTabForm4=' . urlencode($_REQUEST['idTabForm4'])),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
		
	case 'tabForm4':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad002/form/editarMarca/', 'Editar Marca', "&tab=geral&acaoId=" . urlencode($r_acaoId) . "&divReload=" . urlencode($r_divReload)),
			//array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad002/grid/empresas/', 'Empresas', "&tab=geral&acaoId=" . urlencode($r_acaoId). '&D001_Id=' . urlencode($r_D001_Id)),
			//array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad002/grid/pedidos/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad002/grid/oc/', 'OC', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabEditarMarca':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_D001_Id) . '&D049_Id=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/editarMarca/', 'Editar', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
			array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/grid/cad002GridListaFornecedorProduto/', 'Tabelas', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/content/cad002ContentMontagem/', 'Montagem', "&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/grid/locacao/', 'Locação', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/form/tributos/', 'Tributos', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/grid/pedidos/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			//array(uniqid(), '/cad/cad002/grid/oc/', 'OC', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/grid/cad002GridOrdemCompraNovo/', 'OC', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			//array(uniqid(), '/cad/cad002/grid/cad002GridCotacoes/', 'Cotações', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			array(uniqid(), '/cad/cad002/grid/cad002GridCotacoesNovo/', 'Cotações', "&tab=geral&acaoId=" . urlencode($r_D009_Id)),
			
			//array(uniqid(), '/cad/cad002/form/substituicao/', 'Substituição', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad002/grid/empresas/', 'Empresas', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D001_Id=' . urlencode($r_D001_Id)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'tabForm5CadastroMarca':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cadastroMarca/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($r_divReload))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabEcommerce':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormEcommerce/', 'Produto', "&tab=produto&D001_Id=" . urlencode($_REQUEST['acaoId']) . "&D049_Id=" . urlencode($r_D049_Id) . "&divReload=" . urlencode($r_divReload) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad002/form/cad002FormEcommerce/', 'Estoque', "&tab=estoque&D001_Id=" . urlencode($_REQUEST['acaoId']) . "&D049_Id=" . urlencode($r_D049_Id) . "&divReload=" . urlencode($r_divReload) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad002/form/cad002FormEcommerce/', 'Valores', "&tab=valores&D001_Id=" . urlencode($_REQUEST['acaoId']) . "&D049_Id=" . urlencode($r_D049_Id) . "&divReload=" . urlencode($r_divReload) . '&formId=' . urlencode($formId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs,true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabCadastroEmpresa':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs

		$tabs = array();
		if($r_D001_Id > 0){
			$tabsList = array(
				array(uniqid(), '/cad/cad002/form/cad002FormCadastroEmpresa/', 'Cadastro', "&tab=geral&D001_Id=" . urlencode($r_D001_Id) . '&acaoId=' . urlencode($r_acaoId))
			);
		} else {
			$tabsList = array(
				array(uniqid(), '/cad/cad002/form/cad002FormCadastroReferencia/', 'Cadastro', "&tab=geral&D001_Id=" . urlencode($r_D001_Id) . '&acaoId=' . urlencode($r_acaoId))
			);
		}
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabCadastroLocacao':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormCadastroLocacao/', 'Locação', "&tab=geral&D009_Id=" . urlencode($r_D009_Id))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabCadastroLocacaoEditar':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormCadastroLocacao/', 'Locação', "&tab=geral&D009_Id=" . urlencode($r_D009_Id) . "&acaoId=". urlencode($r_acaoId))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabEidtarHistorico':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormEditarHistorico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D009_Id=' . urlencode($r_D009_Id))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabPreCadastro':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormPreCadastro/', 'Pré-Cadastro', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T189_Descricao_Produto='. urlencode($r_T189_Descricao_Produto)),

		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
	
	 case 'cad002TabJuntarProdutos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/form/cad002FormJuntarProdutos/', 'Juntar Produtos', '&tab=geral'),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002TabTrocarCodigos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/form/cad002FormTrocarCodigos/', 'Trocar Códigos', '&tab=geral'),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'tabGridComercial':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/comercial/', 'Sub-itens', '&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'tabHistoricoPrecos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridHistoricoPrecos/', 'Histórico Preços', '&D009_Id=' . urlencode($r_D009_Id)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad002tabRetonarPrecos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridRetornarPrecos/', 'Retornar', '&extraSql=' . urlencode($r_extraSql)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002tabRetonarPrecosPromocao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridRetornarPrecosPromocao/', 'Retornar', '&extraSql=' . urlencode($r_extraSql)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabExportarXmlProdutos':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridExportarXmlProdutos/', 'Produtos'),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabJuntar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad002/form/cad002FormJuntar/', 'Juntar Produtos', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	case 'cad002TabListaFornecedor':
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/form/cad002FormListaFornecedor/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabListaFornecedorAlterar':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoListaFornecedor/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
	
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridListaFornecedorProdutos/', 'Produtos', '&tab=geral&D070_Id=' . urlencode($acaoId) . '&acaoId=' . urlencode($acaoId)),
			//array(uniqid(), '/cad/cad002/content/cad002ContentListaFornecedorAlterarProduto2rows/', 'Produtos', '&tab=geral&acaoId=' . urlencode($acaoId)),
			//array(uniqid(),'/cad/cad002/grid/cad002GridListaFornecedorProdutos/', 'Produtos', '&D070_Id=' . urlencode($r_acaoId) . '&divReload='),
			array(uniqid(), '/cad/cad002/form/cad002FormListaFornecedor/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabAssociarProduto':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/cad/cad002/form/cad002FormAssociarProduto/', 'Item', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabDocumentosProduto':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/content/certificados/', 'Documentos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabEstoqueExterno':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_D001_Id), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridEstoqueExterno/', 'Estoque', "&tab=geral&acaoId=" . urlencode($r_D001_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabTabelaFornecedor':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridListaFornecedor/', 'Tabelas'),
			array(uniqid(), '/cad/cad002/grid/cad002GridProdutosListaFornecedor/', 'Produtos'),			
		);
		if($g['c029Ids'][274]){
			// Adicionar uma parte no menu dessa importação
			$tabsList[] = array(uniqid(), '/cad/cad002/outro/atualizarTabelaGeral/', 'Importar CSV');
			$tabsList[] = array(uniqid(), '/cad/cad002/grid/cad002GridAtualizarTabelaGeralFornecedor/', 'Produtos Tabelas Fornecedor');
		}
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabItensMontagem':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/cad/cad002/grid/cad002GridItensMontagem/', 'Itens', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabEstoqueSimilares':

		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoProduto/', '&acaoId=' . urlencode($r_D001_Id), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridEstoqueGrade/', 'Grade', "&tab=geral&D001_Id=" . urlencode($r_D001_Id) . '&T003_Id=' . urlencode($T003_Id) . '&T005_Id=' . urlencode($T005_Id)),
			array(uniqid(), '/cad/cad002/grid/cad002GridEstoqueSimilares/', 'Similares', "&tab=geral&D001_Id=" . urlencode($r_D001_Id) . '&T003_Id=' . urlencode($T003_Id) . '&T005_Id=' . urlencode($T005_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabProdutosHistoricoMontagem':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoMontagem/', '&T001_Id=' . urlencode($r_T001_Id), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/cad/cad002/grid/cad002GridProdutosHistoricoMontagem/', 'Itens', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabIncluirSimilar':

		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$acao_Id = isset($r_acaoId) ? $r_acaoId : false;
		
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad002/outro/cad002CabecalhoSimilar/', '&D001_Id=' . urlencode($D001_Id), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/cad/cad002/form/cad002FormIncluirSimilar/', 'Similares', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D001_Id=' . urlencode($D001_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabJuntarLocacao':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/form/cad002FormJuntarLocacao/', 'Juntar Locação', "&tab=geral&D009_Id=" . urlencode($r_D009_Id))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabTabelaFornVenda':
		// Form (caso seja o mesmo form em multiplas tabs)
		// $formId = uniqid();
		// $g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad002/grid/cad002GridListaFornecedorProdutoVenda/', 'Tabelas', "&tab=geral&acaoId=" . urlencode($r_acaoId))
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabCadastroFornecedoresProdutos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridCadastroFornecedoresProdutos/', 'Fornecedores', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//Importar CSV
			array(uniqid(), '/cad/cad002/content/cad002ImportarCSVFornecedorProduto/', 'Importar CSV'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabAtivosImobilizados':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridAtivosImobilizados/', 'Ativos Imobilizados', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002tabGridPreco':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs 
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/cad002GridPrecos/', 'Preços'),
			array(uniqid(), '/cad/cad002/grid/cad002GridHistoricoPrecosGeral/', 'Histórico', '&D009_Id=' . urlencode($r_D009_Id)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabConversaoUnidade':

		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$acao_Id = isset($r_acaoId) ? $r_acaoId : false;
		
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/cad/cad002/form/cad002FormIncluirRegradeConversao/', 'Regra de Conversão', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D001_Id=' . urlencode($D001_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad002TabECommerceTray':
	
		// Definição das Tabs 
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/form/cad002FormECommerceTray/', 'Cadastro', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad002/grid/cad002GridAtributosECommerceTray/', 'Atributos', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad002/form/cad002FormImagemTray/', 'Imagens', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabValidade':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs 
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/lista/', 'Produtos'),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabDocumentoValidade':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs 
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad007/grid/cad007grid01/', 'Documentos'),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
