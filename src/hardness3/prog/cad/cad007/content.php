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
	case 'cad007content01':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será divido o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad007/tab/cad007tab01/')
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	/**
	* Form Principal
	* Chamado em: cad007grid01
	*/
	case 'cad007content02':
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
			array($content1, '/cad/cad007/tab/cad007tab02/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'cad007content02Editar':
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
			array($content1, '/cad/cad007/tab/cad007tab02/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'cad007ContentProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '50px');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad007/form/cad007FormItens/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($content2)),
			array($content2, '/cad/cad007/grid/cad007GridItensDocumento/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo  gProcessaAcoes($actions);
	break;
}
?>