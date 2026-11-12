<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: est015ContentPrincipal
	 */
	case 'est015TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridPrincipalOrcamentos/', 'Cotação de compra'),
			array(uniqid(), '/est/est015/grid/est015GridPrincipalOrcamentosCancelados/', 'Cancelados'),
            array(uniqid(), '/est/est015/grid/est015GridPrincipalProdutos/', 'Produtos'),
			array(uniqid(), '/est/est015/grid/est015GridPrincipalProdutosVsFornecedor/', 'Prod. x Forn.'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
		
	/**
	 * Grid Principal
	 * Chamado em: est015ContentOrcamento
	 */
	case 'est015TabOrcamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$r_GerarCotacaoEstoque = (isset($r_GerarCotacaoEstoque)) ? $r_GerarCotacaoEstoque : false;
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/form/est015FormOrcamento/', 'Cotação de Compra', '&tab=orcamentos&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($T003_Id) . '&T139_Id=' . urlencode(@$r_T139_Id) . '&D024_Id_CRM='.urlencode(@$r_D024_Id_CRM) . '&tabCatalogoCRM='.urlencode($r_tabCatalogoCRM) . '&GerarCotacaoEstoque=' . urlencode($r_GerarCotacaoEstoque) . '&extra=' . urlencode($r_extra) . '&GerarCotacaoEmergencia=' . urlencode($r_GerarCotacaoEmergencia)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	/**
	 * Form Principal - Editar Cotação de Compras
	 * Chamado em: est015ContentEditarOrcamento
	 */
	case 'est015TabEditarOrcamento':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est015/outro/est015CabecalhoOrcamento/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
	
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tab4 = uniqid();
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est015/content/est015ContentProdutos/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($tab4)),			
			array(uniqid(), '/est/est015/form/est015FormOrcamento/', 'Cabeçalho', '&tab=orcamentos&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($r_divIdMain)),
			array($tab4, '/est/est015/content/est015contentTotais/', 'Totais', "&acaoId=" . urlencode($r_acaoId), false, true),
			array(uniqid(), '/est/est015/content/est015contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_acaoId),false,true),
			array(uniqid(), '/est/est015/content/est015ContentDocumentos/', 'Documentos', "&acaoId=" . urlencode($r_acaoId) . "&T218_Id=" . urlencode($r_acaoId),false,true),
			array(uniqid(), '/est/est015/content/est015ContentEmail/', 'E-mail', "&acaoId=" . urlencode($r_acaoId) . "&T218_Id=" . urlencode($r_acaoId)),
			array(uniqid(), '/est/est015/form/est015FormConfiguracoes/', 'Configurações', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($r_divIdMain)),
			// array(uniqid(), '/est/est015/content/est015contentTrocas/', 'Trocas', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			// array(uniqid(), '/est/est015/content/est015contentServicos/', 'Serviços', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
			//array(uniqid(), '/est/est015/content/est015contentMateriais/', 'Materiais', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est015TabEditarServico':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est015/content/est015contentProcessos/', 'Processos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est015/content/est015contentMateriais/', 'Materiais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est015TabEditarProduto':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est015/form/est015FormProdutosEditar/', 'Produto', '&tab=item&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T218_Id=' . urlencode($r_T218_Id)),
			array(uniqid(), '/est/est015/form/est015FormProdutosEditar/', 'IPI', '&tab=ipi&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T218_Id=' . urlencode($r_T218_Id)),
			array(uniqid(), '/est/est015/form/est015FormProdutosEditar/', 'Icms', '&tab=icms&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T218_Id=' . urlencode($r_T218_Id)),
			array(uniqid(), '/est/est015/form/est015FormProdutosEditar/', 'Pis/Cofins', '&tab=pis-cofins&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T218_Id=' . urlencode($r_T218_Id)),
            array(uniqid(), '/est/est015/content/est015ContentAgregados/', 'Agregados', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T218_Id=' . urlencode($r_T218_Id),false,true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabPrincipalDividir':
        $cabecalhoId = uniqid();
        echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 90px;"></div>';
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($cabecalhoId, '/est/est015/outro/est015Totalizadores/', '&T218_Id=' . urlencode($r_T218_Id) . '&divRefresh=' . urlencode($r_divRefresh), true),
        );
        echo gProcessaAcoes($actions);
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        array(uniqid(), '/est/est015/grid/est015GridProdutosDividir/', 'Produtos', '&acaoId=' . urlencode($r_T218_Id)),
        array(uniqid(), '/est/est015/grid/est015GridTrocasDividir/', 'Trocas', '&acaoId=' . urlencode($r_T218_Id)),
        array(uniqid(), '/est/est015/grid/est015GridServicosDividir/', 'Serviços', '&acaoId=' . urlencode($r_T218_Id)),
        array(uniqid(), '/est/est015/content/est015contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_T218_Id),false,true),
        //array(uniqid(), '/est/est002/form/est002form01/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_T224_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'est015TabSelecionarEmpresa':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);
    	$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
    	//$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        array(uniqid(), '/est/est015/form/est015FormSelecionarEmpresa/', 'Empresas', '&tab=geral&T218_Id=' . urlencode($r_T218_Id) . '&divRefresh=' . urlencode($r_divRefresh) . '&mudarFilial=' . urlencode($mudarFilial)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'est015TabAplicarDesconto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
    	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/form/est015FormAplicarDesconto/', 'Percentual', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
			array(uniqid(), '/est/est015/form/est015FormAplicarDescontoReais/', 'Reais', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
			array(uniqid(), '/est/est015/form/est015FormAplicarDescontoTotal/', 'Definir valor total', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabCompras':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridCompras/', 'Cotação', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabCotar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/form/est015FormCotar/', 'Cotar Produto', '&tab=geral&T218_Id=' . urlencode($r_T218_Id) . '&T219_Id=' . urlencode($r_T219_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabPrincipalAnalise':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridPrincipalAnalise/', 'Análise de Cotação de Compra'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est015TabNaoLiberar':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/form/est015FormNaoLiberar/', 'Motivo', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabProdutosVsFornecedor':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridPrincipalComparar/', 'Comparar'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est015TabCotacoesGrupo':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridCotacoesGrupo/', 'Cotações'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
