<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'fis003TabNcm':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		global $g;
		
		// Retirada verificação dia 11/03/2019, pois essas tabs serão habilitadas para todos os usuários
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis003/grid/fis003GridNcm/', 'Regras NCM desta empresa', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/fis/fis003/grid/fis003GridD005/', 'Cadastro NCM - Vale para todas empresas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
	

		$tabs = array();

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'fis003ContentNCMIncluir':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis003/form/fis003FormNCM/', 'NCM', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
			==============================================================*/
			array(uniqid(), '/fis/fis003/form/fis003FormNCM/', 'Reforma', '&tab=reforma&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			/* ============================================================== */
			//array(uniqid(), '/fis/fis003/form/fis003FormImportacao/', 'Importação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fis003ContentNCMAlterar':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/fis/fis003/outro/fis003CabecalhoNCM/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis003/form/fis003FormNCMAlterar/', 'NCM', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/fis/fis003/form/fis003FormImportacao/', 'Importação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>