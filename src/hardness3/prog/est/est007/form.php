<?php
namespace hardness;    

require_once("form_func-js.php");
    
switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 03/01/2012  
	*/
	case 'est007FormCadastroMontagens':
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
		$form_tabela = 'T047';
		$form_campo_pk = 'T047_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est007FormCadastroMontagensBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est007/form/est007FormCadastroMontagens/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					// 'D131_D116_Id' => array('name' => 'D131_D116_Id', 'label' => 'Veículo', 'type' => 'select', 
						// 'select' =>  gGeraSelect('D116','D116_Id','D116_Modelo','',true),
					// ),
					'T047_C004_Id' => array('name' => 'T047_C004_Id', 'label' => 'EmpresaAtual', 'type' => 'hidden', 'value'=>$g['empresaAtual'], 'length' => array(2,37)),
					'T047_C007_Id' => array('name' => 'T047_C007_Id', 'label' => 'UsuarioAtual', 'type' => 'hidden', 'value'=>$g['usuarioAtual'], 'length' => array(2,37)),
					'T047_Data' => array('name' => 'T047_Data', 'label' => 'Data', 'type' => 'data', 'disabled'=>true, 'value'=>date("d/m/Y"), 'length' => array(15,37)),
					'T047_Observacao' => array('name' => 'T047_Observacao', 'label' => 'Observação', 'type' => 'textarea','length' => array(2,50)),
				)
			)
		);
        $form = new Formulario('form-est007FormPrincipal');
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
						$acaoId = $g['mysqlLastId'];
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Montagem - Nº ' + '{$acaoId}', '/est/est007/content/est007ContentMontagensEditarNovo/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [850,500]);
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
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est007FormCadastroMontagensBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est007TabMontagensFormIncluirProduto':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		
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
		$enviar_btnId = 'cad002FormSubsBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormMontagem/';
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		
		//$pesquisaGridExtra = "left join D001 on D001_Id=D049_D001_Id left join D024 on D024_Id=D049_D024_Id WHERE D001_Flag_Ativo='S'";		//$pesquisaGridExtra .= " where D009_C004_Id='{$g['empresaAtual']}'";
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id  
			left join D041 on D041_D049_Id=D049_Id  
			left join D009 on D009_D049_Id=D049_Id  
			left join D082 on D082_Id=D049_D082_Id  
			left join D037 on D037_Id=D001_D037_Id   
			left join D060 on D060_Id=D001_D060_Id
			left join C008 on C008_Id=D001_C008_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN D144 ON D144_D049_Id=D049_Id  
			left join C007 on C007_Id=D009_C007_Id_Comprador
			where (D009_C004_Id = '{$g['empresaAtual']}'  and D001_Flag_Ativo='S' and ifnull(D049_Modelo,'')!='0' and ifnull(D049_Flag_Ativo,'')!='N')  
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;
		
		/*$pesquisaGrid = array(
			'D049',																								// tabela
			'D049_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D024_Nome_Empresa',				// campos
			$pesquisaGridExtra,																					// extras sql
			'Consulta Rápida de Produtos',																		// título da janela
			'Referência,Descrição,Descrição Inglês,Fornecedor',													// descrição dos campos
			'campoProcuraProduto',																				// callback js
			$g['divId'],																						// divId
			$acaoId																								
		);*/
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D049_Id',																										      // chave primaria
			'D049_Id||D009_Id||C008_Id||D060_Id||D001_Codigo_Produto||D049_Modelo||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||"" as retornaPrecoVenda||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T003_Id . '" as T003_Id||D001_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Id,Dep,Setor,Código,Sub-item,Descrição,Referência,Marca,UN,Preço,Preço 2,Preço 3,Estoque Real,Estoque Líq,Est. Loja,Estoque Locação,Estoque Fora,Preço,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,D001_Id',	  // descrição dos campos
			'campoProcuraProduto',																					              // callback js
			$g['divId'],																									              // divId
			$acaoId,   																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%;font-weight:bold'),
				array('style' => 'font-weight:bold;white-space:normal;'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
				array('colunaAlinhar' => 'center', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
				array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
				array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVenda', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
				array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false)
			),
			'D049_Id||D009_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque_Liquido' // Campos que são ignorados na cláusula where do pesquisaGrid
		);
		
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Incluir Ítem', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					
					//'D017_Codigo_Antigo' => array('name' => 'D017_Codigo_Antigo', 'label'=>'Incluir', 'type' => 'text','length' => array(35,80)),
					//'cad002FormSubsBtn' => array('name' => 'cad002FormSubsBtn', 'type'=>'submit','reload'=>false,'value'=>'Incluir', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					//'D001_Id' => array('name' => 'D001_Id', 'value'=>$D001_Id, 'label'=>'Incluir', 'type' => 'hidden','length' => array(40,40)),
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
				//$msg = $CAD002->gravarSubstituicao($form);
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad002FormSubsBtn">Executar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'est007TabMontagensFormIncluirSubItem':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		
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
		//$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormSubsBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormMontagem/';
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		
		$pesquisaGridExtra = "left join D049 on D049_Id=D009_D049_Id left join D001 on D001_Id=D049_D001_Id left join D082 on D082_Id=D049_D082_Id";
		$pesquisaGridExtra .= " where D009_C004_Id='{$g['empresaAtual']}'";
		
		$pesquisaGrid = array(
			'D009',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca',											// campos
			$pesquisaGridExtra,																					// extras sql
			'Consulta Rápida de Produtos',																		// título da janela
			'Referência,Descrição do Produto,Fornecedor',														// descrição dos campos
			'campoProcuraProdutoSubItens',																		// callback js
			$g['divId'],																						// divId
			$acaoId																								
		);
		
		$form = new Formulario('form_est007TabMontagensFormIncluirSubItem');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'Procura_ProdutoSubItens' => array('name' => 'Procura_ProdutoSubItens' , 'label' => 'Incluir Sub-Item', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					
					//'D017_Codigo_Antigo' => array('name' => 'D017_Codigo_Antigo', 'label'=>'Incluir', 'type' => 'text','length' => array(35,80)),
					//'cad002FormSubsBtn' => array('name' => 'cad002FormSubsBtn', 'type'=>'submit','reload'=>false,'value'=>'Incluir', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					//'D001_Id' => array('name' => 'D001_Id', 'value'=>$D001_Id, 'label'=>'Incluir', 'type' => 'hidden','length' => array(40,40)),
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
				//$msg = $CAD002->gravarSubstituicao($form);
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
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
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad002FormSubsBtn">Executar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
}
?>