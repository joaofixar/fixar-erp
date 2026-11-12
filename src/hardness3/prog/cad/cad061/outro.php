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
	case 'cad061CabecalhoOrcamento':
		// Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            D157
        left join D024 on D157_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D024_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        WHERE
            (D157_Id='$r_acaoId')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (trim($row_)=="") { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        
        $possuiRestricoes = "";
        if($row['D024_Flag_Liberado']=="N" || $row['D024_Flag_Restricao'] == "S"){
            $possuiRestricoes = "<font color='red'><b>RESTRIÇÕES</b></font>";
        }else{
            $possuiRestricoes = "<font color='green'><b>LIBERADO</b></font>";
        }
        if($row['D024_Flag_Contribuinte']=="0"){
            $Contribuinte = "<font color='blue'><b>NÃO CONTRIB.</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="1"){
            $Contribuinte = "<font color='blue'><b>CONSUM.FINAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="2"){
            $Contribuinte = "<font color='blue'><b>REVENDA</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="3"){
            $Contribuinte = "<font color='blue'><b>PROD.RURAL</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="4"){
            $Contribuinte = "<font color='blue'><b>SIMPLES</b></font>";
        } else if($row['D024_Flag_Contribuinte']=="5"){
            $Contribuinte = "<font color='blue'><b>INDUSTRIA</b></font>";
        } else if ($row['D024_Flag_Contribuinte']==""){
            $Contribuinte="<font color='red'>FALTA</font>";            
        }
        
        $dataEmissao = gCorrigeData($row['D157_Data_Emissao']);
        if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 35);
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 35);
        }
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 16);
        $row['D024_Telefone_1'] = str_replace(array(' ','-','.'), '', $row['D024_Telefone_1']);
        $D024_Telefone = gTruncar($row['D024_DDD_Telefone_1'].' '.$row['D024_Telefone_1'], 20);
        
        switch($row['D157_Flag_Lista_Preco']){
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

        switch($row['D157_Flag_Destino_Produto']){
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
                    </tr>
                    <tr class="linhaVal" style="cursor:pointer;" {$urlCadastroCliente}>
                        <td style='padding:1px 5px 4px 2px;' align='center'>{$row['D157_Id']}</td>
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
                    </tr>
                </table>
            </div>
EOT;

	break;
	
	case 'cad061observacoes':
		global $g;
        $D157_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;
        mysql_query("call D157_Gravar_Totalizacao_4($D157_Id);");
        $D157 = "SELECT *, D157_Prazos(D157_Id) as Prazo, D157_Status_Pedido(D157_Flag_Status,1) as Status, SUM(D158_Valor_Lucro) as Lucro,Liberou.C007_Primeiro_Nome AS Liberou,LiberouOrcamento.C007_Primeiro_Nome AS LiberouOrcamento
                      FROM D157
                 LEFT JOIN D024 ON D024_Id = D157_D024_Id
                 LEFT JOIN D158 ON D158_D157_Id = D157_Id
                 LEFT JOIN T003 ON T003_Id = D157_T003_Id
                 LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=D157_Liberou_Alcada_C007_Id
                 LEFT JOIN C007 AS LiberouOrcamento ON LiberouOrcamento.C007_Id=T003_Liberou_Alcada_C007_Id
                     WHERE D157_Id='{$D157_Id}'";
        $D157 = mysql_query($D157);
        $mD157= mysql_fetch_array($D157);

        $obsComercial    = substr($mD157['D024_Observacao_Comercial'], 0, 140);
        $obsFinanceiro   = substr($mD157['D024_Observacao_Financeiro'], 0, 180);
        $valorLimite     = gCorrigeNumero($mD157['D024_Valor_Limite']);
        $lucro = gCorrigeNumero(($mD157['Lucro']/$mD157['D157_Valor_Total_Produtos'])*100);
        $mD157['Lucro'] = gCorrigeNumero($mD157['Lucro']);
        $ipv = gCorrigeNumero($mD157['D157_Valor_Total_Produtos']/$mD157['D157_Valor_Total_Custo'],4);
        $margem = gCorrigeNumero((($mD157['D157_Valor_Total_Produtos']-$mD157['D157_Valor_Total_Custo'])/$mD157['D157_Valor_Total_Produtos'])*100);
        $mD157['D157_Valor_Total_Produtos'] = gCorrigeNumero($mD157['D157_Valor_Total_Produtos']);
        $mD157['D157_Valor_Total_Custo'] = gCorrigeNumero($mD157['D157_Valor_Total_Custo']);
        $mD157['D157_Valor_Total_ICMS'] = gCorrigeNumero($mD157['D157_Valor_Total_ICMS']);
        $mD157['D157_Valor_Total_IPI'] = gCorrigeNumero($mD157['D157_Valor_Total_IPI']);
        $mD157['D157_Valor_ST'] = gCorrigeNumero($mD157['D157_Valor_ST']);
        $mD157['D157_Valor_Total'] = gCorrigeNumero($mD157['D157_Valor_Total']);
        $mD157['D157_IPV'] = gCorrigeNumero($mD157['D157_IPV'],4);
        $mD157['D157_Percentual_Margem'] = gCorrigeNumero($mD157['D157_Percentual_Margem'],2);
        $mD157['D157_Valor_Total_PIS'] = gCorrigeNumero($mD157['D157_Valor_Total_PIS']);
        $mD157['D157_Valor_Total_COFINS'] = gCorrigeNumero($mD157['D157_Valor_Total_COFINS']);
        $mD157['D157_Valor_Total_IRPJ'] = gCorrigeNumero($mD157['D157_Valor_Total_IRPJ']);
        $mD157['D157_Valor_Total_CSLL'] = gCorrigeNumero($mD157['D157_Valor_Total_CSLL']);
        $mD157['D157_Valor_Desconto_Manual'] = gCorrigeNumero($mD157['D157_Valor_Desconto_Manual']*-1);

        $mD157['D157_Percentual_Comissao'] = gCorrigeNumero($mD157['D157_Percentual_Comissao']);
        $mD157['D157_Valor_Total_Comissao'] = gCorrigeNumero($mD157['D157_Valor_Total_Comissao']);

        //Calcula saldo do cliente
        $sqlSaldo = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
                            ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                        FROM T002
                        WHERE T002_D024_Id='{$mD157['D024_Id']}'
                        and T002_Data_Recebimento='0000-00-00' 
                        and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                        ";
        log("SQL Vencido e vencer: ".$sqlSaldo);
        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo= mysql_fetch_array($resultSaldo);

        log("Vencido: ".$linhaSaldo['Vencido']);
        log("Vencer: " .$linhaSaldo['Vencer']);

        
        $sqlPedidos="select ifnull(sum(D157_Valor_Total),0) as Pedidos
                                from D157
                           left join T007 on T007_D157_Id=D157_Id
                               where D157_D024_Id='{$mD157['D024_Id']}'
                                 and D157_Flag_Almoxarifado='S'
                                 and T007_Id is null";
        log("SQL PEdidos: ".$sqlPedidos);
        $retornoPedidos = mysql_query($sqlPedidos);
        $linhaPedidos = mysql_fetch_array($retornoPedidos);
        log("Pedidos: ".$linhaPedidos['Pedidos']);
        $mD157['D024_Valor_Limite'] = (!empty($mD157['D024_Valor_Limite']) ? $mD157['D024_Valor_Limite'] : '0.00');
        log("Limite: ".$mD157['D024_Valor_Limite']);
        $saldoAtual=gCorrigeNumero($mD157['D024_Valor_Limite']-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$retornoPedidos['Pedidos']));
        
        $percentualDesconto = gCorrigeNumero(($mD157['D157_Valor_Desconto']/$mD157['D157_Valor_Total_Produtos_Sem_Desconto'])*100,2);

        //$valorOrcamento = $VEN001->calculaTotalGeral($D157_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        //$mD157['D157_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mD157['D157_Valor_Total_Produtos_Sem_Desconto']);
        $mD157['D157_Valor_Desconto'] = gCorrigeNumero($mD157['D157_Valor_Desconto']);
        $mD157['D157_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mD157['D157_Valor_ICMS_Substituicao_Retencao']);
        $valorCreditoAntecipado = gCorrigeNumero($mD157['D024_Valor_Credito_Antecipado']);
        $valorCreditoTroca = gCorrigeNumero($mD157['D024_Valor_Credito_Troca']);
        
        require_once('bibliotecas/funcoesCallback/campoPrecoTabelaGridPedido.php');
        $array['todosCampos']['D157_Id'] = $D157_Id;
        $array['todosCampos']['D157_Flag_Status'] = $mD157['D157_Flag_Status'];
        $array['todosCampos']['D157_Flag_Status_Cotacao'] = $mD157['D157_Flag_Status_Cotacao'];
        $array['campoAtual'] = $mD157['D157_Valor_Total_Produtos_Sem_Desconto'];
        $valorPrecoTabelaPedido = str_replace('R$ ','',campoPrecoTabelaGridPedido($array, true));
        
        $onclick = ($g['nivelAlcada'] == 4) ? "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Montagem IPV', '/ven/ven002/content/contentMontagemIPV/', '&acaoId={$D157_Id}', [370,580]);" : "";
        echo <<<EOT
            <style type="text/css">
                .observacoesLimites{
                    width: 285px;
                    height: 186px;
                    float: left;
                    margin: 7px 0px 10px 8px;
                    border-radius:5px;
                    border-color:silver;
                }
                .observacoesLimites .comercial{
                    height: 52px;
                    padding:1px;
                    border-bottom:none;

                }
                .observacoesLimites .financeiro{
                    height: 68px;
                    padding:1px;

                }
                .observacoesLimites .limite{
                    height: 15%;
                    padding:1px;
                    border-top:none;
                }
                .observacoesLimites .liberado{
                    height: 13%;
                    padding:0px;
                    border-bottom:1px solid silver;
                }
                .observacoesLimites .saldo{
                    height: 9%;
                    padding:1px;
                    border-top:none;
                }
            </style>
            <table style='font-size:13px; border-left:5px;width: 195px;float:left; height: 210px; margin: 7px 5px 0 5px; cursor: pointer;' cellspacing="0" cellpadding="0" onclick="{$onclick}">
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4'>
                        Tabela
                    </td>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4' align='right'>
                        {$valorPrecoTabelaPedido}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Desconto
                    </td>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        <font style='font-size:11px'>($percentualDesconto%)</font> {$mD157['D157_Valor_Desconto']}
                    </td>
                </tr>
                <tr>
                    <td style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                        Produtos
                    </td>
                    <td style='font-weight:bold;font-size:14px;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mD157['D157_Valor_Total_Produtos']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        IPI
                    </td>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_IPI']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        ST
                    </td>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_ICMS_Substituicao_Retencao']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Desconto
                    </td>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Desconto_Manual']}
                    </td>
                </tr>
                <!--<tr>
                    <td>
                        IPV:
                    </td>
                    <td style='font-weight:bold;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mD157['D157_IPV']}
                    </td>
                </tr>-->
                <tr>
                    <td style='font-weight:bold;border-bottom:1px solid #F4F4F4'>
                        Total
                    </td>
                    <td style='font-weight:bold;font-size:18px;border-bottom:1px solid #F4F4F4' align='right'>
                        {$mD157['D157_Valor_Total']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        ICM
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_ICMS']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        PIS
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_PIS']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        COFINS
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_COFINS']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        IRPJ
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_IRPJ']}
                    </td>
                </tr>
                <tr>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px'>
                        CSLL
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        {$mD157['D157_Valor_Total_CSLL']}
                    </td>
                </tr>
                <tr>
                    <td style='font-weight:normal;border-bottom:1px solid #F4F4F4;font-size:11px'>
                        Comissão
                    </td>
                    <td style='border-bottom:1px solid #F4F4F4;font-size:11px' align='right'>
                        ({$mD157['D157_Percentual_Comissao']}%) {$mD157['D157_Valor_Total_Comissao']}
                    </td>
                </tr>
            </table>
            <div class="observacoes observacoesLimites" style='border:1px solid silver; '>
                <div class="comercial" title='{$mD157['D024_Observacao_Comercial']}'>
                    <b>Observação comercial:</b><br />
                    <span style="color: var(--BGVermelho);">{$obsComercial}</span>
                </div>                
                <div class="liberado">
                    <span>Orçamento liberado por: <span style="color: var(--BGVermelho);">{$mD157['LiberouOrcamento']}</span></span><br />
                    <span>Pedido liberado por: <span style="color: var(--BGVermelho);">{$mD157['Liberou']}</span></span>  
                </div>
                <div class="financeiro" title='{$mD157['D024_Observacao_Financeiro']}'>
                    <b>Observação financeiro:</b><br />
                    <span style="color: var(--BGVermelho);">{$obsFinanceiro}</span>
                </div>
                <div class="limite">
                    Prazo Pagamento: <span style="color: var(--BGVermelho);">{$mD157['Prazo']}</span><br>
                    Limite: <span style="color: var(--BGVermelho);">{$valorLimite}</span>
                    Saldo disponível: <span style="color: var(--BGVermelho);">{$saldoAtual}</span><br />
                    Crédito Antecipado: <span style="color: var(--BGVermelho);">{$valorCreditoAntecipado}</span> Crédito Troca: <span style="color: var(--BGVermelho);">{$valorCreditoTroca}</span>
                </div>
                <div style='font-weight:bold;border-radius:5px;border-bottom:1px solid #F4F4F4;color:white;background:blue; height: 16px; font-size: 13px; margin-top: 11px; text-align: center; padding: 1px;'>{$mD157['Status']}</div>
            </div>
EOT;
	break;
}
?>
