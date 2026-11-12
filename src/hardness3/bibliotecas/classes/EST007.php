<?php
namespace hardness;

require_once("old/lib/funcoes.php");

definirClasse('EST007');
class EST007_
{
    /**
     * est007gravarProduto
     *
     * @param int $D049_Id
     * @param int $T047_Id
     */
    public function est007gravarProduto($D049_Id, $T047_Id)
    {
        global $g;

        // *** variáveis que vem do pesquisaGrid
        $nT047_Id      = $T047_Id;
        // $cCodigo_Produto = $_REQUEST[Consulta_T048_D049_Id];
        $nT048_D049_Id = $D049_Id;
        
        if (empty($nT047_Id))
            return "Nao foi recebido o id da nota = T047_Id";
        
        
        // if (empty($cCodigo_Produto))
        // return "Informe o codigo do produto";
        
        
        $cClausula = " select ";
        $cClausula .= " D001_Codigo_Produto, ";
        $cClausula .= " D001_Flag_Ativo ";
        $cClausula .= " from D049 ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " where D049_Id='$nT048_D049_Id' ";
        $T048 = mysql_query("$cClausula");
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: $cErro <br /> $cClausula";
        }
        
        $mT048 = mysql_fetch_array($T048);
        if (mysql_num_rows($T048) == 0)
            return "Erro na seleção do produto!";
        
        $cClausula = " select ";
        $cClausula .= " T047_Flag_Status ";
        $cClausula .= " from T047 ";
        $cClausula .= " where T047_Id='$nT047_Id' ";
        $cSql_T047 = mysql_query($cClausula);
        $mT047     = mysql_fetch_array($cSql_T047);
        
        if ($mT047['T047_Flag_Status'] == 'A')
            return 'Esta montagem já foi finalizada!';
        
        if ($mT048['D001_Flag_Ativo'] == 'N')
            return '**** ITEM NÃO PODE SER INCLUÍDO ****<br />Este ítem está inativo!';
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE O ITEM DIGITADO EXISTE NO CADASTRO DE MONTAGEM
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $cSql_D049 = mysql_query("select D049_Flag_Tipo from D049 where D049_Id='$D049_Id'");
        $mD049     = mysql_fetch_array($cSql_D049);
        if ($mD049['D049_Flag_Tipo'] != 'MT')
            return "Este item não consta no cadastro de montagens.<br />VERIFIQUE!!!";
        
        $cSql_T048 = mysql_query("select T048_Id
		                        from T048
		                       where T048_D049_Id='$nT048_D049_Id'
		                         and T048_T047_Id='$nT047_Id'");
        
        if (mysql_num_rows($cSql_T048) > 0)
            return "Produto já cadastrado nesta montagem";
        
        mysql_query("set autocommit=0");
        mysql_query("start transaction");
        
        // insere o item selecionado
        $cClausula = " insert into T048 (";
        $cClausula .= " T048_D049_Id, ";
        $cClausula .= " T048_T047_Id, ";
        $cClausula .= " T048_C004_Id, ";
        $cClausula .= " T048_Quantidade, ";
        $cClausula .= " T048_Codigo_Produto, ";
        $cClausula .= " T048_Observacao ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '$nT048_D049_Id', ";
        $cClausula .= " '$nT047_Id', ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '1', ";
        $cClausula .= " '{$mT048['D001_Codigo_Produto']}', ";
        $cClausula .= " '' ";
        $cClausula .= " ) ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: $cErro <br /> $cClausula";
        }
        
        //$nT048_Id=mysql_insert_id();
        $nT048_Id = $g['mysqlLastId'];
        
        $cClausula = " select";
        $cClausula .= " D042_Id, ";
        $cClausula .= " D042_D049_Id_Itens, ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D042_Quantidade, ";
        $cClausula .= " D001_Codigo_Produto ";
        $cClausula .= " from D042 ";
        $cClausula .= " left join D009 on D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id='{$g['C004']['C004_Id']}' ";
        $cClausula .= " left join D049 on D049_Id=D042_D049_Id_Itens ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " where D042_D049_Id='$nT048_D049_Id' ";
        $cClausula .= " and D009_C004_Id='{$g['C004']['C004_Id']}' ";
        
        $cSql_D042 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: $cErro<br />$cClausula";
        }
        
        // insere os sub-itens do item selecionado
        while ($mD042 = mysql_fetch_array($cSql_D042)) {
            $cClausula = " insert into T071 (";
            $cClausula .= " T071_T048_Id, ";
            $cClausula .= " T071_D009_Id, ";
            $cClausula .= " T071_Quantidade, ";
            $cClausula .= " T071_Codigo_Produto ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '$nT048_Id', ";
            $cClausula .= " '{$mD042['D009_Id']}', ";
            $cClausula .= " '{$mD042['D042_Quantidade']}', ";
            $cClausula .= " '{$mD042['D001_Codigo_Produto']}' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro<br />$cClausula";
            }
            
        }
        
        mysql_query("commit");
        
        return true;
    }
    
    /**
     * est007gravarProdutoSubItens
     *
     * @param int $D009_Id
     * @param int $T048_Id
     */
    public function est007gravarProdutoSubItens($D009_Id, $T048_Id)
    {
        global $g;
        
        $nT048_Id      = $T048_Id;
        //$cCodigo_Produto = $_REQUEST[Consulta_T071_D009_Id];
        $nT071_D009_Id = $D009_Id;
        
        if (empty($nT048_Id))
            return "Nao foi recebido o id da nota = T048_Id";
        
        // if (empty($cCodigo_Produto))
        // return "Informe o codigo do produto";
        
        $cClausula = " select ";
        $cClausula .= " D001_Flag_Ativo, ";
        $cClausula .= " D001_Codigo_Produto ";
        $cClausula .= " from D009 ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " where D009_Id='$nT071_D009_Id' ";
        $T071 = mysql_query("$cClausula");
        
        $cErro = mysql_error();
        if (!empty($cErro))
            return "Erro: $cErro <br /> $cClausula";
        
        $mT071 = mysql_fetch_array($T071);
        if (mysql_num_rows($T071) == 0)
            return "Erro na seleção do produto!";
        
        if ($mT071['D001_Flag_Ativo'] != 'S')
            return '**** ITEM NÃO PODE SER INCLUÍDO ****<br />Este ítem está inativo!';
        
        $cClausula = " insert into T071 (";
        $cClausula .= " T071_T048_Id, ";
        $cClausula .= " T071_D009_Id, ";
        $cClausula .= " T071_Quantidade, ";
        $cClausula .= " T071_Codigo_Produto ";
        $cClausula .= " ) values ( ";
        $cClausula .= " '$nT048_Id', ";
        $cClausula .= " '$nT071_D009_Id', ";
        $cClausula .= " '1', ";
        $cClausula .= " '{$mT071['D001_Codigo_Produto']}' ";
        $cClausula .= " ) ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro))
            return "Erro: $cErro <br /> $cClausula";
        
        return true;
    }
    
    /**
     * est007ProcessarMontagem
     *
     * @param int $T047_Id
     */
    public function est007ProcessarMontagem($T047_Id)
    {
        global $g;
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        if ($T047_Id <= 0)
            return 'Falha no processo, não foi recebido o T047_Id';
        
        $D048_Id = 1;
        
        $cSql_T047 = mysql_query("select *
		                            from T047
		                            where T047_Id='$T047_Id'");
        $mT047     = mysql_fetch_array($cSql_T047);
        if ($mT047['T047_Flag_Status'] == 'A')
            return "Esta montagem já foi feita. VERIFIQUE!!!";
        
        /////////////////////////////////////////////////////////////////////
        // VERIFICA SE A MONTAGEM NAO ESTA VAZIA OU SEM SUB-ITENS
        /////////////////////////////////////////////////////////////////////
        $cSql_T071 = mysql_query("select sum(if(T071_Id is not null,1,0)) as Total_Sub_Itens,
		                                 D001_Codigo_Produto
		                            from T048
		                       left join T071 on T071_T048_Id=T048_Id
		                       left join T047 on T048_T047_Id=T047_Id
		                       left join D049 on T048_D049_Id=D049_Id
		                       left join D001 on D049_D001_Id=D001_Id
		                           where T047_Id='$T047_Id'
		                        group by T048_Id");
        if (mysql_num_rows($cSql_T071) == 0)
            return "Não existem itens nesta montagem!";
        
        while ($mT071 = mysql_fetch_array($cSql_T071)) {
            if ($mT071['Total_Sub_Itens'] == 0) {
                $mMsgSubItem[] = $mT071['D001_Codigo_Produto'];
            }
            
        }
        if (count($mMsgSubItem) > 0)
            return "Os itens abaixo não possuem sub-itens:<br />" . implode("<br>", $mMsgSubItem) . "<br />VERIQUE POR FAVOR!";
        /*
        //////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE EXISTEM SUB-ITENS SEM CUSTO
        //////////////////////////////////////////////////////////////////////
        $cSql_T071=mysql_query("select D001_Codigo_Produto,
        D002_Descricao_Produto
        from T048
        left join T071 on T071_T048_Id=T048_Id
        left join T047 on T048_T047_Id=T047_Id
        left join D009 on T071_D009_Id=D009_Id
        left join D049 on D009_D049_Id=D049_Id
        left join D001 on D049_D001_Id=D001_Id
        left join D002 on D001_D002_Id=D002_Id
        where T047_Id='$T047_Id'
        and D009_Valor_Custo_Compra<=0");
        if (mysql_num_rows($cSql_T071)>0) {
        while ($mT071=mysql_fetch_array($cSql_T071)) {
        $mInativados[]="{$mT071['D001_Codigo_Produto']} - {$mT071['D002_Descricao_Produto']}";
        }
        $Inativados=implode('<br />',$mInativados);
        return "OS SUB-ITENS ABAIXO ESTÃO SEM CUSTO:<br />$Inativados";
        }
        */
        
        //////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE EXISTEM SUB-ITENS INATIVOS
        //////////////////////////////////////////////////////////////////////
        $cSql_T071 = mysql_query("select D001_Codigo_Produto,
		                                 D002_Descricao_Produto
		                           from T048
		                      left join T071 on T071_T048_Id=T048_Id
		                      left join T047 on T048_T047_Id=T047_Id
		                      left join D009 on T071_D009_Id=D009_Id
		                      left join D049 on D009_D049_Id=D049_Id
		                      left join D001 on D049_D001_Id=D001_Id
		                      left join D002 on D001_D002_Id=D002_Id
		                          where T047_Id='$T047_Id'
		                            and D001_Flag_Ativo='N'");
        if (mysql_num_rows($cSql_T071) > 0) {
            while ($mT071 = mysql_fetch_array($cSql_T071)) {
                $mInativados[] = "{$mT071['D001_Codigo_Produto']} - {$mT071['D002_Descricao_Produto']}";
            }
            $Inativados = implode('/p', $mInativados);
            return "OS SUB-ITENS ABAIXO ESTÃO INATIVOS:<br />$Inativados";
            
        }
        
        //////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE EXISTEM ITENS INATIVOS
        //////////////////////////////////////////////////////////////////////
        $cSql_T048 = mysql_query("select D001_Codigo_Produto,
                                         D002_Descricao_Produto
                                    from T048
                               left join D049 on T048_D049_Id=D049_Id
                               left join D001 on D049_D001_Id=D001_Id
                               left join D002 on D001_D002_Id=D002_Id
                                   where T048_T047_Id='$T047_Id'
                                     and D001_Flag_Ativo='N'");
        if (mysql_num_rows($cSql_T048) > 0) {
            while ($mT048 = mysql_fetch_array($cSql_T048)) {
                $mInativados[] = "{$mT048['D001_Codigo_Produto']} - {$mT048['D002_Descricao_Produto']}";
            }
            $Inativados = implode('<br />', $mInativados);
            return "OS ITENS ABAIXO ESTÃO INATIVOS:<br />$Inativados";
        }
        
        //////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE EXISTEM ITENS SEM O CAMPO TIPO PREENCHIDO
        //////////////////////////////////////////////////////////////////////
        $cSql_T048 = mysql_query("select D001_Codigo_Produto,
                                         D001_Descricao_Produto
                                    from T048
                               left join D049 on T048_D049_Id=D049_Id
                               left join D001 on D049_D001_Id=D001_Id
                               left join D002 on D001_D002_Id=D002_Id
                                   where T048_T047_Id='$T047_Id'
                                     and (T048_Flag_Tipo_Montagem='' or T048_Flag_Tipo_Montagem is null)");
        if (mysql_num_rows($cSql_T048) > 0) {
            while ($mT048 = mysql_fetch_array($cSql_T048)) {
                $mrestricaoTipo[] = "{$mT048['D001_Codigo_Produto']} - {$mT048['D001_Descricao_Produto']}";
            }
            $restricaoTipo = implode('<br />', $mrestricaoTipo);
            return "Você deve preencher a coluna \"Tipo\" dos itens abaixo:<br />$restricaoTipo";
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERFICA SE O ESTOQUE � SUFICIENTE PARA MONTAR OS ITENS
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_T048 = mysql_query("select *
		                            from T048
		                       left join D049 on D049_Id=T048_D049_Id
                               left join D001 on D001_Id=D049_D001_Id
		                       left join D009 on D049_Id=D009_D049_Id and D009_C004_Id='{$g['C004']['C004_Id']}'
                               left join T066 on T066_Id=T048_T066_Id
		                           where T048_T047_Id='$T047_Id'");
        if (mysql_num_rows($cSql_T048) == 0)
            return "Não existem ítens para montar.<br />VERIFIQUE!!!";
        
        while ($mT048 = mysql_fetch_array($cSql_T048)) {
            
            if (empty($mT048['D009_Id']))
                return 'O Código: ' . $mT048['T048_Codigo_Produto'] . ' não está ligado ao Estoque (D009_Id)' . "select * from T048 left join D049 on D049_Id=T048_D049_Id left join D009 on D049_Id=D009_D049_Id and D009_C004_Id='{$g['C004']['C004_Id']}' where T048_T047_Id='$T047_Id'";
            
            $cSql_T071 = mysql_query("select *
		                                from T071
		                           left join D009 on D009_Id=T071_D009_Id
		                           left join D049 on D049_Id=D009_D049_Id
		                           left join D001 on D001_Id=D049_D001_Id
                                   left join T066 on T066_Id=T071_T066_Id
		                               where T071_T048_Id={$mT048['T048_Id']}");
            
            while ($mT071 = mysql_fetch_array($cSql_T071)) {
                
                if (empty($mT071['D009_Id'])) {
                    $mMsg_Erro[] = "Codigo: {$mD071['D001_Codigo_Produto']}";
                }
                if ($mT048['T048_Flag_Tipo_Montagem'] == 'M') {
                    if($mT071['T071_T066_Id'] > 0){
                        $quantidadeEstoque = $mT071['T066_Quantidade_Estoque'];
                    } else{
                         $quantidadeEstoque = $mT071['D009_Quantidade_Estoque'];
                    }
                    $Quantidade_Necessaria = $mT071['T071_Quantidade'] * $mT048['T048_Quantidade'];
                    if (round($Quantidade_Necessaria, 2) > round($quantidadeEstoque, 2)) {
                        if($mT071['T071_T066_Id'] > 0){
                            $mMsg[] = "Produto: {$mT048['D001_Codigo_Produto']} - Sub-Item: {$mT071['D001_Codigo_Produto']} | Estoque Locação: " . number_format($quantidadeEstoque, 2, ',', '.') . " | Necessário para Montagem: " . number_format($Quantidade_Necessaria, 2, ',', '.');
                        } else {
                            $mMsg[] = "Produto: {$mT048['D001_Codigo_Produto']} - Sub-Item: {$mT071['D001_Codigo_Produto']} | Estoque: " . number_format($quantidadeEstoque, 2, ',', '.') . " | Necessário para Montagem: " . number_format($Quantidade_Necessaria, 2, ',', '.');
                        }
                    }
                } else {
                    if($mT048['T048_T066_Id'] > 0){
                        $quantidadeEstoque = $mT048['T066_Quantidade_Estoque'];
                    } else {
                        $quantidadeEstoque = $mT048['D009_Quantidade_Estoque'];
                    }
                    $Quantidade_Necessaria = $mT048['T048_Quantidade'];
                    if (round($Quantidade_Necessaria, 2) > round($quantidadeEstoque, 2)) {
                        if($mT048['T048_T066_Id'] > 0){
                            $mMsg[] = "Produto: {$mT048['D001_Codigo_Produto']} - Sub-Item: {$mT048['D001_Codigo_Produto']} | Estoque Locação: " . number_format($quantidadeEstoque, 2, ',', '.') . " | Necessário para Desmontagem: " . number_format($Quantidade_Necessaria, 2, ',', '.');
                        } else {
                            $mMsg[] = "Produto: {$mT048['D001_Codigo_Produto']} - Sub-Item: {$mT048['D001_Codigo_Produto']} | Estoque: " . number_format($quantidadeEstoque, 2, ',', '.') . " | Necessário para Desmontagem: " . number_format($Quantidade_Necessaria, 2, ',', '.');
                        }                        
                    }
                }
            }
            
        }
        if ((count($mMsg) > 0) or (count($mMsg_Erro) > 0)) {
            
            if (count($mMsg_Erro) > 0) {
                $cMsg_Erro = implode('<br />', $mMsg_Erro);
                $cMensagem = "Os ítens abaixo não têm relacionamento com Estoque:<br />$cMsg_Erro";
                if (count($mMsg) > 0) {
                    $cMensagem .= "<br />" . str_repeat("-", 80) . "<br />";
                }
            }
            
            if (count($mMsg) > 0) {
                $cMsg = implode('<br />', $mMsg);
                $cMensagem .= "Quantidade insuficiente para montar/desmontar os itens abaixo:<br />$cMsg";
            }
            return $cMensagem;
            
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- ATUALIZA O HISTORICO
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        $cSql_Dolar = mysql_query("select D016_Dolar('') as Dolar");
        $mDolar     = mysql_fetch_array($cSql_Dolar);

        // Array para pegar o D009 dos subitens e processar o estoque depois que for processado o estoque do item montado
        $T075_D009_Id = array();
        
        $cSql_T048 = mysql_query("SELECT *
		                            FROM T048
		                       LEFT JOIN D049 ON D049_Id=T048_D049_Id
		                       LEFT JOIN D001 ON D001_Id=D049_D001_Id
		                       LEFT JOIN D009 ON D049_Id=D009_D049_Id AND D009_C004_Id='{$g['C004']['C004_Id']}'
		                           WHERE T048_T047_Id='{$T047_Id}'");

        while ($mT048 = mysql_fetch_array($cSql_T048)) {
            
            unset($mDemonstrativo_Custo);
            
            $cSql_T071 = mysql_query("SELECT *,
                                             D009_Valor_Custo_Compra AS Valor_Custo_Certo
		                                FROM T071
		                           LEFT JOIN D009 ON D009_Id=T071_D009_Id
		                           LEFT JOIN D049 ON D049_Id=D009_D049_Id
		                           LEFT JOIN D001 ON D001_Id=D049_D001_Id
		                           LEFT JOIN D002 ON D002_Id=D001_D002_Id
		                               WHERE T071_T048_Id='{$mT048['T048_Id']}'");
            
            $D009_Valor_Custo_Unitario_Principal = 0;

            while ($mT071 = mysql_fetch_array($cSql_T071)) {
                
                if ($mT048['T048_Flag_Tipo_Montagem'] == 'M') {
                    $T001_Observacao         = "Montagem Ok";
                    $T001_Descricao_Operacao = "Mont-$T047_Id";
                    $Flag_OperacaoT071       = "S";
                    $Flag_OperacaoT048       = "E";
                } else {
                    $T001_Observacao         = "Desmontagem Ok";
                    $T001_Descricao_Operacao = "Desmont-$T047_Id";
                    $Flag_OperacaoT071       = "E";
                    $Flag_OperacaoT048       = "S";
                }
                
                $Quantidade_Necessaria = $mT071['T071_Quantidade'] * $mT048['T048_Quantidade'];
                
                //                  if ( $mT071[D009_Quantidade_Estoque]>=$Quantidade_Necessaria ) {
                
                $Custo_Sub_Item = $mT071['Valor_Custo_Certo'] * $mT071['T071_Quantidade'];
                
                $D009_Valor_Custo_Unitario_Principal += $Custo_Sub_Item;
                
                $mDemonstrativo_Custo[] = "{$mT071['T071_Quantidade']},,{$mT071['D001_Codigo_Produto']},," . number_format($Custo_Sub_Item, 2, ',', '.');
                
                
                $cSql_D009 = mysql_query("SELECT *
		                                    FROM D009
		                               LEFT JOIN D049 ONT D009_D049_Id=D049_Id
		                                   WHERE D009_Id='{$mT071['D009_Id']}'");
                $mD009     = mysql_fetch_array($cSql_D009);
                
                ///////////////////////////////////////////////////////////////////////////////////////////////
                // ENCONTRA O ID DO T066
                ///////////////////////////////////////////////////////////////////////////////////////////////
                
                //$cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='{$mT071['D009_Id']}'");
                //$mT066     = mysql_fetch_array($cSql_T066);
                if($mT071['T071_T066_Id'] > 0){
                    $mT066['T066_Id'] = $mT071['T071_T066_Id'] ;
                } else {
                    $sqlT066 = <<<EOT
                                    SELECT T066_Id
                                      FROM T066
                                 LEFT JOIN D004 ON D004_Id = T066_D004_Id
                                 LEFT JOIN D146 ON D146_Id = D004_D146_Id
                                     WHERE T066_D009_Id='{$mT071['D009_Id']}'
                                  ORDER BY D146_Flag_Principal DESC
EOT;
                    $mT066 = mysql_fetch_array(mysql_query($sqlT066));              
                }

                $tmpD004_Id = getD004($mT066['T066_Id']);
                // ATUALIZA O HISTORICO DO PRODUTO

                $T001_Id    = mysql_query("insert into T001 (
		                                                            T001_D024_Id,
		                                                            T001_D009_Id,
		                                                            T001_C004_Id,
		                                                            T001_T048_Id,
		                                                            T001_T071_Id,
		                                                            T001_T066_Id,
		                                                            T001_Codigo_Produto,
		                                                            T001_Data_Lancamento,
		                                                            T001_Descricao_Operacao,
		                                                            T001_Quantidade,
		                                                            T001_Flag_Operacao,
		                                                            T001_Flag_Atualiza_Custo,
		                                                            T001_Valor_Preco_Unitario,
		                                                            T001_Observacao,
		                                                            T001_Valor_Custo_Unitario,
		                                                            T001_Quantidade_Saldo,
		                                                            T001_Flag_MMF,
		                                                            T001_D004_Id
		                                                            )
		                                                    values
		                                                            ('{$mD009['D049_D024_Id']}',
		                                                            '{$mT071['D009_Id']}',
		                                                            '{$g['empresaAtual']}',
		                                                            '{$mT048['T048_Id']}',
		                                                            '{$mT071['T071_Id']}',
		                                                            '{$mT066['T066_Id']}',
		                                                            '{$mD42['D001_Codigo_Produto']}',
		                                                            current_date(),
		                                                            '$T001_Descricao_Operacao',
		                                                            '$Quantidade_Necessaria',
		                                                            '$Flag_OperacaoT071',
		                                                            'N',
		                                                            '0',
		                                                            '$T001_Observacao',
		                                                            '{$mT071['Valor_Custo_Certo']}',
		                                                            '0',
		                                                            'S',
		                                                            '{$tmpD004_Id}')");

                // Array para pegar o D009 dos subitens e processar o estoque depois que for processado o estoque do item montado
                $T075_D009_Id[] = $mT071['D009_Id'];
            
                //                }
                
            }            
            $mDemonstrativo_Custo[] = ",,,,TOTAL,," . number_format($D009_Valor_Custo_Unitario_Principal, 2, ',', '.');
            $Demonstrativo_Custo    = implode("<br />", $mDemonstrativo_Custo);
            unset($mDemonstrativo_Custo);
            
            $cSql_D009 = mysql_query("select *
		                              from D009
		                         left join D049 on D009_D049_Id=D049_Id
		                             where D009_Id='{$mT048['D009_Id']}'");
            $mD009     = mysql_fetch_array($cSql_D009);
            
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // ENCONTRA O ID DO T066
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            //$cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='{$mT048['D009_Id']}'");
            //$mT066     = mysql_fetch_array($cSql_T066);

            if($mT048['T048_T066_Id'] > 0){
                $mT066['T066_Id'] = $mT048['T048_T066_Id'];
            } else {
                $sqlT066 = <<<EOT
                                SELECT T066_Id
                                 FROM T066
                            LEFT JOIN D004 ON D004_Id = T066_D004_Id
                            LEFT JOIN D146 ON D146_Id = D004_D146_Id
                                WHERE T066_D009_Id='{$mT048['D009_Id']}'
                            ORDER BY D146_Flag_Principal DESC
EOT;
                $mT066 = mysql_fetch_array(mysql_query($sqlT066));
            }

            
            $tmpD004_Id = getD004($mT066['T066_Id']);
            // ATUALIZA O HISTORICO DO PRODUTO
            $T001_Id    = mysql_query("insert into T001 (
		                                          T001_D024_Id,
		                                          T001_D009_Id,
		                                          T001_C004_Id,
		                                          T001_T048_Id,
		                                          T001_T066_Id,
		                                          T001_Codigo_Produto,
		                                          T001_Data_Lancamento,
		                                          T001_Descricao_Operacao,
		                                          T001_Quantidade,
		                                          T001_Flag_Operacao,
		                                          T001_Valor_Preco_Unitario,
		                                          T001_Observacao,
		                                          T001_Valor_Custo_Unitario,
		  					                      T001_Flag_Atualiza_Custo,
		                                          T001_Quantidade_Saldo,
		                                          T001_Demonstrativo_Custo,
		                                          T001_D004_Id
		                                          )
		                                  values
		                                          ('{$mD009['D049_D024_Id']}',
		                                          '{$mT048['D009_Id']}',
		                                          '{$g['empresaAtual']}',
		                                          '{$mT048['T048_Id']}',
		                                          '{$mT066['T066_Id']}',
		                                          '{$mT048['D001_Codigo_Produto']}',
		                                          current_date(),
		                                          '$T001_Descricao_Operacao',
		                                          '{$mT048['T048_Quantidade']}',
		                                          '$Flag_OperacaoT048',
		                                          '0',
		                                          '$T001_Observacao',
		                                          '$D009_Valor_Custo_Unitario_Principal',
		                                          'S',
		                                          '0',
		                                          '$Demonstrativo_Custo',
		                                          '{$tmpD004_Id}')");
            
            $retorno = $CAD002->D001_reprocessa_historico(false, $mT048['D009_Id'], 10);
            if ($retorno !== true) {
                return $retorno;
            }
        }
        
        // recalcula o historido dos subitens
        foreach($T075_D009_Id as $D009_Id){
            $retorno = $CAD002->D001_reprocessa_historico(false, $D009_Id, 10);
            if ($retorno !== true) {
                return $retorno;
            }
        }
        
        mysql_query("UPDATE T047 SET T047_Flag_Status='A' WHERE T047_Id='{$T047_Id}'");        
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * xD001_reprocessa_historico
     *
     * @param int $D001_Id
     * @param int $D009_Id
     */
    public function xD001_reprocessa_historico($D001_Id = 0, $D009_Id = 0)
    {
        global $g;
        
        $cSql_D009 = mysql_query("select D009_Id,
	                                   D009_Quantidade_Estoque,
	                                   D009_Valor_Custo_Medio_Unitario,
	                                   D009_Valor_Custo_Compra,
	                                   D009_Valor_Melhor_Custo,
	                                   D049_Flag_Nacional_Importado,
	                                   D049_Flag_Tipo,
	                                   D049_Valor_Preco_Tabela,
	                                   D049_Percentual_Desconto_Tabela,
	                                   D082_Percentual_Acrescimo,
	                                   D049_Aliquota_ICMS_Tabela,
	                                   D049_Id,
	                                   D049_D001_Id,
	                                   D049_ICF,
	                                   D049_IPV,
	                                   C008_Flag_Custo
	                              from D009 as T1
	                          left join D049 on D049_Id=D009_D049_Id
	                          left join D001 on D001_Id=D049_D001_Id
	                          left join C008 on C008_Id=D001_C008_Id
	                          left join D082 on D082_Id=D049_D082_Id
	                              where (D049_D001_Id='$D001_Id'
	                                 or D009_Id='$D009_Id')
	                                and D009_C004_Id='{$g['C004']['C004_Id']}'");
        
        $C020  = mysql_query("select * from C020 where C020_C004_Id='{$g['C004']['C004_Id']}'");
        $mC020 = mysql_fetch_array($C020);
        
        if ($mC020['C020_IPV_Custo'] <= 0) {
            $mC020['C020_IPV_Custo'] = 1;
        }
        
        if (mysql_num_rows($cSql_D009) <= 0) {
            return 'O produto não foi encontrado D009_Id';
        }
        
        while ($mD009 = mysql_fetch_array($cSql_D009)) {
            
            $Total_Produtos++;
            $cSql_T001   = mysql_query("select T001_Quantidade,
	                                           T001_Flag_Operacao,
	                                           T001_Flag_Atualiza_Custo,
	                                           T001_Quantidade_Saldo,
	                                           T001_Valor_Custo_Unitario,
	                                           T001_Valor_Custo_Medio_Unitario,
	                                           T001_Id,
	                                           T001_T066_Id,
	                                           T001_Flag_MMF,
	                                           T001_T008_Id,
	                                           T001_Descricao_Operacao,
	                                           T008_Valor_Custo_Unitario,
	                                           T008_Valor_Preco_Unitario,
	                                           T008_IPV,
	                                           T007_D006_Id,
	                                           T055_Quantidade,
	                                           T001_D024_Id,
	                                           T001_Data_Lancamento,
	                                           if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario,
	                                           T008_Quantidade,
	                                           D006_Flag_Venda_Compra_Outros
	                                     from T001
	                                left join T008 on T008_Id=T001_T008_Id
	                                left join T007 on T007_Id=T008_T007_Id
	                                left join T055 on T055_Id=T001_T055_Id
	                                left join D006 on D006_Id=T001_D006_Id
	                                    where T001_D009_Id='{$mD009['D009_Id']}'
	                                 order by T001_Data_Lancamento,T001_Id");
            $Saldo       = 0;
            $Custo       = 0;
            $Custo_Medio = 0;
            while ($mT001 = mysql_fetch_array($cSql_T001)) {
                
                if ($mT001['T001_Flag_Operacao'] != 'C') {
                    
                    if (($mT001['T007_D006_Id'] == 19) and ($g['empresaAtual'] == 1)) {
                        if ($mT001['T001_Quantidade'] <= 0) {
                            $Custo_Medio = ((($Saldo - $mT001['T008_Quantidade']) * $Custo_Medio) + ($mT001['T001_Valor_Preco_Unitario'] * $mT001['T008_Quantidade'])) / ($Saldo);
                        } else {
                            $Custo_Medio = (($Saldo * $Custo_Medio) + ($mT001['T001_Valor_Preco_Unitario'] * $mT001['T001_Quantidade'])) / ($Saldo + $mT001['T001_Quantidade']);
                        }
                    }
                    
                    if (($mT001['T001_Flag_Atualiza_Custo'] == 'S') and ($mT001['T001_Flag_Operacao'] != 'S')) {
                        if ($mT001['T001_Valor_Custo_Medio_Unitario'] <= 0) {
                            if (($Saldo + $mT001['T001_Quantidade']) > 0) {
                                $Custo_Medio = (($Saldo * $Custo_Medio) + ($mT001['T001_Valor_Preco_Unitario'] * $mT001['T001_Quantidade'])) / ($Saldo + $mT001['T001_Quantidade']);
                            } else {
                                $Custo_Medio = $mT001['T001_Valor_Preco_Unitario'];
                            }
                        } else {
                            $Custo_Medio = $mT001['T001_Valor_Custo_Medio_Unitario'];
                        }
                        $Custo = $mT001['T001_Valor_Preco_Unitario'];
                        
                        $D024_Id            = $mT001['T001_D024_Id'];
                        $D024_Id_Fornecedor = $mT001['T001_D024_Id'];
                        $mFD024[$D024_Id]   = $D024_Id;
                    }
                    
                    $T066_Id = $mT001['T001_T066_Id'];
                    if ($mT001['T001_Flag_Operacao'] == 'S') {
                        $Saldo -= $mT001['T001_Quantidade'];
                        $mSaldo_T066[$T066_Id] -= $mT001['T001_Quantidade'];
                        if ($mT001['D006_Flag_Venda_Compra_Outros'] == 'V') {
                            $D024_Id          = $mT001['T001_D024_Id'];
                            $mCD024[$D024_Id] = $D024_Id;
                        }
                    } else if ($mT001['T001_Flag_Operacao'] == 'E') {
                        $Saldo += $mT001['T001_Quantidade'];
                        $mSaldo_T066[$T066_Id] += $mT001['T001_Quantidade'];
                    } else if ($mT001['T001_Flag_Operacao'] == 'B') {
                        $Saldo                 = $mT001['T001_Quantidade'];
                        $mSaldo_T066[$T066_Id] = $mT001['T001_Quantidade'];
                    }
                }
                
                if (($mT001['T001_Flag_MMF'] == 'S') and ($mD009['D049_Flag_Nacional_Importado'] == 'I')) {
                    $D016        = mysql_query("select D016_Valor_Engepecas from D016 where D016_Data<='{$mT001['T001_Data_Lancamento']}' order by D016_Data desc limit 1");
                    $mD016       = mysql_fetch_array($D016);
                    $Custo_Reais = $Custo * $mD016[0];
                } else {
                    $Custo_Reais = $Custo;
                }
                
                if ($Saldo <= 0) {
                    $Custo       = 0;
                    $Custo_Reais = 0;
                    $Custo_Medio = 0;
                }
                
                if ((number_format($Saldo, 2) != number_format($mT001['T001_Quantidade_Saldo'], 2)) or (number_format($Custo_Medio, 2) != number_format($mT001['T001_Valor_Custo_Medio_Unitario'], 2)) or ((number_format($mT001['T001_Valor_Custo_Unitario'], 2) != number_format($Custo_Reais, 2)) and ($mT001['T001_Flag_MMF'] == 'S')) or (($mT001['T001_Valor_Custo_Unitario'] <= 0) and ($Custo_Reais > 0))) {
                    mysql_query("update T001 set T001_Quantidade_Saldo='$Saldo',
	                                                   T001_Valor_Custo_Unitario='$Custo_Reais',
	                                                   T001_Valor_Custo_Medio_Unitario='$Custo_Medio'
	                                             where T001_Id='{$mT001['T001_Id']}'");
                }
                
                /*                  if (($mT001[T001_T008_Id]>0) and
                ((number_format($mT001[T008_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) or ($mT001[T008_IPV]<=0)) and
                ($mT001[T001_Flag_MMF]=='S'))
                {
                mysql_query("update T008 set T008_Valor_Custo_Unitario='$Custo_Reais',
                T008_IPV=(T008_Valor_Preco_Unitario-round(T008_Valor_ICMS/T008_Quantidade,2)-T008_Valor_Desconto_Unitario)/round(T008_Valor_Custo_Unitario,2),
                T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade where T008_Id='$mT001[T001_T008_Id]'");
                } */
            }
            
            $Custo_Compra = $Custo;
            
            $D069 = mysql_query("select (D069_Preco_Unitario-(D069_Preco_Unitario*(D069_Percentual_Desconto/100))) D069_Preco_Unitario,
	                                       D069_D070_Id
	                                  from D069
	                             left join D070 on D070_Id=D069_D070_Id
	                                 where D069_D009_Id='{$mD009['D009_Id']}'
	                                   and D070_Flag_Ativo='S'
	                                 order by (D069_Preco_Unitario-(D069_Preco_Unitario*(D069_Percentual_Desconto/100)))");
            
            $mD069 = mysql_fetch_array($D069);
            
            if ($mD009['D049_Flag_Tipo'] == 'MD') {
                $D049         = mysql_query("select D009_Valor_Custo_Compra,
	                                          D049_ICF,
	                                          D049_Valor_Preco_Tabela,
	                                          D049_Percentual_Desconto_Tabela,
	                                          D049_Aliquota_ICMS_Tabela,
	                                          C008_Flag_Custo
	                                          from D009
	                                     left join D049 on D049_Id=D009_D049_Id
	                                     left join D001 on D001_Id=D049_D001_Id
	                                     left join C008 on C008_Id=D001_C008_Id
	                                         where D049_D001_Id='{$mD009['D049_D001_Id']}' and D049_Flag_Tipo='CP' and D009_C004_Id='{$g['C004']['C004_Id']}'");
                $mD049        = mysql_fetch_array($D049);
                $Custo_Tabela = 0;
                if ($mD049['D049_Valor_Preco_Tabela'] > 0) {
                    $Custo_Tabela = $mD049['D049_Valor_Preco_Tabela'] - ($mD049['D049_Valor_Preco_Tabela'] * ($mD049['D049_Percentual_Desconto_Tabela'] / 100));
                    $Custo_Tabela = $Custo_Tabela - ($Custo_Tabela * ($mD049['D049_Aliquota_ICMS_Tabela'] / 100));
                }
                $Custo = $mD049['D009_Valor_Custo_Compra'];
                if (($mD049['C008_Flag_Custo'] == 'TABELA' and $Custo_Tabela > 0) or ($Custo <= 0)) {
                    $Custo = $Custo_Tabela;
                }
                if ($mD009['D082_Percentual_Acrescimo'] > 0) {
                    $Custo = $Custo * (1 + ($mD009['D082_Percentual_Acrescimo'] / 100));
                }
            } else {
                if ($mD009['D049_Valor_Preco_Tabela'] > 0) {
                    $Custo_Tabela = $mD009['D049_Valor_Preco_Tabela'] - ($mD009['D049_Valor_Preco_Tabela'] * ($mD009['D049_Percentual_Desconto_Tabela'] / 100));
                    $Custo_Tabela = $Custo_Tabela - ($Custo_Tabela * ($mD009['D049_Aliquota_ICMS_Tabela'] / 100));
                    if (($mD009['C008_Flag_Custo'] == 'TABELA' and $Custo_Tabela > 0) or ($Custo <= 0)) {
                        $Custo = $Custo_Tabela;
                    }
                } else if ($mD009['D049_Flag_Tipo'] == 'MT') {
                    $D042 = mysql_query("select D042_Id
	                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
	                                                  left join D082 on D049_D082_Id=D082_Id
	                                                  left join D001 on D001_Id=D049_D001_Id
	                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
	                                            where D042_D049_Id='{$mD009['D049_Id']}'
	                                              and D042_C004_Id='{$g['C004']['C004_Id']}' 
	                                              and D009_C004_Id='{$g['C004']['C004_Id']}'
	                                              and (D009_Valor_Custo_Compra*D042_Quantidade)<=0");
                    if (mysql_num_rows($D042) <= 0) {
                        $D042  = mysql_query("select sum(D009_Valor_Custo_Compra*D042_Quantidade)
		                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
		                                                  left join D082 on D049_D082_Id=D082_Id
		                                                  left join D001 on D001_Id=D049_D001_Id
		                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
		                                            where D042_D049_Id='{$mD009['D049_Id']}'
		                                              and D042_C004_Id='{$g['C004']['C004_Id']}' 
		                                              and D009_C004_Id='{$g['C004']['C004_Id']}'");
                        $mD042 = mysql_fetch_array($D042);
                        $Custo = $mD042[0];
                    }
                }
            }
            if ($Custo < 0) {
                $Custo = 0;
            }
            
            if ($mD009['D049_ICF'] <= 0) {
                $mD009['D049_ICF'] = 0;
            }
            
            mysql_query("update D009
	                       left join D049 on D049_Id=D009_D049_Id
	                       left join D082 on D082_Id=D049_D082_Id
	                       left join D001 on D001_Id=D049_D001_Id
	                       left join C008 on C008_Id=D001_C008_Id
	                             set D009_Valor_Custo_Medio_Unitario='$Custo_Medio',
	                                 D009_Valor_Custo_Compra='$Custo',
	                                 D009_Valor_Custo_Ultima_Compra='$Custo_Compra',
	                                 D009_Valor_Melhor_Custo='{$mD069['D069_Preco_Unitario']}',
	                                 D009_D070_Id='{$mD069['D069_D070_Id']}',
	                                 D009_Valor_Custo_Unitario=D009_Valor_Custo_Compra*if({$mD009['D049_ICF']}>0,{$mD009['D049_ICF']},if(D082_ICF>0,D082_ICF,if(C008_ICF>0,C008_ICF,{$mC020['C020_IPV_Custo']}))),
	                                 D009_Quantidade_Estoque='$Saldo'
	                           where D009_Id='{$mD009['D009_Id']}'");
            
            mysql_query("update D049 set D049_D024_Id='$D024_Id_Fornecedor' where D049_Id='{$mD009['D049_Id']}'");
            
            
            if (count($mSaldo_T066) > 0) {
                $mT066_Ids = array_keys($mSaldo_T066);
                for ($i = 0; $i <= count($mT066_Ids); $i++) {
                    $T066_Id = $mT066_Ids[$i];
                    if ($T066_Id > 0) {
                        mysql_query("update T066 set T066_Quantidade_Estoque='{$mSaldo_T066['$T066_Id']}' where T066_Id='$T066_Id'");
                    }
                }
            }
            if ($mD009['D049_IPV'] > 0) {
                mysql_query("update D041
		                       left join D049 on D049_Id=D041_D049_Id
		                       left join D009 on D009_D049_Id=D049_Id
		                       left join D082 on D082_Id=D049_D082_Id
		                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*{$mD009['D049_IPV']},
		                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*{$mD009['D049_IPV']},
		                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*{$mD009['D049_IPV']}
		                           where D009_Id='{$mD009['D009_Id']}'
		                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
            } else if ($mC020['C020_IPV_Revenda'] > 0) {
                mysql_query("update D041
		                       left join D049 on D049_Id=D041_D049_Id
		                       left join D009 on D009_D049_Id=D049_Id
		                       left join D082 on D082_Id=D049_D082_Id
		                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*{$mC020['C020_IPV_Revenda']},
		                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*{$mC020['C020_IPV_Atacado']},
		                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*{$mC020['C020_IPV_Varejo']}
		                           where D009_Id='{$mD009['D009_Id']}'
		                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
            }
            
            // Cadastra os fornecedores do produto
            if (count($mFD024) > 0) {
                $mD024_Ids = array_keys($mFD024);
                for ($i = 0; $i <= count($mD024_Ids); $i++) {
                    $D024_Id = $mD024_Ids[$i];
                    if ($D024_Id > 0) {
                        /*
                        $D083 = mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mD009['D049_D001_Id']}' and D083_D049_Id='{$mD009['D049_Id']}' and D083_D024_Id='$D024_Id'");
                        if (mysql_num_rows($D083) <= 0) {
                            mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor) values ('{$mD009['D049_Id']}','{$mD009['D049_D001_Id']}','$D024_Id',curdate(),'S')");
                        } else {
                            $mD083 = mysql_fetch_array($D083);
                            if ($mD083['D083_Flag_Fornecedor'] != 'S') {
                                mysql_query("update D083 set D083_Flag_Fornecedor='S' where D083_Id='{$mD083['D083_Id']}'");
                            }
                        }
                        */
                        require_once("bibliotecas/classes/CAD002.php");
                        $CAD002 = new CAD002();
                        $CAD002->definirCodigoFornecedor($mD009['D009_Id'], $D024_Id, '', '', false, true);
                    }
                }
            }
            
            // Cadastra os fornecedores do produto
            if (count($mCD024) > 0) {
                $mD024_Ids = array_keys($mCD024);
                for ($i = 0; $i <= count($mD024_Ids); $i++) {
                    $D024_Id = $mD024_Ids[$i];
                    if ($D024_Id > 0) {
                        /*
                        $D083 = mysql_query("select D083_Id,D083_Flag_Cliente from D083 where D083_D001_Id='{$mD009['D049_D001_Id']}' and D083_D049_Id='{$mD009['D049_Id']}' and D083_D024_Id='$D024_Id'");
                        if (mysql_num_rows($D083) <= 0) {
                            mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente) values ('{$mD009['D049_Id']}','{$mD009['D049_D001_Id']}','$D024_Id',curdate(),'S')");
                        } else {
                            $mD083 = mysql_fetch_array($D083);
                            if ($mD083['D083_Flag_Cliente'] != 'S') {
                                mysql_query("update D083 set D083_Flag_Cliente='S' where D083_Id='{$mD083['D083_Id']}'");
                            }
                        }
                        */
                        require_once("bibliotecas/classes/CAD002.php");
                        $CAD002 = new CAD002();
                        $CAD002->definirCodigoFornecedor($mD009['D009_Id'], $D024_Id, '', '', true, false);
                    }
                }
            }
        }
        
        return true;
        
        /*    mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id
        left join D001 on D001_Id=D049_D001_Id
        set D009_Quantidade_Minima_Estoque=D009_Minimo_Maximo(D009_Id,1),
        D009_Quantidade_Maxima_Estoque=D009_Minimo_Maximo(D009_Id,2),
        D009_Frequencia_Venda=D009_Frequencia(D009_Id),
        D009_Data_Ultima_Venda=(select T001_Data_Lancamento
        from T001
        where T001_Flag_MMF='S'
        and T001_D009_Id=D009_Id
        order by T001_Data_Lancamento desc limit 1),
        D009_Data_Ultimo_Movimento=(select T001_Data_Lancamento
        from T001
        where (T001_Flag_MMF='S'
        or T001_Flag_Atualiza_Custo='S')
        and T001_D009_Id=D009_Id
        order by T001_Data_Lancamento desc limit 1),
        D009_Data_Ultima_Entrada=(select T001_Data_Lancamento
        from T001
        where T001_Flag_Atualiza_Custo='S'
        and T001_D009_Id=D009_Id
        order by T001_Data_Lancamento desc limit 1)
        where (D001_Id='$D001_Id'
        or D009_Id='$D009_Id')
        and D009_C004_Id='$g['empresaAtual']'");
        
        
        
        if (!empty($D001_Id)) {
        mysql_query("update D049
        set D049_Data_Ultima_Venda=(select D009_Data_Ultima_Venda
        from D009
        where D009_D049_Id=D049_Id
        and D009_Data_Ultima_Venda!='0000-00-00'
        order by D009_Data_Ultima_Venda limit 1)
        where D049_D001_Id='$D001_Id'");
        } */
        
    }

    /**
     * est007GerarMontagemEmergencia
     *
     * @param string $sql
     * @return T047_Id inserido
     */
    public function est007GerarMontagemEmergencia($sql)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        mysql_query("INSERT INTO T047 (T047_C004_Id, T047_C007_Id, T047_Data, T047_Observacao) VALUES ('{$g['empresaAtual']}', '{$g['usuarioAtual']}', CURDATE(), 'Montagem de emergência')");        
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("rollback");
            return "Erro: $erro <br /> $cClausula";
        }

        $T047_Id = $g['mysqlLastId'];
        
        //Select dos itens da cotação
        $res = mysql_query($sql);
        while($row = mysql_fetch_array($res)){
            if ($row['T006_Flag_Gerar_Cotacao'] != 'S' || ($row['T006_Flag_Gerar_Cotacao'] == 'S' && $row['T006_C007_Id_Comprador'] != $g['usuarioAtual'])){
                continue;
            }
            //Insere o item
            $cClausula = " INSERT INTO T048 (";
            $cClausula .= " T048_D049_Id, ";
            $cClausula .= " T048_T047_Id, ";
            $cClausula .= " T048_C004_Id, ";
            $cClausula .= " T048_Quantidade, ";
            $cClausula .= " T048_Codigo_Produto, ";
            $cClausula .= " T048_Observacao ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$row['D049_Id']}', ";
            $cClausula .= " '{$T047_Id}', ";
            $cClausula .= " '{$g['empresaAtual']}', ";
            $cClausula .= " '{$row['Falta']}', ";
            $cClausula .= " '{$row['D001_Codigo_Produto']}', ";
            $cClausula .= " '' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("rollback");
                return "Erro: $erro <br /> $cClausula";
            }
            $T048_Id = $g['mysqlLastId'];
            
            $cClausula = " SELECT";
            $cClausula .= " D042_Id, ";
            $cClausula .= " D042_D049_Id_Itens, ";
            $cClausula .= " D009_Id, ";
            $cClausula .= " D042_Quantidade, ";
            $cClausula .= " D001_Codigo_Produto ";
            $cClausula .= " from D042 ";
            $cClausula .= " left join D009 on D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id='{$g['empresaAtual']}' ";
            $cClausula .= " left join D049 on D049_Id=D042_D049_Id_Itens ";
            $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
            $cClausula .= " where D042_D049_Id='{$row['D049_Id']}' ";
            $cClausula .= " and D009_C004_Id='{$g['empresaAtual']}' ";
            
            $cSql_D042 = mysql_query($cClausula);
            $erro     = mysql_error();
            if (!empty($erro)) {
                mysql_query("rollback");
                return "Erro: $erro<br />$cClausula";
            }
            
            // insere os sub-itens do item selecionado
            while ($mD042 = mysql_fetch_array($cSql_D042)) {
                $cClausula = " insert into T071 (";
                $cClausula .= " T071_T048_Id, ";
                $cClausula .= " T071_D009_Id, ";
                $cClausula .= " T071_Quantidade, ";
                $cClausula .= " T071_Codigo_Produto ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '${T048_Id}', ";
                $cClausula .= " '{$mD042['D009_Id']}', ";
                $cClausula .= " '{$mD042['D042_Quantidade']}', ";
                $cClausula .= " '{$mD042['D001_Codigo_Produto']}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br />$cClausula";
                }
            }
        }
        
        mysql_query("COMMIT");
        return $T047_Id;
    }

    /**
     * est007GerarMontagemEstoque
     *
     * @param string $sql
     * @return T047_Id inserido
     */
    public function est007GerarMontagemEstoque($sql)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        mysql_query("INSERT INTO T047 (T047_C004_Id, 
                                       T047_C007_Id, 
                                       T047_Data, 
                                       T047_Observacao) 
                               VALUES ('{$g['empresaAtual']}',
                                       '{$g['usuarioAtual']}',
                                       CURDATE(),
                                       'MONTAGEM ESTOQUE')");        
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("rollback");
            return "Erro: {$erro}";
        }

        $T047_Id = $g['mysqlLastId'];
        
        //Select dos itens da cotação
        $res = mysql_query($sql);
        while($row = mysql_fetch_array($res)){
            if ($row['D009_Flag_Cotar'] != 'S' || ($row['D009_Flag_Cotar'] == 'S' && $row['D009_C007_Id_Comprar'] != $g['usuarioAtual'])) {
                continue;
            }

            $QuantidadeComprar = ($row['D009_Quantidade_Comprar'] > 0) ? $row['D009_Quantidade_Comprar'] : $row['D009_Quantidade_Comprar_Calculada'];

            //Insere o item
            $cClausula = " INSERT INTO T048 (";
            $cClausula .= " T048_D049_Id, ";
            $cClausula .= " T048_T047_Id, ";
            $cClausula .= " T048_C004_Id, ";
            $cClausula .= " T048_Quantidade, ";
            $cClausula .= " T048_Codigo_Produto, ";
            $cClausula .= " T048_Observacao ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$row['D049_Id']}', ";
            $cClausula .= " '{$T047_Id}', ";
            $cClausula .= " '{$g['empresaAtual']}', ";
            $cClausula .= " '{$QuantidadeComprar}', ";
            $cClausula .= " '{$row['D001_Codigo_Produto']}', ";
            $cClausula .= " '' ";
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("rollback");
                return "Erro: $erro <br /> $cClausula";
            }

            $T048_Id = $g['mysqlLastId'];

            mysql_query("UPDATE D009 SET D009_Flag_Cotar         = 'N', 
                                         D009_C007_Id_Comprar    = '0',
                                         D009_Quantidade_Comprar = '0' 
                                   WHERE D009_Id = '{$row['D009_Id']}'");

            
            $cClausula = " SELECT";
            $cClausula .= " D042_Id, ";
            $cClausula .= " D042_D049_Id_Itens, ";
            $cClausula .= " D009_Id, ";
            $cClausula .= " D042_Quantidade, ";
            $cClausula .= " D001_Codigo_Produto ";
            $cClausula .= " from D042 ";
            $cClausula .= " left join D009 on D009_D049_Id=D042_D049_Id_Itens and D009_C004_Id='{$g['empresaAtual']}' ";
            $cClausula .= " left join D049 on D049_Id=D042_D049_Id_Itens ";
            $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
            $cClausula .= " where D042_D049_Id='{$row['D049_Id']}' ";
            $cClausula .= " and D009_C004_Id='{$g['empresaAtual']}' ";
            
            $cSql_D042 = mysql_query($cClausula);
            $erro     = mysql_error();
            if (!empty($erro)) {
                mysql_query("rollback");
                return "Erro: $erro<br />$cClausula";
            }
            
            // insere os sub-itens do item selecionado
            while ($mD042 = mysql_fetch_array($cSql_D042)) {
                $cClausula = " insert into T071 (";
                $cClausula .= " T071_T048_Id, ";
                $cClausula .= " T071_D009_Id, ";
                $cClausula .= " T071_Quantidade, ";
                $cClausula .= " T071_Codigo_Produto ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '{$T048_Id}', ";
                $cClausula .= " '{$mD042['D009_Id']}', ";
                $cClausula .= " '{$mD042['D042_Quantidade']}', ";
                $cClausula .= " '{$mD042['D001_Codigo_Produto']}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br />$cClausula";
                }
            }
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        return $T047_Id;
    }
}
