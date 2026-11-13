<?php
$conf['restricaoIncluirUpdate']['D030_Nome_Pais'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D030_Sigla_Pais'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
 $conf['restricaoIncluirUpdate']['D030_Codigo_Pais'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D030_Codigo_Area'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>