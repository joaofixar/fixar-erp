<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'est017TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est017/content/est017ContentPCP/', 'PCP'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabPCP':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est017/grid/est017GridAnaliseCusto/', 'Análise de Custo'),
			array(uniqid(), '/est/est017/grid/est017GridProducaoEmergencia/', 'Produção Emergência'),
			array(uniqid(), '/est/est017/content/est017ContentOrdensProducao/', 'Ordem Produção'),
			array(uniqid(), '/est/est017/content/est017ContentProducoes/', 'Produção'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabOrdemProducao':
		if($r_acaoId > 0){
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 35px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/est/est017/outro/est017CabecalhoOrdemProducao/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if($r_acaoId > 0){
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			$tabsList[] = array(uniqid(), '/est/est017/grid/est017GridProdutosOrdemProducao/', 'Produtos', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/est/est017/form/est017FormCabecalhoOrdemProducao/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/est/est017/grid/est017GridMateriais/', 'Matéria Prima / Consumo', '&tab=geral&T255_Id=' . urlencode($r_acaoId));
		} else {
			$tabsList[] = array(uniqid(), '/est/est017/form/est017FormCabecalhoOrdemProducao/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabEntradaProducao':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 34px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est017/outro/est017CabecalhoEntradaProducao/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est017/content/est017ContentProdutoProducao/', 'Produto', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est017/content/est017ContentEntradaProduto/', 'Produção', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est017/grid/est017GridComposicaoProduto/', 'Composição', '&T256_Id=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est017/grid/est017GridProcessosProduto/', 'Processos', '&T256_Id=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabProcessarProducao':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est017/grid/est017GridProcessarProducao/', 'Entradas', '&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabConsumiveis':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est017/grid/est017GridConsumiveis/', 'Consumiveis', '&T262_Id=' . urlencode($r_T262_Id)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs,true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabOrdensProducao':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est017/grid/est017GridOrdemProducaoAberto/', 'Aberto', ''),
			array(uniqid(), '/est/est017/grid/est017GridOrdemProducaoFinalizado/', 'Finalizado', ''),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabProducoes':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est017/content/est017ContentProducaoAberto/', 'Aberto', ''),
			array(uniqid(), '/est/est017/content/est017ContentProducaoFinalizado/', 'Finalizado', ''),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabProducaoAberto':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est017/grid/est017GridProducaoProdutosAberto/', 'Produtos', ''),
			array(uniqid(), '/est/est017/grid/est017GridProducaoComposicaoAberto/', 'Composição', ''),
			array(uniqid(), '/est/est017/grid/est017GridProducaoProcessosAberto/', 'Processos', ''),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est017TabProducaoFinalizado':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est017/grid/est017GridProducaoProdutosFinalizado/', 'Produtos', ''),
			array(uniqid(), '/est/est017/grid/est017GridProducaoComposicaoFinalizado/', 'Composição', ''),
			array(uniqid(), '/est/est017/grid/est017GridProducaoProcessosFinalizado/', 'Processos', ''),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
