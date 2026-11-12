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
	case 'cad052Cabecalho':
		// Select
		$sql = <<<EOT
		SELECT 
			*
		FROM 
			D001
			LEFT JOIN D005 on D005_Id=D001_D005_Id
			LEFT JOIN D037 on D037_Id=D001_D037_Id
			LEFT JOIN D049 on D049_D001_Id=D001_Id
			LEFT JOIN D009 on D009_D049_Id=D049_Id
		WHERE
			(D001_Id='{$r_D001_Id}')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		
		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		if($row['D001_Flag_Pre_Cadastro'] == "N"){
			$preCad = "Não";
		} else {
			$preCad = "Sim";
		}
		if($row['D001_Flag_Ativo'] == "N"){
			$ativo = "Não";
		} else {
			$ativo = "Sim";
		}
		$D005_Observacao = substr($row['D005_Observacao'], 0, 25);
		if(!empty($D005_Observacao)){
			$D005_Observacao = " - ".$D005_Observacao;
		}
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>Código</td>
						<td>Produto</td>
						<td>Custo</td>
						<td>Unidade</td>
						<td>Lote Econômico</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
						<td>{$row['D009_Valor_Custo_Unitario']}</td>
						<td>{$row['D037_Unidade']}</td>
						<td>{$row['D001_Lote_Economico']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
?>