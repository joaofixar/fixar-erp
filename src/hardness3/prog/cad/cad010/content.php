<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Grid Principal
	 * Chamado em: Menu
	 */
	case 'cad010content01':
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
		array($content1, '/cad/cad010/tab/cad010tab01/')
		);
		echo gProcessaAcoes($actions);

		break;

		/**
		 * Form Principal
		 * Chamado em: cad007grid01
		 */
	case 'cad010content02':
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
		array($content1, '/cad/cad010/tab/cad010tab02/', '&typeForm=' . urlencode($r_typeForm) . '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		break;
		
	 case 'cad010content03':
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
		array($content1, '/cad/cad010/tab/cad010tab03/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		break;
		
	 case 'cad010ContentDocumentos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/cad/cad010/form/cad010FormDocumentos/', '&tab=geral&D109_Id=' . urlencode($r_D109_Id). '&divReload=' . urlencode($content2)),
		array($content2, '/cad/cad010/grid/cad010GridDocumentos/', '&acaoId=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		break;
		
		case 'cad010ContentCompetencias':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/cad/cad010/form/cad010form02/', "&tab=competencias&acaoId=" . urlencode($_REQUEST['acaoId']). '&divReload=' . urlencode($content2)),
		array($content2, '/cad/cad010/grid/cad010grid02/', '&acaoId=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		break;
		
		case 'cad010ContentCadastroDigital':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			echo  $g['smarty']->fetch('content-full.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($content1, '/cad/cad010/tab/cad010TabCadastroDigital', '&acaoId=' . urlencode($r_acaoId))
			);
			echo  gProcessaAcoes($actions);

		break;
}
?>