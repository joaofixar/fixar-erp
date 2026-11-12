<?php
namespace hardness;
/**
 * VEN - VEN001
 */

function VEN001FormProdutosPesquisaGrid($mT003,$C004_Id_Estoque,$filtro) {	
    global $g; 
	
	$pesquisaGridExtra = <<<EOT
        left join D049 on D049_D001_Id=D001_Id  
        left join D041 on D041_D049_Id=D049_Id  
        left join D009 on D009_D049_Id=D049_Id  
        left join D082 on D082_Id=D049_D082_Id  
        left join D037 on D037_Id=D001_D037_Id   
        left join D060 on D060_Id=D001_D060_Id
        left join D003 on D003_Id=D001_D003_Id
        LEFT JOIN C004 ON C004_Id=D001_C004_Id
        left join D002 on D002_Id=D001_D002_Id
        left join C008 on C008_Id=D001_C008_Id
        left join D005 on D005_Id=D001_D005_Id
        left join T066 on T066_D009_Id=D009_Id
        left join D004 on D004_Id=T066_D004_Id
        LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT003['T003_D024_Id']}'
        LEFT JOIN D144 ON D144_D049_Id=D049_Id  
        left join C007 on C007_Id=D009_C007_Id_Comprador
        LEFT JOIN T198 on T198_D009_Id=D009_Id AND T198_D024_Id='{$mT003['T003_D024_Id']}' AND T198_C007_Id='{$g['usuarioAtual']}'
        where (D009_C004_Id = '{$C004_Id_Estoque}'  and D001_Flag_Ativo='S' and ifnull(D049_Modelo,'')!='0' and ifnull(D049_Flag_Ativo,'')!='N')
        AND NOT EXISTS (
            SELECT 1
            FROM D009A D009A_Visibilidade
            INNER JOIN D009 D009_Visibilidade ON D009_Visibilidade.D009_Id=D009A_Visibilidade.D009A_D009_Id
            INNER JOIN D049 D049_Visibilidade ON D049_Visibilidade.D049_Id=D009_Visibilidade.D009_D049_Id
            WHERE D049_Visibilidade.D049_D001_Id=D001_Id
              AND D009_Visibilidade.D009_C004_Id='{$g['empresaAtual']}'
              AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
        )
        {$filtro}
        group by D009_Id  
        order by D001_Codigo_Produto ASC
EOT;

    $pesquisaGrid = array(
        'D001',																											              // tabela
        'D009_Id,D049_Flag_Tipo,D144_Id',																										      // chave primaria
        'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D049_Flag_Tipo||D005_Classificacao_Fiscal||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Similar||"" as retornaPrecoVenda||T198_Quantidade||infoCustoVendaProduto||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T003_Id . '" as T003_Id||D001_Id||D009_Data_Alteracao_Custo||D009_Data_Ultima_Venda||D009_Flag_Custo_Utilizado||D009_Quantidade_Estoque_Tabela(D009_Id)',	// campos
        $pesquisaGridExtra,																								              // extras sql
        'Pesquisa de Produto',																										  // título da janela
        'Id,Dep,Setor,Foto,Código,Sub-item,Cód.Cli,Descrição,Referência,Marca,UN,Tipo,NCM,Preço,Preço 2,Preço 3,Físico,Líquido,Loja,Locação,Fora,Similar,Preço,Quantidade,,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,D001_Id,D009_Data_Alteracao_Custo,D009_Data_Ultima_Venda,D009_Flag_Custo_Utilizado,Est.Tab',	  // descrição dos campos
        'ven001CampoProcuraProduto',																					              // callback js
        $g['divId'],																									              // divId
        array($T003_Id, $mT003['T003_D024_Id'], $g['C031']['habilitarInclusaoSubItensMontagem']),									  // parametro extra
        array(																											              // parametros para cada campo do grid
            array('chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),				
            array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
            array('colunaAlinhar' => 'left', 'style' => 'width:1%;font-weight:bold'),
            array('colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('style' => 'font-weight:bold;white-space:normal;'),
            array('colunaAlinhar' => 'left', 'style' => 'width:1%'),
            array('style' => 'width:1%'),
            array('style' => 'width:1%'),
            array('style' => 'width:1%'),
            array('style' => 'width:1%'),
            array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
            array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
            array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
            array('colunaAlinhar' => 'center', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
            array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array($r_T003_Id)),
            array('callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
            array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVenda', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
            array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T198_Quantidade', 'colunaEditarInsert' => true, 'colunaExibir' => $incluirLote),
            array('callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
            array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
            array('style' => 'width:1%'),
            array('style' => 'width:1%'),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaTitulo' => 'T003_Id', 'colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaExibir' => false),
            array('colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%', 'colunaExibir'=>false),
        ),
        'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||infoCustoVendaProduto||D144_Id||C008_Id||D060_Id||D037_Unidade||D009_Quantidade_Estoque_Real||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_OC||D144_Id||D001_Id||D009_Flag_Custo_Utilizado||T198_Id' // Campos que são ignorados na cláusula where do pesquisaGrid
	);

    return $pesquisaGrid;
}

?>
