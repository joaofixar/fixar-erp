<?php
namespace hardness;        
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 03/01/2012  
	*/
	case 'cad046FormCadastroMotivosCancelamento':
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
		$form_tabela = 'D047';
		$form_campo_pk = 'D047_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad046FormCadastroMotivosCancelamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad046/form/cad046FormCadastroMotivosCancelamento/';

		$camposForm = ($g['C031']['conferenciaVolumes'] === 'S') ? ',"D047_Flag_Conferencia":{"top":145,"left":7.703125,"width":63,"hidden":0}' : '';
		$form_editor = array(
			'geral' => '{"D047_Flag_Compra":{"top":122,"left":46.546875,"width":63,"hidden":0},"D047_Descricao_Motivo":{"top":7,"left":105.71875,"height":108,"width":239,"hidden":0}'. $camposForm .'}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'D047_Flag_Compra' => array('name' => 'D047_Flag_Compra', 'label' => 'Motivo Comprar (S/N)', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),   
							array('title' => 'Não', 'value' => 'N', ),   
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'D047_Descricao_Motivo' => array('name' => 'D131_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(10,37)),
				)
			)
		);

		if ($g['C031']['conferenciaVolumes'] === 'S') {
			$formArray['campos']['geral']['D047_Flag_Conferencia'] = array('name' => 'D047_Flag_Conferencia', 'label' => 'Utilizado na conferência (S/N)', 'type' => 'select', 'select' => 
																		array(
																			array('title' => 'Não', 'value' => 'N', 'selected' => true),
																			array('title' => 'Sim', 'value' => 'S')
																		));
		}

        $form = new Formulario('form-cad046FormPrincipal');
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
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad046FormCadastroMotivosCancelamentoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
