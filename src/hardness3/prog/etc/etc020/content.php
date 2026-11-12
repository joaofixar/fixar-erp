<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc020ContentEnviarEmail':
		global $g;
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
			array($content1, '/etc/etc020/tab/etc020TabEnviarEmail/', $g['repassarParametros'])
		);
		echo gProcessaAcoes($actions);
	break;
}
