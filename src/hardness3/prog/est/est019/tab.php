<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 03/01/12
	*/
	case 'est019TabComprasEstoque':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEstoque/', 'Compras de Estoque', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),
			array(uniqid(), '/est/est019/grid/est019GridCotacaoEmergencia/', 'Cotação de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
			array(uniqid(), '/est/est019/grid/est019GridComprasEmergencia/', 'Compras de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabComprasEstoqueLinha':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEstoque/', 'Geral', '&tab=geral&content1=' . urlencode($r_content1) . '&extraLinha=' . urlencode($r_extraLinha)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');	
	break;
	
	case 'cad046TabCadastroMotivosCancelamento':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad046/form/cad046FormCadastroMotivosCancelamento/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabCotacaoEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridCotacaoEmergencia/', 'Cotação de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabMontagemEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridMontagensEmergencia/', 'Montagens de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabServicosEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridServicosEmergencia/', 'Serviços de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabProducoesEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridServicosEmergencia/', 'Produções de Emergência', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabPrevisaoDI':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridPrevisaoDI/', 'Previsão DI', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&mes=' . urlencode($r_mes)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabProdutosEstoqueVazio':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEstoque/', 'Compras de Estoque', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabprodutosEstoqueEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEstoque/', 'Compras Emergencia', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabProdutosHistoricoSemLocacao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridProdutosHistoricoSemLocacao/', 'Históricos sem locação', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabComprasEmergencia':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEmergencia/', 'Cotar'),
        	array(uniqid(), '/est/est019/grid/est019GridComprasEmergenciaCotando/', 'Cotando'),
        	//array(uniqid(), '/est/est019/grid/est019GridComprasEmergenciaComprar/', 'Comprar'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabComprasInternacional':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if (isset($g['c029Ids'][258])) {
			$tabsList[] = array(uniqid(), '/est/est019/grid/est019GridComprasEstoque/', 'Compras Estoque', '&acaoId=' . urlencode($r_acaoId), false, false);			
		}
		if (isset($g['c029Ids'][259])) {
			if($g['C031']['desabilitarCotacaoEmergenciaInternacional'] == 'N'){
				$tabsList[] = array(uniqid(), '/est/est019/grid/est019GridCotacaoEmergencia/', 'Cotação Emergência', '&acaoId=' . urlencode($r_acaoId), false, false);
			}
			if($g['C031']['desabilitarCompraEmergenciaInternacional'] == 'N'){
				$tabsList[] = array(uniqid(), '/est/est019/content/est019ContentComprasEmergencia/', 'Compras Emergência', '&acaoId=' . urlencode($r_acaoId), false, false);
			}
			if($g['C031']['desabilitarMontagemEmergencia'] == 'N'){
				$tabsList[] = array(uniqid(), '/est/est019/grid/est019GridMontagensEmergencia/', 'Montagens Emergência', "&acaoId=" . urlencode($r_acaoId), false, false);
			}
		}		


		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabCotacaoEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est009/grid/est019GridCotacaoEmergencia/', 'Cotação de Emergência Internacional', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est019TabEstoqueEmergencia':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est019/grid/est019GridComprasEstoque', 'Compras Estoque Internacional', '&acaoId=' . urlencode($r_acaoId), false, false),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
