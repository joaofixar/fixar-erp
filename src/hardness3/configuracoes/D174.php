<?php
/**
 * Tabela D174 - Averbações da seguradoa
 */
$conf['restricaoTravar']['D174'] = <<<'EOT'
$retorno = true;
	if ($operacao == 'alterar') {
		$sqlT276 = "SELECT T276_Numero_Protocolo_MDFe,
  						   T276_Flag_Travar_MDFe 
				      FROM D174 
				 LEFT JOIN T279 ON T279_Id=D174_T279_Id	
				 LEFT JOIN T276 ON T276_Id = T279_T276_Id 
   					 WHERE D174_Id='{$valorPk}'";

		$resultT276 = mysql_query($sqlT276);
		$linhaT276 = mysql_fetch_array($resultT276);
		if (!empty($linhaT276['T276_Numero_Protocolo_MDFe'])) {
			$retorno = 'MDFe já transmitida. Não é possível alterar.';
		} else if ($linhaT276['T276_Flag_Travar_MDFe'] == 'S') {
			$retorno = 'MDFe já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}
	} else {
		$sqlT276 = "SELECT T276_Numero_Protocolo_MDFe, 
						   T276_Flag_Travar_MDFe 
					  FROM D174 
		     	 LEFT JOIN T279 ON T279_Id='{$valorPkParent}'	
				 LEFT JOIN T276 on T276_Id=T279_T276_Id";
		$resultT276 = mysql_query($sqlT276);
		$linhaT276 = mysql_fetch_array($resultT276);
		if (!empty($linhaT276['T276_Numero_Protocolo_MDFe'])) {
			$retorno = 'MDFe já transmitida. Não é possível alterar.';
		} else if ($linhaT276['T276_Flag_Travar_MDFe'] == 'S') {
			$retorno = 'MDFe já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}
	}
EOT;
$conf['restricaoIncluirUpdate']['D174_Numero_Averbacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>