<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/2011
	*/
	case 'cad051ContentPrincipal':
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
			array($content1, '/cad/cad051/tab/cad051TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad051ContentCadastroModelo':
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
			array($content1, '/cad/cad051/tab/cad051TabCadastroModelo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad051ContentImpressaoGenerica':
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
			array($content1, '/cad/cad051/tab/cad051TabImpressaGenerica/', '&acaoId=' . urlencode($r_acaoId) . '&print=' . (isset($r_print) ? $r_print : ''))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad051SelecionarModelo':
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
			array($content1, '/cad/cad051/tab/cad051SelecionarModelo/', '&D004_Id=' . (isset($r_D004_Id) ? $r_D004_Id : '') . '&D009_Id=' . (isset($r_D009_Id) ? $r_D009_Id : '') . '&D024_Id=' . (isset($r_D024_Id) ? $r_D024_Id : ''))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
