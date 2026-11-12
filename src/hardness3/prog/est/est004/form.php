<?php
namespace hardness;

/**
 * Ações
 */

require_once('bibliotecas/classes/EST004.php');
$EST004 = new EST004();
 
switch ($g['r'][3]) {
	
	case 'est004FormProdutosNota':
		require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$r_divReload = (isset($r_ven001FormProdutosForm)) ? $r_ven001FormProdutosForm['divReload'] : $r_divReload;
		$idD009 = (isset($r_idD009)) ? $r_idD009 : false;

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
		$form_tabela = 'T014';
		$form_campo_pk = 'T014_Id';
		$form_parent = 'T013_Id';
		$form_parent_valor = $r_T013_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est004FormProdutosNotaBtnAdd','est004FormProdutosNotaBtnCln','est004FormProdutosNotaBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormProdutosNota/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			AND
			D001_Flag_Ativo='S')
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Descrição Inglês,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoNota',																			// callback js
			$g['divId'],																						// divId
			array($r_divReload, $r_T013_Id)																		// parametro extra
		);

		$T013=mysql_query("SELECT T013_Flag_Travar_Nota FROM T013 WHERE T013_Id='{$r_T013_Id}'");
		$mT013=mysql_fetch_array($T013);

		$disabled=false;
		if($mT013['T013_Flag_Travar_Nota']=='S'){
			$disabled=true;
		}

		$form_editor = array(
			'geral' => '{"Descricao_Produto":{"top":30,"left":25.71875,"width":328,"hidden":0},"Procura_Produto":{"top":7,"left":35.75,"width":314,"hidden":0},"T014_Quantidade":{"top":53,"left":18.4375,"width":136,"hidden":0},"T014_Valor_Custo_Unitario":{"top":53,"left":230.171875,"width":120,"hidden":0},"T014_D006_Id":{"top":76,"left":46.859375,"width":335,"hidden":0},"T014_T066_Id":{"top":99,"left":32.65625,"width":335,"hidden":0},"est004FormProdutosNotaBtnCln":{"top":120,"left":288,"width":null,"hidden":0},"est004FormProdutosNotaBtnAdd":{"top":120,"left":352,"width":null,"hidden":0}}'
		);		
		// novo formulário
		$form = new Formulario('est004FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(90,90), 'reject' => true, 'disabled'=>$disabled, 
						'extra' => "placeholder = 'BUSCAR'"),
					'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'reject' => true,'readonly'=>true),
					'T014_Quantidade' => array('name' => 'T014_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda','disabled'=>$disabled),
					'T014_Valor_Custo_Unitario' => array('name' => 'T014_Valor_Custo_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda','disabled'=>$disabled),					
					'T014_D006_Id' => array('name' => 'T014_D006_Id', 'label' => 'CFOP', 'type' => 'select', 'select' => 
						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Venda_Compra_Outros != "V" ORDER BY D006_Codigo_CFOP','...',false)
					,'disabled'=>$disabled),					
					'T014_D009_Id' => array('name' => 'T014_D009_Id', 'label' => 'T014_D009_Id', 'type' => 'hidden','disabled'=>$disabled),	
					'T014_T066_Id' => array('name' => 'T014_T066_Id', 'label' => 'Locação', 'type' => 'select', 
						'select' => gGeraSelect('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$idD009}' ORDER BY D004_Local", true),
						'selectRefresh' => gGeraSelect('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D009_Id = '{$idD009}' ORDER BY D004_Local", true),'disabled'=>$disabled
					),									
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['geral']['est004FormProdutosNotaBtnNew'] = array('name' => 'est004FormProdutosNotaBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est004FormProdutosNotaBtnAdd'] = array('name' => 'est004FormProdutosNotaBtnAdd', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true,'disabled'=>$disabled);		
		$formArray['campos']['geral']['est004FormProdutosNotaBtnCln'] = array('name' => 'est004FormProdutosNotaBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'reject' => true,'disabled'=>$disabled);
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
				log("CHEGOU AQUI SEM ERROS");
				$retorno = $EST004->incluirProduto($form,$r_T013_Id);				
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {							
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true)
								divRefresh('{$form->campoValorEnviado('divReload')}');																
							} </script></div>
EOT;				$form->limpar();					
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

		//form editor
		$form_editor = array(
			'geral' => '{"Total_Parcelas":{"top":7,"left":48,"width":94,"hidden":0},"est004FormPagamentosNotaBtn":{"top":51,"left":272,"width":null,"hidden":0},"IntervalorDias":{"top":30,"left":8,"width":45,"hidden":0},"Portador_D031_Id":{"top":53,"left":49,"width":153,"hidden":0}}' , 
		);

		$select = array();
        $select[] = array('value' => '0', 'title' => 'A COMBINAR');
        for ($i=1; $i<=60; $i++) {
            if($i=='1'){
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas"), 'selected'=>true);
            }else{
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas"));
            }
        }
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'reject'=>true),
					'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => 'Intervalo em Dias', 'type' => 'text', 'length' => array(4, 10)),
					'Portador_D031_Id' => array('name' => 'D031_Id', 'label' => 'Portador', 'type' => 'select', 'select' => 
						gGeraSelect('D031', 'D031_Id', 'D031_Portador', ' order by D031_Portador', true)
					),
					'est004FormPagamentosNotaBtn' => array('name' => 'est004FormPagamentosNotaBtn', 'label' => '','align' => 'right', 'value'=>'Gerar Parcelas', 'type' => 'submit', 'length' => array(4, 10), 'reload' => false, 'disabled' => isset($g['c029Ids'][189]) ? false : true, 'style' => isset($g['c029Ids'][189]) ? '' : 'cursor: no-drop;'),
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
                                       divRefresh('{$r_divReload}', true);
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
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'est004FormImportarXML':
	
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormImportarXML_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormImportarXML/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form_editor = array(
			'geral' => '{"uploadArquivoXMLEst004":{"top":7,"left":38.71875,"width":245,"hidden":0},"est004FormImportarXML_btn":{"top":5,"left":400,"width":null,"hidden":0},"incluirItens":{"top":31,"left":-1.625,"width":13,"hidden":0},"cadastrarCFOP":{"top":53,"left":39.96875,"width":13,"hidden":1}}');
		$form = new Formulario('est004FormImportarXML');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoXMLEst004'] 		= array('name' => 'uploadArquivoXMLEst004', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['est004FormImportarXML_btn'] 		= array('name' => 'est004FormImportarXML_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T007_Arquivo_XML'] 				= array('name' => 'T007_Arquivo_XML', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		$formArray['campos']['geral']['incluirItens']					= array('name' => 'incluirItens', 'label' => 'Importar os ítens do XML', 'type' => 'checkbox', 'checked'=>'checked', 'reject' => true);
		$formArray['campos']['geral']['cadastrarCFOP']					= array('name' => 'cadastrarCFOP', 'label' => 'Cadastrar CFOP', 'type' => 'checkbox', 'checked'=> false);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoXMLEst004');
				$incluirItens= $form->campoValorEnviado('incluirItens');
				$cadastrarCFOP = $form->campoValorEnviado('cadastrarCFOP');
				$retorno = $EST004->prepararImportarXML($arquivo,$incluirItens, $cadastrarCFOP);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style="width:900px; height:400px; text-transform: none;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Nota Importada com Sucesso!');
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
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'est004FormCadastroNota':
        $GerarNFOC = (isset($r_GerarNFOC)) ? $r_GerarNFOC : false;
        $extra = (isset($r_extra)) ? $r_extra : false;
        
		require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            function preencheCamposFrete(elem, divId) {
                $('#' + divId).showLoading();
                $.getJSON('/est/est004/form_func-ajax/preencheCamposFrete/?ajax=true&T084_Numero_Conhecimento=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
                        $('#' + divId).hideLoading();
		                if (request.code) {
			                $('#T013_D022_Id_Pesquisa').val(request.data['D022_Nome_Empresa']);
			                $('#T013_D022_Id').val(request.data['D022_Id']);
			                $('#T013_Tipo_Transporte').val(request.data['D036_Tipo_Transporte']);
			                $('#T013_Valor_Frete').val(request.data['T084_Valor_Conhecimento']);
			                reloadUI('#' . divId);
		                } else {
			                var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    		                dialogConfirm("Erro!", request.data, buttons)
		                }
	                });
                }
            } </script></div>
EOT;
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_est004FormCadastroMarca)) ? $r_est004FormCadastroMarca['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T013';
		$form_campo_pk = 'T013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormCadastroNota_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormCadastroNota/';

        //Form Editor
        $form_editor = array(
            'principal' => '{"T013_D006_Id":{"top":282.99632263183594,"left":126.86886644363403,"width":449,"hidden":1},"T013_D024_Id":{"top":6.98529052734375,"left":44.62316179275513,"width":458.039216,"hidden":0},"T013C004Id":{"top":0,"left":0,"width":7.960784,"hidden":1},"T013_Tipo":{"top":29.993865966796875,"left":132.91972398757935,"width":143,"hidden":0},"T013_Flag_Finalidade_NFe":{"top":29.993865966796875,"left":344.24018907546997,"width":239,"hidden":0},"T013_T075_Id":{"top":0,"left":0,"width":7.960784,"hidden":1},"T013_Numero_Nota_Fiscal":{"top":52.97181701660156,"left":57.38357973098755,"width":136.039216,"hidden":0},"T013_Serie":{"top":0,"left":0,"width":135.778,"hidden":0},"T013_Data_Emissao":{"top":75.98039245605469,"left":86.94853258132935,"width":136.039216,"hidden":0},"T013_Data_Entrada":{"top":75.98039245605469,"left":427.5275731086731,"width":136.039216,"hidden":0},"T013_Valor_Total_Produtos":{"top":98.98896789550781,"left":54.91727876663208,"width":136.039216,"hidden":0},"T013_Valor_Desconto_Produtos":{"top":98.98896789550781,"left":418.88786363601685,"width":136.039216,"hidden":0},"T013_Valor_Total":{"top":121.98222351074219,"left":44.19424104690552,"width":136.039216,"hidden":0},"T013_Valor_Desconto":{"top":121.98222351074219,"left":408.16484117507935,"width":136.039216,"hidden":0},"T013_Observacao_1":{"top":144.9907989501953,"left":96.3082127571106,"height":107.03921600000001,"width":472.039216,"hidden":0},"T013_D024_Id_Participante":{"top":259.9877471923828,"left":95.40441179275513,"width":457.039216,"hidden":0},"T013_Chave_Acesso_Nfe":{"top":0,"left":0,"width":470.778,"hidden":0}}',
			'icms' => '{"T013_Valor_Total_Base_ICMS":{"top":7,"left":82.25,"width":104,"hidden":0},"T013_Valor_Total_ICMS":{"top":30,"left":82.46875,"width":104,"hidden":0},"T013_Casas_Decimais_Valor_ICMS":{"top":53,"left":-0.0625,"width":39,"hidden":0},"T013_Valor_Total_Base_ICMS_Substituicao":{"top":76,"left":19.265625,"width":104,"hidden":0},"T013_Valor_ICMS_Substituicao":{"top":99,"left":19.484375,"width":104,"hidden":0},"T013_Valor_Total_FCP_ST":{"top":122,"left":72.265625,"width":104,"hidden":0},"T013_Valor_ICMS_Diferenca_Aliquotas":{"top":191,"left":17,"width":128,"hidden":1},"T013_Aliquota_ICMS":{"top":260,"left":407.984375,"width":125,"hidden":1},"T013_Valor_Total_Base_ICMS_Diferido":{"top":306,"left":343.984375,"width":125,"hidden":1}}',
            'ipi' => '{"T013_Valor_Total_IPI":{"top":7,"left":84,"width":125,"hidden":0},"T013_Flag_Calcular_ICMS_IPI":{"top":30,"left":12,"width":56,"hidden":0},"T013_Flag_Arredondar_Valor_IPI":{"top":53,"left":25,"width":56,"hidden":0}}',
	        'frete' => '{"T013_Numero_Conhecimento":{"top":6.98529052734375,"left":25.536152362823486,"width":136.039216,"hidden":0},"button":{"top":4.993865966796875,"left":319.9754796028137,"width":null,"hidden":0},"botaoConhecimento":{"top":4.993865966796875,"left":400.0000157356262,"width":0,"hidden":0},"T013_Flag_Frete":{"top":29.993865966796875,"left":64.50673723220825,"width":144,"hidden":0},"T013_D022_Id":{"top":52.98713684082031,"left":63.25061082839966,"width":410.039216,"hidden":0},"T013_Valor_Frete":{"top":75.99571228027344,"left":85.66176652908325,"width":136.039216,"hidden":0},"T013_Flag_Calcular_ICMS_Frete":{"top":98.98896789550781,"left":11.79534387588501,"width":56,"hidden":0},"T013_Flag_Calcular_IPI_Frete":{"top":121.99754333496094,"left":25.15318727493286,"width":56,"hidden":0},"T013_Flag_Rateio_Frete":{"top":167.99937438964844,"left":18.964460849761963,"width":144,"hidden":0},"T013A_Flag_Destaca_Frete_SPED":{"top":144.9907989501953,"left":26.332722187042236,"width":56,"hidden":0},"T013_Tipo_Transporte":{"top":0,"left":0,"width":150,"hidden":1}}',
            'servicos' => '{"T013_Mao_Obra_1":{"top":7,"left":59,"width":317,"hidden":0},"T013_Codigo_Servico":{"top":122,"left":75,"width":324,"hidden":0},"T013_Valor_Mao_Obra":{"top":145,"left":83,"width":124,"hidden":0},"T013_Aliquota_ISS":{"top":168,"left":125,"width":124,"hidden":0},"T013_Valor_ISS":{"top":191,"left":110,"width":124,"hidden":0},"T013_Valor_Credito_IPTU":{"top":214,"left":9,"width":124,"hidden":0}}',
            'outros' => '{"tituloTelecomunicacao":{"top":372,"left":160,"width":null,"hidden":1},"T013_D014_Id":{"top":7,"left":13.421875,"width":140,"hidden":0},"T013_Tipo_Assinante":{"top":398,"left":145.46875,"width":192,"hidden":0},"T013_Flag_Validacoes":{"top":30,"left":164.8125,"width":56,"hidden":0},"T013_Flag_Calcular":{"top":53,"left":134.546875,"width":56,"hidden":0},"T013_Numero_Dias":{"top":53,"left":701,"width":74,"hidden":1},"T013_Flag_Obrigatorio_OC":{"top":76,"left":105.46875,"width":55,"hidden":0},"T013_Valor_Juros":{"top":99,"left":723,"width":124,"hidden":1},"tituloDespesasAcessorias":{"top":100,"left":181,"width":null,"hidden":1},"T013_Valor_Despesas_Acessorias":{"top":122,"left":78.3125,"width":124,"hidden":0},"T013_Flag_Calcular_ICMS_Despesas_Acessorias":{"top":145,"left":41.671875,"width":56,"hidden":0},"T013_Flag_Calcular_IPI_Despesas_Acessorias":{"top":168,"left":24.75,"width":56,"hidden":0},"tituloSeguro":{"top":191,"left":197,"width":null,"hidden":1},"T013_Valor_Seguro":{"top":214,"left":156,"width":124,"hidden":0},"T013_Flag_Calcular_ICMS_Seguro":{"top":237,"left":82.09375,"width":56,"hidden":0},"T013_Flag_Calcular_IPI_Seguro":{"top":260,"left":95.453125,"width":56,"hidden":0},"tituloEnergiaEletrica":{"top":280,"left":163,"width":null,"hidden":1},"T013_Classe_Consumo":{"top":306,"left":122.390625,"width":192,"hidden":0},"T013_Tipo_Ligacao":{"top":329,"left":140.6875,"width":191,"hidden":0},"T013_Grupo_Tensao":{"top":352,"left":137.984375,"width":191,"hidden":0}}',
        );

		$T013_Flag_Rateio_Frete_Preco = $g['C031']['formaRateioFreteNotaRecebida'] == '1' ? true : false;
		$T013_Flag_Rateio_Frete_Quantidade = $g['C031']['formaRateioFreteNotaRecebida'] == '2' ? true : false;
		$disabled = isset($g['c029Ids'][253]) ? false : true;
        $descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao)';
		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}
		// novo formulário
		$form = new Formulario('est004FormCadastroMarca');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'principal' => array(
					'T013_D024_Id' => array('name' => 'T013_D024_Id', 'label' => 'Fornecedor CPF/CNPJ', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id,D024_Cnpj,D024_Cpf','WHERE D024_Flag_Ativo="S" '.$clientePorEmpresa), 'length' => array(60,30)),
                    'T013_C004_Id' => array('name' => 'T013_C004_Id','label'=>'Empresa Atual', 'type' => 'hidden', 'value' => $g['empresaAtual'] ),
					'T013_D006_Id' => array('name' => 'T013_D006_Id','label'=>'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006','D006_Id',"LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}",'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Venda_Compra_Outros != "V" ORDER BY D006_Codigo_CFOP', true, false, '%1 - %2 - %3')
					),
                    'T013C004Id' => array('name' => 'T013C004Id', 'label' => 'Empresa', 'type' => 'select', 'reject' => true, 'select'=> 
                        gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa','',false,$g['empresaAtual']),
                    ),
					'T013_Tipo' => array('name' => 'T013_Tipo','label'=>'Tipo', 'type' => 'select', 
						'select' => array(
								array('title' => '...', 'value' => '', 'selected' => true),
								array('title' => 'NF-e', 'value' => '1'),
								array('title' => 'ACP', 'value' => '2'),
								array('title' => 'NFC-e', 'value' => '3'),
								array('title' => 'Cupom Fiscal', 'value' => '4'),
								array('title' => 'NF', 'value' => '5'),
								array('title' => 'NF/Conta de Energia Elétrica', 'value' => '6'),
								array('title' => 'NFS de Comunicação', 'value' => '7'),
								array('title' => 'NFS de Telecomunicação', 'value' => '8'),								
								array('title' => 'NFS', 'value' => '10'),
								array('title' => 'NF3e', 'value' => '11'),										
								array('title' => 'Outros', 'value' => '9'),		
								array('title' => 'NFCom', 'value' => '12'),										
   							)  			
						),
					'T013_Flag_Finalidade_NFe' => array('name' => 'T013_Flag_Finalidade_NFe', 'label'=>'Finalidade','type' => 'select', 'select' => 
						array(
							array('title' => 'Normal', 'value' => 1),
							array('title' => 'Complementar', 'value' => 2),
							array('title' => 'Ajuste', 'value' => 3),
							array('title' => 'Devolução de mercadoria', 'value' => 4),
							)
					),
					'T013_T075_Id' => array('name' => 'T013_T075_Id','label'=>'DI', 'align' => 'right','style' => 'float:right;', 'type' => 'select',
						'select' => gGeraSelect('T075','T075_Id','T075_Numero_DI','', true)
					),
					'T013_Numero_Nota_Fiscal' => array('name' => 'T013_Numero_Nota_Fiscal','label'=>'Número Nota Fiscal', 'type' => 'text', 'length' => array(20,30)),
  					'T013_Data_Emissao' => array('name' => 'T013_Data_Emissao','label'=>'Data Emissão', 'type' => 'data', 'length' => array(20,30)),
  					'T013_Data_Entrada' => array('name' => 'T013_Data_Entrada','label'=>'Data Entrada','align' => 'right','style' => 'float:right;', 'type' => 'data', 'length' => array(20,30)),
  					'T013_Valor_Total_Produtos' => array('name' => 'T013_Valor_Total_Produtos','label'=>'Valor Total Produtos', 'type' => 'moeda', 'length' => array(20,30)),
  					'T013_Valor_Desconto_Produtos' => array('name' => 'T013_Valor_Desconto_Produtos','label'=>'Desc Produtos','align' => 'right','style' => 'float:right;', 'type' => 'moeda', 'length' => array(20,30)),
  					'T013_Valor_Total' => array('name' => 'T013_Valor_Total','label'=>'Valor Total Nota Fiscal', 'type' => 'moeda', 'length' => array(20,30)),
  					'T013_Valor_Desconto' => array('name' => 'T013_Valor_Desconto','label'=>'Desc Nota Fiscal','align' => 'right','style' => 'float:right;', 'type' => 'moeda', 'length' => array(20,30)),
  					'T013_Observacao_1' => array('name' => 'T013_Observacao_1','label'=>'Observação', 'type' => 'textarea', 'length' => array(6,60)),
  					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),										
					'T013_D024_Id_Participante' => array('name' => 'T013_D024_Id_Participante', 'label' => 'Participante', 'type' => 'pesquisa', 
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id,D024_Cnpj,D024_Cpf','WHERE D024_Flag_Ativo="S"'), 'length' => array(60,30)
				),
					'T013_Serie' => array('name' => 'T013_Serie','label'=>'Série Nota Fiscal', 'type' => 'text', 'disabled' => $disabledNFCom),
					'T013_Chave_Acesso_Nfe' => array('name' => 'T013_Chave_Acesso_Nfe','label'=>'Chave de Acesso', 'type' => 'text', 'disabled' => $disabledNFCom),
				),
				'icms' => array(
					'T013_Valor_Total_Base_ICMS' => array('name' => 'T013_Valor_Total_Base_ICMS','label'=>'Base ICMS', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_Total_Base_ICMS_Diferido' => array('name' => 'T013_Valor_Total_Base_ICMS_Diferido','label'=>'B.ICMS Dif','align' => 'right','style' => 'float:right;',
							'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_Total_ICMS' => array('name' => 'T013_Valor_Total_ICMS','label'=>'Valor ICMS', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Aliquota_ICMS' => array('name' => 'T013_Aliquota_ICMS','label'=>'Aliquota ICMS', 'align' => 'right','style' => 'float:right;',
							'type' => 'moeda', 'length' => array(20,30)),
					'T013_Casas_Decimais_Valor_ICMS' => array('name' => 'T013_Casas_Decimais_Valor_ICMS','label'=>'Casas Decimais Valor ICMS', 'type' => 'select', 
							'select' =>  array(
									array('title' => '2', 'value' => '2', 'selected' => true),
									array('title' => '3', 'value' => '3'),
									array('title' => '4', 'value' => '4'),
									array('title' => '5', 'value' => '5'),
								),
							),
					'T013_Valor_Total_Base_ICMS_Substituicao' => array('name' => 'T013_Valor_Total_Base_ICMS_Substituicao','label'=>'Base ICMS Substituição', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_ICMS_Substituicao' => array('name' => 'T013_Valor_ICMS_Substituicao','label'=>'Valor ICMS Substituição', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_ICMS_Diferenca_Aliquotas' => array('name' => 'T013_Valor_ICMS_Diferenca_Aliquotas','label'=>'Valor Crédito ICMS Diferença aliquotas', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_Total_FCP_ST' => array('name' => 'T013_Valor_Total_FCP_ST','label'=>'Valor FCP ST', 'type' => 'moeda', 'length' => array(20,30)),
				),
				'ipi' => array(
					'T013_Valor_Total_IPI' => array('name' => 'T013_Valor_Total_IPI','label'=>'Valor IPI', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Flag_Calcular_ICMS_IPI' => array('name' => 'T013_Flag_Calcular_ICMS_IPI','label'=>'Calcular ICMS sobre IPI', 'type' => 'select', 
							'select' =>  array(
									array('title' => 'Sim', 'value' => 'S', 'selected' => true),
									array('title' => 'Não', 'value' => 'N'),
								),
							),
					'T013_Flag_Arredondar_Valor_IPI' => array('name' => 'T013_Flag_Arredondar_Valor_IPI','label'=>'Arredondar Valor IPI', 'type' => 'select', 
							'select' =>  array(
									array('title' => 'Sim', 'value' => 'S', 'selected' => true),
									array('title' => 'Não', 'value' => 'N'),
								),
							),
				),
				'frete' => array(
					/*
					'T013_Numero_Conhecimento' => array('name' => 'T013_Numero_Conhecimento','label'=>'Numero Conhecimento', 'type' => 'pesquisa','extra' => "onchange=\"preencheCamposFrete(this, '{$g['divId']}');\"",
						'pesquisa' => array('T084','T084_Id','T084_Numero_Conhecimento','LEFT JOIN D022 ON D022_Id=T084_D022_Id')
					),
					*/
					'T013_Numero_Conhecimento' => array('name' => 'T013_Numero_Conhecimento','label'=>'Numero Conhecimento', 'type' => 'text', 'length' => array(25,30)),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Não tem','reload' => false,'extra' => 'onclick="preencheNaoTem()"','align' => 'right', 'reject'=>true),
 					'T013_Flag_Frete' => array('name' => 'T013_Flag_Frete','label'=>'Frete por conta', 'type' => 'select', 
							'select' =>  array(
									array('title' => '9-Sem frete', 'value' => '0'),
									array('title' => '0-Por conta do emitente', 'value' => '1' ),
									array('title' => '1-Por conta do destinatário', 'value' => '2', 'selected' => true),
									array('title' => '2-Por conta de terceiros', 'value' => '3'),
								),
							),
					'T013_D022_Id' => array('name' => 'T013_D022_Id','label'=>'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022', 'D022_Id', 'D022_Nome_Empresa,D022_Cnpj'), 'length' => array(47,60)),
					'T013_Tipo_Transporte' => array('name' => 'T013_Tipo_Transporte','label'=>'Tipo Transporte', 'type' => 'text', 'length' => array(25,30)),
					'T013_Valor_Frete' => array('name' => 'T013_Valor_Frete','label'=>'Valor Frete', 'type' => 'moeda', 'align' => 'right','style' => 'float:right;', 'length' => array(20,30)),
					'T013_Flag_Calcular_ICMS_Frete' => array('name' => 'T013_Flag_Calcular_ICMS_Frete','label'=>'Calcular ICMS sobre Frete', 'type' => 'select',
							'select' => array(
									array('title' => 'Sim', 'value' => 'S',),
									array('title' => 'Não', 'value' => 'N','selected' => true),
								),
							),
						'T013_Flag_Calcular_IPI_Frete' => array('name' => 'T013_Flag_Calcular_IPI_Frete','label'=>'Calcular IPI sobre Frete', 'align' => 'right','style' => 'float:right;', 'type' => 'select',
								'select' => array(
										array('title' => 'Sim', 'value' => 'S',),
										array('title' => 'Não', 'value' => 'N','selected' => true),
										),
									),
						'T013A_Flag_Destaca_Frete_SPED' => array('name' => 'T013A_Flag_Destaca_Frete_SPED','label'=>'Destaca Frete no SPED', 'type' => 'select',
								'select' => array(
										array('title' => '...', 'value' => '','selected' => true),
										array('title' => 'Sim', 'value' => 'S'),
										array('title' => 'Não', 'value' => 'N'),
									),
								),
						'T013_Flag_Rateio_Frete' => array('name' => 'T013_Flag_Rateio_Frete', 'label' => 'Forma de rateio do Frete', 'align' => 'right','style' => 'float:right;', 'type' => 'select',
							'select' => array(
								array('title' => 'Preço', 'value' => '1', 'selected' => $T013_Flag_Rateio_Frete_Preco),
							array('title' => 'Quantidade', 'value' => '2', 'selected' => $T013_Flag_Rateio_Frete_Quantidade),
						),
					),
				),
				'servicos' => array(
					'T013_Mao_Obra_1' => array('name' => 'T013_Mao_Obra_1','label'=>'Descrição Serviços', 'type' => 'textarea', 'length' => array(6,60)),
					'T013_Codigo_Servico' => array('name' => 'T013_Codigo_Servico','label'=>'Código Serviços', 'type' => 'text', 'length' => array(60,60)),
					'T013_Valor_Mao_Obra' => array('name' => 'T013_Valor_Mao_Obra','label'=>'Valor Serviços', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Aliquota_ISS' => array('name' => 'T013_Aliquota_ISS','label'=>'% ISS', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_ISS' => array('name' => 'T013_Valor_ISS','label'=>'Valor ISS', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Valor_Credito_IPTU' => array('name' => 'T013_Valor_Credito_IPTU','label'=>'Crédito p/ Abatimento do IPTU ', 'type' => 'moeda', 'length' => array(20,30)),
				),
				'outros' => array(
				//D014_SubConta,D014_Grupo
					'T013_D014_Id' => array('name' => 'T013_D014_Id','label'=>'Conta (Deixar em branco p/usar da CFOP)', 'type' => 'pesquisa', 'pesquisa' => array('D014','D014_Id',"D014_SubConta,D032_Descricao,D154_Descricao", "LEFT JOIN D154 ON D154_Id=D014_D154_Id LEFT JOIN D032 ON D032_Id = D014_D032_Id"), 'length' => array(40,30)),
					/*
					'T013_D031_Id' => array('name' => 'T013_D031_Id','label'=>'Portador', 'type' => 'select', 'extra' => 'style="width:230px;"',
					    	'select' => gGeraSelect('D031','D031_Id','D031_Portador', 'order by D031_Portador', true),
						),
					'T013_Numero_Parcelas' => array('name' => 'T013_Numero_Parcelas','label'=>'Número Parcelas', 'type' => 'select', 
							'select' => array(
								array('title' => '1', 'value' => '1',  'selected' => true),
								array('title' => '2', 'value' => '2',),						
								array('title' => '3', 'value' => '3',),						
								array('title' => '4', 'value' => '4',),						
								array('title' => '5', 'value' => '5',),						
								array('title' => '6', 'value' => '6',),						
								array('title' => '7', 'value' => '7',),						
								array('title' => '8', 'value' => '8',),						
								array('title' => '9', 'value' => '9',),						
								array('title' => '10', 'value' => '10',),						
								array('title' => '11', 'value' => '11',),						
								array('title' => '12', 'value' => '12',),						
								array('title' => '13', 'value' => '13',),						
								array('title' => '14', 'value' => '14',),						
								array('title' => '15', 'value' => '15',),						
								array('title' => '16', 'value' => '16',),						
								array('title' => '17', 'value' => '17',),						
								array('title' => '18', 'value' => '18',),						
								array('title' => '19', 'value' => '19',),						
								array('title' => '20', 'value' => '20',),						
								array('title' => '21', 'value' => '21',),						
								array('title' => '22', 'value' => '22',),						
								array('title' => '23', 'value' => '23',),						
								array('title' => '24', 'value' => '24',),						
								array('title' => '25', 'value' => '25',),						
								array('title' => '26', 'value' => '26',),						
								array('title' => '27', 'value' => '27',),						
								array('title' => '28', 'value' => '28',),						
								array('title' => '29', 'value' => '29',),						
								array('title' => '30', 'value' => '30',),						
								array('title' => '31', 'value' => '31',),						
								array('title' => '32', 'value' => '32',),						
								array('title' => '33', 'value' => '33',),						
								array('title' => '34', 'value' => '34',),						
								array('title' => '35', 'value' => '35',),						
								array('title' => '36', 'value' => '36',),						
								array('title' => '37', 'value' => '37',),						
								array('title' => '38', 'value' => '38',),						
								array('title' => '39', 'value' => '39',),						
								array('title' => '40', 'value' => '40',),						
								array('title' => '41', 'value' => '41',),						
								array('title' => '42', 'value' => '42',),						
								array('title' => '43', 'value' => '43',),						
								array('title' => '44', 'value' => '44',),						
								array('title' => '45', 'value' => '45',),						
								array('title' => '46', 'value' => '46',),						
								array('title' => '47', 'value' => '47',),						
								array('title' => '48', 'value' => '48',),						
							)		
						),
					*/
					'T013_Numero_Dias' => array('name' => 'T013_Numero_Dias','label'=>'Dias entre as parcelas', 'align' => 'right','style' => 'float:right;',
							'type' => 'text', 'length' => array(10,10)),
					
					'T013_Valor_Juros' => array('name' => 'T013_Valor_Juros','label'=>'Valor Juros', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Flag_Validacoes' => array('name' => 'T013_Flag_Validacoes','label'=>'Validações', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'T013_Flag_Calcular' => array('name' => 'T013_Flag_Calcular','label'=>'Realizar Cálculos', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'T013_Flag_Obrigatorio_OC' => array('name' => 'T013_Flag_Obrigatorio_OC','label'=>'Obrigatório vincular OC', 'disabled' => $disabled, 'type' => 'select',
					'select' => array(
						array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
						array('title' => 'Não', 'value' => 'N',),
						)
					),
					'tituloDespesasAcessorias' => array('name' => 'tituloDespesasAcessorias', 'type' => 'titulo', 'label' => 'Despesas Acessorias', 'reject' => true),
					'T013_Valor_Despesas_Acessorias' => array('name' => 'T013_Valor_Despesas_Acessorias','label'=>'Outras Despesas Acessórias', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Flag_Calcular_ICMS_Despesas_Acessorias' => array('name' => 'T013_Flag_Calcular_ICMS_Despesas_Acessorias','label'=>'Calcular ICMS Despesas Acessorias', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'T013_Flag_Calcular_IPI_Despesas_Acessorias' => array('name' => 'T013_Flag_Calcular_IPI_Despesas_Acessorias','label'=>'Calcular IPI sobre Despesas Acessorias', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'tituloSeguro' => array('name' => 'tituloSeguro', 'type' => 'titulo', 'label' => 'Seguro', 'reject' => true),
					'T013_Valor_Seguro' => array('name' => 'T013_Valor_Seguro','label'=>'Valor Seguro', 'type' => 'moeda', 'length' => array(20,30)),
					'T013_Flag_Calcular_ICMS_Seguro' => array('name' => 'T013_Flag_Calcular_ICMS_Seguro','label'=>'Calcular ICMS sobre Seguro', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'T013_Flag_Calcular_IPI_Seguro' => array('name' => 'T013_Flag_Calcular_IPI_Seguro','label'=>'Calcular IPI sobre Seguro', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),						
							array('title' => 'Não', 'value' => 'N',),
						)
					),
					'T013_Numero_Nota_Fiscal_Devolucao' => array('name' => 'T013_Numero_Nota_Fiscal_Devolucao','label'=>'T013_Numero_Nota_Fiscal_Devolucao', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Codigo_CFOP' => array('name' => 'T013_Codigo_CFOP','label'=>'T013_Codigo_CFOP', 'type' => 'hidden', 'length' => array(20,30)),
					//'T013_Numero_Controle' => array('name' => 'T013_Numero_Controle','label'=>'T013_Numero_Controle', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Parcelas' => array('name' => 'T013_Parcelas','label'=>'T013_Parcelas', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Valor_Encargos' => array('name' => 'T013_Valor_Encargos','label'=>'T013_Valor_Encargos', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Numero_Ordem_Compra' => array('name' => 'T013_Numero_Ordem_Compra','label'=>'T013_Numero_Ordem_Compra', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Valor_Total_Base_IPI' => array('name' => 'T013_Valor_Total_Base_IPI','label'=>'T013_Valor_Total_Base_IPI', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Nome_Transportadora' => array('name' => 'T013_Nome_Transportadora','label'=>'T013_Nome_Transportadora', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_T085_Id' => array('name' => 'T013_T085_Id','label'=>'T013_T085_Id', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Mao_Obra_2' => array('name' => 'T013_Mao_Obra_2','label'=>'T013_Mao_Obra_2', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_Valor_Dolar' => array('name' => 'T013_Valor_Dolar','label'=>'T013_Valor_Dolar', 'type' => 'hidden', 'length' => array(20,30)),
					'T013_C008_Id' => array('name' => 'T013_C008_Id','label'=>'T013_C008_Id', 'type' => 'hidden', 'length' => array(20,30)),
					'tituloEnergiaEletrica' => array('name' => 'tituloEnergiaEletrica', 'type' => 'titulo', 'label' => 'Energia Elétrica', 'reject' => true),
					'T013_Classe_Consumo' => array('name' => 'T013_Classe_Consumo', 'label'=>'Classe de consumo','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Comercial', 'value' => '01' ),
							array('title' => 'Consumo Próprio', 'value' => '02' ),
							array('title' => 'Iluminação Pública', 'value' => '03' ),
							array('title' => 'Industrial', 'value' => '04' ),
							array('title' => 'Poder Público', 'value' => '05' ),
							array('title' => 'Residencial', 'value' => '06' ),
							array('title' => 'Rural', 'value' => '07' ),
							array('title' => 'Serviço Público', 'value' => '08' ),
						)
					),
					'T013_Tipo_Ligacao' => array('name' => 'T013_Tipo_Ligacao', 'label'=>'Tipo de Ligação','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Monofásico', 'value' => '1' ),							
							array('title' => 'Bifásico', 'value' => '2' ),							
							array('title' => 'Trifásico', 'value' => '3' ),							
						)
					),
					'T013_Grupo_Tensao' => array('name' => 'T013_Grupo_Tensao', 'label'=>'Grupo de tensão','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'AI - Alta Tensão (230kV ou mais) ', 'value' => '01' ),
							array('title' => 'A2 - Alta Tensão (88 a J38kV)', 'value' => '02' ),
							array('title' => 'A3 - Alta Tensão (69kV)', 'value' => '03' ),
							array('title' => 'A3a - Alta Tensão (30kV a 44kV) ', 'value' => '04' ),
							array('title' => 'A4 - Alta Tensão (2,3kV a 25kV) ', 'value' => '05' ),
							array('title' => 'AS - Alta Tensão Subterrâneo 06 ', 'value' => '06' ),
							array('title' => 'B 1 - Residencia l 07', 'value' => '07' ),
							array('title' => 'B 1 - Residencia l BaLxa Renda 08 ', 'value' => '08' ),
							array('title' => 'B2 - Rural 09', 'value' => '09' ),
							array('title' => 'B2 - Cooperativa de Eletrificação Rural', 'value' => '10' ),
							array('title' => 'B2 - Serviço Público de Irrigação', 'value' => '11' ),
							array('title' => 'B3 - Demais Classes', 'value' => '12' ),
							array('title' => 'B4a - Iluminação Pública - rede de distribuição', 'value' => '13' ),
							array('title' => 'B4b - Iluminação Pública - bulbo de lâmpada', 'value' => '14' ),
						)
					),
					'tituloTelecomunicacao' => array('name' => 'tituloTelecomunicacao', 'type' => 'titulo', 'label' => 'Comunicação/Telecomunicação', 'reject' => true),
					'T013_Tipo_Assinante' => array('name' => 'T013_Tipo_Assinante', 'label'=>'Tipo Assinante','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Comercial/Industrial', 'value' => '1' ),
							array('title' => 'Poder Público', 'value' => '2' ),
							array('title' => 'Residencial/Pessoa física', 'value' => '3' ),
							array('title' => 'Público', 'value' => '4' ),
							array('title' => 'Semi-Público', 'value' => '5' ),
							array('title' => 'Outros', 'value' => '6' ),
						)
					),
				)
			)
		);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #T013_Tipo').change(function() {
					if ($(this).val() == 12) {
						$('#{$g['divId']} #T013_Serie').closest('.formEditor').show();
						$('#{$g['divId']} #T013_Chave_Acesso_Nfe').closest('.formEditor').show();
						setTimeout(function() {
							$('#{$g['divId']} #T013_Serie').focus();
							$('#{$g['divId']} #T013_Chave_Acesso_Nfe').focus();
						}, 100);
					}else{
						$('#{$g['divId']} #T013_Serie').closest('.formEditor').hide();
						$('#{$g['divId']} #T013_Chave_Acesso_Nfe').closest('.formEditor').hide();
					}
				});
				$('#{$r_formId} #T013_Tipo').trigger('change');
			} </script></div>
EOT;

		$sqlT084 = mysql_query("SELECT T013_T084_Id FROM T013 WHERE T013_Id = '{$acaoId}' AND T013_T084_Id > 0");
		if(mysql_num_rows($sqlT084) == 1){
			$resT084 = mysql_fetch_array($sqlT084);
			$formArray['campos']['frete']['botaoConhecimento'] = array('name' => 'botaoConhecimento', 'label'=>'', 'value'=>'Abrir Conhecimento', 'type' => 'button', 'reject' => true, 'disabled' => false);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#botaoConhecimento').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Conhecimento', '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent('{$resT084['T013_T084_Id']}') + '&tabela=T084', [800,500]);
					});
				} </script></div>
EOT;
		} else {
			$formArray['campos']['frete']['botaoConhecimento'] = array('name' => 'botaoConhecimento', 'label'=>'', 'value'=>'Abrir Conhecimento', 'type' => 'button', 'reject' => true, 'disabled' => true ,  'style' => 'opacity: 0.5;', 'extra' => 'title="Conhecimento não vinculado, caso já tenha digitado o número verifique se a transportadora é a mesma na NF e CONHECIMENTO"');
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
                if ($form->campoValorEnviado('T013C004Id')) {
                    $form->campoSetarValor('T013_C004_Id', $form->campoValorEnviado('T013C004Id'), true);
                }
				// Insert ou Update?
				if (!empty($acaoId)) {
						// Update
	                    $C004_Id = $form->campoValorEnviado('T013_C004_Id');
	                    mysql_query("UPDATE T014 SET T014_C004_Id='{$C004_Id}' WHERE T014_T013_Id='{$acaoId}'");
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							$T013A_Flag_Destaca_Frete_SPED = $form->campoValorEnviado('T013A_Flag_Destaca_Frete_SPED');
							if($T013A_Flag_Destaca_Frete_SPED != 'S'){
								$T013A_Flag_Destaca_Frete_SPED = 'N';
							}
							mysqli_query("INSERT INTO T013A(T013A_T013_Id, T013A_Flag_Destaca_Frete_SPED)
										   SELECT '{$acaoId}', '{$T013A_Flag_Destaca_Frete_SPED}'
										    WHERE NOT EXISTS(SELECT 1 FROM T013A WHERE T013A_T013_Id = '{$acaoId}')");
							mysqli_query("UPDATE T013A
										  SET T013A_Flag_Destaca_Frete_SPED = '{$T013A_Flag_Destaca_Frete_SPED}'
										WHERE T013A_T013_Id = '{$acaoId}'");
							if ($r_fechaForm == 1) {
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										//setTimeout(function() {
											{$alert}
										//}, 150);
	  								//});
									//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$r_tabFreteId}', true);
									console.log('{$r_tabFreteId}');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
                                    if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
                                        divRefresh('{$g['divIdParent']}', true);
                                    }
								} </script></div>
EOT;
						}

						$EST004->acoesAposAlterarNota($acaoId);
					}
				} else {
                    // Insert
                    //Duas últimas variáveis são recebidas pela tab Ordem Compra;
					$retorno = $EST004->cadastrarNota($form, $acaoId, $GerarNFOC, $extra);
					if ($retorno !== true) {
						echo gGeraAlertMsg('Operação ERRO', $retorno);
					} else {
						echo gGeraAlertMsg('Operação OK', 'Registro Incuido com Sucesso!');
						echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Nota Fiscal - ID: ' + '{$ultimoT013_Id}', '/est/est004/content/est004ContentEditarNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent('{$ultimoT013_Id}'), [990,550]);
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
	                $form->campoSetarValor('T013C004Id', $form->campoValor('T013_C004_Id'));
					$sqlT013A = mysql_query("SELECT IFNULL(T013A_Flag_Destaca_Frete_SPED, 'N') as T013A_Flag_Destaca_Frete_SPED
											   FROM T013A
											  WHERE T013A_T013_Id = '{$acaoId}'");
					if($sqlT013A && mysql_num_rows($sqlT013A) > 0){
						$mT013A = mysql_fetch_array($sqlT013A);
						$form->campoSetarValor('T013A_Flag_Destaca_Frete_SPED', $mT013A['T013A_Flag_Destaca_Frete_SPED']);
					} else {
						$form->campoSetarValor('T013A_Flag_Destaca_Frete_SPED', 'N');
					}
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
	break;
	
	case 'est004FormTotais':
		mysql_query("call T013_Gravar_Totalizacao($r_acaoId)");
		
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
		$sql = " select sum(T014_Valor_Base_ICMS) as T014_Valor_Base_ICMS,
                          round(sum(T014_Valor_ICMS),2) as T014_Valor_Total_ICMS,
                          sum(T014_Valor_Base_IPI) as T014_Valor_Base_IPI,
                          sum(T014_Valor_IPI) as T014_Valor_Total_IPI,
                          sum(T014_Valor_Total_Custo) as T014_Valor_Total_Produtos,
                          sum(T014_Valor_Total_Desconto) as T014_Valor_Total_Desconto,
                          sum(T014_Valor_ICMS_Substituicao) as T014_Valor_ICMS_Substituicao,
                          sum(T014_Valor_Despesas_Acessorias) as T014_Valor_Despesas_Acessorias,
						  sum(T014_Valor_Frete) as T014_Valor_Frete,
						  sum(T014_Valor_FCP_ST) as T014_Valor_FCP_ST
                     from T014
                    where T014_T013_Id='{$r_acaoId}'";
        
        $result = mysql_query($sql);
        $linha = mysql_fetch_assoc($result);
        
        $sqlT003 = " select if(T013_Numero_Conhecimento='NAO TEM' and T013_Valor_Frete>0,ifnull(SUM(T013_Valor_Frete),0.00),0.00) as TotalFrete
                     from T013
                    where T013_Id='{$r_acaoId}'";
        
        $resultT003 = mysql_query($sqlT003);
        $linhaT003 = mysql_fetch_assoc($resultT003);
        
		$form_tabela = 'T014';
		$form_campo_pk = 'T014_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormPagamentosNotaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormPagamentoNota/';

		//form editor
		$form_editor = array(
			'geral' => '{"T014_Valor_Total_Produtos":{"top":7,"left":13.859375,"width":168,"hidden":0},"T014_Valor_Total_ICMS":{"top":29.96875,"left":30.375,"width":168,"hidden":0},"T014_Valor_ICMS_Substituicao":{"top":53,"left":14.484375,"width":168,"hidden":0},"T014_Valor_Total_IPI":{"top":76,"left":44.4375,"width":168,"hidden":0},"T014_Valor_FCP_ST":{"top":99,"left":20.1875,"width":168,"hidden":0},"T014_Valor_Total":{"top":122,"left":34.640625,"width":168,"hidden":0}}', 
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T014_Valor_Total_Produtos' => array('name' => 'T014_Valor_Total_Produtos', 'value'=>number_format($linha['T014_Valor_Total_Produtos'],2,',','.'), 'label' => 'Produtos', 'disabled'=>true, 'type' => 'text', 'length' => array(30,60)),
					'T014_Valor_Total_ICMS' => array('name' => 'T014_Valor_Total_ICMS', 'value'=>number_format($linha['T014_Valor_Total_ICMS'],2,',','.'), 'label' => 'ICMS', 'disabled'=>true, 'type' => 'text', 'length' => array(30,60)),
					'T014_Valor_ICMS_Substituicao' => array('name' => 'T014_Valor_ICMS_Substituicao', 'label' => 'ICMS ST','value'=>number_format($linha['T014_Valor_ICMS_Substituicao'],2,',','.'), 'disabled'=>true, 'type' => 'text', 'length' => array(30,60)),
					'T014_Valor_Total_IPI' => array('name' => 'T014_Valor_Total_IPI', 'value'=>number_format($linha['T014_Valor_Total_IPI'],2,',','.'), 'label' => 'IPI', 'IPI'=>true, 'type' => 'text', 'length' => array(30,60), 'disabled'=>true),
					'T014_Valor_FCP_ST' => array('name' => 'T014_Valor_FCP_ST', 'value'=>number_format($linha['T014_Valor_FCP_ST'],2,',','.'), 'label' => 'FCP ST', 'disabled'=>true, 'type' => 'text', 'length' => array(30,60)),
					'T014_Valor_Total' => array('name' => 'T014_Valor_Total', 'label' => 'Total', 'value'=>number_format($linha['T014_Valor_Total_Produtos']+$linhaT003['TotalFrete']+$linha['T014_Valor_Total_IPI']+$linha['T014_Valor_ICMS_Substituicao']+$linha['T014_Valor_FCP_ST']-$linha['T014_Valor_Total_Desconto'],2,',','.'), 'IPI'=>true, 'type' => 'text', 'length' => array(30,60), 'disabled'=>true),
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
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'est004FormAssociarItemProduto':
		global $g;
		require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T014_Id = (isset($r_T014_Id)) ? $r_T014_Id : false;

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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormAssociarItemProduto_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormAssociarItemProduto/';

		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}' AND D001_Flag_Ativo != 'N')
			{$filtro}
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoAssociarItem',																		// callback js
			$g['divId'],																						// divId
			''																									// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('est004FormAssociarItemProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50)),
					// Campos Hidden
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label' => 'D001_Codigo_Produto', 'type' => 'hidden', 'restricao' => false),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T014_Id' => array('name' => 'T014_Id', 'label' => 'T014_Id','value' => $T014_Id, 'type' => 'hidden'),
				),
			)
		);
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
					$T014_Id = $form->campoValorEnviado('T014_Id');
					$retorno = $EST004->associarProduto($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							//divRefresh('{$g['divIdParent']}', true);
							// Ação: abrir nova janela do browser 
							// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
							//nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id='+encodeURIComponent('{$form->campoValorEnviado('T005_Id')}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
					}
				}
			}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est004PreCadastrarProdutoBtn">Pré-Cadastrar produto</button>';
		$botoesEsq .= '<button type="submit" id="est004VincularOC">Ordem de Compra</button>';
		$botoesDir .= '<button type="submit" id="est004FormAssociarItemProduto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est004PreCadastrarProdutoBtn').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);
				return false;
			});

			$('#est004VincularOC').unbind('click').bind('click', function(data) {
				fecharJanela("{$g['divId']}");
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione o Item', '/est/est004/content/est004ContentAssociarItemNovo/', '&acaoId=' + encodeURIComponent('{$T014_Id}') + '&divRefresh=' + encodeURIComponent('{$r_divRefresh}'), [950,500]);
			});
		} </script></div>
EOT;

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;


	case 'est004FormAssociarItem':
		require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T014_Id = (isset($r_T014_Id)) ? $r_T014_Id : false;

		$sqlT013 = mysql_query("SELECT T013_Flag_Travar_Nota, ROUND(T014_Valor_Custo_Unitario,2) as T014_Valor_Custo_Unitario, T014_Quantidade FROM T013 LEFT JOIN T014 ON T014_T013_Id = T013_Id WHERE T014_Id = '{$T014_Id}'");
		$rowT013 = mysql_fetch_array($sqlT013);
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormAssociarItem_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004FormAssociarItem/';
	
		//funcao gridLinha do pesquisaGrid
		$gridLinha = array(
			array('clausulaIf' => base64_encode("\$array['todosCampos']['T008_Valor_Preco_Unitario'] == " . $rowT013['T014_Valor_Custo_Unitario'] ." && ". $rowT013['T014_Quantidade'] . " <= \$array['todosCampos']['T008_Quantidade']"), 'corLinha' => 'linhaVerde linhaNegrito')
		);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			LEFT JOIN T007 ON T008_T007_Id=T007_Id
			LEFT JOIN T014 ON T014_T008_Id=T008_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
			WHERE T007_C004_Id = '{$g['empresaAtual']}' AND T007_D024_Id = '{$r_D024_Id}'
			AND T008_Quantidade_Falta_Lancar(T008_Id) > 0
			GROUP BY T008_Id
			ORDER BY T008_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'T008',																								// tabela
			'T008_Id',																							// chave primaria
			'T008_Codigo_Produto,T008_Descricao_Produto,T007_Numero_Nota_Fiscal,T007_Data_Emissao,T008_Quantidade,T008_Quantidade_Falta_Lancar(T008_Id),T008_Valor_Preco_Unitario,T008_Aliquota_IPI,T008_Aliquota_ICMS,T008_Valor_ICMS_Substituicao_Retencao,C007_Primeiro_Nome,T007_Id',// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto - Notas Fiscais Emitidas',																				// título da janela
			'Código,Descrição,Nota Fiscal,Emissão,Quantidade,Saldo,Preço Unit.,IPI,ICMS,ST R$,Vendedor,T007_Id',																// descrição dos campos
			'campoProcuraProdutoAssociar',																		// callback js
			$g['divId'],																						// divId
			array($T014_Id),																					// parametro extra
			array(																											              // parametros para cada campo do grid
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'left', 'style' => 'font-weight:bold'),
				array('callback' => 'abrirJanela', 'callbackParameter' => array('Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'T007_Id', '990,605')),
				array('callback' => 'gCorrigeData'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'width:1%;'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%'),
				array('colunaExibir' => false),
			),
			'',
			'[950,500]',
			'/est/est004/outro/est004CabecalhoAssociarItemNovo/',
			$gridLinha
		);
		
		// novo formulário
		$form = new Formulario('est004FormAssociarItem');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50), 'disabled' => ($rowT013['T013_Flag_Travar_Nota'] == 'S') ? true : false),
					// Campos Hidden
					'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto', 'label' => 'T008_Codigo_Produto', 'type' => 'hidden', 'restricao' => false),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T008_Id' => array('name' => 'T008_Id', 'label' => 'T008_Id', 'type' => 'hidden'),
					'T014_Id' => array('name' => 'T014_Id', 'label' => 'T014_Id','value' => $T014_Id, 'type' => 'hidden'),
				),
			)
		);
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
				$retorno = $EST004->associarProdutoT008($form);
				if ($retorno === true) {
					$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela("{$g['divId']}");
						divRefresh('{$g['divIdParent']}', true);
						// Ação: abrir nova janela do browser 
						// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
						//nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id='+encodeURIComponent('{$form->campoValorEnviado('T005_Id')}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
					} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Erro', 'Erro ao vincular produto da nota fiscal.<br />' . $retorno);
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		$disabled = ($rowT013['T013_Flag_Travar_Nota'] == 'S') ? 'disabled=disabled' : '';
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesEsq .= '<button type="submit" id="est004PreCadastrarProdutoBtn">Pré-Cadastrar produto</button>';
		$botoesEsq .= '<button type="submit" id="est004DesvincularProduto" '. $disabled . '>Desvincular produto</button>';
		$botoesDir .= '<button type="submit" id="est004FormAssociarItem_Btn" '. $disabled . '>Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est004PreCadastrarProdutoBtn').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);
				return false;
			});

			$('#est004DesvincularProduto').unbind('click').bind('click', function(data) {
				$.getJSON('/est/est004/form_func-ajax/est004DesvincularProduto/?ajax=true&T014_Id=' + encodeURIComponent('{$T014_Id}') + '&callback=?', function(request) {
					if (request.code) {															
						divRefresh('{$g['divIdParent']}');
					} else {
						var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
						dialogConfirm("Erro!", request.data, buttons)
					}
				});
				return false;
			});
		} </script></div>
EOT;

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;
	
	case 'est004formInserirQuantidade':
		require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T225_Id 			= (isset($r_T225_Id)) ? $r_T225_Id : false;
		$T013_Id 			= (isset($r_T013_Id)) ? $r_T013_Id : false;
		$T086_Quantidade 	= (isset($r_T086_Quantidade)) ? $r_T086_Quantidade : false;
		
		$qnt = number_format($T086_Quantidade, 3, ',', ' ');
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004formInserirQuantidade_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est004/form/est004formInserirQuantidade/';
		
		
		// novo formulário
		$form = new Formulario('form_est004formInserirQuantidade');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campos Hidden
					'quantidade' => array('name' => 'quantidade', 'label' => 'Recebido', 'value'=>@$qnt, 'type' => 'text', 'length' => array(30,60)),
				),
			)
		);
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
                    if($T013_Id == false){
                        $retorno = $EST004->est004_alterar_quantidade2($form, $T225_Id);
                    }else{
					    $retorno = $EST004->est004_alterar_quantidade($form, $T225_Id, $T013_Id);
                    }
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
					}
				}
			}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est004formInserirQuantidade_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;

	case 'est004FormCancelarNotaEntrada':

		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;
		
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
		$enviar_btnId = 'cad035FormReciboDeLocacoesBtn';
		// Form que será executado ao clicar no botão54
		$enviar_acaoHref = '/est/est004/form/est004FormCancelarNotaEntrada/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'Motivo_Cancelamento' => array('name' => 'Motivo_Cancelamento', 'label' => 'Motivo Cancelamento', 'type' => 'textarea', 'length' => array(8,65)),
				)
			)
		);
        $form = new Formulario('est004FormCancelarNotaEntrada');
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
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$retorno = $EST004->EST004CancelarNotaEntrada($form, $T013_Id);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Nota cancelada com sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										{$alert}
                                        divRefresh('{$g['divIdParent']}', true);
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Erro!', $retorno);
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
		$novoIdTelaForm = uniqid();
		global $g;
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad035FormReciboDeLocacoesBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		if($r_acaoId <> 0)
			$botoesEsq .= "<button onclick=\"abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Recibo Locação', '/cad/cad035/outro/cad035locacaoBensMoveis/','&T156_Id={$r_acaoId}', [1000,700]);\">Imprimir</button>";
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	

	case 'est004FormCadastroObservacao':
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;		
		
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
		$form_tabela = 'T297';
		$form_campo_pk = 'T297_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormCadastroObservacaoBtn';
		// Form que será executado ao clicar no botão54
		$enviar_acaoHref = '/est/est004/form/est004FormCadastroObservacao/';
		
		$form_editor = array(
			'geral' => '{"T297_D188_Id":{"top":7,"left":80.296875,"width":362,"hidden":0},"T297_Descricao_Complementar_Observacao":{"top":30,"left":14.421875,"height":105,"width":376,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T297_D188_Id' => array('name' => 'T297_D188_Id', 'label' => 'Observação', 'type' => 'pesquisa', 
						'pesquisa' => array('D188','D188_Id','D188_Codigo_Observacao,D188_Descricao_Observacao')
					),
					'T297_Descricao_Complementar_Observacao' => array('name' => 'T297_Descricao_Complementar_Observacao', 'label' => 'Descrição Complementar', 'type' => 'textarea'),
					'T297_T013_Id' => array('name' => 'T297_T013_Id', 'label' => 'T014_Id','value' => $T013_Id, 'type' => 'hidden'),
				)
			)
		);
        $form = new Formulario('est004FormCadastroObservacao');
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
		$botoesDir .= '<button type="submit" id="est004FormCadastroObservacaoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	
	case 'est004FormCadastroObrigacaoTributaria':
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;		
		$T297_Id = isset($r_T297_Id) ? $r_T297_Id : false;						

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
		$form_tabela = 'T298';
		$form_campo_pk = 'T298_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormCadastroObrigacaoTributariaBtn';
		// Form que será executado ao clicar no botão54
		$enviar_acaoHref = '/est/est004/form/est004FormCadastroObrigacaoTributaria/';
		
		$form_editor = array(
			'geral' => '{"T298_D189_Id":{"top":7,"left":139.640625,"width":441,"hidden":0},"T298_Descricao_Complementar_Ajuste":{"top":30,"left":12.734375,"width":456,"hidden":0},"T298_T014_Id":{"top":53,"left":111.078125,"width":441,"hidden":0},"T298_Valor_Base_ICMS":{"top":76,"left":117.265625,"width":104,"hidden":0},"T298_Aliquota_ICMS":{"top":99,"left":100.921875,"width":104,"hidden":0},"T298_Valor_ICMS":{"top":122,"left":116.203125,"width":104,"hidden":0},"T298_Outros_Valores":{"top":145,"left":109.703125,"width":104,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T298_D189_Id' => array('name' => 'T298_D189_Id', 'label' => 'Ajuste', 'type' => 'pesquisa', 
						'pesquisa' => array('D189','D189_Id','D189_Codigo_Ajuste,D189_Descricao_Ajuste')
					),
					'T298_T014_Id' => array('name' => 'T298_T014_Id', 'label' => 'Código Item', 'type' => 'pesquisa', 
						'pesquisa' => array('T014','T014_Id','D001_Codigo_Produto,T014_Descricao_Produto', "LEFT JOIN D009 ON D009_Id = T014_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id  WHERE T014_T013_Id = '{$T013_Id}'")						
					),					
					'T298_Descricao_Complementar_Ajuste' => array('name' => 'T298_Descricao_Complementar_Ajuste', 'label' => 'Descricao Complementar Ajuste', 'type' => 'text'),
					'T298_Valor_Base_ICMS' => array('name' => 'T298_Valor_Base_ICMS', 'label' => 'Base ICMS', 'type' => 'moeda'),
					'T298_Aliquota_ICMS' => array('name' => 'T298_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type' => 'moeda'),
					'T298_Valor_ICMS' => array('name' => 'T298_Valor_ICMS', 'label' => 'Valor ICMS', 'type' => 'moeda'),
					'T298_Outros_Valores' => array('name' => 'T298_Outros_Valores', 'label' => 'Valor Outros', 'type' => 'moeda'),					
					'T298_T297_Id' => array('name' => 'T298_T297_Id', 'label' => 'T298_T297_Id','value' => $T297_Id, 'type' => 'hidden'),
				)
			)
		);
        $form = new Formulario('est004FormCadastroObservacao');
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
		$botoesDir .= '<button type="submit" id="est004FormCadastroObrigacaoTributariaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est004FormCadastroCiap':
		
		$T013_Id = isset($r_T013_Id) ? $r_T013_Id : false;	
		$acaoId = isset($r_T316_Id) ? $r_T316_Id : false;	

		$T013 = mysql_query("SELECT T013_Data_Entrada
							   FROM T013
							  WHERE T013_Id = '{$T013_Id}'");
		$mT013 = mysql_fetch_array($T013);

		$mT013['T013_Data_Entrada'] = date("d/m/Y", strtotime($mT013['T013_Data_Entrada']));

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
		$form_tabela = 'T316';
		$form_campo_pk = 'T316_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est004FormCadastroCiapBtn';
		// Form que será executado ao clicar no botão54
		$enviar_acaoHref = '/est/est004/form/est004FormCadastroCiap/';
		
		$form_editor = array(
			'geral' => '{"T316_T014_Id":{"top":6.95465087890625,"left":80.39215087890625,"width":298.039216,"hidden":0},"T316_Codigo_Produto_CIAP":{"top":6.98529052734375,"left":454.871337890625,"width":88.03921600000001,"hidden":0},"T316_Descricao_Utilizacao_Bem":{"top":29.993865966796875,"left":8.76226806640625,"width":504.039216,"hidden":0},"T316_Data_Inicio_Apropriacao_Credito":{"top":52.98713684082031,"left":26.16424560546875,"width":104.03921600000001,"hidden":0},"T316_Numero_Parcelas_Apropriacao":{"top":75.99571228027344,"left":57.1844482421875,"width":104.03921600000001,"hidden":0},"T316_Flag_Ativo_Baixado":{"top":98.98898315429688,"left":83.19549560546875,"width":111,"hidden":0}}'
		);

		$readonly = ($acaoId > 0) ? true : false ;

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T316_T014_Id' => array('name' => 'T298_T014_Id', 'label' => 'Código', 'readonly' => $readonly , 'type' => 'pesquisa', 
						'pesquisa' => array('T014','T014_Id','D001_Codigo_Produto,T014_Descricao_Produto', "LEFT JOIN D009 ON D009_Id = T014_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id  WHERE T014_T013_Id = '{$T013_Id}' AND D001_Flag_Tipo_Item = '08'")						
					),			
					'T316_Codigo_Produto_CIAP' => array('name' => 'T316_Codigo_Produto_CIAP', 'label' => 'Cód.Escrituração', 'type' => 'text','length' => array(60,60)),		
					'T316_Data_Inicio_Apropriacao_Credito' => array('name' => 'T316_Data_Inicio_Apropriacao_Credito', 'label' => 'Início Apropriação', 'type' => 'data', 'value' => $mT013['T013_Data_Entrada']),
					'T316_Numero_Parcelas_Apropriacao' => array('name' => 'T316_Numero_Parcelas_Apropriacao', 'label' => 'Nº Parcelas', 'type' => 'text'),
					'T316_Descricao_Utilizacao_Bem' => array('name' => 'T316_Descricao_Utilizacao_Bem', 'label' => 'Descr. Utilização Bem', 'type' => 'text','length' => array(60,60)),
					'T316_Flag_Ativo_Baixado' => array('name' => 'T316_Flag_Ativo_Baixado','label'=>'Baixado', 'type' => 'select', 
						'select' => array(
								array('title' => 'Não', 'value' => 'N', 'selected' => true),
								array('title' => 'Sim', 'value' => 'S'),
				
						)  			
					),					
				)
			)
		);
        $form = new Formulario('est004FormCadastroObservacao');
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
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
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
					$T316_Codigo_Produto_CIAP = $form->campoValorEnviado('T316_Codigo_Produto_CIAP');

					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {

						$T316_Id = $g['mysqlLastId'];
						if(empty($T316_Codigo_Produto_CIAP)){
							$T316 = mysql_query("SELECT D001_Codigo_Produto 
										    	   FROM T316
									          LEFT JOIN T014 ON T014_Id = T316_T014_Id
											  LEFT JOIN D009 ON D009_Id = T014_D009_Id
									          LEFT JOIN D049 ON D049_Id = D009_D049_Id
									          LEFT JOIN D001 ON D001_Id = D049_D001_Id
									              WHERE T316_Id = '{$T316_Id}'");
							
							$mT316 = mysql_fetch_array($T316);

							mysqli_query("UPDATE T316 SET T316_Codigo_Produto_CIAP = '{$mT316['D001_Codigo_Produto']}' WHERE T316_Id = '{$T316_Id}'");
						}


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
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est004FormCadastroCiapBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
