<?php
/**
 * Andrey
 * 10/11/2011
 *
 * Tabela D117
 *
 * Documentação:
 * http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['D117_Nome'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
/*
$conf['restricaoIncluirUpdate']['D117_Email'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_D020_Id'] = <<<'EOT'
$retorno = rVerificaFK('D020_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_Endereco'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_D018_Id'] = <<<'EOT'
$retorno = rVerificaFK('D018_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_Telefone'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_Cep'] = <<<'EOT'
$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['restricaoIncluirUpdate']['D117_Observacao'] = <<<'EOT'
$retorno = rRegx('naoVazio', $valor);
EOT;
*/
?>