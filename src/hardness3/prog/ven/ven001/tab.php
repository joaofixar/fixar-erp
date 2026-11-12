<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: ven001ContentPrincipal
	 */
	case 'ven001TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridPrincipalOrcamentos/', 'Orçamentos'),
			array(uniqid(), '/ven/ven001/grid/ven001GridPrincipalOrcamentosCancelados/', 'Cancelados'),
            array(uniqid(), '/ven/ven001/grid/ven001GridPrincipalProdutos/', 'Produtos'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	 * Grid Principal
	 * Chamado em: ven001ContentOrcamento
	 */
	case 'ven001TabOrcamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/form/ven001FormOrcamento/', 'Orçamento', '&tab=orcamentos&acaoId=' . urlencode($r_acaoId) . '&T139_Id=' . urlencode(@$r_T139_Id) . '&D024_Id_CRM='.urlencode(@$r_D024_Id_CRM) . '&tabCatalogoCRM='.urlencode($r_tabCatalogoCRM)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	/**
	 * Form Principal - Editar Orçamentos
	 * Chamado em: ven001ContentEditarOrcamento
	 */
	case 'ven001TabEditarOrcamento':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional), pularDisplay
			array($cabecalhoId, '/ven/ven001/outro/ven001CabecalhoOrcamento/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tab4 = uniqid();
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven001/content/ven001ContentProdutos/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($tab4)),
			array(uniqid(), '/ven/ven001/form/ven001FormOrcamento/', 'Cabeçalho', '&tab=orcamentos&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($r_divIdMain)),
			//array($tab4, '/ven/ven001/content/ven001contentTotais/', 'Totais', "&acaoId=" . urlencode($r_acaoId), false, true),
			array(uniqid(), '/ven/ven001/content/ven001contentPagamentos/', 'Pagamento', "&acaoId=" . urlencode($r_acaoId),false,true),
			array(uniqid(), '/ven/ven001/content/ven001ContentDocumentos/', 'Documentos', "&acaoId=" . urlencode($r_acaoId) . "&T003_Id=" . urlencode($r_acaoId),false,true),
			array(uniqid(), '/ven/ven001/content/ven001ContentEmail/', 'E-mail', "&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/ven/ven001/content/ven001contentTrocas/', 'Trocas', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/ven/ven001/content/ven001contentServicos/', 'Serviços', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
			//array(uniqid(), '/crm/crm001/grid/crm001GridAgendaPorOrcamento/', 'Agenda', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
			array(uniqid(), '/ven/ven001/form/ven001FormMoeda/', 'Moeda', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
			array(uniqid(), '/etc/etc017/content/etc017ContentAtividadesT003/', 'Atividades', "&T003_Id=" . urlencode($_REQUEST['acaoId']),false,true),
			//array(uniqid(), '/ven/ven001/content/ven001contentMateriais/', 'Materiais', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			

		);
		if($g['nivelAlcada'] == 4){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormComissoes/', 'Comissões', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		}
		$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormConfiguracoes/', 'Configurações', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabEditarServico':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven001/content/ven001contentProcessos/', 'Processos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven001/content/ven001contentMateriais/', 'Materiais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabEditarProduto':
		
		$sqlT004 = mysql_query("SELECT T004_D059_Id FROM T004 WHERE T004_Id = '{$r_acaoId}'");
		$rowT004 = mysql_fetch_array($sqlT004);
		
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven001/form/ven001FormProdutosEditar/', 'Produto', '&tab=item&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T003_Id=' . urlencode($r_T003_Id)),
			array(uniqid(), '/ven/ven001/form/ven001FormProdutosEditar/', 'IPI', '&tab=ipi&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T003_Id=' . urlencode($r_T003_Id)),
			array(uniqid(), '/ven/ven001/form/ven001FormProdutosEditar/', 'Icms', '&tab=icms&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T003_Id=' . urlencode($r_T003_Id) . '&D059_Id=' . $rowT004['T004_D059_Id']),
			array(uniqid(), '/ven/ven001/form/ven001FormProdutosEditar/', 'Pis/Cofins', '&tab=pis-cofins&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T003_Id=' . urlencode($r_T003_Id)),
            array(uniqid(), '/ven/ven001/content/ven001ContentAgregados/', 'Agregados', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T003_Id=' . urlencode($r_T003_Id),false,true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabPrincipalDividir':
        $cabecalhoId = uniqid();
        echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 90px;"></div>';
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($cabecalhoId, '/ven/ven001/outro/ven001Totalizadores/', '&T003_Id=' . urlencode($r_T003_Id) . '&divRefresh=' . urlencode($r_divRefresh), true),
        );
        echo gProcessaAcoes($actions);
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        array(uniqid(), '/ven/ven001/grid/ven001GridProdutosDividir/', 'Produtos', '&acaoId=' . urlencode($r_T003_Id)),
        array(uniqid(), '/ven/ven001/grid/ven001GridTrocasDividir/', 'Trocas', '&acaoId=' . urlencode($r_T003_Id)),
        array(uniqid(), '/ven/ven001/grid/ven001GridServicosDividir/', 'Serviços', '&acaoId=' . urlencode($r_T003_Id)),
        array(uniqid(), '/ven/ven001/content/ven001contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_T003_Id),false,true),
        //array(uniqid(), '/ven/ven002/form/ven002form01/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_T005_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'ven001TabSelecionarEmpresa':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);
    	$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
    	//$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        array(uniqid(), '/ven/ven001/form/ven001FormSelecionarEmpresa/', 'Empresas', '&tab=geral&T003_Id=' . urlencode($r_T003_Id) . '&divRefresh=' . urlencode($r_divRefresh) . '&mudarFilial=' . urlencode($mudarFilial)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'ven001TabAplicarDesconto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
    	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if($g['C031']['habilitarDescontoPercentual'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarDesconto/', 'Percentual', '&tab=geral&idTabelaCabecalho=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&tabelaProduto=' . urlencode($r_tabelaProduto));
		}
		if($g['C031']['habilitarDescontoReais'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarDescontoReais/', 'Reais', '&tab=geral&idTabelaCabecalho=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&tabelaProduto=' . urlencode($r_tabelaProduto));
		}
		if($g['C031']['habilitarDefinirValorTotal'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormDefinirValorTotal/', 'Definir valor total', '&tab=geral&idTabelaCabecalho=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&tabelaProduto=' . urlencode($r_tabelaProduto));
		}
		if($g['C031']['habilitarDescontoValorTotal'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarDescontoValorTotal/', 'Desconto no valor total', '&tab=geral&acaoId=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho));
		}
		if($g['C031']['habilitarPercentualDescontoValorTotal'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarPercentualDescontoValorTotal/', 'Percentual no valor total', '&tab=geral&acaoId=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabCompras':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridCompras/', 'Cotação', '&tab=geral&T003_Id=' . urlencode($r_T003_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabCotar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/form/ven001FormCotar/', 'Cotar Produto', '&tab=geral&T003_Id=' . urlencode($r_T003_Id) . '&T004_Id=' . urlencode($r_T004_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabProdutoOC':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutoOC/', 'OC', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutoDI/', 'DI', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutoPO/', 'PO', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutoNF/', 'NF', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabProdutoOP':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutoOP/', 'OP', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabHistoricoProduto':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad002/grid/historico/', 'Histórico', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabAplicarPrecoPorKg':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/form/ven001FormAplicarPrecoPorKg/', 'Aplicar', "&T003_Id={$r_T003_Id}&tab=geral",'',true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabMontagemIPV':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridMontagemIPV/', 'IPV', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabProdutosSimples':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridProdutosSimples/', 'Produtos', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabIncluirItemMontado':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven001/grid/ven001GridIncluirItemMontado/', 'Produtos', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven001TabAssociarItemProduto':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/ven/ven001/form/ven001FormAssociarItemProduto/', 'Item', "&tab=geral&T004_Id=" . urlencode($r_T004_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
