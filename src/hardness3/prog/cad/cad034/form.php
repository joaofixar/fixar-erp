<?php
namespace hardness;

//Classes
require_once("bibliotecas/classes/CAD034.php");
$CAD034 = new CAD034();

switch ($g['r'][3]) {
/**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad034FormPlanoDeContas':
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
		$form_tabela = 'D014';
		$form_campo_pk = 'D014_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad034FormPlanoDeContasBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormPlanoDeContas/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D014_D032_Id":{"top":6.9975433349609375,"left":156.8750081062317,"width":255,"hidden":0},"D014_Flag_Ativo":{"top":6.9975433349609375,"left":545.7230763435364,"width":64,"hidden":0},"D014_D154_Id":{"top":30,"left":135.07352495193481,"width":255,"hidden":0},"D014_Flag_Verificar_Saldo":{"top":30,"left":491.7279591560364,"width":64,"hidden":0},"D014_SubConta":{"top":52.99018859863281,"left":157.52451372146606,"width":248.431372,"hidden":0},"D014_Flag_Registrar_Conta":{"top":52.99018859863281,"left":486.91176652908325,"width":64,"hidden":0},"D014_D073_Id":{"top":75.99264526367188,"left":102.23039293289185,"width":255,"hidden":0},"D014_Flag_Pagamento_Unico":{"top":75.99264526367188,"left":484.705894947052,"width":64,"hidden":0},"D014_Flag_Tipo":{"top":98.99508666992188,"left":157.59804582595825,"width":256,"hidden":0},"D014_Grupo_DRE":{"top":121.99755859375,"left":133.9093098640442,"width":256,"hidden":0},"D014_D031_Id":{"top":145,"left":143.6887288093567,"width":255,"hidden":0},"D014_Dia_Vencimento":{"top":167.99020385742188,"left":109.59558916091919,"width":56.431371999999996,"hidden":0},"D014_Dia_Semana_Vencimento":{"top":190.99264526367188,"left":66.82598161697388,"width":144,"hidden":0},"D014_D024_Id":{"top":213.99508666992188,"left":144.41176652908325,"width":426.431372,"hidden":0},"D014_Observacao":{"top":236.99755859375,"left":128.29657793045044,"height":85.43137200000001,"width":440.431372,"hidden":0},"D014_Codigo_Natureza_Conta_Corrente":{"top":328.98284912109375,"left":17.76960802078247,"width":446,"hidden":0},"D014_Codigo_Indicador_Conta_Corrente":{"top":351.98529052734375,"left":14.816175937652588,"width":447,"hidden":0},"D014_Codigo_Plano_Conta_Fiscal":{"top":375,"left":109.85294389724731,"width":440.431372,"hidden":0},"D014_Mascara_Plano_Conta_Fiscal":{"top":397.99017333984375,"left":102.1201024055481,"width":440.431372,"hidden":0}}'
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D014_Flag_Plano' => array('name' => 'D014_Flag_Plano', 'label' => 'Tipo', 'type' => 'select', 
						// 'select' => array(
							// array('title' => '...', 'value' => ''),
							// array('title' => 'Grupo', 'value' => 'G'),
							// array('title' => 'Movimentação', 'value' => 'M', 'selected' => true),
						// )
					// ),
					//'D014_Grupo' => array('name' => 'D014_Grupo', 'label' => 'Código', 'type' => 'text', 'length' => array(20,40)),
					'D014_D032_Id' => array('name' => 'D014_D032_Id', 'label'=>'Grupo', 'type' => 'select','select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao', 'order by D032_Id', true, @$r_D032_Id)
					),
					'D014_D154_Id' => array('name' => 'D014_D154_Id', 'label'=>'Sub-Grupo', 'type' => 'select','select' => 
						gGeraSelect('D154', 'D154_Id', 'D154_Descricao', 'order by D154_Id', true)
					),
					'D014_SubConta' => array('name' => 'D014_SubConta', 'label' => 'Conta', 'type' => 'text', 'length' => array(40,40)),
					'D014_Flag_Tipo' => array('name' => 'D014_Flag_Tipo', 'label' => '(+)/(-)', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Recebimento', 'value' => 'RC'),
							array('title' => 'Pagamento', 'value' => 'PG'),
						)
					),
					'D014_Grupo_DRE' => array('name' => 'D014_Grupo_DRE', 'label' => 'Grupo DRE', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '1-Despesas com vendas', 'value' => '1'),
							array('title' => '2-Despesas administrativas', 'value' => '2'),
							array('title' => '3-Participações no lucro', 'value' => '3'),
							array('title' => '4-Impostos', 'value' => '4'),
							array('title' => '5-Investimentos', 'value' => '5'),
							array('title' => '6-Receita bruta', 'value' => '6'),
							array('title' => '7-Devoluções', 'value' => '7'),
							array('title' => '8-Fornecedores', 'value' => '8'),
						)
					),
					'D014_Dia_Vencimento' => array('name' => 'D014_Dia_Vencimento', 'label' => 'Dia Vencimento', 'type' => 'text', 'length' => array(2,2)),
					'D014_Dia_Semana_Vencimento' => array('name' => 'D014_Dia_Semana_Vencimento', 'label' => 'Dia Semana Vencimento', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Domingo', 'value' => '1'),
							array('title' => 'Segunda-Feira', 'value' => '2'),
							array('title' => 'Terça-Feira', 'value' => '3'),
							array('title' => 'Quarta-Feira', 'value' => '4'),
							array('title' => 'Quinta-Feira', 'value' => '5'),
							array('title' => 'Sexta-Feira', 'value' => '6'),
							array('title' => 'Sábado', 'value' => '7'),
						)
					),
					'D014_Flag_Ativo' => array('name' => 'D014_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D014_Flag_Verificar_Saldo' => array('name' => 'D014_Flag_Verificar_Saldo', 'label' => 'Verificar Saldo ?', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D014_Flag_Registrar_Conta' => array('name' => 'D014_Flag_Registrar_Conta', 'label' => 'Registrar Conta ?', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D014_D031_Id' => array('name' => 'D014_D031_Id', 'label' => 'Portador', 'type' => 'select', 'select' => 
						gGeraSelect('D031', 'D031_Id', 'D031_Portador', 'order by D031_Id', true)
					),
					'D014_D073_Id' => array('name' => 'D014_D073_Id', 'label' => 'Centro de Custos', 'type' => 'select', 'select' => 
						gGeraSelect('D073', 'D073_Id', 'D073_Nome', 'where D073_Flag_Ativo = "S" order by D073_Id', true)
					),
					'D014_D024_Id' => array('name' => 'Consulta_D014_D024_Id', 'label'=>'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(85,30)),
					'D014_Flag_Pagamento_Unico' => array('name' => 'D014_Flag_Pagamento_Unico', 'label' => 'Pagamento único', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					/*'D014_Flag_Emite_Cupom' => array('name' => 'D014_Flag_Emite_Cupom', 'label' => 'Emite Cupom', 'type' => 'select', 
						'select' => array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),*/
					'D014_Codigo_Natureza_Conta_Corrente' => array('name' => 'D014_Codigo_Natureza_Conta_Corrente', 'label' => 'Código natureza da conta corrente', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01-Contas de ativo', 'value' => '01'),
							array('title' => '02-Contas de passivo', 'value' => '02'),
							array('title' => '03-Patrimônio líquido', 'value' => '03'),
							array('title' => '04-Contas de resultado', 'value' => '04'),
							array('title' => '05-Contas de compensação', 'value' => '05'),
							array('title' => '09-Outras', 'value' => '09'),
						)
					),
					'D014_Codigo_Indicador_Conta_Corrente' => array('name' => 'D014_Codigo_Indicador_Conta_Corrente', 'label' => 'Código indicador da conta corrente', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Analítica', 'value' => 'A'),
							array('title' => 'Sintética', 'value' => 'S'),
						)
					),
					'D014_Observacao' => array('name' => 'D014_Observacao', 'label' => 'Observação', 'type' => 'textarea'),
					'D014_Codigo_Plano_Conta_Fiscal' => array('name' => 'D014_Codigo_Plano_Conta_Fiscal', 'label' => 'Código plano de&nbsp<br>contas contábil', 'type' => 'text'),
					'D014_Mascara_Plano_Conta_Fiscal' => array('name' => 'D014_Mascara_Plano_Conta_Fiscal', 'label' => 'Máscara plano de&nbsp<br>contas contábil', 'type' => 'text'),
				)
			)
		);
        	$form = new Formulario('cad034FormPlanoDeContas');
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
						mysql_query("UPDATE D014 SET D014_Data_Inclusao = CURDATE() WHERE D014_Id = '{$r_acaoId}'");
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
						$D014_Id = $g['mysqlLastId'];
						mysql_query("UPDATE D014 SET D014_Data_Inclusao = CURDATE() WHERE D014_Id = '{$D014_Id}'");
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
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
		$botoesDir .= '<button type="submit" id="cad034FormPlanoDeContasBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad034FormPlanoDeContasGrupo':
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
		$form_tabela = 'D032';
		$form_campo_pk = 'D032_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad034FormPlanoDeContasGrupoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormPlanoDeContasGrupo/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D032_Descricao":{"top":7,"left":11,"width":216,"hidden":0},"D032_Tipo":{"top":30,"left":21,"width":224,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D032_Descricao' => array('name' => 'D032_Descricao', 'label' => 'Grupo', 'type' => 'text', 'length' => array(40,50)),
					'D032_Tipo' => array('name' => 'D032_Tipo', 'label'=>'Tipo','type' => 'select',
						'select' => 
							array(
								array('title' => '...', 'value' => 0, 'selected' => true),
								array('title' => '01-RECEITAS', 'value' => 1),
								array('title' => '10-FIXAS', 'value' => 10),
								array('title' => '20-VARIAVEIS', 'value' => 20),
								array('title' => '25-FORNECEDORES', 'value' => 25),
								array('title' => '30-INVESTIMENTOS', 'value' => 30),
								array('title' => '40-SOCIOS', 'value' => 40),
								array('title' => '50-OUTROS', 'value' => 50),
							)
					),
				)
			)
		);
        $form = new Formulario('cad034FormPlanoDeContasGrupo');
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
						mysql_query("UPDATE D032 SET D032_Data_Inclusao = CURDATE() WHERE D032_Id = '{$r_acaoId}'");
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
						$D032_Id = $g['mysqlLastId'];
						mysql_query("UPDATE D032 SET D032_Data_Inclusao = CURDATE() WHERE D032_Id = '{$D032_Id}'");
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
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
		$botoesDir .= '<button type="submit" id="cad034FormPlanoDeContasGrupoBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad034FormProjecao':
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
		$form_tabela = 'T018';
		$form_campo_pk = 'T018_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad034FormProjecaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormProjecao/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T018_D014_Id' => array('name' => 'T018_D014_Id', 'label'=>'Conta', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D014', 'D014_Id', 'D014_SubConta', '', true)
					),
					'T018_Mes' => array('name' => 'T018_Mes', 'label' => 'Mês', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Janeiro', 'value' => '1'),
							array('title' => 'Fevereiro', 'value' => '2'),
							array('title' => 'Março', 'value' => '3'),
							array('title' => 'Abril', 'value' => '4'),
							array('title' => 'Maio', 'value' => '5'),
							array('title' => 'Junho', 'value' => '6'),
							array('title' => 'Julho', 'value' => '7'),
							array('title' => 'Agosto', 'value' => '8'),
							array('title' => 'Setembro', 'value' => '9'),
							array('title' => 'Outubro', 'value' => '10'),
							array('title' => 'Novembro', 'value' => '11'),
							array('title' => 'Dezembro', 'value' => '12'),
						)
					),
					'T018_Ano' => array('name' => 'T018_Ano', 'label' => 'Ano', 'type' => 'text', 'length' => array(4,4)),
					'T018_Valor' => array('name' => 'T018_Valor', 'label' => 'Valor R$', 'type' => 'numero', 'length' => array(20,20)),
					'T018_D014_Id' => array('name' => 'T018_D014_Id', 'label' => 'Valor R$', 'type' => 'hidden', 'value'=>$r_T014_Id, 'length' => array(20,20)),
				)
			)
		);
        		$form = new Formulario('cad034FormPlanoDeContasGrupo');
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
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
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
		$botoesDir .= '<button type="submit" id="cad034FormProjecaoBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad034FormPlanoDeContasSubGrupo':
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
		$form_tabela = 'D154';
		$form_campo_pk = 'D154_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad034FormPlanoDeContasSubGrupoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormPlanoDeContasSubGrupo/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D154_Descricao":{"top":4,"left":4,"width":225}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D154_Descricao' => array('name' => 'D154_Descricao', 'label' => 'Sub-Grupo', 'type' => 'text', 'length' => array(40,50)),
				)
			)
		);
        $form = new Formulario('cad034FormPlanoDeContasSubGrupo');
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
						$D032_Id = $g['mysqlLastId'];
						mysql_query("UPDATE D032 SET D032_Data_Inclusao = CURDATE() WHERE D032_Id = '{$D032_Id}'");
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Plano de Conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral', [500,320]);
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
		$botoesDir .= '<button type="submit" id="cad034FormPlanoDeContasSubGrupoBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad034FormImportarCSV':
	
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
		$form_tabela = 'D014';
		$form_campo_pk = 'D014';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'CAD034FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad034FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCAD034'] 		= array('name' => 'uploadArquivoCSVCAD034', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['CAD034FormImportarCSV_btn'] 		= array('name' => 'CAD034FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D014_Arquivo_Csv'] 				= array('name' => 'D014_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT

EOT;

		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCAD034');
				$retorno = $CAD034->importarCSV($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
		$g['smarty']->display('form.tpl');
	break;

	case 'cad034FormImportarCSVGrupos':
	
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
		$form_tabela = 'D032';
		$form_campo_pk = 'D032';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'CAD034FormImportarCSVGrupos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormImportarCSVGrupos/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad034FormImportarCSVGrupos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCAD034'] 		= array('name' => 'uploadArquivoCSVCAD034', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['CAD034FormImportarCSVGrupos_btn'] 		= array('name' => 'CAD034FormImportarCSVGrupos_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D032_Arquivo_Csv'] 				= array('name' => 'D032_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT

EOT;

		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCAD034');
				$retorno = $CAD034->importarCSVGrupos($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
		$g['smarty']->display('form.tpl');
	break;

	case 'cad034FormImportarCSVSubGrupos':
	
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
		$form_tabela = 'D154';
		$form_campo_pk = 'D154';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'CAD034FormImportarCSVSubGrupos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad034/form/cad034FormImportarCSVSubGrupos/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad034FormImportarCSVSubGrupos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCAD034'] 		= array('name' => 'uploadArquivoCSVCAD034', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['CAD034FormImportarCSVSubGrupos_btn'] 		= array('name' => 'CAD034FormImportarCSVSubGrupos_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D154_Arquivo_Csv'] 				= array('name' => 'D154_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT

EOT;

		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCAD034');
				$retorno = $CAD034->importarCSVSubGrupos($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
		$g['smarty']->display('form.tpl');
	break;
}
