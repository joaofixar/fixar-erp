<?php
namespace hardness;
require_once('prog/ven/ven009/form_func-js.php');

/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: ven009tab02
	 */
	case 'ven009form01':
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
		$form_tabela = 'T143';
		$form_campo_pk = 'T143_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn_ven009';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven009/form/ven009form01/';

		$r_D106_Id = isset($r_form_ven009_form1) ? $r_form_ven009_form1['D106_Id'] : (isset($r_D106_Id)?$r_D106_Id : null);
		$r_T141_Id = isset($r_form_ven009_form1) ? $r_form_ven009_form1['T141_Id'] : (isset($r_T141_Id)?$r_T141_Id : null);

		$form = new Formulario('form_ven009_form1');
		$formArray = array(
			'campos' => array(
				'logistica_aluguel' => array(
				)
			)
		);
		if (!$r_D106_Id){
			$formArray['campos']['logistica_aluguel']['T143_D106_Id'] 			= array('name' => 'Consulta_T143_D106_Id','label'=>'Bem', 'type' => 'pesquisa','pesquisa'=>array('D106','D106_Id','D106_Descricao'), 'length' => array(65,30));
		}else{
			$r_D106_Id = isset($r_form_ven009_form1) ? $r_form_ven009_form1['T143_D106_Id'] : (isset($r_D106_Id)?$r_D106_Id : null);
			$tmp = gGeraSelect('D106', 'D106_Id', 'D106_Descricao', "WHERE D106_Id = $r_D106_Id");
			$formArray['campos']['logistica_aluguel']['__T143_D106_Id'] 		= array('name' => '__T143_D106_Id', 'label'=>'Bem', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' =>
			$tmp
			);
			$formArray['campos']['logistica_aluguel']['T143_D106_Id'] 				= array('name' => 'T143_D106_Id', 'type' => 'hidden', 'value' => $r_D106_Id);
		}
		if(!$r_T141_Id){
			$formArray['campos']['logistica_aluguel']['T143_T141_Id'] 				= array('name' => 'T143_T141_Id', 'label'=>'Contrato', 'type' => 'select', 'extra'=>'width:133px;','select' => gGeraSelect('T141','T141_Id','T141_Id,D024_Nome_Fantasia','left join D024 on D024_Id=T141_D024_Id where T141_Flag_Status="A" order by T141_Id'));
		}else{
			$tmp = gGeraSelect('T141', 'T141_Id', 'T141_Id,D024_Nome_Fantasia', "left join D024 on D024_Id=T141_D024_Id where T141_Flag_Status='A' AND T141_Id = $r_T141_Id order by T141_Id");
			$formArray['campos']['logistica_aluguel']['__T143_T141_Id'] 				= array('name' => '__T143_T141_Id', 'label'=>'Contrato', 'type' => 'select','disabled' => true, 'reject'=> true, 'extra'=>'width:133px;','select' => 
			$tmp
			);
			$formArray['campos']['logistica_aluguel']['T143_T141_Id'] 				= array('name' => 'T143_T141_Id', 'type' => 'hidden', 'value' => $r_T141_Id);
		}
		$formArray['campos']['logistica_aluguel']['T143_D030_Id'] 				= array('name' => 'T143_D030_Id', 'label'=>'País', 'type' => 'select', 'extra'=>'width:133px;', 'select' => gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30));
		$formArray['campos']['logistica_aluguel']['T143_D018_Id'] 				= array('name' => 'T143_D018_Id', 'label'=>'Estado', 'type' => 'select', 'extra'=>'width:133px;', 'select' => gGeraSelect('D018', 'D018_Id', 'D018_Estado', 'ORDER BY D018_UF ASC', true), 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"");
		$formArray['campos']['logistica_aluguel']['T143_D020_Id'] 				= array('name' => 'T143_D020_Id', 'label'=>'Cidade', 'type' => 'select', 'extra'=>'width:133px;', 'select' => gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', '', true));
		$formArray['campos']['logistica_aluguel']['T143_Data_Transferencia'] 	= array('name' => 'T143_Data_Transferencia', 'label'=>'Data Transferência','type' => 'data', 'length' => array(20,10));
		$formArray['campos']['logistica_aluguel']['T143_Data_Prev_Retorno'] 	= array('name' => 'T143_Data_Prev_Retorno', 'label'=>'Data Prev. Retorno','type' => 'data', 'length' => array(20,10));
		$formArray['campos']['logistica_aluguel']['T143_Observacao'] 			= array('name' => 'T143_Observacao','label'=>'Observação', 'type' => 'textarea', 'length' => array(3,60));
		$formArray['campos']['logistica_aluguel']['T143_C004_Id'] 				= array('name' => 'T002_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']);
		// Chaves externas para permitir ou não a edição do campo
		$formArray['campos']['logistica_aluguel']['T141_Id'] 					= array('name' => 'T141_Id', 'type' => 'hidden', 'value' => $r_T141_Id, 'reject' => true);
		$formArray['campos']['logistica_aluguel']['D106_Id'] 					= array('name' => 'D106_Id', 'type' => 'hidden', 'value' => $r_D106_Id, 'reject' => true);
			
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
		
		$botoesDir .= '<button type="submit" id="content_form1_btn_ven009">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
}
?>