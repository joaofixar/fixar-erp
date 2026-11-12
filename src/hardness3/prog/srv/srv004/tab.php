<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: cad049Content01
		*/

		case 'srv004TabInspecao':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srv/srv004/grid/srv004GridInspecao/', 'Inspeção de Tocas', "&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		
		break;

	}

?>
