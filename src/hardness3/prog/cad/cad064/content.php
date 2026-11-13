<?php
namespace hardness;
	
switch ($g['r'][3]) {
	case 'cad064ContentCadastroVeiculos':
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
			array($content1, '/cad/cad064/tab/cad064TabCadastroVeiculos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad064ContentFormCadastrarVeiculos':
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
			array($content1, '/cad/cad064/tab/cad064TabFormCadastrarVeiculos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>