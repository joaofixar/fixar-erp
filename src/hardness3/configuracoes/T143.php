<?php
/**
 * Tabela T143

 */
 
$conf['restricaoIncluirUpdate']['T143_D106_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D106_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_D030_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D030_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_D018_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_D020_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D020_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_Data_Transferencia'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_Data_Prev_Retorno'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T143_T141_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

?>