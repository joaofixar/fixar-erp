<?php
/**
 * Tabela D108

 */
$conf['restricaoIncluirUpdate']['D108_Codigo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D108_D001_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>