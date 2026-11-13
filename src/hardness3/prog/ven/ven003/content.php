<?php
namespace hardness;

switch ($g['r'][3]) {

    case 'ven003ContentListaNFDebitoCredito':
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        $actions = array(
            array($content1, '/ven/ven003/grid/ven003GridListaNFDebitoCredito/', '&tab=geral'),
        );
        echo gProcessaAcoes($actions);
    break;

	case 'ven003ContentPrincipal':
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
		array($content1, '/ven/ven003/tab/ven003TabPrincipal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;
		
	case 'ven003ContentImportarXMLTXT':
		/* Divisão da Area em Conteúdos
		*/
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/ven/ven003/tab/ven003TabTXTXML/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);

	break;

	case 'ven003ContentImportarXMLAtualizar':
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
			array($content1, '/ven/ven003/form/ven003FormImportarXMLAtualizar/', '&tab=geral&divIdAberto=' . urlencode($r_divIdAberto)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentTecnico':
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
		array($content1, '/ven/ven003/form/ven003FormTecnico/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		array($content2, '/ven/ven003/grid/ven003GridTecnico/', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content2)),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'ven003ContentVarsLayoutDanfe':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content1Height', '100px;');
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($content1, '/ven/ven003/form/ven003VarsLayout/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		array($content2, '/ven/ven003/outro/ven003ManualVarsLayout/','&tab=geral'),
		);
		echo gProcessaAcoes($actions);

	break;
	
	case 'ven003ContentProdutosNF':
		/*
		 * Divisão da Area em Conteúdos
		 */
		$content1 = uniqid();
//		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
//		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('tipo', 'unico');
		//$g['smarty']->assign('tipo', '2linhas');
		$actions = array(
			//array($content1, '/ven/ven003/form/ven003FormProdutos/', "&tab=produto&T007_Id=" . urlencode($r_T007_Id) ."&divReload=" . urlencode($content2)),
			array($content1, '/ven/ven003/grid/ven003GridProdutos/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);

	break;
		
	/**
	* @author AndreySliwinski
	* @since 23/11/2011
	*/
	case 'ven003contentPagamentos':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$content2 = uniqid();
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '160px');


		
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven003/form/ven003FormPagamentos/', '&tab=geral&T007_Id=' . urlencode($r_acaoId). '&divReload=' . urlencode($content2).'&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/ven/ven003/grid/ven003GridPagamentos/', '&divReload=' . urlencode($content1).'&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);	    
	
	break;
	
	case 'ven003contentIncluirNotaFiscal':
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
			array($content1, '/ven/ven003/tab/ven003TabIncluirNotaFiscal/', '&tab=geral&T007_Id=' . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($g['divId'])),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		if ($r_acaoId){
			$sql="select * from T007 where T007_Id={$r_acaoId}";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);

			if($r_telaPDV == 'S' && $g['C031']['alterarDocumentoParaNFEAoAbrirCompleta'] == 'S' && empty($row['T007_Numero_Nota_Fiscal'])){
				// Se abrir a NF completa através da tela do PDV verifica se está para alterar o tipo do documento para NFe
				mysql_query("UPDATE T007 SET T007_Flag_ACP = '6' WHERE T007_Id = '{$r_acaoId}'");
			}

			$botaoAtualizar = true;
			$botaoDesconto  = true;
			$T127 = mysql_query("SELECT T127_Retorno_Sefaz 
                                   FROM T127 
                                  WHERE T127_Numero_Nota_Fiscal='{$row['T007_Numero_Nota_Fiscal']}' 
                                    AND T127_C004_Id='{$g['empresaAtual']}' 
                                    AND T127_Modelo=IF('{$row['T007_Flag_ACP']}' = '6', '55', '65') 
                                    AND (T127_Codigo_Retorno = '539' OR T127_Codigo_Retorno = '204' OR T127_Codigo_Retorno = '635')
                                  LIMIT 1");

            if(mysql_num_rows($T127) > 0){
				$botaoAtualizar = false;
				$botaoDesconto = false;
            }
		}
		// Definição dos botões
		$botoes = array();
		if(empty($row['T007_Numero_Protocolo_Nfe'])){
			if($row['T007_Flag_Travar_Nota'] == 'N'){
				$botoes[] = array('titulo' => 'Finalizar', 'id' => 'ven003FinalizarNFE');
				if($g['C031']['botaoAplicarDesconto'] == 'S' && $botaoDesconto){
					$botoes[] = array('titulo' => 'Desconto', 'id' => 'ven003AplicarDesconto');
				}
			}
			$botoes[] = array('titulo' => 'Pré-visualizar', 'id' => 'ven003PrevisualizarDANFE');
			if($row['T007_Flag_Travar_Nota'] == 'N'){
				//$botoesEsq .= '<button type="submit" id="ven003AtualizarAliquotas">Atualizar</button>';
				if($botaoAtualizar){
					$botoes[] = array('titulo' => 'Atualizar', 'id' => 'ven003AtualizarOpcoes');
				}
				$botoes[] = array('titulo' => 'Excluir', 'id' => 'ven003ExcluirNFE');
			} else if(isset($g['c029Ids'][214])){
				$botoes[] = array('titulo' => 'Habilitar edição', 'id' => 'ven003HabilitarEdicaoNota');
			}
		}else{
			$botoes[] = array('titulo' => 'Danfe', 'id' => 'ven003VisualizarDANFE');
			if ((int) $row['T007_Flag_ACP'] === 6
				&& $row['T007_Flag_Cancelada'] !== 'S'
				&& $row['T007_Flag_Inutilizado'] !== 'S'
				&& $row['T007_Flag_Denegado'] !== 'S'
				&& strlen($row['T007_Chave_Acesso_Nfe']) === 44) {
				$botoes[] = array('titulo' => 'NF Débito/Crédito', 'id' => 'ven003NFDebitoCreditoAutorizada');
			}
			//$botoesEsq .= '<button type="submit" id="ven003enviarCCe">CC-e</button>';
		}
		//$botoesEsq .= '<button type="submit" id="ven003abrirTelaAntigo">Versão antiga</button>';
		$botoes[] = array('titulo' => 'Gerar nota espelho', 'id' => 'ven003NotaTransporte');
		if($row['T007_Flag_ACP'] == '3'){
			$botoes[] = array('titulo' => 'Alterar NFe', 'id' => 'ven003AlterarModeloNota');
		}
		if($row['T007_Flag_ACP'] == '4' && $g['nivelAlcada'] >= '4' && !empty($row['T007_Nome_Arquivo_PDV'])){
			$botoes[] = array('titulo' => 'Reimportar cupom', 'id' => 'ven003ReimportarCupomFiscal');
		}
		if($g['C031']['exibirBotaoTransmitirNota'] == 'S' && ($row['T007_Flag_ACP'] == '6' || $row['T007_Flag_ACP'] == '8')){
			$tipoDocumento = ($row['T007_Flag_ACP'] == '6') ? 'NF-e' : 'NFC-e';
			$habilitaBtnTransmitir = ($row['T007_TX_Flag_Finalizada'] != '1' && empty($row['T007_Protocolo_Inutilizacao']) && $row['T007_Flag_Almoxarifado'] == 'S' && !empty($row['T007_Numero_Nota_Fiscal'])) ? false : true;
			$botoes[] = array('titulo' => "Transmitir {$tipoDocumento}", 'id' => 'ven003TransmitirNota', 'desabilitar' => $habilitaBtnTransmitir);
		}

		if($row['T007_Flag_ACP'] == '4' && $row['T007_Flag_Cancelada'] == 'S'){
			$botoes[] = array('titulo' => 'Recuperar cupom', 'id' => 'ven003RecuperarCupomFiscal');
		}

		$botoes[] = array('titulo' => "Enviar", 'id' => 'ven003FormPrincipal_btn', 'posicao' => 'direita');
		$botoes[] = array('titulo' => 'Cancelar', 'tipo' => 'codigojs', 'callback' => "function callback($array) { return 'fecharJanela(\'".$g['divId']."\');'; }", 'posicao' => 'direita');

		echo Botoes::processar($botoes, 'content');

		// Parâmetros necessários para abrir o DANFE
		$Query = md5($row['T007_D024_Id'].'|'.$r_acaoId);
		$valorNaDanfe = $g['C031']['exibirValorNFDANFE'];

		//Ações dos Botões
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven003NFDebitoCreditoAutorizada').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar NF de Débito ou Crédito', '/ven/ven003/content/ven003ContentNFDebitoCreditoAutorizada/', '&acaoId=' + encodeURIComponent("{$r_acaoId}"), [1100,650]);
				});
				$('#ven003AplicarDesconto').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Aplicar Desconto', '/ven/ven001/content/ven001ContentAplicarDesconto/', '&tab=geral&idTabelaCabecalho=' + encodeURIComponent("{$r_acaoId}") + '&tabelaCabecalho=T007&tabelaProduto=T008', [600,200]);
				});

				$('#ven003TransmitirNota').bind('click', function(data) {
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven003/content/ven003ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,300]);
				});

				$('#ven003ReimportarCupomFiscal').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/reimportarCupomFiscal/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', 'Cupom fiscal reimportado com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Reimportar cupom fiscal', 'Atenção, os dados do cupom fiscal serão atualizados com os valores<br> do arquivo do cupom, você tem certeza? ', \$buttons);
				});

				$('#ven003RecuperarCupomFiscal').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/recuperarCupomFiscal/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', 'Cupom fiscal recuperado com sucesso!', \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Recuperar cupom fiscal', 'Recuperar cupom fiscal ? ', \$buttons);
				});

				$('#ven003HabilitarEdicaoNota').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/habilitarEdicaoNota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									$('#{$g['divId']}').parent().parent().find('.dialog-refresh').trigger('click');
									divRefresh('{$g['divIdParent']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								divRefresh('{$g['divId']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Habilitar edição', 'Deseja habilitar a edição da nota?', \$buttons);
				});

				$('#ven003AtualizarAliquotas').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/atualizarAliquota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divIdParent']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
				});
				
				$('#ven003FinalizarNFE').unbind('click').bind('click', function(data) {
					//var FlagACP = $('#{$g['divId']} .ui-tabs-panel:eq(0) .itemForm-T007_Flag_ACP').val();
                    //var NumeroNota = $('#{$g['divId']} .ui-tabs-panel:eq(0) .itemForm-T007_Numero_Nota_Fiscal').val();
                    //if((FlagACP == 7 || FlagACP == 5) && NumeroNota == ''){
                        //var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                        //dialogConfirm('Erro', 'É necessário preencher o campo "Numero NF".', \$buttons);
                    //}else{
                        var \$buttons = {
						    "Sim": function() {
							    //var T005_Id = $('#{$g['divId']} #T005_Id').val();
							    idProgressBar = dialogAlert('Finalizando Nota Fiscal',"<span id='progressBar'><b><br>0% Validando Nota</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

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

									    				$.getJSON('/ven/ven003/form_func-ajax/finalizarNotaFiscalContinuarLiberacao/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
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
															    				dialogConfirm('Nota fiscal finalizada com sucesso!','<br><b><font color="green">100% Nota validada &#10004;</font></b></br>' + '<b><font color="green">100% Produtos da nota totalizados &#10004;</font></b></br>' + '<b><font color="green">100% Nota fiscal finalizada &#10004;</font></b></br><b><font color="green">100% Histórico reprocessado &#10004;</font></b></br>');

															    				if(request.transmitir){
																					divRefresh('{$g['divIdParent']}', true);
																					if(request.transmitirDireto){
																						abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven003/content/ven003ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,320]);
																					} else {
																			    		var \$buttons2 = {
																						    "Sim": function() {
																						    	abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven003/content/ven003ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_acaoId}"), [800,320]);
																							    $(this).dialog("destroy").remove();
																						    },
																						    "Não": function() {
																						    	$(this).dialog("destroy").remove(); 
																						    }
																					    };
																					    dialogConfirm('Transmitir Nota Fiscal', 'Deseja transmitir a nota fiscal? ', \$buttons2);
																					}
																				} else {
																				    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }

																				    divRefresh('{$g['divIdParent']}', true);
																			    	fecharJanela("{$g['divId']}");
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
							    $(this).dialog("destroy").remove();
						    },
						    "Não": function() { $(this).dialog("destroy").remove(); } 
					    };
					    dialogConfirm('Finalizar Nota Fiscal', 'Você tem certeza? ', \$buttons);
                    //}
				});
				$('#ven003PrevisualizarDANFE').unbind('click').bind('click', function(data){
					nova_janela('/hardness3/hardness/nota_emitida2/gerar_pre_danfe.php?T007_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Pre_Danfe');
				});
				$('#ven003VisualizarDANFE').unbind('click').bind('click', function(data){
					if('{$row['T007_Flag_ACP']}' == '8'){
						var buttons = {
							"Impressão": function() {
								nova_janela('/hardness3/hardness/danfe/imprimir_danfe.php?Query='+encodeURIComponent("{$Query}")+'&vlrNF='+encodeURIComponent("{$valorNaDanfe}")+'&T007_Id='+encodeURIComponent("{$r_acaoId}")+'&impressaoHtml=true',0,0,1000,600,'no','no','no','no','no','no','Danfe');
								$(this).dialog("destroy").remove();
							},
							"PDF": function() {
								nova_janela('/hardness3/hardness/danfe/imprimir_danfe.php?Query='+encodeURIComponent("{$Query}")+'&vlrNF='+encodeURIComponent("{$valorNaDanfe}")+'&T007_Id='+encodeURIComponent("{$r_acaoId}")+'&impressaoHtml=false',0,0,1000,600,'no','no','no','no','no','no','Danfe');
								$(this).dialog("destroy").remove();
							},
							"Cancelar": function() {
								$(this).dialog("destroy").remove();
							}
						}
						var dialog = dialogConfirm('DANFE NFC-e', 'Selecione a maneira que deseja abrir o danfe', buttons);
					} else {
						nova_janela('/hardness3/hardness/danfe/imprimir_danfe.php?Query='+encodeURIComponent("{$Query}")+'&vlrNF='+encodeURIComponent("{$valorNaDanfe}")+'&T007_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Danfe');
					}
				});
				$('#ven003enviarCCe').unbind('click').bind('click', function(data){
					nova_janela('/hardness/carta_correcao/principal.php?T007_Id=$r_acaoId&Nova_Janela=S',0,0,900,600,'no','no','no','no','yes','yes',0);
				});
				$('#ven003abrirTelaAntigo').unbind('click').bind('click', function(data){
					nova_janela('/hardness/nota_emitida2/nota_principal.php?Operacao=A&T007_Id=$r_acaoId&Nova_Janela=S&edicaoHard3=S',0,0,1220,700,'no','no','no','no','yes','yes',0);
				});

				$('#ven003ExcluirNFE').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/excluirNota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', "Nota Fiscal Excluida!", \$buttons);
									fecharJanela("{$g['divId']}");
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
				});

				$('#ven003NotaTransporte').unbind('click').bind('click', function(data){
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar nota espelho', '/ven/ven003/content/ven003ContentNotaTransporte/', '&tab=geral&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&divIdAberto=' + encodeURIComponent('{$g['divId']}'), [580,330]);
				});

				$('#ven003AlterarModeloNota').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							 $.getJSON('/ven/ven003/form_func-ajax/alterarModeloNota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
							    if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
									divRefresh('{$g['divIdParent']}', true);
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
					dialogConfirm('Atenção', 'Deseja alterar o modelo para NFe? ', \$buttons);
				});

				$('#ven003AtualizarOpcoes').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons = {
								"Sim": function() {
									var T005_Id = $('#{$g['divId']} #T005_Id').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven003/form_func-ajax/atualizarAliquota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    				dialogConfirm('Ok', request.data, \$buttons);
						    				divRefresh('{$g['divId']}');
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    				dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divIdParent']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
						},
						"Atualizar CFOP": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven003/content/ven003ContentAtualizarCFOP/', '&tab=geral&T007_Id=' + encodeURIComponent("{$r_acaoId}"), [500,200]);
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { 
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
				});
			} </script></div>
EOT;
			//nova_janela('../hardness/carta_correcao/principal.php?T007_Id=$r_acaoId&Nova_Janela=S',0,0,900,600,'no','no','no','no','yes','yes',0);
			
	break;

	case 'ven003contentEnviarNotaFiscal':
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
			array($content1, '/ven/ven003/tab/ven003TabEnviarNotaFiscal/', '&tab=geral&T007_Id=' . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		if ($r_acaoId){
			$sql="select T007_Numero_Protocolo_Nfe from T007 where T007_Id={$r_acaoId}";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
		}
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		if(empty($row['T007_Numero_Protocolo_Nfe'])){
			$botoesEsq .= '<button type="submit" id="ven003FinalizarNFE">Finalizar Nota Fiscal</button>';
			$botoesEsq .= '<button type="submit" id="ven003PrevisualizarDANFE">Pré-visualizar Danfe</button>';
			//$botoesEsq .= '<button type="submit" id="ven003AtualizarAliquotas">Atualizar Aliquotas</button>';
			$botoesEsq .= '<button type="submit" id="ven003AtualizarOpcoes">Atualizar</button>';
			$botoesEsq .= '<button type="submit" id="ven003ExcluirNFE">Excluir</button>';
		}else{
			$botoesEsq .= '<button type="submit" id="ven003VisualizarDANFE">Danfe</button>';
			$botoesEsq .= '<button type="submit" id="ven003enviarCCe">CC-e</button>';
		}
		$botoesEsq .= '<button type="submit" id="ven003abrirTelaAntigo">Versão antiga</button>';
		$botoesEsq .= '<button type="submit" id="ven003NotaTransporte">Nota Transporte</button>';
		//$botoesEsq .= '<button type="submit" id="ven003MarcarEnviar_btn">Marcar Enviar</button>';
		$botoesDir .= '<button type="submit" id="ven003FormPrincipal_btn">Enviar</button>';		
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		
		//Ações dos Botões
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { 
				$('#ven003AtualizarAliquotas').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							var T005_Id = $('#{$g['divId']} #T005_Id').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven003/form_func-ajax/atualizarAliquota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divIdParent']}');
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
				});
				
				$('#ven003FinalizarNFE').unbind('click').bind('click', function(data) {
					// Verifica se precisa de liberação
					$('#{$g['divId']}').showLoading();
					$.getJSON('/ven/ven003/form_func-ajax/verificaAlcada/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						// Não precisa, pode gerar a NF
						if (request.code) {
	                        var \$buttons = {
							    "Sim": function() {
								    $('#{$g['divId']}').showLoading();
								    $.getJSON('/ven/ven003/form_func-ajax/finalizarNotaFiscal/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								    	$('#{$g['divId']}').hideLoading();
									    if (request.code) {
										    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				    dialogConfirm('Ok', request.data, \$buttons);
					    				    divRefresh('{$g['divIdParent']}', true);
					    				    fecharJanela("{$g['divId']}");
									    } else {
										    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				    dialogConfirm('Erro', request.data, \$buttons);
									    }
								    });
								    $(this).dialog("destroy").remove();
							    },
							    "Não": function() { $(this).dialog("destroy").remove(); } 
						    };
						    dialogConfirm('Finalizar Nota Fiscal', 'Você tem certeza? ', \$buttons);
						   // Precisa, pergunta se quer enviar para análise de crédito
						} else {
							var buttons = {
								"Sim": function() {
									// Envia para análise de crédito
								    $('#{$g['divId']}').showLoading();
								   	$.getJSON('/ven/ven003/form_func-ajax/verificaAlcada/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&pedirLiberacao=true&callback=?', function(request) {
								   		$('#{$g['divId']}').hideLoading();
								   		dialogConfirm('Liberação', 'Opção executada com sucesso.');
								   		divRefresh('{$g['divIdParent']}', true);
								   		divRefresh('{$g['divId']}', true);
								   	});
								    $(this).dialog("destroy").remove();
							    },
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							dialogConfirm('Liberação', request.data, buttons);
						}
                    });
				});
				$('#ven003PrevisualizarDANFE').bind('click', function(data){
					nova_janela('/hardness3/hardness/nota_emitida2/gerar_pre_danfe.php?T007_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Pre_Danfe');
				});                                            
				$('#ven003VisualizarDANFE').bind('click', function(data){
					nova_janela('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id='+encodeURIComponent("{$r_acaoId}"),0,0,1000,600,'no','no','no','no','no','no','Danfe');
				});
				$('#ven003enviarCCe').bind('click', function(data){
					nova_janela('/hardness/carta_correcao/principal.php?T007_Id=$r_acaoId&Nova_Janela=S',0,0,900,600,'no','no','no','no','yes','yes',0);
				});
				$('#ven003abrirTelaAntigo').bind('click', function(data){
					nova_janela('/hardness/nota_emitida2/nota_principal.php?Operacao=A&T007_Id=$r_acaoId&Nova_Janela=S&edicaoHard3=S',0,0,1220,700,'no','no','no','no','yes','yes',0);
				});

				$('#ven003ExcluirNFE').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$.getJSON('/ven/ven003/form_func-ajax/excluirNota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', "Nota Fiscal Excluida!", \$buttons);
									fecharJanela("{$g['divId']}");
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
					dialogConfirm('Atenção', 'Deseja excluir a Nota Fiscal?<br /><font color="red">Este processo irá excluir a nota fiscal e seus itens, assim como os títulos a receber,<br />boletos bancários e lançamentos no caixas e bancos referentes a esta nota.</font>', \$buttons);
				});

				$('#ven003NotaTransporte').bind('click', function(data){
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Nota Transporte', '/ven/ven003/content/ven003ContentNotaTransporte/', '&tab=geral&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&divIdAberto=' + encodeURIComponent('{$g['divId']}'), [580,280]);
				});

				$('#ven003MarcarEnviar_btn').bind('click', function(data) {
				    $.getJSON('/ven/ven003/form_func-ajax/marcarEnviarNota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {});		
				});

				$('#ven003AtualizarOpcoes').bind('click', function(data) {
					var \$buttons = {
						"Atualizar Aliquotas": function() {
							var \$buttons = {
								"Sim": function() {
									var T005_Id = $('#{$g['divId']} #T005_Id').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/ven/ven003/form_func-ajax/atualizarAliquota/?ajax=true&T007_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    				dialogConfirm('Ok', request.data, \$buttons);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    				dialogConfirm('Erro', request.data, \$buttons);
										}
										$('#{$g['divId']}').hideLoading();
										divRefresh('{$g['divIdParent']}');
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							$(this).dialog("destroy").remove();
							dialogConfirm('Atualizar Alíquota', 'Você tem certeza? ', \$buttons);
						},
						"Atualizar CFOP": function() { 
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar CFOP', '/ven/ven003/content/ven003ContentAtualizarCFOP/', '&tab=geral&T007_Id=' + encodeURIComponent("{$r_acaoId}"), [500,200]);
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { 
							$(this).dialog("destroy").remove();
						}
					};
					dialogConfirm('Atualizar', 'Qual atualização você deseja? ', \$buttons);
				});
			} </script></div>
EOT;
			//nova_janela('../hardness/carta_correcao/principal.php?T007_Id=$r_acaoId&Nova_Janela=S',0,0,900,600,'no','no','no','no','yes','yes',0);
			
	break;
	
	case 'ven003ContentProdutoEditar':
    	
    	
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
			array($content1, '/ven/ven003/tab/ven003TabProdutoEditar/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode(@$r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button id="ven003FormProdutoEditar_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');	    
	
	break;
	
	case 'ven003ContentCancelamentoNotaEmitida':
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
				array($content1, '/ven/ven003/form/ven003FormCancelarNotaEmitida/', '&tab=geral&T007_Id=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

	break;
	
		case 'ven003ContentEditarDadosNota':
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
				array($content1, '/ven/ven003/form/ven003EditarDadosNota/', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);
			echo gProcessaAcoes($actions);

	break;
	
	case'ven003ContentReferenciada':
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
			array($content1, '/ven/ven003/grid/ven003GridReferenciada/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;

	case'ven003ContentIncluirNotaReferenciada':
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
			array($content1, '/ven/ven003/tab/ven003TabIncluirNotaReferenciada/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003FormIncluirNotaReferenciada">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;

	case 'ven003ContentVisualizarNotaReferenciada':
		$T132_Id = isset($r_T132_Id) ? (int) $r_T132_Id : 0;
		$T007_Id = isset($r_T007_Id) ? (int) $r_T007_Id : 0;
		$referenciaSql = mysql_query("SELECT T132_Id,
											 T132_Tipo,
											 T132_Chave_Acesso,
											 T132_Numero_Nota_Fiscal,
											 T132_Modelo,
											 T132_Cupom_Modelo,
											 T132_Serie,
											 T132_Ano_Mes,
											 T132_Cnpj,
											 T132_Cpf
										FROM T132
									   WHERE T132_Id = '{$T132_Id}'
										 AND T132_T007_Id = '{$T007_Id}'
									   LIMIT 1");
		$referencia = $referenciaSql ? mysql_fetch_assoc($referenciaSql) : false;
		if (!$referencia) {
			echo gGeraAlertMsg('Referência fiscal', 'Registro não encontrado para esta nota fiscal.');
			break;
		}
		$tipoReferencia = $referencia['T132_Cupom_Modelo'] === 'PA' || empty($referencia['T132_Tipo'])
			? 'Pagamento antecipado (gPagAntecipado/refNFe)'
			: $referencia['T132_Tipo'] . ' (NFref)';
		$camposReferencia = array(
			'ID' => $referencia['T132_Id'],
			'Tipo fiscal' => $tipoReferencia,
			'Chave de acesso' => $referencia['T132_Chave_Acesso'],
			'Número' => $referencia['T132_Numero_Nota_Fiscal'],
			'Modelo' => $referencia['T132_Modelo'],
			'Série' => $referencia['T132_Serie'],
			'Emissão (AAMM)' => $referencia['T132_Ano_Mes'],
			'CNPJ/CPF' => !empty($referencia['T132_Cnpj']) ? $referencia['T132_Cnpj'] : $referencia['T132_Cpf']
		);
		echo '<div style="padding:12px 16px;">';
		echo '<table style="width:100%; border-collapse:collapse;">';
		foreach ($camposReferencia as $tituloCampo => $valorCampo) {
			echo '<tr><th style="width:180px; padding:6px; text-align:left; border-bottom:1px solid #ddd;">'
				. htmlspecialchars($tituloCampo, ENT_QUOTES, 'UTF-8')
				. '</th><td style="padding:6px; border-bottom:1px solid #ddd; word-break:break-all;">'
				. htmlspecialchars((string) $valorCampo, ENT_QUOTES, 'UTF-8')
				. '</td></tr>';
		}
		echo '</table></div>';

		$botoesEsq = '';
		$botoesDir = '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

    case 'ven003ContentServicosNF':
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
            array($content1, '/ven/ven003/form/ven003FormServicos/', "&tab=geral&T007_Id=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($content2)),
            array($content2, '/ven/ven003/grid/ven003GridServicos/', '&acaoId=' . urlencode($r_acaoId) . "&divReload=" . urlencode($content1)),
            );
            echo gProcessaAcoes($actions); 
    break;

	case 'ven003ContentDetalhesCheque':
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
		array($content1, '/ven/ven003/form/ven003FormDetalhesCheque/', "&tab=geral&acaoId=" . urlencode($r_T118_Id)),
		);
		echo gProcessaAcoes($actions);			
	
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003FormDetalhesCheque_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');			

	break;

	case 'ven003ContentNFDebitoCreditoAutorizada':
		$T007_IdOrigem = (int) $r_acaoId;
		$origemSql = mysql_query("SELECT T007_Id, T007_D024_Id, T007_D006_Id,
									 T007_Flag_Destino_Produto, T007_Flag_ACP,
									 T007_Flag_Cancelada, T007_Flag_Inutilizado,
									 T007_Flag_Denegado, T007_Numero_Nota_Fiscal,
									 T007_Serie, T007_Chave_Acesso_Nfe,
									 T007_Numero_Protocolo_Nfe, D024_Nome_Empresa
								FROM T007
							LEFT JOIN D024 ON D024_Id=T007_D024_Id
							   WHERE T007_Id='{$T007_IdOrigem}'
								 AND T007_C004_Id='{$g['empresaAtual']}'
							   LIMIT 1");
		$origem = $origemSql ? mysql_fetch_assoc($origemSql) : false;
		if (!$origem || (int) $origem['T007_Flag_ACP'] !== 6
			|| empty($origem['T007_Numero_Protocolo_Nfe'])
			|| strlen($origem['T007_Chave_Acesso_Nfe']) !== 44
			|| substr($origem['T007_Chave_Acesso_Nfe'], 20, 2) !== '55'
			|| $origem['T007_Flag_Cancelada'] === 'S'
			|| $origem['T007_Flag_Inutilizado'] === 'S'
			|| $origem['T007_Flag_Denegado'] === 'S') {
			echo gGeraAlertMsg('NF de Débito/Crédito', 'A origem deve ser uma NF-e modelo 55 autorizada e ativa.');
			break;
		}

		$casasQuantidade = isset($g['c020']['C020_Casas_Decimais_Quantidade']) ? (int) $g['c020']['C020_Casas_Decimais_Quantidade'] : 2;
		$casasQuantidade = max(0, min(6, $casasQuantidade));
		$linhasProdutos = '';
		$itensSql = mysql_query("SELECT T008_Id,T008_Codigo_Produto,T008_Descricao_Produto,T008_Quantidade,
										IFNULL(T008A_Situacao_Tributaria_CBS_IBS,'') AS T008A_Situacao_Tributaria_CBS_IBS,
										IFNULL(T008A_Classificacao_Tributaria_CBS_IBS,'') AS T008A_Classificacao_Tributaria_CBS_IBS,
										IFNULL(T008A_Aliquota_CBS,0) AS T008A_Aliquota_CBS,
										IFNULL(T008A_Percentual_Reducao_CBS,0) AS T008A_Percentual_Reducao_CBS,
										IFNULL(T008A_Percentual_Diferimento_CBS,0) AS T008A_Percentual_Diferimento_CBS,
										IFNULL(T008A_Aliquota_IBS_UF,0) AS T008A_Aliquota_IBS_UF,
										IFNULL(T008A_Percentual_Reducao_IBS_UF,0) AS T008A_Percentual_Reducao_IBS_UF,
										IFNULL(T008A_Percentual_Diferimento_IBS_UF,0) AS T008A_Percentual_Diferimento_IBS_UF,
										IFNULL(T008A_Aliquota_IBS_Municipal,0) AS T008A_Aliquota_IBS_Municipal,
										IFNULL(T008A_Percentual_Reducao_IBS_Municipal,0) AS T008A_Percentual_Reducao_IBS_Municipal,
										IFNULL(T008A_Percentual_Diferimento_IBS_Municipal,0) AS T008A_Percentual_Diferimento_IBS_Municipal,
										IFNULL(T008A_Valor_Base_CBS_IBS,0) AS T008A_Valor_Base_CBS_IBS,
										IFNULL(T008A_Valor_CBS,0) AS T008A_Valor_CBS,
										IFNULL(T008A_Valor_IBS,0) AS T008A_Valor_IBS,
										IFNULL(T008A_Valor_IBS_UF,0) AS T008A_Valor_IBS_UF,
										IFNULL(T008A_Valor_IBS_Municipal,0) AS T008A_Valor_IBS_Municipal,
										IFNULL(T008A_Valor_Diferimento_CBS,0) AS T008A_Valor_Diferimento_CBS,
										IFNULL(T008A_Valor_Diferimento_IBS_UF,0) AS T008A_Valor_Diferimento_IBS_UF,
										IFNULL(T008A_Valor_Diferimento_IBS_Municipal,0) AS T008A_Valor_Diferimento_IBS_Municipal,
										IFNULL(T008A_Valor_IBS_Transferido,0) AS T008A_Valor_IBS_Transferido,
										IFNULL(T008A_Valor_CBS_Transferido,0) AS T008A_Valor_CBS_Transferido,
										IFNULL(T008A_Competencia_Ajuste,'') AS T008A_Competencia_Ajuste,
										IFNULL(T008A_Valor_IBS_Ajuste_Competencia,0) AS T008A_Valor_IBS_Ajuste_Competencia,
										IFNULL(T008A_Valor_CBS_Ajuste_Competencia,0) AS T008A_Valor_CBS_Ajuste_Competencia,
										IFNULL(T008A_Valor_IBS_Estornado,0) AS T008A_Valor_IBS_Estornado,
										IFNULL(T008A_Valor_CBS_Estornado,0) AS T008A_Valor_CBS_Estornado
								 FROM T008
							LEFT JOIN T008A ON T008A_T008_Id=T008_Id
								WHERE T008_T007_Id='{$T007_IdOrigem}'
							 ORDER BY T008_Id");
		if (!$itensSql) {
			echo gGeraAlertMsg('NF de Débito/Crédito', 'Não foi possível consultar os produtos da NF-e autorizada: ' . mysql_error());
			break;
		}
		$linhasReformaNFDC = '';
		while ($item = mysql_fetch_assoc($itensSql)) {
			$valorBR = function ($campo, $casas = 2) use ($item) {
				return number_format((float) $item[$campo], $casas, ',', '.');
			};
			$inputFiscal = function ($campo, $classe, $casas = 2, $largura = 82) use ($valorBR) {
				return '<label class="' . $classe['grupo'] . '">' . $classe['label'] . '<input type="text" class="campoReformaNFDC" data-campo="' . $campo . '" value="' . $valorBR($campo, $casas) . '" style="width:' . $largura . 'px;text-align:right" /></label>';
			};
			$inputConsultaFiscal = function ($campo, $label) use ($valorBR) {
				return '<label class="grupoReformaComum campoConsultaNFDC">' . $label . '<input type="text" value="' . $valorBR($campo, 2) . '" readonly="readonly" /></label>';
			};
			$competencia = htmlspecialchars($item['T008A_Competencia_Ajuste'], ENT_QUOTES, 'UTF-8');
			if (preg_match('/^(\d{4})-(\d{2})$/', $competencia, $partesCompetencia)) {
				$competencia = $partesCompetencia[2] . '/' . $partesCompetencia[1];
			}
			$cst = htmlspecialchars($item['T008A_Situacao_Tributaria_CBS_IBS'], ENT_QUOTES, 'UTF-8');
			$cclass = htmlspecialchars($item['T008A_Classificacao_Tributaria_CBS_IBS'], ENT_QUOTES, 'UTF-8');
			$linhasReformaNFDC .= '<div class="linhaReformaNFDCOrigem" data-t008-id="' . (int) $item['T008_Id'] . '" style="display:none"><div class="nfdc-reforma-item">'
				. '<div class="nfdc-reforma-titulo"><div><strong>Valores da NF-e de origem</strong><small>Somente para consulta. A nova NF recalculará CBS e IBS conforme sua quantidade e configuração fiscal.</small></div><span>Origem: CST ' . ($cst !== '' ? $cst : '-') . ' &nbsp;·&nbsp; cClassTrib ' . ($cclass !== '' ? $cclass : '-') . '</span></div>'
				. '<div class="nfdc-reforma-campos nfdc-valores-principais">'
				. $inputConsultaFiscal('T008A_Valor_Base_CBS_IBS', 'Base CBS/IBS')
				. $inputConsultaFiscal('T008A_Valor_CBS', 'Valor CBS')
				. $inputConsultaFiscal('T008A_Valor_IBS_UF', 'IBS UF')
				. $inputConsultaFiscal('T008A_Valor_IBS_Municipal', 'IBS Municipal')
				. '<label class="grupoReformaComum campoCalculadoNFDC">IBS total<input type="text" class="valorIBSTotalNFDC" value="' . $valorBR('T008A_Valor_IBS', 2) . '" readonly="readonly" /></label>'
				. '</div>'
				. '<div class="nfdc-grupo-especial grupoTransferenciaNFDC"><div class="nfdc-grupo-especial-texto"><strong>Transferência de crédito</strong><small>Preencha os valores exigidos pelo grupo gTransfCred.</small></div>'
				. $inputFiscal('T008A_Valor_IBS_Transferido', array('label'=>'Valor IBS transferido','grupo'=>'campoEspecialNFDC'))
				. $inputFiscal('T008A_Valor_CBS_Transferido', array('label'=>'Valor CBS transferido','grupo'=>'campoEspecialNFDC')) . '</div>'
				. '<div class="nfdc-grupo-especial grupoAjusteNFDC"><div class="nfdc-grupo-especial-texto"><strong>Ajuste de competência</strong><small>Preencha os dados exigidos pelo grupo gAjusteCompet.</small></div>'
				. '<label class="campoEspecialNFDC">Competência (MM/AAAA)<input type="text" class="campoReformaNFDC" data-campo="T008A_Competencia_Ajuste" value="' . $competencia . '" maxlength="7" placeholder="MM/AAAA" /></label>'
				. $inputFiscal('T008A_Valor_IBS_Ajuste_Competencia', array('label'=>'Valor IBS do ajuste','grupo'=>'campoEspecialNFDC'))
				. $inputFiscal('T008A_Valor_CBS_Ajuste_Competencia', array('label'=>'Valor CBS do ajuste','grupo'=>'campoEspecialNFDC')) . '</div>'
				. '<div class="nfdc-grupo-especial grupoEstornoNFDC"><div class="nfdc-grupo-especial-texto"><strong>Estorno de crédito</strong><small>Preencha os valores exigidos pelo grupo gEstornoCred.</small></div>'
				. $inputFiscal('T008A_Valor_IBS_Estornado', array('label'=>'Valor IBS estornado','grupo'=>'campoEspecialNFDC'))
				. $inputFiscal('T008A_Valor_CBS_Estornado', array('label'=>'Valor CBS estornado','grupo'=>'campoEspecialNFDC')) . '</div>'
				. '</div></div>';
		}
		if ($linhasReformaNFDC === '') {
			echo gGeraAlertMsg('NF de Débito/Crédito', 'A NF-e autorizada não possui produtos.');
			break;
		}

		$idModal = 'ven003NFDCOrigem_' . uniqid();
		$numeroOrigem = htmlspecialchars($origem['T007_Numero_Nota_Fiscal'], ENT_QUOTES, 'UTF-8');
		$serieOrigem = htmlspecialchars($origem['T007_Serie'], ENT_QUOTES, 'UTF-8');
		$clienteOrigem = htmlspecialchars($origem['D024_Nome_Empresa'], ENT_QUOTES, 'UTF-8');
		$chaveOrigem = htmlspecialchars($origem['T007_Chave_Acesso_Nfe'], ENT_QUOTES, 'UTF-8');
		$credito02Disabled = (int) date('Y') < 2029 ? ' disabled="disabled"' : '';
		require_once('bibliotecas/classes/ReformaTributaria.php');
		$regrasFiscaisNFDC = json_encode((new ReformaTributaria())->getRegrasNFDebitoCredito());

		// Formulário nativo do framework. O content é carregado pela rota /form,
		// mantendo os metadados que os atalhos F (formulário) e P (grade) usam.
		// A grade é inserida depois deste content e, portanto, permanece o alvo
		// do atalho P.
		$contentFormularioNFDC = 'ven003FormNFDebitoCreditoAutorizada_' . uniqid();
		$rotaFormularioNFDC = '/ven/ven003/form/ven003FormNFDebitoCreditoAutorizada/';
		ob_start();
		echo '<div id="' . $contentFormularioNFDC . '" class="content nfdc-formulario-framework" style="width:100%;height:auto"></div>';
		echo gProcessaAcoes(array(
			array($contentFormularioNFDC, $rotaFormularioNFDC, '', true)
		));
		$formularioNFDCHtml = ob_get_clean();

		// A grade de itens é um content/grid real do framework. Assim o atalho
		// Ctrl+Alt+Shift+P atua na grade desta janela, e não na NF aberta ao fundo.
		$contentGridNFDC = 'ven003GridNFDebitoCreditoAutorizada_' . uniqid();
		$g['smarty']->assign('content1', $contentGridNFDC);
		$g['smarty']->assign('tipo', 'unico');
		ob_start();
		echo '<div id="' . $contentGridNFDC . '" class="content" style="width:100%;height:auto"></div>';
		echo gProcessaAcoes(array(
			array($contentGridNFDC, '/ven/ven003/grid/ven003GridNFDebitoCreditoAutorizada/', '&T007_Id=' . urlencode($T007_IdOrigem), true)
		));
		$gradeNFDCHtml = ob_get_clean();
		echo <<<EOT
		<style type="text/css">
			#{$idModal} { position:relative; min-height:100%; color:#263746; }
			#{$idModal} .nfdc-conteudo { padding:14px 16px 68px; }
			#{$idModal} .nfdc-aviso { padding:10px 12px; margin-bottom:12px; background:#fff9df; border:1px solid #ead88b; border-radius:4px; color:#554b22; }
			#{$idModal} .nfdc-origem { padding:11px 13px; margin-bottom:13px; line-height:1.35; background:#f2f7fa; border:1px solid #c8d8e2; border-radius:4px; }
			#{$idModal} .nfdc-autoajuda { margin-bottom:13px; border:1px solid #9fc5da; border-radius:4px; background:#f7fbfd; box-shadow:0 1px 2px rgba(40,79,101,.08); }
			#{$idModal} .nfdc-autoajuda-cabecalho { display:flex; align-items:center; width:100%; min-height:38px; padding:7px 10px; box-sizing:border-box; border:0; border-radius:3px; background:#e9f4f9; color:#294f64; text-align:left; }
			#{$idModal} .nfdc-autoajuda-cabecalho:hover { background:#deeff6; border-color:transparent; }
			#{$idModal} .nfdc-autoajuda-icone { display:inline-flex; align-items:center; justify-content:center; width:21px; height:21px; margin-right:8px; border-radius:50%; background:#4389ae; color:#fff; font-weight:bold; }
			#{$idModal} .nfdc-autoajuda-titulo { font-weight:bold; white-space:nowrap; }
			#{$idModal} .nfdc-autoajuda-resumo { flex:1; margin-left:12px; color:#506b79; font-weight:normal; }
			#{$idModal} .nfdc-autoajuda-seta { margin-left:8px; font-size:13px; }
			#{$idModal} .nfdc-autoajuda-lista { display:block; padding:7px 10px 9px; border-top:1px solid #c8dde8; }
			#{$idModal} .nfdc-autoajuda-passo { display:flex; align-items:center; gap:8px; min-height:29px; padding:3px 7px; border-radius:3px; color:#526a77; cursor:pointer; }
			#{$idModal} .nfdc-autoajuda-passo:hover { background:#edf5f8; }
			#{$idModal} .nfdc-autoajuda-passo-atual { background:#e4f2f8; color:#224f68; font-weight:bold; }
			#{$idModal} .nfdc-autoajuda-passo-concluido { color:#70848e; }
			#{$idModal} .nfdc-autoajuda-numero { display:inline-flex; flex:0 0 20px; align-items:center; justify-content:center; width:20px; height:20px; border:1px solid #9ebac9; border-radius:50%; background:#fff; font-size:10px; }
			#{$idModal} .nfdc-autoajuda-passo-atual .nfdc-autoajuda-numero { border-color:#4389ae; background:#4389ae; color:#fff; }
			#{$idModal} .nfdc-autoajuda-passo-concluido .nfdc-autoajuda-numero { border-color:#75a68b; background:#75a68b; color:#fff; }
			#{$idModal} .nfdc-formulario-framework { min-height:112px; margin-bottom:8px; padding:3px 0; border-bottom:1px solid #d8e2e8; }
			#{$idModal} .nfdc-formulario-framework .formFormatoEditor { min-height:108px; }
			#{$idModal} .nfdc-formulario-framework select { height:28px; box-sizing:border-box; border:1px solid #aebdc8; border-radius:3px; background:#fff; }
			#{$idModal} .nfdc-formulario-framework input { height:26px; box-sizing:border-box; }
			#{$idModal} .nfdc-formulario-ajuda { margin:-29px 0 12px 455px; color:#666; }
			#{$idModal} .nfdc-grade { margin-top:8px; }
			#{$idModal} .nfdc-grade .content { height:auto !important; min-height:0 !important; }
			#{$idModal} .nfdc-reforma-grade { margin-top:0; }
			#{$idModal} .nfdc-referencia { margin:12px 0; padding:9px 11px; border-left:4px solid #4c89ad; border-radius:0 4px 4px 0; background:#f3f8fb; }
			#{$idModal} button { min-height:27px; padding:3px 10px; border:1px solid #aebbc3; border-radius:3px; background:#f7f8f9; color:#30434f; cursor:pointer; }
			#{$idModal} button:hover { background:#eaf1f5; border-color:#8fa7b5; }
			#{$idModal} .nfdc-grade { max-height:430px; overflow:auto; border:1px solid #b7c4cc; border-radius:4px; background:#fff; }
			#{$idModal} .nfdc-grade table { width:100%; border-collapse:collapse; }
			#{$idModal} .nfdc-grade th { position:sticky; top:0; z-index:1; padding:8px 7px; background:#e9eff4; color:#334b5b; border-bottom:1px solid #bccbd4; }
			#{$idModal} .linhaProdutoNFDCOrigem td { padding:9px 7px; background:#fff; border-bottom:0; }
			#{$idModal} .qtdNFDCOrigem, #{$idModal} .precoNFDCOrigem { height:24px; padding:0 6px; border:1px solid #91a6b4; border-radius:3px; background:#fff; }
			#{$idModal} .nfdc-reforma-item { padding:0 12px 12px; background:#fff; border-bottom:7px solid #edf1f4; }
			#{$idModal} .nfdc-reforma-titulo { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:10px 0 8px; color:#35566a; border-top:1px solid #d8e2e8; }
			#{$idModal} .nfdc-reforma-titulo strong { display:block; font-size:12px; }
			#{$idModal} .nfdc-reforma-titulo small { display:block; margin-top:2px; color:#6b7e89; font-weight:normal; }
			#{$idModal} .nfdc-reforma-titulo > span { padding:4px 8px; background:#eef4f7; border-radius:10px; color:#587080; font-size:10px; white-space:nowrap; }
			#{$idModal} .nfdc-reforma-campos { display:grid; grid-template-columns:repeat(5,minmax(115px,1fr)); gap:9px; padding:10px; background:#f7fafb; border:1px solid #d8e3e9; border-radius:4px; }
			#{$idModal} .nfdc-reforma-campos label, #{$idModal} .nfdc-grupo-especial label { display:flex; flex-direction:column; gap:4px; color:#425965; font-size:11px; font-weight:bold; white-space:nowrap; }
			#{$idModal} .nfdc-reforma-campos input, #{$idModal} .nfdc-grupo-especial input { width:100% !important; height:26px; padding:0 7px; box-sizing:border-box; text-align:right; border:1px solid #9eb2bf; border-radius:3px; background:#fff; color:#243640; }
			#{$idModal} .nfdc-reforma-campos input:focus, #{$idModal} .nfdc-grupo-especial input:focus { border-color:#4c8fb8; box-shadow:0 0 0 2px rgba(76,143,184,.12); outline:none; }
			#{$idModal} .campoConsultaNFDC input, #{$idModal} .campoCalculadoNFDC input { background:#edf2f4; color:#526873; border-color:#c8d4da; cursor:default; }
			#{$idModal} .nfdc-grupo-especial { align-items:flex-end; gap:12px; margin-top:9px; padding:10px; background:#fff8e7; border:1px solid #e7cf91; border-left:4px solid #d59c28; border-radius:4px; }
			#{$idModal} .nfdc-grupo-especial-texto { flex:1 1 auto; min-width:250px; align-self:center; color:#685119; }
			#{$idModal} .nfdc-grupo-especial-texto strong, #{$idModal} .nfdc-grupo-especial-texto small { display:block; }
			#{$idModal} .nfdc-grupo-especial-texto small { margin-top:3px; color:#806d3e; }
			#{$idModal} .campoEspecialNFDC { flex:0 0 155px; }
			#{$idModal} .grupoTransferenciaNFDC, #{$idModal} .grupoAjusteNFDC, #{$idModal} .grupoEstornoNFDC { display:none; }
			#{$idModal} .nfdc-rodape { position:absolute; bottom:0; left:0; right:0; display:flex; justify-content:flex-end; gap:6px; padding:10px 16px; background:#f3f5f6; border-top:1px solid #cbd4d9; }
			#{$idModal} #ven003GerarNFDCOrigem { background:#397fa8; border-color:#2f6f94; color:#fff; font-weight:bold; }
			#{$idModal} #ven003GerarNFDCOrigem:hover { background:#2f7198; }
			#{$idModal} #ven003GerarNFDCOrigem:disabled { opacity:.55; cursor:default; }
		</style>
		<div id="{$idModal}">
			<div class="nfdc-conteudo">
				<div class="nfdc-aviso"><strong>Geração em elaboração:</strong> a nova NF será criada para revisão no Fiscal. A referência será preenchida automaticamente somente quando exigida pela Nota Técnica.</div>
				<div class="nfdc-origem"><strong>NF-e de origem:</strong> {$numeroOrigem} &nbsp; Série: {$serieOrigem}<br /><strong>Cliente:</strong> {$clienteOrigem}<br /><strong>Chave:</strong> {$chaveOrigem}</div>
				<div class="nfdc-autoajuda" id="autoajudaNFDCOrigem">
					<button type="button" class="nfdc-autoajuda-cabecalho" id="alternarAutoajudaNFDCOrigem" aria-expanded="true">
						<span class="nfdc-autoajuda-icone">?</span><span class="nfdc-autoajuda-titulo">Ajuda de preenchimento</span>
						<span class="nfdc-autoajuda-resumo" id="resumoAutoajudaNFDCOrigem">1. Escolha se será uma NF de débito ou de crédito.</span><span class="nfdc-autoajuda-seta">&#9650;</span>
					</button>
					<div class="nfdc-autoajuda-lista" id="listaAutoajudaNFDCOrigem">
						<div class="nfdc-autoajuda-passo" data-passo="1" data-alvo="#tipoOperacaoNFDCOrigem"><span class="nfdc-autoajuda-numero">1</span><span>Selecione a operação: débito ou crédito.</span></div>
						<div class="nfdc-autoajuda-passo" data-passo="2" data-alvo="#cfopNFDCOrigem"><span class="nfdc-autoajuda-numero">2</span><span>Selecione a CFOP adequada à operação fiscal.</span></div>
						<div class="nfdc-autoajuda-passo" data-passo="3" data-alvo="#tipoFiscalNFDCOrigem"><span class="nfdc-autoajuda-numero">3</span><span>Escolha o tipo fiscal liberado conforme a operação.</span></div>
						<div class="nfdc-autoajuda-passo" data-passo="4" data-alvo="#percentualNFDCOrigem"><span class="nfdc-autoajuda-numero">4</span><span>Confirme o percentual ou informe manualmente as quantidades.</span></div>
						<div class="nfdc-autoajuda-passo" data-passo="5" data-alvo=".campoEspecialNFDC:visible:first input"><span class="nfdc-autoajuda-numero">5</span><span>Quando solicitado, preencha os dados especiais da Reforma Tributária.</span></div>
						<div class="nfdc-autoajuda-passo" data-passo="6" data-alvo="#ven003GerarNFDCOrigem"><span class="nfdc-autoajuda-numero">6</span><span>Revise os dados e clique em Gerar NF.</span></div>
					</div>
				</div>
				{$formularioNFDCHtml}
				<div class="nfdc-formulario-ajuda">Informe a nova quantidade e, se necessário, o preço unit. novo de cada item na grade. Preço em branco mantém o da origem.</div>
				<div class="nfdc-referencia" id="mensagemReferenciaNFDCOrigem">Selecione a operação e o tipo fiscal para visualizar como a NF-e de origem será referenciada.</div>
				<div class="nfdc-grade">{$gradeNFDCHtml}</div>
				<div class="nfdc-reforma-grade">{$linhasReformaNFDC}</div>
			</div>
			<div class="buttonsBottom nfdc-rodape"><button type="button" id="ven003GerarNFDCOrigem">Gerar NF</button><button type="button" onclick="$(this).closest('.ui-dialog-content').dialog('close');">Fechar</button></div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var modal = $('#{$idModal}');
				var casasQuantidadeNFDCOrigem = {$casasQuantidade};
				var quantidadeRevisadaNFDCOrigem = false;
				var regrasFiscaisNFDCOrigem = {$regrasFiscaisNFDC};
				var tiposCredito = '<option value="">Selecione...</option><option value="01">01 - Multa e juros</option><option value="02"{$credito02Disabled}>02 - Crédito presumido ZFM (a partir de 2029)</option><option value="03">03 - Retorno por recusa total/não localização</option><option value="04">04 - Redução de valores</option><option value="05">05 - Transferência de crédito na sucessão</option><option value="06">06 - Retorno por recusa parcial na entrega</option>';
				var tiposDebito = '<option value="">Selecione...</option><option value="01">01 - Transferência de créditos p/ cooperativas</option><option value="02">02 - Anulação de crédito por saídas imunes/isentas</option><option value="03">03 - Débitos de NF não processadas na apuração</option><option value="04">04 - Multa e juros</option><option value="05">05 - Transferência de crédito de sucessão</option><option value="07">07 - Perda em estoque</option><option value="08">08 - Desenquadramento do Simples Nacional</option>';
				var textosAutoajudaNFDCOrigem = {
					1: '1. Escolha se será uma NF de débito ou de crédito.',
					2: '2. Agora selecione a CFOP adequada à operação.',
					3: '3. Selecione o tipo fiscal da NF de débito ou crédito.',
					4: '4. Confirme o percentual e as quantidades que serão geradas.',
					5: '5. Preencha os dados especiais exigidos para este tipo fiscal.',
					6: '6. Dados principais informados. Revise e gere a nova NF.'
				};
				function abrirSeletorNFDCOrigem(campo) {
					if (!campo || !campo.length || campo.prop('disabled')) return;
					campo.focus();
					try { if (campo[0] && typeof campo[0].showPicker === 'function') campo[0].showPicker(); } catch (erro) {}
				}
				function passoAtualAutoajudaNFDCOrigem() {
					if (!modal.find('#tipoOperacaoNFDCOrigem').val()) return 1;
					var cfopSelecionada = modal.find('#cfopNFDCOrigem').val();
					if (!cfopSelecionada || cfopSelecionada === '0') return 2;
					if (!modal.find('#tipoFiscalNFDCOrigem').val()) return 3;
					if (!quantidadeRevisadaNFDCOrigem) return 4;
					var grupoEspecial = modal.find('.nfdc-grupo-especial:visible');
					if (grupoEspecial.length) {
						var pendente = false;
						grupoEspecial.each(function() {
							var grupo = $(this), linha = grupo.closest('.linhaReformaNFDCOrigem');
							var quantidade = modal.find('.qtdNFDCOrigem[data-t008-id="' + linha.attr('data-t008-id') + '"]');
							if (numeroNFDCOrigem(quantidade.val()) <= 0) return;
							var positivo = false;
							grupo.find('.campoReformaNFDC').each(function() {
								if ($(this).attr('data-campo') === 'T008A_Competencia_Ajuste') {
									if (!$.trim($(this).val())) pendente = true;
								} else if (numeroNFDCOrigem($(this).val()) >= 0.005) positivo = true;
							});
							if (!grupo.hasClass('grupoEstornoNFDC') && !positivo) pendente = true;
						});
						if (pendente) return 5;
					}
					return 6;
				}
				function atualizarAutoajudaNFDCOrigem(passoForcado) {
					var passo = passoForcado || passoAtualAutoajudaNFDCOrigem();
					modal.find('#resumoAutoajudaNFDCOrigem').text(textosAutoajudaNFDCOrigem[passo]);
					modal.find('.nfdc-autoajuda-passo').removeClass('nfdc-autoajuda-passo-atual nfdc-autoajuda-passo-concluido').each(function() {
						var numero = parseInt($(this).attr('data-passo'), 10);
						if (numero < passo) $(this).addClass('nfdc-autoajuda-passo-concluido');
						if (numero === passo) $(this).addClass('nfdc-autoajuda-passo-atual');
					});
				}
				function numeroNFDCOrigem(valor) {
					valor = $.trim(String(valor || '0'));
					if (valor.indexOf(',') >= 0) valor = valor.replace(/\./g, '').replace(',', '.');
					var numero = parseFloat(valor);
					return isNaN(numero) ? 0 : numero;
				}
				function formatarQuantidadeNFDCOrigem(valor) {
					var fator = Math.pow(10, casasQuantidadeNFDCOrigem);
					valor = Math.round((valor + 0.000000001) * fator) / fator;
					return valor.toFixed(casasQuantidadeNFDCOrigem).replace('.', ',');
				}
				function atualizarTipoNFDCOrigem() {
					var operacao = modal.find('#tipoOperacaoNFDCOrigem').val();
					var tipo = modal.find('#tipoFiscalNFDCOrigem');
					if (operacao === 'credito') { tipo.html(tiposCredito).removeAttr('disabled').prop('disabled',false); }
					else if (operacao === 'debito') { tipo.html(tiposDebito).removeAttr('disabled').prop('disabled',false); }
					else { tipo.html('<option value="">Selecione primeiro a operação...</option>').attr('disabled','disabled').prop('disabled',true); }
					atualizarMensagemNFDCOrigem();
					atualizarAutoajudaNFDCOrigem();
				}
				function atualizarMensagemNFDCOrigem() {
					var operacao = modal.find('#tipoOperacaoNFDCOrigem').val();
					var tipo = modal.find('#tipoFiscalNFDCOrigem').val();
					var mensagem = 'Este tipo não exige referência automática à NF-e de origem.';
					if (operacao === 'credito' && (tipo === '01' || tipo === '03' || tipo === '04')) mensagem = '<strong>Referência automática no cabeçalho:</strong> ide/NFref/refNFe.';
					if ((operacao === 'debito' && (tipo === '03' || tipo === '04')) || (operacao === 'credito' && tipo === '06')) mensagem = '<strong>Referência automática por item:</strong> det/DFeReferenciado' + ((operacao === 'debito' && tipo === '03') ? ', sem nItem.' : ', com nItem da NF-e de origem.');
					modal.find('#mensagemReferenciaNFDCOrigem').html(mensagem);
					var regra = regrasFiscaisNFDCOrigem[(operacao === 'debito' ? '6' : '5') + ':' + tipo] || {};
					var selecionado = operacao !== '' && tipo !== '';
					modal.find('.linhaReformaNFDCOrigem').toggle(selecionado);
					modal.find('.grupoTransferenciaNFDC,.grupoAjusteNFDC,.grupoEstornoNFDC').hide();
					if (regra.grupo === 'transferencia') modal.find('.grupoTransferenciaNFDC').css('display','flex');
					if (regra.grupo === 'ajuste') modal.find('.grupoAjusteNFDC').css('display','flex');
					if (regra.grupo === 'estorno' || regra.classe === null) {
						modal.find('.grupoEstornoNFDC').css('display','flex');
						modal.find('.grupoEstornoNFDC small').text(regra.grupo === 'estorno'
							? 'Perda em estoque: informe os créditos efetivamente estornados; valores zerados são permitidos.'
							: 'Informe os valores de estorno conforme a apuração do fiscal. Se não houver estorno, deixe zerado.');
					}
					quantidadeRevisadaNFDCOrigem = false;
					atualizarAutoajudaNFDCOrigem();
				}
				modal.undelegate('#alternarAutoajudaNFDCOrigem', 'click.nfdcOrigem').delegate('#alternarAutoajudaNFDCOrigem', 'click.nfdcOrigem', function() {
					var lista = modal.find('#listaAutoajudaNFDCOrigem');
					var abrir = !lista.is(':visible');
					lista.toggle(abrir);
					$(this).attr('aria-expanded', abrir ? 'true' : 'false').find('.nfdc-autoajuda-seta').html(abrir ? '&#9650;' : '&#9660;');
				});
				modal.undelegate('.nfdc-autoajuda-passo', 'click.nfdcOrigem').delegate('.nfdc-autoajuda-passo', 'click.nfdcOrigem', function() {
					var passo = parseInt($(this).attr('data-passo'), 10);
					var alvo = modal.find($(this).attr('data-alvo')).first();
					atualizarAutoajudaNFDCOrigem(passo);
					if (alvo.is('select')) abrirSeletorNFDCOrigem(alvo); else alvo.focus();
				});
				modal.undelegate('#tipoOperacaoNFDCOrigem', 'change.nfdcOrigem').delegate('#tipoOperacaoNFDCOrigem', 'change.nfdcOrigem', atualizarTipoNFDCOrigem);
				modal.undelegate('#tipoFiscalNFDCOrigem', 'change.nfdcOrigem').delegate('#tipoFiscalNFDCOrigem', 'change.nfdcOrigem', atualizarMensagemNFDCOrigem);
				modal.undelegate('#cfopNFDCOrigem,.campoReformaNFDC', 'change.nfdcAutoajuda').delegate('#cfopNFDCOrigem,.campoReformaNFDC', 'change.nfdcAutoajuda', function() { atualizarAutoajudaNFDCOrigem(); });
				modal.undelegate('.qtdNFDCOrigem', 'change.nfdcAutoajuda').delegate('.qtdNFDCOrigem', 'change.nfdcAutoajuda', function() { quantidadeRevisadaNFDCOrigem = true; atualizarAutoajudaNFDCOrigem(); });
				function iniciarFormularioNFDCOrigem() {
					if (!modal.find('#tipoOperacaoNFDCOrigem').length) { setTimeout(iniciarFormularioNFDCOrigem, 30); return; }
					atualizarTipoNFDCOrigem();
					atualizarAutoajudaNFDCOrigem();
					setTimeout(function() { abrirSeletorNFDCOrigem(modal.find('#tipoOperacaoNFDCOrigem')); }, 120);
				}
				iniciarFormularioNFDCOrigem();
				modal.undelegate('#preencherPercentualNFDCOrigem', 'click.nfdcOrigem').delegate('#preencherPercentualNFDCOrigem', 'click.nfdcOrigem', function() {
					var percentual = numeroNFDCOrigem(modal.find('#percentualNFDCOrigem').val());
					if (percentual <= 0 || percentual > 100) {
						dialogConfirm('Percentual inválido', 'Informe um percentual maior que zero e menor ou igual a 100%.');
						return;
					}
					modal.find('.qtdNFDCOrigem').each(function() {
						var campoQuantidade = $(this);
						var quantidadeOrigem = numeroNFDCOrigem(campoQuantidade.attr('data-quantidade-origem'));
						campoQuantidade.val(formatarQuantidadeNFDCOrigem(quantidadeOrigem * percentual / 100));
					});
					quantidadeRevisadaNFDCOrigem = true;
					atualizarAutoajudaNFDCOrigem();
				});
				modal.undelegate('#ven003GerarNFDCOrigem', 'click.nfdcOrigem').delegate('#ven003GerarNFDCOrigem', 'click.nfdcOrigem', function() {
					var botao = $(this), quantidades = {}, precos = {}, reforma = {};
					if (botao.prop('disabled')) return;
					modal.find('.qtdNFDCOrigem').each(function() { var campo=$(this); quantidades[campo.attr('data-t008-id')]=campo.val(); });
					// Enviar o texto original: não perder as dez casas decimais em parseFloat.
					modal.find('.precoNFDCOrigem').each(function() { var campo=$(this); precos[campo.attr('data-t008-id')]=campo.val(); });
					modal.find('.linhaReformaNFDCOrigem').each(function() {
						var linha=$(this), dados={};
						linha.find('.campoReformaNFDC').each(function(){ dados[$(this).attr('data-campo')]=$(this).val(); });
						reforma[linha.data('t008-id')]=dados;
					});
					botao.prop('disabled',true); modal.showLoading();
					$.ajax({url:'/ven/ven003/form_func-ajax/gerarNFDebitoCreditoAutorizada/',dataType:'jsonp',data:{ajax:true,T007_Id:'{$T007_IdOrigem}',tipoOperacao:modal.find('#tipoOperacaoNFDCOrigem').val(),tipoFiscal:modal.find('#tipoFiscalNFDCOrigem').val(),D006_Id:modal.find('#cfopNFDCOrigem').val(),quantidades:JSON.stringify(quantidades),precos:JSON.stringify(precos),reforma:JSON.stringify(reforma)},success:function(request){modal.hideLoading();if(!request.code)botao.prop('disabled',false);dialogConfirm(request.code?'NF gerada':'Verifique os dados',request.data);},error:function(){modal.hideLoading();botao.prop('disabled',false);dialogConfirm('Erro','Não foi possível gerar a NF.');}});
				});
			} </script></div>
		</div>
EOT;
	break;

	case 'ven003ContentNotaTransporte':
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
			array($content1, '/ven/ven003/form/ven003FormNotaTransporte/', '&tab=geral&T007_Id=' . urlencode($r_T007_Id) . '&divIdAberto=' . urlencode($r_divIdAberto) . '&acao=' . urlencode($r_acao)) ,
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentAtualizarCFOP':
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
			array($content1, '/ven/ven003/form/ven003FormAtualizarCFOP/', '&tab=geral&T007_Id=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
	break;
    
    case 'ven003ContentCancelarNF':
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
			array($content1, '/ven/ven003/form/ven003FormCancelarNF/', '&tab=geral&T007_Id=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
    break;

	case 'ven003ContentInutilizarNumeracao':
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
		array($content1, '/ven/ven003/tab/ven003TabInutilizarNumeracao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentImportarXML':
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
		array($content1, '/ven/ven003/tab/ven003TabImportarXML/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentDevolucao':
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
		array($content1, '/ven/ven003/tab/ven003TabDevolucao/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentDevolucaoNotaRecebida':
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
			array($content1, '/ven/ven003/form/ven003FormDevolucaoNotaRecebida/', '&tab=geral&listaT013s=' . urlencode($r_listaT013s) . '&divIdAberto=' . urlencode($r_divIdAberto)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003contentCCe':
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
		array($content1, '/ven/ven003/tab/ven003TabCCe/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003contentIncluirCCe':
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
			array($content1, '/ven/ven003/tab/ven003TabIncluirCCe/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_T007_Id))
		);
		echo gProcessaAcoes($actions);

		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven003FormIncluirCCeBtn">Enviar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003contentIncluirEventoNFe':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		$actions = array(
			array($content1, '/ven/ven003/tab/ven003TabIncluirEventoNFe/', '&T007_Id=' . urlencode($r_T007_Id))
		);
		echo gProcessaAcoes($actions);

		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button type="submit" id="ven003FormIncluirEventoNFeBtn">Salvar rascunho</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003contentEvento112140':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		echo gProcessaAcoes(array(
			array($content1, '/ven/ven003/tab/ven003TabEvento112140/', '&T321_Id=' . urlencode($r_T321_Id))
		));
	break;

	case 'ven003contentIncluirItemEvento112140':
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		echo gProcessaAcoes(array(
			array($content1, '/ven/ven003/form/ven003FormIncluirItemEvento112140/', '&T321_Id=' . urlencode($r_T321_Id))
		));
	break;

	case 'ven003ContentProdutosConsumidor':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$divOutro = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $content2);
		$g['smarty']->assign('content1Height', '220px');
		$g['smarty']->assign('tipo', '2linhas');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven003/content/ven003ContentFormProdutosConsumidor/', '&T007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($content2) . '&divOutro=' . urlencode($divOutro)),
			array($content2, '/ven/ven003/grid/ven003GridProdutos/', '&acaoId=' . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_acaoId) . '&divReload=' . urlencode($divOutro)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentFormProdutosConsumidor':
		/*
		 * Divisão da Area em Conteúdos
		 */
		// Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
		$content1 = uniqid();
		$content2 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('content2', $r_divOutro);
		$g['smarty']->assign('content1Width', '350px');
		$g['smarty']->display('content-2columnsOrcamento.tpl');
		// O que será carregado em cada conteúdo?
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($content1, '/ven/ven003/form/ven003FormProdutosConsumidor/', '&tab=produto&T007_Id=' . urlencode($r_T007_Id) . '&divReload=' . urlencode($r_divReload) . "&divOutro=" . urldecode($r_divOutro)),
			array($r_divOutro, '/ven/ven003/outro/ven003observacoes/', '&acaoId=' . urlencode($r_T007_Id)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentTransmitirNotaFiscal':
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
			array($content1, '/ven/ven003/tab/ven003TabTransmitirNotaFiscal/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003contentPagamentosPDV':
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
			array($content1, '/ven/ven003/outro/ven003TrocoPagamentoPDV/', '&acaoId=' . urlencode($r_acaoId)),
			array($content2, '/ven/ven003/grid/ven003GridPagamentosPDV/', '&divReload=' . urlencode($content1).'&acaoId=' . urlencode($r_acaoId)),
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003contentLogCCe':
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
			array($content1, '/ven/ven003/tab/ven003TabLogCCe/', '&T199_Id=' . urlencode($r_T199_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentCorrecaoNFCe':
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
			array($content1, '/ven/ven003/tab/ven003TabCorrecaoNFCe/', '&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentCadastroCiap':
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
			array($content1, '/ven/ven003/tab/ven003TabCadastroCiap/', '&tab=geral&T007_Id=' . urlencode($r_T007_Id))
		);
		echo gProcessaAcoes($actions);
	break;

	case 'ven003ContentCadastroObservacao':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		echo gProcessaAcoes(array(
			array($content1, '/ven/ven003/tab/ven003TabCadastroObservacao/', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T007_Id=' . urlencode($T007_Id))
		));
	break;

	case 'ven003ContentCadastroObrigacaoTributaria':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$T297_Id = isset($r_T297_Id) ? $r_T297_Id : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$content1 = uniqid();
		$g['smarty']->assign('content1', $content1);
		$g['smarty']->assign('tipo', 'unico');
		echo gProcessaAcoes(array(
			array($content1, '/ven/ven003/tab/ven003TabCadastroObrigacaoTributaria/', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T297_Id=' . urlencode($T297_Id) . '&T007_Id=' . urlencode($T007_Id))
		));
	break;

}
