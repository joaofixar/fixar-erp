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

		 case 'est006ContentPrincipal':
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
			array($content1, '/est/est006/tab/est006TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est006ContentIncluirOrdemCompra':
			$GerarOCEstoqueRequisicao 		  = (isset($r_GerarOCEstoqueRequisicao)) 		 ? $r_GerarOCEstoqueRequisicao 		   : false;
			$GerarOCEstoque 		  		  = (isset($r_GerarOCEstoque)) 		 			 ? $r_GerarOCEstoque 		   		   : false;
			$gerarCotacaoRequisicaoTabCotacao = (isset($r_gerarCotacaoRequisicaoTabCotacao)) ? $r_gerarCotacaoRequisicaoTabCotacao : false;
			$sql 							  = (isset($r_sql)) 							 ? $r_sql 							   : false;
			$D024_Id 						  = (isset($r_D024_Id)) 						 ? $r_D024_Id 						   : false;
			
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
			array($content1, '/est/est006/tab/est006TabIncluirOrdemCompra/', '&acaoId=' . urlencode($r_acaoId) . '&GerarOCEstoqueRequisicao=' . urlencode($GerarOCEstoqueRequisicao) . 
																												 '&gerarCotacaoRequisicaoTabCotacao=' . urlencode($gerarCotacaoRequisicaoTabCotacao) . 
																												 '&sql=' . urlencode($sql) .
																												 '&GerarOCEstoque=' . urlencode($GerarOCEstoque) .
																												 '&D024_Id=' . urlencode($D024_Id))
			);
			echo gProcessaAcoes($actions);
 		break;
 		
 		case 'est006ContentAbreEditarOrdemCompra':
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
			array($content1, '/est/est006/tab/est006TabEditarOrdemCompra/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
 		break;
 		
 		case 'est006ContentEditarOrdemCompra':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();  
			$g['smarty']->assign('content1', $content1); 
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '150px;');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est006/form/est006FormEditarOrdemCompra/', '&tab=geral&T011_Id=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est006/grid/est006GridEditarOrdemCompra/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1))
			);
			echo gProcessaAcoes($actions);
			
			/**
			 * Botões
			 */
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesEsq .= '<button type="submit" id="est006AplicarDescontoBtn">Aplicar Desconto</button>';
			$botoesEsq .= '<button type="submit" id="est006AplicarIcmsBtn">Aplicar ICMS</button>';
			$botoesEsq .= '<button type="submit" id="est006AplicarIPICadastroBtn">Aplicar IPI Cadastro</button>';
			$botoesEsq .= '<button type="submit" id="est006EnviarEmail">Enviar Email</button>';
			$botoesEsq .= '<button type="submit" id="est006Imprimir">Imprimir</button>';
			$botoesEsq .= '<button type="submit" id="est006GerarNotaFiscal">Gerar Nota Fiscal</button>';
			//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est006AplicarDescontoBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/est/est006/content/est006ContentAplicarDesconto/', '&tab=geral&T011_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#est006AplicarIcmsBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar ICMS', '/est/est006/content/est006ContentAplicarICMS/', '&tab=geral&T011_Id=' + encodeURIComponent("{$r_acaoId}"), [320,130]);
				});
				$('#est006AplicarIPICadastroBtn').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T011_Id = $('#{$g['divId']} #T011_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est006/form_func-ajax/aplicarIPICadastro/?ajax=true&T011_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}', true);
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Aplicar IPI Cadastro', 'Você tem certeza? ', \$buttons);
				});
				$('#est006GerarNotaFiscal').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T011_Id = $('#{$g['divId']} #T011_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est006/form_func-ajax/est006GerarNotaFiscal/?ajax=true&T011_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
						    		abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal: ' + request.T013_Id, '/est/est004/content/est004ContentEditarNotaFiscal/', '&acaoId=' + encodeURIComponent(request.T013_Id), [990,550]);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}', true);
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Gerar Nota Fiscal', 'Você tem certeza que deseja gerar a nota fiscal de entrada? ', \$buttons);
				});
				$('#est006EnviarEmail').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar um Email', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T011_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
				});
				$('#est006Imprimir').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est006/form_func-ajax/est006ImprimirOrdemCompra/?ajax=true&T011_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						window.open(request.data);
					});
				});
			} </script></div>
EOT;
			
 		break;
		
			
		/**
		* @author AndreySliwinski
		* @since 30/11/2011
		*/
		case 'est006ContentAplicarDesconto':
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
				array($content1, '/est/est006/form/est006FormAplicarDesconto/', '&tab=geral&T011_Id=' . urlencode($r_T011_Id).'&acaoId=' . urlencode($r_acaoId). "&divReload=" . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);	
		
		break;
		
		/**
		* @author AndreySliwinski
		* @since 30/11/2011
		*/
		case 'est006ContentAplicarICMS':
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
				array($content1, '/est/est006/form/est006FormAplicarIcms/', '&tab=geral&T011_Id=' . urlencode($r_T011_Id).'&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);	
		
		break;
		
		case 'est006ContentEmail':
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
				array($content1, '/etc/etc006/grid/etc006GridTodos/', '&tab=geral&T011_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);	
		
		break;
		
		case 'est006ContentPagamentoCotacao':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();  
			$g['smarty']->assign('content1', $content1); 
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '120px;');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est006/form/est006FormPagamento/', '&tab=geral&T011_Id=' . urlencode($r_T011_Id) . "&divReload=" . urlencode($content2)),
			array($content2, '/est/est006/grid/est006GridPagamento/', '&T011_Id=' . urlencode($r_T011_Id) . "&divReload=" . urlencode($content1))
			);
			echo gProcessaAcoes($actions);
		break;

		case 'est006AgregadosOrdemCompraTab':
			/*
			 * Divisão da Area em Conteúdos
			 */
			$content1 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('tipo', 'unico');
			// O que será carregado em cada conteúdo?
			$actions = array(
			    // identificador, url, paramentros get (opcional)
			    array($content1, '/est/est006/tab/est006TabAgregados/', '&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);
		break;

		case 'est006AgregadosOrdemCompra':
			/*
			 * Divisão da Area em Conteúdos
			 */
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height', '90px');
			$g['smarty']->assign('tipo', '2linhas');
			// O que será carregado em cada conteúdo?
			$actions = array(
			    // identificador, url, paramentros get (opcional)
			    array($content1, '/est/est006/form/est006FormAgregados/', '&tab=geral&T012_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			    array($content2, '/est/est006/grid/est006GridAgregados/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
			);
			echo gProcessaAcoes($actions);
		break;
}			
			
?>
