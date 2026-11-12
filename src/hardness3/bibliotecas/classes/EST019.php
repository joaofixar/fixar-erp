<?php
namespace hardness;
definirClasse('EST019');
class EST019_
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

        /**
     * insereItemCotacaoEmergencia
     *
     * @param int $T218_Id
     */
    public function insereItemCotacaoEmergencia($T293_Id, $extra, $T003_Id)
    {
        global $g;

        $sqlD024 = mysql_query("SELECT T293_D024_Id
                                  FROM T293
                                 WHERE T293_Id = '{$T293_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T004_Flag_Gerar_Cotacao='S' AND T004_C007_Id_Comprador = '{$g['usuarioAtual']}'"));

        if($T003_Id){
            
            $sql = <<<EOT
                    SELECT T004_Id,
                            D001_Codigo_Produto,
                            D001_Descricao_Produto,
                            D001_Descricao_Ingles,
                            D009_Quantidade_Comprar_Calculada,
                            IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                            D009_Id,
                            T004_Valor_Preco_Sem_Desconto_Unitario,
                            D009_C004_Id,
                            D005_Id,
                            D037_Id,
                            D083_Codigo_Produto_Fornecedor,
                            T005_Id
                       FROM T004 
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
                  LEFT JOIN T294 ON T004_Id=T294_T004_Id
                  LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                  LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                  LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                  LEFT JOIN T241 ON T241_D001_Id=D001_Id
                  LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                  LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T293_D024_Id']}'
                  LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                     WHERE {$extra}
                       AND T004_T003_Id={$T003_Id}
                       AND IFNULL(T004_T219_Id,0)<=0 AND IFNULL(T219_Id,0)<=0
                  GROUP BY T004_Id
EOT;
        }else{

            $sql = <<<EOT
                    SELECT T004_Id,
                           D001_Codigo_Produto,
                           D001_Descricao_Produto,
                           D001_Descricao_Ingles,
                           D009_Quantidade_Comprar_Calculada,
                           IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                           D009_Id,
                           T004_Valor_Preco_Sem_Desconto_Unitario,
                           D009_C004_Id,
                           D005_Id,
                           D037_Id,                           
                           D083_Codigo_Produto_Fornecedor,
                           T005_Id
                      FROM T004 
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
                 LEFT JOIN T294 ON T004_Id=T294_T004_Id
                 LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
                 LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                 LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
                 LEFT JOIN T241 ON T241_D001_Id=D001_Id
                 LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                 LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T293_D024_Id']}'
                 LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                     WHERE {$extra}
                  GROUP BY T004_Id
EOT;
        }
                
        $retorno = mysql_query($sql);
        $error = mysql_error();
        if(!empty($error)){
            return $error;
        }

        while ($row = mysql_fetch_array($retorno)) {
            
            // Tratando caracteres de uma string
            $row['D001_Descricao_Ingles'] = mysql_real_escape_string($row['D001_Descricao_Ingles']);

            $sqlInsert = "insert into T294 ";
            $sqlInsert .= "(T294_T293_Id, ";
            $sqlInsert .= "T294_C004_Id, ";
            $sqlInsert .= "T294_Codigo_Produto, ";
            $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
            $sqlInsert .= "T294_Descricao_Produto, ";
            $sqlInsert .= "T294_Quantidade, ";
            //$sqlInsert .= "T294_Valor_Custo_Unitario,  ";
            $sqlInsert .= "T294_D009_Id, ";
            $sqlInsert .= "T294_D037_Id, ";
            $sqlInsert .= "T294_D005_Id, ";
            $sqlInsert .= "T294_T004_Id) ";
            $sqlInsert .= "values  ";
            $sqlInsert .= "('{$T293_Id}', ";
            $sqlInsert .= "'{$row['D009_C004_Id']}', ";
            $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
            $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
            $sqlInsert .= "'{$row['D001_Descricao_Ingles']}', ";
            $sqlInsert .= "'{$row['T004_Quantidade']}', ";
            //$sqlInsert .= "'{$row['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
            $sqlInsert .= "'{$row['D009_Id']}', ";
            $sqlInsert .= "'{$row['D037_Id']}', ";
            $sqlInsert .= "'{$row['D005_Id']}', ";
            $sqlInsert .= "'{$row['T004_Id']}' ";
            $sqlInsert .= "); ";
            mysql_query($sqlInsert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro . " <br/><br/>SQL = " . $sqlInsert;
            }
            
            /*if($T003_Id){
                $T219_Id = $g['mysqlLastId'];
                mysql_query("UPDATE T004 SET T004_T219_Id={$T219_Id} WHERE T004_Id={$row['T004_Id']}");
            }    */        
        }

        return true;
    }

    public function atualizarAliquotas($arrayParametros = null)
    { 
        global $g;

        $D009_Id               = $arrayParametros['D009_Id'];
        $T294_Id               = $arrayParametros['T294_Id'];  
        $T293_Id               = $arrayParametros['T293_Id']; 
        $D001_Peso_Unitario_Kg = $arrayParametros['D001_Peso_Unitario_Kg']; 
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
            
        require_once('bibliotecas/classes/IMP003.php');
        $IMP003 = new IMP003();
		$Aliquotas = $IMP003->retornaAliquotas($D009_Id, $T293_Id, false, false);
                           
        $sql = "UPDATE T294 SET ";
        $sql .= "T294_D005_Id                           ='{$Aliquotas['D005_Id']}',";
        $sql .= "T294_Classificacao_Fiscal              ='{$Aliquotas['D005_Classificacao_Fiscal']}',";
        $sql .= "T294_Aliquota_IPI                      ='{$Aliquotas['Aliquota_IPI']}',";
        $sql .= "T294_Aliquota_PIS                      ='{$Aliquotas['Aliquota_PIS']}',";
        $sql .= "T294_Aliquota_COFINS                   ='{$Aliquotas['Aliquota_COFINS']}',";    
        $sql .= "T294_Aliquota_Imposto_Importacao       ='{$Aliquotas['Imposto_Importacao']}',";
        $sql .= "T294_Aliquota_ICMS                     ='{$Aliquotas['Aliquota_ICMS_Importacao']}',";    
        $sql .= "T294_Aliquota_ICMS_Presumido           ='{$Aliquotas['Aliquota_ICMS_Presumido_Importacao']}',";    
        $sql .= "T294_Percentual_Reducao_ICMS           ='{$Aliquotas['Percentual_Reducao_ICMS_Importacao']}',";    
        $sql .= "T294_Percentual_Reducao_ICMS_Presumido ='{$Aliquotas['Percentual_Reducao_ICMS_Presumido_Importacao']}',";    
        $sql .= "T294_Aliquota_ICMS_PIS_COFINS          ='{$Aliquotas['Aliquota_ICMS_PIS_COFINS_Importacao']}',";    
        $sql .= "T294_Aliquota_ICMS_Credito_Presumido   ='{$Aliquotas['Aliquota_ICMS_Credito_Presumido_Importacao']}',";      
        $sql .= "T294_Peso_Kg_Liquido_Unitario          ='{$D001_Peso_Unitario_Kg}' ";             
        $sql .= "WHERE T294_Id='{$T294_Id}'";
               
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Erro atualizando item do orcamento ID {$T294_Id}: $erro";
            log("entrou aqui ?? ");
        }
    
        mysql_query("call T294_Gravar_Totalizacao('{$T294_Id}');");  
        mysql_query("COMMIT");

        return true;
    }
 
    /**
     * gerarCotacaoAutomatica
     *
     * @param string $extra
     * @return true/string (erros)
     */
    public function gerarCotacaoAutomatica($extra)
    {
        global $g;

        $countErro = 0;
        $arrayD024 = array();

        $sql = "SELECT Fornecedor.D024_Id,
                       GROUP_CONCAT(T004_Id SEPARATOR ', ') AS T004_Ids, 
                       COUNT(T004_Id) AS count
                  FROM T004 
             LEFT JOIN D009 ON D009_Id=T004_D009_Id
             LEFT JOIN D049 ON D049_Id=D009_D049_Id
             LEFT JOIN D001 ON D001_Id=D049_D001_Id
             LEFT JOIN D082 ON D082_Id=D049_D082_Id
             LEFT JOIN D037 ON D037_Id=T004_D037_Id
             LEFT JOIN D005 ON D005_Id=T004_D005_Id
             LEFT JOIN D006 ON D006_Id=T004_D006_Id
             LEFT JOIN T006 ON T006_T004_Id=T004_Id
             LEFT JOIN T005 ON T005_Id=T006_T005_Id
             LEFT JOIN T003 ON T003_Id=T004_T003_Id
             LEFT JOIN D024 AS Cliente ON T003_D024_Id=Cliente.D024_Id
             LEFT JOIN T294 ON T004_Id=T294_T004_Id
             LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T003_C007_Id_Vendedor_Interno
             LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
             LEFT JOIN C007 AS marcou ON marcou.C007_Id = T004_C007_Id_Comprador
             LEFT JOIN T241 ON T241_D001_Id=D001_Id
             LEFT JOIN D024 AS Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
             LEFT JOIN T004A ON T004_Id=T004A_T004_Id
                 WHERE {$extra}";

        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY Fornecedor.D024_Id";

        $retorno = mysql_query($sql);
        while($row = mysql_fetch_array($retorno)){
            if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['D024_Id']] = $row['T004_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $T293  = mysql_query("SELECT T293_Grupo FROM T293 ORDER BY T293_Grupo DESC LIMIT 1");
        $mT293 = mysql_fetch_array($T293);
        $grupo = $mT293['T293_Grupo'] + 1;

        $C004 = mysql_query("SELECT C004_Observao_Cotation 
                               FROM C004
                              WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $T004_Ids) {    
                        
            mysql_query("INSERT INTO T293 (T293_D024_Id, 
                                           T293_C004_Id, 
                                           T293_C007_Id, 
                                           T293_Data_Emissao,
                                           T293_Important_Remark,
                                           T293_Grupo
                                ) VALUES (
                                          '{$D024_Id}', 
                                          '{$g['empresaAtual']}',
                                          '{$g['usuarioAtual']}', 
                                          CURDATE(),
                                          '{$mC004['C004_Observao_Cotation']}',
                                          '{$grupo}')");    
            
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }

            $T293_Id = $g['mysqlLastId'];

            $sql = <<<EOT
                       SELECT T004_Id,
                              D001_Codigo_Produto,
                              D001_Descricao_Produto,
                              D001_Descricao_Ingles,
                              D009_Quantidade_Comprar_Calculada,
                              IF(T004_Quantidade>D009_Quantidade_Estoque_Liquido, T004_Quantidade-if(D009_Quantidade_Estoque_Liquido>0,D009_Quantidade_Estoque_Liquido,0), T004_Quantidade) as T004_Quantidade,
                              D009_Id,
                              T004_Valor_Preco_Sem_Desconto_Unitario,
                              D009_C004_Id,
                              D005_Id,
                              D037_Id,
                              D049_Origem_Mercadoria,
                              D083_Codigo_Produto_Fornecedor
                         FROM T004 
                    LEFT JOIN D009 on D009_Id=T004_D009_Id
                    LEFT JOIN D049 on D049_Id=D009_D049_Id
                    LEFT JOIN D001 on D001_Id=D049_D001_Id
                    LEFT JOIN D082 on D082_Id=D049_D082_Id
                    LEFT JOIN D037 on D037_Id=T004_D037_Id
                    LEFT JOIN D005 on D005_Id=T004_D005_Id
                    LEFT JOIN D006 on D006_Id=T004_D006_Id
                    LEFT JOIN T006 on T006_T004_Id=T004_Id
                    LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '{$D024_Id}'
                        WHERE T004_Id IN ({$T004_Ids})
                     GROUP BY T004_Id
EOT;
            log("vai fazer a query dos produtos orçamento");
            log($sql);
            $retorno = mysql_query($sql);

            while ($row = mysql_fetch_array($retorno)) {

                $row['D009_C004_Id']                           = mysql_real_escape_string($row['D009_C004_Id']);
                $row['D001_Codigo_Produto']                    = mysql_real_escape_string($row['D001_Codigo_Produto']);
                $row['D083_Codigo_Produto_Fornecedor']         = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                $row['D001_Descricao_Ingles']                  = mysql_real_escape_string($row['D001_Descricao_Ingles']);
                $row['T004_Quantidade']                        = mysql_real_escape_string($row['T004_Quantidade']);
                $row['T004_Valor_Preco_Sem_Desconto_Unitario'] = mysql_real_escape_string($row['T004_Valor_Preco_Sem_Desconto_Unitario']);
                $row['D009_Id']                                = mysql_real_escape_string($row['D009_Id']);
                $row['D037_Id']                                = mysql_real_escape_string($row['D037_Id']);
                $row['D005_Id']                                = mysql_real_escape_string($row['D005_Id']);
                $row['T004_Id']                                = mysql_real_escape_string($row['T004_Id']);

                $sqlInsert = "INSERT INTO T294 ";
                $sqlInsert .= "(T294_T293_Id, ";
                $sqlInsert .= "T294_C004_Id, ";
                $sqlInsert .= "T294_Codigo_Produto, ";
                $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
                $sqlInsert .= "T294_Descricao_Produto, ";
                $sqlInsert .= "T294_Quantidade, ";
                //$sqlInsert .= "T294_Valor_Custo_Unitario,  ";
                $sqlInsert .= "T294_D009_Id, ";
                $sqlInsert .= "T294_D037_Id, ";
                $sqlInsert .= "T294_D005_Id, ";
                $sqlInsert .= "T294_T004_Id) ";
                $sqlInsert .= "VALUES  ";
                $sqlInsert .= "('{$T293_Id}', ";
                $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                $sqlInsert .= "'{$row['D001_Descricao_Ingles']}', ";
                $sqlInsert .= "'{$row['T004_Quantidade']}', ";
                //$sqlInsert .= "'{$row['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
                $sqlInsert .= "'{$row['D009_Id']}', ";
                $sqlInsert .= "'{$row['D037_Id']}', ";
                $sqlInsert .= "'{$row['D005_Id']}', ";
                $sqlInsert .= "'{$row['T004_Id']}' ";
                $sqlInsert .= "); ";
                mysql_query($sqlInsert);
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro: " . $erro;
                }
            }
        }
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return array('grupo' => $grupo);
    }

    /**
     * est019atualizarCFOP
     *
     * @param int $T293_id
     */
    public function est019atualizarCFOP($T293_Id)
    {
        global $g;
        
        // Não retorna CFOP por enquanto, na cotation é definido no T293 (cabeçalho)
        /*
        $sqlT293 = mysql_query("SELECT * 
                                  FROM T293 
                             LEFT JOIN T294 ON T293_Id = T294_T293_Id 
                                 WHERE T293_Id = '{$T293_Id}'");

        while ($T293 = mysql_fetch_array($sqlT293)) {

            $D006_Id = $this->retornaCFOP($T293['T293_D024_Id'], $T293['T294_D009_Id'], $g['empresaAtual'], $T293['T294_Flag_Origem_Produto']);

            $demonstrativoCFOP = $this->retornaCFOP($T293['T293_D024_Id'], $T293['T294_D009_Id'], $g['empresaAtual'], $T293['T294_Flag_Origem_Produto'], true);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);

            mysql_query("UPDATE T219 
                      LEFT JOIN D009 ON D009_Id=T294_D009_Id
                      LEFT JOIN D049 ON D049_Id=D009_D049_Id
                      LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            SET T294_D006_Id = '{$D006_Id}', T294_D006_Id_Info = '{$demonstrativoCFOP}', T294_Flag_Pre_Cadastro=D001_Flag_Pre_Cadastro WHERE T294_Id = '{$T293['T294_Id']}'");
            
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro1: " . $erro;
            }

        }
        if ($D006_Id > 0) {
            mysql_query("UPDATE T293 SET T293_D006_Id='{$D006_Id}' WHERE T293_Id='{$T293_Id}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro2: " . $erro;
            }

        }*/

        return true;

    }

    /**
     * insereItemCotacaoPedidoEmergencia
     *
     * @param int $T293_Id
     */
    public function insereItemCotacaoPedidoEmergencia($T293_Id, $extra)
    {
        global $g;

        $sqlD024 = mysql_query("SELECT T293_D024_Id FROM T293 WHERE T282_Id = '{$T293_Id}'");
        $rowD024 = mysql_fetch_array($sqlD024);

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T006_Flag_Gerar_Cotacao='S' AND T006_C007_Id_Comprador = '{$g['usuarioAtual']}'"), true);
        
        $sql = <<<EOT
                   SELECT T006_Id,
                          D001_Codigo_Produto,
                          D001_Descricao_Produto,
                          D001_Descricao_Ingles,
                          D009_Quantidade_Comprar_Calculada,
                          T006_Quantidade,
                          T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as Qtde_Comprar,
                          D009_Id,
                          D001_Id,
                          T006_Valor_Preco_Sem_Desconto_Unitario,
                          D009_C004_Id,
                          D005_Id,
                          D037_Id,
                          D083_Codigo_Produto_Fornecedor,
                          ifnull(D049_Flag_Tipo,'CP') AS D049_Flag_Tipo,
                          T005_Id
                     FROM T006
                LEFT JOIN D009 on D009_Id=T006_D009_Id
                LEFT JOIN D049 on D049_Id=D009_D049_Id
                LEFT JOIN D001 on D001_Id=D049_D001_Id
                LEFT JOIN D037 on D037_Id=T006_D037_Id
                LEFT JOIN D005 on D005_Id=T006_D005_Id
                LEFT JOIN D006 on D006_Id=T006_D006_Id
                LEFT JOIN T005 ON T005_Id=T006_T005_Id
                LEFT JOIN D024 as Cliente ON T005_D024_Id=Cliente.D024_Id
                LEFT JOIN T003 ON T003_Id=T005_T003_Id
                LEFT JOIN T004 ON T004_Id=T006_T004_Id
                LEFT JOIN T294 ON T006_Id=T294_T006_Id
                LEFT JOIN T293 ON T293_Id=T294_T293_Id
                LEFT JOIN T296 ON T294_Id=T296_T294_Id
                LEFT JOIN T295 ON T295_Id=T296_T295_Id
                LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                LEFT JOIN T241 ON T241_D001_Id=D001_Id
                LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                    WHERE {$extra}
                 GROUP BY T006_Id
EOT;
        $retorno = mysql_query($sql);

        while ($row = mysql_fetch_array($retorno)) {
            log("entrou no while para inserir os itens");
            if($row['D049_Flag_Tipo'] != 'PD'){

                $row['D001_Codigo_Produto']            = mysql_real_escape_string($row['D001_Codigo_Produto']);
                $row['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                $row['D001_Descricao_Ingles']          = mysql_real_escape_string($row['D001_Descricao_Ingles']);

                $sqlInsert = "insert into T294 ";
                $sqlInsert .= "(T294_T293_Id, ";
                $sqlInsert .= "T294_C004_Id, ";
                $sqlInsert .= "T294_Codigo_Produto, ";
                $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
                $sqlInsert .= "T294_Descricao_Produto, ";
                $sqlInsert .= "T294_Quantidade, ";
                //$sqlInsert .= "T294_Valor_Custo_Unitario,  ";
                $sqlInsert .= "T294_D009_Id, ";
                $sqlInsert .= "T294_D037_Id, ";
                $sqlInsert .= "T294_D005_Id, ";
                $sqlInsert .= "T294_T006_Id) ";
                $sqlInsert .= "values  ";
                $sqlInsert .= "('{$T293_Id}', ";
                $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                $sqlInsert .= "'{$row['D001_Descricao_Ingles']}', ";
                $sqlInsert .= "'{$row['Qtde_Comprar']}', ";
                //$sqlInsert .= "'{$row['T004_Valor_Preco_Sem_Desconto_Unitario']}', ";
                $sqlInsert .= "'{$row['D009_Id']}', ";
                $sqlInsert .= "'{$row['D037_Id']}', ";
                $sqlInsert .= "'{$row['D005_Id']}', ";
                $sqlInsert .= "'{$row['T006_Id']}' ";
                $sqlInsert .= "); ";

                mysql_query($sqlInsert);
                $erro = mysql_error();

                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro: " . $erro . " <br/><br/>SQL = " . $sqlInsert;
                }

            } else {

                $sqlComponentes = mysql_query("SELECT D009_Id,
                                                      D009_C004_Id,
                                                      D037_Id,
                                                      D005_Id,
                                                      D083_Codigo_Produto_Fornecedor,
                                                      D001_Codigo_Produto,
                                                      D001_Descricao_Ingles,
                                                      D001_Descricao_Produto,
                                                      D074_Quantidade
                                                 FROM D074
                                            LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                                            LEFT JOIN D049 ON D001_Id = D049_D001_Id
                                            LEFT JOIN D009 ON D049_Id = D009_D049_Id
                                            LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                            LEFT JOIN D005 ON D005_Id = D001_D005_Id
                                            LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$rowD024['T218_D024_Id']}'
                                                WHERE D074_D001_Id = '{$row['D001_Id']}' 
                                                  AND D009_C004_Id ='{$g['empresaAtual']}'
                                                  AND D049_Flag_Tipo = 'CP'");

                while($rowComponentes = mysql_fetch_array($sqlComponentes)){

                    $rowComponentes['Qtde_Comprar']                   = $row['Qtde_Comprar']*$rowComponentes['D074_Quantidade'];
                    $rowComponentes['D001_Codigo_Produto']            = mysql_real_escape_string($rowComponentes['D001_Codigo_Produto']);
                    $rowComponentes['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($rowComponentes['D083_Codigo_Produto_Fornecedor']);
                    $rowComponentes['D001_Descricao_Ingles']          = mysql_real_escape_string($rowComponentes['D001_Descricao_Ingles']);

                    $sqlInsert = "insert into T294 ";
                    $sqlInsert .= "(T294_T293_Id, ";
                    $sqlInsert .= "T294_C004_Id, ";
                    $sqlInsert .= "T294_Codigo_Produto, ";
                    $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
                    $sqlInsert .= "T294_Descricao_Produto, ";
                    $sqlInsert .= "T294_Quantidade, ";
                    $sqlInsert .= "T294_Valor_Custo_Unitario,  ";
                    $sqlInsert .= "T294_D009_Id, ";
                    $sqlInsert .= "T294_D037_Id, ";
                    $sqlInsert .= "T294_D005_Id, ";
                    $sqlInsert .= "T294_T006_Id) ";
                    $sqlInsert .= "('{$T293_Id}', ";
                    $sqlInsert .= "'{$rowComponentes['D009_C004_Id']}', ";
                    $sqlInsert .= "'{$rowComponentes['D001_Codigo_Produto']}', ";
                    $sqlInsert .= "'{$rowComponentes['D083_Codigo_Produto_Fornecedor']}', ";
                    $sqlInsert .= "'{$rowComponentes['D001_Descricao_Ingles']}', ";
                    $sqlInsert .= "'{$rowComponentes['Qtde_Comprar']}', ";
                    $sqlInsert .= "'0', ";
                    $sqlInsert .= "'{$rowComponentes['D009_Id']}', ";
                    $sqlInsert .= "'{$rowComponentes['D037_Id']}', ";
                    $sqlInsert .= "'{$rowComponentes['D005_Id']}', ";
                    $sqlInsert .= "'{$row['T006_Id']}' ";
                    $sqlInsert .= "); ";

                    log("insert Ligia: ".$sqlInsert);
                    mysql_query($sqlInsert);
                }
            }
        }

        return true;
    }

       /**
     * gerarCotacaoPedidoAutomatica
     *
     * @param string $extra
     * @return true/string (erros)
     */
    public function gerarCotacaoPedidoAutomatica($extra)
    {
        global $g;

        $countErro = 0;
        $arrayD024 = array();

        $sql = "SELECT Fornecedor.D024_Id, GROUP_CONCAT(T006_Id SEPARATOR ', ') as T006_Ids,
                       COUNT(T006_Id) as count FROM T006 
             LEFT JOIN T261 on T006_Id=T261_T006_Id
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
             LEFT JOIN T294 ON T006_Id=T294_T006_Id
             LEFT JOIN T293 ON T293_Id=T294_T293_Id
             LEFT JOIN T296 ON T294_Id=T296_T294_Id
             LEFT JOIN T295 ON T295_Id=T296_T295_Id
             LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
             LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
             LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
             LEFT JOIN T241 ON T241_D001_Id=D001_Id
             LEFT JOIN D024 as Fornecedor ON T241_D024_Id=Fornecedor.D024_Id
                 WHERE {$extra}";

        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY Fornecedor.D024_Id";
        log("vai fazer a query agora");
        $retorno = mysql_query($sql);

        while($row = mysql_fetch_array($retorno)){
            if(empty($row['D024_Id']) || $row['D024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['D024_Id']] = $row['T006_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $T293  = mysql_query("SELECT T293_Grupo FROM T293 ORDER BY T293_Grupo DESC LIMIT 1");
        $mT293 = mysql_fetch_array($T293);
        $grupo = $mT293['T293_Grupo'] + 1;

        $C004 = mysql_query("SELECT C004_Observao_Cotation 
                               FROM C004
                              WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $T006_Ids) {


            mysql_query("INSERT INTO T293 (T293_D024_Id, 
                                           T293_C004_Id, 
                                           T293_C007_Id, 
                                           T293_Data_Emissao,
                                           T293_Important_Remark,
                                           T293_Grupo
                                ) VALUES (
                                           '{$D024_Id}',
                                           '{$g['empresaAtual']}', 
                                           '{$g['usuarioAtual']}', 
                                           CURDATE(),
                                           '{$mC004['C004_Observao_Cotation']}',
                                           '{$grupo}')");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
            $T293_Id = $g['mysqlLastId'];

            $sql = <<<EOT
                        SELECT T006_Id,
                               D001_Codigo_Produto,
                               D001_Descricao_Produto,
                               D001_Descricao_Ingles,
                               D009_Quantidade_Comprar_Calculada,
                               T006_Quantidade,
                               T006_Quantidade-if(T006_Quantidade_Estoque>0,T006_Quantidade_Estoque,0)-T006_Quantidade_OC as Qtde_Comprar,
                               D009_Id,
                               T006_Valor_Preco_Sem_Desconto_Unitario,
                               D009_C004_Id,
                               D005_Id,
                               D001_Id,
                               D037_Id,
                               D083_Codigo_Produto_Fornecedor,
                               ifnull(D049_Flag_Tipo,'CP') AS D049_Flag_Tipo,
                               T005_Id
                          FROM T006
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
                     LEFT JOIN T294 ON T004_Id=T294_T004_Id OR T006_Id = T294_T006_Id
                     LEFT JOIN T293 ON T293_Id=T294_T293_Id
                     LEFT JOIN T296 ON T294_Id=T296_T294_Id
                     LEFT JOIN T295 ON T295_Id=T296_T295_Id
                     LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id='{$D024_Id}'
                     LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
                     LEFT JOIN C007 AS comprador ON comprador.C007_Id=D009_C007_Id_Comprador
                     LEFT JOIN C007 AS marcou ON marcou.C007_Id = T006_C007_Id_Comprador
                         WHERE T006_Id IN ({$T006_Ids})
                      GROUP BY T006_Id
EOT;
            $retorno = mysql_query($sql);

            while ($row = mysql_fetch_array($retorno)) {

                if($row['D049_Flag_Tipo'] != 'PD'){

                    $row['D009_C004_Id']                           = mysql_real_escape_string($row['D009_C004_Id']);
                    $row['D001_Codigo_Produto']                    = mysql_real_escape_string($row['D001_Codigo_Produto']);
                    $row['D083_Codigo_Produto_Fornecedor']         = mysql_real_escape_string($row['D083_Codigo_Produto_Fornecedor']);
                    $row['D001_Descricao_Ingles']                  = mysql_real_escape_string($row['D001_Descricao_Ingles']);
                    $row['Qtde_Comprar']                           = mysql_real_escape_string($row['Qtde_Comprar']);
                    $row['T006_Valor_Preco_Sem_Desconto_Unitario'] = mysql_real_escape_string($row['T006_Valor_Preco_Sem_Desconto_Unitario']);
                    $row['D009_Id']                                = mysql_real_escape_string($row['D009_Id']);
                    $row['D037_Id']                                = mysql_real_escape_string($row['D037_Id']);
                    $row['D005_Id']                                = mysql_real_escape_string($row['D005_Id']);
                    $row['T006_Id']                                = mysql_real_escape_string($row['T006_Id']);

                    $T005_Id = $row['T005_Id'];

                    $sqlInsert = "INSERT INTO T294 ";
                    $sqlInsert .= "(T294_T293_Id, ";
                    $sqlInsert .= "T294_C004_Id, ";
                    $sqlInsert .= "T294_Codigo_Produto, ";
                    $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
                    $sqlInsert .= "T294_Descricao_Produto, ";
                    $sqlInsert .= "T294_Quantidade, ";
                    $sqlInsert .= "T294_Valor_Custo_Unitario,  ";
                    $sqlInsert .= "T294_D009_Id, ";
                    $sqlInsert .= "T294_D037_Id, ";
                    $sqlInsert .= "T294_D005_Id, ";
                    $sqlInsert .= "T294_T006_Id) ";
                    $sqlInsert .= "VALUES  ";
                    $sqlInsert .= "('{$T293_Id}', ";
                    $sqlInsert .= "'{$row['D009_C004_Id']}', ";
                    $sqlInsert .= "'{$row['D001_Codigo_Produto']}', ";
                    $sqlInsert .= "'{$row['D083_Codigo_Produto_Fornecedor']}', ";
                    $sqlInsert .= "'{$row['D001_Descricao_Ingles']}', ";
                    $sqlInsert .= "'{$row['Qtde_Comprar']}', ";
                    $sqlInsert .= "'0', ";
                    $sqlInsert .= "'{$row['D009_Id']}', ";
                    $sqlInsert .= "'{$row['D037_Id']}', ";
                    $sqlInsert .= "'{$row['D005_Id']}', ";
                    $sqlInsert .= "'{$row['T006_Id']}' ";
                    $sqlInsert .= "); ";

                    mysql_query($sqlInsert);
                    $erro = mysql_error();
                    if(!empty($erro)){
                        log("deu erro aqui ? ");
                        mysql_query("ROLLBACK");
                        return "Erro: " . $erro;
                    }

                } else {

                    $sqlComponentes = mysql_query("SELECT D009_Id,
                                                          D009_C004_Id,
                                                          D037_Id,
                                                          D005_Id,
                                                          D083_Codigo_Produto_Fornecedor,
                                                          D001_Codigo_Produto,
                                                          D001_Descricao_Produto,
                                                          D074_Quantidade
                                                     FROM D074
                                                LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao 
                                                LEFT JOIN D049 ON D001_Id = D049_D001_Id 
                                                LEFT JOIN D009 ON D049_Id = D009_D049_Id 
                                                LEFT JOIN D037 ON D037_Id = D001_D037_Id
                                                LEFT JOIN D005 ON D005_Id = D001_D005_Id
                                                LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$D024_Id}'
                                                    WHERE D074_D001_Id = '{$row['D001_Id']}' AND D009_C004_Id='{$g['empresaAtual']}'");
                    while($rowComponentes = mysql_fetch_array($sqlComponentes)){

                        $rowComponentes['Qtde_Comprar']                   = $row['Qtde_Comprar']*$rowComponentes['D074_Quantidade'];
                        $rowComponentes['D001_Codigo_Produto']            = mysql_real_escape_string($rowComponentes['D001_Codigo_Produto']);
                        $rowComponentes['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($rowComponentes['D083_Codigo_Produto_Fornecedor']);
                        $rowComponentes['D001_Descricao_Produto']         = mysql_real_escape_string($rowComponentes['D001_Descricao_Produto']);

                        $sqlInsert = "INSERT INTO T294 ";
                        $sqlInsert .= "(T294_T293_Id, ";
                        $sqlInsert .= "T294_C004_Id, ";
                        $sqlInsert .= "T294_Codigo_Produto, ";
                        $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";
                        $sqlInsert .= "T294_Descricao_Produto, ";
                        $sqlInsert .= "T294_Quantidade, ";
                        $sqlInsert .= "T294_Valor_Custo_Unitario,  ";
                        $sqlInsert .= "T294_D009_Id, ";
                        $sqlInsert .= "T294_D037_Id, ";
                        $sqlInsert .= "T294_D005_Id, ";
                        $sqlInsert .= "T219_T006_Id) ";
                        $sqlInsert .= "VALUES  ";
                        $sqlInsert .= "('{$T293_Id}', ";
                        $sqlInsert .= "'{$rowComponentes['D009_C004_Id']}', ";
                        $sqlInsert .= "'{$rowComponentes['D001_Codigo_Produto']}', ";
                        $sqlInsert .= "'{$rowComponentes['D083_Codigo_Produto_Fornecedor']}', ";
                        $sqlInsert .= "'{$rowComponentes['D001_Descricao_Produto']}', ";
                        $sqlInsert .= "'{$rowComponentes['Qtde_Comprar']}', ";
                        $sqlInsert .= "'0', ";
                        $sqlInsert .= "'{$rowComponentes['D009_Id']}', ";
                        $sqlInsert .= "'{$rowComponentes['D037_Id']}', ";
                        $sqlInsert .= "'{$rowComponentes['D005_Id']}', ";
                        $sqlInsert .= "'{$row['T006_Id']}' ";
                        $sqlInsert .= "); ";

                        mysql_query($sqlInsert);
                        $erro = mysql_error();
                        if(!empty($erro)){
                            mysql_query("ROLLBACK");
                            return "Erro: " . $erro;
                        }
                    }
                }
            }
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        return array('grupo' => $grupo);
    }

    /**
     * adicionarProdutosOrdemCompra
     *
     * @param String $T224_Id
     * @param String $listaT219
     * @return String/Bool
     */
    public function adicionarProdutosOrdemCompra($T295_Id, $listaT294)
    {
        global $g;

        $sqlT295 = mysql_query("SELECT T295_D024_Id,
                                       D024_Nome_Empresa 
                                  FROM T295 
                             LEFT JOIN D024 ON D024_Id=T295_D024_Id
                                 WHERE T295_Id = '{$T295_Id}'");
        $rowT295 = mysql_fetch_array($sqlT295);

        $sqlFornecedor = mysql_query("SELECT DISTINCT T293_D024_Id,
                                             D024_Nome_Empresa 
                                        FROM T294 
                                   LEFT JOIN T293 ON T293_Id=T294_T293_Id
                                   LEFT JOIN D024 ON D024_Id=T293_D024_Id
                                       WHERE T294_Id IN({$listaT294})");
        log("acabaou de fazer a query também");
        if(mysql_num_rows($sqlFornecedor) > 1){

            return "Foram selecionados produtos de fornecedores diferentes";

        } else {

            $rowT293 = mysql_fetch_array($sqlFornecedor);
            if($rowT293['T293_D024_Id'] != $rowT295['T295_D024_Id']){
                return "O fornecedor da P.O  não é o mesmo da Cotation <br><br> P.O: <b>{$rowT295['D024_Nome_Empresa']}</b><br>Cotation: <b>{$rowT293['D024_Nome_Empresa']} ";
            }
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        //inserir na T296 todos da T294 selecionados
        $sql = <<<EOT
                    SELECT * 
                      FROM T294 
                     WHERE T294_Quantidade_PO > 0  
                       AND T294_Id IN({$listaT294})
EOT;
        $retornoConsulta = mysql_query($sql);
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return '8 Erro: ' . $error;
        }

        while ($rowT294 = mysql_fetch_assoc($retornoConsulta)) {
            
            $insertT296 = array(
                "T296_T295_Id"                                     => $T295_Id,
                "T296_T294_Id"                                     => $rowT294['T294_Id'],
                "T296_T060_Id"                                     => $rowT294['T294_T060_Id'],
                "T296_T054_Id"                                     => $rowT294['T294_T054_Id'],
                "T296_D037_Id"                                     => $rowT294['T294_D037_Id'],
                "T296_D005_Id"                                     => $rowT294['T294_D005_Id'],
                "T296_D009_Id"                                     => $rowT294['T294_D009_Id'],
                "T296_T053_Id"                                     => $rowT294['T294_T053_Id'],
                "T296_C004_Id"                                     => $rowT294['T294_C004_Id'],
                "T296_Codigo_Produto"                              => $rowT294['T294_Codigo_Produto'],
                "T296_Codigo_Produto_Fornecedor"                   => $rowT294['T294_Codigo_Produto_Fornecedor'],
                "T296_Descricao_Produto"                           => $rowT294['T294_Descricao_Produto'],
                "T296_Classificacao_Fiscal"                        => $rowT294['T294_Classificacao_Fiscal'],
                "T296_Prazo_Entrega"                               => $rowT294['T294_Prazo_Entrega'],
                "T296_Quantidade"                                  => $rowT294['T294_Quantidade_PO'],
                "T296_Valor_Custo_Unitario_Anterior"               => $rowT294['T294_Valor_Custo_Unitario_Anterior'],
                "T296_Valor_Custo_Unitario"                        => $rowT294['T294_Valor_Custo_Unitario'],
                "T296_Valor_Total_Custo"                           => $rowT294['T294_Valor_Total_Custo'],
                "T296_Valor_Preco_Unitario_Nota_Fiscal"            => $rowT294['T294_Valor_Preco_Unitario_Nota_Fiscal'],
                "T296_Valor_Total_Preco_Nota_Fiscal"               => $rowT294['T294_Valor_Total_Preco_Nota_Fiscal'],
                "T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal" => $rowT294['T294_Valor_Total_Despesas_Acessorias_Nota_Fiscal'],
                "T296_Valor_Custo_Unitario_Original"               => $rowT294['T294_Valor_Custo_Unitario_Original'],
                "T296_Valor_Total_Custo_Original"                  => $rowT294['T294_Valor_Total_Custo_Original'],
                "T296_Valor_Base_IPI"                              => $rowT294['T294_Valor_Base_IPI'],
                "T296_Valor_Base_ICMS_Sem_Reducao"                 => $rowT294['T294_Valor_Base_ICMS_Sem_Reducao'],
                "T296_Valor_Base_ICMS_Normal"                      => $rowT294['T294_Valor_Base_ICMS_Normal'],
                "T296_Valor_Base_ICMS_Presumido"                   => $rowT294['T294_Valor_Base_ICMS_Presumido'],
                "T296_Valor_Base_COFINS"                           => $rowT294['T294_Valor_Base_COFINS'],
                "T296_Valor_Base_PIS"                              => $rowT294['T294_Valor_Base_PIS'],
                "T296_Valor_Base_Extra"                            => $rowT294['T294_Valor_Base_Extra'],
                "T296_Valor_Base_Imposto_Importacao"               => $rowT294['T294_Valor_Base_Imposto_Importacao'],
                "T296_Aliquota_IPI"                                => $rowT294['T294_Aliquota_IPI'],
                "T296_Aliquota_Imposto_Importacao"                 => $rowT294['T294_Aliquota_Imposto_Importacao'],
                "T296_Aliquota_COFINS"                             => $rowT294['T294_Aliquota_COFINS'],
                "T296_Aliquota_PIS"                                => $rowT294['T294_Aliquota_PIS'],
                "T296_Aliquota_Extra"                              => $rowT294['T294_Aliquota_Extra'],
                "T296_Valor_ICMS"                                  => $rowT294['T294_Valor_ICMS'],
                "T296_Valor_ICMS_Presumido"                        => $rowT294['T294_Valor_ICMS_Presumido'],
                "T296_Valor_ICMS_Pagar"                            => $rowT294['T294_Valor_ICMS_Pagar'],
                "T296_Valor_IPI"                                   => $rowT294['T294_Valor_IPI'],
                "T296_Valor_Imposto_Importacao"                    => $rowT294['T294_Valor_Imposto_Importacao'],
                "T296_Valor_COFINS"                                => $rowT294['T294_Valor_COFINS'],
                "T296_Valor_PIS"                                   => $rowT294['T294_Valor_PIS'],
                "T296_Valor_Extra"                                 => $rowT294['T294_Valor_Extra'],
                "T296_Valor_Frete"                                 => $rowT294['T294_Valor_Frete'],
                "T296_Valor_Despesas"                              => $rowT294['T294_Valor_Despesas'],
                "T296_Valor_Despesas_Fatura"                       => $rowT294['T294_Valor_Despesas_Fatura'],
                "T296_Valor_Aduaneira"                             => $rowT294['T294_Valor_Aduaneira'],
                "T296_Valor_Despesas_Aduaneira"                    => $rowT294['T294_Valor_Despesas_Aduaneira'],
                "T296_Valor_Custo_Final_Unitario"                  => $rowT294['T294_Valor_Custo_Final_Unitario'],
                "T296_Valor_Total_Custo_Final"                     => $rowT294['T294_Valor_Total_Custo_Final'],
                "T296_Peso_Kg_Liquido_Unitario"                    => $rowT294['T294_Peso_Kg_Liquido_Unitario'],
                "T296_Peso_Kg_Liquido_Total"                       => $rowT294['T294_Peso_Kg_Liquido_Total'],
                "T296_Peso_Lb_Liquido_Unitario"                    => $rowT294['T294_Peso_Lb_Liquido_Unitario'],
                "T296_Peso_Lb_Liquido_Total"                       => $rowT294['T294_Peso_Lb_Liquido_Total'],
                "T296_Multiplicador_X"                             => $rowT294['T294_Multiplicador_X'],
                "T296_Multiplicador_Y"                             => $rowT294['T294_Multiplicador_Y'],
                "T296_Data_Transferencia_Estoque"                  => $rowT294['T294_Data_Transferencia_Estoque'],
                "T296_Valor_Total_ICMS"                            => $rowT294['T294_Valor_Total_ICMS'],
                "T296_Valor_Total_ICMS_Presumido"                  => $rowT294['T294_Valor_Total_ICMS_Presumido'],
                "T296_Valor_Total_ICMS_Pagar"                      => $rowT294['T294_Valor_Total_ICMS_Pagar'],
                "T296_Valor_Total_IPI"                             => $rowT294['T294_Valor_Total_IPI'],
                "T296_Valor_Total_Imposto_Importacao"              => $rowT294['T294_Valor_Total_Imposto_Importacao'],
                "T296_Valor_Total_COFINS"                          => $rowT294['T294_Valor_Total_COFINS'],
                "T296_Valor_Total_PIS"                             => $rowT294['T294_Valor_Total_PIS'],
                "T296_Valor_Total_Extra"                           => $rowT294['T294_Valor_Total_Extra'],
                "T296_Valor_Total_Frete"                           => $rowT294['T294_Valor_Total_Frete'],
                "T296_Valor_Total_Despesas"                        => $rowT294['T294_Valor_Total_Despesas'],
                "T296_Valor_Total_Despesas_Fatura"                 => $rowT294['T294_Valor_Total_Despesas_Fatura'],
                "T296_Valor_Total_Aduaneira"                       => $rowT294['T294_Valor_Total_Aduaneira'],
                "T296_Valor_Total_Despesas_Aduaneira"              => $rowT294['T294_Valor_Total_Despesas_Aduaneira'],
                "T296_Valor_Total_Base_IPI"                        => $rowT294['T294_Valor_Total_Base_IPI'],
                "T296_Valor_Total_Base_ICMS_Sem_Reducao"           => $rowT294['T294_Valor_Total_Base_ICMS_Sem_Reducao'],
                "T296_Valor_Total_Base_ICMS_Normal"                => $rowT294['T294_Valor_Total_Base_ICMS_Normal'],
                "T296_Valor_Total_Base_ICMS_Presumido"             => $rowT294['T294_Valor_Total_Base_ICMS_Presumido'],
                "T296_Valor_Total_Base_COFINS"                     => $rowT294['T294_Valor_Total_Base_COFINS'],
                "T296_Valor_Total_Base_PIS"                        => $rowT294['T294_Valor_Total_Base_PIS'],
                "T296_Valor_Total_Base_Extra"                      => $rowT294['T294_Valor_Total_Base_Extra'],
                "T296_Valor_Total_Base_Imposto_Importacao"         => $rowT294['T294_Valor_Total_Base_Imposto_Importacao'],
                "T296_Peso_Kg_Liquido_Unitario_Fatura"             => $rowT294['T294_Peso_Kg_Liquido_Unitario_Fatura'],
                "T296_Peso_Kg_Liquido_Total_Fatura"                => $rowT294['T294_Peso_Kg_Liquido_Total_Fatura'],
                "T296_Valor_Adicao"                                => $rowT294['T294_Valor_Adicao'],
                "T296_Flag_ME"                                     => $rowT294['T294_Flag_ME'],
                "T296_D006_Id"                                     => $rowT294['T294_D006_Id'],
                "T296_Flag_Frete"                                  => $rowT294['T294_Flag_Frete'],
                "T296_Valor_AFRMM"                                 => $rowT294['T294_Valor_AFRMM'],
                "T296_Valor_Total_AFRMM"                           => $rowT294['T294_Valor_Total_AFRMM'],
                "T296_Aliquota_ICMS"                               => $rowT294['T294_Aliquota_ICMS'],
                "T296_Aliquota_ICMS_Presumido"                     => $rowT294['T294_Aliquota_ICMS_Presumido'],
                "T296_Percentual_Reducao_ICMS"                     => $rowT294['T294_Percentual_Reducao_ICMS'],
                "T296_Percentual_Reducao_ICMS_Presumido"           => $rowT294['T294_Percentual_Reducao_ICMS_Presumido'],
                "T296_Aliquota_ICMS_PIS_COFINS"                    => $rowT294['T294_Aliquota_ICMS_PIS_COFINS'],
                "T296_Aliquota_ICMS_Credito_Presumido"             => $rowT294['T294_Aliquota_ICMS_Credito_Presumido'],
                "T296_Adicao_Item"                                 => $rowT294['T294_Adicao_Item'],
                "T296_T225_Id"                                     => $rowT294['T294_T225_Id'],
                "T296_Flag_Calculo_Manual"                         => $rowT294['T294_Flag_Calculo_Manual'],
                "T296_Valor_Total_Multa"                           => $rowT294['T294_Valor_Total_Multa'],
                "T296_Valor_Juros_Multa"                           => $rowT294['T294_Valor_Juros_Multa'],
                "T296_Valor_Dumping"                               => $rowT294['T294_Valor_Dumping'],
                "T296_Valor_Seguro"                                => $rowT294['T294_Valor_Seguro'],
                "T296_Valor_Total_Seguro"                          => $rowT294['T294_Valor_Total_Seguro'],
                "T296_Valor_Custo_Debug"                           => $rowT294['T294_Valor_Custo_Debug'],
                "T296_Valor_Base_II_Debug"                         => $rowT294['T294_Valor_Base_II_Debug'],
                "T296_Cotacao_CC"                                  => $rowT294['T294_Cotacao_CC'],
                "T296_Item_Ac_Drawback"                            => $rowT294['T294_Item_Ac_Drawback'],
                "T296_Unidade_Medida"                              => $rowT294['T294_Unidade_Medida'],
                "T296_T270_Id"                                     => $rowT294['T294_T270_Id'],
                "T296_T066_Id"                                     => $rowT294['T294_T066_Id'],
                "T296_Percentual_Acrescimo_Impostos"               => $rowT294['T294_Percentual_Acrescimo_Impostos'],
                "T296_Quantidade_Saldo"                            => $rowT294['T294_Quantidade_PO'],
            );
            
            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT296 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T296 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            log("vai fazer o insert agora");
            log($sql);
            mysql_query($sql);
            $error = mysql_error();
               
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . $error;
            }

            $T296_Id = $g['mysqlLastId'];

            mysql_query("UPDATE T294 set T294_Quantidade_PO = 0 WHERE T294_Id='{$rowT294['T294_Id']}'");            
            mysql_query("CALL T296_Gravar_Totalizacao('$T296_Id')");            
            log("CALL T296_Gravar_Totalizacao('$T296_Id')");
            $erro = mysql_error();
            if (!empty($erro)) {
                log($erro);
                mysql_query("ROLLBACK");
                return '10 Erro: ' . $erro;
            }
        }            
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        /*
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $sqlT225 = mysql_query("SELECT T225_D009_Id FROM T225 WHERE T225_T224_Id = '{$T224_Id}'");
        while($T225 = mysql_fetch_array($sqlT225)){
            $CAD002->reprocessaOCPedidos($T225['T225_D009_Id']);
        }
        */
        
        return true;
    }


    /**
     * gerarPedido
     *
     * @param int $T293_Id
     */
    public function gerarPedido($T293_Id, $listaT293 = '', $grupo = false)
    {
        global $g;

        // inserir um novo registro na T224 levando: T224_T218_Id, e os dois acima
        if (!$T293_Id) {
            return "ERRO: Não existe o ID da Cotation";
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        //Verifica se recebeu a lista do T293_Id
        $extraWhere = ($listaT293 != '') ? "(T294_T293_Id ='{$T293_Id}' OR T294_T293_Id IN ({$listaT293}))" : "T294_T293_Id ='{$T293_Id}'";

        $T293 = mysql_query("SELECT * 
                               FROM T293 
                          LEFT JOIN D006 ON D006_Id=T293_D006_Id 
                          LEFT JOIN D024 ON D024_Id=T293_D024_Id 
                          LEFT JOIN D020 ON D020_Id=D024_D020_Id 
                              WHERE T293_Id='{$T293_Id}'");
        $mT293 = mysql_fetch_assoc($T293);
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return '1 Erro: ' . $error();
        }                   
                
        if($grupo === false){

            $T295 = mysql_query("SELECT T295_Grupo 
                                   FROM T295
                               ORDER BY T295_Grupo DESC LIMIT 1");
            $mT295 = mysql_fetch_array($T295);
            $grupo = $mT295['T295_Grupo'] + 1;
        }

        $C004 = mysql_query("SELECT C004_Observao_Purchase_Order 
                               FROM C004
                              WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        
        $insertT295 = array(
            "T295_D018_Id"                               => $mT293['T293_D018_Id'],
            "T295_T054_Id"                               => $mT293['T293_T054_Id'],
            "T295_Data_Emissao"                          => $mT293['T293_Data_Emissao'],
            "T295_Numero_DI"                             => $mT293['T293_Numero_DI'],
            "T295_Valor_Produtos"                        => $mT293['T293_Valor_Produtos'],
            "T295_Valor_Frete"                           => $mT293['T293_Valor_Frete'],
            "T295_Valor_CIF"                             => $mT293['T293_Valor_CIF'],
            "T295_Valor_THC"                             => $mT293['T293_Valor_THC'],
            "T295_Valor_SISCOMEX"                        => $mT293['T293_Valor_SISCOMEX'],
            "T295_Valor_Imposto_Importacao"              => $mT293['T293_Valor_Imposto_Importacao'],
            "T295_Valor_IPI"                             => $mT293['T293_Valor_IPI'],
            "T295_Valor_PIS"                             => $mT293['T293_Valor_PIS'],
            "T295_Valor_COFINS"                          => $mT293['T293_Valor_COFINS'],
            "T295_Valor_DI"                              => $mT293['T293_Valor_DI'],
            "T295_Local_Desembaraco"                     => $mT293['T293_Local_Desembaraco'],
            "T295_Data_Desembaraco"                      => $mT293['T293_Data_Desembaraco'],
            "T295_Codigo_Exportador"                     => $mT293['T293_Codigo_Exportador'],
            "T295_D024_Id"                               => $mT293['T293_D024_Id'],
            "T295_D118_Id"                               => $mT293['T293_D118_Id'],
            "T295_D119_Id"                               => $mT293['T293_D119_Id'],
            "T295_Cotacao_ME"                            => $mT293['T293_Cotacao_ME'],
            "T295_Valor_Frete_Incluso"                   => $mT293['T293_Valor_Frete_Incluso'],
            "T295_Valor_ICMS"                            => $mT293['T293_Valor_ICMS'],
            "T295_Valor_Seguro"                          => $mT293['T293_Valor_Seguro'],
            "T295_Valor_Despesa"                         => $mT293['T293_Valor_Despesa'],
            "T295_Observacao_DI"                         => $mT293['T293_Observacao_DI'],
            "T295_Observacao_NF"                         => $mT293['T293_Observacao_NF'],
            "T295_C004_Id"                               => $mT293['T293_C004_Id'],
            "T295_Data_Embarque"                         => $mT293['T293_Data_Embarque'],
            "T295_ETA_Data"                              => $mT293['T293_ETA_Data'],
            "T295_ETA_Hora"                              => $mT293['T293_ETA_Hora'],
            "T295_D044_Id"                               => $mT293['T293_D044_Id'],
            "T295_Aliquota_ICMS"                         => $mT293['T293_Aliquota_ICMS'],
            "T295_Aliquota_ICMS_Presumido"               => $mT293['T293_Aliquota_ICMS_Presumido'],
            "T295_Percentual_Reducao_ICMS"               => $mT293['T293_Percentual_Reducao_ICMS'],
            "T295_Percentual_Reducao_ICMS_Presumido"     => $mT293['T293_Percentual_Reducao_ICMS_Presumido'],
            "T295_Valor_VMLE"                            => $mT293['T293_Valor_VMLE'],
            "T295_Valor_VMLD"                            => $mT293['T293_Valor_VMLD'],
            "T295_Aliquota_ICMS_PIS_COFINS"              => $mT293['T293_Aliquota_ICMS_PIS_COFINS'],
            "T295_D006_Id"                               => $mT293['T293_D006_Id'],
            "T295_Aliquota_ICMS_Credito_Presumido"       => $mT293['T293_Aliquota_ICMS_Credito_Presumido'],
            "T295_Peso_Liquido"                          => $mT293['T293_Peso_Liquido'],
            "T295_Peso_Bruto"                            => $mT293['T293_Peso_Bruto'],
            "T295_C007_Id"                               => $mT293['T293_C007_Id'],
            "T295_Ordem_Compra"                          => $mT293['T293_Ordem_Compra'],
            "T295_Data_Previsao"                         => $mT293['T293_Data_Previsao'],
            "T295_Data_Estoque"                          => $mT293['T293_Data_Estoque'],
            "T295_Valor_AFRMM"                           => $mT293['T293_Valor_AFRMM'],
            "T295_Data_Atualizacao_Custo"                => $mT293['T293_Data_Atualizacao_Custo'],
            "T295_Flag_MP"                               => $mT293['T293_Flag_MP'],
            "T295_Data_Licenca_Importacao"               => $mT293['T293_Data_Licenca_Importacao'],
            "T295_Calculo_THC"                           => $mT293['T293_Calculo_THC'],
            "T295_Somar_PIS_Custo"                       => $mT293['T293_Somar_PIS_Custo'],
            "T295_Somar_COFINS_Custo"                    => $mT293['T293_Somar_COFINS_Custo'],
            "T295_Somar_Seguro_Custo"                    => $mT293['T293_Somar_Seguro_Custo'],
            "T295_Somar_AFRMM_Custo"                     => $mT293['T293_Somar_AFRMM_Custo'],
            "T295_Somar_ICMS_Pagar_Custo"                => $mT293['T293_Somar_ICMS_Pagar_Custo'],
            "T295_Somar_IPI_Custo"                       => $mT293['T293_Somar_IPI_Custo'],
            "T295_UF_Desembaraco"                        => $mT293['T293_UF_Desembaraco'],
            "T295_Codigo_Fabricante"                     => $mT293['T293_Codigo_Fabricante'],
            "T295_Flag_Transporte_DI"                    => $mT293['T293_Flag_Transporte_DI'],
            "T295_Flag_Tipo_Intermedio_DI"               => $mT293['T293_Flag_Tipo_Intermedio_DI'],
            "T295_CNPJ_Importador"                       => $mT293['T293_CNPJ_Importador'],
            "T295_UF_Importador"                         => $mT293['T293_UF_Importador'],
            "T295_Numero_Drawback_Importacao"            => $mT293['T293_Numero_Drawback_Importacao'],
            "T295_Cotacao_CC"                            => $mT293['T293_Cotacao_CC'],
            "T295_Codigo_URF_Entrada"                    => $mT293['T293_Codigo_URF_Entrada'],
            "T295_Codigo_URF_Despacho"                   => $mT293['T293_Codigo_URF_Despacho'],
            "T295_Codigo_Moeda"                          => $mT293['T293_Codigo_Moeda'],
            "T295_Codigo_Incoterms"                      => $mT293['T293_Codigo_Incoterms'],
            "T295_Condicao_Mercadoria"                   => $mT293['T293_Condicao_Mercadoria'],
            "T295_Enquadramento_Material_Usado"          => $mT293['T293_Enquadramento_Material_Usado'],
            "T295_Operacao_Enquadramento_Material_Usado" => $mT293['T293_Operacao_Enquadramento_Material_Usado'],
            "T295_Data_Inicio_Demurrage"                 => $mT293['T293_Data_Inicio_Demurrage'],
            "T295_Siscoserv_RAS"                         => $mT293['T293_Siscoserv_RAS'],
            "T295_Siscoserv_RP"                          => $mT293['T293_Siscoserv_RP'],
            "T295_Orgao_Desembaraco"                     => $mT293['T293_Orgao_Desembaraco'],
            "T295_Grupo"                                 => $grupo,
            "T295_Important_Remark"                      => $mC004['C004_Observao_Purchase_Order']
        );

        $sqlChaves = '';
        $sqlDados = '';
        foreach ($insertT295 as $key => $value) {
            $sqlChaves .= $sqlChaves ? ',' : '';
            $sqlChaves .= $key;
            $sqlDados .= $sqlDados ? ',' : '';
            $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
        }
        $sql = "INSERT INTO T295 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
        mysql_query($sql);

        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return '1 Erro: ' . $error;
        }

        $T295_Id = $g['mysqlLastId'];

        //selecionar todos da T294 onde T294_T293_Id = $T293
        //inserir na T225 todos da T294 selecionados
        //$extraWhere = ($listaT293 != '') ? "(T294_T293_Id ='{$T293_Id}' OR T294_T293_Id IN ({$listaT293}))" : "T294_T293_Id ='{$T293_Id}'";
        
        $T294 = mysql_query("SELECT *
                               FROM T294
                          LEFT JOIN T293 ON T294_T293_Id=T293_Id
                          LEFT JOIN D009 ON D009_Id=T294_D009_Id
                          LEFT JOIN D005 on D005_Id=T294_D005_Id
                          LEFT JOIN T004 ON T004_Id=T294_T004_Id
                          LEFT JOIN T003 ON T003_Id=T004_T003_Id
                          LEFT JOIN T006 ON T004_Id=T006_T004_Id
                              WHERE T294_Quantidade_PO > 0
                                AND {$extraWhere}");
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return '8 Erro: ' . $error();
        }

        // vai verifivar se a contation está vinculada a um orçamento e o mesmo ja gerou pedido                   
        $temPedido = true;
        $msg = 'Cotations vinculada a um orçamento, é necessário a geração do pedido para gerar a P.O<br><br>';  
                    
        while ($rowT294 = mysql_fetch_assoc($T294)) {
            
            if($rowT294['T294_Quantidade_PO'] == 0){
                continue;
            }    

            if($rowT294['T294_T004_Id'] > 0 && ($rowT294['T006_T004_Id'] <= 0 || empty($rowT294['T006_T004_Id']))){
                $temPedido = false;
                $msg .= "<b>Cotation {$rowT294['T293_Id']}</b> - Código {$rowT294['T294_Codigo_Produto']} está vinculado ao orçamento {$rowT294['T003_Id']}<br>";
                continue;
            }
    
            $insertT296 = array(
                "T296_T295_Id"                                     => $T295_Id,
                "T296_T294_Id"                                     => $rowT294['T294_Id'],
                "T296_T060_Id"                                     => $rowT294['T294_T060_Id'],
                "T296_T054_Id"                                     => $rowT294['T294_T054_Id'],
                "T296_D037_Id"                                     => $rowT294['T294_D037_Id'],
                "T296_D005_Id"                                     => $rowT294['T294_D005_Id'],
                "T296_D009_Id"                                     => $rowT294['T294_D009_Id'],
                "T296_T053_Id"                                     => $rowT294['T294_T053_Id'],
                "T296_C004_Id"                                     => $rowT294['T294_C004_Id'],
                "T296_Codigo_Produto"                              => $rowT294['T294_Codigo_Produto'],
                "T296_Codigo_Produto_Fornecedor"                   => $rowT294['T294_Codigo_Produto_Fornecedor'],
                "T296_Descricao_Produto"                           => $rowT294['T294_Descricao_Produto'],
                "T296_Classificacao_Fiscal"                        => $rowT294['T294_Classificacao_Fiscal'],
                "T296_Prazo_Entrega"                               => $rowT294['T294_Prazo_Entrega'],
                "T296_Quantidade"                                  => $rowT294['T294_Quantidade_PO'],
                "T296_Valor_Custo_Unitario_Anterior"               => $rowT294['T294_Valor_Custo_Unitario_Anterior'],
                "T296_Valor_Custo_Unitario"                        => $rowT294['T294_Valor_Custo_Unitario'],
                "T296_Valor_Total_Custo"                           => $rowT294['T294_Valor_Total_Custo'],
                "T296_Valor_Preco_Unitario_Nota_Fiscal"            => $rowT294['T294_Valor_Preco_Unitario_Nota_Fiscal'],
                "T296_Valor_Total_Preco_Nota_Fiscal"               => $rowT294['T294_Valor_Total_Preco_Nota_Fiscal'],
                "T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal" => $rowT294['T294_Valor_Total_Despesas_Acessorias_Nota_Fiscal'],
                "T296_Valor_Custo_Unitario_Original"               => $rowT294['T294_Valor_Custo_Unitario_Original'],
                "T296_Valor_Total_Custo_Original"                  => $rowT294['T294_Valor_Total_Custo_Original'],
                "T296_Valor_Base_IPI"                              => $rowT294['T294_Valor_Base_IPI'],
                "T296_Valor_Base_ICMS_Sem_Reducao"                 => $rowT294['T294_Valor_Base_ICMS_Sem_Reducao'],
                "T296_Valor_Base_ICMS_Normal"                      => $rowT294['T294_Valor_Base_ICMS_Normal'],
                "T296_Valor_Base_ICMS_Presumido"                   => $rowT294['T294_Valor_Base_ICMS_Presumido'],
                "T296_Valor_Base_COFINS"                           => $rowT294['T294_Valor_Base_COFINS'],
                "T296_Valor_Base_PIS"                              => $rowT294['T294_Valor_Base_PIS'],
                "T296_Valor_Base_Extra"                            => $rowT294['T294_Valor_Base_Extra'],
                "T296_Valor_Base_Imposto_Importacao"               => $rowT294['T294_Valor_Base_Imposto_Importacao'],
                "T296_Aliquota_IPI"                                => $rowT294['T294_Aliquota_IPI'],
                "T296_Aliquota_Imposto_Importacao"                 => $rowT294['T294_Aliquota_Imposto_Importacao'],
                "T296_Aliquota_COFINS"                             => $rowT294['T294_Aliquota_COFINS'],
                "T296_Aliquota_PIS"                                => $rowT294['T294_Aliquota_PIS'],
                "T296_Aliquota_Extra"                              => $rowT294['T294_Aliquota_Extra'],
                "T296_Valor_ICMS"                                  => $rowT294['T294_Valor_ICMS'],
                "T296_Valor_ICMS_Presumido"                        => $rowT294['T294_Valor_ICMS_Presumido'],
                "T296_Valor_ICMS_Pagar"                            => $rowT294['T294_Valor_ICMS_Pagar'],
                "T296_Valor_IPI"                                   => $rowT294['T294_Valor_IPI'],
                "T296_Valor_Imposto_Importacao"                    => $rowT294['T294_Valor_Imposto_Importacao'],
                "T296_Valor_COFINS"                                => $rowT294['T294_Valor_COFINS'],
                "T296_Valor_PIS"                                   => $rowT294['T294_Valor_PIS'],
                "T296_Valor_Extra"                                 => $rowT294['T294_Valor_Extra'],
                "T296_Valor_Frete"                                 => $rowT294['T294_Valor_Frete'],
                "T296_Valor_Despesas"                              => $rowT294['T294_Valor_Despesas'],
                "T296_Valor_Despesas_Fatura"                       => $rowT294['T294_Valor_Despesas_Fatura'],
                "T296_Valor_Aduaneira"                             => $rowT294['T294_Valor_Aduaneira'],
                "T296_Valor_Despesas_Aduaneira"                    => $rowT294['T294_Valor_Despesas_Aduaneira'],
                "T296_Valor_Custo_Final_Unitario"                  => $rowT294['T294_Valor_Custo_Final_Unitario'],
                "T296_Valor_Total_Custo_Final"                     => $rowT294['T294_Valor_Total_Custo_Final'],
                "T296_Peso_Kg_Liquido_Unitario"                    => $rowT294['T294_Peso_Kg_Liquido_Unitario'],
                "T296_Peso_Kg_Liquido_Total"                       => $rowT294['T294_Peso_Kg_Liquido_Total'],
                "T296_Peso_Lb_Liquido_Unitario"                    => $rowT294['T294_Peso_Lb_Liquido_Unitario'],
                "T296_Peso_Lb_Liquido_Total"                       => $rowT294['T294_Peso_Lb_Liquido_Total'],
                "T296_Multiplicador_X"                             => $rowT294['T294_Multiplicador_X'],
                "T296_Multiplicador_Y"                             => $rowT294['T294_Multiplicador_Y'],
                "T296_Data_Transferencia_Estoque"                  => $rowT294['T294_Data_Transferencia_Estoque'],
                "T296_Valor_Total_ICMS"                            => $rowT294['T294_Valor_Total_ICMS'],
                "T296_Valor_Total_ICMS_Presumido"                  => $rowT294['T294_Valor_Total_ICMS_Presumido'],
                "T296_Valor_Total_ICMS_Pagar"                      => $rowT294['T294_Valor_Total_ICMS_Pagar'],
                "T296_Valor_Total_IPI"                             => $rowT294['T294_Valor_Total_IPI'],
                "T296_Valor_Total_Imposto_Importacao"              => $rowT294['T294_Valor_Total_Imposto_Importacao'],
                "T296_Valor_Total_COFINS"                          => $rowT294['T294_Valor_Total_COFINS'],
                "T296_Valor_Total_PIS"                             => $rowT294['T294_Valor_Total_PIS'],
                "T296_Valor_Total_Extra"                           => $rowT294['T294_Valor_Total_Extra'],
                "T296_Valor_Total_Frete"                           => $rowT294['T294_Valor_Total_Frete'],
                "T296_Valor_Total_Despesas"                        => $rowT294['T294_Valor_Total_Despesas'],
                "T296_Valor_Total_Despesas_Fatura"                 => $rowT294['T294_Valor_Total_Despesas_Fatura'],
                "T296_Valor_Total_Aduaneira"                       => $rowT294['T294_Valor_Total_Aduaneira'],
                "T296_Valor_Total_Despesas_Aduaneira"              => $rowT294['T294_Valor_Total_Despesas_Aduaneira'],
                "T296_Valor_Total_Base_IPI"                        => $rowT294['T294_Valor_Total_Base_IPI'],
                "T296_Valor_Total_Base_ICMS_Sem_Reducao"           => $rowT294['T294_Valor_Total_Base_ICMS_Sem_Reducao'],
                "T296_Valor_Total_Base_ICMS_Normal"                => $rowT294['T294_Valor_Total_Base_ICMS_Normal'],
                "T296_Valor_Total_Base_ICMS_Presumido"             => $rowT294['T294_Valor_Total_Base_ICMS_Presumido'],
                "T296_Valor_Total_Base_COFINS"                     => $rowT294['T294_Valor_Total_Base_COFINS'],
                "T296_Valor_Total_Base_PIS"                        => $rowT294['T294_Valor_Total_Base_PIS'],
                "T296_Valor_Total_Base_Extra"                      => $rowT294['T294_Valor_Total_Base_Extra'],
                "T296_Valor_Total_Base_Imposto_Importacao"         => $rowT294['T294_Valor_Total_Base_Imposto_Importacao'],
                "T296_Peso_Kg_Liquido_Unitario_Fatura"             => $rowT294['T294_Peso_Kg_Liquido_Unitario_Fatura'],
                "T296_Peso_Kg_Liquido_Total_Fatura"                => $rowT294['T294_Peso_Kg_Liquido_Total_Fatura'],
                "T296_Valor_Adicao"                                => $rowT294['T294_Valor_Adicao'],
                "T296_Flag_ME"                                     => $rowT294['T294_Flag_ME'],
                "T296_D006_Id"                                     => $rowT294['T294_D006_Id'],
                "T296_Flag_Frete"                                  => $rowT294['T294_Flag_Frete'],
                "T296_Valor_AFRMM"                                 => $rowT294['T294_Valor_AFRMM'],
                "T296_Valor_Total_AFRMM"                           => $rowT294['T294_Valor_Total_AFRMM'],
                "T296_Aliquota_ICMS"                               => $rowT294['T294_Aliquota_ICMS'],
                "T296_Aliquota_ICMS_Presumido"                     => $rowT294['T294_Aliquota_ICMS_Presumido'],
                "T296_Percentual_Reducao_ICMS"                     => $rowT294['T294_Percentual_Reducao_ICMS'],
                "T296_Percentual_Reducao_ICMS_Presumido"           => $rowT294['T294_Percentual_Reducao_ICMS_Presumido'],
                "T296_Aliquota_ICMS_PIS_COFINS"                    => $rowT294['T294_Aliquota_ICMS_PIS_COFINS'],
                "T296_Aliquota_ICMS_Credito_Presumido"             => $rowT294['T294_Aliquota_ICMS_Credito_Presumido'],
                "T296_Adicao_Item"                                 => $rowT294['T294_Adicao_Item'],
                "T296_T225_Id"                                     => $rowT294['T294_T225_Id'],
                "T296_Flag_Calculo_Manual"                         => $rowT294['T294_Flag_Calculo_Manual'],
                "T296_Valor_Total_Multa"                           => $rowT294['T294_Valor_Total_Multa'],
                "T296_Valor_Juros_Multa"                           => $rowT294['T294_Valor_Juros_Multa'],
                "T296_Valor_Dumping"                               => $rowT294['T294_Valor_Dumping'],
                "T296_Valor_Seguro"                                => $rowT294['T294_Valor_Seguro'],
                "T296_Valor_Total_Seguro"                          => $rowT294['T294_Valor_Total_Seguro'],
                "T296_Valor_Custo_Debug"                           => $rowT294['T294_Valor_Custo_Debug'],
                "T296_Valor_Base_II_Debug"                         => $rowT294['T294_Valor_Base_II_Debug'],
                "T296_Cotacao_CC"                                  => $rowT294['T294_Cotacao_CC'],
                "T296_Item_Ac_Drawback"                            => $rowT294['T294_Item_Ac_Drawback'],
                "T296_Unidade_Medida"                              => $rowT294['T294_Unidade_Medida'],
                "T296_T270_Id"                                     => $rowT294['T294_T270_Id'],
                "T296_T066_Id"                                     => $rowT294['T294_T066_Id'],
                "T296_Percentual_Acrescimo_Impostos"               => $rowT294['T294_Percentual_Acrescimo_Impostos'],
                "T296_Quantidade_Saldo"                            => $rowT294['T294_Quantidade_PO'],
            );
            
            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT296 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T296 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);
            $error = mysql_error();
                
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . $error;
            }

            $T296_Id = $g['mysqlLastId'];
            mysql_query("UPDATE T294 SET T294_Quantidade_PO = 0 WHERE T294_Id='{$rowT294['T294_Id']}'");

            mysql_query("CALL T296_Gravar_Totalizacao('$T296_Id')");
            $erro = mysql_error();
            if (!empty($erro)) {
                log($erro);
                mysql_query("ROLLBACK");
                return '10 Erro: ' . $erro;
            }  
        }
        
        if($temPedido === false){
            mysql_query("ROLLBACK");
            return $msg;
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        return true;        
    }
 
    /**
     * insereItemEstoque
     *
     * @param int $T293_Id
     * @param string $extra
     */
    public function insereItemEstoque($T293_Id, $extra)
    {
        global $g;
        $T293 = mysql_query("SELECT T293_D024_Id FROM T293 WHERE T293_Id = '{$T293_Id}'");
        $mT293 = mysql_fetch_array($T293);

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id='{$g['empresaAtual']}' AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));

        $D009 = mysql_query("SELECT D001_Codigo_Produto,
                                    D083_Codigo_Produto_Fornecedor,
                                    D001_Descricao_Produto,
                                    D001_Descricao_Ingles,
                                    D009_Quantidade_Comprar_Calculada,
                                    D009_Quantidade_Comprar,
                                    D009_Id,
                                    D041_Valor_Base,
                                    D009_C004_Id,
                                    D005_Id,
                                    D037_Id
                               FROM D009 
                          LEFT JOIN T187 ON T187_D009_Id=D009_Id 
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                          LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                          LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$mT293['T293_D024_Id']}'
                          LEFT JOIN D037 ON D037_Id=D001_D037_Id 
                          LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                          LEFT JOIN D003 ON D003_Id=D001_D003_Id 
                          LEFT JOIN D060 ON D001_D060_Id=D060_Id 
                          LEFT JOIN D002 ON D001_D002_Id=D002_Id 
                          LEFT JOIN D015 ON D001_D015_Id=D015_Id 
                          LEFT JOIN T001 ON T001_Id=D009_T001_Id 
                          LEFT JOIN D024 AS ultimaCompra ON ultimaCompra.D024_Id = T001_D024_Id
                          LEFT JOIN D041 ON D041_D049_Id=D049_Id
                          LEFT JOIN D005 ON D005_Id=D001_D005_Id
                          LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
                          LEFT JOIN T241 ON T241_D001_Id=D001_Id
                          LEFT JOIN D024 AS fornecedor ON fornecedor.D024_Id=T241_D024_Id
                              WHERE {$extra}
                           GROUP BY D009_Id");

        $erro = mysql_error();
        if(!empty($erro)){
            mysql_query("ROLLBACK");
            return "Erro1: " . $erro;
        }
        
        while ($mD009 = mysql_fetch_array($D009)) {
            
            $QuantidadeComprar = ($mD009['D009_Quantidade_Comprar'] > 0) ? $mD009['D009_Quantidade_Comprar'] : $mD009['D009_Quantidade_Comprar_Calculada'];

            $sqlInsert = "INSERT INTO T294 ";                                   
            $sqlInsert .= "(T294_T293_Id, ";                                    #1
            $sqlInsert .= "T294_C004_Id, ";                                     #2
            $sqlInsert .= "T294_Codigo_Produto, ";                              #3
            $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";                   #4
            $sqlInsert .= "T294_Descricao_Produto, ";                           #5
            $sqlInsert .= "T294_Quantidade, ";                                  #6
            $sqlInsert .= "T294_Valor_Custo_Unitario,  ";                       #7
            $sqlInsert .= "T294_D009_Id, ";                                     #8
            $sqlInsert .= "T294_D037_Id, ";                                     #9
            $sqlInsert .= "T294_D005_Id) ";                                     #10
            $sqlInsert .= "VALUES  ";
            $sqlInsert .= "('{$T293_Id}', ";                                    #1
            $sqlInsert .= "'{$mD009['D009_C004_Id']}', ";                       #2
            $sqlInsert .= "'{$mD009['D001_Codigo_Produto']}', ";                #3
            $sqlInsert .= "'{$mD009['D083_Codigo_Produto_Fornecedor']}', ";     #4
            $sqlInsert .= "'{$mD009['D001_Descricao_Ingles']}', ";              #5
            $sqlInsert .= "'{$QuantidadeComprar}', ";                           #6
            $sqlInsert .= "'{$mD009['D041_Valor_Base']}', ";                    #7
            $sqlInsert .= "'{$mD009['D009_Id']}', ";                            #8
            $sqlInsert .= "'{$mD009['D037_Id']}', ";                            #9
            $sqlInsert .= "'{$mD009['D005_Id']}' ";                             #10
            $sqlInsert .= "); ";

            mysql_query($sqlInsert);
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }
    
            mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N', D009_C007_Id_Comprar = '0', D009_Quantidade_Comprar = '0' WHERE D009_Id = '{$mD009['D009_Id']}'");
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro3: " . $erro;
            } 
        }
        
        return true;
    }

    /**
     * gerarCotacaoEstoqueAutomatica
     *
     * @param string $extra
     * @return string/array
     */
    public function gerarCotacaoEstoqueAutomatica($extra)
    {
        global $g;
        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "D009_Flag_Cotar='S' and D009_C004_Id='{$g['empresaAtual']}' AND D009_C007_Id_Comprar = '{$g['usuarioAtual']}'"));
        
        $countErro = 0;
        $arrayD024 = array();
        $sql = "SELECT fornecedor.D024_Id AS fornecedorD024_Id, 
                       GROUP_CONCAT(DISTINCT D009_Id SEPARATOR ', ') AS D009_Ids, 
                       COUNT(DISTINCT D009_Id) AS count 
                  FROM D009 
             LEFT JOIN T187 ON T187_D009_Id=D009_Id 
             LEFT JOIN D049 ON D049_Id=D009_D049_Id 
             LEFT JOIN D001 ON D001_Id=D049_D001_Id 
             LEFT JOIN D082 ON D082_Id=D049_D082_Id 
             LEFT JOIN D083 ON D083_D049_Id=D049_Id 
             LEFT JOIN D037 ON D037_Id=D001_D037_Id 
             LEFT JOIN C008 ON C008_Id=D001_C008_Id 
             LEFT JOIN D003 ON D003_Id=D001_D003_Id 
             LEFT JOIN D060 ON D001_D060_Id=D060_Id 
             LEFT JOIN D002 ON D001_D002_Id=D002_Id 
             LEFT JOIN D015 ON D001_D015_Id=D015_Id 
             LEFT JOIN T001 ON T001_Id=D009_T001_Id
             left join D024 AS ultimaCompra ON ultimaCompra.D024_Id = T001_D024_Id 
             LEFT JOIN D041 ON D041_D049_Id=D049_Id
             LEFT JOIN D005 ON D005_Id=D001_D005_Id
             LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
             LEFT JOIN T241 ON T241_D001_Id=D001_Id
             LEFT JOIN D024 AS fornecedor ON fornecedor.D024_Id=T241_D024_Id
                 WHERE {$extra}";

        $sql = explode("LIMIT", $sql);
        $sql = $sql[0];
        $sql .= " GROUP BY fornecedor.D024_Id";

        $retorno = mysql_query($sql);
        while($row = mysql_fetch_array($retorno)){
            if(empty($row['fornecedorD024_Id']) || $row['fornecedorD024_Id'] == 0){
                $countErro = $row['count'];
            }
            $arrayD024[$row['fornecedorD024_Id']] = $row['D009_Ids'];
        }

        if($countErro > 0){
            if($countErro == 1){
                return "Existe um produto sem fornecedor.";
            } else {
                return "Existem {$countErro} produtos sem fornecedor.";
            }
        }

        $T293 = mysql_query("SELECT T293_Grupo 
                               FROM T293
                           ORDER BY T293_Grupo DESC LIMIT 1");

        $mT293 = mysql_fetch_array($T293);
        $grupo = $mT293['T293_Grupo'] + 1;

        $C004 = mysql_query("SELECT C004_Observao_Cotation 
                               FROM C004
                              WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        foreach ($arrayD024 as $D024_Id => $D009_Ids) {
          
            // faz o insert do cabeçalho
            mysql_query("INSERT INTO T293 (T293_D024_Id, 
                                           T293_C004_Id, 
                                           T293_C007_Id, 
                                           T293_Data_Emissao,
                                           T293_Grupo,
                                           T293_Important_Remark                                           
                                ) VALUES (
                                          '{$D024_Id}', 
                                          '{$g['empresaAtual']}',
                                          '{$g['usuarioAtual']}', 
                                          CURDATE(),
                                          '{$grupo}',
                                          '{$mC004['C004_Observao_Cotation']}')");    
            
            $erro = mysql_error();
            if(!empty($erro)){
                mysql_query("ROLLBACK");
                return "Erro: " . $erro;
            }

            $T293_Id = $g['mysqlLastId'];

            // busca os produtos para inserir no T294
            $D009 = mysql_query("SELECT D001_Codigo_Produto,
                                        D001_Descricao_Produto,
                                        D001_Descricao_Ingles,
                                        D009_Quantidade_Comprar_Calculada,
                                        D009_Quantidade_Comprar,
                                        D009_Id,
                                        D041_Valor_Base,
                                        D009_C004_Id,
                                        D083_Codigo_Produto_Fornecedor,
                                        D037_Id,
                                        D005_Id
                                   FROM D009 
                              LEFT JOIN T187 ON T187_D009_Id=D009_Id 
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                              LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                              LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                              LEFT JOIN D037 ON D037_Id=D001_D037_Id 
                              LEFT JOIN C008 ON C008_Id=D001_C008_Id 
                              LEFT JOIN D003 ON D003_Id=D001_D003_Id 
                              LEFT JOIN D060 ON D001_D060_Id=D060_Id 
                              LEFT JOIN D002 ON D001_D002_Id=D002_Id 
                              LEFT JOIN D015 ON D001_D015_Id=D015_Id 
                              LEFT JOIN T001 ON T001_Id=D009_T001_Id 
                              LEFT JOIN D024 ON D024_Id=T001_D024_Id
                              LEFT JOIN D041 ON D041_D049_Id=D049_Id
                              LEFT JOIN D005 ON D005_Id=D001_D005_Id
                              LEFT JOIN C007 ON C007_Id=D009_C007_Id_Comprar
                              LEFT JOIN D083 ON D001_Id=D083_D001_Id AND D083_D024_Id = '{$D024_Id}'
                                  WHERE D009_Id IN ({$D009_Ids})
                               GROUP BY D009_Id");
            
            while ($mD009 = mysql_fetch_array($D009)) {
                
                $QuantidadeComprar = ($mD009['D009_Quantidade_Comprar'] > 0) ? $mD009['D009_Quantidade_Comprar'] : $mD009['D009_Quantidade_Comprar_Calculada'];
                if($QuantidadeComprar < 0){
                    $QuantidadeComprar = 0;
                }

                $mD009['D009_C004_Id']                   = mysql_real_escape_string($mD009['D009_C004_Id']);
                $mD009['D001_Codigo_Produto']            = mysql_real_escape_string($mD009['D001_Codigo_Produto']);
                $mD009['D083_Codigo_Produto_Fornecedor'] = mysql_real_escape_string($mD009['D083_Codigo_Produto_Fornecedor']);
                $mD009['D001_Descricao_Ingles']          = mysql_real_escape_string($mD009['D001_Descricao_Ingles']);
                $mD009['D041_Valor_Base']                = mysql_real_escape_string($mD009['D041_Valor_Base']);
                $mD009['D009_Id']                        = mysql_real_escape_string($mD009['D009_Id']);
                $mD009['D037_Id']                        = mysql_real_escape_string($mD009['D037_Id']);
                $mD009['D005_Id']                        = mysql_real_escape_string($mD009['D005_Id']);

                $sqlInsert = "INSERT INTO T294 ";                                   
                $sqlInsert .= "(T294_T293_Id, ";                                    #1
                $sqlInsert .= "T294_C004_Id, ";                                     #2
                $sqlInsert .= "T294_Codigo_Produto, ";                              #3
                $sqlInsert .= "T294_Codigo_Produto_Fornecedor, ";                   #4
                $sqlInsert .= "T294_Descricao_Produto, ";                           #5
                $sqlInsert .= "T294_Quantidade, ";                                  #6
                $sqlInsert .= "T294_Valor_Custo_Unitario,  ";                       #7
                $sqlInsert .= "T294_D009_Id, ";                                     #8
                $sqlInsert .= "T294_D037_Id, ";                                     #9
                $sqlInsert .= "T294_D005_Id) ";                                     #10
                $sqlInsert .= "VALUES  ";
                $sqlInsert .= "('{$T293_Id}', ";                                    #1
                $sqlInsert .= "'{$mD009['D009_C004_Id']}', ";                       #2
                $sqlInsert .= "'{$mD009['D001_Codigo_Produto']}', ";                #3
                $sqlInsert .= "'{$mD009['D083_Codigo_Produto_Fornecedor']}', ";     #4
                $sqlInsert .= "'{$mD009['D001_Descricao_Ingles']}', ";              #5
                $sqlInsert .= "'{$QuantidadeComprar}', ";                           #6
                $sqlInsert .= "'{$mD009['D041_Valor_Base']}', ";                    #7
                $sqlInsert .= "'{$mD009['D009_Id']}', ";                            #8
                $sqlInsert .= "'{$mD009['D037_Id']}', ";                            #9
                $sqlInsert .= "'{$mD009['D005_Id']}' ";                             #10
                $sqlInsert .= "); ";

                mysql_query($sqlInsert);
                $erro = mysql_error();
                if(!empty($erro)){
                    mysql_query("ROLLBACK");
                    return "Erro: " . $erro;
                }
                
                mysql_query("UPDATE D009 SET D009_Flag_Cotar = 'N', D009_C007_Id_Comprar = '0', D009_Quantidade_Comprar = '0' WHERE D009_Id = '{$mD009['D009_Id']}'");
            }
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return array('grupo' => $grupo);
    }
}
