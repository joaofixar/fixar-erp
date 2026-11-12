<?php
namespace hardness;

require_once("bibliotecas/classes/CAD056.php");
$CAD056 = new CAD056();

switch ($g['r'][3]) {
	
	case 'cad056FormBancos':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
		$form_tabela = 'D062';
		$form_campo_pk = 'D062_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad056FormBancos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad056/form/cad056FormBancos/';

		// novo formulário
		$form = new Formulario('cad056FormBancos');
		// Editor
		$form_editor = array(
			'geral' => '{"D062_Nome_Banco":{"top":7,"left":14,"width":264,"hidden":0},"D062_Numero_Banco":{"top":30,"left":4,"width":56,"hidden":0},"cad056FormBancos_btn":{"top":40,"left":304,"width":null,"hidden":0}}',
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D062_Id' => array('name' => 'D062_Id', 'label'=>'id', 'type' => 'hidden', 'value' => $r_acaoId),
					'D062_Numero_Banco' => array('name' => 'D062_Numero_Banco', 'label'=>'Número do Banco', 'type' => 'text'),
					'D062_Nome_Banco' => array('name' => 'D062_Nome_Banco', 'label'=>'Nome do Banco', 'type' => 'text'),
				)
			)
		);
		$formArray['campos']['geral']['cad056FormBancos_btn'] = array('name' => 'cad056FormBancos_btn', 'type' => 'submit', 'reload' => false, 'reject' => true, 'value' => (!empty($r_acaoId)) ? 'Enviar' : 'Incluir');
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
				$D062_Numero_Banco = $form->campoValorEnviado('D062_Numero_Banco');
				$return = $CAD056->verificarCodigoBanco($D062_Numero_Banco, $r_acaoId);
				if($return === true){
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											//divRefresh('{$g['divId']}');
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
							echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											//divRefresh('{$g['divId']}');
										}, 150);
									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
						}
					}
				} else {
					echo gGeraAlertMsg('Erro', 'Já existe um banco com este número!');
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
