<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 27/04/2012
	*/
	case 'erp001ContentPrincipal':
		global $g;
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
			// 
			// !!! ALTERAÇÃO PARA EXIBIR O SISTEMA DE TABS CLIENTE !!!
			//
			//array($content1, '/erp/erp001/content/erp001ContentPrincipal2/')
			array($content1, '/erp/erp001/tab/erp001TabGeral/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'erp001ContentPrincipal2':
		global $g;
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/erp/erp001/tab/erp001TabPrincipal/', '&content1=' . urlencode($g['divId']) . '&D024_Id=' . urlencode($D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'erp001ContentPrincipal2rows':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array();
			// identificador, url, paramentros get (opcional)
			$actions[] = array($content1, '/erp/erp001/tab/erp001TabGeral/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($r_acaoId));
		
		echo gProcessaAcoes($actions);
	break;
	
	case 'erp001ContentPrincipalCliente':
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
			array($content1, '/erp/erp001/tab/erp001TabPrincipalCliente/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'erp001ContentPrincipalerp':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$content3 = uniqid();
		$content4 = uniqid();
		$content5 = uniqid();
		$content6 = uniqid();
		$content7 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content3', $content3);
		$g['smarty']->assign('content4', $content4);
		$g['smarty']->assign('content5', $content5);
		$g['smarty']->assign('content6', $content6);
		$g['smarty']->assign('content7', $content7);
		$g['smarty']->display('crm_principal.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content2, '/erp/erp001/outro/erp001InfoCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content3, '/erp/erp001/outro/erp001DadosFinanceiro/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content6, '/erp/erp001/outro/erp001Observacoes/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)), 
			array($content7, '/erp/erp001/content/erp001ContentPrincipalCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content4, '/erp/erp001/grid/erp001GridAgenda/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content5, '/erp/erp001/grid/erp001GridContatos/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content3, '/erp/erp001/outro/erp001Totais/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'erp001ContentSaldoGeralPedidos':
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
			array($content1, '/erp/erp001/tab/erp001TabSaldoGeralPedidos/', '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'erp001contentEditarAgenda':
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
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
			array($content1, '/erp/erp001/tab/erp001TabEditarAgenda/', '&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'erp001contentEditarContato':
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
            array($content1, '/erp/erp001/tab/erp001TabEditarContato/', '&D024_Id=' . urlencode($r_D024_Id) . '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;
    
    case 'erp001ContentPedidosProduto':
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
            array($content1, '/erp/erp001/tab/erp001TabPedidosProduto/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;
    
    case 'erp001ContentDiMP':
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
            array($content1, '/erp/erp001/tab/erp001TabDiMP/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break; 

    case 'erp001ContentDiLP':
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
            array($content1, '/erp/erp001/tab/erp001TabDiLP/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;    
    
    case 'erp001ContentCliente':
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
            array($content1, '/erp/erp001/form/erp001FormSetCliente/', '&tab=geral&content1=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;
}
?>