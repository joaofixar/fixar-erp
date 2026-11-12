<?php
namespace hardness;
/**
 * Weiberlan
 * 10/11/2011
 */

function demoItemDRE($array, $param){
	global $g;
	$novoIdTelaForm = uniqid();
	$valor = number_format($array['campoAtual'], 2, ',', '.');
	$titulo = $array['todosCampos']['DRE'];
	log("titulo: " . $titulo);
	$param = explode('|', $param);
	$C004_Id = $param[0];
	$extra =  htmlentities(urlencode($param[1]));
	$retorno = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Demonstrativo DRE', '/rel/fin/content/demonstrativoDRE/','&acaoId={$titulo}&C004_Id={$C004_Id}&extra=' + encodeURIComponent('$extra'), [800,500]);">
			{$valor}
		</span>
EOT;
	
	return $retorno;
}

