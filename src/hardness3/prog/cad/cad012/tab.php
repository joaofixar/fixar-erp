<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: cad012content01
	 */
	case 'cad012tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad012/grid/cad012grid01/', 'Materiais'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

		/**
		 * Form Principal - Cadastro de materiais
		 * Chamado em: cad012content02
		 */
	case 'cad012tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		array(uniqid(), '/cad/cad012/form/cad012form01/', 'Cadastro de Materiais', "&tab=materiais&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;
}
?>