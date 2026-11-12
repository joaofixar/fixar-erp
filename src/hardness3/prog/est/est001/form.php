<?php
namespace hardness;
require_once 'form_func-js.php';
require_once("bibliotecas/classes/EST001.php");
$EST001 = new EST001();

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	
	case 'est001FormDivergenciasEstoque':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
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
		$form_tabela = 'T029';
		$form_campo_pk = 'T029_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est001FormDivergenciasEstoqueBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est001/form/est001FormDivergenciasEstoque/';

		//form editor
		$form_editor = array('geral' => '{"T029_Descricao":{"top":30,"left":18,"height":62,"width":242,"hidden":0},"est001FormDivergenciasEstoqueBtn":{"top":120,"left":80,"width":null,"hidden":0},"T029_Data":{"top":7,"left":45,"width":75,"hidden":0}}' , );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T029_Descricao' => array('name' => 'T029_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(3,40)),
					'T029_Data' => array('name' => 'T029_Data', 'label' => 'Data', 'type' => 'data', 'length' => array(10,30)),
					'est001FormDivergenciasEstoqueBtn' => array('name' => 'est001FormDivergenciasEstoqueBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar','align' => 'right', 'style' => 'float:right;', 'reject' => true),
					// Campo normal (do banco)
					'T029_Id' => array('name' => 'T029_Id', 'label' => 'Id', 'type' => 'hidden', 'value'=>$acaoId),
					'T029_C004_Id' => array('name' => 'T029_C004_Id', 'label' => 'Empresa Atual', 'type' => 'hidden', 'value'=>$g['empresaAtual']),
					'T029_Flag_Ok' => array('name' => 'T029_Flag_Ok', 'label' => 'Flag Ok', 'type' => 'hidden', 'value'=>'N'),
					'T029_C007_Id' => array('name' => 'T029_C007_Id', 'label' => 'Usuário Atual', 'type' => 'hidden', 'value'=>$g['usuarioAtual']),
				)
			)
		);
        $form = new Formulario('form-abc001FormNome');
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
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Divergências de Estoque - ID: ' + {$id}, '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent({$id}) + '&tabela={$form_tabela}', [950,600]);
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

		/*
		 * Botões
		 
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est001FormDivergenciasEstoqueBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		*/
	break;
	
	case 'est001FormDivergenciasEstoqueIncluirProduto':
				
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		
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
		$form_tabela = 'T030';
		$form_campo_pk = 'T030_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est001FormDivergenciasEstoqueIncluirProdutoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est001/form/est001FormDivergenciasEstoqueIncluirProduto/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Produto":{"top":7,"left":52,"width":184,"hidden":0},"Descricao_Produto":{"top":7,"left":344,"width":168,"hidden":0},"T030_Quantidade":{"top":30,"left":34,"width":200,"hidden":0},"T030_Flag_Entrada_Saida":{"top":30,"left":338,"width":176,"hidden":0},"alterarLocacao":{"top":28,"left":592,"width":null,"hidden":0},"T030_T066_Id":{"top":53,"left":2,"width":208,"hidden":0},"T030_T066_Id_Destino":{"top":53,"left":306,"width":175,"hidden":0},"T030_Observacao":{"top":76,"left":29,"width":472,"hidden":0},"est001FormDivergenciasEstoqueIncluirProdutoBtn":{"top":74,"left":608,"width":null,"hidden":0}}');

		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }
		
		//$pesquisaGridExtra = "left join D049 on D049_Id=D009_D049_Id left join D001 on D001_Id=D049_D001_Id left join D082 on D082_Id=D049_D082_Id left join d144 on d144_d049_id = D049_Id";
		//$pesquisaGridExtra .= " where D009_C004_Id='{$g['empresaAtual']}' AND D001_Flag_Ativo='S'";
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
			{$filtro}
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;
		
		/*$pesquisaGrid = array(
			'D009',																								// tabela
			'D009_Id,D144_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D049_Modelo,D009_Quantidade_Estoque_Real',				// campos
			$pesquisaGridExtra,																					// extras sql
			'Consulta Rápida de Produtos',																		// título da janela
			'Referência,Descrição do Produto,Marca,Sub-Item,Estoque Real',												// descrição dos campos
			'campoProcuraProdutoDivergencia',																				// callback js
			$g['divId'],																						// divId
			$r_acaoId																							// (opcional) parametro extra (passado para o callback js)
		);*/
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id,D144_Id',																										      // chave primaria
			'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||"" as retornaPrecoVenda||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T003_Id . '" as T003_Id||D001_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Foto,Código,Sub-item,Descrição,Referência,Marca,UN,Preço,Preço 2,Preço 3,Estoque Real,Estoque Líq,Est. Loja,Estoque Locação,Estoque Fora,Preço,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,D001_Id',	  // descrição dos campos
			'campoProcuraProdutoDivergencia',																					              // callback js
			$g['divId'],																									              // divId
			$r_acaoId,   																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
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
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false)
			),
			'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido' // Campos que são ignorados na cláusula where do pesquisaGrid
		);

		$r_T029_Id = isset($r_est001FormDivergenciasEstoqueIncluirProduto) ? $r_est001FormDivergenciasEstoqueIncluirProduto['T030_T029_Id'] : $r_T029_Id;
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(30,30), 'reject' => true),
					'Descricao_Produto' => array('name' => 'Descricao_Produto' , 'label' => 'Descrição', 'type' => 'text','align' => 'right', 'length' => array(30,30), 'reject' => true),
					'T030_Quantidade' => array('name' => 'T030_Quantidade' , 'label' => 'Quantidade', 'type' => 'moeda','casasDecimais' => '5', 'length' => array(10,30)),
					'T030_Flag_Entrada_Saida' => array('name' => 'T030_Flag_Entrada_Saida' , 'label' => 'Operação', 'type' => 'select','align' => 'right',
						'select' => array(
						 	array('title' => '...','value' => ''),
						 	array('title' => 'Credita','value' => 'E'),
						 	array('title' => 'Debita','value' => 'S'),
						 	array('title' => 'Balanço','value' => 'B'),
							array('title' => 'Não altera','value' => 'N'),
						 	array('title' => 'Transferência','value' => 'T'),
						)
					),
					/*'T030_Locacao' => array('name' => 'T030_Locacao' , 'label' => 'Locação', 'type' => 'pesquisa','length' => array(30,60),
						'select' => gGeraSelect('D004','T066_Id','D004_Local','where D004_C004_Id='.$g['empresaAtual'].' order by D004_Local',true)
					),*/
					/*'T003_D013_Id' => array('name' => 'T003_D013_Id', 'label' => 'Contato', 'type' => 'select', 'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente",true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente",true)
					),*/
					'T030_Observacao' => array('name' => 'T030_Observacao' , 'label' => 'Observação', 'type' => 'text','align' => 'right','length' => array(30,60)),
					'T030_Codigo_Produto' => array('name' => 'T030_Codigo_Produto' , 'label' => 'Produto', 'type' => 'hidden'),
					'est001FormDivergenciasEstoqueIncluirProdutoBtn' => array('name' => 'est001FormDivergenciasEstoqueIncluirProdutoBtn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir','align' => 'right', 'reject' => true),
					'T030_D009_Id' => array('name' => 'T030_D009_Id' , 'label' => 'T030_D009_Id', 'type' => 'hidden'),
					//'T030_Valor_Custo_Unitario_Origem' => array('name' => 'T030_Valor_Custo_Unitario_Origem' , 'label' => 'T030_Valor_Custo_Unitario_Origem', 'type' => 'text'),
					'T030_C007_Id' => array('name' => 'T030_C007_Id' , 'label' => 'T030_C007_Id', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'T030_C004_Id' => array('name' => 'T030_C004_Id' , 'label' => 'T030_C004_Id', 'type' => 'hidden','value' => $g['empresaAtual']),
					'T030_T029_Id' => array('name' => 'T030_T029_Id' , 'label' => 'T030_T029_Id', 'type' => 'hidden','value' => $r_T029_Id),
					'T030_D144_Id' => array('name' => 'T030_D144_Id' , 'label' => 'T030_D144_Id', 'type' => 'hidden'),
					'T030_Data_Inclusao' => array('name' => 'T030_Data_Inclusao' , 'label' => 'T030_Data_Inclusao', 'type' => 'hidden','value' => date('Y-m-d')),
					'divReload' => array('name' => 'divReload' , 'label' => 'divReload', 'type' => 'hidden','value' => @$r_divReload,'reject' => true),
					'T030_T066_Id' => array('name' => 'T030_T066_Id' , 'label' => 'Locação Origem', 'type' => 'select',
						'select' => array(array('title' => '...','value' => ''))
					),
					'T030_T066_Id_Destino' => array('name' => 'T030_T066_Id_Destino' , 'label' => 'Locação Destino', 'reject' => true, 'type' => 'select',
						'select' => array(array('title' => '...','value' => ''))
					),
					'alterarLocacao' => array('name' => 'alterarLocacao', 'type'=>'button', 'reload' => false, 'value' => 'Alterar Locação', 'reject' => true),
				)
			)
		);
		
		$form = new Formulario('est001FormDivergenciasEstoqueIncluirProduto');
		$form->definirFormularioArray($formArray);
		$calculadoraQtdNavegaTab = (!isset($g['C031']['calculadoraQtdNavegaTab']) || $g['C031']['calculadoraQtdNavegaTab'] !== 'N') ? 'true' : 'false';

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var formCalculadoraQuantidade = $('#{$g['divId']}');
				formCalculadoraQuantidade.unbind('calculadoraQuantidade:resultado.est001').bind('calculadoraQuantidade:resultado.est001', function(_, medidas) {
					formCalculadoraQuantidade.find('#T030_Quantidade').val(medidas.resultado.replace('.', ',')).trigger('change');
				});
				$.getScript('/hardness3/static/js/calculadoraQuantidade.js').done(function() {
					configurarCalculadoraQuantidade({participarTab: {$calculadoraQtdNavegaTab}});
					inicializarCalculadoraQuantidade({formulario: formCalculadoraQuantidade, formId: '{$g['divId']}', campo: '#T030_Quantidade', divIdRoot: '{$g['divIdRoot']}', divIdParent: '{$g['divId']}' });
				});
				$('#{$g['divId']} #T030_Flag_Entrada_Saida').change(function() {
					if($(this).val() == 'T') {
						$('#{$g['divId']} #T030_T066_Id_Destino').closest('.formEditor').show();
						setTimeout(function() {
							$('#{$g['divId']} #T030_T066_Id_Destino').focus();
						}, 100);
					} else {
						$('#{$g['divId']} #T030_T066_Id_Destino').closest('.formEditor').hide();
					}
				});
				$('#{$g['divId']} #T030_Flag_Entrada_Saida').trigger('change');

				$('#alterarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/est/est003/content/est003ContentLocacoes/','&acaoId=' + encodeURIComponent($('#alterarLocacao').attr('D009_Id')) + '&D009_Id=' + encodeURIComponent($('#alterarLocacao').attr('D009_Id')) + '&divergencia=true', [700,350]);
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
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
							} </script></div> 
EOT;
					}
				} else {
					// Insert
					$locacaoDestino = $form->campoValorEnviado('T030_T066_Id_Destino');
					
					$retorno = $EST001->incluirTransferenciaEstoque($form, $locacaoDestino);
					if($retorno === true){
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', $retorno);
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
	
	case 'est001FormDivergenciasEstoqueImportar':
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
		$form_tabela = 'D030';
		$form_campo_pk = 'D030';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est001FormDivergenciasEstoqueImportar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est001/form/est001FormDivergenciasEstoqueImportar/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est001FormDivergenciasEstoqueImportar');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoColetora'] 						= array('name' => 'uploadArquivoColetora', 'label' => 'Selecionar Arquivo', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['T029_Id'] 									= array('name' => 'T029_Id', 'type' => 'hidden', 'value' => @$r_T029_Id);
		$formArray['campos']['geral']['est001FormDivergenciasEstoqueImportar_btn'] 	= array('name' => 'est001FormDivergenciasEstoqueImportar_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Importar', 'align' => 'right', 'extra' => 'style="float: right;"', 'reject'=>true);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoColetora');
				$T029_Id = $form->campoValorEnviado('T029_Id');
				$retorno = $EST001->ImportarColetora($arquivo, $T029_Id);
				
				if ($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Arquivo importado com Sucesso!');	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							//divRefresh('{$g['divId']}');
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
}
?>