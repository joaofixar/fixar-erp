<?php
namespace hardness;

/**
* Ações
*/
switch ($g['r'][3]) {
	
	case 'editarSelect':
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D151';
		$form_campo_pk = 'D151_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'layoutExportacao_content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/layoutExportacao/form/editarSelect/';

		//form editor
		$form_editor = array(
			'geral' => '{"D151_Select":{"top":7,"left":12,"height":384,"width":712,"hidden":0}}', 
		);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('layoutExportacaoForm');  
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D151_Select' => array('name' => 'D151_Select', 'label' => 'Select', 'type' => 'textarea', 'uppercase' => false),
				),						
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
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
	break;

	case 'uploadCSV':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		
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
		$form_tabela = 'XXXX';
		$form_campo_pk = 'XXXX_Id';
                
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('layoutExpBtn', 'cad002FormUploadFotoProdutoBtnCln', 'cad002FormUploadFotoProdutoBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/layoutExportacao/form/uploadCSV/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"uploadCSVArq":{"top":7,"left":12,"width":264,"hidden":0},"layoutExpBtn":{"top":8,"left":368,"width":null,"hidden":0}}'
        );
        
		// Array de definição do formulário
		//$r_D024_Id = isset($r_cad001FormDocumentos) ? $r_cad001FormDocumentos['D087_D024_Id'] : $r_D024_Id;
		
		// $tmp = gGeraSelect('D024', 'D024_Id', 'D024_Nome_Fantasia,D024_Nome_Empresa', " where D024_Id = $r_D024_Id");
		// foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad001FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'uploadCSVArq' => array('name' => 'uploadArquivo', 'label' => 'Arquivo CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false),
					'layoutExpBtn' => array('name' => 'layoutExpBtn', 'type' => 'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true)
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
				$arquivo = $form->campoValorEnviado('uploadCSVArq');
				if ($arquivo['error'] == UPLOAD_ERR_OK) {
					//
					$sql = "DELETE FROM D152 WHERE D152_D151_Id = '{$r_D151_Id}'";
					mysql_query($sql);
					//
					$csvArq = file($arquivo['tmp_name']);
					$D152_Id = false;
					foreach ($csvArq as $linha) {
						$csvLinha = str_getcsv($linha, ';');

						if (empty($csvLinha[0]) && !empty($D152_Id)) {
							if (!empty($csvLinha[5])) {
								$D152_Comentario = mysql_real_escape_string(utf8_encode($csvLinha[5]));
								$sql = "UPDATE D152 SET D152_Comentario = CONCAT(D152_Comentario, '\\n', '{$D152_Comentario}') WHERE D152_Id = '{$D152_Id}'";
								mysqli_query($sql);
							}
							if (!empty($csvLinha[3])) {
								$D152_Descricao = mysql_real_escape_string(utf8_encode($csvLinha[3]));
								$sql = "UPDATE D152 SET D152_Descricao = CONCAT(D152_Descricao, '\\n', '{$D152_Descricao}') WHERE D152_Id = '{$D152_Id}'";
								mysqli_query($sql);
							}
							continue;
						}

						$D152_Campo = mysql_real_escape_string(utf8_encode($csvLinha[0]));
						preg_match('#^(X|9) ?\(([0-9]+)\)(V([0-9]+))?$#', $csvLinha[1], $matches);
						$D152_Formato = $matches[1];
						$D152_Tamanho = (int) $matches[2];
						$D152_Decimal = isset($matches[4]) ? strlen($matches[4]) : 'NULL';
						preg_match('#^([0-9]+) a ([0-9]+)\)?$#i', $csvLinha[2], $matches);
						$D152_Posicao_Inicio = (int) $matches[1];
						$D152_Posicao_Fim = (int) $matches[2];
						$D152_Descricao = mysql_real_escape_string(utf8_encode($csvLinha[3]));
						$D152_Exemplo = mysql_real_escape_string(utf8_encode($csvLinha[4]));
						$D152_Comentario = mysql_real_escape_string(utf8_encode($csvLinha[5]));
						
						$sql = <<<EOT
INSERT INTO D152
(D152_D151_Id, D152_Campo, D152_Formato, D152_Tamanho, D152_Posicao_Inicio, D152_Posicao_Fim, D152_Descricao, D152_Exemplo, D152_Comentario, D152_Decimal)
VALUES
('$r_D151_Id', '{$D152_Campo}', '{$D152_Formato}', '{$D152_Tamanho}', '{$D152_Posicao_Inicio}', '{$D152_Posicao_Fim}', '{$D152_Descricao}', '{$D152_Exemplo}', '{$D152_Comentario}', {$D152_Decimal})
EOT;
						mysqli_query($sql);
						$D152_Id = mysql_insert_id();
					}
					echo gGeraAlertMsg('Operação OK', 'CSV importado com sucesso!');
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
		$gerado = gGeraCamposForm($form, $tab, $D001_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
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
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
	// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'campoSelect':
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D152';
		$form_campo_pk = 'D152_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'layoutExportacao_content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/layoutExportacao/form/campoSelect/';

		//form editor
		$form_editor = array(
			//'geral' => '{"D152_Select":{"top":7,"left":12,"height":384,"width":712,"hidden":0}}', 
		);

		// Nomes das colunas do select
		$colunasSelect = array();
		$colunasSelect[] = array('title' => '(LIMPAR)', 'value' => '');
		$row = mysql_fetch_assoc(mysql_query("SELECT D151_Select FROM D152 LEFT JOIN D151 ON D151_Id=D152_D151_Id WHERE D152_Id = '{$r_acaoId}'"));
		$sql = $row['D151_Select'] . ' LIMIT 1';
		eval("\$sql = \"{$sql}\";");
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		foreach ($row as $key => $value) {
			$colunasSelect[] = array('title' => $key, 'value' => $key);
		}
		usort($colunasSelect, function ($a, $b) {
			if ($a['title'] == '(LIMPAR)') {
				return -1;
			}
			if ($a['title'] == $b['title']) {
		        return 0;
		    }
		    return ($a['title'] < $b['title']) ? -1 : 1;
		});
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('layoutExportacaoFormSelect');  
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D152_Campo_Select' => array('name' => 'D152_Campo_Select', 'label' => 'Select', 'type' => 'select', 'select' => $colunasSelect, 'uppercase' => false),
				),						
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true, false, false, '{$acaoId}', 'D152_Id');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true, false, false, '{$acaoId}', 'D152_Id');
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
	break;

	case 'adicionarCallback':
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D153';
		$form_campo_pk = 'D153_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'layoutExportacao_content_form3_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/layoutExportacao/form/adicionarCallback/';

		//form editor
		$form_editor = array(
			'geral' => '{"D153_Nome":{"top":7,"left":29,"width":146,"hidden":0},"D153_Tipo":{"top":30,"left":37,"width":89,"hidden":0},"D153_Callback":{"top":53,"left":16,"height":384,"width":552,"hidden":0}}', 
		);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('layoutExportacaoFormCallback');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D153_Nome' => array('name' => 'D153_Nome', 'label' => 'Nome', 'type' => 'text'),
					'D153_Tipo' => array('name' => 'D153_Tipo', 'label' => 'Tipo', 'type' => 'select', 'uppercase' => false, 'select' => 
						array(
							array('title' => 'Genérico', 'value' => 'Genérico', 'selected' => true),
							array('title' => 'Específico', 'value' => 'Específico')
						)
					),
					'D153_Callback' => array('name' => 'D153_Callback', 'label' => 'Callback', 'type' => 'textarea', 'uppercase' => false),
				),						
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
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
	break;

	case 'uiUsuario':
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'XXX';
		$form_campo_pk = 'XXX_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'layoutExportacao_content_form4_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/outros/layoutExportacao/form/uiUsuario/';

		//form editor
		$form_editor = array(
			'geral' => '{"D150_Id":{"top":7,"left":25,"width":287,"hidden":0},"Data_Inicial":{"top":30,"left":5,"width":86,"hidden":0},"Data_Final":{"top":53,"left":9,"width":86,"hidden":0}}', 
		);

		// 
		$arrayD150 = gGeraSelect('D150', 'D150_Id', 'D150_Nome');
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('layoutExportacaoForm');  
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D150_Id' => array('name' => 'D150_Id', 'label' => 'Layout', 'type' => 'select', 'select' => $arrayD150, 'reject' => true, 'extra' => 'onchange="mudarLayout(this);"'),
					'Data_Inicial' => array('name' => 'Data_Inicial', 'label' => 'Data Inicial', 'type' => 'data', 'reject' => true),
					'Data_Final' => array('name' => 'Data_Final', 'label' => 'Data Final', 'type' => 'data', 'reject' => true),
				)
			)
		);
		
		$form->definirFormularioArray($formArray);

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function mudarLayout(elem) {
					var val = $(elem).val();
					$('#Data_Inicial').removeAttr('readonly').closest('.formEditor').css('opacity', '1');
					$('#Data_Final').removeAttr('readonly').closest('.formEditor').css('opacity', '1');
					if (val == 1 || val == 2 || val == 5) {
						$('#Data_Inicial').attr('readonly', 'readonly').closest('.formEditor').css('opacity', '0.4');
						$('#Data_Final').attr('readonly', 'readonly').closest('.formEditor').css('opacity', '0.4');
					}
				}
				mudarLayout($('#D150_Id'));
			} </script></div>
EOT;

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
				$D150_Id = $form->campoValorEnviado('D150_Id');
				$Data_Inicial = gCorrigeDataInverte($form->campoValorEnviado('Data_Inicial'));
				$Data_Final = gCorrigeDataInverte($form->campoValorEnviado('Data_Final'));
				echo  <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/outros/layoutExportacao/outro/processarLayout/?ajax=true&D150_Id=' + encodeURIComponent('{$D150_Id}') + '&Data_Inicial=' + encodeURIComponent('{$Data_Inicial}') + '&Data_Final=' + encodeURIComponent('{$Data_Final}') + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							window.location = '/outros/layoutExportacao/outro/download/?ajax=true&D150_Id=' + request.data;
						});
					}, 100);
				} </script></div>
EOT;
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
