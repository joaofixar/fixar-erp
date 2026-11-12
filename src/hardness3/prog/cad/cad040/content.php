<?php
namespace hardness;
/**
 * Weiberlan
 * 07/12/2011
 */
switch ($g['r'][3]) {
	
	case 'cad040ContentCertificadoConformidade':
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
			array($content1, '/cad/cad040/tab/cad040TabCertificadoConformidade/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad040ContentCadastroCertificadoConformidade':
		$D106_Id = (isset($r_D106_Id)) ? $r_D106_Id : false;
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
			array($content1, '/cad/cad040/tab/cad040TabCadastroCertificadoConformidade/', '&acaoId=' . urlencode($r_acaoId). '&D106_Id='. urlencode($D106_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad040ContentAcessorios':
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
		array($content1, '/cad/cad040/form/cad040FormAcessorios/', "&tab=geral&D125_Id=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
		array($content2, '/cad/cad040/grid/cad040GridAcessorios/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
}
?>
