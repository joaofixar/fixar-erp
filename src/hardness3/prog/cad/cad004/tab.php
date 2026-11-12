<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'tabGrid':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad004/grid/transportadoras/', 'Transportadoras'),
			array(uniqid(), '/cad/cad004/grid/transportadorasEcommerce/', 'Transportadoras ecommerce'),
			array(uniqid(), '/cad/cad004/grid/transportadorasInativas/', 'Transportadoras Inativas'),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabForm1':
		// Form (caso seja o mesmo form em multiplas tabs, senão comente as duas linhas)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad004/form/form1/', 'Geral', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad004/form/form1/', 'Mais', "&tab=mais&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad004/form/form1/', 'Frete', "&tab=frete&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'tabform2':
		// Form (caso seja o mesmo form em multiplas tabs, senão comente as duas linhas)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad004/form/form2/', 'Geral', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
}
?>