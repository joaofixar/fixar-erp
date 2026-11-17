<?php
namespace hardness;

set_time_limit(0);

switch($g['r'][3]){
	case 'contentOpcoes':
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
			array($content1, '/sistema/funcoes/editarColuna/tabOpcoes/')
		);
		echo gProcessaAcoes($actions);
	break;
	
	case 'tabOpcoes':
		$tipo = 'texto';
		if (empty($r_tipo)) {
			// detectar tipo automaticamente
			$campo = explode('_',$r_campo);
			$res = mysql_query("SHOW COLUMNS FROM {$campo[0]} LIKE '%$r_campo%'");
			$mRes = mysql_fetch_array($res);
			if(strpos(" " . strtolower($mRes['Type']), "dec") !== false) {
				$tipo = 'moeda';
			} else if ($mRes['Type'] == 'date' || $mRes['Type'] == 'datetime') {
				$tipo = 'data';
			}
		} else {
			$tipo = $r_tipo;
		}

		// Definição das Tabs
		$tabs = array();
		if ($tipo == 'texto' || $tipo == 'select') {
			$tabsList = array(
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesValor/', 'Valor', '&tab=geral'),
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesFormula/', 'Fórmula', '&tab=geral'),
			);
		} else if ($tipo == 'moeda') {
			$tabsList = array(
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesValor/', 'Valor', '&tab=geral&tipo=' . $tipo),
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesDesconto/', '% Desconto', '&tab=geral'),
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesAcresicmo/', '% Acréscimo', '&tab=geral'),
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesFormula/', 'Fórmula', '&tab=geral'),
			);
		} else if ($tipo == 'data') {
			$tabsList = array(
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesData/', 'Data', '&tab=geral'),
				array(uniqid(), '/sistema/funcoes/editarColuna/formOpcoesFormula/', 'Fórmula', '&tab=geral'),
			);
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'XformOpcoesValor':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesValorBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesValor/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Valor":{"top":30,"left":17,"width":125,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Atencao' => array('name' => 'Atencao', 'label' => 'O novo valor será aplicado a todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
		if($r_tipo == 'moeda') {
			$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'text', 'reject' => true, 'uppercase' => false, 'style' => 'width: 400px;');
		} else if($r_tipo == 'select') {
			$r_extra = unserialize(base64_decode($r_extra));
			if(is_array($r_extra[0])){
				$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'select', 'reject' => true, 'style' => 'width: 400px;', 'select' => $r_extra);
			} else {
				$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'select', 'reject' => true, 'style' => 'width: 400px;', 'select' => call_user_func_array('\\hardness\\gGeraSelect', $r_extra));
			}
		} else {
			$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'textarea', 'reject' => true, 'uppercase' => false, 'style' => 'width: 446px; height: 62px;');
		}
        $form = new Formulario('form-editarColunaOpcoesForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Valor');
				$novoValor = urlencode($novoValor);
				$campo = urlencode($r_campo);
				$sql = urlencode($r_sql);
				$tipo = urlencode($r_tipo);
				$extra = urlencode($r_extra);
				//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
				$alert = '';
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();

								$.ajax({
									url: '/sistema/funcoes/editarColuna/update/',
									type: 'POST',
									dataType: 'jsonp',
									data: {
										"ajax": 'true',
										"campo": '{$campo}',
										"valor": '{$novoValor}',
										"sql": '{$sql}',
										"operacao": 'valor',
										"tipo": '{$tipo}',
										"extra": '{$extra}',
										"triggerPHP": '{$r_triggerPHP}',
									}
								}).done(function(request, temp1, temp2){
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												{$alert}
												divRefresh('{$g['divIdParent']}', true);
											}, 150);
										});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} else {
										dialogConfirm('Erro!', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						}
						dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
					} </script></div>
EOT;
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesValorBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'formOpcoesValor':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesValorBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesValor/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Valor":{"top":30,"left":17,"width":125,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Atencao' => array('name' => 'Atencao', 'label' => 'O novo valor será aplicado a todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
		if($r_tipo == 'moeda') {
			$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'text', 'reject' => true, 'uppercase' => false, 'style' => 'width: 400px;');
		} else if($r_tipo == 'select') {
			$r_extra = unserialize(base64_decode($r_extra));
			if(is_array($r_extra[0])){
				$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'select', 'reject' => true, 'style' => 'width: 400px;', 'select' => $r_extra);
			} else {
				$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'select', 'reject' => true, 'style' => 'width: 400px;', 'select' => call_user_func_array('\\hardness\\gGeraSelect', $r_extra));
			}
		} else {
			$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Novo Valor', 'type' => 'textarea', 'reject' => true, 'uppercase' => false, 'style' => 'width: 446px; height: 62px;');
		}
        $form = new Formulario('form-editarColunaOpcoesForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Valor');
				$novoValor = urlencode($novoValor);
				$campo = urlencode($r_campo);
				$sql = urlencode($r_sql);
				$tipo = urlencode($r_tipo);
				$extra = urlencode($r_extra);
				//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
				$alert = '';
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b>0% processado</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								processoOpcoesValorEtapas(idProgressBar,1);
								function processoOpcoesValorEtapas(idProgressBar,inicio){

									$.ajax({
										url: '/sistema/funcoes/editarColuna/updateEtapas/',
										type: 'POST',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'inicio': inicio,
											'campo': '{$campo}',
											'valor': '{$novoValor}',
											'sql': '{$sql}',
											'operacao': 'valor',
											'tipo': '{$tipo}',
											'extra': '{$extra}',
											'triggerPHP': '{$r_triggerPHP}',
										}
									}).done(function(request, temp1, temp2){
										if (request.concluido == 0) {
											$('#progressBar').html('<b>' + request.percentual + '% processo</b>');
											processoOpcoesValorEtapas(idProgressBar,0);
										}else{
											if (request.code) {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												//dialogConfirm('Processo','<b><font color="green">' + request.percentual + '<b>%</b><b> processado &#10004;');
												//divRefresh('{$g['divId']}', true);
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
														divRefresh('{$g['divIdParent']}', true);
													}, 150);
												});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
														
											} else {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												dialogConfirm('Erro!', request.data);
												divRefresh('{$g['divId']}', true);
											}
										} 
									});	                    
								}
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						}
						dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
					} </script></div>
EOT;
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesValorBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'formOpcoesDesconto':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesDescontoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesDesconto/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Valor":{"top":30,"left":17,"width":125,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Valor' => array('name' => 'Valor', 'label' => '% Desconto', 'type' => 'text', 'reject' => true, 'uppercase' => false),
					'Atencao' => array('name' => 'Atencao', 'label' => 'O percentual será subtraído em todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
        $form = new Formulario('form-editarColunaOpcoesForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Valor');
				if (empty($novoValor) && $novoValor != '0') {
					echo gGeraAlertMsg('Erro', 'Valor não pode ser vazio!');
				} else {
					$novoValor = urlencode($novoValor);
					$campo = urlencode($r_campo);
					$sql = urlencode($r_sql);
					$tipo = urlencode($r_tipo);
					$extra = urlencode($r_extra);
					//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
					$alert = '';
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b>0% processado</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								processoOpcoesValorEtapas(idProgressBar,1);
								function processoOpcoesValorEtapas(idProgressBar,inicio){

									$.ajax({
										url: '/sistema/funcoes/editarColuna/updateEtapas/',
										type: 'POST',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'inicio': inicio,
											'campo':'{$campo}',
											'valor':'{$novoValor}',
											'sql':'{$sql}',
											'operacao':'desconto',
											'tipo':'{$tipo}',
											'extra':'{$extra}',
											'triggerPHP':'{$r_triggerPHP}'
										}
									}).done(function(request, temp1, temp2){
										if (request.concluido == 0) {
											$('#progressBar').html('<b>' + request.percentual + '% processo</b>');
											processoOpcoesValorEtapas(idProgressBar,0);
										}else{
											if (request.code) {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												//dialogConfirm('Processo','<b><font color="green">' + request.percentual + '<b>%</b><b> processado &#10004;');
												//divRefresh('{$g['divId']}', true);
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
														divRefresh('{$g['divIdParent']}', true);
													}, 150);
												});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
														
											} else {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												dialogConfirm('Erro!', request.data);
												divRefresh('{$g['divId']}', true);
											}
										} 
									});	                    
								}
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						}
						dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
					} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesDescontoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'formOpcoesAcresicmo':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesAcrescimoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesAcresicmo/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Valor":{"top":30,"left":17,"width":125,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Valor' => array('name' => 'Valor', 'label' => '% Acréscimo', 'type' => 'text', 'reject' => true, 'uppercase' => false),
					'Atencao' => array('name' => 'Atencao', 'label' => 'O percentual será acrescido em todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
        $form = new Formulario('form-editarColunaOpcoesForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Valor');
				if (empty($novoValor) && $novoValor != '0') {
					echo gGeraAlertMsg('Erro', 'Valor não pode ser vazio!');
				} else {
					$novoValor = urlencode($novoValor);
					$campo = urlencode($r_campo);
					$sql = urlencode($r_sql);
					$tipo = urlencode($r_tipo);
					$extra = urlencode($r_extra);
					//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
					$alert = '';
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b>0% processado</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								processoOpcoesValorEtapas(idProgressBar,1);
								function processoOpcoesValorEtapas(idProgressBar,inicio){

									$.ajax({
										url: '/sistema/funcoes/editarColuna/updateEtapas/',
										type: 'POST',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'inicio': inicio,
											'campo': '{$campo}',
											'valor': '{$novoValor}',
											'sql': '{$sql}',
											'operacao': 'acrescimo',
											'tipo': '{$tipo}',
											'extra': '{$extra}',
											'triggerPHP': '{$r_triggerPHP}',
										}
									}).done(function(request, temp1, temp2){
										if (request.concluido == 0) {
											$('#progressBar').html('<b>' + request.percentual + '% processo</b>');
											processoOpcoesValorEtapas(idProgressBar,0);
										}else{
											if (request.code) {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												//dialogConfirm('Processo','<b><font color="green">' + request.percentual + '<b>%</b><b> processado &#10004;');
												//divRefresh('{$g['divId']}', true);
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
														divRefresh('{$g['divIdParent']}', true);
													}, 150);
												});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
														
											} else {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												dialogConfirm('Erro!', request.data);
												divRefresh('{$g['divId']}', true);
											}
										} 
									});	                    
								}
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						}
						dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
					} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesAcrescimoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'formOpcoesFormula':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesAcrescimoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesFormula/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Formula":{"top":30,"left":18.28125,"height":62,"width":472,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Formula' => array('name' => 'Formula', 'label' => 'Fórmula', 'type' => 'textarea', 'reject' => true, 'uppercase' => false),
					'Atencao' => array('name' => 'Atencao', 'label' => 'A fórmula será aplicada em todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
        $form = new Formulario('form-editarColunaOpcoesForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Formula');
				if (empty($novoValor) && $novoValor != '0') {
					echo gGeraAlertMsg('Erro', 'Fórmula não pode ser vazia!');
				} else {
					$novoValor = urlencode($novoValor);
					$campo = urlencode($r_campo);
					$sql = urlencode($r_sql);
					$tipo = urlencode($r_tipo);
					$extra = urlencode($r_extra);
					//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
					$alert = '';
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b>0% processado</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								processoOpcoesValorEtapas(idProgressBar,1);
								function processoOpcoesValorEtapas(idProgressBar,inicio){

									$.ajax({
										url: '/sistema/funcoes/editarColuna/updateEtapas/',
										type: 'POST',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'inicio': inicio,
											'campo': '{$campo}',
											'valor': '{$novoValor}',
											'sql': '{$sql}',
											'operacao': 'formula',
											'tipo': '{$tipo}',
											'extra': '{$extra}',
											'triggerPHP': '{$r_triggerPHP}'
										}
									}).done(function(request, temp1, temp2){
										if (request.concluido == 0) {
											$('#progressBar').html('<b>' + request.percentual + '% processo</b>');
											processoOpcoesValorEtapas(idProgressBar,0);
										}else{
											if (request.code) {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												//dialogConfirm('Processo','<b><font color="green">' + request.percentual + '<b>%</b><b> processado &#10004;');
												//divRefresh('{$g['divId']}', true);
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
														divRefresh('{$g['divIdParent']}', true);
													}, 150);
												});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
														
											} else {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												dialogConfirm('Erro!', request.data);
												divRefresh('{$g['divId']}', true);
											}
										} 
									});	                    
								}
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						}
						dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
					} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesAcrescimoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'formOpcoesData':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'editarColunaOpcoesDataBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/sistema/funcoes/editarColuna/formOpcoesData/';
		// Editor
		$form_editor = array(
			'geral' => '{"Atencao":{"top":4,"left":12,"width":null,"hidden":1},"Valor":{"top":30,"left":17,"width":80,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Atencao' => array('name' => 'Atencao', 'label' => 'A nova data será aplicada a todos os registros filtrados!', 'type' => 'titulo', 'reject' => true, 'style' => 'color: red;'),
				)
			)
		);
		$formArray['campos']['geral']['Valor'] = array('name' => 'Valor', 'label' => 'Nova Data', 'type' => 'data', 'reject' => true);
        $form = new Formulario('form-editarColunaOpcoesDataForm');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$novoValor = $form->campoValorEnviado('Valor');
				if (empty($novoValor)) {
					echo gGeraAlertMsg('Erro', 'Valor não pode ser vazio!');
				} else {
					$novoValor = urlencode($novoValor);
					$campo = urlencode($r_campo);
					$sql = urlencode($r_sql);
					$tipo = urlencode($r_tipo);
					$extra = urlencode($r_extra);
					//$alert = gGeraAlertMsg('Operação OK', 'Valores filtrados da coluna foram alterados!', false);
					$alert = '';
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var buttons = {
								"Sim": function() {
									$('#{$g['divId']}').showLoading();

									$.ajax({
										url: '/sistema/funcoes/editarColuna/update/',
										type: 'POST',
										dataType: 'jsonp',
										data: {
											'ajax': 'true',
											'campo': '{$campo}',
											'valor': '{$novoValor}',
											'sql': '{$sql}',
											'operacao': 'valor',
											'tipo': 'data',
											'extra': '{$extra}',
											'triggerPHP': '{$r_triggerPHP}',
										}
									}).done(function(request, temp1, temp2){
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
													{$alert}
													divRefresh('{$g['divIdParent']}', true);
												}, 150);
	  										});
											$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										} else {
											dialogConfirm('Erro!', request.data);
										}
									});
									$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							}
							dialogConfirm('Atenção!', '<span style="font-weight: bold; font-size: 1.6em; color: red">ATENÇÃO!</span><br /><br />Esta operação irá mudar os valores de todos os registros filtrados.<br />Continuar?', buttons);
						} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="editarColunaOpcoesDataBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'update':
		$r_campo 	= urldecode($r_campo);
		$r_sql   	= urldecode($r_sql);
		$r_valor 	= urldecode($r_valor);
		$r_extra 	= urldecode($r_extra);
		$r_usuario 	= urldecode($r_usuario);

		$resposta = array('code' => true, 'data' => array());
		if ($r_tipo != 'flag') {
			$r_sql = urldecode($r_sql);
		}
		$r_valor = mysql_real_escape_string($r_valor);
		/*echo $r_valor;
		echo "<br />";
		echo $r_campo;
		echo "<br />";
		echo urldecode($r_sql);
		die();*/

		$triggerPHP = ($r_triggerPHP == 'false') ? false : true;
		
		// Tabela do campo
		$tabela = explode('_', $r_campo);
		$tabela = $tabela[0];
        // Tabelas auxiliares possuem tabela principal
        $tabelaPrincipal = '';
        if (preg_match('/^[A-Z][0-9]{3}A$/i', $tabela)) {
            $tabelaPrincipal = rtrim($tabela, 'A');
        }

		// Caso seja um campo DECIMAL, aceita tanto ponto quanto vírgula
		if ($r_tipo == 'moeda') {
			$r_valor = str_replace(',', '.', $r_valor);
		}
		if ($r_tipo == 'data') {
			$r_valor = gCorrigeDataInverte($r_valor);
		}

		// Garante que teremos o ID desta tabela na primeira posição
		$r_sql = preg_replace('/^SELECT/i', "SELECT {$tabela}_Id, ", $r_sql);
		// Retirna o LIMIT
		$r_sql = preg_split('/limit[^limit]+$/i', $r_sql);
		$r_sql = $r_sql[0];
		// Pega os left joins
		$leftjoin = '';
		$lj = preg_split('/LEFT JOIN/i', $r_sql, 2);
		if (isset($lj[1])) {
			$lj = preg_split('/WHERE/i', $lj[1], 2);
			$leftjoin = 'LEFT JOIN ' . str_replace("\n", ' ', $lj[0]);
		}
		$leftjoin = ''; // por enquanto está vazio para todos
		// Tabela do SQL (comparar se tabela do campo é igual ao do SQL, se sim, pode usar o leftjoin)
		/*preg_match('/FROM ([^ ]+)/i', $r_sql, $matches);
		print_r($matches);
		die();*/
		//if (!isset($matches[1])) {
		//	$resposta = array('code' => false, 'data' => 'Erro identificando tabela do SQL.');
		//} else {
			//$tabelaSQL = $matches[1];
			// Percorre o SELECT
		//}
		if ($r_operacao=='acrescimo'){

			$r_valor = gCorrigeNumeroInverte($r_valor);

			$res = mysql_query($r_sql);
			while ($row = mysql_fetch_array($res)) {
				$id = $row["{$tabela}_Id"];
				if (empty($id)) { continue; } // por segurança
				
				$sql = "UPDATE {$tabela} {$leftjoin} SET {$r_campo} = ({$r_campo} + ({$r_campo} * ($r_valor/100))) WHERE {$tabela}_Id = '{$id}';";

				//echo $sql . "<br />";
				mysql_query($sql, $triggerPHP);
			}
		} else if($r_operacao=='desconto'){

			$r_valor = gCorrigeNumeroInverte($r_valor);

			$res = mysql_query($r_sql);
			while ($row = mysql_fetch_array($res)) {
				$id = $row["{$tabela}_Id"];
				if (empty($id)) { continue; } // por segurança

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$r_campo} = ({$r_campo} - ({$r_campo} * ($r_valor/100))) WHERE {$tabela}_Id = '{$id}';";

				//echo $sql . "<br />";
				mysql_query($sql, $triggerPHP);
			}	

		} else if($r_operacao=='formula'){

			//$r_valor = str_replace(',', '.', $r_valor);
			
			$res = mysql_query($r_sql);
			while ($row = mysql_fetch_array($res)) {
				$id = $row["{$tabela}_Id"];
				if (empty($id)) { continue; } // por segurança

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$r_campo} = (" . $r_valor . ") WHERE {$tabela}_Id = '{$id}';";

				log("UPDATE FORMULA -> " . $sql);

				//echo $sql . "<br />";
				mysql_query($sql, $triggerPHP);
			}	

		} else {
			$res = mysql_query($r_sql);
			while ($row = mysql_fetch_array($res)) {
                $tabelaPk = $tabelaPrincipal ? $tabelaPrincipal : $tabela;
				$id = $row["{$tabelaPk}_Id"];
				if (empty($id)) { continue; } // por segurança
				if ($r_valor[0] == '=') {
					$valor = trim($r_valor, '=');
				} else {
					$valor = "'" . $r_valor . "'";
				}
				if (!empty($r_usuario)) {				
					// verifica se já não pertence a outro usuário
					//log("if ({$row[$r_campo]} == 'S' && {$row[$r_usuario]} > 0 && {$row[$r_usuario]} != {$g['usuarioAtual']})");
					if ($row[$r_campo] == 'S' && $row[$r_usuario] > 0 && $row[$r_usuario] != $g['usuarioAtual']) { continue; }

					$usuario = ($valor == "'N'") ? 0 : $g['usuarioAtual'];

					// Ajusta UPDATE do usuário para suportar campo JSON	
					if (strpos($r_usuario, '->') !== false) 
					{
						$chave = explode('->', $r_usuario);
						$campo = trim($chave[0]);
						$campoJson = trim($chave[1]);
						// UPDATE
						$setUpdateUsuario = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$usuario})";						
						// INSERT
						$novo_r_campo = $campo;
						$usuario = "JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$usuario})";
					}
					else
					{						
						// UPDATE
						$setUpdateUsuario = "{$r_usuario} = {$usuario}";
						// INSERT
						$novo_r_campo = $r_usuario;
					}

					// Verifica se a modificação foi na estendida
					if (!empty($tabelaPrincipal))
					{
						$sql = "INSERT INTO {$tabela} {$leftjoin} ({$tabela}_{$tabelaPrincipal}_Id, {$novo_r_campo}) VALUES ('{$id}', {$usuario}) ON DUPLICATE KEY UPDATE {$setUpdateUsuario}";
					}   
					else
					{
						$sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdateUsuario} WHERE {$tabela}_Id = '{$id}'";
					}

					log($sql);
					mysql_query($sql, $triggerPHP);
				}

				// Ajusta UPDATE para campo do tipo JSON		
				if (strpos($r_campo, '->') !== false) 
				{
					$chave = explode('->', $r_campo);
					$campo = trim($chave[0]);
					$campoJson = trim($chave[1]);
					// UPDATE
					$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', $valor)";
					// INSERT
					$novo_r_campo = $campo;
					$valor = "JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$valor})";
				}
				else
				{
					// UPDATE
					$setUpdate = "{$r_campo} = {$valor}";
					// INSERT
					$novo_r_campo = $r_campo;
				}

                if ($tabelaPrincipal) {
                    $sql = "INSERT INTO {$tabela} {$leftjoin} ({$tabela}_{$tabelaPrincipal}_Id, {$novo_r_campo}) VALUES ('{$id}', {$valor}) ON DUPLICATE KEY UPDATE {$setUpdate}";
//                    if (strlen($valor) == 0 || $valor == "''") {
//                        $sql = "DELETE FROM {$tabela} WHERE {$tabela}_{$tabelaPrincipal}_Id = '{$id}'";
//                    }
                } else {					
                    $sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdate} WHERE {$tabela}_Id = '{$id}';";
                }

				log($sql);
				mysql_query($sql, $triggerPHP);
			}

		}
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'updateEtapas':
		global $g;

		$r_sql   = urldecode($r_sql);
		$r_valor = urldecode($r_valor);
		$r_extra = urldecode($r_extra);
		// O $r_campo é encodado duas vezes | Ex.: T005A_JSON->meses_compra => T005A_JSON-%253Emeses_compra  
		$r_campo = urldecode(urldecode($r_campo)); 		
		
		$resposta = array('code' => true, 'data' => array());

        if ($r_inicio==1){

			if ($r_tipo != 'flag') {
				$r_sql = urldecode($r_sql);
			}
			$r_valor = mysql_real_escape_string($r_valor);
			/*echo $r_valor;
			echo "<br />";
			echo $r_campo;
			echo "<br />";
			echo urldecode($r_sql);
			die();*/
	
			$triggerPHP = ($r_triggerPHP == 'false') ? false : true;
			
			// Tabela do campo
			$tabela = explode('_', $r_campo);
			$tabela = $tabela[0];
			// Tabelas auxiliares possuem tabela principal
			$tabelaPrincipal = '';
			if (preg_match('/^[A-Z][0-9]{3}A$/i', $tabela)) {
				$tabelaPrincipal = rtrim($tabela, 'A');
			}

			//$tabela = $tabelaPrincipal ? $tabelaPrincipal : $tabela;

			// Caso seja um campo DECIMAL, aceita tanto ponto quanto vírgula
			if ($r_tipo == 'moeda') {
				$r_valor = gCorrigeNumeroInverte($r_valor);
			}
			if ($r_tipo == 'data') {
				$r_valor = gCorrigeDataInverte($r_valor);
			}
	
			// Garante que teremos o ID desta tabela na primeira posição
			$r_sql = preg_replace('/^SELECT/i', "SELECT {$tabela}_Id, ", $r_sql);
			// Retirna o LIMIT
			$r_sql = preg_split('/limit[^limit]+$/i', $r_sql);
			$r_sql = $r_sql[0];
			// Pega os left joins
			$leftjoin = '';
			$lj = preg_split('/LEFT JOIN/i', $r_sql, 2);
			if (isset($lj[1])) {
				$lj = preg_split('/WHERE/i', $lj[1], 2);
				$leftjoin = 'LEFT JOIN ' . str_replace("\n", ' ', $lj[0]);
			}
			$leftjoin = ''; // por enquanto está vazio para todos
			// Tabela do SQL (comparar se tabela do campo é igual ao do SQL, se sim, pode usar o leftjoin)
			/*preg_match('/FROM ([^ ]+)/i', $r_sql, $matches);
			print_r($matches);
			die();*/
			//if (!isset($matches[1])) {
			//	$resposta = array('code' => false, 'data' => 'Erro identificando tabela do SQL.');
			//} else {
				//$tabelaSQL = $matches[1];
				// Percorre o SELECT
			//}

			log("Sql cache " . $r_sql);
	
			$sqlCache = mysql_query($r_sql);
        	$count=0;
			$cache = array();
        	while($rowCache = mysql_fetch_array($sqlCache)){
			    $cache[$count]['id'] = $rowCache["{$tabela}_Id"];
			    $cache[$count]['idPrincipal'] = $rowCache["{$tabelaPrincipal}_Id"];
			    $cache[$count]['usuario'] = $rowCache[$r_usuario];
			    $cache[$count]['campo'] = $rowCache[$r_campo];
				$count++;
			}
			$resposta['valor'] = $r_valor;
			$resposta['operacao'] = $r_operacao;
			$resposta['tabela'] = $tabela;
			$resposta['leftjoin'] = $leftjoin;
			$resposta['campo'] = $r_campo;
			$resposta['triggerPHP'] = $triggerPHP;
			$resposta['tabelaPrincipal'] = $tabelaPrincipal;
			$resposta['tabelaPk'] = $tabelaPk;
			$resposta['usuario'] = $r_usuario;

			$resposta['totalRegistros'] = count($cache);
			$resposta['etapas'] = round($resposta['totalRegistros'] / 1);
	    	$resposta['etapas'] = ($resposta['etapas'] < 1) ? 1 : $resposta['etapas'];
	    	$resposta['registrosEtapa'] = round( $resposta['totalRegistros'] / $resposta['etapas'] );
			$resposta['etapa'] = 1;
	        $resposta['ok'] = 0;
    	    $resposta['erro'] = 0;
        	$resposta['concluido'] = 0;
			//apc_delete("cacheEditarColuna-{$g['usuarioAtual']}");
			//apc_delete("cacheEditarColuna-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEditarColuna-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheEditarColuna-resposta-{$g['usuarioAtual']}");
	        //$retorno = apc_store("cacheEditarColuna-{$g['usuarioAtual']}", serialize($cache));
			$retorno = file_put_contents("/tmp/cacheEditarColuna-{$g['usuarioAtual']}", serialize($cache));

	        if(!$retorno){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache';
	        }else{
	        	$resposta['code'] = true;
	        }
	    } else {
			//$cache = unserialize(apc_fetch("cacheEditarColuna-{$g['usuarioAtual']}"));
			//$resposta = unserialize(apc_fetch("cacheEditarColuna-resposta-{$g['usuarioAtual']}"));
			$cache = unserialize(file_get_contents("/tmp/cacheEditarColuna-{$g['usuarioAtual']}"));
			$resposta = unserialize(file_get_contents("/tmp/cacheEditarColuna-resposta-{$g['usuarioAtual']}"));
	        if(!$resposta){
	        	$resposta['code'] = false;
	        	$resposta['data'] = 'Erro ao criar variável no cache resposta';
	        }else{
	        	$resposta['code'] = true;
	        }
	    }
		
		$resposta['limit1'] = floor( ( $resposta['registrosEtapa'] * $resposta['etapa'] ) - $resposta['registrosEtapa']);

    	if($resposta['etapa'] >= $resposta['etapas']){
    		$resposta['limit2'] += ceil($resposta['registrosEtapa'] + ($resposta['totalRegistros'] - ($resposta['limit1'] + $resposta['registrosEtapa'])));
    	} else {
    		$resposta['limit2'] += $resposta['registrosEtapa'] ;
    	}

        $resposta['percentual'] = round((100/$resposta['etapas']) * $resposta['etapa']);

		if ($resposta['percentual']>100){
			$resposta['percentual'] = 100;
		}

        $ok = 0;
    	$erro = 0;
    	$erros ="";

		$r_operacao      = $resposta['operacao'];
 		$r_valor         = $resposta['valor'];
		$tabela          = $resposta['tabela'];
		$r_campo         = $resposta['campo'];
		$leftjoin        = $resposta['leftJoin'];
		$triggerPHP      = $resposta['triggerPHP'];
		$tabelaPrincipal = $resposta['tabelaPrincipal'];
		$tabelaPk        = $resposta['tabelaPk'];
		$r_usuario       = $resposta['usuario'];

        for ($i=$resposta['limit1']; $i < $resposta['limit2'] ; $i++) { 

			$id = $cache[$i]['id'];
			$idPrincipal = $cache[$i]['idPrincipal'];
			
			$row = array();
			$row[$r_campo] = $cache[$i]['campo'];
			$row[$r_usuario] = $cache[$i]['usuario'];

			if ($r_operacao=='acrescimo'){

				$r_valor = gCorrigeNumeroInverte($r_valor);
	
				if (empty($id)) { continue; } // por segurança
				
				// Ajusta UPDATE para campo do tipo JSON com operador de 'acrescimo'		
				if (strpos($r_campo, '->') !== false) 
				{
					$chave = explode('->', $r_campo);
					$campo = trim($chave[0]);
					$campoJson = trim($chave[1]);
					$campoJsonValor = "IFNULL(JSON_UNQUOTE(JSON_EXTRACT({$campo}, '$.{$campoJson}')), 0)";
					$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', ({$campoJsonValor} + ({$campoJsonValor} * ({$r_valor} / 100))))";
				}
				else
				{
					$setUpdate = "{$r_campo} = ({$r_campo} + ({$r_campo} * ({$r_valor} / 100)))";
				}

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdate} WHERE {$tabela}_Id = '{$id}';";

				//echo $sql . "<br />";
				mysql_query($sql, $triggerPHP);

			} else if($r_operacao=='desconto'){
	
				$r_valor = gCorrigeNumeroInverte($r_valor);
	
				$res = mysql_query($r_sql);

				if (empty($id)) { continue; } // por segurança

				// Ajusta UPDATE para campo do tipo JSON com operador de 'desconto'		
				if (strpos($r_campo, '->') !== false) 
				{
					$chave = explode('->', $r_campo);
					$campo = trim($chave[0]);
					$campoJson = trim($chave[1]);
					$campoJsonValor = "IFNULL(JSON_UNQUOTE(JSON_EXTRACT({$campo}, '$.{$campoJson}')), 0)";
					$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', ({$campoJsonValor} - ({$campoJsonValor} * ({$r_valor} / 100))))";
				}
				else
				{
					$setUpdate = "{$r_campo} = ({$r_campo} - ({$r_campo} * ({$r_valor} / 100)))";
				}

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdate} WHERE {$tabela}_Id = '{$id}';";

				//echo $sql . "<br />";
				mysql_query($sql, $triggerPHP);
	
			} else if($r_operacao=='formula'){
	
				if (empty($id)) { continue; } // por segurança
			
				// Formata e substitui todos os campos JSON informados no $r_valor
				$padrao = '/([^\s]+)->/'; // Encontra todos os campos antes do '->'
				preg_match_all($padrao, $r_valor, $matches);
				$j = 0;
				foreach ($matches[1] as $campo_r_valor) 
				{
					$j++;
					$camposJSON_r_valor[$j]['banco'] = $campo_r_valor;
				}

				$padrao = '/->([^\s]+)\s?/'; // Encontra todos os campos depois do '->'
				preg_match_all($padrao, $r_valor, $matches);
				$j = 0;
				foreach ($matches[1] as $campo_r_valor) 
				{
					$j++;
					$camposJSON_r_valor[$j]['json'] = $campo_r_valor;
				}
				
				if (!empty($camposJSON_r_valor))
				{
					foreach ($camposJSON_r_valor as $campo_r_valor)
					{
						$campoJSON_query = "IFNULL(JSON_UNQUOTE(JSON_EXTRACT({$campo_r_valor['banco']}, '$.{$campo_r_valor['json']}')), 0)";
						$r_valor = str_replace($campo_r_valor['banco'] . '->' . $campo_r_valor['json'], $campoJSON_query, $r_valor);
					}
				}		

				// Ajusta UPDATE para campo do tipo JSON com operador de 'formula'		
				if (strpos($r_campo, '->') !== false) 
				{
					$chave = explode('->', $r_campo);
					$campo = trim($chave[0]);
					$campoJson = trim($chave[1]);			
					$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', ({$r_valor}))";
				}
				else
				{
					$setUpdate = "{$r_campo} = ({$r_valor})";
				}

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdate} WHERE {$tabela}_Id = '{$id}';";

				mysql_query($sql, $triggerPHP);
	
			} else {

				if ($r_valor[0] == '=') {
					$valor = trim($r_valor, '=');
				} else {
					$valor = "'" . $r_valor . "'";
				}

				if (empty($id)) { 
					if ($tabelaPrincipal) { 

						// Ajusta UPDATE e INSERT para campo do tipo JSON sem operador	
						if (strpos($r_campo, '->') !== false) 
						{							
							$chave = explode('->', $r_campo);
							$campo = trim($chave[0]);
							$campoJson = trim($chave[1]);
							// UPDATE
							$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$valor})";
							// INSERT
							$r_campo = $campo;
							$valor = "JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$valor})";
						}
						else
						{
							// UPDATE
							$setUpdate = "{$r_campo} = '{$r_valor}'";
						}

						$sql = "INSERT INTO {$tabela} ({$tabela}_{$tabelaPrincipal}_Id, {$r_campo}) VALUES ('{$idPrincipal}', {$valor}) ON DUPLICATE KEY UPDATE {$setUpdate}";
						mysql_query($sql, $triggerPHP);

						log($sql);
					}						
					continue; 
				} 

				if (isset($r_usuario)) {
					// verifica se já não pertence a outro usuário
					if ($row[$r_campo] == 'S' && $row[$r_usuario] > 0 && $row[$r_usuario] != $g['usuarioAtual']) { continue; }
					$sql = "UPDATE {$tabela} {$leftjoin} SET {$r_usuario} = '{$g['usuarioAtual']}' WHERE {$tabela}_Id = '{$id}';";
					mysql_query($sql, $triggerPHP);
				}

				// Ajusta UPDATE para campo do tipo JSON sem operador	
				if (strpos($r_campo, '->') !== false) 
				{
					$chave = explode('->', $r_campo);
					$campo = trim($chave[0]);
					$campoJson = trim($chave[1]);
					$setUpdate = "{$campo} = JSON_SET(COALESCE({$campo}, '{}'), '$.{$campoJson}', {$valor})";
				}
				else
				{
					$setUpdate = "{$r_campo} = {$valor}";
				}

				$sql = "UPDATE {$tabela} {$leftjoin} SET {$setUpdate} WHERE {$tabela}_Id = '{$id}';";

				mysql_query($sql, $triggerPHP);
			}

			$erro = mysql_error();

			log("[{$i}] updateEtapas: " . $sql);
	
   	        if(!empty($erro)) {
       	     	$erro++;
           	 	$erros .= $erro ." - ". $retorno."<br />";
           	}else{
           		$ok++;
           	}

		}

		$resposta['data'] = '';        
        $resposta['ok'] += $ok;
        $resposta['erro'] += $erro;
        $resposta['erros'] .= $erros;

		if($resposta['etapa'] > $resposta['etapas']){
			//apc_delete("cacheEditarColuna-{$g['usuarioAtual']}");
			//apc_delete("cacheEditarColuna-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEditarColuna-{$g['usuarioAtual']}");
        	@unlink("/tmp/cacheEditarColuna-resposta-{$g['usuarioAtual']}");
			$resposta['concluido'] = 1;
		} else {
	        //apc_delete("cacheEditarColuna-resposta-{$g['usuarioAtual']}");
			@unlink("/tmp/cacheEditarColuna-resposta-{$g['usuarioAtual']}");
	        $resposta['etapa'] = $resposta['etapa'] + 1;
	        //apc_store("cacheEditarColuna-resposta-{$g['usuarioAtual']}", serialize($resposta));
			file_put_contents("/tmp/cacheEditarColuna-resposta-{$g['usuarioAtual']}", serialize($resposta));
		}

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";		

	break;

}

?>