<?php
namespace hardness;
/**
 * Ações
 */

switch ($g['r'][3]) {
	case 'crm005PrePedido':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/crm/crm005/tab/crm005PrePedido/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urldecode($g['divId'])),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'crm005ContentProduto':		
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$content3 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content3', $content3);
		$g['smarty']->assign('content1Height', '30px');
		$g['smarty']->assign('content2Height', '35px');
		$g['smarty']->assign('content3Height', '90%');
		$g['smarty']->display('content-3rows.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/crm/crm005/outro/crm005Data/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urldecode($g['divId']) . '&data=' . urlencode($data)),
			array($content3, '/crm/crm005/grid/crm005GridPrePedidoCRM/', '&T099_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1) . '&D024_Id=' . urlencode(@$r_D024_Id) . '&divContent=' . urldecode($g['divId']) . '&data=' . urlencode($data)),
			array($content2, '/crm/crm005/form/crm005FormProdutosCRM/', '&tab=produto&T099_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content3) . '&D024_Id=' . urlencode(@$r_D024_Id) . '&data=' . urlencode($data)),
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	case 'crm005ContentPrePedido':		
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$content3 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content3', $content3);
		$g['smarty']->assign('content1Height', '60px');
		$g['smarty']->assign('content2Height', '35px');
		$g['smarty']->assign('content3Height', '90%');
		$g['smarty']->display('content-3rows.tpl');

		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content2, '/crm/crm005/form/crm005FormProdutos/', '&tab=produto&T099_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content3) . '&D024_Id=' . urlencode(@$r_D024_Id) . '&data=' . urlencode($data)),
			array($content3, '/crm/crm005/grid/crm005GridPrePedido/', '&T099_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2) . '&D024_Id=' . urlencode(@$r_D024_Id) . '&divContent=' . urldecode($g['divId']) . '&data=' . urlencode($data) . '&divOutro=' . urldecode($content1)),
			array($content1, '/crm/crm005/outro/crm005Cliente/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urldecode($g['divId']) . '&data=' . urlencode($data) . '&formIdCliente='. urlencode($content2) . '&D024_Id=' . urlencode(@$r_D024_Id)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'crm005contentResumo':		
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		
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
			array($content1, '/crm/crm005/tab/crm005tabResumo/', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'crm005contentProgramacao':		
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		
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
			array($content1, '/crm/crm005/tab/crm005TabProgramacao/', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
			//array($content1, '/crm/crm005/tab/crm005GridProgramacao/', '&D024_Id=' . urlencode($r_D024_Id) . '&data=' . urlencode($data)),
		);
		echo gProcessaAcoes($actions);
		
	break;	

	case 'crm005ContentPrePedidoEmpresas':
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		
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
			array($content1, '/crm/crm005/tab/crm005TabPrePedidoEmpresas/', '&data=' . urlencode($data) . '&divRefresh=' . urlencode($r_divRefresh)),
			//array($content1, '/crm/crm005/grid/crm005GridPrePedidoEmpresas/', '&data=' . urlencode($data) . '&divRefresh=' . urlencode($r_divRefresh)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'crm005contentSelecionarVendedorInterno':
		//Data enviada do crm005Data
		$data = (isset($r_data)) ? $r_data : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$Flag_ACP = (isset($r_Flag_ACP)) ? $r_Flag_ACP : false;
		
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
			array($content1, '/crm/crm005/tab/crm005TabSelecionarVendedor/', '&data=' . urlencode($data) . '&D024_Id=' . urlencode($D024_Id) . '&Flag_ACP='. urlencode($Flag_ACP)),
			//array($content1, '/crm/crm005/grid/crm005GridPrePedidoEmpresas/', '&data=' . urlencode($data) . '&divRefresh=' . urlencode($r_divRefresh)),
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
