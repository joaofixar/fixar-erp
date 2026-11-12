<?php
namespace hardness;

definirClasse('EST017');
class EST017_
{
    /**
     * gerarOrdemProducao
     *
     * @param string $sql
     * @param int $nivelOp
     * @param int $idOpPai
     * @return true/string
     */
    public function gerarOrdemProducao($sql, $T256_Id_Pai = 0, $nivelOp = 0)
    {
        global $g, $T255_Id;
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        $res = mysql_query($sql);
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro ao verificar produtos selecionados, por favor entre em contato com o suporte técnico.";
        }

        if(mysql_num_rows($res) <= 0){
            return "Nenhum produto foi selecionado, por favor verifique!";
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        //Insere cabeçalho Ordem produção
        mysql_query("INSERT INTO T255 (T255_C004_Id, T255_C007_Id, T255_Data_Emissao, T255_Flag_Status) VALUES ('{$g['empresaAtual']}', '{$g['usuarioAtual']}', current_date(), '0')");
        $erro = mysql_error();
        if(!empty($erro)){
            mysql_query("ROLLBACK");
            return "Erro ao gravar o cabeçalho da ordem de produção, por favor entre em contato com o suporte técnico.";
        }
        $T255_Id = $g['mysqlLastId'];

        //Grava a t255a vinculando com a t255
        mysql_query("INSERT INTO T255a (T255A_T255_Id) VALUES ('{$T255_Id}')");

        //Insere itens da ordem de produção
        $pedidos = array();
        while($row = mysql_fetch_array($res)){
            //Insere o item na ordem de produção

            mysql_query("INSERT INTO T256 (T256_T255_Id, T256_D009_Id, T256_Quantidade, T256_Quantidade_Saldo, T256_T006_Id) VALUES ('{$T255_Id}', '{$row['D009_Id']}', '{$row['Saldo']}', '{$row['Saldo']}', '{$row['T006_Id']}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (1)";
            }
            $T256_Id = $g['mysqlLastId'];
            
            if($row['T006_Id'] > 0){ // Se veio da produção de emergência, com um pedido vinculado
                mysql_query("insert into 
                                T256A 
                                (
                                    T256A_T256_Id,
                                    T256A_T256_Id_Op_Pai,
                                    T256A_Data_Inicio
                                ) 
                                values 
                                (
                                    '{$T256_Id}',
                                    '0',
                                    current_date();
                                )");
            }

            if ($T256_Id_Pai > 0) {
                $this->gravarOpPai($T256_Id, $T256_Id_Pai);
            }

            //Verifica se a cópia da composição já está inserida na tabela T261
            $composicaoJaInserida = false;
            if ($row['T006_Id'] > 0) {
                $sqlT261 = mysql_query("SELECT * FROM T261 WHERE T261_T006_Id = '{$row['T006_Id']}'");
                $composicaoJaInserida = mysql_num_rows($sqlT261) > 0;
            }

            if($composicaoJaInserida){
                while($rowT261 = mysql_fetch_array($sqlT261)){
                    mysql_query("UPDATE T261 SET T261_T256_Id = '{$T256_Id}' WHERE T261_Id = '{$rowT261['T261_Id']}'");
                    $erro = mysql_error();
                    if(!empty($erro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (2.1)";
                    }
                }
            } else {
                //Copia a tabela de matéria prima do produto
                mysql_query("INSERT INTO T261 (
                                         T261_D001_Id_Composicao, 
                                         T261_Quantidade, 
                                         T261_T256_Id, 
                                         T261_T006_Id) 
                                  SELECT D074_D001_Id_Composicao, 
                                         D074_Quantidade, 
                                         '{$T256_Id}', 
                                         '{$row['T006_Id']}' 
                                    FROM D074 
                               LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao -- id dos produtos comprados (substituir por D074_D001_Id_Composicao)
                               LEFT JOIN D049 ON D001_Id = D049_D001_Id
                               LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                   WHERE D074_D001_Id = '{$row['D001_Id']}'
                                     AND D009_C004_Id='{$g['empresaAtual']}'");
                             
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (2.2)";
                } 
                $T261_Id = $g['mysqlLastId'];
                //mysql_query("INSERT INTO T261A (T261A_T261_Id, T261A_T006_Id) VALUES ('{$T261_Id}', '{$row['T006_Id']}')");


                mysql_query("INSERT INTO T261A (T261A_T261_Id, T261A_T006_Id, T261A_T256_Id) 
                                 SELECT  T261_Id, 
                                         T261_T006_Id,
                                         T261_T256_Id'
                                    FROM T261 
                                   WHERE T261_T256_Id     = '{$T256_Id}'
                                     AND T261_T006_Id = '{$row['T006_Id']}'");
            }

            //Verifica se a cópia da composição já está inserida na tabela T261
            $sqlT262 = mysql_query("SELECT * FROM T262 WHERE T262_T006_Id = '{$row['T006_Id']}'");
            if(mysql_num_rows($sqlT262) > 0){
                while($rowT262 = mysql_fetch_array($sqlT262)){
                    mysql_query("UPDATE T262 SET T262_T256_Id = '{$T256_Id}' WHERE T262_Id = '{$rowT262['T262_Id']}'");
                    $erro = mysql_error();
                    if(!empty($erro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (3)";
                    }
                }
            } else {
                //Copia a tabela de processos de produção do produto
                $sqlD075 = mysql_query("SELECT D075_Id FROM D075 WHERE D075_D001_Id = '{$row['D001_Id']}'");
                while($rowD075 = mysql_fetch_assoc($sqlD075)){
                    mysql_query("INSERT INTO T262 (T262_D079_Id, T262_D078_Id, T262_Lead_Time, T262_Descricao, T262_Ordem, T262_Numero_Funcionarios, T262_T256_Id) SELECT D075_D079_Id, D075_D078_Id, D075_Lead_Time, D075_Descricao, D075_Ordem, D075_Numero_Funcionarios, '{$T256_Id}' FROM D075 WHERE D075_Id = '{$rowD075['D075_Id']}'");
                    $erro = mysql_error();
                    if(!empty($erro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (4)";
                    }
                    $T262_Id = $g['mysqlLastId'];

                    $parametrosGatilho = array($T262_Id, $rowD075['D075_Id']);
                    $erro = $this->gatilhoT262AposInsert($parametrosGatilho);

                    if(!empty($erro)){
                        return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (gatilhoT262AposInsert). Erro: {$erro}";
                    }

                    //Copia a tabela de material de consumo dos processos de produção do produto
                    mysql_query("INSERT INTO T263 (T263_T262_Id, T263_D009_Id, T263_Quantidade) SELECT '{$T262_Id}', D076_D009_Id, D076_Quantidade FROM D076 WHERE D076_D075_Id = '{$rowD075['D075_Id']}'");
                    $erro = mysql_error();
                    if(!empty($erro)){
                        mysql_query("ROLLBACK");
                        return "Erro ao gravar o item da ordem de produção, por favor entre em contato com o suporte técnico. (5)";
                    }
                }
            }
            $CAD002->D001_reprocessa_historico(0, $row['D009_Id']);
            if($row['T005_Id'] > 0 && !in_array($row['T005_Id'], $pedidos)){
                $pedidos[] = $row['T005_Id'];
            }
        }

        $this->processarProdutosBaixa($T255_Id, false);

        foreach ($pedidos as $T005_Id){
            $sqlT259 = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_Entidade = 'T005' AND D085_T005_Id = '{$T005_Id}'");
            $rowT259 = mysql_fetch_assoc($sqlT259);
            mysql_query("INSERT INTO D085 (D085_C007_Id, D085_Entidade, D085_Id_Entidade, D085_T259_Id, D085_Data, D085_Hora, D085_Mensagem) VALUES ('{$g['usuarioAtual']}', 'T255', '{$T255_Id}', '{$rowT259['D085_T259_Id']}', current_date(), current_time(), 'OP {$T255_Id}')");
        }

        mysql_query("COMMIT");

        $this->gerarOrdemProducaoComposicao($T255_Id, 0, $nivelOp);

        return true;
    }

    public function gravarOpPai($T256A_T256_Id, $T256A_T256_Id_Op_Pai) {

        $sqlFindT256A = " SELECT T256A_Id FROM T256A WHERE T256A_T256_Id = {$T256A_T256_Id} ";
        $query = mysql_query($sqlFindT256A);

        if (mysql_num_rows($query) <= 0) {
            $sqlT256A = " INSERT INTO T256A (T256A_T256_Id, T256A_T256_Id_Op_Pai, T256A_Data_Inicio) VALUES ({$T256A_T256_Id}, {$T256A_T256_Id_Op_Pai}, current_date()) ";
        } else {
            $sqlT256A = "UPDATE T256A SET T256A_T256_Id_Op_Pai = {$T256A_T256_Id} WHERE T256A_T256_Id = {$T256A_T256_Id_Op_Pai} ";
        }

        mysql_query($sqlT256A);

        $dadosT006_Id = mysql_query("select 
                                        T256_T006_Id
                                    from
                                        T256
                                    where
                                        T256_Id = '{$T256A_T256_Id_Op_Pai}'");
        $varT006_Id = mysql_fetch_array($dadosT006_Id);
        $T006_Id    = $varT006_Id['T256_T006_Id'];

        mysql_query("update
                        T256
                    set
                        T256_T006_Id = '{$T006_Id}'
                    where
                        T256_Id = '{$T256A_T256_Id}'");
    }

    /**
     * gerarOrdemProducaoComposicao
     *
     * @param @param string $T255_Id
     * @return true/string
     */
    public function gerarOrdemProducaoComposicao($T255_Id, $T256_Id, $nivelOp) {

        global $g;
        $paramNivelMaximoComposicaoGeracaoOP = $g['C031']['nivelMaximoComposicaoGeracaoOP'];

        $nivelOp++;

        if ($paramNivelMaximoComposicaoGeracaoOP > 0 && $nivelOp <= $paramNivelMaximoComposicaoGeracaoOP) {

            $sqlT261 = "SELECT 
                            D009_Id, 
                            T261_Id, 
                            T256_Id, 
                            D009_Quantidade_Estoque,                            
                                (select 
                                    sum(T261_soma.T261_Quantidade) as qtde
                                from
                                    T261 as T261_soma 
                                JOIN D001 ON D001_Id = T261_soma.T261_D001_Id_Composicao
                                JOIN D049 ON D001_Id = D049_D001_Id AND D049_Flag_Tipo = 'PD'
                                JOIN D009 ON D049_Id = D009_D049_Id AND D009_C004_Id = 1
                                JOIN T256 ON T256_Id = T261_soma.T261_T256_Id 
                                join T255 on T256_T255_Id = T255_Id
                                where T255_Data_Hora_Baixa_Materiais = '0000-00-00 00:00:00'
                                and T261_soma.T261_D001_Id_Composicao = T261.T261_D001_Id_Composicao ) as saldo
                        FROM 
                            T261 
                                JOIN D001 ON D001_Id = T261_D001_Id_Composicao
                                JOIN D049 ON D001_Id = D049_D001_Id AND D049_Flag_Tipo = 'PD'
                                JOIN D009 ON D049_Id = D009_D049_Id AND D009_C004_Id = '{$g['empresaAtual']}'
                                JOIN T256 ON T256_Id = T261_T256_Id AND T256_T255_Id = '{$T255_Id}'";

            if ($T256_Id > 0) {
                $sqlT261 .= " WHERE T256_Id = {$T256_Id} ";
            }

            $sqlT261 .= " ORDER BY T261_Id ";

            $qryT261 = mysql_query($sqlT261);

            while($rowT261 = mysql_fetch_array($qryT261)) {
                
                if($rowT261['saldo'] > $rowT261['D009_Quantidade_Estoque']){
                    $sqlParam = " SELECT 
                                    {$rowT261['D009_Id']} AS D009_Id,
                                    T261_D001_Id_Composicao AS D001_Id,
                                    T256_Quantidade * T261_Quantidade AS Saldo,
                                    0 AS T005_Id,
                                    0 AS T006_Id
                                FROM T261 
                                JOIN T256 ON T256_Id = T261_T256_Id
                                WHERE T261_Id = {$rowT261['T261_Id']} ";

                    $this->gerarOrdemProducao($sqlParam, $rowT261['T256_Id'], $nivelOp);
                }
            }
        }
    }

    /**
     * processarEntrada
     *
     * @param string $extra
     * @return true/string
     */
    public function processarEntrada($extra)
    {
        global $g;
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $sql = mysql_query("SELECT T256_Id, 
                                   T257_Id, 
                                   T257_Quantidade, 
                                   T257_T066_Id, 
                                   T256_D009_Id, 
                                   T256_T255_Id, 
                                   D001_Id, 
                                   D001_Codigo_Produto, 
                                   D001_Descricao_Produto, 
                                   T005_Id 
                                   FROM T256 
                                   {$extra}"); 
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro ao verificar entradas, por favor entre em contato com o suporte técnico." .$erro;
        }

        if(mysql_num_rows($sql) <= 0){
            return "Nenhuma entrada de produção pendente foi encontrada, por favor verifique! ";
        }

        //Verifica entradas sem locação
        $entradasSemLocacao = '';
        while($row = mysql_fetch_array($sql)){
            if(empty($row['T257_T066_Id'])){
                $entradasSemLocacao = "{$row['D001_Codigo_Produto']} - {$row['D001_Descricao_Produto']}<br>";
            }
        }
        if(!empty($entradasSemLocacao)){
            return "Atenção<br>As entradas abaixo estãos em locação selecionada, por favor verifique.<br><br>$entradasSemLocacao";
        }
        mysql_data_seek($sql, 0);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $pedidosAtualizados = array();
        while($row = mysql_fetch_array($sql)){
            $D004_Id = getD004($row['T257_T066_Id']);
            //Custo total da Composição
            $composicao = "SELECT SUM(D009_Valor_Custo_Unitario * D074_Quantidade) as Custo
                             FROM D074
                        LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao
                        LEFT JOIN D049 ON D001_Id = D049_D001_Id
                        LEFT JOIN D009 ON D049_Id = D009_D049_Id
                        LEFT JOIN D037 ON D037_Id = D001_D037_Id
                            WHERE D074_D001_Id = '{$row['D001_Id']}'
                              AND D009_C004_Id='{$g['empresaAtual']}'";
            $rComposicao = mysql_query($composicao);
            $composicao = mysql_fetch_array($rComposicao);
            $composicao = $composicao['Custo'];

            //Custo total e tempo dos processos
            $processo = "SELECT SUM(D075_Lead_Time) as Tempo, SUM(D075_Lead_Time * (D079_Custo_Minuto + D078_Custo_Minuto)) as Custo
                            FROM D075
                            LEFT JOIN D078 on D078_Id = D075_D078_Id
                            LEFT JOIN D079 on D079_Id = D075_D079_Id
                            WHERE D075_D001_Id = '{$row['D001_Id']}'";
            $rProcesso = mysql_query($processo);
            $processo = mysql_fetch_array($rProcesso);
            $custoProcesso = $processo['Custo'];
            $tempoProducao = $processo['Tempo'];

            //Custo total do material de consumo utilizado nos processos
            $consumo = "SELECT SUM(D076_Quantidade * D009_Valor_Custo_Unitario) as Custo
                            FROM D076
                            LEFT JOIN D009 ON D009_Id = D076_D009_Id
                            LEFT JOIN D001 ON D001_Id = D009_D001_Id
                            LEFT JOIN D037 ON D037_Id = D001_D037_Id
                            LEFT JOIN D075 ON D075_Id = D076_D075_Id
                            WHERE D075_D001_Id = '{$row['D001_Id']}'";
            $rConsumo = mysql_query($consumo);
            $consumo = mysql_fetch_array($rConsumo);
            $consumo = $consumo['Custo'];
            //Custo final
            $custo_final = $composicao + $custoProcesso + $consumo;

            $insert = <<<EOT
                INSERT INTO T001 (
                            T001_D009_Id,
                            T001_C004_Id,
                            T001_T257_Id,
                            T001_T066_Id,
                            T001_D004_Id,
                            T001_Quantidade,
                            T001_Flag_Operacao,
                            T001_Data_Lancamento,
                            T001_Descricao_Operacao,
                            T001_Flag_Atualiza_Custo,
                            T001_Valor_Custo_Unitario,
                            T001_Valor_Preco_Unitario
                        ) VALUES (
                            '{$row['T256_D009_Id']}',
                            '{$g['empresaAtual']}',
                            '{$row['T257_Id']}',
                            '{$row['T257_T066_Id']}',
                            '{$D004_Id}',
                            '{$row['T257_Quantidade']}',
                            'E',
                            current_date(),
                            'PRODUÇÃO-{$row['T256_T255_Id']}',
                            'S',
                            '{$custo_final}',
                            '{$custo_final}'
                        )
EOT;
            mysql_query($insert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao realizar entrada de produção, por favor entre em contato com o suporte técnico." . $erro;
            }
            $CAD002->D001_reprocessa_historico(0, $row['T256_D009_Id']);

            //Update para invocar a trigger after update no T256
            mysql_query("UPDATE T257 SET T257_Id=T257_Id WHERE T257_Id = '{$row['T257_Id']}'");

            //Atualiza o pedido com notificação de estoque
            if(isset($row['T005_Id']) && $row['T005_Id'] > 0 && !in_array($row['T005_Id'], $pedidosAtualizados)) {
                $pedidosAtualizados[] = $row['T005_Id'];
                mysql_query("UPDATE T005 SET T005_Flag_Notificacao_Estoque = 'S' WHERE T005_Id = '{$row['T005_Id']}'");
            }

            $T256=mysql_query("SELECT SUM(IFNULL(T256_Quantidade_Saldo,0)) as Saldo FROM T256  WHERE T256_T255_Id='{$row['T256_T255_Id']}'");
            $mT256=mysql_fetch_array($T256);

            if(empty($mT256['Saldo']) || $mT256['Saldo']==0 || $mT256['Saldo']=''){
                log("UPDATE T255 set T255_Flag_Status='99' WHERE T255_Id='{$row['T256_T255_Id']}'");
                $T255=mysql_query("UPDATE T255 set T255_Flag_Status='99' WHERE T255_Id='{$row['T256_T255_Id']}'");
            }

        }

        mysql_query("COMMIT");
        return true;
    }

    /**
     * validaProcessarEntradaEmEtapas
     *
     * @param Array $extra - Extra da query do grid de PCP
     * @return String|Bool
     */
    public function validaProcessarEntradaEmEtapas($extra) {
        global $g;

        $sql = "SELECT T256_Id, T257_T066_Id, D001_Codigo_Produto, D001_Descricao_Produto FROM T256 {$extra}";
        $sqlValida = mysqli_query($sql);
        if(mysql_num_rows($sqlValida) <= 0){ return "Nenhuma entrada de produção pendente foi encontrada, por favor verifique! "; }

        //Verifica entradas sem locação
        $entradasSemLocacao = '';
        while($row = mysql_fetch_assoc($sqlValida)){
            if(empty($row['T257_T066_Id'])){ $entradasSemLocacao = "{$row['D001_Codigo_Produto']} - {$row['D001_Descricao_Produto']}<br>"; }
        }

        if(!empty($entradasSemLocacao)){ return "Atenção<br>As entradas abaixo estãos em locação selecionada, por favor verifique.<br><br>{$entradasSemLocacao}"; }

        return true;
    }
    
    /**
     * processarEntradaEmEtapas
     *
     * @param Array $dados - Array com dados necessários para processar a entrada em etapas
     * @param Array $cacheAnterior - Array com dados do cache da execução anterior
     * @return String|Bool
     */
    public function processarEntradaEmEtapas($dados, $cacheAnterior = false) {
        global $g;
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        $sql = "SELECT T256_Id, T257_Id, T257_Quantidade, T257_T066_Id, T256_D009_Id, T256_T255_Id, D001_Id, D001_Codigo_Produto, D001_Descricao_Produto, T005_Id FROM T256
                    LEFT JOIN T255 on T255_Id = T256_T255_Id
                    LEFT JOIN T257 ON T256_Id = T257_T256_Id
                    LEFT JOIN D009 on D009_Id = T256_D009_Id
                    LEFT JOIN D049 on D049_Id = D009_D049_Id
                    LEFT JOIN D001 on D001_Id = D049_D001_Id
                    LEFT JOIN T006 on T006_Id = T256_T006_Id
                    LEFT JOIN T005 on T005_Id = T006_T005_Id
                WHERE
                    T256_Id = '{$dados['T256_Id']}'
                GROUP BY
                    T256_Id";
        $row = mysql_fetch_assoc(mysqli_query($sql));

        $pedidosAtualizados = [];

        // loop original
        
        $D004_Id = getD004($row['T257_T066_Id']);
        // Custo total da Composição
        $compSql = "SELECT SUM(D009_Valor_Custo_Unitario * D074_Quantidade) as Custo FROM D074
                        LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao
                        LEFT JOIN D049 ON D001_Id = D049_D001_Id
                        LEFT JOIN D009 ON D049_Id = D009_D049_Id
                        LEFT JOIN D037 ON D037_Id = D001_D037_Id
                    WHERE D074_D001_Id = '{$row['D001_Id']}'
                        AND D009_C004_Id='{$g['empresaAtual']}'";
        $compRow = mysql_fetch_assoc(mysqli_query($compSql));
        $composicao = $compRow['Custo'];

        // Custo total e tempo dos processos
        $procsSql = "SELECT SUM(D075_Lead_Time) as Tempo, SUM(D075_Lead_Time * (D079_Custo_Minuto + D078_Custo_Minuto)) as Custo FROM D075
                        LEFT JOIN D078 on D078_Id = D075_D078_Id
                        LEFT JOIN D079 on D079_Id = D075_D079_Id
                    WHERE D075_D001_Id = '{$row['D001_Id']}'";
        $procsRow = mysql_fetch_assoc(mysqli_query($procsSql));
        $custoProcesso = $procsRow['Custo'];
        $tempoProducao = $procsRow['Tempo'];

        // Custo total do material de consumo utilizado nos processos
        $consSql = "SELECT SUM(D076_Quantidade * D009_Valor_Custo_Unitario) as Custo FROM D076
                        LEFT JOIN D009 ON D009_Id = D076_D009_Id
                        LEFT JOIN D001 ON D001_Id = D009_D001_Id
                        LEFT JOIN D037 ON D037_Id = D001_D037_Id
                        LEFT JOIN D075 ON D075_Id = D076_D075_Id
                    WHERE D075_D001_Id = '{$row['D001_Id']}'";
        $consRow = mysql_fetch_assoc(mysqli_query($consSql));
        $consumo = $consRow['Custo'];
        // Custo final
        $custo_final = $composicao + $custoProcesso + $consumo;

        $ins = <<<SQL
            INSERT INTO T001 (
                T001_D009_Id,
                T001_C004_Id,
                T001_T257_Id,
                T001_T066_Id,
                T001_D004_Id,
                T001_Quantidade,
                T001_Flag_Operacao,
                T001_Data_Lancamento,
                T001_Descricao_Operacao,
                T001_Flag_Atualiza_Custo,
                T001_Valor_Custo_Unitario,
                T001_Valor_Preco_Unitario
            ) VALUES (
                '{$row['T256_D009_Id']}',
                '{$g['empresaAtual']}',
                '{$row['T257_Id']}',
                '{$row['T257_T066_Id']}',
                '{$D004_Id}',
                '{$row['T257_Quantidade']}',
                'E',
                current_date(),
                'PRODUÇÃO-{$row['T256_T255_Id']}',
                'S',
                '{$custo_final}',
                '{$custo_final}'
            )
        SQL;

        mysql_query('START TRANSACTION');
        
        if (!mysql_query($ins)) {   mysql_query('ROLLBACK');    return "Erro ao realizar entrada de produção.<br>" . mysql_error(); }
        
        $CAD002->D001_reprocessa_historico(0, $row['T256_D009_Id']);

        // Update para invocar a trigger after update no T256
        $updt = "UPDATE T257 SET T257_Id = T257_Id WHERE T257_Id = '{$row['T257_Id']}'";
        if (!mysql_query($updt)) {  mysql_query('ROLLBACK');    return "Erro chamar trigger after update no T257.<br>" . mysql_error(); }

        // Atualiza o pedido com notificação de estoque
        if (isset($row['T005_Id']) && $row['T005_Id'] > 0 && !in_array($row['T005_Id'], $pedidosAtualizados)) {
            $pedidosAtualizados[] = $row['T005_Id'];
            
            $updt = "UPDATE T005 SET T005_Flag_Notificacao_Estoque = 'S' WHERE T005_Id = '{$row['T005_Id']}'";
            if (!mysql_query($updt)) {  mysql_query('ROLLBACK');    return "Erro ao atualizar notificação de estoque.<br>" . mysql_error(); }
        }

        $sql = "SELECT SUM(IFNULL(T256_Quantidade_Saldo,0)) as Saldo FROM T256 WHERE T256_T255_Id = '{$row['T256_T255_Id']}'";
        $mT256 = mysql_fetch_assoc(mysql_query($sql));

        if (empty($mT256['Saldo']) || $mT256['Saldo'] == 0 || $mT256['Saldo'] == '') {

            $updt = "UPDATE T255 set T255_Flag_Status = '99' WHERE T255_Id = '{$row['T256_T255_Id']}'";
            log($updt);
            if (!mysql_query($updt)) {  mysql_query('ROLLBACK');    return "Erro ao atualizar status.<br>" . mysql_error(); }
        }

        mysql_query('COMMIT');

        // fim loop original
        
        return true;
    }

    /**
     * processarProdutosBaixa
     *
     * @param string $T255_Id
     * @return true/string
     */
    public function processarProdutosBaixa($T255_Id, $transacao = true) {
        global $g;
        $paramlocacaoPadraoProducao = $g['C031']['locacaoPadraoProducao'];

        if ($transacao) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
        }

        mysql_query("DELETE FROM T264 WHERE T264_T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if(!empty($erro)) {
            if ($transacao) {
                mysql_query("ROLLBACK");
            }
            return "Erro ao processar produtos, por favor entre em contato com o suporte técnico.";
        }

        $sql = mysql_query("SELECT D009_Id, Quantidade FROM
                                (SELECT D009_Id, 
                                        SUM(T261_Quantidade*T256_Quantidade) AS Quantidade 
                                   FROM T261 
                              LEFT JOIN T256 ON T256_Id = T261_T256_Id
                              LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao 
                              LEFT JOIN D049 ON D001_Id = D049_D001_Id
                              LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                  WHERE T256_T255_Id = '{$T255_Id}'
                                    AND D009_C004_Id='{$g['empresaAtual']}'
                               GROUP BY D009_Id
                            UNION ALL
                                SELECT T263_D009_Id AS D009_Id, SUM(T263_Quantidade*T256_Quantidade) AS Quantidade FROM T263 LEFT JOIN T262 ON T262_Id=T263_T262_Id LEFT JOIN T256 ON T256_Id=T262_T256_Id WHERE T256_T255_Id = '{$T255_Id}' GROUP BY D009_Id) Produtos
                            GROUP BY D009_Id");
        while ($row = mysql_fetch_assoc($sql)) {
            $T264_T066_Id = null;

            if (!empty($paramlocacaoPadraoProducao)) {
                $sqlLocacao = "SELECT T066_Id
                                 FROM T066
                                WHERE T066_D009_Id = '{$row['D009_Id']}'
                                  AND T066_D004_Id = '{$paramlocacaoPadraoProducao}'";
                $Locacao = mysql_fetch_assoc(mysql_query($sqlLocacao));

                $T264_T066_Id = $Locacao['T066_Id'];
            }

            $sqlInsert = "INSERT INTO T264 (
                T264_T255_Id,
                T264_D009_Id,
                T264_T066_Id,
                T264_Quantidade)
              VALUES (
                '{$T255_Id}',
                '{$row['D009_Id']}',
                '{$T264_T066_Id}',
                '{$row['Quantidade']}')";

            mysql_query($sqlInsert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao processar produtos, por favor entre em contato com o suporte técnico.";
            }
        }

        if ($transacao) {
            mysql_query("COMMIT");
        }

        return true;
    }

    // A classe personalizada EST017 precisa acessar este método ao validar o saldo da locação.
    protected function tabelaSemSaldoLocacao($produto, $qtdeOp, $qtdeLocacao) {
        return "<tr>
                    <td>
                        {$produto}
                    </td>
                    <td style='text-align:right; width: 120px;'>
                        {$qtdeOp}
                    </td>
                    <td style='text-align:right; width: 120px;'>
                        {$qtdeLocacao}
                    </td>
                </tr>";
    }

     /**
     * baixarEstoqueMateriais
     *
     * @param string $T255_Id
     * @param string $sql
     * @return true/string
     */
    public function baixarEstoqueMateriais($T255_Id, $sql) {
        global $g;
        $paramConsumoMaterialOpSemEstoque = $g['C031']['consumoMaterialOpSemEstoque'] == 'N';

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $sql = base64_decode($sql);
        $res = mysql_query($sql);

        $arrayProdutos = array();
        //Verifica produtos sem locação
        $produtosSemLocacao = '';
        $produtosSemSaldo   = '';

        while($row = mysql_fetch_array($res)){
            $arrayProdutos[$row['T264_D009_Id']] = $row['T264_Quantidade'];
            $produto = "{$row['D001_Codigo_Produto']} - {$row['D001_Descricao_Produto']}<br>";

            if(empty($row['T264_T066_Id'])){
                $produtosSemLocacao .= $produto;
            }

            if (($paramConsumoMaterialOpSemEstoque) && ($row['T264_Quantidade'] > $row['T066_Quantidade_Estoque'])) {
                $produtosSemSaldo .= $this->tabelaSemSaldoLocacao($produto, $row['T264_Quantidade'], $row['T066_Quantidade_Estoque']);
            }
        }

        if (!empty($produtosSemLocacao)) {
            return "<h3>Atenção</h3><h5>Os produtos abaixo estãos sem locação selecionada, por favor verifique.</h5>{$produtosSemLocacao}";
        }

        if (($paramConsumoMaterialOpSemEstoque) && (!empty($produtosSemSaldo))) {
            $cabecalho = $this->tabelaSemSaldoLocacao('<b>Produto</b>', '<b>Quantidade OP</b>', '<b>Estoque Locação</b>');
            return  "<h3>Atenção</h3><h5>Os produtos abaixo estãos sem saldo na locação selecionada, por favor verifique.</h5><table>{$cabecalho}{$produtosSemSaldo}</table>";
        }

        //Verifica se os produtos processados estão iguais aos materiais da engenharia dos produtos
        $sqlCheck = mysql_query("SELECT D009_Id, Quantidade FROM
                                (SELECT D009_Id, 
                                        SUM(T261_Quantidade*T256_Quantidade) AS Quantidade 
                                   FROM T261 
                              LEFT JOIN T256 ON T256_Id = T261_T256_Id
                              LEFT JOIN D001 ON D001_Id = T261_D001_Id_Composicao
                              LEFT JOIN D049 ON D001_Id = D049_D001_Id
                              LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                  WHERE T256_T255_Id = '{$T255_Id}' 
                                    AND D009_C004_Id='{$g['empresaAtual']}'
                               GROUP BY D009_Id
                            UNION ALL
                                SELECT T263_D009_Id AS D009_Id, 
                                       SUM(T263_Quantidade*T256_Quantidade) AS Quantidade 
                                  FROM T263 
                             LEFT JOIN T262 ON T262_Id=T263_T262_Id 
                             LEFT JOIN T256 ON T256_Id=T262_T256_Id 
                                 WHERE T256_T255_Id = '{$T255_Id}' 
                              GROUP BY D009_Id) Produtos
                              GROUP BY D009_Id");
        $erroProdutosProcessados = false;
        while($rowCheck = mysql_fetch_assoc($sqlCheck)){
            if(empty($arrayProdutos[$rowCheck['D009_Id']]) || $arrayProdutos[$rowCheck['D009_Id']] != $rowCheck['Quantidade']){
                $erroProdutosProcessados = true;
            }
        }
        if($erroProdutosProcessados){
            return "Atenção<br>Os produtos processados não estão de acordo com a engenharia dos produtos desta ordem de produção, por favor processe novamente os produtos antes de realizar a baixa de estoque.";
        }

        mysql_data_seek($res, 0);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        while($row = mysql_fetch_assoc($res)){
            $D004_Id = getD004($row['T264_T066_Id']);
            $insert = "INSERT INTO T001 (
                                   T001_D009_Id,
                                   T001_C004_Id,
                                   T001_T066_Id,
                                   T001_D004_Id,
                                   T001_T264_Id,
                                   T001_Flag_Operacao,
                                   T001_Descricao_Operacao,
                                   T001_Quantidade,
                                   T001_Data_Lancamento,
                                   T001_Flag_Atualiza_Custo
                                ) VALUES (
                                    '{$row['T264_D009_Id']}',
                                    '{$g['empresaAtual']}',
                                    '{$row['T264_T066_Id']}',
                                    '{$D004_Id}',
                                    '{$row['T264_Id']}',
                                    'S',
                                    'BAIXA MATÉRIA PRIMA',
                                    '{$row['T264_Quantidade']}',
                                    current_date(),
                                    'N'
                                )";
            mysql_query($insert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao baixar estoque, por favor entre em contato com o suporte técnico.";
            }
            $CAD002->D001_reprocessa_historico(0, $row['T264_D009_Id']);
        }
        mysql_query("UPDATE T255 SET T255_Data_Hora_Baixa_Materiais = current_timestamp() WHERE T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if(!empty($erro)){
            mysql_query("ROLLBACK");
            return "Erro ao baixar estoque, por favor entre em contato com o suporte técnico.";
        }
        mysql_query("COMMIT");
        return true;
    }

    /**
     * processarEngenhariaProduto
     *
     * @param string $T256_Id
     * @return true/string
     */
    public function processarEngenhariaProduto($T256_Id)
    {
        global $g;
        $sql = mysql_query("SELECT D001_Id FROM T256 LEFT JOIN D009 ON D009_Id=T256_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T256_Id = '{$T256_Id}'");
        $row = mysql_fetch_array($sql);
        //Copia a tabela de matéria prima do produto
        mysql_query("INSERT INTO T261 (
                                 T261_D001_Id_Composicao, 
                                 T261_Quantidade, 
                                 T261_T256_Id) 
                          SELECT D074_D001_Id_Composicao, 
                                 D074_Quantidade, 
                                 '{$T256_Id}' 
                            FROM D074 
                       LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                       LEFT JOIN D049 ON D001_Id = D049_D001_Id
                       LEFT JOIN D009 ON D049_Id = D009_D049_Id
                           WHERE D074_D001_Id = '{$row['D001_Id']}'
                             AND D009_C004_Id='{$g['empresaAtual']}'");
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (1)";
        } 
        $T261_Id = $g['mysqlLastId'];            
        mysql_query("INSERT INTO T261A (T261A_T261_Id, T261A_T006_Id, T261A_T256_Id) 
                        SELECT T261_Id, 
                               T261_T006_Id,
                               T261_T256_Id
                          FROM T261 
                         WHERE T261_T256_Id     = '{$T256_Id}'");
        

        //Copia a tabela de processos de produção do produto
        $sqlD075 = mysql_query("SELECT D075_Id FROM D075 WHERE D075_D001_Id = '{$row['D001_Id']}'");
        while($rowD075 = mysql_fetch_assoc($sqlD075)){
            mysql_query("INSERT INTO T262 (T262_D079_Id, T262_D078_Id, T262_Lead_Time, T262_Descricao, T262_Ordem, T262_Numero_Funcionarios, T262_T256_Id) SELECT D075_D079_Id, D075_D078_Id, D075_Lead_Time, D075_Descricao, D075_Ordem, D075_Numero_Funcionarios, '{$T256_Id}' FROM D075 WHERE D075_Id = '{$rowD075['D075_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (2)";
            }
            $T262_Id = $g['mysqlLastId'];

            $parametrosGatilho = array($T262_Id, $rowD075['D075_Id']);
            $erro = $this->gatilhoT262AposInsert($parametrosGatilho);

            if(!empty($erro)){
                return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (gatilhoT262AposInsert). Erro: {$erro}";
            }

            //Copia a tabela de material de consumo dos processos de produção do produto
            mysql_query("INSERT INTO T263 (T263_T262_Id, T263_D009_Id, T263_Quantidade) SELECT '{$T262_Id}', D076_D009_Id, D076_Quantidade FROM D076 WHERE D076_D075_Id = '{$rowD075['D075_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (3)";
            }
        }
        return true;
    }

    
    /**
     * gatilhoT262AposInsert
     *
     * @param array $parametrosGatilho
     * @return string
     */
    public function gatilhoT262AposInsert($parametrosGatilho)
    {
        return "";
    }

    /**
     * gatilhoValidacaoInsertT257
     *
     * @param array $parametrosGatilho
     * @return string
     */  
    public function gatilhoValidacaoInsertT257($parametrosGatilho) {
        return "";
    }

    /**
     * atualizarComponentes
     *
     * @param string $sql
     * @return true/string
     */
    public function atualizarComponentes($T005_Id = false, $T255_Id = false)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $updateT256_Id = false;
        $updateT006_Id = false;
        if($T005_Id > 0 && !$T255_Id){
            $FK_T261 = 'T261_T006_Id';
            $FK_T262 = 'T262_T006_Id';
            $FK_T261A = 'T261A_T006_Id';
            $updateT256_Id = true;
            $res = mysql_query("SELECT D001_Id, T006_Id as FK, IFNULL(T256_Id,0) as T256_Id FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN T256 ON T256_T006_Id=T006_Id WHERE T006_T005_Id = '{$T005_Id}'");
        } else if($T255_Id > 0 && !$T005_Id){
            $FK_T261 = 'T261_T256_Id';
            $FK_T262 = 'T262_T256_Id';
            $FK_T261A = 'T261A_T256_Id';
            $updateT006_Id = true;
            $res = mysql_query("SELECT D001_Id, T256_Id as FK, IFNULL(T256_T006_Id,0) as T256_T006_Id FROM T256 LEFT JOIN D009 ON D009_Id=T256_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE T256_T255_Id = '{$T255_Id}'");
        } else {
            mysql_query("ROLLBACK");
            return "Erro ao atualizar componentes, por favor entre em contato com o suporte técnico";
        }

        while($row = mysql_fetch_array($res)){
            $FK_valor = $row['FK'];

            //Remove a composição do produto
            mysql_query("DELETE FROM T261 WHERE $FK_T261 = '{$FK_valor}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao gravar os componentes, por favor entre em contato com o suporte técnico. (1)";
            } 
            mysql_query("DELETE FROM T261A WHERE $FK_T261A = '{$FK_valor}'");
            
            //Atualiza a tabela de composição do produto
            mysql_query("INSERT INTO T261 (
                                     T261_D001_Id_Composicao, 
                                     T261_Quantidade, 
                                     {$FK_T261}) 
                              SELECT D074_D001_Id_Composicao, 
                                     D074_Quantidade, 
                                     '{$FK_valor}' 
                                FROM D074 
                           LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                           LEFT JOIN D049 ON D001_Id = D049_D001_Id
                           LEFT JOIN D009 ON D049_Id = D009_D049_Id
                               WHERE D074_D001_Id = '{$row['D001_Id']}'
                                 AND D009_C004_Id='{$g['empresaAtual']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao gravar os componentes, por favor entre em contato com o suporte técnico. (2)";
            }

            mysql_query("INSERT INTO T261A (T261A_T261_Id, {$FK_T261A}) 
                              SELECT T261_Id, 
                                     '{$FK_valor}'
                                FROM T261 
                               WHERE {$FK_T261})  = '{$FK_valor}'");
            
            //Caso a engenharia seja atualizada pela ordem de produção e existe um pedido, fazemos update nos componentes para vincular ao item do pedido
            if($updateT006_Id && $row['T256_T006_Id'] > 0){
                mysql_query("UPDATE T261 SET T261_T006_Id = '{$row['T256_T006_Id']}' WHERE T261_T256_Id = '{$FK_valor}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os componentes, por favor entre em contato com o suporte técnico. (3)";
                }
            }

            //Caso a engenharia seja atualizada pelo pedido e exista uma ordem de produção, fazemos update nos componentes para vincular ao item da ordem de produção
            if($updateT256_Id && $row['T256_Id'] > 0){
                mysql_query("UPDATE T261 SET T261_T256_Id = '{$row['T256_Id']}' WHERE T261_T006_Id = '{$FK_valor}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os componentes, por favor entre em contato com o suporte técnico. (4)";
                }
            }

            //Remove os materiais de consumo dos processos de produção existentes
            mysql_query("DELETE FROM T263 WHERE T263_T262_Id IN (SELECT GROUP_CONCAT(T262_Id SEPARATOR ',') FROM T262 WHERE T262_T256_Id = '{$FK_valor}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao gravar os materiais de consumo, por favor entre em contato com o suporte técnico. (1)";
            }
            //Remove os processos de produção existentes
            mysql_query("DELETE FROM T262 WHERE $FK_T262 = '{$FK_valor}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro ao gravar os processos, por favor entre em contato com o suporte técnico. (1)";
            }
            $sqlD075 = mysql_query("SELECT D075_Id FROM D075 WHERE D075_D001_Id = '{$row['D001_Id']}'");
            while($rowD075 = mysql_fetch_assoc($sqlD075)){

                //Atualiza a tabela de processos de produção do produto
                mysql_query("INSERT INTO T262 (T262_D079_Id, T262_D078_Id, T262_Lead_Time, T262_Descricao, T262_Ordem, T262_Numero_Funcionarios, {$FK_T262}) SELECT D075_D079_Id, D075_D078_Id, D075_Lead_Time, D075_Descricao, D075_Ordem, D075_Numero_Funcionarios, '{$FK_valor}' FROM D075 WHERE D075_Id = '{$rowD075['D075_Id']}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os processos, por favor entre em contato com o suporte técnico. (2)";
                }
                $T262_Id = $g['mysqlLastId'];

                $parametrosGatilho = array($T262_Id, $rowD075['D075_Id']);
                $erro = $this->gatilhoT262AposInsert($parametrosGatilho);

                if(!empty($erro)){
                    return "Erro ao gravar engenharia do produto, por favor entre em contato com o suporte técnico. (gatilhoT262AposInsert). Erro: {$erro}";
                }

                //Atualiza a tabela de material de consumo dos processos de produção do produto
                mysql_query("INSERT INTO T263 (T263_T262_Id, T263_D009_Id, T263_Quantidade) SELECT '{$T262_Id}', D076_D009_Id, D076_Quantidade FROM D076 WHERE D076_D075_Id = '{$rowD075['D075_Id']}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os materiais de consumo, por favor entre em contato com o suporte técnico. (2)";
                }
            }

            //Caso a engenharia seja atualizada pela ordem de produção e existe um pedido, fazemos update nos processos para vincular ao item do pedido
            if($updateT006_Id && $row['T256_T006_Id'] > 0){
                mysql_query("UPDATE T262 SET T262_T006_Id = '{$row['T256_T006_Id']}' WHERE T262_T256_Id = '{$FK_valor}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os processos, por favor entre em contato com o suporte técnico. (3)";
                }
            }

            //Caso a engenharia seja atualizada pelo pedido e exista uma ordem de produção, fazemos update nos processos para vincular ao item da ordem de produção
            if($updateT256_Id && $row['T256_Id'] > 0){
                mysql_query("UPDATE T262 SET T262_T256_Id = '{$row['T256_Id']}' WHERE T262_T006_Id = '{$FK_valor}'");
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro ao gravar os processos, por favor entre em contato com o suporte técnico. (4)";
                }
            }
        }

        mysql_query("COMMIT");
        return true;
    }

    public function dadosCertificadosOP() {
        global $g;

        $certificado = array();

        $sqlOP  = " SELECT T255.T255_Id, ";
        $sqlOP .=        " T255.T255_Quantidade, ";
        $sqlOP .=        " D024.D024_Id, ";
        $sqlOP .=        " D001.D001_Id, ";
        $sqlOP .=        " T005.T005_T003_Id, ";
        $sqlOP .=        " T006.T006_Numero_Pedido_Compra, ";
        $sqlOP .=        " D001.D001_Codigo_Produto, ";
        $sqlOP .=        " D001a.D001A_FLAG_PRODUTO_EMITE_CERTIFICADO, ";
        $sqlOP .=        " IFNULL(T256_Pai.T256_T255_Id, 'PAI') AS OP_PAI ";
        $sqlOP .=   " FROM T255 ";
        $sqlOP .=        " LEFT JOIN T256             ON T256.T256_T255_Id          = T255.T255_Id ";
        $sqlOP .=        " LEFT JOIN D009             ON T256.T256_D009_Id          = D009.D009_Id ";
        $sqlOP .=        " LEFT JOIN D001             ON D009.D009_D001_Id          = D001.D001_Id ";
        $sqlOP .=        " LEFT JOIN D001a            ON D001a.D001a_D001_Id        = D001.D001_Id ";
        $sqlOP .=        " LEFT JOIN T006             ON T256.T256_T006_Id          = T006.T006_Id ";
        $sqlOP .=        " LEFT JOIN T005             ON T006.T006_T005_Id          = T005.T005_Id ";
        $sqlOP .=        " LEFT JOIN D024             ON T005.T005_D024_Id          = D024.D024_Id ";
        $sqlOP .=        " LEFT JOIN T256A            ON T256A.T256A_T256_Id        = T256.T256_Id ";
        $sqlOP .=        " LEFT JOIN T256 AS T256_Pai ON T256A.T256A_T256_Id_Op_Pai = T256_Pai.T256_Id ";
        $sqlOP .=        " LEFT JOIN T257             ON T257.T257_T256_Id          = T256.T256_Id ";
        $sqlOP .=        " LEFT JOIN T001             ON T001.T001_T257_Id          = T257.T257_Id ";
        $sqlOP .=  " WHERE T255.T255_C004_Id                                       = '{$g['empresaAtual']}' ";
        $sqlOP .=    " AND T255.T255_Flag_Status                                   NOT IN ('99', '98')  ";
        $sqlOP .=    " AND T255.T255_Id                                            = T256.T256_T255_Id  ";
        $sqlOP .=    " AND T257.T257_Id                                            > 0 ";
        $sqlOP .=    " AND T001.T001_Id                                            IS NULL ";
        $sqlOP .=    " AND T256_Pai.T256_T255_Id                                   IS NULL ";
        $sqlOP .=    " AND IFNULL(D001a.D001A_FLAG_PRODUTO_EMITE_CERTIFICADO, 'N') = 'S' ";
        $sqlOP .=  " GROUP BY T255_Id ";

        $dadosOP = mysql_query($sqlOP);

        while ($op = mysql_fetch_assoc($dadosOP)) {
            $emails = array();

            $sqlContato  = " SELECT D013_E_Mail ";
            $sqlContato .=   " FROM D013 ";
            $sqlContato .=   " LEFT JOIN d013a on D013A_D013_Id = D013_Id ";
            $sqlContato .=  " WHERE D013.D013_D024_Id            = {$op['D024_Id']} ";
            $sqlContato .=    " AND D013A_Flag_Envio_Certificado = 'S' ";

            $dadosContato = mysql_query($sqlContato);

            while ($contato = mysql_fetch_assoc($dadosContato)) {
                array_push($emails, strtolower($contato['D013_E_Mail']));
            }

            if (count($emails) > 0) {
                $caminho = $this->gerarCertificado($op['T255_Id']);
                $assunto = $this->gerarEmailAssunto($op);
                $corpo   = $this->gerarEmailCorpo($op);
    
                $itemOP = array('op'          => $op['T255_Id'],
                                'assunto'     => $assunto,
                                'corpo'       => $corpo,
                                'emails'      => $emails,
                                'certificado' => $caminho);
    
                array_push($certificado, $itemOP);
            }
        }

        return $certificado;
    }

    public function gerarCertificado($T255_Id) {
        require_once('bibliotecas/classes/SMTPRINT.php');
        $SMTPRINT = new SMTPRINT_();

        $relatorio = "/sistema/personalizacao/tela/b7bfcf82ce0fdd90c8d4d5af660382a7/";
        $parametro = "acaoId={$T255_Id}";

        return $SMTPRINT->gerarPDF($relatorio, $parametro);
    }

    private function gerarEmailAssunto($dadosOP) {
        $serie = "";
        $qtdeOP = round($dadosOP['T255_Quantidade']);
        if ($qtdeOP == 1) {
            $serie = "{$dadosOP['T255_Id']}/01";
        } else {
            $qtde = ($qtdeOP < 10) ? "0{$qtdeOP}" : $qtdeOP;
            $serie = "de {$dadosOP['T255_Id']}/01 até {$dadosOP['T255_Id']}/{$qtde}";
        }

        return "Cetificado CE do número de série {$serie} do item {$dadosOP['D001_Codigo_Produto']} do pedido {$dadosOP['T006_Numero_Pedido_Compra']}.";
    }

    private function gerarEmailCorpo($dadosOP) {
        $email  = " <!DOCTYPE html> ";
        $email .= " <html> ";
        $email .=   " <head> ";
        $email .=     " <title>Email Certificado</title> ";
        $email .=     " <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'> ";
        $email .=     " <style type='text/css'> ";
        $email .=       " @page{ ";
        $email .=         " margin-top: 3cm; ";
        $email .=         " margin-left: 3cm; ";
        $email .=         " margin-bottom: 2cm; ";
        $email .=         " margin-left: 2cm; ";
        $email .=       " } ";
        $email .=       " #titulo{ ";
        $email .=         " font-size: 12px; ";
        $email .=         " text-align: justify; ";
        $email .=       " } ";
        $email .=     " </style> ";
        $email .=   " </head> ";
        $email .=   " <body> ";
        $email .=     " <div> ";
        $email .=       " <p id='titulo'> Prezado cliente, </p> ";
        $email .=       " <p id='titulo'> Segue anexo certificado CE referente ao vosso pedido de compra número {$dadosOP['T006_Numero_Pedido_Compra']}.  </p> ";
        $email .=     " </div> ";
        $email .=     " <footer id='layout-footer'> ";
        $email .=       " <div id='location'> ";
        $email .=         " <div class='container'> ";
        $email .=           " <div class='row'> ";
        $email .=             " <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center'> ";
        $email .=               " <p id='titulo'> Att, </p> ";
        $email .=               " <p id='titulo'><b> PÓLUX INDÚSTRIA ELETROMECÂNICA LTDA </b></p> ";
        $email .=               " <p id='titulo'><b> Rua João Januário Ayroso, 300 / Bairro Jaraguá Esquerdo - CEP: 89253 - 100 </b></p> ";
        $email .=               " <p id='titulo'><b> Jaraguá do Sul - SC - Brasil </b></p> ";
        $email .=               " <p id='titulo'><b> CNPJ: 79.383.899/0001-29 </b></p> ";
        $email .=               " <div class='col-lg-2 col-lg-offset-1 col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-12 text-center'> ";
        $email .=                 " <a href='http://www.polux.ind.br'> ";
        $email .=                 " <img src='http://www.polux.ind.br/themes/default/assets/images/logo.png' alt='Pólux Transformadores'> ";
        $email .=               " </div> ";
        $email .=             " </div> ";
        $email .=           " </div> ";
        $email .=         " </div> ";
        $email .=       " </div> ";
        $email .=     " </footer> ";
        $email .=   " </body> ";
        $email .= " </html> ";

        return $email;
    }

    public function cancelarOrdemProducao($T255_Id)
    {
        $T255_Id = (int) $T255_Id;
        if ($T255_Id <= 0) {
            return "Ordem de produção inválida.";
        }

        $sql = mysql_query("SELECT T255_Flag_Status FROM T255 WHERE T255_Id = '{$T255_Id}' LIMIT 1");
        if (mysql_num_rows($sql) <= 0) {
            return "Ordem de produção não encontrada.";
        }

        $row = mysql_fetch_assoc($sql);
        if ($row['T255_Flag_Status'] == '99') {
            return "Esta ordem de produção já está finalizada.";
        }

        if ($row['T255_Flag_Status'] == '98') {
            return true;
        }

        mysql_query("UPDATE T255 SET T255_Flag_Status = '98' WHERE T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro ao cancelar ordem de produção, por favor entre em contato com o suporte técnico.";
        }

        return true;
    }

    public function excluirOrdemProducao($T255_Id)
    {
        $T255_Id = (int) $T255_Id;
        if ($T255_Id <= 0) {
            return "Ordem de produção inválida.";
        }

        $sqlT255 = mysql_query("SELECT T255_Flag_Status, T255_Data_Hora_Baixa_Materiais FROM T255 WHERE T255_Id = '{$T255_Id}' LIMIT 1");
        if (mysql_num_rows($sqlT255) <= 0) {
            return "Ordem de produção não encontrada.";
        }

        $rowT255 = mysql_fetch_assoc($sqlT255);
        if ($rowT255['T255_Flag_Status'] == '99') {
            return "Esta ordem de produção já está finalizada.";
        }

        if ($rowT255['T255_Data_Hora_Baixa_Materiais'] != '0000-00-00 00:00:00') {
            return "Este ordem de produção já baixou matéria prima e material de consumo";
        }

        $sqlT257 = mysql_query("SELECT T257_Id
                                  FROM T257
                             LEFT JOIN T256 ON T256_Id = T257_T256_Id
                                 WHERE T256_T255_Id = '{$T255_Id}'
                                 LIMIT 1");
        $rowT257 = mysql_fetch_assoc($sqlT257);
        if (!empty($rowT257['T257_Id'])) {
            return "Esta ordem de produção já possui quantidade produzida.";
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        mysql_query("DELETE FROM D085 WHERE D085_Entidade = 'T255' AND D085_Id_Entidade = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (1)";
        }

        mysql_query("DELETE FROM T264 WHERE T264_T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (2)";
        }

        mysql_query("DELETE T263
                       FROM T263
                 INNER JOIN T262 ON T262_Id = T263_T262_Id
                 INNER JOIN T256 ON T256_Id = T262_T256_Id
                      WHERE T256_T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (3)";
        }

        mysql_query("DELETE FROM T262
                      WHERE T262_T256_Id IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (4)";
        }

        mysql_query("DELETE FROM T261A
                      WHERE T261A_T256_Id IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )
                         OR T261A_T261_Id IN (
                            SELECT T261_Id
                              FROM T261
                             WHERE T261_T256_Id IN (
                                    SELECT T256_Id
                                      FROM T256
                                     WHERE T256_T255_Id = '{$T255_Id}'
                             )
                      )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (5)";
        }

        mysql_query("DELETE FROM T261
                      WHERE T261_T256_Id IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (6)";
        }

        mysql_query("DELETE FROM T256A
                      WHERE T256A_T256_Id IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )
                         OR T256A_T256_Id_Op_Pai IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (7)";
        }

        mysql_query("DELETE FROM T257
                      WHERE T257_T256_Id IN (
                            SELECT T256_Id
                              FROM T256
                             WHERE T256_T255_Id = '{$T255_Id}'
                      )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (8)";
        }

        mysql_query("DELETE FROM T256 WHERE T256_T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (9)";
        }

        mysql_query("DELETE FROM T255A WHERE T255A_T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (10)";
        }

        $sqlTabelaP001 = mysql_query("SHOW TABLES LIKE 'P001'");
        if (mysql_num_rows($sqlTabelaP001) > 0) {
            mysql_query("DELETE FROM P001 WHERE P001_T255_Id = '{$T255_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (11)";
            }
        }

        mysql_query("DELETE FROM T255 WHERE T255_Id = '{$T255_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir ordem de produção, por favor entre em contato com o suporte técnico. (12)";
        }

        mysql_query("COMMIT");
        return true;
    }

}
