<?php
namespace hardness;
definirClasse('CAD002');
class CAD002_
{
    protected $D009IsetPendente = array();

    public function preencherLocacoes($D009_Id, $T005_Id, $T006_Id, $qtdPed, $locEscolhida) {
        return true;
    }

    public function obterNovoCodigoProdutoSmart($D001_Id, $segundaSeq, $preCadastro) {
        return "";
    }

    public function inventario($dataInventario) {
        return true;
    }

    public function inventarioBloco_0($dataInventario){
        return true;
    }

    public function inventarioBloco_H($dataInventario){
        return true;
    }

    public function atualizaPrecoTabelaOrcamentoSmart($mT004, $forcarUpdate = false, $TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0){
        return true;
    }
    /**
     * juntarMarcas
     * @param int   $D049_Antigo_Id
     * @param int   $D049_Novo_Id
     * @return true/erro(string)
     */
    public function juntarMarcas($D049_Antigo_Id, $D049_Novo_Id)
    {
        global $g;
        
        if ($D049_Antigo_Id <= 0 || $D049_Novo_Id <= 0) {
            return "Por favor, selecione as marcas!";
        }
        
        if ($D049_Antigo_Id <= 0) {
            return "Por favor, selecione a marca antiga!";
        }
        
        if ($D049_Novo_Id <= 0) {
            return "Por favor, selecione a nova marca!";
        }
        
        if ($D049_Antigo_Id == $D049_Novo_Id) {
            return "As marcas são iguais, verifique!";
        }
        
        //Faz uma consulta retornando todos os produtos fornecedor (D049) referente ao produto (D001 antigo)
        $cClausula = "SELECT D082_Marca,
                            D049_Id
                            FROM D049
                            LEFT JOIN D082 on D082_Id=D049_D082_Id
                            WHERE D049_Id='{$D049_Antigo_Id}'";
        
        $cSql_D049_Antigo = mysql_query($cClausula);
        $cErro            = mysql_error();
        
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
            exit;
        }
        if (mysql_num_rows($cSql_D049_Antigo) == 0) {
            return "A marca antiga nao existe $D049_Antigo_Id";
        }
        
        $mD049_Antigo = mysql_fetch_array($cSql_D049_Antigo);
        
        mysql_query("SET autocommit=0");
        mysql_query("start transaction");
        
        // Verifica se o produto fornecedor (D049) existe no produto (D001 novo)
        $cClausula      = " SELECT D049_Id,D049_D001_Id FROM D049 WHERE D049_Id='{$D049_Novo_Id}' ";
        $cSql_D049_Novo = mysql_query($cClausula);
        $cErro          = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
        }
        if (mysql_num_rows($cSql_D049_Novo) == 0) {
            return "A marca antiga não existe";
        }
        
        $mD049_Novo = mysql_fetch_array($cSql_D049_Novo);
        
        if (mysql_num_rows($cSql_D049_Novo) == 0) {
            //Se o produto fornecedor (D049) não existir para o produto (D001 novo),
            //Faz o UPDATE
            $cClausula = "UPDATE D049 SET D049_D001_Id='{$mD049_Novo['D049_D001_Id']}' WHERE D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
            }
        } else {
            //Corrige os relacionamentos do produto forneceodr (D049)
            //trocarId("D049","{$mD049_Antigo['D049_Id']}","{$mD049_Novo['D049_Id']}",array("D009","D041"));
            
            $cClausula = "UPDATE D042 SET D042_D049_Id='{$mD049_Novo['D049_Id']}' WHERE D042_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE D042 SET D042_D049_Id_Itens='{$mD049_Novo['D049_Id']}' WHERE D042_D049_Id_Itens='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE D083 SET D083_D049_Id='{$mD049_Novo['D049_Id']}' WHERE D083_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE T006 SET T006_D049_Id='{$mD049_Novo['D049_Id']}' WHERE T006_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE T046 SET T046_D049_Id='{$mD049_Novo['D049_Id']}' WHERE T046_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE T048 SET T048_D049_Id='{$mD049_Novo['D049_Id']}' WHERE T048_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE T098 SET T098_D049_Id='{$mD049_Novo['D049_Id']}' WHERE T098_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cClausula = "UPDATE T129 SET T129_D049_Id='{$mD049_Novo['D049_Id']}' WHERE T129_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            //-- SE O PRODUTO FORNECEDOR EXISTE, ENTAO VERIFICO A EXISTENCIA DE TODOS OS D009 ANTIGOS NO D009 NOVO
            //-- BUSCO TODOS OS D009 REFERENTE AO D049 ANTIGO:
            
            $cClausula        = "SELECT D009_C004_Id,
                                     D009_Quantidade_Estoque,
                                     D001_Codigo_Produto,
                                     D009_Id
                                FROM D009
                           LEFT JOIN D049 on D049_Id=D009_D049_Id
                           LEFT JOIN D001 on D001_Id=D049_D001_Id
                               WHERE D009_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $cSql_D009_Antigo = mysql_query($cClausula);
            $cErro            = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                exit;
            }
            
            //-- ELIMINA TABELA DE PRECO (D041 ANTIGO)
            $cClausula = "DELETE FROM D041 WHERE D041_D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
            }
            
            //-- ELIMINA O PRODUTO FORNECEDOR (D049 ANTIGO)
            $cClausula = "DELETE FROM D049 WHERE D049_Id='{$mD049_Antigo['D049_Id']}'";
            $mLog[]    = $cClausula;
            mysql_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
            }
            
            while ($mD009_Antigo = mysql_fetch_array($cSql_D009_Antigo)) {
                
                //-- VERIFICO SE O (D009) EXISTE NO PRODUTO FORNECEDOR (D049 NOVO)
                $cClausula = "SELECT *
                                        FROM D009
                                        LEFT JOIN T066 on T066_D009_Id=D009_Id                        
                                       WHERE D009_D049_Id='{$mD049_Novo['D049_Id']}'
                                         AND D009_C004_Id='{$mD009_Antigo['D009_C004_Id']}'";
                
                $cSql_D009_Novo = mysql_query($cClausula);
                
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
                }
                
                $mD009_Novo = mysql_fetch_array($cSql_D009_Novo);
                
                //-- CORRIGI OS RELACIONAMENTOS
                //trocarId("D009","{$mD009_Antigo['D009_Id']}","{$mD009_Novo['D009_Id']}",array("T066"));
                
                $cClausula = "UPDATE D069 SET D069_D009_Id='{$mD009_Novo['D009_Id']}' WHERE D069_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE D086 SET D086_D009_Id='{$mD009_Novo['D009_Id']}' WHERE D086_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE L001 SET L001_D009_Id='{$mD009_Novo['D009_Id']}' WHERE L001_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T001 SET T001_D009_Id='{$mD009_Novo['D009_Id']}', T001_T066_Id='{$mD009_Novo['T066_Id']}' WHERE T001_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T004 SET T004_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T004_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T006 SET T006_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T006_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T006 SET T006_D009_Id_Comprar='{$mD009_Novo['D009_Id']}' WHERE T006_D009_Id_Comprar='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T008 SET T008_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T008_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T008 SET T008_D009_Id_Pendencia='{$mD009_Novo['D009_Id']}' WHERE T008_D009_Id_Pendencia='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T012 SET T012_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T012_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T014 SET T014_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T014_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T028 SET T028_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T028_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T030 SET T030_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T030_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T031 SET T031_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T031_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T044 SET T044_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T044_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T053 SET T053_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T053_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T055 SET T055_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T055_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T058 SET T058_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T058_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_1='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_1='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_2='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_2='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_3='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_3='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_4='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_4='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_5='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_5='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T059 SET T059_D009_Id_6='{$mD009_Novo['D009_Id']}' WHERE T059_D009_Id_6='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T062 SET T062_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T062_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T063 SET T063_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T063_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T071 SET T071_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T071_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T072 SET T072_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T072_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T072 SET T072_D009_Id_Original='{$mD009_Novo['D009_Id']}' WHERE T072_D009_Id_Original='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T079 SET T079_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T079_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T081 SET T081_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T081_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T091 SET T091_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T091_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T099 SET T099_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T099_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T121 SET T121_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T121_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T123 SET T123_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T123_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                $cClausula = "UPDATE T129 SET T129_D009_Id='{$mD009_Novo['D009_Id']}' WHERE T129_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                
                //-- EMILINA O ESTOQUE (T066 ANTIGO)
                $cClausula = "DELETE FROM T066 WHERE T066_D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
                }
                
                //-- EMILINA O ESTOQUE (D009 ANTIGO)
                $cClausula = "DELETE FROM D009 WHERE D009_Id='{$mD009_Antigo['D009_Id']}'";
                $mLog[]    = $cClausula;
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
                }
                
                ////////////////////////////////////////
                //////<---HISTÓRICO DO PRODUTO--->//////
                ////////////////////////////////////////
                
                unset($mGravar);
                
                $Quantidade = $mD009_Novo['D009_Quantidade_Estoque'] + $mD009_Antigo['D009_Quantidade_Estoque'];
                
                $cClausula = <<<EOT
                    INSERT INTO T001 (
                        T001_Data_Lancamento,
                        T001_Descricao_Operacao,
                        T001_Quantidade,
                        T001_Flag_Complemento,
                        T001_Flag_Operacao,
                        T001_Valor_Preco_Unitario,
                        T001_Observacao,
                        T001_Valor_Custo_Unitario,
                        T001_Flag_Atualiza_Custo,
                        T001_Quantidade_Saldo 
                    ) VALUES ( 
                        current_date(), 
                        'MUD.MARCA: {$mD049_Antigo['D082_Marca']}', 
                        '$Quantidade',
                        'M',
                        'B', 
                        '0', 
                        '', 
                        '0', 
                        'N', 
                        '0' 
                    ) 
EOT;
                mysql_query($cClausula);
                
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("rollback");
                    return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
                }
                
                $mLog[] = $cClausula;
                
                // Registra o codigo antigo como substituicao do codigo novo
                
                $mLog[] = "<hr>";
                
            } //end while
            
        } //end else
        
        if (count($mLog) > 0) {
            $cLogJuntarCodigos = implode("\n", $mLog);
            $cLogJuntarCodigos = addslashes($cLogJuntarCodigos);
        }
        
        $cClausula = <<<EOT
            INSERT INTO D017 ( 
                D017_D049_Id, 
                D017_C007_Id, 
                D017_C008_Id, 
                # D017_Codigo_Novo, 
                # D017_Codigo_Antigo, 
                D017_Data_Inclusao, 
                D017_Flag_Mudanca, 
                D017_Log_Juntar_Codigo 
            ) VALUES ( 
                '$D049_Novo_Id', 
                '{$g['usuarioAtual']}', 
                '1', 
                # '$Consulta_D001_D001_Id_Novo', 
                # '$Consulta_D001_D001_Id_Antigo', 
                current_date(), 
                '0', 
                '$cLogJuntarCodigos' 
            ) 
EOT;
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("rollback");
            $retorno = "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog) . "<br />";
            $retrno .= "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mLog);
            return $retorno;
        }
        
        mysql_query("COMMIT");
        
        return true;
    }

    /**
     * excluir
     *
     * @param String $T140_Id
     * @return bool (true)
     */     
    public function excluir($T140_Id, $D001_Id)
    {
        global $g;
        $T140_Id = mysql_real_escape_string($T140_Id);
        $D001_Id = mysql_real_escape_string($D001_Id);

        $T303 = mysql_query("SELECT T303_Id FROM T303 WHERE T303_T140_Id = '{$T140_Id}'");
        if(mysql_num_rows($T303) > 1){
            $sql = "DELETE FROM T303 WHERE T303_D001_Id = '{$D001_Id}' AND T303_T140_Id = '{$T140_Id}'";
            mysql_query($sql);
        } else {
            $sql = "DELETE FROM T303 WHERE T303_D001_Id = '{$D001_Id}' AND T303_T140_Id = '{$T140_Id}'";
            mysql_query($sql);
            $sql = "DELETE FROM T140 WHERE T140_Id='{$T140_Id}'";
            mysql_query($sql);
        }

        return true;
    }

    /**
     * incluirMarca
     *
     * @param Form $form
     */
    public function incluirMarca($form)
    {
        global $g;
        $D001_Id = $g['mysqlLastId'];
        $sqlD001 = mysql_query("SELECT * FROM D001 WHERE D001_Id = '{$D001_Id}'");
        $resD001 = mysql_fetch_array($sqlD001);

//        if ($g['C031']['multimarcas'] == 'N') {
            $D049_D082_Id           = $form->campoValorEnviado('D049_D082_Id');
            $D049_D024_Id           = $form->campoValorEnviado('D049_D024_Id');
            $D049_Flag_Tipo         = $form->campoValorEnviado('D049_Flag_Tipo');
            $D049_Origem_Mercadoria = $form->campoValorEnviado('D049_Origem_Mercadoria');
            $D049_Flag_Nao_Comprar  = $form->campoValorEnviado('D049_Flag_Nao_Comprar');
            mysql_query("INSERT INTO D049 (D049_D082_Id, D049_D001_Id, D049_D024_Id, D049_Flag_Tipo, D049_Origem_Mercadoria,D049_Flag_Nao_Comprar) VALUES ('{$D049_D082_Id}', '{$D001_Id}', '{$D049_D024_Id}', '{$D049_Flag_Tipo}', '{$D049_Origem_Mercadoria}', '{$D049_Flag_Nao_Comprar}')");
            $D049_Id = $g['mysqlLastId'];

            mysql_query("INSERT INTO D041 (D041_D049_Id) VALUES ('$D049_Id')");

            $D009_Valor_Preco_Tabela = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Preco_Tabela'));
            $D009_IPV_2   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_2'));
            $D009_IPV_3   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_3'));
            $D009_IPV_4   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_4'));
            $D009A_IPV_5   = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_IPV_5'));
            $D009_Preco_1 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_1'));
            $D009_Preco_2 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_2'));
            $D009_Preco_3 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_3'));
            $D009_Preco_4 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_4'));
            $D009A_Preco_5 = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_Preco_5'));
            $D009_IPT_1   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_1'));
            $D009_IPT_2   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_2'));
            $D009_IPT_3   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_3'));
            $D009_IPT_4   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_4'));
            $D009A_IPT_5   = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_IPT_5'));
            $C004 = mysql_query("SELECT * FROM C004 ORDER BY C004_Id");
            while ($mC004 = mysql_fetch_array($C004)) {
                
                $D009_ICF_1 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_1'));
                $D009_IPV_1 = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_1'));
                //IPV 1 E ICF SE ESTIVEREM PREENCHIDOS SOBRESCREVEM O VALOR PADRÃO CADASTRADO NO C004
                $D009_ICF_1 = ($D009_ICF_1 > 0) ? $D009_ICF_1 : $mC004['C004_ICF_Padrao'];
                $D009_IPV_1 = ($D009_IPV_1 > 0) ? $D009_IPV_1 : $mC004['C004_IPV_Padrao'];

                $sql = "INSERT INTO D009 (
                                    D009_D049_Id,
                                    D009_D001_Id,
                                    D009_C004_Id,
                                    D009_Valor_Preco_Tabela,
                                    D009_ICF_1,
                                    D009_IPV_1,
                                    D009_IPV_2,
                                    D009_IPV_3,
                                    D009_IPV_4,
                                    D009_Preco_1,
                                    D009_Preco_2,
                                    D009_Preco_3,
                                    D009_Preco_4,
                                    D009_IPT_1,
                                    D009_IPT_2,
                                    D009_IPT_3,
                                    D009_IPT_4
                                ) VALUES (
                                    '{$D049_Id}',
                                    '{$D001_Id}',
                                    '{$mC004['C004_Id']}',
                                    '{$D009_Valor_Preco_Tabela}',
                                    '{$D009_ICF_1}',
                                    '{$D009_IPV_1}',
                                    '{$D009_IPV_2}',
                                    '{$D009_IPV_3}',
                                    '{$D009_IPV_4}',
                                    '{$D009_Preco_1}',
                                    '{$D009_Preco_2}',
                                    '{$D009_Preco_3}',
                                    '{$D009_Preco_4}',
                                    '{$D009_IPT_1}',
                                    '{$D009_IPT_2}',
                                    '{$D009_IPT_3}',
                                    '{$D009_IPT_4}'
                                )";
                mysql_query($sql);
                
                $D009_Id = $g['mysqlLastId'];

                $sqlD009A = "INSERT INTO D009A (
                            D009A_D009_Id,
                            D009A_IPV_5,
                            D009A_Preco_5,
                            D009A_IPT_5
                        ) VALUES (
                            '{$D009_Id}',
                            '{$D009A_IPV_5}',
                            '{$D009A_Preco_5}',
                            '{$D009A_IPT_5}'
                        )";
                mysql_query($sqlD009A);
            }

            $this->atualizarFlagProdutoPertenceEmpresa(
                $D001_Id,
                $form->campoValorEnviado('D009A_Flag_Pertence_Empresa')
            );

            $this->Cad002PersonalizacaoTriggerInserirAposD009($D001_Id);

          //$this->D001_reprocessa_historico($D001_Id);
/*        } else if ($g['C031']['multimarcas'] == 'ambas') {
            if ($resD001['D001_Flag_Multimarcas'] != 'XXX') {
                $D049_D082_Id = $form->campoValorEnviado('D049_D082_Id');
                mysql_query("INSERT INTO D049 (D049_D082_Id, D049_D001_Id) VALUES ('{$D049_D082_Id}', '{$D001_Id}')");
                
                $D049_Id = $g['mysqlLastId'];
                mysql_query("INSERT INTO D041 (D041_D049_Id) VALUES ('$D049_Id')");
                
                $C004 = mysql_query("SELECT * FROM C004 ORDER BY C004_Id");
                
                while ($mC004 = mysql_fetch_array($C004)) {
                    
                    $sql = "INSERT INTO D009 (D009_D049_Id,
                                            D009_D001_Id,
                                            D009_C004_Id,
                                            D009_IPV_1,
                                            D009_ICF_1)
                                        VALUES
                                           ('$D049_Id',
                                            '$D001_Id',
                                            '{$mC004['C004_Id']}',
                                            '{$mC004['C004_IPV_Padrao']}',
                                            '{$mC004['C004_ICF_Padrao']}'
                                         )";
                    mysql_query($sql);

                    $D070 = mysql_query("select D070_Id, D070_D024_Id from D070 where D070_Id='{$g['C031']['inserirTabelaFornecedorPadrao']}'");
                    if (mysql_num_rows($D070)>0) {
                        $D009_Id = mysql_insert_id();
                        $mD070 = mysql_fetch_array($D070);
                        mysql_query("insert into D069 (D069_D024_Id, D069_D070_Id, D069_Codigo_Produto, D069_Descricao_Produto, D069_D009_Id) values ('{$mD070['D070_D024_Id']}','{$mD070['D070_Id']}','{$resD001['D001_Codigo_Produto']}','{$resD001['D001_Descricao_Produto']}','$D009_Id'");
                        $opcoes = array('campoNCM' => 'false', 'campoIPI' => 'false', 'campoNossoCodigo' => 'false', 'campoOrigemProduto' => 'false', 'campoCFOP' => 'false', 'campoICMS' => 'false', 'campoICMSST' => 'false');
                        $this->atualizarAliquotasProdutos("WHERE D069_Id = '{$g['mysqlLastId']}'", $opcoes);
                    }
                }
                $this->D001_reprocessa_historico($D049_D001_Id);
            }
        } */
        
        /*$D049_D082_Id = $form->campoValorEnviado('D049_D082_Id');
        $D049_Flag_Tipo = $form->campoValorEnviado('D049_Flag_Tipo');
        $D049_Modelo = $form->campoValorEnviado('D049_Modelo');
        $D049_Codigo_Produto_Fornecedor = $form->campoValorEnviado('D049_Codigo_Produto_Fornecedor');
        $D049_Valor_Preco_Tabela = $form->campoValorEnviado('D049_Valor_Preco_Tabela');
        $D049_Percentual_Desconto_Tabela = $form->campoValorEnviado('D049_Percentual_Desconto_Tabela');
        $D049_Aliquota_ICMS_Tabela = $form->campoValorEnviado('D049_Aliquota_ICMS_Tabela');
        $D049_IPV = $form->campoValorEnviado('D049_IPV');
        $D049_ICF = $form->campoValorEnviado('D049_ICF');
        $D049_ICF1 = $form->campoValorEnviado('D049_ICF1');
        $D049_ICF2 = $form->campoValorEnviado('D049_ICF2');
        $D049_Observacao_Compra = $form->campoValorEnviado('D049_Observacao_Compra');
        $D049_Flag_Nacional_Importado = $form->campoValorEnviado('D049_Flag_Nacional_Importado');
        $D049_Data_Cadastro = $form->campoValorEnviado('D049_Data_Cadastro');
        $D049_Flag_Ativo = $form->campoValorEnviado('D049_Flag_Ativo');
        $D049_Flag_Substituicao_Tributaria = $form->campoValorEnviado('D049_Flag_Substituicao_Tributaria');
        
        $sql = "INSERT INTO D049 (
        D049_D082_Id,
        D049_Flag_Tipo,
        D049_Modelo,
        D049_Codigo_Produto_Fornecedor,
        D049_Valor_Preco_Tabela,
        D049_Percentual_Desconto_Tabela,
        D049_Aliquota_ICMS_Tabela,
        D049_IPV,
        D049_ICF,
        D049_ICF1,
        D049_ICF2,
        D049_Observacao_Compra,
        D049_Flag_Nacional_Importado,
        D049_Data_Cadastro,
        D049_Flag_Ativo,
        D049_Flag_Substituicao_Tributaria,
        D049_D001_Id
        ) VALUES (
        '{$D049_D082_Id}',
        '{$D049_Flag_Tipo}',
        '{$D049_Modelo}',
        '{$D049_Codigo_Produto_Fornecedor}',
        '{$D049_Valor_Preco_Tabela}',
        '{$D049_Percentual_Desconto_Tabela}',
        '{$D049_Aliquota_ICMS_Tabela}',
        '{$D049_IPV}',
        '{$D049_ICF}',
        '{$D049_ICF1}',
        '{$D049_ICF2}',
        '{$D049_Observacao_Compra}',
        '{$D049_Flag_Nacional_Importado}',
        '{$D049_Data_Cadastro}',
        '{$D049_Flag_Ativo}',
        '{$D049_Flag_Substituicao_Tributaria}',
        '{$D001_Id}',
        )";*/
        mysql_query($sql);
    }
    
    /**
     * Atualiza a visibilidade do produto somente na empresa ativa.
     *
     * O D009A é um complemento de D009. Alguns produtos legados podem não
     * ter esse complemento, por isso ele é criado antes da atualização.
     */
    public function atualizarFlagProdutoPertenceEmpresa($D001_Id, $flag)
    {
        global $g;

        $D001_Id = (int) $D001_Id;
        $C004_Id = (int) $g['empresaAtual'];
        $flag = in_array($flag, array('S', 'N')) ? $flag : '';

        if ($D001_Id <= 0 || $C004_Id <= 0) {
            return false;
        }

        $sql = <<<EOT
            INSERT INTO D009A (D009A_D009_Id, D009A_Flag_Pertence_Empresa)
            SELECT D009.D009_Id, '{$flag}'
              FROM D009
              INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
              LEFT JOIN D009A ON D009A.D009A_D009_Id = D009.D009_Id
             WHERE D049.D049_D001_Id = '{$D001_Id}'
               AND D009.D009_C004_Id = '{$C004_Id}'
               AND D009A.D009A_Id IS NULL
EOT;
        if (!mysql_query($sql)) {
            return mysql_error();
        }

        $sql = <<<EOT
            UPDATE D009A
            INNER JOIN D009 ON D009.D009_Id = D009A.D009A_D009_Id
            INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
               SET D009A.D009A_Flag_Pertence_Empresa = '{$flag}'
             WHERE D049.D049_D001_Id = '{$D001_Id}'
               AND D009.D009_C004_Id = '{$C004_Id}'
EOT;
        if (!mysql_query($sql)) {
            return mysql_error();
        }

        return true;
    }

    /**
     * Retorna o valor consolidado da flag para o produto na empresa ativa.
     */
    public function obterFlagProdutoPertenceEmpresa($D001_Id)
    {
        global $g;

        $D001_Id = (int) $D001_Id;
        $C004_Id = (int) $g['empresaAtual'];
        if ($D001_Id <= 0 || $C004_Id <= 0) {
            return '';
        }

        $sql = <<<EOT
            SELECT CASE
                WHEN SUM(D009A.D009A_Flag_Pertence_Empresa = 'N') > 0 THEN 'N'
                WHEN SUM(D009A.D009A_Flag_Pertence_Empresa = 'S') > 0 THEN 'S'
                ELSE ''
            END AS D009A_Flag_Pertence_Empresa
              FROM D009
              INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
              LEFT JOIN D009A ON D009A.D009A_D009_Id = D009.D009_Id
             WHERE D049.D049_D001_Id = '{$D001_Id}'
               AND D009.D009_C004_Id = '{$C004_Id}'
EOT;
        $resultado = mysql_query($sql);
        if (!$resultado) {
            return '';
        }
        $registro = mysql_fetch_assoc($resultado);

        return isset($registro['D009A_Flag_Pertence_Empresa'])
            ? $registro['D009A_Flag_Pertence_Empresa']
            : '';
    }

    /**
     * atualizarMarca
     *
     * @param Form $form
     * @param String $D001_Id
     * @return true
     */ 
    function atualizarMarca($form, $D001_Id)
    {
        global $g;
        $D049_Id                           = $form->campoValorEnviado('D049_Id');
        $D049_D082_Id                      = $form->campoValorEnviado('D049_D082_Id');
        $D049_D024_Id                      = $form->campoValorEnviado('D049_D024_Id');
        $D049_Flag_Tipo                    = $form->campoValorEnviado('D049_Flag_Tipo');
        $D049_Modelo                       = $form->campoValorEnviado('D049_Modelo');
        $D049_Codigo_Produto_Fornecedor    = $form->campoValorEnviado('D049_Codigo_Produto_Fornecedor');
        $D049_Origem_Mercadoria            = $form->campoValorEnviado('D049_Origem_Mercadoria');
        $D049_Valor_Preco_KG               = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Valor_Preco_KG'));        
        $D049_Percentual_Acrescimo_Tabela  = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Percentual_Acrescimo_Tabela'));
        $D049_Flag_Nao_Comprar             = $form->campoValorEnviado('D049_Flag_Nao_Comprar');
        
        $D049_Observacao_Compra            = $form->campoValorEnviado('D049_Observacao_Compra');
        $D049_Flag_Nacional_Importado      = $form->campoValorEnviado('D049_Flag_Nacional_Importado');
        $D049_Data_Cadastro                = gCorrigeDataInverte($form->campoValorEnviado('D049_Data_Cadastro'));
        $D049_Flag_Ativo                   = $form->campoValorEnviado('D049_Flag_Ativo');
        $D049_Flag_Substituicao_Tributaria = $form->campoValorEnviado('D049_Flag_Substituicao_Tributaria');
        $D049_IPV                          = gCorrigeNumeroInverte($form->campoValorEnviado('D049_IPV'));
        $D049_IPV1                         = gCorrigeNumeroInverte($form->campoValorEnviado('D049_IPV1'));
        $D049_IPV2                         = gCorrigeNumeroInverte($form->campoValorEnviado('D049_IPV2'));
        $D049_Quantidade_Embalagem_Compra  = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Quantidade_Embalagem_Compra'));
        $D041_Valor_Base                   = gCorrigeNumeroInverte($form->campoValorEnviado('D041_Valor_Base'));
        $D041_Valor_Preco_Consumidor       = gCorrigeNumeroInverte($form->campoValorEnviado('D041_Valor_Preco_Consumidor'));
        $D041_Valor_Preco_Revenda          = gCorrigeNumeroInverte($form->campoValorEnviado('D041_Valor_Preco_Revenda'));
        $D041_Valor_Custo                  = gCorrigeNumeroInverte($form->campoValorEnviado('D041_Valor_Custo'));
        
        // Novos campos de preços que eram da D049, alterados para D009
        $D009_IPV_1                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_1'));
        $D009_IPV_2                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_2'));
        $D009_IPV_3                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_3'));
        $D009_IPV_4                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_4'));
        $D009A_IPV_5                       = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_IPV_5'));
        $D009_Preco_Pauta_ST               = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_Pauta_ST'));
        $D009_IPT_1                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_1'));
        $D009_IPT_2                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_2'));
        $D009_IPT_3                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_3'));
        $D009_IPT_4                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPT_4'));
        $D009A_IPT_5                       = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_IPT_5'));
        $D009_ICF_1                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_1'));
        $D009_ICF_2                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_2'));
        $D009_ICF_3                        = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_3'));
        $D009_Valor_Preco_Tabela           = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Preco_Tabela'));
        $D009_Aliquota_ICMS_Tabela         = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Aliquota_ICMS_Tabela'));
        $D009_Percentual_Desconto_Tabela   = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Percentual_Desconto_Tabela'));
        $D009_Preco_1                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_1'));
        $D009_Preco_2                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_2'));
        $D009_Preco_3                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_3'));
        $D009_Preco_4                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_4'));
        $D009A_Preco_5                     = gCorrigeNumeroInverte($form->campoValorEnviado('D009A_Preco_5'));
        $D009_Preco_Loja                   = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_Loja'));
        $D009_Flag_Promocao                = $form->campoValorEnviado('D009_Flag_Promocao');
        $D009_Flag_Usar_Custo              = $form->campoValorEnviado('D009_Flag_Usar_Custo');
        $D009_Flag_Preco_Tabelado          = $form->campoValorEnviado('D009_Flag_Preco_Tabelado');
        $D009_Origem_Mercadoria            = $form->campoValorEnviado('D009_Origem_Mercadoria');
        
        if ($D009_Flag_Promocao == 'on') {
            $D009_Flag_Promocao = "S";
        } else {
            $D009_Flag_Promocao = "N";
        }
        if ($D009_Flag_Preco_Tabelado == 'on') {
            $D009_Flag_Preco_Tabelado = "S";
        } else {
            $D009_Flag_Preco_Tabelado = "N";
        }
        
        $sql = <<<EOT
                    UPDATE D049 SET D049_D082_Id = '{$D049_D082_Id}',
                                D049_D024_Id = '{$D049_D024_Id}',
                                D049_Flag_Tipo = '{$D049_Flag_Tipo}',
                                D049_Modelo = '{$D049_Modelo}',
                                D049_Codigo_Produto_Fornecedor = '{$D049_Codigo_Produto_Fornecedor}',
                                D049_Valor_Preco_Tabela = '{$D049_Valor_Preco_Tabela}',
                                D049_Percentual_Desconto_Tabela = '{$D049_Percentual_Desconto_Tabela}',
                                D049_Percentual_Acrescimo_Tabela = '{$D049_Percentual_Acrescimo_Tabela}',
                                D049_Aliquota_ICMS_Tabela = '{$D049_Aliquota_ICMS_Tabela}',
                                D049_IPV = '{$D049_IPV}',
                                D049_IPV1 = '{$D049_IPV1}',
                                D049_IPV2 = '{$D049_IPV2}',
                                D049_ICF = '{$D049_ICF}',
                                D049_ICF1 = '{$D049_ICF1}',
                                D049_ICF2 = '{$D049_ICF2}',
                                D049_Observacao_Compra = '{$D049_Observacao_Compra}',
                                D049_Flag_Nacional_Importado = '{$D049_Flag_Nacional_Importado}',
                                D049_Data_Cadastro = '{$D049_Data_Cadastro}',
                                D049_Flag_Ativo = '{$D049_Flag_Ativo}',
                                D049_Flag_Substituicao_Tributaria = '{$D049_Flag_Substituicao_Tributaria}',
                                D049_Preco_1='{$D049_Preco_1}',
                                D049_Preco_2='{$D049_Preco_2}',
                                D049_Preco_3='{$D049_Preco_3}',
                                D049_Flag_Promocao='{$D049_Flag_Promocao}',
                                D049_Origem_Mercadoria='{$D049_Origem_Mercadoria}',
                                D049_Valor_Preco_KG='{$D049_Valor_Preco_KG}',
                                D049_Quantidade_Embalagem_Compra='{$D049_Quantidade_Embalagem_Compra}',
                                D049_Flag_Nao_Comprar='{$D049_Flag_Nao_Comprar}'
                          WHERE D049_D001_Id = '{$D001_Id}'
EOT;
        mysql_query($sql);
        log($sql);
        
        $sql = <<<EOT
            UPDATE D041
            LEFT JOIN D049 on D049_Id=D041_D049_Id
            LEFT JOIN D009 on D009_D049_Id=D049_Id
            LEFT JOIN D082 on D082_Id=D049_D082_Id
                  SET D041_Valor_Base            ='{$D041_Valor_Base}',
                      D041_Valor_Preco_Consumidor='{$D041_Valor_Preco_Consumidor}',
                      D041_Valor_Preco_Revenda   ='{$D041_Valor_Preco_Revenda}',
                      D041_Valor_Custo           ='{$D041_Valor_Custo}'
                WHERE D049_D001_Id = '{$D001_Id}'
EOT;
        mysql_query($sql);
        
        /*$sqlD009 = <<<EOT
        UPDATE D009 
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        SET 
        D009_ICF_1='{$D009_ICF_1}',
        D009_ICF_2='{$D009_ICF_2}',
        D009_ICF_3='{$D009_ICF_3}',
        D009_IPV_1='{$D009_IPV_1}',
        D009_IPV_2='{$D009_IPV_2}',
        D009_IPV_3='{$D009_IPV_3}',
        D009_Preco_1='{$D009_Preco_1}',
        D009_Preco_2='{$D009_Preco_2}',
        D009_Preco_3='{$D009_Preco_3}',
        D009_Valor_Preco_Tabela='{$D009_Valor_Preco_Tabela}',
        D009_Aliquota_ICMS_Tabela='{$D009_Aliquota_ICMS_Tabela}',
        D009_Percentual_Desconto_Tabela='{$D009_Percentual_Desconto_Tabela}',
        D009_Flag_Preco_Tabelado = '{$D009_Flag_Preco_Tabelado}',
        D009_Flag_Promocao = '{$D009_Flag_Promocao}'
        WHERE D049_D001_Id = '{$D001_Id}' AND D009_C004_Id = '{$g['empresaAtual']}';
        EOT;
        mysql_query($sqlD009);*/
        
        $sql = <<<EOT
            SELECT D009_Id 
            FROM D009 
            LEFT JOIN D049 ON D049_Id=D009_D049_Id 
            WHERE D049_D001_Id = '{$D001_Id}' AND D009_C004_Id = '{$g['empresaAtual']}';
EOT;
        $res = mysql_query($sql);
        while ($row = mysql_fetch_assoc($res)) {
            $sqlD009 = <<<EOT
                    UPDATE D009 
                    SET 
                    D009_ICF_1='{$D009_ICF_1}',
                    D009_ICF_2='{$D009_ICF_2}',
                    D009_ICF_3='{$D009_ICF_3}',
                    D009_IPV_1='{$D009_IPV_1}',
                    D009_IPV_2='{$D009_IPV_2}',
                    D009_IPV_3='{$D009_IPV_3}',
                    D009_IPV_4='{$D009_IPV_4}',
                    D009_Preco_Pauta_ST='{$D009_Preco_Pauta_ST}',
                    D009_IPT_1='{$D009_IPT_1}',
                    D009_IPT_2='{$D009_IPT_2}',
                    D009_IPT_3='{$D009_IPT_3}',
                    D009_IPT_4='{$D009_IPT_4}',
                    D009_Preco_1='{$D009_Preco_1}',
                    D009_Preco_2='{$D009_Preco_2}',
                    D009_Preco_3='{$D009_Preco_3}',
                    D009_Preco_4='{$D009_Preco_4}',
                    D009_Preco_Loja='{$D009_Preco_Loja}',
                    D009_Valor_Preco_Tabela='{$D009_Valor_Preco_Tabela}',
                    D009_Aliquota_ICMS_Tabela='{$D009_Aliquota_ICMS_Tabela}',
                    D009_Percentual_Desconto_Tabela='{$D009_Percentual_Desconto_Tabela}',
                    D009_Flag_Preco_Tabelado = '{$D009_Flag_Preco_Tabelado}',
                    D009_Flag_Promocao = '{$D009_Flag_Promocao}',
                    D009_Flag_Usar_Custo = '{$D009_Flag_Usar_Custo}',
                    D009_Origem_Mercadoria = '{$D009_Origem_Mercadoria}'
                    WHERE D009_Id = '{$row['D009_Id']}'
EOT;
            log($sqlD009);
            mysql_query($sqlD009);
            
            $sqlD009A = <<<EOT
                    UPDATE D009A SET 
                    D009A_IPV_5='{$D009A_IPV_5}',
                    D009A_IPT_5='{$D009A_IPT_5}',
                    D009A_Preco_5='{$D009A_Preco_5}'
                    WHERE D009A_D009_Id = '{$row['D009_Id']}'
EOT;
            mysql_query($sqlD009A);
        }

        $this->atualizarFlagProdutoPertenceEmpresa(
            $D001_Id,
            $form->campoValorEnviado('D009A_Flag_Pertence_Empresa')
        );

        $D009_Valor_Custo_Unitario       = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Custo_Unitario'));
        $D009_Valor_Custo_Medio_Unitario = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Custo_Medio_Unitario'));
        $D009_Valor_Custo_Compra         = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Custo_Compra'));
        $D009_Valor_Custo_Ultima_Compra  = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Custo_Ultima_Compra'));
        
        return true;
        
    }

    /**
     * atualizarMarcaD009
     *
     * @param Form $form
     * @param String $D049_Id
     */     
    function atualizarMarcaD009($form, $D049_Id)
    {
        global $g;
        
        // Novos campos de preços que eram da D049, alterados para D009
        $D009_IPV_1                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_1'));
        $D009_IPV_2                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_2'));
        $D009_IPV_3                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_IPV_3'));
        $D009_ICF_1                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_1'));
        $D009_ICF_2                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_2'));
        $D009_ICF_3                      = gCorrigeNumeroInverte($form->campoValorEnviado('D009_ICF_3'));
        $D009_Valor_Preco_Tabela         = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Valor_Preco_Tabela'));
        $D009_Aliquota_ICMS_Tabela       = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Aliquota_ICMS_Tabela'));
        $D009_Percentual_Desconto_Tabela = gCorrigeNumeroInverte($form->campoValorEnviado('D049_Percentual_Desconto_Tabela'));
        $D009_Preco_1                    = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_1'));
        $D009_Preco_2                    = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_2'));
        $D009_Preco_3                    = gCorrigeNumeroInverte($form->campoValorEnviado('D009_Preco_3'));
        $D009_Flag_Promocao              = $form->campoValorEnviado('D009_Flag_Promocao');
        
        $D009_Flag_Preco_Tabelado = $form->campoValorEnviado('D009_Flag_Preco_Tabelado');
        
        if ($D009_Flag_Promocao == 'on') {
            $D009_Flag_Promocao = "S";
        } else {
            $D009_Flag_Promocao = "N";
        }
        if ($D009_Flag_Preco_Tabelado == 'on') {
            $D009_Flag_Preco_Tabelado = "S";
        } else {
            $D009_Flag_Preco_Tabelado = "N";
        }
        
        $sqlD009 = <<<EOT
            UPDATE D009 
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            SET 
            D009_ICF_1='{$D009_ICF_1}',
            D009_ICF_2='{$D009_ICF_2}',
            D009_ICF_3='{$D009_ICF_3}',
            D009_IPV_1='{$D009_IPV_1}',
            D009_IPV_2='{$D009_IPV_2}',
            D009_IPV_3='{$D009_IPV_3}',
            D009_Preco_1='{$D009_Preco_1}',
            D009_Preco_2='{$D009_Preco_2}',
            D009_Preco_3='{$D009_Preco_3}',
            D009_Valor_Preco_Tabela='{$D009_Valor_Preco_Tabela}',
            D009_Aliquota_ICMS_Tabela='{$D009_Aliquota_ICMS_Tabela}',
            D009_Percentual_Desconto_Tabela='{$D009_Percentual_Desconto_Tabela}',
            D009_Flag_Preco_Tabelado = '{$D009_Flag_Preco_Tabelado}',
            D009_Flag_Promocao = '{$D009_Flag_Promocao}'
            WHERE D049_Id = '{$D049_Id}' AND D009_C004_Id = '{$g['empresaAtual']}'
EOT;
        mysql_query($sqlD009);
    }
    
    /**
     * cadastroMarca
     *
     * @param Form $form
     */ 
    function cadastroMarca($form)
    {
        global $g;
        
        $D049_Id            = $form->campoValorEnviado('D049_Id');
        $D049_D001_Id       = $form->campoValorEnviado('D049_D001_Id');
        $D049_Data_Cadastro = $form->campoValorEnviado('D049_Data_Cadastro');
        $D049_Flag_Ativo    = $form->campoValorEnviado('D049_Flag_Ativo');
        
        $D049_Id = $g['mysqlLastId'];
        
        mysql_query("INSERT INTO D041 (D041_D049_Id) VALUES ('{$D049_Id}')");
        
        $C004 = mysql_query("SELECT * FROM C004 ORDER BY C004_Id");
        
        while ($mC004 = mysql_fetch_array($C004)) {
            $sql = <<<EOT
                    INSERT INTO D009 (D009_D049_Id,
                                    D009_D001_Id,
                                    D009_C004_Id,
                                    D009_Data_Cadastro,
                                    D009_Flag_Ativo,
                                    D009_IPV_1,
                                    D009_ICF_1
                                    )
                                VALUES
                                   ('$D049_Id',
                                    '$D049_D001_Id',
                                    '{$mC004['C004_Id']}',
                                    '$D049_Data_Cadastro',
                                    '$D049_Flag_Ativo',
                                    '2.50',
                                    '1.05'
                                   )
EOT;
            mysql_query($sql);
        }
        $this->atualizarMarcaD009($form, $D049_Id);
        $this->D001_reprocessa_historico($D049_D001_Id);
        mysql_query("UPDATE D001 SET D001_Flag_Multimarcas = 'S' WHERE D001_Id = '{$D049_D001_Id}'");
    }
    
    /**
     * preCadastroMarca
     *
     * @param String $D049_Id
     * @param String $D001_Id
     * @param String $D041_Valor_Base
     * @param bool $flagPrecoTabelado
     * @return String
     */     
    function preCadastroMarca($D049_Id, $D001_Id, $D041_Valor_Base = "", $flagPrecoTabelado = 'N')
    {
        global $g, $D009_Id_Cotacao;
        $D041_Valor_Base = gCorrigeNumeroInverte($D041_Valor_Base);
        
        $D009_Flag_Preco_Tabelado = $flagPrecoTabelado;
        
        mysql_query("INSERT INTO D041 (D041_D049_Id,D041_Valor_Base) VALUES ('{$D049_Id}','{$D041_Valor_Base}')");
        
        $C004 = mysql_query("SELECT * FROM C004 ORDER BY C004_Id");
        
        while ($mC004 = mysql_fetch_array($C004)) {    
            $sql = <<<EOT
                    INSERT INTO D009 (D009_D049_Id,
                                    D009_D001_Id,
                                    D009_C004_Id,
                                    D009_Data_Cadastro,
                                    D009_Flag_Ativo,
                                    D009_Flag_Preco_Tabelado,
                                    D009_ICF_1,
                                    D009_IPV_1
                                     )
                                VALUES
                                   ('$D049_Id',
                                    '$D001_Id',
                                    '{$mC004['C004_Id']}',
                                    CURDATE(),
                                    'S',
                                    '{$D009_Flag_Preco_Tabelado}',
                                    '{$mC004['C004_ICF_Padrao']}',
                                    '{$mC004['C004_IPV_Padrao']}'
                                 )
EOT;
            mysql_query($sql);
            if ($mC004['C004_Id'] == $g['empresaAtual']) {
                $D009_Id_Cotacao = $g['mysqlLastId'];
            }
        }
        
        $this->D001_reprocessa_historico($D001_Id);
        $this->Cad002PersonalizacaoTriggerInserirAposD009($D001_Id);

        return $D009_Id_Cotacao;
    }
    
    /**
     * D001_reprocessa_historico
     *
     * @param String $D001_Id
     * @param String $D009_Id     
     * @param int $rapido -> Indica quantos registros do histórico vão ser processados(a partir do último no prod)
     * @param bool $venda -> Indica se a função precisa fazer todo o processo inteira lógicas de contagem + venda, se false só faz lógicas de contagem
     * @return true
     */     
    function D001_reprocessa_historico($D001_Id = 0, $D009_Id = 0, $rapido = 0, $venda = false)
    {

        global $g;
        $D024_Id_Fornecedor = NULL;
        $mSaldo_T066        = array();
        $mFD024             = array();
        $mCD024             = array();
        $Custo_Tabela       = NULL;
        $this->D009IsetPendente = array();

        // Melhoria desempenho atualizacao estoque em venda, não está usando nenhuma das duas classes - Felipe 12/09/2022
        //require_once('bibliotecas/classes/VEN001.php');
        //$VEN001 = new VEN001();
        //require_once('bibliotecas/classes/VEN002.php');
        //$VEN002 = new VEN002();

        if ($D009_Id > 0) {
            $D001_Where = "D009_Id = '$D009_Id'";
        } else {
            $D001_Where = "D001_Id = '$D001_Id'";
        }

        // Melhoria desempenho atualizacao estoque em venda, não está usando o $mC020 em nenhum lugar da funcao - Felipe 12/09/2022
        //$C020  = mysql_query("SELECT * FROM C020 WHERE C020_C004_Id='{$g['empresaAtual']}'");
        //$mC020 = mysql_fetch_array($C020);
        
        $cSql_D009 = mysql_query("SELECT D009_Id,
                                         D009_Quantidade_Estoque,
                                         D009_Quantidade_Estoque_Liquido,
                                         D009_Valor_Custo_Medio_Unitario,
                                         D009_Valor_Custo_Compra,
                                         D009_Valor_Melhor_Custo,
                                         D009_Valor_Preco_Tabela,
                                         D049_Flag_Nacional_Importado,
                                         D049_Flag_Tipo,
                                         D049_Quantidade_Minima_Compra,
                                         D049_Quantidade_Embalagem_Compra,
                                         D009_Percentual_Desconto_Tabela,
                                         D009_Percentual_Acrescimo_Tabela,
                                         D009_Aliquota_ICMS_Tabela,
                                         D009_Flag_Usar_Custo,  
                                         D009_Meses_MMF,  
                                         D009_Meses_Compra,
                                         D049_Id,
                                         D009_C004_Id,
                                         D049_D001_Id,
                                         D009_D069_Id,
                                         D009_ICF_1,
                                         D009_IPV_1,
                                         D009_IPV_2,
                                         D009_IPV_3,
                                         D049_IPV,
                                         D049_IPV1,
                                         D049_IPV2,
                                         D009_Valor_Custo_ST_Medio_Unitario,
                                         D009_Valor_Custo_ST_Unitario,
                                         D049_Origem_Mercadoria,
                                         D049_Percentual_Acrescimo_Custo,
                                         D009_Preco_Loja_Real,
                                         D009_T001_Id, 
                                         D149_Flag_ST,
                                         D149_UF_ST,
                                         D009_D049_Id,
                                         D001_Codigo_Produto,
                                         D001_Id,
                                         D009_Data_Ultima_Entrada,
                                         D009A_Flag_Atualizar_Estoque,
                                         IFNULL(D009A_Id,0) as D009A_Id
                                   FROM D009 AS T1
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id
                              LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              LEFT JOIN D149 ON D149_D005_Id=D001_D005_Id and D149_C004_Id=D009_C004_Id
                              LEFT JOIN D009A ON D009_Id=D009A_D009_Id
                                  WHERE $D001_Where");
        
        if (mysql_num_rows($cSql_D009) <= 0) {
            return "O produto não foi encontrado: D001_Id: {$D001_Id} / D009_Id: {$D009_Id}";
        }

        while ($mD009 = mysql_fetch_assoc($cSql_D009)) {

            $D001_Id = $mD009['D001_Id'];
            $D001_Codigo_Produto = $mD009['D001_Codigo_Produto'];
        
	            $cSql_T001           = mysql_query("SELECT T001_Quantidade,
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
	                                                        T001_D024_Id,
                                                            T001_Data_Lancamento,
                                                            if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario,
                                                            D006_Flag_Venda_Compra_Outros,
                                                            T001_Valor_ST_Unitario,
                                                            T014_Flag_Origem_Produto,
                                                            concat(year(T001_Data_Lancamento),month(T001_Data_Lancamento)) mes,
                                                            T001_Valor_Custo_Mes_Anterior,
                                                            T001_Valor_Custo_Medio_Mes_Anterior,
                                                            T001_Quantidade_Saldo_Mes_Anterior,
                                                            T001_Quantidado_Saldo_Locacao,
                                                            T066_D004_Id,
                                                            T001_T014_Id,
                                                            T001_D009_Id,
                                                            T001_Estoque_Locacao,
                                                            T066_D009_Id,
                                                            D004_Local,
                                                            D004_Descricao, 
                                                            D004_Flag_Somar_Estoque_Fisico,
                                                            T001A_Valor_Custo_Unitario_Liquido
                                                       FROM T001
                                                  LEFT JOIN D006 ON D006_Id=T001_D006_Id
                                                  LEFT JOIN T014 ON T014_Id=T001_T014_Id
                                                  LEFT JOIN T066 ON T066_Id=T001_T066_Id 
                                                  LEFT JOIN D004 on D004_Id=T066_D004_Id
                                                  LEFT JOIN T001A ON T001_Id = T001A_T001_Id
                                                      WHERE T001_D009_Id='{$mD009['D009_Id']}' 
                                                   ORDER BY T001_Data_Lancamento, T001_Id");
            
            $count = 0;

            if($rapido>0){

                $count = mysql_num_rows($cSql_T001);
                $count = ( $count - ($rapido + 10) );
               
            }

            if ($rapido>0 and $count>0) {

                mysql_data_seek($cSql_T001, ($count + 10));

                $mcSql_T001 = mysql_fetch_assoc($cSql_T001);

                $D009_Id_Custo = (int) $mD009['D009_Id'];
                $T001_Id_Custo = (int) $mcSql_T001['T001_Id'];
                $dataCusto     = mysql_real_escape_string($mcSql_T001['T001_Data_Lancamento']);

                // O início do processamento rápido pode ser uma saída, que não representa o custo da última compra. 
                // Recupera o custo válido anterior à janela para que os próximos movimentos continuem o cálculo a partir do valor correto.
                $sqlT001 = <<<SQL
                    SELECT
                        T001_Id,
                        T001A_Valor_Custo_Unitario_Liquido
                    FROM T001
                        LEFT JOIN T001A ON T001A_T001_Id = T001_Id
                    WHERE T001_D009_Id = '{$D009_Id_Custo}'
                        AND T001_Flag_Atualiza_Custo = 'S'
                        AND T001_Flag_Operacao NOT IN ('S', 'C')
                        AND (
                            T001_Data_Lancamento < '{$dataCusto}'
                            OR (
                                T001_Data_Lancamento = '{$dataCusto}'
                                AND T001_Id <= '{$T001_Id_Custo}'
                            )
                        )
                    ORDER BY T001_Data_Lancamento DESC, T001_Id DESC
                    LIMIT 1
                SQL;

                $resT001 = mysql_query($sqlT001);

                if ($erro = mysql_error()) {
                    return "Erro ao consultar custo líquido do produto D009 {$D009_Id_Custo}: " . $erro;
                }

                $T001 = mysql_fetch_assoc($resT001);

                $Saldo                      = $mcSql_T001['T001_Quantidade_Saldo'];
                $Custo                      = $mcSql_T001['T001_Valor_Custo_Unitario'];
                $Custo_Medio                = $mcSql_T001['T001_Valor_Custo_Medio_Unitario'];
                $Custo_Compra               = $mcSql_T001['T001_Valor_Custo_Unitario'];
                $Custo_Liquido_Compra       = isset($T001['T001A_Valor_Custo_Unitario_Liquido']) ? $T001['T001A_Valor_Custo_Unitario_Liquido'] : 0;
                $Custo_ST                   = $mcSql_T001['T001_Valor_ST_Unitario'];
                $Custo_ST_Medio             = $mcSql_T001['T001_Valor_ST_Unitario'];
                $Custo_Utilizado            = '';
                //$Data_Ultima_Entrada        = $mcSql_T001['T001_Data_Lancamento'];
                // Estava atualizando a data errada, se não tivesse uma entrada no T001 dentro da quantidade do parametro do rápido, atualiza a data de compra com um registro de venda
                $Data_Ultima_Entrada        = $mD009['D009_Data_Ultima_Entrada'];
                $origemMercadoria           = $mD009['D049_Origem_Mercadoria'];
                $Mes                        = $mcSql_T001['mes'];
                $Custo_Mes_Anterior         = $mcSql_T001['T001_Valor_Custo_Mes_Anterior'];
                $Custo_Medio_Mes_Anterior   = $mcSql_T001['T001_Valor_Custo_Medio_Mes_Anterior'];
                $Saldo_Mes_Anterior         = $mcSql_T001['T001_Quantidade_Saldo_Mes_Anterior'];
                $T066_Id                    = $mcSql_T001['T001_T066_Id'];
                $D009_T001_Id               = $mD009['D009_T001_Id'];
                $D009_Data_Ultima_Venda     = '';
                $D009_Data_Ultima_Saida     = '';
                $D009_Data_Ultimo_Movimento = '';

                if ($mcSql_T001['T001_Flag_MMF'] == 'S'){
                    $D009_Data_Ultima_Venda = $mcSql_T001['T001_Data_Lancamento'];
                }
                if ($mcSql_T001['T001_Flag_Operacao'] == 'S'){
                    $D009_Data_Ultima_Saida = $mcSql_T001['T001_Data_Lancamento'];
                }
                $D009_Data_Ultimo_Movimento = $mcSql_T001['T001_Data_Lancamento'];

                $atualizaEstoqueLocacao = explode('|', $mcSql_T001['T001_Estoque_Locacao']);
                $estoquePorLocacao = unserialize($atualizaEstoqueLocacao[0]);
                $estoquePorLocacaoNaoSoma = unserialize($atualizaEstoqueLocacao[1]);

                if(number_format($Saldo, 4) != number_format(array_sum($estoquePorLocacao), 4)){
                    return " <b><font style='color:red;'> INFORME AO SUPORTE TÉCNICO O ERRO: </font></b> <br/><br/>Histórico Id " . $mcSql_T001['T001_Id'] . " possui a quantidade de saldo (T001_Quantidade_Saldo) diferente da quantidade de saldo locação (T001_Quantidado_Saldo_Locacao).";
                }
 
	            }else{
                
                $Saldo                      = 0;
                $Custo                      = 0;
                $Custo_Medio                = 0;
                $Custo_Compra               = 0;
                $Custo_Liquido_Compra       = 0;
                $Custo_ST                   = 0;
                $Custo_ST_Medio             = 0;
                $Custo_Utilizado            = '';
                $Data_Ultima_Entrada        = '0000-00-00';
                $estoquePorLocacao          = array();
                $estoquePorLocacaoNaoSoma   = array();
                $origemMercadoria           = $mD009['D049_Origem_Mercadoria'];
                $Mes                        = 0;
                $Custo_Mes_Anterior         = 0;
                $Custo_Medio_Mes_Anterior   = 0;
                $Saldo_Mes_Anterior         = 0;
                $D009_T001_Id               = 0;
                $D009_Data_Ultima_Venda     = '';
                $D009_Data_Ultima_Saida     = '';
	            $D009_Data_Ultimo_Movimento = '';


	            }

	            $t001LinhasLidas   = 0;
	            $t001LinhasPuladas = 0;

	            while ($mT001 = mysql_fetch_assoc($cSql_T001)) {
	                $t001LinhasLidas++;
	                // Segurança extra: nunca processar histórico fora da empresa/D009 corrente (em caso de dados inconsistentes).
                    // Teve caso em alguns clientes que processou os dados com o T001 vinculado ao D009 da outra empresa, causando problema no custo e saldo
	                if ($mT001['T001_D009_Id'] !== $mD009['D009_Id']) {
	                    $t001LinhasPuladas++;
	                    continue;
	                }
	                
	                // Corrige histórico de histórico com locação de outra empresa
	                if ($mT001['T066_D009_Id']!=$mD009['D009_Id']){
	                    $T066_Id_Correto = mysql_query("select T066_Id, D004_Id from T066 left join D004 on D004_Id=T066_D004_Id where T066_D009_Id='{$mD009['D009_Id']}' and REPLACE(D004_Local,' ','')=REPLACE('{$mT001['D004_Local']}',' ','')");
                        $mT066_Id_Correto = mysql_fetch_array($T066_Id_Correto);
                        mysql_query("update T001 set T001_T066_Id='{$mT066_Id_Correto['T066_Id']}' where T001_Id='{$mT001['T001_Id']}'");
                        $mT001['T066_D004_Id'] = $mT066_Id_Correto['D004_Id'];
                     }

                    if ($mT001['mes']!=$Mes) {
                        $Mes = $mT001['mes'];
                        $Custo_Mes_Anterior = $Custo_Reais;
                        $Custo_Medio_Mes_Anterior = $Custo_Medio;
                        $Saldo_Mes_Anterior = $Saldo;
                    }

                    $D009_Data_Ultimo_Movimento = $mT001['T001_Data_Lancamento'];

                    // A locação precisa participar do cálculo mesmo quando o histórico foi cancelado,
                    // para que seu saldo seja gravado como zero no reprocessamento.
                    $T066_Id = $mT001['T001_T066_Id'];

                    if($mT001['D004_Flag_Somar_Estoque_Fisico'] == 'S' || empty($mT001['D004_Flag_Somar_Estoque_Fisico'])){
                        if (!empty($T066_Id) && !isset($estoquePorLocacao[$T066_Id])) {
                            $estoquePorLocacao[$T066_Id] = 0;
                        }
                    } else {
                        if (!empty($T066_Id) && !isset($estoquePorLocacaoNaoSoma[$T066_Id])) {
                            $estoquePorLocacaoNaoSoma[$T066_Id] = 0;
                        }
                    }

                    if ($mT001['T001_Flag_Operacao'] != 'C') {
                        
                        if (($mT001['T001_Flag_Atualiza_Custo'] == 'S') and ($mT001['T001_Flag_Operacao'] != 'S')) {
                            if (($Saldo + $mT001['T001_Quantidade']) > 0 and $Saldo > 0 and $Custo_Medio > 0 and $mT001['T001_Flag_Operacao'] != 'B') {
                                $Custo_Medio = (($Saldo * $Custo_Medio) + ($mT001['T001_Valor_Preco_Unitario'] * $mT001['T001_Quantidade'])) / ($Saldo + $mT001['T001_Quantidade']);
                            } else {
                                $Custo_Medio = $mT001['T001_Valor_Preco_Unitario'];
                            }
                            $D009_T001_Id = $mT001['T001_Id'];
                            
                            // Custo ST Médio
                            if (($Saldo + $mT001['T001_Quantidade']) > 0 and $Saldo > 0 and $Custo_ST_Medio > 0 and $mT001['T001_Flag_Operacao'] != 'B') {
                                $Custo_ST_Medio = (($Saldo * $Custo_ST_Medio) + ($mT001['T001_Valor_ST_Unitario'] * $mT001['T001_Quantidade'])) / ($Saldo + $mT001['T001_Quantidade']);
                            } else {
                                $Custo_ST_Medio = $mT001['T001_Valor_ST_Unitario'];
                            }
                            
                            $Custo_Compra         = $mT001['T001_Valor_Custo_Unitario'];
                            $Custo_Liquido_Compra = $mT001['T001A_Valor_Custo_Unitario_Liquido'];

                            $Custo_ST             = $mT001['T001_Valor_ST_Unitario'];
                            $Data_Ultima_Entrada  = $mT001['T001_Data_Lancamento'];
                            $origemMercadoria     = $mT001['T014_Flag_Origem_Produto'];
                            
                            $D024_Id              = $mT001['T001_D024_Id'];
                            $D024_Id_Fornecedor   = $mT001['T001_D024_Id'];
                            $mFD024['{$D024_Id}'] = $D024_Id;
                        }

                        if ($mT001['T001_Flag_Operacao'] == 'S') {

                            if ($mT001['T001_Flag_MMF'] == 'S'){
                                $D009_Data_Ultima_Venda = $mT001['T001_Data_Lancamento'];
                            }
                            $D009_Data_Ultima_Saida = $mT001['T001_Data_Lancamento'];

                            if ($mT001['D006_Flag_Venda_Compra_Outros'] == 'V') {
                                $D024_Id             = $mT001['T001_D024_Id'];
                                $mCD024['{$D024_Id'] = $D024_Id;
                            }
                            // por locacao
                            if (!empty($T066_Id)) {
                            if($mT001['D004_Flag_Somar_Estoque_Fisico'] == 'S' || empty($mT001['D004_Flag_Somar_Estoque_Fisico'])){
                                    $estoquePorLocacao[$T066_Id] -= $mT001['T001_Quantidade'];
                            } else {
                                    $estoquePorLocacaoNaoSoma[$T066_Id] -= $mT001['T001_Quantidade'];
                                }
                            }
                            if ($mT001['T001_T008_Id'] > 0 && $venda !== true){
                            $this->populaT290($mT001['T001_D009_Id'], $mT001['T001_T008_Id'], $mT001['T001_Quantidade'], $mT001['T001_Flag_Operacao'], $mT001['T001_Data_Lancamento']); 
                            }
                        } else if ($mT001['T001_Flag_Operacao'] == 'E') {
                            //if ($T066_Id>0) { $mSaldo_T066[$T066_Id]+=$mT001['T001_Quantidade']; }
                            // por locacao
                            if (!empty($T066_Id)) {
                                if($mT001['D004_Flag_Somar_Estoque_Fisico'] == 'S' || empty($mT001['D004_Flag_Somar_Estoque_Fisico'])){
                                    $estoquePorLocacao[$T066_Id] += $mT001['T001_Quantidade'];
                                } else {
                                    $estoquePorLocacaoNaoSoma[$T066_Id] += $mT001['T001_Quantidade'];
                                }
                            }
                            // Popula a tabela T289 apenas com nota de entrada
                            if ( ( $mT001['T001_T014_Id'] > 0 || $mT001['T001_T008_Id'] > 0) && $venda !== true ) {
                                $this->populaT289($mT001['T001_D009_Id'], $mT001['T001_T014_Id'], $mT001['T001_T008_Id'], $mT001['T001_Quantidade'], $mT001['T001_Flag_Operacao'], $mT001['T001_Data_Lancamento']); 
                            }
                        } else if ($mT001['T001_Flag_Operacao'] == 'B') {
                            //if ($T066_Id>0) { $mSaldo_T066[$T066_Id]=$mT001['T001_Quantidade']; }
                            // por locacao
                            if (!empty($T066_Id)) {
                                if($mT001['D004_Flag_Somar_Estoque_Fisico'] == 'S' || empty($mT001['D004_Flag_Somar_Estoque_Fisico'])){
                                    $estoquePorLocacao[$T066_Id] = $mT001['T001_Quantidade'];
                                } else {
                                    $estoquePorLocacaoNaoSoma[$T066_Id] = $mT001['T001_Quantidade'];
                                }
                            }
                        }
                        $Saldo = array_sum($estoquePorLocacao);
                    }

                    // Gera um Update em T289 caso a nota de compra tenha sido cancelada
                    if ($mT001['T001_Flag_Operacao'] == 'C' && $mT001['D006_Flag_Venda_Compra_Outros'] == 'C' && $mT001['T001_T014_Id'] > 0 && $venda !== true) {
                        $this->populaT289($mT001['T001_D009_Id'], $mT001['T001_T014_Id'], $mT001['T001_T008_Id'], $mT001['T001_Quantidade'], $mT001['T001_Flag_Operacao'], $mT001['T001_Data_Lancamento']);
                    }

                    // Gera um Update em T290 caso a nota de venda tenha sido cancelada
                    if ($mT001['T001_Flag_Operacao'] == 'C' && $mT001['D006_Flag_Venda_Compra_Outros'] == 'V' && $mT001['T001_T008_Id'] > 0 && $venda !== true) {
                    $this->populaT290($mT001['T001_D009_Id'], $mT001['T001_T008_Id'], $mT001['T001_Quantidade'], $mT001['T001_Flag_Operacao'], $mT001['T001_Data_Lancamento']); 
                    }

                    $saldoT066 = 0;
                    if (!empty($T066_Id)) {
                        if($mT001['D004_Flag_Somar_Estoque_Fisico'] == 'S' || empty($mT001['D004_Flag_Somar_Estoque_Fisico'])){
                            $saldoT066 = $estoquePorLocacao[$T066_Id];
                        } else {
                            $saldoT066 = $estoquePorLocacaoNaoSoma[$T066_Id];
                        }
                    }
                    
                    $atualizaEstoqueLocacao = serialize($estoquePorLocacao) . '|' . serialize($estoquePorLocacaoNaoSoma);

	                $Custo_Reais = $Custo_Compra;
	                if ((number_format($Saldo, 2) != number_format($mT001['T001_Quantidade_Saldo'], 2)) 
	                or (number_format($saldoT066, 2) != number_format($mT001['T001_Quantidado_Saldo_Locacao'], 2))
	                or (number_format($Custo_Medio, 2) != number_format($mT001['T001_Valor_Custo_Medio_Unitario'], 2)) 
	                or (number_format($mT001['T001_Valor_Custo_Unitario'], 2) != number_format($Custo_Reais, 2))
	                or (($mT001['T001_Valor_Custo_Unitario'] <= 0) and ($Custo_Reais > 0))
	                or (number_format($Saldo_Mes_Anterior, 2) != number_format($mT001['T001_Quantidade_Saldo_Mes_Anterior'], 2))
	                or (number_format($Custo_Mes_Anterior, 2) != number_format($mT001['T001_Valor_Custo_Mes_Anterior'], 2))
	                or (number_format($Custo_Medio_Mes_Anterior, 2) != number_format($mT001['T001_Valor_Custo_Medio_Mes_Anterior'], 2))
	                or ($atualizaEstoqueLocacao != $mT001['T001_Estoque_Locacao'])) {
	                    mysqli_query("UPDATE T001
	                                        LEFT JOIN D006 ON D006_Id=T001_D006_Id
	                                             SET T001_Quantidade_Saldo='$Saldo',
                                                 T001_Quantidado_Saldo_Locacao='{$saldoT066}',
                                                 T001_Valor_Custo_Unitario=if(IFNULL(D006_Flag_Venda_Compra_Outros,'')!='C','$Custo_Reais',T001_Valor_Custo_Unitario),
                                                 T001_Valor_Custo_Medio_Unitario='$Custo_Medio',
                                                 T001_Valor_Custo_Mes_Anterior='$Custo_Mes_Anterior',
                                                 T001_Valor_Custo_Medio_Mes_Anterior='$Custo_Medio_Mes_Anterior',
                                                 T001_Quantidade_Saldo_Mes_Anterior='$Saldo_Mes_Anterior',
                                                 T001_Estoque_Locacao='$atualizaEstoqueLocacao'
	                                           WHERE T001_Id='{$mT001['T001_Id']}'");
	                }
	
	            }

                // Teve caso em alguns clientes que processou os dados com o T001 vinculado ao D009 da outra empresa, causando problema no custo e saldo
                // Se não processou nada por algum erro de T001 errado, manda para o proesso automatico novamente
	            $t001LinhasProcessadas = ($t001LinhasLidas - $t001LinhasPuladas);
               
	            if ($t001LinhasLidas > 0 && $t001LinhasProcessadas <= 0) {
                    if (!empty($mD009['D009A_Id']) && $mD009['D009A_Id'] > 0) {
                        mysqli_query("UPDATE D009A
                                         SET D009A_Flag_Atualizar_Estoque = 'S'
                                       WHERE D009A_D009_Id = '{$mD009['D009_Id']}'");
                    } else {
                        mysqli_query("INSERT INTO D009A (D009A_D009_Id, D009A_Flag_Atualizar_Estoque)
                                        VALUES ('{$mD009['D009_Id']}', 'S')");
                    }
                    continue;                
	            } 

	            // por locação
	            foreach ($estoquePorLocacao as $T066_Id => $SaldoPorLocacao) {
	                mysql_query("UPDATE T066 SET T066_Quantidade_Estoque = '{$SaldoPorLocacao}' WHERE T066_Id = '{$T066_Id}' AND T066_D009_Id='{$mD009['D009_Id']}'");
	            }            
	            // por locação
	            foreach ($estoquePorLocacaoNaoSoma as $T066_Id => $SaldoPorLocacao) {
	                mysql_query("UPDATE T066 SET T066_Quantidade_Estoque = '{$SaldoPorLocacao}' WHERE T066_Id = '{$T066_Id}' AND T066_D009_Id='{$mD009['D009_Id']}'");
	            }  
            
            
            if ( $venda === true ) {
                // Case seja atualização estoque modo VENDA atualiza estoque liquido das locações e finaliza a function

                // T066_Quantidade_Estoque_Liquido
                mysql_query("UPDATE T066 
                                SET T066_Quantidade_Estoque_Liquido = (T066_Quantidade_Estoque-D009_Quantidade_Pedido_Por_Locacao(T066_D009_Id, T066_Id))
                              WHERE T066_D009_Id = '{$mD009['D009_Id']}'");

                mysql_query("UPDATE D009
                                SET D009_Quantidade_Estoque='{$Saldo}',
                                    D009_Data_Ultima_Venda='{$D009_Data_Ultima_Venda}',
                                    D009_Data_Ultima_Saida='{$D009_Data_Ultima_Saida}',
                                    D009_Data_Ultimo_Movimento='{$D009_Data_Ultimo_Movimento}'
                              WHERE D009_Id='{$mD009['D009_Id']}'");                
                
                //Flag utilizada no processo automático para atualizar estoque
                if($mD009['D009A_Id'] > 0){
                    mysqli_query("UPDATE D009A SET D009A_Flag_Atualizar_Estoque = 'S'
                                             WHERE D009A_D009_Id = '{$mD009['D009_Id']}'");
                } else {
                    mysqli_query("INSERT INTO D009A (D009A_D009_Id, 
                                                     D009A_Flag_Atualizar_Estoque
                                            )VALUES('{$mD009['D009_Id']}',
                                                    'S')");
                }


                // Melhoria desempenho atualizacao estoque em venda, vai fazer pela chamada do crontab - Felipe 12/09/2022
                //if($Saldo <= 0){
                    // Se o estoque zerar, atualiza o custo para pegar a regra configurada do custo com estoque 0
                //    $this->atualizarCustoProduto($mD009['D009_Id'], $dadosCusto);               
                //} 

                continue;

            }   

            $Saldo_Regras = $Saldo;            
            $dadosCusto = array(
                'Custo_Medio'           => $Custo_Medio, 
                'Custo_Compra'          => $Custo_Compra, 
                'Saldo_Regras'          => $Saldo_Regras, 
                'Data_Ultima_Entrada'   => $Data_Ultima_Entrada, 
                'Custo_Liquido_Compra'  => $Custo_Liquido_Compra, 
            );
            
            //Atualiza o custo do produto
            $this->atualizarCustoProduto($mD009['D009_Id'], $dadosCusto);

            //Atualiza os dados estatisticos utilizados em compras de estoque
            $this->atualizarEstatisticasCompra($mD009['D009_Id'], $Saldo);

            if ($Custo < 0) {
                $Custo = 0;
            }
            
            if ($mD009['D009_ICF_1'] <= 0) {
                $mD009['D009_ICF_1'] = 0;
            }
            //if ($Custo <= 0 or ($Saldo <= 0 AND $Custo_Tabela > 0)) {
            //    $Custo = $Custo_Tabela;
            //    $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
            //    $Custo_Utilizado = 'T';
            //}



			// Verifica caso o campo US ST esteja preenchida na NCM se a UF destino está na lista
			$D018_UF_ST = mysql_query("SELECT D018_UF FROM D018 WHERE D018_Id='{$g['c004']['C004_D018_Id']}'");
			$mD018_UF_ST = mysql_fetch_array($D018_UF_ST);
            if ($mD009['D149_Flag_ST'] == 'S' && $mD009['D149_UF_ST'] !='') {
                $pos = strpos($mD009['D149_UF_ST'], $mD018_UF_ST['D018_UF']);
                if ($pos === false) {
                    $mD009['D149_Flag_ST'] = "N";
                }
            }


            $arrayPrecoLoja = $this->RetornaPrecoLoja($mD009);

            $preco = $arrayPrecoLoja['preco'];
            $demonstrativo = $arrayPrecoLoja['demonstrativo'];

            $diasMP=$g['C031']['diasMedioPrazo'];
            $data = strtotime("+$diasMP day", strtotime("now"));
            $dataMP = date("Y-m-d", $data); 
            
            $retorno=mysql_query("select sum(T055_Quantidade) as QteOC from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id='{$mD009['D009_Id']}' AND ifnull(T075_Data_Estoque,'0000-00-00')='0000-00-00' and T075_Data_Previsao<='{$dataMP}' and T075_Data_Previsao>0 and T075_Flag_MP = 1");
            $retorno=mysql_fetch_array($retorno);            
            $qteOC=$retorno['QteOC'];
            
            $retorno=mysql_query("SELECT T075_Id FROM T075 LEFT JOIN T055 ON T075_Id=T055_T075_Id LEFT JOIN D009 ON T055_D009_Id=D009_Id WHERE T055_D009_Id='{$mD009['D009_Id']}' and T075_Data_Previsao<='{$dataMP}' and T075_Flag_MP = 1 group by T075_Id");
            $qtePed=0;
            while ($ret=mysql_fetch_array($retorno)){
                $retorno1=mysql_query("select sum(T006_Quantidade) 
                    from T006 
                    left join T005 on T005_Id=T006_T005_Id 
                    where T006_D009_Id='{$mD009['D009_Id']}'
                    and T005_Flag_Status!=5
                    and T005_Flag_Status!='7'
                    and T005_Flag_Status!='8'
                    and T005_Flag_Status!='11'
                    and T006_T075_Id='{$ret['T075_Id']}'
                    and (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)");
                $ret1=mysql_fetch_array($retorno1);
                $qtePed+=$ret1[0];
            }
            $D009_Quantidade_Estoque_Medio_Prazo= ($qteOC-$qtePed);

             // D009_Quantidade_DF=D009_Saldo_DF(D009_Id),
             // D009_Quantidade_DM=D009_Saldo_DM(D009_Id),
            mysql_query("UPDATE D009
                             SET D009_Quantidade_Estoque='$Saldo',
                                 D009_T001_Id=IF(D009_Valor_Custo_Compra>0,'{$D009_T001_Id}',0),
                                 D009_Valor_Custo_ST_Medio_Unitario='{$Custo_ST_Medio}',
                                 D009_Valor_Custo_ST_Unitario='{$Custo_ST}',
                                 D009_Quantidade_OC=D009_Quantidade_OC_3(D009_Id,0),
                                 D009_Quantidade_OP=D009_Quantidade_OP(D009_Id),
                                 D009_Quantidade_OE=D009_Saldo_OE(D009_Id,0),
                                 D009_Quantidade_Estoque_Tabela=D009_Quantidade_Estoque_Tabela(D009_Id),
                                 D009_Quantidade_Estoque_Medio_Prazo='{$D009_Quantidade_Estoque_Medio_Prazo}',
                                 D009_Data_Reprocessa_Historico=now(),
                                 D009_Preco_Loja_Real = TRUNCATE('{$preco}', 2), 
                                 D009_Preco_Loja_Real_T001_Id = '{$mD009['D009_T001_Id']}', 
                                 D009_Preco_Loja_Real_Demonstrativo = '{$demonstrativo}',
                                 D009_Data_Ultima_Venda='{$D009_Data_Ultima_Venda}',
                                 D009_Data_Ultima_Saida='{$D009_Data_Ultima_Saida}',
                                 D009_Data_Ultimo_Movimento='{$D009_Data_Ultimo_Movimento}'
                           WHERE D009_Id='{$mD009['D009_Id']}'");

            $sqlSaldo = "SELECT (D009_Quantidade_Estoque-D009_Saldo_OE(D009_Id,0)-D009_Quantidade_Estoque_Loja) as Saldo FROM D009 WHERE D009_Id='{$mD009['D009_Id']}'";
            $resSaldo = mysql_query($sqlSaldo);
            $rowSaldo = mysql_fetch_assoc($resSaldo);
            $Saldo = $rowSaldo['Saldo'];

            if ($D009_Id>0) {
                /* Como o D009_Id é maior que zero(foi enviado por parametro) força o recalculo(chama trigger D009_trigger_estoque_real)
                dos estoques de todos os D009 do produto, isso acontece pois utiliza a chave estrangeira dos D009, a D049_Id no where.
                Isso faz com que na trigger todos os D009 são chamados para UPDATE. Utilizamos mysqli para não chamar as triggers
                do php e economizar em performance*/
                mysqli_query("UPDATE D009
                                 SET D009_Quantidade_Estoque_Fora = 0
                               WHERE D009_D049_Id = '{$mD009['D009_D049_Id']}'");
            } else {
                /*Força o recalculo do estoque líquido (o estoque da LOJA pode ter mudado, e ele é deduzido do líquido na trigger)
                Recalcula todos os itens do D009 pois estamos num loop a partir da D001(linha 1078)*/
                mysqli_query("UPDATE D009
                                 SET D009_Quantidade_Estoque_Liquido=0 
                               WHERE D009_Id='{$mD009['D009_Id']}'");
            }

            //Flag utilizada no processo automático para atualizar estoque
            //Processou o estoque completo, altera flag para N
            mysqli_query("UPDATE D009A SET D009A_Flag_Atualizar_Estoque = 'N'
                                     WHERE D009A_D009_Id = '{$mD009['D009_Id']}'");
            // Estoque por locação
            $this->processaEstoqueLiquidoPorLocacao($mD009['D009_Id']);

            // Reprocessa estoque dos pedidos
            $this->processaEstoquePedidos($mD009['D009_Id'], $Saldo);

            // Reprocessa OC dos pedidos
            $this->reprocessaOCPedidos($mD009['D009_Id']);

            // Reprocessa OP dos pedidos
            $this->reprocessaOPPedidos($mD009['D009_Id']);


            // Atualiza preço tabela orçamento 
            if($g['C031']['atualizaPrecoTabelaOrcamento'] == 'S'){
                $T004 = mysql_query("SELECT D009_Valor_Custo_Unitario,
                                            D009_ICF_1,
                                            D049_Origem_Mercadoria,
                                            D009_T001_Id,
                                            D009_Id,
                                            D049_Id,
                                            T003_Flag_Lista_Preco,
                                            T003_D024_Id,
                                            T003_Flag_ACP,
                                            T004_D006_Id,
                                            T004_Aliquota_ICMS,
                                            T004_Aliquota_PIS,
                                            T004_Aliquota_COFINS,
                                            T004_Aliquota_IPI,
                                            D009_T001_Id,
                                            T004_T219_Id,
                                            T004_Id,
                                            T219_Flag_Comprar,
                                            T004_Flag_Origem_Produto,
                                            T004_D005_Id,
                                            T004_D006_Id,
                                            T004_T003_Id,
                                            T219_Flag_Origem_Produto,
                                            T219_Valor_Custo_Unitario,
                                            T004_ST_VA,
                                            T004_Percentual_Reducao_ICMS_ST,
                                            T004_ST_ICMS_Interno,
                                            T004_Percentual_Reducao_ICMS,
                                            T004_ICMS_Inter_Estadual,
                                            T004_Aliquota_IRPJ,
                                            T004_Aliquota_CSLL,
                                            T004_Flag_Transferencia,
                                            D009_Quantidade_Estoque_Liquido,
                                            T004_Quantidade,
                                            T004_C004_Id_Transferencia,
                                            T004_Valor_Preco_Original,
                                            T004_Valor_Custo_Unitario,
                                            T004_Valor_Preco_Tabela,
                                            T004_Valor_Preco_Transferencia,
                                            T004_T001_Id,
                                            T006_T219_Id,
                                            T219_T218_Id,
                                            T003A_Flag_Ratear_Frete_Custo,
                                            T003_Casas_Decimais_Preco                                            
                                            FROM T004
                                        LEFT JOIN T006 ON T006_T004_Id=T004_Id
                                        LEFT JOIN D009 ON D009_Id=T004_D009_Id
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                        LEFT JOIN T003 ON T003_Id=T004_T003_Id
                                        LEFT JOIN T001 ON T001_Id=D009_T001_Id
                                        LEFT JOIN T219 ON T219_Id=T004_T219_Id
                                        LEFT JOIN T003A ON T003_Id=T003A_T003_Id
                                            WHERE T004_D009_Id='{$mD009['D009_Id']}'
                                            AND (T006_Id IS NULL OR (IFNULL(T004_T219_Id,0) <= 0 AND T004_Flag_Comprar = 'S'))
                                            AND T003_Data_Emissao BETWEEN DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND CURRENT_DATE()
                                            AND T003_Flag_Perdido!='S'");
                while ($mT004 = mysql_fetch_array($T004)) {
                    //Método de atualização do preço tabela dos produtos
                    $this->atualizaPrecoTabelaOrcamento($mT004);
                }
            }

            if($g['C031']['atualizaPrecoTabelaPedido'] == 'S'){
                // Atualiza preço tabela pedido 
                $T006 = mysql_query("SELECT D009_Valor_Custo_Unitario,
                                            D009_ICF_1,
                                            D049_Origem_Mercadoria,
                                            D009_T001_Id,
                                            D009_Id,
                                            D049_Id,
                                            T005_Flag_Lista_Preco,
                                            T005_D024_Id,
                                            T005_Flag_ACP,
                                            T005_C004_Id,
                                            T006_D006_Id,
                                            T005_Aliquota_ICMS,
                                            T006_Flag_Destino_Produto,
                                            D009_T001_Id,
                                            T006_Id,
                                            T005_Id,
                                            T219_Flag_Origem_Produto,
                                            T006_D005_Id,
                                            T006_Flag_Origem_Produto,
                                            T219_Flag_Comprar,
                                            T219_Valor_Custo_Unitario,
                                            T006_T219_Id,
                                            T006_T225_Id,
                                            T006_Aliquota_ICMS,
                                            T006_Aliquota_PIS,
                                            T006_Aliquota_COFINS,
                                            T006_Aliquota_IPI,
                                            T006_D009_Id,
                                            T006_T005_Id,
                                            T006_Valor_Preco_Original,
                                            T006_Valor_Custo_Unitario,
                                            ifnull(T005_T224_Id,0) as T005_T224_Id,
                                            T006_Valor_Preco_Tabela,
                                            T006_ST_VA,
                                            T006_Percentual_Reducao_ICMS_ST,
                                            T006_ST_ICMS_Interno,
                                            T006_Percentual_Reducao_ICMS,
                                            T006_ICMS_Inter_Estadual,
                                            T006_Aliquota_IRPJ,
                                            T006_Aliquota_CSLL,
                                            T006_Flag_Transferencia,
                                            T006_Quantidade_Estoque,
                                            T006_Quantidade,
                                            T006_C004_Id_Transferencia,
                                            T006_Valor_Preco_Transferencia,
                                            T006_T001_Id,
                                            T005A_Flag_Ratear_Frete_Custo,
                                            T005_Casas_Decimais_Preco
                                    FROM T006
                                LEFT JOIN T008 ON T008_T006_Id=T006_Id
                                LEFT JOIN T007 ON T007_Id=T008_T007_Id
                                LEFT JOIN D009 ON D009_Id=T006_D009_Id
                                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                                LEFT JOIN T219 ON T219_Id=T006_T219_Id
                                LEFT JOIN T005A ON T005_Id=T005A_T005_Id
                                    WHERE T006_D009_Id='{$mD009['D009_Id']}'
                                        AND (T008_Id IS NULL OR T007_Flag_Cancelada='S')
                                        AND T005_Flag_Status!='8'
                                        AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)");
                while ($mT006 = mysql_fetch_array($T006)) {
                    //Método de atualização do preço tabela dos produtos
                    $this->atualizaPrecoTabelaPedido($mT006);
                }
            }

            $this->triggerFimLoopReprocessa($mD009);
        }

        // Reprocessa estoque similares
        // Melhoria desempenho atualizacao estoque em venda, vai fazer pela chamada do crontab - Felipe 12/09/2022
        if (!empty($D001_Codigo_Produto) && $venda !== true) {
            $this->reprocessaEstoqueSimilares($D001_Id, $D001_Codigo_Produto);
        }

        $this->triggerAposReprocessa();

        mysql_free_result($cSql_D009);
        mysql_free_result($cSql_T001);
        mysql_free_result($retorno);
        mysql_free_result($T004);
        mysql_free_result($T006);

        return true;
    }

    protected function triggerFimLoopReprocessa($mD009) {
        return true;
    }

    protected function triggerAposReprocessa() {
        return true;
    }

    /**
     * gravarSubstituicao
     *
     * @param Form $form
     * @return String/bool
     */
    public function gravarSubstituicao($form)
    {
        global $g;

        $D017_Codigo_Antigo = $form->campoValorEnviado('D017_Codigo_Antigo');
        $D001_Id            = $form->campoValorEnviado('D001_Id');

        $cClausula = <<<EOT
            INSERT INTO D017 (
                D017_D001_Id, 
                D017_C007_Id, 
                D017_Codigo_Antigo, 
                D017_Data_Inclusao 
            ) VALUES ( 
                '{$D001_Id}', 
                '{$g['usuarioAtual']}', 
                '{$D017_Codigo_Antigo}', 
                curdate()
            ) 
EOT;
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: " . $cErro . "<br />" . $cClausula;
        } else {
            return true;
        }
    }
    
    /**
     * updateD049_Flag_Padrao
     *
     * @param String $D001_Id
     * @param String $selected     
     * @return true
     */ 
    public function updateD049_Flag_Padrao($D001_Id, $selected)
    {
        global $g;
        
        $D049  = mysql_query("SELECT D049_Id FROM D049 WHERE D049_D001_Id='$D001_Id'");
        $mD049 = mysql_fetch_array($D049);
        
        if (mysql_num_rows($D049) <= 0) {
            mysql_query("INSERT INTO D049 (D049_Flag_Padrao) VALUES ('S')");
        } else {
            mysql_query("UPDATE D049 SET D049_Flag_Padrao='$selected' WHERE D049_Id='{$mD049['D049_Id']}'");
        }        
        return true;        
    }

    /**
     * cad002gravarProduto
     *
     * @param String $D049_Id_Itens
     * @param String $D049_Id     
     * @return String/bool
     */     
    public function cad002gravarProduto($D049_Id_Itens, $D049_Id)
    {       
        global $g;
         
        if (empty($D049_Id)) {
            return "Nao foi recebido o id da nota = D049_Id";
        }
        
        $cClausula = "SELECT * FROM D049 LEFT JOIN D001 on D001_Id=D049_D001_Id WHERE D049_Id='{$D049_Id}'";
        $D042  = mysql_query($cClausula);
        $mD042 = mysql_fetch_array($D042);
        if (mysql_num_rows($D042) == 0) {
            exit;
        }
        
        if ($mD042['D001_Flag_Ativo'] != 'S') {
            return "**** Este item está inativo! *****";
        }

        if ($mD042['D049_Flag_Tipo'] == 'MD') {
            $sql = mysql_query("SELECT D042_Id FROM D042 LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens WHERE D042_D049_Id='{$mD042['D049_Id']}'");
            if(mysql_num_rows($sql) == 1){
                return "Este produto é modificado, é permitido apenas um produto bruto.";
            }

        }

        $cClausula = <<<EOT
            INSERT INTO D042 (
                D042_D049_Id, 
                D042_D049_Id_Itens, 
                D042_C004_Id 
            ) VALUES ( 
                '{$D049_Id}', 
                '{$D049_Id_Itens}', 
                '{$g['empresaAtual']}' 
            ) 
EOT;
        $D042  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Por favor, contate o suporte técnico.<br />$cErro<br /><br />$cClausula";
        } else {
            return true;
        }
    }

    /**
     * verificarCodigoProduto
     *
     * @param Form $form
     * @param String $D001_Id     
     * @return String/bool
     */     
    public function verificarCodigoProduto($form, $D001_Id = false)
    {
        $D001_Codigo_Produto = $form->campoValorEnviado('D001_Codigo_Produto');
        if ($D001_Id) {
            $sql = "SELECT * FROM D001 WHERE D001_Codigo_Produto='{$D001_Codigo_Produto}' AND D001_Id != {$D001_Id}";
        } else {
            $sql = "SELECT * FROM D001 WHERE D001_Codigo_Produto='{$D001_Codigo_Produto}'";
        }

        $result = mysql_query($sql);
        if (mysql_num_rows($result) > 0) {
            return "Já existe o código " . $D001_Codigo_Produto . " cadastrado. Por favor, insira um novo código";
        } else {
            return true;
        }
    }
    
    /**
     * verificarLocal
     *
     * @param Form $form
     * @return String/bool
     */     
    public function verificarLocal($form)
    {
        $D004_Local = $form->campoValorEnviado('T066_D004_Id');
        $D009_Id    = $form->campoValorEnviado('T066_D009_Id');
        $sql        = "SELECT D004_Flag_Tipo, T066_D004_Id FROM T066 LEFT JOIN D004 ON T066_D004_Id = D004_Id WHERE T066_D004_Id = $D004_Local;";
        $res        = mysql_query($sql);
        if (mysql_numrows($res) > 0) {
            $result = mysql_fetch_array($res);
            if ($result['D004_Flag_Tipo'] == '2') {
                return true;
            } else {
                return "Você deve preencher o campo \"Tipo\" no cadastro da locação.";
            }
        }
        return true;
    }
    
    /**
     * updateCheckboxD069FlagTabelaPrincipal
     *
     * @param String $D069_Id
     * @param String $selected
     * @return true
     */     
    public function updateCheckboxD069FlagTabelaPrincipal($D069_Id, $selected)
    {
        global $g;
        $D069_Id  = mysql_real_escape_string($D069_Id);
        $selected = mysql_real_escape_string($selected);
/*        if ($selected=='S'){
            $D069=mysql_query("SELECT D009_Id, D009_D049_Id, D009_C004_Id, D069_D070_Id FROM D069 LEFT JOIN D009 ON D009_Id=D069_D009_Id WHERE D069_Id='$D069_Id'");
            $mD069=mysql_fetch_array($D069);
            if ($mD069['D009_Id']>0){
                mysql_query("UPDATE D009 SET D009_D069_Id='', D009_D070_Id='' WHERE D009_D049_Id='{$mD069['D009_D049_Id']}' AND D009_C004_Id='{$mD069['D009_C004_Id']}'");
                mysql_query("UPDATE D009 SET D009_D069_Id='$D069_Id',D009_D070_Id='{$mD069['D069_D070_Id']}' WHERE D009_Id='{$mD069['D009_Id']}'");
                mysql_query("UPDATE D069 SET D069_Flag_Tabela_Principal='' WHERE D069_D009_Id='{$mD069['D009_Id']}'");
            }
        } */
        mysql_query("UPDATE D069 SET D069_Flag_Tabela_Principal='$selected' WHERE D069_Id='$D069_Id'");
        return true;
    }

        /**
     * juntarProdutos
     *
     * @param Form $form
     * @return String/bool
     */ 
    // Antigo, criado uma função nova refaturada abaixo
    public function xxxjuntarProdutos($form)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $D001_Id_Antigo = (int) $form->campoValorEnviado('D001_Id_Antigo');
        $D001_Id_Novo   = (int) $form->campoValorEnviado('D001_Id_Novo');
        $multimarcas    = $g['C031']['multimarcas'];
        
        // Verificações
        if ($D001_Id_Antigo <= 0 || $D001_Id_Novo <= 0) {
            return 'Os dois códigos devem ser preenchidos.';
        }
        if ($D001_Id_Antigo == $D001_Id_Novo) {
            return 'Os códigos não podem ser o mesmo.';
        }
        
        // Encontra D001 antigo
        $sql           = "SELECT D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$D001_Id_Antigo}'";
        $resD001Antigo = mysql_query($sql);
        if (!$resD001Antigo) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }
        $rowD001Antigo = mysql_fetch_assoc($resD001Antigo);
        
        // Encontra D049 ANTIGO
        $sql           = "SELECT D049_Id, D049_D082_Id FROM D049 WHERE D049_D001_Id = '{$D001_Id_Antigo}'";
        $resD049Antigo = mysql_query($sql);
        if (!$resD049Antigo) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }
        if (mysql_num_rows($resD049Antigo) > 1) {
            return 'Erro - Empresa não utiliza multi-marcas, mais tem mais de uma marca para o produto Antigo!';
        }
        
        // Loop nos D049
        while ($rowD049Antigo = mysql_fetch_assoc($resD049Antigo)) {
            // O D082 do ANTIGO existe no NOVO?
            if ($multimarcas == 'N') {
                $sql         = "SELECT D049_Id FROM D049 WHERE D049_D001_Id = '{$D001_Id_Novo}'";
                $resD049Novo = mysql_query($sql);
                if (!$resD049Novo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 2';
                }
                if (mysql_num_rows($resD049Novo) > 1) {
                    return 'Erro - Empresa não utiliza multi-marcas, mais tem mais de uma marca para o produto Novo!';
                }
            } else {
                $sql         = "SELECT D049_Id FROM D049 WHERE D049_D082_Id = '{$rowD049Antigo['D049_D082_Id']}' AND D049_D001_Id = '{$D001_Id_Novo}'";
                $resD049Novo = mysql_query($sql);
                if (!$resD049Novo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 3';
                }
            }
            if (mysql_num_rows($resD049Novo) == 0) {
                log("D049 Antigo [{$rowD049Antigo['D049_Id']}] SEM D049 no Novo");
                // Não EXISTE = Transferir para o NOVO
                $sql = "UPDATE D049 SET D049_D001_Id='{$D001_Id_Novo}' WHERE D049_Id='{$rowD049Antigo['D049_Id']}'";
                $res = mysql_query($sql, false);
                if (!$res) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 4';
                }
            } else {
                // EXISTE
                $rowD049Novo = mysql_fetch_assoc($resD049Novo);
                // Apaga o preço do antigo
                $sql         = "DELETE FROM D041 WHERE D041_D049_Id='{$rowD049Antigo['D049_Id']}'";
                $res         = mysql_query($sql, false);
                if (!$res) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 5';
                }
                
                // Lista todos os D009 do D049 ANTIGO
                $sql           = "SELECT D009_Id, D009_C004_Id, D009_Quantidade_Estoque FROM D009 WHERE D009_D049_Id = '{$rowD049Antigo['D049_Id']}'";
                $resD009Antigo = mysql_query($sql);
                if (!$resD009Antigo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 6';
                }
                while ($rowD009Antigo = mysql_fetch_assoc($resD009Antigo)) {
                    // Verifica se o D009 existe no D049 NOVO
                    $sql         = "SELECT D009_Id,
                                           T066_Id,
                                           D009_Quantidade_Estoque,
                                           D009_C004_Id,
                                           T066_D004_Id
                                      FROM D009 
                                 LEFT JOIN T066 ON T066_D009_Id=D009_Id 
                                     WHERE D009_D049_Id = '{$rowD049Novo['D049_Id']}' 
                                       AND D009_C004_Id = '{$rowD009Antigo['D009_C004_Id']}'";
                    $resD009Novo = mysql_query($sql);
                    if (!$resD009Novo) {
                        mysql_query('ROLLBACK');
                        return 'Erro interno 7';
                    }
                    if (mysql_num_rows($resD049Novo) == 0) {
                        return 'Erro interno - D009 novo não encontrado.';
                    } else {
                        $rowD009Novo = mysql_fetch_assoc($resD009Novo);
                        // Elimina estoque antigo
                        //$sql         = "DELETE FROM T066 WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $sql = "SELECT T066_Id, T066_D004_Id FROM T066 WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $resT066Antigo         = mysql_query($sql);
                        if (!$res) {
                            mysql_query('ROLLBACK');
                            return 'Erro consulta T066 antigo';
                        }
                        while ($rowT066Antigo = mysql_fetch_assoc($resT066Antigo)){
                            $sql = "SELECT T066_Id FROM T066 WHERE T066_D009_Id='{$rowD009Novo['D009_Id']}' AND T066_D004_Id='{$rowT066Antigo['T066_D004_Id']}'";
                            $resT066Novo           = mysql_query($sql);
                            if (!$res) {
                                mysql_query('ROLLBACK');
                                return 'Erro consulta T066 novo';
                            }
                            $sql = "SELECT T066_Id FROM T066 WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}' AND T066_D004_Id='{$rowT066Antigo['T066_D004_Id']}'";
                            $resT066AntigoLocacaoDuplicada = mysql_query($sql);
                            // Caso a locacao não exista no novo produto, ou caso exista varias locacoes iguais no produto antigo ou novo, apenas levar a locacao para o novo produto.
                            if (mysql_num_rows($resT066Novo)<=0 || mysql_num_rows($resT066Novo)>1 || mysql_num_rows($resT066AntigoLocacaoDuplicada)>1){
                                mysql_query("update T066 set T066_D009_Id='{$rowD009Novo['D009_Id']}' where T066_Id='{$rowT066Antigo['T066_Id']}'", false);
                            } else {
                                log("T066 Antigo [{$rowT066Antigo['T066_Id']}] T066 Novo [{$rowT066Novo['T066_Id']}]");
                                $rowT066Novo=mysql_fetch_assoc($resT066Novo);
                                $retorno = gJuntarCodigos('T066', $rowT066Antigo['T066_Id'], $rowT066Novo['T066_Id'], false, array('T066A'), false);
                                if ($retorno !== true) {
                                    mysql_query('ROLLBACK');
                                    return 'Erro juntar T066: ' . $retorno;
                                }
                            }
                        }
                        // Elimina D009 antigo
                        /*$sql = "DELETE FROM D009 WHERE D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $res = mysql_query($sql);
                        if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 9'; }*/
                        // D009_Id Antigo -> D009_Id Novo
                        $retorno = gJuntarCodigos('D009', $rowD009Antigo['D009_Id'], $rowD009Novo['D009_Id'], false, array('D009A'), false);
                        if ($retorno !== true) {
                            mysql_query('ROLLBACK');
                            return 'Erro juntar D009: ' . $retorno;
                        }
                        // Histórico
                        $Quantidade                         = $rowD009Novo['D009_Quantidade_Estoque'] + $rowD009Antigo['D009_Quantidade_Estoque'];
                        // Este campo não existe na consulta D009
                        $rowD009Novo['D001_Codigo_Produto'] = "";
                        $sql                                = <<<EOT
                                                                INSERT INTO T001 (
                                                                    T001_D024_Id,
                                                                    T001_D009_Id,
                                                                    T001_T066_Id,
                                                                    T001_C004_Id,
                                                                    T001_Codigo_Produto,
                                                                    T001_Data_Lancamento,
                                                                    T001_Descricao_Operacao,
                                                                    T001_Quantidade,
                                                                    T001_Flag_Complemento,
                                                                    T001_Flag_Operacao,
                                                                    T001_Valor_Preco_Unitario,
                                                                    T001_Observacao,
                                                                    T001_Valor_Custo_Unitario,
                                                                    T001_Flag_Atualiza_Custo,
                                                                    T001_Quantidade_Saldo,
                                                                    T001_D004_Id
                                                                ) VALUES (
                                                                    '{$rowD009Novo['D049_D024_Id']}', 
                                                                    '{$rowD009Novo['D009_Id']}', 
                                                                    '{$rowD009Novo['T066_Id']}', 
                                                                    '{$rowD009Novo['D009_C004_Id']}', 
                                                                    '{$rowD009Novo['D001_Codigo_Produto']}', 
                                                                    current_date(), 
                                                                    'MUD.COD:{$rowD001Antigo['D001_Codigo_Produto']}',
                                                                    '{$Quantidade}',
                                                                    'M',
                                                                    'B',
                                                                    '0',
                                                                    '',
                                                                    '0',
                                                                    'N',
                                                                    '0',
                                                                    '{$rowD009Novo['T066_D004_Id']}'
                                                                );
EOT;
                        $res                                = mysql_query($sql, false);
                        if (!$res) {
                            mysql_query('ROLLBACK');
                            return 'Erro interno 10';
                        }
                    }
                }
                // D049_Antigo -> D049_Novo
                $retorno = gJuntarCodigos('D049', $rowD049Antigo['D049_Id'], $rowD049Novo['D049_Id'], false, array('D049A'), false);
                if ($retorno !== true) {
                    mysql_query('ROLLBACK');
                    return 'Erro juntar D049: ' . $retorno;
                }
                // Elimina o D049 Antigo
                /*$sql = "DELETE FROM D049 WHERE D049_Id = '{$rowD049Antigo['D049_Id']}'";
                $res = mysql_query($sql);
                if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 11'; }*/
            }
        }
        // D001_Id Antigo -> D001_Id Novo
        $retorno = gJuntarCodigos('D001', $D001_Id_Antigo, $D001_Id_Novo, false, array('T241','D001A'), false);
        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro juntar D001: ' . $retorno;
        }else{
            mysql_query("UPDATE D001 SET D001_Id_Antigo={$D001_Id_Antigo} where D001_Id={$D001_Id_Novo}");
        }
        // Elimina o D049 Antigo
        /*$sql = "DELETE FROM D001 WHERE D001_Id = '{$D001_Id_Antigo}'";
        $res = mysql_query($sql);
        if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 12'; }*/
        
        $retorno = $this->D001_reprocessa_historico($D001_Id_Novo, false);
        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro ao reprocessar o histórico, Por favor, contate o suporte técnico. <br />Erro: ' . $retorno;
        }

        // Fim
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        

        return true;
    }

    /**
     * juntarProdutos
     *
     * @param Form $form
     * @return String/bool
     */ 
    public function juntarProdutos($form)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $D001_Id_Antigo = (int) $form->campoValorEnviado('D001_Id_Antigo');
        $D001_Id_Novo   = (int) $form->campoValorEnviado('D001_Id_Novo');
        $multimarcas    = $g['C031']['multimarcas'];
        
        // Verificações
        if ($D001_Id_Antigo <= 0 || $D001_Id_Novo <= 0) {
            return 'Os dois códigos devem ser preenchidos.';
        }

        if ($D001_Id_Antigo == $D001_Id_Novo) {
            return 'Os códigos não podem ser o mesmo.';
        }
        
        // Encontra D001 antigo
        $sql = "SELECT D001_Codigo_Produto 
                  FROM D001 
                 WHERE D001_Id = '{$D001_Id_Antigo}'";

        $resD001Antigo = mysql_query($sql);
        if (!$resD001Antigo) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }

        $rowD001Antigo = mysql_fetch_assoc($resD001Antigo);

        // Encontra D001 novo
        $sql = "SELECT D001_Codigo_Produto 
                  FROM D001 
                 WHERE D001_Id = '{$D001_Id_Novo}'";

        $resD001Novo = mysql_query($sql);
        if (!$resD001Novo) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }
        $rowD001Novo = mysql_fetch_assoc($resD001Novo);

        /*
         * T004 e T006 guardam uma cópia do código do produto. A junção já
         * atualiza as chaves D009/D049/D001, mas não esses campos de exibição.
         * Guarda os itens ainda em aberto antes de mover os vínculos, para que
         * somente eles recebam o código do produto novo ao final da transação.
         */
        $T004_Ids_Atualizar_Codigo = array();
        $sql = "SELECT T004.T004_Id
                  FROM T004
            INNER JOIN T003 ON T003.T003_Id = T004.T004_T003_Id
            INNER JOIN D009 ON D009.D009_Id = T004.T004_D009_Id
            INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
                 WHERE D049.D049_D001_Id = '{$D001_Id_Antigo}'
                   AND T003.T003_Flag_Status_Orcamento IN ('P', 'V')";
        $resT004Aberto = mysql_query($sql);
        if (!$resT004Aberto) {
            mysql_query('ROLLBACK');
            return 'Erro ao listar orçamentos em aberto do produto antigo.';
        }
        while ($rowT004Aberto = mysql_fetch_assoc($resT004Aberto)) {
            $T004_Ids_Atualizar_Codigo[] = (int) $rowT004Aberto['T004_Id'];
        }

        $T006_Ids_Atualizar_Codigo = array();
        $sql = "SELECT T006.T006_Id
                  FROM T006
            INNER JOIN D009 ON D009.D009_Id = T006.T006_D009_Id
            INNER JOIN D049 ON D049.D049_Id = D009.D009_D049_Id
             LEFT JOIN T008 ON T008.T008_T006_Id = T006.T006_Id
                 WHERE D049.D049_D001_Id = '{$D001_Id_Antigo}'
                   AND T008.T008_Id IS NULL";
        $resT006Aberto = mysql_query($sql);
        if (!$resT006Aberto) {
            mysql_query('ROLLBACK');
            return 'Erro ao listar pedidos em aberto do produto antigo.';
        }
        while ($rowT006Aberto = mysql_fetch_assoc($resT006Aberto)) {
            $T006_Ids_Atualizar_Codigo[] = (int) $rowT006Aberto['T006_Id'];
        }
        
        // Encontra D049 ANTIGO
        $sql = "SELECT D049_Id,
                       D049_D082_Id,
                       D049_D024_Id 
                  FROM D049 
                 WHERE D049_D001_Id = '{$D001_Id_Antigo}'";

        $resD049Antigo = mysql_query($sql);
        if (!$resD049Antigo) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }
        if (mysql_num_rows($resD049Antigo) > 1) {
            return 'Erro - Empresa não utiliza multi-marcas, mais tem mais de uma marca para o produto Antigo!';
        }
        
        // Loop nos D049
        while ($rowD049Antigo = mysql_fetch_assoc($resD049Antigo)) {
            // O D082 do ANTIGO existe no NOVO?
            if ($multimarcas == 'N') {
                $sql = "SELECT D049_Id, 
                               D049_D024_Id 
                          FROM D049 
                         WHERE D049_D001_Id = '{$D001_Id_Novo}'";

                $resD049Novo = mysql_query($sql);
                if (!$resD049Novo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 2';
                }

                if (mysql_num_rows($resD049Novo) > 1) {
                    return 'Erro - Empresa não utiliza multi-marcas, mais tem mais de uma marca para o produto Novo!';
                }

            } else {

                $sql = "SELECT D049_Id,
                               D049_D024_Id 
                          FROM D049 
                         WHERE D049_D082_Id = '{$rowD049Antigo['D049_D082_Id']}' 
                           AND D049_D001_Id = '{$D001_Id_Novo}'";
                $resD049Novo = mysql_query($sql);
                if (!$resD049Novo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 3';
                }
            }

            if (mysql_num_rows($resD049Novo) == 0) {
                log("D049 Antigo [{$rowD049Antigo['D049_Id']}] SEM D049 no Novo");
                // Não EXISTE = Transferir para o NOVO
                $sql = "UPDATE D049 SET D049_D001_Id='{$D001_Id_Novo}' WHERE D049_Id='{$rowD049Antigo['D049_Id']}'";
                $res = mysql_query($sql, false);
                if (!$res) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 4';
                }
            } else {
                // EXISTE
                $rowD049Novo = mysql_fetch_assoc($resD049Novo);
                // Apaga o preço do antigo
                $sql         = "DELETE FROM D041 WHERE D041_D049_Id='{$rowD049Antigo['D049_Id']}'";
                $res         = mysql_query($sql, false);
                if (!$res) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 5';
                }
	                
                // Lista todos os D009 do D049 ANTIGO
                $sql = "SELECT D009_Id, 
                               D009_C004_Id, 
                               D009_Quantidade_Estoque 
                          FROM D009 
                         WHERE D009_D049_Id = '{$rowD049Antigo['D049_Id']}'";

                $resD009Antigo = mysql_query($sql);
                if (!$resD009Antigo) {
                    mysql_query('ROLLBACK');
                    return 'Erro interno 6';
                }
                while ($rowD009Antigo = mysql_fetch_assoc($resD009Antigo)) {
                    // Verifica se o D009 existe no D049 NOVO
                    $sql         = "SELECT D009_Id,
                                           D009_Quantidade_Estoque,
                                           D009_C004_Id
                                      FROM D009
                                     WHERE D009_D049_Id = '{$rowD049Novo['D049_Id']}' 
                                       AND D009_C004_Id = '{$rowD009Antigo['D009_C004_Id']}'";
                    $resD009Novo = mysql_query($sql);
                    if (!$resD009Novo) {
                        mysql_query('ROLLBACK');
                        return 'Erro interno 7';
                    }
                    if (mysql_num_rows($resD009Novo) == 0) {
                        // Não existe no novo: apenas transferir o D009 (e suas locações T066) para o D049 novo
                        $sql = "UPDATE D009 SET D009_D049_Id='{$rowD049Novo['D049_Id']}' WHERE D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $res = mysql_query($sql, false);
                        if (!$res) {
                            mysql_query('ROLLBACK');
                            return 'Erro interno - falha ao transferir D009.';
                        }
                        continue;
                    } else {
                        $rowD009Novo = mysql_fetch_assoc($resD009Novo);
                        $locacoesAfetadas = array();
                        // Elimina estoque antigo
                        //$sql         = "DELETE FROM T066 WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $sql          = "SELECT T066_Id, 
                                                T066_D004_Id, 
                                                T066_Quantidade_Estoque 
                                           FROM T066 
                                          WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}'";

                        $resT066Antigo = mysql_query($sql);
                        if (!$resT066Antigo) {
                            mysql_query('ROLLBACK');
                            return 'Erro consulta T066 antigo';
                        }

                        while ($rowT066Antigo = mysql_fetch_assoc($resT066Antigo)){
                            
                            $sql = "SELECT T066_Id, 
                                           T066_Quantidade_Estoque 
                                      FROM T066 
                                     WHERE T066_D009_Id='{$rowD009Novo['D009_Id']}' 
                                       AND T066_D004_Id='{$rowT066Antigo['T066_D004_Id']}'";

                            $resT066Novo = mysql_query($sql);
                            if (!$resT066Novo) {
                                mysql_query('ROLLBACK');
                                return 'Erro consulta T066 novo';
                            }

                            $sql = "SELECT T066_Id 
                                      FROM T066 
                                     WHERE T066_D009_Id='{$rowD009Antigo['D009_Id']}' 
                                       AND T066_D004_Id='{$rowT066Antigo['T066_D004_Id']}'";

                            $resT066AntigoLocacaoDuplicada = mysql_query($sql);
                            if (!$resT066AntigoLocacaoDuplicada) {
                                mysql_query('ROLLBACK');
                                return 'Erro consulta duplicidade T066 antigo';
                            }

                            // Caso a locacao não exista no novo produto, ou caso exista varias locacoes iguais no produto antigo ou novo, apenas levar a locacao para o novo produto.
                            if (mysql_num_rows($resT066Novo)<=0 || mysql_num_rows($resT066Novo)>1 || mysql_num_rows($resT066AntigoLocacaoDuplicada)>1){
                                $res = mysql_query("UPDATE T066 SET T066_D009_Id='{$rowD009Novo['D009_Id']}' WHERE T066_Id='{$rowT066Antigo['T066_Id']}'", false);
                                if (!$res) {
                                    mysql_query('ROLLBACK');
                                    return 'Erro ao transferir T066 para o novo produto';
                                }
                                // Locação foi afetada (mudou de D009)
                                $locacoesAfetadas[(int) $rowT066Antigo['T066_Id']] = true;
                            } else {
                                $rowT066Novo = mysql_fetch_assoc($resT066Novo);
                                log("T066 Antigo [{$rowT066Antigo['T066_Id']}] T066 Novo [{$rowT066Novo['T066_Id']}]");
                                $QuantidadeLocacao = $rowT066Novo['T066_Quantidade_Estoque'] + $rowT066Antigo['T066_Quantidade_Estoque'];
                                $retorno = gJuntarCodigos('T066', $rowT066Antigo['T066_Id'], $rowT066Novo['T066_Id'], false, array('T066A'), false);
                                if ($retorno !== true) {
                                    mysql_query('ROLLBACK');
                                    return 'Erro juntar T066: ' . $retorno;
                                }
                                // Mesma locação (D004): somar o estoque na T066 do produto novo
                                $res = mysql_query("UPDATE T066 SET T066_Quantidade_Estoque='{$QuantidadeLocacao}' WHERE T066_Id='{$rowT066Novo['T066_Id']}'", false);
                                if (!$res) {
                                    mysql_query('ROLLBACK');
                                    return 'Erro ao somar estoque da locação (T066)';
                                }
                                // Locação foi afetada (houve soma de estoque)
                                $locacoesAfetadas[(int) $rowT066Novo['T066_Id']] = true;
                            }
                        }

                        // Elimina D009 antigo
                        /*$sql = "DELETE FROM D009 WHERE D009_Id='{$rowD009Antigo['D009_Id']}'";
                        $res = mysql_query($sql);
                        if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 9'; }*/
                        // D009_Id Antigo -> D009_Id Novo
                        $retorno = gJuntarCodigos('D009', $rowD009Antigo['D009_Id'], $rowD009Novo['D009_Id'], false, array('D009A'), false);
                        if ($retorno !== true) {
                            mysql_query('ROLLBACK');
                            return 'Erro juntar D009: ' . $retorno;
                        }

                        // Histórico (balanço por locação): o estoque é formado pelas locações (T066)
                        $sql = "SELECT T066_Id, 
                                       T066_D004_Id, 
                                       T066_Quantidade_Estoque 
                                  FROM T066 
                                 WHERE T066_D009_Id='{$rowD009Novo['D009_Id']}'";

                        $resT066NovoAll = mysql_query($sql);
                        if (!$resT066NovoAll) {
                            mysql_query('ROLLBACK');
                            return 'Erro ao listar T066 do produto novo';
                        }

                        while ($rowT066NovoAll = mysql_fetch_assoc($resT066NovoAll)) {
                            
                            $Quantidade = $rowT066NovoAll['T066_Quantidade_Estoque'];
                            // B somente para locações afetadas; para as demais, apenas marca o evento sem reancorar saldo
                            $FlagOperacao = isset($locacoesAfetadas[(int) $rowT066NovoAll['T066_Id']]) ? 'B' : 'N';
                            $sql        = <<<EOT
                                            INSERT INTO T001 (
                                                T001_D024_Id,
                                                T001_D009_Id,
                                                T001_T066_Id,
                                                T001_C004_Id,
                                                T001_Codigo_Produto,
                                                T001_Data_Lancamento,
                                                T001_Descricao_Operacao,
                                                T001_Quantidade,
                                                T001_Flag_Complemento,
                                                T001_Flag_Operacao,
                                                T001_Valor_Preco_Unitario,
                                                T001_Observacao,
                                                T001_Valor_Custo_Unitario,
                                                T001_Flag_Atualiza_Custo,
                                                T001_Quantidade_Saldo,
                                                T001_D004_Id
                                            ) VALUES (
                                                '{$rowD049Novo['D049_D024_Id']}', 
                                                '{$rowD009Novo['D009_Id']}', 
                                                '{$rowT066NovoAll['T066_Id']}', 
                                                '{$rowD009Novo['D009_C004_Id']}', 
                                                '{$rowD001Novo['D001_Codigo_Produto']}', 
                                                current_date(), 
                                                'MUD.COD:{$rowD001Antigo['D001_Codigo_Produto']}',
                                                '{$Quantidade}',
                                                'M',
                                                '{$FlagOperacao}',
                                                '0',
                                                '',
                                                '0',
                                                'N',
                                                '0',
                                                '{$rowT066NovoAll['T066_D004_Id']}'
                                            );
EOT;
                            $res = mysql_query($sql, false);
                            if (!$res) {
                                mysql_query('ROLLBACK');
                                return 'Erro interno 10';
                            }
                        }
                    }
                }

                // D049_Antigo -> D049_Novo
                $retorno = gJuntarCodigos('D049', $rowD049Antigo['D049_Id'], $rowD049Novo['D049_Id'], false, array('D049A'), false);
                if ($retorno !== true) {
                    mysql_query('ROLLBACK');
                    return 'Erro juntar D049: ' . $retorno;
                }
                // Elimina o D049 Antigo
                /*$sql = "DELETE FROM D049 WHERE D049_Id = '{$rowD049Antigo['D049_Id']}'";
                $res = mysql_query($sql);
                if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 11'; }*/
            }
        }
        
        // D001_Id Antigo -> D001_Id Novo
        $retorno = gJuntarCodigos('D001', $D001_Id_Antigo, $D001_Id_Novo, false, array('T241','D001A'), false);
        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro juntar D001: ' . $retorno;
        }else{
            mysql_query("UPDATE D001 SET D001_Id_Antigo={$D001_Id_Antigo} where D001_Id={$D001_Id_Novo}");
        }
        // Elimina o D049 Antigo
        /*$sql = "DELETE FROM D001 WHERE D001_Id = '{$D001_Id_Antigo}'";
        $res = mysql_query($sql);
        if (!$res) { mysql_query('ROLLBACK'); return 'Erro interno 12'; }*/
        
        $retorno = $this->D001_reprocessa_historico($D001_Id_Novo, false);
        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro ao reprocessar o histórico, Por favor, contate o suporte técnico. <br />Erro: ' . $retorno;
        }

        // Atualiza o código gravado nos itens que estavam abertos antes da junção.
        $D001_Codigo_Produto_Novo = mysql_real_escape_string($rowD001Novo['D001_Codigo_Produto']);
        if (!empty($T004_Ids_Atualizar_Codigo)) {
            $sql = "UPDATE T004
                       SET T004_Codigo_Produto = '{$D001_Codigo_Produto_Novo}'
                     WHERE T004_Id IN (" . implode(',', $T004_Ids_Atualizar_Codigo) . ")";
            $res = mysql_query($sql, false);
            if (!$res) {
                mysql_query('ROLLBACK');
                return 'Erro ao atualizar o código dos orçamentos em aberto.';
            }
        }

        if (!empty($T006_Ids_Atualizar_Codigo)) {
            $sql = "UPDATE T006
                       SET T006_Codigo_Produto = '{$D001_Codigo_Produto_Novo}'
                     WHERE T006_Id IN (" . implode(',', $T006_Ids_Atualizar_Codigo) . ")";
            $res = mysql_query($sql, false);
            if (!$res) {
                mysql_query('ROLLBACK');
                return 'Erro ao atualizar o código dos pedidos em aberto.';
            }
        }

        // Fim
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        

        return true;
    }
    
    /**
     * trocarCodigos
     *
     * @param Form $form
     * @return String/bool
     */ 
    public function trocarCodigos($form)
    {
        global $g;
        $D001_Codigo_Antigo = $form->campoValorEnviado('D001_Codigo_Antigo');
        $D001_Codigo_Novo   = $form->campoValorEnviado('D001_Codigo_Novo');
        
        if (empty($D001_Codigo_Antigo)) {
            return "Informe o código antigo";
        }
        if (empty($D001_Codigo_Novo)) {
            return "Informe o código novo";
        }
        
        $D001_Codigo_Novo   = strtoupper(trim($D001_Codigo_Novo));
        $D001_Codigo_Antigo = strtoupper(trim($D001_Codigo_Antigo));
        
        list($Codigo_Antigo) = explode("-", $D001_Codigo_Antigo);
        list($Codigo_Novo) = explode("-", $D001_Codigo_Novo);
        
        if ($Codigo_Antigo == $Codigo_Novo) {
            return "Os dois códigos são iguais! Verifique!!!";
        }
        //Estava assim no sistema antigo
        //$D001_Antigo=mysql_query("SELECT D001_Id,D001_Codigo_Produto,D001_Descricao_Produto FROM D001 WHERE D001_Codigo_Produto like '$Codigo_Antigo-%'");
        $D001_Antigo = mysql_query("SELECT D001_Id,D001_Codigo_Produto,D001_Descricao_Produto FROM D001 WHERE D001_Codigo_Produto='{$Codigo_Antigo}'");
        if (mysql_num_rows($D001_Antigo) == 0) {
            return "Código antigo não existe. Processo cancelado.";
        }
        
        /*
        $mCA=explode(".",$D001_Codigo_Antigo);
        $mCN=explode(".",$D001_Codigo_Novo);
        
        if (count($mCA)!=count($mCN))
        {
        return "O Número de Partes dos códigos não é o mesmo. Processo Cancelado!";
        }
        
        if (count($mCA)<4)
        {
        return "O Código deve conter 4 Partes. Processo Cancelado!";
        }
        */
        
        list($D003_Padrao_Antigo, $D060_Padrao_Antigo, $D002_Padrao_Antigo, $D015_Padrao_Antigo) = explode(".", $D001_Codigo_Antigo);
        $C008_Padrao_Antigo = substr($D003_Padrao_Antigo, 0, 1);
        $D003_Padrao_Antigo = substr($D003_Padrao_Antigo, 1, 3);
        
        list($D003_Padrao_Novo, $D060_Padrao_Novo, $D002_Padrao_Novo, $D015_Padrao_Novo) = explode(".", $D001_Codigo_Novo);
        $C008_Padrao_Novo = substr($D003_Padrao_Novo, 0, 1);
        $D003_Padrao_Novo = substr($D003_Padrao_Novo, 1, 3);
        
        $Descricao_Antiga = '';
        $Descricao_Nova   = '';
        
        // ALTERA O TIPO DO PRODUTO
        if ($C008_Padrao_Antigo != $C008_Padrao_Novo) {
            $cClausula = " SELECT C008_Id FROM C008 WHERE C008_Codigo_Padrao='{$C008_Padrao_Novo}' ";
            $cSql      = mysql_query($cClausula);
            if (mysql_num_rows($cSql) == 0) {
                return "No Código Novo: {$Codigo_Novo}, não localizei no banco de dados o tipo: {$C008_Padrao_Novo}";
            }
            $mSql         = mysql_fetch_array($cSql);
            $mAlteracao[] = " D001_C008_Id='{$mSql['C008_Id']}' ";
        }
        // ALTERA A LINHA DO PRODUTO
        $cClausula = " SELECT D003_Id,D003_Descricao_Padrao FROM D003 WHERE D003_Codigo_Padrao='{$D003_Padrao_Antigo}' AND D003_C008_Id='{$C008_Padrao_Novo}'";
        return $cClausula;
        $cSql = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "1 - No Código Antigo: {$Codigo_Novo}, não localizei no banco de dados a linha: {$D003_Padrao_Antigo}";
        }
        $mSql_Antigo = mysql_fetch_array($cSql);
        $cClausula   = " SELECT D003_Id,D003_Descricao_Padrao FROM D003 WHERE D003_Codigo_Padrao='{$D003_Padrao_Novo}'  AND D003_C008_Id='{$C008_Padrao_Novo}'";
        $cSql        = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "2 - No Código Novo: {$Codigo_Novo}, não localizei no banco de dados a linha: {$D003_Padrao_Novo}";
        }
        $mSql_Novo = mysql_fetch_array($cSql);
        if (!empty($mSql_Antigo[1])) {
            $Descricao_Antiga .= trim($mSql_Antigo[1]);
            $Descricao_Nova .= trim($mSql_Novo[1]);
        }
        if ($D003_Padrao_Antigo != $D003_Padrao_Novo) {
            $mAlteracao[] = " D001_D003_Id='{$mSql_Novo['D003_Id']}' ";
        }
        // ALTERA O GRUPO DO PRODUTO
        $cClausula = " SELECT D060_Id,D060_Descricao_Padrao FROM D060 WHERE D060_Codigo_Padrao='{$D060_Padrao_Antigo}' AND D060_C008_Id='{$C008_Padrao_Novo}'";
        $cSql      = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "No Código Antigo: {$Codigo_Antigo}, não localizei no banco de dados o Grupo: {$D060_Padrao_Antigo}";
        }
        $mSql_Antigo = mysql_fetch_array($cSql);
        $cClausula   = " SELECT D060_Id,D060_Descricao_Padrao FROM D060 WHERE D060_Codigo_Padrao='{$D060_Padrao_Novo}' AND D060_C008_Id='{$C008_Padrao_Novo}'";
        $cSql        = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "No Código Novo: {$Codigo_Novo}, não localizei no banco de dados o Grupo: {$D060_Padrao_Novo}";
        }
        $mSql_Novo        = mysql_fetch_array($cSql);
        $Descricao_Antiga = trim($Descricao_Antiga) . ' ' . trim($mSql_Antigo[1]);
        $Descricao_Nova   = trim($Descricao_Nova) . ' ' . trim($mSql_Novo[1]);
        if ($D060_Padrao_Antigo != $D060_Padrao_Novo) {
            $mAlteracao[] = " D001_D060_Id='{$mSql_Novo['D060_Id']}'";
        }
        // ALTERA O SUB-GRUPO DO PRODUTO
        $cClausula = " SELECT D002_Id,D002_Descricao_Padrao FROM D002 WHERE D002_Codigo_Padrao='{$D002_Padrao_Antigo}' AND D002_C008_Id='{$C008_Padrao_Novo}'";
        $cSql      = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "No Código Novo: {$Codigo_Antigo}, não localizei no banco de dados o Sub-Grupo: {$D002_Padrao_Antigo}";
        }
        $mSql_Antigo = mysql_fetch_array($cSql);
        $cClausula   = " SELECT D002_Id,D002_Descricao_Padrao FROM D002 WHERE D002_Codigo_Padrao='{$D002_Padrao_Novo}' AND D002_C008_Id='{$C008_Padrao_Novo}'";
        $cSql        = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "No Código Novo: {$Codigo_Novo}, não localizei no banco de dados o Sub-Grupo: {$D002_Padrao_Novo}";
        }
        $mSql_Novo        = mysql_fetch_array($cSql);
        $Descricao_Antiga = trim($Descricao_Antiga) . ' ' . trim($mSql_Antigo[1]);
        $Descricao_Nova   = trim($Descricao_Nova) . ' ' . trim($mSql_Novo[1]);
        if ($D002_Padrao_Antigo != $D002_Padrao_Novo) {
            $mAlteracao[] = " D001_D002_Id='{$mSql_Novo['D002_Id']}' ";
        }
        // ALTERAR A APLICAÇÃO DO PRODUTO
        $cClausula = " SELECT D015_Id,D015_Descricao_Padrao FROM D015 WHERE D015_Codigo_Padrao='{$D015_Padrao_Antigo}' AND D015_C008_Id='{$C008_Padrao_Novo}'";
        $cSql      = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "Codigo Antigo para a Aplicação não existe: {$D015_Padrao_Antigo}";
        }
        $mSql_Antigo = mysql_fetch_array($cSql);
        $cClausula   = " SELECT D015_Id,D015_Descricao_Padrao FROM D015 WHERE D015_Codigo_Padrao='{$D015_Padrao_Novo}' AND D015_C008_Id='{$C008_Padrao_Novo}'";
        $cSql        = mysql_query($cClausula);
        if (mysql_num_rows($cSql) == 0) {
            return "Codigo novo para a Aplicação não existe: {$D015_Padrao_Novo}";
        }
        $mSql_Novo        = mysql_fetch_array($cSql);
        $Descricao_Antiga = trim($Descricao_Antiga) . ' ' . trim($mSql_Antigo[1]);
        $Descricao_Nova   = trim($Descricao_Nova) . ' ' . trim($mSql_Novo[1]);
        if ($D015_Padrao_Antigo != $D015_Padrao_Novo) {
            $mAlteracao[] = " D001_D015_Id='{$mSql_Novo['D015_Id']}' ";
        }
        
        $Descricao_Antiga = trim($Descricao_Antiga);
        $Descricao_Nova   = trim($Descricao_Nova);
        
        //$mAlteracao[]=" D001_Codigo_Aplicacao=D001_Codigo_Produto ";
        
        if (count($mAlteracao) > 0) {
            $cAlteracoes = implode(",", $mAlteracao);
        }
        
        if (!empty($cAlteracoes)) {
            $cAlteracoes .= ",";
        }
        
        mysql_query("SET autocommit=0");
        mysql_query("start transaction");
        
        while ($mD001_Antigo = mysql_fetch_array($D001_Antigo)) {
            list($x, $Medidas) = explode("-", $mD001_Antigo['D001_Codigo_Produto']);
            
            $CodigoInteiro = "$Codigo_Novo-$Medidas";
            
            $Descricao = Str_replace($Descricao_Antiga, $Descricao_Nova, str_replace('  ', ' ', $mD001_Antigo['D001_Descricao_Produto']));
            
            if ((trim($Descricao) == $mD001_Antigo['D001_Descricao_Produto'] AND $Descricao_Nova != $Descricao_Antiga) OR trim($Codigo_Inteiro) == $mD001_Antigo['D001_Codigo_Produto']) {
                mysql_query("ROLLBACK");
                return "Problemas na alteração do produto {$mD001_Antigo['D001_Codigo_Produto']}. Processo cancelado <br />
                            Descricao antiga: {$Descricao_Antiga} <br />
                            Descricao nova: {$Descricao_Nova} <br />
                            Descricao final: {$Descricao}";
            }
            
            $D001_Novo  = mysql_query("SELECT D001_Id FROM D001 WHERE D001_Codigo_Produto = '{$CodigoInteiro}'");
            $mD001_Novo = mysql_fetch_array($D001_Novo);
            if (mysql_num_rows($D001_Novo) > 0) {
            }
            
            $cClausula         = "UPDATE D001 SET {$cAlteracoes} D001_Descricao_Produto='{$Descricao}',D001_Codigo_Produto='{$CodigoInteiro}' WHERE D001_Id = '{$mD001_Antigo['D001_Id']}'";
            $cLogJuntarCodigos = $cClausula;
            mysql_query($cClausula);
            $cError = mysql_error();
            if (!empty($cError)) {
                mysql_query("ROLLBACK");
                return "Erro:" . str_replace(array(
                    "'",
                    '"'
                ), "", $cError);
            }
            
            $cLogJuntarCodigos = addslashes($cLogJuntarCodigos);
            
            $cClausula = <<<EOT
                INSERT INTO D017 ( 
                    D017_D001_Id, 
                    D017_C007_Id, 
                    D017_C008_Id, 
                    D017_Codigo_Novo, 
                    D017_Codigo_Antigo, 
                    D017_Descricao, 
                    D017_Data_Inclusao, 
                    D017_Flag_Mudanca, 
                    D017_Log_Juntar_Codigo 
                ) VALUES ( 
                    '{$mD001_Antigo['D001_Id']}', 
                    '{$Usuario_Atual}', 
                    '1', 
                    '{$CodigoInteiro}', 
                    '{$mD001_Antigo['D001_Codigo_Produto']}', 
                    '{$mD001_Antigo['D001_Descricao_Produto']}', 
                    current_date(), 
                    '0', 
                    '{$cLogJuntarCodigos}' 
                ) 
EOT;
            $mDebug[] = $cClausula;
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("rollback");
                return "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mDebug) . "Erro: $cErro<br>$cClausula<hr>Sql Executados e Abortados:<br> " . implode("<br>", $mDebug);
            }
        }
        //mysql_query("ROLLBACK");
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * CAD002montarPdfGridProdutos
     *
     * @param String $sqlD001
     * @return array
     */ 
    public function CAD002montarPdfGridProdutos($sqlD001)
    {
        global $g, $confUsuario;
        
        $resultD001 = mysql_query($sqlD001);
        
        
        $sqlC004    = "SELECT C004_Nome_Empresa FROM C004 WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $C004       = mysql_fetch_array($resultC004);
        
        $sqlC007    = "SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'";
        $resultC007 = mysql_query($sqlC007);
        $C007       = mysql_fetch_array($resultC007);
        
        //$layout = $g['conf']->ini('layoutOrcamentoEmailPdf');
        
        // Array de retorno para criação do e-mail
        $array            = array();
        $array['Para']    = "";
        $array['Assunto'] = "Produtos";
        
        /**
         * PDF
         */
        require_once('bibliotecas/tcpdf/config/lang/eng.php');
        require_once('bibliotecas/tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // SET document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Hardness Sistemas');
        // remove default header/footer
        $pdf->setPrintHeader(false);
        //$pdf->setPrintFooter(true);
        $pdf->SetMargins(8, 8, 8, 8);
        // SET font
        $pdf->SetFont('helvetica', '', 7);
        //SET auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //SET image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Orientação da página
        $pdf->setPageOrientation('P');
        // add a page
        $pdf->AddPage();
        
        $html = '';
        /**
         * PDF - STYLE
         */
        $html .= <<<EOT
                    <style>
                        .header .text {
                            font-size: 25px;
                        }
                        .header .text2 {
                            font-size: 35px;
                            font-weight: bold;
                        }
                        .titulo {
                            background-color: #E1ECF1;
                        }
                        .titulo_superior {
                            font-size: 30px;
                        }
                        .itens td {
                            border: 1px solid black;
                        }
                        .textIncluso {
                            font-size: 18px;
                            color: red;
                            background-color: #E1ECF1;
                        }
                        
                    </style>
EOT;
        
        /**
         * PDF - HEADER
         */
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        $html .= <<<EOT
            <table style='height:10px;border:1px solid black;'>
                <tr>
                    <td align="left">{$g['c004']['C004_Nome_Empresa']}</td>
                    <td align="right" style="font-size: 230%;"><b>Produtos</b></td>
                </tr>
            </table>
EOT;
        
        /**
         * PDF - TABELA PRODUTOS
         */
        
        $html .= <<<EOT
                <div style="height:20px; width:100%;"></div>
                        <table class="itens" cellspacing="0" cellpadding="2">
                            <tr class="titulo">
                                <td style="width: 8%";>Código</td>
                                <td style="width: 60%"; align="left">Descrição</td>
                                <td style="width: 8%"; align="center">Marca</td>
                                <td style="width: 8%"; align="right">Estoque</td>
                                <td style="width: 16%"; align="right">Preço R$ </td>
                            </tr>
                        </table>
EOT;
        
        $html .= <<<EOT
            <table class="itens" cellspacing="0" cellpadding="2">
EOT;
        while ($rowD001 = mysql_fetch_array($resultD001)) {
            
            $rowD001['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
            $rowD001['D041_Valor_Base']                 = gCorrigeNumero($row['D041_Valor_Base']);
            $rowD001['D001_Descricao_Produto']          = ($rowD001['D001_Flag_Pre_Cadastro'] == "S" ? "{$rowD001['D001_Descricao_Produto']} (PRÉ)" : $rowD001['D001_Descricao_Produto']);
            
            $html .= <<<EOT
                
                    <tr>
                        <td style="width: 8%";>{$rowD001['D001_Codigo_Produto']}</td>
                        <td style="width: 60%;white-space:normal"; align="left">{$rowD001['D001_Descricao_Produto']}</td>
                        <td style="width: 8%"; align="center">{$rowD001['D082_Marca']}</td>
                        <td style="width: 8%; padding-right:20px;" align="right">{$rowD001['D009_Quantidade_Estoque_Liquido']}</td>
                        <td style="width: 16%"; align="right">{$rowD001['D041_Valor_Base']}</td>
                    </tr>
                    
EOT;
            
        }
        $html .= '</table>';
        
        /**
         * PDF - GERA!
         */
        $pdf->writeHTML($html, true, false, true, false, 'C');
        // reset pointer to the last page
        $pdf->lastPage();
        // get data
        $data = $pdf->getPDFData();
        
        // Gravar arquivo no disco
        $anexoDir = $g['pathDados'] . 'tmp/';
        is_dir($anexoDir) OR mkdir($anexoDir, 0777, true);
        $anexoNome = "{$anexoDir}produtosLista.pdf";
        @unlink($anexoNome);
        $h = fopen($anexoNome, 'w');
        fwrite($h, $data);
        fclose($h);
        // Adiciona como anexo
        $array['Anexos']    = array(
            $anexoNome
        );
        $array['AnexosWeb'] = array(
            '/hardness3/' . $anexoNome
        );
        // Logo         
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        // (TODO) passar para o arq. conf
        $caminhoImagem  = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
        $caminhoImagem  = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);
        // Variaveis enviadas para o modulo de e-mails (que serão gravadas na tabela T139)
        $array['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><br /><br /><br /><br /><font style='font-size:16px'>Lista de Produtos em anexo.</font><br /><br /><br />";
        
        return $array;
    }
    
    /**
     * cad002RetornaPrecoVenda
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $atualizarDadosCompra
     * @param String $cfop
     * @param String $Aliquota_ICMS
     * @param String $T219_Id
     * @param String $T225_Id     
     * @return array
     */     
    // NÃO ESTÁ SENDO UTILIZADA - UTILIZAR RETORNAPRECOVENDA
    public function cad002RetornaPrecoVenda($D009_Id, $listaPreco = null, $D024_Id = null, $atualizarDadosCompra = false, $cfop = null, $Aliquota_ICMS = 0, $T219_Id = 0, $T225_Id = 0, $casasDecimaisPreco = 4, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0)
    {
        log("cad002RetornaPrecoVenda: $D009_Id , $listaPreco , $D024_Id , $atualizarDadosCompra , $cfop , $Aliquota_ICMS, $T219_Id , $T225_Id , $casasDecimaisPreco , $Aliquota_PIS , $Aliquota_COFINS, $Aliquota_IPI");
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }
        
        $demostrativo = array();
        $custoMedio = 0;
        $quantidadeEstoque = 0;

        // Cliente/Fornecedor
        $sql   = <<<EOT
            SELECT
              *
            FROM D024
            WHERE D024_Id='{$D024_Id}'
EOT;
        $D024  = mysql_query($sql);
        $mD024 = mysql_fetch_array($D024);
        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024
        if ($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        // Produto
        $sql   = <<<EOT
            SELECT *,
            IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria 
            FROM D009 
            JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D006 ON D006_Id=T001_D006_Id
            LEFT JOIN D069 ON D069_Id=D009_D069_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE D009_Id='{$D009_Id}'
EOT;
        $D009  = mysql_query($sql);
        $mD009 = mysql_fetch_array($D009);

        if($mD009['D009_Origem_Mercadoria'] != ''){
            $mD009['D049_Origem_Mercadoria'] = $mD009['D009_Origem_Mercadoria'];
        }

        log("Custo Compra: {$mD009['D009_Valor_Custo_Compra']}");
        $custoCadastroProduto = $mD009['D009_Valor_Custo_Compra'];

        switch($listaPreco){
            case '1':
                $precoTabelado = $mD009['D009_Preco_1'];
            break;
            case '2':
                $precoTabelado = $mD009['D009_Preco_2'];
            break;
            case '3':
                $precoTabelado = $mD009['D009_Preco_3'];
            break;
            default:
                $precoTabelado = $mD009['D009_Preco_1'];
            break;
        }
        

        //Verifica se caso tenha cotação ou ordem de compra, já foi dado entrada da nota fiscal, para que o preço utilizado seja da Nota
        $possuiNFEntrada = false;
        log("T219: " . $T219_Id . " - T225_Id: " . $T225_Id);
        if ($T219_Id > 0 || $T225_Id > 0) {
            $zerarT225_Id = false;
            if(empty($T225_Id) || $T225_Id <= 0){
                $sqlT225 = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T219_Id = '{$T219_Id}'");
                if(mysql_num_rows($sqlT225) > 0){
                    $rowT225 = mysql_fetch_array($sqlT225);
                    $T225_Id = $rowT225['T225_Id'];
                    $zerarT225_Id = true;
                }
            }

            if($T225_Id > 0){
                $sqlT014 = mysql_query("SELECT T001_Id, T001_Valor_Custo_Unitario FROM T014 LEFT JOIN T001 ON T001_T014_Id = T014_Id WHERE T014_T225_Id = '{$T225_Id}' AND T001_Flag_Operacao != 'C'");
                if(mysql_num_rows($sqlT014) > 0){
                   $mSqlT014 = mysql_fetch_array($sqlT014);
                   $mD009['D009_Valor_Custo_Compra'] = $mSqlT014['T001_Valor_Custo_Unitario'];
                   $mD009['D009_Valor_Custo_Ultima_Compra'] = $mSqlT014['T001_Valor_Custo_Unitario'];
                   $possuiNFEntrada = true;
                }
            }

            //T225_id veio em branco, porém foi encontrado pela cotação, se não entrou nota ainda, zeramos o T225 para que o preço não seja atualizado pela ordem de compra
            if($zerarT225_Id == true){
                $T225_Id = 0;
            }
        }
        // Usar cotação compra
        if ($T219_Id > 0 && $possuiNFEntrada == false) {
            $sql   = <<<EOT
                SELECT 
                    T219_Valor_Custo_Unitario as D009_Valor_Custo_Compra,
                    999 as D009_Quantidade_Estoque,
                    T219_Valor_ST/T219_Quantidade as T001_Valor_ST,
                    (round(T219_Valor_ST/T219_Quantidade,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI/T219_Quantidade,2)))*100 as T001_Percentual_ST,
                    T219_Valor_ICMS/T219_Quantidade as T001_Valor_ICMS,
                    T219_Aliquota_ICMS as T001_Aliquota_ICMS,
                    T219_Valor_PIS/T219_Quantidade as T001_Valor_PIS,
                    T219_Aliquota_PIS as T001_Aliquota_PIS,
                    T219_Valor_COFINS/T219_Quantidade as T001_Valor_COFINS,
                    T219_Aliquota_COFINS as T001_Aliquota_COFINS,
                    T219_T218_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T219_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T219_Quantidade_Estoque,
                    T006_Valor_Custo_Unitario,
                    T219_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T004_T219_Id,0) AS T004_T219_Id,
                    T219_Valor_Custo_Medio_Unitario,
                    T219_Valor_Custo_Momento_Compra
                FROM T219
                LEFT JOIN T004 ON T004_Id=T219_T004_Id
                LEFT JOIN T006 ON T006_Id=T219_T006_Id
                LEFT JOIN D006 ON D006_Id=T219_D006_Id
                JOIN D009 ON D009_Id=T219_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T219_Id='{$T219_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Cotação OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T219_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }

                if(($mD009['T006_T219_Id'] > 0 || $mD009['T004_T219_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T219_Valor_Custo_Momento_Compra']*$mD009['T219_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T219_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T219_Quantidade']+$quantidadeEstoque);
            }
            
            $obsCusto = "(Cot {$mD009['T219_T218_Id']})";
        }
        
        // Usar ordem de compra
        if ($T225_Id > 0 && $possuiNFEntrada == false) {
            $sql   = <<<EOT
                SELECT 
                    T225_Valor_Custo_Unitario AS D009_Valor_Custo_Compra,
                    999 AS D009_Quantidade_Estoque,
                    T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade AS T001_Valor_ST,
                    (ROUND(T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade,2)/(ROUND(T225_Valor_Total_Preco,2)+ROUND(T225_Valor_IPI/T225_Quantidade,2)))*100 AS T001_Percentual_ST,
                    T225_Valor_ICMS/T225_Quantidade AS T001_Valor_ICMS,
                    T225_Aliquota_ICMS AS T001_Aliquota_ICMS,
                    T225_Valor_PIS/T225_Quantidade AS T001_Valor_PIS,
                    T225_Aliquota_PIS AS T001_Aliquota_PIS,
                    T225_Valor_COFINS/T225_Quantidade AS T001_Valor_COFINS,
                    T225_Aliquota_COFINS AS T001_Aliquota_COFINS,
                    T225_T224_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T225_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T225_Quantidade_Estoque,
                    T006_Valor_Custo_Unitario,
                    T225_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T006_T225_Id,0) AS T006_T225_Id,
                    T225_Valor_Custo_Medio_Unitario,
                    T225_Valor_Custo_Momento_Compra
                FROM T225
                LEFT JOIN T006 ON T006_T225_Id=T225_Id OR T006_T219_Id=T225_T219_Id
                LEFT JOIN D006 ON T225_D006_Id=D006_Id
                JOIN D009 ON D009_Id=T225_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T225_Id='{$T225_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Compra OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T225_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }

                if(($mD009['T006_T219_Id'] > 0 || $mD009['T006_T225_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T225_Valor_Custo_Momento_Compra']*$mD009['T225_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T225_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T225_Quantidade']+$quantidadeEstoque);
            }
            
            $obsCusto = "(OC {$mD009['T225_T224_Id']})";
        }
        //log("T219_Id = $T219_Id");
        
        $origemMercadoria = $mD009['D049_Origem_Mercadoria'];

        // CFOP
        $sql   = <<<EOT
            SELECT
              *
            FROM D006
            WHERE D006_Id='{$cfop}'
EOT;
        $D006  = mysql_query($sql);
        $mD006 = mysql_fetch_array($D006);
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S' && $quantidadeEstoque > 0 && $custoMedio > 0){
            $mD009['D009_Valor_Custo_Compra'] = $custoMedio;
        }
        $preco          = $mD009['D009_Valor_Custo_Compra'];
        //Caso a lista de preço seja "T", para uma transferencia, utilizamos o custo da ultima compra
        if($listaPreco == 'T'){
            $preco = $mD009['D009_Valor_Custo_Ultima_Compra'];
        }

        $demostrativo[] = array(
            'campo' => 'Custo Compra ' . $obsCusto,
            'valor' => $mD009['D009_Valor_Custo_Compra'],
            'operacao' => '='
        );
        
        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S' && $mD009['D006_Flag_Substituicao_Tributaria'] == 'S') {
            if ($T219_Id > 0 || $T225_Id > 0) {
                $custoST      = $mD009['T001_Valor_ST'];
                $percentualST = number_format($mD009['T001_Percentual_ST'], 5, ',', '.');
            } else {
                if ($mD009['D009_Flag_Custo_Utilizado'] == 'T') {
                    $custoST      = $mD009['D069_Valor_ICMS_Substituicao_Retencao'];
                    $percentualST = number_format($mD009['D069_Percentual_ST'], 5, ',', '.');
                } else {
                    $custoST      = $mD009['T001_Valor_ST'];
                    $percentualST = number_format($mD009['T001_Percentual_ST'], 5, ',', '.');
                }
            }
        }
        log('1 ST COMPRA: ' . $custoST . ' - ' . $percentualST);
        $preco -= $custoST;
        $demostrativo[] = array(
            'campo' => $percentualST . '% ST',
            'valor' => $custoST,
            'operacao' => '-'
        );
        


        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            if ($T219_Id > 0 || $T225_Id > 0) {
                $icmsCompra   = $mD009['T001_Valor_ICMS'];
                $aliquotaICMS = number_format($mD009['T001_Aliquota_ICMS'], 2, ',', '.');
            } else {
                if ($mD009['D009_Flag_Custo_Utilizado'] == 'T') {
                    $icmsCompra   = $mD009['D069_Valor_ICMS'];
                    $aliquotaICMS = number_format($mD009['D069_Aliquota_ICMS'], 2, ',', '.');
                } else {
                    $icmsCompra   = $mD009['T001_Valor_ICMS'];
                    $aliquotaICMS = number_format($mD009['T001_Aliquota_ICMS'], 2, ',', '.');
                }
            }
        }

        $preco -= $icmsCompra;
        log('2 ICMS COMPRA: ' . $icmsCompra . ' - ' . $aliquotaICMS);
        $demostrativo[] = array(
            'campo' => $aliquotaICMS . '% ICMS compra',
            'valor' => $icmsCompra,
            'operacao' => '-'
        );


        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S') {
            if ($T219_Id > 0 || $T225_Id > 0) {
                $pisCompra   = $mD009['T001_Valor_PIS'];
                $aliquotaPIS = number_format($mD009['T001_Aliquota_PIS'], 2, ',', '.');
            } else {
                if ($mD009['D009_Flag_Custo_Utilizado'] == 'T') {
                    $pisCompra   = $mD009['D069_Valor_PIS'];
                    $aliquotaPIS = number_format($mD009['D069_Aliquota_PIS'], 2, ',', '.');
                } else {
                    $pisCompra   = $mD009['T001_Valor_PIS'];
                    $aliquotaPIS = number_format($mD009['T001_Aliquota_PIS'], 2, ',', '.');
                }
            }
        }

        $preco -= $pisCompra;
        log('3 PIS COMPRA: ' . $pisCompra . ' - ' . $aliquotaPIS);
        $demostrativo[] = array(
            'campo' => $aliquotaPIS . '% PIS compra',
            'valor' => $pisCompra,
            'operacao' => '-'
        );


        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S') {
            if ($T219_Id > 0 || $T225_Id > 0) {
                $cofinsCompra   = $mD009['T001_Valor_COFINS'];
                $aliquotaCOFINS = number_format($mD009['T001_Aliquota_COFINS'], 2, ',', '.');
            } else {
                if ($mD009['D009_Flag_Custo_Utilizado'] == 'T') {
                    $cofinsCompra   = $mD009['D069_Valor_COFINS'];
                    $aliquotaCOFINS = number_format($mD009['D069_Aliquota_COFINS'], 2, ',', '.');
                } else {
                    $cofinsCompra   = $mD009['T001_Valor_COFINS'];
                    $aliquotaCOFINS = number_format($mD009['T001_Aliquota_COFINS'], 2, ',', '.');
                }
            }
        }

        $preco -= $cofinsCompra;
        log('4 COFINS COMPRA: ' . $cofinsCompra . ' - ' . $aliquotaCOFINS);
        $demostrativo[] = array(
            'campo' => $aliquotaCOFINS . '% COFINS compra',
            'valor' => $cofinsCompra,
            'operacao' => '-'
        );

        
        // ICF
        $ICF = 1;
        if ($mD009['D009_ICF_1'] > 0) {
            $ICF = $mD009['D009_ICF_1'];
        }
        // D082_ICF
        // C008_ICF
        $preco *= $ICF;
        log('5 ICF: ' . $ICF);
        $custoProduto = $preco;
        log('Custo: ' . $custoProduto);
        $demostrativo[] = array(
            'campo' => 'ICF',
            'valor' => $ICF,
            'operacao' => '*'
        );

        $demostrativo[] = array(
            'campo' => 'Custo Líquido',
            'valor' => $custoProduto,
            'operacao' => '<b>=</b>'
        );
        
        // IPV
        if($listaPreco == 'T'){
            //Caso a lista de preço informada seja "T", para uma transferência, o IPV será 1
            $IPV = 1;
        } else {
            $IPV = 0;
            if ($mD024['D024_IPV_1'] > 0) {
                $IPV = $mD024['D024_IPV_1'];
            } else if ($mD009['D009_IPV_' . $listaPreco] > 0) {
                $IPV = $mD009['D009_IPV_' . $listaPreco];
            } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
                switch ($listaPreco) {
                    case 1:
                        $IPV = $g['C020']['C020_IPV_Revenda'];
                        break;
                    case 2:
                        $IPV = $g['C020']['C020_IPV_Atacado'];
                        break;
                    case 3:
                        $IPV = $g['C020']['C020_IPV_Varejo'];
                        break;
                        if ($IPV <= 0) {
                            $IPV = 1;
                        }
                }
            }
        } 
        $preco *= $IPV;
        log('6 IPV: ' . $IPV);
        $demostrativo[] = array(
            'campo' => 'IPV',
            'valor' => $IPV,
            'operacao' => '*'
        );


        // ICMS
        $icmsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_ICMS);
        $icmsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $preco += $icmsVenda;
        }
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        log('7 ICMS VENDA: ' . $icmsVenda . ' - ' . $Aliquota_ICMS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => $icmsVenda,
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_PIS);
        $pisVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $preco += $pisVenda;
        }
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        log('8 PIS VENDA: ' . $pisVenda . ' - ' . $Aliquota_PIS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => $pisVenda,
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_COFINS);
        $cofinsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $preco += $cofinsVenda;
        }
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        log('9 COFINS VENDA: ' . $cofinsVenda . ' - ' . $Aliquota_COFINS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => $cofinsVenda,
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IPI
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $ipiVenda = 0;
            $porcentagem = 100 / (100 - $Aliquota_IPI);
            $ipiVenda   = ($preco * $porcentagem) - $preco;
            if ($Aliquota_IPI > 0) {
                //$preco += $ipiVenda;
            }
            $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
            log('10 IPI VENDA: ' . $ipiVenda . ' - ' . $Aliquota_IPI);
            
            $demostrativo[] = array(
                'campo' => $Aliquota_IPI . '% IPI venda',
                'valor' => $ipiVenda,
                'operacao' => '+',
                'exibir' => 'N'
            );
        }
        
        
        //Produtos em pré cadastro retornar valor zero
        if($mD009['D001_Flag_Pre_Cadastro'] == 'S'){
            //Removido return por Fabio 15/12/2014
            //return array(0, '');
        }

        //Caso o preço trabalho seja tabelado, retornamos o preço direto do D009
        //if($g['C031']['tipoPreco'] == 'M'){
        //Nova regra verifica se o preço manual está preenchido, caso esteja, irá substituir o preço por IPV
        if($precoTabelado > 0){
            $preco = $precoTabelado;
        }

        $preco = number_format($preco, $casasDecimaisPreco, ',', '.');
        return array(
            $preco,
            $demostrativo,
            $custoMedio,
            $quantidadeEstoque,
            $custoProduto,
            $origemMercadoria,
            $possuiNFEntrada
        );
    }
    
    /**
     * cad002RetornaPrecoCompra
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id         
     * @param String $Flag_ACP
     * @return String
     */ 
    public function cad002RetornaPrecoCompra($D009_Id, $listaPreco = null, $D024_Id = null, $Flag_ACP = null)
    {
        global $g;
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico.  É necessário informar a lista preço ou o D024_Id";
        }
        
        /**
         * Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido
         * irá usar o que veio, ignorando o D024
         */
        if ($listaPreco == null) {
            $D024       = mysql_query("SELECT D024_Flag_Lista_Preco FROM D024 WHERE D024_Id='{$D024_Id}'");
            $mD024      = mysql_fetch_array($D024);
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        $D009  = mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D041 ON D041_D049_Id=D049_Id AND ifnull(D041_C004_Id,0)=0 WHERE D009_Id='{$D009_Id}'");
        $mD009 = mysql_fetch_array($D009);
        
        switch ($listaPreco) {
            case '1':
                if ($mD009['D009_Preco_1'] == 0.00 || $mD009['D009_Preco_1'] == null) {
                    // retorna D041
                    $preco = number_format($mD009['D041_Valor_Preco_Revenda'], 4, ',', '.');
                } else {
                    // retorna D009
                    $preco = number_format($mD009['D009_Preco_1'], 4, ',', '.');
                }
                break;
            case '2':
                if ($mD009['D009_Preco_2'] == 0.00 || $mD009['D009_Preco_2'] == null) {
                    // retorna D041
                    $preco = number_format($mD009['D041_Valor_Base'], 4, ',', '.');
                } else {
                    // retorna D009
                    $preco = number_format($mD009['D009_Preco_2'], 4, ',', '.');
                }
                break;
            case '3':
                if ($mD009['D009_Preco_3'] == 0.00 || $mD009['D009_Preco_3'] == null) {
                    // retorna D041
                    $preco = number_format($mD009['D041_Valor_Preco_Consumidor'], 4, ',', '.');
                } else {
                    // retorna D009
                    $preco = number_format($mD009['D009_Preco_3'], 4, ',', '.');
                }
                break;
            default:
                if ($mD009['D009_Preco_1'] == 0.00 || $mD009['D009_Preco_1'] == null) {
                    // retorna D041
                    $preco = number_format($mD009['D041_Valor_Preco_Consumidor'], 4, ',', '.');
                } else {
                    // retorna D009
                    $preco = number_format($mD009['D009_Preco_1'], 4, ',', '.');
                }
        }
        return $preco;
    }
    
    /**
     * insereRegistrosT098
     *
     * @param String $D009_Id
     */     
    public function insereRegistrosT098($D009_Id)
    {
        global $g;
        $D009  = mysqli_query("SELECT * FROM D009 WHERE D009_Id='{$D009_Id}'");
        $mD009 = mysql_fetch_array($D009);
        
        $T098   = <<<EOT
            INSERT INTO T098 (
                T098_D009_Id,
                T098_Data,
                T098_C007_Id,
                T098_D009_ICF_1,
                T098_D009_Valor_Preco_Tabela,
                T098_D009_Valor_Custo_Tabela,
                T098_D009_Aliquota_ICMS_Tabela,
                T098_D009_Percentual_Desconto_Tabela,
                T098_D009_IPV_1,
                T098_D009_IPV_2,
                T098_D009_IPV_3,
                T098_D009_ICF_2,
                T098_D009_ICF_3,
                T098_Flag_Promocao,
                T098_D009_Preco_1,
                T098_D009_Preco_2,
                T098_D009_Preco_3,
                T098_C004_Id
            ) VALUES (
                '{$D009_Id}',
                CURRENT_TIMESTAMP(),
                '{$g['usuarioAtual']}',
                '{$mD009['D009_ICF_1']}',
                '{$mD009['D009_Valor_Preco_Tabela']}',
                '{$mD009['D009_Valor_Custo_Tabela']}',
                '{$mD009['D009_Aliquota_ICMS_Tabela']}',
                '{$mD009['D009_Percentual_Desconto_Tabela']}',
                '{$mD009['D009_IPV_1']}',
                '{$mD009['D009_IPV_2']}',
                '{$mD009['D009_IPV_3']}',
                '{$mD009['D009_ICF_2']}',
                '{$mD009['D009_ICF_3']}',
                '{$mD009['D009_Flag_Promocao']}',
                '{$mD009['D009_Preco_1']}',
                '{$mD009['D009_Preco_2']}',
                '{$mD009['D009_Preco_3']}',
                '{$g['empresaAtual']}'
            );
EOT;
        $upD009 = mysqli_query("UPDATE D009 SET D009_Data_Alteracao_Preco=CURRENT_TIMESTAMP() WHERE D009_Id='{$D009_Id}'");
        mysqli_query($T098);
    }
    
    /**
     * alterarClientePreCadastro
     *
     * @return true
     */     
    public function reprocessarValidade()
    {
        /**
         * Fazendo o cálculo do saldo atual para exibir a validade do produto
         */
        $D009 = mysql_query("SELECT * FROM D009 WHERE D009_Quantidade_Estoque>0");
        while ($mD009 = mysql_fetch_array($D009)) {
            // seleciona todas as entradas desse D009 ordenando da mais nova para a mais antiga
            $T001Validade = mysql_query("SELECT T001_Id, T001_Quantidade FROM T001 
                 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND (T001_Flag_Operacao='E' OR T001_Flag_Operacao='B') ORDER BY T001_Id DESC");
            
            $D009_Quantidade_Estoque_Validade = $mD009['D009_Quantidade_Estoque'];
            
            while ($mT001Validade = mysql_fetch_array($T001Validade)) {
                
                if ($D009_Quantidade_Estoque_Validade > $mT001Validade['T001_Quantidade']) {
                    
                    /**
                     * caso a quantidade em estoque seja maior que a quantidade da entrada, 
                     * quer dizer que o saldo validade dessa entrada é exatamente o que entrou
                     */
                    $upT001 = mysql_query("UPDATE T001 SET T001_Saldo_Validade='{$mT001Validade['T001_Quantidade']}' WHERE T001_Id='{$mT001Validade['T001_Id']}'");
                    
                    /**
                     * Diminui o estoque atual do saldo atual dessa entrada, o restante é das entradas anteriores 
                     * (irá executar no próximo looping porém é da entrada anterior)
                     */
                    $D009_Quantidade_Estoque_Validade -= $mT001Validade['T001_Quantidade'];
                    
                } else {
                    
                    if ($D009_Quantidade_Estoque_Validade > 0) {
                        
                        /**
                         * caso a quantidade em estoque não seja zero ou negatiava, o saldo dessa 
                         * entrada é o restante da subtração do primeiro IF
                         */
                        $upT001 = mysql_query("UPDATE T001 SET T001_Saldo_Validade='{$D009_Quantidade_Estoque_Validade}' WHERE T001_Id='{$mT001Validade['T001_Id']}'");
                        
                        // zerando o estoque atual para fazer o UPDATE nas entradas que não possuem saldo em estoque (cair no último else)
                        $D009_Quantidade_Estoque_Validade = 0;
                    } else {
                        
                        // fazendo UPDATE do salco igual a zero nas entradas que não possuem saldo
                        $upT001 = mysql_query("UPDATE T001 SET T001_Saldo_Validade='{$D009_Quantidade_Estoque_Validade}' WHERE T001_Id='{$mT001Validade['T001_Id']}'");
                    }
                }
            }
        }
        return true;
    }
    
    /**
     * cad002RetornarPrecosPromocao
     *
     * @param String $extraRetornoPreco
     * @return String/bool
     */     
    public function cad002RetornarPrecosPromocao($extraRetornoPreco)
    {
        global $g;
        $extraRetornoPreco = base64_decode($extraRetornoPreco);
        $D009              = mysql_query("SELECT * FROM D049 {$extraRetornoPreco}");
        
        
        while ($mD009 = mysql_fetch_assoc($D009)) {
            $preco_Update_1 = "";
            $preco_Update_2 = "";
            $preco_Update_3 = "";
            $T098           = mysql_query("SELECT T098_D009_Preco_1, T098_D009_Preco_2, T098_D009_Preco_3, T098_Data, T098_Flag_Promocao FROM T098 WHERE T098_D009_Id='{$mD009['D009_Id']}' ORDER BY T098_Id DESC;");
            $count1         = 1; // count criado para que entre no if apenas umas vez e pegue o primeiro preço diferente para dar o UPDATE
            $count2         = 1;
            $count3         = 1;
            while ($mT098 = mysql_fetch_assoc($T098)) {
                
                if (($mD009['D009_Preco_1'] != $mT098['T098_D009_Preco_1']) && ($mT098['T098_D009_Preco_1'] != '0.00') && ($mT098['T098_Flag_Promocao'] == "N") && ($count1 == 1)) {
                    $preco_Update_1 = $mT098['T098_D009_Preco_1'];
                    $count1++;
                }
                
                if (($mD009['D009_Preco_2'] != $mT098['T098_D009_Preco_2']) && ($mT098['T098_D009_Preco_2'] != '0.00') && ($mT098['T098_Flag_Promocao'] == "N") && ($count2 == 1)) {
                    $preco_Update_2 = $mT098['T098_D009_Preco_2'];
                    $count2++;
                }
                if (($mD009['D009_Preco_3'] != $mT098['T098_D009_Preco_3']) && ($mT098['T098_D009_Preco_3'] != '0.00') && ($mT098['T098_Flag_Promocao'] == "N") && ($count3 == 1)) {
                    $preco_Update_3 = $mT098['T098_D009_Preco_3'];
                    $count3++;
                }
            }
            $preco_Update_1 = (!empty($preco_Update_1)) ? $preco_Update_1 : "0.00";
            $preco_Update_2 = (!empty($preco_Update_2)) ? $preco_Update_2 : "0.00";
            $preco_Update_3 = (!empty($preco_Update_3)) ? $preco_Update_3 : "0.00";

            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            
            $upD009 = mysql_query("UPDATE D009 SET D009_Preco_1='{$preco_Update_1}', D009_Preco_2='{$preco_Update_2}', D009_Preco_3='{$preco_Update_3}', D009_Flag_Promocao='N' WHERE D009_Id='{$mD009['D009_Id']}';");
            $erro   = mysql_error();
            if ($erro) {
                return "Erro: " . $erro . " <br />D009_Id: {$mD009['D009_Id']}";
                mysql_query("ROLLBACK");
            }
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * cad002GerarXmlProdutos
     *
     * @param String $extra
     * @return true
     */     
    public function cad002GerarXmlProdutos($extra)
    {
        global $g;
        
        $extraTeste = base64_decode($extra);
        header("Content-Type: text/xml");
        $dom = new \DOMDocument("1.0", "UTF-8");
        
        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;
        
        #gerar o codigo
        $dom->formatOutput = true;
        
        #criando o nó principal (root)
        $importa_estoque = $dom->createElement("importa_estoque");
        $dom->appendChild($importa_estoque);
        
        $produtos = $dom->createElement("produtos");
        $importa_estoque->appendChild($produtos);
        
        $D001 = mysql_query("SELECT * FROM D001 {$extraTeste}");
        
        while ($mD001 = mysql_fetch_array($D001)) {
            
            // Criado o nó produto
            $produto = $dom->createElement("produto");
            
            $mTipo           = 'P';
            $mCodigo_produto = $mD001['D001_Codigo_Produto'];
            $mReferencia     = '';
            $mDescricao      = $mD001['D001_Descricao_Produto'];
            $mSaldo          = $mD001['D009_Quantidade_Estoque_Real'];
            $mPreco          = $mD001['D041_Valor_Base'];
            $mPreco_custo    = $mD001['D009_Valor_Custo_Unitario'];
            $mPeso           = $mD001['D001_Peso_Unitario_Kg'];
            $mPeso_cubico    = '';
            
            $tipo           = $dom->createElement("tipo", $mTipo); // 
            $codigo_produto = $dom->createElement("codigo_produto", $mCodigo_produto); // D001_Codigo_Produto
            $referencia     = $dom->createElement("referencia", $mReferencia); // 
            $descricao      = $dom->createElement("descricao", $mDescricao); // D001_Descricao_Produto
            $saldo          = $dom->createElement("saldo", $mSaldo); // D009_Quantidade_Estoque_Real
            $preco          = $dom->createElement("preco", $mPreco); // D041_Valor_Base
            $preco_custo    = $dom->createElement("preco_custo", $mPreco_custo); // D009_Valor_Custo_Unitario
            $peso           = $dom->createElement("peso", $mPeso); // D001_Peso_Unitario_Kg
            $peso_cubico    = $dom->createElement("peso_cubico", $mPeso_cubico);
            
            
            // Adicionado ou nó "produto" todos seus sub-tens
            $produto->appendChild($tipo);
            $produto->appendChild($codigo_produto);
            $produto->appendChild($referencia);
            $produto->appendChild($descricao);
            $produto->appendChild($saldo);
            $produto->appendChild($preco);
            $produto->appendChild($preco_custo);
            $produto->appendChild($peso);
            $produto->appendChild($peso_cubico);
            
            // Adicionado a nó "Produtos" cada produto criado
            $produtos->appendChild($produto);
        }
        $dir = $g['pathDados'] . "estoque/produtos/";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $dom->save("{$dir}estoque.xml");
        
        return true;
        
        //print $dom->saveXML();
        
        // $dir = $g['pathDadosUsuario']."arq_temp/";
        // is_dir($dir) OR mkdir($dir, 0777, true);
        // $cArquivoTemp=$g['pathDadosUsuario']."arq_temp/".basename("estoque.xml");
        // if (!@move_uploaded_file($mArquivo["tmp_name"], $cArquivoTemp))
        // {
        //  return array(0,0,"Erro ao tentar copiar arquivo temporario: " . $mArquivo['tmp_name'] . "para: ".$cArquivoTemp);
        // }    
        
        // $zip = zip_open($cArquivoTemp);
    }
    
    /**
     * retornaICMS
     *
     * @param String $C004_Id
     * @param String $D024_Id
     * @param String $D009_Id
     * @param String $D006_Id
     * @param String $D018_Id
     * @param String $Flag_Revenda_Consumidor
     * @param String $Comissao
     * @param String $Documento
     * @param String $Tipo
     * @return String
     */     
    public function retornaICMS($C004_Id, $D024_Id, $D009_Id, $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Comissao, $Documento, $Tipo, $Flag_Destino_Produto = false, $origemProduto = false)
    {
        global $g;
        $Aliquota_ICMS                    = 0;
        $Percentual_Reducao_ICMS          = 0;
        $Aliquota_IPV_Cliente             = 0;
        $Aliquota_IPV_Utilizado           = 0;
        $D024_Inscricao_Estadual          = '';
        $D024_Flag_Pessoa_Fisica_Juridica = '';
        $D024_Valor_IPV                   = 0;
        $D024_Flag_Isento_ICMS            = '';
        $D024_Flag_Isento_ICMS_Venda      = 'N';
        $D024_Percentual_Reducao_ICMS     = 0;
        $D024_Aliquota_ICMS               = 0;
        $D020_Flag_ICMS                   = '';
        $D006_Flag_Isento_ICMS            = '';
        $D049_Id                          = 0;
        
        if(empty($C004_Id)){
            $C004_Id = $g['empresaAtual'];
        }

        $C004  = mysql_query("SELECT C004_Uf, C004_Flag_Isento_ICMS, C004_D018_Id FROM C004 WHERE C004_Id=$C004_Id");
        $mC004 = mysql_fetch_array($C004);
        
        if ($D024_Id > 0) {
            $D024  = mysql_query("SELECT D024_Inscricao_Estadual,
                  D024_Flag_Pessoa_Fisica_Juridica,
                  D024_Valor_IPV,
                  D024_Flag_Revenda_Consumidor,
                  D024_Flag_Isento_ICMS,
                  D024_Flag_Isento_ICMS_Venda,
                  D024_Percentual_Reducao_ICMS,
                  D024_Aliquota_ICMS,
                  D053_Aliquota_ICMS_1,
                  D053_Percentual_Reducao_ICMS_1,
                  D053_Aliquota_ICMS_2,
                  D053_Percentual_Reducao_ICMS_2,
                  D053_Aliquota_ICMS_Revenda,
                  D053_Percentual_Reducao_ICMS_Revenda,
                  D053_Aliquota_ICMS_Consumidor,
                  D053_Percentual_Reducao_ICMS_Consumidor,
                  D020_Flag_ICMS,
                  D053_Aliquota_ICMS_Importado,
                  D053_Aliquota_ICMS_Importado_2,
                  D018_UF,
                  D024_D018_Id
                  FROM D024
                  LEFT JOIN D020 ON D020_Id=D024_D020_Id
                  LEFT JOIN D018 ON D018_Id=D024_D018_Id
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
            WHERE D024_Id='{$D024_Id}' LIMIT 1");
            $mD024 = mysql_fetch_array($D024);
            
            $D018_Aliquota_ICMS_1                    = $mD024['D053_Aliquota_ICMS_1'];
            $D018_Percentual_Reducao_ICMS_1          = $mD024['D053_Percentual_Reducao_ICMS_1'];
            $D018_Aliquota_ICMS_2                    = $mD024['D053_Aliquota_ICMS_2'];
            $D018_Percentual_Reducao_ICMS_2          = $mD024['D053_Percentual_Reducao_ICMS_2'];
            $D018_Aliquota_ICMS_Revenda              = $mD024['D053_Aliquota_ICMS_Revenda'];
            $D018_Percentual_Reducao_ICMS_Revenda    = $mD024['D053_Percentual_Reducao_ICMS_Revenda'];
            $D018_Aliquota_ICMS_Consumidor           = $mD024['D053_Aliquota_ICMS_Consumidor'];
            $D018_Percentual_Reducao_ICMS_Consumidor = $mD024['D053_Percentual_Reducao_ICMS_Consumidor'];
            $D018_UF                                 = $mD024['D018_UF'];
            $D020_Flag_ICMS                          = $mD024['D020_Flag_ICMS'];
            $D024_Valor_IPV                          = $mD024['D024_Valor_IPV'];
            $D018_Aliquota_ICMS_Importado_1          = $mD024['D053_Aliquota_ICMS_Importado'];
            $D018_Aliquota_ICMS_Importado_2          = $mD024['D053_Aliquota_ICMS_Importado_2'];
            
        } elseif ($D018_Id > 0) {
            
            $mD024['D024_Flag_Revenda_Consumidor'] = $Flag_Revenda_Consumidor;
            
            $D018  = mysql_query("SELECT 
                  D053_Aliquota_ICMS_1,
                  D053_Percentual_Reducao_ICMS_1,
                  D053_Aliquota_ICMS_2,
                  D053_Percentual_Reducao_ICMS_2,
                  D053_Aliquota_ICMS_Revenda,
                  D053_Percentual_Reducao_ICMS_Revenda,
                  D053_Aliquota_ICMS_Consumidor,
                  D053_Percentual_Reducao_ICMS_Consumidor,
                  D053_Aliquota_ICMS_Importado,
                  D018_UF,
                  D053_Aliquota_ICMS_Importado_2
                  FROM D018
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
                  WHERE D018_Id='{$D018_Id}'
                  LIMIT 1");
            $mD018 = mysql_fetch_array($D018);
            
            $D018_Aliquota_ICMS_1                    = $mD018['D053_Aliquota_ICMS_1'];
            $D018_Percentual_Reducao_ICMS_1          = $mD018['D053_Percentual_Reducao_ICMS_1'];
            $D018_Aliquota_ICMS_2                    = $mD018['D053_Aliquota_ICMS_2'];
            $D018_Percentual_Reducao_ICMS_2          = $mD018['D053_Percentual_Reducao_ICMS_2'];
            $D018_Aliquota_ICMS_Revenda              = $mD018['D053_Aliquota_ICMS_Revenda'];
            $D018_Percentual_Reducao_ICMS_Revenda    = $mD018['D053_Percentual_Reducao_ICMS_Revenda'];
            $D018_Aliquota_ICMS_Consumidor           = $mD018['D053_Aliquota_ICMS_Consumidor'];
            $D018_Percentual_Reducao_ICMS_Consumidor = $mD018['D053_Percentual_Reducao_ICMS_Consumidor'];
            $D018_UF                                 = $mD018['D018_UF'];
            $D018_Aliquota_ICMS_Importado_1          = $mD018['D053_Aliquota_ICMS_Importado'];
            $D018_Aliquota_ICMS_Importado_2          = $mD018['D053_Aliquota_ICMS_Importado_2'];
        }
        if ($D006_Id > 0) {
            
            $D006  = mysql_query("
                SELECT D006_Flag_Isento_ICMS,
                      D006_Flag_Venda_Compra_Outros,
                      D006_Percentual_Reducao_ICMS,
                      D006_D059_Id,
                      D006_Flag_Isento_ICMS,
                      D006_Aliquota_ICMS,
                      D006_Percentual_Reducao_ICMS
                 FROM D006
                WHERE D006_Id='{$D006_Id}';
            ");
            $mD006 = mysql_fetch_array($D006);
        }
        
        if ($D009_Id > 0) {
            
            $D009  = mysql_query("
            SELECT D009_Aliquota_ICMS_Venda,
                D009_Percentual_Reducao_ICMS_Venda,
                D009_Flag_Isento_ICMS_Venda,
                D009_D049_Id,
                D049_Origem_Mercadoria
            FROM D049
                LEFT JOIN D009 ON D009_D049_Id=D049_Id
                WHERE D009_Id='{$D009_Id}'
                AND D009_C004_Id='{$C004_Id}';");
            $mD009 = mysql_fetch_array($D009);
            
            if ($mD006['D006_Flag_Venda_Compra_Outros'] != 'T') {
                
                $T046 = mysql_query("SELECT T046_Id
                    FROM T046
                    LEFT JOIN T016 ON T016_Id=T046_T016_Id
                    WHERE T046_D049_Id='{$mD009['D009_D049_Id']}'
                    AND CURRENT_DATE()>=T016_Data_Inicio
                    AND CURRENT_DATE()<=T016_Data_Final
                    LIMIT 1;");
                
                $mT046    = mysql_fetch_array($T046);
                $Tabelado = $mT046['T046_Id'];
                
                if ($Tabelado == "NULL") {
                    
                    $D041     = mysql_query("SELECT D041_Id
                    FROM D041
                    WHERE D041_D049_Id='{$mD009['D009_D049_Id']}'
                    LIMIT 1;");
                    $mD041    = mysql_fetch_array($D041);
                    $Tabelado = $mD041['D041_Id'];
                }
            }
        }

        if ($origemProduto) {
            $mD009['D049_Origem_Mercadoria'] = $origemProduto;
        }

        // Quando a CFOP é isenta de ICMS, o sistema vai gerar o valor do ICMS nos campos com extenção "Oculto", para que estes valores possam ser utilizados na observação da nota
        // O tipo = 22 ou 33 é para retornar as aliquotas mesmo que a CFOP seja isenta
        // Marcelo de Paula - 18-09-2014
        if ($Tipo == 22 or $Tipo == 33){
            $mD006['D006_Flag_Isento_ICMS'] = 'N';
            $mC004['C004_Flag_Isento_ICMS'] = 0;
        }

        if($mD006['D006_Flag_Isento_ICMS'] == 'D' && ($mD006['D006_D059_Id'] == '5' || $mD006['D006_D059_Id'] == '6' || $mD006['D006_D059_Id'] == '7')){
            $mD006['D006_Flag_Isento_ICMS'] = 'S';
        }

        if ($mC004['C004_Flag_Isento_ICMS'] == 1 OR $mD006['D006_Flag_Isento_ICMS'] == 'S' OR $D020_Flag_ICMS == 'S' OR $mD009['D009_Flag_Isento_ICMS_Venda'] == 1) {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if ($mD006['D006_Flag_Venda_Compra_Outros'] == 'V' AND $mD024['D024_Flag_Isento_ICMS_Venda'] == 'S') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if ($mD006['D006_Flag_Venda_Compra_Outros'] == 'C' AND $mD024['D024_Flag_Isento_ICMS'] == '1') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if((($mD009['D049_Origem_Mercadoria'] == '1' || $mD009['D049_Origem_Mercadoria'] == '2' || $mD009['D049_Origem_Mercadoria'] == '3' || $mD009['D049_Origem_Mercadoria'] == '8') AND $mD024['D024_D018_Id'] != $mC004['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND ($mD024['D024_Inscricao_Estadual'] == '' OR $mD024['D024_Inscricao_Estadual'] == 'ISENTO' OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F')) {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_Importado_1;
        } else if((($mD009['D049_Origem_Mercadoria'] == '1' || $mD009['D049_Origem_Mercadoria'] == '2' || $mD009['D049_Origem_Mercadoria'] == '3' || $mD009['D049_Origem_Mercadoria'] == '8') AND $mD024['D024_D018_Id'] != $mC004['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND (($mD024['D024_Inscricao_Estadual'] != '' AND $mD024['D024_Inscricao_Estadual'] != 'ISENTO') OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J')) {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_Importado_2;
        } else if ($mD009['D009_Aliquota_ICMS_Venda'] > 0) {
            $Aliquota_ICMS           = $mD009['D009_Aliquota_ICMS_Venda'];
            $Percentual_Reducao_ICMS = $mD009['D009_Percentual_Reducao_ICMS_Venda'];
        } else if ($mD024['D024_Aliquota_ICMS'] > 0) {
            $Aliquota_ICMS           = $mD024['D024_Aliquota_ICMS'];
            $Percentual_Reducao_ICMS = $mD024['D024_Percentual_Reducao_ICMS'];
        } else if ($mD006['D006_Aliquota_ICMS'] > 0){
            $Aliquota_ICMS           = $mD006['D006_Aliquota_ICMS'];
            $Percentual_Reducao_ICMS = $mD006['D006_Percentual_Reducao_ICMS'];
        } else if ($mD024['D024_Inscricao_Estadual'] == '' OR $mD024['D024_Inscricao_Estadual'] == 'ISENTO') {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_1;
            $Percentual_Reducao_ICMS = $D018_Percentual_Reducao_ICMS_2;
        } else if (($mD024['D024_Inscricao_Estadual'] != '' AND $mD024['D024_Inscricao_Estadual'] != 'ISENTO') OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_2;
            $Percentual_Reducao_ICMS = $D018_Percentual_Reducao_ICMS_1;
        } 

        if ($D024_Valor_IPV > 0) {
            $Aliquota_IPV_Cliente = $D024_Valor_IPV;
            $Flag_IPV_Cliente     = 'S';
            
            $D019  = mysql_query("SELECT 
                              D019_Tipo_1,
                              D019_Percentual_Desconto_ACP
                            FROM D019
                            WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
            $mD019 = mysql_fetch_array($D019);
            
            $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
            $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];
            
        } else {
            
            if ($mD024['D024_Flag_Revenda_Consumidor'] != '1') {
                
                $D019  = mysql_query("
                    SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP
                    FROM D019
                    WHERE D019_Aliquota_ICMS=0;");
                $mD019 = mysql_fetch_array($D019);
                
                $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
                $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];
                $D019_IPV_Maximo              = $mD019['D019_IPV_Maximo'];
                
            } else {
                
                $D019 = mysql_query("
                SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP
                FROM D019
                WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
                
                $mD019 = mysql_fetch_array($D019);
                
                $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
                $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];
                $D019_IPV_Maximo              = $mD019['D019_IPV_Maximo'];
                
            }
            $Aliquota_IPV_Cliente = $D019_IPV_Maximo;
            $Flag_IPV_Cliente     = 'N';
        }
        
        if ($Documento == 3) {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        }
        
        if ($D009_Id > 0) {
            $D024_2  = mysql_query("
            SELECT D024_Percentual_IPV_Consumidor,
                 D024_Percentual_IPV_Fornecedor_1,
                 D024_Percentual_IPV_Fornecedor_2,
                 D024_Percentual_IPV_Fornecedor_3
            FROM D009
                 LEFT JOIN D049 ON D009_D049_Id=D049_Id
                 LEFT JOIN D024 ON D049_D024_Id=D024_Id
                 WHERE D009_Id='{$D009_Id}'
                 AND D009_C004_Id='{$C004_Id}'");
            $mD024_2 = mysql_fetch_array($D024_2);
            
            $D024_Percentual_IPV_Consumidor   = $mD024_2['D024_Percentual_IPV_Consumidor'];
            $D024_Percentual_IPV_Fornecedor_1 = $mD024_2['D024_Percentual_IPV_Fornecedor_1'];
            $D024_Percentual_IPV_Fornecedor_2 = $mD024_2['D024_Percentual_IPV_Fornecedor_2'];
            $D024_Percentual_IPV_Fornecedor_3 = $mD024_2['D024_Percentual_IPV_Fornecedor_3'];
            
            if ($Flag_IPV_Cliente == 'N') {
                if ($mD024['D024_Flag_Revenda_Consumidor'] != '1') {
                    $Aliquota_IPV_Utilizado = $D024_Percentual_IPV_Consumidor;
                } elseif ($Aliquota_ICMS == 7) {
                    $Aliquota_IPV_Utilizado = $D024_Percentual_IPV_Fornecedor_1;
                } elseif ($Aliquota_ICMS == 12) {
                    $Aliquota_IPV_Utilizado = $D024_Percentual_IPV_Fornecedor_2;
                } elseif ($Aliquota_ICMS == 17 OR $Aliquota_ICMS == 18) {
                    $Aliquota_IPV_Utilizado = $D024_Percentual_IPV_Fornecedor_3;
                }
            }
            if ($Aliquota_IPV_Utilizado == 0) {
                $Aliquota_IPV_Utilizado = $Aliquota_IPV_Cliente;
            }
        } else {
            $Aliquota_IPV_Utilizado = $Aliquota_IPV_Cliente;
        }
        
        if ($Comissao > 0) {
            $D019_Tipo_2            = $Aliquota_IPV_Utilizado / (100 / (100 - $D019_Tipo_1));
            $D019_Tipo_2            = (($D019_Tipo_2 - 1) * 100) / $D019_Tipo_2;
            $D019_Tipo_2            = (100 / (100 - ($D019_Tipo_2 + $Comissao)));
            $D019_Tipo_1            = (100 / (100 - ($D019_Tipo_1)));
            $Aliquota_IPV_Utilizado = round($D019_Tipo_1 * $D019_Tipo_2, 4);
        }
        
        if ($Documento == 3) {
            $Aliquota_IPV_Utilizado = $Aliquota_IPV_Utilizado - ($Aliquota_IPV_Utilizado * (($D019_Percentual_Desconto_ACP * 2) / 100));
        } else {
            
            if ($mD006['D006_Percentual_Reducao_ICMS'] > 0) {
                $Percentual_Reducao_ICMS = $mD006['D006_Percentual_Reducao_ICMS'];
            }
            
            if ($mC004['C004_Flag_Isento_ICMS'] == 1 OR $mD006['D006_Flag_Isento_ICMS'] == 'S' OR $D020_Flag_ICMS == 'S' OR $mD009['D009_Flag_Isento_ICMS_Venda'] == 1) {
                $Aliquota_ICMS           = 0;
                $Percentual_Reducao_ICMS = 0;
            }

            // Quando a CST for DIFERIMENTO (ID 8) e o tipo for 33 (ICMS_Oculto)
            if($mD006['D006_D059_Id'] == '8' && $Tipo == 33){
                $Percentual_Reducao_ICMS = 0;
            }
            if ($Tipo == 1 AND $Tabelado > 0) {
                
                if ($mD024['D024_Flag_Revenda_Consumidor'] != '1') {
                    $D019                   = mysql_query("
                    SELECT D019_IPV_Maximo
                    FROM D019
                    WHERE D019_Aliquota_ICMS=0;");
                    $mD019                  = mysql_fetch_array($D019);
                    $Aliquota_IPV_Utilizado = $mD019['D019_IPV_Maximo'];
                } else {
                    $D019                   = mysql_query("
                    SELECT D019_IPV_Maximo
                    FROM D019
                    WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
                    $mD019                  = mysql_fetch_array($D019);
                    $Aliquota_IPV_Utilizado = $mD019['D019_IPV_Maximo'];
                }
            }
        }
        
         
        $retorno = $this->excessoesICMS($D006_Id,$D024_Id,$Flag_Destino_Produto,$mD009['D049_Origem_Mercadoria']);

        if(!empty($retorno['Aliquota_ICMS'])){
            $Aliquota_ICMS = $retorno['Aliquota_ICMS'];
        }

        if(!empty($retorno['Percentual_Reducao_ICMS'])){
            $Percentual_Reducao_ICMS = $retorno['Percentual_Reducao_ICMS'];
        }

        if(!empty($retorno['Aliquota_IPV_Utilizado'])){
            $Aliquota_IPV_Utilizado = $retorno['Aliquota_IPV_Utilizado'];
        }
        
        if ($Tipo == 1) {
            return $Aliquota_IPV_Utilizado;
        } else if ($Tipo == 2 || $Tipo == 22) {
            return $Aliquota_ICMS;
        } else if ($Tipo == 3 || $Tipo == 33) {
            return $Percentual_Reducao_ICMS;
        }
    }

    public function excessoesICMS($D006_Id=false,$D024_Id=false,$Flag_Destino_Produto=false,$Flag_Origem_Produto=false){
        //Personalizar validações para clientes
        $retorno = array();

        $retorno['Aliquota_ICMS'] = "";
        $retorno['Aliquota_IPV_Utilizado'] = "";
        $retorno['Percentual_Reducao_ICMS'] = "";

        return $retorno;
    }
    
    /**
     * retornaICMSCompra
     *
     * @param String $C004_Id
     * @param String $D024_Id
     * @param String $D009_Id
     * @param String $D006_Id
     * @param String $D018_Id
     * @param String $Flag_Revenda_Consumidor
     * @param String $Comissao
     * @param String $Documento
     * @param String $Tipo
     * @return String
     */     
    public function retornaICMSCompra($C004_Id, $D024_Id, $D009_Id, $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Comissao, $Documento, $Tipo, $Origem_Mercadoria)
    {
        
        $Aliquota_ICMS                    = 0;
        $Percentual_Reducao_ICMS          = 0;
        $Aliquota_IPV_Cliente             = 0;
        $Aliquota_IPV_Utilizado           = 0;
        $D024_Inscricao_Estadual          = '';
        $D024_Flag_Pessoa_Fisica_Juridica = '';
        $D024_Valor_IPV                   = 0;
        $D024_Flag_Isento_ICMS            = '';
        $D024_Flag_Isento_ICMS_Venda      = 'N';
        $D024_Percentual_Reducao_ICMS     = 0;
        $D024_Aliquota_ICMS               = 0;
        $D020_Flag_ICMS                   = '';
        $D006_Flag_Isento_ICMS            = '';
        $D049_Id                          = 0;
        
        $C004  = mysql_query("SELECT C004_Uf,
                                     C004_Flag_Isento_ICMS,
                                     C004_D018_Id
                                FROM C004
                               WHERE C004_Id=$C004_Id");
        $mC004 = mysql_fetch_array($C004);
        
        if ($D024_Id > 0) {
            $D024  = mysql_query("SELECT D024_Inscricao_Estadual,
                  D024_Flag_Pessoa_Fisica_Juridica,
                  D053_Aliquota_ICMS_Compra,
                  D053_Aliquota_ICMS_Importado_Compra,
                  D020_Flag_ICMS,
                  D018_UF,
                  D024_D018_Id
                  FROM D024
                  LEFT JOIN D020 ON D020_Id=D024_D020_Id
                  LEFT JOIN D018 ON D018_Id=D024_D018_Id
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
            WHERE D024_Id='{$D024_Id}' LIMIT 1");
            $mD024 = mysql_fetch_array($D024);
            
            $D018_Aliquota_ICMS_1                    = $mD024['D053_Aliquota_ICMS_Compra'];
            $D018_UF                                 = $mD024['D018_UF'];
            $D020_Flag_ICMS                          = $mD024['D020_Flag_ICMS'];
            $D018_Aliquota_Importado_Compra          = $mD024['D053_Aliquota_ICMS_Importado_Compra'];
            
        } elseif ($D018_Id > 0) {
            
            $mD024['D024_Flag_Revenda_Consumidor'] = $Flag_Revenda_Consumidor;
            
            $D018  = mysql_query("SELECT 
                  D053_Aliquota_ICMS_Compra,
                  D053_Aliquota_ICMS_Importado_Compra,
                  D018_UF
                  FROM D018
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
                  WHERE D018_Id='{$D018_Id}'
                  LIMIT 1");
            $mD018 = mysql_fetch_array($D018);
            
            $D018_Aliquota_ICMS_1                    = $mD018['D053_Aliquota_ICMS_Compra'];
            $D018_UF                                 = $mD018['D018_UF'];
            $D018_Aliquota_Importado_Compra          = $mD024['D053_Aliquota_ICMS_Importado_Compra'];
        }
        if ($D006_Id > 0) {
            
            $D006  = mysql_query("
                SELECT D006_Flag_Isento_ICMS,
                      D006_Flag_Venda_Compra_Outros,
                      D006_Percentual_Reducao_ICMS,
                      D006_D059_Id,
                      D006_Aliquota_ICMS
                 FROM D006
                WHERE D006_Id='{$D006_Id}';
            ");
            $mD006 = mysql_fetch_array($D006);
        }
        
        $Percentual_Reducao_ICMS = $mD006['D006_Percentual_Reducao_ICMS'];
        $Aliquota_ICMS           = $D018_Aliquota_ICMS_1;
        if ($mD006['D006_Flag_Isento_ICMS'] == 'S' OR $D020_Flag_ICMS == 'S') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if ($mD006['D006_Aliquota_ICMS'] > 0){
            $Aliquota_ICMS           = $mD006['D006_Aliquota_ICMS'];
            $Percentual_Reducao_ICMS = $mD006['D006_Percentual_Reducao_ICMS'];
        } else if(($Origem_Mercadoria == '1' || $Origem_Mercadoria == '2' || $Origem_Mercadoria == '3') AND $mD006['D006_Flag_Isento_ICMS'] != 'S') {
            $Aliquota_ICMS            = $D018_Aliquota_Importado_Compra;
        }

        if ($Documento == 3) {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        }
        
        include_once("prog/cad/cad002/excessoesIcms.php");
        
        if ($Tipo == 1) {
            return $Aliquota_IPV_Utilizado;
        } else if ($Tipo == 2) {
            return $Aliquota_ICMS;
        } else if ($Tipo == 3) {
            return $Percentual_Reducao_ICMS;
        }
    }
    
    /**
     * calculaEstoque
     *
     * @param String $D009
     * @return bool
     */     
/*    public function calculaEstoque($D009)
    {
        $Quantidade_Minima_Estoque    = '0';
        $Quantidade_Maxima_Estoque    = '0';
        $Frequencia_Venda             = '0';
        $Quantidade_Comprar_Calculada = '0';
        $Quantidade_Consumo           = '0';
        $Data_Ultima_Venda            = '0000-00-00';
        $Data_Ultima_Saida            = '0000-00-00';
        $Clientes_Consumo             = '0';
        
        $ultimaData                   = date("Y-m-d", strtotime("-0" . $mD009['D009_Meses_MMF'] . " months"));
        log("Ultima data = " . $ultimaData);
        $T001_Medio                   = mysql_query("SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S'");
        log("T001_Medio = SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S'");
        $mT001_Media                  = mysql_fetch_array($T001_Medio);
        $Quantidade_Minima_Estoque    = $mT001_Media['SUM(T001_Quantidade)'] / $mD009['D009_Meses_MMF'];
        $Quantidade_Maxima_Estoque    = $Quantidade_Minima_Estoque * 2;
        $Quantidade_Comprar_Calculada = ($Quantidade_Minima_Estoque * $mD009['D009_Meses_Compra']) - $mD009['D009_Quantidade_Estoque'];
        if (($mD009['D049_Quantidade_Embalagem_Compra'] > 0) AND ($Quantidade_Comprar_Calculada > 0)) {
            $Quantidade_Comprar_Embalagem = round($Quantidade_Comprar_Calculada / $mD009['D049_Quantidade_Embalagem_Compra'], 0) * $mD009['D049_Quantidade_Embalagem_Compra'];
            if ($Quantidade_Comprar_Embalagem < $Quantidade_Comprar_Calculada) {
                if ((($Quantidade_Comprar_Calculada - $Quantidade_Comprar_Embalagem) / $mD009['D049_Quantidade_Embalagem_Compra']) > 0.30) {
                    $maisUmaEmbalagem = $mD009['D049_Quantidade_Embalagem_Compra'];
                }
            }
            $Quantidade_Comprar_Calculada = $Quantidade_Comprar_Embalagem + $maisUmaEmbalagem;
        }
        
        $T001              = mysql_query("SELECT COUNT(DISTINCT T001_D024_Id_Cliente) as Clientes FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S'");
        $mClientes_Consumo = mysql_fetch_array($T001);
        $Clientes_Consumo  = $mClientes_Consumo['Clientes'];
        
        $T001                   = mysql_query("SELECT COUNT(distinct month(T001_Data_Lancamento)) FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S'");
        $mMeses                 = mysql_fetch_array($T001);
        $Frequencia_Venda_Meses = $mMeses[0];
        
        $T001             = mysql_query("SELECT count(*) FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S'");
        $mFrequecia_Venda = mysql_fetch_array($T001);
        $Frequencia_Venda = $mFrequecia_Venda['count(*)'];
        
        $T001              = mysql_query("SELECT T001_Data_Lancamento FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Data_Lancamento >= '{$ultimaData}' AND T001_Flag_MMF='S' ORDER BY T001_Data_Lancamento DESC");
        $mT001_SUM_MMF     = mysql_fetch_array($T001);
        $Data_Ultima_Venda = $mT001_SUM_MMF['T001_Data_Lancamento'];
        $Data_Ultima_Saida = $mT001_SUM_MMF['T001_Data_Lancamento'];
        
        $T001                = mysql_query("SELECT SUM(T001_Quantidade) FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND T001_Flag_MMF='S' AND T001_Data_Lancamento >= '{$ultimaData}'");
        $mQuantidade_Consumo = mysql_fetch_array($T001);
        $Quantidade_Consumo  = $mQuantidade_Consumo['SUM(T001_Quantidade)'];
        
        mysql_query("UPDATE D009
                  LEFT JOIN D049 on D049_Id=D009_D049_Id
                  LEFT JOIN D082 on D082_Id=D049_D082_Id
                  LEFT JOIN D001 on D001_Id=D049_D001_Id
                  LEFT JOIN C008 on C008_Id=D001_C008_Id
                     SET D009_Quantidade_Minima_Estoque='{$Quantidade_Minima_Estoque}',
                         D009_Quantidade_Maxima_Estoque='{$Quantidade_Maxima_Estoque}',
                         D009_Frequencia_Venda='{$Frequencia_Venda}',
                         D009_Quantidade_Comprar_Calculada='{$Quantidade_Comprar_Calculada}',
                         D009_Quantidade_Consumo='{$Quantidade_Consumo}',
                         D009_Data_Ultima_Venda='$Data_Ultima_Venda',
                         D009_Data_Ultima_Saida='{$Data_Ultima_Saida}',
                         D009_Clientes_Consumo='{$Clientes_Consumo}',
                         D009_Frequencia_Venda_Meses='{$Frequencia_Venda_Meses}'
                   WHERE D009_Id='{$mD009['D009_Id']}'");
        log("UPDATE D009
                  LEFT JOIN D049 on D049_Id=D009_D049_Id
                  LEFT JOIN D082 on D082_Id=D049_D082_Id
                  LEFT JOIN D001 on D001_Id=D049_D001_Id
                  LEFT JOIN C008 on C008_Id=D001_C008_Id
                     SET D009_Quantidade_Minima_Estoque='{$Quantidade_Minima_Estoque}',
                         D009_Quantidade_Maxima_Estoque='{$Quantidade_Maxima_Estoque}',
                         D009_Frequencia_Venda='{$Frequencia_Venda}',
                         D009_Quantidade_Comprar_Calculada='{$Quantidade_Comprar_Calculada}',
                         D009_Quantidade_Consumo='{$Quantidade_Consumo}',
                         D009_Data_Ultima_Venda='$Data_Ultima_Venda',
                         D009_Data_Ultima_Saida='{$Data_Ultima_Saida}',
                         D009_Clientes_Consumo='{$Clientes_Consumo}',
                         D009_Frequencia_Venda_Meses='{$Frequencia_Venda_Meses}'
                   WHERE D009_Id='{$mD009['D009_Id']}'");
        
        
    }
    */
    
    /**
     * associarProdutoListaFornecedor
     *
     * @param Form $form
     * @return String
     */     
    public function associarProdutoListaFornecedor($form)
    {
        $D009_Id = $form->campoValorEnviado('D009_Id');
        $D069_Id = $form->campoValorEnviado('D069_Id');

        log("D009_Id " . $D009_Id);

        $cClausula = " SELECT ";
        $cClausula .= " D009_Id,";
        $cClausula .= " D049_Id ";
        $cClausula .= " FROM D009 ";
        $cClausula .= " LEFT JOIN D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D005 on D009_D005_Id(D009_Id)=D005_Id ";
        $cClausula .= " LEFT JOIN D037 on D037_Id=D001_D037_Id ";
        $cClausula .= " WHERE D009_Id='{$D009_Id}' ";
        $D009 = mysql_query("{$cClausula}");
        echo mysql_error();
        $mD009 = mysql_fetch_array($D009);
        if (mysql_num_rows($D009) == 0) {
            return "Produto não encontrado";
        }
        
        $cClausula = " UPDATE D069 SET ";
        $cClausula .= " D069_D009_Id='{$D009_Id}' ";
        $cClausula .= " WHERE D069_Id='{$D069_Id}' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro...: $cErro<br />$cClausula";
        }
        
        $sql    = mysql_query("SELECT D070_D024_Id, D069_Codigo_Produto FROM D069 LEFT JOIN D070 on D070_Id=D069_D070_Id WHERE D069_Id='{$D069_Id}'");
        $result = mysql_fetch_array($sql);
        
        /*
        $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente FROM D083 WHERE D083_D001_Id='{$mD009['D001_Id']}' AND D083_D049_Id='{$mD009['D049_Id']}' AND D083_D024_Id='{$result['D070_D024_Id']}' AND D083_Codigo_Produto_Fornecedor!='' AND D083_Codigo_Produto_Fornecedor IS NOT NULL");
        if (mysql_num_rows($D083) <= 0) {
            $sql = mysqli_query("INSERT INTO D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mD009['D049_Id']}','{$mD009['D001_Id']}','{$result['D070_D024_Id']}',curdate(),'S','{$result['D069_Codigo_Produto']}')");
        }
        */
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->definirCodigoFornecedor($mD009['D009_Id'], $result['D070_D024_Id'], $result['D069_Codigo_Produto'], '', true, false);
        return true;
    }
    
    /**
     * atualizarAliquotasProdutos
     *
     * @param String $extra
     * @return String/bool
     */ 
    public function atualizarAliquotasProdutos($D069_Id, $opcoes)
    {
        require_once("bibliotecas/classes/EST015.php");
        $EST015 = new EST015();
        require_once("bibliotecas/classes/FIS003.php");
        $FIS003 = new FIS003();
        global $g;

        $usuarioAtualizou = array('usuario' => $g['usuarioAtual'].'-'.substr($g['C007']['C007_Primeiro_Nome'],0,20), 'dataHora' => date("Y-m-d H:i:s"));
        $camposAtualizados = serialize(array_merge($opcoes, $usuarioAtualizou));
        
        $sqlD069 = mysql_query("SELECT D069_D009_Id,
                                       D069_Id,
                                       D070_D024_Id,
                                       D070_Id,
                                       D070_Flag_Tabela_Propria,
                                       D070_Flag_Codigos_Comuns,
                                       D069_Codigo_Produto,
                                       D070_D082_Id,
                                       D070_C004_Id,
                                       D069_NCM,
                                       IFNULL(D069_D005_Id,0) AS D069_D005_Id,
                                       D069_Aliquota_IPI,
                                       D024_Situacao_Tributaria_PIS_COFINS,
                                       D024_Flag_Isento_PIS_COFINS,
                                       D069_Codigo_Barras,
                                       D069_Flag_Origem_Produto,
                                       D070_Flag_Vincula_Item_Pre_Cadastro 
                                  FROM D070 
                                LEFT JOIN D069 ON D069_D070_Id = D070_Id
                                LEFT JOIN D024 ON D024_Id = D070_D024_Id 
                                LEFT JOIN D009 ON D009_Id = D069_D009_Id    
                                LEFT JOIN D049 ON D049_Id = D009_D049_Id
                                LEFT JOIN D001 ON D001_Id = D049_D001_Id
                                LEFT JOIN D005 ON D005_Id = D001_D005_Id
                                WHERE D069_Id='{$D069_Id}'");
        $error   = mysql_error();
        
        if (!empty($error)) {
            return 'sqlD069: ' . $error;
        }
        
        $mD069 = mysql_fetch_array($sqlD069);
        
        $D009_Id       = 0;
        $count         = 0;
        $ultimoD024_Id = 0;
        $C004_Id       = ($mD069['D070_C004_Id'] > 0) ? $mD069['D070_C004_Id'] : $g['empresaAtual'];

        if($ultimoD024_Id != $mD069['D070_D024_Id']){
            $sqlD024 = mysql_query("SELECT D024_Cnpj FROM D024 WHERE D024_Id = '{$mD069['D070_D024_Id']}'");
            $rowD024 = mysql_fetch_assoc($sqlD024);
            $cnpjFornecedor = substr($rowD024['D024_Cnpj'], 0, 10);
            $ultimoD024_Id = $mD069['D070_D024_Id'];
        }

        $clasulaPreCadastro = "";
        if($mD069['D070_Flag_Vincula_Item_Pre_Cadastro'] == 'N'){            
            $clasulaPreCadastro = "AND D001_Flag_Pre_Cadastro = 'N'";
        }

        //Primeira verificação são as referencias no cadastro do produto
        $D083 = mysql_query("SELECT D009_Id,
                                    D001_D005_Id 
                               FROM D083 
                          LEFT JOIN D024 ON D024_Id = D083_D024_Id 
                          LEFT JOIN D001 ON D001_Id=D083_D001_Id 
                          LEFT JOIN D049 ON D049_D001_Id=D001_Id 
                          LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                              WHERE D083_Codigo_Produto_Fornecedor='{$mD069['D069_Codigo_Produto']}'
                                AND (D083_D024_Id = '{$mD069['D070_D024_Id']}' OR D024_Cnpj LIKE '{$cnpjFornecedor}%')
                                AND D009_C004_Id  = '{$C004_Id}'
                                    $clasulaPreCadastro");
        $mD083 = mysql_fetch_array($D083);
 
        if ($mD083['D009_Id']>0){
            $D009_Id = $mD083['D009_Id'];
            $D005_Id = $mD083['D001_D005_Id'];
        } else {
            //Segunda verificação é feita com o cadastro de produtos (caso seja uma tabela própria)
            $D001 = mysql_query("SELECT D009_Id,
                                        D001_D005_Id 
                                   FROM D001 
                              LEFT JOIN D049 ON D049_D001_Id=D001_Id 
                              LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                                  WHERE D001_Codigo_Produto='{$mD069['D069_Codigo_Produto']}' 
                                    AND D009_C004_Id = '{$C004_Id}'
                                        $clasulaPreCadastro");
            $mD001 = mysql_fetch_array($D001);

            if ($mD001['D009_Id'] > 0 && $mD069['D070_Flag_Codigos_Comuns'] == 'S'){
                $D009_Id=$mD001['D009_Id'];
                $D005_Id=$mD001['D001_D005_Id'];
            } else {
                //Terceira verificação é feita com a referencia do produto (verifica o campo "Referência" do cadastro do produto)
                $D001 = mysql_query("SELECT D009_Id,
                                            D001_D005_Id 
                                       FROM D001 
                                  LEFT JOIN D083 ON D083_D001_Id = D001_Id 
                                  LEFT JOIN D049 ON D049_D001_Id = D001_Id 
                                  LEFT JOIN D009 on D009_D049_Id = D049_Id 
                                      WHERE D001_Aplicacao = '{$mD069['D069_Codigo_Produto']}' 
                                        AND D049_D082_Id   = '{$mD069['D070_D082_Id']}' 
                                        AND (D083_D024_Id  = '{$mD069['D070_D024_Id']}' OR D049_D024_Id='{$mD069['D070_D024_Id']}') 
                                        AND D009_C004_Id   = '{$C004_Id}' 
                                            $clasulaPreCadastro");
                $mD001 = mysql_fetch_array($D001);

                $D009_Id = $mD001['D009_Id'];
                $D005_Id = $mD001['D001_D005_Id'];
            }
        }

        if($mD069['D069_D009_Id'] > 0 && $mD069['D070_Flag_Codigos_Comuns']=='N'){
            $retorno = $this->definirCodigoFornecedor($mD069['D069_D009_Id'], $mD069['D070_D024_Id'], $mD069['D069_Codigo_Produto'], $mD069['D069_Codigo_Barras']);
            if($retorno !== true){
                return '.'.$retorno;
            }
        }

        /*
        [campoNCM] => true
        [campoOrigemProduto] => false
        [campoIPI] => true
        [campoICMS] => false
        [campoICMSST] => true
        [campoCFOP] => true
        [campoNossoCodigo] => true
        
        SE CAMPONCM = false 
           SE D069_D005_iD branco
             SE CAMPOIPI = FALSE
                BUSCAR D005 ONDE D005_Classificacao_Fiscal = D059_NCM
             SE NÃO
                BUSCAR D005 ONDE (D005_Classificacao_Fiscal = D069_NCM AND D005_IPI = D069_IPI) OR (D005_Classificacao_Fiscal = D069_NCM)
           SE NÃO
                D005_ID = D069_D005_iD
           FIM
        Fim
        */
        $mD069['D069_NCM'] = str_replace(array(' ', '.'), '', $mD069['D069_NCM']);
        if($opcoes['campoNCM'] == 'false'){
            if($mD069['D069_D005_Id'] <= 0){
                if($opcoes['campoIPI'] == 'false'){
                    $D005 = mysql_query("SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='{$mD069['D069_NCM']}'");
                } else {
                    $D005 = mysql_query("SELECT D005_Id FROM D005 LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id = '{$C004_Id}' WHERE D005_Classificacao_Fiscal='{$mD069['D069_NCM']}' AND D149_Aliquota_IPI = '{$mD069['D069_Aliquota_IPI']}'");
                    if(mysql_num_rows($D005) <= 0){
                        $D005 = mysql_query("SELECT D005_Id FROM D005 LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id = '{$C004_Id}' WHERE D005_Classificacao_Fiscal='{$mD069['D069_NCM']}'");
                    }
                }
                $mD005 = mysql_fetch_array($D005);
                $D005_Id = $mD005['D005_Id'];
            } else {
                $D005_Id = $mD069['D069_D005_Id'];
            }
        }

        $demonstrativoNCM = $FIS003->demonstrativoNCM($D005_Id);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

        $update = "UPDATE D069 ";
        $update .= "LEFT JOIN D009 on D009_Id='$D009_Id' ";
        $update .= "LEFT JOIN D049 on D049_Id=D009_D049_Id ";
        $update .= "LEFT JOIN D005 on D005_Id='$D005_Id' ";
        $update .= "LEFT JOIN D001 on D001_Id=D049_D001_Id ";
        $update .= "LEFT JOIN D037 on D037_Id=D001_D037_Id ";
        $update .= "SET ";
        $update .= "D069_Campos_Atualizados='{$camposAtualizados}' ";
        if(!empty($D005_Id)){
            $update .= ",D069_D005_Id = D005_Id, ";
            $update .= "D069_D005_Id_Info = '{$demonstrativoNCM}', ";
            $update .= "D069_NCM = D005_Classificacao_Fiscal ";
        }
        if(!empty($D009_Id)){                
            $update .= ",D069_Descricao_Produto = D001_Descricao_Produto, ";
            $update .= "D069_Peso_Unitario = D001_Peso_Unitario_Kg, ";
            $update .= "D069_D037_Id = D037_Id, ";
            $update .= "D069_Unidade = D037_Unidade ";
        }
        if($opcoes['campoNossoCodigo'] == 'true'){
            $update .= ",D069_D009_Id = D009_Id ";
            $mD069['D069_D009_Id'] = $D009_Id;
        }
        if($opcoes['campoOrigemProduto'] == 'true'){
            $update .= ",D069_Flag_Origem_Produto=D049_Origem_Mercadoria ";
        }
        $update .= "WHERE D069_Id='{$mD069['D069_Id']}'";
        mysql_query($update);

        if($opcoes['campoCFOP'] == 'true'){
            $D006_Id = $EST015->retornaCFOP($mD069['D070_D024_Id'], $mD069['D069_D009_Id'], $C004_Id, $mD069['D069_Flag_Origem_Produto'], false, $mD069['D069_Id']);
            $demonstrativoCFOP = $EST015->retornaCFOP($mD069['D070_D024_Id'], $mD069['D069_D009_Id'], $C004_Id, $mD069['D069_Flag_Origem_Produto'], true, $mD069['D069_Id']);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            mysql_query("UPDATE D069 SET D069_D006_Id = '{$D006_Id}', D069_D006_Id_Info = '{$demonstrativoCFOP}' WHERE D069_Id = '{$mD069['D069_Id']}'");
        }
        
        $Aliquotas = $EST015->retornaAliquotas($mD069['D069_D009_Id'], false, false, false, false, false, false, $mD069['D070_Id'], $mD069['D069_Id']);
        
        $cClausula = " UPDATE D069 ";
        $cClausula .= " LEFT JOIN D006 on D006_Id=D069_D006_Id ";
        $cClausula .= " LEFT JOIN D059 on D059_Id=D006_D059_Id ";
        $cClausula .= " LEFT JOIN C004 on C004_Id='{$C004_Id}' ";
        $cClausula .= " LEFT JOIN D005 on D005_Id=D069_D005_Id ";
        $cClausula .= " LEFT JOIN D149 on D005_Id=D149_D005_Id AND D149_C004_Id=C004_Id";
        $cClausula .= " SET ";
        //$cClausula.=" D069_Flag_Origem_Produto='{$Aliquotas['Origem_Mercadoria']}',";
        if($opcoes['campoIPI'] == 'true'){
            $cClausula .= " D069_Aliquota_IPI='{$Aliquotas['Aliquota_IPI']}',";
            $cClausula .= " D069_Percentual_Reducao_IPI='{$Aliquotas['Percentual_Reducao_IPI']}',";
            $cClausula .= " D069_Situacao_Tributaria_IPI='{$Aliquotas['Situacao_Tributaria_IPI']}', ";
        }
        if($opcoes['campoICMS'] == 'true'){
            $cClausula .= " D069_Aliquota_ICMS='{$Aliquotas['Aliquota_ICMS']}',";
            $cClausula .= " D069_Percentual_Reducao_ICMS='{$Aliquotas['Percentual_Reducao_ICMS']}',";
            $cClausula .= " D069_D059_Id='{$Aliquotas['D059_Id']}', ";
        }
        if($opcoes['campoICMSST'] == 'true'){
            $cClausula .= " D069_Percentual_Reducao_ICMS_ST='{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
            $cClausula .= " D069_Flag_ST=D006_Flag_Substituicao_Tributaria,";
            $cClausula .= " D069_ST_VA='{$Aliquotas['ST_VA']}', ";
            $cClausula .= " D069_ICMS_Inter_Estadual='{$Aliquotas['ICMS_Inter_Estadual']}',";
            $cClausula .= " D069_ICMS_Inter_Estadual_MVA='{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
            $cClausula .= " D069_ST_ICMS_Interno_MVA='{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        }
        if (!empty($mD069['D024_Situacao_Tributaria_PIS_COFINS'])) {
            $cClausula .= " D069_Situacao_Tributaria_PIS='{$mD069['D024_Situacao_Tributaria_PIS_COFINS']}',";
            $cClausula .= " D069_Situacao_Tributaria_COFINS='{$mD069['D024_Situacao_Tributaria_PIS_COFINS']}',";
            if ($mD069['D024_Flag_Isento_PIS_COFINS'] == 'S') {
                $cClausula .= " D069_Flag_Isento_PIS='{$mD069['D024_Flag_Isento_PIS_COFINS']}',";
                $cClausula .= " D069_Aliquota_PIS='0',";
                $cClausula .= " D069_Flag_Isento_COFINS='{$mD069['D024_Flag_Isento_PIS_COFINS']}',";
                $cClausula .= " D069_Aliquota_COFINS='0',";
            } else {
                $cClausula .= " D069_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
                $cClausula .= " D069_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
                $cClausula .= " D069_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
                $cClausula .= " D069_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
            }
        } else {
            $cClausula .= " D069_Flag_Isento_PIS=D006_Flag_Isento_PIS,";
            $cClausula .= " D069_Aliquota_PIS=if(D149_Aliquota_PIS>0,D149_Aliquota_PIS,C004_Percentual_PIS),";
            $cClausula .= " D069_Situacao_Tributaria_PIS=D006_Situacao_Tributaria_PIS,";
            $cClausula .= " D069_Flag_Isento_COFINS=D006_Flag_Isento_COFINS,";
            $cClausula .= " D069_Aliquota_COFINS=if(D149_Aliquota_COFINS>0,D149_Aliquota_COFINS,C004_Percentual_COFINS),";
            $cClausula .= " D069_Situacao_Tributaria_COFINS=D006_Situacao_Tributaria_COFINS, ";
        }
        $cClausula .= " D069_ST_ICMS_Interno='{$Aliquotas['ST_ICMS_Interno']}',";
        $cClausula .= " D069_Flag_Situacao_Tributaria='{$Aliquotas['Flag_Situacao_Tributaria']}', ";            
        $cClausula .= " D069_Percentual_Trib_Transparencia='{$Aliquotas['Percentual_Trib_Transparencia']}' ";
        $cClausula .= " WHERE D069_Id='{$mD069['D069_Id']}'";
        mysql_query($cClausula);

        $error = mysql_error();
        
        if (!empty($error)) {
            return 'Id: ' . $D069_Id . 'Código: ' . $mD069['D069_Codigo_Produto'] . ' ' . $error;
        }

        $D069A = mysql_query("SELECT D069A_Id FROM D069A WHERE D069A_D069_Id='{$D069_Id}'");
        if (mysql_num_rows($D069A) > 0) {
            if($opcoes['campoICMSST'] == 'true'){
                $cClausulaD069A = " UPDATE D069A ";
                $cClausulaD069A .= " SET ";
                $cClausulaD069A .= " D069A_Percentual_Reducao_ICMS_IE_ST  = '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}', ";
                $cClausulaD069A .= " D069A_Percentual_Reducao_ICMS_INT_ST = '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}' ";
                $cClausulaD069A .= " WHERE D069A_D069_Id='{$D069_Id}'";
            }

        } else {
            $cClausulaD069A = " INSERT INTO D069A (";
            if($opcoes['campoICMSST'] == 'true'){
                $cClausulaD069A .= " D069A_Percentual_Reducao_ICMS_IE_ST,";
                $cClausulaD069A .= " D069A_Percentual_Reducao_ICMS_INT_ST,";
            }
            $cClausulaD069A .= " D069A_D069_Id";
            $cClausulaD069A .= ") VALUES (";
            if($opcoes['campoICMSST'] == 'true'){
                $cClausulaD069A .= " '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}',";
                $cClausulaD069A .= " '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}',";
            }
            $cClausulaD069A .= " '{$D069_Id}'";
            $cClausulaD069A .= ")";
        }
        mysql_query($cClausulaD069A);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item da tabela D069A ID D069_Id '$D069_Id': $erro";
        }   
        
        //mysql_query("call D069_Gravar_Totalizacao('{$D069['D069_Id']}')");
        return true;
    }

    // ***
    // Estoque líquido por locação (reserva de locação)
    // ***
    public function processaEstoqueLiquidoPorLocacao($D009_Id) 
    {

        $T006 = mysql_query("SELECT T006_Id,
                                    T006_Quantidade,
                                    T006_Quantidade_Estoque,
                                    T006_Flag_Reservar_Estoque,
                                    T005_Flag_Notificacao_Estoque,
                                    T006_T005_Id,
                                    T006_D009_Id,
                                    T004A_T066_Id,
                                    T005_Id_Pedido_Externo,
                                    D022_Nome_Empresa
                               FROM T006 
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id
                          LEFT JOIN D006 ON D006_Id=T006_D006_Id
                          LEFT JOIN T004 ON T004_Id=T006_T004_Id
                          LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                          LEFT JOIN D022 ON T005_D022_Id = D022_Id
                          LEFT JOIN T005A ON T005_Id = T005A_T005_Id
                              WHERE T006_D009_Id='{$D009_Id}'
                                AND ifnull(T006_T075_Id,0) <= 0
                                AND ((T006_Flag_Status!='3'
                                AND T005_Flag_Status!='4'
                                AND T005_Flag_Status!='5'
                                AND T005_Flag_Status!='7'
                                AND T005_Flag_Status!='11'
                                AND T005_Flag_Status!='8'
                                AND T005_Flag_Status!='26')
                                 OR T005_Flag_Status='')
                                AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                AND IFNULL(T005A_Flag_Tipo,1) != 3
                           ORDER BY T006_Prioridade DESC, T006_Id ASC");

        $estoquePedido = $Saldo;
        while ($mT006 = mysql_fetch_array($T006)) {
            $resT066 = mysql_query("SELECT T066_Id, 
                                           T066_Quantidade_Estoque, 
                                           D004_Local,
                                           D004_Indicador_Propriedade, 
                                           (T066_Quantidade_Estoque-D009_Quantidade_Pedido_Por_Locacao(T066_D009_Id, T066_Id)) as EstoqueLiquido
                                      FROM D009
                                 LEFT JOIN T066 ON T066_D009_Id=D009_Id
                                 LEFT JOIN D004 ON D004_Id=T066_D004_Id
                                     WHERE D009_Id = '{$mT006['T006_D009_Id']}'
                                  ORDER BY T066_Quantidade_Estoque DESC");
            $T066_Id        = 0;
            $T066_Id_Local  = 0;
            $locacaoLoja    = 0;
            $locacaoExcecao = false;
            while ($rowT066 = mysql_fetch_assoc($resT066)) {
                if ($rowT066['D004_Local'] == 'LOJA') {
                    // Caso tenha apenas uma locação no produto que se chama loja, grava ela
                    $locacaoLoja = $rowT066['T066_Id'];
                }

                if ($rowT066['D004_Local'] != 'LOJA') {
                    $T066_Id_Local = $rowT066['T066_Id'];
                }

                if (!$T066_Id && $rowT066['D004_Local'] != 'LOJA') {
                    $T066_Id = $rowT066['T066_Id'];
                }

                if ($rowT066['EstoqueLiquido'] == $mT006['T006_Quantidade'] && $rowT066['D004_Local'] != 'LOJA' && !$locacaoExcecao) {
                    $T066_Id = $rowT066['T066_Id'];
                }
                
                //Se for selecionado a locação no item do orçamento, utiliza ela no pedido
                if($mT006['T004A_T066_Id'] == $rowT066['T066_Id'] && $rowT066['D004_Local'] != 'LOJA' && !$locacaoExcecao){
                    $T066_Id = $mT006['T004A_T066_Id'];
                }

                $locacaoExcecao = $this->excecaoLocacao($rowT066, $mT006);
                if ($locacaoExcecao AND !isset($excecaoDefinida))
                {
                    $excecaoDefinida = true;                    
                    $T066_Id = $rowT066['T066_Id'];
                }                       
            }

            if (!$T066_Id) {
                $T066_Id_Local = ($T066_Id_Local <= 0) ? $locacaoLoja : $T066_Id_Local;
                $T066_Id       = $T066_Id_Local;
            }
            
            // T006_T066_Id
            mysqli_query("UPDATE T006 
                             SET T006_T066_Id = '{$T066_Id}'
                           WHERE T006_Id = '{$mT006['T006_Id']}'");

        }

        // T066_Quantidade_Estoque_Liquido
        mysqli_query("UPDATE T066 
                         SET T066_Quantidade_Estoque_Liquido = (T066_Quantidade_Estoque-D009_Quantidade_Pedido_Por_Locacao(T066_D009_Id, T066_Id))
                       WHERE T066_D009_Id = '{$D009_Id}'");

    }

    function excecaoLocacao($rowT066, $mT006)
    {                
        return false;
    }     

    /**
     * reprocessaOCPedidos
     *
     * @param int $D009_Id
     * @return true
     */
    public function reprocessaOCPedidos($D009_Id) 
    {
        global $g;

        $sqlD009 = mysql_query("SELECT D009_Quantidade_OC_3(D009_Id,0) as D009_Quantidade_OC FROM D009 WHERE D009_Id = '{$D009_Id}'");
        $mD009 = mysql_fetch_array($sqlD009);
        $ocTotal = $mD009['D009_Quantidade_OC'];

        $T006 = mysql_query("SELECT T006_Id,
                                      T006_Quantidade,
                                      T006_Quantidade_Estoque,
                                      T006_Flag_Reservar_Estoque,
                                      T005_Flag_Notificacao_Estoque,
                                      T006_T005_Id,
                                      T006_D009_Id,
                                      T005_Flag_Lista_Preco,
                                      T005_D024_Id,
                                      T005_Flag_ACP,
                                      T006_D006_Id,
                                      T006_Aliquota_ICMS,
                                      T006_Quantidade_OC
                                 FROM T006 
                            LEFT JOIN T005 on T005_Id=T006_T005_Id
                            LEFT JOIN D006 on D006_Id=T006_D006_Id
                            LEFT JOIN T005A ON T005_Id=T005A_T005_Id
                                WHERE T006_D009_Id='{$D009_Id}'
                                  AND ifnull(T006_T075_Id,0) <= 0
                                  AND ifnull(T006_T006_Id_Dividir,0)<=0
                                  AND T006_Quantidade>T006_Quantidade_Estoque
                                  AND ((T006_Flag_Status!='3'
                                  AND T005_Flag_Status!='4'
                                  AND T005_Flag_Status!='5'
                                  AND T005_Flag_Status!='7'
                                  AND T005_Flag_Status!='11'
                                  AND T005_Flag_Status!='8'
                                  AND T005_Flag_Status!='26')
                                   OR T005_Flag_Status='')
                                  AND D006_Flag_Estoque='D'
                                  AND ifnull(T005A_Flag_Tipo,1) !='2'
                                  AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                             ORDER BY T006_Prioridade DESC, T006_Id ASC");

        //vai usar para chamar o T005_Gravar_Totalizacao
        $mAtualizarT005 = array();

        while ($mT006 = mysql_fetch_array($T006)) {
            
            $T005_Id = $mT006['T006_T005_Id'];

            $dividido = mysql_query("SELECT T006_Id,
                                            T006_Quantidade,
                                            T006_Quantidade_Estoque,
                                            T006_Flag_Reservar_Estoque,
                                            T006_Quantidade_OC
                                        FROM T006
                                   LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                   LEFT JOIN D006 ON D006_Id=T006_D006_Id
                                   LEFT JOIN T005A ON T005_Id=T005A_T005_Id
                                       WHERE T006_T006_Id_Dividir='{$mT006['T006_Id']}'
                                         AND ifnull(T006_T075_Id,0) <= 0
                                         AND T006_Quantidade>T006_Quantidade_Estoque
                                         AND ((T006_Flag_Status!='3'
                                         AND T005_Flag_Status!='4'
                                         AND T005_Flag_Status!='5'
                                         AND T005_Flag_Status!='7'
                                         AND T005_Flag_Status!='11'
                                         AND T005_Flag_Status!='8'
                                         AND T005_Flag_Status!='26'
                                         AND D006_Flag_Estoque!='N')
                                          OR T005_Flag_Status='')
                                         AND ifnull(T005A_Flag_Tipo,1) !='2'
                                         AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                    ORDER BY T006_Prioridade DESC, T006_Id ASC");
                                    
            while ($mDividido = mysql_fetch_array($dividido)) {
                
                $oc = $ocTotal;

                if (gCorrigeNumero($mDividido['T006_Quantidade_OC'],5) !== gCorrigeNumero($oc,5)) {
                    mysqli_query("UPDATE T006 SET T006_Quantidade_OC='{$oc}' WHERE T006_Id='{$mDividido['T006_Id']}'");
                    $mAtualizarT005[] = $T005_Id;
                }

                $ocTotal -= ($mDividido['T006_Quantidade_Estoque']>0) ? $mDividido['T006_Quantidade'] - $mDividido['T006_Quantidade_Estoque'] : $mDividido['T006_Quantidade'];

                if ($ocTotal < 0) {
                    $ocTotal = 0;
                }
            }
            
            $oc = $ocTotal;
            if (gCorrigeNumero($mT006['T006_Quantidade_OC'],5) !== gCorrigeNumero($oc,5)) {
                mysqli_query("UPDATE T006 SET T006_Quantidade_OC='{$oc}' WHERE T006_Id='{$mT006['T006_Id']}'");
                $mAtualizarT005[] = $T005_Id;
            }
            $ocTotal -= ($mT006['T006_Quantidade_Estoque']>0) ? $mT006['T006_Quantidade'] - $mT006['T006_Quantidade_Estoque'] : $mT006['T006_Quantidade'];
            if ($ocTotal < 0) {
                $ocTotal = 0;
            }
        }

        foreach (array_unique($mAtualizarT005) as $key) {
            mysqli_query("call T005_Gravar_Totalizacao_4('{$key}')");
        }

        mysql_free_result($T006);
        mysql_free_result($dividido);

        return true;
    }

    /**
     * reprocessaOPPedidos
     *
     * @param int $D009_Id
     * @return true
     */
    public function reprocessaOPPedidos($D009_Id) 
    {
        global $g;

        $sqlD009 = mysql_query("SELECT D009_Quantidade_OP(D009_Id) as D009_Quantidade_OP FROM D009 WHERE D009_Id = '{$D009_Id}'");
        $mD009 = mysql_fetch_array($sqlD009);
        $opTotal = $mD009['D009_Quantidade_OP'];

        $T006 = mysql_query("SELECT T006_Id,
                                      T006_Quantidade,
                                      T006_Quantidade_Estoque,
                                      T006_Flag_Reservar_Estoque,
                                      T005_Flag_Notificacao_Estoque,
                                      T006_T005_Id,
                                      T006_D009_Id,
                                      T005_Flag_Lista_Preco,
                                      T005_D024_Id,
                                      T005_Flag_ACP,
                                      T006_D006_Id,
                                      T006_Aliquota_ICMS
                                 FROM T006 
                            LEFT JOIN T005 on T005_Id=T006_T005_Id
                            LEFT JOIN D006 on D006_Id=T006_D006_Id
                                WHERE T006_D009_Id='{$D009_Id}'
                                     AND ifnull(T006_T075_Id,0) <= 0
                                     AND ifnull(T006_T006_Id_Dividir,0)<=0
                                     AND T006_Quantidade>T006_Quantidade_Estoque
                                     AND ((T006_Flag_Status!='3'
                                     AND T005_Flag_Status!='4'
                                     AND T005_Flag_Status!='5'
                                     AND T005_Flag_Status!='7'
                                     AND T005_Flag_Status!='11'
                                     AND T005_Flag_Status!='8'
                                     AND T005_Flag_Status!='26'
                                     AND D006_Flag_Estoque!='N')
                                     OR T005_Flag_Status='')
                                     AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                             ORDER BY T006_Prioridade DESC, T006_Id ASC");
        while ($mT006 = mysql_fetch_array($T006)) {
            $dividido = mysql_query("SELECT T006_Id,
                                            T006_Quantidade,
                                            T006_Quantidade_Estoque,
                                            T006_Flag_Reservar_Estoque
                                        FROM T006
                                        LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                        LEFT JOIN D006 ON D006_Id=T006_D006_Id
                                        WHERE T006_T006_Id_Dividir='{$mT006['T006_Id']}'
                                            AND ifnull(T006_T075_Id,0) <= 0
                                            AND T006_Quantidade>T006_Quantidade_Estoque
                                            AND ((T006_Flag_Status!='3'
                                            AND T005_Flag_Status!='4'
                                            AND T005_Flag_Status!='5'
                                            AND T005_Flag_Status!='7'
                                            AND T005_Flag_Status!='11'
                                            AND T005_Flag_Status!='8'
                                            AND T005_Flag_Status!='26'
                                            AND D006_Flag_Estoque!='N')
                                            OR T005_Flag_Status='')
                                            AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                        ORDER BY T006_Prioridade DESC, T006_Id ASC");
            while ($mDividido = mysql_fetch_array($dividido)) {
                $oc = $opTotal;
                mysqli_query("UPDATE T006 SET T006_Quantidade_OP='$oc' WHERE T006_Id='{$mDividido['T006_Id']}'");
                $opTotal -= ($mDividido['T006_Quantidade_Estoque']>0) ? $mDividido['T006_Quantidade'] - $mDividido['T006_Quantidade_Estoque'] : $mDividido['T006_Quantidade'];
                if ($opTotal < 0) {
                    $opTotal = 0;
                }
            }
            
            $oc = $opTotal;
            mysqli_query("UPDATE T006 SET T006_Quantidade_OP='$oc' WHERE T006_Id='{$mT006['T006_Id']}'");
            $opTotal -= ($mT006['T006_Quantidade_Estoque']>0) ? $mT006['T006_Quantidade'] - $mT006['T006_Quantidade_Estoque'] : $mT006['T006_Quantidade'];
            if ($opTotal < 0) {
                $opTotal = 0;
            }
        }

        mysql_free_result($sqlD009);
        mysql_free_result($T006);
        mysql_free_result($dividido);

        return true;
    }

    public function definirCodigoFornecedor($D009_Id, $D083_D024_Id, $D083_Codigo_Produto_Fornecedor = '', $D083_Codigo_Barras = '', $Cliente = false, $Fornecedor = false) {
        global $g;
        // Pegar IDs

        $sqlD009 = <<<EOT
            SELECT D001_Id,
                   D049_Id
              FROM D009
         LEFT JOIN D049 ON D049_Id=D009_D049_Id
         LEFT JOIN D001 ON D001_Id=D049_D001_Id
             WHERE D009_Id = '{$D009_Id}'
EOT;
        $resD009 = mysql_query($sqlD009);
        $rowD009 = mysql_fetch_assoc($resD009);

        if(empty($rowD009['D001_Id'])){
            return false;
        }
        
        // Verificar se já existe
        $sql = "SELECT D083_Id, 
                       D083_D024_Id
                  FROM D083 
                 WHERE D083_D001_Id = '{$rowD009['D001_Id']}'
                   AND D083_D024_Id = '{$D083_D024_Id}'";

        $res = mysql_query($sql);

       
        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_assoc($res);

            $updateD083 = [];
            if ($D083_Codigo_Produto_Fornecedor) {
                $updateD083[] = "D083_Codigo_Produto_Fornecedor = '{$D083_Codigo_Produto_Fornecedor}'";
            }
            
            if ($D083_Codigo_Barras) {            
                $updateD083[] = "D083_Codigo_Barras = '{$D083_Codigo_Barras}'";
            }
            
            if ($Cliente) {
                $updateD083[] = "D083_Flag_Cliente = 'S'";
            }
            
            if ($Fornecedor) {
                $updateD083[] = "D083_Flag_Fornecedor = 'S'";
            }
            
            if (!empty($updateD083)) {
                $updateQuery = "UPDATE D083 SET " . implode(', ', $updateD083) . " WHERE D083_Id = '{$row['D083_Id']}'";
                mysql_query($updateQuery);
                
            }

        } else {
            
            $Cliente    = $Cliente ? 'S' : 'N';
            $Fornecedor = $Fornecedor ? 'S' : 'N';
            $restricao  = $g['conf']->restricaoIncluirUpdate('D083_Codigo_Barras', $D083_Codigo_Barras, array('D083_D024_Id' => $D083_D024_Id));

            if($restricao !== true){
                return $restricao;
            }

            if($D083_Codigo_Produto_Fornecedor || $D083_Codigo_Barras){
                
                $sql = <<<EOT
                INSERT INTO D083 (D083_D049_Id,
                                  D083_D001_Id,
                                  D083_D024_Id,
                                  D083_Data_Cadastro,
                                  D083_Flag_Cliente,
                                  D083_Flag_Fornecedor,
                                  D083_Codigo_Produto_Fornecedor,
                                  D083_Codigo_Barras)
                           VALUES
                                 ('{$rowD009['D049_Id']}',
                                 '{$rowD009['D001_Id']}',
                                 '{$D083_D024_Id}',
                                 curdate(),
                                 '{$Cliente}',
                                 '{$Fornecedor}',
                                 '{$D083_Codigo_Produto_Fornecedor}',
                                 '{$D083_Codigo_Barras}')
EOT;
                mysql_query($sql);
            }
        }
        return true;
    }

    public function obterNovoCodigoEAN() {
        $sql = <<<EOT
            SELECT 
                D001_Codigo_Barras
            FROM D001
            WHERE
                D001_Codigo_Barras REGEXP '^[0-9]{6}$'
            ORDER BY D001_Codigo_Barras DESC
            LIMIT 1
EOT;
        $res = mysql_query($sql);
        if (!mysql_numrows($res)) {
            return '000001';
        } else {
            $row = mysql_fetch_array($res);
            $EAN = ltrim($row['D001_Codigo_Barras'], '0');
            $EAN++;
            return str_pad($EAN, 6, '0', STR_PAD_LEFT);
        }
    }

    public function obterNovoCodigoProduto($D001_Id = false, $segundaSeq = false, $preCadastro = false) {
        global $g;
        $resultado = $this->obterNovoCodigoProdutoSmart($D001_Id, $segundaSeq, $preCadastro);

        if(!empty($resultado)){
            return $resultado;
        }
        // Mascara (passar para o painel de configuração)
        if ($preCadastro) {
            $mascara = $g['C031']['mascaraCodigoProdutoPreCadastro'];
        } else {
            $mascara = $g['C031']['mascaraCodigoProduto'];
        }

        // $segundaSeq é utilizado quando tem 2 sequencias no código
        preg_match_all('#SEQ#', $mascara, $matches);
        if ($segundaSeq && count($matches[0]) < 2) {
            return false;
        }

        // Campos para dar o replace
        $sql = <<<EOT
            SELECT
                D001_C008_Id,
                D001_D060_Id,
                D001_D003_Id,
                D001_D002_Id
            FROM D001
            WHERE
                D001_Id = '{$D001_Id}'
EOT;
        $res = mysql_query($sql);

        $resD049 = mysql_query("SELECT D001_C008_Id,
                                       D001_D060_Id,
                                       D001_D003_Id,
                                       D001_D002_Id,
                                       D049_Flag_Tipo
                                   FROM D001
                                   LEFT JOIN D049 ON D049_D001_Id=D001_Id
                                   WHERE D049_D001_Id = '{$D001_Id}'");
        //Verifica se o produto já possui D049
        if(mysql_num_rows($resD049) > 0){
            $row = mysql_fetch_assoc($resD049);
        } else {
            $row = mysql_fetch_assoc($res);
        }

        // Monta o novo código
        $codigo = '';
        // Quebra todas as partes da máscara
        preg_match_all('#(\[[^\]]+\]+|[^\[]+)#', $mascara, $matches);
        foreach ($matches[0] as $key => $value) {
            if(preg_match('#^\[IF\(.+\)\]#', $value)){
                // Encontra as partes da sequência [IF(CONDICAO;VALOR1;VALOR2)]
                preg_match('#^\[IF\((.+);(.+);(.+)\)\]#', $value, $seqMatches);

                foreach ($row as $campo => $valor) {
                    log($campo . ' ' . $valor);
                    $check = strpos($seqMatches[1], $campo);
                    if($check !== false){
                        $seqMatches[1] = str_replace($campo, $valor, $seqMatches[1]);
                    }
                }
                foreach ($row as $campo => $valor) {
                    $check = strpos($seqMatches[2], $campo);
                    if($check !== false){
                        $seqMatches[2] = str_replace($campo, $valor, $seqMatches[2]);
                    }
                }
                foreach ($row as $campo => $valor) {
                    $check = strpos($seqMatches[3], $campo);
                    if($check !== false){
                        $seqMatches[3] = str_replace($campo, $valor, $seqMatches[3]);
                    }
                }
                $seqMatches[2] = str_replace("'", "", $seqMatches[2]);
                $seqMatches[3] = str_replace("'", "", $seqMatches[3]);
                //Executa o IF da mascara e popula a variavel retornoIF com um booleano para usar na sequencia
                $retornoIF = eval("return ({$seqMatches[1]}) ? '0' : '1';");
                if($retornoIF == '0') {
                    $codigo .= $seqMatches[2];
                    $strComplementar = $seqMatches[3];
                } else {
                    $codigo .= $seqMatches[3];
                    $strComplementar = $seqMatches[2];
                }
            } else if ($value[0] == '[' && !preg_match('#^\[SEQ#', $value)) {
                // Encontra as partes do campo [NOME|NÚMERO]
                preg_match('#^\[([^\|]+)(\|([0-9]))?\]#', $value, $campoMatches);
                $campo = $campoMatches[1];
                $campo = $row[$campo];
                if (empty($campo)) {
                    return false;
                }
                if (isset($campoMatches[3])) {
                    $campo = str_pad($campo, $campoMatches[3], '0', STR_PAD_LEFT);
                }
                $codigo .= $campo;
            } else if (preg_match('#^\[SEQ#', $value)) {
                // Encontra as partes da sequência [SEQ|NÚMERO]
                preg_match('#^\[SEQ\|([0-9]+)\]#', $value, $seqMatches);
                $nrSeq = false;
                if (isset($seqMatches[1])) {
                    $nrSeq = $seqMatches[1];
                }

                // Número procura o número sequencial atual no banco
                if ($segundaSeq) {
                    $sql = "SELECT D001_Codigo_Produto FROM D001 WHERE D001_Id = '{$D001_Id}'";
                    log("Código Produto 1: ".$sql);
                } else {
                    if(!empty($codigo)){
                        $sql = "SELECT D001_Codigo_Produto FROM D001 WHERE D001_Codigo_Produto LIKE '{$codigo}%' ORDER BY D001_Codigo_Produto DESC LIMIT 1";
                        log("Código Produto 2: ".$sql);
                    } if(isset($strComplementar)) {
                        $strlenComplementar = strlen($strComplementar);
                        $sql = "SELECT D001_Codigo_Produto FROM D001 WHERE IFNULL(D001_Codigo_Produto,'') != '' AND SUBSTRING(D001_Codigo_Produto, 1, {$strlenComplementar}) != '{$strComplementar}' ORDER BY D001_Codigo_Produto DESC LIMIT 1";
                        log("Código Produto 3: ".$sql);
                    } else {
                        $sql = "SELECT D001_Codigo_Produto FROM D001 WHERE IFNULL(D001_Codigo_Produto,'') != '' ORDER BY CAST(D001_Codigo_Produto AS UNSIGNED) DESC LIMIT 1";
                        log("Código Produto 3: ".$sql);
                    }
                }

                $resSeq = mysql_query($sql);
                if (mysql_num_rows($resSeq) > 0) {
                    $rowSeq = mysql_fetch_assoc($resSeq);
                    $proximoNumero = $rowSeq['D001_Codigo_Produto'];
                    $proximoNumero = preg_replace('#^' . preg_quote($codigo) . '#', '', $proximoNumero);

                    if ($nrSeq) {
                        if(empty($codigo) && isset($strComplementar)){
                            $valorMascara = str_replace("'", "", $strComplementar);
                            $valorMascara = strlen($valorMascara);
                            $nrSeq = $nrSeq + $valorMascara;
                        }
                        $proximoNumero = substr($proximoNumero, 0, $nrSeq);
                    }
                    $proximoNumero = ltrim($proximoNumero, '0');

                    if (empty($proximoNumero)) {
                        return false;
                    }
                    if ($segundaSeq) {
                        $segundaSeq = false;
                    } else {
                        $proximoNumero++;
                    }
                } else {
                    if ($segundaSeq) {
                        return false;
                    }
                    if(empty($codigo) && isset($strComplementar)){
                        $valorMascara = str_replace("'", "", $strComplementar);
                        $valorMascara = strlen($valorMascara);
                        $nrSeq = $nrSeq + $valorMascara;
                    }
                    $proximoNumero = 1;
                }
                $codigo .= $nrSeq ? str_pad($proximoNumero, $nrSeq, '0', STR_PAD_LEFT) : $proximoNumero;
            } else {
                // Qualquer outro texto
                $codigo .= $value;
            }
        }
        return $codigo;
    }

    /**
     * cad002RetornaPrecoVendaTabelaFornecedor
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $Flag_ACP
     * @param String $cfop
     * @param String $Aliquota_ICMS
     * @return array
     */     
    public function cad002RetornaPrecoVendaTabelaFornecedor($D069_Id, $listaPreco = null, $D024_Id = null, $Flag_ACP = null, $cfop = null, $Aliquota_ICMS = 0, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0,$TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0)
    {
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }
        
        $demostrativo = array();
        
        // Cliente/Fornecedor
        $sql   = "SELECT * FROM D024 WHERE D024_Id='{$D024_Id}'";
        $D024  = mysql_query($sql);
        $mD024 = mysql_fetch_array($D024);

        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024
        if ($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        // Produto
        $sql   = "SELECT * FROM D069 LEFT JOIN D009 ON D069_Id=D009_D069_Id WHERE D069_Id='{$D069_Id}'";
        $D069  = mysql_query($sql);
        $mD069 = mysql_fetch_array($D069);
        log("SQL D069: ".$sql);
        // CFOP
        $sql   = "SELECT * FROM D006 WHERE D006_Id='{$cfop}'";
        $D006  = mysql_query($sql);
        $mD006 = mysql_fetch_array($D006);
        log("SQL D006: ".$sql);

        $demostrativo[] = array(
            'campo' => '',
            'valor' => '',
            'operacao' => ''
        );
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        $preco          = $mD069['D069_Valor_Custo_Unitario'];
        $demostrativo[] = array(
            'campo' => 'Custo Compra ' . $obsCusto,
            'valor' => $mD069['D069_Valor_Custo_Unitario'],
            'operacao' => '='
        );
        log('1 '. $preco);
        
        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S') {
            $custoST      = $mD069['D069_Valor_ICMS_Substituicao_Retencao'];
            $percentualST = number_format($mD069['D069_Percentual_ST'], 5, ',', '.');
        }
        $preco -= $custoST;
        $demostrativo[] = array(
            'campo' => $percentualST . '% ST',
            'valor' => $custoST,
            'operacao' => '-'
        );
        log('2: '. $preco."- Custo ST: ".$custoST);
        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            $icmsCompra   = $mD069['D069_Valor_ICMS'];
            $aliquotaICMS = number_format($mD069['D069_Aliquota_ICMS'], 2, ',', '.');
        }
        
        $preco -= $icmsCompra;
        $demostrativo[] = array(
            'campo' => $aliquotaICMS . '% ICMS compra',
            'valor' => $icmsCompra,
            'operacao' => '-'
        );
        log('3 '. $preco ."- ICMS Compra: ".$icmsCompra);
        // ICF
        $ICF = 1;
        if ($mD069['D009_ICF_' . $listaPreco] > 0) {
            $ICF = $mD069['D009_ICF_' . $listaPreco];
        } else if ($g['C004']['C004_ICF_Padrao'] > 0) {
            $ICF = $g['C004']['C004_ICF_Padrao'];
        }
        $preco *= $ICF;
        
        $demostrativo[] = array(
            'campo' => 'ICF',
            'valor' => $ICF,
            'operacao' => '*'
        );


        // IPV
        $IPV = 0;
        if ($mD024['D024_IPV_1'] > 0) {
            $IPV = $mD024['D024_IPV_1'];
        } else if ($mD069['D009_IPV_' . $listaPreco] > 0) {
            $IPV = $mD069['D009_IPV_' . $listaPreco];
        } else if ($g['C004']['C004_IPV_Padrao'] > 0) {
            $IPV = $g['C004']['C004_IPV_Padrao'];
        }
        $preco *= $IPV;
        log('4 '. $preco.' * IPV: '.$IPV);
        
        $demostrativo[] = array(
            'campo' => 'IPV',
            'valor' => $IPV,
            'operacao' => '*'
        );
        
        // ICMS
        $icmsVenda = 0;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $porcentagem = 100 / (100 - $Aliquota_ICMS);
            $icmsVenda   = ($preco * $porcentagem) - $preco;
        }
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        
        
        $preco += $icmsVenda;
        $demostrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => $icmsVenda,
            'operacao' => '+',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );
        log('5 '. $preco . " + ICMS Venda: ".$Aliquota_ICMS);

         // PIS
        $pisVenda = 0;
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $porcentagem = 100 / (100 - $Aliquota_PIS);
            $pisVenda   = ($preco * $porcentagem) - $preco;
        }
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        
        
        $preco += $pisVenda;
        $demostrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => $pisVenda,
            'operacao' => '+',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );
        log('6 '. $preco . " + PIS Venda: ".$pisVenda);

         // COFINS
        $cofinsVenda = 0;
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $porcentagem = 100 / (100 - $Aliquota_COFINS);
            $cofinsVenda   = ($preco * $porcentagem) - $preco;
        }
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        
        
        $preco += $cofinsVenda;
        $demostrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => $cofinsVenda,
            'operacao' => '+',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );
        log('7 '. $preco. " + COFINS Venda: ".$cofinsVenda);

        // IPI
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $ipiVenda = 0;
            $porcentagem = 100 / (100 - $Aliquota_IPI);
            $ipiVenda   = ($preco * $porcentagem) - $preco;
            if ($Aliquota_IPI > 0) {
                //$preco += $ipiVenda;
            }
            $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
            
            $demostrativo[] = array(
                'campo' => $Aliquota_IPI . '% IPI venda',
                'valor' => $ipiVenda,
                'operacao' => '+',
                'exibir' => 'N'
            );
        }
        $preco = number_format($preco, 4, ',', '.');
        return array(
            $preco,
            $demostrativo
        );
    }

     /**
     * cad002RetornaPrecoTabela
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $Flag_ACP
     * @param String $cfop
     * @param String $Aliquota_ICMS   
     * @return array
     */     
    public function cad002RetornaPrecoTabela($D009_Id, $listaPreco = null, $D024_Id = null, $Flag_ACP = null, $cfop = null, $Aliquota_ICMS = 0, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0,$TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0, $Aliquota_IRPJ = 0, $Aliquota_CSLL = 0, $C004_Id = false)
    {
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }
        
        $demonstrativo = array();

        // se receber o C004_Id (exemplo na troca de empresas) usa o C004_Id, caso não usa o empresaAtual
        $C004_Id = ($C004_Id === false) ? $g['empresaAtual'] : $C004_Id;

        if($g['empresaAtual'] != $C004_Id){
            log("entrou no select do C031 tabela fornecedor");
            // no caso de troca de empresa, faz o select nas globais para ver quais impostos somam no preço de venda
            $C031 = mysql_query("SELECT C031_Valor, 
                                        C031_Campo 
                                   FROM C031 
                                  WHERE C031_C004_Id = '{$C004_Id}'
                                    AND (C031_Campo  = 'somarICMSPrecoVenda'   OR
                                        C031_Campo   = 'somarPISPrecoVenda'	   OR
                                        C031_Campo   = 'somarCOFINSPrecoVenda' OR
                                        C031_Campo   = 'somarIRPJPrecoVenda'   OR
                                        C031_Campo   = 'somarCSLLPrecoVenda')");

            while($mC031 = mysql_fetch_assoc($C031)){
                $g['C031'][$mC031['C031_Campo']] = $mC031['C031_Valor'];
            } 
        }      
        
        // Cliente/Fornecedor
        $sql   = <<<EOT
            SELECT
              *
            FROM D024
            WHERE D024_Id='{$D024_Id}'
EOT;
        $D024  = mysql_query($sql);
        $mD024 = mysql_fetch_array($D024);
        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024
        if ($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        // Produto
        $sql   = <<<EOT
            SELECT 
                * 
            FROM D009 
            JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D069 ON D069_Id=D009_D069_Id
            LEFT JOIN D006 ON D006_Id=D069_D006_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE D009_Id='{$D009_Id}'
EOT;
        $D009  = mysql_query($sql);     

        $mD009 = mysql_fetch_array($D009);

        // So montar preco e demonstrativo se tiver tabela
        if (empty($mD009['D069_Id'])) {
            return array(
                0,
                array()
            );
        }
        
        // CFOP
        $sql   = <<<EOT
            SELECT
              *
            FROM D006
            WHERE D006_Id='{$cfop}'
EOT;
        $D006  = mysql_query($sql);
        $mD006 = mysql_fetch_array($D006);
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        $preco           = $mD009['D009_Valor_Custo_Tabela'];
        $demonstrativo[] = array(
            'campo'    => 'Custo Compra ' . $obsCusto,
            'valor'    => $mD009['D009_Valor_Custo_Tabela'],
            'operacao' => '='
        );
        
        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S' && $mD009['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $custoST      = $mD009['D069_Valor_ICMS_Substituicao_Retencao'];
            $percentualST = number_format($mD009['D069_Percentual_ST'], 5, ',', '.');
        }
        $preco -= $custoST;
        $demonstrativo[] = array(
            'campo'    => $percentualST . '% ST',
            'valor'    => $custoST,
            'operacao' => '-'
        );
        
        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            $icmsCompra   = $mD009['D069_Valor_ICMS'];
            $aliquotaICMS = number_format($mD009['D069_Aliquota_ICMS'], 2, ',', '.');
        }
        
        $preco -= $icmsCompra;
        $demonstrativo[] = array(
            'campo'    => $aliquotaICMS . '% ICMS compra',
            'valor'    => $icmsCompra,
            'operacao' => '-'
        );

        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S') {
            $pisCompra   = $mD009['D069_Valor_PIS'];
            $aliquotaPIS = number_format($mD009['D069_Aliquota_PIS'], 2, ',', '.');
        }
        
        $preco -= $pisCompra;
        $demonstrativo[] = array(
            'campo'    => $aliquotaPIS . '% PIS compra',
            'valor'    => $pisCompra,
            'operacao' => '-'
        );

        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S') {
            $cofinsCompra   = $mD009['D069_Valor_COFINS'];
            $aliquotaCOFINS = number_format($mD009['D069_Aliquota_COFINS'], 2, ',', '.');
        }
        
        $preco -= $cofinsCompra;
        $demonstrativo[] = array(
            'campo'    => $aliquotaCOFINS . '% COFINS compra',
            'valor'    => $cofinsCompra,
            'operacao' => '-'
        );
        
        // ICF
        $ICF = 1;
        if ($mD009['D009_ICF_1'] > 0) {
            $ICF = $mD009['D009_ICF_1'];
        }
        
        if ($TotalPorcentagemICF>0 || $TotalPorcentagemICF<0){
            //**********************************************
            // Atualiza IPV com novas porcentagens digitadas na tela de montagem de ICF
            //**********************************************
            
            //Pega o valor original do IPV e transforma novamente em uma porcentagem
            $PorcentagemICF   = ceil(((100*$ICF)-100)/$ICF);
       
            //Soma a porcentagem original com a digitada na tela de montagem de IPV
            $PorcentagemTotal = $PorcentagemICF+$TotalPorcentagemICF;

            //Transforma tudo novamente em um IPV
            $ICF = (100/(100-$PorcentagemTotal)); 

            //**************************************************
        }

        // D082_ICF
        // C008_ICF
        $preco *= $ICF;
        
        $demonstrativo[] = array(
            'campo'    => 'ICF',
            'valor'    => $ICF,
            'operacao' => '*'
        );

        $demonstrativo[] = array(
            'campo'    => 'Custo Líquido',
            'valor'    => $preco,
            'operacao' => '<b>=</b>'
        );

        // IPV
        $IPV = 0;
        if ($mD024['D024_IPV_1'] > 0) {
            $IPV = $mD024['D024_IPV_1'];
        } else if ($mD009['D009_IPV_' . $listaPreco] > 0) {
            $IPV = $mD009['D009_IPV_' . $listaPreco];
        } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
            switch ($listaPreco) {
                case 1:
                    $IPV = $g['C020']['C020_IPV_Revenda'];
                    break;
                case 2:
                    $IPV = $g['C020']['C020_IPV_Atacado'];
                    break;
                case 3:
                    $IPV = $g['C020']['C020_IPV_Varejo'];
                    break;
                    if ($IPV <= 0) {
                        $IPV = 1;
                    }
            }
        }

        if ($TotalPorcentagemIPV>0) {
            //**********************************************
            // Atualiza IPV com novas porcentagens digitadas na tela de montagem de IPV
            //**********************************************

            //Pega o valor original do IPV e transforma novamente em uma porcentagem
            $PorcentagemIPV   = (((100*$IPV)-100)/$IPV);
       
            //Soma a porcentagem original com a digitada na tela de montagem de IPV,

            $PorcentagemTotal = $PorcentagemIPV+$TotalPorcentagemIPV;

            //Transforma tudo novamente em um IPV
            $IPV = (100/(100-$PorcentagemTotal)); 
            //***********************************************
        }

        // Total aliquotas para projetar preço de venda
        $totalAliquotas = 0;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_ICMS;
        }
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_PIS;
        }
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_COFINS;
        }
        if ($Aliquota_IRPJ > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_IRPJ;
        }
        if ($Aliquota_CSLL > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_CSLL;
        }

        $totalAliquotas = 100 / ( 100 - $totalAliquotas);
        
        $preco *= $IPV;
        
        $demonstrativo[] = array(
            'campo'    => 'IPV',
            'valor'    => $IPV,
            'operacao' => '*'
        );

        $precoBaseImpostos = $preco * $totalAliquotas;

        // ICMS
        $icmsVenda       = 0;
        $porcentagem     = $Aliquota_ICMS / 100;        
        $baseCalculoICMS = ($mD006['D006_Percentual_Reducao_ICMS'] > 0) ? ((100 - $mD006['D006_Percentual_Reducao_ICMS']) / 100) * $precoBaseImpostos : $precoBaseImpostos;
        $icmsVenda       = $baseCalculoICMS * $porcentagem;

        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $preco += $icmsVenda;
        }

        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        $demonstrativo[] = array(
            'campo'    => $Aliquota_ICMS . '% ICMS venda',
            'valor'    => $icmsVenda,
            'operacao' => ($g['C031']['somarICMSPrecoVenda'] == 'S') ? '+' : '',
            'exibir'   => $g['C031']['somarICMSPrecoVenda']
        );
        // FIM ICMS

        // PIS
        $pisVenda    = 0;
        $porcentagem = $Aliquota_PIS / 100;
        $pisVenda    = $precoBaseImpostos * $porcentagem;

        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $preco += $pisVenda;
        }

        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        $demonstrativo[] = array(
            'campo'    => $Aliquota_PIS . '% PIS venda',
            'valor'    => $pisVenda,
            'operacao' => ($g['C031']['somarPISPrecoVenda'] == 'S') ? '+' : '',
            'exibir'   => $g['C031']['somarPISPrecoVenda']
        );
        // FIM PIS

        // COFINS
        $cofinsVenda   = 0;
        $porcentagem   = $Aliquota_COFINS / 100;
        $cofinsVenda   = $precoBaseImpostos * $porcentagem;

        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $preco += $cofinsVenda;
        }

        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        $demonstrativo[] = array(
            'campo'    => $Aliquota_COFINS . '% COFINS venda',
            'valor'    => $cofinsVenda,
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir'   => $g['C031']['somarCOFINSPrecoVenda']
        );
        // FIM COFINS

        // IRPJ
        $irpjVenda   = 0;
        $porcentagem = $Aliquota_IRPJ / 100;
        $irpjVenda   = $precoBaseImpostos * $porcentagem;

        if ($Aliquota_IRPJ > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') {
            $preco += $irpjVenda;
        }

        $Aliquota_IRPJ = number_format($Aliquota_IRPJ, 2, ',', '.');
        $demonstrativo[] = array(
            'campo'    => $Aliquota_IRPJ . '% IRPJ venda',
            'valor'    => $irpjVenda,
            'operacao' => ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? '+' : '',
            'exibir'   => $g['C031']['somarIRPJPrecoVenda']
        );
        // FIM IRPJ

        // CSLL
        $csllVenda   = 0;
        $porcentagem = $Aliquota_CSLL / 100;
        $csllVenda   = $precoBaseImpostos * $porcentagem;

        if ($Aliquota_CSLL > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') {
            $preco += $csllVenda;
        }

        $Aliquota_CSLL = number_format($Aliquota_CSLL, 2, ',', '.');
        $demonstrativo[] = array(
            'campo'    => $Aliquota_CSLL . '% CSLL venda',
            'valor'    => $csllVenda,
            'operacao' => ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? '+' : '',
            'exibir'   => $g['C031']['somarCSLLPrecoVenda']
        );
        // FIM CSLL

        // IPI
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $ipiVenda    = 0;
            $porcentagem = 100 / (100 - $Aliquota_IPI);
            $ipiVenda    = ($preco * $porcentagem) - $preco;
            if ($Aliquota_IPI > 0) {
                //$preco += $ipiVenda;
            }
            
            $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
            
            $demonstrativo[] = array(
                'campo'    => $Aliquota_IPI . '% IPI venda',
                'valor'    => $ipiVenda,
                'operacao' => '+',
                'exibir'   => 'N'
            );
        }
        
        $preco = number_format($preco, 4, ',', '.');        
        //Produtos em pré cadastro retornar valor zero
        //if($mD009['D001_Flag_Pre_Cadastro'] == 'S'){
        //    return array(0, '');   
        //}
        return array(
            $preco,
            $demonstrativo
        );
    }

     /**
     * cad002RetornaPrecoUltimaCompra
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $Flag_ACP
     * @param String $cfop
     * @param String $Aliquota_ICMS
     * @return array
     */     
    public function cad002RetornaPrecoUltimaCompra($D009_Id, $listaPreco = null, $D024_Id = null, $Flag_ACP = null, $cfop = null, $Aliquota_ICMS = 0, $casasDecimaisPreco = 4, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0)
    {
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }
        
        log('cfop ' . $cfop);
        $demostrativo = array();
        
        // Cliente/Fornecedor
        $sql   = <<<EOT
            SELECT
              *
            FROM D024
            WHERE D024_Id='{$D024_Id}'
EOT;
        $D024  = mysql_query($sql);
        $mD024 = mysql_fetch_array($D024);
        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024
        if ($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        // Produto
        $sql   = <<<EOT
            SELECT 
                * 
            FROM D009 
            JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D006 ON D006_Id=T001_D006_Id
            LEFT JOIN D069 ON D069_Id=D009_D069_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE D009_Id='{$D009_Id}'
EOT;
        $D009  = mysql_query($sql);
        $mD009 = mysql_fetch_array($D009);
        

        // CFOP
        $sql   = <<<EOT
            SELECT
              *
            FROM D006
            WHERE D006_Id='{$cfop}'
EOT;
        $D006  = mysql_query($sql);
        $mD006 = mysql_fetch_array($D006);
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        if($mD009['D009_Valor_Preco_Tabela'] > 0){
            $preco = $mD009['D009_Valor_Custo_Compra'];
        } else if($mD009['D009_Flag_Custo_Utilizado'] == 'M'){
            $preco = $mD009['D009_Valor_Custo_Medio_Unitario'];
        } else {
            $preco = $mD009['D009_Valor_Custo_Ultima_Compra'];
        }
        $demostrativo[] = array(
            'campo' => 'Custo Compra ' . $obsCusto,
            'valor' => $mD009['D009_Valor_Custo_Ultima_Compra'],
            'operacao' => '='
        );
        
        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S' && $mD009['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $custoST      = $mD009['T001_Valor_ST'];
            $percentualST = number_format($mD009['T001_Percentual_ST'], 5, ',', '.');
        }
        $preco -= $custoST;
        $demostrativo[] = array(
            'campo' => $percentualST . '% ST',
            'valor' => $custoST,
            'operacao' => '-'
        );
        
        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            $icmsCompra   = $mD009['T001_Valor_ICMS'];
            $aliquotaICMS = number_format($mD009['T001_Aliquota_ICMS'], 2, ',', '.');
        }
        
        $preco -= $icmsCompra;
        log('2 ICMS COMPRA: ' . $icmsCompra . ' - ' . $aliquotaICMS);
        $demostrativo[] = array(
            'campo' => $aliquotaICMS . '% ICMS compra',
            'valor' => $icmsCompra,
            'operacao' => '-'
        );

        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S') {
            $pisCompra   = $mD009['T001_Valor_PIS'];
            $aliquotaPIS = number_format($mD009['T001_Aliquota_PIS'], 2, ',', '.');
        }
        
        $preco -= $pisCompra;
        log('3 PIS COMPRA: ' . $pisCompra . ' - ' . $aliquotaPIS);
        $demostrativo[] = array(
            'campo' => $aliquotaPIS . '% PIS compra',
            'valor' => $pisCompra,
            'operacao' => '-'
        );

        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S') {
            $cofinsCompra   = $mD009['T001_Valor_COFINS'];
            $aliquotaCOFINS = number_format($mD009['T001_Aliquota_COFINS'], 2, ',', '.');
        }
        
        $preco -= $cofinsCompra;
        log('2 COFINS COMPRA: ' . $cofinsCompra . ' - ' . $aliquotaCOFINS);
        $demostrativo[] = array(
            'campo' => $aliquotaCOFINS . '% COFINS compra',
            'valor' => $cofinsCompra,
            'operacao' => '-'
        );
        
        // ICF
        $ICF = 1;
        if ($mD009['D009_ICF_1'] > 0) {
            $ICF = $mD009['D009_ICF_1'];
        }
        // D082_ICF
        // C008_ICF
        $preco *= $ICF;
        
        $demostrativo[] = array(
            'campo' => 'ICF',
            'valor' => $ICF,
            'operacao' => '*'
        );

        $demostrativo[] = array(
            'campo' => 'Custo Líquido',
            'valor' => $preco,
            'operacao' => '<b>=</b>'
        );
        // IPV
        $IPV = 0;
        if ($mD024['D024_IPV_1'] > 0) {
            $IPV = $mD024['D024_IPV_1'];
        } else if ($mD009['D009_IPV_' . $listaPreco] > 0) {
            $IPV = $mD009['D009_IPV_' . $listaPreco];
        } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
            switch ($listaPreco) {
                case 1:
                    $IPV = $g['C020']['C020_IPV_Revenda'];
                    break;
                case 2:
                    $IPV = $g['C020']['C020_IPV_Atacado'];
                    break;
                case 3:
                    $IPV = $g['C020']['C020_IPV_Varejo'];
                    break;
                    if ($IPV <= 0) {
                        $IPV = 1;
                    }
            }
        }
        $preco *= $IPV;
        
        $demostrativo[] = array(
            'campo' => 'IPV',
            'valor' => $IPV,
            'operacao' => '*'
        );


        // ICMS
        $icmsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_ICMS);
        $icmsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $preco += $icmsVenda;
        }        
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        $demostrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => $icmsVenda,
            'operacao' => ($g['C031']['somarICMSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );
        

        // PIS
        $pisVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_PIS);
        $pisVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $preco += $pisVenda;
        }        
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        $demostrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => $pisVenda,
            'operacao' => ($g['C031']['somarPISPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_COFINS);
        $cofinsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $preco += $cofinsVenda;
        }
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');        
        $demostrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => $cofinsVenda,
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );

        // IPI
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $ipiVenda = 0;
            $porcentagem = 100 / (100 - $Aliquota_IPI);
            $ipiVenda   = ($preco * $porcentagem) - $preco;
            if ($Aliquota_IPI > 0) {
                //$preco += $ipiVenda;
            }
            $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
            
            $demostrativo[] = array(
                'campo' => $Aliquota_IPI . '% IPI venda',
                'valor' => $ipiVenda,
                'operacao' => '+',
                'exibir' => 'N'
            );
        }

        $preco = number_format($preco, $casasDecimaisPreco, ',', '.');
        //Produtos em pré cadastro retornar valor zero
        //if($mD009['D001_Flag_Pre_Cadastro'] == 'S'){
        //    return array(0, '');   
        //}
        return array(
            $preco,
            $demostrativo
        );
    }

    /**
     * cadastrarFornecedorProduto
     * @param int   $D001_Id
     * @param int   $D024_Id
     * @return true/erro(string)
     */
    public function cadastrarFornecedorProduto($D001_Id, $D024_Id)
    {
        global $g;
        mysql_query("INSERT INTO T241 (T241_D001_Id, T241_D024_Id, T241_Data) VALUES ('{$D001_Id}', '{$D024_Id}', CURDATE())");
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro: " . $erro;
        }
        return true;
    }

    /**
     * gravarPercentualAcrescimoCusto
     * @param int   $D001_Id
     * @param int   $D049_Percentual_Acrescimo_Custo
     * @return true/erro(string)
     */
    public function gravarPercentualAcrescimoCusto($D001_Id, $D049_Percentual_Acrescimo_Custo)
    {
        global $g;
        mysql_query("UPDATE D049 SET D049_Percentual_Acrescimo_Custo = '{$D049_Percentual_Acrescimo_Custo}' WHERE D049_D001_Id = '{$D001_Id}'");
        $erro = mysql_error();
        if(!empty($erro)){
            return "Erro: " . $erro;
        }
        return true;
    }

    /**
     * reprocessaEstoqueSimilares
     * @param int   $D009_Id
     * @return padrao similares (string)
     */
    public function reprocessaEstoqueSimilares($D001_Id, $D001_Codigo_Produto)
    {
        global $g;
        $mascara = $g['C031']['mascaraCodigoProdutoSimilares'];
        $mEstoqueSimilar = array();

        if(!empty($mascara)){
            // Caso possua mascara da grade configurada busca o total do estoque por grade
            $padraoGrade = "";
            preg_match_all('#(\[[^\]]+\]+|[^\[]{1})#', $mascara, $matches);
            foreach ($matches[0] as $key => $value) {
                if ($value[0] == '[') {
                    $value = explode(",", trim($value, '[]'));                
                    if(empty($value[1])){
                        $padraoGrade .= substr($D001_Codigo_Produto, $value[0]);
                    } else {
                        $padraoGrade .= substr($D001_Codigo_Produto, $value[0], $value[1]);
                    }
                } else {
                    $padraoGrade .= $value;
                }
            }
            $sqlEstoqueGrade = mysql_query("SELECT D009_Quantidade_Estoque,
                                                   D009_Id
                                              FROM D001 
                                         LEFT JOIN D049 ON D049_D001_Id=D001_Id 
                                         LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                                             WHERE D001_Codigo_Produto LIKE '{$padraoGrade}'
                                               AND D009_C004_Id = '{$g['empresaAtual']}'");
            
            while ($rowEstoqueGrade = mysql_fetch_array($sqlEstoqueGrade)){
                $D009_Id = $rowEstoqueGrade['D009_Id'];
                $mEstoqueSimilar[$D009_Id] = $rowEstoqueGrade['D009_Quantidade_Estoque'];
            }
        }

        // Total estoque dos produtos $D001_Id
        $sqlEstoqueSimilar = mysql_query("SELECT D009_Quantidade_Estoque,
                                                 D009_Id   
                                            FROM D009 
                                       LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                                       LEFT JOIN D001 ON D001_Id = D049_D001_Id 
                                           WHERE D001_Id = '{$D001_Id}'
                                             AND D009_C004_Id = '{$g['empresaAtual']}'");
        while($rowEstoqueSimilar = mysql_fetch_array($sqlEstoqueSimilar)){
            $D009_Id = $rowEstoqueSimilar['D009_Id'];
            $mEstoqueSimilar[$D009_Id] = $rowEstoqueSimilar['D009_Quantidade_Estoque'];
            log("D009_Id " . $D009_Id . " Estoque " . $rowEstoqueSimilar['D009_Quantidade_Estoque']);
        }

        // Total estoque dos produtos D156_D001_Id_1
        $sqlEstoqueSimilar = mysql_query("SELECT D009_Quantidade_Estoque,
                                                 D009_Id   
                                            FROM D009 
                                       LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                                       LEFT JOIN D001 ON D001_Id = D049_D001_Id 
                                       LEFT JOIN D156 ON D156_D001_Id_2=D001_Id
                                           WHERE D156_D001_Id_1 = '{$D001_Id}'
                                             AND D009_C004_Id = '{$g['empresaAtual']}'");
        while($rowEstoqueSimilar = mysql_fetch_array($sqlEstoqueSimilar)){
            $D009_Id = $rowEstoqueSimilar['D009_Id'];
            $mEstoqueSimilar[$D009_Id] = $rowEstoqueSimilar['D009_Quantidade_Estoque'];
            log("D009_Id " . $D009_Id . " Estoque " . $rowEstoqueSimilar['D009_Quantidade_Estoque']);
        }

        // Total estoque dos produtos D156_D001_Id_2
        $sqlEstoqueSimilar = mysql_query("SELECT D009_Quantidade_Estoque,
                                                 D009_Id   
                                            FROM D009 
                                       LEFT JOIN D049 ON D049_Id = D009_D049_Id 
                                       LEFT JOIN D001 ON D001_Id = D049_D001_Id 
                                       LEFT JOIN D156 ON D156_D001_Id_1=D001_Id
                                           WHERE D156_D001_Id_2 = '{$D001_Id}'
                                             AND D009_C004_Id = '{$g['empresaAtual']}'");
        while($rowEstoqueSimilar = mysql_fetch_array($sqlEstoqueSimilar)){
            $D009_Id = $rowEstoqueSimilar['D009_Id'];
            log("D009_Id " . $D009_Id . " Estoque " . $rowEstoqueSimilar['D009_Quantidade_Estoque']);
            $mEstoqueSimilar[$D009_Id] = $rowEstoqueSimilar['D009_Quantidade_Estoque'];
        }

        // totaliza estoque similar e estoque grade
        $totalEstoqueSimilar = array_sum($mEstoqueSimilar);

        // loop atualiza estoque de todos produtos similares
        foreach ($mEstoqueSimilar as $D009_Id => $estoqueD009 ){
            $estoqueSimilar = $totalEstoqueSimilar - $estoqueD009;
            mysql_query("UPDATE D009 SET D009_Quantidade_Estoque_Similar = '{$estoqueSimilar}' WHERE D009_Id = '{$D009_Id}' AND D009_Quantidade_Estoque_Similar!='{$estoqueSimilar}'");
        }

        return $padraoGrade;
    }

    /**
     * atualizarPrecosPorPeso
     * @param int $D009_Id
     * @return bool/string
     */
    public function atualizarPrecosPorPeso($D009_Id)
    {
        if(empty($D009_Id)){
            return "D009_Id não recebido. Por favor, contato o suporte técnico.";
        }

        $D009 = mysql_query("SELECT D049_Id FROM D009
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D041 ON D041_D049_Id=D049_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
        WHERE D009_Id='{$D009_Id}' AND D001_Peso_Unitario_Kg>0;");

        $mD009 = mysql_fetch_array($D009);

        mysql_query("UPDATE D009
                        LEFT JOIN D049 ON D049_Id=D009_D049_Id
                        LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    SET
                        D009_Preco_1=D049_Valor_Preco_KG*D001_Peso_Unitario_Kg,
                        D009_Preco_2=D049_Valor_Preco_KG*D001_Peso_Unitario_Kg,
                        D009_Preco_3=D049_Valor_Preco_KG*D001_Peso_Unitario_Kg
                    WHERE D049_Id='{$mD009['D049_Id']}';");
        $erro = mysql_error();

        if(empty($erro)){
            return true;
        }else{
            return $erro;
        }
    }

    /**
     * processaEstoquePedidos
     * @param int $D009_Id
     * @return bool
     */
    public function processaEstoquePedidos($D009_Id, $Saldo)
    {
        global $g;
        // Reprocessa estoque dos pedidos
        $T006 = mysql_query("SELECT T006_Id,
                                    T006_Quantidade,
                                    T006_Quantidade_Estoque,
                                    T006_Flag_Reservar_Estoque,
                                    T005_Flag_Notificacao_Estoque,
                                    T006_T005_Id,
                                    T006_D009_Id,
                                    D006_Flag_Estoque,
                                    T005_Flag_Status
                               FROM T006 
                          LEFT JOIN D009 ON D009_Id=T006_D009_Id
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id
                          LEFT JOIN D006 ON D006_Id=T006_D006_Id
                          LEFT JOIN T007 ON T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
                          LEFT JOIN T005A ON T005_Id=T005A_T005_Id
                              WHERE T006_D009_Id='{$D009_Id}'
                                AND IFNULL(T006_T075_Id,0) <= 0
                                AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                AND IF(IFNULL(T006_C004_Id_Estoque,0)>0, T006_C004_Id_Estoque, D009_C004_Id) = D009_C004_Id
                                AND (((T005_Flag_Status!='4' OR (T005_Flag_Status='4' AND T005_Flag_Expedicao = '2' AND T007_Id is null))
                                AND T005_Flag_Status!='5'
                                AND T005_Flag_Status!='7'
                                AND T005_Flag_Status!='11'
                                AND T005_Flag_Status!='8'
                                AND T005_Flag_Status!='26')
                                OR T005_Flag_Status='')
                                AND IFNULL(T005A_Flag_Tipo,1) !='2'                                
                          ORDER BY T006_Prioridade DESC, IFNULL(T006_T006_Id_Dividir,0) DESC, T006_Id ASC");
        $estoquePedido = $Saldo;
        while ($mT006 = mysql_fetch_array($T006)) {
                if ($mT006['D006_Flag_Estoque']=='D') {
                    if ($mT006['T005_Flag_Status'] != '3') {
                        if ($estoquePedido >= $mT006['T006_Quantidade'] && $mT006['T006_Quantidade_Estoque'] < $mT006['T006_Quantidade'] && $mT006['T005_Flag_Notificacao_Estoque'] != 'S') {
                            mysqli_query("UPDATE T005 SET T005_Flag_Notificacao_Estoque = 'S' WHERE T005_Id = '{$mT006['T006_T005_Id']}' AND T005_Flag_Notificacao_Estoque != 'S'");
                        }
                        mysqli_query("UPDATE T006 SET T006_Quantidade_Estoque='$estoquePedido' WHERE T006_Id='{$mT006['T006_Id']}'");
                    }
                    $estoquePedido -= $mT006['T006_Quantidade'];
                } else {
                    if ($mT006['T005_Flag_Status'] != '3') {
                        mysqli_query("UPDATE T006 SET T006_Quantidade_Estoque=0 WHERE T006_Id='{$mT006['T006_Id']}' AND T006_Quantidade_Estoque!=0");
                    }
                }
        }

        // Chamando o gravar totalização do T005 manualmente pois a atualização do T006_Quantidade_Estoque influencia campos no T005 que nao estao sendo
        // preenchidos por falta de ter a trigger
        $T006 = mysql_query("SELECT T006_T005_Id
                               FROM T006 
                          LEFT JOIN D009 ON D009_Id=T006_D009_Id
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id
                          LEFT JOIN D006 ON D006_Id=T006_D006_Id
                          LEFT JOIN T007 ON T007_T005_Id=T005_Id and T007_Flag_Cancelada!='S'
                              WHERE T006_D009_Id='{$D009_Id}'
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
                                AND D006_Flag_Estoque!='N'
                          GROUP BY T006_T005_Id");
        while ($mT006 = mysql_fetch_array($T006)) {
            log("CALL T005_Gravar_Totalizacao_4('{$mT006['T006_T005_Id']}')");
            mysqli_query("CALL T005_Gravar_Totalizacao_4('{$mT006['T006_T005_Id']}')");
        }
        return true;
    }

    /**
     * cad002demonstrativoCustoTabela
     * @param int $D009_Id
     * @return string
     */
    public function cad002demonstrativoCustoTabela($D009_Id) 
    {
        global $g;

        $C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004=mysql_fetch_array($C004);

        $cSql_D009 = mysql_query("select *
                                from D009
                           left join D069 on D069_Id=D009_D069_Id
                           left join D070 on D070_Id=D069_D070_Id
                           left join D006 on D006_Id=D069_D006_Id
                           left join D024 on D024_Id=D070_D024_Id
                               where D009_Id='{$D009_Id}' AND D069_Flag_Tabela_Principal='S'");

        // So montar custo e demonstrativo se tiver tabela
        if (mysql_num_rows($cSql_D009) <= 0) {
            return array(
                0,
                array()
            );
        }

        $mD069 = mysql_fetch_array($cSql_D009);

        unset($mDemonstrativo_Custo);
        $mDemonstrativo_Custo[] = " ,,Preço,,".number_format($mD069['D069_Valor_Preco_Original'],5,',','.');
        $Custo_Final=$mD069['D069_Valor_Preco_Original'];
        $mDemonstrativo_Custo[] = "-,,Desconto nos Itens,,".number_format($mD069['D069_Valor_Preco_Original']*($mD069['D069_Percentual_Desconto']/100),5,',','.');
        $Custo_Final-=$mD069['D069_Valor_Preco_Original']*($mD069['D069_Percentual_Desconto']/100);
        $mDemonstrativo_Custo[] = " ,,Preço Liquido,,".number_format($mD069['D069_Valor_Preco_Unitario'],5,',','.');
        //acrescretado IF para a operação nao dar erro (divisao por '0' = "nan")
        if($mD069['D069_Valor_ICMS'] >0 && $mD069['D069_Quantidade']>0 ){
            if ($mC004['C004_Flag_Empresa_Simples']!='1') {
                $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_ICMS']}% ICMS,,".number_format($mD069['D069_Valor_ICMS']/$mD069['D069_Quantidade'],5,',','.');
            }
        }else{
            $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_ICMS']}% ICMS,,".number_format(0,5,',','.');
        }
        if($mD069['D069_Valor_PIS']>0 && $mD069['D069_Quantidade']> 0){
        $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_PIS']}% PIS,,".number_format($mD069['D069_Valor_PIS']/$mD069['D069_Quantidade'],5,',','.');
        }else{
            $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_PIS']}% PIS,,".number_format(0,5,',','.');
        }
        if($mD069['D069_Valor_COFINS']>0 && $mD069['D069_Quantidade']>0){
        $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_COFINS']}% COFINS,,".number_format($mD069['D069_Valor_COFINS']/$mD069['D069_Quantidade'],5,',','.');
        }else{
            $mDemonstrativo_Custo[] = " ,,{$mD069['D069_Aliquota_COFINS']}% COFINS,,".number_format(0,5,',','.');
        }
        if($mD069['D069_Valor_IPI']>0 && $mD069['D069_Quantidade']>0 ){
        $mDemonstrativo_Custo[] = "+,,{$mD069['D069_Aliquota_IPI']}% IPI,,".number_format($mD069['D069_Valor_IPI']/$mD069['D069_Quantidade'],5,',','.');
        $Custo_Final+=$mD069['D069_Valor_IPI']/$mD069['D069_Quantidade'];
        }else{
            $mDemonstrativo_Custo[] = "+,,{$mD069['D069_Aliquota_IPI']}% IPI,,".number_format(0,5,',','.');
            $Custo_Final+=0;
        }

        $mDemonstrativo_Custo[] = "+,,{$mD069['D069_Percentual_ST']}% ST,,".number_format($mD069['D069_Valor_ICMS_Substituicao_Retencao'],5,',','.');
        $Custo_Final+=$mD069['D069_Valor_ICMS_Substituicao_Retencao'];

        $mDemonstrativo_Custo[] = "+,,Frete,,".number_format($mD069['D069_Valor_Frete'],5,',','.');
        $Custo_Final+=$mD069['D069_Valor_Frete'];
        $mDemonstrativo_Custo[] = "+,,Seguro,,".number_format(0,5,',','.');
        $Custo_Final+=0;
        // retirado valor seguro por nao conter valor correspondente na tabela D009
        // $mDemonstrativo_Custo[] = "+,,Seguro,,".number_format($mD069['T014_Valor_Seguro']/$mD069['T014_Quantidade'],5,',','.');
        // $Custo_Final+=round($mD069['T014_Valor_Seguro']/$mD069['T014_Quantidade'],2);
        $mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".number_format(0,5,',','.');
        $Custo_Final+=0;
        // retirado valor despesas acessorias por nao conter valor correspondente na tabela D009
        //$mDemonstrativo_Custo[] = "+,,Despesas Acessórias,,".number_format($mD069['T014_Valor_Despesas_Acessorias']/$mD069['T014_Quantidade'],5,',','.');
        //$Custo_Final+=round($mD069['T014_Valor_Despesas_Acessorias']/$mD069['T014_Quantidade'],2);
        $mDemonstrativo_Custo[] = "=,,<font style='font-weight:bold'>Custo Final,,<font style='font-weight:bold'>".number_format($Custo_Final,5,',','.')."</font>";

        $mD069['D024_Nome_Empresa'] = substr($mD069['D024_Nome_Empresa'], 0, 19);
        $mD069['D006_Descricao'] = substr($mD069['D006_Descricao'],0,20);

        if($mD069['D069_D070_Id'] > 0){
            $descricao = '-'.$mD069['D069_D070_Id'].'-'.$mD069['D024_Nome_Empresa'];
        }
        
        $D070_Validade               = gCorrigeData($mD069['D070_Data_Vencimento']);
        $D069_Data_Atualizacao_Custo = gCorrigeData($mD069['D069_Data_Atualizacao_Custo']);

        $demonstrativo = '';
        $demonstrativo .= "<div style='font-size: 11px; border-bottom:1px solid black; color: black' align='center' class='fonte'><b>TAB{$descricao}</b><br/><font style='color:blue;'>Validade tabela: {$D070_Validade}<br>Preço atualizado em {$D069_Data_Atualizacao_Custo}</font></div>";
        $demonstrativo .= "<table width=100% cellspacing=0 cellpadding=0 align=center>";

        for ($Linha=0;$Linha<count($mDemonstrativo_Custo);$Linha++) {
            $demonstrativo .= "<tr>";
            $mColuna=explode(",,",$mDemonstrativo_Custo[$Linha]);
            for ($Coluna=0;$Coluna<count($mColuna);$Coluna++) {
                $demonstrativo .= "<td style='padding: 2px 3px;'";
                if ($Coluna+1==count($mColuna)) {
                   $demonstrativo .= " align=right";
                }
                $demonstrativo .= ">";
                $demonstrativo .= "<font class=fonte style='font-size:11px'>$mColuna[$Coluna]</font>";
                $demonstrativo .= "</td>";
            }
            if (empty($mDemonstrativo_Custo[$Linha])) {
                $demonstrativo .= "<td><hr></td>";
            }
            $demonstrativo .= "</tr>";
        }
        $demonstrativo .= "</table>";
        return $demonstrativo;
    }

    /**
     * cad002RetornaPrecoCotacao
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $atualizarDadosCompra
     * @param String $cfop
     * @param String $Aliquota_ICMS
     * @param String $T219_Id
     * @param String $T225_Id     
     * @param String $casasDecimaisPreco
     * @param String $Aliquota_PIS
     * @param String $Aliquota_COFINS
     * @param String $Aliquota_IPI
     * @return array
     */     
    public function cad002RetornaPrecoCotacao($D009_Id, $listaPreco = null, $D024_Id = null, $atualizarDadosCompra = false, $cfop = null, $Aliquota_ICMS = 0, $T219_Id = 0, $T225_Id = 0, $casasDecimaisPreco = 4, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0)
    {
        log("cad002RetornaPrecoCotacao: $D009_Id , $listaPreco , $D024_Id , $Flag_ACP , $cfop , $Aliquota_ICMS, $T219_Id , $T225_Id , $casasDecimaisPreco , $Aliquota_PIS , $Aliquota_COFINS, $Aliquota_IPI");
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }
        
        $demostrativo = array();
        $custoMedio = 0;
        $quantidadeEstoque = 0;
        $preco = 0;

        if($T219_Id <= 0 && $T225_Id <= 0){
            return array(
                $preco,
                $demostrativo,
                $custoMedio,
                $quantidadeEstoque
            );
        }
        
        // Cliente/Fornecedor
        $sql   = <<<EOT
            SELECT
              *
            FROM D024
            WHERE D024_Id='{$D024_Id}'
EOT;
        $D024  = mysql_query($sql);
        $mD024 = mysql_fetch_array($D024);
        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024
        if ($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }
        
        // Produto
        $sql   = <<<EOT
            SELECT 
                * 
            FROM D009 
            JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D006 ON D006_Id=T001_D006_Id
            LEFT JOIN D069 ON D069_Id=D009_D069_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE D009_Id='{$D009_Id}'
EOT;
        $D009  = mysql_query($sql);
        $mD009 = mysql_fetch_array($D009);
        $custoCadastroProduto = $mD009['D009_Valor_Custo_Compra'];

        // Usar cotação compra
        if ($T219_Id > 0) {
            $sql   = <<<EOT
                SELECT 
                    T219_Valor_Custo_Unitario as D009_Valor_Custo_Compra,
                    999 as D009_Quantidade_Estoque,
                    T219_Valor_ST/T219_Quantidade as T001_Valor_ST,
                    (round(T219_Valor_ST/T219_Quantidade,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI/T219_Quantidade,2)))*100 as T001_Percentual_ST,
                    T219_Valor_ICMS/T219_Quantidade as T001_Valor_ICMS,
                    T219_Aliquota_ICMS as T001_Aliquota_ICMS,
                    T219_Valor_PIS/T219_Quantidade as T001_Valor_PIS,
                    T219_Aliquota_PIS as T001_Aliquota_PIS,
                    T219_Valor_COFINS/T219_Quantidade as T001_Valor_COFINS,
                    T219_Aliquota_COFINS as T001_Aliquota_COFINS,
                    T219_T218_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T219_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T219_Quantidade_Estoque,
                    T219_Valor_Custo_Medio_Unitario,
                    T006_Valor_Custo_Unitario,
                    T219_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T004_T219_Id,0) AS T004_T219_Id,
                    T219_Valor_Custo_Momento_Compra
                FROM T219
                LEFT JOIN T006 ON T006_Id=T219_T006_Id
                LEFT JOIN T004 ON T004_Id=T219_T004_Id
                LEFT JOIN D006 ON D006_Id=T219_D006_Id
                JOIN D009 ON D009_Id=T219_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T219_Id='{$T219_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Cotação OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T219_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }

                if(($mD009['T006_T219_Id'] > 0 || $mD009['T004_T219_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T219_Valor_Custo_Momento_Compra']*$mD009['T219_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T219_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T219_Quantidade']+$quantidadeEstoque);
            }
            
            $obsCusto = "(Cot {$mD009['T219_T218_Id']})";
        }
        
        // Usar ordem de compra
        if ($T225_Id > 0) {
            $sql   = <<<EOT
                SELECT 
                    T225_Valor_Custo_Unitario AS D009_Valor_Custo_Compra,
                    999 AS D009_Quantidade_Estoque,
                    T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade AS T001_Valor_ST,
                    (ROUND(T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade,2)/(ROUND(T225_Valor_Total_Preco,2)+ROUND(T225_Valor_IPI/T225_Quantidade,2)))*100 AS T001_Percentual_ST,
                    T225_Valor_ICMS/T225_Quantidade AS T001_Valor_ICMS,
                    T225_Aliquota_ICMS AS T001_Aliquota_ICMS,
                    T225_Valor_PIS/T225_Quantidade AS T001_Valor_PIS,
                    T225_Aliquota_PIS AS T001_Aliquota_PIS,
                    T225_Valor_COFINS/T225_Quantidade AS T001_Valor_COFINS,
                    T225_Aliquota_COFINS AS T001_Aliquota_COFINS,
                    T225_T224_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T225_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T225_Quantidade_Estoque,
                    T225_Valor_Custo_Medio_Unitario,
                    T006_Valor_Custo_Unitario,
                    T225_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T006_T225_Id,0) AS T006_T225_Id,
                    T225_Valor_Custo_Momento_Compra
                FROM T225
                LEFT JOIN T006 ON T006_T225_Id=T225_Id OR T006_T219_Id=T225_T219_Id
                LEFT JOIN D006 ON T225_D006_Id=D006_Id
                JOIN D009 ON D009_Id=T225_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T225_Id='{$T225_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Compra OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T225_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }
                if(($mD009['T006_T219_Id'] > 0 || $mD009['T006_T225_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T225_Valor_Custo_Momento_Compra']*$mD009['T225_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T225_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T225_Quantidade']+$quantidadeEstoque);
            }
            
            $obsCusto = "(OC {$mD009['T225_T224_Id']})";
        }
        
        // CFOP
        $D006  = mysql_query("SELECT * FROM D006 WHERE D006_Id='{$cfop}'");
        $mD006 = mysql_fetch_array($D006);
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S' && $quantidadeEstoque > 0 && $custoMedio > 0){
            $mD009['D009_Valor_Custo_Compra'] = $custoMedio;
        }
        $preco = $mD009['D009_Valor_Custo_Compra'];
        
        $demostrativo[] = array(
            'campo' => 'Custo Compra ' . $obsCusto,
            'valor' => $mD009['D009_Valor_Custo_Compra'],
            'operacao' => '='
        );
        

        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S' && $mD009['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $custoST      = $mD009['T001_Valor_ST'];
            $percentualST = number_format($mD009['T001_Percentual_ST'], 5, ',', '.');
        }
        log('1 ST COMPRA: ' . $custoST . ' - ' . $percentualST);

        $preco -= $custoST;
        $demostrativo[] = array(
            'campo' => $percentualST . '% ST',
            'valor' => $custoST,
            'operacao' => '-'
        );


        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            $icmsCompra   = $mD009['T001_Valor_ICMS'];
            $aliquotaICMS = number_format($mD009['T001_Aliquota_ICMS'], 2, ',', '.');
        }

        $preco -= $icmsCompra;
        log('2 ICMS COMPRA: ' . $icmsCompra . ' - ' . $aliquotaICMS);
        $demostrativo[] = array(
            'campo' => $aliquotaICMS . '% ICMS compra',
            'valor' => $icmsCompra,
            'operacao' => '-'
        );


        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S') {
            $pisCompra   = $mD009['T001_Valor_PIS'];
            $aliquotaPIS = number_format($mD009['T001_Aliquota_PIS'], 2, ',', '.');
        }

        $preco -= $pisCompra;
        log('3 PIS COMPRA: ' . $pisCompra . ' - ' . $aliquotaPIS);
        $demostrativo[] = array(
            'campo' => $aliquotaPIS . '% PIS compra',
            'valor' => $pisCompra,
            'operacao' => '-'
        );


        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S') {
            $cofinsCompra   = $mD009['T001_Valor_COFINS'];
            $aliquotaCOFINS = number_format($mD009['T001_Aliquota_COFINS'], 2, ',', '.');
        }

        $preco -= $cofinsCompra;
        log('4 COFINS COMPRA: ' . $cofinsCompra . ' - ' . $aliquotaCOFINS);
        $demostrativo[] = array(
            'campo' => $aliquotaCOFINS . '% COFINS compra',
            'valor' => $cofinsCompra,
            'operacao' => '-'
        );
        

        // ICF
        $ICF = 1;
        if ($mD009['D009_ICF_1'] > 0) {
            $ICF = $mD009['D009_ICF_1'];
        }
        // D082_ICF
        // C008_ICF
        $preco *= $ICF;
        log('5 ICF: ' . $ICF);
        $custoProduto = $preco;
        log('Custo: ' . $custoProduto);
        $demostrativo[] = array(
            'campo' => 'ICF',
            'valor' => $ICF,
            'operacao' => '*'
        );

        $demostrativo[] = array(
            'campo' => 'Custo Líquido',
            'valor' => $custoProduto,
            'operacao' => '<b>=</b>'
        );
        
        // IPV
        if($listaPreco == 'T'){
            //Caso a lista de preço informada seja "T", para uma transferência, o IPV será 1
            $IPV = 1;
        } else {
            $IPV = 0;
            if ($mD024['D024_IPV_1'] > 0) {
                $IPV = $mD024['D024_IPV_1'];
            } else if ($mD009['D009_IPV_' . $listaPreco] > 0) {
                $IPV = $mD009['D009_IPV_' . $listaPreco];
            } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
                switch ($listaPreco) {
                    case 1:
                        $IPV = $g['C020']['C020_IPV_Revenda'];
                        break;
                    case 2:
                        $IPV = $g['C020']['C020_IPV_Atacado'];
                        break;
                    case 3:
                        $IPV = $g['C020']['C020_IPV_Varejo'];
                        break;
                        if ($IPV <= 0) {
                            $IPV = 1;
                        }
                }
            }
        } 
        $preco *= $IPV;
        log('6 IPV: ' . $IPV);
        $demostrativo[] = array(
            'campo' => 'IPV',
            'valor' => $IPV,
            'operacao' => '*'
        );


        // ICMS
        $icmsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_ICMS);
        $icmsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $preco += $icmsVenda;
        }
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        log('7 ICMS VENDA: ' . $icmsVenda . ' - ' . $Aliquota_ICMS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => $icmsVenda,
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_PIS);
        $pisVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $preco += $pisVenda;
        }
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        log('8 PIS VENDA: ' . $pisVenda . ' - ' . $Aliquota_PIS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => $pisVenda,
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_COFINS);
        $cofinsVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $preco += $cofinsVenda;
        }
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        log('9 COFINS VENDA: ' . $cofinsVenda . ' - ' . $Aliquota_COFINS);
        
        $demostrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => $cofinsVenda,
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IPI
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $ipiVenda = 0;
            $porcentagem = 100 / (100 - $Aliquota_IPI);
            $ipiVenda   = ($preco * $porcentagem) - $preco;
            if ($Aliquota_IPI > 0) {
                //$preco += $ipiVenda;
            }
            $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
            log('10 IPI VENDA: ' . $ipiVenda . ' - ' . $Aliquota_IPI);
            
            $demostrativo[] = array(
                'campo' => $Aliquota_IPI . '% IPI venda',
                'valor' => $ipiVenda,
                'operacao' => '+',
                'exibir' => 'N'
            );
        }

        $preco = number_format($preco, $casasDecimaisPreco, ',', '.');
        return array(
            $preco,
            $demostrativo,
            $custoMedio,
            $quantidadeEstoque
        );
    }

    /**
     * atualizarCustoProduto
     *
     * @param String $D009_Id
     * @param array $dadosCusto
     * @return true
     */  
    public function atualizarCustoProduto($D009_Id, $dadosCusto = false)
    {
        global $g;
        $sqlD009 = mysql_query("SELECT  D009_Data_Atualizacao_Preco_Tabela,
                                        D009_Valor_Preco_Tabela,
                                        D009_Valor_Custo_Medio_Unitario,
                                        D009_Valor_Custo_Ultima_Compra,
                                        D009_Quantidade_Estoque,
                                        D009_Quantidade_Estoque_Liquido,
                                        D009_Data_Ultima_Entrada,
                                        D049_Flag_Tipo,
                                        D049_Percentual_Acrescimo_Custo,
                                        D001_Id,
                                        D009_ICF_1,
                                        D009_C004_Id,
                                        D001_Codigo_Produto,
                                        D001_Descricao_Produto,
                                        D009_D069_Id,
                                        D049_Flag_Tipo,
                                        D049_Id,
                                        D009_Id,
                                        D009A_Valor_Custo_Liquido_Ultima_Compra 
                                   FROM D009 
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                              LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                              LEFT JOIN D009A ON D009_Id=D009A_D009_Id
                                  WHERE D009_Id = '{$D009_Id}'");
        $mD009   = mysql_fetch_array($sqlD009);

        $Data_Alteracao_Custo_Manual = $mD009['D009_Data_Atualizacao_Preco_Tabela'];
        $Custo_Manual                = $mD009['D009_Valor_Preco_Tabela'];
        if($dadosCusto !== false){
            //Dados do método D001_reprocessa_historico
            $Custo_Medio           = $dadosCusto['Custo_Medio'];
            $Custo_Compra          = $dadosCusto['Custo_Compra'];
            $Saldo_Regras          = $dadosCusto['Saldo_Regras'];
            $Data_Ultima_Entrada   = $dadosCusto['Data_Ultima_Entrada'];
            $Custo_Liquido_Compra  = $dadosCusto['Custo_Liquido_Compra'];
        } else {
            //Dados do cadastro do produto
            $Custo_Medio           = $mD009['D009_Valor_Custo_Medio_Unitario'];
            $Custo_Compra          = $mD009['D009_Valor_Custo_Ultima_Compra']; 
            $Saldo_Regras          = $mD009['D009_Quantidade_Estoque'];
            $Data_Ultima_Entrada   = $mD009['D009_Data_Ultima_Entrada'];
            $Custo_Liquido_Compra  = $mD009['D009A_Valor_Custo_Liquido_Ultima_Compra'];
        }

        if($g['C031']['estoqueUtilizadoFormarCusto'] == 'L'){
            // Se a configuração do custo é pelo estoque, verifica se usa o líquido
            // Por padrão valida pelo físico
            $Saldo_Regras = $mD009['D009_Quantidade_Estoque_Liquido'];
        }

        // Verifica se a configuração de inclusão automatica de tabela fornecedor, caso não exista um registro e seja automatico, sera cadastro 
        $C031 = mysql_query("select C031_Valor from C031 where C031_Campo='inserirTabelaFornecedorPadrao' and C031_C004_Id='{$mD009['D009_C004_Id']}'");
        $mC031 = mysql_fetch_array($C031);
        $sqlD069 = mysql_query("SELECT D069_Id FROM D069 JOIN D070 ON D070_Id=D069_D070_Id WHERE D069_D009_Id='{$D009_Id}' AND D069_D070_Id='{$mC031['C031_Valor']}'");
        if (mysql_num_rows($sqlD069)<=0){
            $D070 = mysql_query("select D070_Id, D070_D024_Id from D070 where D070_Id='{$mC031['C031_Valor']}'");
            if (mysql_num_rows($D070)>0) {
                $mD070 = mysql_fetch_array($D070);
                $D069 = mysql_query("select D069_Id from D069 where D069_D009_Id='{$D009_Id}' and D069_D070_Id='{$mD070['D070_Id']}'");
                if (mysql_num_rows($D069)<=0){
                    mysql_query("insert into D069 (D069_D024_Id, D069_D070_Id, D069_Codigo_Produto, D069_Descricao_Produto, D069_D009_Id) values ('{$mD070['D070_D024_Id']}','{$mD070['D070_Id']}','{$mD009['D001_Codigo_Produto']}','{$mD009['D001_Descricao_Produto']}','$D009_Id')");
                    $opcoes = array('campoNCM' => 'false', 'campoIPI' => 'false', 'campoNossoCodigo' => 'false', 'campoOrigemProduto' => 'false', 'campoCFOP' => 'false', 'campoICMS' => 'false', 'campoICMSST' => 'false');
                    $this->atualizarAliquotasProdutos("WHERE D069_Id = '{$g['mysqlLastId']}'", $opcoes);
                }
            }
        }

        $sqlD069 = mysql_query("SELECT D069_Valor_Custo_Unitario, D069_D070_Id, D069_Id, D069_Data_Atualizacao_Custo FROM D069 JOIN D070 ON D070_Id=D069_D070_Id WHERE D069_Id='{$mD009['D009_D069_Id']}'");
        $mD069   = mysql_fetch_array($sqlD069);
        $Custo_Tabela          = $mD069['D069_Valor_Custo_Unitario'];
        $Data_Alteracao_Tabela = $mD069['D069_Data_Atualizacao_Custo'];

        /***********************************
        * PRODUTOS MONTADOS E MODIFICADOS
        ***********************************/
        if ($mD009['D049_Flag_Tipo'] == 'MT' || $mD009['D049_Flag_Tipo'] == 'MD') {
            //Dados dos produtos que são matéria primas para os produtos montados/modificados
            $D042  = mysql_query("SELECT SUM(D009_Valor_Custo_Compra * D042_Quantidade) as Custo, 
                                         SUM(D009_Valor_Custo_Ultima_Compra * D042_Quantidade) as Custo_Compra,
                                         SUM(D009_Valor_Custo_Medio_Unitario * D042_Quantidade) as Custo_Medio,
                                         SUM(D009_Valor_Custo_Tabela * D042_Quantidade) as Custo_Tabela,
                                         MIN(D009_Data_Ultima_Entrada) as Data_Compra,
                                         MIN(D009_Data_Atualizacao_Custo_Fornecedor) as Data_Fornecedor,
                                         MIN(FLOOR(D009_Quantidade_Estoque_Real/D042_Quantidade)) as Disponivel_Montagem
                                    FROM D042 
                               LEFT JOIN D049 ON D049_Id=D042_D049_Id_Itens
                               LEFT JOIN D001 ON D001_Id=D049_D001_Id
                               LEFT JOIN D009 ON D009_D049_Id=D042_D049_Id_Itens AND D009_C004_Id = '{$g['empresaAtual']}'
                                   WHERE D042_D049_Id='{$mD009['D049_Id']}'");

            $mD042 = mysql_fetch_array($D042);
            $Custo = $mD042['Custo'];
            $Custo_Compra = $mD042['Custo_Compra'];
            $Custo_Medio = $mD042['Custo_Medio'];
            $Custo_Tabela = $mD042['Custo_Tabela'];
            $Data_Ultima_Entrada = $mD042['Data_Compra'];
            $Data_Alteracao_Tabela = $mD042['Data_Fornecedor'];
            $Saldo_Regras = $mD042['Disponivel_Montagem'];

            if($mD009['D049_Percentual_Acrescimo_Custo'] > 0){
                $Custo_Compra = $Custo_Compra + ($Custo_Compra * ($mD009['D049_Percentual_Acrescimo_Custo']/100));
                $Custo_Medio = $Custo_Medio + ($Custo_Medio * ($mD009['D049_Percentual_Acrescimo_Custo']/100));
                $Custo_Tabela = $Custo_Tabela + ($Custo_Tabela * ($mD009['D049_Percentual_Acrescimo_Custo']/100));
                $Custo = $Custo + ($Custo * ($mD009['D049_Percentual_Acrescimo_Custo']/100));
            }
            //Define a data da atualização e o custo utilizado com base na disponibilidade de montagem
            if($Saldo_Regras > 0){
                $Data_Alteracao_Custo = $mD042['Data_Compra'];
                $Custo_Utilizado = 'U';
            } else {
                $Data_Alteracao_Custo = $mD042['Data_Fornecedor'];
                $Custo_Utilizado = 'T';
            }
            if ($mD009['D009_Valor_Preco_Tabela']>0){
                $Custo = $mD009['D009_Valor_Preco_Tabela'];
                $Data_Alteracao_Custo = $Data_Alteracao_Custo_Manual;
                $Custo_Utilizado = 'MA';
            }

        } else if($mD009['D049_Flag_Tipo'] == 'PD'){
            /**********************
            * PRODUTOS PRODUZIDOS *
            **********************/
            if ($mD009['D009_Valor_Preco_Tabela']>0){
                $Custo = $mD009['D009_Valor_Preco_Tabela'];
                $Data_Alteracao_Custo = $Data_Alteracao_Custo_Manual;
                $Custo_Utilizado = 'MA';

            }else{
                //Custo total da Composição
                $sqlComposicao = "SELECT SUM(D009_Valor_Custo_Unitario * D074_Quantidade) as Custo,
                                         SUBSTRING(MIN(D009_Data_Alteracao_Custo),1,10) as Data_Custo
                                    FROM D074
                               LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                               LEFT JOIN D049 ON D001_Id = D049_D001_Id 
                               LEFT JOIN D009 ON D049_Id = D009_D049_Id 
                               LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                   WHERE D074_D001_Id = '{$mD009['D001_Id']}'
                                     AND D009_C004_Id='{$g['empresaAtual']}'";
                $sqlComposicao = mysql_query($sqlComposicao);
                $rowComposicao = mysql_fetch_array($sqlComposicao);
                $composicao = $rowComposicao['Custo'];
                $dataCusto = substr($rowComposicao['Data_Custo'],0,10);

                //Custo total e tempo dos processos
                $processo = "SELECT SUM(D075_Lead_Time) as Tempo, SUM(D075_Lead_Time * (D079_Custo_Minuto + D078_Custo_Minuto)) as Custo
                               FROM D075
                          LEFT JOIN D078 on D078_Id = D075_D078_Id
                          LEFT JOIN D079 on D079_Id = D075_D079_Id
                              WHERE D075_D001_Id = '{$mD009['D001_Id']}'";
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
                             WHERE D075_D001_Id = '{$mD009['D001_Id']}'";
                $rConsumo = mysql_query($consumo);
                $consumo = mysql_fetch_array($rConsumo);
                $consumo = $consumo['Custo'];
                //Custo final
                $Custo = $Custo_Tabela = $composicao + $custoProcesso + $consumo;
                $Data_Alteracao_Tabela = $Data_Alteracao_Custo = $dataCusto;
                $Custo_Utilizado = 'T';
            }
        } else {
            //$Custo_Medio                  Custo médio
            //$Custo_Compra                 Custo última compra
            //$Custo_Tabela                 Custo tabela
            //$Saldo_Regras                 Estoque com base no histórico do produto ou dos produtos que são matéria prima para os produtos montados/modificados
            //$Data_Alteracao_Tabela        Data da ultima alteração do custo tabela
            //$Data_Ultima_Entrada          Data da ultima compra/custo médio
            $diasCustoCompra = round(abs(strtotime(date("Y-m-d"))-strtotime($Data_Ultima_Entrada))/86400);
            $diasCustoTabela = round(abs(strtotime(date("Y-m-d"))-strtotime($Data_Alteracao_Tabela))/86400);
            log("diasCustoCompra: {$diasCustoCompra} diasCustoTabela: {$diasCustoTabela}");
            //Verificamos o tipo de custo utilizado pela empresa (De acordo com o estoque ou o custo mais recente)
            if($g['C031']['tipoCusto'] == 'E'){
                //Caso o estoque seja positivo, utilizamos a primeira regra de custo
                if($Saldo_Regras > 0){
                    if($g['C031']['prazoCustoPrincipal'] > 0){
                        //Custo principal utilizado é médio
                        if($g['C031']['custoPrincipal'] == 'M' && $diasCustoCompra <= $g['C031']['prazoCustoPrincipal']){
                            $Custo = $Custo_Medio;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é última compra
                        if($g['C031']['custoPrincipal'] == 'U' && $diasCustoCompra <= $g['C031']['prazoCustoPrincipal']){
                            $Custo = $Custo_Compra;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é o tabela
                        if($g['C031']['custoPrincipal'] == 'T' && $diasCustoTabela <= $g['C031']['prazoCustoPrincipal']){
                            $Custo = $Custo_Tabela;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                        }
                    } else {
                        //Custo principal utilizado é médio
                        if($g['C031']['custoPrincipal'] == 'M'){
                            $Custo = $Custo_Medio;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é última compra
                        if($g['C031']['custoPrincipal'] == 'U'){
                            $Custo = $Custo_Compra;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é o tabela
                        if($g['C031']['custoPrincipal'] == 'T'){
                            $Custo = $Custo_Tabela;
                            $Custo_Utilizado = $g['C031']['custoPrincipal'];
                            $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                        }
                    }
                }

                //Caso o estoque esteja igual/menor a zero ou nao aplicou nenhum custo mesmo com estoque positivo, utilizamos a segunda regra de custo
                if($Saldo_Regras <= 0 || $Custo_Utilizado == '' || $Custo <= 0){
                    if($g['C031']['prazoCustoSecundario'] > 0){
                        //Custo principal utilizado é médio
                        if($g['C031']['custoSecundario'] == 'M' && $diasCustoCompra <= $g['C031']['prazoCustoSecundario']){
                            $Custo = $Custo_Medio;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é última compra
                        if($g['C031']['custoSecundario'] == 'U' && $diasCustoCompra <= $g['C031']['prazoCustoSecundario']){
                            $Custo = $Custo_Compra;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é o tabela
                        if($g['C031']['custoSecundario'] == 'T' && $diasCustoTabela <= $g['C031']['prazoCustoSecundario']){
                            $Custo = $Custo_Tabela;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                        }
                    } else {
                        //Custo principal utilizado é médio
                        if($g['C031']['custoSecundario'] == 'M'){
                            $Custo = $Custo_Medio;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é última compra
                        if($g['C031']['custoSecundario'] == 'U'){
                            $Custo = $Custo_Compra;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        }
                        //Custo principal utilizado é o tabela
                        if($g['C031']['custoSecundario'] == 'T'){
                            $Custo = $Custo_Tabela;
                            $Custo_Utilizado = $g['C031']['custoSecundario'];
                            $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                        }
                    }
                }

                //Caso nenhuma regra de custo foi aplicada, será utilizado a regra de custo zerado
                if($Custo_Utilizado == '' || $Custo <= 0){
                    switch ($g['C031']['custoZerado']) {
                        case 'M':
                            $Custo = $Custo_Medio;
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        break;
                        case 'U':
                            $Custo = $Custo_Compra;
                            $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                        break;
                        case 'T':
                            $Custo = $Custo_Tabela;
                            $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                        break;
                    }
                    $Custo_Utilizado = $g['C031']['custoZerado'];
                }
                
                //if ($mD009['D009_Valor_Preco_Tabela']>0 && !empty($Custo_Utilizado) && $Custo>0){
                
                if ($mD009['D009_Valor_Preco_Tabela']>0){
                    $Custo = $mD009['D009_Valor_Preco_Tabela'];
                    $Data_Alteracao_Custo = $Data_Alteracao_Custo_Manual;
                    $Custo_Utilizado = 'MA';
                }
                
            } else {
                //Caso seja utilizado o custo mais recente
                $CustoVerificado = ($g['C031']['custoMaisRecenteUtilizado'] == 'M') ?  $Custo_Medio : $Custo_Compra;
                if(strtotime($Data_Ultima_Entrada) > strtotime($Data_Alteracao_Tabela) && strtotime($Data_Ultima_Entrada) > strtotime($Data_Alteracao_Custo_Manual) && $CustoVerificado > 0) {
                    $Custo = ($g['C031']['custoMaisRecenteUtilizado'] == 'M') ?  $Custo_Medio : $Custo_Compra;
                    $Data_Alteracao_Custo = $Data_Ultima_Entrada;
                    $Custo_Utilizado = $g['C031']['custoMaisRecenteUtilizado'];
                } else if(strtotime($Data_Alteracao_Custo_Manual) > strtotime($Data_Alteracao_Tabela) && strtotime($Data_Alteracao_Custo_Manual) > strtotime($Data_Ultima_Entrada) && $Custo_Manual > 0) {
                    $Custo = $Custo_Manual;
                    $Data_Alteracao_Custo = $Data_Alteracao_Custo_Manual;
                    $Custo_Utilizado = 'MA';
                } else {
                    $Custo = $Custo_Tabela;
                    $Data_Alteracao_Custo = $Data_Alteracao_Tabela;
                    $Custo_Utilizado = 'T';
                }
            }
        }
        $Custo_Utilizado      = ($Custo_Utilizado == '') ? '' : $Custo_Utilizado;
        $Custo_Compra         = isset($Custo_Compra) ? $Custo_Compra : 0;
        $mD009['D009_ICF_1'] = empty($mD009['D009_ICF_1']) ? '0' : $mD009['D009_ICF_1'];

        $sql = <<<EOT
            UPDATE D009
                SET D009_Valor_Custo_Medio_Unitario='$Custo_Medio',
                    D009_Valor_Custo_Compra='$Custo',
                    D009_Valor_Custo_Ultima_Compra='$Custo_Compra',
                    D009_Valor_Custo_Tabela='$Custo_Tabela',
                    D009_Valor_Melhor_Custo='{$mD069['D069_Valor_Custo_Unitario']}',
                    D009_Data_Atualizacao_Custo_Fornecedor='$Data_Alteracao_Tabela',
                    D009_Valor_Custo_Unitario=D009_Valor_Custo_Compra*if({$mD009['D009_ICF_1']}>0,{$mD009['D009_ICF_1']},1),
                    D009_Data_Ultima_Entrada='$Data_Ultima_Entrada',
                    D009_Data_Alteracao_Custo='$Data_Alteracao_Custo',
                    D009_Flag_Custo_Utilizado='{$Custo_Utilizado}'
            WHERE D009_Id='{$mD009['D009_Id']}'
EOT;
        mysql_query($sql);

        $Custo_Liquido_Compra = isset($Custo_Liquido_Compra) ? $Custo_Liquido_Compra : 0;

        $sql = <<<EOT
            UPDATE D009A
               SET D009A_Valor_Custo_Liquido_Ultima_Compra = '{$Custo_Liquido_Compra}',
                   D009A_Valor_Custo_Unitario_Liquido      = D009A_Valor_Custo_Liquido_Ultima_Compra*if({$mD009['D009_ICF_1']}>0,{$mD009['D009_ICF_1']},1)
          WHERE D009A_D009_Id = '{$mD009['D009_Id']}'
EOT;
         mysql_query($sql);

         return true;
    }

    /**
     * atualizarEstatisticasCompra
     *
     * @param String $D009_Id
     * @param String $D009_Quantidade_Estoque
     * @return true
     */  
    public function atualizarEstatisticasCompra($D009_Id, $D009_Quantidade_Estoque = false)
    {
        global $g;
        $sqlD009 = mysql_query("SELECT D009_Quantidade_Estoque,
                                        D009_Quantidade_Estoque_Liquido,
                                        D009_Meses_MMF,
                                        D009_Id,
                                        D009_Meses_Compra,
                                        D049_Quantidade_Minima_Compra,
                                        D009_Quantidade_OC,
                                        D049_Quantidade_Embalagem_Compra,
                                        D009_Quantidade_Pedido(D009_Id,0) as D009_Quantidade_Pedidos,
                                        D009_Meses_Reposicao,
                                        D009_Quantidade_Minima_Estoque,
                                        D009_Quantidade_Maxima_Estoque
                                    FROM D009 
                                        LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                    WHERE 
                                        D009_Id = '{$D009_Id}'");
        $mD009 = mysql_fetch_array($sqlD009);

        // verifica se está habilitada a opção do calculo da quantidade a comprar através do estoque físico ou líquido
        if($g['C031']['estoqueSugestaoCompra'] == 'F'){
            $tipoEstoque = $mD009['D009_Quantidade_Estoque'];
        }else if($g['C031']['estoqueSugestaoCompra'] == 'L'){
            $tipoEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
        }

        if($D009_Quantidade_Estoque == false){
            $D009_Quantidade_Estoque = $tipoEstoque;
        }

        $Quantidade_Minima_Estoque    = '0';
        $Quantidade_Maxima_Estoque    = '0';
        $Frequencia_Venda             = '0';
        $Quantidade_Comprar_Calculada = '0';
        $Quantidade_Consumo           = '0';
        $Data_Ultima_Venda            = '0000-00-00';
        $Data_Ultima_Saida            = '0000-00-00';
        $Clientes_Consumo             = '0';
        $ultimaData                   = '9999-99-99';

            // Não está funcionando com a global estatisticaDeCompraMesesComMovimento == S, traz os valores errados
       /*if ($mD009['D009_Meses_MMF']>0){
            if($g['C031']['estatisticaDeCompraMesesComMovimento'] == 'N'){
                $ultimaData = date("Y-m-d", strtotime("-0" . $mD009['D009_Meses_MMF'] . " months"));
            } else {
                // Atualiza estatisca de venda apenas com os meses que tiveram venda
                $ultimaData = mysql_query("SELECT T001_Data_Lancamento
                                             FROM T001
                                            WHERE T001_D009_Id='{$mD009['D009_Id']}'
                                              AND T001_Flag_MMF='S'
                                         GROUP BY YEAR(T001_Data_Lancamento),MONTH(T001_Data_Lancamento) 
                                         ORDER BY T001_Data_Lancamento ASC
                                            LIMIT {$mD009['D009_Meses_MMF']}");

                $ultimaData = mysql_fetch_array($ultimaData);
                $ultimaData = date('Y-m-d', strtotime("+1 days",strtotime($ultimaData['T001_Data_Lancamento'])));
            }
        } */
        
        if ($mD009['D009_Meses_MMF']>0){
            $ultimaData = date("Y-m-d", strtotime("-0" . $mD009['D009_Meses_MMF'] . " months"));
        }

        $T001 = mysql_query("SELECT count(*) 
                               FROM T001 
                              WHERE T001_D009_Id='{$mD009['D009_Id']}' 
                                AND T001_Data_Lancamento > '{$ultimaData}' 
                                AND IF(T001_Flag_MMF='S',1,0)=1
                                AND T001_Flag_Operacao != 'C'");
        
        $mFrequecia_Venda = mysql_fetch_array($T001);
        $Frequencia_Venda = $mFrequecia_Venda['count(*)'];

        $T001  = mysql_query("SELECT SUM(T001_Quantidade) as Quantidade 
                                FROM T001 
                               WHERE T001_D009_Id='{$mD009['D009_Id']}' 
                                 AND IF(T001_Flag_MMF='S',1,0)=1
                                 AND T001_Data_Lancamento >= '{$ultimaData}'
                                 AND T001_Flag_Operacao != 'C'");

        $mT001 = mysql_fetch_array($T001);
        $Quantidade_Consumo = $mT001['Quantidade'];
        $mediaMMF = $Quantidade_Consumo / $mD009['D009_Meses_MMF'];
        $mD009['mediaMMF'] = $mediaMMF;

        $mD009['qtdVendaMedia'] = $Quantidade_Consumo / $Frequencia_Venda;
        $mD009['ultimaData']    = $ultimaData; 

        if($g['C031']['calculaEstoqueMinimoMaximoProduto'] == 'N'){
            $Quantidade_Minima_Estoque = $mD009['D009_Quantidade_Minima_Estoque'];
        } else {
            $Quantidade_Minima_Estoque =  $this->formulaCalculoEstoqueMinimo($mD009);
        }
        
        if ($mD009['D049_Quantidade_Minima_Compra']>$Quantidade_Minima_Estoque) {
            $Quantidade_Minima_Estoque = $mD009['D049_Quantidade_Minima_Compra'];
        }

        //$D009_Quantidade_Estoque = $D009_Quantidade_Estoque-$Quantidade_Minima_Estoque;
        $mD009['D009_Quantidade_Estoque'] = $D009_Quantidade_Estoque;
        $mD009['Quantidade_Minima_Estoque'] = $Quantidade_Minima_Estoque;
        
        // $Quantidade_Maxima_Estoque_Calculado = $Quantidade_Minima_Estoque * 2;
        $Quantidade_Maxima_Estoque_Calculado = $this->formulaCalculoEstoqueMaximo($Quantidade_Minima_Estoque);
        $Quantidade_Comprar_Calculada        = $this->formulaCalculoSugestaoCompra($mD009);

        $T001 = mysql_query("SELECT COUNT(DISTINCT T001_D024_Id_Cliente) as Clientes 
                               FROM T001 
                              WHERE T001_D009_Id='{$mD009['D009_Id']}' 
                                AND T001_Data_Lancamento >= '{$ultimaData}' 
                                AND IF(T001_Flag_MMF='S',1,0)=1
                                AND T001_Flag_Operacao != 'C'");

        $mClientes_Consumo = mysql_fetch_array($T001);
        $Clientes_Consumo  = $mClientes_Consumo['Clientes'];

        $T001 = mysql_query("SELECT COUNT(distinct month(T001_Data_Lancamento)) 
                               FROM T001 
                              WHERE T001_D009_Id='{$mD009['D009_Id']}' 
                                AND T001_Data_Lancamento >= '{$ultimaData}' 
                                AND IF(T001_Flag_MMF='S',1,0)=1
                                AND T001_Flag_Operacao != 'C'");

        $mMeses                 = mysql_fetch_array($T001);
        $Frequencia_Venda_Meses = $mMeses[0];

        // Verifica a quantidade orçada no periodo
        $T004 = mysql_query("SELECT SUM(T004_Quantidade) AS T004_Quantidade 
                               FROM T004 
                          LEFT JOIN T003 ON T003_Id = T004_T003_Id 
                          LEFT JOIN D006 ON D006_Id = T004_D006_Id
                              WHERE T004_D009_Id = '{$D009_Id}' 
                                AND T003_Data_Emissao >= '{$ultimaData}' 
                                AND T003_Flag_Status_Orcamento != 'C'
                                AND IF(D006_Flag_MMF='S',1,0)=1");
        $mT004 = mysql_fetch_array($T004);

        // Quantidade de meses de estoque
        $quantidadeMesesEstoque = ($D009_Quantidade_Estoque / ($Quantidade_Consumo / $mD009['D009_Meses_MMF']));

        /*$T001              = mysql_query("SELECT T001_Data_Lancamento FROM T001 WHERE T001_D009_Id='{$mD009['D009_Id']}' AND IF(T001_Flag_MMF='S',1,0)=1 ORDER BY T001_Data_Lancamento DESC");
        $mT001_SUM_MMF     = mysql_fetch_array($T001);
        $Data_Ultima_Venda = $mT001_SUM_MMF['T001_Data_Lancamento'];
        $Data_Ultima_Saida = $mT001_SUM_MMF['T001_Data_Lancamento'];*/
           
        // busca a data da ultima compra
        $T001 = mysql_query("SELECT T001_Data_Lancamento
                               FROM T001
                              WHERE T001_D009_Id = '{$mD009['D009_Id']}'
                                AND T001_Flag_Operacao = 'E'
                           ORDER BY T001_Data_Lancamento DESC LIMIT 1");

        $mT001            = mysql_fetch_array($T001);
        $dataUltimaCompra = $mT001['T001_Data_Lancamento'];

        //reduz a quantidade de meses na data da ultima compra
        $ultimaDataAntesCompra = date('Y-m-d', strtotime('-0'. $mD009['D009_Meses_MMF'] .'month', strtotime($dataUltimaCompra)));

        // soma a quantidade vendida antes da ultima entrada
        $T001 = mysql_query("SELECT SUM(T001_Quantidade) AS quantidade
                               FROM T001 
                              WHERE T001_D009_Id = '{$mD009['D009_Id']}' 
                                AND T001_Flag_MMF = 'S'
                                AND T001_Data_Lancamento >= '{$ultimaDataAntesCompra}'
                                AND T001_Data_Lancamento <= '{$dataUltimaCompra}'
                                AND T001_Flag_Operacao != 'C'");

        $mT001 = mysql_fetch_array($T001);

        $mediaVendaAntesUltimaCompra = $mT001['quantidade'] / $mD009['D009_Meses_MMF'];

        //verifica a quantidade de meses da ultima compra até a data atual
        $date             = new \DateTime($dataUltimaCompra);           // data ultima compra
        $dataAtual        = $date->diff(new \DateTime(date("Y-m-d")));  // data atual
        $anos             = $dataAtual->format('%Y') * 12;
        $meses            = $dataAtual->format('%m');
        $totalMeses       = ($anos + $meses);  

        // se o mesesMMF for maior que o total de meses da ultima compra até a data atual, mesesMMF = totalMeses
        $mesesMMF = $mD009['D009_Meses_MMF'];
        if($mD009['D009_Meses_MMF'] > $totalMeses){
            $mesesMMF = $totalMeses;
        }

        //acrescenta a quantidade de meses na data da ultima compra
        $ultimaDataAposCompra = date('Y-m-d', strtotime('+0'. $mesesMMF .'month', strtotime($dataUltimaCompra)));

        // soma a quantidade vendida depois da ultima entrada
        $T001 = mysql_query("SELECT SUM(T001_Quantidade) AS quantidade
                               FROM T001 
                              WHERE T001_D009_Id = '{$mD009['D009_Id']}' 
                                AND T001_Flag_MMF = 'S'
                                AND T001_Data_Lancamento >= '{$dataUltimaCompra}'
                                AND T001_Data_Lancamento <= '{$ultimaDataAposCompra}'
                                AND T001_Flag_Operacao != 'C'");
        $mT001 = mysql_fetch_array($T001);

        $mediaVendaAposUltimaCompra = $mT001['quantidade'] / $mesesMMF;

        $updateD009  = "UPDATE D009 SET ";
        if($g['C031']['calculaEstoqueMinimoMaximoProduto'] == 'S'){
            $updateD009 .= "D009_Quantidade_Minima_Estoque = '{$Quantidade_Minima_Estoque}', ";
            $updateD009 .= "D009_Quantidade_Maxima_Estoque = '{$Quantidade_Maxima_Estoque}', ";
        }
        $updateD009 .= "D009_Frequencia_Venda              =  '{$Frequencia_Venda}', ";
        $updateD009 .= "D009_Quantidade_Comprar_Calculada  =  '{$Quantidade_Comprar_Calculada}', ";
        $updateD009 .= "D009_Quantidade_Consumo            =  '{$Quantidade_Consumo}', ";
        $updateD009 .= "D009_Clientes_Consumo              =  '{$Clientes_Consumo}', ";
        $updateD009 .= "D009_Frequencia_Venda_Meses        =  '{$Frequencia_Venda_Meses}' ";
        $updateD009 .= "WHERE D009_Id = '{$mD009['D009_Id']}'";
        mysql_query($updateD009); 

/*         $sql = <<<EOT
            UPDATE D009
               SET D009_Quantidade_Minima_Estoque    = '{$Quantidade_Minima_Estoque}',
                   D009_Quantidade_Maxima_Estoque    = '{$Quantidade_Maxima_Estoque}',
                   D009_Frequencia_Venda             = '{$Frequencia_Venda}',
                   D009_Quantidade_Comprar_Calculada = '{$Quantidade_Comprar_Calculada}',
                   D009_Quantidade_Consumo           = '{$Quantidade_Consumo}',
                   D009_Clientes_Consumo             = '{$Clientes_Consumo}',
                   D009_Frequencia_Venda_Meses       = '{$Frequencia_Venda_Meses}'
             WHERE D009_Id='{$mD009['D009_Id']}'
EOT;
        mysql_query($sql); */

        // verifiva se ja existe registro na tabela estendida D009A
        $D009A = mysql_query("SELECT D009A_Id FROM D009A WHERE D009A_D009_Id = '{$D009_Id}'");   
        if(mysql_num_rows($D009A) > 0){
            mysql_query("UPDATE D009A 
                            SET D009A_Quantidade_Orcada               = '{$mT004['T004_Quantidade']}',
                                D009A_Quantidade_Meses_Estoque        = '{$quantidadeMesesEstoque}',
                                D009A_Media_Venda_Antes_Ultima_Compra = '{$mediaVendaAntesUltimaCompra}',
                                D009A_Media_Venda_Apos_Ultima_Compra  = '{$mediaVendaAposUltimaCompra}'
                          WHERE D009A_D009_Id = '{$D009_Id}'");

            $error = mysql_error();
            if(!empty($error)){
                return 'Erro ao atualizar registro na tabela estendida D009A</br>'.$error;
            }
        } else {
            mysql_query("INSERT INTO D009A (D009A_D009_Id,
                                            D009A_Quantidade_Orcada,
                                            D009A_Quantidade_Meses_Estoque,
                                            D009A_Media_Venda_Antes_Ultima_Compra,
                                            D009A_Media_Venda_Apos_Ultima_Compra
                                    )VALUES('{$D009_Id}',
                                            '{$mT004['T004_Quantidade']}',
                                            '{$quantidadeMesesEstoque}',
                                            '{$mediaVendaAntesUltimaCompra}',
                                            '{$mediaVendaAposUltimaCompra}')");
            $error = mysql_error();
            if(!empty($error)){
                return 'Erro ao inserir registro na tabela estendida D009A</br>'.$error;
            }
        }

        // Função personalizável para executar exceções
        $this->excecaoAtualizarEstatisticasCompra($D009_Id);

        return true;
    }

    public function excecaoAtualizarEstatisticasCompra($D009_Id)
    {
        return false;
    }

    public function formulaCalculoEstoqueMinimo($mD009){
        global $g;
        // Quantidade média vendas * meses de estoque
        $Quantidade_Minima_Estoque = $mD009['mediaMMF']*$mD009['D009_Meses_Compra'];
        // Quantide média de vendas * meses prazo de entrega
        if ($mD009['D009_Meses_Reposicao']>0) {
            $Quantidade_Minima_Estoque += $mD009['mediaMMF']*$mD009['D009_Meses_Reposicao'];
        }
        return $Quantidade_Minima_Estoque;
    }

    public function formulaCalculoEstoqueMaximo($mD009){
        global $g;
        $Quantidade_Maxima_Estoque_Calculado = $mD009['Quantidade_Minima_Estoque'] * 2;
        return $Quantidade_Maxima_Estoque_Calculado;
    }

    public function formulaCalculoSugestaoCompra($mD009){
        global $g;
        // Deduzir quantidade em estoque e quantidade OC para chegar
        $Quantidade_Comprar_Calculada = $mD009['Quantidade_Minima_Estoque'] - $mD009['D009_Quantidade_Estoque_Liquido'] - $mD009['D009_Quantidade_OC'];
        if ($mD009['D049_Quantidade_Embalagem_Compra'] > 0 AND $Quantidade_Comprar_Calculada > 0) {
            if ($mD009['D049_Quantidade_Embalagem_Compra'] > $Quantidade_Comprar_Calculada) {
                $Quantidade_Comprar_Calculada = $mD009['D049_Quantidade_Embalagem_Compra'];
            } else {
                $resto = fmod($Quantidade_Comprar_Calculada,$mD009['D049_Quantidade_Embalagem_Compra']);
                $Quantidade_Comprar_Calculada = $mD009['D049_Quantidade_Embalagem_Compra'] * floor($Quantidade_Comprar_Calculada/$mD009['D049_Quantidade_Embalagem_Compra']);
                log ("Quantidade embalagens " . $Quantidade_Comprar_Calculada);
                if ($resto > 0) {
                    log ("Quantidade embalagens resto " . $resto);
                    $Quantidade_Comprar_Calculada += $mD009['D049_Quantidade_Embalagem_Compra'];
                }
            }
        }
        return $Quantidade_Comprar_Calculada;
    }

    /**
     * atualizaPrecoTabelaOrcamento
     *
     * @param Array $mT004
     * @return true
     */ 
    public function atualizaPrecoTabelaOrcamento($mT004, $forcarUpdate = false, $TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0)
    {
        global $g;        

		require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        
        if(empty($mT004['T004_T219_Id']) && !empty($mT004['T006_T219_Id'])){
            mysql_query("UPDATE T004 SET T004_T219_Id = '{$mT004['T006_T219_Id']}' WHERE T004_Id = '{$mT004['T004_Id']}'", false);
            $mT004['T004_T219_Id'] = $mT004['T006_T219_Id'];
        }

        // Se não tiver T218_Id zera o T219_Id, são casos em que o vendedor apenas solicita prazo para compras
        $mT004['T004_T219_Id'] = ($mT004['T219_T218_Id'] > 0) ? $mT004['T004_T219_Id'] : 0;

        //Retorno do demonstrativo e preço da cotação/OC
        $precoCotacao             = $this->cad002RetornaPrecoCotacao($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], false, $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $mT004['T004_T219_Id'], false, 4, $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI']);
        $demonstrativoCotacao     = $precoCotacao[1];
        $custoMedio               = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[2] : 0;
        $quantidadeEstoque        = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[3] : 0;
        $precoCotacao             = gCorrigeNumeroInverte($precoCotacao[0]);
        $demonstrativoCotacao     = mysql_real_escape_string(serialize($demonstrativoCotacao));

        //Retorno do demonstrativo e preço da ultima compra/registro estoque altera custo
        $precoCompra              = $this->cad002RetornaPrecoUltimaCompra($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], $mT004['T003_Flag_ACP'], $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], 4, $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI']);
        $demonstrativoCompra      = $precoCompra[1];
        $precoCompra              = gCorrigeNumeroInverte($precoCompra[0]);
        $demonstrativoCompra      = mysql_real_escape_string(serialize($demonstrativoCompra));

        //Retorno do demonstrativo e preço da tabela do fornecedor
        $precoTabela              = $this->cad002RetornaPrecoTabela($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], $mT004['T003_Flag_ACP'], $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI'], $TotalPorcentagemIPV, $TotalPorcentagemICF, $mT004['T004_Aliquota_IRPJ'], $mT004['T004_Aliquota_CSLL']);
        $demonstrativoPrecoTabela = $precoTabela[1];
        $valorPrecoTabela         = gCorrigeNumeroInverte($precoTabela[0]);
        $demonstrativoPrecoTabela = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));

        //Retorno do demonstrativo de custo da tabela do fornecedor
        $demonstrativoCustoTabela = $this->cad002demonstrativoCustoTabela($mT004['D009_Id']);
        $demonstrativoCustoTabela = mysql_real_escape_string($demonstrativoCustoTabela);

        //Método retornaPrecoVenda atualizado
        $dadosPrecoVenda          = $this->retornaPrecoVenda($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], false, $mT004['T004_D006_Id'], $mT004['T004_Aliquota_ICMS'], $mT004['T004_T219_Id'], false, $mT004['T003_Casas_Decimais_Preco'], $mT004['T004_Aliquota_PIS'], $mT004['T004_Aliquota_COFINS'], $mT004['T004_Aliquota_IPI'], $mT004['T004_ST_VA'], $mT004['T004_Percentual_Reducao_ICMS_ST'], $mT004['T004_ST_ICMS_Interno'], $mT004['T004_Percentual_Reducao_ICMS'], $mT004['T004_ICMS_Inter_Estadual'], $mT004['T004_Aliquota_IRPJ'], $mT004['T004_Aliquota_CSLL'], $mT004['T004_Valor_Frete_Unitario'], $TotalPorcentagemIPV, $TotalPorcentagemICF, false, $mT004['T003A_Flag_Ratear_Frete_Custo']);
        $precoVendaProduto        = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda  = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoMedio               = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[2] : 0;
        $custoProduto             = $dadosPrecoVenda[4];
        $origemProduto            = $dadosPrecoVenda[5];

        //Retorno do preço de transferencia
        if($mT004['T004_Flag_Transferencia'] == 'S' && $mT004['T004_Quantidade'] > $mT004['D009_Quantidade_Estoque_Liquido']){
            $sqlD009_Transferencia = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id = '{$mT004['D049_Id']}' AND D009_C004_Id = '{$mT004['T004_C004_Id_Transferencia']}'");
            $rowD009_Transferencia = mysql_fetch_array($sqlD009_Transferencia);
            $D009_Id               = $rowD009_Transferencia['D009_Id'];

            $dadosPrecoVenda                  = $this->cad002RetornaPrecoTransferencia($D009_Id, $mT004['T004_C004_Id_Transferencia'], $g['C004']['C004_D024_Id'], $mT004['T004_T003_Id']);
            $precoVendaProduto                = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $valorPrecoTransferencia          = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $demonstrativoPrecoVenda          = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
            $demonstrativoPrecoTransferencia  = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
            $custoProduto                     = $dadosPrecoVenda[4];
            $origemProduto                    = $dadosPrecoVenda[5];
            $valorCustoTransferencia          = $dadosPrecoVenda[4];
            $origemProdutoTransferencia       = $dadosPrecoVenda[5];
        } else {
            $sqlD009_Transferencia = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id = '{$mT004['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
            $rowD009_Transferencia = mysql_fetch_array($sqlD009_Transferencia);
            $D009_Id               = $rowD009_Transferencia['D009_Id'];

            $dadosPrecoTransferencia          = $this->cad002RetornaPrecoTransferencia($D009_Id, $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], $mT004['T004_T003_Id']);
            $valorPrecoTransferencia          = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
            $demonstrativoPrecoTransferencia  = mysql_real_escape_string(serialize($dadosPrecoTransferencia[1]));
            $valorCustoTransferencia          = $dadosPrecoTransferencia[4];
            $origemProdutoTransferencia       = $dadosPrecoTransferencia[5];
        }

        /*return retornaPrecoVenda
        array(
          0  $preco,
          1  $demonstrativo,
          2  $custoMedio,
          3  $quantidadeEstoque,
          4  $custoProduto,
          5  $origemMercadoria,
          6  $possuiNFEntrada
        );*/

        if($precoVendaProduto != $mT004['T004_Valor_Preco_Original'] ||
            $custoProduto != $mT004['T004_Valor_Custo_Unitario'] ||
            $valorPrecoTabela != $mT004['T004_Valor_Preco_Tabela'] ||
            $valorPrecoTransferencia != $mT004['T004_Valor_Preco_Transferencia'] ||
            $mT004['D009_T001_Id'] != $mT004['T004_T001_Id'] ||
            $origemProduto != $mT004['T004_Flag_Origem_Produto'] || $forcarUpdate === true){
            if (empty($mT004['T004_T219_Id'])) {
                mysql_query("UPDATE T004
                                SET T004_Valor_Preco_Original              = '{$precoVendaProduto}',
                                    T004_Valor_Preco_Original_Bruto        = '{$precoVendaProduto}',
                                    T004_Valor_Preco_Compra                = '{$precoCompra}',
                                    T004_Valor_Preco_Tabela                = '{$valorPrecoTabela}',
                                    T004_Valor_Preco_Transferencia         = '{$valorPrecoTransferencia}',
                                    T004_Valor_Custo_Transferencia         = '{$valorCustoTransferencia}',
                                    T004_Valor_Custo_Unitario              = '{$custoProduto}',
                                    T004_Percentual_Desconto               = ((T004_Valor_Preco_Original-T004_Valor_Preco_Unitario)/T004_Valor_Preco_Original)*100,
                                    T004_T001_Id                           = '{$mT004['D009_T001_Id']}',
                                    T004_Flag_Origem_Produto               = '{$origemProduto}',
                                    T004_Flag_Origem_Produto_Transferencia = '{$origemProdutoTransferencia}',
                                    T004_Demonstrativo_Preco               = '{$demonstrativoCompra}',
                                    T004_Demonstrativo_Preco_Cotacao       = '',
                                    T004_Demonstrativo_Preco_Tabela        = '{$demonstrativoPrecoTabela}',
                                    T004_Demonstrativo_Custo_Tabela        = '{$demonstrativoCustoTabela}',
                                    T004_Demonstrativo_Preco_Venda         = '{$demonstrativoPrecoVenda}',
                                    T004_Demonstrativo_Preco_Transferencia = '{$demonstrativoPrecoTransferencia}'
                              WHERE T004_Id = '{$mT004['T004_Id']}'");
                
                if($origemProduto != $mT004['T004_Flag_Origem_Produto']){
                    // pode mudar a aliquota do ICMS por conta da origem
                    $VEN001->atualizarAliquotas($mT004['T004_T003_Id'], null, $mT004['T004_Id'], null);
                }

                return true;
            }

            mysql_query("UPDATE T004
                                 SET T004_Valor_Preco_Original              = '{$precoVendaProduto}',
                                     T004_Valor_Preco_Original_Bruto        = '{$precoVendaProduto}',
                                     T004_Valor_Preco_Compra                = '{$precoCompra}',
                                     T004_Valor_Preco_Cotacao               = '{$precoCotacao}',
                                     T004_Valor_Preco_Tabela                = '{$valorPrecoTabela}',
                                     T004_Valor_Preco_Transferencia         = '{$valorPrecoTransferencia}',
                                     T004_Valor_Custo_Transferencia         = '{$valorCustoTransferencia}',
                                     T004_Valor_Custo_Unitario              = '{$custoProduto}',
                                     T004_Percentual_Desconto               = ((T004_Valor_Preco_Original-T004_Valor_Preco_Unitario)/T004_Valor_Preco_Original)*100,
                                     T004_T001_Id                           = '{$mT004['D009_T001_Id']}',
                                     T004_Flag_Origem_Produto               = '{$origemProduto}',
                                     T004_Flag_Origem_Produto_Transferencia = '{$origemProdutoTransferencia}',
                                     T004_Demonstrativo_Preco               = '{$demonstrativoCompra}',
                                     T004_Demonstrativo_Preco_Cotacao       = '{$demonstrativoCotacao}',
                                     T004_Demonstrativo_Preco_Tabela        = '{$demonstrativoPrecoTabela}',
                                     T004_Demonstrativo_Custo_Tabela        = '{$demonstrativoCustoTabela}',
                                     T004_Demonstrativo_Preco_Venda         = '{$demonstrativoPrecoVenda}',
                                     T004_Demonstrativo_Preco_Transferencia = '{$demonstrativoPrecoTransferencia}'
                               WHERE T004_Id = '{$mT004['T004_Id']}'");

            mysql_query("UPDATE T219
                           LEFT JOIN T004 ON T004_T219_Id=T219_Id
                                 SET T219_Valor_Preco_Venda           = T004_Valor_Preco_Original,
                                     T219_Valor_Preco_Venda_Negociado = T004_Valor_Preco_Unitario,
                                     T219_Valor_Custo_Medio_Unitario  = '{$custoMedio}'
                               WHERE T004_Id = '{$mT004['T004_Id']}'");

            mysql_query("UPDATE T225
                           LEFT JOIN T219 ON T219_Id=T225_T219_Id
                           LEFT JOIN T004 ON T004_T219_Id=T219_Id
                                 SET T225_Valor_Preco_Venda           = T004_Valor_Preco_Original,
                                     T225_Valor_Preco_Venda_Negociado = T004_Valor_Preco_Unitario,
                                     T225_Valor_Custo_Medio_Unitario  = '{$custoMedio}'
                               WHERE T004_Id = '{$mT004['T004_Id']}'");

            if($origemProduto != $mT004['T004_Flag_Origem_Produto']){
                // pode mudar a aliquota do ICMS por conta da origem
                $VEN001->atualizarAliquotas($mT004['T004_T003_Id'], null, $mT004['T004_Id'], null);
            }

        }
        
        return true;
    }

    /**
     * atualizaPrecoTabelaPedido
     *
     * @param Array $mT006
     * @return true
     */
    public function atualizaPrecoTabelaPedido($mT006, $forcarUpdate = false, $TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0)
    {
        global $g;

        require_once('bibliotecas/classes/VEN002.php');
        $VEN002 = new VEN002();

        //Caso a OC esteja gravada no pedido, verificamos se é uma transferencia
        if($mT006['T005_T224_Id'] > 0){
            $sqlT224 = mysql_query("SELECT T224_Flag_Tipo FROM T224 WHERE T224_Id = '{$mT006['T005_T224_Id']}'");
            $rowT224 = mysql_fetch_array($sqlT224);
            if($rowT224['T224_Flag_Tipo'] == '2'){
                //Caso seja uma transferência, o preço deve ser o da ultima compra
                $mT006['T005_Flag_Lista_Preco'] = 'T';
            }
        }
        
        //Retorno do demonstrativo e preço da cotação/OC
        $precoCotacao             = $this->cad002RetornaPrecoCotacao($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], false, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $mT006['T006_T219_Id'], $mT006['T006_T225_Id'], 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI']);
        $demonstrativoCotacao     = $precoCotacao[1];
        $custoMedio               = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[2] : 0;
        $quantidadeEstoque        = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $precoCotacao[3] : 0;
        $precoCotacao             = gCorrigeNumeroInverte($precoCotacao[0]);
        $demonstrativoCotacao     = mysql_real_escape_string(serialize($demonstrativoCotacao));

        //Retorno do demonstrativo e preço da ultima compra/registro estoque altera custo
        $precoCompra              = $this->cad002RetornaPrecoUltimaCompra($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], $mT006['T005_Flag_ACP'], $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], 4, $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI']);
        $demonstrativoCompra      = $precoCompra[1];
        $demonstrativoCompra      = mysql_real_escape_string(serialize($demonstrativoCompra));
        $precoCompra              = gCorrigeNumeroInverte($precoCompra[0]);

        //Retorno do demonstrativo e preço da tabela do fornecedor
        $precoTabela              = $this->cad002RetornaPrecoTabela($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], $mT006['T005_Flag_ACP'], $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI'], $TotalPorcentagemIPV, $TotalPorcentagemICF, $mT006['T006_Aliquota_IRPJ'], $mT006['T006_Aliquota_CSLL'], $mT006['T005_C004_Id']);
        $demonstrativoPrecoTabela = $precoTabela[1];
        $valorPrecoTabela         = gCorrigeNumeroInverte($precoTabela[0]);
        $demonstrativoPrecoTabela = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));

        //Retorno do demonstrativo de custo da tabela do fornecedor
        $demonstrativoCustoTabela = $this->cad002demonstrativoCustoTabela($mT006['D009_Id']);
        $demonstrativoCustoTabela = mysql_real_escape_string($demonstrativoCustoTabela);
    
        //Método retornaPrecoVenda atualizado com apenas um demonstrativo
        $dadosPrecoVenda          = $this->retornaPrecoVenda($mT006['D009_Id'], $mT006['T005_Flag_Lista_Preco'], $mT006['T005_D024_Id'], false, $mT006['T006_D006_Id'], $mT006['T006_Aliquota_ICMS'], $mT006['T006_T219_Id'], $mT006['T006_T225_Id'], $mT006['T005_Casas_Decimais_Preco'], $mT006['T006_Aliquota_PIS'], $mT006['T006_Aliquota_COFINS'], $mT006['T006_Aliquota_IPI'], $mT006['T006_ST_VA'], $mT006['T006_Percentual_Reducao_ICMS_ST'], $mT006['T006_ST_ICMS_Interno'], $mT006['T006_Percentual_Reducao_ICMS'], $mT006['T006_ICMS_Inter_Estadual'], $mT006['T006_Aliquota_IRPJ'], $mT006['T006_Aliquota_CSLL'], $mT006['T006_Valor_Frete_Unitario'], $TotalPorcentagemIPV, $TotalPorcentagemICF, $mT006['T005_C004_Id'], $mT006['T005A_Flag_Ratear_Frete_Custo']);
        $precoVendaProduto        = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda  = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoMedio               = ($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S') ? $dadosPrecoVenda[2] : 0;
        $custoProduto             = $dadosPrecoVenda[4];
        $origemProduto            = $dadosPrecoVenda[5];

        if($mT006['T006_Flag_Transferencia'] == 'S' && $mT006['T006_Quantidade'] > $mT006['T006_Quantidade_Estoque']){
            $sqlD009_Transferencia = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id = '{$mT006['D049_Id']}' AND D009_C004_Id = '{$mT006['T006_C004_Id_Transferencia']}'");
            $rowD009_Transferencia = mysql_fetch_array($sqlD009_Transferencia);
            $D009_Id               = $rowD009_Transferencia['D009_Id'];

            $dadosPrecoVenda                 = $this->cad002RetornaPrecoTransferencia($D009_Id, $mT006['T006_C004_Id_Transferencia'], $g['C004']['C004_D024_Id'], false, $mT006['T006_T005_Id']);
            $precoVendaProduto               = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $valorPrecoTransferencia         = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
            $demonstrativoPrecoVenda         = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
            $demonstrativoPrecoTransferencia = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
            $custoProduto                    = $dadosPrecoVenda[4];
            $origemProduto                   = $dadosPrecoVenda[5];
            $valorCustoTransferencia         = $dadosPrecoVenda[4];
            $origemProdutoTransferencia      = $dadosPrecoVenda[5];
        } else {
            $sqlD009_Transferencia = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id = '{$mT006['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
            $rowD009_Transferencia = mysql_fetch_array($sqlD009_Transferencia);
            $D009_Id               = $rowD009_Transferencia['D009_Id'];

            $dadosPrecoTransferencia          = $this->cad002RetornaPrecoTransferencia($D009_Id, $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], false, $mT006['T006_T005_Id']);
            $valorPrecoTransferencia          = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
            $demonstrativoPrecoTransferencia  = mysql_real_escape_string(serialize($dadosPrecoTransferencia[1]));
            $valorCustoTransferencia          = $dadosPrecoTransferencia[4];
            $origemProdutoTransferencia       = $dadosPrecoTransferencia[5];
        }
        /*return retornaPrecoVenda
        array(
          0  $preco,
          1  $demonstrativo,
          2  $custoMedio,
          3  $quantidadeEstoque,
          4  $custoProduto,
          5  $origemMercadoria,
          6  $possuiNFEntrada
        );*/

        if($precoVendaProduto != $mT006['T006_Valor_Preco_Original'] ||
            $custoProduto != $mT006['T006_Valor_Custo_Unitario'] ||
            $valorPrecoTabela != $mT006['T006_Valor_Preco_Tabela'] ||
            $valorPrecoTransferencia != $mT006['T006_Valor_Preco_Transferencia'] ||
            $mT006['D009_T001_Id'] != $mT006['T006_T001_Id'] ||
            $origemProduto != $mT006['T006_Flag_Origem_Produto'] || $forcarUpdate === true){

            //Verifica se o T001 é referente a uma importação de emergência
            $T006_T225_Id = 0;
            $sqlT001DI = mysql_query("SELECT IFNULL(T055_T225_Id,0) AS T055_T225_Id FROM T001 LEFT JOIN T008 ON T008_Id=T001_T008_Id LEFT JOIN T055 ON T055_Id=T008_T055_Id WHERE T001_Id = '{$mT006['D009_T001_Id']}'");
            if(mysql_num_rows($sqlT001DI) == 1){
                $rowT001DI = mysql_fetch_assoc($sqlT001DI);
                $T006_T225_Id = $rowT001DI['T055_T225_Id'];

            }
            
            mysqli_query("UPDATE T006 SET T006_Valor_Preco_Original          = '{$precoVendaProduto}',
                                     T006_Valor_Preco_Original_Bruto        = '{$precoVendaProduto}',
                                     T006_Valor_Preco_Compra                = '{$precoCompra}',
                                     T006_Valor_Preco_Cotacao               = '{$precoCotacao}',
                                     T006_Valor_Preco_Tabela                = '{$valorPrecoTabela}',
                                     T006_Valor_Preco_Transferencia         = '{$valorPrecoTransferencia}',
                                     T006_Valor_Custo_Unitario              = '{$custoProduto}',
                                     T006_Valor_Custo_Transferencia         = '{$valorCustoTransferencia}',
                                     T006_Percentual_Desconto               = ((T006_Valor_Preco_Original-T006_Valor_Preco_Unitario)/T006_Valor_Preco_Original)*100,
                                     T006_T001_Id                           = '{$mT006['D009_T001_Id']}',
                                     T006_Flag_Origem_Produto               = '{$origemProduto}',
                                     T006_Flag_Origem_Produto_Transferencia = '{$origemProdutoTransferencia}',
                                     T006_Demonstrativo_Preco               = '{$demonstrativoCompra}',
                                     T006_Demonstrativo_Preco_Cotacao       = '{$demonstrativoCotacao}',
                                     T006_Demonstrativo_Preco_Tabela        = '{$demonstrativoPrecoTabela}',
                                     T006_Demonstrativo_Custo_Tabela        = '{$demonstrativoCustoTabela}',
                                     T006_Demonstrativo_Preco_Venda         = '{$demonstrativoPrecoVenda}',
                                     T006_Demonstrativo_Preco_Transferencia = '{$demonstrativoPrecoTransferencia}',
                                     T006_T225_Id                           = IF('{$T006_T225_Id}' > 0, '{$T006_T225_Id}', T006_T225_Id)
                               WHERE T006_Id = '{$mT006['T006_Id']}'");

            mysqli_query("UPDATE T219 LEFT JOIN T006 ON T006_T219_Id=T219_Id
                                SET T219_Valor_Preco_Venda            = T006_Valor_Preco_Original, 
                                    T219_Valor_Preco_Venda_Negociado  = T006_Valor_Preco_Unitario,
                                    T219_Valor_Custo_Medio_Unitario  = '{$custoMedio}'
                                WHERE T006_Id = '{$mT006['T006_Id']}'");

            mysqli_query("UPDATE T225 LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                LEFT JOIN T006 ON T006_T219_Id=T219_Id
                                SET T225_Valor_Preco_Venda            = T006_Valor_Preco_Original, 
                                    T225_Valor_Preco_Venda_Negociado  = T006_Valor_Preco_Unitario,
                                    T225_Valor_Custo_Medio_Unitario  = '{$custoMedio}'
                                WHERE T006_Id = '{$mT006['T006_Id']}'");

            if($origemProduto != $mT006['T006_Flag_Origem_Produto']){
                // Pode mudar a aliquota do ICMS por conta da origem
                $VEN002->atualizarAliquotasPedidos($mT006['T006_T005_Id'], false , $mT006['T006_Id']);
            }            


        }
        return true;
    }

    /**
     * cad002RetornaPrecoTransferencia
     *
     * @param String $D009_Id
     * @param String $C004_Id
     * @param String $D024_Id
     * @return array
     */     
    public function cad002RetornaPrecoTransferencia($D009_Id, $C004_Id, $D024_Id = false, $T003_Id = false, $T005_Id = false, $T007_Id = false)
    {
        global $g;
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();

        // So montar preco e demonstrativo transfercia se tiver empresa para buscar o preço
        if($C004_Id <= 0){
            return array(
                0,
                array()
            );
        }

        if($D024_Id <= 0){
            $D024_Id = $g['C004']['C004_D024_Id'];
        }

        if($T003_Id > 0){
            $sqlT003 = mysql_query("SELECT * FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id = '{$T003_Id}'");
            $mCab = mysql_fetch_array($sqlT003);
            $listaPreco = $mCab['T003_Flag_Lista_Preco'];
        } else if($T005_Id > 0){
            $sqlT005 = mysql_query("SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id = '{$T005_Id}'");
            $mCab = mysql_fetch_array($sqlT005);
            $listaPreco = $mCab['T005_Flag_Lista_Preco'];
        } else {
            $sqlT007 = mysql_query("SELECT * FROM T007 LEFT JOIN D024 ON D024_Id=T007_D024_Id WHERE T007_Id = '{$T007_Id}'");
            $mCab = mysql_fetch_array($sqlT007);
        }        


        $D006_Id    = $VEN001->retornaCFOP($D024_Id, $D009_Id, $C004_Id, 2);
        $Aliquotas  = $VEN001->retornaAliquotas($D009_Id, false, false, false, false, false, false, $D006_Id, $D024_Id);
        $precoVenda = $this->retornaPrecoVenda($D009_Id, 'T', $D024_Id, false, $D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);

        $sqlD006 = mysql_query("SELECT * FROM D006 WHERE D006_Id = '{$D006_Id}'");
        $mD006 = mysql_fetch_array($sqlD006);
        $sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$C004_Id}'");
        $mC004 = mysql_fetch_array($sqlC004);
        $sqlD009 = mysql_query("SELECT * FROM D009
                                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                                LEFT JOIN D006 ON D006_Id=T001_D006_Id
                                WHERE D009_Id = '{$D009_Id}'");
        $mD009 = mysql_fetch_array($sqlD009);
        $mC004['C004_Nome_Abreviado'] = substr($mC004['C004_Nome_Abreviado'], 0, 12);
        array_unshift($precoVenda[1], array('operacao' => 'cab', 'valor' => "TRANSF. {$mC004['C004_Nome_Abreviado']} ".date("d/m/Y"), 'css' => 'border-bottom: 1px solid black; text-align: center;'));
        $sqlD009_Atual = mysql_query("SELECT * FROM D009 WHERE D009_D049_Id = '{$mD009['D009_D049_Id']}' AND D009_C004_Id = '{$g['empresaAtual']}'");
        $mD009_Atual = mysql_fetch_array($sqlD009_Atual);
        if($listaPreco === false){
            $listaPreco = $mD009_Atual['D009_IPV_1'];
        }
        $valorTotalPreco = $precoVenda[7];
        $precoVenda[2] = $mD009['D009_T001_Id'];
        if($mD009['D009_Origem_Mercadoria'] != ''){
            $mD009['D049_Origem_Mercadoria'] = $mD009['D009_Origem_Mercadoria'];
        }
        $precoVenda[5] = $mD009['D049_Origem_Mercadoria'];

        $valorST = $precoVenda[8];

        $precoVenda[1][count($precoVenda[1])-1]['css'] = 'font-size: 12px; border-bottom:1px solid #000;';
        /*******************************
        * PREÇO DA NF DE TRANSFERÊNCIA
        *******************************/
        //$precoVenda[1][] = array(
        //    'campo' => 'PREÇO TRANSF.',
        //    'valor' => gCorrigeNumero($valorTotalPreco,5),
        //    'operacao' => '=',
        //    'tipo' => 'total',
        //    'css' => 'font-weight: bold; font-size: 12px;border-bottom: 1px solid #000;'
        //);

        /*******************************************************************************************************
        * CALCULO DOS IMPOSTOS DA NOTA DE TRANSFERÊNCIA (SERÃO UTILIZADOS COMO CRÉDITO NA VENDA PARA O CLIENTE)
        *******************************************************************************************************/
        //Calculo IPI
        if($mD006['D006_Flag_Isento_IPI'] == 'S'){
            $baseIPI = $valorIPI = 0;
        } else {
            if($Aliquotas['Aliquota_IPI'] > 0){
                $baseIPI = $valorTotalPreco;
                if($Aliquotas['Percentual_Reducao_IPI'] > 0){
                    $baseIPI = $baseIPI * ($Aliquotas['Percentual_Reducao_IPI']/100);
                }
            }
            $valorIPI = $baseIPI * ($Aliquotas['Aliquota_IPI']/100);
        }

        // Retira o valor st do preco, para chegar no preco liquido para base de impostos
        $valorTotalCusto = $valorTotalPreco - $valorST;

        if($mD006['D006_Flag_Somar_IPI_Base_ICMS'] == 'S'){
            $valorTotalCusto += $valorIPI;
        }

        //Calculo ICMS ST
        if($mD006['D006_Flag_Substituicao_Tributaria'] != 'S'){
            $baseST = $valorST = 0;
        } 
        // O valorST vem como parametro do metodo retornaPrecoVenda
        /*
        else {
            $baseST = ($valorTotalPreco + $valorIPI) * (($Aliquotas['ST_VA']/100)+1);

            $varA = 0;
            if($Aliquotas['Percentual_Reducao_ICMS_ST'] > 0){
                $baseST = $baseST * ((100-$Aliquotas['Percentual_Reducao_ICMS_ST'])/100);
            }
            $varA = $baseST * ($Aliquotas['ST_ICMS_Interno']/100);

            $varB = $valorTotalCusto;
            if($Aliquotas['Percentual_Reducao_ICMS'] > 0){
                $varB = $valorTotalCusto * ((100-$Aliquotas['Percentual_Reducao_ICMS'])/100);
            }
            $varB = $varB * ($Aliquotas['ICMS_Inter_Estadual']/100);

            $valorST = $varA - $varB;
        } */
        $aliquotaST = ($valorST/$valorTotalPreco)*100;
        $aliquotaST = ($aliquotaST < 0) ? 0 : $aliquotaST;

        //Calculo ICMS
        if($mD006['D006_Flag_Isento_ICMS'] == 'S'){
            $baseICMS = $valorICMS = 0;
        } else {
            if($Aliquotas['Aliquota_ICMS'] > 0){
                if($Aliquotas['Percentual_Reducao_ICMS'] > 0){
                    $baseICMS = $valorTotalCusto * ((100-$Aliquotas['Percentual_Reducao_ICMS'])/100);
                } else {
                    $baseICMS = $valorTotalCusto;
                }
                $valorICMS = $baseICMS * ($Aliquotas['Aliquota_ICMS']/100);
            }
        }


        //Calculo PIS
        if($mD006['D006_Flag_Isento_PIS'] == 'S'){
            $basePIS = $valorPIS = 0;
        } else {
            $valorPIS = 0;
            if($mD006['D006_Flag_Forma_Calculo_PIS'] == 'L'){
                $basePIS = $valorTotalPreco - $precoVenda[4];
            } else {
                $basePIS = $valorTotalPreco;
            }
            if($Aliquotas['Aliquota_PIS'] > 0){
                $valorPIS = $basePIS * ($Aliquotas['Aliquota_PIS']/100);
            }
        }
        
        //Calculo COFINS
        if($mD006['D006_Flag_Isento_COFINS'] == 'S'){
            $baseCOFINS = $valorCOFINS = 0;
        } else {
            $valorCOFINS = 0;
            if($mD006['D006_Flag_Forma_Calculo_COFINS'] == 'L'){
                $baseCOFINS = $valorTotalPreco - $precoVenda[4];
            } else {
                $baseCOFINS = $valorTotalPreco;
            }
            if($Aliquotas['Aliquota_COFINS'] > 0){
                $valorCOFINS = $baseCOFINS * ($Aliquotas['Aliquota_COFINS']/100);
            }
        }
        

      //T006_Valor_Base_ICMS=If(T006_Aliquota_ICMS>0,T006_Valor_Total_Custo_Final*if(T006_Percentual_Reducao_ICMS>0,((100-T006_Percentual_Reducao_ICMS)/100),1),0),
      //T006_Valor_ICMS=T006_Valor_Base_ICMS*(T006_Aliquota_ICMS/100),
      
      //T006_Valor_Base_PIS=if(T006_Flag_Isento_PIS='S',0,if(xD006_Flag_Forma_Calculo_PIS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
      //T006_Valor_PIS=if(T006_Aliquota_PIS>0,T006_Valor_Base_PIS*(T006_Aliquota_PIS/100),0),
      //T006_Valor_Base_COFINS=if(T006_Flag_Isento_COFINS='S',0,if(xD006_Flag_Forma_Calculo_COFINS='L',T006_Valor_Total_Preco-T006_Valor_Total_Custo,T006_Valor_Total_Preco)),
      //T006_Valor_COFINS=if(T006_Aliquota_COFINS>0,T006_Valor_Base_COFINS*(T006_Aliquota_COFINS/100),0),




        //$precoVenda[1][] = array('campo' => 'Custo Bruto', 'valor' => $mD009['D009_Valor_Custo_Unitario'], 'operacao' => '=');
        //$out = array_splice($precoVenda[1], count($precoVenda[1])-1, 1);
        //array_splice($precoVenda[1], 1, 0, $out);        
        
        //$precoVenda[1][] = array('campo' => gCorrigeNumero($Aliquotas['Aliquota_IPI']).'% IPI venda', 'valor' => gCorrigeNumero($valorIPI,5), 'operacao' => '+');
        //$precoVenda[1][] = array('campo' => gCorrigeNumero($aliquotaST,4).'% ST venda', 'valor' => gCorrigeNumero($valorST,5), 'operacao' => '+');
        //$valorTotalPreco += $valorIPI;
        //$valorTotalPreco += $valorST;
        //$precoVenda[1][] = array('campo' => 'PREÇO BRUTO', 'valor' => gCorrigeNumero($valorTotalPreco,5), 'operacao' => '=', 'tipo' => 'total', 'css' => 'font-weight: bold; font-size: 12px;border-bottom: 1px solid #000;');

        /*******************************************************************************
        * CREDITOS DE IMPOSTOS NA TRANSFERÊNCIA DA MERCADORIA
        *******************************************************************************/
        $D006_Id_Venda  = $VEN001->retornaCFOP($mCab['D024_Id'], $mD009_Atual['D009_Id'], $g['empresaAtual'], 2);
        $AliquotasVenda = $VEN001->retornaAliquotas($mD009_Atual['D009_Id'], $T003_Id, $T005_Id, $T007_Id, false, false, false, $D006_Id_Venda, $mCab['D024_Id']);
        
        $sqlD006_Venda  = mysql_query("SELECT * FROM D006 WHERE D006_Id = '{$D006_Id_Venda}'");
        $mD006_Venda    = mysql_fetch_array($sqlD006_Venda);


        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['CFOP_Utilizado'] . 'CFOP Utilizado (venda)',
            'valor' => '('.$mD006['D006_Id'].') '.$mD006['D006_Codigo_CFOP'],
            'operacao' => '',
            'exibir' => 'S'
        );

        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006_Venda['D006_Flag_Deb_Crd_ST'] == 'S' && $mD006['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $custoST      = $valorST;
            $percentualST = number_format($aliquotaST, 5, ',', '.');
        }  
        $valorTotalPreco -= $custoST;
        $precoVenda[1][] = array('campo' => gCorrigeNumero($percentualST,4) . '% ST', 'valor' => gCorrigeNumero($custoST,5), 'operacao' => '-');
    

        // ICMS
        $icmsCompra = 0;
        if ($mD006_Venda['D006_Flag_Deb_Crd_ICMS'] == 'S') {
            $icmsCompra   = $valorICMS;
            $aliquotaICMS = number_format($Aliquotas['Aliquota_ICMS'], 2, ',', '.');
        }
        $valorTotalPreco -= $icmsCompra;
        $precoVenda[1][] = array('campo' => $aliquotaICMS . '% ICMS compra', 'valor' => gCorrigeNumero($icmsCompra,5), 'operacao' => '-');


        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S') {
            $pisCompra   = $valorPIS;
            $aliquotaPIS = number_format($Aliquotas['Aliquota_PIS'], 2, ',', '.');
        }
        $valorTotalPreco -= $pisCompra;
        $precoVenda[1][] = array('campo' => $aliquotaPIS . '% PIS compra', 'valor' => gCorrigeNumero($pisCompra,5), 'operacao' => '-');


        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S') {
            $cofinsCompra   = $valorCOFINS;
            $aliquotaCOFINS = number_format($Aliquotas['Aliquota_COFINS'], 2, ',', '.');
        }
        $valorTotalPreco -= $cofinsCompra;
        $precoVenda[1][] = array('campo' => $aliquotaCOFINS . '% COFINS compra', 'valor' => gCorrigeNumero($cofinsCompra,5), 'operacao' => '-');
        
        //ICF
        $ICF = 1;
        if ($mD009_Atual['D009_ICF_1'] > 0) {
            $ICF = $mD009_Atual['D009_ICF_1'];
        }

        $valorTotalPreco *= $ICF;
        $custoProduto = $valorTotalPreco;
        $precoVenda[4] = $custoProduto;
        $precoVenda[1][] = array('campo' => 'ICF', 'valor' => gCorrigeNumero($ICF,5), 'operacao' => '*');
        $precoVenda[1][] = array(
            'campo' => 'CUSTO LÍQUIDO',
            'valor' => gCorrigeNumero($custoProduto,5),
            'operacao' => '=',
            'tipo' => 'total',
            'css' => 'font-weight: bold; font-size: 12px; border-bottom: 1px solid #000;'
        );
       
        // IPV 
        $IPV = 0;
        if($mD009_Atual['D009_IPV_' . $listaPreco] > 0) {
            $IPV = $mD009_Atual['D009_IPV_' . $listaPreco];
        } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
            switch ($listaPreco) {
                case 1:
                    $IPV = $g['C020']['C020_IPV_Revenda'];
                break;
                case 2:
                    $IPV = $g['C020']['C020_IPV_Atacado'];
                break;
                case 3:
                    $IPV = $g['C020']['C020_IPV_Varejo'];
                break;
                if ($IPV <= 0) {
                    $IPV = 1;
                }
            }
        }
       
       
        $valorTotalPreco *= $IPV;
        $precoVenda[1][] = array('campo' => 'IPV', 'valor' => gCorrigeNumero($IPV,5), 'operacao' => '*');

        // ICMS
        $icmsVenda = 0;
        $porcentagem = 100 / (100 - $AliquotasVenda['Aliquota_ICMS']);
        $icmsVenda   = ($valorTotalPreco * $porcentagem) - $valorTotalPreco;
        if ($AliquotasVenda['Aliquota_ICMS'] > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $valorTotalPreco += $icmsVenda;
        }
        $AliquotasVenda['Aliquota_ICMS'] = number_format($AliquotasVenda['Aliquota_ICMS'], 2, ',', '.');
        
        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['Aliquota_ICMS'] . '% ICMS venda',
            'valor' => gCorrigeNumero($icmsVenda,5),
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $porcentagem = 100 / (100 - $AliquotasVenda['Aliquota_PIS']);
        $pisVenda   = ($valorTotalPreco * $porcentagem) - $valorTotalPreco;
        if ($AliquotasVenda['Aliquota_PIS'] > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $valorTotalPreco += $pisVenda;
        }
        $AliquotasVenda['Aliquota_PIS'] = number_format($AliquotasVenda['Aliquota_PIS'], 2, ',', '.');
        
        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['Aliquota_PIS'] . '% PIS venda',
            'valor' => gCorrigeNumero($pisVenda,5),
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $porcentagem = 100 / (100 - $AliquotasVenda['Aliquota_COFINS']);
        $cofinsVenda   = ($valorTotalPreco * $porcentagem) - $valorTotalPreco;
        if ($AliquotasVenda['Aliquota_COFINS'] > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $valorTotalPreco += $cofinsVenda;
        }
        $AliquotasVenda['Aliquota_COFINS'] = number_format($AliquotasVenda['Aliquota_COFINS'], 2, ',', '.');
        
        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['Aliquota_COFINS'] . '% COFINS venda',
            'valor' => gCorrigeNumero($cofinsVenda,5),
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IRPJ
        $irpjVenda = 0;
        $porcentagem = 100 / (100 - $AliquotasVenda['Aliquota_IRPJ']);
        $irpjVenda   = ($valorTotalPreco * $porcentagem) - $valorTotalPreco;
        if ($AliquotasVenda['Aliquota_IRPJ'] > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') {
            $valorTotalPreco += $irpjVenda;
        }
        $AliquotasVenda['Aliquota_IRPJ'] = number_format($AliquotasVenda['Aliquota_IRPJ'], 2, ',', '.');
        
        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['Aliquota_IRPJ'] . '% IRPJ venda',
            'valor' => gCorrigeNumero($irpjVenda,5),
            'operacao' => ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarIRPJPrecoVenda']
        );


        // CSLL
        $csllVenda = 0;
        $porcentagem = 100 / (100 - $AliquotasVenda['Aliquota_CSLL']);
        $csllVenda   = ($valorTotalPreco * $porcentagem) - $valorTotalPreco;
        if ($AliquotasVenda['Aliquota_CSLL'] > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') {
            $valorTotalPreco += $csllVenda;
        }
        $AliquotasVenda['Aliquota_CSLL'] = number_format($AliquotasVenda['Aliquota_CSLL'], 2, ',', '.');
        
        $precoVenda[1][] = array(
            'campo' => $AliquotasVenda['Aliquota_CSLL'] . '% CSLL venda',
            'valor' => gCorrigeNumero($csllVenda,5),
            'operacao' => ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCSLLPrecoVenda']
        );



        /*****************************************************************************************************************************************************
        CALCULO DO IPV DOS IMPOSTOS ATUALIZADOS


        $Aliquota_IPV = round(((($IPV-1)*10000)/($IPV*10000))*100, 2);
        $Aliquota_IPV_Total = $Aliquota_IPV;
        $Aliquota_IPV_Total += ($AliquotasVenda['Aliquota_ICMS'] > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') ? $AliquotasVenda['Aliquota_ICMS'] : 0;
        $Aliquota_IPV_Total += ($AliquotasVenda['Aliquota_PIS'] > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') ? $AliquotasVenda['Aliquota_PIS'] : 0;
        $Aliquota_IPV_Total += ($AliquotasVenda['Aliquota_COFINS'] > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $AliquotasVenda['Aliquota_COFINS'] : 0;
        $Aliquota_IPV_Total += ($AliquotasVenda['Aliquota_IRPJ'] > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') ? $AliquotasVenda['Aliquota_IRPJ'] : 0;
        $Aliquota_IPV_Total += ($AliquotasVenda['Aliquota_CSLL'] > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') ? $AliquotasVenda['Aliquota_CSLL'] : 0;

        $IPV_Total = 100 / (100 - $Aliquota_IPV_Total);
        $precoBase = $preco * $IPV_Total;
        
        $ipvVenda = ($precoBase * ($Aliquota_IPV/100));
        $preco *= $IPV;
        log('6 IPV: ' . gCorrigeNumero($Aliquota_IPV));
        $demonstrativo[] = array(
            'campo' => gCorrigeNumero($Aliquota_IPV) . '% IPV ' . gCorrigeNumero($IPV,4),
            'valor' => gCorrigeNumero($ipvVenda,5),
            'operacao' => '+'
        );

         // ICMS
        $icmsVenda = 0;
        $icmsVenda   = ($precoBase * ($AliquotasVenda['Aliquota_ICMS']/100));
        $AliquotasVenda['Aliquota_ICMS'] = number_format($AliquotasVenda['Aliquota_ICMS'], 2, ',', '.');
        log('7 ICMS VENDA: ' . $icmsVenda . ' - ' . $Aliquota_ICMS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => gCorrigeNumero($icmsVenda,5),
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $pisVenda   = ($precoBase * ($AliquotasVenda['Aliquota_PIS']/100));
        $AliquotasVenda['Aliquota_PIS'] = number_format($AliquotasVenda['Aliquota_PIS'], 2, ',', '.');
        log('8 PIS VENDA: ' . $pisVenda . ' - ' . $Aliquota_PIS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => gCorrigeNumero($pisVenda,5),
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $cofinsVenda   = ($precoBase * ($AliquotasVenda['Aliquota_COFINS']/100));
        $AliquotasVenda['Aliquota_COFINS'] = number_format($AliquotasVenda['Aliquota_COFINS'], 2, ',', '.');
        log('9 COFINS VENDA: ' . $cofinsVenda . ' - ' . $Aliquota_COFINS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => gCorrigeNumero($cofinsVenda,5),
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IRPJ
        $irpjVenda = 0;
        $irpjVenda   = ($precoBase * ($AliquotasVenda['Aliquota_IRPJ']/100));
        $AliquotasVenda['Aliquota_IRPJ'] = number_format($AliquotasVenda['Aliquota_IRPJ'], 2, ',', '.');
        log('9 IRPJ VENDA: ' . $irpjVenda . ' - ' . $Aliquota_IRPJ);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_IRPJ . '% IRPJ venda',
            'valor' => gCorrigeNumero($irpjVenda,5),
            'operacao' => ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarIRPJPrecoVenda']
        );


        // CSLL
        $csllVenda = 0;
        $csllVenda   = ($precoBase * ($AliquotasVenda['Aliquota_CSLL']/100));
        $AliquotasVenda['Aliquota_CSLL'] = number_format($AliquotasVenda['Aliquota_CSLL'], 2, ',', '.');
        log('9 CSLL VENDA: ' . $csllVenda . ' - ' . $Aliquota_CSLL);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_CSLL . '% CSLL venda',
            'valor' => gCorrigeNumero($csllVenda,5),
            'operacao' => ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCSLLPrecoVenda']
        );

        $valorTotalPreco = $precoBase;
        *******************************************************************************************************************************************************/



        $precoVenda[1][] = array(
            'campo' => 'PREÇO VENDA',
            'valor' => gCorrigeNumero($valorTotalPreco,5),
            'operacao' => '=',
            'tipo' => 'total',
            'css' => 'font-weight: bold; font-size: 12px; border-bottom: 1px solid #000;'
        );
        $precoVenda[0] = gCorrigeNumero($valorTotalPreco, 5);
        return $precoVenda;
    }

    /**
     * retornaPrecoLoja
     *
     * @param int $D009_Id
     * @param int $d024_Id
     * @param int $D006_Id
     * @param array $aliquotas
     * @return array
     */
    public function retornaPrecoLoja($mD009) 
    {
        global $g;
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        // ATUALIZAÇÃO DO PREÇO LOJA - LUCAS E MARCELO - 05/06/2015 -----------------------------------------------------------------
        $D006_Id = ($mD009['D149_Flag_ST'] == 'S') ? $g['C031']['cfopCupomFiscalComST'] : $g['C031']['cfopCupomFiscalSemST'];
        $D024_Id = $g['C031']['codigoClienteCupomFiscal'];

        $D024 = mysql_query("SELECT D024_Flag_Lista_Preco 
                               FROM D024
                              WHERE D024_Id = '{$D024_Id}'");
        $mD024 = mysql_fetch_array($D024);
        
        $Aliquotas       = $VEN001->retornaAliquotas($mD009['D009_Id'], false, false, false, false, false, false, $D006_Id, $D024_Id);
        $dadosPrecoVenda = $this->retornaPrecoVenda($mD009['D009_Id'], $mD024['D024_Flag_Lista_Preco'], 'loja', false, $D006_Id, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $preco           = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativo   = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        // --------------------------------------------------------------------------------------------------------------------------
        return array('preco' => $preco, 'demonstrativo' => $demonstrativo);
    }

    /**
     * retornaPrecoVenda
     *
     * @param String $D009_Id
     * @param String $listaPreco
     * @param String $D024_Id
     * @param String $atualizarDadosCompra
     * @param String $cfop
     * @param String $Aliquota_ICMS
     * @param String $T219_Id
     * @param String $T225_Id
     * @return array
     */
    public function retornaPrecoVenda($D009_Id, $listaPreco = null, $D024_Id = null, $atualizarDadosCompra = false, $cfop = null, $Aliquota_ICMS = 0, $T219_Id = 0, $T225_Id = 0, $casasDecimaisPreco = 4, $Aliquota_PIS = 0, $Aliquota_COFINS = 0, $Aliquota_IPI = 0, $ST_VA = 0, $Percentual_Reducao_ICMS_ST = 0, $ST_ICMS_Interno = 0, $Percentual_Reducao_ICMS = 0, $ICMS_Inter_Estadual = 0, $Aliquota_IRPJ = 0, $Aliquota_CSLL = 0, $valorFreteCusto = 0,$TotalPorcentagemIPV = 0,$TotalPorcentagemICF = 0, $C004_Id = false, $ratearFreteVendaNoCusto = '')
    {
    
        global $g;
        if (($listaPreco == null) && ($D024_Id == null)) {
            return "Contate o suporte técnico. É necessário informar a lista preço ou o D024_Id.";
        }

        $demonstrativo = array();
        $custoMedio = 0;
        $quantidadeEstoque = 0;
        $precoDinamico = true;

        // se receber o C004_Id (exemplo na troca de empresas) usa o C004_Id, caso não usa o empresaAtual
        $C004_Id = ($C004_Id === false) ? $g['empresaAtual'] : $C004_Id;

        if($g['empresaAtual'] != $C004_Id){
            // no caso de troca de empresa, faz o select nas globais para ver quais impostos somam no preço de venda
            $C031 = mysql_query("SELECT C031_Valor, 
                                        C031_Campo 
                                   FROM C031 
                                  WHERE C031_C004_Id = '{$C004_Id}'
                                    AND (C031_Campo  = 'somarICMSPrecoVenda'   OR
                                        C031_Campo   = 'somarPISPrecoVenda'	   OR
                                        C031_Campo   = 'somarCOFINSPrecoVenda' OR
                                        C031_Campo   = 'somarIRPJPrecoVenda'   OR
                                        C031_Campo   = 'somarCSLLPrecoVenda')");

            while($mC031 = mysql_fetch_assoc($C031)){
                $g['C031'][$mC031['C031_Campo']] = $mC031['C031_Valor'];
            } 
        }      
        
        // Empresa
        $C004  = mysql_query("SELECT C004_Id,C004_Flag_Isento_ICMS,C004_Flag_Empresa_Simples FROM C004 WHERE C004_Id = '{$C004_Id}'");
        $mC004 = mysql_fetch_array($C004);

        // Cliente/Fornecedor
        $D024  = mysql_query("SELECT D024_Id,D024_Id_Matriz,D024_Flag_Lista_Preco,D024_IPV_1 FROM D024 WHERE D024_Id='{$D024_Id}'");
        $mD024 = mysql_fetch_array($D024);

        $D024_Id_Tabela_Preco_Cliente = $D024_Id;
        if(!empty($mD024['D024_Id_Matriz'])){
            $D024_Id_Tabela_Preco_Cliente = $mD024['D024_Id_Matriz'];
        }

        // Se o listaPreco for branco, irá buscar o lista preço do D024, caso ele esteja preenchido irá usar o que veio, ignorando o D024

        if($listaPreco == null) {
            $listaPreco = $mD024['D024_Flag_Lista_Preco'];
        }

        // Produto
        $sql   = <<<EOT
            SELECT  D009_T001_Id,
                    D049_Origem_Mercadoria,
                    D009_Valor_Custo_Compra,
                    D009_Flag_Custo_Utilizado,
                    D001_Preco_1,
                    D001_Preco_2,
                    D001_Preco_3,
                    D001_Preco_4,
                    D009_Preco_1,
                    D009_Preco_2,
                    D009_Preco_3,
                    D009_Preco_4,
                    D009A_Preco_5,
                    D009_Valor_Custo_Ultima_Compra,
                    D009_Quantidade_Estoque_Liquido,
                    D006_Flag_Substituicao_Tributaria,
                    T001_Valor_ST,
                    T001_Percentual_ST,
                    D069_Valor_ICMS_Substituicao_Retencao,
                    D069_Percentual_ST,
                    T001_Valor_ICMS,
                    T001_Aliquota_ICMS,
                    D069_Valor_ICMS,
                    D069_Aliquota_ICMS,
                    T001_Valor_PIS,
                    T001_Aliquota_PIS,
                    D069_Valor_PIS,
                    D069_Aliquota_PIS,
                    T001_Valor_COFINS,
                    T001_Aliquota_COFINS,
                    D069_Valor_COFINS,
                    D069_Aliquota_COFINS,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_IPV_4,
                    D009A_IPV_5,
            IFNULL(D009_Origem_Mercadoria,'') as D009_Origem_Mercadoria 
            FROM D009 
            JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D009A ON D009_Id=D009A_D009_Id
            LEFT JOIN T001 ON T001_Id=D009_T001_Id
            LEFT JOIN D006 ON D006_Id=T001_D006_Id
            LEFT JOIN D069 ON D069_Id=D009_D069_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE D009_Id='{$D009_Id}'
EOT;
        log("Berenice: ".$sql);
        $D009  = mysql_query($sql);
        $mD009 = mysql_fetch_array($D009);
        $T001_Id = $mD009['D009_T001_Id'];

        //Se a origem da mercadoria estiver preenchida no D009, atualiza a variavel origem
        if($mD009['D009_Origem_Mercadoria'] != ''){
            $mD009['D049_Origem_Mercadoria'] = $mD009['D009_Origem_Mercadoria'];
        }

        log("Custo Compra: {$mD009['D009_Valor_Custo_Compra']}");
        $custoCadastroProduto = $mD009['D009_Valor_Custo_Compra'];

        $precoUtilizado = 'H';
        if($mD009['D009_Flag_Custo_Utilizado'] == 'T') {
            $precoUtilizado = 'T';
        }

        //Preenche preço tabelado por D001
        if($listaPreco > 0 && $mD009['D001_Preco_' . $listaPreco] > 0){
            $precoTabelado = $mD009['D001_Preco_' . $listaPreco];
        }

        //Preenche preço tabelado por D009
        if($listaPreco > 0 && $mD009['D009_Preco_' . $listaPreco] > 0){
            $precoTabelado = $mD009['D009_Preco_' . $listaPreco];
        }
        //Preenche preço tabelado por D009A - Criado para suportar mais preços apartir do preco5
        if($listaPreco >= 5 && $mD009['D009A_Preco_' . $listaPreco] > 0){
            $precoTabelado = $mD009['D009A_Preco_' . $listaPreco];
        }
        
        //Verifica se caso tenha cotação ou ordem de compra, já foi dado entrada da nota fiscal, para que o preço utilizado seja da Nota
        $possuiNFEntrada = false;
        log("T219: " . $T219_Id . " - T225_Id: " . $T225_Id);
        if ($T219_Id > 0 || $T225_Id > 0) {
            $zerarT225_Id = false;
            if(empty($T225_Id) || $T225_Id <= 0){
                $sqlT225 = mysql_query("SELECT T225_Id FROM T225 WHERE T225_T219_Id = '{$T219_Id}'");
                if(mysql_num_rows($sqlT225) > 0){
                    $rowT225 = mysql_fetch_array($sqlT225);
                    $T225_Id = $rowT225['T225_Id'];
                    $zerarT225_Id = true;
                }
            }

            if($T225_Id > 0){
                $sqlT014 = mysql_query("SELECT T001_Id, T001_Valor_Custo_Unitario, T001_Valor_Custo_Medio_Unitario, T001_Flag_Atualiza_Custo FROM T014 LEFT JOIN T001 ON T001_T014_Id = T014_Id WHERE T014_T225_Id = '{$T225_Id}' AND T001_Flag_Operacao != 'C'");
                if(mysql_num_rows($sqlT014) > 0){
                    log("SELECT T001_Id, T001_Valor_Custo_Unitario FROM T014 LEFT JOIN T001 ON T001_T014_Id = T014_Id WHERE T014_T225_Id = '{$T225_Id}' AND T001_Flag_Operacao != 'C'");
                    $mSqlT014 = mysql_fetch_array($sqlT014);
                    if ($mD009['D009_Flag_Custo_Utilizado']=='M' && $mSqlT014['T001_Flag_Atualiza_Custo'] != 'N'){
                        $mD009['D009_Valor_Custo_Compra'] = $mSqlT014['T001_Valor_Custo_Medio_Unitario'];
                        $mD009['D009_Valor_Custo_Ultima_Compra'] = $mSqlT014['T001_Valor_Custo_Medio_Unitario'];
                    } else {
                        $mD009['D009_Valor_Custo_Compra'] = $mSqlT014['T001_Valor_Custo_Unitario'];
                        $mD009['D009_Valor_Custo_Ultima_Compra'] = $mSqlT014['T001_Valor_Custo_Unitario'];
                    }
                    $T001_Id = $mSqlT014['T001_Id'];
                    $precoUtilizado = 'H';
                    $possuiNFEntrada = true;
                }

                $sqlT008 = mysql_query("SELECT T001_Id, T001_Valor_Custo_Unitario, T001_Valor_Custo_Medio_Unitario, T001_Flag_Atualiza_Custo FROM T008 LEFT JOIN T055 ON T055_Id=T008_T055_Id LEFT JOIN T001 ON T001_T008_Id=T008_Id WHERE T055_T225_Id = '{$T225_Id}' AND T001_Flag_Operacao != 'C'");
                if(mysql_num_rows($sqlT008) > 0){
                    log("SELECT T001_Id, T001_Valor_Custo_Unitario, T001_Valor_Custo_Medio_Unitario, T001_Flag_Atualiza_Custo FROM T008 LEFT JOIN T055 ON T055_Id=T008_T055_Id LEFT JOIN T001 ON T001_T008_Id=T008_Id WHERE T055_T225_Id = '{$T225_Id}' AND T001_Flag_Operacao != 'C'");
                    $mSqlT008 = mysql_fetch_array($sqlT008);
                    if ($mD009['D009_Flag_Custo_Utilizado']=='M' && $mSqlT008['T001_Flag_Atualiza_Custo'] != 'N'){
                        $mD009['D009_Valor_Custo_Compra'] = $mSqlT008['T001_Valor_Custo_Medio_Unitario'];
                        $mD009['D009_Valor_Custo_Ultima_Compra'] = $mSqlT008['T001_Valor_Custo_Medio_Unitario'];
                    } else {
                        $mD009['D009_Valor_Custo_Compra'] = $mSqlT008['T001_Valor_Custo_Unitario'];
                        $mD009['D009_Valor_Custo_Ultima_Compra'] = $mSqlT008['T001_Valor_Custo_Unitario'];
                    }
                    $T001_Id = $mSqlT008['T001_Id'];
                    $precoUtilizado = 'H';
                    $possuiNFEntrada = true;
                }

            }

            //T225_id veio em branco, porém foi encontrado pela cotação, se não entrou nota ainda, zeramos o T225 para que o preço não seja atualizado pela ordem de compra
            if($zerarT225_Id == true){
                $T225_Id = 0;
            }
        }

        // Usar cotação compra
        if ($T219_Id > 0 && $possuiNFEntrada == false) {
            $sql   = <<<EOT
                SELECT 
                    T219_Valor_Custo_Unitario as D009_Valor_Custo_Compra,
                    999 as D009_Quantidade_Estoque,
                    T219_Valor_ST/T219_Quantidade as T001_Valor_ST,
                    (round(T219_Valor_ST/T219_Quantidade,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI/T219_Quantidade,2)))*100 as T001_Percentual_ST,
                    T219_Valor_ICMS/T219_Quantidade as T001_Valor_ICMS,
                    T219_Aliquota_ICMS as T001_Aliquota_ICMS,
                    T219_Valor_PIS/T219_Quantidade as T001_Valor_PIS,
                    T219_Aliquota_PIS as T001_Aliquota_PIS,
                    T219_Valor_COFINS/T219_Quantidade as T001_Valor_COFINS,
                    T219_Aliquota_COFINS as T001_Aliquota_COFINS,
                    T219_T218_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T219_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T219_Quantidade_Estoque,
                    T006_Valor_Custo_Unitario,
                    T219_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T004_T219_Id,0) AS T004_T219_Id,
                    T219_Valor_Custo_Medio_Unitario,
                    T219_Valor_Custo_Momento_Compra
                FROM T219
                LEFT JOIN T004 ON T004_Id=T219_T004_Id
                LEFT JOIN T006 ON T006_Id=T219_T006_Id
                LEFT JOIN D006 ON D006_Id=T219_D006_Id
                JOIN D009 ON D009_Id=T219_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T219_Id='{$T219_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Cotação OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T219_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }

                if(($mD009['T006_T219_Id'] > 0 || $mD009['T004_T219_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T219_Valor_Custo_Momento_Compra']*$mD009['T219_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T219_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T219_Quantidade']+$quantidadeEstoque);
            }
            $obsCusto = "(Cot {$mD009['T219_T218_Id']})";
            $precoUtilizado = 'C';
        }
        
        // Usar ordem de compra
        if ($T225_Id > 0 && $possuiNFEntrada == false) {
            $sql   = <<<EOT
                SELECT 
                    T225_Valor_Custo_Unitario AS D009_Valor_Custo_Compra,
                    999 AS D009_Quantidade_Estoque,
                    T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade AS T001_Valor_ST,
                    (ROUND(T225_Valor_ICMS_Substituicao_Retencao/T225_Quantidade,2)/(ROUND(T225_Valor_Total_Preco,2)+ROUND(T225_Valor_IPI/T225_Quantidade,2)))*100 AS T001_Percentual_ST,
                    T225_Valor_ICMS/T225_Quantidade AS T001_Valor_ICMS,
                    T225_Aliquota_ICMS AS T001_Aliquota_ICMS,
                    T225_Valor_PIS/T225_Quantidade AS T001_Valor_PIS,
                    T225_Aliquota_PIS AS T001_Aliquota_PIS,
                    T225_Valor_COFINS/T225_Quantidade AS T001_Valor_COFINS,
                    T225_Aliquota_COFINS AS T001_Aliquota_COFINS,
                    T225_T224_Id,
                    D009_IPV_1,
                    D009_IPV_2,
                    D009_IPV_3,
                    D009_ICF_1,
                    D009_ICF_2,
                    D009_ICF_3,
                    D009_Valor_Custo_Unitario,
                    D009_Quantidade_Estoque_Liquido,
                    T225_Quantidade,
                    IFNULL(T006_Id,0) AS T006_Id,
                    T006_Quantidade_Estoque,
                    T225_Quantidade_Estoque,
                    T006_Valor_Custo_Unitario,
                    T225_Flag_Origem_Produto as D049_Origem_Mercadoria,
                    D006_Flag_Substituicao_Tributaria,
                    IFNULL(T006_T219_Id,0) AS T006_T219_Id,
                    IFNULL(T006_T225_Id,0) AS T006_T225_Id,
                    T225_Valor_Custo_Medio_Unitario,
                    T225_Valor_Custo_Momento_Compra
                FROM T225
                LEFT JOIN T006 ON T006_T225_Id=T225_Id OR T006_T219_Id=T225_T219_Id
                LEFT JOIN D006 ON T225_D006_Id=D006_Id
                JOIN D009 ON D009_Id=T225_D009_Id 
                JOIN D049 ON D049_Id=D009_D049_Id
                WHERE T225_Id='{$T225_Id}'
EOT;
            $D009  = mysql_query($sql);
            $mD009 = mysql_fetch_array($D009);
            log("Custo Compra OC: {$mD009['D009_Valor_Custo_Compra']}");
            if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S'){
                $custoMedioCotacao = $mD009['D009_Valor_Custo_Compra']*$mD009['T225_Quantidade'];
                $custoMedio = $custoCadastroProduto*$mD009['D009_Quantidade_Estoque_Liquido'];
                $quantidadeEstoque = $mD009['D009_Quantidade_Estoque_Liquido'];
                if($mD009['T006_Id'] > 0){
                    $custoMedio = $custoCadastroProduto*$mD009['T006_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T006_Quantidade_Estoque'];
                }

                if(($mD009['T006_T219_Id'] > 0 || $mD009['T006_T225_Id'] > 0) && $atualizarDadosCompra == false){
                    $custoMedio = $mD009['T225_Valor_Custo_Momento_Compra']*$mD009['T225_Quantidade_Estoque'];
                    $quantidadeEstoque = $mD009['T225_Quantidade_Estoque'];
                }
                $custoMedio = ($custoMedioCotacao+$custoMedio)/($mD009['T225_Quantidade']+$quantidadeEstoque);
            }
            $obsCusto = "(OC {$mD009['T225_T224_Id']})";
            $precoUtilizado = 'OC';
        }
        
        $origemMercadoria = $mD009['D049_Origem_Mercadoria'];

        // CFOP
        $D006  = mysql_query("SELECT    D006_Flag_Deb_Crd_ST,
                                        D006_Flag_Deb_Crd_ICMS,
                                        D006_Flag_Deb_Crd_PIS,
                                        D006_Flag_Deb_Crd_COFINS,
                                        D006_Percentual_Reducao_ICMS,
                                        D006_Flag_Somar_Fator_ICMS_Dif_Base,
                                        D006_Flag_Substituicao_Tributaria 
                FROM D006 WHERE D006_Id='{$cfop}'");
        $mD006 = mysql_fetch_array($D006);
        
        /*
         * Calculo do preço de venda
         */
        // Custo compra
        if($g['C031']['atualizarCustoVendaComCustoMedio'] == 'S' && $quantidadeEstoque > 0 && $custoMedio > 0){
            $mD009['D009_Valor_Custo_Compra'] = $custoMedio;
            log("Entrou custo médio = Custo médio = " . $custoMedio);
        }
        
        $preco = $mD009['D009_Valor_Custo_Compra'];
        
        log("Preço = " . $preco . " D009_Valor_Custo_Compra = " . $mD009['D009_Valor_Custo_Compra']);
        //Caso a lista de preço seja "T", para uma transferencia, utilizamos o custo da ultima compra
        if($listaPreco == 'T'){
            $preco = $mD009['D009_Valor_Custo_Ultima_Compra'];
            $precoUtilizado = 'H';
        }

        //$demonstrativo[] = array(
        //    'campo' => 'Custo Compra ' . $obsCusto,
        //    'valor' => gCorrigeNumero($mD009['D009_Valor_Custo_Compra'],5),
        //    'operacao' => '=',
        //    'tipo' => 'total'
        //);
        
        // ST - Se o Estado for diferente, a empresa 'se credita', então retira a ST do preço 
        $custoST = 0;
        if ($mD006['D006_Flag_Deb_Crd_ST'] == 'S' && $mD009['D006_Flag_Substituicao_Tributaria'] == 'S' && $mC004['C004_Flag_Isento_ICMS']!=1) {
            //Se tiver cotação, ordem de compra ou o preço utilizado não é tabela
            if ($T219_Id > 0 || $T225_Id > 0 || $precoUtilizado != 'T') {
                $custoST      = $mD009['T001_Valor_ST'];
                $percentualST = number_format($mD009['T001_Percentual_ST'], 5, ',', '.');
            } else {
                $custoST      = $mD009['D069_Valor_ICMS_Substituicao_Retencao'];
                $percentualST = number_format($mD009['D069_Percentual_ST'], 5, ',', '.');
            }
        }
        log('1 ST COMPRA: ' . $custoST . ' - ' . $percentualST);
        $preco -= $custoST;
        $demonstrativo[] = array(
            'campo' => $percentualST . '% ST',
            'valor' => gCorrigeNumero($custoST,5),
            'operacao' => '-'
        );
        
        // ICMS
        $icmsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_ICMS'] == 'S' && $mC004['C004_Flag_Isento_ICMS']!=1) {
            //Se tiver cotação, ordem de compra ou o preço utilizado não é tabela
            if ($T219_Id > 0 || $T225_Id > 0 || $precoUtilizado != 'T') {
                $icmsCompra   = $mD009['T001_Valor_ICMS'];
                $aliquotaICMS = number_format($mD009['T001_Aliquota_ICMS'], 2, ',', '.');
            } else {
                $icmsCompra   = $mD009['D069_Valor_ICMS'];
                $aliquotaICMS = number_format($mD009['D069_Aliquota_ICMS'], 2, ',', '.');
            }
        }
        $preco -= $icmsCompra;
        log('2 ICMS COMPRA: ' . $icmsCompra . ' - ' . $aliquotaICMS);
        $demonstrativo[] = array(
            'campo' => $aliquotaICMS . '% ICMS compra',
            'valor' => gCorrigeNumero($icmsCompra,5),
            'operacao' => '-'
        );


        // PIS
        $pisCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_PIS'] == 'S' && $mC004['C004_Flag_Isento_ICMS']!=1) {
            //Se tiver cotação, ordem de compra ou o preço utilizado não é tabela
            if ($T219_Id > 0 || $T225_Id > 0 || $precoUtilizado != 'T') {
                $pisCompra   = $mD009['T001_Valor_PIS'];
                $aliquotaPIS = number_format($mD009['T001_Aliquota_PIS'], 2, ',', '.');
            } else {
                $pisCompra   = $mD009['D069_Valor_PIS'];
                $aliquotaPIS = number_format($mD009['D069_Aliquota_PIS'], 2, ',', '.');
            }
        }

        $preco -= $pisCompra;
        log('3 PIS COMPRA: ' . $pisCompra . ' - ' . $aliquotaPIS);
        $demonstrativo[] = array(
            'campo' => $aliquotaPIS . '% PIS compra',
            'valor' => gCorrigeNumero($pisCompra,5),
            'operacao' => '-'
        );


        // COFINS
        $cofinsCompra = 0;
        if ($mD006['D006_Flag_Deb_Crd_COFINS'] == 'S' && $mC004['C004_Flag_Isento_ICMS']!=1) {
            //Se tiver cotação, ordem de compra ou o preço utilizado não é tabela
            if ($T219_Id > 0 || $T225_Id > 0 || $precoUtilizado != 'T') {
                $cofinsCompra   = $mD009['T001_Valor_COFINS'];
                $aliquotaCOFINS = number_format($mD009['T001_Aliquota_COFINS'], 2, ',', '.');
            } else {
                $cofinsCompra   = $mD009['D069_Valor_COFINS'];
                $aliquotaCOFINS = number_format($mD009['D069_Aliquota_COFINS'], 2, ',', '.');
            }
        }

        $preco -= $cofinsCompra;
        log('4 COFINS COMPRA: ' . $cofinsCompra . ' - ' . $aliquotaCOFINS);
        $demonstrativo[] = array(
            'campo' => $aliquotaCOFINS . '% COFINS compra',
            'valor' => gCorrigeNumero($cofinsCompra,5),
            'operacao' => '-'
        );

        // ICF
        $ICF = 1;
        if ($mD009['D009_ICF_1'] > 0) {
            $ICF = $mD009['D009_ICF_1'];
        }

        if ($TotalPorcentagemICF>0 || $TotalPorcentagemICF<0){
            log("ICF: ". $ICF);
            //**********************************************
            // Atualiza IPV com novas porcentagens digitadas na tela de montagem de ICF
            //**********************************************
            
            //Pega o valor original do IPV e transforma novamente em uma porcentagem
            $PorcentagemICF   = ceil(((100*$ICF)-100)/$ICF);
       
            log("$PorcentagemICF: ". $PorcentagemICF);
            //Soma a porcentagem original com a digitada na tela de montagem de IPV
            $PorcentagemTotal = $PorcentagemICF+$TotalPorcentagemICF;

             log("$PorcentagemTotal: ". $PorcentagemTotal);

            //Transforma tudo novamente em um IPV
            $ICF = (100/(100-$PorcentagemTotal)); 
             log("novo ICF: ". $ICF);

            //**************************************************
        }

        $preco *= $ICF;
        log('5 ICF: ' . $ICF);
        $custoProduto = $preco;
        log('Custo: ' . $custoProduto);
        $demonstrativo[] = array(
            'campo' => 'ICF',
            'valor' => gCorrigeNumero($ICF,5),
            'operacao' => '*'
        );
        
        $ratearFreteVendaNoCusto = ($ratearFreteVendaNoCusto != '') ? $ratearFreteVendaNoCusto : $g['C031']['ratearFreteVendaNoCusto'];

        // FRETE
        if ($valorFreteCusto > 0 && $ratearFreteVendaNoCusto == 'S') {
            log('5.1 FRETE: ' . $valorFreteCusto);
            $custoProduto += $valorFreteCusto;

            $demonstrativo[] = array(
                'campo' => 'Frete',
                'valor' => gCorrigeNumero($valorFreteCusto,5),
                'operacao' => '+'
            );
        }

        $demonstrativo[] = array(
            'campo' => 'CUSTO LÍQUIDO',
            'valor' => gCorrigeNumero($custoProduto,5),
            'operacao' => '=',
            'css' => 'font-weight: bold;border-bottom:1px solid #000; font-size: 12px',
            'tipo' => 'total'
        );

        if($g['C031']['custoUtilizadoNaVenda'] == 'F'){
            // Se for o custo fixo, usa o custo * ICF do cadastro do produto, sem debito e crédito do ICMS

            $custoProduto = $custoCadastroProduto * $ICF;
            $preco        = $custoProduto;

            $demonstrativo[] = array(
                'campo' => 'CUSTO CADASTRO',
                'valor' => gCorrigeNumero($custoProduto,5),
                'operacao' => '=',
                'css' => 'font-weight: bold; color: blue; font-size: 12px;',
                'tipo' => 'total'
            );
        }




        // IPV
        if($listaPreco == 'T'){
            //Caso a lista de preço informada seja "T", para uma transferência, o IPV será 1
            $IPV = 1;
        } else {
            $IPV = 0;
            if ($mD024['D024_IPV_1'] > 0) {
                $IPV = $mD024['D024_IPV_1'];
            } else if ($g['C007']['C007_IPV_1'] > 0) {
                $IPV = $g['C007']['C007_IPV_1'];
            } else if ($mD009['D009_IPV_' . $listaPreco] > 0) {
                $IPV = $mD009['D009_IPV_' . $listaPreco];
            } else if ($listaPreco >= 5) {
                $IPV = $mD009['D009A_IPV_' . $listaPreco];
            } else if ($g['C020']['C020_IPV_Revenda'] > 0) {
                switch ($listaPreco) {
                    case 1:
                        $IPV = $g['C020']['C020_IPV_Revenda'];
                        break;
                    case 2:
                        $IPV = $g['C020']['C020_IPV_Atacado'];
                        break;
                    case 3:
                        $IPV = $g['C020']['C020_IPV_Varejo'];
                        break;
                        if ($IPV <= 0) {
                            $IPV = 1;
                        }
                }
            }
        } 

        if ($TotalPorcentagemIPV>0 || $TotalPorcentagemIPV<0) {
            //**********************************************
            // Atualiza IPV com novas porcentagens digitadas na tela de montagem de IPV
            //**********************************************
            log("IPV original:" .$IPV);

            //Pega o valor original do IPV e transforma novamente em uma porcentagem
            $PorcentagemIPV   = (((100*$IPV)-100)/$IPV);
       
            //Soma a porcentagem original com a digitada na tela de montagem de IPV,
            log("Porcentagem do ipv original: ". $PorcentagemIPV."    porcentagem digitada:".$TotalPorcentagemIPV);

            $PorcentagemTotal = $PorcentagemIPV+$TotalPorcentagemIPV;

            log("nova porcentagem: " .$PorcentagemTotal);

            //Transforma tudo novamente em um IPV
            $IPV = (100/(100-$PorcentagemTotal)); 
            log("novo IPV: " .$IPV);
            //***********************************************
        }

        /*****************************************************************************************************************************************************
        CALCULO DO IPV DOS IMPOSTOS ATUALIZADOS

        $Aliquota_IPV = round(((($IPV-1)*10000)/($IPV*10000))*100, 2);
        $Aliquota_IPV_Total = $Aliquota_IPV;
        $Aliquota_IPV_Total += ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') ? $Aliquota_ICMS : 0;
        $Aliquota_IPV_Total += ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') ? $Aliquota_PIS : 0;
        $Aliquota_IPV_Total += ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $Aliquota_COFINS : 0;
        $Aliquota_IPV_Total += ($Aliquota_IRPJ > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') ? $Aliquota_IRPJ : 0;
        $Aliquota_IPV_Total += ($Aliquota_CSLL > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') ? $Aliquota_CSLL : 0;

        $IPV_Total = 100 / (100 - $Aliquota_IPV_Total);
        $precoBase = $preco * $IPV_Total;
        
        $ipvVenda = ($precoBase * ($Aliquota_IPV/100));
        $preco *= $IPV;
        log('6 IPV: ' . gCorrigeNumero($Aliquota_IPV));
        $demonstrativo[] = array(
            'campo' => gCorrigeNumero($Aliquota_IPV) . '% IPV ' . gCorrigeNumero($IPV,4),
            'valor' => gCorrigeNumero($ipvVenda,5),
            'operacao' => '+'
        );

         // ICMS
        $icmsVenda = 0;
        $baseCalculoICMS = ($mD006['D006_Percentual_Reducao_ICMS'] > 0) ? ((100-$mD006['D006_Percentual_Reducao_ICMS'])/100)*$precoBase : $precoBase;
        $icmsVenda   = ($baseCalculoICMS * ($Aliquota_ICMS/100));
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        log('7 ICMS VENDA: ' . $icmsVenda . ' - ' . $Aliquota_ICMS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => gCorrigeNumero($icmsVenda,5),
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $pisVenda   = ($precoBase * ($Aliquota_PIS/100));
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        log('8 PIS VENDA: ' . $pisVenda . ' - ' . $Aliquota_PIS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => gCorrigeNumero($pisVenda,5),
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );


        // COFINS
        $cofinsVenda = 0;
        $cofinsVenda   = ($precoBase * ($Aliquota_COFINS/100));
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        log('9 COFINS VENDA: ' . $cofinsVenda . ' - ' . $Aliquota_COFINS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => gCorrigeNumero($cofinsVenda,5),
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IRPJ
        $irpjVenda = 0;
        $irpjVenda   = ($precoBase * ($Aliquota_IRPJ/100));
        $Aliquota_IRPJ = number_format($Aliquota_IRPJ, 2, ',', '.');
        log('9 IRPJ VENDA: ' . $irpjVenda . ' - ' . $Aliquota_IRPJ);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_IRPJ . '% IRPJ venda',
            'valor' => gCorrigeNumero($irpjVenda,5),
            'operacao' => ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarIRPJPrecoVenda']
        );

        // CSLL
        $csllVenda = 0;
        $csllVenda   = ($precoBase * ($Aliquota_CSLL/100));
        $Aliquota_CSLL = number_format($Aliquota_CSLL, 2, ',', '.');
        log('9 CSLL VENDA: ' . $csllVenda . ' - ' . $Aliquota_CSLL);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_CSLL . '% CSLL venda',
            'valor' => gCorrigeNumero($csllVenda,5),
            'operacao' => ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCSLLPrecoVenda']
        );
        *******************************************************************************************************************************************************/



        // Total aliquotas para projetar preço de venda
        $totalAliquotas = 0;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_ICMS;
        }
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_PIS;
        }
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_COFINS;
        }
        if ($Aliquota_IRPJ > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_IRPJ;
        }
        if ($Aliquota_CSLL > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') {
            $totalAliquotas += $Aliquota_CSLL;
        }
        $totalAliquotas = 100 / ( 100 - $totalAliquotas);

        // CALCULO DO IPV E DOS IMPOSTOS ANTIGO        
        $preco *= $IPV;
        log('6 IPV: ' . $IPV);
        $demonstrativo[] = array(
            'campo' => 'IPV',
            'valor' => gCorrigeNumero($IPV,5),
            'operacao' => '*'
        );

        $precoBaseImpostos = $preco * $totalAliquotas;

        // ICMS
        $icmsVenda = 0;
        $porcentagem = $Aliquota_ICMS / 100;
        $Fator_ICMS = (100 - $ST_ICMS_Interno) / 100;
        log("Fator ICMS: ".$Fator_ICMS);
        $baseCalculoICMS = ($mD006['D006_Percentual_Reducao_ICMS'] > 0) ? ((100-$mD006['D006_Percentual_Reducao_ICMS'])/100)*$precoBaseImpostos : $precoBaseImpostos;
        $baseCalculoICMS = ($mD006['D006_Flag_Somar_Fator_ICMS_Dif_Base']=='S') ? ($baseCalculoICMS / $Fator_ICMS) : $baseCalculoICMS;

        $icmsVenda   = $baseCalculoICMS * $porcentagem;
        if ($Aliquota_ICMS > 0 && $g['C031']['somarICMSPrecoVenda'] == 'S') {
            $preco += $icmsVenda;
        }
        $Aliquota_ICMS = number_format($Aliquota_ICMS, 2, ',', '.');
        log('7 ICMS VENDA: ' . $icmsVenda . ' - ' . $Aliquota_ICMS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_ICMS . '% ICMS venda',
            'valor' => gCorrigeNumero($icmsVenda,5),
            'operacao' => ($g['C031']['somarICMSPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarICMSPrecoVenda']
        );


        // PIS
        $pisVenda = 0;
        $porcentagem = $Aliquota_PIS / 100;
        $pisVenda   = $precoBaseImpostos * $porcentagem;
        if ($Aliquota_PIS > 0 && $g['C031']['somarPISPrecoVenda'] == 'S') {
            $preco += $pisVenda;
        }
        $Aliquota_PIS = number_format($Aliquota_PIS, 2, ',', '.');
        log('8 PIS VENDA: ' . $pisVenda . ' - ' . $Aliquota_PIS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_PIS . '% PIS venda',
            'valor' => gCorrigeNumero($pisVenda,5),
            'operacao' => ($g['C031']['somarPISPrecoVenda']=='S') ? '+' : '',
            'exibir' => $g['C031']['somarPISPrecoVenda']
        );

        // COFINS
        $cofinsVenda = 0;
        $porcentagem = $Aliquota_COFINS / 100;
        $cofinsVenda   = $precoBaseImpostos * $porcentagem;
        if ($Aliquota_COFINS > 0 && $g['C031']['somarCOFINSPrecoVenda'] == 'S') {
            $preco += $cofinsVenda;
        }
        $Aliquota_COFINS = number_format($Aliquota_COFINS, 2, ',', '.');
        log('9 COFINS VENDA: ' . $cofinsVenda . ' - ' . $Aliquota_COFINS);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_COFINS . '% COFINS venda',
            'valor' => gCorrigeNumero($cofinsVenda,5),
            'operacao' => ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCOFINSPrecoVenda']
        );


        // IRPJ
        $irpjVenda = 0;
        $porcentagem = $Aliquota_IRPJ / 100;
        $irpjVenda   = $precoBaseImpostos * $porcentagem;
        if ($Aliquota_IRPJ > 0 && $g['C031']['somarIRPJPrecoVenda'] == 'S') {
            $preco += $irpjVenda;
        }
        $Aliquota_IRPJ = number_format($Aliquota_IRPJ, 2, ',', '.');
        log('9 IRPJ VENDA: ' . $irpjVenda . ' - ' . $Aliquota_IRPJ);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_IRPJ . '% IRPJ venda',
            'valor' => gCorrigeNumero($irpjVenda,5),
            'operacao' => ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarIRPJPrecoVenda']
        );


        // CSLL
        $csllVenda = 0;
        $porcentagem = $Aliquota_CSLL / 100;
        $csllVenda   = $precoBaseImpostos * $porcentagem;
        if ($Aliquota_CSLL > 0 && $g['C031']['somarCSLLPrecoVenda'] == 'S') {
            $preco += $csllVenda;
        }
        $Aliquota_CSLL = number_format($Aliquota_CSLL, 2, ',', '.');
        log('9 CSLL VENDA: ' . $csllVenda . ' - ' . $Aliquota_CSLL);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_CSLL . '% CSLL venda',
            'valor' => gCorrigeNumero($csllVenda,5),
            'operacao' => ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? '+' : '',
            'exibir' => $g['C031']['somarCSLLPrecoVenda']
        );

        /**
            DESCOMENTAR ESTE CÓDIGO AO ATIVAR CALCULO DE IPV E IMPOSTOS ATUALIZADO
        *   $preco = $precoBase;
        */

        //PREÇO DE VENDA LIQUIDO
        $demonstrativo[] = array(
            'campo' => 'PREÇO LÍQUIDO',
            'valor' => gCorrigeNumero($preco,5),
            'operacao' => '=',
            'tipo' => 'total',
            'css' => 'font-size: 12px; border-bottom: 1px solid #000;'
        );


        //Caso o preço trabalho seja tabelado e a empresa trabalha com o preço bruto
        if($g['C031']['precopadraoVendaProduto'] == 'liquido'){
            if($precoTabelado > 0){
                $diferenca = $precoTabelado - $preco;
                $preco = $precoTabelado;
                $precoDinamico = false;

                $demonstrativo[] = array(
                    'campo' => 'PREÇO TABELADO',
                    'valor' => gCorrigeNumero($preco,5),
                    'operacao' => '=',
                    'tipo' => 'fixo',
                    'css' => 'font-size: 12px;'
                );

                $demonstrativo[] = array(
                    'campo' => 'Diferença',
                    'valor' => gCorrigeNumero($diferenca,5),
                    'operacao' => '',
                    'tipo' => 'fixo',
                    'css' => 'font-size: 12px; color: blue; border-bottom: 1px solid #000;'
                );
            }

            //Verifica tabela de preços do cliente
            $sqlD160 = mysql_query("SELECT D160_Valor_Preco FROM D160 WHERE D160_D024_Id = '{$D024_Id_Tabela_Preco_Cliente}' AND D160_D009_Id = '{$D009_Id}'");
            if(mysql_num_rows($sqlD160) == 1){
                $rowD160 = mysql_fetch_array($sqlD160);
                if($rowD160['D160_Valor_Preco'] > 0){
                    $diferenca = $rowD160['D160_Valor_Preco'] - $preco;
                    $preco = $rowD160['D160_Valor_Preco'];
                    $precoDinamico = false;

                    $demonstrativo[] = array(
                        'campo' => 'TABELA CLIENTE',
                        'valor' => gCorrigeNumero($preco,5),
                        'operacao' => '=',
                        'tipo' => 'fixo',
                        'css' => 'font-size: 12px;'
                    );

                    $demonstrativo[] = array(
                        'campo' => 'Diferença',
                        'valor' => gCorrigeNumero($diferenca,5),
                        'operacao' => '',
                        'tipo' => 'fixo',
                        'css' => 'font-size: 12px; color: blue; border-bottom: 1px solid #000;'
                    );
                }
            }

            //Gatilho para alterar o preço
            $this->gatilhoAposDefinirPrecoVenda($preco,$demonstrativo,$diferenca, $D009_Id);
                        
        }

        //CALCULO DO PREÇO BRUTO
        $precoBruto = $preco;

        // IPI
        $ipiVenda = 0;
        $porcentagem = 100 / (100 - $Aliquota_IPI);
        $ipiVenda   = ($preco * $porcentagem) - $preco;
        if ($Aliquota_IPI > 0) {
            $precoBruto += $ipiVenda;
        }
        $Aliquota_IPI = number_format($Aliquota_IPI, 2, ',', '.');
        log('10 IPI VENDA: ' . $ipiVenda . ' - ' . $Aliquota_IPI);
        
        $demonstrativo[] = array(
            'campo' => $Aliquota_IPI . '% IPI venda',
            'valor' => gCorrigeNumero($ipiVenda,5),
            'operacao' => '+'
        );

        // CALCULO DO ST
        if($mD006['D006_Flag_Substituicao_Tributaria'] != 'S'){
            $baseST = $valorST = 0;
        } else {
            $baseST = ($preco + $valorIPI) * (($ST_VA/100)+1);

            $varA = 0;
            if($Percentual_Reducao_ICMS_ST > 0){
                $baseST = $baseST * ((100-$Percentual_Reducao_ICMS_ST)/100);
            }
            $varA = $baseST * ($ST_ICMS_Interno/100);

            $varB = ($preco + $valorIPI);
            if($Percentual_Reducao_ICMS > 0){
                $varB = $varB * ((100-$Percentual_Reducao_ICMS)/100);
            }
            $varB = $varB * ($ICMS_Inter_Estadual/100);

            $valorST = $varA - $varB;
        }
        $aliquotaST = ($valorST/$preco)*100;
        $aliquotaST = ($aliquotaST < 0) ? 0 : $aliquotaST;

        if($valorST > 0){
            $precoBruto += $valorST;
        }
        $demonstrativo[] = array(
            'campo' => gCorrigeNumero($aliquotaST) . '% ST venda',
            'valor' => gCorrigeNumero($valorST,5),
            'operacao' => '+'
        );
        
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            $preco = $precoBruto;
        }

        $demonstrativo[] = array(
            'campo' => 'PREÇO BRUTO',
            'valor' => gCorrigeNumero($precoBruto,5),
            'operacao' => '=',
            'tipo' => 'fixo',
            'css' => 'font-size: 12px;' . (($precoTabelado > 0 && $g['C031']['precopadraoVendaProduto'] == 'bruto') ? 'border-bottom:1px solid #000;' : '')
        );

        //Caso o preço trabalho seja tabelado e a empresa trabalha com o preço bruto
        if($g['C031']['precopadraoVendaProduto'] == 'bruto'){
            if($precoTabelado > 0){
                $diferenca = $precoTabelado - $preco;
                $preco = $precoTabelado;
                $precoDinamico = false;

                $demonstrativo[] = array(
                    'campo' => 'PREÇO TABELADO',
                    'valor' => gCorrigeNumero($preco,5),
                    'operacao' => '=',
                    'tipo' => 'fixo',
                    'css' => 'font-size: 12px;'
                );

                $demonstrativo[] = array(
                    'campo' => 'Diferença',
                    'valor' => gCorrigeNumero($diferenca,5),
                    'operacao' => '',
                    'tipo' => 'fixo',
                    'css' => 'font-size: 12px; color: blue;'
                );
            }

            //Verifica tabela de preços do cliente
            $sqlD160 = mysql_query("SELECT D160_Valor_Preco FROM D160 WHERE D160_D024_Id = '{$D024_Id_Tabela_Preco_Cliente}' AND D160_D009_Id = '{$D009_Id}'");
            if(mysql_num_rows($sqlD160) == 1){
                $rowD160 = mysql_fetch_array($sqlD160);
                if($rowD160['D160_Valor_Preco'] > 0){
                    $diferenca = $rowD160['D160_Valor_Preco'] - $preco;
                    $preco = $rowD160['D160_Valor_Preco'];
                    $precoDinamico = false;

                    $demonstrativo[] = array(
                        'campo' => 'TABELA CLIENTE',
                        'valor' => gCorrigeNumero($preco,5),
                        'operacao' => '=',
                        'tipo' => 'fixo',
                        'css' => 'font-size: 12px;'
                    );

                    $demonstrativo[] = array(
                        'campo' => 'Diferença',
                        'valor' => gCorrigeNumero($diferenca,5),
                        'operacao' => '',
                        'tipo' => 'fixo',
                        'css' => 'font-size: 12px; color: blue; border-bottom: 1px solid #000;'
                    );
                }
            }
            //Gatilho para alterar o preço
            $this->gatilhoAposDefinirPrecoVenda($preco,$demonstrativo,$diferenca, $D009_Id);
        }

        $valorSTPrecoBruto = $valorST;

        //////////////////////////////////////////////////////////////////////////////////
        //                           DEMONSTRATIVO DO CUSTO                             //
        //////////////////////////////////////////////////////////////////////////////////

        //POPULA VARIAVEIS DO DEMONSTRATIVO DO CUSTO
        if($precoUtilizado == 'C' || $precoUtilizado == 'OC'){
            if($precoUtilizado == 'C'){
                //DADOS DA COTAÇÃO
                $sqlT219 = mysql_query("SELECT ((round(T219_Valor_ST,2)/(round(T219_Valor_Total_Preco,2)+round(T219_Valor_IPI,2)))*100) AS ST, D024_Nome_Empresa, T218_Data_Emissao, T219.* FROM T219 LEFT JOIN T218 ON T218_Id=T219_T218_Id LEFT JOIN D024 ON D024_Id=T218_D024_Id WHERE T219_Id = '{$T219_Id}'");
                $mT219 = mysql_fetch_array($sqlT219);

                $precoUnitario           = $mT219['T219_Valor_Preco_Unitario'];
                $descontoUnitario        = $mT219['T219_Percentual_Desconto']*($mT219['T219_Valor_Preco_Unitario']/100);
                $precoLiquido            = $mT219['T219_Valor_Preco_Unitario'];
                $aliquotaICMS            = $mT219['T219_Aliquota_ICMS'];
                $valorICMS               = $mT219['T219_Valor_ICMS']/$mT219['T219_Quantidade'];
                $aliquotaPIS             = $mT219['T219_Aliquota_PIS'];
                $valorPIS                = $mT219['T219_Valor_PIS']/$mT219['T219_Quantidade'];
                $aliquotaCOFINS          = $mT219['T219_Aliquota_COFINS'];
                $valorCOFINS             = $mT219['T219_Valor_COFINS']/$mT219['T219_Quantidade'];
                $aliquotaIPI             = $mT219['T219_Aliquota_IPI'];
                $valorIPI                = $mT219['T219_Valor_IPI']/$mT219['T219_Quantidade'];
                $percentualST            = $mT219['ST'];
                $valorST                 = $mT219['T219_Valor_ST']/$mT219['T219_Quantidade'];
                $valorFrete              = ($mT219['T219_Valor_Frete']+$mT219['T219_Valor_Frete_Total'])/$mT219['T219_Quantidade'];
                $percentualFrete         = $mT219['T219_Percentual_Frete'];
                $valorSeguro             = 0;
                $valorDespesasAcessorias = $mT219['T219_Valor_Despesas_Acessorias_Unitario'];
                $aliquotaDIFAL           = $mT219['T219_Aliquota_ICMS_Diferenca_Aliquotas'];
                $valorDIFAL              = $mT219['T219_Valor_ICMS_Diferenca_Aliquotas']/$mT219['T219_Quantidade'];
                
                $mT219['D024_Nome_Empresa'] = substr($mT219['D024_Nome_Empresa'] ,0 ,12);
                $mT219['T218_Data_Emissao'] = gCorrigeData($mT219['T218_Data_Emissao']);
                $cabecalhoDemonstrativo  = "COT-{$mT219['T219_T218_Id']}-{$mT219['D024_Nome_Empresa']}-{$mT219['T218_Data_Emissao']}";
            } else {
                //DADOS DA ORDEM DE COMPRA
                $sqlT225 = mysql_query("SELECT ((T225_Valor_ICMS_Substituicao_Retencao/(T225_Valor_Total_Preco+T225_Valor_IPI))*100) AS ST, D024_Nome_Empresa, T224_Data_Emissao, T225.* FROM T225 LEFT JOIN T224 ON T224_Id=T225_T224_Id LEFT JOIN D024 ON D024_Id=T224_D024_Id WHERE T225_Id = '{$T225_Id}'");
                $mT225 = mysql_fetch_array($sqlT225);

                $precoUnitario           = $mT225['T225_Valor_Preco_Unitario'];
                $descontoUnitario        = $mT225['T225_Valor_Preco_Unitario']*($mT225['T225_Percentual_Desconto']/100);
                $precoLiquido            = $mT225['T225_Valor_Preco_Unitario'];
                $aliquotaICMS            = $mT225['T225_Aliquota_ICMS'];
                $valorICMS               = $mT225['T225_Valor_ICMS']/$mT225['T225_Quantidade'];
                $aliquotaPIS             = $mT225['T225_Aliquota_PIS'];
                $valorPIS                = $mT225['T225_Valor_PIS']/$mT225['T225_Quantidade'];
                $aliquotaCOFINS          = $mT225['T225_Aliquota_COFINS'];
                $valorCOFINS             = $mT225['T225_Valor_COFINS']/$mT225['T225_Quantidade'];
                $aliquotaIPI             = $mT225['T225_Aliquota_IPI'];
                $valorIPI                = $mT225['T225_Valor_IPI']/$mT225['T225_Quantidade'];
                $percentualST            = $mT225['ST'];
                $valorST                 = $mT225['T225_Valor_ICMS_Substituicao_Retencao']/$mT225['T225_Quantidade'];
                $valorFrete              = $mT225['T225_Valor_Frete']/$mT225['T225_Quantidade'];
                $percentualFrete         = $mT225['T225_Percentual_Frete'];
                $valorSeguro             = 0;
                $valorDespesasAcessorias = $mT225['T225_Valor_Despesas_Acessorias_Unitario'];
                $aliquotaDIFAL           = $mT225['T225_Aliquota_ICMS_Diferenca_Aliquotas'];
                $valorDIFAL              = $mT225['T225_Valor_ICMS_Diferenca_Aliquotas']/$mT225['T225_Quantidade'];
                
                $mT225['D024_Nome_Empresa'] = substr($mT225['D024_Nome_Empresa'], 0, 12);
                $mT225['T224_Data_Emissao'] = gCorrigeData($mT225['T224_Data_Emissao']);
                $cabecalhoDemonstrativo  = "OC-{$mT225['T225_T224_Id']}-{$mT225['D024_Nome_Empresa']}-{$mT225['T224_Data_Emissao']}";
            }

        } else if($precoUtilizado == 'T'){
            //DADOS DA TABELA DO FORNECEDOR
            $cSql_D009 = mysql_query("select    D009_Id,
                                                D070_Id,
                                                D069_Id,
                                                D006_Id,
                                                D024_Id,
                                                D069_Valor_Preco_Original,
                                                D069_Percentual_Desconto,
                                                D069_Valor_Preco_Unitario,
                                                D069_Aliquota_ICMS,
                                                D069_Valor_ICMS,
                                                D069_Quantidade,
                                                D069_Aliquota_PIS,
                                                D069_Valor_PIS,
                                                D069_Aliquota_COFINS,
                                                D069_Valor_COFINS,
                                                D069_Aliquota_IPI,
                                                D069_Valor_IPI,
                                                D069_Percentual_ST,
                                                D069_Valor_ICMS_Substituicao_Retencao,
                                                D069_Valor_Frete,
                                                D069_Valor_Despesas_Acessorias_Unitario,
                                                D069_D070_Id,
                                                D024_Nome_Empresa,
                                                D070_Data_Vencimento,
                                                D069_Aliquota_ICMS_Diferenca_Aliquotas,
                                                D069_Valor_ICMS_Diferenca_Aliquotas

                                        from D009
                                   left join D069 on D069_Id=D009_D069_Id
                                   left join D070 on D070_Id=D069_D070_Id
                                   left join D006 on D006_Id=D069_D006_Id
                                   left join D024 on D024_Id=D070_D024_Id
                                       where D009_Id='{$D009_Id}' AND D069_Flag_Tabela_Principal='S'");
            $mD069 = mysql_fetch_array($cSql_D009);

            $precoUnitario           = $mD069['D069_Valor_Preco_Original'];
            $descontoUnitario        = $mD069['D069_Valor_Preco_Original']*($mD069['D069_Percentual_Desconto']/100);
            $precoLiquido            = $mD069['D069_Valor_Preco_Unitario'];
            $aliquotaICMS            = $mD069['D069_Aliquota_ICMS'];
            $valorICMS               = $mD069['D069_Valor_ICMS']/$mD069['D069_Quantidade'];
            $aliquotaPIS             = $mD069['D069_Aliquota_PIS'];
            $valorPIS                = $mD069['D069_Valor_PIS']/$mD069['D069_Quantidade'];
            $aliquotaCOFINS          = $mD069['D069_Aliquota_COFINS'];
            $valorCOFINS             = $mD069['D069_Valor_COFINS']/$mD069['D069_Quantidade'];
            $aliquotaIPI             = $mD069['D069_Aliquota_IPI'];
            $valorIPI                = $mD069['D069_Valor_IPI']/$mD069['D069_Quantidade'];
            $percentualST            = $mD069['D069_Percentual_ST'];
            $valorST                 = $mD069['D069_Valor_ICMS_Substituicao_Retencao'];
            $valorFrete              = $mD069['D069_Valor_Frete'];
            $valorSeguro             = 0;
            $valorDespesasAcessorias = $mD069['D069_Valor_Despesas_Acessorias_Unitario'];
            $aliquotaDIFAL           = $mD069['D069_Aliquota_ICMS_Diferenca_Aliquotas'];
            $valorDIFAL              = $mD069['D069_Valor_ICMS_Diferenca_Aliquotas']/$mD069['D069_Quantidade'];
            
            if($mD069['D069_D070_Id'] > 0){
                $mD069['D024_Nome_Empresa'] = substr($mD069['D024_Nome_Empresa'], 0, 19);
                $descricao = '-'.$mD069['D069_D070_Id'].'-'.$mD069['D024_Nome_Empresa'];
                $D070_Vencimento=gCorrigeData($mD069['D070_Data_Vencimento']);
            }
            $cabecalhoDemonstrativo  = "TAB{$descricao}<br/><font style='color:blue;'>Vencimento:{$D070_Vencimento}</font>";
        } else {
            //DADOS DO HISTÓRICO DO PRODUTO
            $sqlT001 = mysql_query("select  T001_Id,
                                            T001_Valor_Preco_Unitario,
                                            T001_Valor_Desconto_Unitario,
                                            T001_Aliquota_ICMS,
                                            T001_Valor_ICMS,
                                            T001_Aliquota_PIS,
                                            T001_Valor_PIS,
                                            T001_Aliquota_COFINS,
                                            T001_Valor_COFINS,
                                            T001_Aliquota_IPI,
                                            T001_Valor_IPI,
                                            T001_Percentual_ST,
                                            T001_Valor_ST,
                                            T001_Valor_Frete,
                                            T001_Valor_Seguro,
                                            T001_Quantidade,
                                            T001_Valor_Despesas_Acessorias,
                                            D024_Nome_Empresa,
                                            T001_Data_Lancamento,
                                            T001_Descricao_Operacao,
                                            D009_D049_Id,
                                            D006_Flag_Substituicao_Tributaria,
                                            D009_Flag_Custo_Utilizado,
                                            D009_Valor_Custo_Medio_Unitario,
                                            D009_Valor_Preco_Tabela,
                                            T001_Valor_ICMS_Diferenca_Aliquotas,
                                            T001_Aliquota_ICMS_Diferenca_Aliquotas
                                      from T001 
                                      left join T014 on T014_Id=T001_T014_Id
                                      left join T013 on T013_Id=T014_T013_Id
                                      left join D006 on D006_Id=T001_D006_Id 
                                      left join D024 on D024_Id=T001_D024_Id
                                      left join T219 on T219_Id=T001_T219_Id
                                      left join T218 on T218_Id=T219_T218_Id
                                      left join D009 on D009_Id=T001_D009_Id
                                      where T001_Id='{$T001_Id}'");
            $mT001 = mysql_fetch_array($sqlT001);

            $precoUnitario           = $mT001['T001_Valor_Preco_Unitario'];
            $descontoUnitario        = $mT001['T001_Valor_Desconto_Unitario'];
            $precoLiquido            = $mT001['T001_Valor_Preco_Unitario']-$mT001['T001_Valor_Desconto_Unitario'];
            $aliquotaICMS            = $mT001['T001_Aliquota_ICMS'];
            $valorICMS               = $mT001['T001_Valor_ICMS'];
            $aliquotaPIS             = $mT001['T001_Aliquota_PIS'];
            $valorPIS                = $mT001['T001_Valor_PIS'];
            $aliquotaCOFINS          = $mT001['T001_Aliquota_COFINS'];
            $valorCOFINS             = $mT001['T001_Valor_COFINS'];
            $aliquotaIPI             = $mT001['T001_Aliquota_IPI'];
            $valorIPI                = $mT001['T001_Valor_IPI'];
            $percentualST            = $mT001['T001_Percentual_ST'];
            $valorST                 = $mT001['T001_Valor_ST'];
            $valorFrete              = $mT001['T001_Valor_Frete'];
            if ($mT001['T001_Valor_Seguro']>0 && $mT001['T001_Quantidade']>0){
                $valorSeguro         = $mT001['T001_Valor_Seguro']/$mT001['T001_Quantidade'];
            } else {
                $valorSeguro         = 0;
            }
            $valorDespesasAcessorias = $mT001['T001_Valor_Despesas_Acessorias'];
            $aliquotaDIFAL           = $mT001['T001_Aliquota_ICMS_Diferenca_Aliquotas'];
            $valorDIFAL              = $mT001['T001_Valor_ICMS_Diferenca_Aliquotas'];

            $mT001['D024_Nome_Empresa'] = '-'.substr($mT001['D024_Nome_Empresa'], 0, 12);
            $mT001['T001_Data_Lancamento'] = (gCorrigeData($mT001['T001_Data_Lancamento']) != '') ? '-'.gCorrigeData($mT001['T001_Data_Lancamento']) : '';
            $cabecalhoDemonstrativo  = "{$mT001['T001_Descricao_Operacao']}{$mT001['D024_Nome_Empresa']}{$mT001['T001_Data_Lancamento']}";

            $eventoClick = "abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Histórico do Produto', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent('{$mT001['D009_D049_Id']}') + '&tabela=T001', [990,605]);";
           
        }

        //MONTA DEMONSTRATIVO           
        $demonstrativoCusto = array();
        $custoFinal = 0;

        $demonstrativoCusto[] = array('operacao' => 'cab', 'valor' => $cabecalhoDemonstrativo, 'css' => 'border-bottom: 1px solid black; text-align: center;', 'onclick' => $eventoClick);

        $demonstrativoCusto[] = array('operacao' => '', 'campo' => 'PREÇO FOB', 'valor' => gCorrigeNumero($precoUnitario,5), 'tipo' => 'fixo', 'css' => 'font-size: 12px; border-bottom: 1px solid #000;');
        $custoFinal = $precoUnitario;
        
        $demonstrativoCusto[] = array('operacao' => '-', 'campo' => 'Desconto nos Itens', 'valor' => gCorrigeNumero($descontoUnitario,5));
        $custoFinal -= $descontoUnitario;
        
        $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'Preço Liquido', 'valor' => gCorrigeNumero($precoLiquido,5));
        

        if($precoUtilizado == 'H' || ($mC004['C004_Flag_Empresa_Simples']!='1' && $precoUtilizado != 'H')) {
            $demonstrativoCusto[] = array('operacao' => '', 'campo' => gCorrigeNumero($aliquotaICMS) . ' % ICMS', 'valor' => gCorrigeNumero($valorICMS,5));
        }

        $demonstrativoCusto[] = array('operacao' => '', 'campo' => gCorrigeNumero($aliquotaPIS) . ' % PIS', 'valor' => gCorrigeNumero($valorPIS,5));
        $demonstrativoCusto[] = array('operacao' => '', 'campo' => gCorrigeNumero($aliquotaCOFINS) . ' % COFINS', 'valor' => gCorrigeNumero($valorCOFINS,5));

        $demonstrativoCusto[] = array('operacao' => '+', 'campo' => gCorrigeNumero($aliquotaIPI) . ' % IPI', 'valor' => gCorrigeNumero($valorIPI,5));
        $custoFinal += $valorIPI;
        
        if($precoUtilizado == 'H'){
            if ($mT001['D006_Flag_Substituicao_Tributaria']=='S'){
                $demonstrativoCusto[] = array('operacao' => '+', 'campo' => gCorrigeNumero($percentualST) . ' % ST', 'valor' => gCorrigeNumero($valorST,5));
                $custoFinal += $valorST;
            } else if($mT001['D006_Flag_Substituicao_Tributaria'] != 'S' && $mT001['T001_Valor_ST'] > 0){
                $demonstrativoCusto[] = array('operacao' => '', 'campo' => gCorrigeNumero($percentualST) . ' % ST', 'valor' => gCorrigeNumero($valorST,5), 'css' => 'color: red;');
            } else {
                $demonstrativoCusto[] = array('operacao' => '', 'campo' => gCorrigeNumero($percentualST) . ' % ST', 'valor' => gCorrigeNumero($valorST,5));
            }
        } else {
            $demonstrativoCusto[] = array('operacao' => '+', 'campo' => gCorrigeNumero($percentualST) . ' % ST', 'valor' => gCorrigeNumero($valorST,5));
            $custoFinal += $valorST;
        }

        $demonstrativoCusto[] = array('operacao' => '+', 'campo' => gCorrigeNumero($aliquotaDIFAL) . ' % DIFAL ICMS', 'valor' => gCorrigeNumero($valorDIFAL,5));
        $custoFinal += $valorDIFAL;
     
        if($percentualFrete <= 0){
            $percentualFrete = 0;
            if ($valorFrete>0 && $custoFinal>0) {
                $percentualFrete = gCorrigeNumero(($custoFinal/$valorFrete)-1);
            } 
        } else {
            $percentualFrete = gCorrigeNumero($percentualFrete);
        }
        $demonstrativoCusto[] = array('operacao' => '+', 'campo' => $percentualFrete . ' % Frete', 'valor' => gCorrigeNumero($valorFrete,5));
        $custoFinal += $valorFrete;
        $demonstrativoCusto[] = array('operacao' => '+', 'campo' => 'Seguro', 'valor' => gCorrigeNumero($valorSeguro,5));
        $custoFinal += $valorSeguro;
        
        $demonstrativoCusto[] = array('operacao' => '+', 'campo' => 'Despesas Acessórias', 'valor' => gCorrigeNumero($valorDespesasAcessorias,5));
        $custoFinal += $valorDespesasAcessorias;

        $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'CUSTO BRUTO', 'valor' => gCorrigeNumero($custoFinal,5), 'css' => 'font-weight: bold; border-bottom: 1px solid #000; font-size: 12px;', 'tipo' => 'total');
        
        if($precoUtilizado == 'H' && $mT001['D009_Flag_Custo_Utilizado'] == 'M'){ // Custo médio
            $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'MÉDIO', 'valor' => gCorrigeNumero($mT001['D009_Valor_Custo_Medio_Unitario'],5), 'css' => 'font-weight: bold; color: blue; font-size: 12px;', 'tipo' => 'total');
        } else if(($precoUtilizado == 'H' || $precoUtilizado == 'H') && $mT001['D009_Flag_Custo_Utilizado'] == 'MA'){ // Custo manual
            $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'MANUAL', 'valor' => gCorrigeNumero($mT001['D009_Valor_Preco_Tabela'],5), 'css' => 'font-weight: bold; color: blue; font-size: 12px;', 'tipo' => 'total');
        } else if($precoUtilizado == 'C' && $mT219['T219_Quantidade_Estoque'] > 0 && $mT219['T219_Valor_Custo_Medio_Unitario'] > 0){ // Custo cotação
            $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'MÉDIO <span style="font-size: 10px;">(Estq: '.gCorrigeNumero($mT219['T219_Quantidade_Estoque']).')</span>', 'valor' => gCorrigeNumero($mT219['T219_Valor_Custo_Medio_Unitario'],5), 'css' => 'font-weight: bold; color: blue; font-size: 12px;', 'tipo' => 'total');
        } else if($precoUtilizado == 'OC' && $mT225['T225_Quantidade_Estoque'] > 0 && $mT225['T225_Valor_Custo_Medio_Unitario'] > 0){ // Custo OC
            $demonstrativoCusto[] = array('operacao' => '=', 'campo' => 'MÉDIO <span style="font-size: 10px;">(Estq: '.gCorrigeNumero($mT225['T225_Quantidade_Estoque']).')</span>', 'valor' => gCorrigeNumero($mT225['T225_Valor_Custo_Medio_Unitario'],5), 'css' => 'font-weight: bold; color: blue; font-size: 12px;', 'tipo' => 'total');
        }

        $demonstrativo = array_merge($demonstrativoCusto, $demonstrativo);

        if($precoDinamico === false){
            // So montar demonstrativo se for preço IPV
            $demonstrativo = array();
        }
        
        $preco = number_format($preco, $casasDecimaisPreco, ',', '.');

        mysql_free_result($C004);
        mysql_free_result($D024);
        mysql_free_result($D009);
        mysql_free_result($sqlT225);
        mysql_free_result($sqlT014);
        mysql_free_result($sqlT008);
        mysql_free_result($D006);
        mysql_free_result($sqlD160);
        mysql_free_result($sqlT219);
        mysql_free_result($cSql_D009);
        mysql_free_result($sqlT001);

        return array(
            $preco,
            $demonstrativo,
            $custoMedio,
            $quantidadeEstoque,
            $custoProduto,
            $origemMercadoria,
            $possuiNFEntrada,
            $precoBruto,
            $valorSTPrecoBruto
        );
    }

    /**
     * retornaPrecoVenda
     *
     * @param String $D024_Id
     * @param String $query
     * @return bool
     */
    public function cadastrarFornecedorLote($D024_Id, $query)
    {
        global $g;
        mysql_query("START TRANSACTION");
        mysql_query("SET AUTOCOMMIT=0");
        $query = base64_decode($query);
        $sql = mysql_query($query);
        while($row = mysql_fetch_array($sql)){
            $sqlCheck = mysql_query("SELECT * FROM T241 WHERE T241_D024_Id = '{$D024_Id}' AND T241_D001_Id = '{$row['D001_Id']}'");
            if(mysql_num_rows($sqlCheck) > 0){
                continue;
            }
            mysql_query("INSERT INTO T241 (T241_D001_Id, T241_D024_Id, T241_Data) VALUES ('{$row['D001_Id']}', '{$D024_Id}', CURDATE())");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
        }
        mysql_query("COMMIT");
        return true;
    }

    /**
     * atualizaTabelaPrecoCliente
     *
     * @param String $D024_Id
     * @param String $D009_Id
     * @param String $preco
     * @return bool
     */
    public function atualizaTabelaPrecoCliente($D024_Id, $D009_Id, $preco)
    {
        global $g;
        if(empty($D009_Id) || empty($D024_Id)){
            return false;
        }
        $sqlD024 = mysql_query("SELECT D024_Id_Matriz FROM D024 WHERE D024_Id = '{$D024_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);
        if(!empty($rowD024['D024_Id_Matriz'])){
            $D024_Id = $rowD024['D024_Id_Matriz'];
        }

        $sqlD161 = mysql_query("SELECT * FROM D161 WHERE D161_D024_Id = '{$D024_Id}'");
        if(mysql_num_rows($sqlD161) > 0){
            $rowD161 = mysql_fetch_assoc($sqlD161);
            $D161_Id = $rowD161['D161_Id'];
        } else {
            mysqli_query("INSERT INTO D161 (D161_D024_Id) VALUES ('{$D024_Id}')");
            $D161_Id = $g['mysqlLastId'];
        }

        $sqlCheck = mysql_query("SELECT D160_Id FROM D160 WHERE D160_D024_Id = '{$D024_Id}' AND D160_D009_Id = '{$D009_Id}'");
        if(mysql_num_rows($sqlCheck) > 0){
            $rowD160 = mysql_fetch_array($sqlCheck);
            mysql_query("UPDATE D160 SET D160_Data_Hora = CURRENT_TIMESTAMP(), D160_Valor_Preco = '{$preco}', D160_C007_Id = '{$g['usuarioAtual']}' WHERE D160_Id = '{$rowD160['D160_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
        } else {
            mysqli_query("INSERT INTO D160 (D160_D161_Id, D160_D024_Id, D160_D009_Id, D160_C007_Id, D160_Data_Hora, D160_Valor_Preco) VALUES ('{$D161_Id}', '{$D024_Id}', '{$D009_Id}', '{$g['usuarioAtual']}', CURRENT_TIMESTAMP(), '{$preco}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
        }
        return true;
    }

    /**
     * retornaICMSTabelaFornecedor
     *
     * @param String $C004_Id
     * @param String $D024_Id
     * @param String $D069_Id
     * @param String $D006_Id
     * @param String $D018_Id
     * @param String $Flag_Revenda_Consumidor
     * @param String $Comissao
     * @param String $Documento
     * @param String $Tipo
     * @return String
     */
    public function retornaICMSTabelaFornecedor($C004_Id, $D024_Id, $D069_Id, $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Comissao, $Documento, $Tipo)
    {
        global $g;
        $Aliquota_ICMS                    = 0;
        $Percentual_Reducao_ICMS          = 0;
        $Aliquota_IPV_Cliente             = 0;
        $Aliquota_IPV_Utilizado           = 0;
        $D024_Inscricao_Estadual          = '';
        $D024_Flag_Pessoa_Fisica_Juridica = '';
        $D024_Valor_IPV                   = 0;
        $D024_Flag_Isento_ICMS            = '';
        $D024_Flag_Isento_ICMS_Venda      = 'N';
        $D024_Percentual_Reducao_ICMS     = 0;
        $D024_Aliquota_ICMS               = 0;
        $D020_Flag_ICMS                   = '';
        $D006_Flag_Isento_ICMS            = '';

        if(empty($C004_Id)){
            $C004_Id = $g['empresaAtual'];
        }

        $C004  = mysql_query("SELECT C004_Uf, C004_Flag_Isento_ICMS, C004_D018_Id FROM C004 WHERE C004_Id='{$C004_Id}'");
        $mC004 = mysql_fetch_array($C004);

        if ($D024_Id > 0) {
            $D024  = mysql_query("SELECT D024_Inscricao_Estadual,
                  D024_Flag_Pessoa_Fisica_Juridica,
                  D024_Valor_IPV,
                  D024_Flag_Revenda_Consumidor,
                  D024_Flag_Isento_ICMS,
                  D024_Flag_Isento_ICMS_Venda,
                  D024_Percentual_Reducao_ICMS,
                  D024_Aliquota_ICMS,
                  D053_Aliquota_ICMS_1,
                  D053_Percentual_Reducao_ICMS_1,
                  D053_Aliquota_ICMS_2,
                  D053_Percentual_Reducao_ICMS_2,
                  D053_Aliquota_ICMS_Revenda,
                  D053_Percentual_Reducao_ICMS_Revenda,
                  D053_Aliquota_ICMS_Consumidor,
                  D053_Percentual_Reducao_ICMS_Consumidor,
                  D020_Flag_ICMS,
                  D053_Aliquota_ICMS_Importado,
                  D053_Aliquota_ICMS_Importado_2,
                  D018_UF,
                  D024_D018_Id
                  FROM D024
                  LEFT JOIN D020 ON D020_Id=D024_D020_Id
                  LEFT JOIN D018 ON D018_Id=D024_D018_Id
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
            WHERE D024_Id='{$D024_Id}' LIMIT 1");
            $mD024 = mysql_fetch_array($D024);

            $D018_Aliquota_ICMS_1                    = $mD024['D053_Aliquota_ICMS_1'];
            $D018_Percentual_Reducao_ICMS_1          = $mD024['D053_Percentual_Reducao_ICMS_1'];
            $D018_Aliquota_ICMS_2                    = $mD024['D053_Aliquota_ICMS_2'];
            $D018_Percentual_Reducao_ICMS_2          = $mD024['D053_Percentual_Reducao_ICMS_2'];
            $D018_Aliquota_ICMS_Revenda              = $mD024['D053_Aliquota_ICMS_Revenda'];
            $D018_Percentual_Reducao_ICMS_Revenda    = $mD024['D053_Percentual_Reducao_ICMS_Revenda'];
            $D018_Aliquota_ICMS_Consumidor           = $mD024['D053_Aliquota_ICMS_Consumidor'];
            $D018_Percentual_Reducao_ICMS_Consumidor = $mD024['D053_Percentual_Reducao_ICMS_Consumidor'];
            $D018_UF                                 = $mD024['D018_UF'];
            $D020_Flag_ICMS                          = $mD024['D020_Flag_ICMS'];
            $D024_Valor_IPV                          = $mD024['D024_Valor_IPV'];
            $D018_Aliquota_ICMS_Importado_1          = $mD024['D053_Aliquota_ICMS_Importado'];
            $D018_Aliquota_ICMS_Importado_2          = $mD024['D053_Aliquota_ICMS_Importado_2'];

        } elseif ($D018_Id > 0) {

            $mD024['D024_Flag_Revenda_Consumidor'] = $Flag_Revenda_Consumidor;

            $D018  = mysql_query("SELECT
                  D053_Aliquota_ICMS_1,
                  D053_Percentual_Reducao_ICMS_1,
                  D053_Aliquota_ICMS_2,
                  D053_Percentual_Reducao_ICMS_2,
                  D053_Aliquota_ICMS_Revenda,
                  D053_Percentual_Reducao_ICMS_Revenda,
                  D053_Aliquota_ICMS_Consumidor,
                  D053_Percentual_Reducao_ICMS_Consumidor,
                  D053_Aliquota_ICMS_Importado,
                  D018_UF,
                  D053_Aliquota_ICMS_Importado_2
                  FROM D018
                  LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id='{$C004_Id}'
                  WHERE D018_Id='{$D018_Id}'
                  LIMIT 1");
            $mD018 = mysql_fetch_array($D018);

            $D018_Aliquota_ICMS_1                    = $mD018['D053_Aliquota_ICMS_1'];
            $D018_Percentual_Reducao_ICMS_1          = $mD018['D053_Percentual_Reducao_ICMS_1'];
            $D018_Aliquota_ICMS_2                    = $mD018['D053_Aliquota_ICMS_2'];
            $D018_Percentual_Reducao_ICMS_2          = $mD018['D053_Percentual_Reducao_ICMS_2'];
            $D018_Aliquota_ICMS_Revenda              = $mD018['D053_Aliquota_ICMS_Revenda'];
            $D018_Percentual_Reducao_ICMS_Revenda    = $mD018['D053_Percentual_Reducao_ICMS_Revenda'];
            $D018_Aliquota_ICMS_Consumidor           = $mD018['D053_Aliquota_ICMS_Consumidor'];
            $D018_Percentual_Reducao_ICMS_Consumidor = $mD018['D053_Percentual_Reducao_ICMS_Consumidor'];
            $D018_UF                                 = $mD018['D018_UF'];
            $D018_Aliquota_ICMS_Importado_1          = $mD018['D053_Aliquota_ICMS_Importado'];
            $D018_Aliquota_ICMS_Importado_2          = $mD018['D053_Aliquota_ICMS_Importado_2'];
        }

        if ($D006_Id > 0) {
            $D006  = mysql_query("
                SELECT D006_Flag_Isento_ICMS,
                      D006_Flag_Venda_Compra_Outros,
                      D006_Percentual_Reducao_ICMS,
                      D006_D059_Id,
                      D006_Flag_Isento_ICMS
                 FROM D006
                WHERE D006_Id='{$D006_Id}';
            ");
            $mD006 = mysql_fetch_array($D006);
        }

        if ($D069_Id > 0) {
            $D069  = mysql_query("SELECT D069_Flag_Origem_Produto FROM D069 WHERE D069_Id='{$D069_Id}'");
            $mD069 = mysql_fetch_array($D069);
        }

        // Quando a CFOP é isenta de ICMS, o sistema vai gerar o valor do ICMS nos campos com extenção "Oculto", para que estes valores possam ser utilizados na observação da nota
        // O tipo = 22 ou 33 é para retornar as aliquotas mesmo que a CFOP seja isenta
        // Marcelo de Paula - 18-09-2014
        if ($Tipo == 22 or $Tipo == 33){
            $mD006['D006_Flag_Isento_ICMS'] = 'N';
        }

        if($mD006['D006_Flag_Isento_ICMS'] == 'D' && ($mD006['D006_D059_Id'] == '5' || $mD006['D006_D059_Id'] == '6' || $mD006['D006_D059_Id'] == '7')){
            $mD006['D006_Flag_Isento_ICMS'] = 'S';
        }

        if ($mC004['C004_Flag_Isento_ICMS'] == 1 OR $mD006['D006_Flag_Isento_ICMS'] == 'S' OR $D020_Flag_ICMS == 'S') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if ($mD006['D006_Flag_Venda_Compra_Outros'] == 'V' AND $mD024['D024_Flag_Isento_ICMS_Venda'] == 'S') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if ($mD006['D006_Flag_Venda_Compra_Outros'] == 'C' AND $mD024['D024_Flag_Isento_ICMS'] == '1') {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        } else if((($mD069['D069_Flag_Origem_Produto'] == '1' || $mD069['D069_Flag_Origem_Produto'] == '2' || $mD069['D069_Flag_Origem_Produto'] == '3') AND $mD024['D024_D018_Id'] != $mC004['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND ($mD024['D024_Inscricao_Estadual'] == '' OR $mD024['D024_Inscricao_Estadual'] == 'ISENTO' OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F')) {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_Importado_1;
        } else if((($mD069['D069_Flag_Origem_Produto'] == '1' || $mD069['D069_Flag_Origem_Produto'] == '2' || $mD069['D069_Flag_Origem_Produto'] == '3') AND $mD024['D024_D018_Id'] != $mC004['C004_D018_Id'] AND $mD006['D006_Flag_Isento_ICMS'] != 'S') AND (($mD024['D024_Inscricao_Estadual'] != '' AND $mD024['D024_Inscricao_Estadual'] != 'ISENTO') OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J')) {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_Importado_2;
        } else if ($mD024['D024_Aliquota_ICMS'] > 0) {
            $Aliquota_ICMS           = $mD024['D024_Aliquota_ICMS'];
            $Percentual_Reducao_ICMS = $mD024['D024_Percentual_Reducao_ICMS'];
        } else if ($mD024['D024_Inscricao_Estadual'] == '' OR $mD024['D024_Inscricao_Estadual'] == 'ISENTO' OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'F') {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_1;
            $Percentual_Reducao_ICMS = $D018_Percentual_Reducao_ICMS_2;
        } else if (($mD024['D024_Inscricao_Estadual'] != '' AND $mD024['D024_Inscricao_Estadual'] != 'ISENTO') OR $mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $Aliquota_ICMS           = $D018_Aliquota_ICMS_2;
            $Percentual_Reducao_ICMS = $D018_Percentual_Reducao_ICMS_1;
        }

        if ($D024_Valor_IPV > 0) {

            $Aliquota_IPV_Cliente = $D024_Valor_IPV;
            $Flag_IPV_Cliente     = 'S';

            $D019  = mysql_query("SELECT D019_Tipo_1, D019_Percentual_Desconto_ACP FROM D019 WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
            $mD019 = mysql_fetch_array($D019);

            $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
            $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];

        } else {

            if ($mD024['D024_Flag_Revenda_Consumidor'] != '1') {

                $D019  = mysql_query("SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP FROM D019 WHERE D019_Aliquota_ICMS=0;");
                $mD019 = mysql_fetch_array($D019);

                $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
                $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];
                $D019_IPV_Maximo              = $mD019['D019_IPV_Maximo'];

            } else {

                $D019 = mysql_query("SELECT D019_IPV_Maximo,D019_Tipo_1,D019_Percentual_Desconto_ACP FROM D019 WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
                $mD019 = mysql_fetch_array($D019);

                $D019_Tipo_1                  = $mD019['D019_Tipo_1'];
                $D019_Percentual_Desconto_ACP = $mD019['D019_Percentual_Desconto_ACP'];
                $D019_IPV_Maximo              = $mD019['D019_IPV_Maximo'];

            }
            $Aliquota_IPV_Cliente = $D019_IPV_Maximo;
            $Flag_IPV_Cliente     = 'N';
        }

        if ($Documento == 3) {
            $Aliquota_ICMS           = 0;
            $Percentual_Reducao_ICMS = 0;
        }

        $Aliquota_IPV_Utilizado = $Aliquota_IPV_Cliente;

        if ($Comissao > 0) {
            $D019_Tipo_2            = $Aliquota_IPV_Utilizado / (100 / (100 - $D019_Tipo_1));
            $D019_Tipo_2            = (($D019_Tipo_2 - 1) * 100) / $D019_Tipo_2;
            $D019_Tipo_2            = (100 / (100 - ($D019_Tipo_2 + $Comissao)));
            $D019_Tipo_1            = (100 / (100 - ($D019_Tipo_1)));
            $Aliquota_IPV_Utilizado = round($D019_Tipo_1 * $D019_Tipo_2, 4);
        }

        if ($Documento == 3) {
            $Aliquota_IPV_Utilizado = $Aliquota_IPV_Utilizado - ($Aliquota_IPV_Utilizado * (($D019_Percentual_Desconto_ACP * 2) / 100));
        } else {

            if ($mD006['D006_Percentual_Reducao_ICMS'] > 0) {
                $Percentual_Reducao_ICMS = $mD006['D006_Percentual_Reducao_ICMS'];
            }

            if ($mC004['C004_Flag_Isento_ICMS'] == 1 OR $mD006['D006_Flag_Isento_ICMS'] == 'S' OR $D020_Flag_ICMS == 'S') {
                $Aliquota_ICMS           = 0;
                $Percentual_Reducao_ICMS = 0;
            }
            if ($Tipo == 1 AND $Tabelado > 0) {

                if ($mD024['D024_Flag_Revenda_Consumidor'] != '1') {
                    $D019                   = mysql_query("SELECT D019_IPV_Maximo FROM D019 WHERE D019_Aliquota_ICMS=0;");
                    $mD019                  = mysql_fetch_array($D019);
                    $Aliquota_IPV_Utilizado = $mD019['D019_IPV_Maximo'];
                } else {
                    $D019                   = mysql_query("SELECT D019_IPV_Maximo FROM D019 WHERE D019_Aliquota_ICMS='{$Aliquota_ICMS}';");
                    $mD019                  = mysql_fetch_array($D019);
                    $Aliquota_IPV_Utilizado = $mD019['D019_IPV_Maximo'];
                }
            }
        }

        include_once("prog/cad/cad002/excessoesIcms.php");
        if ($Tipo == 1) {
            return $Aliquota_IPV_Utilizado;
        } else if ($Tipo == 2 || $Tipo == 22) {
            return $Aliquota_ICMS;
        } else if ($Tipo == 3 || $Tipo == 33) {
            return $Percentual_Reducao_ICMS;
        }
    }

    /**
     * juntarlocacao
     *
     * @param Form $form
     * @return String/bool
     */ 
    public function juntarlocacao($form,$D009_Id)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $T066_Id_Antigo = (int) $form->campoValorEnviado('T066_Id_Antigo');
        $T066_Id_Novo   = (int) $form->campoValorEnviado('T066_Id_Novo');

        // Verificações
        if ($T066_Id_Antigo <= 0 || $T066_Id_Novo <= 0) {
            return 'Os dois códigos devem ser preenchidos.';
        }
        if ($T066_Id_Antigo == $T066_Id_Novo) {
            return 'Os códigos não podem ser o mesmo.';
        }

        $T066_Antigo = mysql_query("SELECT * from T066 WHERE T066_Id='{$T066_Id_Antigo}'");
        if (mysql_error()) {
            mysql_query('ROLLBACK');
            return 'Erro interno 1';
        }
        $mT066_Antigo = mysql_fetch_array($T066_Antigo);

        $T066_Novo = mysql_query("SELECT * from T066 WHERE T066_Id='{$T066_Id_Novo}'");
        if (mysql_error()) {
            mysql_query('ROLLBACK');
            return 'Erro interno 2';
        }
        $mT066_Novo = mysql_fetch_array($T066_Novo);

        $somandoT066=mysql_query("SELECT SUM(T066_Quantidade_Estoque) as Quantidade_Estoque_Total FROM T066 WHERE T066_Id='{$T066_Id_Antigo}' OR T066_Id='{$T066_Id_Novo}'");
        $mSomandoT066=mysql_fetch_array($somandoT066);
        $quantidade_Estoque_Total = $mSomandoT066['Quantidade_Estoque_Total'];

        $D009=mysql_query("SELECT * FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$D009_Id}'");
        $mD009=mysql_fetch_array($D009);

        $retorno = gJuntarCodigos('T066', $T066_Id_Antigo, $T066_Id_Novo, false, array('T066A'), false);

        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro juntar T066: ' . $retorno;
        }

        $T066=mysql_query("SELECT T066_D004_Id from T066 WHERE T066_Id='{$T066_Id_Novo}'");
        $mT066=mysql_fetch_array($T066);

        if (!mysqli_query("UPDATE T001
                              SET T001_D004_Id='{$mT066['T066_D004_Id']}'
                            WHERE T001_T066_Id='{$T066_Id_Novo}'")) {
            mysql_query('ROLLBACK');
            return 'Erro interno ao atualizar a locação dos históricos.';
        }

        $sql = <<<EOT
            INSERT INTO T001 (
                T001_D024_Id,
                T001_D009_Id,
                T001_T066_Id,
                T001_C004_Id,
                T001_Codigo_Produto,
                T001_Data_Lancamento,
                T001_Descricao_Operacao,
                T001_Quantidade,
                T001_Flag_Complemento,
                T001_Flag_Operacao,
                T001_Valor_Preco_Unitario,
                T001_Observacao,
                T001_Valor_Custo_Unitario,
                T001_Flag_Atualiza_Custo,
                T001_Quantidade_Saldo,
                T001_D004_Id
            ) VALUES (
                '{$mD009['D049_D024_Id']}', 
                '{$mD009['D009_Id']}', 
                '{$T066_Id_Novo}', 
                '{$mD009['D009_C004_Id']}', 
                '{$mD009['D001_Codigo_Produto']}', 
                current_date(), 
                'JUNÇÃO LOCAÇÃO MANUAL - {$T066_Id_Novo}+{$T066_Id_Antigo}',
                '{$quantidade_Estoque_Total}',
                'M',
                'B',
                '0',
                '',
                '0',
                'N',
                '0',
                '{$mT066_Novo['T066_D004_Id']}'
            );
EOT;
        $res = mysql_query($sql, false);
        if (!$res) {
            mysql_query('ROLLBACK');
            return 'Erro interno 10';
        }

        $retorno = $this->D001_reprocessa_historico(false, $D009_Id, 0);
        if ($retorno !== true) {
            mysql_query('ROLLBACK');
            return 'Erro ao reprocessar o histórico, Por favor, contate o suporte técnico. <br />Erro: ' . $retorno;
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        
        return true;

    }

     /**
     * prepararImportarCSV
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function importarCSV($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('Codigo','Descricao','Departamento','Setor','Grupo','Sub-Grupo','NCM','Origem','Marca','Unidade','Custo','Preco1','Preco2','Preco3','Preco4','Peso_Liquido','Peso_Bruto','Locacao','Codigo_Fornecedor','Codigo_Produto_Fornecedor','EAN_Produto_Fornecedor','Link Foto','Pre_Cadastro');
         //posicao do array <0>      <1>           <2>         <3>     <4>        <5>      <6>     <7>     <8>      <9>     <10>      11      <12>      13      14>       15                 16       17           18                   19                      <20>                    <21>            22


        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {

                // if(($array_linha == fgetcsv($ponteiro,1000,',')) ==True){
                //     $return .= "Documento não está com o formato correto. Favor verificar!\\n";
                // }     
                $array_linha = explode(';',utf8_encode($array_linha));
                for ($count=0;$count<count($array_linha);$count++){
                    if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                         $array_linha[$count] = utf8_encode($array_linha[$count]); 
                         $array_linha[$count] = trim($array_linha[$count]);                     
                    }
                }
                
                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                  $total_cols = count($array_cols);
                    if ($total_cols!=count($array_cols)) {
                        $return .= "O número de colunas é diferente de {$total_cols} \\n";
                    }
                    $array_linha[0] = str_replace(array(chr(239),chr(197),chr(201),chr(201), chr(195), chr(175), chr(194), chr(187), chr(191)),'',$array_linha[0]);
                    for ($count=0;$count<count($array_linha);$count++){
                        if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            //Verificação erro do nome da coluna Codigo
                            /*
                            $aCodigo = str_split($array_linha[0]);
                            $array_linha[$count]='';
                            for ($count=0;$count<count($aCodigo);$count++){
                                $return.= ord($aCodigo[$count]).'-';
                            }
                            return $return;
                            // var_dump($array_linha[$count]);
                            // var_dump($array_cols[$count]);

                            //239-197-201-67-111-100-105-103-111-
                            //195-175-194-187-194-191-67-111-100-105-103-111-

                            */
                            $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                           
                        }
                    }                   

                } else {                                                        
                    if(empty($array_linha[0])){
                        $return .=  $msgLinha . "O código está em branco. \\n";
                    }else{
                        // Validar se código está duplicado ou está branco   
                        $cClausula = "SELECT D001_Id from D001 WHERE D001_Codigo_Produto='".$array_linha[0]."'";
                        $cSQL_D001 = mysql_query($cClausula);
                        $mD001     = mysql_fetch_array($cSQL_D001);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }     
                        if(mysql_num_rows($cSQL_D001) > 0){
                            $return .= $msgLinha . "O código do produto ". $array_linha[0] ." já está cadastrado!\\n";
                        }
                    }
                    
                     // Validar se descricao está em branco
                    if(empty($array_linha[1])){
                        $return .=  $msgLinha . "A coluna de descrição está em branco.\\n";
                    }

                    // Origem deve estar entre 0 e 8
                    if($array_linha[7]>8 || $array_linha[7]<0){
                        $return .=  $msgLinha . "A coluna origem deve ser um número entre 0 e 8.\\n";
                    }
                    
                    // Validar se departamento(2), setor(3), grupo(4), Sub-Grupo(5), NCM(6),marca(8), Unidade(9) estão cadastrado
                    //Validar se os campos departamento, setor, grupo, sub-grupo estao em branco

                    //DEPARTAMENTO 
                    if(!empty($array_linha[2])){
                        $cClausula = "SELECT C008_Id FROM C008 WHERE C008_Tipo='".trim($array_linha[2])."'";
                        $cSQL_C008 = mysql_query($cClausula);
                        $mC008     = mysql_fetch_array($cSQL_C008);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_C008) == 0) {          
                             $return .=  $msgLinha . "Departamento " .$array_linha[2]. " não está cadastrado.\\n";
                        }
                    }
                            
                    //SETOR
                    if(!empty($array_linha[3])){
                        $cClausula = "SELECT D060_Id FROM D060 WHERE D060_Descricao_Grupo='".trim($array_linha[3])."'";
                        $cSQL_D060 = mysql_query($cClausula);
                        $mD060     = mysql_fetch_array($cSQL_D060);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D060) == 0) {          
                             $return .=  $msgLinha . "Setor ".$array_linha[3]." não está cadastrado.\\n";
                        }
                    }    
                    //GRUPO
                    if(!empty($array_linha[4])){
                        $cClausula = "SELECT D003_Id FROM D003 WHERE D003_Nome_Linha='".trim($array_linha[4])."'";
                        $cSQL_D003 = mysql_query($cClausula);
                        $mD003     = mysql_fetch_array($cSQL_D003);
                        $cErro     = mysql_error();
                         if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D003) == 0) {          
                             $return .=  $msgLinha ."Grupo ".$array_linha[4]." não está cadastrado.\\n";
                        }
                    }    
                    //SUB_GRUPO
                    if(!empty($array_linha[5])){    
                        $cClausula = "SELECT D002_Id FROM D002 WHERE D002_Descricao_Produto='".trim($array_linha[5])."'";
                        $cSQL_D002 = mysql_query($cClausula);
                        $mD002     = mysql_fetch_array($cSQL_D002);
                        $cErro     = mysql_error();
                         if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D002) == 0) {          
                             $return .=  $msgLinha . "Sub-Grupo ".$array_linha[5]." não está cadastrado.\\n";
                        }
                    }
                            
                    //NCM
                    if(!empty($array_linha[6])){
                        $cClausula = "SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='".trim($array_linha[6])."' AND D005_Flag_Ativo = 'S'";                        
                        $cSQL_D005 = mysql_query($cClausula);
                        $mD005     = mysql_fetch_array($cSQL_D005);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D005) == 0) {          
                            $return .=  $msgLinha . " A NCM ".$array_linha[6]." não está cadastrada.\\n";
                        }
                    }

                    //MARCA
                    if(!empty($array_linha[8])){
                        $cClausula = "SELECT D082_Id FROM D082 WHERE D082_Marca='".trim($array_linha[8])."'";
                        $cSQL_D082 = mysql_query($cClausula);
                        $mD082     = mysql_fetch_array($cSQL_D082);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D082) == 0) {          
                            $return .=  $msgLinha . "A Marca ".$array_linha[8]." não está cadastrada.\\n";
                        }
                    }

                    //UNIDADE
                    if(!empty($array_linha[9])){
                        $cClausula = "SELECT D037_Id FROM D037 WHERE D037_Unidade='".trim($array_linha[9])."'";
                        $cSQL_D037 = mysql_query($cClausula);
                        $mD037     = mysql_fetch_array($cSQL_D037);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D037) == 0) {          
                            $return .=  $msgLinha . "A Unidade ".$array_linha[9]." não está cadastrada.\\n";
                        }
                    }
                    
                    //LOCACOES
                    if(!empty($array_linha[17])){
                        $cClausula = "SELECT D004_Id FROM D004 WHERE D004_Local='".trim($array_linha[17])."'";
                        $cSQL_D004 = mysql_query($cClausula);
                        $mD004     = mysql_fetch_array($cSQL_D004);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\n Clausula: ".$cClausula; 
                        }   
                        if (mysql_num_rows($cSQL_D004) == 0) {          
                            $return .=  $msgLinha . "A Locação ".$array_linha[17]." não está cadastrada.\\n";
                        }
                    }

                    //FORNECEDOR
                    if(!empty($array_linha[18])){
                        $cClausula = "SELECT D024_Id FROM D024 WHERE D024_Id='".trim($array_linha[18])."'";
                        $cSQL_D024 = mysql_query($cClausula);
                        $mD024     = mysql_fetch_array($cSQL_D024);
                        $cErro     = mysql_error();
                        if(!empty($cErro)){
                            $return .= "Erro: ".$cErro."\\n Clausula: ".$cClausula; 
                        } 
                        if (mysql_num_rows($cSQL_D024) == 0) {          
                            $return .=  $msgLinha . "O Fornecedor ".$array_linha[18]." não está cadastrado.\\n";
                        }
                    }

                    //SÓ IRÁ INSERIR OS DADOS DA TABELA D083 SE O CODIGO DO FORNECEDOR ESTIVER NO CSV
                    if(empty($array_linha[18])){
                        if(!empty($array_linha[19]) || !empty($array_linha[20])){
                            $return .= $msgLinha . "O Codigo Produto-Fornecedor e o EAN fornecedor não podem ser cadastrados sem o código do fornecedor.\\n";
                        }
                    }
                }
                $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro      = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;
            //PERCORRER AS LINHAS NOVAMENTE 
            while (($array_linha = fgetcsv($ponteiro,1000,';')) !== FALSE) {
                                    
                    if($linha==1){
                        $linha++;
                        continue;
                    } 
                    $linha++;
                    //REALIZA OS SELECT´S NO BANCO PARA ALIMENTAR O INSERT
                    //DEPARTAMENTO 
                    $cClausula = "SELECT C008_Id FROM C008 WHERE C008_Tipo='{$array_linha[2]}'";
                    $cSQL_C008 = mysql_query($cClausula);
                    $mC008     = mysql_fetch_array($cSQL_C008);

                    //SETOR
                    $cClausula = "SELECT D060_Id FROM D060 WHERE D060_Descricao_Grupo='{$array_linha[3]}'";
                    $cSQL_D060 = mysql_query($cClausula);
                    $mD060     = mysql_fetch_array($cSQL_D060);

                    //GRUPO
                    $cClausula = "SELECT D003_Id FROM D003 WHERE D003_Nome_Linha='{$array_linha[4]}'";
                    $cSQL_D003 = mysql_query($cClausula);
                    $mD003     = mysql_fetch_array($cSQL_D003);

                    //SUB_GRUPO
                    $cClausula = "SELECT D002_Id FROM D002 WHERE D002_Descricao_Produto='{$array_linha[5]}'";
                    $cSQL_D002 = mysql_query($cClausula);
                    $mD002     = mysql_fetch_array($cSQL_D002);

                    //NCM
                    $cClausula = "SELECT D005_Id FROM D005 WHERE D005_Classificacao_Fiscal='{$array_linha[6]}' AND D005_Flag_Ativo = 'S'";
                    $cSQL_D005 = mysql_query($cClausula);
                    $mD005     = mysql_fetch_array($cSQL_D005);

                    //MARCA
                    $cClausula = "SELECT D082_Id FROM D082 WHERE D082_Marca='{$array_linha[8]}'";
                    $cSQL_D082 = mysql_query($cClausula);
                    $mD082     = mysql_fetch_array($cSQL_D082);

                    //UNIDADE
                    $cClausula = "SELECT D037_Id FROM D037 WHERE D037_Unidade='{$array_linha[9]}'";
                    $cSQL_D037 = mysql_query($cClausula);
                    $mD037     = mysql_fetch_array($cSQL_D037);

                    //LOCACOES
                    $cClausula = "SELECT D004_Id FROM D004 WHERE D004_Local='{$array_linha[17]}'";
                    $cSQL_D004 = mysql_query($cClausula);
                    $mD004     = mysql_fetch_array($cSQL_D004);

                    //FORNECEDOR
                    $cClausula = "SELECT D024_Id FROM D024 WHERE D024_Id='{$array_linha[18]}'";
                    $cSQL_D024 = mysql_query($cClausula);
                    $mD024     = mysql_fetch_array($cSQL_D024);

                    //TROCAR VIRGULA POR PONTO PARA A INSERÇÃO NO BANCO
                    $array_linha[10]=gCorrigeNumeroInverte($array_linha[10]);
                    $array_linha[11]=gCorrigeNumeroInverte($array_linha[11]);
                    $array_linha[12]=gCorrigeNumeroInverte($array_linha[12]);
                    $array_linha[13]=gCorrigeNumeroInverte($array_linha[13]);
                    $array_linha[14]=gCorrigeNumeroInverte($array_linha[14]);           
                    $array_linha[15]=gCorrigeNumeroInverte($array_linha[15]);
                    $array_linha[16]=gCorrigeNumeroInverte($array_linha[16]);

                    $array_linha[22] = trim($array_linha[22]);
                    //caso a coluna Pre-Cadastro estiver em branco, sera cadastrado como 'S'
                    if(empty($array_linha[22])){
                        $array_linha[22] = 'S';
                    }

                    //CONVERTE PARA DESCRICAO PARA MAIUSCULO
                    $array_linha[1] = strtoupper(mysql_real_escape_string($array_linha[1]));
                  
                    // INSERE PRODUTO NA TABELA D001
                    $cClausula = <<<EOT
                    insert into D001(
                            D001_Codigo_Produto,
                            D001_Descricao_Produto,
                            D001_C008_Id,
                            D001_D060_Id,
                            D001_D003_Id,
                            D001_D002_Id,
                            D001_D005_Id,
                            D001_D037_Id,
                            D001_Peso_Unitario_Kg, 
                            D001_Peso_Unitario_Bruto,
                            D001_Flag_Pre_Cadastro
                            )
                            values
                            (
                            '{$array_linha['0']}',
                            '{$array_linha['1']}',
                            '{$mC008['C008_Id']}', 
                            '{$mD060['D060_Id']}',
                            '{$mD003['D003_Id']}',
                            '{$mD002['D002_Id']}',
                            '{$mD005['D005_Id']}',
                            '{$mD037['D037_Id']}',
                            '{$array_linha[15]}',
                            '{$array_linha[16]}',
                            '{$array_linha[22]}')

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                         mysql_query("ROLLBACK");
                         return "Erro: " . $cErro . "\\n" . $cClausula;
                    } else {
                         $D001_Id = $g['mysqlLastId'];
                    }

                    //INCLUI O REGISTRO DAS OUTRAS TABELAS
                    $cClausula = <<<EOT
                        insert into D049(
                            D049_D001_Id,
                            D049_Origem_Mercadoria,
                            D049_D082_Id,
                            D049_Data_Cadastro
                            )
                            values
                            (
                            '{$D001_Id}',
                            '{$array_linha[7]}',
                            '{$mD082['D082_Id']}',
                            curdate())

EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                         mysql_query("ROLLBACK");
                         return "Erro: " . $cErro . "\\n" . $cClausula;
                    } else {
                        $D049_Id = $g['mysqlLastId'];
                    }
                       
                    //INCLUI A URL DA FOTO 
                    if(!empty($array_linha[21])){
                        $cClausula = <<<EOT
                            insert into T144(
                                T144_D001_Id,
                                T144_Url,
                                )
                                values
                                (
                                '{$D001_Id}',
                                '{$array_linha[21]}')

EOT;
                        $cSql  = mysql_query($cClausula);
                        $cErro = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . $cErro . "\\n" . $cClausula;
                        } 
                    }
                    //INCLUI OS DADOS NA TABELA D083 CASO HAJA DADOS DO FORNECEDOR-PRODUTO
                    if(!empty($array_linha[18])){
                        if(!empty($array_linha[19]) || !empty($array_linha[20])){
                            $cClausula = <<<EOT
                                insert into D083(
                                    D083_D001_Id,
                                    D083_D024_Id,
                                    D083_Codigo_Produto_Fornecedor,
                                    D083_Codigo_Barras
                                    )
                                    values
                                    (
                                    '{$D001_Id}',
                                    '{$mD024['D024_Id']}',
                                    '{$array_linha[19]}',
                                    '{$array_linha[20]}')
EOT;
                            $cSql  = mysql_query($cClausula);
                            $cErro = mysql_error();
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "Erro: " . $cErro . "\\n" . $cClausula;
                            } 
                        }
                    }

                    //FAZ A CONEXÃO ENTRE AS TABELAS D001 D049 E D009
                    $C004  = mysql_query("select C004_Id from C004");

                    while ($mC004 = mysql_fetch_array($C004)) {

                            $cClausula = <<<EOT
                            insert into D009(
                                D009_D001_Id,
                                D009_D049_Id,
                                D009_C004_Id,
                                D009_Valor_Preco_Tabela,
                                D009_Preco_1,
                                D009_Preco_2,
                                D009_Preco_3,
                                D009_Preco_4    
                                )
                                values
                                (
                                '{$D001_Id}',
                                '{$D049_Id}',
                                '{$mC004['C004_Id']}',
                                '{$array_linha[10]}',
                                '{$array_linha[11]}',
                                '{$array_linha[12]}',
                                '{$array_linha[13]}',
                                '{$array_linha[14]}')
EOT;

                           $cSql  = mysql_query($cClausula);
                            $cErro = mysql_error();
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "Erro: " . $cErro . "<br />" . $cClausula;
                            } else {
                                $D009_Id = $g['mysqlLastId'];
                            }

                            $T066 = mysql_query("select T066_Id from T066 where T066_D009_Id='$D009_Id'");
                            $mT066 = mysql_fetch_array($T066);

                            if ($mT066['T066_Id']<=0){
                                mysql_query("ROLLBACK");
                                return "Erro: Não incluiu o T066 ao incluir o D009 (Código: ".$array_linha[10]."), informe este erro ao suporte técnico.";
                            }

                            $cSql  = mysql_query("update T066 set T066_D004_Id='{$mD004['D004_Id']}' where T066_Id='{$mT066['T066_Id']}'");
                            $cErro = mysql_error();
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "Erro: " . $cErro . "<br />" . $cClausula;
                            } 
                    }
            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");

            return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." produtos!!";

      
        } else {

            return $return;

        }        
    

    }


    /**
     * duplicar_form2Comum
     *
     * @param String $divId
     * @param String $divIdRoot
     * @param String $tabela
     * @param String $D001_Id_Antigo
     * @return String
     */    
    public function duplicar_form2Comum($divId, $divIdRoot, $tabela, $D001_Id_Antigo)
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o pedido
        $D001  = mysql_query("SELECT * FROM D001 WHERE D001_Id='{$D001_Id_Antigo}'");
        $mD001 = mysql_fetch_assoc($D001);

        if (mysql_num_rows($D001) == 0) {
            $retorno = <<<EOT
            var \$buttons = {
              "Fechar": function() { $( this).dialog("destroy").remove(); }
            };
            dialogConfirm("Erro", "Produto não encontrado", \$buttons);
EOT;
            return $retorno;
        }
        
        $campos  = "";
        $valores = "";
        foreach ($mD001 as $key => $value) {
            if ($key == 'D001_Id') {
                continue;
            }else if($key == "D001_Codigo_Barras"){
                $campos .= $key . ", ";
                $novoEAN= $this->obterNovoCodigoEAN();
                $valores .= "'" . mysql_real_escape_string($novoEAN) . "',";
            }else if($key == "D001_Codigo_Produto"){
                $campos .= $key . ", ";
                if($g['C031']['habilitaMascaraCodigo'] == 'S'){   
                    $novoCodigo= $this->obterNovoCodigoProduto($D001_Id_Antigo);
                }else{
                    $novoCodigo= $this->obterNovoCodigoProduto();
                }
                $valores .= "'" . mysql_real_escape_string($novoCodigo) . "',";
            }else if($key == 'D001_JSON') {            
                continue;                   
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }

        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insD001 = "INSERT INTO D001 ({$campos}) VALUES ({$valores})";
        $resD001 = mysql_query($insD001);                

        if (!$resD001) {
            mysql_query("ROLLBACK");
            $erro =  mysql_error();
            return "alert('Erro 1: Insert D001');";
        }
        $D001_Id_Novo = $g['mysqlLastId'];

        #region Grava D001A
        $D001A = mysql_query("SELECT * FROM D001A WHERE D001A_D001_Id='{$D001_Id_Antigo}'");
        if(mysql_num_rows($D001A) > 0){
            $mD001A = mysql_fetch_assoc($D001A);
            $campos  = "";
            $valores = "";
            foreach ($mD001A as $key => $value) {
                if ($key == 'D001A_Id') {
                    continue;
                } else if($key == "D001A_D001_Id"){
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($D001_Id_Novo) . "',";
                }else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insD001a = "INSERT INTO D001A ({$campos}) VALUES ({$valores})";
            $resD001a = mysql_query($insD001a);
            if (!$resD001a) {
                mysql_query("ROLLBACK");
                $erro =  mysql_error();
                return "alert('Erro 2: Insert D001A');";
            }
        }else{
            $insD001a = mysql_query("INSERT INTO D001A (D001A_D001_Id) VALUES ('{$D001_Id_Novo}')");
            if (!$insD001a) {
                mysql_query("ROLLBACK");
                $erro =  mysql_error();
                return "alert('Erro 3: Insert D001A');";
            }
        }
        #endregion

        // Copia fotos do produto
        $T172 = mysql_query("SELECT * FROM T172 WHERE T172_D001_Id = '{$D001_Id_Antigo}'");
        if(mysql_num_rows($T172) > 0){
            
            $dirAntigo = "{$g['pathDados']}produtos/".$D001_Id_Antigo."/fotos/";
            $dirNovo   = "{$g['pathDados']}produtos/".$D001_Id_Novo."/fotos/";
            is_dir($dirNovo) or mkdir($dirNovo, 0777, true);            
            
            while($mT172 = mysql_fetch_assoc($T172)){
                $campos  = "";
                $valores = "";

                foreach ($mT172 as $key => $value) {
                    if ($key == 'T172_Id') {
                        continue;
                    } else if($key == "T172_D001_Id"){
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D001_Id_Novo) . "',";     
                    }else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }        
                }

                $campos         = trim($campos, ', ');
                $valores        = trim($valores, ',');
                $insT172 = "INSERT INTO T172 ({$campos}) VALUES ({$valores})";
                mysql_query($insT172);
                $T172_Id_Novo = $g['mysqlLastId'];

                $T172_Id_Antigo = $mT172['T172_Id'];
                $pathinfo       = pathinfo($mT172['T172_Nome_Arquivo']);

                //Copia arquivo tamanho original
                $fileAntigo   = $dirAntigo . $T172_Id_Antigo . '.'. $pathinfo['extension'];
                $fileNovo     = $dirNovo . $T172_Id_Novo . '.'. $pathinfo['extension'];
                if(file_exists($fileAntigo)){
                    copy($fileAntigo, $fileNovo);
                }

                // Copia tumb
                $fileAntigo = $dirAntigo . $T172_Id_Antigo . '-100.'. $pathinfo['extension'];
                $fileNovo   = $dirNovo . $T172_Id_Novo . '-100.'. $pathinfo['extension'];
                if(file_exists($fileAntigo)){
                    copy($fileAntigo, $fileNovo);
                }
            }
        }

        // Copia documentos do produto
        $T140 = mysql_query("SELECT * FROM T140 WHERE T140_D001_Id = '{$D001_Id_Antigo}'");
        if(mysql_num_rows($T140) > 0){
            
            $dir = "{$g['pathDados']}certificados/";        
            
            while($mT140 = mysql_fetch_assoc($T140)){
                $campos  = "";
                $valores = "";

                foreach ($mT140 as $key => $value) {
                    if ($key == 'T140_Id') {
                        continue;
                    } else if($key == "T140_D001_Id"){
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D001_Id_Novo) . "',";     
                    }else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }        
                }

                $campos         = trim($campos, ', ');
                $valores        = trim($valores, ',');
                $insT140 = "INSERT INTO T140 ({$campos}) VALUES ({$valores})";
                mysql_query($insT140);
                $T140_Id_Novo = $g['mysqlLastId'];

                $T140_Id_Antigo = $mT140['T140_Id'];
                $pathinfo       = pathinfo($mT140['T140_Nome_Arquivo']);

                //Copia arquivo tamanho original
                $fileAntigo   = $dir . $T140_Id_Antigo . '.'. $pathinfo['extension'];
                $fileNovo     = $dir . $T140_Id_Novo . '.'. $pathinfo['extension'];
                if(file_exists($fileAntigo)){
                    copy($fileAntigo, $fileNovo);
                }
            }
        }
        
        // Selecionando todos os itens do D049
        $D049 = mysql_query("SELECT * FROM D049 WHERE D049_D001_Id='{$D001_Id_Antigo}'");
        
        //While que insere o D049 novo
        while ($mD049 = mysql_fetch_assoc($D049)) {
            $campos  = "";
            $valores = "";
            foreach ($mD049 as $key => $value) {
                if ($key == 'D049_Id') {
                    $D049_Id_Antigo=$value;
                    continue;
                } else if($key == "D049_D001_Id"){
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($D001_Id_Novo) . "',";
                }else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insD049 = "INSERT INTO D049 ({$campos}) VALUES ({$valores})";
            $resD049 = mysql_query($insD049);
            if (!$resD049) {
                mysql_query("ROLLBACK");
                $erro =  mysql_error();
                return "alert('Erro 4: Insert D049');";
            }

            $D049_Id_Novo = $g['mysqlLastId'];

            #region Grava D049A
            $D049A = mysql_query("SELECT * FROM D049A WHERE D049A_D049_Id='{$D049_Id_Antigo}'");
            if(mysql_num_rows($D049A) > 0){
                $mD049A = mysql_fetch_assoc($D049A);
                $campos  = "";
                $valores = "";
                foreach ($mD049A as $key => $value) {
                    if ($key == 'D049A_Id') {
                        continue;
                    } else if($key == "D049A_D049_Id"){
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D049_Id_Novo) . "',";
                    }else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insD049A = "INSERT INTO D049A ({$campos}) VALUES ({$valores})";
                $resD049A = mysql_query($insD049A);
                if (!$resD049A) {
                    mysql_query("ROLLBACK");
                    $erro =  mysql_error();
                    return "alert('Erro 5: Insert D049A);";
                }
            }else{
                mysql_query("INSERT INTO D049A (D049A_D049_Id) VALUES ('{$D049_Id_Novo}')");
                $error =  mysql_error();
                if(!empty($error)){
                    return "alert('Erro 6: Insert D049A');";
                }
            }
            #endregion

            // Selecionando todos os itens do D009 de dentro do D049
            $D009 = mysql_query("SELECT * FROM D009 WHERE D009_D049_Id='{$D049_Id_Antigo}'");
            while ($mD009 = mysql_fetch_assoc($D009)) {
                $campos  = "";
                $valores = "";
                foreach ($mD009 as $key => $value) {
                    if ($key == 'D009_Id') {
                        $D009_Id_Antigo=$value;
                        continue;
                    } else if($key == "D009_D001_Id"){
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D001_Id_Novo) . "',";
                    } else if($key == "D009_D049_Id"){
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($D049_Id_Novo) . "',";
                    }else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value) . "',";
                    }
                }
                $campos  = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insD009 = "INSERT INTO D009 ({$campos}) VALUES ({$valores})";
                $resD009 = mysql_query($insD009);
                if (!$resD009) {
                    mysql_query("ROLLBACK");
                    $erro =  mysql_error();
                     return "alert('Erro 7: Insert D009');";
                }

                $D009_Id_Novo = $g['mysqlLastId'];

                #region Grava D009A
                $D009A = mysql_query("SELECT * FROM D009A WHERE D009A_D009_Id='{$D009_Id_Antigo}'");
                if(mysql_num_rows($D009A) > 0){
                    $mD009A = mysql_fetch_assoc($D009A);
                    $campos  = "";
                    $valores = "";
                    foreach ($mD009A as $key => $value) {
                        if ($key == 'D009A_Id') {
                            continue;
                        } else if($key == "D009A_D009_Id"){
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($D009_Id_Novo) . "',";
                        } else if($key == 'D009A_JSON') {            
                            continue;                   
                        } else {
                            $campos .= $key . ", ";
                            $valores .= "'" . mysql_real_escape_string($value) . "',";
                        }
                    }
                    $campos  = trim($campos, ', ');
                    $valores = trim($valores, ',');
                    $insD009A = "INSERT INTO D009A ({$campos}) VALUES ({$valores})";
                    $resD009A = mysql_query($insD009A);
                    if (!$resD009A) {
                        mysql_query("ROLLBACK");
                        $erro =  mysql_error();
                        return "alert('Erro 8: Insert D009A');";
                    }
                } else {
                    mysql_query("INSERT INTO D009A (D009A_D009_Id) VALUES ('{$D009_Id_Novo}')");
                    $error =  mysql_error();
                    if(!empty($error)){
                        return "alert('Erro 9: Insert D009A');";
                    }
                }

                #endregion
                //return "alert('D009_Id ANTIGO:{$D009_Id_Antigo}      D009_ID NOVO:{$D009_Id_Novo}')";
                // Selecionando todos os itens do T066 de dentro do D009
                // $T066 = mysql_query("SELECT * FROM T066 WHERE T066_D009_Id='{$D009_Id_Antigo}'");
                // while ($mT066 = mysql_fetch_assoc($T066)) {
                //     $campos  = "";
                //     $valores = "";
                //     foreach ($mT066 as $key => $value) {
                //         if ($key == 'T066_Id') {
                //             continue;
                //         } else if($key == "T066_D009_Id"){
                //             $campos .= $key . ", ";
                //             $valores .= "'" . mysql_real_escape_string($D009_Id_Novo) . "',";
                //         }else {
                //             $campos .= $key . ", ";
                //             $valores .= "'" . mysql_real_escape_string($value) . "',";
                //         }
                //     }
                //     $campos  = trim($campos, ', ');
                //     $valores = trim($valores, ',');
                //     $insT066 = "INSERT INTO T066 ({$campos}) VALUES ({$valores})";
                //     $resT066 = mysql_query($insT066);
                //     if (!$resT066) {
                //         mysql_query("ROLLBACK");
                //         $erro =  mysql_error();
                //         return "alert('Erro 4:{$erro}');";
                //     }
                // }   
            }

        }

        $D001_Id = $D001_Id_Novo;
        $this->D001_reprocessa_historico($D001_Id, 0, false, false);

        mysql_query("COMMIT");
        $novoId = uniqid();
        return "$('#{$divId}').closest('.ui-dialog-content').dialog('close'); abrirJanela(false, '{$divIdRoot}', '{$divId}' ,'{$novoId}', '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$D001_Id}') + '&tabela=D001', [990,605]);";
    }
    

    public function processarD069($D009_Id, $D069_Id){
        global $g;
        $chaveValor = $D069_Id;
        log("chegou: ".$D069_Id);
        $sql = "SELECT D069_Valor_Custo_Unitario, D069_Data_Atualizacao_Custo FROM D069 WHERE D069_Id = '{$chaveValor}'";
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        $D069_Custo_Unitario_anterior = $row['D069_Valor_Custo_Unitario'];

        // require_once("bibliotecas/classes/CAD002.php");
        // $CAD002 = new CAD002();
        
        mysql_query("call D069_Gravar_Totalizacao('$chaveValor')");
        $D069=mysqli_query("select D069_Id,D069_D009_Id,D049_Id,D070_D024_Id,D069_Codigo_Substituto,D049_D001_Id,D069_Flag_Tabela_Principal,D069_D070_Id,D009_C004_Id,D009_Id,D069_Data_Atualizacao_Custo, D009_D069_Id from D069 left join D009 on D009_Id=D069_D009_Id left join D049 on D049_Id=D009_D049_Id left join D070 on D070_Id=D069_D070_Id where D069_Id='$chaveValor'");
        $mD069=mysql_fetch_array($D069);
        // Deixa apenas uma tabela marcada para este D009
        /*
        if ($mD069['D069_Flag_Tabela_Principal']=='S' and $mD069['D009_Id']>0){
            //mysqli_query("UPDATE D069 SET D069_Flag_Tabela_Principal='' WHERE D069_D009_Id='{$mD069['D009_Id']}' and D069_Id!='{$mD069['D069_Id']}'");
        }
        $D069Principal=mysqli_query("select D069_Id,D069_Flag_Tabela_Principal,D069_D070_Id,D009_Id,D069_Data_Atualizacao_Custo from D069 left join D009 on D009_Id=D069_D009_Id left join D049 on D049_Id=D009_D049_Id left join D070 on D070_Id=D069_D070_Id where D069_Id='$chaveValor'");
        $mD069Principal=mysql_fetch_array($D069Principal);
        // Atualiza D009 conforme tabela principal
        if ($mD069Principal['D069_Flag_Tabela_Principal']=='S' and $mD069Principal['D009_Id']>0){
            mysqli_query("UPDATE D009 SET D009_D069_Id='{$mD069Principal['D069_Id']}', D009_D070_Id='{$mD069Principal['D069_D070_Id']}', D009_Data_Atualizacao_Custo_Fornecedor = '{$mD069Principal['D069_Data_Atualizacao_Custo']}' WHERE D009_Id='{$mD069Principal['D009_Id']}'");
        } else {
            mysqli_query("UPDATE D009 SET D009_D069_Id='', D009_D070_Id='' WHERE D009_Id='{$mD069['D009_Id']}' and D009_D069_Id='{$mD069Principal['D069_Id']}'");
        }
        */
        
        if($mD069['D069_Codigo_Substituto']!= ''){
            $this->definirCodigoFornecedor($mD069['D069_D009_Id'], $mD069['D070_D024_Id'], $mD069['D069_Codigo_Substituto'], '', false, true);
        }

        // Verifica se o campo D069_Valor_Custo_Unitario foi alterado
        $sql = "SELECT D069_Valor_Custo_Unitario, D069_D009_Id, D069_Data_Atualizacao_Custo, D069_C007_Id_Atualizacao_Custo FROM D069 WHERE D069_Id = '{$chaveValor}'";
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        log("D069_Custo_Unitario_anterior: ".$D069_Custo_Unitario_anterior . " - row['D069_Valor_Custo_Unitario']: ".$row['D069_Valor_Custo_Unitario'] . " Data atualizacao custo: ".$row['D069_Data_Atualizacao_Custo']);
        if (($D069_Custo_Unitario_anterior != $row['D069_Valor_Custo_Unitario']) || ($row['D069_Valor_Custo_Unitario'] > 0 && $row['D069_Data_Atualizacao_Custo'] == '0000-00-00')) {
            $T162_Extra = "Trigger PHP - Tabela D069 (1)";
            $camposAlterados = array();
            $camposAlterados['D069_Data_Atualizacao_Custo'] = array($row['D069_Data_Atualizacao_Custo'], date('Y-m-d'));
            $camposAlterados['D069_C007_Id_Atualizacao_Custo'] = array($row['D069_C007_Id_Atualizacao_Custo'], $g['usuarioAtual']);
            $camposAlterados = mysql_real_escape_string(serialize($camposAlterados));

            mysqli_query("UPDATE D069 SET D069_Data_Atualizacao_Custo = CURDATE(), D069_C007_Id_Atualizacao_Custo = '{$g['usuarioAtual']}' WHERE D069_Id = '{$chaveValor}'");
            mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'U', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'D069', '{$chaveValor}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
        }
        mysqli_query("UPDATE D069 SET D069_Data_Hora_Ultima_Alteracao = CURRENT_TIMESTAMP(), D069_C007_Id_Ultima_Alteracao = '{$g['usuarioAtual']}' WHERE D069_Id = '{$chaveValor}'");

        $sqlD009 = mysql_query("SELECT D009_Data_Atualizacao_Custo_Fornecedor, D009_D069_Id FROM D009 WHERE D009_Id = '{$row['D069_D009_Id']}'");
        $rowD009 = mysql_fetch_array($sqlD009);
        if($mD069['D009_D069_Id'] != $chaveValor && $mD069['D009_Id']>0){
            $T162_Extra = "Trigger PHP - Tabela D069 (2)";
            $camposAlterados = array();
            $camposAlterados['D009_Data_Atualizacao_Custo_Fornecedor'] = array($rowD009['D009_Data_Atualizacao_Custo_Fornecedor'], date('Y-m-d'));
            $camposAlterados = mysql_real_escape_string(serialize($camposAlterados));

            mysqli_query("UPDATE D009 SET D009_Data_Atualizacao_Custo_Fornecedor = CURDATE() WHERE D009_Id = '{$row['D069_D009_Id']}' AND D009_D069_Id = '{$chaveValor}'");
            mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'U', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'D009', '{$row['D069_D009_Id']}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
        }
        $this->atualizarCustoProduto($row['D069_D009_Id']);

        return true;
    }

    /**
     * populaT289
     *
     * @param String $T001_D009_Id
     * @param String $T001_Quantidade   
     * @param String $T001_T014_Id
     * @param String $T001_Flag_Operacao
     * @param String $T001_Data_Lancamento
     * @return true
     */     
    function populaT289($T001_D009_Id, $T001_T014_Id, $T001_T008_Id, $T001_Quantidade, $T001_Flag_Operacao, $T001_Data_Lancamento)
    {
        global $g;

        if ($T001_T014_Id>0) {

            $sqlT014 = mysql_query("SELECT T014_Id,
                                           D006_Flag_Venda_Compra_Outros,
                                           D006_Flag_Atualiza_Custo,
                                           T014_Valor_Base_ICMS_Substituicao,
                                           T014_Valor_ICMS_Substituicao_Unitario,
                                           T014_Valor_Base_ICMS / $T001_Quantidade as T014_Valor_Base_ICMS,
                                           T014_Valor_ICMS / $T001_Quantidade as T014_Valor_ICMS
                                      FROM T014
                                 LEFT JOIN D006 ON D006_Id=T014_D006_Id
                                     WHERE T014_Id = '{$T001_T014_Id}'");
            $mT014 = mysql_fetch_array($sqlT014);

        } else if ($T001_T008_Id>0){

            $sqlT014 = mysql_query("SELECT T008_Id as T014_Id,
                                           D006_Flag_Venda_Compra_Outros,
                                           D006_Flag_Atualiza_Custo,
                                           T008_Valor_Base_ICMS_Substituicao as T014_Valor_Base_ICMS_Substituicao,
                                           T008_Valor_ICMS_Substituicao_Unitario as T014_Valor_ICMS_Substituicao_Unitario,
                                           T008_Valor_Base_ICMS / $T001_Quantidade as T014_Valor_Base_ICMS,
                                           T008_Valor_ICMS / $T001_Quantidade as T014_Valor_ICMS
                                      FROM T008
                                 LEFT JOIN D006 ON D006_Id=T008_D006_Id
                                     WHERE T008_Id = '{$T001_T008_Id}'");
            $mT014 = mysql_fetch_array($sqlT014);

            //$T001_T014_Id = $T001_T008_Id;

        }

        if( $T001_T014_Id > 0){
            $sqlT289 = mysql_query("SELECT T289_Id,
                                           T289_Quantidade_Saldo
                                      FROM T289
                                     WHERE T289_T014_Id = '{$T001_T014_Id}'");
            $mT289 = mysql_fetch_array($sqlT289);
        } else {

			// Processo estava errado, vincula o T008_Id no T289_T014_Id e as NFs de entrada de compra de importação ficavam errradas
            // Foi arrumado para vincular correto agora, verifica se já existe registro, se já existir mata a função porque já foi lançado anteriormente errado
            $verifica = mysql_query("SELECT T289_Id
                                       FROM T289
                                      WHERE T289_T014_Id = '{$T001_T008_Id}'");

            if(mysql_num_rows($verifica) > 0){
                return true;
            }

            $sqlT289 = mysql_query("SELECT T289_Id,
                                           T289_Quantidade_Saldo
                                      FROM T289
                                 LEFT JOIN T289A ON T289_Id = T289A_T289_Id
                                     WHERE T289A_T008_Id = '{$T001_T008_Id}'");
            $mT289 = mysql_fetch_array($sqlT289);

        }

        // Verifica se a CFOP atualiza custo e se é uma CFOP de compra
        if ($mT014['D006_Flag_Atualiza_Custo']=='S' && $mT014['D006_Flag_Venda_Compra_Outros']=='C'){
            // Caso seja Cancelada a nota, será zerado o estoque
            if ($T001_Flag_Operacao=='C') { 
                $T001_Quantidade      =0;
                $mT289['T289_Quantidade_Saldo']=0; 
            }

            if ($mT289['T289_Id']<=0) {
                if($T001_T014_Id > 0){
                    $valorBaseIcms = $mT014['T014_Valor_Base_ICMS_Substituicao']/$T001_Quantidade;
                    $sql = "INSERT INTO T289 (
                                        T289_D009_Id, 
                                        T289_T014_Id,
                                        T289_Quantidade,
                                        T289_Quantidade_Saldo,
                                        T289_Flag_Operacao,
                                        T289_Data_Lancamento,
                                        T289_Valor_Base_ICMS_Substituicao_Unitario,
                                        T289_Valor_ICMS_Substituicao_Unitario,
                                        T289_Base_ICMS_Substituto_Unitario,
                                        T289_Valor_ICMS_Substituto_Unitario
                                     ) VALUES (
                                        '{$T001_D009_Id}', 
                                        '{$T001_T014_Id}', 
                                        '{$T001_Quantidade}',
                                        '{$T001_Quantidade}',
                                        '{$T001_Flag_Operacao}',
                                        '{$T001_Data_Lancamento}',
                                        '{$valorBaseIcms}',
                                        '{$mT014['T014_Valor_ICMS_Substituicao_Unitario']}',
                                        '{$mT014['T014_Valor_Base_ICMS']}',
                                        '{$mT014['T014_Valor_ICMS']}')";
                    mysql_query($sql);
                    $cErro = mysql_error();

                } else{

                    $valorBaseIcms = $mT014['T014_Valor_Base_ICMS_Substituicao']/$T001_Quantidade;
                    $sql = "INSERT INTO T289 (
                                        T289_D009_Id, 
                                        T289_Quantidade,
                                        T289_Quantidade_Saldo,
                                        T289_Flag_Operacao,
                                        T289_Data_Lancamento,
                                        T289_Valor_Base_ICMS_Substituicao_Unitario,
                                        T289_Valor_ICMS_Substituicao_Unitario,
                                        T289_Base_ICMS_Substituto_Unitario,
                                        T289_Valor_ICMS_Substituto_Unitario
                                     ) VALUES (
                                        '{$T001_D009_Id}', 
                                        '{$T001_Quantidade}',
                                        '{$T001_Quantidade}',
                                        '{$T001_Flag_Operacao}',
                                        '{$T001_Data_Lancamento}',
                                        '{$valorBaseIcms}',
                                        '{$mT014['T014_Valor_ICMS_Substituicao_Unitario']}',
                                        '{$mT014['T014_Valor_Base_ICMS']}',
                                        '{$mT014['T014_Valor_ICMS']}')";
                    mysql_query($sql);
                    $cErro = mysql_error();
                    $T289_Id = $g['mysqlLastId'];

                    mysql_query("INSERT INTO T289A (T289A_T289_Id, T289A_T008_Id) VALUES ('{$T289_Id}', '{$T001_T008_Id}')");
                    $cErro = mysql_error();
                }

            } else {
                $sql = "UPDATE T289 
                           SET T289_D009_Id          = '{$T001_D009_Id}', 
                               T289_Quantidade       = '{$T001_Quantidade}',
                               T289_Quantidade_Saldo = '{$mT289['T289_Quantidade_Saldo']}',
                               T289_Flag_Operacao    = '{$T001_Flag_Operacao}',
                               T289_Data_Lancamento  = '{$T001_Data_Lancamento}'
                         WHERE T289_Id               = '{$mT289['T289_Id']}'";           
            }

        }  
    }

    /**
     * populaT290
     *
     * @param String $T001_D009_Id
     * @param String $T001_Quantidade   
     * @param String $T001_T008_Id
     * @param String $T001_Flag_Operacao
     * @param String $T001_Data_Lancamento
     * @return true
     */     
    function populaT290($T001_D009_Id, $T001_T008_Id, $T001_Quantidade, $T001_Flag_Operacao, $T001_Data_Lancamento, $quantidadeDevolucao = 0, $observacao = '')
    {

        $sqlT008 = mysql_query("SELECT T008_Id,
                                       T290_T289_Id,
                                       T290_T008_Id,
                                       D006_Flag_Venda_Compra_Outros,
                                       D006_Flag_Atualiza_Custo,
                                       T001_Flag_Operacao,
                                       T290_Id,
                                       T290_Quantidade,
                                       T289_Flag_Operacao,
                                       T289_Quantidade_Saldo,
                                       T290_Flag_Operacao,
                                       T008_Quantidade
                                  FROM T001
                             LEFT JOIN T008 ON T008_Id=T001_T008_Id
                             LEFT JOIN D006 ON D006_Id=T008_D006_Id
                             LEFT JOIN T290 ON T008_Id=T290_T008_Id
                             LEFT JOIN T289 ON T289_Id=T290_T289_Id
                                 WHERE T008_Id = '{$T001_T008_Id}'");
        $mT008 = mysql_fetch_array($sqlT008);

        $return = '';
        if (($mT008['D006_Flag_Venda_Compra_Outros'] != 'V') || ($T001_Flag_Operacao == 'C') || ($mT008['T289_Flag_Operacao'] == 'C')) {
            if (($mT008['T290_Id'] > 0) && ($mT008['T290_Flag_Operacao'] != 'C')) {

                // $quantidadeSaldo = $mT008['T289_Quantidade_Saldo'] + $mT008['T290_Quantidade'];

                $loopT290 = mysql_query("SELECT T290_Quantidade, 
                                                T290_T289_Id 
                                           FROM T290 
                                          WHERE T290_T008_Id='{$mT008['T290_T008_Id']}'"); 
                                          
               
                while ($mLoopT290 = mysql_fetch_array($loopT290)){
                      $updateT289 = mysql_query("UPDATE T289 SET T289_Quantidade_Saldo = T289_Quantidade_Saldo + IF({$quantidadeDevolucao} > 0, {$quantidadeDevolucao}, {$mLoopT290['T290_Quantidade']})
                                                           WHERE T289_Id               = '{$mLoopT290['T290_T289_Id']}'");
                }
                mysql_query("UPDATE T290 SET T290_Quantidade    = IF({$quantidadeDevolucao} > 0 , T290_Quantidade - {$quantidadeDevolucao}, 0),
                                             T290_Flag_Operacao = 'C'
                                       WHERE T290_T008_Id = '{$mT008['T290_T008_Id']}'");
                
                $cErro = mysql_error();
                if(!empty($cErro)){
                    $return.= "Erro: ".$cErro."\\nClausula: ".$updateT289; 
                }

                $T290A = mysql_query("SELECT T290A_Id
                                        FROM T290A
                                       WHERE T290A_T290_Id = '{$mT008['T290_Id']}'");

                $mT290A = mysql_fetch_array($T290A);
                if($mT290A['T290A_Id'] > 0){
                    mysqli_query("UPDATE T290A SET T290A_Observacao = '{$observacao}' WHERE T290A_Id = '{$mT290A['T290A_Id']}'");
                } else {
                    mysqli_query("INSERT INTO T290A (T290A_T290_Id, T290A_Observacao) VALUES('{$mT008['T290_Id']}', '{$observacao}')");
                }

            } elseif($quantidadeDevolucao > 0 && $mT008['T290_Flag_Operacao'] == 'C'){

                $loopT290 = mysql_query("SELECT T290_Quantidade, 
                                                T290_T289_Id 
                                           FROM T290 
                                          WHERE T290_T008_Id='{$mT008['T290_T008_Id']}'");                                           
               
                while ($mLoopT290 = mysql_fetch_array($loopT290)){
                      $updateT289 = mysql_query("UPDATE T289 SET T289_Quantidade_Saldo = T289_Quantidade_Saldo - {$quantidadeDevolucao}
                                                           WHERE T289_Id               = '{$mLoopT290['T290_T289_Id']}'");
                }

                mysql_query("UPDATE T290 SET T290_Quantidade    = '{$mT008['T008_Quantidade']}',
                                             T290_Flag_Operacao = 'S'
                                       WHERE T290_T008_Id = '{$mT008['T290_T008_Id']}'");

                $cErro = mysql_error();
                if(!empty($cErro)){
                    $return.= "Erro: ".$cErro."\\nClausula: ".$updateT289; 
                }

                $T290A = mysql_query("SELECT T290A_Id
                                        FROM T290A
                                       WHERE T290A_T290_Id = '{$mT008['T290_Id']}'");

                $mT290A = mysql_fetch_array($T290A);
                if($mT290A['T290A_Id'] > 0){
                    mysqli_query("UPDATE T290A SET T290A_Observacao = '' WHERE T290A_Id = '{$mT290A['T290A_Id']}'");
                }
            }

            return false;
       
        } else if ($mT008['T290_Id']>0) {
            //Já existe registro no T290 vinculado ao T008, finaliza
            return false;        
        }

        $sqlT289 = mysql_query("SELECT T289_Id,
                                       T289_Quantidade_Saldo,
                                       T289_D009_Id,
                                       T289_Quantidade,
                                       T289_Flag_Operacao,
                                       T289_Data_Lancamento,
                                       T289_T014_Id,
                                       T289_Valor_Base_ICMS_Substituicao_Unitario,
                                       T289_Valor_ICMS_Substituicao_Unitario,
                                       T289_Base_ICMS_Substituto_Unitario,
                                       T289_Valor_ICMS_Substituto_Unitario
                                  FROM T289
                             LEFT JOIN T289A ON T289A_T289_Id = T289_Id
                                 WHERE T289_D009_Id           = '{$T001_D009_Id}'
                                   AND T289_Quantidade_Saldo  > 0
                                   AND T289_Data_Lancamento   <= '{$T001_Data_Lancamento}'
                              ORDER BY T289_Data_Lancamento, T289_Id");
                              //ORDER BY T289_T014_Id, T289A_T008_Id

        if (mysql_num_rows($sqlT289)<=0){
            return true;
        }
        
        $Saldo = $T001_Quantidade;

        while (($mT289 = mysql_fetch_array($sqlT289)) && ($Saldo > 0)) {

            if ($mT289['T289_Quantidade_Saldo'] >= $Saldo){
                $Quantidade        = $Saldo;
            } else {
                $Quantidade        = $mT289['T289_Quantidade_Saldo'];
            }

            $Quantidade_Saldo  = $mT289['T289_Quantidade_Saldo'] - $Quantidade;
            $Saldo            -= $Quantidade;  

            $valorIcmsBase           = $mT289['T289_Valor_Base_ICMS_Substituicao_Unitario'] * $Quantidade;
            $valorIcmsST             = $mT289['T289_Valor_ICMS_Substituicao_Unitario'] * $Quantidade;
            $valorICMSSubstituto     = $mT289['T289_Valor_ICMS_Substituto_Unitario'] * $Quantidade;
            $valorBaseICMSSubstituto = $mT289['T289_Base_ICMS_Substituto_Unitario'] * $Quantidade;

            $sql = "INSERT INTO T290 (
                                T290_D009_Id, 
                                T290_T008_Id,
                                T290_Quantidade,
                                T290_T289_Id,
                                T290_Flag_Operacao,
                                T290_Data_Lancamento,
                                T290_Valor_Base_ICMS_Substituicao,
                                T290_Valor_ICMS_Substituicao,
                                T290_Base_ICMS_Substituto,
                                T290_Valor_ICMS_Substituto
                            ) VALUES (
                                '{$T001_D009_Id}', 
                                '{$T001_T008_Id}', 
                                '{$Quantidade}',
                                '{$mT289['T289_Id']}',
                                '{$T001_Flag_Operacao}',
                                '{$T001_Data_Lancamento}',
                                '{$valorIcmsBase}',
                                '{$valorIcmsST}',
                                '{$valorBaseICMSSubstituto}',
                                '{$valorICMSSubstituto}')";

            $sql1 = "UPDATE T289 
                        SET T289_Quantidade_Saldo = '{$Quantidade_Saldo}',
                            T289_Flag_Operacao    = '{$mT289['T289_Flag_Operacao']}'
                      WHERE T289_Id = '{$mT289['T289_Id']}'"; 
            
            $res   = mysql_query($sql);
            $res1  = mysql_query($sql1);

            $cErro = mysql_error();
            if(!empty($cErro)){
                $return.= "Erro: ".$cErro."\\nClausula: ".$res; 
            }

            $cErro1 = mysql_error();
            if(!empty($cErro1)){
                $return.= "Erro1: ".$cErro1."\\nClausula: ".$res1; 
            }
        }
    }

    /**
     * prepararImportarCSVFornecedorProduto
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
    */
     
    public function importarCSVFornecedorProduto($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
        
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols = array('Codigo_Produto','Codigo_Fornecedor');
        //posicao do array      <0>                   <1>         

        $linha = 1;        
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha

        while ($array_linha = fgets($ponteiro)) {
   
            $array_linha = explode(';',utf8_encode($array_linha));
            for ($count=0;$count<count($array_linha);$count++){
                if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {   
                    $array_linha[$count] = utf8_encode($array_linha[$count]); 
                    $array_linha[$count] = trim($array_linha[$count]);                     
                }
            }
            
            $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
           
            if ($linha==1){
                // Validar número de colunas e o se o nome das colunas bate com o padrão  
                $total_cols = count($array_cols);
                if ($total_cols!=count($array_cols)) {
                    $return .= "O número de colunas é diferente de {$total_cols} \\n";
                }
                $array_linha[0] = str_replace(array(chr(239),chr(197),chr(201)),'',$array_linha[0]);
                for ($count=0;$count<count($array_linha);$count++){
                    if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                        $return .="A coluna ".trim($array_linha[$count])." do arquivo não confere com a coluna ".$array_cols[$count]." esperada.\\n \\n";
                    }
                }                   
            } else {

                //Verifica se código do produto está cadastrado
                if(empty(trim($array_linha[0]))){
                    $return .=  $msgLinha . "O código do produto está em branco. \\n";
                }else{
                    // Validar se código está duplicado ou está branco   
                    $cClausula = "SELECT D001_Id from D001 WHERE D001_Codigo_Produto='".trim($array_linha[0])."'";
                    $cSQL_D001 = mysql_query($cClausula);
                    $mD001     = mysql_fetch_array($cSQL_D001);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D001) <= 0){
                        $return .= $msgLinha . "O código do produto ". $array_linha[0] ." não está cadastrado!\\n";

                    }else{
                        $D001_Id=$mD001['D001_Id'];
                    }
                }
                
                //Verifica se código do fornecedor está cadastrado                                                     
                if(empty(trim($array_linha[1]))){
                    $return .=  $msgLinha . "O código do fornecedor está em branco. \\n";
                }else{
                    // Validar se código está duplicado ou está branco   
                    $cClausula = "SELECT D024_Id,D024_Nome_Empresa from D024 WHERE D024_Id='".trim($array_linha[1])."'";
                    $cSQL_D024 = mysql_query($cClausula);
                    $mD024     = mysql_fetch_array($cSQL_D024);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_D024) <= 0){
                        $return .= $msgLinha . "O fornecedor código ". $array_linha[1] ." não existe!\\n";

                    }else{
                        $D024_Id=$mD024['D024_Id'];
                    }
                }

                //Verifica se o código do produto e fornecedor não estão em branco e verifica se já estão vinculados
/*              if(!(empty($D024_Id)) && !(empty($D001_Id))){
                    // Validar se código está duplicado ou está branco   
                    $cClausula = "SELECT T241_Id from T241 WHERE T241_D001_Id='{$D001_Id}' AND T241_D024_Id='{$D024_Id}'";
                    $cSQL_T241 = mysql_query($cClausula);
                    $mT241     = mysql_fetch_array($cSQL_T241);
                    $cErro     = mysql_error();
                    if(!empty($cErro)){
                        $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                    }     
                    if(mysql_num_rows($cSQL_T241) >= 1){
                        $return .= $msgLinha . "O código do produto ". $array_linha[0] ." já está cadastrado ao fornecedor  ".$mD024['D024_Id']." - ".$mD024['D024_Nome_Empresa']."!\\n";
                    }
                }*/
            }
            $linha++;
        }

        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro      = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $linha=1;
            //PERCORRER AS LINHAS NOVAMENTE 
            while (($array_linha = fgetcsv($ponteiro,1000,';')) !== FALSE) {
                                    
                if($linha==1){
                    $linha++;
                    continue;
                } 
             
                //REALIZA OS SELECT´S NO BANCO PARA ALIMENTAR O INSERT
                //CADASTRO DE CLIENTE
                $cClausula = "SELECT D024_Id from D024 WHERE D024_Id='".trim($array_linha[1])."'";
                $cSQL_D024 = mysql_query($cClausula);
                $mD024     = mysql_fetch_array($cSQL_D024);

                //CADASTRO DE PRODUTO
                $cClausula = "SELECT D001_Id from D001 WHERE D001_Codigo_Produto='".trim($array_linha[0])."'";
                $cSQL_D001 = mysql_query($cClausula);
                $mD001     = mysql_fetch_array($cSQL_D001);

                //VALIDAÇÃO FEITA PARA NÃO DAR ERRO EM CASO DE LINHAS DUPLICADAS NO CSV
                $cClausula = "SELECT T241_Id from T241 WHERE T241_D001_Id='{$mD001['D001_Id']}' AND T241_D024_Id='{$mD024['D024_Id']}'";
                $cSQL_T241 = mysql_query($cClausula);
                $numero = mysql_num_rows($cSQL_T241);

                if($numero <= 0){
                    // INSERE NA TABELA T241
                    $cClausula = <<<EOT
                        INSERT INTO T241(
                            T241_D001_Id,
                            T241_D024_Id
                        )VALUES(
                            '{$mD001['D001_Id']}',
                            '{$mD024['D024_Id']}'
                        )
EOT;
                    $cSql  = mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro insert: " . $cErro . "\\n" . $cClausula;
                    } else {
                        $linha++;
                    }
                } else{
                    $duplicados++;
                    continue;
                }
            }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        return "Importação concluída com sucesso!!\\n************************************************** \\n\\n Foram importados ".($linha-2)." fornecedores!!\\n"." Foram ignorados " .($duplicados)." fornecedores duplicados.";

        } else {
            return "Importação cancelada, verifique os erros abaixo:\\n******************************************************** \\n\\n".$return;
        }        
    }

     /**
     * cad002FotoProduto
     *
     * @param intiger $D001_Id
     * @param string $Tamanho
     * @return String/html
     */
    function cad002FotoProduto($D001_Id, $tamanhoPx=30){
        global $g, $confUsuario;

        $T172=mysql_query("SELECT * FROM T172 WHERE T172_D001_Id='{$D001_Id}' ORDER BY T172_Nome_Arquivo DESC LIMIT 1");

        if (mysql_num_rows($T172)>0) {
            $mT172=mysql_fetch_array($T172);
    
            $idFoto = $mT172['T172_Id'];
            $idProduto = $mT172['T172_D001_Id'];
            $pathinfo = pathinfo($mT172['T172_Nome_Arquivo']);
            $cArquivo = "{$g['pathRaiz']}dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            
            $dir = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            $dirMini = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$idProduto}/fotos/{$idFoto}.{$pathinfo['extension']}";
            if (!file_exists($dir)){
                $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
                $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
            }
        } else {
            $T144 = mysql_query("SELECT T144_Id, T144_Data_Cadastro, D001_Descricao_Produto, T144_Url, T144_Descricao, T144_Ultimo_Acesso, T144_Versao_Cache 
                                   FROM T144 
                              LEFT JOIN D001 ON D001_Id=T144_D001_Id 
                                  WHERE T144_D001_Id = '{$D001_Id}'");

            $mT144 = mysql_fetch_array($T144);
    
            $idProduto = $mT144['T144_Id'];
            $pathinfo = 'img';
            $dir     = $mT144['T144_Url'];
            $dirFull = $mT144['T144_Url'];
            $dirMini = $mT144['T144_Url'];
        }

/*      Comentado uso da imagem na tabela D003

        $D003=mysql_query("SELECT D003_Imagem FROM D001 left join D003 on D003_Id=D001_D003_Id WHERE D001_Id='{$D001_Id}'");
        $mD003=mysql_fetch_array($D003);
        
        $idProduto = $mD003['D003_Imagem'];
        $pathinfo = pathinfo($mD003['D003_Imagem']);
        $dir     = $g['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        $dirFull = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        $dirMini = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/linhas/{$mD003['D003_Imagem']}";
        if (!file_exists($dir)){
            $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
            $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
        }
*/

	    if (!empty($idProduto) && $pathinfo['extension']=='pdf') {
            $dirMini = "/hardness3/static/img/format-pdf-file.png";
        }
    
        if (empty($idProduto)) {
            $dirMini = "/hardness3/static/img/indisponivel100x100.jpg";
            $dirFull = "/hardness3/static/img/indisponivel100x100.jpg";
        }

        $foto = "<img src='{$dirMini}' style='max-width:{$tamanhoPx}px;max-height:{$tamanhoPx}px' onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imagem Original', '/crm/crm001/outro/crm001ImagemOriginal/', '&urlImagem=' + encodeURIComponent('{$dirFull}') + '&D001_Id=' + encodeURIComponent('{$D001_Id}'), [1100,700])\"><br>";

        return $foto;
    }

     /**
     * cad002AtualizaEstoque
     *
     * @param array $mD001
     * @param array $mD001Anterior
     * @return String
     * 
     */
    public function cad002AtualizaEstoque($mD001, $mD001Anterior){
        log("Entrou");
        $this->D001_reprocessa_historico(false, $mD001['D009_Id'], 100);
        return true;

    }


    /**
     * conversaoUnidadeProduto
     * @param intiger $T004_Id
     * @param intiger $T006_Id
     * @param intiger $T008_Id
     * @param bool $percentualDesconto
     * @return true
     * 
     */
    public function conversaoUnidadeProduto($T004_Id = false, $T006_Id = false, $T008_Id = false, $percentualDesconto = false){
        global $g;
        
        if($T004_Id > 0){

            $T004 = mysql_query("SELECT T004A_D184_Id, 
                                        D001_Id,
                                        T004A_Id,
                                        T004_Quantidade, 
                                        T004A_Quantidade_Comercial,
                                        T004_Valor_Preco_Unitario, 
                                        T004A_Preco_Unitario_Comercial,  
                                        D037_Id, 
                                        D184_Tipo_Operacao, 
                                        D184_Quantidade_Conversao,
                                        T003_Casas_Decimais_Preco 
                                   FROM T004 
                              left join T004A on T004_Id = T004A_T004_Id 
                              left join D009 on D009_Id = T004_D009_Id 
                              left join D049 on D049_Id = D009_D049_Id 
                              left join D001 on D001_Id = D049_D001_Id 
                              left join D184 on D184_Id = T004A_D184_Id 
                              left join D037 on D037_Id = D184_D037_Id 
                              left join T003 on T003_Id = T004_T003_Id
                                  WHERE T004_Id = '{$T004_Id}'");

            $mT004 = mysql_fetch_array($T004);
            
            $quantidadeComercial    = $mT004['T004A_Quantidade_Comercial'];
            $quantidadeConversao    = $mT004['D184_Quantidade_Conversao'];
            $valorUnitario          = $mT004['T004_Valor_Preco_Unitario'];
            $valorUnitarioComercial = $mT004['T004A_Preco_Unitario_Comercial'];
            $tipoOperacao           = $mT004['D184_Tipo_Operacao'];
            $unidadeConversao       = $mT004['D037_Id'];
            $casasDecimaisPreco     = $mT004['T003_Casas_Decimais_Preco'];

        } elseif($T006_Id > 0){

            $T006 = mysql_query("SELECT T006A_D184_Id, 
                                        D001_Id,
                                        T006A_Id,
                                        T006_Quantidade, 
                                        T006A_Quantidade_Comercial,
                                        T006_Valor_Preco_Sem_Desconto_Unitario, 
                                        T006A_Preco_Unitario_Comercial,  
                                        D037_Id, 
                                        D184_Tipo_Operacao, 
                                        D184_Quantidade_Conversao,
                                        T005_Casas_Decimais_Preco 
                                   FROM T006 
                              left join T006A on T006_Id = T006A_T006_Id 
                              left join D009 on D009_Id = T006_D009_Id 
                              left join D049 on D049_Id = D009_D049_Id 
                              left join D001 on D001_Id = D049_D001_Id 
                              left join D184 on D184_Id = T006A_D184_Id 
                              left join D037 on D037_Id = D184_D037_Id 
                              left join T005 on T005_Id = T006_T005_Id
                                  WHERE T006_Id = '{$T006_Id}'");

            $mT006 = mysql_fetch_array($T006);
            
            $quantidadeComercial    = $mT006['T006A_Quantidade_Comercial'];
            $quantidadeConversao    = $mT006['D184_Quantidade_Conversao'];
            $valorUnitario          = $mT006['T006_Valor_Preco_Sem_Desconto_Unitario'];
            $valorUnitarioComercial = $mT006['T006A_Preco_Unitario_Comercial'];
            $tipoOperacao           = $mT006['D184_Tipo_Operacao'];
            $unidadeConversao       = $mT006['D037_Id'];
            $casasDecimaisPreco     = $mT006['T005_Casas_Decimais_Preco'];

        }elseif($T008_Id > 0){
            
            $T008 = mysql_query("SELECT T008A_D184_Id, 
                                        D001_Id,
                                        T008A_Id,
                                        T008_Quantidade, 
                                        T008A_Quantidade_Comercial,
                                        T008_Valor_Preco_Sem_Desconto_Unitario, 
                                        T008A_Preco_Unitario_Comercial,  
                                        D037_Id, 
                                        D184_Tipo_Operacao, 
                                        D184_Quantidade_Conversao,
                                        T007_Casas_Decimais_Preco 
                                   FROM T008 
                              left join T008A on T008_Id = T008A_T008_Id 
                              left join D009 on D009_Id = T008_D009_Id 
                              left join D049 on D049_Id = D009_D049_Id 
                              left join D001 on D001_Id = D049_D001_Id 
                              left join D184 on D184_Id = T008A_D184_Id 
                              left join D037 on D037_Id = D184_D037_Id 
                              left join T007 on T007_Id = T008_T007_Id
                                  WHERE T008_Id = '{$T008_Id}'");

            $mT008 = mysql_fetch_array($T008);
            
            $quantidadeComercial    = $mT008['T008A_Quantidade_Comercial'];
            $quantidadeConversao    = $mT008['D184_Quantidade_Conversao'];
            $valorUnitario          = $mT008['T008_Valor_Preco_Sem_Desconto_Unitario'];
            $valorUnitarioComercial = $mT008['T008A_Preco_Unitario_Comercial'];
            $tipoOperacao           = $mT008['D184_Tipo_Operacao'];
            $unidadeConversao       = $mT008['D037_Id'];
            $casasDecimaisPreco     = $mT008['T007_Casas_Decimais_Preco'];
        }

        if(empty($unidadeConversao) || $unidadeConversao <= 0){
            // Se não tem unidade de convesão selecionada, atualuza os valores da tabela principal com os dados digitados
            if($T004_Id > 0){
                
                if(!$percentualDesconto){
                     mysql_query("UPDATE T004 
                                     SET T004_Valor_Preco_Unitario    = '{$valorUnitarioComercial}',
                                         T004_Quantidade              = '{$quantidadeComercial}',
                                         T004_Percentual_Desconto     = if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
                                         T004_Valor_Desconto_Unitario = if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*(T004_Valor_Preco_Unitario/100)),0)
                                   WHERE T004_Id = '{$T004_Id}'");
                } else {
                     // Se o campo alterado for o percentual desconto, atualiza o comercial porque já foi calculado o valor da tabela principal
                     mysqli_query("UPDATE T004A 
                                      SET T004A_Preco_Unitario_Comercial = '{$valorUnitario}'
                                    WHERE T004A_T004_Id = '{$T004_Id}'");
                }
  

            } elseif($T006_Id > 0){

                if(!$percentualDesconto){
                    mysql_query("UPDATE T006 
                                    SET T006_Valor_Preco_Sem_Desconto_Unitario = '{$valorUnitarioComercial}',
                                        T006_Quantidade                        = '{$quantidadeComercial}',
                                        T006_Percentual_Desconto               = if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Sem_Desconto_Unitario / T006_Valor_Preco_Original))*100,0),
                                        T006_Valor_Desconto_Unitario           = if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario /100)),0)
                                WHERE T006_Id = '{$T006_Id}'");
                } else {
                    // Se o campo alterado for o percentual desconto, atualiza o comercial porque já foi calculado o valor da tabela principal
                    mysqli_query("UPDATE T006A 
                                     SET T006A_Preco_Unitario_Comercial = '{$valorUnitario}'
                                   WHERE T006A_T006_Id = '{$T006_Id}'");
                }

            } elseif($T008_Id > 0){
               
                if(!$percentualDesconto){
                    mysql_query("UPDATE T008
                                    SET T008_Valor_Preco_Sem_Desconto_Unitario = '{$valorUnitarioComercial}',
                                        T008_Quantidade                        = '{$quantidadeComercial}',
                                        T008_Percentual_Desconto               = if(T008_Valor_Preco_Original>0,(1-(T008_Valor_Preco_Sem_Desconto_Unitario / T008_Valor_Preco_Original))*100,0),
                                        T008_Valor_Desconto_Unitario           = if(T008_Valor_Preco_Original>0,(T008_Valor_Preco_Original*(T008_Valor_Preco_Sem_Desconto_Unitario /100)),0)
                                  WHERE T008_Id = '{$T008_Id}'");
                } else {
                    // Se o campo alterado for o percentual desconto, atualiza o comercial porque já foi calculado o valor da tabela principal
                    mysqli_query("UPDATE T008A 
                                     SET T008A_Preco_Unitario_Comercial = '{$valorUnitario}'
                                   WHERE T008A_T008_Id = '{$T008_Id}'");
                }

            }

        } else {
            
            $quantidadeReal    = 0;
            $novoValorUnitario = 0;
            
            //Calula a quantidade do produto
            $operacao = [
                'D' => $quantidadeComercial / $quantidadeConversao,
                'M' => $quantidadeComercial * $quantidadeConversao,            
            ];      
    
            $quantidadeReal = $operacao[$tipoOperacao];           
           
            // Calcula o preço do produto
            if(!$percentualDesconto && $valorUnitarioComercial != $valorUnitario){
                // O calculo do preço tem que ser invertido a operação quando valor editado é o comercial
                // se a operação é divisão tem que multiplicar, se é multiplicação tem que dividir
                $operacaoPreco = [              
                    'D' => $valorUnitarioComercial * $quantidadeConversao,
                    'M' => $valorUnitarioComercial / $quantidadeConversao,   
                ];
            } else {
                // Se o campo alterado for o percentual desconto, atualiza o comercial porque já foi calculado o valor da tabela principal
                $operacaoPreco = [              
                    'D' => $valorUnitario / $quantidadeConversao,
                    'M' => $valorUnitario * $quantidadeConversao,   
                ];
            }   

            $novoValorUnitario = $operacaoPreco[$tipoOperacao];   
                      
            if($T004_Id > 0){

                if(!$percentualDesconto && $valorUnitarioComercial != $valorUnitario){
                    mysql_query("UPDATE T004  
                                    SET T004_Quantidade              = '{$quantidadeReal}',
                                        T004_Valor_Preco_Unitario    = round('{$novoValorUnitario}', '{$casasDecimaisPreco}'),
                                        T004_Percentual_Desconto     = if(T004_Valor_Preco_Original>0,(1-(T004_Valor_Preco_Unitario/T004_Valor_Preco_Original))*100,0),
                                        T004_Valor_Desconto_Unitario = if(T004_Valor_Preco_Original>0,(T004_Valor_Preco_Original*(T004_Valor_Preco_Unitario/100)),0)
                                  WHERE T004_Id = '{$T004_Id}'");
                } else {
                    mysqli_query("UPDATE T004A 
                               LEFT JOIN T004 ON T004_Id = T004A_T004_Id
                                     SET T004A_Preco_Unitario_Comercial = '{$novoValorUnitario}',
                                         T004_Quantidade                = '{$quantidadeReal}'
                                   WHERE T004A_T004_Id = '{$T004_Id}'");
                }


            } elseif($T006_Id > 0){
                
                if(!$percentualDesconto && $valorUnitarioComercial != $valorUnitario){

                    mysqli_query("UPDATE T006  
                                     SET T006_Quantidade                        = '{$quantidadeReal}',
                                         T006_Valor_Preco_Sem_Desconto_Unitario = round('{$novoValorUnitario}', '{$casasDecimaisPreco}'),
                                         T006_Percentual_Desconto               = if(T006_Valor_Preco_Original>0,(1-(T006_Valor_Preco_Sem_Desconto_Unitario/T006_Valor_Preco_Original))*100,0),
                                         T006_Valor_Desconto_Unitario           = if(T006_Valor_Preco_Original>0,(T006_Valor_Preco_Original*(T006_Valor_Preco_Sem_Desconto_Unitario/100)),0)
                                   WHERE T006_Id = '{$T006_Id}'");
                } else {

                    mysqli_query("UPDATE T006A  
                               LEFT JOIN T006 ON T006_Id = T006A_T006_Id
                                     SET T006A_Preco_Unitario_Comercial = '{$novoValorUnitario}',
                                         T006_Quantidade                = '{$quantidadeReal}'
                                   WHERE T006A_T006_Id = '{$T006_Id}'");

                }

            } elseif($T008_Id > 0){
                
                if(!$percentualDesconto && $valorUnitarioComercial != $valorUnitario){

                    mysqli_query("UPDATE T008  
                                     SET T008_Quantidade                        = '{$quantidadeReal}',
                                         T008_Valor_Preco_Sem_Desconto_Unitario = round('{$novoValorUnitario}','{$casasDecimaisPreco}'),
                                         T008_Percentual_Desconto               = if(T008_Valor_Preco_Original>0,(1-(T008_Valor_Preco_Sem_Desconto_Unitario/T008_Valor_Preco_Original))*100,0),
                                         T008_Valor_Desconto_Unitario           = if(T008_Valor_Preco_Original>0,(T008_Valor_Preco_Original*(T008_Valor_Preco_Sem_Desconto_Unitario/100)),0)
                                   WHERE T008_Id = '{$T008_Id}'");
                } else {
                    
                    mysqli_query("UPDATE T008A  
                               LEFT JOIN T008 ON T008_Id = T008A_T008_Id
                                     SET T008A_Preco_Unitario_Comercial = '{$novoValorUnitario}',
                                         T008_Quantidade                = '{$quantidadeReal}'
                                   WHERE T008A_T008_Id = '{$T008_Id}'");
                }            
            }
        }

        return true;
    }


    /**
     * retornaDestinoProdutos
     * @return array
     * 
     */
    // Recebe consumidor no orçamento e a operacao em todas as chamadas
    // Operacao é V de venda e C de compra, pode ser personalizado a função para retornar os destinos conforme a operação
    public function retornaDestinoProdutos($consumidor = false, $operacao = false) {
        $destinos =  array(
                        array('title' => '...', 'value' => "", 'selected' => (isset($consumidor) && $consumidor) ? false : true),
                        array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
                        array('title' => '2 - REVENDA', 'value' => "2"),
                        array('title' => '3 - CONSUMO', 'value' => "3", 'selected' => (isset($consumidor) && $consumidor) ? true : false),
                        array('title' => '4 - ESPECIAL', 'value' => "4"),
                        array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
                        array('title' => '6 - TROCA', 'value' => "6"),
                        array('title' => '7 - DEMONSTRAÇÃO', 'value' => "7"),
                        array('title' => '8 - CONSIGNAÇÃO', 'value' => "8"),
                        array('title' => '9 - VENDA FUTURA', 'value' => "9"),
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
                        array('title' => '21 - PRÓ-EMPREGO CONSUMO', 'value' => "21"),
                        array('title' => '22 - PRÓ-EMPREGO SEM DESTAQUE', 'value' => "22"),
                        array('title' => '23 - CONTA E CORDEM (TRANSFORMAÇÃO)', 'value' => "23"),
                        array('title' => '24 - SUCATA', 'value' => "24"),
                        array('title' => '25 - DRAWBACK', 'value' => "25"),
                        array('title' => '26 - REIDI', 'value' => "26"),
                        array('title' => '27 - MATERIAL PUBLICITARIO', 'value' => "27"),
                    );
            
        return $destinos;
    }

    /**
     * triggerAposInsercaoT001
     * @return boolean
     * 
     */
    // Funcao personalizada por cliente
    public function triggerAposInsercaoT001($dados = array()) {

        // Parametros que estao sendo recebidos
        // $dados['T001_Id']
        // $dados['T001_Quantidade']
        // $dados['T066_Quantidade_Estoque']
        // $dados['T001_Flag_Operacao']

        return true;
    }

    public function cad002AtualizarAtributosIntegracaoTray($D001_Id) {

        log("UPDATE D186 SET D186_Integrado_Tray = 'N' WHERE D186_D001_Id = {$D001_Id} ");
        mysql_query("UPDATE D186 SET D186_Integrado_Tray = 'N' WHERE D186_D001_Id = {$D001_Id} ");
    }

    public function gatilhoAposDefinirPrecoVenda(&$preco, &$demonstrativo, &$diferenca, $D009_Id) {
        global $g;
        
        return true;
    }

    public function retornaArrayTabelasGeral()
    {
        /* Essa configuração só aceita campos das tabelas D069 e D069A, se quiser adicionar outras tabelas favor atualizar cad/cad002/form_func-ajax/tabelaGeral.
        Para se referir a um campo JSON utilizar o seguinte formato <nomeCampoJson>-><nomeDoCampoDentroJson>
        Exemplo: D069A_JSON->Percentual_Desconto */

        $camposObrigatorios = array(
            array("nomeCampo" => "D069_Codigo_Produto", "obrigatorio" => true, "aviso" => "Esse campo só pode ser número"),
            array("nomeCampo" => "D069_Percentual_Desconto", "obrigatorio" => false),
			array("nomeCampo" => "D069_Flag_Origem_Produto", "obrigatorio" => false),
			array("nomeCampo" => "D069_Quantidade_Estoque", "obrigatorio" => false),
			array("nomeCampo" => "D069_Unidade", "obrigatorio" => false, "aviso" => "Exemplo: UN/PC/KG/ML/L"),
			array("nomeCampo" => "D069_Valor_Preco_Original", "obrigatorio" => false),
			array("nomeCampo" => "D069_Aliquota_IPI", "obrigatorio" => false),
			array("nomeCampo" => "D069_Aliquota_ICMS", "obrigatorio" => false),
			array("nomeCampo" => "D069_Percentual_Reducao_ICMS", "obrigatorio" => false),
			array("nomeCampo" => "D069_Percentual_ST", "obrigatorio" => false),
			array("nomeCampo" => "D069_Aliquota_PIS", "obrigatorio" => false),
			array("nomeCampo" => "D069_Aliquota_COFINS", "obrigatorio" => false),
			array("nomeCampo" => "D069_Valor_Frete", "obrigatorio" => false),
			array("nomeCampo" => "D069_Valor_Custo_Unitario", "obrigatorio" => false),
			array("nomeCampo" => "D069_Percentual_Frete","obrigatorio"=>false),
			array("nomeCampo" => "D069_Percentual_Financeiro","obrigatorio"=>false),
			array("nomeCampo" => "D069_ICMS_Inter_Estadual","obrigatorio"=>false),
			array("nomeCampo" => "D069_ST_ICMS_Interno","obrigatorio"=>false),
			array("nomeCampo" => "D069_Peso_Unitario","obrigatorio"=>false),
		);

        return $camposObrigatorios;
    }

    public function cad002AtualizaTabelaFornecedorGeral($cache, $cache2){
        global $g;
        $CAD002 = new CAD002();
        
        $D069_Id = $cache['D069_Id'];

        $opcoes = array(
            "campoNCM" => false,
            "campoOrigemProduto" => false,
            "campoIPI" => false,
            "campoICMS" => false,
            "campoICMSST" => false,
            "campoCFOP" => false,
            "campoNossoCodigo" => false,
        );
        $retorno = $CAD002->atualizarAliquotasProdutos($D069_Id, $opcoes);
        if($retorno !== true){
            log('Erro na função cad002AtualizaTabelaFornecedorGeral(atualizarAliquotasProdutos): ' . $retorno);
            return $retorno;
        }

        // Função trigger para personalização desse botão
        $retorno = $CAD002->Cad002PersonalizacaoTriggerAtualizarTabelaGeralFornecedor($cache);
        if($retorno !== true){
            log('Erro na função cad002AtualizaTabelaFornecedorGeral(Cad002PersonalizacaoTriggerAtualizarTabelaGeralFornecedor): ' . $retorno);
            return $retorno;
        }

        mysqli_query("call D069_Gravar_Totalizacao('{$D069_Id}', '{$g['usuarioAtual']}')");

        if($cache['D069_Codigo_Substituto']!= ''){
            $CAD002->definirCodigoFornecedor($cache['D069_D009_Id'], $cache['D070_D024_Id'], $cache['D069_Codigo_Substituto'], '', false, true);
        }

        $retorno = $CAD002->atualizarCustoProduto($cache['D069_D009_Id']);
        if($retorno !== true){
            log('Erro na função cad002AtualizaTabelaFornecedorGeral(atualizarCustoProduto): ' . $retorno);
            return $retorno;
        }

        return true;
    }

    public function Cad002PersonalizacaoTriggerAtualizarTabelaGeralFornecedor($cache){
        return true;
    }

    public function Cad002AtualizarTabelaGeralFornecedorBotoesPreSelect(){
        /* Exemplo para criar um botão de pre-seleção
         array("nome" => "Seleciona XYZ", "tabelas" => "21,22,24")
         As tabelas devem ser colocadas separadas por ,*/

        $botoesPreSelect = array(
            //array("nome" => "Seleciona XYZ", "tabelas" => "21,22,24")
        );

        return $botoesPreSelect;
    }

    /// GUIMEPA 

    public function Cad002PersonalizacaoTriggerInserirAposD009 ($D001) {
        return true;
    }

}

$CAD002 = new CAD002();

