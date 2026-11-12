<?php
namespace hardness;
//Classes
//require_once("bibliotecas/classes/VEN011.php");
//$VEN011 = new VEN011();
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: ven011tab02
	 */
	case 'ven011form01':
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
		$form_tabela = 'T142';
		$form_campo_pk = 'T142_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn_ven010';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven011/form/ven011form01/';
		
		@$r_D106_Id = isset($r_form_ven011_form1) ? $r_form_ven011_form1['T142_D106_Id'] : (isset($r_D106_Id)?$r_D106_Id : null);
		$form = new Formulario('form_ven011_form1');
		$formArray = array(
			'campos' => array(
				'manutencao_bem' => array(
				)
			)
		);
		$formArray['campos']['manutencao_bem']['T142_C004_Id'] 			= array('name' => 'T142_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']);
		
		if (!$r_D106_Id){
			$formArray['campos']['manutencao_bem']['T142_D106_Id'] 		= array('name' => 'Consulta_T142_D106_Id','label'=>'Bem', 'type' => 'pesquisa','pesquisa'=>array('D106','D106_Id','D106_Descricao'), 'length' => array(65,30));
		} else {			
			$tmp = gGeraSelect('D106', 'D106_Id', 'D106_Descricao', "WHERE D106_Id = $r_D106_Id");
			$formArray['campos']['manutencao_bem']['__T142_D106_Id'] 		= array('name' => '__T142_D106_Id', 'label'=>'Bem', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
				$tmp
			);
			$formArray['campos']['manutencao_bem']['T142_D106_Id'] 			= array('name' => 'T142_D106_Id', 'label' => 'T142_D106_Id', 'type' => 'hidden', 'value' => $r_D106_Id);
		}
		$formArray['campos']['manutencao_bem']['T142_Data_Manutencao'] 		= array('name' => 'T142_Data_Manutencao', 'label'=>'Data Manutenção','type' => 'data', 'length' => array(20,10));
		$formArray['campos']['manutencao_bem']['T142_Descricao'] 		= array('name' => 'T142_Descricao', 'label'=>'Descrição', 'type' => 'textarea', 'length' => array(2,60));
		$formArray['campos']['manutencao_bem']['T142_Valor'] 	= array('name' => 'T142_Valor', 'label'=>'Valor Manutenção', 'type' => 'moeda', 'length' => array(20,10));
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn_ven010">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
}
?>
