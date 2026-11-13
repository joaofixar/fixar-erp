<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'etc017TabTarefas':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc017/grid/etc017GridTarefas/', 'Atividades', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/etc/etc017/grid/etc017GridLixeira/', 'Lixeira', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc017TabTarefasCliente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc017/grid/etc017GridTarefasCliente/', 'Atividades', '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/etc/etc017/grid/etc017GridLixeira/', 'Lixeira', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc017TabTarefa':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 34px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/etc/etc017/outro/etc017CabecalhoTarefa/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc017/content/etc017ContentAtividades/', 'Atividades', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'etc017TabLeituraAgenda':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc017/outro/etc017LeituraAgenda/', 'Registro de contato', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
