<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D175_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D175_Tipo_Impressora'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;


