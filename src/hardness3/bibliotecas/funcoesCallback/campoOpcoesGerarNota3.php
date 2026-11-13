<?php
namespace hardness;
function campoOpcoesGerarNota3($array){
	global $g;
	$imagem = <<<EOT
		<span onclick="ven012ImprimirPedido('{$array['todosCampos']['T005_Id']}');">
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
		   </div>
		</span>
EOT;
	return $imagem;
}