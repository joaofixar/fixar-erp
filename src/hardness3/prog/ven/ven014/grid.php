<?php
namespace hardness;

// Carrega funções callback
require_once('prog/ven/ven014/grid_func-callback.php');

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 22/11/2011
	*/                 
	case 'ven014GridPrincipal':
    
    $D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
    
    //mysql_query("DELETE FROM T198");
	/**
    * Filtro
    */
    //Sempre que mudar esta variável, caso o grid/filtro esteja customizado, deve-se também mudar o valor no grid/filtro
    $diasMP = $g['C031']['diasMedioPrazo'];
    $data = strtotime("+$diasMP day", strtotime("now"));
    $dataMP = date("Y-m-d", $data);    
    
    $gridFiltro = array(
        'D001_Descricao_Produto,D001_Descricao_Ingles' => array('titulo' => 'Português/Inglês', 'tipo' => 'texto'),
        "D001_Codigo_Produto" => array('titulo' => 'Código', 'tipo' => 'numero'),
        'D082_Marca' => array('titulo' => 'Marca', 'tipo' => 'texto'),
        /*'C008_Tipo' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
        'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Sim', 'value' => 'S'),
            array('title' => 'Não', 'value' => 'N'),
        ), 'naoGerarExtra' => false),
        //'D001_Flag_Pre_Cadastro' => array('titulo' => 'Pré-Cadastro (S/N)?', 'tipo' => 'texto'),
        'D004_Local' => array('titulo' => 'Locação', 'tipo' => 'texto'),
        'D009_Quantidade_Estoque' => array('titulo' => 'Qte.Estoque', 'tipo' => 'numero'),
        'D001_Foto' => array('titulo' => 'IMPA/FANN', 'tipo' => 'numero'),*/
        'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => false, 'campos' => array(
            array('title' => 'Modelo', 'campoAgrupar' => 'D001_D003_Id', 'campoMostrar' => 'D003_Nome_Linha', 'campoOrdem' => 'ASC', 'campoSelecionado' => true)
        )),
        "(SELECT 
            IFNULL((
                SELECT 
                    SUM(T055_Quantidade) 
                FROM 
                    T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                WHERE 
                    T055_D009_Id=D009_Id 
                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
                    AND IFNULL(T075_Data_Estoque[[virgula]]'0000-00-00') = '0000-00-00'
            )[[virgula]]0)-
            IFNULL((
                SELECT 
                    SUM(T006_Quantidade) 
                FROM 
                    T006 
                LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                WHERE T006_D009_Id=D009_Id 
                    AND T005_Flag_Status!='5'
                    AND T005_Flag_Status!='7' 
                    AND T005_Flag_Status!='8' 
                    AND T005_Flag_Status!='11' 
                    AND T006_T075_Id IN 
                (
                    SELECT 
                        T075_Id 
                    FROM 
                        T075 
                    LEFT JOIN T055 ON T075_Id=T055_T075_Id 
                    LEFT JOIN D009 ON T055_D009_Id=D009_Id
                    WHERE 
                        T055_D009_Id=D009_Id 
                        AND T075_Data_Previsao<=DATE(DATE_ADD(NOW()[[virgula]] INTERVAL {$diasMP} DAY))
                        AND T075_Data_Previsao > 0
                        AND IFNULL(T075_Data_Estoque[[virgula]]'0000-00-00') = '0000-00-00'
                ) 
            )[[virgula]]0))" => array('titulo' => 'Estoque MP', 'tipo' => 'numero','exibir' => false),
        'ItensSelecionados' => array('titulo' => 'Marcados', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'Sim', 'value' => " AND T198_D009_Id IS NOT NULL AND T198_C007_Id='{$g['usuarioAtual']}' "),
            array('title' => 'Não', 'value' => " AND T198_D009_Id IS NULL OR T198_C007_Id!='{$g['usuarioAtual']}' "),
        ), 'naoGerarExtra' => true,'exibir' => false),
        "IF(
        (SELECT SUM(T055_Quantidade) AS QteOC FROM T055 
        LEFT JOIN T075 ON T075_Id=T055_T075_Id 
        WHERE T055_D009_Id=D009_Id 
        AND IFNULL(T075_Data_Estoque[[virgula]]'0000-00-00')='0000-00-00' 
        AND T075_Data_Previsao<='{$dataMP}'AND T075_Data_Previsao>0)>0 AND
        (D009_Quantidade_Estoque) > 0[[virgula]]
        
        'MP / PE'[[virgula]]
        
        IF((SELECT SUM(T055_Quantidade) as QteOC FROM T055 
        LEFT JOIN T075 ON T075_Id=T055_T075_Id 
        WHERE T055_D009_Id=D009_Id 
        AND ifnull(T075_Data_Estoque[[virgula]]'0000-00-00')='0000-00-00' 
        AND T075_Data_Previsao<='{$dataMP}'and T075_Data_Previsao>0) > 0[[virgula]]

        'MP'[[virgula]] 
        
        IF((D009_Quantidade_Estoque) > 0[[virgula]]
        
        'PE'[[virgula]]
        
        '')))" => array('titulo' => 'Estoque Disponível', 'tipo' => 'select', 'select' => array(
            array('title' => 'Todos', 'value' => ''),
            array('title' => 'MP/PE', 'value' => 'MP / PE'),    
            array('title' => 'MP', 'value' => 'MP'),
            array('title' => 'PE', 'value' => 'PE'),
        ),'exibir' => false),
        "D049_Flag_Promocao" => 
        	array('titulo' => 'Promoção', 'tipo' => 'select', 'select' => array(
	        	array('title' => 'Todos', 'value' => ''),
	        	array('title' => 'Sim', 'value' => 'S'),
	        	array('title' => 'Não', 'value' => 'N'),
        	),'exibir' => false),
    );
    $g['ui']->gridFiltroExtra(false, $gridFiltro, '');
    
    $C008_Id = $r_acaoId;
    
    /**
     * Definição do grid tabela
     */
    $grid = array(
        array('colunaValor'=>"Foto", 'colunaTipo'=>'livre', 'colunaTitulo' => 'Modelo', 'colunaAgrupar' => true, 'style' => 'width: 1%;text-align: center;background-color: white;', 'callback'=>'mostrarImagemD003'),
        array('colunaValor'=>'D001_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir'=>false),
        array('colunaValor'=>'D024_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
        array('colunaValor'=>'D009_Id', 'colunaTitulo'=>'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
        array('colunaValor'=>'T198_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
        array('colunaValor'=>'D049_Id', 'colunaTitulo'=>'ID', 'colunaExibir' => false),
        array('colunaValor'=>'D001_D003_Id', 'colunaTitulo'=>'ID',  'colunaExibir' => false),
        // array('colunaValor'=>'C008_Tipo', 'colunaTitulo'=>'Tipo', 'colunaAlinhar' => 'left'),
         array('colunaValor'=>'D001_Flag_Pre_Cadastro', 'colunaTitulo'=>'Pré', 'colunaAlinhar' => 'center','colunaExibir' => false),
         array('colunaValor'=>'D001_Flag_Ativo', 'colunaTitulo'=>'Ativo', 'colunaAlinhar' => 'center','colunaExibir' => false),
        array('colunaValor' => "CampoBx", 'colunaTipo' => "livre", 'colunaTitulo' => 'Orçamento', 'callback' => 'checkboxGerarOrcamentoT198', 'colunaAlinhar' => 'center', 'style'=>'width:1%;'),
        array('colunaValor'=>'D001_Codigo_Produto', 'colunaTitulo'=>'Código', 'style' => 'font-weight:bold', 'style'=>'width:1%;'),
            // array('colunaValor'=>'D001_Foto', 'colunaTitulo'=>'IMPA/FANN'),
        array('colunaValor'=>'IF(D049_Flag_Promocao="S", concat(D001_Descricao_Produto, " <font color=\'green\'>Promoção!</font>"), D001_Descricao_Produto) as descricao', 'colunaTitulo'=>'Descrição português', 'style' => 'font-weight:bold'),
        //array('colunaValor'=>'D001_Descricao_Ingles', 'colunaTitulo'=>'Descrição inglês'),
        array('colunaValor'=>'D082_Marca', 'colunaTitulo'=>'Marca'),
        //array('colunaValor'=>'D005_Classificacao_Fiscal', 'colunaTitulo'=>'NCM', 'colunaAlinhar' => 'center'),
        //array('colunaValor'=>'D037_Unidade', 'colunaTitulo'=>'Un', 'colunaAlinhar' => 'center'),
        //array('colunaValor'=>'D009_Valor_Custo_Unitario', 'colunaTitulo'=>'Custo', 'colunaAlinhar' => 'right'),
        array('colunaValor'=>"D009_Quantidade_Estoque", 'colunaTitulo'=>'Estoque','colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'callback' => 'estoqueCorD009_Quantidade_Estoque'),
        array('colunaValor'=>'D009_Quantidade_Estoque_Real', 'colunaTitulo'=>'Estoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
        array('colunaValor'=>'D009_Quantidade_Pedido(D009_Id,1) as Pedido', 'colunaTitulo' => 'Pedidos', 'colunaAlinhar' => 'right', 'callback' => 'verPedidosDoProduto'),
        array('colunaValor'=>'D009_Quantidade_Estoque_Liquido', 'colunaTitulo'=>'Líquido', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
        array('colunaValor'=>"estoqueMedioPrazo",'colunaTipo'=>'livre', 'colunaTitulo'=>'MP','colunaAlinhar' => 'right', 'style' => 'font-weight:normal','callback'=>'estoqueMP', 'colunaExibir' => false),
        array('colunaValor'=>"estoqueLongoPrazo",'colunaTipo'=>'livre', 'colunaTitulo'=>'LP','colunaAlinhar' => 'right', 'style' => 'font-weight:normal','callback'=>'estoqueLP', 'colunaExibir' => false),        
        array('colunaValor'=>'D009_Estoque_Defeito', 'colunaTitulo'=>'Estoque defeito', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
        array('colunaValor'=>'D009_Estoque_Mostruario', 'colunaTitulo'=>'Estoque mostruario', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal', 'colunaExibir' => false ),
        array('colunaValor'=>'D009_Preco_1', 'colunaTitulo'=>'Preço 1', 'colunaAlinhar' => 'right', 'callback'=>'validaCampoPreco1'),
		array('colunaValor'=>'D009_Preco_2', 'colunaTitulo'=>'Preço 2', 'colunaAlinhar' => 'right', 'callback'=>'validaCampoPreco2'),
		array('colunaValor'=>'D009_Preco_3', 'colunaTitulo'=>'Preço 3', 'colunaAlinhar' => 'right', 'callback'=>'validaCampoPreco3'),
		array('colunaValor'=>'D009_Flag_Promocao', 'colunaTitulo'=>'Preço 1', 'colunaAlinhar' => 'right', 'colunaExibir'=>false),
        array('colunaValor'=>'D003_Nome_Linha', 'colunaTitulo' => 'Linha', 'colunaExibir'=>false),
        array('colunaValor'=>'D003_Imagem', 'colunaTitulo' => 'Imagem', 'colunaExibir'=>false),
        array('colunaValor'=>"D009_Observacao_Venda", 'colunaTitulo'=>'Obs', 'style' => 'width:5%;', 'colunaExibir'=>false),
        array('colunaValor' => "IF(
                                (SELECT SUM(T055_Quantidade) AS QteOC FROM T055 
                                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                                WHERE T055_D009_Id=D009_Id 
                                AND IFNULL(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
                                AND T075_Data_Previsao<='{$dataMP}'AND T075_Data_Previsao>0)>0 AND
                                (D009_Quantidade_Estoque) > 0,
                                
                                'MP / PE',
                                
                                IF((SELECT SUM(T055_Quantidade) as QteOC FROM T055 
                                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                                WHERE T055_D009_Id=D009_Id 
                                AND ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' 
                                AND T075_Data_Previsao<='{$dataMP}'and T075_Data_Previsao>0) > 0,

                                'MP', 
                                
                                IF((D009_Quantidade_Estoque) > 0,
                                
                                'PE',
                                
                                ''))) as estoqueDisponivel", 'colunaTitulo' => 'Estoque Disponível', 'colunaAlinhar' => 'center', 'colunaTipo' => 'banco', 'colunaExibir' => false),
        
            //array('colunaValor'=>"if(D005_Flag_ST='S','ST','') as ST", 'colunaTitulo'=>'ST', 'colunaAlinhar' => 'center'),
            //array('colunaValor'=>'D049_Flag_Tipo', 'colunaTitulo'=>'TP', 'colunaAlinhar' => 'center'),
        //array('colunaValor'=>"(select group_concat(D004_Local SEPARATOR ', ') from D004 left join T066 on T066_D004_Id=D004_Id where T066_D009_Id=D009_Id) as D004_Local", 'colunaTitulo'=>'Local'),
        //array('colunaValor'=>'D024_Nome_Empresa', 'colunaTitulo'=>'Último Fornecedor'),
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
                left join D072 on D072_D001_Id=D001_Id
                left join T066 on T066_D009_Id=D009_Id
                left join D004 on D004_Id=T066_D004_Id
                left join D041 on D041_D049_Id=D049_Id
                left join T198 on T198_D009_Id=D009_Id AND T198_C007_Id='{$g['usuarioAtual']}'
                where (D009_C004_Id='{$g['empresaAtual']}' or D009_Id is null)
                AND (D001_Flag_Ativo != 'N' or D001_Flag_Ativo is null)
                {$g['ui']->getGridFiltroCampoBusca('ItensSelecionados')}
                GROUP BY D009_Id ORDER BY D001_Descricao_Produto ASC
EOT;

    /**
    * Botões (Versão Acima do GRID)
    */
    echo <<<EOT
        <div class="buttonsBottomTop">
        <!--<button type="button" id="crm001GerarOrcamentoTabCatalogo">Gerar Orçamento</button>
        <button type="button" id="crm001MarcarTodos">Marcar Todos</button>
        <button type="button" id="crm001DesmarcarTodosCheck">Desmarcar todos</button> 
        <button type="button" id="cad002JuntarProdutos">Juntar Produtos</button>
        <button type="button" id="cad002TrocarCodigos">Trocar Códigos</button>-->
        </div>
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#crm001DesmarcarTodosCheck').unbind('click').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#' + '{$g['divId']}').showLoading();
                        $.getJSON('/crm/crm001/grid_func-ajax/crm001checkDesmarcarTodos/?ajax=true&callback=?', function(request) {
                            if (request.code) {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                divRefresh('{$g['divId']}', true);
                            } else {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                dialogConfirm('Erro', request.data, \$buttons);
                            }
                            $('#' + '{$g['divId']}').hideLoading();
                        });
                        $(this).dialog("destroy").remove();
                    },
                    "Não": function() { $(this).dialog("destroy").remove(); } 
                };
                dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja desmacar todos os produtos selecionados?', \$buttons);
            });
            $('#crm001GerarOrcamentoTabCatalogo').unbind('click').bind('click', function(data) {
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Orçamento', '/ven/ven001/content/ven001ContentOrcamento/', '&D024_Id_CRM=' + encodeURIComponent('{$D024_Id}') + '&tabCatalogoCRM=true', [650,480]);
            });
            
        } </script></div>
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
    // Gera o filtro e acrescenta ao $extra
    $filtroObrigatorio = true;
    $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
    // Pegar o valor de um campo do filtro: $g['ui']->getGridFiltroCampoBusca('D001_Flag_Pre_Cadastro');
    // Imprime o filtro do GRID (normalmente executado acima do GRID)
    echo $g['ui']->gridFiltroPrint($gridFiltro);
        
    /**
    * Botões (Versão Acima do GRID)
    */
    $valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra, true);
    $valorTotal2 = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(distinct D009_Quantidade_Estoque*D009_Valor_Custo_Compra)", $from, $extra));
    $valorTotalPE = abs($g['sqlAuto']->pegarSqlCampo("sum(D009_Quantidade_Estoque_Liquido)", $from, str_replace('GROUP BY D009_Id','',$extra)));
    $valorTotalMP = abs($g['sqlAuto']->pegarSqlCampo("sum((SELECT 
            IFNULL((
                SELECT 
                    SUM(T055_Quantidade) 
                FROM 
                    T055 
                LEFT JOIN T075 ON T075_Id=T055_T075_Id 
                WHERE 
                    T055_D009_Id=D009_Id 
                    AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
                    AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
            ),0)-
            IFNULL((
                SELECT 
                    SUM(T006_Quantidade) 
                FROM 
                    T006 
                LEFT JOIN T005 ON T005_Id=T006_T005_Id 
                WHERE T006_D009_Id=D009_Id 
                    AND T005_Flag_Status!='5'
                    AND T005_Flag_Status!='7' 
                    AND T005_Flag_Status!='8' 
                    AND T005_Flag_Status!='11' 
                    AND T006_T075_Id IN 
                (
                    SELECT 
                        T075_Id 
                    FROM 
                        T075 
                    LEFT JOIN T055 ON T075_Id=T055_T075_Id 
                    LEFT JOIN D009 ON T055_D009_Id=D009_Id
                    WHERE 
                        T055_D009_Id=D009_Id 
                        AND T075_Data_Previsao<=DATE(DATE_ADD(NOW(), INTERVAL {$diasMP} DAY))
                        AND T075_Data_Previsao > 0
                        AND IFNULL(T075_Data_Estoque,'0000-00-00') = '0000-00-00'
                ) 
            ),0)))", $from, str_replace('GROUP BY D009_Id','',$extra)));
    echo <<<EOT
        <div class="buttonsBottomTop">
            <span class="item" style="text-align:right">Total Itens<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Estoque PE<br><input type="text" value="{$valorTotalPE}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Estoque MP<br><input type="text" value="{$valorTotalMP}" class="whiteBg" id="" size="11" readonly></span>
            <div style="clear:both;"></div>
        </div>
EOT;
    
    /**
    * Geração: Monta o SQL e retorna o dados
    */
    list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 1000);
    
    $sqlGridFiltro = base64_encode($geradoSql);
    
    /**
    * Define ação do click de abertura (ao clicar em uma linha do grid)
    */
    echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#crm001MarcarTodos').unbind('click').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#' + '{$g['divId']}').showLoading();
                        $.getJSON('/crm/crm001/grid_func-ajax/crm001checkMarcarTodos/?ajax=true&sqlFiltro='+encodeURIComponent('{$sqlGridFiltro}')+'&callback=?', function(request) {
                            if (request.code) {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                divRefresh('{$g['divId']}', true);
                            } else {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                dialogConfirm('Erro', request.data, \$buttons);
                            }
                            $('#' + '{$g['divId']}').hideLoading();
                        });
                        $(this).dialog("destroy").remove();
                    },
                    "Não": function() { $(this).dialog("destroy").remove(); } 
                };
                dialogConfirm('Você tem certeza?', 'Você tem certeza que deseja marcar todos os produtos selecionados?', \$buttons);
            });    
        } </script></div>
EOT;
    
    if($g['c007']['C007_Flag_Vendedor'] == 'D'){
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
}

?>
