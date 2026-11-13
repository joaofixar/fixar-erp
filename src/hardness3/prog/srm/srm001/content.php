<?php
namespace hardness;
	
switch ($g['r'][3]) {
	case 'srm001ContentPrincipal':
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
			array($content1, '/srm/srm001/content/srm001ContentPrincipal2/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'srm001ContentPrincipal2':
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
			array($content1, '/srm/srm001/tab/srm001TabPrincipal/', '&content1=' . urlencode($g['divId']) . '&D024_Id=' . urlencode($D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'srm001ContentPrincipal2rows':
		$novocliente = (isset($r_novocliente)) ? $r_novocliente : '';
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
			$actions[] = array($content1, '/srm/srm001/tab/srm001TabGeral/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($r_acaoId) . '&novocliente=' . $novocliente);
		
		echo gProcessaAcoes($actions);
	break;
	
	case 'srm001ContentPrincipalCliente':
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
			array($content1, '/srm/srm001/tab/srm001TabPrincipalCliente/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'srm001ContentPrincipalSRM':
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
			array($content2, '/srm/srm001/outro/srm001InfoCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content3, '/srm/srm001/outro/srm001DadosFinanceiro/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content6, '/srm/srm001/outro/srm001Observacoes/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)), 
			array($content7, '/srm/srm001/content/srm001ContentPrincipalCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content4, '/srm/srm001/grid/srm001GridAgenda/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content5, '/srm/srm001/grid/srm001GridContatos/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content3, '/srm/srm001/outro/srm001Totais/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			
		);
		echo gProcessaAcoes($actions);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$content7}').parent().parent().css('overflow', 'hidden');
				setTimeout(function(){
					$('#{$content7}').width('100%');
					var widthContent = $('#{$content7}').width();
					var heightContentParent = $('#{$content7}').parent().height();
					widthContent = widthContent - 10;
					heightContentParent = heightContentParent - 118;
			    	$('#{$content7}').css({
						"width": widthContent+"px",
						"margin-left": "5px",
						"margin-right": "5px",
						"height": heightContentParent+"px"
					});
				}, 100);
		    } </script></div>
EOT;
	break;

	case 'srm001ContentComprasEstoque':
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
			array($content1, '/srm/srm001/tab/srm001TabComprasEstoque/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'srm001ContentCotacoes':
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
			array($content1, '/srm/srm001/tab/srm001TabCotacoes/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;


	case 'srm001ContentOrdensCompra':
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
			array($content1, '/srm/srm001/tab/srm001TabOrdensCompras/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'srm001ContentNotasRecebidas':
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
			array($content1, '/srm/srm001/tab/srm001TabNotasRecebidas/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'srm001ContentConhecimentos':
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
			array($content1, '/est/est005/tab/est005TabPrincipal/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'srm001ContentCotacoesFornecedor':
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
			array($content1, '/srm/srm001/tab/srm001TabCotacoesFornecedor/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;


	case 'srm001ContentOrdensComprasFornecedor':
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
			array($content1, '/srm/srm001/tab/srm001TabOrdensComprasFornecedor/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'srm001ContentNotasRecebidasFornecedor':
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
			array($content1, '/srm/srm001/tab/srm001TabNotasRecebidasFornecedor/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;

    case 'srm001ContentFornecedor':
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
            array($content1, '/srm/srm001/form/srm001FormSetFornecedor/', '&tab=geral&content1=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'srm001ContentEmailFornecedor';
    	$extra = isset($r_extra) ? $r_extra : false;
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
            array($content1, '/etc/etc006/tab/etc006TabLeituraCliente/', '$D024_Id=' . urlencode($r_D024_Id))
        );
        echo gProcessaAcoes($actions);
    break;

    case 'srm001ContentComprasEmergencia';
    	$extra = isset($r_extra) ? $r_extra : false;
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
            array($content1, '/srm/srm001/tab/srm001TabComprasEmergencia/', '')
        );
        echo gProcessaAcoes($actions);
    break;

    case 'srm001ContentComprasEstoqueModelos':
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
			array($content1, '/srm/srm001/tab/srm001TabComprasEstoqueModelos/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
	break;
}