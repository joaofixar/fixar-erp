<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Raquel Pazini
	* @since ...
	*/
	case 'cad062RMA':
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
			array($content1, '/cad/cad062/tab/cad062TabRMA/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad062RMAIncluir':
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
			array($content1, '/cad/cad062/tab/cad062TabCadastroStatusRMA/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'cad062TipoRMAIncluir':
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
			array($content1, '/cad/cad062/tab/cad062TabCadastroTipoRMA/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
}
?>