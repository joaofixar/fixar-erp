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
	case 'fin006content01':
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
			array($content1, '/fin/fin006/tab/fin006tab01/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	/**
	* Form de comissões
	* Chamado em: fin006grid01
	*/
	case 'fin006content02':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');

		$acaoId;
		if (isset($_REQUEST['acaoId']))
			$acaoId=$_REQUEST['acaoId'];
		else
			$acaoId='';
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin006/tab/fin006tab02/', '&acaoId=' . urlencode($acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	/**
	* totalizar de comissões
	* Chamado em: fin006grid01
	*/
	case 'fin006content03':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');

		$acaoId;
		if (isset($_REQUEST['acaoId']))
			$acaoId=$_REQUEST['acaoId'];
		else
			$acaoId='';
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin006/tab/fin006tab03/', '&acaoId=' . urlencode($acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;	
	
}
?>
