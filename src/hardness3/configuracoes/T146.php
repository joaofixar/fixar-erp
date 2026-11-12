<?php
/**
 * Andrey
 * 10/11/2011
 *
 * Tabela T147
 *
 * Documentação:
 *		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['T146_Titulo'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T146_Descricao'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T146_T145_Id'] = <<<'EOT'
$retorno = rVerificaFK('T145_Id', $valor);
EOT;
?>