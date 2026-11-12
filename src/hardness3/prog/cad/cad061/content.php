<?php
namespace hardness;
/**
* Weiberlan
* 08/11/2011
*/
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Grid Principal
	* Chamado em: Menu
	*/
	case 'ven002content01':
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
		array($content1, '/ven/ven002/tab/ven002tab01/')
		);
		echo gProcessaAcoes($actions);

	break;

	/**
	* Form Principal
	* Chamado em: ven002grid01
	*/
	case 'cad061content02':
        
		$sql="select D157_Id from D157 where D157_Id='{$r_acaoId}'";
		$consultaD157_Id=mysql_query($sql);
		if (!empty($r_acaoId) && mysql_num_rows($consultaD157_Id)<=0) {
			$alert = gGeraAlertMsg('Erro', 'Este pedido não existe!', false);
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
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad061/tab/cad061tab02/', '&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
			
			$D157 = mysql_query("SELECT D157_Flag_Primeira_Liberacao_Credito, D157_C007_Id_Vendedor_Interno, D157_Flag_Status, D157_Flag_Expedicao, D157_Flag_ACP, D157_Flag_Reenviado_Almoxarifado, D157_Flag_Reenviado_Almoxarifado_Lido, D157_Flag_Notificacao_Financeiro, D157_Flag_Notificacao_Liberacao, D157_Flag_Notificacao_Estoque from D157 where D157_Id='{$r_acaoId}'");
			$mD157 = mysql_fetch_array($D157);
			
			//Remoção das notificações do pedido
			if($mD157['D157_C007_Id_Vendedor_Interno'] == $g['usuarioAtual']){
				//Retorno de almoxarifado
				if($mD157['D157_Flag_Reenviado_Almoxarifado_Lido'] == 'N' && $mD157['D157_Flag_Reenviado_Almoxarifado'] == 'S'){
					echo gGeraAlertMsg('Atenção', 'Este pedido retornou do almoxarifado.');
					mysql_query("UPDATE D157 SET D157_Flag_Reenviado_Almoxarifado_Lido = 'S' WHERE D157_Id = '{$r_acaoId}'");
				}

				//Analise de credio
				if($mD157['D157_Flag_Notificacao_Financeiro'] != ''){
					if($mD157['D157_Flag_Notificacao_Financeiro'] == 'S'){
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>aprovado</b> na análise de crédito.');
					} else if($mD157['D157_Flag_Notificacao_Financeiro'] == 'N'){
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>negado</b> na análise de crédito.');
					}
					mysql_query("UPDATE D157 SET D157_Flag_Notificacao_Financeiro = '' WHERE D157_Id = '{$r_acaoId}'");
				}

				//Analise de pedido
				if($mD157['D157_Flag_Notificacao_Liberacao'] != ''){
					if($mD157['D157_Flag_Notificacao_Liberacao'] == 'S'){
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>aprovado</b> na análise comercial.');
					} else if($mD157['D157_Flag_Notificacao_Liberacao'] == 'N'){
						echo gGeraAlertMsg('Atenção', 'Este pedido foi <b>negado</b> na análise comercial.');
					}
					mysql_query("UPDATE D157 SET D157_Flag_Notificacao_Liberacao = '' WHERE D157_Id = '{$r_acaoId}'");
				}

				$mensagem = "Chegou <b>estoque</b> para o pedido pendente <b>{$r_acaoId}</b>.";
				mysqli_query("UPDATE N001 SET N001_Flag_Excluido = 'S' WHERE N001_Mensagem = '{$mensagem}' AND N001_Flag_Excluido='N'");

				//Entrada de mercadoria
				if($mD157['D157_Flag_Notificacao_Estoque'] == 'S'){
					echo gGeraAlertMsg('Atenção', 'Chegou <b>estoque</b> para este pedido.');
					mysql_query("UPDATE D157 SET D157_Flag_Notificacao_Estoque = 'N' WHERE D157_Id = '{$r_acaoId}'");
				}
			}


			$disabled2="";
			if($mD157['D157_Flag_Status'] > 0 AND $mD157['D157_Flag_Status']!=14 AND $mD157['D157_Flag_Status']!=15 AND $mD157['D157_Flag_Status']!=9 AND $mD157['D157_Flag_Status']!=26 AND $mD157['D157_Flag_Status']!=22){
				$disabled2 = 'disabled="disabled"';
			}

			$botoesMid = '';
			$botoesDir = '';
			$botoesEsq = '';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002ExcluirItensMarcados">Excluir</button>';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002FaturarPedido">Faturar</button>';
			if($g['C031']['botaoAplicarDesconto'] == 'S'){
				//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002AplicarDesconto">Desconto</button>';
			}
			if($g['C031']['permissaoFaturarEmergencia'] == 'S'){
				if (gTemPermissao(87)) {
					//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002FaturarEmergencia">Faturar Emergência</button>';
				}
			}
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002AplicarIpv">Aplicar IPV</button>';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002ImportarItens">Importar</buton>';
			//$botoesEsq.= '<button type="submit" '.$disabled2.' id="ven002AtualizarAliquotas">Atualizar</button>';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002AtualizarOpcoes">Atualizar</button>';
			$botoesEsq .= '<button type="submit" id="ven002EnviarEmail">E-mail</button>';
			$botoesEsq .= '<button type="submit" id="ven002ImprimirPedido">Imprimir</button>';
			if($mD157['D157_Flag_Status'] == 26){
				//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002ReativarPedido">Reativar</button>';
			}
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="ven002DividirPedidoBtn">Dividir</button>';
			//$botoesEsq .= '<button type="submit" id="ven002Compras">Compras</button>';
			//$botoesEsq .= '<button type="submit" id="ven002Teste">Recibo</button>';
			if($mD157['D157_Flag_Status'] == 9 || ($g['C031']['verificaCreditoLiberadoGerarPedido'] == 'S' && $mD157['D157_Flag_Status'] == '0' && $mD157['D157_Flag_Primeira_Liberacao_Credito'] == '0')){
				//$botoesEsq .= '<button type="submit" id="ven002NovaLiberacao">Pedir Nova Liberação</button>';
			}
			if($mD157['D157_Flag_ACP'] == 4){
				//$botoesEsq .= '<button type="submit" id="ven002imprimirDav">DAV</button>';
			}
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
				$('#ven002Teste').bind('click', function(data) {
					mp2032PreCarregarApplet();
					var buttons = {
						"Sim": function() {
							$.ajax({
								url: '/sistema/impressao/cupom/romaneioestoque/',
								dataType: 'html',
								data: 'ajax=true&D157_Id=' + encodeURIComponent('{$r_acaoId}'),
								type: 'post',
								success: function(response) {
									eval(response);
								}
							});
							$(this).dialog("destroy").remove();
					 	},
						"Não": function() {
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Impressão', 'Confirmar Impressão?', buttons);
				});
				$('#ven002AplicarIpv').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar IPV', '/ven/ven002/content/ven002ContentAplicarIpv/', '&tab=geral&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#ven002ImportarItens').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Ítens', '/ven/ven002/content/ven002ContentImportarItens/', '&tab=geral&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#ven002AplicarDesconto').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven002/content/ven002ContentAplicarDesconto/', '&tab=geral&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [450,150]);
				});
				$('#ven002DividirPedidoBtn').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Pedido', '/ven/ven012/content/ven012ContentDividirPedido/', '&tab=geral&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
				});
				$('#ven002ImprimirPedido').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=imprimir&callback=?', function(request) {
						 $('#{$g['divId']}').hideLoading();
						 if (request.code === true) {
						 	$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/ven002VerificarMoedaImpressao/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if(request.code === true){
									imprimirPedido(1);
								} else if(request.code == 'verificarMoeda'){									
									dialogConfirm('Imprimir Pedido', "Selecione a moeda para impressão", [
										{
											text: "REAL",
											click: function() {
											  	imprimirPedido(1);
											  	$(this).dialog("destroy").remove(); 
												return false;
											}
										},
										{
											text: request.moeda,
											click: function() {
											  	imprimirPedido(request.data);
											  	$(this).dialog("destroy").remove(); 
												return false;
											}
										},
										{
											text: "Cancelar",
											click: function() {
											  	$(this).dialog("destroy").remove(); 
											}
										}
									]);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						 }else if(request.code == 'erro') {
								var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Enviar E-mail', request.data, \$buttons);
						 } else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=imprimir' + '&callback=?', function(request2) {
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
				});

				function imprimirPedido(moeda){
					$('#{$g['divId']}').showLoading();
				 	$.getJSON('/ven/ven002/form_func-ajax/ven002ImprimirPedido/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&moeda=' + encodeURIComponent(moeda) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if(request.code===true) {
							window.open(request.data);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
						}
					});
				}
				
				$('#ven002AtualizarAliquotas').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var D157_Id = $('#{$g['divId']} #D157_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

				$('#ven002imprimirDav').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/imprimirDav/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								window.open(request.data);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});
				
				$('#ven002FaturarEmergencia').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturar Emergência', '/ven/ven002/content/ven002ContentFaturarEmergencia/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [450,180]);
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Faturar Emergência?', 'Este pedido será encaminhado direto para emissão de NFe!', \$buttons);
				});
				
				$('#ven002ReativarPedido').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/ven/ven002/form_func-ajax/reativarPedido/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$g['divId']}');
									}
								});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Reativar Pedido?', 'Deseja reativar o pedido?', \$buttons);
				});

				$('#ven002NovaLiberacao').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/ven/ven002/form_func-ajax/novaLiberacao/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									//divRefresh('{$g['divId']}');
									$('#{$g['divId']}').dialog('refresh');
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Nova Liberação', 'Deseja pedir uma nova liberação para este pedido?', \$buttons);
				});
				
				$('#ven002FaturarPedido').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
				    $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=faturar&callback=?', function(request) {
					    $('#{$g['divId']}').hideLoading();
						if (request.code === true) {
							var buttons = {
								"Sim": function() {
									var D157_Id = $('#{$g['divId']} #D157_Id').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/faturarPedido/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    					dialogConfirm('Ok', request.data, buttons);
										} else {
											var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    					dialogConfirm('Erro', request.data, buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Faturar Pedido', 'Deseja faturar o pedido {$r_acaoId}?', buttons);
						}else if(request.code == 'erro') {
							var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
						    dialogConfirm('Faturar Pedido', request.data, \$buttons);

						} else {

							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=faturar' + '&callback=?', function(request2) {
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
							dialogConfirm('Faturar Pedido', request.data, \$buttons);
						}
				    });
					
				});

				$('#ven002EnviarEmail').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven002/form_func-ajax/verificarParcelas/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
						$('#{$g['divId']}').hideLoading();
						if(request1.code) {
							 $.getJSON('/ven/ven002/form_func-ajax/verificaAlcadaNovo/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&acao=enviarEmail&callback=?', function(request) {
								 $('#{$g['divId']}').hideLoading();
								 if (request.code == true) {
							 		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
								 }else if(request.code == 'erro') {
										var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
									    dialogConfirm('Enviar E-mail', request.data, \$buttons);
								 }else{
									var \$buttons = {
										"Sim": function() {
											$('#{$g['divId']}').showLoading();
											$(this).dialog("destroy").remove();
											$.getJSON('/ven/ven002/form_func-ajax/enviarParaAnalise/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&alcadaUpdate=' + encodeURIComponent(request.code) + '&acao=email' + '&callback=?', function(request2) {
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
						}else{
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', request1.data, buttons);
				    	}
					});
				});

				$('#ven002Compras').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Compras', '/ven/ven002/content/ven002ContentCompras/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [850,600]);
				});

				$('#ven002ExcluirItensMarcados').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/excluirItensMarcados/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

				$('#ven002AtualizarOpcoes').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven002/content/ven002ContentAtualizarCFOP/', '&tab=geral&D157_Id=' + encodeURIComponent("{$r_acaoId}"), [500,250]);
							$(this).dialog("destroy").remove();
						},
						"Atualizar preço tabela": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarPrecoTabela/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Preço(s) tabela atualizado com sucesso!", \$buttons);
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
							dialogConfirm('Atenção', 'Deseja atualizar o preço tabela dos produtos?', \$buttons2);
						},
						"Atualizar engenharia": function() {
							var \$buttons2 = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven002/form_func-ajax/atualizarComponentesProdutos/?ajax=true&D157_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Componentes atualizados com sucesso!", \$buttons);
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
							dialogConfirm('Atenção', 'Deseja atualizar a engenharia dos produtos igual ao cadastro de produtos?', \$buttons2);
						},
						"Cancelar": function() {
							$(this).dialog("destroy").remove();
						}
					};
					var dialog = dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
					var buttons = $('#dc-' + dialog).parent().find('.ui-dialog-buttonpane button')
					var botao3 = $(buttons[3]);
					if('{$g['C031']['desabilitarProducaoEmergencia']}' == 'S'){
						$(botao3).remove();
					}
				});
			} </script></div>
EOT;

		break;
		
		case 'ven002ContentFaturarEmergencia':
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
			array($content1, '/ven/ven002/form/ven002FormFaturarEmergencia/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);						

		break;

		case 'ven002content03':
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
			array($content1, '/ven/ven002/tab/ven002tab03/', '&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'cad061content04':
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
			//array($content1, '/ven/ven002/form/ven002form02/', "&tab=geral&T005_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content1, '/cad/cad061/content/cad061contentform/', "&D157_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2) . '&divOutro=' . urlencode($divOutro)),
			array($content2, '/cad/cad061/grid/cad061grid02/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($divOutro)),
			);
			echo gProcessaAcoes($actions);
		break;
					
		case 'cad061contentform':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $r_divOutro);
			$g['smarty']->assign('content1Width', '350px');
			$g['smarty']->display('content-2columnsPedido.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/cad/cad061/form/cad061form02/', "&tab=geral&D157_Id=" . urlencode($r_D157_Id) . "&divReload=" . urlencode($r_divReload) . "&divOutro=" . urldecode($r_divOutro)),
			array($r_divOutro, '/cad/cad061/outro/cad061observacoes/', '&acaoId=' . urlencode($r_T005_Id)),
			);
			echo gProcessaAcoes($actions);
		break;
			
		case 'ven002contentTrocas':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '190px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002formTrocas/', "&tab=trocas&T005_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002gridTrocas/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
		break;			

		case 'ven002content05':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '230px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven002/form/ven002form03/', "&tab=geral&T005_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid03/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content06':
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
			array($content1, '/ven/ven002/form/ven002form04/', "&tab=editar_material&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid04/', '&T147_Id=' . urlencode($r_T147_Id)  . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content07':
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
			array($content1, '/ven/ven002/form/ven002form05/', "&tab=geral&T147_Id=" . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002grid05/', '&acaoId=' . urlencode($r_T147_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'ven002content08':
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
			array($content1, '/ven/ven002/tab/ven002tab03/', "&tab=geral&T147_Id=" . urlencode($r_T147_Id))
			);
			echo gProcessaAcoes($actions);

			break;

		case 'cad061ContentEditarProduto':

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
			array($content1, '/cad/cad061/tab/cad061TabEditarProduto/', '&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
			);
			echo gProcessaAcoes($actions);
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="cad061FormEditarProdutosBtn">Alterar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
		
		
	case 'ven002ContentAplicarIpv':

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
			array($content1, '/ven/ven002/form/ven002FormAplicarIpv/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="ven002AplicarIpvBtnExecutar">Executar</button>';
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
			$('#ven002AplicarIpvBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T005_Id = $('#{$g['divId']} #T005_Id').val();
						var IPV = $('#{$g['divId']} #IPV').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/aplicarIPV/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&IPV=' + encodeURIComponent(IPV) + '&callback=?', function(request) {
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
				dialogConfirm('Gerar Pedido', 'Você tem certeza? ', \$buttons);
			});
			} </script></div>
EOT;
	break;
	
	case 'ven002ContentImportarItens':

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
			array($content1, '/ven/ven002/form/ven002FormImportarItens/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="ven002ImportarItensBtnExecutar">Executar</button>';
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
			$('#ven002ImportarItensBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T005_Id = $('#{$g['divId']} #T005_Id').val();
						var T005_Id_Origem = $('#{$g['divId']} #T005_Id_Origem').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/ven/ven002/form_func-ajax/importarItens/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&T005_Id_Origem=' + encodeURIComponent(T005_Id_Origem) + '&callback=?', function(request) {
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
				dialogConfirm('Gerar Pedido', 'Você tem certeza? ', \$buttons);
			});
			} </script></div>
EOT;
	break;

		case 'ven002ContentAbrePagamento':
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
			array($content1, '/ven/ven002/tab/ven002TabPagamento/', '&T005_Id=' . urlencode($r_T005_Id)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'cad061ContentPagamento':
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
            // "&acaoId=" . urlencode($r_acaoId)
			array($content1, '/cad/cad061/form/cad061FormPagamento/', "&tab=geral&D157_Id=" . urlencode($r_D157_Id) . "&acaoId=" . "&divReload=" . urlencode($content2)),
			array($content2, '/cad/cad061/grid/cad061GridPagamento/', '&acaoId=' . urlencode($r_D157_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentEmail':
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
			//array($content1, '/ven/ven002/grid/ven002GridEmail/', '&acaoId=' . urlencode($r_acaoId)),
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&T005_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentDocumentos':
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
			array($content1, '/ven/ven002/form/ven002FormDocumentos/', "&tab=geral&T005_Id=" . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/ven/ven002/grid/ven002GridDocumentos/', '&acaoId=' . urlencode($r_T005_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'ven002ContentDetalhesCheque':
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
			array($content1, '/ven/ven002/form/ven002FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T090_Id)),
			);
			echo gProcessaAcoes($actions);			
		
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="ven002FormDetalhesCheque_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');			

		break;	
		
	case 'ven002ContentAplicarDesconto':

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
		array($content1, '/ven/ven002/tab/ven002TabAplicarDesconto/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
    
    case 'ven002ContentImportarDoc':
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
        array($content1, '/ven/ven002/form/ven002FormImportarDoc/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id) . '&divReload=' . urlencode($r_divReload)),
        );
        echo gProcessaAcoes($actions);

    break;
    
    
    case 'ven002contentCancelarPedido':
            
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
            	array($content1, '/ven/ven002/form/ven002FormCancelarPedido/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
            );
            echo gProcessaAcoes($actions);

    break;
            
    case 'ven002ContentAgregados':
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
            array($content1, '/ven/ven002/form/ven002FormAgregados/', '&tab=geral&T006_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
            array($content2, '/ven/ven002/grid/ven002GridAgregados/', '&acaoId=' . urlencode($r_acaoId) . '&T006_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
        );
        echo gProcessaAcoes($actions);
        
        /**
         * Botões
         */
        // Definição dos botões
        $botoesEsq = '';
        //$botoesDir = '';
        //$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnConfirmarTudo">Confirmar Tudo</button>';
        //$botoesEsq .= '<button type="submit" id="ven001ContetnProdutosBtnZerarTudo">Zerar Tudo</button>';
        //$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        //$g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        $novoIdTelaForm = uniqid();        
    break;
   
	case 'ven002ContentProdutosDefeitos':
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
        array($content1, '/ven/ven002/grid/ven002GridPedidoProdutosDefeito/', '&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
	break;

	case 'cad061contentIncluirPedido':
		$acaoId=isset($r_acaoId) ? $r_acaoId : false;
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
        array($content1, '/cad/cad061/tab/cad061TabIncluirPedido/', '&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentCompras':
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
			array($content1, '/ven/ven002/tab/ven002TabCompras/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentCotar':
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
			array($content1, '/ven/ven002/tab/ven002TabCotar/', '&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven002FormCotar_btn">Cotar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002ContentComprar':
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
			array($content1, '/ven/ven002/tab/ven002TabComprar/', '&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven002FormComprar_btn">Comprar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002ContentMudarPedidoEmpresa':
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
			array($content1, '/ven/ven002/form/ven002FormMudarPedidoEmpresa/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
	break;


	case 'ven002ContentAtualizarCFOP':
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
			array($content1, '/ven/ven002/form/ven002FormAtualizarCFOP/', '&tab=geral&T005_Id=' . urlencode($r_T005_Id)),
		);
		echo gProcessaAcoes($actions);
	break;


	case 'contentMontagemIPV':
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
			array($content1, '/ven/ven002/tab/ven002TabMontagemIPV/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven002ContentEngenhariaProduto':
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
			array($content1, '/ven/ven002/tab/ven002TabEngenhariaProduto/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'cad061ContentPrincipal':
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

	case 'cad061ContentProcessarPedidos':
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
			array($content1, '/cad/cad061/form/cad061FormProcessarPedidos/', '&tab=geral')
		);
		echo gProcessaAcoes($actions);
	break;
}
