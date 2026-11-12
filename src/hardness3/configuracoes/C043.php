<?php
/**
* restricaoTravar
*/

$conf['restricaoIncluirUpdate']['C043_CNPJ'] = <<<'EOT'
	global  $g;
    if(!empty($valores['C043_CNPJ'])){
        $retorno = rValidarCnpj($valor);
    }

EOT;
$conf['restricaoIncluirUpdate']['C043_CPF'] = <<<'EOT'
	global $g;  
    if(!empty($valores['C043_CPF'])){
        $retorno = rValidarCpf($valor);
    }                                            
EOT;


