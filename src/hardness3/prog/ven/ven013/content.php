<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Grid Principal
	 * Chamado em: Menu
	 */
	case 'ven013ContentPrincipal':
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
			array($content1, '/ven/ven013/tab/ven013TabPrincipal/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
	case 'ven013ContentNaoLiberar':
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
			array($content1, '/ven/ven013/tab/ven013TabNaoLiberar/', '&T005_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;
}
?>
