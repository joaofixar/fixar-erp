<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Andrey Sliwinski
	* @since 16/11/2011
	*/
	case 'cad028ContentSubGrupos':
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
			array($content1, '/cad/cad028/tab/cad028TabSubGrupos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad028ContentCadastroSubGrupos':
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
			array($content1, '/cad/cad028/tab/cad028TabCadastroSubGrupos/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
}
?>
