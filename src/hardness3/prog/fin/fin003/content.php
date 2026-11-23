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

	case 'fin003ContentComposicaoRetorno':
		require_once 'bibliotecas/classes/ComposicaoRetornoExtrato.php';
		$consultaRetorno = ComposicaoRetornoExtrato::consultar($r_acaoId, $g['empresaAtual']);
		$grupoRetorno = ComposicaoRetornoExtrato::localizar($consultaRetorno,
			isset($r_T216_Id) ? $r_T216_Id : 0, isset($r_T037_Id) ? $r_T037_Id : 0,
			isset($r_assinatura) ? $r_assinatura : '');
		if (!$grupoRetorno) {
			echo '<div class="ui-state-error">A composição não foi confirmada ou mudou. Atualize o extrato e abra o link novamente.</div>';
			break;
		}
		$escapar = function ($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
		$quantidadeTitulos = count($grupoRetorno['membros']);
		echo '<div class="fin003-composicao-retorno"><style>'
			. '.fin003-composicao-retorno{padding:12px}.fin003-composicao-retorno p{margin:8px 0}'
			. '.fin003-composicao-retorno .tabela-scroll{max-height:410px;overflow:auto}'
			. '.fin003-composicao-retorno table{width:100%;border-collapse:collapse}'
			. '.fin003-composicao-retorno th,.fin003-composicao-retorno td{padding:8px;border:1px solid #d6dce2;text-align:left}'
			. '.fin003-composicao-retorno th{background:#e9eef4;position:sticky;top:0}'
			. '.fin003-composicao-retorno .valor{text-align:right;white-space:nowrap}'
			. '</style><p><strong>Retorno ' . (int) $grupoRetorno['retorno_id'] . '</strong> · Arquivo: '
			. $escapar($grupoRetorno['arquivo']) . '<br>Conta: ' . $escapar($consultaRetorno['extrato']['conta_bancaria'])
			. '<br>Crédito: ' . $escapar(ComposicaoRetornoExtrato::formatarData($grupoRetorno['movimento']['data_movimento']))
			. ' · <strong>R$ ' . $escapar(ComposicaoRetornoExtrato::formatarCentavos($grupoRetorno['total'])) . '</strong></p>'
			. '<p>' . $quantidadeTitulos . ($quantidadeTitulos === 1 ? ' título nesta composição.' : ' títulos nesta composição.') . '</p>'
			. '<div class="tabela-scroll"><table><thead><tr><th>Título</th><th>Empresa</th><th>Cliente</th>'
			. '<th>Pagamento</th><th>Crédito</th><th>Recebido</th></tr></thead><tbody>';
		foreach ($grupoRetorno['membros'] as $tituloRetorno) {
			echo '<tr><td>' . $escapar($tituloRetorno['duplicata']) . '<br><small>ID ' . (int) $tituloRetorno['titulo_id']
				. '</small></td><td>' . $escapar($tituloRetorno['empresa_titulo']) . '</td><td>' . $escapar($tituloRetorno['cliente'])
				. '</td><td>' . $escapar(ComposicaoRetornoExtrato::formatarData($tituloRetorno['pagamento_no_retorno']))
				. '</td><td>' . $escapar(ComposicaoRetornoExtrato::formatarData($tituloRetorno['credito_no_retorno']))
				. '</td><td class="valor">' . $escapar(ComposicaoRetornoExtrato::formatarCentavos(ComposicaoRetornoExtrato::centavos($tituloRetorno['recebido_no_retorno']))) . '</td></tr>';
		}
		echo '</tbody><tfoot><tr><td colspan="5"><strong>Total desta composição</strong></td><td class="valor"><strong>'
			. $escapar(ComposicaoRetornoExtrato::formatarCentavos($grupoRetorno['total'])) . '</strong></td></tr></tfoot></table></div>'
			. '<p>Consulta dos valores recebidos informados no retorno importado. Não efetua baixas ou processamento do extrato.</p></div>';
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
