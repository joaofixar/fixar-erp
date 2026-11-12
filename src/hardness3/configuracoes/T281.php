<?php
/**
 * Tabela T281
 */
$conf['restricaoIncluirUpdate']['T281_Identificacao_Unidade_Transporte'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
	
$conf['restricaoTravar']['T281'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T281
		                            LEFT JOIN T284 ON T284_Id=T281_T284_Id
		                            LEFT JOIN T276 ON T276_Id=T284_T276_Id
		                            WHERE T281_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);

    } else if ($operacao == 'incluir') {
		
		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPkParent}'");
		$mT276 = mysql_fetch_assoc($T276);
		log("TESTE INCLUIR = SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPkParent}'");
	} 

	if ($mT276['T276_Flag_Cancelada']=='S') {

		$retorno = 'MDF-e está cancelado!';

	} else if ($mT276['T276_Flag_Travar_MDFe'] == 'S') {
		
		if (!empty($mT276['T276_Numero_Protocolo_MDFe'])) {

			if(!empty($mT276['T276_Numero_Protocolo_Encerramento_MDFe'])){
				$retorno = utf8_encode('MDF-e já encerrado, não é possível alterar.');
			} else {
				$retorno = utf8_encode('MDF-e já enviado, não é possível alterar.');
			}

		} else {

			$retorno = utf8_encode('MDF-e já finalizado, para altera-lá utilize o botão "Habilitar edição"');
			
		}
	}
EOT;

$conf['restricaoIncluirUpdate']['T281_Flag_Unidade_Transporte'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>
