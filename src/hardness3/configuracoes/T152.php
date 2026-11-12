<?php
/**
 * Andrey
 * 10/11/2011
 *
 * Tabela T147
 *
 * Documentação:
 * http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['T152_D009_Id'] = <<<'EOT'
$retorno = rVerificaFK('D009_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T152_Quantidade'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T152_D037_Id'] = <<<'EOT'
$retorno = rVerificaFK('D037_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T152_T145_Id'] = <<<'EOT'
$retorno = rVerificaFK('T145_Id', $valor);
EOT;

?>