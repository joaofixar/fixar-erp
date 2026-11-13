<?php
namespace hardness;        
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 04/01/2012  
	*/
	case 'etc013FormControleInterno':
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
		$form_tabela = 'I001';
		$form_campo_pk = 'I001_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc013FormControleInternoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc013/form/etc013FormControleInterno/';
		
		// Definição do Formulário
		$formArray = array(	
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'I001_C007_Id' => array('name' => 'I001_C007_Id', 'label' => 'Analista', 'extra'=>'style="width:300px;"', 'type' => 'select', 
						'select' =>  gGeraSelect('C007', 'C007_Id','C007_Primeiro_Nome', 'order by C007_Primeiro_Nome',true),
					),
					'I001_Topico' => array('name' => 'I001_Topico', 'label' => 'Tópico', 'type' => 'text', 'length' => array(123,300)),
					'I001_Descricao_Requisito' => array('name' => 'I001_Descricao_Requisito', 'label' => 'Descrição Requisito', 'type' => 'textarea', 'length' => array(4,120), 'uppercase' => false),
					'I001_Descricao_Pre_Analise' => array('name' => 'I001_Descricao_Pre_Analise', 'label' => 'Descrição Pré-Análise', 'type' => 'textarea', 'length' => array(4,120), 'uppercase' => false),
					'I001_Descricao_Analise' => array('name' => 'I001_Descricao_Analise', 'label' => 'Descrição Análise', 'type' => 'textarea', 'length' => array(10,120), 'uppercase' => false),
					'I001_Previsao_Analise_Horas' => array('name' => 'I001_Previsao_Analise_Horas', 'label' => 'Previsão Horas', 'type' => 'text', 'length' => array(20,2)),
					'I001_Previsao_Analise_Data' => array('name' => 'I001_Previsao_Analise_Data', 'label' => 'Previsão Data', 'type' => 'data', 'length' => array(20,120)),
					'I001_Inicio_Analise_Data' => array('name' => 'I001_Inicio_Analise_Data', 'label' => 'Data Início', 'type' => 'data', 'length' => array(20,120)),
					'I001_Inicio_Analise_Hora' => array('name' => 'I001_Inicio_Analise_Hora', 'label' => 'Hora Início', 'type' => 'tempo', 'length' => array(20,120)),
					'I001_Conclusao_Analise_Data' => array('name' => 'I001_Conclusao_Analise_Data', 'label' => 'Data Fim', 'type' => 'data', 'length' => array(20,120)),
					'I001_Conclusao_Analise_Hora' => array('name' => 'I001_Conclusao_Analise_Hora', 'label' => 'Hora Fim', 'type' => 'tempo', 'length' => array(20,120)),
					'I001_Prioridade' => array('name' => 'I001_Prioridade', 'label' => 'Prioridade', 'type' => 'text', 'length' => array(20,120)),
					'I001_Tipo' => array('name' => 'I001_Tipo', 'label'=>'Tipo','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
						array(
							array('title' => 'Correção', 'value' => 'C', 'selected' => true),
							array('title' => 'Implementação', 'value' => 'I'),
						)
					),
					'I001_Status' => array('name' => 'I001_Status', 'label'=>'Status','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
						array(
							array('title' => 'Pendente', 'value' => 'P', 'selected' => true),
							array('title' => 'Execução', 'value' => 'E'),
							array('title' => 'Concluído', 'value' => 'C'),
							array('title' => 'Pré-Analise', 'value' => 'A'),
							array('title' => 'Pós-Analise', 'value' => 'N'),
						)
					),
					'I001_Modulo' => array('name' => 'I001_Modulo', 'label' => 'Módulo', 'type' => 'text', 'length' => array(15,15)),
				)
			)
		);
        $form = new Formulario('form-etc010FormPrincipal');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc013FormControleInternoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'etc013FormControleInternoOS':
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
		$form_tabela = 'I002';
		$form_campo_pk = 'I002_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc013FormControleInternoOSBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc013/form/etc013FormControleInternoOS/';
		
		// Definição do Formulário
		$formArray = array(	
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'I002_I001_Id' => array('name' => 'Consulta_I002_I001_Id', 'label'=>'Análise', 'type' => 'pesquisa', 'extra'=>'style="width:300px;"', 'pesquisa'=>array('I001','I001_Id','I001_Topico'),'length' => array(67,30)),
					'I002_C007_Id' => array('name' => 'Consulta_I002_C007_Id', 'label'=>'Programador', 'type' => 'pesquisa', 'extra'=>'style="width:300px;"', 'pesquisa'=>array('C007','C007_Id','C007_Primeiro_Nome'),'length' => array(67,30)),
					'I002_Topico' => array('name' => 'I002_Topico', 'label' => 'Tópico', 'type' => 'text', 'length' => array(123,300)),
					'I002_Descricao_OS' => array('name' => 'I002_Descricao_OS', 'label' => 'Descrição OS', 'type' => 'textarea', 'length' => array(20,120), 'uppercase' => false),
					'I002_Previsao_OS_Horas' => array('name' => 'I002_Previsao_OS_Horas', 'label' => 'Previsão Horas', 'type' => 'text', 'length' => array(20,2)),
					'I002_Previsao_OS_Data' => array('name' => 'I002_Previsao_OS_Data', 'label' => 'Previsão Data', 'type' => 'data', 'length' => array(20,120)),
					'I002_Inicio_OS_Data' => array('name' => 'I002_Inicio_OS_Data', 'label' => 'Data Início', 'type' => 'data', 'length' => array(20,120)),
					'I002_Inicio_OS_Hora' => array('name' => 'I002_Inicio_OS_Hora', 'label' => 'Hora Início', 'type' => 'tempo', 'length' => array(20,120)),
					'I002_Conclusao_OS_Data' => array('name' => 'I002_Conclusao_OS_Data', 'label' => 'Data Fim', 'type' => 'data', 'length' => array(20,120)),
					'I002_Conclusao_OS_Hora' => array('name' => 'I002_Conclusao_OS_Hora', 'label' => 'Hora Fim', 'type' => 'tempo', 'length' => array(20,120)),
					'I002_Prioridade' => array('name' => 'I002_Prioridade', 'label' => 'Prioridade', 'type' => 'text', 'length' => array(20,120)),
					'I002_Status' => array('name' => 'I002_Status', 'label'=>'Tipo','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
						array(
							array('title' => 'Correção', 'value' => 'C', 'selected' => true),
							array('title' => 'Implementação', 'value' => 'I'),
						)
					),
					'I002_Status' => array('name' => 'I002_Status', 'label'=>'Status','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
						array(
							array('title' => 'Pendente', 'value' => 'P', 'selected' => true),
							array('title' => 'Execução', 'value' => 'E'),
							array('title' => 'Concluído', 'value' => 'C'),
						)
					),
				)
			)
		);
        $form = new Formulario('form-etc010FormPrincipal');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="etc013FormControleInternoOSBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>