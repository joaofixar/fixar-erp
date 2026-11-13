<?php	
namespace hardness;

function cancelarProdutoOC($array) {
	global $g;
	$T225_Flag_Cancelada = $array['todosCampos']['T225_Flag_Cancelada'];
	if($T225_Flag_Cancelada != 'S' && $array['todosCampos']['T014_Id'] <= 0){
		return <<<EOT
			<a href="#" onclick="cancelarProdutoOC('{$array['todosCampos']['T225_Id']}', '{$T225_Flag_Cancelada}', '{$g['divId']}')">
				<span class="ui-icon ui-icon-circle-close" title="Cancelar"></span>
			</a>
EOT;
	} else {
		return '';
	}
}