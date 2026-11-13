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
	case 'est001CabecalhoDE':
		// Select
		$sql = <<<EOT
		SELECT 
			T029_Id,
			T029_Data,
			T029_Descricao,
			C007_Primeiro_Nome,
			T029_Flag_Ok
		FROM 
			T029
			left join C007 on T029_C007_Id=C007_Id
		WHERE
			(T029_Id='{$r_acaoId}')
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
		if($row['T029_Flag_Ok'] == "S"){
			$statusDE = "<font color='red'><b>Concluído</b></font>";
		}else{
			$statusDE = "Execução";
		}
		
		$dataDE = gCorrigeData($row['T029_Data']);
		
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>D.E</td>
						<td>Data</td>
						<td>Descrição</td>
						<td>Usuário</td>
						<td>Status</td>
						<td></td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['T029_Id']}</td>
						<td>{$dataDE}</td>
						<td>{$row['T029_Descricao']}</td>
						<td>{$row['C007_Primeiro_Nome']}</td>
						<td>{$statusDE}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>