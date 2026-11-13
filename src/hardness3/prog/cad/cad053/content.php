<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'cad053ContentEngenharia':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad053/tab/cad053tabEngenharia/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad053contentFuncao':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad053/form/cad053formFuncoes/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
	
	case 'cad053contentMaquina':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		echo  $g['smarty']->fetch('content-full.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad053/form/cad053formMaquinas/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
	break;
}
?>