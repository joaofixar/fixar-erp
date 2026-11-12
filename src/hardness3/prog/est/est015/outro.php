<?php
namespace hardness;
require_once("bibliotecas/classes/EST015.php");
$EST015 = new EST015();
/**
 * Ações
 */
switch ($g['r'][3]) {
    /**
     * Cabeçalho Orçamento
     * Chamado em: est015TabEditarOrcamento
     */
    case 'est015CabecalhoOrcamento':
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T218
        left join D024 on T218_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D024_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        WHERE
            (T218_Id='$r_acaoId')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        
        $possuiRestricoes = "";
        if($row['D024_Flag_Liberado']=="N" || $row['D024_Flag_Restricao'] == "S"){
            $possuiRestricoes = "<font color='red'><b>C/RESTRIÇÕES</b></font>";
        }else{
            $possuiRestricoes = "<font style='color: var(--BGVerde);'><b>LIBERADO</b></font>";
        }
        $Contribuinte="<font class='BGVermelho'><b>&nbsp;INFORMAR!&nbsp;</b></font>";
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
        }   
        
        $dataEmissao = gCorrigeData($row['T218_Data_Emissao']);
        if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 35);
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 35);
        }
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 15);
        
        switch($row['T218_Flag_Lista_Preco']){
            case '1':   
                $listaPreco="Preço 1";
                break;
            case '2':
                $listaPreco="Preço 2";
                break;
            case '3':
                $listaPreco = "Preço 3";
                break;
            default:
                $listaPreco="<font style='color: var(--BGAzul);'><b>FALTA</b></font>";
        }
        if($row['D024_Id'] == '<span style="color: var(--BGAzul);">FALTA</span>'){
            $abrirJanela = "";
        }else{
            $abrirJanela = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');";
        }

        $Crt="<font class='BGVermelho'><b>&nbsp;</b></font>";
        if($row['D024_Codigo_Regime_Tributario']=="1"){
            $Crt = "<font style='color: var(--BGAzul);'><b>1-SIMPLES</b></font>";
        } else if($row['D024_Codigo_Regime_Tributario']=="2"){
            $Crt = "<font style='color: var(--BGAzul);font-size:9px'><b>2-SIMPLES(SUB)</b></font>";
        } else if($row['D024_Codigo_Regime_Tributario']=="3"){
            $Crt = "<font style='color: var(--BGAzul);'><b>3-NORMAL</b></font>";
        }
      
        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc" style="cursor:pointer;" onclick="{$abrirJanela}">
                        <td width='50px'>Nº</td>
                        <td width='50px'>Código</td>
                        <td width='400px'>Cliente</td>
                        <td width='150px'>Cidade</td>
                        <td width='30px'>UF</td>
                        <td width='70px'>Emissão</td>
                        <td width='50px'>Tipo</td>
                        <td width='50px'>Financeiro</td>
                        <td width='50px'>CRT</td>
                    </tr>
                    <tr class="linhaVal" style="cursor:pointer;" onclick="{$abrirJanela}">
                        <td>{$row['T218_Id']}</td>
                        <td>{$row['D024_Id']}</td>
                        <td>{$D024_Nome_Empresa}</td>
                        <td>{$D020_Nome_Cidade}</td>
                        <td>{$row['D018_UF']}</td>
                        <td>{$dataEmissao}</td>
                        <td>{$Contribuinte}</td>
                        <td>{$possuiRestricoes}</td>
                        <td>{$Crt}</td>
                    </tr>
                </table>
            </div>
EOT;

    break;
    
    case 'est015observacoes':
        global $g;
        session_write_close();
        
        $T218_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;
        mysql_query("call T218_Gravar_Totalizacao_3($T218_Id);");
        $sqlD024 = "SELECT * FROM T218
                        LEFT JOIN D024 ON D024_Id = T218_D024_Id
                        LEFT JOIN T218A ON T218_Id = T218A_T218_Id
                        WHERE T218_Id='{$T218_Id}'";
        $resultD024 = mysql_query($sqlD024);
        $linhaD024= mysql_fetch_array($resultD024);

        $obsComercial = substr($linhaD024['D024_Observacao_Comercial'], 0, 500);
        $obsFinanceiro = substr($linhaD024['D024_Observacao_Financeiro'], 0, 500);

        $valorLimite = gCorrigeNumero($linhaD024['D024_Valor_Limite']);
        $linhaD024['T218_Valor_Total']          = gCorrigeNumero($linhaD024['T218_Valor_Total']);
        $totalICMS                              = gCorrigeNumero($linhaD024['T218_Valor_Total_ICMS'] + $linhaD024['T218A_Valor_FCP']);
        $linhaD024['T218A_Valor_FCP']           = gCorrigeNumero($linhaD024['T218A_Valor_FCP']);
        $linhaD024['T218_Valor_Total_ICMS']     = gCorrigeNumero($linhaD024['T218_Valor_Total_ICMS']);
        $linhaD024['T218_Valor_Total_IPI']      = gCorrigeNumero($linhaD024['T218_Valor_Total_IPI']);
        $totalST                                = gCorrigeNumero($linhaD024['T218_Valor_ST'] + $linhaD024['T218A_Valor_FCP_ST']);
        $linhaD024['T218_Valor_ST']             = gCorrigeNumero($linhaD024['T218_Valor_ST']);
        $linhaD024['T218A_Valor_FCP_ST']        = gCorrigeNumero($linhaD024['T218A_Valor_FCP_ST']);
        $linhaD024['T218_Valor_Total_Produtos'] = gCorrigeNumero($linhaD024['T218_Valor_Total_Produtos']);
        $linhaD024['T218_Valor_Frete']          = gCorrigeNumero($linhaD024['T218_Valor_Frete']);
        
        //Calcula saldo do cliente
        $sqlSaldo = "SELECT ifnull(sum(if(T002_Data_VENCIMENTO<curdate(),T002_Valor_Saldo,0)),0) as vencer, 
                            ifnull(sum(if(T002_Data_VENCIMENTO>=curdate(),T002_Valor_Saldo,0)),0) as vencer
                        FROM T002
                        WHERE T002_D024_Id='{$linhaD024['D024_Id']}'
                        and T002_Data_Recebimento='0000-00-00' 
                        and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                        ";
        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo= mysql_fetch_array($resultSaldo);
        
        $sqlPedidos="select ifnull(sum(T224_Valor_Total),0) as Pedidos
                                from T224
                           left join T007 on T007_T224_Id=T224_Id
                               where T224_D024_Id='{$linhaD024['D024_Id']}'
                                 and T224_Flag_Almoxarifado='S'
                                 and T007_Id is null";
        $retornoPedidos = mysql_query($sqlPedidos);
        $linhaPedidos = mysql_fetch_array($retornoPedidos);
        $linhaD024['D024_Valor_Limite'] = (!empty($linhaD024['D024_Valor_Limite']) ? $linhaD024['D024_Valor_Limite'] : '0.00');
        $saldoAtual=gCorrigeNumero($linhaD024['D024_Valor_Limite']-($linhaSaldo['vencer']+$linhaSaldo['vencer']+$linhaPedidos['Pedidos']));
        
        $T219 = "SELECT sum(T219_Valor_Preco_Venda*T219_Quantidade) as totalVenda,
                        sum(T219_Valor_Preco_Venda_Negociado*T219_Quantidade) as totalVendaNegociado 
                        FROM T219
                        LEFT JOIN T004 ON T004_T219_Id=T219_Id
                        LEFT JOIN T003 ON T003_Id=T004_T003_Id
                        WHERE T219_T218_Id='{$T218_Id}'
                          AND T219_Valor_Preco_Venda_Negociado>0
                          AND IFNULL(T003_Flag_Perdido,'N')!='S'";
        $T219 = mysql_query($T219);
        $mT219 = mysql_fetch_array($T219);

        $mT219['totalVenda']=($mT219['totalVenda']*0.96); // Aplicando 4% de desconto

        $totalSobra      = $mT219['totalVendaNegociado']- $mT219['totalVenda'];
        $percentualSobra = ($totalSobra > 0) ? gCorrigeNumero(($totalSobra/$mT219['totalVenda'])*100) : '0,00';
        $totalSobra      = gCorrigeNumero($totalSobra);

        //$valorOrcamento = $EST015->calculaTotalGeral($T218_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        if($exibirLimites == 'S'){
            $observacoesLimites = <<<EOT
                <div class="limite">
                    <b>Faturamento mínimo:</b>
                    <span style='color: var(--BGVermelho);'>{$valorLimite}</span>
                </div>
                <div class="saldo">
                    <!--<b>Saldo disponível:</b>
                    <span style='color: var(--BGVermelho);'>{$saldoAtual}</span><br />-->
                </div>
EOT;
        }
            echo <<<EOT
                <style type="text/css">
                .observacoesLimites{
                    width: 330px;
                    height: 205px;
                    float:left;
                    margin: 10px 0px 10px 8px;
                    border-radius:5px;
                    border-color:silver;
                    float: left;
                }
                .observacoesLimites .comercial{
                    height: 90px;
                    padding:1px;
                    word-wrap: break-word;
                    overflow: auto;
                }
                .observacoesLimites .financeiro{
                    height: 85px;
                    padding:1px;
                    border-top:none;
                    word-wrap: break-word;
                    overflow: auto;
                }
                .observacoesLimites .limite{
                    height: 9%;
                    padding:1px;
                    border-top:none;
                }
                .observacoesLimites .saldo{
                    height: 9%;
                    padding:1px;
                    border-top:none;
                }
                </style>
                <div style="float: left; height: 210px; width: 230px; margin: 5px 25px 0 25px; font-size: 13px;">   
                    <table style="width: 240px; background: none">
                        <tr>
                            <td class='observacaoTotaisTd'>
                                <b>Produtos</b>
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 15px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                <b> R$ {$linhaD024['T218_Valor_Total_Produtos']}</b>
                            </td>
                        </tr>

                        <tr>
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                IPI
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                R$ {$linhaD024['T218_Valor_Total_IPI']}
                            </td>
                        </tr>
                        <tr title="ST: &nbsp; R$ {$linhaD024['T218_Valor_ST']}\nFCP ST: &nbsp; R$ {$linhaD024['T218A_Valor_FCP_ST']}">
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                <u> ST+FCP ST</u>
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                R$ {$totalST}
                            </td>
                        </tr>
                        <tr>
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                Frete
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                R$ {$linhaD024['T218_Valor_Frete']}
                            </td>
                        </tr>
                        <tr>
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                <b>Total</b>
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 15px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                <b> R$ {$linhaD024['T218_Valor_Total']}</b>
                            </td>
                        </tr>
                        <tr title="ICMS: &nbsp; R$ {$linhaD024['T218_Valor_Total_ICMS']}\nFCP: &nbsp; R$ {$linhaD024['T218A_Valor_FCP']}">
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                <u>ICMS+FCP</u>
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                R$ {$totalICMS}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                Sobra 
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                R$ {$totalSobra}  (-4%)
                            </td>
                        </tr>
                        <tr>
                            <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                                Perc. sobra 
                            </td>
                            <td class='observacaoTotaisTd' style="font-size: 13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4">
                                {$percentualSobra}%
                            </td>
                        </tr>
                    </table>
                </div>
                 <div class="observacoes observacoesLimites">
                    <div class="comercial">
                        <b>Comercial:</b><br />
                        <span style="color: var(--BGVermelho);">$obsComercial</span>
                    </div>
                    <div class="financeiro">
                        <b>Financeiro:</b><br />
                        <span style="color: var(--BGVermelho);">{$obsFinanceiro}</span>
                    </div>
                    {$observacoesLimites}
                </div>
EOT;
    break;

    case 'est015Totalizadores':
        
        // Select
        $sql = <<<EOT
        SELECT 
            *,
            if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
        FROM 
            T218
        left join D024 on T218_D024_Id=D024_Id
        left join D020 on D024_D020_Id=D020_Id
        left join D018 on D020_D018_Id=D018_Id
        left join C007 on C007_Id=D024_C007_Vendedor_Interno
        left join D030 on D024_D030_Id=D030_Id
        left join C004 on C004_Id=T218_C004_Id
        WHERE
            (T218_Id='$r_T218_Id')
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) { 
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }
        
        $dataEmissao = gCorrigeData($row['T218_Data_Emissao']);
        $valor = gCorrigeNumero($row['T218_Valor_Total']);
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
                        <td>{$row['T218_Id']}</td>
                        <td><a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent({$row['D024_Id']}) + '&tabela=D024', [960,583]);" style="padding: 5px 50px 5px 5px;">{$row['D024_Nome_Empresa']}</a></td>
                        <td>{$valor}</td>
                        <td>{$row['C004_Nome_Empresa']}</td>
                    </tr>
                </table>
            </div>
EOT;
        
        $T218_Id = $r_T218_Id;
        $sql="SELECT T218_Valor_Total_Produtos,T218_Valor_Total_Servicos,T218_Valor_Troca,T218_Valor_Desconto_Manual,T218_Valor_Total FROM T218 WHERE T218_Id={$T218_Id}";
        $sqlResult = mysql_query($sql);
        $row = mysql_fetch_array($sqlResult);
        
        $totalProdutos = gCorrigeNumero($row['T218_Valor_Total_Produtos']);
        $totalServicos = gCorrigeNumero($row['T218_Valor_Total_Servicos']);
        $totalTroca = gCorrigeNumero($row['T218_Valor_Troca']);
        $totalDesconto = gCorrigeNumero($row['T218_Valor_Desconto_Manual']);
        $totalGeral = gCorrigeNumero($row['T218_Valor_Total']);
        echo <<<EOT
        <div class="buttonsBottomTop">
            <button type="button" id="est015DividirPedidoBtn" style="margin:8px;">Dividir Orçamento</button>
            <button type="button" id="est015MudarFilial" style="margin:8px;">Trocar empresa</button>
            <!-- <button type="button" id="est015GerarNFMarcados" style="margin:8px;">Gerar NF</button> -->
            <!-- <button type="button" id="est015ConcluirNF" style="margin:8px;">Concluir</button> -->
            <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$totalProdutos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Serviço<br><input type="text" value="{$totalServicos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Trocas<br><input type="text" value="{$totalTroca}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Desconto<br><input type="text" value="{$totalDesconto}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Total<br><input type="text" value="{$totalGeral}" class="whiteBg" id="" size="11" readonly></span>
            <div style="clear:both;"></div>
        </div>
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
            $('#est015DividirPedidoBtn').unbind('click').bind('click', function(data) {
                $.getJSON('/est/est015/grid_func-ajax/est015VerificaItensDividir/?T218_Id=' + encodeURIComponent('{$T218_Id}') + '&ajax=true&callback=?', function(request) {
                    if(request.code){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/est/est015/content/est015ContentSelecionarEmpresa/', '&T218_Id=' + encodeURIComponent('{$T218_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}'), [400,200]);
                    }else{
                        var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                        dialogConfirm('Erro', 'Não há itens selecionadas para dividir o pedido', \$buttons);
                    }
                });
            });
        
        $('#est015MudarFilial').unbind('click').bind('click', function(data) {
            $.getJSON('/est/est015/grid_func-ajax/est015VerificaItensMudarFIlial/?T218_Id=' + encodeURIComponent('{$T218_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/est/est015/content/est015ContentSelecionarEmpresa/', '&T218_Id=' + encodeURIComponent('{$T218_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&mudarFilial=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste orçamento', \$buttons);
                }
            });
        });
            
        $('#est015GerarNFMarcados').unbind('click').bind('click', function(data) {
            $.getJSON('/est/est015/grid_func-ajax/est015VerificaItensGerarNF/?T218_Id=' + encodeURIComponent('{$T218_Id}') + '&ajax=true&callback=?', function(request) {
                if(request.code){
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/est/est015/content/est015ContentSelecionarEmpresa/', '&T218_Id=' + encodeURIComponent('{$T218_Id}') + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&gerarNF=true', [400,200]);
                }else{
                    var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                    dialogConfirm('Erro', 'Não há itens neste pedido', \$buttons);
                }
            });
        });
        
        $('#est015ConcluirNF').unbind('click').bind('click', function(data) {
            var \$buttons = {
                "Sim": function() {
                    $('#' + '{$g['divId']}').showLoading();
                    $.getJSON('/est/est015/grid_func-ajax/est015ConcluirPedido/?T218_Id=' + encodeURIComponent('{$T218_Id}') + '&ajax=true&callback=?', function(request) {
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

    case 'est015OutrosCotacaoCompraOnline':
        $p = explode('|', base64_decode($r_p));
        //-- Fun��es Gen�ricas em PHP
    //require "../hardness/func/funcoes_genericas.inc";

    global $g;
    $Finalizar = $r_Finalizar;

    //require_once("old/lib/funcoes_genericas.inc");
    //die("djbgkdjsb");

    $Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens";
    $cArquivo_Cabecalho_Relatorio = "hardness/cabecalho_impressoes.php";

    //echo $cArquivo_Cabecalho_Relatorio;

    if ($p==false)
    {
        echo "<p style='font-size:16px;color: var(--BGVermelho);font-family:arial'>O link está incorreto, favor pedir suporte tecnico. (1)</p>";
        exit;
    }
    $xp=$p;
    $Empresa_Atual=$xp[0];
    $T218_Id=$xp[1];
    $D024_Id=$xp[2];
    $Tipo_Opcoes='ComValor';

    if ($Finalizar=='S') {
        mysql_query("update T218 set T218_Data_Finalizado_Fornecedor=now(), T218_Flag_Notificacao = 1 where T218_Id='$T218_Id'");
        echo "<p align=center style='font-weight:bold;color: var(--BGVermelho);font-size:18px'>Cotação finalizada pelo fornecedor!</font>";
        exit;
    }

    // Busca os dados da empresa usu�ria
    $cSql=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
    $mC004=mysql_fetch_array($cSql);

    $cSql_T218 = mysql_query("select  T218_Data_Finalizado,
                                    T218_Data_Finalizado_Fornecedor,
                                    T218_Id,
                                    T218_Prazo_Entrega,
                                    T218_Frete,
                                    T218_Forma_Pagamento,
                                    T218_Valor_Total_Produtos,
                                    T218_Valor_Total_ICMS,
                                    T218_Valor_Total_IPI,
                                    T218_Valor_ST,
                                    T218_Valor_Total,
                                    C007_Email_Assinatura
                                            from T218
                            left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno
                                            where T218_Id='{$T218_Id}'");
    $mT218=mysql_fetch_array($cSql_T218);

    echo mysql_error();

    if ($mT218['T218_Data_Finalizado'] !='0000-00-00 00:00:00')
    {
        echo "<p style='font-size:18px;color:blue;font-family:arial' align=center>Esta cotação já foi finalizada pelo cliente em {$mT218['T218_Data_Finalizado']}.</p>";
        exit;
    } else if ($mT218['T218_Data_Finalizado_Fornecedor'] !='0000-00-00 00:00:00')
    {
        echo "<p style='font-size:18px;color:blue;font-family:arial' align=center>Esta cotação já foi finalizada pelo fornecedor em {$mT218['T218_Data_Finalizado_Fornecedor']}.</p>";
        exit;
    }

    if (($mT218['T218_Id']<=0))
    {
        echo "<p style='font-size:16px;color: var(--BGVermelho);font-family:arial'>O link está incorreto, favor pedir suporte técnico. (3 $p $D024_Id)</p>";
        exit;
    }

    echo "
    <!DOCTYPE html>
    <html id='{$g['divId']}'>
    <head>
    <title>Cotação de Compra N.$T218_Id</title>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  
    <meta http-equiv='Pragma' content='no-cache'>
    <link rel=stylesheet type='text/css'>
    <!-- Funções Gen´wricas em Javascript -->
    <script src='/hardness3/static/js/jquery-1.6.2.min.js'></script>
    <script src='/hardness3/static/js/jquery-ui-1.8.16.custom.min.js'></script>
    <script src='/hardness3/static/js/jquery.showLoading.js'></script>
    <script src='https://kit.fontawesome.com/67c8471bba.js'></script>
    <link rel='stylesheet' href='/hardness3/static/css/temas/padrao/jqueryui.css' type='text/css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' type='text/css'>
    <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/css/temas/tema_{$g['c004']['C004_Banco_Dados']}_cache.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/css/fileuploader.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/bibliotecas/codemirror-3.20/lib/codemirror.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/bibliotecas/codemirror-3.20/theme/monokai.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/bootstrapErp/css/bootstrap.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/css/tema-claro.css?load=13929227' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/js/monaco-editor/0.25.2/min/vs/editor/editor.main.css' type='text/css'>
    <link rel='stylesheet' href='/hardness3/static/css/iso_bootstrap4.0.0min.css' type='text/css'>
    <STYLE TYPE='text/css'><!--
    A:link {text-decoration:none; color:000000}
    A:visited {text-decoration:none; color:000000}
    A:hover {text-decoration:underline; color:000000; cursor:hand}
    A:active {text-decoration:underline; color:000000; cursor:hand}

    .fontemed
    {ar
    font-size:12px;
    font-family:arial,verdana;
    }
    #divLoadingCenter {
        width:150px;
        height:25px;
        position: absolute;
        top:50%;
        left:48%;
        margin-top:-50px;
        margin-left:-50px;
        z-index:auto;
        background-color: yellow;
        border: 1px solid #BDCBD8;
        clear:both;
        vertical-align:middle;
        color: red;
        padding:11px;
            font-family:arial, tahoma, verdana, helvetica, sans-serif;
        font-size:14px;
        text-align:center;
    }
    .titulo_relatorio2{
        font-size:20px;
        font-weight:bold;
    }
    --></style>
    <!-- //$('#{$g['divId']}').showLoading(); //$('#{$g['divId']}').hideLoading(); -->
    <script>
    var zCount=0;
    function GravarEdicaoCampo(Objeto,Id) {
    if (Objeto.type=='checkbox') {
        var Conteudo_Campo_Editado=Objeto.checked;
    } else {
        var Conteudo_Campo_Editado=Objeto.value;
    }
    var Campo_Editado=Objeto.name;
    //MostrarMensagem('inline');
    document.getElementById('fme_gravar').src='/est/est015/gravar_campo_editado_online/?T218_Id='+Id+'&T219_Id='+Id+'&Campo_Editado='+Campo_Editado+'&Conteudo_Campo_Editado='+Conteudo_Campo_Editado+ '&D049_Id=' + Id + '&ajax=true&p={$g['empresaAtual']}&noEmp=true';
    setTimeout (function(){
            $('#{$g['divId']}').showLoading();
    },100);
    setTimeout (function(){
            $('#{$g['divId']}').hideLoading();
    },400);

    }
    function MostrarMensagem(Visivel,Forcar) {
        document.getElementById('divLoadingCenter').style.display=Visivel;
        document.getElementById('divLoadingCenter').style.zIndex = zCount++;
        if (Forcar=='S') {
            document.getElementById('fme_mensagem').src='mensagem_carregando.php3?Visivel='+Visivel;
        }
    }
    setTimeout (function(){
        $('#T219_Valor_Preco_Unitario').focus();
    },100);

    </script>
    </head>
    <body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>
    ";

    //--  MENSAGEM "CARREGANDO..."
    echo "<div id='divLoadingCenter' style='display:none;'>
            <img src='../hardness/imagens/gif/loading.gif' alt='' /><br />
            &nbsp;Aguarde...
        </div>";

    $T219 = mysql_query("select T219_Id,T219_T218_Id from T219 where T219_T218_Id='$T218_Id'");
    while ($mT219 = mysql_fetch_array($T219)){
        mysql_query("call T219_Gravar_Totalizacao('{$mT219['T219_Id']}')");
    }
    mysql_query("call T218_Gravar_Totalizacao_3('{$mT219['T219_Id']}')");


    $cClausula  = " select ";
    $cClausula .= " *, ";
    $cClausula .= " T219_Id, ";
    $cClausula .= " D049_Id, ";
    $cClausula .= " D037_Unidade, ";
    $cClausula .= " T218_D024_Id, ";
    $cClausula .= " T219_Valor_Preco_Unitario, ";
    $cClausula .= " T219_Aliquota_IPI, ";
    $cClausula .= " T219_Quantidade, ";
    $cClausula .= " T219_D009_Id, ";
    $cClausula .= " D001_Codigo_Produto, ";
    $cClausula .= " D001_Descricao_Produto, ";
    $cClausula .= " T219_Descricao_Produto, ";
    $cClausula .= " D009_Quantidade_Estoque, ";
    $cClausula .= " T218_Data_Emissao, ";
    $cClausula .= " if(ifnull(T219_Quantidade>0,0),T219_Quantidade,T219_Quantidade) T219_Quantidade, ";
    $cClausula .= " if(ifnull(T219_D009_Id,0)>0,T219_D009_Id,T219_D009_Id) T219_D009_Id, ";
    $cClausula .= " T218_Id, ";
    $cClausula .= " D024_Nome_Empresa, ";
    $cClausula .= " C007_Primeiro_Nome, ";
    $cClausula .= " C007_Email_Assinatura, ";
    $cClausula .= " D049_Codigo_Produto_Fornecedor, ";
    $cClausula .= " D049_Modelo, ";
    $cClausula .= " D001_Id, ";
    $cClausula .= " D082_Marca";
    $cClausula .= " from T219 ";
    $cClausula .= " left join T218 on T218_Id=T219_T218_Id ";
    $cClausula .= " left join D024 on D024_Id='{$D024_Id}' ";
    //$cClausula .= " left join T012 on T012_T219_Id=T219_Id ";
    $cClausula .= " left join C007 on C007_Id=T218_C007_Id_Vendedor_Interno ";
    $cClausula .= " left join D009 on D009_Id=if(ifnull(T219_D009_Id,0)>0,T219_D009_Id,T219_D009_Id) ";
    $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
    $cClausula .= " left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$D024_Id}'";
    $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
    $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
    $cClausula .= " left join D082 on D082_Id=D049_D082_Id ";
    $cClausula .= " left join C008 on C008_Id=D001_C008_Id ";
    $cClausula .= " where T219_T218_Id='{$T218_Id}' group by T219_Id";
    /**
    *Variavel n�o declarada. 
    if ($Ocultar_Comprados=='true')
    {
        $cClausula .= " and (select T012_Id from T012 where T012_T219_Id=T219_Id limit 1) is null ";
    }
    */
    $cClausula .= " order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))";

    $T219 = mysql_query($cClausula);
    echo mysql_error();

        //
        // CABE�ALHO
        //

    echo "<table align=center border='0' valign=top cellpadding=0 cellspacing=5 bgcolor=white width='900' >";
    echo "<tr>";
    echo "<td valign=top height='1%'>";
        echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=2 bgcolor=white width='900' >";
        echo "<tr>";
        echo "<td valign=top>";

        echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
            echo "<tr>";
            echo "<td bgcolor=white width=80%><br />";

                    //-- $cArquivo_Cabecalho_Relatorio � definido no arquivo include/pasta_usuario.inc
                    include $cArquivo_Cabecalho_Relatorio;

            echo "</td>";
            echo "</tr>";
            echo "</table>";
            echo "</td>";
            echo "<td bgcolor=white align=center width=20% rowspan=2>";
            echo "<font class='titulo_relatorio2'>COTAÇÃO DE COMPRA <br> No.$T218_Id</font>";
            echo "</font><br /><br />";
            echo "</td>";
        echo "</tr>";
        echo "</table>";

        echo "</td>";
    echo "</tr>";

    echo "<tr>";

        echo "<td>";

                    $cClausula  = " select ";
                    $cClausula .= " D024_Id, ";
                    $cClausula .= " D024_Nome_Empresa, ";
                    $cClausula .= " D024_Nome_Fantasia, ";
                    $cClausula .= " D024_Endereco, ";
                    $cClausula .= " D024_Numero, ";
                    $cClausula .= " D020_Nome_Cidade, ";
                    $cClausula .= " D018_UF, ";
                    $cClausula .= " D024_Bairro, ";
                    $cClausula .= " D024_Cep, ";
                    $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
                    $cClausula .= " if(D024_Cnpj='',D024_Cpf,D024_Cnpj) D024_Cnpj, ";
                    $cClausula .= " D024_Flag_Revenda_Consumidor, ";
                    $cClausula .= " D024_Flag_Tipo_Empresa, ";
                    $cClausula .= " D024_Nome_Contato, ";
                    $cClausula .= " D024_Observacoes, ";
                    $cClausula .= " D024_Inscricao_Estadual, ";
                    $cClausula .= " D024_Praca_Pagamento,  ";
                    $cClausula .= " D024_Aviso, ";
                    $cClausula .= " D024_Flag_Classe, ";
                    $cClausula .= " D024_Flag_Liberado, ";
                    $cClausula .= " D030_Nome_Pais, ";
                    $cClausula .= " D024_Observacao_Compra,  ";
                    $cClausula .= " D024_Data_Irregular, ";
                    $cClausula .= " D024_DDD_Telefone_1,  ";
                    $cClausula .= " D024_Telefone_1,  ";
                    $cClausula .= " D024_Telefone_2,  ";
                    $cClausula .= " D024_Fax, ";
                    $cClausula .= " D024_Dias_Carencia_Pagamento, ";
                    $cClausula .= " D024_Dia_Semana_Pagamento, ";
                    $cClausula .= " D024_Dia_Mes_Pagamento, ";
                    $cClausula .= " D024_Observacoes, ";
                    $cClausula .= " T218_Observacao, ";
    //                $cClausula .= " T218_Data_Entrega, ";
    //                $cClausula .= " T218_Forma_Pagamento, ";
                    $cClausula .= " C007_Primeiro_Nome ";
                    $cClausula .= " from T218 ";
                    $cClausula .= " left join D024 on D024_Id='$D024_Id' ";
                    $cClausula .= " left join D020 on D024_D020_Id=D020_Id ";
                    $cClausula .= " left join D018 on D020_D018_Id=D018_Id ";
                    $cClausula .= " left join D030 on D024_D030_Id=D030_Id ";
                    $cClausula .= " left join C007 on C007_Id=D024_C007_Vendedor_Interno ";
                    $cClausula .= " where T218_Id='$T218_Id'";
                    $cSql_D024 = mysql_query($cClausula);
                    
                    echo mysql_error();

                    $mD024=mysql_fetch_array($cSql_D024);

                    echo "<br /><table border=0 align=center cellspacing=0 bgcolor='white' cellpadding=2 width=900px style='border:1px solid #09387E'>";
                    echo "<tr>";
                        echo "<td colspan=10 bgcolor='#09387E' style='color:white;font-size:14px;font-weight:bold;padding:1px'>";
                                echo "FORNECEDOR";
                        echo "</td>";
                    echo "</tr>";

                        echo "<tr>";
                        echo "<td valign=top height=100>";
                                echo "<form name='formCotacao' />";
                                echo "<table border=0 cellspacing=0 width=100% cellpadding=1 bgcolor=#09387E>";

                                    echo "<tr>";
                                        echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>Razão Social<br><b>{$mD024['D024_Nome_Empresa']}</b></font>";
                                        echo "</td>";
                                        echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>CNPJ / CPF<br><b>{$mD024['D024_Cnpj']}</b></font>";
                                        echo "</td>";
                                        echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>I.E.<br>";
                                            if (empty($mD024['D024_Inscricao_Estadual']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_Inscricao_Estadual']}</b></font>";
                                            }
                                        echo "</td>";
                                    echo "</tr>";

                                    echo "<tr>";
                                        echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>Endereço<br>";
                                            if (empty($mD024['D024_Endereco']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_Endereco']}</b></font>";
                                            }
                                        echo "</td>";
                                        echo "<td align=left bgcolor=white  style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>Número<br>";
                                            if (empty($mD024['D024_Numero']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_Numero']}</b></font>";
                                            }
                                        echo "</td>";
                                        echo "<td align=left bgcolor=white  style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>Bairro<br>";
                                            if (empty($mD024['D024_Bairro']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_Bairro']}</b></font>";
                                            }
                                        echo "</td>";
                                        echo "<td bgcolor=white align=left style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>Cidade<br><b>{$mD024['D020_Nome_Cidade']}</b> </font>";
                                        echo "</td>";
                                        echo "<td bgcolor=white align=left style='border-bottom:1px solid silver'>";
                                            echo "<font class=fontemed>UF<br><b>{$mD024['D018_UF']}</b> </font>";
                                        echo "</td>";
                                    echo "</tr>";

                                    echo "<tr>";
                                        echo "<td align=left bgcolor=white >";
                                            echo "<font class=fontemed>DDD<br>";
                                            if (empty($mD024['D024_DDD_Telefone_1']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_DDD_Telefone_1']}</b></font>";
                                            }
                                        echo "</td>";
                                        echo "<td align=left bgcolor=white >";
                                            echo "<font class=fontemed>Telefone<br>";
                                            if (empty($mD024['D024_Telefone_1']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['D024_Telefone_1']}</b></font>";
                                            }
                                        echo "</td>";
                                        echo "<td bgcolor=white align=left>";
                                                echo "<font class=fontemed>FAX<br><b>{$mD024['D024_Fax']}&nbsp;</b></font>";
                                            echo "</a>";
                                        echo "</td>";
                                        echo "<td bgcolor=white >";
                                                echo "<font class=fontemed>Contato<br><b>{$mD024['D024_Nome_Contato']}&nbsp;</b></font>";
                                            echo "</a>";
                                        echo "</td>";
                                        echo "<td bgcolor=white colspan=2>";
                                            echo "<font class=fontemed>Vendedor<br>";
                                            if (empty($mD024['C007_Primeiro_Nome']))
                                            {
                                                echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                            }
                                            else
                                            {
                                                echo "<b>{$mD024['C007_Primeiro_Nome']}</b></font>";
                                            }
                                            echo "</a>";
                                        echo "</td>";
                                    echo "</tr>";

                                echo "</table>";
                            echo "</td>";
                        echo "</tr>";

                    echo "</table>";

            echo "</td>";

    echo "</tr>";

        echo "<td align=center>";

        echo "<table border=0 cellspacing=0 width='900' align=center cellpadding=5 style='border:1px solid #09387E'>";
            echo "<tr>";
                echo "<td colspan=10 bgcolor='#09387E' style='color:white;font-size:14px;font-weight:bold;padding:1px'>";
                    echo "DETALHES";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td align=left width='130' class='fontemed' style='font-weight:normal;font-size:12px'>";
                echo 'Prazo de Entrega:';
            echo "</td>";
            echo "<td align=left class='fontemed' style='font-weight:bold;font-size:12px'>";
                echo "<input type='text' name='T218_Prazo_Entrega' id='T218_Prazo_Entrega' value='{$mT218['T218_Prazo_Entrega']}' size=55 style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT218['T218_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=left width='55' class='fontemed' style='font-weight:normal;font-size:12px'>";
                echo 'Transporte:';
            echo "</td>";
            echo "<td align=left class='fontemed' style='font-weight:bold;font-size:12px'>";
                echo "<input type='text' name='T218_Frete' id='T218_Frete' value='{$mT218['T218_Frete']}' size=55 style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT218['T218_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
        echo "</tr>";
            echo "<tr>";
            echo "<td align=left width='130' class='fontemed' style='font-weight:normal;font-size:12px'>";
                echo 'Forma de Pagamento:';
            echo "</td>";
            echo "<td align=left class='fontemed' style='font-weight:bold;font-size:14px'>";
                echo "<input type='text' name='T218_Forma_Pagamento' id='T218_Forma_Pagamento' value='{$mT218['T218_Forma_Pagamento']}' size=55 style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT218['T218_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td align=center>";

    echo "<table border=0 cellspacing=0 width='900px' align=center cellpadding=4 style='border:1px solid #09387E'>";

        echo "<tr bgcolor=#F4F4F4>";
    /*  		echo "<td align=center width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Cód<br />Produto";
            echo "</td>"; */
            echo "<td align=center width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Cód<br />Produto";
            echo "</td>";
            echo "<td align=left  class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Descrição";
            echo "</td>";
    /*		echo "<td align=left width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
                echo "Marca";
            echo "</td>"; */
            echo "<td align=right width='30' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Qtde";
            echo "</td>";
            echo "<td align=center width='20' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Un";
            echo "</td>";
            echo "<td align=right width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Preço Unitário";
            echo "</td>";
            echo "<td align=right width='30' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "% IPI";
            echo "</td>";
        echo "<td align=right width='30' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "% ICM";
        echo "</td>";
        echo "<td align=right width='30' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Total ST R$";
        echo "</td>";
        echo "<td align=right width='30' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "MVA";
        echo "</td>";
            echo "<td align=left width='100px' class='fontemed' style='font-weight:bold;border-bottom:1px solid #000;color:white;background:#09387E'>";
                echo "Entrega";
            echo "</td>";
        echo "</tr>";

    $cor='#CCDFF9';
    
    $Total=0;

    while ($mT219 = mysql_fetch_array($T219))
    {

        if ($cor=='#CCDFF9')
        {
            $cor='white';
        }
        else
        {
            $cor='#CCDFF9';
        }

        echo "<tr bgcolor=$cor Id='l_{$mT219['T219_Id']}' height=19>";

    /*          echo "<td align=center class='fontemed' width=30>";
                echo $mT219['D001_Codigo_Produto'];
            echo "</td>";*/
            echo "<td align=center class='fontemed' width=30>";
                        echo "<input type='text' name='T219_Codigo_Substituto' id='T219_Codigo_Substituto' value='{$mT219['T219_Codigo_Substituto']}' size=12 style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=left class='fontemed' >";
                echo $mT219['T219_Descricao_Produto'].' <b>'.$mT219['D049_Modelo'].'</b>';
            echo "</td>";
    /*          echo "<td align=left class='fontemed' width=60>";
                //echo $mT219['D082_Marca'];
                    
            $D009=mysql_query("select D082_Marca                                             
                                    from T219
                            left join D009 on D009_Id=T219_D009_Id      
                            left join D049 on D049_Id=D009_D049_Id
                            left join D082 on D082_Id=D049_D082_Id
                                where T219_Id='{$mT219['T219_Id']}'
                                    and D009_C004_Id='$Empresa_Atual'");
            $mD009=mysql_fetch_array($D009);               	            	   
            echo $mD009['D082_Marca'];

            echo "</td>"; */
            echo "<td align=right class='fontemed'>";
                    echo "<input type='text' name='T219_Quantidade' id='T219_Quantidade' value='".number_format($mT219['T219_Quantidade'],3,',','.')."' size=8 style='border:1px solid silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=center>";
                echo $mT219['D037_Unidade'];
            echo "</td>";
            echo "<td align=right class='fontemed'>";
                    echo "<input type='text' name='T219_Valor_Preco_Unitario' id='T219_Valor_Preco_Unitario' value='".number_format($mT219['T219_Valor_Preco_Unitario'],5,',','.')."' size=8 style='border:1px solid silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=right class='fontemed' >";
                    echo "<input type='text' name='T219_Aliquota_IPI' id='T219_Aliquota_IPI' value='".number_format($mT219['T219_Aliquota_IPI'],2,',','.')."' size=4 style='border-color:silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=right class='fontemed' >";
                echo "<input type='text' name='T219_Aliquota_ICMS' id='T219_Aliquota_ICMS' value='".number_format($mT219['T219_Aliquota_ICMS'],2,',','.')."' size=4 style='border-color:silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=right class='fontemed' >";
                echo "<input type='text' name='T219_Valor_ST_Digitado' id='T219_Valor_ST_Digitado' value='".number_format($mT219['T219_Valor_ST_Digitado'],2,',','.')."' size=4 style='border-color:silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=right class='fontemed' >";
                echo "<input type='text' name='T219_ST_VA' id='T219_Valor_ST' value='".number_format($mT219['T219_ST_VA'],2,',','.')."' size=4 style='border-color:silver;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
            echo "<td align=center class='fontemed' >";
                echo "<input type='text' name='T219_Observacao_Entrega' id='T219_Observacao_Entrega' value='{$mT219['T219_Observacao_Entrega']}' style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;width:95px' onBlur=\"GravarEdicaoCampo(this,'{$mT219['T219_Id']}')\" class='formInput ui-state-default ui-corner-all'>";
            echo "</td>";
        echo "</tr>";
        $Total+=$mT219['T219_Valor_Preco_Unitario']*$mT219['T219_Quantidade'];

    }

    echo "</form>";
    echo "</table>";

    $mT218['T218_Valor_Total_Produtos'] = number_format($mT218['T218_Valor_Total_Produtos'],2,',','.');
    $mT218['T218_Valor_Total_ICMS'] = number_format($mT218['T218_Valor_Total_ICMS'],2,',','.');
    $mT218['T218_Valor_Total_IPI'] = number_format($mT218['T218_Valor_Total_IPI'],2,',','.');
    $mT218['T218_Valor_ST'] = number_format($mT218['T218_Valor_ST'],2,',','.');
    $mT218['T218_Valor_Total'] = number_format($mT218['T218_Valor_Total'],2,',','.');

    echo "</td>";

    echo "</tr>";

    echo "<tr>";

    echo "<td align=center>";

    echo "<table align=right style='border:1px solid #09387E;font-size:12px' width='900px' cellspacing=0 cellpadding=5px>
        <tr>
            <td colspan=10 bgcolor='#09387E' style='color:white;font-size:14px;font-weight:bold;padding:1px'>
            TOTAIS
            </td>
        </tr>
        <tr>
            <td>
                Produtos: R$ 
            </td>
            <td style='font-weight:bold;font-size:18px;'>
                {$mT218['T218_Valor_Total_Produtos']}
            </td>
            <td>
                ICMS: R$
            </td>
            <td  style='font-weight:bold;font-size:18px;'>
                {$mT218['T218_Valor_Total_ICMS']}
            </td>
            <td>
                IPI: R$
            </td>
            <td  style='font-weight:bold;font-size:18px;'>
                {$mT218['T218_Valor_Total_IPI']}
            </td>
            <td>
                ST: R$
            </td>
            <td  style='font-weight:bold;font-size:18px;'>
                {$mT218['T218_Valor_ST']}
            </td>
            <td>
                Total: R$
            </td>
            <td  style='font-weight:bold;font-size:18px;'>
            {$mT218['T218_Valor_Total']}
            </td>
        <tr>
    </table>";           

    echo "</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td align=center>";

        echo "<table border=0 cellspacing=0 width='900px' align=center cellpadding=5 style='border:1px solid #09387E'>";
            echo "<tr>";
                echo "<td colspan=10 bgcolor='#09387E' style='color:white;font-size:14px;font-weight:bold;padding:1px'>";
                        echo "OBSERVAÇÃO";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td align=left height='80px'>";
                    echo "<textarea name='T218_Observacao' id='T218_Observacao'  style='border:1px solid silver;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;width:99%;height:99%' xystyle='order:1px solid #000;background:#fff;text-align:left;font-size:12px;-moz-border-radius:0;width:100%;height:100%;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box' onBlur=\"GravarEdicaoCampo(this,'{$mT218['T218_Id']}')\" class='formInput ui-state-default ui-corner-all'> {$mD024['T218_Observacao']} </textarea>";
                echo "</td>";
            echo "</tr>";
            echo "</table>";

            echo "</td>";
        echo "</tr>";
    echo "<tr>";

    echo "<tr>";
    echo "<td align=left valign=top>";


    /*
    if (!empty($mT218['C007_Email_Assinatura'])) {
    $assinatura = $mT218['C007_Email_Assinatura'];
    echo "<br /><br /><div style=\"text-align:left; font-size: 12px;\"><span align=\"left\">{$assinatura}</span></div>";
    }
    */

    $link = "{$confUsuario['urlRaiz']}est/est015/outro/est015OutrosCotacaoCompraOnline/?p={$r_p}&oneWindow=true&Finalizar=S";

    echo "<table align=center border='0' valign=top cellpadding=5px cellspacing=2px bgcolor=white width='900px' style='border-top:0px dashed #09387E' >";
    echo "<tr>";
    echo "<td align=left valign=top>";
        echo "<font class=fonte style='font-size:12px'>Hardness Sistemas - www.hardness.com.br";
    echo "</td>";
    echo "<td valign=top align=right width=50px>";
        echo "<input type='button' value='Totalizar' style='font-size:18px;font-weight:normal;background:#CCDFF9;border:1px solid #CCDFF9' onclick='javascript:location.reload();'>";
    echo "</td>";
    echo "<td valign=top align=right width=50px>";
        echo "<input type='button' value='Finalizar' style='font-size:18px;font-weight:normal;background:#CCDFF9;border:1px solid #CCDFF9' onclick='javascript:window.location.assign(\"$link\");'>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";


    echo "<iframe name='fme_gravar' id='fme_gravar' src='' style='position:absolute;display:none;top:0;width:100;height:100;'></iframe>";

    echo "</td>";
    echo "</tr>";
    echo "</table>";

         //include 'prog/est/est015/cotacaoViaEmail.php';
    break;

    case 'est015AnaliseOC':
        global $g, $confUsuario;
        
        /**
        * SELECT's
        */
        $C004 = mysql_query("SELECT C004_Id FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        echo mysql_error();
    
        $T218_Id = mysql_real_escape_string($r_T218_Id);
        $T218 =  <<<EOT
                SELECT 
                    T218_Id,
                    T218_Data_Emissao,
                    D024_Nome_Empresa,
                    D024_Endereco,
                    D024_Numero,
                    D024_Bairro,
                    D024_Cep,
                    D020_Nome_Cidade,
                    D018_UF,
                    D024_Cnpj,
                    D024_Cpf,
                    D024_Inscricao_Estadual,
                    D013_Nome_Contato,
                    D013_Telefone_1,
                    D013_E_Mail,
                    comprador.C007_Primeiro_Nome as compradorC007_Primeiro_Nome,
                    comprador.C007_Email as compradorC007_Email,
                    comprador.C007_DDD_Telefone as compradorC007_DDD_Telefone,
                    comprador.C007_Telefone as compradorC007_Telefone,
                    T218_Valor_Total,
                    T218_Valor_Total_Produtos,
                    T218_Valor_Frete,
                    T218_Valor_Total_IPI,
                    T218_Valor_Total_ICMS,
                    T218_Valor_Total,
                    T218_Data_Entrega,
                    T218_Valor_ST,
                    T218_Flag_Frete,
                    D022_Nome_Empresa
                FROM T218 
                LEFT JOIN D013 ON D013_Id=T218_D013_Id 
                LEFT JOIN D116 ON D116_Id=T218_D116_Id 
                LEFT JOIN D133 ON D133_Id=D116_D133_Id 
                LEFT JOIN D134 ON D134_Id=D116_D134_Id 
                LEFT JOIN D024 ON D024_Id=T218_D024_Id 
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                LEFT JOIN D022 ON D022_Id=T218_D022_Id
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=T218_C007_Id_Vendedor_Interno
                WHERE T218_Id='{$T218_Id}';
EOT;

        $T218 = mysql_query($T218);
        $mT218 = mysql_fetch_array($T218);
        echo mysql_error();

        $T219 = <<<EOT
                SELECT 
                    D001_Id,
                    T219_Codigo_Produto,
                    T219_Descricao_Produto,
                    T219_Quantidade,
                    T219_Valor_Preco_Sem_Desconto_Unitario,
                    T219_Valor_Total_Preco,
                    T219_Valor_Custo_Unitario,
                    T219_IPV,
                    T219_Valor_Preco_Venda,
                    T219_Valor_Preco_Venda_Negociado,
                    T219_Observacao_Entrega,
                    D037_Unidade,
                    D005_Classificacao_Fiscal,
                    D006_Codigo_CFOP,
                    T219_Aliquota_IPI,
                    T219_Aliquota_ICMS,
                    T219_ST_VA_Ajustada,
                    T219_Valor_ST,
                    T219_Valor_Frete_Unitario,
                    D009_Quantidade_Estoque,
                    D009_Quantidade_Comprar_Calculada,
                    D009_Quantidade_Consumo ,
                    D009_Quantidade_Minima_Estoque,
                    D009_Quantidade_Maxima_Estoque,
                    D009_Frequencia_Venda,
                    D009_Clientes_Consumo,
                    D009_Frequencia_Venda_Meses,
                    T218_Id,
                    T218_Data_Emissao,
                    D024_Nome_Empresa,
                    T218_Forma_Pagamento,
                    T219_D009_Id,
                    datediff(curdate(),T001_Data_Lancamento) as Dias 
                FROM T219 
                LEFT JOIN D009 ON D009_Id=T219_D009_Id 
                LEFT JOIN T218 ON T218_Id=T219_T218_Id
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                LEFT JOIN D003 ON D003_Id=D001_D003_Id
                LEFT JOIN T144 ON T144_D001_Id=D001_Id
                LEFT JOIN D037 ON D037_Id=T219_D037_Id
                LEFT JOIN D005 ON D005_Id=T219_D005_Id
                LEFT JOIN D006 ON D006_Id=T219_D006_Id
                LEFT JOIN T001 on T001_Id=D009_T001_Id
                LEFT JOIN D024 ON D024_Id=T001_D024_Id
                WHERE T219_T218_Id = '{$T218_Id}' 
                GROUP BY T219_Id
                ORDER BY D009_Id ASC, IF('{$T218_Id}'=T218_Id,1,2) ASC
EOT;
        $T219 = mysql_query($T219); 
        echo mysql_error();
        
        
        $html .= <<<EOT
        <style>
            .body {
                margin: 0px 0px 0px 0px;
                background: white;
                background-color: white;
                color: #000;
            }
            .moldura {
                font-size: 12px;
                background:white;
                cellspacing:0px;
                width:100%;
                padding: 0px;
                margin: 0px 0px 0px 0px;
                color: #000
            }           
            .moldura td {
                background:white;
                padding:1px 1px 1px 1px;
                color: #000
            }           
            .tabela {
                cellspacing:0px;
                font-size:12px;
                padding: 2px 2px 2px 2px;
                color: #000
                background: #fff;

            }           
            .tdBorda {
                border-right:1px solid silver;
                border-bottom:1px dotted silver;
                background:white;
                font-size:12px;
                color: #000
            }           
            .tdBordaR {
                font-size:12px;
                background:white;
                border-bottom:1px dotted silver;
                color: #000
            }           
            .tdBordaL {
                border-right:1px solid silver;
                border-bottom:1px dotted silver;
                background:white;
                font-size:12px;
                color: #000
            }           
            .tdTitulo {
                border-right:1px solid silver;
                font-size:12px;
            }           
            .tdTituloR {
                font-size:12px;
                background:lightblue;
                color: #fff
            }           
            .tdTituloL {
                border-right:1px solid silver;
                font-size:12px;
                background:lightblue;
                color: #fff
            }           
            table {
                font-family: Arial;
                color: #000;
                background-color: #fff !important;
            }  
        </style>
EOT;

        /**
        * PDF - HEADER
*/
// (TODO) passar para o arq. conf
$caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
$caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);

$mT218['T218_Data_Emissao'] = gCorrigeData($mT218['T218_Data_Emissao']);
$T218_Numero_Ordem_Compra = str_pad($T218_Id, 6, "0", STR_PAD_LEFT);

$html .= <<<EOT
<body bgcolor="white">
<table class="moldura">            
    <tr>
        <td colspan="3" style="background:white;font-size:18px;font-weight:bold;padding:5px 5px 5px 5px;border-bottom:1px solid Black" align="center">
            ANÁLISE COTAÇÃO DE COMPRA
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table class="tabela" style="width:1000px">
                <tr>
                    <td width="30%">
                        <table class="tabela" width="100%">
                            <tr>
                                <td><b>{$mT218['D024_Nome_Empresa']}</b></td>
                            </tr>
                            <tr>
                                <td>{$mT218['D024_Endereco']}, {$mT218['D024_Numero']} - {$mT218['D024_Bairro']}</td>
                            </tr>
                            <tr>
                                <td><b>CEP:</b> {$mT218['D024_Cep']} {$mT218['D020_Nome_Cidade']} - {$mT218['D018_UF']}</td>
                            </tr>
                            <tr>
                                <td><b>CNPJ/CPF:</b> {$mT218['D024_Cnpj']}{$mT218['D024_Cpf']}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="30%">
                        <table class="tabela" width="100%">
                            <tr>
                                <td><b>Incr. Estadual:</b> {$mT218['D024_Inscricao_Estadual']}</td>
                            </tr>
                            <tr>
                                <td><b>Contato: </b>{$mT218['D013_Nome_Contato']}</td>
                            </tr>
                            <tr>
                                <td><b>Telefone: </b>{$mT218['D013_Telefone_1']}</td>
                            </tr>
                            <tr>
                                <td><b>E-mail: </b>{$mT218['D013_E_Mail']}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="25%">
                        <table class="tabela" width="100%">                                    
                            <tr>
                                <td>
                                    <table class="tabela">
                                        <tr>
                                            <td><b><u>COMPRADOR:</u></b></td>
                                        </tr>
                                        <tr>
                                            <td>{$mT218['compradorC007_Primeiro_Nome']}</td>
                                        </tr>
                                        <tr>
                                            <td>{$mT218['compradorC007_Email']}</td>
                                        </tr>
                                        <tr>
                                            <td>{$mT218['compradorC007_DDD_Telefone']}-{$mT218['compradorC007_Telefone']}</td>
                                        </tr>
                                    </table>                    
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td style="font-size:18px" align="center">
                                    <b>Cotação<br>Nº {$T218_Numero_Ordem_Compra}<br>{$mT218['T218_Data_Emissao']}<b>
                                </td>
                            </tr>
                        </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
EOT;
        
        /**
        * PDF - TABELA PRODUTOS
        */
        $html .= <<<EOT
            <tr>
                <td colspan="3" style="padding:0px 0px 0px 0px;border-top:1px solid Black" >
                    <table cellspacing="0" cellpadding="4px" width="100%" height="100%" style="border:0px;padding:2px 2px 2px 2px">
EOT;
        $count = 1;
        $valorTotalProd = $D009Id = 0;

        $html .= <<<EOT
            <tr>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    Produto
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    Quant
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Preço R\$
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Total R\$
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Custo
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    IPV
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Venda
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Venda
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Sobra
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Entrega
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    Und
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    NCM
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    CFOP
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    %IPI
                </td>
                <td align="center" style="background:#58657A;color:white" class="tdTitulo">
                    %ICM
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    MVA
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    ST R\$
                </td>
                <td align="right" style="background:#58657A;color:white" class="tdTitulo">
                    Frete R\$
                </td>
                <td align="center" style="background:#58657A;color:white;width:60px" class="tdTitulo">
                    Estoque
                </td>
                <td align="center" style="background:#58657A;color:white;width:60px" class="tdTitulo">
                    Comprar
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Vendido
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Mínimo
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Máximo
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Freq
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Client
                </td>
                <td class="tdTitulo" align="center" style="background:#58657A;color:white;">
                    Freq Mes
                </td>
            </tr>
EOT;

        while($mT219 = mysql_fetch_array($T219)){
            $mT219['sobra'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda_Negociado']-$mT219['T219_Valor_Preco_Venda']);
            if ($mT219['T219_Valor_Preco_Venda_Negociado']<=0){
                $mT219['sobra'] = 0;
            }
            $mT219['T219_Quantidade']      = gCorrigeNumero($mT219['T219_Quantidade']);
            $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT219['T219_Valor_Preco_Sem_Desconto_Unitario'],5,',','.');
            $mT219['T219_Valor_Total_Preco'] = gCorrigeNumero($mT219['T219_Valor_Total_Preco']);
            $mT219['T219_Aliquota_IPI']    = number_format($mT219['T219_Aliquota_IPI'],0);
            $mT219['T219_Valor_IPI']       = gCorrigeNumero($mT219['T219_Valor_IPI']);
            $mT219['T219_Aliquota_ICMS']   = number_format($mT219['T219_Aliquota_ICMS'],0);
            $mT219['T219_Valor_ICMS']      = gCorrigeNumero($mT219['T219_Valor_ICMS']);
            $mT219['T219_Valor_ST'] = gCorrigeNumero($mT219['T219_Valor_ST']);
            $mT219['T219_ST_VA_Ajustada'] = gCorrigeNumero($mT219['T219_ST_VA_Ajustada']);
            $mT219['T219_Valor_Preco_Venda_Negociado'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda_Negociado']);
            $mT219['D009_Quantidade_Estoque'] = gCorrigeNumero($mT219['D009_Quantidade_Estoque']);
            $mT219['D009_Quantidade_Consumo'] = gCorrigeNumero($mT219['D009_Quantidade_Consumo']);
            $mT219['D009_Quantidade_Comprar_Calculada'] = gCorrigeNumero($mT219['D009_Quantidade_Comprar_Calculada']);
            $mT219['T001_Quantidade'] = gCorrigeNumero($mT219['T001_Quantidade']);
            $mT219['T001_Valor_Preco_Unitario'] = number_format($mT219['T001_Valor_Preco_Unitario'],5,',','.');
            $mT219['T001_Data_Lancamento'] = gCorrigeData($mT219['T001_Data_Lancamento']);
            $mT219['T219_IPV'] = gCorrigeNumero($mT219['T219_IPV']);
            $mT219['T219_Valor_Preco_Venda'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda']);
            $mT219['T219_Valor_Custo_Unitario'] = gCorrigeNumero($mT219['T219_Valor_Custo_Unitario']);
            $mT219['T219_Valor_Frete_Unitario'] = gCorrigeNumero($mT219['T219_Valor_Frete_Unitario']);
            $mT219['D009_Quantidade_Minima_Estoque'] = gCorrigeNumero($mT219['D009_Quantidade_Minima_Estoque']);
            $mT219['D009_Quantidade_Maxima_Estoque'] = gCorrigeNumero($mT219['D009_Quantidade_Maxima_Estoque']);
            $observacaoItem='<font></font>';
            if (!empty($mT219['T219_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> '.$mT219['T219_Observacao'].'</font><br>';
            }
            if (!empty($mT219['T219_Codigo_Substituto'])){
                //$mT219['T219_Codigo_Produto']=$mT219['T219_Codigo_Substituto'];
            }
            //$corLinha = ($mT219['T219_T218_Id'] == $T218_Id) ? 'style="background-color: #DDD;"' : '';
            
            $corLinha="background:white;";

            $html .=<<<EOT
               <tr onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique() , '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$mT219['D001_Id']}') + '&tabela=D001', [1000,600]);">
                <td align="left" style="{$corLinha}max-width:400px;min-width:450px;width:450px;border-top:1px solid silver;cursor: pointer;" class="tdBorda" width="300px">
                    <b><u>{$mT219['T219_Codigo_Produto']} - {$mT219['T219_Descricao_Produto']}</u></b>
                </td>
                <td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    <b>{$mT219['T219_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    <b>{$mT219['T219_Valor_Preco_Sem_Desconto_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_Valor_Total_Preco']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_IPV']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_Valor_Preco_Venda']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_Valor_Preco_Venda_Negociado']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['sobra']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_Observacao_Entrega']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_Valor_ST']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT219['T219_Valor_Frete_Unitario']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
                    {$mT219['D009_Quantidade_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
                    {$mT219['D009_Quantidade_Comprar_Calculada']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Quantidade_Consumo']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Quantidade_Minima_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Quantidade_Maxima_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Frequencia_Venda']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Clientes_Consumo']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['D009_Frequencia_Venda_Meses']}
                </td>
            </tr>
EOT;

            $xT219 = <<<EOT
                    SELECT 
                        T218_Id,
                        T218_Data_Emissao,
                        D024_Nome_Empresa,
                        T219_Valor_Preco_Venda_Negociado,
                        T219_Valor_Preco_Venda,
                        T219_Quantidade,
                        T219_Valor_Preco_Sem_Desconto_Unitario,
                        T219_Valor_Total_Preco,
                        T219_Aliquota_IPI,
                        T219_Valor_IPI,
                        T219_Aliquota_ICMS,
                        T219_Valor_ICMS,
                        T219_Valor_ST,
                        T219_ST_VA_Ajustada,
                        T219_Valor_Preco_Venda_Negociado,
                        D009_Quantidade_Estoque,
                        D009_Quantidade_Consumo,
                        D009_Quantidade_Comprar_Calculada,
                        T219_IPV,
                        T219_Valor_Preco_Venda,
                        T219_Valor_Custo_Unitario,
                        T219_Valor_Frete_Unitario,
                        D009_Quantidade_Minima_Estoque,
                        D009_Quantidade_Maxima_Estoque,
                        T219_Observacao,
                        T219_Codigo_Substituto
                    FROM T219 
                    LEFT JOIN D009 ON D009_Id=T219_D009_Id 
                    LEFT JOIN T218 ON T218_Id=T219_T218_Id
                    LEFT JOIN D049 ON D049_Id=D009_D049_Id
                    LEFT JOIN D001 ON D001_Id=D049_D001_Id
                    LEFT JOIN D003 ON D003_Id=D001_D003_Id
                    LEFT JOIN T144 ON T144_D001_Id=D001_Id
                    LEFT JOIN D037 ON D037_Id=T219_D037_Id
                    LEFT JOIN D005 ON D005_Id=T219_D005_Id
                    LEFT JOIN D006 ON D006_Id=T219_D006_Id
                    LEFT JOIN D024 ON D024_Id=T218_D024_Id
                    WHERE T219_D009_Id = '{$mT219['T219_D009_Id']}' 
                      AND T218_Id!='$T218_Id'
                    GROUP BY T219_Id
                    ORDER BY T218_Id desc
EOT;

            $xT219 = mysql_query($xT219); 
            echo mysql_error();

        while ($xmT219=mysql_fetch_array($xT219)){
            $xmT219['sobra'] = gCorrigeNumero($xmT219['T219_Valor_Preco_Venda_Negociado']-$xmT219['T219_Valor_Preco_Venda']);
             if ($xmT219['T219_Valor_Preco_Venda_Negociado']<=0){
                 $xmT219['sobra'] = 0;
            }           
            $xmT219['T218_Data_Emissao']    = gCorrigeData($xmT219['T218_Data_Emissao']);
            $xmT219['T219_Quantidade']      = gCorrigeNumero($xmT219['T219_Quantidade']);
            $xmT219['T219_Valor_Preco_Sem_Desconto_Unitario'] = number_format($xmT219['T219_Valor_Preco_Sem_Desconto_Unitario'],5,',','.');
            $xmT219['T219_Valor_Total_Preco'] = gCorrigeNumero($xmT219['T219_Valor_Total_Preco']);
            $xmT219['T219_Aliquota_IPI']    = number_format($xmT219['T219_Aliquota_IPI'],0);
            $xmT219['T219_Valor_IPI']       = gCorrigeNumero($xmT219['T219_Valor_IPI']);
            $xmT219['T219_Aliquota_ICMS']   = number_format($xmT219['T219_Aliquota_ICMS'],0);
            $xmT219['T219_Valor_ICMS']      = gCorrigeNumero($xmT219['T219_Valor_ICMS']);
            $xmT219['T219_Valor_ST'] = gCorrigeNumero($xmT219['T219_Valor_ST']);
            $xmT219['T219_ST_VA_Ajustada'] = gCorrigeNumero($xmT219['T219_ST_VA_Ajustada']);
            $xmT219['T219_IPV'] = gCorrigeNumero($xmT219['T219_IPV']);
            $xmT219['T219_Valor_Preco_Venda'] = gCorrigeNumero($xmT219['T219_Valor_Preco_Venda']);
            $xmT219['T219_Valor_Custo_Unitario'] = gCorrigeNumero($xmT219['T219_Valor_Custo_Unitario']);
            $xmT219['T219_Valor_Preco_Venda_Negociado'] = gCorrigeNumero($xmT219['T219_Valor_Preco_Venda_Negociado']);
            $xmT219['T219_Valor_Frete_Unitario'] = gCorrigeNumero($xmT219['T219_Valor_Frete_Unitario']);
            $xmT219['D024_Nome_Empresa'] = substr($xmT219['D024_Nome_Empresa'],0,20);
            $observacaoItem='<font></font>';
            if (!empty($xmT219['T219_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> '.$xmT219['T219_Observacao'].'</font><br>';
            }
            if (!empty($xmT219['T219_Codigo_Substituto'])){
                //$mT219['T219_Codigo_Produto']=$mT219['T219_Codigo_Substituto'];
            }
            if ($corLinha=="background:white;"){
                $corLinha='background:#D1EEFC;';
            } else {
                $corLinha="background:white;";
            }
            $html .= "<tr>";
            $html .=<<<EOT
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    Cotação: {$xmT219['T218_Id']} - {$xmT219['T218_Data_Emissao']} - {$xmT219['D024_Nome_Empresa']}
                </td>
                <td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
                    <b>{$xmT219['T219_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
                    <b>{$xmT219['T219_Valor_Preco_Sem_Desconto_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_Valor_Total_Preco']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$xmT219['T219_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$xmT219['T219_IPV']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$xmT219['T219_Valor_Preco_Venda']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$xmT219['T219_Valor_Preco_Venda_Negociado']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$xmT219['sobra']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; padding: 5px 5px 5px 5px">
                    {$xmT219['T219_Observacao_Entrega']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_Valor_ST']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$xmT219['T219_Valor_Frete_Unitario']}
                </td>
                <td align="left" style='{$corLinha}padding: 5px 5px 5px 5px' class="tdBordaR" colspan=30>
                    Pagamento: {$xmT219['T218_Forma_Pagamento']}
                </td>
            </tr>
EOT;
        }

        $T001 = <<<EOT
                SELECT 
                T001_Data_Lancamento,
                T001_Quantidade,
                T001_Id,
                T001_Flag_Operacao,
                T001_Descricao_Operacao,
                T001_Valor_Preco_Unitario,
                D024_Nome_Empresa,
                T001_Valor_Custo_Unitario,
                D037_Unidade,
                T014_Classificacao_Fiscal,
                T001_Aliquota_IPI,
                T001_Aliquota_ICMS,
                T014_MVA_Ajustada,
                T001_Valor_ST,
                T001_Valor_Frete
                FROM T001
                LEFT JOIN D009 ON D009_Id=T001_D009_Id 
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                LEFT JOIN T014 ON T014_Id=T001_T014_Id
                LEFT JOIN T013 ON T013_Id=T014_T013_Id
                LEFT JOIN D037 ON D037_Id=D001_D037_Id
                LEFT JOIN T008 ON T008_Id=T001_T008_Id
                LEFT JOIN D006 ON D006_Id=T014_D006_Id OR D006_Id=T008_D006_Id
                LEFT JOIN D024 ON D024_Id=T001_D024_Id
                WHERE T001_D009_Id = '{$mT219['T219_D009_Id']}'
                AND D006_Flag_Venda_Compra_Outros='C' 
                AND T001_Flag_Operacao='E'
                GROUP BY T001_Id
                ORDER BY T001_Id desc
                LIMIT 3
EOT;
        $T001 = mysql_query($T001); 
        echo mysql_error();

        while ($mT001=mysql_fetch_array($T001)){
            $mT001['T001_Data_Lancamento']      = gCorrigeData($mT001['T001_Data_Lancamento']);
            $mT001['T001_Valor_Total_Preco']    = gCorrigeNumero($mT001['T001_Valor_Preco_Unitario']*$mT001['T001_Quantidade']);
            $mT001['T001_Valor_Preco_Unitario'] = number_format($mT001['T001_Valor_Preco_Unitario'],5,',','.');
            $mT001['T001_Quantidade']           = gCorrigeNumero($mT001['T001_Quantidade']);
            $mT001['D024_Nome_Empresa']         = substr($mT001['D024_Nome_Empresa'],0,20);
            $mT001['T001_Valor_Custo_Unitario'] = gCorrigeNumero($mT001['T001_Valor_Custo_Unitario']);
            $mT001['T001_Aliquota_IPI']         = gCorrigeNumero($mT001['T001_Aliquota_IPI']);
            $mT001['T001_Aliquota_ICMS']        = gCorrigeNumero($mT001['T001_Aliquota_ICMS']);
            $mT001['T014_MVA_Ajustada']         = gCorrigeNumero($mT001['T014_MVA_Ajustada']);
            $mT001['T001_Valor_ST']             = gCorrigeNumero($mT001['T001_Valor_ST'],5);
            $mT001['T001_Valor_Frete']          = gCorrigeNumero($mT001['T001_Valor_Frete'],5);
            $observacaoItem='<font></font>';
            if ($corLinha=="background:white;"){
                $corLinha='background:#D1EEFC;';
            } else {
                $corLinha="background:white;";
            }
            $html .= "<tr>";
            $html .=<<<EOT
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    Histórico: {$mT001['T001_Descricao_Operacao']} - {$mT001['T001_Data_Lancamento']} - {$mT001['D024_Nome_Empresa']}
                </td>
                <td align="center" class="tdBorda" style='background-color: #C9E1FC;padding: 5px 5px 5px 5px'>
                    <b>{$mT001['T001_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #C9E1FC;padding: 5px 5px 5px 5px'>
                    <b>{$mT001['T001_Valor_Preco_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px'>
                    {$mT001['T001_Valor_Total_Preco']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                    {$mT001['T001_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; padding: 5px 5px 5px 5px">
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T014_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T001_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T001_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T014_MVA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T001_Valor_ST']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT001['T001_Valor_Frete']}
                </td>
                <td align="left" style='{$corLinha}padding: 5px 5px 5px 5px' class="tdBordaR" colspan=30>
                </td>
            </tr>
EOT;
        }

        $D069 = <<<EOT
            SELECT
            D069_D070_Id,
            D070_Data,
            D024_Nome_Empresa,
            D037_Unidade,
            D005_Classificacao_Fiscal,
            D006_Codigo_CFOP,
            D069_Valor_Preco_Original,
            D069_Valor_Custo_Unitario,
            D069_Valor_ICMS_Substituicao_Retencao,
            D069_Aliquota_IPI,
            D069_Aliquota_ICMS,
            D069_ST_VA_Ajustada,
            D069_Observacao_Compra,
            D069_Valor_Frete
            FROM D069
            LEFT JOIN D070 ON D070_Id=D069_D070_Id
            LEFT JOIN D009 ON D009_Id=D069_D009_Id
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            LEFT JOIN D037 ON D037_Id=D069_D037_Id
            LEFT JOIN D006 ON D006_Id=D069_D006_Id
            LEFT JOIN D005 ON D005_Id=D069_D005_Id
            LEFT JOIN D024 ON D024_Id=D070_D024_Id
            WHERE D069_D009_Id = '{$mT219['T219_D009_Id']}'
            AND D070_Flag_Ativo = 'S'
            GROUP BY D069_Id
            ORDER BY D069_Id desc
EOT;

        $D069 = mysql_query($D069); 
        echo mysql_error();

        while ($mD069=mysql_fetch_array($D069)){
            $mD069['D070_Data']                             = gCorrigeData($mD069['D070_Data']);
            $D069_Valor_Preco_Original_                     = gCorrigeNumero($mD069['D069_Valor_Preco_Original']);
            $mD069['D069_Valor_Preco_Original']             = gCorrigeNumero($mD069['D069_Valor_Preco_Original'], 5);
            $mD069['D024_Nome_Empresa']                     = substr($mD069['D024_Nome_Empresa'],0,20);
            $mD069['D069_Valor_Custo_Unitario']             = gCorrigeNumero($mD069['D069_Valor_Custo_Unitario']);
            $mD069['D069_Aliquota_IPI']                     = gCorrigeNumero($mD069['D069_Aliquota_IPI']);
            $mD069['D069_Aliquota_ICMS']                    = gCorrigeNumero($mD069['D069_Aliquota_ICMS']);
            $mD069['D069_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mD069['D069_Valor_ICMS_Substituicao_Retencao'], 5);
            $mD069['D069_Valor_Frete']                      = gCorrigeNumero($mD069['D069_Valor_Frete'], 5);
            $mD069['D069_ST_VA_Ajustada']                   = gCorrigeNumero($mD069['D069_ST_VA_Ajustada']);
            $observacaoItem='<font></font>';
            if ($corLinha=="background:white;"){
                $corLinha='background:#D1EEFC;';
            } else {
                $corLinha="background:white;";
            }
            $html .= "<tr>";
            $html .=<<<EOT
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    Tabela: {$mD069['D069_D070_Id']} - {$mD069['D070_Data']} - {$mD069['D024_Nome_Empresa']}
                </td>
                <td align="center" class="tdBorda" style='background-color: #C9E1FC;padding: 5px 5px 5px 5px'>
                    <b>0,00</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #C9E1FC;padding: 5px 5px 5px 5px'>
                    <b>{$mD069['D069_Valor_Preco_Original']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px'>
                    {$D069_Valor_Preco_Original_}
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                    {$mD069['D069_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="right" class="tdBorda" style="background-color: #C9E1FC; padding: 5px 5px 5px 5px">
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; padding: 5px 5px 5px 5px">
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D069_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D069_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D069_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D069_Valor_ICMS_Substituicao_Retencao']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mD069['D069_Valor_Frete']}
                </td>
                <td align="left" style='{$corLinha}padding: 5px 5px 5px 5px' class="tdBordaR" colspan=30>
                     <b>{$mD069['D069_Observacao_Compra']}</b>
                </td>
            </tr>
EOT;
        }


        $count = $count + 1;
    }

    $html .= <<<EOT
        </table>
                
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding:0px 0px 0px 0px;border-top:1px solid black;">

EOT;

        /**
        * PDF - TOTAIS
        */
        $totalOC = $mT218['T218_Valor_Total'];
        $mT218['T218_Valor_Total_Produtos']  = gCorrigeNumero($mT218['T218_Valor_Total_Produtos']);
        $mT218['T218_Valor_Frete']           = gCorrigeNumero($mT218['T218_Valor_Frete']);
        $mT218['T218_Valor_Total_IPI']  = gCorrigeNumero($mT218['T218_Valor_Total_IPI']);
        $mT218['T218_Valor_Total_ICMS'] = gCorrigeNumero($mT218['T218_Valor_Total_ICMS']);
        $mT218['T218_Valor_Total']      = gCorrigeNumero($mT218['T218_Valor_Total']);
        $mT218['T218_Data_Entrega']     = gCorrigeData($mT218['T218_Data_Entrega']);
        $mT218['T218_Valor_ST'] = gCorrigeNumero($mT218['T218_Valor_ST']);

        $html .= <<<EOT
            <table class="tabela" cellspacing="0px" cellpadding="4px">
                <tr>
                    <td align="left" width="105">Forma pagamento: </td><td align="left" width="330"><b>{$mT218['T218_Prazos_Pagamentos']}</b></td>
                    <td align="left" width="105">Valor produtos: </td><td  align="left" width="250px"><b>R$ {$mT218['T218_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;
        switch($mT218['T218_Flag_Frete']){
            case '0':
                $T218_Frete = "Por conta do emitente";
                break;
            case '1':
                $T218_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T218_Frete = "Terceiros";
                break;
            case '9':
                $T218_Frete = "Sem frete";
                break;
            default:
                $T218_Frete = "Não informado";
                break;
        }
        
        $html .= <<<EOT
                <tr>
                    <td align="left" width="105">Frete: </td><td align="left"><b>{$T218_Frete}</b></td>
                    <td align="left" width="105">Valor IPI: </td><td  align="left"><b>R$ {$mT218['T218_Valor_Total_IPI']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105">Transportadora: </td><td align="left"><b>{$mT218['D022_Nome_Empresa']}</b></td>
                    <td align="left" width="105">Valor frete: </td><td  align="left"><b>R$ {$mT218['T218_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105">Prazo de Entrega: </td><td align="left" width="330"><b>{$mT218['T218_Data_Entrega']}</b></td>
                    <td align="left" width="105">Valor ST: </td><td align="left"><b>R$ {$mT218['T218_Valor_ST']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330px"></td>
                    <td align="left" width="105">Valor total: </td><td align="left" style="font-size:18px"><b>R$ {$mT218['T218_Valor_Total']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left"></td>
                    <td align="left" width="105">Valor ICMS: </td><td align="left"><b>R$ {$mT218['T218_Valor_Total_ICMS']}</b></td>
                </tr>
            
EOT;

        $html .= "</table>";

        $html .= <<<EOT
                </td>
            </tr>

            <tr>
                <td colspan="3" style='border-bottom:1px solid black;border-top:1px solid black;'>
EOT;

        $html .= '<br>'.str_replace(chr(10),'<br>','<b>'.$mT218['T218_Observacao']).'</b><br><br>';

        echo $html;
    break;

    case 'demonstrativoPrecoVenda':
        global $g;
        $sql = "SELECT T219_Demonstrativo_Preco, T219_Valor_Preco_Venda FROM T219 WHERE T219_Id = '{$r_acaoId}'";
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        $preco = gCorrigeNumero($row['T219_Valor_Preco_Venda'],5);
        $demonstrativo = unserialize($row['T219_Demonstrativo_Preco']);
        echo montarDemonstrativo($demonstrativo);
    break;

}
function montarDemonstrativo($demonstrativo){
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
            $html .= <<<EOT
                <div class="cabDemonstrativo" style="width: 100%; float: left; font-size:11px; font-weight: bold; {$campo['css']}" onclick="{$campo['onclick']}">
                    {$campo['valor']}
                </div>
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