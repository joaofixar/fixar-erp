<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author Weiberlan Garcia
	* @since 02/03/2012
	*/
	case 'est011ContentGarantiaConserto':
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
			array($content1, '/est/est011/tab/est011TabPrincipal/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	case 'est011ContentIncluirProduto':
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
			array($content1, '/est/est011/tab/est011TabIncluirProduto/', '&acaoId=' . urlencode($r_acaoId))
		);
		
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est011FormIncluirProduto_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;	

	case 'est011ContentDocumentos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '120px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est011/form/est011FormDocumentos/', "&tab=geral&T182_Id=" . urlencode($r_T182_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/est/est011/grid/est011GridDocumentos/', '&T182_Id=' . urlencode($r_T182_Id) . '&T182_Id=' . urlencode($r_T182_Id) . "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est011ContentGerarRemessa':
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
			array($content1, '/est/est011/tab/est011TabGerarRemessa/', '&acaoId=' . urlencode($r_acaoId))
		);
		
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est011FormGerarRemessa_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;
	
	case 'est011ContentEditarRemessa':
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
			array($content1, '/est/est011/tab/est011TabEditarRemessa/', '&acaoId=' . urlencode($r_acaoId))		
		);

		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnConfirmarTudo">Gerar NFe</button>';
		$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnZerarTudo">Gerar Sem NFe</button>';		
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est011FormCabecalhoRemessa_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;		
	
}
?>
