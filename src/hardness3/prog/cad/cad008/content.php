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
	case 'cad008content01':
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
		array($content1, '/cad/cad008/tab/cad008tab01/')
		);
		echo gProcessaAcoes($actions);

		break;

		/**
		 * Form Principal
		 * Chamado em: cad007grid01
		 */
	case 'cad008content02':
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
		array($content1, '/cad/cad008/tab/cad008tab02/', '&acaoId=' . urlencode($r_acaoId)),
		array($content2, '/cad/cad008/tab/cad008tab03/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		break;

	case 'cad008content03':
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
		array($content1, '/cad/cad008/tab/cad008tab02/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

		break;
}
?>