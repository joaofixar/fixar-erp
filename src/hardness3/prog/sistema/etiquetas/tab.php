<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	 case 'sistemaEtiquetasSelecao':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/etiquetas/form/sistemaEtiquetasSelecao/', 'Modelos de Etiqueta', '&tab=geral&D00X_Id=' . $r_D00X_Id),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;
	 
	 case 'sistemaEtiquetasQtdProduto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/etiquetas/form/sistemaEtiquetasQtdProduto/', 'Quantidades', '&tab=geral&D00X_Id=' . $r_D00X_Id),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;
	 
	 case 'etiquetasGenericaXX000':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/etiquetas/form/etiquetasGenericaXX000/', 'Impressão de Etiquetas', '&tab=geral&D00X_Id=' . $r_D00X_Id),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;
	 
	 case 'tabSistemaSelecionarModelo':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/etiquetas/form/formSistemaSelecionarModelo/', 'Selecione o Modelo', '&tab=geral&D00X_Id=' . $r_D00X_Id),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;

}
?>
