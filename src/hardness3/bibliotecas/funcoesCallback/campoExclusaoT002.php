<?php	
namespace hardness;

function campoExclusaoT002($array) {
	global $g;
	if (($array['todosCampos']['T007_Id'] > 0) || (!isset($g['c029Ids'][111]))){
		return "&nbsp";
	} else {
		return <<<EOT
			<a href="#" onclick="fin001CampoExclusao('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	}
}