<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Seguradora
	 * Chamado em: cad066TabFormCadastrarSeguradoras
	 */
	case 'cad066CabecalhoSeguradora':
		// Select
		$sql = <<<EOT
		SELECT
			*
		FROM
			D172
		LEFT JOIN D020 ON D020_Id=D172_D020_Id
		LEFT JOIN D018 ON D018_Id=D172_D018_Id
		WHERE
			(D172_Id='{$r_acaoId}')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$D172_Nome = gTruncar($row['D172_Nome'], 50);
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" {$habilitarLinkCadastro}>
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Seguradora</td>
						<td width='150px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
						{$atalho}
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D172_Id']}</td>
						<td>{$D172_Nome}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
