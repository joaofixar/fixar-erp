<?php
$conf['restricaoIncluirUpdate']['D007_CNPJ'] = <<<'EOT'
	if($valores['D007_CNPJ'] == ''){
		$retorno = rRegx('naoVazio', $valor);
		$retorno = rValidarCnpj($valor);
	}else{
		$retorno = rValidarCnpj($valor);
	}
EOT;

?>