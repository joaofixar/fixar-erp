<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'crm005TabProdutos':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm005/form/crm005FormProduto/', 'Produto', '&tab=produto&acaoId=' . urlencode($r_acaoId) . '&T139_Id=' . urlencode(@$r_T139_Id). '&D024_Id_CRM='.urlencode(@$r_D024_Id_CRM)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
	
	case 'crm005PrePedido':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm005/content/crm005ContentPrePedido/', 'Pré Pedido', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'crm005tabResumo':
		// Form (caso seja o mesmo form em multiplas tabs)
		$data = (isset($r_data)) ? $r_data : false;
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			//array(uniqid(), '/crm/crm005/grid/crm005GridProdutoCliente/', 'Produto/Cliente', '&acaoId=' . urlencode($r_acaoId) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridProduto/', 'Produto', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridTipo/', 'Tipo', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridProdutoTurno/', 'Produto/Turno', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridMpTurno/', 'M.P./Turno', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridVendedor/', 'Vendedor', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			//array(uniqid(), '/crm/crm005/grid/crm005GridVendedorCliente/', 'Vendedor/Cliente', '&acaoId=' . urlencode($r_acaoId) . '&data=' . urlencode($data)),
			//array(uniqid(), '/crm/crm005/grid/crm005GridCliente/', 'Cliente', '&acaoId=' . urlencode($r_acaoId) . '&data=' . urlencode($data)),
			array(uniqid(), '/crm/crm005/grid/crm005GridHora/', 'Hora', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm005TabProgramacao':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm005/grid/crm005GridProgramacao/', 'Resumo', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($r_data)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'crm005TabPrePedidoEmpresas':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm005/grid/crm005GridPrePedidoEmpresas/', 'Empresas', '&data=' . urlencode($r_data) . '&divRefresh=' . urlencode($r_divRefresh)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm005TabSelecionarVendedor':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm005/form/crm005FormSelecionarVendedor/', 'Vendedor', '&tab=geral&data=' . urlencode($r_data) . '&D024_Id=' . urlencode($r_D024_Id) . '&Flag_ACP=' . urlencode($r_Flag_ACP) . '&acaoId=' . urlencode($r_D024_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>