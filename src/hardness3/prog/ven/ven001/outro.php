<?php
namespace hardness;
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();

/**
 * Ações
 */
switch ($g['r'][3]) {
    /**
     * Cabeçalho Orçamento
     * Chamado em: ven001TabEditarOrcamento
     */
    case 'ven001CabecalhoOrcamento':
        global $g;
        // Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T003
        left join D024 on T003_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D024_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        WHERE
            (T003_Id='$r_acaoId')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (trim($row_)=="") { 
                $row_ = '<span style="color: var(--BGVermelho); line-height: 0.5rem;">FALTA</span>';
            }
        }
        $possuiRestricoes = "";
        if($row['D024_Flag_Liberado']=="N" || $row['D024_Flag_Restricao'] == "S"){
            $possuiRestricoes = "<font style='color: var(--BGVermelho); line-height: 0.5rem;'><b>RESTRIÇÕES</b></font>";
        }else{
            $possuiRestricoes = "<font style='color: var(--BGVerde); line-height: 0.5rem;'><b>LIBERADO</b></font>";
        }
        $Contribuinte = "<font style='color: var(--BGVermelho); line-height: 0.5rem;'><b> - </b></font>";
        if($row['D024_Flag_Contribuinte']=="0"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>NÃO CONTRIB.</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="1"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>CONSUM.FINAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="2"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>REVENDA</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="3"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>PROD.RURAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="4"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>SIMPLES</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="5"){
            $Contribuinte = "<font style='color: var(--BGAzul); line-height: 0.5rem;'><b>INDUSTRIA</b></font>";
        } else if ($row['D024_Flag_Contribuinte']==""){
            $Contribuinte="<font color='red'>FALTA</font>";            
        }
        
        switch($row['T003_Flag_Lista_Preco']){
            case '1':   
                $listaPreco="Preço1";
                break;
            case '2':
                $listaPreco="Preço2";
                break;
            case '3':
                $listaPreco = "Preço3";
                break;
            case '4':
                $listaPreco = "Preço4";
                break;
            case '5':
                $listaPreco = "Preço5";
                break;
            default:
                $listaPreco="<font color='red'><b>FALTA</b></font>";
        }

        switch($row['T003_Flag_Destino_Produto']){
            case '1':   
                $destino="Industrialização";
                break;
            case '2':
                $destino="Revenda";
                break;
            case '3':
                $destino = "Consumo";
                break;
            case '4':
                $destino = "Especial";
                break;
            default:
                $destino="<font color='red'><b>FALTA</b></font>";
        }
        
        $dataEmissao = gCorrigeData($row['T003_Data_Emissao']);
        if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 24, ' ');
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 24, ' ');
        }
        $D024_Nome_Empresa = $D024_Nome_Empresa.' '.$row['D024_Cnpj'];
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 15);
        $row['D024_Telefone_1'] = str_replace(array(' ', '.', '-'), '', $row['D024_Telefone_1']);
        $D024_Telefone = strpos($row['D024_Telefone_1'], "FALTA") ? '<span style="color: var(--BGVermelho);">FALTA</span>' : gTruncar($row['D024_DDD_Telefone_1'].' '.$row['D024_Telefone_1'], 20);

        $ultimaCompra = $row['D024_Data_Ultima_Compra'];

        if (!strpos($ultimaCompra,'FALTA')) {
            if ($ultimaCompra < date("Y-m-d",strtotime(date("Y-m-d")."-12 month"))) {
                $ultimaCompra = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>" . gCorrigeData($ultimaCompra) . "</font></blink>";
            } else {
                $ultimaCompra = "<font style='background:green;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>" . gCorrigeData($ultimaCompra) . "</font>";
            }
        } else {
            $ultimaCompra = "<blink><font style='background:red;color:white;font-weight:bold;padding:3px;border-radius: .425rem;'>SEM COMPRA</font></blink>";
        }

        /*if($g['C007']['C007_Flag_Vendedor'] == 'V' && $row['D024_Flag_Pre_Cadastro'] == 'S'){
            $urlCadastroCliente = <<<EOT
                onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Pré Cadastro Cliente/Fornecedor', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
EOT;
        } else {
            $urlCadastroCliente = <<<EOT
                onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
EOT;
        }*/
        $urlCadastroCliente = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;
        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc" style="cursor:pointer;" {$urlCadastroCliente}>
                        <td min-width='4%'>Nº</td>
                        <td min-width='4%'>Código</td>
                        <td min-width='35%'>Cliente</td>
                        <td min-width='11%'>Telefone</td>
                        <td min-width='13%'>Cidade</td>
                        <td min-width='1%'>UF</td>
                        <td min-width='5%'>Emissão</td>
                        <td min-width='7%'>Tipo</td>
                        <td min-width='7%'>Financeiro</td>
                        <td min-width='5%'>Lista</td>
                        <td min-width='9%'>Destino</td>
                        <td min-width='9%'>Ult.Compra</td>
                    </tr>
                    <tr class="linhaVal" style="cursor:pointer;" {$urlCadastroCliente}>
                        <td align='center'>{$row['T003_Id']}</td>
                        <td>{$row['D024_Id']}</td>
                        <td>{$D024_Nome_Empresa}</td>
                        <td>{$D024_Telefone}</td>
                        <td>{$D020_Nome_Cidade}</td>
                        <td>{$row['D018_UF']}</td>
                        <td>{$dataEmissao}</td>
                        <td>{$Contribuinte}</td>
                        <td>{$possuiRestricoes}</td>
                        <td>{$listaPreco}</td>
                        <td>{$destino}</td>
                        <td>{$ultimaCompra}</td>
                    </tr>
                </table>
            </div>
EOT;

    break;
    
    case 'ven001observacoes':
        global $g;
        $T003_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;

        // Retirado o gravar _totalizacao dos produtos pois estava gerando muita lentidão ao carregar a tela de orçamentos
        // $cSql_T004=mysql_query("select T004_Id from T004 where T004_T003_Id='$T003_Id'");
        // while ($mT004=mysql_fetch_array($cSql_T004)) {
        //     mysql_query("call T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}')");
        // }

        mysql_query("call T003_Gravar_Totalizacao_4($T003_Id);");
        
        $T003 = "SELECT T003_Id,
                        T003_Valor_Total_Produtos,
                        T003_Flag_Frete,
                        T003_Valor_Frete,
                        T003_Valor_Despesas_Acessorias,
                        T003_Valor_Total_Custo,
                        T003_Valor_Desconto,
                        T003_Valor_Total_Produtos_Sem_Desconto,
                        T003_Valor_Desconto_Manual,
                        T003_Valor_Total_ICMS,
                        T003A_Valor_Total_FCP,
                        T003_Valor_Total_ICMS_Credito,
                        T003_Valor_Total_IPI,
                        T003_Valor_ST,
                        T003A_Valor_FCP_ST,
                        T003_Valor_Total,
                        T003_IPV,
                        T003_Percentual_Margem,
                        T003_Peso_Total,
                        T003_Peso_Bruto_Total,
                        T003_Flag_Status_Orcamento,
                        T003_Valor_Total_Produtos_Confirmado,
                        T003_Valor_Total_ICMS_Confirmado,
                        T003_Valor_Total_IPI_Confirmado,
                        T003_Valor_ST_Confirmado,
                        T003_Valor_Total_Confirmado,
                        T003_Percentual_Comissao,
                        T003_Valor_Total_Comissao,
                        T003_Flag_Status_Cotacao
                        T003_Aliquota_ICMS_Credito,
                        T003_Valor_Total_PIS,
                        T003_Valor_Total_COFINS,
                        T003_Valor_Total_IRPJ,
                        T003_Valor_Total_CSLL,
                        T003_Valor_Total_Partilha_ICMS_Destino,
                        T003_Valor_Total_Partilha_ICMS_Origem,
                        D024_Id,
                        D024_Observacao_Comercial,
                        D024_Observacao_Financeiro,
                        D024_Valor_Credito_Antecipado,
                        D024_Valor_Credito_Troca,
                        T003_Prazos(T003_Id) AS Prazo, 
                        ROUND(SUM(T004_Valor_Frete_Total),2) AS FreteProdutos, 
                        GROUP_CONCAT(DISTINCT T005_Id) AS pedidos, 
                        SUM(T004_Valor_Lucro) AS Lucro,
                        Liberou.C007_Primeiro_Nome AS Liberou,
                        SUM(T004_Quantidade) AS quantidadeTotal,
                        SUM(T004_Cubagem_Unitaria*T004_Quantidade) AS cubagemTotal
                   FROM T003
              LEFT JOIN D024 ON D024_Id = T003_D024_Id
              LEFT JOIN T004 ON T004_T003_Id = T003_Id
              LEFT JOIN T005 ON T005_T003_Id=T003_Id and T005_Flag_Status!=8
              LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T003_Liberou_Alcada_C007_Id
              LEFT JOIN T003A ON T003_Id = T003A_T003_Id
                  WHERE T003_Id='{$T003_Id}'
               GROUP BY T003_Id";

        $T003  = mysql_query($T003);
        $mT003 = mysql_fetch_array($T003);

        $D024_Id      = $mT003['D024_Id'];    

        $dadosCliente = mysql_query("SELECT D024_Data_Cadastro, 
                                            D024_Valor_Limite, 
                                            D024_Valor_Credito_Antecipado, 
                                            D024_Valor_Credito_Troca,
                                            D024_Cnpj_Matriz 
                                       FROM D024
                                      WHERE D024_Id = '{$D024_Id}'");

        $dadosCliente = mysql_fetch_array($dadosCliente);

        $filtroSaldo = "D024_Id = '{$D024_Id}'";
        //calcula a soma dos limites das empresas vinculadas a um Cnpj Matriz
        if(($g['C031']['somarLimiteCnpjMatriz'] == 'S') && (!empty($dadosCliente['D024_Cnpj_Matriz']))){

            $filtroSaldo = "D024_Cnpj_Matriz = '{$dadosCliente['D024_Cnpj_Matriz']}'";

            $dadosLimite = mysql_query("SELECT SUM(D024_Valor_Limite) AS D024_Valor_Limite, 
                                               SUM(D024_Valor_Credito_Antecipado) AS D024_Valor_Credito_Antecipado, 
                                               SUM(D024_Valor_Credito_Troca) AS D024_Valor_Credito_Troca 
                                          FROM D024 
                                         WHERE $filtroSaldo");

            $dadosLimite = mysql_fetch_array($dadosLimite);

            $dadosCliente['D024_Valor_Limite']             = $dadosLimite['D024_Valor_Limite'];
            $dadosCliente['D024_Valor_Credito_Antecipado'] = $dadosLimite['D024_Valor_Credito_Antecipado'];
            $dadosCliente['D024_Valor_Credito_Troca']      = $dadosLimite['D024_Valor_Credito_Troca'];           
        } 


        $obsComercial    = substr($mT003['D024_Observacao_Comercial'], 0, 140);
        $obsFinanceiro   = substr($mT003['D024_Observacao_Financeiro'], 0, 180);
        $valorLimite     = $dadosCliente['D024_Valor_Limite'];

        //Calcula saldo do cliente
        $sqlSaldo = "SELECT IFNULL(SUM(IF(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) AS Vencido,
                            IFNULL(SUM(IF(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) AS Vencer
                       FROM T002
                  LEFT JOIN D024 ON D024_Id=T002_D024_Id
                      WHERE $filtroSaldo
                        AND T002_Data_Recebimento='0000-00-00'
                        AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";

        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo  = mysql_fetch_array($resultSaldo);

/*         $sqlAtraso = mysql_query("SELECT SUM(T002_Valor_Total) As Atraso
                                    FROM T002
                               LEFT JOIN D024 ON D024_Id = T002_D024_Id
                                   WHERE $filtroSaldo 
                                     AND T002_Data_Recebimento = '0000-00-00'
                                     AND T002_Data_Vencimento < CURDATE()
                                     AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)");

        $linhaAtraso           = mysql_fetch_array($sqlAtraso); */
        $linhaAtraso['Atraso'] = gCorrigeNumero($linhaSaldo['Vencido']);
  
        //$linhaAtraso = gCorrigeNumero($atraso);

        //Verifica os pedidos em aberto
        $sqlPedidos   = mysql_query("SELECT IFNULL(SUM(T005_Valor_Total),0) as Pedidos
                                       FROM T005 
                                  LEFT JOIN T007 ON T007_T005_Id=T005_Id 
                                  LEFT JOIN D024 ON D024_Id=T005_D024_Id
                                      WHERE $filtroSaldo
                                        AND T005_Flag_Status !=8
                                        AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null) 
                                        AND T007_Id IS NULL");
        $linhaPedidos = mysql_fetch_array($sqlPedidos);
        
        // CALCULA SALDO LIMITE
        $saldoAtual = ($valorLimite-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$linhaPedidos['Pedidos']));
        $saldoAtual = gCorrigeNumero($saldoAtual);        

        $valorCreditoAntecipado = gCorrigeNumero($mT003['D024_Valor_Credito_Antecipado']);
        $valorCreditoTroca      = gCorrigeNumero($mT003['D024_Valor_Credito_Troca']);
        $valorLimite            = gCorrigeNumero($valorLimite);      

        if($mT003['T003_Valor_Total_Produtos'] > 0 && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT003['T003_Flag_Frete'] == '0' && $mT003['FreteProdutos'] != $mT003['T003_Valor_Frete']) {
            $mT003['T003_Valor_Frete'] = '<span style="color: var(--BGVermelho)">Atualizar preço tabela</span>';
        } else {
            $mT003['T003_Valor_Frete'] = gCorrigeNumero($mT003['T003_Valor_Frete']);
        }

        $mT003['T003_Valor_Despesas_Acessorias'] = gCorrigeNumero($mT003['T003_Valor_Despesas_Acessorias']);
        $lucro                                   = gCorrigeNumero(($mT003['Lucro']/$mT003['T003_Valor_Total_Produtos'])*100);
        $mT003['Lucro']                          = gCorrigeNumero($mT003['Lucro']);
        $margem                                  = gCorrigeNumero((($mT003['T003_Valor_Total_Produtos']-$mT003['T003_Valor_Total_Custo'])/$mT003['T003_Valor_Total_Produtos'])*100);
        $percentualDesconto                      = gCorrigeNumero(($mT003['T003_Valor_Desconto']/$mT003['T003_Valor_Total_Produtos_Sem_Desconto'])*100);

        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();
        $percentualDesconto = $VEN001->ven001ModObservacaoPercentualDesconto($percentualDesconto, $mT003);

        $percentualDescontoManual               = gCorrigeNumero(($mT003['T003_Valor_Desconto_Manual']/$mT003['T003_Valor_Total_Produtos_Sem_Desconto'])*100);
        $ipv                                    = gCorrigeNumero($mT003['T003_Valor_Total_Produtos']/$mT003['T003_Valor_Total_Custo'],4);
        $mT003['T003_Valor_Total_Custo']        = gCorrigeNumero($mT003['T003_Valor_Total_Custo']);
        $mT003['T003_Valor_Desconto']           = gCorrigeNumero($mT003['T003_Valor_Desconto']);
        //$mT003['T003_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mT003['T003_Valor_Total_Produtos_Sem_Desconto']);
        $mT003['T003_Valor_Total_Produtos']     = gCorrigeNumero($mT003['T003_Valor_Total_Produtos']);
        $valorTotalICMS                         = gCorrigeNumero($mT003['T003_Valor_Total_ICMS'] + $mT003['T003A_Valor_Total_FCP']);
        $mT003['T003_Valor_Total_ICMS']         = gCorrigeNumero($mT003['T003_Valor_Total_ICMS']);
        $mT003['T003A_Valor_Total_FCP']         = gCorrigeNumero($mT003['T003A_Valor_Total_FCP']);
        $mT003['T003_Valor_Total_ICMS_Credito'] = gCorrigeNumero($mT003['T003_Valor_Total_ICMS_Credito']);
        $mT003['T003_Valor_Total_IPI']          = gCorrigeNumero($mT003['T003_Valor_Total_IPI']);
        $valortotalST                           = gCorrigeNumero($mT003['T003_Valor_ST'] + $mT003['T003A_Valor_FCP_ST']);
        $mT003['T003A_Valor_FCP_ST']            = gCorrigeNumero($mT003['T003A_Valor_FCP_ST']);
        $mT003['T003_Valor_ST']                 = gCorrigeNumero($mT003['T003_Valor_ST']);        
        $titleST                                = "ST: &nbsp; {$mT003['T003_Valor_ST']}\nFCP ST: &nbsp; {$mT003['T003A_Valor_FCP_ST']}";
        $mT003['T003_Valor_Total']              = gCorrigeNumero($mT003['T003_Valor_Total']);
        $mT003['T003_IPV']                      = gCorrigeNumero($mT003['T003_IPV'],4);
        $mT003['T003_Percentual_Margem']        = gCorrigeNumero($mT003['T003_Percentual_Margem'],2);
        $mT003['T003_Valor_Desconto_Manual']    = gCorrigeNumero($mT003['T003_Valor_Desconto_Manual']*-1);
        $mT003['quantidadeTotal']               = gCorrigeNumero($mT003['quantidadeTotal']);
        $mT003['cubagemTotal']                  = gCorrigeNumero($mT003['cubagemTotal']);
        $mT003['pesoLiquido']                   = gCorrigeNumero($mT003['T003_Peso_Total']);
        $mT003['pesoBruto']                     = gCorrigeNumero($mT003['T003_Peso_Bruto_Total']);

        require_once('prog/crm/crm001/grid_func-callback.php');
        if ($mT003['T005_Id']>0){
            $pedido = "<div class='blue' style='border-radius:5px;background:blue;color:white; padding: 1px;margin-top: 14px;height: 16px;font-size: 13px;' align=center>Gerou pedido n. {$mT003['pedidos']}</div>";
        } else {
            $arrayStatus = array();
            $arrayStatus['todosCampos']['T003_Flag_Status_Orcamento'] = $mT003['T003_Flag_Status_Orcamento'];
            $statusOrcamento = campoStatus($arrayStatus);
            $pedido = "<div class='blue' style='border-radius:5px;background:blue;color:white; padding: 1px;margin-top: 14px;height: 16px;font-size: 13px;' align=center>{$statusOrcamento}</div>";
        }

        $mT003['T003_Valor_Total_Produtos_Confirmado'] = gCorrigeNumero($mT003['T003_Valor_Total_Produtos_Confirmado']);
        $mT003['T003_Valor_Total_ICMS_Confirmado']     = gCorrigeNumero($mT003['T003_Valor_Total_ICMS_Confirmado']);
        $mT003['T003_Valor_Total_IPI_Confirmado']      = gCorrigeNumero($mT003['T003_Valor_Total_IPI_Confirmado']);
        $mT003['T003_Valor_ST_Confirmado']             = gCorrigeNumero($mT003['T003_Valor_ST_Confirmado']);
        $mT003['T003_Valor_Total_Confirmado']          = gCorrigeNumero($mT003['T003_Valor_Total_Confirmado']);
        $mT003['T003_Percentual_Comissao']             = gCorrigeNumero($mT003['T003_Percentual_Comissao']);
        $mT003['T003_Valor_Total_Comissao']            = gCorrigeNumero($mT003['T003_Valor_Total_Comissao']);

        
        //$valorOrcamento = $VEN001->calculaTotalGeral($T003_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        //if($exibirLimites == false){

        require_once('bibliotecas/funcoesCallback/campoPrecoTabelaGridOrcamento.php');
        $array['todosCampos']['T003_Id']                  = $T003_Id;
        $array['todosCampos']['T003_Flag_Status_Cotacao'] = $mT003['T003_Flag_Status_Cotacao'];
        $array['campoAtual']                              = $mT003['T003_Valor_Total_Produtos_Sem_Desconto'];
        $valorPrecoTabelaOrcamento = str_replace('R$ ','',campoPrecoTabelaGridOrcamento($array, true));

        $onclick = '';
        if(isset($g['c029Ids'][215])){
            $onclick = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Demonstrativo resultado', '/ven/ven001/content/ven001contentMontagemIPV/', '&acaoId={$T003_Id}' , [400,620]);";
        }
        
        $heightComercial=60;
        $heightFinanceiro=49;

        if(!(isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
            $heightComercial=110;
        }
        if((isset($g['c029Ids'][217])) && !(isset($g['c029Ids'][218]))){
            $heightFinanceiro=135;
        }

        $C004  = mysql_query("SELECT C004_Codigo_Regime_Tributario 
                                FROM C004
                               WHERE C004_Id='{$g['empresaAtual']}'");

        $mC004 = mysql_fetch_array($C004);

        if($mC004['C004_Codigo_Regime_Tributario'] == 1 ){
            $ICMS         = 'ICMS Crédito';
            $aliquotaICMS = "(".$mT003['T003_Aliquota_ICMS_Credito']."%) ";
            $valorICMS    = $mT003['T003_Valor_Total_ICMS_Credito'];
            $titleICMS    = "ICMS: &nbsp; {$mT003['T003_Valor_Total_ICMS']}";
        }else{
            $ICMS         = 'ICM';
            $aliquotaICMS = '';
            $valorICMS    = $valorTotalICMS;
            $titleICMS    = "ICMS: &nbsp; {$mT003['T003_Valor_Total_ICMS']}\nFCP: &nbsp; {$mT003['T003A_Valor_Total_FCP']}";

        }

        $somaTributos = $mT003['T003_Valor_Total_PIS']+$mT003['T003_Valor_Total_COFINS']+$mT003['T003_Valor_Total_IRPJ']+$mT003['T003_Valor_Total_CSLL']+$mT003['T003_Valor_Total_Partilha_ICMS_Destino']+$mT003['T003_Valor_Total_Partilha_ICMS_Origem'];
        $somaTributos = gCorrigeNumero($somaTributos);

        $mT003['T003_Valor_Total_PIS']                   = gCorrigeNumero($mT003['T003_Valor_Total_PIS']);
        $mT003['T003_Valor_Total_COFINS']                = gCorrigeNumero($mT003['T003_Valor_Total_COFINS']);
        $mT003['T003_Valor_Total_IRPJ']                  = gCorrigeNumero($mT003['T003_Valor_Total_IRPJ']);
        $mT003['T003_Valor_Total_CSLL']                  = gCorrigeNumero($mT003['T003_Valor_Total_CSLL']);
        $mT003['T003_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT003['T003_Valor_Total_Partilha_ICMS_Destino']);
        $mT003['T003_Valor_Total_Partilha_ICMS_Origem']  = gCorrigeNumero($mT003['T003_Valor_Total_Partilha_ICMS_Origem']);
        $title = "PIS: &nbsp; {$mT003['T003_Valor_Total_PIS']}\nCOFINS: &nbsp; {$mT003['T003_Valor_Total_COFINS']}\nIRPJ: &nbsp; {$mT003['T003_Valor_Total_IRPJ']}\nCSLL:  &nbsp; {$mT003['T003_Valor_Total_CSLL']}\nDIFAL DEST:  &nbsp {$mT003['T003_Valor_Total_Partilha_ICMS_Destino']}\nDIFAL ORIG:  &nbsp; {$mT003['T003_Valor_Total_Partilha_ICMS_Origem']}";

        $mT003['T003_Valor_Frete_Calculado'] = gCorrigeNumero($mT003['T003_Valor_Frete_Calculado']);

        echo <<<EOT
            <style type="text/css">
                .observacoesLimites{
                    box-sizing: border-box;
                    width: min(200px, calc(100vw - 24px));
                    max-width: calc(100% - 8px);
                    min-height: 186px;
                    height: auto;
                    float: left;
                    margin: 7px 0 10px 8px;
                    border-radius: 5px;
                    border-color: silver;
                    overflow: visible;
                    position: relative;
                    padding-bottom: 22px;
                }
                .observacoesLimites > .blue{
                    position: absolute;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    box-sizing: border-box;
                    height: 18px;
                    margin: 0 !important;
                    padding: 1px;
                }
                .observacoesLimites .comercial,
                .observacoesLimites .financeiro,
                .observacoesLimites .limite,
                .observacoesLimites .liberado,
                .observacoesLimites .saldo{
                    box-sizing: border-box;
                    height: auto !important;
                    min-height: 18px;
                    padding: 1px;
                    white-space: normal;
                    overflow: visible;
                    overflow-wrap: break-word;
                    word-break: break-word;
                }
                .observacoesLimites .comercial{
                    padding: 1px;
                    border-bottom: none;
                }
                .observacoesLimites .liberado{
                    padding: 0;
                    border-bottom: 1px solid silver;
                }
                .observacoesLimites .limite,
                .observacoesLimites .saldo{
                    border-top: none;
                }
                .observacoesLinha{
                    display: flex;
                    flex-wrap: nowrap;
                    align-items: flex-start;
                    width: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                }
                .observacoesLinha > table{
                    flex: 0 0 195px;
                    float: none !important;
                }
                .observacoesLinha > .observacoesLimites{
                    flex: 0 0 200px;
                    float: none !important;
                }
            </style>
            <div class="observacoesLinha">
            <table style='font-size:13px; border-left:5px;width: 195px;float:left; height: 210px; margin: 3px 5px 0 5px; cursor: pointer;' cellspacing="0" cellpadding="0" onclick="{$onclick}">
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    Tab
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$valorPrecoTabelaOrcamento}
                </td>
              </tr>
             
EOT;
           
            echo <<<EOT
                <tr>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                        Desconto
                    </td>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                        <font style='font-size:11px'>($percentualDesconto%)</font> {$mT003['T003_Valor_Desconto']}
                    </td>
                </tr>
              <tr>
                <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                    Produtos
                </td>
                <td class='observacaoTotaisTd' style='font-weight:bold;font-size:14px;border-bottom:1px solid #F4F4F4' align='right'>
                    {$mT003['T003_Valor_Total_Produtos']}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    IPI
                </td>
                <td class='observacaoTotaisTd'  style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$mT003['T003_Valor_Total_IPI']}
                </td>
              </tr>
              <tr title="{$titleST}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    <u>ST+FCP ST</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$valortotalST}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    Desconto
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    <font style='font-size:11px'>($percentualDescontoManual%)</font> {$mT003['T003_Valor_Desconto_Manual']}
                </td>
              </tr>
              <tr title="Quantidade: &nbsp; {$mT003['quantidadeTotal']}\nCubagem: &nbsp; {$mT003['cubagemTotal']}m³\nPeso liquido: &nbsp; {$mT003['pesoLiquido']} kg\nPeso bruto:  &nbsp; {$mT003['pesoBruto']} kg\n Frete calculado:  &nbsp; R$ {$mT003['T003_Valor_Frete_Calculado']}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    <u>Frete</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$mT003['T003_Valor_Frete']}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    Desp.Acessorias
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$mT003['T003_Valor_Despesas_Acessorias']}
                </td>
              </tr>
              <tr>
                <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                    Total
                </td>
                <td class='observacaoTotaisTd' style='font-weight:bold;font-size:16px;border-bottom:1px solid #F4F4F4' align='right'>
                    {$mT003['T003_Valor_Total']}
                </td>
              </tr>
              <tr title="{$titleICMS}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    <u>{$ICMS}</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$aliquotaICMS}{$valorICMS}
                </td>
              </tr>
               <tr title="{$title}">
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    <u>Outros tributos</u>
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    {$somaTributos}
                </td>
              </tr>
                           
              <tr>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;'>
                    Comissão
                </td>
                <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                    ({$mT003['T003_Percentual_Comissao']}%) {$mT003['T003_Valor_Total_Comissao']}
                </td>
              </tr>              
            </table>
EOT;

    if((isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='float:left;'>
                <div style="height:135px;"></div>
EOT;
    }

    if(!(isset($g['c029Ids'][217])) || !(isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='float:left;'>
EOT;

    }

    if(!(isset($g['c029Ids'][217]))){
        echo<<<EOT
            <div class="comercial" title='{$mT003['D024_Observacao_Comercial']}'>
                <b>Observação comercial:</b><br />
                <span style="color: var(--BGVermelho);">{$obsComercial}</span>
            </div>
            <div class="liberado">
                <span style="">Liberado Por: <span style="color: var(--BGVermelho);">{$mT003['Liberou']}</span></span>
            </div>
EOT;

    }

     if(!(isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="financeiro"  title='{$mT003['D024_Observacao_Financeiro']}'>
                <b>Observação financeiro:</b><br />
                <span style="color: var(--BGVermelho);">{$obsFinanceiro}</span>
            </div> 
EOT;

    }

    $diasSemNota = "";
    if ($g['C031']['mostrarDiasSemNota'] == 'S' && $g['C031']['diasUltimaVendaPedirAnaliseCredito'] > 0) {
        $select = "SELECT T007_Id, datediff(curdate(), T007_Data_Emissao) as ultVendaDias
                    FROM T007 
                        LEFT JOIN D006 ON D006_Id = T007_D006_Id
                    WHERE
                        T007_D024_Id = '{$D024_Id}'
                        AND T007_Flag_Cancelada != 'S'
                        AND D006_Flag_Venda_Compra_Outros = 'V'
                        AND D006_Flag_Devolucao != 'S'
                    order by T007_Id desc limit 1";
        $result = mysql_query($select);
        $mT007 = mysql_fetch_assoc($result);

        echo "<style type='text/css'>
                .observacoesLimites .limite{
                    height: 20%;
                    padding:1px;
                    border-top:none;
                }
            </style>";

        if ($mT007['ultVendaDias'] >= $g['C031']['diasUltimaVendaPedirAnaliseCredito']) {
            $corSemNota = "var(--BGVermelho);" ;
            $msg = " - Pedido ira para Análise de crédito";    
        } else {
            $corSemNota = "var(--BGAzul);"; 
            $msg = "";
        }
        $diasSemNota = "Dias sem nota: <span style=\"color: {$corSemNota}\">{$mT007['ultVendaDias']}{$msg}</span><br>";
        $diasSemNota = (mysql_num_rows($result) <= 0) ? "Primeira venda para este cliente" : $diasSemNota;
    }
    
       echo<<<EOT
            <div class="limite">
                    {$diasSemNota}
                    Prazo Pagamento: <span style="color: var(--BGVermelho);">{$mT003['Prazo']}</span><br>
                    Limite: <span style="color: var(--BGVermelho);">{$valorLimite}</span> Saldo: <span style="color: var(--BGVermelho);">{$saldoAtual}</span><br>
                    Crédito Antecipado: <span style="color: var(--BGVermelho);">{$valorCreditoAntecipado}</span> Crédito Troca: <span style="color: var(--BGVermelho);">{$valorCreditoTroca}</span><br>
                    Valores em atraso: <span style="color: var(--BGVermelho);">{$linhaAtraso['Atraso']}</span>
            </div>
           <br>$pedido
        </div> 
        </div>
       
EOT;
    break;

    case 'ven001Totalizadores':
        
        // Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T003
        left join D024 on T003_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D020_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        left join C004 on C004_Id=T003_C004_Id
        WHERE
            (T003_Id='$r_T003_Id')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        
        $dataEmissao = gCorrigeData($row['T003_Data_Emissao']);
        $valor = gCorrigeNumero($row['T003_Valor_Total']);
        $row['C004_Nome_Empresa'] = substr($row['C004_Nome_Empresa'], 0, 30);
        if(strlen($row['C004_Nome_Empresa']) == 30){
            $row['C004_Nome_Empresa'] = $row['C004_Nome_Empresa'];
        }
        
        // Print
        $novoIdTelaForm = uniqid();
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all" width="100%">
                    <tr class="linhaDesc">
                        <td>Orçamento</td>
                        <td style="width:500px;"><a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent({$row['D024_Id']}) + '&tabela=D024', [960,583]);" style="padding: 5px 200px 5px 5px;">Cliente</a></td>
                        <td>Valor</td>
                        <td>Empresa</td>
                    </tr>
                    <tr class="linhaVal">
                        <td>{$row['T003_Id']}</td>
                        <td><a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent({$row['D024_Id']}) + '&tabela=D024', [960,583]);" style="padding: 5px 50px 5px 5px;">{$row['D024_Nome_Empresa']}</a></td>
                        <td>{$valor}</td>
                        <td>{$row['C004_Nome_Empresa']}</td>
                    </tr>
                </table>
            </div>
EOT;
        
        $T003_Id = $r_T003_Id;
        $sql="SELECT T003_Valor_Total_Produtos,T003_Valor_Total_Servicos,T003_Valor_Troca,T003_Valor_Desconto_Manual,T003_Valor_Total FROM T003 WHERE T003_Id={$T003_Id}";
        $sqlResult = mysql_query($sql);
        $row = mysql_fetch_array($sqlResult);
        
        $totalProdutos = gCorrigeNumero($row['T003_Valor_Total_Produtos']);
        $totalServicos = gCorrigeNumero($row['T003_Valor_Total_Servicos']);
        $totalTroca = gCorrigeNumero($row['T003_Valor_Troca']);
        $totalDesconto = gCorrigeNumero($row['T003_Valor_Desconto_Manual']);
        $totalGeral = gCorrigeNumero($row['T003_Valor_Total']);
        echo <<<EOT
        <div class="buttonsBottomTop">
            <button type="button" id="ven001DividirPedidoBtn" style="margin:8px;">Dividir Orçamento</button>
            <button type="button" id="ven001MudarFilial" style="margin:8px;">Trocar empresa</button>
            <!-- <button type="button" id="ven001GerarNFMarcados" style="margin:8px;">Gerar NF</button> -->
            <!-- <button type="button" id="ven001ConcluirNF" style="margin:8px;">Concluir</button> -->
            <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$totalProdutos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Serviço<br><input type="text" value="{$totalServicos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Trocas<br><input type="text" value="{$totalTroca}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Desconto<br><input type="text" value="{$totalDesconto}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Total<br><input type="text" value="{$totalGeral}" class="whiteBg" id="" size="11" readonly></span>
            <div style="clear:both;"></div>
        </div>
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
            $('#ven001DividirPedidoBtn').unbind('click').bind('click', function(data) {
                $.getJSON('/ven/ven001/grid_func-ajax/ven001VerificaItensDividir/?T003_Id=' + encodeURIComponent('{$T003_Id}') + '&ajax=true&callback=?', function(request) {
                    if(request.code){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven001/content/ven001ContentSelecionarEmpresa/', '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}'), [400,200]);
                    }else{
                        var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                        dialogConfirm('Erro', 'Não há itens selecionadas para dividir o pedido', \$buttons);
                    }
                });
            });
        
        $('#ven001MudarFilial').unbind('click').bind('click', function(data) {
            $.getJSON('/ven/ven001/grid_func-ajax/ven001VerificaItensMudarFIlial/?T003_Id=' + encodeURIComponent('{$T003_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven001/content/ven001ContentSelecionarEmpresa/', '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&mudarFilial=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste orçamento', \$buttons);
                }
            });
        });
            
        $('#ven001GerarNFMarcados').unbind('click').bind('click', function(data) {
            $.getJSON('/ven/ven001/grid_func-ajax/ven001VerificaItensGerarNF/?T003_Id=' + encodeURIComponent('{$T003_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/ven/ven001/content/ven001ContentSelecionarEmpresa/', '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&gerarNF=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
                }
            });
        });
        
        $('#ven001ConcluirNF').unbind('click').bind('click', function(data) {
            var \$buttons = {
                "Sim": function() {
                    $('#' + '{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven001/grid_func-ajax/ven001ConcluirPedido/?T003_Id=' + encodeURIComponent('{$T003_Id}') + '&ajax=true&callback=?', function(request) {
                        if (request.code) {
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Ok', request.data, \$buttons);
                            divRefresh('{$g['divId']}', true);
                        } else {
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Erro', request.data, \$buttons);
                        }
                        $('#' + '{$g['divId']}').hideLoading();
                        reloadUI('{$g['divId']}');
                    });
                    $(this).dialog("destroy").remove();
                },
                "Não": function() { $(this).dialog("destroy").remove(); } 
            };
            dialogConfirm('Conclusão', 'Você tem certeza?', \$buttons);
        });
} </script></div>
EOT;
    break;

    case 'teste':
        global $g;
        require_once('bibliotecas/classes/VEN001.php');
        $VEN001 = new VEN001();

        $sqlT003 = mysql_query("SELECT * FROM T003 WHERE T003_Data_Emissao >= '2013-05-06'");
        while($T003 = mysql_fetch_array($sqlT003)){     
            $T004=mysqli_query("SELECT * FROM T004 WHERE T004_T003_Id='{$T003['T003_Id']}'");
            while($mT004 = mysql_fetch_array($T004)){   
                $C007=mysql_query("SELECT * FROM C007 WHERE C007_Id='{$T003['T003_C007_Id_Vendedor_Interno']}'");
                $mC007=mysql_fetch_array($C007);
                
                if($mC007['C007_Flag_Vendedor'] == "V"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT004['T004_IPV'], 'V', $mT004['T004_Valor_Total_Preco']);
                    
                }else if($mC007['C007_Flag_Vendedor'] == "C"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT004['T004_IPV'], 'C', $mT004['T004_Valor_Total_Preco']);
                }
            
                $upT004 = mysqli_query("UPDATE T004 SET T004_Valor_Comissao='{$retorno['valorComissao']}', T004_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T004_Id='{$mT004['T004_Id']}'");
                $upT003 = mysqli_query("UPDATE T003 SET T003_Valor_Total_Comissao=(SELECT SUM(T004_Valor_Comissao) FROM T004 WHERE T004_T003_Id=T003_Id) WHERE T003_Id = '{$mT004['T004_T003_Id']}'");
            }
        }

        $sqlT005 = mysql_query("SELECT * FROM T005 WHERE T005_Data_Emissao >= '2013-05-06'");
        while($T005 = mysql_fetch_array($sqlT005)){     
            $T006=mysqli_query("SELECT * FROM T006 WHERE T006_T005_Id='{$T005['T005_Id']}'");
            while($mT006 = mysql_fetch_array($T006)){   
                $C007=mysql_query("SELECT * FROM C007 WHERE C007_Id='{$T005['T005_C007_Id_Vendedor_Interno']}'");
                $mC007=mysql_fetch_array($C007);
                
                if($mC007['C007_Flag_Vendedor'] == "V"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT006['T006_IPV'], 'V', $mT006['T006_Valor_Total_Preco']);
                    
                }else if($mC007['C007_Flag_Vendedor'] == "C"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT006['T006_IPV'], 'C', $mT006['T006_Valor_Total_Preco']);
                }
            
                $upT006 = mysqli_query("UPDATE T006 SET T006_Valor_Comissao='{$retorno['valorComissao']}', T006_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T006_Id='{$mT006['T006_Id']}'");
                $upT005 = mysqli_query("UPDATE T005 SET T005_Valor_Total_Comissao=(SELECT SUM(T006_Valor_Comissao) FROM T006 WHERE T006_T005_Id=T005_Id) WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
            }
        }
        
        $sqlT007 = mysql_query("SELECT * FROM T007 WHERE T007_Id >= 4754");
        while($T007 = mysql_fetch_array($sqlT007)){     
            $T008=mysqli_query("SELECT * FROM T008 WHERE T008_T007_Id='{$T007['T007_Id']}'");
            while($mT008 = mysql_fetch_array($T008)){   
                $C007=mysql_query("SELECT * FROM C007 WHERE C007_Id='{$T007['T007_C007_Id_Vendedor_Interno']}'");
                $mC007=mysql_fetch_array($C007);
                
                if($mC007['C007_Flag_Vendedor'] == "V"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT008['T008_IPV'], 'V', $mT008['T008_Valor_Total_Preco']);
                    
                }else if($mC007['C007_Flag_Vendedor'] == "C"){
                    
                    // O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
                    $retorno = $VEN001->calcularComissao($mT008['T008_IPV'], 'C', $mT008['T008_Valor_Total_Preco']);
                }
            
                $upT008 = mysqli_query("UPDATE T008 SET T008_Valor_Comissao='{$retorno['valorComissao']}', T008_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T008_Id='{$mT008['T008_Id']}'");
                $upT007 = mysqli_query("UPDATE T007 SET T007_Valor_Total_Comissao=(SELECT SUM(T008_Valor_Comissao) FROM T008 WHERE T008_T007_Id=T007_Id) WHERE T007_Id = '{$mT008['T008_T007_Id']}'");
            }
        }

        echo 'Atualização Finalizada';
    break;

      case 'vendasDozeMeses':
        // pega os valores dos parâmetros da curva
        $parametrosCurvaABC = [0, 0, 0];
        $pCurvaA = 0;
        $pCurvaB = 0;
        $pCurvaC = 0;
        $somaParametros = 0;

        if (!empty($g['C031']['curvaABCprodutos'])) {
            $parametrosCurvaABC = explode(",", str_replace(" ", "", trim($g['C031']['curvaABCprodutos'], ",")));
            $parametrosCurvaABC[0] = (float)($parametrosCurvaABC[0] ?? 0);
            $parametrosCurvaABC[1] = (float)($parametrosCurvaABC[1] ?? 0);
            $parametrosCurvaABC[2] = (float)($parametrosCurvaABC[2] ?? 0);

            // mantém a mesma lógica antiga: B exibido como B + C
            $parametrosCurvaABC[1] += $parametrosCurvaABC[2];

            $pCurvaA = $parametrosCurvaABC[0] / 100;
            $pCurvaB = $parametrosCurvaABC[1] / 100;
            $pCurvaC = $parametrosCurvaABC[2] / 100;
            $somaParametros = ($pCurvaA + $pCurvaB) * 100;
        }

        $mesInicial = strtotime('-12 month');
        $xLast12 = date('Y-m-d', $mesInicial);

        // procura o produto para gerar o relatório
        $sqlD009 = mysql_query("
            SELECT 
                D001_Codigo_Produto,
                D001_Descricao_Produto,
                D009_D049_Id
            FROM D009
            INNER JOIN D049 ON D049_Id = D009_D049_Id
            INNER JOIN D001 ON D001_Id = D049_D001_Id
            WHERE D009_Id = '{$r_acaoId}'
            LIMIT 1
        ");
        $rowD009 = mysql_fetch_array($sqlD009);

        if (empty($rowD009)) {
            echo "Produto não encontrado.";
            break;
        }

        $d049Id = (int)$rowD009['D009_D049_Id'];
        $d009Id = (int)$r_acaoId;

        // monta os 13 meses do relatório
        $meses = [];
        $mapEmpresa = [];
        $mapGrupo = [];

        $mesTmp = $mesInicial;
        for ($i = 0; $i < 13; $i++) {
            $chaveMes = date('Y-m', $mesTmp);
            $meses[$chaveMes] = [
                'titulo' => date('m/Y', $mesTmp),
                'empresa' => ['Quantidade' => 0, 'ValorTotal' => 0, 'Frequencia' => 0],
                'grupo'   => ['Quantidade' => 0, 'ValorTotal' => 0, 'Frequencia' => 0],
            ];
            $mesTmp = strtotime('+1 month', $mesTmp);
        }

        // ---------------------------------------------------------------------------------
        // 1) AGREGA T001 LOCAL EM UMA ÚNICA QUERY
        // ---------------------------------------------------------------------------------
        $sqlEmpresa = mysql_query("
            SELECT
                DATE_FORMAT(T001_Data_Lancamento, '%Y-%m') AS MesRef,
                SUM(T001_Quantidade) AS Quantidade,
                SUM(T001_Quantidade * T001_Valor_Preco_Unitario) AS ValorTotal,
                COUNT(*) AS Frequencia
            FROM T001
            WHERE T001_D009_Id = '{$d009Id}'
            AND T001_Data_Lancamento >= '{$xLast12}'
            AND T001_Flag_MMF = 'S'
            AND T001_Flag_Operacao = 'S'
            GROUP BY DATE_FORMAT(T001_Data_Lancamento, '%Y-%m')
        ");
        while ($row = mysql_fetch_array($sqlEmpresa)) {
            $mapEmpresa[$row['MesRef']] = [
                'Quantidade' => (float)$row['Quantidade'],
                'ValorTotal' => (float)$row['ValorTotal'],
                'Frequencia' => (int)$row['Frequencia'],
            ];
        }

        // ---------------------------------------------------------------------------------
        // 2) AGREGA T001 GRUPO EM UMA ÚNICA QUERY
        // ---------------------------------------------------------------------------------
        $sqlGrupo = mysql_query("
            SELECT
                DATE_FORMAT(T001_Data_Lancamento, '%Y-%m') AS MesRef,
                SUM(T001_Quantidade) AS Quantidade,
                SUM(T001_Quantidade * T001_Valor_Preco_Unitario) AS ValorTotal,
                COUNT(*) AS Frequencia
            FROM T001
            INNER JOIN D009 ON D009_Id = T001_D009_Id
            WHERE D009_D049_Id = '{$d049Id}'
            AND T001_Data_Lancamento >= '{$xLast12}'
            AND T001_Flag_MMF = 'S'
            AND T001_Flag_Operacao = 'S'
            GROUP BY DATE_FORMAT(T001_Data_Lancamento, '%Y-%m')
        ");
        while ($row = mysql_fetch_array($sqlGrupo)) {
            $mapGrupo[$row['MesRef']] = [
                'Quantidade' => (float)$row['Quantidade'],
                'ValorTotal' => (float)$row['ValorTotal'],
                'Frequencia' => (int)$row['Frequencia'],
            ];
        }

        $total = 0;
        $valorTotal = 0;
        $frequencia = 0;
        $totalGrupo = 0;
        $valorTotalGrupo = 0;
        $frequenciaGrupo = 0;

        foreach ($meses as $chaveMes => &$dadosMes) {
            if (!empty($mapEmpresa[$chaveMes])) {
                $dadosMes['empresa'] = $mapEmpresa[$chaveMes];
            }
            if (!empty($mapGrupo[$chaveMes])) {
                $dadosMes['grupo'] = $mapGrupo[$chaveMes];
            }

            $total += $dadosMes['empresa']['Quantidade'];
            $valorTotal += $dadosMes['empresa']['ValorTotal'];
            $frequencia += $dadosMes['empresa']['Frequencia'];

            $totalGrupo += $dadosMes['grupo']['Quantidade'];
            $valorTotalGrupo += $dadosMes['grupo']['ValorTotal'];
            $frequenciaGrupo += $dadosMes['grupo']['Frequencia'];
        }
        unset($dadosMes);

        // ---------------------------------------------------------------------------------
        // 3) BASE ÚNICA PARA CURVA ABC (VALOR E QUANTIDADE)
        //    Agrupa por produto (D049_D001_Id) e já traz as curvas
        // ---------------------------------------------------------------------------------
        $sqlCurvas = mysql_query("
            SELECT
                D049_D001_Id,
                D049_Flag_Curva_ABC,
                D049_Flag_Curva_ABC_Qtd,
                SUM(T008_Valor_Preco_Unitario * T008_Quantidade) AS valorTotal,
                SUM(T008_Quantidade) AS qtdTotal
            FROM T007
            INNER JOIN D006 ON D006_Id = T007_D006_Id
            INNER JOIN T008 ON T008_T007_Id = T007_Id
            INNER JOIN D009 ON D009_Id = T008_D009_Id
            INNER JOIN D049 ON D049_Id = D009_D049_Id
            WHERE T007_Data_Emissao >= '{$xLast12}'
            AND T007_Flag_Cancelada != 'S'
            AND D006_Flag_Venda_Compra_Outros = 'V'
            GROUP BY
                D049_D001_Id,
                D049_Flag_Curva_ABC,
                D049_Flag_Curva_ABC_Qtd
        ");

        $resumoValor = [
            'A' => ['totalItens' => 0, 'valorMax' => null, 'valorMin' => null],
            'B' => ['totalItens' => 0, 'valorMax' => null, 'valorMin' => null],
            'C' => ['totalItens' => 0, 'valorMax' => null, 'valorMin' => null],
        ];

        $resumoQtd = [
            'A' => ['qtdItens' => 0, 'qtdMax' => null, 'qtdMin' => null],
            'B' => ['qtdItens' => 0, 'qtdMax' => null, 'qtdMin' => null],
            'C' => ['qtdItens' => 0, 'qtdMax' => null, 'qtdMin' => null],
        ];

        $totalVendido = 0;
        $qtdTotalVendida = 0;

        while ($row = mysql_fetch_array($sqlCurvas)) {
            $curvaValor = trim((string)$row['D049_Flag_Curva_ABC']);
            $curvaQtd = trim((string)$row['D049_Flag_Curva_ABC_Qtd']);
            $valorItem = (float)$row['valorTotal'];
            $qtdItem = (float)$row['qtdTotal'];

            $totalVendido += $valorItem;
            $qtdTotalVendida += $qtdItem;

            if (isset($resumoValor[$curvaValor])) {
                $resumoValor[$curvaValor]['totalItens']++;

                if ($resumoValor[$curvaValor]['valorMax'] === null || $valorItem > $resumoValor[$curvaValor]['valorMax']) {
                    $resumoValor[$curvaValor]['valorMax'] = $valorItem;
                }
                if ($resumoValor[$curvaValor]['valorMin'] === null || $valorItem < $resumoValor[$curvaValor]['valorMin']) {
                    $resumoValor[$curvaValor]['valorMin'] = $valorItem;
                }
            }

            if (isset($resumoQtd[$curvaQtd])) {
                $resumoQtd[$curvaQtd]['qtdItens']++;

                if ($resumoQtd[$curvaQtd]['qtdMax'] === null || $qtdItem > $resumoQtd[$curvaQtd]['qtdMax']) {
                    $resumoQtd[$curvaQtd]['qtdMax'] = $qtdItem;
                }
                if ($resumoQtd[$curvaQtd]['qtdMin'] === null || $qtdItem < $resumoQtd[$curvaQtd]['qtdMin']) {
                    $resumoQtd[$curvaQtd]['qtdMin'] = $qtdItem;
                }
            }
        }

        $CurvaA = abreviarNumero($totalVendido * $pCurvaA);
        $CurvaB = abreviarNumero($totalVendido * $pCurvaB);
        $CurvaC = abreviarNumero($totalVendido * $pCurvaC);

        $CurvaQtdA = abreviarNumero($qtdTotalVendida * $pCurvaA);
        $CurvaQtdB = abreviarNumero($qtdTotalVendida * $pCurvaB);
        $CurvaQtdC = abreviarNumero($qtdTotalVendida * $pCurvaC);

        $valorMaxCurvaA = abreviarNumero($resumoValor['A']['valorMax']);
        $valorMinCurvaA = abreviarNumero($resumoValor['A']['valorMin']);
        $valorMaxCurvaB = abreviarNumero($resumoValor['B']['valorMax']);
        $valorMinCurvaB = abreviarNumero($resumoValor['B']['valorMin']);
        $valorMaxCurvaC = abreviarNumero($resumoValor['C']['valorMax']);
        $valorMinCurvaC = abreviarNumero($resumoValor['C']['valorMin']);

        $qtdMaxCurvaA = round((float)abreviarNumero($resumoQtd['A']['qtdMax']));
        $qtdMinCurvaA = round((float)abreviarNumero($resumoQtd['A']['qtdMin']));
        $qtdMaxCurvaB = round((float)abreviarNumero($resumoQtd['B']['qtdMax']));
        $qtdMinCurvaB = round((float)abreviarNumero($resumoQtd['B']['qtdMin']));
        $qtdMaxCurvaC = round((float)abreviarNumero($resumoQtd['C']['qtdMax']));
        $qtdMinCurvaC = round((float)abreviarNumero($resumoQtd['C']['qtdMin']));

        $totalItens = $resumoValor['A']['totalItens'] + $resumoValor['B']['totalItens'] + $resumoValor['C']['totalItens'];
        $qtdItens = $resumoQtd['A']['qtdItens'] + $resumoQtd['B']['qtdItens'] + $resumoQtd['C']['qtdItens'];

        $totalFmt = gCorrigeNumero($total);
        $valorTotalFmt = gCorrigeNumero($valorTotal);
        $totalGrupoFmt = gCorrigeNumero($totalGrupo);
        $valorTotalGrupoFmt = gCorrigeNumero($valorTotalGrupo);
        $rowTotalVendidoFmt = gCorrigeNumero($totalVendido);
        $rowTotalQtdVendidaFmt = gCorrigeNumero($qtdTotalVendida);

        $html = <<<EOT
    <style>
        .columnB {
            border-bottom: 1px solid gray;
            padding: 3px !important;
        }
        .columnBL {
            border-bottom: 1px solid gray;
            border-left: 1px solid gray;
            padding: 3px !important;
        }
        .columnBR {
            border-bottom: 1px solid gray;
            border-right: 1px solid gray;
            padding: 3px !important;
        }
        .columnBLR {
            border-left: 1px solid gray;
            border-bottom: 1px solid gray;
            border-right: 1px solid gray;
            padding: 3px !important;
        }
        .columnT {
            border-top: 1px solid gray;
            padding: 3px !important;
        }
        .columnTL {
            border-top: 1px solid gray;
            border-left: 1px solid gray;
            padding: 3px !important;
        }
        .columnTR {
            border-top: 1px solid gray;
            border-right: 1px solid gray;
            padding: 3px !important;
        }
        .columnTLR {
            border-left: 1px solid gray;
            border-top: 1px solid gray;
            border-right: 1px solid gray;
            padding: 3px !important;
        }
    </style>

    <table cellspacing="0" cellpadding="2" border="0" style="margin: auto; float: left; margin-bottom: 10px; width: 99%;">
        <tr>
            <td colspan="2" style="font-weight:bold">
                {$rowD009['D001_Codigo_Produto']} - {$rowD009['D001_Descricao_Produto']}
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <table cellspacing="0" cellpadding="2" style="border: 2px solid grey; margin: auto; width: 100%; float: left; margin-bottom: 10px;">
                    <tr>
                        <td class="columnBR" rowspan="2" style="font-weight: bold; text-align: center;">Mês</td>
                        <td class="columnBLR" colspan="3" style="font-weight: bold; text-align: center;">Local</td>
                        <td class="columnBL" colspan="3" style="font-weight: bold; text-align: center;">Grupo</td>
                    </tr>
                    <tr>
                        <td class="columnBLR" style="font-weight: bold; text-align: center; padding: 1px 5px;">Qtde.</td>
                        <td class="columnBR" style="font-weight: bold; text-align: center; padding: 1px 5px;">R$</td>
                        <td class="columnBR" style="font-weight: bold; text-align: center; padding: 1px 5px;">Freq.</td>
                        <td class="columnBL" style="font-weight: bold; text-align: center; padding: 1px 5px;">Qtde.</td>
                        <td class="columnBL" style="font-weight: bold; text-align: center; padding: 1px 5px;">R$</td>
                        <td class="columnBL" style="font-weight: bold; text-align: center; padding: 1px 5px;">Freq.</td>
                    </tr>
EOT;

        foreach ($meses as $dadosMes) {
            $empresaQtd = gCorrigeNumero($dadosMes['empresa']['Quantidade']);
            $empresaValor = gCorrigeNumero($dadosMes['empresa']['ValorTotal']);
            $empresaFreq = $dadosMes['empresa']['Frequencia'];

            $grupoQtd = gCorrigeNumero($dadosMes['grupo']['Quantidade']);
            $grupoValor = gCorrigeNumero($dadosMes['grupo']['ValorTotal']);
            $grupoFreq = $dadosMes['grupo']['Frequencia'];

            $html .= <<<EOT
                    <tr>
                        <td class="columnTR">{$dadosMes['titulo']}</td>
                        <td class="columnTLR" style="text-align: right;">{$empresaQtd}</td>
                        <td class="columnTR" style="text-align: right;">{$empresaValor}</td>
                        <td class="columnTR" style="text-align: right;">{$empresaFreq}</td>
                        <td class="columnTL" style="text-align: right;">{$grupoQtd}</td>
                        <td class="columnTL" style="text-align: right;">{$grupoValor}</td>
                        <td class="columnTL" style="text-align: right;">{$grupoFreq}</td>
                    </tr>
EOT;
        }

        if ($r_extra == 'exibirTotal') {
            $html .= <<<EOT
                    <tr>
                        <td class="columnTR" style="font-weight: bold;">Total</td>
                        <td class="columnTLR" style="font-weight: bold; text-align: right;">{$totalFmt}</td>
                        <td class="columnTR" style="font-weight: bold; text-align: right;">{$valorTotalFmt}</td>
                        <td class="columnTR" style="font-weight: bold; text-align: right;">{$frequencia}</td>
                        <td class="columnTL" style="font-weight: bold; text-align: right;">{$totalGrupoFmt}</td>
                        <td class="columnTL" style="font-weight: bold; text-align: right;">{$valorTotalGrupoFmt}</td>
                        <td class="columnTL" style="font-weight: bold; text-align: right;">{$frequenciaGrupo}</td>
                    </tr>
EOT;
        }

        $html .= <<<EOT
                </table>
            </td>
            <td style="height:0px">
                <table cellspacing="0" style="float: left; margin-left: 10px; border: 2px solid grey; width: 100%">
                    <tr>
                        <td class="columnB" colspan="5" style="text-align: center; font-weight: bold;">Curva ABC Grupo - Valor</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">A ({$parametrosCurvaABC[0]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaA} ({$resumoValor['A']['totalItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$valorMaxCurvaA} à {$valorMinCurvaA}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">B ({$parametrosCurvaABC[1]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaB} ({$resumoValor['B']['totalItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$valorMaxCurvaB} à {$valorMinCurvaB}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">C ({$parametrosCurvaABC[2]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaC} ({$resumoValor['C']['totalItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$valorMaxCurvaC} à {$valorMinCurvaC}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="font-weight: bold; text-align: center;">{$somaParametros}%</td>
                        <td class="columnTL" colspan="4" style="font-weight: bold; text-align: center;">R$ {$rowTotalVendidoFmt} ({$totalItens} itens)</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <table cellspacing="0" style="float: left; margin-left: 10px; border: 2px solid grey; width: 100%;" valign="top">
                    <tr>
                        <td class="columnB" colspan="5" style="text-align: center; font-weight: bold;">Curva ABC Grupo - Quantidade</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">A ({$parametrosCurvaABC[0]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaQtdA} ({$resumoQtd['A']['qtdItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$qtdMaxCurvaA} à {$qtdMinCurvaA}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">B ({$parametrosCurvaABC[1]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaQtdB} ({$resumoQtd['B']['qtdItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$qtdMaxCurvaB} à {$qtdMinCurvaB}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="text-align: center;">C ({$parametrosCurvaABC[2]}%)</td>
                        <td class="columnTLR" colspan="2" style="text-align: center;">{$CurvaQtdC} ({$resumoQtd['C']['qtdItens']} itens)</td>
                        <td class="columnT" colspan="2" style="text-align: center;">{$qtdMaxCurvaC} à {$qtdMinCurvaC}</td>
                    </tr>
                    <tr>
                        <td class="columnTR" style="font-weight: bold; text-align: center;">{$somaParametros}%</td>
                        <td class="columnTL" colspan="4" style="font-weight: bold; text-align: center;">{$rowTotalQtdVendidaFmt} ({$qtdItens} itens)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
EOT;

        echo $html;
    break;

    case 'demonstrativoPrecoVenda':
        global $g;
        $origem = '';
        if ($r_extra == 'T00X') {
            $tmp = explode('-', $r_acaoId);
            $r_extra = $tmp[0];
            $r_acaoId = $tmp[1];
        }
        $array = array();
        if ($r_extra == 'T004') {
            $sql = mysql_query("SELECT T004_Demonstrativo_Preco_Venda, T004_T219_Id FROM T004 LEFT JOIN T003 ON T003_Id=T004_T003_Id WHERE T004_Id = '{$r_acaoId}'");
            $row = mysql_fetch_assoc($sql);
            $demonstrativo = unserialize($row['T004_Demonstrativo_Preco_Venda']);
            $array['todosCampos']['T004_Id'] = $r_acaoId;
            $array['T219_Id'] = $row['T004_T219_Id'];
            $array['PK'] = 'T004_Id';
            $array['tabela'] = $r_extra;
        } else if ($r_extra == 'T006') {
            $sql = mysql_query("SELECT T006_Demonstrativo_Preco_Venda, T006_T219_Id FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_Id = '{$r_acaoId}'");
            $row = mysql_fetch_assoc($sql);
            $demonstrativo = unserialize($row['T006_Demonstrativo_Preco_Venda']);
            $array['todosCampos']['T006_Id'] = $r_acaoId;
            $array['T219_Id'] = $row['T006_T219_Id'];
            $array['PK'] = 'T006_Id';
            $array['tabela'] = $r_extra;
        } else if ($r_extra == 'D009_Real') {
            $sql = "SELECT D009_Preco_Loja_Real_Demonstrativo, D009_Preco_Loja_Real, D009_Preco_Loja_Real_T001_Id, D009_Id FROM D009 WHERE D009_Id = '{$r_acaoId}'";
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $preco = gCorrigeNumero($row['D009_Preco_Loja_Real'],2);
            $precoCompra = gCorrigeNumero($row['D009_Preco_Loja_Real'],2);
            $T001_Id = $row['D009_Preco_Loja_Real_T001_Id'];
            $D009_Id = $row['D009_Id'];
            $demonstrativo = unserialize($row['D009_Preco_Loja_Real_Demonstrativo']);
            if (empty($row['D009_Preco_Loja_Real_Demonstrativo'])) {
                echo "Demonstrativo de preço não disponível.";
                die();
            }
        }

        echo montarDemonstrativo($demonstrativo, $array);
    break;

    case 'demonstrativoPrecoTransferencia':
        $demonstrativo = unserialize(base64_decode($r_extra));
        echo montarDemonstrativo($demonstrativo);
    break;

    case 'demonstrativoPrecoTabelaFornecedor':
        $extra = unserialize(base64_decode($r_extra));
        echo montarDemonstrativoSimples($extra[1], $extra[0]);
    break;

    case 'ven001DemonstrativoIPV':

        $sqlT003 = mysql_query("SELECT T003_Id,T003_Valor_Total_Produtos, T003_Flag_Lista_Preco, T003_Valor_Total_ICMS, T003_Valor_Total_IRPJ, T003_Valor_Total_CSLL, T003_Valor_Total_PIS, T003_Valor_Total_COFINS, T003_Valor_Total_Produtos FROM T003 WHERE T003_Id = '{$r_acaoId}'");
        $mT003 = mysql_fetch_array($sqlT003);

        $impostos = 0;
        $impostos += ($g['C031']['somarICMSPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_ICMS'] : 0;
        $impostos += ($g['C031']['somarPISPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_PIS'] : 0;
        $impostos += ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_COFINS'] : 0;
        $impostos += ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_IRPJ'] : 0;
        $impostos += ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? $mT003['T003_Valor_Total_CSLL'] : 0;

        $percentualImpostos = ($impostos / $mT003['T003_Valor_Total_Produtos']) * 100;
        
        $T003_Valor_Total_Produtos = $mT003['T003_Valor_Total_Produtos'] - $impostos;

        $sqlT004 = mysql_query("SELECT SUM(T004_Valor_Custo_Unitario*T004_Quantidade) custo from T004 left join D009 on D009_Id=T004_D009_Id where T004_T003_Id='{$r_acaoId}'");
        $mT004 = mysql_fetch_array($sqlT004);
        $custo = $mT004['custo'];



        $ipvSemLucroQuery = mysql_query("SELECT SUM((if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100))) as ipvSemLucro from T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id
            WHERE T250_T003_Id = '{$r_acaoId}' and ifnull(T250_Tipo,0)!='3' and IFNULL(T250_Tipo,0)!='4' AND T250_Grupo='IPV'");
        $ipvSemLucro = mysql_fetch_array($ipvSemLucroQuery);


        $ipvLucroQuery = mysql_query("SELECT SUM((if(T250_Grupo='IPV',$T003_Valor_Total_Produtos,$custo)*(T250_Aliquota/100))) as IpvLucro from T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id WHERE T250_T003_Id = '{$r_acaoId}' and ifnull(T250_Tipo,0)='3' AND T250_Grupo='IPV'");
        $ipvLucro = mysql_fetch_array($ipvLucroQuery);


        $aliquotasIPVQuery = mysql_query("SELECT sum(T250_Aliquota) as aliquotasIPV from T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='IPV' and ifnull(T250_Tipo,'')!=4");
        $aliquotasIPV = mysql_fetch_array($aliquotasIPVQuery);


        $aliquotasIPVSemLucroQuery = mysql_query("SELECT sum(T250_Aliquota) as aliquotasIPVSemLucro from T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id WHERE T250_T003_Id = '{$r_acaoId}' and ifnull(T250_Tipo,0)!='3' and IFNULL(T250_Tipo,0)!='4' AND T250_Grupo='IPV'");
        $aliquotasIPVSemLucro = mysql_fetch_array($aliquotasIPVSemLucroQuery);


        $IPVTributosQuery = mysql_query("SELECT sum(T250_Aliquota) as IPVTributos from T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='IPV' and T250_Tipo='4'");
        $IPVTributos = mysql_fetch_array($IPVTributosQuery);
       

       $ICFQuery = mysql_query("SELECT (100/(100-SUM(T250_Aliquota))) AS ICF FROM T250 LEFT JOIN T003 ON T003_Id=T250_T003_Id WHERE T250_T003_Id = '{$r_acaoId}' and T250_Grupo='ICF'");
       $ICF = mysql_fetch_array($ICFQuery);
     

        if ($IPVTributos<=0){
            $IPVTributos = ($tributos / $mT003['T003_Valor_Total_Produtos']) * 100;
        }    

        $IPV         = round((100 / ( 100 - $aliquotasIPV['aliquotasIPV'] )) , 4 );
        $IPVSL       = round((100 / ( 100 - $aliquotasIPVSemLucro['aliquotasIPVSemLucro'] )) , 4 );
        $total       = $T003_Valor_Total_Produtos + $impostos;
        $valorLucro  = $T003_Valor_Total_Produtos - $custo - $ipvSemLucro['ipvSemLucro'];
        $valorMinimo = ( $custo * $IPV );
        $valorMinimoSL = ( $custo * $IPVSL );

        $IPVTributos = gCorrigeNumero( (100 / ( 100 - $IPVTributos['IPVTributos'] )) , 4 );
        $ICF         = gCorrigeNumero($ICF['ICF'], 4);

        $impostos        = gCorrigeNumero($impostos);
        $percentualImpostos = gCorrigeNumero($percentualImpostos);
        $ipvSemLucro     = gCorrigeNumero($ipvSemLucro['ipvSemLucro']);
        $percentualLucro = gCorrigeNumero(($valorLucro/$T003_Valor_Total_Produtos)*100);
        $percentualCusto = gCorrigeNumero(($custo/$T003_Valor_Total_Produtos)*100);
        $custo           = gCorrigeNumero($custo);
        $valorLucro      = gCorrigeNumero($valorLucro);
        $total           = gCorrigeNumero($total);
        $valorMinimo     = gCorrigeNumero($valorMinimo);
        $valorMinimoSL   = gCorrigeNumero($valorMinimoSL);
        $T003_Valor_Total_Produtos = gCorrigeNumero($T003_Valor_Total_Produtos);



        if($g['nivelAlcada']==4){        
            echo <<<EOT
                <span>
                    <table id='totais' style="font-size:14px;border:1px solid silver" align="center" width='360px' cellpadding='2px'>
                        <tr style="font-weight:bold;">
                            <td>Produtos</td><td align=right>R$ {$total}</td>
                        </tr>
                        <tr>
                            <td>Tributos </td><td align=right>R$ {$impostos}</td>
                            <td align=right>{$percentualImpostos}%</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Produtos-Tributos</td><td align=right>R$ {$T003_Valor_Total_Produtos}</td>
                        </tr>
                        <tr id='custo'>
                            <td id='custo'>Custo</td><td align=right>R$ {$custo}</td>
                            <td align=right>{$percentualCusto}%</td>
                        </tr>
                        <tr>
                            <td>IPV - Lucro</td><td align=right>R$ {$ipvSemLucro}</td>
                            <td align=right>{$aliquotasIPVSemLucro['aliquotasIPVSemLucro']}%</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Lucro</td><td align=right>R$ {$valorLucro}</td>
                            <td align=right>{$percentualLucro}%</td>
                        </tr>                       
                    </table>
                </pan>
EOT;
        }

    break;

    case 'demonstrativoPrecoFornecedor':
        global $g;
        $origem = '';
        if ($r_extra == 'T00X') {
            $tmp = explode('-', $r_acaoId);
            $r_extra = $tmp[0];
            $r_acaoId = $tmp[1];
        }
        $array = array();
        if ($r_extra == 'T004') {
            $sql = mysql_query("SELECT T004_Demonstrativo_Preco_Tabela, T004_Demonstrativo_Custo_Tabela, T004_Valor_Preco_Tabela FROM T004 LEFT JOIN T003 ON T003_Id=T004_T003_Id WHERE T004_Id = '{$r_acaoId}'");
            $row = mysql_fetch_assoc($sql);
            $demonstrativo = unserialize($row['T004_Demonstrativo_Preco_Tabela']);
            $preco = gCorrigeNumero($row['T004_Valor_Preco_Tabela'], 5);
            $demonstrativoCustoTabela = $row['T004_Demonstrativo_Custo_Tabela'];
        } else if ($r_extra == 'T006') {
            $sql = mysql_query("SELECT T006_Demonstrativo_Preco_Tabela, T006_Demonstrativo_Custo_Tabela, T006_Valor_Preco_Tabela FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id WHERE T006_Id = '{$r_acaoId}'");
            $row = mysql_fetch_assoc($sql);
            $demonstrativo = unserialize($row['T006_Demonstrativo_Preco_Tabela']);
            $preco = gCorrigeNumero($row['T006_Valor_Preco_Tabela'], 5);
            $demonstrativoCustoTabela = $row['T006_Demonstrativo_Custo_Tabela'];
        }

        echo $demonstrativoCustoTabela;
        echo montarDemonstrativoSimples($demonstrativo, $preco);
    break;

    case 'ven001CabecalhoItemMontado':
        $sql = mysql_query("SELECT D001_Codigo_Produto,
                                   D001_Descricao_Produto,
                                   D082_Marca,
                                   D037_Unidade,
                                   D009_Quantidade_Estoque,
                                   D009_Quantidade_Estoque_Liquido,
                                   D009_Quantidade_OC
                              FROM D009
                         LEFT JOIN D049 ON D049_Id=D009_D049_Id
                         LEFT JOIN D001 ON D001_Id=D049_D001_Id
                         LEFT JOIN D082 ON D082_Id=D049_D082_Id
                         LEFT JOIN D037 ON D037_Id=D001_D037_Id
                         WHERE D009_Id = '{$r_D009_Id}'");
        $row = mysql_fetch_assoc($sql);
        $row['D009_Quantidade_Estoque'] = gCorrigeNumero($row['D009_Quantidade_Estoque']);
        $row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
        $row['D009_Quantidade_OC'] = gCorrigeNumero($row['D009_Quantidade_OC']);
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc">
                        <td min-width='4%'>Código</td>
                        <td min-width='35%'>Descrição</td>
                        <td min-width='11%'>Marca</td>
                        <td min-width='5%'>Unidade</td>
                        <td min-width='5%'>Estoque</td>
                        <td min-width='5%'>Líquido</td>
                        <td min-width='7%'>O.C.</td>
                    </tr>
                    <tr style="font-size:1.1em; font-weight:bold;">
                        <td style='padding:1px 10px 4px 2px;'>{$row['D001_Codigo_Produto']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D001_Descricao_Produto']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D082_Marca']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D037_Unidade']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D009_Quantidade_Estoque']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D009_Quantidade_Estoque_Liquido']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D009_Quantidade_OC']}</td>
                    </tr>
                </table>
            </div>
EOT;
    break;
}

function montarDemonstrativo($demonstrativo, $array = array()){
    
    if(!empty($array)){
        $array['campoAtual'] = '<div class="icone ui-state-default ui-corner-all" style="position: absolute; margin-top: -32px; right: 11px;"><span class="ui-icon  ui-icon-comment"></span></div>';
    }

    $param = array(
        'Demonstrativo Preço de Venda',
        '/ven/ven001/outros/demonstrativoPrecoVenda/', 
        $array['PK'], 
        'right', 
        false, 
        $array['tabela'], 
        '', 
        ($array['T219_Id'] > 0) ? '675' : '450',
        'S'
    );
    require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');    

    $html = <<<EOT
        <script>
            $(function() {
                $('#divDemonstrativo h4').click(function() {
                    if($(this).prev().hasClass('cabDemonstrativo') || $('.popover').find('.arrow').offset() == null){
                        return false;
                    }
                    heightInicial = parseFloat($('.popover').css('height'));
                    if ($('.popover').find('.arrow').offset() != null) {
                        arrowInicial = $('.popover').find('.arrow').offset().top;
                    }
                    $(this).prev().toggle();
                    heightFinal = parseFloat($('.popover').css('height'));
                    var popoverHeight2 = parseFloat($('.popover').css('height'));
                    var contentHeight2 = parseFloat($('.popover').closest('.ui-tabs-panel').css('height'));
                    
                    var novoTop = $('.popover').position().top - ((heightFinal-heightInicial)/2);
                    var topUsado = '1 ' + novoTop;
                    if((novoTop + heightFinal) > contentHeight2){
                        $('.popover').data($(this).attr('id'), ((novoTop + heightFinal) - contentHeight2));
                        novoTop = novoTop - ((novoTop + heightFinal) - contentHeight2);                        
                        topUsado = '2 ' + novoTop;
                    } else if(heightInicial > heightFinal){                        
                        novoTop = $('.popover').position().top + ((heightInicial-heightFinal)/2);
                        if($('.popover').data($(this).attr('id')) > 0){
                            novoTop = novoTop + $('.popover').data($(this).attr('id'));
                        }
                        topUsado = '3 ' + novoTop;
                    }
                    $('.popover').css('top', novoTop);
                    arrowFinal = $('.popover').find('.arrow').offset().top;
                    
                    if (typeof($('.popover').find('.arrow').offset()) != null) {
                        $('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top + (arrowInicial - arrowFinal));
                    }

                    return false;
                });
                $('.contentCollapse').click(function() {
                    $(this).next().trigger('click');
                });
            });
        </script>
        <div id="divDemonstrativo" style="color: #000; margin-bottom: 2px;">
EOT;
    $tagAberta = false;
    foreach ($demonstrativo as $campo) {
        if ($campo['operacao'] == 'cab') {
            if(!empty($array)){
                $button = abrirNotificacao($array, $param);
            }
            $html .= <<<EOT
                <div class="cabDemonstrativo" style="width: 100%; float: left; font-size:11px; font-weight: bold; {$campo['css']}" onclick="{$campo['onclick']}">
                    {$campo['valor']}
                </div>
                {$button}
                
EOT;
            continue;
        }
        $campo['css'] = ($campo['exibir'] == 'N') ? 'color: gray;' : $campo['css'];
        
        if($campo['tipo'] == 'total' || $campo['tipo'] == 'fixo'){
            if($tagAberta == true){
                $html .= '</div><h4 id="h4-'.uniqid().'" style="border: 0px; font-weight: bold; margin: 0px;">';
            } else {
                $html .= '<h4 id="h4-'.uniqid().'" style="border: 0px; font-weight: bold; margin: 0px;">';
            }
            $tagAberta = true;
        }
        
        $campo['operacao'] = (empty($campo['operacao'])) ? '&nbsp;' : $campo['operacao'];
        $html .= <<<EOT
                <div style="width:11px; float: left; font-size:11px; padding: 2px 0px; {$campo['css']}">
                    {$campo['operacao']}
                </div>
                <div style="width:120px; float: left; font-size:11px; padding: 2px 0px; {$campo['css']}">
                    {$campo['campo']}
                </div>
                <div style='width:75px; float: left; font-size:11px; padding: 2px 0px; text-align: right; {$campo['css']}'>
                    {$campo['valor']}
                </div>
EOT;
        if($campo['tipo'] == 'total' || $campo['tipo'] == 'fixo'){
            $html .= '</h4><div class="contentCollapse" style="border: 0; background: none; color: #000; padding: 0px; width: 100%; float: left;">';
        }
    }
    return $html;
}

function montarDemonstrativoSimples($demonstrativo, $preco){
    $html .= "<td style='width: 210px;'><table cellpadding=0 cellspacing=0 border=0 style='float: left; margin-top: 10px; width: 100%;'>";
    $index = 0;
    foreach ($demonstrativo as $campo) {
        $border = '';
        if($index == 0){
            $index++;
            continue;
        }
        $valor = gCorrigeNumero($campo['valor'],5);
        if ($campo['campo'] == 'Custo Compra' || $campo['campo'] == 'Preço Bruto') {
            $border = 'border-bottom: 1px solid black;';
            //$valor = "<a href=\"#\" onclick=\"nova_janela('/hardness/empresas/E003/externos/T001_demonstracao_custo.php3?Abre_Janela=N&T001_Id={$T001_Id}',150,300,450,400,'yes','no','no','no','yes','no','Teste')\">$valor</a>";
        }
        if($campo['campo'] == 'Custo Líquido' || $campo['campo'] == 'Custo Bruto' || $campo['campo'] == 'Preço Bruto'){
            $valor = "<b>".$valor."</b>";
            $campo['campo'] = "<b>".$campo['campo']."</b>";
        }
        $cssLinha = ($campo['exibir'] == 'N') ? 'color: gray;' : '';
        $html .= <<<EOT
            <tr>
                <td style="padding: 2px 2px;font-size:11px;{$cssLinha}{$border}">
                    {$campo['operacao']}
                </td>
                <td style="padding: 2px 2px;font-size:11px;{$cssLinha}{$border}">
                    {$campo['campo']}
                </td>
                <td style='text-align: right; padding: 2px 2px;font-size:11px;{$cssLinha}{$border}'>
                    {$valor}
                </td>
            </tr>
EOT;
    }
    $html .= <<<EOT
            <tr>
                <td style="border-top: 1px solid black; padding: 2px 2px;">
                    =
                </td>
                <td style="border-top: 1px solid black; padding: 2px 2px;">
                    Preço fornecedor
                </td>
                <td style="border-top: 1px solid black; text-align: right; padding: 2px 2px;">
                    {$preco}
                </td>
            </tr>
            </table>
        </td>
EOT;
    return $html;
}
