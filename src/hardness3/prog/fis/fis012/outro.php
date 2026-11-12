<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Mdfe
	 * Chamado em: fis012TabMdfeCadastro
	 */
	case 'fis012CabecalhoMdfe':
		// Select
		$sql = <<<EOT
		SELECT T276_Numero_MDFe,
			   T276_Emitente_Nome_Fantasia,
			   T276_Flag_Modalidade_Transporte,
			   descarregamentoD018.D018_UF descarregamentoD018,
			   carregamentoD018.D018_UF carregamentoD018,
			   T276_Ambiente_MDFe
		  FROM T276
	 LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento
	 LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento
		 WHERE T276_Id = '{$r_acaoId}'
EOT;

		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$T276_Numero_MDFe = gTruncar($row['T276_Numero_MDFe'], 30);
		$T276_Emitente_Nome_Fantasia = gTruncar($row['T276_Emitente_Nome_Fantasia'], 50);
		$T276_UF_Carregamento = gTruncar($row['carregamentoD018'], 5);
		$T276_UF_Descarregamento = gTruncar($row['descarregamentoD018'], 5);
		$Modalidade = 'RODOVIÁRIO';

		if($row['T276_Ambiente_MDFe'] == 2){
			$T276_Ambiente_MDFe = "<span style='color:white;background-color: var(--BGVermelho)';font-size:50px>HOMOLOGAÇÃO</span>";
		}else{
			$T276_Ambiente_MDFe = "PRODUÇÃO";
		}

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Nº</td>
						<td width='400px'>Emitente</td>
						<td width='150px'>UF Carregamento</td>
						<td width='150px'>UF Descarregamento</td>
						<td width='150px'>Modal</td>
						<td width='500px'>Ambiente MDF-e</td> 
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['T276_Numero_MDFe']}</td>
						<td>{$T276_Emitente_Nome_Fantasia}</td>
						<td>{$T276_UF_Carregamento}</td>
						<td>{$T276_UF_Descarregamento}</td>
						<td>{$Modalidade}</td>
						<td>{$T276_Ambiente_MDFe}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}
