<?php
namespace hardness;

require_once('grid_func-callback.php');
require_once('grid_func-js.php');
require_once('prog/ven/ven002/form_func-js.php');
require_once('prog/ven/ven001/form_func-js.php');

$_REQUEST['idTabForm4'] = isset($_REQUEST['idTabForm4']) ? $_REQUEST['idTabForm4'] : false;

switch($g['r'][3]){
	case 'lista':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		$C008_Id = $r_acaoId;
		$exibirValidade = ($r_validadeProduto == 'S') ? true : false; // vem das notificaoes validadeProduto
		
		/**
		 * Definição do grid tabela


		 */
		//configuração global de separar produto por empresa
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}

		$exibirEstoqueTray = ($g["C031"]["plataformaECommerce"] == "tray" || $g["C031"]["plataformaECommerce2"] == "tray") ? true : false;

	
		$gridModelos = array(
    		'e-Commerce' => 'e-Commerce'
    	);

		/**
		* Definição do Grid (Tabela)
		*/
		if (!modoModelo('e-Commerce')) {

			$grid = array(
				array('colunaValor' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'colunaExibirNivel' => 4, 'colunaExibir' => false, 'callbackParameter' => array('D049_Id', $g['divId'])),
				array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 650), 'style' => 'width: 1%;', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => false, 'colunaExibir' => false, 'chavePrimaria' => true),
				array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
				array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2, 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
				array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
				array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'ABC(V)', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto (*Dados processados com base nos últimos 12 meses)', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '800')),
				array('colunaValor' => 'D049_Flag_Curva_ABC_Qtd', 'colunaTitulo' => 'ABC(Q)', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto (*Dados processados com base nos últimos 12 meses)', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '800')),
				array('colunaValor' => 'D001_Codigo_Barra1', 'colunaTitulo' => 'EAN Fab', 'style' => 'width:1%', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Modelo', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referência', 'colunaEditar' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'ifnull(D069_D070_Id,"_")', 'colunaTitulo' => 'Tab', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2, 'callback' => 'abrirJanela', 'callbackParameter' => array("Cadastro Produto", "/cad/cad002/content/form2/", "D001_Id", "1000,600", false, false, "&abrirTab=tabela", false)),
				array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
				array('colunaValor' => '(select group_concat(concat(DATE_FORMAT(T066A_Data_Validade,"%d/%m/%Y"),\' - \',D004_Local) SEPARATOR \'<br>\') from D004 left join T066 on T066_D004_Id=D004_Id left join T066A on T066_Id = T066A_T066_Id where T066_D009_Id=D009_Id and T066_Quantidade_Estoque > 0 AND T066A_Data_Validade > "0000-00-00") as validades', 'colunaTitulo' => 'Validade', 'colunaExibir' => $exibirValidade,'style' => 'width:1%;color: var(--BGVermelho)'),
				array('colunaValor' => 'D049_Codigo_Produto', 'colunaTitulo' => 'Descrição inglês', 'colunaExibir' => false),
				array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Depto', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
				array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
				array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
				array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Sub-Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
				array('colunaValor' => 'D049_D082_Id', 'colunaTitulo' => 'Marca', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D082_Marca', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('D082', 'D082_Id', 'D082_Marca', "ORDER BY D082_Marca", true)
				),
				array('colunaValor' => 'substr(D082_Marca,1,15) as D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
				array('colunaValor' => 'D049_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarNivel' => 2, 'style' => 'width:1%'),
				array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as NCM', 'colunaTitulo' => 'NCM', 'colunaExibir' => false),
				array('colunaValor' => 'D001_D005_Id', 'colunaTitulo' => 'NCM', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'NCM', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D005_Observacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
				),
				array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => false),
				array('colunaValor' => 'D001_D037_Id', 'colunaTitulo' => 'Un', 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D037_Unidade', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('D037', 'D037_Id', "D037_Unidade", "ORDER BY D037_Unidade", true)
				),
				array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
				array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
				array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
				array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'colunaAlinhar' => 'right', 'callback' => 'estoqueFora'),
				array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
				array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
				array('colunaValor' => 'D009_Quantidade_OP', 'colunaTitulo' => 'OP', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', 'D009_Id', '700,400', true), 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
				array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => 4, 'colunaExibir' => false),
				array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Entrada', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
				array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'Ult.Saída', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
				array('colunaValor' => "if(D149_Flag_ST='S','ST','') as ST", 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => "(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaExibir' => false),
				array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Último Fornecedor', 'colunaExibir' => false),
				array('colunaValor' => 'C004_Nome_Empresa', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false),
				array('colunaValor' => 'D001_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false),
				array('colunaValor' => 'D009_IPV_1', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right', 'colunaEditarNivel' => 4, 'colunaExibirNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'colunaExibir' => false),
				array('colunaValor' => 'D009_C007_Id_Comprador', 'colunaTitulo' => 'Comprador', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('C007', 'C007_Id', "C007_Nome", "ORDER BY C007_Nome", true)
				),
				
				array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
				array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
				array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
				
				array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Depto','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Id, C008_Tipo', "ORDER BY C008_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraDepartamento', 'callbackParameter'=>'C008_Tipo', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'D001_D060_Id', 'colunaTitulo' => 'Setor','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', "ORDER BY D060_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraSetor', 'callbackParameter'=>'D060_Descricao_Grupo', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'D001_D003_Id', 'colunaTitulo' => 'Grupo','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D003', 'D003_Id', 'D003_Id, D003_Nome_Linha', "ORDER BY D003_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraGrupo', 'callbackParameter'=>'D003_Nome_Linha', 'colunaEditarTodos'=>true),
				array('colunaValor' => 'D001_D002_Id', 'colunaTitulo' => 'Sub-Grupo','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D002', 'D002_Id', 'D002_Id, D002_Descricao_Produto', "ORDER BY D002_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraSubGrupo', 'callbackParameter'=>'D002_Descricao_Produto', 'colunaEditarTodos'=>true),

				array('colunaValor' => 'D049_D024_Id', 'colunaTitulo' => 'Fornecedor', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'D024_Nome_Empresa', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('D024', 'D024_Id', "D024_Nome_Empresa,D024_Id", "WHERE D024_Flag_Fornecedor='S' ORDER BY D024_Nome_Empresa", true)
				),
				array('colunaValor' => 'D001_C004_Id', 'colunaExibir' => false,'colunaTitulo' => 'Empresa', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'campoMostraValor', 'callbackParameter' => 'C004_Nome_Empresa', 'colunaEditarNivel' => 2,
					'colunaEditarGeraSelect' => array('C004', 'C004_Id', "C004_Nome_Empresa,C004_Id", "ORDER BY C004_Nome_Empresa", true)
				),
				array('colunaValor' => 'D009_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarNivel' => 2, 'style' => 'width:1%', 'colunaExibir'=>false),
				array('colunaValor' => 'D001_Flag_Ecommerce', 'colunaTitulo' => 'E-co', 'callback' => 'gCheckbox','callbackParameter' => array('D001_Flag_Ecommerce', 'N|S') , 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%','colunaExibir' => false),
				array('colunaValor' => 'D001_Flag_Etiqueta_Pedido', 'colunaTitulo' => 'Etq.Pedido', 'callback' => 'gCheckbox','callbackParameter' => array('D001_Flag_Etiqueta_Pedido', 'N|S') , 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%','colunaExibir' => false),
				array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde Embalagem', 'colunaAlinhar' => 'right', 'colunaEditarTodos' => true, 'colunaExibir' => false, 'colunaEditar' => true),
				array('colunaValor' => 'D049_Flag_Nao_Comprar', 'colunaTitulo' => 'Comprar?', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),  
				array('colunaValor' => 'D001_Flag_Tipo_Item', 'colunaTitulo' => 'Categoria', 'colunaExibir' => true, 'colunaAlinhar' => 'center'),  
				array('colunaValor' => 'D009A_Id', 'colunaTitulo' => 'D009A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'D009A_Ficha_Conteudo_Importacao', 'colunaTitulo' => 'Número FCI', 'colunaExibir' => false, 'colunaEditar' => true, 'colunaAlinhar' => 'left'),  
				array('colunaValor' => 'D001A_Id', 'colunaTitulo' => 'D001A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaValor' => 'D001A_Quantidade_Estoque_Tray', 'colunaTitulo' => 'Estoque Tray', 'colunaExibir' => $exibirEstoqueTray, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'right'),  
			);

		} else {

			$grid = array(
				array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
				array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => false, 'colunaExibir' => false, 'chavePrimaria' => true),
				array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
				array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
				array('colunaValor' => 'D001_Codigo_Barra1', 'colunaTitulo' => 'EAN Fab', 'style' => 'width:1%', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => false),
				array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
				array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
				array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
				array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde Embalagem', 'colunaAlinhar' => 'right', 'colunaEditarTodos' => true, 'colunaExibir' => false, 'colunaEditar' => true),
			);	
			
		}
		$from = 'D001';
		$extra = <<<EOT
		-- JOIN
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D049_D024_Id=D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			left join C004 on C004_Id=D001_C004_Id
			left join C007 on C007_Id=D009_C007_Id_Comprador
			left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			left join D069 on D069_D009_Id=D009_Id and D069_Flag_Tabela_Principal='S'
			left join D070 on D070_Id=D069_D070_Id
			left join D001A on D001A_D001_Id=D001_Id
			left join D009A on D009A_D009_Id=D009_Id
		-- WHERE
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
		-- GROUP BY
			GROUP BY D009_Id
EOT;
	
		$extraTotal = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join D037 on D001_D037_Id=D037_Id
			left join D005 on D001_D005_Id=D005_Id
			left join D072 on D072_D001_Id=D001_Id
			left join C007 on C007_Id=D009_C007_Id_Comprador
			left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			left join D069 on D069_D009_Id=D009_Id and D069_Flag_Tabela_Principal='S'
			left join D070 on D070_Id=D069_D070_Id
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			{$filtro}
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)

EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
		       return 'linhaCinza';
		    }
		    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
		       return 'linhaVermelho';
			}
			if ($array['todosCampos']['D049_Flag_Nao_Comprar']=='S') {
				return 'linhaError';
			 }
		}	

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            "ifnull((select group_concat(distinct D083_Codigo_Produto_Fornecedor separator ' ') from D083 where D083_D001_Id=D001_Id)[[VIRGULA]]'')" => array('titulo' => 'Referência', 'tipo' => 'texto'),
            "concat(ifnull(D001_Codigo_Barras[[VIRGULA]]'')[[VIRGULA]]' '[[VIRGULA]]ifnull(D001_Codigo_Barra1[[VIRGULA]]'')[[VIRGULA]]' '[[VIRGULA]]ifnull((select group_concat(distinct D083_Codigo_Barras separator ' ') from D083 where D083_D001_Id=D001_Id)[[VIRGULA]]''))" => array('titulo' => 'Cód. Barras', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad023/grid/cad023GridTipos/', 'campo' => 'C008_Tipo', 'tamanho' => 20)),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad027/grid/cad027GridGrupos/', 'campo' => 'D060_Descricao_Grupo', 'tamanho' => 20)),
            'D003_Id,D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad026/grid/cad026GridLinhas/', 'campo' => 'D003_Nome_Linha', 'tamanho' => 20)),
            'D002_Id,D002_Descricao_Produto' => array('titulo' => 'Sub-grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad028/grid/cad028GridSubGrupos/', 'campo' => 'D002_Descricao_Produto', 'tamanho' => 20)),
            "ifnull((select group_concat(distinct D004_Local separator ' ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id)[[VIRGULA]]'')" => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.loja', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde.OC', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Data_Ultima_Entrada' => array('titulo' => 'Ult.Compra', 'tipo' => 'data'),
            'D009_Data_Alteracao_Custo' => array('titulo' => 'Alt.Custo', 'tipo' => 'data'),
            'D009_Data_Ultima_Venda' => array('titulo' => 'Ult.Venda', 'tipo' => 'data'),
            'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto', 'exibir' => false),
            'D049_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Origem', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
            'D009_Valor_Custo_Unitario' => array('titulo' => 'Custo R$', 'tipo' => 'numero'),
            'D049_Flag_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto'),
			'D049_Flag_Nao_Comprar' => array('titulo' => 'Comprar?', 'tipo' => 'select', 'select' =>
			array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),  
				array('title' => 'Sim', 'value' => 'N',), 
				array('title' => 'Não', 'value' => 'S',), 
				)
			), 
			'D070_Data_Vencimento' => array ('titulo' => 'Validade Tabela', 'tipo' => 'data'),
			'D049_Data_Cadastro' => array ('titulo' => 'Cadastro', 'tipo' => 'data'),
            'D001_Flag_Tipo_Item' => array('titulo' => 'Categoria', 'tipo' => 'texto'),
			'D009A_Ficha_Conteudo_Importacao' => array('titulo' => 'Número FCI', 'tipo' => 'texto', 'exibir' => false),
	    );
	 	// Gera o filtro e acrescenta ao $extra
		$g['cacheCount'] = true;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(distinct D009_Id)", $from, $extraTotal);
		$etapas = round($valorTotal / 1) ; 
		// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		
		/**
		* Geração: Monta o SQL e retorna os dados
		*/
		$geradoSql = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100, false, false, true);
		unset($g['cacheCount']);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra_ = base64_encode($extra);

		$EnderecoHTTP = $_SERVER['HTTP_HOST'];

		/*$incluirProduto = '';
		if(!isset($g['c029Ids'][186])){
			$incluirProduto = 'disabled';
		}
		
		echo <<<EOT
		    <div class="buttonsBottomTop">
		        <button type="button" id="cad002Incluir" {$incluirProduto}>Incluir</button>
		        <button type="button" id="cad002Juntar" {$incluirProduto}>Juntar</button>
				<button type="button" id="cad002AtualizarEstoqueGeral">Atualizar Estoque</button>
				<button type="button" id="cad002GridAtualizarEstoqueRapido">Atualizar Estoque Rápido</button>
				<!--<button type="button" id="cad002MeuBotao">Meu Botão</button>-->
		 		<!--<button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>-->
		    	<!--<button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
EOT;
				if($g['C031']['plataformaECommerce']=='magento1' && $g['C031']['HabilitarAtualizacaoEstoque']=='S'){
					echo <<<EOT
						<button type="button" id="cad002EstoqueMagento" style="padding-left:5px;">Atualizar Estoque Magento</button>
EOT;
				}

				if($g['C031']['plataformaECommerce']=='tray' || $g['C031']['plataformaECommerce2']=='tray'){
					echo <<<EOT
						<button type="button" id="cad002EstoqueTray" style="padding-left:5px;">Atualizar Estoque Tray</button>
EOT;
				}
*/

		// Botões 
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad002Incluir',  'exibirCallback' => 'return isset($g["c029Ids"][186]);'),
			array('titulo' => 'Juntar', 'id' => 'cad002Juntar',  'exibirCallback' => 'return isset($g["c029Ids"][186]);'),
			array('titulo' => 'Atualizar Estoque', 'id' => 'cad002AtualizarEstoqueGeral'),
			array('titulo' => 'Atualizar Estoque Rápido', 'id' => 'cad002GridAtualizarEstoqueRapido'),
			array('titulo' => 'Atualizar Estoque Tray', 'id' => 'cad002EstoqueTray',  'exibirCallback' => 'return $g["C031"]["plataformaECommerce"]=="tray" || $g["C031"]["plataformaECommerce2"]=="tray";'),
			array('titulo' => 'Atualizar Nuvemshop', 'id' => 'cad002ProdutosNuvemshop',  'exibirCallback' => 'return $g["C031"]["plataformaECommerce"]=="nuvemshop" || $g["C031"]["plataformaECommerce2"]=="nuvemshop";'),
		);
		echo Botoes::processar($botoes);

			echo <<<EOT
			</div>
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#cad002AtualizarEstoqueGeral').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$('#{$g['divId']}').showLoading();
							cad002AtualizarEstoqueGeralLoop(idProgressBar,1,0,0);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		        });
		        function cad002AtualizarEstoqueGeralLoop(idProgressBar, etapa, erro, ok){
                    $.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueGeral/?extraSql=' + encodeURIComponent('{$extra_}') + '&ajax=true&callback=?'+'&valorTotal=$valorTotal&etapas=$etapas&etapa=' + etapa, function(request) {
                        if (request.etapa<=$etapas && request.concluido == 0) {
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                        	cad002AtualizarEstoqueGeralLoop(idProgressBar,request.etapa, erro, ok);
                        }else{
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                        	dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                        	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                        } else {
	                            dialogConfirm('Erro!', request.data);
	                        }
                        } 
	                });	                    
				}
				
				$('#cad002GridAtualizarEstoqueRapido').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$('#{$g['divId']}').showLoading();
							cad002AtualizarEstoqueRapidoLoop(idProgressBar,1,0,0);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
				});
				function cad002AtualizarEstoqueRapidoLoop(idProgressBar, etapa, erro, ok){
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueRapido/?extraSql=' + encodeURIComponent('{$extra_}') +'&ajax=true&callback=?'+'&valorTotal=$valorTotal&etapas=$etapas&etapa=' + etapa, function(request) {
						if (request.etapa<=$etapas && request.concluido == 0) {
							erro += request.erro;
							ok += request.ok;
							$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
							cad002AtualizarEstoqueRapidoLoop(idProgressBar,request.etapa, erro, ok);
						}else{
							erro += request.erro;
							ok += request.ok;
							$('#dc-' + idProgressBar).dialog("destroy").remove();
							dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
							if (request.code) {
								divRefresh('{$g['divId']}');
							} else {
								dialogConfirm('Erro!', request.data);
							}
						} 
					});	                    
				}

				$('#cad002ProdutosNuvemshop').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b> Processando 0% </b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            cad002AtualizaProdutosNuvemshop(idProgressBar,1,data);
					            $(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Atualiza estoque', 'Confirma a atualização de Produtos da Nuvemshop?', \$buttons);
				});

				function cad002AtualizaProdutosNuvemshop(idProgressBar, inicio, data){
					
	                $.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueNuvemshop/?ajax=true&inicio=' + inicio + '&callback=?', function(request) {
						$('#dc-' + idProgressBar).dialog("destroy").remove();
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualização Nuvemshop', '/cad/cad002/outro/retornoAtualizacaoNuvemshop/','&totalRegistros=' + encodeURIComponent(request.totalRegistros) + '&atualizados=' + encodeURIComponent(request.ok) + '&erro=' + encodeURIComponent(request.erro) + '&encontrados=' + encodeURIComponent(request.produtosEncontrados) + '&naoEncontrados=' + encodeURIComponent(request.produtosNaoEncontrados), [200,200]);	                     
	                });	                    
		        }
				
				$('#cad002EstoqueTray').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'><b> Processando 0% </b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            cad002BuscarEstoqueTrayLoop(idProgressBar,1,data);
					            $(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Atualiza estoque', 'Confirma a atualização do estoque da Tray?', \$buttons);
				});

				function cad002BuscarEstoqueTrayLoop(idProgressBar, pagina, data){
					
	                $.getJSON('/cad/cad002/grid_func-ajax/buscarEstoqueTray/?ajax=true&pagina=' + pagina + '&callback=?', function(request) {
	                    if ((request.concluido == 0)) {
	                    	$('#progressBar').html('<b>Buscando produtos '+ request.percentual + '%</b>');
	                    	pagina++;
	                    	cad002BuscarEstoqueTrayLoop(idProgressBar,pagina,data);
	                    }else{
	                    	if (request.code) {
	                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
								idProgressBar = dialogAlert('Atualizando Estoque',"<span id='progressBar'><b> Processando 0% </b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
	                    		cad002AtualizarEstoqueTrayLoop(idProgressBar,1,data);
	                        } else {
	                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                            dialogConfirm('Erro!', request.data);
	                        }
	                    } 
	                });	                    
		        }

				function cad002AtualizarEstoqueTrayLoop(idProgressBar, inicio, data){
					
	                $.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueTray/?ajax=true&inicio=' + inicio + '&callback=?', function(request) {
	                    if (request.concluido == 0) {
	                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br />');

	                    	cad002AtualizarEstoqueTrayLoop(idProgressBar,0,data);
	                    }else{
	                    	if (request.code) {
	                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
	                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualização Estoque', '/cad/cad002/outro/retornoAtualizacaoEstoqueTray/','&totalRegistros=' + encodeURIComponent(request.totalRegistros) + '&atualizados=' + encodeURIComponent(request.ok) + '&erro=' + encodeURIComponent(request.erro) + '&encontrados=' + encodeURIComponent(request.produtosEncontrados) + '&naoEncontrados=' + encodeURIComponent(request.produtosNaoEncontrados), [980,550]);
	                        } else {
	                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
	                            dialogConfirm('Erro!', request.data);
	                        }
	                    } 
	                });	                    
		        }


				$('#cad002EstoqueMagento').bind('click', function(data) {
				window.open('http://{$EnderecoHTTP}/crm/crm001/outro/Magento1PutInventory?ajax=true');
				});
				
				$('#cad002Juntar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntar/','', [400,200]);
				});
		        
		        $('#cad002Incluir').unbind('click').bind('click', function(data) {
		            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&tabela=$from', [1000,635]);
		            //abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Novo Cadastro', '/cad/cad002/content/form1/', '', [600,400]);
		            //abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&abrirCadastro=true&divForm={$g['divId']}', [600,430]);
		        });
		        $('#cad002JuntarProdutos').unbind('click').bind('click', function(data) {
		            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntarProdutos/', '', [450,200]);
		        });
		        $('#cad002JuntarProdutos').unbind('click').bind('click', function(data) {
		            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntarProdutos/', '', [450,200]);
		        });
		        $('#cad002TrocarCodigos').unbind('click').bind('click', function(data) {
		            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Trocar Códigos', '/cad/cad002/content/cad002ContentTrocarCodigos/', '', [450,200]);
		        });
		    } </script></div>
EOT;
    
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/*if (!$r_generateHTML && !$r_generateCSV) {
			$count = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0, false, true);
			if ($count > 3500) {
				//echo '<div class="gridMsgFiltre">Consulta retornou muitos registros ('.$count.').<br/>Por favor, utilize o filtro.</div>'; 
				//die(); 

				//echo $this->gridFiltroPrint($gridFiltro);
				echo <<<EOT
		    		<div class="gridOptTop" style="margin-top: -25px; position: absolute; right: 17px;">
		    		    <div class="btn-group">
		    		        <button class="btn btn-mini" onclick="gridRefresh(this); return false;">Atualizar</button>
		    		        <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle"><span class="caret"></span></button>
		    		        <ul class="dropdown-menu pull-left" style="margin-left: -80px; margin-top: -5px;">
		    		            <li><a href="#" onclick="acoesGrid(this, false, false, true, false); return false;" class="acao-grid-imprimir">Imprimir</a></li>
		    		            <li><a href="#" onclick="acoesGrid(this, false, false, false, true); return false;" class="acao-grid-email">Enviar via e-mail</a></li>
		    		            <li><a href="#" onclick="acoesGrid(this, false, false, false, false, true, true); return false;" class="acao-grid-pdf">Gerar PDF</a></li>
		    		            <li><a href="#" onclick="acoesGrid(this, false, true, false, false); return false;" class="acao-grid-csv">Gerar CSV (Excel)</a></li>
		    		        </ul>
		    		    </div>
		    		</div>
EOT;
				echo '<div gridsql="" style="background-color: transparent;"><table class="grid" cellspacing="1" style="margin-top: 2px;" bgcolor="#EDE7DE"></table><div class="gridMsgFiltre">Consulta retornou muitos registros ('.$count.').<br/>Por favor, utilize o filtro.</div></div>';
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						corrigeGrid('{$g['divId']}');
					} </script></div>
EOT;
				die(); 
			}
		}
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		*/
		
		// echo $geradoSql;
		// die();
		
		/**
		* Botões (Versão Acima do GRID)
		*/

		//$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;*/
		/**
		 * Totais
		 */
		$totais = array(
			// sql
			array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
		);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D001_Id']) + '&tabela=$from&D024_Id=' + encodeURIComponent(array['todosCampos']['D024_Id']) + '&D001_Id=' + encodeURIComponent(array['todosCampos']['D001_Id']), [1000,635]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'cad002GridPertenceEmpresa':
		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
			array('colunaValor' => "'N' AS D009A_Flag_Pertence_Empresa", 'colunaTitulo' => 'Pertence à empresa', 'colunaAlinhar' => 'center')
		);

		$from = 'D001';
		$extra = <<<EOT
			LEFT JOIN D049 ON D049_D001_Id=D001_Id
			LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id='{$g['empresaAtual']}'
			LEFT JOIN D009A ON D009A_D009_Id=D009_Id
			LEFT JOIN D082 ON D082_Id=D049_D082_Id
			LEFT JOIN D037 ON D037_Id=D001_D037_Id
			WHERE D009A_Flag_Pertence_Empresa='N'
			GROUP BY D001_Id
			ORDER BY D001_Codigo_Produto
EOT;

		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto')
		);
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D001_Id']), [1000,635]);
				});
			} </script></div>
EOT;

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;
	
	/**
	 * Tab Comercial
	 */
	case 'comercial':
		//$C008_Id = $_REQUEST['C008_Id'];
			
		$D001_Id = $r_acaoId;
			
	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	    $mC008=mysql_fetch_array($C008);

	    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
	    $mC004=mysql_fetch_array($C004);

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D049_Id', $g['divId'])),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'callback' => 'validaMarca'),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item'),
            array('colunaValor' => 'D049_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'Custo tabela', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Valor_Custo_Ultima_Compra', 'colunaTitulo' => 'Custo compra', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo médio', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo venda', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_IPV_1', 'colunaTitulo' => 'IPV', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Preco_Loja', 'colunaTitulo' => 'Preco loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true),
            array('colunaValor' => 'group_concat(distinct D004_Local)', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'D049_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaExibir' => false),
        );

		$from = 'D049';

		$extra = <<<EOT
			left join D009 on D009_D049_Id=D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D049_D082_Id=D082_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			where D001_Id='{$r_acaoId}'
			and (D009_C004_Id='{$g['empresaAtual']}')
			group by D009_Id
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D049_Id = id[0].replace(/D049_Id-/, '');
					var D001_Id = id[1].replace(/D001_Id-/, '');
					var D009_Id = id[2].replace(/D009_Id-/, '');
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Sub-item', '/cad/cad002/content/cad002ContentEditarMarca/', '&acaoId=' + D049_Id + '&D001_Id=' + D001_Id + '&D009_Id=' + D009_Id + '&tabela={$from}',  [1000,600]);
					
					//Setar id da D049 para abrir o formulário de E-commerce e gravar este campo na D136_D049_Id
					//$('#cad002Ecommerce').data('D049_Id', id);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		function gridLinha($array) {
		    if ($array['todosCampos']['D049_Flag_Ativo']=='N') {
		        return 'linhaCinza';
		    }
		}
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'inativos':
		//$C008_Id = $_REQUEST['C008_Id'];
			
		$D001_Id = $r_acaoId;
			
	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
	    $mD001=mysql_fetch_array($D001);

	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	    $mC008=mysql_fetch_array($C008);

	    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
	    $mC004=mysql_fetch_array($C004);

		/**
		 * Definição do grid tabela
		 */
		
		$grid = array(
			array('colunaValor'=>'livre','colunaTipo'=>'livre', 'callback'=>'gExcluirLinha','callbackParameter' => array('D049_Id', $g['divId'])),
			array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id',  'colunaAlinhar' => 'center' , 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id',  'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => '', 'colunaTitulo' => '', 'callback' => 'validaEditarMarca', 'style' => 'width:1%;', 'colunaTipo'=>'livre'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'callback' => 'validaMarca'),
			array('colunaValor' => 'D049_Flag_Padrao', 'colunaTitulo' => '', 'callback'=>'validarFlagPadrao', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_Separado_3(D009_Id) Separado', 'colunaTitulo' => 'Separado', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'if(D049_Flag_Tipo="CP",0,D082_Percentual_Acrescimo) as D082_Percentual_Acrescimo', 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
			array('colunaValor' => 'D041_Valor_Base', 'colunaTitulo' => 'Preço 1',  'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IPV1', 'colunaTitulo' => 'IPV', 'callback' => 'validaIPV1', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D041_Valor_Preco_Consumidor', 'colunaTitulo' => 'Preço 2', 'callback'=>'validaConsumidorVarejo', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IPV2', 'colunaTitulo' => 'IPV', 'callback'=>'validaIPV2', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D041_Valor_Preco_Revenda', 'colunaTitulo' => 'Preço 3', 'callback'=>'validaRevenda', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IPV3', 'colunaTitulo' => 'IPV', 'callback'=>'validaIPV3', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre'),
			array('colunaValor' => 'D009_Flag_Preco_Tabelado', 'colunaTitulo' => '<li title=".ui-icon-locked" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-locked"></span></li>', 'callback'=>'validaPrecoTabelado', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			//array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => 'Min.', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => 'Max.', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq.', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTipo' =>'livre', 'colunaTitulo' =>'ICF', 'callback' => 'validaICF', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'if(D049_Flag_Tipo="CP",0,D082_Percentual_Acrescimo) D082_Percentual_Acrescimo', 'colunaTitulo' =>'%', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' =>'Custo', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' =>'Médio', 'colunaAlinhar' => 'right'),
			//array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			//array('colunaValor' => 'D041_Id', 'colunaTitulo' => '', 'colunaExibir'=>false),
			//array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir'=>false),
			//array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir'=>false),
		);



		$from = 'D049';

		$extra = <<<EOT
					left join D009 on D009_D049_Id=D049_Id
	                left join D001 on D001_Id=D049_D001_Id
	                left join D024 on D024_Id=D049_D024_Id
	                left join D041 on D041_D049_Id=D049_Id
	                left join D082 on D049_D082_Id=D082_Id
	            where D001_Id='$D001_Id' AND D049_Flag_Ativo != 'S'
	          	and (D009_C004_Id={$g['empresaAtual']} or D009_Id is null)
EOT;


	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
			$novoIdTelaForm = uniqid();
			echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id');
					var id = id.split('-');
					id = id[1];
					//Setar id da D049 para abrir o formulário de E-commerce e gravar este campo na D136_D049_Id
					$('#cad002Ecommerce').data('D049_Id', id);
					divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
					divRefresh('{$_REQUEST['idTabForm4']}');
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	/**
	 * Tab Estoque
	 */
	case 'estoque':
		

	//$C008_Id = $_REQUEST['C008_Id'];
		
	$D001_Id = $r_acaoId;
	
		
    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
    $mD001=mysql_fetch_array($D001);

    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
    $mC008=mysql_fetch_array($C008);

    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
    $mC004=mysql_fetch_array($C004);

	/**
	 * Definição do grid tabela
	 */

	$grid = array(
		array('colunaValor' => '', 'colunaTipo' => 'livre', 'callback'=>'validaCampoExcluir'),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' => $mC008['C008_Apelido_Marca'], 'callback' => 'validaMarca'),
		array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Separado_3(D009_Id) Separado', 'colunaTitulo' => 'Separado', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => 'Min.', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => 'Max.', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq.', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D009_Custo_Unitario(D009_Id) Custo', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D041_Valor_Base', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D041_Valor_Preco_Consumidor', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D041_Valor_Preco_Revenda', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir' => false),
	);



	$from = 'D009';

	$extra = <<<EOT
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D024 on D024_Id=D049_D024_Id
                left join D041 on D041_D049_Id=D049_Id and (ifnull(D041_C004_Id,0)<=0 or D041_C004_Id={$g['empresaAtual']})
                left join D082 on D049_D082_Id=D082_Id
            where D001_Id='$D001_Id'
            and D009_C004_Id={$g['empresaAtual']}
	
EOT;

	
/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	/**
	 * Tab Custos
	 */
	case 'custos':
		

	//$C008_Id = $_REQUEST['C008_Id'];
		
	$D001_Id = $r_acaoId;
		
    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id=$D001_Id");
    $mD001=mysql_fetch_array($D001);

    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
    $mC008=mysql_fetch_array($C008);

    $C004=mysql_query("select C004_Flag_Decimais_Sugestao_Compra from C004 where C004_Id={$g['empresaAtual']}");
    $mC004=mysql_fetch_array($C004);

	/**
	 * Definição do grid tabela
	 */
	
	$grid = array(
		array('colunaValor' => '', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir', 'colunaTipo' => 'livre'),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>$mC008['C008_Apelido_Marca'], 'callback' => 'validaMarca'),
		array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' =>'Trabalho', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTipo' =>'livre', 'colunaTitulo' =>'ICF', 'callback' => 'validaICF', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'if(D049_Flag_Tipo="CP",0,D082_Percentual_Acrescimo) D082_Percentual_Acrescimo', 'colunaTitulo' =>'%', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' =>'Custo', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' =>'Médio', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Quantidade_OC(D009_Id) OC', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Custo_Unitario(D009_Id) Custo', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D041_Valor_Base', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D041_Valor_Preco_Consumidor', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D041_Valor_Preco_Revenda', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => '', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' => '', 'colunaExibir' => false),
	);


	$from = 'D009';

	$extra = <<<EOT
                left join D049 on D049_Id=D009_D049_Id
                left join D001 on D001_Id=D049_D001_Id
                left join D024 on D024_Id=D049_D024_Id
                left join D041 on D041_D049_Id=D049_Id
                left join D082 on D049_D082_Id=D082_Id
                    where D001_Id='$D001_Id'
                      and D009_C004_Id={$g['empresaAtual']}
                 group by D009_Id
	
EOT;

	
/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	 * Tab SUBSTITUIÇÃO
	 */
	case 'substituicao':
		
	//$C008_Id = $_REQUEST['C008_Id'];
		
	$D001_Id = $r_acaoId;

	/**
	 * Definição do grid tabela
	 */
	
	$grid = array(
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D017_Id', $g['divId'])),
		array('colunaValor' => 'D017_Id', 'colunaTitulo' =>'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
		array('colunaValor' => 'D017_Data_Inclusao', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D017_Codigo_Antigo', 'colunaTitulo' =>'Substituição'),
		array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' =>'Usuário'),

	);


	$from = 'D017';

	$extra = <<<EOT
                left join D001 on D001_Id=D017_D001_Id
                left join C007 on D017_C007_Id=C007_Id
                where D017_D001_Id='$D001_Id'
                order by D017_Codigo_Antigo
EOT;

	
/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
		$r_idTabForm4 = (isset($r_idTabForm4)) ? $r_idTabForm4 : false;
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$r_idTabForm4}', id);
				divRefresh('{$r_idTabForm4}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	
	
	if($D001_Id){
		$g['smarty']->display('grid.tpl');		
	}else{
		echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
	}
	
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		//$botoesDir = '<button type="submit" id="cad002EditarMarca_btn" style="margin-right:20px;">Gravar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	/**
	 * Tab Empresas
	 */
	case 'empresas':			
		//$C008_Id = $_REQUEST['C008_Id'];
		
		$D001_Id_Original =  $r_D001_Id;		
		$D001_Id = $r_D001_Id;

		/**
		 * Definição do grid tabela
		 */
	    $D001=mysql_query("select D001_C008_Id from D001 where D001_Id='$D001_Id'");
	    $mD001=mysql_fetch_array($D001);
	    
	    $C008=mysql_query("select * from C008 where C008_Id='$mD001[D001_C008_Id]'");
	  	$mC008=mysql_fetch_array($C008);
		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D083_Id', $g['divId'])),
            array('colunaValor' => 'D083_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código empresa', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D083_Flag_Fornecedor', 'colunaTitulo' => 'Fornecedor?', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'D083_Flag_Cliente', 'colunaTitulo' => 'Cliente?', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Código Produto'),
            array('colunaValor' => 'D083_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaEditar' => true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D083_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
        );

		$from = 'D083';

		$extra = <<<EOT
            left join D024 on D024_Id=D083_D024_Id
			left join D049 on D049_Id=D083_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			where D083_D001_Id='{$r_D001_Id}'
			order by D024_Nome_Fantasia
	
EOT;

	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D083_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Empresa - ID: ' + acaoId, '/cad/cad002/content/cad002ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent(acaoId) + '&D001_Id=' + encodeURIComponent('$D001_Id_Original'), [660,240]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		$sqlD001 = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D001 WHERE D001_Id = '{$r_D001_Id}'");
		$D001 = mysql_fetch_assoc($sqlD001);
		if($D001['D001_Flag_Pre_Cadastro'] == 'S'){
			$attrDisabled = (isset($g['c029Ids'][187])) ? "" : "disabled='disabled'";
		} else {
       		$attrDisabled = (isset($g['c029Ids'][186])) ? "" : "disabled='disabled'";
		}
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad002FormCadastroTabEmpresaBtnIncluir" {$attrDisabled}>Incluir</button>
			</div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D024_Id' => array('titulo' => 'Código empresa', 'tipo' => 'numero'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D083_Flag_Fornecedor' => array('titulo' => 'Fornecedor?(S/N)', 'tipo' => 'texto'),
			'D083_Flag_Cliente' => array('titulo' => 'Fornecedor?(S/N)', 'tipo' => 'texto'),
			'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Código produto', 'tipo' => 'texto'),
			'D083_Codigo_Barras' => array('titulo' => 'EAN', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		//$botoesEsq .= '<button type="button" id="cad002FormCadastroTabEmpresaBtnIncluir" style="margin:0 0px 20px 10px;">Incluir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002FormCadastroTabEmpresaBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Empresas', '/cad/cad002/content/cad002ContentCadastroEmpresas/', '&tab=geral&D001_Id=' + encodeURIComponent('$D001_Id_Original'), [660,240]);
				});
			} </script></div>
EOT;
	
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', md5($g['r_']));
			$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($D001_Id){
			$g['smarty']->display('buttons-floatBottom.tpl');	
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;
	
	/**
	 * Tab Histórico
	 */
	case 'historico':
		/**
		 * Definição do grid tabela
		 */

		$grid = array(
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T001_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'editarHistorico', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T048_Id', 'colunaTitulo' => 'T001_T048_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T029_Id', 'colunaTitulo' => 'T001_T029_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T001_T030_Id', 'colunaTitulo' => 'T030_T030_Id', 'colunaExibir' => false, 'chavePrimaria' => true),  
			array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true),          
            array('colunaValor' => 'T001_Lote_Validade', 'colunaTitulo' => 'Sub-item', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\' or T001_Flag_Operacao=\'N\') and IFNULL(T001_T008_Id,0)=0,Forn.D024_Id,Cli.D024_Id) as IdCliente', 'colunaTitulo' => 'Cód', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\' or T001_Flag_Operacao=\'N\') and IFNULL(T001_T008_Id,0)=0,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa),1,20) as Cliente', 'colunaTitulo' => 'Cliente/Fornecedor'),
            array('colunaValor' => 'if(Cli.D024_CNPJ is null,Forn.D024_CNPJ,Cli.D024_CNPJ)', 'colunaTitulo' => 'CNPJ', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
            array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo', '/cad/cad002/outro/demonstrativoCusto/', 'T001_Id', 'right', 5, '', '', '320'), 'colunaExibirNivel' => 2, 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal'),
			array('colunaValor' => 'T001A_Valor_Custo_Unitario_Liquido', 'colunaTitulo' => 'Custo Líq.', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2, 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal', 'colunaCasasDecimais' => '5', 'colunaExibir' => false),
            array('colunaValor' => 'T001_Valor_Custo_Medio_Unitario as Medio', 'colunaTitulo' => 'Médio', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'colunaExibirNivel' => 2, 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal'),
            array('colunaValor' => 'T001_Valor_Preco_Unitario as Preco', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'IPV', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvHistorico', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '4', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) as T001_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-verde);', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
            array('colunaValor' => 'T001_Quantidade_Estoque_Inicial', 'colunaTitulo' => 'Inicial', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Flag_Operacao', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);'),
            array('colunaValor' => 'T001_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-verde);;'),
            array('colunaValor' => 'T001_Flag_MMF', 'colunaTitulo' => 'F', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Flag_Atualiza_Custo', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
            array('colunaValor' => "CONCAT(D004_Local, ' - ', T066_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left', 'colunaExibirNivel' => 2, 'style' => 'background: var(--celula-azul);font-weight:normal'),
            array('colunaValor' => 'T001_Quantidado_Saldo_Locacao', 'colunaTitulo' => 'Saldo Loc.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background: var(--celula-azul);font-weight:normal'),
            array('colunaValor' => 'T001_Flag_Devolucao', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Observacao', 'colunaTitulo' => 'Observacao', 'colunaExibir' => true, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Numero_Controle', 'colunaTitulo' => 'CNPJ', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_T014_Id', 'colunaTitulo' => 'T001_T014_Id', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Percentual_Custo_Financeiro', 'colunaTitulo' => '%C.F.', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => 'DIFAL R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
			array('colunaValor' => 'T001A_Valor_CBS', 'colunaTitulo' => 'CBS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)', 'colunaExibirNivel' => 2),
			array('colunaValor' => 'T001A_Valor_IBS', 'colunaTitulo' => 'IBS R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)', 'colunaExibirNivel' => 2),
			array('colunaValor' => 'T001A_Aliquota_CBS_Efetiva', 'colunaTitulo' => '% CBS Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)', 'colunaExibirNivel' => 2),
			array('colunaValor' => 'T001A_Aliquota_IBS_UF_Efetiva', 'colunaTitulo' => '% IBS UF Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)', 'colunaExibirNivel' => 2),
			array('colunaValor' => 'T001A_Aliquota_IBS_Municipal_Efetiva', 'colunaTitulo' => '% IBS Mun. Efetiva', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-azul)', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Custo_Financeiro', 'colunaTitulo' => 'C.F. R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
	        array('colunaValor' => 'T001_Quantidade_Saldo_Mes_Anterior', 'colunaTitulo' => 'Saldo mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
        	array('colunaValor' => 'T001_Valor_Custo_Mes_Anterior', 'colunaTitulo' => 'Custo mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
			array('colunaValor' => 'T001_Valor_Custo_Medio_Mes_Anterior', 'colunaTitulo' => 'Médio mês anterior', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right', 'colunaExibir' => true),
			array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaExibir' => false),

        );

		$from = 'T001';

		$extra = <<<EOT
		-- JOIN
			left join D024 as Forn on Forn.D024_Id=T001_D024_Id
			left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
			left join D006 on D006_Id=T001_D006_Id
			left join D009 on D009_Id=T001_D009_Id
			left join C004 on C004_Id=D009_C004_Id
			left join T066 on T066_Id=T001_T066_Id
			left join D004 on D004_Id=T001_D004_Id
			left join T014 on T014_Id=T001_T014_Id
			left join T013 on T013_Id=T014_T013_Id
			left join T008 on T008_Id=T001_T008_Id
			left join T007 on T007_Id=T008_T007_Id
			left join T030 on T030_Id=T001_T030_Id
			left join T029 on T030_T029_Id=T029_Id
			left join D021 on D021_Id=T007_D021_Id
			left join C007 as Vendedor on Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T007_C007_Id_Vendedor_Externo
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			left join T001A on T001_Id = T001A_T001_Id
		-- WHERE
			where D009_Id='{$r_acaoId}'
			{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
		-- ORDER BY
			order by T001_Data_Lancamento desc,T001_Id desc
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='E') 
	 	    {
	  	       return 'linhaVerdeEscuro';
	 	    }
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='C') 
	 	    {
	  	       return 'linhaVermelho';
	 	    }
	 	    if ($array['todosCampos']['T001_Flag_Operacao']=='B') 
	 	    {
	  	       return 'linhaAzul';
	 	    }
	   }	

	   if($g['C031']['mostrarBotaoIncluirHistoricoProduto'] == 'S') {
	   		echo <<<EOT
				<div class="buttonsBottomTop">
				<button type="button" id="cad002FormCadastroTabHistoricoBtnIncluir">Incluir</button>
				</div>
			EOT;
	   }
	   echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002FormCadastroTabHistoricoBtnIncluir').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Histórico', '/cad/cad002/content/cad002contentEditarHistorico/','&D009_Id={$r_acaoId}', [400,350]);
				});
			} </script></div>
EOT;

		/*
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T007 = id[0].replace(/T001_T007_Id-/, '');
					var T013 = id[1].replace(/T001_T013_Id-/, '');
					//console.log(T007 + ' - ' + T013);
					if (id > 0) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,600]);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Orçamento - ID: ' + id, '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,600]);
					}
				});
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;
*/

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T001_Id = id[0].replace(/T001_Id-/, '');
					var T013_Id = id[1].replace(/T013_Id-/, '');
					var T007_Id = id[2].replace(/T007_Id-/, '');
					var T048_Id = id[3].replace(/T001_T048_Id-/, '');
					var T029_Id = id[4].replace(/T001_T029_Id-/, '');
					var T030_Id = id[5].replace(/T001_T030_Id-/, '');
					if (T007_Id > 0) {
						nova_janela('/hardness3/hardness/almoxarifado/gerar_danfe.php?T007_Id=' + T007_Id + '',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
					} else if(T013_Id > 0){
        				nova_janela('/hardness3/hardness/nota_recebida/gerar_danfe.php?T013_Id=' + T013_Id + '',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
					} else if(T048_Id > 0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Montagem', '/cad/cad002/content/cad002ContentProdutosHistoricoMontagem/','&T001_Id=' + encodeURIComponent(T001_Id) + '&acaoId=' + encodeURIComponent(T048_Id), [750,500]);
					}else if(T030_Id > 0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Divergências de Estoque - ID: ' + T029_Id, '/est/est001/content/est001ContentDivergenciasEstoqueIncluir/', '&acaoId=' + encodeURIComponent(T029_Id) + '&T029_Id=' + encodeURIComponent(T029_Id), [950,600]);
					}
				});
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T001_Descricao_Operacao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
			'T001_Valor_Custo_Unitario' => array('titulo' => 'Custo', 'tipo' => 'texto'),
			'T001_Valor_Custo_Medio_Unitario' => array('titulo' => 'Médio', 'tipo' => 'texto'),
			'T001_Valor_Preco_Unitario' => array('titulo' => 'Preço', 'tipo' => 'numero'),
			'T001_Quantidade' => array('titulo' => 'Qtde', 'tipo' => 'numero'),
			'T001_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
			'D004_Local' => array('titulo' => 'Local', 'tipo' => 'texto'),
			'T001_Flag_MMF' => array('titulo' => 'F', 'tipo' => 'texto'),
			'T001_Flag_Atualiza_Custo' => array('titulo' => 'C', 'tipo' => 'texto'),
			'T001_Flag_Operacao' => array('titulo' => 'Operação(E/S/B/C)', 'tipo' => 'texto'),
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'NFe Entrada', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NFe Saída', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),

		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
/*		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tr').bind('click', function(data) {
			nova_janela('/hardness/nota_emitida/nf_emitida.php?T007_Id={$array['todosCampos']['T001_T007_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
			});
		} </script></div>
EOT;
		}else{
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tr').bind('click', function(data) {
        	nova_janela('/hardness/nota_recebida/espelho_nf_entrada.php?T013_Id={$array['todosCampos']['T001_T013_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Nota_Emitida');
        	});
		} </script></div>
EOT;
		} 
	*/
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		gBotaoAuditoria($from);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;
	
	/**
	 * Tab Montagem
	 */
	case 'montagem':
		//$C008_Id = $_REQUEST['C008_Id'];
		// AcaoID aqui deve ser o D049_Id
		$r_acaoId = mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id = '{$r_acaoId}'");
		$r_acaoId = mysql_fetch_assoc($r_acaoId);
		$r_acaoId = $r_acaoId['D009_D049_Id'];
			
		/**
		 * Definição do grid tabela
		 */

		$grid = array(
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D042_Id', $g['divId'])),
	        array('colunaValor' => 'D042_Quantidade', 'colunaTitulo' => 'Quant.', 'colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id','colunaExibir' => false),
	        array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
	        array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
	        array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'floor(D009_Quantidade_Estoque/D042_Quantidade) as Quantidade_Minima', 'colunaTitulo' => 'Disponib.', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo unit', 'colunaCasasDecimais' => 4, 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
	        array('colunaValor' => 'D009_Valor_Custo_Unitario*D042_Quantidade as custoTotalFinal', 'colunaTitulo' => 'Custo total', 'colunaCasasDecimais' => 4, 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
	        array('colunaValor' => 'D042_Observacao', 'colunaTitulo' => 'Observação', 'callback' => 'validaObervacaoMontagem', 'colunaEditar' => true),
	        array('colunaValor' => 'D042_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
	        array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' => 'Custo Compra', 'callback' => 'formatarNumeroCustoCompra', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	        array('colunaValor' => 'custoTotal', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Custo Total', 'callback' => 'validaCustoTotalMontagem', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	        array('colunaValor' => 'D049_Valor_Preco_Tabela*(1-(D049_Percentual_Desconto_Tabela/100)) as D049_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tabela', 'callback' => 'formatarNumeroCustoTabela', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	        array('colunaValor' => 'custoTotal2', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Custo Total', 'callback' => 'validaCustoTotal2Montagem', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
	    );
		$from = 'D042';
		$extra = <<<EOT
			-- JOIN
	      		LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
	    		LEFT JOIN D082 ON D049_D082_Id=D082_Id
			 	LEFT JOIN D001 ON D001_Id=D049_D001_Id
	    		LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id={$g['empresaAtual']}
			-- WHERE
		    	WHERE (D042_D049_Id='$r_acaoId'
		    	AND D009_C004_Id={$g['empresaAtual']})
EOT;

	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Itens', 'totalizador' => 'count', 'callbackParameter' => 0),
			array('nome' => 'Custo_Total', 'valor' => 'custoTotalFinal', 'titulo' => 'Total Custo'),
        );

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

/* 		if (!personalizacaoTotais()) {
			$totalItens= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra),0);
			$totalCusto= gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(D009_Valor_Custo_Unitario*D042_Quantidade)", $from, $extra),4); */

	 		/**
			* Botões (Versão Acima do GRID)
			*/
/* 			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">Itens<br><input type="text" class="whiteBg" value="{$totalItens}" size="6" readonly></span>
					<span class="item" style="text-align:right">Total custo<br><input type="text" class="whiteBg" value="{$totalCusto}" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
	  	} */
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	
		if($r_acaoId){
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
			//$g['smarty']->display('buttons-floatBottom.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}

	break;
	
	/**
	 * Tab Locação
	 */
	case 'locacao':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T066_Id', $g['divId'])),
            array('colunaValor' => 'T066_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
            array('colunaValor' => 'D004_Id', 'colunaTitulo' => 'D004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'D004_Flag_Somar_Estoque_Fisico', 'colunaTitulo' => 'Somar Estoque', 'style' => 'width:1px','colunaAlinhar' => 'center'),
            array('colunaValor' => 'T066_Locacao', 'colunaTitulo' => 'Locação', 'colunaExibir' => false),
            array('colunaValor' => 'D146_Descricao', 'colunaTitulo' => 'Armazém'),
            array('colunaValor' => 'D048_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero','style' => 'width:1px'),
            array('colunaValor' => 'D009_Quantidade_Pedido_Por_Locacao(T066_D009_Id,T066_Id) as Pedidos', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero','style' => 'width:1px'),
            array('colunaValor' => 'T066_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Líquido', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero','style' => 'width:1px'),
            array('colunaValor' => 'T066_Minimo', 'colunaTitulo' => 'Estoque mínimo', 'colunaAlinhar' => 'right', 'style' => 'width:1px', 'callback' => 'gCorrigeNumero'),
            array('colunaValor' => 'T066_Maximo', 'colunaTitulo' => 'Estoque máximo', 'colunaAlinhar' => 'right', 'style' => 'width:1px', 'callback' => 'gCorrigeNumero'),
			array('colunaValor' => 'T066A_Data_Validade', 'colunaTitulo' => 'Validade', 'width:1px', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T066_Agrupamento', 'colunaTitulo' => 'Agrupamento', 'colunaAlinhar' => 'right', 'style' => 'width:1px', 'callback' => 'gCorrigeNumero', 'colunaExibir' => false),
            array('colunaValor' => 'limparLocacao', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'limparLocacao'),
        );

		$from = 'T066';
	
		$extra = <<<EOT
    	  	left join D009 on T066_D009_Id=D009_Id
			left join D048 on D048_Id=T066_D048_Id
			left join D004 on D004_Id=T066_D004_Id
			left join D146 on D146_Id=D004_D146_Id
			left join T066A ON T066_Id=T066A_T066_Id
			where D009_Id='{$r_acaoId}'
			and D009_C004_Id={$g['empresaAtual']}
EOT;

	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T066_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentCadastroLocacaoEditar/','&D049_Id=' + encodeURIComponent($r_acaoId) + '&D009_Id=' + encodeURIComponent($r_acaoId) + '&acaoId=' + encodeURIComponent(id) + '&tabela=T066', [500,200]);
			});
		} </script></div>
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		if($r_acaoId && $g['C031']['habilitarMultiplasLocacoes'] == 'S'){
			/**
			* Botões (Versão Acima do GRID)
			*/
			$sqlD001 = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id = '{$r_acaoId}'");
			$D001 = mysql_fetch_assoc($sqlD001);
			if($D001['D001_Flag_Pre_Cadastro'] == 'S'){
				$attrDisabled = (isset($g['c029Ids'][187])) ? "" : "disabled='disabled'";
			} else {
				$attrDisabled = (isset($g['c029Ids'][186])) ? "" : "disabled='disabled'";
			}
			echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad002IncluirLocacao" {$attrDisabled}>Incluir</button>
				<button type="button" id="cad002JuntarLocacao" {$attrDisabled}>Juntar Locações</button>
			</div>
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad002IncluirLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentCadastroLocacao/','&D009_Id=' + encodeURIComponent($r_acaoId), [500,200]);
				});
				$('#cad002JuntarLocacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Locação', '/cad/cad002/content/cad002ContentJuntarLocacao/','&D009_Id=' + encodeURIComponent($r_acaoId), [500,200]);
				});
			} </script></div>
EOT;
		}	

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');	
			$g['smarty']->display('buttons-floatBottom.tpl');			
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'locacaoVisualizar':	
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T066_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D004_Id', 'colunaTitulo' => 'D004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Locação', 'style' => 'width: 1%; padding: 0 10px;'),
            array('colunaValor' => 'T066_Locacao', 'colunaTitulo' => 'Locação', 'colunaExibir' => false),
            array('colunaValor' => 'D048_Estoque', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'left', 'callback' => 'gCorrigeNumero', 'style' => 'width: 1%;'),
            array('colunaValor' => 'T066_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaAlinhar' => 'left', 'callback' => 'gCorrigeNumero'),
        );

		$from = 'T066';
	
		$extra = <<<EOT
    	  	left join D009 on T066_D009_Id=D009_Id
			left join D048 on D048_Id=T066_D048_Id
			left join D004 on D004_Id=T066_D004_Id
			where D009_Id='{$r_acaoId}'
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');	
	break;
	
	case 'referenciaProduto':	

		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
								
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D083_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'D083_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código produto', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição produto', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código empresa', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D083_Flag_Fornecedor', 'colunaTitulo' => 'Fornecedor?', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'D083_Flag_Cliente', 'colunaTitulo' => 'Cliente?', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D083_Codigo_Produto_Fornecedor', 'colunaTitulo' => 'Código produto empresa', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D083_Codigo_Barras', 'colunaTitulo' => 'EAN produto empresa', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D083_Quantidade_Minima_Compra', 'colunaTitulo' => '', 'colunaExibir' => false),
           
        );

		$from = 'D083';

		$extra = <<<EOT
            left join D024 on D024_Id=D083_D024_Id
			left join D049 on D049_Id=D083_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[1].replace('D001_Id-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastro Produto - ID: ' + acaoId, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela=' + encodeURIComponent('D001'), [1000,650]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);


		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad002FormCadastroTabEmpresaBtnIncluir'),
		);
		echo Botoes::processar($botoes);

		/*echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad002FormCadastroTabEmpresaBtnIncluir" {$attrDisabled}>Incluir</button>
			</div>
EOT;
*/
		/*
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		//$botoesEsq .= '<button type="button" id="cad002FormCadastroTabEmpresaBtnIncluir" style="margin:0 0px 20px 10px;">Incluir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002FormCadastroTabEmpresaBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Empresas', '/cad/cad002/content/cad002ContentCadastroEmpresas/', '&tab=geral&D001_Id=' + encodeURIComponent('$D001_Id_Original'), [660,240]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código produto', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição produto', 'tipo' => 'texto'),
			'D024_Id' => array('titulo' => 'Código empresa', 'tipo' => 'numero'),
			'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D083_Flag_Fornecedor' => array('titulo' => 'Fornecedor?(S/N)', 'tipo' => 'texto'),
			'D083_Flag_Cliente' => array('titulo' => 'Fornecedor?(S/N)', 'tipo' => 'texto'),
			'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Código produto', 'tipo' => 'texto'),
			'D083_Codigo_Barras' => array('titulo' => 'EAN', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
	
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		gBotaoAuditoria($from);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		$g['smarty']->display('grid.tpl');		
	break;

	/**
	 * Tab Pedidos
	 */
	case 'pedidos':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Nº Pedido', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '1000,615')),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo' => 'Tp', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Separado', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'width:1%'),
            //array('colunaValor' => 'T006_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T006_Flag_Reservar_Estoque', 'N|S')),
            array('colunaValor' => 'T006_Prioridade', 'colunaTitulo' => 'Prioridade', 'style' => 'width: 1%'),
            array('colunaValor' => 'T006_Prioridade', 'colunaTitulo' => 'Prioridade', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvPedidos', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T007_Id) as NF', 'colunaTitulo' => 'NF'),
            array('colunaValor' => 'GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) as Estoque', 'colunaTitulo' => 'Est', 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T075_Id', 'colunaTitulo' => 'DI', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_D024_Id', 'colunaTitulo' => 'T005_D024_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T006_Id_Dividir', 'colunaTitulo' => 'Id Div', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Valor_Desconto', 'colunaTitulo' => 'R$ Descto', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'width:1%', 'colunaExibirNivel' => '2'),
            array('colunaValor' => 'totalDescontoPedidosCliente', 'colunaTitulo' => 'R$ Descto Pendentes', 'callback' => 'totalDescontoPedidosCliente', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => '2'),
        );

		$from = 'T005';

		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T006 on T006_T005_Id=T005_Id
    		left join D009 on D009_Id=T006_D009_Id
    		left join D049 on D049_Id=D009_D049_Id
    		left join D082 on D082_Id=D049_D082_Id
    		left join T008 on T008_T006_Id=T006_Id
   			left join D006 ON D006_Id=T006_D006_Id
       		left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
   	     	left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
        	where T006_D009_Id='{$r_acaoId}'
			AND IFNULL(T006_T075_Id,0) <= 0
			AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
			AND IFNULL(T006_C004_Id_Estoque, D009_C004_Id) = D009_C004_Id
			AND ((T006_Flag_Status!='3'
			AND (T005_Flag_Status!='4' OR (T005_Flag_Status='4' AND T005_Flag_Expedicao = '2' AND T007_Id is null))
			AND T005_Flag_Status!='5'
			AND T005_Flag_Status!='7'
			AND T005_Flag_Status!='11'
			AND T005_Flag_Status!='8'
			AND T005_Flag_Status!='26')
			 OR T005_Flag_Status='')
			AND D006_Flag_Estoque='D'    
       group by T006_Id
       ORDER BY IF(T008_Id IS NULL,1,0) ASC, T006_Prioridade DESC
EOT;

/* CLAUSULA ANTERIORMENTE USADA NESTE GRID, ALTEREI PARA USAR A MESMA CLAUSLA DA FUNCTION D009_Quantidade_Pedido (Marcelo-06-05-2019)

    		and (T006_Quantidade>0 or T006_Quantidade_Separacao>0)
    		and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
    		and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, D009_C004_Id) = D009_C004_Id
    		and T007_Id is null
*/

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// Modelo: NÂO permitir ORDERNAÇÃO
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),		
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),	
			'T005_Nome_Status' => array('titulo' => 'Status', 'tipo' => 'texto'),
		);
                 
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		function gridLinha($array) {
		    if($array['todosCampos']['T005_Flag_Status']=='5' or $array['todosCampos']['T005_Flag_Status']=='4'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T005_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['T005_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['T005_Flag_Status']=='14'){
		        return 'linhaVerde';
		    } else if($array['todosCampos']['T005_Flag_Status']<=0 || $array['todosCampos']['T005_Flag_Status']=='9'){
		       return 'linhaAzul';
		    }
		}

		// Botões
		$botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if($r_acaoId){
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'diMP':
		
	//$C008_Id = $_REQUEST['C008_Id'];
	$D049_Id = $r_acaoId;
	
    $sql = mysql_query("select D009_Id from D009 where D009_D049_Id='{$D049_Id}' and D009_C004_Id='{$g['empresaAtual']}'");
    $row = mysql_fetch_array($sql);
    
	$diasMP=$g['C031']['diasMedioPrazo'];
	$data = strtotime("+$diasMP day", strtotime(date("Y-m-d")));
	$dataMP = date("Y-m-d", $data);	   

	$sql = mysql_query("");
    
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' =>'DI'),
		array('colunaValor' => 'T075_Id','chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor' => 'D009_Id','colunaExibir' => false),
		array('colunaValor' => 'T055_Id','colunaExibir' => false),
		array('colunaValor' => 'T075_Data_Previsao', 'colunaTitulo' =>'Prev. Chegada', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
		//array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Produto'),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Fornecedor'),		
		array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' =>'Qt. DI', 'callback'=>'gCorrigeNumero'),
		array('colunaValor' => 'QteLiquido','colunaTipo'=>'livre', 'colunaTitulo' =>'Qt. Liq.', 'callback'=>'QteLiquido'),
		array('colunaValor' => 'QtePedidos','colunaTipo'=>'livre', 'colunaTitulo' =>'Qt. Pedidos', 'callback'=>'QtePedidos'),	
	);


	$from = 'T075';

	$extra = <<<EOT
			left join T055 on T055_T075_Id=T075_Id
			left join D009 on D009_Id=T055_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id			
			left join D024 on D024_Id=T075_D024_Id
	        where T075_Id IN (select T055_T075_Id from T055 where T055_D009_Id='{$row['D009_Id']}')
	        and T075_Data_Previsao<='{$dataMP}'
	        and T075_Data_Previsao>0
	        and T055_D009_Id='{$row['D009_Id']}' 
	        and ifnull(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
	        order by T075_Data_Previsao asc
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	if($D049_Id){
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	}else{
		echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
	}
break;

	case 'diLP':
		
	//$C008_Id = $_REQUEST['C008_Id'];
	$D049_Id = $r_acaoId;
	
    $sql = mysql_query("select D009_Id from D009 where D009_D049_Id='{$D049_Id}' and D009_C004_Id='{$g['empresaAtual']}'");
    $row = mysql_fetch_array($sql);
    
	$diasMP=$g['C031']['diasMedioPrazo'];
	$data = strtotime("+$diasMP day", strtotime(date("Y-m-d")));
	$dataMP = date("Y-m-d", $data);	   

	$sql = mysql_query("");
    
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' =>'DI'),
		array('colunaValor' => 'T075_Id','chavePrimaria'=>true, 'colunaExibir' => false),
		array('colunaValor' => 'D009_Id','colunaExibir' => false),
		array('colunaValor' => 'T055_Id','colunaExibir' => false),
		array('colunaValor' => 'T075_Data_Previsao', 'colunaTitulo' =>'Prev. Chegada', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
		//array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Produto'),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
		array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Fornecedor'),		
		array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' =>'Qt. DI', 'callback'=>'gCorrigeNumero'),
		array('colunaValor' => 'QteLiquido','colunaTipo'=>'livre', 'colunaTitulo' =>'Qt. Liq.', 'callback'=>'QteLiquido'),	
		array('colunaValor' => 'QtePedidos','colunaTipo'=>'livre', 'colunaTitulo' =>'Qt. Pedidos', 'callback'=>'QtePedidos'),
	);


	$from = 'T075';

	$extra = <<<EOT
			left join T055 on T055_T075_Id=T075_Id
			left join D009 on D009_Id=T055_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id			
			left join D024 on D024_Id=T075_D024_Id
	        where T075_Id IN (select T055_T075_Id from T055 where T055_D009_Id='{$row['D009_Id']}')
	        and T075_Data_Previsao>'{$dataMP}'
	        and T075_Data_Previsao>0
	        AND T055_D009_Id='{$row['D009_Id']}' 
	        order by T075_Data_Previsao asc
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	if($D049_Id){
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	}else{
		echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
	}
break;
	
/**
 * Tab OC
 */
case 'oc':
	$D009_Id = $r_acaoId;	
	//$C008_Id = $_REQUEST['C008_Id'];
	
	if($g['C031']['multimarcas'] == 'S'){
    	/*$cClausula  = " select ";
	    $cClausula .= " D001_Codigo_Produto, ";
	    $cClausula .= " D001_Descricao_Produto,";
	    $cClausula .= " D009_Id";
	    $cClausula .= " from D001";
	    $cClausula .= " left join D049 on D049_D001_Id=D001_Id";
	    $cClausula .= " left join D009 on D009_D049_Id=D049_Id";
		$cClausula .= " where D001_Id='$D001_Id'";
		
		$cClausula .= "   and D009_C004_Id={$g['empresaAtual']}";

		$D001 = mysql_query($cClausula);
		$mD001=mysql_fetch_array($D001);
		
		$D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');*/
    } else {
		$D009_Id = (isset($r_acaoId) ? $r_acaoId : '');
    }
	
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'T011_Data_Emissao', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'T011_Id', 'colunaTitulo' =>'OC'),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' =>'Fornecedor'),
		array('colunaValor' => 'T012_Quantidade', 'colunaTitulo' =>'Quantidade', 'colunaAlinhar' => 'right'),
		//O campo NF não existe no select do código antigo produtos/produtos_oc.php
		//array('colunaValor' => 'NF', 'colunaTitulo' =>'NF'),
		array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' =>'Nº Pedido', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T006_Id', 'colunaTitulo' =>'Id item pedido venda', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T012_Quantidade_Falta_Lancar(T012_Id,0) as T012_Quantidade_Pendente', 'colunaTitulo' =>'', 'colunaExibir'=>false, 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'T012_Codigo_Produto', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Valor_Custo_Unitario', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Valor_Total_Custo', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Descricao_Produto', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Flag_Cancelada', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Flag_Travar_Pedido', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'D049_Codigo_Produto_Fornecedor', 'colunaTitulo' =>'', 'colunaExibir'=>false),
		array('colunaValor' => 'T012_Id', 'colunaTitulo' =>'', 'colunaExibir'=>false),
	);


	$from = 'T012';

	$extra = <<<EOT
			left join D009 on D009_Id=T012_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			left join T011 on T011_Id=T012_T011_Id
			left join T006 on T006_Id=T012_T006_Id
			left join D024 on D024_Id=T011_D024_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_Id='$D009_Id'
EOT;

	
/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	
	if($D009_Id){
		$g['smarty']->display('grid.tpl');		
	}else{
		echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
	}
break;
	
/**
 * Tab Certificados
 */
case 'certificados':
		
	$D001_Id = $r_acaoId;
	$D024_Id = isset($r_D024_Id) ? $r_D024_Id : '';
    $r_idTabForm4 = isset($r_idTabForm4) ? $r_idTabForm4 : '';
	
	$sqlD024 = "SELECT * FROM D024 WHERE D024_Id='$D024_Id'";
	$queryD024 = mysql_query($sqlD024);
	$resultD024 = mysql_fetch_array($queryD024);

	$sqlD001 = "SELECT * FROM D001 WHERE D001_Id='$D001_Id'";
	$queryD001 = mysql_query($sqlD001);
	$resultD001 = mysql_fetch_array($queryD001);
	
	
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'validaCampoExcluir1', 'colunaTitulo' =>'', 'callback' => 'validaCampoExcluir1', 'colunaTipo' => 'livre', 'style' => 'width: 1%'),
		array('colunaValor' => 'T140_Id', 'colunaTitulo' =>'ID', 'chavePrimaria'=>true, 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
		array('colunaValor' => 'T303_D001_Id', 'colunaTitulo' => 'T303_D001_Id', 'colunaExibir' => false),
		array('colunaValor' => 'IF(T140_Link != "", T140_Link, T140_Nome_Arquivo)', 'colunaTitulo' => 'Documento'),
		array('colunaValor' => 'T140_Link', 'colunaTitulo' => 'T140_Link', 'colunaExibir' => false),
		array('colunaValor' => 'abrirCertificado', 'colunaTitulo' =>'', 'colunaTipo'=>'livre', 'callback'=>'abrirCertificado', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' => 'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('T140_Id', 'documentoProduto'), 'style' => 'width: 1%'),
	);


	$from = 'T140';

	$extra = <<<EOT
		left join T303 ON T140_Id = T303_T140_Id
		where T140_D001_Id = '{$D001_Id}'
		   or T303_D001_Id = '{$D001_Id}'
	 group by T140_Id 
EOT;

	
/**
* Define ação do click de abertura (ao clicar em uma linha do grid)
*/
		$botoes = array(
		);
		echo Botoes::processar($botoes);

		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T140_Id-/, '');
				//divSetarAcaoId('{$r_idTabForm4}', id);
				//divRefresh('{$g['divId']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;

	/**
	* Filtro
	*/
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($grid);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	$g['smarty']->display('grid.tpl');
	
	break;
	
	case 'cad002GridPrecos':
		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
		$C008_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}
		$grid = array(
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Tipo', ''), 'colunaEditar' => '', 'callback' => 'mostrarTipo', 'colunaExibir' => false),
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Tipo', 'colunaExibir' => false, 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Grupo', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditarGeraSelect' => array(
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaEditarGeraSelect' => array(
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição produto'),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D049_D082_Id', 'colunaTitulo' => 'Marca', 'colunaEditarGeraSelect' => array('D082', 'D082_Id', 'D082_Marca', ''), 'colunaEditar' => '', 'callback' => 'mostrarMarca'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'D001_Qte_Embalagem', 'colunaTitulo' => 'Qtde Caixa', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Manual R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_ICF_1', 'colunaTitulo' => 'ICF', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'width:1%;', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'width: 1%'),
			array('colunaValor' => 'D009A_Valor_Custo_Unitario_Liquido', 'colunaTitulo' => 'Custo Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'style' => 'width: 1%', 'colunaExibir' => false),
			array('colunaValor' => 'D009_IPV_1', 'colunaTitulo' => 'IPV 1', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#b9f7b9;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPV_2', 'colunaTitulo' => 'IPV 2', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#b9f7b9;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPV_3', 'colunaTitulo' => 'IPV 3', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#b9f7b9;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPV_4', 'colunaTitulo' => 'IPV 4', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#b9f7b9;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
			array('colunaValor' => 'D009A_IPV_5', 'colunaTitulo' => 'IPV 5', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#b9f7b9;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPT_1', 'colunaTitulo' => 'IPT 1', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#c3e6f7;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPT_2', 'colunaTitulo' => 'IPT 2', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#c3e6f7;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPT_3', 'colunaTitulo' => 'IPT 3', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#c3e6f7;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_IPT_4', 'colunaTitulo' => 'IPT 4', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#c3e6f7;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
			array('colunaValor' => 'D009A_IPT_5', 'colunaTitulo' => 'IPT 5', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background:#c3e6f7;width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Preco_1', 'colunaTitulo' => 'Preço 1', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Preco_4', 'colunaTitulo' => 'Preço 4', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
			array('colunaValor' => 'D009A_Preco_5', 'colunaTitulo' => 'Preço 5', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D001_Preco_1', 'colunaTitulo' => 'Preço 1', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true, 'colunaExibir'=>false),
            array('colunaValor' => 'D001_Preco_2', 'colunaTitulo' => 'Preço 2', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true, 'colunaExibir'=>false),
            array('colunaValor' => 'D001_Preco_3', 'colunaTitulo' => 'Preço 3', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true, 'colunaExibir'=>false),
            array('colunaValor' => 'D001_Preco_4', 'colunaTitulo' => 'Preço 4', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaEditar' => true, 'style' => 'background-color: var(--celula-amarelo-claro);width:1%', 'colunaEditarNivel' => '2', 'colunaEditarTodos' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'D001_Peso_Unitario_Kg', 'colunaTitulo'=>'Peso Unit KG','colunaAlinhar' => 'right','colunaCasasDecimais' => '3', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'width: 1%'),
            array('colunaValor' => 'D049_Valor_Preco_KG', 'colunaTitulo' => 'Preço KG', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Flag_Promocao', 'colunaTitulo' => 'Promoção', 'colunaAlinhar' => 'center','style' => 'width: 1%', 'colunaEditarTodos' => true, 'callback'=>'gCheckbox','callbackParameter' => array('D009_Flag_Promocao', 'N|S'), 'colunaExibir'=>false),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Flag_Ecommerce', 'colunaTitulo' => 'E-co', 'callback' => 'gCheckbox','callbackParameter' => array('D001_Flag_Ecommerce', 'N|S') , 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width:1%','colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Tabela', 'colunaTitulo' => 'Custo tab.forn', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
			array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Compra', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
			array('colunaValor' => 'D009_Data_Atualizacao_Custo_Fornecedor', 'colunaTitulo' => 'Alt.Tabela', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaExibir' =>true),
			array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaExibir' => true,
			'colunaEditarGeraSelect' => array(
				array('title' => '...', 'value' => ''), 
				array('title' => 'CP-Comprado', 'value' => 'CP'),
				array('title' => 'PD-Produzido', 'value' => 'PD'),
				array('title' => 'MT-Montado', 'value' => 'MT'),
				array('title' => 'MD-Modificado', 'value' => 'MD'),
				array('title' => 'IN-Industrialização', 'value' => 'IN')
			)),
	);

		$from = 'D001';
		$extra = <<<EOT
			LEFT JOIN D003 ON D003_Id=D001_D003_Id 
			LEFT JOIN D049 ON D049_D001_Id=D001_Id 
			LEFT JOIN D009 ON D009_D049_Id=D049_Id 
			LEFT JOIN C008 ON D001_C008_Id=C008_Id 
			LEFT JOIN D082 ON D049_D082_Id=D082_Id 
			LEFT JOIN D024 ON D024_Id=D049_D024_Id 
			LEFT JOIN D060 ON D001_D060_Id=D060_Id 
			LEFT JOIN D002 ON D001_D002_Id=D002_Id 
			LEFT JOIN D015 ON D001_D015_Id=D015_Id 
			LEFT JOIN D037 ON D001_D037_Id=D037_Id 
			LEFT JOIN D005 ON D001_D005_Id=D005_Id 
			LEFT JOIN D072 ON D072_D001_Id=D001_Id 
			LEFT JOIN T066 ON T066_D009_Id=D009_Id 
			LEFT JOIN D004 ON D004_Id=T066_D004_Id 
			LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprador 
			LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' 
			LEFT JOIN D069 ON D069_D009_Id=D009_Id AND D069_Flag_Tabela_Principal='S' 
			LEFT JOIN D083 ON D083_D001_Id=D001_Id 
			LEFT JOIN D070 ON D070_Id=D069_D070_Id 
			LEFT JOIN D009A ON D009_Id=D009A_D009_Id 
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
			GROUP BY D009_Id
EOT;

		$extra_total = <<<EOT
			LEFT JOIN D003 ON D003_Id=D001_D003_Id 
			LEFT JOIN D049 ON D049_D001_Id=D001_Id 
			LEFT JOIN D009 ON D009_D049_Id=D049_Id 
			LEFT JOIN C008 ON D001_C008_Id=C008_Id 
			LEFT JOIN D082 ON D049_D082_Id=D082_Id 
			LEFT JOIN D024 ON D024_Id=D049_D024_Id 
			LEFT JOIN D060 ON D001_D060_Id=D060_Id 
			LEFT JOIN D002 ON D001_D002_Id=D002_Id 
			LEFT JOIN D015 ON D001_D015_Id=D015_Id 
			LEFT JOIN D037 ON D001_D037_Id=D037_Id 
			LEFT JOIN D005 ON D001_D005_Id=D005_Id 
			LEFT JOIN D072 ON D072_D001_Id=D001_Id 
			LEFT JOIN T066 ON T066_D009_Id=D009_Id 
			LEFT JOIN D004 ON D004_Id=T066_D004_Id 
			LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprador 
			LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' 
			LEFT JOIN D069 ON D069_D009_Id=D009_Id AND D069_Flag_Tabela_Principal='S' 
			LEFT JOIN D083 ON D083_D001_Id=D001_Id 
			LEFT JOIN D070 ON D070_Id=D069_D070_Id 
			where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
			AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
			{$filtro}
EOT;

		/* echo <<<EOT
	        <div class="buttonsBottomTop">
			<button type="button" id="cad002PrecosAtualizarEstoqueGeral">Atualizar Estoque</button>
			<button type="button" id="cad002PrecosGridAtualizarEstoqueRapido">Atualizar Estoque Rápido</button>
			<button type="button" id="cad002AplicarPrecos">Aplicar preços por peso</button>
	        </div>
EOT; */

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
	 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
	 	    {
	  	       return 'linhaCinza';
	 	    }
	 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
	 	    {
	  	       return 'linhaVermelho';
	 	    }
		}	

		/**
		 * Botões
		 */

		$botoes = array(
			array('titulo' => 'Atualizar Estoque', 'id' => 'cad002PrecosAtualizarEstoqueGeral'),
			array('titulo' => 'Atualizar Estoque Rápido', 'id' => 'cad002PrecosGridAtualizarEstoqueRapido'),
			array('titulo' => 'Aplicar preços por peso', 'id' => 'cad002AplicarPrecos'),
			//array('titulo' => 'Calcular Limite', 'id' => 'cad001CalcularLimite',  'exibirCallback' => 'return isset($g["c029Ids"][222]);'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Referência', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1,D083_Codigo_Barras' => array('titulo' => 'Cód. Barras', 'tipo' => 'texto'),
            "ifnull(D082_Marca[[VIRGULA]]'')" => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad023/grid/cad023GridTipos/', 'campo' => 'C008_Tipo', 'tamanho' => 20)),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad027/grid/cad027GridGrupos/', 'campo' => 'D060_Descricao_Grupo', 'tamanho' => 20)),
            'D003_Id,D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad026/grid/cad026GridLinhas/', 'campo' => 'D003_Nome_Linha', 'tamanho' => 20)),
            'D002_Id,D002_Descricao_Produto' => array('titulo' => 'Sub-grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad028/grid/cad028GridSubGrupos/', 'campo' => 'D002_Descricao_Produto', 'tamanho' => 20)),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.loja', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde.OC', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
			'D009_Data_Ultima_Entrada' => array('titulo' => 'Ult.Compra', 'tipo' => 'data'),
			'D009_Data_Atualizacao_Custo_Fornecedor' => array('titulo' => 'Alt.Tabela', 'tipo' => 'data'),
            'D009_Data_Alteracao_Custo' => array('titulo' => 'Alt.Custo', 'tipo' => 'data'),
            'D009_Data_Ultima_Venda' => array('titulo' => 'Ult.Venda', 'tipo' => 'data'),
            'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto', 'exibir' => false),
            'D049_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Origem', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
			'D009_Valor_Custo_Unitario' => array('titulo' => 'Custo', 'tipo' => 'numero'),
			'D009_Valor_Preco_Tabela' => array('titulo' => 'Custo Manual', 'tipo' => 'numero'),
			'D009_Valor_Custo_Tabela' => array('titulo' => 'Custo Tab.Forn', 'tipo' => 'numero'),
            'D049_Flag_Tipo' => array('titulo' => 'Tipo (CP,PD,MT,MD,IN)?', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$g['cacheCount'] = true;
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extra_total = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extra_total);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		$geradoSql = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000, false, false, true);

		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$etapas = round($valorTotal / 1) ; 

		$extraParam = base64_encode($extra);
		$sqlParam = base64_encode($geradoSql);
		$extraParam = base64_encode($extra);
		/**
	    * Botões (Versão Acima do GRID)
	    */
	    $sql_ = urlencode(base64_encode($geradoSql));
	    $extra_ = base64_encode($extra);

	    echo <<<EOT
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

                $('#cad002PrecosAtualizarEstoqueGeral').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$('#{$g['divId']}').showLoading();
							cad002PrecosAtualizarEstoqueGeralLoop(idProgressBar,1,0,0);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
		        });
		        function cad002PrecosAtualizarEstoqueGeralLoop(idProgressBar, etapa, erro, ok){
                    $.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueGeral/?extraSql=' + encodeURIComponent('{$extra_}') + '&ajax=true&callback=?'+'&valorTotal=$valorTotal&etapas=$etapas&etapa=' + etapa, function(request) {
                        if (request.etapa<=$etapas && request.concluido == 0) {
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                        	cad002PrecosAtualizarEstoqueGeralLoop(idProgressBar,request.etapa, erro, ok);
                        }else{
                        	erro += request.erro;
                        	ok += request.ok;
                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
                        	dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                        	if (request.code) {
	                            divRefresh('{$g['divId']}');
	                        } else {
	                            dialogConfirm('Erro!', request.data);
	                        }
                        } 
	                });	                    
				}
				
				$('#cad002PrecosGridAtualizarEstoqueRapido').unbind('click').bind('click', function() {
					var buttons = {
						"Sim": function() {
							$(this).dialog("destroy").remove();
							idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
							$('#{$g['divId']}').showLoading();
							cad002PrecosAtualizarEstoqueRapidoLoop(idProgressBar,1,0,0);
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Confirmação', 'Confirma a atualização do estoque?', buttons);
				});
				function cad002PrecosAtualizarEstoqueRapidoLoop(idProgressBar, etapa, erro, ok){
					$.getJSON('/cad/cad002/grid_func-ajax/atualizarEstoqueRapido/?extraSql='+ encodeURIComponent('{$extra_}') + '&ajax=true&callback=?'+'&valorTotal=$valorTotal&etapas=$etapas&etapa=' + etapa, function(request) {
						if (request.etapa<=$etapas && request.concluido == 0) {
							erro += request.erro;
							ok += request.ok;
							$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
							cad002PrecosAtualizarEstoqueRapidoLoop(idProgressBar,request.etapa, erro, ok);
						}else{
							erro += request.erro;
							ok += request.ok;
							$('#dc-' + idProgressBar).dialog("destroy").remove();
							dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + ok + '</b><br/>Produtos com erro: <b> ' + erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
							if (request.code) {
								divRefresh('{$g['divId']}');
							} else {
								dialogConfirm('Erro!', request.data);
							}
						} 
					});	                    
				}

				$('#cad002AplicarPrecos').unbind('click').bind('click', function() {
	                var buttons = {
	                    "Sim": function() {
	                        $('#{$g['divId']}').showLoading();
	                        $.getJSON('/cad/cad002/grid_func-ajax/atualizarPrecosPorPeso/?extraSql={$sql_}&ajax=true&callback=?', function(request) {
	                            $('#{$g['divId']}').hideLoading();
	                            if (request.code) {
	                                dialogConfirm('Ok!', '<b>Produtos atualizados:</b> ' + request.ok + '<br /><b>Produtos com erro:</b> ' + request.erro + '<br /><br /><b>Erros:</b><br /> ' + request.erros);
	                                divRefresh('{$g['divId']}');
	                            } else {
	                                dialogConfirm('Erro!', request.data);
	                            }
	                        });
	                        $(this).dialog("destroy").remove();
	                    },
	                    "Não": function() { $(this).dialog("destroy").remove(); }
	                }
	                dialogConfirm('Atenção', '<b>Atenção</b>!!!<br />Esta processo irá alterar os preços dos produtos filtrados. Deseja continuar?', buttons);
	            });
	        } </script></div>
EOT;


	/*	if($g['conf']->ini('usarICFeIPV') == "S"){
			echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">ICF<br><input onkeypress="return(Verifica_Caracter(event, this));" onchange="return(Corrige_Valor(event, this,4));" class="aplicarItens" type="text" class="whiteBg" id="aplicarValorICF" size="11"></span>
				<span class="item" style="text-align:right">IPV 1<br><input onkeypress="return(Verifica_Caracter(event, this));" onchange="return(Corrige_Valor(event, this,4));" class="aplicarItens" type="text" class="whiteBg" id="aplicarValorIPV1" size="11"></span>
				<span class="item" style="text-align:right">IPV 2<br><input onkeypress="return(Verifica_Caracter(event, this));" onchange="return(Corrige_Valor(event, this,4));" class="aplicarItens" type="text" class="whiteBg" id="aplicarValorIPV2" size="11"></span>
				<span class="item" style="text-align:right">IPV 3<br><input onkeypress="return(Verifica_Caracter(event, this));" onchange="return(Corrige_Valor(event, this,4));" class="aplicarItens" type="text" class="whiteBg" id="aplicarValorIPV3" size="11"></span>
				<span class="item" style="text-align:right">Custo manual<br><input onkeypress="return(Verifica_Caracter(event, this));" onchange="return(Corrige_Valor(event, this,4));" class="aplicarItens" type="text" class="whiteBg" id="aplicarValorCustoManual" size="11"></span>
				<span class="item" style="text-align:right">Promoção<br><input class="classePromo" type="checkbox" class="whiteBg" id="aplicarValorPromocao" size="11"></span>
			</div>
	EOT;
		}else{
			echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">% Desconto 1<br><input class="aplicarItens" type="text" class="whiteBg" id="aplicarValorDesconto1" size="11"></span>
				<span class="item" style="text-align:right">% Desconto 2<br><input class="aplicarItens" type="text" class="whiteBg" id="aplicarValorDesconto2" size="11"></span>
				<span class="item" style="text-align:right">% Desconto 3<br><input class="aplicarItens" type="text" class="whiteBg" id="aplicarValorDesconto3" size="11"></span>
				<span class="item" style="text-align:right">Promoção<br><input class="classePromo" type="checkbox" class="whiteBg" id="aplicarValorPromocao" size="11"></span>
			</div>
	EOT;
		} 

		$alert = gGeraAlertMsg('Concluído', 'Concluído!', false);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="cad002AplicarValores">Aplicar</button>
				<!-- <button type="button" id="cad002RetornarPrecos">Retornar Preços</button> -->
				<button type="button" id="cad002RetornarPrecosPromocao">Retornar Preços Promoção</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad002RetornarPrecos').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Retornar Preços', '/cad/cad002/content/cad002ContentRetornarPrecos/', '&extraSql=' + encodeURIComponent('{$extraParam}'), [1000,700]);
				});
				$('#cad002RetornarPrecosPromocao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Retornar Preços Promoção', '/cad/cad002/content/cad002ContentRetornarPrecosPromocao/', '&extraSql=' + encodeURIComponent('{$extraParam}'), [1000,700]);
				});
				$('#cad002AplicarValores').unbind('click').bind('click', function(data) {
					var buttons = { 
						"Ok": function() { 
							$('#{$g['divId']}').showLoading();
							var ICF = $('#aplicarValorICF').val();
							var IPV1 =  $('#aplicarValorIPV1').val();
							var IPV2 =  $('#aplicarValorIPV2').val();
							var IPV3 =  $('#aplicarValorIPV3').val();
							var CustoManual =  $('#aplicarValorCustoManual').val();
							var Desconto1 =  $('#aplicarValorDesconto1').val();
							var Desconto2 =  $('#aplicarValorDesconto2').val();
							var Desconto3 =  $('#aplicarValorDesconto3').val();
							var Promocao =   $('#aplicarValorPromocao').prop('checked');
							if(ICF == undefined){ICF=""}
							if(IPV1 == undefined){IPV1=""}
							if(IPV2 == undefined){IPV2=""}
							if(IPV3 == undefined){IPV3=""}
							if(CustoManual == undefined){CustoManual=""}
							if(Desconto1 == undefined){Desconto1=""}
							if(Desconto2 == undefined){Desconto2=""}
							if(Desconto3 == undefined){Desconto3=""}
							if(Promocao == undefined){Promocao=""}
							$.getJSON('/cad/cad002/grid_func-ajax/aplicarValores/?ajax=true&sqlParam=' + encodeURIComponent('{$sqlParam}') + '&ICF=' + encodeURIComponent(ICF) 
																																	       + '&IPV1=' + encodeURIComponent(IPV1)
																																	       + '&IPV2=' + encodeURIComponent(IPV2)
																																	       + '&IPV3=' + encodeURIComponent(IPV3)
																																	       + '&CustoManual=' + encodeURIComponent(CustoManual)
																																	       + '&Desconto1=' + encodeURIComponent(Desconto1)
																																	       + '&Desconto2=' + encodeURIComponent(Desconto2)
																																	       + '&Desconto3=' + encodeURIComponent(Desconto3)
																																	       + '&Promocao=' + encodeURIComponent(Promocao) 
																																	       + '&extraParam=' + encodeURIComponent('{$extraParam}')
																																		   + '&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
								if (request.code) {
									{$alert}
									//$('#D021_D020_Id').html(request.data['D021_D020_Id']);
									divRefresh('{$g['divId']}');
								} else {
									var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
	    							//divRefresh('{$g['divId']}');
	    							dialogConfirm("Erro!", request.data, buttons)
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Fechar": function() { $(this).dialog("destroy").remove(); } 
					};
					dialogConfirm("Confirmação", 'Tem certeza que deseja aplicar os valores informados?', buttons)
				});
			} </script></div>
	EOT;
	*/
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
		$count = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0, false, true);
		if ($count > 3500) { 
			echo '<div class="gridMsgFiltre">Consulta retornou muitos registros ('.$count.').<br/>Por favor, utilize o filtro.</div>'; 
			die(); 
		}
		*/

		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		
		/*
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra_total));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			   var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
			   		// somente executa a ação ao clicar na linha ou na coluna
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					// pega o ID da linha
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace(/D001_Id-/, '');
					var D024_Id = id[1].replace(/D049_D024_Id-/, '');
			       abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
			   });
			   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$ts = microtime(true);
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		//print_r($gridTabela);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	
	case 'cad002GridCustos':

	$C008_Id = $r_acaoId;
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D041_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D001_C008_Id', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Tipo','' ), 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'mostrarTipo'),
		array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo','colunaEditarOculto' => true, 'colunaEditarTodos' => true,
		'colunaEditarGeraSelect' => array(
									array('title' => 'Sim', 'value' => 'S'), 
									array('title' => 'Não', 'value' => 'N'))
		),
		array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré','colunaEditarOculto' => true,  'colunaEditarTodos' => true,
		'colunaEditarGeraSelect' => array(
									array('title' => 'Sim', 'value' => 'S'), 
									array('title' => 'Não', 'value' => 'N'))
		),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
		array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN', 'colunaEditarOculto' => true),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português', 'colunaEditarOculto' => true),
        array('colunaValor'=>'D049_D082_Id', 'colunaTitulo'=>'Marca', 'colunaEditarGeraSelect' => array('D082', 'D082_Id', 'D082_Marca','' ), 'colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'callback' => 'mostrarMarca'),
		//array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês', 'colunaEditar' => true, 'style' => 'width:200px'),
		array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D037_Id', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade','' ), 'colunaEditarOculto' => true, 'colunaEditarTodos' => true,  'callback' => 'mostrarUnidade'),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque'),
		//array('colunaValor'=>"D001_Peso_Unitario_Kg", 'colunaTitulo'=>'Peso Liq','colunaAlinhar' => 'right','colunaCasasDecimais' => '3','colunaEditar' => true),
		//array('colunaValor'=>"D001_Peso_Total_Kg", 'colunaTitulo'=>'Peso Brt','colunaAlinhar' => 'right','colunaCasasDecimais' => '3','colunaEditar' => true),
		array('colunaValor'=>'D049_Valor_Preco_Tabela', 'colunaTitulo'=>'Tabela Fornecedor', 'colunaAlinhar' => 'right','colunaEditarOculto' => true, 'colunaEditarTodos' => true, 'colunaCasasDecimais' => '2'),
		array('colunaValor'=>'D049_ICF', 'colunaTitulo'=>'ICF', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '4'),
		array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo trabalho', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
		//array('colunaValor'=>"D041_Valor_Base", 'colunaTitulo'=>'Preço 1','colunaAlinhar' => 'right','colunaCasasDecimais' => '2','colunaEditar' => true),
		//array('colunaValor'=>"D041_Valor_Preco_Consumidor", 'colunaTitulo'=>'Preço 2','colunaAlinhar' => 'right','colunaCasasDecimais' => '2','colunaEditar' => true),
		//array('colunaValor'=>"D041_Valor_Preco_Revenda", 'colunaTitulo'=>'Preço 3','colunaAlinhar' => 'right','colunaCasasDecimais' => '2','colunaEditar' => true),
		//array('colunaValor'=>'D049_IPV', 'colunaTitulo'=>'IPV', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '4'),
		//array('colunaValor'=>'D049_ICF1', 'colunaTitulo'=>'ICF 2', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '4'),
		//array('colunaValor'=>'D049_ICF2', 'colunaTitulo'=>'ICF 3', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '4'),
        //array('colunaValor'=>'D009_Flag_Preco_Tabelado', 'colunaTitulo'=>"Preço tabelado?",'colunaEditar' => true, 'colunaAlinhar' => 'center',
        //'colunaEditarGeraSelect' => array(
        //                            array('title' => 'Sim', 'value' => 'S'), 
        //                            array('title' => 'Não', 'value' => 'N'))),
		array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
        array('colunaValor'=>'D149_Aliquota_IPI', 'colunaTitulo'=>'Aliq IPI', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
        //array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Ùlt.custo', 'colunaAlinhar' => 'right','colunaEditar' => false,'colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D082_Marca", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"C008_Tipo", 'colunaTitulo'=>'','colunaExibir' => false),
		
	);

	$from = 'D001';
	$extra = <<<EOT
				left join D049 on D049_D001_Id=D001_Id 
				left join D003 on D003_Id=D001_D003_Id
				left join D041 on D041_D049_Id=D049_Id 
				left join D009 on D009_D049_Id=D049_Id
				left join C008 on D001_C008_Id=C008_Id
				left join D082 on D049_D082_Id=D082_Id
				left join D024 on D024_Id=D049_D024_Id
				left join D060 on D001_D060_Id=D060_Id
				left join D002 on D001_D002_Id=D002_Id
				left join D015 on D001_D015_Id=D015_Id
				left join D037 on D001_D037_Id=D037_Id
				left join D005 on D001_D005_Id=D005_Id
				left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
				left join D072 on D072_D001_Id=D001_Id
				where
					(D009_C004_Id='{$g['c004']['C004_Id']}'
				or 
					D009_Id is null)
				AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
EOT;

	$extra_total = <<<EOT
				left join D049 on D049_D001_Id=D001_Id 
				left join D003 on D003_Id=D001_D003_Id
				left join D009 on D009_D049_Id=D049_Id
				left join C008 on D001_C008_Id=C008_Id
				left join D082 on D049_D082_Id=D082_Id
				left join D024 on D024_Id=D049_D024_Id
				left join D060 on D001_D060_Id=D060_Id
				left join D002 on D001_D002_Id=D002_Id
				left join D015 on D001_D015_Id=D015_Id
				left join D037 on D001_D037_Id=D037_Id
				left join D005 on D001_D005_Id=D005_Id
				left join D072 on D072_D001_Id=D001_Id
				where
					(D009_C004_Id='{$g['c004']['C004_Id']}'
				or 
					D009_Id is null)
				AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
EOT;

	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	


	/**
	* Botões (Versão Acima do GRID)
	
	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002Incluir">Incluir</button>
			
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Novo Cadastro', '/cad/cad002/content/form1/', '', [550,550]);
			});
		} </script></div>
EOT;
*/ 


	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
		'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
		'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
		'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'moeda'),
        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
	);

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	$extra_total = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extra_total);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
	
	/**
	* Botões (Versão Acima do GRID)
	*/
	$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(distinct D009_Id)", $from, $extra_total);
	$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(D009_Quantidade_Estoque*D049_Valor_Preco_Tabela)", $from, $extra_total));
	$sqlParaUp = base64_encode($geradoSql);
	echo <<<EOT
		<div class="buttonsBottomTop">
			<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
			
			<span class="item" style="text-align:right">Total Custo Manual<br><input type="text" value="{$valorTotal2}" class="whiteBg" id="" size="11" readonly></span>

			<span class="item" style="text-align:right"> &nbsp; | &nbsp; <br> &nbsp; | &nbsp; </span>

			<!-- <span class="item" style="text-align:right">IPV<br><input id="cad002AlterarIPV" type="text" value="0,0000" onkeypress="return(Formatar_Valor('0',this,'.',',',event,4,14));" class="whiteBg" id="" size="11" style="width:60px"></span>
			<span class="item" style="text-align:right">&nbsp;<br><input type="submit" value="Alterar" class="ui-state-default ui-corner-all"style="cursor: pointer;" onclick="AlterarIPV($('#cad002AlterarIPV'), '{$g['divId']}', '{$sqlParaUp}'); return false;"></span> -->
			
			<span class="item" style="text-align:right">ICF<br><input id="cad002AlterarICF1" type="text" value="0,0000" onkeypress="return(Formatar_Valor('0',this,'.',',',event,4,14));" class="whiteBg" id="" size="11" style="width:60px"></span>
			<span class="item" style="text-align:right">&nbsp;<br><input type="submit" value="Alterar" class="ui-state-default ui-corner-all" style="cursor: pointer;" onclick="AlterarICF1($('#cad002AlterarICF1'), '{$g['divId']}', '{$sqlParaUp}'); return false;"></span>
			
			<!-- <span class="item" style="text-align:right">ICF 2<br><input id="cad002AlterarICF2" type="text" value="0,0000" onkeypress="return(Formatar_Valor('0',this,'.',',',event,4,14));" class="whiteBg" id="" size="11" style="width:60px"></span>
			<span class="item" style="text-align:right">&nbsp;<br><input type="submit" value="Alterar" class="ui-state-default ui-corner-all" style="cursor: pointer;" onclick="AlterarICF2($('#cad002AlterarICF2'), '{$g['divId']}', '{$sqlParaUp}'); return false;"></span>
			
			<span class="item" style="text-align:right">ICF 3<br><input id="cad002AlterarICF3" type="text" value="0,0000" onkeypress="return(Formatar_Valor('0',this,'.',',',event,4,14));" class="whiteBg" id="" size="11" style="width:60px"></span>
			<span class="item" style="text-align:right">&nbsp;<br><input type="submit" value="Alterar" class="ui-state-default ui-corner-all" style="cursor: pointer;" onclick="AlterarICF3($('#cad002AlterarICF3'), '{$g['divId']}', '{$sqlParaUp}'); return false;"></span> -->
			
			<div style="clear:both;"></div>
		</div>
EOT;
	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	$novoIdTelaForm = uniqid();
	echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
		   		// somente executa a ação ao clicar na linha ou na coluna
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha
				var id = $(this).closest('tr').attr('id').split('|');
				var D001_Id = id[0].replace(/D001_Id-/, '');
				var D024_Id = id[1].replace(/D024_Id-/, '');
		       abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,600]);
		   });
		   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
	

	case 'cad002GridOutros':

	$C008_Id = $r_acaoId;
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D001_C008_Id', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Tipo','' ), 'colunaEditar' => true, 'callback' => 'mostrarTipo'),
		array('colunaValor'=>'D001_D060_Id', 'colunaTitulo'=>'Grupo', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('D060', 'D060_Id', 'D060_Descricao_Grupo','' ), 'colunaEditar' => true, 'callback' => 'mostrarGrupo'),
		array('colunaValor'=>'D001_D002_Id', 'colunaTitulo'=>'Sub-Grupo', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('D002', 'D002_Id', 'D002_Grupo','' ), 'colunaEditar' => true, 'callback' => 'mostrarSubGrupo'),
		array('colunaValor'=>'D001_D003_Id', 'colunaTitulo'=>'Linha', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('D003', 'D003_Id', 'D003_Nome_Linha','' ), 'colunaEditar' => true, 'callback' => 'mostrarLinha'),
		array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo','colunaEditar' => true,
		'colunaEditarGeraSelect' => array(
									array('title' => 'Sim', 'value' => 'S'), 
									array('title' => 'Não', 'value' => 'N'))
		),
		array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré','colunaEditar' => true,
		'colunaEditarGeraSelect' => array(
									array('title' => 'Sim', 'value' => 'S'), 
									array('title' => 'Não', 'value' => 'N'))
		),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
		array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN', 'colunaEditar' => true),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português', 'colunaEditar' => true),
		array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês', 'colunaEditar' => true, 'style' => 'width:200px'),
		array('colunaValor'=>'D049_D082_Id', 'colunaTitulo'=>'Marca', 'colunaEditarGeraSelect' => array('D082', 'D082_Id', 'D082_Marca','' ), 'colunaEditar' => true, 'callback' => 'mostrarMarca'),
		array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D037_Id', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'left', 'colunaEditarGeraSelect' => array('D037', 'D037_Id', 'D037_Unidade','' ), 'colunaEditar' => true, 'callback' => 'mostrarUnidade'),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque'),
		array('colunaValor'=>"D001_Peso_Unitario_Kg", 'colunaTitulo'=>'Peso Liq','colunaAlinhar' => 'right','colunaCasasDecimais' => '3','colunaEditar' => true),
		array('colunaValor'=>"D001_Peso_Total_Kg", 'colunaTitulo'=>'Peso Brt','colunaAlinhar' => 'right','colunaCasasDecimais' => '3','colunaEditar' => true),
		array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>"D082_Marca", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"C008_Tipo", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"D003_Nome_Linha", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"D002_Grupo", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"D060_Descricao_Grupo", 'colunaTitulo'=>'','colunaExibir' => false),
	);

	$from = 'D049';
	$extra = <<<EOT
				left join D001 on D001_Id=D049_D001_Id 
				left join D003 on D003_Id=D001_D003_Id
				left join D041 on D041_D049_Id=D049_Id 
				left join D009 on D009_D049_Id=D049_Id
				left join C008 on D001_C008_Id=C008_Id
				left join D082 on D049_D082_Id=D082_Id
				left join D024 on D024_Id=D049_D024_Id
				left join D060 on D001_D060_Id=D060_Id
				left join D002 on D001_D002_Id=D002_Id
				left join D015 on D001_D015_Id=D015_Id
				left join D037 on D001_D037_Id=D037_Id
				left join D005 on D001_D005_Id=D005_Id
				left join D072 on D072_D001_Id=D001_Id
				where
					(D009_C004_Id='{$g['c004']['C004_Id']}'
				or 
					D009_Id is null)
				AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
EOT;

	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	


	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002GerarXML">Gerar XML</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002GerarXML').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar XML', '/cad/cad002/content/formXML/', '', [300,200]);
			});			
		} </script></div>
EOT;


	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
		'D001_Codigo_Produto,D001_Foto' => array('titulo' => 'Código', 'tipo' => 'numero'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
		'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
		'D001_Flag_Ativo' => array('titulo' => 'Ativos (S/N)?', 'tipo' => 'texto'),
		'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
		'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
	);

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
	

	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	$novoIdTelaForm = uniqid();
	echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
		   		// somente executa a ação ao clicar na linha ou na coluna
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha
				var id = $(this).closest('tr').attr('id').split('|');
				var D001_Id = id[0].replace(/D001_Id-/, '');
				var D024_Id = id[1].replace(/D024_Id-/, '');
		       abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,600]);
		   });
		   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
			$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'cad002GridFotoProduto':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'campoExclusao', 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T172_Id', $g['divId']),'style' => 'width: 1%'),
			array('colunaValor' => 'T172_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'style' => 'width: 1%;'),
			array('colunaValor' => 'Foto', 'colunaTipo'=>'livre', 'colunaTitulo' => 'Foto', 'style' => 'width: 1%;text-align: center;background-color: white;', 'callback'=>'mostrarImagemProduto'),
			array('colunaValor' => 'T172_D001_Id', 'colunaTitulo' => 'Produto', 'colunaExibir' => false),
			array('colunaValor' => "if(T172_Link_Video != '', T172_Link_Video, T172_Nome_Arquivo) as nomeArquivo ", 'colunaTitulo' => 'Arquivo', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T172_Link_Video', 'colunaTitulo' => 'T172_Link_Video', 'colunaExibir' => false),
			array('colunaValor' => 'T172_Nome_Arquivo', 'colunaTitulo' => 'T172_Nome_Arquivo', 'colunaExibir' => false),
			array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Abrir', 'colunaTipo' => 'livre', 'callback'=>'abrirFotoProduto','style' => 'width: 60px', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'visualizarArquivo', 'colunaTitulo' => 'Visualizar', 'colunaTipo'=>'livre', 'callback'=>'visualizarArquivo', 'callbackParameter' => array('T172_Id', 'fotoProduto'), 'style' => 'width: 60px','colunaAlinhar' => 'center'),
			array('colunaValor' => '', 'colunaTipo' => 'livre'),
		);

		/**
		*
		* Definição da SQL
		*/
		$from = 'T172';

		$extra = <<<EOT
			where 
			(T172_D001_Id = {$r_acaoId})
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D087_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		$novoIdTelaForm = uniqid();
	break;
	
	case 'cad002gridInativos':

	
	$C008_Id = $r_acaoId;
	/**
	 * Definição do grid tabela
	 */
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
			$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
			$filtro = '';
		}

		$grid = array(
		    array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/produto/', 'D001_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;'),
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Depto','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Id, C008_Tipo', "ORDER BY C008_Id", true), 'colunaExibir' => false),
		    array('colunaValor' => 'D001_D060_Id', 'colunaTitulo' => 'Setor','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', "ORDER BY D060_Id", true), 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
		    array('colunaValor' => 'D001_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaEditar' => true, 'colunaEditarNivel' => 2),
		    array('colunaValor' => 'D001_Flag_Ativo', 'colunaTitulo' => 'Ativo', 'colunaEditar' => true, 'colunaEditarNivel' => 2, 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré', 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '720')),
		    array('colunaValor' => 'D001_Codigo_Barra1', 'colunaTitulo' => 'EAN Fab', 'colunaEditar' => true, 'colunaEditarNivel' => 2, 'colunaExibir' => false),
		    array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		    array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaEditar' => true, 'colunaEditarNivel' => 2),
		    array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		);
	$from = 'D001';
	$extra = <<<EOT
			left join D003 on D003_Id=D001_D003_Id
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join C008 on D001_C008_Id=C008_Id
			left join D082 on D049_D082_Id=D082_Id
			left join D024 on D024_Id=D049_D024_Id
			left join T066 on T066_D009_Id=D009_Id
			where
					(D009_C004_Id='{$g['empresaAtual']}'
				or 
					D009_Id is null)
				AND
					 D001_Flag_Ativo="N"
					 {$filtro}
				GROUP BY D009_Id
EOT;

	/**
	* Botões (Versão Acima do GRID)
	
	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002Incluir">Incluir</button>
			
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Novo Cadastro', '/cad/cad002/content/form1/', '', [550,550]);
			});
		} </script></div>
EOT;
*/
	
	/**
	 * Botões
	 */
	$botoes = array(
	);
	echo Botoes::processar($botoes);


	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	

	/**
	* Filtro
	*/
		$gridFiltro = array(
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D001_Codigo_Barras,D001_Codigo_Barra1' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            "ifnull(D082_Marca[[VIRGULA]]'')" => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'D003_Id,D003_Nome_Linha' => array('titulo' => 'Linha', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Sub-grupo', 'tipo' => 'texto'),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estoq.físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoq.líquido', 'tipo' => 'numero'),
            'D037_Unidade' => array('titulo' => 'Unidade', 'tipo' => 'texto'),
            'D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D069_D070_Id' => array('titulo' => 'Tabela Fornecedor', 'tipo' => 'texto', 'exibir' => false),
            'D049_Modelo' => array('titulo' => 'Modelo', 'tipo' => 'texto'),
            'D049_Origem_Mercadoria' => array('titulo' => 'Origem', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ), 'naoGerarExtra' => ''),
        );
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		

		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );


		/**
		* Botões (Versão Acima do GRID)
		*/
		/*
		if (!personalizacaoTotais()) { 
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		 echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		}*/
	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	       $novoIdTelaForm = uniqid();
	       echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
		               	// somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
		               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,600]);
		           });
		           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	* Geração: Grid
	*/
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
		
	case 'cad002GridServicos':

			/**
			* Definição do Grid (Tabela)
			*/
			$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D135_Id', $g['divId'])),
			array('colunaValor' => 'D135_Id', 'colunaTitulo' => 'Id', 'chavePrimaria'=>true, 'colunaExibir'=>false),
			array('colunaValor' => 'D112_Id', 'colunaTitulo' => 'ID'),
			array('colunaValor' => 'D112_Titulo', 'colunaTitulo' => 'Serviço'),
			array('colunaValor' => 'D112_Descricao', 'colunaTitulo' => 'Descrição'),
			);

			/**
			*
			* Definição da SQL
			*/
			$from = 'D135';

			$extra = <<<EOT
				left join D112 on D112_Id=D135_D112_Id
				where 
				D135_D001_Id = {$r_acaoId}
EOT;

			/**
			* Filtro
			*/
			// Gera o filtro e acrescenta ao $extra
			$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			//echo $g['ui']->gridFiltroPrint($grid);

			/**
			* Geração: Monta o SQL e retorna o dados
			*/
			list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

			/**
			* Geração: Grid
			*/
			$gridTabela = $g['ui']->grid($grid, $geradoDados);
			$g['smarty']->assign('thead', $gridTabela['thead']);
			$g['smarty']->assign('tbody', $gridTabela['tbody']);
			$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
			$g['smarty']->assign('gridId', md5($g['r_']));
			$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
			gBotaoAuditoria($from);
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

			$novoIdTelaForm = uniqid();
		break;
		
	/**
	 * TAB Códigos Duplicados
	 */
		case 'cad002gridCodigosDuplicados':

	
	$C008_Id = $r_acaoId;
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'C008_Tipo', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left'),
		array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português'),
		array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês'),
		array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
		array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>"if(D149_Flag_ST='S','ST','') as ST", 'colunaTitulo'=>'ST', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>'D049_Flag_Tipo', 'colunaTitulo'=>'TP', 'colunaAlinhar' => 'center'),
		array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'center'),
		//array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo', 'colunaAlinhar' => 'right'),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque'),
		//array('colunaValor'=>'D009_Quantidade_Estoque_Real', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'right'),
		array('colunaValor'=>'D009_Quantidade_Estoque_Liquido', 'colunaTitulo'=>'Líquido', 'colunaAlinhar' => 'right'),
		array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
		array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Último Fornecedor'),
		//array('colunaValor'=>'D015_Nome_Grupo', 'colunaTitulo' => 'Aplicação'),
	);
//left join D083 on D083_D024_Id=D024_Id and D083_D001_Id=D001_Id and D083_D049_Id=D049_Id
	$from = 'D001 as T1';
	$extra = <<<EOT
				left join D003 on D003_Id=D001_D003_Id
				left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join C008 on D001_C008_Id=C008_Id
				left join D082 on D049_D082_Id=D082_Id
				left join D024 on D024_Id=D049_D024_Id
				left join D060 on D001_D060_Id=D060_Id
				left join D002 on D001_D002_Id=D002_Id
				left join D015 on D001_D015_Id=D015_Id
				left join D037 on D001_D037_Id=D037_Id
				left join D005 on D001_D005_Id=D005_Id
				left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
				left join D072 on D072_D001_Id=D001_Id
				left join T066 on T066_D009_Id=D009_Id
				left join D004 on D004_Id=T066_D004_Id
				where (Select Count(*) from D001 as T2 where T1.D001_Codigo_Produto = T2.D001_Codigo_Produto AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null))>1
				ORDER BY T1.D001_Codigo_Produto
EOT;
	
	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	

	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
		"D001_Codigo_Produto,D001_Foto" => array('titulo' => 'Código', 'tipo' => 'numero'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
		'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
		'D001_Flag_Ativo' => array('titulo' => 'Ativos (S/N)?', 'tipo' => 'texto'),
		'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
		'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
	);
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		// echo $geradoSql;
		// die();
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		if (!personalizacaoTotais()) { 
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, false);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		}
	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	       $novoIdTelaForm = uniqid();
	       echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		           var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
		               	// somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
		               abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto - ID: ' + D001_Id, '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&D024_Id=' + encodeURIComponent(D024_Id), [980,550]);
		           });
		           $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	* Geração: Grid
	*/
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;
    
    case 'cad002GridHistoricoGeral':
        

	$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
	//$C008_Id = $_REQUEST['C008_Id'];
    $D001_Id = $r_acaoId;
        
    /**
     * Definição do grid tabela
     */
    
    $cClausula  = " select ";
    $cClausula .= " D001_Codigo_Produto, ";
    $cClausula .= " D001_Descricao_Produto,";
    $cClausula .= " D009_Id";
    $cClausula .= " from D001";
    $cClausula .= " left join D049 on D049_D001_Id=D001_Id";
    $cClausula .= " left join D009 on D009_D049_Id=D049_Id";
    $cClausula .= " where D049_Id='$D001_Id'";
    $cClausula .= "   and D009_C004_Id={$g['empresaAtual']}";

    $D001 = mysql_query($cClausula);
    $mD001=mysql_fetch_array($D001);
    
    $D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');
    $filtro = '';
	if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
		$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
	}else{
		$filtro = '';
	}

    
	$grid = array(
        array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'editarHistorico', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
        array('colunaValor' => 'T001_Lote_Validade', 'colunaTitulo' => 'Sub-item', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
        array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar%' => 'center', 'colunaDimensao' => 'S'),
	    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
	    array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'),),
	    array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaEditar' => true, 'colunaEditarNivel' => 2),
        array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'substr(if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\') and T001_T008_Id is null,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa),1,20) as Cliente', 'colunaTitulo' => 'Empresa'),
        array('colunaValor' => 'if(Cli.D024_CNPJ is null,Forn.D024_CNPJ,Cli.D024_CNPJ)', 'colunaTitulo' => 'CNPJ', 'style' => 'font-size:10px', 'colunaExibirNivel' => 2, 'colunaExibir' => false),
        array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'Descrição'),
        array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
        array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Demonstrativo', '/cad/cad002/outro/demonstrativoCusto/', 'T001_Id', 'right', 5, '', '', '320'), 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_Custo_Medio_Unitario as Medio', 'colunaTitulo' => 'Médio', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_Preco_Unitario as Preco', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
        array('colunaValor' => 'IPV', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvHistorico', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre', 'colunaCasasDecimais' => '4', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) as T001_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaMetrica' => 'S'),
        array('colunaValor' => 'T001_Quantidade_Estoque_Inicial', 'colunaTitulo' => 'Inicial', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
		array('colunaValor' => '(T001_Valor_Preco_Unitario*T001_Quantidade) as totalPreco', 'colunaTitulo' => 'Total preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
		array('colunaValor' => 'T001_Flag_Operacao', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
        array('colunaValor' => 'T001_Flag_MMF', 'colunaTitulo' => 'F', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Flag_Atualiza_Custo', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center', 'colunaExibirNivel' => 2),
        array('colunaValor' => "CONCAT(D004_Local, ' - ', T066_Id) as D004_Local", 'colunaTitulo' => 'Local', 'colunaAlinhar' => 'left', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_T007_Id', 'colunaTitulo' => 'T001_T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_T013_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Flag_Devolucao', 'colunaTitulo' => '', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Observacao', 'colunaTitulo' => 'Observacao', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Numero_Controle', 'colunaTitulo' => 'CNPJ', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_T014_Id', 'colunaTitulo' => 'T001_T014_Id', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir' => false, 'chavePrimaria' => true, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T007_Id', 'colunaTitulo' => 'T007_Id', 'colunaExibir' => false, 'chavePrimaria' => true, 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Aliquota_ICMS', 'colunaTitulo' => '%ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Percentual_Frete', 'colunaTitulo' => '%Frete', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Percentual_Custo_Financeiro', 'colunaTitulo' => '%C.F.', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-amarelo-claro)', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_ST', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_Frete', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
        array('colunaValor' => 'T001_Valor_Custo_Financeiro', 'colunaTitulo' => 'C.F. R$', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibirNivel' => 2),
    );

	$from = 'T001';

	$extra = <<<EOT
		left join D024 as Forn on Forn.D024_Id=T001_D024_Id
		left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
		left join D006 on D006_Id=T001_D006_Id
		left join D009 on D009_Id=T001_D009_Id
		left join D049 on D049_Id=D009_D049_Id
		left join D001 on D001_Id=D049_D001_Id
		left join C004 on C004_Id=D009_C004_Id
		left join T066 on T066_Id=T001_T066_Id
		left join D004 on D004_Id=T001_D004_Id
		left join T014 on T014_Id=T001_T014_Id
		left join T013 on T013_Id=T014_T013_Id
		left join T008 on T008_Id=T001_T008_Id
		left join T007 on T007_Id=T008_T007_Id
		where D009_C004_Id='{$g['empresaAtual']}'
		{$filtro}
		order by T001_Data_Lancamento desc,T001_Id desc

EOT;

    /**
    * Função que define classes CSS para cada linha do grid
    */
    function gridLinha($array) {
         if ($array['todosCampos']['T001_Flag_Operacao']=='E') 
         {
             return 'linhaVerde';
         }
         if ($array['todosCampos']['T001_Flag_Operacao']=='C') 
         {
             return 'linhaVermelho';
         }
         if ($array['todosCampos']['T001_Flag_Operacao']=='B') 
         {
             return 'linhaAzul';
         }
   }    

    /**
    * Filtro
    */
    $gridFiltro = array(
        'substr(if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\') and T001_T008_Id is null[[virgula]]Forn.D024_Nome_Empresa[[virgula]]Cli.D024_Nome_Empresa)[[virgula]]1[[virgula]]20)' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
        'T001_Observacao' => array('titulo' => 'CNPJ', 'tipo' => 'texto'),
        'T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
        'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
        'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Descr.Produto', 'tipo' => 'texto'),
        'T001_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
        'T001_Flag_Operacao' => array('titulo' => 'Operação (E/S/B/C)?', 'tipo' => 'texto'),
        'T001_Descricao_Operacao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
        'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
        'T001_Aliquota_ICMS' => array('titulo' => '%ICMS', 'tipo' => 'numero'),
        'T001_Aliquota_IPI' => array('titulo' => '%IPI', 'tipo' => 'numero'),
        'T001_Percentual_ST' => array('titulo' => '%ST', 'tipo' => 'numero'),
        'T001_Percentual_Custo_Financeiro' => array('titulo' => '%C.F.', 'tipo' => 'numero'),
    );

	/**
	 * Botões
	 */
	$botoes = array(
	);
	echo Botoes::processar($botoes);

    // Gera o filtro e acrescenta ao $extra
    $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
    // Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
    // Imprime o filtro do GRID (normalmente executado acima do GRID)
    echo $g['ui']->gridFiltroPrint($gridFiltro);
    
    /**
     * Geração: Monta o SQL e retorna o dados
     */    

    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
    /**
    * Botões (Versão Acima do GRID)
    */
    /* $itensTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, false);
    $quantidadeTotal = number_format($g['sqlAuto']->pegarSqlCampo("sum(T001_Quantidade)", $from, $extra, false),2,',','.');
    $precoTotal = number_format($g['sqlAuto']->pegarSqlCampo("SUM(T001_Valor_Preco_Unitario*T001_Quantidade)", $from, $extra, false),2,',','.');
    echo <<<EOT
		<div class="buttonsBottomTop">
            <span class="item" style="text-align:right">Total itens<br><input type="text" value="{$itensTotal}" class="whiteBg" id="" size="11" readonly></span>
			<SPAN CLASS="item" STYLE="text-align:right">Total quantidade<BR><INPUT TYPE="TEXT" VALUE="{$quantidadeTotal}" CLASS="whiteBg" ID="" SIZE="11" READONLY></SPAN>
			<SPAN CLASS="item" STYLE="text-align:right">Total preço<BR><INPUT TYPE="TEXT" VALUE="{$precoTotal}" CLASS="whiteBg" ID="" SIZE="11" READONLY></SPAN>
            <div style="clear:both;"></div>
        </div>
EOT;
*/

	/**
	 * Totais
	 */
	$totais = array(
		// sql
		array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
		array('nome' => 'Valor_Total', 'valor' => 'T001_Quantidade', 'titulo' => 'Total quantidade'),
		array('nome' => 'Total_Preco', 'valor' => 'totalPreco', 'titulo' => 'Total preço'),
	);

    /**
     * Geração: Grid
     */
    $gridTabela = $g['ui']->grid($grid, $geradoDados);
    $g['smarty']->assign('thead', $gridTabela['thead']);
    $g['smarty']->assign('tbody', $gridTabela['tbody']);
    $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
    $g['smarty']->assign('gridId', md5($g['r_']));
    $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
    $g['smarty']->display('grid.tpl'); 
    break;
    
    case 'cad002GridHistoricoPrecos':
    	$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T098_Id', $g['divId'])),
			array('colunaValor' => 'T098_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T098_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'T098_D009_Preco_1', 'colunaTitulo' => 'Preço 1', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo manual', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_Aliquota_ICMS_Tabela', 'colunaTitulo' => 'ICMS Tabela', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_Percentual_Desconto_Tabela', 'colunaTitulo' => 'Desconto Tabela', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_ICF_1', 'colunaTitulo' => 'ICF 1', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_ICF_2', 'colunaTitulo' => 'ICF 2', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_ICF_3', 'colunaTitulo' => 'ICF 3', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_1', 'colunaTitulo' => 'IPV 1', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_2', 'colunaTitulo' => 'IPV 2', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_3', 'colunaTitulo' => 'IPV 3', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'IF(T098_Flag_Promocao="S", "Sim", "Não") as Flag_Promocao', 'colunaTitulo' => 'Promoção'),
			array('colunaValor' => 'T098_Flag_Promocao', 'colunaTitulo' => 'Promoção', 'colunaExibir'=>false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T098';	
		$extra = <<<EOT
		LEFT JOIN C007 ON C007_Id=T098_C007_Id
		where T098_D009_Id='{$D009_Id}'
		ORDER BY T098_Id DESC
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad043FormCadastroBensEspecificacoesBtnIncluir">Incluir</button> -->
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad043FormCadastroBensEspecificacoesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Especificações de Bens', '/cad/cad043/content/cad043ContentBensEspecificacoesIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T098_Flag_Promocao']=='S'){
				return 'linhaAzul';
			}
		}
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D128_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D129_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D129_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Especificações de Bens - ID: ' + acaoId, '/cad/cad043/content/cad043ContentBensEspecificacoesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

	break;
	
	case 'cad002GridHistoricoPrecosGeral':

		$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
    	$D009_Id = (isset($r_D009_Id)) ? $r_D009_Id : false;
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T098_Id', $g['divId'])),
			array('colunaValor' => 'T098_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=>false),
			array('colunaValor' => 'T098_Data', 'colunaTitulo' => 'Data', 'callback'=>'gCorrigeData'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descriçao'),
			array('colunaValor' => 'T098_D009_Preco_1', 'colunaTitulo' => 'Preço 1', 'callback'=>'gCorrigeNumero','colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_Preco_2', 'colunaTitulo' => 'Preço 2', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_Preco_3', 'colunaTitulo' => 'Preço 3', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo manual', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_Aliquota_ICMS_Tabela', 'colunaTitulo' => 'ICMS Tabela', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_Percentual_Desconto_Tabela', 'colunaTitulo' => 'Desconto Tabela', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T098_D009_ICF_1', 'colunaTitulo' => 'ICF 1', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_ICF_2', 'colunaTitulo' => 'ICF 2', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_ICF_3', 'colunaTitulo' => 'ICF 3', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_1', 'colunaTitulo' => 'IPV 1', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_2', 'colunaTitulo' => 'IPV 2', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'T098_D009_IPV_3', 'colunaTitulo' => 'IPV 3', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
			array('colunaValor' => 'IF(T098_Flag_Promocao="S", "Sim", "Não") as Flag_Promocao', 'colunaTitulo' => 'Promoção'),
			array('colunaValor' => 'T098_Flag_Promocao', 'colunaTitulo' => 'Promoção', 'colunaExibir'=>false),
			array('colunaValor' => 'T098_D009_Valor_Custo_Tabela', 'colunaTitulo' => 'Custo tabela', 'callback'=>'gCorrigeNumero', 'colunaAlinhar' => 'right'),			
		);

		/**
		* Definição da SQL
		*/
		$from = 'T098';	
		$extra = <<<EOT
		LEFT JOIN C007 ON T098_C007_Id=C007_Id
		LEFT JOIN D009 ON T098_D009_Id=D009_Id
		LEFT JOIN D049 ON D009_D049_Id=D049_Id
		LEFT JOIN D001 ON D049_D001_Id=D001_Id
		WHERE D009_C004_Id ='{$g['empresaAtual']}'
		ORDER BY T098_Id DESC
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		/* 
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad043FormCadastroBensEspecificacoesBtnIncluir">Incluir</button> -->
				
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#cad043FormCadastroBensEspecificacoesBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Especificações de Bens', '/cad/cad043/content/cad043ContentBensEspecificacoesIncluir/', '&tab=geral', [400,300]);
				});
			} </script></div>
EOT;
		 */
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T098_Flag_Promocao']=='S'){
				return 'linhaAzul';
			}
		}

		/**
		 * Botões
		 */
		$botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T098_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D128_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D129_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		/*
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D129_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Especificações de Bens - ID: ' + acaoId, '/cad/cad043/content/cad043ContentBensEspecificacoesIncluir/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		*/

		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

    break;
    
    case 'cad002GridValidade':
        
    //$C008_Id = $_REQUEST['C008_Id'];
    $D001_Id = $r_acaoId;
        
    /**
     * Definição do grid tabela
     */
    
    $cClausula  = " select ";
    $cClausula .= " D001_Codigo_Produto, ";
    $cClausula .= " D001_Descricao_Produto,";
    $cClausula .= " D009_Id";
    $cClausula .= " from D001";
    $cClausula .= " left join D049 on D049_D001_Id=D001_Id";
    $cClausula .= " left join D009 on D009_D049_Id=D049_Id";
    $cClausula .= " where D049_Id='$D001_Id'";
    $cClausula .= "   and D009_C004_Id={$g['empresaAtual']}";

    $D001 = mysql_query($cClausula);
    $mD001=mysql_fetch_array($D001);
    
    $D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');

    
    $grid = array(
        array('colunaValor'=>'CampoCadastro', 'colunaTitulo'=>'', 'colunaTipo'=>'livre', 'callback'=>'editarHistorico', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;'),
        array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' =>'Data', 'callback' => 'gCorrigeData'),
        array('colunaValor' => 'substr(if((T001_Flag_Operacao=\'E\' 
                                or T001_Flag_Operacao=\'B\') 
                                and T001_T008_Id is null,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa),1,30) as Cliente', 'colunaTitulo' =>'Cliente', 'style' => 'width:1%;' ),
        array('colunaValor' => 'D001_Foto', 'colunaTitulo' =>'Foto'),
        array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' =>'Código Produto'),
        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Descrição Produto' ),
        array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
        array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' =>'Descrição'),
        array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' =>'Custo', 'colunaAlinhar' => 'right', 'callback' => 'campoCusto', 'colunaExibir'=>false),
        array('colunaValor' => 'T001_Valor_Custo_Medio_Unitario', 'colunaTitulo' =>'Médio', 'colunaAlinhar' => 'right', 'callback' => 'campoMedio', 'colunaExibir'=>false),
        array('colunaValor' => 'T001_Valor_Preco_Unitario', 'colunaTitulo' =>'Preço', 'colunaAlinhar' => 'right', 'callback' => 'campoPreco', 'colunaExibir'=>false),
        array('colunaValor' => 'IPV', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvHistorico', 'colunaAlinhar' => 'right', 'colunaTipo' => 'livre', 'colunaExibir'=>false),
        array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) as T001_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right' ,'callback' => 'gCorrigeNumero'),
        array('colunaValor' => 'T001_Flag_Operacao', 'colunaTitulo' => '', 'colunaAlinhar' => 'center'),
        array('colunaValor' => 'T001_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right','callback' => 'gCorrigeNumero'),
        //array('colunaValor' => 'T001_Flag_MMF', 'colunaTitulo' => 'F', 'colunaAlinhar' => 'center'),
        //array('colunaValor' => 'T001_Flag_Atualiza_Custo', 'colunaTitulo' => 'C', 'colunaAlinhar' => 'center'),
        array('colunaValor' => 'T001_Id', 'colunaTitulo' => '', 'colunaExibir'=>false, 'chavePrimaria'=>true),
        array('colunaValor' => 'T001_T007_Id', 'colunaTitulo' => '', 'colunaExibir'=>false, 'chavePrimaria' => true),
        array('colunaValor' => 'T001_T013_Id', 'colunaTitulo' => '', 'colunaExibir'=>false, 'chavePrimaria' => true),
        array('colunaValor' => '(T001_Quantidade * if(T001_Flag_Operacao=\'S\',-1,1)) as T001_Quantidade2', 'colunaTitulo' => '', 'colunaExibir'=>false),
        array('colunaValor' => 'T001_Flag_Devolucao', 'colunaTitulo' => '', 'colunaExibir'=>false),
        array('colunaValor' => 'T001_Data_Fabricacao', 'colunaTitulo' => 'Fabricação', 'colunaEditar'=>true, 'callback'=>'gCorrigeData'),
        array('colunaValor' => 'T001_Data_Validade', 'colunaTitulo' => 'Validade', 'colunaEditar'=>true, 'callback'=>'gCorrigeData'),
        array('colunaValor' => 'T001_Lote_Validade', 'colunaTitulo' => 'Lote', 'colunaEditar'=>true, 'style'=>'width:90px;'),
        array('colunaValor' => 'T001_Saldo_Validade', 'colunaTitulo' => 'Saldo Validade'),
    );


    $from = 'T001';

    $extra = <<<EOT
                    left join D024 as Forn on Forn.D024_Id=T001_D024_Id
                    left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
                    left join D009 on D009_Id=T001_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join D001 on D001_Id=D049_D001_Id
                    WHERE (T001_Flag_Operacao='E' OR T001_Flag_Operacao='B')
                    order by T001_Data_Lancamento desc,T001_Id desc
    
EOT;

    /**
    * Função que define classes CSS para cada linha do grid
    */
    function gridLinha($array) {
        if($array['todosCampos']['T001_Saldo_Validade']>0){
             if (empty($array['todosCampos']['T001_Data_Validade']) || $array['todosCampos']['T001_Data_Validade']='0000-00-00') {
                 return 'linhaAzul';
             } else if ($array['todosCampos']['T001_Data_Validade'] < date('Y-m-d')) {
                 return 'linhaVermelho';
             }
        }else{
            return 'linhaCinza';
        }
   }    

    /**
    * Filtro
    */
    $gridFiltro = array(        
        'D024_Nome_Empresa,D024_Nome_Fantasia,D024_Id' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
        'T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
        'D001_Foto' => array('titulo' => 'Foto', 'tipo' => 'numero'),
        "D001_Codigo_Produto" => array('titulo' => 'Código', 'tipo' => 'numero'),
        'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
        'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        'T001_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
    );
    // Gera o filtro e acrescenta ao $extra
    $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
    // Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
    // Imprime o filtro do GRID (normalmente executado acima do GRID)
    echo $g['ui']->gridFiltroPrint($gridFiltro);
    
    
    /**
    * Geração: Monta o SQL e retorna o dados
    */
    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
    /**
    * Botões (Versão Acima do GRID)
    */
    $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, false);
    echo <<<EOT
        <div class="buttonsBottomTop">
            <!-- <span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span> -->
            <button type="button" id="cad002ReprocessarValidade">Reprocessar validade</button>
            <div style="clear:both;"></div>
        </div>
EOT;
	    echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002ReprocessarValidade').unbind('click').bind('click', function() {
				var buttons = {
					"Sim": function() {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/cad/cad002/grid_func-ajax/reprocessarValidade/?ajax=true&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								dialogConfirm('Ok!', 'Atualizado com sucesso...!');
								divRefresh('{$g['divId']}');
							} else {
								dialogConfirm('Erro!', request.data);
							}
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				}
				dialogConfirm('Confirmação', 'Atualizar saldo das validades?', buttons);
			});
		} </script></div>
EOT;
    /**
     * Geração: Grid
     */
    $gridTabela = $g['ui']->grid($grid, $geradoDados);
    $g['smarty']->assign('thead', $gridTabela['thead']);
    $g['smarty']->assign('tbody', $gridTabela['tbody']);
    $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
    $g['smarty']->assign('gridId', md5($g['r_']));
    $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
    $g['smarty']->display('grid.tpl');        
    break;
    
    case 'cad002GridRetornarPrecos':
    	$extraSql = isset($r_extraSql) ? base64_decode($r_extraSql) : false;
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D041_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
		array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN', 'colunaEditar' => true, 'colunaExibir'=>false),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português'),
        array('colunaValor'=>'D049_D082_Id', 'colunaTitulo'=>'Marca', 'callback' => 'mostrarMarca'),
		array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D037_Id', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'left', 'callback' => 'mostrarUnidade', 'colunaExibir' => false),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque', 'colunaExibir'=>false),
		array('colunaValor'=>"D009_Preco_1", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_1", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'callback'=>'validaPrecoRetorno1', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno1', 'callbackParameter'=>'data'),
		array('colunaValor'=>"D009_Preco_2", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_2", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'callback'=>'validaPrecoRetorno2', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno2', 'callbackParameter'=>'data'),
		array('colunaValor'=>"D009_Preco_3", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_3", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'callback'=>'validaPrecoRetorno3', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno3', 'callbackParameter'=>'data'),
        array('colunaValor'=>'D009_Flag_Preco_Tabelado', 'colunaTitulo'=>"Preço tabelado?", 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'callback'=>'CAD002checkboxCampoPrecoTabelado', 'colunaExibir'=>false),
        array('colunaValor'=>'D009_Flag_Promocao', 'colunaTitulo'=>"Promoção?", 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'callback'=>'CAD002checkboxCampoPromocao', 'colunaExibir'=>false),
		array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
        array('colunaValor'=>'D005_Aliquota_IPI', 'colunaTitulo'=>'Aliq IPI', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'colunaExibir'=>false),
		array('colunaValor'=>"D082_Marca", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"C008_Tipo", 'colunaTitulo'=>'','colunaExibir' => false),
	);

	$from = 'D049';
	$extra = <<<EOT
				{$extraSql}
EOT;

	/*
	* Função que define classes CSS para cada linha do grid
	
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	
	*/


	/*
	* Botões (Versão Acima do GRID)
	
	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002Incluir">Incluir</button>
			
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Novo Cadastro', '/cad/cad002/content/form1/', '', [550,550]);
			});
		} </script></div>
EOT;
*/ 


	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
		'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
		'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
		'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'moeda'),
        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
        '(SELECT T098_Data from T098 WHERE T098_D049_Id=D049_Id ORDER BY T098_Id DESC LIMIT 1)' => array('titulo' => 'Dt Alteração', 'tipo' => 'data'),
	);

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	//$extra_total = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extra_total);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($gridFiltro);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);

	echo <<<EOT
		<div class="buttonsBottomTop">
			<!-- <button type="button" id="#">Confirmar</button> -->
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Retornar Preços', '/cad/cad002/content/cad002ContentRetornarPrecos/', '&extraSql=' + encodeURIComponent(), [900,500]);
			});
		} </script></div>
EOT;

	
	/**
	* Botões (Versão Acima do GRID)
	*/

	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	$novoIdTelaForm = uniqid();
	echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
		   		// somente executa a ação ao clicar na linha ou na coluna
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha
				var id = $(this).closest('tr').attr('id').split('|');
				var D001_Id = id[0].replace(/D001_Id-/, '');
				var D009_Id = id[1].replace(/D009_Id-/, '');
		       abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Histórico Preços', '/cad/cad002/content/cad002contentHistóricoPrecos/', '&D009_Id=' + encodeURIComponent(D009_Id), [1000,553]);
		   });
		   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	//print_r($gridTabela);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	
	$primeiraLinhaGridHead = <<<EOT

<tr>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 1</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 2</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 3</th>
</tr>
EOT;
	$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
	
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
    break;

    case 'cad002GridRetornarPrecosPromocao':
    	$extraSql = isset($r_extraSql) ? base64_decode($r_extraSql) : false;

    	$extraParam = str_replace("\\", "", $extraSql);

		$where = 'WHERE ' . gPegarParteWhere($extraParam);
		
		$where = gInsertExtraWhere($where, "D009_Flag_Promocao='S'");

		$extraParam = preg_split('/where/i', $extraParam);

		$Joins = $extraParam[0];
		//die("Joins: " . $Joins . " <br /> <br />Extra: " . $where);
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D041_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
		array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN', 'colunaEditar' => true, 'colunaExibir'=>false),
		array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português'),
        array('colunaValor'=>'D049_D082_Id', 'colunaTitulo'=>'Marca', 'callback' => 'mostrarMarca'),
		array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaExibir' => false),
		array('colunaValor'=>'D001_D037_Id', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'left', 'callback' => 'mostrarUnidade', 'colunaExibir' => false),
		array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque', 'colunaExibir'=>false),
		array('colunaValor'=>"D009_Preco_1", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_1", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'color:green;', 'callback'=>'validaPrecoRetorno1Promocao', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno1Promocao', 'callbackParameter'=>'data'),
		array('colunaValor'=>"D009_Preco_2", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_2", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'color:green;', 'callback'=>'validaPrecoRetorno2Promocao', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno2Promocao', 'callbackParameter'=>'data'),
		array('colunaValor'=>"D009_Preco_3", 'colunaTitulo'=>'Atual','colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
		array('colunaValor'=>"D009_Preco_3", 'colunaTitulo'=>'Anterior','colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'style' => 'color:green;', 'callback'=>'validaPrecoRetorno3Promocao', 'callbackParameter'=>'preco'),
		array('colunaValor'=>"DataAlteracao", 'colunaTitulo'=>'Dt Alteração','colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaTipo'=>'livre', 'callback'=>'validaPrecoRetorno3Promocao', 'callbackParameter'=>'data'),
        array('colunaValor'=>'D009_Flag_Preco_Tabelado', 'colunaTitulo'=>"Preço tabelado?", 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'callback'=>'CAD002checkboxCampoPrecoTabelado', 'colunaExibir'=>false),
        array('colunaValor'=>'D009_Flag_Promocao', 'colunaTitulo'=>"Promoção?", 'colunaAlinhar' => 'center', 'style'=>'width:1%;', 'callback'=>'CAD002checkboxCampoPromocao', 'colunaExibir'=>false),
		array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
        array('colunaValor'=>'D005_Aliquota_IPI', 'colunaTitulo'=>'Aliq IPI', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2', 'colunaExibir'=>false),
		array('colunaValor'=>"D082_Marca", 'colunaTitulo'=>'','colunaExibir' => false),
		array('colunaValor'=>"C008_Tipo", 'colunaTitulo'=>'','colunaExibir' => false),
	);

	$from = 'D049';
	$extra = <<<EOT
				{$Joins} {$where} 
EOT;

	/**
	* Função que define classes CSS para cada linha do grid
	
	function gridLinha($array) {
 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
 	    {
  	       return 'linhaCinza';
 	    }
 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
 	    {
  	       return 'linhaVermelho';
 	    }
	}	
	*/


	/**
	* Botões (Versão Acima do GRID)
	
	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002Incluir">Incluir</button>
			
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002Incluir').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Novo Cadastro', '/cad/cad002/content/form1/', '', [550,550]);
			});
		} </script></div>
EOT;
*/ 


	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
		'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
		'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
		'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'moeda'),
        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
        '(SELECT T098_Data from T098 WHERE T098_D049_Id=D049_Id ORDER BY T098_Id DESC LIMIT 1)' => array('titulo' => 'Dt Alteração', 'tipo' => 'data'),
	);

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	//$extra_total = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extra_total);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	//echo $g['ui']->gridFiltroPrint($gridFiltro);
	
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);

	$extraRetornoPreco = base64_encode($extra);

	echo <<<EOT
		<div class="buttonsBottomTop">
			<button type="button" id="cad002RetornarPrecosPromocaoBtn">Confirmar</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#cad002RetornarPrecosPromocaoBtn').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var T003_Id = $('#{$g['divId']} #T003_Id').val();
						var Desconto = $('#{$g['divId']} #Desconto').val();
						$('#{$g['divId']}').showLoading();
						$.getJSON('/cad/cad002/form_func-ajax/cad002RetornarPrecosPromocao/?ajax=true&extraRetornoPreco=' + encodeURIComponent('{$extraRetornoPreco}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    			dialogConfirm('Ok', request.data);
				    			divRefresh('{$g['divIdParent']}', true);
				    			fecharJanela('{$g['divId']}');
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    			dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); } 
				};
				dialogConfirm('Retornar Preços', '<font color="red"><b>Atenção!</b></font><br /><br />Ao confirmar esta operação todos os preços dos produtos filtrados sofrerão alteração no preço. Você tem certeza? ', \$buttons);
			});
		} </script></div>
EOT;

	
	/**
	* Botões (Versão Acima do GRID)
	*/

	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	$novoIdTelaForm = uniqid();
	echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
		   		// somente executa a ação ao clicar na linha ou na coluna
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha
				var id = $(this).closest('tr').attr('id').split('|');
				var D001_Id = id[0].replace(/D001_Id-/, '');
				var D009_Id = id[1].replace(/D009_Id-/, '');
		       abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Histórico Preços', '/cad/cad002/content/cad002contentHistóricoPrecos/', '&D009_Id=' + encodeURIComponent(D009_Id), [1000,553]);
		   });
		   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	//print_r($gridTabela);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	
	$primeiraLinhaGridHead = <<<EOT

<tr>
	<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 1</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 2</th>
	<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Preço 3</th>
</tr>
EOT;
	$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
	
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
    break;

    case 'cad002GridExportarXmlProdutos':
    	$C008_Id = $r_acaoId;
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor'=>'C008_Tipo', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left'),
			array('colunaValor'=>'D003_Nome_Linha', 'colunaTitulo'=>'Linha', 'colunaAlinhar' => 'left', 'colunaExibir' => false),
			array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré', 'colunaAlinhar' => 'center'),		
			array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo', 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código'),
	        array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN'),
			array('colunaValor'=>'D001_Descricao_Produto', 'colunaTitulo'=>'Descrição português'),
			array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês'),
			array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
			array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
			array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'center'),
			//array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo', 'colunaAlinhar' => 'right'),
			array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'callback' => 'corEstoque'),
			//array('colunaValor'=>'D009_Quantidade_Estoque_Real', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'right'),
			array('colunaValor'=>'D009_Quantidade_Estoque_Liquido', 'colunaTitulo'=>'Líquido', 'colunaAlinhar' => 'right'),
	        array('colunaValor'=>"if(D149_Flag_ST='S','ST','') as ST", 'colunaTitulo'=>'ST', 'colunaAlinhar' => 'center'),
	        array('colunaValor'=>'D049_Flag_Tipo', 'colunaTitulo'=>'TP', 'colunaAlinhar' => 'center'),
			array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
			array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Último Fornecedor'),
	        array('colunaValor'=>'D009_Id','colunaTitulo' => 'D009_Id', 'colunaExibir'=>false),
			//array('colunaValor'=>'D015_Nome_Grupo', 'colunaTitulo' => 'Aplicação'),
		);
	//left join D083 on D083_D024_Id=D024_Id and D083_D001_Id=D001_Id and D083_D049_Id=D049_Id
		$from = 'D001';
		$extra = <<<EOT
					left join D003 on D003_Id=D001_D003_Id
					left join D049 on D049_D001_Id=D001_Id
					left join D009 on D009_D049_Id=D049_Id
					left join C008 on D001_C008_Id=C008_Id
					left join D082 on D049_D082_Id=D082_Id
					left join D024 on D024_Id=D049_D024_Id
					left join D060 on D001_D060_Id=D060_Id
					left join D002 on D001_D002_Id=D002_Id
					left join D015 on D001_D015_Id=D015_Id
					left join D037 on D001_D037_Id=D037_Id
					left join D005 on D001_D005_Id=D005_Id
					left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
					left join D072 on D072_D001_Id=D001_Id
					left join T066 on T066_D009_Id=D009_Id
					left join D004 on D004_Id=T066_D004_Id
					left join D041 on D041_D049_Id=D049_Id
					where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
					AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
					GROUP BY D009_Id
EOT;
		
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
	 	    if ($array['todosCampos']['D001_Flag_Ativo']=='N') 
	 	    {
	  	       return 'linhaCinza';
	 	    }
	 	    if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') 
	 	    {
	  	       return 'linhaVermelho';
	 	    }
		}	

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
			"D001_Codigo_Produto" => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
			'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
				array('title' => 'Todos', 'value' => ''),
				array('title' => 'Sim', 'value' => 'S'),
				array('title' => 'Não', 'value' => 'N'),
			), 'naoGerarExtra' => false),
			//'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
			'D049_Flag_Ativo' => array('titulo' => 'Locação', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
	        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
	    
	    /**
	    * Geração: Monta o SQL e retorna os dados
	    */
	    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	    
	    /**
	    * Botões (Versão Acima do GRID)
	    */
	    $extra_ = urlencode(base64_encode($extra));

	    echo <<<EOT
	        <div class="buttonsBottomTop">
	            <button type="button" id="cad002GerarXMLProdutos">Gerar XML</button>
	        </div>
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            $('#cad002GerarXMLProdutos').unbind('click').bind('click', function() {
	                var buttons = {
	                    "Sim": function() {
	                        $('#{$g['divId']}').showLoading();
	                        $.getJSON('/cad/cad002/grid_func-ajax/cad002AjaxGerarXmlProdutos/?extraSql={$extra_}&ajax=true&callback=?', function(request) {
	                            $('#{$g['divId']}').hideLoading();
	                            if (request.code) {
	                                dialogConfirm('Ok!', 'XML gerado com sucesso.');
	                                divRefresh('{$g['divId']}');
	                            } else {
	                                dialogConfirm('Erro!', request.data);
	                            }
	                        });
							$('#{$g['divId']}').hideLoading();
	                        $(this).dialog("destroy").remove();
	                    },
	                    "Não": function() { $(this).dialog("destroy").remove(); }
	                }
	                dialogConfirm('Confirmação', 'Tem certeza que deseja gerar o xml dos produtos filtrados?', buttons);
	            });
	            
	            
	        } </script></div>
EOT;
	    
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		// echo $geradoSql;
		// die();
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		$valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				   var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
					    // somente executa a ação ao clicar na linha ou na coluna
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						// pega o ID da linha
						var id = $(this).closest('tr').attr('id').split('|');
						var D001_Id = id[0].replace(/D001_Id-/, '');
						var D024_Id = id[1].replace(/D024_Id-/, '');
					   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,600]);
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
		

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'cad002GridProdutosSerie':

    	$D049=mysql_query("SELECT D009_D049_Id FROM D009 WHERE D009_Id='{$r_D009_Id}'");
    	$mD049=mysql_fetch_array($D049);

		/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D144_Id', $g['divId'])),
			array('colunaValor' => 'D144_Id', 'colunaTitulo' => 'Num Série', 'chavePrimaria'=>true),
			array('colunaValor' => 'D144_D049_Id', 'colunaTitulo' => 'D144_D049_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'D144_T055_Id', 'colunaTitulo' => 'D144_T055_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'D144_T014_Id', 'colunaTitulo' => 'D144_T014_Id', 'colunaExibir'=>false),
			array('colunaValor' => 'D144_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaEditar'=>true),
			array('colunaValor' => 'D144_Quantidade_Estoque', 'colunaTitulo' => 'Estoque'),
			array('colunaValor' => 'D144_Valor_Preco_Venda', 'colunaTitulo' => 'Preço Venda'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D144';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
				LEFT JOIN D049 ON D049_Id=D144_D049_Id
				LEFT JOIN D001 ON D001_Id=D049_D001_Id
				LEFT JOIN T055 ON T055_Id=D144_T055_Id
				LEFT JOIN T075 ON T075_Id=T055_T075_Id
				LEFT JOIN T014 ON T014_Id=D144_T014_Id
				LEFT JOIN T013 ON T013_Id=T014_T013_Id
				WHERE D144_D049_Id='{$mD049['D009_D049_Id']}'
EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="est014incluir">Incluir</button>-->
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est014incluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Requisição de Compra', '/est/est014/content/est014RequisicaoCompra/','', [500,300]);
				});

			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// var id = $(this).closest('tr').attr('id').replace(/D144_Id-/, '');
				// divSetarAcaoId('{$r_divReload}', id);
				// divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
			} </script></div>	
EOT;
			//est008contentCadastroCotacaoCompra
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T128_Id' => array('titulo' => 'Número', 'tipo' => 'texto', 'exibir'),
			'T128_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Remessa', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'cad002GridListaFornecedor':

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D070_Id', $g['divId'])),
            array('colunaValor' => 'D070_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D070_D024_Id', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D070_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'D070_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'Validade', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D070_Flag_Ativo', 'colunaTitulo' => 'Ativo?', 'colunaEditar' => 'S', 'colunaEditarNivel' => '2'),
            array('colunaValor' => 'D070_Flag_Tabela_Propria', 'colunaTitulo' => 'Própria?', 'colunaEditar' => 'S', 'colunaEditarNivel' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Flag_Codigos_Comuns', 'colunaTitulo' => 'Códigos Comuns', 'colunaEditar' => 'S', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D070';
		
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id=D070_D024_Id
			LEFT JOIN D082 ON D082_Id=D070_D082_Id
			WHERE D070_C004_Id='{$g['empresaAtual']}'
EOT;
		/**
		 * Botões
		 */
		$botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est002IncluirListaFornecedorBtn'),
		);
		echo Botoes::processar($botoes);

		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/

		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<!-- <div class="buttonsBottomTop">
				<button type="button" id="est002IncluirListaFornecedorBtn">Incluir</button>
			</div> -->
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#est002IncluirListaFornecedorBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Tabela Fornecedor', '/cad/cad002/content/cad002ContentListaFornecedorIncluir/','', [650,330]);
				});

				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D070_Id = id[0].replace(/D070_Id-/, '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Alterar Tabela Fornecedor', '/cad/cad002/content/cad002ContentListaFornecedorAlterar/', '&acaoId=' + encodeURIComponent(D070_Id) + '&D070_Id=' + encodeURIComponent(D070_Id) + '&tabela=$from',[1000,600]);
				});
			} </script></div>
EOT;
			//est008contentCadastroCotacaoCompra
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D070_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'D024_Id,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D070_Observacao' => array('titulo' => 'Observação', 'tipo' => 'texto'),
			'D070_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
			'D070_Data_Vencimento' => array('titulo' => 'Validade', 'tipo' => 'data'),
			'D070_Flag_Ativo' => array('titulo' => 'Ativo?(S/N)', 'tipo' => 'texto'),
			'D070_Flag_Codigos_Comuns' => array('titulo' => 'Cod.Comuns?(S/N)', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */

		/* $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'cad002GridListaFornecedorProdutos':
    	$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
    	$importacaoDados=array('D069_D070_Id'=>$r_D070_Id);
    	/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D069_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Excluir', 'colunaTitulo' => 'Exc', 'callback' => 'checkboxExcluirD069', 'colunaEditarTodos' => true, 'colunaAlinhar' => center ,'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => '1|2'),
            array('colunaValor' => 'D069_Campos_Atualizados', 'colunaTitulo' => '?', 'colunaAlinhar' => 'center', 'callback' => 'camposAtualizadosTabFornecedor', 'style' => 'width: 1%'),
            array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Tabela_Principal', 'colunaTitulo' => 'Usar?','colunaAlinhar' => 'center', 'callback' => 'checkboxD069FlagTabelaPrincipal'),
            array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'if(D001_Descricao_Produto is not null,D001_Descricao_Produto,D069_Descricao_Produto)', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D069_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%'),
            array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'callback' => 'campoInfo', 'callbackParameter' => array('D069_D006_Id_Info', 'Demonstrativo CFOP', 'D069_Id', 'left', '', '500')),
            array('colunaValor' => 'D069_D006_Id_Info', 'colunaExibir' => false),
            //array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM Cad', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px'),
            array('colunaValor' => 'D069_NCM', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => "IF(D005_Classificacao_Fiscal != '',concat(D005_Classificacao_Fiscal,'-',D005_Flag_Aplicacao),CONCAT('<font color=\"red\">',D069_NCM,'</font>'))", 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('D069_D005_Id_Info', 'Demonstrativo NCM', 'D069_Id', 'left', '', '500')),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            
            array('colunaValor' => 'D069_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'D069_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            //array('colunaValor' => 'D069_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Valor_Preco_Unitario', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
            array('colunaValor' => 'D069_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
           	array('colunaValor' => 'D069_Aliquota_PIS', 'colunaTitulo' => '%PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_Aliquota_COFINS', 'colunaTitulo' => '%COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'ifnull(D069_Valor_Frete,0)+ifnull(D069_Valor_Total_Frete,0)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_Data_Atualizacao_Custo', 'colunaTitulo' => 'Atualização Custo', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#A1FCA9', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado-D069_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao_Entrega', 'colunaTitulo' => 'Prazo entrega', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao_Compra', 'colunaTitulo' => 'Observação', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Percentual_Reducao_ICMS_ST', 'colunaTitulo' => '% Red ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069A_Percentual_Reducao_ICMS_INT_ST', 'colunaTitulo' => '% Red ICM Int ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069A_Percentual_Reducao_ICMS_IE_ST', 'colunaTitulo' => '% Red ICM IE ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_ICMS', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Peso_Unitario', 'colunaTitulo' => 'Peso unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Dir001_Id', 'colunaExibir' => false, 'Alinhar' => 'center'),
            array('colunaValor' => 'D069_Quantidade_Embalagem', 'colunaTitulo' => 'D069_Quantidade_Embalagem', 'colunaExibir' => false),
            array('colunaValor' => 'D069_D005_Id', 'colunaTitulo' => 'D069_D005_Id', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'D069 Class.Fiscal', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Data_Hora_Ultima_Alteracao', 'colunaTitulo' => 'Data Calculou', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário Calculou', 'style' => 'width:1%'),
            array('colunaValor' => 'D069_Data_Hora_Ultima_Alteracao_Sem_Calcular', 'colunaTitulo' => 'Data Alterado', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Data_Atualizacao_Estoque', 'colunaTitulo' => 'Data estoque', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Quantidade_Estoque_Anterior', 'colunaTitulo' => 'Estoque anterior', 'style' => 'width:1%', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Data_Atualizacao_Preco', 'colunaTitulo' => 'Data Preco', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D069_Valor_Preco_Original_Anterior', 'colunaTitulo' => 'Preco anterior', 'style' => 'width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca Produto', 'style' => 'width:1%'),
        ); 

		/**
		* Definição da SQL
		*/
		$from = 'D069';

		$extra = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			left join C007 on C007_Id=D069_C007_Id_Ultima_Alteracao
			left join D069A on D069_Id = D069A_D069_Id
			where D069_D070_Id = '{$r_D070_Id}'
			group by D069_Id
EOT;
/* 		$extraTotal = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			left join C007 on C007_Id=D069_C007_Id_Ultima_Alteracao
			WHERE D069_D070_Id='{$r_D070_Id}'
EOT; */

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			// (substituir pelo mesmo ID da chave primária do grid)
				var chavePrimaria = 'T004_Id';
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var D069_Id = id[0].replace(/D069_Id-/, '');
				// Ação: abrir nova janela
				//divSetarAcaoId('{$r_divReload}',D069_Id);
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Selecione o Produto', '/cad/cad002/content/cad002ContentAssociarProduto/', '&acaoId=' + encodeURIComponent(D069_Id) + '&tabela={$from}', [480,150]);
				divRefresh('{$r_divReload}');
			});
		} </script></div>
EOT;
		$gridFiltro = array(
            'D069_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'ifnull(D001_Codigo_Produto[[virgula]]\'\')' => array('titulo' => 'Nosso', 'tipo' => 'texto'),
            'D069_Descricao_Produto,D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D069_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')' => array('titulo' => 'CFOP', 'tipo' => 'texto'),
            'D069_NCM,D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'D069_Flag_Origem_Produto' => array('titulo' => 'Origem', 'tipo' => 'texto'),
            'D069_Aliquota_IPI' => array('titulo' => '%IPI', 'tipo' => 'texto'),
            'D069_Aliquota_ICMS' => array('titulo' => '%ICM', 'tipo' => 'texto'),
            'D069_Percentual_Reducao_ICMS' => array('titulo' => '%Red', 'tipo' => 'texto'),
            'D069_Percentual_ST' => array('titulo' => '%ST', 'tipo' => 'texto'),
            'D069_Aliquota_PIS' => array('titulo' => '%PIS', 'tipo' => 'texto'),
            'D069_Aliquota_COFINS' => array('titulo' => '%COFINS', 'tipo' => 'texto'),
            'D069_Codigo_Barras' => array('titulo' => 'EAN', 'tipo' => 'texto'),
            'D082_Marca,D069_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto', 'exibir' => false),
            'D069_Data_Hora_Ultima_Alteracao' => array('titulo' => 'Data Calculou', 'tipo' => 'data'),
			'D069_C007_Id_Ultima_Alteracao' => array('titulo' => 'Usuário Calculou', 'tipo' => 'texto'),
			'D069_Data_Hora_Ultima_Alteracao_Sem_Calcular' => array('titulo' => 'Data Alterado', 'tipo' => 'data'),
			'D069_Data_Atualizacao_Estoque' => array('titulo' => 'Data estoque', 'tipo' => 'data'),
			'D069_Data_Atualizacao_Preco' => array('titulo' => 'Data preço', 'tipo' => 'data'),
			'D082_Marca' => array('titulo' => 'Marca Produto', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);
		//$valorTotal = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);

		// Botões
				$botoes = array(
			array('titulo' => 'Atualizar alíquotas e calcular', 'id' => 'cad002AtualizarAliquotas'),
			array('titulo' => 'Incluir Produto', 'id' => 'cad002IncluirProdutoFornecedor'),
			array('titulo' => 'Excluir produtos marcados', 'id' => 'cad002ExcluirProdutoFornecedor'),
			array('titulo' => 'Marcar produtos', 'id' => 'cad002MarcarProdutos'),
		);
		echo Botoes::processar($botoes);

		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		*	Botões acima do filtro
		*/
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra_ = base64_encode($extra);
		if (!personalizacaoBotoes()) {
            // Não executar quando houver o refresh de linha
            if (empty($r_linhaGridId)) {
                echo <<<EOT
				<div class="buttonsBottomTop">
					<!--button type="button" id="cad002AtualizarAliquotas">Atualizar alíquotas e calcular</button-->
					<!--button type="button" id="cad002IncluirProdutoFornecedor">Incluir produto</button-->
					<!--button type="button" id="cad002ExcluirProdutoFornecedor">Excluir produtos marcados</button-->
					<!--button type="button" id="cad002MarcarProdutos">Marcar produtos</button-->
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#cad002IncluirProdutoFornecedor').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produto tabela fornecedor', '/cad/cad002/content/cad002ContentIncluirProdutoFornecedor/', '&D070_Id={$r_D070_Id}', [600,90]);
					});

					$('#cad002AtualizarAliquotas').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
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
					            $(this).dialog("destroy").remove();
								
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,1);
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						var dialogOptions = dialogConfirm('Atualizar Alíquotas', 'Atualizar as aliquotas igual ao cadastro de produtos: <br />', \$buttons);
						$('<input type="checkbox" checked="checked">').attr('id', 'campoCFOP').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelCFOP').html('CFOP').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox" checked="checked">').attr('id', 'campoNossoCodigo').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelNossoCodigo').html('Nosso Código').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
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

			        function cad002AtualizarAliquotasLoop(idProgressBar, arrayOpcoes, inicio){
		                $.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&opcoes=' + encodeURIComponent(arrayOpcoes) + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
		                    if (request.concluido == 0) {
		                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
		                    	cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,0);
		                    }else{
		                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
		                    	dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
		                    	if (request.code) {
		                            divRefresh('{$g['divId']}');
		                        } else {
		                            dialogConfirm('Erro!', request.data);
		                        }
		                    } 
		                });	                    
			        }

					$('#cad002ExcluirProdutoFornecedorx').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/cad/cad002/grid_func-ajax/excluirD069Marcados/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&D070_Id=' + encodeURIComponent("{$r_D070_Id}") + '&callback=?', function(request) {
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
						dialogConfirm('Atenção', 'Deseja excluir os itens marcados?', \$buttons);
					});

					$('#cad002ExcluirProdutoFornecedor').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								idProgressBar = dialogAlert('Excluindo produtos',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
								cad002ExcluirProdutoTabelaFornecedor(idProgressBar,1); 
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Excluir', 'Deseja excluir os itens marcados? ', \$buttons);
					});
	
					function cad002ExcluirProdutoTabelaFornecedor(idProgressBar, inicio){
						$.getJSON('/cad/cad002/grid_func-ajax/excluirD069Marcados/?ajax=true&D070_Id=' + encodeURIComponent("{$r_D070_Id}") + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
							if (request.concluido == 0) {
								$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Produtos excluídos: <b>' + request.ok + '</b><br/>Erros ao excluir: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
								cad002ExcluirProdutoTabelaFornecedor(idProgressBar,0);
							}else{
								$('#dc-' + idProgressBar).dialog("destroy").remove();
								dialogConfirm('Produtos excluídos','<b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Produtos excluídos: <b>' + request.ok + '</b><br/>Erros ao excluir: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
								if (request.code) {
									divRefresh('{$g['divId']}');
									$(this).dialog("destroy").remove();     
								} 
							} 
						});	                  
					}

					$('#cad002MarcarProdutos').bind('click', function(data) {
						var \$buttons = {
							"Marcar todos": function() {
								idProgressBar = dialogAlert('Usar produtos marcados',"<span id='progressBar'><b>0% Marcando produtos</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            cad002MarcarTodosProdutos(idProgressBar,1,'S');

									function cad002MarcarTodosProdutos(idProgressBar,inicio, flagMarcar){
						                $.getJSON('/cad/cad002/grid_func-ajax/MarcarTodosProdutos/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio  + '&flagMarcar='+ flagMarcar +'&callback=?', function(request) {
						                    if (request.concluido == 0) {
						                    	$('#progressBar').html('<b>' + request.percentual + '% Marcando produtos</b>');
						                    	cad002MarcarTodosProdutos(idProgressBar,0,flagMarcar);
						                    }else{
						                    	if (request.code) {
						                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
					                    			dialogConfirm('Usar produtos marcados','<b><font color="green">' + request.percentual + '<b>%</b><b> Produtos marcados &#10004;');
					                    			divRefresh('{$g['divId']}', true);
						                        } else {
						                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
						                            dialogConfirm('Erro!', request.data);
						                            divRefresh('{$g['divId']}', true);
						                        }
						                    } 
						                });	                    
							        }
								$(this).dialog("destroy").remove();
							},
							"Desmarcar todos": function() { 
								idProgressBar = dialogAlert('Usar produtos marcados',"<span id='progressBar'><b>0% Desmarcando produtos</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            cad002DesmarcarTodosProdutos(idProgressBar,1, 'N');

									function cad002DesmarcarTodosProdutos(idProgressBar,inicio,flagMarcar){
						                $.getJSON('/cad/cad002/grid_func-ajax/MarcarTodosProdutos/?ajax=true&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio  + '&flagMarcar='+ flagMarcar + '&callback=?', function(request) {
						                    if (request.concluido == 0) {
						                    	$('#progressBar').html('<b>' + request.percentual + '% Desmarcando produtos</b>');
						                    	cad002DesmarcarTodosProdutos(idProgressBar,0, flagMarcar);
						                    }else{
						                    	if (request.code) {
						                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
					                    			dialogConfirm('Usar produtos marcados','<b><font color="green">' + request.percentual + '<b>%</b><b> Produtos desmarcados &#10004;');
					                    			divRefresh('{$g['divId']}', true);
						                        } else {
						                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
						                            dialogConfirm('Erro!', request.data);
						                            divRefresh('{$g['divId']}', true);
						                        }
						                    } 
						                });	                    
							        }
								$(this).dialog("destroy").remove();
							},
							"Cancelar": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Usar produtos', 'Escolha uma opção para aplicar nos produtos da tabela: ', \$buttons);
					});

				} </script></div>
EOT;
            }
		}
		
/* 		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Produtos<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
				<div style="clear:both;"></div>             
			</div>
EOT; */
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	
		/**
		 * Botões
		 */
		// Definição dos botões
		/*$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad002FormListaFornecedorBtn">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
		$botoesDir .= '<button type="submit" id="cad002AtualizarAliquotas">Atualizar Alíquotas</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	
		$sql_ = urlencode(base64_encode($geradoSql));
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {			
				$('#cad002AtualizarAliquotas').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&sql=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
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
					dialogConfirm('Atualizar Alíquotas', 'Você tem certeza? ', \$buttons);
				});
			} </script></div>
EOT;
*/
    break;
	
	case 'cad002GridListaFornecedorProduto':

    	/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D069_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'D069_D070_Id', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Flag_Tabela_Principal', 'colunaTitulo' => 'Usar?', 'callback' => 'checkboxD069FlagTabelaPrincipal', 'callbackParameter' => true),
            array('colunaValor' => 'horizontal', 'colunaTipo' => 'livre', 'callback' => 'horizontal', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data', 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'Validade', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30) as D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'colunaExibir' => false),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Descricao_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false ),
            array('colunaValor' => 'D069_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D069_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'D069_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background:var(--celula-verde)'),
            array('colunaValor' => 'D069_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
            array('colunaValor' => 'D069_Valor_Preco_Unitario', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'background:var(--celula-verde)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
            //array('colunaValor' => 'D069_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'extra' => 'title="teste"'),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);'),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-vermelho);'),
            array('colunaValor' => 'D069_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),

            array('colunaValor' => 'D069_Aliquota_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => '%DIFAL ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),

            array('colunaValor' => 'D069_Valor_ICMS_Diferenca_Aliquotas', 'colunaTitulo' => 'DIFAL ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'ifnull(D069_Valor_Frete,0)+ifnull(D069_Valor_Total_Frete,0)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_Data_Atualizacao_Custo', 'colunaTitulo' => 'Atualização Custo', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D069_Data_Atualizacao_Custo', 'colunaTitulo' => 'Atualização Custo', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true),

            array('colunaValor' => 'D069_Observacao_Entrega', 'colunaTitulo' => 'Prazo entrega', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Observacao_Compra', 'colunaTitulo' => 'Observação', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),

            array('colunaValor' => 'D069_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#A1FCA9', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado-D069_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Flag_ST', 'colunaTitulo' => 'ST?', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_ST_ICMS_Interno', 'colunaTitulo' => '%ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
            array('colunaValor' => 'D069_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_ICMS', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Excluir', 'colunaTitulo' => 'D069_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Dir001_Id', 'colunaExibir' => false, 'Alinhar' => 'center'),
            array('colunaValor' => 'D069_Data_Hora_Ultima_Alteracao', 'colunaTitulo' => 'Data Calculou', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário Calculou', 'style' => 'width:1%'),
            array('colunaValor' => 'D069_Data_Hora_Ultima_Alteracao_Sem_Calcular', 'colunaTitulo' => 'Data Alterado', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
        );
		/**
		* Definição da SQL
		*/
		$from = 'D069';
		
		$extra = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			left join C007 on C007_Id=D069_C007_Id_Ultima_Alteracao
			WHERE D069_D009_Id='{$r_acaoId}'
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D069_Id = id[0].replace(/D069_Id-/, '');
					// Ação: abrir nova janela

				});
			} </script></div>
EOT;
		$gridFiltro = array(
			// 'T128_Id' => array('titulo' => 'Número', 'tipo' => 'texto', 'exibir'),
			// 'T128_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			// 'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			// 'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			// 'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Remessa', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);

		$extra_ = base64_encode($extra);
		if (!personalizacaoBotoes()) {
            // Não executar quando houver o refresh de linha
            if (empty($r_linhaGridId)) {
                echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="cad002AtualizarAliquotas">Atualizar alíquotas e calcular</button>
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#cad002IncluirProdutoFornecedor').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produto tabela fornecedor', '/cad/cad002/content/cad002ContentIncluirProdutoFornecedor/', '&D070_Id={$r_D070_Id}', [600,90]);
					});

					$('#cad002AtualizarAliquotas').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
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
					            $(this).dialog("destroy").remove();
								
								idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					            cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,1);
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						var dialogOptions = dialogConfirm('Atualizar Alíquotas', 'Atualizar as aliquotas igual ao cadastro de produtos: <br />', \$buttons);
						$('<input type="checkbox" checked="checked">').attr('id', 'campoCFOP').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelCFOP').html('CFOP').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox" checked="checked">').attr('id', 'campoNossoCodigo').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelNossoCodigo').html('Nosso Código').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
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

			        function cad002AtualizarAliquotasLoop(idProgressBar, arrayOpcoes, inicio){
		                $.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&opcoes=' + encodeURIComponent(arrayOpcoes) + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&callback=?', function(request) {
		                    if (request.concluido == 0) {
		                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
		                    	cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,0);
		                    }else{
		                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
		                    	dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
		                    	if (request.code) {
		                            divRefresh('{$g['divId']}');
		                        } else {
		                            dialogConfirm('Erro!', request.data);
		                        }
		                    } 
		                });	                    
			        }

					$('#cad002ExcluirProdutoFornecedor').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/cad/cad002/grid_func-ajax/excluirD069Marcados/?ajax=true&D070_Id=' + encodeURIComponent("{$r_D070_Id}") + '&callback=?', function(request) {
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
						dialogConfirm('Atenção', 'Deseja excluir os itens marcados?', \$buttons);
					});
				} </script></div>
EOT;
            }
		}
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002GridOrdemCompraNovo':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T224_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605', false, false, '&tabela=T218')),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'cachePrimaria' => true, 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605', false, false, '&tabela=T224'), 'chavePrimaria' => true),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T225_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T225_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T225_Quantidade_Pendente_3(T225_Id)', 'colunaTitulo' => 'Pendente', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'IF(T225_Flag_Cancelada = "S" OR T224_Flag_Status = "11", "SIM", "NÃO") as itemCancelado', 'colunaTitulo' => 'Item Cancelado', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),		
			array('colunaValor' => 'T224_Nome_Status', 'colunaTitulo' => 'Status OC'),	
			array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
			array('colunaValor' => 'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'center', 'callback' => 'qtePedidos', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'T225_Flag_Cancelada', 'colunaTitulo' => 'T225_Flag_Cancelada', 'colunaExibir' => false),
        );


		$from = 'T225';

		$extra = <<<EOT
				LEFT JOIN D009 ON D009_Id=T225_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D082 ON D082_Id=D049_D082_Id
				LEFT JOIN D001 ON D049_D001_Id=D001_Id
				LEFT JOIN T224 ON T224_Id=T225_T224_Id
				LEFT JOIN T218 ON T218_Id=T224_T218_Id
				LEFT JOIN T219 ON T218_Id=T219_T218_Id
				LEFT JOIN T006 ON T219_Id=T006_T219_Id
				LEFT JOIN T004 ON T219_Id=T004_T219_Id
				LEFT JOIN D024 ON D024_Id=T224_D024_Id
				LEFT JOIN D037 ON D037_Id=D001_D037_Id
				LEFT JOIN C007 AS comprador ON comprador.C007_Id=T224_C007_Id_Vendedor_Interno
				WHERE D009_Id='{$r_acaoId}'
				and if('{$g['C007']['C007_Flag_Vendedor']}' != 'D',(T224_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' OR Comprador.C007_C007_Id = '{$g['usuarioAtual']}'),1=1)
				GROUP BY T225_Id
				ORDER BY T224_Data_Emissao DESC,T225_Id DESC
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T224_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T224_Id' => array('titulo' => 'O.C.', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		// Grid linha
		function gridLinha($array) {
		    global $g;
			if($array['todosCampos']['T224_Flag_Status'] == '11'){
		        return 'linhaVermelho';
		    }elseif($array['todosCampos']['T225_Flag_Cancelada'] == 'S'){
				return 'linhaVermelhoClaro';  
			}elseif($array['todosCampos']['T224_Flag_Status'] <= 0 && $array['todosCampos']['T225_Quantidade_Pendente_3(T225_Id)'] > 0){
				return 'linhaAzul';
			} elseif($array['todosCampos']['T225_Quantidade_Pendente_3(T225_Id)']<=0){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T224_Flag_Status'] == '3'){
				return 'linhaVerde';
			}
		}
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				// pega o ID da linha (verifica as chave(s) primaria(s) do grid)
				var id = $(this).closest('tr').attr('id').split('|');
				var T224_Id = id[0].replace(/T224_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela={$from}', [990,605]);  //950 615
			});
		} </script></div>
EOT;


		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'cad002GridOrdemProducao':
        /*
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade-T256_Quantidade_Produzida', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado","Aberto")', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag Status', 'style' => 'width:1%;', 'colunaExibir' => false),
        );

		$from = 'T256';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
		    LEFT JOIN T255 on T255_Id=T256_T255_Id
		    LEFT JOIN C007 on C007_Id=T255_C007_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			WHERE D009_Id='{$r_acaoId}'
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'cad002GridCotacoesNovo':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T218_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T218_Id', 'colunaTitulo' => 'Cot.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', 'T218_Id', '990,605', false, false, '&tabela=T218'),),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605', false, false, '&tabela=T224'),),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T219_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T219_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T004_T003_Id', 'colunaTitulo' => 'Orçam.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T004_T003_Id', '990,605', false, false, '&tabela=T003'),),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'Ped.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T006_T005_Id', '990,605', false, false, '&tabela=T005'),),
			array('colunaValor' => 'Comprador.C007_Primeiro_Nome as comprador', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'T219_Codigo_Produto', 'colunaTitulo' => 'T219_Codigo_Produto', 'colunaExibir' => false),
			array('colunaValor' => 'T224_Flag_Status', 'colunaTitulo' => 'T224_Flag_Status', 'colunaExibir' => false),
			array('colunaValor' => 'T218_Flag_Perdido', 'colunaTitulo' => 'T218_Flag_Perdido', 'colunaExibir' => false),
			array('colunaValor' => 'T218_Flag_Status_Orcamento', 'colunaTitulo' => 'T218_Flag_Status_Orcamento', 'colunaExibir' => false),	
        );

		$from = 'T219';

		$extra = <<<EOT
				LEFT JOIN D009 ON D009_Id=T219_D009_Id
				LEFT JOIN D049 ON D049_Id=D009_D049_Id
				LEFT JOIN D082 ON D082_Id=D049_D082_Id
				LEFT JOIN D001 ON D049_D001_Id=D001_Id
				LEFT JOIN T218 ON T218_Id=T219_T218_Id
				LEFT JOIN T224 ON T218_Id=T224_T218_Id
				LEFT JOIN T225 ON T224_Id=T225_T224_Id
				LEFT JOIN T004 ON T219_Id=T004_T219_Id
				LEFT JOIN T006 ON T219_Id=T006_T219_Id
				LEFT JOIN D024 ON D024_Id=T218_D024_Id
				LEFT JOIN D037 ON D037_Id=D001_D037_Id
				left join C007 as Comprador on Comprador.C007_Id=T218_C007_Id_Vendedor_Interno
				where D009_Id='{$r_acaoId}'
				and if('{$g['C007']['C007_Flag_Vendedor']}' != 'D',(T218_C007_Id_Vendedor_Interno = '{$g['usuarioAtual']}' OR Comprador.C007_C007_Id = '{$g['usuarioAtual']}'),1=1)
				GROUP BY T218_Id
				ORDER BY T218_Data_Emissao DESC
EOT;

		function gridLinha($array) {
			if($array['todosCampos']['T224_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T218_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T224_Id']<=0){
				if ($array['todosCampos']['T218_Flag_Status_Orcamento'] == 'V') {
					return 'linhaFundoAmareloClaro linhaAzul';
				} else {
					return 'linhaAzul';
				}
			}
		}

	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T218_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'T218_Id' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($r_acaoId){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'cad002GridCotacoes':
		$D009_Id = $r_acaoId;	
		//$C008_Id = $_REQUEST['C008_Id'];
		
		if($g['C031']['multimarcas'] == 'S'){
			/*$cClausula  = " select ";
		    $cClausula .= " D001_Codigo_Produto, ";
		    $cClausula .= " D001_Descricao_Produto,";
		    $cClausula .= " D009_Id";
		    $cClausula .= " from D001";
		    $cClausula .= " left join D049 on D049_D001_Id=D001_Id";
		    $cClausula .= " left join D009 on D009_D049_Id=D049_Id";
			$cClausula .= " where D001_Id='$D001_Id'";
			
			$cClausula .= "   and D009_C004_Id={$g['empresaAtual']}";

			$D001 = mysql_query($cClausula);
			$mD001=mysql_fetch_array($D001);
			
			$D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');*/
		} else {
			$D009_Id = (isset($r_acaoId) ? $r_acaoId : '');
		}
		
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
		    array('colunaValor' => 'T173_Id', 'colunaTitulo' => 'T173_Id', 'colunaExibir' => false),
		    array('colunaValor' => 'T059_Id', 'colunaTitulo' => 'T059_Id', 'colunaExibir' => false),
		    array('colunaValor' => 'T174_Id', 'colunaTitulo' => 'T174_Id', 'colunaExibir' => false),
		    array('colunaValor' => 'T010_Data', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
		    array('colunaValor' => 'T010_Id', 'colunaTitulo' => 'Cotação'),
		    array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
		    array('colunaValor' => 'T059_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'center'),
		    array('colunaValor' => 'T059_Codigo_Produto', 'colunaTitulo' => 'Cod', 'colunaExibir' => false),
		    array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
		    array('colunaValor' => 'T174_Valor_Total', 'colunaTitulo' => 'Valor Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		    array('colunaValor' => '(SELECT SUM((T174_Quantidade * T174_Valor_Custo_Unitario) + (T174_Valor_Custo_Unitario * (IFNULL(T174_IPI,0)/100)*T174_Quantidade) + IFNULL(T174_Valor_ST,0)) FROM T174 LEFT JOIN T059 ON T059_Id=T174_T059_Id WHERE T174_T173_Id=T173_Id ) as totalVenda', 'colunaTitulo' => 'R$ Venda', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
		    array('colunaValor' => 'T059_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaExibir' => false),
		    array('colunaValor' => 'T011_Id', 'colunaTitulo' => 'OC'),
		);


		$from = 'T059';

		$extra = <<<EOT
			LEFT JOIN T010 ON T010_Id=T059_T010_Id
			LEFT JOIN T173 ON T173_T010_Id=T010_Id
			LEFT JOIN T174 ON T174_T059_Id=T059_Id
			LEFT JOIN D024 ON D024_Id=T173_D024_Id
			left join D009 on D009_Id=T059_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D001 on D049_D001_Id=D001_Id
			LEFT JOIN T011 ON T011_T010_Id=T010_Id
			where T059_D009_Id='{$D009_Id}'
			ORDER BY T010_Data DESC
EOT;

	
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				var id = $(this).closest('tr').attr('id');
				var id = id.split('-');
				id = id[1];
				divSetarAcaoId('{$_REQUEST['idTabForm4']}', id);
				divRefresh('{$_REQUEST['idTabForm4']}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		
		if($D009_Id){
			$g['smarty']->display('grid.tpl');		
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'cad002GridEstoqueExterno':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
	        array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'C004_Id', 'colunaTitulo' => 'C004_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
	        array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
	        array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade'),
	        array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', 'D009_Id', '700,400', true)),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
            array('colunaValor' => 'precoTransferencia', 'colunaTitulo' => 'Preço Transf.', 'colunaAlinhar' => 'right', 'callback' => 'retornaPrecoTransferencia', 'style' => 'width:1%', 'colunaTipo' => 'livre', 'callbackParameter' => array($r_T003_Id, $r_T005_Id, false, $r_idItemVenda)),
        //array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Ordem de Compra', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
	    );

		$from = 'D009';

		$C004_Id = isset($r_C004_Id) ? $r_C004_Id : $g['empresaAtual'];
		$extra = <<<EOT
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN C004 ON C004_Id = D009_C004_Id
			LEFT JOIN D020 ON D020_Id = C004_D020_Id
			LEFT JOIN D018 ON D018_Id = C004_D018_Id
 			WHERE D001_Id = '{$r_acaoId}'
			AND C004_Id != '{$C004_Id}'
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;

	case 'cad002GridProdutosListaFornecedor':
		$filtroObrigatorio = true;
		/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D069_Id', $g['divId']), 'colunaExibir' => 'S'),
            array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_D009_Id', 'colunaTitulo' => 'D009_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Campos_Atualizados', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'callback' => 'camposAtualizadosTabFornecedor', 'style' => 'width: 1%'),
            array('colunaValor' => 'D069_Flag_Tabela_Principal', 'colunaTitulo' => 'Usar?', 'colunaEditar' => false, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'callback' => 'checkboxD069FlagTabelaPrincipal'),
            array('colunaValor' => 'D070_Id', 'colunaTitulo' => 'Tab', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,15)', 'colunaTitulo' => 'Fornecedor', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso', 'colunaAlinhar' => 'left', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaExibir' => false),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'extra' => 'title="teste"'),
            array('colunaValor' => 'if(D005_Classificacao_Fiscal is null,D069_NCM,D005_Classificacao_Fiscal)', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_NCM', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);'),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaExibir' => false),
            array('colunaValor' => 'D069_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'D069_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => false),
            array('colunaValor' => 'D069_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => false, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
            array('colunaValor' => 'D069_Valor_Preco_Unitario', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
            array('colunaValor' => 'D069_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => false, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'ifnull(D069_Valor_Frete,0)+ifnull(D069_Valor_Total_Frete,0)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#A1FCA9', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaEditar' => false, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado-D069_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao_Entrega', 'colunaTitulo' => 'Prazo entrega', 'colunaEditar' => false, 'style' => 'background: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => false, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => false, 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);'),
            array('colunaValor' => 'D069_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Base_ICMS', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Peso_Unitario', 'colunaTitulo' => 'Peso unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D069_Codigo_Barras', 'colunaTitulo' => 'EAN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Excluir', 'colunaTitulo' => 'D069_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Dir001_Id', 'colunaExibir' => false, 'Alinhar' => 'center'),
            array('colunaValor' => 'D069_D005_Id', 'colunaTitulo' => 'D069_D005_Id', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'D069 Class.Fiscal', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Data_Atualizacao_Custo', 'colunaTitulo' => 'Atualização Custo', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeData', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D069_Data_Hora_Ultima_Alteracao', 'colunaTitulo' => 'Última alteração', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D069';

		$extra = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			left join C007 on C007_Id=D069_C007_Id_Ultima_Alteracao
			where D070_C004_Id='{$g['empresaAtual']}'
			group by D069_Id
EOT;

		$extraTotal = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			LEFT JOIN D009 ON D009_Id=D069_D009_Id
			LEFT JOIN D049 ON D049_Id=D009_D049_Id
			LEFT JOIN D001 ON D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			left join C007 on C007_Id=D069_C007_Id_Ultima_Alteracao
			where D070_C004_Id='{$g['empresaAtual']}'
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T004_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D070_Id = id[2].replace(/D070_Id-/, '');
					// Ação: abrir nova janela
		        	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Alterar Tabela Fornecedor', '/cad/cad002/content/cad002ContentListaFornecedorAlterar/', '&acaoId=' + encodeURIComponent(D070_Id) + '&tabela=$from', [1000,600]);
				});
			} </script></div>
EOT;

		$gridFiltro = array(
            'D069_D070_Id' => array('titulo' => 'Tabela', 'tipo' => 'numero'),
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'D069_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'ifnull(D001_Codigo_Produto[[virgula]]\'\')' => array('titulo' => 'Nosso', 'tipo' => 'texto'),
            'D069_Descricao_Produto,D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D069_NCM,D005_Classificacao_Fiscal' => array('titulo' => 'NCM', 'tipo' => 'texto'),
            'D069_Data_Atualizacao_Custo' => array('titulo' => 'Atualização custo', 'tipo' => 'data'),
            'D069_Flag_Tabela_Principal' => array('titulo' => 'Usar?', 'tipo' => 'texto'),
            'D069_Data_Hora_Ultima_Alteracao' => array('titulo' => 'Última alteração', 'tipo' => 'data'),
			'D069_C007_Id_Ultima_Alteracao' => array('titulo' => 'Usuário alterou', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$sql_ = urlencode(base64_encode($geradoSql));

		/**
		* Botões
		*/
		$botoes = array(
			array('titulo' => 'Atualizar Alíquotas', 'id' => 'cad002AtualizarAliquotasTodos'),
		);
		echo Botoes::processar($botoes);

		if (!personalizacaoBotoes()) {

	 		echo <<<EOT
				<!-- <div class="buttonsBottomTop">
					<button type="button" id="cad002AtualizarAliquotasTodos">Atualizar Alíquotas</button>
				</div>  -->
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#cad002AtualizarAliquotasTodos').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
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
								$('#{$g['divId']}').showLoading();
								$.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&opcoes=' + encodeURIComponent(arrayOpcoes) + '&sql=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
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
						var dialogOptions = dialogConfirm('Atualizar Alíquotas', 'Atualizar as aliquotas igual ao cadastro de produtos: <br />', \$buttons);
						$('<input type="checkbox" checked="checked">').attr('id', 'campoCFOP').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelCFOP').html('CFOP').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
						$('<input type="checkbox" checked="checked">').attr('id', 'campoNossoCodigo').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
						$('<label>').attr('id', 'labelNossoCodigo').html('Nosso Código').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
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
				} </script></div>
EOT;
		}

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Totais
		 */
		$totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	
	break;

	/**
	 * Tab Pedidos
	 */
	case 'pedidosProduto':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'style' => 'font-size:11px'),
            array('colunaValor' => 'T006_T006_Id_Dividir', 'colunaTitulo' => 'ID Div', 'colunaAlinhar' => 'center', 'style' => 'font-size:11px'),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçam.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '1000,615', false, false, '&tabela=T003')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '1000,615', false, false, '&tabela=T005')),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T006_Flag_Tipo_Produto', 'colunaTitulo' => 'Tp', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T006_Quantidade_Separacao', 'colunaTitulo' => 'Sep', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'width:1%'),
            //array('colunaValor' => 'T006_Flag_Reservar_Estoque', 'colunaTitulo' => 'Reservado', 'colunaAlinhar' => 'center', 'callback' => 'gCheckbox', 'callbackParameter' => array('T006_Flag_Reservar_Estoque', 'N|S')),
            array('colunaValor' => 'T006_Prioridade', 'colunaTitulo' => 'Prioridade', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaEditarNivel' => 2, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Custo_Unitario', 'colunaTitulo' => 'IPV', 'callback' => 'validaIpvPedidos', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal) as NF', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            //array('colunaValor' => 'GROUP_CONCAT(DISTINCT(IF(T006_T075_Id>0,"MP","PE"))) as Estoque', 'colunaTitulo' => 'Est', 'style' => 'width:1%;'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'T005_Status_Pedido(T005_Flag_Status,1) as Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T075_Id', 'colunaTitulo' => 'DI', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T219_T218_Id', 'colunaTitulo' => 'Cotação', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cotação', '/est/est015/content/est015ContentEditarOrcamento/', 'T219_T218_Id', '1000,615', false, false, '&tabela=T218')),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Cot Qte', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'T225_T224_Id', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de compra', '/est/est016/content/est016content02/', 'T225_T224_Id', '1000,615', false, false, '&tabela=T224')),
            array('colunaValor' => 'T225_Quantidade', 'colunaTitulo' => 'OC Qte', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'T014_T013_Id', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Nota fiscal recebida', '/est/est004/content/est004ContentEditarNotaFiscal/', 'T014_T013_Id', '1000,615', false, false, '&tabela=T013')),
            array('colunaValor' => 'T014_Quantidade', 'colunaTitulo' => 'NF Qte', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D006_Flag_Estoque', 'colunaTitulo' => 'CFOP Estoque', 'colunaAlinhar' => 'center'),
        );

		$from = 'T005';

		$extra = <<<EOT
			left join D024 on D024_Id=T005_D024_Id
    		left join T006 on T006_T005_Id=T005_Id
    		left join D009 on D009_Id=T006_D009_Id
    		left join T004 on T004_Id=T006_T004_Id
    		left join T003 on T003_Id=T004_T003_Id
    		left join D049 on D049_Id=D009_D049_Id
    		left join D082 on D082_Id=D049_D082_Id
    		left join T008 on T008_T006_Id=T006_Id
    		left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
			left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T005_C007_Id_Vendedor_Externo
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			LEFT JOIN T219 ON T006_Id=T219_T006_Id
			LEFT JOIN T225 ON T225_Id=T006_T225_Id
			LEFT JOIN T014 ON T014_T225_Id=T225_Id
			LEFT JOIN D006 ON D006_Id=T006_D006_Id
			LEFT JOIN D021 ON D021_Id=T005_D021_Id
			LEFT JOIN T005A ON T005_Id=T005A_T005_Id
        	where T006_D009_Id='{$r_acaoId}'
    		and (T006_Quantidade>0 or T006_Quantidade_Separacao>0)
    		and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
    		and IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, D009_C004_Id) = D009_C004_Id
			and ifnull(T005A_Flag_Tipo,1) != '2'
    		{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
    		group by T006_Id
    		ORDER BY IF(T007_Id IS NULL,T006_Prioridade,0) DESC, IFNULL(T006_T006_Id_Dividir,0) DESC, T006_Id DESC
EOT;

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// Modelo: NÂO permitir ORDERNAÇÃO
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Status_Pedido(T005_Flag_Status[[virgula]]1)' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		function gridLinha($array) {
		    if($array['todosCampos']['T005_Flag_Status']=='5' or $array['todosCampos']['T005_Flag_Status']=='4'){
		        return 'linhaCinza';
		    } else if($array['todosCampos']['T005_Flag_Status']=='8'){
		        return 'linhaVermelho';
		    } else if($array['todosCampos']['T005_Flag_Status']=='26'){
		        return 'linhaLaranja';
		    } else if($array['todosCampos']['T005_Flag_Status']=='14'){
		        return 'linhaVerde';
		    } else if($array['todosCampos']['T005_Flag_Status']<=0 || $array['todosCampos']['T005_Flag_Status']=='9'){
		       return 'linhaAzul';
		    }
		}

		// Botões
		$botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if($r_acaoId){
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	/**
	 * Tab Orçamentos
	 */
	case 'orcamentosProduto':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orçam.', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '1115,615', false, false, '&tabela=T003')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '1000,615', false, false, '&tabela=T005')),
            array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T004_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'group_concat(distinct T005_Id) as Ped', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'group_concat(distinct T007_Numero_Nota_Fiscal) as NF', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'T003_Status_Orcamento(T003_Flag_Status_Orcamento) as Status', 'colunaTitulo' => 'Status'),
            array('colunaValor' => 'T005_Flag_Status', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Flag_Perdido', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
        );

		$from = 'T003';

		$extra = <<<EOT
			left join T004 on T003_Id=T004_T003_Id
			left join D024 on D024_Id=T003_D024_Id
    		left join T006 on T006_T004_Id=T004_Id
    		left join T005 on T005_Id=T006_T005_Id
    		left join D009 on D009_Id=T004_D009_Id
    		left join D049 on D049_Id=D009_D049_Id
    		left join D082 on D082_Id=D049_D082_Id
    		left join T008 on T008_T006_Id=T006_Id
    		left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
			left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			left join C007 as Externo on Externo.C007_Id=T003_C007_Id_Vendedor_Externo
			left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
			left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
			left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
			left join D021 on D021_Id=T003_D021_Id
        	where T004_D009_Id = '{$r_acaoId}'
    		and (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
    		{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
    		group by T004_Id
    		ORDER BY T003_Data_Emissao DESC
EOT;

		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// Modelo: NÂO permitir ORDERNAÇÃO
				$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T003_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T003_Status_Orcamento(T003_Flag_Status_Orcamento)' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'T003_Id' => array('titulo' => 'Orçamento', 'tipo' => 'texto'),
        );

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		function gridLinha($array) {
			global $g;
			if($array['todosCampos']['T005_Flag_Status']=='5'){
				return 'linhaCinza';
			} else if($array['todosCampos']['T003_Flag_Perdido']=='S'){
				return 'linhaVermelho';
			} else if($array['todosCampos']['T005_Id']>0){
			} else {
				return 'linhaAzul';
			}
		}

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		if($r_acaoId){
			if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		}else{
			echo  '<div align="center"><br /><br /><br />Selecione uma Marca.</div>';		
		}
	break;

	case 'cad002GridFornecedoresProduto':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T241_Id', $g['divId'])),
            array('colunaValor'=>'T241_Id', 'colunaTitulo'=>'Código', 'chavePrimaria'=>true, 'colunaExibir' => false),
            array('colunaValor'=>'T241_Flag_Principal', 'colunaTitulo'=>'', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'callback' => 'gCheckbox', 'callbackParameter' => array('T241_Flag_Principal', 'N|S')),
            array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Fornecedor'),
            array('colunaValor'=>"T241_Data", 'colunaTitulo' => 'Cadastrado', 'callback' => 'gCorrigeData', 'style' => 'width:1%'),
            array('colunaValor'=>"IF(T241_Flag_Homologado = 'S', 'SIM', 'NÃO')", 'colunaTitulo'=>'Homologado', 'style' => 'width:1%'),
            array('colunaValor'=>"T241_Flag_Homologado", 'colunaTitulo'=>'Flag_Homologado', 'style' => 'width:1%', 'colunaExibir' => false),
        );
        
        /**
        * Definição da SQL
        */
        $from = 'T241';
        $extra = <<<EOT
            left join D024 on D024_Id=T241_D024_Id
            where T241_D001_Id='{$r_acaoId}'
EOT;

        /**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
			if ($array['todosCampos']['T241_Flag_Homologado']=='N') 
			{
				return 'linhaVermelho';
            }
		}

        /**
        * Botões (Versão Acima do GRID)
        */
        echo <<<EOT
            <div class="buttonsBottomTop">
EOT;
		$sqlD001 = mysql_query("SELECT D001_Flag_Pre_Cadastro FROM D001 WHERE D001_Id = '{$r_acaoId}'");
		$D001 = mysql_fetch_assoc($sqlD001);
		if($D001['D001_Flag_Pre_Cadastro'] == 'S'){
			$attrDisabled = (isset($g['c029Ids'][187])) ? "" : "disabled='disabled'";
		} else {
       		$attrDisabled = (isset($g['c029Ids'][186])) ? "" : "disabled='disabled'";
		}
        echo <<<EOT
            <button type="button" id="cad002IncluirFornecedorProduto" {$attrDisabled}>Incluir</button>
EOT;
        echo <<<EOT
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {      
                $('#cad002IncluirFornecedorProduto').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Fornecedor', '/cad/cad002/content/cad002contentCadastroFornecedorProduto/','&D001_Id={$r_acaoId}', [560,95]);
                });
            } </script></div>
EOT;

        /**
        * Filtro
        */
        $gridFiltro = array(
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'Texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
    
        /**
        * Geração: Grid
        */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        //$g['smarty']->assign('gridColunasFixo', 2);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    /**
	 * Tab itensMontagem
	 */
	case 'cad002GridItensMontagem':
		/**
		 * Definição do grid tabela
		 */

		$grid = array(
	        array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D042_Id', $g['divId'])),
	        array('colunaValor' => 'D042_Quantidade', 'colunaTitulo' => 'Quant.', 'colunaEditar' => true, 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3'),
	        array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Produto', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
	        array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
	        //array('colunaValor' => 'D009_Valor_Custo_Compra', 'colunaTitulo' => 'Custo Compra', 'callback' => 'formatarNumeroCustoCompra', 'colunaAlinhar' => 'right'),
	        //array('colunaValor' => 'custoTotal', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Custo Total', 'callback' => 'validaCustoTotalMontagem', 'colunaAlinhar' => 'right'),
	        //array('colunaValor' => 'D049_Valor_Preco_Tabela*(1-(D049_Percentual_Desconto_Tabela/100)) as D049_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tabela', 'callback' => 'formatarNumeroCustoTabela', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'custoTotal2', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Custo Total', 'callback' => 'validaCustoTotal2Montagem', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Real', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'floor(D009_Quantidade_Estoque_Real/D042_Quantidade) as Quantidade_Minima', 'colunaTitulo' => 'Disponib.', 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'D042_Observacao', 'colunaTitulo' => 'Observação', 'callback' => 'validaObervacaoMontagem', 'colunaEditar' => true),
	        array('colunaValor' => 'D042_Id', 'colunaTitulo' => '', 'colunaExibir' => false, 'chavePrimaria' => true),
	        //array('colunaValor' => '(select D041_Valor_Base from D041 where D041_D049_Id=D049_Id limit 1) as Preco_Unit', 'colunaTitulo' => '', 'colunaExibir' => false),
	        //array('colunaValor' => '((select D041_Valor_Base from D041 where D041_D049_Id=D049_Id limit 1)*D042_Quantidade) as Preco_Total', 'colunaTitulo' => '', 'colunaExibir' => false),
	    );
		$from = 'D042';
		$extra = <<<EOT
	      		left join D049 on D049_Id=D042_D049_Id_Itens
	    		left join D082 on D049_D082_Id=D082_Id
			 	left join D001 on D001_Id=D049_D001_Id
	    		left join D009 on D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id={$g['empresaAtual']}
		    	where (D042_D049_Id='{$r_acaoId}' and D009_C004_Id={$g['empresaAtual']})
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;

	case 'cad002GridEstoqueGrade':
		$C004_Id = isset($r_C004_Id) ? $r_C004_Id : $g['empresaAtual'];
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();

		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		$T005_Id = isset($r_T005_Id) ? $r_T005_Id : false;

		$D001 = mysql_query("SELECT D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$r_D001_Id}'");
		$mD001 = mysql_fetch_array($D001);

		$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($mD001['D001_Id'], $mD001['D001_Codigo_Produto']);

		/**
		 * Definição do grid tabela
		 */
		$grid = array(
	        array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
	        array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
	        array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'colunaExibir' => false),
	        array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
	        array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', 'D009_Id', '700,400', true), 'style' => 'width: 1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%', 'style' => 'width: 1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', 'D009_Id', '700,400', true), 'style' => 'width:1%', 'style' => 'width: 1%'),
	        //array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Ordem de Compra', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
	    );

	    if ($T003_Id > 0) {
	  		$grid[] = array('colunaValor' => 'preco', 'colunaTitulo' => 'Preço', 'colunaTipo' => 'livre' , 'colunaExibir' => true , 'callback' => 'retornaPrecoVenda' , 'callbackParameter' => array($T003_Id), 'colunaAlinhar' => 'right');
	    } else if ($T005_Id>0) {
	  		$grid[] = array('colunaValor' => 'preco', 'colunaTitulo' => 'Preço', 'colunaTipo' => 'livre' , 'colunaExibir' => true , 'callback' => 'retornaPrecoVendaPedido' , 'callbackParameter' => array($T005_Id), 'colunaAlinhar' => 'right');
	    }

		$from = 'D009';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D082 ON D082_Id = D049_D082_Id
			LEFT JOIN C004 ON C004_Id = D009_C004_Id
			LEFT JOIN D020 ON D020_Id = C004_D020_Id
			LEFT JOIN D018 ON D018_Id = C004_D018_Id
 			WHERE D001_Codigo_Produto LIKE '{$padraoSimilares}'
			AND C004_Id = '{$C004_Id}'
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		//caso o T005 esteja setado o click de linha chama a função do js em ven002/form_func_js passando o id do grid, o D009_Id e o T005_Id
		//ao chamar essa função os dados do produto selecionado sao adicionados ao form do editar produto(tela de tras) permitindo o cliente buscar e adicionar ao pedido
		if($T005_Id>0){
			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
						// (substituir pelo mesmo ID da chave primária do grid)
						var chavePrimaria = 'D009_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');
						// Ação: abrir nova janela
						campoProcuraProdutoGrid("{$g['divId']}", acaoId, 0, {$T005_Id});
					});	
				} 
				</script></div>
EOT;
		}else if($T003_Id){
			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
						// (substituir pelo mesmo ID da chave primária do grid)
						var chavePrimaria = 'D009_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');
						// Ação: abrir nova janela
						ven001CampoProcuraProdutoGrid("{$g['divId']}", acaoId, 0, {$T003_Id});
					});	
				} 
				</script></div>
EOT;
		}	
		

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;

	case 'cad002GridProdutosHistoricoMontagem':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T071_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T071_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
		    //array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq.Físico', 'colunaAlinhar' => 'right', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq.Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq.Loja', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            //array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qte.OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%'),
		    //array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq.Fora', 'colunaAlinhar' => 'right', 'callback' => 'estoqueFora'),
            array('colunaValor' => 'T001_Quantidade', 'colunaTitulo' => 'Quant.', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T001_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo Unit.', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'T001_Valor_Custo_Unitario*T001_Quantidade', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'style' => 'width:1%', 'colunaCasasDecimais' => 2, 'colunaExibirNivel' => 2),
            //array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Compra', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaExibirNivel' => 4),
            array('colunaValor' => 'T071_Observacao', 'colunaTitulo' => 'Observação'),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaExibir' => false),
            array('colunaValor' => 'T048_Quantidade', 'colunaTitulo' => 'Qnt', 'colunaExibir' => false),
            array('colunaValor' => 'T047_Flag_Status', 'colunaTitulo' => 'Quantidade', 'colunaExibir' => false),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T071';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
		
				left join T048 on T048_Id=T071_T048_Id
				left join T047 on T047_Id=T048_T047_Id
				left join D009 on D009_Id=T071_D009_Id
				left join D049 on D049_Id=D009_D049_Id
				left join D001 on D001_Id=D049_D001_Id
				left join D082 on D082_Id=D049_D082_Id
				left join T001 on T071_Id=T001_T071_Id
				where T071_T048_Id='{$r_acaoId}'
EOT;
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		// echo <<<EOT
			// <div class="buttonsBottomTop">
				// <button type="button" id="cad046FormCadastroMotivosCanecelamento">Incluir</button>
// 				
			// </div>
			// <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// $('#cad046FormCadastroMotivosCanecelamento').unbind('click').bind('click', function(data) {
					// abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Montagens', '/est/est007/content/est007ContentMontagensIncluir/', '&tab=geral', [400,300]);
				// });
			// } </script></div>
// EOT;
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D131_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T048_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagens - Nº: ' + acaoId, '/est/est007/content/est007ContentMontagensIncluirSubItem/', '&acaoId=' + encodeURIComponent(acaoId), [400,300]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002GridSimilares':

		$D009_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		$D001=mysql_query("SELECT D001_Id from D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id='{$D009_Id}'");
		$mD001 = mysql_fetch_array($D001);
		$D001_Id = $mD001['D001_Id'];
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D156_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'D156_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => true),
 			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
 			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Liq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D156_Observacao', 'colunaTitulo' => 'Observação'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "D156";
		
		$extra = <<<EOT
		LEFT JOIN D001 ON D001_Id=D156_D001_Id_2
		LEFT JOIN D049 ON D049_D001_Id=D001_Id
		LEFT JOIN D009 ON D009_D049_Id=D049_Id
		WHERE (D156_D001_Id_1='{$D001_Id}' OR D156_D001_Id_2='{$D001_Id}') AND D009_Id != '{$D009_Id}' AND D009_C004_Id='{$g['empresaAtual']}'
		GROUP BY D001_Id
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'D044_Moeda' => array('titulo' => 'Moeda', 'tipo' => 'texto'),
			'D044_Simbolo' => array('titulo' => 'Símbolo', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
	
		/**
		* Botões (Versão Acima do GRID)
		*/
		$disabled='';
		if (!isset($g['c029Ids'][209])) {
			$disabled = "disabled='disabled'";
		}

		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" {$disabled} id="cad002BotaoIncluirSimilar">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad002BotaoIncluirSimilar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Similar', '/cad/cad002/content/cad002ContentIncluirSimilar/','&D001_Id=' + encodeURIComponent('{$D001_Id}'), [500,220]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/D156_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Similar', '/cad/cad002/content/cad002ContentIncluirSimilar/', '&acaoId=' + encodeURIComponent(id) + '&D001_Id=' + encodeURIComponent('{$D001_Id}') + '&tabela={$from}', [500,220]);
			});
		} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'cad002GridEstoqueSimilares':
		$C004_Id = isset($r_C004_Id) ? $r_C004_Id : $g['empresaAtual'];
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();

		$T003_Id = $r_T003_Id;
		$T005_Id = $r_T005_Id;		
		
		$sql = mysql_query("SELECT D009_Id FROM D009 LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE D001_Id = '{$r_D001_Id}' AND D009_C004_Id = '{$C004_Id}'");
		$row = mysql_fetch_array($sql);
		$D009_Id = $row['D009_Id'];

		$D001 = mysql_query("SELECT D001_Id, D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$r_D001_Id}'");
		$mD001 = mysql_fetch_array($D001);
		$padraoSimilares = $CAD002->reprocessaEstoqueSimilares($mD001['D001_Id'], $mD001['D001_Codigo_Produto']);
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D156_Id', $g['divId']), 'style' => 'width: 1%'),
	        array('colunaValor' => 'D156_Id', 'colunaTitulo' => 'D156_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
	        array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
	        array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
	        array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'colunaExibir' => false),
	        array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
	        array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Líquido', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', 'D009_Id', '700,400', true), 'style' => 'width: 1%'),
	        array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right', 'style' => 'width: 1%'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width:1%', 'style' => 'width: 1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', 'D009_Id', '700,400', true), 'style' => 'width:1%', 'style' => 'width: 1%'),
	        //array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Ordem de Compra', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'right'),
	        array('colunaValor' => 'D009_Id','colunaTitulo' => 'D009_Id', 'colunaExibir' => false, 'chavePrimaria' => true ),
	    );

  		if ($T003_Id > 0) {
	  		$grid[] = array('colunaValor' => 'preco', 'colunaTitulo' => 'Preço', 'colunaTipo' => 'livre' , 'colunaExibir' => true , 'callback' => 'retornaPrecoVenda' , 'callbackParameter' => array($T003_Id), 'colunaAlinhar' => 'right');
	    } else if ($T005_Id>0) {
	  		$grid[] = array('colunaValor' => 'preco', 'colunaTitulo' => 'Preço', 'colunaTipo' => 'livre' , 'colunaExibir' => true , 'callback' => 'retornaPrecoVendaPedido' , 'callbackParameter' => array($T005_Id), 'colunaAlinhar' => 'right');
	    }

		$from = 'D009';

		$extra = <<<EOT
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D082 ON D082_Id = D049_D082_Id
			LEFT JOIN C004 ON C004_Id = D009_C004_Id
			LEFT JOIN D020 ON D020_Id = C004_D020_Id
			LEFT JOIN D018 ON D018_Id = C004_D018_Id
			LEFT JOIN D156 ON D156_D001_Id_2=D001_Id OR D156_D001_Id_1=D001_Id
 			WHERE (D156_D001_Id_1='{$r_D001_Id}' OR D156_D001_Id_2='{$r_D001_Id}') AND D009_Id != '{$D009_Id}'
			AND C004_Id = '{$C004_Id}' 
			GROUP BY D001_Id
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		$disabled='';
		if (!isset($g['c029Ids'][209])) {
			$disabled = "disabled='disabled'";
		}
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" {$disabled} id="cad002BotaoIncluirSimilar">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad002BotaoIncluirSimilar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Similar', '/cad/cad002/content/cad002ContentIncluirSimilar/','&D001_Id=' + encodeURIComponent('{$r_D001_Id}'), [500,220]);
				});
			} </script></div>
EOT;
		//caso o T005 esteja setado o click de linha chama a função do js em ven002/form_func_js passando o id do grid, o D009_Id e o T005_Id
		//ao chamar essa função os dados do produto selecionado sao adicionados ao form do editar produto(tela de tras) permitindo o cliente buscar e adicionar ao pedido
		if($T005_Id>0){
			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
						// (substituir pelo mesmo ID da chave primária do grid)
						var chavePrimaria = 'D009_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');
						// Ação: abrir nova janela
						campoProcuraProdutoGrid("{$g['divId']}", acaoId, 0, {$T005_Id});
					});	
				} 
				</script></div>
EOT;
		}else if($T003_Id>0){
			/**
			* Define ação do click de abertura (ao clicar em uma linha do grid)
			*/
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var dbclickPrevent = false;
					$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
						// Previne duplo click (abrir duas janelas)
						if (dbclickPrevent) { return false; }
						dbclickPrevent = true;
						setTimeout(function() { dbclickPrevent = false; }, 500);
						// (substituir pelo mesmo ID da chave primária do grid)
						var chavePrimaria = 'D009_Id';	
						if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
						var id = $(this).closest('tr').attr('id').split('|');
						var acaoId = id[0].replace(chavePrimaria + '-', '');
						// Ação: abrir nova janela
						ven001CampoProcuraProdutoGrid("{$g['divId']}", acaoId, 0, {$T003_Id});
					});	
				} 
				</script></div>
EOT;
		}
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */	

		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		$g['smarty']->display('grid.tpl');
	break;

	case 'cad002GridSimilaresGeral':

		// $D009_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		// $D001=mysql_query("SELECT D001_Id from D001 LEFT JOIN D049 ON D049_D001_Id=D001_Id LEFT JOIN D009 ON D009_D049_Id=D049_Id WHERE D009_Id='{$D009_Id}'");
		// $mD001 = mysql_fetch_array($D001);
		// $D001_Id = $mD001['D001_Id'];
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
 			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D156_Id', $g['divId']), 'style' => 'width: 1%'),
 			array('colunaValor' => 'D156_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'D001_1.D001_Id', 'colunaTitulo' => 'Id', 'style' => 'width: 1%', 'colunaExibir' => false, 'chavePrimaria'=>true),
 			array('colunaValor' => 'D001_1.D001_Codigo_Produto', 'colunaTitulo' => 'Código 1', 'style' => 'width: 1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_1.D001_Id', '1000,583')),
 			array('colunaValor' => 'D001_1.D001_Descricao_Produto', 'colunaTitulo' => 'Descrição 1', 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_1.D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_1.D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Liq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D001_2.D001_Id', 'colunaTitulo' => 'Id', 'style' => 'width: 1%', 'colunaExibir' => false),
 			array('colunaValor' => 'D001_2.D001_Codigo_Produto', 'colunaTitulo' => 'Código 2', 'style' => 'width: 1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_2.D001_Id', '1000,583')),
 			array('colunaValor' => 'D001_2.D001_Descricao_Produto', 'colunaTitulo' => 'Descrição 2', 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_2.D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D009_2.D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque Liq', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'style'=>'width:1%;'),
 			array('colunaValor' => 'D156_Observacao', 'colunaTitulo' => 'Observação'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "D156";
		
		$extra = <<<EOT
		LEFT JOIN D001 as D001_1 ON D001_1.D001_Id=D156_D001_Id_1
		LEFT JOIN D049 as D049_1 ON D049_1.D049_D001_Id=D001_1.D001_Id
		LEFT JOIN D009 as D009_1 ON D009_1.D009_D049_Id=D049_1.D049_Id
		LEFT JOIN D001 as D001_2 ON D001_2.D001_Id=D156_D001_Id_2
		LEFT JOIN D049 as D049_2 ON D049_2.D049_D001_Id=D001_2.D001_Id
		LEFT JOIN D009 as D009_2 ON D009_2.D009_D049_Id=D049_2.D049_Id
		WHERE (D009_1.D009_C004_Id='{$g['empresaAtual']}' OR D009_2.D009_C004_Id='{$g['empresaAtual']}')
		GROUP BY D156_Id
EOT;
		/**
		* Filtro
		*/		
		$gridFiltro = array(
			'concat(D001_1.D001_Codigo_Produto[[virgula]]D001_2.D001_Codigo_Produto)' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'concat(D001_1.D001_Descricao_Produto[[virgula]]D001_2.D001_Descricao_Produto)' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		);

		/**
		 * Botões
		 */
		$botoes = array(
		);
		echo Botoes::processar($botoes);
	
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<!-- <button type="button" id="cad002BotaoIncluirSimilar">Incluir</button>-->
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#cad002BotaoIncluirSimilar').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Similar', '/cad/cad002/content/cad002ContentIncluirSimilar/','&D001_Id=' + encodeURIComponent('{$D001_Id}'), [500,220]);
				});
			} </script></div>
EOT;

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
			
		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        /* echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;
*/
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var acaoId = $(this).closest('tr').attr('id').split('|');
				var id = acaoId[0].replace('D156_Id-', '');
				var D001_Id = acaoId[1].replace('D001_1.D001_Id-', '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Similar', '/cad/cad002/content/cad002ContentIncluirSimilar/', '&acaoId=' + encodeURIComponent(id) + '&D001_Id=' + encodeURIComponent(D001_Id) + '&tabela={$from}', [500,220]);
			});
		} </script></div>
EOT;
		
		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'cad002GridListaFornecedorProdutoVenda':

    	/**
		* Definição do Grid (Tabela)
		*/		
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D069_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor' => 'D069_D070_Id', 'colunaTitulo' => 'Tabela', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Tabela_Principal', 'colunaTitulo' => 'Usar?', 'callback' => 'checkboxD069FlagTabelaPrincipal', 'callbackParameter' => true, 'colunaExibir' => false),
            array('colunaValor' => 'horizontal', 'colunaTipo' => 'livre', 'callback' => 'horizontal', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data', 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'D070_Data_Vencimento', 'colunaTitulo' => 'Validade', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'colunaExibir' => false),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,30) as D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
            array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Nosso', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Aplicacao', 'colunaTitulo' => 'Referencia', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Descricao_Produto', 'colunaTitulo' => 'Produto', 'colunaExibir' => false ),
            array('colunaValor' => 'D069_Marca', 'colunaTitulo' => 'Marca', 'style' => 'width: 1%'),
            array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D069_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D069_T075_Id', 'colunaTitulo' => 'Estoque', 'callback' => 'montaGridEstoque', 'colunaExibir' => false),
            array('colunaValor' => 'D069_T206_Id', 'colunaExibir' => false),
            array('colunaValor' => 'estoqueMedioPrazo', 'colunaTipo' => 'livre', 'colunaTitulo' => 'MP', 'colunaAlinhar' => 'right', 'callback' => 'estoqueMP', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background:var(--celula-verde)', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Unitario', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'background:var(--celula-verde)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            //array('colunaValor' => 'D069_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preco Unitario', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Total_Preco', 'colunaTitulo' => 'Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'extra' => 'title="teste"', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D005_Classificacao_Fiscal,\'-\',D005_Flag_Aplicacao) as D005_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-vermelho);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao_Entrega', 'colunaTitulo' => 'Entrega dias', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'ifnull(D069_Valor_Frete,0)+ifnull(D069_Valor_Total_Frete,0)', 'colunaTitulo' => 'Frete R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Financeiro', 'colunaTitulo' => 'Finan R$', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Data_Atualizacao_Custo', 'colunaTitulo' => 'Atualização Custo', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_IPV', 'colunaTitulo' => 'IPV', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background:#A1FCA9', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda', 'colunaTitulo' => 'Preço venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado', 'colunaTitulo' => 'Venda', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Preco_Venda_Negociado-D069_Valor_Preco_Venda', 'colunaTitulo' => 'Sobra', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-azul);', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Preco_Tabela', 'colunaTitulo' => 'Custo Tab', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Valor_Custo_Medio_Unitario', 'colunaTitulo' => 'Custo Medio', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Reducao_ICMS', 'colunaTitulo' => '% Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_ST', 'colunaTitulo' => 'ST?', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_ICMS_Inter_Estadual', 'colunaTitulo' => '%ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_ST_ICMS_Interno', 'colunaTitulo' => '%ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D069_ST_VA_Ajustada', 'colunaTitulo' => 'Ajustada', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo);', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Base_ICMS_Substituicao', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Base_IPI', 'colunaTitulo' => 'Base IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Base_ICMS', 'colunaTitulo' => 'Base ST', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Valor_Total_Agregados', 'colunaTitulo' => 'Agregados', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Numero_Pedido_Compra', 'colunaTitulo' => 'Num.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Item_Pedido_Compra', 'colunaTitulo' => 'Item.Ped.', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D069_Flag_Excluir', 'colunaTitulo' => 'D069_Flag_Excluir', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Dir001_Id', 'colunaExibir' => false, 'Alinhar' => 'center'),
        );
		/**
		* Definição da SQL
		*/
		$from = 'D069';
		
		$extra = <<<EOT
			left join D070 on D070_Id=D069_D070_Id
			left join D024 on D024_Id=D070_D024_Id
			left join D037 on D037_Id=D069_D037_Id
			left join D009 on D009_Id=D069_D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D006 on D006_Id=D069_D006_Id
			left join D005 on D005_Id=D069_D005_Id
			WHERE D069_D009_Id='{$r_acaoId}'
EOT;
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo  <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T004_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D069_Id = id[0].replace(/D069_Id-/, '');
					// Ação: abrir nova janela

				});
			} </script></div>
EOT;
		$gridFiltro = array(
			// 'T128_Id' => array('titulo' => 'Número', 'tipo' => 'texto', 'exibir'),
			// 'T128_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			// 'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			// 'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
			// 'T007_Numero_Nota_Fiscal' => array('titulo' => 'NF Remessa', 'tipo' => 'texto'),
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad002GridCadastroFornecedoresProdutos':
		$importacaoDados = true;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T241_Id', $g['divId']), 'style' => 'width:1%'),
			array('colunaValor' => 'T241_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%'),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código Fornecedor', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código Produto', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'SUBSTR(D082_Marca, 1, 15) AS D082_Marca', 'colunaTitulo' => 'Marca'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T241';
		$extra = <<<EOT
			LEFT JOIN D024 ON D024_Id = T241_D024_Id  
			LEFT JOIN D001 ON D001_Id = T241_D001_Id  
			LEFT JOIN D049 ON D001_Id = D049_D001_Id 
			LEFT JOIN D082 ON D082_Id = D049_D082_Id 
EOT;
		
		/**
		* Botões
		*/
		$botoes = array(
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
EOT;
	
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T241_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
			'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Produto', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código do Produto', 'tipo' => 'numero'),
			'D024_Id' => array('titulo' => 'Código do Fornecedor', 'tipo' => 'numero'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		

		/**
		* Totais
		*/
		/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(DISTINCT T241_Id)", $from, gRetirarGroupBy($extra));
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Fornecedores<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
*/		
		/**
         * Totais
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
EOT;


		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'cad002FIFO':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
			array('colunaValor'=>'nfEmitida.T289_Id', 'colunaTitulo'=>'T289_Id Emitida', 'chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor'=>'nfRecebida.T289_Id', 'colunaTitulo'=>'T289_Id Recebida', 'chavePrimaria'=>true, 'colunaExibir' => false),
			//array('colunaValor'=>'IF(T289A_Id > 0, nfEmitida.T289_Id, nfRecebida.T289_Id) as T289_Id', 'colunaTitulo'=>'Código', 'colunaExibir' => true),
			array('colunaValor'=>'T290_Id', 'chavePrimaria'=>true, 'colunaTitulo'=>'Código', 'colunaExibir' => false),
            array('colunaValor'=>'IF(T289A_Id > 0 , cabecalhoNfEntrada.T007_Numero_Nota_Fiscal , T013_Numero_Nota_Fiscal) as nfEntrada', 'colunaTitulo'=>'NF Entrada', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor'=> 'excluirLinhaT289', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T289_Id', $g['divId']), 'colunaExibir' => false),
			array('colunaValor'=>'cabecalhoNfVenda.T007_Numero_Nota_Fiscal as nfSaida', 'colunaTitulo'=>'NF Saída', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
			array('colunaValor'=> 'excluirLinhaT290', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T290_Id', $g['divId']), 'colunaExibir' => false),
            array('colunaValor'=>"T001_Data_Lancamento", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width:1%'),
            array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Fornecedor/Cliente'),
            array('colunaValor'=>'if(T289A_Id > 0, nfEmitida.T289_Quantidade, nfRecebida.T289_Quantidade) as T289_Quantidade', 'colunaTitulo'=>'Qtde Entrada', 'style' => 'background: var(--celula-amarelo); width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaMetrica' => 'S'),
			array('colunaValor'=>'if(T289A_Id > 0, nfEmitida.T289_Quantidade_Saldo, nfRecebida.T289_Quantidade_Saldo) as T289_Quantidade', 'colunaTitulo'=>'Saldo Entrada', 'style' => 'background: var(--celula-amarelo); width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaMetrica' => 'S'),
			array('colunaValor'=>'T290_Quantidade', 'colunaTitulo'=>'Qtde Saída', 'style' => 'background: var(--celula-verde);;width:1%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'), 
            //array('colunaValor'=>'IF(T289A_Id> 0 , nfEmitida.T289_Flag_Operacao, nfRecebida.T289_Flag_Operacao) as  T289_Flag_Operacao', 'colunaTitulo'=>'', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),  
			array('colunaValor'=>'nfEmitida.T289_Flag_Operacao', 'colunaTitulo'=>'', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),  
            array('colunaValor'=>'nfRecebida.T289_Flag_Operacao', 'colunaTitulo'=>'', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),  
			array('colunaValor'=>'T290_Flag_Operacao', 'colunaTitulo'=>'T290_Flag_Operacao', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-verde);', 'colunaExibir' => false),  
			//array('colunaValor'=>'if(nfRecebida.T289_Valor_Base_ICMS_Substituicao_Unitario is null, T290_Valor_Base_ICMS_Substituicao, T289_Valor_Base_ICMS_Substituicao_Unitario*T289_Quantidade)', 'colunaTitulo'=>'Base ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			//array('colunaValor'=>'if(nfRecebida.T289_Valor_ICMS_Substituicao_Unitario is null, T290_Valor_ICMS_Substituicao, nfRecebida.T289_Valor_ICMS_Substituicao_Unitario*nfRecebida.T289_Quantidade)', 'colunaTitulo'=>'Valor ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			//array('colunaValor'=>'if(nfRecebida.T289_Base_ICMS_Substituto_Unitario is null, T290_Base_ICMS_Substituto, nfRecebida.T289_Base_ICMS_Substituto_Unitario*nfRecebida.T289_Quantidade)', 'colunaTitulo'=>'Base ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			//array('colunaValor'=>'if(nfRecebida.T289_Valor_ICMS_Substituto_Unitario  is null, T290_Valor_ICMS_Substituto , nfRecebida.T289_Valor_ICMS_Substituto_Unitario*nfRecebida.T289_Quantidade)', 'colunaTitulo'=>'Valor ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			array('colunaValor'=>'if(nfRecebida.T289_Valor_Base_ICMS_Substituicao_Unitario is null AND nfEmitida.T289_Valor_Base_ICMS_Substituicao_Unitario is null, T290_Valor_Base_ICMS_Substituicao, if(nfRecebida.T289_Valor_Base_ICMS_Substituicao_Unitario >=0 , nfRecebida.T289_Valor_Base_ICMS_Substituicao_Unitario*nfRecebida.T289_Quantidade, nfEmitida.T289_Valor_Base_ICMS_Substituicao_Unitario*nfEmitida.T289_Quantidade))', 'colunaTitulo'=>'Base ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			array('colunaValor'=>'if(nfRecebida.T289_Valor_ICMS_Substituicao_Unitario is null AND  nfEmitida.T289_Valor_ICMS_Substituicao_Unitario is null, T290_Valor_ICMS_Substituicao, if(nfRecebida.T289_Valor_ICMS_Substituicao_Unitario > 0, nfRecebida.T289_Valor_ICMS_Substituicao_Unitario*nfRecebida.T289_Quantidade, nfEmitida.T289_Valor_ICMS_Substituicao_Unitario*nfEmitida.T289_Quantidade))', 'colunaTitulo'=>'Valor ST', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			array('colunaValor'=>'if(nfRecebida.T289_Base_ICMS_Substituto_Unitario is null AND nfEmitida.T289_Base_ICMS_Substituto_Unitario is null, T290_Base_ICMS_Substituto, if(nfRecebida.T289_Base_ICMS_Substituto_Unitario > 0 , nfRecebida.T289_Base_ICMS_Substituto_Unitario*nfRecebida.T289_Quantidade, nfEmitida.T289_Base_ICMS_Substituto_Unitario*nfEmitida.T289_Quantidade))', 'colunaTitulo'=>'Base ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			array('colunaValor'=>'if(nfRecebida.T289_Valor_ICMS_Substituto_Unitario is null AND nfEmitida.T289_Valor_ICMS_Substituto_Unitario is null, T290_Valor_ICMS_Substituto , if(nfRecebida.T289_Valor_ICMS_Substituto_Unitario > 0, nfRecebida.T289_Valor_ICMS_Substituto_Unitario*nfRecebida.T289_Quantidade, nfEmitida.T289_Valor_ICMS_Substituto_Unitario*nfEmitida.T289_Quantidade))', 'colunaTitulo'=>'Valor ICMS', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '5'),
			array('colunaValor'=>'T290_T289_Id', 'colunaTitulo'=>'T290_T289_Id', 'colunaExibir' => false),
			array('colunaValor'=>'T289A_T008_Id', 'colunaTitulo'=>'T289A_T008_Id', 'colunaExibir' => false),
			array('colunaValor'=>'T290A_Observacao', 'colunaTitulo'=>'Observacao', 'colunaExibir' => true, 'style' => 'width:1%'),

        );
        
        /**
        * Definição da SQL
        */
        $from = 'T001';
        $extra = <<<EOT
        	LEFT JOIN T289 as nfRecebida ON nfRecebida.T289_T014_Id=T001_T014_Id
			LEFT JOIN T289A ON T289A_T008_Id=T001_T008_Id
			LEFT JOIN T289 as nfEmitida ON T289A_T289_Id=nfEmitida.T289_Id
        	LEFT JOIN T290 ON T290_T008_Id=T001_T008_Id
        	LEFT JOIN T008 as itemNfEntrada ON itemNfEntrada.T008_Id=T289A_T008_Id
        	LEFT JOIN T007 as cabecalhoNfEntrada ON cabecalhoNfEntrada.T007_Id=itemNfEntrada.T008_T007_Id
        	LEFT JOIN T008 as itemNfVenda ON itemNfVenda.T008_Id=T290_T008_Id
        	LEFT JOIN T007 as cabecalhoNfVenda ON cabecalhoNfVenda.T007_Id=itemNfVenda.T008_T007_Id
        	LEFT JOIN T014 ON T014_Id=nfRecebida.T289_T014_Id
        	LEFT JOIN T013 ON T013_Id=T014_T013_Id
        	LEFT JOIN D024 ON D024_Id=T013_D024_Id OR D024_Id=cabecalhoNfVenda.T007_D024_Id OR D024_Id=cabecalhoNfEntrada.T007_D024_Id
			LEFT JOIN T290A ON T290_Id = T290A_T290_Id
            WHERE T001_D009_Id='{$r_acaoId}'
			AND (T290_Id > 0 OR nfRecebida.T289_Id > 0 OR nfEmitida.T289_Id > 0) 
			ORDER BY IF(nfRecebida.T289_Id IS NULL AND nfEmitida.T289_Id IS NULL, T290_T289_Id ,IF(nfRecebida.T289_Id > 0, nfRecebida.T289_Id, nfEmitida.T289_Id)) DESC, T001_Flag_Operacao DESC, T001_Id DESC
EOT;
//ORDER BY nfRecebida.T289_Id,nfEmitida.T289_Id,T290_T289_Id DESC, T001_Flag_Operacao DESC, T001_Id DESC

        /**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
	 	    if ($array['todosCampos']['nfRecebida.T289_Flag_Operacao']=='E' || $array['todosCampos']['nfEmitida.T289_Flag_Operacao']=='E') 
	 	    {
	  	       return 'linhaVerdeEscuro';
	 	    }
	 	    if ($array['todosCampos']['nfRecebida.T289_Flag_Operacao']=='C' || $array['todosCampos']['nfEmitida.T289_Flag_Operacao']) 
	 	    {
	  	       return 'linhaVermelho';
	  		}
			if ($array['todosCampos']['T290_Flag_Operacao'] =='C') 
	 	    {
	  	       return 'linhaVermelho';
	  		}
	  	}
        /**
        * Botões (Versão Acima do GRID)
        */
        echo <<<EOT
EOT;
        /**
        * Filtro
        */
        $gridFiltro = array(
            'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'T289_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
			'T013_Numero_Nota_Fiscal' => array('titulo' => 'NFe Entrada', 'tipo' => 'texto'),
			'T007_Numero_Nota_Fiscal' => array('titulo' => 'NFe Saída', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
    
        /**
        * Geração: Grid
        */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        //$g['smarty']->assign('gridColunasFixo', 2);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

case 'cad002GridRegraConversao':

	$D001_Id = (isset($r_acaoId)) ? $r_acaoId : false;

	/**
	 * Definição do Grid (Tabela)		
	 * Id, Unidade, Produto, Unidade Produto, Quantidade Conversao, Tipo de Operacao
	 */

	$grid = array(			
		array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D184_Id', $g['divId'])),			
		array('colunaValor' => 'D184_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,  'colunaExibir' => true),						
		array('colunaValor' => 'D037_Prod.D037_Unidade', 'colunaTitulo' => 'Unidade Produto', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D037_Conv.D037_Unidade', 'colunaTitulo' => 'Unidade Conversão', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D184_Quantidade_Conversao', 'colunaTitulo' => 'Quantidade Conversão',  'colunaAlinhar' => 'center','colunaCasasDecimais' => 2),
		array('colunaValor' => 'D184_Tipo_Operacao', 'colunaTitulo' => 'Tipo Operação', "callback" => "retornaTipoOperacao",'colunaAlinhar' => 'center'),			
	);

	/**
	 * Definição da SQL
	 */
	$from = "D184";

	$extra = <<<EOT
	LEFT JOIN D037 as D037_Conv ON D037_Conv.D037_Id = D184_D037_Id
	LEFT JOIN D001 ON D184_D001_Id = D001_Id		
	LEFT JOIN D037 as D037_Prod ON D037_Prod.D037_Id=D001_D037_Id		
	WHERE D184_D001_Id='{$D001_Id}'
EOT;


	// Botões acima do grid
	$botoes = array(
		array('titulo' => 'Incluir', 'id' => 'cad002IncluirRegraConversaoBtn'),			
	);
	echo Botoes::processar($botoes);

	/**
	 * Filtro
	 */
	$gridFiltro = array();
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);



	echo <<<EOT
		
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#cad002IncluirRegraConversaoBtn').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Regra de Conversao', '/cad/cad002/content/cad002ContentIncluirConversaoUnidade/','&D001_Id=' + encodeURIComponent('{$D001_Id}'), [500,220]);
			});
		} </script></div>
EOT;

	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);


	/**
	 * Define ação do click de abertura (ao clicar em uma linha do grid)
	 */
	$novoIdTelaForm = uniqid();
	echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
			var id = $(this).closest('tr').attr('id').replace(/D184_Id-/, '');
			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Regra de Conversao', '/cad/cad002/content/cad002ContentIncluirConversaoUnidade/', '&acaoId=' + encodeURIComponent(id) + '&D001_Id=' + encodeURIComponent('{$D001_Id}') + '&tabela={$from}', [500,220]);
		});
	} </script></div>
EOT;

	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) {
		ob_end_clean();
		ob_start();
		$g['smarty']->display('gridHTML.tpl');
	} else {
		$g['smarty']->display('grid.tpl');
	}
	break;

case 'cad002GridECommerceTray':
		
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D185_Id', $g['divId'])),
		array('colunaValor' => 'D185_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor' => 'D185_Codigo', 'colunaTitulo' => 'Codigo e-commerce','style' => 'width: 1%;'),
		array('colunaValor' => 'D185_Descricao', 'colunaTitulo' => 'Descrição e-commerce'),
		array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código Produto Mestre','style' => 'width: 1%;background: var(--celula-amarelo-claro);'),
		array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição Produto Mestre', 'style' => 'background: var(--celula-amarelo-claro);'),
		array('colunaValor' => 'D185_Integrado_Tray', 'colunaTitulo' => 'Integrado', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'colunaEditarOculto' => 'S'),
		array('colunaValor' => 'D185_Integracao_ID', 'colunaTitulo' => 'Id Integração', 'colunaExibir' => true,'style' => 'width: 1%;'),		
		array('colunaValor' => 'D185_Data_Hora_Ult_Envio', 'colunaTitulo' => 'Último envio', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'callback' => 'gCorrigeData'),		
	);
	$from = 'D185';

	$extra = "
		left join D186 on D186_D185_Id = D185_Id
		left join D001 on D186_D001_Id = D001_Id
		GROUP BY D185_Codigo ";

	$botoes = array(
		array('titulo' => 'Novo', 'id' => 'novoProdutoEcommerceTray'),
		array('titulo' => 'Atualizar Tray', 'id' => 'cad002EnviarProdutosTrayBtn'),
	);
	echo Botoes::processar($botoes);

	/**
	* Filtro
	*/
	$gridFiltro = array();

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	echo $g['ui']->gridFiltroPrint($gridFiltro);

	$totais = array();

	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	$novoIdTelaForm = uniqid();
	
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
			$('#novoProdutoEcommerceTray').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Novo Produto E-Commerce', '/cad/cad002/content/cad002ContentFormECommerceTray/', '', [600,400]);
			});

			$('#cad002EnviarProdutosTrayBtn').unbind('click').bind('click', function(data) {
				const btSimNao = {
					"Sim": function() {
						idProgressBar = dialogAlert('Enviando produtos, atributos e categorias',"<span id='progressBar'>Processando</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						cad002EnviarProdutosTray(idProgressBar,1); 
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				};
				// $(this).dialog("destroy").remove();
				dialogConfirm('Atualizar dados', 'Confirma o envio dos dados dos produtos para a tray? ', btSimNao);
			});

		} </script></div>
	</div>
EOT;
	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				// (substituir pelo mesmo ID da chave primária do grid)
				var chavePrimaria = 'D185_Id';	
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var acaoId = id[0].replace(chavePrimaria + '-', '');
				// Ação: abrir nova janela
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto E-Commerce - ID: ' + acaoId, '/cad/cad002/content/cad002ContentTabECommerceTray/', '&acaoId=' + encodeURIComponent(acaoId), [900,500]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		
			function cad002EnviarProdutosTray(idProgressBar, inicio){

				$.getJSON('/cad/cad002/form_func-ajax/enviarProdutosTray/?ajax=true&inicio=' + inicio + '&callback=?&idProgressBar=' + idProgressBar, function(request) {

					$('#dc-' + idProgressBar).dialog("destroy").remove();
					dialogConfirm('Envio concluído','Envio processado');
					if (request.code) {
						divRefresh('{$g['divId']}');
						$(this).dialog("destroy").remove();     
					} 

				}).fail(function(jqXHR, textStatus, errorThrown) {
					alert("Ocorreu um erro inesperado no servidor. Por favor entre em contato com o suporte!");
					divRefresh('{$g['divId']}');
					$('#dc-' + idProgressBar).dialog("destroy").remove();
				});	                  
			}

		} </script></div>
EOT;
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
break;

case 'cad002GridAtributosECommerceTray':

	$D185_Id = $r_acaoId;
		
	/**
	 * Definição do grid tabela
	 */
	$grid = array(
		array('colunaValor' => 'Excluir', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D186_Id', $g['divId'])),
		array('colunaValor' => 'D186_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor' => "D001_Codigo_Produto", 'colunaTitulo' => 'Cód Produto'),
		array('colunaValor' => "D001_Descricao_Produto", 'colunaTitulo' => 'Desc Produto'),
		array('colunaValor' => 'D186_Atributo_Nome', 'colunaTitulo' => 'Nome Atributo'),
		array('colunaValor' => 'D186_Atributo_Valor', 'colunaTitulo' => 'Valor Atributo'),
		array('colunaValor' => 'D186_Integrado_Tray', 'colunaTitulo' => 'Integrado', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;background: var(--celula-amarelo-claro);', 'colunaEditarOculto' => 'S'),
		array('colunaValor' => 'D186_Integracao_ID', 'colunaTitulo' => 'Id Integração', 'colunaExibir' => true,'style' => 'width: 1%;background: var(--celula-amarelo-claro);'),		
		array('colunaValor' => 'D186_Data_Hora_Ult_Envio', 'colunaTitulo' => 'Último envio', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;background: var(--celula-amarelo-claro);', 'callback' => 'gCorrigeData'),		
		
	);
	$from = 'D186';

	$extra = "
		left join D001 on D186_D001_Id = D001_Id
		where D186_D185_Id = {$D185_Id}
		GROUP BY D001_Codigo_Produto ";

	$botoes = array(
		array('titulo' => 'Novo', 'id' => 'novoAtributoEcommerceTray'),
	);
	echo Botoes::processar($botoes);

	/**
	* Filtro
	*/
	$gridFiltro = array();

	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	echo $g['ui']->gridFiltroPrint($gridFiltro);

	$totais = array();

	/**
	 * Geração: Monta o SQL e retorna o dados
	 */	

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	$novoIdTelaForm = uniqid();
	
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
			$('#novoAtributoEcommerceTray').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Novo Atributo E-Commerce', '/cad/cad002/content/cad002ContentFormAtributosECommerceTray/', '&acaoId=&D185_Id={$D185_Id}', [600,400]);
			});
		} </script></div>
	</div>
EOT;
	
	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				// (substituir pelo mesmo ID da chave primária do grid)
				var chavePrimaria = 'D186_Id';	
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var acaoId = id[0].replace(chavePrimaria + '-', '');
				// Ação: abrir nova janela
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Produto E-Commerce - ID: ' + acaoId, '/cad/cad002/content/cad002ContentFormAtributosECommerceTray/', '&acaoId=' + encodeURIComponent(acaoId) + '&D185_Id={$D185_Id}', [600,400]);
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

break;

case 'cad002GridAtualizarTabelaGeralFornecedor':
	$filtroObrigatorio = true;

	$grid = array(
		array('colunaValor' => 'D069_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
		array('colunaValor' => 'D069A_Id', 'colunaTitulo' => 'D069A_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
		array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Id. Fornecedor'),
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
		array('colunaValor' => 'D070_Id', 'colunaTitulo' => 'Id. Tabela'),
		array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
		array('colunaValor' => 'D069_Flag_Tabela_Principal', 'colunaTitulo' => 'Usar?','colunaAlinhar' => 'center', 'callback' => 'checkboxD069FlagTabelaPrincipal'),
		array('colunaValor' => 'D069_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Cod. Prod.', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'if(D001_Descricao_Produto is not null,D001_Descricao_Produto,D069_Descricao_Produto)', 'colunaTitulo' => 'Produto'),
		array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
		array('colunaValor' => 'D069_Percentual_Desconto', 'colunaTitulo' => 'Desc %', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaEditarTodos' => true),
		array('colunaValor' => 'concat(D006_Codigo_CFOP,\'(\',D006_Id,\')\')', 'colunaTitulo' => 'CFOP', 'style' => 'background: var(--celula-vermelho);', 'callback' => 'campoInfo', 'callbackParameter' => array('D069_D006_Id_Info', 'Demonstrativo CFOP', 'D069_Id', 'left', '', '500')),
		array('colunaValor' => "IF(D005_Classificacao_Fiscal != '',concat(D005_Classificacao_Fiscal,'-',D005_Flag_Aplicacao),CONCAT('<font color=\"red\">',D069_NCM,'</font>'))", 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);;font-size:12px', 'callback' => 'campoInfo', 'callbackParameter' => array('D069_D005_Id_Info', 'Demonstrativo NCM', 'D069_Id', 'left', '', '500')),
		array('colunaValor' => 'D069_Flag_Origem_Produto', 'colunaTitulo' => 'Orig', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-vermelho);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_Quantidade_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto', 'colunaEditarTodos' => true), // Adicionar propriedades de edição aqui
		array('colunaValor' => 'D069_Valor_Preco_Original', 'colunaTitulo' => 'Preço', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_Valor_Preco_Unitario', 'colunaTitulo' => 'Liquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => '', 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_D069_Percentual_Desconto'),
		array('colunaValor' => 'D069_Aliquota_IPI', 'colunaTitulo' => '%IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'D069_Aliquota_ICMS', 'colunaTitulo' => '%ICM', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'D069_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D069_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D069_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D069_Percentual_Reducao_ICMS', 'colunaTitulo' => '%Red', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'D069_Percentual_ST', 'colunaTitulo' => '%ST', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'D069_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-verde);'),
		array('colunaValor' => 'D069_Data_Atualizacao_Estoque', 'colunaTitulo' => 'Data estoque', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D069_Data_Atualizacao_Preco', 'colunaTitulo' => 'Data Preco', 'style' => 'width:1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'D069_Observacao', 'colunaTitulo' => 'Observação', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo-claro);', 'colunaExibir' => false),
		array('colunaValor' => 'D069_Percentual_Frete', 'colunaTitulo' => '% Frete', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);', 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_Percentual_Financeiro', 'colunaTitulo' => '% Finan', 'colunaCasasDecimais' => '4', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'D069_Flag_ST', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_ICMS_Inter_Estadual', 'colunaTitulo' => 'ICMS IE', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_ST_ICMS_Interno', 'colunaTitulo' => 'ICMS Int', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_ST_VA', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-azul);', 'colunaEditar' => true, 'colunaEditarTodos' => true),
		array('colunaValor' => 'D069_Peso_Unitario', 'colunaTitulo' => 'Peso unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
		array('colunaValor' => 'D069_Observacao_Compra', 'colunaTitulo' => 'Observação', 'style' => 'background: var(--celula-amarelo);', 'colunaEditar' => true),
	);

	/**
	 * Definição da SQL
	 */
	$from = "D069";
	
	$extra = <<<EOT
		-- JOIN
			left join D009 on D069_D009_Id = D009_Id
			left join D049 on D009_D049_Id = D049_Id
			left join D001 on D049_D001_Id = D001_Id
			left join D069A on D069A_D069_Id = D069_Id
			left join D070 on D069_D070_Id = D070_Id
			left join D024 on D070_D024_Id = D024_Id
			left join C004 on D070_C004_Id = C004_Id
			left join D082 on D049_D082_Id = D082_Id
			left join D005 on D005_Id=D069_D005_Id
			left join D006 on D006_Id=D069_D006_Id
EOT;

	/**
	 * Botões Almir
	 */
	$botoes = array(
		array('titulo' => 'Atualizar alíquotas e calcular', 'id' => 'cad002AtualizarAliquotasECalcular'),
	);
	echo Botoes::processar($botoes);

	/**
	* Botões (Versão Acima do GRID)
	*/
	
		/*<div class="buttonsBottomTop">
			<button type="button" id="fis005Incluir">Incluir</button>
			
		</div>*/

		
	/**
	* Filtro
	*/

	$gridFiltro = array(
		'D069_Id' => array('titulo' => 'Id', 'tipo' => 'numero'),
		'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
		'D070_Id' => array('titulo' => 'Id. Fornecedor', 'tipo' => 'numero'),
		'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
		'D069_Codigo_Produto' => array('titulo' => 'Cod. Prod.', 'tipo' => 'numero'),
		'D069_Flag_Tabela_Principal' => array('titulo' => 'Usar?', 'tipo' => 'texto'),
		'D001_Descricao_Produto' => array('titulo' => 'Prod.', 'tipo' => 'texto'),
		'D082_Marca' => array('titulo' => 'Marca Produto', 'tipo' => 'texto'),
		'D069_Flag_Origem_Produto' => array('titulo' => 'Origem', 'tipo' => 'texto'),
		'D069_Unidade' => array('titulo' => 'Un', 'tipo' => 'texto'),
		'D069_Quantidade_Estoque' => array('titulo' => 'Qtd. Estoque', 'tipo' => 'numero'),
		'D069_Valor_Preco_Original'=> array('titulo' => 'Preço', 'tipo' => 'numero'),
		'D069_Aliquota_IPI' => array('titulo' => '%IPI', 'tipo' => 'numero'),
		'D069_Aliquota_ICMS' => array('titulo' => '%ICM', 'tipo' => 'numero'),
		'D069_Percentual_Reducao_ICMS' => array('titulo' => '%Red', 'tipo' => 'numero'),
		'D069_Percentual_ST' => array('titulo' => '%ST', 'tipo' => 'numero'),
		'D069_Valor_Custo_Unitario'=> array('titulo' => 'Custo', 'tipo' => 'numero'),
		'D069_Data_Atualizacao_Estoque' => array('titulo' => 'Data estoque', 'tipo' => 'data'),
		'D069_Data_Atualizacao_Preco' => array('titulo' => 'Data preço', 'tipo' => 'data'),
		'D069_Observacao'=> array('titulo' => 'Observacao', 'tipo' => 'texto'),
		'D069_Percentual_Frete'=> array('titulo' => 'Frete', 'tipo' => 'numero'),
		'D069_Percentual_Financeiro'=> array('titulo' => 'Per. Financeiro', 'tipo' => 'numero'),
		'D069_ICMS_Inter_Estadual'=> array('titulo' => '%ICM', 'tipo' => 'numero'),
		'D069_ST_ICMS_Interno'=> array('titulo' => '$ICM Int', 'tipo' => 'numero'),
		'D069_Peso_Unitario'=> array('titulo' => 'Peso', 'tipo' => 'numero'),
	);
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);



	/**
	 * Totais Almir
	 */
	$totais = array(
		// sql
		array('nome' => 'qtd_registros', 'titulo' => 'Registros', 'tipo' => 'sql', 'valor' => '', 'exibir' => 'S', 'totalizador' => 'count', 'callbackParameter' => 0),
		array('nome' => 'qtd_empresas', 'titulo' => 'Empresas', 'tipo' => 'sql', 'valor' => 'C004_Id', 'exibir' => 'S', 'totalizador' => 'distinct', 'callbackParameter' => 0),
		array('nome' => 'fornecedores', 'titulo' => 'Fornecedores', 'tipo' => 'sql', 'valor' => 'D024_Id', 'exibir' => 'S', 'totalizador' => 'distinct', 'callbackParameter' => 0),
		array('nome' => 'tabelas_fornecedor', 'titulo' => 'Tabelas Fornecedor', 'tipo' => 'sql', 'valor' => 'D070_Id', 'exibir' => 'S', 'totalizador' => 'distinct', 'callbackParameter' => 0),
		array('nome' => 'marcas', 'titulo' => 'Marcas', 'tipo' => 'sql', 'valor' => 'D082_Id', 'exibir' => 'S', 'totalizador' => 'distinct', 'callbackParameter' => 0),
		array('nome' => 'prods_c_desc', 'titulo' => 'Prods com Desc', 'tipo' => 'sql', 'valor' => 'IF(D069_Percentual_Desconto > 0, 1, 0)', 'exibir' => 'S', 'totalizador' => 'soma', 'callbackParameter' => 0),
		array('nome' => 'total_custo', 'titulo' => 'Total Custo', 'tipo' => 'sql', 'valor' => 'D069_Valor_Custo_Unitario', 'exibir' => 'S', 'totalizador' => 'soma', 'callbackParameter' => 0),
		array('nome' => 'prods_com_est', 'titulo' => 'Prods. com Est.', 'tipo' => 'sql', 'valor' => 'IF(D069_Quantidade_Estoque > 0, 1, 0)', 'exibir' => 'S', 'totalizador' => 'soma', 'callbackParameter' => 0),
		array('nome' => 'prods_sem_est', 'titulo' => 'Prods. sem Est.', 'tipo' => 'sql', 'valor' => 'IF(D069_Quantidade_Estoque <= 0, 1, 0)', 'exibir' => 'S', 'totalizador' => 'soma', 'callbackParameter' => 0),
		array('nome' => 'prods_preco_zero', 'titulo' => 'Prods. Preço Zero', 'tipo' => 'sql', 'valor' => 'IF(D069_Valor_Preco_Original <= 0, 1, 0)', 'exibir' => 'S', 'totalizador' => 'soma', 'callbackParameter' => 0),
	);
		
	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	
	/**
	* Botões (Versão Acima do GRID)
	*/
	/*$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
	
	echo <<<EOT
		<div class="buttonsBottomTop">
			<span class="item" style="text-align:right">Cidades<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
			<div style="clear:both;"></div>
		</div>
EOT;*/

	$extra_ = base64_encode($extra);
	echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#cad002AtualizarAliquotasECalcular').unbind('click').bind('click', function(data) {
			var \$buttons = {
				"Sim": function() {
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
					$(this).dialog("destroy").remove();
					
					idProgressBar = dialogAlert('Processando',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
					cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,1);
				},
				"Não": function() { $(this).dialog("destroy").remove(); } 
			};
			var dialogOptions = dialogConfirm('Atualizar Alíquotas', 'Atualizar as aliquotas igual ao cadastro de produtos: <br />', \$buttons);
			$('<input type="checkbox" checked="checked">').attr('id', 'campoCFOP').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
			$('<label>').attr('id', 'labelCFOP').html('CFOP').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
			$('<input type="checkbox" checked="checked">').attr('id', 'campoNossoCodigo').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + dialogOptions).find('p'));
			$('<label>').attr('id', 'labelNossoCodigo').html('Nosso Código').appendTo($("#dc-" + dialogOptions).find('p')).append('<br />');
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

		function cad002AtualizarAliquotasLoop(idProgressBar, arrayOpcoes, inicio){
			$.getJSON('/cad/cad002/form_func-ajax/atualizarAliquotas/?ajax=true&opcoes=' + encodeURIComponent(arrayOpcoes) + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
				if (request.concluido == 0) {
					$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					cad002AtualizarAliquotasLoop(idProgressBar,arrayOpcoes,0);
				}else{
					$('#dc-' + idProgressBar).dialog("destroy").remove();
					dialogConfirm('Concluído','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					if (request.code) {
						divRefresh('{$g['divId']}');
					} else {
						dialogConfirm('Erro!', request.data);
					}
				} 
			});	                    
		}
	} </script></div>
EOT;

	/**
	 * Define ação do click de abertura (ao clicar em uma linha do grid)
	 */
	$novoIdTelaForm = uniqid();
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
			});
		} </script></div>
EOT;
	
	/**
	 * Geração: Grid
	 */
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  break;

  case 'cad002GridCiap':

	$grid = array(
		//array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T316_Id', $g['divId'])),
		array('colunaValor' => 'T316_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir'=> true, 'style' => 'width: 1px;'),		
		array('colunaValor' => 'T013_Id', 'colunaTitulo' => 'T013_Id', 'colunaExibir'=> false, 'style' => 'width: 1px;'),
		array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir'=> false, 'style' => 'width: 1px;'),
		array('colunaValor' => 'LPAD(T013_Numero_Nota_Fiscal,7,"0")', 'colunaTitulo' => 'NF Entrada', 'colunaExibir'=> true, 'style' => 'width: 1px;', 'colunaAlinhar' => 'center',  'callback' => 'abrirJanela', 'callbackParameter' => array('NF Entrada', '/est/est004/content/est004ContentEditarNotaFiscal/', 'T013_Id', '1000,600'),),	
		array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor'),
		array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'callback' => 'abrirJanela', 'callbackParameter' => array('Cadastro Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,600'), 'colunaAlinhar' => 'center', 'style' => 'background: var(--celula-amarelo); width: 1px;', 'colunaExibir' => false),				
		array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'background: var(--celula-amarelo);'),
		array('colunaValor' => 'T316_Codigo_Produto_CIAP', 'colunaTitulo' => 'Código Escrituração', 'style' => 'background: var(--celula-verde);width: 1px;', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'T316_Descricao_Utilizacao_Bem', 'colunaTitulo' => 'Descrição Utilização Bem', 'style' => 'background: var(--celula-verde);'),
		array('colunaValor' => 'T014_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaEditarOculto' => true, 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background:var(--celula-vermelho); width: 100px;'),
		array('colunaValor' => 'T316_Data_Inicio_Apropriacao_Credito', 'colunaTitulo' => 'Início Apropriação', 'callback' => 'gCorrigeData', 'style' => 'width: 1px;', 'colunaAlinhar' => 'center'),						
		array('colunaValor' => 'T316_Numero_Parcelas_Apropriacao', 'colunaTitulo' => 'Nº Parcelas', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),	
		array('colunaValor' => 'validaParcelaDataAtualCiap', 'colunaTipo' => 'livre' ,'colunaTitulo' => 'Nº Parcela Aproprição<br>Sobre Data Atual','callback' => 'validaParcelaDataAtualCiap', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),								
		array('colunaValor' => 'T316_Flag_Ativo_Baixado', 'colunaTitulo' => 'Baixado CIAP', 'style' => ' width: 1px;', 'colunaAlinhar' => 'center'),						
		array('colunaValor' => 'T316_Data_Fim_Apropriacao_Credito', 'colunaTitulo' => 'Fim Apropriação', 'colunaExibir' => true, 'callback' => 'gCorrigeData','style' => 'width: 1px;', 'colunaAlinhar' => 'center'),								
	);

	/**
	 * Definição da SQL
	 */
	$from = "T316";
	$extra = <<<EOT
	LEFT JOIN T014 ON T014_Id = T316_T014_Id
	LEFT JOIN T013 ON T013_Id = T014_T013_Id
	LEFT JOIN D009 ON D009_Id = T014_D009_Id 
	LEFT JOIN D049 ON D049_Id = D009_D049_Id 
	LEFT JOIN D001 ON D001_Id = D049_D001_Id 
	LEFT JOIN D024 ON D024_Id = T013_D024_Id
	WHERE D001_Id = '{$r_acaoId}'
	  AND T013_C004_Id = '{$g['empresaAtual']}'
EOT;
	$botoes = array(
	);
	echo Botoes::processar($botoes);

	/**
	 * Filtro
	 */
	$gridFiltro = array(
		'T013_Numero_Nota_Fiscal' => array('titulo' => 'NF Entrada', 'tipo' => 'text'),			
		'D024_Nome_Empresa' => array('titulo' => 'Fornecedor', 'tipo' => 'text'),	
		'T316_Data_Inicio_Apropriacao_Credito' => array('titulo' => 'Início Apropriação', 'tipo' => 'data'),			
		'T316_Flag_Ativo_Baixado' => array('titulo' => 'Baixado (S/N)', 'tipo' => 'text'),	
		'T316_Data_Fim_Apropriacao_Credito' => array('titulo' => 'Fim Apropriação', 'tipo' => 'data'),	
	);
	// Gera o filtro e acrescenta ao $extra
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

	// Imprime o filtro do GRID (normalmente executado acima do GRID)
	echo $g['ui']->gridFiltroPrint($gridFiltro);

	/**
	 * Define ação do click de abertura (ao clicar em uma linha do grid)
	 */
  	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var dbclickPrevent = false;
			$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
				// Previne duplo click (abrir duas janelas)
				if (dbclickPrevent) { return false; }
				dbclickPrevent = true;
				setTimeout(function() { dbclickPrevent = false; }, 500);
				// (substituir pelo mesmo ID da chave primária do grid)
				var chavePrimaria = 'T316_Id';	
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var acaoId = id[0].replace(chavePrimaria + '-', '');
				var T013_Id = id[1].replace(/T013_Id-/, '');					
				// Ação: abrir nova janela
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Obrigação Tributária - ID: ' + acaoId, '/est/est004/content/est004ContentCadastroCiap/', '&T316_Id=' + encodeURIComponent(acaoId) + '&T013_Id=' + encodeURIComponent(T013_Id), [600,250]);
			});
			//$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
		} </script></div>
EOT;	 


	/**
	 * Totais Almir
	 */
	$totais = array(
		// sql
		array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
	);

	/**
	 * Geração: Monta o SQL e retorna o dados
	 */
	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  break;	
  
  
 case 'cad002GridAtivosImobilizados':
	$filtroObrigatorio = ($g['C031']['obrigatorioFiltroTelaTransicao'] == 'S') ? true : false;
	
	//configuração global de separar produto por empresa
	$filtro = '';
	if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
		$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
	}

	$grid = array(
		array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'colunaExibir' => false, ),
		array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
		array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
		array('colunaValor' => 'verificaCiap', 'colunaTitulo' => 'Tem lançamento?', 'colunaTipo' => 'livre', 'callback' => 'verificaCiap', 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
		array('colunaValor' => 'substr(D082_Marca,1,15) as D082_Marca', 'colunaTitulo' => 'Marca', 'colunaExibir' => true),
		array('colunaValor' => 'D049_Origem_Mercadoria', 'colunaTitulo' => 'Origem', 'colunaEditarTodos' => true, 'colunaEditarOculto' => true, 'colunaAlinhar' => 'center', 'colunaEditarNivel' => 2, 'style' => 'width:1%'),
		array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => true),
		array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
		array('colunaValor' => 'D009_Data_Ultima_Entrada', 'colunaTitulo' => 'Ult.Entrada', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'Ult.Saída', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'gCorrigeData'),
		array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009 Id', 'colunaExibir' => false),		
		array('colunaValor' => 'D001_C008_Id', 'colunaTitulo' => 'Depto','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('C008', 'C008_Id', 'C008_Id, C008_Tipo', "ORDER BY C008_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraDepartamento', 'callbackParameter'=>'C008_Tipo', 'colunaEditarTodos'=>true),
		array('colunaValor' => 'D001_D060_Id', 'colunaTitulo' => 'Setor','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D060', 'D060_Id', 'D060_Id, D060_Descricao_Grupo', "ORDER BY D060_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraSetor', 'callbackParameter'=>'D060_Descricao_Grupo', 'colunaEditarTodos'=>true),
		array('colunaValor' => 'D001_D003_Id', 'colunaTitulo' => 'Grupo','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D003', 'D003_Id', 'D003_Id, D003_Nome_Linha', "ORDER BY D003_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraGrupo', 'callbackParameter'=>'D003_Nome_Linha', 'colunaEditarTodos'=>true),
		array('colunaValor' => 'D001_D002_Id', 'colunaTitulo' => 'Sub-Grupo','colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarNivel' => 2, 'colunaEditarGeraSelect' => array('D002', 'D002_Id', 'D002_Id, D002_Descricao_Produto', "ORDER BY D002_Id", true), 'colunaExibir' => false, 'callback'=>'campoMostraSubGrupo', 'callbackParameter'=>'D002_Descricao_Produto', 'colunaEditarTodos'=>true),
		array('colunaValor' => 'D001_Flag_Tipo_Item', 'colunaTitulo' => 'Categoria', 'colunaExibir' => true, 'colunaAlinhar' => 'center'),  
		array('colunaValor' => 'D009A_Id', 'colunaTitulo' => 'D009A_Id', 'colunaExibir' => false),
		array('colunaValor' => 'D001A_Id', 'colunaTitulo' => 'D001A_Id', 'colunaExibir' => false),

	);	
		
	$from = 'D001';
	$extra = <<<EOT
	-- JOIN
		left join D003 on D003_Id=D001_D003_Id
		left join D049 on D049_D001_Id=D001_Id
		left join D009 on D009_D049_Id=D049_Id
		left join C008 on D001_C008_Id=C008_Id
		left join D082 on D049_D082_Id=D082_Id
		left join D060 on D001_D060_Id=D060_Id
		left join D002 on D001_D002_Id=D002_Id
		left join D037 on D001_D037_Id=D037_Id
		left join D005 on D001_D005_Id=D005_Id
		left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
		left join D001A on D001A_D001_Id=D001_Id
		left join D009A on D009A_D009_Id=D009_Id
	-- WHERE
		where D001_Flag_Tipo_Item = '08'
		AND (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
		AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
		{$filtro}
	-- GROUP BY
		GROUP BY D009_Id
EOT;

	/**
	* Função que define classes CSS para cada linha do grid
	*/
	function gridLinha($array) {
		if ($array['todosCampos']['D001_Flag_Ativo']=='N') {
		   return 'linhaCinza';
		}
		if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
		   return 'linhaVermelho';
		}
		if ($array['todosCampos']['D049_Flag_Nao_Comprar']=='S') {
			return 'linhaError';
		 }
	}	

	/**
	* Filtro
	*/
	$gridFiltro = array(
		'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
		'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
		'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto')
	);

	 // Gera o filtro e acrescenta ao $extra
	
	/**
	* Geração: Monta o SQL e retorna os dados
	*/
	$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
	echo $g['ui']->gridFiltroPrint($gridFiltro);
	
	/**
	 * Totais
	 */
	$totais = array(
		// sql
		array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
	);

	/**
	* Define ação do click de abertura (ao clicar em uma linha do grid)
	*/
	$novoIdTelaForm = uniqid();
	echo  <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Lançamento CIAP Código : ' + encodeURIComponent(array['todosCampos']['D001_Codigo_Produto'])  , '/cad/cad002/grid/cad002GridCiap/', '&acaoId=' + encodeURIComponent(array['todosCampos']['D001_Id']) + '&tabela=D001_Id', [1100,500]);
			});
		} </script></div>
EOT;

	list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
	/**
	* Geração: Grid
	*/
	$gridTabela = $g['ui']->grid($grid, $geradoDados);
	$g['smarty']->assign('thead', $gridTabela['thead']);
	$g['smarty']->assign('tbody', $gridTabela['tbody']);
	$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
	$g['smarty']->assign('gridId', md5($g['r_']));
	$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
	gBotaoAuditoria($from);
	if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  break;

}
