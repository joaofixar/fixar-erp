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
		case 'est016content01':
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
			array($content1, '/est/est016/tab/est016tab01/')
			);
			echo gProcessaAcoes($actions);

		break;

	/**
	* Form Principal
	* Chamado em: est016grid01
	*/
	case 'est016content02':
		$sql="select T224_Id from T224 where T224_Id='{$r_acaoId}'";
		$consultaT224_Id=mysql_query($sql);
		if (!empty($r_acaoId) && mysql_num_rows($consultaT224_Id)<=0) {
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

		$sql = mysql_query("SELECT * FROM T224 WHERE T224_Id='{$r_acaoId}'");
		$mSql = mysql_fetch_array($sql);
		//Remoção das notificações de ordem de compra
		if($mSql['T224_C007_Id_Vendedor_Interno'] == $g['usuarioAtual']){
			if($mSql['T224_Flag_Visualizou_Liberacao'] == 'N'){
				if($mSql['T224_Flag_Status'] == '2'){
					echo gGeraAlertMsg('Atenção', 'Esta ordem de compra foi <b>negada</b>.');
				} else {
					echo gGeraAlertMsg('Atenção', 'Esta ordem de compra foi <b>aprovada</b>.');
				}
				mysql_query("UPDATE T224 SET T224_Flag_Visualizou_Liberacao='S' WHERE T224_Id='{$r_acaoId}'");
			}
		}

		$D013_Id = $mSql['T224_D013_Id'];
			
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
		array($content1, '/est/est016/tab/est016tab02/', '&acaoId=' . urlencode($r_acaoId) . '&divIdMain=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions, false);
		
		$T224 = mysql_query("SELECT T224_Flag_Status from T224 where T224_Id='{$r_acaoId}'");
		$mT224 = mysql_fetch_array($T224);
		
		$disabled2="";
		if($mT224['T224_Flag_Status'] != '' && $mT224['T224_Flag_Status'] != '0' && $mT224['T224_Flag_Status'] != '2' && $g['nivelAlcada'] < 4){
			$disabledAtualizar = 'disabled="disabled"';
		}

		$disableColeta="";
		if($mT224['T224_Flag_Status'] > 3 OR $mT224['T224_Flag_Status'] == '1'){
			$disableColeta = 'disabled="disabled"';
		}

		$disableFinalizar = false;
		if($mT224['T224_Flag_Status']==10){
			$disableFinalizar = 'disabled="disabled"';
		}

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';

		// 1 - Compra 2 - Transferência
		if ($mSql['T224_Flag_Tipo'] == 1) {
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016ExcluirItensMarcados">Excluir</button>';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016FaturarPedido">Faturar</button>';
			if($g['C031']['botaoAplicarDesconto'] == 'S'){
				$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016AplicarDesconto">Desconto</button>';
			}
			if($g['C031']['permissaoFaturarEmergencia'] == 'S'){
				if (gTemPermissao(87)) {
					$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016FaturarEmergencia">Fatura emergência</button>';
				}
			}
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016AplicarIpv">Aplicar IPV</button>';
			$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016ImportarItens">Importa</button>';
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016AtualizarAliquotas">Atualizar</button>';
			$botoesEsq .= '<button type="submit" '.$disabledAtualizar.' id="est016AtualizarOpcoes">Atualiza</button>';
			$botoesEsq .= '<button type="submit" id="est016EnviarEmail">E-mail</button>';
			$botoesEsq .= '<button type="submit" id="est016EnviarWhats">WhatsApp</button>';
			$botoesEsq .= '<button type="submit" id="est016ImprimirPedido">Imprime</button>';
			$botoesEsq .= '<button type="submit" id="est016AnaliseOC">Análise</button>';
			//if($mT224['T224_Flag_Status'] < 9){
				$botoesEsq .= '<button type="submit" id="est016AtualizarCusto">Atualiza custo</button>';
			//}
			if($mT224['T224_Flag_Status'] == 12){
				$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016ReativarPedido">Reativa pedido</button>';
			}
			$botoesEsq .= '<button type="submit" '.$disableColeta.' id="est016SolicitarColeta">Solicita coleta</button>';
			if($mSql['T224_Flag_Status'] == '3'){
				$botoesEsq .= '<button type="submit" id="est016RetornarOCPendente">Retorna pendente</button>';	
			}
			//$botoesEsq .= '<button type="submit" '.$disabled2.' id="est016DividirPedidoBtn">Dividir</button>';
			//$botoesEsq .= '<button type="submit" id="est016Compras">Compras</button>';
			if($mT224['T224_Flag_Status'] >= 3){
				$botoesEsq .= '<button type="button" '.$disableFinalizar.' id="est016FinalizarOCManual">Finaliza manual</button>';
			}
		} else {
			// Encontra o Pedido gerada por esta OT
			$sql = "SELECT T005_Id, T005_Flag_Status, T005_Status_Pedido(T005_Flag_Status,1) as status, C004_Nome_Abreviado FROM T005 LEFT JOIN C004 ON T005_C004_Id=C004_Id WHERE T005_T224_Id = '{$mSql['T224_Id']}'";
			$resT005 = mysql_query($sql);
			if (mysql_num_rows($resT005) == 0) {
				// Botão Gerar Pedido
				$botoesEsq .= '<button type="submit" id="est016GerarPedidoTransf">Gerar Pedido de Transferência</button>';
			} else {
				$botoesEsq .= '<button type="submit" disabled="disabled">Gerar Pedido de Transferência</button> (Gerou pedido(s): ';
				$countPedidoOT = 1;
				while ($rowT005 = mysql_fetch_assoc($resT005)) {
					$botoesEsq .= ($countPedidoOT>1) ? ' e ' : '';
					$botoesEsq .=  "<b>{$rowT005['T005_Id']}</b> em <b>{$rowT005['C004_Nome_Abreviado']}</b> status <b>{$rowT005['status']}</b> ";
					$countPedidoOT++;
				}
				$botoesEsq .= trim($botaoEsq) . ')';
			}
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
				$('#est016RetornarOCPendente').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est016/form_func-ajax/retornarOCPendente/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Sucesso', request.data, \$buttons);
									divRefresh('{$g['divIdParent']}', true);
									divRefresh('{$g['divId']}', true);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Pedido', 'Deseja retornar esta Ordem de Compra para o status pendente?', \$buttons);
				});

				$('#est016FinalizarOCManual').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							if('{$g['C031']['obrigatorioOCNotaFiscalCompra']}' == 'S'){
								$.getJSON('/est/est016/form_func-ajax/finalizarOCManual/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Finalizar OC', request.data, \$buttons);
										divRefresh('{$g['divId']}', true);
									}
								});
								$(this).dialog("destroy").remove();
							} else{
								$.getJSON('/est/est016/form_func-ajax/finalizarOCManualSemNota/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Finalizar OC', request.data, \$buttons);
										divRefresh('{$g['divId']}', true);
									}
								});
								$(this).dialog("destroy").remove();
							}
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Finalizar OC', 'Deseja finalizar esta Ordem de Compra manualmente?', \$buttons);
				});

				$('#est016GerarPedidoTransf').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							var T224_Id = {$r_acaoId};
							idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Gerando pedido de transferência</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$.getJSON('/est/est016/form_func-ajax/gerarPedidoTransferencia/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var mensagem = request.data;
									var T005_Id  = request.T005_Id;
									var empresa  = request.empresa;
									var T005     = request.T005;
									
									$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b>0% Processando CFOP pedido</b></br>');
									
									Object.values(T005).forEach(ven002AtualizarCFOP);

									function ven002AtualizarCFOP(T005_Id,index,array){
										$.getJSON('/ven/ven002/form_func-ajax/atualizarCFOP/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
											if(request.code){
												var T005_Id = request.T005_Id;
												$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b><font color="green>100% CFOP pedido processada</font></b></br>'+'<br><b>0% Processando alíquotas pedido</b></br>');
												ven002AtualizarAliquotas(idProgressBar,T005_Id,1); 

												function ven002AtualizarAliquotas(idProgressBar,T005_Id, inicio){
													$.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
														if (request.concluido == 0) {
															$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b><font color="green>100% CFOP pedido processada &#10004;</font></b></br>'+ request.percentual + '% Processando alíquotas pedido</b>' + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
															var T005_Id = request.T005_Id;
															ven002AtualizarAliquotas(idProgressBar,T005_Id,0);
														}else{
															if(request.code){
																var T005_Id = request.T005_Id;
																$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b><font color="green">100% CFOP pedido processada &#10004;</font></b></br>'+'<b><font color="green">100% Alíquotas pedido processadas &#10004;</font></b></br>'+'<b>0% Processando CFOP OC</b></br>');
																$.getJSON('/est/est016/form_func-ajax/atualizarCFOP/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&T005_Id= '+encodeURIComponent(T005_Id) +'&extra=' +encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
																	if(request.code){
																		var T005_Id = request.T005_Id;
																		$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b><font color="green">100% CFOP pedido processada &#10004;</font></b></br>'+'<b><font color="green">100% Alíquotas pedido processadas &#10004;</font></b></br>'+'<b><font color="green">100% CFOP OC processada &#10004;</font></b></br>'+'<b>0% Processando alíquotas OC');
																		est016AtualizarAliquotasOC(idProgressBar,T224_Id,1);

																		function est016AtualizarAliquotasOC(idProgressBar,T224_Id, inicio){
																			$.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) +'&T005_Id= '+encodeURIComponent(T005_Id)+ '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
																				if (request.concluido == 0) {
																					$('#progressBar').html('<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b></br>' + '<b><font color="green">100% CFOP pedido processada &#10004;</font></b></br>'+'<b><font color="green">100% Alíquotas pedido processadas &#10004;</font></b></br>'+'<b><font color="green">100% CFOP OC processada &#10004;</font></b></br><b>' + request.percentual + '% Processando alíquotas OC</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
																					est016AtualizarAliquotasOC(idProgressBar,T224_Id,0);
																				}else{
																					if(request.code){
																						fecharJanela('{$g['divId']}');
																						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
																						$('#dc-' + idProgressBar).dialog("destroy").remove();
																						dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Pedido de transferência gerado &#10004;</font></b><br><b><font color="green">100% CFOP pedido processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b>'+'<br><b><font color="green">100% CFOP OC processada &#10004;</font></b></br>'+'<b><font color="green">100% Alíquotas OC processadas &#10004;</font></b></br>'+'<br><b><font color="blue">'+mensagem+'</b></font>');
																					}else{
																						var T005_Id = request.T005_Id;
																						$('#dc-' + idProgressBar).dialog("destroy").remove();
																						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																						dialogConfirm('Erro ao processar alíquotas OC', request.data, \$buttons);
																					}
																				} 
																			});	                  
																		}
																	}else{
																		var T005_Id = request.T005_Id;
																		$('#dc-' + idProgressBar).dialog("destroy").remove();
																		var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																		dialogConfirm('Erro ao processar CFOP OC', request.data, \$buttons);
																	}
																});									                    	
															}else{
																var T005_Id = request.T005_Id;
																$('#dc-' + idProgressBar).dialog("destroy").remove();
																var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																dialogConfirm('Erro ao processar alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><font color="blue">Foi gerado o pedido de transferência nº'+T005_Id+' na '+empresa+'</font></br><br><font color="red"><b>* * ERRO * *</b><br>Não foi possível atualizar alíquotas'+'<br>Acesse o pedido, atualize as alíquotas e fature-o novamente'+'</font></b><br>'+'<br>Erro(s) encontrado(s):<br>'+ request.erros+'<br>'+request.data+'</div>', \$buttons);
																$.getJSON('/est/est016/form_func-ajax/alterarStatusPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
																	if(request.code){
																		fecharJanela('{$g['divId']}');
																		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615		
																	}
																});	
															}	
														} 
													});	                  
												}
											}else{
												var T005_Id = request.T005_Id;
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><font color="blue">Foi gerado o pedido de transferência nº'+T005_Id+' na '+empresa+'</font></br><br><font color="red"><b>* * ERRO * *</b><br>Não foi possível atualizar CFOP e Alíquotas'+'<br>Acesse o pedido, atualize CFOP e alíquotas e fature-o novamente'+'</font></b><br>'+'<br>Erro(s) encontrado(s):<br>'+request.data+'</div>', \$buttons);
												$.getJSON('/est/est016/form_func-ajax/alterarStatusPedido/?ajax=true&T005_Id=' + encodeURIComponent(T005_Id) + '&callback=?', function(request) {
													if(request.code){
														fecharJanela('{$g['divId']}');
														abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615	
													}
												});
											}
										});	
									}

								} else {
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									var \$buttons = { 
										"Fechar": function() { 
											$(this).dialog("destroy").remove(); 
										} 
									}
									dialogConfirm('Erro ao gerar pedido de transferência', request.data, \$buttons);
								}
							});
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Pedido', 'Deseja Gerar um Pedido de Transferência?', \$buttons);
				});

				$('#est016SolicitarColeta').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est016/form_func-ajax/verificaAlcada/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request) {
						 $('#{$g['divId']}').hideLoading();
						 if (request.code === true) {
						 	var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/est/est016/form_func-ajax/solicitarColeta/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request2) {
										$('#{$g['divId']}').hideLoading();
										if (request2.code) {
											var \$buttons = {
												 "Fechar": function() {
													$(this).dialog("destroy").remove();
													divRefresh('{$g['divIdParent']}', true);
												 } 
											};
											dialogConfirm('Ação concluída', request2.data, \$buttons);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request2.data, \$buttons);
										}
									});
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Coleta', 'Deseja solicitar a coleta dos produtos?', \$buttons);
						 } else if (request.code === 'erro') {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
	    				} else if(request.code == 'analise'){
						 	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise de Ordem de Compra', '/est/est016/outro/est016AnaliseOC/', '&tab=geral&coleta=S&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
						} else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/est/est016/form_func-ajax/enviarParaAnalise/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request2) {
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
				$('#est016AplicarIpv').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar IPV', '/est/est016/content/est016ContentAplicarIpv/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#est016ImportarItens').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Ítens', '/est/est016/content/est016ContentImportarItens/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#est016AplicarDesconto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/est/est016/content/est016ContentAplicarDesconto/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [400,200]);
				});
				$('#est016DividirPedidoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Pedido', '/est/est012/content/est012ContentDividirPedido/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
				});
				$('#est016ImprimirPedido').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est016/form_func-ajax/verificaAlcada/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						 $('#{$g['divId']}').hideLoading();
						 if (request.code === true) {
						 	$('#{$g['divId']}').showLoading();
						 	$.getJSON('/est/est016/form_func-ajax/est016ImprimirPedido/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if(request.code) {
									var w = window.open(request.data);
									w.addEventListener("load", function(event) {
										setTimeout(function() {
											$.getJSON('/est/est016/form_func-ajax/est016DeletarPDFOrdemCompra/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {});
										}, 3000);
									});
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    					dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						 } else if (request.code === 'erro') {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    					dialogConfirm('Erro', request.data, \$buttons);
	    				} else if(request.code == 'analise'){
						 	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise de Ordem de Compra', '/est/est016/outro/est016AnaliseOC/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
						 } else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/est/est016/form_func-ajax/enviarParaAnalise/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request2) {
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

				$('#est016EnviarWhats').bind('click', function(data) {
					$.getJSON('/est/est016/form_func-ajax/est016PhoneWhats/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
						dialogConfirm('Enviar ordem compra WhatsApp: ' + decodeURIComponent(request1.phoneWhatsApp), "O que deseja fazer? " + decodeURIComponent(request1.notificaSemPhone) , [
							{
								text: "Enviar formato texto",
								click: function() {
									$.getJSON('/est/est016/form_func-ajax/pedidoWhats/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + decodeURIComponent(request1.phoneWhatsApp) + '&callback=?', function(request) {
										nova_janela('https://api.whatsapp.com/send?phone=' + request1.phoneWhatsApp + '&text=' + request.data,0,0,750,500,'yes','no','no','no','yes','no','WhatsApp');
									});
									$(this).dialog("destroy").remove();
									return false;
								}
							},
							{
								text: "Enviar link PDF",
								click: function() {
									$.getJSON('/est/est016/form_func-ajax/pedidoWhatsPDF/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&phone=' + decodeURIComponent(request1.phoneWhatsApp) + '&callback=?', function(request) {
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

				$('#est016AtualizarAliquotas').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T224_Id = $('#{$g['divId']} #T224_Id').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
			
			$('#est016FaturarEmergencia').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Faturar Emergência', '/est/est016/content/est016ContentFaturarEmergencia/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [450,180]);
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Faturar Emergência?', 'Este pedido será encaminhado direto para emissão de NFe!', \$buttons);
			});
			
			$('#est016ReativarPedido').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$.getJSON('/est/est016/form_func-ajax/reativarPedido/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

			$('#est016EnviarEmail').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/est/est016/form_func-ajax/verificarParcelas/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request1) {
					if(request1.code) {
						 $.getJSON('/est/est016/form_func-ajax/verificaAlcada/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							 $('#{$g['divId']}').hideLoading();
							 if (request.code == true) {
						 		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
							 } else if(request.code == 'erro') {
									var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
								    dialogConfirm('Enviar E-mail', request.data, \$buttons);
							 } else if(request.code == 'analise'){
							 	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise de Ordem de Compra', '/est/est016/outro/est016AnaliseOC/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
							 } else {
								var \$buttons = {
									"Sim": function() {
										$('#{$g['divId']}').showLoading();
										$(this).dialog("destroy").remove();
										$.getJSON('/est/est016/form_func-ajax/enviarParaAnalise/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request2) {
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

			$('#est016Compras').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Compras', '/est/est016/content/est016ContentCompras/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [850,600]);
			});

			$('#est016ExcluirItensMarcados').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/form_func-ajax/excluirItensMarcados/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

			$('#est016AtualizarOpcoes').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Atualizar Aliquotas": function() {
						$(this).dialog("destroy").remove();
						est016SelecionarCamposAtualizacao('aliquotas', function(opcoes) {
								var T224_Id = $('#{$g['divId']} #T224_Id').val();
								idProgressBar = dialogAlert('Processando alíquotas',"<span id='progressBar'><b><br>0% Processando Alíquotas</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            est016AtualizarAliquotasLoop(idProgressBar,T224_Id,1,opcoes);
						});
					},
					"Atualizar CFOP": function() {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/est/est016/content/est016ContentAtualizarCFOP/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [500,300]);
						$(this).dialog("destroy").remove();
					},
					"Atualizar Aliquotas/CFOP Tabela": function() {
						$(this).dialog("destroy").remove();
						est016SelecionarCamposAtualizacao('tabela', function(opcoes) {
								var T218_Id = $('#{$g['divId']} #T218_Id').val();
								var T224_Id = $('#{$g['divId']} #T224_Id').val();
								idProgressBar = dialogAlert('Processando alíquotas',"<span id='progressBar'><b>0% Processando alíquotas</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            est016AtualizarAliquotasTabelaLoop(idProgressBar,T224_Id,1,opcoes);
						});
					},
					"Cancelar": function() { 
						$(this).dialog("destroy").remove();
					}
				};
				dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
			});

			$('#est016AtualizarOpcoesX').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/est/est016/content/est016ContentAtualizarCFOP/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [500,250]);
			});

			$('#est016AnaliseOC').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/est/est016/form_func-ajax/verificaAlcada/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if(request.code == 'erro') {
						var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise de Ordem de Compra', '/est/est016/outro/est016AnaliseOC/', '&tab=geral&T224_Id=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
					}
				});
			});

			$('#est016AtualizarCusto').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/est/est016/form_func-ajax/verificaAlcada/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if(request.code == 'erro') {
						var \$buttons = { "OK": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					} else {
						var \$buttons = {
							"Apenas esta venda": function() {
								var \$buttons2 = {
									"Sim": function() {
										$('#{$g['divId']}').showLoading();
										$.getJSON('/est/est016/form_func-ajax/atualizarCustoVenda/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
							"Cancelar": function() { $(this).dialog("destroy").remove(); }
						};
						var dialog = dialogConfirm('Atualizar Custos', '<b>Atenção!</b><br />Você deseja atualizar o custo:', \$buttons);
						var buttons = $('#dc-' + dialog).parent().find('.ui-dialog-buttonpane button')
						var botao0 = $(buttons[0]);
						if('{$g['nivelAlcada']}' < '{$g['C031']['nivelAlcadaAtualizarCustoVenda']}'){
							$(botao0).attr('disabled', 'disabled').addClass('ui-state-disabled');
						}
					}
				});
			});

			function recarregarContent(){
				divRefresh('{$g['divId']}');
				divRefresh('{$g['divIdParent']}');
			}

			function est016SelecionarCamposAtualizacao(tipo, concluir){
				var grupos = [
					{ titulo: 'Impostos', campos: [['ipi', 'IPI'], ['icms', 'ICMS'], ['st', 'ST'], ['pisCofins', 'PIS/COFINS']] },
					{ titulo: 'Dados do produto', campos: [['cfop', 'CFOP'], ['origem', 'Origem do produto'], ['ncm', 'NCM'], ['unidade', 'Unidade de medida'], ['peso', 'Peso do produto'], ['precoTabela', 'Preço da tabela do fornecedor']] }
				];
				if (tipo == 'aliquotas') grupos[1].campos = [['ncm', 'NCM'], ['unidade', 'Unidade de medida'], ['peso', 'Peso do produto']];
				var botoes = {
					"Atualizar": function() {
						var opcoes = {};
						$('#dc-' + dialogo).find('input.est016CampoAtualizacao').each(function() {
							opcoes[$(this).attr('data-campo')] = $(this).is(':checked');
						});
						$(this).dialog('destroy').remove();
						concluir(opcoes);
					},
					"Cancelar": function() { $(this).dialog('destroy').remove(); }
				};
				var dialogo = dialogConfirm('Selecionar atualização', 'Marque os dados que deseja atualizar:', botoes);
				$('#dc-' + dialogo).dialog('option', 'width', 380);
				var area = $('#dc-' + dialogo).find('p');
				area.append('<br />');
				var colunas = $('<div>').css({'white-space':'nowrap'}).appendTo(area);
				for (var grupo = 0; grupo < grupos.length; grupo++) {
					var coluna = $('<div>').css({'display':'inline-block', 'vertical-align':'top', 'width': grupo === 0 ? '38%' : '62%', 'white-space':'normal'}).appendTo(colunas);
					$('<div>').text(grupos[grupo].titulo).css({'color':'#2e75b6', 'font-weight':'bold', 'margin-bottom':'3px', 'border-bottom':'1px solid #b7cde3'}).appendTo(coluna);
					for (var i = 0; i < grupos[grupo].campos.length; i++) {
						var campo = grupos[grupo].campos[i];
						$('<input type="checkbox" checked="checked" class="est016CampoAtualizacao">').attr('data-campo', campo[0]).appendTo(coluna);
						$('<label>').html(campo[1]).appendTo(coluna).append('<br />');
					}
				}
			}

			function est016AtualizarAliquotasLoop(idProgressBar, T224_Id, inicio, opcoes){
                $.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>' + request.percentual + '% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
						est016AtualizarAliquotasLoop(idProgressBar,T224_Id,0,opcoes);
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

	        function est016AtualizarAliquotasTabelaLoop(idProgressBar, T224_Id, inicio, opcoes){
                $.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent("{$r_acaoId}") + '&usarTabelaFornecedor=true' +  '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
                    if (request.concluido == 0) {
                    	$('#progressBar').html('<b>' + request.percentual + '% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
						est016AtualizarAliquotasTabelaLoop(idProgressBar,T224_Id,0,opcoes);
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
//Retirando refresh automatico no divParent, estava causando lentidão em algumas telas
/* 		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				divRefresh('{$g['divIdParent']}', true);
			} </script></div>
EOT; */

		break;
		
		case 'est016ContentFaturarEmergencia':
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
			array($content1, '/est/est016/form/est016FormFaturarEmergencia/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);						

		break;

		case 'est016content03':
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
			array($content1, '/est/est016/tab/est016tab03/', '&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016content04':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '213px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			//array($content1, '/est/est016/form/est016form02/', "&tab=geral&T224_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content1, '/est/est016/content/est016contentform/', "&T224_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016grid02/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
		break;
					
		case 'est016contentform':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->display('content-2columnsPedido.tpl');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est016/form/est016form02/', "&tab=geral&T224_Id=" . urlencode($r_T224_Id) . "&divReload=" . urlencode($r_divReload) . "&divOutro=" . urldecode($content2)),
			array($content2, '/est/est016/outro/est016observacoes/', '&acaoId=' . urlencode($r_T224_Id)),
			);
			echo gProcessaAcoes($actions);
		
		break;
			
		case 'est016contentTrocas':
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
			array($content1, '/est/est016/form/est016formTrocas/', "&tab=trocas&T224_Id=" . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016gridTrocas/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
		break;			

		case 'est016content05':
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
			array($content1, '/est/est016/form/est016form03/', "&tab=geral&T224_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016grid03/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016content06':
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
			array($content1, '/est/est016/form/est016form04/', "&tab=editar_material&T230_Id=" . urlencode($r_T230_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016grid04/', '&T230_Id=' . urlencode($r_T230_Id)  . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016content07':
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
			array($content1, '/est/est016/form/est016form05/', "&tab=geral&T230_Id=" . urlencode($r_T230_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016grid05/', '&acaoId=' . urlencode($r_T230_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016content08':
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
			array($content1, '/est/est016/tab/est016tab03/', "&tab=geral&T230_Id=" . urlencode($r_T230_Id))
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016ContentEditarProduto':

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
			array($content1, '/est/est016/tab/est016TabEditarProduto/', '&acaoId=' . urlencode($r_acaoId) . '&T224_Id=' . urlencode($r_T224_Id)),
			);
			echo gProcessaAcoes($actions);
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est016FormEditarProdutosBtn">Alterar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			echo  $g['smarty']->fetch('buttons-floatBottom.tpl');

	break;
		
		
	case 'est016ContentAplicarIpv':

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
			array($content1, '/est/est016/form/est016FormAplicarIpv/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est016AplicarIpvBtnExecutar">Executar</button>';
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
			$('#est016AplicarIpvBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T224_Id = $('#{$g['divId']} #T224_Id').val();
						var IPV = $('#{$g['divId']} #IPV').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/form_func-ajax/aplicarIPV/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&IPV=' + encodeURIComponent(IPV) + '&callback=?', function(request) {
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
	
	case 'est016ContentImportarItens':

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
			array($content1, '/est/est016/form/est016FormImportarItens/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est016ImportarItensBtnExecutar">Executar</button>';
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
			$('#est016ImportarItensBtnExecutar').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T224_Id = $('#{$g['divId']} #T224_Id').val();
						var T224_Id_Origem = $('#{$g['divId']} #T224_Id_Origem').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est016/form_func-ajax/importarItens/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&T224_Id_Origem=' + encodeURIComponent(T224_Id_Origem) + '&callback=?', function(request) {
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

		case 'est016ContentAbrePagamento':
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
			array($content1, '/est/est016/tab/est016TabPagamento/', '&T224_Id=' . urlencode($r_T224_Id)),
			);
			echo gProcessaAcoes($actions);

			break;

		case 'est016ContentPagamento':
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
			array($content1, '/est/est016/form/est016FormPagamento/', "&tab=geral&T224_Id=" . urlencode($r_T224_Id) . "&acaoId=" . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016GridPagamento/', '&acaoId=' . urlencode($r_T224_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est016ContentEmail':
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
			//array($content1, '/est/est016/grid/est016GridEmail/', '&acaoId=' . urlencode($r_acaoId)),
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&T224_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est016ContentDocumentos':
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
			array($content1, '/est/est016/form/est016FormDocumentos/', "&tab=geral&T224_Id=" . urlencode($r_T224_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/est/est016/grid/est016GridDocumentos/', '&acaoId=' . urlencode($r_T224_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est016ContentDetalhesCheque':
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
			array($content1, '/est/est016/form/est016FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T226_Id)),
			);
			echo gProcessaAcoes($actions);			
		
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="est016FormDetalhesCheque_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			//$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');			

		break;	
		
	case 'est016ContentAplicarDesconto':

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
		array($content1, '/est/est016/tab/est016TabAplicarDesconto/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
    
    case 'est016ContentImportarDoc':
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
        array($content1, '/est/est016/form/est016FormImportarDoc/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id) . '&divReload=' . urlencode($r_divReload)),
        );
        echo gProcessaAcoes($actions);

    break;
    
    
    case 'est016contentCancelarPedido':
            
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
            	array($content1, '/est/est016/form/est016FormCancelarPedido/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
            );
            echo gProcessaAcoes($actions);

    break;

    case 'est016contentCancelarOCObs':

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
        	array($content1, '/est/est016/form/est016FormCancelarOCObs/', '&tab=geral&acaoId=' . urlencode($r_T224_Id)),
        );
        echo gProcessaAcoes($actions);
    break;
            
    case 'est016ContentAgregados':
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
            array($content1, '/est/est016/form/est016FormAgregados/', '&tab=geral&T225_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
            array($content2, '/est/est016/grid/est016GridAgregados/', '&acaoId=' . urlencode($r_acaoId) . '&T225_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
        );
        echo gProcessaAcoes($actions);
        
        /**
         * Botões
         */
        // Definição dos botões
        $botoesEsq = '';
        //$botoesDir = '';
        //$botoesEsq .= '<button type="submit" id="est001ContetnProdutosBtnConfirmarTudo">Confirmar Tudo</button>';
        //$botoesEsq .= '<button type="submit" id="est001ContetnProdutosBtnZerarTudo">Zerar Tudo</button>';
        //$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        //$g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        $novoIdTelaForm = uniqid();        
    break;
   
	case 'est016ContentProdutosDefeitos':
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
        array($content1, '/est/est016/grid/est016GridPedidoProdutosDefeito/', '&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);
	break;

	case 'est016contentIncluirPedido':
		$acaoId=isset($r_acaoId) ? $r_acaoId : false;
		$GerarOCEstoque = isset($r_GerarOCEstoque) ? $r_GerarOCEstoque : false;
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
        array($content1, '/est/est016/tab/est016TabIncluirPedido/', '&acaoId=' . urlencode($r_acaoId) . '&GerarOCEstoque=' . urlencode($GerarOCEstoque)),
        );
        echo gProcessaAcoes($actions);
	break;

	case 'est016ContentCompras':
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
			array($content1, '/est/est016/tab/est016TabCompras/', '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est016ContentCotar':
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
			array($content1, '/est/est016/tab/est016TabCotar/', '&T224_Id=' . urlencode($r_T224_Id) . '&T225_Id=' . urlencode($r_T225_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="est016FormCotar_btn">Cotar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016ContentComprar':
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
			array($content1, '/est/est016/tab/est016TabComprar/', '&T224_Id=' . urlencode($r_T224_Id) . '&T225_Id=' . urlencode($r_T225_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		echo gProcessaAcoes($actions);

		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="est016FormComprar_btn">Comprar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016ContentMudarPedidoEmpresa':
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
			array($content1, '/est/est016/form/est016FormMudarPedidoEmpresa/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id)),
		);
		echo gProcessaAcoes($actions);
	break;


	case 'est016ContentAtualizarCFOP':
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
			array($content1, '/est/est016/form/est016FormAtualizarCFOP/', '&tab=geral&T224_Id=' . urlencode($r_T224_Id) . '&opcoes=' . urlencode($r_opcoes)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est016ContentPrincipalAnalise':
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
			array($content1, '/est/est016/tab/est016TabPrincipalAnalise/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
	case 'est016ContentNaoLiberar':
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
			array($content1, '/est/est016/tab/est016TabNaoLiberar/', '&T224_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est016ContentNaoLiberarTelaAnalise':
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
			array($content1, '/est/est016/tab/est016TabNaoLiberarTelaAnalise/', '&T224_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est016ContentProdutosDivergencia':
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
			array($content1, '/est/est016/tab/est016TabProdutosDivergencia/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'GerarOT':
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
			array($content1, '/est/est016/tab/GerarOT/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'apenasOCs':
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
			array($content1, '/est/est016/tab/apenasOCs/')
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est016ContentOrdensCompraGrupo':
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
			array($content1, '/est/est016/tab/est016TabOrdensCompraGrupo/')
		);
		echo gProcessaAcoes($actions);
	break;	

	/**
	* Grid Principal
	* Chamado em: Menu
	*/
	case 'est016contentIncluirOC':
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
		array($content1, '/est/est016/tab/est016tabIncluirOC/')
		);
		echo gProcessaAcoes($actions);

	break;

	case 'est016contentDividirOrdemCompra':
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
        	array($content1, '/est/est016/tab/est016tabDividirOrdemCompra/', '&D024_Id=' . urlencode($r_D024_Id) . '&divRefresh=' . urlencode($g['divId']))
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

	case 'est016ContentEmpresaDividirOrdemCompra':
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
        	array($content1, '/est/est016/tab/est016TabEmpresaDividirOrdemCompra/', "&T224_Id=" . urlencode($r_T224_Id) . '&divRefresh=' . urlencode($r_divRefresh)),
        );
        echo gProcessaAcoes($actions);

        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="est016FormEmpresaDividirOrdemCompra_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
