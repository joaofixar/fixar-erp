<?php
/**
 * Tabela T020

 */
 
$conf['restricaoIncluirUpdate']['T020_Historico'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T020_Valor'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T020_Flag_Deb_Cred'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

?>