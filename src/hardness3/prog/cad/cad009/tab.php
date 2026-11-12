<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: cad009content01
	*/
	case 'cad009tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad009/grid/cad009grid01/', 'Listas'),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal - Cadastro catálogo
	* Chamado em: cad009content02
	*/
	case 'cad009tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad009/form/cad009form01/', 'Cadastro de Lista', "&tab=cadastro_catalogo&acaoId=" . urlencode($_REQUEST['acaoId'])),						
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Form Principal - Cadastro itens
	* Chamado em: cad009content02
	*/
	case 'cad009tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad009/grid/cad009grid02/', 'Produtos', "&tab=cadastro_itens&acaoId=" . urlencode($_REQUEST['acaoId'])),						
			array(uniqid(), '/cad/cad009/form/cad009form01/', 'Cadastro de Lista', "&tab=cadastro_catalogo&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	
}
?>