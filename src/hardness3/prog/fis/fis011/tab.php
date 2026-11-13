<?php
namespace hardness;
	switch ($g['r'][3]) {
		/**
		* Chamado em: fis009Content01
		*/
		case 'fis011TabPrincipal':
			// Definição das Tabs			
			$tabs = array();
			$tabsList = array(				
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/fis/fis011/content/fis011ContentPrincipalSped/', 'SPED Contribuições', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
	}

?>
