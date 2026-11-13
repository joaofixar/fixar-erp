<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D170_Identificacao_Veiculo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_Placa'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_Tara'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_D018_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_Flag_Rodado'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_Flag_Carroceria'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D170_Flag_Ativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
