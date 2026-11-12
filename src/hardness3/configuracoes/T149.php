<?php
/**
 * Weiberlan
 * 08/11/2011
 * Tabela T149
 *
 * Documentação:
 *		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */

$conf['restricaoIncluirUpdate']['T149_D037_Id'] = <<<'EOT'
$retorno = rVerificaFK('D037_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T149_D111_Id'] = <<<'EOT'
$retorno = rVerificaFK('D111_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T149_Quantidade'] = <<<'EOT'
$retorno = rNumeroMaiorQueZero($valor);
EOT;
?>