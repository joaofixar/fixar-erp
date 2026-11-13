<?php
namespace hardness;
definirClasse('EST009');
class EST009_
{
	/**
     * updateCheckboxGerarCotar
     *
     * @param int $D009_Id
     * @param string $selected
     */
    public function updateCheckboxGerarCotar($D009_Id, $selected)
    {
        global $g;
        
        $D009_Id  = mysql_real_escape_string($D009_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update D009 set D009_Flag_Cotar='$selected', D009_C007_Id_Comprar = '{$g['usuarioAtual']}' where D009_Id='$D009_Id'");
        return true;
    }
    
    /**
     * updateCheckboxGerarComprar
     *
     * @param int $D009_Id
     * @param string $selected
     */
    public function updateCheckboxGerarComprar($D009_Id, $selected)
    {
        global $g;
        
        $D009_Id  = mysql_real_escape_string($D009_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update D009 set D009_Flag_Comprar='$selected' where D009_Id='$D009_Id'");
        return true;
    }

     /**
     * updateCheckboxGerarNaoComprar
     *
     * @param int $D049_Id
     * @param string $selected
     */
    public function updateCheckboxGerarNaoComprar($D049_Id, $selected)
    {
        global $g;
        
        $D009_Id  = mysql_real_escape_string($D049_Id);
        $selected = mysql_real_escape_string($selected);
        mysql_query("update D049 set D049_Flag_Nao_Comprar='$selected' where D049_Id='$D049_Id'");
        return true;
    }


    public function est009RecuperarEstoqueLoja($extra)
    {
        global $g;

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $mensagem = "";
        $itensSemEstoqueLoja = "";
        $itens = "";
        $itensSemLocacaoLoja = "";
        $retorno = array(); 

        $sql = "SELECT T006_Id,
                       T006_D024_Id,
                       (T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC) as Falta,
                       T006_Codigo_Produto,
                       D009_Quantidade_Estoque,
                       T006_Id,
                       D009_Quantidade_Estoque_Loja,
                       T006_D009_Id,
                       T006_T066_Id,
                       T006_C004_Id,
                       T005_Id,
                       T006_Valor_Preco_Unitario,
                       T006_Valor_Custo_Unitario
                  FROM T006
             LEFT JOIN T006A ON T006A_T006_Id = T006_Id
             LEFT JOIN D009 on D009_Id=T006_D009_Id
             LEFT JOIN D049 on D049_Id=D009_D049_Id
             LEFT JOIN D001 on D001_Id=D049_D001_Id
             LEFT JOIN D082 on D082_Id=D049_D082_Id
             LEFT JOIN D037 on D037_Id=T006_D037_Id
             LEFT JOIN D005 on D005_Id=T006_D005_Id
             LEFT JOIN D006 on D006_Id=T006_D006_Id
             LEFT JOIN T005 ON T005_Id=T006_T005_Id
             LEFT JOIN D024 ON T005_D024_Id=D024_Id
             LEFT JOIN T003 ON T003_Id=T005_T003_Id
             LEFT JOIN T004 ON T004_Id=T006_T004_Id
             LEFT JOIN T219 ON T006_Id=T219_T006_Id
             LEFT JOIN T218 ON T218_Id=T219_T218_Id
             LEFT JOIN T224 ON T218_Id=T224_T218_Id
             LEFT JOIN T225 ON T225_Id=T006_T225_Id
                 WHERE {$extra}";
        
        $select = mysql_query($sql);
        $erro = mysql_error();

        if($erro){
            $retorno[0]=false;
            $retorno[1]=$erro;
            return $retorno;
        }

        $qtde = mysql_num_rows($select);
        if($qtde <= 0){
            $retorno[0] = false;
            $retorno[1] = "Nenhum produto foi selecionado";
            return $retorno;
        } else {

            while($row = mysql_fetch_array($select)){
            
                $query = mysql_query("SELECT T066_Id,D004_Id FROM T066 LEFT JOIN D004 ON T066_D004_Id=D004_Id WHERE D004_Local='LOJA' AND T066_D009_Id='{$row['T006_D009_Id']}'");
                $T066=mysql_fetch_array($query);

                //Verifica se o D009 do T006 tem um T066 LOJA vinculado e 
                //Verifica se o Quantidade estoque em loja é o suficiente para suprir o que está em falta
                if(empty($T066['T066_Id'])){

                    if(empty($itensSemLocacaoLoja)){
                        $itensSemLocacaoLoja.=$row['T006_Codigo_Produto'];
                    }else{
                        $itensSemLocacaoLoja.=",".$row['T006_Codigo_Produto'];
                    }
                }else if($row['D009_Quantidade_Estoque_Loja']<$row['Falta']){
                    if(empty($itensSemEstoqueLoja)){
                        $itensSemEstoqueLoja.=$row['T006_Codigo_Produto'];
                    }else{
                        $itensSemEstoqueLoja.=",".$row['T006_Codigo_Produto'];
                    }
                }
            }

            //verifica se existem itens sem estoque o suficiente e sem locação loja e mostra a mensagem na tela
            if(!empty($itensSemLocacaoLoja)){
                $mensagem.="Não há locação LOJA vinculada nos itens:<br>".$itensSemLocacaoLoja."<br><br>";
            }

            if(!empty($itensSemEstoqueLoja)){
                $mensagem.="Não há quantidade suficiente no estoque loja para suprir o que está em falta nos itens:<br>".$itensSemEstoqueLoja."<br><br>";    
            }

            //aborta o processo e retorna a mensagem com os erros
            if(!empty($mensagem)){
                $mensagem.="Processo abortado.";
                $retorno[0] = false;
                $retorno[1] = $mensagem;
            }else{
                mysql_query("START TRANSACTION");

                $select = mysql_query($sql);
                $erro = mysql_error();

                if($erro){
                    $retorno[0]=false;
                    $retorno[1]=$erro;
                    return $retorno;
                }

                //processo não deu nenhuma mensagem de erro pode dar continuação
                while($row = mysql_fetch_array($select)){
                    
                    $query = mysql_query("SELECT T066_Id,D004_Id FROM T066 LEFT JOIN D004 ON T066_D004_Id=D004_Id WHERE D004_Local='LOJA' AND T066_D009_Id='{$row['T006_D009_Id']}'");
                    $T066=mysql_fetch_array($query);

                    $Descricao_Operacao = "Transferência loja";
                    
                    //TRANSAÇÃO DE SAÍDA DO VALOR QUE FALTA DO LOJA PARA O T006_T066_Id
                    $cClausula = <<<EOT
                        insert into T001 ( 
                            T001_D024_Id_Cliente, 
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
                            T001_D004_Id,
                            T001_T006_Id 
                        ) values ( 
                            '{$row['T006_D024_Id']}', 
                            '{$row['T006_D024_Id']}', 
                            '{$row['T006_D009_Id']}', 
                            '{$T066['T066_Id']}',
                            '{$row['T006_C004_Id']}', 
                            '{$row['T006_Codigo_Produto']}', 
                            current_date(), 
                            '{$Descricao_Operacao}', 
                            '{$row['Falta']}', 
                            '', 
                            'S', 
                            '{$row['T006_Valor_Preco_Unitario']}', 
                            '', 
                            '{$row['T006_Valor_Custo_Unitario']}', 
                            'N', 
                            0, 
                            '{$T066['D004_Id']}',
                            '{$row['T006_Id']}' 
                        )
EOT;
                    mysql_query($cClausula);
                    $T001_Id = $g['mysqlLastId'];
                    $erroSaida = mysql_error();

                    if(!empty($erroSaida)){
                        mysql_query("ROLLBACK");
                        $retorno[0]=false;
                        $retorno[1]="<br>ERRO(S):".$erroSaida."<br><br>";
                        return $retorno;
                    } 

                    $cClausula = <<<EOT
                        insert into T186 ( 
                            T186_T066_Id, 
                            T186_T001_Id, 
                            T186_Quantidade 
                        ) values ( 
                            '{$T066['T066_Id']}', 
                            '{$T001_Id}', 
                            '{$row['Falta']}'
                        )
EOT;
                    mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        $retorno[0]=false;
                        $retorno[1]="ERRO(T186 S): " . $cErro."<br><br>";
                        return $retorno;
                    }

                    //TRANSIÇÃO DE ENTRADA DO VALOR DO ESTOQUE DA LOJA PARA A T006_T066_ID
                    $tmpD004_Id = getD004($row['T006_T066_Id']);                    
                    $cClausula = <<<EOT
                        insert into T001 ( 
                            T001_D024_Id_Cliente, 
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
                            T001_D004_Id,
                            T001_T006_Id 
                        ) values ( 
                            '{$row['T006_D024_Id']}', 
                            '{$row['T006_D024_Id']}', 
                            '{$row['T006_D009_Id']}', 
                            '{$row['T006_T066_Id']}',
                            '{$row['T006_C004_Id']}', 
                            '{$row['T006_Codigo_Produto']}', 
                            current_date(), 
                            '{$Descricao_Operacao}', 
                            '{$row['Falta']}', 
                            '', 
                            'E', 
                            '{$row['T006_Valor_Preco_Unitario']}', 
                            '', 
                            '{$row['T006_Valor_Custo_Unitario']}', 
                            'N', 
                             0, 
                            '{$tmpD004_Id}',
                            '{$row['T006_Id']}' 
                        )
EOT;
                    mysql_query($cClausula);
                    $T001_Id = $g['mysqlLastId'];

                    $erroEntrada = mysql_error();

                    if(!empty($erroEntrada)){
                        mysql_query("ROLLBACK");
                        $retorno[0]=false;
                        $retorno[1]="<br>ERRO(E):".$erroEntrada."<br><br>";
                        return $retorno;
                    } 

                    
                    $cClausula = <<<EOT
                        insert into T186 ( 
                            T186_T066_Id, 
                            T186_T001_Id, 
                            T186_Quantidade 
                        ) values ( 
                            '{$row['T006_T066_Id']}', 
                            '{$T001_Id}', 
                            '{$row['Falta']}'
                        )
EOT;
                    mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        $retorno[0]=false;
                        $retorno[1]="ERRO(T186 E): " . $cErro."<br><br>";
                        return $retorno;

                    }

                    $CAD002->D001_reprocessa_historico(0, $row['T006_D009_Id']);
                    mysql_query("UPDATE T006 SET T006_Flag_Gerar_Cotacao='N' WHERE T006_Id={$row['T006_Id']}");
                }
                $retorno[0]=true;
                $retorno[1]="Transferência realizada com sucesso.";
                mysql_query("COMMIT");
            }
            return $retorno;
        }

    }


}
