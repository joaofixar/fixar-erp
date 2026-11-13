<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/11
	*/
	case 'imp003TabDi':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if (isset($g['c029Ids'][258]) || isset($g['c029Ids'][259])) {
			$tabsList[] = array(uniqid(), '/est/est019/content/est019ComprasInternacinal/', 'Compras', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		if (isset($g['c029Ids'][260])) {
			$tabsList[] = array(uniqid(), '/imp/imp004/content/imp004ContentDi/', 'Quotation', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		if (isset($g['c029Ids'][261])) {
			$tabsList[] = array(uniqid(), '/imp/imp005/content/imp005ContentDi/', 'Purchase Order', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}

		$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003ContentDiDeclaracaoImportacao/', 'D.I.', '&tab=geral&acaoId=' . urlencode($r_acaoId));

		
		// $tabsList = array(
		// 	// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		// 	array(uniqid(), '/imp/imp004/content/imp004ContentDi/', 'Cotation', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		// 	array(uniqid(), '/imp/imp005/content/imp005ContentDi/', 'Purchase Order', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		// 	array(uniqid(), '/imp/imp003/content/imp003ContentDiDeclaracaoImportacao/', 'D.I.', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		// 	// array(uniqid(), '/imp/imp003/grid/imp003GridDi/', 'D.I - Declaração de Importação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		// 	// array(uniqid(), '/imp/imp003/grid/imp003GridProdutoDI/', 'D.I - Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		// 	// array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		// );
		
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp003TabDiDeclaracaoImportacao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp003/grid/imp003GridDi/', 'Declaração de Importação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/imp/imp003/grid/imp003GridProdutoDI/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp003TabDiIncluir':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/

		$T075 = mysql_query("SELECT T075_Modelo_Calculos FROM T075 WHERE T075_Id = '{$r_acaoId}'");
		$mT075 = mysql_fetch_array($T075);

		if (isset($r_acaoId) && !empty($r_acaoId)) {
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/imp/imp003/outro/imp003Cabecalho/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		}
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if (!empty($r_acaoId)) {
			$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003ContentProdutos/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			if($mT075['T075_Modelo_Calculos'] == '1'){
				$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormDi/', 'D.I.', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			} else {
				$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormDINovo/', 'D.I.', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			}
			$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridAdicoes/', 'Adicoes', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormTotal/', 'Total produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId),false,true);
			$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003ContentDocumentos/', 'Documentos', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($r_acaoId), false);
			if($mT075['T075_Modelo_Calculos'] == '1'){
				$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridDespesas/', 'Despesas', '&acaoId=' . urlencode($r_acaoId));
			} else {
				$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridDespesasNovo/', 'Despesas', '&acaoId=' . urlencode($r_acaoId));
			}

			$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridChecklist/', 'Checklist', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003ContentEmail/', 'E-mail', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridContasPagar/', 'Contas pagar', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridContasReceber/', 'Contas Receber', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormConfiguracoes/', 'Configurações', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/grid/imp003GridLI/', 'L.I.', '&acaoId=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003contentImportarCSV/', 'Importar CSV', '&acaoId=' . urlencode($r_acaoId));
		} else {
			if($g['C031']['modeloCalculosDI'] == '1'){
				$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormDi/', 'Declaração de Importação', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			} else {
				$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormDINovo/', 'D.I.', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			}
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp003TabCadastroDespesas':

		log("tab T168_Id=".$r_acaoId);
		$T155_T075_Id = isset($r_T155_T075_Id) ? $r_T155_T075_Id : false;
		log("tab T155_T075_Id=".$T155_T075_Id);

		$T075 = mysql_query("SELECT T075_Modelo_Calculos FROM T075 WHERE T075_Id = '{$r_T075_Id}'");
		$mT075 = mysql_fetch_array($T075);
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			
		);

		if($mT075['T075_Modelo_Calculos'] == '1'){
			$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormCadastroDespesas/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T155_T075_Id=' . urlencode($T155_T075_Id));			
		} else {
			$tabsList[] = array(uniqid(), '/imp/imp003/form/imp003FormCadastroDespesasNovo/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T155_T075_Id=' . urlencode($T155_T075_Id));			
		}

		if($r_acaoId>0){
			$tabsList[] = array(uniqid(), '/imp/imp003/content/imp003ContentDocumentosDespesa/', 'Documentos', '&acaoId=' . urlencode($r_acaoId) . '&T155_T075_Id=' . urlencode($T155_T075_Id) . '&T168_Id=' . urlencode($r_acaoId));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp003TabCadastroLI':
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp003/form/imp003FormCadastroLI/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T075_Id=' . urlencode($r_T075_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'imp003TabCadastroContasPagar':
	     //echo "acao ID: " . $r_acaoId;
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/imp/imp003/form/imp003FormCadastroContasPagar/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T075_Id=' . urlencode($r_T075_Id)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
        case 'imp003TabPedidosProduto':

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                array(uniqid(), '/imp/imp003/grid/pedidosProduto/', 'Pedidos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        	
	
	case 'imp003TabCadastroContasReceber':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/imp/imp003/form/imp003FormCadastroContasReceber/', 'Cadastro', "&tab=cadastro_titulo&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&T075_Id=' . urlencode($r_T075_Id)),						
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

	case 'imp003TabImportarItensOC':
		$sqlD024 = mysql_query("SELECT T075_D024_Id FROM T075 WHERE T075_Id = '{$r_acaoId}'");
		$resD024 = mysql_fetch_array($sqlD024);
		
		$divRefresh = uniqid();

		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 72px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/imp/imp003/outro/imp003CabecalhoItensOC/', '&D024_Id=' . urlencode($resD024['T075_D024_Id']) . '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($divRefresh), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($divRefresh, '/imp/imp003/grid/imp003GridImportarItensOC/', 'Itens', '&tab=geral&D024_Id=' . urlencode($resD024['T075_D024_Id']) . '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp003TabAssociarItemProduto':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/imp/imp003/form/imp003FormAssociarItemProduto/', 'Item', "&tab=geral&T055_Id=" . urlencode($r_T055_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp003TabPacoteDespesas':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/imp/imp003/form/imp003FormPacoteDespesas/', 'Pacote', "&tab=geral&T055_Id=" . urlencode($r_T055_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'imp003TabItensImportadosDI':

		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/imp/imp003/outro/Imp003OutroCabecalhoItemXML/', '&acaoId=' . urlencode($r_T055_Id), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array();
		if($r_T310_Id > 0){
			$tabsList[] = array($tab1, '/imp/imp003/grid/imp003GridItemVinculadoXMLDI/', 'Item Vinculado', "&tab=geral&T055_Id=" . urlencode($r_T055_Id));
		} else {
			$tabsList[] = array($tab1, '/imp/imp003/grid/imp003GridItensImportadosDI/', 'Vincular Produto', "&tab=geral&T075_Id=" . urlencode($r_T075_Id) . '&T055_Id=' . urlencode($r_T055_Id));
		}

		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
	

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
