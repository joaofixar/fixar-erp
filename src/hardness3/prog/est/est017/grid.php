<?php
namespace hardness;
// Carrega funções callback
require_once('prog/est/est017/grid_func-callback.php');

// Carrega funções do ajax
require_once('prog/est/est017/grid_func-js.php');

switch($g['r'][3]){
	case 'est017GridAnaliseCusto':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T004_D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaExibir' => false),
            array('colunaValor' => 'T004_Item', 'colunaTitulo' => 'It', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'gContador', 'colunaTipo'  => 'livre', 'colunaValor' => '', 'callback' => 'gContador', 'style' => 'width:1%;'),
            array('colunaValor' => 'campoAnaliseCustoFinalizado', 'colunaTipo'  => 'livre', 'colunaValor' => '', 'callback' => 'campoAnaliseCustoFinalizado', 'style' => 'width:1%;'),
            array('colunaValor' => 'T003_Data_Emissao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);font-weight:bold'),
            array('colunaValor' => 'T004_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'T004_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;font-weight:bold'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Valor_Custo_Unitario', 'colunaTitulo' => 'Custo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal'),
            array('colunaValor' => 'T004_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:normal'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_OP', 'colunaTitulo' => 'OP', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', 'T004_D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'T004_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:bold;max-width:180px;white-space:normal;min-width:180px'),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
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
			LEFT JOIN D024 as Cliente ON T003_D024_Id=Cliente.D024_Id
			LEFT JOIN T219 ON T004_Id=T219_T004_Id
			LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
			LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
			LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
			LEFT JOIN T241 ON T241_D001_Id=D001_Id
			LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
			WHERE T004_Flag_Comprar = 'S'
			AND T003_Flag_Status_Orcamento != 'V'
			AND T003_Flag_Perdido != 'S'
			AND D009_C004_Id='{$g['empresaAtual']}'
			AND T006_Id IS NULL
			AND D049_Flag_Tipo = 'PD'
			GROUP BY T004_Id
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
            )
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

	case 'est017GridProducaoEmergencia':
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
            array('colunaValor' => 'substr(vendedor.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-amarelo-claro);font-weight:bold'),
            array('colunaValor' => 'T006_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'center', 'style' => 'font-weight:normal;width:1%', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D049_Modelo', 'colunaTitulo' => 'Sub-item', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Codigo_Substituto', 'colunaTitulo' => 'Cód.Cli', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'style' => 'max-width:300px;min-width:300px;white-space:normal;font-weight:bold', 'callback' => 'est017DescricaoProduto'),
            array('colunaValor' => 'substr(D082_Marca,1,12)', 'colunaTitulo' => 'Marca'),
            array('colunaValor' => 'D049_Flag_Tipo', 'colunaTitulo' => 'TP', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Data_Alteracao_Custo', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Data_Ultima_Venda', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
		    array('colunaValor' => 'D009_Flag_Custo_Utilizado', 'colunaTitulo' => 'D009 Id', 'chavePrimaria' => false, 'colunaExibir' => false),
            array('colunaValor' => 'infoCustoVendaProduto', 'colunaTitulo' => '', 'callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'UN', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T006_Quantidade', 'colunaTitulo' => 'Qtde', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:bold'),
            array('colunaValor' => 'T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OP as Falta', 'colunaTitulo' => 'Falta', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho);font-weight:bold'),
            array('colunaValor' => '(T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OP) AS Saldo', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Flag_Gerar_Producao', 'colunaTitulo' => '', 'callback' => 'checkboxGerarOrdemProducao', 'colunaAlinhar' => 'center', 'colunaEditarTodos' => 'S', 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N', 'colunaEditarUsuario' => 'T006_C007_Id_Comprador', 'style' => 'width: 1%'),
            array('colunaValor' => 'T218_Grupo', 'callback' => 'abrirJanela', 'colunaTitulo' => 'Cot.', 'callbackParameter' => array('Produtos X Fornecedor', '/est/est015/content/est015ContentProdutosVsFornecedor/', '', '1000,583', false, false, array('T218_Grupo' => 'T218_Grupo')), 'colunaExibir' => false),
            array('colunaValor' => 'T224_Id', 'colunaTitulo' => 'O.C.', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordem de Compra', '/est/est016/content/est016content02/', 'T224_Id', '990,605'), 'colunaExibir' => false),
            array('colunaValor' => 'T219_T218_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T219_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T006_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Reserv', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
            array('colunaValor' => 'D009_Quantidade_Estoque', 'colunaTitulo' => 'Estoq<br>Físico', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Liquido as Pedido', 'colunaTitulo' => 'Estoq<br>Líquido', 'callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => false, 'valorMenor' => 'T004_Quantidade')),
            //array('colunaValor' => 'D009_Quantidade_Estoque_Liquido', 'colunaTitulo' => 'Estoq<br>Líquido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Loja', 'colunaTitulo' => 'Estoq<br>Loja', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2', 'style' => 'color: var(--BGVermelho)'),
		    array('colunaValor' => 'T006_Quantidade_OP', 'colunaTitulo' => 'OP', 'colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Produção', '/ven/ven001/content/ven001contentProdutoOP/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D009_Quantidade_Estoque_Fora', 'colunaTitulo' => 'Estoq<br>Fora', 'callback' => 'estoqueFora', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('colunaValor' => 'D049_Quantidade_Embalagem_Compra', 'colunaTitulo' => 'Emba<br>lagem', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D049_Quantidade_Minima_Compra', 'colunaTitulo' => 'Qtde<br>Min.', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeNumero', 'colunaCasasDecimais' => '2', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observacao', 'style' => 'background-color: var(--celula-amarelo-claro);font-weight:bold;max-width:180px;white-space:normal;min-width:180px'),
            //array('colunaValor' => 'ultimosFornecedores', 'colunaTitulo' => 'Fornecedores', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%', 'callback' => 'colunaFornecedores', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'substr(comprador.C007_Primeiro_Nome,1,10)', 'colunaTitulo' => 'Comprador', 'colunaAlinhar' => 'left', 'style' => 'font-weight:normal;width:1%;background-color: var(--celula-verde);font-weight:bold', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D049_Id', 'colunaTitulo' => 'D049_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
            array('colunaValor' => 'T006_Valor_Preco_Unitario', 'colunaTitulo' => 'Preço', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '4', 'colunaExibir' => false),
            array('colunaValor' => 'T003_Id', 'colunaTitulo' => 'Orç', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', 'T003_Id', '990,605')),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Ped', 'colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Pedido', '/ven/ven002/content/ven002content02/', 'T005_Id', '990,605')),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data pedido', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'substr(D024_Nome_Empresa,1,25)', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left'),
            array('colunaValor' => 'D001_Flag_Pre_Cadastro', 'colunaTitulo' => 'Pre cadastro', 'colunaExibir' => false),
            array('colunaValor' => 'T006_C007_Id_Comprador', 'colunaExibir' => false),
            array('colunaValor' => 'marcou.C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => 'Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day)', 'colunaTitulo' => 'Previsão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
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
			AND (if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)+T006_Quantidade_OP) < T006_Quantidade 
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
					<button type="button" id="est017GerarOrdemProducao">Gerar Ordem de Produção</button>
					<button type="button" id="est017AtualizarEstoqueProducao">Atualizar estoque</button>
				</div>
EOT;
		}*/

		// Botões
		$botoes = array(
			array('titulo' => 'Gerar Ordem de Produção', 'id' => 'est017GerarOrdemProducao'),
			array('titulo' => 'Atualizar estoque', 'id' => 'est017AtualizarEstoqueProducao'),
		);
		echo Botoes::processar($botoes);

		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017GerarOrdemProducao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est017/grid_func-ajax/est017VerificarGerarOrdemProducao/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							var \$buttons = {
								"Gerar Ordem de Produção": function() {
									var numeroCotacao = $(this).find('input').val();
									$('#{$g['divId']}').showLoading();
									$.getJSON('/est/est017/grid_func-ajax/est017GerarOrdemProducao/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
										$('#{$g['divId']}').hideLoading();
										if (request.code) {
											var \$buttons = {
												"Fechar": function() {
													var T255_Id = request.T255_Id;
													$(this).dialog("destroy").remove();
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Ordem de Produção', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent(T255_Id), [990,605]);
													divRefresh('{$g['divIdParent']}', true);
												} 
											};
											dialogConfirm('Ok', 'Ordem de produção gerada com sucesso', \$buttons);
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
							dialogConfirm('Gerar Ordem de Produção', request.data, \$buttons);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
						$('#{$g['divId']}').hideLoading();
					});
				});

				$('#est017AtualizarEstoqueProducao').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est017/grid_func-ajax/processaCalcularEstoqueEmergencia/?ajax=true&sql=' + encodeURIComponent($('#{$g['divId']}').find('.divToScroll').attr('gridsql')) + '&callback=?', function(request) {
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

	case 'est017GridProdutosOrdemProducao':
		$rowT255 = mysql_fetch_assoc(mysql_query("SELECT * FROM T255 WHERE T255_Id = '{$r_acaoId}'"));
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T256_Id', $g['divId'])),
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade-T256_Quantidade_Produzida', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Data_Previsao_Entrega', 'colunaTitulo' => 'Previsão entrega', 'style' => 'width:1%;', 'callback' => 'gCorrigeData', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Observacao_Producao', 'colunaTitulo' => 'Observação produção', 'style' => 'width:1%;', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Prazo', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day)', 'colunaTitulo' => 'Previsão', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
        );

		$from = 'T256';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D037 on D037_Id=D001_D037_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			WHERE T256_T255_Id = '{$r_acaoId}'
			GROUP BY T256_Id
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

		$enabled = $rowT255["T255_Data_Hora_Baixa_Materiais"] == "0000-00-00 00:00:00";
		/*
		if (!personalizacaoBotoes()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est017IncluirProdutoOP" {$disabled}>Incluir</button>
					<button type="button" id="est017AtualizarEngenhariaOP" {$disabled}>Atualizar engenharia</button>
				</div>
EOT;
		}
		*/

		// Botões
		$botoes = array(
			//array('titulo' => 'Teste 2', 'id' => 'btnIdTeste', 'janelaUrl' => '/cad/cad067/content/cad067ContentPerifericos/', 'janelaTamanho' => '[800,600]'),
			array('titulo' => 'Incluir', 'id' => 'est017IncluirProdutoOP', 'exibirCallback' => 'return ' . $enabled . ';'),
			array('titulo' => 'Atualizar engenharia', 'id' => 'est017AtualizarEngenhariaOP','exibirCallback' => 'return ' . $enabled . ';'),
		);
		echo Botoes::processar($botoes);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017IncluirProdutoOP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir produto', '/est/est017/content/est017ContentProdutoProducao/', '&T255_Id=' + encodeURIComponent("{$r_acaoId}"), [400,150]);
				});

				$('#est017AtualizarEngenhariaOP').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est017/form_func-ajax/atualizarComposicaoProdutos/?ajax=true&T255_Id=' + encodeURIComponent("{$r_acaoId}") + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}', true);
									dialogConfirm('Ok', 'Componentes atualizados com sucesso!');
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Atenção', 'Deseja atualizar a engenharia dos produtos igual ao cadastro de produtos?', \$buttons);
				});
			} </script></div>
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Produto', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [800,500]);
			});
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

	case 'est017GridOrdemProducaoAberto':
				/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'est017CampoExcluirOrdemProducao'),
            array('colunaValor' => 'cancelar', 'colunaTipo' => 'livre', 'colunaTitulo' => 'cancelar', 'callback' => 'est017CampoCancelarOrdemProducao'),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T255_Total_Itens', 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado",IF(T255_Flag_Status="98","Cancelado","Aberto"))', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag Status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct D024_Nome_Empresa separator "<br>")', 'colunaTitulo' => 'Cliente', 'style' => 'width:1%;', 'colunaExibir' => true),
        );

		$from = 'T255';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T255_C007_Id
			LEFT JOIN T256 on T256_T255_Id=T255_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status != '99'
			GROUP BY T255_Id
EOT;
		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'est017IncluirOrdemProducao'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T255_Id' => array('titulo' => 'Ordem produção', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/*if (!personalizacaoBotoes()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est017IncluirOrdemProducao">Incluir</button>
				</div>
EOT;
		}*/

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017IncluirOrdemProducao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Ordem de produção', '/est/est017/content/est017ContentOrdemProducao/', '', [550,450]);
				});
			} </script></div>
EOT;
		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       //return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
		    }
		}	

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de produção', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T255_Id']) + '&tabela={$from}', [990,605]);
			});
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

	case 'est017GridOrdemProducaoFinalizado':
				/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'est017CampoExcluirOrdemProducao'),
            array('colunaValor' => 'cancelar', 'colunaTipo' => 'livre', 'colunaTitulo' => 'cancelar', 'callback' => 'est017CampoCancelarOrdemProducao'),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'T255_Total_Itens', 'colunaTitulo' => 'Itens', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T255_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'T255_Quantidade_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'colunaCasasDecimais' => '3', 'colunaAlinhar' => 'right'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado",IF(T255_Flag_Status="98","Cancelado","Aberto"))', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag Status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'group_concat(distinct D024_Nome_Empresa separator "<br>")', 'colunaTitulo' => 'Cliente', 'style' => 'width:1%;', 'colunaExibir' => true),
        );

		$from = 'T255';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T255_C007_Id
			LEFT JOIN T256 on T256_T255_Id=T255_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status = '99'
			GROUP BY T255_Id
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
            'T255_Id' => array('titulo' => 'Ordem produção', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       return 'linhaAzul';
		    }
		}	

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T255_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Ordem de produção', '/est/est017/content/est017ContentOrdemProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [990,605]);
			});
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

	case 'est017GridProducaoProdutosAberto':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado",IF(T255_Flag_Status="98","Cancelado","Aberto"))', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T256_Data_Previsao_Entrega', 'colunaTitulo' => 'Previsão entrega', 'style' => 'width:1%;', 'callback' => 'gCorrigeData', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Observacao_Producao', 'colunaTitulo' => 'Observação produção', 'style' => 'width:1%;', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Prazo', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day)', 'colunaTitulo' => 'Previsão pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação Pedido', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'font-weight:normal;background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),            
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observação Compra', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'font-weight:normal;background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),            
        );

		$from = 'T256';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN T001 ON T257_Id=T001_T257_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D037 on D037_Id=D001_D037_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status != '99'
			GROUP BY T256_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
		    }
		}	

		/**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Processar estoque', 'id' => 'est017ProcessarProducao'),
		);
		echo Botoes::processar($botoes);

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),

        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

		$sql_ = str_replace("LIMIT 0,100", "", $geradoSql);
		$sql_ = explode("FROM T256", $sql_);
		$sql_ = gInsertExtraWhere($sql_[1], "T257_Id > 0 AND T001_Id IS NULL");
		$sql_ = str_replace("GROUP BY T256_Id", "GROUP BY T257_Id", $sql_);
		$sql_ = base64_encode($sql_);

		/*if (!personalizacaoBotoes()) { 
			echo <<<EOT
				<div class="buttonsBottomTop">
					<button type="button" id="est017ProcessarProducao">Processar estoque</button>
				</div>
EOT;
		}*/

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#est017ProcessarProducao').unbind('click').bind('click', function(data) {
					$('#{$g['divId']}').showLoading();
					$.getJSON('/est/est017/grid_func-ajax/est017VerificarProcessarProducao/?ajax=true&extra=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
						$('#{$g['divId']}').hideLoading();
						if (request.code) {
							abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processar Produção', '/est/est017/content/est017ContentProcessarProducao/', '&extra=' + encodeURIComponent("{$sql_}"), [700,650]);
						} else {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							dialogConfirm('Erro', request.data, \$buttons);
						}
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

	case 'est017GridProducaoProdutosFinalizado':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Entrada_Estoque', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'IF(T255_Flag_Status="99","Finalizado",IF(T255_Flag_Status="98","Cancelado","Aberto"))', 'colunaTitulo' => 'Status', 'style' => 'width:1%;'),
            array('colunaValor' => 'T255_Flag_Status', 'colunaTitulo' => 'Flag status', 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T256_Data_Previsao_Entrega', 'colunaTitulo' => 'Previsão entrega', 'style' => 'width:1%;', 'callback' => 'gCorrigeData', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_Observacao_Producao', 'colunaTitulo' => 'Observação produção', 'style' => 'width:1%;', 'colunaEditar' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'T005_Id', 'colunaTitulo' => 'Pedido', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T005_Data_Emissao', 'colunaTitulo' => 'Data pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao_Entrega', 'colunaTitulo' => 'Prazo', 'colunaAlinhar' => 'center', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'Date_add(T005_Data_Emissao, interval T006_Observacao_Entrega day)', 'colunaTitulo' => 'Previsão pedido', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'colunaAlinhar' => 'left', 'style' => 'background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T006_Observacao', 'colunaTitulo' => 'Observação Pedido', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'font-weight:normal;background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),            
            array('colunaValor' => 'T006_Observacao_Compra', 'colunaTitulo' => 'Observação Compra', 'colunaAlinhar' => 'left', 'colunaEditar' => false, 'style' => 'font-weight:normal;background:var(--celula-amarelo-claro)', 'colunaEditarJSCallback' => 'colunaEditarCallbackJSReload'),            
        );

		$from = 'T256';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN T001 ON T257_Id=T001_T257_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN D037 on D037_Id=D001_D037_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status = '99'
			GROUP BY T256_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
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
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

	case 'est017GridProducaoComposicaoAberto':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009PD.D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001PD.D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001PD.D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001PD.D001_Id', '1000,583')),
            array('colunaValor' => 'D001PD.D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'D001CP.D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001CP.D001_Id', '1000,583')),
            array('colunaValor' => 'D001CP.D001_Descricao_Produto', 'colunaTitulo' => 'Composição'),
            array('colunaValor' => 'T261_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T261_Quantidade*T256_Quantidade', 'colunaTitulo' => 'Quantidade Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'CONCAT(T255_Id, " - ", DATE_FORMAT(T255_Data_Emissao,"%d/%m/%Y")) as Op', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001PD.D001_Codigo_Produto, " - ", D001PD.D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001CP.D001_Codigo_Produto, " - ", D001CP.D001_Descricao_Produto) as Composicao', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(T255_Id, " - ", D001PD.D001_Codigo_Produto, " - ", D001PD.D001_Descricao_Produto, " - ", T256_Quantidade) as OpProduto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
        );

		$from = 'T261';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T256 ON T256_Id=T261_T256_Id
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN T001 ON T257_Id=T001_T257_Id
			LEFT JOIN D009 as D009PD on D009PD.D009_Id=T256_D009_Id
			LEFT JOIN D049 as D049PD on D049PD.D049_Id=D009PD.D009_D049_Id
			LEFT JOIN D001 as D001PD on D001PD.D001_Id=D049PD.D049_D001_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN D001 AS D001CP ON D001CP.D001_Id = T261_D001_Id_Composicao
			LEFT JOIN D049 AS D049CP ON D001CP.D001_Id = D049CP.D049_D001_Id
			LEFT JOIN D009 AS D009CP ON D049CP.D049_Id = D009CP.D009_D049_Id
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND D009CP.D009_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status != '99'
			GROUP BY T261_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
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
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001PD.D001_Codigo_Produto,D001CP.D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
			'D001PD.D001_Descricao_Produto,D001CP.D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => true, 'campos' => array(
                	array('title' => 'OP', 'campoAgrupar' => 'Op', 'campoMostrar' => 'Op', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'T255_Data_Emissao')),
                	array('title' => 'PRODUTO', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'Produto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001PD.D001_Codigo_Produto', 'D001PD.D001_Descricao_Produto')),
                	array('title' => 'COMPOSIÇÃO', 'campoAgrupar' => 'Composicao', 'campoMostrar' => 'Composicao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001CP.D001_Codigo_Produto', 'D001CP.D001_Descricao_Produto')),
                	array('title' => 'OP + PRODUTO', 'campoAgrupar' => 'OpProduto', 'campoMostrar' => 'OpProduto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'D001PD.D001_Codigo_Produto', 'D001PD.D001_Descricao_Produto', 'T256_Quantidade')),
                	array('title' => 'DATA', 'campoAgrupar' => 'T255_Data_Emissao', 'campoMostrar' => 'T255_Data_Emissao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Data_Emissao')),
              	),
            ),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

	case 'est017GridProducaoComposicaoFinalizado':
				/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009PD.D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001PD.D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001PD.D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001PD.D001_Id', '1000,583')),
            array('colunaValor' => 'D001PD.D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'D001CP.D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001CP.D001_Id', '1000,583')),
            array('colunaValor' => 'D001CP.D001_Descricao_Produto', 'colunaTitulo' => 'Composição'),
            array('colunaValor' => 'T261_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T261_Quantidade*T256_Quantidade', 'colunaTitulo' => 'Quantidade Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'CONCAT(T255_Id, " - ", DATE_FORMAT(T255_Data_Emissao,"%d/%m/%Y")) as Op', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001PD.D001_Codigo_Produto, " - ", D001PD.D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001CP.D001_Codigo_Produto, " - ", D001CP.D001_Descricao_Produto) as Composicao', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(T255_Id, " - ", D001PD.D001_Codigo_Produto, " - ", D001PD.D001_Descricao_Produto, " - ", T256_Quantidade) as OpProduto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
        );

		$from = 'T261';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T256 ON T256_Id=T261_T256_Id
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN T001 ON T257_Id=T001_T257_Id
			LEFT JOIN D009 as D009PD on D009PD.D009_Id=T256_D009_Id
			LEFT JOIN D049 as D049PD on D049PD.D049_Id=D009PD.D009_D049_Id
			LEFT JOIN D001 as D001PD on D001PD.D001_Id=D049PD.D049_D001_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			LEFT JOIN D001 AS D001CP ON D001CP.D001_Id = T261_D001_Id_Composicao
			LEFT JOIN D049 AS D049CP ON D001CP.D001_Id = D049CP.D049_D001_Id
			LEFT JOIN D009 AS D009CP ON D049CP.D049_Id = D009CP.D009_D049_Id
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND D009CP.D009_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status = '99'
			GROUP BY T261_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
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
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001PD.D001_Codigo_Produto,D001CP.D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001PD.D001_Descricao_Produto,D001CP.D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => true, 'campos' => array(
                	array('title' => 'OP', 'campoAgrupar' => 'Op', 'campoMostrar' => 'Op', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'T255_Data_Emissao')),
                	array('title' => 'PRODUTO', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'Produto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001PD.D001_Codigo_Produto', 'D001PD.D001_Descricao_Produto')),
                	array('title' => 'COMPOSIÇÃO', 'campoAgrupar' => 'Composicao', 'campoMostrar' => 'Composicao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001CP.D001_Codigo_Produto', 'D001CP.D001_Descricao_Produto')),
                	array('title' => 'OP + PRODUTO', 'campoAgrupar' => 'OpProduto', 'campoMostrar' => 'OpProduto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'D001PD.D001_Codigo_Produto', 'D001PD.D001_Descricao_Produto', 'T256_Quantidade')),
                	array('title' => 'DATA', 'campoAgrupar' => 'T255_Data_Emissao', 'campoMostrar' => 'T255_Data_Emissao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Data_Emissao')),
              	),
            ),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

	case 'est017GridProducaoProcessosAberto':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'T262_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'D079_Nome', 'colunaTitulo' => 'Máquina', 'colunaAlinhar' => 'left', 'style' => 'width:1%;', 'colunaDimensao' => true),
            array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => '(D079_Custo_Minuto*T262_Lead_Time)', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => 'D078_Nome', 'colunaTitulo' => 'Função', 'colunaAlinhar' => 'left', 'style' => 'width:1%;', 'colunaDimensao' => true),
            array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => '(D078_Custo_Minuto*T262_Lead_Time)', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => 'T262_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
            array('colunaValor' => 'T262_Lead_Time', 'colunaTitulo' => 'Lead time', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '0', 'colunaMetrica' => true),
            array('colunaValor' => 'T262_Numero_Funcionarios', 'colunaTitulo' => 'Funcionários', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),

            array('colunaValor' => 'CONCAT(T255_Id, " - ", DATE_FORMAT(T255_Data_Emissao,"%d/%m/%Y")) as Op', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001_Codigo_Produto, " - ", D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
        );

		$from = 'T262';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T256 ON T256_Id=T262_T256_Id
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN D078 on D078_Id=T262_D078_Id
			LEFT JOIN D079 on D079_Id=T262_D079_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status != '99'
			GROUP BY T262_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
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
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D079_Nome' => array('titulo' => 'Máquina', 'tipo' => 'texto'),
            'D078_Nome' => array('titulo' => 'Função', 'tipo' => 'texto'),
            'T262_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => true, 'campos' => array(
                	array('title' => 'OP', 'campoAgrupar' => 'Op', 'campoMostrar' => 'Op', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'T255_Data_Emissao')),
                	array('title' => 'PRODUTO', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'Produto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001_Codigo_Produto', 'D001_Descricao_Produto')),
                	array('title' => 'DATA', 'campoAgrupar' => 'T255_Data_Emissao', 'campoMostrar' => 'T255_Data_Emissao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Data_Emissao')),
                	array('title' => 'MÁQUINA', 'campoAgrupar' => 'D079_Nome', 'campoMostrar' => 'D079_Nome', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D079_Nome', 'D078_Custo_Minuto', '(D078_Custo_Minuto*T262_Lead_Time)')),
                	array('title' => 'FUNÇÃO', 'campoAgrupar' => 'D078_Nome', 'campoMostrar' => 'D078_Nome', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D078_Nome', 'D079_Custo_Minuto', '(D079_Custo_Minuto*T262_Lead_Time)')),
              	),
            ),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

	case 'est017GridProducaoProcessosFinalizado':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T256_Id', 'colunaTitulo' => 'OP', 'chavePrimaria' => true, 'style' => 'width:1%;', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'D009_Id', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T255_Data_Emissao', 'colunaTitulo' => 'Data OP', 'callback' => 'gCorrigeData', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaDimensao' => true),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T256_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),

            array('colunaValor' => 'T262_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),
            array('colunaValor' => 'D079_Nome', 'colunaTitulo' => 'Máquina', 'colunaAlinhar' => 'left', 'style' => 'width:1%;', 'colunaDimensao' => true),
            array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => '(D079_Custo_Minuto*T262_Lead_Time)', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => 'D078_Nome', 'colunaTitulo' => 'Função', 'colunaAlinhar' => 'left', 'style' => 'width:1%;', 'colunaDimensao' => true),
            array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => '(D078_Custo_Minuto*T262_Lead_Time)', 'colunaTitulo' => 'Custo Total', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '2', 'colunaMetrica' => true),
            array('colunaValor' => 'T262_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left', 'style' => 'width:1%;'),
            array('colunaValor' => 'T262_Lead_Time', 'colunaTitulo' => 'Lead time', 'colunaAlinhar' => 'right', 'style' => 'width:1%;', 'colunaCasasDecimais' => '0', 'colunaMetrica' => true),
            array('colunaValor' => 'T262_Numero_Funcionarios', 'colunaTitulo' => 'Funcionários', 'colunaAlinhar' => 'center', 'style' => 'width:1%;'),

            array('colunaValor' => 'CONCAT(T255_Id, " - ", DATE_FORMAT(T255_Data_Emissao,"%d/%m/%Y")) as Op', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'CONCAT(D001_Codigo_Produto, " - ", D001_Descricao_Produto) as Produto', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
        );

		$from = 'T262';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T256 ON T256_Id=T262_T256_Id
			LEFT JOIN T255 on T255_Id=T256_T255_Id
			LEFT JOIN T257 ON T256_Id=T257_T256_Id
			LEFT JOIN D078 on D078_Id=T262_D078_Id
			LEFT JOIN D079 on D079_Id=T262_D079_Id
			LEFT JOIN D009 on D009_Id=T256_D009_Id
			LEFT JOIN D049 on D049_Id=D009_D049_Id
			LEFT JOIN D001 on D001_Id=D049_D001_Id
			LEFT JOIN T006 on T006_Id=T256_T006_Id
			LEFT JOIN T005 on T005_Id=T006_T005_Id
			LEFT JOIN D024 on D024_Id=T005_D024_Id
			LEFT JOIN C007 on C007_Id=T005_C007_Id_Vendedor_Interno
			WHERE T255_C004_Id = '{$g['empresaAtual']}'
			AND T255_Flag_Status = '99'
			GROUP BY T262_Id
EOT;

		/**
		* Função que define classes CSS para cada linha do grid
		*/
		function gridLinha($array) {
		    if ($array['todosCampos']['T255_Flag_Status']=='99') {
		       return 'linhaCinza';
		    } else {
		       //return 'linhaAzul';
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
            'T255_Id' => array('titulo' => 'OP', 'tipo' => 'numero'),
            'T255_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D079_Nome' => array('titulo' => 'Máquina', 'tipo' => 'texto'),
            'D078_Nome' => array('titulo' => 'Função', 'tipo' => 'texto'),
            'T262_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T005_Id' => array('titulo' => 'Pedido', 'tipo' => 'numero'),
            'IF(T255_Flag_Status="99"[[virgula]]"Finalizado"[[virgula]]IF(T255_Flag_Status="98"[[virgula]]"Cancelado"[[virgula]]"Aberto"))' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'Date_add(T005_Data_Emissao[[virgula]] interval T006_Observacao_Entrega day)' => array('titulo' => 'Previsão Ped', 'tipo' => 'data'),
            'T256_Data_Previsao_Entrega' => array('titulo' => 'Previsão OP', 'tipo' => 'data'),
            'T256_Quantidade_Saldo' => array('titulo' => 'Saldo', 'tipo' => 'numero'),
            'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => true, 'campos' => array(
                	array('title' => 'OP', 'campoAgrupar' => 'Op', 'campoMostrar' => 'Op', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Id', 'T255_Data_Emissao')),
                	array('title' => 'PRODUTO', 'campoAgrupar' => 'Produto', 'campoMostrar' => 'Produto', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D001_Codigo_Produto', 'D001_Descricao_Produto')),
                	array('title' => 'DATA', 'campoAgrupar' => 'T255_Data_Emissao', 'campoMostrar' => 'T255_Data_Emissao', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('T255_Data_Emissao')),
                	array('title' => 'MÁQUINA', 'campoAgrupar' => 'D079_Nome', 'campoMostrar' => 'D079_Nome', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D079_Nome', 'D078_Custo_Minuto', '(D078_Custo_Minuto*T262_Lead_Time)')),
                	array('title' => 'FUNÇÃO', 'campoAgrupar' => 'D078_Nome', 'campoMostrar' => 'D078_Nome', 'campoOrdem' => 'ASC', 'campoSelecionado' => false, 'colunasOcultar' => array('D078_Nome', 'D079_Custo_Minuto', '(D079_Custo_Minuto*T262_Lead_Time)')),
              	),
            ),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T256_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Entrada de produção', '/est/est017/content/est017ContentEntradaProducao/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [600,400]);
			});
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

	case 'est017GridEntradaProducao':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T257_Id', 'colunaTitulo' => '', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T257_Id', $g['divId'])),
            array('colunaValor' => 'T257_Data_Producao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'T257_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'IF(IFNULL(T001_Id,0) > 0, "SIM", "NÃO")', 'colunaTitulo' => 'Estoque', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
        );

		$from = 'T257';

		/**
		* Definição do Extra
		*/
		$extra = <<<EOT
			LEFT JOIN T001 on T257_Id=T001_T257_Id
			LEFT JOIN C007 ON C007_Id=T257_C007_Id
			WHERE T257_T256_Id = '{$r_acaoId}' 
			GROUP BY T257_Id
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T257_Id' => array('titulo' => '', 'tipo' => 'texto'),
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
	break;

	case 'est017GridProcessarProducao':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => 'T257_Id', 'colunaTitulo' => '', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T256_T255_Id', 'colunaTitulo' => 'OP', 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T256_D009_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'D001_Id', 'colunaTitulo' => '', 'colunaExibir' => false),
            array('colunaValor' => 'T257_Data_Producao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'style' => 'width:1%;'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width:1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T257_Quantidade', 'colunaTitulo' => 'Entrada', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Produzida', 'colunaTitulo' => 'Produzido', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T256_Quantidade_Saldo', 'colunaTitulo' => 'Saldo', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'style' => 'width:1%;'),
            array('colunaValor' => 'T257_T066_Id', 'colunaTitulo' => 'Locação', 'colunaAlinhar' => 'left', 'style' => 'width: 20%', 'callback' => 'T257_T066_Id_Local', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Producao.C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width:1%;'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
        );

		$from = 'T256';

		/**
		* Definição do Extra
		*/
		$where = explode("WHERE", base64_decode($r_extra));
		$extra = <<<EOT
			{$where[0]}
			LEFT JOIN C007 as Producao ON Producao.C007_Id=T257_C007_Id
			LEFT JOIN T066 ON T066_Id = T257_T066_Id
			LEFT JOIN D004 ON D004_Id = T066_D004_Id
			WHERE {$where[1]}
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
            'T257_Id' => array('titulo' => '', 'tipo' => 'texto'),
        );
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($gridFiltro);

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

	case 'est017GridComposicaoProduto':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T261_Id', $g['divId'])),
			array('colunaValor' => 'T261_Id', 'colunaTitulo' => 'T261_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583')),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D037_Unidade', 'colunaTitulo' => 'Un', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;'),
			array('colunaValor' => 'T261_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'colunaCasasDecimais' => '5'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T261';
		$extra = <<<EOT
		LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao
		LEFT JOIN D049 ON D001_Id = D049_D001_Id
		LEFT JOIN D009 ON D049_Id = D009_D049_Id
		LEFT JOIN D037 ON D037_Id = D001_D037_Id
		WHERE T261_T256_Id = '{$r_T256_Id}'
		AND D009_C004_Id={$g['empresaAtual']}
EOT;

		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		/**
		* Botões (Versão Acima do GRID)
		*/
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Itens<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button style="margin-top: 3px;" id="incluirProdutoComposicaoOP">Incluir</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirProdutoComposicaoOP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/est/est017/content/est017ContentProdutoComposicao/', '&T256_Id={$r_T256_Id}', [360,110]);
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

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').split('|');
				var T261_Id = id[0].replace(/T261_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Itens Composição', '/est/est017/content/est017ContentProdutoComposicao/', '&acaoId=' + encodeURIComponent(T261_Id), [360,110]);
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
		$g['smarty']->display('grid.tpl');
	break;

	case 'est017GridProcessosProduto':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T262_Id', $g['divId'])),
			array('colunaValor' => 'T262_Id', 'colunaTitulo' => 'id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'T262_Ordem', 'colunaTitulo' => 'Ordem', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
			array('colunaValor' => 'D079_Nome', 'colunaTitulo' => 'Máquina', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D079_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D078_Nome', 'colunaTitulo' => 'Função', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D078_Custo_Minuto', 'colunaTitulo' => 'Custo Min.', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T262_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T262_Lead_Time', 'colunaTitulo' => 'Lead Time', 'colunaAlinhar' => 'right', 'colunaEditar' => true, 'colunaCasasDecimais' => '0'),
			array('colunaValor' => 'T262_Numero_Funcionarios', 'colunaTitulo' => 'Funcionários', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'materialConsumoOP', 'colunaTipo' => "livre", 'callback' => 'materialConsumoOP', 'style' => 'width: 1px;'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T262';
		$extra = <<<EOT
			LEFT JOIN D078 on D078_Id = T262_D078_Id
			LEFT JOIN D079 on D079_Id = T262_D079_Id
			WHERE T262_T256_Id = '{$r_T256_Id}'
			ORDER BY T262_Ordem
EOT;

		$gridFiltro = array();
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Botões (Versão Acima do GRID)
		*/
		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Processos<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button type="button" style="margin-top: 3px;" id="incluirProcessoProdutoOP">Incluir</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirProcessoProdutoOP').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Processo', '/est/est017/content/est017ContentProcessos/', '&T256_Id=' + encodeURIComponent('{$r_T256_Id}'), [355,250]);
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T262_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Processo', '/est/est017/content/est017ContentProcessos/', '&acaoId=' + encodeURIComponent(id), [355,250]);
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
		$g['smarty']->display('grid.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est017GridConsumiveis':
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T263_Id', $g['divId'])),
			array('colunaValor' => 'T263_Id', 'colunaTitulo' => 'T263_Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'style' => 'width: 1%'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T263_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2'),
		);

		/**
		* Definição da SQL
		*/
		$from = 'T263';
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id = T263_D009_Id
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D037 ON D037_Id = D001_D037_Id
			WHERE T263_T262_Id = '{$r_T262_Id}'
			AND D009_C004_Id='{$g['empresaAtual']}'
EOT;

		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Materiais<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button type="button" id="inserirConsumivelOP" style="margin-top: 5px;">Incluir</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#inserirConsumivelOP').unbind('click').bind('click', function(data) {
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Material de Consumo', '/est/est017/content/est017ContentConsumivel/', '&T262_Id={$r_T262_Id}', [360,110]);
				});
			} </script></div>
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(data.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T263_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Material de Consumo', '/est/est017/content/est017ContentConsumivel/', '&acaoId=' + encodeURIComponent(id), [360,110]);
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
		$g['smarty']->display('grid.tpl');
	break;

	case 'est017GridMateriais':
		$paramlocacaoPadraoProducao = $g['C031']['locacaoPadraoSimulacao'];

		$sqlSaldoLocacao  = " IFNULL((SELECT SUM(T066_Simulacao.T066_Quantidade_Estoque) ";
		$sqlSaldoLocacao .=           " FROM T066 AS T066_Simulacao ";
		$sqlSaldoLocacao .=           " LEFT JOIN D009 ON T066_Simulacao.T066_D009_Id = D009_Id ";
		$sqlSaldoLocacao .=           " LEFT JOIN D001 ON D009_D001_Id = D001_Id ";
		$sqlSaldoLocacao .=           " LEFT JOIN D001A ON D001A_D001_Id = D001_Id ";
		$sqlSaldoLocacao .=          " WHERE T066_Simulacao.T066_D004_Id = '{$paramlocacaoPadraoProducao}' ";
		$sqlSaldoLocacao .=            " AND T066_Simulacao.T066_D009_Id = T264.T264_D009_Id), 0) ";

		$sqlSaldoOP       = " IFNULL((SELECT SUM(T264_Simulacao.T264_Quantidade) ";
		$sqlSaldoOP      .=           " FROM T264 AS T264_Simulacao ";
		$sqlSaldoOP      .=                " LEFT JOIN T255  AS T255_SALDO  ON (T264_Simulacao.T264_T255_Id = T255_SALDO.T255_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN T255a AS T255a_SALDO ON (T255a_SALDO.T255A_T255_Id   = T255_SALDO.T255_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN T255                 ON (T255.T255_Id                = T264.T264_T255_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN T255a                ON (T255a.T255A_T255_Id         = T255.T255_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN T256                 ON (T256.T256_T255_Id           = T255.T255_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN D009                 ON (T256.T256_D009_Id           = D009.D009_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN D001                 ON (D009.D009_D001_Id           = D001.D001_Id) ";
		$sqlSaldoOP      .=                " LEFT JOIN D001A                ON (D001A.D001A_D001_Id         = D001.D001_Id) ";
		$sqlSaldoOP      .=          " WHERE T264_Simulacao.T264_D009_Id               = T264.T264_D009_Id ";
		$sqlSaldoOP      .=          "   AND T255.T255_Flag_Status                    != 99 ";
		$sqlSaldoOP      .=          "   AND T255.T255_Data_Hora_Baixa_Materiais      = '0000-00-00 00:00:00' ";
		$sqlSaldoOP      .=          "   AND T255a_SALDO.T255A_Data_Simulacao_Estoque <= T255a.T255A_Data_Simulacao_Estoque), 0) ";

		$sqlSimulacao     = " ({$sqlSaldoLocacao} - {$sqlSaldoOP}) ";

		$rowT255 = mysql_fetch_assoc(mysql_query("SELECT * FROM T255 WHERE T255_Id = '{$r_T255_Id}'"));
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'T264_Id', 'colunaTitulo' => 'T264_Id', 'colunaExibir' => false, 'chavePrimaria' => true),
			array('colunaValor' => 'D001_Id', 'colunaTitulo' => 'D001_Id', 'colunaExibir' => false),
			array('colunaValor' => 'T264_D009_Id', 'colunaTitulo' => 'T264_D009_Id', 'colunaExibir' => false),
			array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'colunaAlinhar' => 'left', 'callback' => 'abrirJanela', 'callbackParameter' => array('Produto', '/cad/cad002/content/form2/', 'D001_Id', '1000,583'), 'style' => 'width: 1%'),
			array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T264_Quantidade', 'colunaTitulo' => 'Quantidade', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T264_T066_Id', 'colunaTitulo' => 'Locação', 'colunaAlinhar' => 'left', 'style' => 'width: 20%', 'callback' => 'T264_T066_Id_Local'),
			array('colunaValor' => 'IF(T264_T066_Id > 0, T066_Quantidade_Estoque, D009_Quantidade_Estoque_Liquido)', 'colunaTitulo' => 'Estoque', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'D004_Local', 'colunaExibir' => false),
			array('colunaValor' => 'T066_Quantidade_Estoque', 'colunaTitulo' => 'T066_Quantidade_Estoque', 'colunaExibir' => false),
			array('colunaValor' => 'T264A_Observacao', 'colunaTitulo' => 'Observação', 'colunaExibir' => true, 'colunaEditar' => true),
		);

		if($paramlocacaoPadraoProducao > 0){
			$grid[] = array('colunaValor' => "{$sqlSaldoLocacao} as SaldoLocacao", 'colunaTitulo' => 'Saldo Locação', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2');
			$grid[] = array('colunaValor' => "{$sqlSaldoOP} as SaldoOP", 'colunaTitulo' => 'Saldo OP', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2');
			$grid[] = array('colunaValor' => "{$sqlSimulacao} as Simulacao", 'colunaTitulo' => 'Simulação', 'colunaAlinhar' => 'right', 'style' => 'width: 1%', 'colunaCasasDecimais' => '2');
		}


		/**
		* Definição da SQL
		*/
		$from = 'T264';
		$extra = <<<EOT
			LEFT JOIN D009 on D009_Id = T264_D009_Id
			LEFT JOIN D049 ON D049_Id = D009_D049_Id
			LEFT JOIN D001 ON D001_Id = D049_D001_Id
			LEFT JOIN D037 ON D037_Id = D001_D037_Id
			LEFT JOIN T066 ON T066_Id = T264_T066_Id
			LEFT JOIN D004 ON D004_Id = T066_D004_Id
			LEFT JOIN T264A ON T264A_T264_Id = T264_Id
			LEFT JOIN D001A ON D001A_D001_Id = D001_Id 
			WHERE T264_T255_Id = '{$r_T255_Id}'
			AND D009_C004_Id='{$g['empresaAtual']}'
			GROUP BY D009_Id
EOT;
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sql_ = base64_encode($geradoSql);

		$disabled = ($rowT255['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00') ? 'disabled=disabled' : false;
		$count = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Produtos<br><input type="text" value="{$count}" class="whiteBg" id="" size="4" readonly></span>
				<button id="processarProdutosBaixa" style="margin-top: 5px;" {$disabled}>Processar</button>
				<button id="baixarEstoqueMateriais" style="margin-top: 5px;" {$disabled}>Baixar Estoque</button>
				<div style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#processarProdutosBaixa').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est017/grid_func-ajax/processarProdutosBaixa/?ajax=true&T255_Id=' + encodeURIComponent('{$r_T255_Id}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', 'Produtos processados com sucesso!', \$buttons);
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
					dialogConfirm('Processar produtos', 'Deseja processar os produtos utilizados nesta ordem de produção?', \$buttons);
				});

				$('#baixarEstoqueMateriais').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/est/est017/grid_func-ajax/baixarEstoqueMateriais/?ajax=true&T255_Id=' + encodeURIComponent('{$r_T255_Id}') + '&sql=' + encodeURIComponent("{$sql_}") + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Operação Ok', 'Produtos baixados com sucesso!', \$buttons);
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
					dialogConfirm('Baixar matéria prima', 'Deseja baixar o estoque da matéria prima? <br><br><span style="color: var(--BGVermelho)">ATENÇÃO: Este processo não poderá ser desfeito, <br>e a ordem de produção não poderá ser alterada!</span>', \$buttons);
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
		$g['smarty']->display('grid.tpl');
	break;
}
