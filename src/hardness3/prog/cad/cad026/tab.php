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
		* Chamado em: cad026Content01
		*/

		case 'cad026TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad026/grid/cad026GridLinhas/', 'Produtos - Grupos', "&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


		case 'cad026TabCadastroLinha':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad026/form/cad026FormCadastroLinha/', 'Grupos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad026/form/cad026FormCadastroImagem/', 'Imagem', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;		
	}

?>
