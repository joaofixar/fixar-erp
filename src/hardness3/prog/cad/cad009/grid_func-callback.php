<?php
namespace hardness;
/**
 * CAD - cad009 - Cadastro de Catálogos
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad009CampoExclusao('{$array['todosCampos']['D107_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
// exclusão do grid de acrescimos e descontos
function campoExclusaoItem($array) {	
	global $g;
	return <<<EOT
    	<a href="#" onclick="cad009campoExclusaoItem('{$array['todosCampos']['D108_Id']}','{$array['todosCampos']['D108_D107_Id']}', '{$g['divId']}')">
    		<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
?>