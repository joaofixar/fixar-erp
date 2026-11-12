<?php
namespace hardness;

switch ($g['r'][3]) {
	
	case 'cadastro':
		$exemplos = <<<EOT
*** Janela ***
abrirJanela(false, divIdRootAberto(), '', menuId('cad030b'), 'Usuário', '.: Usuário:.', '/cad/cad030/content/cad030ContentCadastroUsuarioAtual/', '', [950,550], false, false, 'center'); 

*** Janela externa ***
// nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id=' + T005_Id,0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');

*** Dialog de confirmação ***
var buttons = {
	"Sim": function() {
		$.getJSON('/sistema/funcoes/controleFormulario/duplicarRegistro/?ajax=true&acaoId=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
			//
		});
		$(this).dialog("destroy").remove();
	},
	"Não": function() { $(this).dialog("destroy").remove(); } 
};
dialogConfirm('Alerta', 'Mensagem de alerta? ', buttons);
EOT;
	
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
		$form_tabela = 'D142';
		$form_campo_pk = 'D142_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad055FormAtalho_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad055/form/cadastro/';

		// novo formulário
		$form = new Formulario('cad055FormCadastroROta');
		// Editor
		$form_editor = array(
			'geral' => '{"D142_Atalho":{"top":7,"left":32,"width":200,"hidden":0},"D142_Nome":{"top":30,"left":79,"width":168,"hidden":0},"D142_C029_Id":{"top":53,"left":56,"width":352,"hidden":0},"D142_Codigo":{"top":76,"left":58,"height":129,"width":520,"hidden":0}}',
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D142_Atalho' => array('name' => 'D142_Atalho','label'=>'Tecla de Atalho', 'type' => 'text', 'length' => array(30,30), 'uppercase' => false),
					'D142_Nome' => array('name' => 'D142_Nome','label'=>'Nome', 'type' => 'text', 'length' => array(30,50), 'uppercase' => false),
					//'D142_Descricao' => array('name' => 'D142_Descricao','label'=>'Descrição', 'type' => 'text', 'length' => array(60,250), 'uppercase' => false),
					'D142_Codigo' => array('name' => 'D142_Codigo','label'=>'Código JS', 'type' => 'textarea', 'uppercase' => false, 'value' => $exemplos),
					'D142_C029_Id' => array('name' => 'D142_C029_Id', 'label'=>'Permissão', 'type' => 'select', 'uppercase' => false, 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('C029', 'C029_Id', 'C029_Descricao_Acesso, C029_Id', '', true)
					),
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
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
		$botoesDir .= '<button type="submit" id="cad055FormAtalho_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
