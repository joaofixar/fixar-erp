<?php
namespace hardness;
require_once("old/lib/funcoes.php");
/**
 * Ações
 */
switch($g['r'][3]){
	case 'erp001InfoCliente':
		
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$sqlD024 = "SELECT * FROM D024 LEFT JOIN D020 on D020_Id=D024_D020_Id 
									   LEFT JOIN D018 on D018_Id=D024_D018_Id 
									   LEFT JOIN C007 on C007_Id=D024_C007_Vendedor_Interno 
									   WHERE D024_Id='{$D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$linhaD024= mysql_fetch_array($resultD024);
		if(!empty($linhaD024['D024_Cnpj'])){
			$cnpjCpf = (!empty($linhaD024['D024_Cnpj'])) ? "<span style=\"font-weight:normal; font-size:11px;\">CNPJ</span><br />{$linhaD024['D024_Cnpj']}" : "<span style=\"font-weight:normal; font-size:11px;\">CNPJ</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
		} else {
			$cnpjCpf = (!empty($linhaD024['D024_Cpf'])) ? "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br />{$linhaD024['D024_Cpf']}" : "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
		}
		$linhaD024['D024_Nome_Contato'] = substr($linhaD024['D024_Nome_Contato'], 0, 15);

		$D024_IE 			= (!empty($linhaD024['D024_Inscricao_Estadual'])) ? $linhaD024['D024_Inscricao_Estadual'] : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_Email 		= (!empty($linhaD024['D024_E_Mail'])) 			  ? substr($linhaD024['D024_E_Mail'], 0, 30)  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_DDD			= (!empty($linhaD024['D024_DDD_Telefone_1'])) 	  ? $linhaD024['D024_DDD_Telefone_1'] 	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$D024_Telefone		= (!empty($linhaD024['D024_Telefone_1']))   	  ? $linhaD024['D024_Telefone_1']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$C007_Primeiro_Nome = (!empty($linhaD024['C007_Primeiro_Nome']))   	  ? $linhaD024['C007_Primeiro_Nome']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$Contato			= (!empty($linhaD024['D024_Nome_Contato']))   	  ? $linhaD024['D024_Nome_Contato']   	  : "<span style='color: var(--BGVermelho);'>FALTA</span>"; 
		$DDD_FAX			= (!empty($linhaD024['D024_DDD_Fax']))   	  	  ? $linhaD024['D024_DDD_Fax']   	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		$FAX				= (!empty($linhaD024['D024_Fax']))   	  	  	  ? $linhaD024['D024_Fax']   	  	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		
		$nomeEmpresa = substr($linhaD024['D024_Nome_Empresa'], 0, 17);
		$nomeFantasia = substr($linhaD024['D024_Nome_Fantasia'], 0, 18);
		$endereco = substr($linhaD024['D024_Endereco'], 0, 12);

		$retorno = <<<EOT
		<style type="text/css">
		.containerTop{
			width: 99%;
			height: 103px;
			float:left;
			margin-left: 5px;
			border-radius: 5px;
		}
		.containerTop .infoCli table{
			width: 100%;
			height: 100px;
			float: left;
		}
		</style>
		<div class="containerTop">
			<div class="infoCli">
				<table>
					<tr>
EOT;
					if(!empty($linhaD024['D024_Cnpj'])){
						$retorno .= <<<EOT
						<td>
							<table>
								<tr class="dadosEmpresa">
									<td style="cursor: pointer;"><span style="font-weight:normal; font-size:11px;">Razão Social</span><br/>$nomeEmpresa</td>
								</tr>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">Nome Fantasia</span><br />$nomeFantasia</td>
								</tr>
								<tr>
									<td style="border-bottom: none;">{$cnpjCpf}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">I.E.</span><br />{$D024_IE}</td>
								</tr>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">Endereço</span><br />$endereco - Nº{$linhaD024['D024_Numero']}</td>
								</tr>
								<tr>
									<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">Bairro</span><br />{$linhaD024['D024_Bairro']}</td>
								</tr>
							</table>
						</td>
EOT;
					} else {
						$retorno .= <<<EOT
						<td width="40%">
							<table>
								<tr class="dadosEmpresa">
									<td style="cursor: pointer;" colspan="2"><span style="font-weight:normal; font-size:11px;">Nome</span><br/>{$linhaD024['D024_Nome_Fantasia']}</td>
								</tr>
								<tr>
									<td colspan="2"><span style="font-weight:normal; font-size:11px;">Endereço</span><br />{$linhaD024['D024_Endereco']} - Nº{$linhaD024['D024_Numero']}</td>
								</tr>
								<tr>
									<td style="border-bottom: none;">{$cnpjCpf}</td>
									<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">Bairro</span><br />{$linhaD024['D024_Bairro']}</td>
								</tr>
							</table>
						</td>
EOT;
					}
					$retorno .= <<<EOT
						<td>
							<table>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">Cidade/UF</span><br />{$linhaD024['D020_Nome_Cidade']}/{$linhaD024['D018_UF']}</td>
								</tr>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">DDD - Telefone</span><br />{$D024_DDD}-{$D024_Telefone}</td>
								</tr>
								<tr>
									<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">E-Mail</span><br />{$D024_Email}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">Contato</span><br />{$Contato}</td>
								</tr>
								<tr>
									<td><span style="font-weight:normal; font-size:11px;">Vendedor</span><br />{$C007_Primeiro_Nome}</td>
								</tr>
								<tr>
									<td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">DDD-FAX</span><br />{$DDD_FAX}-{$FAX}</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
EOT;
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('.dadosEmpresa').unbind('click').bind('click', function(data) {
				var id = '{$r_D024_Id}';
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Cliente - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id), [960,583]);
			});
		} </script></div>
EOT;
			echo $retorno;
	break;
	
	case 'erp001DadosFinanceiro':
		$xLast12 = date('Y-m-d');
		$xLast12 = new \DateTime($xLast12);
		$xLast12->modify('-12 months');
		$Data_Inicial = $xLast12->format('Y-m-d');
	
		$D024_Id = $r_D024_Id;
		$dadosCliente = mysql_query("SELECT D024_Data_Cadastro, D024_Valor_Limite FROM D024 WHERE D024_Id = '{$D024_Id}' LIMIT 1");
		$dadosCliente = mysql_fetch_array($dadosCliente);

		//Maior Compra
		$maiorCompra = mysql_query("SELECT T007_Valor_Total, T007_Data_Emissao 
										FROM T007
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicial}'
										AND T007_Flag_Cancelada='N'
										ORDER BY T007_Valor_Total DESC,
										T007_Data_Emissao DESC
										LIMIT 1");
		$maiorCompra = mysql_fetch_array($maiorCompra);
		//Ultima Compra
		$ultimaCompra = mysql_query("SELECT T007_Data_Emissao
										FROM T007
										WHERE T007_D024_Id = '{$D024_Id}'
										AND T007_Data_Emissao >= '{$Data_Inicial}'
										AND T007_Flag_Cancelada!='S'
										AND T007_Valor_Total>0
										ORDER BY T007_Data_Emissao DESC
										LIMIT 1");
		$ultimaCompra = mysql_fetch_array($ultimaCompra);
        
        $mesesComprados = mysql_query("SELECT T007_Id
                                        FROM T007
                                        LEFT JOIN D006 ON D006_Id=T007_D006_Id
                                        WHERE T007_D024_Id = '{$D024_Id}'
                                        AND T007_Flag_Cancelada!='S'
                                        AND D006_Flag_Entrada_Saida='S'
                                        AND D006_Flag_Venda_Compra_Outros='V'
                                        AND T007_Valor_Total>0
                                        GROUP BY MONTH(T007_Data_Emissao)
                                        LIMIT 12");
        $mesesComprados = mysql_num_rows($mesesComprados);
        
        $mediaVendasUlt12Meses = mysql_query("SELECT (SUM(T007_Valor_Total)/if({$mesesComprados}>12,12,{$mesesComprados})) as mediaVendas
                                        FROM T007
                                        LEFT JOIN D006 ON D006_Id=T007_D006_Id
                                        WHERE T007_D024_Id = '{$D024_Id}'
                                        AND T007_Data_Emissao >= '{$Data_Inicial}'
                                        AND T007_Flag_Cancelada!='S'
                                        AND D006_Flag_Entrada_Saida='S'
                                        AND D006_Flag_Venda_Compra_Outros='V'
                                        AND T007_Valor_Total>0");
        $mediaVendasUlt12Meses = mysql_fetch_array($mediaVendasUlt12Meses);
                                        
		//Maior Atraso
		$maiorAtraso = mysql_query("SELECT DATEDIFF(T002_Data_Recebimento,T002_Data_Vencimento)
										FROM T002
										WHERE T002_Data_Recebimento>T002_Data_Vencimento
										AND T002_D024_Id = '{$D024_Id}'
										AND T002_Data_Emissao >= '{$Data_Inicial}'
										AND T002_Flag_Cancelada='N'
										ORDER BY DATEDIFF(T002_Data_Recebimento,T002_Data_Vencimento) DESC 
										LIMIT 1");
		$maiorAtraso = mysql_fetch_array($maiorAtraso);
		//Total a pagar
		$totalPagar = mysql_query("SELECT SUM(T002_Valor_Saldo)
									FROM T002
									WHERE T002_Data_Recebimento='0000-00-00'
									AND T002_Data_Emissao >= '{$Data_Inicial}'
									AND T002_D024_Id = '{$D024_Id}'
									AND T002_Flag_Cancelada='N'");
		$totalPagar = mysql_fetch_array($totalPagar);
		//Total Atrasado
		$totalAtrasado = mysql_query("SELECT SUM(T002_Valor_Saldo)
										FROM T002
										WHERE CURRENT_DATE()>T002_Data_Vencimento
										AND T002_Data_Recebimento='0000-00-00'
										AND T002_Data_Emissao >= '{$Data_Inicial}'
										AND T002_D024_Id = '{$D024_Id}'
										AND T002_Flag_Cancelada='N'");
		$totalAtrasado = mysql_fetch_array($totalAtrasado);
		
		$saldo = mysql_query("SELECT SUM(if(T002_Data_Vencimento>=curdate(),T002_Valor_Saldo,0)) as Vencer,
									 SUM(if(T002_Data_Vencimento<curdate(),T002_Valor_Saldo,0)) as Vencido
									 FROM T002 
									 LEFT JOIN D024 ON D024_Id = T002_D024_Id
									 WHERE D024_Id = '{$D024_Id}' AND T002_Data_Recebimento='0000-00-00'
									 AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)");
		$saldo = mysql_fetch_array($saldo);
		
		$pedidosAbertos = mysql_query("select SUM(T005_Valor_Total)
											FROM T005
											LEFT JOIN T007 on T007_T005_Id=T005_Id
											WHERE T005_D024_Id='{$D024_Id}'
											AND T005_Flag_Almoxarifado='S'
											AND T007_Id is null");
		$pedidosAbertos = mysql_fetch_array($pedidosAbertos);
		
		$vencido = $saldo['Vencido'];
		$vencer = $saldo['Vencer'];
		
		$dataCadastro = gCorrigeData($dadosCliente['D024_Data_Cadastro']);
		$dataCadastro = substr($dataCadastro, 0, 10);
		//$dadosCliente['D024_Valor_Limite'] = gCorrigeNumero($dadosCliente['D024_Valor_Limite']);
		$maiorCompra = gCorrigeNumero($maiorCompra[0]);
		
		if(empty($ultimaCompra)){
			$ultimaCompra = '-';
		} else {
			$ultimaCompra = gCorrigeData($ultimaCompra[0]);
		}
		$totalPagar = gCorrigeNumero($totalPagar[0]);
		$totalAtrasado = gCorrigeNumero($totalAtrasado[0]);
        $mediaVendasUlt12Meses = gCorrigeNumero($mediaVendasUlt12Meses['mediaVendas']);
		
		$valorLimite = $dadosCliente['D024_Valor_Limite'];
		
		$retorno = $valorLimite-($vencido+$vencer+$pedidosAbertos[0]);
		
		if ($retorno<0) {
			$saldoLimite = '<span style="color: var(--BGVermelho);">'.number_format($retorno,2,',','.').'</span>';
		} else {
			$saldoLimite = number_format($retorno,2,',','.');
		}
		
		$valorLimite = gCorrigeNumero($valorLimite);

		$retorno = <<<EOT
		<style type="text/css">
		.containerFinanceiro {
			width: 99%;
			height: 103px;
			float:left;
			margin-left: 5px;
			border-radius: 5px;
		}
		
		.containerFinanceiro table {
			align: center;
			width: 48.7%;
			height: 100%;
			float: left;
			margin-left: 5px;
			padding-right: 5px;
		}
		</style>
			<div class="containerFinanceiro">
				<table class="tableFinanceiro">
					<tr>
						<td>Data de Cadastro</td>
						<td>{$dataCadastro}</td>
					</tr>
					<tr>
						<td>Maior Compra</td>
						<td>R$ {$maiorCompra}</td>
					</tr>
					<tr>
						<td>Ultima Compra</td>
						<td>{$ultimaCompra}</td>
					</tr>
					<tr>
						<td>Maior Atraso</td>
						<td>{$maiorAtraso[0]} dias</td>
					</tr>
                    <tr>
                        <td style="border-bottom: none;">Meses comprados</td>
                        <td style="border-bottom: none;">{$mesesComprados}</td>
                    </tr>
				</table>
				<table>
					<tr>
						<td>Total à Pagar</td>
						<td>R$ {$totalPagar}</td>
					</tr>
					<tr>
						<td>Total Atrasado</td>
						<td>R$ {$totalAtrasado}</td>
					</tr>
					<tr>
						<td>Valor Limite</td>
						<td>R$ {$valorLimite}</td>
					</tr>
					<tr>
						<td>Saldo Limite</td>
						<td>R$ $saldoLimite</td>
					</tr>
                    <tr>
                        <td style="border-bottom: none;">Média Vendas Mensal</td>
                        <td style="border-bottom: none;">{$mediaVendasUlt12Meses}</td>
                    </tr>
				</table>
			</div>		
EOT;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('.containerFinanceiro').unbind('click').bind('click', function(data) {
					nova_janela('/hardness3/hardness/clientes/referencias_comerciais.php?D024_Id=13&Abre_Janela=Nao&Voltar=N',0,0,800,600,'no','no','no','no','yes','yes',0)});
							
		} </script></div>
EOT;
			echo $retorno;
	break;
	
	case 'erp001Observacoes':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$sqlD024 = "SELECT * FROM D024 LEFT JOIN D020 on D020_Id=D024_D020_Id 
									   LEFT JOIN D018 on D018_Id=D024_D018_Id 
									   LEFT JOIN C007 on C007_Id=D024_C007_Vendedor_Interno 
									   WHERE D024_Id='{$D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$linhaD024= mysql_fetch_array($resultD024);
		
		$obsComercial = substr($linhaD024['D024_Observacao_Comercial'], 0, 120);
		$obsFinanceiro = substr($linhaD024['D024_Observacao_Financeiro'], 0, 120);
		echo <<<EOT
		<style type="text/css">
		.observacoes{
			width: 99%;
			height: 103px;
			float:left;
			margin-left:8px;
			border-radius:5px;
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
				$obsComercial
			</div>
			<div class="financeiro">
				<b>Financeiro:</b><br />
				<span style="color: var(--BGVermelho);">$obsFinanceiro</span>
			</div>
		</div>
EOT;
	break;
	case 'erp001Totais':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;

		
		//<td>Orçamentos</td>
		$retorno = <<<EOT
		<div class="tabela ui-state-default ui-corner-all" style="height:85px;">
			<table style="height:99.9%; width:100%;margin:0 auto; width:99.9%; font-size:12px; width:10%; float:left; margin-left:20px;">
EOT;
		//<td>Pedidos</td>
		$retorno .=<<<EOT
				<tr>
					<td><span style='font-size:11px; font-weight:normal;'>Total Pedidos</span><br /><input type="text" value="{$linhaT005TotalPedidos['totalPedidos']}" disabled='disabled' style='background:#fff;' /></td>
					<td><span style='font-size:11px; font-weight:normal;'>Valor Total Pedidos</span><br /><input type="text" value="{$linhaT005ValorTotalPedidos['ValorTotalPedidos']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Abertos</span><br /><input type="text" value="{$linhaT005TotalAberto['totalAberto']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Valor Total Abertos</span><br /><input type="text" value="{$linhaT005ValorTotalAberto['ValorTotalAbertos']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Faturados</span><br /><input type="text" value="{$linhaT005TotalFaturados['totalFaturado']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Valor Total Faturados</span><br /><input type="text" value="{$linhaT005ValorTotalFaturados['ValorTotalFaturados']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Cancelados</span><br /><input type="text" value="{$linhaT005TotalCancelados['totalCancelado']}" disabled='disabled' style='background:#fff;'/></td>
					<td><span style='font-size:11px; font-weight:normal;'>Valor Total Cancelados</span><br /><input type="text" value="{$linhaT005ValorTotalCancelados['valorTotalCancelados']}" disabled='disabled' style='background:#fff;'/></td>
				</tr>
			</table>
		</div>
EOT;
		echo $retorno;
	break;
    
    case 'erp001ImagemOriginal':
        $urlImagem = (isset($r_urlImagem)) ? $r_urlImagem : false;
        $nomeLinha = (isset($r_nomeLinha)) ? $r_nomeLinha : false;
        $urlImagem = urldecode($urlImagem);
        echo <<<EOT
        <div>
            <img src="{$urlImagem}" />
        </div>
EOT;
    break;
}
?>
