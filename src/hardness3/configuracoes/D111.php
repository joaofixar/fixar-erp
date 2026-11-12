<?php
/**
 * Tabela D111

 */
$conf['restricaoIncluirUpdate']['D111_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D111_D037_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D037_Id', $valor);
EOT;
?>