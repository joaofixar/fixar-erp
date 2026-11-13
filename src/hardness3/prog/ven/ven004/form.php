<?php
namespace hardness;
/**
* Formulário
* /fis/fis001/form/
*/

/**
* Ações
*/
switch ($g['r'][3]) {

	case 'form1':
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
			$formId = (isset($r_formfis001form1)) ? $r_formfis001form1['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D006';
		$form_campo_pk = 'D006_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis001/form/form1/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formfis001form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Codigo_CFOP' => array('name' => 'D006_Codigo_CFOP', 'label'=>'Código CPOF', 'type' => 'text','length' => array(6,6)),
		        	'D006_Descricao' => array('name' => 'D006_Descricao', 'label'=>'Descrição', 'type' => 'text','length' => array(50,50)),
					'D006_Flag_Entrada_Saida' => array('name' => 'D006_Flag_Entrada_Saida', 'label'=>'Saida/Entrada', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Saida', 'value' => 'S', 'selected' => true),
							array('title' => 'Entrada', 'value' => 'E'),
						)
					),
					'D006_Flag_Estado' => array('name' => 'D006_Flag_Estado', 'label'=>'Estado', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Dentro do Estado', 'value' => 'D', 'selected' => true),
							array('title' => 'Fora do Estado', 'value' => 'F'),
							array('title' => 'Internacional', 'value' => 'I'),
						)
					),					
					'D006_Flag_Devolucao' => array('name' => 'D006_Flag_Devolucao', 'label'=>'Devolução', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S' ),
						)
					),
					'D006_Flag_Venda_Compra_Outros' => array('name' => 'D006_Flag_Venda_Compra_Outros', 'label'=>'Tipo', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Venda', 'value' => 'V', 'selected' => true),
							array('title' => 'Compra', 'value' => 'C' ),
							array('title' => 'Transferencia', 'value' => 'T' ),
							array('title' => 'Outros', 'value' => 'O' ),
							array('title' => 'Devolução Vendas', 'value' => 'DV' ),
							array('title' => 'Devolução Compras', 'value' => 'DC' ),
						)
					),					
					'D006_Flag_Departamento' => array('name' => 'D006_Flag_Departamento', 'label'=>'Departamento', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Comercial', 'value' => 'COMERCIAL', 'selected' => true),
							array('title' => 'Administração', 'value' => 'ADMINISTRACAO' ),
						)
					),					
					'D006_Observacao' => array('name' => 'D006_Observacao', 'label'=>'Observação', 'type' => 'textarea','length' => array(5,65)),
					'D006_Flag_Inativo' => array('name' => 'D006_Flag_Inativo', 'label'=>'Inativar CFOP', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S' ),
						)
					),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
                ),
				
				'estoque' => array(
					'D006_Flag_Estoque' => array('name' => 'D006_Flag_Estoque', 'label'=>'Estoque', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Não altera estoque', 'value' => 'N', 'selected' => true),
							array('title' => 'Credita', 'value' => 'C'),
							array('title' => 'Debita', 'value' => 'D'),
						),
					),
					'D006_Flag_MMF' => array('name' => 'D006_Flag_MMF', 'label'=>'Min, Max e Frequencia', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Atualiza_Custo' => array('name' => 'D006_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Atualiza_Custo' => array('name' => 'D006_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
				),
				
				'financeiro' => array(
					'D006_Flag_Gera_Contas' => array('name' => 'D006_Flag_Gera_Contas', 'label'=>'Gera contas', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Não gera contas', 'value' => 'N', 'selected' => true),
							array('title' => 'Receber', 'value' => 'R'),
							array('title' => 'Pagar', 'value' => 'P'),
						),
					),
					'D006_D014_Id' => array('name' => 'D006_D014_Id', 'label'=>'Conta', 'type' => 'pesquisa', 'pesquisa' => array('D014','D014_Id','D014_SubConta,D154_Descricao','LEFT JOIN D154 ON D154_Id=D014_D154_Id') ),
                ),
				
				'retorno' => array(
					'D006_Flag_Retorno' => array('name' => 'D006_Flag_Retorno', 'label'=>'Possui retorno', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Numero_Dias_Retorno' => array('name' => 'D006_Numero_Dias_Retorno', 'label'=>'Dias para retorno', 'type' => 'text', 'length' => array(5,5)), 
                ),				
                
				'icms' => array(
					'D006_Flag_Isento_ICMS' => array('name' => 'D006_Flag_Isento_ICMS', 'label'=>'Isento ICMS', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Somar_IPI_Base_ICMS' => array('name' => 'D006_Flag_Somar_IPI_Base_ICMS', 'label'=>'Somar IPI na base', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Deb_Crd_ICMS' => array('name' => 'D006_Flag_Deb_Crd_ICMS', 'label'=>'Gerar base Sintegra', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					
					'D006_Flag_Deb_Crd_ICMS' => array('name' => 'D006_Flag_Deb_Crd_ICMS', 'label'=>'Gerar base Sintegra', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Percentual_Reducao_ICMS' => array('name' => 'D006_Percentual_Reducao_ICMS', 'label'=>'Redução base cálculo', 'type' => 'text', 'length' => array(5,5)),
					'D006_D059_Id' => array('name' => 'D006_D059_Id', 'label'=>'Conta', 'type' => 'select', 'extra'=>'style="width:250px;"','select' => 
						gGeraSelect('D059', 'D059_Id', 'D059_Descricao_Origem',"where D059_Simples_Nacional='N'"),
					),
					
				),				
                
				'ipi' => array(
					'D006_Flag_Isento_IPI' => array('name' => 'D006_Flag_Isento_IPI', 'label'=>'Isento IPI', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Somar_Despesas_Base_IPI' => array('name' => 'D006_Flag_Somar_Despesas_Base_IPI', 'label'=>'Somar Despesas na base', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Credita_IPI' => array('name' => 'D006_Flag_Credita_IPI', 'label'=>'Gerar base Sintegra', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					
                ),	
                			
				'st' => array(
					'D006_Flag_Substituicao_Tributaria' => array('name' => 'D006_Flag_Substituicao_Tributaria', 'label'=>'Possui ST', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_ST_VA' => array('name' => 'D006_Flag_ST_VA', 'label'=>'Agrega MVA', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
                ),	
                			
				'pis' => array(
					'D006_Flag_Isento_PIS' => array('name' => 'D006_Flag_Isento_PIS', 'label'=>'Isento PIS', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Forma_Calculo_PIS' => array('name' => 'D006_Flag_Forma_Calculo_PIS', 'label'=>'Isento PIS', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Venda', 'value' => 'V', 'selected' => true),
							array('title' => 'Lucro', 'value' => 'L'),
						),
					),
					'D006_Situacao_Tributaria_PIS' => array('name' => 'D006_Situacao_Tributaria_PIS', 'label'=>'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
						),
					),
					
				),	
                			
				'cofins' => array(
					'D006_Flag_Isento_COFINS' => array('name' => 'D006_Flag_Isento_COFINS', 'label'=>'Isento COFINS', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Forma_Calculo_COFINS' => array('name' => 'D006_Flag_Forma_Calculo_COFINS', 'label'=>'Isento COFINS', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Venda', 'value' => 'V', 'selected' => true),
							array('title' => 'Lucro', 'value' => 'L'),
						),
					),
					'D006_Situacao_Tributaria_COFINS' => array('name' => 'D006_Situacao_Tributaria_COFINS', 'label'=>'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
						),
					),
                ),    
            ) // 'campos'
		);
		$form->definirFormularioArray($formArray);
		
		
		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
						echo  $processado;
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
		if (!$formMultiTab) { echo  "<form id=\"{$idTagForm}\">"; }
		echo  $g['smarty']->fetch('form.tpl');
	break;
	
}

?>
