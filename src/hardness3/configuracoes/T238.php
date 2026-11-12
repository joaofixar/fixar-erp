<?php
/**
 * Tabela T238
 * 
 * Documentação: 
 * 		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */
 
/*$conf['restricaoIncluirUpdate']['T238_Quantidade_Separacao'] = <<<'EOT'
	if ($valor <= 0) {
		$retorno = 'Valor deve ser maior que 0.';
	} else {
		$retorno = true;
	}
EOT;*/
$conf['restricaoIncluirUpdate']['T238_T066_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T238_T006_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
