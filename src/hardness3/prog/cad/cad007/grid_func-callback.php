<?php
namespace hardness;
/**
 * CAD - CAD007 - Cadastro de Certificados
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="cad007CampoExclusao('{$array['todosCampos']['T140_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
	function abrirCertificado($array){
			$id = $array['todosCampos']['T140_Id'];
			$retorno = "<a href=\"/cad/cad007/grid_func-ajax/abreCertificado/?ajax=true&acaoId={$id}\">Abrir</a>";
			return $retorno;
	}
?>