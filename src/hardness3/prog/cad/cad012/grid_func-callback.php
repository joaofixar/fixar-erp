<?php
namespace hardness;
/**
 * CAD - CAD012 - Cadastro de Bens
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad012CampoExclusao('{$array['todosCampos']['D111_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
?>