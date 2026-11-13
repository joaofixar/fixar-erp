<?php
namespace hardness;

function configurarRelatorioBI($array, $tabBI){
	global $g;
	$id = uniqid();
	$D132_Id = $array['todosCampos']['D132_Id'];
	$onclick = <<<EOT
		$('#{$g['divId']}').closest('.tabs').tabs('select', '#{$tabBI}');
		divSetarAcaoId('{$tabBI}', '{$D132_Id}', 'D132_Id');
		setTimeout(function(){
			divRefresh('{$tabBI}');
		}, 350);
EOT;
	$onclick = str_replace("\n", ' ', $onclick);
	return <<<EOT
		<a id="{$id}" href="#" onclick="{$onclick}">Configurar</a>
EOT;
}
