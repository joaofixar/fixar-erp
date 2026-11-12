<?php
/**
 * Tabela D106

 */
$conf['restricaoIncluirUpdate']['D106_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D106_Data_Compra'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D106_Data_Validade'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D106_Flag_Status'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D106_Tipo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
