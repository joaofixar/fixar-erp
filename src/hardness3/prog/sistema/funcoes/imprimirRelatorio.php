<?php
namespace hardness;
switch($g['r'][3]){
	
	// http://flycker/etc/etc015/outro/etc015GerarGrid/?ajax=true&acaoId=1
	
	case 'modoFiltro':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/imprimirRelatorio/modoFiltroTab/', '&grid=' . urlencode($r_grid))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'modoFiltroTab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), $r_grid . '&modoFiltro=true', 'Filtros', '&grid=' . urlencode($r_grid), false, true),
			//array(uniqid(), '/sistema/funcoes/imprimirRelatorio/configuracao/', 'Configurações', '&tab=geral&grid=' . urlencode($r_grid), false, true)

		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'modoGrid':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/funcoes/imprimirRelatorio/modoGridTab/', '&grid=' . urlencode($r_grid))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'modoGridTab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), $r_grid, 'Filtros', '&grid=' . urlencode($r_grid), false, true),
			//array(uniqid(), '/sistema/funcoes/imprimirRelatorio/configuracao/', 'Configurações', '&tab=geral&grid=' . urlencode($r_grid), false, true)

		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

}