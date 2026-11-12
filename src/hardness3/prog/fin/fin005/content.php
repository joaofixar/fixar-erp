<?php
namespace hardness;
	/**
	* FIN - FIN005 - Boletos Bancários
	* Weiberlan
	* 17/11/2011
	*/
	switch($g['r'][3]) {
		case 'fin005ContentPrincipal':
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
				array($content1, '/fin/fin005/tab/fin005TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'fin005ContentAbreCadastroBoleto':
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
				array($content1, '/fin/fin005/tab/fin005TabCadastroBoleto/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2) . '&T002_Id=' . urlencode(@$r_T002_Id)),
				array($content2, '/fin/fin005/tab/fin005TabInfoBoleto/', '&acaoId=' . urlencode($r_acaoId) . '&T002_Id=' . urlencode(@$r_T002_Id))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'fin005ContentAtualizarVencimento':
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
				array($content1, '/fin/fin005/form/fin005FormAtualizarVencimento/', '&tab=geral&acaoId=' . urlencode($r_T117_Id))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'fin005ContentAtualizarVencimentoMarcados':
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
				array($content1, '/fin/fin005/form/fin005FormAtualizarVencimentoMarcados/', '&tab=geral&extra=' . urlencode($r_extra))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'fin005ContentBoletosParaEnvio':
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
				array($content1, '/fin/fin005/tab/fin005TabBoletosParaEnvio/', '&tab=geral&extra=' . urlencode($r_extra))
			);
			echo gProcessaAcoes($actions);

			$botoes = array(
				array('titulo' => 'Enviar Padrão', 'id' => 'fin005EnviarBoletos'),
				array('titulo' => 'Enviar Lembrete', 'id' => 'fin005EnviarBoletosLembrete'),
				array('titulo' => 'Enviar Cobrança', 'id' => 'fin005EnviarBoletosCobranca'),
				array('titulo' => 'Enviar Boletos e DANFES', 'id' => 'fin005EnviarDanfes'),
				array('titulo' => 'Enviar Boletos e ACP', 'id' => 'fin005EnviarACP'),
			);
	
			echo Botoes::processar($botoes, 'content');
	

/* 			$botoesMid = '';
			$botoesDir = '';
			$botoesEsq = '';
			$botoesEsq .= '<button type="submit" id="fin005EnviarBoletos">Enviar Padrão</button>';
			$botoesEsq .= '<button type="submit" id="fin005EnviarBoletosLembrete">Enviar Lembrete</button>';
			$botoesEsq .= '<button type="submit" id="fin005EnviarBoletosCobranca">Enviar Cobrança</button>';
			$botoesEsq .= '<button type="submit" id="fin005EnviarDanfes">Enviar Boletos e DANFES</button>';
			$botoesEsq .= '<button type="submit" id="fin005EnviarACP">Enviar Boletos e ACP</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl'); */
			
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#fin005EnviarBoletos').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Atenção', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$content1}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Enviar Boletos Modelo Padrão', 'Você irá enviar o modelo <b>PADRÃO</b> de cobrança, deseja continuar? ', \$buttons);
					});

					$('#fin005EnviarBoletosLembrete').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoletoLembrete/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Atenção', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$content1}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Enviar Boletos Modelo Lembrete', 'Você irá enviar o modelo <b>LEMBRETE</b> de cobrança, deseja continuar? ', \$buttons);
					});

					$('#fin005EnviarBoletosCobranca').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoletoCobranca/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Atenção', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$content1}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Enviar Boletos Modelo Cobrança', 'Você irá enviar o modelo de <b>COBRANÇA</b>, deseja continuar? ', \$buttons);
					});

					$('#fin005EnviarDanfes').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoleto/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&enviarDanfe=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Atenção', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$content1}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Enviar Boletos', 'Você tem certeza? ', \$buttons);
					});

					$('#fin005EnviarACP').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/fin/fin005/grid_func-ajax/enviarMultiEmailBoletoACP/?ajax=true&extra=' + encodeURIComponent("{$r_extra}") + '&enviarACP=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Atenção', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$content1}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Enviar Boletos e ACP', 'Você tem certeza? ', \$buttons);
					});
				} </script></div>
EOT;
		break;
	}
?>
