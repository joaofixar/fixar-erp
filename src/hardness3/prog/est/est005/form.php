<?php
namespace hardness;

require_once("prog/est/est005/form_func-js.php");
require_once("prog/est/est004/form_func-js.php");

/**
 * Ações
 */

 require_once('bibliotecas/classes/EST004.php');
 $EST004 = new EST004();
 require_once('bibliotecas/classes/EST005.php');
 $EST005 = new EST005();
 
switch ($g['r'][3]) {
	
	case 'est004FormProdutosNota':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$r_divReload = (isset($r_ven001FormProdutosForm)) ? $r_ven001FormProdutosForm['divReload'] : $r_divReload;

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
		$form_tabela = 'T004';
		$form_campo_pk = 'T004_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven001FormProdutosBtnAdd', 'ven001FormProdutosBtnCln', 'ven001FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormProdutos/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoNota',																			// callback js
			$g['divId'],																						// divId
			array($r_divReload, $acaoId)																		// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('est004FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(30,30), 'reject' => true),
					// Campos Hidden
					//'T004_T003_Id' => array('name' => 'T004_T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id),
					//'T004_C004_Id' => array('name' => 'T004_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven001FormProdutosBtnNew'] = array('name' => 'ven001FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['ven001FormProdutosBtnCln'] = array('name' => 'ven001FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['ven001FormProdutosBtnAdd'] = array('name' => 'ven001FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;
	
	case 'est005FormImportarXML':
	
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
		$form_tabela = 'T084';
		$form_campo_pk = 'T084';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est005FormImportarXML_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est005/form/est005FormImportarXML/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est005FormImportarXML');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoXMLEst005'] 		= array('name' => 'uploadArquivoXMLEst005', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['est005FormImportarXML_btn'] 		= array('name' => 'est005FormImportarXML_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T084_Arquivo_XML'] 				= array('name' => 'T084_Arquivo_XML', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoXMLEst005');
				$incluirItens= $form->campoValorEnviado('incluirItens');
				$retorno = $EST005->prepararImportarXML($arquivo,$incluirItens);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style="width:900px; height:400px; text-transform: none;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Conhecimento importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;

	case 'est004FormPagamentoNota':
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;	
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
		
		
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormPagamentosNotaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormPagamentoNota/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => 'A_COMBINAR'),
							array('title' => '1 Parcela', 'value' => '1'),
							array('title' => '2 Parcelas', 'value' => '2'),
							array('title' => '3 Parcelas', 'value' => '3'),
							array('title' => '4 Parcelas', 'value' => '4'),
							array('title' => '5 Parcelas', 'value' => '5'),
							array('title' => '6 Parcelas', 'value' => '6'),
							array('title' => '7 Parcelas', 'value' => '7'),
							array('title' => '8 Parcelas', 'value' => '8'),
							array('title' => '9 Parcelas', 'value' => '9'),
							array('title' => '10 Parcelas', 'value' => '10'),
							array('title' => '11 Parcelas', 'value' => '11'),
							array('title' => '12 Parcelas', 'value' => '12'),
							array('title' => '13 Parcelas', 'value' => '13'),
							array('title' => '14 Parcelas', 'value' => '14'),
							array('title' => '15 Parcelas', 'value' => '15'),
							array('title' => '16 Parcelas', 'value' => '16'),
							array('title' => '17 Parcelas', 'value' => '17'),
							array('title' => '18 Parcelas', 'value' => '18'),
							array('title' => '19 Parcelas', 'value' => '19'),
							array('title' => '20 Parcelas', 'value' => '20'),
							array('title' => '21 Parcelas', 'value' => '21'),
							array('title' => '22 Parcelas', 'value' => '22'),
							array('title' => '23 Parcelas', 'value' => '23'),
							array('title' => '24 Parcelas', 'value' => '24'),
						)
					),
					'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => 'Intervalo em Dias', 'type' => 'text', 'length' => array(4, 10)),
					'Portador_D027_Id' => array('name' => 'D027_Id', 'label' => 'Portador', 'type' => 'select', 'extra'=>'style="width:288px;"',  'select' => 
						gGeraSelect('D027', 'D027_Id', 'D027_Portador', 'order by D027_Portador', true)
					),
					'est004FormPagamentosNotaBtn' => array('name' => 'est004FormPagamentosNotaBtn', 'label' => '', 'value'=>'Gerar Parcelas', 'type' => 'submit', 'length' => array(4, 10)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'acaoId' => array('name' => 'acaoId', 'label' => 'Intervalo em Dias', 'value'=>$acaoId, 'type' => 'hidden', 'length' => array(4, 10)),
				)
			)
		);
        $form = new Formulario('formEst004FormPagamentosNota');
		$form->definirFormularioArray($formArray);
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
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $EST004->pagamentoNota($form, $acaoId);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       divRefresh("{$g['divId']}", true);
                                       divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                       divRefresh('{$g['divIdParent']}', true);
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
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
	break;
	
	case 'est005FormCadastroConhecimento':
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
		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est005FormCadastroConhecimento';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est005/form/est005FormCadastroConhecimento/';

		//form editor
		$form_editor = array('geral' =>'{"T084_D022_Id":{"top":7,"left":114.78125,"width":441,"hidden":0},"T084_D024_Id":{"top":30,"left":125.609375,"width":441,"hidden":0},"T084_D006_Id":{"top":53,"left":182.828125,"width":463,"hidden":0},"T084_D073_Id":{"top":76,"left":141.328125,"width":464,"hidden":0},"T084A_D014_Id":{"top":99,"left":14.421875,"width":442,"hidden":0},"T084_Numero_Conhecimento":{"top":122,"left":105.5,"width":120,"hidden":0},"T084_Valor_Base_PIS":{"top":122,"left":482.96875,"width":120,"hidden":0},"T084_Data_Lancamento":{"top":145,"left":132.09375,"width":120,"hidden":0},"T084_Aliquota_PIS":{"top":145,"left":494.6875,"width":120,"hidden":0},"T084_Data_Emissao":{"top":168,"left":150.9375,"width":120,"hidden":0},"T084_Valor_PIS":{"top":168,"left":510.015625,"width":120,"hidden":0},"T084_Data_Vencimento":{"top":191,"left":134.71875,"width":120,"hidden":0},"T084_Valor_Base_COFINS":{"top":191,"left":461.265625,"width":120,"hidden":0},"T084_Valor_Conhecimento":{"top":214,"left":119.359375,"width":120,"hidden":0},"T084_Aliquota_COFINS":{"top":214,"left":472.984375,"width":120,"hidden":0},"T084_Valor_Base_ICMS":{"top":237,"left":137.140625,"width":120,"hidden":0},"T084_Valor_COFINS":{"top":237,"left":488.3125,"width":120,"hidden":0},"T084_Aliquota_ICMS":{"top":260,"left":148.875,"width":120,"hidden":0},"T084_Valor_ICMS":{"top":283,"left":164.203125,"width":120,"hidden":0},"T084_Valor_ICMS_ST":{"top":306,"left":148.390625,"width":120,"hidden":0},"T084_Flag_Fechado":{"top":306,"left":696.546875,"width":59,"hidden":1},"T084_Indicador_Tipo_Frete":{"top":329,"left":144.5,"width":464,"hidden":0},"T084_Situacao":{"top":352,"left":175.75,"width":463,"hidden":0},"T084_Indicador_Natureza_Frete":{"top":375,"left":131.5,"width":463,"hidden":0},"T084_Base_Calculo_Credito":{"top":398,"left":102.25,"width":463,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T084_D022_Id' => array('name' => 'T084_D022_Id', 'label' => 'CNPJ Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022', 'D022_Id', 'D022_Cnpj,D022_Nome_Empresa,D022_Id'), 'length' => array(50,30)),
					'T084_D024_Id' => array('name' => 'T084_D024_Id', 'label' => 'Fornecedor/Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Cnpj,D024_Id','WHERE D024_Flag_Ativo="S"'), 'length' => array(50,30)),
					'T084_Numero_Conhecimento' => array('name' => 'T084_Numero_Conhecimento', 'label' => 'Número Conhecimento', 'type' => 'text', 'length' => array(20,30)),
					'T084_Data_Lancamento' => array('name' => 'T084_Data_Lancamento', 'label' => 'Data Lançamento', 'type' => 'data', 'length' => array(15,20)),
					'T084_Data_Emissao' => array('name' => 'T084_Data_Emissao', 'label' => 'Data Emissão', 'type' => 'data', 'length' => array(15,20)),
					'T084_Data_Vencimento' => array('name' => 'T084_Data_Vencimento', 'label' => 'Data Vencimento', 'type' => 'data', 'length' => array(15,20)),
					'T084_Valor_Conhecimento' => array('name' => 'T084_Valor_Conhecimento', 'label' => 'Valor Conhecimento', 'type' => 'moeda', 'length' => array(20,10)),
					'T084_Valor_Base_ICMS' => array('name' => 'T084_Valor_Base_ICMS', 'label' => 'Valor Base ICMS', 'type' => 'moeda', 'length' => array(20,10)),
					'T084_Aliquota_ICMS' => array('name' => 'T084_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type' => 'moeda', 'length' => array(20,10)),
					'T084_Valor_ICMS' => array('name' => 'T084_Valor_ICMS', 'label' => 'Valor ICMS', 'type' => 'moeda', 'length' => array(20,10)),
					'T084_Valor_ICMS_ST' => array('name' => 'T084_Valor_ICMS_ST', 'label' => 'Valor ICMS ST', 'type' => 'moeda', 'length' => array(20,10)),
					'T084_D006_Id' => array('name' => 'T084_D006_Id', 'label' => 'C.F.O.P.', 'type' => 'select', 'select' =>
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo <> "S" ORDER BY D006_Codigo_CFOP,D006_Descricao', true),
						'extra' => "onchange=\"est005PopulaD073('{$g['divId']}', this); return false;\""
					),
					'T084_D073_Id' => array('name' => 'T084_D073_Id', 'label' => 'Centro de custo', 'type' => 'select', 'select' =>
						gGeraSelect('D073', 'D073_Id', 'D073_Nome', 'WHERE D073_Flag_Ativo = "S"', true)
					),
					//'T084_D073_Id' => array('name' => 'T084_D073_Id', 'label'=>'Centro de Custos', 'type' => 'pesquisa', 'pesquisa' => array('D073','D073_Id','D073_Nome')),
					'T084_Cnpj_Transportadora' => array('name' => 'T084_Cnpj_Transportadora', 'label' => 'T084_Cnpj_Transportadora', 'type' => 'hidden'),
					'T084_C004_Id' => array('name' => 'T084_C004_Id', 'label' => 'T084_Cnpj_Transportadora', 'value'=>$g['empresaAtual'], 'type' => 'hidden'),
					'T084_C007_Id' => array('name' => 'T084_C007_Id', 'label' => 'T084_Cnpj_Transportadora', 'value'=>$g['usuarioAtual'], 'type' => 'hidden'),
					'T084_Flag_Fechado' => array('name' => 'T084_Flag_Fechado', 'label' => 'Finalizado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'T084_Valor_Base_PIS' => array('name' => 'T084_Valor_Base_PIS', 'label' => 'Valor Base PIS', 'type' => 'moeda'),
					'T084_Valor_Base_COFINS' => array('name' => 'T084_Valor_Base_COFINS', 'label' => 'Valor Base COFINS', 'type' => 'moeda'),
					'T084_Aliquota_PIS' => array('name' => 'T084_Aliquota_PIS', 'label' => 'Alíquota PIS', 'type' => 'moeda'),
					'T084_Aliquota_COFINS' => array('name' => 'T084_Aliquota_COFINS', 'label' => 'Alíquota COFINS', 'type' => 'moeda'),
					'T084_Valor_PIS' => array('name' => 'T084_Valor_PIS', 'label' => 'Valor PIS', 'type' => 'moeda'),
					'T084_Valor_COFINS' => array('name' => 'T084_Valor_COFINS', 'label' => 'Valor COFINS', 'type' => 'moeda'),			
					'T084_Situacao' => array('name' => 'T084_Situacao', 'label' => 'Situação', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Documento regular', 'value' => '00'),
							array('title' => 'Escrituração extemporânea de documento regular', 'value' => '01'),
							array('title' => 'Documento cancelado', 'value' => '02'),
							array('title' => 'Escrituração extemporânea de documento cancelado', 'value' => '03'),
							array('title' => 'NF-e, NFC-e ou CT-e denegado', 'value' => '04'),
							array('title' => 'NF-e, NFC-e ou CT-e - Numeração inutilizada', 'value' => '05'),
							array('title' => 'Documento Fiscal Complementar', 'value' => '06'),
							array('title' => 'Escrituração extemporânea de documento complementar', 'value' => '07'),
							array('title' => 'Documento Fiscal emitido com base em Regime Especial ou Norma Específica', 'value' => '08'),
						)
					),		
					'T084_Indicador_Tipo_Frete' => array('name' => 'T084_Indicador_Tipo_Frete', 'label' => 'Frete por conta', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Por conta do emitente', 'value' => '0'),
							array('title' => 'Por conta do destinatário/remetente', 'value' => '1'),
							array('title' => 'Por conta de terceiros', 'value' => '2'),
							array('title' => 'Sem cobrança de frete', 'value' => '9'),
						)
					),		
					'T084_Indicador_Natureza_Frete' => array('name' => 'T084_Indicador_Natureza_Frete', 'label' => 'Natureza do Frete', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Operações de vendas, com ônus suportado pelo estabelecimento vendedor', 'value' => '0'),
							array('title' => '1 - Operações de vendas, com ônus suportado pelo adquirente', 'value' => '1'),
							array('title' => '2 - Operações de compras (bens para revenda, matériasprima e outros produtos, geradores de crédito)', 'value' => '2'),
							array('title' => '3 - Operações de compras (bens para revenda, matériasprima e outros produtos, não geradores de crédito)', 'value' => '3'),
							array('title' => '4 - Transferência de produtos acabados entre estabelecimentos da pessoa jurídica', 'value' => '4'),
							array('title' => '5 - Transferência de produtos em elaboração entre estabelecimentos da pessoa jurídic', 'value' => '5'),
							array('title' => '9 - Outras', 'value' => '9'),
						)
					),	
					'T084_Base_Calculo_Credito' => array('name' => 'T084_Base_Calculo_Credito', 'label' => 'Base de Calculo Crédito', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01 - Aquisição de bens para revenda', 'value' => '01'), 
							array('title' => '02 - Aquisição de bens utilizados como insumo', 'value' => '02'), 
							array('title' => '03 - Aquisição de serviços utilizados como insumo', 'value' => '03'), 
							array('title' => '04 - Energia elétrica e térmica, inclusive sob a forma de vapor', 'value' => '04'), 
							array('title' => '05 - Aluguéis de prédios', 'value' => '05'), 
							array('title' => '06 - Aluguéis de máquinas e equipamentos', 'value' => '06'), 
							array('title' => '07 - Armazenagem de mercadoria e frete na operação de venda', 'value' => '07'), 
							array('title' => '08 - Contraprestações de arrendamento mercantil', 'value' => '08'), 
							array('title' => '09 - Máquinas, equipamentos e outros bens incorporados ao ativo imobilizado (crédito sobre encargos de depreciação).', 'value' => '09'), 
							array('title' => '10 - Máquinas, equipamentos e outros bens incorporados ao ativo imobilizado (crédito com base no valor de aquisição).', 'value' => '10'), 
							array('title' => '11 - Amortização e Depreciação de edificações e benfeitorias em imóveis', 'value' => '11'), 
							array('title' => '12 - Devolução de Vendas Sujeitas à Incidência Não-Cumulativa', 'value' => '12'), 
							array('title' => '13 - Outras Operações com Direito a Crédito (inclusive os créditos presumidos sobre receitas)', 'value' => '13'), 
							array('title' => '14 - Atividade de Transporte de Cargas – Subcontratação', 'value' => '14'), 
							array('title' => '15 - Atividade Imobiliária – Custo Incorrido de Unidade Imobiliária', 'value' => '15'), 
							array('title' => '16 - Atividade Imobiliária – Custo Orçado de unidade não concluída', 'value' => '16'), 
							array('title' => '17 - Atividade de Prestação de Serviços de Limpeza, Conservação e Manutenção – vale-transporte, vale-refeição ou vale-alimentação, fardamento ou uniforme.', 'value' => '17'), 
							array('title' => '18 - Estoque de abertura de bens', 'value' => '18'), 						
						)
					),
					'T084A_D014_Id' => array('name' => 'T084A_D014_Id','label'=>'Conta (Deixar em branco p/usar da CFOP)', 'type' => 'pesquisa', 'pesquisa' => array('D014','D014_Id',"D014_SubConta,D032_Descricao,D154_Descricao", "LEFT JOIN D154 ON D154_Id=D014_D154_Id LEFT JOIN D032 ON D032_Id = D014_D032_Id"), 'length' => array(40,30)),
				)
			)
		);
        $form = new Formulario('formest005FormConhecimentos');
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
					$T084_Flag_Fechado = $form->campoValorEnviado('T084_Flag_Fechado');
					$T085=mysql_query("select T085_Id from T085 WHERE T085_T084_Id='{$r_acaoId}' and T085_Flag_Fechado='N'");
					if($T084_Flag_Fechado=='S' && mysql_num_rows($T085) > 0){
							echo gGeraAlertMsg('Erro!', 'Não é possível finalizar o conhecimento, pois existem notas relacionadas que não foram lançadas.');
					}else{
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
						
					}
				} else {
					$D022_Id = $form->campoValorEnviado('T084_D022_Id');
					$T084_Numero_Conhecimento = $form->campoValorEnviado('T084_Numero_Conhecimento');
					// Verifica se já existe conhecimento com mesmo número
					$T084 = mysql_query("select T084_Id from T084 where T084_D022_Id='$D022_Id' and T084_Numero_Conhecimento='$T084_Numero_Conhecimento' and T084_C004_Id='{$g['empresaAtual']}';");
					if (mysql_num_rows($T084)>0){
						$alert = gGeraAlertMsg('Erro!', 'Este conhecimento já foi lançado! Favor verificar.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
							} </script></div>
EOT;
					} else {
						// Duplica informação CNPJ
						$sql = "SELECT D022_Cnpj FROM D022 WHERE D022_Id = '{$D022_Id}';";
						$resultado = mysql_query($sql);
						$resultado = mysql_fetch_assoc($resultado);
						$form->campoSetarValor('T084_Cnpj_Transportadora', $resultado['D022_Cnpj'], true); 
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) { 
							// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
							$T084A_Id = $g['mysqlLastId'];
							$T084A = mysql_query("SELECT T084A_T084_Id FROM T084A WHERE T084A_Id = '{$T084A_Id}'");
							$mT084A = mysql_fetch_array($T084A);

							$acaoId = $mT084A['T084A_T084_Id'];

							$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Conhecimento - ID: ' + '{$acaoId}', '/est/est005/content/est005ContentEditarConhecimento/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'));
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
		$sqlT015 = mysql_query("SELECT * FROM T015 WHERE T015_T084_Id = '{$r_acaoId}'");
		$titulosGerados = '';
		if(mysql_num_rows($sqlT015) > 0){
			$T015 = mysql_fetch_array($sqlT015);
			$T015['T015_Data_Emissao'] = gCorrigeData($T015['T015_Data_Emissao']);
			$titulosGerados = "Títulos gerados em: {$T015['T015_Data_Emissao']}";
		}
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if(!empty($r_acaoId)){
			$botoesEsq .= '<button type="submit" id="est005GerarContasAPagar">Gerar Contas a Pagar</button> ';
			$botoesEsq .= '<button type="submit" id="est005AtualizarAliquotas">Atualizar PIS e COFINS</button> '.$titulosGerados;
		}
		$botoesDir .= '<button type="submit" id="est005FormCadastroConhecimento">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est005GerarContasAPagar').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est005/form_func-ajax/gerarContasAPagar/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}', true);
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atenção', 'Deseja gerar os títulos referentes as contas a pagar? ', \$buttons);
				});
				$('#est005AtualizarAliquotas').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est005/form_func-ajax/atualizaAliquotas/?ajax=true&T084_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Ok', request.data, \$buttons);
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    		dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
								divRefresh('{$g['divId']}', true);
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm('Atenção', 'Deseja atualizar alíquotas? ', \$buttons);
				});
			} </script></div>
EOT;
	break;
	
	case 'est005FormCadastroNotasRelacionadas':
		
		$T084_Id =  (isset($r_T084_Id)) ? $r_T084_Id : false;
		
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
		$form_tabela = 'T085';
		$form_campo_pk = 'T085_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est005FormCadastroNotasRelacionadasBtnAdd', 'est005FormCadastroNotasRelacionadasBtnCln', 'est005FormCadastroNotasRelacionadasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est005/form/est005FormCadastroNotasRelacionadas/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T085_Numero_Nota_Fiscal' => array('name' => 'T084_Numero_Nota_Fiscal', 'label' => 'Número NF', 'type' => 'text', 'length' => array(60,90)),
					'T085_Valor_Total_Nota' => array('name' => 'T084_Valor_Total_Nota', 'label' => 'Valor Total NF', 'type' => 'moeda', 'length' => array(60,90)),
					'T085_T084_Id' => array('name' => 'T085_T084_Id', 'label' => 'Valor Total NF', 'type' => 'hidden', 'value'=>$T084_Id, 'length' => array(60,90)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T085_Flag_Tipo_Nota' => array('name' => 'T085_Flag_Tipo_Nota', 'label' => 'Tipo nota', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Entrada', 'value' => 'E'),
							array('title' => 'Saida', 'value' => 'S'),
						)
					),
				)
			)
		);
        $form = new Formulario('formest005FormNotasRelacionadas');
		
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est005FormCadastroNotasRelacionadasBtnNew'] = array('name' => 'est005FormCadastroNotasRelacionadasBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est005FormCadastroNotasRelacionadasBtnCln'] = array('name' => 'est005FormCadastroNotasRelacionadasBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est005FormCadastroNotasRelacionadasBtnAdd'] = array('name' => 'est005FormCadastroNotasRelacionadasBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
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
								//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					$T085_Flag_Tipo_Nota = $form->campoValorEnviado('T085_Flag_Tipo_Nota');
					//Se a nota vinculada pelo form for de saida  
					if($T085_Flag_Tipo_Nota == 'S'){
						$T085_Numero_Nota_Fiscal = $form->campoValorEnviado('T085_Numero_Nota_Fiscal');
						$cClausula   = "SELECT T007_Id from T007 where T007_Numero_Nota_Fiscal='{$T085_Numero_Nota_Fiscal}' and T007_Flag_Cancelada='N' and T007_C004_Id='{$g['empresaAtual']}'";
						$cSql_T007 = mysql_query($cClausula);
						$mT007     = mysql_fetch_array($cSql_T007);
						if (mysql_num_rows($cSql_T007) <= 0) {
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										dialogConfirm("Erro!", "Nota não encontrada!");
									} </script></div>
EOT;
						} else {
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
								$acaoId = $g['mysqlLastId'];
								$cClausula = mysql_query("Update T085 set T085_T007_Id='{$mT007['T007_Id']}',T085_Flag_Fechado='S' where T085_Id='{$acaoId}'");
								$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									} </script></div>
EOT;
							}
						}
					} else {
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
								$acaoId = $g['mysqlLastId'];
								$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="est005FormCadastroNotasRelacionadas">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>