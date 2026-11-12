<?php

$conf['restricaoTravar']['D087'] = <<<'EOT'
	global $g;
	if ($operacao == 'excluir' || $operacao == 'alterar' || $operacao == 'incluir') {
		if($g['c029Ids'][237]){
			$retorno = false;
		}else{
			$retorno = true;
		}
	}
EOT;
?>