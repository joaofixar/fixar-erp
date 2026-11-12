<?php
namespace hardness;
function campoOpcoesRetornarColeta($array, $flag_status){
	global $g;
	$novoIdTelaForm = uniqid();
	$imagem = <<<EOT
		<span onclick="campoOpcoesRetornarColeta('{$array['todosCampos']['T224_Id']}', '{$flag_status}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Coletar"></span>
		   </div>
		</span>
EOT;
	if($array['todosCampos']['T224_Flag_Status'] == '5' || $array['todosCampos']['T224_Flag_Status'] == '6' || $array['todosCampos']['T224_Flag_Status'] == '7'){
		return $imagem;
	}
	return '';
}