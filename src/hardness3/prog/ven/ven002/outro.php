<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: ven001TabEditarOrcamento
	 */
	case 'ven002CabecalhoOrcamento':
		// Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T005
        left join D024 on D024_Id = T005_D024_Id
        left join D020 on D020_Id = D024_D020_Id
        left join D018 on D018_Id = D024_D018_Id
        left join C007 on C007_Id = D024_C007_Vendedor_Interno
        left join D030 on D030_Id = D024_D030_Id
        WHERE
            (T005_Id='$r_acaoId')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_array($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (trim($row_)=="") { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        } 
        
        $possuiRestricoes = "";
        if($row['D024_Flag_Liberado']=="N" || $row['D024_Flag_Restricao'] == "S"){
            $possuiRestricoes = "<font style='color: var(--BGVermelho);'><b>RESTRIÇÕES</b></font>";
        }else{
            $possuiRestricoes = "<font style='color: var(--BGVerde);'><b>LIBERADO</b></font>";
        }
        if($row['D024_Flag_Contribuinte']=="0"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>NÃO CONTRIB.</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="1"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>CONSUM.FINAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="2"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>REVENDA</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="3"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>PROD.RURAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="4"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>SIMPLES</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="5"){
            $Contribuinte = "<font style='color: var(--BGAzul);'><b>INDUSTRIA</b></font>";
        } else if ($row['D024_Flag_Contribuinte']==""){
            $Contribuinte="<font style='color: var(--BGVermelho);'>FALTA</font>";            
        }
        
        $dataEmissao = gCorrigeData($row['T005_Data_Emissao']);
        if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 35);
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 35);
        }
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 16);
        $row['D024_Telefone_1'] = str_replace(array(' ','-','.'), '', $row['D024_Telefone_1']);
        $D024_Telefone = gTruncar($row['D024_DDD_Telefone_1'].' '.$row['D024_Telefone_1'], 20);
        
        switch($row['T005_Flag_Lista_Preco']){
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
                $listaPreco="<font style='color: var(--BGVermelho);'><b>FALTA</b></font>";
        }

        switch($row['T005_Flag_Destino_Produto']){
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
                $destino="<font style='color: var(--BGVermelho);'><b>FALTA</b></font>";
        }

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
       
        //onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + '{$row['D024_Id']}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
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
                        <td style='padding:1px 5px 4px 2px;' align='center'>{$row['T005_Id']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D024_Id']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$D024_Nome_Empresa}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$D024_Telefone}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$D020_Nome_Cidade}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$row['D018_UF']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$dataEmissao}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$Contribuinte}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$possuiRestricoes}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$listaPreco}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$destino}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$ultimaCompra}</td>
                    </tr>
                </table>
            </div>
EOT;

	break;
	
	case 'ven002observacoes':
		global $g;
        $T005_Id   = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;

        mysql_query("call T005_Gravar_Totalizacao_4($T005_Id);");

        $T005 = "SELECT T005_Id,
                        T005_Valor_Total_Produtos,
                        T005_Flag_Frete,
                        T005_Valor_Frete,
                        T005_Valor_Despesas_Acessorias,
                        T005_Valor_Total_Custo,
                        T005_Valor_Total_ICMS,
                        T005_Valor_Total_IPI,
                        T005_Valor_Total,
                        T005_IPV,
                        T005_Percentual_Margem,
                        T005_Peso_Liquido,
                        T005_Peso_Bruto,
                        T005_Percentual_Comissao,
                        T005_Valor_Total_Comissao,
                        T005_Valor_Frete_Calculado,
                        T005_Valor_Desconto,
                        T005_Valor_Desconto_Manual,
                        T005_Valor_ICMS_Substituicao_Retencao,
                        T005_Flag_Status,
                        T005_Flag_Status_Cotacao,
                        T005_Valor_Total_Produtos_Sem_Desconto,
                        T005_Aliquota_ICMS_Credito,
                        T005_Valor_Total_ICMS_Credito,
                        T005_Valor_Total_PIS,
                        T005_Valor_Total_COFINS,
                        T005_Valor_Total_IRPJ,
                        T005_Valor_Total_CSLL,
                        T005_Valor_Total_Partilha_ICMS_Destino,
                        T005_Valor_Total_Partilha_ICMS_Origem,
                        T005A_Valor_Total_FCP,
                        T005A_Valor_FCP_ST,
                        D024_Id,
                        D024_Observacao_Comercial,
                        D024_Observacao_Financeiro,
                        D024_Valor_Credito_Antecipado,
                        D024_Valor_Credito_Troca,
                        T005_Prazos(T005_Id) as Prazo, 
                        ROUND(SUM(T006_Valor_Total_Frete),2) AS FreteProdutos, 
						T005_Status_Pedido(T005_Flag_Status,1) as Status,
                        SUM(T006_Valor_Lucro) as Lucro,
                        Liberou.C007_Primeiro_Nome AS Liberou,
                        LiberouOrcamento.C007_Primeiro_Nome AS LiberouOrcamento,
                        SUM(T006_Quantidade) as quantidadeTotal,
                        SUM(T006_Cubagem_Unitaria*T006_Quantidade) as cubagemTotal
                   FROM T005
              LEFT JOIN D024 ON D024_Id = T005_D024_Id
              LEFT JOIN T006 ON T006_T005_Id = T005_Id
              LEFT JOIN T003 ON T003_Id = T005_T003_Id
              LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberou_Alcada_C007_Id
              LEFT JOIN C007 AS LiberouOrcamento ON LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
              LEFT JOIN T005A ON T005_Id = T005A_T005_Id
                  WHERE T005_Id='{$T005_Id}'";

        $T005  = mysql_query($T005);
        $mT005 = mysql_fetch_array($T005);

        $D024_Id = $mT005['D024_Id'];

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

        $valorLimite     = $dadosCliente['D024_Valor_Limite'];

        //Calcula saldo do cliente
        $sqlSaldo = "SELECT IFNULL(sum(IF(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) AS Vencido,
                            IFNULL(sum(IF(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) AS Vencer
                       FROM T002
                  LEFT JOIN D024 ON D024_Id=T002_D024_Id
                      WHERE $filtroSaldo
                        AND T002_Data_Recebimento='0000-00-00'
                        AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";

        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo  = mysql_fetch_array($resultSaldo);

/*         $sqlAtraso = mysql_query("SELECT SUM(T002_Valor_Total) as Atraso
                                 FROM T002
                                 LEFT JOIN D024 ON D024_Id = T002_D024_Id
                                 WHERE $filtroSaldo 
                                 and T002_Data_Recebimento = '0000-00-00'
                                 and T002_Data_Vencimento < CURDATE()
                                 and (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado is null)");

        $linhaAtraso = mysql_fetch_array($sqlAtraso); */
        $linhaAtraso['Atraso'] = gCorrigeNumero($linhaSaldo['Vencido']);

        //Verifica os pedidos em aberto
        $sqlPedidos   = mysql_query("SELECT IFNULL(sum(T005_Valor_Total),0) AS Pedidos
                                       FROM T005 
                                  LEFT JOIN T007 ON T007_T005_Id=T005_Id 
                                  LEFT JOIN D024 ON D024_Id=T005_D024_Id
                                      WHERE $filtroSaldo
                                        AND T005_Flag_Status !=8
                                        AND (T005_T005_Id_Agrupado<=0 or T005_T005_Id_Agrupado is null) 
                                        AND T007_Id IS NULL");
        $linhaPedidos = mysql_fetch_array($sqlPedidos);    
        
        // CALCULA SALDO LIMITE
        $saldoAtual  = ($valorLimite-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$linhaPedidos['Pedidos']));
        $saldoAtual  = gCorrigeNumero($saldoAtual);
        $valorLimite = gCorrigeNumero($valorLimite);

        if($mT005['T005_Valor_Total_Produtos'] > 0 && $g['C031']['ratearFreteVendaNoCusto'] == 'S' && $mT005['T005_Flag_Frete'] == '0' && $mT005['FreteProdutos'] != $mT005['T005_Valor_Frete']) {
            $mT005['T005_Valor_Frete'] = '<span style="color: var(--BGVermelho)">Atualizar preço tabela</span>';
        } else {
            $mT005['T005_Valor_Frete'] = gCorrigeNumero($mT005['T005_Valor_Frete']);
        }  
        
        $mT005['T005_Valor_Despesas_Acessorias']=gCorrigeNumero($mT005['T005_Valor_Despesas_Acessorias']);

        $obsComercial                           = substr($mT005['D024_Observacao_Comercial'], 0, 140);
        $obsFinanceiro                          = substr($mT005['D024_Observacao_Financeiro'], 0, 180);
        $lucro                                  = gCorrigeNumero(($mT005['Lucro']/$mT005['T005_Valor_Total_Produtos'])*100);
        $mT005['Lucro']                         = gCorrigeNumero($mT005['Lucro']);
        $ipv                                    = gCorrigeNumero($mT005['T005_Valor_Total_Produtos']/$mT005['T005_Valor_Total_Custo'],4);
        $margem                                 = gCorrigeNumero((($mT005['T005_Valor_Total_Produtos']-$mT005['T005_Valor_Total_Custo'])/$mT005['T005_Valor_Total_Produtos'])*100);
        $mT005['T005_Valor_Total_Produtos']     = gCorrigeNumero($mT005['T005_Valor_Total_Produtos']);
        $mT005['T005_Valor_Total_Custo']        = gCorrigeNumero($mT005['T005_Valor_Total_Custo']);
        $valorTotalICMS                         = gCorrigeNumero($mT005['T005_Valor_Total_ICMS'] + $mT005['T005A_Valor_Total_FCP']);
        $mT005['T005_Valor_Total_ICMS']         = gCorrigeNumero($mT005['T005_Valor_Total_ICMS']);
        $mT005['T005A_Valor_Total_FCP']         = gCorrigeNumero($mT005['T005A_Valor_Total_FCP']);
        $mT005['T005_Valor_Total_ICMS_Credito'] = gCorrigeNumero($mT005['T005_Valor_Total_ICMS_Credito']);
        $mT005['T005_Valor_Total_IPI']          = gCorrigeNumero($mT005['T005_Valor_Total_IPI']);
        $mT005['T005_Valor_ST']                 = gCorrigeNumero($mT005['T005_Valor_ST']);
        $mT005['T005_Valor_Total']              = gCorrigeNumero($mT005['T005_Valor_Total']);
        $mT005['T005_IPV']                      = gCorrigeNumero($mT005['T005_IPV'],4);
        $mT005['T005_Percentual_Margem']        = gCorrigeNumero($mT005['T005_Percentual_Margem'],2);
        $mT005['T005_Valor_Desconto_Manual']    = gCorrigeNumero($mT005['T005_Valor_Desconto_Manual']*-1);
        $mT005['quantidadeTotal']               = gCorrigeNumero($mT005['quantidadeTotal']);
        $mT005['cubagemTotal']                  = gCorrigeNumero($mT005['cubagemTotal']);
        $mT005['pesoLiquido']                   = gCorrigeNumero($mT005['T005_Peso_Liquido']);
        $mT005['pesoBruto']                     = gCorrigeNumero($mT005['T005_Peso_Bruto']);
        $mT005['T005_Percentual_Comissao']      = gCorrigeNumero($mT005['T005_Percentual_Comissao']);
        $mT005['T005_Valor_Total_Comissao']     = gCorrigeNumero($mT005['T005_Valor_Total_Comissao']);
        $mT005['T005_Valor_Frete_Calculado']    = gCorrigeNumero($mT005['T005_Valor_Frete_Calculado']);
        $valorCreditoAntecipado                 = gCorrigeNumero($mT005['D024_Valor_Credito_Antecipado']);
        $valorCreditoTroca                      = gCorrigeNumero($mT005['D024_Valor_Credito_Troca']);
        
        $percentualDesconto = gCorrigeNumero(($mT005['T005_Valor_Desconto']/$mT005['T005_Valor_Total_Produtos_Sem_Desconto'])*100,2);
        require_once("bibliotecas/classes/VEN002.php");
        $VEN002 = new VEN002();
        $percentualDesconto = $VEN002->ven002ModObservacaoPercentualDesconto($percentualDesconto, $mT005);


        $percentualDescontoManual = gCorrigeNumero(( ($mT005['T005_Valor_Desconto_Manual'] * -1) / $mT005['T005_Valor_Total_Produtos_Sem_Desconto'] )*100,2);

        //$valorOrcamento = $VEN001->calculaTotalGeral($T005_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        //$mT005['T005_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mT005['T005_Valor_Total_Produtos_Sem_Desconto']);
        $mT005['T005_Valor_Desconto'] = gCorrigeNumero($mT005['T005_Valor_Desconto']);

        $valorTotalST                                   = gCorrigeNumero($mT005['T005_Valor_ICMS_Substituicao_Retencao'] + $mT005['T005A_Valor_FCP_ST']);
        $mT005['T005_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT005['T005_Valor_ICMS_Substituicao_Retencao']);
        $mT005['T005A_Valor_FCP_ST']                    = gCorrigeNumero($mT005['T005A_Valor_FCP_ST']);
        $titleST = "ST: &nbsp; {$mT005['T005_Valor_ICMS_Substituicao_Retencao']}\nFCP ST: &nbsp; {$mT005['T005A_Valor_FCP_ST']}";

        
        require_once('bibliotecas/funcoesCallback/campoPrecoTabelaGridPedido.php');
        $array['todosCampos']['T005_Id']                  = $T005_Id;
        $array['todosCampos']['T005_Flag_Status']         = $mT005['T005_Flag_Status'];
        $array['todosCampos']['T005_Flag_Status_Cotacao'] = $mT005['T005_Flag_Status_Cotacao'];
        $array['campoAtual']                              = $mT005['T005_Valor_Total_Produtos_Sem_Desconto'];
        $valorPrecoTabelaPedido = str_replace('R$ ','',campoPrecoTabelaGridPedido($array, true));
        
        $onclick = '';
        if(isset($g['c029Ids'][215])){
            $onclick = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Demonstrativo resultado', '/ven/ven002/content/contentMontagemIPV/', '&acaoId={$T005_Id}', [400,620]);";
        }

        $heightComercial  = 60;
        $heightFinanceiro = 49;

        if(!(isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
            $heightComercial=110;
        }
        if((isset($g['c029Ids'][217])) && !(isset($g['c029Ids'][218]))){
            $heightFinanceiro=135;
        }

        $C004 = mysql_query("SELECT C004_Codigo_Regime_Tributario 
                               FROM C004 
                              WHERE C004_Id='{$g['empresaAtual']}'");

        $mC004 = mysql_fetch_array($C004);
        if($mC004['C004_Codigo_Regime_Tributario'] == 1 ){
            $ICMS         = 'ICMS Crédito';
            $aliquotaICMS = "(".$mT005['T005_Aliquota_ICMS_Credito']."%) ";
            $valorICMS    = $mT005['T005_Valor_Total_ICMS_Credito'];
            $titleICMS    = "ICMS: &nbsp; {$mT005['T005_Valor_Total_ICMS']}";
        }else{
            $ICMS         = 'ICM';
            $aliquotaICMS = '';
            $valorICMS    =  $valorTotalICMS;
            $titleICMS    = "ICMS: &nbsp; {$mT005['T005_Valor_Total_ICMS']}\nFCP: &nbsp; {$mT005['T005A_Valor_Total_FCP']}";
        }

        $somaTributos = $mT005['T005_Valor_Total_PIS']+$mT005['T005_Valor_Total_COFINS']+$mT005['T005_Valor_Total_IRPJ']+$mT005['T005_Valor_Total_CSLL']+$mT005['T005_Valor_Total_Partilha_ICMS_Destino']+$mT005['T005_Valor_Total_Partilha_ICMS_Origem'];
        $somaTributos = gCorrigeNumero($somaTributos);

        $mT005['T005_Valor_Total_PIS']                   = gCorrigeNumero($mT005['T005_Valor_Total_PIS']);
        $mT005['T005_Valor_Total_COFINS']                = gCorrigeNumero($mT005['T005_Valor_Total_COFINS']);
        $mT005['T005_Valor_Total_IRPJ']                  = gCorrigeNumero($mT005['T005_Valor_Total_IRPJ']);
        $mT005['T005_Valor_Total_CSLL']                  = gCorrigeNumero($mT005['T005_Valor_Total_CSLL']);
        $mT005['T005_Valor_Total_Partilha_ICMS_Destino'] = gCorrigeNumero($mT005['T005_Valor_Total_Partilha_ICMS_Destino']);
        $mT005['T005_Valor_Total_Partilha_ICMS_Origem']  = gCorrigeNumero($mT005['T005_Valor_Total_Partilha_ICMS_Origem']);
        $title = "PIS: &nbsp; {$mT005['T005_Valor_Total_PIS']}\nCOFINS: &nbsp; {$mT005['T005_Valor_Total_COFINS']}\nIRPJ: &nbsp; {$mT005['T005_Valor_Total_IRPJ']}\nCSLL:  &nbsp; {$mT005['T005_Valor_Total_CSLL']} \nDIFAL DEST:  &nbsp; {$mT005['T005_Valor_Total_Partilha_ICMS_Destino']} \nDIFAL ORIG:  &nbsp; {$mT005['T005_Valor_Total_Partilha_ICMS_Origem']}";

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
                .divEspacamento {
                    height:137px;
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
                        Tabela
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal; border-bottom:1px solid #F4F4F4; font-size:11px;' align='right'>
                        {$valorPrecoTabelaPedido}
                    </td>
                </tr>
EOT;
            echo <<<EOT
                <tr>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Desconto
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        <font style='font-size:11px'>($percentualDesconto%)</font> {$mT005['T005_Valor_Desconto']}
                    </td>
                </tr>
                <tr>
                    <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                        Produtos
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:bold;font-size:14px;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mT005['T005_Valor_Total_Produtos']}
                    </td>
                </tr>
                <tr>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        IPI
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mT005['T005_Valor_Total_IPI']}
                    </td>
                </tr>
                <tr title="$titleST">
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        <u>ST+FCP ST</u>
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$valorTotalST}
                    </td>
                </tr>
                <tr>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Desconto
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        <font style='font-size:11px'>($percentualDescontoManual%)</font> {$mT005['T005_Valor_Desconto_Manual']}
                    </td>
                </tr>
                <tr title="Quantidade: &nbsp; {$mT005['quantidadeTotal']}\nCubagem: &nbsp; {$mT005['cubagemTotal']}m³\nPeso liquido: &nbsp; {$mT005['pesoLiquido']} kg\nPeso bruto:  &nbsp; {$mT005['pesoBruto']} kg \nFrete calculado: &nbsp; R$ {$mT005['T005_Valor_Frete_Calculado']}">
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        <u>Frete</u>
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mT005['T005_Valor_Frete']}
                    </td>
                </tr>
                <tr>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Desp.Acessorias
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mT005['T005_Valor_Despesas_Acessorias']}
                    </td>
                </tr>

                <!--<tr>
                    <td>
                        IPV:
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mT005['T005_IPV']}
                    </td>
                </tr>-->
                <tr>
                    <td class='observacaoTotaisTd' style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                        Total
                    </td>
                    <td class='observacaoTotaisTd' style='font-weight:bold;font-size:16px;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mT005['T005_Valor_Total']}
                    </td>
                </tr>
                <tr title="{$titleICMS}">
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        <u>{$ICMS}</u>
                    </td>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$aliquotaICMS}{$valorICMS}
                    </td>
                </tr>
                <tr title="{$title}">
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        <u>Outros tributos</u>
                    </td>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$somaTributos}
                    </td>
                </tr>
                <tr>
                    <td class='observacaoTotaisTd' style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Comissão
                    </td>
                    <td class='observacaoTotaisTd' style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        ({$mT005['T005_Percentual_Comissao']}%) {$mT005['T005_Valor_Total_Comissao']}
                    </td>
                </tr>
            </table>
EOT;

    if((isset($g['c029Ids'][217])) && (isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='border:1px solid silver; float:left;'>
                <div class="divEspacamento"></div>
EOT;
    }
    if(!(isset($g['c029Ids'][217])) || !(isset($g['c029Ids'][218]))){
        echo<<<EOT
            <div class="observacoes observacoesLimites" style='border:1px solid silver; float:left;'>
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
                    height: 25%;
                    padding:1px;
                    border-top:none;
                }
                .divEspacamento {
                    height:120px;
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
            <br><div class='blue' style='font-weight:bold;border-radius:5px;color:white;background:blue; height: 16px; font-size: 13px; margin-top: 11px; text-align: center; padding: 1px;'>{$mT005['Status']}</div>
            </div>
        </div>
EOT;
    break;
    
    case 'ven002DemonstrativoIPV':
        $sqlT005 = mysql_query("SELECT T005_Id,
                                       T005_Valor_Total_Produtos, 
                                       T005_Flag_Lista_Preco, 
                                       T005_Valor_Total_ICMS, 
                                       T005_Valor_Total_IRPJ, 
                                       T005_Valor_Total_CSLL, 
                                       T005_Valor_Total_PIS, 
                                       T005_Valor_Total_COFINS, 
                                       T005_Valor_Total_Produtos 
                                  FROM T005 
                                 WHERE T005_Id = '{$r_acaoId}'");

        $mT005 = mysql_fetch_array($sqlT005);

        $impostos = 0;
        $impostos += ($g['C031']['somarICMSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_ICMS'] : 0;
        $impostos += ($g['C031']['somarPISPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_PIS'] : 0;
        $impostos += ($g['C031']['somarCOFINSPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_COFINS'] : 0;
        $impostos += ($g['C031']['somarIRPJPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_IRPJ'] : 0;
        $impostos += ($g['C031']['somarCSLLPrecoVenda'] == 'S') ? $mT005['T005_Valor_Total_CSLL'] : 0;

        $percentualImpostos        = ($impostos / $mT005['T005_Valor_Total_Produtos']) * 100;
        $T005_Valor_Total_Produtos = $mT005['T005_Valor_Total_Produtos'] - $impostos;

        $sqlT006 = mysql_query("SELECT SUM(T006_Valor_Custo_Unitario*T006_Quantidade) AS custo 
                                  FROM T006 
                             LEFT JOIN D009 on D009_Id=T006_D009_Id 
                                 WHERE T006_T005_Id='{$r_acaoId}'");

        $mT006 = mysql_fetch_array($sqlT006);
        $custo = $mT006['custo'];

        $ipvSemLucroQuery = mysql_query("SELECT SUM((IF(T251_Grupo='IPV',$T005_Valor_Total_Produtos,$custo)*(T251_Aliquota/100))) AS ipvSemLucro,
                                                SUM(T251_Aliquota) AS aliquotasIPVSemLucro
                                           FROM T251 
                                      LEFT JOIN T005 ON T005_Id=T251_T005_Id
                                          WHERE T251_T005_Id = '{$r_acaoId}' 
                                            AND IFNULL(T251_Tipo,0)!='3' 
                                            AND IFNULL(T251_Tipo,0)!='4' 
                                            AND T251_Grupo='IPV'");

        $ipvSemLucro = mysql_fetch_array($ipvSemLucroQuery);
 
        $valorLucro            = $T005_Valor_Total_Produtos - $custo - $ipvSemLucro['ipvSemLucro'];
        $total                 = $T005_Valor_Total_Produtos+$impostos;
        $impostos              = gCorrigeNumero($impostos);
        $percentualImpostos    = gCorrigeNumero($percentualImpostos);
        $valorIpvSemLucro      = gCorrigeNumero($ipvSemLucro['ipvSemLucro']);
        $percentuaIpvSemlLucro = gCorrigeNumero(($ipvSemLucro['ipvSemLucro']/$T005_Valor_Total_Produtos)*100);
        $percentualLucro       = gCorrigeNumero(($valorLucro/$T005_Valor_Total_Produtos)*100);
        $percentualCusto       = gCorrigeNumero(($custo/$T005_Valor_Total_Produtos)*100);
        $custo                 = gCorrigeNumero($custo);
        $valorLucro            = gCorrigeNumero($valorLucro);
        $total                 = gCorrigeNumero($total);

        $T005_Valor_Total_Produtos = gCorrigeNumero($T005_Valor_Total_Produtos);

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
                            <td>Produtos-Tributos</td><td align=right>R$ {$T005_Valor_Total_Produtos}</td>
                        </tr>
                        <tr id='custo'>
                            <td id='custo'>Custo</td><td align=right>R$ {$custo}</td>
                            <td align=right>{$percentualCusto}%</td>
                        </tr>
                        <tr>
                            <td>IPV - Lucro</td><td align=right>R$ {$valorIpvSemLucro}</td>
                            <td align=right>{$percentuaIpvSemlLucro}%</td>
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
}
?>
