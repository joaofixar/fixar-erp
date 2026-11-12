<?php
/**
 * Tabela D113

 */
	$conf['restricaoIncluirUpdate']['D113_Titulo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
	$conf['restricaoIncluirUpdate']['D113_D110_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D110_Id', $valor);
EOT;

?>