<?php
namespace hardness;

switch ($g['r'][3]) {	
	case 'cont001TabPrincipal':		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cont/cont001/grid/cont001GridContas/', 'Plano contas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cont/cont002/grid/cont002GridLancamentos/', 'Lançamentos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cont/cont003/grid/cont003GridSaldos/', 'Saldos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cont/cont002/grid/cont002GridEscrituracao/', 'Escrituração', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);	
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	

	case 'cont001TabCadastroConta':				
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cont/cont001/form/cont001FormConta/', 'Conta', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
