<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'crm001ContentPrincipal':
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
			array($content1, '/crm/crm001/content/crm001ContentPrincipal2/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'crm001ContentPrincipal2':
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
			array($content1, '/crm/crm001/tab/crm001TabPrincipal/', '&content1=' . urlencode($g['divId']) . '&D024_Id=' . urlencode($D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'crm001ContentPrincipal2rows':
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
			$actions[] = array($content1, '/crm/crm001/tab/crm001TabGeral/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($r_acaoId) . '&novocliente=' . $novocliente);

		echo gProcessaAcoes($actions);
	break;

	case 'crm001ContentPrincipalCliente':
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
			array($content1, '/crm/crm001/tab/crm001TabPrincipalCliente/', '&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'crm001ContentPrincipalCRM':
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
			array($content2, '/crm/crm001/outro/crm001InfoCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content3, '/crm/crm001/outro/crm001DadosFinanceiro/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			array($content6, '/crm/crm001/outro/crm001Observacoes/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)), 
			array($content7, '/crm/crm001/content/crm001ContentPrincipalCliente/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content4, '/crm/crm001/grid/crm001GridAgenda/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content5, '/crm/crm001/grid/crm001GridContatos/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
			//array($content3, '/crm/crm001/outro/crm001Totais/', '&tab=geral&content1=' . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),
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

	case 'crm001ContentSaldoGeralPedidos':
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
			array($content1, '/crm/crm001/tab/crm001TabSaldoGeralPedidos/', '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'crm001contentEditarAgenda':
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
			array($content1, '/crm/crm001/tab/crm001TabEditarAgenda/', '&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'crm001contentEditarAgendaOrcamento':
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
			array($content1, '/crm/crm001/tab/crm001TabEditarAgendaOrcamento/', '&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'crm001contentEditarContato':
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
            array($content1, '/crm/crm001/tab/crm001TabEditarContato/', '&D024_Id=' . urlencode($r_D024_Id) . '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'crm001ContentPedidosProduto':
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
            array($content1, '/crm/crm001/tab/crm001TabPedidosProduto/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

     case 'crm001ContentPrincipalB2B':
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
            array($content1, '/crm/crm001/tab/crm001TabPrincipalB2B/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentPedidos':
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
            array($content1, '/crm/crm001/tab/crm001TabPedidos/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'crm001ContentDiMP':
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
            array($content1, '/crm/crm001/tab/crm001TabDiMP/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'crm001ContentDiLP':
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
            array($content1, '/crm/crm001/tab/crm001TabDiLP/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'crm001ContentCliente':
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
            array($content1, '/crm/crm001/form/crm001FormSetCliente/', '&tab=geral&content1=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

    case 'crm001ContentIncluirNotificacao':
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
            array($content1, '/crm/crm001/form/crm001FormNotificacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

        /**
         * Botões
         */
        // Definição dos botões

        $botoesMid = '';
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="crm001FormNotificacao_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário

    break;

    case 'crm001ContentSelecionarDiasDuplicar';
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
            array($content1, '/crm/crm001/tab/crm001TabDuplicarPedido/', '&tab=geral&extra=' . $r_extra)
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentIncluirAgendaGeral';
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
            array($content1, '/crm/crm001/tab/crm001TabIncluirAgendaGeral/', '&tab=geral&extra='.urlencode($extra))
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentGridAgenda';
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
            array($content1, '/crm/crm001/tab/crm001TabGridAgenda/')
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentAnaliseCompras';
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
            array($content1, '/crm/crm001/tab/crm001TabAnaliseCompras/')
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentEmail';
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
            array($content1, '/etc/etc006/tab/etc006TabLeitura/')
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentEmailCliente';
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

   	case 'crm001ContentOrcamentoRetornoPendente':
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
			array($content1, '/crm/crm001/tab/crm001TabOrcamentoRetornoPendente/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'crm001Resumos':
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
			array($content1, '/crm/crm001/tab/crm001Resumos/')
		);
		echo gProcessaAcoes($actions);
	break;

    case 'crm001ContentOrcamentos':
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
            array($content1, '/crm/crm001/tab/crm001TabOrcamentos/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);
    break;

	case 'crm001ContentConfiguracoesComerciais':
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
			array($content1, '/crm/crm001/tab/crm001TabConfiguracoesComerciais/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'crm001ContentCanalProspecaoCliente':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/crm/crm001/tab/crm001TabCanalProspecaoCliente/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$g['smarty']->assign('botoesEsq', '');
		$g['smarty']->assign('botoesMid', '');
		$g['smarty']->assign('botoesDir', '<button type="submit" id="crm001FormCanalProspecaoCliente_btn">Enviar</button><button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Fechar</button>');
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

    case 'crm001ContentPrincipalNotasFiscais':
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
			array($content1, '/crm/crm001/tab/crm001TabPrincipalNotasFiscais/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'crm001ContentPrincipalClientes':
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
			array($content1, '/crm/crm001/tab/crm001TabPrincipalClientes/')
		);
		echo gProcessaAcoes($actions);
	break;

    case 'crm001ContentPrincipalOrdens':
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
            array($content1, '/crm/crm001/tab/crm001TabPrincipalOrdens/')
        );
        echo gProcessaAcoes($actions);
    break;

	case 'crm001ContentCancelarPedidos':
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
            array($content1, '/crm/crm001/tab/crm001TabCancelarPedidos/', '&tab=geral&extra=' . urlencode($r_extra)),
        );
        echo gProcessaAcoes($actions);

        $botoesMid = '';
        $botoesDir = '';
        $botoesEsq = '';
        $botoesDir .= '<button type="submit" id="buttonCancelar">Cancelar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#buttonCancelar').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar Pedidos', '/crm/crm001/content/crm001ContentFormularioCancelamentos/', '&tab=geral&extra=' + encodeURIComponent("{$r_extra}"), [350,200]);
                });
            } </script></div>
EOT;

	break;

    case 'crm001ContentFormularioCancelamentos':
    
        /**
        * Este código PHP será executado ao acessar: /crm-crm001-content-crm001ContentFormularioCancelamentos/
        * Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
        *
        * Caso seja uma AJAX, é necessário acrescentar este código:
        * No inicio: $resposta = array('code' => true, 'data' => array());
        * No final: echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
        */

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
            array($content1, '/crm/crm001/form/crm001FormCancelamento/', '&tab=geral&extra=' . urlencode($r_extra)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentIncluirProdutosTabelaPreco':
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
            array($content1, '/crm/crm001/tab/crm001TabIncluirProdutosTabelaPreco/', '&tab=geral&extra=' . urlencode($r_extra)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentTabelaPrecos':
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
            array($content1, '/crm/crm001/tab/crm001TabTabelaPrecos/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentIncluirTabelaCliente':
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
            array($content1, '/crm/crm001/form/crm001FormIncluirTabelaCliente/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'crm001ContentCSVTabelaPrecos':
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('content1Height', '40px;');
        $content2 = uniqid();
        $g['smarty']->assign('content2', $content2);        
        echo  $g['smarty']->fetch('content-2rows.tpl');     
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/crm/crm001/form/crm001FormCSVTabelaPrecos/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
            array($content2, '/crm/crm001/outro/crm001ManualCSVTabelaPrecos/','&tab=geral')
        );
        echo  gProcessaAcoes($actions);
        
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
    break;

    case 'crm001contentOrcamentoPerdido':
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
            array($content1, '/crm/crm001/tab/crm001TabOrcamentoPerdido/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);
    break;
}
