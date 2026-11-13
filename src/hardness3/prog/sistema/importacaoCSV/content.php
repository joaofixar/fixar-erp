<?php
namespace hardness;

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'sistemaPImpCSVPrincipal':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Width', '255px');
		$g['smarty']->assign('tipo', '2colunas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/importacaoCSV/tab/sistemaPImpCSVTabelas/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/sistema/importacaoCSV/tab/sistemaPImpCSVAcoes/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'sistemaPImpCSVEnviar':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/importacaoCSV/form/sistemaPImpCSVEnviar/', '&tab=geral'),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'sistemaPImpCSVRecuperar':
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
			array($content1, '/sistema/importacaoCSV/tab/sistemaPImpCSVRecuperar/'),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'sistemaPImpCSVEnviarZIP':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/sistema/importacaoCSV/form/sistemaPImpCSVEnviarZIP/', '&tab=geral'),
		);
		echo gProcessaAcoes($actions);
	break;
}
?>