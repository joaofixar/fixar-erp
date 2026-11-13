<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {
		case 'cad057TabImpressoras':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad057/grid/cad057GridImpressoras/', 'Impressoras', "&tab=geral"),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
	}

?>
