<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho CT-e	 
	 */
	case 'est020CabecalhoCte':
		$sql = <<<EOT
			SELECT 	T084_Numero_Conhecimento,
					CASE 
						WHEN T084_Flag_Modalidade_Transporte = "01" THEN "Rodoviário"     
						WHEN T084_Flag_Modalidade_Transporte = "02" THEN "Aéreo"
						WHEN T084_Flag_Modalidade_Transporte = "03" THEN "Aquaviário"
						WHEN T084_Flag_Modalidade_Transporte = "04" THEN "Ferroviário"
						WHEN T084_Flag_Modalidade_Transporte = "05" THEN "Dutoviário"
						WHEN T084_Flag_Modalidade_Transporte = "06" THEN "Multimodal"    
						ELSE "Não Informado"
					END as Modal,
					IF(C004_Ambiente_CTe = 2, 'Homogação', 'Produção') as Ambiente					
				FROM T084 
				LEFT JOIN C004 ON C004_Id = T084_C004_Id
				WHERE T084_Id = '{$r_acaoId}'
		EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Nº</td>						
						<td width='150px'>Modal</td>
						<td width='500px'>Ambiente CT-e</td> 
					</tr>
					<tr class="linhaVal">
						<td>{$row['T084_Numero_Conhecimento']}</td>											
						<td>{$row['Modal']}</td>
						<td>{$row['Ambiente']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
