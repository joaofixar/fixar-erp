<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc015Reports':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc015/grid/etc015Relatorios/', 'Relatórios Salvos', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/etc/etc015/content/etc015CriarRelatorio/', 'Criar Relatórios', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'etc015VerRelatorio':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc015/outro/etc015GerarGrid/', 'Relatório', "&acaoId=" . urlencode($r_acaoId) . '&sql=' . urlencode(@$r_sql) . '&personalizar=' . urlencode(@$r_personalizar)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>
