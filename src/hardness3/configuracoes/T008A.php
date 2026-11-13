
<?php
/**
* Tabela T008A
*/

$conf['triggers']['T008A']['updateAfter'] = <<<'EOT'
		
		$T008  = mysql_query("SELECT T008_Id,
									 T008_T007_Id,
									 T007A_Flag_Conversao_Unidade 
		                        FROM T008A 
						   LEFT JOIN T008 ON T008_Id = T008A_T008_Id
						   LEFT JOIN T007 ON T007_Id = T008_T007_Id
						   LEFT JOIN T007A ON T007_Id = T007A_T007_Id
							   WHERE T008A_Id = '{$chaveValor}'");
		$mT008 = mysql_fetch_array($T008);

		if($mT008['T007A_Flag_Conversao_Unidade'] == 'S'){ 
			
			require_once('bibliotecas/classes/CAD002.php');
			$CAD002 = new CAD002();
			
			$CAD002->conversaoUnidadeProduto(false, false, $mT008['T008_Id'], false);
	
			mysqli_query("CALL T008_Gravar_Totalizacao_4('{$mT008['T008_Id']}')");
			mysqli_query("CALL T007_Gravar_Totalizacao_4('{$mT008['T008_T007_Id']}')");
		}
EOT;

$conf['restricaoTravar']['T008A'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar') {
		$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe, 
						   T007_Flag_Travar_Nota 
					  FROM T008A
				 LEFT JOIN T008 ON T008_Id = T008A_T008_Id 
				 LEFT JOIN T007 ON T007_Id=T008_T007_Id 
				     WHERE T008A_Id='{$valorPk}'";

		$resultT007 = mysql_query($sqlT007);
		$linhaT007  = mysql_fetch_array($resultT007);

		if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
			$retorno = 'Nota já transmitida. Não é possível alterar.';
		} else if ($linhaT007['T007_Flag_Travar_Nota'] == 'S') {
			$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';
		}
	}
EOT;