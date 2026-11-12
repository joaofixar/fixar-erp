<?php
namespace hardness;
/**
* Weiberlan
* 28/11/2011
*/

/**Classe
* 
* require_once("bibliotecas/classes/cad026.php");
* $cad026 = new cad026();
*/

/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'cad026FormCadastroLinha':
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
		$form_tabela = 'D003';
		$form_campo_pk = 'D003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad026FormCadastroLinha_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad026/form/cad026FormCadastroLinha/';
		$form_editor = array(
			'geral' => '{"D003_Nome_Linha":{"top":7,"left":32,"width":296,"hidden":0},"D003_C008_Id":{"top":53,"left":661,"width":200,"hidden":1},"D003_D082_Id":{"top":99,"left":629,"width":200,"hidden":1},"D003_Codigo_Padrao":{"top":145,"left":626,"width":224,"hidden":1},"D003_Descricao_Padrao":{"top":191,"left":626,"height":42,"width":197,"hidden":1}}'
		);

		// novo formulário
		$form = new Formulario('cad026FormCadastroLinha');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D003_C008_Id' => array('name' => 'Consulta_D082_C008_Id','label'=>'Tipo','extra' =>'style="width:200px"', 'type' => 'select',
						'select' => gGeraSelect('C008', 'C008_Id', 'C008_Tipo')
					),
                    'D003_D082_Id' => array('name' => 'D003_D082_Id','label'=>'Marca Padrão','extra' =>'style="width:200px"', 'type' => 'select', 
                          'select' => gGeraSelect('D082','D082_Id','D082_Marca')
                      ),
					'D003_Nome_Linha' => array('name' => 'D003_Nome_Linha','label'=>'Grupo', 'type' => 'text', 'length' => array(33,60)),
					/*
					'D003_Flag_Visualizar_Site' => array('name' => 'D003_Flag_Visualizar_Site','label'=>'Visualizar no Site?','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "S", 'selected' => true),
							array('title' => 'Não', 'value' => "N")
						)
					),
                    */
					
					'D003_Codigo_Padrao' => array('name' => 'D003_Codigo_Padrao','label'=>'Código Padrão', 'type' => 'text', 'length' => array(33,30)),
  					'D003_Descricao_Padrao' => array('name' => 'D003_Descricao_Padrao','label'=>'Descrição Padrão', 'type' => 'textarea', 'length' => array(3,30)),
					'D003_Integrado_Tray' => array('name' => 'D003_Integrado_Tray', 'label' => 'Integrado Tray', 'type' => 'hidden', 'value' => 'N'),
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

				$form->campoSetarValor("D003_Integrado_Tray", "N", true);

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
		$botoesDir .= '<button type="submit" id="cad026FormCadastroLinha_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad026FormCadastroImagem':
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
		$form_tabela = 'D003';
		$form_campo_pk = 'D003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad026FormCadastroImagem';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad026/form/cad026FormCadastroImagem/';

		// novo formulário
		$form = new Formulario('cad026FormCadastroImagem');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'uploadImagemLinhas' => array('name' => 'uploadImagemLinhas', 'reject'=>true, 'label' => 'Upload Imagem', 'type' => 'file', 'length' => array(10,80)),
					'D003_Imagem' => array('name' => 'D003_Imagem', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false),
  					
				)
			)
		);
		$formArray['campos']['geral']['cad026FormCadastroImagem'] = array('name' => 'cad026FormCadastroImagem', 'type'=>'button', 'reload' => false, 'value' => 'Incluir' , 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

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
				if (true) {
					// Update
					
					// Insert
					$arquivo = $form->campoValorEnviado('uploadImagemLinhas');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						$pathinfo = pathinfo($arquivo['name']);
						$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
						$nomeArquivo = md5(mt_rand() . $arquivo['name']);
						$form->campoSetarValor('D003_Imagem', $nomeArquivo . '.' . $pathinfo['extension'], true);
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Gravar o arquivo no disco
							//produtos/idProduto/fotos/idFoto
							$dir = "{$g['pathDados']}produtos/linhas/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $nomeArquivo . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
							// gera thumbnails da imagem
							gGerarThumb($file, $dir . '/' . $nomeArquivo . '-100.' . $pathinfo['extension'], 100);
							gGerarThumb($file, $dir . '/' . $nomeArquivo . '-200.' . $pathinfo['extension'], 200);
							gGerarThumb($file, $dir . '/' . $nomeArquivo . '-300.' . $pathinfo['extension'], 300);
							gGerarThumb($file, $dir . '/' . $nomeArquivo . '-400.' . $pathinfo['extension'], 400);
							gGerarThumb($file, $dir . '/' . $nomeArquivo . '-500.' . $pathinfo['extension'], 500);
							$alert = gGeraAlertMsg('Operação OK', 'Imagem enviada com sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										{$alert}
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
						}
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
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
