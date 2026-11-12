<?php
namespace hardness;

switch ($g['r'][3]) {	
	case 'cont002TabCadastroLancamento':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cont/cont002/form/cont002FormLancamento/', 'Lançamentos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),		
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cont002TabProcessarLancamento':				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cont/cont002/form/cont002FormProcessarLancamento/', 'Processar Lançamento', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cont002TabCadastroEscrituracao':				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cont/cont002/form/cont002FormCadastroEscrituracao/', 'Escrituracao', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		if(!empty($r_acaoId))
			$tabsList[] = array(uniqid(),  '/cont/cont002/grid/cont002GridCorrelacoes/', 'Correlações', "&tab=geral&acaoId=" . urlencode($r_acaoId));			
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cont002TabCadastroCorrelacao':				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cont/cont002/form/cont002FormCadastroCorrelacao/', 'Correlação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
