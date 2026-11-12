<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Orçamento
	 * Chamado em: est001TabEditarOrcamento
	 */
	case 'est016CabecalhoOrcamento':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T224
		left join D024 on T224_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D024_D018_Id=D018_Id
		left join C007 on C007_Id=D024_C007_Vendedor_Interno
		left join D030 on D024_D030_Id=D030_Id
		WHERE
			(T224_Id='$r_acaoId')
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
			$possuiRestricoes = "<font style='color: var(--BGVermelho);'><b>C/RESTRIÇÕES</b></font>";
		}else{
			$possuiRestricoes = "<font style='color: var(--BGVerde);'><b>LIBERADO</b></font>";
		}
		$Contribuinte="<font class='BGVermelho'><b>&nbsp;INFORMAR!&nbsp;</b></font>";
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
		}	
		
		$dataEmissao = gCorrigeData($row['T224_Data_Emissao']);
		if($g['C031']['campoNomeEmpresa'] == 'fantasia'){
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Fantasia'], 35);
        } else {
            $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 35);
        }
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 15);
		
		switch($row['T224_Flag_Lista_Preco']){
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
				$listaPreco="<font style='color: var(--BGVermelho);'><b>FALTA</b></font>";
		}
		if($row['D024_Id'] == '<span style="color: var(--BGVermelho);">FALTA</span>'){
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
						<td width='70px'>Nº</td>
						<td width='30px'>Código</td>
						<td width='400px'>Cliente</td>
						<td width='150px'>Cidade</td>
						<td width='30px'>UF</td>
						<td width='70px'>Emissão</td>
						<td width='50px'>Tipo</td>
						<td width='50px'>Financeiro</td>
						<td width='75px'>Lista</td>
                        <td width='75px'>CRT</td>
					</tr>
					<tr class="linhaVal" style="cursor:pointer;" onclick="{$abrirJanela}">
						<td align='center'>{$row['T224_Id']}</td>
						<td>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$Contribuinte}</td>
						<td>{$possuiRestricoes}</td>
						<td>{$listaPreco}</td>
                        <td>{$Crt}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
	
	case 'est016observacoes':
		global $g;
        session_write_close();
        
        $T224_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;
        mysql_query("call T224_Gravar_Totalizacao_3($T224_Id);");
        $sqlD024 = "SELECT *,                       
                        analista.C007_Primeiro_Nome as analistaC007_Primeiro_Nome,
                        T224_Nome_Status as status
                         FROM T224
                        LEFT JOIN D024 ON D024_Id = T224_D024_Id
                        LEFT JOIN T224A ON T224_Id = T224A_T224_Id
                        LEFT JOIN C007 AS analista ON analista.C007_Id=T224_Liberou_Alcada_C007_Id
                        WHERE T224_Id='{$T224_Id}'";
        $resultD024 = mysql_query($sqlD024);
        $linhaD024= mysql_fetch_array($resultD024);

        $obsComercial = substr($linhaD024['D024_Observacao_Comercial'], 0, 120);
        $obsFinanceiro = substr($linhaD024['D024_Observacao_Financeiro'], 0, 120);
        $valorLimite = gCorrigeNumero($linhaD024['D024_Valor_Limite']);
        $linhaD024['T224_Valor_Total']                      = gCorrigeNumero($linhaD024['T224_Valor_Total']);
        $totalICMS                                          = gCorrigeNumero($linhaD024['T224_Valor_Total_ICMS'] + $linhaD024['T224A_Valor_FCP']);
        $linhaD024['T224_Valor_Total_ICMS']                 = gCorrigeNumero($linhaD024['T224_Valor_Total_ICMS']);
        $linhaD024['T224A_Valor_FCP']                       = gCorrigeNumero($linhaD024['T224A_Valor_FCP']);
        $linhaD024['T224_Valor_Total_IPI']                  = gCorrigeNumero($linhaD024['T224_Valor_Total_IPI']);
        $totalST                                            = gCorrigeNumero($linhaD024['T224_Valor_ICMS_Substituicao_Retencao'] + $linhaD024['T224A_Valor_FCP_ST']);
        $linhaD024['T224_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($linhaD024['T224_Valor_ICMS_Substituicao_Retencao']);
        $linhaD024['T224A_Valor_FCP_ST']                    = gCorrigeNumero($linhaD024['T224A_Valor_FCP_ST']);
        $linhaD024['T224_Valor_Total_Produtos']             = gCorrigeNumero($linhaD024['T224_Valor_Total_Produtos']);
        $linhaD024['T224_Valor_Frete']                      = gCorrigeNumero($linhaD024['T224_Valor_Frete']);
        $linhaD024['T224_Valor_Desconto_Manual']            = gCorrigeNumero($linhaD024['T224_Valor_Desconto_Manual']);

        if ($linhaD024['T224_Flag_Status']==1) {
            $corAutorizacao = 'blue';
        } else if ($linhaD024['T224_Flag_Status']==3) {
            $corAutorizacao = 'green';
        } else if ($linhaD024['T224_Flag_Status']==2) {
            $corAutorizacao = 'red';
        }
        
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
        
        $T225 = "SELECT sum(T225_Valor_Preco_Venda*T225_Quantidade) as totalVenda,
                        sum(T225_Valor_Preco_Venda_Negociado*T225_Quantidade) as totalVendaNegociado 
                        FROM T225
                        WHERE T225_T224_Id='{$T224_Id}'
                          AND T225_Valor_Preco_Venda_Negociado>0";
        $T225 = mysql_query($T225);
        $mT225 = mysql_fetch_array($T225);

        $mT225['totalVenda']=($mT225['totalVenda']*0.96); // Aplicando 4% de desconto
        
        $totalSobra      = gCorrigeNumero($mT225['totalVendaNegociado']-$mT225['totalVenda']);
        $percentualSobra = ($totalSobra > 0 ) ? gCorrigeNumero(($totalSobra/$mT225['totalVenda'])*100) : '0.00';
        $linhaD024['T224_Liberou_Alcada_Data'] = gCorrigeData($linhaD024['T224_Liberou_Alcada_Data']);
        $linhaD024['analistaC007_Primeiro_Nome'] = substr($linhaD024['analistaC007_Primeiro_Nome'], 0, 8);

		//$valorOrcamento = $EST015->calculaTotalGeral($T224_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        if($exibirLimites == 'S'){
            $observacoesLimites = <<<EOT
                <div class="limite">
                    <b>Faturamento mínimo:</b>
                    <span style="color: var(--BGVermelho);">{$valorLimite}</span>
                </div>
EOT;
        }
        echo <<<EOT
            <style type="text/css">
            .observacoesLimites{
                width: 330px;
                height: 200px;
                float:left;
                margin: 10px 0px 10px 8px;
                border-radius:5px;
                border-color:silver;
            }
            .observacoesLimites .comercial{
                height:80px;
                padding:1px;
            }
            .observacoesLimites .financeiro{
                height: 80px;
                padding:1px;
                border-top:none;
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
            <div style="font-size: 13px; float: left; height: 215px; width: 230px; margin: 5px 25px 0 25px;">
                <table style="width: 240px; background: none;">
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            <b>Produtos</b>
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:15px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            <b> R$ {$linhaD024['T224_Valor_Total_Produtos']}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            IPI 
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            R$ {$linhaD024['T224_Valor_Total_IPI']}
                        </td>
                    </tr>
                    <tr title="ST: &nbsp; R$ {$linhaD024['T224_Valor_ICMS_Substituicao_Retencao']}\nFCP ST: &nbsp; R$ {$linhaD024['T224A_Valor_FCP_ST']}">
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            <u>ST+FCP ST</u>
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            R$ $totalST
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            Frete
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            R$ {$linhaD024['T224_Valor_Frete']}
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            Desconto
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            - R$ {$linhaD024['T224_Valor_Desconto_Manual']}
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            <b>Total</b>
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:15px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            <b> R$ {$linhaD024['T224_Valor_Total']}</b>
                        </td>
                    </tr>
                    <tr title="ICMS: &nbsp; R$ {$linhaD024['T224_Valor_Total_ICMS']}\nFCP: &nbsp; R$ {$linhaD024['T224A_Valor_FCP']}">
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            <u>ICMS+FCP</u> 
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            R$ {$totalICMS}
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            Sobra 
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            R$ {$totalSobra} (-4%) 
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            Perc. sobra 
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            {$percentualSobra}%
                        </td>
                    </tr>
                    <tr>
                        <td class='observacaoTotaisTd' style="border-bottom:1px solid #F4F4F4;">
                            Status 
                        </td>
                        <td class='observacaoTotaisTd' style="font-size:13px; margin: 10px 0px 0px 8px; text-align: right; border-bottom:1px solid #F4F4F4;">
                            <b>{$linhaD024['status']}</b>
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
                <div class="saldo">
                    <b>Autorização:</b>
                    <span style="color: $corAutorizacao;">{$linhaD024['analistaC007_Primeiro_Nome']} - {$linhaD024['T224_Liberou_Alcada_Data']}</span>
                </div>
            </div>            
EOT;
	break;
		global $g;
        session_write_close();
        
        $T224_Id = isset($r_acaoId) ? $r_acaoId : false;
        $divAjuste = isset($r_divAjuste) ? $r_divAjuste : false;
        mysql_query("call T224_Gravar_Totalizacao_3($T224_Id);");
        $sqlD024 = "SELECT * FROM T224
                        LEFT JOIN D024 ON D024_Id = T224_D024_Id
                        WHERE T224_Id='{$T224_Id}'";
        $resultD024 = mysql_query($sqlD024);
        $linhaD024= mysql_fetch_array($resultD024);

        $obsComercial = substr($linhaD024['D024_Observacao_Comercial'], 0, 120);
        $obsFinanceiro = substr($linhaD024['D024_Observacao_Financeiro'], 0, 120);
        $valorLimite = gCorrigeNumero($linhaD024['D024_Valor_Limite']);
        $linhaD024['T224_Valor_Total'] = gCorrigeNumero($linhaD024['T224_Valor_Total']);
        $linhaD024['T224_Valor_Total_ICMS'] = gCorrigeNumero($linhaD024['T224_Valor_Total_ICMS']);
        $linhaD024['T224_Valor_Total_IPI'] = gCorrigeNumero($linhaD024['T224_Valor_Total_IPI']);
        $linhaD024['T224_Valor_ST'] = gCorrigeNumero($linhaD024['T224_Valor_ST']);
        $linhaD024['T224_Valor_Total_Produtos'] = gCorrigeNumero($linhaD024['T224_Valor_Total_Produtos']);
        
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
        $saldoAtual=gCorrigeNumero($linhaD024['D024_Valor_Limite']-($linhaSaldo['vencer']+$linhaSaldo['vencer']+$retornoPedidos['Pedidos']));
        
		//$valorOrcamento = $EST015->calculaTotalGeral($T224_Id);
        $exibirLimites = $g['C031']['exibirLimitesOrcamentoPedido'];
        if($exibirLimites == 'N'){
            echo <<<EOT
		    <style type="text/css">
		    .observacoes{
			    width: 95%;
			    height: 123px;
			    float:left;
			    margin: 20px 0px 10px 8px;
			    border-radius:5px;
			    border-color:silver;
		    }
		    .observacoes .comercial{
			    height:49%;
			    padding:1px;
		    }
		    .observacoes .financeiro{
			    height: 49%;
			    padding:1px;
			    border-top:none;
		    }
		    </style>
		    <div class="observacoes">
			    <div class="comercial">
				    <b>Comercial:</b><br />
				    <span style="color: var(--BGVermelho);">$obsComercial</span>
			    </div>
			    <div class="financeiro">
				    <b>Financeiro:</b><br />
				    <span style="color: var(--BGVermelho);">$obsFinanceiro</span>
			    </div>
		    </div>
            <table>
              <tr>
                <td>
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'><b>Valor total Produtos: R$ </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'><b>Valor total ICMS: R$ </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'><b>Valor total IPI: R$ </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'><b>Valor total ST: R$ </font></span> 
                </td>
                <td>
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;">{$linhaD024['T224_Valor_Total_Produtos']}</b></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;">{$linhaD024['T224_Valor_Total_ICMS']}</b></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;">{$linhaD024['T224_Valor_Total_IPI']}</b></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;">{$linhaD024['T224_Valor_ST']}</b></span> 
                </td>
                <td>
                    <span style="font-size: 18px; margin: 10px 0px 0px 8px;"><font style='font-size:14px'><b>Valor total: R$ </font> {$linhaD024['T224_Valor_Total']}</b> </span>
                </td>
              </tr>
            </table>
			
EOT;
		}else{
            echo <<<EOT
            <style type="text/css">
            .observacoesLimites{
                width: 95%;
                height: 123px;
                float:left;
                margin: 10px 0px 10px 8px;
                border-radius:5px;
                border-color:silver;
            }
            .observacoesLimites .comercial{
                height:37%;
                padding:1px;
            }
            .observacoesLimites .financeiro{
                height: 37%;
                padding:1px;
                border-top:none;
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
            <div class="observacoes observacoesLimites">
                <div class="comercial">
                    <b>Comercial:</b><br />
                    <span style="color: var(--BGVermelho);">$obsComercial</span>
                </div>
                <div class="financeiro">
                    <b>Financeiro:</b><br />
                    <span style="color: var(--BGVermelho);">{$obsFinanceiro}</span>
                </div>
                <div class="limite">
                    <b>Limite:</b>
                    <span style="color: var(--BGVermelho);">{$valorLimite}</span>
                </div>
                <div class="saldo">
                    <b>Saldo disponível:</b>
                    <span style="color: var(--BGVermelho);">{$saldoAtual}</span><br />
                </div>
            </div>
            <table>
              <tr>
                <td>
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'>Valor total Produtos: </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'>Valor total ICMS: </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'>Valor total IPI: </font></span> <br />
                    <span style="font-size: 12px; margin: 10px 0px 0px 8px;"><font style='font-size:12px'>Valor total ST: </font></span> 
                </td>
                <td>
                    <span style="font-size: 11px; margin: 10px 0px 0px 8px;">R$ <font style='font-size:13px'><b>{$linhaD024['T224_Valor_Total_Produtos']}</b></span> <br />
                    <span style="font-size: 11px; margin: 10px 0px 0px 8px;">R$ <font style='font-size:13px'><b>{$linhaD024['T224_Valor_Total_ICMS']}</b></span> <br />
                    <span style="font-size: 11px; margin: 10px 0px 0px 8px;">R$ <font style='font-size:13px'><b>{$linhaD024['T224_Valor_Total_IPI']}</b></span> <br />
                    <span style="font-size: 11px; margin: 10px 0px 0px 8px;">R$ <font style='font-size:13px'><b>{$linhaD024['T224_Valor_ST']}</b></span> 
                </td>
                <td>
                    <span style="font-size: 18px; margin: 10px 0px 0px 8px;"><font style='font-size:14px'>Valor total: R$ </font><font style='size:18px'></font><b>{$linhaD024['T224_Valor_Total']}</b> </span>
                </td>
              </tr>
            </table>
            
EOT;
        }
	break;

    case 'est016AnaliseOC':
        global $g, $confUsuario;
        
        /**
        * SELECT's
        */
        $C004 = mysql_query("SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id = '{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);

        $T224_Id = mysql_real_escape_string($r_T224_Id);

        if(isset($r_coleta) && $r_coleta == 'S'){
            mysql_query("UPDATE T224 SET T224_Flag_Coleta = 'S' WHERE T224_Id = '{$T224_Id}'");
        }

        $sqlT224 = mysql_query("SELECT T224_Flag_Status FROM T224 WHERE T224_Id = '{$T224_Id}'");
        $T224 = mysql_fetch_array($sqlT224);
        if($T224['T224_Flag_Status'] < '3' || $T224['T224_Flag_Status'] == ''){
            require_once('bibliotecas/classes/EST016.php');
            $EST016 = new EST016();
            $EST016->aplicarAlcadaOC($T224_Id);
        }
    
        $T224 =  <<<EOT
                SELECT 
                    T224_Id,
                    T224_Data_Emissao,
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
                    analista.C007_Primeiro_Nome as analistaC007_Primeiro_Nome,
                    T224_Nome_Status as status,
                    T224_Valor_Total,
                    T224_Valor_Total_Produtos,
                    T224_Valor_Frete,
                    T224_Valor_Total_IPI,
                    T224_Valor_Total_ICMS,
                    T224_Data_Entrega,
                    T224_Valor_ICMS_Substituicao_Retencao,
                    T224_Flag_Frete,
                    D022_Nome_Empresa,
                    T224_Liberou_Alcada_Data,
                    T224_Motivo_Pedido_Liberacao,
                    T224_Prazos_Pagamentos,
                    T224_Flag_Alcada,
                    T224_Valor_Desconto_Manual
                FROM T224 
                LEFT JOIN D013 ON D013_Id=T224_D013_Id 
                LEFT JOIN D116 ON D116_Id=T224_D116_Id 
                LEFT JOIN D133 ON D133_Id=D116_D133_Id 
                LEFT JOIN D134 ON D134_Id=D116_D134_Id 
                LEFT JOIN D024 ON D024_Id=T224_D024_Id 
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id 
                LEFT JOIN D022 ON D022_Id=T224_D022_Id
                LEFT JOIN C007 AS comprador ON comprador.C007_Id=T224_C007_Id_Vendedor_Interno
                LEFT JOIN C007 AS analista ON analista.C007_Id=T224_Liberou_Alcada_C007_Id
                WHERE T224_Id='{$T224_Id}';
EOT;

        $T224 = mysql_query($T224);
        $mT224 = mysql_fetch_array($T224);

        $T225 = <<<EOT
                SELECT 
                    D001_Id,
                    T225_Codigo_Produto,
                    T225_Descricao_Produto,
                    T225_Quantidade,
                    T225_Valor_Preco_Sem_Desconto_Unitario,
                    T225_Valor_Total_Preco,
                    T225_Valor_Custo_Unitario,
                    T225_IPV,
                    T225_Valor_Preco_Venda,
                    T225_Valor_Preco_Venda_Negociado,
                    T225_Observacao_Entrega,
                    D037_Unidade,
                    D005_Classificacao_Fiscal,
                    D006_Codigo_CFOP,
                    T225_Aliquota_IPI,
                    T225_Aliquota_ICMS,
                    T225_ST_VA_Ajustada,
                    T225_Valor_ICMS_Substituicao,
                    T225_Valor_Frete_Unitario,
                    D009_Quantidade_Estoque,
                    D009_Quantidade_Comprar_Calculada,
                    D009_Quantidade_Consumo ,
                    D009_Quantidade_Minima_Estoque,
                    D009_Quantidade_Maxima_Estoque,
                    D009_Frequencia_Venda,
                    D009_Clientes_Consumo,
                    D009_Frequencia_Venda_Meses,
                    T224_Id,
                    T224_Data_Emissao,
                    D024_Nome_Empresa,
                    T225_D009_Id,
                    datediff(curdate(),T001_Data_Lancamento) as Dias FROM T225 
                LEFT JOIN D009 ON D009_Id=T225_D009_Id 
                LEFT JOIN T224 ON T224_Id=T225_T224_Id
                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                LEFT JOIN D003 ON D003_Id=D001_D003_Id
                LEFT JOIN T144 ON T144_D001_Id=D001_Id
                LEFT JOIN D037 ON D037_Id=T225_D037_Id
                LEFT JOIN D005 ON D005_Id=T225_D005_Id
                LEFT JOIN D006 ON D006_Id=T225_D006_Id
                LEFT JOIN T001 on T001_Id=D009_T001_Id
                LEFT JOIN D024 ON D024_Id=T001_D024_Id
                WHERE T225_T224_Id = '{$T224_Id}' 
                  AND ifnull(T225_Flag_Cancelada,'')!='S'
                GROUP BY T225_Id
                ORDER BY D009_Id ASC, IF('{$T224_Id}'=T224_Id,1,2) ASC
EOT;
        $T225 = mysql_query($T225); 
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
            }           
            .moldura td {
                background:white;
                padding:1px 1px 1px 1px;
                color: #000;
            }           
            .tabela {
                cellspacing:0px;
                font-size:12px;
                padding: 2px 2px 2px 2px;
            }           
            .tdBorda {
                border-right:1px solid silver;
                border-bottom:1px dotted silver;
                background:white;
                color: #000;
                font-size:12px;
            }           
            .tdBordaR {
                font-size:12px;
                background:white;
                border-bottom:1px dotted silver;
                color: #000;
            }           
            .tdBordaL {
                border-right:1px solid silver;
                border-bottom:1px dotted silver;
                background:white;
                font-size:12px;
                color: #000;
            }           
            .tdTitulo {
                border-right:1px solid silver;
                font-size:12px;
                background:lightblue;
                color: #000;
            }           
            .tdTituloR {
                font-size:12px;
                background:lightblue;
                color: #000;
            }           
            .tdTituloL {
                border-right:1px solid silver;
                font-size:12px;
                background:lightblue;
                color: #000;
            }           
            table {
                font-family: Arial;
                color: #000;
                background: #fff;
            }  
        </style>
EOT;

        /**
        * PDF - HEADER
*/
// (TODO) passar para o arq. conf
$caminhoImagem = str_replace('201.22.57.137:8282', 'eurosul', $caminhoImagem);
$caminhoImagem = str_replace('201.22.57.137:8181', 'sigma', $caminhoImagem);

$mT224['T224_Data_Emissao'] = gCorrigeData($mT224['T224_Data_Emissao']);
$T224_Numero_Ordem_Compra = str_pad($T224_Id, 6, "0", STR_PAD_LEFT);

$html .= <<<EOT
<body bgcolor="white">
<table class="moldura">            
    <tr>
        <td colspan="3" style="background:white;font-size:18px;font-weight:bold;padding:5px 5px 5px 5px;border-bottom:1px solid Black" align="center">
            ANÁLISE DA ORDEM DE COMPRA
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table class="tabela" style="width:1000px">
                <tr>
                    <td width="30%">
                        <table class="tabela" width="100%">
                            <tr>
                                <td><b>{$mT224['D024_Nome_Empresa']}</b></td>
                            </tr>
                            <tr>
                                <td>{$mT224['D024_Endereco']}, {$mT224['D024_Numero']} - {$mT224['D024_Bairro']}</td>
                            </tr>
                            <tr>
                                <td><b>CEP:</b> {$mT224['D024_Cep']} {$mT224['D020_Nome_Cidade']} - {$mT224['D018_UF']}</td>
                            </tr>
                            <tr>
                                <td><b>CNPJ/CPF:</b> {$mT224['D024_Cnpj']}{$mT224['D024_Cpf']}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="30%">
                        <table class="tabela" width="100%">
                            <tr>
                                <td><b>Incr. Estadual:</b> {$mT224['D024_Inscricao_Estadual']}</td>
                            </tr>
                            <tr>
                                <td><b>Contato: </b>{$mT224['D013_Nome_Contato']}</td>
                            </tr>
                            <tr>
                                <td><b>Telefone: </b>{$mT224['D013_Telefone_1']}</td>
                            </tr>
                            <tr>
                                <td><b>E-mail: </b>{$mT224['D013_E_Mail']}</td>
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
                                            <td>{$mT224['compradorC007_Primeiro_Nome']}</td>
                                        </tr>
                                        <tr>
                                            <td>{$mT224['compradorC007_Email']}</td>
                                        </tr>
                                        <tr>
                                            <td>{$mT224['compradorC007_DDD_Telefone']}-{$mT224['compradorC007_Telefone']}</td>
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
                                    <b>Ordem compra<br>Nº {$T224_Numero_Ordem_Compra}<br>{$mT224['T224_Data_Emissao']}<b>
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
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    Produto
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    Quant
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Preço R\$
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Total R\$
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Custo
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    IPV
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Venda
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Venda
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Sobra
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Entrega dias
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    Und
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    NCM
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    CFOP
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    %IPI
                </td>
                <td align="center" style="background:#C9DFEA" class="tdTitulo">
                    %ICM
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    MVA
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    ST R\$
                </td>
                <td align="right" style="background:#C9DFEA" class="tdTitulo">
                    Frete R\$
                </td>
                <td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
                    Estoque
                </td>
                <td align="center" style="background:#C9DFEA;width:60px" class="tdTitulo">
                    Comprar
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Vendido
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Mínimo
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Máximo
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Freq
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Client
                </td>
                <td class="tdTitulo" align="center" style="background:#C9DFEA;">
                    Freq Mes
                </td>
            </tr>
EOT;

        while($mT225 = mysql_fetch_array($T225)){
            $mT225['sobra'] = gCorrigeNumero($mT225['T225_Valor_Preco_Venda_Negociado']-$mT225['T225_Valor_Preco_Venda']);
            if ($mT225['T225_Valor_Preco_Venda_Negociado']<=0){
                $mT225['sobra'] = 0;
            }
            $mT225['T225_Quantidade']      = gCorrigeNumero($mT225['T225_Quantidade']);
            $mT225['T225_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT225['T225_Valor_Preco_Sem_Desconto_Unitario'],5,',','.');
            $mT225['T225_Valor_Total_Preco'] = gCorrigeNumero($mT225['T225_Valor_Total_Preco']);
            $mT225['T225_Aliquota_IPI']    = number_format($mT225['T225_Aliquota_IPI'],0);
            $mT225['T225_Valor_IPI']       = gCorrigeNumero($mT225['T225_Valor_IPI']);
            $mT225['T225_Aliquota_ICMS']   = number_format($mT225['T225_Aliquota_ICMS'],0);
            $mT225['T225_Valor_ICMS']      = gCorrigeNumero($mT225['T225_Valor_ICMS']);
            $mT225['T225_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT225['T225_Valor_ICMS_Substituicao_Retencao']);
            $mT225['T225_ST_VA_Ajustada'] = gCorrigeNumero($mT225['T225_ST_VA_Ajustada']);
            $mT225['T225_Valor_Preco_Venda_Negociado'] = gCorrigeNumero($mT225['T225_Valor_Preco_Venda_Negociado']);
            $mT225['D009_Quantidade_Estoque'] = gCorrigeNumero($mT225['D009_Quantidade_Estoque']);
            $mT225['D009_Quantidade_Consumo'] = gCorrigeNumero($mT225['D009_Quantidade_Consumo']);
            $mT225['D009_Quantidade_Comprar_Calculada'] = gCorrigeNumero($mT225['D009_Quantidade_Comprar_Calculada']);
            $mT225['T001_Quantidade'] = gCorrigeNumero($mT225['T001_Quantidade']);
            $mT225['T001_Valor_Preco_Unitario'] = number_format($mT225['T001_Valor_Preco_Unitario'],5,',','.');
            $mT225['T001_Data_Lancamento'] = gCorrigeData($mT225['T001_Data_Lancamento']);
            $mT225['T225_IPV'] = gCorrigeNumero($mT225['T225_IPV']);
            $mT225['T225_Valor_Preco_Venda'] = gCorrigeNumero($mT225['T225_Valor_Preco_Venda']);
            $mT225['T225_Valor_Custo_Unitario'] = gCorrigeNumero($mT225['T225_Valor_Custo_Unitario']);
            $mT225['T225_Valor_Frete_Unitario'] = gCorrigeNumero($mT225['T225_Valor_Frete_Unitario']);
            $mT225['D009_Quantidade_Minima_Estoque'] = gCorrigeNumero($mT225['D009_Quantidade_Minima_Estoque']);
            $mT225['D009_Quantidade_Maxima_Estoque'] = gCorrigeNumero($mT225['D009_Quantidade_Maxima_Estoque']);
            $observacaoItem='<font></font>';
            if (!empty($mT225['T225_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> '.$mT225['T225_Observacao'].'</font><br>';
            }
            if (!empty($mT225['T225_Codigo_Substituto'])){
                //$mT225['T225_Codigo_Produto']=$mT225['T225_Codigo_Substituto'];
            }
            //$corLinha = ($mT225['T225_T224_Id'] == $T224_Id) ? 'style="background-color: #DDD;"' : '';
            
            $corLinha="background:white;";

            $html .=<<<EOT
               <tr onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique() , '', 'Cadastro Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$mT225['D001_Id']}') + '&tabela=D001', [1000,600]);">
                <td align="left" style="{$corLinha}max-width:400px;min-width:450px;width:450px;border-top:1px solid silver;cursor: pointer;" class="tdBorda" width="300px">
                    <b><u>{$mT225['T225_Codigo_Produto']} - {$mT225['T225_Descricao_Produto']}</u></b>
                </td>
                <td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    <b>{$mT225['T225_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    <b>{$mT225['T225_Valor_Preco_Sem_Desconto_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_Valor_Total_Preco']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['T225_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['T225_IPV']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['T225_Valor_Preco_Venda']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['T225_Valor_Preco_Venda_Negociado']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['sobra']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['T225_Observacao_Entrega']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_Valor_ICMS_Substituicao']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px;border-top:1px solid silver'>
                    {$mT225['T225_Valor_Frete_Unitario']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
                    {$mT225['D009_Quantidade_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;font-weight:bold;border-top:1px solid silver">
                    {$mT225['D009_Quantidade_Comprar_Calculada']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Quantidade_Consumo']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Quantidade_Minima_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Quantidade_Maxima_Estoque']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Frequencia_Venda']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Clientes_Consumo']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; color: #000; padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT225['D009_Frequencia_Venda_Meses']}
                </td>
            </tr>
EOT;

            $T219 = <<<EOT
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
                    WHERE T219_D009_Id = '{$mT225['T225_D009_Id']}' 
                    GROUP BY T219_Id
                    ORDER BY T218_Id desc
EOT;

            $T219 = mysql_query($T219); 
            echo mysql_error();

        while ($mT219=mysql_fetch_array($T219)){
            $mT219['sobra'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda_Negociado']-$mT219['T219_Valor_Preco_Venda']);
             if ($mT219['T219_Valor_Preco_Venda_Negociado']<=0){
                 $mT219['sobra'] = 0;
            }           
            $mT219['T218_Data_Emissao']    = gCorrigeData($mT219['T218_Data_Emissao']);
            $mT219['T219_Quantidade']      = gCorrigeNumero($mT219['T219_Quantidade']);
            $mT219['T219_Valor_Preco_Sem_Desconto_Unitario'] = number_format($mT219['T219_Valor_Preco_Sem_Desconto_Unitario'],5,',','.');
            $mT219['T219_Valor_Total_Preco'] = gCorrigeNumero($mT219['T219_Valor_Total_Preco']);
            $mT219['T219_Aliquota_IPI']    = number_format($mT219['T219_Aliquota_IPI'],0);
            $mT219['T219_Valor_IPI']       = gCorrigeNumero($mT219['T219_Valor_IPI']);
            $mT219['T219_Aliquota_ICMS']   = number_format($mT219['T219_Aliquota_ICMS'],0);
            $mT219['T219_Valor_ICMS']      = gCorrigeNumero($mT219['T219_Valor_ICMS']);
            $mT219['T219_Valor_ST'] = gCorrigeNumero($mT219['T219_Valor_ST']);
            $mT219['T219_ST_VA_Ajustada'] = gCorrigeNumero($mT219['T219_ST_VA_Ajustada']);
            $mT219['T219_IPV'] = gCorrigeNumero($mT219['T219_IPV']);
            $mT219['T219_Valor_Preco_Venda'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda']);
            $mT219['T219_Valor_Custo_Unitario'] = gCorrigeNumero($mT219['T219_Valor_Custo_Unitario']);
            $mT219['T219_Valor_Preco_Venda_Negociado'] = gCorrigeNumero($mT219['T219_Valor_Preco_Venda_Negociado']);
            $mT219['T219_Valor_Frete_Unitario'] = gCorrigeNumero($mT219['T219_Valor_Frete_Unitario']);
            $mT219['D024_Nome_Empresa'] = substr($mT219['D024_Nome_Empresa'],0,20);
            $observacaoItem='<font></font>';
            if (!empty($mT219['T219_Observacao'])) {
                $observacaoItem = '<font style="background:#FCF9AE"><b>Observação:</b> '.$mT219['T219_Observacao'].'</font><br>';
            }
            if (!empty($mT219['T219_Codigo_Substituto'])){
                //$mT219['T219_Codigo_Produto']=$mT219['T219_Codigo_Substituto'];
            }
            if ($corLinha=="background:white;"){
                $corLinha='background:#F4F4F4;';
            } else {
                $corLinha="background:white;";
            }
            $html .= "<tr>";
            $html .=<<<EOT
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    Cotação: {$mT219['T218_Id']} - {$mT219['T218_Data_Emissao']} - {$mT219['D024_Nome_Empresa']}
                </td>
                <td align="center" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
                    <b>{$mT219['T219_Quantidade']}</b>
                </td>
                <td align="right" class="tdBorda" style='background-color: #A1FCA9;padding: 5px 5px 5px 5px'>
                    <b>{$mT219['T219_Valor_Preco_Sem_Desconto_Unitario']}</b>
                </td>
                <td align="right" class="tdBorda" style='padding: 5px 5px 5px 5px'>
                    {$mT219['T219_Valor_Total_Preco']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$mT219['T219_Valor_Custo_Unitario']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$mT219['T219_IPV']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #A1FCA9; padding: 5px 5px 5px 5px">
                    {$mT219['T219_Valor_Preco_Venda']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['T219_Valor_Preco_Venda_Negociado']}
                </td>
                <td align="right" class="tdBorda" style="background-color: #BADCFC;  padding: 5px 5px 5px 5px;border-top:1px solid silver">
                    {$mT219['sobra']}
                </td>
                <td align="center" class="tdBorda" style="background-color: lightyellow; padding: 5px 5px 5px 5px">
                    {$mT219['T219_Observacao_Entrega']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['D037_Unidade']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['D005_Classificacao_Fiscal']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['D006_Codigo_CFOP']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['T219_Aliquota_IPI']}
                </td>
                <td align="center" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['T219_Aliquota_ICMS']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['T219_ST_VA_Ajustada']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['T219_Valor_ST']}
                </td>
                <td align="right" class="tdBorda" style='{$corLinha}padding: 5px 5px 5px 5px'>
                    {$mT219['T219_Valor_Frete_Unitario']}
                </td>
                <td align="left" style='{$corLinha}padding: 5px 5px 5px 5px' class="tdBordaR" colspan=30>
                    Pagamento: {$mT219['T218_Forma_Pagamento']}
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
                LEFT JOIN D006 ON D006_Id=T014_D006_Id
                LEFT JOIN D024 ON D024_Id=T001_D024_Id
                WHERE T001_D009_Id = '{$mT225['T225_D009_Id']}' 
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
                $corLinha='background:#F4F4F4;';
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
                    <b>{$mD069['D069_Observacao_Compra']}</b>
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
            WHERE D069_D009_Id = '{$mT225['T225_D009_Id']}'
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
                $corLinha='background:#F4F4F4;';
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
        $totalOC = $mT224['T224_Valor_Total'];
        $mT224['T224_Valor_Total_Produtos']  = gCorrigeNumero($mT224['T224_Valor_Total_Produtos']);
        $mT224['T224_Valor_Frete']           = gCorrigeNumero($mT224['T224_Valor_Frete']);
        $mT224['T224_Valor_Total_IPI']  = gCorrigeNumero($mT224['T224_Valor_Total_IPI']);
        $mT224['T224_Valor_Total_ICMS'] = gCorrigeNumero($mT224['T224_Valor_Total_ICMS']);
        $mT224['T224_Valor_Total']      = gCorrigeNumero($mT224['T224_Valor_Total']);
        $mT224['T224_Data_Entrega']     = gCorrigeData($mT224['T224_Data_Entrega']);
        $mT224['T224_Valor_ICMS_Substituicao_Retencao'] = gCorrigeNumero($mT224['T224_Valor_ICMS_Substituicao_Retencao']);
        $mT224['T224_Valor_Desconto_Manual'] = gCorrigeNumero($mT224['T224_Valor_Desconto_Manual']);

        $html .= <<<EOT
            <table class="tabela" cellspacing="0px" cellpadding="4px">
                <tr>
                    <td align="left" width="105">Forma pagamento: </td><td align="left" width="330"><b>{$mT224['T224_Prazos_Pagamentos']}</b></td>
                    <td align="left" width="105">Valor produtos: </td><td  align="left" width="250px"><b>R$ {$mT224['T224_Valor_Total_Produtos']}</b></td>
                </tr>
EOT;
        switch($mT224['T224_Flag_Frete']){
            case '0':
                $T224_Frete = "Por conta do emitente";
                break;
            case '1':
                $T224_Frete = "Por conta do destinatário";
                break;
            case '2':
                $T224_Frete = "Terceiros";
                break;
            case '9':
                $T224_Frete = "Sem frete";
                break;
            default:
                $T224_Frete = "Não informado";
                break;
        }
        
        $html .= <<<EOT
                <tr>
                    <td align="left" width="105">Frete: </td><td align="left"><b>{$T224_Frete}</b></td>
                    <td align="left" width="105">Valor IPI: </td><td  align="left"><b>R$ {$mT224['T224_Valor_Total_IPI']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105">Transportadora: </td><td align="left"><b>{$mT224['D022_Nome_Empresa']}</b></td>
                    <td align="left" width="105">Valor frete: </td><td  align="left"><b>R$ {$mT224['T224_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105">Prazo de Entrega: </td><td align="left" width="330"><b>{$mT224['T224_Data_Entrega']}</b></td>
                    <td align="left" width="105">Valor ST: </td><td align="left"><b>R$ {$mT224['T224_Valor_ICMS_Substituicao_Retencao']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330px"></td>
                    <td align="left" width="105">Desconto: </td><td align="left""><b>R$ {$mT224['T224_Valor_Desconto_Manual']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left" width="330px"></td>
                    <td align="left" width="105">Valor total: </td><td align="left" style="font-size:18px"><b>R$ {$mT224['T224_Valor_Total']}</b></td>
                </tr>
                <tr>
                    <td align="left" width="105"></td><td align="left"></td>
                    <td align="left" width="105">Valor ICMS: </td><td align="left"><b>R$ {$mT224['T224_Valor_Total_ICMS']}</b></td>
                </tr>
            
EOT;

        $html .= "</table>";

        $html .= <<<EOT
                </td>
            </tr>

            <tr>
                <td colspan="3" style='border-bottom:1px solid black;border-top:1px solid black;'>
EOT;

        $html .= '<br>'.str_replace(chr(10),'<br>','<b>'.$mT224['T224_Observacao']).'</b><br><br>';

        $mT224['T224_Liberou_Alcada_Data'] = gCorrigeData($mT224['T224_Liberou_Alcada_Data']);
        
        $alcada = false;
        if($g['C007']['C007_Flag_Vendedor'] == 'D'){
            $alcada = true;
        } else {
            if($g['C007']['C007_Flag_Vendedor'] == 'S' && ($mT224['T224_Flag_Alcada'] == 'S' || $mT224['T224_Flag_Alcada'] == 'C')){
                $alcada = true;
            } else if(($g['C007']['C007_Flag_Vendedor'] == 'C' || $g['C007']['C007_Flag_Vendedor'] == 'V') && $mT224['T224_Flag_Alcada'] == 'C'){
                $alcada = true;
            }
        }

        if($g['C031']['controleAlcadasOrdemCompra'] == 'N'){
            $alcada = true;        
        }

        $html .= <<<EOT
                    
                <tr>
                    <td colspan="3" height="100%">

                        <table class="tabela">
                            <tr>
                                <td><b><u>AUTORIZAÇÃO:</u></b></td>
                            </tr>
                            <tr>
                                <td>{$mT224['analistaC007_Primeiro_Nome']}</td>
                            </tr>
                            <tr>
                                <td>{$mT224['T224_Liberou_Alcada_Data']}</td>
                            </tr>
                            <tr>
                                <td>{$mT224['status']}</td>
                            </tr>
                        </table>                    

                    </td>
                </tr>
                </td>
            </tr>
            <tr>
                <td colspan="20" align="center">

                    <table class="tabela">
                        <tr>
                            <td>{$mT224['T224_Motivo_Pedido_Liberacao']}</td>
                        </tr>
                    </table>                    

                </td>
            </tr>
EOT;

        if ($alcada) {

            $html .= <<<EOT
                <tr>
                    <td colspan="3" height="100%">
                        <div style="text-align: center;">
                            <button type="submit" id="est016ReprovarOC" style='color: var(--BGVermelho);font-size:18px;font-weight:bold'>Negar</button>
                            <button type="submit" id="est016AprovarOC"style='color:green;font-size:18px;font-weight:bold'>Aprovar</button>
                        </div>
                    </td>
                </tr>

            </table>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#est016AprovarOC').unbind('click').bind('click', function(data) {
                    var buttons = {
                        "Sim": function() {
                            $.getJSON('/est/est016/grid_func-ajax/est016AprovarPedido/?ajax=true&T224_Id=' + encodeURIComponent('{$T224_Id}') + '&callback=?', function(request) {
                                var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); fecharJanela('{$g['divId']}'); divRefresh('{$g['divIdParent']}') } }
                                dialogConfirm('Análise de Ordem de Compra', request.data, buttons);
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); } 
                    };
                    dialogConfirm('Atenção!!', 'Deseja aprovar a Ordem de Compra {$T224_Id} ?', buttons);
                });

                $('#est016ReprovarOC').unbind('click').bind('click', function(data) {
                    abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/est/est016/content/est016ContentNaoLiberarTelaAnalise/', '&acaoId={$T224_Id}&divReload={$g['divIdParent']}', [400,300]);
                });
            }</script></div>
EOT;
        } else {
            $html .= <<<EOT
                <tr>
                    <td colspan="20" align="center">

                        <table class="tabela">
                            <tr>
                                <td style='font-size:18px;color: var(--BGVermelho)'><br><b>EXIGE MAIOR ALÇADA!</b></td>
                           </tr>
EOT;
            if($mT224['T224_Flag_Status'] == '' || $mT224['T224_Flag_Status'] == '0' || $mT224['T224_Flag_Status'] == '2'){
                $html .= <<<EOT
                           <tr>
                                <td><button type="submit" id="est016SolicitarLiberacao" style='color:black;font-size:18px;font-weight:bold'>Solicitar Liberação</button></td>
                           </tr>
EOT;
            } else if($mT224['T224_Flag_Status'] == '1'){
                $html .= <<<EOT
                           <tr>
                                <td style='font-size:16px;color:black;text-align:center;'><br><b>Aguardando Análise!</b></td>
                           </tr>
EOT;
            }
            $html .= <<<EOT
                        </table>                    
                    </td>
                </tr>
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    $('#est016SolicitarLiberacao').unbind('click').bind('click', function(data) {
                        var buttons = {
                            "Sim": function() {
                                $.getJSON('/est/est016/form_func-ajax/enviarParaAnalise/?ajax=true&T224_Id=' + encodeURIComponent('{$T224_Id}') + '&callback=?', function(request) {
                                    var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); window.close(); } }
                                    dialogConfirm('Análise de Ordem de Compra', request.data, buttons);
                                });
                                $(this).dialog("destroy").remove();
                            },
                            "Não": function() { $(this).dialog("destroy").remove(); } 
                        };
                        dialogConfirm('Atenção!!', 'Deseja enviar ordem de compra para análise ?', buttons);
                    });
                }</script></div>
EOT;

        }
        $html .= <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $(window).unload( function () { opener.recarregarContent(); } );
            }</script></div>
EOT;
        echo $html;
    break;

    case 'est016TotalizadoresDividirOrdemCompra':
        $resultado = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE (D024_Id='{$r_acaoId}')");
        $row = mysql_fetch_assoc($resultado);

        // Registro vazios = FALTA
        foreach ($row as &$row_) {
            if (empty($row_)) {
                $row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
            }
        }

        $D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
        $D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc">
                        <td width='70px'>Código</td>
                        <td width='400px'>Cliente</td>
                        <td width='150px'>Cidade</td>
                        <td width='150px'>Estado</td>
                        <td></td>
                    </tr>
                    <tr class="linhaVal">
                        <td align='center'>{$row['D024_Id']}</td>
                        <td>{$D024_Nome_Empresa}</td>
                        <td>{$D020_Nome_Cidade}</td>
                        <td>{$row['D018_UF']}</td>
                        <td></td>
                    </tr>
                </table>
            </div>
EOT;
        $novoIdTelaForm = uniqid();
        $D024_Id=$r_acaoId;

        $T006 = mysql_query("SELECT T224_Id,
                                    SUM(T225_Quantidade*T225_Valor_Preco_Sem_Desconto_Unitario) as totalProdutos,
                                    SUM(T225_Quantidade_Separacao*T225_Valor_Preco_Sem_Desconto_Unitario) as totalSeparado,
                                    SUM(if(T225_Quantidade_Estoque<T225_Quantidade,T225_Quantidade_Estoque,T225_Quantidade)*T225_Valor_Preco_Sem_Desconto_Unitario) as totalEstoque,
                                    SUM(T225_Quantidade_Dividir*T225_Valor_Preco_Sem_Desconto_Unitario) as totalDividir
                               FROM T225
                          LEFT JOIN T224 on T224_Id=T225_T224_Id
                              WHERE T224_D024_Id='{$D024_Id}' AND T225_Quantidade > 0 AND T224_C004_Id = '{$g['empresaAtual']}'
                                AND (T224_Flag_Status='' OR T224_Flag_Status='0' OR T224_Flag_Status='2' OR T224_Flag_Status='3' OR T224_Flag_Status='12')
                                AND ifnull(T224_Flag_Dividido,'N') != 'S' AND T225_Flag_Cancelada != 'S'
                           ORDER BY T224_Id DESC LIMIT 1");
        $mT006 = mysql_fetch_array($T006);
        echo mysql_error();

        $T224_Id = $mT006['T224_Id'];
        $totalProdutos = gCorrigeNumero($mT006['totalProdutos']);
        $totalSeparado = gCorrigeNumero($mT006['totalSeparado']);
        $totalEstoque = gCorrigeNumero($mT006['totalEstoque']);
        $totalDividir = gCorrigeNumero($mT006['totalDividir']);
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="buttonsBottomTop">
            <button type="button" id="est016DividirOrdemCompraBtn" style="margin:8px;">Dividir</button>
            <button type="button" id="est016DefinirQuantidadeBtn" style="margin:8px;">Preencher quantidade</button>
            <button type="button" id="est016LimparQuantidadeBtn" style="margin:8px;">Limpar quantidade</button>
            <span class="item" style="text-align:right">Produtos<br><input type="text" value="{$totalProdutos}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Disponível<br><input type="text" value="{$totalEstoque}" class="whiteBg" id="" size="11" readonly></span>
            <span class="item" style="text-align:right">Dividir<br><input type="text" value="{$totalDividir}" class="whiteBg" id="" size="11" readonly></span>
            <div style="clear:both;"></div>
        </div>
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#est016DividirOrdemCompraBtn').bind('click', function(data) {
                $.getJSON('/est/est016/grid_func-ajax/est016VerificaItensDividirOrdemCompra/?T224_Id=' + encodeURIComponent('{$T224_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
                    if(request.code){
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione a empresa', '/est/est016/content/est016ContentEmpresaDividirOrdemCompra/', '&T224_Id=' + encodeURIComponent('{$T224_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&divRefresh='+ encodeURIComponent('{$r_divRefresh}') + '&dividirPedidoEmpresa=true', [400,200]);
                    }else{
                        var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                        dialogConfirm('Erro', request.data, \$buttons);
                    }
                });
            });

            $('#est016DefinirQuantidadeBtn').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#' + '{$g['divId']}').showLoading();
                        $.getJSON('/est/est016/grid_func-ajax/est016DefinirQuantidadeOrdemCompra/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
                            if (request.code) {
                                divRefresh('{$r_divRefresh}', true);
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
                dialogConfirm('Definir quantidade', 'Este processo irá definir a quantidade dividida com a quantidade de produtos da ordem de compra.<br> Você tem certeza?', \$buttons);
            });

            $('#est016LimparQuantidadeBtn').bind('click', function(data) {
                var \$buttons = {
                    "Sim": function() {
                        $('#' + '{$g['divId']}').showLoading();
                        $.getJSON('/est/est016/grid_func-ajax/est016LimparQuantidades/?D024_Id=' + encodeURIComponent('{$D024_Id}') + '&sql=' + encodeURIComponent($('#{$g['divId']}').parent().find('.divToScroll:first').attr('gridsql')) + '&ajax=true&callback=?', function(request) {
                            if (request.code) {
                                var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                dialogConfirm('Ok', "Operação realizada com sucesso!", \$buttons);
                                divRefresh('{$r_divRefresh}', true);
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
                dialogConfirm('Atanção!', 'Este processo ira zerar a quantidade á dividir. Você tem certeza?', \$buttons);
            });
        } </script></div>
EOT;

    break;

}
