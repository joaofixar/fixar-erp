<?php

$conf['restricaoIncluirUpdate']['D119_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D119_Telefone'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D119_Email'] = <<<'EOT'
	$retorno = rRegx('email', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D119_Endereco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['D119_D020_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D020_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D119_D018_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D119_C004_Id'] = <<<'EOT'
	$retorno = rVerificaFK('C004_Id', $valor);
EOT;

?>