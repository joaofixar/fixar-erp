<?php
$conf['restricaoIncluirUpdate']['T168_Data'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T168_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T168_Flag_ME'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>