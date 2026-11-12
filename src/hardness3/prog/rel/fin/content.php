<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'demonstrativoDRE':
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
			array($content1, '/rel/fin/tab/demonstrativoDRE/','&acaoId=' . urlencode($r_acaoId) . '&C004_Id=' . urlencode($r_C004_Id) . '&tab=' . @urlencode($r_tab))
		);
		echo  gProcessaAcoes($actions);
	break;
	
	
}