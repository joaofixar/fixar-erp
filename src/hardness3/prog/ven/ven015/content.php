<?php
namespace hardness;

switch ($g['r'][3]) {

	case 'ven015contentNotaFiscalPDV':

		if($r_T007_Id>0){
			// Recebe o T007_Id do APP INTEGRACAO PAGAMENTO POS
			$r_acaoId = $r_T007_Id;
		} else {
			// Busca a venda em aberto para o usuário corrente
			$C007 = mysql_query("select C007_T007_Id_PDV, 
										T007_Id, 
										T007_C004_Id,
										C004_Nome_Abreviado,
										T007_C007_Id_Vendedor_Interno
							  	   from C007 
							  left join T007 on T007_Id = C007_T007_Id_PDV 
							  left join C004 on C004_Id = T007_C004_Id
								  where C007_Id = '{$g['usuarioAtual']}'");
			$mC007 = mysql_fetch_assoc($C007);

			if (!empty($mC007['T007_C004_Id']) && $mC007['T007_C004_Id']!=$g['empresaAtual']){
				echo <<<EOT
					<script type="text/javascript">
					fecharJanela("{$g['divId']}");
					dialogConfirm('Ok', "Você possui uma venda aberta na empresa {$mC007['C004_Nome_Abreviado']}");
					</script>
EOT;
			}

			$r_acaoId = $mC007['T007_Id'];
		}

		// O que será carregado em cada conteúdo?
		if ($r_acaoId>0) {
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '40px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
				array($content1, '/ven/ven015/outro/ven015CabecalhoNotaPDV/', '&acaoId=' . urlencode($r_acaoId)),
				array($content2, '/ven/ven015/tab/ven015TabNotaFiscalPDV/', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true),
			);
		} else {
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			$actions = array(
				array($content1, '/ven/ven015/outro/ven015AbrirNovaVenda/', "&formId=" . urlencode($formId) . "&divRefresh=" . urldecode($g['divId'])),
			);
		}
		echo gProcessaAcoes($actions, false);

		/**
		* Botões
		*/
		if ($r_acaoId>0) {
			$sql="select * from T007 where T007_Id={$r_acaoId}";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);

/* 			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';

			//$botoesEsq .= '<button type="submit" id="ven003AlterarQuantidade">F1 - Alterar quantidade</button>';
			$botoesEsq .= '<button type="submit" id="ven015CPF">F2 - CPF</button>';
			$botoesEsq .= '<button type="submit" id="ven015AplicarDesconto">F4 - Desconto</button>';
			$botoesEsq .= '<button type="submit" id="ven003Pagamento">F6 - Pagamento</button>';
			$botoesEsq .= '<button type="submit" id="ven015FinalizarNFe">F7 - Finalizar venda</button>';
			$botoesEsq .= '<button type="submit" id="ven015ImportarDAV">F8 - DAV</button>';
			$botoesEsq .= '<button type="submit" id="ven015AlterarVendedor">F9 - Diversos</button>';
			$botoesEsq .= '<button type="submit" id="ven015ExcluirPDV">F10 - Cancelar venda</button>';
			$botoesEsq .= '<button type="submit" id="ven003NFe">F3 - Completa</button>';
			if($g['C031']['abrirGavetaManual'] == 'S'){
				$botoesEsq .= '<button type="submit" id="ven015AbrirGaveta">Ctrl+F11 - Abrir gaveta</button>';
			} */

			// Funcao personalizada no APP INTEGRACAO PAGAMENTO POS
       		// Felipe 18/11/2022
			require_once("bibliotecas/classes/VEN015.php");
			$VEN015 = new VEN015();
			$exibirBotaoPagamento = $VEN015->validaExibirBotaoPagamento($r_acaoId);
			// Se não exibir o botão pagamento, também não será transmtitida automaticamente a nota
			$transmiteNotaAoFinalizar = $exibirBotaoPagamento;
			$exibirBotaoDesconto  = true;
			$exibirBotaoDav       = true;
			if($r_T007_Id > 0){
				// só terá T007_Id se utilizar o app INTEGRAÇÃO PAGAMENTO POS 
				$exibirBotaoDesconto  = false;
				$exibirBotaoDav       = false;
			}

			$botoes = array(
				array('titulo' => 'F2 - CPF', 'id' => 'ven015incluirCPF', 'posicao' => 'esquerda'),
				array('titulo' => 'F4 - Desconto', 'id' => 'ven015AplicarDesconto', 'posicao' => 'esquerda', 'exibir' => $exibirBotaoDesconto),
				array('titulo' => 'F6 - Pagamento', 'id' => 'ven003Pagamento', 'posicao' => 'esquerda','exibir' => $exibirBotaoPagamento),
				array('titulo' => 'F7 - Finalizar venda', 'id' => 'ven015FinalizarNFe', 'posicao' => 'esquerda', 'exibir' => $exibirBotaoFinalizar),
				array('titulo' => 'F8 - DAV', 'id' => 'ven015ImportarDAV', 'posicao' => 'esquerda', 'exibir' => $exibirBotaoDav),
				array('titulo' => 'F9 - Diversos', 'id' => 'ven015AlterarVendedor', 'posicao' => 'esquerda'),
				array('titulo' => 'F10 - Cancelar venda', 'id' => 'ven015ExcluirPDV', 'posicao' => 'esquerda'),
				array('titulo' => 'F3 - Completa', 'id' => 'ven003NFe', 'posicao' => 'esquerda'),
				array('titulo' => 'Ctrl+F11 - Abrir gaveta', 'id' => 'ven015AbrirGaveta', 'posicao' => 'esquerda','exibirCallback' => 'return $g["C031"]["abrirGavetaManual"] == "S";'),				
			);

			echo Botoes::processar($botoes, 'content');


			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo $g['smarty']->fetch('buttons-floatBottom.tpl');

			// Parâmetros necessários para abrir o DANFE
			$Query = md5($row['T007_D024_Id'].'|'.$r_acaoId);
			$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];

			//Ações dos Botões
			$novoIdTelaForm = uniqid();
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {

						shortcut.remove("F5");
						shortcut.add("F5",function(data) {
							//document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
							divRefresh('{$g['divId']}', false);
							return false;
						});

						$('#ven015incluirCPF').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'CPF', '/ven/ven015/content/ven015ContentFormCPF/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [400,150]);
						});
						shortcut.remove("F2");
						shortcut.add("F2",function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'CPF', '/ven/ven015/content/ven015ContentFormCPF/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [400,150]);
							return false;
						});

						$('#ven015AlterarVendedor').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Diversos', '/ven/ven015/content/ven015ContentFormVendedor/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&C007_Id=' + encodeURIComponent("{$row['T007_C007_Id_Vendedor_Interno']}") + '&divReload=' + "{$g['divId']}", [700,200]);
						});
						shortcut.remove("F9");
						shortcut.add("F9",function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Diversos', '/ven/ven015/content/ven015ContentFormVendedor/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&C007_Id=' + encodeURIComponent("{$row['T007_C007_Id_Vendedor_Interno']}") +'&divReload=' + "{$g['divId']}", [700,200]);
							return false;
						});

						$('#ven015AplicarDesconto').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven015/content/ven015ContentAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' + encodeURIComponent("{$r_acaoId}") + '&tabelaCabecalho=T007&tabelaProduto=T008', [600,200]);
						});
						shortcut.remove("F4");
						shortcut.add("F4",function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven015/content/ven015ContentAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' + encodeURIComponent("{$r_acaoId}") + '&tabelaCabecalho=T007&tabelaProduto=T008', [600,200]);
							return false;
						});

						$('#ven003TransmitirNota').bind('click', function(data) {
							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven003/content/ven015ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,300]);
						});

						$('#ven003NFe').bind('click', function(data) {
							var \$buttons2 = {
								"Sim": function() {
									$(this).dialog("destroy").remove();
									$.getJSON('/ven/ven015/form_func-ajax/ven015VerificaAbrirNotaCompleta/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if(request.code){
											$.getJSON('/ven/ven015/form_func-ajax/Ven015finalizarNotaFiscalPDV/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
												fecharJanela('{$g['divId']}');
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Nota Fiscal - ID: ' + $r_acaoId, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&telaPDV=S&tabela=' + encodeURIComponent("{$from}"), [1000,550]);
											});	
										}else{
											$(this).dialog("destroy").remove(); 
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Sem permissão para abrir NF completa', request.data, \$buttons);
										}									
									});	
								},
								"Não": function() {
									$(this).dialog("destroy").remove(); 
								}
							};
							dialogConfirm('Mudar para tela completa', 'Ao mudar para a tela completa a tela PDV será fechada automaticamente para esta venda, continuar?', \$buttons2);
						});

						shortcut.remove("F3");
						shortcut.add("F3",function(data) {
							var \$buttons2 = {
								"Sim": function() {
									$(this).dialog("destroy").remove();
									$.getJSON('/ven/ven015/form_func-ajax/ven015VerificaAbrirNotaCompleta/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if(request.code){
											$.getJSON('/ven/ven015/form_func-ajax/Ven015finalizarNotaFiscalPDV/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
												fecharJanela('{$g['divId']}');
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Nota Fiscal - ID: ' + $r_acaoId, '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&telaPDV=S&tabela=' + encodeURIComponent("{$from}"), [1000,550]);
											});	
										}else{
											$(this).dialog("destroy").remove(); 
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Sem permissão para abrir NF completa', request.data, \$buttons);
										}
									});	
								},
								"Não": function() {
									$(this).dialog("destroy").remove(); 
								}
							};
							dialogConfirm('Mudar para tela completa', 'Ao mudar para a tela completa a tela PDV será fechada automaticamente para esta venda, continuar?', \$buttons2);
						});

						$('#ven003Pagamento').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pagamento', '/ven/ven015/content/ven015ContentPrincipalPagamentosPDV/', '&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&tabela=' + encodeURIComponent("{$from}"), [750,550]);
						});
						shortcut.remove("F6");
						shortcut.add("F6",function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pagamento', '/ven/ven015/content/ven015ContentPrincipalPagamentosPDV/', '&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&tabela=' + encodeURIComponent("{$from}"), [650,550]);
							return false;
						});

						$('#ven015FinalizarNFe').unbind('click').bind('click', function(data) {
							ven015FinalizarNFe(data);
						});
						shortcut.remove("F7");
						shortcut.add("F7",function(data) {
							ven015FinalizarNFe(data);
							return false;
						});

						$('#ven015ImportarDAV').bind('click', function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar DAV', '/ven/ven015/content/ven015ContentFormDAV/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [400,200]);
						});
						shortcut.remove("F8");
						shortcut.add("F8",function(data) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar DAV', '/ven/ven015/content/ven015ContentFormDAV/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [400,200]);
							return false;
						});

						$('#ven015ExcluirPDV').unbind('click').bind('click', function(data) {
							ven015ExcluirPDV(data);
						});
						shortcut.remove("F10");
						shortcut.add("F10",function(data) {
							ven015ExcluirPDV(data);
							return false;
						});

						$('#ven015AbrirGaveta').bind('click', function(data) {
							ven015AbrirGaveta(data);
							return false;
						});
						shortcut.remove("CTRL+F11");
						shortcut.add("CTRL+F11",function(data) {
							ven015AbrirGaveta(data);
							return false;
						});
					}, 200 );

					function ven015FinalizarNFe(data) {
						var \$buttons = {
							"Sim": function() {
								//var T005_Id = $('#{$g['divId']} #T005_Id').val();
								idProgressBar = dialogAlert('Finalizando Nota Fiscal',"<span id='progressBar'><b><br>0% Validando Nota</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								$.getJSON('/ven/ven015/form_func-ajax/Ven015finalizarNotaFiscalPDV/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									$.getJSON('/ven/ven003/form_func-ajax/finalizarNotaFiscal/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if(request.code){
											var T007_Id = request.T007_Id;
											$('#progressBar').html('<br><b><font color="green">100% Nota validada &#10004;</font></b></br>' + '<b>0% Totalizando produtos da nota</b></br>');
											ven003TotalizarProdutosNotaLoop(idProgressBar,T007_Id, 1); 
											
											function ven003TotalizarProdutosNotaLoop(idProgressBar,T007_Id, inicio){
												$.getJSON('/ven/ven003/form_func-ajax/totalizarNotaFiscal/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&inicio=' + inicio + '&callback=?', function(request) {
													if (request.concluido == 0) {
														$('#progressBar').html('<br><b><font color="green">100% Nota Validada &#10004;</font></b></br><b>' + request.percentual + '%</b>' + '<b> Totalizando produtos da nota</b></br>');
														ven003TotalizarProdutosNotaLoop(idProgressBar,T007_Id, 0); 
													}else{
														if(request.code){
															$('#progressBar').html('<br><b><font color="green">100% Nota validada &#10004;</font></b></br>' + '<b><font color="green">'+ request.percentual + '% Produtos da nota totalizados &#10004;</font></b></br>' + '<b>0% Finalizando nota</b></br>');

															$.getJSON('/ven/ven003/form_func-ajax/finalizarNotaFiscalContinuarLiberacao/?ajax=true&C007PDV=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
																if(request.code){
																	$('#progressBar').html('<br><b><font color="green">100% Nota validada &#10004;</font></b></br>' + '<b><font color="green">100% Produtos da nota totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota fiscal finalizada &#10004;</font></b></br>'+'<b>0% Reprocessando histórico</b></br>');
																	ven003TotalizarReprocessaHistoricoLoop(idProgressBar,T007_Id, 1);
																	
																	function ven003TotalizarReprocessaHistoricoLoop(idProgressBar,T007_Id,inicio){
																		$.getJSON('/ven/ven003/form_func-ajax/totalizarReprocessaHistorico/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&inicio=' + inicio + '&callback=?', function(request) {
																			if (request.concluido == 0) {
																				$('#progressBar').html('<br><b><font color="green">100% Nota validada &#10004;</font></b></br>' + '<b><font color="green">100% Produtos da nota totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota fiscal finalizada &#10004;</font></b></br><b>' + request.percentual + '% Reprocessando histórico</b></br>');
																				ven003TotalizarReprocessaHistoricoLoop(idProgressBar,T007_Id, 0); 
																			}else{
																				if(request.code){
																					$('#dc-' + idProgressBar).dialog("destroy").remove();
																					divRefresh('{$g['divId']}', true);
																					if('{$transmiteNotaAoFinalizar}'){
																						if(request.transmitirDireto){
																							abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven015/content/ven015ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,320]);
																						} else {
																							var \$buttons2 = {
																								"Sim": function() {
																									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven015/content/ven015ContentTransmitirNotaFiscal/', '&tab=geral&naoFechaJanelaPDV=true&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,320]);
																									$(this).dialog("destroy").remove();
																								},
																								"Não": function() {
																									$(this).dialog("destroy").remove(); 
																									divRefresh("{$g['divId']}");
																								}
																							};
																							dialogConfirm('Transmitir Nota Fiscal', 'Deseja transmitir a nota fiscal? ', \$buttons2);	
																						}
																					}	
																				}else{
																					$('#dc-' + idProgressBar).dialog("destroy").remove();
																					var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																					dialogConfirm('Erro ao reprocessar histórico', request.data, \$buttons);
																				}
																			}
																		});
																	}
																}else{
																	$('#dc-' + idProgressBar).dialog("destroy").remove();
																	var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																	dialogConfirm('Erro ao liberar nota', request.data, \$buttons);
																}
															});
														}else{
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro ao totalizar produtos da nota', request.data, \$buttons);
														}
													}
												});
											}
										}else{
											$('#dc-' + idProgressBar).dialog("destroy").remove();
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro ao validar nota', request.data, \$buttons);
										}
									});
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Finalizar Nota Fiscal', 'Você tem certeza? ', \$buttons);
					}

					function ven015AbrirGaveta(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven015/form_func-ajax/verificaAbrirGaveta/?ajax=true&callback=?', function(request) {
									if (request.code) {
										// Exemplo: abrirJanela
										$('#{$g['divId']}').showLoading();
										$.ajax({
											url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
											type: 'GET',
											dataType: 'jsonp',
											data: {
												'ajax': 'true',
												'acaoId': 'da681c8d16233fa8bdc9a645a506841b',
												'link': '/sistema/personalizacao/tela/da681c8d16233fa8bdc9a645a506841b/?ajax=true&C007_Id='+ request.C007_Id,
											},
											success: function(resposta) {
												$('#{$g['divId']}').hideLoading();
												window.open(resposta.data, '_blank');
											}
										});
									} else {
										var \$buttons = { 
											"Fechar": function() { 
													$(this).dialog("destroy").remove(); 
													setTimeout(function() { 
													}, 150); 
											}, 
											"Pedir autorização": function() { 
													$(this).dialog("destroy").remove(); 
													abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Autorização', '/sistema/funcoes/autoriza/contentAbrirGaveta/', '&divForm={$g['divId']}', [300,220]);
											} 
										};
										dialogConfirm('Erro', request.data, \$buttons);									
									}
									$('#{$g['divId']}').hideLoading();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Abrir gaveta', 'Você tem certeza que deseja abrir gaveta? ', \$buttons);
					}

					function ven015ExcluirPDV(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven015/form_func-ajax/excluirNotaPDV/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									if (request.code) {
										if('{$r_T007_Id}' > 0){
											fecharJanela("{$g['divId']}");
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											divRefresh('{$g['divId']}', true);
											//document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
										}	
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
						dialogConfirm('Atenção', 'Deseja excluir a Nota Fiscal? <font color="red">Este processo irá excluir a nota fiscal e seus itens, assim como <br />os títulos a receber, boletos e lançamentos no caixas e bancos referentes a esta nota.</font>', \$buttons);
					}

					$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						// Evento ao fechar janela PDV, retorna teclas de atalho ao padrão do sistema
						setTimeout(function() {
							shortcut.remove("F1");
							shortcut.remove("F2");
							shortcut.remove("F3");
							shortcut.remove("F4");
							shortcut.remove("F5");
							shortcut.remove("F6");
							shortcut.remove("F7");
							shortcut.remove("F8");
							shortcut.remove("F9");
							shortcut.remove("F10");
							shortcut.remove("CTRL+F11");
						
EOT;
					$teclasAtalhoF1 = "<span style=\"font-size: 1.3em; font-weight: bold;\">F1</span> (Ajuda Teclas de Atalho)<br />";

					$res = mysql_query("SELECT * FROM D142 ORDER BY D142_Atalho ASC;");
					if ($res) {
						while ($row = mysql_fetch_assoc($res)) {
							echo <<<EOT
								shortcut.add("{$row['D142_Atalho']}",function() {
									$.getJSON('/cad/cad055/ajax/verificarPermissao/?ajax=true&C029_Id=' + encodeURIComponent('{$row['D142_C029_Id']}') + '&callback=?', function(request) {
										if (request.code) {
											{$row['D142_Codigo']}
											shortcut.remove("ESC");
											$('#teclasf1').remove();
										}
									});
									return false;
								});
EOT;
							$teclasAtalhoF1 .= "<span style=\"font-size: 1.3em; font-weight: bold;\">{$row['D142_Atalho']}</span> ({$row['D142_Nome']})<br />";
					
						}
					}

			echo <<<EOT
						shortcut.add("F1",function() {
							if ($('#teclasf1').size() != 0) { return false; }
							$('<div>')
								.attr('id', 'teclasf1')
								.css({
									'width': '100%',
									'position': 'fixed',
									'top': '150px',
									'text-align': 'center',
									'color': 'white',
									'font-weight': 'normal',
									'font-size': '18px',
									'z-index': '999999999',
									'background-color': 'black',
									'padding': '15px 0'
								})
								.html('{$teclasAtalhoF1}')
								.appendTo('body');
								shortcut.add("Esc",function() {
									shortcut.remove("Esc");
									$('#teclasf1').remove();
									return false;
								});
							});
						}, 150);
					});	

				} </script></div>
EOT;
			}

	break;

	case 'ven015ContentProdutosPDV':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$divOutro = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '130px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven015/content/ven015ContentFormProdutosPDV/', '&T007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2) . '&divOutro=' . urlencode($divOutro)),
			array($content2, '/ven/ven015/grid/ven015GridProdutosPDV/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($divOutro)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentFormProdutosPDV':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $r_divOutro);
		$g['smarty']->assign('content1Width', '350px');
		$g['smarty']->display('content-2columnsPDV.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven015/form/ven015FormProdutosPDV/', '&tab=produto&T007_Id=' . urlencode($r_T007_Id) . '&divReload=' . urlencode($r_divReload) . "&divOutro=" . urldecode($r_divOutro)),
			array($r_divOutro, '/ven/ven015/outro/ven015TotalPDV/', '&acaoId=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentPrincipalPagamentosPDV':
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
			array($content1, '/ven/ven015/tab/ven015TabPrincipalPagamentosPDV/', '&acaoId=' . urlencode("{$r_acaoId}") . '&T007_Id=' . urlencode("{$r_acaoId}") . '&tabela=' . urlencode("{$r_from}")),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015contentPagamentosPDV':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '40px');

		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven015/outro/ven015TrocoPagamentoPDV/', '&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/ven/ven015/grid/ven015GridPagamentosPDV/', '&divReload=' . urlencode($content1).'&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentFormCPF':
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
			array($content1, '/ven/ven015/tab/ven015TabFormCPF/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	
	case 'ven015ContentFormVendedor':
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
			array($content1, '/ven/ven015/tab/ven015TabFormVendedor/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentFormDAV':
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
			array($content1, '/ven/ven015/tab/ven015TabFormDAV/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentAplicarDesconto':

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
			array($content1, '/ven/ven015/tab/ven015TabAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&tabelaProduto=' . urlencode($r_tabelaProduto)),
		);
		echo gProcessaAcoes($actions);

	break;

	case 'ven015ContentTransmitirNotaFiscal':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '45px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven015/outro/ven015CabecalhoTransmitirNota/', '&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/ven/ven003/tab/ven003TabTransmitirNotaFiscal/', '&acaoId=' . urlencode($r_acaoId) . '&naoFechaJanelaPDV=' . urlencode($r_naoFechaJanelaPDV))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven015ContentDiversos':
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
			array($content1, '/ven/ven015/tab/ven015TabDiversos/', '&C007_Id=' . urlencode($g['usuarioAtual']) . '&divRefresh=' . urlencode($r_divRefresh)),
		);
		echo gProcessaAcoes($actions);
	break;

}
