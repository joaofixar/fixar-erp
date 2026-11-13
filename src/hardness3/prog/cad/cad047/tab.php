<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 03/01/12
	*/
	case 'cad047TabTransportesTipos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad047/grid/cad047GridTransportesTipos/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad047TabCadastroTransportesTipos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			// array(uniqid(), '/cad/cad047/form/cad047FormCadastroTransportesTipos/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			// array(uniqid(), '/cad/cad047/form/cad047FormCadastroTransportesTipos/', 'Ecommerce', '&tab=ecommerce&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad047/form/cad047FormCadastroTransportesTipos/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/cad/cad047/form/cad047FormCadastroTransportesTipos/', 'Ecommerce', '&tab=ecommerce&acaoId=' . urlencode($r_acaoId) . "&formId=" . urlencode($formId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>
