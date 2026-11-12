<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Grid Principal
	*/
	case 'fin009tabProjecaoOrcamentaria':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin009/grid/fin009GridProjecaoOrcamentaria/', 'Projeção Orçamentária', '&tab=geral'),
			array(uniqid(), '/fin/fin009/grid/fin009GridProjecaoOrcamentariaGrupo/', 'Por grupo', '&tab=geral'),
			array(uniqid(), '/fin/fin009/grid/fin009GridProjecaoOrcamentariaMes/', 'Por mês', '&tab=geral'),
			array(uniqid(), '/fin/fin009/grid/fin009GridLancamentos/', 'Lançamentos', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}