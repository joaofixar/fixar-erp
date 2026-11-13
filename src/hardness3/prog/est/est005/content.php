<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		 case 'est005ContentPrincipal':
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
			array($content1, '/est/est005/tab/est005TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est005ContentCadastroConhecimento':
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
			array($content1, '/est/est005/tab/est005TabCadastroConhecimento/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
		break;
		
		case 'est005ContentEditarConhecimento':
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
			array($content1, '/est/est005/tab/est005TabEditarConhecimento/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
		break;
		
		case 'est005FormCadastroNotasRelacionadas':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '100px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
				array($content1, '/est/est005/form/est005FormCadastroNotasRelacionadas/', '&tab=geral&T084_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2)),
				array($content2, '/est/est005/grid/est005GridCadastroNotasRelacionadas/', '&T084_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1))
			);
			echo gProcessaAcoes($actions);
			
		break;
}			
			
?>