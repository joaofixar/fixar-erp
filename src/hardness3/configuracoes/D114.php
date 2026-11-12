<?php
/**
 * Tabela D114

 */
	$conf['restricaoIncluirUpdate']['D114_D009_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D009_Id', $valor);
EOT;
	$conf['restricaoIncluirUpdate']['D114_D037_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D037_Id', $valor);
EOT;
	$conf['restricaoIncluirUpdate']['D114_Quantidade'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

?>