<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est013TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est013/grid/est013GridFrequencia/', 'Frequência'),
			array(uniqid(), '/est/est013/grid/est013GridSobras/', 'Sobras'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
}
?>