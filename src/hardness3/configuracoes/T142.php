<?php
/**
 * Tabela T140

 */
 
$conf['restricaoIncluirUpdate']['T142_D106_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D106_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T142_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T142_Data_Manutencao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T142_Valor'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
?>