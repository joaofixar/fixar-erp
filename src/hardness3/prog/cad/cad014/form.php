<?php
namespace hardness;
  
switch ($g['r'][3]) {
  case 'cad014FormPrincipal':
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
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_formcad014FormPrincipal)) ? $r_formcad014FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D007';
		$form_campo_pk = 'D007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad014FormPrincipalBtnEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad014/form/cad014FormPrincipal/';
		// Editor
		$form_editor = array(
			'bancario' => '{"D007_CPF":{"top":7,"left":5,"width":146,"hidden":1},"D007_Nome_Banco":{"top":7,"left":44.84375,"width":125,"hidden":0},"D007_Posto_Cooperativa":{"top":214,"left":319.375,"width":56,"hidden":0},"D007_D062_Id":{"top":30,"left":90.6875,"width":247,"hidden":0},"D007_Numero_Agencia":{"top":53,"left":82.125,"width":55,"hidden":0},"D007_Digito_Verificador_Agencia":{"top":76,"left":50.34375,"width":30,"hidden":0},"D007_Numero_Conta":{"top":99,"left":35.0625,"width":75,"hidden":0},"D007_Digito_Verificador_Conta":{"top":122,"left":60.734375,"width":30,"hidden":0},"D007_CNPJ":{"top":145,"left":85.8125,"width":136,"hidden":0},"D007_Nome_Contato":{"top":168,"left":83.359375,"width":136,"hidden":0},"D007_DDD_Telefone":{"top":191,"left":80.296875,"width":30,"hidden":0},"D007_Telefone":{"top":191,"left":176,"width":88,"hidden":0},"D007_Flag_Ativo":{"top":214,"left":97.421875,"width":48,"hidden":0},"D007_Observacao":{"top":237,"left":62.578125,"height":59,"width":342,"hidden":0},"D007_Flag_Fluxo_Caixa":{"top":305.984375,"left":24.0625,"width":56,"hidden":0},"D007_Flag_Emite_Cupom":{"top":306,"left":343.25,"width":59,"hidden":0}}',
			'emissao' => '{"D007_Nome_Cooperativa":{"top":375,"left":47.515625,"width":280,"hidden":0},"D007_Nome_Conta":{"top":7,"left":63.125,"width":312,"hidden":0},"D007_Numero_Contrato":{"top":7,"left":647.0625,"width":72,"hidden":0},"D007_Codigo_Cedente":{"top":30,"left":60.625,"width":75,"hidden":0},"D007_Codigo_Baixa":{"top":30,"left":571.28125,"width":168,"hidden":0},"D007_Numero_Range":{"top":53,"left":65.796875,"width":75,"hidden":0},"D007_Nosso_Numero":{"top":53,"left":601.3125,"width":104,"hidden":0},"D007_Flag_Layout":{"top":76,"left":69.78125,"width":64,"hidden":0},"D007_Aceite":{"top":76,"left":731.75,"width":64,"hidden":0},"D007_Flag_Tipo_Registro":{"top":99,"left":52.546875,"width":128,"hidden":0},"D007_Especie":{"top":99,"left":692.421875,"width":88,"hidden":0},"D007_Numero_Convenio":{"top":122,"left":51.015625,"width":152,"hidden":0},"D007_Especie_Documento":{"top":122,"left":670.453125,"width":88,"hidden":0},"D007_Tipo_Carteira_Banco_Brasil":{"top":145,"left":99.890625,"width":72,"hidden":0},"D007_Uso_Banco":{"top":145,"left":663.34375,"width":88,"hidden":0},"D007_Variacao_Carteira":{"top":168,"left":39.125,"width":72,"hidden":0},"D007_Flag_Tipo_Multa":{"top":168,"left":579.1875,"width":112,"hidden":0},"D007_Codigo_Cliente":{"top":191,"left":67.3125,"width":72,"hidden":0},"D007_Percentual_Multa":{"top":191,"left":580.484375,"width":104,"hidden":0},"D007_Instrucao_Protesto_1":{"top":214,"left":63.734375,"width":168,"hidden":0},"D007_Percentual_Juros_Dia":{"top":214,"left":632.140625,"width":104,"hidden":0},"D007_Instrucao_Protesto_2":{"top":237,"left":63.734375,"width":168,"hidden":0},"D007_Instrucoes_Protesto":{"top":237,"left":482.171875,"height":62,"width":280,"hidden":0},"D007_Instrucao_Cobranca_2":{"top":260,"left":67.40625,"width":168,"hidden":0},"D007_Instrucoes_Boleto":{"top":283,"left":52.859375,"height":62,"width":280,"hidden":0},"D007_Tipo_Cobranca":{"top":306,"left":546.234375,"width":72,"hidden":0},"D007_Baixar_Automatico_Valor_Excedido_Retorno":{"top":329,"left":514.921875,"width":64,"hidden":0},"D007_Modelo_Layout_Boleto":{"top":352,"left":32.046875,"width":288,"hidden":0},"D007_Numero_Sequencial_Remessa":{"top":398,"left":50.921875,"width":120,"hidden":0},"D007_Valor_Maximo_Boleto_Sem_Registro":{"top":375,"left":502.171875,"width":136,"hidden":0},"D007_Layout_Cobranca":{"top":398,"left":600.09375,"width":143,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'bancario' => array(
					// Campo normal (do banco)
					'D007_Nome_Banco' => array('name' => 'D007_Nome_Banco', 'label' => 'Nome da Conta', 'type' => 'text', 'length' => array(20,30)),
					// Dados Bancários
					'D007_D062_Id' => array('name' => 'D007_D062_Id', 'label' => 'Banco', 'type' => 'pesquisa', 'pesquisa' => 
						array('D062', 'D062_Id', 'D062_Numero_Banco,D062_Nome_Banco'), 'length' => array(50,50)
					),
					'D007_Numero_Agencia' => array('name' => 'D007_Numero_Agencia', 'label' => 'Agência', 'type' => 'text', 'length' => array(6,6)),
					'D007_Digito_Verificador_Agencia' => array('name' => 'D007_Digito_Verificador_Agencia', 'label' => 'Dígito Agência', 'type' => 'text', 'length' => array(1,3)),
					'D007_Numero_Conta' => array('name' => 'D007_Numero_Conta', 'label' => 'Número da Conta', 'type' => 'text', 'length' => array(10,10)),
					'D007_Digito_Verificador_Conta' => array('name' => 'D007_Digito_Verificador_Conta', 'label' => 'Dígito Conta', 'type' => 'text', 'length' => array(1,3)),
					'D007_CNPJ' => array('name' => 'D007_CNPJ', 'label' => 'CNPJ', 'type' => 'cnpj'),
					'D007_CPF' => array('name' => 'D007_CPF', 'label' => 'CPF', 'type' => 'cpf'),
					'D007_Nome_Contato' => array('name' => 'D007_Nome_Contato', 'label' => 'Contato', 'type' => 'text', 'length' => array(18,30)),
					'D007_DDD_Telefone' => array('name' => 'D007_DDD_Telefone', 'label' => 'Telefone', 'type' => 'text', 'length' => array(1,2)),
					'D007_Telefone' => array('name' => 'D007_Telefone', 'label' => '', 'type' => 'text','align' => 'right', 'length' => array(10,9)),
					'D007_Flag_Ativo' => array('name' => 'D007_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D007_Flag_Fluxo_Caixa' => array('name' => 'D007_Flag_Fluxo_Caixa', 'label' => 'Exibir fluxo de caixa', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D007_Observacao' => array('name' => 'D007_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(3,60)),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'D007_Flag_Emite_Cupom' => array('name' => 'D007_Flag_Emite_Cupom', 'label' => 'Emite Cupom', 'type' => 'select', 
						'select' => array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D007_Posto_Cooperativa' => array('name' => 'D007_Posto_Cooperativa', 'label' => 'Posto Cooperativa', 'type' => 'text', 'length' => array(2,2)),
				),
				'emissao' => array(
					//Dados para emissão dos boletos
					'D007_Nome_Conta' => array('name' => 'D007_Nome_Conta', 'label' => 'Nome Empresa', 'type' => 'text', 'length' => array(30,30)),
					'D007_Codigo_Cedente' => array('name' => 'D007_Codigo_Cedente', 'label' => 'Código Cedente', 'type' => 'text', 'length' => array(12,12)),
					'D007_Numero_Range' => array('name' => 'D007_Numero_Range', 'label' => 'Número Range', 'type' => 'text', 'length' => array(12,12)),
					'D007_Flag_Layout' => array('name' => 'D007_Flag_Layout', 'label' => 'Possui Layout', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => '1', 'selected' => true),
							array('title' => 'Não', 'value' => '2')
						)
					),
					'D007_Flag_Tipo_Registro' => array('name' => 'D007_Flag_Tipo_Registro', 'label' => 'Tipo de Cobrança', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '0', 'selected' => true),
							array('title' => 'Com Registro', 'value' => '1'),
							array('title' => 'Sem Registro', 'value' => '2'),
							array('title' => 'DDA', 'value' => '3')
						)
					),
					'D007_Numero_Convenio' => array('name' => 'D007_Numero_Convenio', 'label' => 'Número Convênio', 'type' => 'text', 'length' => array(20,20)),
					'D007_Tipo_Carteira_Banco_Brasil' => array('name' => 'D007_Tipo_Carteira_Banco_Brasil', 'label' => 'Carteira', 'type' => 'text', 'length' => array(10,10)),
					'D007_Numero_Contrato' => array('name' => 'D007_Numero_Contrato', 'label' => 'Número do Contrato', 'type' => 'text', 'length' => array(10,10)),
					'D007_Codigo_Baixa' => array('name' => 'D007_Codigo_Baixa', 'label' => 'Código da Baixa', 'type' => 'text', 'length' => array(30,30)),
					'D007_Nosso_Numero' => array('name' => 'D007_Nosso_Numero', 'label' => 'Nosso Número(Último)', 'type' => 'text', 'length' => array(15,15)),
					'D007_Aceite' => array('name' => 'D007_Aceite', 'label' => 'Aceite', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D007_Especie' => array('name' => 'D007_Especie', 'label' => 'Espécie', 'type' => 'text','value' => 'R$', 'length' => array(30,30)),
					'D007_Especie_Documento' => array('name' => 'D007_Especie_Documento', 'label' => 'Espécie Doc', 'type' => 'text', 'length' => array(30,30)),
					'D007_Uso_Banco' => array('name' => 'D007_Uso_Banco', 'label' => 'Uso do banco', 'type' => 'text', 'length' => array(30,30)),
					'D007_Percentual_Juros_Dia' => array('name' => 'D007_Percentual_Juros_Dia', 'label' => '% Juros por MÊS', 'type' => 'moeda', 'length' => array(15,15)),
					'D007_Percentual_Multa' => array('name' => 'D007_Percentual_Multa', 'label' => '% Multa após o vencimento', 'type' => 'moeda', 'length' => array(15,15)),
					'D007_Instrucoes_Boleto' => array('name' => 'D007_Instrucoes_Boleto', 'label' => 'Instruções Boleto', 'type' => 'textarea', 'length' => array(3,60)),
					'D007_C004_Id' => array('name' => 'D007_C004_Id', 'label' => 'EmpresaAtual', 'type' => 'hidden', 'value'=>$g['empresaAtual']),
					'D007_Instrucao_Protesto_1' => array('name' => 'D007_Instrucao_Protesto_1', 'label' => 'Instr.Protesto 1', 'type' => 'text', 'length' => array(30,30)),
					'D007_Instrucao_Protesto_2' => array('name' => 'D007_Instrucao_Protesto_2', 'label' => 'Instr.Protesto 2', 'type' => 'text', 'length' => array(30,30)),
					'D007_Instrucao_Cobranca_2' => array('name' => 'D007_Instrucao_Cobranca', 'label' => 'Instr.Cobrança', 'type' => 'text', 'length' => array(30,30)),
					'D007_Instrucoes_Protesto' => array('name' => 'D007_Instrucoes_Protesto', 'label' => 'Instruções <br>de Protesto', 'type' => 'textarea', 'length' => array(3,60)),
					'D007_Flag_Tipo_Multa' => array('name' => 'D007_Flag_Tipo_Multa', 'label' => 'Formato da multa no boleto', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Valor', 'value' => 'V', 'selected' => true),
							array('title' => 'Porcentagem', 'value' => 'P'),							
						)
					),
					'D007_Variacao_Carteira' => array('name' => 'D007_Variacao_Carteira', 'label' => 'Variação da Carteira', 'type' => 'text', 'length' => array(10,10)),
					'D007_Tipo_Cobranca' => array('name' => 'D007_Tipo_Cobranca', 'label' => 'Tipo de Cobrança (02VIN para vinculada)', 'type' => 'text', 'length' => array(10,10)),
					'D007_Numero_Sequencial_Remessa' => array('name' => 'D007_Numero_Sequencial_Remessa', 'label' => 'Nº Sequencial <br>Remessa (Último)', 'type' => 'text', 'length' => array(10,10)),
					'D007_Codigo_Cooperativa' => array('name' => 'D007_Codigo_Cooperativa', 'label' => 'Código Cooperativa', 'type' => 'hidden', 'length' => array(12,12)),
					'D007_Digito_Verificador_Cooperativa' => array('name' => 'D007_Digito_Verificador_Cooperativa', 'label' => 'Dígito Cooperativa', 'type' => 'hidden', 'length' => array(1,3)),
					'D007_Codigo_Cliente' => array('name' => 'D007_Codigo_Cliente', 'label' => 'Código Cliente', 'type' => 'text', 'length' => array(10,10)),
					'D007_Digito_Verificador_Cliente' => array('name' => 'D007_Digito_Verificador_Cliente', 'label' => 'Dígito Verificador', 'type' => 'hidden', 'length' => array(1,3)),
					'D007_Baixar_Automatico_Valor_Excedido_Retorno' => array('name' => 'D007_Baixar_Automatico_Valor_Excedido_Retorno', 'label' => 'Baixa automatica com valores excedidos no retorno <br />(adicionado em acréscimos/descontos)', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D007_Valor_Maximo_Boleto_Sem_Registro' => array('name' => 'D007_Valor_Maximo_Boleto_Sem_Registro', 'label' => 'Valor máximo do boleto sem registro', 'type' => 'moeda', 'length' => array(20,30)),
					'D007_Modelo_Layout_Boleto' => array('name' => 'D007_Modelo_Layout_Boleto', 'label' => 'Modelo Layout Boleto', 'type' => 'select', 'type' => 'select', 'select' => 
						array(
							array('title' => '1 Boleto por página', 'value' => ''),
							array('title' => '3 Boletos por página', 'value' => '2'),
						)
					),
					'D007_Layout_Cobranca' => array('name' => 'D007_Layout_Cobranca', 'label' => 'Layout Cobrança', 'type' => 'select', 'type' => 'select', 'select' => 
						array(
							array('title' => 'CNAB400', 'value' => '1'),
							array('title' => 'CNAB240', 'value' => '2'),
						)
					),
					'D007_Nome_Cooperativa' => array('name' => 'D007_Nome_Cooperativa', 'label' => 'Nome Cooperativa', 'type' => 'text', 'length' => array(20,30)),
				)
			)
		);
        $form = new Formulario('formcad014FormPrincipal');
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
						echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso! - ');
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Título da Janela - ID: ' + '{$r_acaoId}', '/cad/cad014/content/cad014ContentNome/', '&tab=geral&acaoId=' + encodeURIComponent('{$r_acaoId}'));
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
	break;

	case 'cad014FormCadastroBanco':
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
		$form_tabela = 'D062';
		$form_campo_pk = 'D062_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad014FormCadastroBanco_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad014/form/cad014FormCadastroBanco/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"D062_Nome_Banco":{"top":7,"left":27.671875,"width":360,"hidden":0},"D062_Numero_Banco":{"top":30,"left":11.875,"width":128,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad014FormCadastroBanco');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D062_Nome_Banco' => array('name' => 'D062_Nome_Banco','label'=>'Banco', 'type' => 'text', 'length' => array(30,60)),
					'D062_Numero_Banco' => array('name' => 'D062_Numero_Banco','label'=>'Número', 'type' => 'text', 'length' => array(30,60)),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),

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
?>
