<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est004CabecalhoNotaFiscal':
		// Select
		$sql = <<<EOT
		SELECT 
			*,
			if(D024_Cnpj='',D024_Cpf,D024_Cnpj) as D024_Cnpj
		FROM 
			T013
		left join D024 on T013_D024_Id=D024_Id
		left join D020 on D024_D020_Id=D020_Id
		left join D018 on D020_D018_Id=D018_Id
		left join D030 on D024_D030_Id=D030_Id
		left join D006 on D006_Id=T013_D006_Id
		WHERE
			(T013_Id='$r_acaoId')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">????</span>';
			}
		}
		
		$dataEmissao = gCorrigeData($row['T013_Data_Emissao']);
		$dataEntrada = gCorrigeData($row['T013_Data_Entrada']);
		$row['D024_Nome_Empresa'] = gTruncar($row['D024_Nome_Empresa'], 50);
		$row['D020_Nome_Cidade'] = gTruncar($row['D020_Nome_Cidade'], 30);
		$valorTotal = gCorrigeNumero($row['T013_Valor_Total']);

		$abrirJanela = "abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'SRM - Fornecedor', '/srm/srm001/content/srm001ContentPrincipalSRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');";
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc" style="cursor:pointer;" onclick="{$abrirJanela}">
						<td width='70px'>Nota Fiscal</td>
						<td width='20px'>Cód</td>
						<td width='400px'>Fornecedor</td>
						<td width='200px'>Cidade</td>
						<td width='50px'>Estado</td>
						<td width='140px'>Emissão</td>
						<td width='70px'>Entrada</td>
						<td width='70px'>Valor total</td>
						<td></td>
					</tr>
					<tr class="linhaVal" style="cursor:pointer;" onclick="{$abrirJanela}">
						<td align=center>{$row['T013_Numero_Nota_Fiscal']}</td>
						<td>{$row['D024_Id']}</td>
						<td>{$row['D024_Nome_Empresa']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$dataEntrada}</td>
						<td>{$valorTotal}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;

	case 'est004CabecalhoNotaFiscalOC':
		// Select
		$T013 = mysql_query("SELECT T013_Numero_Nota_Fiscal,
									D024_Id,
		                            D024_Nome_Empresa,
									T013_Valor_Total_Produtos,
									T013_Valor_Total_ICMS,
									T013_Valor_Total_IPI,
									T013_Valor_ICMS_Substituicao,
									T013_Valor_Total,
									sum(T014_Valor_Total_Preco_Recebida) as valorProdutos,
									sum(T014_Valor_ICMS_Recebida) as valorICMS,
									sum(T014_Valor_IPI_Recebida) as valorIPI,
									sum(T014_Valor_ICMS_Substituicao_Retencao_Recebida) as valorST,
									sum(T014_Valor_Custo_Unitario_Recebida*T014_Quantidade) as valorTotal
							   FROM T013
						  LEFT JOIN D024 on T013_D024_Id=D024_Id
						  LEFT JOIN T014 on T014_T013_Id=T013_Id
							  WHERE T013_Id='$r_acaoId'");

		$mT013 = mysql_fetch_assoc($T013);
		
		$mT013['D024_Nome_Empresa'] = gTruncar($mT013['D024_Nome_Empresa'], 50);
		$valorProdutos = gCorrigeNumero($mT013['T013_Valor_Total_Produtos']);
		$valorICMS     = gCorrigeNumero($mT013['T013_Valor_Total_ICMS']);
		$valorIPI      = gCorrigeNumero($mT013['T013_Valor_Total_IPI']);
		$valorST       = gCorrigeNumero($mT013['T013_Valor_ICMS_Substituicao']);
		$valorTotal    = gCorrigeNumero($mT013['T013_Valor_Total']);

		$valorProdutosOC = gCorrigeNumero($mT013['valorProdutos']);
		$valorICMSOC     = gCorrigeNumero($mT013['valorICMS']);
		$valorIPIOC      = gCorrigeNumero($mT013['valorIPI']);
		$valorSTOC       = gCorrigeNumero($mT013['valorST']);
		$valorTotalOC    = gCorrigeNumero($mT013['valorTotal']);

		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Nota Fiscal</td>
						<td width='20px'>Cód</td>
						<td width='400px'>Fornecedor</td>
						<td width='120px' align='right'>Valor produtos</td>
						<td width='120px' align='right'>Valor ICMS</td>
						<td width='120px' align='right'>Valor IPI</td>
						<td width='120px' align='right'>Valor ST</td>
						<!--<td width='120px' align='right'>Valor total</td>-->
						<td></td>
					</tr>
					<tr class="linhaVal">
						<td align=center>{$mT013['T013_Numero_Nota_Fiscal']}</td>
						<td>{$mT013['D024_Id']}</td>
						<td>{$mT013['D024_Nome_Empresa']}</td>
						<td align='right'>{$valorProdutos}</td>
						<td align='right'>{$valorICMS}</td>
						<td align='right'>{$valorIPI}</td>
						<td align='right'>{$valorST}</td>
						<!--<td align='right'>{$valorTotal}</td>-->
						<td></td>
					</tr>
					<tr class="linhaVal">
						<td>&nbsp;</td>
						<td>&nbsp</td>
						<td>Total ordem de compra:</td>
						<td align='right'>{$valorProdutosOC}</td>
						<td align='right'>{$valorICMSOC}</td>
						<td align='right'>{$valorIPIOC}</td>
						<td align='right'>{$valorSTOC}</td>
						<!--<td align='right'>{$valorTotalOC}</td>-->
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;


	case 'est004CabecalhoAssociarItemNovo':
		// Select
		$sql = <<<EOT
		SELECT 
			*
		FROM 
			T014
		left join T013 on T013_Id=T014_T013_Id
left join T012 on T012_Id=T014_T012_Id
left join T225 on T225_Id=T014_T225_Id
left join D009 on D009_Id=T014_D009_Id
left join D049 on D049_Id=D009_D049_Id
left join D001 on D001_Id=D049_D001_Id
left join D037 on D037_Id=D001_D037_Id
left join D082 on D082_Id=D049_D082_Id
left join T001 on T001_T014_Id=T014_Id
left join D006 on D006_Id=T014_D006_Id
		WHERE
			(T014_Id='{$r_acaoId}')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">-</span>';
			}
		}

		$row['T014_Quantidade'] = number_format($row['T014_Quantidade'],2,',','.');
		$row['T014_Valor_Custo_Unitario'] = number_format($row['T014_Valor_Custo_Unitario'],4,',','.');
		$row['T014_Aliquota_IPI'] = number_format($row['T014_Aliquota_IPI'],2,',','.');
		$row['T014_Aliquota_ICMS'] = number_format($row['T014_Aliquota_ICMS'],2,',','.');
		$row['T014_Valor_ICMS_Substituicao'] = number_format($row['T014_Valor_ICMS_Substituicao'],2,',','.');
		
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Cód Forn</td>
						<td width='80px'>Código</td>
						<td width='380px'>Descrição</td>
						<td width='180px'>OC/Item XML</td>
						<td>NCM</td>
						<td>Qtde</td>
						<td>Preço</td>
						<td>IPI</td>
						<td>ICMS</td>
						<td>ST R$</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T014_Codigo_Produto']}</td>
						<td>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['T014_Descricao_Produto']}</td>
						<td>{$row['T014_Numero_Pedido_Compra']} / {$row['T014_Item_Pedido_Compra']}</td>
						<td>{$row['T014_Classificacao_Fiscal']}</td>
						<td>{$row['T014_Quantidade']}</td>
						<td>{$row['T014_Valor_Custo_Unitario']}</td>
						<td>{$row['T014_Aliquota_IPI']}</td>
						<td>{$row['T014_Aliquota_ICMS']}</td>
						<td>{$row['T014_Valor_ICMS_Substituicao']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>