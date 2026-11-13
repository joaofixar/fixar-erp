<?php
namespace hardness;

definirClasse('VEN015');
class VEN015_
{
    /**
     * Importa o DAV para dentro de uma venda PDV
     *
     * @param int $T005_Id
     * @param int $T007_Id
     * 
     * return true/string(erro)
     */
    public function importarDAV($T005_Id = 0, $T007_Id = 0) {
        global $g;

        require_once('bibliotecas/classes/VEN003.php');
        $VEN003 = new VEN003();

        if ($T005_Id == 0 || $T007_Id == 0) {
            return "Parâmetros número DAV ou número NFC-e não recebidos, informe suporte técnico ";
        }

        $T005  = mysql_query("SELECT T005_Id,
                                     T005_Flag_ACP,
                                     T005_Observacao,
                                     T005_Valor_Desconto_Manual,
                                     T007_Numero_Nota_Fiscal,
                                     T005_Flag_Status,
                                     T005_C007_Id_Vendedor_Interno
                                FROM T005
                           LEFT JOIN T007 ON T007_T005_Id=T005_Id AND T007_Flag_Cancelada != 'S'
                               WHERE T005_Id='{$T005_Id}'");  
        $mT005 = mysql_fetch_array($T005);

        // CARLOS CARVALHO
        $vendedorInternoT005 = $mT005['T005_C007_Id_Vendedor_Interno'];

        if (empty($mT005['T005_Id']))  {
            return "O DAV número {$T005_Id} não existe";
        }

        if ($mT005['T005_Flag_ACP'] != '8') {
            return "O pedido {$T005_Id} não é DAV (tipo diferente de NFC-e)";
        }

        if (!empty($mT005['T007_Numero_Nota_Fiscal'])) {
            return "Este DAV já está vinculado a nota fiscal número {$mT005['T007_Numero_Nota_Fiscal']}";
        }

        if ($mT005['T005_Flag_Status'] == '8') {
            return "O pedido {$T005_Id} está cancelado";
        }

        if (($mT005['T005_Flag_Status'] !='1' && $mT005['T005_Flag_Status'] !='2' && $mT005['T005_Flag_Status'] !='10' && $mT005['T005_Flag_Status'] !='4') && $g['C031']['permiteImportarDavQualquerStatus'] == 'N') {
            return "O pedido {$T005_Id} não foi faturado, favor verificar";
        }

        if($g['C031']['permiteImportarVariasDavPdv'] == 'N'){
            $T008  = mysql_query("SELECT T006_T005_Id
                                    FROM T008
                               LEFT JOIN T006 ON T006_Id = T008_T006_Id
                                   WHERE T008_T007_Id = '{$T007_Id}'
                                     AND T008_T006_Id > 0"); 

            if(mysql_num_rows($T008) > 0){
                $mT008 = mysql_fetch_array($T008);
                return "DAV {$mT008['T006_T005_Id']} já vinculada a esta NFC-e, processo não pode ser realizado";
            }
        }

        $T007  = mysql_query("SELECT T007_Id,
                                     T007_Numero_Nota_Fiscal,
                                     T007_Observacao_Nota_Fiscal_1
                                FROM T007
                               WHERE T007_Id = '{$T007_Id}'");  
        $mT007 = mysql_fetch_array($T007);

        $T006 = mysql_query("SELECT T006_Id,
                                    T006_D009_Id,
                                    T006_Codigo_Produto,
                                    T006_Descricao_Produto,
                                    T006_Quantidade,
                                    T006_Valor_Preco_Sem_Desconto_Unitario,
                                    T006_D059_Id,
                                    T006_D006_Id,
                                    T006_T066_Id,
                                    T006_D005_Id,
                                    T006_D037_Id,
                                    D005_D037_Id_Tributavel,
                                    T006_Peso_Unitario,
                                    T006_Percentual_Desconto,
                                    T006_Valor_Preco_Original,
                                    T006_Peso_Total,
                                    T006_Peso_Bruto,
                                    T006_Peso_Bruto_Total,
                                    T006_Situacao_Tributaria_IPI,
                                    T006_Situacao_Tributaria_PIS,
                                    T006_Situacao_Tributaria_COFINS,
                                    T006_Flag_Isento_IRPJ,
                                    T006_Flag_Isento_CSLL,
                                    T006_Flag_Isento_PIS,
                                    T006_Flag_Isento_COFINS,
                                    T006_Valor_Base_PIS,
                                    T006_Valor_Base_COFINS,
                                    D049_Origem_Mercadoria,
                                    D059_Codigo_ST,
                                    D005_Classificacao_Fiscal,
                                    T006_Modalidade_ICMS,
                                    T006_Modalidade_ICMS_Substituicao,
                                    D037_Unidade,
                                    T006_Observacao,
                                    T006_Valor_Custo_Unitario
                               FROM T006
                          LEFT JOIN T008 ON T006_Id = T008_T006_Id
                          LEFT JOIN T007 ON T007_Id = T008_T007_Id
                          LEFT JOIN D037 ON D037_Id = T006_D037_Id
                          LEFT JOIN D006 ON D006_Id = T006_D006_Id
                          LEFT JOIN D059 ON D059_Id = D006_D059_Id
                          LEFT JOIN D009 ON D009_Id = T006_D009_Id
                          LEFT JOIN D049 ON D049_Id = D009_D049_Id
                          LEFT JOIN D005 ON D005_Id = T006_D005_Id
                              WHERE T006_T005_Id='{$T005_Id}'
                                AND (T008_Id IS NULL OR T007_Flag_Cancelada = 'S')");

        $erro = mysql_error();
        if (!empty($erro)){
            return 'Erro sql: ' . $erro;
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        while ($mT006 = mysql_fetch_assoc($T006)) {
            $Flag_Situacao_Tributaria = $mT006['D049_Origem_Mercadoria'] . $mT006['D059_Codigo_ST'];
            $Observacao               = !empty($mT006['T006_Observacao']) ? " - " . $mT006['T006_Observacao'] : "";
            $Descricao                = $mT006['T006_Descricao_Produto'] . $Observacao;

            mysql_query("INSERT INTO T008 (
                                    T008_T007_Id,
                                    T008_C004_Id,
                                    T008_T006_Id,
                                    T008_D009_Id,
                                    T008_Codigo_Produto,
                                    T008_Descricao_Produto,
                                    T008_Quantidade,
                                    T008_Unidade,
                                    T008_D059_Id,
                                    T008_D037_Id,
                                    T008_D037_Id_Tributavel,
                                    T008_D006_Id,
                                    T008_T066_Id,
                                    T008_D005_Id,
                                    T008_Peso_Unitario,
                                    T008_Peso_Total,
                                    T008_Peso_Bruto,
                                    T008_Peso_Bruto_Total,
                                    T008_Valor_Preco_Sem_Desconto_Unitario,
                                    T008_Flag_Isento_IRPJ,
                                    T008_Flag_Isento_CSLL,
                                    T008_Flag_Isento_PIS,
                                    T008_Flag_Isento_COFINS,
                                    T008_Situacao_Tributaria_IPI,
                                    T008_Situacao_Tributaria_PIS,
                                    T008_Situacao_Tributaria_COFINS,
                                    T008_Valor_Base_PIS,
                                    T008_Valor_Base_COFINS,
                                    T008_Classificacao_Fiscal,
                                    T008_Modalidade_ICMS,
                                    T008_Modalidade_ICMS_Substituicao,
                                    T008_Flag_Situacao_Tributaria,
                                    T008_Data_Emissao,
                                    T008_Flag_Classificacao,
                                    T008_Flag_Calculo_Manual,
                                    T008_Flag_Entrada_Saida,
                                    T008_Percentual_Desconto,
                                    T008_Valor_Custo_Unitario,
                                    T008_Valor_Preco_Original)
                                VALUES (
                                    '{$T007_Id}',
                                    '{$g['empresaAtual']}',
                                    '{$mT006['T006_Id']}',
                                    '{$mT006['T006_D009_Id']}',
                                    '{$mT006['T006_Codigo_Produto']}',
                                    '{$Descricao}',
                                    '{$mT006['T006_Quantidade']}',
                                    '{$mT006['D037_Unidade']}',
                                    '{$mT006['T006_D059_Id']}',
                                    '{$mT006['T006_D037_Id']}',
                                    '{$mT006['D005_D037_Id_Tributavel']}',
                                    '{$mT006['T006_D006_Id']}',
                                    '{$mT006['T006_T066_Id']}',
                                    '{$mT006['T006_D005_Id']}',
                                    '{$mT006['T006_Peso_Unitario']}',
                                    '{$mT006['T006_Peso_Total']}',
                                    '{$mT006['T006_Peso_Bruto']}',
                                    '{$mT006['T006_Peso_Bruto_Total']}',
                                    '{$mT006['T006_Valor_Preco_Sem_Desconto_Unitario']}',
                                    '{$mT006['T006_Flag_Isento_IRPJ']}',
                                    '{$mT006['T006_Flag_Isento_CSLL']}',
                                    '{$mT006['T006_Flag_Isento_PIS']}',
                                    '{$mT006['T006_Flag_Isento_COFINS']}',
                                    '{$mT006['T006_Situacao_Tributaria_IPI']}',
                                    '{$mT006['T006_Situacao_Tributaria_PIS']}',
                                    '{$mT006['T006_Situacao_Tributaria_COFINS']}',
                                    '{$mT006['T006_Valor_Base_PIS']}',
                                    '{$mT006['T006_Valor_Base_COFINS']}',
                                    '{$mT006['D005_Classificacao_Fiscal']}',
                                    '{$mT006['T006_Modalidade_ICMS']}',
                                    '{$mT006['T006_Modalidade_ICMS_Substituicao']}',
                                    '{$Flag_Situacao_Tributaria}',
                                    current_date(),
                                    1,
                                    'N',
                                    'S',
                                    '{$mT006['T006_Percentual_Desconto']}',
                                    '{$mT006['T006_Valor_Custo_Unitario']}',
                                    '{$mT006['T006_Valor_Preco_Original']}')");
            $erro = mysql_error();
            $T008_Id = $g['mysqlLastId'];
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro sql: ' . $erro;
            }

            $VEN003->atualizarAliquotasNF($T007_Id);
            mysql_query("call T008_Gravar_Totalizacao_4('$T008_Id');");
            mysql_query("UPDATE T006 SET T006_Flag_Pdv_Importado = 'S' WHERE T006_Id = '{$mT006['T006_Id']}'");

            if($g['C031']['desabilitarSeparacao'] == 'S' || $g['C031']['desabilitarExpedicaoPDV'] == 'S'){
                // Se vai importar a DAV, verifica se existe uma locação chamada LOJA, se existir, seta ela no pedido
                $T066 = mysql_query("SELECT T066_Id 
                                       FROM T066
                                  LEFT JOIN D009 ON D009_Id = T066_D009_Id
                                  LEFT JOIN D004 ON D004_Id = T066_D004_Id
                                      WHERE T066_D009_Id = '{$mT006['T006_D009_Id']}'
                                        AND D004_Local = 'LOJA'");
                if(mysql_num_rows($T066) > 0){
                    $mT066 = mysql_fetch_array($T066);
                    mysqli_query("UPDATE T006 SET T006_T066_Id = '{$mT066['T066_Id']}' WHERE T006_Id = '{$mT006['T006_Id']}' AND T006_T066_Id != '{$mT066['T066_Id']}'");
                }
            } 
        }

        mysql_query("UPDATE T005 SET T005_Flag_Pdv_Importado = 'S',
                                     T005_T007_Id            = '{$T007_Id}'
                               WHERE T005_Id = '{$T005_Id}'");
                               
        mysql_query("call T007_Gravar_Totalizacao_4('$T007_Id');");

        $ObsPedido = !empty($mT005['T005_Observacao']) ? $mT005['T005_Observacao'] . " - " : "";
        $ObsNota   = $mT007['T007_Observacao_Nota_Fiscal_1'] . $ObsPedido;
        
        if($g['C031']['habilitaUpdateVendedor'] == 'S'){
                mysql_query("UPDATE T007 SET T007_T005_Id                  = '{$T005_Id}',
                                             T007_Valor_Desconto           = '{$mT005['T005_Valor_Desconto_Manual']}',
                                             T007_Observacao_Nota_Fiscal_1 = '{$ObsNota}',
                                             T007_C007_Id_Vendedor_Interno = '{$vendedorInternoT005}'
                                       WHERE T007_Id='{$T007_Id}'");      
        }else{
            mysql_query("UPDATE T007 SET T007_T005_Id                  = '{$T005_Id}',
                                         T007_Valor_Desconto           = '{$mT005['T005_Valor_Desconto_Manual']}',
                                         T007_Observacao_Nota_Fiscal_1 = '{$ObsNota}'
                                   WHERE T007_Id='{$T007_Id}'");      
        }

        if($g['C031']['desabilitarSeparacao'] == 'S' || $g['C031']['desabilitarExpedicaoPDV'] == 'S'){

            mysql_query("UPDATE T005 SET T005_Flag_Status = 4,
                                         T005_Nome_Status = T005_Status_Pedido(T005_Flag_Status, 1)
                                   WHERE T005_Id='{$T005_Id}'");

            mysql_query("INSERT INTO T178 (T178_T005_Id,
                                           T178_C007_Id,
                                           T178_Data,
                                           T178_Descricao,
                                           T178_Observacao)
                                   VALUES (
                                          '{$T005_Id}',
                                          '{$g['usuarioAtual']}', 
                                          CURRENT_TIMESTAMP(),
                                          'Faturou Pedido',
                                          '')");

            mysql_query("INSERT INTO T178 (T178_T005_Id,
                                           T178_C007_Id,
                                           T178_Data,
                                           T178_Descricao,
                                           T178_Observacao)
                                   VALUES (
                                          '{$T005_Id}',
                                          '{$g['usuarioAtual']}',
                                          CURRENT_TIMESTAMP(),
                                          'Gerou nota fiscal (DAV) <b>{$mT007['T007_Numero_Nota_Fiscal']}</b>',
                                          '')");

            require_once("bibliotecas/classes/VEN012.php");
            $VEN012 = new VEN012();
            $retorno = $VEN012->separarPedido($T005_Id);
            if($retorno !== true){
                return $retorno;
            }
        }

        // Se alterar o cliente, ajusta os registros da tarefa
		$sqlD085 = mysql_query("SELECT D085_Id,
                                       D085_T259_Id
                                  FROM D085
                                 WHERE D085_T007_Id = '{$T007_Id}'");

        if(mysql_num_rows($sqlD085) == 1){
            // Quando abre uma NF via PDF já faz um registro nas atividades
            // Quando importar a DAV, apaga o primeiro registro e lança outro vincunlado ao pedido
            $rowD085 = mysql_fetch_array($sqlD085);

            mysqli_query("DELETE FROM D085 WHERE D085_Id = '{$rowD085['D085_Id']}'");
            mysqli_query("DELETE FROM T259 WHERE T259_Id = '{$rowD085['D085_T259_Id']}'");

            require_once('bibliotecas/classes/ETC017.php');
            $ETC017 = new ETC017();
            $ETC017->inserirRegistroTarefa($T007_Id, 'T007');
        }

        mysql_query("COMMIT");

        return true;
    }

    /**
     * ven015IncluirParcelasNotaFiscal
     *
     * @return String/bool
     */	
    function ven015IncluirParcelasNotaFiscal($T007_Id)
    {      
        // Funcao personalizada no APP INTEGRACAO PAGAMENTO POS
        // Felipe 18/11/2022
        
        return '';             
    }

    /**
     * ven015IncluirParcelasNotaFiscal
     *
     * @return String/bool
     */	
    function validaExibirBotaoPagamento($T007_Id)
    {      
        // Funcao personalizada no APP INTEGRACAO PAGAMENTO POS
        // Felipe 18/11/2022
        
        return true;             
    }

    /**
     * vinculaPagamentoNotaPOS
     *
     * @return String/bool
     */	
    function vinculaPagamentoNotaPOS($IPP004_Id, $T118_Id, $T007_Id)
    {      
        // Funcao personalizada no APP INTEGRACAO PAGAMENTO POS
        
        return true;             
    }    

    /**
     * vinculaPagamentoNotaPOS
     *
     * @return String/bool
     */	
    function verificaClubeDesconto($T007_Id)
    {              
        return true;             
    }  
}
