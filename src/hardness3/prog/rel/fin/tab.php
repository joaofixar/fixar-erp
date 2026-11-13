<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'demonstrativoDRE':
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/rel/fin/outro/demonstrativoDRE/','Relatorio','&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'dre':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), '/rel/fin/grid/dre/','DRE Comercial', '&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id)),
			array(uniqid(), '/rel/fin/grid/dreFinanceiro/','DRE Financeiro Caixas e Bancos', '&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	}
