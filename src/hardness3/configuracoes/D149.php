<?php
/**
 * Tabela D149
 * 
 * Documentação: 
 * 		http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
 */
 
$conf['restricaoIncluirUpdate']['D149_Flag_ST'] = <<<'EOT'
 	$retorno = rRegx('naoSelecionado', $valor);
EOT;
