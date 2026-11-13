<?php
namespace hardness;
set_time_limit(0);
require_once('prog/ven/ven015/form_func-js.php');
require_once('prog/ven/ven003/grid_func-callback.php');
require_once("bibliotecas/classes/VEN001.php");
require_once("bibliotecas/classes/VEN003.php");
require_once("bibliotecas/classes/VEN015.php");
$VEN001 = new VEN001();
$VEN003 = new VEN003();
$VEN015 = new VEN015();

/**
 * Ações
 */
switch ($g['r'][3]) {

	case 'ven015FormProdutosPDV':
		$g['smarty']->assign('navSql', "T007_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T007_Id, T007_Data_Emissao as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T007_D024_Id = D024_Id]');
			} </script></div>
EOT;
		//gHabilitaNavegacao($g['divId'], 'VEN003', 'ven015FormProdutosPDV');		

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T007=mysql_query("SELECT T007_D024_Id, T007_Casas_Decimais_Preco, T007_Flag_PDV FROM T007 WHERE T007_Id='{$r_T007_Id}'");
		$mT007=mysql_fetch_array($T007);

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
		$form_tabela = 'T008';
		$form_campo_pk = 'T008_Id';
		$form_parent = 'T007_Id';
		$form_parent_valor = $r_T007_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven015FormProdutosPDVBtnAdd', 'ven015FormProdutosPDVBtnCln', 'ven015FormProdutosPDVBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven015/form/ven015FormProdutosPDV/';
        
        //form editor
        $form_editor = array(
            'produto' => '{"Procura_Produto":{"top":7,"left":35.359375,"width":346,"hidden":0},"T008_Descricao_Produto":{"top":30,"left":17.640625,"width":360,"hidden":0},"T008_Quantidade":{"top":53,"left":9.65625,"width":296,"hidden":0},"T008_D037_Id":{"top":53,"left":384,"width":63,"hidden":0},"T008_Valor_Preco_Sem_Desconto_Unitario":{"top":76,"left":12.140625,"width":360,"hidden":0},"ven015FormProdutosPDVBtnAdd":{"top":74,"left":448,"width":null,"hidden":0}}',
        );
		// Campo: Procura_Produto
		$T007_Id = isset($r_ven015FormProdutosPDVForm) ? $r_ven015FormProdutosPDVForm['T008_T007_Id'] : $r_T007_Id;

		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id  
			left join D009 on D009_D049_Id=D049_Id  
			left join D037 on D037_Id=D001_D037_Id   
			LEFT JOIN D083 on D083_D001_Id=D001_Id
			LEFT JOIN T007 on T007_Id='$r_T007_Id'
			where D009_C004_Id = '{$g['empresaAtual']}'
			  and D001_Flag_Ativo='S'
			  and D001_Flag_Pre_Cadastro!='S'
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'T007_Id,D009_Id',																										      // chave primaria
			'D009_Id||foto||D001_Codigo_Produto||D001_Codigo_Barras||D083_Codigo_Produto_Fornecedor||D083_Codigo_Barras||D001_Descricao_Produto||D037_Unidade||D009_Preco_Loja||D009_Preco_1||"' . $T007_Id . '" as T007_Id||D001_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Foto,Código,EAN Int,Cód.Forn,EAN Forn,Descrição,UN,Preço unitário,Preço,T007_Id,D001_Id',	  // descrição dos campos
			'campoProcuraProdutoPDV',																					              // callback js
			$g['divId'],																									              // divId
			array($T007_Id, $mT007['T007_D024_Id'], $mT007['T007_Flag_PDV']),															  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%','callback' => 'mostrarImagemProdutoPequena', 'colunaTipo' => 'livre'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'left'  , 'style' => 'width:1%;font-weight:normal'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:normal'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false),
				array('chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			),
			'D009_Id||foto||D009_Preco_Loja||D037_Unidade||D009_Preco_1||T007_Id||D001_Id',	 // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			true,                                                                            // Novo parâmetro, caso TRUE realizará pesquisa antes de abrir janela, caso exista apenas 1 resultado para pesquisa já retorna o valor
		);
		
		$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
		$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
		$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

		// novo formulário
		$form = new Formulario('ven015FormProdutosPDVForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true, 'extra' => 'placeholder="BUSCAR"'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'hidden', 'length' => array(20,255), 'reject' => true ),
					'T008_Codigo_Substituto' => array('name' => 'T008_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'hidden', 'length' => array(20, 120)),
					'T008_Descricao_Produto' => array('name' => 'T008_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'readonly' => $readOnlyDescricao, 'length' => array(64, 120)),
					'T008_Quantidade' => array('name' => 'T008_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30), 'value' => '1,00', 'extra' => 'onblur="ProcuraProdutoPesquisaFocus()"'),
					'T008_D037_Id' => array('name' => 'T008_D037_Id', 'label' => '', 'readonly' => $readOnlyUnidade, 'type' => 'select', 'extra' => $extraUnidade, 'select' => 
					    gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T008_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $mT007['T007_Casas_Decimais_Preco'], 'readonly' => true),
					'PrecoMinimo' => array('name' => 'PrecoMinimo', 'type' => 'hidden', 'length' => array(20, 30), 'reject' => true, 'readonly' => true, 'style' => 'text-align: right;', 'label' => 'Prc. Mínimo'),
					'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'OC/Item', 'type' => 'hidden', 'length' => array(20, 60)),
					'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'type' => 'hidden', 'length' => array(20, 30)),
					'T008_Percentual_Desconto' => array('name' => 'T008_Percentual_Desconto', 'label' => '%D', 'type' => 'hidden', 'length' => array(15, 30), 'reject'=>true),
					'T008_D005_Id' => array('name' => 'T008_D005_Id', 'label' => 'NCM', 'type' => 'hidden', 'readonly' => true, 'restricao' => false, 'extra'=>'style="width:95px;"', 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' => 
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T008_Aliquota_IPI' => array('name' => 'T008_Aliquota_IPI', 'label' => 'IPI', 'type' => 'hidden', 'length' => array(5,20)),
					//'T008_Valor_Custo_Unitario' => array('name' => 'T008_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					//'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					//'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Observacao' => array('name' => 'T008_Observacao', 'label' => 'Observação', 'type' => 'hidden', 'length' => array(64, 120)),
					'T008_Informacoes_Adicionais' => array('name' => 'T008_Observacao', 'label' => 'Observação', 'type' => 'hidden'),
					'T008_Observacao_Entrega' => array('name' => 'T008_Observacao_Entrega', 'label' => 'Obs. Entrega', 'type' => 'hidden'),
					//Este campo não existe no banco, é apenas para inserir um serviço na T147
					// Campos Hidden
					'T008_T007_Id' => array('name' => 'T008_T007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T007_Id),
					'T008_C004_Id' => array('name' => 'T008_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T008_D009_Id' => array('name' => 'T008_D009_Id', 'label' => 'T008_D009_Id', 'type' => 'hidden'),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'UltimoPrecoVenda' => array('name' => 'UltimoPrecoVenda', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Histórico', '.: Histórico :.', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [900,650]); return false;\">Histórico</a>", 'type' => 'hidden', 'reject'=>true, 'readonly'=>true),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
                    'T008_D006_Id' => array('name' => 'T008_D006_Id', 'label' => 'CFOP', 'type' => 'hidden', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP DESC','...',false)
					),
					'T008_Flag_Destino_Produto' => array('name' => 'T008_Flag_Destino_Produto','label'=>'Destino', 'type' => 'hidden', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
                            array('title' => '3 - CONSUMO', 'value' => "3"),
                            array('title' => '4 - ESPECIAL', 'value' => "4"),
                            array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
							array('title' => '7 - DEMONSTRAÇÃO', 'value' => "7"),
							array('title' => '8 - CONSIGNAÇÃO', 'value' => "8"),
							array('title' => '9  - VENDA FUTURA', 'value' => "9"),
							array('title' => '10 - VENDA CONTA E ORDEM', 'value' => "10"),
							array('title' => '11 - VENDA PRÓ-EMPREGO', 'value' => "11"),
							array('title' => '12 - REMESSA PARA CONSERTO', 'value' => "12"),
							array('title' => '13 - REMESSA PARA INDUSTRIALIZAÇÃO', 'value' => "13"),
							array('title' => '14 - OUTRAS SAÍDAS', 'value' => "14"),
							array('title' => '15 - AMOSTRA', 'value' => "15"),
							array('title' => '16 - DOAÇÃO', 'value' => "16"),
							array('title' => '17 - VENDA DE ATIVO IMOBILIZADO', 'value' => "17"),
							array('title' => '18 - TRANSFERÊNCIA ENTRE EMPRESAS', 'value' => "18"),
							array('title' => '19 - REMESSA TRIANGULAR', 'value' => "19"),
							array('title' => '20 - REMESSA VENDA FUTURA', 'value' => "20"),
						)
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [700,400]); return false;\">OC</a>", 'type' => 'hidden', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [700,400]); return false;\">Est. Fora</a>", 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'precoTabela' => array('name' => 'precoTabela', 'label' => 'Prc. Tabela', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					//'infoCustoVenda' => array('name' => 'infoCustoVenda', 'label' => '', 'type' => 'titulo', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'enviado' => array('name' => 'enviado', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'Codigo_Origem_Mercadoria' => array('name' => 'Codigo_Origem_Mercadoria', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven015FormProdutosPDVBtnNew'] = array('name' => 'ven015FormProdutosPDVBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'reject' => true);
		}
		$formArray['campos']['produto']['ven015FormProdutosPDVBtnAdd'] = array('name' => 'ven015FormProdutosPDVBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'reject' => true);
		//$formArray['campos']['produto']['ven015FormProdutosPDVBtnCln'] = array('name' => 'ven015FormProdutosPDVBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'reject' => true);
		//$formArray['campos']['produto']['ven001BtnCadastrarProduto'] = array('name' => 'ven001BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001BtnCadastrarProduto').unbind('click');
				$('#ven001BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T007_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T007_Id={$T007_Id}&divForm={$g['divId']}', [600,430]);
					return false;
				});
		    } </script></div>
EOT;
	
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			$D009_Id = $form->campoValorEnviado('T008_D009_Id');
			$T008_Quantidade = $form->campoValorEnviado('T008_Quantidade');
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$T008_Id = false;
				$retorno = $VEN003->incluirProduto($form, "", true);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					$T008_Id = $g['mysqlLastId'];
					$VEN003->atualizarAliquotasNF($form->campoValorEnviado('T008_T007_Id'));
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$T008_Id}', 'T008_Id'); // grid
							divRefresh('{$r_divOutro}', true, false, 2); // totais
							divRefresh('{$g['divId']}');
						} </script></div>
EOT;
					// Em caso de sucesso, limpa o form (a ação padrão é manter os dados) não precisar dar: divRefresh('{$g['divId']}', true);
					$form->limpar();
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

	case 'ven015FormCPF':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven015FormCPFBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven015/form/ven015FormCPF/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"T007_Cliente_CNPJ_CPF":{"top":7,"left":14.96875,"width":312,"hidden":0}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_Cliente_CNPJ_CPF' => array('name' => 'T007_Cliente_CNPJ_CPF', 'label' => 'CPF', 'type' => 'cpf', 'length' => array(11,14)),
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
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$botoesDir .= '<button type="submit" id="ven015FormCPFBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven015FormCNPJ':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven015FormCNPJBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven015/form/ven015FormCNPJ/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"T007_Cliente_CNPJ_CPF":{"top":4,"left":8.203125,"width":312,"hidden":0}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_Cliente_CNPJ_CPF' => array('name' => 'T007_Cliente_CNPJ_CPF', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(18,18)),
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
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$botoesDir .= '<button type="submit" id="ven015FormCNPJBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven015FormVendedor':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven015FormVendedorBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven015/form/ven015FormVendedor/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"T007_D024_Id":{"top":30,"left":25.78125,"width":570,"hidden":0},"T007_C007_Id_Vendedor_Interno":{"top":7,"left":11.71875,"width":591,"hidden":0}}',
		);

		// Exemplo de como criar um dialog que ira exibir apos selecionar o cliente
		// $dialog = "{ 'titulo':'Atenção!','msg':'Teste','buttons':{ 'Ok': function() { alert('Teste') },'Fechar': function() { $(this).dialog('destroy').remove();}} }";
		$dialog = "";
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_D024_Id' => array('name' => 'Consulta_T007_D024_Id','label'=>'Cliente', 'type' => 'pesquisa', 
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30),
						'extra' => "onchange=\"exibeMensagem(" . $dialog . ");\"",
					),			                                                                                
					'T007_C007_Id_Vendedor_Interno' => array('name' => 'T007_C007_Id_Vendedor_Interno', 'label' => 'Vendedor', 'type' => 'select', 
						'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','WHERE C007_Ativo = "S" AND (C007_Flag_Vendedor = "V" OR  C007_Flag_Vendedor = "S" OR  C007_Flag_Vendedor = "D") ORDER BY C007_Primeiro_Nome', true, $g['usuarioAtual']),						
					),					
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
						$D024_Id = $form->campoValorEnviado('T007_D024_Id');
						$mD024 = mysql_fetch_array(mysql_query("SELECT D024_Cpf, D024_Flag_Destino_Produto FROM D024 WHERE D024_Id = '{$D024_Id}'"));
						if($D024_Id == $g['C031']['codigoClienteCupomFiscal']){
							$mD024['D024_Cpf'] = '';
						}
						mysql_query("UPDATE T007 
										SET T007_Cliente_CNPJ_CPF = '{$mD024['D024_Cpf']}',
											T007_Flag_Destino_Produto = IF('{$mD024['D024_Flag_Destino_Produto']}' != '','{$mD024['D024_Flag_Destino_Produto']}', T007_Flag_Destino_Produto)
									WHERE T007_Id = '{$r_acaoId}'");
										
						require_once('bibliotecas/classes/VEN015.php');
						$VEN015 = new VEN015();
						$VEN015->verificaClubeDesconto($r_acaoId);
						
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$r_divReload}', true);
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
		$botoesDir .= '<button type="submit" id="ven015FormVendedorBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven015FormDAV':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven015FormDAVBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven015/form/ven015FormDAV/';
		
		//form editor
		$form_editor = array(
			'geral' => '{"numeroDAV":{"top":4,"left":4,"width":225}}',
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'numeroDAV' => array('name' => 'numeroDAV', 'label' => 'Número DAV', 'type' => 'text', 'length' => array(40,80), 'reject' => 'true'),
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
					$retorno = $VEN015->ImportarDAV($form->campoValorEnviado('numeroDAV'), $r_acaoId);
					if ($retorno === true) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('DAV', 'DAV Importada com sucesso.', false);
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
					} else {
						$alert = gGeraAlertMsg('ERRO', $retorno , false);
						echo <<<EOT
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
		$botoesDir .= '<button type="submit" id="ven015FormDAVBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Importar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

}
