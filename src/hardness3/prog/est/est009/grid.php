<?php
namespace hardness;

// Carrega funções callback
require_once('prog/est/est009/grid_func-callback.php');
require_once('prog/est/est009/grid_func-js.php');
require_once('prog/cad/cad002/grid_func-callback.php');
require_once('prog/cad/cad002/grid_func-js.php');

switch ($g['r'][3]) {               
	case 'est009GridComprasEstoque':

        //Incluido o IF no filtro obrigatório para a nova funçao de clicar sobre a linha do GRID e trazer o resultado do D001 de todas as C004 (07/01/2021)
        if (!$r_extraLinha) {
            $filtroObrigatorio = true;
        }
		
		$meses = 12; 
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
				$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
				$filtro = '';
		}

        //Incluido o IF para a nova funçao de clicar sobre a linha do GRID para trazer o nome da empresa apenas quando for o resultado de todos as C004 (07/01/2021)
        if (!$r_extraLinha) {
			$exibirEmpresa = false; 
			$exibirProduto = true;
			$gridFoto = array(
				array('colunaValor' => 'Foto', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Imagem', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false),
			);
		} else { 
			$exibirEmpresa = true; 
			$exibirProduto = false;
			$gridFoto = array(
				array('colunaValor' => 'FotoNormal', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Imagem', 'colunaAgrupar' => '1', 'callback' => 'mostrarImagemProdutoNormal', 'colunaExibir' => $exibirEmpresa),
			);
		}

		$editarEstoqueMinimoMaximo = ($g['C031']['calculaEstoqueMinimoMaximoProduto'] == 'N') ? true : false;

		$grid = array(
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'extra' => 'style="width:1%;"'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'C', 'style' => 'width:1%;', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '720')),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'colunaExibir' => $exibirProduto ),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'colunaExibir' => $exibirProduto),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca', 'colunaExibir' => $exibirProduto),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => $exibirEmpresa, 'extra' => 'width:1%;'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Comprar_Calculada', 'colunaTitulo' => 'Sugestão', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Quantidade_Compra_Emergencia', 'colunaTitulo' => 'Emer<br>gência', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-vermelho);'),
            //array('colunaValor' => 'D009_Compra_Emergencia(D009_Quantidade_Minima_Estoque,D009_Quantidade_Estoque_Liquido,D009_Quantidade_OC)', 'colunaTitulo' => 'Emer<br>gência', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-vermelho);'),
            array('colunaValor' => 'D009_Quantidade_Comprar', 'colunaTitulo' => 'Qtde<br>Comprar', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarTodos' => true, 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-amarelo);font-size:14px'),
            array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => '', 'callback' => 'validaCampoCotar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'D009_C007_Id_Comprar'),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Compra<br>mínima', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'border-right:1px dashed silver'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq<BR>Físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq<BR>Loja', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq<br/>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),            
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Estq<BR>Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qtde<BR>OC', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela', 'colunaTitulo' => 'Estq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            // array('colunaValor' => "(SELECT SUM(T004_Quantidade) 
            //                                FROM T004 
            //                           LEFT JOIN T003 ON T003_Id=T004_T003_Id 
            //                           LEFT JOIN D047 ON D047_Id = T003_D047_Id
            //                               WHERE T004_D009_Id=D009_Id
            //                                 AND T003_Flag_Status_Orcamento='C' 
            //                                 AND D047_Flag_Compra = 'S'
            //                                 AND T003_C004_Id=D009_C004_Id) AS vendaPerdida", 
            //                         'colunaTitulo' => 'Venda<br>Perdida', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamentos Cancelados', '/crm/crm001/content/crm001contentOrcamentoPerdido/', 'D009_Id', '1000,1000', true), 'style' => 'border-left:1px dashed silver'),
            array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => 'Estq<br>mínimo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => $editarEstoqueMinimoMaximo),
            array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => 'Estq<br>máximo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'colunaEditar' => $editarEstoqueMinimoMaximo, 'colunaExibir' => $editarEstoqueMinimoMaximo),
			array('colunaValor' => 'D009_Quantidade_Consumo', 'colunaTitulo' => 'Vendido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback' => 'est009campoConsumido', 'style' => 'border-left:1px dashed silver'),
			array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            array('colunaValor' => 'D009_Clientes_Consumo', 'colunaTitulo' => 'Client', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            array('colunaValor' => 'D009_Frequencia_Venda_Meses', 'colunaTitulo' => 'Freq<br>Mes', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            // array('colunaValor' => '(GROUP_CONCAT(DISTINCT CONCAT(T241_Flag_Principal,"|",T241_Flag_Homologado,"|",fornecedor.D024_Flag_Contribuinte,"|",fornecedor.D024_Nome_Empresa) SEPARATOR "<br>")) as colunaFornecedores', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;border-left:1px dashed silver', 'callback' => 'colunaFornecedores'),
            array('colunaValor' => 'colunaFornecedores', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;border-left:1px dashed silver', 'callback' => 'colunaFornecedores'),
            //array('colunaValor' => '(select group_concat(distinct substr(D024_Nome_Empresa,1,25) separator "<br>") from T001 left join D024 on D024_Id=T001_D024_Id where T001_D009_Id=D009_Id and T001_Flag_Atualiza_Custo="S" and T001_Flag_Operacao="E" and datediff(curdate(),T001_Data_Lancamento)<=800 limit 1) as ultimosFornecedores', 'colunaTitulo' => 'Últimos fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:10px;width:1%'),
            array('colunaValor' => 'T001_Quantidade', 'colunaTitulo' => 'Qte<br>Ult.NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'border-left:1px dashed silver'),
            array('colunaValor' => 'T001_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço NF', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'datediff(curdate(),T001_Data_Lancamento)', 'colunaTitulo' => 'Dias', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data NF', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'ultimaCompra.D024_Nome_Fantasia', 'colunaTitulo' => 'NF Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'border-right:1px dashed silver'),
            array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback' => 'validaCampoComprar', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Meses_MMF', 'colunaTitulo' => 'Meses MMF', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Meses_Compra', 'colunaTitulo' => 'Meses Compra', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Meses_Reposicao', 'colunaTitulo' => 'Meses Reposição', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'border-right:1px dashed silver'),
			// array('colunaValor' => 'mediaVendasMMF', 'colunaTitulo' => 'Média Vendas MMF', 'colunaTipo' => 'livre', 'callback' => 'mediaMMF', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_C007_Id_Comprar', 'colunaTitulo' => 'D009_C007_Id_Comprar', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'Ult.Venda', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'datediff(curdate(),D009_Data_Ultima_Venda)', 'colunaTitulo' => 'Dias', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => 'vendasMes', 'colunaTitulo' => 'Vendas por mês', 'colunaTipo' => 'livre', 'callback' => 'vendasMes', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct D004_Local)', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Especificacoes', 'colunaTitulo' => 'Especificações', 'colunaExibir' => true, 'style' => 'max-width:250px;min-width:250px;white-space:normal' ),
			array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré-cadastro?', 'colunaExibir' => false, 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'D049_Flag_Nao_Comprar', 'colunaTitulo' => 'Não Comprar', 'callback' => 'validaCampoNaoComprar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'vendaPerdida', 'colunaTitulo' => 'Venda Perdida', 'colunaTipo' => 'livre', 'callback' => 'est009VendaPerdida', 'style' => 'border-right:1px dashed silver', 'colunaAlinhar' => 'center','colunaExibir' => false),
            array('colunaValor' => 'concat(D001_Codigo_Produto, \' - \', D001_Descricao_Produto, \' - \', D082_Marca) as tituloProduto', 'colunaTitulo' => 'tituloProduto', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D001_Codigo_Produto, D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'Produto', 'style' => 'width:5%;', 'colunaExibir' => false),
		);

		// Devido aos 2 modelos de foto para o grid normal e gridLinha
		$grid = array_merge($gridFoto,$grid);

		/**
		* Definição da SQL
		*/
		$from = 'D009';
		
		$extra = <<<EOT
			LEFT JOIN T187 on T187_D009_Id=D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D083 on D083_D049_Id=D049_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join T001 on T001_Id=D009_T001_Id
			left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
			left join C007 on (C007_Id=D009_C007_Id_Comprar and D009_Flag_Cotar='S') or C007_Id=D009_C007_Id_Comprador
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
			LEFT JOIN C004 ON D009_C004_Id
			LEFT JOIN D009A ON D009A_D009_Id = D009_Id
			where D009_C004_Id='{$g['empresaAtual']}'
			  AND D001_Flag_Ativo='S'
			  AND D009A_Flag_Pertence_Empresa != 'N'
			     {$filtro}
			group by D009_Id
EOT;


		function gridLinha($array) {
		   global $g;
		   if ($array['todosCampos']['round(D009_Quantidade_Comprar_Calculada,0)']<0) {
		      //return 'linhaCinza';
		   }
		   if ($array['todosCampos']['D049_Flag_Nao_Comprar']=='S') {
			   return 'linhaError';
			}
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
				return 'linhaVermelho';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001_Aplicacao,D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Referência', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad023/grid/cad023GridTipos/', 'campo' => 'C008_Tipo', 'tamanho' => 20)),
			'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad027/grid/cad027GridGrupos/', 'campo' => 'D060_Descricao_Grupo', 'tamanho' => 20)),
			'D003_Nome_Linha,D003_Id' => array('titulo' => 'Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad026/grid/cad026GridLinhas/', 'campo' => 'D003_Nome_Linha', 'tamanho' => 20)),
			'D002_Descricao_Produto,D002_Id' => array('titulo' => 'Sub-Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad028/grid/cad028GridSubGrupos/', 'campo' => 'D002_Descricao_Produto', 'tamanho' => 20)),
			'D009_Quantidade_Estoque' => array('titulo' => 'Estoq.Físico', 'tipo' => 'numero'),
			'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoq.Líquido', 'tipo' => 'numero'),
			'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estoq.Loja', 'tipo' => 'numero'),
			'D009_Quantidade_OC' => array('titulo' => 'Qtd. OC', 'tipo' => 'numero'),
			'D009_Quantidade_Comprar_Calculada' => array('titulo' => 'Sugestão Compra', 'tipo' => 'numero'),
			'D009_Quantidade_Compra_Emergencia'=> array('titulo' => 'Qtde Emgergência', 'tipo' => 'numero'),
			'D009_Compra_Emergencia(D009_Quantidade_Minima_Estoque[[virgula]]D009_Quantidade_Estoque_Liquido[[virgula]]D009_Quantidade_OC)' => array('titulo' => 'Qtde Emgergência', 'tipo' => 'numero', 'exibir' => false),
			'D009_Frequencia_Venda' => array('titulo' => 'Frequencia', 'tipo' => 'numero'),
			'D009_Clientes_Consumo' => array('titulo' => 'Clientes', 'tipo' => 'numero'),
			'D009_Quantidade_Consumo' => array('titulo' => 'Vendido', 'tipo' => 'numero', 'exibir' => false),
			'D009_Flag_Cotar' => array('titulo' => 'Marcados', 'tipo' => 'texto'),
			'D009_Flag_Comprar' => array('titulo' => 'Flag Comprar', 'tipo' => 'texto', 'exibir' => false),
			'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
			'T001_Data_Lancamento' => array('titulo' => 'Data NF Forn', 'tipo' => 'data'),
			'D009_Data_Ultima_Venda' => array('titulo' => 'Ultima venda', 'tipo' => 'data'),
			'datediff(curdate(),D009_Data_Ultima_Venda)' => array('titulo' => 'Dias ult.venda', 'tipo' => 'numero'),
			'D049_Quantidade_Embalagem_Compra' => array('titulo' => 'Embalagem', 'tipo' => 'texto'),
			'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
			'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
			'fornecedor.D024_Nome_Empresa,fornecedor.D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
			'fornecedor.D024_Flag_Contribuinte' => array('titulo' => 'Tipo Fornecedor', 'tipo' => 'select', 'select' =>
				array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '0 - Não contribuinte', 'value' => '0' ),   
					array('title' => '1 - Consumidor Final', 'value' => '1'),   
					array('title' => '2 - Revenda', 'value' => '2'),   
					array('title' => '3 - Produtor rural', 'value' => '3'),
					array('title' => '4 - Simples', 'value' => '4'),   
					array('title' => '5 - Industria', 'value' => '5')   
				)
			),
			'D049_Flag_Nao_Comprar' => array('titulo' => 'Comprar?', 'tipo' => 'select', 'select' =>
			array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),  
				array('title' => 'Sim', 'value' => 'N',), 
				array('title' => 'Não', 'value' => 'S',), 
				)
			),
			'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' =>
			array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),  
				array('title' => 'Sim', 'value' => 'S',), 
				array('title' => 'Não', 'value' => 'N',), 
				)
			),
			'D009_Quantidade_Estoque_Tabela' => array('titulo' => 'Estq Tabela', 'tipo' => 'numero'),
			'D009_Quantidade_Estoque_Fora' => array ('titulo' => 'Estq Fora', 'tipo' => 'numero'),
		);

        //Incluido o IF na clausula where para a nova funçao de clicar sobre a linha do GRID e trazer o resultado do D001 de todas as empresas (07/01/2021)
        if ($r_extraLinha>0) {

			$gridFiltro['Agrupar'] = array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				                 array('title' => 'Produto', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'tituloProduto', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
				            ));

			$extraLinha = ' where D001_Id = "' . $r_extraLinha .'"  AND C004_Flag_Ativo!="N"'
				. " AND NOT EXISTS (SELECT 1 FROM D009A D009A_Visibilidade WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N')"
				. ' group by D009_Id';
		}

		/**
         * Totais
         */
        $totais = array(
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0)
		);

		// Gera o filtro e acrescenta ao $extra
		$extra      = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra     , false, false, false, false, false, $extraLinha);

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100, false, false, NULL, 120000);

		/**
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
        if (!$r_extraLinha) {
			$sql_ = urlencode(base64_encode($geradoSql));
			/*
			if (!personalizacaoBotoes()) { 
				echo <<<EOT
					<div class="buttonsBottomTop">
						<!--<button type="button" id="est014FormCadastroRequisicoesCompra">Incluir</button>-->
						<!--<button type="button" id="est009FlagCotar">Marcar todos</button>-->
						<!--<button type="button" id="est009FlagDesmarcarCotar">Desmarcar todos</button>-->
						<!--<button type="button" id="est009FlagMarcarComprar">Marcar Comprar</button>-->
						<!--<button type="button" id="est009FlagDesmarcarComprar">Desmarcar Comprar</button>-->
						<!--<button type="button" id="est009GerarOC">Gerar OC</button>-->
						<!--<button type="button" id="est009GerarOCNovo">Gerar OC</button>-->
						<!--<button type="button" id="est009GerarCotacao">Gerar Cotação</button>-->
						<button type="button" id="est009GerarCotacaoNovo">Gerar Cotação Manual</button>
						<button type="button" id="est009GerarCotacaoAutomatica">Gerar Cotações Automáticas</button>
						<button type="button" id="est009GerarOTEstoque">Gerar Transferência</button>
						<button type="button" id="est009GerarMontagemEstoque">Gerar Montagem</button>
						<button type="button" id="est014AtualizarCalculoEstoque">Atualizar estoque</button>
					</div>
EOT;
			}*/

			
			// Botões
			$botoes = array(
				array('titulo' => 'Gerar Cotação Manual', 'id' => 'est009GerarCotacaoNovo'),
				array('titulo' => 'Gerar Cotações Automáticas', 'id' => 'est009GerarCotacaoAutomatica'),
				array('titulo' => 'Gerar Transferência', 'id' => 'est009GerarOTEstoque'),
				array('titulo' => 'Gerar Montagem', 'id' => 'est009GerarMontagemEstoque'),
				array('titulo' => 'Atualizar estoque', 'id' => 'est014AtualizarCalculoEstoque'),
			);
			echo Botoes::processar($botoes);

            // Não executar quando houver o refresh de linha
            if (empty($r_linhaGridId)) {
                echo <<<EOT

				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#est014FormCadastroRequisicoesCompra').unbind('click').bind('click', function(data) {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Requisições de compra', '/cad/cad046/content/cad046ContentMotivosCancelamentoIncluir/', '&tab=geral', [400,300]);
					});
					$('#est009GerarOTEstoque').unbind('click').bind('click', function(data) { // PASSA SQL
						$('#{$g['divId']}').showLoading();
						var dadosFiltro = 'ajax=true&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&callback=?';
						$.ajax({
							url: '/est/est009/grid_func-ajax/est009GerarOTEstoque/',
							type: 'POST',
							dataType: 'jsonp',
							data: dadosFiltro,
						}).done(function(request, temp1, temp2){
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Transferência', '/est/est016/content/GerarOT/', '&tab=geral&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&count=' + encodeURIComponent(request.data) + '&origem=estoque', [550,200]);
								return false;
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
						});
					});
					$('#est014AtualizarCalculoEstoque').unbind('click').bind('click', function(data) { // PASSA SQL SQL
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();

								var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?';
								$.ajax({
									url: '/est/est009/grid_func-ajax/processaCalcularEstoque/',
									type: 'POST',
									dataType: 'jsonp',
									data: dadosFiltro,
								}).done(function(request, temp1, temp2){
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Operação Ok', request.data, \$buttons);
										divRefresh('{$g['divId']}', true);
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
						dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
					});
					$('#est009GerarOC').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est009/grid_func-ajax/est009GerarOC/?ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = {
												"Sim": function() {
													$('#{$g['divId']}').showLoading();
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar OC', '/est/est006/content/est006ContentIncluirOrdemCompra/', '&tab=geral&GerarOCEstoque=true', [500,300]);
													$('#{$g['divId']}').hideLoading();
													$(this).dialog("destroy").remove();
												},
												"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Confirmação', request.data, \$buttons);
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
						dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra? ', \$buttons);
					});
					$('#est009GerarOCNovo').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est009/grid_func-ajax/est009GerarOC/?ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = {
												"Sim": function() {
													$('#{$g['divId']}').showLoading();
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Pedido', '/est/est016/content/est016contentIncluirPedido/', '&GerarOCEstoque=true', [1000,615]);
													$('#{$g['divId']}').hideLoading();
													$(this).dialog("destroy").remove();
												},
												"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Confirmação', request.data, \$buttons);
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
						dialogConfirm('Gerar Ordem de Compra', 'Gostaria de gerar Ordem de Compra? ', \$buttons);
					});
					
					$('#est009GerarCotacao').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacao/?ajax=true&callback=?', function(request) {
									if (request.code) {
										var \$buttons = {
												"Sim": function() {
													$('#{$g['divId']}').showLoading();
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est008/content/est008contentCadastroCotacaoCompraNew/', '&tab=geral&GerarCotacaoEstoque=true', [500,300]);
													$('#{$g['divId']}').hideLoading();
													$(this).dialog("destroy").remove();
												},
												"Não": function() { $(this).dialog("destroy").remove(); } 
											};
											dialogConfirm('Confirmação', request.data, \$buttons);
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
						dialogConfirm('Gerar Cotação', 'Gostaria de gerar Cotação? ', \$buttons);
					});

					$('#est009GerarCotacaoNovo').unbind('click').bind('click', function(data) { // PASSA SQL

						var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?';
						$.ajax({
							url: '/est/est009/grid_func-ajax/est009GerarCotacao/',
							type: 'POST',
							dataType: 'jsonp',
							data: dadosFiltro,
						}).done(function(request, temp1, temp2){
							if (request.code) {
								var \$buttons = {
									"Adicionar": function() {
										var numeroCotacao = $(this).find('input').val();
										if(!(numeroCotacao == "")){
											idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div>");


											var dadosFiltro = 'ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$sql_}") + '&tipo=comprasEstoque&callback=?';
											$.ajax({
												url: '/est/est009/grid_func-ajax/adicionarProdutosCotacao/',
												type: 'POST',
												dataType: 'jsonp',
												data: dadosFiltro,
											}).done(function(request, temp1, temp2){
												if (request.code) {
													$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
													$.getJSON('/est/est015/form_func-ajax/atualizarCFOP/?ajax=true&T218_Id=' + numeroCotacao + '&callback=?', function(request) {
														if (request.code) {
															$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% Processando CFOP &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
															est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,1);

															function est015AtualizarAliquotasCotacaoLoop(idProgressBar, numeroCotacao, inicio){
																$.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&C004_Id= '+ encodeURIComponent("{$g['empresaAtual']}") + '&callback=?', function(request) {
																	if (request.concluido == 0) {
																		$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
																		est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,0);
																	}else{
																		if (request.code) {
																			$.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
																				if(request.code){
																					divRefresh('{$g['divId']}');
																					$('#dc-' + idProgressBar).dialog("destroy").remove();
																					dialogConfirm('CFOP e alíquotas processadas','<div style=\'border:none;width:auto;height:200px;overflow:auto\'><br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font></div>' );
																					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);   
																				}else{
																					divRefresh('{$g['divId']}');
																					$('#dc-' + idProgressBar).dialog("destroy").remove();
																					var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																					dialogConfirm('Erro ao totalizar itens', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao totalizar itens<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data +'</div>', \$buttons);
																					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]); 
																				}
																			});     
																		}else{
																			divRefresh('{$g['divId']}');
																			$('#dc-' + idProgressBar).dialog("destroy").remove();
																			dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>', \$buttons);
																			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);     
																		}           
																	} 
																});                         
															}
														}else{
															divRefresh('{$g['divId']}');
															$('#dc-' + idProgressBar).dialog("destroy").remove();
															dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'</div>', \$buttons);
															abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);  
														}
													});
												} else {
													$('#dc-' + idProgressBar).dialog("destroy").remove();
													dialogConfirm('Erro ao adicionar produtos', request.data);  
												}
											});
										}else{
											dialogConfirm('Erro', 'Informe um número de cotação', \$buttons);
										}
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
									},
									"Nova Cotação": function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est015/content/est015ContentOrcamento/', '&tab=geral&GerarCotacaoEstoque=true&extra=' + encodeURIComponent("{$sql_}"), [650,500]);
										$(this).dialog("destroy").remove();
									},
									"Cancelar": function() { $(this).dialog("destroy").remove(); } 
								};
								var novoId = dialogConfirm('Gerar Cotação', request.data + '<br /><br />Digite o número de uma cotação para adicionar os produtos selecionados.<br /><br />Cotação: ', \$buttons);
								$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
								return false;
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
						});
					});
					
					$('#est009GerarCotacaoAutomatica').unbind('click').bind('click', function(data) { // PASSA SQL
						$('#{$g['divId']}').showLoading();

						var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?';
						$.ajax({
							url: '/est/est009/grid_func-ajax/est009VerificarCotacaoEstoqueAutomatica/',
							type: 'POST',
							dataType: 'jsonp',
							data: dadosFiltro,
						}).done(function(request, temp1, temp2){
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								var \$buttons = {
									"Gerar Cotações": function() {
										idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div>");

										var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?';
										$.ajax({
											url: '/est/est009/grid_func-ajax/est009GerarCotacaoAutomatica/',
											type: 'POST',
											dataType: 'jsonp',
											data: dadosFiltro,
										}).done(function(request, temp1, temp2){
											if (request.code) {
												var grupo = request.data;
												$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
												est009atualizarCFOPLoop(idProgressBar, grupo, 1);

												function est009atualizarCFOPLoop(idProgressBar, grupo, inicio){
													$.getJSON('/est/est009/grid_func-ajax/est009atualizarCFOP/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
														if (request.concluido == 0) {
															$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando CFOP</b>' + '<br/>Ordens de compra atualizadas: <b>' + request.ordemDeCompra );
															est009atualizarCFOPLoop(idProgressBar,grupo,0);
														}else{
															if (request.code) {
																grupo = request.grupo;
																$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% CFOP processada &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
																	est009AtualizarAliquotasLoop(idProgressBar,grupo,1);

																function est009AtualizarAliquotasLoop(idProgressBar, grupo, inicio){
																	$.getJSON('/est/est009/grid_func-ajax/atualizarAliquota/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
																		if (request.concluido == 0) {
																			$('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
																					est009AtualizarAliquotasLoop(idProgressBar,grupo,0);
																		}else{
																			if (request.code) {
																				grupo = request.grupo;
																				$.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
																					if(request.code){
																						grupo = request.grupo;
																						$('#dc-' + idProgressBar).dialog("destroy").remove();
																						dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
																						divRefresh('{$g['divId']}');
																						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
																					}else{
																						grupo = request.grupo;
																						divRefresh('{$g['divId']}');
																						$('#dc-' + idProgressBar).dialog("destroy").remove();
																						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																						dialogConfirm('Erro ao totalizar itens', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao totalizar itens<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data +'</div>', \$buttons);
																						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
																					}
																				});     
																			}else{
																				grupo = request.grupo;
																				divRefresh('{$g['divId']}');
																				$('#dc-' + idProgressBar).dialog("destroy").remove();
																				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																				dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>', \$buttons);
																				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
																			}           
																		} 
																	});                         
																}
															} else {
																grupo = request.grupo;
																divRefresh('{$g['divId']}');
																$('#dc-' + idProgressBar).dialog("destroy").remove();
																dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'<br>'+request.erros+'</div>');
																abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
															}
														} 
													});                   
												}
											} else {
												$('#dc-' + idProgressBar).dialog("destroy").remove();
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro ao incluir produtos na cotação', request.data, \$buttons);
											}
											// divRefresh('{$g['divId']}');
										});
										$(this).dialog("destroy").remove();
										$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
									},
									"Cancelar": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Gerar Cotação', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
						});					
					});
					
					$('#est009FlagCotar').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est009/grid_func-ajax/est009FlagMarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}', true);
						});
					});
					$('#est009FlagDesmarcarCotar').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est009/grid_func-ajax/est009FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}', true);
						});
					});
					$('#est009FlagMarcarComprar').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est009/grid_func-ajax/est009FlagMarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}', true);
						});
					});
					$('#est009FlagDesmarcarComprar').unbind('click').bind('click', function(data) {
						$('#{$g['divId']}').showLoading();
						$.getJSON('/est/est009/grid_func-ajax/est009FlagDesmarcar/?ajax=true&extra={$sql_}&operacao=2&callback=?', function(request) {
							$('#{$g['divId']}').hideLoading();
							divRefresh('{$g['divId']}', true);
						});
					});
					$('#est009GerarMontagemEstoque').unbind('click').bind('click', function(data) { // PASSA SQL
						$('#{$g['divId']}').showLoading();

						var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?';
						$.ajax({
							url: '/est/est009/grid_func-ajax/est009VerificarGerarMontagemEstoque/',
							type: 'POST',
							dataType: 'jsonp',
							data: dadosFiltro,
						}).done(function(request, temp1, temp2){
							$('#{$g['divId']}').hideLoading();
							if (request.code) {
								var \$buttons = {
									"Gerar Montagem": function() {
										var numeroCotacao = $(this).find('input').val();
										$('#{$g['divId']}').showLoading();

										var dadosFiltro = 'ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?';
										$.ajax({
											url: '/est/est009/grid_func-ajax/est009GerarMontagemEstoque/',
											type: 'POST',
											dataType: 'jsonp',
											data: dadosFiltro,
										}).done(function(request, temp1, temp2){
											$('#{$g['divId']}').hideLoading();
											if (request.code) {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Ok', "Montagem gerada com sucesso!", \$buttons);
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagem', '/est/est007/content/est007ContentMontagensEditarNovo/', '&acaoId=' + encodeURIComponent(request.data), [850,500]);
											} else {
												var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
												dialogConfirm('Erro', request.data, \$buttons);
											}
											divRefresh('{$g['divId']}');
										});
										$(this).dialog("destroy").remove();
										$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
									},
									"Cancelar": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Gerar Montagem', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
								dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
						});
					});
				} </script></div>
EOT;
            }
			// Imprime o filtro do GRID (normalmente executado acima do GRID)
			echo $g['ui']->gridFiltroPrint($gridFiltro);
	  }	
      
 
		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D047_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
					if ('$r_extraLinha' != '') {
	        			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [1000,600]);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Compras estoque por produto', '/est/est009/content/est009GridComprasEstoqueLinha/', '&extraLinha=' + encodeURIComponent(D001_Id), [1000,500]);
					}
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

	case 'est009GridComprasEstoqueEmpresas':

        //Incluido o IF no filtro obrigatório para a nova funçao de clicar sobre a linha do GRID e trazer o resultado do D001 de todas as C004 (07/01/2021)
        if (!$r_extra) {
            $filtroObrigatorio = true;
        }
		
		$meses = 12; 
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
		$D009_Id = (isset($mD001['D009_Id']) ? $mD001['D009_Id'] : '');
		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
				$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
				$filtro = '';
		}

        //Incluido o IF para a nova funçao de clicar sobre a linha do GRID para trazer o nome da empresa apenas quando for o resultado de todos as C004 (07/01/2021)
        if (!$r_extra) { $exibirEmpresa = false; } else { $exibirEmpresa = true; }

		$grid = array(
			array('colunaValor' => 'Foto', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Imagem', 'colunaAgrupar' => '1', 'callback' => 'mostrarImagemProdutoNormal', 'colunaExibir' => true),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'extra' => 'style="width:1%;"'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Flag_Curva_ABC', 'colunaTitulo' => 'C', 'style' => 'width:1%;', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', false, 'exibirTotal', false, '720')),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-Item', 'style' => 'width:1%'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'style' => 'width:1%'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa'),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Comprar_Calculada', 'colunaTitulo' => 'Sugestão', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Quantidade_Compra_Emergencia', 'colunaTitulo' => 'Emer<br>gência', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-vermelho);'),
            //array('colunaValor' => 'D009_Compra_Emergencia(D009_Quantidade_Minima_Estoque,D009_Quantidade_Estoque_Liquido,D009_Quantidade_OC)', 'colunaTitulo' => 'Emer<br>gência', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-vermelho);'),
            array('colunaValor' => 'D009_Quantidade_Comprar', 'colunaTitulo' => 'Qtde<br>Comprar', 'colunaEditar' => true, 'colunaCasasDecimais' => '2', 'colunaEditarTodos' => true, 'colunaAlinhar' => 'right', 'style' => 'background-color: var(--celula-amarelo);font-size:14px'),
            array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => '', 'callback' => 'validaCampoCotar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'D009_C007_Id_Comprar'),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Compra<br>mínima', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'border-right:1px dashed silver'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq<BR>Físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq<BR>Loja', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq<br/>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),            
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Estq<BR>Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qtde<BR>OC', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela', 'colunaTitulo' => 'Estq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            array('colunaValor' => 'D009_Quantidade_Consumo', 'colunaTitulo' => 'Vendido', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'callback' => 'est009campoConsumido', 'style' => 'border-left:1px dashed silver'),
            // array('colunaValor' => "(SELECT SUM(T004_Quantidade) 
            //                                FROM T004 
            //                           LEFT JOIN T003 ON T003_Id=T004_T003_Id 
            //                           LEFT JOIN D047 ON D047_Id = T003_D047_Id
            //                               WHERE T004_D009_Id=D009_Id
            //                                 AND T003_Flag_Status_Orcamento='C' 
            //                                 AND D047_Flag_Compra = 'S'
            //                                 AND T003_C004_Id=D009_C004_Id) AS vendaPerdida", 
            //                         'colunaTitulo' => 'Venda<br>Perdida', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamentos Cancelados', '/crm/crm001/content/crm001contentOrcamentoPerdido/', 'D009_Id', '1000,1000', true), 'style' => 'border-left:1px dashed silver'),
            array('colunaValor' => 'D009_Quantidade_Minima_Estoque', 'colunaTitulo' => 'Estoque<br>mínimo', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero'),
            array('colunaValor' => 'D009_Quantidade_Maxima_Estoque', 'colunaTitulo' => 'Máximo', 'colunaAlinhar' => 'right', 'callback' => 'gCorrigeNumero', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Frequencia_Venda', 'colunaTitulo' => 'Freq', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            array('colunaValor' => 'D009_Clientes_Consumo', 'colunaTitulo' => 'Client', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            array('colunaValor' => 'D009_Frequencia_Venda_Meses', 'colunaTitulo' => 'Freq<br>Mes', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '0'),
            // array('colunaValor' => '(GROUP_CONCAT(DISTINCT CONCAT(T241_Flag_Principal,"|",T241_Flag_Homologado,"|",fornecedor.D024_Flag_Contribuinte,"|",fornecedor.D024_Nome_Empresa) SEPARATOR "<br>")) as colunaFornecedores', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;border-left:1px dashed silver', 'callback' => 'colunaFornecedores'),
            array('colunaValor' => 'colunaFornecedores', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Fornecedores', 'colunaAgrupar' => '1', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;border-left:1px dashed silver', 'callback' => 'colunaFornecedores'),
            //array('colunaValor' => '(select group_concat(distinct substr(D024_Nome_Empresa,1,25) separator "<br>") from T001 left join D024 on D024_Id=T001_D024_Id where T001_D009_Id=D009_Id and T001_Flag_Atualiza_Custo="S" and T001_Flag_Operacao="E" and datediff(curdate(),T001_Data_Lancamento)<=800 limit 1) as ultimosFornecedores', 'colunaTitulo' => 'Últimos fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:10px;width:1%'),
            array('colunaValor' => 'T001_Quantidade', 'colunaTitulo' => 'Qte<br>Ult.NF', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right', 'style' => 'border-left:1px dashed silver'),
            array('colunaValor' => 'T001_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço NF', 'colunaCasasDecimais' => '5', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'datediff(curdate(),T001_Data_Lancamento)', 'colunaTitulo' => 'Dias', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'NF', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data NF', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'ultimaCompra.D024_Nome_Fantasia', 'colunaTitulo' => 'NF Fornecedor', 'colunaAlinhar' => 'left', 'style' => 'border-right:1px dashed silver'),
            array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback' => 'validaCampoComprar', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Meses_MMF', 'colunaTitulo' => 'Meses MMF', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Meses_Compra', 'colunaTitulo' => 'Meses Compra', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D009_Meses_Reposicao', 'colunaTitulo' => 'Meses Reposição', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'border-right:1px dashed silver'),
			// array('colunaValor' => 'mediaVendasMMF', 'colunaTitulo' => 'Média Vendas MMF', 'colunaTipo' => 'livre', 'callback' => 'mediaMMF', 'colunaAlinhar' => 'right','colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D009_C007_Id_Comprar', 'colunaTitulo' => 'D009_C007_Id_Comprar', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'Ult.Venda', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'datediff(curdate(),D009_Data_Ultima_Venda)', 'colunaTitulo' => 'Dias', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador'),
            array('colunaValor' => 'vendasMes', 'colunaTitulo' => 'Vendas por mês', 'colunaTipo' => 'livre', 'callback' => 'vendasMes', 'colunaAlinhar' => 'right', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct D004_Local)', 'colunaTitulo' => 'Locação'),
            array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Especificacoes', 'colunaTitulo' => 'Especificações', 'colunaAgrupar' => '1', 'colunaExibir' => true, 'style' => 'max-width:250px;min-width:250px;white-space:normal' ),
			array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pré-cadastro?', 'colunaExibir' => false, 'colunaAlinhar' => 'center'), 
			array('colunaValor' => 'D049_Flag_Nao_Comprar', 'colunaTitulo' => 'Não Comprar', 'callback' => 'validaCampoNaoComprar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
			array('colunaValor' => 'vendaPerdida', 'colunaTitulo' => 'Venda Perdida', 'colunaTipo' => 'livre', 'callback' => 'est009VendaPerdida', 'style' => 'border-right:1px dashed silver', 'colunaAlinhar' => 'center','colunaExibir' => false),
            array('colunaValor' => 'concat(D001_Codigo_Produto, \' - \', D001_Descricao_Produto) as tituloProduto', 'colunaTitulo' => 'tituloProduto', 'style' => 'width:5%;', 'colunaExibir' => false),
            array('colunaValor' => 'concat(D001_Codigo_Produto, D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'Produto', 'style' => 'width:5%;', 'colunaExibir' => false),
		);

		/**
		* Definição da SQL
		*/
		$from = 'D009';
		
		$extra = <<<EOT
			LEFT JOIN T187 on T187_D009_Id=D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D083 on D083_D049_Id=D049_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join T001 on T001_Id=D009_T001_Id
			left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
			left join C007 on (C007_Id=D009_C007_Id_Comprar and D009_Flag_Cotar='S') or C007_Id=D009_C007_Id_Comprador
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			left join C004 on C004_Id=D009_C004_Id
			LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
EOT;

        //Incluido o IF na clausula where para a nova funçao de clicar sobre a linha do GRID e trazer o resultado do D001 de todas as empresas (07/01/2021)
        if ($r_extra) {
            $extra .= ' where D001_Id = "' . $r_extra .'"  AND C004_Flag_Ativo="S" group by D009_Id';            
        } else {
            $extra .= <<<EOT
                where D001_Flag_Ativo='S'
				  and C004_Flag_Ativo='S'
                    {$filtro}
                group by D009_Id
EOT;
		}
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extraTotal = <<<EOT
		LEFT JOIN T187 on T187_D009_Id=D009_Id
		left join D049 on D049_Id=D009_D049_Id
		left join D001 on D001_Id=D049_D001_Id
		left join D082 on D082_Id=D049_D082_Id
		left join D083 on D083_D049_Id=D049_Id
		left join D037 on D037_Id=D001_D037_Id
		left join C008 on C008_Id=D001_C008_Id
		left join D060 on D001_D060_Id=D060_Id
		left join D002 on D001_D002_Id=D002_Id
		left join D003 on D003_Id=D001_D003_Id or D003_Id=D002_D003_Id
		left join D015 on D001_D015_Id=D015_Id
		left join T001 on T001_Id=D009_T001_Id
		left join D024 as ultimaCompra on ultimaCompra.D024_Id = T001_D024_Id
		left join C007 on C007_Id=D009_C007_Id_Comprar
		left join T066 on T066_D009_Id=D009_Id
		left join D004 on D004_Id=T066_D004_Id
		LEFT JOIN T241 ON T241_D001_Id=D001_Id
		left join C004 on C004_Id=D009_C004_Id
		LEFT JOIN D024 as fornecedor on fornecedor.D024_Id=T241_D024_Id
EOT;

        //Incluido o IF na clausula where para a nova funçao de clicar sobre a linha do GRID e trazer o resultado do D001 de todas as empresas (07/01/2021)
        if ($r_extra) {
            $extraTotal .= ' where D001_Id = "' . $r_extra .'" AND C004_Flag_Ativo="S" group by D009_Id';            
        } else {
            $extraTotal .= <<<EOT
                where D001_Flag_Ativo='S'
				and C004_Flag_Ativo='S'
				 {$filtro}
EOT;
		}

		function gridLinha($array) {
		   global $g;
		   if ($array['todosCampos']['round(D009_Quantidade_Comprar_Calculada,0)']<0) {
		      //return 'linhaCinza';
		   }
		   if ($array['todosCampos']['D049_Flag_Nao_Comprar']=='S') {
			   return 'linhaError';
			}
			if ($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S') {
				return 'linhaVermelho';
			}
		}

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);


		/**
		* Filtro
		*/
		$gridFiltro = array(
			'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
				array('title' => 'Produto', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'tituloProduto', 'campoOrdem' => 'ASC', 'campoSelecionado' => true),
			  ),
			),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
            'D001_Aplicacao,D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Referência', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'C008_Tipo,C008_Id' => array('titulo' => 'Depto', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad023/grid/cad023GridTipos/', 'campo' => 'C008_Tipo', 'tamanho' => 20)),
            'D060_Descricao_Grupo,D060_Id' => array('titulo' => 'Setor', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad027/grid/cad027GridGrupos/', 'campo' => 'D060_Descricao_Grupo', 'tamanho' => 20)),
            'D003_Nome_Linha,D003_Id' => array('titulo' => 'Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad026/grid/cad026GridLinhas/', 'campo' => 'D003_Nome_Linha', 'tamanho' => 20)),
            'D002_Descricao_Produto,D002_Id' => array('titulo' => 'Sub-Grupo', 'tipo' => 'texto', 'grid' => array('url' => '/cad/cad028/grid/cad028GridSubGrupos/', 'campo' => 'D002_Descricao_Produto', 'tamanho' => 20)),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estoq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estoq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtd. OC', 'tipo' => 'numero'),
            'D009_Quantidade_Comprar_Calculada' => array('titulo' => 'Sugestão Compra', 'tipo' => 'numero'),
			'D009_Quantidade_Compra_Emergencia' => array('titulo' => 'Qtde Emgergência', 'tipo' => 'numero'),
			'D009_Compra_Emergencia(D009_Quantidade_Minima_Estoque[[virgula]]D009_Quantidade_Estoque_Liquido[[virgula]]D009_Quantidade_OC)' => array('titulo' => 'Qtde Emgergência', 'tipo' => 'numero', 'exibir' => false),
            'D009_Frequencia_Venda' => array('titulo' => 'Frequencia', 'tipo' => 'numero'),
            'D009_Clientes_Consumo' => array('titulo' => 'Clientes', 'tipo' => 'numero'),
            'D009_Quantidade_Consumo' => array('titulo' => 'Vendido', 'tipo' => 'numero', 'exibir' => false),
            'D009_Flag_Cotar' => array('titulo' => 'Marcados', 'tipo' => 'texto'),
            'D009_Flag_Comprar' => array('titulo' => 'Flag Comprar', 'tipo' => 'texto', 'exibir' => false),
            'C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'T001_Data_Lancamento' => array('titulo' => 'Data NF Forn', 'tipo' => 'data'),
            'D009_Data_Ultima_Venda' => array('titulo' => 'Ultima venda', 'tipo' => 'data'),
            'datediff(curdate(),D009_Data_Ultima_Venda)' => array('titulo' => 'Dias ult.venda', 'tipo' => 'numero'),
            'D049_Quantidade_Embalagem_Compra' => array('titulo' => 'Embalagem', 'tipo' => 'texto'),
            'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
            'D049_Flag_Curva_ABC' => array('titulo' => 'Curva ABC', 'tipo' => 'texto'),
            'fornecedor.D024_Nome_Empresa,fornecedor.D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'fornecedor.D024_Flag_Contribuinte' => array('titulo' => 'Tipo Fornecedor', 'tipo' => 'select', 'select' =>
            	array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '0 - Não contribuinte', 'value' => '0' ),   
					array('title' => '1 - Consumidor Final', 'value' => '1'),   
					array('title' => '2 - Revenda', 'value' => '2'),   
					array('title' => '3 - Produtor rural', 'value' => '3'),
					array('title' => '4 - Simples', 'value' => '4'),   
					array('title' => '5 - Industria', 'value' => '5')   
				)
			),
			'D049_Flag_Nao_Comprar' => array('titulo' => 'Comprar?', 'tipo' => 'select', 'select' =>
			array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),  
				array('title' => 'Sim', 'value' => 'N',), 
				array('title' => 'Não', 'value' => 'S',), 
				)
			),
			'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' =>
			array(
				array('title' => 'Todos', 'value' => '', 'selected' => true),  
				array('title' => 'Sim', 'value' => 'S',), 
				array('title' => 'Não', 'value' => 'N',), 
				)
			),
			'D009_Quantidade_Estoque_Tabela' => array('titulo' => 'Estq Tabela', 'tipo' => 'numero'),
			'D009_Quantidade_Estoque_Fora' => array ('titulo' => 'Estq Fora', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Gera o filtro e acrescenta ao $extraTotal
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal, false, false, true);
		
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
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100, false, false, NULL, 120000);
		
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
      
      // totalizadores
/*
	  $D009 = mysql_query("SELECT D009_Id FROM ". $from . " " . $extra);
      $totalItens = mysql_num_rows($D009);
      
      echo <<<EOT
        <div class="buttonsBottomTop">
              <span class="item" style="text-align:right">Itens<br><input type="text" value="{$totalItens}" class="whiteBg" id="" size="11" readonly></span>
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
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D047_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [1000,600]);
					//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Compras estoque por produto', '/est/est009/content/est009GridComprasEstoqueLinha/', '&extra=' + encodeURIComponent(D001_Id), [1000,500]);
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


	case 'est009GridCotacaoEmergencia':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo'  => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T004_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal'),
            array('colunaValor' => 'IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade)', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            array('colunaValor' => 'T004_Flag_Gerar_Cotacao', 'colunaTitulo' => 'Cotar', 'callback' => 'checkboxCotarEmergencia', 'colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'T004_C007_Id_Comprador'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estoq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            array('colunaValor' => 'T004_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            // array('colunaValor' => "(GROUP_CONCAT(CONCAT(T241_Flag_Principal,'|',T241_Flag_Homologado,'|',Fornecedor.D024_Flag_Contribuinte,'|',Fornecedor.D024_Nome_Empresa) SEPARATOR '<br>')) as colunaFornecedores", 'colunaExibir' => false),
            array('colunaValor' => "ColunaFornecedor", 'colunaTipo' => 'livre', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores', 'colunaEditarExtra' => "/cad/cad002/content/cad002contentCadastroFornecedorLote/|550|100"),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T004_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'substr(Cliente.D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T004_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
			array('colunaValor' => 'T004A_Flag_Solicitar_Prazo_Compras', 'colunaTitulo' => 'Prazo entrega', 'colunaExibir' => false)			
        );

		$from = 'T004';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T004_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T004_D037_Id
			LEFT JOIN D005 on D005_Id=T004_D005_Id
			LEFT JOIN D006 on D006_Id=T004_D006_Id
			LEFT JOIN T006 on T006_T004_Id=T004_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN T003 ON T003_Id=T004_T003_Id
			LEFT JOIN D024 as Cliente ON Cliente.D024_Id=T003_D024_Id
			LEFT JOIN T219 ON T219_T004_Id=T004_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as Fornecedor ON Fornecedor.D024_Id=T241_D024_Id
			LEFT JOIN T004A ON T004_Id=T004A_T004_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}'
			AND (T004_Flag_Comprar='S' OR T004A_Flag_Solicitar_Prazo_Compras ='S')
			AND T219_Id is null
			AND T006_Id is null
			AND T003_Flag_Status_Orcamento = 'P'
			AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')
			AND D006_Flag_Estoque = 'D'
			GROUP BY T004_Id
EOT;

/*
			WHERE ifnull(T219_Id,0)<=0 
			AND IFNULL(T004_T219_Id,0) <= 0 
			AND T004_Flag_Comprar = 'S' 
			AND D009_C004_Id='{$g['empresaAtual']}'
			AND T006_Id IS NULL
			AND (ifnull(D049_Flag_Tipo,'CP') = 'CP' or ifnull(D049_Flag_Tipo,'CP') = '')
			AND T003_Flag_Perdido != 'S' AND T003_Flag_Status_Orcamento != 'V'
*/

		// Botões
		$botoes = array(
			array('titulo' => 'Gerar Cotação Manual', 'id' => 'est009GerarCotacaoEmergencia'),
			array('titulo' => 'Gerar Cotações Automáticas', 'id' => 'est009GerarCotacaoEmergenciaAutomatica'),
			array('titulo' => 'Juntar produtos', 'id' => 'est009JuntarProdutos1'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'T003_Data_Emissao' => array('titulo' => 'Emissão Orçam.', 'tipo' => 'data'),
            'Fornecedor.D024_Nome_Empresa,Fornecedor.D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'Fornecedor.D024_Flag_Contribuinte' => array('titulo' => 'Tipo Fornecedor', 'tipo' => 'select', 'select' =>
            	array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '0 - Não contribuinte', 'value' => '0' ),   
					array('title' => '1 - Consumidor Final', 'value' => '1'),   
					array('title' => '2 - Revenda', 'value' => '2'),   
					array('title' => '3 - Produtor rural', 'value' => '3'),
					array('title' => '4 - Simples', 'value' => '4'),   
					array('title' => '5 - Industria', 'value' => '5')   
				)
            ),
            'T004_Flag_Gerar_Cotacao' => array('titulo' => 'Cotar?(S/N)', 'tipo' => 'texto'),
			'T004A_Flag_Solicitar_Prazo_Compras' => array('titulo' => 'Prazo entrega', 'exibir' => false, 'tipo' => 'select', 'select' =>
				array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => 'Não', 'value' => 'N',),
					array('title' => 'Sim', 'value' => 'S')    
				)
			),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		
		function gridLinha($array) {
		    global $g;
			if($array['todosCampos']['T004A_Flag_Solicitar_Prazo_Compras']=='S'){
		        return 'linhaFundoAmareloClaro';
		    }
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}

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

		$sql_ = base64_encode($geradoSql);
		
		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarCotacaoEmergencia">Gerar Cotação Manual</button>
					<button type="button" id="est009GerarCotacaoEmergenciaAutomatica">Gerar Cotações Automáticas</button>
					<button type="button" id="est009JuntarProdutos1">Juntar produtos</button>
				</div>
EOT;
		}*/

		echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est009JuntarProdutos1').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntar/','', [400,200]);
				});

				$('#est009GerarCotacaoEmergencia').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoEmergencia/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Adicionar": function() {
									var numeroCotacao = $(this).find('input').val();
                                    if(!(numeroCotacao == "")){
                                        idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

    									$.getJSON('/est/est009/grid_func-ajax/adicionarProdutosCotacao/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
                                            if (request.code) {
                                                var T005_Id = request.data;
                                                $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                                $.getJSON('/est/est015/form_func-ajax/est015atualizarCFOP/?ajax=true&T218_Id=' + numeroCotacao + '&callback=?', function(request) {
                                                    if (request.code) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% Processando CFOP &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                        est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,1);

                                                        function est015AtualizarAliquotasCotacaoLoop(idProgressBar, numeroCotacao, inicio){
                                                            $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + numeroCotacao + '&C004_Id='+encodeURIComponent("{$g['empresaAtual']}")+'&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                                if (request.concluido == 0) {
                                                                    $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                            est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,0);
                                                                }else{
                                                                    if (request.code) {
                                                                        $.getJSON('/est/est015/form_func-ajax/est015TotalizarInsereItemCotacaoEmergencia/?ajax=true&T218_Id=' + numeroCotacao +'&T005_Id='+ T005_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                            if(request.code){
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);   
                                                                            }else{
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                                dialogConfirm('Erro ao totalizar itens', request.data, \$buttons); 
                                                                            }
                                                                        });     
                                                                    }else{
                                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                        dialogConfirm('Erro ao atualizar Alíquotas', request.data, \$buttons);     
                                                                    }           
                                                                } 
                                                            });                         
                                                        }
                                                    }else{
                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                        dialogConfirm('Erro ao processar CFOP', request.data, \$buttons);  
                                                    }
                                                });
                                            } else {
                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                dialogConfirm('Erro ao adicionar produtos', request.data, \$buttons);  
                                            }
                                            // divRefresh('{$g['divId']}');
                                        });
                                    }else{
                                        dialogConfirm('Erro', 'Informe um número de cotação', \$buttons);
                                    }
								$(this).dialog("destroy").remove();
								$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Nova Cotação": function() {
									$('#{$g['divId']}').showLoading();
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est015/content/est015ContentOrcamento/', '&tab=geral&GerarCotacaoEmergencia=true&extra=' + encodeURIComponent("{$sql_}"), [650,500]);
									$('#{$g['divId']}').hideLoading();
									$(this).dialog("destroy").remove();
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', request.data + '<br /><br />Digite o número de uma cotação para adicionar os produtos selecionados.<br /><br />Cotação: ', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est009GerarCotacaoEmergenciaAutomatica').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarCotacaoAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Cotações": function() {
                                    idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

									$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoEmergenciaAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										if (request.code) {
                                            var grupo = request.data;
                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                            est009atualizarCFOPLoop(idProgressBar, grupo, 1);

                                            function est009atualizarCFOPLoop(idProgressBar, grupo, inicio){
                                                $.getJSON('/est/est009/grid_func-ajax/est009atualizarCFOP/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                    if (request.concluido == 0) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando CFOP</b>' + '<br/>Ordens de compra atualizadas: <b>' + request.ordemDeCompra );
                                                        est009atualizarCFOPLoop(idProgressBar,grupo,0);
                                                    }else{
                                                        if (request.code) {
                                                            grupo = request.grupo;
                                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% CFOP processada &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,1);

                                                            function est009AtualizarAliquotasLoop(idProgressBar, grupo, inicio){
                                                                $.getJSON('/est/est009/grid_func-ajax/atualizarAliquota/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                                    if (request.concluido == 0) {
                                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,0);
                                                                    }else{
                                                                        if (request.code) {
                                                                            grupo = request.grupo;
                                                                            $.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                                if(request.code){
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                    divRefresh('{$g['divId']}');
                                                                                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                                                }else{
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                                    dialogConfirm('Erro ao totalizar itens', request.data, \$buttons);
                                                                                }
                                                                            });     
                                                                        }else{
                                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                            dialogConfirm('Erro ao atualizar alíquotas', request.data, \$buttons);
                                                                        }           
                                                                    } 
                                                                });                         
                                                            }
                                                        } else {
                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                            dialogConfirm('Erro ao processar CFOP', request.data, \$buttons);
                                                        }
                                                    } 
                                                });                   
                                            }
                                        } else {
                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                            dialogConfirm('Erro ao incluir produtos na cotação', request.data, \$buttons);
                                        }
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Cotação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
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
					var D001_Id = id[1].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Compras estoque por produto', '/est/est009/content/est009GridComprasEstoqueLinha/', '&extraLinha=' + encodeURIComponent(D001_Id), [1000,500]);
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

	case 'est009GridComprasEmergencia':
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				function checkboxCotarPedidoEmergencia(checkbox,T006_Id,divId) {
				    var selected;
				    if($(checkbox).is(':checked')) {
				        selected='S';
				    } else {
				        selected='N';
				    }
				    $.getJSON('/est/est009/grid_func-ajax/checkboxCotarPedidoEmergencia/?ajax=true&T006_Id=' + encodeURIComponent(T006_Id) + '&selected=' + selected + '&callback=?', function(request) {});
				}
			}</script></div>
EOT;
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            //array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'colunaEditar' => true),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'callback' => 'est009DescricaoProduto'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as Falta', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            //array('colunaValor' => 'T006_Flag_Gerar_Cotacao', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'T006_C007_Id_Comprador', 'style' => 'width: 1%'),
            array('colunaValor' => 'T006_Flag_Gerar_Cotacao', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'T006_C007_Id_Comprador', 'style' => 'width: 1%'),
            //array('colunaValor' => '(SELECT SUBSTR(D024_Nome_Empresa,1,10) FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = T006_T219_Id)', 'colunaTitulo' => 'Forn. Selec.', 'style' => 'font-size: 10px;'),
            //array('colunaValor' => 'T218_Grupo', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produtos X Fornecedor', '/est/est015/content/est015ContentProdutosVsFornecedor/', '', '1000,583', false, false, array('T218_Grupo' => 'T218_Grupo'))),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Flag_Comprar', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estoq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            // array('colunaValor' => '(GROUP_CONCAT(CONCAT(T241_Flag_Principal,"|",T241_Flag_Homologado,"|",IFNULL(Fornecedor.D024_Flag_Contribuinte,0),"|",Fornecedor.D024_Nome_Empresa) SEPARATOR "<br>")) as colunaFornecedores', 'colunaExibir' => false),
            array('colunaValor' => "ColunaFornecedor", 'colunaTitulo' => 'Fornecedores', 'colunaTipo' => 'livre', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores', 'colunaEditarExtra' => "/cad/cad002/content/cad002contentCadastroFornecedorLote/|550|100"),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(Cliente.D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Nao_Comprar', 'colunaTitulo' => 'Não<br>Comprar', 'callback' => 'gCheckbox', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'callbackParameter' => array('T006_Flag_Nao_Comprar', 'N|S'), 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            //array('colunaValor' => 'IF(T219_Id > 0,"Comprar","Cotar") as GroupBy', 'colunaExibir' => false, 'colunaAgrupar' => true),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_Id=T219_T006_Id
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T224 ON T218_Id=T224_T218_Id
			LEFT JOIN T225 ON T225_Id=T006_T225_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}'
			AND T005_Flag_Status!=4 
			AND T005_Flag_Status!=5
			AND T005_Flag_Status!=8
			AND T005_Flag_Status!=26
			AND T005_Flag_Status!=13
			AND T005_Flag_Status!=15
			AND T005_Flag_Status!=6
			AND T005_Flag_Status!=9
			AND T005_Flag_Status!=20
			AND T219_Id is null
			AND T225_Id is null
			AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
			AND (T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-if(T006_Quantidade_OC>0,T006_Quantidade_OC,0))>0
			AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')			
			AND T006_Flag_Nao_Comprar != 'S'
			AND D006_Flag_Estoque = 'D'
			GROUP BY T006_Id
EOT;

/*          AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade
			AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
			AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
			AND (T219_Id IS NULL OR T219_Id <= 0)
			AND (ifnull(D049_Flag_Tipo,'CP') = 'CP' or ifnull(D049_Flag_Tipo,'CP') = 'PD' or ifnull(D049_Flag_Tipo,'CP') = '') */

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            //'(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            //'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
			//	array('title' => 'Tipo CFOP', 'campoAgrupar' => 'GroupBy', 'campoMostrar' => 'GroupBy', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
			//)),
			//'ifnull(T219_T218_Id[[virgula]]0)' => array('titulo' => 'Operacao', 'tipo' => 'select', 
			//	'select' => array(
			//		array('title' => '...', 'value' => ''),
			//		array('title' => 'Cotar', 'value' => '*VAZIO*'),
			//		array('title' => 'Comprar', 'value' => '!'),
			//	)
			//),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
            'Fornecedor.D024_Nome_Empresa,Fornecedor.D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'texto'),
            'Fornecedor.D024_Flag_Contribuinte' => array('titulo' => 'Tipo Fornecedor', 'tipo' => 'select', 'select' =>
            	array(
					array('title' => '...', 'value' => '', 'selected' => true),
					array('title' => '0 - Não contribuinte', 'value' => '0' ),   
					array('title' => '1 - Consumidor Final', 'value' => '1'),   
					array('title' => '2 - Revenda', 'value' => '2'),   
					array('title' => '3 - Produtor rural', 'value' => '3'),
					array('title' => '4 - Simples', 'value' => '4'),   
					array('title' => '5 - Industria', 'value' => '5')   
				)
            ),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'texto'),
            'T006_Flag_Gerar_Cotacao' => array('titulo' => 'Marcados?(S/N)', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}

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

		$sql_ = base64_encode($geradoSql);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarCotacaoPedidoEmergencia">Gerar Cotação Manual</button>
					<button type="button" id="est009GerarCotacaoPedidoEmergenciaAutomatica">Gerar Cotações Automáticas</button>
					<!--<button type="button" id="est009GerarOrdemCompra">Gerar O.C.</button>-->
					<button type="button" id="est009GerarOT">Gerar Transferência</button>
					<button type="button" id="est009AtualizarEstoque">Atualizar estoque</button>
					<button type="button" id="est009JuntarProdutos2">Juntar produtos</button>
                    <button type="button" id="est009RecuperarEstoqueLoja">Recuperar loja</button>
				</div>
EOT;
		}*/

		// Botões
		$botoes = array(
			array('titulo' => 'Gerar Cotação Manual', 'id' => 'est009GerarCotacaoPedidoEmergencia'),
			array('titulo' => 'Gerar Cotações Automáticas', 'id' => 'est009GerarCotacaoPedidoEmergenciaAutomatica'),
			array('titulo' => 'Gerar Transferência', 'id' => 'est009GerarOT'),
			array('titulo' => 'Atualizar estoque', 'id' => 'est009AtualizarEstoque'),
			array('titulo' => 'Juntar produtos', 'id' => 'est009JuntarProdutos2', 'janelaUrl' => '/cad/cad002/content/cad002ContentJuntar/', 'janelaTamanho' => '[400,200]'),
			array('titulo' => 'Recuperar loja', 'id' => 'est009RecuperarEstoqueLoja'),
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#xest009JuntarProdutos2').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntar/','', [400,200]);
				});

				$('#est009GerarCotacaoPedidoEmergencia').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoPedidoEmergencia/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Adicionar": function() {
									var numeroCotacao = $(this).find('input').val();
                                    if(!(numeroCotacao == "")){
                                        idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

    									$.getJSON('/est/est009/grid_func-ajax/adicionarProdutosPedidoCotacao/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
    										$('#{$g['divId']}').hideLoading();
    										if (request.code) {
                                                var T005_Id = request.data;
                                                $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                                $.getJSON('/est/est015/form_func-ajax/est015atualizarCFOP/?ajax=true&T218_Id=' + numeroCotacao + '&callback=?', function(request) {
                                                    if (request.code) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% Processando CFOP &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                        est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,1);

                                                        function est015AtualizarAliquotasCotacaoLoop(idProgressBar, numeroCotacao, inicio){
                                                            $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&C004_Id= '+'{$g['empresaAtual']}'+ '&callback=?', function(request) {
                                                                if (request.concluido == 0) {
                                                                    $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                    est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,0);
                                                                }else{
                                                                    if (request.code) {
                                                                        $.getJSON('/est/est015/form_func-ajax/est015TotalizarInsereItemCotacaoEmergencia/?ajax=true&T218_Id=' + numeroCotacao +'&T005_Id= '+T005_Id+'&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                            if(request.code){
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);  
                                                                                divRefresh('{$g['divIdParent']}'); 
                                                                            }else{
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                                dialogConfirm('Erro ao totalizar itens', request.data, \$buttons); 
                                                                            }
                                                                        });     
                                                                    }else{
                                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                        dialogConfirm('Erro ao atualizar Alíquotas', request.data, \$buttons);     
                                                                    }           
                                                                } 
                                                            });                         
                                                        }
                                                    }else{
                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                        dialogConfirm('Erro ao processar CFOP', request.data, \$buttons);  
                                                    }
                                                });
                                            } else {
                                                ('#dc-' + idProgressBar).dialog("destroy").remove();
                                                dialogConfirm('Erro ao adicionar produtos', request.data, \$buttons);  
                                            }
    										divRefresh('{$g['divId']}');
    									});
                                    }else{
                                        dialogConfirm('Erro', 'Informe um número de cotação', \$buttons);
                                    }
                                $(this).dialog("destroy").remove();
                                $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Nova Cotação": function() {
									$('#{$g['divId']}').showLoading();
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est015/content/est015ContentOrcamento/', '&tab=geral&extra=' + encodeURIComponent("{$sql_}") + '&GerarCotacaoPedidoEmergencia=true', [650,500]);
									$('#{$g['divId']}').hideLoading();
									$(this).dialog("destroy").remove();
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', request.data + '<br /><br />Digite o número de uma cotação para adicionar os produtos selecionados.<br /><br />Cotação: ', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				$('#est009GerarOT').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009GerarOT/?ajax=true&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Transferência', '/est/est016/content/GerarOT/', '&tab=geral&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&count=' + encodeURIComponent(request.data) + '&origem=transferencia', [550,200]);
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				$('#est009AtualizarEstoque').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

                $('#est009RecuperarEstoqueLoja').unbind('click').bind('click', function(data) {
                      var \$buttons = {
                            "Sim": function() {
                                  $('#{$g['divId']}').showLoading();
                                  $.getJSON('/est/est009/grid_func-ajax/est009RecuperarEstoqueLoja/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
                                        if (request.code) {
                                              var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                              dialogConfirm('Operação Ok', request.data, \$buttons);
                                              divRefresh('{$g['divId']}', true);
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
                      dialogConfirm('Recuperar loja', 'Deseja realizar a transferencia do estoque loja? ', \$buttons);
                });

				$('#est009GerarCotacaoPedidoEmergenciaAutomatica').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarCotacaoPedidoAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Cotações": function() {
									idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoPedidoEmergenciaAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
                                        if (request.code) {
                                            var grupo = request.data;
                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                            est009atualizarCFOPLoop(idProgressBar, grupo, 1);

                                            function est009atualizarCFOPLoop(idProgressBar, grupo, inicio){
                                                $.getJSON('/est/est009/grid_func-ajax/est009atualizarCFOP/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                    if (request.concluido == 0) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando CFOP</b>' + '<br/>Ordens de compra atualizadas: <b>' + request.ordemDeCompra );
                                                        est009atualizarCFOPLoop(idProgressBar,grupo,0);
                                                    }else{
                                                        if (request.code) {
                                                            grupo = request.grupo;
                                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% CFOP processada &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,1);

                                                            function est009AtualizarAliquotasLoop(idProgressBar, grupo, inicio){
                                                                $.getJSON('/est/est009/grid_func-ajax/atualizarAliquota/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                                    if (request.concluido == 0) {
                                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,0);
                                                                    }else{
                                                                        if (request.code) {
                                                                            grupo = request.grupo;
                                                                            $.getJSON('/est/est015/form_func-ajax/est015TotalizarInsereItemCotacaoEmergencia/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                                if(request.code){
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                    divRefresh('{$g['divId']}');
                                                                                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                                                }else{
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                                    dialogConfirm('Erro ao totalizar itens', request.data, \$buttons);
                                                                                }
                                                                            });     
                                                                        }else{
                                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                            dialogConfirm('Erro ao atualizar alíquotas', request.data, \$buttons);
                                                                        }           
                                                                    } 
                                                                });                         
                                                            }
                                                        } else {
                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                            dialogConfirm('Erro ao processar CFOP', request.data, \$buttons);
                                                        }
                                                    } 
                                                });                   
                                            }
                                        } else {
                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                            dialogConfirm('Erro ao incluir produtos na cotação', request.data, \$buttons);
                                        }	
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Cotação', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
	
				$('#est009GerarOrdemCompra').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Ordens de Compra": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Ordens de compra geradas com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordens de Compra Geradas - Grupo: ' + request.data, '/est/est016/content/est016ContentOrdensCompraGrupo/', '&T224_Grupo=' + encodeURIComponent(request.data), [800,600]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Ordem de Compra', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
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
					var D001_Id = id[1].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Compras estoque por produto', '/est/est009/content/est009GridComprasEstoqueLinha/', '&extraLinha=' + encodeURIComponent(D001_Id), [1000,500]);
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

	case 'est009GridComprasEmergenciaComprar':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'callback' => 'est009DescricaoProduto'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte<br>Ped.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Qte<br>Cot.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#def7e3;', 'colunaEditar' => 'S'),
            array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width: 1%'),
            array('colunaValor' => 'IFNULL((SELECT SUBSTR(D024_Nome_Empresa,1,25) FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = T006_T219_Id),"<font color=\'red\'>SELECIONE O MELHOR PREÇO</font>")', 'colunaTitulo' => 'Fornecedor escolhido', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_Grupo', 'callback' => 'abrirJanela', 'colunaTitulo' => 'Cot.', 'callbackParameter' => array('Produtos X Fornecedor', '/est/est015/content/est015ContentProdutosVsFornecedor/', '', '1000,583', false, false, array('T218_Grupo' => 'T218_Grupo'))),
            array('colunaValor' => 'GROUP_CONCAT(DISTINCT T224_Id SEPARATOR ",") as T224_Id', 'colunaTitulo' => 'O.C.', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605'), 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estoq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            // array('colunaValor' => '(GROUP_CONCAT(DISTINCT CONCAT(T241_Flag_Principal,"|",T241_Flag_Homologado,"|",IFNULL(Fornecedor.D024_Flag_Contribuinte,0),"|",Fornecedor.D024_Nome_Empresa) SEPARATOR "<br>")) as colunaFornecedores', 'colunaExibir' => false),
            array('colunaValor' => "ColunaFornecedor", 'colunaTipo' => 'livre', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores','colunaEditarExtra' => "/cad/cad002/content/cad002contentCadastroFornecedorLote/|550|100"),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(Cliente.D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_T219_Id=T219_Id
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T225 ON T219_Id=T225_T219_Id
			LEFT JOIN T224 ON T224_Id=T225_T224_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			LEFT JOIN T005A ON T005A_T005_Id = T005_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}' 
			AND T005_Flag_Status!=4 
			AND T005_Flag_Status!=5
			AND T005_Flag_Status!=8
			AND T005_Flag_Status!=26
			AND T005_Flag_Status!=13
			AND T005_Flag_Status!=15
			AND T005_Flag_Status!=6
			AND T005_Flag_Status!=9
			AND T005_Flag_Status!=20
			AND T219_Id is not null
			AND T225_Id is null
			AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
			AND (T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-if(T006_Quantidade_OC>0,T006_Quantidade_OC,0))>0
			AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')			
			AND T006_Flag_Nao_Comprar != 'S'
			AND D006_Flag_Estoque = 'D'
			AND IFNULL(T005A_Flag_Tipo,1) != 2
			GROUP BY T006_Id
EOT;

/*			AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade 
			AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
			AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
			AND IF(T006_T219_Id > 0,T219_Id = T006_T219_Id, 1=1)
			AND T219_Id > 0
			AND (ifnull(D049_Flag_Tipo,'CP') = 'CP' or ifnull(D049_Flag_Tipo,'CP') = 'PD' or ifnull(D049_Flag_Tipo,'CP') = '')*/



		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            '(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
            'T224_Id' => array('titulo' => 'Ordem de Compra', 'tipo' => 'texto'),
            'T219_Flag_Comprar' => array('titulo' => 'Marcados?(S/N)', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}

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

		$sql_ = base64_encode($geradoSql);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarOrdemCompraComprar">Gerar O.C.</button>
					<button type="button" id="est009AtualizarEstoqueComprar">Atualizar estoque</button>
					<button type="button" id="est009JuntarProdutos3">Juntar produtos</button>
				</div>
EOT;
		}*/

		// Botões
        $botoes = array(
			array('titulo' => 'Gerar O.C.', 'id' => 'est009GerarOrdemCompraComprar'),
			array('titulo' => 'Atualizar estoque', 'id' => 'est009AtualizarEstoqueComprar'),
			array('titulo' => 'Juntar produtos', 'id' => 'est009JuntarProdutos3', 'janelaUrl' => '/cad/cad002/content/cad002ContentJuntar/', 'janelaTamanho' => '[400,200]'),

         );
        echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$colunaFornecedor = 'SUZOVUxMKChTRUxFQ1QgU1VCU1RSKEQwMjRfTm9tZV9FbXByZXNhLDEsMjUpIEZST00gVDIxOSBMRUZUIEpPSU4gVDIxOCBPTiBUMjE4X0lkPVQyMTlfVDIxOF9JZCBMRUZUIEpPSU4gRDAyNCBPTiBEMDI0X0lkPVQyMThfRDAyNF9JZCBXSEVSRSBUMjE5X0lkID0gVDAwNl9UMjE5X0lkKSwiPGZvbnQgY29sb3I9J3JlZCc+U0VMRUNJT05FIE8gTUVMSE9SIFBSRcOHTzwvZm9udD4iKQ==';

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th[valor="{$colunaFornecedor}"]').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#xest009JuntarProdutos3').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntar/','', [400,200]);
				});

				$('#est009AtualizarEstoqueComprar').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarOrdemCompraComprar').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						var mensagemFornecedor = request.data;
						if (request.code) {
							$.getJSON('/est/est009/grid_func-ajax/est009VerificarQuantidade/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
								if (request.code) {
									gerarOrdemCompra();
								} else {
									var buttons = {
		  							    "Continuar": function() {
											$(this).dialog("destroy").remove(); 
											gerarOrdemCompra();
										},
										"Cancelar": function() { 
											$(this).dialog("destroy").remove();
										}
									}	
									dialogConfirm('Alerta', '<textarea style=" background-color: #e7e5e5; width:900px; height:400px; text-transform: none; font-size: 14px;">' + request.estoque + '</textarea>', buttons);
  								}
							});

						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();

						function gerarOrdemCompra(){
							var \$buttons = {
								"Adicionar": function() {
									var numeroOC = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/adicionarProdutosOrdemCompra/?ajax=true&T224_Id=' + numeroOC + '&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Produto(s) incluido(s) com sucesso", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(numeroOC) + '&tabela=T224', [990,605]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Gerar Ordens de Compra": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Ordens de compra geradas com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordens de Compra Geradas - Grupo: ' + request.data, '/est/est016/content/est016ContentOrdensCompraGrupo/', '&T224_Grupo=' + encodeURIComponent(request.data), [800,600]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', mensagemFornecedor + '<span style="font-size: 13px"><br>Digite o número da ordem de compra para adicionar os produtos selecionados. <br>Ordem de Compra: </span>', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						}
					});
				});
			} </script></div>
EOT;
        }

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
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
					var D001_Id = id[1].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Compras estoque por produto', '/est/est009/content/est009GridComprasEstoqueLinha/', '&extraLinha=' + encodeURIComponent(D001_Id), [1000,500]);
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

	case 'est009GridComprasEmergenciaCotando':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;', 'callback' => 'est009DescricaoProduto'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qte<br>Ped.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            array('colunaValor' => 'T219_Quantidade', 'colunaTitulo' => 'Qte<br>Cot.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background:#def7e3;', 'colunaEditar' => 'S'),
            //array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width: 1%'),
            array('colunaValor' => 'IFNULL((SELECT SUBSTR(D024_Nome_Empresa,1,25) FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = T006_T219_Id),"<font color=\'red\'>SELECIONE O MELHOR PREÇO</font>")', 'colunaTitulo' => 'Fornecedor escolhido', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_Grupo', 'callback' => 'abrirJanela', 'colunaTitulo' => 'Cot.', 'callbackParameter' => array('Produtos X Fornecedor', '/est/est015/content/est015ContentProdutosVsFornecedor/', '', '1000,583', false, false, array('T218_Grupo' => 'T218_Grupo'))),
            array('colunaValor' => 'GROUP_CONCAT(DISTINCT T224_Id SEPARATOR ",") as T224_Id', 'colunaTitulo' => 'O.C.', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605'), 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estoq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            array('colunaValor' => "ColunaFornecedor", 'colunaTipo' => 'livre', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores','colunaEditarExtra' => "/cad/cad002/content/cad002contentCadastroFornecedorLote/|550|100"),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(Cliente.D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_Id=T219_T006_Id			
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T225 ON T219_Id=T225_T219_Id
			LEFT JOIN T224 ON T224_Id=T225_T224_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			LEFT JOIN T005A ON T005_Id = T005A_T005_Id
			WHERE D009_C004_Id='{$g['empresaAtual']}' 
			AND T005_Flag_Status!=4 
			AND T005_Flag_Status!=5
			AND T005_Flag_Status!=8
			AND T005_Flag_Status!=26
			AND T005_Flag_Status!=13
			AND T005_Flag_Status!=15
			AND T005_Flag_Status!=6
			AND T005_Flag_Status!=9
			AND T005_Flag_Status!=20
			AND T219_Id IS NOT NULL
			AND IF(IFNULL(T005A_Flag_Tipo,1) = 1 , IFNULL(T006_T219_Id,0) <= 0 , IFNULL(T225_Id,0) <= 0)
			AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
			AND (T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-if(T006_Quantidade_OC>0,T006_Quantidade_OC,0))>0
			AND (D049_Flag_Tipo='CP' OR D049_Flag_Tipo='PD')			
			AND T006_Flag_Nao_Comprar != 'S'
			AND D006_Flag_Estoque = 'D'
			GROUP BY T006_Id
EOT;

		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            '(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
            'T224_Id' => array('titulo' => 'Ordem de Compra', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
				
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    } 
		}

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

		$sql_ = base64_encode($geradoSql);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<button type="button" id="est009GerarOrdemCotando">Gerar O.C.</button>-->
					<button type="button" id="est009AtualizarEstoqueCotando">Atualizar estoque</button>
					<button type="button" id="est009JuntarProdutosCotando">Juntar produtos</button>
				</div>
EOT;
		}*/

		// Botões
        $botoes = array(
			array('titulo' => 'Atualizar estoque', 'id' => 'est009AtualizarEstoqueCotando'),
			array('titulo' => 'Juntar produtos', 'id' => 'est009JuntarProdutosCotando', 'janelaUrl' => '/cad/cad002/content/cad002ContentJuntar/', 'janelaTamanho' => '[400,200]'),
         );
        echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		$colunaFornecedor = 'SUZOVUxMKChTRUxFQ1QgU1VCU1RSKEQwMjRfTm9tZV9FbXByZXNhLDEsMjUpIEZST00gVDIxOSBMRUZUIEpPSU4gVDIxOCBPTiBUMjE4X0lkPVQyMTlfVDIxOF9JZCBMRUZUIEpPSU4gRDAyNCBPTiBEMDI0X0lkPVQyMThfRDAyNF9JZCBXSEVSRSBUMjE5X0lkID0gVDAwNl9UMjE5X0lkKSwiPGZvbnQgY29sb3I9J3JlZCc+U0VMRUNJT05FIE8gTUVMSE9SIFBSRcOHTzwvZm9udD4iKQ==';

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid th[valor="{$colunaFornecedor}"]').removeAttr('onclick').css({'cursor': 'auto'});
			} </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#xest009JuntarProdutosCotando').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Juntar Produtos', '/cad/cad002/content/cad002ContentJuntar/','', [400,200]);
				});

				$('#est009AtualizarEstoqueCotando').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarOrdemCotando').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Adicionar": function() {
									var numeroOC = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/adicionarProdutosOrdemCompra/?ajax=true&T224_Id=' + numeroOC + '&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Produto(s) incluido(s) com sucesso", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(numeroOC) + '&tabela=T224', [990,605]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Gerar Ordens de Compra": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Ordens de compra geradas com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordens de Compra Geradas - Grupo: ' + request.data, '/est/est016/content/est016ContentOrdensCompraGrupo/', '&T224_Grupo=' + encodeURIComponent(request.data), [800,600]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', request.data + '<span style="font-size: 13px"><br>Digite o número da ordem de compra para adicionar os produtos selecionados. <br>Ordem de Compra: </span>', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }
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

	case 'est009GridMontagensEmergencia':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
			array('colunaValor' => 'foto', 'colunaTitulo' => 'Foto', 'colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => '(T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-IFNULL((SELECT SUM(T048_Quantidade) FROM T048 LEFT JOIN T047 ON T047_Id=T048_T047_Id WHERE T048_D049_Id = D049_Id AND T047_C004_Id = D009_C004_Id AND (T047_Flag_Status != "A" OR T047_Flag_Status IS NULL)),0)-T006_Quantidade_OC) as Falta', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            array('colunaValor' => 'T006_Flag_Gerar_Cotacao', 'colunaTitulo' => '', 'callback' => 'checkboxMarcarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width: 1%', 'colunaEditarUsuario' => 'T006_C007_Id_Comprador'),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'estoqueDisponivelMontagem', 'colunaTitulo' => 'Disp.<br>Mont.', 'colunaAlinhar' => 'right', 'callback' => 'estoqueDisponivelMontagem', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => '(SELECT SUM(T048_Quantidade) FROM T048 LEFT JOIN T047 ON T047_Id=T048_T047_Id WHERE T048_D049_Id = D049_Id AND T047_C004_Id = D009_C004_Id AND (T047_Flag_Status != "A" OR T047_Flag_Status IS NULL))', 'colunaTitulo' => 'Aguard.<br>Mont.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaExibir' => false),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN D024 ON D024_Id=T005_D024_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_Id=T219_T006_Id
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T224 ON T218_Id=T224_T218_Id
			LEFT JOIN T225 ON T225_Id=T006_T225_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			WHERE D009_C004_Id='{$g['empresaAtual']}' 
			AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0) + T006_Quantidade_OC + 
				IFNULL((SELECT SUM(T048_Quantidade) FROM T048 LEFT JOIN T047 ON T047_Id=T048_T047_Id WHERE T048_D049_Id = D049_Id AND T047_C004_Id = D009_C004_Id AND (T047_Flag_Status != 'A' OR T047_Flag_Status IS NULL)),0)) < T006_Quantidade 
			AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
			AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
			AND D049_Flag_Tipo = 'MT'
			GROUP BY T006_Id
EOT;


		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            '(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}

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

		$sql_ = base64_encode($geradoSql);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarMontagem">Gerar Montagem</button>
					<button type="button" id="est009AtualizarEstoqueMontagem">Atualizar estoque</button>
				</div>
EOT;
		}*/

		$botoes = array(
			array('titulo' => 'Gerar Montagem', 'id' => 'est009GerarMontagem'),
			array('titulo' => 'Atualizar estoque', 'id' => 'est009AtualizarEstoqueMontagem'),
        );
        echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est009AtualizarEstoqueMontagem').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarMontagem').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarMontagem/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Montagem": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarMontagem/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Montagem gerada com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagem', '/est/est007/content/est007ContentMontagensEditarNovo/', '&acaoId=' + encodeURIComponent(request.data), [850,500]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Montagem', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

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

	case 'est009GridServicosEmergencia':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            //array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width: 1%'),
            //array('colunaValor' => 'IFNULL((SELECT SUBSTR(D024_Nome_Empresa,1,25) FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = T006_T219_Id),"<font color=\'red\'>SELECIONE O MELHOR PREÇO</font>")', 'colunaTitulo' => 'Fornecedor escolhido', 'style' => 'width:1%;'),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            array('colunaValor' => "(SELECT CONCAT(D001_Codigo_Produto,' - ', SUBSTRING(D001_Descricao_Produto,1,12)) FROM D042 LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens AND D009_C004_Id = '{$g['empresaAtual']}' WHERE D042_D049_Id=D1.D049_Id)", 'colunaTitulo' => 'Produto base', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaProdutoBruto'),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 AS D1 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN D024 ON D024_Id=T005_D024_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_Id=T219_T006_Id
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T224 ON T218_Id=T224_T218_Id
			LEFT JOIN T225 ON T225_Id=T006_T225_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			WHERE D009_C004_Id='{$g['empresaAtual']}' 
			AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade 
			AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
			AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
			AND D049_Flag_Tipo = 'MD'
			GROUP BY T006_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            '(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}
		
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

		$sql_ = base64_encode($geradoSql);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<button type="button" id="est009GerarOrdemServico">Gerar Ordem de Serviço</button>-->
					<button type="button" id="est009AtualizarEstoqueServico">Atualizar estoque</button>
				</div>
EOT;
		}*/

		// Botões
		$botoes = array(
			array('titulo' => 'Atualizar estoque', 'id' => 'est009AtualizarEstoqueServico'),
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est009AtualizarEstoqueServico').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarOrdemServico').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarOrdemServico/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Ordens de Compra": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Ordens de compra geradas com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordens de Compra Geradas - Grupo: ' + request.data, '/est/est016/content/est016ContentOrdensCompraGrupo/', '&T224_Grupo=' + encodeURIComponent(request.data), [800,600]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Ordem de Compra', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

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

	case 'est009GridProducoesEmergencia':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_T219_Id', 'colunaTitulo' => 'T219_Id', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);'),
            //array('colunaValor' => 'T219_Flag_Comprar', 'colunaTitulo' => '', 'callback' => 'checkboxCotarPedidoEmergencia', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'style' => 'width: 1%'),
            //array('colunaValor' => 'IFNULL((SELECT SUBSTR(D024_Nome_Empresa,1,25) FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = T006_T219_Id),"<font color=\'red\'>SELECIONE O MELHOR PREÇO</font>")', 'colunaTitulo' => 'Fornecedor escolhido', 'style' => 'width:1%;'),
            array('colunaValor' => 'T218_Grupo', 'callback' => 'abrirJanela', 'colunaTitulo' => 'Cot.', 'callbackParameter' => array('Produtos X Fornecedor', '/est/est015/content/est015ContentProdutosVsFornecedor/', '', '1000,583', false, false, array('T218_Grupo' => 'T218_Grupo'))),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'O.C.', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605'), 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'T006_Quantidade_OC', 'colunaTitulo' => 'OC', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'T006_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);max-width:180px;white-space:normal;min-width:180px'),
            //array('colunaValor' => 'ultimosFornecedores', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores',),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4'),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
        );

		$from = 'T006';
		
		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T006_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D082 on D082_Id=D049_D082_Id
			LEFT JOIN D037 on D037_Id=T006_D037_Id
			LEFT JOIN D005 on D005_Id=T006_D005_Id
			LEFT JOIN D006 on D006_Id=T006_D006_Id
			LEFT JOIN T005 ON T005_Id=T006_T005_Id
			LEFT JOIN T003 ON T003_Id=T005_T003_Id
			LEFT JOIN D024 ON D024_Id=T005_D024_Id
			LEFT JOIN T004 ON T004_Id=T006_T004_Id
			LEFT JOIN T219 ON T006_Id=T219_T006_Id
			LEFT JOIN T218 ON T218_Id=T219_T218_Id
			LEFT JOIN T224 ON T218_Id=T224_T218_Id
			LEFT JOIN T225 ON T225_Id=T006_T225_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
			WHERE D009_C004_Id='{$g['empresaAtual']}' 
			AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OC) < T006_Quantidade 
			AND (T005_Flag_Status = 0 OR T005_Flag_Status = '' OR T005_Flag_Status=14)
			AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
			AND D049_Flag_Tipo = 'PD'
			GROUP BY T006_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            '(ifnull(T219_T218_Id[[virgula]]0))' => array('titulo' => 'Cotação', 'tipo' => 'texto'),
            'D009_Quantidade_Estoque' => array('titulo' => 'Estq.Físico', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estq.Líquido', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Loja' => array('titulo' => 'Estq.Loja', 'tipo' => 'numero'),
            'D009_Quantidade_Estoque_Fora' => array('titulo' => 'Estq.Fora', 'tipo' => 'numero'),
            'D009_Quantidade_OC' => array('titulo' => 'Qtde OC', 'tipo' => 'numero'),
            'T005_Data_Emissao' => array('titulo' => 'Emissão Pedido', 'tipo' => 'data'),
            'T218_Data_Emissao' => array('titulo' => 'Emissão Cotação', 'tipo' => 'data'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		function gridLinha($array) {
		    global $g;
		    if($array['todosCampos']['D001_Flag_Pre_Cadastro']=='S'){
		        return 'linhaVermelho';
		    }
		}
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		$sql_ = base64_encode($geradoSql);

		if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<button type="button" id="est009GerarOrdemServico">Gerar Lista de Produção</button>-->
					<button type="button" id="est009AtualizarEstoqueProducao">Atualizar estoque</button>
				</div>
EOT;
		}

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est009AtualizarEstoqueProducao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarOrdemServico').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarOrdemServico/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Ordens de Compra": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarOrdemCompra/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Ordens de compra geradas com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Ordens de Compra Geradas - Grupo: ' + request.data, '/est/est016/content/est016ContentOrdensCompraGrupo/', '&T224_Grupo=' + encodeURIComponent(request.data), [800,600]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Ordem de Compra', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

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

	case 'est009GridComprasEstoqueModelo2':
		$filtroObrigatorio = true;
		$meses = 12;
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
            $filtro = '';
            if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
                  $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
            }else{
                  $filtro = '';
            }
		$grid = array();
		//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D001_Id', $g['divId'])),
		$grid[] = array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false,'extra' => 'style="width:1%;"');
		$grid[] = array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false,'style' => 'width:1%;','colunaAlinhar' => 'center');
		$grid[] = array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'));
		$grid[] = array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'');
		$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
        $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq<BR>Físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq<BR>Loja', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq<br/>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Estq<BR>Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qtde<BR>OC', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right');
            $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estq<br/>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true);

		$mes = strtotime('-3 month');
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$grid[] = array('colunaValor' => "(select sum(T008_Quantidade) from T008 left join D009 on D009_Id=T008_D009_Id left join D049 on D049_Id=D009_D049_Id left join D006 on D006_Id=T008_D006_Id left join T007 on T007_Id=T008_T007_Id where D049_D001_Id=D001_Id and D006_Flag_MMF='S' and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$dataInicial' and T007_Data_Emissao<='$dataFinal') as vendas", 'colunaTitulo' => "$tituloMes", 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'style' => 'background:#FFFFCC;');
			$mes = strtotime('+1 month', $mes);
		}
		
		$grid[] = array('colunaValor' => 'D009_Quantidade_Consumo', 'colunaTitulo' => 'Vendido', 'colunaAlinhar' => 'right', 'style'=>'background:#FFFF66;', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', true, false, false, '720'));

		$mes = time();
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$campo = date('Y-m-01', $mes);
			//Criação de uma coluna com concat para ser utilizado na passagem de parametros
			$grid[] = array('colunaValor' => "concat(D049_Id,'|','{$mes}') as parametro".$mes, 'colunaExibir' => false);
			$grid[] = array('colunaValor' => "(select sum(T055_Quantidade) from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id=D009_Id and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>='{$dataInicial}' and T075_Data_Previsao<='{$dataFinal}' and T075_Flag_MP != 1)", 'colunaTitulo' => "$tituloMes", 'colunaAlinhar'=>'right', 'style' => 'background:lightblue', 'callback' => 'abrirJanela', 'callbackParameter' => array('Previsão DI', '/est/est009/content/est009ContentPrevisaoDI/', 'parametro'.$mes, '700,400', true));
			$mes = strtotime('+1 month', strtotime($dataInicial));
		}

		$mes = time();
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$campo = date('Y-m-01', $mes);
			$grid[] = array('colunaValor' => "(SELECT IFNULL(T232_Quantidade,0) FROM T232 WHERE T232_D009_Id=D009_Id AND T232_MesAno = '{$campo}')", 'colunaTitulo' => "$tituloMes", 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'colunaEditar' => 'true', 'colunaEditarJSCallback' => 'compraEstoqueProjetarFuturo', 'style' => 'background:#FFFF99;');
			$mes = strtotime('+1 month', strtotime($dataInicial));
		}


		//$grid[] = array('colunaValor' => "D009_Quantidade_Minima_Estoque", 'colunaTitulo' => 'Média<br>Mês', 'colunaAlinhar'=>'right', 'callback'=>'gCorrigeNumero');
		//$grid[] = array('colunaValor' => "D009_Frequencia_Venda", 'colunaTitulo' => 'Freq<br>Venda', 'colunaAlinhar'=>'center');
		//$grid[] = array('colunaValor' => "D009_Clientes_Consumo", 'colunaTitulo' => 'Clientes', 'colunaAlinhar'=>'center');
		$grid[] = array('colunaValor' => "D009_Quantidade_Comprar_Calculada", 'colunaTitulo' => 'Comprar', 'colunaAlinhar'=>'right', 'callback'=>'gCorrigeNumero', 'style'=>'background-color: var(--celula-verde);');
		$grid[] = array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true);
        $grid[] = array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Compra<br>mínima', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'style' => 'border-right:1px dashed silver');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Comprar', 'colunaTitulo' => 'Comprar', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2', 'style' => 'background-color: var(--celula-amarelo);font-size:14px');
		$grid[] = array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => '', 'callback' => 'validaCampoCotar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'D009_C007_Id_Comprar');
		$grid[] = array('colunaValor' => 'D009_C007_Id_Comprar', 'colunaTitulo' => 'D009_C007_Id_Comprar', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D009_Meses_MMF', 'colunaTitulo' => 'Meses MMF', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2');
		//$grid[] = array('colunaValor' => 'D009_Meses_Compra', 'colunaTitulo' => 'Meses Compra', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2');
		//$grid[] = array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotar');
		//$grid[] = array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback'=>'validaCampoComprar');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Entrada", 'colunaTitulo' => 'Última entrada', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Venda", 'colunaTitulo' => 'Última venda', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Saida", 'colunaTitulo' => 'Última saída', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');

		/**
		* Definição da SQL
		*/
		$from = 'D009 as A';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			where (
				D009_C004_Id='{$g['empresaAtual']}'
				AND D001_Flag_Ativo='S'
                        {$filtro}
			)
			group by D009_Id
EOT;
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extraTotal = <<<EOT
			LEFT JOIN T187 on T187_D009_Id=D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D083 on D083_D049_Id=D049_Id
			left join D024 on D024_Id=D083_D024_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join T001 on T001_Id=D009_T001_Id
			left join C007 on C007_Id=D009_C007_Id_Comprar
			where (
				D009_C004_Id='{$g['empresaAtual']}'
				AND D001_Flag_Ativo='S'
                        {$filtro}
			)
EOT;
		/**
		* Botões Almir
		*/
		$botoes = array(
			array('titulo' => 'Gerar Cotação Manual', 'id' => 'est009GerarCotacaoNovoM2'),
			array('titulo' => 'Gerar Cotações Automáticas', 'id' => 'est009GerarCotacaoAutomaticaM2'),
			array('titulo' => 'Gerar Transferência', 'id' => 'est009GerarOTEstoqueM2'),
			array('titulo' => 'Gerar Montagem', 'id' => 'est009GerarMontagemEstoqueM2'),
			array('titulo' => 'Atualizar estoque', 'id' => 'est014AtualizarCalculoEstoqueM2'),
			);
			echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Código Cliente', 'tipo' => 'numero'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
			'D009_Quantidade_Comprar_Calculada' => array('titulo' => 'Qtd. Comprar', 'tipo' => 'numero'),
			'D009_Flag_Cotar' => array('titulo' => 'Cotar', 'tipo' => 'texto'),
			'D009_Flag_Comprar' => array('titulo' => 'Comprar', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque_Tabela' => array('titulo' => 'Estq Tabela', 'tipo' => 'numero'),

		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Gera o filtro e acrescenta ao $extraTotal
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal, false, false, true);


		// Monta fórmula total produtos sem impostos como base do cálculo da Margem
		//$baseTotal = ('{Quantidade_Comprar}' > '0') ? "{Quantidade_Comprar}" : "{Quantidade_Comprar_Calculada}";
		//$baseTotal .= "(";
		//$baseTotal .= ('{Quantidade_Comprar_Calculada}' > '0') ? "{Quantidade_Comprar_Calculada}" : '0' ;
		//$baseTotal .= ")";

		/**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
			//array('nome' => 'Valor_Preco_Unitario', 'valor' => 'T001_Valor_Preco_Unitario', 'titulo' => 'Preço Unitário'),
			//array('nome' => 'Quantidade_Comprar', 'valor' => 'D009_Quantidade_Comprar', 'titulo' => 'Quantidade Comprar'),
			//array('nome' => 'Quantidade_Comprar_Calculada', 'valor' => 'D009_Quantidade_Comprar_Calculada', 'titulo' => 'Quantidade Comprar Calculada'),
			//array('nome' => 'Valor_Total_R$', 'tipo' => 'totais', 'valor' => '({Valor_Preco_Unitario}*'.$baseTotal.');', 'titulo' => 'Valor Total Compra R$'),
			//array('nome' => 'Valor_Compra_R$', 'tipo' => 'totais', 'valor' => '{Valor_Total_R$}', 'titulo' => 'Valor Total Compra R$'),
        );
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est009GerarCotacaoNovoM2">Gerar Cotação Manual</button>
					<button type="button" id="est009GerarCotacaoAutomaticaM2">Gerar Cotações Automáticas</button>
					<button type="button" id="est009GerarOTEstoqueM2">Gerar Transferência</button>
					<button type="button" id="est009GerarMontagemEstoqueM2">Gerar Montagem</button>
					<button type="button" id="est014AtualizarCalculoEstoqueM2">Atualizar estoque</button>
				</div>
EOT;
		}*/

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est014AtualizarCalculoEstoqueM2').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est009/grid_func-ajax/processaCalcularEstoque/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', request.data, \$buttons);
									divRefresh('{$g['divId']}', true);
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
					dialogConfirm('Atualizar Estoque', 'Deseja atualizar o estoque? ', \$buttons);
				});

				$('#est009GerarCotacaoNovoM2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
                    $.getJSON('/est/est009/grid_func-ajax/est009GerarCotacao/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code) {
                            var \$buttons = {
                                "Adicionar": function() {
                                    var numeroCotacao = $(this).find('input').val();
                                    if(!(numeroCotacao == "")){
                                        idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div>");


                                        $.getJSON('/est/est009/grid_func-ajax/adicionarProdutosCotacao/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$sql_}") + '&tipo=comprasEstoque&callback=?', function(request) {
                                            if (request.code) {
                                                $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                                $.getJSON('/est/est015/form_func-ajax/atualizarCFOP/?ajax=true&T218_Id=' + numeroCotacao + '&callback=?', function(request) {
                                                    if (request.code) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% Processando CFOP &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                        est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,1);

                                                        function est015AtualizarAliquotasCotacaoLoop(idProgressBar, numeroCotacao, inicio){
                                                            $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&C004_Id= '+encodeURIComponent("{$g['empresaAtual']}")+ '&callback=?', function(request) {
                                                                if (request.concluido == 0) {
                                                                    $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                            est015AtualizarAliquotasCotacaoLoop(idProgressBar,numeroCotacao,0);
                                                                }else{
                                                                    if (request.code) {
                                                                        $.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&T218_Id=' + numeroCotacao + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                            if(request.code){
                                                                                divRefresh('{$g['divId']}');
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                divRefresh('{$g['divId']}');
                                                                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);   
                                                                            }else{
                                                                                divRefresh('{$g['divId']}');
                                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                dialogConfirm('Erro ao totalizar itens', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao totalizar itens<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data +'</div>', \$buttons); 
                                                                                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]); 
                                                                            }
                                                                        });     
                                                                    }else{
                                                                        divRefresh('{$g['divId']}');
                                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                        dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>', \$buttons); 
                                                                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]); 
                                                                    }           
                                                                } 
                                                            });                         
                                                        }
                                                    }else{
                                                        divRefresh('{$g['divId']}');
                                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                        dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'</div>', \$buttons); 
                                                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + numeroCotacao+'', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + numeroCotacao + '&tabela=T218', [1000,610]);  
                                                    }
                                                });
                                            } else {
                                                divRefresh('{$g['divId']}');
                                                ('#dc-' + idProgressBar).dialog("destroy").remove();
                                                dialogConfirm('Erro ao adicionar produtos', request.data, \$buttons);  
                                            }
                                        });
                                    }else{
                                        $('#dc-' + idProgressBar).dialog("destroy").remove();
                                        dialogConfirm('Erro', 'Informe um número de cotação', \$buttons);
                                    }
                                $(this).dialog("destroy").remove();
                                $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
                                },
								"Nova Cotação": function() {
									$('#{$g['divId']}').showLoading();
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cadastrar Cotação', '/est/est015/content/est015ContentOrcamento/', '&tab=geral&GerarCotacaoEstoque=true&extra=' + encodeURIComponent("{$sql_}"), [650,500]);
									$('#{$g['divId']}').hideLoading();
									$(this).dialog("destroy").remove();
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							var novoId = dialogConfirm('Gerar Cotação', request.data + '<br /><br />Digite o número de uma cotação para adicionar os produtos selecionados.<br /><br />Cotação: ', \$buttons);
							$('<input>').attr('id', 'numeroCotacao').addClass('formInput ui-state-default ui-corner-all').appendTo($("#dc-" + novoId).find('p')).focus();
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    		dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				
				$('#est009GerarCotacaoAutomaticaM2').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/est/est009/grid_func-ajax/est009VerificarCotacaoEstoqueAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code) {
                            var \$buttons = {
                                "Gerar Cotações": function() {
                                     idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Adicionando produtos na cotação</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div>");

                                    $.getJSON('/est/est009/grid_func-ajax/est009GerarCotacaoAutomatica/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&tipo=cotacaoEmergencia&callback=?', function(request) {
                                        if (request.code) {
                                            var grupo = request.data;
                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');
                                            est009atualizarCFOPLoop(idProgressBar, grupo, 1);

                                            function est009atualizarCFOPLoop(idProgressBar, grupo, inicio){
                                                $.getJSON('/est/est009/grid_func-ajax/est009atualizarCFOP/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                    if (request.concluido == 0) {
                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando CFOP</b>' + '<br/>Ordens de compra atualizadas: <b>' + request.ordemDeCompra );
                                                        est009atualizarCFOPLoop(idProgressBar,grupo,0);
                                                    }else{
                                                        if (request.code) {
                                                            grupo = request.grupo;
                                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b></br>' + '<font color="green"><b>100% CFOP processada &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,1);

                                                            function est009AtualizarAliquotasLoop(idProgressBar, grupo, inicio){
                                                                $.getJSON('/est/est009/grid_func-ajax/atualizarAliquota/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                                    if (request.concluido == 0) {
                                                                        $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                                                est009AtualizarAliquotasLoop(idProgressBar,grupo,0);
                                                                    }else{
                                                                        if (request.code) {
                                                                            grupo = request.grupo;
                                                                            $.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&grupo=' + grupo + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
                                                                                if(request.code){
                                                                                    grupo = request.grupo;
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b><br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
                                                                                    divRefresh('{$g['divId']}');
                                                                                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                                                }else{
                                                                                    grupo = request.grupo;
                                                                                    divRefresh('{$g['divId']}');
                                                                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                                    dialogConfirm('Erro ao totalizar itens', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao totalizar itens<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data +'</div>', \$buttons);
                                                                                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                                                }
                                                                            });     
                                                                        }else{
                                                                            grupo = request.grupo;
                                                                            divRefresh('{$g['divId']}');
                                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                                                            dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>', \$buttons);
                                                                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                                        }           
                                                                    } 
                                                                });                         
                                                            }
                                                        } else {
                                                            grupo = request.grupo;
                                                            divRefresh('{$g['divId']}');
                                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                            dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'<br>'+request.erros+'</div>');
                                                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Cotações Geradas - Grupo: ' + grupo, '/est/est015/content/est015ContentCotacoesGrupo/', '&T218_Grupo=' + grupo, [800,600]);
                                                        }
                                                    } 
                                                });                   
                                            }
                                        } else {
                                            $('#dc-' + idProgressBar).dialog("destroy").remove();
                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                            dialogConfirm('Erro ao incluir produtos na cotação', request.data, \$buttons);
                                        }
                                        divRefresh('{$g['divId']}');
                                    });
                                    $(this).dialog("destroy").remove();
                                    $('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
                                },
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Cotação', request.data, \$buttons);
						} else {
                            $('#dc-' + idProgressBar).dialog("destroy").remove();
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				$('#est009GerarOTEstoqueM2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009GerarOTEstoque/?ajax=true&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Transferência', '/est/est016/content/GerarOT/', '&tab=geral&sql=' + encodeURIComponent($('.divToScroll:visible:last').attr('gridsql')) + '&count=' + encodeURIComponent(request.data) + '&origem=estoque', [550,200]);
							return false;
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
				$('#est009GerarMontagemEstoqueM2').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est009/grid_func-ajax/est009VerificarGerarMontagemEstoque/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Montagem": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est009/grid_func-ajax/est009GerarMontagemEstoque/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Ok', "Montagem gerada com sucesso!", \$buttons);
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagem', '/est/est007/content/est007ContentMontagensEditarNovo/', '&acaoId=' + encodeURIComponent(request.data), [850,500]);
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro', request.data, \$buttons);
										}
										divRefresh('{$g['divId']}');
									});
									$(this).dialog("destroy").remove();
									$('#{$g['divId']}').find('.ui-tabs-panel:eq(0)').data('fechaForm', 1);
								},
								"Cancelar": function() { $(this).dialog("destroy").remove(); } 
							};
							dialogConfirm('Gerar Montagem', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});
			} </script></div>
EOT;
        }

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		

		if (!personalizacaoTotais()) { 
			/**
			 * Totalizadores
			 */
			$itens = $g['sqlAuto']->pegarSqlCampo("count(distinct D009_Id)", $from, $extraTotal);
			$total = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T001_Valor_Preco_Unitario*if(D009_Quantidade_Comprar>0,D009_Quantidade_Comprar,if(D009_Quantidade_Comprar_Calculada>0,D009_Quantidade_Comprar_Calculada,0)))", $from, $extraTotal));
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<span class="item" style="text-align:right">Itens<br><input type="text" value="{$itens}" class="whiteBg" id="" size="11" readonly></span>-->
					<span class="item" style="text-align:right">Valor compra R$<br><input type="text" value="{$total}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D047_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var D001_Id = id[0].replace(/D001_Id-/, '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id), [1000,600]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });

				function compraEstoqueProjetarFuturo(idChavePrimaria, campoEditado, valorAntigo, novoValor) {
					$.getJSON('/est/est008/grid_func-ajax/compraEstoqueProjetarFuturo/?ajax=true&D009_Id=' + encodeURIComponent(idChavePrimaria) + '&campoEditado=' + encodeURIComponent(campoEditado) + '&novoValor=' + encodeURIComponent(novoValor) + '&callback=?', function(request) {
						if (request.code) {
							//
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
				    		dialogConfirm("Erro!", request.data, \$buttons);
						}
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

		$primeiraLinhaGridHead = <<<EOT
			<tr>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Produto</th>
				<th colspan="7" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Estoque</th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Últimas vendas</th>
				<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Importações</th>
			    <th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Projeção venda</th>
				<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
			</tr>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'est009GridComprasEstoqueModelo2View':
		$filtroObrigatorio = true;
		$meses = 12;
		$periodo = date('Y-m-d', strtotime("-{$meses} month"));
		/**
		* Definição do Grid (Tabela)
		*/
            $filtro = '';
            if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
                  $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
            }else{
                  $filtro = '';
            }
		$grid = array();
		//array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D001_Id', $g['divId'])),
		$grid[] = array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false,'extra' => 'style="width:1%;"');
		$grid[] = array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true,'colunaExibir' => false,'style' => 'width:1%;','colunaAlinhar' => 'center');
		$grid[] = array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'));
		$grid[] = array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style'=>'');
		$grid[] = array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca');
        $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estq<BR>Físico', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estq<BR>Loja', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'width:1%');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estq<br/>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Estq<BR>Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right');
		$grid[] = array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'Qtde<BR>OC', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right');
            $grid[] = array('colunaValor' => 'D009_Quantidade_Estoque_Tabela(D009_Id)', 'colunaTitulo' => 'Estq</br>Tabela', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%','colunaAlinhar'=>'right', 'colunaExibir'=>true);

		$mes = strtotime('-3 month');
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$grid[] = array('colunaValor' => "(select sum(T008_Quantidade) from T008 left join D009 on D009_Id=T008_D009_Id left join D049 on D049_Id=D009_D049_Id left join D006 on D006_Id=T008_D006_Id left join T007 on T007_Id=T008_T007_Id where D049_D001_Id=D001_Id and D006_Flag_MMF='S' and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$dataInicial' and T007_Data_Emissao<='$dataFinal') as vendas", 'colunaTitulo' => "$tituloMes", 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'style' => 'background:#FFFFCC;');
			$mes = strtotime('+1 month', $mes);
		}
		
		$grid[] = array('colunaValor' => 'D009_Quantidade_Consumo', 'colunaTitulo' => 'Vendido', 'colunaAlinhar' => 'right', 'style'=>'background:#FFFF66;', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Estatística de venda do produto', '/ven/ven001/outro/vendasDozeMeses/', 'D009_Id', 'right', true, false, false, '720'));

		$mes = time();
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$campo = date('Y-m-01', $mes);
			//Criação de uma coluna com concat para ser utilizado na passagem de parametros
			$grid[] = array('colunaValor' => "concat(D049_Id,'|','{$mes}') as parametro".$mes, 'colunaExibir' => false);
			$grid[] = array('colunaValor' => "(select sum(T055_Quantidade) from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id=D009_Id and ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao>='{$dataInicial}' and T075_Data_Previsao<='{$dataFinal}' and T075_Flag_MP != 1)", 'colunaTitulo' => "$tituloMes", 'colunaAlinhar'=>'right', 'style' => 'background:lightblue', 'callback' => 'abrirJanela', 'callbackParameter' => array('Previsão DI', '/est/est009/content/est009ContentPrevisaoDI/', 'parametro'.$mes, '700,400', true));
			$mes = strtotime('+1 month', strtotime($dataInicial));
		}

		$mes = time();
		for ($i=0;$i<4;$i++){
			$dataInicial = date('Y-m-1', $mes);
			$dataFinal = date('Y-m-t', $mes);
			$tituloMes = date('m/Y', $mes);
			$campo = date('Y-m-01', $mes);
			$grid[] = array('colunaValor' => "(SELECT IFNULL(T232_Quantidade,0) FROM T232 WHERE T232_D009_Id=D009_Id AND T232_MesAno = '{$campo}')", 'colunaTitulo' => "$tituloMes", 'colunaCasasDecimais'=>'2', 'colunaAlinhar'=>'right', 'style' => 'background:#FFFF99;');
			$mes = strtotime('+1 month', strtotime($dataInicial));
		}


		//$grid[] = array('colunaValor' => "D009_Quantidade_Minima_Estoque", 'colunaTitulo' => 'Média<br>Mês', 'colunaAlinhar'=>'right', 'callback'=>'gCorrigeNumero');
		//$grid[] = array('colunaValor' => "D009_Frequencia_Venda", 'colunaTitulo' => 'Freq<br>Venda', 'colunaAlinhar'=>'center');
		//$grid[] = array('colunaValor' => "D009_Clientes_Consumo", 'colunaTitulo' => 'Clientes', 'colunaAlinhar'=>'center');
		$grid[] = array('colunaValor' => "D009_Quantidade_Comprar_Calculada", 'colunaTitulo' => 'Comprar', 'colunaAlinhar'=>'right', 'callback'=>'gCorrigeNumero', 'style'=>'background-color: var(--celula-verde);');
		$grid[] = array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2');
        $grid[] = array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Compra<br>mínima', 'colunaAlinhar' => 'center', 'colunaCasasDecimais' => '2', 'style' => 'border-right:1px dashed silver');
		$grid[] = array('colunaValor' => 'D009_Quantidade_Comprar', 'colunaTitulo' => 'Comprar', 'colunaCasasDecimais'=>'2', 'style' => 'background-color: var(--celula-amarelo);font-size:14px');
		$grid[] = array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => '', 'callback' => 'validaCampoCotar', 'style' => 'width:1%;background-color: var(--celula-amarelo);', 'colunaAlinhar' => 'center', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'D009_C007_Id_Comprar');
		$grid[] = array('colunaValor' => 'D009_C007_Id_Comprar', 'colunaTitulo' => 'D009_C007_Id_Comprar', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D009_C004_Id', 'colunaTitulo' => 'D009_C004_Id', 'colunaExibir' => false);
		$grid[] = array('colunaValor' => 'D009_Meses_MMF', 'colunaTitulo' => 'Meses MMF', 'colunaCasasDecimais'=>'2');
		//$grid[] = array('colunaValor' => 'D009_Meses_Compra', 'colunaTitulo' => 'Meses Compra', 'colunaEditar'=>true, 'colunaCasasDecimais'=>'2');
		//$grid[] = array('colunaValor' => 'D009_Flag_Cotar', 'colunaTitulo' => 'Cotar', 'callback' => 'validaCampoCotar');
		//$grid[] = array('colunaValor' => 'D009_Flag_Comprar', 'colunaTitulo' => 'Comprar', 'callback'=>'validaCampoComprar');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Entrada", 'colunaTitulo' => 'Última entrada', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Venda", 'colunaTitulo' => 'Última venda', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');
		//$grid[] = array('colunaValor' => "D009_Data_Ultima_Saida", 'colunaTitulo' => 'Última saída', 'callback'=>'gCorrigeData', 'colunaAlinhar'=>'right');

		/**
		* Definição da SQL
		*/
		$from = 'D009 as A';
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extra = <<<EOT
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join T001 on T001_Id=D009_T001_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			where (
				D009_C004_Id='{$g['empresaAtual']}'
				AND D001_Flag_Ativo='S'
                        {$filtro}
			)
			group by D009_Id
EOT;
		
		//left join D116 on D116_Id=D131_D116_Id	
		$extraTotal = <<<EOT
			LEFT JOIN T187 on T187_D009_Id=D009_Id
			left join D049 on D049_Id=D009_D049_Id
			left join D001 on D001_Id=D049_D001_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D083 on D083_D049_Id=D049_Id
			left join D024 on D024_Id=D083_D024_Id
			left join D037 on D037_Id=D001_D037_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D060 on D001_D060_Id=D060_Id
			left join D002 on D001_D002_Id=D002_Id
			left join D015 on D001_D015_Id=D015_Id
			left join T001 on T001_Id=D009_T001_Id
			left join C007 on C007_Id=D009_C007_Id_Comprar
			where (
				D009_C004_Id='{$g['empresaAtual']}'
				AND D001_Flag_Ativo='S'
                        {$filtro}
			)
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D083_Codigo_Produto_Fornecedor' => array('titulo' => 'Código Cliente', 'tipo' => 'numero'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
			'D009_Quantidade_Comprar_Calculada' => array('titulo' => 'Qtd. Comprar', 'tipo' => 'numero'),
			'D009_Flag_Cotar' => array('titulo' => 'Cotar', 'tipo' => 'texto'),
			'D009_Flag_Comprar' => array('titulo' => 'Comprar', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Gera o filtro e acrescenta ao $extraTotal
		$extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal, false, false, true);
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 100);
		
		/**
		* Botões (Versão Acima do GRID)
		*/
		//$extra_ = urlencode(base64_encode($extra));
		$sql_ = urlencode(base64_encode($geradoSql));
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		

		if (!personalizacaoTotais()) { 
			/**
			 * Totalizadores
			 */
			$itens = $g['sqlAuto']->pegarSqlCampo("count(distinct D009_Id)", $from, $extraTotal);
			$total = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T001_Valor_Preco_Unitario*if(D009_Quantidade_Comprar>0,D009_Quantidade_Comprar,if(D009_Quantidade_Comprar_Calculada>0,D009_Quantidade_Comprar_Calculada,0)))", $from, $extraTotal));
			
			echo <<<EOT
				<div class="buttonsBottomTop">
					<!--<span class="item" style="text-align:right">Itens<br><input type="text" value="{$itens}" class="whiteBg" id="" size="11" readonly></span>-->
					<span class="item" style="text-align:right">Valor compra R$<br><input type="text" value="{$total}" class="whiteBg" id="" size="11" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		$novoIdTelaForm = uniqid();

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
		$primeiraLinhaGridHead = <<<EOT
			<tr>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Produto</th>
				<th colspan="7" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Estoque</th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Últimas vendas</th>
				<th colspan="1" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Importações</th>
			    <th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;">Projeção venda</th>
				<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center; z-index: 2;"></th>
EOT;
		$g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	

	case 'est009GridPrevisaoDI':
		global $g;
		$D049_Id = $r_acaoId;

		$dataInicial = date('Y-m-1', $r_mes);
		$dataFinal = date('Y-m-t', $r_mes);
		
	    $sql = mysql_query("select D009_Id from D009 where D009_D049_Id='{$D049_Id}' and D009_C004_Id='1'");
	    $row = mysql_fetch_array($sql);
	    
		$diasMP=$g['conf']->ini('diasMedioPrazo');
		$data = strtotime("+$diasMP day", strtotime(date("Y-m-d")));
		$dataMP = date("Y-m-d", $data);	   

		$sql = mysql_query("");
	    
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
			array('colunaValor' => 'T075_Numero_DI', 'colunaTitulo' =>'DI'),
			array('colunaValor' => 'T075_Ordem_Compra', 'colunaTitulo' =>'PO'),
			array('colunaValor' => 'T075_Id','chavePrimaria'=>true, 'colunaExibir' => false),
			array('colunaValor' => 'D009_Id','colunaExibir' => false),
			array('colunaValor' => 'T055_Id','colunaExibir' => false),
			array('colunaValor' => 'T075_Data_Previsao', 'colunaTitulo' =>'Prev. Chegada', 'callback'=>'gCorrigeData', 'colunaAlinhar' => 'center'),
			//array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' =>'Produto'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' =>'Marca'),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' =>'Fornecedor'),		
			array('colunaValor' => 'T055_Quantidade', 'colunaTitulo' =>'Qt. DI', 'callback'=>'gCorrigeNumero'),
			array('colunaValor' => 'D009_Quantidade_MP(D009_Id, T075_Id)', 'colunaTitulo' =>'Qt. Liq.', 'colunaCasasDecimais' => '2'),
			//array('colunaValor' => 'QtePedidos','colunaTipo'=>'livre', 'colunaTitulo' =>'Qt. Pedidos', 'callback'=>'QtePedidos'),*/
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
		        and T075_Data_Previsao <= '{$dataFinal}'
				and T075_Data_Previsao >= '{$dataInicial}'
				and T075_Flag_MP = 0
		        and T055_D009_Id='{$row['D009_Id']}' 
		        and ifnull(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
		        order by T075_Data_Previsao asc
EOT;

				//and T075_Data_Previsao<='{$dataMP}'
				//and T075_Data_Previsao>0

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

	case 'est009GridProdutosHistoricoSemLocacao':
		/**
		 * Definição do grid tabela
		 */
		$grid = array(
		    array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
		    array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049 Id', 'chavePrimaria' => false, 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'CampoCadastro', 'colunaTitulo' => '', 'colunaTipo' => 'livre', 'callback' => 'editarHistorico', 'colunaAlinhar' => 'center', 'style' => 'width: 16px;', 'colunaExibirNivel' => 2),
		    array('colunaValor' => 'T001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:11px'),
		    array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código'),
		    array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'font-weight:normal', 'colunaEditarOculto' => true, 'colunaEditarNivel' => 2),
		    array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaExibir' => true),
  	        array('colunaValor' => 'T001_Data_Lancamento', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'colunaDimensao' => 'S'),
            array('colunaValor' => 'C004_Nome_Abreviado', 'colunaTitulo' => 'Empresa', 'colunaExibir' => false, 'colunaExibirNivel' => 2),
            array('colunaValor' => 'substr(if((T001_Flag_Operacao=\'E\' or T001_Flag_Operacao=\'B\') and T001_T008_Id is null,Forn.D024_Nome_Empresa,Cli.D024_Nome_Empresa),1,20) as Cliente', 'colunaTitulo' => 'Cliente/Fornecedor'),
            array('colunaValor' => 'T001_Descricao_Operacao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T001_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => 3, 'colunaAlinhar' => 'right'),
  			array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 0)),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Loja', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
		    array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Fora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueFora'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Similar', 'colunaTitulo' => 'Similar', 'callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'D009_Quantidade_OC', 'colunaTitulo' => 'O.C.', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),		    
		);
		$from = 'T001';

		$extra = <<<EOT
			left join D024 as Forn on Forn.D024_Id=T001_D024_Id
			left join D024 as Cli on Cli.D024_Id=T001_D024_Id_Cliente
			LEFT JOIN D009 ON D009_Id = T001_D009_Id 
            LEFT JOIN D049 ON D049_Id = D009_D049_Id 
            LEFT JOIN D001 ON D001_Id = D049_D001_Id 
            LEFT JOIN C004 ON C004_Id = D009_C004_Id
            LEFT JOIN D082 ON D049_D082_Id=D082_Id
			LEFT JOIN D037 ON D001_D037_Id=D037_Id
            WHERE (IFNULL(T001_T066_Id, 0) <= 0 or IFNULL(T001_D004_Id, 0) <= 0)
            AND T001_Data_lancamento > C004_Data_Inicio_Sistema
            AND T001_Flag_Operacao != 'C'
            AND T001_Flag_Operacao != 'N'
            AND T001_Quantidade > 0
            AND D009_C004_Id = '{$g['empresaAtual']}'
            GROUP BY T001_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
			//'D131_Tipo' => array('titulo' => 'Tipos', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'numero'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'T001_Data_Lancamento' => array('titulo' => 'Data', 'tipo' => 'data'),
			'T001_Descricao_Operacao' => array('titulo' => 'Descrição', 'tipo' => 'texto')
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
					   abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(D001_Id) + '&tabela=$from&D024_Id=' + encodeURIComponent(D024_Id), [1000,635]);
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
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'est009GridComprasEstoqueModelo3':

		$filtroObrigatorio = true;

		$grid = array(
			array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T006_Id', $g['divId'])),
			array('colunaValor' => 'T006_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Produto', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T006_T005_Id', 'colunaTitulo' => 'T006_T005_Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido'),
			array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data Emissão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Razão Social', 'colunaExibir' => false),
			array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia', 'colunaExibir' => false),
			array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Produto'),
			array('colunaValor' => 'T006_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D082_Marca', 'colunaTitulo' => 'Marca'),
			array('colunaValor' => 'T006_Classificacao_Fiscal', 'colunaTitulo' => 'NCM', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T006_Flag_Situacao_Tributaria', 'colunaTitulo' => 'ST', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center' ),
			array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'colunaTitulo' => 'Preço Unitário', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_Total_Preco_Sem_Desconto', 'colunaTitulo' => 'Preço Total', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Aliquota_ICMS', 'colunaTitulo' => '% ICMS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Aliquota_IPI', 'colunaTitulo' => '% IPI', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Aliquota_PIS', 'colunaTitulo' => '% PIS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Aliquota_COFINS', 'colunaTitulo' => '% COFINS', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_St_Va', 'colunaTitulo' => 'MVA', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T006_Valor_ICMS', 'colunaTitulo' => 'ICMS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_IPI', 'colunaTitulo' => 'IPI R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_PIS', 'colunaTitulo' => 'PIS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_COFINS', 'colunaTitulo' => 'COFINS R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'T006_Valor_ICMS_Substituicao_Retencao', 'colunaTitulo' => 'ST R$', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'right'),
			array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T006";
		
		$extra = <<<EOT
		            left join D009 on D009_Id=T006_D009_Id
		            left join D049 on D049_Id=D009_D049_Id
		            left join D082 on D082_Id=D049_D082_Id
		            left join D001 on D001_Id=D049_D001_Id
		            left join D006 on D006_Id=T006_D006_Id
		            left join D037 on D037_Id=T006_D037_Id
		            left join T005 on T005_Id=T006_T005_Id
		            left join D024 on D024_Id=T005_D024_Id
		            left join C008 on C008_Id=D001_C008_Id
		            left join D021 on D021_Id=T005_D021_Id
		            left join C007 as Vendedor on Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno      
                    where (D006_Flag_Venda_Compra_Outros='V')
                    AND (T006_C004_Id='{$g['empresaAtual']}')
                    AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null)
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
						var T006_T005_Id = id[3].replace(/T006_T005_Id-/, '');
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pedido - ID: ' + T006_T005_Id, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(T006_T005_Id) + '&tabela=T005', [950,615]);            
				   });
				   $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
         * Botões Almir
         */
        $botoes = array(
			);
		echo Botoes::processar($botoes);
		
		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'T005_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'T006_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D024_Nome_Fantasia,D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'D006_Codigo_CFOP' => array('titulo' => 'CFOP', 'tipo' => 'data'),
			'T007_Data_Emissao' => array('titulo' => 'Emissão NF', 'tipo' => 'data'),
			'D082_Marca' => array('titulo' => 'Ordem de compra', 'tipo' => 'texto'),
			'T005_Ordem_Compra' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'C004_Nome_Abreviado' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
			'D001_Codigo_Produto' => array('titulo' => 'Cod Produto', 'tipo' => 'texto'),
			'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
			'D009_Quantidade_Estoque_Liquido' => array('titulo' => 'Estoque Liquido', 'tipo' => 'texto'),
			'T005_Status_Pedido(T005_Flag_Status[[virgula]]1)' => array('titulo' => 'Status Pedido', 'tipo' => 'texto'),
			'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
			'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
			'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
			'(SELECT GROUP_CONCAT(DISTINCT(D082_Marca))
							FROM T006 
							LEFT JOIN D009 ON D009_Id = T006_D009_Id
							LEFT JOIN D049 ON D049_Id = D009_D049_Id
							LEFT JOIN D082 ON D082_Id = D049_D082_Id
							WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Marca', 'tipo' => 'texto'),
			'(SELECT GROUP_CONCAT(DISTINCT(C008_Tipo))
							FROM T006 
							LEFT JOIN D009 ON D009_Id = T006_D009_Id
							LEFT JOIN D049 ON D049_Id = D009_D049_Id
							LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                                        LEFT JOIN C008 ON C008_Id = D001_C008_Id
							WHERE T006_T005_Id=T005_Id)' => array('titulo' => 'Tipo Produto', 'tipo' => 'texto'),
		);
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		
		if (!personalizacaoTotais()) { 
		}


		$novoIdTelaForm = uniqid();

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
