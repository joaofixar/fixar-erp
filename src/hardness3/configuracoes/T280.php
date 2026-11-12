<?php
/**
 * Tabela T280
 */
$conf['restricaoTravar']['T280'] = <<<'EOT'
	$retorno = true;

	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T280
		                            LEFT JOIN T276 ON T276_Id=T280_T276_Id
		                            WHERE T280_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);

    } else if ($operacao == 'incluir') {
		
		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPkParent}'");
		$mT276 = mysql_fetch_assoc($T276);
		
	} 

	if ($mT276['T276_Flag_Cancelada']=='S') {

		$retorno = 'MDF-e está cancelado!';

	} else if ($mT276['T276_Flag_Travar_MDFe'] == 'S') {
		log("TESTE = S");
		if (!empty($mT276['T276_Numero_Protocolo_MDFe'])) {
			
			if(!empty($mT276['T276_Numero_Protocolo_Encerramento_MDFe'])){
				$retorno = 'MDF-e.';
			} else {
				$retorno = 'MDF-e já enviado, não é possível alterar.';
			}

		} else {

			$retorno = 'MDF-e já finalizado, para altera-lá utilize o botão "Habilitar edição"';
			
		}
	}
EOT;

$conf['restricaoIncluirUpdate']['T280_D171_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D171_Id', $valor);
EOT;
?>
