<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/2011
	*/
	case 'cad003ContentRepresentantes':
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
			array($content1, '/cad/cad003/tab/cad003TabRepresentantes/', '&acaoId=' . urlencode($r_acaoId) . '&tab=' . @urlencode($r_tab)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad003ContentCadastroRepresentantes':
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
			array($content1, '/cad/cad003/tab/cad003TabCadastroRepresentantes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$botoesDir .= '<button type="submit" id="cad003FormRepresentantesBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad003ContentRepresentantesUsuarios':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '30px');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// Identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad003/form/cad003FormRepresentantesUsuarios/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/cad/cad003/grid/cad003GridRepresentantesUsuarios/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
}
?>
