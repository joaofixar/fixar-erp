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

 		case 'fis005ContentPrincipal':
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
			array($content1, '/fis/fis005/tab/fis005TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;
			
		case 'fis005contentCadastroCidade':
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
			array($content1, '/fis/fis005/tab/fis005TabCadastroCidade/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;
			
		case 'fis005contentCadastroCidadeEdicao':
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
			array($content1, '/fis/fis005/tab/fis005TabCadastroCidadeEdicao/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;

		case 'fis005contentCadastroCep':
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
			array($content1, '/fis/fis005/tab/fis005TabCadastroCep/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;
			
		case 'fis005contentCadastroCepEdicao':
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
			array($content1, '/fis/fis005/tab/fis005TabCadastroCepEdicao/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;	

		case 'fis005contentCadastroBairro':
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
			array($content1, '/fis/fis005/tab/fis005TabCadastroBairro/', '&idCidade=' . urlencode($r_idCidade) . '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			break;				

 
			
	}			

?>