<?php
namespace hardness;
function campoOpcoesGerarNota2($array){
	global $g;
	$imagem = <<<EOT
		<span onclick="ven012RetornarPedido('{$array['todosCampos']['T005_Id']}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido para o vendedor"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}