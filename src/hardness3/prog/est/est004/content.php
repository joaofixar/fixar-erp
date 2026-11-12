<?php
namespace hardness;
/**
* Weiberlan
* 29/11/2011
*/
/**
* Ações
*/
switch ($g['r'][3]) {

	 case 'est004ContentPrincipal':
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
			array($content1, '/est/est004/tab/est004TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est004ContentCadastroNotaFiscal':
        $GerarNFOC = (isset($r_GerarNFOC)) ? $r_GerarNFOC : false;
        $extra = (isset($r_extra)) ? $r_extra : false;
        
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
			array($content1, '/est/est004/tab/est004TabCadastroNotaFiscal/', '&acaoId=' . urlencode($r_acaoId) . '&GerarNFOC=' . urlencode($GerarNFOC) . '&extra=' . urlencode($extra))
		);
		echo gProcessaAcoes($actions);
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est004FormCadastroNota_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
		
		case 'est004ContentEditarNotaFiscal':
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
			array($content1, '/est/est004/tab/est004TabEditarNotaFiscal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions, false);

		$T013 = mysql_query("SELECT T013_Flag_Travar_Nota, 
				                    T013_Flag_Financeiro_Finalizado, 
				                    T013_Flag_Estoque_Finalizado, 
								    D006_Flag_Venda_Compra_Outros,
							    	T013_D024_Id,
				                    T013_Flag_Cancelada
			                   FROM T013 
						  LEFT JOIN T014 ON T014_T013_Id = T013_Id 
						  LEFT JOIN D006 ON D006_Id = T013_D006_Id 
							  WHERE T013_Id = {$r_acaoId}");
		
		$mT013 = mysql_fetch_array($T013);

		$spanCompras='';
		$spanFinanceiro='';
		$spanEstoque='';
		$disabled='';

		if($mT013['T013_Flag_Travar_Nota']=='S'){
			$spanCompras='<span class="ui-icon ui-icon-circle-check" style="float:right;margin-left:2px;"></span>';
			$disabledCompras='disabled';
		}else{
			$disabledFinanceiro='disabled';
			$disabledEstoque='disabled';
		}
		
		if($mT013['T013_Flag_Financeiro_Finalizado']=='S'){
			$spanFinanceiro='<span class="ui-icon ui-icon-circle-check" style="float:right;margin-left:2px;"></span>';
			$disabledFinanceiro='disabled';
		}

		if($mT013['T013_Flag_Estoque_Finalizado']=='S'){
			$spanEstoque='<span class="ui-icon ui-icon-circle-check" style="float:right;margin-left:2px;"></span>';
			$disabledEstoque='disabled';
		}

		if($mT013['T013_Flag_Travar_Nota']=='N' or empty($mT013['T013_Flag_Travar_Nota']) or $mT013['T013_Flag_Financeiro_Finalizado']=='N' or empty($mT013['T013_Flag_Financeiro_Finalizado'])){
			//$disabledEstoque='disabled';
		}

		if($mT013['T013_Flag_Travar_Nota']=='S'){
			$disabledCalcularAnalise = 'disabled';
		}

		$C021=mysql_query("SELECT C030_C029_Id FROM C030 left join C029 ON C029_Id=C030_C029_Id WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S' AND C029_Id='36'");
		if(mysql_num_rows($C021)<=0 && $g['c029Ids'][220]<=0){
			$disabledFinanceiro='disabled';
		}
		/**
		 * Botões
		 */
		if(!isset($g['c029Ids'][189]) || $mT013['T013_Flag_Cancelada']=='S'){
			$disabledCompras         = 'disabled';
			$disabledFinanceiro 	 = 'disabled';
			$disabledEstoque         = 'disabled';
			$disabledCalcularAnalise = 'disabled';
			$disabledCalcularAnalise = 'disabled';
			$disabledWMS = 'disabled';
		}
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		if($r_acaoId){
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004FinalizarNota_btn"'.$disabledCompras.'>Finalizar Compras '.$spanCompras.'</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004FinalizarNotaFinanceiro_btn"'.$disabledFinanceiro.'>Finalizar Financeiro '.$spanFinanceiro.'</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004FinalizarNotaEstoque_btn"'.$disabledEstoque.'>Finalizar Estoque '.$spanEstoque.'</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004Calcular"'.$disabledCalcularAnalise.'>Calcular</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004PreencherLocacao_btn"'.$disabledCalcularAnalise.'>Preencher locação</button>';

			// Basta buscar 1 item que esteja com o campo D006_Flag_Venda_Compra_Outros = 'C' para que o botão "Análise OC" apareça.
			$T014 = mysql_query("SELECT T014_Id
				      			   FROM T014
					          LEFT JOIN D006 ON D006_Id = T014_D006_Id
								  WHERE T014_T013_Id = {$r_acaoId}
								    AND (D006_Flag_Venda_Compra_Outros = 'C' OR D006_Flag_Venda_Compra_Outros = 'T') LIMIT 1;");
			$exibirOC = 'N';
			
			if (mysql_num_rows($T014) > 0) { // Se houver pelo menos 1 registro retornado da query acima (campo D006_Flag_Venda_Compra_Outros = C), mostrar o botão "Análise OC".
				
				$exibirOC = 'S';
				//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004ImportarOCNovo">Ordem compra</button>';
			}

			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004ImportarOS">Importar OS</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004AtualizarEstoqueProdutos">Atualizar Estoque</button>';
			//$botoesEsq .= '<button style="white-space: pre-wrap;" type="submit" id="est004ImportarItensNota">Importar Itens NF</button>';
		}
		//$botoesDir .= '<button style="white-space: pre-wrap;" type="submit" id="est004FormCadastroNota_btn">Enviar</button>';
		//$botoesDir .= '<button style="white-space: pre-wrap;" onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';

		$botoes = array(
			array('titulo' => "Finalizar Compras {$spanCompras}", 'id' => 'est004FinalizarNota_btn', 'desabilitar' => $disabledCompras),
			array('titulo' => "Finalizar Financeiro {$spanFinanceiro}", 'id' => 'est004FinalizarNotaFinanceiro_btn', 'desabilitar' => $disabledFinanceiro),
			array('titulo' => "Finalizar Estoque {$spanEstoque}", 'id' => 'est004FinalizarNotaEstoque_btn', 'desabilitar' => $disabledEstoque),
			array('titulo' => "Calcular", 'id' => 'est004Calcular', 'desabilitar' => $disabledCalcularAnalise),
			array('titulo' => "Preencher locação", 'id' => 'est004PreencherLocacao_btn', 'desabilitar' => $disabledCalcularAnalise),
			array('titulo' => 'Ordem compra', 'id' => 'est004ImportarOCNovo', 'exibirCallback' => "return ('{$exibirOC}' == 'S');"),
			array('titulo' => 'Importar Itens OC', 'id' => 'est004ImportarOC', 'exibir' => false),
			array('titulo' => 'Importar Itens NF', 'id' => 'est004ImportarItensNota', 'exibir' => false),
			array('titulo' => 'Enviar', 'id' => 'est004FormCadastroNota_btn', 'posicao' => 'direita'),
			array('titulo' => 'Fechar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita')
		);

		if($g['C031']['cyberlogInvoiceMethod'] == '2' || $g['C031']['cyberlogInvoiceMethod'] == '3') {
			require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
			$WMS = new WMS();

			$disabled = $WMS->isWarehouseUser() ? '' : 'disabled';

			if($WMS->isEnabled()) {
				$botoes[] = array('titulo' => 'Enviar Recebimento WMS', 'id' => 'est004EnviarParaWMS', 'desabilitar' => $disabled);
				$botoes[] = array('titulo' => 'Cancelar Recebimento WMS', 'id' => 'est004CancelarWMS', 'desabilitar' => $disabled);
			}
		}

		echo Botoes::processar($botoes, 'content');
		
		// Botão Duplicar

		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		// Ações dos botões
		$novoIdTelaForm = uniqid();
		
		$sqlT013 = mysql_query("SELECT * FROM T013 WHERE T013_Id = '{$r_acaoId}'");
		$T013 = mysql_fetch_array($sqlT013);
		$D024_Id = $T013['T013_D024_Id'];

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#est004FinalizarNota_btn').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 0);
				var \$buttons = {
					"Sim": function() {
						var \$dig1 = $(this);
						var value1 = $('#{$g['divId']} #T013_Numero_Nota_Fiscal').val().toUpperCase();
						T013_Id= value1;
						var value2 = $(this).find('input').val().toUpperCase();
						if (value1 != value2) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); \$dig1.find('input').focus(); } }
					    	dialogConfirm('Erro', 'Número da Nota Fiscal não confere!', \$buttons);
						} else {
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
							idProgressBar = dialogAlert('Finalizando Compras',"<span id='progressBar'><b><br>0% Compras finalizadas</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$.getJSON('/est/est004/form_func-ajax/finalizarNotaEntrada/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									$('#progressBar').html('<br><b><font color="green">100% Compras finalizadas &#10004;</font></b></br>' + '<b>0% Totalizando produtos da nota</b></br>');
									est004TotalizarCompras(idProgressBar, T013_Id, 1);

			                    	function est004TotalizarCompras(idProgressBar,T013_Id, inicio){
						               $.getJSON('/est/est004/form_func-ajax/totalizarNotaEntrada/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&extra=' + encodeURIComponent("{$extra_}")+ '&inicio= '+ inicio + '&callback=?', function(request) {
											if (request.concluido == 0) {
				                    			$('#progressBar').html('<br><b><font color="green">100% Compras finalizadas &#10004;</font></b></br><b>' + request.percentual + '<b>% totalizando itens da nota</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
				                    			est004TotalizarCompras(idProgressBar,T013_Id,0);
							                }else{
						                    	if (request.code) {
						                    		var T013_Id = request.T013_Id;
													$('#dc-' + idProgressBar).dialog("destroy").remove();
													dialogConfirm('Compras finalizadas com sucesso!','<br><b><font color="green">100% Formulário validado &#10004;</font></b></br>' + '<b><font color="green">100% Produtos da nota totalizados &#10004;</font></b></br>');
													$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
														setTimeout(function() {
															abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Nota Fiscal - ID: ' + T013_Id, '/est/est004/content/est004ContentEditarNotaFiscal/', '&tab=geral&acaoId=' + T013_Id, [990,550]);
														}, 150);
					  								});
													fecharJanela('{$g['divId']}');
													divRefresh('{$g['divIdParent']}');    
						                        } else {
													$('#dc-' + idProgressBar).dialog("destroy").remove();
													var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    					dialogConfirm('Erro', request.data, \$buttons);
						                        }
						                    } 
						                });	                  
							        }
								} else {
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    			dialogConfirm('Erro!', request.data, \$buttons);
								}
								//divRefresh('{$g['divId']}');
							});
						}
					},
					"Não": function() { $(this).dialog("destroy").remove(); $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1); } 
				};
				var novoId = dialogConfirm('Finalizar Nota', 'Por favor, confirme o número.<br /><br />Número Nota Fiscal: ', \$buttons);
				$('<input>').attr('id', 'conf_T013_Numero_Nota_Fiscal').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
				return false;
			});
			
			$('#est004FinalizarNotaEstoque_btn').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/est/est004/form_func-ajax/verificaFinalizarNotaEntradaEstoque/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
					$('#{$g['divId']}').hideLoading();
					if (request.code) {
						finalizarNotaEstoque();
						return false;
					} else {
						var \$buttons = {
							"Sim": function() {
								$(this).dialog("destroy").remove();
								finalizarNotaEstoque();
							},
							"Não": function() { $(this).dialog("destroy").remove(); $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1); }
						};
						dialogConfirm('Finalizar Estoque', request.data, \$buttons);
					}
				});
			});

			function finalizarNotaEstoque(){
				$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 0);
				var \$buttons = {
					"Sim": function() {
						var \$dig1 = $(this);
						var value1 = $('#{$g['divId']} #T013_Numero_Nota_Fiscal').val().toUpperCase();
						var T013_Id = value1;
						var value2 = $(this).find('input').val().toUpperCase();
						if (value1 != value2) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); \$dig1.find('input').focus(); } }
					    	dialogConfirm('Erro', 'Número da Nota Fiscal não confere!', \$buttons);
						} else {
							idProgressBar = dialogAlert('Finalizando Estoque',"<span id='progressBar'><b><br>0% finalizando estoque</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$.getJSON('/est/est004/form_func-ajax/verificaFinalizarNotaValidar/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									$.getJSON('/est/est004/form_func-ajax/finalizarNotaEntradaEstoque/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											$('#progressBar').html('<br><b><font color="green">100% Estoque finalizado &#10004;</font></b></br>' + '<b>0% Reprocessando histórico</b></br>');
											est004ReprocessandoHistorico(idProgressBar, T013_Id, 1);

											function est004ReprocessandoHistorico(idProgressBar, T013_Id,inicio){
												$.getJSON('/est/est004/form_func-ajax/reprocessaHistoricoNotaEntradaEstoque/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&inicio=' + inicio + '&callback=?', function(request) {
													if (request.concluido == 0) {
														$('#progressBar').html('<br><b><font color="green">100% Estoque finalizado &#10004;</font></b></br><b>' + request.percentual + '<b>% reprocessando histórico</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
														est004ReprocessandoHistorico(idProgressBar,T013_Id,0);
														$.getJSON('/est/est004/form_func-ajax/verificaReprocessaHistorico/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {});
													}else{
														if (request.code) {
															$.getJSON('/est/est004/form_func-ajax/verificaReprocessaHistorico/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
																$('#dc-' + idProgressBar).dialog("destroy").remove();
																dialogConfirm('Estoque finalizado com sucesso!','<br><b><font color="green">100% Estoque finalizado &#10004;</font></b></br>' + '<b><font color="green">100% Histórico reprocessado &#10004;</font></b></br>');
																fecharJanela('{$g['divId']}');
																abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Nota Fiscal', '/est/est004/content/est004ContentEditarNotaFiscal/', '&tab=geral&T013_Id=' + encodeURIComponent('{$r_acaoId}'), [900,500]);
															});
														} else {
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro', request.data, \$buttons);
															$.getJSON('/est/est004/form_func-ajax/verificaReprocessaHistorico/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
															});
														}
													} 
												});	                  
											}
										} else {
											$('#dc-' + idProgressBar).dialog("destroy").remove();
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
									});
								} else {
									$('#dc-' + idProgressBar).dialog("destroy").remove();
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
						}
					},
					"Não": function() { $(this).dialog("destroy").remove(); $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1); } 
				};
				var novoId = dialogConfirm('Finalizar Nota', 'Por favor, confirme o número.<br /><br />Número Nota Fiscal: ', \$buttons);
				$('<input>').attr('id', 'conf_T013_Numero_Nota_Fiscal').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
				return false;
			}
			
			$('#est004FinalizarNotaFinanceiro_btn').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 0);
				var \$buttons = {
					"Sim": function() {
						var \$dig1 = $(this);
						var value1 = $('#{$g['divId']} #T013_Numero_Nota_Fiscal').val().toUpperCase();
						var value2 = $(this).find('input').val().toUpperCase();
						if (value1 != value2) {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); \$dig1.find('input').focus(); } }
					    	dialogConfirm('Erro', 'Número da Nota Fiscal não confere!', \$buttons);
						} else {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est004/form_func-ajax/finalizarNotaEntradaFinanceiro/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    			dialogConfirm('Ok', request.data, \$buttons);
					    			$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    			dialogConfirm('Erro', request.data, \$buttons);
								}
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
							$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
						}
					},
					"Não": function() { $(this).dialog("destroy").remove(); $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1); } 
				};
				var novoId = dialogConfirm('Finalizar Nota', 'Por favor, confirme o número.<br /><br />Número Nota Fiscal: ', \$buttons);
				$('<input>').attr('id', 'conf_T013_Numero_Nota_Fiscal').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
				return false;
			});
			
			$('#est004AtualizarEstoqueProdutos').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est004/form_func-ajax/est004AtualizarEstoqueProdutosAjax/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    				dialogConfirm('Ok', request.data, \$buttons);
			    				divRefresh('{$g['divIdParent']}');
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
				dialogConfirm('Atulizar estoque', 'Você tem certeza? ', \$buttons);
			});
			
			$('#est004Calcular').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
					var T013_Id = $('#{$g['divId']} #T013_Id').val();
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est004/form_func-ajax/Calcular/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						if (request.code) {
							var \$buttons = { 
								"Fechar": function() { $(this).dialog("destroy").remove(); 
									divRefresh('{$g['divId']}');
									$(this).dialog("destroy").remove();
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
		
		$('#est004ImportarOC').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Importar OC', '/est/est004/content/est004ContentImportarOC/', '&tab=geral&T013_Id=' + encodeURIComponent('{$r_acaoId}'), [900,500]);
		});		
		$('#est004ImportarOCNovo').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Análise OC', '/est/est004/content/est004ContentImportarOCNovo/', '&tab=geral&T013_Id=' + encodeURIComponent('{$r_acaoId}'), [900,500]);
		});
		$('#est004ImportarOS').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Importar OS', '/est/est004/content/est004ContentImportarOS/', '&tab=geral&T013_Id=' + encodeURIComponent('{$r_acaoId}'), [900,500]);
		});
		$('#est004ImportarItensNota').unbind('click').bind('click', function(data) {
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Importar Itens da Nota Fiscal', '/est/est004/content/est004ContentImportarItensNota/', '&tab=geral&T013_Id=' + encodeURIComponent('{$r_acaoId}') + '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [900,500]);
		});
		$('#est004PreencherLocacao_btn').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
					var T013_Id = $('#{$g['divId']} #T013_Id').val();
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est004/form_func-ajax/preencherLocacao/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
				    		dialogConfirm('Locações', request.data, \$buttons);
							$(this).dialog("destroy").remove();
							divRefresh('{$g['divIdParent']}', true);
						}
						$('#{$g['divId']}').hideLoading();
					});
					$(this).dialog("destroy").remove();
					divRefresh('{$g['divIdParent']}', true);
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			dialogConfirm('Preencher locação', 'Confirma o preenchimento automático das locações conforme o cadastro do produto? ', \$buttons);
		});

		$('#est004EnviarParaWMS').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();
			
					$.getJSON('/est/est004/form_func-ajax/enviarParaWMS/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						if (request.code) {
							dialogConfirm('Ok', request.data);
						} else {
							dialogConfirm('Erro', request.data);
						}

						$('#{$g['divId']}').hideLoading();
					});

					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			};

			dialogConfirm('Enviar para WMS', 'Você tem certeza que deseja enviar a nota para o WMS?', \$buttons);
		});

		$('#est004CancelarWMS').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
					$('#{$g['divId']}').showLoading();

					$.getJSON('/est/est004/form_func-ajax/cancelarWMS/?ajax=true&T013_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						if (request.code) {
							dialogConfirm('Ok', request.data);
						} else {
							dialogConfirm('Erro', request.data);
						}

						$('#{$g['divId']}').hideLoading();
					});

					$(this).dialog("destroy").remove();
				},
				"Não": function() { $(this).dialog("destroy").remove(); }
			};

			dialogConfirm('Cancelar WMS', 'Você tem certeza que deseja cancelar a nota no WMS?', \$buttons);
		});
	} </script></div>
	<div id="est004ContentEditarNotaFiscal" style="width: 0; height: 0; border: 0; padding: 0; margin: 0;"></div>
EOT;
	break;
	
	case 'est004ContentProdutosNota':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '150px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est004/form/est004FormProdutosNota/', '&tab=geral&T013_Id='. urlencode($r_acaoId) . '&divReload=' . urlencode($content2)), // 
			array($content2, '/est/est004/grid/est004GridProdutosNota/', '&tab=geral&acaoId='. urlencode($r_acaoId) . '&divReload=' . urlencode($content1)) //  . urlencode($r_acaoId)
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est004ContentPagamentoNota':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
 		$g['smarty']->assign('content1Height', '80px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est004/form/est004FormPagamentoNota/', '&tab=geral&acaoId='. urlencode($r_acaoId) . '&divReload=' . urlencode($content2)), // . urlencode($r_acaoId)
			array($content2, '/est/est004/grid/est004GridPagamentoNota/', '&tab=geral&acaoId='. urlencode($r_acaoId) . '&divReload=' . urlencode($content1)) //  . urlencode($r_acaoId)
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est004ContentAssociarItem':
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
			array($content1, '/est/est004/tab/est004TabAssociarItem/', '&T014_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'est004ContentAssociarItemProduto':
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
			array($content1, '/est/est004/tab/est004TabAssociarItemProduto/', '&T014_Id=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'est004ContentImportarOC':
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
			array($content1, '/est/est004/tab/est004TabImportarOC/', '&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est004ContentImportarOCNovo':
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
			array($content1, '/est/est004/tab/est004TabImportarOCNovo/', '&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'est004ContentImportarOS':
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
			array($content1, '/est/est004/tab/est004TabImportarOS/', '&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
		
	break;
	case 'est004ContentInserirQuantidade':
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
			array($content1, '/est/est004/form/est004formInserirQuantidade/', '&tab=geral&T013_Id=' . urlencode($r_T013_Id) . '&T225_Id=' . urlencode($r_T225_Id) . '&T086_Quantidade=' . urlencode($r_T086_Quantidade))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'est004ContentImportarItensNota':
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
			array($content1, '/est/est004/tab/est004TabImportarItensNota/', '&T013_Id=' . urlencode($r_T013_Id) . '&D024_Id=' . urlencode($r_D024_Id))
		);
		echo gProcessaAcoes($actions);
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est004ConcluirInclusaoItens">Incluir os itens selecionados</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004ConcluirInclusaoItens').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').hideLoading();
							$(this).dialog("destroy").remove();
							$.getJSON('/est/est004/grid_func-ajax/est004IncluirItensNF/?ajax=true&T013_Id=' + encodeURIComponent("{$r_T013_Id}") + '&callback=?', function(request) {
								if (request.code) {
									fecharJanela('{$g['divId']}');
									divRefresh('{$g['divIdParent']}', true);
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', "Importação realizada com sucesso", \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Erro', request.data, \$buttons);
								}
							});
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Incluir Itens', 'Desaja incluir os itens na NF', \$buttons);
					
				});
			} </script></div>
EOT;
	break;

	case 'est004ContentCancelarNotaEntrada':
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
			array($content1, '/est/est004/tab/est004TabCancelarNotaEntrada/', '&tab=geral&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est004ContentImportarXml':
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
		array($content1, '/est/est004/tab/est004TabImportarXml/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est004ContentAssociarItemNovo':
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
			array($content1, '/est/est004/tab/est004TabAssociarItemNovo/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$T014 = mysql_query("SELECT T014_Id, T014_T225_Id FROM T014 LEFT JOIN T013 ON T013_Id = T014_T013_Id WHERE T013_Flag_Travar_Nota = 'S' AND T014_Id = '{$r_acaoId}'");
		$disabled = '';
		if(mysql_num_rows($T014) > 0){
			$disabled = "disabled=disabled";
		}
		$T014 = mysql_query("SELECT T014_Id, T014_T225_Id FROM T014 LEFT JOIN T013 ON T013_Id = T014_T013_Id WHERE T014_Id = '{$r_acaoId}'");
		$mT014 = mysql_fetch_assoc($T014);
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est004DesvincularItemNota" ' . $disabled .'>Desvincular Item da Nota</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est004DesvincularItemNota').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est004/grid_func-ajax/est004DesvincularItemNota/?ajax=true&T014_Id=' + encodeURIComponent("{$r_acaoId}") + '&T225_Id=' + encodeURIComponent("{$mT014['T014_T225_Id']}") + '&callback=?', function(request) {
						 $('#{$g['divId']}').hideLoading();
						 if (request.code) {
						 	divRefresh('{$g['divIdParent']}');
						 	fecharJanela('{$g['divId']}');
						 } else {
						 	var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						 }
					 });
				});
			} </script></div>
EOT;
	break;

	case 'est004ContentCadastroObservacao':
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
			array($content1, '/est/est004/tab/est004TabCadastroObservacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId)  . '&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
	break;
	case 'est004ContentCadastroObrigacaoTributaria':
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
			array($content1, '/est/est004/tab/est004TabCadastroObrigacaoTributaria/', '&tab=geral&acaoId='  . urlencode($r_acaoId)  . '&T297_Id='. urlencode($r_T297_Id)  . '&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est004ContentCadastroCiap':
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
			array($content1, '/est/est004/tab/est004TabCadastroCiap/', '&tab=geral&T013_Id=' . urlencode($r_T013_Id))
		);
		echo gProcessaAcoes($actions);
	break;
}			
			
?>
