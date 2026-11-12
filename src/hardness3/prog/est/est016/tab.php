<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Grid Principal
	* Chamado em: est016content01
	*/
	case 'est016tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est016/grid/est016grid01/', 'Ordem de Compra'),
        array(uniqid(), '/est/est016/grid/est016GridProdutosPedido/', 'Produtos'),
        array(uniqid(), '/est/est016/grid/est016GridServicosPedido/', 'Serviços'),
        array(uniqid(), '/est/est016/grid/est016GridProdutosTrocaPedido/', 'Troca'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	/**
	* Form Principal - Cadastro Ordem de Compra
	* Chamado em: est016content02
	*/
	case 'est016tab02':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est016/outro/est016CabecalhoOrcamento/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		if ($_REQUEST['acaoId']) {
			$tabs = array();
			$tabsList = array(
			);
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/est/est016/content/est016content04/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($r_acaoId)));
			$tab1Id = uniqid();
			array_push($tabsList, array($tab1Id, '/est/est016/form/est016form01/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&divIdMain=' . urlencode($r_divIdMain)));
			$tab4Id = uniqid();
			array_push($tabsList, array($tab4Id, '/est/est016/form/est016FormTotais/', 'Totais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId']),false,true));
			$tab5Id = uniqid();
			array_push($tabsList, array($tab5Id, '/est/est016/content/est016ContentPagamento/', 'Pagamento', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId']),false,true));
			$tab6Id = uniqid();
			array_push($tabsList, array($tab6Id, '/est/est016/content/est016ContentDocumentos/', 'Documentos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId'])));
			$tab7Id = uniqid();
			array_push($tabsList, array($tab7Id, '/est/est016/grid/est016GridHistorico/', 'Histórico', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId']),false,true));
			$tab8Id = uniqid();
			array_push($tabsList, array($tab8Id, '/est/est016/content/est016ContentEmail/', 'E-mail', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId'])));
			$tab9Id = uniqid();
			array_push($tabsList, array($tab9Id, '/est/est016/form/est016FormConfiguracoes/', 'Configurações', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId'])));
			//array_push($tabsList, array($tab9Id, '/est/est016/form/est016FormPedidoFaturamento/', 'Faturamento', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId'])));
			$tab9Id = uniqid();
			//array_push($tabsList, array($tab9Id, '/est/est016/content/est016contentTrocas/', 'Trocas', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($r_acaoId)));
			$tab3Id = uniqid();
			//array_push($tabsList, array($tab3Id, '/est/est016/content/est016content05/', 'Serviços', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId']),false,true));

			$tab8Id = uniqid();
			//array_push($tabsList, array($tab8Id, '/est/est016/grid/est016GridEntregaServicos/', 'Entrega', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T224_Id=" . urlencode($_REQUEST['acaoId']),false,true));
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		}
	break;

	case 'est016tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est016/content/est016content07/', 'Processos', '&T230_Id=' . urlencode($r_T230_Id)),
		array(uniqid(), '/est/est016/content/est016content06/', 'Materiais', '&T230_Id=' . urlencode($r_T230_Id)),

		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabEditarProduto':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		array(uniqid(), '/est/est016/form/est016FormProdutosEditar/', 'Produto', '&tab=item&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
		array(uniqid(), '/est/est016/form/est016FormProdutosEditar/', 'IPI', '&tab=ipi&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
		array(uniqid(), '/est/est016/form/est016FormProdutosEditar/', 'Icms', '&tab=icms&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
		array(uniqid(), '/est/est016/form/est016FormProdutosEditar/', 'Pis/Cofins', '&tab=pis-cofins&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
        array(uniqid(), '/est/est016/content/est016ContentAgregados/', 'Agregados', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T224_Id=' . urlencode($r_T224_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

	case 'est016TabPagamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est016/content/est016ContentPagamento/', 'Pagamento', '&T224_Id=' . urlencode($r_T224_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabIncluirPedido':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$GerarOCEstoque = isset($r_GerarOCEstoque) ? $r_GerarOCEstoque : false;

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormIncluirPedido/', 'Pedido', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&GerarOCEstoque=' . urlencode($GerarOCEstoque)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabAplicarDesconto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormAplicarDescontoPercentual/', 'Percentual Itens', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
			array(uniqid(), '/est/est016/form/est016FormAplicarDescontoReais/', 'Reais', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
			array(uniqid(), '/est/est016/form/est016FormAplicarDescontoTotal/', 'Definir Valor Total', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
			array(uniqid(), '/est/est016/form/est016FormAplicarDescontoValorTotal/', 'Desconto no Valor Total', '&tab=geral&acaoId=' . urlencode($r_T224_Id)),

		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabCompras':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016GridCompras/', 'Compras', '&acaoId=' . urlencode($r_acaoId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabCotar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormCotar/', 'Cotar Produto', '&tab=geral&T224_Id=' . urlencode($r_T224_Id) . '&T225_Id=' . urlencode($r_T225_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabComprar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormComprar/', 'Comprar Produto', '&tab=geral&T224_Id=' . urlencode($r_T224_Id) . '&T225_Id=' . urlencode($r_T225_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabPrincipalAnalise':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016GridPrincipalAnalise/', 'Análise de Ordem de Compra'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabNaoLiberar':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormNaoLiberar/', 'Motivo', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabNaoLiberarTelaAnalise':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormNaoLiberarTelaAnalise/', 'Motivo', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabProdutosDivergencia':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016GridProdutosPedido/', 'Recebimento NFe', "&modoModelo=RECEBIMENTO"),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	 * Grid Principal
	 * Chamado em: est015ContentPrincipal
	 */
	case 'GerarOT':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/GerarOT/', 'Transferência', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'apenasOCs':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016grid01/', 'Ordens'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016TabOrdensCompraGrupo':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016GridOrdensCompraGrupo/', 'Ordens de Compra'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016tabIncluirOC':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/form/est016FormIncluirOC/', 'Ordens de Compra', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est016tabDividirOrdemCompra':
		$cabecalhoId = uniqid();
        echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 75px;"></div>';
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($cabecalhoId, '/est/est016/outro/est016TotalizadoresDividirOrdemCompra/', '&acaoId=' . urlencode($r_D024_Id) . '&divRefresh=' . urlencode($r_divRefresh), true),
        );
        echo gProcessaAcoes($actions);
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
	        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
	        array(uniqid(), '/est/est016/grid/est016gridDividirOrdemCompra/', 'Produtos', '&acaoId=' . urlencode($r_D024_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
	break;

	case 'est016TabEmpresaDividirOrdemCompra':
		// Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
	        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
	        array(uniqid(), '/est/est016/form/est016FormEmpresaDividirOrdemCompra/', 'Empresas', '&tab=geral&T224_Id=' . urlencode($r_T224_Id) . '&divRefresh=' . urlencode($r_divRefresh)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
	break;
}
