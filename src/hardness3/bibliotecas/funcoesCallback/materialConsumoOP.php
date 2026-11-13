<?php
namespace hardness;

function materialConsumoOP($array) {
	global $g;
	return <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Consumiveis', '/est/est017/content/est017ContentConsumiveis/', '&T262_Id=' + encodeURIComponent('{$array['todosCampos']['T262_Id']}'), [800,500])">
			<span class="ui-icon ui-icon-written-document" title="Consumivel"></span>
		</span>
EOT;
}

