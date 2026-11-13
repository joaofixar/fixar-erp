<?php
namespace hardness;
	
switch ($g['r'][3]) {
	/**
	* @author RubiaYwata
	* @since 16/11/2011
	*/
	case 'imp004ContentDi':
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
			array($content1, '/imp/imp004/tab/imp004TabDi/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp004ContentDiIncluir':
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
			array($content1, '/imp/imp004/tab/imp004TabDiIncluir/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

		$T293 = mysql_query("SELECT T293_D024_Id FROM T293 WHERE T293_Id = '{$r_acaoId}'");
		$mT293 = mysql_fetch_array($T293);

		if($r_acaoId > 0){
			$botoes = array(
					array('titulo' => 'Excluir', 'id' => 'imp004ExcluirItens', 'posicao' => 'esquerda'),
					array('titulo' => 'Gerar PO', 'id' => 'imp004GerarPO', 'posicao' => 'esquerda'),
					array('titulo' => 'Calcular', 'id' => 'imp004Calcular', 'posicao' => 'esquerda'),
					array('titulo' => 'Imprimir', 'id' => 'imp004Imprimir', 'posicao' => 'esquerda'),
					array('titulo' => 'Atualizar Locação', 'id' => 'imp004AtualizarLocacao', 'posicao' => 'esquerda'),
					array('titulo' => 'Análise', 'id' => 'imp004AnaliseQuotation', 'posicao' => 'esquerda'),
				);
		}
		$botoes[] = array('titulo' => 'Enviar', 'id' => 'imp004FormDiBtn', 'posicao' => 'direita');
		$botoes[] = array('titulo' => 'Cancelar', 'id' => 'btnFechar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita');

		echo Botoes::processar($botoes, 'content');

		/*$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		if($r_acaoId > 0){
			$botoesEsq .= '<button type="submit" id="imp004ExcluirItens">Excluir</button>';
			$botoesEsq .= '<button type="submit" id="imp004GerarPO">Gerar PO</button>';
			//$botoesEsq .= '<button type="submit" id="imp004GerarNFe">Gerar NFe</button>';
			$botoesEsq .= '<button type="submit" id="imp004Calcular">Calcular</button>';
			$botoesEsq .= '<button type="submit" id="imp004Imprimir">Imprimir</button>';
			//$botoesEsq .= '<button type="submit" id="imp004ImportarOC">Importar Itens OC</button>';
			// if($g['C031']['DIUsarBotaoEntradaEstoque'] == 'S') {
			// 	$botoesEsq .= '<button type="submit" id="imp004Finalizar">Entrada estoque</button>';
			// }
			// $botoesEsq .= '<button type="submit" id="imp004GerarXMLLI">Gerar XML LI</button>';
			// $botoesEsq .= '<button type="submit" id="imp004AtualizarCusto">Atualizar Custo*</button>';
			$botoesEsq .= '<button type="submit" id="imp004AtualizarLocacao">Atualizar Locação</button>';
			$botoesEsq .= '<button type="submit" id="imp004AnaliseQuotation">Análise</button>';
			// if(isset($T293['T293_Data_Atualizacao_Custo']) && $T293['T293_Data_Atualizacao_Custo'] != '0000-00-00 00:00:00'){
			// 	$botoesEsq .= ' Custo atualizado em: '.gCorrigeData($T293['T293_Data_Atualizacao_Custo']);
			// }
		}
		$botoesDir .= '<button type="submit" id="imp004FormDiBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');*/
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#imp004Imprimir').unbind('click').bind('click', function(data) {
					// Exemplo: abrirJanela
					$('#{$g['divId']}').showLoading();
					$.ajax({
						url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
						type: 'GET',
						dataType: 'jsonp',
						data: {
							'ajax': 'true',
							'acaoId': '9da6468cc29f3b4f47d95d743fc68e8e',
							'link': '/sistema/personalizacao/tela/9da6468cc29f3b4f47d95d743fc68e8e/?ajax=true&acaoId={$r_acaoId}',
						},
						success: function(resposta) {
							$('#{$g['divId']}').hideLoading();
							window.open(resposta.data, '_blank');
						}
					});
				});
				$('#imp004GerarPO').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar PO', '/imp/imp004/content/imp004contentGerarPO/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&D024_Id=' + encodeURIComponent("{$mT293['T293_D024_Id']}"), [1219,500]);
					divRefresh('{$g['divIdParent']}', true);	
				});
				
				$('#imp004AtualizarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar a locação dos itens da importação', '/imp/imp004/content/imp004ContentAtualizarLocacao/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [500,120]);
					divRefresh('{$g['divIdParent']}', true);	
				});
				$('#imp004Calcular').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T293_Id = $('#{$g['divId']} #T293_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/form_func-ajax/Calcular/?ajax=true&T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { 
										"Fechar": function() { $(this).dialog("destroy").remove(); 
											$(this).dialog("destroy").remove();
										 	divRefresh('{$g['divId']}', true);
											console.log('{$g['divId']}');
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
				$('#imp004GerarXMLLI').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/form_func-ajax/GerarXMLLI/?ajax=true&T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									dialogConfirm('Download XML','XML gerado com sucesso! <a href=\"/imp/imp004/form_func-ajax/downloadXMLLI/?T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&ajax=true\">Abrir Arquivo</a>');
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
				$('#imp004Finalizar').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T293_Id = $('#{$g['divId']} #T293_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/form_func-ajax/imp007Finalizar/?ajax=true&T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

				$('#imp004AtualizarCusto').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T293_Id = $('#{$g['divId']} #T293_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/form_func-ajax/imp004AtualizarCusto/?ajax=true&T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

				$('#imp004ImportarOC').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Importar Itens Ordem de Compra', '/imp/imp004/content/imp004contentImportarItensOC/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&divDI=' + encodeURIComponent('{$content1}'), [800,550]);
				});

				$('#imp004AnaliseQuotation').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Análise Quotation', '/imp/imp004/outro/imp004AnaliseQuotation/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&divDI=' + encodeURIComponent('{$content1}'));
				});

				$('#imp004ExcluirItens').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/imp/imp004/form_func-ajax/excluirItensMarcados/?ajax=true&T293_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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

	case 'imp004ContentAtualizarLocacao':
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
			array($content1, '/imp/imp004/form/imp004FormAtualizarLocacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'imp004ContentProdutos':
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
			array($content1, '/imp/imp004/form/imp004FormProdutoIncluir/', '&tab=geral&T293_Id=' . urlencode($r_acaoId). "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp004/grid/imp004GridProduto/', '&acaoId=' . urlencode($r_acaoId). "&divReload=" . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp004ContentDocumentos':
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
			array($content1, '/imp/imp004/form/imp004FormDocumentos/', "&tab=geral&T293_Id=" . urlencode($r_T293_Id) .  "&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp004/grid/imp004GridDocumentos/', '&acaoId=' . urlencode($r_T293_Id) .  "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;

		case 'imp004ContentDocumentosDespesa':
			$T155_T293_Id = isset($r_T155_T293_Id) ? $r_T155_T293_Id : false;
			

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
			array($content1, '/imp/imp004/form/imp004FormDocumentosDespesa/', "&tab=geral&T155_T293_Id=" . urlencode($T155_T293_Id) .  "&T168_Id=" . urlencode($r_T168_Id)."&divReload=" . urlencode($content2)),
			array($content2, '/imp/imp004/grid/imp004GridDocumentosDespesa/', '&acaoId=' . urlencode($r_acao) .  '&T155_T293_Id=' . urlencode($T155_T293_Id) . "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'imp004ContentEmail':
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
			array($content1, '/etc/etc006/grid/etc006GridTodos/', '&tab=geral&T293_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	
	
	break;
	
	case 'imp004ContentDespesasIncluir':
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;

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
			array($content1, '/imp/imp004/tab/imp004TabCadastroDespesas/', '&acaoId=' . urlencode($r_acaoId) . '&T293_Id=' . urlencode($T293_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp004ContentLIIncluir':

		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
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
			array($content1, '/imp/imp004/tab/imp004TabCadastroLI/', '&acaoId=' . urlencode($r_acaoId) . '&T293_Id=' . urlencode($T293_Id))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'imp004contentIncluirContasPagar':
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
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
			array($content1, '/imp/imp004/tab/imp004TabCadastroContasPagar/', '&acaoId=' . urlencode($r_acaoId) . '&T293_Id=' . urlencode($T293_Id))
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'imp004contentIncluirContasReceber':
		
		$T293_Id = isset($r_T293_Id) ? $r_T293_Id : false;
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
			array($content1, '/imp/imp004/tab/imp004TabCadastroContasReceber/', '&acaoId=' . urlencode($r_acaoId) . '&T293_Id=' . urlencode($T293_Id))
		);
		echo gProcessaAcoes($actions);
		
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="imp004FormCadastroContasReceberbtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
    case 'imp004ContentPedidosProduto':
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
            array($content1, '/imp/imp004/tab/imp004TabPedidosProduto/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;	

    case 'imp004contentImportarItensOC':
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
            array($content1, '/imp/imp004/tab/imp004TabImportarItensOC/', '&acaoId=' . urlencode($r_acaoId))
        );
        echo gProcessaAcoes($actions);

    break;

	case 'imp004contentImportarCSV':
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
           	array($content1, '/imp/imp004/form/imp004FormImportarCSV/',"&tab=geral&acaoId=" . urlencode($r_acaoId)),
		    array($content2, '/imp/imp004/outro/imp004ManualCSVCliente/','&tab=geral')
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

	case 'imp004contentGerarPO':
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
    		array($content1, '/imp/imp004/tab/imp004TabGerarPO/', '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($g['divId']))
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

	case 'imp004ContentCotacoesGrupo':
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
			array($content1, '/imp/imp004/tab/imp004TabCotacoesGrupo/', '&T293_Id=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
	break;
}
