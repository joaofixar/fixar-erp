<?php
/**
 * Tabela D172 - Cadastro Seguradoras
 */

global $idFormDecImport;
$conf['restricaoIncluirUpdate']['D172_Nome'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D172_CNPJ'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor); 
	if ($retorno === true) {                                                                      
		$retorno = rValidarCnpj($valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D172_Flag_Ativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
?>