<?php
namespace hardness;
/**
* Weiberlan
* 24/02/2012
*/
 
/**Classe
* 
* require_once("bibliotecas/classes/est008.php");
* $est008 = new est008();
*/
require_once('prog/est/est008/form_func-js.php');
require_once("bibliotecas/classes/EST008.php");
require_once("bibliotecas/classes/CAD002.php");
$EST008 = new EST008();
$CAD002 = new CAD002();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est008FormCadastroCotacaoCompra':
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
		$form_tabela = 'T010';
		$form_campo_pk = 'T010_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est008FormCadastroCotacaoCompra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormCadastroCotacaoCompra/';

		// form editar
		$form_editor = array('geral' => '{"T010_C007_Id":{"top":7,"left":69,"width":109,"hidden":0},"T010_Flag_Marca_Individual":{"top":190.99996948242188,"left":37,"width":257,"hidden":1},"T010_Flag_Quantidade_Individual":{"top":167.99996948242188,"left":12,"width":285,"hidden":1},"T010_Observacao":{"top":30,"left":62,"height":62,"width":342,"hidden":0},"est008FormCadastroCotacaoCompra_btn":{"top":120,"left":304,"width":null,"hidden":0},"T010_Flag_Cotacao_Finalizada":{"top":99,"left":40,"width":13,"hidden":0}}' , );

		// novo formulário
		$form = new Formulario('est008FormCadastroCotacaoCompra');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T010_C007_Id' => array('name' => 'T010_C007_Id','label'=>'Comprador', 'type' => 'select',
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','',true,$g['usuarioAtual'])
					),
					'T010_Flag_Marca_Individual' => array('name' => 'T010_Flag_Marca_Individual','label'=>'Marca do Produto', 'type' => 'select',
						'select' => array(
							array('title' => '1-Utilizar a marca PRINCIPAL da cotação', 'value' => 'N'),
							array('title' => '2-Utilizar marca por FORNECEDOR', 'value' => 'S'),
						)
					),
					'T010_Flag_Quantidade_Individual' => array('name' => 'T010_Flag_Quantidade_Individual','label'=>'Quantidade do produto', 'type' => 'select',
						'select' => array(
							array('title' => '1-Utilizar a quantidade PRINCIPAL da cotação', 'value' => 'N'),
							array('title' => '2-Utilizar quantidade por FORNECEDOR', 'value' => 'S'),
						)
					),
					'T010_Observacao' => array('name' => 'T010_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(3,60)),
					'T010_C004_Id' => array('name' => 'T010_C004_Id', 'label' => 'EmpresaAtual', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T010_Data' => array('name' => 'T010_Data', 'label' => 'T010_Data', 'type' => 'hidden', 'value' => date('Y-m-d')),
					'T010_Flag_Cotacao_Finalizada' => array('name' => 'T010_Flag_Cotacao_Finalizada', 'label' => 'Finalizar cotação', 'type' => 'checkbox'),
					'est008FormCadastroCotacaoCompra_btn' => array('name' => 'est008FormCadastroCotacaoCompra_btn', 'type'=>'submit','reload'=>false,'value'=>'Salvar','align' => 'right', 'style'=>'float:right;', 'reject'=>true),
					'GerarCotacaoEstoque' => array('name' => 'GerarCotacaoEstoque','label'=>'GerarCotacaoEstoque', 'type' => 'hidden', 'value' => @$r_GerarCotacaoEstoque,'reject' => true),
  					'GerarCotacaoEmergencia' => array('name' => 'GerarCotacaoEmergencia','label'=>'GerarCotacaoEmergencia', 'type' => 'hidden', 'value' => @$r_GerarCotacaoEmergencia,'reject' => true),
  					'GerarCotacaoEstoqueRequsicao' => array('name' => 'GerarCotacaoEstoqueRequsicao','label'=>'GerarCotacaoEstoqueRequsicao', 'type' => 'hidden', 'value' => @$r_GerarCotacaoEstoqueRequsicao,'reject' => true),
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					// echo var_dump($form->campoValorEnviado('T010_Flag_Cotacao_Finalizada'));
					// die();
					if($form->campoValorEnviado('T010_Flag_Cotacao_Finalizada') == null){
						$form->campoSetarValor('T010_Flag_Cotacao_Finalizada', "N", true);
					}else{
						$form->campoSetarValor('T010_Flag_Cotacao_Finalizada', "S", true);
					}
					// die();
					// $tmp = ($form->campoValorEnviado('T010_Flag_Cotacao_Finalizada')) ? 'S' : 'N';
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						if($form->campoValorEnviado('GerarCotacaoEstoque') == true){
							$retorno = $EST008->insereItemEstoque($acaoId);
						}elseif($form->campoValorEnviado('GerarCotacaoEmergencia') == true){
							$retorno = $EST008->insereItemEmergencia($acaoId);
						}elseif($form->campoValorEnviado('GerarCotacaoEstoqueRequsicao') == true){
							$retorno = $EST008->insereItemEstoqueRequisicao($acaoId);
						}
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação - Número: ' + '{$acaoId}', '/est/est008/content/est008contentCadastroCotacaoCompra/',        '&acaoId='           + encodeURIComponent('{$acaoId}'), [1000,650]);
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
						echo $processado;
					}
				}
				if($form->campoValor('T010_Flag_Cotacao_Finalizada') == 'S'){
					$form->checkedIt('T010_Flag_Cotacao_Finalizada');
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
		// $botoesDir .= '<button type="submit" id="est008FormCadastroCotacaoCompra_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est008FormProdutos':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		$sqlT059 = "SELECT * FROM T059 
								  left join D009 on D009_Id=T059_D009_Id 
								  left join D049 on D049_Id=D009_D049_Id 
								  left join D082 on D082_Id=D049_D082_Id 
								  left join D001 on D001_Id=D009_D001_Id WHERE T059_Id='$acaoId'";
		$resultT059 = mysql_query($sqlT059);
		$linhaT059 = mysql_fetch_array($resultT059);
		
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
		$form_tabela = 'T059';
		$form_campo_pk = 'T059_Id';
		$form_parent = 'T010_Id';
		$form_parent_valor = $r_T010_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est008FormProdutosBtnAdd', 'est008FormProdutosBtnCln', 'est008FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormProdutos/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Produto":{"top":7,"left":21,"width":408,"hidden":0},"D082_Marca":{"top":30,"left":28,"width":425,"hidden":0},"T059_Descricao_Produto":{"top":53,"left":9,"width":425,"hidden":0},"T059_Quantidade":{"top":76,"left":3,"width":425,"hidden":0},"est008FormProdutosBtnCln":{"top":97,"left":352,"width":null,"hidden":0},"est008FormProdutosBtnAdd":{"top":97,"left":432,"width":null,"hidden":0}}' , );
		
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
			'campoProcuraProduto',																									// callback js
			$g['divId']																												// divId
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid','value'=>@$linhaT059['D001_Codigo_Produto'], 'pesquisaGrid' => $pesquisaGrid, 'length' => array(80,30), 'reject' => true),
					'D082_Marca' => array('name' => 'D082_Marca', 'label' => 'Marca', 'type' => 'text', 'value'=>@$linhaT059['D082_Marca'], 'length' => array(80,30), 'reject' => true, 'readonly' => true),
					// Campo Pesquisa
					'T059_Descricao_Produto' => array('name' => 'T059_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(80,50)),
					'T059_Quantidade' => array('name' => 'T059_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(80,50)),
					//'T059_Valor_Custo_Unitario' => array('name' => 'T059_Valor_Custo_Unitario', 'label' => 'Valor Unitário', 'type' => 'moeda','value' => '0,00', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T059_T010_Id' => array('name' => 'T059_T010_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T010_Id),
					'T059_Codigo_Produto' => array('name' => 'T059_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'T059_D009_Id' => array('name' => 'T059_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T059_C004_Id' => array('name' => 'T059_C004_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value'=>$g['empresaAtual']),
				)
			)
		);
        $form = new Formulario('est008FormProdutos');
        // Botões dentro do Formulário
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est008FormProdutosBtnNew'] = array('name' => 'est008FormProdutosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est008FormProdutosBtnAdd'] = array('name' => 'est008FormProdutosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est008FormProdutosBtnCln'] = array('name' => 'est008FormProdutosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						/**
						* Função para totalizar os itens
						*/
						$EST008->insereItem($form, $r_acaoId);
						
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
						 $EST008->insereItem($form, $g['mysqlLastId']);
						
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
	
	case 'est008FormFornecedores':
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
		
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T173';
		$form_campo_pk = 'T173_Id';
		$form_parent = 'T010_Id';
		$form_parent_valor = $r_T010_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est008FormFornecedoresBtnAdd', 'est008FormFornecedoresBtnCln', 'est008FormFornecedoresBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormFornecedores/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Fornecedor":{"top":7,"left":34,"width":344,"hidden":0},"T173_Desconto":{"top":30,"left":636.0000610351562,"width":75,"hidden":1},"est008PreCadastrarCliente":{"top":51,"left":96,"width":null,"hidden":0},"est008GerarOC":{"top":51,"left":256,"width":null,"hidden":0},"est008FormFornecedoresBtnAdd":{"top":51,"left":336,"width":null,"hidden":0},"est008FormFornecedoresBtnCln":{"top":51,"left":400,"width":null,"hidden":0},"T173_D036_Id":{"top":76,"left":373,"width":144,"hidden":1},"T173_ICMS":{"top":76,"left":690.0000610351562,"width":75,"hidden":1},"T173_Frete":{"top":99,"left":321,"width":75,"hidden":1},"T173_Forma_Pagamento":{"top":99,"left":480,"width":345,"hidden":1}}' , );
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
			'Código,Fornecedor,Nome_Fantasia,Cidade,UF,CNPJ,DDD,Telefone',														// descrição dos campos
			'est008ProcuraFornecedor',																					// callback js
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
		
		//array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',"left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo='S' and D024_Flag_Cliente='S' order by D024_Nome_Empresa",'(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(80,30)),
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Fornecedor' => array('name' => 'Procura_Fornecedor' , 'label' => 'Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid2, 'length' => array(64,120), 'reject' => true),
					'T173_Forma_Pagamento' => array('name' => 'T173_Forma_Pagamento','label'=>'Forma pagamento', 'type' => 'text', 'length' => array(64,120)),
					//'T173_D024_Id' => array('name' => 'T173_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa','pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',"left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo='S' and D024_Flag_Cliente='S' order by D024_Nome_Empresa",'(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(80,30)),
					'T173_Frete' => array('name' => 'T173_Frete','label'=>'Frete', 'type' => 'moeda','casasDecimais' => 2,'length' => array(10,15)),
					'T173_D036_Id' => array('name' => 'T173_D036_Id','label'=>'Transporte', 'type' => 'select', 'select' => gGeraSelect('D036', 'D036_Id', 'D036_Tipo_Transporte', '')),
					'T173_Desconto' => array('name' => 'T173_Desconto','label'=>'Desconto', 'type' => 'moeda','casasDecimais' => 2,'length' => array(10,15)),
					'T173_ICMS' => array('name' => 'T173_ICMS','label'=>'ICMS', 'type' => 'moeda','casasDecimais' => 2,'length' => array(10,15)),
					'T173_T010_Id' => array('name' => 'T173_T010_Id', 'label' => 'T173_T010_Id', 'type' => 'hidden', 'value' => @$r_T010_Id),
					'T173_D024_Id' => array('name' => 'T173_D024_Id', 'label' => 'T173_D024_Id', 'type' => 'hidden'),
					'T173_D013_Id' => array('name' => 'T173_D013_Id', 'label' => 'Contato', 'type' => 'select', 
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_Tipo_Contato',"where D013_D024_Id = 0",true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_Tipo_Contato',"where D013_D024_Id=0",true)
					),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
        $form = new Formulario('est008FormFornecedores');
        // Botões dentro do Formulário
		$formArray['campos']['geral']['est008GerarOC'] = array('name' => 'est008GerarOC', 'type' => 'button', 'reload' => false, 'value' => 'Gerar OC', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est008FormFornecedoresBtnNew'] = array('name' => 'est008FormFornecedoresBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est008FormFornecedoresBtnAdd'] = array('name' => 'est008FormFornecedoresBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est008FormFornecedoresBtnCln'] = array('name' => 'est008FormFornecedoresBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est008PreCadastrarCliente'] = array('name' => 'est008PreCadastrarCliente', 'type' => 'button', 'reload' => false, 'value' => 'Pré-Cadastrar Fornecedor', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est008GerarOC').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar OC', '/est/est008/content/est008ContentGerarOC/','&T010_Id=' + encodeURIComponent({$r_T010_Id}), [600,400]);
			});
			$('#est008PreCadastrarCliente').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Cliente', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T139', [800,550]);
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
						// Atualiza o GRID e da um reload no FORM
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								//divRefresh('{$g['divIdParent']}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					$verifica = $EST008->verificaFornecedor($form);
					//$alert = gGeraAlertMsg('Erro!', $verifica);
					if($verifica === true){
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {						
							// Atualiza o GRID e da um reload no FORM
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$form->campoValorEnviado('divReload')}', true);
									//divRefresh('{$g['divIdParent']}', true);
									divRefresh('{$g['divId']}', true);
								} </script></div>
EOT;
						}
					} else {
						echo gGeraAlertMsg('Erro!', $verifica);
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
	
	case 'est008formAlterarMarca':
		//require_once("prog/est/est004/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T059_Id 	  = (isset($r_T059_Id)) ? $r_T059_Id : false;
		$T010_Id 	  = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id 	  = (isset($r_T173_Id)) ? $r_T173_Id : false;
		$T174_D009_Id = (isset($r_T174_D009_Id)) ? $r_T174_D009_Id : false;
		$acao_Id 	  = (isset($r_acao_Id)) ? $r_acao_Id : false;
		//$T086_Quantidade 	= (isset($r_T086_Quantidade)) ? $r_T086_Quantidade : false;
		
		
		$res = mysql_query("SELECT DISTINCT D001_Descricao_Produto,D001_Id, D049_D082_Id, D009_Id, D049_Id from T174
							LEFT JOIN T059 on T059_Id=T174_T059_Id
							LEFT JOIN D009 on D009_Id=T059_D009_Id
							LEFT JOIN D049 on D049_Id=D009_D049_Id
							LEFT JOIN D001 on D001_Id=D049_D001_Id
							WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='$T059_Id'");
		
		$lin = mysql_fetch_array($res);
		
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
		$enviar_btnId = 'est008formAlterarMarca_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008formAlterarMarca/';
		
		// novo formulário
		$form = new Formulario('form_est008formAlterarMarca');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campos Hidden
					'Marca' => array('name' => 'Marca', 'label'=>'Marca', 'type' => 'select',
						'select' => gGeraSelect('D009','D009_Id','D082_Marca','left join D049 on D049_Id=D009_D049_Id left join D082 on D082_Id=D049_D082_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id where D049_D001_Id='.$lin['D001_Id'].' AND D009_C004_Id='.$g['empresaAtual'], false, $T174_D009_Id)
					),
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
					$retorno = $EST008->est008_alterar_marca($form, $T059_Id, $T010_Id, $T173_Id);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							divRefresh('{$g['divIdParent']}', true	);
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
		$gerado = gGeraCamposForm($form, $tab, $acao_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est008formAlterarMarca_Btn">Enviar</button>';
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

	case 'est008FormIncluirProdForn':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
		$div = (isset($r_div)) ? $r_div : false;
		//$r_divReload = (isset($r_ven001FormProdutosForm)) ? $r_ven001FormProdutosForm['divReload'] : $r_divReload;

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
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormIncluirProdForn/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtraProdutoForn = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			AND
			D001_Flag_Ativo='S')
			order by D001_Codigo_Produto
EOT;
		$pesquisaGridProcuraProdutoForn = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtraProdutoForn,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Descrição Inglês,Marca,Un,Estoque',																// descrição dos campos
			'est008campoProcuraProdutoCotacaoForn',																			// callback js
			$g['divId'],																						// divId
			array($T010_Id, $T173_Id, $div)																		// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('est008FormIncluirProdFornForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_ProdutoFornCotacao' => array('name' => 'Procura_ProdutoFornCotacao' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGridProcuraProdutoForn, 'length' => array(30,30), 'reject' => true),
					// Campos Hidden
					//'T004_T003_Id' => array('name' => 'T004_T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id),
					//'T004_C004_Id' => array('name' => 'T004_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					//'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
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
		$gerado = gGeraCamposForm($form, $tab, $T010_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		
		$botoesMid = '';
		$botoesDir = '';
		$botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est008FormPreCadProd':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T059_Id = (isset($r_T059_Id)) ? $r_T059_Id : false;
		$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
		$abrirEditar = (isset($r_abrirEditar)) ? $r_abrirEditar : false;
		$T189_Descricao_Produto = (isset($r_T189_Descricao_Produto)) ? $r_T189_Descricao_Produto : '';
		
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
            'geral' => '{"D001_Codigo_Produto":{"top":7,"left":22.8125,"width":197,"hidden":0},"D001_Flag_Pre_Cadastro":{"top":7,"left":372.640625,"width":60,"hidden":0},"D001_Descricao_Produto":{"top":30,"left":-2.984375,"width":392,"hidden":0},"D041_Valor_Base":{"top":53,"left":78.1875,"width":101,"hidden":0},"D001_D037_Id":{"top":76,"left":53.375,"width":182,"hidden":0},"D001_D005_Id":{"top":99,"left":74.53125,"width":182,"hidden":0},"D049_D082_Id":{"top":122,"left":68.1875,"width":182,"hidden":0},"D001_Flag_Tipo":{"top":145,"left":54.453125,"width":182,"hidden":0},"D001_Flag_Ativo":{"top":145,"left":386.59375,"width":60,"hidden":0}}',
        );
		
		$sql = "SELECT D001_Codigo_Produto FROM D001 ORDER BY FLOOR(D001_Codigo_Produto) DESC LIMIT 1";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		$codigoProduto = $row['D001_Codigo_Produto'] + 1;
		
		$T059 = mysql_query("SELECT * FROM T059 WHERE T059_Id='{$T059_Id}'");
		$mT059 = mysql_fetch_array($T059);
		$desc = $mT059['T059_Descricao_Produto'];
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormPreCadastro_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormPreCadProd/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est008FormPreCadastro');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto','value' => $codigoProduto, 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
		        	'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro','disabled' => true, 'type' => 'select', 'extra' => 'style="width:60px;"', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'value'=>@$desc, 'label'=>'Descrição Português', 'type' => 'text', 'length' => array(64, 120)),
					'D041_Valor_Base' => array('name' => 'D041_Valor_Base', 'label'=>'Preço', 'type' => 'moeda', 'length' => array(14, 14),'reject' => true),
					/**
					'D001_Descricao_Ingles' => array('name' => 'D001_Descricao_Ingles', 'label'=>'Descrição Inglês', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(64, 60)),
		        	
		        	'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'extra'=>'style="width:182px;"', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('C008', 'C008_Id', 'C008_Tipo', '', true)
					),
					
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo, D060_Codigo_Padrao', ' order by D060_Descricao_Grupo ', true)
					),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'type' => 'select', 'extra'=>'style="width:182px;"','select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),
										
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg','casasDecimais' => 3, 'label'=>'Peso Unitário Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'type' => 'text', 'label'=>'Qtde por embalagem ', 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg', 'casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					*/
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select',  'extra'=>'style="width:182px;"','select' =>
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal', 'order by D005_Classificacao_Fiscal', true)
					),
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select',  'extra'=>'style="width:182px;"','reject'=>'true','select' =>
						gGeraSelect('D082', 'D082_Id', 'D082_Marca', '', true)
					),
					//'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(5, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'extra' => 'style="width:182px;"', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'PRODUZIDO/IMPORTADO', 'value'=>'PRODUZIDO'),
							array('title' => 'ADQUIRIDO MERCADO NACIONAL', 'value' =>'COMPRADO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'extra' => 'style="width:60px;"', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
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
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						//Pega o últmo Id inserido
						$D001_Id = $g['mysqlLastId'];
						//Insert D049
						$D082_Id = $form->campoValorEnviado('D049_D082_Id');
						$D041_Valor_Base = $form->campoValorEnviado('D041_Valor_Base');
						$T059_Codigo_Produto = $form->campoValorEnviado('D001_Codigo_Produto');
						$precoTabelado = $g['C031']['precoTabelado'];
						mysql_query("insert into D049 (D049_D001_Id,D049_D082_Id) values ({$D001_Id},{$D082_Id})");
						$D049_Id = $g['mysqlLastId'];
						$return = $CAD002->preCadastroMarca($D049_Id,$D001_Id,$D041_Valor_Base,$precoTabelado);
						
						if(!empty($T059_Codigo_Produto)){
                            // Atualizando a T059
                            mysql_query("UPDATE T059 SET T059_D009_Id='{$D009_Id_Cotacao}', T059_Codigo_Produto='{$T059_Codigo_Produto}' WHERE T059_Id='{$T059_Id}'");
                            
                            // Atualizando a T174
                            mysql_query("UPDATE T174 SET T174_D009_Id='{$D009_Id_Cotacao}' WHERE T174_T010_Id='{$T010_Id}' AND T174_T059_Id='{$T059_Id}'");
                        }
                        
						$alert=gGeraAlertMsg('Erro', "{$return}");
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						
						if($abrirEditar == 'false'){
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//divRefresh('{$g['divIdParent']}', true);
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								} </script></div>
EOT;
				
						}else{
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//divRefresh('{$g['divIdParent']}', true);
									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Produto - ID: ' + '{$D001_Id}', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$D001_Id}'), [980,550]);
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
				
				/**
				 * Modifica campos em caso de update 
				 */
				
				$cClausula = '';
				if ($form->campoValor('D001_C008_Id') > 9999)
				{
                    $cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
                if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S')
                {
		   		    $cClausula .= " order by D003_Codigo_Padrao ";
                }
                else
                {
		   		    $cClausula .= " order by D003_Nome_Linha ";
                }
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula));
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
		$g['smarty']->display('formEditor.tpl');
		//echo  $g['smarty']->fetch('form.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormPreCadastro_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'est008FormSelecionarEmpresa':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		$C007 = mysql_query("SELECT C007_Nome from C007 WHERE C007_Id='{$g['usuarioAtual']}'");
		$mC007 = mysql_fetch_array($C007);
		
		$T010_Id=(isset($r_T010_Id)) ? $r_T010_Id : false;
		$T173_Id=(isset($r_T173_Id)) ? $r_T173_Id : false;
		
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
		$form_tabela = 'D129';
		$form_campo_pk = 'D129_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est008FormSelecionarEmpresa_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormSelecionarEmpresa/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'C004_Id' => array('name' => 'C004_Id', 'label' => 'Empresa', 'type' => 'select', 
						'select' =>  gGeraSelect('C004','C004_Id','C004_Nome_Empresa','ORDER BY C004_Id',true, $g['empresaAtual']),
					),
				)
			)
		);
        $form = new Formulario('form-est008FormSelecionarEmpresa');
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
					$C004_Id = $form->campoValorEnviado('C004_Id');
					// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
					//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var \$buttons = {
							"Sim": function() {
									$('#{$g['divId']}').showLoading();
										$.getJSON('/est/est008/grid_func-ajax/est008geraOCEmpresa/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$T010_Id}) + '&T173_Id=' + encodeURIComponent({$T173_Id}) + '&C004_Id=' + encodeURIComponent({$C004_Id}), function(request) {
											if (request.code === true) {
												divRefresh('{$g['divId']}', true);
												dialogConfirm('Operação OK!', request.data);
												var T011_Id=request.respostaT011
												// Abre a ordem de compra recém criado
												abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra: ' + T011_Id, '/est/est006/content/est006ContentAbreEditarOrdemCompra/', '&acaoId=' + encodeURIComponent(T011_Id), [1000,600]);
												divRefresh('{$g['divIdParent']}', true);
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
												//$("#{$g['divIdParent']}").closest('.ui-dialog-content').dialog('close');
											} else if (request.code == 2) {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Falha ao gerar Ordem de Compra (cabeçalho)", \$buttons);
											} else if (request.code === 3) {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Falha ao gerar Ordem de Compra (produtos)", \$buttons);
											} else if (request.code == 4) {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Você não tem permissão para gerar OC", \$buttons);
											} else if (request.code == 5){
												var \$buttons = {
													"Sim": function() {
														$('#{$g['divId']}').showLoading();
														$.getJSON('/est/est008/grid_func-ajax/est008liberacaoAlcada/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$T010_Id}) + '&T173_Id=' + encodeURIComponent({$T173_Id}), function(request) {
															if (request.code === true) {
																divRefresh('{$g['divId']}', true);
																dialogConfirm('Operação OK!', request.data);
																divRefresh('{$g['divIdParent']}', true);
															} else {
																var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													    		dialogConfirm('Erro', request.data, \$buttons);
															}
															$('#{$g['divId']}').hideLoading();
														});
														$('#{$g['divId']}').hideLoading();
														$(this).dialog("destroy").remove();
													},
													"Não": function() { $(this).dialog("destroy").remove(); } 
												};
												dialogConfirm('Confirmação', 'A geração desta OC exige maior alçada, pedir liberação?(1)', \$buttons);
											} else if (request.code == 6){
												var \$buttons = {
													"Sim": function() {
														$('#{$g['divId']}').showLoading();
														$.getJSON('/est/est008/grid_func-ajax/est008liberacaoAlcada/?ajax=true&callback=?' + '&T010_Id=' + encodeURIComponent({$T010_Id}) + '&T173_Id=' + encodeURIComponent({$T173_Id}), function(request) {
															if (request.code === true) {
																divRefresh('{$g['divId']}', true);
																dialogConfirm('Operação OK!', request.data);
																divRefresh('{$g['divIdParent']}', true);
															} else {
																var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
													    		dialogConfirm('Erro', request.data, \$buttons);
															}
															$('#{$g['divId']}').hideLoading();
														});
														$('#{$g['divId']}').hideLoading();
														$(this).dialog("destroy").remove();
													},
													"Não": function() { $(this).dialog("destroy").remove(); } 
												};
												dialogConfirm('Confirmação', 'A geração desta OC exige maior alçada, pedir liberação?(2)', \$buttons);
											} else if (request.code == 7) {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Esta OC já foi gerada!", \$buttons);
											} else if(request.code == 8){
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Existem produtos não cadastrados!", \$buttons);
											} else if(request.code == 9){
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Existem produtos não cadastrados na empresa selecionada", \$buttons);
											} else if(request.code == 10){
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									    		dialogConfirm('Erro', "Existem produtos sem quantidade. Verifique!", \$buttons);
											}
											$('#{$g['divId']}').hideLoading();
										});
										$('#{$g['divId']}').hideLoading();
										$(this).dialog("destroy").remove();
								},
								"Não": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar ordem de compra', 'ATNEÇÃO!<br /><br />Após a ordem de compra gerada não será mais possível alterar a cotação desse fornecedor. <br /> Você tem certeza? ', \$buttons);
						} </script></div>
EOT;
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
		$botoesDir .= '<button type="submit" id="est008FormSelecionarEmpresa_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est008FormEditarProdutoFornecedor':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		$sqlT059 = "SELECT * FROM T059 
								  left join D009 on D009_Id=T059_D009_Id 
								  left join D049 on D049_Id=D009_D049_Id 
								  left join D082 on D082_Id=D049_D082_Id 
								  left join D001 on D001_Id=D009_D001_Id WHERE T059_Id='$acaoId'";
		$resultT059 = mysql_query($sqlT059);
		$linhaT059 = mysql_fetch_array($resultT059);
		
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
		
		// Pegando a descrição do produto
		$T174 = mysql_query("SELECT T174_Descricao_Produto FROM T174 WHERE T174_T059_Id='{$acaoId}' AND T174_T010_Id='{$r_T010_Id}' AND T174_T173_Id='{$r_T173_Id}'");
		$mT174 = mysql_fetch_array($T174);
		
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T059';
		$form_campo_pk = 'T059_Id';
		$form_parent = 'T010_Id';
		$form_parent_valor = $r_T010_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est008FormProdutosBtnAddFornecedor', 'est008FormProdutosBtnClnFornecedor', 'est008FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est008/form/est008FormEditarProdutoFornecedor/';
		
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
			'campoProcuraProdutoFornecedor',																									// callback js
			$g['divId']																												// divId
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Fornecedor' => array('name' => 'Procura_Produto_Fornecedor' , 'label' => 'Produto', 'type' => 'pesquisaGrid','value'=>@$linhaT059['D001_Codigo_Produto'], 'pesquisaGrid' => $pesquisaGrid, 'length' => array(80,30), 'reject' => true),
					'D082_Marca' => array('name' => 'D082_Marca', 'label' => 'Marca', 'type' => 'text', 'value'=>@$linhaT059['D082_Marca'], 'length' => array(80,30), 'reject' => true, 'readonly' => true),
					// Campo Pesquisa
					'T059_Descricao_Produto' => array('name' => 'T059_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(80,50), 'value'=>@$mT174['T174_Descricao_Produto'], 'reject'=>true),
					'T059_Quantidade' => array('name' => 'T059_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(80,50)),
					//'T059_Valor_Custo_Unitario' => array('name' => 'T059_Valor_Custo_Unitario', 'label' => 'Valor Unitário', 'type' => 'moeda','value' => '0,00', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T059_T010_Id' => array('name' => 'T059_T010_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T010_Id),
					'T059_Codigo_Produto' => array('name' => 'T059_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'T059_D009_Id' => array('name' => 'T059_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
				)
			)
		);
        $form = new Formulario('est008FormProdutosFornecedor');
        // Botões dentro do Formulário
		if (!empty($r_acaoId)) {
			 //$formArray['campos']['geral']['est008FormProdutosBtnNew'] = array('name' => 'est008FormProdutosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est008FormProdutosBtnAddFornecedor'] = array('name' => 'est008FormProdutosBtnAddFornecedor', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est008FormProdutosBtnClnFornecedor'] = array('name' => 'est008FormProdutosBtnClnFornecedor', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
					$retorno = $EST008->alteradoProduto($form, $r_acaoId, $r_T173_Id);
					if($retorno === true){
						/**
						* Função para totalizar os itens
						*/
						$EST008->insereItem($form, $r_acaoId);
						
						// Atualiza o GRID e da um reload no FORM
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
					} else {
						$alert = gGeraAlertMsg('Erro', 'Erro ao editar produto!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
								{$alert}
								}, 150);
  							});
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							//divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					}
					
				} else {
					// Insert
					/**
					* Função para totalizar os itens
					*/ 
					 $EST008->insereItem($form, $g['mysqlLastId']);
					
					// Atualiza o GRID e da um reload no FORM
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
		$g['smarty']->display('form.tpl');
		
		$botoesDir = $botoesEsq = '';
		$botoesEsq .= '<button type="submit" id="est008formPreCadastrarProdutoFornecedor">Pré cadastrar</button>';
		//$botoesEsq .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<script type="text/javascript">
		//Botão atualizar preço
		$('#est008formPreCadastrarProdutoFornecedor').unbind('click').bind('click', function() {
			abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$linhaT059['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$r_T010_Id}') + '&abrirEditar=false', [550,400]);
		});
		</script>
EOT;
	break;
}
?>
