<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: cad011content01
	 */
	case 'cad011tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad011/grid/cad011grid01/', 'Competências'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

		/**
		 * Form Principal - Cadastro de competências
		 * Chamado em: cad011content02
		 */
	case 'cad011tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		array(uniqid(), '/cad/cad011/form/cad011form01/', 'Cadastro de Competências', "&tab=competencias&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;
}
?>