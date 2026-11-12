<?php
namespace hardness;
  
switch ($g['r'][3]) {
  case 'cad014TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad014/grid/cad014GridPrincipal/', 'Contas', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad014/grid/cad014GridContasInativas/', 'Inativas', '&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad014TabEditarConta':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad014/form/cad014FormPrincipal/', 'Cadastro', '&tab=bancario&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad014/form/cad014FormPrincipal/', 'Configuração', '&tab=emissao&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad014contentCadastroBanco':
		// Definição das Tabs
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad014/form/cad014FormCadastroBanco/', 'Banco', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
}
?>
