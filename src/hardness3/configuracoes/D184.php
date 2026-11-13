<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D184_D037_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);

	if ($retorno === true && !$acaoId) {		
		$sql = "SELECT D184_Id FROM D184 WHERE D184_D001_Id = '{$valores['D184_D001_Id']}' AND D184_D037_Id = '{$valores['D184_D037_Id']}'";
		$res = mysql_query($sql);		
		if (mysql_num_rows($res) > 0) {
			$retorno = 'Já existe uma registro cadastrado para esta regra de conversão neste produto.';
		} else {
			$retorno = true;
		}
	}
EOT;