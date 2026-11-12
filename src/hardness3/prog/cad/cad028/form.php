<?php
namespace hardness; 
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 21/11/2011  
	*/
	case 'cad028FormCadastroSubGrupos':
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
		$form_tabela = 'D002';
		$form_campo_pk = 'D002_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad028FormSubGruposBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad028/form/cad028FormCadastroSubGrupos/';
		$form_editor = array(
			'geral' => '{"D002_D003_Id":{"top":7,"left":75,"width":319,"hidden":0},"D002_Descricao_Produto":{"top":30,"left":52,"width":312,"hidden":0},"D002_Flag_Visualizar_Site":{"top":30,"left":592,"width":200,"hidden":1},"D002_Codigo_Padrao":{"top":53,"left":612,"width":128,"hidden":1},"D002_Descricao_Padrao":{"top":76,"left":596,"width":178,"hidden":1},"D002_IPV":{"top":99,"left":682,"width":103,"hidden":1},"D002A_Utiliza_Conversao":{"top":53,"left":16.28125,"width":64,"hidden":0},"D002A_Tolerancia_Conversao":{"top":76,"left":51.328125,"width":56,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D002_D003_Id' => array('name' => 'D002_D003_Id', 'label' => 'Grupo', 'type' => 'select', 'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D002_Descricao_Produto' => array('name' => 'D002_Descricao_Produto', 'label' => 'Sub-Grupo', 'type' => 'text', 'length' => array(30,40)),
					'D002_Flag_Visualizar_Site' => array('name' => 'D002_Flag_Visualizar_Site','label'=>'Visualizar no Site?','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "S", 'selected' => true),
							array('title' => 'Não', 'value' => "N")
						)
					),
					'D002_IPV' => array('name' => 'D002_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(15,40)),
					'D002_Codigo_Padrao' => array('name' => 'D002_Codigo_Padrao', 'label' => 'Código Padrão', 'type' => 'text', 'length' => array(20,40)),
					'D002_Descricao_Padrao' => array('name' => 'D002_Descricao_Padrao', 'label' => 'Descrição Padrão', 'type' => 'text', 'length' => array(30,40)),
					'D002_Integrado_Tray' => array('name' => 'D002_Integrado_Tray', 'label' => 'Integrado Tray', 'type' => 'hidden', 'value' => 'N'),
				)
			)
		);

		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$formArray['campos']['geral']['D002A_Utiliza_Conversao']    = array('name' => 'D002A_Utiliza_Conversao','label' => 'Utiliza Conversão?', 'type' => 'select', 
																			'select' => array(
																				array('title' => 'Sim', 'value' => "S"),
																				array('title' => 'Não', 'value' => "N", 'selected' => true)
																			));
			$formArray['campos']['geral']['D002A_Tolerancia_Conversao'] = array('name' => 'D002A_Tolerancia_Conversao', 'label' => '% Tolerâcia', 'type' => 'moeda', 'length' => array(15,40));
		}

        $form = new Formulario('form-cad024FormPrincipal');
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

				$form->campoSetarValor("D002_Integrado_Tray", "N", true);

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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad028FormSubGruposBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>