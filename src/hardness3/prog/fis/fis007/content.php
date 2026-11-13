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

 		case 'fis007ContentExportarXMLNFe':
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
			array($content1, '/fis/fis007/tab/fis007TabExportarXMLNFe/', '&acaoId=' . urlencode($r_acaoId))
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

 		case 'fis007ContentExportarXMLNFeEntrada':
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
			array($content1, '/fis/fis007/form/fis007FormExportarXMLNFeEntrada/', '&tab=geral')
			);
			echo gProcessaAcoes($actions);
		break;	
	}			

?>