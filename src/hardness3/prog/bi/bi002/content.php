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

 		case 'bi002ContentConfBI':
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
			array($content1, '/bi/bi002/tab/bi002TabConfBI/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;
			
		case 'bi002ContentCadastroRelatorio':
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
			array($content1, '/bi/bi002/tab/bi002TabCadastroRelatorio/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'bi002ContentCadastroPerfil':
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
				array($content1, '/bi/bi002/tab/bi002TabCadastroPerfil/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'bi002ContentRelatorios':
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
				array($content1, '/bi/bi002/grid/bi002GridRelatorios/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

	}

?>