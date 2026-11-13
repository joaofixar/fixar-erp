<?php
/**
 * Tabela D171
 */
$conf['restricaoIncluirUpdate']['D171_Tipo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D171_Numero_Lacre'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>