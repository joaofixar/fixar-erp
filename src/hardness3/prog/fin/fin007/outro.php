<?php
namespace hardness;
require_once("old/lib/funcoes.php");
/**
 * Ações
 */
switch($g['r'][3]){

	case 'fin007cabecalhoValorLimite':
		// Select
		$sql = <<<EOT
		SELECT
			*
		FROM
			D024
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE
			(D024_Id='{$r_acaoId}')
EOT;
		
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		log("resultado=" .$sql);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
		$D024_Cnpj_Matriz = $row['D024_Cnpj_Matriz'];
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" {$habilitarLinkCadastro}>
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Cliente</td>
						<td width='160px'>CNPJ Matriz</td>
						<td width='200px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
						{$atalho}
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$D024_Cnpj_Matriz}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;

case 'fin007cabecalhoSaldoLimite':
		// Select
		$sql = <<<EOT
		SELECT
			*
		FROM
			D024
		LEFT JOIN D020 ON D020_Id=D024_D020_Id
		LEFT JOIN D018 ON D018_Id=D024_D018_Id
		WHERE
			(D024_Id='{$r_acaoId}')
EOT;
		
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		log("resultado=" .$sql);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) { 
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}

		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 50);
		$D024_Cnpj_Matriz = $row['D024_Cnpj_Matriz'];
		$D020_Nome_Cidade = gTruncar($row['D020_Nome_Cidade'], 19);

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout" {$habilitarLinkCadastro}>
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td width='70px'>Código</td>
						<td width='400px'>Cliente</td>
						<td width='160px'>CNPJ Matriz</td>
						<td width='200px'>Cidade</td>
						<td width='150px'>Estado</td>
						<td></td>
						{$atalho}
					</tr>
					<tr class="linhaVal">
						<td align='center'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$D024_Cnpj_Matriz}</td>
						<td>{$D020_Nome_Cidade}</td>
						<td>{$row['D018_UF']}</td>
						<td></td>
					</tr>
				</table>
			</div>
EOT;

	break;
	/**
	 * Cabeçalho Agenda
	 */
	case 'cad001CabecalhoAgenda':
		// Select
		if(isset($r_D024_Id) && $r_D024_Id > 0 && $r_acaoId <= 0){
			$resultado = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Id = '{$r_D024_Id}'");
		} else {
			$resultado = mysql_query("SELECT * FROM D085 LEFT JOIN D024 ON D024_Id=D085_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id LEFT JOIN T003 ON T003_Id=D085_T003_Id WHERE D085_Id = '{$r_acaoId}'");
		}
		$row = mysql_fetch_assoc($resultado);

		if(isset($r_T003_Id) && $r_T003_Id > 0){
			$resultado = mysql_query("SELECT * FROM T003 WHERE T003_Id = '{$r_T003_Id}'");
			$row2 = mysql_fetch_assoc($resultado);
			$row = array_merge($row, $row2);
		}

		if($row['T003_Id'] > 0 && ($g['nivelAlcada'] == '4' || $row['T003_C007_Id_Vendedor_Interno'] == $g['usuarioAtual'])){
			$row['T003_Id'] = "<span style=\"cursor: pointer;\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$row['T003_Id']}') + '&tabela=T003', [990,605]);\") + '&tabela=D024', 'auto');\"><u>{$row['T003_Id']}</u></span>";
		}

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) {
				$row_ = '<span>-</span>';
			}
		}

		$D024_Nome_Empresa = gTruncar($row['D024_Nome_Empresa'], 30);

		$row['T003_Data_Emissao'] = gCorrigeData($row['T003_Data_Emissao']);
		$row['T003_Valor_Total'] = gCorrigeNumero($row['T003_Valor_Total']);

        $urlCadastroCliente = <<<EOT
            onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/', '&D024_Id=' + encodeURIComponent('{$row['D024_Id']}') + '&tabela=D024', 'auto');"
EOT;

		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all" style="cursor:pointer;">
					<tr class="linhaDesc" {$urlCadastroCliente}>
						<td width='5%' align='center'>Código</td>
						<td width='40%'>Cliente</td>
						<td width='15%'>Telefone</td>
						<td width='25%'>Cidade</td>
						<td width='5%'>UF</td>
					</tr>
					<tr class="linhaVal" {$urlCadastroCliente}>
						<td style='text-align: center; padding-right: 2px;'>{$row['D024_Id']}</td>
						<td>{$D024_Nome_Empresa}</td>
						<td>{$row['D024_DDD_Telefone_1']} - {$row['D024_Telefone_1']}</td>
						<td>{$row['D020_Nome_Cidade']}</td>
						<td>{$row['D020_UF']}</td>
					</tr>
				</table>
			</div>
EOT;

	break;
}