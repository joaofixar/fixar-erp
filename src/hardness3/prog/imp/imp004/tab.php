<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/11
	*/
	case 'imp004TabDi':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp004/grid/imp004GridDi/', 'Quotation', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/imp/imp004/grid/imp004GridProdutoDI/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp004TabDiIncluir':
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
			array($cabecalhoId, '/imp/imp004/outro/imp004Cabecalho/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		}
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if (!empty($r_acaoId)) {
			$tabsList[] = array(uniqid(), '/imp/imp004/content/imp004ContentProdutos/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp004/form/imp004FormDi/', 'Quotation', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridAdicoes/', 'Adicoes', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			//$tabsList[] = array(uniqid(), '/imp/imp004/form/imp004FormTotal/', 'Total produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId),false,true);
			// $tabsList[] = array(uniqid(), '/imp/imp004/content/imp004ContentDocumentos/', 'Documentos', '&acaoId=' . urlencode($r_acaoId) . '&T293_Id=' . urlencode($r_acaoId), false);
			$tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridDespesas/', 'Despesas', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridChecklist/', 'Checklist', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/content/imp004ContentEmail/', 'E-mail', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridContasPagar/', 'Contas pagar', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridContasReceber/', 'Contas Receber', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/form/imp004FormConfiguracoes/', 'Configurações', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/grid/imp004GridLI/', 'L.I.', '&acaoId=' . urlencode($r_acaoId));
			// $tabsList[] = array(uniqid(), '/imp/imp004/content/imp004contentImportarCSV/', 'Importar CSV', '&acaoId=' . urlencode($r_acaoId));
		} else {
			$tabsList[] = array(uniqid(), '/imp/imp004/form/imp004FormDi/', 'Quotation', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp004TabCadastroDespesas':
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp004/form/imp004FormCadastroDespesas/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp004TabCadastroLI':
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp004/form/imp004FormCadastroLI/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T293_Id=' . urlencode($r_T293_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp004TabCadastroContasPagar':
	     //echo "acao ID: " . $r_acaoId;
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp004/form/imp004FormCadastroContasPagar/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T293_Id=' . urlencode($r_T293_Id)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
        case 'imp004TabPedidosProduto':

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                array(uniqid(), '/imp/imp004/grid/pedidosProduto/', 'Pedidos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        	
	
	case 'imp004TabCadastroContasReceber':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/imp/imp004/form/imp004FormCadastroContasReceber/', 'Cadastro', "&tab=cadastro_titulo&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&T293_Id=' . urlencode($r_T293_Id)),						
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

	case 'imp004TabImportarItensOC':
		$sqlD024 = mysql_query("SELECT T293_D024_Id FROM T293 WHERE T293_Id = '{$r_acaoId}'");
		$resD024 = mysql_fetch_array($sqlD024);
		
		$divRefresh = uniqid();

		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 72px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/imp/imp004/outro/imp004CabecalhoItensOC/', '&D024_Id=' . urlencode($resD024['T293_D024_Id']) . '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($divRefresh), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($divRefresh, '/imp/imp004/grid/imp004GridImportarItensOC/', 'Itens', '&tab=geral&D024_Id=' . urlencode($resD024['T293_D024_Id']) . '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp004TabGerarPO':
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 37px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/imp/imp004/outro/imp004CabecalhoGerarPO/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($r_divRefresh), true),
		);
		echo gProcessaAcoes($actions);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/imp/imp004/grid/imp004GridGerarPO/', 'Produtos', '&acaoId=' . urlencode($r_acaoId)),
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

	case 'imp004TabCotacoesGrupo':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp004/grid/imp004GridCotacoesGrupo/', 'Cotações'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
}
