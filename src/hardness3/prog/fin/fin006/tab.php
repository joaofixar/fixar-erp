<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: fin006content01
	*/
	case 'fin006tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin006/grid/fin006grid01/', 'Comissões de Venda'),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Form meta comissões
	* Chamado em: fin006content02
	*/
	case 'fin006tab02':
		
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin006/form/fin006form01/', 'Comissões', "&tab=comissoes&acaoId=" . urlencode($_REQUEST['acaoId'])),						
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	
	
	/**
	* Form totalizar comissões
	* Chamado em: fin006content03
	*/
	case 'fin006tab03':
		
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin006/form/fin006form02/', 'Totalizar Comissões', "&tab=totalizar_comissoes&acaoId=" . urlencode($_REQUEST['acaoId'])),						
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	

}
?>