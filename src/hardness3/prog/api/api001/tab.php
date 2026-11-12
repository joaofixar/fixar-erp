<?php
namespace hardness;
	/**
	* Raquel
	* 21/03/2018
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {
		case 'API001TabAPI':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/api/api001/outro/API001OutroLinksApi/', 'API GET'),
				array(uniqid(), '/api/api001/content/API001ContentConfApi/', 'Integrador'),
				array(uniqid(), '/api/api001/form/API001FormAPI/', 'Exportar CSV', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/api/api001/form/API001FormPolicyAdmin/', 'API REST'),
			);

/* 			if ((int) ($g['usuarioAtual'] ?? 0) === 63) {
				$tabsList[] = array(uniqid(), '/api/api001/form/API001FormPolicyAdmin/', 'API REST');
			} */

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

	case 'API001TabConfApi':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/api/api001/form/API001FormConfApi/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/api/api001/grid/API001GridAPIProcessos/', 'Processo', "&tab=geral&C033_Id=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/api/api001/grid/API001GridAPICallback/', 'Callback', "&tab=geral&C033_Id=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'API001TabApiProcessos':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/api/api001/form/API001FormApiProcessos/', 'Processo', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'API001TabApiCallback':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/api/api001/form/API001FormApiCallback/', 'Callback', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

}

?>
