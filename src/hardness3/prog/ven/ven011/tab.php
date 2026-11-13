<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: ven011content01
	*/
	case 'ven011tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven011/grid/ven011grid01/', 'Manutenção de bem'),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal - Manutenção de bem
	* Chamado em: ven011content02
	*/
	case 'ven011tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		if (!isset($r_D106_Id))
			$r_D106_Id = false;	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven011/form/ven011form01/', 'Manutenção de Bem', "&tab=manutencao_bem&acaoId=" . urlencode($_REQUEST['acaoId']) . "&D106_Id=" . urlencode($r_D106_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>