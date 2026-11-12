<?php
namespace hardness;

/**
 * Ações
 */
switch($g['r'][3]){
	case 'etc007OutroRegistro':
		global $g;
		$r_acaoId = mysql_real_escape_string($r_acaoId,$g['conexaoBancoAuditoria']);
		$sql = "SELECT T162_Id, T162_C007_Id, T162_Acao, UNCOMPRESS(T162_Query) AS T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao, T162_Registro_Id, UNCOMPRESS(T162_Registro) AS T162_Registro, T162_IP FROM T162 WHERE T162_Id = '{$r_acaoId}';";
		$resultado = mysqli_query($sql,$g['conexaoBancoAuditoria']);
		$registroT162 = mysql_fetch_assoc($resultado);
		$registro = unserialize($registroT162['T162_Registro']);

		$sqlC007 = "SELECT C007_Nome, C007_Login FROM C007 WHERE C007_Id = '{$registroT162['T162_C007_Id']}';";
		$resultadoC007 = mysql_query($sqlC007);
		$registroC007 = mysql_fetch_assoc($resultadoC007);

		/*
		// ocultar IDs (futuramente mostrar campo na tabela pai)		
		$sql = <<<EOT
			SELECT 
				T162_Id, T162_C007_Id, T162_Acao, UNCOMPRESS(T162_Query) AS T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao, T162_Registro_Id, UNCOMPRESS(T162_Registro) AS T162_Registro, T162_IP, C007.* 
			FROM 
				T162 
			LEFT JOIN C007 ON C007_Id=T162_C007_Id 
			WHERE 
				(T162_Acao != 'S' AND T162_Tabela = '{$r_tabela}' AND T162_Registro_Id = '{$registro[$r_tabela . '_Id']}')
			ORDER BY 
				T162_DataHora DESC
EOT;
		$T162s = mysql_query($sql);

		// último registro (a primeira vez é o atual)
		$id = $registro[$r_tabela . '_Id'];
		$ultimo = mysql_query("SELECT * FROM {$r_tabela} WHERE {$r_tabela}_Id = '{$id}';");
		$ultimo = serialize(mysql_fetch_assoc($ultimo));
		
		$ultimoEncontrado = array();
		while ($row = mysql_fetch_assoc($T162s)) {
			if ($row['T162_Id'] == $r_acaoId) {
				$ultimoEncontrado = $ultimo;
			}
			$ultimo = $row['T162_Registro'];
		}
		$ultimoEncontrado = unserialize($ultimoEncontrado);
		*/
		
		echo '<div style="margin: 0px 0px 5px 10px; background: transparent;">';
		
			$registroT162['T162_DataHora'] = gCorrigeData($registroT162['T162_DataHora']);
			if ($registroT162['T162_Acao'] == 'I') {
				echo '<h2>Alterado por</h2>';
			} else if ($registroT162['T162_Acao'] == 'D') {
				echo '<h2>Removido por</h2>';
			} else {
				echo '<h2>Alterado por</h2>';
			}
			echo '<table style="border: 1px solid silver; width: 1%;" class="grid">';
			echo "<tr class='color1'><td>Login:</td><td>{$registroC007['C007_Login']}</td></tr>";
			echo "<tr class='color2'><td>Nome:</td><td>{$registroC007['C007_Nome']}</td></tr>";
			echo "<tr class='color1'><td>Data/hora:</td><td>{$registroT162['T162_DataHora']}</td></tr>";
			echo "<tr class='color1'><td>Tabela:</td><td>{$r_tabela}</td></tr>";
			echo '</table>';

			if ($registroT162['T162_Acao'] == 'I') {
				echo '<h2>Campos incluidos</h2>';
			} else if ($registroT162['T162_Acao'] == 'D') {
				echo '<h2>Último registro</h2>';
			} else {
				echo '<h2>Campos alterados</h2>';
			}
			echo '<table style="border: 1px solid silver; width: 1%;" class="grid">';
			echo '<thead>';
			if ($registroT162['T162_Acao'] == 'I' || $registroT162['T162_Acao'] == 'D') {
				echo '<tr><th>Campo</th><th>Valor</th></tr>';
			} else {
				echo '<tr><th>Campo</th><th>De</th><th>Para</th></tr>';
			}
			echo '</thead>';
			if ($registroT162['T162_Acao'] == 'I' || $registroT162['T162_Acao'] == 'D') {
				$nr = 1;
				foreach ($registro as $key => $value) {
					$key_ = str_replace($r_tabela . '_', '', $key);
					echo "<tr class='color{$nr}'><td>{$key_}</td><td>{$value}</td></tr>";
					$nr++;
					if ($nr == 3) { $nr = 1; }
				}
			} else {
				$nr = 1;
				foreach ($registro as $key => $value) {
					$hidden = '';
					if(strlen($value[0]) > 500 || strlen($value[1]) > 500){
						$hidden = 'style=\"display: none\";';
					}
					$key_ = str_replace($r_tabela . '_', '', $key);
					echo "<tr {$hidden} class='color{$nr}'><td>{$key_}</td><td>{$value[0]}</td><td>{$value[1]}</td></tr>";
					$nr++;
					if ($nr == 3) { $nr = 1; }
				}
			}
			echo '</table>';
			
			/*
			echo '<h2>Campos alterados</h2>';
			echo '<table style="border: 1px solid silver; width: 1%;" class="grid">';
			echo '<thead>';
			echo '<tr><th>Campo</th><th>De</th><th>Para</th></tr>';
			echo '</thead>';
			$nr = 1;
			foreach ($registro as $key => $value) {
				if ($ultimoEncontrado[$key] != $value ) {
					$key_ = str_replace($r_tabela . '_', '', $key);
					echo "<tr class='color{$nr}'><td>{$key_}</td><td>{$value}</td><td>{$ultimoEncontrado[$key]}</td></tr>";
					$nr++;
					if ($nr == 3) { $nr = 1; }
				}
			}
			echo '</table>';
			*/
			
			/*echo '<h2>Campos não alterados</h2>';
			echo '<table style="border: 1px solid silver; width: 1%;" class="grid">';
			echo '<thead>';
			echo '<tr><th>Campo</th><th>Valor</th></tr>';
			echo '</thead>';
			$nr = 1;
			foreach ($registro as $key => $value) {
				if ($ultimoEncontrado[$key] == $value ) {
					if (preg_match('/.*_.*_Id$/', $key)) { continue; }
					$key_ = str_replace($r_tabela . '_', '', $key);
					echo "<tr class='color{$nr}'><td>{$key_}</td><td>{$value}</td></tr>";
					$nr++;
					if ($nr == 3) { $nr = 1; }
				}
			}
			echo '</table>';*/
		echo '</div>';
	break;
}
?>