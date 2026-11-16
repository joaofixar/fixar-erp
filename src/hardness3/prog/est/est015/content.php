<?php
namespace hardness;
/**
 * Ações
 */
require_once("bibliotecas/classes/EST015.php");
$EST015 = new EST015();
switch ($g['r'][3]) {
	/**
	 * Grid Principal
	 * Chamado em: Menu
	 */
	case 'est015ContentPrincipal':
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
			array($content1, '/est/est015/tab/est015TabPrincipal/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
		
	/**
	 * Orçamento
	 * Chamado em: est015GridPrincipal
	 */
	case 'est015ContentOrcamento':
		$r_D024_Id_CRM = (isset($r_D024_Id_CRM)) ? $r_D024_Id_CRM : false;
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		
		// Via tab catálogos do CRM
		$r_tabCatalogoCRM = (isset($r_tabCatalogoCRM)) ? $r_tabCatalogoCRM : false;
		$r_GerarCotacaoEstoque = (isset($r_GerarCotacaoEstoque)) ? $r_GerarCotacaoEstoque : false;

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
			array($content1, '/est/est015/tab/est015TabOrcamento/', '&acaoId=' . urlencode($r_acaoId) . '&T139_Id=' . urlencode(@$r_T139_Id) . '&T003_Id=' . urlencode($T003_Id) . '&D024_Id_CRM='.urlencode($r_D024_Id_CRM) . '&tabCatalogoCRM='.urlencode($r_tabCatalogoCRM) . '&GerarCotacaoEstoque='.urlencode($r_GerarCotacaoEstoque) . '&extra=' . urlencode($r_extra) . '&GerarCotacaoEmergencia=' . urlencode($r_GerarCotacaoEmergencia))
		);
		echo gProcessaAcoes($actions);

		break;
		
	/**
	 * Orçamento/Produtos/Serviços/Materiais
	 * Chamado em: est015GridPrincipal
	 */
	case 'est015ContentEditarOrcamento':
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;

		$sql="select T218_Id from T218 where T218_Id='{$r_acaoId}'";
		$consultaT218_Id=mysql_query($sql);
		
		if (!empty($r_acaoId) && mysql_num_rows($consultaT218_Id)<=0) {
			$alert = gGeraAlertMsg('Erro', 'Esta cotação de compra não existe!', false);
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

		$sql = mysql_query("SELECT * FROM T218 WHERE T218_Id = '{$r_acaoId}'");
		$mSql = mysql_fetch_array($sql);
		// Remoção das notificações de cotação
		if($mSql['T218_C007_Id_Vendedor_Interno'] == $g['usuarioAtual']){
			if($mSql['T218_Flag_Visualizou_Liberacao'] == 'N'){
				if($mSql['T218_Flag_Status_Liberacao'] == '3'){
					echo gGeraAlertMsg('Atenção', 'Esta cotação de compra foi <b>negada</b>.');
				} else {
					echo gGeraAlertMsg('Atenção', 'Esta cotação de compra foi <b>aprovada</b>.');
				}
				mysql_query("UPDATE T218 SET T218_Flag_Visualizou_Liberacao='S' WHERE T218_Id='{$r_acaoId}'");
			}

			if($mSql['T218_Flag_Notificacao'] == '1'){
				echo gGeraAlertMsg('Atenção', 'Esta cotação foi finalizada pelo fornecedor.');
				mysql_query("UPDATE T218 SET T218_Flag_Notificacao='2' WHERE T218_Id='{$r_acaoId}'");
			}
		}

		$D013_Id    = $mSql['T218_D013_Id'];
		$T218_Grupo = $mSql['T218_Grupo'];

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
			array($content1, '/est/est015/tab/est015TabEditarOrcamento/', '&acaoId=' . urlencode($r_acaoId) . '&T003_Id=' . urlencode($T003_Id) . '&divIdMain=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions, false);
		
		/**
		 * Botões
		 */			
		// Definição dos botões
		$botoes = array(
			array('titulo' => 'Desconto', 'id' => 'est015AplicarDescontoTabOrcamento', 'exibirCallback' => 'return ($g["C031"]["botaoAplicarDesconto"] == "S");'),
			array('titulo' => 'Importar', 'id' => 'est015ImportarOrcamentoTabOrcamento'),
			array('titulo' => 'Atualizar', 'id' => 'est015AtualizarOpcoes'),
			array('titulo' => 'E-mail', 'id' => 'est015EnviarEmail'),
			array('titulo' => 'WhatsApp', 'id' => 'est015EnviarWhats'),
			array('titulo' => 'Imprimir', 'id' => 'est015ImprimirOrcamento'),
			array('titulo' => 'Comparar', 'id' => 'est015AnaliseGrupo'),
			array('titulo' => 'Análise', 'id' => 'est015AnaliseOC'),
			array('titulo' => 'Fechar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita')
		);
		// Ações dos botões
		echo Botoes::processar($botoes, 'content');
		$alertAlcada = gGeraAlertMsg('Gerar Pedido', 'Você não tem permissão para gerar orçamentos!', false);
		//Verificar se o usuário possui alçadas para enviar e-mail e gerar pedido
		$usarAlcada = $g['C031']['usarAlcada'];
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est015DividirOrcamentoBtn').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Orçamento', '/est/est015/content/est015ContentDividirOrcamento/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
			});

			$('#est015AnaliseGrupo').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Produtos X Fornecedor - Grupo: {$T218_Grupo}', '/est/est015/content/est015ContentProdutosVsFornecedor/', '&tab=geral&T218_Grupo=' + encodeURIComponent("{$T218_Grupo}"), [1000,600]);
			});

			$('#est015GerarPedidoTabOrcamento').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$(this).dialog("destroy").remove();
						$.getJSON('/est/est015/form_func-ajax/gerarPedido/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								var \$buttons = {
									"Fechar": function() {
										$(this).dialog("destroy").remove();
										fecharJanela('{$g['divId']}');
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra - ID: ' + request.T224_Id, '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(request.T224_Id), [990,605]);
										divRefresh('{$g['divIdParent']}', true);
									} 
								};
								dialogConfirm('Ok', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Gerar Ordem de Compra', 'Você tem certeza que deseja gerar a ordem de compra?', \$buttons);
			});
			
			$('#est015AplicarIpvTabOrcamento').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar IPV', '/est/est015/content/est015ContentAplicarIpv/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
			});
			$('#est015AplicarDescontoTabOrcamento').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/est/est015/content/est015ContentAplicarDesconto/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [350,150]);
			});
			
			$('#est015ImportarOrcamentoTabOrcamento').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Ítens', '/est/est015/content/est015ContentImportarOrcamento/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
			});
			
			$('#est015AnaliseOC').unbind('click').bind('click', function(data) {
				//nova_janela('/est/est015/outro/est015AnaliseOC/?oneWindow=true&T218_Id=$r_acaoId',0,0,1220,700,'no','no','no','no','yes','yes',0);
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise de cotação', '/est/est015/outro/est015AnaliseOC/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
			});

			$('#est015AtualizarAliquotaBtn').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T218_Id = $('#{$g['divId']} #T218_Id').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Ok', request.data, \$buttons);
								divRefresh('{$g['divId']}');
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
				dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
			});
			
			$('#est015EnviarWhats').bind('click', function(data) {
				$.getJSON('/est/est015/form_func-ajax/est015PhoneWhats/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
					dialogConfirm('Enviar cotação WhatsApp: ' + decodeURIComponent(request1.phoneWhatsApp), "O que deseja fazer? " + decodeURIComponent(request1.notificaSemPhone) , [
						{
							text: "Enviar formato texto",
							click: function() {
								$.getJSON('/est/est015/form_func-ajax/orcamentoWhats/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + request1.phoneWhatsApp + '&callback=?', function(request) {
									nova_janela('https://api.whatsapp.com/send?phone=' + request1.phoneWhatsApp + '&text=' + request.data,0,0,750,500,'yes','no','no','no','yes','no','WhatsApp');
								});
								$(this).dialog("destroy").remove();
								return false;
							}
						},
						{
							text: "Enviar link PDF",
							click: function() {
								$.getJSON('/est/est015/form_func-ajax/orcamentoWhatsPDF/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + request1.phoneWhatsApp + '&callback=?', function(request) {
									nova_janela('https://api.whatsapp.com/send?phone=' + request1.phoneWhatsApp + '&text=' + request.data,0,0,750,500,'yes','no','no','no','yes','no','WhatsApp');
								});
								$(this).dialog("destroy").remove();
								return false;
							}
						},
EOT;
						if (!empty($D013_Id)) {
							echo <<<EOT
							{
								text: "Atualizar celular contato",
								click: function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar contato', '/cad/cad001/content/cad001ContentAlterarContato/', '&tab=geral&acaoId=' + encodeURIComponent("{$D013_Id}"), [600,300]);
									$(this).dialog("destroy").remove();
									return false;
								}
							},
EOT;
						}
						echo <<<EOT
						{
							text: "Cancelar",
							click: function() {
								$(this).dialog("destroy").remove();
							}
						}
					]);
				});
			});

			$('#est015EnviarEmail').unbind('click').bind('click', function(data) {
				$.getJSON('/est/est015/form_func-ajax/verificarParcelas/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
					if(request1.code){
						$.getJSON('/est/est015/form_func-ajax/verificaAlcadaNovo/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code === true) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
							} else {
								var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Enviar E-mail', request.data, \$buttons);
								var \$buttons = {
									"Sim": function() {
										$('#{$g['divId']}').showLoading();
										$(this).dialog("destroy").remove();
										$.getJSON('/est/est015/form_func-ajax/enviarParaAnalise/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request2) {
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
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request1.data, \$buttons);
					}
				});
			});

			$('#est015EnviarEmail').unbind('click').bind('click', function(data) {
				// CONFERIDO
				$.getJSON('/est/est015/form_func-ajax/verificaEmailGenerico/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
					if (request.code) {
						var \$buttons = {
							"E-mail PDF": function() {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email PDF', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
								$(this).dialog("destroy").remove();
							},
							"E-mail Link": function() { 
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email Link', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&emailLink=true', [900,550]);
								$(this).dialog("destroy").remove();
							},
							"Cancelar": function() { 
								$(this).dialog("destroy").remove();
							}
						};
						dialogConfirm('Enviar E-mail', 'Selecione uma opção: ', \$buttons);
					} else {
						dialogConfirm('Erro', request.data);
					}
				});	
			});

			$('#est015ImprimirOrcamento').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				// Conferido
				$.getJSON('/est/est015/form_func-ajax/verificaImprimirGenerico/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
					if(!request.code)
					{
						dialogConfirm('Erro', request.data);
						$('#{$g['divId']}').hideLoading();
					}
					else
					{
						$.getJSON('/est/est015/form_func-ajax/est015ImprimirOrcamento/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();		
							if (request.code) {
								var w = window.open(request.data);
								w.addEventListener("load", function(event) {
									setTimeout(function() {
										$.getJSON('/est/est015/form_func-ajax/est015DeletarPDFCotacao/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {});
									}, 3000);
								});
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
				});	
			});

			$('#est015Compras').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Compras', '/est/est015/content/est015ContentCompras/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [850,600]);
			});

			$('#est015ExcluirItems').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est015/form_func-ajax/excluirItensMarcados/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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


			$('#est015AtualizarOpcoes').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Atualizar Aliquotas": function() {
						$(this).dialog("destroy").remove();
						est015SelecionarCamposAtualizacao('aliquotas', function(opcoes) {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								idProgressBar = dialogAlert('Processando alíquotas',"<span id='progressBar'><b><br>0% Processando Alíquotas</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            est015AtualizarAliquotasLoop(idProgressBar,T218_Id,1,opcoes);
						});
					},
					"Atualizar CFOP": function() { 
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/est/est015/content/est015ContentAtualizarCFOP/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [500,300]);
						$(this).dialog("destroy").remove();
					},
					"Atualizar Aliquotas/CFOP Tabela": function() {
						$(this).dialog("destroy").remove();
						est015SelecionarCamposAtualizacao('tabela', function(opcoes) {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								idProgressBar = dialogAlert('Processando alíquotas',"<span id='progressBar'><b>0% Processando alíquotas</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            est015AtualizarAliquotasTabelaLoop(idProgressBar,T218_Id,1,opcoes);
						});
					},
					"Cancelar": function() { 
						$(this).dialog("destroy").remove();
					}
				};
				dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
			});

			$('#est015AtualizarOpcoesX').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/est/est015/content/est015ContentAtualizarCFOP/', '&tab=geral&T218_Id=' + encodeURIComponent("{$r_acaoId}"), [500,200]);
			});
			
			$('#est015AtualizarCusto').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Apenas esta venda": function() {
						var \$buttons2 = {
							"Sim": function() {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est015/form_func-ajax/atualizarCustoVenda/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}');
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
						$(this).dialog("destroy").remove();
						dialogConfirm('Atenção', 'Deseja atualizar o custo apenas para esta venda?', \$buttons2);
					},
					"Atualizar o produto": function() {
						var \$buttons2 = {
							"Sim": function() {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est015/form_func-ajax/atualizarCustoProduto/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}');
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
						$(this).dialog("destroy").remove();
						dialogConfirm('Atenção', 'Deseja atualizar o custo dos produtos selecionados?', \$buttons2);
					},
					"Atualizar tabela fornecedor": function() {
						var \$buttons2 = {
							"Sim": function() {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est015/form_func-ajax/atualizarTabelaFornecedor/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}');
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
						$(this).dialog("destroy").remove();
						dialogConfirm('Atenção', 'Deseja atualizar a tabela do fornecedor dos produtos selecionados?', \$buttons2);
					},
					"Cancelar": function() { $(this).dialog("destroy").remove(); }
				};
				var dialog = dialogConfirm('Atualizar Custos', '<b>Atenção!</b><br />Você deseja atualizar o custo:', \$buttons);
				var buttons = $('#dc-' + dialog).parent().find('.ui-dialog-buttonpane button')
				var botao0 = $(buttons[0]);
				var botao1 = $(buttons[1]);
				var botao2 = $(buttons[2]);
				if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoVenda']}'){
					$(botao0).attr('disabled', 'disabled').addClass('ui-state-disabled');
				}
				if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoProduto']}'){
					$(botao1).attr('disabled', 'disabled').addClass('ui-state-disabled');
				}
				if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoFornecedor']}'){
					$(botao2).attr('disabled', 'disabled').addClass('ui-state-disabled');
				}
			});

			function est015SelecionarCamposAtualizacao(tipo, concluir){
				var grupos = [
					{ titulo: 'Impostos', campos: [['ipi', 'IPI'], ['icms', 'ICMS'], ['st', 'ST'], ['pisCofins', 'PIS/COFINS']] },
					{ titulo: 'Dados do produto', campos: [['cfop', 'CFOP'], ['origem', 'Origem do produto'], ['ncm', 'NCM'], ['unidade', 'Unidade de medida'], ['peso', 'Peso do produto'], ['precoTabela', 'Preço da tabela do fornecedor']] }
				];
				if (tipo == 'aliquotas') grupos[1].campos = [['ncm', 'NCM'], ['unidade', 'Unidade de medida'], ['peso', 'Peso do produto']];
				var dialogo, botoes = {
					"Atualizar": function() {
						var opcoes = {};
						$('#dc-' + dialogo).find('input.est015CampoAtualizacao').each(function() { opcoes[$(this).attr('data-campo')] = $(this).is(':checked'); });
						$(this).dialog('destroy').remove();
						concluir(opcoes);
					},
					"Cancelar": function() { $(this).dialog('destroy').remove(); }
				};
				dialogo = dialogConfirm('Selecionar atualização', 'Marque os dados que deseja atualizar:', botoes);
				$('#dc-' + dialogo).dialog('option', 'width', 380);
				var area = $('#dc-' + dialogo).find('p');
				area.append('<br />');
				var colunas = $('<div>').css({'white-space':'nowrap'}).appendTo(area);
				for (var grupo = 0; grupo < grupos.length; grupo++) {
					var coluna = $('<div>').css({'display':'inline-block', 'vertical-align':'top', 'width': grupo === 0 ? '38%' : '62%', 'white-space':'normal'}).appendTo(colunas);
					$('<div>').text(grupos[grupo].titulo).css({'color':'#2e75b6', 'font-weight':'bold', 'margin-bottom':'3px', 'border-bottom':'1px solid #b7cde3'}).appendTo(coluna);
					for (var i = 0; i < grupos[grupo].campos.length; i++) {
						var campo = grupos[grupo].campos[i];
						$('<input type="checkbox" checked="checked" class="est015CampoAtualizacao">').attr('data-campo', campo[0]).appendTo(coluna);
						$('<label>').html(campo[1]).appendTo(coluna).append('<br />');
					}
				}
			}

			function est015AtualizarAliquotasLoop(idProgressBar, T218_Id, inicio, opcoes){
                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>' + request.percentual + '% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
						est015AtualizarAliquotasLoop(idProgressBar,T218_Id,0,opcoes);
                    }else{
                    	if (request.code) {
                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
                    		if(request.erro == 0){
                    			dialogConfirm('Alíquotas processadas','<b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    		}else{
								dialogConfirm('Alíquotas processadas','<b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    		}
                    		divRefresh('{$g['divId']}', true);
                        } else {
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                            dialogConfirm('Erro!', request.data || request.erros || 'Não foi possível atualizar as alíquotas.');
                            divRefresh('{$g['divId']}', true);
                        }
                    } 
                });	                    
	        }

	        	function est015AtualizarAliquotasTabelaLoop(idProgressBar, T218_Id, inicio, opcoes){
                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + encodeURIComponent("{$r_acaoId}") + '&usarTabelaFornecedor=true' +  '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>' + request.percentual + '% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
						est015AtualizarAliquotasTabelaLoop(idProgressBar,T218_Id,0,opcoes);
                    }else{
                    	if (request.code) {
                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
                    		if(request.erro == 0){
                    			dialogConfirm('Alíquotas processadas','<b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    		}else{
								dialogConfirm('Alíquotas processadas','<b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    		}
                    		divRefresh('{$g['divId']}', true);
                        } else {
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                            dialogConfirm('Erro!', request.data || request.erros || 'Não foi possível atualizar as alíquotas.');
                            divRefresh('{$g['divId']}', true);
                        }
                    } 
                });	                    
	        }

		} </script></div>
EOT;

		// restrições
// 		$restricao = $g['conf']->restricaoTravar('alterar', 'T218_Id', $r_acaoId);
// 		if ($restricao !== true) {
// 			echo <<<EOT
// 				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
// 					$('#est015ImportarOrcamentoTabOrcamento').attr('disabled', 'disabled').unbind('click');
// 					$('#est015DividirOrcamentoBtn').attr('disabled', 'disabled').unbind('click');
// 				} </script></div>
// EOT;
// 		}	
	break;
	
	/**
	 * Tab Produtos
	 * Chamado em: est015TabEditarOrcamento
	 */
	case 'est015ContentProdutos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$divOutro = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '220px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			//array($content1, '/est/est015/form/est015FormProdutos/', '&tab=produto&T218_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content1, '/est/est015/content/est015contentform/', '&T218_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2) . '&divOutro=' . urlencode($divOutro)),
			array($content2, '/est/est015/grid/est015GridProdutos/', '&acaoId=' . urlencode($r_acaoId) . '&T218_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($divOutro)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		//$botoesDir = '';
		//$botoesEsq .= '<button type="submit" id="est015ContetnProdutosBtnConfirmarTudo">Confirmar Tudo</button>';
		//$botoesEsq .= '<button type="submit" id="est015ContetnProdutosBtnZerarTudo">Zerar Tudo</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		//$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
			$('#est015ContetnProdutosBtnConfirmarTudo').unbind('click').bind('click', function(data) {
				$.getJSON('/est/est015/form_func-ajax/confirmarTudo/?ajax=true&T218_Id=' + encodeURIComponent('$r_acaoId') + '&callback=?', function(request) {
				divRefresh('{$content2}'); 
				});
			});
			$('#est015ContetnProdutosBtnZerarTudo').unbind('click').bind('click', function(data) {
				$.getJSON('/est/est015/form_func-ajax/zerarTudo/?ajax=true&T218_Id=' + encodeURIComponent('$r_acaoId') + '&callback=?', function(request) {
				divRefresh('{$content2}'); 
				});
			});
		} </script></div>
EOT;
		
	break;
	
	case 'est015contentform':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $r_divOutro);
		$g['smarty']->assign('content1Width', '350px');
		$g['smarty']->display('content-2columnsOrcamento.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormProdutos/', '&tab=produto&T218_Id=' . urlencode($r_T218_Id) . '&divReload=' . urlencode($r_divReload) . "&divOutro=" . urldecode($r_divOutro)),
			array($r_divOutro, '/est/est015/outro/est015observacoes/', '&acaoId=' . urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est015contentServicos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '220px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormServicos/', '&tab=servicos'. '&T218_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est015/grid/est015GridServicos/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est015contentTrocas':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '180px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormTrocas/', '&tab=trocas'. '&T218_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est015/grid/est015GridTrocas/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;	

	
	case 'est015ContentEditarServico':
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
			array($content1, '/est/est015/tab/est015TabEditarServico/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est015contentProcessos':
				/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormProcessos/', '&tab=geral&T223_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2)),
			array($content2, '/est/est015/grid/est015grid05Processos/', '&T223_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);


	break;
	
	case 'est015contentMateriais':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormMateriais/', '&tab=geral&T223_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2)),
			array($content2, '/est/est015/grid/est015GridMateriais/', '&T223_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions); 
	break;
	
	/**
	* @author RubiaYwata
	* @since 16/11/2011
	*/
	case 'est015contentTotais':
    	
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);


		
		$g['smarty']->assign('tipo', '2colunas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormTotalOrcamento/', '&tab=geral&T218_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($g['divId']).'&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/est/est015/form/est015FormTotalConfirmado/', '&tab=geral2&T218_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($g['divId']).'&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	    
	
	break;
	
	case 'est015ContentEditarProduto':

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
			array($content1, '/est/est015/tab/est015TabEditarProduto/', '&acaoId=' . urlencode($r_acaoId) . '&T218_Id='. urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);
				/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est015FormEditarProdutosBtn">Alterar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
	
	case 'est015ContentAplicarIpv':

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
			array($content1, '/est/est015/form/est015FormAplicarIpv/', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est015AplicarIpvBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est015AplicarIpvBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T218_Id = $('#{$g['divId']} #T218_Id').val();
						var IPV = $('#{$g['divId']} #IPV').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est015/form_func-ajax/aplicarIPV/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&IPV=' + encodeURIComponent(IPV) + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
							fecharJanela('{$g['divId']}');
							divRefresh('{$g['divIdParent']}');
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Gerar Pedido', 'Você tem certeza que deseja gerar a Cotação de Compra? ', \$buttons);
			});
			} </script></div>
EOT;

	break;
	

	case 'est015ContentAplicarDesconto':

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
		array($content1, '/est/est015/tab/est015TabAplicarDesconto/', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est015ContentImportarOrcamento':

		/*Im
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormImportarOrcamento/', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est015ImportarOrcamentoBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		
		/*
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est015ImportarOrcamentoBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T218_Id = $('#{$g['divId']} #T218_Id').val();
						var T218_Id_Origem = $('#{$g['divId']} #T218_Id_Origem').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est015/form_func-ajax/ImportarOrcamento/?ajax=true&T218_Id=' + encodeURIComponent(T218_Id) + '&T218_Id_Origem=' + encodeURIComponent(T218_Id_Origem) + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
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
				dialogConfirm('Gerar Pedido', 'Você tem certeza que deseja gerar o pedido? ', \$buttons);
			});
			} </script></div>
EOT;
		 * *
		 */

	break;
	
	/**
	* @author AndreySliwinski
	* @since 23/11/2011
	*/
	case 'est015contentPagamentos':
    	
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '150px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est015/form/est015FormPagamentos/', '&tab=geral&T218_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2).'&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/est/est015/grid/est015GridPagamentos/', '&tab=geral2&T218_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content1).'&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	    
	
	break;
	
	case 'est015ContentEmail':
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
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&T218_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

	break;
	
	case 'est015ContentCancelamentoOrcamento':
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
			array($content1, '/est/est015/form/est015FormCancelarOrcamento/', '&tab=geral&T218_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

	break;
	
	case 'est015ContentDocumentos':
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
		array($content1, '/est/est015/form/est015FormDocumentos/', "&tab=geral&T218_Id=" . urlencode($r_T218_Id) .  "&divReload=" . urlencode($content2)),
		array($content2, '/est/est015/grid/est015GridDocumentos/', '&acaoId=' . urlencode($r_T218_Id) .  "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'est015ContentDividirOrcamento':
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
        array($content1, '/est/est015/tab/est015TabPrincipalDividir/', '&T218_Id=' . urlencode($r_T218_Id) . '&divRefresh=' . urlencode($g['divId']))
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
        
    case 'est015ContentAgregados':
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $content2 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('content2', $content2);
        $g['smarty']->assign('content1Height', '90px');
        $g['smarty']->assign('tipo', '2linhas');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/est/est015/form/est015FormAgregados/', '&tab=geral&T219_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
            array($content2, '/est/est015/grid/est015GridAgregados/', '&acaoId=' . urlencode($r_acaoId) . '&T219_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
        );
        echo gProcessaAcoes($actions);
        
        /**
         * Botões
         */
        // Definição dos botões
        $botoesEsq = '';
        //$botoesDir = '';
        //$botoesEsq .= '<button type="submit" id="est015ContetnProdutosBtnConfirmarTudo">Confirmar Tudo</button>';
        //$botoesEsq .= '<button type="submit" id="est015ContetnProdutosBtnZerarTudo">Zerar Tudo</button>';
        //$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        //$g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        $novoIdTelaForm = uniqid();        
    break;

    case 'est015ContentSelecionarEmpresa':
		$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
		$T218_Id = (isset($r_T218_Id)) ? $r_T218_Id : false;
		//$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;
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
        array($content1, '/est/est015/tab/est015TabSelecionarEmpresa/', "&T218_Id=" . urlencode($r_T218_Id) . '&divRefresh=' . urlencode($r_divRefresh) . '&mudarFilial=' . urlencode($mudarFilial)),
        );
        echo gProcessaAcoes($actions);            
    
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="est015SelecionarEmpresa_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');                              
    break;

    case 'est015ContentCompras':
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
			array($content1, '/est/est015/tab/est015TabCompras/', '&T218_Id=' . urlencode($r_T218_Id))
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

	case 'est015ContentCotar':
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
			array($content1, '/est/est015/tab/est015TabCotar/', '&T218_Id=' . urlencode($r_T218_Id) . '&T219_Id=' . urlencode($r_T219_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
        );
        echo gProcessaAcoes($actions);
        
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="est015FormCotar_btn">Cotar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est015ContentAtualizarCFOP':
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
			array($content1, '/est/est015/form/est015FormAtualizarCFOP/', '&tab=geral&T218_Id=' . urlencode($r_T218_Id)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est015ContentPrincipalAnalise':
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
			array($content1, '/est/est015/tab/est015TabPrincipalAnalise/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
	case 'est015ContentNaoLiberar':
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
			array($content1, '/est/est015/tab/est015TabNaoLiberar/', '&T218_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est015ContentProdutosVsFornecedor':
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
			array($content1, '/est/est015/tab/est015TabProdutosVsFornecedor/', '&T218_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est015ContentCotacoesGrupo':
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
			array($content1, '/est/est015/tab/est015TabCotacoesGrupo/', '&T218_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;
}
