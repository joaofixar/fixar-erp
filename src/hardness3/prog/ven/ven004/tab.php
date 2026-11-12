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
			array(uniqid(), '/ven/ven004/grid/orcamentos/', 'Orçamentos'),
			array(uniqid(), '/ven/ven004/grid/pedidos/', 'Pedidos'),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'tabForm1':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), '/fis/fis001/form/form1/', 'Geral', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			//array(uniqid(), '/fis/fis001/form/form1/', 'Estoque', "&tab=estoque&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'Financeiro', "&tab=financeiro&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'Retorno', "&tab=retorno&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'ICMS', "&tab=icms&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'IPI', "&tab=ipi&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'ST', "&tab=st&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'PIS', "&tab=pis&acaoId=" . urlencode($_REQUEST['acaoId'])),
			//array(uniqid(), '/fis/fis001/form/form1/', 'Cofins', "&tab=cofins&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
}
?>