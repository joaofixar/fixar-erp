<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'fin003TabContent1':
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin003/grid/fin003GridBancos/', 'Bancos', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabContent2':
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin003/grid/fin003GridLancamentos/', 'Lançamentos', '&D007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload)),
			array(uniqid(), '/fin/fin003/content/fin003ContentExtratos/', 'Extratos', '&D007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload)),
		);

		// Processa Tabs
		echo  gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabLancamento':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/form/fin003FormLancamentos/', 'Lançamento', '&acaoId=' . urlencode($r_acaoId))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabEditaLancamento':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/form/fin003FormLancamentos/', 'Lançamentos', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D007_Id=' . urlencode($r_D007_Id))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'fin003TabGeral':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/content/fin003Content01/', 'Caixas e Bancos', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']))
		);
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabTransacoes':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/grid/fin003GridTransacoes/', 'Extrato', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true),
			array(uniqid(), '/fin/fin003/grid/fin003GridRegrasExtrato/', 'Regras', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true),
			array(uniqid(), '/fin/fin003/grid/fin003GridTransacoesExcluidas/', 'Excluídos', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true),
		);
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabDesmembrarTransacao':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/fin/fin003/outro/fin003CabecalhoTransacao/', '&T216_Id=' . urlencode($r_T216_Id), true),
		);
		echo gProcessaAcoes($actions);

		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/grid/fin003GridDesmembrarTransacao/', 'Títulos a Pagar', "&tab=geral&T216_Id=" . urlencode($r_T216_Id) . '&divIdRefresh=' . urlencode($r_divIdRefresh)),
			array(uniqid(), '/fin/fin003/grid/fin003GridDesmembrarTransacaoRecebida/', 'Títulos a Receber', "&tab=geral&T216_Id=" . urlencode($r_T216_Id) . '&divIdRefresh=' . urlencode($r_divIdRefresh))
		);

		$sqlT216 = mysql_query("SELECT T216_Valor FROM T216 WHERE T216_Id = '{$r_T216_Id}'");
		$rowT216 = mysql_fetch_array($sqlT216);
		if($rowT216['T216_Valor'] > 0){
			$tabsList = array_reverse($tabsList);
		}
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin003TabEditarTransacao':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin003/form/fin003FormEditarTransacao/', 'Transação', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}