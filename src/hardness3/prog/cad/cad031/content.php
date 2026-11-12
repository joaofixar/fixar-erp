<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Andrey Sliwinski
	* @since 16/11/2011
	*/
	case 'cad031ContentEmpresas':
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
			array($content1, '/cad/cad031/tab/cad031TabEmpresas/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'cad031ContentCadastroEmpresas':
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
			array($content1, '/cad/cad031/tab/cad031TabCadastroEmpresas/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad031FormCadastroEmpresasEnviar">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cad031ContentCadastroAvisosIndex':
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
			//array($content1, '/cad/cad031/form/cad031FormAvisosIndex/', '&acaoId=' . urlencode($r_acaoId). '&C004_Id=' . urlencode($r_C004_Id))
			array($content1, '/cad/cad031/tab/cad031TabCadastroAvisosIndex/', '&C004_Id='. urlencode($r_C004_Id) . '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	
	break;

	case 'cad031ContentFormAvisosPerfil':
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
			array($content1, '/cad/cad031/tab/cad031TabFormAvisosPerfil/', '&acaoId=' . urlencode($r_acaoId) . '&C032_Id=' . urlencode($r_C032_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad031ContentAvisosIndex':
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
			//array($content1, '/cad/cad031/form/cad031FormAvisosIndex/', '&acaoId=' . urlencode($r_acaoId). '&C004_Id=' . urlencode($r_C004_Id))
			array($content1, '/cad/cad031/tab/cad031TabAvisosIndex/', '&C004_Id='. urlencode($r_C004_Id) . '&acaoId=' . urlencode($r_acaoId)),

		);
		echo gProcessaAcoes($actions);
	
	break;
}
?>
