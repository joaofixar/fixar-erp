<?php
/**
 * Tabela D173 - Cdastro Apólices
 */

global $idFormDecImport;
$conf['restricaoIncluirUpdate']['D173_Identificacao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D173_Numero_Apolice'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D173_Flag_Ativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>