<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/11
	*/
	case 'imp005TabDi':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/grid/imp005GridDi/', 'Purchase Order', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/imp/imp005/grid/imp005GridProdutoDI/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp005TabDiIncluir':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/imp/imp005/outro/imp005Cabecalho/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		}
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if (!empty($r_acaoId)) {
			$tabsList[] = array(uniqid(), '/imp/imp005/content/imp005ContentProdutos/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp005/form/imp005FormDi/', 'Purchase Order', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridAdicoes/', 'Adicoes', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/imp/imp005/form/imp005FormTotal/', 'Total produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId),false,true);
			// $tabsList[] = array(uniqid(), '/imp/imp005/content/imp005ContentDocumentos/', 'Documentos', '&acaoId=' . urlencode($r_acaoId) . '&T295_Id=' . urlencode($r_acaoId), false);
			$tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridDespesas/', 'Despesas', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridChecklist/', 'Checklist', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/content/imp005ContentEmail/', 'E-mail', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridContasPagar/', 'Contas pagar', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridContasReceber/', 'Contas Receber', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/form/imp005FormConfiguracoes/', 'Configurações', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/grid/imp005GridLI/', 'L.I.', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp005/content/imp005contentImportarCSV/', 'Importar CSV', '&acaoId=' . urlencode($r_acaoId));
		} else {
			$tabsList[] = array(uniqid(), '/imp/imp005/form/imp005FormDi/', 'Purchase Order', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp005TabCadastroDespesas':

		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/form/imp005FormCadastroDespesas/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp005TabCadastroLI':
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/form/imp005FormCadastroLI/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T295_Id=' . urlencode($r_T295_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp005TabCadastroContasPagar':
	     //echo "acao ID: " . $r_acaoId;
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/form/imp005FormCadastroContasPagar/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T295_Id=' . urlencode($r_T295_Id)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
        case 'imp005TabPedidosProduto':

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                array(uniqid(), '/imp/imp005/grid/pedidosProduto/', 'Pedidos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        	
	
	case 'imp005TabCadastroContasReceber':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/imp/imp005/form/imp005FormCadastroContasReceber/', 'Cadastro', "&tab=cadastro_titulo&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&T295_Id=' . urlencode($r_T295_Id)),						
		);
		
		// só cria as demais tabs caso seja update
		// if ($_REQUEST['acaoId']) {
			// $tab2Id = uniqid();
			// array_push($tabsList, array($tab2Id, '/fin/fin001/form/fin001form02/', 'Acréscimos e Descontos', "&tab=acrescimos_descontos&acaoId=" . urlencode($_REQUEST['acaoId'])));
			// $tab3Id = uniqid();
			// array_push($tabsList, array($tab3Id, '/fin/fin001/form/fin001form03/', 'Recebimentos', "&tab=recebimentos&acaoId=" . urlencode($_REQUEST['acaoId'])));
			// $tab4Id = uniqid();
			// array_push($tabsList, array($tab4Id, '/fin/fin001/form/fin001form04/', 'Agrupamentos', "&tab=agrupamentos&acaoId=" . urlencode($_REQUEST['acaoId'])));
		// }
// 		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp005TabImportarItensOC':
		$sqlD024 = mysql_query("SELECT T295_D024_Id FROM T295 WHERE T295_Id = '{$r_acaoId}'");
		$resD024 = mysql_fetch_array($sqlD024);
		
		$divRefresh = uniqid();

		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 72px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/imp/imp005/outro/imp005CabecalhoItensOC/', '&D024_Id=' . urlencode($resD024['T295_D024_Id']) . '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($divRefresh), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($divRefresh, '/imp/imp005/grid/imp005GridImportarItensOC/', 'Itens', '&tab=geral&D024_Id=' . urlencode($resD024['T295_D024_Id']) . '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp005TabGerarDI':
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 37px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/imp/imp005/outro/imp005CabecalhoGerarDI/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($r_divRefresh), true),
		);
		echo gProcessaAcoes($actions);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/imp/imp005/grid/imp005GridGerarDI/', 'Produtos', '&acaoId=' . urlencode($r_acaoId)),
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

	case 'imp005TabOrdemCompraGrupo':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/grid/imp005GridOrdemCompraGrupo/', 'P.O'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'imp005TabNaoLiberarTelaAnalise':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/form/imp005FormNaoLiberarTelaAnalise/', 'Motivo', '&tab=geral&T295_Id=' . urlencode($r_T295_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp005TabPrincipalAnalise':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp005/grid/imp005GridPrincipalAnalise/', 'Análise PO'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
