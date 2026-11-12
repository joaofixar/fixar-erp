<?php
namespace hardness;

/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'cad022FormCadastroPortador':
			/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D027';
		$form_campo_pk = 'D027_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad022FormCadastroPortador_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad022/form/cad022FormCadastroPortador/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"D027_Portador":{"top":7,"left":95.6875,"width":360,"hidden":0},"D027_Flag_Tipo":{"top":30,"left":116.921875,"width":128,"hidden":0},"D027_Flag_Boleto":{"top":30,"left":380.515625,"width":64,"hidden":0},"D027_D007_Id":{"top":53,"left":107.671875,"width":367,"hidden":0},"D027_Flag_Baixa_Automatica":{"top":76,"left":47.1875,"width":112,"hidden":0},"D027_Baixa_Automatica_Dias":{"top":76,"left":367.71875,"width":56,"hidden":0},"D027_Flag_Cupom":{"top":99,"left":117.5625,"width":112,"hidden":0},"D027_Flag_Cupom_Gaveta":{"top":99,"left":382.359375,"width":64,"hidden":0},"D027_Valor_Minimo_Parcela":{"top":122,"left":15.84375,"width":104,"hidden":0},"D027_Flag_Ativo":{"top":122,"left":417.734375,"width":64,"hidden":0},"D027_Observacao":{"top":145,"left":80.296875,"height":83,"width":360,"hidden":0},"D027_Flag_Exibe_CRM":{"top":237,"left":389.96875,"width":64,"hidden":0},"D027_Taxa":{"top":236.96875,"left":104.65625,"width":136,"hidden":0},"D027_Descricao_Taxa":{"top":259.96875,"left":51.078125,"width":360,"hidden":0},"D027_Flag_Credito_Antecipado":{"top":282.96875,"left":44.90625,"width":368,"hidden":0},"D027_Acrescimo_IPV":{"top":305.96875,"left":55.78125,"width":168,"hidden":0},"D027_Flag_Prazo":{"top":306,"left":436.25,"width":80,"hidden":1},"D027_Tipo_Forma_Pagamento_NFCE":{"top":329,"left":26.46875,"width":176,"hidden":0},"D027_Codigo_Bandeira_NFCE":{"top":351.96875,"left":95.515625,"width":176,"hidden":0},"D027_Cnpj_Instituicao_Pagamento":{"top":375,"left":26.015625,"width":168,"hidden":0},"D027_Cnpj_Intermediador_Transacao":{"top":398,"left":30.609375,"width":168,"hidden":0},"D027_Id_Usuario_Intermediador_Transacao":{"top":421,"left":9.609375,"width":360,"hidden":0}}',
			'ecommerce' => '{"D027_Descricao_B2B":{"top":99,"left":49.125,"height":84,"width":317,"hidden":0},"D027_Descricao_Ecommerce":{"top":7,"left":11.234375,"height":84,"width":317,"hidden":0},"D027_Parcelas_Ecommerce":{"top":191,"left":17.34375,"width":86,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad022FormCadastroPortador');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D027_Portador' => array('name' => 'D027_Portador','label'=>'Portador', 'type' => 'text', 'length' => array(30,60)),
					'D027_Flag_Tipo' => array('name' => 'D027_Flag_Tipo','label'=>'Tipo', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Boleto', 'value' => '1'),
							array('title' => 'Cheque', 'value' => '2'),
							array('title' => 'Promissória', 'value' => '3'),
							array('title' => 'E-Commerce', 'value' => '4'),
							array('title' => 'Outros', 'value' => '5'),
						)
					),
					'D027_Flag_Prazo' => array('name' => 'D027_Flag_Prazo','label'=>'Venda a Prazo', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '',  'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Nenhum', 'value' => 'X'),
						)
					),
					'D027_Flag_Boleto' => array('name' => 'D027_Flag_Boleto','label'=>'Emite Boleto', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_D007_Id' => array('name' => 'Consulta_D027_D007_Id', 'label' => 'Banco', 'type' => 'select', 'select' =>
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco',"left join D062 on D062_Id=D007_D062_Id where D062_Id is not null AND D007_Flag_Layout='1' and D007_Flag_Ativo='S' order by D007_Codigo_Conta,D007_Nome_Banco", true),
					),
					'D027_Flag_Cupom' => array('name' => 'D027_Flag_Cupom','label'=>'PDV', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '',  'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_Valor_Minimo_Parcela' => array('name' => 'D027_Valor_Minimo_Parcela','label'=>'Valor mínimo por parcela', 'type' => 'moeda', 'length' => array(10,20)),
					'D027_Observacao' => array('name' => 'D027_Observacao','label'=>'Observação', 'type' => 'textarea', 'length' => array(3,60)),
					'D027_Flag_Baixa_Automatica' => array('name' => 'D027_Flag_Baixa_Automatica','label'=>'Baixa Automática?', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_Flag_Cupom_Gaveta' => array('name' => 'D027_Flag_Cupom_Gaveta','label'=>'Abrir Gaveta', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '',  'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_Flag_Ativo' => array('name' => 'D027_Flag_Ativo','label'=>'Ativo', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '',  'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_Flag_Exibe_CRM' => array('name' => 'D027_Flag_Exibe_CRM','label'=>'Exibe CRM', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '',  'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D027_Taxa' => array('name' => 'D027_Taxa','label'=>'% Taxa', 'type' => 'moeda', 'length' => array(10,20)),
					'D027_Descricao_Taxa' => array('name' => 'D027_Descricao_Taxa','label'=>'Descrição da taxa', 'type' => 'text', 'length' => array(10,20)),
					'D027_Acrescimo_IPV' => array('name' => 'D027_Acrescimo_IPV','label'=>'% Acréscimo IPV', 'type' => 'moeda', 'length' => array(10,20)),
					'D027_Flag_Credito_Antecipado' => array('name' => 'D027_Flag_Credito_Antecipado', 'label' => 'Crédito Antecipado', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Crédito Antecipado', 'value' => '1'),
							array('title' => 'Crédito Troca', 'value' => '2'),
							array('title' => 'Abatimento Crédito Antecipado', 'value' => '3'),
							array('title' => 'Abatimento Crédito Troca', 'value' => '4'),
						)
					),
					'D027_Baixa_Automatica_Dias' => array('name' => 'D027_Baixa_Automatica_Dias', 'label' => 'Dias para baixa', 'type' => 'text', 'length' => array(10,20)),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'D027_Tipo_Forma_Pagamento_NFCE' => array('name' => 'D027_Tipo_Forma_Pagamento_NFCE','label'=>'Forma de pagamento', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01 - Dinheiro', 'value' => '01'),
							array('title' => '02 - Cheque', 'value' => '02'),
							array('title' => '03 - Cartão de Crédito', 'value' => '03'),
							array('title' => '04 - Cartão de Débito', 'value' => '04'),
							array('title' => '05 - Crédito Loja', 'value' => '05'),
							array('title' => '10 - Vale Alimentação', 'value' => '10'),
							array('title' => '11 - Vale Refeição', 'value' => '11'),
							array('title' => '12 - Vale Presente', 'value' => '12'),
							array('title' => '13 - Vale Combustível', 'value' => '13'),
							array('title' => '14 - Duplicata Mercantil', 'value' => '14'),
							array('title' => '15 - Boleto Bancário', 'value' => '15'),
							array('title' => '16 - Depósito Bancário', 'value' => '16'),
							array('title' => '17 - Pagamento Instantâneo (PIX)', 'value' => '17'),
							array('title' => '18 - Transferência bancária, Carteira Digital', 'value' => '18'),
							array('title' => '19 - Programa de fidelidade, Cashback, Crédito Virtual', 'value' => '19'),
							array('title' => '90 - Sem Pagamento', 'value' => '90'),
							array('title' => '99 - Outros', 'value' => '99'),
						)
					),
					'D027_Codigo_Bandeira_NFCE' => array('name' => 'D027_Codigo_Bandeira_NFCE','label'=>'Bandeira', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01 - Visa', 'value' => '01'),
							array('title' => '02 - Mastercard', 'value' => '02'),
							array('title' => '03 - American Express', 'value' => '03'),
							array('title' => '04 - Sorocred', 'value' => '04'),
							array('title' => '05 - Dinners Club', 'value' => '05'),
							array('title' => '06 - Elo', 'value' => '06'),
							array('title' => '07 - Hipercard', 'value' => '07'),
							array('title' => '08 - Aura', 'value' => '08'),
							array('title' => '09 - Cabal', 'value' => '09'),
							array('title' => '10 - Alelo', 'value' => '10'),
							array('title' => '11 - Banes Card', 'value' => '11'),
							array('title' => '12 - CalCard', 'value' => '12'),
							array('title' => '13 - Credz', 'value' => '13'),
							array('title' => '14 - Discover', 'value' => '14'),
							array('title' => '15 - GoodCard', 'value' => '15'),
							array('title' => '16 - GreenCard', 'value' => '16'),
							array('title' => '17 - Hiper', 'value' => '17'),
							array('title' => '18 - JcB', 'value' => '18'),
							array('title' => '19 - Mais', 'value' => '19'),
							array('title' => '20 - MaxVan', 'value' => '20'),
							array('title' => '21 - Policard', 'value' => '21'),
							array('title' => '22 - RedeCompras', 'value' => '22'),
							array('title' => '23 - Sodexo', 'value' => '23'),
							array('title' => '24 - ValeCard', 'value' => '24'),
							array('title' => '25 - Verocheque', 'value' => '25'),
							array('title' => '26 - VR', 'value' => '26'),
							array('title' => '27 - Ticket', 'value' => '27'),
							array('title' => '99 - Outros', 'value' => '99'),
						)
					),
					'D027_Cnpj_Instituicao_Pagamento' => array('name' => 'D027_Cnpj_Instituicao_Pagamento', 'label'=>'CNPJ Insti. Pagamento', 'type' => 'cnpj','length' => array(18,18)),
					'D027_Cnpj_Intermediador_Transacao' => array('name' => 'D027_Cnpj_Intermediador_Transacao', 'label'=>'CNPJ Inter. Transação', 'type' => 'cnpj','length' => array(18,18)),
					'D027_Id_Usuario_Intermediador_Transacao' => array('name' => 'D027_Id_Usuario_Intermediador_Transacao', 'label'=>'ID Usuario Inter.Transação', 'length' => array(1,60)),
				),
				'PDV' => array(
					'D027_Id_Portador_PDV' => array('name' => 'D027_Id_Portador_PDV', 'label' => 'ID portador PDV', 'type' => 'text', 'length' => array(10,20)),
					'D027_Operacao_Importar_PDV' => array('name' => 'D027_Operacao_Importar_PDV', 'label' => 'Operação ao importar cupom fiscal', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Gerar título a receber', 'value' => '1'),
							array('title' => 'Gerar desconto', 'value' => '2'),
						)
					),
				),
				'ecommerce' => array(
					'D027_Descricao_Ecommerce' => array('name' => 'D027_Descricao_Ecommerce', 'label' => 'Descricao Ecommerce', 'type' => 'textarea', 'length' => array(6,50)),
					'D027_Descricao_B2B' => array('name' => 'D027_Descricao_B2B', 'label' => 'Descricao B2B', 'type' => 'textarea', 'length' => array(6,50)),
					'D027_Parcelas_Ecommerce' => array('name' => 'D027_Parcelas_Ecommerce', 'label' => 'Parcelas Ecommerce', 'type' => 'text', 'length' => array(10,20)),
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
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
}
