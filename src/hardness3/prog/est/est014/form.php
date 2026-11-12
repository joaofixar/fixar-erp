<?php
namespace hardness;
require_once('prog/est/est014/form_func-js.php');

require_once("bibliotecas/classes/EST012.php");
$EST012 = new EST012();
require_once("bibliotecas/classes/EST014.php");
$EST014 = new EST014();


/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: ven013tab02
	 */
	case 'est014FormRequisicoesdeCompraEditarIncluir':
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
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est014TabRequisicoesdeCompraEditarIncluirBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est014/form/est014FormRequisicoesdeCompraEditarIncluir/';
		
		// Definição do Formulário
		$formArray = array('campos' => array('geral' => array()));
		if(!empty($r_acaoId)){
			$formArray['campos']['geral']['T189_Descricao_Produto'] = array('name' => 'T189_Descricao_Produto', 'label' => 'Descrição Produto', 'type' => 'textarea', 'length' => array(3,50));
			$formArray['campos']['geral']['T189_Quantidade'] = array('name' => 'T189_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(10,50));
		}
        $form = new Formulario('est012FormNaoLiberar');
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
						$id = $g['mysqlLastId'];
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Divergências de Estoque - ID: ' + {$id}, '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent({$id}), [950,600]);
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
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		
		$sqlT189 = "SELECT T189_D009_Id FROM T189 WHERE T189_Id='{$r_acaoId}'";
		$resultT189 = mysql_query($sqlT189);
		$linhaT189 = mysql_fetch_array($resultT189);
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if(empty($linhaT189['T189_D009_Id'])){
			$botoesEsq .= '<button type="submit" id="est014AssociarProduto">Associar Produto</button>';
		}
		$botoesDir .= '<button type="submit" id="est014TabRequisicoesdeCompraEditarIncluirBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est014AssociarProduto').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Associar Produto', '/est/est014/content/est014ContentAssociarProduto/', '&T189_Id=' + encodeURIComponent('{$r_acaoId}'), [380,150]);
			});
		} </script></div>
EOT;
	break;
	
	case 'est014FormAssociarProduto':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T189_Id = (isset($r_T189_Id)) ? $r_T189_Id : false;
		
		$sqlT189 = "SELECT T189_Descricao_Produto FROM T189 WHERE T189_Id='{$T189_Id}'";
		$result = mysql_query($sqlT189);
		$linhaT189 = mysql_fetch_array($result);
		$desc = (isset($linhaT189['T189_Descricao_Produto'])) ? $linhaT189['T189_Descricao_Produto'] : false;
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
		$enviar_btnId = 'est014FormAssociarProduto_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est014/form/est014FormAssociarProduto/';
		
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
			'D001',																									// tabela
			'D009_Id',																								// chave primaria
			'floor(D001_Codigo_Produto),D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',	// campos
			$pesquisaGridExtra,																						// extras sql
			'Pesquisa de Produto',																					// título da janela
			'Código,Descrição,Marca,Un,Estoque',																	// descrição dos campos
			'campoProcuraProdutoAssociar',																			// callback js
			$g['divId'],																							// divId
			array($T189_Id)																							// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('est014FormAssociarProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50)),
					// Campos Hidden
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label' => 'D001_Codigo_Produto', 'type' => 'hidden'),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label' => 'D001_Descricao_Produto', 'type' => 'hidden'),
					'T189_Id' => array('name' => 'T189_Id', 'label' => 'T189_Id','value' => $T189_Id, 'type' => 'hidden'),
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
					$retorno = $EST014->est014associarProduto($form);
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
		$gerado = gGeraCamposForm($form, $tab, $T189_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesEsq .= '<button type="submit" id="est014PreCadProduto_Btn">Pré-cadastrar produto</button>';
		$botoesDir .= '<button type="submit" id="est014FormAssociarProduto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est014PreCadProduto_Btn').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T189_Descricao_Produto=' + encodeURIComponent('{$linhaT189['T189_Descricao_Produto']}'), [600,300]);											
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
	
	case 'est014FormRequisicaoCompra':
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

		$form_editor = array(
			'geral' => '{"Procura_Produto":{"top":7,"left":37,"width":324,"hidden":0},"T189_Descricao_Produto":{"top":30,"left":25,"width":324,"hidden":0},"Procura_Fornecedor":{"top":53,"left":18,"width":324,"hidden":0},"T189_Flag_Tipo":{"top":76,"left":54,"width":159,"hidden":0},"T189_Quantidade":{"top":99.00003051757812,"left":19,"width":154,"hidden":0},"T189_Quantidade_Estoque_Atual":{"top":122.00003051757812,"left":9,"width":154,"hidden":0},"T189_Observacao":{"top":145.00003051757812,"left":14,"height":39,"width":302,"hidden":0}}'
		);

		

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est014FormRequisicaoCompraBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est014/form/est014FormRequisicaoCompra/';
		
		// Pesquisa de proodutos
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S')
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
		'D001',																														// tabela
			'D009_Id',																												// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtra,																										// extras sql
			'Pesquisa de Produto',																									// título da janela
			'Código,Descrição,Descrição Inglês,Marca,Un,Estoque',																	// descrição dos campos
			'campoProcuraProdutoRequisitar',																						// callback js
			$g['divId']																												// divId
		);
		
		// Pesquisa de Fornecedores
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
			'Código,Fornecedor,Nome_Fantasia,Cidade,UF,CNPJ,DDD,Telefone',														// descrição dos campos
			'est014ProcuraFornecedor',																					// callback js
			$g['divId'],																									// divId
			false,					                                                                                          // parametro extra
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
		
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,60), 'reject' => true),
					'T189_Descricao_Produto' => array('name' => 'T189_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(60,60)),
					'Procura_Fornecedor' => array('name' => 'Procura_Fornecedor' , 'label' => 'Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid2, 'length' => array(60,60), 'reject' => true),
					'T189_Flag_Tipo' => array('name' => 'T189_Flag_Tipo','label'=>'Tipo', 'type' => 'select',
						'select' => array(
							array('title' => 'Apenas Cotar', 'value' => 'C'),
							array('title' => 'Comprar', 'value' => 'P'),
							array('title' => 'Reposição de Estoque', 'value' => 'E'),
							array('title' => 'Cancelado', 'value' => 'R'),							
						)
					),
					'T189_Quantidade' => array('name' => 'T189_Quantidade', 'label' => 'Quantidade', 'type' => 'text', 'length' => array(26,30)),
					'T189_Quantidade_Estoque_Atual' => array('name' => 'T189_Quantidade_Estoque_Atual', 'label' => 'Estoque atual', 'type' => 'text', 'length' => array(26,30)),
					'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(2,57)),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => 'Observação', 'type' => 'hidden', 'length' => array(2,47)),
					'T189_D024_Id' => array('name' => 'T189_D024_Id', 'label' => 'Observação', 'type' => 'hidden', 'length' => array(2,47)),
					'T189_Data_Criado' => array('name' => 'T189_Data_Criado', 'label' => 'Data', 'type' => 'hidden', 'value'=>date("Y-m-d H:i:s"), 'length' => array(2,47)),
					'T189_Detalhes' => array('name' => 'T189_Detalhes', 'label' => 'Detalhes', 'type' => 'hidden'),
					'T189_C007_Id' => array('name' => 'T189_C007_Id', 'label' => 'Usuário', 'type' => 'hidden', 'value'=>$g['usuarioAtual']),
				)
			)
		);
        $form = new Formulario('form-est014FormRequisicaoCompra');
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
				$form->campoSetarValor('T189_Detalhes', 'Requisição PC - Fornecedor: '.$form->campoValorEnviado('Procura_Fornecedor').' - Estoque atual: ' . $form->campoValorEnviado('T189_Quantidade_Estoque_Atual'), true);
				// Insert ou Update?
				if (!empty($r_acaoId)) {
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
		$botoesDir .= '<button type="submit" id="est014FormRequisicaoCompraBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est014FormProdutosOrdemServico':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$T128_Id = (isset($r_T128_Id)) ? $r_T128_Id : false;
		
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
		$form_tabela = 'T129';
		$form_campo_pk = 'T129_Id';
		$form_parent = 'T128_Id';
		$form_parent_valor = $r_T128_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est018FormProdutosOSBtnAdd', 'est014FormProdutosOSBtnCln', 'est014FormProdutosOSBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est014/form/est014FormProdutosOrdemServico/';

		//form editor
		$form_editor = array('geral' => '{"T129_Descricao_Produto":{"top":30,"left":9,"width":424,"hidden":0},"Procura_Produto":{"top":7,"left":21,"width":408,"hidden":0},"T129_Quantidade":{"top":76,"left":3,"width":424,"hidden":0},"D082_Marca":{"top":53,"left":28,"width":424,"hidden":0},"est008FormBtnCadastrarProduto":{"top":97,"left":176,"width":null,"hidden":0},"est008FormProdutosBtnCln":{"top":97,"left":352,"width":null,"hidden":0},"est008FormProdutosBtnAdd":{"top":97,"left":448,"width":null,"hidden":0}}');
		
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S')
			AND D049_Flag_Tipo='MD'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
		'D001',																														// tabela
			'D009_Id',																												// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtra,																										// extras sql
			'Pesquisa de Produto',																									// título da janela
			'Código,Descrição,Origem,Marca,Un,Estoque',																	// descrição dos campos
			'campoProcuraProdutoOS',																									// callback js
			$g['divId']																												// divId
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(80,30), 'reject' => true),
					'D082_Marca' => array('name' => 'D082_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(80,30), 'reject' => true, 'readonly' => true),
					// Campo Pesquisa
					'T129_Descricao_Produto' => array('name' => 'T129_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(80,50)),
					'T129_Quantidade' => array('name' => 'T129_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(80,50)),
					//'T059_Valor_Custo_Unitario' => array('name' => 'T059_Valor_Custo_Unitario', 'label' => 'Valor Unitário', 'type' => 'moeda','value' => '0,00', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T129_Codigo_Produto' => array('name' => 'T129_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'T129_D009_Id' => array('name' => 'T129_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T129_C004_Id' => array('name' => 'T129_C004_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value'=>$g['empresaAtual']),
					'T129_T128_Id' => array('name' => 'T129_T128_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value'=>$r_T128_Id),
					'T129_Quantidade_Estoque' => array('name' => 'T129_Quantidade_Estoque', 'label' => 'C004_Id', 'type' => 'hidden', 'value'=>$r_T128_Id),
				)
			)
		);
        $form = new Formulario('est014FormProdutosOS');
        // Botões dentro do Formulário
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est008FormProdutosBtnNew'] = array('name' => 'est008FormProdutosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est018FormProdutosOSBtnAdd'] = array('name' => 'est018FormProdutosOSBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est014FormProdutosOSBtnCln'] = array('name' => 'est014FormProdutosOSBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est008FormBtnCadastrarProduto'] = array('name' => 'est008FormBtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est008FormBtnCadastrarProduto').unbind('click');
				$('#est008FormBtnCadastrarProduto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,430]);
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						/**
						* Função para totalizar os itens
						*/
						
						// Atualiza o GRID e da um reload no FORM
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
						/**
						* Função para totalizar os itens
						*/ 
						// Atualiza o GRID e da um reload no FORM
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
		
	break;

	case 'est014FormCabecalhoOS':
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
		$form_tabela = 'T128';
		$form_campo_pk = 'T128_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est014FormCabecalhoOSIncluirBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est014/form/est014FormCabecalhoOS/';

		//form editor
        $form_editor = array(
            'geral' => '{"T128_D024_Id":{"top":7,"left":19,"width":277,"hidden":0},"T128_C007_Id":{"top":30,"left":21,"width":303,"hidden":0},"T128_D006_Id":{"top":53,"left":47,"width":303,"hidden":0},"T128_Observacao":{"top":76,"left":14,"height":60,"width":296,"hidden":0},"est014FormCabecalhoOSIncluirBtn":{"top":143,"left":320,"width":null,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array('campos' => array('geral' => array()));

		$formArray['campos']['geral']['T128_D024_Id'] = array('name' => 'T128_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa',''), 'length' => array(80,30));
		$formArray['campos']['geral']['T128_C007_Id'] = array('name' => 'T128_C007_Id', 'label'=>'Comprador','type' => 'select', 'select' => 
															gGeraSelect('C007','C007_Id','C007_Nome','ORDER BY C007_Nome',true, $g['usuarioAtual']),
														);
		$formArray['campos']['geral']['T128_D006_Id'] = array('name' => 'T128_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
															gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao','ORDER BY D006_Codigo_CFOP',true, $g['C031']['CFOPPadraoOrdemServico']),
														);
		$formArray['campos']['geral']['T128_D022_Id'] = array('name' => 'T128_D022_Id', 'label'=>'Transportadora','type' => 'select', 'select' => 
															gGeraSelect('D022','D022_Id','D022_Nome_Empresa','ORDER BY D022_Nome_Empresa',true),
														);
		$formArray['campos']['geral']['T128_Observacao'] = array('name' => 'T128_Observacao','label'=>'Observação', 'type' => 'textarea');
		$formArray['campos']['geral']['T128_C004_Id'] = array('name' => 'T128_C004_Id','label'=>'T128_C004_Id', 'type' => 'hidden', 'value'=>$g['empresaAtual']);
		$formArray['campos']['geral']['T128_Data_Emissao'] = array('name' => 'T128_Data_Emissao','label'=>'T128_Data_Emissao', 'type' => 'hidden', 'value'=>date('Y-m-d'));
		$formArray['campos']['geral']['est014FormCabecalhoOSIncluirBtn'] = array('name' => 'est014FormCabecalhoOSIncluirBtn', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

        $form = new Formulario('est012FormNaoLiberar');
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
										
									}, 150);
  								});
								//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								{$alert}
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$id = $g['mysqlLastId'];
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Ordem de Serviço - Número: ' + {$id}, '/est/est014/content/est014contentOrdemServico/', '&T128_Id=' + encodeURIComponent('{$id}') + '&tabela={$from}', [900,500]);
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
		
		$sqlT189 = "SELECT T189_D009_Id FROM T189 WHERE T189_Id='{$r_acaoId}'";
		$resultT189 = mysql_query($sqlT189);
		$linhaT189 = mysql_fetch_array($resultT189);
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		if(empty($linhaT189['T189_D009_Id'])){
		//	$botoesEsq .= '<button type="submit" id="est014AssociarProduto">Associar Produto</button>';
		}
		//$botoesDir .= '<button type="submit" id="est014FormCabecalhoOSIncluirBtn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>