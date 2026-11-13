<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D179_Login'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D179_Senha'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;




