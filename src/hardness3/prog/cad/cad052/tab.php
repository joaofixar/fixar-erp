<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad052TabGeral':
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad052/outro/cad052Cabecalho/', '&D001_Id=' . urlencode($r_D001_Id), true),
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad052/grid/cad052gridComposicao/', 'Composição', "&D001_Id=" . urlencode($r_D001_Id)),
			array(uniqid(), '/cad/cad052/grid/cad052gridProcessos/', 'Processos', "&D001_Id=" . urlencode($r_D001_Id)),
			array(uniqid(), '/cad/cad052/outro/cad052Demonstracao/', 'Demonstração Custo', "&D001_Id=" . urlencode($r_D001_Id)),
			array(uniqid(), '/cad/cad052/form/cad052formProduto/', 'Outro', "&tab=geral&acaoId=" . urlencode($r_D001_Id)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad052tabProcesso':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad052/grid/cad052gridProcessos/', 'Processos', "&D001_Id=" . urlencode($r_D001_Id)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs,true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad052tabConsumiveis':
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad052/grid/cad052gridConsumiveis/', 'Consumiveis', "&D001_Id=" . urlencode($r_D001_Id) . '&D075_Id=' . urlencode($r_D075_Id)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs,true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>
