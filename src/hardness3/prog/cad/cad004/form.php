<?php
namespace hardness;
require_once('prog/cad/cad004/form_func-js.php');
/**
* Formulário
* /fis/fis001/form/
*/

/**
* Ações
*/
switch ($g['r'][3]) {

	case 'form1':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D022';
		$form_campo_pk = 'D022_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad004/form/form1/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D022_Nome_Empresa":{"top":7,"left":101.515625,"width":264,"hidden":0},"D022_Flag_Pessoa_Fisica_Juridica":{"top":30,"left":94.8125,"width":120,"hidden":0},"D022_Cnpj":{"top":53,"left":111.640625,"width":137,"hidden":0},"buttonSearchCnpj1":{"top":51,"left":287.984375,"width":null,"hidden":0},"D022_Cpf":{"top":76,"left":118.40625,"width":136,"hidden":0},"D022_Inscricao_Estadual":{"top":99,"left":48.5,"width":137,"hidden":0},"D022_Cep":{"top":122,"left":119.421875,"width":104,"hidden":0},"buttonSearchCep":{"top":120,"left":255.984375,"width":null,"hidden":0},"D022_Endereco":{"top":145,"left":92.734375,"width":264,"hidden":0},"D022_Bairro":{"top":168,"left":109.453125,"width":264,"hidden":0},"D022_D018_Id":{"top":191,"left":125.375,"width":48,"hidden":0},"D022_DDD_Fax":{"top":191,"left":375.515625,"width":232,"hidden":1},"D022_D020_Id":{"top":214,"left":104.53125,"width":190,"hidden":0},"D022_D030_Id":{"top":237,"left":117.328125,"width":190,"hidden":0},"D022_DDD_Telefone_1":{"top":260,"left":67.890625,"width":35,"hidden":0},"D022_Telefone_1":{"top":260,"left":191.984375,"width":100,"hidden":0},"D022_DDD_Telefone_2":{"top":283,"left":101.390625,"width":35,"hidden":0},"D022_Telefone_2":{"top":283,"left":191.984375,"width":100,"hidden":0},"D022_Fax":{"top":306,"left":111.359375,"width":232,"hidden":0},"D022_Complemento":{"top":329,"left":99.734375,"width":264,"hidden":0},"D022_D036_Id":{"top":352,"left":46.328125,"width":143,"hidden":0},"D022A_Prioridade":{"top":351.984375,"left":312.34375,"width":41,"hidden":0},"D022_Flag_Tipo_Etiqueta_Transporte":{"top":375,"left":18.109375,"width":271,"hidden":0},"D022A_Flag_Ativo":{"top":398,"left":113.703125,"width":53,"hidden":0}}'
        );
        
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad_cad004_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D022_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D022_Flag_Pessoa_Fisica_Juridica', 'label'=>'Pessoa Física / Jurídica', 'type' => 'select', 'extra'=>'style="width:120px;"','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Jurídica', 'value' => 'J'),
							array('title' => 'Física', 'value' => 'F'),
							)
						),
					'D022_D036_Id' => array('name' => 'D022_D036_Id', 'label' => 'Tipo de Transporte', 'type' => 'select', 'select' => 
						gGeraSelect('D036', 'D036_Id', 'D036_Tipo_Transporte', 'order by D036_Tipo_Transporte', true)
					),
		        	'D022_Cnpj' => array('name' => 'D022_Cnpj', 'label'=>'CNPJ', 'type' => 'cnpj','length' => array(18,18)),
					'buttonSearchCnpj1' => 	array('name' => 'buttonSearchCnpj1',	'label' => '', 'type' => 'button', 'readonly' => $disabledCadastros, 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJTransportadoras(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D022_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					
		        	'D022_Cpf' => array('name' => 'D022_Cpf', 'label'=>'CPF', 'type' => 'cpf','length' => array(11,14)),
		        	'D022_Inscricao_Estadual' => array('name' => 'D022_Inscricao_Estadual', 'label'=>'Inscrição Estadual', 'type' => 'text','length' => array(20,20)),
		        	'D022_Nome_Empresa' => array('name' => 'D022_Nome_Empresa', 'label'=>'Nome', 'type' => 'text','length' => array(30,40)),
                    'D022_Cep' => array('name' => 'D022_Cep', 'label'=>'Cep', 'type' => 'cep','length' => array(15,40)),
                    'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D022_Cep\", {\"EnderecoDb\": \"D022_Endereco\", \"Cep\": \"D022_Cep\", \"BairroDb\": \"D022_Bairro\", \"UfDb\": \"D022_D018_Id\",\"CidadeDb\": \"D022_D020_Id\", \"PaisDb\": \"D022_D030_Id\"})'", 'align'=>'right', 'value'=>'Buscar', 'reject'=>true),
		        	'D022_Endereco' => array('name' => 'D022_Endereco', 'label'=>'Endereço', 'type' => 'text','length' => array(30,40)),
		        	'D022_Complemento' => array('name' => 'D022_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(15,40)),
					'D022_Bairro' => array('name' => 'D022_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(30,40)),
					'D022_D018_Id' => array('name' => 'D022_D018_Id', 'label' => 'UF', 'extra' => "onchange=\"alteraEstadoTransportadoras(this, '{$g['divId']}')\"", 'type' => 'select', 
						'select' => gGeraSelect('D018', 'D018_Id', 'D018_UF', 'order by D018_UF', true)
					),
					'D022_D020_Id' => array('name' => 'D022_D020_Id', 'label' => 'Cidade', 'type' => 'select', 'extra' => 'style="width:190px;"', 
						'select' => gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'order by D020_Nome_Cidade', true)
					),
					'D022_D030_Id' => array('name' => 'D022_D030_Id', 'label' => 'País', 'type' => 'select', 'extra' => 'style="width:190px;"', 
						'select' => gGeraSelect('D030', 'D030_Id', 'D030_Codigo_Area,D030_Nome_Pais', '', false, 30)
					),
					'D022_DDD_Telefone_1' => array('name' => 'D022_DDD_Telefone_1', 'label'=>'Telefone 1', 'type' => 'text','length' => array(2,2)),
					'D022_Telefone_1' => array('name' => 'D022_Telefone_1', 'label'=>'', 'type' => 'text','length' => array(15,40), 'align'=>'right'),
					'D022_DDD_Telefone_2' => array('name' => 'D022_DDD_Telefone_2', 'label'=>'Telefone 2', 'type' => 'text','length' => array(2,2)),
					'D022_Telefone_2' => array('name' => 'D022_Telefone_2', 'label'=>'', 'type' => 'text','length' => array(15,40), 'align'=>'right'),
					'D022_DDD_Fax' => array('name' => 'D022_DDD_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(2,2)),
					'D022_Fax' => array('name' => 'D022_Fax', 'label'=>'', 'type' => 'text', 'align'=>'right'),
					'D022_Flag_Tipo_Etiqueta_Transporte' => array('name' => 'D022_Flag_Tipo_Etiqueta_Transporte', 'label'=>'Tipo Etiqueta Transporte', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Padrão do sistema', 'value' => '1'),
							array('title' => 'Controle Interno', 'value' => '2'),
							array('title' => 'Mercado Livre', 'value' => '3'),
							// array('title' => 'Definido pela transportadora', 'value' => 'T'),
						)
					),
					'D022A_Prioridade' => array('name' => 'D022A_Prioridade', 'label' => 'Prioridade', 'type' => 'text', 'align' => 'right', 'length' => array(5,10)),
					'D022A_Flag_Ativo' => array('name' => 'D022A_Flag_Ativo', 'label'=>'Ativo', 'type' => 'select', 'select'=> array(
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N'),
					)),
                ),
                'mais' => array(
					'D022_Descricao_Ecommerce' => array('name' => 'D022_Descricao_Ecommerce', 'label'=>'Descricao Ecommerce', 'type' => 'textarea', 'align'=>'right', 'uppercase' => false),
					'D022_Logo_Etiqueta' => array('name' => 'D022_Logo_Etiqueta', 'label'=>'Logo etiqueta', 'type' => 'textarea', 'align'=>'right', 'uppercase' => false),
					'D022_Url_Rastreio' => array('name' => 'D022_Url_Rastreio', 'label' => 'Url Rastreio', 'type' => 'text', 'uppercase' => false),
                ), 
                'frete' => array(
					'D022_Taxa_Frete'=>array('name'=>'D022_Taxa_Frete','label'=>'Percentual valor nota (%)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
					'D022_Valor_Frete_Peso'=>array('name'=>'D022_Valor_Frete_Peso','label'=>'Frete Kg (R$)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
					'D022_Valor_Frete_Cubagem'=>array('name'=>'D022_Valor_Frete_Cubagem','label'=>'Frete cubagem (Kg/m3)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
                	'D022_Valor_Frete_Minimo'=>array('name'=>'D022_Valor_Frete_Minimo','label'=>'Frete mínimo (R$)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
					'D022_Valor_Taxa_Pedagio'=>array('name'=>'D022_Valor_Taxa_Pedagio','label'=>'Pedagio (R$)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
					'D022_Valor_Fracao_Pedagio'=>array('name'=>'D022_Valor_Fracao_Pedagio','label'=>'Fração pedagio (Kg)','type'=>'moeda','casasDecimais' => 3, 'length' => array(5,10),'align' => 'right'),
                	'D022_Taxa_ICMS_Dentro_Estado'=>array('name'=>'D022_Taxa_ICMS_Dentro_Estado','label'=>'ICMS dentro estado (%)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
                	'D022_Taxa_ICMS_Fora_Estado'=>array('name'=>'D022_Taxa_ICMS_Fora_Estado','label'=>'ICMS fora estado (%)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
                	'D022_Taxa_GRIS'=>array('name'=>'D022_Taxa_GRIS','label'=>'GRIS (%)','type'=>'moeda','casasDecimais' => 2, 'length' => array(5,10),'align' => 'right'),
                ),    
            ) // 'campos'
		);
		$form->definirFormularioArray($formArray);
		
EOT;

		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		
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

	case 'form2':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D168';
		$form_campo_pk = 'D168_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad004/form/form2/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D168_Id_Transportadora_Ecommerce":{"top":7,"left":10.5,"width":86,"hidden":0},"D168_Nome_Transportadora_Ecommerce":{"top":30,"left":16.015625,"width":116,"hidden":0},"D168_D022_Id":{"top":53,"left":31.078125,"width":207,"hidden":0},"D168_D036_Id":{"top":76,"left":13.765625,"width":127,"hidden":0},"D168_Plataforma":{"top":99,"left":52.6875,"width":120,"hidden":0}}',
        );
        
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad_cad004_form2');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D168_Plataforma' => array('name' => 'D168_Plataforma', 'label'=>'Plataforma', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Intelipost', 'value' => 'INTELIPOST'),
							)
						),
		        	'D168_D036_Id' => array('name' => 'D168_D036_Id', 'label' => 'Tipo de Transporte', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
		        	'D168_D022_Id' => array('name' => 'D168_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Id,D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
		        	'D168_Id_Transportadora_Ecommerce' => array('name' => 'D168_Id_Transportadora_Ecommerce', 'label'=>'Código ecommerce', 'type' => 'text','length' => array(10,10)),
		        	'D168_Nome_Transportadora_Ecommerce' => array('name' => 'D168_Nome_Transportadora_Ecommerce', 'label'=>'Nome ecommerce', 'type' => 'text','length' => array(15,40)),
                ),    
            ) // 'campos'
		);
		$form->definirFormularioArray($formArray);
		
		
		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		
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
	
}

?>
