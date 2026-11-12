<?php
/**
 * Tabela T033

 */
 
$conf['restricaoIncluirUpdate']['T033_Historico'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T033_Valor'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T033_Flag_Deb_Cred'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

?>