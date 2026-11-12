<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'est017ContentPrincipal':
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
			array($content1, '/est/est017/tab/est017TabPrincipal/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentPCP':
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
			array($content1, '/est/est017/tab/est017TabPCP/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentOrdemProducao':
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
			array($content1, '/est/est017/tab/est017TabOrdemProducao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if($r_acaoId > 0){
			$botoesEsq .= '<button type="submit" id="est017ImprimirOrdemProducao_btn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		}
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017ImprimirOrdemProducao_btn').bind('click', function(data) {
					var buttons = {
						"Um produto por folha": function() {
							nova_janela('/est/est017/outro/imprimirOrdemProducao/?ajax=true&imprimirSequencia=false&T255_Id=' + encodeURIComponent("{$r_acaoId}") + '',0,0,750,500,'yes','no','no','no','yes','no','Ordem de Produção');
							$(this).dialog("destroy").remove();
					 	},
						"Impressão sequêncial": function() {
							nova_janela('/est/est017/outro/imprimirOrdemProducao/?ajax=true&imprimirSequencia=true&T255_Id=' + encodeURIComponent("{$r_acaoId}") + '',0,0,750,500,'yes','no','no','no','yes','no','Ordem de Produção');
							$(this).dialog("destroy").remove();
						},
						"Fechar": function() {
							$(this).dialog("destroy").remove();
						},
					};
					dialogConfirm('Impressão', 'Selecione o formato da impressão:', buttons);
				});
			} </script></div>
EOT;
	break;

	case 'est017ContentEntradaProducao':
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
			array($content1, '/est/est017/tab/est017TabEntradaProducao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est017ContentEntradaProduto':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '35px;');
		echo  $g['smarty']->fetch('content-2rows.tpl');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est017/form/est017FormEntradaProducao/', '&tab=geral&T256_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est017/grid/est017GridEntradaProducao/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentProcessarProducao':
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
			array($content1, '/est/est017/tab/est017TabProcessarProducao/', '&extra=' . urlencode($r_extra))
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est017ProcessarProducao_btn">Processar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		if ($g['C031']['processarEntradaEmEtapas'] == 'S') {
			
			$sql = "SELECT T256_Id FROM T256 " . base64_decode($r_extra);
			$sql = gComprimeSql($sql);	
			
			$js = <<<JS
				$('#{$g['divId']}').showLoading();
				$.getJSON('/est/est017/form_func-ajax/processaProducaoEmEtapas/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					
					if (request.code) {
						processaEtapasAsync("Processar Produção?", "EST017", "processarEntradaEmEtapas()", "{$sql}", false, "{$g['divId']}" || $('#{$g['divIdParent']}').attr('id') || '')
							.then(function () {
								console.log('then executando depois do processaEtapasAsync');
							})
							.catch(function (err) {
								console.log('erro etapas:', err);
							})
							.finally(function () {								
								console.log('finally etapas');
								divRefresh($("#{$g['divIdParent']}").parent().parent().find('.content-2rows-last').attr('id'));
								divRefresh($("#{$g['divIdParent']}").parent().parent().find('.content-2rows-width').attr('id'));
								fecharJanela("{$g['divId']}");
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est017/form_func-ajax/afterProcessaProducaoEmEtapas/?ajax=true&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											console.log('Retorno do disparo do e-mail do certificado de qualidade: ' + request.email);
											divRefresh('{$g['divIdParent']}');
											if (request.data) {
												setTimeout(function(){
													dialogConfirm('Ok', request.data); // Entrada de estoque realizada com sucesso!
												}, 150);
											}
										});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} else {
										console.log('Retorno do disparo do e-mail do certificado de qualidade: ' + request.email);
										dialogConfirm('Erro', request.data);
									}
								});
							});
					} else {
						dialogConfirm('Erro', request.data);
					}
				});
			JS;
		} else {
			$js = <<<JS
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est017/form_func-ajax/est017ProcessarEntradas/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									console.log('Retorno do disparo do e-mail do certificado de qualidade: ' + request.email);
									divRefresh('{$g['divIdParent']}');
									setTimeout(function(){
										dialogConfirm('Ok', request.data);
									}, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} else {
								console.log('Retorno do disparo do e-mail do certificado de qualidade: ' + request.email);
								dialogConfirm('Erro', request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				};
				dialogConfirm('Atenção', 'Deseja processar as entradas?', \$buttons);
			JS;
		}

		echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017ProcessarProducao_btn').bind('click', function(data) {
					{$js}
				});
			} </script></div>
JS;
	break;

	case 'est017ContentConsumiveis':
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
			array($content1, '/est/est017/tab/est017TabConsumiveis/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	break;

	case 'est017ContentProdutoComposicao':
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
			array($content1, '/est/est017/form/est017FormProdutoComposicao/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	break;

	case 'est017ContentProcessos':
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
			array($content1, '/est/est017/form/est017FormProcessos/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	break;

	case 'est017ContentConsumivel':
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
			array($content1, '/est/est017/form/est017FormConsumivel/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	break;

	case 'est017ContentProdutoProducao':
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
			array($content1, '/est/est017/form/est017FormProdutoProducao/', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	break;

	case 'est017ContentOrdensProducao':
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
			array($content1, '/est/est017/tab/est017TabOrdensProducao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentProducoes':
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
			array($content1, '/est/est017/tab/est017TabProducoes/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentProducaoAberto':
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
			array($content1, '/est/est017/tab/est017TabProducaoAberto/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est017ContentProducaoFinalizado':
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
			array($content1, '/est/est017/tab/est017TabProducaoFinalizado/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
}
