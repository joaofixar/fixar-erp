<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc014TabListar':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc014/grid/etc014GridSave/', 'Relatórios Salvos', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'etc010TabPivot':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc014/content/etc014ContentPivotRelatorio/', 'Visualização', '&bi=visu&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&D132_Id=' . @urlencode($r_D132_Id)),
			array(uniqid(), '/etc/etc014/content/etc014ContentPivotRelatorio/', 'Criação de Relatórios', '&bi=conf&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&D132_Id=' . @urlencode($r_D132_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'etc014TabSalvar':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc014/form/etc014FormSalvar/', 'Relatório', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&fato=' . urlencode($r_fato) . '&fato2=' . urlencode($r_fato2) . '&dimensao1=' . urlencode($r_dimensao1) . '&dimensao1ord=' . urlencode($r_dimensao1ord) . '&dimensao2=' . urlencode($r_dimensao2) . '&dimensao2ord=' . urlencode($r_dimensao2ord) . '&medida=' . urlencode($r_medida) . '&mostrarCurvaABC=' . urlencode($r_mostrarCurvaABC) . '&incluirLinhaVazia=' . urlencode($r_incluirLinhaVazia) . '&incluirColunaVazia=' . urlencode($r_incluirColunaVazia) . '&tipodata=' . urlencode($r_tipodata)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>
