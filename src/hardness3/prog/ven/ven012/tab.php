<?php
namespace hardness;
	/**
	* Weiberlan
	* 12/11/2011
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: ven012Content01
		*/

		case 'ven012TabPrincipal':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnviar/', 'Enviar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T003_Flag_Expedicao=2&divReload=" . urlencode($uniqidPrincipal)),
				array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnviadas/', 'Enviados', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T003_Flag_Expedicao=2&divReload=" . urlencode($uniqidPrincipal)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'ven012TabFaturamento':
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/ven/ven012/outro/ven012CabecalhoNota/', '&T005_Id=' . urlencode($r_T005_Id), true),
			);
			echo gProcessaAcoes($actions);
			//Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
			$formId = uniqid();
			$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/ven/ven012/form/ven012FormPedido/', 'Pedido', "&tab=cabecalho&acaoId=" . urlencode($r_T005_Id) . '&formId=' . urlencode($formId). '&T005_Id=' . urlencode($r_T005_Id)),
				array(uniqid(), '/ven/ven012/form/ven012FormPedido', 'Frete', "&tab=frete&acaoId=" . urlencode($r_T005_Id) . '&formId=' . urlencode($formId) . '&T005_Id=' . urlencode($r_T005_Id)),
				array(uniqid(), '/ven/ven012/content/ven012ContentPagamento/', 'Pagamento',  "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($r_T005_Id), true, true),
				array(uniqid(), '/ven/ven002/form/ven002FormPedidoFaturamento/', 'Faturamento',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id)),
				//array(uniqid(), '/est/est002/grid/est002GridProdutosPedido/', 'Produtos',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id), true)
				array(uniqid(), '/ven/ven012/grid/ven012GridProdutosPedido/', 'Produtos',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id), true, true),
				array(uniqid(), '/ven/ven012/grid/ven012GridServicosPedido/', 'Serviços',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id), true, true),
				array(uniqid(), '/ven/ven012/grid/ven012GridTrocasPedido/', 'Trocas',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id), true, true),
				array(uniqid(), '/ven/ven012/grid/ven012GridAgrupamentoPedido/', 'Agrupamentos',  "&tab=geral&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id), true, true)
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs, true);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'ven012TabPagamento':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven012/content/ven012ContentPagamento/', 'Pagamento', '&T005_Id=' . urlencode($r_T005_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;
			
		case 'ven012TabInutilizar':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven003/form/ven003FormInutilizar/', 'Inutilizar', '&tab=geral&T007_Id=' . urlencode($r_T007_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
            
        case 'ven012TabSelecionarEmpresa':
            // Form (caso seja o mesmo form em multiplas tabs)
            //$formId = uniqid();
            //$g['smarty']->assign('formId', $formId);
	    	$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
	    	$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;
	    	$dividirPedidoEmpresa = isset($r_dividirPedidoEmpresa) ? $r_dividirPedidoEmpresa : false;

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/ven/ven012/form/ven012FormSelecionarEmpresa/', 'Empresas', '&tab=geral&T005_Id=' . urlencode($r_T005_Id) . '&divRefresh=' . urlencode($r_divRefresh) . '&mudarFilial=' . urlencode($mudarFilial) . '&gerarNF=' . urlencode($r_gerarNF) . '&dividirPedidoEmpresa='. urlencode($dividirPedidoEmpresa)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        
        case 'ven012TabPrincipalDividir':
            $cabecalhoId = uniqid();
            echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 90px;"></div>';
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($cabecalhoId, '/ven/ven012/outro/ven012Totalizadores/', '&T005_Id=' . urlencode($r_T005_Id) . '&divRefresh=' . urlencode($r_divRefresh), true),
            );
            echo gProcessaAcoes($actions);
            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/ven/ven012/grid/van012GridProdutosDividir/', 'Produtos', '&acaoId=' . urlencode($r_T005_Id)),
            array(uniqid(), '/ven/ven012/grid/ven012GridTrocasDividir/', 'Trocas', '&acaoId=' . urlencode($r_T005_Id)),
            array(uniqid(), '/ven/ven012/grid/ven012GridServicosDividir/', 'Serviços', '&acaoId=' . urlencode($r_T005_Id)),
            array(uniqid(), '/ven/ven002/content/ven002ContentPagamento/', 'Pagamentos', "&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id)),
            //array(uniqid(), '/ven/ven002/form/ven002form01/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_T005_Id)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;

        case 'ven012TabPrincipalDividirPedidoEmpresa':
            $cabecalhoId = uniqid();
            echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 37px;"></div>';
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($cabecalhoId, '/ven/ven012/outro/ven012TotalizadoresDividirEmpresa/', '&acaoId=' . urlencode($r_D024_Id) . '&divRefresh=' . urlencode($r_divRefresh), true),
            );
            echo gProcessaAcoes($actions);
            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/ven/ven012/grid/van012GridProdutosDividirEmpresa/', 'Produtos', '&acaoId=' . urlencode($r_D024_Id)),
            //array(uniqid(), '/ven/ven012/grid/ven012GridTrocasDividirEmpresa/', 'Trocas', '&acaoId=' . urlencode($r_D024_Id)),
            //array(uniqid(), '/ven/ven012/grid/ven012GridServicosDividirEmpresa/', 'Serviços', '&acaoId=' . urlencode($r_D024_Id)),
            //array(uniqid(), '/ven/ven002/content/ven002ContentPagamento/', 'Pagamentos', "&acaoId=" . urlencode($r_T005_Id) . "&T005_Id=" . urlencode($r_T005_Id)),
            //array(uniqid(), '/ven/ven002/form/ven002form01/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_T005_Id)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;

        case 'ven012GridFilaDeEnvioNovo':
			// Definição das Tabs
			$tabs = array();
			
			$tabsList = array(
				array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnviar/', 'Enviar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),				
				array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnviadas/', 'Enviadas', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
				//array(uniqid(), '/ven/ven012/grid/ven012GridLogNotas/', 'Log', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
				array(uniqid(), '/ven/ven012/grid/ven012GridErrosSefaz/', 'Tabela Erros SEFAZ', "&divReload=" . urlencode($uniqidPrincipal)),
			);
			
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');

		break;
	}

?>
