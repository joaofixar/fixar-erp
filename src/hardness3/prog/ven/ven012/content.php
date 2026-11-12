<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	case 'ven012ContentPrincipal':
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
		array($content1, '/ven/ven012/tab/ven012TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven012ContentFilaDeEnvioNovo':
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
		array($content1, '/ven/ven012/tab/ven012GridFilaDeEnvioNovo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven012ContentFaturamento':
		$sql=mysql_query("SELECT T007_Numero_Nota_Fiscal,T007_Data_Emissao FROM T007 WHERE T007_T005_Id='{$r_T005_Id}' AND T007_Flag_Cancelada!='S' AND T007_Flag_Inutilizado!='S' AND IFNULL(T007_Protocolo_Inutilizacao,'') = ''");
	    if (mysql_num_rows($sql)>0) {
			$cMsg_Faturado="Este pedido já foi faturado:, ";
			while ($mT007=mysql_fetch_array($sql)){
				$mT007['T007_Data_Emissao']=gCorrigeData($mT007['T007_Data_Emissao']);
				$cMsg_Faturado.="NF: {$mT007['T007_Numero_Nota_Fiscal']} no dia {$mT007['T007_Data_Emissao']}, ";
			}
	    	$cSql=mysql_query("update T005 set T005_Flag_Status='4',T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),T005_Flag_Situacao=' ' where T005_Id='$r_T005_Id'");

	     	// Imprime uma mensagem e fecha a janela
			$alert = gGeraAlertMsg('Pedido já faturado', $cMsg_Faturado, false);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						setTimeout(function() {
						{$alert}
						}, 150);
					});
					$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
				} </script></div>
EOT;
			die();
    	}

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
		array($content1, '/ven/ven012/tab/ven012TabFaturamento/', '&acaoId=' . urlencode($r_acaoId) . "&T005_Id=" . urlencode($r_T005_Id))
		);
		echo gProcessaAcoes($actions);

		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven012FormPedidoAlterar_btn" title="Salvar as alterações sem gerar a nota fiscal">Alterar</button>';
		$botoesDir .= '<button type="submit" id="ven012FormPedido_btn">Continuar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesEsq .= '<button type"submit" id="ven012AtualizarAliquotas">Atualizar Aliquotas</button>';
		$botoesEsq .= '<button type="submit" id="ven002AtualizarOpcoes">Atualizar</button>';
		$botoesEsq .= '<button type="submit" id="ven012ImprimirPedido">Imp. Pedido</button>';
		$botoesEsq .= '<button type="submit" id="ven012ImprimirChecklist">Checklist</button>';
		$botoesEsq .= '<button type="submit" id="ven012ImprimirOrdemServico">Ordem de serviço</button>';
        $botoesEsq .= '<button type="submit" id="ven012DividirPedido">Dividir Pedido</button>';
        if($g['C031']['botaoAplicarDesconto'] == 'S'){
			$botoesEsq .= '<button type="submit" id="ven012AplicarDesconto">Aplicar Desconto</button>';
		}
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#ven012FormPedidoAlterar_btn').unbind('click').bind('click', function() {
				var formulariosPedido = $('#{$g['divId']} form').filter(function() {
					return $(this).find('[name^="ven012FormPedido["]').length > 0;
				});
				if (!formulariosPedido.length) {
					dialogConfirm('Atenção', 'Abra a aba Pedido ou Frete antes de salvar as alterações.');
					return false;
				}
				formulariosPedido.find('.ven012SalvarSemGerar').remove();
				formulariosPedido.append('<input type="hidden" class="ven012SalvarSemGerar" name="salvarSemGerar" value="S">');
				$('#ven012FormPedido_btn').trigger('click');
				return false;
			});
			$('#ven012AtualizarAliquotas').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T005_Id = $('#{$g['divId']} #T005_Id').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven012/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, \$buttons);
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
				dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
			});
			$('#ven012ImprimirPedido').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					window.open(request.data);
				});
			});
			$('#ven012AplicarDesconto').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven001/content/ven001ContentAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' + encodeURIComponent("{$r_T005_Id}") + '&tabelaCabecalho=T005&tabelaProduto=T006', [600,200]);
			});
			$('#ven012ImprimirChecklist').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven012/form_func-ajax/ven012ImprimirChecklist/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					window.open(request.data);
				});
			});
			$('#ven012ImprimirOrdemServico').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven012/form_func-ajax/ven012ImprimirOrdemServico/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					window.open(request.data);
				});
			});
			$('#ven012DividirPedido').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Pedido', '/ven/ven012/content/ven012ContentDividirPedido/', '&T005_Id=' + encodeURIComponent("{$r_T005_Id}"), [1000,580]);
			});
			$('#ven002AtualizarOpcoes').bind('click', function(data) {
				var \$buttons = {
					"Atualizar Aliquotas": function() {
						var \$buttons2 = {
							"Sim": function() {
								var T005_Id = $r_T005_Id;
								idProgressBar = dialogAlert('Atualizando alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,1);
					            $(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						$(this).dialog("destroy").remove();
						dialogConfirm('Atenção', 'Deseja atualizar as aliquotas?', \$buttons2);
					},
					"Atualizar CFOP": function() { 
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven002/content/ven002ContentAtualizarCFOP/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_acaoId}"), [500,200]);
						$(this).dialog("destroy").remove();
					},
					"Cancelar": function() { 
						$(this).dialog("destroy").remove();
					}
				};
				dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
			});

			function ven002AtualizarAliquotasLoop(idProgressBar, T005_Id, inicio){
                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,0);
                    }else{
                    	if (request.code) {
                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
                    		dialogConfirm('Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                            divRefresh('{$g['divId']}');
                        } else {
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                            dialogConfirm('Erro!', request.data);
                        }
                    } 
                });	                    
	        }
		} </script></div>
EOT;

	break;

	case 'ven012ContentAbrePagamento':
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
		array($content1, '/ven/ven012/tab/ven012TabPagamento/', '&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven012ContentPagamento':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '160px');
		$g['smarty']->assign('tipo', '2linhas');

		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002FormPagamento/', "&tab=geral&T005_Id=" . urlencode($r_T005_Id) . "&acaoId=" . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002GridPagamento/', '&acaoId=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'ven012ContentInutilizar':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/ven/ven012/tab/ven012TabInutilizar/', '&tab=inutilizar&acaoId=' . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven012ContentDetalhesCheque':
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
		array($content1, '/ven/ven012/form/ven012FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T090_Id)),
		);
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven012FormDetalhesCheque_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

    case 'ven012ContentDividirPedido':
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
    		array($content1, '/ven/ven012/tab/ven012TabPrincipalDividir/', '&T005_Id=' . urlencode($r_T005_Id) . '&divRefresh=' . urlencode($g['divId']))
        );
        echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesEsq .= '<button id="ven012ImprimirPedido" class="btnFechar">Imprimir</button>';
		$botoesEsq .= '<button id="ven012EnviarEmail" class="btnFechar">E-mail</button>';
		$botoesEsq .= '<button id="ven012AplicarDesconto" class="btnFechar">Desconto</button>';
		$botoesEsq .= '<button id="ven002AtualizarOpcoes" class="btnFechar">Atualizar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		$usarAlcada = $g['C031']['usarAlcada'];
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven012AplicarDesconto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven002/content/ven002ContentAplicarDesconto/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_T005_Id}"), [400,200]);
				});
				$('#ven002AtualizarOpcoes').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', request.data, \$buttons);
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
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar as aliquotas?', \$buttons2);
						},
						"Atualizar CFOP": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven002/content/ven002ContentAtualizarCFOP/', '&tab=geral&T005_Id=' + encodeURIComponent("{$r_T005_Id}"), [500,200]);
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { 
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
				});

				$('#ven012EnviarEmail').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven002/form_func-ajax/verificarParcelas/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request1) {
						if(request1.code) {
							if('{$usarAlcada}' == 'N'){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T005_Id=' + encodeURIComponent("{$r_T005_Id}"), [900,550]);
							}else{
								 $.getJSON('/ven/ven002/form_func-ajax/verificaAlcada/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
									 $('#{$g['divId']}').hideLoading();
									 if (request.code == true) {
								 		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T005_Id=' + encodeURIComponent("{$r_T005_Id}"), [900,550]);
									 }else if(request.code == false) {
											var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
										    dialogConfirm('Enviar E-mail', request.data, \$buttons);
									 }else{
										var \$buttons = {
											"Sim": function() {
												$('#{$g['divId']}').showLoading();
												$(this).dialog("destroy").remove();
												$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.alcadaUpdate) + '&callback=?', function(request2) {
													$('#{$g['divId']}').hideLoading();
													if (request2.code) {
														var \$buttons = {
															 "Fechar": function() {
																$(this).dialog("destroy").remove();
																fecharJanela('{$g['divId']}');
																divRefresh('{$g['divIdParent']}', true);
															 } 
														};
														dialogConfirm('Ok', request2.data, \$buttons);
													} else {
														var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
														dialogConfirm('Erro', request2.data, \$buttons);
													}
												});
											},
											"Não": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Enviar E-mail', request.data, \$buttons);
									 }
								 });
							}
						}else{
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', request1.data, buttons);
				    	}
					});
				});

				$('#ven012ImprimirPedido').unbind('click').bind('click', function(data) {
					if('{$usarAlcada}' == 'N'){
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if(request.code) {
								window.open(request.data);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    			dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					}else{
						$.getJSON('/ven/ven002/form_func-ajax/verificaAlcada/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
							 $('#{$g['divId']}').hideLoading();
							 if (request.code == true) {
							 	$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if(request.code) {
										window.open(request.data);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    					dialogConfirm('Erro', request.data, \$buttons);
									}
								});
							 }else if(request.code == false) {
									var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Enviar E-mail', request.data, \$buttons);
							 } else {
								var \$buttons = {
									"Sim": function() {
										$('#{$g['divId']}').showLoading();
										$(this).dialog("destroy").remove();
										$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&alcadaUpdate=' + encodeURIComponent(request.alcadaUpdate) + '&callback=?', function(request2) {
											$('#{$g['divId']}').hideLoading();
											if (request2.code) {
												var \$buttons = {
													 "Fechar": function() {
														$(this).dialog("destroy").remove();
														fecharJanela('{$g['divId']}');
														divRefresh('{$g['divIdParent']}', true);
													 } 
												};
												dialogConfirm('Ok', request2.data, \$buttons);
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro', request2.data, \$buttons);
											}
										});
									},
									"Não": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Enviar E-mail', request.data, \$buttons);
							 }
						 });
					}
				});
			} </script></div>
EOT;
    break;

    case 'ven012ContentDividirPedidoEmpresa':
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
    		array($content1, '/ven/ven012/tab/ven012TabPrincipalDividirPedidoEmpresa/', '&D024_Id=' . urlencode($r_D024_Id) . '&divRefresh=' . urlencode($g['divId']))
        );
        echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

    break;

    case 'ven012ContentItensDividirPedido':
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
        $g['smarty']->display('content-3rows.tpl');

        // O que será carregado em cada conteúdo?
        $actions = array(
        // identificador, url, paramentros get (opcional)
            array($content1, '/ven/ven012/grid/van012GridProdutosDividir/', "&T005_Id=" . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content2)),
            array($content2, '/ven/ven012/grid/ven012GridServicosDividir/', '&T005_Id=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content1)),
            array($content3, '/ven/ven012/grid/ven012GridTrocasDividir/', '&T005_Id=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content2)),
        );
        echo gProcessaAcoes($actions);

    break;

    case 'ven012ContentSelecionarEmpresa':
		$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
		$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;
		$dividirPedidoEmpresa = isset($r_dividirPedidoEmpresa) ? $r_dividirPedidoEmpresa : false;
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
        array($content1, '/ven/ven012/tab/ven012TabSelecionarEmpresa/', "&T005_Id=" . urlencode($r_T005_Id) . '&divRefresh=' . urlencode($r_divRefresh) . '&mudarFilial=' . urlencode($mudarFilial) . '&gerarNF=' . urlencode($gerarNF) . '&dividirPedidoEmpresa='. urlencode($dividirPedidoEmpresa)),
        );
        echo gProcessaAcoes($actions);

        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="ven012SelecionarEmpresa_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'ven012GridFilaDeEnvioNovo':
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
            array($content1, '/ven/ven012/tab/ven012GridFilaDeEnvioNovo/', "&T007_Id=" . urlencode($r_T007_Id)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'ven012EnviarNFe':
		/* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		//array($content1, '/ven/ven003/tab/ven003TabTXTXML/', '&acaoId=' . urlencode($r_acaoId))
		array($content1, '/ven/ven012/tab/ven012GridFilaDeEnvioNovo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
}
