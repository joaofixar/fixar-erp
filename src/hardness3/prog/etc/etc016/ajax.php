<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

/**
* Ações
*/
switch($g['r'][3]) {
	case 'etc016ProdutoNovo':
		$codbarra = isset($r_codbarra) ? $r_codbarra : '';
		$codbarra = mysql_real_escape_string($codbarra);
		$flag = base64_decode($r_flag);
		$cod = unserialize(base64_decode($r_cod));
		$qtd = base64_decode($r_qtd);
		$desc = base64_decode($r_desc);
		$itens = json_decode($r_itens, true);
		if (empty($codbarra)) {
			$resposta['code'] = false;
			$resposta['data'] = "Código de barras não encontrado no sistema!";
		} else {
			// Sql vindo do grid
			$sql = base64_decode($r_sql);
			$resGrid = mysql_query($sql);
			$encontrou = false;
			$rowsGrid = array();
			while ($rowGrid = mysql_fetch_assoc($resGrid)) {
				$rowsGrid[] = $rowGrid;
			}
			// Soma quantidade por cod. barra (GRID) para não limitar por linha
			$arrayGridQtd = array();
			foreach ($rowsGrid as $rowGrid) {
				$codIguais = array(); // evita duplicar quando o mesmo cod. aparece em mais de um campo
				foreach ($cod as $codValue) {
					$codBarras = explode('|', $rowGrid[$codValue]);
					foreach ($codBarras as $codBarrasItem) {
						if (empty($codBarrasItem)) { continue; }
						$codKey = $codBarrasItem;
						if (is_numeric($codKey)) {
							$codKey = ltrim($codKey, '0');
							if ($codKey === '') { $codKey = '0'; }
						}
						if (in_array($codKey, $codIguais)) { continue; }
						if (!isset($arrayGridQtd[$codKey])) {
							$arrayGridQtd[$codKey] = 0;
						}
						$rowQtd = str_replace(',', '.', $rowGrid[$qtd]);
						$arrayGridQtd[$codKey] += floatval($rowQtd);
						$codIguais[] = $codKey;
					}
				}
			}
			// Normaliza itens recebidos do front para evitar inconsistências com zeros à esquerda
			$itensNorm = array();
			if (is_array($itens)) {
				foreach ($itens as $itensKey => $itensValue) {
					$itensKeyNorm = $itensKey;
					if (is_numeric($itensKeyNorm)) {
						$itensKeyNorm = ltrim($itensKeyNorm, '0');
						if ($itensKeyNorm === '') { $itensKeyNorm = '0'; }
					}
					if (!isset($itensNorm[$itensKeyNorm])) {
						$itensNorm[$itensKeyNorm] = 0;
					}
					$itensValue = str_replace(',', '.', $itensValue);
					$itensNorm[$itensKeyNorm] += floatval($itensValue);
				}
			}
			foreach ($rowsGrid as $rowGrid) {
				foreach ($cod as $codValue) {
					$linhaArray = json_encode($rowGrid);
					$codBarras = explode('|', $rowGrid[$codValue]);
					$multiplicadores = explode('|', $rowGrid['D083A_Multiplicador']);
					foreach ($codBarras as $codBarrasItem) {
						
						if (empty($codBarrasItem)) { continue; }
						if ($codBarrasItem == $codbarra) {
							$codbarraNorm = $codbarra;
							if (is_numeric($codbarraNorm)) {
								$codbarraNorm = ltrim($codbarraNorm, '0');
								if ($codbarraNorm === '') { $codbarraNorm = '0'; }
							}
							$quantidade = 1;
							$value      = '';
							$encontrou  = true;
							foreach ($multiplicadores as $multiplicador) {
								$quantidades = explode('X', $multiplicador);
								if ((count($quantidades) === 2) and ($quantidades[0] === $codbarra)) {
									$quantidade = floatval($quantidades[1]);
									$value      = $quantidades[1];
								}
							}

							$limiteTotal = isset($arrayGridQtd[$codbarraNorm]) ? $arrayGridQtd[$codbarraNorm] : 0;
							$qtdAtual = isset($itensNorm[$codbarraNorm]) ? $itensNorm[$codbarraNorm] : 0;
							$qtdNovo = ($value !== '') ? floatval(str_replace(',', '.', $value)) : 1;
							if (($qtdAtual + $qtdNovo) > $limiteTotal) {
								$resposta['code'] = false;
                                $resposta['data'] = "Quantidade excedida para o item {$codbarra}";
							} else {														
							$descricao = $codBarrasItem . ' - ' . $rowGrid[$desc];
							$resposta['data'] = <<<EOT
<tr class="item" style="width: 100%; height: 35px;" verificado="1" cod="{$codBarrasItem}">
	<td style="width: 1%;">
		<span title="Cancelar" class="ui-icon ui-icon-circle-close" style="margin: 0 8px; cursor: pointer;" onclick="$(this).closest('tr').remove(); verCodBarraAjustesUI();"></span>
	</td>
	<td style="text-align: left;">
		<div style="font-size: 1.3em; font-weight: bold;">
			{$descricao}
		</div>
	</td>
	<td style="text-align: right; width: 1%; padding: 6px 11px;">
		<input type="text" style="width: 80px; font-size: 15px; text-align: right;" placeholder="{$quantidade}" value="{$value}" onkeypress="if (event.which == 13) { $(this).blur(); }" onblur="verCodBarraAjustesUI();" />
	</td>
</tr>

EOT;
							}
						}
					}
				}
			}
			if (!$encontrou) {
				$resposta['code'] = false;
				$sql = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D001 WHERE D001_Codigo_Barras = '{$codbarra}'";
				$resD001 = mysql_query($sql);
				if (!mysql_num_rows($resD001)) {
					$sql = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D083 LEFT JOIN D001 ON D001_Id = D083_D001_Id WHERE D083_Codigo_Barras = '{$codbarra}' LIMIT 1";
					$resD001 = mysql_query($sql);
					if (!mysql_num_rows($resD001)) {
						$resposta['data'] = "Código de barras não encontrado no sistema!";
					} else {
						$rowD001 = mysql_fetch_assoc($resD001);
						$resposta['data'] = "Produto não encontrado no pedido: " . $rowD001['D001_Codigo_Produto'] . ' - ' . substr($rowD001['D001_Descricao_Produto'], 0, 40) . " ...";	
					}
				} else {
					$rowD001 = mysql_fetch_assoc($resD001);
					$resposta['data'] = "Produto não encontrado no pedido: " . $rowD001['D001_Codigo_Produto'] . ' - ' . substr($rowD001['D001_Descricao_Produto'], 0, 40) . " ...";	
				}
			}
		}
	break;

	case 'etc016ProdutoNovoPDV':
		$codbarra = isset($r_codbarra) ? $r_codbarra : '';
		$codbarra = mysql_real_escape_string($codbarra);
		$cod = unserialize(base64_decode($r_cod));
		$id = base64_decode($r_id);
		$desc = base64_decode($r_desc);
		$preco = base64_decode($r_preco);

		// Sql vindo do grid
		$sql = base64_decode($r_sql);
		// Monta WHERE
		$extra = '';
		foreach ($cod as $codValue) {
			$extra .= empty($extra) ? '' : ' OR ';
			$extra .= $codValue . " = '{$codbarra}'";
		}
		if (stripos($sql, 'WHERE') !== false) {
			$sql = str_replace('WHERE', "WHERE ({$extra}) AND ", $sql);
		} else {
			$sql .= " WHERE ($extra)";
		}
		$resGrid = mysql_query($sql);
		if (mysql_num_rows($resGrid) > 0) {
			$rowGrid = mysql_fetch_assoc($resGrid);
			$descricao = $codbarra . ' - ' . $rowGrid[$desc];
			$mostrarPreco = '';
			if ($preco) {
				$mostrarPreco = eval($preco);
				$mostrarPreco = <<<EOT
					<td class="mostrarPreco" style="text-align: right; font-size: 1.5em; padding: 0px 11px; width: 1%; white-space: nowrap;" preco-original="{$mostrarPreco}">
						R$ {$mostrarPreco}
					</div>
EOT;
			}
			$resposta['data'] = <<<EOT
<tr class="item" style="width: 100%; height: 35px;" verificado="1" cod="{$rowGrid[$id]}">
	<td style="width: 1%;">
		<span title="Cancelar" class="ui-icon ui-icon-circle-close" style="margin: 0 8px; cursor: pointer;" onclick="$(this).closest('tr').remove(); verCodBarraAjustesUI();"></span>
	</td>
	<td style="text-align: left;">
		<div style="font-size: 1.3em; font-weight: bold;">
			{$descricao}
		</div>
	</td>
	<td style="text-align: right; width: 1%; padding: 6px 11px;">
		<input type="text" style="width: 80px; font-size: 15px; text-align: right;" placeholder="1" onkeypress="if (event.which == 13) { $(this).blur(); }" onblur="verCodBarraAjustesUI();" />
	</td>
	{$mostrarPreco}
</tr>
EOT;
		} else {
			$resposta['code'] = false;
			$resposta['data'] = "Código de barras não encontrado no sistema!";
		}
	break;

	case 'etc016Verificar':
		$itens = json_decode($r_itens, true);
		$flag = base64_decode($r_flag);
		$cod = unserialize(base64_decode($r_cod));
		$qtd = base64_decode($r_qtd);
		$desc = base64_decode($r_desc);
		$tabela = explode('_', $flag);
		$tabela = $tabela[0];

		// Agrupamento quantidade por cod. barra (GRID)
		$arrayGrid = array();
		$arrayGridIds = array();
		$sql = base64_decode($r_sql);
		$resGrid = mysql_query($sql);
		while ($rowGrid = mysql_fetch_assoc($resGrid)) {
			$codIguais = array(); // para não duplicar caso os campos tenham o mesmo código de barras
			foreach ($cod as $codValue) {
				if (in_array($rowGrid[$codValue], $codIguais)) { continue; }
				if (!isset($arrayGrid[$rowGrid[$codValue]])) {
					$arrayGrid[$rowGrid[$codValue]] = 0;
				}
				$arrayGrid[$rowGrid[$codValue]] += $rowGrid[$qtd];
				// precisamos gravar as PK dos itens para fazer o update em todos depois
				$chavePrimaria = $rowGrid[$tabela . '_Id'];
				$arrayGridIds[$rowGrid[$codValue]][] = $chavePrimaria;
				$codIguais[] = $rowGrid[$codValue];
			}
		}

		// Agrupamento quantidade por cod. barra (ITENS)
		$arrayItens = array();
		foreach ($itens as $item) {
			if (!isset($arrayItens[$item['cod']])) {
				$arrayItens[$item['cod']] = 0;
			}
			$item['qtd'] = str_replace(',', '.', $item['qtd']);
			$arrayItens[$item['cod']] += $item['qtd'];
		}
		// Verifica
		foreach ($arrayGrid as $arrayGridKey => $arrayGridValue) {
			foreach ($arrayItens as $arrayItensKey => $arrayItensValue) {
				if ($arrayGridKey == $arrayItensKey && $arrayGridValue == $arrayItensValue) {
					foreach ($arrayGridIds[$arrayGridKey] as $chavePrimaria) {
						$sql = "UPDATE {$tabela} SET {$flag} = 'S' WHERE {$tabela}_Id = '{$chavePrimaria}'";
						mysql_query($sql);
					}
				}
			}
		}
	break;

	case 'etc016SqlCodVar':
		$codbarra = ltrim($r_codbarra, '0');
		$sql_codvar = base64_decode($r_sql_codvar);
		$sql_codvar = str_replace('[[CODVAR]]', $codbarra, $sql_codvar);

		$res = mysql_query($sql_codvar);
		if (!$res || mysql_num_rows($res) == 0) {
			$resposta['code'] = false;
			$resposta['data'] = "ID não econtrado no sistema!";
		} else {
			$resposta['data'] = base64_encode($sql_codvar);
		}
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
