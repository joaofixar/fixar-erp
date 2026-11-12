<?php
namespace hardness;        
switch ($g['r'][3]) {
	/**
	* @author Lígia Nakazato
	*/
	case 'cad064FormCadastrarVeiculos':
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
		$form_tabela = 'D170';
		$form_campo_pk = 'D170_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad064FormCadastrarVeiculosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad064/form/cad064FormCadastrarVeiculos/';

		// Editor
		$form_editor = array(
			'geral' => '{"D170_Identificacao_Veiculo":{"top":7,"left":52.78125,"width":152,"hidden":0},"D170_Placa":{"top":30,"left":87.03125,"width":152,"hidden":0},"D170_Renavam":{"top":53,"left":74.796875,"width":152,"hidden":0},"D170_Tara":{"top":76,"left":72.359375,"width":152,"hidden":0},"D170_Capacidade_Kg":{"top":99,"left":42.390625,"width":152,"hidden":0},"D170_Capacidade_M3":{"top":122,"left":43.015625,"width":152,"hidden":0},"D170_Flag_Rodado":{"top":145,"left":56.25,"width":160,"hidden":0},"D170_Flag_Carroceria":{"top":168,"left":41.59375,"width":160,"hidden":0},"D170_D018_Id":{"top":191,"left":25.890625,"width":79,"hidden":0},"D170_Categoria":{"top":214,"left":19.75,"width":160,"hidden":0},"D170_D109_Id":{"top":237,"left":39.34375,"width":282,"hidden":0},"btnIncluirMotorista":{"top":235,"left":432,"width":null,"hidden":0},"D170_Flag_Ativo":{"top":260,"left":90.09375,"width":80,"hidden":0}}',
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D170_Identificacao_Veiculo' => array('name' => 'D170_Identificacao_Veiculo', 'label' => 'Identificação'),
					'D170_Placa' => array('name' => 'D170_Placa', 'label' => 'Placa'),
					'D170_Renavam' => array('name' => 'D170_Renavam', 'label' => 'Renavam'),
					'D170_Tara' => array('name' => 'D170_Tara', 'label' => 'Tara(Kg)'),
					'D170_Capacidade_Kg' => array('name' => 'D170_Capacidade_Kg', 'label' => 'Capacidade(Kg)'),
					'D170_Capacidade_M3' => array('name' => 'D170_Capacidade_M3', 'label' => 'Capacidade(m³)'),
					'D170_Flag_Rodado' => array('name' => 'D170_Flag_Rodado', 'label' => 'Tipo rodado', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Truck', 'value' => '01'),
							array('title' => 'Toco', 'value' => '02'),
							array('title' => 'Cavalo Mecânico', 'value' => '03'),
							array('title' => 'Van', 'value' => '04'),
							array('title' => 'Utilitário', 'value' => '05'),
							array('title' => 'Outros', 'value' => '06'),
						)
					),
					'D170_Flag_Carroceria' => array('name' => 'D170_Flag_Carroceria', 'label' => 'Tipo carroceria', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não aplicável', 'value' => '00'),
							array('title' => 'Aberta', 'value' => '01'),
							array('title' => 'Fechada/Baú', 'value' => '02'),
							array('title' => 'Graneleira', 'value' => '03'),
							array('title' => 'Porta-conteiner', 'value' => '04'),
							array('title' => 'Sider', 'value' => '05'),
						)
					),
					'D170_D018_Id' => array('name' => 'D170_D018_Id', 'label' => 'UF Licenciamento', 'type' => 'select','align'=>'left', 'style'=>'float:left', 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D170_Categoria' => array('name' => 'D170_Categoria', 'label' => 'Categoria do Veículo', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Reboque', 'value' => '1'),
							array('title' => 'Tração', 'value' => '2'),
						)
					),
					'D170_D109_Id' => array('name' => 'D170_D109_Id','label'=>'Motorista padrão','type' => 'pesquisa', 'pesquisa' => array('D109','D109_Id','D109_Nome','WHERE D109_Funcao = "MOTORISTA"'), 'length' => array(80,30)),
					'D170_Flag_Ativo' => array('name' => 'D170_Flag_Ativo', 'label' => 'Ativo','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D170_C004_Id' => array('name' => 'D170_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'btnIncluirMotorista' => array('name' => 'btnIncluirMotorista', 'type' => 'button', 'reload' => false, 'value' => 'Novo Motorista', 'reject' => true),
				)
			)
		);
		$form = new Formulario('cad064FormCadastrarVeiculos');
		$form->definirFormularioArray($formArray);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#btnIncluirMotorista').unbind('click');
				$('#btnIncluirMotorista').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Motorista', '/cad/cad010/content/cad010content02/', '&tab=geral&typeForm=cad064GridCadastroVeiculos',  [550,550]);
					return false;
				});
		    } </script></div>
EOT;

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
				$D170_Categoria = $form->campoValorEnviado('D170_Categoria');
				$D170_D109_Id = $form->campoValorEnviado('D170_D109_Id');
				log("Categoria: ".$D170_Categoria);
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					if($D170_Categoria == 1 && !empty($D170_D109_Id)){
						echo gGeraAlertMsg('Erro', 'Não pode haver motorista para veículo tipo Reboque');
					}else{
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
					}
				} else {
					// Insert
					$D170_Placa = $form->campoValorEnviado('D170_Placa');
					$D170_D018_Id = $form->campoValorEnviado('D170_D018_Id');
					$sql = mysql_query("SELECT D170_Placa, D170_D018_Id FROM D170 WHERE D170_Placa = \"{$D170_Placa}\" AND D170_D018_Id = {$D170_D018_Id} AND D170_C004_Id = '{$g['empresaAtual']}'");
					if($D170_Categoria == 1 && !empty($D170_D109_Id)){
						echo gGeraAlertMsg('Erro', 'Não pode haver motorista para veículo tipo Reboque');
					}else if(mysql_num_rows($sql) > 0){
						echo gGeraAlertMsg('Erro', 'O número de placa para esta UF já foi cadastrada.');
					} else {
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!', false);
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
		$botoesDir .= '<button type="submit" id="cad064FormCadastrarVeiculosBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
