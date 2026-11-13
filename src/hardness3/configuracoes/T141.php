<?php
/**
 * Tabela T141
 */

// camposNaoUsar
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'T141_D116_Id'
));

// restricaoIncluirUpdate
$conf['restricaoIncluirUpdate']['T141_D106_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D106_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_Data_Inicio'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_Data_Validade'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_Valor_Mensal'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_Dia_Vencimento'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['T141_Flag_Status'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
