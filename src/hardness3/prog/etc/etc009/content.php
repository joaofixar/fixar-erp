<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Andrey Sliwinski
	* @since 04/01/2012
	*/
	case 'etc009ContentContadoresClientes':
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
			array($content1, '/etc/etc009/tab/etc009TabContadoresClientes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'etc009ContentFormCotadorClientesIncluir':
		
		$acao_Id =(isset($r_acaoId)) ? $r_acaoId : false;
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
			array($content1, '/etc/etc009/tab/etc009TabFormContadoresClientes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
}
?>
