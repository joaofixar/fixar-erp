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
	case 'fin002content01':
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
			array($content1, '/fin/fin002/tab/fin002tab01/')
		);
		echo gProcessaAcoes($actions);
		
	break;
	
	/**
	* Form Principal
	* Chamado em: fin002grid01
	*/
	case 'fin002content02':
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
			array($content1, '/fin/fin002/tab/fin002tab02/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);

	break;
	/**
	* Baixar
	* Chamado em: fin002grid01
	*/
	case 'fin002content03':
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
			array($content1, '/fin/fin002/tab/fin002tab03/', '&acaoId=' . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'fin002ContentAgrupar':
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
			array($content1, '/fin/fin002/tab/fin002TabAgrupar/', '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'fin002ContentReparcelar':

		 /*
		 * Divisão da Area em Conteúdos
		 */
		 // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		 $content1 = uniqid();
		 $content2 = uniqid();
		 $g['smarty']->assign('content1', $content1);
		 $g['smarty']->assign('content2', $content2);
		 $g['smarty']->assign('content1Height', '200px');
		 $g['smarty']->assign('tipo', '2linhas');
		 // O que será carregado em cada conteúdo?
		 $actions = array(
		 // identificador, url, paramentros get (opcional)
		 array($content1, '/fin/fin002/tab/fin002tabReparcelamentoJanela/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
		 array($content2, '/fin/fin002/content/fin002ContentReparcelamento/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) .  '&extra=' . urlencode($r_extra)),
		 );
		 echo gProcessaAcoes($actions, true);
	break;
		 
		 
	 case 'fin002ContentReparcelamento':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '100px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/fin/fin002/form/fin002FormReparcelameto/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
		array($content2, '/fin/fin002/tab/fin002TabGridPagamento/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions, true);
	break;
	
	case 'fin002ContentReparcelarWindow':
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
			array($content1, '/fin/fin002/tab/fin002tabReparcelamento/', '&acaoId=' . urlencode($r_acaoId)),
		);

		echo gProcessaAcoes($actions);


		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="fin002EnviarEmailReparcelamento">Enviar Email</button>';
		$botoesEsq .= '<button type="submit" id="fin002ImprimirReparcelamento">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		$novoIdTelaForm = uniqid();
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin002EnviarEmailReparcelamento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T196_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
				});
				$('#fin002ImprimirReparcelamento').unbind('click').bind('click', function() {
					nova_janela('/fin/fin002/form_func-ajax/imprimirReparcelamento/?ajax=true&T196_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Imprimir Simulação');
				});
			} </script></div>
EOT;
		

	break;

	case 'fin002ContentAcrescimosDescontos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin002/form/fin002form02/', "&tab=acrescimos_descontos&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
			array($content2, '/fin/fin002/grid/fin002grid02/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'contentBaixa2rows':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin002/grid/fin002grid04/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .'&extra=' . urlencode($r_extra)),
			array($content2, '/fin/fin002/form/fin002form05/', '&tab=baixar&extra=' . urlencode($r_extra))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_baixar_btn">Baixar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fin002ContentProjecoes':
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
			array($content1, '/fin/fin002/tab/fin002TabProjecoes/', '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);
	break;

	/**
	* Agrupar
	* Chamado em: fin002grid01
	*/
	case 'fin002contentAgrupar':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin002/grid/fin002GridAgrupar/','&extra='.urlencode($r_extra)),
			array($content2, '/fin/fin002/form/fin002FormAgrupar/', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_agrupar_btn">Agrupar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';        
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'fin002ContentDocumentos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin002/form/fin002FormDocumentos/', "&tab=geral&divReload=" . urlencode($content2)),
			array($content2, '/fin/fin002/grid/fin002GridDocumentos/', ''),
		);
		echo gProcessaAcoes($actions, true);
	break;

	case 'fin002ContentDividir':
		 /*
		 * Divisão da Area em Conteúdos
		 */
		 // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		 $content1 = uniqid();
		 $content2 = uniqid();
		 $g['smarty']->assign('content1', $content1);
		 $g['smarty']->assign('content2', $content2);
		 $g['smarty']->assign('content1Height', '170px');
		 $g['smarty']->assign('tipo', '2linhas');
		 // O que será carregado em cada conteúdo?
		 $actions = array(
			 // identificador, url, paramentros get (opcional)
			 array($content1, '/fin/fin002/tab/fin002TabDividir/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
			 array($content2, '/fin/fin002/content/fin002ContentDivisao/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) .  '&extra=' . urlencode($r_extra)),
		 );
		 echo gProcessaAcoes($actions, true);
	break;

	case 'fin002ContentDivisao':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin002/form/fin002FormDivisao/', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2) . '&extra=' . urlencode($r_extra)),
			array($content2, '/fin/fin002/tab/fin002TabDivisao/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions, true);

		$novoIdTelaForm = uniqid();
        $botoesMid = '';
        $botoesDir = '';
        $botoesEsq = '';
        $botoesEsq .= '<button type="submit" id="fin002DividirBtn">Dividir</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        $extra_ = urlencode($r_extra);
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#fin002DividirBtn').unbind('click').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#{$g['divId']}').showLoading();
                        $.getJSON('/fin/fin002/grid_func-ajax/fin002Dividir/?ajax=true&&C007_Id=' + encodeURIComponent("{$g['empresaAtual']}") + '&valorSomado=' + encodeURIComponent("{$valorSomado}")+ '&extra={$extra_}&callback=?', function(request) {
                            if (request.code) {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                dialogConfirm('Ok', 'Processo concluído com sucesso', \$buttons);
                                divRefresh('{$g['divIdParent']}', true);
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            } else {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                dialogConfirm('Erro', request.data, \$buttons);
                            }
                            $('#{$g['divId']}').hideLoading();
                            divRefresh('{$g['divId']}');
                        });
                        $(this).dialog("destroy").remove();
                    },
                    "Não": function() { $(this).dialog("destroy").remove(); } 
                };
                dialogConfirm('Dividir duplicata(s)', 'Você tem certeza? ', \$buttons);
            });
        } </script></div>
EOT;
	break;	

	case 'fin002ImportarCSV':
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
           	array($content1, '/fin/fin002/form/fin002FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/fin/fin002/outro/fin002ManualCSVCliente/','&tab=geral')
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
}
