<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {


     case 'ven015TotalPDV':
		global $g;

        require_once("bibliotecas/classes/VEN003.php");
        $VEN003 = new VEN003();

        $T007_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;

        $cSql_T008=mysql_query("select T008_Id from T008 where T008_T007_Id='$T007_Id'");
        while ($mT008=mysql_fetch_array($cSql_T008)) {
              mysql_query("call T008_Gravar_Totalizacao_4('{$mT008['T008_Id']}')");
        }

        mysql_query("call T007_Gravar_Totalizacao_4($T007_Id);");
        $T007 = "SELECT *, T007_Prazos(T007_Id) as Prazo, 
                           SUM(T008_Valor_Lucro) as Lucro,
                           sum(T008_Quantidade) as quantidadeTotal,
                           sum(D001_Cubagem_Unitaria*T008_Quantidade) as cubagemTotal
                      FROM T007
                LEFT JOIN D024 ON D024_Id = T007_D024_Id
                 LEFT JOIN T008 ON T008_T007_Id = T007_Id
                 LEFT JOIN D009 ON D009_Id = T008_D009_Id
                 LEFT JOIN D049 ON D049_Id = D009_D049_Id
                 LEFT JOIN D001 ON D001_Id = D049_D001_Id
                     WHERE T007_Id='{$T007_Id}'
                     GROUP BY T007_Id";
        $T007 = mysql_query($T007);
        $mT007= mysql_fetch_array($T007);

        $mT007['T007_Valor_Frete']+=$mT007['T007_Valor_Despesas_Acessorias'];
        $mT007['T007_Valor_Frete']=gCorrigeNumero($mT007['T007_Valor_Frete']);
        $obsComercial    = substr($mT007['D024_Observacao_Comercial'], 0, 140);
        $obsFinanceiro   = substr($mT007['D024_Observacao_Financeiro'], 0, 180);
        $valorLimite     = gCorrigeNumero($mT007['D024_Valor_Limite']);
        
        $lucro = gCorrigeNumero(($mT007['Lucro']/$mT007['T007_Valor_Total_Produtos'])*100);
        $mT007['Lucro'] = gCorrigeNumero($mT007['Lucro']);
        $margem = gCorrigeNumero((($mT007['T007_Valor_Total_Produtos']-$mT007['T007_Valor_Total_Custo'])/$mT007['T007_Valor_Total_Produtos'])*100);
        $valorDescontoTabela = $mT007['T007_Valor_Total_Produtos_Sem_Desconto'] - $mT007['T007_Valor_Total_Produtos'];
        $percentualDesconto = gCorrigeNumero(($valorDescontoTabela/$mT007['T007_Valor_Total_Produtos_Sem_Desconto'])*100);
        $percentualDesconto = $VEN003->ven003ModObservacaoPercentualDesconto($percentualDesconto, $mT007);
        $ipv = gCorrigeNumero($mT007['T007_Valor_Total_Produtos']/$mT007['T007_Valor_Total_Custo'],4);
        $mT007['T007_Valor_Total_Custo'] = gCorrigeNumero($mT007['T007_Valor_Total_Custo']);
        $mT007['T007_Valor_Desconto'] = gCorrigeNumero($mT007['T007_Valor_Desconto']);
        //$mT007['T007_Valor_Total_Produtos_Sem_Desconto'] = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Sem_Desconto']);
        $mT007['T007_Valor_Total_Produtos'] = gCorrigeNumero($mT007['T007_Valor_Total_Produtos']);
        $mT007['T007_Valor_Total_ICMS'] = gCorrigeNumero($mT007['T007_Valor_Total_ICMS']);
        $mT007['T007_Valor_Total_ICMS_Credito'] = gCorrigeNumero($mT007['T007_Valor_Total_ICMS_Credito']);
        $mT007['T007_Valor_Total_IPI'] = gCorrigeNumero($mT007['T007_Valor_Total_IPI']);
        $mT007['T007_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT007['T007_Valor_ICMS_Substituicao_Retencao']);
        $mT007['T007_Valor_Total'] = gCorrigeNumero($mT007['T007_Valor_Total']);
        $mT007['T007_IPV'] = gCorrigeNumero($mT007['T007_IPV'],4);
        $mT007['T007_Percentual_Margem'] = gCorrigeNumero($mT007['T007_Percentual_Margem'],2);
        $mT007['T007_Valor_Total_PIS'] = gCorrigeNumero($mT007['T007_Valor_Total_PIS']);
        $mT007['T007_Valor_Total_COFINS'] = gCorrigeNumero($mT007['T007_Valor_Total_COFINS']);
        $mT007['T007_Valor_Total_IRPJ'] = gCorrigeNumero($mT007['T007_Valor_Total_IRPJ']);
        $mT007['T007_Valor_Total_CSLL'] = gCorrigeNumero($mT007['T007_Valor_Total_CSLL']);
        $valorDescontoTabela = gCorrigeNumero($valorDescontoTabela);
        $mT007['quantidadeTotal'] = gCorrigeNumero($mT007['quantidadeTotal']);
        $mT007['cubagemTotal'] = gCorrigeNumero($mT007['cubagemTotal']);
        $mT007['T007_Valor_Frete_Calculado'] = gCorrigeNumero($mT007['T007_Valor_Frete_Calculado']);
        $mT007['pesoLiquido'] = gCorrigeNumero($mT007['T007_Peso_Liquido']);
        $mT007['pesoBruto'] = gCorrigeNumero($mT007['T007_Peso_Bruto']);

        require_once('prog/crm/crm001/grid_func-callback.php');
        if($mT007['T007_Flag_ACP'] == '6' || $mT007['T007_Flag_ACP'] == '8') {
            $doc = ($mT007['T007_Flag_ACP'] == '6') ? 'NF-e' : 'NFC-e';
            if($mT007['T007_Flag_Cancelada'] == 'S'){
                $status = "$doc cancelada";
            } else if($mT007['T007_Flag_Almoxarifado'] == 'N' || empty($mT007['T007_Numero_Nota_Fiscal']) || $mT007['T007_Flag_Travar_Nota'] == 'N'){
                $status = "Aguard. finalizar $doc";
            } else if($mT007['T007_Flag_Almoxarifado'] == 'S' && $mT007['T007_TX_Flag_Finalizada'] != '1'){
                $status = "Aguard. transmitir $doc";
            } else {
                $status = "$doc transmitida";
            }
        }
        $status = "<div style='font-weight:bold;border-radius:5px;border-bottom:1px solid #F4F4F4;background:blue;color:white; padding: 1px;margin-top: 14px;height: 16px;font-size: 13px;' align=center>{$status}</div>";

        $mT007['T007_Valor_Total_Produtos_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Confirmado']);
        $mT007['T007_Valor_Total_ICMS_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_ICMS_Confirmado']);
        $mT007['T007_Valor_Total_IPI_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_IPI_Confirmado']);
        $mT007['T007_Valor_ST_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_ST_Confirmado']);
        $mT007['T007_Valor_Total_Confirmado'] = gCorrigeNumero($mT007['T007_Valor_Total_Confirmado']);


        $mT007['T007_Percentual_Comissao'] = gCorrigeNumero($mT007['T007_Percentual_Comissao']);
        $mT007['T007_Valor_Total_Comissao'] = gCorrigeNumero($mT007['T007_Valor_Total_Comissao']);

        //Calcula Parcelar em aberto do cliente
        $sqlSaldo = "SELECT ifnull(sum(if(T002_Data_Vencimento<curdate(),T002_Valor_Total,0)),0) as Vencido, 
                            ifnull(sum(if(T002_Data_Vencimento>=curdate(),T002_Valor_Total,0)),0) as Vencer
                        FROM T002
                        WHERE T002_D024_Id='{$mT007['D024_Id']}'
                        and T002_Data_Recebimento='0000-00-00' 
                        and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
                        ";
        $resultSaldo = mysql_query($sqlSaldo);
        $linhaSaldo= mysql_fetch_array($resultSaldo);
        
        $sqlPedidos="select ifnull(sum(T005_Valor_Total),0) as Pedidos
                                from T005
                           left join T007 on T007_T005_Id=T005_Id
                               where T005_D024_Id='{$linhaD024['D024_Id']}'
                                 and T005_Flag_Almoxarifado='S'
                                 and T007_Id is null";
        $retornoPedidos = mysql_query($sqlPedidos);
        $linhaPedidos = mysql_fetch_array($retornoPedidos);
/*         $linhaD024['D024_Valor_Limite'] = (!empty($linhaD024['D024_Valor_Limite']) ? $linhaD024['D024_Valor_Limite'] : '0.00');
        $saldoAtual=gCorrigeNumero($linhaD024['D024_Valor_Limite']-($linhaSaldo['Vencido']+$linhaSaldo['Vencer']+$linhaPedidos['Pedidos'])); */


        $aVencer = gCorrigeNumero($linhaSaldo['Vencer']);
        $vencido = gCorrigeNumero($linhaSaldo['Vencido']);
        $valorCreditoAntecipado = gCorrigeNumero($mT007['D024_Valor_Credito_Antecipado']);
        $valorCreditoTroca = gCorrigeNumero($mT007['D024_Valor_Credito_Troca']);
        //$valorOrcamento = $VEN001->calculaTotalGeral($T007_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];

        $valorPrecoTabela = gCorrigeNumero($mT007['T007_Valor_Total_Produtos_Sem_Desconto']);
        
        echo <<<EOT
            <table style='font-size:13px;border-left:5px;float:left; margin: 3px 5px 0 5px; height: 120px; width: 195px;' cellspacing="0" cellpadding="0" onclick="{$onclick}">
              <tr >
                <td style='border:0px;font-size:14px'>
                    Tabela
                </td>
                <td style='font-weight:normal;border:0px;font-size:14px' align='right'>
                    R$ {$valorPrecoTabela}
                </td>
              </tr>
EOT;
        if($valorDescontoTabela<0 && $g['C031']['mostrarDescontoProdutosAcrescimo']=="S"){     
            $percentualDesconto="0,00";
            $valorDescontoTabela = "0,00";
        }
            echo <<<EOT
              <tr>
                <td style='border:0px;font-size:14px'>
                    Desconto
                </td>
                <td style='font-weight:normal;border:0px;font-size:14px' align='right'>
                   - ($percentualDesconto%) R$ {$valorDescontoTabela}
                </td>
              </tr>
              <tr>
                <td style='font-weight:normal;border:0px;font-size:14px'>
                    Sub-total
                </td>
                <td style='font-weight:normal;font-size:14px;border:0px' align='right'>
                    R$ {$mT007['T007_Valor_Total_Produtos']}
                </td>
              </tr>
              <tr>
                <td style='border:0px;font-size:14px'>
                    Desconto
                </td>
                <td style='font-weight:normal;border:0px;font-size:14px' align='right'>
                    - R$ {$mT007['T007_Valor_Desconto']}
                </td>
              </tr>
              <tr>
                <td style='font-weight:normal;border:0px;font-size:18px'>
                    Total
                </td>
                <td style='font-weight:bold;font-size:20px;border:0px' align='right'>
                    R$ {$mT007['T007_Valor_Total']}
                </td>
              </tr>
            </table>
EOT;

        if($mT007['D024_Id'] != $g['C031']['codigoClienteCupomFiscal']){
            echo <<<EOT
            <table style='font-size:13px;border: 1px solid black;border-color:silver;border-radius:5px; float:left; margin: 3px 5px 0 100px; height: 120px; width: 400px;' cellspacing="0" cellpadding="0" onclick="{$onclick}">
                <tr>
                <td style='border:0px;font-size:13px;text-align: left;'>
                    <b>Observação Financeiro:</b><br><span style="font-size:12px; color: var(--BGVermelho);">{$obsFinanceiro}</span>
                </td>
            </tr>
            <tr>
                <td style='border:0px;font-size:13px'>
                    <b>Crédito Antecipado:</b> <span style="color: var(--BGVermelho);">{$valorCreditoAntecipado}</span> <b>Crédito Troca:</b> <span style="color: var(--BGVermelho);">{$valorCreditoTroca}</span><br>
                    <b>Em aberto:</b> <span style="color: var(--BGVermelho);">{$aVencer}</span> <b>Vencido:</b> <span style="color: var(--BGVermelho);">{$vencido}</span><br>

                </td>
            </tr>
            </table>
EOT;
        }


        // Logo cliente na tela
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $logo = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $logo = "<img src=\"{$logo}\">";
        } else {
            $logo = "";
        }

        echo <<<EOT
            <div id='divSpacePDV' style='border:0px; float:center;display: inline-block;width:50px'>
                  
            </div>

            <div id='divLogoPDV' style='border: 0px; float:center;display: inline-block;'>
                 <!--$logo-->
            </div>
EOT;

    break;

    case 'ven015AbrirNovaVenda':
        global $g;

        // Logo cliente na tela
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $logo = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $logo = "<img src=\"{$logo}\">";
        } else {
            $logo = "";
        }

        echo <<<EOT
            <table width='99%' height='90%'>
                <tr>
                    <td align='center' valign='center'>
                        $logo
                    </td>
                </tr>
                <tr>
                    <td syle='height:50%;' align='center' valign='center'>
                        <font style='font-size:60px; font-weight: bold;'>
                            Próximo cliente
                        </font>
                    </td>
                </tr>
                <tr>
                    <td align='center' valign='center'>
                        <button type="button" id="ven003IncluirNotaFiscalConsumidor">F1 - Nova venda</button>
                    </td>
                </tr>
EOT;
        if($g['C031']['abrirGavetaManual'] == 'S' || $g['C031']['habitaOpcoesDiversasPDV'] == 'S'){
            echo <<<EOT
                <tr>
                    <td align='center' valign='center'>
EOT;
            if($g['C031']['abrirGavetaManual'] == 'S'){
                    echo <<<EOT
                        <button type="button" id="ven015AbrirGaveta">Ctrl+F11 - Abrir gaveta</button>
EOT;    
            }
            if($g['C031']['habitaOpcoesDiversasPDV'] == 'S'){
                    echo <<<EOT
                        <button type="button" id="ven015Diversos">Ctrl+F10 - Diversos</button>
EOT;
            }
            echo <<<EOT
                    </td>
                </tr>
EOT;
        }

        echo <<<EOT
            </table>
EOT;

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		

                $('#ven003IncluirNotaFiscalConsumidor').focus();

                $('#ven003IncluirNotaFiscalConsumidor').unbind('click').bind('click', function(data) {
                    abreVenda(data);
                });

                shortcut.remove("F1");
                shortcut.add("F1",function(data) {
                    abreVenda(data);
                    return false;
                });

                $('#ven015AbrirGaveta').bind('click', function(data) {
                    ven015AbrirGaveta(data);
                    return false;
                });
                shortcut.remove("CTRL+F11");
                shortcut.add("CTRL+F11",function(data) {
                    ven015AbrirGaveta(data);
                    return false;
                });

                $('#ven015Diversos').bind('click', function(data) {
                    abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Diversos', '/ven/ven015/content/ven015ContentDiversos/', '&tab=geral&divRefresh={$r_divRefresh}', [800,300]);
                });
        
                shortcut.remove("CTRL+F10");
                shortcut.add("CTRL+F10",function(data) {
                    abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Diversos', '/ven/ven015/content/ven015ContentDiversos/', '&tab=geral&divRefresh={$r_divRefresh}', [800,300]);
                    return false;
                });

                function abreVenda(data) {
                    //document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/ven/ven015/grid_func-ajax/ven015incluirNotaFiscalPDV/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        if (request.code) { 
                            divRefresh('{$r_divRefresh}', false);
                        } else {
                            dialogConfirm('Erro', request.data, false);
                        }
                    });
                }

                function ven015AbrirGaveta(data) {
                    var \$buttons = {
                        "Sim": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/ven/ven015/form_func-ajax/verificaAbrirGaveta/?ajax=true&callback=?', function(request) {
                                if (request.code) {
                                    // Exemplo: abrirJanela
                                    $('#{$g['divId']}').showLoading();
                                    $.ajax({
                                        url: '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/',
                                        type: 'GET',
                                        dataType: 'jsonp',
                                        data: {
                                            'ajax': 'true',
                                            'acaoId': 'da681c8d16233fa8bdc9a645a506841b',
                                            'link': '/sistema/personalizacao/tela/da681c8d16233fa8bdc9a645a506841b/?ajax=true&C007_Id=' + request.C007_Id,
                                        },
                                        success: function(resposta) {
                                            $('#{$g['divId']}').hideLoading();
                                            window.open(resposta.data, '_blank');
                                        }
                                    });
                                } else {
                                    var \$buttons = { 
                                        "Fechar": function() { 
                                                $(this).dialog("destroy").remove(); 
                                                setTimeout(function() { 
                                                }, 150); 
                                        }, 
                                        "Pedir autorização": function() { 
                                                $(this).dialog("destroy").remove(); 
                                                abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Autorização', '/sistema/funcoes/autoriza/contentAbrirGaveta/', '&divForm={$g['divId']}', [300,220]);
                                        } 
                                    };
                                    dialogConfirm('Erro', request.data, \$buttons);									
                                }
                                $('#{$g['divId']}').hideLoading();
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Abrir gaveta', 'Você tem certeza que deseja abrir gaveta? ', \$buttons);
                }
   
            } </script></div>
EOT;

        // Pesquisa última nota fiscal não transmitida do usuário corrente
        $T007Enviar = mysql_query("select T007_Id 
                                     from T007 
                                    where T007_C007_Id_Vendedor_Interno='{$g['usuarioAtual']}'
                                      and T007_Flag_Travar_Nota = 'S'
                                      and (T007_Flag_ACP=6 OR T007_Flag_ACP=8)
                                      and T007_C004_Id='1' 
                                      and T007_Flag_Expedicao != '2' 
                                      and T007_TX_Flag_Finalizada != 1 
                                      and T007_Protocolo_Inutilizacao is null 
                                      and T007_Numero_Protocolo_Nfe is null 
                                order by T007_Id desc 
                                    limit 1");

        $mT007Enviar = mysql_fetch_assoc($T007Enviar);

        if ($mT007Enviar['T007_Id']>0) {
            echo <<<EOT
                <table width='99%'>
                    <tr>
                        <td align='center' valign='center'>
                            <button type="button" id="ven003TransmitirNota">F4-Envio última NFC-e</button>
                        </td>
                    </tr>
                </table>

                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
                    $('#ven003TransmitirNota').bind('click', function(data) {
                        abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven015/content/ven015ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$mT007Enviar['T007_Id']}"), [800,300]);
                    });

                    shortcut.remove("F4");
                    shortcut.add("F4",function(data) {
                        abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Transmitir nota fiscal', '/ven/ven015/content/ven015ContentTransmitirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent("{$mT007Enviar['T007_Id']}"), [800,300]);
                        return false;
                    });
    
                } </script></div>
EOT;
        }

    break;

    case 'ven015TrocoPagamentoPDV':
        $sqlT007 = mysql_query("SELECT T007_Valor_Total FROM T007 WHERE T007_Id = '{$r_acaoId}'");
        $rowT007 = mysql_fetch_assoc($sqlT007);

        $sqlT292 = mysql_query("SELECT SUM(T292_Valor_Recebido) as Valor_Recebido FROM T292 WHERE T292_T007_Id = '{$r_acaoId}'");
        $rowT292 = mysql_fetch_assoc($sqlT292);

        if ($rowT007['T007_Valor_Total']>$rowT292['Valor_Recebido']) {
            $troco = $rowT007['T007_Valor_Total']-$rowT292['Valor_Recebido'];
            $labelDiferenca = 'Falta';
            $labelCor = 'red';
        } else {
            $troco = $rowT292['Valor_Recebido']-$rowT007['T007_Valor_Total'];
            $labelDiferenca = 'Troco';
            $labelCor = 'blue';
        }

        $troco = gCorrigeNumero($troco);
        $rowT292['Valor_Recebido'] = gCorrigeNumero($rowT292['Valor_Recebido']);
        $rowT007['T007_Valor_Total'] = gCorrigeNumero($rowT007['T007_Valor_Total']);
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right; font-size: 14px;">Total R$ <input id="trocoTotal" type="text" style="font-size: 16px; height: 28px; font-weight:bold; margin-right: 5px;" value="{$rowT007['T007_Valor_Total']}" class="whiteBg" id="" size="6" readonly></span>
                <span class="item" style="text-align:right; font-size: 14px;">Recebido  R$ <input id="trocoRecebido" type="text" style="font-size: 16px; height: 28px; font-weight:bold; margin-right: 5px;" value="{$rowT292['Valor_Recebido']}"   class="whiteBg" id="" size="6" readonly></span>
                <span class="item" style="text-align:right; font-size: 14px;">$labelDiferenca R$ <input  id="trocoTroco" type="text" style="font-size: 16px; height: 28px; font-weight:bold; color:$labelCor;" value="{$troco}"     class="whiteBg" id="" size="6" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;
    break;

    /**
     * Cabeçalho Orçamento
     * Chamado em: ven001TabEditarOrcamento
     */
    case 'ven015CabecalhoTransmitirNota':
        global $g;

        $T007 = mysql_query("SELECT T007_Numero_Nota_Fiscal,
                                    T007_Data_Emissao,
                                    D024_Id,
                                    D024_Nome_Empresa
                               from T007
                          left join D024 on T007_D024_Id=D024_Id
                              where T007_Id='{$r_acaoId}'");
        $mT007 = mysql_fetch_assoc($T007);

        $mT007['T007_Data_Emissao'] = gCorrigeData($mT007['T007_Data_Emissao']);

        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all" width="100%">
                    <tr class="linhaDesc">
                        <td align="center":center>NFC-e</td>
                        <td align="center">Código</td>
                        <td>Cliente</td>
                        <td align="center">Data</td>
                        <td rowspan="2" valign="center" style='padding:1px 10px 4px 2px;' align="center"><button type="submit" id="ven003NFe">Acessar NF-e</button></td>
                    </tr>
                    <tr style="font-size:1.1em; font-weight:bold;">
                        <td style='padding:1px  1px 4px 2px;' align="center">{$mT007['T007_Numero_Nota_Fiscal']}</td>
                        <td style='padding:1px 10px 4px 2px;' align="center">{$mT007['D024_Id']}</td>
                        <td style='padding:1px 10px 4px 2px;'>{$mT007['D024_Nome_Empresa']}</td>
                        <td style='padding:1px 10px 4px 2px;' align="center">{$mT007['T007_Data_Emissao']}</td>
                    </tr>
                </table>
            </div>
EOT;

echo <<<EOT
        
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#ven003NFe').bind('click', function(data) {
                abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent("{$r_acaoId}") + '&tabela=' + encodeURIComponent("{$from}"), [1000,550]);
            });
        }</script></div>

EOT;

break;

case 'ven015CabecalhoNotaPDV':
    global $g;

    $T007 = mysql_query("SELECT T007_Id,
                                T007_Numero_Nota_Fiscal,
                                T007_Data_Emissao,
                                D024_Id,
                                D024_Nome_Empresa,
                                C007_Primeiro_Nome
                           FROM T007
                      LEFT JOIN D024 ON T007_D024_Id=D024_Id
                      LEFT JOIN C007 ON T007_C007_Id_Vendedor_Interno = C007_Id
                          WHERE T007_Id='{$r_acaoId}'");
    $mT007 = mysql_fetch_assoc($T007);

    $mT007['T007_Data_Emissao'] = gCorrigeData($mT007['T007_Data_Emissao']);

    echo <<<EOT
        <div class="contentCabecalhoLayout">
            <table class="tabela" width="100%">
                <tr class="linhaDesc"> 
                    <td align="center">ID</td>
                    <td align="center">NFC-e</td>
                    <td align="center">Código</td>
                    <td>Cliente</td>
                    <td align="center">Data</td>
                    <td align="left">Vendedor</td>
                </tr>
                <tr style="font-size:1.1em; font-weight:bold;">
                    <td style='padding:1px  1px 4px 2px;' align="center">{$mT007['T007_Id']}</td>
                    <td style='padding:1px  1px 4px 2px;' align="center">{$mT007['T007_Numero_Nota_Fiscal']}</td>
                    <td style='padding:1px 10px 4px 2px;' align="center">{$mT007['D024_Id']}</td>
                    <td style='padding:1px 10px 4px 2px;'>{$mT007['D024_Nome_Empresa']}</td>
                    <td style='padding:1px 10px 4px 2px;' align="center">{$mT007['T007_Data_Emissao']}</td>
                    <td style='padding:1px 10px 4px 2px;' align="left">{$mT007['C007_Primeiro_Nome']}</td>
                </tr>
            </table>
        </div>
EOT;

    break;


}
