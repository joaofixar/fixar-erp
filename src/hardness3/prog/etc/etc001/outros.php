<?php
namespace hardness;
/**
 * Ações
 */
switch($g['r'][3]) {
	/**
	* Grid Principal
	* Chamado em: fin001tab01
	*/
	case 'etc001_outros_iframe':
		$r_cache = (isset($r_cache)) ? true : false;
		$r_acaoId_ = mysql_real_escape_string($r_acaoId);
		$sql = "SELECT T144_Url, T144_Versao_Cache FROM T144 WHERE T144_Id = '{$r_acaoId_}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		if (!preg_match("/https?:\/\//ui", $row['T144_Url'])) {
			$row['T144_Url'] = 'http://' . $row['T144_Url'];
		}
		
		echo <<<EOT
		<script>
			$('#links_iframe').height($('#links_iframe').height()-40);
		</script>
EOT;
		
		echo '<table>';
		echo "<tr><td>Endereço:</td><td style=\"font-weight: bold;\">{$row['T144_Url']}<td></tr>";
		echo '</table>';
		echo '<div style="margin-top: 5px; width: 100%; height: 2px; background-color: #A6C9E2;"></div>';
		if ($r_cache) {
			echo "<iframe id=\"links_iframe\" src='/etc/etc001/outros/etc001_outros_iframe_conteudo/?ajax=true&acaoId={$r_acaoId}' style='margin-top: 3px; border: none; width: 100%; height: 100%;'>";
		} else {
			echo "<iframe id=\"links_iframe\" src='{$row['T144_Url']}' style='margin-top: 3px; border: none; width: 100%; height: 100%;'>";
		}
		// Updates
		$date = date('Y-m-d h:m:s');
		if (empty($row['T144_Versao_Cache'])) {
			$cache = mysql_real_escape_string(@file_get_contents($row['T144_Url']));
			$sql = "UPDATE T144 SET T144_Ultimo_Acesso = '$date', T144_Versao_Cache = '$date', T144_Cache = '$cache' WHERE T144_Id = '{$r_acaoId_}';";
			mysql_query($sql);
			echo mysql_error();
			echo $sql;
		} else {
			$sql = "UPDATE T144 SET T144_Ultimo_Acesso = '$date' WHERE T144_Id = '{$r_acaoId_}';";
			mysql_query($sql);
		}
	break;
	
	case 'etc001_outros_iframe_conteudo':
		$r_acaoId_ = mysql_real_escape_string($r_acaoId);
		$sql = "SELECT T144_Cache FROM T144 WHERE T144_Id = '{$r_acaoId_}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);
		echo $row['T144_Cache'];
	break;
}
?>
