<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: cad022Content01
		*/

		case 'cad022TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad022/grid/cad022GridPortadoresReceber/', 'Portadores a Receber', "&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


		case 'cad022TabCadastroPortador':

			// Definição das Tabs
			$formId = uniqid();
			$g['smarty']->assign('formId', $formId);

			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad022/form/cad022FormCadastroPortador/', 'Portador a Receber', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/cad/cad022/form/cad022FormCadastroPortador/', 'PDV', "&tab=PDV&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/cad/cad022/form/cad022FormCadastroPortador/', 'Ecommerce', "&tab=ecommerce&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs, true);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;		
	}

?>
