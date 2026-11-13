<?php
namespace hardness;
require_once('prog/cad/cad040/form_func-js.php');
switch ($g['r'][3]) {
	case 'cad040FormCertificadoConformidade':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$D106_Id = (isset($r_D106_Id)) ? $r_D106_Id : false;
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
		$form_tabela = 'D125';
		$form_campo_pk = 'D125_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad040FormCertificadosConformidadeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad040/form/cad040FormCertificadoConformidade/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D128 on D106_D128_Id=D128_Id		
EOT;

		$pesquisaGrid = array(
					'D106',																								// tabela
					'D106_Id',																							// chave primaria
					'D106_Descricao,D106_D128_Id,D128_Tipo,D106_Flag_Status',														// campos
					$pesquisaGridExtra,																					// extras sql
					'Pesquisa de Bem',																					// título da janela
					'Descrição,,Tipo,Status',																			// descrição dos campos
					'campoProcuraBem',																					// callback js
					$g['divId'],
					'',
					array(array(),array('colunaExibir'=>false),array(),array('callback'=>'validaCampoStatus')),
					
		);
		$sql = "select D106_Descricao from D106 where D106_Id='{$D106_Id}'";
		$array = mysql_query($sql);
		$row = mysql_fetch_assoc($array);
		$D106_Descricao = $row['D106_Descricao'];
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D125_Numero' => array('name' => 'D125_Numero', 'label' => 'Número', 'type' => 'text', 'length' => array(15,60)),
					'D125_Emissao' => array('name' => 'D125_Emissao', 'label' => 'Emissão', 'type' => 'data', 'value'=>date('d/m/Y'), 'length' => array(15,60)),
					'D125_Validade' => array('name' => 'D125_Validade', 'label' => 'Validade', 'type' => 'data',  'length' => array(15,60)),
					'Procura_Bem' => array('name' => 'Procura_Bem','value' => $D106_Descricao, 'label'=>'Bem', 'type' => 'pesquisaGrid', 'pesquisaGrid'=> $pesquisaGrid,'length' => array(40,60), 'reject'=>true),
					'D125_T141_Id' => array('name' => 'D125_T141_Id', 'label' => 'Contrato', 'type' => 'select', 
						'select' =>  gGeraSelect('T141','T141_Id','T141_Id,D024_Nome_Fantasia',"left join D024 on D024_Id=T141_D024_Id where T141_Flag_Status='A'"),
					),
					'D125_D106_Id' => array('name' => 'D125_D106_Id', 'label' => 'Observação', 'type' => 'hidden',  'length' => array(2,37)),
					'D125_Observacao' => array('name' => 'D125_Observacao', 'label' => 'Observação', 'type' => 'textarea',  'length' => array(2,37)),
				)
			)
		);
        $form = new Formulario('cad040FormCertificadosConformidade');
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
						$D106_Id = $form->campoValorEnviado('D125_D106_Id');
						$acaoId = $g['mysqlLastId'];
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' , unique(), '', 'Editar Certificado de Conformidade - ID: ' + '{$acaoId}', '/cad/cad040/content/cad040ContentCadastroCertificadoConformidade/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}') + '&D106_Id=' + encodeURIComponent('{$D106_Id}'), '[500,300]');
										
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
		$botoesDir .= '<button type="submit" id="cad040FormCertificadosConformidadeBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		if(!empty($r_acaoId)){
			$botoesEsq .= "<button onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Certificado de Conformidade', '/cad/cad040/outro/cad040OutroCertificado/', '&acaoId=' + encodeURIComponent('{$r_acaoId}'), [1000,600]); return false;\">Imprimir</button>";
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad040FormAcessorios':
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
			$form_tabela = 'T164';
			$form_campo_pk = 'T164_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad040FormAcessorios_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad040/form/cad040FormAcessorios/';

			// novo formulário
			$form = new Formulario('cad040FormAcessorios');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T164_D125_Id' => array('name' => 'T164_D125_Id','label'=>'D125_Id', 'type' => 'hidden', 'value' => @$r_D125_Id),
						'T164_D106_Id' => array('name' => 'T164_D106_Id','label'=>'Bem', 'type' => 'pesquisa','length' => array(50,60),
							'pesquisa' => array('D106', 'D106_Id', 'D106_Descricao'),
						),
						'cad040FormAcessorios_btn' => array('name' => 'cad040FormAcessorios_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
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
								echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
							}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
			echo  $gerado['pagina'];
			if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
			$g['smarty']->display('form.tpl');

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			//$botoesDir .= '<button type="submit" id="content_form5_btn">Enviar</button>';
			//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

		break;
	
	
}
?>