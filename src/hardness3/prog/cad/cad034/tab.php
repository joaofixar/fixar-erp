<?php
namespace hardness;

switch ($g['r'][3]) {
	 /**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad034TabPlanoDeContas':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/tab/cad034TabPlanoDeContasContas/', 'Contas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/tab/cad034TabPlanoDeContasGrupos/', 'Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/tab/cad034TabPlanoDeContasSubGrupos/', 'Sub-Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/cad/cad034/grid/cad034GridSubGruposPlanoContas/', 'Sub-Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad034TabPlanoDeContasContas':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/grid/cad034GridPlanoDeContas/', 'Ativo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/grid/cad034GridPlanoDeContasInativo/', 'Inativo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/content/cad034ImportarCSV/', 'Importar CSV'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad034TabPlanoDeContasGrupos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/grid/cad034GridGruposPlanoContas/', 'Ativo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/grid/cad034GridGruposPlanoContasInativo/', 'Inativo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/content/cad034ImportarCSVGrupos/', 'Importar CSV'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad034TabPlanoDeContasSubGrupos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/grid/cad034GridSubGruposPlanoContas/', 'Sub-Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad034/content/cad034ImportarCSVSubGrupos/', 'Importar CSV'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad034TabPlanoDeContasIncluir':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$D014 = mysql_fetch_array(mysql_query("SELECT * FROM D014 WHERE D014_Id = '{$r_acaoId}'"));
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if($r_acaoId > 0){
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad034/outro/cad034CabecalhoPlanoDeContas/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		
		// Definição das Tabs
		$tabs = array();
		if(empty($r_acaoId)){
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad034/form/cad034FormPlanoDeContas/', 'Plano de Contas', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D032_Id=' . urlencode($r_D032_Id)),
				//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
			);
		}else{
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad034/grid/cad034GridProjecaoOrcamentaria/', 'Projeção orçamentária', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
				array(uniqid(), '/cad/cad034/form/cad034FormPlanoDeContas/', 'Cadastro', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			if($D014['D014_Flag_Tipo'] == 'PG'){
				$tabsList[] = array(uniqid(), '/cad/cad034/grid/cad034GridContasPagar/', 'Contas a pagar', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			} else if($D014['D014_Flag_Tipo'] == 'RC'){
				$tabsList[] = array(uniqid(), '/cad/cad034/grid/cad034GridContasReceber/', 'Contas a receber', '&tab=geral&acaoId=' . urlencode($r_acaoId));
			}
			$tabsList[] = array(uniqid(), '/cad/cad034/grid/cad034GridLancamentos/', 'Lançamentos bancários', '&acaoId=' . urlencode($r_acaoId));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad034TabPlanoDeContasIncluirGrupo':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/form/cad034FormPlanoDeContasGrupo/', 'Grupo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad034TabIncluirProjecao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/form/cad034FormProjecao/', 'Cadastro', '&tab=geral&T014_Id=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad034TabPlanoDeContasIncluirSubGrupo':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad034/form/cad034FormPlanoDeContasSubGrupo/', 'Sub-Grupo', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
