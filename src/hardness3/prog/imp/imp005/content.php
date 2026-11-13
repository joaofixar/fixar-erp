<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author RubiaYwata
	* @since 16/11/2011
	*/
	case 'imp005ContentDi':
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
			array($content1, '/imp/imp005/tab/imp005TabDi/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp005ContentDiIncluir':
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
			array($content1, '/imp/imp005/tab/imp005TabDiIncluir/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$T295 = mysql_query("SELECT T295_D024_Id FROM T295 WHERE T295_Id = '{$r_acaoId}'");
		$T295 = mysql_fetch_array($T295);

		if($r_acaoId > 0){
			$botoes = array(
					array('titulo' => 'Excluir', 'id' => 'imp005ExcluirItens', 'posicao' => 'esquerda'),
					array('titulo' => 'Gerar DI', 'id' => 'imp005GerarDI', 'posicao' => 'esquerda'),
					array('titulo' => 'Calcular', 'id' => 'imp005Calcular', 'posicao' => 'esquerda'),
					array('titulo' => 'Imprimir', 'id' => 'imp005Imprimir', 'posicao' => 'esquerda'),
					array('titulo' => 'Atualizar Locação', 'id' => 'imp005AtualizarLocacao', 'posicao' => 'esquerda'),
					array('titulo' => 'Análise', 'id' => 'imp005AnalisePO', 'posicao' => 'esquerda'),
				);
		}
		$botoes[] = array('titulo' => 'Enviar', 'id' => 'imp005FormDiBtn', 'posicao' => 'direita');
		$botoes[] = array('titulo' => 'Cancelar', 'id' => 'btnFechar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita');
		
		echo Botoes::processar($botoes, 'content');

		/*
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($r_acaoId > 0){
			$botoesEsq .= '<button type="submit" id="imp005ExcluirItens">Excluir</button>';
			$botoesEsq .= '<button type="submit" id="imp005GerarDI">Gerar DI</button>';
			// $botoesEsq .= '<button type="submit" id="imp005GerarNFe">Gerar NFe</button>';
			$botoesEsq .= '<button type="submit" id="imp005Calcular">Calcular</button>';
			$botoesEsq .= '<button type="submit" id="imp005Imprimir">Imprimir</button>';
			// $botoesEsq .= '<button type="submit" id="imp005ImportarOC">Importar Itens OC</button>';
			// if($g['C031']['DIUsarBotaoEntradaEstoque'] == 'S') {
			// 	$botoesEsq .= '<button type="submit" id="imp005Finalizar">Entrada estoque</button>';
			// }
			// $botoesEsq .= '<button type="submit" id="imp005GerarXMLLI">Gerar XML LI</button>';
			// $botoesEsq .= '<button type="submit" id="imp005AtualizarCusto">Atualizar Custo</button>';
			$botoesEsq .= '<button type="submit" id="imp005AtualizarLocacao">Atualizar Locação</button>';
			// if(isset($T295['T295_Data_Atualizacao_Custo']) && $T295['T295_Data_Atualizacao_Custo'] != '0000-00-00 00:00:00'){
			// 	$botoesEsq .= ' Custo atualizado em: '.gCorrigeData($T295['T295_Data_Atualizacao_Custo']);
			// }
			$botoesEsq .= '<button type="submit" id="imp005AnalisePO">Análise</button>';
		}
		$botoesDir .= '<button type="submit" id="imp005FormDiBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		*/
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp005Imprimir').unbind('click').bind('click', function(data) {
					$.getJSON('/imp/imp005/form_func-ajax/imp005verificaAlcada/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code === true) {
							// Exemplo: abrirJanela
							$('#{$g['divId']}').showLoading();
							$.ajax({
								url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
								type: 'GET',
								dataType: 'jsonp',
								data: {
									'ajax': 'true',
									'acaoId': '837dfabf8add1e8e828dfb72817cca9f',
									'link': '/sistema/personalizacao/tela/837dfabf8add1e8e828dfb72817cca9f/?ajax=true&acaoId={$r_acaoId}',
								},
								success: function(resposta) {
									$('#{$g['divId']}').hideLoading();
									window.open(resposta.data, '_blank');
								}
							});
						} else if(request.code == 'analise'){
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise PO', '/imp/imp005/outro/imp005AnalisePO/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
						} else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/imp/imp005/form_func-ajax/imp005enviarParaAnalise/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request2) {
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
							dialogConfirm('Enviar PO para análise', request.data, \$buttons);
						}
					});
				});		
				$('#imp005GerarDI').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/imp/imp005/form_func-ajax/imp005verificaAlcada/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code === true) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar DI', '/imp/imp005/content/imp005contentGerarDI/', '&acaoId='+ encodeURIComponent("{$r_acaoId}") +'&tab=geral&D024_Id=' + encodeURIComponent("{$T295['T295_D024_Id']}"), [1219,500]);
							divRefresh('{$g['divIdParent']}', true);
						} else if(request.code == 'analise'){
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Análise PO', '/imp/imp005/outro/imp005AnalisePO/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [1000,600]);
						} else {
							var \$buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();
									$(this).dialog("destroy").remove();
									$.getJSON('/imp/imp005/form_func-ajax/imp005enviarParaAnalise/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&coleta=S&callback=?', function(request2) {
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
							dialogConfirm('Enviar PO para análise', request.data, \$buttons);
						}
					});
				});

				$('#imp005AtualizarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar a locação dos itens da importação', '/imp/imp005/content/imp005ContentAtualizarLocacao/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [500,120]);
					divRefresh('{$g['divIdParent']}', true);	
				});
				$('#imp005Calcular').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T295_Id = $('#{$g['divId']} #T295_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp005/form_func-ajax/Calcular/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
											divRefresh('{$g['divId']}', true);
											console.log('{$g['divIdParent']}');
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
				$('#imp005GerarXMLLI').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp005/form_func-ajax/GerarXMLLI/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									dialogConfirm('Download XML','XML gerado com sucesso! <a href=\"/imp/imp005/form_func-ajax/downloadXMLLI/?T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&ajax=true\">Abrir Arquivo</a>');
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
				$('#imp005Finalizar').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T295_Id = $('#{$g['divId']} #T295_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp005/form_func-ajax/imp007Finalizar/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
					dialogConfirm('ATENÇÃO!', 'Este processo só pode ser executado uma vez.<br />Atualizar estoque<br />Você tem certeza? ', \$buttons);
				});

				$('#imp005AtualizarCusto').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T295_Id = $('#{$g['divId']} #T295_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp005/form_func-ajax/imp005AtualizarCusto/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

				$('#imp005ImportarOC').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Importar Itens Ordem de Compra', '/imp/imp005/content/imp005contentImportarItensOC/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&divDI=' + encodeURIComponent('{$content1}'), [800,550]);
				});
				
				$('#imp005AnalisePO').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Análise PO', '/imp/imp005/outro/imp005AnalisePO/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&divDI=' + encodeURIComponent('{$content1}'));
				});

				$('#imp005ExcluirItens').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp005/form_func-ajax/excluirItensMarcados/?ajax=true&T295_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
			} </script></div>
EOT;
	break;

	case 'imp005ContentAtualizarLocacao':
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
			array($content1, '/imp/imp005/form/imp005FormAtualizarLocacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp005ContentProdutos':
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
			array($content1, '/imp/imp005/form/imp005FormProdutoIncluir/', '&tab=geral&T295_Id=' . urlencode($r_acaoId). "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp005/grid/imp005GridProduto/', '&acaoId=' . urlencode($r_acaoId). "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp005ContentDocumentos':
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
			array($content1, '/imp/imp005/form/imp005FormDocumentos/', "&tab=geral&T295_Id=" . urlencode($r_T295_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp005/grid/imp005GridDocumentos/', '&acaoId=' . urlencode($r_T295_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;

		case 'imp005ContentDocumentosDespesa':
			$T155_T295_Id = isset($r_T155_T295_Id) ? $r_T155_T295_Id : false;			

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
			array($content1, '/imp/imp005/form/imp005FormDocumentosDespesa/', "&tab=geral&T155_T295_Id=" . urlencode($T155_T295_Id) .  "&T168_Id=" . urlencode($r_T168_Id)."&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp005/grid/imp005GridDocumentosDespesa/', '&acaoId=' . urlencode($r_acao) .  '&T155_T295_Id=' . urlencode($T155_T295_Id) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'imp005ContentEmail':
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
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&tab=geral&T295_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	
	
	break;
	
	case 'imp005ContentDespesasIncluir':
		
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
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
			array($content1, '/imp/imp005/tab/imp005TabCadastroDespesas/', '&acaoId=' . urlencode($r_acaoId) . '&T295_Id=' . urlencode($T295_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp005ContentLIIncluir':

		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
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
			array($content1, '/imp/imp005/tab/imp005TabCadastroLI/', '&acaoId=' . urlencode($r_acaoId) . '&T295_Id=' . urlencode($T295_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp005contentIncluirContasPagar':
		
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
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
			array($content1, '/imp/imp005/tab/imp005TabCadastroContasPagar/', '&acaoId=' . urlencode($r_acaoId) . '&T295_Id=' . urlencode($T295_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp005contentIncluirContasReceber':
		
		$T295_Id = isset($r_T295_Id) ? $r_T295_Id : false;
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
			array($content1, '/imp/imp005/tab/imp005TabCadastroContasReceber/', '&acaoId=' . urlencode($r_acaoId) . '&T295_Id=' . urlencode($T295_Id))
		);
		echo gProcessaAcoes($actions);
		
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp005FormCadastroContasReceberbtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
    case 'imp005ContentPedidosProduto':
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
            array($content1, '/imp/imp005/tab/imp005TabPedidosProduto/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;	

    case 'imp005contentImportarItensOC':
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
            array($content1, '/imp/imp005/tab/imp005TabImportarItensOC/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

	case 'imp005contentImportarCSV':
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
           	array($content1, '/imp/imp005/form/imp005FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/imp/imp005/outro/imp005ManualCSVCliente/','&tab=geral')
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

	case 'imp005contentGerarDI':
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
    		array($content1, '/imp/imp005/tab/imp005TabGerarDI/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($g['divId']))
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

	case 'imp005ContentOrdemCompraGrupo':
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
			array($content1, '/imp/imp005/tab/imp005TabOrdemCompraGrupo/', '&T295_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'imp005ContentNaoLiberarTelaAnalise':
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
			array($content1, '/imp/imp005/tab/imp005TabNaoLiberarTelaAnalise/', '&T295_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;

	case 'imp005ContentPrincipalAnalise':
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
			array($content1, '/imp/imp005/tab/imp005TabPrincipalAnalise/'),
		);
		echo gProcessaAcoes($actions);
		
	break;
}
