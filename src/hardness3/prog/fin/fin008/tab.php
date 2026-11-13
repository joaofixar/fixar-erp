<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: fin007content01
	*/
	case 'fin008tabPrincipalFluxoCaixa':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin008/form/fin008FormFluxoCaixa/', 'Fluxo de Caixa', '&tab=geral'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin008tabBalancete':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin008/form/fin008FormBalancete/', 'Balancete', '&tab=geral'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	case 'fin008tabComissoes':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin008/form/fin008FormComissoes/', 'Comissões', '&tab=geral'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin008tabMapaVendas':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin008/form/fin008FormMapaVendas/', 'Mapa de Vendas', '&tab=geral'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'fin008tabDRE':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin008/form/fin008FormDRE/', 'DRE', '&tab=geral'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

}
?>