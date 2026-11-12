<?php
namespace hardness;
function campoOpcoesGeral2($array){
	global $g;
	$novoIdTelaForm = uniqid();	
	if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
		$imagem = <<<EOT
			<span onclick="nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id={$array['todosCampos']['T005_Id']}',0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');">
			   <div class="icone ui-state-default ui-corner-all">
		   			<span class="ui-icon ui-icon-print" title="Imprimir Pedido"></span>
			   </div>
			</span>
EOT;
	}
	return $imagem;
}