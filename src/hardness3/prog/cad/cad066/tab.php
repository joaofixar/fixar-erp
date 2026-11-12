<?php 
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author Lígia Nakazato
	*/	
	case 'cad066TabCadastroSeguradoras':
				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad066/grid/cad066GridCadastroSeguradoras/', 'Seguradoras', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad066/grid/cad066GridSeguradorasInativas/', 'Inativas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad066TabFormCadastrarSeguradoras':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad066/outro/cad066CabecalhoSeguradora/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Form (caso seja o mesmo form em multiplas tabs) // '&formId=' . urlencode($formId)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		if($r_acao == 'novo'){		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/cad/cad066/form/cad066FormCadastrarSeguradoras/', 'Seguradora', "&tab=seguradora&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			);
		}else{
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			array(uniqid(), '/cad/cad066/form/cad066FormCadastrarSeguradoras/', 'Cabeçalho', "&tab=seguradora&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			//array(uniqid(), '/cad/cad066/grid/gridCadastroApolices/', 'Apólices', "&tab=apolice&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			);
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad066TabFormCadastrarApolices':
		// Form (caso seja o mesmo form em multiplas tabs) // '&formId=' . urlencode($formId)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad066/form/cad066FormCadastrarApolices/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/cad/cad066/content/cad066ContentGridCadastrarAverbacoes/', 'Averbações', "&tab=geral&D173_Id=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>