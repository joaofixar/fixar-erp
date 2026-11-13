<?php
namespace hardness;
  /**
 * Weiberlan
 * 07/12/2011
 */
switch ($g['r'][3]) {
	
	case 'cad035TabReciboDeLocacoes':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad035/grid/cad035GridReciboDeLocacoes/', 'Recibos de Locações', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad035TabCadastroReciboDeLocacoes':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		if(empty($r_acaoId)){
		$tabsList = array(
			array(uniqid(), '/cad/cad035/form/cad035FormReciboDeLocacoes/', 'Cadastro', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		}else{
		$tabsList = array(
			array(uniqid(), '/cad/cad035/content/cad035ContentBens/', 'Bens', '&tab=geral&acaoId=' . urlencode($r_acaoId) . "&T156_Id=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad035/form/cad035FormReciboDeLocacoes/', 'Recibo de Locação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>