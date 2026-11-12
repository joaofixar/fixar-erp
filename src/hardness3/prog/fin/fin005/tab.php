<?php
namespace hardness;
	/**
	* FIN - FIN005 - Boletos Bancários
	* Weiberlan
	* 17/11/2011
	*/
	switch($g['r'][3]) {
		case 'fin005TabPrincipal':
			// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin005/grid/fin005GridPrincipal/', 'Boleto Bancário', '&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'fin005TabCadastroBoleto':
			// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin005/form/fin005FormCadastro/', 'Cadastro de Boleto', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&T002_Id=' . urlencode($r_T002_Id)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'fin005TabInfoBoleto':
			// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin005/grid/fin005GridTitulos/', 'Títulos', '&acaoId=' . urlencode($r_acaoId) . '&T002_Id=' . urlencode($r_T002_Id)),
			array(uniqid(), '/fin/fin005/grid/fin005GridHistorico/', 'Histórico', '&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'fin005TabBoletosParaEnvio':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/fin/fin005/grid/fin005GridBoletosParaEnvio/', 'Boletos', '&extra=' . urlencode($r_extra)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}
?>
