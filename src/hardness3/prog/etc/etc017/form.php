<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'etc017FormInserirAgendaTarefa':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		$T182_Id = (isset($r_T182_Id)) ? $r_T182_Id : false;

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc017FormInserirAgendaTarefa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc017/form/etc017FormInserirAgendaTarefa/';

		// novo formulário
		$form = new Formulario('etc017FormInserirAgendaTarefa');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":792,"hidden":0},"etc017FormInserirAgendaTarefa_btn":{"top":7,"left":812,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_T003_Id' => array('name' => 'D085_T003_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T003_Id),
					'D085_T005_Id' => array('name' => 'D085_T005_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T005_Id),
					'D085_T182_Id' => array('name' => 'D085_T182_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T182_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(2,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc017FormInserirAgendaTarefa_btn' => array('name' => 'etc017FormInserirAgendaTarefa_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
							} </script></div>
EOT;
						}
				} else {
					// Insert
					$form->campoSetarValor('D085_Hora', date('H:i:s'), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc017FormInserirAgendaTarefaT003':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
	
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc017FormInserirAgendaTarefaT003_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc017/form/etc017FormInserirAgendaTarefaT003/';

		// novo formulário
		$form = new Formulario('etc017FormInserirAgendaTarefaT003');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":872,"hidden":0},"etc017FormInserirAgendaTarefaT003_btn":{"top":5,"left":912,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_T003_Id' => array('name' => 'D085_T003_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T003_Id),
					'D085_T005_Id' => array('name' => 'D085_T005_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T005_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(2,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc017FormInserirAgendaTarefaT003_btn' => array('name' => 'etc017FormInserirAgendaTarefaT003_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
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
					$form->campoSetarValor('D085_Hora', date('H:i:s'), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc017FormInserirAgendaTarefaT007':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;
	
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc017FormInserirAgendaTarefaT007_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc017/form/etc017FormInserirAgendaTarefaT007/';

		// novo formulário
		$form = new Formulario('etc017FormInserirAgendaTarefaT007');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":872,"hidden":0},"etc017FormInserirAgendaTarefaT007_btn":{"top":5,"left":912,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_T007_Id' => array('name' => 'D085_T007_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T007_Id),
					'D085_T005_Id' => array('name' => 'D085_T005_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T005_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(2,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc017FormInserirAgendaTarefaT007_btn' => array('name' => 'etc017FormInserirAgendaTarefaT007_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
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
					$form->campoSetarValor('D085_Hora', date('H:i:s'), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc017FormInserirAgendaTarefaT005':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		
	
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc017FormInserirAgendaTarefaT005_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc017/form/etc017FormInserirAgendaTarefaT005/';

		// novo formulário
		$form = new Formulario('etc017FormInserirAgendaTarefaT005');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":872,"hidden":0},"etc017FormInserirAgendaTarefaT005_btn":{"top":5,"left":896,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_T005_Id' => array('name' => 'D085_T005_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T005_Id),
					'D085_T003_Id' => array('name' => 'D085_T003_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T003_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(2,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc017FormInserirAgendaTarefaT005_btn' => array('name' => 'etc017FormInserirAgendaTarefaT005_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
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
					$form->campoSetarValor('D085_Hora', date('H:i:s'), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'etc017FormInserirAgendaTarefaT182':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$T259_Id = (isset($r_T259_Id)) ? $r_T259_Id : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		$T182_Id = (isset($r_T182_Id)) ? $r_T182_Id : false;

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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc017FormInserirAgendaTarefaT182_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc017/form/etc017FormInserirAgendaTarefaT182/';

		// novo formulário
		$form = new Formulario('etc017FormInserirAgendaTarefaT182');
		$form_editor = array('geral' => '{"D085_Mensagem":{"top":7,"left":16,"height":39,"width":462,"hidden":0},"etc017FormInserirAgendaTarefaT182_btn":{"top":5,"left":496,"width":null,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array('name' => 'D085_D024_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $D024_Id),
					'D085_T259_Id' => array('name' => 'D085_T259_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T259_Id),
					'D085_T003_Id' => array('name' => 'D085_T003_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T003_Id),
					'D085_T005_Id' => array('name' => 'D085_T005_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T005_Id),
					'D085_T182_Id' => array('name' => 'D085_T182_Id', 'label'=> "Cliente", 'type' => 'hidden', 'value' => $T182_Id),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_Data' => array('name' => 'D085_Data', 'label'=>'', 'type' => 'hidden', 'value' => date("Y-m-d")),
					'D085_Hora' => array('name' => 'D085_Hora', 'label'=>'', 'type' => 'hidden', 'value' => date('H:i:s')),
					'D085_Mensagem' => array('name' => 'D085_Mensagem', 'label'=>'', 'type' => 'textarea', 'length' => array(2,60), 'extra' => 'placeholder="INSIRA UMA OBSERVAÇÃO"'),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'type' => 'hidden', 'value' => 'S'),
					'etc017FormInserirAgendaTarefaT182_btn' => array('name' => 'etc017FormInserirAgendaTarefaT182_btn', 'type' => 'submit', 'reload' => false,'value' => 'Incluir', 'align' => 'right', 'style' => 'height: 26px', 'reject'=>true),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_divReload, 'reject'=>true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
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
					$form->campoSetarValor('D085_Hora', date('H:i:s'), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$divReload = $form->campoValorEnviado('divReload');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divReload}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
}