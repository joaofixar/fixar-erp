<?php
namespace hardness;
/**
 * Weiberlan
 * 07/12/2011
 */
switch ($g['r'][3]) {
	
	case 'cad035ContentReciboDeLocacoes':
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
			array($content1, '/cad/cad035/tab/cad035TabReciboDeLocacoes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad035ContentReciboDeLocacoesIncluir':
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
			array($content1, '/cad/cad035/tab/cad035TabCadastroReciboDeLocacoes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'cad035ContentBens':
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
			array($content1, '/cad/cad035/form/cad035FormBens/', "&tab=geral&T156_Id=" . urlencode($r_T156_Id)  .  "&divReload=" . urlencode($content2)),
			array($content2, '/cad/cad035/grid/cad035GridBens/', '&acaoId=' . urlencode($r_T156_Id) .  "&divReload=" . urlencode($content1))
		);
		echo gProcessaAcoes($actions);

	break;
	
}
?>
