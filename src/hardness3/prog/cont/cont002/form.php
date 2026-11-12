<?php

namespace hardness;

use DateTime;
//Classes
require_once('bibliotecas/classes/CONTABIL.php');
$CONTABIL = new CONTABIL();

switch ($g['r'][3]) {
	case 'cont002FormLancamento':
		global $g;
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
		$form_tabela = 'T302';
		$form_campo_pk = 'T302_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cont002FormLancamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cont/cont002/form/cont002FormLancamento/';
		$form_editor = array(
			'geral' => '{"T302_Data":{"top":7,"left":51.828125,"width":152,"hidden":0},"T302_Valor":{"top":30,"left":49.203125,"width":152,"hidden":0},"T302_Credito_Debito":{"top":53,"left":3.1875,"width":159,"hidden":0},"T302_D193_Id":{"top":76,"left":45.515625,"width":159,"hidden":0},"T302_Descricao":{"top":99,"left":25.71875,"width":424,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T302_Data' => array('name' => 'T302_Data', 'label' => 'Data', 'type' => 'data'),
					'T302_Valor' => array('name' => 'T302_Valor', 'label' => 'Valor', 'type' => 'moeda'),
					'T302_Descricao' => array('name' => 'T302_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'T302_Credito_Debito' => array(
						'name' => 'T302_Credito_Debito', 'label' => 'Crédito/Débito', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Crédito', 'value' => 'C'),
							array('title' => 'Débito', 'value' => 'D')
						)
					),
					'T302_D193_Id' => array(
						'name' => 'T302_D193_Id', 'label' => 'Conta', 'type' => 'select', 'select' =>
						gGeraSelect('D193', 'D193_Id', 'D193_Nome_Conta', 'WHERE D193_Flag_Conta_Movimento = "S"')
					),
					'T302_C004_Id' => array('name' => 'T302_C004_Id', 'label' => 'T302_C004_Id','value' => $g['empresaAtual'], 'type' => 'hidden'),
				)
			)
		);
		$form = new Formulario('form-cont002FormLancamento');
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
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cont002FormLancamentoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cont002FormProcessarLancamento':
		global $g;
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cont002FormProcessaLancamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cont/cont002/form/cont002FormProcessarLancamento/';
		$form_editor = array(
			'geral' => '{"mes":{"top":7,"left":5,"width":137,"hidden":0},"ano":{"top":30,"left":5,"width":137,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'mes' => array('name' => 'mes', 'label' => 'Mês', 'type' => 'text'),
					'ano' => array('name' => 'ano', 'label' => 'Ano', 'type' => 'text'),
				)
			)
		);
		$form = new Formulario('form-cont002FormProcessaLancamento');
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
				$mes = $form->campoValorEnviado('mes');
				$ano = $form->campoValorEnviado('ano');				


				$retorno = $CONTABIL->processaLancamentos($mes, $ano);


				if ($retorno === true) {
					$alert .= gGeraAlertMsg('Operação OK', 'Lançamentos processados com sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							// Fecha a janela, recarrega o grid e abre a alteração
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {									
									{$alert}
								}, 150);
								});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
		EOT;
					
				} else {
					echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cont002FormProcessaLancamentoBtn">Processar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cont002FormCadastroEscrituracao':
		global $g;
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
		$form_tabela = 'D194';
		$form_campo_pk = 'D194_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cont002FormEscrituracaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cont/cont002/form/cont002FormCadastroEscrituracao/';
		$form_editor = array(
			'geral' => '{"D194_Descricao":{"top":7,"left":25.71875,"width":424,"hidden":0},"D194_Tabela":{"top":30,"left":42.015625,"width":424,"hidden":0},"D194_Campo_Data":{"top":53,"left":13.875,"width":424,"hidden":0},"D194_Link":{"top":76,"left":54.375,"width":424,"hidden":0},"D194_Extra":{"top":99,"left":49.8125,"height":346,"width":955,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D194_Descricao' => array('name' => 'D194_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'D194_Tabela' => array('name' => 'D194_Tabela', 'label' => 'Tabela', 'type' => 'text'),
					'D194_Campo_Data' => array('name' => 'D194_Campo_Data', 'label' => 'Campo Data', 'type' => 'text', 'uppercase' => false),
					'D194_Link' => array('name' => 'D194_Link', 'label' => 'Link', 'type' => 'text', 'uppercase' => false),
					'D194_Extra' => array('name' => 'D194_Extra', 'label' => 'Extra', 'type' => 'textarea', 'uppercase' => false, 'code' => 'sql'),					
				)
			)
		);
		$form = new Formulario('form-cont002FormEscrituracao');
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
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cont002FormEscrituracaoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	case 'cont002FormCadastroCorrelacao':
		global $g;
		$D194_Id = isset($r_D194_Id) ? $r_D194_Id : false;	
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
		$form_tabela = 'D195';
		$form_campo_pk = 'D195_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cont002FormCorrelacaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cont/cont002/form/cont002FormCadastroCorrelacao/';
		$form_editor = array(
			'geral' => '{"D195_Descricao":{"top":7,"left":57.71875,"width":504,"hidden":0},"D195_Campo":{"top":30,"left":71.375,"width":504,"hidden":0},"D195_Conta_Debito":{"top":53,"left":44.15625,"width":489,"hidden":0},"D195_Conta_Credito":{"top":76,"left":40.5625,"width":489,"hidden":0},"D195_Descricao_Contabil":{"top":99,"left":14.40625,"width":504,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D195_Descricao' => array('name' => 'D195_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'D195_Campo' => array('name' => 'D195_Campo', 'label' => 'Campo', 'type' => 'text','uppercase' => false),				
					'D195_Conta_Debito' => array('name' => 'D195_Conta_Debito', 'label' => 'Conta Débito', 'type' => 'pesquisa', 
						'pesquisa' => array('D193','D193_Id','D193_Codigo,D193_Nome_Conta', 'where D193_Flag_Conta_Movimento = "S" order by D193_Codigo')
					),
					'D195_Conta_Credito' => array('name' => 'D195_Conta_Credito', 'label' => 'Conta Crédito', 'type' => 'pesquisa', 
						'pesquisa' => array('D193','D193_Id','D193_Codigo,D193_Nome_Conta', 'where D193_Flag_Conta_Movimento = "S" order by D193_Codigo')
					),					
					'D195_Descricao_Contabil' => array('name' => 'D195_Descricao_Contabil', 'label' => 'Descrição Contábil', 'type' => 'text','uppercase' => false),
					'D195_D194_Id' => array('name' => 'D195_D194_Id', 'label' => 'D195_D194_Id','value' => $D194_Id, 'type' => 'hidden'),
				)
			)
		);
		$form = new Formulario('form-cont002FormCorrelacao');
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
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cont002FormCorrelacaoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
}
