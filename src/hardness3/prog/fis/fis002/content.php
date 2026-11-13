<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'fis002ContentICMS':
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
			array($content1, '/fis/fis002/tab/fis002TabIcms/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'fis002ContentICMSIncluir':
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
			array($content1, '/fis/fis002/tab/fis002TabIcmsIncluir/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fis002ContentICMSIncluirExcessao':
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
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
			array($content1, '/fis/fis002/tab/fis002TabIcmsIncluirExcessao/', '&acaoId=' . urlencode($acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fis002ContentICMSTransporte':
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
			array($content1, '/fis/fis002/tab/fis002TabIcmsTransporte/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
