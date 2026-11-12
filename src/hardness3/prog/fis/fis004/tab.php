<?php
namespace hardness;
	/**
	* @author RubiaYwata
	* @since 18/11/2011
	*/
	
	switch ($g['r'][3]) {
		
		case 'fis004TabNcmPorGrupo':

			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/fis/fis004/grid/fis004GridGrupo/', 'Grupo/Aplicação/Classificação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			
			break;
		
		case 'fis004TabNcmPorGrupoIncluir':
		
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/fis/fis004/form/fis004FormGrupo/', 'Grupo/Aplicação/Classificação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');		
		break;
	}
?>