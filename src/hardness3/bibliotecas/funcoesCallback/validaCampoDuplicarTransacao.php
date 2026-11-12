<?php
namespace hardness;
function validaCampoDuplicarTransacao($array){
	global $g;
	$T216_Id = $array['todosCampos']['T216_Id'];
	return <<<EOT
	<span class="ui-icon ui-icon-circle-plus"  title="Duplicar Registro" onclick="validaCampoDuplicarTransacao('{$T216_Id}', '{$g['divId']}');"></span>
EOT;
}