<?php
namespace hardness;
require_once('prog/fis/fis012/form_func-js.php');
//Classes
require_once("bibliotecas/classes/FIS012.php");
$FIS012 = new FIS012();

switch ($g['r'][3]) {
	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCancelarMdfe':
		global $g;
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
		$form_tabela = 'T276';
		$form_campo_pk = 'T276_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormCancelarMdfeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCancelarMdfe/';
        
        //form editor
        $form_editor = array(
            'geral' => ''
        );
		
		$form = new Formulario('fis012FormCancelarMdfe');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T276_Observacao_Cancelamento' => array('name' => 'T276_Observacao_Cancelamento', 'label' => 'Observação do Cancelamento', 'type' => 'textarea', 'length' => array(8, 70),
						'value' => "MDF-e cancelado por: {$g['C007']['C007_Nome']}\nData e hora : ".agora()."\nMotivo do Cancelamento : ")
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
				$retorno = $FIS012->cancelarMdfe($form, $r_T276_Id);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Operação OK', "MDF-e Cancelado!");	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
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
	
		$botoesDir = '<button type="submit" id="fis012FormCancelarMdfeBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis012FormAlterarDescarregamento':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

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
		$form_tabela = 'T284';
		$form_campo_pk = 'T284_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012AlterarDescarregamento';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormAlterarDescarregamento/';

		$form_editor = array(
			'geral' => '{"Novo_D018_Descarregamento":{"top":7,"left":43.21875,"width":111,"hidden":0},"Novo_D020_Descarregamento":{"top":30,"left":23.015625,"width":254,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fis012FormAlterarDescarregamento');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Novo_D018_Descarregamento' => array('name' => 'Novo_D018_Descarregamento','reject' => true, 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoDescarregamento(this, '{$g['divId']}')\"", 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)
					),
					'Novo_D020_Descarregamento' => array('name' => 'Novo_D020_Descarregamento','reject' => true, 'label' => 'Cidade', 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
				),
			)
		);

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
				if (empty($T276_Id)) {

					$alert = gGeraAlertMsg('Erro', 'Não identificado número do MDF-e!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							{$alert}
						} </script></div>
EOT;
				} else {

					$D018_Id = $form->campoValorEnviado('Novo_D018_Descarregamento');
					$D020_Id = $form->campoValorEnviado('Novo_D020_Descarregamento');

					$retorno=$FIS012->alterarLocalDescarregamento($T276_Id,$D018_Id,$D020_Id);

					if ($retorno===true) {
						$alert = gGeraAlertMsg('Operação OK', 'Registros Alterados com Sucesso!', false);
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
					}else{
						$alert = gGeraAlertMsg('Erro', "Erro ao alterar descarregamento:<br> <br>".$retorno, false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012FormCadastrarMotoristaMdfe':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

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
		$form_tabela = 'T278';
		$form_campo_pk = 'T278_Id';
		// valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarMotoristaMdfeBtnAdd', 'fis012FormCadastrarMotoristaMdfeBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarMotoristaMdfe/';
		$form_editor = array(
			'geral' => '{"T278_D109_Id":{"top":7,"left":6.3125,"width":218,"hidden":0},"fis012FormCadastrarMotoristaMdfeBtnNew":{"top":5,"left":304,"width":null,"hidden":0},"fis012FormCadastrarMotoristaMdfeBtnAdd":{"top":5,"left":400,"width":null,"hidden":0}}',
		);
		
		// Array de definição do formulário
		$form = new Formulario('fis012FormCadastrarMotoristaMdfe');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T278_D109_Id' => array('name' => 'T278_D109_Id', 'label' => 'Motorista', 'type' => 'pesquisa', 'pesquisa' => array('D109', 'D109_Id', 'D109_Nome, D109_Cpf', 'WHERE D109_funcao = "MOTORISTA" AND D109_Flag_Ativo = "S" ORDER BY D109_Nome'), 'extra' => "onchange=\"carregaDadosMotorista(this, '{$g['divId']}');\""),
					'T278_T276_Id' => array('name' => 'T278_T276_Id', 'label' => '', 'type' => 'hidden', 'value' => $T276_Id),
					'T278_Nome' => array('name' => 'T278_Nome', 'label' => 'Nome', 'type' => 'hidden'),
					'T278_CPF' => array('name' => 'T278_CPF', 'label' => 'CPF', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				),
			)
		);

		// Botões do form
		$formArray['campos']['geral']['fis012FormCadastrarMotoristaMdfeBtnNew'] = array('name' => 'fis012FormCadastrarMotoristaMdfeBtnNew', 'type'=>'submit', 'reload' => false, 'value' => '+', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['fis012FormCadastrarMotoristaMdfeBtnAdd'] = array('name' => 'fis012FormCadastrarMotoristaMdfeBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#fis012FormCadastrarMotoristaMdfeBtnNew').unbind('click');
				$('#fis012FormCadastrarMotoristaMdfeBtnNew').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro de Motorista', '/cad/cad010/content/cad010content02/', '&tab=geral&typeForm=cad064GridCadastroVeiculos',  [550,550]);
					return false;
				});
			} </script></div>
EOT;

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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', 0);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					$D109_Id = $form->campoValorEnviado('T278_D109_Id');
					$T276_Id = $form->campoValorEnviado('T278_T276_Id');
					$sql = mysql_query("SELECT T278_Id FROM T278 WHERE T278_T276_Id = {$T276_Id} AND T278_D109_Id = {$D109_Id}");
					if (mysql_num_rows($sql) > 0) {
						echo gGeraAlertMsg('Erro', 'Motorista já vinculado a este MDF-e');
					} else {
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$form->campoValorEnviado('divReload')}', true);
									divRefresh('{$g['divIdParent']}', true);
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
						}
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

	break;

	case 'fis012FormIncluirVeiculoModal':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($r_T276_Id)) ? $r_T276_Id : false;

		$T283_Flag_Carro_Proprio = isset($r_T283_Flag_Carro_Proprio) ? $r_T283_Flag_Carro_Proprio : "S";
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
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

		log("flagCarro:".$T283_Flag_Carro_Proprio);

		// Editor
		if($T283_Flag_Carro_Proprio == "N"){
			$form_editor = array(
				'geral' => '{"T283_Flag_Carro_Proprio":{"top":7,"left":47.28125,"width":80,"hidden":0},"T283_D170_Id":{"top":30,"left":78.453125,"width":232,"hidden":0},"btnIncluirVeiculo":{"top":28,"left":374,"width":null,"hidden":0},"T283_CPF_Proprietario":{"top":30,"left":540.546875,"width":136,"hidden":0},"T283_Placa":{"top":53,"left":87.03125,"width":120,"hidden":0},"T283_CNPJ_Proprietario":{"top":53,"left":533.8125,"width":136,"hidden":0},"T283_Renavam":{"top":76,"left":74.796875,"width":120,"hidden":0},"T283_RNTRC":{"top":76,"left":531.390625,"width":136,"hidden":0},"T283_Tara":{"top":99,"left":93.140625,"width":120,"hidden":0},"T283_Proprietario_Veiculo":{"top":99,"left":458,"width":344,"hidden":0},"T283_Capacidade_Kg":{"top":122,"left":35.046875,"width":72,"hidden":0},"T283_Tipo_Proprietario":{"top":122,"left":466.34375,"width":143,"hidden":0},"T283_Capacidade_M3":{"top":145,"left":43.015625,"width":72,"hidden":0},"T283_Inscricao_Estadual_Proprietario":{"top":145,"left":559.484375,"width":136,"hidden":0},"T283_Flag_Rodado":{"top":168,"left":63.578125,"width":192,"hidden":0},"T283_Flag_Carroceria":{"top":191,"left":48.9375,"width":192,"hidden":0},"T283_UF_Licenciamento":{"top":214,"left":33.234375,"width":79,"hidden":0},"T283_Categoria_Veiculo":{"top":237,"left":19.75,"width":192,"hidden":0}}',
			);				
		}else{
			$form_editor = array(
				'geral' => '{"T283_Flag_Carro_Proprio":{"top":7,"left":47.28125,"width":80,"hidden":0},"T283_D170_Id":{"top":30,"left":78.453125,"width":232,"hidden":0},"btnIncluirVeiculo":{"top":28,"left":374,"width":null,"hidden":0},"T283_CPF_Proprietario":{"top":30,"left":540.546875,"width":136,"hidden":1},"T283_Placa":{"top":53,"left":87.03125,"width":120,"hidden":0},"T283_CNPJ_Proprietario":{"top":53,"left":533.8125,"width":136,"hidden":1},"T283_Renavam":{"top":76,"left":74.796875,"width":120,"hidden":0},"T283_RNTRC":{"top":76,"left":531.390625,"width":136,"hidden":1},"T283_Tara":{"top":99,"left":93.140625,"width":120,"hidden":0},"T283_Proprietario_Veiculo":{"top":99,"left":458,"width":344,"hidden":1},"T283_Capacidade_Kg":{"top":122,"left":35.046875,"width":72,"hidden":0},"T283_Tipo_Proprietario":{"top":122,"left":466.34375,"width":143,"hidden":1},"T283_Capacidade_M3":{"top":145,"left":43.015625,"width":72,"hidden":0},"T283_Inscricao_Estadual_Proprietario":{"top":145,"left":559.484375,"width":136,"hidden":1},"T283_Flag_Rodado":{"top":168,"left":63.578125,"width":192,"hidden":0},"T283_Flag_Carroceria":{"top":191,"left":48.9375,"width":192,"hidden":0},"T283_UF_Licenciamento":{"top":214,"left":33.234375,"width":79,"hidden":0},"T283_Categoria_Veiculo":{"top":237,"left":19.75,"width":192,"hidden":0}}',
			);
		}
       
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T283';
		$form_campo_pk = 'T283_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormIncluirVeiculoModalBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormIncluirVeiculoModal/';		
		// novo formulário
		$form = new Formulario('fis012FormIncluirVeiculoModal');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T283_T276_Id' => array('name' => 'T283_T276_Id', 'value' => @$r_T276_Id, 'type' => 'hidden', 'label' => 'T283_T276_Id'),
					'T283_Versao_Modal' => array('name' => 'T283_Versao_Modal', 'value' => 3.00, 'type' => 'hidden', 'label' => 'T283_Versao_Modal'),
					'T283_Placa' => array('name' => 'T283_Placa', 'label' => 'Placa', 'length' => array(30,30)),
					'T283_Renavam' => array('name' => 'T283_Renavam',  'label' => 'Renavam', 'length' => array(30,30)),
					'T283_Tara' => array('name' => 'T283_Tara', 'label' => 'Tara', 'length' => array(30,30)),
					'T283_Capacidade_Kg' => array('name' => 'T283_Capacidade_Kg', 'label' => 'Capacidade(Kg)', 'length' => array(30,30)),
					'T283_Capacidade_M3' => array('name' => 'T283_Capacidade_M3', 'label' => 'Capacidade(m³)', 'length' => array(30,30)),
					'T283_Flag_Rodado' => array('name' => 'T283_Flag_Rodado', 'label' => 'Tipo rodado', 'type' => 'select', 'select' => 
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
					'T283_Flag_Carroceria' => array('name' => 'T283_Flag_Carroceria', 'label' => 'Tipo carroceria', 'type' => 'select', 'select' => 
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
					'T283_Categoria_Veiculo' => array('name' => 'T283_Categoria_Veiculo', 'label' => 'Categoria do Veículo', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Reboque', 'value' => '1'),
							array('title' => 'Tração', 'value' => '2'),
						)
					),
					'T283_UF_Licenciamento' => array('name' => 'T283_UF_Licenciamento', 'label' => 'UF Licenciamento', 'type' => 'select', 'select' =>
						gGeraSelect('D018', 'D018_UF', 'D018_UF', '', true)
					),
					'T283_CPF_Proprietario' => array('name' => 'T283_CPF_Proprietario','label'=>'CPF', 'type' => 'cpf','length' => array(10,15)),
					'T283_CNPJ_Proprietario' => array('name' => 'T283_CNPJ_Proprietario', 'type' => 'cnpj','label'=>'CNPJ','length' => array(18,18)),
					'T283_RNTRC' => array('name' => 'T283_RNTRC','label'=>'RNTRC','length' => array(10,15)),
					'T283_Proprietario_Veiculo' => array('name' => 'T283_Proprietario_Veiculo','label'=>'Nome do Proprietário','length' => array(10,15)),
					'T283_Inscricao_Estadual_Proprietario' => array('name' => 'T283_Inscricao_Estadual_Proprietario','label'=>'IE','length' => array(10,15)),
					'btnIncluirVeiculo' => array('name' => 'btnIncluirVeiculo', 'type' => 'button', 'reload' => false, 'value' => 'Novo Veículo', 'reject' => true),
					'T283_Tipo_Proprietario' => array('name' => 'T283_Tipo_Proprietario', 'label' => 'Tipo de Proprietário','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' =>true),
							array('title' => 'TAC AGREGADO', 'value' => '0'),
							array('title' => 'TAC INDEPENDENTE', 'value' => '1'),
							array('title' => 'OUTROS', 'value' => '2')
						)
					),
				)
			)
		);
		
		$selected0 = false;
		$selected1 = false;
		if($tipoDocumento=="S"){
			$selectedS = true;
		}else{
			$selectedK = true;
		}

		if($T283_Flag_Carro_Proprio == "S"){
			$selected0 = true;
			$titulo = 'hidden';
			$formArray['campos']['geral']['T283_D170_Id'] = array('name' => 'T283_D170_Id', 'label' => 'Veículo', 'type' => 'select', 'extra' => "onchange=\"verificaVeiculo(this, '{$g['divId']}');\"", 'select' => gGeraSelect('D170', 'D170_Id', 'D170_Identificacao_Veiculo', 'WHERE D170_Flag_Ativo = \'S\' AND D170_C004_Id = ' . $g['empresaAtual'] . ' ORDER BY D170_Identificacao_Veiculo', true));
			$formArray['campos']['geral']['T283_Identificacao_Veiculo'] = array('name' => 'T283_Identificacao_Veiculo', 'label' => 'Veículo', 'length' => array(10,15), 'type' => hidden);
		} else {
			$titulo = 'titulo';
			$selected1 = true;
			$formArray['campos']['geral']['T283_Identificacao_Veiculo'] = array('name' => 'T283_Identificacao_Veiculo', 'label' => 'Veículo', 'length' => array(10,15));
		}

		$formArray['campos']['geral']['tituloInfoProprietario'] = array('name' => 'tituloInfoProprietario', 'label'=>'Informações do proprietário do veículo','type' => $titulo, 'reject' => true);

		if ($g['C004']['C004_Flag_Emitente'] == 2) {
			$formArray['campos']['geral']['T283_Flag_Carro_Proprio'] = array('name' => 'T283_Flag_Carro_Proprio', 'extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', this.value, 'T283_Flag_Carro_Proprio'); divRefresh('{$g['divId']}');\"", 'label' => 'Veículo Próprio', 'type' => 'select', 'reject' => true, 'select' => array(
				array('title' => 'Sim', 'value' => 'S', 'selected' => $selected0))
	 		);
		} else {
			$formArray['campos']['geral']['T283_Flag_Carro_Proprio'] = array('name' => 'T283_Flag_Carro_Proprio', 'extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', this.value, 'T283_Flag_Carro_Proprio'); divRefresh('{$g['divId']}');\"", 'label' => 'Veículo Próprio', 'type' => 'select', 'reject' => true, 'select' => array(
				array('title' => 'Sim', 'value' => 'S', 'selected' => $selected0),
				array('title' => 'Não', 'value' => 'N', 'selected' => $selected1))
	 		);
		}
	
		$form->definirFormularioArray($formArray);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#btnIncluirVeiculo').unbind('click');
				$('#btnIncluirVeiculo').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Veículo', '/cad/cad064/content/cad064ContentFormCadastrarVeiculos/', '&tab=geral',  [550,550]);
					return false;
				});
		    } </script></div>
EOT;
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
				if (!empty($r_acaoId)) {
					// Get data from database and formulary to check if the vehicle category was changed
					$T283 = mysql_query("SELECT T283_Categoria_Veiculo FROM T283 WHERE T283_Id = '{$r_acaoId}'");
					$mT283 = mysql_fetch_array($T283);
					$categoriaVeiculoBD = $mT283['T283_Categoria_Veiculo'];
					$categoriaVeiculoForm = $form->campoValorEnviado('T283_Categoria_Veiculo');

					if ($categoriaVeiculoBD != $categoriaVeiculoForm) {
						$T276_Id = $form->campoValorEnviado('T283_T276_Id');
						$D170_Id = $form->campoValorEnviado('T283_D170_Id');

						$sqlVeiculoTracao = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = '{$T276_Id}' AND T283_Categoria_Veiculo = 2");
						$sqlVeiculoReboque = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = '{$T276_Id}' AND T283_Categoria_Veiculo = 1");
					
						// Validating data
						if ($form->campoValorEnviado('T283_Categoria_Veiculo') == 2 AND mysql_num_rows($sqlVeiculoTracao) > 0) {
							echo gGeraAlertMsg('Erro', 'Só é possível cadastrar um veículo do tipo tração por MDf-e.');
						} elseif ($form->campoValorEnviado('T283_Categoria_Veiculo') == 1 AND mysql_num_rows($sqlVeiculoReboque) > 2) {
							echo gGeraAlertMsg('Erro', 'Só é possível cadastrar até três veículos do tipo reboque por MDf-e.');
						} else {
							// Update with verification (the vehicle category was changed)
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
								// Quando tirava o reject ele se perdia e sempre fixava o T283_Flag_Carro_Proprio no S, correcao paliativa
								$T283_Flag_Carro_Proprio = $form->campoValorEnviado('T283_Flag_Carro_Proprio');
								mysqli_query("UPDATE T283 SET T283_Flag_Carro_Proprio = '{$T283_Flag_Carro_Proprio}' WHERE T283_Id = '{$r_acaoId}'");
								$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com cucesso!', false);
								// Imprime uma mensagem e fecha a janela
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
					} else {
						// Update without verification (the vehicle category was not changed)
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
							// Quando tirava o reject ele se perdia e sempre fixava o T283_Flag_Carro_Proprio no S, correcao paliativa
							$T283_Flag_Carro_Proprio = $form->campoValorEnviado('T283_Flag_Carro_Proprio');
							mysqli_query("UPDATE T283 SET T283_Flag_Carro_Proprio = '{$T283_Flag_Carro_Proprio}' WHERE T283_Id = '{$r_acaoId}'");

							$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com cucesso!', false);
							// Imprime uma mensagem e fecha a janela
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
				} else {
					$T276_Id = $form->campoValorEnviado('T283_T276_Id');
					$D170_Id = $form->campoValorEnviado('T283_D170_Id');

					$sql = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = '{$T276_Id}' AND T283_D170_Id = '{$D170_Id}'");
					$sqlVeiculoTracao = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = '{$T276_Id}' AND T283_Categoria_Veiculo = 2");
					$sqlVeiculoReboque = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = '{$T276_Id}' AND T283_Categoria_Veiculo = 1");
					
					// Validando registros
					if (mysql_num_rows($sql) > 0) {
						echo gGeraAlertMsg('Erro', 'Veículo já cadastrado.');
					} elseif ($form->campoValorEnviado('T283_Categoria_Veiculo') == 2 AND mysql_num_rows($sqlVeiculoTracao) > 0) {
						echo gGeraAlertMsg('Erro', 'Só é possível cadastrar um veículo do tipo tração por MDf-e.');
					} elseif ($form->campoValorEnviado('T283_Categoria_Veiculo') == 1 AND mysql_num_rows($sqlVeiculoReboque) > 2) {
						echo gGeraAlertMsg('Erro', 'Só é possível cadastrar até três veículos do tipo reboque por MDf-e.');
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							log("SQL insert: ".$sql);
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Registro incluído com sucesso!', false);
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
				log("entra QUI PARA CARREGAR OS VALORES");
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012FormCadastrarTipoTransporte':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T284_Id = (isset($_REQUEST['T284_Id'])) ? $_REQUEST['T284_Id'] : false;
		$T276_Id = (isset($r_T276_Id)) ? $r_T276_Id : false;
		log("T276_Id: ".$T276_Id);

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
		$form_tabela = 'T281';
		$form_campo_pk = 'T281_Id';
		// valor passado para o arquivo de configuração T281.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarTipoTransporteBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarTipoTransporte/';
		$form_editor = array(
			'geral' => '{"T281_Flag_Unidade_Transporte":{"top":7,"left":6.421875,"width":240,"hidden":0},"fis012FormCadastrarTipoTransporteBtnAdd":{"top":5,"left":352,"width":null,"hidden":0},"T281_Quantidade_Rateada":{"top":30,"left":4.359375,"width":232,"hidden":0},"T281_Identificacao_Unidade_Transporte":{"top":53,"left":44.125,"width":232,"hidden":0}}',
		);
		
		// Array de definição do formulário
		$form = new Formulario('fis012FormCadastrarTipoTransporte');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T281_Flag_Unidade_Transporte' => array('name' => 'T281_Flag_Unidade_Transporte', 'label' => 'Tipo de Transporte', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - RODOVIÁRIO TRAÇÃO', 'value' => "1"),
							array('title' => '2 - RODOVIÁRIO REBOQUE', 'value' => "2"),
							array('title' => '3 - NAVIO', 'value' => "3"),
							array('title' => '4 - BALSA', 'value' => "4"),
							array('title' => '5 - AERONAVE', 'value' => "5"),
							array('title' => '6 - VAGÃO', 'value' => "6"),
							array('title' => '7 - OUTROS', 'value' => "7")
						)
					),
					'T281_Quantidade_Rateada' => array('name' => 'T281_Quantidade_Rateada', 'type' => 'numero', 'label' => 'Quantidade Rateada'),
					'T281_Identificacao_Unidade_Transporte' => array('name' => 'T281_Identificacao_Unidade_Transporte', 'type' => 'text', 'label' => 'Identificação'),
					'T281_T284_Id' => array('name' => 'T281_T284_Id', 'label' => '', 'type' => 'hidden', 'value' => $T284_Id),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		// Botões do form
		$formArray['campos']['geral']['fis012FormCadastrarTipoTransporteBtnAdd'] = array('name' => 'fis012FormCadastrarTipoTransporteBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', 0);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
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
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012FormCadastrarTipoTransporteCargas':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T281_Id = (isset($_REQUEST['T281_Id'])) ? $_REQUEST['T281_Id'] : false;
		$T276_Id = (isset($r_T276_Id)) ? $r_T276_Id : false;

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
		$form_tabela = 'T282';
		$form_campo_pk = 'T282_Id';
		// valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarTipoTransporteCargasBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarTipoTransporteCargas/';
		$form_editor = array(
			'geral' => '{"T282_Flag_Unidade_Carga":{"top":7,"left":29.25,"width":240,"hidden":0},"fis012FormCadastrarTipoTransporteCargasBtnAdd":{"top":5,"left":368,"width":null,"hidden":0},"T282_Quantidade_Rateada":{"top":30,"left":4.359375,"width":232,"hidden":0},"T282_Identificacao_Unidade_Carga":{"top":53,"left":36.78125,"width":232,"hidden":0}}',
		);
		
		// Array de definição do formulário
		$form = new Formulario('fis012FormCadastrarTipoTransporteCargas');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T282_Flag_Unidade_Carga' => array('name' => 'T282_Flag_Unidade_Carga', 'label'=>'Tipo de Carga', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - CONTAINER', 'value' => 1),
							array('title' => '2 - ULD', 'value' => 2),
							array('title' => '3 - PALLET', 'value' => 3),
							array('title' => '4 - OUTROS', 'value' => 4)
						)
					),
					'T282_Quantidade_Rateada' => array('name' => 'T282_Quantidade_Rateada', 'type' => 'numero', 'label' => 'Quantidade Rateada'),
					'T282_Identificacao_Unidade_Carga' => array('name' => 'T282_Identificacao_Unidade_Carga', 'type' => 'text', 'label' => 'Identificação'),
					'T282_T281_Id' => array('name' => 'T282_T281_Id', 'label' => '', 'type' => 'hidden', 'value' => $T281_Id),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				),
			)
		);

		// Botões do form
		$formArray['campos']['geral']['fis012FormCadastrarTipoTransporteCargasBtnAdd'] = array('name' => 'fis012FormCadastrarTipoTransporteCargasBtnAdd', 'type' => 'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', 0);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divIdParent']}', true);
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
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCadastroNfeMdfe':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		log("T276_Id no fis012FormCadastroNfeMdfe: ".$T276_Id);

		//verifica se essa nota existe no sistema, para deixar alguns campos não editáveis
		$T284 = mysql_query("SELECT T007_Numero_Nota_Fiscal, T284_Numero_Nfe FROM T007 LEFT JOIN T284 ON T284_T007_Id=T007_Id WHERE T284_Id='{$acaoId}'");
		$mT284 = mysql_fetch_array($T284);
		if(!empty($mT284['T007_Numero_Nota_Fiscal'])){
			$readonly = 'readonly';
		}else{
			$readonly = '';
		}

		/**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T284';
		$form_campo_pk = 'T284_Id';
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormCadastroNfeMdfeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastroNfeMdfe/';

		// Editor
		$form_editor = array(
			'geral' => '{"T284_T007_Id":{"top":7,"left":72,"width":282,"hidden":0},"T284_Destinatario_Nome":{"top":30,"left":44,"width":296,"hidden":0},"T284_Valor_Carga":{"top":53,"left":76,"width":146,"hidden":0},"T284_Peso_Bruto_Total":{"top":76,"left":81,"width":146,"hidden":0},"T284_D018_Id":{"top":99,"left":25,"width":50,"hidden":0},"T284_D020_Id":{"top":122,"left":4,"width":191,"hidden":0},"T284_Chave_De_Acesso_NFe":{"top":145,"left":19,"width":296,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T284_Flag_Tipo_Documento' => array('name' => 'T284_Flag_Tipo_Documento', 'label' => 'Tipo de Documento', 'type' => 'hidden', 'value' => '2', 'align'=>'right', 'style'=>'float:right'),
					'T284_T007_Id' => array('name' => 'T284_T007_Id', 'label' => 'Número Nota', 'type' => 'pesquisa', 'extra' => "onchange=\"carregaDadosNfeMdfe(this, '{$g['divId']}');\"", 'pesquisa' => array('T007', 'T007_Id','T007_Numero_Nota_Fiscal', "WHERE T007_C004_Id = '{$g['empresaAtual']}' AND T007_Flag_ACP='6' ORDER BY T007_Numero_Nota_Fiscal")), 
					'T284_Destinatario_Nome' => array('name' => 'T284_Destinatario_Nome', 'label'=>'Nome Destinatário', 'type' => 'text','readonly' => $readonly),
					'T284_Valor_Carga' => array('name' => 'T284_Valor_Carga', 'label' => 'Valor Total', 'type' => 'moeda', 'readonly' => $readonly),
					'T284_Peso_Bruto_Total' => array('name' => 'T284_Peso_Bruto_Total', 'label'=>'Peso (Kg)', 'type' => 'moeda'),
					'T284_D018_Id' => array('name' => 'T284_D018_Id', 'label' => 'UF Descarregamento','type' => 'select', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)),
					'T284_D020_Id' => array('name' => 'T284_D020_Id', 'label' => 'Cidade Descarregamento', 'type' => 'select', 'select' => gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', '', true)),
					'T284_Chave_De_Acesso_NFe' => array('name' => 'T284_Chave_De_Acesso_NFe', 'label' => 'Chave de Acesso NF-e','type' => 'text','readonly' => $readonly),
					//Filling in extra fields
					'T284_T276_Id' => array('name' => 'T284_T276_Id', 'label' => 'T284_T276_Id', 'type' => 'hidden', 'value' => $T276_Id),
					'T284_Numero_NFe' => array('name' => 'T284_Numero_NFe', 'label' => 'T284_Numero_NFe', 'type' => 'hidden'),
					'T284_Cidade_Descarregamento' => array('name' => 'T284_Cidade_Descarregamento', 'label' => 'T284_Cidade_Descarregamento', 'type' => 'hidden'),
					'T284_Cidade_Descarregamento_Codigo_IBGE' => array('name' => 'T284_Cidade_Descarregamento_Codigo_IBGE', 'label' => 'T284_Cidade_Descarregamento_Codigo_IBGE', 'type' => 'hidden'),
				)
			)
		);

		$form = new Formulario('fis012FormCadastroNfeMdfe');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			//Set the "T284_Cidade_Descarregamento" value
			$sql = "SELECT D020_Nome_Cidade, D020_Codigo_IBGE FROM D020 WHERE D020_Id = " . $form->campoValorEnviado('T284_D020_Id');
			$result = mysql_query($sql);
			$arrayDataD020 = mysql_fetch_assoc($result);
			echo mysql_error();
			$form->campoSetarValor('T284_Cidade_Descarregamento', $arrayDataD020['D020_Nome_Cidade'], true);
			$form->campoSetarValor('T284_Cidade_Descarregamento_Codigo_IBGE', $arrayDataD020['D020_Codigo_IBGE'], true);

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
					//Check if the NF-e already exist in this MDf-e
					$T007_Id = $form->campoValorEnviado('T284_T007_Id');
					$T276_Id = $form->campoValorEnviado('T284_T276_Id');
					$sql = mysql_query("SELECT * FROM T284 WHERE T284_T276_Id = {$T276_Id} AND T284_T007_Id = {$T007_Id}");
					if(mysql_num_rows($sql) > 0){
						echo gGeraAlertMsg('Erro', 'Nota Fiscal já cadastrada.');
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
							echo gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
						}
					}					
				}
				mysql_query("CALL T276_Gravar_Totalizacao('{$T276_Id}')");
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="fis012FormCadastroNfeMdfeBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012FormPrincipal':
		global $g;	
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
		$formMultiTab = true;
		if ($formMultiTab) {
			//$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
			$formId = (isset($r_fis012FormPrincipal)) ? $r_fis012FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
        
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T276';
		$form_campo_pk = 'T276_Id';

		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormPrincipal_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormPrincipal/';		
		
		//form editor
		$form_editor = array(
			'cabecalho'    => '{"T276_CEP_Carregamento":{"top":145,"left":44,"width":200,"hidden":0},"T276_Emitente_Nome_Fantasia":{"top":7,"left":38,"width":360,"hidden":0},"T276_Data_Inicio_Viagem":{"top":7,"left":532,"width":72,"hidden":0},"T276_Hora_Inicio_Viagem":{"top":7,"left":732.53125,"width":56,"hidden":0},"T276_CEP_Descarregamento":{"top":214,"left":26,"width":200,"hidden":0},"T276_Flag_Emitente":{"top":30,"left":53,"width":368,"hidden":0},"T276_Data_Emissao":{"top":30,"left":557,"width":72,"hidden":0},"T276_Hora_Emissao":{"top":30,"left":756.796875,"width":56,"hidden":0},"T276_Flag_Transportador":{"top":53,"left":42,"width":368,"hidden":0},"T276_Flag_Modalidade_Transporte":{"top":76,"left":99,"width":368,"hidden":0},"T276_D018_Id_Carregamento":{"top":99,"left":43,"width":50,"hidden":0},"T276_D020_Id_Carregamento":{"top":122,"left":22,"width":207,"hidden":0},"T276_D018_Id_Descarregamento":{"top":168,"left":33,"width":50,"hidden":0},"T276_D020_Id_Descarregamento":{"top":191,"left":12,"width":207,"hidden":0},"T276_UF_Percurso":{"top":237,"left":77,"width":200,"hidden":0}}',
			'adicionais'   => '{"T276_Informacoes_Adicionais_Fisco":{"top":30,"left":56.375,"height":70,"width":437,"hidden":0},"T276_Informacoes_Adicionais_Contribuinte":{"top":122,"left":7.5,"height":70,"width":437,"hidden":0},"T276_Observacao_Cancelamento":{"top":214,"left":53.296875,"height":70,"width":437,"hidden":0},"avisoObservacoesMDFe":{"top":329,"left":28,"width":null,"hidden":1}}',
			'produtoPredominante'   => '{"T276_Tipo_Carga":{"top":7,"left":52,"width":335,"hidden":0},"T276_Descricao_Produto_Predominante":{"top":30,"left":14,"width":328,"hidden":0},"T276_EAN_Produto_Predominante":{"top":53,"left":85,"width":200,"hidden":0},"T276_NCM_Produto_Predominante":{"top":76,"left":81,"width":200,"hidden":0}}',
		);
		// novo formulário
		$form = new Formulario('fis012FormPrincipal');

		$C004 = mysql_query("SELECT C004_Id, 
									C004_D020_Id,
									C004_D018_Id,
									C004_Nome_Empresa
									FROM C004
									LEFT JOIN D018 ON D018_Id=C004_D018_Id
									LEFT JOIN D020 ON D020_Id=C004_D020_Id
									LEFT JOIN D170 ON D170_Id=C004_D170_Id
									WHERE C004_Id='{$g['empresaAtual']}'");
		$mC004 = mysql_fetch_array($C004);

		// habilita sobre a cidade e UF de descarregamento caso o protocolo de encerramento estiver preenchido
		$readonly = false;
		$T276Descarregamento = mysql_query("SELECT T276_Numero_Protocolo_Encerramento_MDFe,
												   T276_Numero_Protocolo_MDFe, 
												   T276_D018_Id_Descarregamento,
												   T276_D018_Id_Carregamento

			 			  	   FROM T276 
			 			  	  WHERE T276_Id='{$acaoId}'
			 			  ");
		$mT276Descarregamento = mysql_fetch_assoc($T276Descarregamento);
		if(empty($mT276Descarregamento['T276_Numero_Protocolo_Encerramento_MDFe']) && !(empty($mT276Descarregamento['T276_Numero_Protocolo_MDFe']))){
			$readonly = true;
		}

		// Where Cidades
		$whereCidadeCarregamento = " WHERE D020_D018_Id = '' ";
		$whereCidadeDescarregamento = " WHERE D020_D018_Id = '' ";
		if(!empty($acaoId)){
			if($mT276Descarregamento['T276_D018_Id_Descarregamento'] > 0){
				$whereCidadeDescarregamento = " WHERE D020_D018_Id = '{$mT276Descarregamento['T276_D018_Id_Descarregamento']}' AND D020_Codigo_IBGE > 0 ORDER BY D020_Nome_Cidade ";
			}
			if($mT276Descarregamento['T276_D018_Id_Carregamento'] > 0){
				$whereCidadeCarregamento = " WHERE D020_D018_Id = '{$mT276Descarregamento['T276_D018_Id_Carregamento']}' AND D020_Codigo_IBGE > 0 ORDER BY D020_Nome_Cidade ";
			}
		}else{
			if($mC004['C004_D020_Id'] > 0){
				$whereCidadeCarregamento = " WHERE D020_D018_Id = '{$mC004['C004_D018_Id']}' AND D020_Codigo_IBGE > 0 ";
			}
		}
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'cabecalho' => array(
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'T276_Emitente_Nome_Fantasia' => array('name' => 'T276_Emitente_Nome_Fantasia', 'label' => 'Empresa Emitente', 'value'=>$mC004['C004_Nome_Empresa'] ,'type' => 'text', 'readonly' => true),
					'T276_Flag_Emitente' => array('name' => 'T276_Flag_Emitente', 'label' => 'Tipo de Emitente', 'disabled' => $readonly, 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' => 
						array(
							array('title' => '1 - Prestador de Serviço', 'value' => '1'),
							array('title' => '2 - Carga Própria', 'value' => '2', 'selected' => true)
						)
					),
					'T276_Flag_Transportador' => array('name' => 'T276_Flag_Transportador', 'disabled' => $readonly,  'label' => 'Tipo Transportador', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' => 
						array(
							array('title' => '...', 'selected' => true),
							array('title' => '1 - Empresas de Transporte Rodoviário de Carga (ETC)', 'value' => '1'),
							array('title' => '2 - Transportador Autônomo de Cargas (TAC)', 'value' => '2'),
							array('title' => '3 - Cooperativa de Transporte Rodoviário de Cargas (CTC)', 'value' => '3')
						)
					),
					'T276_Flag_Modalidade_Transporte' => array('name' => 'T276_Flag_Modalidade_Transporte', 'disabled' => $readonly, 'label' => 'Modal', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' => 
						array(
							array('title' => '1 - Rodoviário', 'value' => '1', 'selected' => true),
							//array('title' => '2 - Aéreo', 'value' => '2'),
							//array('title' => '3 - Aquaviário', 'value' => '3'),
							//array('title' => '3 - Ferroviário', 'value' => '4')
						)
					),
					//UF (Carregamento)
					'T276_D018_Id_Carregamento' => array('name' => 'T276_D018_Id_Carregamento', 'disabled' => $readonly, 'label' => 'UF Carregamento', 'type' => 'select', 'extra' => "onchange = \"alteraEstadoCabecalho(this, '{$g['divId']}')\"", 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF', 'WHERE D018_Codigo_IBGE > 0 ORDER BY D018_UF', true, $mC004['C004_D018_Id'])
					),
					//Cidade (Carregamento)
					'T276_D020_Id_Carregamento' => array('name' => 'T276_D020_Id_Carregamento', 'disabled' => $readonly, 'label' => 'Cidade Carregamento', 'type' => 'select', 'select' =>
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $whereCidadeCarregamento, true,$mC004['C004_D020_Id'])
					),
					//UF (Descarregamento)
					'T276_D018_Id_Descarregamento' => array('name' => 'T276_D018_Id_Descarregamento', 'label' => 'UF Descarregamento', 'type' => 'select', 'extra' => "onchange = \"estadoDescarregamento(this, '{$g['divId']}')\"", 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF', 'WHERE D018_Codigo_IBGE > 0 ORDER BY D018_UF', true)
					),
					'T276_D020_Id_Descarregamento' => array('name' => 'T276_D020_Id_Descarregamento', 'label' => 'Cidade Descarregamento', 'type' => 'select', 'select' =>
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $whereCidadeDescarregamento, true)
					),
					'T276_Data_Inicio_Viagem' => array('name' => 'T276_Data_Inicio_Viagem', 'readonly' => $readonly, 'label' => 'Data Início Viagem', 'type' => 'data', 'length' => array(12,10)),
					'T276_Hora_Inicio_Viagem' => array('name' => 'T276_Hora_Inicio_Viagem', 'readonly' => $readonly, 'label' => 'Hora Início Viagem', 'type' => 'tempo'),
					'T276_Data_Emissao' => array('name' => 'T276_Data_Emissao', 'readonly' => $readonly, 'label' => 'Data Emissão', 'type' => 'data','value'=>date("d/m/Y"), 'length' => array(12,10)),
					'T276_Hora_Emissao' => array('name' => 'T276_Hora_Emissao', 'readonly' => $readonly, 'label' => 'Hora Emissão', 'type' => 'tempo','value'=>date("H:i")),
					'T276_UF_Percurso' => array('name' => 'T276_UF_Percurso', 'readonly' => $readonly, 'label' => 'UF Percurso', 'type' => 'text'),
					'T276_CEP_Carregamento' => array('name' => 'T276_CEP_Carregamento','label'=>'CEP Carregamento', 'readonly' => $readonly,  'type' => 'cep', 'length' => array(5,70)),
					'T276_CEP_Descarregamento' => array('name' => 'T276_CEP_Descarregamento','label'=>'CEP Descarregamento', 'readonly' => $readonly,  'type' => 'cep', 'length' => array(5,70)),
				),
				'adicionais' => array(
					'T276_Informacoes_Adicionais_Fisco' => array('name' => 'T276_Informacoes_Adicionais_Fisco','label'=>'Informações Interesse FISCO', 'readonly' => $readonly,  'type' => 'textarea', 'length' => array(5,70)),					
					'T276_Informacoes_Adicionais_Contribuinte' => array('name' => 'T276_Informacoes_Adicionais_Contribuinte','label'=>'Informações Interesse CONTRIBUINTE', 'readonly' => $readonly, 'type' => 'textarea', 'length' => array(5,70)),
					'T276_Observacao_Cancelamento' => array('name' => 'T276_Observacao_Cancelamento', 'label' => 'Observação do Cancelamento', 'type' => 'textarea', 'length' => array(8, 70)),
					'avisoObservacoesMDFe' => array('name' => 'avisoObservacoesMDFe', 'label' => 'As observações do FISCO e CONTRIBUINTE constarão no campo de "Observações" no xml do MDF-e', 'type' => 'titulo', 'reject' => true),
				),
				'produtoPredominante' => array(
					'T276_Tipo_Carga' => array('name' => 'T276_Tipo_Carga', 'disabled' => $readonly, 'label' => 'Tipo Carga', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Granel sólido', 'value' => '01'),
							array('title' => 'Granel líquido', 'value' => '02'),
							array('title' => 'Frigorificada', 'value' => '03'),
							array('title' => 'Conteinerizada', 'value' => '04'),
							array('title' => 'Carga Geral', 'value' => '05'),
							array('title' => 'Neogranel', 'value' => '06'),
							array('title' => 'Perigosa (granel sólido)', 'value' => '07'),
							array('title' => 'Perigosa (granel líquido)', 'value' => '08'),
							array('title' => 'Perigosa (carga frigorificada)', 'value' => '09'),
							array('title' => 'Perigosa (conteinerizada)', 'value' => '10'),
							array('title' => 'Perigosa (carga geral).', 'value' => '11'),

						)
					),
					'T276_Descricao_Produto_Predominante' => array('name' => 'T276_Descricao_Produto_Predominante','label'=>'Descrição Produto', 'readonly' => $readonly,  'type' => 'text', 'length' => array(5,70)),
					'T276_EAN_Produto_Predominante' => array('name' => 'T276_EAN_Produto_Predominante','label'=>'EAN', 'readonly' => $readonly,  'type' => 'text', 'length' => array(5,70)),
					'T276_NCM_Produto_Predominante' => array('name' => 'T276_NCM_Produto_Predominante','label'=>'NCM', 'readonly' => $readonly,  'type' => 'text', 'length' => array(5,70)),
					
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
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Setando a UF Carregamento
				/*$T276_D018_Id_Carregamento = $form->campoValorEnviado('T276_D018_Id_Carregamento');
				$sql = "SELECT D018_UF FROM D018 WHERE D018_Id = " . $T276_D018_Id_Carregamento;
				$result = mysql_query($sql);
				$arrayDataD018 = mysql_fetch_assoc($result);
				$form->campoSetarValor('T276_UF_Carregamento', $arrayDataD018['D018_UF'], true);

				// Setando a cidade Carregamento
				$T276_D020_Id_Carregamento = $form->campoValorEnviado('T276_D020_Id_Carregamento');
				$sql = "SELECT D020_Nome_Cidade, D020_Codigo_IBGE FROM D020 WHERE D020_Id = " . $T276_D020_Id_Carregamento;
				$result = mysql_query($sql);
				$arrayDataD020 = mysql_fetch_assoc($result);
				$form->campoSetarValor('T276_Cidade_Carregamento', $arrayDataD020['D020_Nome_Cidade'], true);
				$form->campoSetarValor('T276_Cidade_Carregamento_IBGE', $arrayDataD020['D020_Codigo_IBGE'], true);

				// Setando a UF Descarregamento
				$T276_D018_Id_Descarregamento = $form->campoValorEnviado('T276_D018_Id_Descarregamento');
				$sql = "SELECT D018_UF FROM D018 WHERE D018_Id = " . $T276_D018_Id_Descarregamento;
				$result = mysql_query($sql);
				$arrayDataD018 = mysql_fetch_assoc($result);
				$form->campoSetarValor('T276_UF_Descarregamento', $arrayDataD018['D018_UF'], true);

				// Setando a cidade Descarregamento
				$T276_D020_Id_Descarregamento = $form->campoValorEnviado('T276_D020_Id_Descarregamento');
				$sql = "SELECT D020_Nome_Cidade, D020_Codigo_IBGE FROM D020 WHERE D020_Id = " . $T276_D020_Id_Descarregamento;
				$result = mysql_fetch_assoc(mysql_query($sql));
				$form->campoSetarValor('T276_Cidade_Descarregamento', $result['D020_Nome_Cidade'], true);
				$form->campoSetarValor('T276_Cidade_Descarregamento_IBGE', $result['D020_Codigo_IBGE'], true);
				log("Descarregamento = ".$result['D020_Codigo_IBGE']. " Carregamento = ".$arrayDataD020['D020_Codigo_IBGE']);*/

				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                // O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
                                divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
                            } </script></div>
EOT;
					}
				} else {
				 	$retorno = $FIS012->incluirMDFe($form,$acaoId);
			        $acaoId = $retorno['T276_Id'];
			        log("ACAO ID: ".$acaoId);
			        if (!is_array($retorno)) {
				        echo gGeraAlertMsg('Operação ERRO', $retorno);
			        } else {
				        echo gGeraAlertMsg('Operação OK', 'Registro Incuido com Sucesso!');
			        }
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Alterar MDF-e - ID: ' + '{$acaoId}', '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}'), '[800,700]');
							}, 150);
							});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
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
		echo  $g['smarty']->fetch('formEditor.tpl');
	break;

// 	case 'fis012FormPercurso':
// 		/**
// 		* PARTE 1: Variavéis envidas para o formulário
// 		*/
// 		// Variável indica qual tab do form será exibida
// 		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
// 		// Utilizada em UPDATE, é o ID do registro que está sendo editado
// 		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
// 		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

// 		$D018_Id = "";
// 		if($acaoId > 0){
// 			$row = mysql_fetch_array(mysql_query("SELECT T277_D018_Id FROM T277 WHERE T277_Id = '{$acaoId}'"));
// 			$D018_Id = trim($row['T277_D018_Id']);
// 		}
// 		/**
// 		* PARTE 2: Definição do formulário
// 		*/
// 		// Este mesmo form será exibido em multiplas tabs ou não?
// 		$formMultiTab = false;
// 		if ($formMultiTab) {
// 			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
// 		} else {
// 			$formId = uniqid();
// 		}
				
// 		// A qual tabela este form está relacionada, e a chave primária da tabela
// 		$form_tabela = 'T277';
// 		$form_campo_pk = 'T277_Id';
// 		$form_parent = 'T276_Id';
//         $form_parent_valor = $T276_Id;
// 		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
// 		$enviar_btnId = 'formPercurso_btn';
// 		// Form que será executado ao clicar no botão
// 		$enviar_acaoHref = '/fis/fis012/form/fis012FormPercurso/';

// 		//form editor
// 		$form_editor = array('geral' => '{"T277_D018_Id":{"top":7,"left":11.21875,"width":79,"hidden":0},"formPercurso_btn":{"top":5,"left":128,"width":null,"hidden":0}}' , );
				
// 		// novo formulário
// 		$form = new Formulario('fis012FormPercurso');

// 		$form->campoSetarValor('T277_D018_Id', $D018_Id, true);

// 		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
// 		$formArray = array(
// 			'campos' => array(
// 				'geral' => array(
// 					'T277_T276_Id' => array('name' => 'T277_T276_Id', 'type' => 'hidden', 'value' => $T276_Id,'align' => 'right'),
// 					'T277_D018_Id' => array('name' => 'T277_D018_Id', 'label' => 'UF', 'type' => 'select', 'align' => 'left', 'style' => 'float:left', 'select' => 
// 						gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true, $D018_Id)
// 					),
// 					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
// 				)
// 			)
// 		);
// 		// Botões do form
// 		$formArray['campos']['geral']['formPercurso_btn'] = array('name' => 'formPercurso_btn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
// 		$form->definirFormularioArray($formArray);
		
// 		/**
// 		* PARTE 3: Processamento do formulário (se foi enviado)
// 		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
// 		*/
// 		if ($formCodigo = $form->processarFormulario($form_tabela)) {
// 			// Form com erros
// 			if ($formCodigo == 1) {
// 				echo gGeraAlertMsgErroForm($form);
// 			// Form sem erros
// 			} else if ($formCodigo == 2) {
// 				// Insert ou Update?
// 				if (!empty($acaoId)) {
// 					// Update
// 					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
// 						// Imprime uma mensagem e fecha a janela
// 						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
// 						echo  <<<EOT
// 						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 							divRefresh('{$form->campoValorEnviado('divReload')}', true);
// 							divSetarAcaoId('{$g['divId']}', 0);
// 							divRefresh('{$g['divId']}', true);
// 						} </script></div>
// EOT;
// 					}
// 				} else {
// 					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
// 						// Imprime uma mensagem e fecha a janela
// 						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
// 						echo  <<<EOT
// 							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
// 								divRefresh('{$form->campoValorEnviado('divReload')}', true);
// 								divRefresh('{$g['divId']}', true);
// 							} </script></div>
// EOT;
						
// 					}
// 				}
// 			}
// 		}	
		
// 		/**
// 		* PARTE 4: Gerar o formulário
// 		*/
// 		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);
		
//		/**
//		* PARTE 5: Exibir o formulário
//		*/
//		$g['smarty']->assign('formularioID', $form->formularioID());		
//		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
//		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
//		echo $gerado['pagina'];				
//		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
//		$g['smarty']->display('formEditor.tpl');
//	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012FormTecnico':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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

		$form_tabela = 'T276';
		$form_campo_pk = 'T276_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)

		$enviar_btnId = array('fis012FormBtnEnviar', 'fis012FormBtnLimpar');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormTecnico/';

		// habilita sobre a cidade e UF de descarregamento caso o protocolo de encerramento estiver preenchido
		$readonly = false;
		$T276Descarregamento = mysql_query("SELECT T276_Numero_Protocolo_MDFe 
			 			  	   FROM T276 
			 			  	  WHERE T276_Id='{$acaoId}'
			 			  ");
		$mT276Descarregamento = mysql_fetch_assoc($T276Descarregamento);

		
		if($g['usuarioAtual'] == '63' && (empty($mT276Descarregamento['T276_Numero_Protocolo_MDFe']))){
			$campoDisabled = false;
			$comboDisabled = "";
		} else {
			$campoDisabled = true;
			$comboDisabled = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		}
		if(!empty($mT276Descarregamento['T276_Numero_Protocolo_MDFe'])){
			$campoDisabledEspecifico = true;
			$comboDisabledEspecifico = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		}


		//form editor
		$form_editor = array('geral' => '{"T276_Serie":{"top":76,"left":72.859375,"width":328,"hidden":0},"T276_Chave_Acesso_MDFe":{"top":7,"left":34.953125,"width":326,"hidden":0},"T276_Numero_Recibo_MDFe":{"top":30,"left":29.453125,"width":326,"hidden":0},"T276_Numero_MDFe":{"top":53,"left":30.09375,"width":328,"hidden":0},"T276_TX_Resposta_Codigo":{"top":99,"left":11.53125,"width":328,"hidden":0},"T276_Versao_MDFe":{"top":122,"left":64.296875,"width":64,"hidden":0},"Validador":{"top":120,"left":208,"width":null,"hidden":0},"fis012FormBtnLimpar":{"top":120,"left":320,"width":null,"hidden":0},"fis012FormBtnEnviar":{"top":120,"left":384,"width":null,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T276_Chave_Acesso_MDFe' 	=> array('name' => 'T276_Chave_Acesso_MDFe', 'label' => 'Chave Acesso', 'type' => 'text', 'length' => array(50, 80), 'readonly' => $comboDisabledEspecifico),
					'T276_Numero_Recibo_MDFe' 	=> array('name' => 'T276_Numero_Recibo_MDFe', 'label' => 'Número Recibo', 'type' => 'text', 'length' => array(50,80), 'readonly' => $comboDisabledEspecifico),
					'T276_Numero_MDFe'			=> array('name' => 'T276_Numero_MDFe', 'label' => 'Número MDF-e', 'type' => 'text', 'readonly' => $campoDisabled),
     				'T276_Serie' 				=> array('name' => 'T007_Serie', 'label' => 'Serie', 'type' => 'text', 'readonly' => $campoDisabled),
					'T276_TX_Resposta_Codigo' 	=> array('name' => 'T276_TX_Resposta_Codigo', 'label' => 'Flag Transmissão', 'type' => 'text', 'readonly' => $campoDisabled),
					'T276_Versao_MDFe' 			=> array('name' => 'T276_Versao_MDFe','label' => 'Versão', 'readonly' => $campoDisabled, 'type' => 'select','select' => array(
							array('title' => '3.0', 'value' => '3.0'),
						)
					),
					'Validador' => array('name' => 'Validador', 'type' => 'button', 'reload' => false, 'value' => 'Validação XML', 'reject' => true)
				)
			)
		);
        $form = new Formulario('fis012FormTecnico');

        // Botões dentro do Formulário
		$formArray['campos']['geral']['fis012FormBtnEnviar'] = array('name' => 'fis012FormBtnEnviar', 'type' => 'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['fis012FormBtnLimpar'] = array('name' => 'fis012FormBtnLimpar', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;'.($g['usuarioAtual']!=63 ? 'opacity: 0.8;':''), 'reject' => true, 'disabled' => $campoDisabled);

		$form->definirFormularioArray($formArray);

		$sqlT276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe FROM T276 WHERE T276_Id = '{$r_acaoId}'");
		$rowT276 = mysql_fetch_assoc($sqlT276);
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form, false, empty($rowT276['T276_Numero_Protocolo_MDFe']) ? false : true)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
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
								divRefresh('{$g["divIdParent"]}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, false, false, empty($rowT276['T276_Numero_Protocolo_MDFe']) ? false : true);
		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012FormCadastrarSeguradora':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T276_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		//echo "acaoId = ".$acaoId;
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
		$form_tabela = 'T279';
		$form_campo_pk = 'T279_Id';
		// Valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormCadastrarSeguradora_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarSeguradora/';

		// Form Editor
		$form_editor = array('geral' => '{"T279_D172_Id":{"top":7,"left":42.0625,"width":314,"hidden":0},"fis012FormSeguradora_btn":{"top":5,"left":448,"width":null,"hidden":0},"T279_Numero_Apolice":{"top":7,"left":494.46875,"width":184,"hidden":0},"fis012FormCadastrarSeguradora_btn":{"top":5,"left":736,"width":null,"hidden":0},"T279_Seguradora_Nome":{"top":30,"left":8.65625,"width":328,"hidden":0},"T279_Seguradora_CNPJ":{"top":53,"left":9.28125,"width":328,"hidden":0}}');
				
		// Novo Formulário
		$form = new Formulario('fis012FormCadastrarSeguradora');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T279_Id' => array('name' => 'T279_Id', 'type' => 'hidden', 'align' => 'right'),
					'T279_T276_Id' => array('name' => 'T279_T276_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T276_Id),
					'T279_D172_Id' => array('name' => 'T279_D172_Id', 'label'=> 'Seguradora', 'type' => 'pesquisa', 'pesquisa' => array('D172', 'D172_Id', 'D172_Nome, D172_CNPJ', 'WHERE D172_Flag_Ativo = "S" AND D172_C004_Id = ' . $g['empresaAtual']), 'extra' => "onchange=\"carregaSeguradora(this, '{$g['divId']}');\""),
					'T279_Seguradora_Nome' => array('name' => 'T279_Seguradora_Nome', 'label' => 'Nome Seguradora', 'readonly' => true, 'align' => 'right'),
					'T279_Seguradora_CNPJ' => array('name' => 'T279_Seguradora_CNPJ', 'label' => 'CNPJ Seguradora', 'readonly' => true, 'align' => 'right'),
					'T279_Numero_Apolice' => array('name' => 'T279_Numero_Apolice', 'label' => 'Apólice', 'type' => 'text'),
					'fis012FormSeguradora_btn' => array('name' => 'fis012FormSeguradora_btn', 'type' => 'button', 'reload' => false, 'value' => '+', 'reject' => true),
					'fis012FormCadastrarSeguradora_btn' => array('name' => 'fis012FormCadastrarSeguradora_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)	
			)
		);
		
		$form->definirFormularioArray($formArray);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#fis012FormSeguradora_btn').unbind('click');
		    	$('#fis012FormSeguradora_btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Nova Seguradora', '/cad/cad066/content/cad066ContentFormCadastrarSeguradoras/', '', [570,290]);
				});
		    } </script></div>
EOT;

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
				$D172_Id = $form->campoValorEnviado('T279_D172_Id');
				$T276_Id = $form->campoValorEnviado('T279_T276_Id');

				$sqlD172 = mysql_query("SELECT T279_Id FROM T279 WHERE T279_D172_Id = {$D172_Id} AND T279_T276_Id = {$T276_Id}");

				if(mysql_num_rows($sqlD172) > 0){
					echo gGeraAlertMsg('Erro', 'Seguradora já vinculada a este MDF-e.');
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, 0, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012FormCadastrarAverbacaoSeguradora':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T279_Id = (isset($_REQUEST['T279_Id'])) ? $_REQUEST['T279_Id'] : false;
		log("form T279:".$T279_Id);
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

		$campoParent='T279_Id'; 
		$valorParent=$T279_Id;

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D174';
		$form_campo_pk = 'D174_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormCadastrarAverbacaoSeguradora_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarAverbacaoSeguradora/';

		//form editor
		$form_editor = array('geral' =>'{"D174_Numero_Averbacao":{"top":7,"left":5,"width":232,"hidden":0},"fis012FormCadastrarAverbacaoSeguradora_btn":{"top":5,"left":352,"width":null,"hidden":0}}');
				
		// novo formulário
		$form = new Formulario('fis012FormCadastrarAverbacaoSeguradora');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D174_Numero_Averbacao' => array('name' => 'D174_Numero_Averbacao', 'label' => 'Número averbação', 'type' => 'text'),
					'D174_T279_Id' => array('name' => 'D174_T279_Id', 'label' => 'Número averbação', 'type' => 'hidden', 'value' => $T279_Id),
					'fis012FormCadastrarAverbacaoSeguradora_btn' => array('name' => 'fis012FormCadastrarAverbacaoSeguradora_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'style'=> 'float:right;', 'reject'=>true),
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
				// Insert
				if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					// Imprime uma mensagem e fecha a janela
					$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
					echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divIdParent']}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
				}
			}
		}	
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $campoParent, $valorParent);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCadastrarLacreMdfe':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

		// Parâmetro de lacres
		$T276_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false; // Mdf-e

		// Valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;		
        
        /**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T280';
		$form_campo_pk = 'T280_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarLacreMdfeBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarLacreMdfe/';
		
		// Layout do formulário
		$form_editor = array('geral' => '{"T280_D171_Id":{"top":7,"left":13.84375,"width":217,"hidden":0},"fis012FormCadastrarLacreMdfeBtnAdd":{"top":5,"left":336,"width":null,"hidden":0}}');
		
		// Novo formulário
		$form = new Formulario('fis012FormCadastrarLacreMdfe');
		
		// Array de definição do formulário (campos padrão aos tipos de lacres)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T280_D171_Id' => array('name' => 'T280_D171_Id', 'label' => 'Número Lacre', 'type' => 'pesquisa', 'pesquisa' => array('D171', 'D171_Id', 'D171_Numero_Lacre, D171_Descricao', 'WHERE D171_Tipo = "3" AND D171_Flag_Ativo = "S" AND D171_C004_Id = ' . $g['empresaAtual'] . ' ORDER BY D171_Numero_Lacre')),
					'T280_T276_Id' => array('name' => 'T280_T276_Id', 'label' => '', 'type' => 'hidden', 'value' => $T276_Id),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		$formArray['campos']['geral']['fis012FormCadastrarLacreMdfeBtnAdd'] = array('name' => 'fis012FormCadastrarLacreMdfeBtnAdd', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui T276_Id)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Validações de lacres já existentes (cadastrados/ inseridos)
				$D171_Id = $form->campoValorEnviado('T280_D171_Id');
				$sqlT280 = mysql_query("SELECT T280_Id FROM T280 WHERE T280_T276_Id = {$T276_Id} AND T280_D171_Id = {$D171_Id}");

				if (mysql_num_rows($sqlT280) > 0) {
					echo gGeraAlertMsg('Erro', 'Lacre já vinculado a este MDF-e.');
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		**/
		$gerado = gGeraCamposForm($form, $tab, 0, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		 * PARTE 5: Exibir o formulário
		**/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCadastrarLacreRodoviario':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

		// ID MDF-e
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		// Valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;		
        
        /**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T286';
		$form_campo_pk = 'T286_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarLacreRodoviarioBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarLacreRodoviario/';
		
		// Layout do formulário
		$form_editor = array('geral' => '{"T286_D171_Id":{"top":7,"left":13.84375,"width":217,"hidden":0},"fis012FormCadastrarLacreRodoviarioBtnAdd":{"top":5,"left":336,"width":null,"hidden":0}}');
		
		// Novo formulário
		$form = new Formulario('fis012FormCadastrarLacreRodoviario');
		
		// Array de definição do formulário (campos padrão aos tipos de lacres)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T286_D171_Id' => array('name' => 'T286_D171_Id', 'label' => 'Número Lacre', 'type' => 'pesquisa', 'pesquisa' => array('D171', 'D171_Id', 'D171_Numero_Lacre, D171_Descricao', 'WHERE D171_Tipo = "4" AND D171_Flag_Ativo = "S" AND D171_C004_Id = ' . $g['empresaAtual'] . ' ORDER BY D171_Numero_Lacre')),
					'T286_T276_Id' => array('name' => 'T286_T276_Id', 'label' => '', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		$formArray['campos']['geral']['fis012FormCadastrarLacreRodoviarioBtnAdd'] = array('name' => 'fis012FormCadastrarLacreRodoviarioBtnAdd', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$D171_Id = $form->campoValorEnviado('T286_D171_Id');
				$sqlT283 = mysql_query("SELECT T283_Id FROM T283 WHERE T283_T276_Id = {$T276_Id} AND T283_Categoria_Veiculo = 2");
				$arrayT283 = mysql_fetch_array($sqlT283);
				
				if (mysql_num_rows($sqlT283) <= 0) {
					echo gGeraAlertMsg('Erro', 'Insira antes os dados do veículo de tração na aba Veículo.');
				} else {
					$sqlT286 = mysql_query("SELECT T286_Id, T286_T276_Id FROM T286 WHERE T286_D171_Id = {$D171_Id} AND T286_T276_Id = {$T276_Id}");
					$arrayT286 = mysql_fetch_array($sql);

					if (mysql_num_rows($sqlT286) > 0) {
						echo gGeraAlertMsg('Erro', 'Lacre já vinculado a este Modal Rodoviário.');
					} else {
						$form->campoSetarValor('T286_T276_Id', $T276_Id, true);

						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$form->campoValorEnviado('divReload')}', true);
									divRefresh('{$g['divIdParent']}', true);
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
						}
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		**/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		 * PARTE 5: Exibir o formulário
		**/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCadastrarLacreCarga':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

		// ID MDF-e
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		// Parâmetro de lacres
		$T282_Id = (isset($_REQUEST['T282_Id'])) ? $_REQUEST['T282_Id'] : false; // Mdf-e

		// Valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;		
        
        /**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T288';
		$form_campo_pk = 'T288_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarLacreCargaBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarLacreCarga/';
		
		// Layout do formulário
		$form_editor = array('geral' => '{"T288_D171_Id":{"top":7,"left":13.84375,"width":217,"hidden":0},"fis012FormCadastrarLacreCargaBtnAdd":{"top":5,"left":336,"width":null,"hidden":0}}');
		
		// Novo formulário
		$form = new Formulario('fis012FormCadastrarLacreCarga');
		
		// Array de definição do formulário (campos padrão aos tipos de lacres)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T288_D171_Id' => array('name' => 'T288_D171_Id', 'label' => 'Número Lacre', 'type' => 'pesquisa', 'pesquisa' => array('D171', 'D171_Id', 'D171_Numero_Lacre, D171_Descricao', 'WHERE D171_Tipo = "2" AND D171_Flag_Ativo = "S" AND D171_C004_Id = ' . $g['empresaAtual'] . ' ORDER BY D171_Numero_Lacre')),
					'T288_T282_Id' => array('name' => 'T288_T282_Id', 'label' => '', 'type' => 'hidden', 'value' => $T282_Id),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		$formArray['campos']['geral']['fis012FormCadastrarLacreCargaBtnAdd'] = array('name' => 'fis012FormCadastrarLacreCargaBtnAdd', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Validações de lacres já existentes (cadastrados/ inseridos)
				$D171_Id = $form->campoValorEnviado('T288_D171_Id');
				$sqlT288 = mysql_query("SELECT T288_Id FROM T288 LEFT JOIN T282 ON T282_Id = T288_T282_Id LEFT JOIN T281 ON T281_Id = T282_T281_Id LEFT JOIN T284 ON T284_Id = T281_T284_Id WHERE T284_T276_Id = {$T276_Id} AND T288_D171_Id = {$D171_Id} AND T288_T282_Id = {$T282_Id}");

				if (mysql_num_rows($sqlT288) > 0) {
					echo gGeraAlertMsg('Erro', 'Lacre já vinculado a esta Carga.');
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		**/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		 * PARTE 5: Exibir o formulário
		**/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	/**
	 * @author Ricardo Norio Miyata
	**/
	case 'fis012FormCadastrarLacreTransporte':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

		// ID MDF-e
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		// Parâmetro de lacres
		$T281_Id = (isset($_REQUEST['T281_Id'])) ? $_REQUEST['T281_Id'] : false; // Mdf-e

		// Valor passado para o arquivo de configuração T278.php 
		$form_parent = 'T276';
        $form_parent_valor = $T276_Id;		
        
        /**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T287';
		$form_campo_pk = 'T287_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('fis012FormCadastrarLacreTransporteBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastrarLacreTransporte/';
		
		// Layout do formulário
		$form_editor = array('geral' => '{"T287_D171_Id":{"top":7,"left":13.84375,"width":217,"hidden":0},"fis012FormCadastrarLacreTransporteBtnAdd":{"top":5,"left":336,"width":null,"hidden":0}}');
		
		// Novo formulário
		$form = new Formulario('fis012FormCadastrarLacreTransporte');
		
		// Array de definição do formulário (campos padrão aos tipos de lacres)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T287_D171_Id' => array('name' => 'T287_D171_Id', 'label' => 'Número Lacre', 'type' => 'pesquisa', 'pesquisa' => array('D171', 'D171_Id', 'D171_Numero_Lacre, D171_Descricao', 'WHERE D171_Tipo = "1" AND D171_Flag_Ativo = "S" AND D171_C004_Id = ' . $g['empresaAtual'] . ' ORDER BY D171_Numero_Lacre')),
					'T287_T281_Id' => array('name' => 'T287_T281_Id', 'label' => '', 'type' => 'hidden', 'value' => $T281_Id),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		$formArray['campos']['geral']['fis012FormCadastrarLacreTransporteBtnAdd'] = array('name' => 'fis012FormCadastrarLacreTransporteBtnAdd', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Validações de lacres já existentes (cadastrados/ inseridos)
				$D171_Id = $form->campoValorEnviado('T287_D171_Id');
				$sqlT287 = mysql_query("SELECT T287_Id FROM T287 LEFT JOIN T281 ON T281_Id = T287_T281_Id LEFT JOIN T284 on T284_Id = T281_T284_Id WHERE T284_T276_Id = {$T276_Id} AND T287_D171_Id = {$D171_Id} AND T287_T281_Id = {$T281_Id}");

				if (mysql_num_rows($sqlT287) > 0) {
					echo gGeraAlertMsg('Erro', 'Lacre já vinculado a este Tipo de Transporte.');
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		**/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		 * PARTE 5: Exibir o formulário
		**/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		 * Botões
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;
	case 'fis012FormCadastroCteMdfe':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;		

		/**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T284';
		$form_campo_pk = 'T284_Id';
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormCadastroCteMdfeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormCadastroCteMdfe/';

		// Editor
		$form_editor = array(
			'geral' => '{"T284_Destinatario_Nome":{"top":7,"left":44,"width":296,"hidden":0},"T284_Valor_Carga":{"top":30,"left":76,"width":146,"hidden":0},"T284_Peso_Bruto_Total":{"top":53,"left":81,"width":146,"hidden":0},"T284_D018_Id":{"top":76,"left":25,"width":50,"hidden":0},"T284_D020_Id":{"top":99,"left":4,"width":191,"hidden":0},"T284_Chave_De_Acesso_CTe":{"top":122,"left":28,"width":312,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T284_Flag_Tipo_Documento' => array('name' => 'T284_Flag_Tipo_Documento', 'label' => 'Tipo de Documento', 'type' => 'hidden', 'align'=>'right', 'style'=>'float:right','value' => '1'),				
					'T284_Destinatario_Nome' => array('name' => 'T284_Destinatario_Nome', 'label'=>'Nome Destinatário', 'type' => 'text'),
					'T284_Valor_Carga' => array('name' => 'T284_Valor_Carga', 'label' => 'Valor Total', 'type' => 'moeda'),
					'T284_Peso_Bruto_Total' => array('name' => 'T284_Peso_Bruto_Total', 'label'=>'Peso (Kg)', 'type' => 'moeda'),
					'T284_D018_Id' => array('name' => 'T284_D018_Id', 'label' => 'UF Descarregamento','type' => 'select', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)),
					'T284_D020_Id' => array('name' => 'T284_D020_Id', 'label' => 'Cidade Descarregamento', 'type' => 'select', 'select' => gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', '', true)),
					'T284_Chave_De_Acesso_CTe' => array('name' => 'T284_Chave_De_Acesso_CTe', 'label' => 'Chave de Acesso CT-e','type' => 'text'),					
					'T284_T276_Id' => array('name' => 'T284_T276_Id', 'label' => 'T284_T276_Id', 'type' => 'hidden', 'value' => $T276_Id),
					'T284_Cidade_Descarregamento' => array('name' => 'T284_Cidade_Descarregamento', 'label' => 'T284_Cidade_Descarregamento', 'type' => 'hidden'),
					'T284_Cidade_Descarregamento_Codigo_IBGE' => array('name' => 'T284_Cidade_Descarregamento_Codigo_IBGE', 'label' => 'T284_Cidade_Descarregamento_Codigo_IBGE', 'type' => 'hidden'),
				)
			)
		);

		$form = new Formulario('fis012FormCadastroCteMdfe');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		**/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			//Set the "T284_Cidade_Descarregamento" value
			$sql = "SELECT D020_Nome_Cidade FROM D020 WHERE D020_Id = " . $form->campoValorEnviado('T284_D020_Id');
			$result = mysql_query($sql);
			$arrayDataD020 = mysql_fetch_assoc($result);
			echo mysql_error();
			$form->campoSetarValor('T284_Cidade_Descarregamento', $arrayDataD020['D020_Nome_Cidade'], true);

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
					//Verifica se o CT-e ja existe para esse MDF-e
					$T284_Chave_De_Acesso_CTe = $form->campoValorEnviado('T284_Chave_De_Acesso_CTe');
					$sql = mysql_query("SELECT T284_Id FROM T284 WHERE T284_T276_Id = {$T276_Id} AND T284_Chave_De_Acesso_CTe = '{$T284_Chave_De_Acesso_CTe}'");
					if(mysql_num_rows($sql) > 0){
						echo gGeraAlertMsg('Erro', 'Conhecimento já cadastrado.');
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
							echo gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
						}
					}					
				}
				mysql_query("CALL T276_Gravar_Totalizacao('{$T276_Id}')");
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="fis012FormCadastroCteMdfeBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis012FormInfoPgtoFrete':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		/**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T318';
		$form_campo_pk = 'T318_Id';
		$form_parent = 'T276_Id';
        $form_parent_valor = $T276_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormInfoPgtoFreteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormInfoPgtoFrete/';

			// Editor
			$form_editor = array(
				'geral' => '{"T318_D024_Id":{"top":7,"left":51.78125,"width":185,"hidden":0},"T318_Valor_Contrato":{"top":30,"left":36.078125,"width":200,"hidden":0},"T318_Indicador_Forma_Pagamento":{"top":53,"left":46.546875,"width":207,"hidden":0},"T318_CIOT":{"top":76,"left":63.28125,"width":200,"hidden":0},"T318_Codigo_Banco":{"top":99,"left":50.375,"width":200,"hidden":0},"T318_Codigo_Agencia":{"top":122,"left":41.640625,"width":200,"hidden":0},"T318_Chave_Pix":{"top":145,"left":58.578125,"width":200,"hidden":0},"T318_CNPJ_Instituicao_Pagamento_Frete":{"top":168,"left":15.03125,"width":200,"hidden":0}}'
			);

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T318_T276_Id' => array('name' => 'T318_T276_Id', 'label' => 'MDFe', 'type' => 'hidden', 'align'=>'right', 'style'=>'float:right','value' => $T276_Id),
					'T318_D024_Id' => array('name' => 'T318_D024_Id', 'label' => 'Resp. Pgto.', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" '. $clientePorEmpresa .' order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]')),
					'T318_Valor_Contrato' => array('name' => 'T318_Valor_Contrato', 'label' => 'Valor Contrato', 'type' => 'moeda'),
					'T318_Indicador_Forma_Pagamento' => array('name' => 'T318_Indicador_Forma_Pagamento', 'label' => 'Forma Pgto.', 'type' => 'select', 'select' => array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => '0 - Pagamento à Vista', 'value' => 0),
						array('title' => '1 - Pagamento à Prazo', 'value' => 1),
					)),
					'T318_CIOT' => array('name' => 'T318_CIOT', 'label' => 'CIOT', 'type' => 'text', 'length' => array(12,12)),
					'T318_Codigo_Banco' => array('name' => 'T318_Codigo_Banco', 'label' => 'Cód. Banco', 'type' => 'text'),
					'T318_Codigo_Agencia' => array('name' => 'T318_Codigo_Agencia', 'label' => 'Cód. Agência', 'type' => 'text'),
					'T318_Chave_Pix' => array('name' => 'T318_Chave_Pix', 'label' => 'Chave Pix', 'type' => 'text'),
					'T318_CNPJ_Instituicao_Pagamento_Frete' => array('name' => 'T318_CNPJ_Instituicao_Pagamento_Frete', 'label' => 'CNPJ I. Pgto. Frete', 'type' => 'cnpj'),
				)
			)
		);

		$form = new Formulario('fis012FormInfoPgtoFrete');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		**/
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
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
									});
									//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
							} </script></div>
						JS;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						//echo gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!');
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
								abrirJanela(unique(), '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Info Pgto. Frete: ' + '{$g['mysqlLastId']}', '/fis/fis012/content/fis012AlterarInfoPgtoFrete/', '&T318_Id=' + encodeURIComponent('{$g['mysqlLastId']}') + '&acaoId=' + encodeURIComponent('{$g['mysqlLastId']}'), [750,650]);
							} </script></div>
						JS;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="fis012FormInfoPgtoFreteBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fis012FormInfoPgtoFreteComponente':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T318_Id = (isset($_REQUEST['T318_Id'])) ? $_REQUEST['T318_Id'] : false;

		/**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T319';
		$form_campo_pk = 'T319_Id';
		$form_parent = 'T318_Id';
        $form_parent_valor = $T318_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormInfoPgtoFreteComponenteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormInfoPgtoFreteComponente/';

		// Editor
		$form_editor = array(
			'geral' => '{"T319_Descricao":{"top":7,"left":9.6875,"width":359,"hidden":0},"T319_Valor":{"top":30,"left":33.171875,"width":360,"hidden":0},"T319_Tipo":{"top":53,"left":36.890625,"width":370,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T319_T318_Id' => array('name' => 'T319_T318_Id', 'label' => 'Info Pgto. Frete', 'type' => 'hidden', 'align'=>'right', 'style'=>'float:right','value' => $T318_Id),
					'T319_Descricao' => array('name' => 'T319_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'T319_Valor' => array('name' => 'T319_Valor', 'label' => 'Valor', 'type' => 'moeda'),
					'T319_Tipo' => array('name' => 'T319_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' => array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => '01 - Vale Pedágio', 'value' => '01'),
						array('title' => '02 - Impostos, taxas e contribuições', 'value' => '02'),
						array('title' => '03 - Despesas (bancárias, meios de pagamento, outras)', 'value' => '03'),
						array('title' => '99 - Outros', 'value' => '99'),
					)),
				)
			)
		);

		$form = new Formulario('fis012FormInfoPgtoFreteComponente');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		**/
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
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
									});
									//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
							} </script></div>
						JS;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						//echo gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!');
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
						JS;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="fis012FormInfoPgtoFreteComponenteBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;

	case 'fis012FormInfoPgtoFreteParcela':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		**/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T318_Id = (isset($_REQUEST['T318_Id'])) ? $_REQUEST['T318_Id'] : false;

		/**
		 * PARTE 2: Definição do formulário
		**/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T320';
		$form_campo_pk = 'T320_Id';
		$form_parent = 'T318_Id';
        $form_parent_valor = $T318_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis012FormInfoPgtoFreteParcelaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis012/form/fis012FormInfoPgtoFreteParcela/';

		// Editor
		$form_editor = array(
			'geral' => '{"T320_Numero_Parcela":{"top":7,"left":38.265625,"width":120,"hidden":0},"T320_Valor_Parcela":{"top":30,"left":10.015625,"width":120,"hidden":0},"T320_Data_Vencimento":{"top":53,"left":32.15625,"width":120,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T320_T318_Id' => array('name' => 'T320_T318_Id', 'label' => 'Info Pgto. Frete', 'type' => 'hidden', 'align'=>'right', 'style'=>'float:right','value' => $T318_Id),
					'T320_Numero_Parcela' => array('name' => 'T320_Numero_Parcela', 'label' => 'Nº Parcela', 'type' => 'text'),
					'T320_Valor_Parcela' => array('name' => 'T320_Valor_Parcela', 'label' => 'Valor Parcela R$', 'type' => 'moeda'),
					'T320_Data_Vencimento' => array('name' => 'T320_Data_Vencimento', 'label' => 'Vencimento', 'type' => 'data'),
				)
			)
		);

		$form = new Formulario('fis012FormInfoPgtoFreteParcela');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		**/
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
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
									});
									//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
							} </script></div>
						JS;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						//echo gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!');
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
						JS;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="fis012FormInfoPgtoFreteParcelaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
}
?>
