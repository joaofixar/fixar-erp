<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'fin003Content02':
	
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/tab/fin003TabLancamento/', '&acaoId=' . urlencode($r_acaoId))
		);
		
		echo gProcessaAcoes($actions);
	
	break;
	
	case 'fin003ContentGeral':

   		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '175px');
		$g['smarty']->assign('tipo', '2linhas');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin003/grid/fin003GridBancos/', '&divReload=' . urlencode($content2)),
			array($content2, '/fin/fin003/tab/fin003TabContent2/', '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	
	break;
	
	case 'fin003ContentEditaLancamento':
	
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/tab/fin003TabEditaLancamento/', '&acaoId=' . urlencode($r_acaoId) . '&D007_Id=' . urlencode($r_D007_Id))
		);
		
		echo gProcessaAcoes($actions);
	
	break;
	
	case 'fin003ContentIncluirLancamento':
	
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/form/fin003FormLancamentos/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D007_Id=' . urlencode($r_D007_Id))
		);
		
		echo gProcessaAcoes($actions);
	
	break;

	case 'fin003ContentExtratos':
   		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '30px');
		$g['smarty']->assign('tipo', '2linhas');		
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/fin/fin003/form/fin003FormImportarExtrato/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2)),
			array($content2, '/fin/fin003/grid/fin003GridExtratos/', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content1)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'fin003ContentTransacoes':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/tab/fin003TabTransacoes/', '&acaoId=' . urlencode($r_acaoId))
		);
		
		echo gProcessaAcoes($actions);

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="aplicarRegrasExtrato">Aplicar Regras</button>';
		$botoesDir .= '<button type="submit" id="processarExtrato">Processar extrato</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined'){
				$('#processarExtrato').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin003/form_func-ajax/processarExtrato/?ajax=true&T215_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									dialogConfirm('Ok', request.data);
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja processar os registros marcados?</span>', \$buttons);
				});

				$('#aplicarRegrasExtrato').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/fin/fin003/form_func-ajax/aplicarRegrasExtrato/?ajax=true&T215_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									dialogConfirm('Ok', request.data);
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja aplicar as regras no extrato?</span>', \$buttons);
				});
			} </script></div>
EOT;
	break;

	case 'fin003ContentIncluirTransferencia':
	
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/form/fin003FormIncluirTransferencia/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D007_Id=' . urlencode($r_D007_Id))
		);
		
		echo gProcessaAcoes($actions);
	
	break;

	case 'fin003ContentDesmembrarTransacao':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/tab/fin003TabDesmembrarTransacao/', '&tab=geral&T216_Id=' . urlencode($r_T216_Id) . '&divIdRefresh=' . urlencode($r_divIdRefresh))
		);
		
		echo gProcessaAcoes($actions);
	break;

	case 'fin003ContentEditarTransacao':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/form/fin003FormEditarTransacao/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divIdRefresh=' . urlencode($r_divIdRefresh))
		);

		echo gProcessaAcoes($actions);
	break;

	case 'fin003ContentEditarRegra':
		/*
		* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content = uniqid();
		$g['smarty']->assign('content1', $content);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content, '/fin/fin003/form/fin003FormEditarRegraExtrato/', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divIdRefresh=' . urlencode($r_divIdRefresh))
		);

		echo gProcessaAcoes($actions);
	break;

}
