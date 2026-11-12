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
	case 'est014ContentRequisicoesdeCompraGeral':
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
			array($content1, '/est/est014/tab/est014TabRequisicoesdeCompraGeral/'),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est014ContentRequisicoesdeCompra':
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
			array($content1, '/est/est014/tab/est014TabRequisicoesdeCompra/'),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est014ContentRequisicaoCompraEditarNovo':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
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
			array($content1, '/est/est014/tab/est014TabRequisicoesdeCompraEditarIncluir/', '&acaoId=' . urlencode($acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est014ContentAssociarProduto':
		$T189_Id = (isset($r_T189_Id)) ? $r_T189_Id : false;
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
			array($content1, '/est/est014/tab/est014TabAssociarProduto/', '&T189_Id=' . urlencode($T189_Id)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est014contentGerarOC':
		$extra = (isset($r_extra)) ? $r_extra : false;
		//$extra = base64_decode($extra);
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
			array($content1, '/est/est014/tab/est014TabGerarOC/', '&extra=' . urlencode($extra)),
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'est014RequisicaoCompra':
		$extra = (isset($r_extra)) ? $r_extra : false;
		//$extra = base64_decode($extra);
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
			array($content1, '/est/est014/tab/est014RequisicaoCompra/', '&extra=' . urlencode($extra)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est014contentOrdemServico':
		$T128_Id = (isset($r_T128_Id)) ? $r_T128_Id : false;
		//$extra = base64_decode($extra);
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
			array($content1, '/est/est014/tab/est014TabOrdemServico/', '&T128_Id=' . urlencode($T128_Id)),
		);
		echo gProcessaAcoes($actions);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est014OSProcessarEstoque">Processar Estqoue</button>';
		$botoesEsq .= '<button type="submit" id="est014OSImprimirSeparacao">Imprimir Separação</button>';
		$botoesEsq .= '<button type="submit" id="est014OSImprimirOSFornecedor">Imprimir OS Fornecedor</button>';
		$botoesEsq .= '<button type="submit" id="est014OSGerarNF">Gerar NF</button>';
		$botoesEsq .= '<button type="submit" id="est014OSGerarACP">Gerar ACP</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

			$('#est014OSProcessarEstoque').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						//var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
						//var Desconto = $('#{$g['divId']} #Desconto').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est014/form_func-ajax/est014OSProcessarEstoque/?ajax=true&T128_Id=' + encodeURIComponent('{$T128_Id}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    				dialogConfirm('Ok', request.data, \$buttons);
			    				divRefresh('{$g['divIdParent']}');
			    				//fecharJanela('{$g['divId']}');
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
				dialogConfirm('Processar Estoque', 'Você tem certeza que deseja processar o estoque? ', \$buttons);
			});

			$('#est014OSImprimirSeparacao').unbind('click').bind('click', function(data) {
				nova_janela('../../hardness/os_estoque/imprimir_pedido_almoxarifado.php?T128_Id={$T128_Id}',0,0,900,750,'no','no','no','no','yes','yes',0);
			});

			$('#est014OSImprimirOSFornecedor').unbind('click').bind('click', function(data) {
				nova_janela('../../hardness/os_estoque/imprimir_os.php?T128_Id={$T128_Id}',0,0,900,750,'no','no','no','no','yes','yes',0);
			});

			$('#est014OSGerarNF').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
						var Desconto = $('#{$g['divId']} #Desconto').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est014/form_func-ajax/est014OSGerarNF/?ajax=true&T128_Id=' + encodeURIComponent('{$T128_Id}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    				dialogConfirm('Ok', request.data, \$buttons);
			    				divRefresh('{$g['divIdParent']}');
			    				//fecharJanela('{$g['divId']}');
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
				dialogConfirm('Processar Estoque', 'Você tem certeza que deseja gerar a Nota Fiscal? ', \$buttons);
			});

			$('#est014OSGerarACP').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
						var Desconto = $('#{$g['divId']} #Desconto').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est014/form_func-ajax/est014OSGerarACP/?ajax=true&T128_Id=' + encodeURIComponent('{$T128_Id}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
			    				dialogConfirm('Ok', request.data, \$buttons);
			    				divRefresh('{$g['divIdParent']}');
			    				//fecharJanela('{$g['divId']}');
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
				dialogConfirm('Processar Estoque', 'Você tem certeza que deseja gerar a ACP? ', \$buttons);
			});

		} </script></div>
EOT;

	break;

	case 'est014FormOrdemServico':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height','120px;');
		$g['smarty']->assign('tipo', '2linhas');
		
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/est/est014/form/est014FormProdutosOrdemServico/', '&tab=geral&T128_Id=' . urlencode($r_T128_Id) . '&divReload=' . urlencode($content2)),
		array($content2, '/est/est014/grid/est014GridProdutosOrdemServico/', '&T128_Id=' . urlencode($r_T128_Id) . '&divReload=' . urlencode($content1))
		//array($content2, '/est/est008/grid/est008GridProdutos/', '&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content1))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est014ContentIncluirOS':
		$extra = (isset($r_extra)) ? $r_extra : false;
		//$extra = base64_decode($extra);
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
			array($content1, '/est/est014/tab/est014IncluirOS/', '&extra=' . urlencode($extra)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'est014ContentOrdemServicoGeral':
		$extra = (isset($r_extra)) ? $r_extra : false;
		//$extra = base64_decode($extra);
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
			array($content1, '/est/est014/tab/est014TabOrdemServicoPrincipal/', '&extra=' . urlencode($extra)),
		);
		echo gProcessaAcoes($actions);
	break;
}
?>
