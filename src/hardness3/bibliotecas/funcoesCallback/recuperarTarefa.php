<?php
namespace hardness;
function recuperarTarefa($array){
	global $g;
	return <<<EOT
		<a href="#" onclick="recuperarTarefa('{$array['todosCampos']['T259_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Excluir"></span>
		</a>
EOT;
}