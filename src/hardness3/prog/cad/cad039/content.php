<?php
namespace hardness;
	
switch ($g['r'][3]) {
/**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad039ContentCertificadosDeInspecao':
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
			array($content1, '/cad/cad039/tab/cad039TabCertificadosDeInspecao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad039ContentCertificadosInspecaoIncluir':
		/*
		* Divisão da Area em Conteúdos
		*/
		$D124_D106_Id = (isset($r_D124_D106_Id)) ? $r_D124_D106_Id : false; 
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad039/tab/cad039TabCertificadosDeInspecaoIncluir/', '&acaoId=' . urlencode($r_acaoId) . '&D124_Id=' . urlencode($r_acaoId) . '&D124_D106_Id=' . urlencode($D124_D106_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	case 'cad039ContentAcessorios':
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
		array($content1, '/cad/cad039/form/cad039FormAcessorios/', "&tab=geral&D124_Id=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
		array($content2, '/cad/cad039/grid/cad039GridAcessorios/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
}
?>