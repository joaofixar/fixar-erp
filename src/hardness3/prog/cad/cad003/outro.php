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
	case 'cad003CabecalhoRepresentate':
		// Select
		$sql = <<<EOT
		SELECT
            D021_Id,
			D021_Codigo,
            D021_Nome_Empresa,
            D020_Nome_Cidade,
            D018_UF

		FROM
			D021
		LEFT JOIN D020 ON D020_Id=D021_D020_Id
		LEFT JOIN D018 ON D018_Id=D021_D018_Id
		WHERE
			(D021_Id='{$r_acaoId}')
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$D021_Nome_Empresa = gTruncar($row['D021_Nome_Empresa'], 50);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		//$habilitarLinkCadastro = "style=\"cursor: pointer;\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Representante', '/cad/cad003/content/cad003ContentCadastroRepresentantes/', '&acaoId=' + encodeURIComponent('{$row['D021_Id']}'), [960,583]);\"";
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" {$habilitarLinkCadastro}>
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Representante</td>
						<td width='150px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D021_Id']}</td>
						<td>{$D021_Nome_Empresa}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
