<?php
namespace hardness;
/**
 * CAD - CAD011 - Cadastro de Bens
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad011CampoExclusao('{$array['todosCampos']['D110_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
?>