<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {
		case 'cad056TabBancos':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad056/grid/cad056GridBancos/', 'Bancos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
	}

?>
