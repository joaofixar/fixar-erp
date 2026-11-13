<?php
namespace hardness;
function campoAnaliseCustoFinalizado($array){
 	global $g;
	$novoIdTelaForm = uniqid();
	$retorno = ($array);
	$imagem = <<<EOT
		<span onclick="campoAnaliseCustoFinalizado('{$array['todosCampos']['T004_Id']}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-check" title="Finalizar análise de custo"></span>
		   </div>
		</span>	
EOT;
	return $imagem;
}