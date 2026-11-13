<?php

namespace hardness;

require_once('bibliotecas/classes/CONTABIL.php');
$CONTABIL = new CONTABIL();

switch ($g['r'][3]) {
	case 'cont001FormConta':
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
		$form_tabela = 'D193';
		$form_campo_pk = 'D193_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cont001FormContaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cont/cont001/form/cont001FormConta/';
		$form_editor = array(
			'geral' => '{"D193_Codigo":{"top":7,"left":71.84375,"width":344,"hidden":0},"D193_Nome_Conta":{"top":30,"left":45.21875,"width":344,"hidden":0},"D193_Tipo":{"top":76,"left":84.921875,"width":127,"hidden":0},"D193_Flag_Conta_Movimento":{"top":53,"left":19.703125,"width":127,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D193_Codigo' => array('name' => 'D193_Codigo', 'label' => 'Código', 'type' => 'text'),
					'D193_Nome_Conta' => array('name' => 'D193_Nome_Conta', 'label' => 'Nome Conta', 'type' => 'text'),
					'D193_Tipo' => array(
						'name' => 'D193_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Ativo', 'value' => 'A'),
							array('title' => 'Passivo', 'value' => 'P'),
							array('title' => 'Resultado', 'value' => 'R')
						)
					),
					'D193_Flag_Conta_Movimento' => array(
						'name' => 'D193_Flag_Conta_Movimento', 'label' => 'Conta Movimento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),							
						)
					),
				)
			)
		);
		$form = new Formulario('form-cont001FormConta');
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
		$botoesDir .= '<button type="submit" id="cont001FormContaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;	
}
