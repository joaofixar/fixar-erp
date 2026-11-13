<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad053tabEngenharia':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad053/grid/cad053gridMaquinas/', 'Máquinas', ''),
			array(uniqid(), '/cad/cad053/grid/cad053gridFuncoes/', 'Funções', ''),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>
