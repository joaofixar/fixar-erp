<?php
namespace hardness;
require_once('prog/fis/fis003/form_func-js.php');
require_once("bibliotecas/classes/ReformaTributaria.php");
$reformaTributaria = new ReformaTributaria();

switch ($g['r'][3]) {
	case 'fis003FormNCM':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		$formId = (isset($r_fis003FormNCM)) ? $r_fis003FormNCM['formId'] : $r_formId;
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D005';
		$form_campo_pk = 'D005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis003FormNcmBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis003/form/fis003FormNCM/';
    	// Editor
		$form_editor = array(
			'geral' => '{"D005_Classificacao_Fiscal":{"top":7,"left":73.5,"width":137,"hidden":0},"D005_Flag_Aplicacao":{"top":7,"left":435.875,"width":72,"hidden":0},"D005_D037_Id_Tributavel":{"top":30,"left":13.953125,"width":143,"hidden":0},"D005_Flag_Ativo":{"top":30,"left":433.421875,"width":80,"hidden":0},"D005_UF_ST":{"top":30,"left":653,"height":45,"width":217,"hidden":1},"D005_Observacao":{"top":53,"left":56.96875,"height":108,"width":424,"hidden":0},"D005_Flag_ST":{"top":99,"left":663,"width":120,"hidden":1},"D005_Valor_VA":{"top":122,"left":630,"width":88,"hidden":1},"D005_Aliquota_IPI":{"top":145,"left":608,"width":88,"hidden":1},"D005_Aliquota_PIS":{"top":145,"left":731,"width":72,"hidden":1},"D005_Aliquota_COFINS":{"top":167.984375,"left":709,"width":72,"hidden":1},"D005_Aliquota_ICMS_Interno_Compra":{"top":190.984375,"left":631,"width":88,"hidden":1},"D005_D088_Id":{"top":214,"left":38.125,"width":250,"hidden":1},"D005_Percentual_Trib_Transparencia":{"top":213.984375,"left":581,"width":79,"hidden":1},"D005_Regras_MVA":{"top":236.984375,"left":24,"height":83,"width":248,"hidden":1}}',
			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
			==============================================================*/
			'reforma'	   => '{"D005_Situacao_Tributaria_CBS_IBS":{"top":7,"left":106.359375,"width":383,"hidden":0},"D005_Classificacao_Tributaria_CBS_IBS":{"top":30,"left":36.546875,"width":383,"hidden":0},"D005_Situacao_Tributaria_CBS_IBS_Regular":{"top":53,"left":81.9375,"width":383,"hidden":0},"D005_Classificacao_Tributaria_CBS_IBS_Regular":{"top":76,"left":12.125,"width":383,"hidden":0}}',
		);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
EOT;
		$pesquisaGrid = array(
		'T153',																		// tabela
		'T153_Id',																	// chave primaria
		'T153_NCM,T153_Aliquota,T153_Descricao',									// campos
		$pesquisaGridExtra,															// extras sql
		'Pesquisa de NCM',															// título da janela
		'NCM,IPI,Descrição',														// descrição dos campos
		'campoProcuraNCM',															// callback js
		$g['divId'],																// divId
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D005_Classificacao_Fiscal' => array('name' => 'D005_Classificacao_Fiscal' , 'label' => 'NCM', 'type' => 'texto', 'length' => array(20,30)),
					'D005_Flag_Aplicacao' => array('name' => 'D005_Flag_Aplicacao' , 'label' => 'EX', 'type' => 'texto', 'length' => array(2,2)),
					'D005_Aliquota_IPI' => array('name' => 'D005_Aliquota_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_D088_Id' => array('name' => 'D005_D088_Id', 'label'=>'Situação Tributária IPI', 'type' => 'select', 'extra'=>'style="width:250px;"','select' =>
						gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao',"order by D088_Codigo_ST,D088_Descricao"),
					),
					'D005_Flag_ST' => array('name' => 'D005_Flag_ST', 'label'=>'Subs. Tributária', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D005_Valor_VA' => array('name' => 'D005_Valor_VA', 'label' => 'Margem Valor agregado', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Aliquota_PIS' => array('name' => 'D005_Aliquota_PIS', 'label' => 'PIS', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Aliquota_COFINS' => array('name' => 'D005_Aliquota_COFINS', 'label' => 'COFINS', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Aliquota_ICMS_Interno_Compra' => array('name' => 'D005_Aliquota_ICMS_Interno_Compra', 'label' => 'ICMS Interno compra', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Observacao' => array('name' => 'D005_Observacao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(3,40)),
					'D005_UF_ST' => array('name' => 'D005_UF_ST', 'label' => 'UF ST', 'type' => 'textarea', 'length' => array(3,40)),
					'D005_Regras_MVA' => array('name' => 'D005_Regras_MVA', 'label' => 'Exceções ICM/IPI/MVA', 'type' => 'textarea', 'length' => array(3,40)),
					'D005_Percentual_Trib_Transparencia' => array('name' => 'D005_Percentual_Trib_Transparencia', 'label' => 'Percentual tributos (Lei tarnsp)', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Flag_Ativo' => array('name' => 'D005_Flag_Ativo', 'label'=>'Ativo', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D005_D037_Id_Tributavel' => array('name' => 'D005_D037_Id_Tributavel', 'label'=>'Unidade Tributável', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
				),
			)	
		);

		/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
		==============================================================*/
		// Callback pra pegar a classificação tributária da CST da reforma
		$callbackReformaAoAlterarCst = function ($campo) {
			return <<<EOT
				jsRecarregarCampo('{$campo}', array('cst' => \$array['campoAtual']));
			EOT;
		};
		// Callback pra trocar o select
		$callbackReformaPreencherSelectClassificacaoTributaria = function ($campo) {
			$tabela = explode('_', $campo)[0];
			return <<<EOT
				require_once("bibliotecas/classes/ReformaTributaria.php");
				\$reformaTributaria = new ReformaTributaria();
				// Se o usuário trocar a CST vamos receber ela na hora do form
				if (isset(\$array['cst'])) {
					\$cst = \$array['cst'];
				// Se é a primeira vez que o usuário abriu o form de alteração precisamos descobrir qual é ela
				} else {
					\$qObj = mysql_query("SELECT {$campo} 
										FROM {$tabela}
										WHERE {$tabela}_Id = '{\$array['acaoId']}'");
					\$mObj = mysql_fetch_assoc(\$qObj);
					\$cst = \$mObj['{$campo}'];
				}
				return \$reformaTributaria->getCclassTribFormSelect(\$cst);
			EOT;
		};
		$formArray['campos']['reforma'] = [
			'D005_Situacao_Tributaria_CBS_IBS' => array('name' => 'D005_Situacao_Tributaria_CBS_IBS', 'label' => 'CST CBS/IBS', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('D005_Classificacao_Tributaria_CBS_IBS')),
			'D005_Classificacao_Tributaria_CBS_IBS' => array('name' => 'D005_Classificacao_Tributaria_CBS_IBS', 'label' => 'Classificação Trib. CBS/IBS', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('D005_Situacao_Tributaria_CBS_IBS')),
			'D005_Situacao_Tributaria_CBS_IBS_Regular' => array('name' => 'D005_Situacao_Tributaria_CBS_IBS_Regular', 'label' => 'CST CBS/IBS Reg.', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('D005_Classificacao_Tributaria_CBS_IBS_Regular')),
			'D005_Classificacao_Tributaria_CBS_IBS_Regular' => array('name' => 'D005_Classificacao_Tributaria_CBS_IBS_Regular', 'label' => 'Classificação Trib. CBS/IBS Reg.', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('D005_Situacao_Tributaria_CBS_IBS_Regular')),
		];
		/* ============================================================== */


		$form = new Formulario('fis003FormNCM');
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
						$D005_Flag_Ativo = $form->campoValorEnviado('D005_Flag_Ativo');
						
						//Verifica se existem itens vinculados a NCM
						if($D005_Flag_Ativo == 'N') {
							$D001  = mysql_query("SELECT D001_Id FROM D001 LEFT JOIN D005 ON D005_Id=D001_D005_Id WHERE D001_D005_Id = '{$acaoId}' AND D005_Flag_Ativo = 'S'");
							$total = mysql_num_rows($D001);
							if($total > 0 ){
								$texto = ($total == 1) ? 'existe 1 produto vinculado.' : 'existem '.$total.' produtos vinculados';
								$alert = gGeraAlertMsg('Erro','NCM não pode ser inativada, '.$texto.'. Desvincule e tente novamente', false);
							}else{
								$sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form);
								// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
								$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							}							
						}else{
				            $sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form);
							// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						}

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
	break;
		
	case 'fis003FormNCMAlterar':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D149';
		$form_campo_pk = 'D149_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis003FormNcmBtnAlterar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis003/form/fis003FormNCMAlterar/';
    	// Editor
		$form_editor = array(
			'geral' => '{"D149_Flag_ST":{"top":7,"left":106.296875,"width":96,"hidden":0},"D149_Valor_VA":{"top":7,"left":324.078125,"width":88,"hidden":0},"D149_Aliquota_PIS":{"top":30,"left":152.15625,"width":88,"hidden":0},"D149_Aliquota_COFINS":{"top":30,"left":400.328125,"width":88,"hidden":0},"D149_Flag_Isento_COFINS":{"top":30,"left":639.296875,"width":57,"hidden":0},"D149_Aliquota_IRPJ":{"top":53,"left":146.046875,"width":88,"hidden":0},"D149_Aliquota_CSLL":{"top":53,"left":414.359375,"width":88,"hidden":0},"D149_Situacao_Tributaria_COFINS":{"top":53,"left":577.359375,"width":176,"hidden":0},"D149_Aliquota_IPI":{"top":76,"left":156.4375,"width":88,"hidden":0},"D149_Codigo_CEST":{"top":76,"left":413.765625,"width":88,"hidden":0},"D149_Percentual_Trib_Transparencia":{"top":99,"left":13.15625,"width":88,"hidden":0},"D149_Indicador_Escala":{"top":99,"left":345.265625,"width":96,"hidden":0},"D149_Flag_Isento_PIS":{"top":99,"left":663.125,"width":57,"hidden":0},"D149_D088_Id":{"top":122,"left":9.703125,"width":367,"hidden":0},"D149_Situacao_Tributaria_PIS":{"top":122,"left":601.171875,"width":176,"hidden":0},"D149_Regras_MVA":{"top":145,"left":67.375,"height":37,"width":360,"hidden":0},"D149_Regras_MVA_Compra":{"top":191,"left":59.453125,"height":37,"width":360,"hidden":0},"D149_UF_ST":{"top":236.984375,"left":103.84375,"height":37,"width":360,"hidden":0},"D149_UF_ST_Compras":{"top":282.984375,"left":95.921875,"height":37,"width":360,"hidden":0},"labelImportacao":{"top":324.96875,"left":304,"width":null,"hidden":1},"D149_Imposto_Importacao":{"top":353.96875,"left":153.984375,"width":88,"hidden":0},"D149_Importacao_IPI":{"top":353.96875,"left":418.65625,"width":88,"hidden":0},"D149_Importacao_PIS":{"top":376.96875,"left":142.375,"width":88,"hidden":0},"D149_Importacao_COFINS":{"top":376.96875,"left":390.546875,"width":88,"hidden":0},"D149_Observacao":{"top":399.96875,"left":110.578125,"height":62,"width":360,"hidden":0},"D149_Aliquota_ICMS_Interno_Compra":{"top":421,"left":689.34375,"width":56,"hidden":1}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D149_Aliquota_IPI' => array('name' => 'D149_Aliquota_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Codigo_CEST' => array('name' => 'D149_Codigo_CEST', 'label' => 'CEST', 'type' => 'text', 'length' => array(10,20)),
					'D149_Flag_ST' => array('name' => 'D149_Flag_ST', 'label'=>'Possui ST?', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
						)
					),
					'D149_Valor_VA' => array('name' => 'D149_Valor_VA', 'label' => 'Margem Valor agregado', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Aliquota_PIS' => array('name' => 'D149_Aliquota_PIS', 'label' => 'PIS', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Aliquota_COFINS' => array('name' => 'D149_Aliquota_COFINS', 'label' => 'COFINS', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Aliquota_IRPJ' => array('name' => 'D149_Aliquota_IRPJ', 'label' => 'IRPJ', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Aliquota_CSLL' => array('name' => 'D149_Aliquota_CSLL', 'label' => 'CSLL', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Aliquota_ICMS_Interno_Compra' => array('name' => 'D149_Aliquota_ICMS_Interno_Compra', 'label' => 'ICMS Interno compra', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_UF_ST' => array('name' => 'D149_UF_ST', 'label' => 'UF ST Venda', 'type' => 'textarea', 'length' => array(3,40)),
					'D149_UF_ST_Compras' => array('name' => 'D149_UF_ST_Compras', 'label' => 'UF ST Compra', 'type' => 'textarea', 'length' => array(3,40)),
					'D149_Regras_MVA' => array('name' => 'D149_Regras_MVA', 'label' => 'Exceções<br /> ICM/IPI/MVA Vendas', 'type' => 'textarea', 'length' => array(3,40)),
					'D149_Regras_MVA_Compra' => array('name' => 'D149_Regras_MVA_Compra', 'label' => 'Exceções<br /> ICM/IPI/MVA Compras', 'type' => 'textarea', 'length' => array(3,40)),
					'D149_Percentual_Trib_Transparencia' => array('name' => 'D149_Percentual_Trib_Transparencia', 'label' => 'Percentual Tributos (Lei transp.)', 'type' => 'moeda', 'length' => array(10,20)),
					'labelImportacao' => array('name' => 'labelImportacao', 'label' => '', 'type' => 'titulo', 'label' => 'Importação', 'reject' => true),
					'D149_Imposto_Importacao' => array('name' => 'D149_Imposto_Importacao', 'label' => 'II%', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Importacao_IPI' => array('name' => 'D149_Importacao_IPI', 'label' => 'IPI%', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Importacao_PIS' => array('name' => 'D149_Importacao_PIS', 'label' => 'PIS%', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Importacao_COFINS' => array('name' => 'D149_Importacao_COFINS', 'label' => 'COFINS%', 'type' => 'moeda', 'length' => array(10,20)),
					'D149_Observacao' => array('name' => 'D149_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(3,40)),
					'D149_D088_Id' => array('name' => 'D149_D088_Id', 'label'=>'Sit.tributária IPI Venda (Deixar em<br/>banco para usar da CFOP)', 'type' => 'select', 'select' =>
						gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao',"order by D088_Codigo_ST,D088_Descricao", true),
					),
					'D149_Indicador_Escala' => array('name' => 'D149_Indicador_Escala', 'label'=>'Indicador de Escala', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Relevante', 'value' => 'S'),
							array('title' => 'Não Relevante', 'value' => 'N'),
						)
					),
										'D149_Flag_Isento_PIS' => array('name' => 'D149_Flag_Isento_PIS', 'label'=>'Isento PIS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D149_Situacao_Tributaria_PIS' => array('name' => 'D149_Situacao_Tributaria_PIS', 'label'=>'Situação Tributária PIS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('value'=>'01','title'=>'01 - Operação Tributável com Alíquota Básica'),
							array('value'=>'02','title'=>'02 - Operação Tributável com Alíquota Diferenciada'),
							array('value'=>'03','title'=>'03 - Operação Tributável com Alíquota por Unidade de Medida de Produto'),
							array('value'=>'04','title'=>'04 - Operação Tributável Monofásica - Revenda a Alíquota Zero'),
							array('value'=>'05','title'=>'05 - Operação Tributável por Substituição Tributária'),
							array('value'=>'06','title'=>'06 - Operação Tributável a Alíquota Zero'),
							array('value'=>'07','title'=>'07 - Operação Isenta da Contribuição'),
							array('value'=>'08','title'=>'08 - Operação sem Incidência da Contribuição'),
							array('value'=>'09','title'=>'09 - Operação com Suspensão da Contribuição'),
							array('value'=>'49','title'=>'49 - Outras Operações de Saída'),
							array('value'=>'50','title'=>'50 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'51','title'=>'51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não Tributada no Mercado Interno'),
							array('value'=>'52','title'=>'52 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'53','title'=>'53 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'54','title'=>'54 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'55','title'=>'55 - Operação com Direito a Crédito - Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'56','title'=>'56 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'60','title'=>'60 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'61','title'=>'61 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'62','title'=>'62 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'63','title'=>'63 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'64','title'=>'64 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'65','title'=>'65 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'66','title'=>'66 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'67','title'=>'67 - Crédito Presumido - Outras Operações'),
							array('value'=>'70','title'=>'70 - Operação de Aquisição sem Direito a Crédito'),
							array('value'=>'71','title'=>'71 - Operação de Aquisição com Isenção'),
							array('value'=>'72','title'=>'72 - Operação de Aquisição com Suspensão'),
							array('value'=>'73','title'=>'73 - Operação de Aquisição a Alíquota Zero'),
							array('value'=>'74','title'=>'74 - Operação de Aquisição sem Incidência da Contribuição'),
							array('value'=>'75','title'=>'75 - Operação de Aquisição por Substituição Tributária'),
							array('value'=>'98','title'=>'98 - Outras Operações de Entrada'),
							array('value'=>'99','title'=>'99 - Outras Operações'),
						),
					),
					'D149_Flag_Isento_COFINS' => array('name' => 'D149_Flag_Isento_COFINS', 'label'=>'Isento COFINS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D149_Situacao_Tributaria_COFINS' => array('name' => 'D149_Situacao_Tributaria_COFINS', 'label'=>'Situação Tributária COFINS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('value'=>'01', 'title'=>	'01 - Operação Tributável com Alíquota Básica'),
							array('value'=>'02', 'title'=>	'02 - Operação Tributável com Alíquota Diferenciada'),
							array('value'=>'03', 'title'=>	'03 - Operação Tributável com Alíquota por Unidade de Medida de Produto'),
							array('value'=>'04', 'title'=>	'04 - Operação Tributável Monofásica - Revenda a Alíquota Zero'),
							array('value'=>'05', 'title'=>	'05 - Operação Tributável por Substituição Tributária'),
							array('value'=>'06', 'title'=>	'06 - Operação Tributável a Alíquota Zero'),
							array('value'=>'07', 'title'=>	'07 - Operação Isenta da Contribuição'),
							array('value'=>'08', 'title'=>	'08 - Operação sem Incidência da Contribuição'),
							array('value'=>'09', 'title'=>	'09 - Operação com Suspensão da Contribuição'),
							array('value'=>'49', 'title'=>	'49 - Outras Operações de Saída'),
							array('value'=>'50', 'title'=>	'50 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'51', 'title'=>	'51 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'52', 'title'=>	'52 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'53', 'title'=>	'53 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'54', 'title'=>	'54 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'55', 'title'=>	'55 - Operação com Direito a Crédito - Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'56', 'title'=>	'56 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'60', 'title'=>	'60 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'61', 'title'=>	'61 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'62', 'title'=>	'62 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'63', 'title'=>	'63 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'64', 'title'=>	'64 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'65', 'title'=>	'65 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'66', 'title'=>	'66 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'67', 'title'=>	'67 - Crédito Presumido - Outras Operações'),
							array('value'=>'70', 'title'=>	'70 - Operação de Aquisição sem Direito a Crédito'),
							array('value'=>'71', 'title'=>	'71 - Operação de Aquisição com Isenção'),
							array('value'=>'72', 'title'=>	'72 - Operação de Aquisição com Suspensão'),
							array('value'=>'73', 'title'=>	'73 - Operação de Aquisição a Alíquota Zero'),
							array('value'=>'74', 'title'=>	'74 - Operação de Aquisição sem Incidência da Contribuição'),
							array('value'=>'75', 'title'=>	'75 - Operação de Aquisição por Substituição Tributária'),
							array('value'=>'98', 'title'=>	'98 - Outras Operações de Entrada'),
							array('value'=>'99', 'title'=>	'99 - Outras Operações'),
						),
					),
				)
			)	
		);
		$form = new Formulario('fis003FormNCMAlterar');
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
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fis003FormNcmBtnAlterar">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
		
	case 'fis003FormImportacao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D005';
		$form_campo_pk = 'D005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis003FormImportacaoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis003/form/fis003FormImportacao/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(		
					'D005_Imposto_Importacao' => array('name' => 'D005_Imposto_Importacao', 'label' => 'II%', 'type' => 'moeda', 'length' => array(10,20)),					
					'D005_Importacao_IPI' => array('name' => 'D005_Importacao_IPI', 'label' => 'IPI%', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Importacao_PIS' => array('name' => 'D005_Importacao_PIS', 'label' => 'PIS%', 'type' => 'moeda', 'length' => array(10,20)),
					'D005_Importacao_COFINS' => array('name' => 'D005_Importacao_COFINS', 'label' => 'COFINS%', 'type' => 'moeda', 'length' => array(10,20)),
					)
				)	
			);
		$form = new Formulario('fis003FormNCM');
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
		$botoesDir .= '<button type="submit" id="fis003FormImportacaoBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
}
?>