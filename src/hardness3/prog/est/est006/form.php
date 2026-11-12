<?php
namespace hardness;

/**
 * Ações
 */
require_once('prog/est/est006/form_func-js.php');
require_once("bibliotecas/classes/EST006.php");
$EST006 = new EST006();
switch ($g['r'][3]) {

	case 'est006FormIncluirOrdemCompra':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
		$r_sql = (isset($r_sql)) ? $r_sql : false;
		
		$x = $r_sql;
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
		//die();
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T011';
		$form_campo_pk = 'T011_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est006FormIncluirOrdemCompra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est006/form/est006FormIncluirOrdemCompra/';
        // Form Editor
        $form_editor = array(
            'geral' => '{"Procura_Fornecedor":{"top":7,"left":18,"width":314,"hidden":0},"T011_C007_Id":{"top":30,"left":14,"width":330,"hidden":0},"T011_Observacao":{"top":53,"left":14,"height":62,"width":328,"hidden":0},"T011_Valor_Frete":{"top":121.99996948242188,"left":33,"width":90,"hidden":0},"T011_Prazo_Entrega":{"top":121.99996948242188,"left":214,"width":120,"hidden":0},"T011_Forma_Pagamento":{"top":144.99996948242188,"left":-12,"width":328,"hidden":0},"T011C004Id":{"top":167.99996948242188,"left":32,"width":336,"hidden":1},"est006PreCadastrarCliente":{"top":211.99996948242188,"left":160,"width":null,"hidden":0},"est006FormIncluirOrdemCompra_btn":{"top":211.99996948242188,"left":336.00006103515625,"width":null,"hidden":0}}',
        );
		
		// Campo: Procura_Produto
		$pesquisaGridExtra2 = <<<EOT
			left join D020 on D020_Id=D024_D020_Id 
			left join D018 on D018_Id=D024_D018_Id 
			WHERE D024_Flag_Ativo='S' 
			order by D024_Nome_Empresa
EOT;
		$pesquisaGrid2 = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra2,																								// extras sql
			'Pesquisa de Fornecedor',																							// título da janela
			'Código,Fornecedor,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',														// descrição dos campos
			'est006ProcuraFornecedor',																					// callback js
			$g['divId'],																									// divId
			false,                                                                                      					// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('colunaExibir'=>false),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		
		$sqlT011 = "SELECT D024_Id,D024_Nome_Empresa FROM T011 LEFT JOIN D024 ON D024_Id=T011_D024_Id WHERE T011_Id='{$r_acaoId}'";
		$resultT011 = mysql_query($sqlT011);
		$rowT011 = mysql_fetch_array($resultT011);
		
		$sqlD024 = "SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='{$r_D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$rowD024= mysql_fetch_array($resultD024);
		
		$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : $rowT011['D024_Id'];
		$nomeEmpresa = (isset($rowT011['D024_Nome_Empresa'])) ?  $rowT011['D024_Nome_Empresa'] : $rowD024['D024_Nome_Empresa'];
		
		
		// novo formulário
		$form = new Formulario('est006FormIncluirOrdemCompra');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Fornecedor' => array('name' => 'Procura_Fornecedor' , 'label' => 'Fornecedor', 'value'=>@$D024_Id, 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid2, 'length' => array(60,120), 'reject' => true),
					//'T011_D024_Id' => array('name' => 'T011_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id','WHERE D024_Flag_Ativo="S"'), 'length' => array(60,30)),
					'T011_C007_Id' => array('name' => 'T011_C007_Id','label'=>'Comprador', 'type' => 'select',
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome','', true, $g['usuarioAtual'])
					),
  					'T011_Observacao' => array('name' => 'T011_Observacao','label'=>'Observação', 'type' => 'textarea', 'length' => array(3,56)),
  					'T011_Prazo_Entrega' => array('name' => 'T011_Prazo_Entrega','label'=>'Prazo Entrega', 'type' => 'text', 'length' => array(30,60)),
  					'T011_Valor_Frete' => array('name' => 'T011_Valor_Frete','label'=>'Frete R$', 'type' => 'moeda', 'length' => array(13,60), 'align'=>'right', 'style'=>'float:right'),
  					'T011_Forma_Pagamento' => array('name' => 'T011_Forma_Pagamento','label'=>'Forma Pagamento', 'type' => 'text', 'length' => array(60,60)),
  					'T011_C004_Id' => array('name' => 'T011_C004_Id','label'=>'Empresa Atual', 'type' => 'hidden', 'value' => $g['empresaAtual'] ),
                    'T011C004Id' => array('name' => 'T011C004Id', 'label' => 'Empresa', 'type' => 'select', 'reject' => true, 'select'=> 
                        gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa','',false,$g['empresaAtual']),
                    ),
  					'T011_Data_Emissao' => array('name' => 'T011_Data_Emissao','label'=>'Data Emissão', 'type' => 'hidden', 'value' => date("Y/m/d") ),
  					'T011_Flag_Tipo' => array('name' => 'T011_Flag_Tipo','label'=>'Tipo', 'type' => 'hidden', 'value' => 'Estoque' ),
  					'T011_D024_Id' => array('name' => 'T011_D024_Id', 'label' => 'T011_D024_Id', 'type'=>'hidden', 'value' => @$D024_Id),
  					//'est006FormIncluirOrdemCompra_btn' => array('name' => 'est006FormIncluirOrdemCompra_btn', 'type'=>'submit','reload'=>false,'value'=>'Salvar','align' => 'right', 'style'=>'float:right;', 'reject'=>true),			
  					'GerarOCEstoque' => array('name' => 'GerarOCEstoque','label'=>'GerarOCEstoque', 'type' => 'hidden', 'value' => @$r_GerarOCEstoque,'reject' => true),
  					'GerarOCEmergencia' => array('name' => 'GerarOCEmergencia','label'=>'GerarOCEmergencia', 'type' => 'hidden', 'value' => @$r_GerarOCEmergencia,'reject' => true),
  					'GerarOCEstoqueRequisicao' => array('name' => 'GerarOCEstoqueRequisicao','label'=>'GerarOCEstoqueRequisicao', 'type' => 'hidden', 'value' => @$r_GerarOCEstoqueRequisicao,'reject' => true),
  					'gerarCotacaoRequisicaoTabCotacao' => array('name' => 'gerarCotacaoRequisicaoTabCotacao','label'=>'gerarCotacaoRequisicaoTabCotacao', 'type' => 'hidden', 'value' => @$r_gerarCotacaoRequisicaoTabCotacao,'reject' => true),
				),
			)
		);
		$formArray['campos']['geral']['est006FormIncluirOrdemCompra_btn'] = array('name' => 'est006FormIncluirOrdemCompra_btn', 'type'=>'submit','reload'=>false,'value'=>'Salvar','align' => 'right', 'style'=>'float:right;', 'reject'=>true);
		$formArray['campos']['geral']['est006PreCadastrarCliente'] = array('name' => 'est006PreCadastrarCliente', 'type' => 'button', 'reload' => false, 'value' => 'Pré-Cadastrar Fornecedor','reject' => true, 'style'=>'float:right;','align' => 'right');
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est006PreCadastrarCliente').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Cliente', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T139', [800,550]);
			});
			$('#Procura_Fornecedor_Pesquisa').val('{$nomeEmpresa}');
		} </script></div>
EOT;

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
                if ($form->campoValorEnviado('T011C004Id')) {
                    $form->campoSetarValor('T011_C004_Id', $form->campoValorEnviado('T011C004Id'), true);
                }
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
                    $C004_Id = $form->campoValorEnviado('T011_C004_Id');
                    mysql_query("UPDATE T012 SET T012_C004_Id='{$C004_Id}' WHERE T012_T011_Id='{$acaoId}'");
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert}
								//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
							$acaoId = $g['mysqlLastId'];
							if($form->campoValorEnviado('GerarOCEstoque') == true){
								$retorno = $EST006->insereItemEstoque($acaoId);
							}elseif($form->campoValorEnviado('GerarOCEmergencia') == true){
								$retorno = $EST006->insereItemEmergencia($acaoId);
							}elseif($form->campoValorEnviado('GerarOCEstoqueRequisicao') == true){
								$retorno = $EST006->insereItemEstoqueRequisicao($acaoId);
							}elseif($form->campoValorEnviado('gerarCotacaoRequisicaoTabCotacao') == true){
								$retorno = $EST006->insereItemEstoqueRequisicaoTabCotacao($acaoId, $x);
							}
							//var_dump($retorno);
							//die(); 
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Ordem de Compra - ID:    ' + '{$acaoId}', '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [950,600]);
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
                $form->campoSetarValor('T011C004Id', $form->campoValor('T011_C004_Id'));
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="est006FormIncluirOrdemCompra_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est006FormAprovacao':
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
		$form_tabela = 'T011';
		$form_campo_pk = 'T011_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est006FormAprovacao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est006/form/est006FormAprovacao/';

		//form editor
		$form_editor = array(
			'geral' => '{"T011_C007_Id_Aprovador":{"top":7,"left":16,"width":314,"hidden":0},"T011_Data_Aprovacao":{"top":30,"left":4,"width":75,"hidden":0},"est006FormAprovacao_btn":{"top":51,"left":240,"width":null,"hidden":0},"T011_Status_Aprovacao":{"top":53,"left":52,"width":110,"hidden":0}}' , 
		);

		// novo formulário
		$form = new Formulario('est006FormIncluirAprovacao');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T011_C007_Id_Aprovador' => array('name' => 'T011_C007_Id_Aprovador','label'=>'Autorizador', 'disabled'=>'disabled', 'extra' =>'style="width:330px"', 'type' => 'select',
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome','WHERE C007_Id='.$g['usuarioAtual'])
					),
					'T011_Data_Aprovacao' => array('name' => 'T011_Data_Aprovacao','label'=>'Data Aprovação', 'type' => 'data', 'value' => date('d/m/Y')),
					'T011_Status_Aprovacao' => array('name' => 'T011_Status_Aprovacao', 'label'=>'Status', 'type' => 'select', 'extra'=>'style="width:110px;"','select' => 
						array(
							array('title' => 'Pendente', 'value' => 1, 'selected' => true),
							array('title' => 'Aprovado', 'value' => 2),
							array('title' => 'Negado', 'value' => 3),
						)
					),
					'est006FormAprovacao_btn' => array('name' => 'est006FormAprovacao_btn', 'type'=>'submit','reload'=>false,'value'=>'Salvar','align' => 'right', 'style'=>'float:right;', 'reject'=>true),
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Status Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
  								{$alert}
								divRefresh('{$g['divId']}');
								divRefresh('{$g['divIdParent']}');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
  								{$alert}
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="est006FormAprovacao_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est006FormEditarOrdemCompra':
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
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
		
		$sqlT012 = "SELECT * FROM T012 
								  left join D009 on D009_Id=T012_D009_Id 
								  left join D049 on D049_Id=D009_D049_Id 
								  left join D082 on D082_Id=D049_D082_Id 
								  left join D001 on D001_Id=D009_D001_Id WHERE T012_Id='$acaoId'";
		$resultT012 = mysql_query($sqlT012);
		$linhaT012 = mysql_fetch_array($resultT012);
		
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T012';
		$form_campo_pk = 'T012_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est006FormEditarOrdemCompraBtnAdd', 'est006FormEditarOrdemCompraBtnCln', 'est006FormEditarOrdemCompraBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est006/form/est006FormEditarOrdemCompra/';

		//form editor
		$form_editor = array(
			'geral' =>'{"Procura_Produto":{"top":7,"left":37,"width":264,"hidden":0},"Marca":{"top":30,"left":44,"width":280,"hidden":0},"est006FormEditarOrdemCompraBtnCln":{"top":120,"left":224,"width":null,"hidden":0},"T012_Descricao_Produto":{"top":53,"left":25,"width":280,"hidden":0},"est006FormEditarOrdemCompraBtnAdd":{"top":120,"left":304,"width":null,"hidden":0},"T012_Quantidade":{"top":76,"left":12,"width":280,"hidden":0},"T012_Valor_Custo_Unitario":{"top":99,"left":10,"width":280,"hidden":0}}' , 
		);
		
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
			'campoProcuraProduto',																				// callback js
			$g['divId']																							// divId
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'value'=>@$linhaT012['D001_Codigo_Produto'], 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					// Campo normal (do banco)
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text','value'=>@$linhaT012['D082_Marca'], 'length' => array(50,30), 'reject' => true, 'readonly' => true),
					// Campo Pesquisa
					'T012_Descricao_Produto' => array('name' => 'T012_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(50,50)),
					'T012_Quantidade' => array('name' => 'T012_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(50,30)),
					'T012_Valor_Custo_Unitario' => array('name' => 'T012_Valor_Custo_Unitario', 'label' => 'Valor Unitário', 'type' => 'moeda', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T012_T011_Id' => array('name' => 'T012_T011_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T011_Id),
					'T012_Codigo_Produto' => array('name' => 'T012_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'T012_D009_Id' => array('name' => 'T012_D009_Id', 'label' => '', 'type' => 'hidden'),
				)
			)
		);
        $form = new Formulario('est006FormEditarOrdemCompra');
        // Botões dentro do Formulário
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est006FormEditarOrdemCompraBtnNew'] = array('name' => 'est006FormEditarOrdemCompraBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est006FormEditarOrdemCompraBtnCln'] = array('name' => 'est006FormEditarOrdemCompraBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est006FormEditarOrdemCompraBtnAdd'] = array('name' => 'est006FormEditarOrdemCompraBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$EST006->insereItem($form, $r_acaoId);
						// Atualiza o GRID e da um reload no FORM
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$EST006->insereItem($form, $g['mysqlLastId']);
						// Atualiza o GRID e da um reload no FORM
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
		
	break;
	
	case 'est006FormAplicarDesconto':
	
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$T011_Id = (isset($r_T011_Id)) ? $r_T011_Id : false;
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
		$form_tabela = 'T011';
		$form_campo_pk = 'T011_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est006FormAplicarDesconto';
		$enviar_acaoHref = '/est/est006/form/est006FormAplicarDesconto/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T011_Id' => array('name' => 'T011_Id', 'label' => 'T003_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $T011_Id),
     				'Desconto' => array('name' => 'Desconto', 'label' => 'Desconto %', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est006FormTotais2');
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
				$retorno = $EST006->aplicarDesconto($form);
				
				if($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Desconto Aplicado com sucesso!');	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}', true);
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$botoesDir .= '<button type="submit" id="est006FormAplicarDesconto">' . (empty($r_acaoId) ? 'Executar' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'est006FormAplicarIcms':
	
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$T011_Id = (isset($r_T011_Id)) ? $r_T011_Id : false;
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
		$form_tabela = 'T011';
		$form_campo_pk = 'T011_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est006FormAplicarIcms';
		$enviar_acaoHref = '/est/est006/form/est006FormAplicarIcms/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T011_Id' => array('name' => 'T011_Id', 'label' => 'T003_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $T011_Id),
     				'ICMS' => array('name' => 'ICMS', 'label' => 'Aplicar ICMS', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est006FormTotais2');
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
				$retorno = $EST006->aplicarICMS($form);
				
				if($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'ICMS Aplicado com sucesso!');	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}', true);
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$botoesDir .= '<button type="submit" id="est006FormAplicarIcms">' . (empty($r_acaoId) ? 'Executar' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'est006FormTotalOrdemCompra':
	
	
		//echo "<label style=\"font-weight: bold;\">Total Ordem Compra</label>";
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
		$form_tabela = 'T011';
		$form_campo_pk = 'T011_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';

		//form editor
		$form_editor = array(
			'geral' => '{"T011_Valor_Total_Base_ICMS_disabled":{"top":4,"left":4,"width":225},"T011_Valor_Total_ICMS_disabled":{"top":26,"left":7,"width":225},"T011_Valor_Total_Base_IPI_disabled":{"top":48,"left":18,"width":225},"T011_Valor_Total_IPI_disabled":{"top":70,"left":21,"width":225},"T011_Valor_Total_Custo_disabled":{"top":92,"left":34,"width":225}}' , 
		);
		
		$cSql_T012=mysql_query("select T012_Id from T012 where T012_T011_Id='$r_acaoId'");
		while ($mT012=mysql_fetch_array($cSql_T012)) {
		      mysql_query("call T012_Gravar_Totalizacao('$mT012[T012_Id]')");
		}
		mysql_query("call T011_Gravar_Totalizacao($r_acaoId);");
		
		
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T011_Valor_Total_Base_ICMS' => array('name' => 'T011_Valor_Total_Base_ICMS', 'label' => 'Base ICMS', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
					'T011_Valor_Total_ICMS' => array('name' => 'T011_Valor_Total_ICMS', 'label' => 'Total ICMS', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T011_Valor_Total_Base_IPI' => array('name' => 'T011_Valor_Total_Base_IPI', 'label' => 'Base IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T011_Valor_Total_IPI' => array('name' => 'T011_Valor_Total_IPI', 'label' => 'Total IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T011_Valor_Total_Custo' => array('name' => 'T011_Valor_Total_Custo', 'label' => 'Total', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
				)
			)
		);
        $form = new Formulario('form-esr006FormTotais');
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

	break;
	
	case 'est006FormPagamento':
			$acaoId = (isset($r_T011_Id)) ? $r_T011_Id : false; 
			$T011_Id = (isset($r_T011_Id)) ? $r_T011_Id : false;

			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado		

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
			$form_tabela = 'T011';
			$form_campo_pk = 'T011_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'est006FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est006/form/est006FormPagamento/';

			//form editor
			$form_editor = array(
				'geral' =>'{"Total_Parcelas":{"top":7,"left":16,"width":164,"hidden":0},"primeiroVencimentoData":{"top":30,"left":192,"width":75,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":16,"width":105,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":176,"width":40,"hidden":0},"diaFixo":{"top":53,"left":192,"width":40,"hidden":0},"selecionaDias":{"top":53,"left":6,"width":105,"hidden":0},"IntervalorDias":{"top":53,"left":176,"width":40,"hidden":0},"est006FormPagamento_btn":{"top":74,"left":240,"width":null,"hidden":0},"est006FormAdicionarParcela_btn":{"top":74,"left":368,"width":null,"hidden":0},"Portador_D031_Id":{"top":76,"left":17,"width":165,"hidden":0}}' , 
			);

			// novo formulário
			$form = new Formulario('est006FormPagamento');
			
			$select = array();
			$select[] = array('value' => 'A_COMBINAR', 'title' => 'SEM PARCELAS');
			$T011=mysql_query("select * from T011 where T011_Id='{$T011_Id}'");
			$mT011=mysql_fetch_array($T011);
			$valorTotal = gCorrigeNumero($mT011['T011_Valor_Total_Custo']);
			for ($i=1; $i<=24; $i++) {
				$valorParcela=$EST006->calcularParcelaCombo($i,$mT011['T011_Valor_Total_Custo']);
				if($i=='1'){
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . "x" . " - " .$valorParcela." - Total: ".$valorTotal, 'selected'=>true);
				}else{
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . "x" . " - " .$valorParcela." - Total: ".$valorTotal);
				}
			}
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'extra' => 'style="width:164px;"', 'select' => $select, 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"', 'reject' => true, 'length' => array(5,8), 'align' => 'right', 'readonly'=>'readonly'),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select', 'extra' => 'style="width:105px;"','select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => true),
							array('title' => 'Data', 'value' => 'Data'),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,4), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'type' => 'data', 'reject' => true, 'align' => 'right','extra'=>'style="margin-left:-8px;"'),												
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true, 'extra' => 'style="width:105px;"','select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => true),
							array('title' => 'Dia Fixo', 'value' => 'F'),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,2), 'align' => 'right','extra'=>'style="margin-left:-8px;"'),
						'Portador_D031_Id' => array('name' => 'D031_Id','label'=>'Portador', 'extra'=>'style="width:165px;"', 'type' => 'select', 'reject'=>true, 'select' =>
							gGeraSelect('D031', 'D031_Id', 'D031_Portador','order by D031_Portador')
						),
						'est006FormPagamento_btn' => array('name' => 'est006FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'est006FormAdicionarParcela_btn' => array('name' => 'est006FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
						//'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
						//'est006SalvarObservacao_btn' => array('name' => 'est006SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'style' => 'float:right; margin-right:120px; margin-top:15px;', 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'T011_Id' => array('name' => 'T011_Id', 'label' => '', 'type' => 'hidden', 'value' => @$T011_Id, 'reject' => true),
					)
				)
			);

		$form->definirFormularioArray($formArray);
			     
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est006FormAdicionarParcela_btn').unbind('click');
				$('#est006FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est006/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh("{$r_divReload}");
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#est006SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/est006/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T011_Id=' + encodeURIComponent('{$T011_Id}') + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#{$g['divId']} #selecionaDias').change(function() {
					if ($(this).val() == 'I') {
						$('#{$g['divId']} #diaFixo').val('').hide();
						$('#{$g['divId']} #IntervalorDias').show();
						$('#{$g['divId']} #IntervalorDias').focus();
					} else {
						$('#{$g['divId']} #IntervalorDias').val('').hide();
						$('#{$g['divId']} #diaFixo').show();
						$('#{$g['divId']} #diaFixo').focus();
					}
				});
				$('#{$g['divId']} #selecionaDias').trigger('change');
				
				$('#{$g['divId']} #selecionaPrimeiroVencimento').change(function() {
					if ($(this).val() == 'Dias') {
						$('#{$g['divId']} #primeiroVencimentoData').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoNum').show();
						$('#{$g['divId']} #primeiroVencimentoNum').focus();
					} else {
						$('#{$g['divId']} #primeiroVencimentoNum').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoData').show();
						$('#{$g['divId']} #primeiroVencimentoData').focus();
					}
				});
				$('#{$g['divId']} #selecionaPrimeiroVencimento').trigger('change');	

				
				// calcula e mostra o valor das parcelas (simulação)
				/**
				var changeHandler = function() {					
					$.getJSON('/ven/est006/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {															
							//divRefresh("{$r_divReload}");
							$('#{$g['divId']} #valorParcelaSimulacao').val(request.data);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", request.data, buttons)
						}
					});										
				}	
				$('#{$g['divId']} #Total_Parcelas').change(changeHandler).keyup(changeHandler);
				*/								
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
					// Gerar Boleto			
					$retorno = $EST006->atualizarParcelas($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       //divRefresh("{$g['divId']}", true);
                                       divRefresh('{$form->campoValorEnviado('divReload')}', true);
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
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

	break;

	case 'est006FormAgregados':
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
		$form_tabela = 'T210';
		$form_campo_pk = 'T210_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est006FormAgregadosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est006/form/est006FormAgregados/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T210_T209_Id' => array('name' => 'T210_T209_Id', 'label' => 'Tipo', 'type' => 'select', 'select' => 
						gGeraSelect('T209', 'T209_Id', 'T209_Nome', '', true)
					),
					'T210_Descricao' => array('name' => 'T210_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'T210_Valor_Unitario' => array('name' => 'T210_Valor_Unitario', 'label' => 'Valor', 'type' => 'moeda'),
					'T210_Observacao' => array('name' => 'T210_Observacao', 'label' => 'Observação', 'type' => 'textarea'),
					'T210_T012_Id' => array('name' => 'T210_T012_Id', 'label' => '', 'type' => 'hidden', 'value' => $_REQUEST['T012_Id']),
				)
			)
		);
		$formArray['campos']['geral']['est006FormAgregadosBtn'] = array('name' => 'est006FormAgregadosBtn', 'type'=>'submit', 'reload'=>false, 'value' => !empty($r_acaoId) ? 'Alterar' : 'Incluir', 'align' => 'right', 'style'=>'float:right;', 'reject'=>true);
        $form = new Formulario('est006FormAgregados');
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
								divRefresh('{$r_divReload}');
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
	  							});
								divRefresh('{$r_divReload}');
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}');
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
	break;
	
	
	}
?>
