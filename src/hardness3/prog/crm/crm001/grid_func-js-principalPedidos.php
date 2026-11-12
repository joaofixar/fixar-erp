<?php
namespace hardness;
global $g;

	$sqlEtiqueta  = " SELECT D175.D175_Nome ";
	$sqlEtiqueta .= "   FROM D175 ";
	$sqlEtiqueta .= "  WHERE D175.D175_Tipo_Impressora = 6 ";
	$sqlEtiqueta .= "  ORDER BY D175.D175_Id ASC ";
	$sqlEtiqueta .= "  LIMIT 1 ";
	$fetchEtiqueta = mysql_query($sqlEtiqueta);
	$dadosEtiqueta = mysql_fetch_array($fetchEtiqueta);
	$impressoraEtiqueta = $dadosEtiqueta['D175_Nome'];

            if ($g['C031']['agruparPedidosEmEtapas'] == 'S') {
                echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    function crm001AgruparPedidosEmEtapasLoop(idProgressBar, processoKey) {
                        $.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidosEmEtapas/?ajax=true&processoKey=' + encodeURIComponent(processoKey) + '&callback=?', function(request) {
                            if (!request.code) {
                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                dialogConfirm('Erro', request.data, false);
                                $('#{$g['divId']}').hideLoading();
                                return;
                            }

                            $('#progressBar').html('<b>Processado ' + request.percentual + '%</b><br/><br/>Itens processados: <b>' + request.ok + '</b><br/>Itens restantes: <b>' + request.restante + '</b>');

                            if (request.concluido == 0) {
                                crm001AgruparPedidosEmEtapasLoop(idProgressBar, processoKey);
                                return;
                            }

                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                            dialogConfirm('Ok', 'Pedidos agrupados com sucesso!', false);
                            $('#{$g['divId']}').hideLoading();
                            divRefresh('{$g['divId']}');
                        }).fail(function() {
                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                            $('#{$g['divId']}').hideLoading();
                            dialogConfirm('Erro', 'Ocorreu um erro ao processar o agrupamento em etapas.', false);
                        });
                    }

                    $('#ven002AgruparPedidos, #{$g['divId']} button[botaoid="ven002AgruparPedidos"]').unbind('click.agruparPedidosEtapas').bind('click.agruparPedidosEtapas', function(data) {
                        $('#{$g['divId']}').showLoading();
                        $.getJSON('/crm/crm001/grid_func-ajax/crm001AgruparPedidosEmEtapas/?ajax=true&sqlSelecao=' + encodeURIComponent('{$sqlSelecao}') + '&divIdParent=' + encodeURIComponent('{$g['divId']}') + '&callback=?', function(request) {
                            if (request.code && request.processoKey) {
                                idProgressBar = dialogAlert('Agrupar pedidos', "<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
                                crm001AgruparPedidosEmEtapasLoop(idProgressBar, request.processoKey);
                                return;
                            }

                            if (request.code) {
                                dialogConfirm('Ok', request.data, false);
                                divRefresh('{$g['divId']}');
                            } else {
                                dialogConfirm('Erro', request.data, false);
                            }
                            $('#{$g['divId']}').hideLoading();
                        }).fail(function() {
                            $('#{$g['divId']}').hideLoading();
                            dialogConfirm('Erro', 'Ocorreu um erro ao iniciar o agrupamento em etapas.', false);
                        });
                    });
                } </script></div>
EOT;
            } else {
                echo gProcessaEtapas('Agrupar pedidos','VEN002', "VEN002VerificarAgruparPedidosSelecionados();VEN002AgruparPedidosSelecionados()|false", "$sqlSelecao",'ven002AgruparPedidos');
            }
			$sqlSelecaoDuplicarEmpresa = urlencode($sqlSelecao);
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002DuplicarPedidos, #{$g['divId']} button[botaoid="ven002DuplicarPedidos"]').unbind('click.duplicarPedidos').bind('click.duplicarPedidos', function(data) {
					var botoes = {
						"Empresa atual": function() {
							$(this).dialog("destroy").remove();
							processaEtapas("Duplicar pedidos", "VEN002", "VEN002VerificarDuplicarPedidosSelecionados();VEN002DuplicarPedidosSelecionados()", "{$sqlSelecao}", 0, "{$g['divId']}", null, null, false);
						},
						"Mudar empresa": function() {
							$(this).dialog("destroy").remove();
							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Duplicar e mudar empresa', '/ven/ven002/content/ven002ContentDuplicarPedidoEmpresa/', '&sqlSelecao={$sqlSelecaoDuplicarEmpresa}', [500,150]);
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Duplicar pedidos', 'Deseja iniciar o processo de duplicação?', botoes);
				});
			} </script></div>
EOT;
			echo gProcessaEtapas('Processar vencidos','VEN002', 'VEN002ProcessarVencidos()', "$sqlVencidos",'ven002ProcessarPedidosVencidos');
			echo gProcessaEtapas('Faturar pedidos','VEN002', 'faturarPedidosSelecionadosPedido()', "$sqlSelecao", 'ven002FaturarPedidos');

            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven002importarMagento2, #{$g['divId']} button[botaoid="ven002importarMagento2"]').bind('click', function(data) {
					$.getJSON('/crm/crm001/grid_func-ajax/crm001ImportarMagento2/?ajax=true&callback=?', function(request) {
						if (request.code) { 
							dialogConfirm('Pedidos importados Magento', request.data, false);
						} else {	
							dialogConfirm('Erro', request.data, false);
						}
					});	
				});
				
				$('#ven002AtualizarPedidosPluggto, #{$g['divId']} button[botaoid="ven002AtualizarPedidosPluggto"]').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidosMarcadosPluggto/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
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
						dialogConfirm('Atualizar', 'Atualizar dados do faturamento dos pedidos selecionados? ', \$buttons);
					});


				$('#ven002importarMagento1Oauth, #{$g['divId']} button[botaoid="ven002importarMagento1Oauth"]').bind('click', function(data) {
					window.open('http://{$EnderecoHTTP}/crm/crm001/outro/Magento1GetOrdersOauth?ajax=true');
				});
				$('#ven002CancelarPedidos, #{$g['divId']} button[botaoid="ven002CancelarPedidos"]').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cancelar Pedidos', '/crm/crm001/content/crm001ContentCancelarPedidos/', '&extra=' + encodeURIComponent('{$extra_}'), [700,500]);
				});
				$('#ven002AtualizarOpcoesPedidos, #{$g['divId']} button[botaoid="ven002AtualizarOpcoesPedidos"]').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons2 = {
								"Sim": function() {
									idProgressBar = dialogAlert('Atualizando alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarAliquotasPedidosLoop(idProgressBar,1); 
						            $(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atenção', 'Deseja atualizar as aliquotas dos pedidos selecionados?', \$buttons2);
						},
						"Atualizar CFOP": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven002/content/ven002ContentAtualizarCFOPPedidos/', '&tab=geral&extra=' + encodeURIComponent('{$extra__}'), [500,250]);
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
				});

				function ven002AtualizarAliquotasPedidosLoop(idProgressBar, inicio){
	                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquotaPedidos/?ajax=true&T005_Id=' + encodeURIComponent("") + '&extra=' + encodeURIComponent("{$extra__}") + '&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	ven002AtualizarAliquotasPedidosLoop(idProgressBar,0);
	                    }else{
	                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                    	dialogConfirm('Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                			$(this).dialog("destroy").remove();     
	                        } else {
	                            dialogConfirm('Erro!', request.data);
	                        }
	                    } 
	                });	                  
		        }
		        $('#ven002importarTray, #{$g['divId']} button[botaoid="ven002importarTray"]').bind('click', function(data) {
					var \$buttons = {
						"Importar Tray": function() { 
							var \$buttons1 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
								    $.getJSON('/crm/crm001/grid_func-ajax/crm001ImportarTray/?ajax=true&callback=?', function(request) {
								        $('#' + '{$g['divId']}').hideLoading();
								        if (request.code) { 
								           dialogConfirm('Pedidos importados Tray', request.data, false);
								           $('#' + '{$g['divId']}').hideLoading();
								        } else {
								            dialogConfirm('Erro', request.data, false);
								            $('#' + '{$g['divId']}').hideLoading();
								        }
									}).fail(function(jqXHR, textStatus, errorThrown) {
										$('#' + '{$g['divId']}').hideLoading();
										var mensagem = "<b><font color='red'>Ocorreu um erro ao transmitir NF</font></b>";
										dialogConfirm('Erro', 'Ocorreu um erro ao sincronizar os pedidos', false);
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Importar pedidos', 'Você quer mesmo importar os pedidos da Tray?  ', \$buttons1);
						},
						"Atualizar Tray": function() {
							var \$buttons2 = {
								"Sim": function() {
									idProgressBar = dialogAlert('Atualizando pedidos',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarPedidosTrayLoop(idProgressBar,1); 
						            $(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atualizar dados', 'Atualizar dados do faturamento dos pedidos selecionados? ', \$buttons2);
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Tray', 'Selecione a ação desejada. ', \$buttons);
				});

				function ven002AtualizarPedidosTrayLoop(idProgressBar, inicio){
                	$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidosMarcadosTray/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	ven002AtualizarPedidosTrayLoop(idProgressBar,0);
                    }else{
                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
                    	dialogConfirm('Pedidos atualizados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	if (request.code) {
                            divRefresh('{$g['divId']}');
                			$(this).dialog("destroy").remove();     
                        } 
                    } 
	                });	                  
		        }

		        $('#ven002EnvioIntelipostPedidos, #{$g['divId']} button[botaoid="ven002EnvioIntelipostPedidos"]').bind('click', function(data) {

					var btnOpt = {
						"Enviar dados": function(){
							$(this).dialog("destroy").remove();
							var \$buttons = {
								"Sim": function() {
									idProgressBar = dialogAlert('Atualizando pedidos',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									ven002AtualizarPedidosIntelipostLoop(idProgressBar,1); 
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('Enviar', 'Enviar dados dos pedidos selecionados para a intelipost? ', \$buttons);
						},
						"Importar status": function() {
							$(this).dialog("destroy").remove();
							var \$buttons = {
								"Sim": function() {
									idProgressBar = dialogAlert('Atualiza Status Intelipost',"<span id='progressBar'>Iniciando...</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									crm001AtualizarStatusIntelipostLoop(idProgressBar);
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('<span style="color:red"><b>Atenção!</b></span>', 'Confirma atualizar status dos pedidos <span style="color:red"><b>selecionados</b></span>?<br>Essa operação pode levar algum tempo.', \$buttons);
						},
						"Cancelar": function(){
							$(this).dialog("destroy").remove();
						}
					}
					dialogConfirm('Intelipost', 'Selecione a operação', btnOpt);

				});

				function crm001AtualizarStatusIntelipostLoop(idProgressBar, info = false)
				{
					$.ajax({
						url: '/crm/crm001/grid_func-ajax/crm001AtualizarStatusIntelipost',
						type: 'POST',
						dataType: 'jsonp',
						data: {
							'ajax': true,
							'extra': (!info) ? "{$extra_}" : info,
						}
					}).done(function(r, t1, t2){
						if(!info)
						{
							info = r.info;

							if(info.contagem > 0)
							{
								$('#progressBar').html("<span id='INTELIPOST_PERCENT'>Processando <b>0/" + info.contagem +"</b></span><br>" +
								"<span id='INTELIPOST_ATT'>Pedidos atualizados: </span><br>" +
								"<span id='INTELIPOST_MSMID'>Pedidos com mesmo status: </span><br>" +
								"<span id='INTELIPOST_ERROS'>Pedidos com erro: </span><br>");

								crm001AtualizarStatusIntelipostLoop(idProgressBar, info);
							}
							else
							{
								$('#dc-' + idProgressBar).dialog("destroy").remove();
								dialogConfirm('Atualiza Status Intelipost', 'Nenhum pedido selecionado');
							}
						}
						else
						{
							info = r.info

							switch(r.data.code)
							{
								case 'ok':
									$('#INTELIPOST_PERCENT').html('Processando: <b>' + r.data.etapa + ' (' + r.data.percent + '%)' + '</b>');
									$('#INTELIPOST_ATT').html($('#INTELIPOST_ATT').html() + '<b>' + r.data.mainId + '</b>, ');
									crm001AtualizarStatusIntelipostLoop(idProgressBar, info);
									break;
								case 'msmStatus':
									$('#INTELIPOST_PERCENT').html('Processando: <b>' + r.data.etapa + ' (' + r.data.percent + '%)' + '</b>');
									$('#INTELIPOST_MSMID').html($('#INTELIPOST_MSMID').html() + '<b>' + r.data.mainId + '</b>, ');
									crm001AtualizarStatusIntelipostLoop(idProgressBar, info);
									break;
								case 'erro':
									$('#INTELIPOST_PERCENT').html('Processando: <b>' + r.data.etapa + ' (' + r.data.percent + '%)' + '</b>');
									$('#INTELIPOST_ERROS').html($('#INTELIPOST_ERROS').html() + '<b>' + r.data.mainId + '</b>, ');
									crm001AtualizarStatusIntelipostLoop(idProgressBar, info);
									break;
								case 'concluido':
									divRefresh('{$g['divId']}');
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									dialogConfirm('Atualiza Status Intelipost', 'Processo finalizado.');
									break;
							}

						}
					});
				}

				function ven002AtualizarPedidosIntelipostLoop(idProgressBar, inicio){
                	$.getJSON('/ven/ven002/form_func-ajax/criarPedidosMarcadosIntelipost/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	ven002AtualizarPedidosIntelipostLoop(idProgressBar,0);
	                    }else{
	                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                    	dialogConfirm('Pedidos atualizados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
	                    	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                			$(this).dialog("destroy").remove();     
	                        } 
	                    } 
	                });	                  
		        }

		        $('#ven002MagentoSoap, #{$g['divId']} button[botaoid="ven002MagentoSoap"]').bind('click', function(data) {
					var \$buttons = {
						"Importar Magento": function() {
							var \$buttons2 = {
								"Sim": function() {
									idProgressBar = dialogAlert('Importação pedidos',"<span id='progressBar'>Buscando pedidos</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /><br/>");
						            ven002ImportarPedidosMagentoLoop(idProgressBar,1); 
						            $(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Importar pedidos', 'Deseja importar os pedidos do Magento? ', \$buttons2);
						},
						"Atualizar Magento": function() {
							var \$buttons2 = {
								"Sim": function() {
									idProgressBar = dialogAlert('Atualizando pedidos',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarPedidosMagentoLoop(idProgressBar,1); 
						            $(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atualizar dados', 'Atualizar dados do faturamento dos pedidos selecionados? ', \$buttons2);
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Magento', 'Selecione a ação desejada. ', \$buttons);	
				});

				function ven002AtualizarPedidosMagentoLoop(idProgressBar, inicio){
                	$.getJSON('/ven/ven002/form_func-ajax/atualizarPedidosMarcadosMagento/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	ven002AtualizarPedidosMagentoLoop(idProgressBar,0);
                    }else{
                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
                    	dialogConfirm('Pedidos atualizados','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Pedidos atualizados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                    	if (request.code) {
                            divRefresh('{$g['divId']}');
                			$(this).dialog("destroy").remove();     
                        } 
                    } 
	                });	                  
		        }

				function ven002ImportarPedidosMagentoLoop(idProgressBar, inicio){
	                $.getJSON('/crm/crm001/grid_func-ajax/crm001ImportarMagentoSoap/?ajax=true&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b><br/><br/>Pedidos encontrados: <b>' + request.encontrados + '</b><br/>Pedidos importados anteriormente: <b> ' + request.repetidos + '</b><br/>Pedidos importados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br/>Pedidos não filtrados: <b> ' + request.qteNaoFiltrado + '</b><br/><br/>Duração:<b>' + request.duracao + '</b>' + '</b><br/><br/>Pedidos importados<br/><b> ' + request.sucesso + '</b>'  + '</b><br/><br/>Pedidos com erro<br/><b> ' + request.fracasso + '</b>' + '</b><br/><br/>Pedidos não filtrados<br/><b> ' + request.naoFiltrado + '</b>');
	                    	ven002ImportarPedidosMagentoLoop(idProgressBar,0,request.encontrados,request.repetidos,request.dateStart);
	                    }else{
	                    	if (request.code) {
	                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
	                    		dialogConfirm('Importação Magento','<b>Importação concluída</b></br></br>Pedidos encontrados: <b>' + request.encontrados + '</b><br/>Pedidos importados anteriormente: <b> ' + request.repetidos + '</b><br/>Pedidos importados: <b>' + request.ok + '</b><br/>Pedidos com erro: <b> ' + request.erro + '</b><br/>Pedidos não filtrados: <b> ' + request.qteNaoFiltrado + '</b><br/><br/>Duração:<b>' + request.duracao + '</b>' + '</b><br/><br/>Pedidos importados<br/><b> ' + request.sucesso + '</b>'  + '</b><br/><br/>Pedidos com erro<br/><b> ' + request.fracasso + '</b>' + '</b><br/><br/>Pedidos não filtrados<br/><b> ' + request.naoFiltrado + '</b>');
	                        } else {
	                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                            dialogConfirm('Erro!', request.data);
	                        }
	                    } 
	                });	                    
		        }

				$('#xven002FaturarPedidos, #{$g['divId']} button[botaoid="xven002FaturarPedidos"]').bind('click', function(data) {
					$('#' + '{$g['divId']}').showLoading();
					$.getJSON('/crm/crm001/grid_func-ajax/crm001VerificaFaturarPedidos/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
						$('#' + '{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Sim": function() {
									$('#' + '{$g['divId']}').showLoading();
									$.getJSON('/crm/crm001/grid_func-ajax/crm001FaturarPedidos/?ajax=true&extra=' + encodeURIComponent('{$extra_}') + '&callback=?', function(request) {
										$('#' + '{$g['divId']}').hideLoading();
										if (request.code) {
											dialogConfirm('Sucesso', request.data);
											divRefresh('{$g['divId']}');
										}else{
											divRefresh('{$g['divId']}');
											dialogConfirm('Erro', request.data);
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('Faturar pedidos?', request.data, \$buttons);
						}else{
							dialogConfirm('Erro', request.data);
						}
					});
				});


				//call do botao para as etiquetas de transportadoras
				$('#crm001etiquetasTrasnp, #{$g['divId']} button[botaoid="crm001etiquetasTrasnp"]').bind('click', function(data) {

					let checkSelecionados = document.querySelectorAll('input:checked[data-checkboxid]');
			        const listaIdCheckbox = [];
			       
			        [...checkSelecionados].forEach((element) => {
			            listaIdCheckbox.push(element.getAttribute("data-checkboxid"));
			            console.log(listaIdCheckbox);
			        });
			        listaIdCheckbox.forEach(function(T005_Id) {
						debugger;
						$.ajax({
							url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
							type: 'GET',
							dataType: 'jsonp',
							data: {
								'ajax': 'true',
								'acaoId': 'ab71e3720825c0214e7e11861a3e4f29',
								'link': '/sistema/personalizacao/tela/ab71e3720825c0214e7e11861a3e4f29/?ajax=true&acaoId={$T005_Id}',
							},
							success: function(resposta) {
								$('#{$g['divId']}').hideLoading();
								console.log(resposta);
								console.log(resposta.data);
								window.open(resposta.data, '_blank');
							}
						});


			        });
					
				});

				//call do botao para as etiquetas do Mercado Livre
				$('#crm001etiquetasML, #{$g['divId']} button[botaoid="crm001etiquetasML"]').bind('click', function(data) {

					let checkSelecionados = document.querySelectorAll('input:checked[data-checkboxid]');
			        const listaIdCheckbox = [];
			       
			        [...checkSelecionados].forEach((element) => {
			            listaIdCheckbox.push(element.getAttribute("data-checkboxid"));
			            console.log(listaIdCheckbox);
			        });
			        listaIdCheckbox.forEach(function(T005_Id) {

				        //Os parametros da URL são padrões para a call funcionar
						//passar o ID do pedido externo para funcionar a chamada 
						$.ajax({
						  url: '/outros/integracaoSMT/integrador/integracaoTray/etiquetasML?ajax=true&noEmp=true&p=1&u=63&acaoId=' + T005_Id,
						  type: 'GET',
						  contentType: 'application/json',
						}).success(function(response) {
							if(response.status = 200){
								if(response.semEtiqueta == ""){
									printSMT(response.etiqueta, '{$impressoraEtiqueta}');
								}else{
									alert('Não há etiquetas disponíveis para este pedido');
								}
							}else{
								alert('Tivemosu um problema ao pegar a etiqueta, contate o suporte');
								console.log(response);
							}
						}).error(function(err) {
							alert('Tivemosa um problema ao pegar a etiqueta, contate o suporte');
							console.log(err);
						});

			        });
					
				});




					$('#crm001testeCorreios, #{$g['divId']} button[botaoid="crm001testeCorreios"]').bind('click', function(data) {		
						let rastreio = 'QL432010447BR';
						$.ajax({
						  url: '/outros/integracaoSMT/integrador/integracaoPortalPostal/pegaEtiquetaCorreios?ajax=true&noEmp=true&p=1&u=63&codigoRastreio=' + rastreio,
						  type: 'GET',
						  contentType: 'application/json',
						}).success(function(response) {
							console.log(response);
						}).error(function(err) {
							console.log(err);
							alert('Tivemos um problema com este código de rastreio, aguarde e tente novamente ou contate o suporte');
						});
			        });

					
				

			} </script></div>
EOT;
