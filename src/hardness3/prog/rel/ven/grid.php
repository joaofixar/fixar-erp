<?php
namespace hardness;

switch($g['r'][3]){
    case 'faturamentoData':
        $filtroObrigatorio = true;

        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN D006 on D006_Id=T007_D006_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY T007_Data_Emissao
            ORDER BY T007_Data_Emissao
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
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            //'Agrupar' => array('titulo' => 'Agrupar', 'tipo' => 'agrupar', 'exibir' => true, 'campos' => array(
            //        array('title' => 'MÊS', 'campoAgrupar' => 'Mes', 'campoMostrar' => 'Mes', 'campoOrdem' => 'ASC', 'campoSelecionado' => false),//, 'colunasOcultar' => array('T007_Data_Emissao')),
            //        array('title' => 'ANO', 'campoAgrupar' => 'Ano', 'campoMostrar' => 'Ano', 'campoOrdem' => 'ASC', 'campoSelecionado' => false),//, 'colunasOcultar' => array('T007_Data_Emissao')),
            //    ),
            //),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $extraSubquery = gInsertExtraWhere($extra, "D006_Flag_Venda_Compra_Outros='V' AND D006_Flag_Devolucao='N' and T7.T007_Flag_Cancelada!='S' AND T7.T007_Data_Emissao=T007.T007_Data_Emissao");
        $subquery = "(SELECT COUNT(DISTINCT T008_D009_Id) FROM T007 AS T7 LEFT JOIN T008 ON T008_T007_Id=T007_Id {$extraSubquery}) Itens";
        $subquery = str_replace("\n", "", $subquery);
        $subquery_Soma_Quantidades = "(SELECT SUM(T008_Quantidade) FROM T007 AS T7 LEFT JOIN T008 ON T008_T007_Id=T007_Id {$extraSubquery}) Itens";
        $subquery_Soma_Quantidades = str_replace("\n", "", $subquery_Soma_Quantidades);
        $grid = array(
            array('colunaValor' => 'T007_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'DATE_FORMAT(T007_Data_Emissao,"%m/%Y") as Mes', 'colunaTitulo' => 'Mês', 'colunaExibir' => false),
            array('colunaValor' => 'YEAR(T007_Data_Emissao) as Ano', 'colunaTitulo' => 'Ano', 'colunaExibir' => false),
            array('colunaValor' => 'COUNT(DISTINCT T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery, 'colunaTitulo' => 'Produtos Vendidos', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery_Soma_Quantidades, 'colunaTitulo' => 'Quantidade Vendida', 'colunaTotal' => true, 'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'SUM(T007_Valor_Total)', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right')
        );


        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid          
         */
       
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>             
                <div style="clear:both;"></div>          
            </div>
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

    case 'faturamentoExpedicao':
        $filtroObrigatorio = true;
        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN D006 on D006_Id=T007_D006_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY T007_Flag_Expedicao
            ORDER BY SUM(T007_Valor_Total)
EOT;
        /**
        * Filtro
        */
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, "e74206b2d1a17effeef35bf557c2dc73");
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL(sum(T007_Valor_Total),0)", $from, gRetirarGroupBy($extra));
        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $extraSubquery = gInsertExtraWhere($extra, "D006_Flag_Venda_Compra_Outros='V' AND D006_Flag_Devolucao='N' and T7.T007_Flag_Cancelada!='S' AND T7.T007_Flag_Expedicao=T007.T007_Flag_Expedicao");
        $subquery = "(SELECT COUNT(DISTINCT T008_D009_Id) FROM T007 AS T7 LEFT JOIN T008 ON T008_T007_Id=T007_Id {$extraSubquery}) Itens";
        $subquery = str_replace("\n", "", $subquery);
        $grid = array(
            array('colunaValor' => 'IF(T007_Flag_Expedicao="1","COMERCIAL",IF(T007_Flag_Expedicao="2","BALCAO",T007_Flag_Expedicao)) Expedicao', 'colunaTitulo' => 'Expedição'),
            array('colunaValor' => 'COUNT(DISTINCT T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery, 'colunaTitulo' => 'Produtos Vendidos', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'SUM(T007_Valor_Total)', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(SUM(T007_Valor_Total)/$valorTotal)*100', 'colunaTitulo' => '', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'callback' => 'gInserePorcentagem', 'style' => 'width: 1%')
        );

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro, "e74206b2d1a17effeef35bf557c2dc73");

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

    case 'faturamentoVendedor':
        $filtroObrigatorio = true;
        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN D006 AS CFOP_Cabecalho ON CFOP_Cabecalho.D006_Id = T007_D006_Id 
            LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
            WHERE CFOP_Cabecalho.D006_Flag_Venda_Compra_Outros='V'
            AND CFOP_Cabecalho.D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY T007_C007_Id_Vendedor_Interno
            ORDER BY SUM(T007_Valor_Total) DESC
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
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, "e74206b2d1a17effeef35bf557c2dc73");

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro, "e74206b2d1a17effeef35bf557c2dc73");

        // Valor total para pegar percentual de cada vendedor
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL(sum(T007_Valor_Total),0)", $from, gRetirarGroupBy($extra));
        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $extraSubquery = gInsertExtraWhere($extra, "CFOP_Item.D006_Flag_Venda_Compra_Outros='V' AND CFOP_Item.D006_Flag_Devolucao='N' and T7.T007_Flag_Cancelada!='S' AND T7.T007_C007_Id_Vendedor_Interno=T007.T007_C007_Id_Vendedor_Interno");
        $subquery = "(SELECT COUNT(DISTINCT T008_D009_Id) FROM T007 AS T7 LEFT JOIN T008 ON T008_T007_Id=T007_Id LEFT JOIN D006 AS CFOP_Item ON CFOP_Item.D006_Id = T008_D006_Id {$extraSubquery}) Itens";
        $subquery = str_replace("\n", "", $subquery);
        
        $subquery_Soma_Quantidades = "(SELECT SUM(T008_Quantidade) FROM T007 AS T7 LEFT JOIN T008 ON T008_T007_Id=T007_Id LEFT JOIN D006 AS CFOP_Item ON CFOP_Item.D006_Id = T008_D006_Id {$extraSubquery}) Itens";
        $subquery_Soma_Quantidades = str_replace("\n", "", $subquery_Soma_Quantidades);

        $grid = array(
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor'),
            array('colunaValor' => 'COUNT(DISTINCT T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery, 'colunaTitulo' => 'Produtos Vendidos', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery_Soma_Quantidades, 'colunaTitulo' => 'Quantidade Vendida', 'colunaTotal' => true, 'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'SUM(T007_Valor_Total)', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(SUM(T007_Valor_Total)/$valorTotal)*100', 'colunaTitulo' => '', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'callback' => 'gInserePorcentagem', 'style' => 'width: 1%')
        );

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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

    case 'faturamentoPerfil':
        $filtroObrigatorio = true;
        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN T008 ON T008_T007_Id=T007_Id
            LEFT JOIN D006 on D006_Id=T007_D006_Id
            LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN C019 on C019_C007_Id=C007_Id AND C019_C004_Id=T007_C004_Id
            LEFT JOIN C021 on C021_Id=C019_C021_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY C021_Id
            ORDER BY SUM(T007_Valor_Total) DESC
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
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, "e74206b2d1a17effeef35bf557c2dc73");


        $argumentoSomaTotalNota = "SUM(T008_Valor_Total_Preco+T008_Valor_Total_Frete+T008_Valor_IPI+T008_Valor_ICMS_Substituicao_Retencao)";

        // Valor total para pegar percentual de cada vendedor
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL($argumentoSomaTotalNota,0)", $from, gRetirarGroupBy($extra));
        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $grid = array(
            array('colunaValor' => 'C021_Nome_Perfil', 'colunaTitulo' => 'Perfil'),
            array('colunaValor' => 'COUNT(DISTINCT T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T008_D009_Id) Itens', 'colunaTitulo' => 'Produtos Vendidos', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'SUM(T008_Quantidade) Quantidade_Vendida', 'colunaTitulo' => 'Quantidade Vendida', 'colunaTotal' => true, 'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => "$argumentoSomaTotalNota", 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => "($argumentoSomaTotalNota/$valorTotal)*100", 'colunaTitulo' => '', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'callback' => 'gInserePorcentagem', 'style' => 'width: 1%')
        );

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro, "e74206b2d1a17effeef35bf557c2dc73");

        
	    /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid          
        */
       
        echo <<<EOT
            <div class="buttonsBottomTop">                
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>             
                <div style="clear:both;"></div>          
            </div>
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

    case 'faturamentoUf':
        $filtroObrigatorio = true;
        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN D006 on D006_Id=T007_D006_Id
            LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN D024 on D024_Id=T007_D024_Id
            LEFT JOIN D018 on D018_Id=D024_D018_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY D018_Id
            ORDER BY SUM(T007_Valor_Total) DESC
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
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, "e74206b2d1a17effeef35bf557c2dc73");

        // Valor total para pegar percentual de cada vendedor
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL(sum(T007_Valor_Total),0)", $from, gRetirarGroupBy($extra));
        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $extraSubquery = gInsertExtraWhere($extra, "D006_Flag_Venda_Compra_Outros='V' AND D006_Flag_Devolucao='N' and T7.T007_Flag_Cancelada!='S' AND D18.D018_Id=D018.D018_Id");
        $extraSubquery = explode("WHERE", $extraSubquery);
        $extraSubquery = gRetirarOrderBy(gRetirarGroupBy($extraSubquery[1]));
        $subquery = "(SELECT COUNT(DISTINCT T007_D024_Id) FROM T007 AS T7 LEFT JOIN D006 on D006_Id=T007_D006_Id LEFT JOIN D024 on D024_Id=T007_D024_Id LEFT JOIN D018 as D18 on D018_Id=D024_D018_Id WHERE {$extraSubquery}) Clientes";
        $subquery2 = "(SELECT COUNT(DISTINCT T008_D009_Id) FROM T007 AS T7 LEFT JOIN D006 on D006_Id=T007_D006_Id LEFT JOIN T008 ON T008_T007_Id=T007_Id LEFT JOIN D024 on D024_Id=T007_D024_Id LEFT JOIN D018 as D18 on D018_Id=D024_D018_Id WHERE {$extraSubquery}) Itens";
        $subquery_Soma_Quantidades = "(SELECT SUM(T008_Quantidade) FROM T007 AS T7 LEFT JOIN D006 on D006_Id=T007_D006_Id LEFT JOIN T008 ON T008_T007_Id=T007_Id LEFT JOIN D024 on D024_Id=T007_D024_Id LEFT JOIN D018 as D18 on D018_Id=D024_D018_Id WHERE {$extraSubquery}) Itens";
        $subquery = str_replace("\n", "", $subquery);
        $subquery2 = str_replace("\n", "", $subquery2);
        $subquery_Soma_Quantidades = str_replace("\n", "", $subquery_Soma_Quantidades);
        $grid = array(
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF'),
            array('colunaValor' => 'COUNT(T007_Id) Notas', 'colunaTitulo' => 'Notas emitidas', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery, 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery2, 'colunaTitulo' => 'Produtos Vendidos', 'colunaTotal' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => $subquery_Soma_Quantidades, 'colunaTitulo' => 'Quantidade Vendida', 'colunaTotal' => true, 'colunaAlinhar' => 'center', 'colunaCasasDecimais'=>'2'),
            array('colunaValor' => 'SUM(T007_Valor_Total)', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(SUM(T007_Valor_Total)/$valorTotal)*100', 'colunaTitulo' => '', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'callback' => 'gInserePorcentagem', 'style' => 'width: 1%')
        );

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro, "e74206b2d1a17effeef35bf557c2dc73");

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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

    case 'faturamentoDepartamento':
        $filtroObrigatorio = true;
        $from = 'T007';
        $extra = <<<EOT
            LEFT JOIN D006 ON D006_Id=T007_D006_Id
            LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN T008 ON T008_T007_Id=T007_Id
            LEFT JOIN D009 ON D009_Id=T008_D009_Id
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D001 AS D1 ON D001_Id=D049_D001_Id
            LEFT JOIN D002 ON D002_Id=D001_D002_Id
            LEFT JOIN D003 ON D003_Id=D001_D003_Id
            LEFT JOIN D060 ON D060_Id=D001_D060_Id
            LEFT JOIN C008 ON C008_Id=D001_C008_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
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
        $C007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome FROM C007 WHERE C007_Ativo!='N'");
        $selectC007[] = array('title' => 'Todos', 'value' => '');
        while($rowC007 = mysql_fetch_array($C007)){
            $selectC007[] = array('title' => $rowC007['C007_Primeiro_Nome'], 'value' => $rowC007['C007_Id']);
        }

        $gridFiltro = array(
            'T007_Data_Emissao' => array('titulo' => 'Data', 'tipo' => 'data'),
            'T007_Flag_Expedicao' => array('titulo' => 'Expedicao', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'Comercial', 'value' => '1'),
                    array('title' => 'Balcão', 'value' => '2'),
                )
            ),
            'T007_C007_Id_Vendedor_Interno' => array('titulo' => 'Vendedor', 'tipo' => 'select',
                'select' => $selectC007
            ),
            'T007_C004_Id' => array('titulo' => 'Empresa', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Todas', 'value' => ''),
                    array('title' => 'Atual', 'value' => $g['empresaAtual']),
                )
            ),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, "e74206b2d1a17effeef35bf557c2dc73");

        // Valor total para pegar percentual de cada vendedor
        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL(SUM(T008_Valor_Total_Preco),0)", $from, gRetirarGroupBy($extra));
        /**
        * Definição do grid
        * O grid foi definido aqui pela necessidade do filtro em subquery
        */
        $grid = array(
            array('colunaValor' => 'C008_Tipo', 'colunaTitulo' => 'Departamento'),
            array('colunaValor' => 'D060_Descricao_Grupo', 'colunaTitulo' => 'Setor'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo'),
            array('colunaValor' => 'D002_Descricao_Produto', 'colunaTitulo' => 'Sub-Grupo'),
            array('colunaValor' => 'COUNT(DISTINCT T007_D024_Id) as Clientes', 'colunaTitulo' => 'Clientes atendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'COUNT(DISTINCT T008_D009_Id) as Itens', 'colunaTitulo' => 'Produtos Vendidos', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'SUM(T008_Quantidade) as Quantidade_Vendida', 'colunaTitulo' => 'Quantidade Vendida', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'SUM(T008_Valor_Total_Preco) as Valor_Total', 'colunaTitulo' => 'Valor total', 'colunaCasasDecimais' => '2', 'colunaTotal' => true, 'colunaAlinhar' => 'right'),
            array('colunaValor' => '(SUM(T008_Valor_Total_Preco)/$valorTotal)*100 as Percentual', 'colunaTitulo' => '%', 'colunaCasasDecimais' => '2', 'colunaAlinhar' => 'center', 'callback' => 'gInserePorcentagem', 'style' => 'width: 1%')
        );
        $gridOriginal = $grid;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro, "e74206b2d1a17effeef35bf557c2dc73");

        /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid          
         */
       
        /*echo <<<EOT
            <div class="buttonsBottomTop">                
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>             
                <div style="clear:both;"></div>          
            </div>
EOT;*/
        /**
        * Pode ser definido uma classe para cada linha do grid
        */
        function gridLinha($array) {
            global $g;
            if(!empty($array['todosCampos']['C008_Tipo']) || !empty($array['todosCampos']['D060_Descricao_Grupo']) || !empty($array['todosCampos']['D003_Nome_Linha'])){
                return 'linhaNegrito';
            }
        }

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        list($geradoSql, $geradoDadosFramework) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $valorTotal = $g['sqlAuto']->pegarSqlCampo("IFNULL(sum(T008_Valor_Total_Preco),0)", $from, $extra);

        //Executa sql do grid para pegar a posição das colunas
        $sql = mysql_query($geradoSql);
        $row = mysql_fetch_assoc($sql);

        $geradoDados = array();
        //Ajusta left joins
        $extra1 = str_replace('LEFT JOIN T008 ON T008_T007_Id=T007_Id', 'LEFT JOIN T008 AS T3 ON T008_T007_Id=T007_Id', $extra);
        $extra1 = str_replace('LEFT JOIN D001 AS D1 ON D001_Id=D049_D001_Id', 'LEFT JOIN D001 AS T1 ON D001_Id=D049_D001_Id', $extra1);
        $extra1 = str_replace('T007_Data_Emissao', 'T2.T007_Data_Emissao', $extra1);
        $extra1 = gInsertExtraWhere($extra1, "T1.D001_C008_Id=D1.D001_C008_Id");
        $sqlC008 = mysql_query("SELECT D003_Nome_Linha, D002_Descricao_Produto, sum(T008_Valor_Total_Preco) Valor_Total, count(*) Notas, D060_Id, D060_Descricao_Grupo, C008_Id, C008_Tipo,
                                (SELECT count(DISTINCT T2.T007_D024_Id) FROM T007 AS T2 {$extra1}) Clientes,
                                (select count(DISTINCT T3.T008_D009_Id) FROM T007 AS T2 {$extra1}) Itens
                                FROM T007 {$extra} GROUP BY C008_Id ORDER BY sum(T008_Valor_Total_Preco) DESC");
        while($rowC008 = mysql_fetch_assoc($sqlC008)){
            $geradoDados[] = array($rowC008['C008_Tipo'], '', '', '', $rowC008['Clientes'], $rowC008['Itens'], $rowC008['Valor_Total'], ($rowC008['Valor_Total']/$valorTotal)*100);

            //Segundo nível: Setor
            //Ajusta left joins
            $extra1 = str_replace('AND (T1.D001_C008_Id=D1.D001_C008_Id)', '', $extra1);
            $extra2 = gInsertExtraWhere($extra1, "T1.D001_D060_Id=D1.D001_D060_Id AND D001_C008_Id='{$rowC008['C008_Id']}'");
            $extra2_ = gInsertExtraWhere($extra, "D001_C008_Id='{$rowC008['C008_Id']}'");
            $sqlD060 = mysql_query("SELECT D003_Nome_Linha, D002_Descricao_Produto, SUM(T008_Valor_Total_Preco) Valor_Total, count(*) Notas, D060_Id, D060_Descricao_Grupo,
                                      (SELECT count(DISTINCT T2.T007_D024_Id) FROM T007 AS T2 {$extra2}) Clientes,
                                      (SELECT count(DISTINCT T3.T008_D009_Id) FROM T007 AS T2 {$extra2}) Itens
                                    FROM T007 {$extra2_} GROUP BY D060_Id ORDER BY SUM(T008_Valor_Total_Preco) DESC");
            while($rowD060 = mysql_fetch_assoc($sqlD060)){
                $geradoDados[] = array('', $rowD060['D060_Descricao_Grupo'], '', '', $rowD060['Clientes'], $rowD060['Itens'], $rowD060['Valor_Total'], ($rowD060['Valor_Total']/$valorTotal)*100);

                //Terceiro nível: Grupo
                //Ajusta left joins
                $extra3 = gInsertExtraWhere($extra2, "T1.D001_D003_Id=D1.D001_D003_Id AND D001_D060_Id='{$rowD060['D060_Id']}'");
                $extra3 = str_replace('T1.D001_D060_Id=D1.D001_D060_Id AND ', '', $extra3);
                $extra3_ = gInsertExtraWhere($extra2_, "D001_D060_Id='{$rowD060['D060_Id']}'");
                $sqlD003 = mysql_query("SELECT D003_Nome_Linha, D002_Descricao_Produto, D003_Id, SUM(T008_Valor_Total_Preco) Valor_Total, count(*) Notas,
                                              (SELECT count(DISTINCT T2.T007_D024_Id) FROM T007 AS T2 {$extra3}) Clientes,
                                              (SELECT count(DISTINCT T3.T008_D009_Id) FROM T007 AS T2 {$extra3}) Itens
                                        FROM T007 {$extra3_} GROUP BY D003_Id ORDER BY SUM(T008_Valor_Total_Preco) desc");
                while($rowD003 = mysql_fetch_assoc($sqlD003)){
                    $geradoDados[] = array('', '', $rowD003['D003_Nome_Linha'], '', $rowD003['Clientes'], $rowD003['Itens'], $rowD003['Valor_Total'], ($rowD003['Valor_Total']/$valorTotal)*100);

                    //Quarto nível: Grupo
                    //Ajusta left joins
                    $extra4 = gInsertExtraWhere($extra3, "T1.D001_D002_Id=D1.D001_D002_Id AND D001_D003_Id='{$rowD003['D003_Id']}'");
                    $extra4 = str_replace('T1.D001_D003_Id=D1.D001_D003_Id AND ', '', $extra4);
                    $extra4 = str_replace('AND (T1.D001_C008_Id=D1.D001_C008_Id) ', '', $extra4);
                    $extra4_ = gInsertExtraWhere($extra3_, "D001_D003_Id='{$rowD003['D003_Id']}'");
                    $sqlD002 = mysql_query("SELECT D003_Nome_Linha, D002_Descricao_Produto, sum(T008_Valor_Total_Preco) Valor_Total, count(*) Notas,
                                                          (SELECT COUNT(DISTINCT T2.T007_D024_Id) FROM T007 AS T2 {$extra4}) Clientes,
                                                          (SELECT COUNT(DISTINCT T3.T008_D009_Id) FROM T007 AS T2 {$extra4}) Itens
                                                    FROM T007  {$extra4_} GROUP BY D002_Id ORDER BY sum(T008_Valor_Total_Preco) DESC");
                    while($rowD002 = mysql_fetch_assoc($sqlD002)){
                        $geradoDados[] = array('', '', '', $rowD002['D002_Descricao_Produto'], $rowD002['Clientes'], $rowD002['Itens'], $rowD002['Valor_Total'], ($rowD002['Valor_Total']/$valorTotal)*100);
                    }
                }
            }
        }
        $geradoDados[] = array('', '', '', 'Total', '', '', $valorTotal, '100%');

        /**
         * Geração: Grid
         */
        $geradoDados = $g['ui']->processaArrayDados($gridOriginal, $geradoDados);
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

case 'analiticoPrazoMedioRecebimento':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join C007 as vendedor on vendedor.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
            where D024_Flag_Comercial='A'
            and D024_Flag_Cliente = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
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
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                (SELECT ROUND(SUM(DATEDIFF(T002_Data_Recebimento,T002_Data_Emissao)*T002_Valor_Total)/SUM(T002_Valor_Total),2)FROM T007 LEFT JOIN D006 ON D006_Id=T007_D006_Id LEFT JOIN T002 ON T002_T007_Id=T007_Id WHERE T007_D024_Id = D024_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V' AND T002_Data_Recebimento!='0000-00-00')
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }

        $grid[] = array('colunaValor' => "(SELECT ROUND(SUM(DATEDIFF(T002_Data_Recebimento,T002_Data_Emissao)*T002_Valor_Total)/SUM(T002_Valor_Total),2) FROM T007 LEFT JOIN D006 on D006_Id=T007_D006_Id LEFT JOIN T002 ON T002_T007_Id=T007_Id where T007_D024_Id=D024_Id and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='{$inicio}' and T007_Data_Emissao<='{$fim}' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V' and T002_Data_Recebimento!= '0000-00-00') as Total", 'colunaTitulo' => 'Prazo médio total', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $extraTotal = gRetirarGroupBy($extra);
        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));
         /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        /*echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
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

    case 'analiticoPrazoMedioVencimento':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join C007 as vendedor on vendedor.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
            where D024_Flag_Comercial='A'
            and D024_Flag_Cliente = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
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
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );
        
        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                (SELECT ROUND(SUM(DATEDIFF(T002_Data_Vencimento,T002_Data_Emissao)*T002_Valor_Total)/SUM(T002_Valor_Total),2)FROM T007 LEFT JOIN C004 ON C004_Id=T007_C004_Id LEFT JOIN D006 ON D006_Id=T007_D006_Id LEFT JOIN T002 ON T002_T007_Id=T007_Id WHERE T007_D024_Id = D024_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V' AND T002_Data_Vencimento!='0000-00-00')
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }
        $grid[] = array('colunaValor' => "(SELECT ROUND(SUM(DATEDIFF(T002_Data_Vencimento,T002_Data_Emissao)*T002_Valor_Total)/SUM(T002_Valor_Total),2) FROM T007 LEFT JOIN D006 on D006_Id=T007_D006_Id LEFT JOIN T002 ON T002_T007_Id=T007_Id where T007_D024_Id=D024_Id and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='{$inicio}' and T007_Data_Emissao<='{$fim}' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V' and T002_Data_Vencimento!= '0000-00-00') as Total", 'colunaTitulo' => 'Prazo médio total', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $extraTotal = gRetirarGroupBy($extra);
        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));
         /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        /*echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
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


    case 'analiticoVendasPeriodo':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join C007 as vendedor on vendedor.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
            where D024_Flag_Comercial !='I'
            and D024_Flag_Cliente = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
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
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T007_Valor_Total) FROM T007 LEFT JOIN C004 on C004_Id=T007_C004_Id LEFT JOIN D006 on D006_Id=T007_D006_Id WHERE T007_D024_Id = D024_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }
        $grid[] = array('colunaValor' => '(SELECT SUM(T007_Valor_Total) FROM T007 LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_D024_Id=D024_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $extraTotal = gRetirarGroupBy($extra);
        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));
         /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        /*echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
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

    case 'analiticoComprasPeriodo':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Flag_Comercial', 'colunaTitulo' => 'C', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join C007 as comprador on comprador.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=comprador.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=comprador.C007_C007_Id_2
            where D024_Flag_Fornecedor = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
            GROUP BY D024_Id
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
            'T013_Data_Emissao' => array('titulo' => 'Compras', 'tipo' => 'data', 'naoGerarExtra' => true),
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D024_Flag_Comercial' => array('titulo' => 'Comercial', 'tipo' => 'select', 'select' => array(
                array('title' => '', 'value' => ''),
                array('title' => 'ativo', 'value' => 'A'),
                array('title' => 'inativo', 'value' => 'I'),
              ),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
            ),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T013_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T013_Data_Emissao');
        if (!empty($T013_Data_Emissao[0]) && !empty($T013_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T013_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T013_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN C004 on C004_Id=T013_C004_Id LEFT JOIN D006 on D006_Id=T013_D006_Id WHERE T013_D024_Id = D024_Id AND T013_Data_Emissao >= '{$data1str} 00:00:00' AND T013_Data_Emissao <= '{$data2str} 23:59:59' AND T013_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }
        $grid[] = array('colunaValor' => '(SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_D024_Id=D024_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        
        //totalizadores
        /*$extraTotal = gRetirarGroupBy($extra);
        $numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Fornecedores<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));
         /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid        
        */
        /*echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
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


    case 'analiticoVendasPeriodoQte':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'vendedor.C007_Primeiro_Nome', 'colunaTitulo' => 'Vendedor', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join T007 on T007_D024_Id=D024_Id
            left join D006 on T007_D006_Id=D006_Id
            left join C007 as vendedor on vendedor.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=vendedor.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=vendedor.C007_C007_Id_2
            where D024_Flag_Comercial='A'
            and D024_Flag_Cliente = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', vendedor.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or vendedor.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'vendedor.C007_Primeiro_Nome' => array('titulo' => 'Vendedor', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Cliente', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
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
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN C004 on C004_Id=T007_C004_Id LEFT JOIN D006 on D006_Id=T007_D006_Id WHERE T007_D024_Id = D024_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }
        $grid[] = array('colunaValor' => '(SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_D024_Id=D024_Id and T007_Flag_Cancelada<>"S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $grid[] = array('colunaValor' => '(SELECT SUM(T007_Valor_Total) FROM T007 LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_D024_Id=D024_Id and T007_Flag_Cancelada<>"S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $extraTotal = gRetirarGroupBy($extra);
        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Clientes<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'analiticoComprasPeriodoQte':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D024_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => true, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D024_Flag_Comercial', 'colunaTitulo' => 'C', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Fornecedor', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D020_Nome_Cidade', 'colunaTitulo' => 'Cidade', 'style' => 'width: 1%;'),
            array('colunaValor' => 'D018_Uf', 'colunaTitulo' => 'UF', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Data_Cadastro', 'colunaTitulo' => 'Cadastro', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;font-weight:normal', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'comprador.C007_Primeiro_Nome', 'colunaTitulo' => 'Comprador', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D006_Codigo_CFOP', 'colunaTitulo' => 'CFOP', 'style' => 'width: 1%;'),
            //array('colunaValor' => 'D024_Nome_Fantasia', 'colunaTitulo' => 'Nome Fantasia'),
            // T007_Valor_Total_Comissao
            // C007_Primeiro_Nome
        );

        /**
        * Definição da SQL
        */
        $from = "D024";

        $extra = <<<EOT
            left join D018 on D024_D018_Id=D018_Id
            left join D020 on D024_D020_Id=D020_Id
            left join T013 on T013_D024_Id=D024_Id
            left join D006 on T013_D006_Id=D006_Id
            left join C007 as comprador on comprador.C007_Id=D024_C007_Vendedor_Interno
            left join C007 as supervisor on supervisor.C007_Id=comprador.C007_C007_Id
            left join C007 as supervisor2 on supervisor2.C007_Id=comprador.C007_C007_Id_2
            where D024_Flag_Fornecedor = 'S'
            and D024_Flag_Ativo = 'S'
            and IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V', comprador.C007_Id='{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S', (supervisor.C007_Id='{$g['usuarioAtual']}' or supervisor.C007_Id='{$g['usuarioAtual']}' or comprador.C007_Id='{$g['usuarioAtual']}') ,'1=1'))
            group by D024_Id
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
            'T013_Data_Emissao' => array('titulo' => 'Compras', 'tipo' => 'data', 'naoGerarExtra' => true),
            'comprador.C007_Primeiro_Nome' => array('titulo' => 'Comprador', 'tipo' => 'texto'),
            'supervisor.C007_Primeiro_Nome' => array('titulo' => 'Supervisor', 'tipo' => 'texto'),
            'supervisor2.C007_Primeiro_Nome' => array('titulo' => 'Supervisor 2', 'tipo' => 'texto'),
            'D024_Nome_Empresa,D024_Nome_Fantasia' => array('titulo' => 'Fornecedor', 'tipo' => 'text'),
            'D024_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'text'),
            'D020_Nome_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'text'),
            'D024_Flag_Comercial' => array('titulo' => 'Comercial', 'tipo' => 'select', 'select' => array(
                array('title' => '', 'value' => ''),
                array('title' => 'ativo', 'value' => 'A'),
                array('title' => 'inativo', 'value' => 'I'),
              ),
            'D018_Uf' => array('titulo' => 'UF', 'tipo' => 'text'),
            ),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T013_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T013_Data_Emissao');
        if (!empty($T013_Data_Emissao[0]) && !empty($T013_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T013_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T013_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN C004 on C004_Id=T013_C004_Id LEFT JOIN D006 on D006_Id=T013_D006_Id WHERE T013_D024_Id = D024_Id AND T013_Data_Emissao >= '{$data1str} 00:00:00' AND T013_Data_Emissao <= '{$data2str} 23:59:59' AND T013_Flag_Cancelada<>'S' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }
        }
        $grid[] = array('colunaValor' => '(SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_D024_Id=D024_Id and T013_Flag_Cancelada<>"S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as Total', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $grid[] = array('colunaValor' => '(SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_D024_Id=D024_Id and T013_Flag_Cancelada<>"S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as Total', 'colunaTitulo' => 'Total R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        $extraTotal = gRetirarGroupBy($extra);
        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct d024_id)", $from, $extraTotal);

        //totalizadores
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Fornecedores<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/
        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'analiticoVendasPeriodoQteProduto':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;font-weight:bold'),       
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%;font-weight:bold'),
        );

        /**
        * Definição da SQL
        */
        $from = "D001";

        $extra = <<<EOT
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join D003 on D001_D003_Id=D003_Id
            where D001_Flag_Ativo='S'
            group by D001_Id
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),            
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'text'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        $extraTotal = gRetirarGroupBy($extra);

        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;
            
            $qteTotal = mysql_query("SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$inicio' and T007_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'");
            $qteTotal = mysql_fetch_array($qteTotal);
            $qteTotal = $qteTotal[0];

            if ($qteTotal<=0){
                $qteTotal = 1;
            }

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN C004 on C004_Id=T007_C004_Id LEFT JOIN D006 on D006_Id=T007_D006_Id WHERE T008_D009_Id=D009_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }

            $grid[] = array('colunaValor' => '(SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as QteTotal', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT SUM(T008_Valor_Total_Preco) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Preço R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT (SUM(T008_Valor_Total_Preco)/SUM(T008_Quantidade)) as medio FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Preço Médio', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => "(SELECT sum(T008_Quantidade)/$qteTotal FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$inicio' and T007_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V') as Mix", 'colunaTitulo' => 'Mix Vendido', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        
            /**
             * Totais Almir
             */
            $totais = array(
                // sql
                array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
                array('nome' => 'Qte_Total', 'valor' => 'QteTotal', 'titulo' => 'Quantidade'),
            );
        }

        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct D001_Id)", $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extraTotal)));

        $qteTotal=number_format($qteTotal,0);
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade<br><input type="text" value="{$qteTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/


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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'analiticoComprasPeriodoQteProduto':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;font-weight:bold'),       
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%;font-weight:bold'),
        );

        /**
        * Definição da SQL
        */
        $from = "D001";

        $extra = <<<EOT
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join D003 on D001_D003_Id=D003_Id
            where D001_Flag_Ativo='S'
            group by D001_Id
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
            'T013_Data_Emissao' => array('titulo' => 'Compras', 'tipo' => 'data', 'naoGerarExtra' => true),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),            
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'text'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        $extraTotal = gRetirarGroupBy($extra);

        // Loop para criar colunas com as datas
        $T013_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T013_Data_Emissao');
        if (!empty($T013_Data_Emissao[0]) && !empty($T013_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T013_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T013_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;
            
            $qteTotal = mysql_query("SELECT SUM(T008_Quantidade) FROM T013 LEFT JOIN T008 on T008_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_Flag_Cancelada!='S' and T013_Data_Emissao>='$inicio' and T013_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'");
            $qteTotal = mysql_fetch_array($qteTotal);
            $qteTotal = $qteTotal[0];

            if ($qteTotal<=0){
                $qteTotal = 1;
            }

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN C004 on C004_Id=T013_C004_Id LEFT JOIN D006 on D006_Id=T013_D006_Id WHERE T014_D009_Id=D009_Id AND T013_Data_Emissao >= '{$data1str} 00:00:00' AND T013_Data_Emissao <= '{$data2str} 23:59:59' AND T013_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }

            $grid[] = array('colunaValor' => '(SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as QteTotal', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as ValorTotal', 'colunaTitulo' => 'Preço R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT (SUM(T013_Valor_Total)/SUM(T014_Quantidade)) as medio FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as Total', 'colunaTitulo' => 'Preço Médio', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => "(SELECT sum(T014_Quantidade)/$qteTotal FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!='S' and T013_Data_Emissao>='$inicio' and T013_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C') as Mix", 'colunaTitulo' => 'Mix Vendido', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');
        
            /**
            * Totais Almir
            */
            $totais = array(
                // sql
                array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
                array('nome' => 'Qte_Total', 'valor' => 'QteTotal', 'titulo' => 'Quantidade'),
            );
        }

        /*$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct D001_Id)", $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extraTotal)));

        $qteTotal=number_format($qteTotal,0);
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade<br><input type="text" value="{$qteTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'analiticoVendasPeriodoValorProduto':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;font-weight:bold'),       
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%;font-weight:bold'),
        );

        /**
        * Definição da SQL
        */
        $from = "D001";

        $extra = <<<EOT
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join D003 on D001_D003_Id=D003_Id
            where D001_Flag_Ativo='S'
            group by D001_Id
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
            'T007_Data_Emissao' => array('titulo' => 'Vendas', 'tipo' => 'data', 'naoGerarExtra' => true),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),            
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'text'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        

        $extraTotal = gRetirarGroupBy($extra);

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T007_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T007_Data_Emissao');
        if (!empty($T007_Data_Emissao[0]) && !empty($T007_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T007_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T007_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;
            
            $qteTotal = mysql_query("SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$inicio' and T007_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'");
            $qteTotal = mysql_fetch_array($qteTotal);
            $qteTotal = $qteTotal[0];          

            if ($qteTotal<=0){
                $qteTotal = 1;
            }

            $ValorTotal = mysql_query("SELECT SUM(T007_Valor_Total) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$inicio' and T007_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'");
            $ValorTotal = mysql_fetch_array($ValorTotal);
            $ValorTotal = $ValorTotal[0];


            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T008_Valor_Total_Preco) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN C004 on C004_Id=T007_C004_Id LEFT JOIN D006 on D006_Id=T007_D006_Id WHERE T008_D009_Id=D009_Id AND T007_Data_Emissao >= '{$data1str} 00:00:00' AND T007_Data_Emissao <= '{$data2str} 23:59:59' AND T007_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }

            $grid[] = array('colunaValor' => '(SELECT SUM(T008_Quantidade) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as QtdeTotal', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT SUM(T007_Valor_Total) FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as ValorTotal', 'colunaTitulo' => 'Preço R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT (SUM(T007_Valor_Total)/SUM(T008_Quantidade)) as medio FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!="S" and T007_Data_Emissao>="$inicio" and T007_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="S" AND D006_Flag_Gera_Contas="R" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "V") as Total', 'colunaTitulo' => 'Preço Médio', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => "(SELECT sum(T008_Quantidade)/$qteTotal FROM T007 LEFT JOIN T008 on T008_T007_Id=T007_Id LEFT JOIN D006 on D006_Id=T007_D006_Id where T008_D009_Id=D009_Id and T007_Flag_Cancelada!='S' and T007_Data_Emissao>='$inicio' and T007_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='S' AND D006_Flag_Gera_Contas='R' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'V') as Mix", 'colunaTitulo' => 'Mix Vendido', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            /**
             * Totais Almir
             */
            $totais = array(
                // sql
                array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
                array('nome' => 'Qte_Total', 'totalizador'=>'soma' , 'valor' =>'QtdeTotal', 'titulo' => 'Quantidade'),
                array('nome' => 'Valor_Total', 'totalizador'=>'soma' , 'valor' =>"ValorTotal", 'titulo' => 'Valor Total'),
            );
        }
        
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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'analiticoComprasPeriodoValorProduto':
        global $g;

        $filtroObrigatorio=false;

        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'Ordem', 'colunaTitulo' => '', 'colunaExibir' => true, 'callback' => 'gContador', 'colunaTipo' => 'LIVRE', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'D009_Id', 'colunaTitulo' => 'Código', 'chavePrimaria' => true, 'colunaExibir' => false, 'style' => 'width:1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D001_Codigo_Produto', 'colunaTitulo' => 'Código', 'style' => 'width: 1%;font-weight:bold'),       
            array('colunaValor' => 'D001_Descricao_Produto', 'colunaTitulo' => 'Descricao', 'style' => 'width: 1%;font-weight:bold'),
            array('colunaValor' => 'D003_Nome_Linha', 'colunaTitulo' => 'Grupo', 'style' => 'width: 1%;font-weight:bold'),
        );

        /**
        * Definição da SQL
        */
        $from = "D001";

        $extra = <<<EOT
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join D003 on D001_D003_Id=D003_Id
            where D001_Flag_Ativo='S'
            group by D001_Id
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
            'T013_Data_Emissao' => array('titulo' => 'Compras', 'tipo' => 'data', 'naoGerarExtra' => true),
            'D001_Codigo_Produto' => array('titulo' => 'Código', 'tipo' => 'texto'),
            'D001_Descricao_Produto' => array('titulo' => 'Descrição', 'tipo' => 'texto'),            
            'D003_Nome_Linha' => array('titulo' => 'Grupo', 'tipo' => 'text'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        $extraTotal = gRetirarGroupBy($extra);

        //Adicionando padrão nas datas para evitar erro de SQL
        $inicio = "0000-00-00";
		$fim = "0000-00-00";
        // Loop para criar colunas com as datas
        $T013_Data_Emissao = $g['ui']->getGridFiltroCampoBusca('T013_Data_Emissao');
        if (!empty($T013_Data_Emissao[0]) && !empty($T013_Data_Emissao[1])) {
            $inicio = gCorrigeDataInverte($T013_Data_Emissao[0]);
            $fim = gCorrigeDataInverte($T013_Data_Emissao[1]);
            $data = strtotime($inicio);
            $dataFim = strtotime($fim);
            $dataInicial = $data;
            $dataFinal = $fim;
            
            $qteTotal = mysql_query("SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_Flag_Cancelada!='S' and T013_Data_Emissao>='$inicio' and T013_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'");
            $qteTotal = mysql_fetch_array($qteTotal);
            $qteTotal = $qteTotal[0];

            if ($qteTotal <= 0){
                $qteTotal = 1;
            }

            $ValorTotal = mysql_query("SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T013_Flag_Cancelada!='S' and T013_Data_Emissao>='$inicio' and T013_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'");
            $ValorTotal = mysql_fetch_array($ValorTotal);
            $ValorTotal = $ValorTotal[0];

            while($data < $dataFim){
                if($data == $dataInicial){
                    $data1 = $data;
                } else {
                    $data1 = strtotime(date('Y-m-1', $data));
                }

                if(strtotime(date('Y-m-t', $data)) > $dataFim){
                    $data2 = $dataFim;
                } else {
                    $data2 = strtotime(date('Y-m-t', $data));
                }
                //echo date("d/m/Y", $data1) . ' - ' . date('d/m/Y', $data2) . "<br ></12>";

                $data1str = date('Y-m-d', $data1);
                $data2str = date('Y-m-d', $data2);

                $subSql = <<<EOT
                IFNULL((SELECT SUM(T014_Valor_Total_Custo_Final) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN C004 on C004_Id=T013_C004_Id LEFT JOIN D006 on D006_Id=T013_D006_Id WHERE T014_D009_Id=D009_Id AND T013_Data_Emissao >= '{$data1str} 00:00:00' AND T013_Data_Emissao <= '{$data2str} 23:59:59' AND T013_Flag_Cancelada!='S' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C'),0)
EOT;
                $grid[] = array('colunaValor' => $subSql, 'colunaTitulo' => date('m/Y', $data1), 'style' => 'font-weight: bold;', 'colunaAlinhar' => 'right', 'callback' => 'vermelhoSeZero', 'forcarExibirImpressao' => true);
                $data = strtotime("+1 month", $data);
            }

            $grid[] = array('colunaValor' => '(SELECT SUM(T014_Quantidade) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as QteTotal', 'colunaTitulo' => 'Total Qte', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT SUM(T013_Valor_Total) FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as ValorTotal', 'colunaTitulo' => 'Preço R$', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => '(SELECT (SUM(T013_Valor_Total)/SUM(T014_Quantidade)) as medio FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!="S" and T013_Data_Emissao>="$inicio" and T013_Data_Emissao<="$fim" AND D006_Flag_Entrada_Saida="E" AND D006_Flag_Gera_Contas="P" AND D006_Flag_Devolucao<>"S" AND D006_Flag_Venda_Compra_Outros = "C") as Total', 'colunaTitulo' => 'Preço Médio', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            $grid[] = array('colunaValor' => "(SELECT sum(T014_Quantidade)/$qteTotal FROM T013 LEFT JOIN T014 on T014_T013_Id=T013_Id LEFT JOIN D006 on D006_Id=T013_D006_Id where T014_D009_Id=D009_Id and T013_Flag_Cancelada!='S' and T013_Data_Emissao>='$inicio' and T013_Data_Emissao<='$fim' AND D006_Flag_Entrada_Saida='E' AND D006_Flag_Gera_Contas='P' AND D006_Flag_Devolucao<>'S' AND D006_Flag_Venda_Compra_Outros = 'C') as Mix", 'colunaTitulo' => 'Mix Vendido', 'colunaAlinhar' => 'right', 'style' => 'background: var(--celula-amarelo-claro);font-weight: bold;width:1%', 'callback' => 'gCorrigeNumero');

            /**
            * Totais Almir
            */
            $totais = array(
                // sql
                array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
                array('nome' => 'Qte_Total', 'totalizador'=>'soma' ,'valor' => 'QteTotal', 'titulo' => 'Quantidade'),
                array('nome' => 'Valor_Total', 'totalizador'=>'soma' , 'valor' => 'ValorTotal', 'titulo' => 'Valor total'),
            );

        }
        
        $numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(distinct D001_Id)", $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extraTotal)));

      /* $qteTotal=number_format($qteTotal,0);
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$numeroRegistros}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Quantidade<br><input type="text" value="{$qteTotal}" class="whiteBg" id="" size="11" readonly></span>
                <span class="item" style="text-align:right">Valor total<br><input type="text" value="{$ValorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;*/

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
                    //if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D024_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente ID: ' + acaoId, '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent(acaoId), 'auto');
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        /**
        * Geração: Monta o SQL e retorna o dados
        */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, str_replace('$inicio',"$inicio",str_replace('$fim',"$fim",$extra)));

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

    case 'resumoVendedor':
        $filtroObrigatorio = true;
        $extra = <<<EOT
            LEFT JOIN D006 on D006_Id=T007_D006_Id
            WHERE D006_Flag_Venda_Compra_Outros='V'
            AND D006_Flag_Devolucao='N'
            AND T007_Flag_Cancelada!='S'
            GROUP BY T007_Data_Emissao
            ORDER BY T007_Data_Emissao
EOT;
        /**
         * Botões Almir
         */
        $botoes = array(
            array('titulo' => 'Imprimir', 'id' => 'imprimirResumoVendedor'),
		);
		echo Botoes::processar($botoes);

        /**
        * Filtro
        */
        $arrayAno = array();
        $arrayAno[] = array('title' => date("Y", strtotime("+1 year")), 'value' => date("Y", strtotime("+1 year")));
        $arrayAno[] = array('title' => date("Y"), 'value' => date("Y"), 'selected' => true);
        $arrayAno[] = array('title' => date("Y", strtotime("-1 year")), 'value' => date("Y", strtotime("-1 year")));
        $arrayAno[] = array('title' => date("Y", strtotime("-2 year")), 'value' => date("Y", strtotime("-2 year")));
        $arrayAno[] = array('title' => date("Y", strtotime("-3 year")), 'value' => date("Y", strtotime("-3 year")));


        // novo formulÃ¡rio
        $form = new Formulario('formVendasMensais');
        // DefiniÃ§ão do FormulÃ¡rio (veja documentaÃ§ão dentro da classe Formulario para ver todas as opÃ§Ãµes disponiveis)
        $arrayC007 = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo!="N" AND C007_Id != "63" ORDER BY C007_Nome', true);
        $arrayC007[0]['title'] = 'RESUMO';
        $arrayC007[0]['value'] = '-1';
        $arrayC007Supervisor = gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'WHERE C007_Ativo!="N" AND C007_Id != "63" AND (C007_Flag_Vendedor="D" OR C007_Flag_Vendedor="S") ORDER BY C007_Nome', true);

        $gridFiltro = array(
            'Mes' => array('titulo' => 'Mês', 'tipo' => 'select', 'select' =>
                array(
                    array('title' => 'Janeiro', 'value' => '01', 'selected'=>'selected'),
                    array('title' => 'Fevereiro', 'value' => '02'),
                    array('title' => 'Março', 'value' => '03'),
                    array('title' => 'Abril', 'value' => '04'),
                    array('title' => 'Maio', 'value' => '05'),
                    array('title' => 'Junho', 'value' => '06'),
                    array('title' => 'Julho', 'value' => '07'),
                    array('title' => 'Agosto', 'value' => '08'),
                    array('title' => 'Setembro', 'value' => '09'),
                    array('title' => 'Outubro', 'value' => '10'),
                    array('title' => 'Novembro', 'value' => '11'),
                    array('title' => 'Dezembro', 'value' => '12'),
                )
            ),
            'Ano' =>array('titulo' => 'Ano', 'tipo' => 'select', 'select' => $arrayAno),
            'C007_Id' => array('titulo' => 'Vendedor', 'tipo' => 'select', 
                'select' => $arrayC007
            ),
        );
        if($g['nivelAlcada'] == '4'){
            $gridFiltro['C007_Cargo'] = array('titulo' => 'Cargo', 'tipo' => 'texto');
            $gridFiltro['C007_C007_Id'] = array('titulo' => 'Supervisor', 'tipo' => 'select',
                'select' => $arrayC007Supervisor
            );
        }

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);


        /**
        * Botões (Versão Acima do GRID) 
        *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid          
         */
       
        /*echo <<<EOT
            <div class="buttonsBottomTop">                
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>             
                <div style="clear:both;"></div>          
            </div>
EOT;*/
        //Impressão
        /*
            <button class="btn btn-mini ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="position: absolute; right: 18px; top: 85px; height: 25px;" onclick="imprimirResumoVendedor(); return false;" title="Atualizar" role="button" aria-disabled="false">
                Imprimir
            </button>*/echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                function imprimirResumoVendedor() {
                    var mywindow = window.open('', 'Resumo vendedor', 'height=600,width=900');
                    mywindow.document.write('<html><head><title>Resumo Vendedor</title>');
                    mywindow.document.write('</head><body >');
                    mywindow.document.write($('#{$g['divId']} .resumoVendedor').html());
                    mywindow.document.write('</body></html>');
                    mywindow.document.close();
                    mywindow.focus();
                    mywindow.print();
                    mywindow.close();
                    return true;
                }

                var contentHeight = parseFloat($('#{$g['divId']}').css('height'));
                var filtroHeight = parseFloat($('#{$g['divId']} .gridFiltro').css('height'));
                $('#resumoVendedorWrapper').css('height', contentHeight - filtroHeight  - 10 + 'px');
            }</script></div>
EOT;
        /*
         *
         * MONTA O RELATÓRIO
         *
         */
        $mes = $g['ui']->getGridFiltroCampoBusca('Mes');
        $ano = $g['ui']->getGridFiltroCampoBusca('Ano');
        $C007_Id = $g['ui']->getGridFiltroCampoBusca('C007_Id');
        $C007_Cargo = $g['ui']->getGridFiltroCampoBusca('C007_Cargo');
        $r_C007_C007_Id = $g['ui']->getGridFiltroCampoBusca('C007_C007_Id');
        $whereC007_C007_Id = ($r_C007_C007_Id>0) ? "AND C007_C007_Id = '$r_C007_C007_Id'" : "" ;

        $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Programacao_Vendas LIKE '%{$mes}/{$ano}%'");
        if($C007_Id == '-1'){
            $sqlC007 = mysql_query("SELECT C007_Programacao_Vendas FROM C007 WHERE C007_Programacao_Vendas LIKE '%{$mes}/{$ano}%' AND C007_Ativo != 'N' AND C007_Id != '63' AND C007_Cargo like '%$C007_Cargo%' $whereC007_C007_Id");
        }
        if(mysql_num_rows($sqlC007) <= 0){
            die("Não foi definida a programação para o mês informado");
        }
        while ($C007 = mysql_fetch_array($sqlC007)) {

            $nomeVendedor = $C007['C007_Nome'];
            $cotaAnual = 0;
            $clausulaC007 = ($C007_Id != '-1') ? "AND T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";

            //  PEGA A PROGRAMAÃ‡ÃƒO DO MÃŠS SELECIONADO
            $C007['C007_Programacao_Vendas'] = str_replace('.', '', $C007['C007_Programacao_Vendas']);
            $C007['C007_Programacao_Vendas'] = str_replace(',', '.', $C007['C007_Programacao_Vendas']);
            preg_match_all('#^(' . "{$mes}/{$ano}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosTerceiroMes);
            $cotaMensalTerceiroMes += array_sum($periodosTerceiroMes[4]);
            $cotaAnual += array_sum($periodosTerceiroMes[4]);
            $_cotaMensalTerceiroMes += array_sum($periodosTerceiroMes[4]);
            $dataFinalTerceiroMes = formataDataMes($periodosTerceiroMes[3][count($periodosTerceiroMes[3])-1], $ano);
            $dataInicialTerceiroMes = formataDataMes($periodosTerceiroMes[2][0], $ano);
            //Pega os dois meses anteriores
            $dataParametro = $dataFinalTerceiroMes;
            if(date("d", strtotime($dataParametro)) == '31' && date("m", strtotime($dataParametro)) != '03') {
                $dataParametro = date("Y-m-d", strtotime("-1 day", strtotime($dataParametro)));
            } else if(date("m", strtotime($dataParametro)) == '03'){
                $dataParametro = date("Y-m-28", strtotime($dataParametro));
            }

            $primeiroMes = date("m", strtotime("-2 month", strtotime($dataParametro)));
            $anoPrimeiroMes = date("Y", strtotime("-2 month", strtotime($dataParametro)));
            $segundoMes = date("m", strtotime("-1 month", strtotime($dataParametro)));
            $anoSegundoMes = date("Y", strtotime("-1 month", strtotime($dataParametro)));
            $terceiroMes = $mes;
            if($dataInicialTerceiroMes > $dataFinalTerceiroMes) {
                $dataInicialTerceiroMes = formataDataMes($periodosTerceiroMes[2][0], $ano-1);
            }
            foreach($periodosTerceiroMes[0] as $periodo => $periodos){
                $_cotaSemana[$periodo] += $periodosTerceiroMes[4][$periodo];
            }
        }

        // PEGA A PROGRAMAÃ‡ÃƒO DOS MESES ANTERIORES
        $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$C007_Id}' AND C007_Programacao_Vendas LIKE '%{$segundoMes}/{$anoSegundoMes}%' AND C007_Programacao_Vendas LIKE '%{$primeiroMes}/{$anoPrimeiroMes}%'");
        if($C007_Id == '-1'){
            $sqlC007 = mysql_query("SELECT C007_Programacao_Vendas FROM C007 WHERE C007_Programacao_Vendas LIKE '%{$segundoMes}/{$anoSegundoMes}%' AND C007_Programacao_Vendas LIKE '%{$primeiroMes}/{$anoPrimeiroMes}%' AND C007_Ativo != 'N' AND C007_Id != '63' AND C007_Cargo like '%$C007_Cargo%' $whereC007_C007_Id");
        }
        if(mysql_num_rows($sqlC007) <= 0){
            die("Não foi definida a programação para o meses anteriores");
        }
        while ($C007 = mysql_fetch_array($sqlC007)) {

            //ProgramaÃ§ão do primeiro mÃªs
            $C007['C007_Programacao_Vendas'] = str_replace('.', '', $C007['C007_Programacao_Vendas']);
            $C007['C007_Programacao_Vendas'] = str_replace(',', '.', $C007['C007_Programacao_Vendas']);
            preg_match_all('#^(' . "{$primeiroMes}/{$anoPrimeiroMes}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosPrimeiroMes);
            $cotaMensalPrimeiroMes += array_sum($periodosPrimeiroMes[4]);
            $cotaAnual += array_sum($periodosPrimeiroMes[4]);
            $_cotaMensalPrimeiroMes += array_sum($periodosPrimeiroMes[4]);
            $dataInicialPrimeiroMes = formataDataMes($periodosPrimeiroMes[2][0], $anoPrimeiroMes);
            $dataFinalPrimeiroMes = formataDataMes($periodosPrimeiroMes[3][count($periodosPrimeiroMes[3])-1], $anoPrimeiroMes);
            //Se a data inicial for maior que a data final, houve mudança de ano
            if($dataInicialPrimeiroMes > $dataFinalPrimeiroMes) {
                $dataInicialPrimeiroMes = formataDataMes($periodosPrimeiroMes[2][0], $ano-1);
            }

            //ProgramaÃ§ão do segundo mÃªs
            preg_match_all('#^(' . "{$segundoMes}/{$anoSegundoMes}" . ') +([^-]+)-([^ ]+) +([0-9.,]+)#ms', $C007['C007_Programacao_Vendas'], $periodosSegundoMes);
            $cotaMensalSegundoMes += array_sum($periodosSegundoMes[4]);
            $cotaAnual += array_sum($periodosSegundoMes[4]);
            $_cotaMensalSegundoMes += array_sum($periodosSegundoMes[4]);
            $dataInicialSegundoMes = formataDataMes($periodosSegundoMes[2][0], $anoSegundoMes);
            $dataFinalSegundoMes = formataDataMes($periodosSegundoMes[3][count($periodosSegundoMes[3])-1], $anoSegundoMes);
            //Se a data inicial for maior que a data final, houve mudança de ano
            if($dataInicialSegundoMes > $dataFinalSegundoMes) {
                $dataInicialSegundoMes = formataDataMes($periodosSegundoMes[2][0], $ano-1);
            }
        }
        $_cotaMensalPrimeiroMes = gCorrigeNumero($_cotaMensalPrimeiroMes);
        $_cotaMensalSegundoMes = gCorrigeNumero($_cotaMensalSegundoMes);
        $_cotaMensalTerceiroMes = gCorrigeNumero($_cotaMensalTerceiroMes);

        $primeiroMes = gRetornaMes($primeiroMes);
        $segundoMes = gRetornaMes($segundoMes);
        $terceiroMes = gRetornaMes($terceiroMes);

        $dia_inicial = date('z', strtotime($dataInicialTerceiroMes));
        $dias_mes = date('z', strtotime($dataFinalTerceiroMes));
        $data_inicial_historico = strtotime("-2 months", strtotime($dataInicialTerceiroMes));

        $C007_Cota_Vendas_Mensal = 1;

        $dataInicialAno = $dataInicialPrimeiroMes;
        $dataFinalAno = $dataFinalTerceiroMes;

        $sqlPrimeiroMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
                                                    (SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sqlSegundoMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
                                                    (SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sqlTerceiroMes = mysql_query("SELECT *, round((((SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0))-SUM(T007_Valor_Total_Custo))/
                                                    (SUM(T007_Valor_Total_Produtos)-
                                                      IF('{$g['C031']['somarICMSPrecoVenda']}'   = 'S', IFNULL(SUM(T007_Valor_Total_ICMS),0), 0)-
                                                      IF('{$g['C031']['somarPISPrecoVenda']}'    = 'S', IFNULL(SUM(T007_Valor_Total_PIS),0), 0)-
                                                      IF('{$g['C031']['somarCOFINSPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_COFINS),0), 0)-
                                                      IF('{$g['C031']['somarIRPJPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_IRPJ),0), 0)-
                                                      IF('{$g['C031']['somarCSLLPrecoVenda']}' = 'S', IFNULL(SUM(T007_Valor_Total_CSLL),0), 0)))*100,2) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");

        $resPrimeiroMes = mysql_fetch_array($sqlPrimeiroMes);
        $resSegundoMes = mysql_fetch_array($sqlSegundoMes);
        $resTerceiroMes = mysql_fetch_array($sqlTerceiroMes);
        //Valor total de vendas por mÃªs
        $valorRealizadoPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']);
        $valorRealizadoSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']);
        $valorRealizadoTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']);
        //Quantidade total de clientes atendidos por mÃªs
        $quantidadeClientesPrimeiroMes = $resPrimeiroMes['Atendidos'];
        $quantidadeClientesSegundoMes = $resSegundoMes['Atendidos'];
        $quantidadeClientesTerceiroMes = $resTerceiroMes['Atendidos'];
        $quantidadeClientesAno = $quantidadeClientesPrimeiroMes + $quantidadeClientesSegundoMes + $quantidadeClientesTerceiroMes;
        //Quantidade de NF emitidas por mÃªs
        $quantidadeNFPrimeiroMes = $resPrimeiroMes['NFEmitidas'];
        $quantidadeNFSegundoMes = $resSegundoMes['NFEmitidas'];
        $quantidadeNFTerceiroMes = $resTerceiroMes['NFEmitidas'];
        //Valor total de Vendas por mÃªs
        $valorClientesPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']);
        $valorClientesSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']);
        $valorClientesTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']);
        //Percentual Atingido por mÃªs
        $percentualAtingidoPrimeiroMes = gCorrigeNumero($resPrimeiroMes['Realizado']/$cotaMensalPrimeiroMes*100);
        $percentualAtingidoSegundoMes = gCorrigeNumero($resSegundoMes['Realizado']/$cotaMensalSegundoMes*100);
        $percentualAtingidoTerceiroMes = gCorrigeNumero($resTerceiroMes['Realizado']/$cotaMensalTerceiroMes*100);
        //Margem Média por mÃªs
        $margemMediaPrimeiroMes = gCorrigeNumero($resPrimeiroMes['MargemVenda']);
        $margemMediaSegundoMes = gCorrigeNumero($resSegundoMes['MargemVenda']);
        $margemMediaTerceiroMes = gCorrigeNumero($resTerceiroMes['MargemVenda']);

        //Select T014 notas devolução recebidas
        $sql1PrimeiroMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialPrimeiroMes}' AND T013_Data_Entrada <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql1SegundoMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialSegundoMes}' AND T013_Data_Entrada <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql1TerceiroMes = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialTerceiroMes}' AND T013_Data_Entrada <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $res1PrimeiroMes = mysql_fetch_array($sql1PrimeiroMes);
        $res1SegundoMes = mysql_fetch_array($sql1SegundoMes);
        $res1TerceiroMes = mysql_fetch_array($sql1TerceiroMes);

        $clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
        //Select T008 notas devolução emitidas
        $sql2PrimeiroMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql2SegundoMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalSegundoMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql2TerceiroMes = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T7DV.T007_Data_Emissao <= '{$dataFinalTerceiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $res2PrimeiroMes = mysql_fetch_array($sql2PrimeiroMes);
        $res2SegundoMes = mysql_fetch_array($sql2SegundoMes);
        $res2TerceiroMes = mysql_fetch_array($sql2TerceiroMes);

        //Total de NF Devolvidas
        $quantidadeDevolucaoPrimeiroMes = $res1PrimeiroMes['NFEmitidasDevolvidas'] + $res2PrimeiroMes['NFEmitidasDevolvidas'];
        $quantidadeDevolucaoSegundoMes = $res1SegundoMes['NFEmitidasDevolvidas'] + $res2SegundoMes['NFEmitidasDevolvidas'];
        $quantidadeDevolucaoTerceiroMes = $res1TerceiroMes['NFEmitidasDevolvidas'] + $res2TerceiroMes['NFEmitidasDevolvidas'];
        //Valor total de devoluções
        $valorDevolucaoPrimeiroMes = $res1PrimeiroMes['RealizadoDevolvido'] + $res2PrimeiroMes['RealizadoDevolvido'];
        $valorDevolucaoPrimeiroMes = gCorrigeNumero($valorDevolucaoPrimeiroMes);
        $valorDevolucaoSegundoMes = $res1SegundoMes['RealizadoDevolvido'] + $res2SegundoMes['RealizadoDevolvido'];
        $valorDevolucaoSegundoMes = gCorrigeNumero($valorDevolucaoSegundoMes);
        $valorDevolucaoTerceiroMes = $res1TerceiroMes['RealizadoDevolvido'] + $res2TerceiroMes['RealizadoDevolvido'];
        $valorDevolucaoTerceiroMes = gCorrigeNumero($valorDevolucaoTerceiroMes);


        $valorClientesNovosAno = 0;
        $quantidadeClientesNovosAno = 0;
        $quantidadeClientesNovosPrimeiroMes = 0;
        $quantidadeClientesNovosSegundoMes = 0;
        $quantidadeClientesNovosTerceiroMes = 0;
        $sql3PrimeiroMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007}  {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");
        $sql3SegundoMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007} {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");
        $sql3TerceiroMes = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Flag_Cancelada != 'S' {$clausulaC007} {$whereC007_C007_Id} AND C007_Cargo like '%$C007_Cargo%' GROUP BY T007_D024_Id");

        $valorClientesNovosPrimeiroMes = 0;
        while($res3PrimeiroMes = mysql_fetch_array($sql3PrimeiroMes)){
            $verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3PrimeiroMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialPrimeiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
            if(mysql_num_rows($verifica) <= 0){
                $quantidadeClientesNovosPrimeiroMes++;
                $sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3PrimeiroMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
                $resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
                $valorClientesNovosPrimeiroMes += $resRealizadosNovos['RealizadoNovos'];
            }
        }
        $quantidadeClientesNovosAno += $quantidadeClientesNovosPrimeiroMes;
        $valorClientesNovosAno += $valorClientesNovosPrimeiroMes;
        $valorClientesNovosPrimeiroMes = gCorrigeNumero($valorClientesNovosPrimeiroMes);
        $valorClientesNovosSegundoMes = 0;
        while($res3SegundoMes = mysql_fetch_array($sql3SegundoMes)){
            $verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3SegundoMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialSegundoMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
            if(mysql_num_rows($verifica) <= 0){
                $quantidadeClientesNovosSegundoMes++;
                $sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3SegundoMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
                $resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
                $valorClientesNovosSegundoMes += $resRealizadosNovos['RealizadoNovos'];
            }
        }
        $quantidadeClientesNovosAno += $quantidadeClientesNovosSegundoMes;
        $valorClientesNovosAno += $valorClientesNovosSegundoMes;
        $valorClientesNovosSegundoMes = gCorrigeNumero($valorClientesNovosSegundoMes);
        $valorClientesNovosTerceiroMes = 0;
        while($res3TerceiroMes = mysql_fetch_array($sql3TerceiroMes)){
            $verifica = mysql_query("SELECT * FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_D024_Id = '{$res3TerceiroMes['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialTerceiroMes}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
            if(mysql_num_rows($verifica) <= 0){
                $quantidadeClientesNovosTerceiroMes++;
                $sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$res3TerceiroMes['T007_D024_Id']}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
                $resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
                $valorClientesNovosTerceiroMes += $resRealizadosNovos['RealizadoNovos'];
            }
        }
        $quantidadeClientesNovosAno += $quantidadeClientesNovosTerceiroMes; 
        $valorClientesNovosAno += $valorClientesNovosTerceiroMes;
        $valorClientesNovosTerceiroMes = gCorrigeNumero($valorClientesNovosTerceiroMes);
        $diasUteisPrimeiroMes = calcularDiasUteis($dataInicialPrimeiroMes, $dataFinalPrimeiroMes);
        $diasUteisSegundoMes = calcularDiasUteis($dataInicialSegundoMes, $dataFinalSegundoMes);
        $diasUteisTerceiroMes = calcularDiasUteis($dataInicialTerceiroMes, $dataFinalTerceiroMes);
        $diasUteisAno = calcularDiasUteis($dataInicialAno, $dataFinalAno);

        $sql4PrimeiroMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioPrimeiroMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialPrimeiroMes}' AND T007_Data_Emissao <= '{$dataFinalPrimeiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql4SegundoMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioSegundoMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialSegundoMes}' AND T007_Data_Emissao <= '{$dataFinalSegundoMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $sql4TerceiroMes = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedioTerceiroMes FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialTerceiroMes}' AND T007_Data_Emissao <= '{$dataFinalTerceiroMes}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $res4PrimeiroMes = mysql_fetch_array($sql4PrimeiroMes);
        $res4SegundoMes = mysql_fetch_array($sql4SegundoMes);
        $res4TerceiroMes = mysql_fetch_array($sql4TerceiroMes);
        $prazoMediaVendasPrimeiroMes = round($res4PrimeiroMes['PrazoMedioPrimeiroMes']);
        $prazoMediaVendasSegundoMes = round($res4SegundoMes['PrazoMedioSegundoMes']);
        $prazoMediaVendasTerceiroMes = round($res4TerceiroMes['PrazoMedioTerceiroMes']);

        /****************************************************************************************************************************************************************************************************
        *                       CALCULO DOS DADOS ANUAIS
        *****************************************************************************************************************************************************************************************************/
        $sqlAno = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos/T007_Valor_Total_Custo) as MargemVenda, SUM(T007_Valor_Total_Produtos) as Realizado, COUNT(DISTINCT T007_D024_Id) as Atendidos, COUNT(*) as NFEmitidas, SUM(T007_Valor_Total_Custo) as CustoTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $resAno = mysql_fetch_array($sqlAno);

        $valorRealizadoAno = gCorrigeNumero($resAno['Realizado']);
        $percentualAtingidoAno = gCorrigeNumero($resAno['Realizado']/($cotaAnual)*100);
        $cotaAnual = gCorrigeNumero($cotaAnual);
        $valorClientesAno = gCorrigeNumero($resAno['Realizado']);
        $quantidadeNFAno = $resAno['NFEmitidas'];

        $margemMediaAno = gCorrigeNumero($resAno['MargemVenda']);

        //Select T014 notas devolvidas
        $sqlAno1 = mysql_query("SELECT *, SUM(T014_Valor_Total_Custo) as RealizadoDevolvido, COUNT(DISTINCT T013_Id) as NFEmitidasDevolvidas FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada >= '{$dataInicialAno}' AND T013_Data_Entrada <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $resAno1 = mysql_fetch_array($sqlAno1);

        //Select T008 notas devolução emitidas
        $clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
        $sqlAno2 = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS RealizadoDevolvido, COUNT(DISTINCT T7DV.T007_Id) AS NFEmitidasDevolvidas FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao >= '{$dataInicialAno}' AND T7DV.T007_Data_Emissao <= '{$dataFinalAno}' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $resAno2 = mysql_fetch_array($sqlAno2);

        $valorDevolucaoAno = gCorrigeNumero($resAno1['RealizadoDevolvido'] + $resAno2['RealizadoDevolvido']);
        $quantidadeDevolucaoAno = $resAno1['NFEmitidasDevolvidas'] + $resAno2['NFEmitidasDevolvidas'];

        //$quantidadeClientesNovosAno = 0;
        //$sqlAno3 = mysql_query("SELECT * FROM T007 WHERE T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} GROUP BY T007_D024_Id");
        //$valorClientesNovosAno = 0;
        //while($resAno3 = mysql_fetch_array($sqlAno3)){
        //  $verifica = mysql_query("SELECT * FROM T007 WHERE T007_D024_Id = '{$resAno3['T007_D024_Id']}' AND T007_Data_Emissao < '{$dataInicialAno}'");
        //  if(mysql_num_rows($verifica) <= 0){
        //      $quantidadeClientesNovosAno++;
        //      $sqlRealizadosNovos = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as RealizadoNovos FROM T007 WHERE T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND T007_D024_Id = '{$resAno3['T007_D024_Id']}'");
        //      $resRealizadosNovos = mysql_fetch_array($sqlRealizadosNovos);
        //      $valorClientesNovosAno += $resRealizadosNovos['RealizadoNovos'];
        //  }
        //}
        $valorClientesNovosAno = gCorrigeNumero($valorClientesNovosAno);

        $sqlAno4 = mysql_query("SELECT SUM(T090_Prazos_Dias)/COUNT(*) as PrazoMedio FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN T005 ON T005_Id = T007_T005_Id LEFT JOIN T090 ON T090_T005_Id = T005_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND D006_Flag_Venda_Compra_Outros = 'V' AND T007_Data_Emissao >= '{$dataInicialAno}' AND T007_Data_Emissao <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
        $resAno4 = mysql_fetch_array($sqlAno4);

        $prazoMediaVendasAno = round($resAno4['PrazoMedio']);

        $html = <<<EOT
            <div id="resumoVendedorWrapper" style="width: 100%; height: 100%; overflow: scroll;">
            <div class="resumoVendedor" style="text-align: center;">
            <div style="max-width: 1078px; max-height: 100%; min-height: 765px; border: 4px solid #000; font-family: Arial; font-size: 12px; background-color: #FFF; display: inline-block;">
                <div style="border-bottom: 3px solid #000; margin: 2px 5px; font-weight: bold; font-size: 14px;">Controle de Vendas</div>
                <div style="border-bottom: 2px solid #000; margin: 2px 5px; font-weight: bold; font-size: 12px;">{$nomeVendedor}</div>
                <table cellspacing="4" style="font-family: Arial; font-size: 14px;" width="100%">
                <tr>
                    <td width="20%"></td>
                    <td width="20%" align="center" style="border-bottom: 2px solid #000;">{$primeiroMes}</td>
                    <td width="20%" align="center" style="border-bottom: 2px solid #000;">{$segundoMes}</td>
                    <td width="20%" align="center" style="border-bottom: 2px solid #000;">{$terceiroMes}</td>
                    <td width="20%" align="center" style="border-bottom: 2px solid #000;">{$ano}</td>
                </tr>
                <tr>
                    <td width="20%">Dias Úteis</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$diasUteisAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Cota Mensal</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$_cotaMensalTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$cotaAnual}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Realizado</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorRealizadoAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">% Atingido</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoPrimeiroMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoSegundoMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoTerceiroMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$percentualAtingidoAno}%</td>
                </tr>
                <tr style="margin-top: 5px;">
                    <td width="20%" style="border-bottom: 2px solid #000; font-weight: bold;">Clientes Atendidos</td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                </tr>
                <tr>
                    <td width="20%">Quantidade</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesAno}</td>
                </tr>
                <tr>
                    <td width="20%">Valor</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesAno}</td>
                </tr>
                <tr style="margin-top: 5px;">
                    <td width="20%" style="border-bottom: 2px solid #000; font-weight: bold;">Clientes Novos</td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                    <td width="20%"></td>
                </tr>
                <tr>
                    <td width="20%">Quantidade</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeClientesNovosAno}</td>
                </tr>
                <tr>
                    <td width="20%">Valor</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorClientesNovosAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Qt NFs Emitidas</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeNFAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Qt Devoluções</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$quantidadeDevolucaoAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Vl Devoluções</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$valorDevolucaoAno}</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Margem Média</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaPrimeiroMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaSegundoMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaTerceiroMes}%</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$margemMediaAno}%</td>
                </tr>
                <tr>
                    <td width="20%" style="font-weight: bold;">Pzo Medio Vendas</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasPrimeiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasSegundoMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasTerceiroMes}</td>
                    <td width="20%" style="border-bottom: 1px solid #000;" align="right">{$prazoMediaVendasAno}</td>
                </tr>
                <tr>
                    <td colspan="5" style="border-bottom: 2px solid #000;"></td>
                </tr>
                </table>
EOT;
        $diasMax = 0;
        $totalSemana = array();
        //$mudouAno = false;
        foreach($periodosTerceiroMes[0] as $periodo => $periodos){
            $dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano);
            $dataFinalPeriodo = formataDataMes($periodosTerceiroMes[3][$periodo], $ano);
            if($dataInicialPeriodo > $dataFinalPeriodo){
                $dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano-1);
                //$mudouAno = true;
            }
            $dataInicialPeriodo = strtotime($dataInicialPeriodo);
            $dataFinalPeriodo = strtotime($dataFinalPeriodo);
            $datediff = $dataFinalPeriodo - $dataInicialPeriodo;
            $dias = floor($datediff/(60*60*24)) - 1;
            if($dias > $diasMax) $diasMax = $dias;
        }
        //$mudouAno = false;
        foreach($periodosTerceiroMes[0] as $periodo => $periodos){
            $dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano);
            $dataFinalPeriodo = formataDataMes($periodosTerceiroMes[3][$periodo], $ano);
            if($dataInicialPeriodo > $dataFinalPeriodo){
                $dataInicialPeriodo = formataDataMes($periodosTerceiroMes[2][$periodo], $ano-1);
                //$mudouAno = true;
            }
            $cotaSemana = $_cotaSemana[$periodo];
            $semana = $periodo+1;
            $html .= <<<EOT
                <table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
                            Semana {$semana}
                        </td>
                    </tr>
EOT;
            $dataInicialPeriodo = date("Y-m-d", strtotime("-1 day", strtotime($dataInicialPeriodo)));
            $diasSemana = 0;
            while($dataInicialPeriodo != $dataFinalPeriodo){
                $dataInicialPeriodo = date("Y-m-d", strtotime("+1 day", strtotime($dataInicialPeriodo)));
                if(date("w", strtotime($dataInicialPeriodo)) != 0 && date("w", strtotime($dataInicialPeriodo)) != 6){
                    $diasSemana++; //Contador utilizado para ajustar o layout
                    $diaSemana = substr(gCorrigeData($dataInicialPeriodo), 0, 5);
                    $sqlDia = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as valorTotal FROM T007 LEFT JOIN D006 ON D006_Id = T007_D006_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T007_Data_Emissao = '{$dataInicialPeriodo}' {$clausulaC007} AND T007_Flag_Cancelada != 'S' AND D006_Flag_Venda_Compra_Outros = 'V' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
                    $resDia = mysql_fetch_array($sqlDia);
                    $valorDia = gCorrigeNumero($resDia['valorTotal']);

                    $valorSemana += $resDia['valorTotal'];

                    //Select T014 notas devoluções recebidas
                    $sqlDevolucao = mysql_query("SELECT SUM(T014_Valor_Total_Custo) as valorTotal FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno LEFT JOIN D006 ON D006_Id = T014_D006_Id WHERE T013_C004_Id='{$g['empresaAtual']}' AND T013_Data_Entrada = '{$dataInicialPeriodo}' AND T007_Flag_Cancelada != 'S' AND T013_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV' $whereC007_C007_Id AND C007_Cargo like '%$C007_Cargo%'");
                    $resDevolucao = mysql_fetch_array($sqlDevolucao);

                    //Select T008 notas devolução emitidas
                    $clausulaC007NF = ($C007_Id != '-1') ? "AND T7VN.T007_C007_Id_Vendedor_Interno = '{$C007_Id}'" : "";
                    $sqlDevolucao2 = mysql_query("SELECT SUM(T8DV.T008_Valor_Total_Preco) AS valorTotal FROM T008 AS T8DV LEFT JOIN T008 AS T8VN ON T8VN.T008_Id = T8DV.T008_T008_Id LEFT JOIN T007 AS T7VN ON T7VN.T007_Id = T8VN.T008_T007_Id LEFT JOIN T007 AS T7DV ON T7DV.T007_Id = T8DV.T008_T007_Id LEFT JOIN D006 ON D006_Id = T8DV.T008_D006_Id  LEFT JOIN C007 ON C007_Id=T7VN.T007_C007_Id_Vendedor_Interno WHERE T007_C004_Id='{$g['empresaAtual']}' AND T7VN.T007_Flag_Cancelada != 'S' AND T7DV.T007_Flag_Cancelada != 'S' {$clausulaC007NF} AND D006_Flag_Venda_Compra_Outros = 'DV' AND T7DV.T007_Data_Emissao = '{$dataInicialPeriodo}' AND C007_Cargo like '%$C007_Cargo%'");
                    $resDevolucao2 = mysql_fetch_array($sqlDevolucao2);

                    $valorDevolucaoSemana += $resDevolucao['valorTotal'] + $resDevolucao2['valorTotal'];

                    $html .= <<<EOT
                        <tr>
                            <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$diaSemana}</td>
                            <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia}</td>
                        </tr>
EOT;

                }
            }
            $i = $diasMax - $diasSemana;
            if($i > 0){
                for($j = 0; $j < $i; $j++){
                    $html .= <<<EOT
                        <tr>
                            <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;"></td>
                            <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;"></td>
                        </tr>
EOT;
                }
            }
            $totalSemanaMenosDevolucao = $valorSemana - $valorDevolucaoSemana;
            $totalMenosDevolucaoMes += $totalSemanaMenosDevolucao;
            $totalMenosDevolucao = gCorrigeNumero($totalSemanaMenosDevolucao);
            $totalMes += $valorSemana;
            $valorSemana = gCorrigeNumero($valorSemana);
            $totalDevolucaoMes += $valorDevolucaoSemana;
            $valorDevolucaoSemana = gCorrigeNumero($valorDevolucaoSemana);
            $percentualSemana = gCorrigeNumero($totalSemanaMenosDevolucao/$cotaSemana*100);
            $cotaMensal += $cotaSemana;
            $cotaSemana = gCorrigeNumero($cotaSemana);
            $html .= <<<EOT
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$valorSemana}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$valorDevolucaoSemana}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucao}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaSemana}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualSemana}%</td>
                </tr>
EOT;
            $totalSemana[$periodo] = $totalMenosDevolucao;
            $valorSemana = $valorDevolucaoSemana = $totalMenosDevolucao = 0;
        }
        //die();
        $html .= <<<EOT
            <table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
                        Acumulado Mês
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 1</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[0]}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 2</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[1]}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 3</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[2]}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 4</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[3]}</td>
                </tr>
EOT;
        $i = $diasMax - 4;
        for($j = 0; $j < $i; $j++){
            $html .= <<<EOT
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;"></td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;"></td>
                </tr>
EOT;
        }
        $percentualMes = gCorrigeNumero($totalMenosDevolucaoMes/$cotaMensal*100);
        $totalMes = gCorrigeNumero($totalMes);
        $totalDevolucaoMes = gCorrigeNumero($totalDevolucaoMes);
        $totalMenosDevolucaoMes = gCorrigeNumero($totalMenosDevolucaoMes);
        $cotaMensal = gCorrigeNumero($cotaMensal);
        $html .= <<<EOT
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalMes}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalDevolucaoMes}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucaoMes}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaMensal}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
                    <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualMes}%</td>
                </tr>
            </table>
EOT;


/*
        $dia_inicial = $dataInicialTerceiroMes;

        $quantidade_dias = ($dias_mes - $dia_inicial) +1 ;
        $contadorSemana = 1;
        $mes = array();
        for ($i = 0; $i < $quantidade_dias; $i++) {
            $novaData = strtotime(date("Y-m-d", strtotime($data_inicial)) . " +$i day");
            $dataBanco = date('Y-m-d', $novaData);
            if(date("w", $novaData) == 6){
                $contadorSemana++;
            }
            $mes[$contadorSemana][date("w", $novaData)] = $dataBanco;
        }

        //print_r($mes);
        $contadorSemana = 0;
        $totalSemana = array();
        $valorDevolucao = array();
        $percentualSemana = array();
        $cotaSemanal = array();
        foreach($mes as $semana){
            $valorDia = array();
            $valorSemana = 0;
            $valorDevolucaoSemana = 0;
            foreach($semana as $key => $dia) {
                $sqlDia = mysql_query("SELECT *, SUM(T007_Valor_Total_Produtos) as valorTotal FROM T007 WHERE T007_Data_Emissao = '{$dia}' {$clausulaC007} AND T007_Flag_Cancelada != 'S'");
                $resDia = mysql_fetch_array($sqlDia);
                $semana[$key] = strtotime($semana[$key]);
                $semana[$key] = date('d', $semana[$key]).'/'.substr(gRetornaMes(date("n", $semana[$key])), 0, 3);
                $valorDia[$key] = gCorrigeNumero($resDia['valorTotal']);
                if($key != 0 && $key != 6) $valorSemana += $resDia['valorTotal'];

                $sqlDevolucao = mysql_query("SELECT SUM(T013_Valor_Total) as valorTotal FROM T014 LEFT JOIN T008 ON T008_Id = T014_T008_Id LEFT JOIN T013 ON T013_Id = T014_T013_Id LEFT JOIN T007 ON T007_Id = T008_T007_Id LEFT JOIN D006 ON D006_Id = T013_D006_Id WHERE T013_Data_Entrada = '{$dia}' AND T013_Data_Entrada <= '{$dataFinalAno}' AND T007_Flag_Cancelada != 'S' {$clausulaC007} AND D006_Flag_Venda_Compra_Outros = 'DV'");
                $resDevolucao = mysql_fetch_array($sqlDevolucao);
                $valorDevolucaoSemana += $resDevolucao['valorTotal'];
            }
            $contadorSemana++;
            $valorDevolucao[$contadorSemana] = gCorrigeNumero($valorDevolucaoSemana);
            $totalSemana[$contadorSemana] = gCorrigeNumero($valorSemana);
            $totalSemanaMenosDevolucao = $valorSemana - $valorDevolucaoSemana;
            $totalMenosDevolucao[$contadorSemana] = gCorrigeNumero($totalSemanaMenosDevolucao);

            $diasSemana = (count($semana) > 5) ? count($semana)-2 : count($semana);
            $valorCotaSemanal = $C007_Cota_Vendas_Diaria*$diasSemana;

            $percentualSemana[$contadorSemana] = gCorrigeNumero($totalSemanaMenosDevolucao/$valorCotaSemanal*100);

            $cotaSemanal[$contadorSemana] = gCorrigeNumero($valorCotaSemanal);
            $html .= <<<EOT
                <table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
                            Semana {$contadorSemana}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[1]}</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[1]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[2]}</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[2]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[3]}</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[3]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[4]}</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[4]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">{$semana[5]}</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$valorDia[5]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalSemana[$contadorSemana]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$valorDevolucao[$contadorSemana]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucao[$contadorSemana]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaSemanal[$contadorSemana]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualSemana[$contadorSemana]}%</td>
                    </tr>
                </table>
EOT;
        }
        $html .= <<<EOT
                <table style="width: 150px; float: left; font-size: 13px; border: 2px solid #000; margin: 2px;">
                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">
                            Acumulado MÃªs
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 1</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[1]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 2</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[2]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 3</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[3]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 4</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[4]}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%;">Semana 5</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right;">{$totalSemana[5]}</td>
                    </tr>
EOT;
        foreach($totalSemana as $key => $valor){
            $totalSemana[$key] = gCorrigeNumeroInverte($valor);
        }
        foreach($valorDevolucao as $key => $valor){
            $valorDevolucao[$key] = gCorrigeNumeroInverte($valor);
        }
        foreach($totalMenosDevolucao as $key => $valor){
            $totalMenosDevolucao[$key] = gCorrigeNumeroInverte($valor);
        }
        foreach($cotaSemanal as $key => $valor){
            $cotaSemanal[$key] = gCorrigeNumeroInverte($valor);
        }
        $totalMes = gCorrigeNumero(array_sum($totalSemana));
        $totalDevolucaoMes = gCorrigeNumero(array_sum($valorDevolucao));
        $totalMenosDevolucaoMes = gCorrigeNumero(array_sum($totalMenosDevolucao));
        $cotaMensal = gCorrigeNumero(array_sum($cotaSemanal));
        $percentualMes = gCorrigeNumero(array_sum($totalSemana)/array_sum($cotaSemanal)*100);
        $html .= <<<EOT
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold; padding-top: 8px;">Total</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; padding-top: 8px; font-weight: bold;">{$totalMes}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Devoluções</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalDevolucaoMes}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Tot - Devol</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$totalMenosDevolucaoMes}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">Cota</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$cotaMensal}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; font-weight: bold;">% Atingido</td>
                        <td style="border-bottom: 1px solid #000; height: 20px; width: 50%; text-align: right; font-weight: bold;">{$percentualMes}%</td>
                    </tr>
                </table>
EOT;
        $html .= "</div>";*/
        $html .= "</div>";
        $html .= "</div>";
        echo $html;
    break;
}

function calcularDiasUteis($DataInicial, $DataFinal){
    $diasUteis = 0;
    while($DataInicial != $DataFinal){
        $diaSemana = date("w", strtotime($DataInicial));
        if($diaSemana != 0 && $diaSemana != 6){
            $diasUteis++;
        }
        $DataInicial = date("Y-m-d", strtotime("+1 day", strtotime($DataInicial)));
    }

    $diaSemana = date("w", strtotime($DataInicial));
    if($diaSemana != 0 && $diaSemana != 6){
        $diasUteis++;
    }
    return $diasUteis;
}

function formataDataMes($dataMes, $ano){
    $valor = explode("/", $dataMes);
    return $ano."-".$valor[1]."-".$valor[0];
}