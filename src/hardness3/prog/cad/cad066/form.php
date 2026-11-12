<?php
namespace hardness;    
require_once('prog/cad/cad066/form_func-js.php');    
switch ($g['r'][3]) {
	/**
	* @author Lígia Nakazato
	*/
	case 'cad066FormCadastrarSeguradoras':
		global $g;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		$WHERE_D020 = "WHERE D020_D018_Id = '0'";
		if(!empty($acaoId)) {
			$D172 = mysql_fetch_array(mysql_query("SELECT D172_D018_Id FROM D172 WHERE D172_Id = '{$acaoId}'"));
			if($D172['D172_D018_Id'] > 0){
				$WHERE_D020 = "WHERE D020_D018_Id = '{$D172['D172_D018_Id']}'";
			}
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D172';
		$form_campo_pk = 'D172_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad066FormCadastrarSeguradorasBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad066/form/cad066FormCadastrarSeguradoras/';

		// Editor
		$form_editor = array(
			'seguradora' => '{"D172_Flag_Ativo":{"top":191,"left":49.421875,"width":96,"hidden":0},"D172_Nome":{"top":30,"left":16.390625,"width":344,"hidden":0},"D172_CNPJ":{"top":7,"left":45.15625,"width":200,"hidden":0},"D172_D018_Id":{"top":53,"left":59.21875,"width":63,"hidden":0},"D172_D020_Id":{"top":76,"left":39.015625,"width":207,"hidden":0},"D172_DDD_Telefone":{"top":99,"left":32.296875,"width":30,"hidden":0},"D172_Telefone":{"top":99,"left":120,"width":100,"hidden":0},"D172_Informacoes_Adicionais":{"top":122,"left":23.734375,"height":62,"width":344,"hidden":0}}',
		);

		// Definição do Formulário
		$form = new Formulario('cad066FormCadastrarSeguradoras');
		$formArray = array(
			'campos' => array(
				'seguradora' => array(
					'D172_Nome' => array('name' => 'D172_Nome', 'label' => 'Seguradora'),
					'D172_CNPJ' => 	array('name' => 'D172_CNPJ', 'label' => 'CNPJ', 'type' => 'cnpj','length' => array(18,18)),	
					'D172_Informacoes_Adicionais' => array('name' => 'D172_Informacoes_Adicionais', 'label' => 'Adicionais','type' => 'textarea', 'length' => array(4,57)),
					'D172_D018_Id' => array('name' => 'D172_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left','extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D172_D020_Id' => array('name' => 'D172_D020_Id', 'label' => 'Cidade', 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $WHERE_D020, true)
					),
					'D172_DDD_Telefone' => array('name' => 'D172_DDD_Telefone',	'label' => 'Telefone','type' => 'text', 'length' => array(1,4)),
					'D172_Telefone' => array('name' => 'D172_Telefone',	'type' => 'text', 'length' => array(14,14)),
					'D172_Flag_Ativo' => array('name' => 'D172_Flag_Ativo', 'label' => 'Ativo','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D172_C004_Id' => array('name' => 'D172_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual'])
				),
			)
		);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
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
						log("entra aqui");
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad066FormCadastrarSeguradorasBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad066FormCadastrarApolices':
		global $g;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D173';
		$form_campo_pk = 'D173_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad066FormCadastrarApoliceBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad066/form/cad066FormCadastrarApolices/';

		// Editor
		$form_editor = array(
			'geral' => '{"D173_Flag_Ativo":{"top":122,"left":58.09375,"width":96,"hidden":0},"D173_Identificacao":{"top":7,"left":20.78125,"width":344,"hidden":0},"D173_Numero_Apolice":{"top":30,"left":5.5,"width":146,"hidden":0},"D173_Data_Inicio":{"top":53,"left":37.296875,"width":116,"hidden":0},"D173_Data_Validade":{"top":76,"left":40.953125,"width":116,"hidden":0},"D173_Ramo":{"top":99,"left":60.53125,"width":146,"hidden":0}}',
		);

		// Definição do Formulário
		$form = new Formulario('cad066FormCadastrarApolices');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D173_Identificacao' => array('name' => 'D173_Identificacao', 'label' => 'Identificação'),
					'D173_Numero_Apolice' => array('name' => 'D173_Numero_Apolice', 'label' => 'Número apólice'),
					'D173_Data_Inicio' => array('name' => 'D173_Data_Inicio', 'label'=>'Data Início', 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D173_Data_Validade' => array('name' => 'D173_Data_Validade', 'label'=>'Válido até', 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D173_Ramo' => array('name' => 'D173_Ramo', 'label' => 'Ramo'),
					'D173_D172_Id' => array('name' => 'D173_D172_Id','value' =>$r_seguradora, 'type' => 'hidden'),
					'D173_Flag_Ativo' => array('name' => 'D173_Flag_Ativo', 'label' => 'Ativo','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
			)// 'campos'
		);
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
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
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	case 'cad066FormCadastrarAverbacoes':
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
		$form_tabela = 'D174';
		$form_campo_pk = 'D174_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad066CadastrarAverbacaobtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad066/form/cad066FormCadastrarAverbacoes/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"D174_Descricao":{"top":7,"left":56.96875,"width":146,"hidden":0},"D174_Numero_Averbacao":{"top":30,"left":11.3125,"width":146,"hidden":0}}'
		);
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad066FormCadastrarAverbacoes');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D174_Descricao' => array('name' => 'D174_Descricao', 'label' => 'Descrição'),
					'D174_Numero_Averbacao' => array('name' => 'D174_Numero_Averbacao', 'label' => 'Número Averbação'),
					'D174_D173_Id' => array('name' => 'D174_D173_Id', 'type' => 'hidden', 'value' => $r_D173_Id),
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
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									divRefresh('{$g['divId']}', true);
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
  								});
  								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
  								divRefresh('{$r_divRefresh}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {	
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$r_divRefresh}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad066CadastrarAverbacaobtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
