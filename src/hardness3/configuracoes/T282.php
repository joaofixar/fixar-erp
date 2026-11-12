<?php
/**
 * Tabela T282
 */
$conf['restricaoIncluirUpdate']['T282_Flag_Unidade_Carga'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T282_Identificacao_Unidade_Carga'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoTravar']['T282'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T282
		                            LEFT JOIN T281 ON T281_Id=T282_T281_Id
		                            LEFT JOIN T284 ON T284_Id=T281_T284_Id
		                            LEFT JOIN T276 ON T276_Id=T284_T276_Id
		                            WHERE T282_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);

    } if ($operacao == 'incluir') {
		
		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPkParent}'");
		$mT276 = mysql_fetch_assoc($T276);
		
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
?>