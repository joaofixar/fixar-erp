<?php
namespace hardness;

require_once("bibliotecas/classes/CRM005.php");
$CRM005 = new CRM005();

require_once('prog/crm/crm005/form_func-js.php');

switch ($g['r'][3]) {

	case 'crm005FormProdutos':
		
		echo '<div class="ui-widget-header" style="width: 99%; height: 1px; border: 0; padding: 0; margin: 2px 0 2px 4px;"></div>';
		
		$data = (isset($r_data)) ? $r_data : false;
		
		if(!empty($data)) {	
			$data = gCorrigeDataInverte($data);
		} else {
			$data = date('Y-m-d');
		}
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
		$form_tabela = 'T099';
		$form_campo_pk = 'T099_Id';
		$form_parent = 'T003';
		$form_parent_valor = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('crm005FormProdutosBtnAdd', 'crm005FormProdutosBtnCln', 'crm005FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm005/form/crm005FormProdutos/';
        
        //form editor
        $form_editor = array(
            'produto' => '{"crm005FormProdutosBtnAdd":{"top":5,"left":944,"width":null,"hidden":0},"crm005FormProdutosBtnCln":{"top":5,"left":1008,"width":null,"hidden":0},"Procura_Produto":{"top":7,"left":5,"width":296,"hidden":0},"T099_Quantidade":{"top":7,"left":387,"width":65,"hidden":0},"T099_D037_Id":{"top":7,"left":531,"width":45,"hidden":0},"T099_Valor_Preco_Unitario":{"top":7,"left":631,"width":75,"hidden":0},"T099_Horario_Entrega":{"top":7,"left":795,"width":45,"hidden":0}}',
        );
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S' AND D049_Flag_Ativo != 'N')
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Preco_1,D009_Preco_2,D009_Preco_3,D009_Quantidade_Estoque_Liquido,D001_Descricao_Ingles,D009_Id',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição,Marca,Preço 1,Preço 2,Preço 3,Estoque,Descrição Inglês,Link',														// descrição dos campos
			'crm005CampoProcuraProduto',																					// callback js
			$g['divId'],																									// divId
			$r_D024_Id,					// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array(),
				array('callback' => 'campoLinkProdutoPesquisaGrid')
			)
		);
		// novo formulário
		$form = new Formulario('crm005FormProdutosForm');
		
		$Todos = array('title' => '...', 'value' => '');
		$D037_Id = gGeraSelect('D037','D037_Id','D037_Unidade',"");
		array_unshift($D037_Id, $Todos);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'Procura_ProdutoPrePedido' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,110), 'reject' => true),
					'T099_Quantidade' => array('name' => 'T099_Quantidade','label'=>'Quantidade', 'type' => 'text', 'length' => array(8,60), 'align' => 'right'),
					'T099_D037_Id' => array('name' => 'T099_D037_Id', 'label' => 'Unidade', 'type' => 'select',	
						'select' => $D037_Id, 'align' => 'right'
					),
					'T099_Valor_Preco_Unitario' => array('name' => 'T099_Valor_Preco_Unitario','label'=> 'Preço Unitario', 'type' => 'moeda', 'length' => array(10,60), 'align' => 'right'),
					'T099_Horario_Entrega' => array('name' => 'T099_Horario_Entrega','label'=> 'Hora entrega', 'type' => 'tempo', 'length' => array(4,60), 'align' => 'right'),
					'T099_D009_Id' => array('name' => 'T099_D009_Id','label'=> 'D009_Id', 'type' => 'hidden', 'length' => array(15,60), 'align' => 'right'),
					'T099_D024_Id' => array('name' => 'T099_D024_Id', 'label'=>'D024_Id','type'=>'hidden', 'value' => @$r_D024_Id, 'align' => 'right'),
					'T099_Data' => array('name' => 'T099_Data', 'label'=> 'Data', 'type'=>'hidden', 'value' => $data, 'align' => 'right'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['crm005FormProdutosBtnNew'] = array('name' => 'crm005FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['crm005FormProdutosBtnAdd'] = array('name' => 'crm005FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'reject' => true, 'align' => 'right');
		$formArray['campos']['produto']['crm005FormProdutosBtnCln'] = array('name' => 'crm005FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'reject' => true);
		//$formArray['campos']['produto']['crm005BtnCadastrarProduto'] = array('name' => 'crm005BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#crm005BtnCadastrarProduto').unbind('click');
				$('#crm005BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,300]);											
					return false;
				});
				if (!isempty('{$r_D024_Id}')) {
					setTimeout(function() {
						$('#Procura_ProdutoPrePedido_Pesquisa').focus();
					}, 200);
				}
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $CRM005->incluirProduto($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divReload}', true);
								divRefresh('{$g['divId']}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;
	
	case 'crm005FormProdutosCRM':
		
		echo '<div class="ui-widget-header" style="width: 99%; height: 1px; border: 0; padding: 0; margin: 2px 0 2px 4px;"></div>';
		
		$data = (isset($r_data)) ? $r_data : false;
		
		if(!empty($data)) {	
			$data = gCorrigeDataInverte($data);
		} else {
			$data = date('Y-m-d');
		}
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
		$form_tabela = 'T099';
		$form_campo_pk = 'T099_Id';
		$form_parent = 'T003';
		$form_parent_valor = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('crm005FormProdutosCRMBtnAdd', 'crm005FormProdutosCRMBtnCln', 'crm005FormProdutosCRMBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm005/form/crm005FormProdutosCRM/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S' AND D049_Flag_Ativo != 'N')
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																																	// tabela
			'D009_Id',																																// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D041_Valor_Base,D009_Quantidade_Estoque_Liquido,D001_Descricao_Ingles,D009_Id',	// campos
			$pesquisaGridExtra,																														// extras sql
			'Pesquisa de Produto',																													// título da janela
			'Código,Descrição, Marca,Preço,Estoque,Descrição Inglês,Link',																			// descrição dos campos
			'crm005CampoProcuraProdutoCRM',																											// callback js
			$g['divId'],																															// divId
			'',																																		// parametro extra
			array(																																	// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array(),
				array('callback' => 'campoLinkProdutoPesquisaGrid')
			)
		);
		// novo formulário
		$form = new Formulario('crm005FormProdutosCRM');
		
		$Todos = array('title' => '...', 'value' => '');
		$D037_Id = gGeraSelect('D037','D037_Id','D037_Unidade',"");
		array_unshift($D037_Id, $Todos);
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'Procura_ProdutoCRM' => array('name' => 'Procura_ProdutoCRM' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,110), 'reject' => true),
					'T099_Quantidade' => array('name' => 'T099_Quantidade','label'=>'Quantidade', 'type' => 'text', 'length' => array(8,60), 'align' => 'right'),
					'T099_D037_Id' => array('name' => 'T099_D037_Id', 'label' => 'Unidade', 'type' => 'select',	
						'select' => $D037_Id, 'align' => 'right'
					),
					'T099_Valor_Preco_Unitario' => array('name' => 'T099_Valor_Preco_Unitario','label'=> 'Preço Unitario', 'type' => 'moeda', 'length' => array(10,60), 'align' => 'right'),
					'T099_Horario_Entrega' => array('name' => 'T099_Horario_Entrega','label'=> 'Hora entrega', 'type' => 'tempo', 'length' => array(4,60), 'align' => 'right'),
					'T099_D009_Id' => array('name' => 'T099_D009_Id','label'=> 'D009_Id', 'type' => 'hidden', 'length' => array(15,60), 'align' => 'right'),
					'T099_D024_Id' => array('name' => 'T099_D024_Id', 'label'=>'D024_Id','type'=>'hidden', 'value' => @$r_D024_Id, 'align' => 'right'),
					'T099_Data' => array('name' => 'T099_Data', 'label'=> 'Data', 'type'=>'hidden', 'value' => $data, 'align' => 'right'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['crm005FormProdutosCRMBtnNew'] = array('name' => 'crm005FormProdutosCRMBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['crm005FormProdutosCRMBtnAdd'] = array('name' => 'crm005FormProdutosCRMBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'reject' => true, 'align' => 'right');
		$formArray['campos']['produto']['crm005FormProdutosCRMBtnCln'] = array('name' => 'crm005FormProdutosCRMBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'reject' => true);
		//$formArray['campos']['produto']['crm005BtnCadastrarProduto'] = array('name' => 'crm005BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#crm005BtnCadastrarProduto').unbind('click');
				$('#crm005BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,300]);											
					return false;
				});
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $CRM005->incluirProduto($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divReload}', true);
								divRefresh('{$g['divId']}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

	break;


	case 'crm005FormSelecionarVendedor':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$data = isset($r_data) ? $r_data : false;
		$D024_Id = isset($r_D024_Id) ? $r_D024_Id : null;
		$Flag_ACP = isset($r_Flag_ACP) ? $r_Flag_ACP : null;
		
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'crm005FormSelecionarVendedor_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/crm/crm005/form/crm005FormSelecionarVendedor/';
		
		//$Todos = array('title' => '...', 'value' => '');
		//$D037_Id = 
		//array_unshift($D037_Id, $Todos);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('crm005FormSelecionarVendedor');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'label' => 'Confirmar rota', 'type' => 'select',
						'select' => gGeraSelect("C007", "C007_Id", "C007_Nome","WHERE C007_Flag_Vendedor='V'", true)
					),
				),
            ) // 'campos'
		);
		
		// Botão Limpar / Novo Registro
		$form->definirFormularioArray($formArray);
		
		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				if (!empty($r_acaoId)) {

					$D024_C007_Vendedor_Interno = $form->campoValorEnviado('D024_C007_Vendedor_Interno');

					if(empty($D024_C007_Vendedor_Interno)){
						
						echo gGeraAlertMsg('Erro!', 'Selecione um Vendedor!');

					} else if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						$C007_Id = $form->campoValorEnviado('C007_Id');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("<iframe></iframe>")
									.attr({
										'src': '../hardness/pre_pedido/gerar_pedido_3.php?Data={$data}&D024_Id={$D024_Id}&Flag_ACP={$Flag_ACP}&C007_Id={$C007_Id}'
									})
									.css({
										'display': 'none'
									})
									.appendTo('#{$g['divId']}'
								);
							} </script></div>
EOT;
						//echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
					}
				}
			}
		}else{

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
		$g['smarty']->display('form.tpl');
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="crm005FormSelecionarVendedor_btn">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
}
?>
