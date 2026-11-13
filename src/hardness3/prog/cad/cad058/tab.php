<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad058TabConfGlobal':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if(isset($g['c029Ids'][185]) || $g['usuarioAtual'] == '63'){
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfGeral/', 'Geral', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfCadastros/', 'Cadastros', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfVendas/', 'Vendas', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfEstoque/', 'Estoque', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfFinanceiro/', 'Financeiro', "");
			//$tabsList[] = array(uniqid(), '/cad/cad058/form/cad058FormConfFiscal/', 'Fiscal', "&tab=geral&formId=" . urlencode($formId)),
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfImportacao/', 'Comex', "");
			//$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfMobile/', 'Mobile', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerceTab/', 'Integração', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfMDFe/', 'MDF-e', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfSPEDTab/', 'SPED', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfJobsTab/', 'Jobs', "");
		}

		$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfInterface/', 'Interface', "");

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058ContentConfECommerceTab':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if(isset($g['c029Ids'][185]) || $g['usuarioAtual'] == '63'){
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerce/', 'Geral', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerceMagento1Oauth/', 'Magento Oauth', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerceMagento1Soap/', 'Magento Soap', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerceTray/', 'Tray', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommercePluggto/', 'Pluggto', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfB2B/', 'B2B', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfIntelipost/', 'Intelipost', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfTrello/', 'Trello', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfECommerceNuvemShop/', 'NuvemShop', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfTablet/', 'Tablet', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfCyberlog/', 'Cyberlog', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfMeli/', 'Mercado Livre', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/cad058ContentConfDuimp/', 'Duimp', "");
			$tabsList[] = array(uniqid(), '/cad/cad058/content/openIA/', 'Open IA (GPT)', "");
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	case 'cad058TabConfCadastros':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/content/cad058ContentConfCadastrosProdutos/', 'Produtos', "&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfCadastros/', 'Clientes/Forn.', "&tab=clientesForn&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfCadastrosProdutos':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfCadastros/', 'Geral', "&tab=produtos&formId=" . urlencode($r_formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfCadastros/', 'Custos', "&tab=custos&formId=" . urlencode($r_formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad058TabConfVendas':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Geral', "&tab=geral&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Orçamento', "&tab=orcamento&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Pedido', "&tab=pedido&formId=" . urlencode($formId)),
			//array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Nota Fiscal', "&tab=notaFiscal&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/content/cad058ContentConfNotaFiscal/', 'Nota Fiscal', "&tab=notaFiscal&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'PDV', "&tab=cupomFiscal&formId=" . urlencode($formId)),
			//array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Alçadas', "&tab=alcadaVenda&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/content/cad058ContentConfAlcadas/', 'Alçadas', ""),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Devolução', "&tab=devolucao&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad058TabConfEstoque':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Geral', "&tab=geral&formId=" . urlencode($formId)),
			//array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Estoque', "&tab=estoque&formId=" . urlencode($formId)),			
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Expedição', "&tab=expedicao&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Cotação', "&tab=cotacao&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Ordem de Compra', "&tab=ordemCompra&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Nota de Entrada', "&tab=nfentrada&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'P.C.P.', "&tab=pcp&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Ordem de transferência', "&tab=ordemTransferencia&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfEstoque/', 'Conhecimento', "&tab=conhecimento&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad058TabConfFinanceiro':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Geral', "&tab=geral&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Contas a Receber', "&tab=receber&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Contas a Pagar', "&tab=pagar&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Boleto', "&tab=boleto&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Liberação crédito', "&tab=liberacaoCredito&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfFinanceiro/', 'Configurações de pagamento', "&tab=pagamento&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabGridConfiguracoes':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/grid/cad058GridConfiguracoes/', 'Configurações', "&tab=geral"),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfAlcadas':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Vendedor', "&tab=alcadaVenda&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Supervisor', "&tab=alcadaVendaSupervisor&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Desconto', "&tab=alcadaVendaDesconto&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfEcommerce':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfCadastros/', 'Clientes/Forn.', "&tab=clientesForn&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfNotaFiscal':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'Geral', "&tab=geralNotaFiscal&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'GNRE-ST', "&tab=gnreNotaFiscal&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfVendas/', 'E-mail', "&tab=emailNotaFiscal&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfMDfeGeral':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfMDFe/', 'Geral', "&tab=geral&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfImportacao':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/content/cad058ContentConfImportacaoDi/', 'Declaração de Importação', "&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfImportacaoDi/', 'Compras', "&tab=compras&formId=" . urlencode($r_formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad058TabConfImportacaoDi':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad058/form/cad058FormConfImportacaoDi/', 'Geral', "&tab=geral&formId=" . urlencode($r_formId)),
			array(uniqid(), '/cad/cad058/form/cad058FormConfImportacaoDi/', 'Contas a pagar', "&tab=contasPagar&formId=" . urlencode($r_formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

}

