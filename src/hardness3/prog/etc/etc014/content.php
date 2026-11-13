<?php
namespace hardness;
	
switch ($g['r'][3]) {
	
	case 'etc014ContentPivot':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/etc/etc014/grid/etc014GridPivot/', "&bi=conf&acaoId=" . urlencode($r_acaoId) . '&divReload=' . urlencode(null))
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	case 'etc014ContentSalvar':
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
			array($content1, '/etc/etc014/tab/etc014TabSalvar/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&fato=' . urlencode($r_fato) . '&fato2=' . urlencode($r_fato2) . '&dimensao1=' . urlencode($r_dimensao1) . '&dimensao1ord=' . urlencode($r_dimensao1ord) . '&dimensao2=' . urlencode($r_dimensao2) . '&dimensao2ord=' . urlencode($r_dimensao2ord) . '&medida=' . urlencode($r_medida) . '&mostrarCurvaABC=' . urlencode($r_mostrarCurvaABC) . '&incluirLinhaVazia=' . urlencode($r_incluirLinhaVazia) . '&incluirColunaVazia=' . urlencode($r_incluirColunaVazia) . '&tipodata=' . urlencode($r_tipodata))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc014ContentPivotRelatorio':
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
			array($content1, '/etc/etc014/grid/etc014GridPivot/', "&bi={$r_bi}&acaoId=" . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload) . '&D132_Id=' . urlencode($r_D132_Id))
		);
		echo gProcessaAcoes($actions);
		
	break;
}
?>