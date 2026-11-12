<?php
namespace hardness;
function campoOpcoesColeta($array, $flag_status){
	global $g;
	$novoIdTelaForm = uniqid();
	$imagem = <<<EOT
		<span onclick="campoOpcoesColeta('{$array['todosCampos']['T224_Id']}', '{$flag_status}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-circle-arrow-e" title="Coletar"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}