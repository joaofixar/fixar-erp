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
	case 'est006CabecalhoOrdemCompra':
		$T011_Id = (isset($r_T011_Id)) ? $r_T011_Id : false;
		// Select
		$sql = <<<EOT
		SELECT 
		*
		FROM T011 
		left join D024 on D024_Id=T011_D024_Id 
		left join C007 on C007_Id=T011_C007_Id 
		left join D020 on D020_Id=D024_D020_Id
		left join D018 on D018_Id=D024_D018_Id
		WHERE T011_Id='{$T011_Id}';
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
			$possuiRestricoes = "<font color='green'><b>LIBERADO</b></font>";
		}
		$Contribuinte="<font color='yellow' style='background:red'><b>&nbsp;INFORMAR!&nbsp;</b></font>";
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
		
		$dataEmissao = gCorrigeData($row['T011_Data_Emissao']);
		
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Nº</td>
						<td width='30px'>Código</td>
						<td width='450px'>Fornecedor</td>
						<td width='150px'>Cidade</td>
						<td width='30px'>UF</td>
						<td width='70px'>Emissão</td>
						<td width='50px'>Tipo</td>
						<td width='50px'>Financeiro</td>
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$T011_Id}</td>
						<td>{$row['D024_Id']}</td>
						<td>{$row['D024_Nome_Empresa']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D018_UF']}</td>
						<td>{$dataEmissao}</td>
						<td>{$Contribuinte}</td>
						<td>{$possuiRestricoes}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>