<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D116_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D116_Placa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/*
* camposNaoUsar
*/
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'D116_D126_Id','D116_Modelo','D116_Cor'
));
