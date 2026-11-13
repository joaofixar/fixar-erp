<?php
namespace hardness;
/**
* Weiberlan
* 24/02/2012
*/
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

 		case 'est008ContentPrincipal':
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
			array($content1, '/est/est008/tab/est008TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
		break;
			
		case 'est008contentCadastroCotacaoCompra':
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
			array($content1, '/est/est008/tab/est008TabCadastroCotacaoCompra/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			$r_T189_Id = (isset($r_T189_Id)) ? $r_T189_Id : false;
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
					$.getJSON('/est/est008/grid_func-ajax/est008FlagDesmarcar/?ajax=true&T189_Id={$r_T189_Id}&callback=?', function(request) {
					});
				});
			} </script></div>
EOT;
			
			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			// $botoesDir .= '<button type="submit" id="est008FormCadastroCotacaoCompra_btn">Enviar</button>';
			if(!empty($r_acaoId)){
				//$botoesEsq .= '<button type="submit" id="ven001EnviarEmail">Enviar Email</button>';
				$botoesEsq .= '<button type="submit" id="est008ImprimirCotacaoCompraDemonstrativo">Imprimir demonstrativo</button>';
			}
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est008ImprimirCotacaoCompraDemonstrativo').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est008/form_func-ajax/est008ImprimirCotacaoCompraDemonstrativo/?ajax=true&T010_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						window.open(request.data);
					});
				});
				$('#ven001EnviarEmail').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar E-mail', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T010_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
				});
			} </script></div>
EOT;
			
		break;
		
		case 'est008ContentProdutos':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height','150px;');
			$g['smarty']->assign('tipo', '2linhas');
			
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est008/form/est008FormProdutos/', '&tab=geral&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est008/grid/est008GridProdutos/', '&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content1))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est008ContentFornecedores':
			/*
			* Divisão da Area em Conteúdos
			*/
			// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
			$content1 = uniqid();
			$content2 = uniqid();
			$g['smarty']->assign('content1', $content1);
			$g['smarty']->assign('content2', $content2);
			$g['smarty']->assign('content1Height','150px;');
			$g['smarty']->assign('tipo', '2linhas');
			
			// O que será carregado em cada conteúdo?
			$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/est/est008/form/est008FormFornecedores/', '&tab=geral&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content2)),
			array($content2, '/est/est008/grid/est008GridFornecedores/', '&T010_Id=' . urlencode($r_T010_Id) . '&divReload=' . urlencode($content1))
			);
			echo gProcessaAcoes($actions);
			
		break;
		
		case 'est008ContentGerarOC':
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
			array($content1, '/est/est008/tab/est008TabGerarOC/', '&T010_Id=' . urlencode($r_T010_Id))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est008ContentFornecedoresOC':
			$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
			$T059_Id = (isset($r_T059_Id)) ? $r_T059_Id : false;
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
				array($content1, '/est/est008/tab/est008TabFornecedoresOC/', '&acaoId=' . urlencode($acaoId). '&T059_Id=' . urlencode($T059_Id))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est008ContentFornecedoresOCForn':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
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
				array($content1, '/est/est008/tab/est008TabFornecedoresOCForn/', '&T010_Id=' . urlencode($T010_Id). '&T173_Id=' . urlencode($T173_Id))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est008contentCadastroCotacaoCompraNew':
			$GerarCotacaoEstoque 		  = (isset($r_GerarCotacaoEstoque)) ? $r_GerarCotacaoEstoque : false;
			$GerarCotacaoEstoqueRequsicao = (isset($r_GerarCotacaoEstoqueRequsicao)) ? $r_GerarCotacaoEstoqueRequsicao : false;
			
			$GerarCotacaoEmergencia 	  = (isset($r_GerarCotacaoEmergencia)) ? $r_GerarCotacaoEmergencia : false;
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
				array($content1, '/est/est008/tab/est008tabCadastroCotacaoCompraNew/', '&GerarCotacaoEstoque=' . urlencode($GerarCotacaoEstoque) . 
				  																	   '&GerarCotacaoEmergencia=' . urlencode($GerarCotacaoEmergencia) . 
																					   '&GerarCotacaoEstoqueRequsicao=' . urlencode($GerarCotacaoEstoqueRequsicao))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est006ContentIncluirOrdemCompra':
			$GerarOCEstoque 	= (isset($r_GerarOCEstoque)) ? $r_GerarOCEstoque : false;
			$GerarOCEmergencia  = (isset($r_GerarOCEmergencia)) ? $r_GerarOCEmergencia : false;
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
				array($content1, '/est/est008/tab/est006tabIncluirOrdemCompra/', '&$GerarOCEstoque=' . urlencode($GerarOCEstoque) . '&GerarOCEmergencia=' . urlencode($GerarOCEmergencia))
			);
			echo gProcessaAcoes($actions);
		break;
		
		case 'est008ContentCadastroCotacaoCompra':
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
			array($content1, '/est/est008/tab/est008TabCadastroCotacaoCompra/', '&acaoId=' . urlencode($r_acaoId))
			);
			echo gProcessaAcoes($actions);
			
			$r_T189_Id = (isset($r_T189_Id)) ? $r_T189_Id : false;
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
					$.getJSON('/est/est008/grid_func-ajax/est008FlagDesmarcar/?ajax=true&T189_Id={$r_T189_Id}&callback=?', function(request) {
					});
				});
			} </script></div>
EOT;
			
			/**
			 * Botões
			 */
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			// $botoesDir .= '<button type="submit" id="est008FormCadastroCotacaoCompra_btn">Enviar</button>';
			if(!empty($r_acaoId)){
				//$botoesEsq .= '<button type="submit" id="ven001EnviarEmail">Enviar Email</button>';
				//$botoesEsq .= '<button type="submit" id="est008ImprimirCotacaoCompraDemonstrativo">Imprimir demonstrativo</button>';
			}
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est008ImprimirCotacaoCompraDemonstrativo').bind('click', function(data) {
					$.getJSON('/est/est008/form_func-ajax/est008ImprimirCotacaoCompraDemonstrativo/?ajax=true&T010_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						window.open(request.data);
					});
				});
				$('#ven001EnviarEmail').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Enviar E-mail', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T010_Id=' + encodeURIComponent("{$r_acaoId}"), [900,550]);
				});
			} </script></div>
EOT;
		break;
		
		case 'est008ContentInserirMarca':
			$T059_Id      = (isset($r_T059_Id)) ? $r_T059_Id : false;
			$T010_Id 	  = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id 	  = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$T174_D009_Id = (isset($r_T174_D009_Id)) ? $r_T174_D009_Id : false;
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
				array($content1, '/est/est008/tab/est008tabAlterarMarca/', '&tab=geral&T059_Id=' . urlencode($T059_Id) 
																					 . '&T010_Id=' . urlencode($T010_Id) 
																					 . '&acao_Id=' . urlencode($T010_Id) 
																					 . '&T173_Id=' . urlencode($T173_Id)
																					 . '&T174_D009_Id=' . urlencode($T174_D009_Id)
																					 )
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentResumoCotacao':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
			$T174_Id = (isset($r_T174_Id)) ? $r_T174_Id : false;
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
				array($content1, '/est/est008/tab/est008tabResumoCotacao/', '&tab=geral&T010_Id=' . urlencode($T010_Id) 
																					 . '&T173_Id=' . urlencode($T173_Id) 
																					 . '&D024_Id=' . urlencode($D024_Id) 
																					 . '&T174_Id=' . urlencode($T174_Id)
																					 )
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentResumo':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
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
				array($content1, '/est/est008/tab/est008TabResumo/', '&T010_Id=' . urlencode($T010_Id) . '&D009_Id=' . urlencode($D009_Id))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentRetornarProdForn':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
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
				array($content1, '/est/est008/tab/est008TabRetornarProdForn/', '&T010_Id=' . urlencode($T010_Id) . '&T173_Id=' . urlencode($T173_Id))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentIncluirProdForn':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$div = (isset($r_div)) ? $r_div : false;
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
				array($content1, '/est/est008/tab/est008TabIncluirProdForn/', '&T010_Id=' . urlencode($T010_Id) . '&T173_Id=' . urlencode($T173_Id) . '&div=' . urlencode($div))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentPreCadastrarProduto':
			$T059_Id = (isset($r_T059_Id)) ? $r_T059_Id : false;
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$abrirEditar = (isset($r_abrirEditar)) ? $r_abrirEditar : false;
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
				array($content1, '/est/est008/tab/est008TabPreCadProd/', '&T059_Id=' . urlencode($T059_Id) . '&T010_Id=' . urlencode($T010_Id) . '&abrirEditar='.urlencode($abrirEditar))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentSelecionarEmpresa':
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
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
				array($content1, '/est/est008/tab/est008TabSelecionarEmpresa/', '&T173_Id=' . urlencode($T173_Id) . '&T010_Id=' . urlencode($T010_Id))
			);
			echo gProcessaAcoes($actions);

		break;
		
		case 'est008ContentEditarProdutoFornecedor':
			$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
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
				array($content1, '/est/est008/tab/est008TabEditarProdutoFornecedor/', '&acaoId=' . urlencode($acaoId) . '&T010_Id=' . urlencode($T010_Id) . '&T173_Id='.urlencode($T173_Id))
			);
			echo gProcessaAcoes($actions);
		break;
}			

?>
