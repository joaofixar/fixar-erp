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
	case 'ven010content01':
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
			array($content1, '/ven/ven010/tab/ven010tab01/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	/**
	* Form Principal
	* Chamado em: ven010grid01
	*/
	case 'ven010content02':

		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');		
		// O que será carregado em cada conteúdo?

		if (!isset($r_readOnly))
			$r_readOnly=false;

		if (!isset($r_mostraGerar))
			$r_mostraGerar=false;			

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven010/tab/ven010tab02/', '&acaoId=' . urlencode($r_acaoId) . '&readOnly='.$r_readOnly . '&mostraGerar='.$r_mostraGerar),
		);
		echo gProcessaAcoes($actions);	

	break;
	
	case 'ven010content03':
		/**
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');		
		// O que será carregado em cada conteúdo?

		if (!isset($r_readOnly))
			$r_readOnly=false;	

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven010/tab/ven010tab04/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'ven010ContentBens':
		/**
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');		
		// O que será carregado em cada conteúdo?

		if (!isset($r_readOnly))
			$r_readOnly=false;	

		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven010/form/ven010FormBens/', '&tab=geral&T141_Id=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven010/grid/ven010GridBens/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
		
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>