<?php
namespace hardness;
function excluirAgregadoOrdemCompra($array){
	global $g;
	$T210_Id = $array['todosCampos']['T210_Id'];
	return <<<EOT
		<a href="#" onclick="excluirAgregadoOrdemCompra('{$T210_Id}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}