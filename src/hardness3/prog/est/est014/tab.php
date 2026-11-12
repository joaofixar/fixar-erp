<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est014TabRequisicoesdeCompraGeral':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/grid/est014GridRequisicoesdeCompraGeral/', 'Requisitar compra', '', false, true),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est014TabRequisicoesdeCompra':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/grid/est014GridRequisicoesdeCompra/', 'Requisições de Compra', '', false, true),
			array(uniqid(), '/est/est014/grid/est014GridProdutosCotacoes/', 'Cotações', '', false, true),
			array(uniqid(), '/est/est014/grid/est014GridEmails/', 'E-mail', '', false, true),
            array(uniqid(), '/est/est004/grid/est004GridItensOrdemCompra/', 'Ordens compra pendentes', '', false, true),
			array(uniqid(), '/est/est004/grid/est004GridNotasFiscaisRecebidas/', 'Notas Fiscais Recebidas' ),
			array(uniqid(), '/est/est004/form/est004FormImportarXML/', 'Importar XML', '&tab=geral'),
			array(uniqid(), '/est/est014/grid/est014GridOrdemServico/', 'Ordem de Serviço', '&tab=geral'),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est014TabRequisicoesdeCompraEditarIncluir':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/form/est014FormRequisicoesdeCompraEditarIncluir/', 'Requisições de Compra', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
	
	case 'est014TabAssociarProduto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/form/est014FormAssociarProduto/', 'Produto', '&tab=geral&T189_Id=' . urlencode($r_T189_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
	
	case 'est014TabGerarOC':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/grid/est014GridGerarOC/', 'Gerar OC', '&tab=geral&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
		
	case 'est014RequisicaoCompra':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/form/est014FormRequisicaoCompra/', 'Incluir Requisição', '&tab=geral&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'est014TabOrdemServico':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs

		$T128=mysql_query("SELECT T128_D024_Id from T128 WHERE T128_Id='{$r_T128_Id}'");
		$mT128=mysql_fetch_array($T128);

		if($mT128['T128_D024_Id'] > 0){
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est014/content/est014FormOrdemServico/', 'Produtos', '&tab=geral&T128_Id=' . urlencode($r_T128_Id)),
				array(uniqid(), '/est/est014/form/est014FormCabecalhoOS/', 'Cabeçalho', '&tab=geral&T128_Id=' . urlencode($r_T128_Id) . '&acaoId=' .urlencode($r_T128_Id)),
			);
		}else{
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est014/form/est014FormCabecalhoOS/', 'Cabeçalho', '&tab=geral&T128_Id=' . urlencode($r_T128_Id) . '&acaoId=' .urlencode($r_T128_Id)),
				array(uniqid(), '/est/est014/content/est014FormOrdemServico/', 'Produtos', '&tab=geral&T128_Id=' . urlencode($r_T128_Id)),
			);
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'est014IncluirOS':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$r_T128_Id= isset($r_T128_Id) ? $r_T128_Id : false;

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/form/est014FormCabecalhoOS/', 'Cabeçalho', '&tab=geral&T128_Id=' . urlencode($r_T128_Id) . '&acaoId=' .urlencode($r_T128_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'est014TabOrdemServicoPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$r_T128_Id= isset($r_T128_Id) ? $r_T128_Id : false;

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est014/grid/est014GridOrdemServico/', 'Ordem de Serviço', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
}
?>