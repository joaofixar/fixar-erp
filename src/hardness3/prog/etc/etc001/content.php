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
	case 'etc001_content_urls':
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
			array($content1, '/etc/etc001/tab/etc001_tab_urls/', '&D110_Id=' . urlencode(isset($r_D110_Id) ? $r_D110_Id : ''))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
