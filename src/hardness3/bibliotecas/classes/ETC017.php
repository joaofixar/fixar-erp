<?php
namespace hardness;

definirClasse('ETC017');
class ETC017_
{
    /**
     * inserirRegistroTarefa
     * @param $acaoId - Id do registro que será inserido na tarefa
     * @param $tabela = tabela que será registrada na tarefa (Ex: T003)
     */
    public function inserirRegistroTarefa($acaoId, $tabela)
    {
        global $g;
        switch ($tabela) {
            case 'T003':
                $lastT003_Id = $g['mysqlLastId'];
                $sqlT003 = mysql_query("SELECT * FROM T003 WHERE T003_Id = '{$acaoId}'");
                $rowT003 = mysql_fetch_assoc($sqlT003);

                mysql_query("INSERT INTO D085 (D085_D024_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Mensagem, D085_Entidade, D085_Id_Entidade, D085_Flag_Manual, D085_T003_Id, D085_Flag_Tipo, D085_D013_Id) 
                                        VALUES ('{$rowT003['T003_D024_Id']}', '{$rowT003['T003_C007_Id_Vendedor_Interno']}', curdate(), curtime(), concat('Orcamento {$acaoId}'), 'T003', '{$acaoId}', 'N', '{$acaoId}', '3', '{$rowT003['T003_D013_Id']}')");
                $g['mysqlLastId'] = $lastT003_Id;
                mysql_query("UPDATE D085 LEFT JOIN T003 ON T003_Id=D085_T003_Id LEFT JOIN T005 ON T003_Id=T005_T003_Id LEFT JOIN T007 ON T005_Id=T007_T005_Id SET D085_T005_Id = T005_Id, D085_T007_Id = T007_Id WHERE D085_T003_Id = '{$acaoId}'");
            break;

            case 'T005':
                $lastT005_Id = $g['mysqlLastId'];
                $sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_Id = '{$acaoId}'");
                $rowT005 = mysql_fetch_assoc($sqlT005);

                mysql_query("INSERT INTO D085 (D085_D024_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Mensagem, D085_Entidade, D085_Id_Entidade, D085_Flag_Manual, D085_T005_Id, D085_Flag_Tipo, D085_D013_Id) 
                                        VALUES ('{$rowT005['T005_D024_Id']}', '{$rowT005['T005_C007_Id_Vendedor_Interno']}', curdate(), curtime(), concat('Pedido ', '{$acaoId}'), 'T005', '{$rowT005['T005_Id']}', 'N','{$rowT005['T005_Id']}', '4', '{$rowT005['T005_D013_Id']}')");
                $g['mysqlLastId'] = $lastT005_Id;
                mysql_query("UPDATE D085 LEFT JOIN T005 ON T005_Id=D085_T005_Id LEFT JOIN T003 ON T003_Id=T005_T003_Id LEFT JOIN T007 ON T005_Id=T007_T005_Id SET D085_T003_Id = T005_T003_Id, D085_T007_Id = T007_Id WHERE D085_T005_Id = '{$acaoId}'");
                //mysql_query("UPDATE D085 LEFT JOIN T003 ON T003_Id=D085_T003_Id LEFT JOIN T005 ON T003_Id=T005_T003_Id LEFT JOIN T007 ON T005_Id=T007_T005_Id SET D085_T005_Id = T005_Id, D085_T007_Id = T007_Id WHERE D085_T003_Id = '{$rowD085['T005_T003_Id']}'"); O valor $rowD085['T005_T003_Id'] está errado, não tem rowD085, sempre vem vazio, nunca atualizou, retirando 
            break;

            case 'T007':
                $lastT007_Id = $g['mysqlLastId'];
                $sqlT007 = mysql_query("SELECT * FROM T007 WHERE T007_Id = '{$acaoId}'");
                $rowT007 = mysql_fetch_assoc($sqlT007);

                $sqlT005 = mysql_query("SELECT T005_D013_Id, T005_T003_Id FROM T005 WHERE T005_Id = '{$rowT007['T007_T005_Id']}'");
                $rowT005 = mysql_fetch_assoc($sqlT005);

                mysql_query("INSERT INTO D085 (D085_D024_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Mensagem, D085_Entidade, D085_Id_Entidade, D085_Flag_Manual, D085_T007_Id, D085_Flag_Tipo, D085_D013_Id) 
                                        VALUES ('{$rowT007['T007_D024_Id']}', '{$rowT007['T007_C007_Id_Vendedor_Interno']}', curdate(), curtime(), concat('Nota Fiscal ','{$rowT007['T007_Numero_Nota_Fiscal']}'), 'T007', '{$acaoId}', 'N', '{$acaoId}', '5', '{$rowT005['T005_D013_Id']}')");
                $g['mysqlLastId'] = $lastT007_Id;
                mysql_query("UPDATE D085 LEFT JOIN T007 ON T007_Id=D085_T007_Id LEFT JOIN T005 ON T005_Id=T007_T005_Id SET D085_T003_Id = T005_T003_Id, D085_T005_Id = T007_T005_Id WHERE D085_T007_Id = '{$acaoId}'");
                mysql_query("UPDATE D085 LEFT JOIN T005 ON T005_Id=D085_T005_Id LEFT JOIN T003 ON T003_Id=T005_T003_Id LEFT JOIN T007 ON T005_Id=T007_T005_Id SET D085_T003_Id = T005_T003_Id, D085_T007_Id = T007_Id WHERE D085_T005_Id = '{$rowT007['T007_T005_Id']}'");
                mysql_query("UPDATE D085 LEFT JOIN T003 ON T003_Id=D085_T003_Id LEFT JOIN T005 ON T003_Id=T005_T003_Id LEFT JOIN T007 ON T005_Id=T007_T005_Id SET D085_T005_Id = T005_Id, D085_T007_Id = T007_Id WHERE D085_T003_Id = '{$rowT005['T005_T003_Id']}'");
            break;

            case 'T218':
                //Verifica as tarefas de cada produto ligado a um orçamento/pedido
                $sqlVenda = mysql_query("SELECT IFNULL(T004_T003_Id,0) AS T004_T003_Id, IFNULL(T006_T005_Id,0) AS T006_T005_Id, T218_Grupo FROM T219
                                                                                  LEFT JOIN T218 ON T218_Id=T219_T218_Id
                                                                                  LEFT JOIN T006 ON T006_Id=T219_T006_Id
                                                                                  LEFT JOIN T004 ON T004_Id=T219_T004_Id
                                                                                  WHERE T219_T218_Id = '{$acaoId}'
                                                                                  GROUP BY T004_T003_Id, T006_T005_Id");
                while($rowVenda = mysql_fetch_assoc($sqlVenda)){
                    //Verifica a tarefa do orçamento/pedido
                    $sqlTarefa = mysql_query("SELECT D085_T259_Id FROM D085 WHERE (D085_T005_Id = '{$rowVenda['T006_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0) OR (D085_T003_Id = '{$rowVenda['T004_T003_Id']}' AND IFNULL(D085_T003_Id,0) > 0)");
                    if(mysql_num_rows($sqlTarefa) > 0){
                        $rowTarefa = mysql_fetch_array($sqlTarefa);
                        $sqlCheck = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$rowTarefa['D085_T259_Id']}' AND D085_Id_Entidade = '{$rowVenda['T218_Grupo']}' AND D085_Entidade = 'T218'");
                        if(mysql_num_rows($sqlCheck) <= 0){
                            mysql_query("INSERT INTO D085 (D085_T259_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Id_Entidade, D085_Entidade, D085_Mensagem) VALUES ('{$rowTarefa['D085_T259_Id']}', '{$g['usuarioAtual']}', current_date(), current_time(), '{$rowVenda['T218_Grupo']}', 'T218', 'Cot {$rowVenda['T218_Grupo']}')");
                        }
                    }
                }
            break;

            case 'T224':
                //Verifica as tarefas de cada produto ligado a um pedido
                $sqlVenda = mysql_query("SELECT T006_T005_Id FROM T225
                                                        LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                                        LEFT JOIN T006 ON T006_Id=T219_T006_Id
                                                        WHERE T225_T224_Id = '{$acaoId}'
                                                        GROUP BY T006_T005_Id");
                while($rowVenda = mysql_fetch_assoc($sqlVenda)){
                    //Verifica a tarefa do pedido
                    $sqlTarefa = mysql_query("SELECT D085_T259_Id FROM D085 WHERE D085_T005_Id = '{$rowVenda['T006_T005_Id']}' AND IFNULL(D085_T005_Id,0) > 0");
                    if(mysql_num_rows($sqlTarefa) > 0){
                        $rowTarefa = mysql_fetch_array($sqlTarefa);
                        $sqlCheck = mysql_query("SELECT * FROM D085 WHERE D085_T259_Id = '{$rowTarefa['D085_T259_Id']}' AND D085_Id_Entidade = '{$acaoId}' AND D085_Entidade = 'T224'");
                        if(mysql_num_rows($sqlCheck) <= 0){
                            mysql_query("INSERT INTO D085 (D085_T259_Id, D085_C007_Id, D085_Data, D085_Hora, D085_Id_Entidade, D085_Entidade, D085_Mensagem) VALUES ('{$rowTarefa['D085_T259_Id']}', '{$g['usuarioAtual']}', current_date(), current_time(), '{$acaoId}', 'T224', 'OC {$acaoId}')");
                        }
                    }
                }
            break;
        }
    }
}