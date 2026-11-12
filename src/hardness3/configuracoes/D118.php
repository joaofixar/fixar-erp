<?php
$conf['restricaoIncluirUpdate']['D118_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D118_Estado'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D118_Cidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D118_Flag_Tipo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['D118_D030_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D030_Id', $valor);
EOT;

?>