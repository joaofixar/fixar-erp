<?php
namespace hardness;
//Classes
//require_once("bibliotecas/classes/CAD007.php");
//$CAD007 = new CAD007();
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: cad007tab02
	*/
	case 'cad007form01':
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
		$form_tabela = 'T140';
		$form_campo_pk = 'T140_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad007/form/cad007form01/';
        
        //form editor
        $form_editor = array(
            'cadastro_certificado' => '{"T140_Link":{"top":98.98898315429688,"left":30.116424560546875,"width":376.039216,"hidden":0},"T140_D024_Id":{"top":6.9853057861328125,"left":50.98040771484375,"width":362.039216,"hidden":0},"T140_Tipo_Certificado":{"top":29.993881225585938,"left":84.92648315429688,"width":376.039216,"hidden":0},"T140_Certificadora":{"top":52.98713684082031,"left":43.581512451171875,"width":376.039216,"hidden":0},"T140_Data_Emissao":{"top":75.9957275390625,"left":38.95526123046875,"width":120.03921600000001,"hidden":0},"T140_Data_Validade":{"top":75.9957275390625,"left":292.11090087890625,"width":120.03921600000001,"hidden":0},"uploadArquivo":{"top":121.99755859375,"left":14.460784912109375,"width":376.039216,"hidden":0},"T140_D001_Id":{"top":190.99264526367188,"left":68.76531982421875,"width":151.039216,"hidden":1}}',
        );

		// novo formulário
		$form = new Formulario('form_fin002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'cadastro_certificado' => array(
					'T140_D024_Id' => array('name' => 'Consulta_T140_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa','pesquisa'=>array('D024','D024_Id','D024_Nome_Fantasia,D024_Nome_Empresa',""), 'length' => array(65,30)),
					'T140_D001_Id' => array('name' => 'Consulta_T140_D001_Id', 'label'=>'Produto', 'type' => 'pesquisa','pesquisa'=>array('D001','D001_Id','D001_Descricao_Produto'), 'length' => array(65,30)),
					'T140_Data_Emissao' => array('name' => 'T140_Data_Emissao', 'type' => 'data', 'label'=>'Data Emissão'),
					'T140_Data_Validade' => array('name' => 'T140_Data_Validade', 'type' => 'data', 'label'=>'Data Validade '),
					'T140_Tipo_Certificado' => array('name' => 'T140_Tipo_Certificado', 'type' => 'text', 'label' => 'Tipo', 'length' => array(30,30)),
					'T140_Certificadora' => array('name' => 'T140_Certificadora', 'label'=>'Certificadora', 'type' => 'text', 'length' => array(30,30)),
		            'uploadArquivo' => array('name' => 'uploadArquivo', 'label' => 'Arquivo certificado', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase'=>false),
		            'T140_Nome_Arquivo' => array('name' => 'T140_Nome_Arquivo', 'type' => 'hidden', 'uppercase'=>false),
					'T140_Link' => array('name' => 'T140_link', 'type' => 'text', 'uppercase' => false, 'label' => 'Link Certificado'),
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
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					$T140_Link = $form->campoValorEnviado('T140_Link');

					if(!empty($arquivo['name']) && !empty($T140_Link)){
						echo gGeraAlertMsg('Erro', "Deve ser enviado uma <b>Foto</b> ou um <b>Link</b>.");
					} else {
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Neste caso, guardamos o nome do arquivo no banco
							$form->campoSetarValor('T140_Nome_Arquivo', $arquivo['name'], true);
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
								// Gravar o arquivo no disco
								if(empty($T140_Link)){
									$pathinfo = pathinfo($arquivo['name']);
									$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
									$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/certificados";
									is_dir($dir) or mkdir($dir, 0777, true);
									$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
									if (file_exists($file)) { unlink($file); }
									move_uploaded_file($arquivo['tmp_name'], $file);
								}	
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
						}
					}

				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					$T140_Link = $form->campoValorEnviado('T140_Link');
					if(!empty($arquivo['name']) && !empty($T140_Link)){
						echo gGeraAlertMsg('Erro', "Deve ser enviado uma <b>Foto</b> ou um <b>Link</b>.");
					} else {
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							mysql_query("SET AUTOCOMMIT=0");
							mysql_query("START TRANSACTION");
							$form->campoSetarValor('T140_Nome_Arquivo', $arquivo['name'], true);
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								$acaoId = $g['mysqlLastId'];
								// Gravar o arquivo no disco
								if(empty($T140_Link)){
									$pathinfo = pathinfo($arquivo['name']);
									$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
									$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/certificados";
									is_dir($dir) or mkdir($dir, 0777, true);
									$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
									$retornoMoveFile = move_uploaded_file($arquivo['tmp_name'], $file);
								}
	
								if (!$retornoMoveFile && empty($T140_Link)){
									mysql_query("ROLLBACK");
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											dialogConfirm('ERRO', 'Não foi possível realizar o upload, selecione o arquivo e tente novamente');
											if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
											}
										} </script></div>
EOT;
								} else {
									echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Certificado', '/cad/cad007/content/cad007content02Editar/', '&tab=geral&acaoId=' + encodeURIComponent("{$acaoId}"), [1200,700]);
											}, 150);
										  });
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
								}
							}
							mysql_query("COMMIT");
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;

		case 'cad007FormItens':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad007FormSubsBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad007/form/cad007FormItens/';
			
			//form editor
			$form_editor = array(
				'geral' => '{"T303_D001_Id":{"top":7,"left":3.75,"width":634,"hidden":0},"cad007FormSubsBtn":{"top":5,"left":720,"width":null,"hidden":0},"T303_T140_Id":{"top":30,"left":5,"width":137,"hidden":0}}',
			);
	
			// novo formulário
			$form = new Formulario('form_fin002_form1');
	
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T303_D001_Id' => array('name' => 'Consulta_T303_D001_Id', 'label'=>'Produto', 'type' => 'pesquisa','pesquisa'=>array('D001','D001_Id','D001_Codigo_Produto,D001_Descricao_Produto'), 'length' => array(65,30)),
						'T303_T140_Id'    => array('name' => 'T303_T140_Id' , 'label' => 'T303_T140_Id', 'value' => $acaoId, 'type' => 'hidden'),
						'cad007FormSubsBtn' => array('name' => 'cad007FormSubsBtn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir  ', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:0px;margin-top:0px;padding-left:10px;padding-right:0px;padding-top:0px;height:20px;"', 'reject'=>true),
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
					require_once("bibliotecas/classes/CAD007.php");
					$CAD007 = new CAD007();
					$retorno = $CAD007->incluirItem($form);
					if ($retorno === true) {
						$msg  = gGeraAlertMsg('Operação OK', '');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							dialogConfirm('Ok', 'Intem incluído com sucesso!');
							if (typeof jQuery != 'undefined') {
								divRefresh("{$r_divRefresh}", true);
								divRefresh('{$g['divId']}', true);
							}
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Erro', $retorno);
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
			$botoesDir .= '<button type="submit" id="cad007FormSubsBtn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			/* $g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl'); */
	
		break;
}
?>
