<?php
namespace hardness;
require_once("old/lib/funcoes.php");
/**
 * Ações
 */
switch($g['r'][3]){
	case 'srm001InfoCliente':
		
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$sqlD024 = "SELECT * FROM D024 LEFT JOIN D020 on D020_Id=D024_D020_Id 
									   LEFT JOIN D018 on D018_Id=D024_D018_Id 
									   LEFT JOIN C007 on C007_Id=D024_C007_Vendedor_Interno 
									   WHERE D024_Id='{$D024_Id}'";
		$resultD024 = mysql_query($sqlD024);
		$linhaD024= mysql_fetch_array($resultD024);
		if(!empty($linhaD024['D024_Cnpj'])){
			$cnpjCpf = (!empty($linhaD024['D024_Cnpj'])) ? "<span style=\"font-weight:normal; font-size:10px;\">CNPJ</span><br /><span style='font-weight:normal; font-size:11px;'>{$linhaD024['D024_Cnpj']}" : "<span style=\"font-weight:normal; font-size:11px;\">CNPJ</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
		} else {
			$cnpjCpf = (!empty($linhaD024['D024_Cpf'])) ? "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br /><span style='font-weight:normal; font-size:11px;'>{$linhaD024['D024_Cpf']}" : "<span style=\"font-weight:normal; font-size:10px;\">CPF</span><br /><span style='color: var(--BGVermelho);'>FALTA</span>";
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
		$bairro				= (!empty($linhaD024['D024_Bairro']))   	  	  ? $linhaD024['D024_Bairro']   	  	  : "<span style='color: var(--BGVermelho);'>FALTA</span>";
		
		$nomeEmpresa = substr($linhaD024['D024_Nome_Empresa'], 0, 17);
		$nomeFantasia = substr($linhaD024['D024_Nome_Fantasia'], 0, 18);
		$endereco = substr($linhaD024['D024_Endereco'], 0, 12);
		//$bairro = substr($bairro, 0, 12);

		$retorno = <<<EOT
		<style type="text/css">
		.containerTop{
			width: 99%;
			height: 102px;
			float:left;
			margin-left: 5px;
			padding:0px;
		}
		.containerTop table{
			width: 100%;
			height: 100px;
			float: left;
		}
		</style>
		<div class="containerTop ui-state-default ui-corner-all">
			<div>
				<table>
					<tr>
EOT;
						$retorno .= <<<EOT
						<td>
							<table>
								<tr class="dadosEmpresa">
									<td style="cursor: pointer;border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Razão Social</span><br/><span style="font-weight:bold; font-size:11px;">$nomeEmpresa</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Endereço</span><br /><span style="font-weight:normal; font-size:11px;">$endereco - Nº{$linhaD024['D024_Numero']}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px;"><span style="font-weight:normal; font-size:10px;">Bairro</span><br /><span style="font-weight:normal; font-size:11px;">{$bairro}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Nome Fantasia</span><br /><span style="font-weight:normal; font-size:11px;">$nomeFantasia</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Cidade/UF</span><br /><span style="font-weight:normal; font-size:11px;">{$linhaD024['D020_Nome_Cidade']}/{$linhaD024['D018_UF']}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px;"><span style="font-weight:normal; font-size:10px;">Vendedor</span><br /><span style="font-weight:normal; font-size:11px;">{$C007_Primeiro_Nome}</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td style="border-bottom:1px solid #ACACAC;"><span style="font-weight:normal; font-size:10px;">Telefone</span><br /><span style="font-weight:bold; font-size:11px;">{$D024_DDD}-{$D024_Telefone}</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid; border-color: #ACACAC;">{$cnpjCpf}</td>
								</tr>
								<tr>
									<td style="border-bottom:0px"><span style="font-weight:normal; font-size:10px;">IE</span><br /><span style="font-weight:normal; font-size:11px;">{$D024_IE}</td>
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Fornecedor', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id), [960,583]);
			});
		} </script></div>
EOT;
		echo $retorno;
	break;
	
	case 'srm001DadosFinanceiro':
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
			height: 102px;
			float:left;
			margin-left: 5px;
		}
		
		.containerFinanceiro table {
			align: center;
			width: 47.7%;
			height: 100%;
			float: left;
			margin-left: 5px;
			padding-right: 5px;
		}
		</style>
			<div class="containerFinanceiro ui-state-default ui-corner-all">
				<!--<table width=50px>
					<tr>
						<td>Cadastro</td>
						<td><span style="font-weight:normal; font-size:11px;">{$dataCadastro}</td>
					</tr>
					<tr>
						<td>Maior compra</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$maiorCompra}</td>
					</tr>
					<tr>
						<td>Última compra</td>
						<td><span style="font-weight:normal; font-size:11px;">{$ultimaCompra}</td>
					</tr>
					<tr>
						<td>Maior atraso</td>
						<td><span style="font-weight:normal; font-size:11px;">{$maiorAtraso[0]} dias</td>
					</tr>
                    <tr>
                        <td style="border-bottom: none;">Freq.venda</td>
                        <td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">{$mesesComprados}</td>
                    </tr>
				</table>
				<table width=50px>
					<tr>
						<td>À Pagar</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$totalPagar}</td>
					</tr>
					<tr>
						<td>Atrasado</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$totalAtrasado}</td>
					</tr>
					<tr>
						<td>Limite</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ {$valorLimite}</td>
					</tr>
					<tr>
						<td>Saldo</td>
						<td><span style="font-weight:normal; font-size:11px;">R$ $saldoLimite</td>
					</tr>
                    <tr>
                        <td style="border-bottom: none;">Média mês</td>
                        <td style="border-bottom: none;"><span style="font-weight:normal; font-size:11px;">R$ {$mediaVendasUlt12Meses}</td>
                    </tr>
				</table>-->
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
	
	case 'srm001Observacoes':
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
			height: 102px;
			float:left;
			margin-left:8px;
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
		<div class="observacoes  ui-state-default ui-corner-all">
			<div class="comercial">
				<b>Observação comercial:</b><br />
				<span style="font-weight:normal;color: var(--BGVermelho); font-size:11px;">$obsComercial
			</div>
			<div class="financeiro">
				<b>Observação financeiro:</b><br />
				<span style="color: var(--BGVermelho);"><span style="font-weight:normal; font-size:11px;">$obsFinanceiro</span>
			</div>
		</div>
EOT;
	break;
    
    case 'srm001ImagemOriginal':
        $urlImagem = (isset($r_urlImagem)) ? $r_urlImagem : false;
        $nomeLinha = (isset($r_nomeLinha)) ? $r_nomeLinha : false;
        $urlImagem = urldecode($urlImagem);
        echo <<<EOT
        <div>
            <img src="{$urlImagem}" />
        </div>
EOT;
    break;

	case 'cabecalhoPedido':
		// Select
        $sql = <<<EOT
        SELECT 
        	D001_Codigo_Produto,
        	D001_Descricao_Produto,
        	D009_Quantidade_Estoque_Liquido,
        	D009_Quantidade_Estoque_Loja,
        	D001_Id
        FROM D009
    		left join D049 on D049_Id=D009_D049_Id
    		left join D001 on D001_Id=D049_D001_Id
        	where D009_Id='{$r_acaoId}'
EOT;
        $resultado = mysql_query($sql);
        $row = mysql_fetch_assoc($resultado);
        
        //onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + '{$row['D024_Id']}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', [930,583]);"
        $urlCadastroCliente = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Produto', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent('{$row['D001_Id']}') + '&tabela=D001', [1000,583]);"
EOT;

		$row['D009_Quantidade_Estoque_Liquido'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Liquido']);
		$row['D009_Quantidade_Estoque_Loja'] = gCorrigeNumero($row['D009_Quantidade_Estoque_Loja']);

        // Print
        echo <<<EOT
            <div class="contentCabecalhoLayout">
                <table class="tabela ui-state-default ui-corner-all">
                    <tr class="linhaDesc" style="cursor:pointer;" {$urlCadastroCliente}>
                        <td width='20%'>Código</td>
                        <td width='50%'>Descrição</td>
                        <td width='15%'>Estoque Líquido</td>
                        <td width='15%'>Estoque Loja</td>
                    </tr>
                    <tr class="linhaVal" style="cursor:pointer;" {$urlCadastroCliente}>
                        <td align='center'>{$row['D001_Codigo_Produto']}</td>
                        <td>{$row['D001_Descricao_Produto']}</td>
                        <td>{$row['D009_Quantidade_Estoque_Liquido']}</td>
                        <td>{$row['D009_Quantidade_Estoque_Loja']}</td>
                    </tr>
                </table>
            </div>
EOT;

	break;
}
?>
