<?php
namespace hardness;
/**
 * VEN - VEN011 - Manutenção de Bens
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="ven011CampoExclusao('{$array['todosCampos']['T142_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
?>