<?php
namespace hardness;      
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/2011  
	*/
	case 'cad017FormPortos':
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
		$form_tabela = 'D118';
		$form_campo_pk = 'D118_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad017FormPortosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad017/form/cad017FormPortos/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D118_Nome' => array('name' => 'D118_Nome', 'label' => 'Nome', 'type' => 'text', 'length' => array(60,80)),
					'D118_Estado' => array('name' => 'D118_Estado', 'label' => 'Estado', 'type' => 'text', 'length' => array(30,80)),
					'D118_Cidade' => array('name' => 'D118_Cidade', 'label' => 'Cidade', 'type' => 'text', 'length' => array(30,80)),
					'D118_D030_Id' => array('name' => 'D118_D030_Id', 'label' => 'País', 'type' => 'select', 'length' => array(1,255), 'select' => 
							gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)  
					),
					'D118_Flag_Tipo' => array('name' => 'D118_Flag_Tipo', 'label' => 'Tipo DI', 'type' => 'select', 'length' => array(1,255), 'select' => 
							array(
								array('title' => '...', 'value' =>''),
								array('title' => 'Normal', 'value' =>'N'),
								array('title' => 'Simplificada', 'value' =>'S'),
								array('title' => 'Nenhuma', 'value' =>'X'),
							)
					),
					'D118_C004_Id' => array('name' => 'D118_C004_Id', 'label' => 'Cidade', 'type' => 'hidden', 'value'=>$g['empresaAtual'], 'length' => array(30,80)),
				)
			)
		);
        $form = new Formulario('form-cad017FormPrincipal');
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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad017FormPortosBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad017FormTipoDespesasComexIncluir':
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
		$form_tabela = 'D190';
		$form_campo_pk = 'D190_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad017FormTipoDespesaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad017/form/cad017FormTipoDespesasComexIncluir/';

		//form editor
		$form_editor = array(
			'geral' => '{"D190_Tipo_Despesa":{"top":7,"left":7.828125,"width":472,"hidden":0}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D190_Tipo_Despesa' => array('name' => 'D190_Tipo_Despesa', 'label' => 'Pacote', 'type' => 'text', 'length' => array(60,100)),
				)
			)
		);
        $form = new Formulario('form-cad017FormTipoDespesasComexIncluir');
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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad017FormTipoDespesaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad017FormDespesasComexIncluir':
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
		$form_tabela = 'D191';
		$form_campo_pk = 'D191_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad017FormDespesaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad017/form/cad017FormDespesasComexIncluir/';

		//form editor
		$form_editor = array(
			'geral' => '{"D191_Tipo":{"top":53,"left":52.921875,"width":191,"hidden":0},"D191_Descricao_Despesa":{"top":7,"left":32.234375,"width":488,"hidden":0},"D191_Tipo_Despesa":{"top":30,"left":7.828125,"width":191,"hidden":0}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D191_Descricao_Despesa' => array('name' => 'D191_Descricao_Despesa', 'label' => 'Despesa', 'type' => 'text', 'length' => array(60,100)),
					'D191_Tipo_Despesa' => array('name' => 'D191_Tipo_Despesa', 'label' => 'Tipo Despesa', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),		
							array('title' => 'Outros', 'value' => '1'),		
							array('title' => 'THC', 'value' => '2'),
							array('title' => 'Seguro', 'value' => '3'),	
							array('title' => 'Siscomex', 'value' => '4'),	
							array('title' => 'AFRMM', 'value' => '5'),	
							array('title' => 'Frete', 'value' => '6')
						)
					),
					'D191_Tipo' => array('name' => 'D191_Tipo', 'label' => 'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),		
							array('title' => 'Outras despesas', 'value' => '1'),		
							array('title' => 'Despesa Aduaneira', 'value' => '2'),
							array('title' => 'Despesa Acessória', 'value' => '3')
						)
					),
				)
			)
		);
        $form = new Formulario('form-cad017FormDespesasComexIncluir');
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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad017FormDespesaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad017FormPacoteDespesasComexIncluir':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$D190_Id = isset($r_D190_Id) ? $r_D190_Id : false;
		
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
		$form_tabela = 'D192';
		$form_campo_pk = 'D192_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad017FormPacoteDespesaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad017/form/cad017FormPacoteDespesasComexIncluir/';

		//form editor
		$form_editor = array(
			'geral' => '{"D192_Flag_Base_Rateio":{"top":145,"left":403.6875,"width":112,"hidden":0},"D192_D190_Id":{"top":7,"left":72.28125,"width":463,"hidden":0},"D192_D191_Id":{"top":30,"left":57.71875,"width":463,"hidden":0},"D192_Flag_Soma_Base_IPI":{"top":53,"left":17.65625,"width":55,"hidden":0},"D192_Flag_Soma_Base_COFINS":{"top":53,"left":392.421875,"width":63,"hidden":0},"D192_Flag_Soma_Base_ICMS":{"top":76,"left":4.296875,"width":55,"hidden":0},"D192_Flag_Soma_Base_PIS":{"top":76,"left":414.125,"width":63,"hidden":0},"D192_Flag_Soma_Base_II":{"top":99,"left":24.59375,"width":55,"hidden":0},"D192_Flag_Soma_Preco_NFe":{"top":99,"left":406.65625,"width":63,"hidden":0},"D192_Flag_Soma_Custo":{"top":122,"left":29.40625,"width":55,"hidden":0},"D192_Flag_Tipo_Rateio":{"top":145,"left":75.484375,"width":111,"hidden":0},"D192_Flag_ME":{"top":122,"left":444.4375,"width":63,"hidden":0},"D192_Flag_Tipo":{"top":168,"left":84.921875,"width":111,"hidden":0},"D192_Valor":{"top":191,"left":81.203125,"width":104,"hidden":0},"D192_Aliquota":{"top":214,"left":65.875,"width":104,"hidden":0},"D192_Formula":{"top":237,"left":66.28125,"height":131,"width":456,"hidden":0}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D192_D190_Id' => array('name' => 'D192_D190_Id', 'label' => 'Pacote', 'type' => 'select', 'select' =>
						gGeraSelect('D190', 'D190_Id', 'D190_Tipo_Despesa',"WHERE D190_Id='{$D190_Id}' ORDER BY D190_Tipo_Despesa"),
					),	
					'D192_D191_Id' => array('name' => 'D192_D191_Id', 'label' => 'Descrição', 'type' => 'select', 'select' =>
						gGeraSelect('D191', 'D191_Id', 'D191_Descricao_Despesa','ORDER BY D191_Descricao_Despesa'),
					),		
					'D192_Aliquota' => array('name' => 'D192_Aliquota', 'label' => 'Alíquota', 'type' => 'moeda', 'length' => array(20,10)),
					'D192_Flag_Soma_Base_IPI' => array('name' => 'D192_Flag_Soma_Base_IPI', 'label' => 'Somar na base IPI', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Base_ICMS' => array('name' => 'D192_Flag_Soma_Base_ICMS', 'label' => 'Somar na base ICMS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_ME' => array('name' => 'D192_Flag_ME', 'label' => 'Valor em ME', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Base_PIS' => array('name' => 'D192_Flag_Soma_Base_PIS', 'label' => 'Somar na base PIS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Base_COFINS' => array('name' => 'D192_Flag_Soma_Base_COFINS', 'label' => 'Somar na base COFINS', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Base_II' => array('name' => 'D192_Flag_Soma_Base_II', 'label' => 'Somar na base II', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Custo' => array('name' => 'D192_Flag_Soma_Custo', 'label' => 'Somar no custo', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Soma_Preco_NFe' => array('name' => 'D192_Flag_Soma_Preco_NFe', 'label' => 'Somar no preço NFe', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),		
							array('title' => 'Não', 'value' => 'N', 'selected' => true),				
						)
					),
					'D192_Flag_Tipo_Rateio' => array('name' => 'D192_Flag_Tipo_Rateio', 'label' => 'Rateio', 'type' => 'select','select' => 
						array(
							array('title' => 'Custo', 'value' => 'C', 'selected' => true),		
							array('title' => 'Peso', 'value' => 'P'),				
						)
					),
					'D192_Flag_Base_Rateio' => array('name' => 'D192_Flag_Base_Rateio', 'label' => 'Base rateio', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),		
							array('title' => 'Total DI', 'value' => '1'),		
							array('title' => 'Total Adição', 'value' => '2'),				
							array('title' => 'Peso Adição', 'value' => '3'),				
						)
					),
					'D192_Flag_Tipo' => array('name' => 'D192_Flag_Tipo', 'label' => 'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Valor fixo', 'value' => 'V'),		
							array('title' => 'Fórmula', 'value' => 'F'),				
						)
					),
					'D192_Valor' => array('name' => 'D192_Valor', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20,10)),
					'D192_Formula' => array('name' => 'D192_Formula', 'label' => 'Fórmula', 'type' => 'textarea', 'uppercase' => false, 'code' => 'php', 'codeModoSimples' => true),
				)
			)
		);
        $form = new Formulario('form-cad017FormDespesasComexIncluir');
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D192_Flag_Tipo').change(function() {
					if (($(this).val() == 'V')) {
						$('#{$g['divId']} #D192_Valor').closest('.formEditor').show();
						setTimeout(function() {
							$('#{$g['divId']} #D192_Valor').focus();
						}, 100);
					}else{
						$('#{$g['divId']} #D192_Valor').val('');
						$('#{$g['divId']} #D192_Valor').closest('.formEditor').hide();
					}
					if($(this).val() == 'F'){
						$('#{$g['divId']} #D192_Formula').closest('.formEditor').show();
						$('#{$g['divId']} #D192_Aliquota').closest('.formEditor').show();
						setTimeout(function() {
							$('#{$g['divId']} #D192_Formula').focus();
							$('#{$g['divId']} #D192_Aliquota').focus();
						}, 100);
					}else{
						$('#{$g['divId']} #D192_Formula').val('');
						$('#{$g['divId']} #D192_Formula').closest('.formEditor').hide();
						$('#{$g['divId']} #D192_Aliquota').val('');
						$('#{$g['divId']} #D192_Aliquota').closest('.formEditor').hide();
					}
				});
				$('#{$r_divId} #D192_Flag_Tipo').trigger('change');
			} </script></div>
EOT;


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

						$D192_Id = $g['mysqlLastId'];

						$D192  = mysql_query("SELECT D192_Ordem FROM D192 WHERE D192_D190_Id = '{$D190_Id}' ORDER BY D192_Ordem DESC LIMIT 1");
						$mD192 = mysql_fetch_array($D192);
						if(empty($mD192['D192_Ordem']) || $mD192['D192_Ordem'] == 0){
							$mD192['D192_Ordem'] = '1';
						} else {
							$mD192['D192_Ordem'] = $mD192['D192_Ordem'] + 1;
						}
						mysql_query("UPDATE D192 SET D192_Ordem = '{$mD192['D192_Ordem']}' WHERE D192_Id = '{$D192_Id}'");

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
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad017FormPacoteDespesaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
