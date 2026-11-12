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
		* Chamado em: cad049Content01
		*/

		case 'cad049TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad049/grid/cad049GridCoresVeiculo/', 'Cores Veículos', "&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


		case 'cad049TabCadastroCor':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad049/form/cad049FormCadastroCor/', 'Cor Veículo', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;		
	}

?>
