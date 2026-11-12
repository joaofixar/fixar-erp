<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author RubiaYwata
	* @since 16/11/2011
	*/
	case 'imp003ContentDi':
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
			array($content1, '/imp/imp003/tab/imp003TabDi/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'imp003ContentDiDeclaracaoImportacao':
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
			array($content1, '/imp/imp003/tab/imp003TabDiDeclaracaoImportacao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp003ContentDiIncluir':
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
			array($content1, '/imp/imp003/tab/imp003TabDiIncluir/', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($g['divId']))
		);
		echo gProcessaAcoes($actions);

		$T075 = mysql_query("SELECT * FROM T075 WHERE T075_Id = '{$r_acaoId}'");
		$T075 = mysql_fetch_array($T075);

		$duimpAmbiente = isset($g['C031']['duimpAmbiente']) ? strtoupper(trim((string) $g['C031']['duimpAmbiente'])) : '';
		if ($duimpAmbiente === '') {
			$duimpAmbiente = 'HOMOLOGACAO';
		}

		$duimpPerfil = isset($g['C031']['duimpPerfil']) ? trim((string) $g['C031']['duimpPerfil']) : '';

		if ($duimpAmbiente === 'PRODUCAO') {
			$duimpClientId = isset($g['C031']['duimpClientIdProducao']) ? trim((string) $g['C031']['duimpClientIdProducao']) : '';
			$duimpClientSecret = isset($g['C031']['duimpClientSecretProducao']) ? trim((string) $g['C031']['duimpClientSecretProducao']) : '';
		} else {
			$duimpClientId = isset($g['C031']['duimpClientIdHomologacao']) ? trim((string) $g['C031']['duimpClientIdHomologacao']) : '';
			$duimpClientSecret = isset($g['C031']['duimpClientSecretHomologacao']) ? trim((string) $g['C031']['duimpClientSecretHomologacao']) : '';
		}

		$mostrarBotaoConsultarDuimp = ($duimpPerfil !== '' && $duimpClientId !== '' && $duimpClientSecret !== '');

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($r_acaoId > 0){
			$botoesEsq .= '<button type="submit" id="imp003ExcluirItens">Excluir</button>';
			$botoesEsq .= '<button type="submit" id="imp003GerarNFe">Gerar NFe</button>';
			$botoesEsq .= '<button type="submit" id="imp003Calcular">Calcular</button>';
			$botoesEsq .= '<button type="submit" id="imp003ImportarOC">Importar Itens OC</button>';
			if($g['C031']['DIUsarBotaoEntradaEstoque'] == 'S') {
				$botoesEsq .= '<button type="submit" id="imp003Finalizar">Entrada estoque</button>';
			}
			$botoesEsq .= '<button type="submit" id="imp003GerarXMLLI">Gerar XML LI</button>';
			$botoesEsq .= '<button type="submit" id="imp003AtualizarCusto">Atualizar Custo</button>';
			$botoesEsq .= '<button type="submit" id="imp003AtualizarLocacao">Atualizar Locação</button>';
			if($g['C031']['habilitaBotaoAtualizarCadastroProdutoViaDI'] == 'S') {
				$botoesEsq .= '<button type="submit" id="imp003AtualizarCadastroProdutoOpcoes">Atualizar Cadastro Prd</button>';
			}
			$botoesEsq .= '<button type="submit" id="imp003ImportarXML">Importar XML</button>';
			if ($mostrarBotaoConsultarDuimp) {
				$botoesEsq .= '<button type="submit" id="imp003ConsultarDUIMP">Consultar DUIMP</button>';
			}
			if(isset($T075['T075_Data_Atualizacao_Custo']) && $T075['T075_Data_Atualizacao_Custo'] != '0000-00-00 00:00:00'){
				$botoesEsq .= ' Custo atualizado em: '.gCorrigeData($T075['T075_Data_Atualizacao_Custo']);
			}
		}
		$botoesDir .= '<button type="submit" id="imp003FormDiBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {				
				$('#imp003GerarNFe').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T075_Id = $('#{$g['divId']} #T075_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/gerarNFe/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							    if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Nota Fiscal - ID: ' + request.T007_Id, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent(request.T007_Id), [1200,800]);
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divIdParent']}', true);
										} 
									}
									dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var mensagem = '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">'+request.data+'</textarea>';
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', mensagem, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar NFe', 'Você tem certeza? ', \$buttons);
				});
				$('#imp003AtualizarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar a locação dos itens da importação', '/imp/imp003/content/imp003ContentAtualizarLocacao/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [500,120]);
					divRefresh('{$g['divIdParent']}', true);	
				});
				$('#imp003ImportarXML').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar XML', '/imp/imp003/content/imp003ContentImportarXML/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [500,120]);
					divRefresh('{$g['divIdParent']}', true);	
				});

				$('#imp003ConsultarDUIMP').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							imp003ExecutarConsultaDuimp();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Consultar DUIMP', 'Deseja consultar a DUIMP no Portal Único Siscomex e importar/atualizar os dados?', buttons);
				});

				function imp003ExecutarConsultaDuimp() {
					var processoId = '';
					var limiteLote = 5;
					var totalItens = 0;
					var totalProdutos = 0;
					var dialogId = 'imp003DuimpProgresso_{$novoIdTelaForm}';
					var pesosEtapas = { 1: 15, 2: 55, 3: 15, 4: 15 };
					var progressoEtapas = { 1: 0, 2: 0, 3: 0, 4: 0 };
					$('#' + dialogId).remove();
					$('body').append(''
						+ '<div id="' + dialogId + '" title="Consultar DUIMP">'
						+ '  <div style="font-size:12px; line-height:18px;">'
						+ '    <div id="' + dialogId + '_step1" style="margin-bottom:10px; border:1px solid #D9DEE7; padding:8px;">'
						+ '      <div><b>1. Consultando DUIMP no Portal Único</b> <span id="' + dialogId + '_step1_pct" style="float:right; font-weight:bold;">0%</span></div>'
						+ '      <div id="' + dialogId + '_step1_status" style="margin:4px 0; color:#555;">Aguardando execução</div>'
						+ '      <div style="height:14px; background:#E9EDF3; border:1px solid #D0D7E2;"><div id="' + dialogId + '_step1_bar" style="height:14px; width:0%; background:#5B9E3E;"></div></div>'
						+ '    </div>'
						+ '    <div id="' + dialogId + '_step2" style="margin-bottom:10px; border:1px solid #D9DEE7; padding:8px;">'
						+ '      <div><b>2. Processando itens da DUIMP</b> <span id="' + dialogId + '_step2_pct" style="float:right; font-weight:bold;">0%</span></div>'
						+ '      <div id="' + dialogId + '_step2_status" style="margin:4px 0; color:#555;">Aguardando execução</div>'
						+ '      <div style="height:14px; background:#E9EDF3; border:1px solid #D0D7E2;"><div id="' + dialogId + '_step2_bar" style="height:14px; width:0%; background:#5B9E3E;"></div></div>'
						+ '    </div>'
						+ '    <div id="' + dialogId + '_step3" style="margin-bottom:10px; border:1px solid #D9DEE7; padding:8px;">'
						+ '      <div><b>3. Fechando cabeçalho</b> <span id="' + dialogId + '_step3_pct" style="float:right; font-weight:bold;">0%</span></div>'
						+ '      <div id="' + dialogId + '_step3_status" style="margin:4px 0; color:#555;">Aguardando execução</div>'
						+ '      <div style="height:14px; background:#E9EDF3; border:1px solid #D0D7E2;"><div id="' + dialogId + '_step3_bar" style="height:14px; width:0%; background:#5B9E3E;"></div></div>'
						+ '    </div>'
						+ '    <div id="' + dialogId + '_step4" style="margin-bottom:10px; border:1px solid #D9DEE7; padding:8px;">'
						+ '      <div><b>4. Finalizando importação</b> <span id="' + dialogId + '_step4_pct" style="float:right; font-weight:bold;">0%</span></div>'
						+ '      <div id="' + dialogId + '_step4_status" style="margin:4px 0; color:#555;">Aguardando execução</div>'
						+ '      <div style="height:14px; background:#E9EDF3; border:1px solid #D0D7E2;"><div id="' + dialogId + '_step4_bar" style="height:14px; width:0%; background:#5B9E3E;"></div></div>'
						+ '    </div>'
						+ '    <div id="' + dialogId + '_erro" style="display:none; margin-top:10px; border:1px solid #E6B8BE; background:#FFF4F4; padding:10px;">'
						+ '      <div style="color:#B00020; font-weight:bold; margin-bottom:4px;">Erro na consulta</div>'
						+ '      <div id="' + dialogId + '_erro_resumo" style="color:#333; margin-bottom:4px;"></div>'
						+ '      <div id="' + dialogId + '_erro_detalhe" style="color:#B00020;"></div>'
						+ '    </div>'
						+ '  </div>'
						+ '</div>');

					var processDialog = $('#' + dialogId).dialog({
						modal: true,
						width: 560,
						resizable: false,
						draggable: false,
						closeOnEscape: false,
						open: function() {
							$(this).parent().find('.ui-dialog-titlebar-close').hide();
						},
						buttons: {
							'Fechar': function() {
								$(this).dialog('destroy').remove();
							}
						}
					});
					processDialog.parent().find('button:contains("Fechar")').button('disable');

					function liberarFechar() {
						processDialog.parent().find('button:contains("Fechar")').button('enable');
						processDialog.parent().find('.ui-dialog-titlebar-close').show();
					}

					function atualizarProgressoGeral() {
						return;
					}

					function atualizarEtapa(step, status, detalhe, percentual) {
						percentual = Math.max(0, Math.min(100, parseInt(percentual, 10) || 0));
						progressoEtapas[step] = percentual;
						$('#' + dialogId + '_step' + step + '_status').html(status + (detalhe ? '<br>' + detalhe : ''));
						$('#' + dialogId + '_step' + step + '_pct').text(percentual + '%');
						$('#' + dialogId + '_step' + step + '_bar').css('width', percentual + '%');
						atualizarProgressoGeral();
					}

					function erroProcesso(mensagem) {
						var detalhe = mensagem || 'Não foi possível concluir a operação.';
						$('#' + dialogId + '_erro_resumo').text('Não foi possível consultar a DUIMP no Portal Único.');
						$('#' + dialogId + '_erro_detalhe').text('Detalhe: ' + detalhe);
						$('#' + dialogId + '_erro').show();
						liberarFechar();
						if (processoId) {
							$.getJSON('/imp/imp003/form_func-ajax/consultarDuimpLimpar/?ajax=true&processoId=' + encodeURIComponent(processoId) + '&callback=?', function() {});
						}
					}

					function requisicao(url, callback) {
						$.getJSON(url + '&callback=?', function(request) {
							if (!request.code) {
								erroProcesso(request.data);
								return;
							}
							callback(request);
						});
					}

					function atualizarCabecalho() {
						atualizarEtapa(3, 'Executando', 'Fechando os totais do cabeçalho da D.I.', 25);
						requisicao('/imp/imp003/form_func-ajax/consultarDuimpGravarCabecalho/?ajax=true&processoId=' + encodeURIComponent(processoId), function() {
							atualizarEtapa(3, 'Concluído com sucesso', 'Cabeçalho fechado', 100);
							finalizar();
						});
					}

					function finalizar() {
						atualizarEtapa(4, 'Executando', 'Aplicando totalizações finais', 25);
						requisicao('/imp/imp003/form_func-ajax/consultarDuimpFinalizar/?ajax=true&processoId=' + encodeURIComponent(processoId), function(request) {
							atualizarEtapa(4, 'Concluído com sucesso', request.data, 100);
							liberarFechar();
							divRefresh('{$g['divId']}', true);
						});
					}

					function processarItens(offset) {
						var percentualAtual = totalItens > 0 ? Math.round((offset / totalItens) * 100) : 100;
						atualizarEtapa(2, 'Executando', 'Processados ' + offset + ' de ' + totalItens + ' itens', percentualAtual);
						requisicao('/imp/imp003/form_func-ajax/consultarDuimpProcessarItens/?ajax=true&processoId=' + encodeURIComponent(processoId) + '&offset=' + encodeURIComponent(offset) + '&limite=' + encodeURIComponent(limiteLote), function(request) {
							totalProdutos = request.totalProdutos || totalProdutos;
							var percentual = request.totalItens > 0 ? Math.round((request.processados / request.totalItens) * 100) : 100;
							var detalhe = 'Processados ' + request.processados + ' de ' + request.totalItens + ' itens';
							atualizarEtapa(2, request.finalizado ? 'Concluído com sucesso' : 'Executando', detalhe, percentual);
							if (request.finalizado) {
								atualizarCabecalho();
							} else {
								processarItens(request.processados);
							}
						});
					}

					atualizarEtapa(1, 'Executando', 'Autenticando e consultando versão/cabeçalho no Portal Único', 10);
					requisicao('/imp/imp003/form_func-ajax/consultarDuimpPreparar/?ajax=true&T075_Id=' + encodeURIComponent('{$r_acaoId}'), function(request) {
						processoId = request.processoId;
						totalItens = request.totalItensLista || 0;
						atualizarEtapa(1, 'Concluído com sucesso', 'DUIMP ' + request.numero + ' versão ' + request.versao + ' localizada', 100);
						if (totalItens > 0) {
							atualizarEtapa(2, 'Executando', totalItens + ' itens localizados para processamento', 5);
							processarItens(0);
						} else {
							atualizarEtapa(2, 'Concluído com sucesso', 'Nenhum item para importar', 100);
							atualizarCabecalho();
						}
					});
				}

				$('#imp003Calcular').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T075_Id = $('#{$g['divId']} #T075_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/Calcular/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divId']}', true);
										} 
									}
					    			dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Calcular', 'Você tem certeza? ', \$buttons);
				});
				$('#imp003GerarXMLLI').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/GerarXMLLI/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									dialogConfirm('Download XML','XML gerado com sucesso! <a href=\"/imp/imp003/form_func-ajax/downloadXMLLI/?T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&ajax=true\">Abrir Arquivo</a>');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar XML LI', 'Gerar XML LI? ', \$buttons);
				});
				$('#imp003Finalizar').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T075_Id = $('#{$g['divId']} #T075_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/imp007Finalizar/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divIdParent']}', true);
										} 
									}
					    			dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var mensagem = '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">'+request.data+'</textarea>';
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', mensagem, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('ATENÇÃO!', 'Este processo só pode ser executado uma vez.<br />Atualizar estoque<br />Você tem certeza? ', \$buttons);
				});

				$('#imp003AtualizarCusto').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T075_Id = $('#{$g['divId']} #T075_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/imp003AtualizarCusto/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divIdParent']}', true);
										} 
									}
					    			dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atualizar Custo', 'Deseja atualizar o custos dos produtos? ', \$buttons);
				});

				$('#imp003ImportarOC').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Importar Itens Ordem de Compra', '/imp/imp003/content/imp003contentImportarItensOC/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&divDI=' + encodeURIComponent('{$content1}'), [800,550]);
				});

				$('#imp003ExcluirItens').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp003/form_func-ajax/excluirItensMarcados/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Ok', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja excluir os itens marcados?', \$buttons);
				});
				
				$('#imp003AtualizarCadastroProdutoOpcoes').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Atualizar peso": function() {
							var \$buttons2 = {
								"Sim": function() {
									setTimeout(function() {							  
										$.getJSON('/imp/imp003/form_func-ajax/validaAtualizarPesoProdutos/?ajax=true&T075_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
											if (request.code) {
												$.getJSON('/imp/imp003/form_func-ajax/atualizarPesoProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
													if (request.code) {
														dialogConfirm('Atualizar peso produtos',request.data);
														divRefresh('{$g['divIdParent']}', true);
													} else {
														var \$buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
														dialogConfirm('Erro', request.data, \$buttons2);
													}
													$('#{$g['divId']}').hideLoading();
												});
											} else {
												var \$buttons2 = {
													"Sim": function() {
														$('#{$g['divId']}').showLoading();
														$.getJSON('/imp/imp003/form_func-ajax/atualizarPesoProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
															if (request.code) {
																divRefresh('{$g['divIdParent']}', true);
																dialogConfirm('Atualizar peso produtos',request.data);
															} else {
																var \$buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																dialogConfirm('Erro', request.data, \$buttons2);
															}
															$('#{$g['divId']}').hideLoading();
														});
														$(this).dialog("destroy").remove();
													},
													"Não": function() { $(this).dialog("destroy").remove(); } 
												};
												dialogConfirm('Atenção',request.data, \$buttons2);
											}
											$(this).dialog("destroy").remove();
										});
									}, 100);
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar o peso <b>liquído</b> e <b>bruto</b> no cadastro dos produtos com os valores preenchidos na DI?<br><span style="color: red;"><b>Ação não terá como ser revertida</b></span>', \$buttons2);
						},
						"Atualizar descrição inglês": function() {
							var \$buttons2 = {
								"Sim": function() {
									setTimeout(function() {							  
										$.getJSON('/imp/imp003/form_func-ajax/validaDescricaoProdutos/?ajax=true&T075_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
											if (request.code) {
												$.getJSON('/imp/imp003/form_func-ajax/atualizarDescricaoProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
													if (request.code) {
														dialogConfirm('Atualizar peso produtos',request.data);
														divRefresh('{$g['divIdParent']}', true);
													} else {
														var \$buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
														dialogConfirm('Erro', request.data, \$buttons2);
													}
													$('#{$g['divId']}').hideLoading();
												});
											} else {
												var \$buttons2 = {
													"Sim": function() {
														$('#{$g['divId']}').showLoading();
														$.getJSON('/imp/imp003/form_func-ajax/atualizarDescricaoProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
															if (request.code) {
																divRefresh('{$g['divIdParent']}', true);
																dialogConfirm('Atualizar peso produtos',request.data);
															} else {
																var \$buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																dialogConfirm('Erro', request.data, \$buttons2);
															}
															$('#{$g['divId']}').hideLoading();
														});
														$(this).dialog("destroy").remove();
													},
													"Não": function() { $(this).dialog("destroy").remove(); } 
												};
												dialogConfirm('Atenção',request.data, \$buttons2);
											}
											$(this).dialog("destroy").remove();
										});
									}, 100);
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar a <b>descrição em inglês</b> no cadastro dos produtos com os valores preenchidos na DI?<br><span style="color: red;"><b>Ação não terá como ser revertida</b></span>', \$buttons2);
						},
						"Atualizar custo ME": function() {
							var \$buttons2 = {
								"Sim": function() {
									setTimeout(function() {							  
										$.getJSON('/imp/imp003/form_func-ajax/atualizarCustoMEProdutos/?ajax=true&T075_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
											if (request.code) {
												dialogConfirm('Atualizar custo ME',request.data);
												divRefresh('{$g['divIdParent']}', true);
											} else {
												var \$buttons2 = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro', request.data, \$buttons2);
											}
											$('#{$g['divId']}').hideLoading();
										});
									}, 100);
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar o <b>custo ME</b> no cadastro dos produtos com os valores preenchidos na DI?</span>', \$buttons2);
						},
						"Cancelar": function() { 
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Atualizar Cadastro Produto', 'Qual atualização você deseja realizar no cadastro do produto? ', \$buttons);
				});
			} </script></div>
EOT;
	break;

	case 'imp003ContentAtualizarLocacao':
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
			array($content1, '/imp/imp003/form/imp003FormAtualizarLocacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp003ContentProdutos':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '183px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/imp/imp003/form/imp003FormProdutoIncluir/', '&tab=geral&T075_Id=' . urlencode($r_acaoId). "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp003/grid/imp003GridProduto/', '&acaoId=' . urlencode($r_acaoId). "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp003ContentDocumentos':
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
			array($content1, '/imp/imp003/form/imp003FormDocumentos/', "&tab=geral&T075_Id=" . urlencode($r_T075_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp003/grid/imp003GridDocumentos/', '&acaoId=' . urlencode($r_T075_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;

		case 'imp003ContentDocumentosDespesa':
			$T155_T075_Id = isset($r_T155_T075_Id) ? $r_T155_T075_Id : false;
			log("content do grid/form T155_T075_Id=".$T155_T075_Id);
	
			log("content do grid/form T168_Id=".$r_T168_Id);
			

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
			array($content1, '/imp/imp003/form/imp003FormDocumentosDespesa/', "&tab=geral&T155_T075_Id=" . urlencode($T155_T075_Id) .  "&T168_Id=" . urlencode($r_T168_Id)."&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp003/grid/imp003GridDocumentosDespesa/', '&acaoId=' . urlencode($r_acao) .  '&T155_T075_Id=' . urlencode($T155_T075_Id) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'imp003ContentEmail':
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
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&tab=geral&T075_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	
	
	break;
	
	case 'imp003ContentDespesasIncluir':
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		$T155_T075_Id = isset($r_T155_T075_Id) ? $r_T155_T075_Id : false;
		log("T155_T075_Id=".$T155_T075_Id);
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
			array($content1, '/imp/imp003/tab/imp003TabCadastroDespesas/', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($T075_Id) . '&T155_T075_Id=' . urlencode($T155_T075_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp003ContentLIIncluir':

		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
		log("======Content acaoID".$r_acaoId);
		log(";;;;;;;;Content T075_Id".$r_T075_Id);
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
			array($content1, '/imp/imp003/tab/imp003TabCadastroLI/', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($T075_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp003contentIncluirContasPagar':
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
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
			array($content1, '/imp/imp003/tab/imp003TabCadastroContasPagar/', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($T075_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp003contentIncluirContasReceber':
		
		$T075_Id = isset($r_T075_Id) ? $r_T075_Id : false;
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
			array($content1, '/imp/imp003/tab/imp003TabCadastroContasReceber/', '&acaoId=' . urlencode($r_acaoId) . '&T075_Id=' . urlencode($T075_Id))
		);
		echo gProcessaAcoes($actions);
		
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp003FormCadastroContasReceberbtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
    case 'imp003ContentPedidosProduto':
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
            array($content1, '/imp/imp003/tab/imp003TabPedidosProduto/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;	

    case 'imp003contentImportarItensOC':
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
            array($content1, '/imp/imp003/tab/imp003TabImportarItensOC/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

	case 'imp003contentImportarCSV':
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
           	array($content1, '/imp/imp003/form/imp003FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/imp/imp003/outro/imp003ManualCSVCliente/','&tab=geral')
		);
		echo  gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;

	case 'imp003ContentImportarXML':
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
			array($content1, '/imp/imp003/form/imp003FormImportarXML/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'imp003ContentAssociarItemProduto':
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
			array($content1, '/imp/imp003/tab/imp003TabAssociarItemProduto/', '&T055_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp003ContentPacoteDespesas':
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
			array($content1, '/imp/imp003/tab/imp003TabPacoteDespesas/', '&T055_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp003ContentItensImportadosDI':
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
			array($content1, '/imp/imp003/tab/imp003TabItensImportadosDI/', '&T075_Id=' . urlencode($r_T075_Id) . '&T055_Id=' . urlencode($r_T055_Id) . '&T310_Id=' . urlencode($r_T310_Id))
		);
		echo gProcessaAcoes($actions);

	break;
}
