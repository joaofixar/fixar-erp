<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */
function ven013NaoLiberar($array) {
	global $g;
	if($g['C007']['C007_Flag_Vendedor'] != 'D' && $array['todosCampos']['T005_Flag_Alcada'] != $g['C007']['C007_Flag_Vendedor']){
		return '';
	}
	return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/ven/ven013/content/ven013ContentNaoLiberar/', '&acaoId={$array['todosCampos']['T005_Id']}', [400,300]); return false;">
			<span class="ui-icon ui-icon-closethick" title="Reprovar"></span>
		</a>
EOT;

}
function ven013Liberar($array) {
	global $g;
	if($g['C007']['C007_Flag_Vendedor'] != 'D' && $array['todosCampos']['T005_Flag_Alcada'] != $g['C007']['C007_Flag_Vendedor']){
		return '';
	}
	return <<<EOT
		<a href="#" onclick="ven013AprovarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
			<span class="ui-icon ui-icon-check" title="Aprovar"></span>
		</a>
EOT;

}

?>