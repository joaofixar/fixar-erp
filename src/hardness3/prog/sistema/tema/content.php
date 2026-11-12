<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'temaContentAdd':
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
		array($content1, '/sistema/tema/tab/temaTabAdd/')
		);
		echo gProcessaAcoes($actions);

		break;
}
?>
