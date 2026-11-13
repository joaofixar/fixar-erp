<?php
/**
* Tabela T278 - Motorista MDFe
*/
//$valorPkParent - esse valor é definido no formulário através da varialvel $form_parent
$conf['restricaoTravar']['T278'] = <<<'EOT'

	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T278
		                            LEFT JOIN T276 ON T276_Id=T278_T276_Id
		                            WHERE T278_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);

    } if ($operacao == 'incluir') {
		
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
				$retorno = 'MDF-e já encerrado, não é possível alterar.';
			} else {
				$retorno = 'MDF-e já enviado, não é possível alterar.';
			}

		} else {

			$retorno = 'MDF-e já finalizado, para altera-lá utilize o botão "Habilitar edição"';			
		
		}
	}
EOT;

$conf['restricaoIncluirUpdate']['T278_D109_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>