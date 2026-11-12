<?php
namespace hardness;
/**
* Formulário
* /cad/cad002/form/
*/

require_once("bibliotecas/classes/CAD002.php");
require_once("bibliotecas/classes/xml/ecompleto/xmlProduto.php");
require_once("prog/cad/cad002/form_func-js.php");
require_once('prog/ven/ven001/form_func-js.php');

$CAD002 = new CAD002();
$XML = new xmlProduto();

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
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad002/form/form1/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"D009_Preco_Pauta_ST":{"top":421,"left":765.34375,"width":72,"hidden":1},"D001_CNPJ_Fabricante":{"top":444,"left":54.125,"width":134,"hidden":1},"D001_Codigo_Produto":{"top":7,"left":54.109375,"width":392,"hidden":0},"D049_D082_Id":{"top":7,"left":611.984375,"width":255,"hidden":0},"D001_D015_Id":{"top":7,"left":1066.1875,"width":182,"hidden":1},"D001_Codigo_Barras":{"top":30,"left":107.921875,"width":136,"hidden":0},"D001_Codigo_Barras_Tributavel":{"top":30,"left":320.515625,"width":136,"hidden":0},"D049_D024_Id":{"top":30,"left":593.625,"width":233,"hidden":0},"D001_Flag_Tipo_Item":{"top":30,"left":946.84375,"width":400,"hidden":1},"D009_Aliquota_ICMS_Tabela":{"top":30,"left":1050.90625,"width":75,"hidden":1},"D001_Descricao_Produto":{"top":53,"left":81.640625,"width":392,"hidden":0},"D049_Flag_Tipo":{"top":53,"left":621.75,"width":256,"hidden":0},"D049_Modelo":{"top":53,"left":1077.796875,"width":174,"hidden":1},"D001_Preco_4":{"top":76,"left":-44.03125,"width":86,"hidden":1},"D001_C008_Id":{"top":76,"left":101.1875,"width":399,"hidden":0},"D001_D037_Id":{"top":76,"left":596.0625,"width":255,"hidden":0},"D049_IPV":{"top":76,"left":1166.96875,"width":78,"hidden":1},"D049_Flag_Ativo":{"top":76,"left":1281.421875,"width":80,"hidden":1},"D009_IPT_1":{"top":99,"left":5,"width":86,"hidden":1},"D001_D060_Id":{"top":99,"left":104.25,"width":399,"hidden":0},"D009_Quantidade_Estoque":{"top":99,"left":609.515625,"width":72,"hidden":0},"Pedido":{"top":99,"left":786.125,"width":72,"hidden":0},"D049_IPV2":{"top":99,"left":1086.96875,"width":78,"hidden":1},"D009_IPT_2":{"top":122,"left":5,"width":86,"hidden":1},"D001_D003_Id":{"top":122,"left":107.3125,"width":399,"hidden":0},"D009_Quantidade_Estoque_Liquido":{"top":122,"left":614.40625,"width":72,"hidden":0},"Separado":{"top":122,"left":778.171875,"width":72,"hidden":0},"D049_Flag_Substituicao_Tributaria":{"top":122,"left":988.84375,"width":80,"hidden":1},"D049_Observacao_Compra":{"top":122,"left":1236.390625,"height":15,"width":88,"hidden":1},"D009_IPT_3":{"top":145,"left":5,"width":86,"hidden":1},"D001_D002_Id":{"top":145,"left":84.078125,"width":399,"hidden":0},"D009_Quantidade_Estoque_Loja":{"top":145,"left":629.078125,"width":72,"hidden":0},"D009_Quantidade_OC":{"top":145,"left":809.375,"width":72,"hidden":0},"D009_Data_Alteracao_Custo":{"top":145,"left":974.453125,"width":72,"hidden":1},"D049_Codigo_Produto_Fornecedor":{"top":145,"left":1150.0625,"width":174,"hidden":1},"D009_IPT_4":{"top":168,"left":5,"width":86,"hidden":1},"D001_D005_Id":{"top":168,"left":105.5,"width":399,"hidden":0},"D009_Quantidade_Estoque_Fora":{"top":168,"left":627.25,"width":72,"hidden":0},"D009_Quantidade_Estoque_Similar":{"top":168,"left":780.640625,"width":72,"hidden":0},"D009_Percentual_Desconto_Tabela":{"top":168,"left":1031.9375,"width":75,"hidden":1},"D049_Origem_Mercadoria":{"top":191,"left":40.0625,"width":400,"hidden":0},"D049_Percentual_Acrescimo_Tabela":{"top":191,"left":1016.671875,"width":72,"hidden":1},"D009_Valor_Custo_Tabela":{"top":213,"left":588.109375,"width":72,"hidden":0},"D009_Origem_Mercadoria":{"top":214,"left":15.40625,"width":1078,"hidden":1},"D009_Data_Atualizacao_Custo_Fornecedor":{"top":214,"left":751.890625,"width":72,"hidden":0},"D009_Data_Ultima_Entrada":{"top":236,"left":752.53125,"width":72,"hidden":0},"D001_Especificacoes":{"top":237,"left":64.515625,"height":83,"width":392,"hidden":0},"D009_Valor_Custo_Ultima_Compra":{"top":237,"left":549,"width":72,"hidden":0},"D049_Flag_Nacional_Importado":{"top":236.953125,"left":1018.5,"width":120,"hidden":1},"D009_Valor_Custo_Medio_Unitario":{"top":260,"left":588.109375,"width":72,"hidden":0},"D009_Valor_Preco_Tabela":{"top":283,"left":582.015625,"width":72,"hidden":0},"D009_Data_Atualizacao_Preco_Tabela":{"top":283,"left":755.5625,"width":72,"hidden":0},"D001_Aplicacao":{"top":283,"left":948.6875,"width":104,"hidden":1},"D009_ICF_3":{"top":282.953125,"left":1086.984375,"width":75,"hidden":1},"infoCustoVendaProduto":{"top":286,"left":932,"width":null,"hidden":1},"D009_ICF_1":{"top":306,"left":632.15625,"width":72,"hidden":0},"D009_Valor_Custo_Unitario":{"top":306,"left":767.8125,"width":72,"hidden":0},"D009_ICF_2":{"top":305.953125,"left":1086.984375,"width":75,"hidden":1},"D001_Peso_Total_Kg":{"top":305.953125,"left":1289.28125,"width":56,"hidden":1},"D001_Peso_Unitario_Kg":{"top":329,"left":16.8125,"width":88,"hidden":0},"D001_Qte_Embalagem":{"top":329,"left":367.484375,"width":56,"hidden":0},"D009_Preco_1":{"top":329,"left":611.96875,"width":72,"hidden":0},"D009_IPV_1":{"top":329,"left":798.96875,"width":72,"hidden":0},"D009_Valor_Preco_Importado":{"top":328.953125,"left":1033.171875,"width":72,"hidden":1},"D001_Peso_Unitario_Bruto":{"top":352,"left":25.984375,"width":88,"hidden":0},"D001_Flag_Ativo":{"top":352,"left":418.859375,"width":64,"hidden":0},"D009_Preco_2":{"top":352,"left":611.96875,"width":72,"hidden":0},"D009_IPV_2":{"top":352,"left":798.96875,"width":72,"hidden":0},"D009_Valor_Custo_Compra":{"top":351.921875,"left":1052.125,"width":72,"hidden":1},"D009_Flag_Usar_Custo":{"top":351.953125,"left":1245.140625,"width":256,"hidden":1},"D001_Cubagem_Unitaria":{"top":375,"left":90.1875,"width":88,"hidden":0},"D001_Flag_Pre_Cadastro":{"top":375,"left":403.578125,"width":64,"hidden":0},"D009_Preco_3":{"top":375,"left":611.96875,"width":72,"hidden":0},"D009_IPV_3":{"top":375,"left":798.96875,"width":72,"hidden":0},"D001_Descricao_Ingles":{"top":374.921875,"left":1048.5625,"width":392,"hidden":1},"D049_Data_Cadastro":{"top":398,"left":66.953125,"width":88,"hidden":0},"D001_Flag_Multimarcas":{"top":398,"left":420.078125,"width":64,"hidden":0},"D009_Preco_4":{"top":398,"left":611.96875,"width":72,"hidden":0},"D009_IPV_4":{"top":398,"left":798.96875,"width":72,"hidden":0},"D001_Descricao_Comercial":{"top":397.953125,"left":1156.40625,"width":168,"hidden":1},"D001_Quantidade_Tributavel":{"top":421,"left":14.96875,"width":88,"hidden":0},"D049_Valor_Preco_KG":{"top":421,"left":268.640625,"width":86,"hidden":1},"D001_C004_Id":{"top":421,"left":333.859375,"width":141,"hidden":1},"D009_Data_Ultima_Venda":{"top":421,"left":585.6875,"width":72,"hidden":0},"D049_Quantidade_Embalagem_Compra":{"top":421,"left":992.21875,"width":86,"hidden":1},"D001_Preco_3":{"top":444,"left":483.96875,"width":86,"hidden":1},"D009_Quantidade_OP":{"top":444,"left":665.96875,"width":98,"hidden":1},"D001_Preco_1":{"top":444,"left":835.96875,"width":86,"hidden":1},"D049_IPV1":{"top":443.96875,"left":1246.96875,"width":78,"hidden":1},"D041_Valor_Base":{"top":466.921875,"left":19.96875,"width":78,"hidden":1},"D001_Flag_Ecommerce":{"top":467,"left":351,"width":57,"hidden":1},"D001_Preco_2":{"top":467,"left":467.96875,"width":86,"hidden":1},"D009_Preco_Loja":{"top":467,"left":597.28125,"width":72,"hidden":1},"D009_Preco_Loja_Real":{"top":467,"left":747.59375,"width":72,"hidden":1},"cad002GridHistoricoPrecos":{"top":465,"left":1072,"width":null,"hidden":1},"D041_Valor_Preco_Consumidor":{"top":489.921875,"left":19.96875,"width":78,"hidden":1},"D041_Valor_Preco_Revenda":{"top":512.921875,"left":19.96875,"width":78,"hidden":1},"D001_Flag_Tipo":{"top":513,"left":1093.71875,"width":400,"hidden":1},"D041_Valor_Custo":{"top":535.921875,"left":18.859375,"width":78,"hidden":1},"D009_Flag_Promocao":{"top":581.984375,"left":823.75,"width":13,"hidden":1}}'
		);
		$form_editor['geral'] = substr($form_editor['geral'], 0, -1) . ',"D009A_Flag_Pertence_Empresa":{"top":421,"left":333.859375,"width":141,"hidden":0}}';
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N', 'selected'=>true)
						)
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Descricao_Ingles' => array('name' => 'D001_Descricao_Ingles', 'label'=>'Descrição Inglês', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Descricao_Comercial' => array('name' => 'D001_Descricao_Comercial', 'label'=>'Descrição Comercial', 'type' => 'text', 'length' => array(64, 40)),
		        	'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label' => 'Depto', 'type' => 'select', 'select'=>
						gGeraSelect('C008', 'C008_Id', 'C008_Tipo', 'order by C008_Id', true)
					, 'extra' => "onchange=\"cad002MudarC008('{$g['divId']}', this); return false;\""),
					'D009A_Flag_Pertence_Empresa' => array('name' => 'D009A_Flag_Pertence_Empresa', 'label' => 'Pertence à empresa', 'type' => 'select', 'reject' => true, 'select' => array(
						array('title' => '', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)),
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select', 'restricao' => false, 'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label' => 'Setor', 'type' => 'select', 'select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', " WHERE D060_C008_Id = '{$r_form_cad002_form1['D001_C008_Id']}' order by D060_Descricao_Grupo ", true)
					),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'restricao' => false,'type' => 'select', 'select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select', 'select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),					
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg','casasDecimais' => 3, 'label'=>'Peso Unitário Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'type' => 'text', 'label'=>'Qtde por embalagem ', 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg', 'casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Cubagem_Unitaria' => array('name' => 'D001_Cubagem_Unitaria', 'casasDecimais' => 6, 'label'=>'Cubagem', 'type' => 'moeda', 'value' => '0,000000', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label' => 'Unidade', 'type' => 'select',  'select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select', 'select' =>
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(4, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Origem', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Produzido/Importado', 'value' => 'PRODUZIDO'),
							array('title' => 'Adquirido mercado nacional', 'value' =>'COMPRADO', 'selected' => true)
						)
					),
					'D001_Flag_Tipo_Item' => array('name' => 'D001_Flag_Tipo_Item', 'label' => 'Categoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '00-Mercadoria para Revenda', 'value'=>'00'),
							array('title' => '01-Matéria-Prima', 'value' =>'01'),
							array('title' => '02-Embalagem', 'value' =>'02'),
							array('title' => '03-Produto em Processo', 'value' =>'03'),
							array('title' => '04-Produto Acabado', 'value' =>'04'),
							array('title' => '05-Subproduto', 'value' =>'05'),
							array('title' => '06-Produto Intermediário', 'value' =>'06'),
							array('title' => '07-Material de Uso e Consumo', 'value' =>'07'),
							array('title' => '08-Ativo Imobilizado', 'value' =>'08'),
							array('title' => '09-Serviços', 'value' =>'09'),
							array('title' => '10-Outros insumos', 'value' =>'10'),
							array('title' => '99-Outras', 'value' =>'99')
						)						
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
					'D001_CNPJ_Fabricante' => 	array('name' => 'D001_CNPJ_Fabricante', 'label' => 'CNPJ Fabricante', 'type' => 'cnpj', 'length' => array(18,18)),
                ),
					

				
            ) // 'campos'
		);
		if($g['C031']['multimarcas'] == 'ambas' || $g['C031']['multimarcas'] == 'N' ){
			$formArray['campos']['geral']['D049_D082_Id'] = array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select', 'restricao' => false, 'select'=>
						//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
						gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true), 'reject' => true,
					);
			if($g['C031']['multimarcas'] == 'ambas') {
				$formArray['campos']['geral']['D001_Flag_Multimarcas'] = array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
					array(
						array('title' => 'Não', 'value' =>'N', 'selected' => true),
						array('title' => 'Sim', 'value'=>'S')
					)
				);
			}
		}

		
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
									}, 150)
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					$retorno = $CAD002->verificarCodigoProduto($form);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Descricao_Produto').focus();
							} </script></div>
EOT;
					}else{
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						//Pega o últmo Id inserido
						$acaoId = $g['mysqlLastId'];
						$alert="";
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						$CAD002->incluirMarca($form);						
						// Prox Cod do Produto
						/*
						$cod = $CAD002->obterNovoCodigoProduto($acaoId);
						log("Novo Cod. Produto: {$cod}");
						if ($cod) {
							mysql_query("UPDATE D001 SET D001_Codigo_Produto = '{$cod}' WHERE D001_Id = '{$acaoId}'");
						}
						*/
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Produto - ID: ' + '{$acaoId}', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [980,550]);
										{$alert}
									}, 150);
  								});
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
		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

		echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	function alterar_D001_C008_Id(elem, divId) {	
		// Preencher código
		$('#' + divId).showLoading();
		$.getJSON('/cad/cad001/grid_func-ajax/verificarCodigo/?ajax=true&C008_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
			if (request.code) {
				$('#' + divId + ' .itemForm-D001_Codigo_Produto').val(request.data);
			}
			$('#' + divId).hideLoading();
		});
		// Alterar setores
		$.getJSON('/cad/cad001/form_func-ajax/alteraSetor/?ajax=true&D001_C008_Id=' + encodeURIComponent($(elem).val()) + '&callback=?', function(request) {
	        $('#' + divId).hideLoading();
			if (request.code) {
				$('#D001_D060_Id').html(request.data['D001_D060_Id']);
				reloadUI('#' + divId);
			} else {
				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    		dialogConfirm("Erro!", request.data, \$buttons)
			}
		});
	}
} </script></div>
EOT;
	break;
	
		case 'cad002FormImportarCSV':
	
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCsvCad002'] 		= array('name' => 'uploadArquivoCsvCad002', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['cad002FormImportarCSV_btn'] 		= array('name' => 'cad002FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D001_Arquivo_Csv'] 				= array('name' => 'D001_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT
						<div class=""></div>
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCsvCad002');
				$incluirItens= $form->campoValorEnviado('incluirItens');
				$retorno = $CAD002->importarCSV($arquivo,$incluirItens);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
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

	case 'form2':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		gHabilitaNavegacao($g['divId'], 'CAD002', 'form2');
		$g['smarty']->assign('navSql', "D001_Flag_Ativo='S'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[D001_Codigo_Produto, D001_Descricao_Produto]');
				$('#{$g['divId']}').data('labelPesquisa', '[Código,Descrição]');
				$('#{$g['divId']}').data('sqlExtra', '');
			} </script></div>
EOT;

		
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btnEditarProduto';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/form2/';
        //Editor
        $form_editor = array(
            'geral' => '{"D001_Codigo_Produto":{"top":7,"left":42,"width":88,"hidden":0},"D001_Codigo_Barras":{"top":7,"left":238,"width":104,"hidden":0},"D001_Aplicacao":{"top":7,"left":389,"width":104,"hidden":0},"D001_Descricao_Comercial":{"top":7,"left":999,"width":136,"hidden":1},"D001_Descricao_Produto":{"top":30,"left":66,"width":424,"hidden":0},"D001_Descricao_Ingles":{"top":30,"left":986,"width":345,"hidden":1},"D001_C008_Id":{"top":53,"left":87,"width":431,"hidden":0},"D001_Flag_Tipo_Item":{"top":53,"left":1045,"width":192,"hidden":1},"D001_D060_Id":{"top":76,"left":96,"width":431,"hidden":0},"D001_D003_Id":{"top":99,"left":92,"width":431,"hidden":0},"D001_Flag_Tipo":{"top":76,"left":1087,"width":192,"hidden":1},"D001_D005_Id":{"top":30,"left":756,"width":159,"hidden":0},"D001_D002_Id":{"top":122,"left":69,"width":431,"hidden":0},"D001_D015_Id":{"top":99,"left":1098,"width":191,"hidden":1},"D001_D037_Id":{"top":7,"left":726,"width":159,"hidden":0},"D001_Peso_Unitario_Kg":{"top":53,"left":779,"width":72,"hidden":0},"D001_Qte_Embalagem":{"top":122,"left":1076,"width":88,"hidden":1},"D001_Especificacoes":{"top":145,"left":48,"height":70,"width":424,"hidden":0},"D001_Flag_Ativo":{"top":76,"left":829,"width":64,"hidden":0},"D001_Peso_Unitario_Bruto":{"top":145,"left":1070,"width":128,"hidden":1},"D001_Flag_Pre_Cadastro":{"top":99,"left":811,"width":64,"hidden":0},"D001_Flag_Multimarcas":{"top":122,"left":828,"width":64,"hidden":0},"D001_Peso_Total_Kg":{"top":191,"left":1103,"width":120,"hidden":1}}',
        );
		$form_editor['geral'] = substr($form_editor['geral'], 0, -1) . ',"D009A_Flag_Pertence_Empresa":{"top":99,"left":1045,"width":192,"hidden":0}}';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form2');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20)),
					'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Descricao_Ingles' => array('name' => 'D001_Descricao_Ingles', 'label'=>'Descrição Inglês', 'type' => 'text', 'length' => array(64, 60)),
					'D001_Descricao_Comercial' => array('name' => 'D001_Descricao_Comercial', 'label'=>'Descrição Comercial', 'type' => 'text', 'length' => array(64, 40)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(30, 60)),					
		        	'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'type' => 'select', 'select'=>
	                	gGeraSelect('C008', 'C008_Id', 'C008_Tipo', 'order by C008_Id', true)
	                ),
					'D009A_Flag_Pertence_Empresa' => array('name' => 'D009A_Flag_Pertence_Empresa', 'label' => 'Pertence à empresa', 'type' => 'select', 'reject' => true, 'select' => array(
						array('title' => '', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)),
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select', 'restricao' => false, 'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'restricao' => false, 'select' => 
                		gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', ' order by D060_Descricao_Grupo ', true)
                	),					
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'type' => 'select','select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),					
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),					
						'D001_Peso_Unitario_Bruto' => array('name' => 'D001_Peso_Unitario_Bruto', 'casasDecimais' => 3,'label'=>'Peso Unitário Bruto Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg', 'casasDecimais' => 3,'label'=>'Peso Unitário Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'casasDecimais' => 3,'label'=>'Qtde por embalagem ', 'type' => 'moeda', 'value' => '0,000', 'length' => array(15, 30), 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg','casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select', 'select' =>
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
	                ),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(5, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Produzido/Importado', 'value'=>'PRODUZIDO', 'selected'=>true),
							array('title' => 'Adquirido mercado nacional', 'value' =>'COMPRADO')
						)
					),
					'D001_Aplicacao' => array('name' => 'D001_Aplicacao','label'=>'Referência', 'type' => 'text'),
					'D001_Flag_Tipo_Item' => array('name' => 'D001_Flag_Tipo_Item', 'label' => 'Categoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '00-Mercadoria para Revenda', 'value'=>'00', 'selected'=>true),
							array('title' => '01-Matéria-Prima', 'value' =>'01'),
							array('title' => '02-Embalagem', 'value' =>'02'),
							array('title' => '03-Produto em Processo', 'value' =>'03'),
							array('title' => '04-Produto Acabado', 'value' =>'04'),
							array('title' => '05-Subproduto', 'value' =>'05'),
							array('title' => '06-Produto Intermediário', 'value' =>'06'),
							array('title' => '07-Material de Uso e Consumo', 'value' =>'07'),
							array('title' => '08-Ativo Imobilizado', 'value' =>'08'),
							array('title' => '09-Serviços', 'value' =>'09'),
							array('title' => '10-Outros insumos', 'value' =>'10'),
							array('title' => '99-Outras', 'value' =>'99')
						)						
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Flag_Multimarcas' => array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
                ),
				
            ) // 'campos'
		);
		
		$form->definirFormularioArray($formArray);
				
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D001_Flag_Multimarcas').change(function() {
					if (($(this).val() == 2)) {
						$.getJSON('/cad/cad002/form_func-ajax/flagMultimarcas/?ajax=true&D001_Id=' + encodeURIComponent({$acaoId}) + '&Flag=N&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$r_divIdMain}', true);
							}
						});
					} else if (($(this).val() == 1)) {
						$.getJSON('/cad/cad002/form_func-ajax/flagMultimarcas/?ajax=true&D001_Id=' + encodeURIComponent({$acaoId}) + '&Flag=S&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$r_divIdMain}', true);
							}
						});
					}
				});
				$('#{$g['divId']} #Flag_Multimarcas').trigger('change');
			} </script></div>
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
					$retorno = $CAD002->verificarCodigoProduto($form,$acaoId);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							$CAD002->atualizarFlagProdutoPertenceEmpresa(
								$acaoId,
								$form->campoValorEnviado('D009A_Flag_Pertence_Empresa')
							);
							// Imprime uma mensagem e fecha a janela
							echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						}
					}
				}else {
					// Insert
					$retorno = $CAD002->verificarCodigoProduto($form);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
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
							} </script></div>
EOT;
						}
				 	}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
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
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula, true));
				
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				$form->campoSetarValor('D009A_Flag_Pertence_Empresa', $CAD002->obterFlagProdutoPertenceEmpresa($acaoId));
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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
	break;
	
	case 'form2Comum':
		log(time());
		global $g;
		gHabilitaNavegacao($g['divId'], 'CAD002', 'form2Comum');
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// O gProcessaAcoes envia o divId como o mesmo valor de $content1 usada no content.php.
		$enviar_btnId = 'content_form2_btnEditarProduto_' . $g['divId'];
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/form2Comum/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"D001_Codigo_Produto":{"top":6.969970703125,"left":54.856003284454346,"width":392.039216,"hidden":0},"D049_D082_Id":{"top":6.969970703125,"left":612.208954334259,"width":255,"hidden":0},"D001_D015_Id":{"top":0,"left":0,"width":189.960784,"hidden":1},"D001_Codigo_Barras":{"top":29.993865966796875,"left":109.88051652908325,"width":136.039216,"hidden":0},"D001_Codigo_Barras_Tributavel":{"top":29.993865966796875,"left":322.80944871902466,"width":136.039216,"hidden":0},"D049_D024_Id":{"top":29.993865966796875,"left":594.9754948616028,"width":233.039216,"hidden":0},"D009_Aliquota_ICMS_Tabela":{"top":0,"left":0,"width":75,"hidden":1},"D001_Descricao_Produto":{"top":52.97181701660156,"left":82.27634477615356,"width":392.039216,"hidden":0},"D049_Flag_Tipo":{"top":52.97181701660156,"left":621.6758732795715,"width":256,"hidden":0},"D049_Modelo":{"top":0,"left":0,"width":174,"hidden":1},"D001_Descricao_Ingles":{"top":75.98039245605469,"left":57.61335802078247,"width":392.039216,"hidden":0},"D001_D037_Id":{"top":75.98039245605469,"left":597.3958439826965,"width":255,"hidden":0},"D009_Flag_Promocao":{"top":0,"left":0,"width":13,"hidden":1},"D049_IPV":{"top":0,"left":0,"width":78,"hidden":1},"D001A_Preco_Nuvemshop":{"top":0,"left":0,"width":71,"hidden":1},"D001_D060_Id":{"top":98.97366333007812,"left":105.30024766921997,"width":399,"hidden":0},"D001_Flag_Tipo_Item":{"top":98.98898315429688,"left":595.7567448616028,"width":254,"hidden":0},"D049_IPV2":{"top":0,"left":0,"width":78,"hidden":1},"D049_Flag_Ativo":{"top":0,"left":0,"width":87.96078399999999,"hidden":1},"D001A_Tipo_Zincagem":{"top":0,"left":0,"width":90.96078399999999,"hidden":1},"D001_C008_Id":{"top":121.98223876953125,"left":102.17524766921997,"width":399,"hidden":0},"D009_Quantidade_Estoque":{"top":121.99755859375,"left":610.7843327522278,"width":72.03921600000001,"hidden":0},"Pedido":{"top":121.99755859375,"left":786.8872380256653,"width":72.03921600000001,"hidden":0},"D049_Flag_Substituicao_Tributaria":{"top":0,"left":0,"width":87.96078399999999,"hidden":1},"D049_Observacao_Compra":{"top":0,"left":0,"height":15,"width":88,"hidden":1},"D001_D003_Id":{"top":144.99081420898438,"left":108.88480424880981,"width":399,"hidden":0},"D009_Quantidade_Estoque_Liquido":{"top":144.99081420898438,"left":614.552704334259,"width":72.03921600000001,"hidden":0},"Separado":{"top":144.99081420898438,"left":780.1317448616028,"width":72.03921600000001,"hidden":0},"D009_Data_Alteracao_Custo":{"top":0,"left":0,"width":72,"hidden":1},"D049_Codigo_Produto_Fornecedor":{"top":0,"left":0,"width":174,"hidden":1},"D001_D002_Id":{"top":167.98406982421875,"left":87.10171556472778,"width":399,"hidden":0},"D009_Quantidade_Estoque_Loja":{"top":167.9993896484375,"left":629.7947087287903,"width":72.03921600000001,"hidden":0},"D009_Quantidade_OC":{"top":167.9993896484375,"left":811.8719182014465,"width":72.03921600000001,"hidden":0},"D009_Percentual_Desconto_Tabela":{"top":0,"left":0,"width":75,"hidden":1},"D001_D005_Id":{"top":190.97732543945312,"left":106.54105424880981,"width":399,"hidden":0},"D009_Quantidade_Estoque_Fora":{"top":190.99264526367188,"left":628.8756413459778,"width":72.03921600000001,"hidden":0},"D009_Quantidade_Estoque_Similar":{"top":190.99264526367188,"left":781.311249256134,"width":72.03921600000001,"hidden":0},"D049_Percentual_Acrescimo_Tabela":{"top":0,"left":0,"width":72,"hidden":1},"D049_Origem_Mercadoria":{"top":213.9705810546875,"left":40.77818727493286,"width":400,"hidden":0},"D009_Origem_Mercadoria":{"top":0,"left":0,"width":104.96078399999999,"hidden":1},"D001_Especificacoes":{"top":236.97915649414062,"left":64.90502214431763,"height":39.039215999999996,"width":392.039216,"hidden":0},"D009_Valor_Custo_Tabela":{"top":236.99447631835938,"left":589.0012516975403,"width":72.03921600000001,"hidden":0},"D009_Data_Atualizacao_Custo_Fornecedor":{"top":236.99447631835938,"left":753.0024724006653,"width":72.03921600000001,"hidden":0},"D049_Flag_Nacional_Importado":{"top":0,"left":0,"width":127.96078399999999,"hidden":1},"D009_Valor_Custo_Ultima_Compra":{"top":259.98773193359375,"left":548.560028553009,"width":72.03921600000001,"hidden":0},"D009_Data_Ultima_Entrada":{"top":259.98773193359375,"left":753.4620060920715,"width":72.03921600000001,"hidden":0},"D001_Preco_4":{"top":0,"left":0,"width":86,"hidden":1},"infoCustoVendaProduto":{"top":262.9748840332031,"left":948.9736638069153,"width":null,"hidden":0},"D001_Observacao_Compra":{"top":282.98101806640625,"left":46.76776933670044,"height":39.039215999999996,"width":392.039216,"hidden":0},"D009_Valor_Custo_Medio_Unitario":{"top":282.996337890625,"left":588.6182560920715,"width":72.03921600000001,"hidden":0},"D009_Data_Atualizacao_Preco_Tabela":{"top":282.996337890625,"left":755.9895939826965,"width":72.03921600000001,"hidden":0},"D009_ICF_3":{"top":0,"left":0,"width":75,"hidden":1},"D009_Valor_Preco_Tabela":{"top":305.9895935058594,"left":582.8277897834778,"width":72.03921600000001,"hidden":0},"D009_Data_Ultima_Venda":{"top":305.9895935058594,"left":762.8523259162903,"width":72.03921600000001,"hidden":0},"D001_Aplicacao":{"top":0,"left":0,"width":104,"hidden":1},"D009_ICF_2":{"top":0,"left":0,"width":75,"hidden":1},"D001_Peso_Total_Kg":{"top":0,"left":0,"width":56,"hidden":1},"D001_Peso_Unitario_Kg":{"top":328.98284912109375,"left":18.71936273574829,"width":88.03921600000001,"hidden":0},"D049_Quantidade_Embalagem_Compra":{"top":328.98284912109375,"left":356.5716862678528,"width":56.039215999999996,"hidden":0},"D009_ICF_1":{"top":328.9981689453125,"left":634.3750309944153,"width":72.03921600000001,"hidden":0},"D009_Valor_Custo_Unitario":{"top":328.9981689453125,"left":770.8180241584778,"width":72.03921600000001,"hidden":0},"D009_Valor_Preco_Importado":{"top":0,"left":0,"width":72,"hidden":1},"D001_Peso_Unitario_Bruto":{"top":351.97607421875,"left":28.37009859085083,"width":88.03921600000001,"hidden":0},"D001_Qte_Embalagem":{"top":351.97607421875,"left":364.55270433425903,"width":56.039215999999996,"hidden":0},"D009_Preco_1":{"top":351.99139404296875,"left":613.2812809944153,"width":72.03921600000001,"hidden":0},"D009_IPT_1":{"top":351.99139404296875,"left":735.998749256134,"width":40.039215999999996,"hidden":0},"D009_IPV_1":{"top":351.99139404296875,"left":800.7659611701965,"width":72.03921600000001,"hidden":0},"D009_Valor_Custo_Compra":{"top":0,"left":0,"width":72,"hidden":1},"D009_Flag_Usar_Custo":{"top":0,"left":0,"width":263.960784,"hidden":1},"D001_Cubagem_Unitaria":{"top":375,"left":91.5900731086731,"width":88.03921600000001,"hidden":0},"D001_Flag_Etiqueta_Pedido":{"top":375,"left":398.31494188308716,"width":64,"hidden":0},"D009_Preco_2":{"top":375,"left":613.2812809944153,"width":72.03921600000001,"hidden":0},"D009_IPT_2":{"top":375,"left":735.998749256134,"width":40.039215999999996,"hidden":0},"D009_IPV_2":{"top":375,"left":800.7659611701965,"width":72.03921600000001,"hidden":0},"D049_Flag_Nao_Comprar":{"top":397.97796630859375,"left":96.15502214431763,"width":96,"hidden":0},"D001_Flag_Pre_Cadastro":{"top":397.97796630859375,"left":404.65686082839966,"width":64,"hidden":0},"D009_Preco_3":{"top":397.9932861328125,"left":613.2812809944153,"width":72.03921600000001,"hidden":0},"D009_IPT_3":{"top":397.9932861328125,"left":735.998749256134,"width":40.039215999999996,"hidden":0},"D009_IPV_3":{"top":397.9932861328125,"left":800.7659611701965,"width":72.03921600000001,"hidden":0},"cad002GridHistoricoPrecos":{"top":0,"left":0,"width":null,"hidden":1},"D001_Descricao_Comercial":{"top":0,"left":0,"width":392,"hidden":1},"D049_Data_Cadastro":{"top":420.97119140625,"left":68.64276933670044,"width":88.03921600000001,"hidden":0},"D001_Flag_Ativo":{"top":420.97119140625,"left":418.59680223464966,"width":64,"hidden":0},"D009_Preco_4":{"top":420.98651123046875,"left":613.2812809944153,"width":72.03921600000001,"hidden":0},"D009_IPT_4":{"top":420.98651123046875,"left":735.998749256134,"width":40.039215999999996,"hidden":0},"D009_IPV_4":{"top":420.98651123046875,"left":800.7659611701965,"width":72.03921600000001,"hidden":0},"D009_Preco_Pauta_ST":{"top":0,"left":0,"width":72,"hidden":1},"D001_Quantidade_Tributavel":{"top":443.97979736328125,"left":18.795956134796143,"width":88.03921600000001,"hidden":0},"D001A_Flag_Validade":{"top":443.97979736328125,"left":409.6047673225403,"width":63,"hidden":0},"D009A_Preco_5":{"top":443.9951171875,"left":613.2812809944153,"width":71.03921600000001,"hidden":0},"D009A_IPT_5":{"top":443.9951171875,"left":735.998749256134,"width":40.039215999999996,"hidden":0},"D009A_IPV_5":{"top":443.9951171875,"left":800.7659611701965,"width":71.03921600000001,"hidden":0},"D049_IPV1":{"top":0,"left":0,"width":78,"hidden":1},"D001A_Codigo_IMPA":{"top":466.9730224609375,"left":75.19914293289185,"width":88.03921600000001,"hidden":0},"D001A_Flag_Produto_Emite_Certificado":{"top":466.9730224609375,"left":392.0496220588684,"width":63,"hidden":0},"D009_Preco_Loja":{"top":466.98834228515625,"left":598.6672673225403,"width":72.03921600000001,"hidden":0},"D009_Preco_Loja_Real":{"top":466.98834228515625,"left":750.6893620491028,"width":72.03921600000001,"hidden":0},"D001A_D187_Id":{"top":512.9901733398438,"left":75.36764574050903,"width":400,"hidden":0},"D001_Flag_Ecommerce":{"top":0,"left":0,"width":64.960784,"hidden":1},"D001A_Flag_Kanban":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"D001_Flag_Tipo":{"top":0,"left":0,"width":407.960784,"hidden":1},"D041_Valor_Preco_Revenda":{"top":0,"left":0,"width":78,"hidden":1},"D041_Valor_Base":{"top":0,"left":0,"width":78,"hidden":1},"D049_Valor_Preco_KG":{"top":0,"left":0,"width":86,"hidden":1},"D001_CNPJ_Fabricante":{"top":0,"left":0,"width":117,"hidden":1},"D041_Valor_Custo":{"top":0,"left":0,"width":78,"hidden":1},"D041_Valor_Preco_Consumidor":{"top":0,"left":0,"width":78,"hidden":1},"D001_C004_Id":{"top":0,"left":0,"width":148.960784,"hidden":1},"D009A_Flag_Pertence_Empresa":{"top":489.9969482421875,"left":40.67095613479614,"width":95,"hidden":0},"D001_Preco_3":{"top":0,"left":0,"width":86,"hidden":1},"D001_Preco_2":{"top":0,"left":0,"width":86,"hidden":1},"D001_Flag_Multimarcas":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"D009_Quantidade_OP":{"top":0,"left":0,"width":98,"hidden":1},"D001_Preco_1":{"top":0,"left":0,"width":86,"hidden":1}}'
		);
		
		$D009 = mysql_query("SELECT D009_Id, D009_Flag_Preco_Tabelado, D009_Flag_Promocao, D001_C008_Id, D001_D005_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D049_D001_Id='{$acaoId}' AND D009_C004_Id='{$g['empresaAtual']}'");
		$mD009 = mysql_fetch_array($D009);
		if($mD009['D009_Flag_Preco_Tabelado'] == 'S'){
			$checkedTabelado="checked";
		} else {
			$checkedTabelado="";
		}

		if($mD009['D009_Flag_Promocao'] == 'S'){
			$checked="checked";
		} else {
			$checked="";
		}

		$D037 = mysql_query("SELECT D037_Unidade FROM D037 LEFT JOIN D005 ON D005_D037_Id_Tributavel=D037_Id WHERE D005_Id='{$mD009['D001_D005_Id']}'");
		$mD037 = mysql_fetch_array($D037);
		if(strlen($mD037['D037_Unidade']) > 2){
			$mD037['D037_Unidade'] = substr($mD037['D037_Unidade'], 0, 2);
		}else if(empty($mD037['D037_Unidade'])){
			$mD037['D037_Unidade'] = "??";
		}

		$g['form2Comum_D001_C008_Id'] = $mD009['D001_C008_Id'];

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form2Comum');
		$formArray = array(
			'campos' => array(
				'geral' => array(
		        	'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label'=>'Código Produto', 'type' => 'text','length' => array(30,20), 'readonly' => (empty($r_acaoId) && !empty($g['C031']['mascaraCodigoProduto'])) ? true : false, 'restricao'=>  empty($r_acaoId) ? false : true),
					'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						),
						//'extra' => "onchange=\"if ($(this).val() == 'S') { $('#{$g['divId']} .itemForm-D001_Codigo_Barras').val('').attr('readonly', 'readonly'); } else { $('#{$g['divId']} .itemForm-D001_Codigo_Barras').removeAttr('readonly'); } \""
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Descricao_Ingles' => array('name' => 'D001_Descricao_Ingles', 'label'=>'Descrição Inglês', 'type' => 'text', 'length' => array(64, 60)),
					'D001_Descricao_Comercial' => array('name' => 'D001_Descricao_Comercial', 'label'=>'Descrição Comercial', 'type' => 'text', 'length' => array(64, 40)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'EAN', 'type' => 'text', 'length' => array(13, 13), 'readonly' => (empty($r_acaoId) && !empty($g['C031']['mascaraCodigoProduto'])) ? true : false, 'restricao'=>  empty($r_acaoId) ? false : true), //, 'extra' => 'onchange="if (!isempty($(this).val())) { $(this).val(strPad($(this).val(), 13, \'0\')); }"'
		        	'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'type' => 'select', 'select'=>
	                	gGeraSelect('C008', 'C008_Id', 'C008_Tipo', 'order by C008_Id', true)
	                ),
	                'D001_C004_Id' => array('name' => 'D001_C004_Id', 'label'=>'Empresa', 'type' => 'select', 'select' =>
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa', 'where C004_flag_Ativo="S" order by C004_Nome_Empresa', true, @$r_C004_Id)
					),
					'D009A_Flag_Pertence_Empresa' => array('name' => 'D009A_Flag_Pertence_Empresa', 'label' => 'Pertence à empresa', 'type' => 'select', 'reject' => true, 'select' => array(
						array('title' => '', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)),
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'restricao' => false, 'type' => 'select',  'select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select', 'select' => 
                		gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo', ' order by D060_Descricao_Grupo ', true)
                	),
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'restricao' => false, 'type' => 'select', 'select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg', 'casasDecimais' => 3,'label'=>'Peso Unitário Liquido Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Peso_Unitario_Bruto' => array('name' => 'D001_Peso_Unitario_Bruto', 'casasDecimais' => 3,'label'=>'Peso Unitário Bruto Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'casasDecimais' => 3,'label'=>'Qtd embalagem venda', 'type' => 'moeda', 'value' => '0,000', 'length' => array(15, 30), 'align' => 'right', 'style'=>'float:right;'),
					'D001_Cubagem_Unitaria' => array('name' => 'D001_Cubagem_Unitaria', 'casasDecimais' => 6, 'label'=>'Cubagem', 'type' => 'moeda', 'value' => '0,000000', 'length' => array(20, 30)),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg','casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true)
					),
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select', 'select' =>
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
                	),
					'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(3, 61) ),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Tipo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'ADQUIRIDO', 'value' =>'COMPRADO', 'selected' => true),
							array('title' => 'PRODUZIDO', 'value' =>'PRODUZIDO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Aplicacao' => array('name' => 'D001_Aplicacao', 'label'=>'Referência', 'type' => 'text', 'length' => array(64, 40)),
					'D049_Id' => array('name' => 'D049_Id', 'type' => 'hidden', 'reject' => true),
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select', 'select'=>
						//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
						gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true), 'reject' => true,
					),
					'D049_Flag_Tipo' =>  array('name' => 'D049_Flag_Tipo', 'label' => 'Tipo', 'type'=>'select','align' => 'right', 'reject' => true, 'select' => 
					    array(
					       array('title' => '...', 'value' => ''),
					       array('title' => 'COMPRADO', 'value' => 'CP', 'selected' => true),
					       array('title' => 'PRODUZIDO', 'value' => 'PD'),
					       array('title' => 'MONTADO', 'value' => 'MT'),
					       array('title' => 'MODIFICADO', 'value' => 'MD'),
					    ),
					),
					'D001_Flag_Tipo_Item' => array('name' => 'D001_Flag_Tipo_Item', 'label' => 'Categoria', 'type' => 'select', 'select' =>
						array(
							array('title' => '00-Mercadoria para Revenda', 'value'=>'00'),
							array('title' => '01-Matéria-Prima', 'value' =>'01'),
							array('title' => '02-Embalagem', 'value' =>'02'),
							array('title' => '03-Produto em Processo', 'value' =>'03'),
							array('title' => '04-Produto Acabado', 'value' =>'04'),
							array('title' => '05-Subproduto', 'value' =>'05'),
							array('title' => '06-Produto Intermediário', 'value' =>'06'),
							array('title' => '07-Material de Uso e Consumo', 'value' =>'07'),
							array('title' => '08-Ativo Imobilizado', 'value' =>'08'),
							array('title' => '09-Serviços', 'value' =>'09'),
							array('title' => '10-Outros insumos', 'value' =>'10'),
							array('title' => '99-Outras', 'value' =>'99')
						)
					),
					'D049_Modelo' => array('name' => 'D049_Modelo', 'label' => 'Modelo', 'type'=>'text', 'reject' => true, 'length' => array(26,30)),
					'D049_Codigo_Produto_Fornecedor' => array('name' => 'D049_Codigo_Produto_Fornecedor', 'label' => 'Código Original','align' => 'right','style' => 'float:right;', 'reject' => true, 'type'=>'text', 'length' => array(26,30)),
					//'D049_Valor_Preco_Tabela' => array('name' => 'D049_Valor_Preco_Tabela', 'label' => 'Preço Tabela', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15)),
					//'D049_Percentual_Desconto_Tabela' => array('name' => 'D049_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'moeda', 'reject' => true, 'value' => '0,00',	'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Percentual_Acrescimo_Tabela' => array('name' => 'D049_Percentual_Acrescimo_Tabela', 'label' => 'Acrescimo Tabela', 'type'=>'moeda', 'reject' => true, 'value' => '0,00',	'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					//'D049_Aliquota_ICMS_Tabela' => array('name' => 'D049_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15)),
					'D049_IPV' => array('name' => 'D049_IPV', 'label' => 'IPV 1', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D049_IPV1' => array('name' => 'D049_IPV1', 'label' => 'IPV 2', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D049_IPV2' => array('name' => 'D049_IPV2', 'label' => 'IPV 3', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					//'D049_ICF' => array('name' => 'D049_ICF', 'label' => 'ICF 1', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'reject' => true, 'length' => array(10,15)),
					//'D049_Flag_Promocao' => array('name' => 'D049_Flag_Promocao', 'label' => 'Promoção', 'extra'=>$checked, 'type'=>'checkbox','reject' => true),
					//'D049_ICF1' => array('name' => 'D049_ICF1', 'label' => 'ICF 2', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'reject' => true, 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					//'D049_ICF2' => array('name' => 'D049_ICF2', 'label' => 'ICF 3', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'reject' => true, 'length' => array(10,15)),
					'D049_Valor_Preco_KG' => array('name' => 'D049_Valor_Preco_KG', 'label' => 'Preço Kg', 'type'=>'moeda', 'casasDecimais'	=> 4, 'value' => '0,0000', 'reject' => true, 'length' => array(10,15)),
					'D049_Observacao_Compra' => array('name' => 'D049_Observacao_Compra', 'label' => 'Observação Compra', 'type'=>'textarea', 'reject' => true, 'length' => array(1,30)),
					'D049_Flag_Nacional_Importado' => array('name' => 'D049_Flag_Nacional_Importado', 'label' => 'Nacional/Importado', 'reject' => true, 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'NACIONAL', 'value' => 'N'),
							array('title' => 'IMPORTADO', 'value' => 'I'),
						),
					),
					'D049_D024_Id' => array('name' => 'D049_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa', 'reject'=>true, 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa','WHERE D024_Flag_Fornecedor = "S"'), 'length' => array(80,30)),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_Data_Ultima_Entrada' => array('name' => 'D009_Data_Ultima_Entrada', 'label' => 'Data entrada', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'disabled' => true),
					'D009_Data_Alteracao_Custo' => array('name' => 'D009_Data_Alteracao_Custo', 'label' => 'Data alteração', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'disabled' => true),
					'D009_Data_Atualizacao_Custo_Fornecedor' => array('name' => 'D009_Data_Atualizacao_Custo_Fornecedor', 'label' => 'Data alt. tabela', 'type'=>'data', 'length' => array(10,15), 'reject' => true, 'disabled' => true),
					'D049_Flag_Ativo' => array('name' => 'D049_Flag_Ativo', 'label' => 'Ativo', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S','selected'=>true),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Flag_Substituicao_Tributaria' => array('name' => 'D049_Flag_Substituicao_Tributaria', 'reject' => true, 'label' => 'Substituição Tributária', 'type'=>'select', 'align' => 'right','style' => 'float:right;', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N','selected'=>true),
						),
					),
					'D009_Flag_Usar_Custo' => array('name' => 'D009_Flag_Usar_Custo', 'label' => 'Custo', 'type' => 'select', 'reject' => true, 'select' =>
						array(
							array('title' => '1 - Usar custo médio', 'value'=>'1', 'selected'=>true),
							array('title' => '2 - Usar custo tabela', 'value'=>'2'),
						),
					),
					'D009_Valor_Custo_Unitario' => array('name' => 'D009_Valor_Custo_Unitario', 'label' => 'Custo x ICF', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D009_Valor_Custo_Medio_Unitario' => array('name' => 'D009_Valor_Custo_Medio_Unitario', 'label' => '<u>Custo médio</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Valor_Custo_Compra' => array('name' => 'D009_Valor_Custo_Compra', 'label' => 'Custo', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Valor_Custo_Ultima_Compra' => array('name' => 'D009_Valor_Custo_Ultima_Compra', 'label' => 'Custo compra', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D009_Valor_Custo_Tabela' => array('name' => 'D009_Valor_Custo_Tabela', 'label' => '<u>Custo tabela</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),	
					'D009_Valor_Custo_Importado' => array('name' => 'D009_Valor_Custo_Importado', 'label' => 'Custo ME', 'type'=>'hidden', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),	

					'D009_Preco_Loja' => array('name' => 'D009_Preco_Loja', 'label' => '<u>Preço Loja</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15)),
					'D009_Preco_Loja_Real' => array('name' => 'D009_Preco_Loja_Real', 'label' => '<u>Preço Loja Real</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),

					// novos campos de preços da D009
					'D009_Valor_Preco_Importado' => array('name' => 'D009_Valor_Preco_Importado', 'label' => 'Preço importado',  'type' => 'moeda', 'reject' => true, 'disabled' => true, 'length' => array(10,15)),

					'D009_Preco_1' => array('name' => 'D009_Preco_1', 'label' => 'Preço 1', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D009_Preco_2' => array('name' => 'D009_Preco_2', 'label' => 'Preço 2', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D009_Preco_3' => array('name' => 'D009_Preco_3', 'label' => 'Preço 3', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D009_Preco_4' => array('name' => 'D009_Preco_4', 'label' => 'Preço 4', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D009A_Preco_5' => array('name' => 'D009A_Preco_5', 'label' => 'Preço 5', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),

					'D001_Preco_1' => array('name' => 'D001_Preco_1', 'label' => 'Preço 1', 'type'=>'moeda', 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D001_Preco_2' => array('name' => 'D001_Preco_2', 'label' => 'Preço 2', 'type'=>'moeda', 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D001_Preco_3' => array('name' => 'D001_Preco_3', 'label' => 'Preço 3', 'type'=>'moeda', 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D001_Preco_4' => array('name' => 'D001_Preco_4', 'label' => 'Preço 4', 'type'=>'moeda', 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),

					

					// Campos de preços da D041
					'D041_Valor_Base' => array('name' => 'D041_Valor_Base', 'label' => 'Preço 1', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
					'D041_Valor_Preco_Consumidor' => array('name' => 'D041_Valor_Preco_Consumidor', 'label' => 'Preço 2', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
					'D041_Valor_Preco_Revenda' => array('name' => 'D041_Valor_Preco_Revenda', 'label' => 'Preço 3', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
					'D041_Valor_Custo' => array('name' => 'D041_Valor_Custo', 'label' => 'Novo custo', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),

					'D009_ICF_1' => array('name' => 'D009_ICF_1', 'label' => 'ICF 1', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
					'D009_ICF_2' => array('name' => 'D009_ICF_2', 'label' => 'ICF 2', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
					'D009_ICF_3' => array('name' => 'D009_ICF_3', 'label' => 'ICF 3', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),

					'D009_IPV_1' => array('name' => 'D009_IPV_1', 'label' => 'IPV 1', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPV_2' => array('name' => 'D009_IPV_2', 'label' => 'IPV 2', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPV_3' => array('name' => 'D009_IPV_3', 'label' => 'IPV 3', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPV_4' => array('name' => 'D009_IPV_4', 'label' => 'IPV 4', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009A_IPV_5' => array('name' => 'D009A_IPV_5', 'label' => 'IPV 5', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),

					'D009_Preco_Pauta_ST' => array('name' => 'D009_Preco_Pauta_ST', 'label' => 'Preço Pauta', 'type' => 'moeda','casasDecimais' => 4, 'length' => array(10,15),'reject' => true, 'align' => 'right', 'style'=>'float:right;'),

					'D009_IPT_1' => array('name' => 'D009_IPT_1', 'label' => '', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPT_2' => array('name' => 'D009_IPT_2', 'label' => '', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPT_3' => array('name' => 'D009_IPT_3', 'label' => '', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPT_4' => array('name' => 'D009_IPT_4', 'label' => '', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009A_IPT_5' => array('name' => 'D009A_IPT_5', 'label' => '', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),

					'D009_Data_Ultima_Venda' => array('name' => 'D009_Data_Ultima_Venda', 'label' => 'Última venda', 'type' => 'data', 'length' => array(10,15), 'reject' => true, 'align' => 'right'),
					'D009_Valor_Preco_Tabela' => array('name' => 'D009_Valor_Preco_Tabela', 'label' => 'Custo manual', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15)),
					'D009_Data_Atualizacao_Preco_Tabela' => array('name' => 'D009_Data_Atualizacao_Preco_Tabela', 'label' => 'Data alt. custo', 'type'=>'data', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Aliquota_ICMS_Tabela' => array('name' => 'D009_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15)),
					'D009_Percentual_Desconto_Tabela' => array('name' => 'D009_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'moeda', 'reject' => true, 'value' => '0,00',	'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D009_Flag_Promocao' => array('name' => 'D009_Flag_Promocao', 'label' => 'Promoção', 'extra'=>$checked, 'type'=>'checkbox','reject' => true),

					'cad002GridHistoricoPrecos' => array('name' => 'cad002GridHistoricoPrecos', 'type' => 'button','value' =>'Histórico Preços','reload' => false,'reject'=>true),
					//'D009_Flag_Preco_Tabelado' => array('name' => 'D009_Flag_Preco_Tabelado', 'label' => 'Preço tabelado', 'extra'=>@$checkedTabelado, 'type'=>'checkbox','reject' => true),
					'D009_Quantidade_Estoque_Liquido' => array('name' => 'D009_Quantidade_Estoque_Liquido', 'label' => 'Estoque Liquido', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque_Loja' => array('name' => 'D009_Quantidade_Estoque_Loja', 'label' => 'Loja', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque_Fora' => array('name' => 'D009_Quantidade_Estoque_Fora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [700,400]); return false;\">Fora</a>", 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque_Similar' => array('name' => 'D009_Quantidade_Estoque_Similar', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [850,400]); return false;\">Similares</a>", 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_Estoque' => array('name' => 'D009_Quantidade_Estoque', 'label' => 'Estoque', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Quantidade_OC' => array('name' => 'D009_Quantidade_OC', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent('{$mD009['D009_Id']}'), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'reject' => true, 'disabled' => true),
					'D009_Quantidade_OP' => array('name' => 'D009_Quantidade_OP', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', '&acaoId=' + encodeURIComponent('{$mD009['D009_Id']}'), [700,400]); return false;\">OP</a>", 'type' => 'moeda', 'reject' => true, 'disabled' => true),
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedidos', 'type'=>'moeda', 'value' => '0', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'Separado' => array('name' => 'Separado', 'label' => 'Separado', 'type'=>'texto', 'value' => '0', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
					'D009_Origem_Mercadoria' => array('name' => 'D009_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
					'D001_Flag_Multimarcas' => array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'infoCustoVendaProduto' => array('name' => 'infoCustoVendaProduto', 'label' => '', 'type' => 'titulo', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					//Campos utilizado quando o produto é cadastrado a partir do e-mail
					'T139_Id' => array('name' => 'T139_Id', 'type' => 'hidden', 'reject' => true, 'value' => @$r_T139_Id),
					'C007_Id' => array('name' => 'C007_Id', 'type' => 'hidden', 'reject' => true, 'value' => @$r_C007_Id),
					'file' => array('name' => 'file', 'type' => 'hidden', 'reject' => true, 'value' => @$r_file),
					'divId' => array('name' => 'divId', 'type' => 'hidden', 'reject' => true, 'value' => $g['divId']),
					'D001_Flag_Ecommerce' => array('name' => 'D001_Flag_Ecommerce', 'label'=>'Disponível E-commerce?','type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Flag_Etiqueta_Pedido' => array('name' => 'D001_Flag_Etiqueta_Pedido', 'label'=>'Etiqueta pedido','type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D049_Quantidade_Embalagem_Compra' => array('name' => 'D049_Quantidade_Embalagem_Compra', 'label' => 'Qtd embalagem compra', 'type'=>'moeda', 'reject' => true, 'length' => array(10,15)),
                	'D001_Codigo_Barras_Tributavel' => array('name' => 'D001_Codigo_Barras_Tributavel', 'label'=>'EAN Tributável', 'type' => 'text', 'length' => array(13, 13)), 
                	'D001_Quantidade_Tributavel' => array('name' => 'D001_Quantidade_Tributavel', 'casasDecimais' => 3,'label'=>"Quantidade Tributável {$mD037['D037_Unidade']}", 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_CNPJ_Fabricante' => 	array('name' => 'D001_CNPJ_Fabricante', 'label' => 'CNPJ Fabricante', 'type' => 'cnpj', 'length' => array(18,18)),
					//flag nao comprar esta definido como default (nao), alterado de nao->sim e sim->nao para ficar mais facil compreensao do cliente
					'D049_Flag_Nao_Comprar' => 	array('name' => 'D049_Flag_Nao_Comprar', 'label' => 'Comprar', 'reject' => true, 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' =>'N', 'selected' => true),
							array('title' => 'Não', 'value'=>'S')
						)
					),
					'D001A_Flag_Kanban' => array(
						'name' => 'D001A_Flag_Kanban', 'label' => 'Kanban', 'type' => 'select', 'style' => 'float:right;', 'align' => 'right', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'D001A_Flag_Produto_Emite_Certificado' => array(
						'name' => 'D001A_Flag_Produto_Emite_Certificado', 'label' => 'Envia Certificado', 'type' => 'select', 'style' => 'float:right;', 'align' => 'right', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'D001A_Flag_Validade' => array('name' => 'D001A_Flag_Validade', 'label' => 'Tem validade','type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' =>'N','selected' => true),
							array('title' => 'Sim', 'value' =>'S')
						)
					),
					'D001A_Codigo_IMPA' => array('name' => 'D001A_Codigo_IMPA', 'label'=>'Código IMPA', 'type' => 'text', 'length' => array(20,20)),
					'D001A_Preco_Nuvemshop' => array('name' => 'D001A_Preco_Nuvemshop', 'label' => 'Preço Nuvemshop', 'type'=>'moeda', 'length' => array(10,15), 'casasDecimais' => $g['c004']['C004_Casas_Decimais_Preco']),
					'D001A_D187_Id' => array('name' => 'D001A_D187_Id', 'label'=>'Tipo Produto', 'type' => 'select','select' =>
						gGeraSelect('D187', 'D187_Id', 'D187_Codigo,D187_Descricao', 'order by D187_Codigo', true)
					),
					'D001_Observacao_Compra' => array('name' => 'D001_Observacao_Compra', 'label'=>'Observação Venda', 'type' => 'textarea', 'length' => array(3, 61)),
					'D001A_Tipo_Zincagem' => array('name' => 'D001A_Tipo_Zincagem', 'label' => 'Tipo de Zincagem','type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' =>'','selected' => true),
							array('title' => 'Branco', 'value' =>'BR'),
							array('title' => 'Amarelo', 'value' =>'AM'),
							array('title' => 'Azul', 'value' =>'AZ'),
							array('title' => 'Fogo', 'value' =>'FG'),
							array('title' => 'Ácido', 'value' =>'AC'),
							array('title' => 'Preto', 'value' =>'PT')
						)
					),
                ),

            ) // 'campos'
		);

		if($g['C031']['multimarcas'] == 'ambas'){
			$sql = mysql_query("SELECT * FROM D049 WHERE D049_D001_Id = '{$r_acaoId}'");
			if(mysql_num_rows($sql) == 1){
				$formArray['campos']['geral']['D001_Flag_Multimarcas'] = array('name' => 'D001_Flag_Multimarcas', 'label'=>'Multimarcas', 'type' => 'select', 'select' =>
					array(
						array('title' => 'Sim', 'value'=>'S'),
						array('title' => 'Não', 'value' =>'N')
					)
				);
			}
		}

		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	if(parseFloat($('#{$g['divId']}').css('height')) < 250){
		    		$('#{$g['divId']}').css('overflow', 'auto');
		    	}
				$('#{$g['divId']} #D001_Flag_Multimarcas').change(function() {
					if (($(this).val() == 1)) {
						$.getJSON('/cad/cad002/form_func-ajax/flagMultimarcas/?ajax=true&D001_Id=' + encodeURIComponent({$acaoId}) + '&Flag=S&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$r_divIdMain}', true);
							}
						});
					}
				});
				$('#{$g['divId']} #D001_Flag_Multimarcas').trigger('change');
				$('#cad002GridHistoricoPrecos').unbind('click');
				$('#cad002GridHistoricoPrecos').bind('click', function(data) {
					var id = '{$mD009['D009_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Histórico Preços', '/cad/cad002/content/cad002contentHistóricoPrecos/', '&D009_Id=' + encodeURIComponent(id), [1000,553]);
					return false;
				});

				$('#{$g['divId']} .itemForm-D001_Flag_Pre_Cadastro').trigger('onchange');
			} </script></div>
EOT;
		if(empty($r_acaoId)){
			echo <<<EOT
			    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function(){
						$('#{$g['divId']} #D001_Descricao_Produto').focus();
					}, 100);
				} </script></div>
EOT;
		}
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
					$retorno = $CAD002->verificarCodigoProduto($form,$acaoId);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							$CAD002->atualizarMarca($form, $acaoId);
							//$retorno = $CAD002->D001_reprocessa_historico($r_acaoId, 0, 10);
							$CAD002->cad002AtualizarAtributosIntegracaoTray($acaoId);
							if ($retorno == TRUE)
							{
								echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										//divRefresh('{$g['divId']}', true);
										//console.log('{$r_divIdContentTabProduto}');
										//divSetarAcaoId('{$r_divIdContentTabProduto}', 'true', 'modoFormLeve');
										divRefresh('{$r_divIdMain}', true);
									}</script></div>
EOT;
							}
							else
							{
								echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso! Problemas na atualizacao do estoque!');
							}
						}
					}
				} else {
					// Insert
					require_once("bibliotecas/classes/CAD002.php");
					$CAD002 = new CAD002();
					if($g['C031']['gravarCodProdAoCadastrar'] == 'S'){
						$proxEAN = $CAD002->obterNovoCodigoEAN();
						$form->campoSetarValor('D001_Codigo_Barras', $proxEAN, true);
					}
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Ajuste para pegar D001_Id devido a campos da tabela D001A no formulário
						$D001A_Id = $g['mysqlLastId'];
						$D001A = mysql_query("select D001A_D001_Id from D001A where D001A_Id='{$D001A_Id}'");
						$mD001A = mysql_fetch_array($D001A);
						$D001_Id = $mD001A['D001A_D001_Id'];
						$g['mysqlLastId'] = $D001_Id;
						//--------------------------------------------------------------------------------
						$CAD002->incluirMarca($form);
						if($g['C031']['gravarCodProdAoCadastrar'] == 'S'){
							require_once("bibliotecas/classes/CAD002.php");
							$CAD002 = new CAD002();
							$proxCodigo = $CAD002->obterNovoCodigoProduto($D001_Id);
							mysql_query("UPDATE D001 SET D001_Codigo_Produto = '{$proxCodigo}' WHERE D001_Id = '{$D001_Id}'");
						}
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						if($form->campoValorEnviado('T139_Id') > 0){
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if('{$form->campoValorEnviado('file')}' != ''){
										var buttons = {
											"Sim": function() {
												$('#{$form->campoValorEnviado('divId')}').showLoading();
												$.ajax({
													url: '/etc/etc006/form_func-ajax/arquivarAnexo/',
													dataType: 'jsonp',
													type: 'post',
													data: 'ajax=true&C007_Id={$form->campoValorEnviado('C007_Id')}&file={$form->campoValorEnviado('file')}&Id={$D001_Id}&nomeArquivo={$form->campoValorEnviado('nomeArquivo')}&T139_Id={$form->campoValorEnviado('T139_Id')}&tipo=produto',
													success: function(request) {
														if (request.code) {
															$('#{$form->campoValorEnviado('divId')}').hideLoading();
															setTimeout(function(){
																abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', $('#{$g['divId']}').closest('.ui-dialog-content').data('janelaVars') + '&D001_Id=' + encodeURIComponent('{$D001_Id}'), [1000,600]);
															}, 100);
															dialogConfirm("Ok!", 'Anexo arquivado com Sucesso!');
															fecharJanela("{$g['divId']}");
														} else {
															dialogConfirm("Erro!", request.data);
														}
													}
												});
												$(this).dialog("destroy").remove();
											},
											"Não": function() {
												setTimeout(function(){
													abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', $('#{$g['divId']}').closest('.ui-dialog-content').data('janelaVars') + '&D001_Id=' + encodeURIComponent('{$D001_Id}'), [1000,600]);
												}, 100);
												fecharJanela("{$g['divId']}");
												$(this).dialog("destroy").remove();
											}
										};
										dialogConfirm("Atenção", "Deseja vincular este anexo ao produto?", buttons);
									} else {
										setTimeout(function(){
											abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', $('#{$g['divId']}').closest('.ui-dialog-content').data('janelaVars') + '&D001_Id=' + encodeURIComponent('{$D001_Id}'), [1000,600]);
										}, 100);
										fecharJanela("{$g['divId']}");
									}
								} </script></div>
EOT;
						} else {
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									fecharJanela("{$g['divId']}");
									divRefresh("{$g['divIdParent']}", true);
									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$D001_Id}') + '&tabela=D001', [1000,635]);
								} </script></div>
EOT;
						}
				 	}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
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
				$form->campoSetarSelect('D001_D003_Id', gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', $cClausula, true));
				
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				$sql = mysql_query("SELECT *, D009_Quantidade_Pedido(D009_Id,1) as Pedido,
											  D009_Quantidade_Separado_3(D009_Id) as Separado
				                      FROM D049 
				                 LEFT JOIN D009 ON D009_D049_Id=D049_Id
								 LEFT JOIN D009A ON D009_Id=D009A_D009_Id
				                 LEFT JOIN D041 ON D041_D049_Id=D049_Id     
				                 LEFT JOIN D024 ON D024_Id=D049_D024_Id
								 LEFT JOIN D082 ON D082_Id=D049_D082_Id
				                     WHERE D049_D001_Id = '{$acaoId}' AND D009_C004_Id = '{$g['empresaAtual']}'");
				$res = mysql_fetch_array($sql);
				$form->campoSetarValor('D009A_Flag_Pertence_Empresa', $CAD002->obterFlagProdutoPertenceEmpresa($acaoId));

				$form->campoSetarValor('D049_IPV', gCorrigeNumero($res['D049_IPV'], 4));
				$form->campoSetarValor('D049_IPV1', gCorrigeNumero($res['D049_IPV1'], 4));
				$form->campoSetarValor('D049_IPV2', gCorrigeNumero($res['D049_IPV2'], 4));
				$form->campoSetarValor('D049_Id', $res['D049_Id']);
				$form->campoSetarValor('D049_Flag_Nao_Comprar', $res['D049_Flag_Nao_Comprar']);
				$form->campoSetarValor('D049_D082_Id', $res['D049_D082_Id']);
				$form->formArray['campos']['geral']['D049_D082_Id']['value_Pesquisa'] = $res['D082_Marca'];				
				//$form->campoSetarValor('D049_Flag_Tipo', $g['conf']->camposValorPadrao('D049_Flag_Tipo'));
				$form->campoSetarValor('D049_Flag_Tipo', $res['D049_Flag_Tipo']);
				$form->campoSetarValor('D049_Modelo', $res['D049_Modelo']);
				$form->campoSetarValor('D049_Codigo_Produto_Fornecedor', $res['D049_Codigo_Produto_Fornecedor']);
				$form->campoSetarValor('D049_Origem_Mercadoria', $res['D049_Origem_Mercadoria']);
				$form->campoSetarValor('D009_Origem_Mercadoria', $res['D009_Origem_Mercadoria']);
				$form->campoSetarValor('D049_Valor_Preco_KG', gCorrigeNumero($res['D049_Valor_Preco_KG']));
				$form->campoSetarValor('D009_Valor_Preco_Tabela', gCorrigeNumero($res['D009_Valor_Preco_Tabela'],4));
				$form->campoSetarValor('D009_Percentual_Desconto_Tabela', gCorrigeNumero($res['D009_Percentual_Desconto_Tabela']));
				$form->campoSetarValor('D009_Percentual_Acrescimo_Tabela', gCorrigeNumero($res['D009_Percentual_Acrescimo_Tabela']));
				$form->campoSetarValor('D009_Aliquota_ICMS_Tabela', gCorrigeNumero($res['D009_Aliquota_ICMS_Tabela']));
				$form->campoSetarValor('D009_IPV_1', gCorrigeNumero($res['D009_IPV_1'], 4));
				$form->campoSetarValor('D009_IPV_2', gCorrigeNumero($res['D009_IPV_2'], 4));
				$form->campoSetarValor('D009_IPV_3', gCorrigeNumero($res['D009_IPV_3'], 4));
				$form->campoSetarValor('D009_IPV_4', gCorrigeNumero($res['D009_IPV_4'], 4));
				$form->campoSetarValor('D009A_IPV_5', gCorrigeNumero($res['D009A_IPV_5'], 4));
				$form->campoSetarValor('D009_Preco_Pauta_ST', gCorrigeNumero($res['D009_Preco_Pauta_ST'],4));
				$form->campoSetarValor('D009_IPT_1', gCorrigeNumero($res['D009_IPT_1'], 4));
				$form->campoSetarValor('D009_IPT_2', gCorrigeNumero($res['D009_IPT_2'], 4));
				$form->campoSetarValor('D009_IPT_3', gCorrigeNumero($res['D009_IPT_3'], 4));
				$form->campoSetarValor('D009_IPT_4', gCorrigeNumero($res['D009_IPT_4'], 4));
				$form->campoSetarValor('D009A_IPT_5', gCorrigeNumero($res['D009A_IPT_5'], 4));
				$form->campoSetarValor('D009_ICF_1', gCorrigeNumero($res['D009_ICF_1'], 4));
				$form->campoSetarValor('D009_ICF_2', gCorrigeNumero($res['D009_ICF_2'], 4));
				$form->campoSetarValor('D009_ICF_3', gCorrigeNumero($res['D009_ICF_3'], 4));
				$form->campoSetarValor('D049_Quantidade_Embalagem_Compra', gCorrigeNumero($res['D049_Quantidade_Embalagem_Compra'], 2));
				$form->campoSetarValor('D049_Observacao_Compra', $res['D049_Observacao_Compra']);
				$form->campoSetarValor('D049_Flag_Nacional_Importado', $res['D049_Flag_Nacional_Importado']);
				$form->campoSetarValor('D049_Data_Cadastro', gCorrigeData($res['D049_Data_Cadastro']));
				$form->campoSetarValor('D049_Flag_Ativo', $res['D049_Flag_Ativo']);
				$form->campoSetarValor('D049_Flag_Substituicao_Tributaria', $res['D049_Flag_Substituicao_Tributaria'],4);
				$form->campoSetarValor('D009_Valor_Custo_Unitario', gCorrigeNumero($res['D009_Valor_Custo_Unitario'],4));
				$form->campoSetarValor('D009_Valor_Custo_Medio_Unitario', gCorrigeNumero($res['D009_Valor_Custo_Medio_Unitario'],4));
				$form->campoSetarValor('D009_Valor_Custo_Compra', gCorrigeNumero($res['D009_Valor_Custo_Compra'],4));
				$form->campoSetarValor('D009_Valor_Custo_Ultima_Compra', gCorrigeNumero($res['D009_Valor_Custo_Ultima_Compra'],4));
				$form->campoSetarValor('D009_Valor_Custo_Tabela', gCorrigeNumero($res['D009_Valor_Custo_Tabela'],4));
				$form->campoSetarValor('D009_Valor_Custo_Importado', gCorrigeNumero($res['D009_Valor_Custo_Importado'],2));
				$form->campoSetarValor('D009_Preco_Loja', gCorrigeNumero($res['D009_Preco_Loja'],2));
				$form->campoSetarValor('D009_Preco_Loja_Real', gCorrigeNumero($res['D009_Preco_Loja_Real'],2));
				$form->campoSetarValor('D009_Quantidade_Estoque', gCorrigeNumero($res['D009_Quantidade_Estoque']));
				$form->campoSetarValor('D009_Quantidade_OC', gCorrigeNumero($res['D009_Quantidade_OC']));
				$form->campoSetarValor('D009_Quantidade_OP', gCorrigeNumero($res['D009_Quantidade_OP']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Liquido', gCorrigeNumero($res['D009_Quantidade_Estoque_Liquido']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Loja', gCorrigeNumero($res['D009_Quantidade_Estoque_Loja']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Fora', gCorrigeNumero($res['D009_Quantidade_Estoque_Fora']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Similar', gCorrigeNumero($res['D009_Quantidade_Estoque_Similar']));
				$form->campoSetarValor('D009_Preco_1', gCorrigeNumero($res['D009_Preco_1'],$g['c004']['C004_Casas_Decimais_Preco']));
				$form->campoSetarValor('D009_Preco_2', gCorrigeNumero($res['D009_Preco_2'],$g['c004']['C004_Casas_Decimais_Preco']));
				$form->campoSetarValor('D009_Preco_3', gCorrigeNumero($res['D009_Preco_3'],$g['c004']['C004_Casas_Decimais_Preco']));
				$form->campoSetarValor('D009_Preco_4', gCorrigeNumero($res['D009_Preco_4'],$g['c004']['C004_Casas_Decimais_Preco']));
				$form->campoSetarValor('D009A_Preco_5', gCorrigeNumero($res['D009A_Preco_5'],$g['c004']['C004_Casas_Decimais_Preco']));
				$form->campoSetarValor('D009_Valor_Preco_Importado', gCorrigeNumero($res['D009_Valor_Preco_Importado'],4));
				$form->campoSetarValor('D009_Data_Ultima_Entrada', gCorrigeData($res['D009_Data_Ultima_Entrada']));
				$form->campoSetarValor('D009_Data_Atualizacao_Custo_Fornecedor', gCorrigeData($res['D009_Data_Atualizacao_Custo_Fornecedor']));
				$form->campoSetarValor('D009_Data_Atualizacao_Preco_Tabela', gCorrigeData($res['D009_Data_Atualizacao_Preco_Tabela']));
				$form->campoSetarValor('D009_Data_Ultima_Venda', gCorrigeData($res['D009_Data_Ultima_Venda']));
				$form->campoSetarValor('D009_Data_Alteracao_Custo', gCorrigeData($res['D009_Data_Alteracao_Custo'], true));
				$form->campoSetarValor('D049_D024_Id', $res['D049_D024_Id']);
				$form->campoSetarValor('D009_Flag_Usar_Custo', $res['D009_Flag_Usar_Custo']);
    			$form->formArray['campos']['geral']['D049_D024_Id']['value_Pesquisa'] = $res['D024_Nome_Empresa'];

				$form->campoSetarValor('D041_Valor_Base', gCorrigeNumero($res['D041_Valor_Base']));
				$form->campoSetarValor('D041_Valor_Preco_Consumidor', gCorrigeNumero($res['D041_Valor_Preco_Consumidor']));
				$form->campoSetarValor('D041_Valor_Preco_Revenda', gCorrigeNumero($res['D041_Valor_Preco_Revenda']));
				$form->campoSetarValor('D041_Valor_Custo', gCorrigeNumero($res['D041_Valor_Custo']));

				$form->campoSetarValor('Separado', gCorrigeNumero($res['Separado']));
				$form->campoSetarValor('Pedido', gCorrigeNumero($res['Pedido']));
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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');

		$botaoModoLeve = "";
		if (isset($r_modoFormLeve) && $r_modoFormLeve && $acaoId) {
			//Modo form leve (r_modoFormLeve) e ao montar formulario deu problema para montar o botao habilitar edição na função do framework
			$botaoModoLeve = <<<EOT
				if($("#{$enviar_btnId}").find('span').text()=="Enviar"){
					$("#{$enviar_btnId}").unbind('click.enviaForm').bind('click.enviaForm', function() {
						console.log('{$g['divId']}');
						divSetarAcaoId('{$g['divId']}', '', 'modoFormLeve');
						divRefresh('{$g['divId']}');
						$(this).find('span').css('font-weight', 'normal').html('Enviar');
					}).find('span').css('font-weight', 'bold').html('Habilitar Edição');
				}					
EOT;
		}
		
		$id = 'popover-' . uniqid();
		$id2 = 'popover-' . uniqid();
		$id3 = 'popover-' . uniqid();
		$id4 = 'popover-' . uniqid();
		$id5 = 'popover-' . uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				{$botaoModoLeve}
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').unbind('click').bind('click', function(data) {
					console.log($(this));
					$('.formEditor-D009_Valor_Custo_Ultima_Compra').attr('id', '{$id}');
					if ($('#{$id}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id}').popover('destroy');
								$('#{$id}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id}').popover('show');
								$('#{$id}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Medio_Unitario').attr('id', '{$id2}');
					if ($('#{$id2}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id2}').popover('destroy');
								$('#{$id2}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id2}').popover('show');
								$('#{$id2}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id2}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				
				$('.formEditor-D009_Valor_Custo_Tabela').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Tabela').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Tabela').attr('id', '{$id3}');
					if ($('#{$id3}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCustoTabela/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&D009_Id=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id3}').popover('destroy');
								$('#{$id3}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id3}').popover('show');
								$('#{$id3}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id3}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								var left = $('.popover').position().left - 85;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});

/*				$('.formEditor-D009_Preco_Loja').css('cursor', 'pointer');
				$('.formEditor-D009_Preco_Loja').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Preco_Loja').attr('id', '{$id4}');
					if ($('#{$id4}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/ven/ven001/outros/demonstrativoPrecoVenda/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&extra=D009&acaoId=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id4}').popover('destroy');
								$('#{$id4}').popover({
									html: true,
									placement: 'top',
									title: 'Demonstrativo Preço',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id4}').popover('show');
								$('#{$id4}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id4}').next().find('input:visible:last').focus();
								$('.popover').css('width', 560);
								var left = $('.popover').position().left - 34;
								$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 12);
							}
						});
					}
				});  

*/

				$('.formEditor-D009_Preco_Loja_Real').css('cursor', 'pointer');
				$('.formEditor-D009_Preco_Loja_Real').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Preco_Loja_Real').attr('id', '{$id5}');
					if ($('#{$id5}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/ven/ven001/outro/demonstrativoPrecoVenda/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&extra=D009_Real&acaoId=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id5}').popover('destroy');
								$('#{$id5}').popover({
									html: true,
									placement: 'left',
									title: 'Demonstrativo Preço',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id5}').popover('show');
								$('#{$id5}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('#{$id5}').popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) {
									if (!$(event.target).closest('.popover').size()) {
										$('#{$id5}').popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('body').unbind('mousedown').bind('mousedown', function(event) {
									if (!$(event.target).closest('.popover').size()) {
										$('#{$id5}').popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id5}').next().find('input:visible:last').focus();

								var table = $('.popover').find('table');
								$(table).css('font-size', 12);

								var x = $('.popover').offset().top-23; var y = $('.popover').offset().left+8;
								$('.popover').appendTo('body').css({'z-index': '10000', 'top': x+'px', 'left': y+'px'});
								$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top+23);
								setTimeout(function(){
									if(($('body').height() - $('.popover').position().top) < $('.popover').height()){
										var ajusteX = $('.popover').height() - ($('body').height() - $('.popover').position().top) + 10;
										$('.popover').css('top', $('.popover').position().top - ajusteX + 'px');
										$('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top + ajusteX);
									}
								}, 50);
							}
						});
					}
				});
				$.getJSON('/cad/cad002/form_func-ajax/infoCustoVendaProduto/?ajax=true&D009_Data_Alteracao_Custo=' + encodeURIComponent('{$res['D009_Data_Alteracao_Custo']}') + '&D009_Data_Ultima_Venda=' + encodeURIComponent('{$res['D009_Data_Ultima_Venda']}') + '&D009_Flag_Custo_Utilizado=' + encodeURIComponent('{$res['D009_Flag_Custo_Utilizado']}') + '&callback=?', function(request) {
					if (request.code) {
						$('.formEditor-infoCustoVendaProduto').html(request.data);
					}
				});
			} </script></div>
EOT;
	break;
		
	case 'tributos':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D009_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoTributos';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/tributos/';	
        
        //form editor
        $form_editor = array(
            'geral' => '{"tituloIPI":{"top":4,"left":183,"width":null,"hidden":1},"D009_Flag_Isento_IPI":{"top":30,"left":128,"width":80,"hidden":0},"D009_Percentual_Reducao_IPI":{"top":53,"left":60,"width":75,"hidden":0},"D009_Aliquota_IPI":{"top":76,"left":118,"width":75,"hidden":0},"tituloICMSCompra":{"top":96,"left":154,"width":null,"hidden":1},"D009_Flag_Isento_ICMS":{"top":122,"left":114,"width":80,"hidden":0},"D009_Percentual_Reducao_ICMS":{"top":145,"left":46,"width":75,"hidden":0},"D009_Aliquota_ICMS":{"top":168,"left":104,"width":75,"hidden":0},"tituloICMSVenda":{"top":188,"left":159,"width":null,"hidden":1},"D009_Flag_Isento_ICMS_Venda":{"top":214,"left":114,"width":80,"hidden":0},"D009_Percentual_Reducao_ICMS_Venda":{"top":236.99996948242188,"left":100,"width":75,"hidden":0},"D009_Aliquota_ICMS_Venda":{"top":259.9999694824219,"left":104,"width":75,"hidden":0},"D009_D059_Id":{"top":282.9999694824219,"left":80,"width":350,"hidden":0},"D009_Flag_Base_ICMS_Cheia_NF":{"top":305.9999694824219,"left":29,"width":48,"hidden":0}}'
        );
        
		//Criar a subdivisão do form conforme sistema antigo.
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloIPI' => array('name' => 'tituloIPI', 'type' => 'titulo', 'label' => 'IPI', 'reject' => true),
					'D009_Flag_Isento_IPI' => array('name' => 'D009_Flag_Isento_IPI', 'label' => 'Isento IPI', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_IPI' => array('name' => 'D009_Percentual_Reducao_IPI', 'label' => 'Percentual Redução IPI', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'D009_Aliquota_IPI' => array('name' => 'D009_Aliquota_IPI', 'label' => 'Alíquota IPI', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'tituloICMSCompra' => array('name' => 'tituloICMSCompra', 'type' => 'titulo', 'label' => 'ICMS Compra', 'reject' => true),
					'D009_Flag_Isento_ICMS' => array('name' => 'D009_Flag_Isento_ICMS', 'label' => 'Isento ICMS', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_ICMS' => array('name' => 'D009_Percentual_Reducao_ICMS', 'label' => 'Percentual Redução ICMS', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'D009_Aliquota_ICMS' => array('name' => 'D009_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'tituloICMSVenda' => array('name' => 'tituloICMSVenda', 'type' => 'titulo', 'label' => 'ICMS Venda', 'reject' => true),
					'D009_Flag_Isento_ICMS_Venda' => array('name' => 'D009_Flag_Isento_ICMS_Venda', 'label' => 'Isento ICMS', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Percentual_Reducao_ICMS_Venda' => array('name' => 'D009_Percentual_Reducao_ICMS_Venda', 'label' => 'Redução ICMS', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'D009_Aliquota_ICMS_Venda' => array('name' => 'D009_Aliquota_ICMS_Venda', 'label' => 'Alíquota ICMS', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15)),
					'D009_D059_Id' => array('name' => 'D009_D059_Id', 'label'=>'Situação Tributária', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST, D059_Descricao_Origem', 'where D059_Simples_Nacional=\'S\' order by D059_Codigo_ST,D059_Descricao_Origem' , true),
					),
					'D009_Flag_Base_ICMS_Cheia_NF' => array('name' => 'D009_Flag_Base_ICMS_Cheia_NF', 'label' => 'Emitir Base ICMS Cheia na NF', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2),
						),
					),
					'D009_Id' => array('name' => 'D009_Id', 'type' => 'hidden'),
					'D009_D001_Id' => array('name' => 'D009_D001_Id', 'type' => 'hidden', 'value' => @$r_D001_Id),
					'D009_D024_Id' => array('name' => 'D009_D024_Id', 'type' => 'hidden'),
					'D009_Codigo_Produto_Fornecedor' => array('name' => 'D009_Codigo_Produto_Fornecedor', 'type' => 'hidden'),
					'D009_C004_Id' => array('name' => 'D009_C004_Id', 'type' => 'hidden'),
					'D009_Peso_Unitario_Kg' => array('name' => 'D009_Peso_Unitario_Kg', 'type' => 'hidden'),
					'D009_Aplicacao' => array('name' => 'D009_Aplicacao', 'type' => 'hidden'),
					'D009_Marca' => array('name' => 'D009_Marca', 'type' => 'hidden', 'value' => @$r_acaoId),
					'D009_Prazo_Entrega' => array('name' => 'D009_Prazo_Entrega', 'type' => 'hidden'),
					'D009_Quantidade_Estoque' => array('name' => 'D009_Peso_Unitario_Kg', 'type' => 'hidden'),
					'D009_Quantidade_Maxima_Estoque' => array('name' => 'D009_Quantidade_Maxima_Estoque', 'type' => 'hidden'),
					'D009_Quantidade_Comprar' => array('name' => 'D009_Quantidade_Comprar', 'type' => 'hidden'),
					'D009_Quantidade_Minima_Estoque' => array('name' => 'D009_Quantidade_Minima_Estoque', 'type' => 'hidden'),
					'D009_Quantidade_Seguranca_Estoque' => array('name' => 'D009_Quantidade_Seguranca_Estoque', 'type' => 'hidden'),
					'D009_Valor_Custo_Unitario' => array('name' => 'D009_Valor_Custo_Unitario', 'type' => 'hidden', 'label' => 'Custo x ICF'),
                ),
				
            ) // 'campos'
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
								$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
								echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
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
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
						}
					}
				}else{
				
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
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002AcaoTributos" >Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'juntarMarcas':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002AcaoJuntarMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/juntarMarcas/';
		$extra = "left join D049 on D049_Id=D009_D049_Id
				  left join D001 on D001_Id=D049_D001_Id
                  left join D082 on D049_D082_Id=D082_Id
                where D001_Id=$D001_Id
		        and D009_C004_Id='{$g['empresaAtual']}'";
			// Editor
		$form_editor = array(
			'geral' => '{"D049_Antigo_Id":{"top":7,"left":10,"width":288,"hidden":0},"D049_Novo_Id":{"top":30,"left":16,"width":288,"hidden":0}}',
		);
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_Antigo_Id' => array('name' => 'D049_Antigo_Id', 'label'=>'Marca Antiga', 'type' => 'select', 'select'=>
							gGeraSelect('D009', 'D049_Id', 'D082_Marca, D049_Modelo', $extra, true),
					),
					'D049_Novo_Id' => array('name' => 'D049_Novo_Id', 'label'=>'Marca Nova', 'type' => 'select', 'select'=>
							gGeraSelect('D009', 'D049_Id', 'D082_Marca, D049_Modelo', $extra, true),
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
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
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
		$g['smarty']->display('formEditor.tpl');
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002AcaoJuntarMarcas">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'editarMarca':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		$D049_Id = isset($r_acaoId) ? $r_acaoId : false;
		//$r_acaoId = isset($r_cad002EditarMarca) ? $r_cad002EditarMarca['D049_D082_Id'] : $r_acaoId;
		
		$g['smarty']->assign('navSql', "D049_D001_Id='{$D001_Id}'");
		/*echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T005_Id, T005_Data_Emissao as Data, T005_Data_Entrega as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Entrega, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T005_D024_Id = D024_Id]');
			} </script></div>
EOT;*/
		gHabilitaNavegacao($g['divId'], 'CAD002', 'editarMarca');
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
            'geral' => '{"D049_D082_Id":{"top":7,"left":69,"width":367,"hidden":0},"D049_Flag_Tipo":{"top":30,"left":57,"width":368,"hidden":0},"D049_Origem_Mercadoria":{"top":53,"left":19,"width":368,"hidden":0},"D009_Aliquota_ICMS_Tabela":{"top":53,"left":1040.0000610351562,"width":78,"hidden":1},"D049_Data_Cadastro":{"top":76,"left":37,"width":78,"hidden":0},"D049_Modelo":{"top":76,"left":240,"width":184,"hidden":0},"D009_Flag_Preco_Tabelado":{"top":76,"left":1042.0000610351562,"width":13,"hidden":1},"D009_Valor_Custo_Compra":{"top":99,"left":1070.0000610351562,"width":78,"hidden":1},"D009_Quantidade_Estoque":{"top":122,"left":67,"width":88,"hidden":0},"Pedido":{"top":122,"left":260,"width":72,"hidden":0},"D009_Percentual_Desconto_Tabela":{"top":122,"left":1018.0000610351562,"width":78,"hidden":1},"D009_Quantidade_Estoque_Liquido":{"top":145,"left":72,"width":88,"hidden":0},"Separado":{"top":145,"left":251,"width":72,"hidden":0},"D009_Valor_Preco_Tabela":{"top":145,"left":1036.0000610351562,"width":78,"hidden":1},"D049_Flag_Nacional_Importado":{"top":168,"left":1007.0000610351562,"width":120,"hidden":1},"D009_Valor_Custo_Tabela":{"top":191,"left":46,"width":88,"hidden":0},"D049_Flag_Substituicao_Tributaria":{"top":191,"left":975.0000610351562,"width":80,"hidden":1},"D009_Valor_Custo_Ultima_Compra":{"top":214,"left":9,"width":88,"hidden":0},"D009_ICF_1":{"top":214,"left":283,"width":72,"hidden":0},"D049_Flag_Ativo":{"top":283,"left":273,"width":80,"hidden":0},"D009_Valor_Custo_Medio_Unitario":{"top":237,"left":47,"width":88,"hidden":0},"D009_Valor_Custo_Unitario":{"top":237,"left":243,"width":72,"hidden":0},"D049_Observacao_Compra":{"top":237,"left":982.0000610351562,"height":60,"width":232,"hidden":1},"D009_IPV_1":{"top":283,"left":81,"width":88,"hidden":0},"D009_IPV_2":{"top":305.9999694824219,"left":81,"width":88,"hidden":0},"D009_ICF_2":{"top":305.9999694824219,"left":1090.0000610351562,"width":78,"hidden":1},"D009_IPV_3":{"top":328.9999694824219,"left":81,"width":88,"hidden":0},"D009_ICF_3":{"top":328.9999694824219,"left":1074.0000610351562,"width":78,"hidden":1},"D049_Codigo_Produto_Fornecedor":{"top":351.9999694824219,"left":1010.0000610351562,"width":158,"hidden":1}}',
        );
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D049';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002EditarMarca_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/editarMarca/';	
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002EditarMarca');

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_Id' => array('name' => 'D049_Id', 'label' => 'Id', 'type'=>'hidden', 'reject' => true),
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true),
					),
					'D049_Flag_Tipo' => array('name' => 'D049_Flag_Tipo', 'label' => 'Comprado', 'type'=>'select','align' => 'right','style' => 'float:right;', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'COMPRADO', 'value' => 'CP'),
							array('title' => 'PRODUZIDO', 'value' => 'PD'),
							array('title' => 'MONTADO', 'value' => 'MT'),
							array('title' => 'MODIFICADO', 'value' => 'MD'),
						),
					),
					'D049_Modelo' => array('name' => 'D049_Modelo', 'label' => 'Sub-item', 'type'=>'text'),
					'D049_Codigo_Produto_Fornecedor' => array('name' => 'D049_Codigo_Produto_Fornecedor', 'label' => 'Código Original','align' => 'right','style' => 'float:right;', 'type'=>'text', 'length' => array(26,30)),
					'D009_Valor_Preco_Tabela' => array('name' => 'D009_Valor_Preco_Tabela', 'label' => 'Preço Tabela', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15), 'reject'=>true),
					'D009_Percentual_Desconto_Tabela' => array('name' => 'D009_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'moeda', 'value' => '0,00',
							'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;', 'reject'=>true
					),
					'D009_Aliquota_ICMS_Tabela' => array('name' => 'D009_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15), 'reject'=>true),
					'D009_IPV_1' => array('name' => 'D009_IPV_1', 'label' => 'IPV 1', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPV_2' => array('name' => 'D009_IPV_2', 'label' => 'IPV 2', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),
					'D009_IPV_3' => array('name' => 'D009_IPV_3', 'label' => 'IPV 3', 'type'=>'moeda','casasDecimais' => 4, 'length' => array(10,15), 'reject' => true, 'align' => 'right', 'style'=>'float:right;'),

					'D009_ICF_1' => array('name' => 'D009_ICF_1', 'label' => 'ICF', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'reject'=>true),
					'D009_ICF_2' => array('name' => 'D009_ICF_2', 'label' => 'ICF 2', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;', 'reject'=>true),
					'D009_ICF_3' => array('name' => 'D009_ICF_3', 'label' => 'ICF 3', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'reject'=>true),
					
					'D049_Observacao_Compra' => array('name' => 'D049_Observacao_Compra', 'label' => 'Observação Compra', 'type'=>'textarea', 'length' => array(2,30)),
					'D049_Flag_Nacional_Importado' => array('name' => 'D049_Flag_Nacional_Importado', 'label' => 'Nacional/Importado', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'NACIONAL', 'value' => 'N'),
							array('title' => 'IMPORTADO', 'value' => 'I'),
						),
					),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Flag_Ativo' => array('name' => 'D049_Flag_Ativo', 'label' => 'Ativo', 'type'=>'select', 'select' => 
						array(
							array('title' => 'SIM', 'value' => 'S','selected'=>true),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Flag_Substituicao_Tributaria' => array('name' => 'D049_Flag_Substituicao_Tributaria', 'label' => 'Substituição Tributária', 'type'=>'select', 'align' => 'right','style' => 'float:right;', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N','selected'=>true),
						),
					),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
					
					'D009_Valor_Custo_Unitario' => array('name' => 'D009_Valor_Custo_Unitario', 'label' => 'Custo x ICF', 'type'=>'moeda', 'value' => '0,0000', 'reject' => true, 'length' => array(10,15), 'disabled' => true, 'casasDecimais' => 4),					
					'D009_Valor_Custo_Medio_Unitario' => array('name' => 'D009_Valor_Custo_Medio_Unitario', 'label' => '<u>Custo médio</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Valor_Custo_Compra' => array('name' => 'D009_Valor_Custo_Compra', 'label' => 'Custo', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'D009_Valor_Custo_Ultima_Compra' => array('name' => 'D009_Valor_Custo_Ultima_Compra', 'label' => '<u>Custo última compra</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D009_Valor_Custo_Tabela' => array('name' => 'D009_Valor_Custo_Tabela', 'label' => '<u>Custo tabela</u>', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D009_Flag_Preco_Tabelado' => array('name' => 'D009_Flag_Preco_Tabelado', 'label' => 'Preço tabelado', 'extra'=>@$checkedTabelado, 'type'=>'checkbox','reject' => true),
					
					'D009_Quantidade_Estoque_Liquido' => array('name' => 'D009_Quantidade_Estoque_Liquido', 'label' => 'Liquido', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'D009_Quantidade_Estoque' => array('name' => 'D009_Quantidade_Estoque', 'label' => 'Estoque', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'Pedido' => array('name' => 'Pedido', 'label' => 'Pedidos', 'type'=>'moeda', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),
					'Separado' => array('name' => 'Separado', 'label' => 'Separado', 'type'=>'texto', 'value' => '0,00', 'reject' => true, 'length' => array(10,15), 'disabled' => true),					
					'divReload' => array('name' => 'divReload', 'label'=>'', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				),
            ) // 'campos'
		);
		$form->definirFormularioArray($formArray);
		
		
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
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						$CAD002->atualizarMarcaD009($form, $D049_Id);
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}');
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}');
							} </script></div>
EOT;
					}				
				/*
				$msg = $CAD002->juntarMarcas($form->campoValorEnviado('D049_Antigo_Id'), $form->campoValorEnviado('D049_Novo_Id'));
				if ($msg === true) {
					echo gGeraAlertMsg('Operação OK', 'Processo concluído com sucesso!');
				} else {
					echo gGeraAlertMsg('Operação Falhou!', $msg);
				}*/
				
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				log("SELECT *, D009_Quantidade_Pedido(D009_Id,1) as Pedido,
											  D009_Quantidade_Separado_3(D009_Id) as Separado
				                      FROM D049 
				                 LEFT JOIN D009 ON D009_D049_Id=D049_Id
				                     WHERE D049_Id='{$D049_Id}' AND D009_C004_Id='{$g['empresaAtual']}'");
				$sql = mysql_query("SELECT *, D009_Quantidade_Pedido(D009_Id,1) as Pedido,
											  D009_Quantidade_Separado_3(D009_Id) as Separado
				                      FROM D049 
				                 LEFT JOIN D009 ON D009_D049_Id=D049_Id
				                     WHERE D049_Id='{$D049_Id}' AND D009_C004_Id='{$g['empresaAtual']}'");
				$res = mysql_fetch_array($sql);
				$form->campoSetarValor('D009_Valor_Preco_Tabela', gCorrigeNumero($res['D009_Valor_Preco_Tabela']));
				$form->campoSetarValor('D009_Percentual_Desconto_Tabela', gCorrigeNumero($res['D009_Percentual_Desconto_Tabela']));
				$form->campoSetarValor('D009_Percentual_Acrescimo_Tabela', gCorrigeNumero($res['D009_Percentual_Acrescimo_Tabela']));
				$form->campoSetarValor('D009_Aliquota_ICMS_Tabela', gCorrigeNumero($res['D009_Aliquota_ICMS_Tabela']));
				$form->campoSetarValor('D009_IPV_1', gCorrigeNumero($res['D009_IPV_1'], 4));
				$form->campoSetarValor('D009_IPV_2', gCorrigeNumero($res['D009_IPV_2'], 4));
				$form->campoSetarValor('D009_IPV_3', gCorrigeNumero($res['D009_IPV_3'], 4));
				$form->campoSetarValor('D009_ICF_1', gCorrigeNumero($res['D009_ICF_1'], 4));
				$form->campoSetarValor('D009_ICF_2', gCorrigeNumero($res['D009_ICF_2'], 4));
				$form->campoSetarValor('D009_ICF_3', gCorrigeNumero($res['D009_ICF_3'], 4));

				$form->campoSetarValor('D009_Valor_Custo_Unitario', gCorrigeNumero($res['D009_Valor_Custo_Unitario']));
				$form->campoSetarValor('D009_Valor_Custo_Medio_Unitario', gCorrigeNumero($res['D009_Valor_Custo_Medio_Unitario']));
				$form->campoSetarValor('D009_Valor_Custo_Compra', gCorrigeNumero($res['D009_Valor_Custo_Compra']));
				$form->campoSetarValor('D009_Valor_Custo_Ultima_Compra', gCorrigeNumero($res['D009_Valor_Custo_Ultima_Compra']));
				$form->campoSetarValor('D009_Valor_Custo_Tabela', gCorrigeNumero($res['D009_Valor_Custo_Tabela']));
				$form->campoSetarValor('D009_Quantidade_Estoque', gCorrigeNumero($res['D009_Quantidade_Estoque']));
				$form->campoSetarValor('D009_Quantidade_Estoque_Liquido', gCorrigeNumero($res['D009_Quantidade_Estoque_Liquido']));
				$form->campoSetarValor('D009_Preco_1', gCorrigeNumero($res['D009_Preco_1']));
				$form->campoSetarValor('D009_Preco_2', gCorrigeNumero($res['D009_Preco_2']));
				$form->campoSetarValor('D009_Preco_3', gCorrigeNumero($res['D009_Preco_3']));

				$form->campoSetarValor('Pedido', gCorrigeNumero($res['Pedido']));
				$form->campoSetarValor('Separado', gCorrigeNumero($res['Separado']));

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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		if($acaoId){
			$g['smarty']->display('formEditor.tpl');
			$g['smarty']->display('buttons-floatBottom.tpl');
		}else{
			echo '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';
		}
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '<button type="submit" id="cad002EditarMarca_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		$g['smarty']->assign('botoesId', $g['divId']);
	



$id = 'popover-' . uniqid();
		$id2 = 'popover-' . uniqid();
		$id3 = 'popover-' . uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Ultima_Compra').unbind('click').bind('click', function(data) {
					console.log($(this));
					$('.formEditor-D009_Valor_Custo_Ultima_Compra').attr('id', '{$id}');
					if ($('#{$id}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id}').popover('destroy');
								$('#{$id}').popover({
									html: true,
									placement: 'right',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id}').popover('show');
								$('#{$id}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								//var left = $('.popover').position().left - 4;
								//$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Medio_Unitario').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Medio_Unitario').attr('id', '{$id2}');
					if ($('#{$id2}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCusto/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&acaoId=' + encodeURIComponent('{$res['D009_T001_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id2}').popover('destroy');
								$('#{$id2}').popover({
									html: true,
									placement: 'right',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id2}').popover('show');
								$('#{$id2}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id2}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								//var left = $('.popover').position().left - 4;
								//$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
				
				$('.formEditor-D009_Valor_Custo_Tabela').css('cursor', 'pointer');
				$('.formEditor-D009_Valor_Custo_Tabela').unbind('click').bind('click', function(data) {
					$('.formEditor-D009_Valor_Custo_Tabela').attr('id', '{$id3}');
					if ($('#{$id3}').data('show')) {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					} else {
						$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
						$.ajax({
							url: '/cad/cad002/outro/demonstrativoCustoTabela/',
							dataType: 'html',
							data: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&D009_Id=' + encodeURIComponent('{$res['D009_Id']}'),
							type: 'post',
							success: function(response) {
								$('#{$id3}').popover('destroy');
								$('#{$id3}').popover({
									html: true,
									placement: 'right',
									title: 'Demonstrativo de Custo',
									content: response
								});
								$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
								$('#{$id3}').popover('show');
								$('#{$id3}').data('show', true);
								$('<span class=\'ui-icon ui-icon-closethick\' style=\'float: right; margin: -3px -8px;\'>close</span>').bind('click', function() {
									$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
									$('body').unbind('click');
								}).appendTo($('.popover-title'));
								$('body').unbind('mousedown').bind('mousedown', function(event) { 
									if (!$(event.target).closest('.popover').size()) {
										$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
										$('body').unbind('mousedown');
									}
								});
								$('#{$id3}').next().find('input:visible:last').focus();
								$('.popover').css('width', 320);
								//var left = $('.popover').position().left - 4;
								//$('.popover').css('left', left);
								
								var table = $('.popover').find('table');
								$(table).css('font-size', 14);
							}
						});
					}
				});
			} </script></div>
EOT;
	break;
	
	//Form Cadasatrar Marca
	case 'cadastroMarca':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
				
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
		$form_tabela = 'D049';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002cadastroMarcas';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cadastroMarca/';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
        //form editor
        $form_editor = array(
            'geral' => '{"D049_D082_Id":{"top":7,"left":69,"width":165,"hidden":0},"D049_Flag_Tipo":{"top":30,"left":86,"width":165,"hidden":0},"D049_Codigo_Produto_Fornecedor":{"top":30,"left":674.0000610351562,"width":155,"hidden":1},"D049_Modelo":{"top":53,"left":64,"width":155,"hidden":0},"D009_Percentual_Desconto_Tabela":{"top":53,"left":682.0000610351562,"width":75,"hidden":1},"D049_Origem_Mercadoria":{"top":76,"left":19,"width":304,"hidden":0},"D009_Valor_Preco_Tabela":{"top":76,"left":700.0000610351562,"width":75,"hidden":1},"D049_Data_Cadastro":{"top":99,"left":37,"width":75,"hidden":0},"D009_Aliquota_ICMS_Tabela":{"top":99,"left":704.0000610351562,"width":75,"hidden":1},"D049_Flag_Ativo":{"top":122,"left":81,"width":80,"hidden":0},"D009_ICF_1":{"top":122,"left":738.0000610351562,"width":75,"hidden":1},"D009_ICF_3":{"top":145,"left":738.0000610351562,"width":75,"hidden":1},"D009_IPV_1":{"top":168,"left":746.0000610351562,"width":75,"hidden":1},"D009_ICF_2":{"top":191,"left":738.0000610351562,"width":75,"hidden":1},"D049_Observacao_Compra":{"top":214,"left":502.00006103515625,"height":60,"width":317,"hidden":1},"D049_Flag_Nacional_Importado":{"top":282.9999694824219,"left":639.0000610351562,"width":120,"hidden":1},"D049_Flag_Substituicao_Tributaria":{"top":305.9999694824219,"left":687.0000610351562,"width":80,"hidden":1}}',
        );

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('D082', 'D082_Id', 'D082_Marca', 'order by D082_Marca', true),
					),
					'D049_Flag_Tipo' => array('name' => 'D049_Flag_Tipo', 'label' => 'Tipo', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),                       
							array('title' => 'COMPRADO', 'value' => 'CP', 'selected' => true),
							array('title' => 'PRODUZIDO', 'value' => 'PD'),
							array('title' => 'MONTADO', 'value' => 'MT'),
							array('title' => 'MODIFICADO', 'value' => 'MD'),
						),
					),
					'D049_Modelo' => array('name' => 'D049_Modelo', 'label' => 'Sub-item', 'type'=>'text', 'length' => array(26,30)),
					'D049_Codigo_Produto_Fornecedor' => array('name' => 'D049_Codigo_Produto_Fornecedor', 'label' => 'Código Original', 'type'=>'text', 'length' => array(26,30)),

					'D009_Valor_Preco_Tabela' => array('name' => 'D009_Valor_Preco_Tabela', 'label' => 'Preço Tabela', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15), 'reject'=>true),
					'D009_Percentual_Desconto_Tabela' => array('name' => 'D009_Percentual_Desconto_Tabela', 'label' => 'Desconto Tabela', 'type'=>'moeda', 'value' => '0,00','length' => array(10,15), 'align' => 'right', 'style'=>'float:right;', 'reject'=>true),
					'D009_Aliquota_ICMS_Tabela' => array('name' => 'D009_Aliquota_ICMS_Tabela', 'label' => 'ICMS Tabela', 'type'=>'moeda', 'value' => '0,00', 'length' => array(10,15), 'reject'=>true),
					'D009_IPV_1' => array('name' => 'D009_IPV_1', 'label' => 'IPV', 'type'=>'moeda','casasDecimais'	=> 4, 'value' => '0,0000', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;', 'reject'=>true),
					'D009_ICF_1' => array('name' => 'D009_ICF_1', 'label' => 'ICF 1', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'reject'=>true),
					'D009_ICF_2' => array('name' => 'D009_ICF_2', 'label' => 'ICF 2', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;', 'reject'=>true),
					'D009_ICF_3' => array('name' => 'D009_ICF_3', 'label' => 'ICF 3', 'type'=>'moeda','casasDecimais' => 4, 'value' => '0,0000', 'length' => array(10,15), 'reject'=>true),

					'D049_Observacao_Compra' => array('name' => 'D049_Observacao_Compra', 'label' => 'Observação Compra', 'type'=>'textarea', 'length' => array(3,55)),
					'D049_Flag_Nacional_Importado' => array('name' => 'D049_Flag_Nacional_Importado', 'label' => 'Nacional/Importado', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'NACIONAL', 'value' => 'N'),
							array('title' => 'IMPORTADO', 'value' => 'I', 'selected' => true),
						),
					),
					'D049_Data_Cadastro' => array('name' => 'D049_Data_Cadastro', 'label' => 'Data Cadastro', 'type'=>'data', 'length' => array(10,15), 'align' => 'right', 'style'=>'float:right;'),
					'D049_Flag_Ativo' => array('name' => 'D049_Flag_Ativo', 'label' => 'Ativo', 'type'=>'select', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S','selected'=>true),
							array('title' => 'NÃO', 'value' => 'N'),
						),
					),
					'D049_Flag_Substituicao_Tributaria' => array('name' => 'D049_Flag_Substituicao_Tributaria', 'label' => 'Substituição Tributária', 'type'=>'select','style' => 'float:right;', 'align' => 'right', 'select' => 
						array(
							array('title' => '...'),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N','selected'=>true),
						),
					),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
					'D049_D001_Id' => array('name' => 'D049_D001_Id', 'type'=>'text', 'type'=>'hidden', 'value' => $D001_Id),
					'D049_Aplicacao' => array('name' => 'D049_Aplicacao', 'type'=>'hidden'),
					'D049_Peso_Unitario_Kg' => array('name' => 'D049_Peso_Unitario_Kg', 'type'=>'hidden'),
					'divReload' => array('name' => 'divReload', 'label'=>'', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
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
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert
				$D049_D082_Id = $form->campoValorEnviado('D049_D082_Id');								
				$D049_Modelo = $form->campoValorEnviado('D049_Modelo');								
				$D049 = mysql_query("SELECT D049_D082_Id FROM D049 WHERE D049_D082_Id = '{$D049_D082_Id}' AND D049_Modelo = '{$D049_Modelo}' AND D049_D001_Id = '{$D001_Id}'");
				if(mysql_num_rows($D049) > 0){
					echo gGeraAlertMsg('Erro', 'Este sub-item ja está cadastrada!');
				} else {
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						
						$return = $CAD002->cadastroMarca($form);
						
						$form->campoValorEnviado('T014_Id');
						
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//divRefresh('{$form->campoValorEnviado('divReload')}', true);
									divRefresh('{$g['divIdParent']}', true);
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$gerado = gGeraCamposForm($form, $tab, $D001_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '<button type="submit" id="cad002cadastroMarcas">Cadastrar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	/**
	 * TAB certificados
	 */
	case 'certificados':
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
		$form_tabela = 'T140';
		$form_campo_pk = 'T140_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/certificados/';
        
        //form editor
        $form_editor = array(
			'geral' => '{"uploadArquivo":{"top":6.98529052734375,"left":53.30882406234741,"width":360.039216,"hidden":0},"content_form2_btn":{"top":27.987136840820312,"left":511.9944624900818,"width":null,"hidden":0},"T140_D024_Id":{"top":29.993881225585938,"left":683.9920210838318,"width":359.039216,"hidden":1},"T140_Link":{"top":52.98713684082031,"left":30.346200466156006,"width":360.039216,"hidden":0},"T140_Data_Emissao":{"top":52.98713684082031,"left":680.9895796775818,"width":65.03921600000001,"hidden":1},"T140_Data_Validade":{"top":75.99571228027344,"left":679.9785323143005,"width":65.03921600000001,"hidden":1},"T140_Tipo_Certificado":{"top":98.97364807128906,"left":718.9797530174255,"width":375.039216,"hidden":1},"T140_Certificadora":{"top":121.98222351074219,"left":685.9987959861755,"width":375.039216,"hidden":1}}',
        );
        
		// Array de definição do formulário
		$r_D001_Id = isset($r_form_cad002_form1) ? $r_form_cad002_form1['T140_D001_Id'] : $r_D001_Id;
		$r_D024_Id = isset($r_form_cad002_form1) ? $r_form_cad002_form1['T140_D024_Id'] : $r_D024_Id;
		
		$tmp = gGeraSelect('D001', 'D001_Id', 'D001_Descricao_Produto', "WHERE D001_Id = $r_D001_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['T140_D024_Id'] 			= array('name' => 'T140_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa'=>array('D024','D024_Id','D024_Nome_Empresa',""),'length' => array(70,60));
		//$formArray['campos']['geral']['__T140_D001_Id'] 		= array('name' => '__T140_D001_Id', 'label'=>'Produto', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
			//$tmp
		//);
		$formArray['campos']['geral']['T140_D001_Id'] 			= array('name' => 'T140_D001_Id', 'type' => 'hidden', 'value' => $r_D001_Id);
		$formArray['campos']['geral']['T140_Data_Emissao'] 		= array('name' => 'T140_Data_Emissao', 'label'=>'Data Emissão', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['T140_Data_Validade'] 	= array('name' => 'T140_Data_Validade', 'label'=>'Data Validade', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['T140_Tipo_Certificado'] 	= array('name' => 'T140_Tipo_Certificado', 'label'=>'Tipo', 'type' => 'text','length' => array(70,70));
		$formArray['campos']['geral']['T140_Certificadora'] 	= array('name' => 'T140_Certificadora', 'label'=>'Observação', 'type' => 'text','length' => array(70,70));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['T140_Nome_Arquivo']      = array('name' => 'T140_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		$formArray['campos']['geral']['T140_Link']              = array('name' => 'T140_link', 'type' => 'text', 'uppercase' => false, 'label' => 'Link Arquivo');
		$formArray['campos']['geral']['content_form2_btn'] 		= array('name' => 'content_form2_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir  ', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:0px;margin-top:0px;padding-left:10px;padding-right:0px;padding-top:0px;height:20px;"', 'reject'=>true);
        $formArray['campos']['geral']['divReload']              = array('name' => 'divReload', 'type' => 'hidden', 'value' => $r_idTabForm4,'reject' => true);
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
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('T140_Nome_Arquivo', $arquivo['name'], true);
                        $divReload = $form->campoValorEnviado('divReload');
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/certificados";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
							if (file_exists($file)) { unlink($file); }
							move_uploaded_file($arquivo['tmp_name'], $file);
							// Imprime uma mensagem e fecha a janela
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
                                    divRefresh('{$divReload}', true);
								} </script></div>
EOT;
						}
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
                    $divReload = $form->campoValorEnviado('divReload');
					$T140_Link = $form->campoValorEnviado('T140_Link');

					if(strLen($arquivo['name']) > 100){
						// tamanho do campo e varchar 100, se for maior corta a extesao e não consegue abrir depois
						echo gGeraAlertMsg('Erro', 'Nome do arquivo deve conter até 100 caracteres, favor verificar');
					}elseif(!empty($arquivo['name']) && !empty($T140_Link)){
						echo gGeraAlertMsg('Erro', "Deve ser enviado uma <b>Foto</b> ou um <b>Link</b>.");
					} else {
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							mysql_query("SET AUTOCOMMIT=0");
							mysql_query("START TRANSACTION");
							// Neste caso, guardamos o nome do arquivo no banco
							$form->campoSetarValor('T140_Nome_Arquivo', $arquivo['name'], true);
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								$T140_Id = $g['mysqlLastId'];
								$D001_Id = $form->campoValorEnviado('T140_D001_Id');
								mysql_query("INSERT INTO T303 (T303_T140_Id,T303_D001_Id) VALUES ('{$T140_Id}','{$D001_Id}')");
	
								// Gravar o arquivo no disco
								if(empty($T140_Link)){
									$pathinfo = pathinfo($arquivo['name']);
									$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
									$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/certificados";
									is_dir($dir) or mkdir($dir, 0777, true);
									$file = $dir . '/' . $T140_Id . '.' . $pathinfo['extension'];
									$retornoMoveFile = move_uploaded_file($arquivo['tmp_name'], $file);
								}

								if (!$retornoMoveFile && empty($T140_Link)){
									mysql_query("ROLLBACK");
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											dialogConfirm('ERRO', 'Não foi possível realizar o upload, selecione o arquivo e tente novamente');
											if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
											}
										} </script></div>
	EOT;
								}else{
									// Imprime uma mensagem e fecha a janela
										echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
												divRefresh("{$divReload}", true);
											}
										} </script></div>
	EOT;
	
								}
							}
							mysql_query("COMMIT");
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
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	 case 'substituicao':
		//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA!
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormSubsBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/substituicao/';
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'D017_Codigo_Antigo' => array('name' => 'D017_Codigo_Antigo', 'label'=>'Incluir', 'type' => 'text','length' => array(35,80)),
					'cad002FormSubsBtn' => array('name' => 'cad002FormSubsBtn', 'type'=>'submit','reload'=>false,'value'=>'Incluir', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					'D001_Id' => array('name' => 'D001_Id', 'value'=>$D001_Id, 'label'=>'Incluir', 'type' => 'hidden','length' => array(40,40)),
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
				$msg = $CAD002->gravarSubstituicao($form);
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
		
		
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		 
		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($gridId, '/cad/cad002/grid/substituicao/', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		echo  gProcessaAcoes($actions);
		
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
	
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'cad002FormCadastroEmpresa':
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
		$form_tabela = 'D083';
		$form_campo_pk = 'D083_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormCadastroEmpresa';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormCadastroEmpresa/';
		//
		$form_editor = array(
			'geral' => '{"D083_D049_Id":{"top":7,"left":67.984375,"width":191,"hidden":0},"D083_D024_Id":{"top":30,"left":42.296875,"width":474,"hidden":0},"D083_Codigo_Produto_Fornecedor":{"top":53,"left":29.4375,"width":184,"hidden":0},"D083_Codigo_Barras":{"top":76,"left":12.9375,"width":184,"hidden":0},"D083_Data_Cadastro":{"top":99,"left":34.953125,"width":88,"hidden":0}}'
		);

		//$data = date('Y-m-d');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D083_D049_Id' => array('name' => 'D083_D049_Id', 'label' => 'Marca', 'type' => 'select','select' =>
						gGeraSelect('D049','D049_Id','D082_Marca', 'left join D082 on D082_Id=D049_D082_Id where D049_D001_Id='.@$r_D001_Id,true),
					),
					'D083_D024_Id' => array('name' => 'D083_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa','pesquisa' =>
						array('D024','D024_Id','D024_Nome_Empresa', "where D024_Flag_Ativo='S' order by D024_Nome_Empresa"), 'length' => array(50,30),
					),
					'D083_Codigo_Produto_Fornecedor' => array('name' => 'D083_Codigo_Produto_Fornecedor', 'label'=>'Código Produto', 'type' => 'text', 'length' => array(30,80)),
					'D083_Data_Cadastro' => array('name' => 'D083_Data_Cadastro', 'label'=>'Data Cadastro', 'type' => 'data', 'length' => array(10,15)),
					'D083_D001_Id' => array('name' => 'D083_D001_Id', 'label'=>'Código Produto', 'type' => 'hidden', 'value' => @$r_D001_Id),
					'D083_Codigo_Barras' => array('name' => 'D083_Codigo_Barras', 'label'=>'Código de Barras', 'type' => 'text', 'length' => array(30,80)),
				)
			)
		);

		// Quando abrir via nota recebida, fixa o fornecedor recebido (D024_Id)
		if (!empty($r_D024_Id)) {
			$formArray['campos']['geral']['D083_D024_Id']['value'] = $r_D024_Id;
			$formArray['campos']['geral']['D083_D024_Id']['disabled'] = true;
			$formArray['campos']['geral']['D083_D024_Id']['readonly'] = true;
		}
        $form = new Formulario('form-abc001FormNome');
		$form->definirFormularioArray($formArray);
		
		if (!empty($r_D024_Id)) {
			$sqlFornecedor = mysql_query("SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='" . addslashes($r_D024_Id) . "'");
			if ($sqlFornecedor && mysql_num_rows($sqlFornecedor) > 0) {
				$form->formArray['campos']['geral']['D083_D024_Id']['value_Pesquisa'] = mysql_result($sqlFornecedor, 0, 0);
			}
		}

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
								divRefresh('{$g['divIdParent']}', true);
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
		$botoesDir .= '<button type="submit" id="cad002FormCadastroEmpresa">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'cad002FormCadastroReferencia':
		
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
		$form_tabela = 'D083';
		$form_campo_pk = 'D083_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormCadastroReferencia';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormCadastroReferencia/';
		//
		$form_editor = array(
			'geral' => '{"D083_D001_Id":{"top":7,"left":67.75,"width":473,"hidden":0},"D083_D024_Id":{"top":30,"left":43.5,"width":474,"hidden":0},"D083_Codigo_Produto_Fornecedor":{"top":53,"left":30.265625,"width":184,"hidden":0},"D083_Codigo_Barras":{"top":76,"left":15.203125,"width":184,"hidden":0},"D083_Data_Cadastro":{"top":99,"left":36.625,"width":184,"hidden":0}}'
		);

		//$data = date('Y-m-d');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
/* 					'D083_D049_Id' => array('name' => 'D083_D049_Id', 'label' => 'Marca', 'type' => 'select','select' =>
						gGeraSelect('D049','D049_Id','D082_Marca', 'left join D082 on D082_Id=D049_D082_Id where D049_D001_Id='."'".@$r_D001_Id."'",true),
					), */
					'D083_D024_Id' => array('name' => 'D083_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa','pesquisa' =>
						array('D024','D024_Id','D024_Nome_Empresa', "where D024_Flag_Ativo='S' order by D024_Nome_Empresa"), 'length' => array(50,30),
					),
					'D083_Codigo_Produto_Fornecedor' => array('name' => 'D083_Codigo_Produto_Fornecedor', 'label'=>'Código Produto', 'type' => 'text', 'length' => array(30,80)),
					'D083_Data_Cadastro' => array('name' => 'D083_Data_Cadastro', 'label'=>'Data Cadastro', 'type' => 'data', 'length' => array(10,15)),
					'D083_D001_Id' => array('name' => 'Consulta_D083_D001_Id', 'label'=>'Produto', 'type' => 'pesquisa','pesquisa'=>array('D001','D001_Id','D001_Codigo_Produto,D001_Descricao_Produto'), 'length' => array(65,30)),
					'D083_Codigo_Barras' => array('name' => 'D083_Codigo_Barras', 'label'=>'Código de Barras', 'type' => 'text', 'length' => array(30,80)),
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
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$D001_Id = $form->campoValorEnviado('D083_D001_Id');
						$D083_Id = $g['mysqlLastId'];

						$D001 = mysqli_query("SELECT D049_Id
										        FROM D049 
								           LEFT JOIN D001 ON D001_Id = D049_D001_Id 
									           WHERE D001_Id = '{$D001_Id}'");
						
						$mD001 = mysql_fetch_array($D001);

						mysqli_query("UPDATE D083 SET D083_D049_Id = '{$mD001['D049_Id']}'
										        WHERE D083_Id = '{$D083_Id}'");

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
		$botoesDir .= '<button type="submit" id="cad002FormCadastroReferencia">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'cad002FormMontagem':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D009';
		$form_campo_pk = 'D049_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormSubsBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormMontagem/';
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		
		/*
		$pesquisaGridExtra = "left join D049 on D049_Id=D009_D049_Id left join D001 on D001_Id=D049_D001_Id left join D082 on D082_Id=D049_D082_Id";
		$pesquisaGridExtra .= " where D009_C004_Id='{$g['empresaAtual']}'AND D001_Flag_Ativo='S'";
		*/
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

		// D049_Id
		$r_acaoId = mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id = '{$r_acaoId}'");
		$r_acaoId = mysql_fetch_assoc($r_acaoId);
		$D049_Id = $r_acaoId['D009_D049_Id'];

		/*		
		$pesquisaGrid = array(
			'D009',																								// tabela
			'D049_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca',						// campos
			$pesquisaGridExtra,																					// extras sql
			'Consulta Rápida de Produtos',																		// título da janela
			'Referência,Descrição,Descrição Inglês,Marca',														// descrição dos campos
			'cad002CampoProcuraProduto',																		// callback js
			$g['divId'],	
			$D049_Id																							// divId
		);
		*/
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D049_Id',																										      // chave primaria
			'D049_Id||D009_Id||C008_Id||D060_Id||D001_Codigo_Produto||D049_Modelo||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque_Real||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||"" as retornaPrecoVenda||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T003_Id . '" as T003_Id||D001_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Id,Dep,Setor,Código,Sub-item,Descrição,Referência,Marca,UN,Preço,Preço 2,Preço 3,Estoque Real,Estoque Líq,Est. Loja,Estoque Locação,Estoque Fora,Preço,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,D001_Id',	  // descrição dos campos
			'cad002CampoProcuraProduto',																					              // callback js
			$g['divId'],																									              // divId
			$D049_Id,   																					  // parametro extra
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
			'D049_Id||D009_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque_Liquido||T066_Quantidade_Estoque||D009_Quantidade_OC||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Real||C008_Id||D060_Id||D144_Id||D001_Id||D004_Local' // Campos que são ignorados na cláusula where do pesquisaGrid
		);
		
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'Procura_Produto_Montagem' => array('name' => 'Procura_Produto_Montagem' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(75,50), 'reject' => true),
					
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
				$msg = $CAD002->gravarSubstituicao($form);
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
	
	case 'cad002FormCadastroLocacao':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
		$D049_Id = (isset($r_D049_Id)) ? $r_D049_Id : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		
		//echo "D001: ".$D001_Id."<br />D009_Id: ".$D049_Id."<br />acao: ".$acaoId;
		
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
		$form_tabela = 'T066';
		$form_campo_pk = 'T066_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormCadastroLocacao_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormCadastroLocacao/';
        
        //form editor
        $form_editor = array(
			'geral' => '{"T066_D004_Id":{"top":7,"left":8.53125,"width":329,"hidden":0},"cad002FormLocacaoBtnNew":{"top":5,"left":416,"width":null,"hidden":0},"T066_Minimo":{"top":30,"left":5.625,"width":152,"hidden":0},"T066_Maximo":{"top":30,"left":226.984375,"width":120,"hidden":0},"T066A_Data_Validade":{"top":53,"left":0.3125,"width":77,"hidden":0},"T066_Agrupamento":{"top":53,"left":440.015625,"width":88,"hidden":1}}',
        );
		
		if ($D049_Id) {
			$D009=mysql_query("select D009_Id from D009 where D009_D049_Id='$D049_Id' and D009_C004_Id='$Empresa_Atual'");
        	$mD009=mysql_fetch_array($D009);
        	$D009_Id = $mD009['D009_Id'];
        }
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormCadastroLocacao');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'T066_D048_Id' => array('name' => 'T066_D048_Id', 'label'=>'IdEstoque', 'type' => 'hidden', 'value' => '00', 'length' => array(35,80)),
					//'T004_C004_Id' => array('name' => 'T004_C004_Id', 'label'=>'IdEmpresaAtual', 'type' => 'hidden', 'value' => $g['empresaAtual'], 'length' => array(35,80)),
					'T066_Quantidade_Estoque' => array('name' => 'T066_Quantidade_Estoque', 'value'=>'0.00', 'label'=>'QtdEstoque', 'type' => 'hidden','length' => array(40,40)),
					'T066_D009_Id' => array('name' => 'T066_D009_Id', 'value'=>$D009_Id, 'label'=>'D009_Id', 'type' => 'hidden','length' => array(40,40)),
					'T066_D004_Id' => array('name' => 'T066_D004_Id', 'label'=>'Local', 'type' => 'pesquisa', 'pesquisa' => array('D004', 'D004_Id', 'D004_Local,D146_Descricao,IF(D004_Flag_Tipo=1[[virgula]]"(1)"[[virgula]]"(1+)")', 'LEFT JOIN D146 ON D146_Id=D004_D146_Id where D004_C004_Id=\''.$g['empresaAtual'].'\' order by D004_Local'),  'length' => array(40,40)),
					'T066_Minimo' => array('name' => 'T066_Minimo', 'label'=>'Mínimo', 'type'=>'text', 'length' => array(12,12)),
					'T066_Maximo' => array('name' => 'T066_Maximo', 'label'=>'Maximo', 'type'=>'text', 'length' => array(12,12), 'align'=>'right', 'style'=>'float:right'),
					'T066_Agrupamento' => array('name' => 'T066_Agrupamento', 'label'=>'Agrupamento', 'type'=>'text', 'length' => array(12,12)),
					'T066A_Data_Validade' => array('name' => 'T066A_Data_Validade', 'label'=> 'Validade', 'type'=>'data', 'length' => array(10,15)),
					'cad002FormLocacaoBtnNew' => array('name' => 'cad002FormLocacaoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                ),
				
            ) // 'campos'
		);
		
		
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT D009_D004_Id FROM D009 WHERE D009_Id = '{$r_D009_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002FormLocacaoBtnNew').unbind('click').bind('click', function(data) {
					//var id = '{$row['D009_D004_Id']}';
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Local Locação' + id, '/cad/cad050/content/cad050contentCadastroLocacaoLocal/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D004', [400,300]);
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Locações', '/cad/cad019/content/cad019ContentCadastroLocacoes/', '&tab=geral' + '&noRefresh=true', [600,300]);
					return false;
				});
		    } </script></div>
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
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
						// Insert ou Update?
						if (!empty($acaoId)) {
							// Update
							$retorno = $CAD002->verificarLocal($form);
							if($retorno === true){
								if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
									// Imprime uma mensagem e fecha a janela
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
							}else{
								echo gGeraAlertMsg('Erro', 'Esta locação pode conter apenas um produto');
							}
					} else {
						// Insert
						// Imprime uma mensagem e fecha a janela
							$retorno = $CAD002->verificarLocal($form);
							if($retorno === true){
								if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
									$T066_Id = $g['mysqlLastId'];
									if (isset($r_T014_Id)) {
										// Foi incluido a partir do item da nota fiscal de entrada
										$sql = "UPDATE T014 SET T014_T066_Id = '{$T066_Id}' WHERE T014_Id = '{$r_T014_Id}'";
										mysql_query($sql);
										$alert .= gGeraAlertMsg('Operação OK', 'Uma nova locação foi cadastrada para este produto.', false);
										echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
													}, 150);
			  									});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
												divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T014_Id}', 'T014_Id');
											} </script></div>
EOT;
									} else if (isset($r_T008_Id)) {
										// Foi incluido a partir do item da nota fiscal de entrada
										$sql = "UPDATE T008 SET T008_T066_Id = '{$T066_Id}' WHERE T008_Id = '{$r_T008_Id}'";
										mysql_query($sql);
										$alert .= gGeraAlertMsg('Operação OK', 'Uma nova locação foi cadastrada para este produto.', false);
										echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														{$alert}
													}, 150);
			  									});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
												divRefresh('{$g['divIdParent']}', true, false, false, '{$r_T008_Id}', 'T008_Id');
											} </script></div>
EOT;
									} else {
										$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
										echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
													{$alert}
													divRefresh('{$g['divIdParent']}', true);
													}, 150);
			  									});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
												divRefresh('{$g['divIdParent']}', true);
											} </script></div>
EOT;
									}
								}
							}else{
								echo gGeraAlertMsg('Erro', 'Esta locação pode conter apenas um produto');
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormCadastroLocacao_Btn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
		
	break;
	
	case 'cad002FormEditarHistorico':
		//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA!
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		$D009_Id = isset($r_D009_Id) ?  $r_D009_Id: null;
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
		$form_tabela = 'T001';
		$form_campo_pk = 'T001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormEditarHistorico_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormEditarHistorico/';
		
		$sql = <<<EOT
		SELECT
			D001_Id,
			D009_Id
		FROM  D009
		LEFT JOIN D049 ON D009_D049_Id=D049_Id 
		LEFT JOIN D001 ON D001_Id=D049_D001_Id
		WHERE
			D009_Id = '{$D009_Id}'
	EOT;
		

		$rowD001 = mysql_fetch_assoc(mysql_query($sql));
		$D001_Id = $rowD001['D001_Id'];
		$D009_Id = $rowD001['D009_Id'];
				
		$form_editor = array(
			'geral' => '{"T001_D024_Id":{"top":7,"left":49,"width":266,"hidden":0},"T001_Valor_Frete":{"top":99,"left":516,"width":88,"hidden":0},"T001_D024_Id_Cliente":{"top":30,"left":73,"width":266,"hidden":0},"T001_Valor_Seguro":{"top":122,"left":507,"width":88,"hidden":0},"T001_Data_Lancamento":{"top":7,"left":477,"width":86,"hidden":0},"T001_Valor_Despesas_Acessorias":{"top":145,"left":462,"width":88,"hidden":0},"T001_Descricao_Operacao":{"top":53,"left":49,"width":280,"hidden":0},"T001_Valor_Custo_Unitario":{"top":99,"left":77,"width":88,"hidden":0},"T001_Aliquota_ICMS":{"top":168,"left":357,"width":56,"hidden":0},"T001_Valor_ICMS":{"top":168,"left":515,"width":88,"hidden":0},"T001_Valor_Custo_Medio_Unitario":{"top":122,"left":77,"width":88,"hidden":0},"T001_Aliquota_IPI":{"top":191,"left":371,"width":56,"hidden":0},"T001_Valor_IPI":{"top":191,"left":529,"width":88,"hidden":0},"T001_Valor_Preco_Unitario":{"top":145,"left":77,"width":88,"hidden":0},"T001_Percentual_ST":{"top":214,"left":383,"width":56,"hidden":0},"T001_Valor_ST":{"top":214,"left":528,"width":88,"hidden":0},"T001_Quantidade":{"top":168,"left":43,"width":88,"hidden":0},"T001_Aliquota_PIS":{"top":237,"left":367,"width":56,"hidden":0},"T001_Valor_PIS":{"top":237,"left":525,"width":88,"hidden":0},"T001_Flag_Operacao":{"top":191,"left":50,"width":115,"hidden":0},"T001_Aliquota_COFINS":{"top":260,"left":342,"width":56,"hidden":0},"T001_Valor_COFINS":{"top":260,"left":500,"width":88,"hidden":0},"T001_Flag_MMF":{"top":214,"left":81,"width":64,"hidden":0},"T001_Flag_Atualiza_Custo":{"top":237,"left":38,"width":64,"hidden":0},"T001_D009_Id":{"top":260,"left":63,"width":63,"hidden":0},"T001_D006_Id":{"top":76,"left":75,"width":287,"hidden":0},"T001_T066_Id":{"top":30,"left":480,"width":143,"hidden":0}}'
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormEditarHistorico');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'T001_D024_Id' => array('name' => 'T001_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa','length' => array(40,60),
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id',""),
					),
					'T001_D024_Id_Cliente' => array('name' => 'T001_D024_Id_Cliente', 'label'=>'Cliente', 'type' => 'pesquisa','length' => array(40,60),
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id',""),
					),
					'T001_Data_Lancamento' => array('name' => 'T001_Data_Lancamento', 'label'=>'Data Lançamento', 'type' => 'data','length' => array(10,10)),
					'T001_Descricao_Operacao' => array('name' => 'T001_Descricao_Operacao', 'label'=>'Descrição', 'type' => 'text','length' => array(40,60)),
					'T001_Valor_Custo_Unitario' => array('name' => 'T001_Valor_Custo_Unitario', 'label'=>'Custo', 'type' => 'moeda','length' => array(10,10),'casasDecimais' => 5),
					'T001_Valor_Custo_Medio_Unitario' => array('name' => 'T001_Valor_Custo_Medio_Unitario', 'label'=>'Medio', 'type' => 'moeda','length' => array(10,10),'casasDecimais' => 5),
					'T001_Valor_Preco_Unitario' => array('name' => 'T001_Valor_Preco_Unitario', 'label'=>'Preço', 'type' => 'moeda','length' => array(10,10),'casasDecimais' => 5),
					'T001_Quantidade' => array('name' => 'T001_Quantidade', 'label'=>'Quantidade', 'type' => 'moeda','length' => array(10,10)),
					'T001_Flag_Operacao' => array('name' => 'T001_Flag_Operacao', 'label'=>'Operação', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '','select' => true),
							array('title' => 'ENTRADA', 'value' => 'E'),
							array('title' => 'SAÍDA', 'value' => 'S'),
							array('title' => 'BALANÇO', 'value' => 'B'),
							array('title' => 'CANCELADO', 'value' => 'C'),
							array('title' => 'NÃO ALTERA', 'value' => 'N')
						)
					),
					'T001_Flag_MMF' => array('name' => 'T001_Flag_MMF', 'label'=>'MMF', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '','select' => true),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T001_Flag_Atualiza_Custo' => array('name' => 'T001_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '','select' => true),
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					//'T001_D009_Id' => array('name' => 'T001_D009_Id', 'value' => $r_D009_Id),
					'T001_D009_Id' => array('name' => 'T001_D009_Id', 'label' => 'Sub-item', 'type' => 'select', 'select' => 
						gGeraSelect('D049', 'D009_Id', 'D049_Modelo', "LEFT JOIN D009 ON D009_D049_Id=D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D001_Id = '{$D001_Id}'")
					),
					'T001_T066_Id' => array('name' => 'T001_T066_Id', 'label' => 'Locação', 'type' => 'select', 'select' => 
						gGeraSelect('T066', 'T066_Id', 'T066_Id,D004_Local', "LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$D009_Id}'", true, false, '%2 (%1)')
					),
					'T001_D004_Id' => array('name' => 'T001_D004_Id', 'label' => 'T001_D004_Id', 'type' => 'hidden'),

					'T001_Aliquota_ICMS' => array('name' => 'T001_Aliquota_ICMS', 'label'=>'Alíquota ICMS', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_ICMS' => array('name' => 'T001_Valor_ICMS', 'label'=>'Valor ICMS', 'type' => 'moeda', 'casasDecimais' => 5),

					'T001_Percentual_ST' => array('name' => 'T001_Percentual_ST', 'label'=>'Perc. ST', 'type' => 'moeda', 'casasDecimais' => 5),
					'T001_Valor_ST' => array('name' => 'T001_Valor_ST', 'label'=>'Valor ST', 'type' => 'moeda', 'casasDecimais' => 5),

					'T001_Aliquota_IPI' => array('name' => 'T001_Aliquota_IPI', 'label'=>'Alíquota IPI', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_IPI' => array('name' => 'T001_Valor_IPI', 'label'=>'Valor IPI', 'type' => 'moeda', 'casasDecimais' => 5),
					
					'T001_Aliquota_PIS' => array('name' => 'T001_Aliquota_PIS', 'label'=>'Alíquota PIS', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_PIS' => array('name' => 'T001_Valor_PIS', 'label'=>'Valor PIS', 'type' => 'moeda', 'casasDecimais' => 5),

					'T001_Aliquota_COFINS' => array('name' => 'T001_Aliquota_COFINS', 'label'=>'Alíquota COFINS', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_COFINS' => array('name' => 'T001_Valor_COFINS', 'label'=>'Valor COFINS', 'type' => 'moeda', 'casasDecimais' => 5),

					'T001_Valor_Frete' => array('name' => 'T001_Valor_Frete', 'label'=>'Valor Frete', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_Seguro' => array('name' => 'T001_Valor_Seguro', 'label'=>'Valor Seguro', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_Valor_Despesas_Acessorias' => array('name' => 'T001_Valor_Despesas_Acessorias', 'label'=>'Despesas Acessórias', 'type' => 'moeda', 'casasDecimais' => 2),
					'T001_D006_Id' => array('name' => 'T001_D006_Id', 'label' => 'CFOP', 'restricao' => false, 'type' => 'select', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Id,D006_Codigo_CFOP, D006_Descricao', "ORDER BY D006_Codigo_CFOP", true, false)
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
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
						
						$form->campoSetarValor('T001_D004_Id', getD004($form->campoValorEnviado('T001_T066_Id')), true);
						$D004_Id = $form->campoValorEnviado('T001_D004_Id');
						$operacao = $form->campoValorEnviado('T001_Flag_Operacao');
						$cliente = $form->campoValorEnviado('T001_D024_Id_Cliente');
						$fornecedor = $form->campoValorEnviado('T001_D024_Id');

						$sqlD004 = mysql_query("select D004_Indicador_Propriedade from D004 where D004_Id = {$D004_Id}");
						$mD004 = mysql_fetch_array($sqlD004);


						if(($mD004['D004_Indicador_Propriedade'] == '1' || $mD004['D004_Indicador_Propriedade'] == '2')) {									
								if($operacao == 'E' || $operacao == 'S' || $operacao == 'B') {
									if($operacao == 'E' && empty($fornecedor)) {																		
										echo gGeraAlertMsg('Erro no campo do formulário', 'Necessário informar o <b>Fornecedor</b> quando for uma operação de ENTRADA');
										echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
											} </script></div>
										EOT;								
										return;
									} else if($operacao == 'S' && empty($cliente)) {																		
										echo gGeraAlertMsg('Erro no campo do formulário', 'Necessário informar o <b>Cliente</b> quando for uma operação de SAÍDA');
										echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
											} </script></div>
										EOT;								
										return;
									}

									echo <<<EOT
											<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
												divRefresh("{$g['divId']}", true);
											} </script></div>
									EOT;																	
								} else {
									echo gGeraAlertMsg('Erro no campo do formulário', 'Para locações com indicador de propriedade 1 ou 2, informar apenas operações de ENTRADA, BALANÇO e SAÍDA!');
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
										} </script></div>
									EOT;								
									return;
								}
						} 																					
												
						// Insert ou Update?
						if (!empty($acaoId)) {
							// Update
							// $form->campoSetarValor('T001_D004_Id', getD004($form->campoValorEnviado('T001_T066_Id')), true);
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
								/*require_once("bibliotecas/classes/CAD002.php");
								$CAD002 = new CAD002();
							    $CAD002->D001_reprocessa_historico(0,$D009_Id);*/
								// Imprime uma mensagem e fecha a janela
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
							require_once("bibliotecas/classes/CAD002.php");
							$CAD002 = new CAD002();
							$CAD002->D001_reprocessa_historico(false,$D009_Id);							
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', "Histórico reprocessado com sucesso!<br>Registro Incluido com Sucesso!", false);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		/**
		* Botões estão no content
		*/
		
	break;
	
	case 'cad002FormUploadFotoProduto':
				/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
		
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
		$form_tabela = 'T172';
		$form_campo_pk = 'T172_Id';
                
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad002FormUploadFotoProdutoBtnAdd', 'cad002FormUploadFotoProdutoBtnCln', 'cad002FormUploadFotoProdutoBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormUploadFotoProduto/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"uploadArquivoFotoProd":{"top":7,"left":12.578125,"width":327,"hidden":0},"T172_Link_Video":{"top":53,"left":11.390625,"width":329,"hidden":0},"cad002FormUploadFotoProdutoBtnAdd":{"top":28,"left":480,"width":null,"hidden":0}}'
        );
        
		// Array de definição do formulário
		//$r_D024_Id = isset($r_cad001FormDocumentos) ? $r_cad001FormDocumentos['D087_D024_Id'] : $r_D024_Id;
		
		// $tmp = gGeraSelect('D024', 'D024_Id', 'D024_Nome_Fantasia,D024_Nome_Empresa', " where D024_Id = $r_D024_Id");
		// foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad001FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		// $formArray['campos']['geral']['__D087_D024_Id'] 		= array('name' => '__D087_D024_Id', 'label'=>'Cliente', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
			// $tmp
		// );
		$formArray['campos']['geral']['T172_D001_Id'] 			= array('name' => 'T172_D001_Id', 'type' => 'hidden', 'value' => $D001_Id);
		$formArray['campos']['geral']['uploadArquivoFotoProd']	= array('name' => 'uploadArquivo', 'label' => 'Arquivo Foto', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T172_Nome_Arquivo'] 		= array('name' => 'T172_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		$formArray['campos']['geral']['T172_Integrado_Tray'] 	= array('name' => 'T172_Integrado_Tray', 'type' => 'hidden', 'value' => 'N');
		$formArray['campos']['geral']['T172_Link_Video'] 	    = array('name' => 'T172_Link_Video', 'type' => 'text', 'label' => 'Link Youtube', 'uppercase' => false);

		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['cad002FormUploadFotoProdutoBtnNew'] = array('name' => 'cad002FormUploadFotoProdutoBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		//$formArray['campos']['geral']['cad001FormDocumentosBtnCln'] = array('name' => 'cad001FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad002FormUploadFotoProdutoBtnAdd'] = array('name' => 'cad002FormUploadFotoProdutoBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
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
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo     = $form->campoValorEnviado('uploadArquivoFotoProd');
					$linkYoutube = $form->campoValorEnviado('T172_Link_Video');
					$pathinfo    = pathinfo($arquivo['name']);
					$formatos    = array('bmp', 'jpg', 'jpeg', 'png', 'gif');

					if(!empty($arquivo['name']) && !empty($linkYoutube)){
						echo gGeraAlertMsg('Erro', "Deve ser enviado uma <b>Foto</b> ou um <b>Link Youtube</b>.");
					}elseif(!empty($arquivo['name'])  && (empty($pathinfo['extension']) || !in_array($pathinfo['extension'], $formatos))){
						$formatos = implode(", ", $formatos);
						echo gGeraAlertMsg('Erro', "Formado de arquivo não suportado, verifique se o arquivo selecionado é um dos formatos a seguir: {$formatos}.");
					} else if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('T172_Integrado_Tray', 'N', true);
						$form->campoSetarValor('T172_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//produtos/idProduto/fotos/idFoto
							$dir = "{$g['pathDados']}produtos/".$form->campoValorEnviado('T172_D001_Id')."/fotos/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							$move = move_uploaded_file($arquivo['tmp_name'], $file);
							//Gerar thumbnail 100x100
							gGerarThumb($file, "{$g['pathDados']}produtos/".$form->campoValorEnviado('T172_D001_Id')."/fotos/".$g['mysqlLastId']."-100.".$pathinfo['extension'], 100);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
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
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $D001_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
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
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
	// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'cad002FormPreCadastro':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T189_Descricao_Produto = (isset($r_T189_Descricao_Produto)) ? $r_T189_Descricao_Produto : '';
		$T189_Descricao_Produto = (isset($r_descricaoProduto)) ? $r_descricaoProduto : '';
		$D069_Id = (isset($r_D069_Id)) ? $r_D069_Id : '';
		
		if(empty($r_D005_Id) && !empty($r_NCM)){
			$sql = mysql_query("SELECT D005_Id FROM D005 WHERE (replace(replace(replace(D005_Classificacao_Fiscal,'.',''),'-',''),' ','')) = '{$r_NCM}'");
			if(mysql_num_rows($sql) > 0){
				$res = mysql_fetch_array($sql);
				$r_D005_Id = $res['D005_Id'];
			}
		}

		if(empty($r_D037_Id) && !empty($r_Unidade)){
			$sql = mysql_query("SELECT D037_Id FROM D037 WHERE D037_Unidade = '{$r_Unidade}'");
			if(mysql_num_rows($sql) > 0){
				$res = mysql_fetch_array($sql);
				$r_D037_Id = $res['D037_Id'];
			}
		}

		if(!empty($r_Marca)){
			$sql = mysql_query("SELECT D082_Id FROM D082 WHERE D082_Marca = '{$r_Marca}'");
			if(mysql_num_rows($sql) > 0){
				$res = mysql_fetch_array($sql);				
				$D082_Id = $res['D082_Id'];
			}
		}

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

		// Próximo Código
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		if(!empty($g['C031']['mascaraCodigoProdutoPreCadastro'])){
			$codigoProduto = $CAD002->obterNovoCodigoProduto($r_acaoId, false, true);
			$desabilitarCodigoProduto = true;
		} else {
			$desabilitarCodigoProduto = false;
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormPreCadastro_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormPreCadastro/';

		// form editor
		$form_editor = array(
			'geral' => '{"D009_Preco_1":{"top":237,"left":437.28125,"width":101,"hidden":1},"D001_Codigo_Produto":{"top":7,"left":38.8125,"width":184,"hidden":0},"obterCodigoAtualizado":{"top":5,"left":368,"width":null,"hidden":0},"D001_Descricao_Produto":{"top":30,"left":66.265625,"width":345,"hidden":0},"D001_D037_Id":{"top":53,"left":69.375,"width":191,"hidden":0},"D001_Flag_Pre_Cadastro":{"top":53,"left":340.640625,"width":60,"hidden":0},"D049_D082_Id":{"top":76,"left":84.1875,"width":191,"hidden":0},"D001_Flag_Ativo":{"top":76,"left":354.59375,"width":60,"hidden":0},"D001_D005_Id":{"top":99,"left":90.53125,"width":191,"hidden":0},"D049_Origem_Mercadoria":{"top":122,"left":24.765625,"width":192,"hidden":0},"D001_Flag_Tipo":{"top":145,"left":81,"width":182,"hidden":1},"D001_C008_Id":{"top":168,"left":95,"width":182,"hidden":1},"D001_D003_Id":{"top":191,"left":85,"width":182,"hidden":1},"D041_Valor_Base":{"top":260,"left":446.1875,"width":95,"hidden":1}}'
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormPreCadastro');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto','label'=>'Código Produto', 'type' => 'text','length' => array(30,20), 'value' => $codigoProduto, 'readonly' => $desabilitarCodigoProduto),
					'D001_Flag_Pre_Cadastro' => array('name' => 'D001_Flag_Pre_Cadastro', 'label'=>'Pré-cadastro','disabled' => true, 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_Descricao_Produto' => array('name' => 'D001_Descricao_Produto', 'value'=>@$T189_Descricao_Produto, 'label'=>'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'D041_Valor_Base' => array('name' => 'D041_Valor_Base', 'label'=>'Preço', 'type' => 'moeda', 'length' => array(14, 14),'reject' => true),
					'D009_Preco_1' => array('name' => 'D009_Preco_1', 'label'=>'Preço 1', 'type' => 'moeda', 'casasDecimais' => 2,'reject' => true),
					/*
					'D001_Descricao_Ingles' => array('name' => 'D001_Descricao_Ingles', 'label'=>'Descrição Inglês', 'type' => 'text', 'length' => array(64, 120)),
					'D001_Codigo_Barras' => array('name' => 'D001_Codigo_Barras', 'label'=>'Código de barras', 'type' => 'text', 'length' => array(64, 60)),
							
							'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'type' => 'select', 'select'=>
							//gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '')
							gGeraSelect('C008', 'C008_Id', 'C008_Tipo', '', true)
					),
					
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'type' => 'select','select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D001_D060_Id' => array('name' => 'D001_D060_Id', 'label'=>'Grupo', 'type' => 'select','select' => 
						gGeraSelect('D060', 'D060_Id', 'D060_Descricao_Grupo, D060_Codigo_Padrao', ' order by D060_Descricao_Grupo ', true)
					),          
					'D001_D002_Id' => array('name' => 'D001_D002_Id', 'label'=>'Sub-Grupo', 'type' => 'select','select' => 
						gGeraSelect('D002', 'D002_Id', 'D002_Descricao_Produto, D002_Codigo_Padrao', ' order by D002_Descricao_Produto', true)
					),          
					'D001_D015_Id' => array('name' => 'D001_D015_Id', 'label'=>'Aplicação', 'type' => 'select','select' =>
						gGeraSelect('D015', 'D015_Id', 'D015_Nome_Grupo, D015_Codigo_Padrao', 'order by D015_Nome_Grupo', true)
					),
										
					'D001_Peso_Unitario_Kg' => array('name' => 'D001_Peso_Unitario_Kg','casasDecimais' => 3, 'label'=>'Peso Unitário Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'type' => 'text', 'label'=>'Qtde por embalagem ', 'align' => 'right', 'style'=>'float:right;'),
					'D001_Peso_Total_Kg' => array('name' => 'D001_Peso_Total_Kg', 'casasDecimais' => 3, 'label'=>'Peso Total Emb. Kg', 'type' => 'moeda', 'value' => '0,000', 'length' => array(20, 30)),
					*/
					'D001_D037_Id' => array('name' => 'D001_D037_Id', 'label'=>'Unidades', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', 'order by D037_Unidade', true, @$r_D037_Id)
					),
					
					'D001_D005_Id' => array('name' => 'D001_D005_Id', 'label'=>'NCM', 'type' => 'select', 'select' =>
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true, @$r_D005_Id)
					),
					'D049_D082_Id' => array('name' => 'D049_D082_Id', 'label'=>'Marca', 'type' => 'select','reject'=>'true','select' =>
						gGeraSelect('D082', 'D082_Id', 'D082_Marca', '', true, @$D082_Id)
					),
					//'D001_Especificacoes' => array('name' => 'D001_Especificacoes', 'label'=>'Especificações', 'type' => 'textarea', 'length' => array(5, 61)),
					'D001_Flag_Tipo' => array('name' => 'D001_Flag_Tipo', 'label'=>'Origem', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'PRODUZIDO/IMPORTADO', 'value'=>'PRODUZIDO'),
							array('title' => 'ADQUIRIDO MERCADO NACIONAL', 'value' =>'COMPRADO')
						)
					),
					'D001_Flag_Ativo' => array('name' => 'D001_Flag_Ativo', 'label'=>'Item ativo', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => '...', 'value'=>''),
							array('title' => 'Sim', 'value'=>'S', 'selected'=>true),
							array('title' => 'Não', 'value' =>'N')
						)
					),
					'D001_C008_Id' => array('name' => 'D001_C008_Id', 'label'=>'Tipo', 'restricao' => false, 'type' => 'select', 'select'=>
						gGeraSelect('C008', 'C008_Id', 'C008_Tipo', '', true)
					),
					'D001_D003_Id' => array('name' => 'D001_D003_Id', 'label'=>'Linha', 'restricao' => false, 'type' => 'select','select' => 
						gGeraSelect('D003', 'D003_Id', 'D003_Nome_Linha', 'order by D003_Nome_Linha', true)
					),
					'D049_Origem_Mercadoria' => array('name' => 'D049_Origem_Mercadoria', 'label' => 'Origem mecadoria', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
				),
				
			) // 'campos'
		);
		if($desabilitarCodigoProduto){
			$formArray['campos']['geral']['obterCodigoAtualizado'] = array('name' => 'obterCodigoAtualizado', 'type'=>'button', 'reload' => false, 'value' => 'Atualizar código', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$form->definirFormularioArray($formArray);

		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#obterCodigoAtualizado').unbind('click');
				$('#obterCodigoAtualizado').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/cad/cad002/form_func-ajax/obterCodigoAtualizado/?ajax=true&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							$('#D001_Codigo_Produto').val(request.data);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, \$buttons)
						}
					});
				});
				$('#D049_Origem_Mercadoria').val('{$r_D069_Flag_Origem_Produto}');
				
		    } </script></div>
EOT;

		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		*        E carregamento dos valores (se não foi enviado e possui acaoId)
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
					$retorno = $CAD002->verificarCodigoProduto($form);
					if($retorno !== true){
						echo gGeraAlertMsg('Erro!', $retorno);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#D001_Codigo_Produto').focus();
							} </script></div>
EOT;
					}else{
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							//Pega o últmo Id inserido
							$D001_Id = $g['mysqlLastId'];
							//Insert D049
							$D082_Id 			    = $form->campoValorEnviado('D049_D082_Id');
							$D041_Valor_Base        = $form->campoValorEnviado('D041_Valor_Base');
							$D049_Origem_Mercadoria = $form->campoValorEnviado('D049_Origem_Mercadoria');
							$precoTabelado          = $g['C031']['precoTabelado'];

							mysql_query("insert into D049 (D049_D001_Id,D049_D082_Id,D049_Flag_Tipo,D049_Origem_Mercadoria) values ('{$D001_Id}','{$D082_Id}','CP','{$D049_Origem_Mercadoria}')");

							$D049_Id      = $g['mysqlLastId'];
							$D009_Id      = $CAD002->preCadastroMarca($D049_Id,$D001_Id,$D041_Valor_Base,$precoTabelado);
							$D009_Preco_1 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_1'));
							if($D009_Preco_1 > 0){
								mysqli_query("UPDATE D009 SET D009_Preco_1 = '{$D009_Preco_1}' WHERE D009_D049_Id = '{$D049_Id}'");
							}

							if($r_D069_Id > 0){
								mysql_query("UPDATE D069 SET D069_D009_Id = '{$D009_Id}', D069_Flag_Tabela_Principal='S' WHERE D069_Id = '{$r_D069_Id}'");
								require_once('bibliotecas/classes/CAD002.php');
        						$CAD002 = new CAD002();
								$CAD002->D001_reprocessa_historico(false, $D009_Id, 10);
							}
							$alert=gGeraAlertMsg('Erro', "{$return}");

							// Cadastrando referências na D083
							if($D069_Id > 0){
								$D069=mysql_query("SELECT D070_D024_Id, D069_Codigo_Barras, D069_Codigo_Produto,D070_Flag_Codigos_Comuns from D069 LEFT JOIN D070 ON D070_Id=D069_D070_Id WHERE D069_Id='{$D069_Id}'");
								$mD069=mysql_fetch_array($D069);
								if($mD069['D070_Flag_Codigos_Comuns']=='N'){
									$retorno = $CAD002->definirCodigoFornecedor($D009_Id, $mD069['D070_D024_Id'], $mD069['D069_Codigo_Produto'], $mD069['D069_Codigo_Barras']);
								}
							}
							//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							if (isset($r_abrirCadastro)) {
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												abrirJanela(data, '{$g['divIdRoot']}', '{$r_divForm}', unique(), '', 'Editar Produto - ID: ' + '{$D001_Id}', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$D001_Id}'), [980,550]);
											}, 150);
		  								});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} </script></div>
EOT;
							} else if(isset($r_T139_Id) && $r_T139_Id > 0){
								require('prog/ven/ven001/form_func-js.php');
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
EOT;
								//require("bibliotecas/funcoesJS/ven001CampoProcuraProduto.js");
								echo  <<<EOT
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										ven001CampoProcuraProduto('{$r_divForm}', '{$D009_Id}', '', '{$r_T003_Id}');
									} </script></div>
EOT;
							} else if(isset($r_T003_Id) && $r_T003_Id > 0){
								require('prog/ven/ven001/form_func-js.php');
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
EOT;
								//require("bibliotecas/funcoesJS/ven001CampoProcuraProduto.js");
								echo  <<<EOT
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										ven001CampoProcuraProduto('{$r_divForm}', '{$D009_Id}', '', '', '{$r_T003_Id}');
									} </script></div>
EOT;
							} else if(isset($r_T005_Id) && $r_T005_Id > 0){
								include('prog/ven/ven002/form_func-js.php');
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										campoProcuraProduto('{$r_divForm}', '{$D009_Id}', '', '{$r_T005_Id}');
									} </script></div>
EOT;
							} else if(isset($r_T218_Id) && $r_T218_Id > 0){
								require("bibliotecas/funcoesJS/est015CampoProcuraProduto.js");
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
EOT;
								echo  <<<EOT
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										est015CampoProcuraProduto('{$r_divForm}', '{$D009_Id}', '', '{$r_T218_Id}');
									} </script></div>
EOT;
							} else if(isset($r_T224_Id) && $r_T224_Id > 0){
								include('prog/est/est016/form_func-js.php');
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										campoProcuraProduto('{$r_divForm}', '{$D009_Id}', '', '{$r_T224_Id}');
									} </script></div>
EOT;
							} else {
								echo  <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									} </script></div>
EOT;
							}
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
				if ($form->campoValor('D001_C008_Id') > 9999) {
					$cClausula .= " and D003_C008_Id='" . $form->campoValor('D001_C008_Id') . "' ";
				}
				if ($form->campoValor('C008_Inclusao_Produto_Mostrar_Codigo_Grupos') == 'S') {
					$cClausula .= " order by D003_Codigo_Padrao ";
				} else {
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
		echo  $g['smarty']->fetch('formEditor.tpl');
		
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
	
	case 'cad002FormServicos':
				/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
		
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
		$form_tabela = 'D135';
		$form_campo_pk = 'D135_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad002FormServicosBtnAdd', 'cad002FormServicosBtnCln', 'cad002FormServicosProdutoBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormServicos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D135_D112_Id":{"top":30,"left":15,"width":425,"hidden":0},"cad002FormServicosBtnAdd":{"top":28,"left":528,"width":null,"hidden":0}}',
        );
        
		// Array de definição do formulário
		//$r_D024_Id = isset($r_cad001FormDocumentos) ? $r_cad001FormDocumentos['D087_D024_Id'] : $r_D024_Id;
		
		// $tmp = gGeraSelect('D024', 'D024_Id', 'D024_Nome_Fantasia,D024_Nome_Empresa', "WHERE D024_Id = $r_D024_Id");
		// foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormServicos');
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'D135_D112_Id' => array('name' => 'D135_D112_Id','label'=>'Serviço', 'type' => 'pesquisa', 'pesquisa' => array('D112','D112_Id','D112_Id,D112_Titulo',''), 'length' => array(80,30)),
						//'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'D135_D001_Id' => array('name' => 'D135_D001_Id', 'label' => '', 'type' => 'hidden', 'value' => $D001_Id),
					)
				)
			);
			$formArray['campos']['geral']['cad002FormServicosBtnAdd'] = array('name' => 'cad002FormServicosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
 
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
					// Insert
				if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					// Imprime uma mensagem e fecha a janela					
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
							//divSetarAcaoId('{$g['divId']}',0);
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
						echo  $processado;
					}
				}
			}
		}
		
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $D001_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
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
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
	
	case 'cad002FormJuntarProdutos':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		
		//echo "D001: ".$D001_Id."<br />D009_Id: ".$D049_Id."<br />acao: ".$acaoId;
		
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
		$form_tabela = 'T066';
		$form_campo_pk = 'T066_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormJuntarProdutos_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormJuntarProdutos/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (1=1)
			group by D001_Codigo_Produto
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																											// tabela
			'D009_Id',																										// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido,D001_Descricao_Ingles',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Produto',																							// título da janela
			'Código,Descrição,Marca,Estoque,Descrição Inglês',																// descrição dos campos
			'cad002ProcurarJuntarProdutos',																					// callback js
			$g['divId'],																									// divId
		);
		$pesquisaGrid2 = $pesquisaGrid;
		$pesquisaGrid2[6] = 'cad002ProcurarJuntarProdutos2';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormJuntarProdutos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Consulta_D001_D001_Id_Antigo' => array('name' => 'Consulta_D001_D001_Id_Antigo', 'label'=>'Código Antigo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true),
					'Consulta_D001_D001_Id_Novo' => array('name' => 'Consulta_D001_D001_Id_Novo', 'label'=>'Código Novo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid2, 'length' => array(64,120), 'reject' => true),
					'D001_D001_Id_Antigo' => array('name' => 'D001_D001_Id_Antigo', 'label'=>'D001_D001_Id_Antigo', 'type' => 'hidden', 'reject' => true),
					'D001_D001_Id_Novo' => array('name' => 'D001_D001_Id_Novo', 'label'=>'D001_D001_Id_Novo', 'type' => 'hidden', 'reject' => true),
					
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
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					$retorno = $CAD002->juntarProdutos($form);
					if ($retorno !== true) {
						echo gGeraAlertMsg('Erro', $retorno);	
					} else {
						echo gGeraAlertMsg('Operação OK', "Processo concluído!");	
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		$gerado = gGeraCamposForm($form, $tab, false, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Este processo é irreversível, deseja continuar?");
		
		
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
		$botoesDir .= '<button type="submit" id="cad002FormJuntarProdutos_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário	
        break;
        
        case 'cad002FormTrocarCodigos':
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		
		//echo "D001: ".$D001_Id."<br />D009_Id: ".$D049_Id."<br />acao: ".$acaoId;
		
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
		$form_tabela = 'T066';
		$form_campo_pk = 'T066_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormTrocarCodigos_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormTrocarCodigos/';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormTrocarCodigos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_Codigo_Antigo' => array('name' => 'D001_Codigo_Antigo', 'label'=>'Código Antigo', 'type' => 'text', 'length' => array(30,60), 'reject' => true),
					'D001_Codigo_Novo' => array('name' => 'D001_Codigo_Novo', 'label'=>'Código Novo', 'type' => 'text', 'length' => array(30,60), 'reject' => true),					
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
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					$retorno = $CAD002->trocarCodigos($form);
					if ($retorno !== true) {
						echo gGeraAlertMsg('Erro', $retorno);	
					} else {
						echo gGeraAlertMsg('Operação OK', "Processo concluído!");	
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
		$gerado = gGeraCamposForm($form, $tab, false, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Este processo é irreversível, deseja continuar?");
		
		
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
		$botoesDir .= '<button type="submit" id="cad002FormTrocarCodigos_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário	
		break;
		
		case 'cad002FormEcommerce':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		$r_D049_Id = (isset($_REQUEST['D049_Id'])) ? $_REQUEST['D049_Id'] : false;
		
		$sql="SELECT D136_Id FROM D136 WHERE D136_D001_Id='{$r_D001_Id}'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		if($row['D136_Id']>0){
			$r_acaoId=$row['D136_Id'];
		}
		  
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D136';
		$form_campo_pk = 'D136_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormEcommerce_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormEcommerce/';
		//$data = date('Y-m-d');
		// Definição do Formulário
		
		$selectPercentDesconto = array();
		$selectPercentDesconto[] = array('value' => '0', 'title' => '0 %');
		for ($i=1; $i<=99; $i++) {
				if($i=='5'){
					$selectPercentDesconto[] = array('value' => $i, 'title' => $i . " %", 'selected'=>true);
				}else{
					$selectPercentDesconto[] = array('value' => $i, 'title' => $i . " %");
				}
		 }
		 
		$selectVezesParcelasSemJuros = array();
		$selectVezesParcelasComJuros = array();
		$selectVezesParcelasSemJuros[] = array('value' => '', 'title' => 'Sem Juros');
		$selectVezesParcelasComJuros[] = array('value' => '', 'title' => 'Com Juros');
		for ($i=1; $i<=12; $i++) {
			$selectVezesParcelasSemJuros[] = array('value' => $i, 'title' => $i . " x");
			$selectVezesParcelasComJuros[] = array('value' => $i, 'title' => $i . " x");
		}
		
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'D136_Status_Uso' => array('name' => 'D136_Status_Uso', 'label' => 'Status de uso', 'type' => 'select','select' =>
						array(
							array('title' => 'Novo','value' => 'N'),
							array('title' => 'Usado','value' => 'U'),
							array('title' => 'Recondicionado','value' => 'R'),
							array('title' => 'Mostruário','value' => 'D'),
						),
					),
					'D136_link_youtube' => array('name' => 'D136_link_youtube', 'label' => 'Link Youtube', 'type' => 'text','length' => array(40,100)),
					'D136_Mais_Informacoes' => array('name' => 'D136_Mais_Informacoes', 'label'=>'Mais Informações', 'type' => 'textarea', 'length' => array(5,70)),
					'D136_Detalhes_Tecnicos' => array('name' => 'D136_Detalhes_Tecnicos', 'label'=>'Detalhes Técnicos', 'type' => 'textarea', 'length' => array(5,70)),
					'D136_Itens_Inclusos' => array('name' => 'D136_Itens_Inclusos', 'label'=>'Itens Inclusos', 'type' => 'textarea', 'length' => array(5,70)),
					'D136_Conexoes' => array('name' => 'D136_Conexoes', 'label'=>'Conexões', 'type' => 'textarea', 'length' => array(5,70)),
					'D136_Flag_Mercado_Livre' => array('name' => 'D136_Flag_Mercado_Livre', 'label' => 'Mercado Livre', 'type' => 'select','select' =>
						array(
							array('title' => 'Não','value' => 'N'),
							array('title' => 'Sim','value' => 'S'),
						),
					),
					'D136_D001_Id' => array('name' => 'D136_D001_Id', 'label' => 'D136_D001_Id', 'type' => 'hidden','value' => $r_D001_Id),
					'D136_D049_Id' => array('name' => 'D136_D049_Id', 'label' => 'D136_D049_Id', 'type' => 'hidden','value' => $r_D049_Id),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				),
				'estoque' => array(
					'D136_Garantia' => array('name' => 'D136_Garantia', 'label' => 'Garantia', 'type' => 'text','length' => array(10,10)),
					'D136_Unidade' => array('name' => 'D136_Unidade', 'label' => 'Unidade', 'type' => 'select', 
						'select' => array(
							array('title' => '- Unidade de Medida -','value' => ''),
							array('title' => 'CAIXA','value' => '15'),
							array('title' => 'CONJUNTO','value' => '14'),
							array('title' => 'GRAMA','value' => '17'),
							array('title' => 'JOGO','value' => '1'),
							array('title' => 'KIT','value' => '2'),
							array('title' => 'LITRO','value' => '6'),
							array('title' => 'METRO','value' => '7'),
							array('title' => 'PACOTE','value' => '13'),
							array('title' => 'PAR','value' => '4'),
							array('title' => 'PEÇCA','value' => '3'),
							array('title' => 'POR PÁGINA','value' => '16'),
							array('title' => 'QUILOGRAMA','value' => '10'),
							array('title' => 'SERVIÇO','value' => '11'),
							array('title' => 'UNIDADE','value' => '5'),
						)
					),
					'D136_Qte_Embalagem' => array('name' => 'D136_Qte_Embalagem', 'label' => 'Qtde por embalagem', 'type' => 'moeda','length' => array(10,10)),
					'D136_Largura' => array('name' => 'D136_Largura', 'label' => 'Largura c/ embalagem', 'type' => 'moeda','length' => array(10,10)),
					'D136_Profundidade' => array('name' => 'D136_Profundidade', 'label' => 'Profundidade c/ embalagem', 'type' => 'moeda','length' => array(10,10)),
					'D136_Altura' => array('name' => 'D136_Altura', 'label' => 'Altura c/ embalagem', 'type' => 'moeda','length' => array(10,10)),
					'D136_Peso' => array('name' => 'D136_Peso', 'label' => 'Peso c/ embalagem', 'type' => 'moeda','casasDecimais' =>'3', 'length' => array(10,10)),
					'D136_Estoque_Virtual' => array('name' => 'D136_Estoque_Virtual', 'label' => 'Estoque virtual', 'type' => 'moeda','length' => array(10,10)),
					'D136_Estoque_Minimo' => array('name' => 'D136_Estoque_Minimo', 'label' => 'Estoque mínimo', 'type' => 'moeda','length' => array(10,10)),
					'D136_Qte_Padrao_Carrinho' => array('name' => 'D136_Qte_Padrao_Carrinho', 'label' => 'Qde padrão enviar carrinho', 'type' => 'moeda','length' => array(10,10)),
					'D136_Faturamento_Direto' => array('name' => 'D136_Faturamento_Direto', 'label' => 'Faturamento Direto', 'type' => 'select',
						'select' => array(
							array('title' => 'SIM (PADRÃO)', 'value' => 'T'),
							array('title' => 'NÃO (NÃO PERMITE FATURAMENTO BOLETO PÓS-PAGO)', 'value' => 'F'),
						)
					),
					'D136_Disponibilidade' => array('name' => 'D136_Disponibilidade', 'label' => 'Disponibilidade', 'type' => 'select', 
						'select' => array(
							array('title' => '- Selecione -','value' => ''),
							array('title' => 'Entrega Imediata','value' => '1'),
							array('title' => 'Envio em','value' => '6'),
							array('title' => 'Não Disponível','value' => '5'),
							array('title' => 'Pré-Venda','value' => '4'),
							array('title' => 'Sob Consulta','value' => '2'),
						)
					),
					'D136_Disponibilidade_Dias' => array('name' => 'D136_Disponibilidade_Dias', 'label' => 'Disponibilidade Dias', 'type' => 'text','length' => array(10,10)),
					'cad002tituloTeleVendas' => array('name' => 'cad002tituloTeleVendas', 'label'=>'Tele-Vendas','type' => 'titulo', 'reject' => true,'extra' => 'style="padding-top:20px;"'),
					'D136_Permite_Venda_Site' => array('name' => 'D136_Permite_Venda_Site', 'label' => 'Permite Venda no Site:', 'type' => 'select','extra' => 'style="width:150px;"', 
						'select' => array(
							array('title' => 'SIM(PADRÃO)','value' => 'S'),
							array('title' => 'NÃO (APENAS TELE-VENDAS)','value' => 'N'),
						)
					),
					'D136_Preco_Sob_Consulta' => array('name' => 'D136_Preco_Sob_Consulta', 'label' => 'Permite Venda no Site:', 'type' => 'select','extra' => 'style="width:150px;"', 
						'select' => array(
							array('title' => 'NÃO (PADRÃO)','value' => 'F'),
							array('title' => 'SIM (OCULTAR PREÇO, BLOQUEIA VENDA NO SITE)','value' => 'T'),
						)
					),
					
				),
				'valores' => array(
					'D136_Preco_Fantasia' => array('name' => 'D136_Preco_Fantasia', 'label' => 'Preço de venda fantasia:', 'type' => 'moeda','length' => array(10,10)),
					'D136_Preco_Venda' => array('name' => 'D136_Preco_Venda', 'label' => 'Preço venda', 'type' => 'moeda','length' => array(10,10)),
					'D136_Desconto_Vista' => array('name' => 'D136_Desconto_Vista', 'label' => 'Desconto a vista', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  $selectPercentDesconto,
					),
					'D136_Frete_Gratis' => array('name' => 'D136_Frete_Gratis', 'label' => 'Frete grátis', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  array(
							array('title' => 'NÃO', 'value' => 'F'),
							array('title' => 'SIM', 'value' => 'T'),
						),
					),
					'D136_Frete_Gratis_Carrinho' => array('name' => 'D136_Frete_Gratis_Carrinho', 'label' => 'Para todo carrinho:','align' => 'right', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  array(
							array('title' => 'NÃO', 'value' => 'F'),
							array('title' => 'SIM', 'value' => 'T'),
						),
					),
					'D136_Bloquear_Transporte' => array('name' => 'D136_Bloquear_Transporte', 'label' => 'Bloquear Transporte', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  array(
							array('title' => 'NÃO', 'value' => 'F'),
							array('title' => 'SIM', 'value' => 'T'),
						),
					),
					'D136_Pacote_Presente' => array('name' => 'D136_Pacote_Presente', 'label' => 'Pacote de Presente', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  array(
							array('title' => 'NÃO', 'value' => 'F'),
							array('title' => 'SIM', 'value' => 'T'),
						),
					),
					'D136_Pacote_Presente_Valor' => array('name' => 'D136_Pacote_Presente_Valor', 'label' => 'Valor do Pacote-Presente', 'type' => 'moeda','length' => array(10,10)),
					'D136_Atualizar_Produtos_Opcao' => array('name' => 'D136_Atualizar_Produtos_Opcao', 'label' => 'Atualizar produtos-opção?', 'type' => 'select','extra' => 'style="width:82px;"',
						'select' =>  array(
							array('title' => 'SIM', 'value' => '1'),
							array('title' => 'NÃO', 'value' => '0'),
						),
					),
					'cad002tituloFormaPagamento' => array('name' => 'cad002tituloFormaPagamento', 'label'=>'Forma de Pagamento','type' => 'titulo', 'reject' => true,'extra' => 'style="padding-top:20px;"'),
					//Forma de Pagamento AMEX
					'D136_AMEX_Vezes_Sem_Juros' => array('name' => 'D136_AMEX_Vezes_Sem_Juros', 'label' => 'AMEX', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => $selectVezesParcelasSemJuros,
					),
					'D136_AMEX_Parcela_Juros' => array('name' => 'D136_AMEX_Parcela_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F'),
						)
					),
					'D136_AMEX_Vezes_Com_Juros' => array('name' => 'D136_AMEX_Vezes_Com_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => $selectVezesParcelasComJuros,
					),
					'D136_AMEX_Taxa_Juros' => array('name' => 'D136_AMEX_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
					 //Forma de Pagamento Boleto
					'D136_Boleto_Vezes_Sem_Juros' => array('name' => 'D136_Boleto_Vezes_Sem_Juros', 'label' => 'Boleto Bancário', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => array(
						 	array('title' => 'Sem Juros', 'value' => ''),
						 	array('title' => '1 x', 'value' => '1','selected' => true),
						)
					),
					'D136_Boleto_Parcela_Juros' => array('name' => 'D136_Boleto_Parcela_Juros', 'label' => '','disabled' => true, 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F','selected' => true),
						)
					),
					'D136_Boleto_Vezes_Com_Juros' => array('name' => 'D136_Boleto_Vezes_Com_Juros', 'label' => '','disabled' => true, 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Com Juros', 'value' => ''),
						)
					),
					'D136_Boleto_Taxa_Juros' => array('name' => 'D136_Boleto_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
					//Forma de Pagamento Depósito Itaú
					'D136_Itau_Vezes_Sem_Juros' => array('name' => 'D136_Itau_Vezes_Sem_Juros', 'label' => 'Depósito Itaú', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => array(
						 	array('title' => 'Sem Juros', 'value' => ''),
						 	array('title' => '1 x', 'value' => '1','selected' => true),
						)
					),
					'D136_Itau_Parcela_Juros' => array('name' => 'D136_Itau_Parcela_Juros', 'label' => '','disabled' => true, 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F','selected' => true),
						)
					),
					'D136_Itau_Vezes_Com_Juros' => array('name' => 'D136_Itau_Vezes_Com_Juros', 'label' => '','disabled' => true, 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Com Juros', 'value' => ''),
						)
					),
					'D136_Itau_Taxa_Juros' => array('name' => 'D136_Itau_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
					 //Forma de Pagamento Mastercard
					'D136_Mastercard_Vezes_Sem_Juros' => array('name' => 'D136_Mastercard_Vezes_Sem_Juros', 'label' => 'Mastercard', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => $selectVezesParcelasSemJuros,
					),
					'D136_Mastercard_Parcela_Juros' => array('name' => 'D136_Mastercard_Parcela_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F'),
						)
					),
					'D136_Mastercard_Vezes_Com_Juros' => array('name' => 'D136_Mastercard_Vezes_Com_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => $selectVezesParcelasComJuros,
					),
					'D136_Mastercard_Taxa_Juros' => array('name' => 'D136_Mastercard_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
					 //Forma de Pagamento Visa
					'D136_Visa_Vezes_Sem_Juros' => array('name' => 'D136_Visa_Vezes_Sem_Juros', 'label' => 'Visa', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => $selectVezesParcelasSemJuros,
					),
					'D136_Visa_Parcela_Juros' => array('name' => 'D136_Visa_Parcela_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F'),
						)
					),
					'D136_Visa_Vezes_Com_Juros' => array('name' => 'D136_Visa_Vezes_Com_Juros', 'label' => '', 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => $selectVezesParcelasComJuros,
					),
					'D136_Visa_Taxa_Juros' => array('name' => 'D136_Visa_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
                    //Forma de Pagamento Visa Electron Bradesco
					'D136_VEBradesco_Vezes_Sem_Juros' => array('name' => 'D136_VEBradesco_Vezes_Sem_Juros', 'label' => 'Visa Electron Bradesco', 'type' => 'select', 'extra' => 'title="Vezes s/ Juros" style="width:82px;"',
						'select' => array(
						 	array('title' => 'Sem Juros', 'value' => ''),
						 	array('title' => '1 x', 'value' => '1','selected' => true),
						)
					),
					'D136_VEBradesco_Parcela_Juros' => array('name' => 'D136_VEBradesco_Parcela_Juros', 'label' => '','disabled' => true, 'type' => 'select', 'extra' => 'title="Parcela c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Sim', 'value' => 'T'),
						 	array('title' => 'Não', 'value' => 'F','selected' => true),
						)
					),
					'D136_VEBradesco_Vezes_Com_Juros' => array('name' => 'D136_VEBradesco_Vezes_Com_Juros','disabled' => true, 'label' => '', 'type' => 'select', 'extra' => 'title="Vezes c/ Juros" style="width:82px;"','align' => 'right',
						'select' => array(
						 	array('title' => 'Com Juros', 'value' => ''),
						)
					),
					'D136_VEBradesco_Taxa_Juros' => array('name' => 'D136_VEBradesco_Taxa_Juros', 'label' => '', 'type' => 'moeda','length' => array(10,10),'extra' => 'title="Juros a.m(%)"','align' => 'right'),
				)
			)
		);
        $form = new Formulario('cad002FormEcommerce');
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
				//Caso o campo seja checkbox
				//$form->campoSetarValor('D136_Frete_Gratis_Carrinho', $form->campoValorEnviado('D136_Frete_Gratis_Carrinho') ? 'S' : 'N', true);
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
		$g['smarty']->display('form.tpl');

	break;
	
	case 'formXML':
			/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
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

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D009';
		$form_campo_pk = 'D009_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormGerarXML_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/formXML/';

		// novo formulário
		$form = new Formulario('cad002formXML');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D009_Inicio' => array('name' => 'D009_Inicio','label'=>'Produto Inicial', 'type' => 'text', 'length' => array(30,40),'reject'=>true),
					'D009_Fim' => array('name' => 'D009_Fim','label'=>'Produto Final', 'type' => 'text', 'length' => array(30,40),'reject'=>true),
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
				
				$gerado=$XML->gerarXMLProduto($form->campoValorEnviado('D009_Inicio'),$form->campoValorEnviado('D009_Fim'));			
				if ($gerado>0){
					$path="/hardness3/".$g['pathDados'].'tmp/'.$gerado.'-produtos.xml';
					echo gGeraAlertMsg('Operação OK', 'XML Gerado com Sucesso!<br/><a href="'.$path.'" target="_blank">Abrir Arquivo</a>');
				}else{
					echo gGeraAlertMsg('Erro', 'Falha na Geração do XML');
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormGerarXML_btn">Gerar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormJuntar':
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormBtnJuntar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormJuntar/';
        
        // form editor
        $form_editor = array(
            'geral' => '{"D001_Id_Antigo":{"top":7,"left":8,"width":248,"hidden":0},"D001_Id_Novo":{"top":30,"left":14,"width":248,"hidden":0},"TextoAviso":{"top":53,"left":12,"width":null,"hidden":0}}',
        );
        
		// Array de definição do formulário
		$pesquisaGridAntigo = array(
			'D001',																											              // tabela
			'D001_Id',																										              // chave primaria
			'D001_Codigo_Produto||D001_Descricao_Produto||D001_Descricao_Ingles',
			'ORDER BY D001_Codigo_Produto ASC',
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Descrição,Descrição Inglês,',
			'cad002JuntarProcuraProdutos',																					              // callback js
			$g['divId'],																									              // divId
			'Antigo',					              																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
			),
			''																																// Campos que são ignorados na cláusula where do pesquisaGrid
		);
		$pesquisaGridNovo = array(
			'D001',																											              // tabela
			'D001_Id',																										              // chave primaria
			'D001_Codigo_Produto||D001_Descricao_Produto||D001_Descricao_Ingles',
			'ORDER BY D001_Codigo_Produto ASC',
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Descrição,Descrição Inglês,',
			'cad002JuntarProcuraProdutos',																					              // callback js
			$g['divId'],																									              // divId
			'Novo',					              																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
			),
			''																																// Campos que são ignorados na cláusula where do pesquisaGrid
		);
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formJuntarcad002Form');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_Id_Antigo' => array('name' => 'D001_Id_Antigo', 'label' => 'Produto Antigo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGridAntigo, 'reject' => true),
					'D001_Id_Novo' => array('name' => 'D001_Id_Novo', 'label' => 'Produto Novo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGridNovo, 'reject' => true),
					'TextoAviso' => array('name' => 'TextoAviso', 'label'=>'', 'type' => 'comentario', 'value' => '* Este processo irá juntar o produto <b>Antigo</b> com o <b>Novo</b>.', 'reject' => true),
				),
			) // 'campos'
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
				set_time_limit(0);
				$retorno = $CAD002->juntarProdutos($form);
				if ($retorno === true) {
					$alert .= gGeraAlertMsg('OK', 'Os produtos foram juntados com sucesso!', false);
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
				} else {
					if (strpos($retorno, 'BLOQUEADO') !== false) {
						$retorno = explode('-', $retorno);
						$retorno = "O Produto Antigo possui registros em '{$retorno[1]}' e não pode ser juntado.";
					}
					echo gGeraAlertMsg('Ocorreu um Erro', $retorno);
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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormBtnJuntar">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormProdutosSerie':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
		
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
		$form_tabela = 'D144';
		$form_campo_pk = 'D144_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad002FormProdutosSerieBtnAdd', 'cad002FormProdutosSerieBtnCln', 'cad002FormProdutosSerieBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormProdutosSerie/';

		//form editor
		$form_editor = array('geral' => '{"T129_Descricao_Produto":{"top":30,"left":9,"width":424,"hidden":0},"Procura_Produto":{"top":7,"left":21,"width":408,"hidden":0},"T129_Quantidade":{"top":76,"left":3,"width":424,"hidden":0},"D082_Marca":{"top":53,"left":28,"width":424,"hidden":0},"est008FormBtnCadastrarProduto":{"top":97,"left":176,"width":null,"hidden":0},"est008FormProdutosBtnCln":{"top":97,"left":352,"width":null,"hidden":0},"est008FormProdutosBtnAdd":{"top":97,"left":448,"width":null,"hidden":0}}');
		
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
			'campoProcuraProdutoOS',																									// callback js
			$g['divId']																												// divId
		);

		$D009=mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$D009_Id}'");
		$mD009=mysql_fetch_array($D009);		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(80,30), 'reject' => true),
					//'D082_Marca' => array('name' => 'D082_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(80,30), 'reject' => true, 'readonly' => true),
					// Campo Pesquisa
					'D144_Descricao_Produto' => array('name' => 'D144_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(80,50)),
					'D144_D049_Id' => array('name' => 'D144_D049_Id', 'label' => 'D009_Id', 'type' => 'hidden', 'value'=>$mD009['D009_D049_Id']),
				)
			)
		);
        $form = new Formulario('cad002FormProdutosSerie');
        // Botões dentro do Formulário
		if (!empty($r_acaoId)) {
			 //$formArray['campos']['geral']['est008FormProdutosBtnNew'] = array('name' => 'est008FormProdutosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad002FormProdutosSerieBtnAdd'] = array('name' => 'cad002FormProdutosSerieBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad002FormProdutosSerieBtnCln'] = array('name' => 'cad002FormProdutosSerieBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		//$formArray['campos']['geral']['est008FormBtnCadastrarProduto'] = array('name' => 'est008FormBtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						die($sql);
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

	case 'cad002FormListaFornecedor':
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
		$form_tabela = 'D070';
		$form_campo_pk = 'D070_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormListaFornecedorBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormListaFornecedor/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"D070_C004_Id":{"top":7,"left":23.96875,"width":495,"hidden":0},"D070_D024_Id":{"top":30,"left":11.5,"width":474,"hidden":0},"D070_D082_Id":{"top":53,"left":43.65625,"width":474,"hidden":0},"D070_Data":{"top":76,"left":51.828125,"width":88,"hidden":0},"D070_Data_Vencimento":{"top":76,"left":393.5625,"width":88,"hidden":0},"D070_Flag_Ativo":{"top":99,"left":49.734375,"width":96,"hidden":0},"D070_Observacao":{"top":122,"left":16.296875,"height":60,"width":488,"hidden":0},"D070_Flag_Ajustar_MVA":{"top":191,"left":13.671875,"width":64,"hidden":0},"D070_Flag_Vincula_Item_Pre_Cadastro":{"top":191,"left":361.1875,"width":63,"hidden":0},"D070_Flag_Calcula_Percentual_ST":{"top":237,"left":415.5,"width":55,"hidden":1}}'
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
		        	'D070_C004_Id' => array('name' => 'D070_C004_Id', 'label' => 'Empresa', 'type' => 'select', 'select'=>
						gGeraSelect('C004', 'C004_Id', 'C004_Id, C004_Nome_Empresa', 'order by C004_Id', true)
					),
					'D070_D024_Id' => array('name' => 'D070_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id,D024_Cnpj,D024_Cpf','WHERE D024_Flag_Ativo="S"')),
					'D070_Data' => array('name' => 'D070_Data', 'label' => 'Data', 'type' => 'data', 'value'=>date("d/m/Y"), 'length' => array(40,40)),
					'D070_Data_Vencimento' => array('name' => 'D070_Data_Vencimento', 'label' => 'Data de validade', 'type' => 'data', 'length' => array(40,40)),
					'D070_Observacao' => array('name' => 'D070_Observacao', 'label' => 'Observação', 'type' => 'textarea'),
					'D070_Flag_Ativo' => array('name' => 'D070_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D070_Flag_Ajustar_MVA' => array('name' => 'D070_Flag_Ajustar_MVA', 'label' => 'Ajustar MVA', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D070_D082_Id' => array('name' => 'D070_D082_Id','label'=>'Marca', 'type' => 'pesquisa', 'pesquisa' => array('D082','D082_Id','D082_Marca,D082_Id','WHERE D082_Flag_Ativo="S"')),
					'D070_Flag_Calcula_Percentual_ST' => array('name' => 'D070_Flag_Calcula_Percentual_ST', 'label' => 'Calcula Percentual ST', 'type' => 'select',
						'select' => array(
							array ('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array ('title' => 'Não', 'value' => 'N'),
						)					
					),
					'D070_Flag_Vincula_Item_Pre_Cadastro' => array('name' => 'D070_Flag_Vincula_Item_Pre_Cadastro', 'label' => 'Vincula itens em pré-cadastro', 'type' => 'select',
						'select' => array(
							array ('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array ('title' => 'Não', 'value' => 'N'),
						)					
					),
				)
			)
		);
		$form = new Formulario('cad002FormListaFornecedor');
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
						$D070_Id = $g['mysqlLastId'];
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar Tabela Fornecedor', '/cad/cad002/content/cad002ContentListaFornecedorAlterar/', '&acaoId=' + encodeURIComponent('{$D070_Id}') + '&tabela=$from', [1000,600]);
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
		$botoesDir .= '<button type="submit" id="cad002FormListaFornecedorBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormListaFornecedorProduto':
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
		$form_tabela = 'D069';
		$form_campo_pk = 'D069_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormListaFornecedorProdutoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormListaFornecedorProduto/';
        
        //form editor
        $form_editor = array(
			'geral' => '{"D069_Codigo_Produto":{"top":7,"left":57,"width":129,"hidden":0},"D069_Complemento":{"top":7,"left":281.99998474121094,"width":120,"hidden":0},"D069_Flag_Nacional_Importado":{"top":7,"left":551.9999847412109,"width":144,"hidden":0},"D069_Descricao":{"top":30,"left":41,"width":376,"hidden":0},"D069_MVA":{"top":30,"left":562.9999847412109,"width":136,"hidden":0},"D069_Preco_Unitario":{"top":53,"left":23,"width":136,"hidden":0},"D069_Preco_Unitario_Liquido":{"top":53,"left":242,"width":120,"hidden":0},"D069_Prazo_Entrega":{"top":53,"left":517.9999847412109,"width":136,"hidden":0},"D069_Peso_Unitario":{"top":76,"left":27,"width":136,"hidden":0},"D069_Unidade":{"top":76,"left":331.99998474121094,"width":120,"hidden":0},"D069_Aliquota_IPI":{"top":99,"left":67,"width":40,"hidden":0},"D069_Aliquota_ICMS":{"top":99,"left":149,"width":40,"hidden":0},"D069_NCM":{"top":99,"left":323.99998474121094,"width":120,"hidden":0},"D069_Percentual_Desconto":{"top":122,"left":31,"width":136,"hidden":0},"D069_Codigo_Barras":{"top":122,"left":287.99998474121094,"width":120,"hidden":0},"D069_D009_Id":{"top":145,"left":31,"width":361,"hidden":0},"cad002FormListaFornecedorProdutoBtn":{"top":120,"left":591.9999847412109,"width":null,"hidden":0}}'
        );
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D069_Codigo_Produto' => array('name' => 'D069_Codigo_Produto', 'label' => 'Código', 'type' => 'text'),
					'D069_Descricao'  => array('name' => 'D069_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'D069_Preco_Unitario' => array('name' => 'D069_Preco_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda'),
					'D069_Preco_Unitario_Liquido' => array('name' => 'D069_Preco_Unitario_Liquido', 'label' => 'Preço Unitário Liquido', 'type' => 'moeda'),
					'D069_Peso_Unitario' => array('name' => 'D069_Peso_Unitario', 'label' => 'Peso Unitário', 'type' => 'moeda'),
					'D069_Unidade' => array('name' => 'D069_Unidade', 'label' => 'UN', 'type' => 'text'),
					'D069_Aliquota_IPI' => array('name' => 'D069_Aliquota_IPI', 'label' => '% IPI', 'type' => 'moeda'),
					'D069_NCM' => array('name' => 'D069_NCM', 'label' => 'NCM', 'type' => 'text'),
					'D069_Codigo_Barras' => array('name' => 'D069_Codigo_Barras', 'label' => 'Cód. Barras', 'type' => 'text'),
					'D069_Complemento' => array('name' => 'D069_Complemento', 'label' => 'Complemento', 'type' => 'text'),
					'D069_Percentual_Desconto' => array('name' => 'D069_Percentual_Desconto', 'label' => '% Desconto', 'type' => 'text'),
					'D069_Prazo_Entrega' => array('name' => 'D069_Prazo_Entrega', 'label' => 'Prazo Entrega', 'type' => 'text'),
					'D069_MVA' => array('name' => 'D069_MVA', 'label' => 'MVA', 'type' => 'moeda'),
					'D069_Aliquota_ICMS' => array('name' => 'D069_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'text'),
					'D069_Flag_Nacional_Importado' => array('name' => 'D069_Flag_Nacional_Importado', 'label' => 'Origem', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Nacional', 'value' => 'N'),
							array('title' => 'Importado', 'value' => 'I'),
						)
					),
					'D069_D009_Id' => array('name' => 'D069_D009_Id','label'=>'Nosso Cód', 'type' => 'pesquisa', 'pesquisa' => array('D009','D009_Id','D001_Codigo_Produto,D001_Descricao_Produto',"LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_C004_Id='{$g['empresaAtual']}'")),
					'D069_D070_Id' => array('name' => 'D069_D070_Id', 'label' => 'D069_D070_Id', 'type' => 'hidden', 'value'=>$r_D070_Id),
				)
			)
		);
		$formArray['campos']['geral']['cad002FormListaFornecedorProdutoBtn'] = array('name' => 'cad002FormListaFornecedorProdutoBtn', 'type'=>'button', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $form = new Formulario('cad002FormListaFornecedor');
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
								divRefresh('{$r_divReload}');
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$D014_Id = $g['mysqlLastId'];
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								divRefresh('{$r_divReload}');
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
		//$botoesDir .= '<button type="submit" id="cad002FormListaFornecedorProdutoBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormAssociarProduto':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D069_Id = (isset($r_acaoId)) ? $r_acaoId : false;

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
		$enviar_btnId = 'cad002FormAssociarProduto_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormAssociarProduto/';
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}' AND D001_Flag_Ativo != 'N'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',		// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoAssociar',																		// callback js
			$g['divId'],																						// divId
			''																									// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('cad002FormAssociarProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50)),
					// Campos Hidden
					'D001_Codigo_Produto_Associar' => array('name' => 'D001_Codigo_Produto_Associar', 'label' => 'D001_Codigo_Produto_Associar', 'type' => 'hidden'),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'D069_Id' => array('name' => 'D069_Id', 'label' => 'D069_Id','value' => $D069_Id, 'type' => 'hidden'),
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
				$retorno = $CAD002->associarProdutoListaFornecedor($form);
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
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormAssociarProduto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	case 'cad002FormObservacaoMontagem':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		//$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$acaoId = $r_D001_Id;
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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormObservacaoMontagem_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormObservacaoMontagem/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"D001_Observacao_Montagem":{"top":7,"left":5,"height":131,"width":535,"hidden":0},"D049_Percentual_Acrescimo_Custo":{"top":7,"left":637,"width":40,"hidden":0},"cad002FormObservacaoMontagem_btn":{"top":5,"left":816,"width":null,"hidden":0}}'
		);
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormObservacaoMontagem');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001_Observacao_Montagem' => array('name' => 'D001_Observacao_Montagem', 'label'=>'Observação<br />Montagem   ', 'type' => 'textarea'),
					'D049_Percentual_Acrescimo_Custo' => array('name' => 'D049_Percentual_Acrescimo_Custo', 'label'=>'Perc. Acrés. Custo', 'type' => 'moeda', 'reject' => true),
                )
            )
		);
		$formArray['campos']['geral']['cad002FormObservacaoMontagem_btn'] = array('name' => 'cad002FormObservacaoMontagem_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$D049_Percentual_Acrescimo_Custo = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Percentual_Acrescimo_Custo'));
						$CAD002->gravarPercentualAcrescimoCusto($acaoId, $D049_Percentual_Acrescimo_Custo);
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150)
							} </script></div>
EOT;
					}
				} else {
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {}
					
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				
				$sql = mysql_query("SELECT D049_Percentual_Acrescimo_Custo FROM D049 WHERE D049_D001_Id = '{$acaoId}'");
				$res = mysql_fetch_array($sql);
				$form->campoSetarValor('D049_Percentual_Acrescimo_Custo', gCorrigeNumero($res['D049_Percentual_Acrescimo_Custo']));

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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		echo "<div style='height: 1px; border-top: 1px solid silver; margin-top: 150px; width: 100%;'>";
	break;


	case 'cad002FormNuvemShop':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_D001A_Id)) ? $r_D001A_Id : false;
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;
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
		$form_tabela = 'D001A';
		$form_campo_pk = 'D001A_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormNuvemShop_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormNuvemShop/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"D001A_Enviar_Nuvemshop":{"top":6.9921875,"left":7.109375,"width":54,"hidden":0},"cad002FormNuvemShop_btn":{"top":5,"left":703.984375,"width":null,"hidden":0},"cad002FormNuvemShopAtualiza_btn":{"top":27.98828125,"left":703.984375,"width":null,"hidden":1},"D001A_Titulo_Nuvemshop":{"top":52.98828125,"left":37.1875,"width":503.5,"hidden":0},"D001A_Descricao_Nuvemshop_":{"top":75.99609375,"left":16.11328125,"height":130.5,"width":503.5,"hidden":0},"D001A_Produto_NS_Id":{"top":121.9921875,"left":645.72265625,"width":136.5,"hidden":0},"D001A_Variacao_NS_Id":{"top":145,"left":641.4453125,"width":136.5,"hidden":0}}'
		);
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormNuvemShop');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D001A_Titulo_Nuvemshop' => array('name' => 'D001A_Titulo_Nuvemshop', 'label'=>'Titulo Produto', 'type' => 'text', 'uppercase' => false),
					'D001A_Descricao_Nuvemshop_' => array('name' => 'D001A_Descricao_Nuvemshop_', 'label' => 'Descrição Produto', 'type' => 'textarea', 'length' => array(7,75), 'reject' => true, 'uppercase' => false),
					'D001A_Descricao_Nuvemshop' => array('name' => 'D001A_Descricao_Nuvemshop', 'type' => 'hidden', 'uppercase' => false),
					'D001A_D001_Id' => array('name' => 'D001A_D001_Id', 'type' => 'hidden','value' => $D001_Id),
					'D001A_Id' => array('name' => 'D001A_Id', 'type' => 'hidden','value' => $acaoId),					
					'D001A_Enviar_Nuvemshop' => array('name' => 'D001_Enviar_Nuvemshop', 'label'=>'Enviar Nuvemshop ?', 'type' => 'select', 'style'=>'float:right;', 'align'=>'right', 'select' =>
						array(
							array('title' => 'Sim', 'value'=>'S'),
							array('title' => 'Não', 'value' =>'N', 'selected'=>true)
						)
					),
					'D001A_Produto_NS_Id' => array('name' => 'D001A_Produto_NS_Id', 'label'=>'ID Produto NS', 'type' => 'text', 'uppercase' => false),
					'D001A_Variacao_NS_Id' => array('name' => 'D001A_Variacao_NS_Id', 'label'=>'ID Variação NS', 'type' => 'text', 'uppercase' => false),
                )
            )
		);
		$formArray['campos']['geral']['cad002FormNuvemShop_btn'] = array('name' => 'cad002FormNuvemShop_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad002FormNuvemShopAtualiza_btn'] = array('name' => 'cad002FormNuvemShopAtualiza_btn', 'type'=>'button', 'reload' => false, 'value' => 'Enviar Nuvemshop', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var myEditor2 = new nicEditor({
						iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
						buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
					}).panelInstance('D001A_Descricao_Nuvemshop_');
					myEditor2.addEvent('blur', function() {
						if (typeof(myEditor2.instanceById('D001A_Descricao_Nuvemshop_')) != 'undefined') {
							$('#D001A_Descricao_Nuvemshop').val(myEditor2.instanceById('D001A_Descricao_Nuvemshop_').getContent());
						}
					});
					setTimeout(function() {
						$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
						$('.nicEdit-main').height(70).css('overflow', 'auto');
						if (myEditor2.instanceById('D001A_Descricao_Nuvemshop_') != 'undefined') {
							myEditor2.instanceById('D001A_Descricao_Nuvemshop_').setContent($('#D001A_Descricao_Nuvemshop').val());
						}
					}, 100);
			    } </script></div>
EOT;
		
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
						//$D049_Percentual_Acrescimo_Custo = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Percentual_Acrescimo_Custo'));
						//$CAD002->gravarPercentualAcrescimoCusto($acaoId, $D049_Percentual_Acrescimo_Custo);
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150)
							} </script></div>
EOT;
					}
				} else {
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {}
					
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				$form->campoSetarValor('D001A_Descricao_Nuvemshop_', utf8_decode(htmlentities($form->campoValor('D001A_Descricao_Nuvemshop'))));
				// $sql = mysql_query("SELECT D049_Percentual_Acrescimo_Custo FROM D049 WHERE D049_D001_Id = '{$acaoId}'");
				// $res = mysql_fetch_array($sql);
				// $form->campoSetarValor('D049_Percentual_Acrescimo_Custo', gCorrigeNumero($res['D049_Percentual_Acrescimo_Custo']));

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
		//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$gerado = gGeraCamposForm($form, $tab, $D001_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		//echo "<div style='height: 1px; border-top: 1px solid silver; margin-top: 150px; width: 100%;'>";
	break;


	case 'cad002FormAssociarListaFornecedorProduto':

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
		$form_tabela = 'D069';
		$form_campo_pk = 'D069_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormAssociarListaFornecedorProduto_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormAssociarListaFornecedorProduto/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"ProcuraProduto":{"top":7,"left":12.53125,"width":234,"hidden":0},"cad002FormAssociarListaFornecedorProduto_btn":{"top":5,"left":400,"width":null,"hidden":0},"cad002FormIncluirProdutoFornecedor_btn":{"top":5,"left":528,"width":null,"hidden":0},"cad002AtualizarAliquotasProdutos_btn":{"top":5,"left":752,"width":null,"hidden":0},"cad002CalcularTabela_btn":{"top":5,"left":880,"width":null,"hidden":0}}'
		);

		$pesquisaGridExtra = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			WHERE D001_Id IS NULL
			AND D070_C004_Id = '{$g['empresaAtual']}'
			group by D069_Id
EOT;

		// Pesquisa Grid		
		$pesquisaGrid = array(
			'D069',
			'D069_Id',
			'D069_Id||D001_Id||D070_Id||SUBSTR(D024_Nome_Empresa, 1, 30)||D069_Codigo_Produto||D001_Codigo_Produto||D069_Descricao_Produto||D069_Valor_Preco_Original||D069_Valor_Custo_Unitario',
			$pesquisaGridExtra,
			'Pesquisa de Itens na Tabela Fornecedor',
			'Id,Id,Tabela,Fornecedor,Código,Nosso,Produto,Preço FOB,Custo CIF',
			'cad002AssociarProduto',
			$g['divId'],
			$acaoId,
			array(
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width: 1%'),
				array('colunaAlinhar' => 'left', 'style' => 'width: 1%'),
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('style' => 'font-weight:bold'),
				array('colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
				array('colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold')
			),
			'D069_Id||D001_Id||D069_Valor_Preco_Original||D069_Valor_Custo_Unitario||SUBSTR(D024_Nome_Empresa, 1, 30)||D070_Id||D001_Codigo_Produto||D069_Descricao_Produto', // Campos que são ignorados na cláusula where do pesquisaGrid
			false,
			false,
			false, // 13
			'-%'  // 
		);
		
		$form = new Formulario('form_cad002_form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'ProcuraProduto' => array('name' => 'ProcuraProduto' , 'label' => 'Busca Código Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'cad002FormAssociarListaFornecedorProduto_btn' => array('name' => 'cad002FormAssociarListaFornecedorProduto_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					'cad002FormIncluirProdutoFornecedor_btn' => array('name' => 'cad002FormIncluirProdutoFornecedor_btn', 'type'=>'button','reload'=>false,'value'=>'Incluir produto na tabela do fornecedor', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					//'cad002AtualizarAliquotasProdutos_btn' => array('name' => 'cad002AtualizarAliquotasProdutos_btn', 'type'=>'button','reload'=>false,'value'=>'Atualizar Alíquotas', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					//'cad002CalcularTabela_btn' => array('name' => 'cad002CalcularTabela_btn', 'type'=>'button','reload'=>false,'value'=>'Calcular', 'align' => 'right', 'style'=> 'float: right', 'reject'=>true),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'D069_Id' => array('name' => 'D069_Id', 'label' => 'D069_Id', 'type' => 'hidden'),
                ),
				
            ) // 'campos'
		);
		
		$form->definirFormularioArray($formArray);

		$extra = urlencode(base64_encode("WHERE D069_D009_Id='{$r_acaoId}'"));
		$extra_ = base64_encode("WHERE D069_D009_Id='{$r_acaoId}'");
		
		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
				$('#cad002FormIncluirProdutoFornecedor_btn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produto tabela fornecedor', '/cad/cad002/content/cad002ContentIncluirProdutoTabelaFornecedor/', '&D009_Id={$r_acaoId}&divIdRefresh={$r_divForm}', [600,90]);
				});
				$('#cad002AtualizarAliquotasProdutos_btn').unbind('click').bind('click', function(data) {
					var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								var campoNCM = $('#campoNCM').is(":checked") ? "true" : "false";
								var campoOrigemProduto = $('#campoOrigemProduto').is(":checked") ? "true" : "false";
								var campoIPI = $('#campoIPI').is(":checked") ? "true" : "false";
								var campoICMS = $('#campoICMS').is(":checked") ? "true" : "false";
								var campoICMSST = $('#campoICMSST').is(":checked") ? "true" : "false";
								var campoCFOP = $('#campoCFOP').is(":checked") ? "true" : "false";
								var campoNossoCodigo = $('#campoNossoCodigo').is(":checked") ? "true" : "false";
								var arrayOpcoes = {campoNCM: campoNCM,
												   campoOrigemProduto: campoOrigemProduto,
												   campoIPI: campoIPI,
												   campoICMS: campoICMS,
												   campoICMSST: campoICMSST,
												   campoCFOP: campoCFOP,
												   campoNossoCodigo: campoNossoCodigo};
								arrayOpcoes = JSON.stringify(arrayOpcoes);
								$.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&opcoes=' + encodeURIComponent(arrayOpcoes) + '&sql=' + encodeURIComponent("{$extra}") + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
									divRefresh('{$r_divForm}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						var dialogOptions = dialogConfirm('Atualizar Alíquotas', 'Atualizar as aliquotas igual ao cadastro de produtos: <br />', \$buttons);
						$('<input type="checkbox" checked="checked">').attr('id', 'campoCFOP').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelCFOP').html('CFOP').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox">').attr('id', 'campoNCM').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelNCM').html('NCM').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox">').attr('id', 'campoOrigemProduto').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelOrigemProduto').html('Origem Produto').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox">').attr('id', 'campoIPI').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelIPI').html('Aliquota IPI').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox">').attr('id', 'campoICMS').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelICMS').html('Aliquota ICMS').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox">').attr('id', 'campoICMSST').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelICMSST').html('Aliquota ST').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						return false;
				});

				$('#cad002CalcularTabela_btn').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/cad/cad002/grid_func-ajax/processarD069/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Ok', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Confirma a atualização dos produtos?', \$buttons);
				});
			} </script></div>
EOT;
		
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
				$retorno = $CAD002->associarProdutoListaFornecedor($form);
				if ($retorno === true) {
					$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							//fecharJanela("{$g['divId']}");
							divRefresh('{$g['divId']}', true);
							divRefresh('{$r_divForm}', true);
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
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'cad002formCadastroFornecedorProduto':
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
		$form_tabela = 'T241';
		$form_campo_pk = 'T241_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002formCadastroFornecedorProduto_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002formCadastroFornecedorProduto/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"T241_D024_Id":{"top":7,"left":5,"width":423,"hidden":0}}'
		);
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002formCadastroFornecedorProduto');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T241_D024_Id' => array('name' => 'T241_D024_Id', 'label' => 'Fornecedor', 'type' => 'pesquisa', 'pesquisa' =>
						array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - (%4%5) -  [%2/%3]')
					),
					'T241_D001_Id' => array('name' => 'T241_D001_Id', 'type' => 'hidden', 'value' => $r_D001_Id),
					'T241_Data' => array('name' => 'T241_Data', 'type' => 'hidden', 'value' => date("Y-m-d")),
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150)
							} </script></div>
EOT;
					}
				} else {
					$D024_Id = $form->campoValorEnviado('T241_D024_Id');
					$D001_Id = $form->campoValorEnviado('T241_D001_Id');
					$sql = mysql_query("SELECT * FROM T241 WHERE T241_D024_Id = '{$D024_Id}' AND T241_D001_Id = '{$D001_Id}'");
					if(mysql_num_rows($sql) > 0){
						echo gGeraAlertMsg('Erro', 'Fornecedor já cadastrado.');
					} else {
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Fornecedor incluído com sucesso!', false);
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									setTimeout(function() {
										{$alert}
									}, 150)
									divRefresh('{$g['divIdParent']}');
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
		echo  $g['smarty']->fetch('formEditor.tpl');

		echo "<div style='height: 1px; border-top: 1px solid silver; margin-top: 150px; width: 100%;'>";
	break;

	case 'cad002FormIncluirProdutoFornecedor':

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
		$form_tabela = 'D069';
		$form_campo_pk = 'D069_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormIncluirProdutoFornecedor_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormIncluirProdutoFornecedor/';
		// Form Editor
		$form_editor = array(
			'geral' => '{"ProcuraProduto":{"top":7,"left":13,"width":314,"hidden":0},"cad002FormAssociarListaFornecedorProduto_btn":{"top":5,"left":496,"width":null,"hidden":0}}'
		);

		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			AND D009_C004_Id = '{$g['empresaAtual']}'
			group by D001_Id
EOT;

		// Pesquisa Grid		
		$pesquisaGrid = array(
			'D009',
			'D009_Id',
			'D009_Id||D001_Id||D001_Codigo_Produto||D001_Descricao_Produto',
			$pesquisaGridExtra,
			'Pesquisa de produtos',
			'Id,Id,Código,Produto',
			'cad002IncluirProdutoFornecedor',
			$g['divId'],
			$acaoId,
			array(
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width: 1%'),
				array('colunaAlinhar' => 'left', 'style' => 'width: 1%'),
			),
			'D009_Id||D001_Id' // Campos que são ignorados na cláusula where do pesquisaGrid
		);
		
		$form = new Formulario('cad002FormIncluirProdutoFornecedor');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'ProcuraProduto' => array('name' => 'ProcuraProduto' , 'label' => 'Busca Tabela Fornecedor', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'D069_D009_Id' => array('name' => 'D069_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'D069_Codigo_Produto' => array('name' => 'D069_Codigo_Produto', 'label' => 'Código Produto', 'type' => 'hidden'),
					'D069_Descricao_Produto' => array('name' => 'D069_Descricao_Produto', 'label' => 'Descrição Produto', 'type' => 'hidden'),
					'D069_D070_Id' => array('name' => 'D069_D070_Id', 'label' => 'D069_D070_Id', 'type' => 'hidden', 'value' => @$r_D070_Id),
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
				$D009_Id = $form->campoValorEnviado('D069_D009_Id');
				$D070_Id = $form->campoValorEnviado('D069_D070_Id');
				$sql = mysql_query("SELECT D069_Id FROM D069 WHERE D069_D009_Id = '{$D009_Id}' AND D069_D070_Id = '{$D070_Id}'");
				if(mysql_num_rows($sql) > 0){
					echo gGeraAlertMsg('Erro', 'Produto já cadastrado para este fornecedor!');
				} else {
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$opcoes = array('campoNCM' => 'true', 'campoIPI' => 'true', 'campoNossoCodigo' => 'true', 'campoOrigemProduto' => 'true', 'campoCFOP' => 'true', 'campoICMS' => 'true', 'campoICMSST' => 'true');
						$retorno = $CAD002->atualizarAliquotasProdutos("WHERE D069_Id = '{$g['mysqlLastId']}'", $opcoes);
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Produto incluído com sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150)
								divRefresh('{$g['divIdParent']}');
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
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
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'cad002FormIncluirProdutoTabelaFornecedor':
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

		$sqlD001 = mysql_query("SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id = '{$r_D009_Id}'");		
		$rowD001 = mysql_fetch_array($sqlD001);

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D069';
		$form_campo_pk = 'D069_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormIncluirProdutoTabelaFornecedor_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormIncluirProdutoTabelaFornecedor/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"D069_D070_Id":{"top":7,"left":14,"width":434,"hidden":0}}'
		);
		//$form_editor_pos = '';
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormIncluirProdutoTabelaFornecedor');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D069_D070_Id' => array('name' => 'D069_D070_Id', 'label' => 'Tabela Fornecedor', 'type' => 'pesquisa', 'pesquisa' =>
						array('D070','D070_Id','D070_Id,D024_Nome_Empresa','left join D024 on D024_Id=D070_D024_Id order by D070_Id','%1 - %2')
					),
					'D069_D009_Id' => array('name' => 'D069_D009_Id', 'type' => 'hidden', 'value' => $r_D009_Id),
					'D069_Codigo_Produto' => array('name' => 'D069_Codigo_Produto', 'type' => 'hidden', 'value' => $rowD001['D001_Codigo_Produto']),
					'D069_Descricao_Produto' => array('name' => 'D069_Descricao_Produto', 'type' => 'hidden', 'value' => $rowD001['D001_Descricao_Produto']),
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150)
							} </script></div>
EOT;
					}
				} else {
					$D070_Id = $form->campoValorEnviado('D069_D070_Id');
					$D009_Id = $form->campoValorEnviado('D069_D009_Id');
					$sql = mysql_query("SELECT D069_Id FROM D069 WHERE D069_D070_Id = '{$D070_Id}' AND D069_D009_Id = '{$D009_Id}'");
					if(mysql_num_rows($sql) > 0){
						echo gGeraAlertMsg('Erro', 'Produto já cadastrado.');
					}elseif(empty($D070_Id)){
						echo gGeraAlertMsg('Erro', 'É necessário selecionar uma tabela.');
					} else {
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							$opcoes = array('campoNCM' => 'true', 'campoIPI' => 'true', 'campoNossoCodigo' => 'true', 'campoOrigemProduto' => 'true', 'campoCFOP' => 'true', 'campoICMS' => 'true', 'campoICMSST' => 'true');
							$CAD002->atualizarAliquotasProdutos("WHERE D069_Id = '{$g['mysqlLastId']}'", $opcoes);
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Fornecedor incluído com sucesso!', false);
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									setTimeout(function() {
										{$alert}
									}, 150)
									divRefresh('{$g['divIdParent']}');
									divRefresh('{$r_divIdRefresh}');
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
		echo  $g['smarty']->fetch('formEditor.tpl');

		echo "<div style='height: 1px; border-top: 1px solid silver; margin-top: 150px; width: 100%;'>";
	break;

	case 'cad002FormIncluirSimilar':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;

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
		$form_tabela = 'D156';
		$form_campo_pk = 'D156_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormIncluirSimilar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormIncluirSimilar/';

		// novo formulário
		$form = new Formulario('cad002FormIncluirSimilar');
		// Editor
		$form_editor = array(
			'geral' => '{"D156_D001_Id_2":{"top":7,"left":37,"width":297,"hidden":0},"D156_Observacao":{"top":30,"left":13,"height":39,"width":312,"hidden":0}}',
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D156_D001_Id_2' => array('name' => 'D156_D001_Id_2', 'label'=>'Produto', 'type' => 'pesquisa',
						'pesquisa' => array('D001','D001_Id','D001_Codigo_Produto,D001_Descricao_Produto',""),
					),
					'D156_Observacao' => array('name' => 'D156_Observacao', 'label'=>'Observação', 'type' => 'textarea'),
					'D156_D001_Id_1' => array('name' => 'D156_D001_Id_1', 'label'=>'Produto', 'type' => 'hidden', 'value'=>$D001_Id),
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
					$D156_D001_Id_2 = $form->campoValorEnviado('D156_D001_Id_2');
					$msg='';
					if($D156_D001_Id_2==$D001_Id){
						$msg = gGeraAlertMsg('Erro!', 'Não é possível incluir o produto igual ao produto origem.');	
					}
					if($msg !=''){
						echo $msg;
					}else{
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											//divRefresh('{$g['divId']}');
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
					$D156_D001_Id_2 = $form->campoValorEnviado('D156_D001_Id_2');
					$msg='';
					$D156 = mysql_query("SELECT D156_Id FROM D156 WHERE D156_D001_Id_2='{$D156_D001_Id_2}' AND D156_D001_Id_1='{$D001_Id}'");
					$row = mysql_num_rows($D156);
					if($row > 0){
						$msg = gGeraAlertMsg('Erro!', 'Não é possível incluir o mesmo produto duas vezes.');
					}
					if($D156_D001_Id_2==$D001_Id){
						$msg = gGeraAlertMsg('Erro!', 'Não é possível incluir o produto igual ao produto origem.');	
					}
					if($msg !=''){
						echo $msg;
					}else{
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											//divRefresh('{$g['divId']}');
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
		$botoesDir .= '<button type="submit" id="cad002FormIncluirSimilar_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002formCadastroFornecedorLote':
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
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002formCadastroFornecedorLote_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002formCadastroFornecedorLote/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"T241_D024_Id":{"top":7,"left":5,"width":423,"hidden":0}}'
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002formCadastroFornecedorLote');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T241_D024_Id' => array('name' => 'T241_D024_Id', 'label' => 'Fornecedor', 'reject' => true, 'type' => 'pesquisa', 'pesquisa' =>
						array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - (%4%5) -  [%2/%3]')
					),
					'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => base64_encode($r_sql)),
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
				$retorno = $CAD002->cadastrarFornecedorLote($form->campoValorEnviado('T241_D024_Id'), $form->campoValorEnviado('extra'));
				if($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);
				} else {
					$alert = gGeraAlertMsg('Operação OK', 'Fornecedor incluído com sucesso!', false);
					echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							setTimeout(function() {
								{$alert}
							}, 150)
							divRefresh('{$g['divIdParent']}');
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		echo "<div style='height: 1px; border-top: 1px solid silver; margin-top: 150px; width: 100%;'>";
	break;

	case 'cad002FormJuntarLocacao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;

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
		$form_tabela = 'D001';
		$form_campo_pk = 'D001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormBtnJuntarLocacao';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormJuntarLocacao/';
        
        // form editor
        $form_editor = array(
            'geral' => '{"T066_Id_Antigo":{"top":7,"left":32,"width":265,"hidden":0},"T066_Id_Novo":{"top":30,"left":36,"width":265,"hidden":0},"TextoAviso":{"top":53,"left":12,"width":null,"hidden":0}}',
        );

		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formJuntarLocacaocad002Form');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T066_Id_Antigo' => array('name' => 'T066_Id_Antigo', 'label' => 'Locação Antiga', 'type' => 'pesquisa', 'reject' => true, 'pesquisa' => array('T066','T066_Id','T066_Id,D004_Local','LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '.$D009_Id)),
					'T066_Id_Novo' => array('name' => 'T066_Id_Novo', 'label' => 'Locação Nova', 'type' => 'pesquisa', 'reject' => true, 'pesquisa' => array('T066','T066_Id','T066_Id,D004_Local','LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '.$D009_Id)),
					'TextoAviso' => array('name' => 'TextoAviso', 'label'=>'', 'type' => 'comentario', 'value' => '* Este processo irá juntar a locação <b>Antiga</b> com o <b>Nova</b>.', 'reject' => true),
				),
			) // 'campos'
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
				set_time_limit(0);
				$retorno = $CAD002->juntarlocacao($form,$D009_Id);
				if ($retorno === true) {
					$alert .= gGeraAlertMsg('OK', 'As locações foram juntadas com sucesso!', false);
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
				} else {
					if (strpos($retorno, 'BLOQUEADO') !== false) {
						$retorno = explode('-', $retorno);
						$retorno = "O Produto Antigo possui registros em '{$retorno[1]}' e não pode ser juntado.";
					}
					echo gGeraAlertMsg('Ocorreu um Erro', $retorno);
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
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormBtnJuntarLocacao">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormImportarCSVFornecedorProduto':
	
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
		$form_tabela = 'T241';
		$form_campo_pk = 'T241';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormImportarCSVFornecedorProduto_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormImportarCSVFornecedorProduto/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad002FormImportarCSVFornecedorProduto');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCAD002'] 							= array('name' => 'uploadArquivoCSVCAD002', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['cad002FormImportarCSVFornecedorProduto_btn'] 		= array('name' => 'cad002FormImportarCSVFornecedorProduto_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T241_Arquivo_Csv'] 									= array('name' => 'T241_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 											= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT
					<div class=""></div>
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCAD002');
				$retorno = $CAD002->importarCSVFornecedorProduto($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
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

	
	case 'cad002FormIncluirRegradeConversao':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$D001_Id = (isset($r_D001_Id)) ? $r_D001_Id : false;

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
		$form_tabela = 'D184';
		$form_campo_pk = 'D184_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormIncluirRegradeConversao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormIncluirRegradeConversao/';

		// novo formulário
		$form = new Formulario('cad002FormIncluirRegradeConversao');
		// Editor
		$form_editor = array(
			'geral' => '{"D184_D037_Id":{"top":7,"left":101.375,"width":127,"hidden":0},"D184_Tipo_Operacao":{"top":30,"left":83.140625,"width":127,"hidden":0},"D184_Quantidade_Conversao":{"top":53,"left":28.71875,"width":56,"hidden":0}}',
		);

		$formArray = array(
			'campos' => array(
				'geral' => array(				
					'D184_D037_Id' => array('name' => 'D184_D037_Id', 'label'=>'Unidades', 'type' => 'select','select' =>
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true)
					),
					'D184_Quantidade_Conversao' => array('name' => 'D184_Quantidade_Conversao', 'label' => 'Quantidade de Conversão', 'type' => 'moeda'),
					'D184_Tipo_Operacao' => array(
						'name' => 'D184_Tipo_Operacao', 'label' => 'Tipo Operação', 'type' => 'select',
						'select' => array(							
							array('title' => 'MULTIPLICAÇÃO', 'value' => 'M', 'select' => true),
							array('title' => 'DIVISÃO', 'value' => 'D'),
						)
					),
					'D184_D001_Id' => array('name' => 'D184_D001_Id', 'label' => 'Produto', 'type' => 'hidden', 'value' => $D001_Id),
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {									
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');								
									divRefresh('{$g['divIdParent']}', true);								
							} </script></div>
EOT;
					}
				} else {

					// Insert													 
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$acaoId = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
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
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) {
			$g["smarty"]->assign("formId", $formId);
		}
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormIncluirRegradeConversao_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;

	case 'cad002FormECommerceTray':

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D185_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D185';
		$form_campo_pk = 'D185_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormECommerceTrayBtnGravar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormECommerceTray/';

		$form_editor = array(
			'geral' => '{"D185_Codigo":{"top":7,"left":71.84375,"width":104,"hidden":0},"D185_Descricao":{"top":30,"left":57.71875,"width":648,"hidden":0},"D185_Comprimento":{"top":52.984375,"left":39.671875,"width":103,"hidden":0},"D185_Altura":{"top":53,"left":333.609375,"width":103,"hidden":0},"D185_Largura":{"top":53,"left":613.359375,"width":105,"hidden":0},"D185_Descricao_Detalhada":{"top":75.984375,"left":5.484375,"height":292,"width":648,"hidden":0}}'
		);

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D185_Codigo' => array('name' => 'D185_Codigo', 'label' => 'Código', 'type' => 'text'),
					'D185_Descricao' => array('name' => 'D185_Descricao', 'label' => 'Descrição', 'type' => 'text', 'uppercase' => false),
                	'D185_Comprimento' => array('name' => 'D185_Comprimento', 'label' => 'Comprimento', 'type' => 'moeda', 'casasDecimais' => 2,'align' => 'right'),
                	'D185_Altura' => array('name' => 'D185_Altura', 'label' => 'Altura', 'type' => 'moeda', 'casasDecimais' => 2,'align' => 'right'),
                	'D185_Largura' => array('name' => 'D185_Largura', 'label' => 'Largura', 'type' => 'moeda', 'casasDecimais' => 2,'align' => 'right'),
					'D185_Descricao_Detalhada' => array('name' => 'D185_Descricao_Detalhada', 'label' => 'Descrição Detalhada', 'uppercase' => false, 'type' => 'textarea', 'length' => array(4, 61)),
					'D185_Integrado_Tray' => array ('name' => 'D185_Integrado_Tray', 'label' => 'Integrado Tray', 'type' => 'hidden', 'value' => 'N')
				),
				
            ) // 'campos'
		);
		
		$form = new Formulario('cad002FormECommerceTray');
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
					$form->campoSetarValor('D185_Integrado_Tray', 'N', true);
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
		$g['smarty']->assign('table', $gerado['table']); 
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormECommerceTrayBtnGravar">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002FormAtributosECommerceTray':

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$D185_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
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
		$form_tabela = 'D186';
		$form_campo_pk = 'D186_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad002FormAtributosECommerceTrayBtnGravar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad002/form/cad002FormAtributosECommerceTray/';

		$form_editor = array(
			'geral' => '{"D186_D001_Id":{"top":7,"left":36.75,"width":442,"hidden":0},"D186_Atributo_Nome":{"top":30,"left":45.015625,"width":456,"hidden":0},"D186_Atributo_Valor":{"top":53,"left":49.203125,"width":456,"hidden":0}}'
		);

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D186_D185_Id' => array('name' => 'D186_D185_Id', 'label' => 'Código', 'value' => "$r_D185_Id", 'type' => 'hidden'),

					'D186_D001_Id' => array('name' => 'D186_D001_Id', 'label'=>'Produto', 'type' => 'pesquisa',
						'pesquisa' => array('D001','D001_Id','D001_Codigo_Produto,D001_Descricao_Produto',""),
					),

					'D186_Atributo_Nome' => array('name' => 'D186_Atributo_Nome', 'label' => 'Nome', 'type' => 'text', 'uppercase' => false),
					'D186_Atributo_Valor' => array('name' => 'D186_Atributo_Valor', 'label' => 'Valor', 'type' => 'text', 'uppercase' => false),
					'D186_Integrado_Tray' => array ('name' => 'D186_Integrado_Tray', 'label' => 'Integrado Tray', 'type' => 'hidden', 'value' => 'N')
				),
				
            ) // 'campos'
		);
		
		$form = new Formulario('cad002FormAtributosECommerceTray');
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
					$form->campoSetarValor('D186_Integrado_Tray', 'N', true);
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
		$g['smarty']->assign('table', $gerado['table']); 
		$g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad002FormAtributosECommerceTrayBtnGravar">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

}
