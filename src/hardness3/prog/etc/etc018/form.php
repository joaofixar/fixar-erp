<?php
namespace hardness;

// Modo desenvolvedor é utilizado a tabela D183, no modo normal a C039 - na execução é feito o merge das duas
$tabela = $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'] ? 'D183' : 'C039';

/**
 * Ações
 */
switch ($g['r'][3]) {
	case 'etc018FormEditarCrontab':
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
		$form_tabela = $tabela;
		$form_campo_pk = $tabela . '_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc018FormEditarCrontab_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc018/form/etc018FormEditarCrontab/';

		// novo formulário
		$form = new Formulario('etc018FormEditarCrontab');
		$form_editor = array(
            'geral' => '{"'.$tabela.'_Nome":{"top":7,"left":61.015625,"width":200,"hidden":0},"'.$tabela.'_Crontab":{"top":30,"left":48.125,"width":111,"hidden":0},"'.$tabela.'_Crontab_Extra":{"top":53,"left":20.625,"width":104,"hidden":0},"'.$tabela.'_Codigo":{"top":76,"left":55.859375,"width":360,"hidden":0},"'.$tabela.'_Codigo_Sql":{"top":99,"left":70.59375,"height":315,"width":760,"hidden":0}}'
        );

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
                    $tabela.'_Nome' => array('name' => $tabela.'_Nome', 'label' => 'Nome', 'type' => 'text'),
                    $tabela.'_Codigo' => array('name' => $tabela.'_Codigo', 'label' => 'Código', 'type' => 'text', 'uppercase' => false),
					$tabela.'_Codigo_Sql' => array('name' => $tabela.'_Codigo_Sql', 'label' => 'SQL', 'type' => 'textarea', 'uppercase' => false, 'code' => 'sql', 'codeModoSimples' => true),
                    $tabela.'_Crontab' => array('name' => $tabela.'_Crontab', 'label' => 'Intervalo', 'type' => 'select', 'select' =>
                        array(
                            array('title' => 'Dia', 'value' => 'DIA', 'selected' => true),
                            array('title' => 'Hora', 'value' => 'HORA'),
                            array('title' => 'Minuto', 'value' => 'MINUTO'),
                        ),
                    ),
                    $tabela.'_Crontab_Extra' => array('name' => $tabela.'_Crontab_Extra', 'label' => 'Intervalo Extra', 'type' => 'text'),
				)
			)
		);
        if ($g['modoDesenvolvedorApp']) {
            $formArray['campos']['geral'][$tabela.'_App_Id'] = array('name' => $tabela.'_App_Id', 'type' => 'hidden', 'value' => $g['modoDesenvolvedorApp'], 'uppercase' => false);
            $formArray['campos']['geral'][$tabela.'_Desenvolvimento'] = array('name' => $tabela.'_Desenvolvimento', 'type' => 'hidden', 'value' => 1);
        }

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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
}