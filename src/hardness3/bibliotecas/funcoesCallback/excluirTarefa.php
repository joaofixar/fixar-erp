<?php
namespace hardness;
function excluirTarefa($array){
	global $g;
	return <<<EOT
		<a href="#" onclick="excluirTarefa('{$array['todosCampos']['T259_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}