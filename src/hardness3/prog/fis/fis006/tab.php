<?php
namespace hardness;
	/**
	* Weiberlan
	* 28/11/2011
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: fis005Content01
		*/

		case 'fis006TabExportarSintegra':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis006/form/fis006FormExportarSintegra/', 'Exportar Sintegra', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


		case 'fis005TabCadastroCidade':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis005/form/fis005FormCadastroCidade/', 'Cidade', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;		
	}

?>
