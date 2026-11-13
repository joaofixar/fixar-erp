<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T283_D170_Id'] = <<<'EOT'
	if($valores['T283_Flag_Carro_Proprio'] == 'S'){
		$retorno = rRegx('naoSelecionado', $valor);
	}else{
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['T283_CPF_Proprietario'] = <<<'EOT'
	if($valores['T283_Flag_Carro_Proprio'] == 'N'){
		if(!isset($valores['T283_CNPJ_Proprietario'])|| empty($valores['T283_CNPJ_Proprietario'])){
			$retorno = rRegx('naoVazio', $valor);
			if ($retorno === true) {                                                                      
				$retorno = rValidarCpf($valor);
			}
		}
	}
EOT;
$conf['restricaoIncluirUpdate']['T283_CNPJ_Proprietario'] = <<<'EOT'
	if($valores['T283_Flag_Carro_Proprio'] == 'N'){
		if(!isset($valores['T283_CPF_Proprietario']) || empty($valores['T283_CPF_Proprietario'])){
			$retorno = rRegx('naoVazio', $valor);
			if ($retorno === true) {                                                                      
				$retorno = rValidarCnpj($valor);
			}
		}
	}
EOT;
$conf['restricaoIncluirUpdate']['T283_Proprietario_Veiculo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T283_Tipo_Proprietario'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T283_D018_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T283_Placa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T283_Tara'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T283_Capacidade_Kg'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
	

$conf['restricaoTravar']['T283'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T283
		                            LEFT JOIN T276 ON T276_Id=T283_T276_Id
		                            WHERE T283_Id='{$valorPk}'");
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

