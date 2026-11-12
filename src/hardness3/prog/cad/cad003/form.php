<?php
namespace hardness;
require_once('prog/cad/cad003/form_func-js.php');
      
switch ($g['r'][3]) {
	/**
	* @author 
	* @since 30/11/2011  
	*/
	case 'cad003CadastroRepresentantes':
		global $g;
		gHabilitaNavegacao($g['divId'], 'CAD003', 'cad003CadastroRepresentantes');		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[D021_Cnpj, D021_Nome_Empresa]');
				$('#{$g['divId']}').data('labelPesquisa', '[CNPJ, Nome Empresa]');
				$('#{$g['divId']}').data('sqlExtra', '');
			} </script></div>
EOT;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		//$tab = (isset($r_tab)) ? $r_tab : false;
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D021';
		$form_campo_pk = 'D021_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad003FormRepresentantesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad003/form/cad003CadastroRepresentantes/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D021_Ativo":{"top":398,"left":65.703125,"width":53,"hidden":0},"D021_Nome_Empresa":{"top":7,"left":60.984375,"width":264,"hidden":0},"D021_Flag_Pessoa_Fisica_Juridica":{"top":30,"left":68.890625,"width":95,"hidden":0},"D021_Cnpj":{"top":53,"left":63.640625,"width":122,"hidden":0},"buttonSearchCnpj":{"top":51,"left":224,"width":null,"hidden":0},"D021_Cpf":{"top":76,"left":70.40625,"width":120,"hidden":0},"D021_Inscricao_Estadual":{"top":99,"left":22.015625,"width":72,"hidden":0},"D021_Cep":{"top":122,"left":71.421875,"width":72,"hidden":0},"buttonSearchCep":{"top":120,"left":176,"width":null,"hidden":0},"D021_Endereco":{"top":145,"left":44.734375,"width":216,"hidden":0},"D021_Complemento":{"top":168,"left":12.078125,"width":216,"hidden":0},"D021_Bairro":{"top":191,"left":61.453125,"width":216,"hidden":0},"D021_D018_Id":{"top":214,"left":77.375,"width":48,"hidden":0},"D021_C007_Id":{"top":214,"left":379.703125,"width":190,"hidden":1},"D021_D020_Id":{"top":237,"left":56.53125,"width":190,"hidden":0},"D021_D030_Id":{"top":260,"left":69.328125,"width":190,"hidden":0},"D021_Nome_Fantasia":{"top":259.96875,"left":384.75,"width":175,"hidden":1},"D021_DDD_Telefone_1":{"top":283,"left":48.203125,"width":35,"hidden":0},"D021_Telefone_1":{"top":283,"left":144,"width":72,"hidden":0},"D021_DDD_Fax":{"top":306,"left":73.25,"width":35,"hidden":0},"D021_Fax":{"top":306,"left":144,"width":74,"hidden":0},"D021_D062_Id":{"top":329,"left":59.640625,"width":190,"hidden":0},"D021_Agencia":{"top":329,"left":386.90625,"width":100,"hidden":0},"D021_Conta_Bancaria":{"top":352,"left":61.5,"width":100,"hidden":0},"D021_Nome_Conta":{"top":352,"left":365.171875,"width":100,"hidden":0},"D021_Percentual_Comissao":{"top":375,"left":30.4375,"width":40,"hidden":0}}');
		
		// Definição do Formulário
		
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D021_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D060_C008_Id', 'label' => 'Tipo', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Jurídica', 'value' => 'J'),
							array('title' => 'Física', 'value' => 'F'),
						)
					),
					'D021_Cnpj' => array('name' => 'D021_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(30,40)),
					'buttonSearchCnpj' => 	array('name' => 'buttonSearchCnpj',	'label' => '', 'type' => 'button', 'readonly' => $disabledCadastros, 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJRepresentantes(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D021_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					
					'D021_Agencia' => array('name' => 'D021_Agencia', 'label' => 'Agência','align' => 'right', 'style' => 'float:right;', 'type' => 'text', 'length' => array(15,40)),
					'D021_Cpf' => array('name' => 'D021_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'length' => array(30,40)),
					'D021_Conta_Bancaria' => array('name' => 'D021_Conta_Bancaria', 'label' => 'Conta','align' => 'right', 'style' => 'float:right;', 'type' => 'text', 'length' => array(15,40)),
					'D021_Inscricao_Estadual' => array('name' => 'D021_Inscricao_Estadual', 'label' => 'Insc. Estadual', 'type' => 'text', 'length' => array(30,40)),
					'D021_Nome_Conta' => array('name' => 'D021_Nome_Conta', 'label' => 'Nome Conta','align' => 'right', 'style' => 'float:right;', 'type' => 'text', 'length' => array(15,40)),
					'D021_Nome_Empresa' => array('name' => 'D021_Nome_Empresa', 'label' => 'Nome', 'type' => 'text', 'length' => array(30,40)),
					'D021_Nome_Fantasia' => array('name' => 'D021_Nome_Fantasia', 'label' => 'Fantasia', 'type' => 'text', 'length' => array(30,50)),
					'D021_D062_Id' => array('name' => 'D021_D062_Id', 'label' => 'Banco', 'type' => 'select', 'extra' => 'style="width:190px;"',
						'select' => gGeraSelect('D062','D062_Id','D062_Numero_Banco,D062_Nome_Banco','',true)
					),
                    'D021_Cep' => array('name' => 'D021_Cep', 'label' => 'Cep', 'type' => 'cep', 'length' => array(15,40)),
                    'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D021_Cep\", {\"EnderecoDb\": \"D021_Endereco\", \"Cep\": \"D021_Cep\", \"BairroDb\": \"D021_Bairro\", \"UfDb\": \"D021_D018_Id\",\"CidadeDb\": \"D021_D020_Id\", \"PaisDb\": \"D021_D030_Id\"})'", 'align'=>'right', 'value'=>'Buscar', 'reject'=>true),
					'D021_Endereco' => array('name' => 'D021_Endereco', 'label' => 'Endereço', 'type' => 'text', 'length' => array(30,40)),
					'D021_Complemento' => array('name' => 'D021_Complemento', 'label' => 'Completemento', 'type' => 'text', 'length' => array(15,40)),
					'D021_Bairro' => array('name' => 'D021_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(30,40)),
					'D021_D018_Id' => array('name' => 'D021_D018_Id', 'label' => 'UF', 'extra' => "onchange=\"alteraEstadoRepresentantes(this, '{$g['divId']}')\"", 'type' => 'select',
						'select' => gGeraSelect('D018','D018_Id','D018_UF','',true)
					),
					'D021_D020_Id' => array('name' => 'D021_D020_Id', 'label' => 'Cidade', 'type' => 'select', 'extra' => 'style="width:190px;"',
						'select' => gGeraSelect('D020','D020_Id','D020_Nome_Cidade','',true)
					),
					'D021_D030_Id' => array('name' => 'D021_D030_Id', 'label' => 'País', 'type' => 'select', 'extra' => 'style="width:190px;"',
						'select' => gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)
					),
					'D021_DDD_Telefone_1' => array('name' => 'D021_DDD_Telefone_1', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2)),
					'D021_Telefone_1' => array('name' => 'D021_Telefone_1', 'label' => '', 'type' => 'text', 'length' => array(15,15), 'align'=>'right'),
					'D021_DDD_Fax' => array('name' => 'D021_DDD_Fax', 'label' => 'Fax', 'type' => 'text', 'length' => array(2,2)),
					'D021_Fax' => array('name' => 'D021_Fax', 'label' => '', 'type' => 'text', 'length' => array(15,40), 'align'=>'right'),
					'D021_C007_Id' => array('name' => 'D021_C007_Id', 'label' => 'Vendedor', 'type' => 'select', 'extra' => 'style="width:190px;"',
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','',true)
					),
					'D021_Percentual_Comissao' => array('name' => 'D021_Percentual_Comissao', 'label' => '% Comissão', 'type' => 'Moeda','value' => '0,00', 'length' => array(15,40)),
					'D021_Ativo' => array('name' => 'D021_Ativo', 'label' => 'Ativo', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
				)
			)
		);
        $form = new Formulario('form-cad060FormPrincipal');
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
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// Ao invés de recarregar o grid inteiro, pode ser inserido a linha nova no início do grid
								//divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divIdParent']}', true, false, false, '{$g['mysqlLastId']}', '{$form_campo_pk}');
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
	break;

	/**
	* @author Raquel Pazini Cardoso
	* @since 21/02/2019
	*/
	case 'cad003FormRepresentantesUsuarios':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D021 = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		$form_tabela = 'T291';
		$form_campo_pk = 'T291_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad003FormRepresentantesUsuariosBtn');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad003/form/cad003FormRepresentantesUsuarios/';
		$form_editor = array(
			'geral' => '{"T291_C007_Id":{"top":7,"left":12.640625,"width":190,"hidden":0},"cad003FormRepresentantesUsuariosBtn":{"top":5,"left":272,"width":null,"hidden":0}}',
		);
		
		// Array de definição do formulário
		$form = new Formulario('cad003FormRepresentantesUsuarios');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T291_C007_Id' => array('name' => 'T291_C007_Id', 'label' => 'Usuário', 'type' => 'select', 'extra' => 'style="width:190px;"',
						'select' => gGeraSelect('C007','C007_Id','C007_Nome','WHERE C007_Ativo=\'S\'',true)
					 ),
					'T291_D021_Id' => array('name' => 'T291_D021_Id', 'label' => '', 'type' => 'hidden', 'value' => $D021),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true)
				)
			)
		);

		// Botões do form
		$formArray['campos']['geral']['cad003FormRepresentantesUsuariosBtn'] = array('name' => 'cad003FormRepresentantesUsuariosBtn', 'type'=>'button', 'reload' => true, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
				$C007 = $form->campoValorEnviado('T291_C007_Id');
				$SQL = mysql_query("SELECT T291_C007_Id FROM T291 WHERE T291_D021_Id = '{$D021}' AND T291_C007_Id='{$C007}'");
				if(mysql_num_rows($SQL) > 0){
					echo gGeraAlertMsg('Erro', 'Usuário já cadastrado para esse representante');
					echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
				}else if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
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
	break;
}
?>
