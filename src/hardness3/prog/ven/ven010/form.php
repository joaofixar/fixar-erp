<?php
namespace hardness;

require_once('prog/ven/ven010/form_func-js.php');
//Classes
require_once("bibliotecas/classes/CAD010.php");
$CAD010 = new CAD010();
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: ven010tab02
	 */
	case 'ven010form01':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		if(!empty($acaoId)){
			$CAD010->atualizaTotalMensalGrid($acaoId);
		}
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
		$form_tabela = 'T141';
		$form_campo_pk = 'T141_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn_extra';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven010/form/ven010form01/';
		
		if (empty($acaoId)){
			$geracaoSelectVeiculos = gGeraSelect('D116', 'D116_Id', 'D116_Modelo','WHERE 1=2', true);
		}else{
			$sql = "SELECT D116_D024_Id FROM T141 LEFT JOIN D116 on D116_D024_Id = T141_D024_Id WHERE T141_Id=" . $acaoId . " LIMIT 1";	
			$result = mysql_query($sql);
			$retorno = mysql_fetch_assoc($result);
			if (!empty($retorno['D116_D024_Id']))			
				$geracaoSelectVeiculos = gGeraSelect('D116', 'D116_Id', 'D116_Modelo','WHERE D116_D024_Id='.$retorno['D116_D024_Id'], true);
			else
				$geracaoSelectVeiculos = gGeraSelect('D116', 'D116_Id', 'D116_Modelo', 'WHERE 1=2', true);
		}
		// novo formulário
		$form = new Formulario('form_ven010_form1');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'contratos_locacao' => array(
					'T141_Id' => array('name' => 'T141_Id','type' => 'hidden','reject'=>true),
					'T141_C004_Id' => array('name' => 'T141_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']),
					'T141_D024_Id' => array('name' => 'Consulta_T141_D024_Id','label'=>'Cliente', 'extra' => "onchange=\"carregaVeicEmb(this, '{$g['divId']}');\"", 'type' => 'pesquisa','pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(65,30)),
					'T141_D116_Id' => array('name' => 'T141_D116_Id', 'label'=>'Veículo', 'type' => 'select', 'extra'=>'style="width:250px;"','select' => 
						$geracaoSelectVeiculos,
					),
					'T141_Data_Inicio' => array('name' => 'T141_Data_Inicio', 'label'=>'Data Início','type' => 'data', 'length' => array(10,10)),
					'T141_Data_Validade' => array('name' => 'T141_Data_Validade','label'=>'Data Validade ', 'type' => 'data', 'length' => array(10,10)),
					'T141_Dia_Vencimento' => array('name' => 'T141_Dia_Vencimento', 'label'=>'Dia Vencimento', 'type' => 'text','length' => array(10,2)),
					'T141_Flag_Status' => array('name' => 'T141_Flag_Status', 'label' => 'Status', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Ativo', 'value' => 'A'),
							array('title' => 'Inativo', 'value' => 'I'),
						),
					),
					//'T141_Valor_Mensal' => array('name' => 'T141_Valor_Mensal', 'label'=>'Valor Mensal','type' => 'moeda','disabled' => true, 'length' => array(20,10)),
				)
			)
		);
		if (empty($acaoId)) {
			 unset($formArray['campos']['contratos_locacao']['T141_Valor_Mensal']);
		}

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
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Contrato - ID: ' + '{$g['mysqlLastId']}', '/ven/ven010/content/ven010content02/', '&acaoId=' + encodeURIComponent('{$g['mysqlLastId']}'), '[520,300]');
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		
		//if (isset($r_mostraGerar))
			//$botoesDir .= '<button onclick="VEN010GerarContasReceber(\''.$form->campoValor('T141_Id').'\',\''.$r_divIdContent.'\'); return false;" class="btnFechar">Gerar Recebimentos</button>';
		//if (!$r_readOnly)
		$botoesDir .= '<button type="submit" id="content_form1_btn_extra">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'ven010FormBens':
		
		$T141_Id =  (isset($r_T141_Id)) ? $r_T141_Id : false;
		
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T158';
		$form_campo_pk = 'T158_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven010FormCadastroBensBtnAdd', 'ven010FormCadastroBensBtnCln', 'ven010FormCadastroBensBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven010/form/ven010FormBens/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T158_D106_Id' => array('name' => 'T158_D106_Id', 'label' => 'Bem', 'type' => 'pesquisa', 'pesquisa' => array('D106', 'D106_Id', 'D106_Descricao'), 'length' => array(50,30)),
					'T158_Valor_Mensal' => array('name' => 'T158_Valor_Mensal', 'label' => 'Valor Mensal', 'type' => 'moeda', 'length' => array(15,90)),
					'T158_T141_Id' => array('name' => 'T158_T141_Id', 'label' => 'Valor Mensal', 'type' => 'hidden', 'value'=>$T141_Id, 'length' => array(15,90)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
        $form = new Formulario('formVen010FormBens');
		
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['ven010FormCadastroBensBtnNew'] = array('name' => 'ven010FormCadastroBensBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['ven010FormCadastroBensBtnCln'] = array('name' => 'ven010FormCadastroBensBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven010FormCadastroBensBtnAdd'] = array('name' => 'ven010FormCadastroBensBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
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
						$retorno = $CAD010->atualizaTotalMensal($form);
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
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
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$retorno = $CAD010->atualizaTotalMensal($form);
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

	break;
}
?>
