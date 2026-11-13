<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Ricardo Norio Miyata
	* @since ...
	*/
	case 'cad063ContentCadastroClienteGrupo':
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
			array($content1, '/cad/cad063/tab/cad063TabCadastroClienteGrupo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* @author Ricardo Norio Miyata
	* @since ...
	*/
	case 'cad063CadastroClienteGrupoCadastro':
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
			array($content1, '/cad/cad063/tab/cad063TabCadastroCadastroClienteGrupo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
?>