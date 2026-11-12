<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: ven009content01
	*/
	case 'ven009tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven009/grid/ven009grid01/', 'Logística de Locação'),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Form Principal - Cadastro bem
	* Chamado em: ven009content02
	*/
	case 'ven009tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		if (!isset($r_D106_Id))
			$r_D106_Id=false;
		if (!isset($r_T141_Id))
			$r_T141_Id=false;	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven009/form/ven009form01/', 'Informações do registro', "&tab=logistica_aluguel&acaoId=" . urlencode($_REQUEST['acaoId'])."&D106_Id=" . urlencode($r_D106_Id)."&T141_Id=" . urlencode($r_T141_Id)),					
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	
	
}
?>