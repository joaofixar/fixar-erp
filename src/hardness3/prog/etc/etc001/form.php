<?php
namespace hardness;
// Classes
require_once("bibliotecas/classes/FIN001.php");
$FIN001 = new FIN001();
// Carrega funções do ajax
require_once("prog/fin/fin001/form_func-js.php");

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: fin001tab02
	*/
	case 'etc001_form_urls':		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;

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
		$form_tabela = 'T144';
		$form_campo_pk = 'T144_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc001_form_btn_enviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc001/form/etc001_form_urls/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T144_Flag_Tipo":{"top":6.92401123046875,"left":36.91790771484375,"width":207,"hidden":1},"T144_Url":{"top":29.947906494140625,"left":28.23223876953125,"width":456.039216,"hidden":0},"T144_Descricao":{"top":52.95648193359375,"left":9.72735595703125,"height":44.039215999999996,"width":456.039216,"hidden":0}}',
        );
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$r_D001_Id = isset($r_D001_Id) ? $r_D001_Id : '';
		$form = new Formulario('formetc001urls');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T144_D001_Id' => array('name' => 'T144_D001_Id','label' => 'T144_D001_Id', 'type' => 'hidden', 'value' => $r_D001_Id),
					'T144_Data_Cadastro' => array('name' => 'T144_Data_Cadastro','label' => 'dataCadastro', 'type' => 'hidden', 'value' => date('Y/m/d')),
					'T144_Url' => array('name' => 'T144_Url','label' => 'Link:', 'type' => 'text', 'length' => array(85,255), 'uppercase' => false),
					'T144_Descricao' => array('name' => 'T144_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(3,60)),
					'T144_Flag_Tipo' => array('name' => 'T144_Flag_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selectd' => true),
							array('title' => 'Link Orçamento/Pedido', 'value' => 'V'),
							array('title' => 'Foto Sistema', 'value' =>'F'),
						)						
					),
				),	
			)
		);
		//print_r($formArray);
		//$formArray['campos']['geral']['T144_D001_Id'] 	    = array('name' => 'T144_D001_Id','label' => 'T144_D001_Id', 'type' => 'hidden', 'value' => $r_D001_Id);
		//$formArray['campos']['geral']['T144_Data_Cadastro'] = array('name' => 'T144_Data_Cadastro','label' => 'dataCadastro', 'type' => 'hidden', 'value' => date('Y/m/d'));
		//$formArray['campos']['geral']['T144_Url'] 			= array('name' => 'T144_Url','label' => 'Link:', 'type' => 'text', 'length' => array(85,255), 'uppercase' => false);
		//$formArray['campos']['geral']['T144_Descricao'] 	= array('name' => 'T144_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(3,60));
		
		//print_r($formArray['campos']['geral']);
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
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc001_form_btn_enviar">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
	break;
}
?>
