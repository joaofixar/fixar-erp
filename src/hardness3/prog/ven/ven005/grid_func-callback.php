<?php
namespace hardness;
/**
 * VEN - VEN001 - Cadastro de Bens
 */
function ven005NaoLiberar($array) {
	global $g;
	if($g['C007']['C007_Flag_Vendedor'] != 'D' && $array['todosCampos']['T003_Flag_Alcada'] != $g['C007']['C007_Flag_Vendedor']){
		return '';
	}
	return <<<EOT
		<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Não Liberar', '/ven/ven005/content/ven005ContentNaoLiberar/', '&acaoId={$array['todosCampos']['T003_Id']}', [400,300]); return false;">
			<span class="ui-icon ui-icon-closethick" title="Reprovar"></span>
		</a>
EOT;

}
function ven005Liberar($array) {
	global $g;
	if($g['C007']['C007_Flag_Vendedor'] != 'D' && $array['todosCampos']['T003_Flag_Alcada'] != $g['C007']['C007_Flag_Vendedor']){
		return '';
	}
	return <<<EOT
		<a href="#" onclick="ven005AprovarOrcamento('{$array['todosCampos']['T003_Id']}', '{$g['divId']}');">
			<span class="ui-icon ui-icon-check" title="Aprovar"></span>
		</a>
EOT;

}

?>