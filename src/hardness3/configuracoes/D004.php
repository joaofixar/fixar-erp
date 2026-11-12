<?php
/**
 * Tabela D004

 */
 
$conf['restricaoIncluirUpdate']['D004_Local'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoTravar']['D004'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar'){
		if (!isset($g['c029Ids'][263])) {
			$retorno = 'Você não tem permissao para alterar locação';
		}
	} else if($operacao == 'excluir'){
		if (!isset($g['c029Ids'][264])) {
			$retorno = 'Você não tem permissao para excluir locação';
		}
	}elseif($operacao == 'incluir'){
		if (!isset($g['c029Ids'][262])) {
			$retorno = 'Você não tem permissao para incluir locação';
		}
	}
EOT;
?>
