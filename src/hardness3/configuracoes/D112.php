<?php
/**
 * Tabela D112

 */
 $conf['restricaoIncluirUpdate']['D112_Titulo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
/*
$conf['restricaoIncluirUpdate']['D112_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D112_Tempo_Estimado'] = <<<'EOT'
	$retorno = rRegx('tempo', $valor);
	if ($retorno === true) {
		$retorno = rTempoMaiorQueZero($valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D112_Valor_Unitario'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D112_D110_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
*/
