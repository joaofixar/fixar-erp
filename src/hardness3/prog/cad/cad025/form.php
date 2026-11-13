<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */
 
/**Classe
* 
* require_once("bibliotecas/classes/cad025.php");
* $cad025 = new cad025();
*/


/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'cad025FormCadastroMarca':
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
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D082';
		$form_campo_pk = 'D082_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad025FormCadastroMarca_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad025/form/cad025FormCadastroMarca/';
		$form_editor = array(
			'geral' => '{"D082_Marca":{"top":7,"left":43.3125,"width":200,"hidden":0},"D082_C008_Id":{"top":30,"left":52.890625,"width":207,"hidden":0},"D082_Codigo_Padrao":{"top":53,"left":0.5,"width":201,"hidden":0},"D082_Percentual_Acrescimo":{"top":76,"left":5.4375,"width":200,"hidden":0},"D082_Flag_Ativo":{"top":99,"left":49.421875,"width":49,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad025FormCadastroMarca');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D082_C008_Id' => array('name' => 'Consulta_D082_C008_Id','label'=>'Tipo', 'type' => 'select',
						'select' => gGeraSelect('C008', 'C008_Id', 'C008_Tipo')
					),
					'D082_Marca' => array('name' => 'D082_Marca','label'=>'Marca', 'type' => 'text', 'length' => array(30,40)),
					'D082_Codigo_Padrao' => array('name' => 'D082_Codigo_Padrao','label'=>'Código Padrão', 'type' => 'text', 'length' => array(30,30)),
  					'D082_Percentual_Acrescimo' => array('name' => 'D082_Percentual_Acrescimo','label'=>'Acréscimo(%)', 'type' => 'moeda', 'length' => array(30,30)),
  					'D082_Flag_Ativo' => array('name' => 'D082_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select', 'select' => 
  						array(
  							array('title' => 'SIM', 'value' => 'S'),
  							array('title' => 'NÃO', 'value' => 'N')
  						)
  					)
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad025FormCadastroMarca_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
