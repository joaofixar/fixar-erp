<?php
namespace hardness;
	/**
	* @author RubiaYwata
	* @since 18/11/2011
	*/
	
	
	switch ($g['r'][3]) {

		case 'fis004ContentNcmPorGrupo':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis004/tab/fis004TabNcmPorGrupo/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'fis004ContentNCMporGrupoIncluir':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/fis/fis004/tab/fis004TabNcmPorGrupoIncluir/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);		
			
		break;
	}
?>