<?php
namespace hardness;
require_once('prog/cad/cad013/form_func-js.php');

switch ($g['r'][3]) {

	case 'cad013FormEditarContatos':
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
		$form_tabela = 'D117';
		$form_campo_pk = 'D117_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad013FormEditarContatosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad013/form/cad013FormEditarContatos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D117_Nome":{"top":7,"left":24,"width":232,"hidden":0},"D117_DDD_Telefone":{"top":30,"left":17,"width":35,"hidden":0},"D117_Telefone":{"top":30,"left":112,"width":75,"hidden":0},"D117_Email":{"top":53,"left":30,"width":232,"hidden":0},"buttonSearchCep":{"top":74,"left":144,"width":null,"hidden":0},"D117_Cep":{"top":76,"left":39,"width":75,"hidden":0},"D117_Endereco":{"top":99,"left":12,"width":232,"hidden":0},"D117_D018_Id":{"top":122,"left":25,"width":64,"hidden":0},"D117_D020_Id":{"top":145,"left":25,"width":182,"hidden":0},"D117_Observacao":{"top":168,"left":4,"height":83,"width":232,"hidden":0}}',
        );
        
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad013_Cadastro');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D117_Nome' => array('name' => 'D117_Nome', 'label'=>'Nome', 'type' => 'text','length' => array(30,60)),
		        	'D117_DDD_Telefone' => array('name' => 'D117_DDD_Telefone', 'label'=>'Telefone', 'type' => 'text', 'length' => array(2, 2)),
					'D117_Telefone' => array('name' => 'D117_Telefone', 'label'=>'', 'type' => 'text', 'length' => array(10, 10), 'align'=>'right'),
					'D117_Email' => array('name' => 'D117_Email', 'label'=>'E-mail', 'type' => 'text', 'length' => array(30, 60)),
					'D117_Cep' => array('name' => 'D117_Cep', 'label'=>'CEP', 'type' => 'cep', 'length' => array(10, 10)),
                    'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D117_Cep\", {\"EnderecoDb\": \"D117_Endereco\", \"Cep\": \"D117_Cep\", \"UfDb\": \"D117_D018_Id\",\"CidadeDb\": \"D117_D020_Id\"})'", 'align'=>'right', 'value'=>'Buscar', 'reject'=>true),
                    'D117_Endereco' => array('name' => 'D117_Endereco', 'label'=>'Endereço', 'type' => 'text', 'length' => array(30, 60)),
					'D117_D018_Id' => array('name' => 'D117_D018_Id', 'label'=>'Estado', 'type' => 'select', 'extra' => "onchange=\"alteraEstadoContato(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)
					),
					'D117_D020_Id' => array('name' => 'D117_D020_Id', 'label'=>'Cidade', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', '', true)
					),
					'D117_Observacao' => array('name' => 'D117_Observacao', 'label'=>'Obervação', 'type' => 'textarea', 'length' => array(3, 27)),
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
						$acaoId = $g['mysqlLastId'];
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad013/content/cad013content02Cadastro/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}', '[800,510]'));
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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad013FormEditarContatosBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
}
?>
