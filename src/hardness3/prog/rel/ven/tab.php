<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'prazoMedio':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/rel/ven/grid/analiticoPrazoMedioRecebimento/','PMV - Recebimento', '&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id)),
			array(uniqid(), '/rel/ven/grid/analiticoPrazoMedioVencimento/','PMV - Vencimento', '&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


	}
