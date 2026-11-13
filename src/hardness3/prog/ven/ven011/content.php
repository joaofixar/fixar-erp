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
	case 'ven011content01':
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
			array($content1, '/ven/ven011/tab/ven011tab01/')
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	/**
	* Form Principal
	* Chamado em: ven011grid01
	*/
	case 'ven011content02':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');	
		if (!isset($r_D106_Id))
			$r_D106_Id=false;
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven011/tab/ven011tab02/', '&acaoId=' . urlencode($r_acaoId). '&D106_Id=' . urlencode($r_D106_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
}
?>