<?php
namespace hardness;
// Pedido, D049_Id
function campoAgregadosOrdemCompra($array){
	global $g;
	$T012_Id = $array['todosCampos']['T012_Id'];
	$imagem = <<<EOT
		<span onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Agregados', '/est/est006/content/est006AgregadosOrdemCompraTab/','&acaoId={$T012_Id}', [650,500]);">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-plus" title="Agregados"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}