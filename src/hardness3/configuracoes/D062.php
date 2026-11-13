<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D062_Numero_Banco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
	if ($retorno === true) {
		$retorno = rDuplicidade($campo, $valor, $acaoId, 'Número do Banco');
	}
EOT;

?>