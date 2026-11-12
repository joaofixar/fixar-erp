<?php
namespace hardness;
require_once('prog/cad/cad016/form_func-js.php');           
switch ($g['r'][3]) {
	/**
	* @author 
	* @since 16/11/2011  
	*/
	case 'cad016FormVendedores':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad016FormVendedoresBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad016/form/cad016FormVendedores/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"C007_Flag_Vendedor":{"top":7,"left":63,"width":90,"hidden":0},"buttonSearchCep":{"top":258,"left":208,"width":null,"hidden":0},"C007_Email":{"top":30,"left":62,"width":225,"hidden":0},"C007_Msn":{"top":53,"left":68,"width":225,"hidden":0},"C007_Telefone_Direto":{"top":76,"left":144,"width":75,"hidden":0},"C007_DDD_Telefone_Direto":{"top":76,"left":19,"width":30,"hidden":0},"C007_Primeiro_Nome":{"top":99,"left":16,"width":125,"hidden":0},"C007_Nome":{"top":122,"left":9,"width":225,"hidden":0},"C007_Cargo":{"top":145,"left":61,"width":125,"hidden":0},"C007_C007_Id":{"top":168,"left":37,"width":132,"hidden":0},"C007_Flag_Expedicao":{"top":191,"left":40,"width":132,"hidden":0},"C007_Rg":{"top":214,"left":75,"width":125,"hidden":0},"C007_Cpf":{"top":237,"left":64,"width":125,"hidden":0},"C007_Cep":{"top":260,"left":71,"width":100,"hidden":0},"C007_Endereco":{"top":282.9999694824219,"left":44,"width":225,"hidden":0},"C007_Complemento":{"top":305.9999694824219,"left":26,"width":125,"hidden":0},"C007_Bairro":{"top":328.9999694824219,"left":61,"width":125,"hidden":0},"C007_D018_Id":{"top":351.9999694824219,"left":77,"width":41,"hidden":0},"C007_D020_Id":{"top":374.9999694824219,"left":57,"width":219,"hidden":0},"C007_Telefone":{"top":397.9999694824219,"left":144,"width":75,"hidden":0},"C007_DDD_Telefone":{"top":397.9999694824219,"left":49,"width":30,"hidden":0},"C007_Ativo":{"top":420.9999694824219,"left":65,"width":48,"hidden":0},"C007_Especial":{"top":443.9999694824219,"left":50,"width":48,"hidden":0},"C007_Usuario_Ancora":{"top":466.9999694824219,"left":16,"width":48,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'C007_Flag_Vendedor' => array('name' => 'C007_Flag_Vendedor', 'label' => 'Tipo', 'type' => 'select', 'length' => array(1,255), 
							'select' => array(
											array('title' => 'Vendedor', 'value' => 'V', 'selected' => 'C007_Flag_Vendedor'=='V' ? 'true' : 'false'), 
											array('title' => 'Supervisor', 'value' => 'S', 'selected' => 'C007_Flag_Vendedor'=='S' ? 'true' : 'false'))),
					'C007_Email' => array('name' => 'C007_Email', 'label' => 'E-mail', 'type' => 'text', 'length' => array(40,80)), 
					'C007_Msn' => array('name' => 'C007_Msn', 'label' => 'MSN', 'type' => 'text', 'length' => array(40,80)),
					'C007_DDD_Telefone_Direto' => array('name' => 'C007_DDD_Telefone_Direto', 'label' => 'Telefone direto', 'type' => 'text', 'length' => array(1,2)), 
					'C007_Telefone_Direto' => array('name' => 'C007_Telefone_Direto', 'label' => '', 'align'=>'right', 'type' => 'text', 'length' => array(10,9)), 
					'C007_Primeiro_Nome' => array('name' => 'C007_Primeiro_Nome', 'label' => 'Primeiro nome', 'type' => 'text', 'length' => array(20,20)), 
					'C007_Nome' => array('name' => 'C007_Nome', 'label' => 'Nome Completo', 'type' => 'text', 'length' => array(40,40)), 
					'C007_Cargo' => array('name' => 'C007_Cargo', 'label' => 'Cargo', 'type' => 'text', 'length' => array(20,20)), 
					
					'C007_C007_Id' => array('name' => 'C007_C007_Id', 'label' => 'Supervisor', 'type' => 'select', 'extra' => 'style="width:132px;"', 'select' => gGeraSelect('C007', 'C007_Id', 'C007_Nome', "where (C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S'", 'true')), 
					
					'C007_Flag_Expedicao' => array('name' => 'C007_Flag_Expedicao', 'label' => 'Expedição', 'type' => 'select', 'extra' => 'style="width:132px;"', 'select' => array(
												array('title' => '...', 'value' => '0', 'selected' => 'C007_Flag_Expedicao'=='0' ? 'true' : 'false'),
												array('title' => 'Comercial', 'value' => '1', 'selected' => 'C007_Flag_Expedicao'=='1' ? 'true' : 'false'),
												array('title' => 'Balcão', 'value' => '2', 'selected' => 'C007_Flag_Expedicao'=='2' ? 'true' : 'false'))), 
					
					'C007_Rg' => array('name' => 'C007_Rg', 'label' => 'RG', 'type' => 'text', 'length' => array(20,15)), 
					'C007_Cpf' => array('name' => 'C007_Cpf', 'label' => 'CPF', 'type' => 'cpf'), 
                    'C007_Cep' => array('name' => 'C007_Cep', 'label' => 'CEP', 'type' => 'cep', 'length' => array(15,10)),
                    'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"C007_Cep\", {\"EnderecoDb\": \"C007_Endereco\", \"Cep\": \"C007_Cep\", \"BairroDb\": \"C007_Bairro\", \"UfDb\": \"C007_D018_Id\",\"CidadeDb\": \"C007_D020_Id\"})'", 'align'=>'right', 'value'=>'Buscar', 'reject'=>true),
					'C007_Endereco' => array('name' => 'C007_Endereco', 'label' => 'Endereço', 'type' => 'text', 'length' => array(40,80)), 
					'C007_Complemento' => array('name' => 'C007_Complemento', 'label' => 'Complemento', 'type' => 'text', 'length' => array(20,20)), 
					'C007_Bairro' => array('name' => 'C007_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(20,20)), 
					'C007_D018_Id' => array('name' => 'C007_D018_Id', 'label' => 'UF', 	'type' => 'select','extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'C007_D020_Id' => array('name' => 'C007_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'C007_DDD_Telefone' => array('name' => 'C007_DDD_Telefone', 'label' => 'Telefone', 'type' => 'text', 'length' => array(1,2)), 
					'C007_Telefone' => array('name' => 'C007_Telefone', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'), 
					
					'C007_Ativo' => array('name' => 'C007_Ativo', 'label' => 'Ativo', 'type' => 'select', 'length' => array(1,255), 'select' => array(
											array('title' => 'Sim', 'value' => 'S', 'selected' => 'C007_Ativo'=='S' ? 'true' : 'false'),
											array('title' => 'Não', 'value' => 'N', 'selected' => 'C007_Ativo'=='S' ? 'true' : 'false'))), 
					
					'C007_Especial' => array('name' => 'C007_Especial', 'label' => 'Especial', 'type' => 'select', 'length' => array(1,255), 'select' => array(
											array('title' => 'Sim', 'value' => 'S', 'selected' => 'C007_Especial'=='S' ? 'true' : 'false'), 
											array('title' => 'Não', 'value' => 'N', 'selected' => 'C007_Especial'=='N' ? 'true' : 'false'))), 
																	
					'C007_Usuario_Ancora' => array('name' => 'C007_Usuario_Ancora', 'label' => 'Usuário âncora', 'type' => 'select', 'length' => array(1,255), 'select' => array(
											array('title' => 'Sim', 'value' => 'S', 'selected' => 'C007_Usuario_Ancora'=='S' ? 'true' : 'false'), 
											array('title' => 'Não', 'value' => 'N', 'selected' => 'C007_Usuario_Ancora'=='N' ? 'true' : 'false'))), 
				)
			)
		);
        $form = new Formulario('form-cad016FormPrincipal');
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
		$botoesDir .= '<button type="submit" id="cad016FormVendedoresBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
