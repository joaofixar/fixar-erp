<?php
/**
 * Weiberlan
 * 08/11/2011
 * Tabela T148
 *
 * Documentação:
 *		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['T148_Titulo'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T148_Descricao'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
?>

