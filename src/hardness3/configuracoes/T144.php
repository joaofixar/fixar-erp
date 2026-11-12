<?php
/**
 * Tabela T144

 */
 
$conf['restricaoIncluirUpdate']['T144_Data_Cadastro'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T144_Url'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T144_D001_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D001_Id', $valor);
EOT;
?>