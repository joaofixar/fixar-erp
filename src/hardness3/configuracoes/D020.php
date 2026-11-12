<?php
$conf['restricaoIncluirUpdate']['D020_D018_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D020_Nome_Cidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D020_Codigo_IBGE'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>