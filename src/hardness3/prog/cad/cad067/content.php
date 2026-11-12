<?php
namespace hardness;
/**
* Ações
*/
	switch ($g['r'][3]) {

		case 'cad067ContentPerifericos':
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
			array($content1, '/cad/cad067/tab/cad067TabImpressora/')
			);
			echo gProcessaAcoes($actions);

		break;

		case 'cad067ContentCadastroImpressora':
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
			array($content1, '/cad/cad067/tab/cad067TabCadastroImpressora/', '&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

		break;

	}
?>