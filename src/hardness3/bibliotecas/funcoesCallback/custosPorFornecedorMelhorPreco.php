<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: custosPorFornecedorMelhorPreco()
*/

function custosPorFornecedorMelhorPreco ($array) {
	$campo = explode('|', $array['campoAtual']);
	$campo[0] = gCorrigeNumero($campo[0], 5);
	$campo[1] = substr($campo[1], 0, 10);
	return <<<EOT
<span style="font-weight: normal;">
R$ {$campo[0]} - 
{$campo[1]} - 
<span>{$campo[2]} dias</span>
</span>
EOT;
	
}
