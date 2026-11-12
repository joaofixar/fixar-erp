<?php

$conf['restricaoIncluirUpdate']['D037_Descricao_Unidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D037_Unidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

?>