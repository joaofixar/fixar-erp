<?php
namespace hardness;
//require_once('prog/cad/cad044/form_func-js.php');
require_once('prog/cad/cad051/form_func-js.php');
      
switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/2011  
	*/
	case 'cad051FormModeloEtiqueta':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		$form_tabela = 'D138';
		$form_campo_pk = 'D138_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad051FormModeloEtiqueta_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad051/form/cad051FormModeloEtiqueta/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D138_Flag_Tipo' => array('name' => 'D138_Flag_Tipo', 'label' => 'Tipo','type' => 'select',
						'select' => array(
							array('title' => 'Generica', 'value' => 'G', 'selected' => true),
							array('title' => 'Produto', 'value' => 'P'),
							array('title' => 'Cliente', 'value' => 'C'),
							array('title' => 'Locação', 'value' => 'L'),
							array('title' => 'Pedido', 'value' => 'S')
						)
					),
					'D138_Modelo' => array('name' => 'D138_Modelo', 'label' => 'Nome Modelo','type' => 'text', 'length' => array(65,40)),
					'D138_Linguagem' => array('name' => 'D138_Linguagem', 'label' => 'Linguagem','type' => 'select',
						'select' => array(
							array('title' => 'PPLA (Argox)', 'value' => 'PPLA', 'selected' => true),
							array('title' => 'ZPL (Zebra)', 'value' => 'ZPL')
						)
					),
					'D138_Codigo' => array('name' => 'D138_Codigo', 'label' => 'Código', 'type' => 'textarea', 'uppercase' => false, 'length' => array(15,70)),
					'D138_Flag_Editor' => array('name' => 'D138_Flag_Editor', 'label' => 'Flag Editor', 'type' => 'hidden', 'value' => 'N'),
				)
			)
		);
        $form = new Formulario('cad051FormModeloEtiqueta');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesEsq .= '<button type="submit" id="cad051FormModeloEtiqueta_btn_print">Imprimir</button>';
		$botoesDir .= '<button type="submit" id="cad051FormModeloEtiqueta_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad051FormModeloEtiqueta_btn_print').unbind('click').bind('click', function(data) {
					cad051ImprimirEtiquetas('{$g['divId']}', 1, '{$r_acaoId}');
				});
			} </script></div>
EOT;
	break;
	
	case 'cad051FormImpressaoGenerica':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		$form_tabela = 'D138';
		$form_campo_pk = 'D138_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad051FormImpressaoGenerica_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad051/form/cad051FormImpressaoGenerica/';
		
		$fontes = array(
			array('title' => '4', 'value' => '4'),
			array('title' => '6', 'value' => '6'),
			array('title' => '8', 'value' => '8'),
			array('title' => '10', 'value' => '10'),
			array('title' => '12', 'value' => '12'),
			array('title' => '14', 'value' => '14'),
			array('title' => '18', 'value' => '18'),
			array('title' => '24', 'value' => '24'),
		);
		$espaco = array();
		for ($i = 1; $i <= 300; $i++) {
			$espaco[] = array('title' => $i, 'value' => $i);
		}
		$espacoCol = array();
		for ($i = 0; $i <= 100; $i++) {
			$espacoCol[] = array('title' => $i, 'value' => $i);
		}
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D138_Flag_Tipo' => array('name' => 'D138_Flag_Tipo', 'label' => 'Tipo','type' => 'select',
						'select' => array(
							array('title' => 'Generica', 'value' => 'G', 'selected' => true),
							array('title' => 'Produto', 'value' => 'P'),
							array('title' => 'Cliente', 'value' => 'C'),
							array('title' => 'Locação', 'value' => 'L'),
							array('title' => 'Pedido', 'value' => 'S')
						)
					),
					'D138_Modelo' => array('name' => 'D138_Modelo', 'label' => 'Nome Modelo','type' => 'text', 'length' => array(65,40)),
					'D138_Linguagem' => array('name' => 'D138_Linguagem', 'label' => 'Linguagem','type' => 'select',
						'select' => array(
							array('title' => 'PPLA (Argox)', 'value' => 'PPLA', 'selected' => true),
							array('title' => 'ZPL (Zebra)', 'value' => 'ZPL')
						)
					),
					'D138_Flag_Editor' => array('name' => 'D138_Flag_Editor', 'label' => 'Flag Editor', 'type' => 'hidden', 'value' => 'S'),
					'D138_Codigo' => array('name' => 'D138_Codigo', 'label' => 'Codigo', 'type' => 'hidden', 'value' => 'OK', 'uppercase' => false),
					
					'tituloTamanho' => array('name' => 'titulTamanho', 'type' => 'titulo', 'label' => 'Tamanho da Etiqueta', 'reject' => true, 'extra'=>'style=background:#E8E8E8;'),
					'Altura' => array('reject' => true, 'name' => 'Altura', 'label' => 'Altura (mm)', 'type' => 'text', 'length' => array(4,4)),
					'Largura' => array('reject' => true, 'name' => 'Largura', 'label' => 'Largura (mm)', 'type' => 'text', 'length' => array(4,4), 'align' => 'right'),
					'Colunas' => array('reject' => true, 'name' => 'Colunas', 'label' => 'Colunas', 'type' => 'select', 'select' => array(
						array('title' => '1', 'value' => '1'),
						array('title' => '2', 'value' => '2'),
						array('title' => '3', 'value' => '3')
					), 'align' => 'right'),
					'ColunasSpace' => array('reject' => true, 'name' => 'ColunasSpace', 'label' => 'Espaço (mm)', 'type' => 'select', 'select' => $espacoCol, 'align' => 'right'),
					'tituloLinhas' => array('name' => 'tituloLinhas', 'type' => 'titulo', 'label' => 'Linhas', 'reject' => true,  'extra'=>'style=background:#E8E8E8;')
				)
			)
		);
		for ($i = 1; $i <= 8; $i++) {
			$formArray['campos']['geral']['Texto_' . $i] = array('reject' => true, 'name' => 'Texto_' . $i, 'label' => 'Texto', 'type' => 'text', 'length' => array(80,150), 'uppercase' => false);
			$formArray['campos']['geral']['Font_' . $i] = array('reject' => true,'name' => 'Font_' . $i, 'align'=>'right', 'label' => 'Fonte (px)','type' => 'select',
				'select' => $fontes
			);
			$formArray['campos']['geral']['Space_' . $i] = array('reject' => true,'name' => 'Space_' . $i, 'style'=>'float:right;', 'align'=>'right', 'label' => 'Espaço (mm)','type' => 'select',
				'select' => $espaco
			);
		}
		$formArray['campos']['geral']['tituloBarra'] = array('name' => 'titulBarra', 'type' => 'titulo', 'label' => 'Código de Barra', 'reject' => true, 'extra'=>'style=background:#E8E8E8;');
		$formArray['campos']['geral']['BarraTexto'] = array('reject' => true, 'name' => 'BarraTexto', 'label' => 'Código', 'type' => 'text', 'length' => array(20,100), 'uppercase' => false);
		$formArray['campos']['geral']['BarraLinha'] = array('reject' => true, 'name' => 'BarraLinha', 'label' => 'Linha (mm)', 'type' => 'text', 'length' => array(4,4), 'align' => 'right', 'uppercase' => false);
		$formArray['campos']['geral']['BarraColuna'] = array('reject' => true, 'name' => 'BarraColuna', 'label' => 'Coluna (mm)', 'type' => 'text', 'length' => array(4,4), 'align' => 'right', 'uppercase' => false);
		
        $form = new Formulario('cad051FormModeloEtiqueta');
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
				$param = array(
					'altura' => $form->campoValorEnviado('Altura'),
					'largura' => $form->campoValorEnviado('Largura'),
					'BarraTexto' => $form->campoValorEnviado('BarraTexto'),
					'BarraLinha' => $form->campoValorEnviado('BarraLinha'),
					'BarraColuna' => $form->campoValorEnviado('BarraColuna'),
					'Colunas' => $form->campoValorEnviado('Colunas'),
					'ColunasSpace' => $form->campoValorEnviado('ColunasSpace'),
				);
				for ($i = 1; $i <= 8; $i++) {
					$param['txt_' . $i] = $form->campoValorEnviado('Texto_' . $i);
					$param['font_' . $i] = $form->campoValorEnviado('Font_' . $i);
					$param['space_' . $i] = $form->campoValorEnviado('Space_' . $i);
				}
				$param = serialize($param);
				$form->campoSetarValor('D138_Codigo', $param, true);
				if (!empty($r_print)) {
					// Somente impressão
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							cad051ImprimirEtiquetasTesteGenerico('{$g['divId']}', '{$param}');
						} </script></div>
EOT;
				} else {
					// Insert ou Update?
					if (!empty($r_acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
							// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
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
				// Preenche os campos
				$param = unserialize($form->campoValor('D138_Codigo'));
				$form->campoSetarValor('Altura', isset($param['altura']) ? $param['altura'] : '', true);
				$form->campoSetarValor('Largura', isset($param['largura']) ? $param['largura'] : '', true);
				for ($i = 1; $i <= 8; $i++) {
					$form->campoSetarValor('Texto_'.$i, isset($param['txt_'.$i]) ? $param['txt_'.$i] : '', true);
					$form->campoSetarValor('Font_'.$i, isset($param['font_'.$i]) ? $param['font_'.$i] : '', true);
					$form->campoSetarValor('Space_'.$i, isset($param['space_'.$i]) ? $param['space_'.$i] : '', true);
				}
				$form->campoSetarValor('BarraTexto', isset($param['BarraTexto']) ? $param['BarraTexto'] : '', true);
				$form->campoSetarValor('BarraLinha', isset($param['BarraLinha']) ? $param['BarraLinha'] : '', true);
				$form->campoSetarValor('BarraColuna', isset($param['BarraColuna']) ? $param['BarraColuna'] : '', true);
				$form->campoSetarValor('Colunas', isset($param['Colunas']) ? $param['Colunas'] : '', true);
				$form->campoSetarValor('ColunasSpace', isset($param['ColunasSpace']) ? $param['ColunasSpace'] : '', true);
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if (!empty($r_print)) {
			$botoesDir .= '<button type="submit" id="cad051FormImpressaoGenerica_btn">Imprimir</button>';
		} else {
			$botoesDir .= '<button type="submit" id="cad051FormImpressaoGenerica_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad051FormImpressaoGenerica_btn_print').unbind('click').bind('click', function(data) {
					cad051ImprimirEtiquetas('{$g['divId']}', 1, '{$r_acaoId}');
				});
			} </script></div>
EOT;
	break;
	
	
	case 'cad051SelecionarModelo':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D004_Id = (isset($r_D004_Id)) ? $r_D004_Id : false;
		$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$D138_Id = (isset($D138_Id)) ? $D138_Id : false;

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
		$form_tabela = 'D138';
		$form_campo_pk = 'D138_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad051SelecionarModelo_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad051/form/cad051SelecionarModelo/';

		// novo formulário
		$form = new Formulario('cad051SelecionarModelo_form');
		
		// Qual o TIPO?
		if (!empty($D004_Id)) {
			$tipo = 'L';
		} else if (!empty($D009_Id)) {
			$tipo = 'P';
		} else if (!empty($D024_Id)) {
			$tipo = 'C';
		} else {
			$tipo = 'G';
		}

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D138_Id' => array('name' => 'D138_Id', 'label'=>'Modelo','type' => 'select', 'extra'=>'style="width:200px;"','select' => 
						gGeraSelect('D138','D138_Id','D138_Modelo'," WHERE D138_Flag_Tipo='{$tipo}' AND D138_Flag_Mostrar='S'"),
					),
					'ImpQuantidadeEstoque' => array('name' => 'ImpQuantidadeEstoque', 'label' => 'Imprimir etiquetas por estoque', 'type' => 'checkbox', 'reject' => true, 'align' => 'right'),                                                                                                                                                           
					'ImpQuantidade' => array('name' => 'ImpQuantidade', 'label' => 'Quantidade', 'type' => 'nemero', 'length' => array(2,3), 'align' => 'right', 'value' => 1)
				)
			)
		);
        $formArray['campos']['geral']['btnFechar'] = array('name' => 'btnFechar', 'type' => 'button', 'reload' => false, 'value' => 'Cancelar', 'align' => 'right', 'extra' => 'onclick="fecharJanela(\''.$g['divId'].'\');"','style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['cad051SelecionarModelo_btn'] = array('name' => 'cad051SelecionarModelo_btn', 'type' => 'submit', 'reload' => false, 'value' => 'Imprimir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				$D138_Id = $form->campoValorEnviado('D138_Id');
				$ImpQuantidade = $form->campoValorEnviado('ImpQuantidade');
				
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var impQuantidadeEstoque = $('#ImpQuantidadeEstoque');
		  				cad051ImprimirEtiquetas('{$g['divId']}', '{$ImpQuantidade}', impQuantidadeEstoque, '{$D138_Id}', '{$D004_Id}', '{$D009_Id}', '{$D024_Id}');
					} </script></div>
EOT;
				
				/*$etiquetasProdutoPedirQtd = $g['conf']->ini('etiquetasProdutoPedirQtd');
	
				if (!$etiquetasProdutoPedirQtd) {
					
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		  					cad020ImpTag('{$id}', '{$g['divId']}', false, false, false, '{$D138_Id}');
						} </script></div>
EOT;
				} else {
					
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		  					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Informe a Quantidade', '/sistema/etiquetas/content/sistemaEtiquetasQtdProduto/','&D00X_Id={$id}&D138_Id={$D138_Id}', [300,200]);
						} </script></div>
EOT;
				}*/
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
		$gerado = gGeraCamposForm($form, $tab, $D138_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

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
		 
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad051SelecionarModelo_btn">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
        */

	break;
}
?>