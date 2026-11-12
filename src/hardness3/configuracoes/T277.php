<?php
/**
 * Tabela T277
 */
$conf['restricaoTravar']['T277'] = <<<'EOT'
	$retorno = true;
		if ($operacao == 'alterar') {
			$sqlT276 = "SELECT T276_Numero_Protocolo_MDFe, T276_Flag_Travar_MDFe FROM T277 LEFT JOIN T276 on T276_Id=T277_T276_Id WHERE T277_Id='{$valorPk}'";
			$resultT276 = mysql_query($sqlT276);
			$linhaT276 = mysql_fetch_array($resultT276);
			if (!empty($linhaT276['T276_Numero_Protocolo_MDFe'])) {
				$retorno = 'MDFe já transmitida. Não é possível alterar.';
			} else if ($linhaT276['T276_Flag_Travar_MDFe'] == 'S') {
				$retorno = 'MDFe já finalizada, para altera-lá utilize o botão "Habilitar edição"';
			}
		} else {
			$sqlT276 = "SELECT T276_Numero_Protocolo_MDFe, T276_Flag_Travar_MDFe FROM T276 WHERE T276_Id='{$valorPkParent}'";
			$resultT276 = mysql_query($sqlT276);
			$linhaT276 = mysql_fetch_array($resultT276);
			if (!empty($linhaT276['T276_Numero_Protocolo_MDFe'])) {
				$retorno = 'MDFe já transmitida. Não é possível alterar.';
			} else if ($linhaT276['T276_Flag_Travar_MDFe'] == 'S') {
				$retorno = 'MDFe já finalizada, para altera-lá utilize o botão "Habilitar edição"';
			}
		}
EOT;
$conf['restricaoIncluirUpdate']['T277_D018_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>
