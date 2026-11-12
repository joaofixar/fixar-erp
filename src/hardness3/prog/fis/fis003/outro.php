<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Cliente
	 * Chamado em: cad001tabCadastro
	 */
	case 'fis003CabecalhoNCM':
		// Select
		$sql = mysql_query("SELECT * FROM D149 LEFT JOIN D005 ON D005_Id=D149_D005_Id WHERE D149_Id = '{$r_acaoId}'");
		$row = mysql_fetch_assoc($sql);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		
		$D005_Observacao = gTruncar($row['D005_Observacao'], 70);
		
		// Print
		echo <<<EOT
			<div style="cursor: pointer;" class="contentCabecalhoLayout" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro NCM - Vale para todas empresas', '/fis/fis003/content/fis003ContentNCMIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$row['D005_Id']}'), [600,250]);">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='100px'>Classificação Fiscal</td>
						<td width='500px'>Descrição</td>
						<td></td>
					</tr>
					<tr style="font-weight: bold; font-size: 1.1em;">
						<td align='left'>{$row['D005_Classificacao_Fiscal']}</td>
						<td>{$D005_Observacao}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>