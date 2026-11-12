<?php
namespace hardness;
/**
 * Weiberlan
 * 28/11/2011
 */
 
/**Classe
* 
* require_once("bibliotecas/classes/fis009.php");
* $fis009 = new fis009();
*/


/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'fis010FormCadastroSimplesNacional':
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
		$form_tabela = 'T134';
		$form_campo_pk = 'T134_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis010FormCadastroSimplesNacional_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis010/form/fis010FormCadastroSimplesNacional/';
		$form_editor = array(
			'geral' => '{"T134_Tipo":{"top":7,"left":132.890625,"width":88,"hidden":0},"T134_Faixa_Inicial":{"top":30,"left":95.796875,"width":86,"hidden":0},"T134_Faixa_Final":{"top":53,"left":100.078125,"width":86,"hidden":0},"T134_Aliquota":{"top":76,"left":114.125,"width":86,"hidden":0},"T134_IRPJ":{"top":99,"left":130.046875,"width":86,"hidden":0},"T134_CSLL":{"top":122,"left":126.359375,"width":86,"hidden":0},"T134_COFINS":{"top":145,"left":112.328125,"width":86,"hidden":0},"T134_PIS":{"top":168,"left":136.15625,"width":86,"hidden":0},"T134_INSS_CPP":{"top":191,"left":102.53125,"width":86,"hidden":0},"T134_ICMS":{"top":214,"left":126.375,"width":86,"hidden":0},"T134_IPI":{"top":237,"left":140.4375,"width":86,"hidden":0},"T134_Reducao_Base_ICMS":{"top":260,"left":11.421875,"width":86,"hidden":0}}');
		// novo formulário
		$form = new Formulario('fis010FormCadastroSimplesNacional');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T134_Faixa_Inicial' => array('name' => 'T134_Faixa_Inicial','label'=>'Faixa Inicial', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_Faixa_Final' => array('name' => 'T134_Faixa_Final','label'=>'Faixa Final', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_Aliquota' => array('name' => 'T134_Aliquota','label'=>'Aliquota', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_IRPJ' => array('name' => 'T134_IRPJ','label'=>'IRPJ', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_CSLL' => array('name' => 'T134_CSLL','label'=>'CSLL', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_COFINS' => array('name' => 'T134_COFINS','label'=>'COFINS', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_PIS' => array('name' => 'T134_PIS','label'=>'PIS', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_INSS_CPP' => array('name' => 'T134_INSS_CPP','label'=>'INSS/CPP', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_ICMS' => array('name' => 'T134_ICMS','label'=>'ICMS', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_IPI' => array('name' => 'T134_IPI','label'=>'IPI', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_Reducao_Base_ICMS' => array('name' => 'T134_Reducao_Base_ICMS','label'=>'Reducao Base Cálculo ICMS', 'type' => 'moeda', 'length' => array(10,15)),
					'T134_Tipo' => array('name' => 'T134_Tipo','label'=>'Tipo', 'type' => 'text'),
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
		$botoesDir .= '<button type="submit" id="fis010FormCadastroSimplesNacional_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
