<?php
namespace hardness;
function retornarPedidoViaCupom($array){
	global $g;
	$imagem = '&nbsp;';
	if ($array['todosCampos']['T005_Flag_Expedicao'] == 2) {
		$imagem = <<<EOT
			<span onclick="ven012RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
			   <div class="icone ui-state-default ui-corner-all">
			   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
			   </div>
			</span>
EOT;
	}
	return $imagem;
}